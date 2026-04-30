<?php

namespace App\Services;

use App\Models\ChargeMaster;
use App\Models\DiagnosticOrderItem;
use App\Models\OpdPatient;
use App\Models\Patient;
use App\Models\PatientCharge;
use App\Models\PatientPayment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class ChargeLedgerService
{
    public function resolveChargeRate(?ChargeMaster $chargeMaster, ?int $tpaId = null): float
    {
        if (!$chargeMaster) {
            return 0.0;
        }

        if ($tpaId) {
            $tpaRate = $chargeMaster->tpaRates()
                ->where('tpa_id', $tpaId)
                ->value('rate');

            if ($tpaRate !== null) {
                return (float) $tpaRate;
            }
        }

        return (float) $chargeMaster->standard_rate;
    }

    public function getPendingChargesForPatient(Patient $patient, ?array $chargeIds = null): Collection
    {
        return $this->pendingChargesQuery($patient, $chargeIds)->get();
    }

    public function getOutstandingAmount(Collection $charges): float
    {
        return (float) $charges->sum(function (PatientCharge $charge) {
            return max(0, (float) $charge->amount - (float) $charge->paid_amount);
        });
    }

    public function upsertCharge(array $attributes): PatientCharge
    {
        return DB::transaction(function () use ($attributes) {
            // Prefer explicit charge_master_id; charge_code is lookup-only (no auto-create)
            $directChargeMasterId = $attributes['charge_master_id'] ?? null;
            if ($directChargeMasterId) {
                $chargeMaster = ChargeMaster::find($directChargeMasterId);
            } elseif (filled($attributes['charge_code'] ?? null)) {
                $chargeMaster = $this->resolveChargeMaster([
                    'hospital_id' => $attributes['hospital_id'],
                    'charge_code' => $attributes['charge_code'],
                ]);
            } else {
                $chargeMaster = null;
            }

            $explicitUnitRate = array_key_exists('unit_rate', $attributes)
                ? (float) $attributes['unit_rate']
                : null;

            $quantity = max(1, (float) ($attributes['quantity'] ?? 1));
            $unitRate = $explicitUnitRate ?? ($chargeMaster
                ? $this->resolveChargeRate($chargeMaster, isset($attributes['tpa_id']) ? (int) $attributes['tpa_id'] : null)
                : (float) ($attributes['amount'] ?? 0));
            $discountAmount = (float) ($attributes['discount_amount'] ?? 0);
            $taxAmount = (float) ($attributes['tax_amount'] ?? 0);
            $netAmount = (float) ($attributes['net_amount'] ?? (($quantity * $unitRate) - $discountAmount + $taxAmount));
            $amount = (float) ($attributes['amount'] ?? $netAmount);

            $payload = [
                'hospital_id'       => $attributes['hospital_id'],
                'patient_id'        => $attributes['patient_id'],
                'visitable_type'    => $attributes['visitable_type'] ?? null,
                'visitable_id'      => $attributes['visitable_id'] ?? null,
                'source_type'       => $attributes['source_type'] ?? null,
                'source_id'         => $attributes['source_id'] ?? null,
                'module'            => $attributes['module'] ?? null,
                'particular'        => $attributes['particular'],
                'charge_master_id'  => $chargeMaster?->id ?? $directChargeMasterId,
                'charge_code'       => $attributes['charge_code'] ?? $chargeMaster?->code,
                'charge_category'   => $attributes['charge_category'] ?? $chargeMaster?->category,
                'calculation_type'  => $attributes['calculation_type'] ?? ($chargeMaster?->calculation_type ?? 'fixed'),
                'billing_frequency' => $attributes['billing_frequency'] ?? ($chargeMaster?->billing_frequency ?? 'one_time'),
                'quantity'          => $quantity,
                'unit_rate'         => $unitRate,
                'discount_amount'   => $discountAmount,
                'tax_amount'        => $taxAmount,
                'net_amount'        => $netAmount,
                'amount'            => $amount,
                'payer_type'        => $attributes['payer_type'] ?? 'self',
                'tpa_id'            => $attributes['tpa_id'] ?? null,
                'charged_at'        => $attributes['charged_at'] ?? now(),
            ];

            if (!empty($attributes['source_type']) && !empty($attributes['source_id'])) {
                $existing = PatientCharge::where('source_type', $attributes['source_type'])
                    ->where('source_id', $attributes['source_id'])
                    ->first();

                if ($existing) {
                    // Don't overwrite payment_status on update
                    $existing->update($payload);
                    $existing->payment_status = (float) $existing->paid_amount >= (float) $existing->amount ? 'paid' : ((float) $existing->paid_amount > 0 ? 'partial' : 'unpaid');
                    $existing->save();
                    $charge = $existing;
                } else {
                    $payload['payment_status'] = 'unpaid';
                    $charge = PatientCharge::create($payload);
                }
            } else {
                $payload['payment_status'] = 'unpaid';
                $charge = PatientCharge::create($payload);
            }

            // Auto-consume any existing patient credit/advance against oldest pending dues.
            // This keeps OPD/IPD financial sequence consistent when charges are added across encounters.
            $this->applyAvailableCredit($charge->patient_id);
            $charge->refresh();

            $this->syncSourceFromCharge($charge);

            return $charge->fresh();
        });
    }

    public function collectPayment(Patient $patient, array $attributes, ?array $chargeIds = null): PatientPayment
    {
        $charges = $this->pendingChargesQuery($patient, $chargeIds, true)->get();
        $firstCharge = $charges->first();

        $payment = PatientPayment::create([
            'hospital_id' => $patient->hospital_id,
            'patient_id' => $patient->id,
            'visitable_type' => $this->singleValue($charges, 'visitable_type'),
            'visitable_id' => $this->singleValue($charges, 'visitable_id'),
            'amount' => $attributes['amount'],
            'payment_mode' => $attributes['payment_mode'] ?? null,
            'reference' => $attributes['reference'] ?? null,
            'notes' => $attributes['notes'] ?? null,
            'received_by' => auth()->id(),
            'paid_at' => now(),
        ]);

        $remaining = (float) $attributes['amount'];

        foreach ($charges as $charge) {
            if ($remaining <= 0) {
                break;
            }

            $chargeDue = max(0, (float) $charge->amount - (float) $charge->paid_amount);
            if ($chargeDue <= 0) {
                continue;
            }

            $allocatedAmount = min($remaining, $chargeDue);
            $payment->allocations()->create([
                'patient_charge_id' => $charge->id,
                'amount' => $allocatedAmount,
            ]);

            $charge->paid_amount = (float) $charge->paid_amount + $allocatedAmount;
            $charge->payment_status = $charge->paid_amount >= (float) $charge->amount ? 'paid' : 'partial';
            $charge->save();

            $this->syncSourceFromCharge($charge, $payment);

            $remaining -= $allocatedAmount;
        }

        if ($firstCharge instanceof PatientCharge && $firstCharge->source instanceof OpdPatient && filled($payment->payment_mode)) {
            $firstCharge->source->update([
                'payment_mode' => $payment->payment_mode,
            ]);
        }

        return $payment;
    }

    public function applyDiscount(Patient $patient, float $discountAmount, ?array $chargeIds = null): array
    {
        $charges = $this->pendingChargesQuery($patient, $chargeIds, true)->get();
        $remaining = max(0, (float) $discountAmount);
        $appliedAmount = 0.0;
        $affectedChargeIds = [];

        foreach ($charges as $charge) {
            if ($remaining <= 0) {
                break;
            }

            $due = max(0, (float) $charge->amount - (float) $charge->paid_amount);
            if ($due <= 0) {
                continue;
            }

            $apply = min($remaining, $due);
            if ($apply <= 0) {
                continue;
            }

            $charge->discount_amount = (float) $charge->discount_amount + $apply;
            $charge->net_amount = max(0, (float) $charge->net_amount - $apply);
            $charge->amount = max(0, (float) $charge->amount - $apply);
            $charge->payment_status = (float) $charge->paid_amount >= (float) $charge->amount
                ? 'paid'
                : ((float) $charge->paid_amount > 0 ? 'partial' : 'unpaid');
            $charge->save();

            $this->syncSourceFromCharge($charge);

            $appliedAmount += $apply;
            $remaining -= $apply;
            $affectedChargeIds[] = (int) $charge->id;
        }

        return [
            'applied_amount' => $appliedAmount,
            'affected_charge_ids' => $affectedChargeIds,
            'remaining_discount' => max(0, $remaining),
        ];
    }

    public function deletePayment(PatientPayment $payment): void
    {
        $payment->load(['allocations.charge']);

        foreach ($payment->allocations as $allocation) {
            $charge = $allocation->charge;
            if (!$charge) {
                continue;
            }

            $charge->paid_amount = max(0, (float) $charge->paid_amount - (float) $allocation->amount);
            if ((float) $charge->paid_amount <= 0) {
                $charge->payment_status = 'unpaid';
            } else {
                $charge->payment_status = (float) $charge->paid_amount >= (float) $charge->amount ? 'paid' : 'partial';
            }

            $charge->save();
            $this->syncSourceFromCharge($charge);
        }

        $payment->allocations()->delete();
        $payment->delete();
    }

    public function removeChargeAndRebalance(PatientCharge $charge): array
    {
        $patientId = (int) $charge->patient_id;
        $releasedAmount = (float) $charge->allocations()->sum('amount');

        $charge->allocations()->delete();
        $charge->delete();

        // Re-apply any released credit to remaining pending charges.
        $this->applyAvailableCredit($patientId);

        return [
            'released_amount' => $releasedAmount,
            'unallocated_credit' => $this->getUnallocatedCredit($patientId),
        ];
    }

    protected function pendingChargesQuery(Patient $patient, ?array $chargeIds = null, bool $lockForUpdate = false)
    {
        $query = PatientCharge::query()
            ->where('patient_id', $patient->id)
            ->whereColumn('paid_amount', '<', 'amount')
            ->orderBy('charged_at')
            ->orderBy('id');

        if (!empty($chargeIds)) {
            $query->whereIn('id', collect($chargeIds)->map(fn ($id) => (int) $id)->unique()->values()->all());
        }

        if ($lockForUpdate) {
            $query->lockForUpdate();
        }

        return $query;
    }

    protected function resolveChargeMaster(array $attributes): ?ChargeMaster
    {
        if (blank($attributes['charge_code'])) {
            return null;
        }

        return ChargeMaster::query()
            ->where('hospital_id', $attributes['hospital_id'])
            ->where('code', $attributes['charge_code'])
            ->first();
    }

    protected function applyAvailableCredit(int $patientId, ?array $chargeIds = null): void
    {
        $charges = PatientCharge::query()
            ->where('patient_id', $patientId)
            ->whereColumn('paid_amount', '<', 'amount')
            ->when(!empty($chargeIds), function ($q) use ($chargeIds) {
                $q->whereIn('id', collect($chargeIds)->map(fn ($id) => (int) $id)->all());
            })
            ->orderBy('charged_at')
            ->orderBy('id')
            ->lockForUpdate()
            ->get();

        if ($charges->isEmpty()) {
            return;
        }

        $payments = PatientPayment::query()
            ->where('patient_id', $patientId)
            ->withSum('allocations as allocated_amount', 'amount')
            ->orderBy('paid_at')
            ->orderBy('id')
            ->lockForUpdate()
            ->get();

        foreach ($payments as $payment) {
            $available = max(0, (float) $payment->amount - (float) ($payment->allocated_amount ?? 0));
            if ($available <= 0) {
                continue;
            }

            foreach ($charges as $charge) {
                if ($available <= 0) {
                    break;
                }

                $due = max(0, (float) $charge->amount - (float) $charge->paid_amount);
                if ($due <= 0) {
                    continue;
                }

                $alloc = min($available, $due);
                $payment->allocations()->create([
                    'patient_charge_id' => $charge->id,
                    'amount' => $alloc,
                ]);

                $charge->paid_amount = (float) $charge->paid_amount + $alloc;
                $charge->payment_status = $charge->paid_amount >= (float) $charge->amount ? 'paid' : 'partial';
                $charge->save();

                $this->syncSourceFromCharge($charge, $payment);
                $available -= $alloc;
            }
        }
    }

    protected function syncSourceFromCharge(PatientCharge $charge, ?PatientPayment $payment = null): void
    {
        $source = $charge->source;

        if ($source instanceof DiagnosticOrderItem) {
            $source->update([
                'standard_charge' => $charge->unit_rate,
                'discount_amount' => $charge->discount_amount,
                'tax_amount' => $charge->tax_amount,
                'net_amount' => $charge->net_amount,
                'paid_amount' => $charge->paid_amount,
                'payment_status' => $charge->payment_status,
                'payment_mode' => $payment?->payment_mode ?? $source->payment_mode,
                'payment_reference' => $payment?->reference ?? $source->payment_reference,
                'billed_at' => $charge->charged_at ?? $source->billed_at,
                'paid_at' => (float) $charge->paid_amount > 0 ? ($payment?->paid_at ?? $source->paid_at ?? now()) : null,
            ]);
        }
    }

    public function getUnallocatedCredit(int $patientId): float
    {
        $payments = PatientPayment::query()
            ->where('patient_id', $patientId)
            ->withSum('allocations as allocated_amount', 'amount')
            ->lockForUpdate()
            ->get();

        return (float) $payments->sum(function (PatientPayment $payment) {
            return max(0, (float) $payment->amount - (float) ($payment->allocated_amount ?? 0));
        });
    }

    protected function singleValue(Collection $charges, string $key): mixed
    {
        $values = $charges->pluck($key)->filter()->unique()->values();

        return $values->count() === 1 ? $values->first() : null;
    }
}