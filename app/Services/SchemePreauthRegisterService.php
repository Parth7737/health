<?php

namespace App\Services;

use App\Models\BedAllocation;
use App\Models\Patient;
use App\Models\PreauthRegister;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SchemePreauthRegisterService
{
    /**
     * Create or return the draft preauth row for an active scheme IPD admission.
     *
     * @param  array<string, mixed>  $context  scheme_kyc_type, scheme_is_newborn, born_baby_*, etc.
     */
    public function createOrGetDraftForAdmission(
        int $hospitalId,
        Patient $patient,
        BedAllocation $allocation,
        array $context = []
    ): PreauthRegister {
        return DB::transaction(function () use ($hospitalId, $patient, $allocation, $context) {
            $existing = PreauthRegister::query()
                ->where('hospital_id', $hospitalId)
                ->where('bed_allocation_id', $allocation->id)
                ->where('status', PreauthRegister::STATUS_REGISTER)
                ->first();

            if ($existing) {
                return $this->syncDraftFromContext($existing, $patient, $allocation, $context);
            }

            $row = new PreauthRegister;
            $row->hospital_id = $hospitalId;
            $row->register_id = null;
            $row->sha_preauth_register_id = null;
            $row->patient_id = $patient->id;
            $row->bed_allocation_id = $allocation->id;
            $row->scheme_id = (int) ($allocation->scheme_type_id ?? $context['scheme_type_id'] ?? 0) ?: null;
            $row->status = PreauthRegister::STATUS_REGISTER;

            $this->fillPatientAndSchemeFields($row, $patient, $allocation, $context);
            $row->save();

            return $row;
        });
    }

    /**
     * Build context array from patient registration / IPD admit request.
     */
    public function contextFromRequest(Request $request): array
    {
        $isNewborn = $request->boolean('scheme_is_newborn');

        $context = [
            'scheme_type_id' => $request->input('scheme_type_id'),
            'kyc_type' => $request->input('scheme_kyc_type'),
            'is_new_born_baby' => $isNewborn ? 1 : 0,
            'born_baby_dob' => $request->input('scheme_born_baby_dob') ?: $request->input('date_of_birth'),
            'born_baby_name' => $request->input('scheme_born_baby_name') ?: $request->input('name'),
            'born_baby_gender' => $request->input('scheme_born_baby_gender') ?: $request->input('gender'),
        ];

        if ($request->hasFile('scheme_born_baby_birth_certificate')) {
            $context['born_baby_birth_certificate'] = $request->file('scheme_born_baby_birth_certificate')
                ->store('authentication', 'public');
        }

        return $context;
    }

    protected function syncDraftFromContext(
        PreauthRegister $row,
        Patient $patient,
        BedAllocation $allocation,
        array $context
    ): PreauthRegister {
        $this->fillPatientAndSchemeFields($row, $patient, $allocation, $context);
        $row->save();

        return $row;
    }

    protected function fillPatientAndSchemeFields(
        PreauthRegister $row,
        Patient $patient,
        BedAllocation $allocation,
        array $context
    ): void {
        $row->mobile_no = $patient->phone ?? null;
        $row->full_name = $patient->full_name;
        $row->address = $patient->address;
        $row->district_name = $this->meaningfulString($patient->district);
        $row->state_name = $this->meaningfulString($patient->state);
        $row->pincode = filled($patient->pin_code) ? (string) $patient->pin_code : null;
        $row->aadhar_no = $patient->aadhar_no;

        if (filled($context['kyc_type'] ?? null)) {
            $row->kyc_type = (string) $context['kyc_type'];
        }

        $isNewborn = (int) ($context['is_new_born_baby'] ?? 0);
        $row->is_new_born_baby = $isNewborn;

        if ($isNewborn === 1) {
            $row->born_baby_dob = $context['born_baby_dob'] ?? null;
            $row->born_baby_name = $context['born_baby_name'] ?? null;
            $row->born_baby_gender = $context['born_baby_gender'] ?? null;
            if (! empty($context['born_baby_birth_certificate'])) {
                $row->born_baby_birth_certificate = $context['born_baby_birth_certificate'];
            }
        } else {
            $row->born_baby_dob = null;
            $row->born_baby_name = null;
            $row->born_baby_gender = null;
            $row->born_baby_birth_certificate = null;
        }
    }

    protected function meaningfulString(?string $value): ?string
    {
        $value = trim((string) $value);
        if ($value === '' || in_array($value, ['Select District', 'Select State'], true)) {
            return null;
        }

        return $value;
    }
}
