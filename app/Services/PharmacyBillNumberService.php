<?php

namespace App\Services;

use App\Models\PharmacyPurchaseBill;
use App\Models\PharmacySaleBill;

class PharmacyBillNumberService
{
    public function nextPurchaseBillNo(int $hospitalId, ?\DateTimeInterface $date = null): string
    {
        $date = $date ?: now();
        $prefix = 'PB-' . $date->format('Ym') . '-';

        $latest = PharmacyPurchaseBill::withoutGlobalScopes()
            ->where('hospital_id', $hospitalId)
            ->where('bill_no', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->lockForUpdate()
            ->first();

        $next = 1;
        if ($latest && str_starts_with($latest->bill_no, $prefix)) {
            $lastSeq = (int) substr($latest->bill_no, strlen($prefix));
            $next = $lastSeq + 1;
        }

        return $prefix . str_pad((string) $next, 6, '0', STR_PAD_LEFT);
    }

    public function nextSaleBillNo(int $hospitalId, ?\DateTimeInterface $date = null): string
    {
        $date = $date ?: now();
        $prefix = 'SB-' . $date->format('Ym') . '-';

        $latest = PharmacySaleBill::withoutGlobalScopes()
            ->where('hospital_id', $hospitalId)
            ->where('bill_no', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->lockForUpdate()
            ->first();

        $next = 1;
        if ($latest && str_starts_with($latest->bill_no, $prefix)) {
            $lastSeq = (int) substr($latest->bill_no, strlen($prefix));
            $next = $lastSeq + 1;
        }

        return $prefix . str_pad((string) $next, 6, '0', STR_PAD_LEFT);
    }
}
