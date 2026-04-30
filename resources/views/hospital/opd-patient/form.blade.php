<div class="modal-header">
    <h5 class="modal-title" id="view_modal_dataModelLabel">{{ !$id ? 'Quick OPD Registration' : 'Edit OPD Visit' }}</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

@php
    $revisitPatient = $data?->patient;
    $revisitSymptomsType = $data?->symptoms_type_id;
    $revisitDiseaseType = $revisitPatient?->disease_type_id;
    $revisitDisease = $revisitPatient?->disease_id;

    if (is_null($revisitSymptomsType)) {
        $revisitSymptomsType = [];
    } elseif (!is_array($revisitSymptomsType)) {
        $revisitSymptomsType = [$revisitSymptomsType];
    }

    $revisitSymptomsType = array_values(array_filter($revisitSymptomsType, fn ($value) => $value !== null && $value !== ''));

    if (is_null($revisitDiseaseType)) {
        $revisitDiseaseType = [];
    } elseif (!is_array($revisitDiseaseType)) {
        $revisitDiseaseType = [$revisitDiseaseType];
    }
    $revisitDiseaseType = array_values(array_filter($revisitDiseaseType, fn ($value) => $value !== null && $value !== ''));

    if (is_null($revisitDisease)) {
        $revisitDisease = [];
    } elseif (!is_array($revisitDisease)) {
        $revisitDisease = [$revisitDisease];
    }
    $revisitDisease = array_values(array_filter($revisitDisease, fn ($value) => $value !== null && $value !== ''));

    $todayDateTime = date('d-m-Y H:i');
@endphp

<style>
    .opd-form-card {
        border: 1px solid #e9eef7;
        border-radius: 12px;
        background: #fff;
        padding: 16px;
    }

    .opd-form-card .card-title {
        font-size: 15px;
        font-weight: 700;
        margin-bottom: 12px;
        color: #1f2f59;
    }

    .opd-kpi-chip {
        background: #eef6ff;
        border: 1px solid #cfe2ff;
        border-radius: 10px;
        padding: 8px 10px;
        font-size: 12px;
        color: #1f2f59;
    }

    .opd-advanced-toggle {
        font-weight: 600;
        color: #1f2f59;
    }
</style>

<div class="modal-body">
    <form method="POST" id="savedata" enctype="multipart/form-data">
        <input type="hidden" id="id" name="id" value="{{ $id ?? '' }}">
        <input type="hidden" id="selected_patient_id" name="selected_patient_id" value="{{ $revisitPatient?->id ?? '' }}">
        <input type="hidden" id="revisit_tpa_id" value="{{ $data?->tpa_id ?? '' }}">
        <input type="hidden" id="revisit_doctor_id" value="{{ $data?->doctor_id ?? '' }}">
        <input type="hidden" id="revisit_slot" value="{{ $data?->slot ?? '' }}">
        <input type="hidden" id="revisit_symptoms" value='@json($data?->symptoms ?? [])'>
        <input type="hidden" id="revisit_symptoms_type" value='@json($revisitSymptomsType)'>
        <input type="hidden" id="revisit_disease_type" value='@json($revisitDiseaseType)'>
        <input type="hidden" id="revisit_disease" value='@json($revisitDisease)'>
        <input type="hidden" id="print_mode" name="print_mode" value="none">
        <datalist id="patient-phone-suggestions"></datalist>
        @if($revisitPatient)
            <input type="hidden" id="is_revisit_prefilled" value="1">
        @endif

        <!-- <div class="row g-3 mb-3">
            <div class="col-lg-3 col-sm-6">
                <div class="opd-kpi-chip">
                    <strong>Step 1:</strong> Basic Patient Details
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="opd-kpi-chip">
                    <strong>Step 2:</strong> Department + Doctor
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="opd-kpi-chip">
                    <strong>Step 3:</strong> Charge + Payment
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="opd-kpi-chip">
                    <strong>Step 4:</strong> Save and Print Slip
                </div>
            </div>
        </div> -->

        <div class="row g-3">
            <div class="col-12">
                <div class="opd-form-card">
                    <div class="card-title">Required Registration Details</div>
                    <div class="row g-3">
                        <div class="col-md-2">
                            <label class="form-label">Search By</label>
                            <div class="d-flex gap-3 pt-1">
                                <div class="form-check mb-0">
                                    <input class="form-check-input" type="radio" name="searchBy" id="searchByPhone" value="phone" checked>
                                    <label class="form-check-label" for="searchByPhone">Phone</label>
                                </div>
                                <div class="form-check mb-0">
                                    <input class="form-check-input" type="radio" name="searchBy" id="searchByHealthId" value="health_id">
                                    <label class="form-check-label" for="searchByHealthId">Health ID</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Country Code <span class="text-danger">*</span></label>
                            <select class="form-select select2-modal" name="country_code">
                                <option value="">Select</option>
                                @php $countryCodes = App\Models\CountryCode::pluck('country_code')->toArray(); @endphp
                                @foreach($countryCodes as $code)
                                    <option value="{{ $code }}" {{ ($revisitPatient?->country_code ? $revisitPatient->country_code === $code : $code === '+91') ? 'selected' : '' }}>{{ $code }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label" id="patient-search-input-label">Phone / Health ID <span class="text-danger">*</span></label>
                            <input type="text" id="phone" name="phone" class="form-control" list="patient-phone-suggestions" autocomplete="off" placeholder="Enter phone" value="{{ $revisitPatient?->phone ?? '' }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Patient Name <span class="text-danger">*</span></label>
                            <input type="text" id="name" name="name" class="form-control" value="{{ $revisitPatient?->name ?? '' }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Guardian Name</label>
                            <input type="text" name="guardian_name" class="form-control" value="{{ $revisitPatient?->guardian_name ?? '' }}">
                        </div>


                        <div class="col-md-2">
                            <label class="form-label">Gender <span class="text-danger">*</span></label>
                            <select class="form-select select2-modal" name="gender" required>
                                <option value="">Select</option>
                                <option value="Male" {{ ($revisitPatient?->gender ?? '') === 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ ($revisitPatient?->gender ?? '') === 'Female' ? 'selected' : '' }}>Female</option>
                                <option value="Other" {{ ($revisitPatient?->gender ?? '') === 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Age <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="age_years" class="form-control" placeholder="Year" value="{{ $revisitPatient?->age_years ?? '' }}" required>
                                <input type="number" name="age_months" class="form-control" placeholder="Month" value="{{ $revisitPatient?->age_months ?? '' }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Appointment Date <span class="text-danger">*</span></label>
                            <input type="text" name="appointment_date" class="form-control" id="opd-appointment-date" value="{{ $todayDateTime }}" readonly>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Token</label>
                            <input type="text" name="token_no" class="form-control" readonly placeholder="Auto Generated" value="{{ $data?->token_no ? str_pad($data->token_no, 3, '0', STR_PAD_LEFT) : '' }}">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Department <span class="text-danger">*</span></label>
                            <select class="form-select select2-modal" name="hr_department_id">
                                <option value="">Select</option>
                                @php $departments = App\Models\HrDepartment::get(); @endphp
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Unit</label>
                            <select class="form-select select2-modal" name="hr_department_unit_id">
                                <option value="">Select</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Consultant Doctor <span class="text-danger">*</span></label>
                            <select class="form-select select2-modal" name="doctor_id">
                                <option value="">Select</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Slot</label>
                            <select class="form-select select2-modal" name="slot">
                                <option value="">Select Slot</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Standard Charge (Rs.)</label>
                            <input type="text" class="form-control" name="standard_charge" readonly>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Applied Charge (Rs.) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="applied_charge" min="0" step="0.01">
                            <input type="hidden" class="form-control" value="" name="apply_charge_type">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Payment Mode</label>
                            <select class="form-select" name="payment_mode">
                                <option value="Cash">Cash</option>
                                <option value="Card">Card</option>
                                <option value="Online">Online</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">TPA</label>
                            <select class="form-select select2-modal" name="tpa_id">
                                <option value="">Select</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">TPA Ref. No.</label>
                            <input type="text" class="form-control" name="tpa_reference_no" value="{{ $data?->tpa_reference_no ?? '' }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Consultation Rule</label>
                            <input type="text" class="form-control" name="consultation_case_label_display" readonly value="{{ $data?->consultation_case_label ?? '' }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Charge Source</label>
                            <input type="text" class="form-control" name="consultation_charge_source_display" readonly value="{{ $data?->consultation_charge_source ?? '' }}">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Valid Till</label>
                            <input type="text" class="form-control" name="consultation_valid_until_display" readonly value="{{ $data?->consultation_valid_until ? \Carbon\Carbon::parse($data->consultation_valid_until)->format('d-m-Y') : '' }}">
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Live Consultation</label>
                            <select class="form-select" name="live_consultation">
                                <option value="No" {{ ($data?->live_consultation ?? 'No') === 'No' ? 'selected' : '' }}>No</option>
                                <option value="Yes" {{ ($data?->live_consultation ?? 'No') === 'Yes' ? 'selected' : '' }}>Yes</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Casualty</label>
                            <select class="form-select select2-modal" name="casualty">
                                <option value="No" {{ ($data?->casualty ?? 'No') === 'No' ? 'selected' : '' }}>No</option>
                                <option value="Yes" {{ ($data?->casualty ?? 'No') === 'Yes' ? 'selected' : '' }}>Yes</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">MLC Patient</label>
                            <select class="form-select select2-modal" name="mlc_patient">
                                <option value="No" {{ ($data?->mlc_patient ?? 'No') === 'No' ? 'selected' : '' }}>No</option>
                                <option value="Yes" {{ ($data?->mlc_patient ?? 'No') === 'Yes' ? 'selected' : '' }}>Yes</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="opd-form-card">
                    <div class="card-title">Quick Clinical Notes (Optional)</div>
                    <div class="row g-3">
                        <div class="col-md-2">
                            <label class="form-label">Height</label>
                            <input type="number" name="height" class="form-control" value="{{ $data?->height ?? '' }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Weight</label>
                            <input type="number" name="weight" class="form-control" value="{{ $data?->weight ?? '' }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">BP</label>
                            <input type="number" name="bp" class="form-control" value="{{ $data?->bp ?? '' }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Pulse</label>
                            <input type="number" name="pluse" class="form-control" value="{{ $data?->pluse ?? '' }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Temperature</label>
                            <input type="number" name="temperature" class="form-control" value="{{ $data?->temperature ?? '' }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Respiration</label>
                            <input type="number" name="respiration" class="form-control" value="{{ $data?->respiration ?? '' }}">
                        </div>
                        <div class="col-md-6">
                            @php $symptomsTypes = App\Models\SymptomsType::get(); @endphp
                            <label class="form-label">Symptoms Type</label>
                            <select class="form-select select2-modal" name="symptoms_type[]" multiple>
                                <option value="">Select</option>
                                @foreach($symptomsTypes as $symptomsType)
                                    <option value="{{ $symptomsType->id }}" {{ in_array((string) $symptomsType->id, array_map('strval', (array) ($data?->symptoms_type_id ?? [])), true) ? 'selected' : '' }}>{{ $symptomsType->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Symptoms</label>
                            <select class="form-select select2-modal" name="symptoms[]" multiple>
                                <option value="">Select</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Symptoms Description</label>
                            <textarea class="form-control" name="symptoms_description" rows="2">{{ $data?->symptoms_description ?? '' }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="opd-form-card">
                    <button class="btn btn-link p-0 opd-advanced-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#opdAdvancedSection" aria-expanded="false" aria-controls="opdAdvancedSection">
                        + Open Advanced Patient Details
                    </button>
                    <div class="collapse mt-3" id="opdAdvancedSection">
                        <div class="row g-3">
                            <div class="col-md-2">
                                <label class="form-label">Date of Birth</label>
                                <input type="text" name="date_of_birth" class="form-control" id="opd-dob" value="{{ $revisitPatient?->date_of_birth ? \Carbon\Carbon::parse($revisitPatient->date_of_birth)->format('d-m-Y') : '' }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Blood Group</label>
                                <select class="form-select select2-modal" name="blood_group">
                                    <option value="">Select</option>
                                    @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bloodGroup)
                                        <option value="{{ $bloodGroup }}" {{ ($revisitPatient?->blood_group ?? '') === $bloodGroup ? 'selected' : '' }}>{{ $bloodGroup }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Marital Status</label>
                                <select class="form-select select2-modal" name="marital_status">
                                    <option value="">Select</option>
                                    <option value="Single" {{ ($revisitPatient?->marital_status ?? '') === 'Single' ? 'selected' : '' }}>Single</option>
                                    <option value="Married" {{ ($revisitPatient?->marital_status ?? '') === 'Married' ? 'selected' : '' }}>Married</option>
                                    <option value="Divorced" {{ ($revisitPatient?->marital_status ?? '') === 'Divorced' ? 'selected' : '' }}>Divorced</option>
                                    <option value="Not Specified" {{ ($revisitPatient?->marital_status ?? '') === 'Not Specified' ? 'selected' : '' }}>Not Specified</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Aadhaar No.</label>
                                <input type="text" name="aadhar_card_no" class="form-control" value="{{ $revisitPatient?->aadhar_no ?? '' }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" value="{{ $revisitPatient?->email ?? '' }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Patient Photo</label>
                                <input type="file" name="image" class="form-control">
                            </div>
                            <div class="col-md-3">
                                @php $nationalities = App\Models\Nationality::get(); @endphp
                                <label class="form-label">Nationality</label>
                                <select class="form-select select2-modal" name="nationality_id">
                                    <option value="">Select</option>
                                    @foreach($nationalities as $nationality)
                                        <option value="{{ $nationality->id }}" {{ (string) ($revisitPatient?->nationality_id ?? '') === (string) $nationality->id ? 'selected' : '' }}>{{ $nationality->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                @php $religions = App\Models\Religion::get(); @endphp
                                <label class="form-label">Religion</label>
                                <select class="form-select select2-modal" name="religion_id">
                                    <option value="">Select</option>
                                    @foreach($religions as $religion)
                                        <option value="{{ $religion->id }}" {{ (string) ($revisitPatient?->religion_id ?? '') === (string) $religion->id ? 'selected' : '' }}>{{ $religion->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Is Staff?</label>
                                <div class="pt-2">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="is_staff" id="isStaffYes" value="Yes" {{ ($revisitPatient?->is_staff ?? 'No') === 'Yes' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="isStaffYes">Yes</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="is_staff" id="isStaffNo" value="No" {{ ($revisitPatient?->is_staff ?? 'No') === 'No' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="isStaffNo">No</label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                @php $categories = App\Models\PatientCategory::get(); @endphp
                                <label class="form-label">Category</label>
                                <select class="form-select select2-modal" name="patient_category_id">
                                    <option value="">Select</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ (string) ($revisitPatient?->patient_category_id ?? '') === (string) $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                @php $allergies = App\Models\Allergy::get(); @endphp
                                <label class="form-label">Allergies</label>
                                <select class="form-select select2-modal" name="allergy_id[]" multiple>
                                    @foreach($allergies as $allergy)
                                        <option value="{{ $allergy->id }}" {{ in_array($allergy->id, $revisitPatient?->allergy_id ?? []) ? 'selected' : '' }}>{{ $allergy->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                @php $habits = App\Models\Habit::get(); @endphp
                                <label class="form-label">Habits</label>
                                <select class="form-select select2-modal" name="habit_id[]" multiple>
                                    @foreach($habits as $habit)
                                        <option value="{{ $habit->id }}" {{ in_array($habit->id, $revisitPatient?->habit_id ?? []) ? 'selected' : '' }}>{{ $habit->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                @php $dietaries = App\Models\Dietary::get(); @endphp
                                <label class="form-label">Dietary Preferences</label>
                                <select class="form-select select2-modal" name="dietary_id[]" multiple>
                                    @foreach($dietaries as $dietary)
                                        <option value="{{ $dietary->id }}" {{ in_array($dietary->id, $revisitPatient?->dietary_id ?? []) ? 'selected' : '' }}>{{ $dietary->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                @php $diseaseTypes = App\Models\DiseaseType::get(); @endphp
                                <label class="form-label">Disease Type</label>
                                <select class="form-select select2-modal" name="disease_type_id[]" multiple>
                                    @foreach($diseaseTypes as $diseaseType)
                                        <option value="{{ $diseaseType->id }}" {{ in_array((string) $diseaseType->id, array_map('strval', (array) $revisitDiseaseType), true) ? 'selected' : '' }}>{{ $diseaseType->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                @php $diseases = App\Models\Disease::get(); @endphp
                                <label class="form-label">Diseases</label>
                                <select class="form-select select2-modal" name="disease_id[]" multiple>
                                    @foreach($diseases as $disease)
                                        <option value="{{ $disease->id }}" {{ in_array((string) $disease->id, array_map('strval', (array) $revisitDisease), true) ? 'selected' : '' }}>{{ $disease->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Address</label>
                                <textarea name="address" class="form-control" rows="2">{{ $revisitPatient?->address ?? '' }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Known Allergies / Clinical History</label>
                                <textarea name="known_allergies" class="form-control" rows="2">{{ $revisitPatient?->known_allergies ?? '' }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
    <!-- <button type="button" class="btn btn-warning btn-submit-with-print" data-print-mode="sticker">{{ $id ? 'Update' : 'Create' }} and Print Sticker</button> -->
    <button type="button" class="btn btn-info btn-submit-with-print" data-print-mode="health_card">{{ $id ? 'Update' : 'Create' }} and Print Health Card</button>
    <button type="button" class="btn btn-primary btn-submit-with-print" data-print-mode="opd_print">{{ $id ? 'Update' : 'Create' }} and Print OPD Slip</button>
    <button type="button" class="btn btn-success btn-submit-with-print" data-print-mode="none">{{ $id ? 'Update' : 'Create' }}</button>
</div>
