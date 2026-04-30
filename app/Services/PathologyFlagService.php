<?php

namespace App\Services;

class PathologyFlagService
{
    /**
     * Get appropriate min/max values based on patient gender
     */
    public static function getGenderSpecificRanges($paramDef, $patientGender = null): array
    {
        if (!$paramDef) {
            return ['min' => null, 'max' => null, 'crit_low' => null, 'crit_high' => null];
        }

        $applicableGender = $paramDef->applicable_gender ?? 'all';
        $normalizedPatientGender = strtolower((string) $patientGender);

        $maleRanges = [
            'min' => $paramDef->min_value_male ?? $paramDef->min_value,
            'max' => $paramDef->max_value_male ?? $paramDef->max_value,
            'crit_low' => $paramDef->critical_low_male ?? $paramDef->critical_low,
            'crit_high' => $paramDef->critical_high_male ?? $paramDef->critical_high,
        ];

        $femaleRanges = [
            'min' => $paramDef->min_value_female ?? $paramDef->min_value,
            'max' => $paramDef->max_value_female ?? $paramDef->max_value,
            'crit_low' => $paramDef->critical_low_female ?? $paramDef->critical_low,
            'crit_high' => $paramDef->critical_high_female ?? $paramDef->critical_high,
        ];

        $generalRanges = [
            'min' => $paramDef->min_value,
            'max' => $paramDef->max_value,
            'crit_low' => $paramDef->critical_low,
            'crit_high' => $paramDef->critical_high,
        ];

        // Parameter is specifically for male only.
        if ($applicableGender === 'male') {
            return $maleRanges;
        }

        // Parameter is specifically for female only.
        if ($applicableGender === 'female') {
            return $femaleRanges;
        }

        // For "all", use patient gender where available; fallback to general values.
        if ($normalizedPatientGender === 'male') {
            return $maleRanges;
        }

        if ($normalizedPatientGender === 'female') {
            return $femaleRanges;
        }

        return $generalRanges;
    }

    /**
     * Auto-generate flag based on parameter value and ranges
     * @param float $value
     * @param float|null $minValue
     * @param float|null $maxValue
     * @param float|null $criticalLow
     * @param float|null $criticalHigh
     * @return string|null
     */
    public static function generateFlag($value, $minValue = null, $maxValue = null, $criticalLow = null, $criticalHigh = null): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $val = (float) $value;

        // Check critical values first
        if ($criticalLow !== null && $val < (float) $criticalLow) {
            return 'critical_low';
        }

        if ($criticalHigh !== null && $val > (float) $criticalHigh) {
            return 'critical_high';
        }

        // Check normal range
        if ($minValue !== null && $maxValue !== null) {
            if ($val < (float) $minValue) {
                return 'low';
            }
            if ($val > (float) $maxValue) {
                return 'high';
            }
            return 'normal';
        }

        // If only min or max defined
        if ($minValue !== null && $val < (float) $minValue) {
            return 'low';
        }

        if ($maxValue !== null && $val > (float) $maxValue) {
            return 'high';
        }

        return 'normal';
    }

    /**
     * Get human-readable flag label with badge styling
     */
    public static function getFlagLabel(string $flag = null): array
    {
        $flags = [
            'normal' => ['label' => 'Normal', 'badge' => 'badge-green', 'icon' => '✓'],
            'low' => ['label' => '↓ Low', 'badge' => 'badge-orange', 'icon' => '↓'],
            'high' => ['label' => '↑ High', 'badge' => 'badge-orange', 'icon' => '↑'],
            'critical_low' => ['label' => '↓↓ Critical Low', 'badge' => 'badge-red', 'icon' => '⚠'],
            'critical_high' => ['label' => '↑↑ Critical High', 'badge' => 'badge-red', 'icon' => '⚠'],
        ];

        return $flags[$flag] ?? ['label' => '—', 'badge' => 'badge-gray', 'icon' => '—'];
    }

    /**
     * Check if flag indicates abnormal value
     */
    public static function isAbnormal(string $flag = null): bool
    {
        return in_array($flag, ['low', 'high', 'critical_low', 'critical_high']);
    }

    /**
     * Check if flag is critical
     */
    public static function isCritical(string $flag = null): bool
    {
        return in_array($flag, ['critical_low', 'critical_high']);
    }
}
