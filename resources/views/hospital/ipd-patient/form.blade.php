@php
    $admissionDate = now()->format('d-m-Y H:i');
    $patient = $patient ?? null;
    $sourceOpdPatient = $sourceOpdPatient ?? null;
    $selectedBed = old('bed_id');
@endphp

<div class="modal-header">
    <h5 class="modal-title">IPD Admission</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<style>
    .add-datamodal .modal-dialog {
        max-width: min(1140px, calc(100vw - 1rem));
        margin: 0.5rem auto;
        height: calc(100vh - 1rem);
    }

    .add-datamodal .modal-content {
        height: 100%;
        max-height: 100%;
        display: flex;
        flex-direction: column;
    }

    .add-datamodal #save-ipd-admission {
        flex: 1 1 auto;
        min-height: 0;
        display: flex;
        flex-direction: column;
    }

    .add-datamodal .modal-body {
        flex: 1 1 auto;
        min-height: 0;
        max-height: 100%;
        overflow-y: auto;
        overflow-x: hidden;
        padding-bottom: 1rem;
    }

    .add-datamodal .modal-footer {
        flex-shrink: 0;
        background: #fff;
        border-top: 1px solid #dee2e6;
    }

    @media (max-width: 767.98px) {
        .add-datamodal .modal-dialog {
            max-width: calc(100vw - 0.5rem);
            margin: 0.25rem auto;
            height: calc(100vh - 0.5rem);
        }

        .add-datamodal .modal-body {
            padding: 0.75rem;
        }

        .add-datamodal .modal-footer {
            padding: 0.65rem 0.75rem;
        }
    }

    .ipd-form-card {
        border: 1px solid #e9eef7;
        border-radius: 12px;
        background: #fff;
        padding: 16px;
    }

    .ipd-form-card .card-title {
        font-size: 15px;
        font-weight: 700;
        margin-bottom: 12px;
        color: #1f2f59;
    }

    .ipd-bed-hint {
        border: 1px dashed #c8d6f0;
        border-radius: 10px;
        background: #f8fbff;
        padding: 12px;
        font-size: 13px;
    }

    .select2-container {
        z-index: 1050 !important;
    }

    .select2-dropdown {
        z-index: 1051 !important;
    }
</style>

<form method="POST" id="save-ipd-admission" data-submit-url="{{ route('hospital.ipd-patient.store') }}">
    <div class="modal-body">
        <input type="hidden" name="selected_patient_id" value="{{ $patient?->id ?? '' }}">
        <input type="hidden" name="opd_patient_id" value="{{ $sourceOpdPatient?->id ?? '' }}">
        <input type="hidden" id="prefill_department_id" value="{{ $sourceOpdPatient?->hr_department_id ?? '' }}">
        <input type="hidden" id="prefill_doctor_id" value="{{ $sourceOpdPatient?->doctor_id ?? '' }}">
        <input type="hidden" id="prefill_tpa_id" value="{{ $sourceOpdPatient?->tpa_id ?? '' }}">
        <datalist id="ipd-patient-suggestions"></datalist>

        <div class="row g-3">
            <div class="col-12">
                <div class="ipd-form-card">
                    <div class="card-title">Patient Identity</div>
                    <div class="row g-3">
                        <div class="col-md-2">
                            <label class="form-label">Search By</label>
                            <div class="d-flex gap-3 pt-1">
                                <div class="form-check mb-0">
                                    <input class="form-check-input" type="radio" name="searchBy" value="phone" checked>
                                    <label class="form-check-label">Phone</label>
                                </div>
                                <div class="form-check mb-0">
                                    <input class="form-check-input" type="radio" name="searchBy" value="health_id">
                                    <label class="form-check-label">Health ID</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Country Code <span class="text-danger">*</span></label>
                            <select class="form-select select2-modal" name="country_code">
                                <option value="">Select</option>
                                @php $countryCodes = App\Models\CountryCode::pluck('country_code')->toArray(); @endphp
                                @foreach($countryCodes as $code)
                                    <option value="{{ $code }}" {{ (($patient?->country_code ?? '+91') === $code) ? 'selected' : '' }}>{{ $code }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Phone / Health ID <span class="text-danger">*</span></label>
                            <input type="text" id="ipd-patient-search" name="phone" class="form-control" list="ipd-patient-suggestions" autocomplete="off" value="{{ $patient?->phone ?? '' }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Patient Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ $patient?->name ?? '' }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Guardian Name</label>
                            <input type="text" name="guardian_name" class="form-control" value="{{ $patient?->guardian_name ?? '' }}">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Gender <span class="text-danger">*</span></label>
                            <select class="form-select select2-modal" name="gender">
                                <option value="">Select</option>
                                <option value="Male" @selected(($patient?->gender ?? '') === 'Male')>Male</option>
                                <option value="Female" @selected(($patient?->gender ?? '') === 'Female')>Female</option>
                                <option value="Other" @selected(($patient?->gender ?? '') === 'Other')>Other</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Age <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="age_years" class="form-control" placeholder="Year" value="{{ $patient?->age_years ?? '' }}">
                                <input type="number" name="age_months" class="form-control" placeholder="Month" value="{{ $patient?->age_months ?? '' }}">
                            </div>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">DOB</label>
                            <input type="text" id="ipd-dob" name="date_of_birth" class="form-control" value="{{ optional($patient?->date_of_birth)->format('d-m-Y') }}">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Blood Group</label>
                            <select class="form-select select2-modal" name="blood_group">
                                <option value="">Select</option>
                                @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bloodGroup)
                                    <option value="{{ $bloodGroup }}" @selected(($patient?->blood_group ?? '') === $bloodGroup)>{{ $bloodGroup }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Marital Status</label>
                            <select class="form-select select2-modal" name="marital_status">
                                <option value="">Select</option>
                                @foreach(['Single','Married','Divorced','Not Specified'] as $status)
                                    <option value="{{ $status }}" @selected(($patient?->marital_status ?? '') === $status)>{{ $status }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ $patient?->email ?? '' }}">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Patient Category</label>
                            <select class="form-select select2-modal" name="patient_category_id">
                                <option value="">Select</option>
                                @foreach(App\Models\PatientCategory::orderBy('name')->get(['id', 'name']) as $category)
                                    <option value="{{ $category->id }}" @selected(($patient?->patient_category_id ?? null) == $category->id)>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Nationality</label>
                            <select class="form-select select2-modal" name="nationality_id">
                                <option value="">Select</option>
                                @foreach(App\Models\Nationality::orderBy('name')->get(['id', 'name']) as $nationality)
                                    <option value="{{ $nationality->id }}" @selected(($patient?->nationality_id ?? 82) == $nationality->id)>{{ $nationality->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Religion</label>
                            <select class="form-select select2-modal" name="religion_id">
                                <option value="">Select</option>
                                @foreach(App\Models\Religion::orderBy('name')->get(['id', 'name']) as $religion)
                                    <option value="{{ $religion->id }}" @selected(($patient?->religion_id ?? null) == $religion->id)>{{ $religion->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Address</label>
                            <textarea name="address" class="form-control" rows="2">{{ $patient?->address ?? '' }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="ipd-form-card h-100">
                    <div class="card-title">Admission Details</div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Admission Date <span class="text-danger">*</span></label>
                            <input type="text" id="ipd-admission-date" name="admission_date" class="form-control" value="{{ $admissionDate }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Expected Discharge</label>
                            <input type="text" id="ipd-expected-discharge-date" name="expected_discharge_date" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Admission Type <span class="text-danger">*</span></label>
                            <select class="form-select select2-modal" name="admission_type">
                                <option value="emergency" @selected(($sourceOpdPatient ? 'planned' : 'emergency') === 'emergency')>Emergency</option>
                                <option value="planned" @selected(($sourceOpdPatient ? 'planned' : '') === 'planned')>Planned</option>
                                <option value="observation">Observation</option>
                                <option value="icu">ICU</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Department <span class="text-danger">*</span></label>
                            <select class="form-select select2-modal" name="hr_department_id">
                                <option value="">Select</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}" @selected(($sourceOpdPatient?->hr_department_id ?? null) == $department->id)>{{ $department->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Consultant Doctor <span class="text-danger">*</span></label>
                            <select class="form-select select2-modal" name="doctor_id">
                                <option value="">Select</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Bed <span class="text-danger">*</span></label>
                            <select class="form-select select2-modal" name="bed_id">
                                <option value="">Select</option>
                                @foreach($availableBeds as $bed)
                                    <option
                                        value="{{ $bed->id }}"
                                        data-ward="{{ $bed->room?->ward?->ward_name ?? '-' }}"
                                        data-room="{{ $bed->room?->room_number ?? '-' }}"
                                        data-type="{{ $bed->bedType?->type_name ?? '-' }}"
                                        data-charge="{{ number_format((float) ($bed->bedType?->base_charge ?? 0), 2, '.', '') }}"
                                        @selected((string) $selectedBed === (string) $bed->id)
                                    >
                                        {{ $bed->bed_code }} | {{ $bed->room?->ward?->ward_name ?? '-' }} / {{ $bed->room?->room_number ?? '-' }} | {{ $bed->bedType?->type_name ?? '-' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">TPA</label>
                            <select class="form-select select2-modal" name="tpa_id">
                                <option value="">Self</option>
                                @foreach($tpas as $tpa)
                                    <option value="{{ $tpa->id }}" @selected(($sourceOpdPatient?->tpa_id ?? null) == $tpa->id)>{{ $tpa->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">TPA Ref. No.</label>
                            <input type="text" name="tpa_reference_no" class="form-control" value="{{ $sourceOpdPatient?->tpa_reference_no ?? '' }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Initial Payment</label>
                            <input type="number" name="initial_payment_amount" class="form-control" min="0" step="0.01" placeholder="0.00">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Payment Mode</label>
                            <select class="form-select select2-modal" name="payment_mode">
                                <option value="">Select</option>
                                <option value="Cash">Cash</option>
                                <option value="Card">Card</option>
                                <option value="Online">Online</option>
                            </select>
                        </div>

                        <div class="col-md-8">
                            <label class="form-label">Admission Reason <span class="text-danger">*</span></label>
                            <textarea name="admission_reason" class="form-control" rows="2">{{ $sourceOpdPatient?->symptoms_description ?? '' }}</textarea>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Provisional Diagnosis</label>
                            <textarea name="provisional_diagnosis" class="form-control" rows="2"></textarea>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Admission Notes</label>
                            <textarea name="admission_notes" class="form-control" rows="2">{{ $sourceOpdPatient ? 'Moved from OPD consultation.' : '' }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="ipd-form-card h-100">
                    <div class="card-title">Clinical Snapshot</div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Height</label>
                            <input type="text" name="height" class="form-control" value="{{ $sourceOpdPatient?->height ?? '' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Weight</label>
                            <input type="text" name="weight" class="form-control" value="{{ $sourceOpdPatient?->weight ?? '' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">BP</label>
                            <input type="text" name="bp" class="form-control" value="{{ $sourceOpdPatient?->bp ?? '' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Pulse</label>
                            <input type="text" name="pulse" class="form-control" value="{{ $sourceOpdPatient?->pluse ?? '' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Temperature</label>
                            <input type="text" name="temperature" class="form-control" value="{{ $sourceOpdPatient?->temperature ?? '' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Respiration</label>
                            <input type="text" name="respiration" class="form-control" value="{{ $sourceOpdPatient?->respiration ?? '' }}">
                        </div>
                        <div class="col-12">
                            <div class="ipd-bed-hint" id="ipd-bed-summary">
                                Select a bed to see its location, type and standard base charge.
                            </div>
                        </div>
                        @if($sourceOpdPatient)
                            <div class="col-12">
                                <div class="alert alert-light border mb-0">
                                    <strong>Source:</strong> OPD visit {{ $sourceOpdPatient->case_no }} on {{ optional($sourceOpdPatient->appointment_date)->format('d-m-Y H:i') }}.
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Admit Patient</button>
    </div>
</form>