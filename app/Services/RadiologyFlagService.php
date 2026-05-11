<?php

namespace App\Services;

class RadiologyFlagService
{
    /**
     * Generate flag using radiology parameter configuration (numeric, ordinal, boolean).
     */
    public static function generateFlagByParameter($value, $paramDef, $patientGender = null): ?string
    {
        if ($value === null || $value === '' || !$paramDef) {
            return null;
        }

        $valueType = strtolower((string) ($paramDef->value_type ?? 'numeric'));
        if (!in_array($valueType, ['numeric', 'ordinal', 'boolean'], true)) {
            $valueType = 'numeric';
        }

        if ($valueType === 'numeric') {
            if (!is_numeric($value)) {
                return null;
            }

            $ranges = PathologyFlagService::getGenderSpecificRanges($paramDef, $patientGender);

            return PathologyFlagService::generateFlag(
                $value,
                $ranges['min'] ?? null,
                $ranges['max'] ?? null,
                $ranges['crit_low'] ?? null,
                $ranges['crit_high'] ?? null
            );
        }

        $rules = self::normalizeFlagRules($paramDef->flag_rules ?? null);
        $needle = self::normalizeTextValue($value);
        if ($needle === '') {
            return null;
        }

        if ($valueType === 'boolean') {
            if (in_array($needle, $rules['normal'] ?? [], true)) {
                return 'normal';
            }

            if (in_array($needle, $rules['abnormal'] ?? [], true)) {
                // Keep compatibility with existing abnormal checks in worklist/report flows.
                return 'high';
            }

            return null;
        }

        $priority = [
            'critical_low',
            'critical_high',
            'low',
            'high',
            'normal',
        ];

        foreach ($priority as $flag) {
            if (in_array($needle, $rules[$flag] ?? [], true)) {
                return $flag;
            }
        }

        return null;
    }

    protected static function normalizeFlagRules($rules): array
    {
        $parsed = [];

        if (is_string($rules)) {
            $decoded = json_decode($rules, true);
            $parsed = is_array($decoded) ? $decoded : [];
        } elseif (is_array($rules)) {
            $parsed = $rules;
        }

        $normalized = [
            'normal' => [],
            'abnormal' => [],
            'low' => [],
            'high' => [],
            'critical_low' => [],
            'critical_high' => [],
        ];

        foreach ($normalized as $flag => $items) {
            $rawValues = $parsed[$flag] ?? [];
            if (!is_array($rawValues)) {
                $rawValues = [];
            }

            $normalized[$flag] = array_values(array_unique(array_filter(array_map(function ($v) {
                return self::normalizeTextValue($v);
            }, $rawValues), function ($v) {
                return $v !== '';
            })));
        }

        return $normalized;
    }

    protected static function normalizeTextValue($value): string
    {
        return strtolower(trim((string) $value));
    }
}
