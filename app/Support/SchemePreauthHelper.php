<?php

namespace App\Support;

use App\Models\PreauthInvestigation;
use App\Models\PreauthProcedure;
use App\Models\PreauthRegister;
use App\Models\TreatmentPlanProcedure;
use Illuminate\Support\Collection;

class SchemePreauthHelper
{
    public static function getInvestigations(int $preauth_register_id, int $is_resubmission = 0, int $is_required = 0): Collection
    {
        $procedures = $is_resubmission
            ? PreauthProcedure::query()->where('preauth_register_id', $preauth_register_id)->where('is_resubmission_delete', 0)->get()
            : PreauthProcedure::query()->where('preauth_register_id', $preauth_register_id)->get();

        if ($procedures->isEmpty()) {
            return collect();
        }

        $procedure_ids = $procedures->pluck('procedure_id')->filter()->unique();
        $pre_docs_ids = TreatmentPlanProcedure::query()
            ->whereIn('id', $procedure_ids)
            ->pluck('mandatory_documents_pre_auth')
            ->filter()
            ->flatMap(function ($item) {
                return explode(',', (string) $item);
            })
            ->map(fn ($v) => (int) trim($v))
            ->filter(fn ($v) => $v > 0)
            ->unique()
            ->values()
            ->all();

        if (empty($pre_docs_ids)) {
            return collect();
        }

        $q = \App\Models\TreatmentPlanInvestigation::query()->whereIn('id', $pre_docs_ids);
        if ($is_required === 1) {
            $q->where('is_required', 1);
        }

        return $q->get();
    }

    public static function getPreauthInvestigationsStatus(int $preauth_register_id, int $is_resubmission = 0): bool
    {
        $investigations = self::getInvestigations($preauth_register_id, $is_resubmission, 1);
        if (! PreauthRegister::query()->whereKey($preauth_register_id)->exists()) {
            return false;
        }

        $q = PreauthInvestigation::query()
            ->where('preauth_register_id', $preauth_register_id)
            ->whereHas('investigation', function ($query) {
                $query->where('is_required', 1);
            });
        if ($is_resubmission) {
            $q->where('is_resubmission_delete', 0);
        }
        $preauth_investigations_count = $q->count();

        return $preauth_investigations_count === count($investigations);
    }

    public static function getPreauthPackageStatus(int $preauth_register_id, int $is_resubmission = 0): int
    {
        $q = PreauthProcedure::query()
            ->where('preauth_register_id', $preauth_register_id)
            ->whereHas('procedure', fn ($query) => $query->whereRaw('LOWER(TRIM(procedure_label)) NOT LIKE ?', ['%add-onprocedure%']));
        if ($is_resubmission) {
            $q->where('is_resubmission_delete', 0);
        }
        $procedures = $q->get();
        $package_type = '';
        $mismatch = 0;
        foreach ($procedures as $procedure) {
            $mos = (string) ($procedure->procedure->medical_or_surgical ?? '');
            if ($package_type === '' && $mos !== '') {
                $package_type = $mos;
            }
            if ($package_type !== '' && $mos !== '' && $package_type !== $mos) {
                $mismatch = 1;
            }
        }

        return $mismatch;
    }

    public static function getU100PackageStatus(int $preauth_register_id, int $is_resubmission = 0): int
    {
        $base = PreauthProcedure::query()
            ->where('preauth_register_id', $preauth_register_id)
            ->whereHas('procedure', fn ($query) => $query->whereRaw('LOWER(TRIM(procedure_label)) NOT LIKE ?', ['%add-onprocedure%']));
        if ($is_resubmission) {
            $base->where('is_resubmission_delete', 0);
        }
        $procedure_count = (clone $base)->count();
        $u100 = (clone $base)->whereHas('procedure', fn ($query) => $query->where('procedure_code_1', 'U100'))->count();
        if ($u100 !== 0 && $procedure_count !== $u100) {
            return 1;
        }

        return 0;
    }

    public static function getDeductionAmount(int $preauth_register_id): float
    {
        return (float) (PreauthProcedure::query()
            ->where('preauth_register_id', $preauth_register_id)
            ->where('preauth_claim_status', 'Approved')
            ->sum('deducted_amount') ?? 0);
    }

    public static function getPreauthIntiateAmount(int $preauth_register_id, int $is_applicable_discharge = 1): float
    {
        $result = PreauthProcedure::query()
            ->selectRaw('
                SUM(
                    COALESCE(procedure_price, 0) +
                    COALESCE(stratification_price, 0) +
                    (CASE
                        WHEN COALESCE(procedure_price, 0) = 0
                            AND COALESCE(stratification_price, 0) != 0
                            AND COALESCE(no_of_days, 0) > 1
                        THEN COALESCE(stratification_price, 0) * (COALESCE(no_of_days, 0) - 1)
                        ELSE 0
                    END) +
                    (COALESCE(implant_price, 0) * COALESCE(implant_qty, 0)) +
                    COALESCE(incentive, 0)
                ) as total
            ')
            ->where('preauth_register_id', $preauth_register_id)
            ->first();

        $total = (float) ($result->total ?? 0);

        if ($is_applicable_discharge === 1) {
            $deduction = (float) (PreauthRegister::query()
                ->whereKey($preauth_register_id)
                ->value('deduction_discharge_amount') ?? 0);
            $total -= $deduction;
        }

        return $total;
    }

    public static function getPreauthAmountWithoutDeduction(int $preauth_register_id, int $is_applicable_discharge = 1, int $amount_action_type = 0): float
    {
        $implantSql = $amount_action_type === 0
            ? '(CASE WHEN preauth_implant_status = "Approved" THEN COALESCE(implant_price, 0) * COALESCE(implant_qty, 0) ELSE 0 END)'
            : '(CASE WHEN preauth_claim_implant_status = "Approved" THEN COALESCE(implant_price, 0) * COALESCE(implant_qty, 0) ELSE 0 END)';

        $q = PreauthProcedure::query()
            ->where('preauth_register_id', $preauth_register_id);

        if ($amount_action_type === 0) {
            $q->where(function ($query) {
                $query->whereIn('preauth_status', ['Approved', 'Forwarded To Medical Committee'])
                    ->orWhere('preauth_implant_status', 'Approved');
            });
        } else {
            $q->where(function ($query) {
                $query->where('preauth_claim_status', 'Approved')
                    ->orWhere('preauth_claim_implant_status', 'Approved');
            });
        }

        $result = $q->selectRaw("
                SUM(
                    COALESCE(procedure_price, 0) +
                    COALESCE(stratification_price, 0) +
                    (CASE
                        WHEN COALESCE(procedure_price, 0) = 0
                            AND COALESCE(stratification_price, 0) != 0
                            AND COALESCE(no_of_days, 0) > 1
                        THEN COALESCE(stratification_price, 0) * (COALESCE(no_of_days, 0) - 1)
                        ELSE 0
                    END) +
                    {$implantSql} +
                    COALESCE(incentive, 0)
                ) as total
            ")
            ->first();

        $total = (float) ($result->total ?? 0);

        if ($is_applicable_discharge === 1) {
            $deduction = (float) (PreauthRegister::query()
                ->whereKey($preauth_register_id)
                ->value('deduction_discharge_amount') ?? 0);
            $total -= $deduction;
        }

        return $total;
    }

    public static function fillCompleteStep(int $preauth_register_id): array
    {
        $preauth_register = PreauthRegister::query()->find($preauth_register_id);
        $response = ['medical' => false, 'admission' => false, 'treatment' => false];
        if (! $preauth_register) {
            return $response;
        }

        $general_info = \App\Models\GeneralInfo::query()->where('preauth_register_id', $preauth_register->id)->first();
        $family_history = \App\Models\FamilyHistory::query()->where('preauth_register_id', $preauth_register->id)->first();
        $personal_history = \App\Models\PersonalHistory::query()->where('preauth_register_id', $preauth_register->id)->first();
        $authentication_consent = \App\Models\AuthenticationConsent::query()->where('preauth_register_id', $preauth_register->id)->first();
        $admission_details = \App\Models\AdmissionDetails::query()->where('preauth_register_id', $preauth_register->id)->first();
        $preauth_diagnosis = \App\Models\PreauthDiagnosis::query()->where('preauth_register_id', $preauth_register->id)->get();
        $procedures = PreauthProcedure::query()->where('preauth_register_id', $preauth_register->id)->get();
        $preauth_investigation_status = self::getPreauthInvestigationsStatus($preauth_register->id);
        $preauth_teams = \App\Models\PreauthCareTeam::query()->where('preauth_register_id', $preauth_register->id)->get();

        if ($general_info && $family_history && $personal_history) {
            $response['medical'] = true;
        }
        if ($authentication_consent && $admission_details) {
            $response['admission'] = true;
        }
        if ($preauth_diagnosis->count() > 0 && $procedures->count() > 0 && $preauth_investigation_status && $preauth_teams->count() > 0) {
            $response['treatment'] = true;
        }

        return $response;
    }

    public static function checkandUpdateSurgicalPackage(int $preauth_register_id): void
    {
        $preauth_register = PreauthRegister::query()->find($preauth_register_id);
        if (! $preauth_register || (int) $preauth_register->scheme_id === 1) {
            return;
        }

        $surgical_procedures = PreauthProcedure::query()
            ->where('preauth_register_id', $preauth_register_id)
            ->whereHas('procedure', function ($query) {
                $query->where('medical_or_surgical', 'Surgical')
                    ->whereRaw('LOWER(TRIM(procedure_label)) NOT LIKE ?', ['%add-onprocedure%']);
            })
            ->with(['procedure'])
            ->get()
            ->sortByDesc(fn ($item) => $item->procedure->price ?? 0)
            ->values();

        foreach ($surgical_procedures as $key => $surgical_procedure) {
            $procedure_price = (float) ($surgical_procedure->procedure->price ?? 0);
            if ($key === 0) {
                $surgical_procedure->adj_per = 100;
                $surgical_procedure->procedure_price = $procedure_price;
            } elseif ($key === 1) {
                if ($procedure_price) {
                    $surgical_procedure->adj_per = 50;
                    $surgical_procedure->procedure_price = $procedure_price / 2;
                }
            } else {
                if ($procedure_price) {
                    $surgical_procedure->adj_per = 25;
                    $surgical_procedure->procedure_price = $procedure_price * 0.25;
                }
            }
            if ((int) $surgical_procedure->incentive_per !== 0) {
                $surgical_procedure->incentive = ((float) $surgical_procedure->incentive_per * (float) $surgical_procedure->procedure_price) / 100;
            }
            $surgical_procedure->save();
        }
    }
}
