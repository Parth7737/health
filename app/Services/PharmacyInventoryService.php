<?php

namespace App\Services;

use App\Models\PharmacyGrn;
use App\Models\PharmacyPurchaseBill;
use App\Models\PharmacySaleBill;
use App\Models\PharmacyStockBatch;
use App\Models\PharmacyStockLedger;
use Carbon\Carbon;
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
                'bill_no'     => $this->billNumberService->nextPurchaseBillNo($hospitalId, new \DateTime($billDate)),
                'bill_date'   => $billDate,
                'supplier_id' => Arr::get($payload, 'supplier_id') ?: null,
                'notes'       => Arr::get($payload, 'notes'),
                'created_by'  => auth()->id(),
                'updated_by'  => auth()->id(),
            ]);

            $estimatedTotal = 0.0;

            foreach ($items as $item) {
                $qty = (float) Arr::get($item, 'quantity_purchased', 0);
                if ($qty <= 0) {
                    throw new RuntimeException('Purchase item quantity must be greater than zero.');
                }

                $estRate = (float) Arr::get($item, 'unit_purchase_price', 0);
                $lineEst = round($qty * $estRate, 2);

                $bill->items()->create([
                    'medicine_id'        => (int) Arr::get($item, 'medicine_id'),
                    'batch_no'           => '',
                    'quantity_purchased'  => $qty,
                    'quantity_free'       => 0,
                    'quantity_received'   => 0,
                    'total_quantity'      => $qty,
                    'unit_purchase_price' => $estRate,
                    'unit_sale_price'     => 0,
                    'unit_mrp'            => 0,
                    'tax_percent'         => 0,
                    'tax_amount'          => 0,
                    'line_subtotal'       => $lineEst,
                    'line_total'          => $lineEst,
                ]);

                $estimatedTotal += $lineEst;
            }

            $bill->update([
                'subtotal'       => round($estimatedTotal, 2),
                'net_total'      => round($estimatedTotal, 2),
                'paid_amount'    => 0,
                'due_amount'     => round($estimatedTotal, 2),
                'payment_status' => 'pending',
                'status'         => 'pending',
                'updated_by'     => auth()->id(),
            ]);

            return $bill->fresh(['items']);
        });
    }

    /**
     * Approve a pending PO — no stock inward here, that happens via GRN.
     */
    public function approvePurchaseBill(PharmacyPurchaseBill $bill): PharmacyPurchaseBill
    {
        if ($bill->status !== 'pending') {
            throw new RuntimeException('Only pending purchase orders can be approved.');
        }

        $bill->update([
            'status'      => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return $bill->fresh(['items']);
    }

    /**
     * Create a GRN against an approved PO.
     * Stock inward = accepted qty (received - rejected) per item.
     */
    public function createGRN(array $payload): PharmacyGrn
    {
        return DB::transaction(function () use ($payload) {
            $hospitalId = (int) $payload['hospital_id'];
            $bill = PharmacyPurchaseBill::with('items')->findOrFail($payload['purchase_bill_id']);

            if (! in_array($bill->status, ['approved', 'partially_received'])) {
                throw new RuntimeException('GRN can only be created against an approved purchase order.');
            }
            if ((int) $bill->hospital_id !== $hospitalId) {
                throw new RuntimeException('PO does not belong to this hospital.');
            }

            $grn = PharmacyGrn::create([
                'hospital_id'      => $hospitalId,
                'grn_no'           => $this->billNumberService->nextGrnNo($hospitalId),
                'purchase_bill_id' => $bill->id,
                'supplier_id'      => $bill->supplier_id,
                'invoice_no'       => Arr::get($payload, 'invoice_no'),
                'invoice_date'     => Arr::get($payload, 'invoice_date'),
                'vehicle_no'       => Arr::get($payload, 'vehicle_no'),
                'temperature_status' => Arr::get($payload, 'temperature_status'),
                'notes'            => Arr::get($payload, 'notes'),
                'received_by'      => auth()->id(),
                'received_at'      => now(),
                'created_by'       => auth()->id(),
            ]);

            $grnItems = Arr::get($payload, 'items', []);
            if (empty($grnItems)) {
                throw new RuntimeException('At least one GRN item is required.');
            }

            $totalAmount = 0.0;
            $totalTaxAmount = 0.0;
            $taxableAmount = 0.0;

            foreach ($grnItems as $grnItem) {
                $purchaseItemId = (int) Arr::get($grnItem, 'purchase_item_id');
                $purchaseItem   = $bill->items->firstWhere('id', $purchaseItemId);
                if (! $purchaseItem) {
                    throw new RuntimeException('Invalid purchase item reference.');
                }

                $qtyReceived = (float) Arr::get($grnItem, 'quantity_received', 0);
                $qtyFree     = (float) Arr::get($grnItem, 'quantity_free', 0);
                $qtyRejected = (float) Arr::get($grnItem, 'quantity_rejected', 0);
                $qtyAccepted = max(0, $qtyReceived - $qtyRejected);

                if ($qtyReceived <= 0) {
                    continue;
                }

                // Ensure we don't over-receive beyond ordered qty
                $alreadyReceived = (float) $purchaseItem->quantity_received;
                $orderedTotal    = (float) $purchaseItem->total_quantity;
                $maxReceivable   = $orderedTotal - $alreadyReceived;
                if ($qtyReceived > $maxReceivable) {
                    throw new RuntimeException(
                        'Cannot receive more than remaining ordered qty for ' .
                        ($purchaseItem->medicine?->name ?? 'item #' . $purchaseItem->id) .
                        '. Remaining: ' . $maxReceivable
                    );
                }

                $batchNo       = (string) Arr::get($grnItem, 'batch_no', '');
                $expiryDate    = Arr::get($grnItem, 'expiry_date');
                if (is_string($expiryDate) && preg_match('/^\d{4}-\d{2}$/', $expiryDate)) {
                    $expiryDate = Carbon::parse($expiryDate . '-01')->endOfMonth()->format('Y-m-d');
                }
                $purchasePrice = (float) Arr::get($grnItem, 'unit_purchase_price', 0);
                $salePrice     = (float) Arr::get($grnItem, 'unit_sale_price', 0);
                $mrp           = (float) Arr::get($grnItem, 'unit_mrp', $salePrice);
                $taxPercent    = (float) Arr::get($grnItem, 'tax_percent', 0);

                $lineSubtotal = round($qtyAccepted * $purchasePrice, 2);
                $lineTax      = round(($lineSubtotal * $taxPercent) / 100, 2);
                $lineTotal    = round($lineSubtotal + $lineTax, 2);

                $grn->items()->create([
                    'purchase_item_id'    => $purchaseItem->id,
                    'medicine_id'         => $purchaseItem->medicine_id,
                    'batch_no'            => $batchNo,
                    'expiry_date'         => $expiryDate,
                    'quantity_ordered'    => $purchaseItem->total_quantity,
                    'quantity_received'   => $qtyReceived,
                    'quantity_free'       => $qtyFree,
                    'quantity_rejected'   => $qtyRejected,
                    'quantity_accepted'   => $qtyAccepted,
                    'rejection_reason'    => Arr::get($grnItem, 'rejection_reason'),
                    'unit_purchase_price' => $purchasePrice,
                    'unit_sale_price'     => $salePrice,
                    'unit_mrp'            => $mrp,
                    'tax_percent'         => $taxPercent,
                    'tax_amount'          => $lineTax,
                    'taxable_amount'      => $lineSubtotal,
                    'line_total'          => $lineTotal,
                ]);

                // Update PO item received qty
                $purchaseItem->update([
                    'quantity_received' => $alreadyReceived + $qtyReceived,
                ]);

                // Stock inward for accepted qty (+ free qty) only
                $stockQty = $qtyAccepted + $qtyFree;
                if ($stockQty > 0) {
                    $batch = PharmacyStockBatch::create([
                        'hospital_id'         => $hospitalId,
                        'medicine_id'         => $purchaseItem->medicine_id,
                        'purchase_item_id'    => $purchaseItem->id,
                        'batch_no'            => $batchNo,
                        'expiry_date'         => $expiryDate,
                        'unit_purchase_price' => $purchasePrice,
                        'unit_sale_price'     => $salePrice,
                        'unit_mrp'            => $mrp,
                        'available_qty'       => $stockQty,
                        'status'              => 'active',
                        'received_at'         => now(),
                    ]);

                    $this->createLedgerEntry([
                        'hospital_id'         => $hospitalId,
                        'medicine_id'         => $purchaseItem->medicine_id,
                        'stock_batch_id'      => $batch->id,
                        'reference_type'      => PharmacyGrn::class,
                        'reference_id'        => $grn->id,
                        'entry_type'          => 'in',
                        'quantity'            => $stockQty,
                        'balance_after'       => $batch->available_qty,
                        'unit_purchase_price' => $purchasePrice,
                        'unit_sale_price'     => $salePrice,
                        'remarks'             => 'GRN stock inward (' . $grn->grn_no . ')',
                    ]);
                }

                // Record rejected qty as damaged if any
                if ($qtyRejected > 0 && isset($batch)) {
                    $batch->update([
                        'damaged_qty' => $qtyRejected,
                    ]);
                }

                $totalAmount += $lineTotal;
                $totalTaxAmount += $lineTax;
                $taxableAmount += $lineSubtotal;
            }

            $grn->update(['total_amount' => round($totalAmount, 2),'total_tax' => round($totalTaxAmount, 2),'taxable_amount' => round($taxableAmount, 2)]);

            // Update PO fulfilment status
            $bill->refresh();
            $bill->load('items');
            $allReceived = $bill->items->every(fn ($item) =>
                (float) $item->quantity_received >= (float) $item->total_quantity
            );
            $anyReceived = $bill->items->contains(fn ($item) =>
                (float) $item->quantity_received > 0
            );

            if ($allReceived) {
                $bill->update([
                    'status'         => 'received',
                    'paid_amount'    => $bill->net_total,
                    'due_amount'     => 0,
                    'payment_status' => 'paid',
                ]);
            } elseif ($anyReceived) {
                $bill->update(['status' => 'partially_received']);
            }

            return $grn->fresh(['items.medicine', 'purchaseBill']);
        });
    }

    /**
     * Reject a pending PO.
     */
    public function rejectPurchaseBill(PharmacyPurchaseBill $bill, string $reason = ''): PharmacyPurchaseBill
    {
        if ($bill->status !== 'pending') {
            throw new RuntimeException('Only pending purchase orders can be rejected.');
        }

        $bill->update([
            'status'        => 'rejected',
            'approved_by'   => auth()->id(),
            'approved_at'   => now(),
            'reject_reason' => $reason,
        ]);

        return $bill->fresh();
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
