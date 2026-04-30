<?php

namespace App\Http\Controllers\Hospital;

use App\CentralLogics\Helpers;
use App\Http\Controllers\BaseHospitalController;
use App\Models\Patient;
use App\Models\PatientCharge;
use App\Models\PatientPayment;
use App\Models\PatientPaymentAllocation;
use App\Models\OpdPatient;
use App\Models\HeaderFooter;
use App\Services\ChargeLedgerService;
use App\Services\PatientTimelineService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class PatientChargeController extends BaseHospitalController
{
    protected function getAvailableCredit(Patient $patient): float
    {
        $totalPayments = (float) PatientPayment::query()
            ->where('patient_id', $patient->id)
            ->sum('amount');

        $totalAllocated = (float) PatientPaymentAllocation::query()
            ->whereHas('charge', function ($query) use ($patient) {
                $query->where('patient_id', $patient->id);
            })
            ->sum('amount');

        return max(0, $totalPayments - $totalAllocated);
    }

    public function showPaymentForm(Request $request, Patient $patient, ChargeLedgerService $chargeLedger)
    {
        $this->authorizePatient($patient);

        $chargeIds = collect($request->input('charge_ids', []))
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        $charges = $chargeLedger->getPendingChargesForPatient($patient, $chargeIds ?: null);

        return view('hospital.opd-patient.partials.charge_payment_form', [
            'patient' => $patient,
            'charges' => $charges,
            'submitUrl' => route('hospital.opd-patient.charges.collect-payment', ['patient' => $patient->id]),
            'chargeIds' => $charges->pluck('id')->all(),
            'outstandingAmount' => $chargeLedger->getOutstandingAmount($charges),
            'title' => $request->input('title') ?: 'Collect Payment | ' . $patient->name,
            'contextNote' => $request->input('context_note'),
        ]);
    }

    public function showRefundForm(Request $request, Patient $patient)
    {
        $this->authorizePatient($patient);

        $availableCredit = $this->getAvailableCredit($patient);

        return view('hospital.opd-patient.partials.refund_payment_form', [
            'patient' => $patient,
            'submitUrl' => route('hospital.opd-patient.charges.refund-advance', ['patient' => $patient->id]),
            'availableCredit' => $availableCredit,
            'title' => $request->input('title') ?: 'Refund Advance | ' . $patient->name,
        ]);
    }

    public function showDiscountForm(Request $request, Patient $patient, ChargeLedgerService $chargeLedger)
    {
        $this->authorizePatient($patient);

        $chargeIds = collect($request->input('charge_ids', []))
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        $charges = $chargeLedger->getPendingChargesForPatient($patient, $chargeIds ?: null);

        return view('hospital.opd-patient.partials.charge_discount_form', [
            'patient' => $patient,
            'charges' => $charges,
            'submitUrl' => route('hospital.opd-patient.charges.apply-discount', ['patient' => $patient->id]),
            'chargeIds' => $charges->pluck('id')->all(),
            'outstandingAmount' => $chargeLedger->getOutstandingAmount($charges),
            'title' => $request->input('title') ?: 'Apply Discount | ' . $patient->name,
            'contextNote' => $request->input('context_note'),
        ]);
    }

    public function collectPayment(Request $request, Patient $patient, ChargeLedgerService $chargeLedger, PatientTimelineService $timelineService)
    {
        $this->authorizePatient($patient);

        $validator = Validator::make($request->all(), [
            'amount' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'payment_mode' => 'nullable|string|max:255',
            'reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'charge_ids' => 'nullable|array',
            'charge_ids.*' => 'integer',
            'is_advance' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        $chargeIds = collect($request->input('charge_ids', []))
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        $paymentAmount = max(0, (float) $request->input('amount', 0));
        $discountAmount = max(0, (float) $request->input('discount_amount', 0));
        $settlementAmount = $paymentAmount + $discountAmount;

        if ($settlementAmount <= 0) {
            return response()->json([
                'errors' => [
                    ['code' => 'amount', 'message' => 'Enter payment amount or discount amount.'],
                ],
            ], 422);
        }

        $isAdvance = $request->boolean('is_advance');

        if ($isAdvance) {
            if ($discountAmount > 0) {
                return response()->json([
                    'errors' => [
                        ['code' => 'discount_amount', 'message' => 'Discount is not allowed for advance payment.'],
                    ],
                ], 422);
            }

            $payment = DB::transaction(function () use ($request, $patient) {
                return PatientPayment::create([
                    'hospital_id' => $this->hospital_id,
                    'patient_id' => $patient->id,
                    'visitable_type' => null,
                    'visitable_id' => null,
                    'amount' => (float) $request->amount,
                    'payment_mode' => $request->payment_mode,
                    'reference' => $request->reference,
                    'notes' => $request->notes,
                    'received_by' => auth()->id(),
                    'paid_at' => now(),
                ]);
            });

            $latestOpdVisit = OpdPatient::query()
                ->where('patient_id', $patient->id)
                ->latest('appointment_date')
                ->latest('id')
                ->first();

            $timelinePayload = [
                'event_key' => 'patient.payment.collected',
                'title' => 'Advance Payment Collected',
                'description' => 'Advance payment of ' . number_format((float) $request->amount, 2) . ' has been collected.',
                'meta' => [
                    'payment_id' => $payment->id,
                    'amount' => (float) $request->amount,
                    'payment_mode' => $request->payment_mode,
                    'is_advance' => true,
                ],
                'logged_at' => $payment->paid_at,
            ];

            if ($latestOpdVisit) {
                $timelineService->logForOpdVisit($latestOpdVisit, $timelinePayload);
            } else {
                $timelineService->log($patient, $timelinePayload);
            }

            return response()->json(['status' => true, 'message' => 'Advance payment received successfully.']);
        }

        $charges = $chargeLedger->getPendingChargesForPatient($patient, $chargeIds ?: null);
        $outstandingAmount = $chargeLedger->getOutstandingAmount($charges);

        if ($charges->isEmpty() || $outstandingAmount <= 0) {
            return response()->json([
                'errors' => [
                    ['code' => 'amount', 'message' => 'No pending charge available for payment.'],
                ],
            ], 422);
        }

        if ($settlementAmount > $outstandingAmount) {
            return response()->json([
                'errors' => [
                    ['code' => 'amount', 'message' => 'Payment + discount cannot be greater than pending due amount.'],
                ],
            ], 422);
        }

        $transactionSummary = DB::transaction(function () use ($request, $patient, $chargeLedger, $chargeIds, $paymentAmount, $discountAmount) {
            $discountSummary = null;
            $payment = null;

            if ($discountAmount > 0) {
                $discountSummary = $chargeLedger->applyDiscount($patient, $discountAmount, $chargeIds ?: null);
            }

            if ($paymentAmount > 0) {
                $payment = $chargeLedger->collectPayment(
                    $patient,
                    [
                        'amount' => $paymentAmount,
                        'payment_mode' => $request->payment_mode,
                        'reference' => $request->reference,
                        'notes' => $request->notes,
                    ],
                    $chargeIds ?: null
                );
            }

            return [
                'payment' => $payment,
                'discount' => $discountSummary,
            ];
        });

        $payment = $transactionSummary['payment'] ?? null;
        $discountSummary = $transactionSummary['discount'] ?? null;

        $targetOpdVisit = null;
        if (!empty($chargeIds)) {
            $targetVisitId = PatientCharge::query()
                ->whereIn('id', $chargeIds)
                ->where('visitable_type', OpdPatient::class)
                ->value('visitable_id');

            if ($targetVisitId) {
                $targetOpdVisit = OpdPatient::query()->find($targetVisitId);
            }
        }

        if (!$targetOpdVisit) {
            $targetOpdVisit = OpdPatient::query()
                ->where('patient_id', $patient->id)
                ->latest('appointment_date')
                ->latest('id')
                ->first();
        }

        $timelinePayload = [
            'event_key' => 'patient.payment.collected',
            'title' => 'Payment / Discount Applied',
            'description' => 'Payment of ' . number_format($paymentAmount, 2) . ' and discount of ' . number_format($discountAmount, 2) . ' applied on pending charges.',
            'meta' => [
                'payment_id' => $payment?->id,
                'amount' => $paymentAmount,
                'discount_amount' => $discountAmount,
                'discount_applied' => (float) ($discountSummary['applied_amount'] ?? 0),
                'discount_affected_charge_ids' => $discountSummary['affected_charge_ids'] ?? [],
                'payment_mode' => $request->payment_mode,
                'allocated_charge_ids' => $chargeIds,
                'is_advance' => false,
            ],
            'logged_at' => $payment?->paid_at ?? now(),
        ];

        if ($targetOpdVisit) {
            $timelineService->logForOpdVisit($targetOpdVisit, $timelinePayload);
        } else {
            $timelineService->log($patient, $timelinePayload);
        }

        return response()->json(['status' => true, 'message' => 'Payment/discount applied successfully.']);
    }

    public function applyDiscount(Request $request, Patient $patient, ChargeLedgerService $chargeLedger, PatientTimelineService $timelineService)
    {
        $this->authorizePatient($patient);

        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string',
            'charge_ids' => 'nullable|array',
            'charge_ids.*' => 'integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        $chargeIds = collect($request->input('charge_ids', []))
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        $charges = $chargeLedger->getPendingChargesForPatient($patient, $chargeIds ?: null);
        $outstandingAmount = $chargeLedger->getOutstandingAmount($charges);
        $requestedDiscount = (float) $request->amount;

        if ($charges->isEmpty() || $outstandingAmount <= 0) {
            return response()->json([
                'errors' => [
                    ['code' => 'amount', 'message' => 'No pending charge available for discount.'],
                ],
            ], 422);
        }

        if ($requestedDiscount > $outstandingAmount) {
            return response()->json([
                'errors' => [
                    ['code' => 'amount', 'message' => 'Discount cannot be greater than pending due amount.'],
                ],
            ], 422);
        }

        $summary = DB::transaction(function () use ($chargeLedger, $patient, $requestedDiscount, $chargeIds) {
            return $chargeLedger->applyDiscount($patient, $requestedDiscount, $chargeIds ?: null);
        });

        $targetOpdVisit = null;
        if (!empty($chargeIds)) {
            $targetVisitId = PatientCharge::query()
                ->whereIn('id', $chargeIds)
                ->where('visitable_type', OpdPatient::class)
                ->value('visitable_id');

            if ($targetVisitId) {
                $targetOpdVisit = OpdPatient::query()->find($targetVisitId);
            }
        }

        if (!$targetOpdVisit) {
            $targetOpdVisit = OpdPatient::query()
                ->where('patient_id', $patient->id)
                ->latest('appointment_date')
                ->latest('id')
                ->first();
        }

        $timelinePayload = [
            'event_key' => 'patient.charge.discount_applied',
            'title' => 'Charge Discount Applied',
            'description' => 'Discount of ' . number_format((float) ($summary['applied_amount'] ?? 0), 2) . ' has been applied to pending charges.',
            'meta' => [
                'discount_amount' => (float) ($summary['applied_amount'] ?? 0),
                'affected_charge_ids' => $summary['affected_charge_ids'] ?? [],
                'notes' => $request->notes,
            ],
            'logged_at' => now(),
        ];

        if ($targetOpdVisit) {
            $timelineService->logForOpdVisit($targetOpdVisit, $timelinePayload);
        } else {
            $timelineService->log($patient, $timelinePayload);
        }

        return response()->json([
            'status' => true,
            'message' => 'Discount applied successfully.',
        ]);
    }

    public function printFinalBill(Patient $patient)
    {
        $this->authorizePatient($patient);

        $charges = PatientCharge::query()
            ->with('visitable')
            ->where('patient_id', $patient->id)
            ->orderBy('charged_at')
            ->orderBy('id')
            ->get();
        $payments = PatientPayment::query()
            ->with('visitable')
            ->where('patient_id', $patient->id)
            ->orderBy('paid_at')
            ->orderBy('id')
            ->get();

        $allocations = PatientPaymentAllocation::query()
            ->with(['charge'])
            ->whereHas('charge', function ($query) use ($patient) {
                $query->where('patient_id', $patient->id);
            })
            ->get();

        $totalCharges = (float) $charges->sum('amount');
        $totalPayments = (float) $payments->sum('amount');
        $totalAllocated = (float) $allocations->sum('amount');
        $totalPaid = min($totalCharges, $totalAllocated);
        $balance = max(0, $totalCharges - $totalAllocated);
        $advanceCredit = max(0, $totalPayments - $totalAllocated);

        $groupKeyResolver = static function (?string $type, ?int $id): string {
            if (!$type || !$id) {
                return 'unassigned';
            }

            return $type . ':' . $id;
        };

        $chargesByVisit = $charges->groupBy(function (PatientCharge $charge) use ($groupKeyResolver) {
            return $groupKeyResolver($charge->visitable_type, $charge->visitable_id ? (int) $charge->visitable_id : null);
        });
        $paidByVisit = $allocations->groupBy(function (PatientPaymentAllocation $allocation) use ($groupKeyResolver) {
            $charge = $allocation->charge;
            return $groupKeyResolver($charge?->visitable_type, $charge?->visitable_id ? (int) $charge->visitable_id : null);
        })->map(function ($visitAllocations) {
            return (float) $visitAllocations->sum('amount');
        });

        $visitKeys = $chargesByVisit->keys()->unique()->values();

        $visitBills = $visitKeys->map(function (string $key) use ($chargesByVisit, $paidByVisit) {
            $visitCharges = $chargesByVisit->get($key, collect());
            $firstCharge = $visitCharges->first();
            $visit = $firstCharge?->visitable;
            $sortAt = optional($visit?->appointment_date)
                ?? optional($firstCharge?->charged_at)
                ?? now();

            $visitTotalCharges = (float) $visitCharges->sum('amount');
            $visitTotalPaid = (float) ($paidByVisit->get($key, 0));

            return [
                'key' => $key,
                'visit' => $visit,
                'charges' => $visitCharges,
                'total_charges' => $visitTotalCharges,
                'total_paid' => $visitTotalPaid,
                'total_due' => max(0, $visitTotalCharges - $visitTotalPaid),
                'sort_at' => $sortAt,
            ];
        })->sortBy('sort_at')->values();

        $printTemplate = HeaderFooter::query()
            ->where('type', 'opd_bill')
            ->first();

        return view('hospital.opd-patient.partials.final_bill_print', [
            'patient' => $patient,
            'charges' => $charges,
            'payments' => $payments,
            'totalCharges' => $totalCharges,
            'totalPaid' => $totalPaid,
            'balance' => $balance,
            'advanceCredit' => $advanceCredit,
            'visitBills' => $visitBills,
            'hospital' => auth()->user()?->hospital,
            'printTemplate' => $printTemplate,
        ]);
    }

    public function refundAdvance(Request $request, Patient $patient, PatientTimelineService $timelineService)
    {
        $this->authorizePatient($patient);

        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0.01',
            'payment_mode' => 'nullable|string|max:255',
            'reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 422);
        }

        $requestedAmount = (float) $request->amount;
        $availableCredit = $this->getAvailableCredit($patient);

        if ($availableCredit <= 0) {
            return response()->json([
                'errors' => [
                    ['code' => 'amount', 'message' => 'No advance credit available for refund.'],
                ],
            ], 422);
        }

        if ($requestedAmount > $availableCredit) {
            return response()->json([
                'errors' => [
                    ['code' => 'amount', 'message' => 'Refund amount cannot be greater than available advance credit.'],
                ],
            ], 422);
        }

        $refundPayment = DB::transaction(function () use ($patient, $request, $requestedAmount) {
            return PatientPayment::create([
                'hospital_id' => $this->hospital_id,
                'patient_id' => $patient->id,
                'visitable_type' => null,
                'visitable_id' => null,
                'amount' => -1 * $requestedAmount,
                'payment_mode' => $request->payment_mode,
                'reference' => $request->reference,
                'notes' => $request->notes,
                'received_by' => auth()->id(),
                'paid_at' => now(),
            ]);
        });

        $remainingCredit = $this->getAvailableCredit($patient);
        $latestOpdVisit = OpdPatient::query()
            ->where('patient_id', $patient->id)
            ->latest('appointment_date')
            ->latest('id')
            ->first();

        $timelinePayload = [
            'event_key' => 'patient.payment.refunded',
            'title' => 'Advance Refunded',
            'description' => 'Advance refund of ' . number_format($requestedAmount, 2) . ' has been processed.',
            'meta' => [
                'payment_id' => $refundPayment->id,
                'amount' => $requestedAmount,
                'payment_mode' => $refundPayment->payment_mode,
                'reference' => $refundPayment->reference,
                'remaining_credit' => $remainingCredit,
            ],
            'logged_at' => $refundPayment->paid_at,
        ];

        if ($latestOpdVisit) {
            $timelineService->logForOpdVisit($latestOpdVisit, $timelinePayload);
        } else {
            $timelineService->log($patient, $timelinePayload);
        }

        return response()->json([
            'status' => true,
            'message' => 'Advance refund processed successfully.',
        ]);
    }

    public function printVisitBill(Patient $patient, OpdPatient $opdPatient)
    {
        $this->authorizePatient($patient);

        abort_if((int) $opdPatient->hospital_id !== (int) $this->hospital_id, 403, 'Unauthorized visit.');
        abort_if((int) $opdPatient->patient_id !== (int) $patient->id, 403, 'Unauthorized visit.');

        $charges = PatientCharge::query()
            ->where('patient_id', $patient->id)
            ->where('visitable_type', OpdPatient::class)
            ->where('visitable_id', $opdPatient->id)
            ->orderBy('charged_at')
            ->orderBy('id')
            ->get();

        $paymentAllocations = PatientPaymentAllocation::query()
            ->with(['payment', 'charge'])
            ->whereHas('charge', function ($query) use ($patient, $opdPatient) {
                $query->where('patient_id', $patient->id)
                    ->where('visitable_type', OpdPatient::class)
                    ->where('visitable_id', $opdPatient->id);
            })
            ->get()
            ->sortBy(function (PatientPaymentAllocation $allocation) {
                return optional($allocation->payment?->paid_at)->timestamp
                    ?? optional($allocation->payment?->created_at)->timestamp
                    ?? 0;
            })
            ->values();

        $totalCharges = (float) $charges->sum('amount');
        $totalPaid = (float) $charges->sum('paid_amount');
        $balance = $totalCharges - $totalPaid;

        $printTemplate = HeaderFooter::query()
            ->where('type', 'opd_bill')
            ->first();

        return view('hospital.opd-patient.partials.visit_bill_print', [
            'patient' => $patient,
            'visit' => $opdPatient->loadMissing('consultant'),
            'charges' => $charges,
            'paymentAllocations' => $paymentAllocations,
            'totalCharges' => $totalCharges,
            'totalPaid' => $totalPaid,
            'balance' => $balance,
            'hospital' => auth()->user()?->hospital,
            'printTemplate' => $printTemplate,
        ]);
    }

    public function destroyPayment(Patient $patient, PatientPayment $payment, ChargeLedgerService $chargeLedger, PatientTimelineService $timelineService)
    {
        $this->authorizePatient($patient);

        abort_if((int) $payment->patient_id !== (int) $patient->id, Response::HTTP_FORBIDDEN, 'Unauthorized payment.');
        abort_if((int) $payment->hospital_id !== (int) $this->hospital_id, Response::HTTP_FORBIDDEN, 'Unauthorized payment.');

        $deletedPaymentId = $payment->id;
        $deletedAmount = (float) $payment->amount;
        $deletedMode = $payment->payment_mode;
        $deletedReference = $payment->reference;
        $linkedOpdVisit = ($payment->visitable_type === OpdPatient::class && $payment->visitable_id)
            ? OpdPatient::query()->find($payment->visitable_id)
            : OpdPatient::query()->where('patient_id', $patient->id)->latest('appointment_date')->latest('id')->first();

        DB::transaction(function () use ($chargeLedger, $payment) {
            $chargeLedger->deletePayment($payment);
        });

        $timelinePayload = [
            'event_key' => 'patient.payment.deleted',
            'title' => 'Payment Deleted',
            'description' => 'Payment of ' . number_format($deletedAmount, 2) . ' has been deleted and balances were recalculated.',
            'meta' => [
                'payment_id' => $deletedPaymentId,
                'amount' => $deletedAmount,
                'payment_mode' => $deletedMode,
                'reference' => $deletedReference,
            ],
        ];

        if ($linkedOpdVisit) {
            $timelineService->logForOpdVisit($linkedOpdVisit, $timelinePayload);
        } else {
            $timelineService->log($patient, $timelinePayload);
        }

        return response()->json([
            'status' => true,
            'message' => 'Payment deleted successfully and related charges were updated.',
        ]);
    }

    protected function authorizePatient(Patient $patient): void
    {
        abort_if($patient->hospital_id !== $this->hospital_id, 403, 'Unauthorized patient.');
    }
}
