
<div class="card p-0 shadow-none rounded-0  border-bottom">
    <h5 class="theme-color mt-3">General Information</h5>
    <div class="row">
        <div class="col-md-2">
            <div class="infodata">
                <label><strong>Temperature(°F)</strong></label>
                <p>{{ @$general_info->temprature??'' }}</p>
            </div>
        </div>
        <div class="col-md-2">
            <div class="infodata">
                <label><strong>Pulse Rate Per Minute(BPM)</strong></label>
                <p>{{ @$general_info->pulserate??'' }}</p>
            </div>
        </div>
        <div class="col-md-2">
            <div class="infodata">
                <label><strong>Height (In CM)</strong></label>
                <p>{{ @$general_info->height??'' }}</p>
            </div>
        </div>
        <div class="col-md-2">
            <div class="infodata">
                <label><strong>Weight (In KG)</strong></label>
                <p>{{ @$general_info->weight??'' }}</p>
            </div>
        </div>
        <div class="col-md-2">
            <div class="infodata">
                <label><strong>BMI</strong></label>
                <p>{{ @$general_info->bmi??'' }}</p>
            </div>
        </div>
        <div class="col-md-2">
            <div class="infodata">
                <label><strong>Cyanosis</strong></label>
                <p>{{ @$general_info->cyanosis }}</p>
            </div>
        </div>
        <div class="col-md-2">
            <div class="infodata">
                <label><strong>Pallor</strong></label>
                <p>{{ @$general_info->pallor }}</p>
            </div>
        </div>
        <div class="col-md-2">
            <div class="infodata">
                <label><strong>Malnutration</strong></label>
                <p>{{ @$general_info->malnutration }}</p>
            </div>
        </div>
        <div class="col-md-2">
            <div class="infodata">
                <label><strong>Oedema in
                Feet</strong></label>
                <p>{{ @$general_info->oedema }}</p>
            </div>
        </div>
    </div>
</div>
<div class="card p-0 shadow-none rounded-0  border-bottom">
    <h5 class="theme-color mt-3">Family History</h5>
    <div class="row">
        <div class="col-md-2">
            <div class="infodata">
                <label><strong>Diabetes</strong></label>
                <p>{{ @$family_history->diabetes->name }}</p>
            </div>
        </div>
        <div class="col-md-2">
            <div class="infodata">
                <label><strong>Hypertension</strong></label>
                <p>{{ @$family_history->hypertension->name }}</p>
            </div>
        </div>
        <div class="col-md-2">
            <div class="infodata">
                <label><strong>Heart Disease</strong></label>
                <p>{{ @$family_history->heartdisease->name }}</p>
            </div>
        </div>
        <div class="col-md-2">
            <div class="infodata">
                <label><strong>Stroke</strong></label>
                <p>{{ @$family_history->stroke->name }}</p>
            </div>
        </div>
        <div class="col-md-2">
            <div class="infodata">
                <label><strong>Cancer</strong></label>
                <p>{{ @$family_history->cancer->name }}</p>
            </div>
        </div>
        <div class="col-md-2">
            <div class="infodata">
                <label><strong>Tuberculosis</strong></label>
                <p>{{ @$family_history->tuberculosis->name }}</p>
            </div>
        </div>
        <div class="col-md-2">
            <div class="infodata">
                <label><strong>Tuberculosis</strong></label>
                <p>{{ @$family_history->tuberculosis->name }}</p>
            </div>
        </div>
    </div>
</div>
<div class="card p-0 shadow-none rounded-0  border-bottom">
    <h5 class="theme-color mt-3">Personal History</h5>
    <div class="row">
        <div class="col-md-2">
            <div class="infodata">
                <label><strong>Appetite</strong></label>
                <p>{{ @$personal_history->appetite->name }}</p>
            </div>
        </div>
        <div class="col-md-2">
            <div class="infodata">
                <label><strong>Bowels</strong></label>
                <p>{{ @$personal_history->bowel->name }}</p>
            </div>
        </div>
        <div class="col-md-2">
            <div class="infodata">
                <label><strong>Nutrition</strong></label>
                <p>{{ @$personal_history->nutrition->name }}</p>
            </div>
        </div>
        <div class="col-md-2">
            <div class="infodata">
                <label><strong>Diet</strong></label>
                <p>{{ @$personal_history->diet->name }}</p>
            </div>
        </div>
        <div class="col-md-2">
            <div class="infodata">
                <label><strong>Known Allergies</strong></label>
                <p>{{ @$personal_history->known_allergies }}</p>
            </div>
        </div>
        <div class="col-md-2">
            <div class="infodata">
                <label><strong>Allergy Details</strong></label>
                <p>{{ @$personal_history->allergy_detail }}</p>
            </div>
        </div>
        <div class="col-md-2">
            <div class="infodata">
                <label><strong>Habits/Addictions</strong></label>
                <p>{{ @$personal_history->habits }}</p>
            </div>
        </div>
        <div class="col-md-2">
            <div class="infodata">
                <label><strong>Habits/Addiction Details</strong></label>
                <p>{{ @$personal_history->habits_detail }}</p>
            </div>
        </div>
    </div>
</div>
<div class="card p-0 shadow-none rounded-0  border-bottom">
    <h5 class="theme-color mt-3">Authentication Consent</h5>
    <div class="row">
        @if(@$authentication_consent->hospital_declaration_form)
            <div class="col-md-2 mb-2">
                <div class="infodata">
                    <label><strong>Hospital Declaration Form (During Admission)</strong>&nbsp; <a href="{{ asset('public/storage/'.@$authentication_consent->hospital_declaration_form) }}" target="_blank" class="btn btn-outline-primary btn-sm">View Document</a></label>
                </div>
            </div>
        @endif
        <div class="col-md-6">
            <div class="infodata">
                <label><strong>Remarks</strong></label>
                <p>{{ @$authentication_consent->remarks }}</p>
            </div>
        </div>
    </div>
</div>
<div class="card p-0 shadow-none rounded-0  border-bottom">
    <h5 class="theme-color mt-3">Admission Details</h5>
    <div class="row">
        <div class="col-md-2">
            <div class="infodata">
                <label><strong>Admission Date</strong></label>
                <p>{{ @$admission_details->admission_date }}</p>
            </div>
        </div>
        <div class="col-md-2">
            <div class="infodata">
                <label><strong>Proposed Surgery Date</strong></label>
                <p>{{ @$admission_details->surgery_date }}</p>
            </div>
        </div>
        <div class="col-md-2">
            <div class="infodata">
                <label><strong>AdmissionType</strong></label>
                <p>{{ @$admission_details->admission_type->name }}</p>
            </div>
        </div>
        <div class="col-md-2">
            <div class="infodata">
                <label><strong>Medico Legal Case</strong></label>
                <p>{{ @$admission_details->legal_case }}</p>
            </div>
        </div>
        @if(@$admission_details->fir_doc)
            <div class="col-md-2 mb-2">
                <div class="infodata">
                    <label><strong>FIR </strong>&nbsp; <a href="{{ asset('public/storage/'.@$admission_details->fir_doc) }}" target="_blank" class="btn btn-outline-primary btn-sm">View Document</a></label>
                </div>
            </div>
        @endif
        @if(@$preauth_register->discharge_type)
            <div class="col-md-2">
                <div class="infodata">
                    <label><strong>Discharge Type</strong></label>
                    <p>{{ @$preauth_register->discharge_type }}</p>
                </div>
            </div>
        @endif
        @if(@$preauth_register->discharge_stage)
            <div class="col-md-2">
                <div class="infodata">
                    <label><strong>Discharge Stage</strong></label>
                    <p>{{ @$preauth_register->discharge_stage }}</p>
                </div>
            </div>
        @endif
        @if(@$preauth_register->lama_date)
            <div class="col-md-2">
                <div class="infodata">
                    <label><strong>LAMA/DAMA/DOPR Date</strong></label>
                    <p>{{ @$preauth_register->lama_date }}</p>
                </div>
            </div>
        @endif
        @if(@$preauth_register->surgery_date)
            <div class="col-md-2">
                <div class="infodata">
                    <label><strong>Surgery Date</strong></label>
                    <p>{{ @$preauth_register->surgery_date }}</p>
                </div>
            </div>
        @endif
        @if(@$preauth_register->discharge_date)
            <div class="col-md-2">
                <div class="infodata">
                    <label><strong>Discharge Date</strong></label>
                    <p>{{ @$preauth_register->discharge_date }}</p>
                </div>
            </div>
        @endif
        @if(@$preauth_register->provide_medicine)
            <div class="col-md-2">
                <div class="infodata">
                    <label><strong>Hospital Provided Medicine During Treatment</strong></label>
                    <p>{{ @$preauth_register->provide_medicine }}</p>
                </div>
            </div>
        @endif
    </div>
</div>
<div class="card p-0 shadow-none rounded-0  border-bottom">
    <h5 class="theme-color mt-3">Diagnosis</h5>
    <div class="row">
        <div class="col-12">
            <div
                class="table-responsive mt-5 text-nowrap">
                <table class="table">
                    <thead class="table-dark">
                        <tr>
                            <th>No.</th>
                            <th>Diagnosis Code</th>
                            <th>Diagnosis Description</th>
                            <th>Diagnosis Type</th>
                        </tr>
                    </thead>
                    <tbody
                        class="table-border-bottom-0 diagnosis-body">
                        @include('medical-committee._partials.diagnosis', ['preauth_diagnosis' => @$preauth_diagnosis,'is_action_hide'=>1])
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="card p-0 shadow-none rounded-0  border-bottom">
    <h5 class="theme-color mt-3">Treatment Plan</h5>
    <div class="row">
        <div class="col-12">
            <div
                class="table-responsive mt-5 text-nowrap">
                <table class="table">
                    <thead class="table-dark">
                        <tr>
                            <th>No.</th>
                            <th>Speciality</th>
                            <th>Procedure</th>
                            <th>Stratification</th>
                            <th>Day/Units</th>
                            <th>Amount</th>
                            <th>ICHI Code</th>
                        </tr>
                    </thead>
                    <tbody
                        class="table-border-bottom-0 procedure-body">
                        @include('medical-committee._partials.procedures', ['procedures' => @$procedures,'is_action_hide'=>1])
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="card p-0 shadow-none rounded-0  border-bottom">
    <h5 class="theme-color mt-3">Investigations</h5>
    <div class="row">
        @if(@$investigations->count() > 0 )
            @foreach(@$investigations as $investigation)
                <div class="col-md-2 mb-2">
                    <div class="infodata">
                        <label><strong>{{ @$investigation->investigation->name }}</strong>&nbsp; <a href="{{ asset('public/storage/'.@$investigation->file) }}" target="_blank" class="btn btn-outline-primary btn-sm">View Document</a></label>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>
<div class="card p-0 shadow-none rounded-0  border-bottom">
    <h5 class="theme-color mt-3">Care Team Details</h5>
    <div class="row">
        <div class="col-12">
            <div class="table-responsive mt-5 text-nowrap">
                <table class="table">
                    <thead class="table-dark">
                        <tr>
                            <th>No.</th>
                            <th>Doctor Name</th>
                            <th>Registration ID / HPR ID
                            </th>
                            <th>Speciality</th>
                            <th>Contact Number</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0 care-team-body">
                        @include('medical-committee._partials.teams', ['preauth_teams' => @$preauth_teams,'is_action_hide'=>1])
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="card p-0 shadow-none rounded-0  border-bottom">
    <h5 class="theme-color mt-3">Amount and incentive Details</h5>
    <div class="row">
        <div class="col-12">
            <div class="table-responsive mt-5 text-nowrap">
                <table class="table">
                    <thead class="table-dark">
                        <tr>
                            <th>No.</th>
                            <th>Package Code</th>
                            <th>Package Type</th>
                            <th>Stratification Cost</th>
                            <th>Quality</th>
                            <th>Package Cost</th>
                            <th>Adj Factor</th>
                            <th>Incentive</th>
                            <th>Total Amount</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0 finance-body">
                        @include('medical-committee._partials.finance', ['procedures' => @$procedures])
                    </tbody>
                </table>
            </div>
            <ul class="d-flex listing-right finance-total-body">
                @include('medical-committee._partials.finance-total', ['procedures' => @$procedures])
            </ul>
        </div>
    </div>
</div>