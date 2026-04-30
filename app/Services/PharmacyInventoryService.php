<?php

namespace App\Services;

use App\Models\PharmacyPurchaseBill;
use App\Models\PharmacySaleBill;
use App\Models\PharmacyStockBatch;
use App\Models\PharmacyStockLedger;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class PharmacyInventoryService
{
    public function __construct(
        protected PharmacyBillNumberService $billNumberService,
        protected ChargeLedgerService $chargeLedger
    ) {
    }

    public function createPurchaseBill(array $payload): PharmacyPurchaseBill
    {
        return DB::transaction(function () use ($payload) {
            $hospitalId = (int) $payload['hospital_id'];
            $billDate = Arr::get($payload, 'bill_date', now()->toDateString());
            $items = Arr::get($payload, 'items', []);

            if (empty($items)) {
                throw new RuntimeException('At least one purchase item is required.');
            }

            $bill = PharmacyPurchaseBill::create([
                'hospital_id' => $hospitalId,
                'bill_no' => Arr::get($payload, 'bill_no') ?: $this->billNumberService->nextPurchaseBillNo($hospitalId, new \DateTime($billDate)),
                'bill_date' => $billDate,
                'supplier_id' => Arr::get($payload, 'supplier_id') ?: null,
                'supplier_name' => Arr::get($payload, 'supplier_name'),
                'supplier_invoice_no' => Arr::get($payload, 'supplier_invoice_no'),
                'notes' => Arr::get($payload, 'notes'),
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);

            $subtotal = 0.0;
            $taxTotal = 0.0;
            $discountTotal = 0.0;

            foreach ($items as $item) {
                $qtyPurchased = (float) Arr::get($item, 'quantity_purchased', 0);
                $qtyFree = (float) Arr::get($item, 'quantity_free', 0);
                $totalQty = $qtyPurchased + $qtyFree;

                if ($totalQty <= 0) {
                    throw new RuntimeException('Purchase item quantity must be greater than zero.');
                }

                $purchasePrice = (float) Arr::get($item, 'unit_purchase_price', 0);
                $salePrice = (float) Arr::get($item, 'unit_sale_price', 0);
                $mrp = (float) Arr::get($item, 'unit_mrp', $salePrice);
                $taxPercent = (float) Arr::get($item, 'tax_percent', 0);

                $lineSubtotal = round($qtyPurchased * $purchasePrice, 2);
                $lineTax = round(($lineSubtotal * $taxPercent) / 100, 2);
                $lineTotal = round($lineSubtotal + $lineTax, 2);

                $purchaseItem = $bill->items()->create([
                    'medicine_id' => (int) Arr::get($item, 'medicine_id'),
                    'batch_no' => (string) Arr::get($item, 'batch_no'),
                    'mfg_date' => Arr::get($item, 'mfg_date'),
                    'expiry_date' => Arr::get($item, 'expiry_date'),
                    'pack_size' => Arr::get($item, 'pack_size'),
                    'unit_purchase_price' => $purchasePrice,
                    'unit_sale_price' => $salePrice,
                    'unit_mrp' => $mrp,
                    'quantity_purchased' => $qtyPurchased,
                    'quantity_free' => $qtyFree,
                    'quantity_received' => 0,
                    'total_quantity' => $totalQty,
                    'discount_percent' => 0,
                    'tax_percent' => $taxPercent,
                    'tax_amount' => $lineTax,
                    'line_subtotal' => $lineSubtotal,
                    'line_total' => $lineTotal,
                ]);

                $batch = PharmacyStockBatch::create([
                    'hospital_id' => $hospitalId,
                    'medicine_id' => (int) Arr::get($item, 'medicine_id'),
                    'purchase_item_id' => $purchaseItem->id,
                    'batch_no' => (string) Arr::get($item, 'batch_no'),
                    'mfg_date' => Arr::get($item, 'mfg_date'),
                    'expiry_date' => Arr::get($item, 'expiry_date'),
                    'unit_purchase_price' => $purchasePrice,
                    'unit_sale_price' => $salePrice,
                    'unit_mrp' => $mrp,
                    'available_qty' => $totalQty,
                    'status' => 'active',
                    'received_at' => now(),
                ]);

                $this->createLedgerEntry([
                    'hospital_id' => $hospitalId,
                    'medicine_id' => (int) Arr::get($item, 'medicine_id'),
                    'stock_batch_id' => $batch->id,
                    'reference_type' => PharmacyPurchaseBill::class,
                    'reference_id' => $bill->id,
                    'entry_type' => 'in',
                    'quantity' => $totalQty,
                    'balance_after' => $batch->available_qty,
                    'unit_purchase_price' => $purchasePrice,
                    'unit_sale_price' => $salePrice,
                    'remarks' => 'Purchase stock inward',
                ]);

                $subtotal += $lineSubtotal;
                $taxTotal += $lineTax;
            }

            $shipping = (float) Arr::get($payload, 'shipping_amount', 0);
            $roundOff = (float) Arr::get($payload, 'round_off', 0);

            // Bill-level discount: percent or fixed
            $discountType = Arr::get($payload, 'discount_type', 'fixed');
            $discountValue = (float) Arr::get($payload, 'discount_value', 0);
            if ($discountType === 'percent') {
                $billDiscount = round(($subtotal * $discountValue) / 100, 2);
            } else {
                $billDiscount = round($discountValue, 2);
            }

            $netTotal = round(max(0, $subtotal - $billDiscount + $taxTotal + $shipping + $roundOff), 2);

            $bill->update([
                'subtotal' => round($subtotal, 2),
                'discount_amount' => $billDiscount,
                'discount_type' => $discountType,
                'tax_amount' => round($taxTotal, 2),
                'shipping_amount' => $shipping,
                'round_off' => $roundOff,
                'net_total' => $netTotal,
                'paid_amount' => $netTotal,
                'due_amount' => 0,
                'payment_status' => 'paid',
                'updated_by' => auth()->id(),
            ]);

            return $bill->fresh(['items']);
        });
    }

    /**
     * Update a purchase bill's header/financial fields (items unchanged to protect stock).
     */
    public function updatePurchaseBill(PharmacyPurchaseBill $bill, array $payload): PharmacyPurchaseBill
    {
        return DB::transaction(function () use ($bill, $payload) {
            $subtotal = (float) $bill->items()->sum('line_subtotal');
            $taxAmount = (float) $bill->items()->sum('tax_amount');

            $discountType = Arr::get($payload, 'discount_type', 'fixed');
            $discountValue = (float) Arr::get($payload, 'discount_value', 0);
            if ($discountType === 'percent') {
                $discountAmount = round(($subtotal * $discountValue) / 100, 2);
            } else {
                $discountAmount = round($discountValue, 2);
            }

            $shipping = (float) Arr::get($payload, 'shipping_amount', 0);
            $roundOff = (float) Arr::get($payload, 'round_off', 0);
            $netTotal = round(max(0, $subtotal - $discountAmount + $taxAmount + $shipping + $roundOff), 2);

            $bill->update([
                'bill_date' => Arr::get($payload, 'bill_date'),
                'supplier_id' => Arr::get($payload, 'supplier_id') ?: null,
                'supplier_name' => Arr::get($payload, 'supplier_name'),
                'supplier_invoice_no' => Arr::get($payload, 'supplier_invoice_no'),
                'notes' => Arr::get($payload, 'notes'),
                'discount_type' => $discountType,
                'discount_amount' => $discountAmount,
                'shipping_amount' => $shipping,
                'round_off' => $roundOff,
                'net_total' => $netTotal,
                'paid_amount' => $netTotal,
                'due_amount' => 0,
                'payment_status' => 'paid',
                'updated_by' => auth()->id(),
            ]);

            return $bill->fresh();
        });
    }

    public function createSaleBill(array $payload): PharmacySaleBill
    {
        return DB::transaction(function () use ($payload) {
            $hospitalId = (int) $payload['hospital_id'];
            $billDate = Arr::get($payload, 'bill_date', now()->toDateString());
            $items = Arr::get($payload, 'items', []);

            if (empty($items)) {
                throw new RuntimeException('At least one sale item is required.');
            }

            $saleBill = PharmacySaleBill::create([
                'hospital_id' => $hospitalId,
                'patient_id' => Arr::get($payload, 'patient_id'),
                'visitable_type' => Arr::get($payload, 'visitable_type'),
                'visitable_id' => Arr::get($payload, 'visitable_id'),
                'source_type' => Arr::get($payload, 'source_type'),
                'source_id' => Arr::get($payload, 'source_id'),
                'opd_prescription_id' => Arr::get($payload, 'opd_prescription_id'),
                'ipd_prescription_id' => Arr::get($payload, 'ipd_prescription_id'),
                'bill_no' => Arr::get($payload, 'bill_no') ?: $this->billNumberService->nextSaleBillNo($hospitalId, new \DateTime($billDate)),
                'bill_date' => $billDate,
                'is_from_prescription' => (bool) Arr::get($payload, 'is_from_prescription', false),
                'notes' => Arr::get($payload, 'notes'),
                'created_by' => auth()->id(),
            ]);

            $subtotal = 0.0;
            $discountTotal = 0.0;
            $taxTotal = 0.0;

            foreach ($items as $item) {
                $medicineId = (int) Arr::get($item, 'medicine_id');
                $requiredQty = (float) Arr::get($item, 'quantity', 0);

                if ($requiredQty <= 0) {
                    throw new RuntimeException('Sale quantity must be greater than zero.');
                }

                $remaining = $requiredQty;
                $selectedBatchId = Arr::get($item, 'stock_batch_id');

                $batchesQuery = PharmacyStockBatch::query()
                    ->where('hospital_id', $hospitalId)
                    ->where('medicine_id', $medicineId)
                    ->where('status', 'active')
                    ->where('available_qty', '>', 0)
                    ->where(function ($query) {
                        $query->whereNull('expiry_date')->orWhere('expiry_date', '>=', now()->toDateString());
                    })
                    ->orderByRaw('CASE WHEN expiry_date IS NULL THEN 1 ELSE 0 END')
                    ->orderBy('expiry_date')
                    ->orderBy('id')
                    ->lockForUpdate();

                if ($selectedBatchId) {
                    $batchesQuery->where('id', (int) $selectedBatchId);
                }

                $batches = $batchesQuery->get();
                if ($batches->isEmpty()) {
                    throw new RuntimeException('Stock not available for selected medicine.');
                }

                foreach ($batches as $batch) {
                    if ($remaining <= 0) {
                        break;
                    }

                    $consumeQty = min($remaining, (float) $batch->available_qty);
                    if ($consumeQty <= 0) {
                        continue;
                    }

                    $unitPrice = (float) Arr::get($item, 'unit_price', $batch->unit_sale_price);
                    $unitMrp = (float) Arr::get($item, 'unit_mrp', $batch->unit_mrp);
                    $discountPercent = (float) Arr::get($item, 'discount_percent', 0);
                    $taxPercent = (float) Arr::get($item, 'tax_percent', 0);

                    $lineSubtotal = round($consumeQty * $unitPrice, 2);
                    $lineDiscount = round(($lineSubtotal * $discountPercent) / 100, 2);
                    $taxable = max(0, $lineSubtotal - $lineDiscount);
                    $lineTax = round(($taxable * $taxPercent) / 100, 2);
                    $lineTotal = round($taxable + $lineTax, 2);

                    $saleBill->items()->create([
                        'medicine_id' => $medicineId,
                        'stock_batch_id' => $batch->id,
                        'batch_no' => $batch->batch_no,
                        'expiry_date' => $batch->expiry_date,
                        'quantity' => $consumeQty,
                        'unit_price' => $unitPrice,
                        'unit_mrp' => $unitMrp,
                        'discount_percent' => $discountPercent,
                        'discount_amount' => $lineDiscount,
                        'tax_percent' => $taxPercent,
                        'tax_amount' => $lineTax,
                        'line_subtotal' => $lineSubtotal,
                        'line_total' => $lineTotal,
                        'is_substituted' => (bool) Arr::get($item, 'is_substituted', false),
                        'substitution_note' => Arr::get($item, 'substitution_note'),
                    ]);

                    $batch->available_qty = max(0, (float) $batch->available_qty - $consumeQty);
                    if ((float) $batch->available_qty <= 0) {
                        $batch->status = 'out_of_stock';
                    }
                    $batch->save();

                    $this->createLedgerEntry([
                        'hospital_id' => $hospitalId,
                        'medicine_id' => $medicineId,
                        'stock_batch_id' => $batch->id,
                        'reference_type' => PharmacySaleBill::class,
                        'reference_id' => $saleBill->id,
                        'entry_type' => 'out',
                        'quantity' => $consumeQty,
                        'balance_after' => $batch->available_qty,
                        'unit_purchase_price' => $batch->unit_purchase_price,
                        'unit_sale_price' => $unitPrice,
                        'remarks' => 'Sale stock outward',
                    ]);

                    $remaining -= $consumeQty;
                    $subtotal += $lineSubtotal;
                    $discountTotal += $lineDiscount;
                    $taxTotal += $lineTax;
                }

                if ($remaining > 0) {
                    throw new RuntimeException('Insufficient stock for medicine id ' . $medicineId . '.');
                }
            }

            $extraDiscount = (float) Arr::get($payload, 'discount_amount', 0);
            $netTotal = round(max(0, $subtotal - ($discountTotal + $extraDiscount) + $taxTotal), 2);
            $paidAmount = (float) Arr::get($payload, 'paid_amount', 0);
            $dueAmount = max(0, round($netTotal - $paidAmount, 2));

            $saleBill->update([
                'subtotal' => round($subtotal, 2),
                'discount_amount' => round($discountTotal + $extraDiscount, 2),
                'tax_amount' => round($taxTotal, 2),
                'net_total' => $netTotal,
                'paid_amount' => $paidAmount,
                'due_amount' => $dueAmount,
                'payment_status' => $this->resolvePaymentStatus($netTotal, $paidAmount),
            ]);

            if (!empty($payload['patient_id']) && (bool) Arr::get($payload, 'create_patient_charge', true)) {
                $this->chargeLedger->upsertCharge([
                    'hospital_id' => $hospitalId,
                    'patient_id' => (int) $payload['patient_id'],
                    'visitable_type' => Arr::get($payload, 'visitable_type'),
                    'visitable_id' => Arr::get($payload, 'visitable_id'),
                    'source_type' => PharmacySaleBill::class,
                    'source_id' => $saleBill->id,
                    'module' => 'pharmacy',
                    'particular' => 'Pharmacy bill ' . $saleBill->bill_no,
                    'amount' => $saleBill->net_total,
                    'quantity' => 1,
                    'unit_rate' => $saleBill->net_total,
                    'discount_amount' => $saleBill->discount_amount,
                    'tax_amount' => $saleBill->tax_amount,
                    'net_amount' => $saleBill->net_total,
                    'charged_at' => now(),
                ]);
            }

            return $saleBill->fresh(['items']);
        });
    }

    public function markExpiredBatches(?int $hospitalId = null, ?\DateTimeInterface $asOf = null): int
    {
        return DB::transaction(function () use ($hospitalId, $asOf) {
            $date = ($asOf ?: now())->format('Y-m-d');

            $query = PharmacyStockBatch::query()
                ->whereNotNull('expiry_date')
                ->where('expiry_date', '<', $date)
                ->where('available_qty', '>', 0);

            if ($hospitalId) {
                $query->withoutGlobalScopes()->where('hospital_id', $hospitalId);
            }

            $batches = $query->lockForUpdate()->get();
            $affected = 0;

            foreach ($batches as $batch) {
                $expiredNow = (float) $batch->available_qty;
                if ($expiredNow <= 0) {
                    continue;
                }

                $batch->available_qty = 0;
                $batch->expired_qty = (float) $batch->expired_qty + $expiredNow;
                $batch->status = 'expired';
                $batch->last_expiry_processed_at = now();
                $batch->save();

                $this->createLedgerEntry([
                    'hospital_id' => $batch->hospital_id,
                    'medicine_id' => $batch->medicine_id,
                    'stock_batch_id' => $batch->id,
                    'reference_type' => PharmacyStockBatch::class,
                    'reference_id' => $batch->id,
                    'entry_type' => 'adjustment_expiry',
                    'quantity' => $expiredNow,
                    'balance_after' => $batch->available_qty,
                    'unit_purchase_price' => $batch->unit_purchase_price,
                    'unit_sale_price' => $batch->unit_sale_price,
                    'remarks' => 'Automatic expiry stock deduction',
                ]);

                $affected++;
            }

            return $affected;
        });
    }

    public function adjustBadStock(int $stockBatchId, float $quantity, string $reason = 'damaged'): PharmacyStockBatch
    {
        if ($quantity <= 0) {
            throw new RuntimeException('Adjustment quantity must be greater than zero.');
        }

        return DB::transaction(function () use ($stockBatchId, $quantity, $reason) {
            $batch = PharmacyStockBatch::query()->lockForUpdate()->findOrFail($stockBatchId);

            if ((float) $batch->available_qty < $quantity) {
                throw new RuntimeException('Bad stock quantity exceeds available stock.');
            }

            $batch->available_qty = (float) $batch->available_qty - $quantity;
            $batch->damaged_qty = (float) $batch->damaged_qty + $quantity;
            if ((float) $batch->available_qty <= 0) {
                $batch->status = 'out_of_stock';
            }
            $batch->save();

            $this->createLedgerEntry([
                'hospital_id' => $batch->hospital_id,
                'medicine_id' => $batch->medicine_id,
                'stock_batch_id' => $batch->id,
                'reference_type' => PharmacyStockBatch::class,
                'reference_id' => $batch->id,
                'entry_type' => 'adjustment_damage',
                'quantity' => $quantity,
                'balance_after' => $batch->available_qty,
                'unit_purchase_price' => $batch->unit_purchase_price,
                'unit_sale_price' => $batch->unit_sale_price,
                'remarks' => 'Bad stock adjustment: ' . $reason,
            ]);

            return $batch->fresh();
        });
    }

    public function getAvailableQuantity(int $medicineId, ?int $hospitalId = null): float
    {
        $query = PharmacyStockBatch::query()
            ->where('medicine_id', $medicineId)
            ->where('status', 'active')
            ->where('available_qty', '>', 0)
            ->where(function ($q) {
                $q->whereNull('expiry_date')->orWhere('expiry_date', '>=', now()->toDateString());
            });

        if ($hospitalId) {
            $query->withoutGlobalScopes()->where('hospital_id', $hospitalId);
        }

        return (float) $query->sum('available_qty');
    }

    protected function createLedgerEntry(array $attributes): PharmacyStockLedger
    {
        return PharmacyStockLedger::create([
            'hospital_id' => $attributes['hospital_id'],
            'medicine_id' => $attributes['medicine_id'],
            'stock_batch_id' => $attributes['stock_batch_id'] ?? null,
            'reference_type' => $attributes['reference_type'] ?? null,
            'reference_id' => $attributes['reference_id'] ?? null,
            'entry_type' => $attributes['entry_type'],
            'quantity' => (float) ($attributes['quantity'] ?? 0),
            'balance_after' => (float) ($attributes['balance_after'] ?? 0),
            'unit_purchase_price' => (float) ($attributes['unit_purchase_price'] ?? 0),
            'unit_sale_price' => (float) ($attributes['unit_sale_price'] ?? 0),
            'remarks' => $attributes['remarks'] ?? null,
            'entry_at' => now(),
            'created_by' => auth()->id(),
        ]);
    }

    protected function resolvePaymentStatus(float $netTotal, float $paidAmount): string
    {
        if ($paidAmount <= 0) {
            return 'pending';
        }

        if ($paidAmount >= $netTotal) {
            return 'paid';
        }

        return 'partial';
    }
}
