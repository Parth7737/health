@extends('layouts.hospital.app', ['is_header_hiden' => true])
@section('title','Pre-Authorization')

@push('styles')
<link rel="stylesheet" href="{{ asset('public/front/assets/vendor/fonts/remixicon/remixicon.css') }}" />
<link rel="stylesheet" href="{{ asset('public/front/assets/vendor/libs/bs-stepper/bs-stepper.css') }}" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" href="{{ asset('public/front/assets/css/scheme-preauth.css') }}?v=11" />
@endpush

@section('content')
<div class="loader-overlay scheme-preauth-loader" style="display:none;">
    <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading</span></div>
</div>

<div class="container-xxl flex-grow-1 container-p-y mb-5 scheme-preauth-page">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="javascript:void(0);">Home</a>
            </li>
            <li class="breadcrumb-item active">Pre Authorization ({{ $preauthRegister->register_id ?: 'Draft' }})</li>
        </ol>
    </nav>
    <div class="scheme-preauth-stack">
            <div class="card scheme-preauth-beneficiary-card mb-4 border border-primary">
                <div class="card-body">
                    <div class="row row-cols-5">
                        <div class="col">
                            <div
                                class="d-flex text-center justify-content-center flex-column border-end border-secondary">
                                @if($preauthBeneficiary->image_url)
                                <div class="position-relative image-overlay">
                                        <img src="{{ $preauthBeneficiary->image_url }}" width="80" alt="avatar"
                                            class="mb-3 rounded-circle" />
                                    </div>
                                @endif
                                <span class="number-3 mb-2">{{ @$preauthBeneficiary->name }}</span>
                                <span class="number-2 mb-2">{{ @$preauthBeneficiary->age }} Yr / {{ @$preauthBeneficiary->gender }}</span>
                                @if(@$preauthRegister->is_new_born_baby == 1)
                                    <strong><span class="number-3 mt-2">New Born Baby</span></strong>
                                    <span>{{ $preauthRegister->born_baby_name }}</span>
                                    <span class="number-2 mb-2">DOB : {{ @$preauthRegister->born_baby_dob }}  / {{ @$preauthRegister->born_baby_gender }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="col">
                            <div class="infodata">
                                <label>Care Plan</label>
                                <p><strong>{{ @$preauthBeneficiary->care_plan }}</strong></p>
                                <label>SGHS ID</label>
                                <p><strong>{{ @$preauthBeneficiary->card_id }}</strong></p>
                                <label>ABHA Number</label>
                                <p><strong>{{ @$preauthBeneficiary->aabha_id }}</strong></p>
                            </div>
                        </div>
                        <div class="col">
                            <div class="infodata">
                                <label>Mobile Number</label>
                                <p>{{ @$preauthBeneficiary->mobile_no }}</p>
                                <label>Address</label>
                                <p>{{ @$preauthBeneficiary->address }} | {{ @$preauthRegister->city }}, {{ @$preauthRegister->district_name }}, {{ @$preauthRegister->state_name }} - {{ @$preauthRegister->pincode }}</p>
                            </div>
                        </div>
                        <div class="col">
                            <div class="infodata">
                                <label>Registration ID</label>
                                <p>{{ $preauthRegister->register_id ?: 'Draft (pending SHA sync)' }}</p>
                                <label>Registration Date</label>
                                <p><strong>{{ date("d/m/Y h:i A",strtotime($preauthRegister->created_at)) }}</strong></p>
                            </div>
                        </div>
                        <!-- <div class="col">
                            <div class="infodata">
                                <label>Total Wallet Amount</label>
                                <p class="colored text-info">? 5,00,000.00</p>
                                <label>Wallet Balance</label>
                                <p class="colored text-info">? 5,00,000.00</p>
                                <div class="progress" style="height: 10px;">
                                    <div class="progress-bar bg-gradient-new" role="progressbar"
                                        style="width: 100%;" aria-valuenow="50" aria-valuemin="0"
                                        aria-valuemax="100">
                                    </div>
                                </div>
                            </div>
                        </div> -->
                    </div>
                </div>
            </div>
            <div class="bs-stepper wizard-numbered mt-2">
                @include('hospital.scheme-preauth._partials.preauth-step')
                <div class="bs-stepper-content">
                    <!-- Account Details -->
                    <div id="account-details" class="content">
                        <h6 class="mb-0">First Step</h6>
                    </div>
                    <!-- Personal Info -->
                    <div id="personal-info" class="content active">
                        <div class="accordion accordion-popout mt-4" id="accordionPopout">
                            <div class="accordion-item ">
                                <h2 class="accordion-header" id="headingPopoutOne">
                                    <button type="button" class="accordion-button medical-info-step collapsed {{ @$general_info && @$family_history && @$personal_history?'theme-color':'pending-color' }}"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#accordionPopoutOne"
                                        aria-expanded="false" aria-controls="accordionPopoutOne">
                                        Medical Information
                                    </button>
                                </h2>
                                <div id="accordionPopoutOne"
                                    class="accordion-collapse collapse"
                                    aria-labelledby="headingPopoutOne"
                                    data-bs-parent="#accordionPopout">
                                    <div class="accordion-body">
                                        <div class="inside-left-info-box {{ @$general_info?'success':'pending' }}">
                                            <form onSubmit="return false" id="generalInformationForm">
                                                @csrf
                                                <h4 class="colored-verticle-title">General
                                                    Information</h4>
                                                <div class="row g-3 spa-form-row">
                                                    <div class="col-md-6 col-lg-3">
                                                        <div
                                                            class="form-group">
                                                            <input type="number" id="temprature" name="temprature" oninput="sanitize(this, 'n','4');"
                                                                class="form-control" value="{{ @$general_info->temprature??'' }}"
                                                                placeholder="" />
                                                            <label
                                                                for="temprature">Temperature(�F) <span class="text-danger">*</span></label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-lg-3">
                                                        <div
                                                            class="form-group">
                                                            <input type="text" id="pulserate" name="pulserate" oninput="sanitize(this, 'n','4');" value="{{ @$general_info->pulserate??'' }}"
                                                                class="form-control"
                                                                placeholder="" />
                                                            <label for="pulserate">Pulse Rate Per
                                                                Minute (BPM)<span class="text-danger">*</span></label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-lg-3">
                                                        <div
                                                            class="form-group">
                                                            <input type="number" id="height" name="height" oninput="sanitize(this, 'n','4');" value="{{ @$general_info->height??'' }}"
                                                                class="form-control"
                                                                placeholder="" />
                                                            <label for="height">Height (In
                                                                CM) <span class="text-danger">*</span></label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-lg-3">
                                                        <div
                                                            class="form-group">
                                                            <input type="number" id="weight" name="weight" oninput="sanitize(this, 'n','5');" value="{{ @$general_info->weight??'' }}"
                                                                class="form-control"
                                                                placeholder="" />
                                                            <label for="weight">Weight (In
                                                                KG)</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-lg-3">
                                                        <div
                                                            class="form-group">
                                                            <input type="text" id="bmi" name="bmi" value="{{ @$general_info->bmi??'' }}"
                                                                class="form-control"
                                                                placeholder="" readonly/>
                                                            <label for="bmi">BMI</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-lg-3">
                                                        <label for="cyanosis"
                                                            class="form-label">Cyanosis</label>
                                                        <div class="d-flex spa-radio-group">
                                                            <div class="form-check">
                                                                <input class="form-check-input"
                                                                    type="radio" name="cyanosis"
                                                                    id="cyanosis_yes" value="Yes" {{ @$general_info->cyanosis && $general_info->cyanosis=='Yes'?'checked':'' }}>
                                                                <label class="form-check-label"
                                                                    for="cyanosis_yes">
                                                                    Yes
                                                                </label>
                                                            </div>
                                                            <div class="form-check ms-4">
                                                                <input class="form-check-input"
                                                                    type="radio" name="cyanosis"
                                                                    id="cyanosis_no" value="No" {{ @$general_info->cyanosis && $general_info->cyanosis=='No'?'checked':'' }}>
                                                                <label class="form-check-label"
                                                                    for="cyanosis_no">
                                                                    No
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-lg-3">
                                                        <label for="pallor" class="form-label">Pallor</label>
                                                        <div class="d-flex spa-radio-group">
                                                            <div class="form-check">
                                                                <input class="form-check-input"
                                                                    type="radio" name="pallor"
                                                                    id="pallor_yes" value="Yes" {{ @$general_info->pallor && $general_info->pallor=='Yes'?'checked':'' }}>
                                                                <label class="form-check-label"
                                                                    for="pallor_yes">
                                                                    Yes
                                                                </label>
                                                            </div>
                                                            <div class="form-check ms-4">
                                                                <input class="form-check-input"
                                                                    type="radio" name="pallor"
                                                                    id="pallor_no" value="No" {{ @$general_info->pallor && $general_info->pallor=='No'?'checked':'' }}>
                                                                <label class="form-check-label"
                                                                    for="pallor_no">
                                                                    No
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-lg-3">
                                                        <label for="malnutration"
                                                            class="form-label">Malnutration</label>
                                                        <div class="d-flex spa-radio-group">
                                                            <div class="form-check">
                                                                <input class="form-check-input"
                                                                    type="radio" name="malnutration"
                                                                    id="malnutration_yes" value="Yes" {{ @$general_info->malnutration && $general_info->malnutration=='Yes'?'checked':'' }}>
                                                                <label class="form-check-label"
                                                                    for="malnutration_yes">
                                                                    Yes
                                                                </label>
                                                            </div>
                                                            <div class="form-check ms-4">
                                                                <input class="form-check-input"
                                                                    type="radio" name="malnutration"
                                                                    id="malnutration_no" value="No" {{ @$general_info->malnutration && $general_info->malnutration=='No'?'checked':'' }}>
                                                                <label class="form-check-label"
                                                                    for="malnutration_no">
                                                                    No
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-lg-3">
                                                        <label for="oedema" class="form-label">Oedema in
                                                            Feet</label>
                                                        <div class="d-flex spa-radio-group">
                                                            <div class="form-check">
                                                                <input class="form-check-input"
                                                                    type="radio" name="oedema"
                                                                    id="oedema_yes" value="Yes" {{ @$general_info->oedema && $general_info->oedema=='Yes'?'checked':'' }}>
                                                                <label class="form-check-label"
                                                                    for="oedema_yes">
                                                                    Yes
                                                                </label>
                                                            </div>
                                                            <div class="form-check ms-4">
                                                                <input class="form-check-input"
                                                                    type="radio" name="oedema"
                                                                    id="oedema_no" value="No" {{ @$general_info->oedema && $general_info->oedema=='No'?'checked':'' }}>
                                                                <label class="form-check-label"
                                                                    for="oedema_no">
                                                                    No
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="d-flex justify-content-end">
                                                            <button id="general-info-btn"
                                                                class="btn btn-primary">SAVE</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="inside-left-info-box {{ @$family_history?'success':'pending' }} mt-3">
                                            <form onSubmit="return false" id="familyHistoryForm">
                                                <h4 class="colored-verticle-title">Family History
                                                </h4>
                                                <div class="row g-3 spa-form-row">
                                                    <div class="col-md-6 col-lg-3">
                                                        <div
                                                            class="form-group">
                                                            @php $diabetes_arr = \App\Models\PreauthReferenceOption::where('category', 'Diabetes')->orderBy('sort_order')->get(); @endphp
                                                            <select class="form-control select2" id="diabetes" name="diabetes"
                                                                name="diabetes">
                                                                <option value=""></option>
                                                                @foreach($diabetes_arr as $diabetes)
                                                                    <option value="{{ $diabetes->id }}" {{ @$family_history->diabetes_id == $diabetes->id?'selected':'' }}>{{ $diabetes->name }}</option>
                                                                @endforeach
                                                            </select>
                                                            <label for="diabetes">Diabetes <span class="text-danger">*</span></label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-lg-3">
                                                        <div
                                                            class="form-group">
                                                            @php $hypertension_arr = \App\Models\PreauthReferenceOption::where('category', 'Hypertension')->orderBy('sort_order')->get(); @endphp
                                                            <select class="form-control select2"
                                                                id="hypertension"
                                                                name="hypertension">
                                                                <option value=""></option>
                                                                @foreach($hypertension_arr as $hypertension)
                                                                    <option value="{{ $hypertension->id }}" {{ @$family_history->hypertension_id == $hypertension->id?'selected':'' }}>{{ $hypertension->name }}</option>
                                                                @endforeach
                                                            </select>
                                                            <label for="hypertension">Hypertension <span class="text-danger">*</span></label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-lg-3">
                                                        <div
                                                            class="form-group">
                                                            @php $heartdiseases = \App\Models\PreauthReferenceOption::where('category', 'HeartDisease')->orderBy('sort_order')->get(); @endphp
                                                            <select class="form-control select2"
                                                                id="heartdisease"
                                                                name="heartdisease">
                                                                <option value=""></option>
                                                                @foreach($heartdiseases as $heartdisease)
                                                                    <option value="{{ $heartdisease->id }}" {{ @$family_history->heartdisease_id == $heartdisease->id?'selected':'' }}>{{ $heartdisease->name }}</option>
                                                                @endforeach
                                                            </select>
                                                            <label for="heartdisease">Heart
                                                                Disease <span class="text-danger">*</span></label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-lg-3">
                                                        <div
                                                            class="form-group">
                                                            @php $strokes = \App\Models\PreauthReferenceOption::where('category', 'Stroke')->orderBy('sort_order')->get(); @endphp
                                                            <select class="form-control select2" id="stroke"
                                                                name="stroke">
                                                                <option value=""></option>
                                                                @foreach($strokes as $stroke)
                                                                    <option value="{{ $stroke->id }}" {{ @$family_history->stroke_id == $stroke->id?'selected':'' }}>{{ $stroke->name }}</option>
                                                                @endforeach
                                                            </select>
                                                            <label for="stroke">Stroke <span class="text-danger">*</span></label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-lg-3">
                                                        <div
                                                            class="form-group">
                                                            @php $cancers = \App\Models\PreauthReferenceOption::where('category', 'Cancer')->orderBy('sort_order')->get(); @endphp
                                                            <select class="form-control select2" id="cancer"
                                                                name="cancer">
                                                                <option value=""></option>
                                                                @foreach($cancers as $cancer)
                                                                    <option value="{{ $cancer->id }}" {{ @$family_history->cancer_id == $cancer->id?'selected':'' }}>{{ $cancer->name }}</option>
                                                                @endforeach
                                                            </select>
                                                            <label for="cancer">Cancer <span class="text-danger">*</span></label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-lg-3">
                                                        <div
                                                            class="form-group">
                                                            @php $tuberculosises = \App\Models\PreauthReferenceOption::where('category', 'Tuberculosis')->orderBy('sort_order')->get(); @endphp
                                                            <select class="form-control select2"
                                                                id="tuberculosis"
                                                                name="tuberculosis">
                                                                <option value=""></option>
                                                                @foreach($tuberculosises as $tuberculosis)
                                                                    <option value="{{ $tuberculosis->id }}" {{ @$family_history->tuberculosis_id == $tuberculosis->id?'selected':'' }}>{{ $tuberculosis->name }}</option>
                                                                @endforeach
                                                            </select>
                                                            <label
                                                                for="tuberculosis">Tuberculosis <span class="text-danger">*</span></label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-lg-3">
                                                        <div
                                                            class="form-group">
                                                            @php $asthma_arr = \App\Models\PreauthReferenceOption::where('category', 'Asthma')->orderBy('sort_order')->get(); @endphp
                                                            <select class="form-control select2" id="asthma"
                                                                name="asthma">
                                                                <option value=""></option>
                                                                @foreach($asthma_arr as $asthma)
                                                                    <option value="{{ $asthma->id }}" {{ @$family_history->asthma_id == $asthma->id?'selected':'' }}>{{ $asthma->name }}</option>
                                                                @endforeach
                                                            </select>
                                                            <label for="asthma">Asthma <span class="text-danger">*</span></label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="d-flex justify-content-end">
                                                            <button id="family-history-btn"
                                                                class="btn btn-primary">SAVE</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="inside-left-info-box {{ @$personal_history?'success':'pending' }} mt-3">
                                            <form onSubmit="return false" id="personalHistoryForm">
                                                <h4 class="colored-verticle-title">Personal History
                                                </h4>
                                                <div class="row g-3 spa-form-row">
                                                    <div class="col-md-6 col-lg-3">
                                                        <div
                                                            class="form-group">
                                                            @php $appetites = \App\Models\PreauthReferenceOption::where('category', 'Appetite')->orderBy('sort_order')->get(); @endphp
                                                            <select class="form-control select2" id="appetite"
                                                                name="appetite">
                                                                <option value=""></option>
                                                                @foreach($appetites as $appetite)
                                                                    <option value="{{ $appetite->id }}" {{ @$personal_history->appetite_id == $appetite->id?'selected':'' }}>{{ $appetite->name }}</option>
                                                                @endforeach
                                                            </select>
                                                            <label for="appetite">Appetite <span class="text-danger">*</span></label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-lg-3">
                                                        <div
                                                            class="form-group">
                                                            @php $bowels = \App\Models\PreauthReferenceOption::where('category', 'Bowels')->orderBy('sort_order')->get(); @endphp
                                                            <select class="form-control select2" id="bowels"
                                                                name="bowels">
                                                                <option value=""></option>
                                                                @foreach($bowels as $bowel)
                                                                    <option value="{{ $bowel->id }}" {{ @$personal_history->bowels_id == $bowel->id?'selected':'' }}>{{ $bowel->name }}</option>
                                                                @endforeach
                                                            </select>
                                                            <label for="bowels">Bowels <span class="text-danger">*</span></label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-lg-3">
                                                        <div
                                                            class="form-group">
                                                            @php $nutrition_arr = \App\Models\PreauthReferenceOption::where('category', 'Nutrition')->orderBy('sort_order')->get(); @endphp
                                                            <select class="form-control select2" id="nutrition"
                                                                name="nutrition">
                                                                <option value=""></option>
                                                                @foreach($nutrition_arr as $nutrition)
                                                                    <option value="{{ $nutrition->id }}" {{ @$personal_history->nutrition_id == $nutrition->id?'selected':'' }}>{{ $nutrition->name }}</option>
                                                                @endforeach
                                                            </select>
                                                            <label for="nutrition">Nutrition <span class="text-danger">*</span></label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-lg-3">
                                                        <div
                                                            class="form-group">
                                                            @php $dietes = \App\Models\PreauthReferenceOption::where('category', 'Diet')->orderBy('sort_order')->get(); @endphp
                                                            <select class="form-control select2" id="diet"
                                                                name="diet">
                                                                <option value=""></option>
                                                                @foreach($dietes as $diet)
                                                                    <option value="{{ $diet->id }}" {{ @$personal_history->diet_id == $diet->id?'selected':'' }}>{{ $diet->name }}</option>
                                                                @endforeach
                                                            </select>
                                                            <label for="diet">Diet <span class="text-danger">*</span></label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-lg-3">
                                                        <label for="known_allergies" class="form-label">Known
                                                            Allergies <span class="text-danger">*</span></label>
                                                        <div class="d-flex spa-radio-group">
                                                            <div class="form-check">
                                                                <input class="form-check-input"
                                                                    type="radio" name="known_allergies"
                                                                    id="known_allergies_yes" value="Yes" {{ @$personal_history->known_allergies == 'Yes'?'checked':'' }}>
                                                                <label class="form-check-label"
                                                                    for="known_allergies_yes">
                                                                    Yes
                                                                </label>
                                                            </div>
                                                            <div class="form-check ms-4">
                                                                <input class="form-check-input"
                                                                    type="radio" name="known_allergies"
                                                                    id="known_allergies_no" value="No" {{ @$personal_history->known_allergies == 'No'?'checked':'' }}>
                                                                <label class="form-check-label"
                                                                    for="known_allergies_no">
                                                                    No
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-lg-3 allergy-detail-field d-none">
                                                        <div
                                                            class="form-group">
                                                            <input type="text" id="allergy_detail" oninput="sanitize(this, 't');" name="allergy_detail"
                                                                class="form-control"
                                                                placeholder="" value="{{ @$personal_history->allergy_detail }}"/>
                                                            <label for="allergy_detail">Allergy
                                                                Details</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-lg-3">
                                                        <label for="habits"
                                                            class="form-label">Habits/Addictions <span class="text-danger">*</span></label>
                                                        <div class="d-flex spa-radio-group">
                                                            <div class="form-check">
                                                                <input class="form-check-input"
                                                                    type="radio" name="habits"
                                                                    id="habits_yes" value="Yes" {{ @$personal_history->habits == 'Yes'?'checked':'' }}>
                                                                <label class="form-check-label"
                                                                    for="habits_yes">
                                                                    Yes
                                                                </label>
                                                            </div>
                                                            <div class="form-check ms-4">
                                                                <input class="form-check-input"
                                                                    type="radio" name="habits"
                                                                    id="habits_no" value="No" {{ @$personal_history->habits == 'No'?'checked':'' }}>
                                                                <label class="form-check-label"
                                                                    for="habits_no">
                                                                    No
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-lg-3 habits-detail-field d-none">
                                                        <div
                                                            class="form-group">
                                                            <input type="text" id="habits_detail" oninput="sanitize(this, 't');" name="habits_detail"
                                                                class="form-control"
                                                                placeholder="" value="{{ @$personal_history->habits_detail }}"/>
                                                            <label
                                                                for="habits_detail">Habits/Addiction
                                                                Details</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="d-flex justify-content-end">
                                                            <button id="personal-history-btn"
                                                                class="btn btn-primary">SAVE</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingPopoutTwo">
                                    <button type="button"
                                        class="accordion-button admission-info-step {{ @$authentication_consent && @$admission_details?'theme-color':'pending-color' }} collapsed"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#accordionPopoutTwo"
                                        aria-expanded="false"
                                        aria-controls="accordionPopoutTwo">
                                        Admission Information
                                    </button>
                                </h2>
                                <div id="accordionPopoutTwo" class="accordion-collapse collapse"
                                    aria-labelledby="headingPopoutTwo"
                                    data-bs-parent="#accordionPopout">
                                    <div class="accordion-body">
                                        <div class="inside-left-info-box {{ @$authentication_consent?'success':'pending' }}">
                                            <form onSubmit="return false" id="authenticationConsentForm">
                                                <h4 class="colored-verticle-title">Authentication
                                                    Consent
                                                </h4>
                                                <div class="row align-items-end g-3 spa-form-row">
                                                    <div class="col-md-6 col-lg-3">
                                                        <label for="formFile"
                                                            class="form-label">Hospital 
                                                            Declaration Form (During
                                                            Admission)</label>
                                                        <div class="file-upload-section">
                                                            <div class="file-upload-wrapper">
                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                    height="24px"
                                                                    viewBox="0 -960 960 960"
                                                                    width="24px" fill="#6200ea">
                                                                    <path
                                                                        d="M260-160q-91 0-155.5-63T40-377q0-78 47-139t123-78q25-92 100-149t170-57q117 0 198.5 81.5T760-520q69 8 114.5 59.5T920-340q0 75-52.5 127.5T740-160H520q-33 0-56.5-23.5T440-240v-206l-64 62-56-56 160-160 160 160-56 56-64-62v206h220q42 0 71-29t29-71q0-42-29-71t-71-29h-60v-80q0-83-58.5-141.5T480-720q-83 0-141.5 58.5T280-520h-20q-58 0-99 41t-41 99q0 58 41 99t99 41h100v80H260Zm220-280Z" />
                                                                </svg>
                                                                <p>
                                                                    <strong>Browse</strong></p>
                                                            </div>
                                                            <input type="file" name="hospital_declaration_form"
                                                                class="file-input d-none" />
                                                            <div
                                                                class="uploaded-file file-upload-display d-none">
                                                                <span
                                                                    class="file-name">Sample.pdf</span>
                                                                <button
                                                                    class="remove-file-btn bg-transparent border-0 p-0">
                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                        height="24px"
                                                                        viewBox="0 -960 960 960"
                                                                        width="24px"
                                                                        fill="undefined">
                                                                        <path
                                                                            d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z" />
                                                                    </svg>
                                                                </button>
                                                            </div>
                                                            @if(@$authentication_consent->hospital_declaration_form)
                                                                <label><a href="{{ asset('public/storage/'.@$authentication_consent->hospital_declaration_form) }}" target="_blank" class="btn btn-outline-primary btn-sm mt-2">View Document</a></label>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-lg-3">
                                                        <div
                                                            class="form-group">
                                                            <input type="text" id="remarks" name="remarks" oninput="sanitize(this, 'm');"
                                                                class="form-control" value="{{ @$authentication_consent->remarks }}"
                                                                placeholder="Remarks" />
                                                            <label for="remarks">Remarks</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="d-flex justify-content-end">
                                                            <button id="authentication-consent-btn"
                                                                class="btn btn-primary">SAVE</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="inside-left-info-box {{ @$admission_details?'success':'pending' }} mt-3">
                                            <form onSubmit="return false" id="admissionDetailsForm">
                                                <h4 class="colored-verticle-title">Admission Details
                                                </h4>
                                                <div class="row g-3 spa-form-row">
                                                    <div class="col-md-6 col-lg-3">
                                                        <div
                                                            class="form-group">
                                                            <input type="text"
                                                                id="admission-date" name="admission_date" oninput="sanitize(this, 'd');" value="{{ @$admission_details->admission_date }}"
                                                                class="form-control" />
                                                            <label
                                                                for="admission-date">Admission
                                                                Date <span class="text-danger">*</span></label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-lg-3">
                                                        <div
                                                            class="form-group">
                                                            <input type="text"
                                                                id="surgery-date" name="surgery_date" oninput="sanitize(this, 'd');" value="{{ @$admission_details->surgery_date }}"
                                                                class="form-control" />
                                                            <label
                                                                for="surgery-date">Proposed
                                                                Surgery Date <span class="text-danger">*</span></label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-lg-3">
                                                        <div
                                                            class="form-group">
                                                            @php $admission_types = \App\Models\PreauthReferenceOption::where('category', 'AdmissionType')->orderBy('sort_order')->get(); @endphp
                                                            <select class="form-control select2"
                                                                id="admission_type" name="admission_type">
                                                                <option value=""></option>
                                                                @foreach($admission_types as $admission_type)
                                                                    <option value="{{ $admission_type->id }}" {{ @$admission_details->admission_type_id == $admission_type->id?'selected':'' }}>{{ $admission_type->name }}</option>
                                                                @endforeach
                                                            </select>
                                                            <label for="admission_type">Admission
                                                                Type <span class="text-danger">*</span></label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-lg-3">
                                                        <label for="legal_case" class="form-label">Medico Legal
                                                            Case <span class="text-danger">*</span></label>
                                                        <div class="d-flex spa-radio-group">
                                                            <div class="form-check">
                                                                <input class="form-check-input"
                                                                    type="radio" name="legal_case"
                                                                    id="legal_case_yes" value="Yes" {{ @$admission_details->legal_case == 'Yes'?'checked':'' }}>
                                                                <label class="form-check-label"
                                                                    for="legal_case_yes">
                                                                    Yes
                                                                </label>
                                                            </div>
                                                            <div class="form-check ms-4">
                                                                <input class="form-check-input"
                                                                    type="radio" name="legal_case"
                                                                    id="legal_case_no" value="No" {{ @$admission_details->legal_case == 'No'?'checked':'' }}>
                                                                <label class="form-check-label"
                                                                    for="legal_case_no">
                                                                    No
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-lg-3 legal-field  {{ @$admission_details->legal_case == 'Yes'?'':'d-none' }}">
                                                        <label for="formFile"
                                                            class="form-label">FIR <span class="text-danger">*</span></label>
                                                        <div class="file-upload-section">
                                                            <div class="file-upload-wrapper">
                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                    height="24px"
                                                                    viewBox="0 -960 960 960"
                                                                    width="24px" fill="#6200ea">
                                                                    <path
                                                                        d="M260-160q-91 0-155.5-63T40-377q0-78 47-139t123-78q25-92 100-149t170-57q117 0 198.5 81.5T760-520q69 8 114.5 59.5T920-340q0 75-52.5 127.5T740-160H520q-33 0-56.5-23.5T440-240v-206l-64 62-56-56 160-160 160 160-56 56-64-62v206h220q42 0 71-29t29-71q0-42-29-71t-71-29h-60v-80q0-83-58.5-141.5T480-720q-83 0-141.5 58.5T280-520h-20q-58 0-99 41t-41 99q0 58 41 99t99 41h100v80H260Zm220-280Z" />
                                                                </svg>
                                                                <p>
                                                                    <strong>Browse</strong></p>
                                                            </div>
                                                            <input type="file" name="fir_doc"
                                                                class="file-input d-none" />
                                                            <div
                                                                class="uploaded-file file-upload-display d-none">
                                                                <span
                                                                    class="file-name">Sample.pdf</span>
                                                                <button
                                                                    class="remove-file-btn bg-transparent border-0 p-0">
                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                        height="24px"
                                                                        viewBox="0 -960 960 960"
                                                                        width="24px"
                                                                        fill="undefined">
                                                                        <path
                                                                            d="M280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520ZM360-280h80v-360h-80v360Zm160 0h80v-360h-80v360ZM280-720v520-520Z" />
                                                                    </svg>
                                                                </button>
                                                            </div>
                                                            </br><small class="text-danger fs-11">Upload a only pdf format file and max size should be 5MB</small></br>
                                                            @if(@$admission_details->fir_doc)
                                                                <label><a href="{{ asset('public/storage/'.@$admission_details->fir_doc) }}" target="_blank" class="btn btn-outline-primary btn-sm">View Document</a></label>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="d-flex justify-content-end">
                                                            <button id="admission-details-btn"
                                                                class="btn btn-primary">SAVE</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingPopoutThree">
                                    <button type="button"
                                        class="accordion-button treatment-step {{ ((@$preauth_diagnosis->count() > 0) && (@$procedures->count() > 0) && $preauth_investigation_status && (@$preauth_teams->count() > 0))?'theme-color':'pending-color' }} collapsed"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#accordionPopoutThree"
                                        aria-expanded="false"
                                        aria-controls="accordionPopoutThree">
                                        Treatment
                                    </button>
                                </h2>
                                <div id="accordionPopoutThree"
                                    class="accordion-collapse collapse"
                                    aria-labelledby="headingPopoutThree"
                                    data-bs-parent="#accordionPopout">
                                    <div class="accordion-body">
                                        <div class="inside-left-info-box {{ (@$preauth_diagnosis->count() > 0) ?'success':'pending' }}">
                                            <h4 class="colored-verticle-title">Diagnosis</h4>
                                            <form onSubmit="return false" id="diagnosisForm">
                                                <div class="row g-3 spa-form-row">
                                                    <div class="col-md-6 col-lg-3">
                                                        <div
                                                            class="form-group">
                                                            @php $diagnosis_arr = \App\Models\PreauthDiagnosisMaster::orderBy('name')->get(); @endphp
                                                            <select class="form-control select2"
                                                                id="diagnosis_id"
                                                                name="diagnosis_id">
                                                                <option value="">Select Diagnosis</option>
                                                                @foreach($diagnosis_arr as $diagnosis)
                                                                    <option value="{{ $diagnosis->id }}">{{ $diagnosis->name }}</option>
                                                                @endforeach
                                                            </select>
                                                            <label for="diagnosis_id">Search
                                                                Diagnosis</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-lg-3 other-diagnosis-field d-none">
                                                        <div
                                                            class="form-group">
                                                            <input type="text" id="other_diagnosis" name="other_diagnosis" oninput="sanitize(this, 't');"
                                                                class="form-control" 
                                                                placeholder="" />
                                                            <label
                                                                for="other_diagnosis">Other Diagnosis <span class="text-danger">*</span></label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-lg-3">
                                                        <label for="BMI" class="form-label">Diagnosis
                                                            Type</label>
                                                        <div class="d-flex spa-radio-group">
                                                            <div class="form-check">
                                                                <input class="form-check-input"
                                                                    type="radio" name="diagnosis_type"
                                                                    id="Primary" value="Primary" checked>
                                                                <label class="form-check-label"
                                                                    for="Primary">
                                                                    Primary
                                                                </label>
                                                            </div>
                                                            <div class="form-check ms-4">
                                                                <input class="form-check-input"
                                                                    type="radio" name="diagnosis_type"
                                                                    id="Secondary" value="Secondary">
                                                                <label class="form-check-label"
                                                                    for="Secondary">
                                                                    Secondary
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-lg-3">
                                                        <button
                                                            class="btn btn-outline-info" id="diagnosis-btn">Add</button>
                                                    </div>
                                                </div>
                                            </form>
                                            <div class="row g-3 spa-form-row">
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
                                                                    <th>Actions</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody
                                                                class="table-border-bottom-0 diagnosis-body">
                                                                @include('hospital.scheme-preauth._partials.diagnosis')
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="inside-left-info-box {{ (@$procedures->count() > 0)?'success':'pending' }} mt-3">
                                            <form onSubmit="return false" id="procedureForm">
                                                <h4 class="colored-verticle-title">Treatment Plan
                                                </h4>
                                                <div class="row justify-content-center">
                                                    <div class="col-md-8 mb-4">
                                                        <div
                                                            class="form-group">
                                                            <select class="form-control select2" id="specialitys"
                                                                name="speciality_id">
                                                                <option value=""></option>
                                                                @foreach($hospital_speciality as $hospital_spe)
                                                                    <option value="{{ $hospital_spe->speciality_id }}">{{ $hospital_spe->name }}</option>
                                                                @endforeach
                                                                @if($us)
                                                                    <option value="{{ $us->id }}">{{ @$us->name }}</option>
                                                                @endif
                                                            </select>
                                                            <label
                                                                for="speciality_id">Speciality</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-8 mb-4">
                                                        <div
                                                            class="form-group">
                                                            <select class="form-control select2" id="procedure_id"
                                                                name="procedure_id">
                                                                <option value=""></option>
                                                            </select>
                                                            <label for="procedure_id">Procedure</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-8 mb-4 implant-field d-none">
                                                        <div
                                                            class="form-group">
                                                            <select class="form-control select2" id="implant_id"
                                                                name="implant_id">
                                                                <option value=""></option>
                                                            </select>
                                                            <label for="implant_id">Implant</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-8 mb-4 implant-field d-none">
                                                        <div
                                                            class="form-group">
                                                            <input type="text" id="implant_qty" name="implant_qty"
                                                                class="form-control"
                                                                placeholder="" readonly />
                                                            <label for="implant_qty">Quantity</label>
                                                        </div>
                                                        <div id="implant-qty-error"></div>
                                                    </div>
                                                    <div class="col-md-8 mb-4 stratification-field d-none">
                                                        <div
                                                            class="form-group">
                                                            <select class="form-control select2" id="stratification_id"
                                                                name="stratification_id">
                                                                <option value=""></option>
                                                            </select>
                                                            <label for="stratification_id">Stratification</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-8 mb-4">
                                                        <div
                                                            class="form-group">
                                                            <input type="text" id="no_of_days" name="no_of_days" oninput="sanitize(this, 'm','10');"
                                                                class="form-control"
                                                                placeholder="" />
                                                            <label for="no_of_days">No of
                                                                Days/Units</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-8 mb-4 u100-field d-none">
                                                        <div
                                                            class="form-group">
                                                            <input type="number" id="u100_amount" name="u100_amount" oninput="sanitize(this, 'n','10');"
                                                                class="form-control"
                                                                placeholder="" />
                                                            <label for="u100_amount">Unverfied Amount</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-8 mb-4">
                                                        <div
                                                            class="form-group">
                                                            <input type="text" id="icd_code" name="ichi"
                                                                class="form-control" readonly
                                                                placeholder="" />
                                                            <label for="icd_code">ICHI Code:</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 ">
                                                        <button id="procedure-btn"
                                                            class="btn btn-outline-info" disabled>Add</button>
                                                    </div>
                                                </div>
                                            </form>
                                            <div class="row justify-content-center">
                                                <div class="col-12">
                                                    <div
                                                        class="table-responsive mt-5 spa-procedures-table-wrap">
                                                        <table class="table table-sm spa-procedures-table">
                                                            <thead class="table-dark">
                                                                <tr>
                                                                    <th>No.</th>
                                                                    <th>Speciality</th>
                                                                    <th>Procedure</th>
                                                                    <th>Strat.</th>
                                                                    <th>Days</th>
                                                                    <th>Amount</th>
                                                                    <th>ICHI</th>
                                                                    <th></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody
                                                                class="table-border-bottom-0 procedure-body">
                                                                @include('hospital.scheme-preauth._partials.procedures')
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="inside-left-info-box {{ $preauth_investigation_status?'success':'pending' }} mt-3">
                                            <form onSubmit="return false" id="investigationForm">
                                                <h4 class="colored-verticle-title">Investigation
                                                </h4>
                                                <div class="row justify-content-center">
                                                    <div class="col-12">
                                                        <div
                                                            class="table-responsive mt-5 spa-investigations-table-wrap">
                                                            <table class="table table-sm spa-investigations-table">
                                                                <thead class="table-dark">
                                                                    <tr>
                                                                        <th>No.</th>
                                                                        <th>Investigation</th>
                                                                        <th>Document</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody
                                                                    class="table-border-bottom-0 investigation-body">
                                                                    @include('hospital.scheme-preauth._partials.investigations',['preauth_register_id'=>$preauthRegister->id])
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="d-flex justify-content-end">
                                                            <button id="investigation-btn"
                                                                class="btn btn-primary">SAVE</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="inside-left-info-box {{ @$preauth_teams->count() > 0?'success':'pending' }} mt-3">
                                            <form onSubmit="return false" id="careTeamForm">
                                                <h4 class="colored-verticle-title">Care Team Details
                                                </h4>
                                                <div class="row justify-content-center">
                                                    <div class="col-md-6 mb-4">
                                                        <div
                                                            class="form-group">
                                                            <select class="form-control select2" id="care_team_id"
                                                                name="care_team_id">
                                                                <option value="">Select Doctor</option>
                                                                @foreach($careTeamDoctors as $doctor)
                                                                    <option value="{{ $doctor->id }}">
                                                                        {{ trim($doctor->first_name . ' ' . $doctor->last_name) }}
                                                                        @if($doctor->specialist)
                                                                            ? {{ $doctor->specialist->name }}
                                                                        @endif
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            <label for="care_team_id">Select Doctor</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 mb-4">
                                                        <div class="d-flex flex-wrap">
                                                            <button id="care-team-btn"
                                                                class="btn btn-lg btn-outline-info me-3" {{ @$preauth_teams->count() > 0?'disabled':'' }}>ADD</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
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
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="table-border-bottom-0 care-team-body">
                                                            @include('hospital.scheme-preauth._partials.teams')
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingPopoutFour">
                                    <button type="button"
                                        class="accordion-button finance-color {{ (@$procedures->count() > 0)?'theme-color':'pending-color' }} collapsed"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#accordionPopoutFour"
                                        aria-expanded="false"
                                        aria-controls="accordionPopoutFour">
                                        Finance
                                    </button>
                                </h2>
                                <div id="accordionPopoutFour"
                                    class="accordion-collapse collapse"
                                    aria-labelledby="headingPopoutFour"
                                    data-bs-parent="#accordionPopout">
                                    <div class="accordion-body">
                                        <h4 class="theme-color">Amount and incentive Details
                                        </h4>
                                        <div class="row g-3 spa-form-row">
                                            <div class="col-12">
                                                <div class="table-responsive mt-5 text-nowrap">
                                                    <table class="table">
                                                        <thead class="table-dark">
                                                            <tr>
                                                                <th>No.</th>
                                                                <th>Package Code</th>
                                                                <th>Package Type</th>
                                                                <th>Stratification Cost</th>
                                                                <th>LOS/Quantity</th>
                                                                <th>Package Cost</th>
                                                                <th>Adj Factor</th>
                                                                <th>Incentive</th>
                                                                <th>Total Amount</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="table-border-bottom-0 finance-body">
                                                            @include('hospital.scheme-preauth._partials.finance')
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <ul class="d-flex listing-right finance-total-body">
                                                    @include('hospital.scheme-preauth._partials.finance-total')
                                                </ul>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end mt-3">
                            <button class="btn btn-primary btn-lg" id="preview">Validate & Preview</button>
                        </div>
                    </div>
                    <!-- Social Links -->
                    <div id="social-links" class="content">
                        <div class="row g-3 spa-form-row">
                            <div class="col-12 d-flex justify-content-between">
                                <button class="btn btn-outline-secondary btn-prev">
                                    <i class="ri-arrow-left-line me-sm-1 me-0"></i>
                                    <span
                                        class="align-middle d-sm-inline-block d-none">Previous</span>
                                </button>
                                <button class="btn btn-primary btn-submit">Submit</button>
                            </div>
                        </div>
                    </div>
                    <!-- Clamin Pending -->
                    <div id="claim-pending" class="content">
                        <div class="row g-3 spa-form-row">
                            <div class="col-12 d-flex justify-content-between">
                                <button class="btn btn-outline-secondary btn-prev">
                                    <i class="ri-arrow-left-line me-sm-1 me-0"></i>
                                    <span
                                        class="align-middle d-sm-inline-block d-none">Previous</span>
                                </button>
                                <button class="btn btn-primary btn-submit">Submit</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>
</div>


<div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header ">
                <h4 class="modal-title" id="previewModalLabel3">Preview</h4>
                <button type="button" class="btn-primary btn ms-4" id="print-form">Print Form</button>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body spa-print-target scheme-preauth-page" id="previewModalPrintArea">
                @include('hospital.scheme-preauth._partials.preview-beneficiary-bar')
                <div id="preview-data"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    Close
                </button>
                <button type="button" class="btn btn-primary" id="validate">Initiate Pre-Authorization</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('public/front/assets/js/sanitize.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script>
    if (typeof successMessage !== 'function') {
        function successMessage(msg) {
            if (typeof sendmsg === 'function') {
                sendmsg('success', msg);
            } else {
                alert(msg);
            }
        }
    }
    if (typeof errorMessage !== 'function') {
        function errorMessage(msg) {
            if (typeof sendmsg === 'function') {
                sendmsg('error', msg);
            } else {
                alert(msg);
            }
        }
    }
    var isRtl = false;
    (function () {
        document.addEventListener('click', function (event) {
            if (event.target.closest('.scheme-preauth-page .file-upload-wrapper')) {
                var fileWrapper = event.target.closest('.file-upload-wrapper');
                var fileInput = fileWrapper.parentElement.querySelector('.file-input');
                if (fileInput) {
                    fileInput.click();
                }
            }
            if (event.target.closest('.scheme-preauth-page .remove-file-btn')) {
                var section = event.target.closest('.file-upload-section');
                if (!section) {
                    return;
                }
                var fileInput = section.querySelector('.file-input');
                var uploadedFile = section.querySelector('.uploaded-file');
                var fileWrapper = section.querySelector('.file-upload-wrapper');
                if (fileInput) {
                    fileInput.value = '';
                }
                if (uploadedFile) {
                    uploadedFile.classList.add('d-none');
                }
                if (fileWrapper) {
                    fileWrapper.classList.remove('d-none');
                }
            }
        });
        document.addEventListener('change', function (event) {
            if (!event.target.classList.contains('file-input')) {
                return;
            }
            if (!event.target.closest('.scheme-preauth-page')) {
                return;
            }
            var section = event.target.closest('.file-upload-section');
            if (!section) {
                return;
            }
            var fileWrapper = section.querySelector('.file-upload-wrapper');
            var uploadedFile = section.querySelector('.uploaded-file');
            var fileName = section.querySelector('.file-name');
            var file = event.target.files[0];
            if (file && fileName && uploadedFile && fileWrapper) {
                fileName.textContent = file.name;
                uploadedFile.classList.remove('d-none');
                fileWrapper.classList.add('d-none');
            }
        });
    })();

    $(document).ready(function () {
        let registrationDate = moment("{{ @$preauthRegister->created_at }}", "YYYY-MM-DD");
        let minAllowedDate = registrationDate.clone().subtract(2, 'days');
        let minAllowedSurgeryDate = registrationDate.clone();
        let maxAllowedDate = moment();

        $('#admission-date').daterangepicker({
            singleDatePicker: true,
            locale: {
                format: 'YYYY-MM-DD'
            },
            minDate: minAllowedDate,
            maxDate: maxAllowedDate,
            opens: isRtl ? 'left' : 'right'
        }, function(start) {
            // Update minDate for Surgery Date Picker
            let surgeryPicker = $('#surgery-date').data('daterangepicker');
            if (surgeryPicker) {
                let newMinDate = moment(start, 'YYYY-MM-DD'); // Ensure it's a Moment.js object
                surgeryPicker.setStartDate(newMinDate);
                surgeryPicker.minDate = newMinDate; // Set as Moment object
                surgeryPicker.updateView();
            }
        });
        $('#surgery-date').daterangepicker({
            singleDatePicker: true,
            locale: {
                format: 'YYYY-MM-DD'
            },
            minDate: minAllowedSurgeryDate,
            opens: isRtl ? 'left' : 'right'
        });
    });
    $("input[name='legal_case']").on("change",function(){
        if($("input[name='legal_case']:checked").val() == 'Yes'){
            $(".legal-field").removeClass('d-none');
        }else{
            $(".legal-field").addClass('d-none');
        }
    })
    
    $("input[name='known_allergies']").on("change",function(){
        if($("input[name='known_allergies']:checked").val() == 'Yes'){
            $(".allergy-detail-field").removeClass('d-none');
        }else{
            $(".allergy-detail-field").addClass('d-none');
        }
    })
    $("input[name='habits']").on("change",function(){
        if($("input[name='habits']:checked").val() == 'Yes'){
            $(".habits-detail-field").removeClass('d-none');
        }else{
            $(".habits-detail-field").addClass('d-none');
        }
    })
    $("#general-info-btn").on("click",function(){
        
        $(".loader-overlay").show();
        var formData = new FormData($('#generalInformationForm')[0]);
        
        $('.error').remove();
        $.ajax({
            url: '{{route("hospital.patient-management.scheme-preauth.general-information.store")}}',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                $(".loader-overlay").hide();
                successMessage(response.message);
                fillCompleteStep(response.steps);
                // $("#generalInformationForm").find("input").attr('disabled',true);
                $("#generalInformationForm").closest('.inside-left-info-box').removeClass("pending");
                $("#generalInformationForm").closest('.inside-left-info-box').addClass("success");
            },
            error: function (xhr) {
                $(".loader-overlay").hide();
                $('.error').remove();
                
                if (xhr.status === 422) { 
                    let errors = xhr.responseJSON.errors;
                    let errorMessages = [];
                    for (let field in errors) {
                        $(`[name="${field}"]`).after(`<div class="error text-danger">${errors[field][0]}</div>`);
                        errorMessages.push(errors[field][0]);
                    }
                    if (errorMessages.length > 0) {
                        errorMessage(errorMessages.join('<br>'));
                    }
                } else {
                    errorMessage('Something went wrong. Please try again later.');
                }
            }
        });
    })
    $("#family-history-btn").on("click",function(){
        
        var formData = new FormData($('#familyHistoryForm')[0]);
        
        $(".loader-overlay").show();
        $('.error').remove();
        $.ajax({
            url: '{{route("hospital.patient-management.scheme-preauth.family-history.store")}}',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                $(".loader-overlay").hide();
                successMessage(response.message);
                fillCompleteStep(response.steps);
                // $("#familyHistoryForm").find("input,select").attr('disabled',true);
                $("#familyHistoryForm").closest('.inside-left-info-box').removeClass("pending");
                $("#familyHistoryForm").closest('.inside-left-info-box').addClass("success");
            },
            error: function (xhr) {
                $(".loader-overlay").hide();
                $('.error').remove();
                
                if (xhr.status === 422) { 
                    let errors = xhr.responseJSON.errors;
                    let errorMessages = [];
                    for (let field in errors) {
                        if($(`select[name="${field}"]`).length > 0){
                            $(`[name="${field}"]`).parent().append(`<div class="error text-danger">${errors[field][0]}</div>`);
                        }else{
                            $(`[name="${field}"]`).after(`<div class="error text-danger">${errors[field][0]}</div>`);
                        }
                        errorMessages.push(errors[field][0]);
                    }
                    if (errorMessages.length > 0) {
                        errorMessage(errorMessages.join('<br>'));
                    }
                } else {
                    errorMessage('Something went wrong. Please try again later.');
                }
            }
        });
    })
    $("#personal-history-btn").on("click",function(){
        
        var formData = new FormData($('#personalHistoryForm')[0]);
        
        $(".loader-overlay").show();
        $('.error').remove();
        $.ajax({
            url: '{{route("hospital.patient-management.scheme-preauth.personal-history.store")}}',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                $(".loader-overlay").hide();
                successMessage(response.message);
                fillCompleteStep(response.steps);
                // $("#personalHistoryForm").find("input,select").attr('disabled',true);
                $("#personalHistoryForm").closest('.inside-left-info-box').removeClass("pending");
                $("#personalHistoryForm").closest('.inside-left-info-box').addClass("success");
            },
            error: function (xhr) {
                $(".loader-overlay").hide();
                $('.error').remove();
                
                if (xhr.status === 422) { 
                    let errors = xhr.responseJSON.errors;
                    let errorMessages = [];
                    for (let field in errors) {
                        if($(`select[name="${field}"]`).length > 0){
                            $(`[name="${field}"]`).parent().append(`<div class="error text-danger">${errors[field][0]}</div>`);
                        }else{
                            $(`[name="${field}"]`).after(`<div class="error text-danger">${errors[field][0]}</div>`);
                        }
                        errorMessages.push(errors[field][0]);
                    }
                    if (errorMessages.length > 0) {
                        errorMessage(errorMessages.join('<br>'));
                    }
                } else {
                    errorMessage('Something went wrong. Please try again later.');
                }
            }
        });
    })
    $("#authentication-consent-btn").on("click",function(){
        
        var formData = new FormData($('#authenticationConsentForm')[0]);
        
        $(".loader-overlay").show();
        $('.error').remove();
        $.ajax({
            url: '{{route("hospital.patient-management.scheme-preauth.authentication-consent.store")}}',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                $(".loader-overlay").hide();
                successMessage(response.message);
                fillCompleteStep(response.steps);
                // $("#authenticationConsentForm").find("input,select").attr('disabled',true);
                $("#authenticationConsentForm").closest('.inside-left-info-box').removeClass("pending");
                $("#authenticationConsentForm").closest('.inside-left-info-box').addClass("success");
            },
            error: function (xhr) {
                $(".loader-overlay").hide();
                $('.error').remove();
                
                if (xhr.status === 422) { 
                    let errors = xhr.responseJSON.errors;
                    let errorMessages = [];
                    for (let field in errors) {
                        if($(`select[name="${field}"]`).length > 0){
                            $(`[name="${field}"]`).parent().append(`<div class="error text-danger">${errors[field][0]}</div>`);
                        }else{
                            $(`[name="${field}"]`).after(`<div class="error text-danger">${errors[field][0]}</div>`);
                        }
                        errorMessages.push(errors[field][0]);
                    }
                    if (errorMessages.length > 0) {
                        errorMessage(errorMessages.join('<br>'));
                    }
                } else {
                    errorMessage('Something went wrong. Please try again later.');
                }
            }
        });
    })
    $("#admission-details-btn").on("click",function(){
        
        var formData = new FormData($('#admissionDetailsForm')[0]);
        
        $(".loader-overlay").show();
        $('.error').remove();
        $.ajax({
            url: '{{route("hospital.patient-management.scheme-preauth.admission-details.store")}}',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                $(".loader-overlay").hide();
                successMessage(response.message);
                fillCompleteStep(response.steps);
                // $("#admissionDetailsForm").find("input,select").attr('disabled',true);
                $("#admissionDetailsForm").closest('.inside-left-info-box').removeClass("pending");
                $("#admissionDetailsForm").closest('.inside-left-info-box').addClass("success");
            },
            error: function (xhr) {
                $(".loader-overlay").hide();
                $('.error').remove();
                
                if (xhr.status === 422) { 
                    let errors = xhr.responseJSON.errors;
                    let errorMessages = [];
                    for (let field in errors) {
                        if($(`select[name="${field}"]`).length > 0){
                            $(`[name="${field}"]`).parent().append(`<div class="error text-danger">${errors[field][0]}</div>`);
                        }else{
                            $(`[name="${field}"]`).after(`<div class="error text-danger">${errors[field][0]}</div>`);
                        }
                        errorMessages.push(errors[field][0]);
                    }
                    if (errorMessages.length > 0) {
                        errorMessage(errorMessages.join('<br>'));
                    }
                } else {
                    errorMessage('Something went wrong. Please try again later.');
                }
            }
        });
    })
    $("#diagnosis_id").on("change",function(){
        if($("#diagnosis_id option:selected").html() == 'Other'){
            $(".other-diagnosis-field").removeClass("d-none");
        }else{
            $(".other-diagnosis-field").addClass("d-none");
        }
    });
    $("#diagnosis-btn").on("click",function(){
        
        var formData = new FormData($('#diagnosisForm')[0]);
        
        $(".loader-overlay").show();
        $('.error').remove();
        $.ajax({
            url: '{{route("hospital.patient-management.scheme-preauth.diagnosis.store")}}',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                $(".loader-overlay").hide();
                successMessage(response.message);
                fillCompleteStep(response.steps);
                $("#diagnosis_id").val("");
                $(".diagnosis-body").html(response.html);
                $("#diagnosisForm").closest('.inside-left-info-box').removeClass("pending");
                $("#diagnosisForm").closest('.inside-left-info-box').addClass("success");
            },
            error: function (xhr) {
                $(".loader-overlay").hide();
                $('.error').remove();
                
                if (xhr.status === 422) { 
                    let errors = xhr.responseJSON.errors;
                    let errorMessages = [];
                    for (let field in errors) {
                        if($(`select[name="${field}"]`).length > 0){
                            $(`[name="${field}"]`).parent().append(`<div class="error text-danger">${errors[field][0]}</div>`);
                        }else{
                            $(`[name="${field}"]`).after(`<div class="error text-danger">${errors[field][0]}</div>`);
                        }
                        errorMessages.push(errors[field][0]);
                    }
                    if (errorMessages.length > 0) {
                        errorMessage(errorMessages.join('<br>'));
                    }
                } else {
                    errorMessage('Something went wrong. Please try again later.');
                }
            }
        });
    })
    function deleteDiagnosis(id){
        
        
        $(".loader-overlay").show();
        $('.error').remove();
        $.ajax({
            url: '{{route("hospital.patient-management.scheme-preauth.diagnosis.destroy")}}',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            type: 'POST',
            data: {id:id},
            success: function (response) {
                $(".loader-overlay").hide();
                successMessage(response.message);
                fillCompleteStep(response.steps);
                $(".diagnosis-body").html(response.html);
            },
            error: function (xhr) {
                $(".loader-overlay").hide();
                $('.error').remove();
                
                if (xhr.status === 422) { 
                    let errors = xhr.responseJSON.errors;
                    let errorMessages = [];
                    for (let field in errors) {
                        if($(`select[name="${field}"]`).length > 0){
                            $(`[name="${field}"]`).parent().append(`<div class="error text-danger">${errors[field][0]}</div>`);
                        }else{
                            $(`[name="${field}"]`).after(`<div class="error text-danger">${errors[field][0]}</div>`);
                        }
                        errorMessages.push(errors[field][0]);
                    }
                    if (errorMessages.length > 0) {
                        errorMessage(errorMessages.join('<br>'));
                    }
                } else {
                    errorMessage('Something went wrong. Please try again later.');
                }
            }
        });
    }
    function spaRefreshSelect2($el) {
        if ($el.hasClass('select2-hidden-accessible')) {
            $el.select2('destroy');
        }
        $el.select2({ width: '100%' });
    }

    $(document).on('change select2:select', '#specialitys', function () {
        var id = $(this).val();
        $(".loader-overlay").show();
        if(id != ''){
            $.ajax({
                url: '{{route("hospital.patient-management.scheme-preauth.get-procedures")}}',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                type: 'POST',
                data: {id:id},
                success: function (response) {
                    $(".loader-overlay").hide();
                    var $procedure = $("#procedure_id");
                    if ($procedure.hasClass('select2-hidden-accessible')) {
                        $procedure.select2('destroy');
                    }
                    $procedure.html(response.html || '<option value="">Select Procedure</option>');
                    spaRefreshSelect2($procedure);
                    $procedure.val('').trigger('change');
                    $("#procedure-btn").attr('disabled', true);
                },
                error: function (xhr) {
                    $(".loader-overlay").hide();
                    $('.error').remove();
                    
                    if (xhr.status === 422) { 
                        let errors = xhr.responseJSON.errors;
                        let errorMessages = [];
                        for (let field in errors) {
                            if($(`select[name="${field}"]`).length > 0){
                                $(`[name="${field}"]`).parent().append(`<div class="error text-danger">${errors[field][0]}</div>`);
                            }else{
                                $(`[name="${field}"]`).after(`<div class="error text-danger">${errors[field][0]}</div>`);
                            }
                            errorMessages.push(errors[field][0]);
                        }
                        if (errorMessages.length > 0) {
                            errorMessage(errorMessages.join('<br>'));
                        }
                    } else {
                        errorMessage('Something went wrong. Please try again later.');
                    }
                }
            });
        }else{
            $(".loader-overlay").hide();
            var $procedure = $("#procedure_id");
            if ($procedure.hasClass('select2-hidden-accessible')) {
                $procedure.select2('destroy');
            }
            $procedure.html('<option value="">Select Procedure</option>');
            spaRefreshSelect2($procedure);
            $("#procedure-btn").attr('disabled',true);
        }
    });

    $("#procedure_id").on("change",function(){
        var id = $(this).val();
        $(".loader-overlay").show();
        if(id != ''){
            $.ajax({
                url: '{{route("hospital.patient-management.scheme-preauth.get-procedure-details")}}',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                type: 'POST',
                data: {id:id},
                success: function (response) {
                    $(".loader-overlay").hide();
                    $("#icd_code").val(response.icd_code);
                    $("#no_of_days").val(response.no_of_days);
                    if(response.is_read_only == true){
                        $("#no_of_days").attr("readonly",true);
                    }else{
                        $("#no_of_days").attr("readonly",false);
                    }
                    if(response.price != 0 || response.usp == true){
                        $("#procedure-btn").attr('disabled',false);
                    }else{
                        $("#procedure-btn").attr('disabled',true);
                    }
                    if(response.usp == true){
                        $(".u100-field").removeClass('d-none');
                    }else{
                        $(".u100-field").addClass('d-none');
                    }
                    if(response.is_stratification){
                        $("#stratification_id").html(response.stratification_options);
                        $(".stratification-field").removeClass("d-none");
                    }else{
                        $("#stratification_id").html("");
                        $(".stratification-field").addClass("d-none");
                    }
                    if(response.is_implant){
                        $("#implant_id").html(response.implants_options);
                        $(".implant-field").removeClass("d-none");
                    }else{
                        $("#implant_id").html("");
                        $(".implant-field").addClass("d-none");
                    }
                },
                error: function (xhr) {
                    $(".loader-overlay").hide();
                    $('.error').remove();
                    errorMessage('Something went wrong. Please try again later.');
                }
            });
        }else{
            $(".loader-overlay").hide();
            $("#stratification_id").html("");
            $(".stratification-field").addClass("d-none");
            $("#implant_id").html("");
            $(".implant-field").addClass("d-none");
            $("#icd_code").val("");
            $("#no_of_days").val("");
            $(".u100-field").addClass("d-none");
            $("#u100_amount").val("");
            $("#procedure-btn").attr('disabled',true);
        }
    })
    $("#implant_id").on("change",function(){
        var id = $(this).val();
        $(".loader-overlay").show();
        if(id != ''){
            $.ajax({
                url: '{{route("hospital.patient-management.scheme-preauth.get-implant-details")}}',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                type: 'POST',
                data: {id:id},
                success: function (response) {
                    $(".loader-overlay").hide();
                    $("#implant_qty").val(response.qty);
                    $("#implant_qty").attr("max",response.max);
                    if(response.is_read_only == true){
                        $("#implant_qty").attr("readonly",true);
                    }else{
                        $("#implant_qty").attr("readonly",false);
                    }
                    if(response.price != 0){
                        $("#procedure-btn").attr('disabled',false);
                    }
                },
                error: function (xhr) {
                    $(".loader-overlay").hide();
                    $('.error').remove();
                    errorMessage('Something went wrong. Please try again later.');
                }
            });
        }else{
            $(".loader-overlay").hide();
            $("#implant_qty").val("");
            $("#implant_qty").attr("readonly",true);
            $("#procedure-btn").attr('disabled',false);
        }
    })
    $("#implant_qty").on("input", function () {
        let qty = $(this).val();
        let max =$(this).attr("max");

        console.log(qty);
        console.log(max);
        if (!isNaN(qty) && !isNaN(max) && qty > max) {
            $("#implant-qty-error").html("<span class='text-danger'>You cannot add more than " + max + " qty.</span>");
            $("#procedure-btn").attr('disabled',true);
        } else {
            $("#implant-qty-error").html("");
            if(qty != ''){
                $("#procedure-btn").attr('disabled',false);
            }else{
                $("#procedure-btn").attr('disabled',true);
            }
        }
    });
    $("#stratification_id").on("change",function(){
        var id = $(this).val();
        $(".loader-overlay").show();
        if(id != ''){
            $.ajax({
                url: '{{route("hospital.patient-management.scheme-preauth.get-stratification-details")}}',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                type: 'POST',
                data: {id:id},
                success: function (response) {
                    $(".loader-overlay").hide();
                    if(response.price != 0){
                        $("#procedure-btn").attr('disabled',false);
                    }
                },
                error: function (xhr) {
                    $(".loader-overlay").hide();
                    $('.error').remove();
                    errorMessage('Something went wrong. Please try again later.');
                }
            });
        }else{
            $(".loader-overlay").hide();
        }
    })
    $("#procedure-btn").on("click",function(){
        
        var formData = new FormData($('#procedureForm')[0]);
        
        $(".loader-overlay").show();
        $('.error').remove();
        $.ajax({
            url: '{{route("hospital.patient-management.scheme-preauth.procedure.store")}}',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                $(".loader-overlay").hide();
                successMessage(response.message);
                fillCompleteStep(response.steps);
                $("#specialitys").val("").change();
                $("#procedure_id").html('<option value=""></option>');
                $("#procedure_id").val("").change();
                $("#no_of_days").val("");
                $("#u100_amount").val("");
                $(".procedure-body").html(response.html);
                $(".investigation-body").html(response.investigation_html);
                if(response.preauth_investigation_status){
                    $("#investigationForm").closest('.inside-left-info-box').removeClass("pending");
                    $("#investigationForm").closest('.inside-left-info-box').addClass("success");
                }else{
                    $("#investigationForm").closest('.inside-left-info-box').addClass("pending");
                    $("#investigationForm").closest('.inside-left-info-box').removeClass("success");
                }
                $(".finance-body").html(response.finance_html);
                $(".finance-total-body").html(response.finance_total_html);
                $("#procedureForm").closest('.inside-left-info-box').removeClass("pending");
                $("#procedureForm").closest('.inside-left-info-box').addClass("success");
                $(".finance-color").removeClass('pending-color');
                $(".finance-color").addClass('theme-color');
            },
            error: function (xhr) {
                $(".loader-overlay").hide();
                $('.error').remove();
                
                if (xhr.status === 422) { 
                    let errors = xhr.responseJSON.errors;
                    let errorMessages = [];
                    for (let field in errors) {
                        if($(`select[name="${field}"]`).length > 0){
                            $(`[name="${field}"]`).parent().append(`<div class="error text-danger">${errors[field][0]}</div>`);
                        }else{
                            $(`[name="${field}"]`).after(`<div class="error text-danger">${errors[field][0]}</div>`);
                        }
                        errorMessages.push(errors[field][0]);
                    }
                    if (errorMessages.length > 0) {
                        errorMessage(errorMessages.join('<br>'));
                    }
                } else {
                    errorMessage('Something went wrong. Please try again later.');
                }
            }
        });
    })
    function deleteProcedure(id){
        
        
        $(".loader-overlay").show();
        $('.error').remove();
        $.ajax({
            url: '{{route("hospital.patient-management.scheme-preauth.procedure.destroy")}}',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            type: 'POST',
            data: {id:id},
            success: function (response) {
                $(".loader-overlay").hide();
                successMessage(response.message);
                fillCompleteStep(response.steps);
                $(".procedure-body").html(response.html);
                if(response.html==''){
                    $("#procedureForm").closest('.inside-left-info-box').removeClass("success");
                    $("#procedureForm").closest('.inside-left-info-box').addClass("pending");
                    $(".finance-color").removeClass('theme-color');
                    $(".finance-color").addClass('pending-color');
                }
                $(".finance-body").html(response.finance_html);
                $(".investigation-body").html(response.investigation_html);
                if(response.preauth_investigation_status){
                    $("#investigationForm").closest('.inside-left-info-box').removeClass("pending");
                    $("#investigationForm").closest('.inside-left-info-box').addClass("success");
                }else{
                    $("#investigationForm").closest('.inside-left-info-box').addClass("pending");
                    $("#investigationForm").closest('.inside-left-info-box').removeClass("success");
                }
                $(".finance-total-body").html(response.finance_total_html);
            },
            error: function (xhr) {
                $(".loader-overlay").hide();
                $('.error').remove();
                
                if (xhr.status === 422) { 
                    let errors = xhr.responseJSON.errors;
                    let errorMessages = [];
                    for (let field in errors) {
                        if($(`select[name="${field}"]`).length > 0){
                            $(`[name="${field}"]`).parent().append(`<div class="error text-danger">${errors[field][0]}</div>`);
                        }else{
                            $(`[name="${field}"]`).after(`<div class="error text-danger">${errors[field][0]}</div>`);
                        }
                        errorMessages.push(errors[field][0]);
                    }
                    if (errorMessages.length > 0) {
                        errorMessage(errorMessages.join('<br>'));
                    }
                } else {
                    errorMessage('Something went wrong. Please try again later.');
                }
            }
        });
    }
    function deleteImplant(id){
        
        $(".loader-overlay").show();
        $('.error').remove();
        $.ajax({
            url: '{{route("hospital.patient-management.scheme-preauth.procedure.delete-implant")}}',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            type: 'POST',
            data: {id:id},
            success: function (response) {
                $(".loader-overlay").hide();
                successMessage(response.message);
                fillCompleteStep(response.steps);
                $(".procedure-body").html(response.html);
                if(response.html==''){
                    $("#procedureForm").closest('.inside-left-info-box').removeClass("success");
                    $("#procedureForm").closest('.inside-left-info-box').addClass("pending");
                    $(".finance-color").removeClass('theme-color');
                    $(".finance-color").addClass('pending-color');
                }
                $(".finance-body").html(response.finance_html);
                $(".investigation-body").html(response.investigation_html);
                if(response.preauth_investigation_status){
                    $("#investigationForm").closest('.inside-left-info-box').removeClass("pending");
                    $("#investigationForm").closest('.inside-left-info-box').addClass("success");
                }else{
                    $("#investigationForm").closest('.inside-left-info-box').addClass("pending");
                    $("#investigationForm").closest('.inside-left-info-box').removeClass("success");
                }
                $(".finance-total-body").html(response.finance_total_html);
            },
            error: function (xhr) {
                $(".loader-overlay").hide();
                $('.error').remove();
                
                if (xhr.status === 422) { 
                    let errors = xhr.responseJSON.errors;
                    let errorMessages = [];
                    for (let field in errors) {
                        if($(`select[name="${field}"]`).length > 0){
                            $(`[name="${field}"]`).parent().append(`<div class="error text-danger">${errors[field][0]}</div>`);
                        }else{
                            $(`[name="${field}"]`).after(`<div class="error text-danger">${errors[field][0]}</div>`);
                        }
                        errorMessages.push(errors[field][0]);
                    }
                    if (errorMessages.length > 0) {
                        errorMessage(errorMessages.join('<br>'));
                    }
                } else {
                    errorMessage('Something went wrong. Please try again later.');
                }
            }
        });
    }
    $("#care-team-btn").on("click",function(){
        
        var formData = new FormData($('#careTeamForm')[0]);
        
        $(".loader-overlay").show();
        $('.error').remove();
        $.ajax({
            url: '{{route("hospital.patient-management.scheme-preauth.care-team.store")}}',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                $(".loader-overlay").hide();
                if(response.success){
                    successMessage(response.message);
                    fillCompleteStep(response.steps);
                    $("#care_team_id").val("").change();
                    $(".care-team-body").html(response.html);
                    $("#care-team-btn").attr('disabled',true);
                    $("#careTeamForm").closest('.inside-left-info-box').removeClass("pending");
                    $("#careTeamForm").closest('.inside-left-info-box').addClass("success");
                }else{
                    errorMessage(response.message);
                }
            },
            error: function (xhr) {
                $(".loader-overlay").hide();
                $('.error').remove();
                
                if (xhr.status === 422) { 
                    let errors = xhr.responseJSON.errors;
                    let errorMessages = [];
                    for (let field in errors) {
                        if($(`select[name="${field}"]`).length > 0){
                            $(`[name="${field}"]`).parent().append(`<div class="error text-danger">${errors[field][0]}</div>`);
                        }else{
                            $(`[name="${field}"]`).after(`<div class="error text-danger">${errors[field][0]}</div>`);
                        }
                        errorMessages.push(errors[field][0]);
                    }
                    if (errorMessages.length > 0) {
                        errorMessage(errorMessages.join('<br>'));
                    }
                } else {
                    errorMessage('Something went wrong. Please try again later.');
                }
            }
        });
    })
    function deleteTeam(id){
        
        
        $(".loader-overlay").show();
        $('.error').remove();
        $.ajax({
            url: '{{route("hospital.patient-management.scheme-preauth.care-team.destroy")}}',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            type: 'POST',
            data: {id:id},
            success: function (response) {
                $(".loader-overlay").hide();
                successMessage(response.message);
                fillCompleteStep(response.steps);
                $("#care-team-btn").attr('disabled',false);
                $(".care-team-body").html(response.html);
            },
            error: function (xhr) {
                $(".loader-overlay").hide();
                $('.error').remove();
                
                if (xhr.status === 422) { 
                    let errors = xhr.responseJSON.errors;
                    let errorMessages = [];
                    for (let field in errors) {
                        if($(`select[name="${field}"]`).length > 0){
                            $(`[name="${field}"]`).parent().append(`<div class="error text-danger">${errors[field][0]}</div>`);
                        }else{
                            $(`[name="${field}"]`).after(`<div class="error text-danger">${errors[field][0]}</div>`);
                        }
                        errorMessages.push(errors[field][0]);
                    }
                    if (errorMessages.length > 0) {
                        errorMessage(errorMessages.join('<br>'));
                    }
                } else {
                    errorMessage('Something went wrong. Please try again later.');
                }
            }
        });
    }
    $("#investigation-btn").on("click",function(){
        
        var formData = new FormData($('#investigationForm')[0]);
        
        $(".loader-overlay").show();
        $('.error').remove();
        $.ajax({
            url: '{{route("hospital.patient-management.scheme-preauth.investigation.store")}}',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                $(".loader-overlay").hide();
                successMessage(response.message);
                fillCompleteStep(response.steps);
                $(".investigation-body").html(response.investigation_html);
                if(response.preauth_investigation_status){
                    $("#investigationForm").closest('.inside-left-info-box').removeClass("pending");
                    $("#investigationForm").closest('.inside-left-info-box').addClass("success");
                }else{
                    $("#investigationForm").closest('.inside-left-info-box').addClass("pending");
                    $("#investigationForm").closest('.inside-left-info-box').removeClass("success");
                }
                $("#investigationForm").closest('.inside-left-info-box').removeClass("pending");
                $("#investigationForm").closest('.inside-left-info-box').addClass("success");
            },
            error: function (xhr) {
                $(".loader-overlay").hide();
                $('.error').remove();
                
                if (xhr.status === 422) { 
                    let errors = xhr.responseJSON.errors;
                    let errorMessages = [];
                    for (let field in errors) {
                        if($(`select[name="${field}"]`).length > 0){
                            $(`[name="${field}"]`).parent().append(`<div class="error text-danger">${errors[field][0]}</div>`);
                        }else{
                            $(`[name="${field}"]`).after(`<div class="error text-danger">${errors[field][0]}</div>`);
                        }
                        errorMessages.push(errors[field][0]);
                    }
                    if (errorMessages.length > 0) {
                        errorMessage(errorMessages.join('<br>'));
                    }
                } else {
                    errorMessage('Something went wrong. Please try again later.');
                }
            }
        });
    })
    $("#preview").on("click",function(){
        $(".loader-overlay").show();
        var validateFd = new FormData();
        $.ajax({
            url: '{{route("hospital.patient-management.scheme-preauth.validate-form")}}',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            type: 'POST',
            data: validateFd,
            processData: false,
            contentType: false,
            success: function (response) {
                $(".loader-overlay").hide();
                if(response.validate){
                    $("#previewModal").modal("show");
                    $("#preview-data").html(response.html);
                }else{
                    errorMessage(response.message);
                }
            },
            error: function (xhr) {
                $(".loader-overlay").hide();
                $('.error').remove();
                
                if (xhr.status === 422) { 
                    let errors = xhr.responseJSON.errors;
                    let errorMessages = [];
                    for (let field in errors) {
                        if($(`select[name="${field}"]`).length > 0){
                            $(`[name="${field}"]`).parent().append(`<div class="error text-danger">${errors[field][0]}</div>`);
                        }else{
                            $(`[name="${field}"]`).after(`<div class="error text-danger">${errors[field][0]}</div>`);
                        }
                        errorMessages.push(errors[field][0]);
                    }
                    if (errorMessages.length > 0) {
                        errorMessage(errorMessages.join('<br>'));
                    }
                } else {
                    errorMessage('Something went wrong. Please try again later.');
                }
            }
        });
    })
    $("#validate").on("click",function(){
        Swal.fire({
            title: "Are you sure?",
            text: 'Initiate Preauthorization Request. ',
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes!",
            cancelButtonText: "No, cancel!",
            reverseButtons: true,
            buttonsStyling: false,
            customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-danger'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $(".loader-overlay").show();
                var submitFd = new FormData();
                $.ajax({
                    url: '{{route("hospital.patient-management.scheme-preauth.request-form-sumbit")}}',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    type: 'POST',
                    data: submitFd,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        $(".loader-overlay").hide();
                        if(response.success){
                            successMessage(response.message);
                            Swal.fire({
                                title: response.message,
                                text: ' Case Number: '+response.case_id,
                                icon: 'success',
                                customClass: {
                                confirmButton: 'btn btn-primary waves-effect waves-light'
                                },
                                buttonsStyling: false
                            }).then(() => {
                                window.location.href = '{{ $schemePreauthAfterSubmitUrl }}';
                            });
                        }else{
                            errorMessage(response.message);
                        }
                    },
                    error: function (xhr) {
                        $(".loader-overlay").hide();
                        $('.error').remove();
                        errorMessage('Something went wrong. Please try again later.');
                    }
                });
            }
        });
    })
    function spaPrintPreauthPreview() {
        var printArea = document.getElementById('previewModalPrintArea');
        if (!printArea || !printArea.innerText.trim()) {
            errorMessage('Open preview and wait for content to load.');
            return;
        }
        var cssHref = '{{ asset('public/front/assets/css/scheme-preauth.css') }}?v=11';
        var bsHref = document.querySelector('link[href*="bootstrap"]')?.href || '';
        var iframe = document.createElement('iframe');
        iframe.setAttribute('aria-hidden', 'true');
        iframe.style.cssText = 'position:fixed;right:0;bottom:0;width:0;height:0;border:0';
        document.body.appendChild(iframe);
        var doc = iframe.contentWindow.document;
        doc.open();
        var headLinks = '<link rel="stylesheet" href="' + cssHref + '">';
        if (bsHref) {
            headLinks += '<link rel="stylesheet" href="' + bsHref + '">';
        }
        doc.write('<!DOCTYPE html><html><head><meta charset="utf-8"><title>Pre-Authorization</title>' +
            headLinks +
            '<style>body{padding:16px;font-family:Inter,sans-serif;font-size:12px;color:#111}@media print{body{padding:8px}}</style>' +
            '</head><body class="scheme-preauth-page">' + printArea.innerHTML + '</body></html>');
        doc.close();
        var win = iframe.contentWindow;
        var doPrint = function () {
            win.focus();
            win.print();
            setTimeout(function () { iframe.remove(); }, 2000);
        };
        if (win.document.readyState === 'complete') {
            setTimeout(doPrint, 300);
        } else {
            iframe.onload = function () { setTimeout(doPrint, 300); };
        }
    }
    $("#print-form").on("click", spaPrintPreauthPreview);

    $("#weight,#height").on("change",function(){
        calculateBMI();
    })
    function calculateBMI(){
        var weight = parseFloat($("#weight").val());
        var heightCm = parseFloat($("#height").val());
        var heightMeters = heightCm * 0.01;

        if (weight > 0 && heightMeters > 0) {
            var bmi = (weight / (heightMeters * heightMeters)).toFixed(2);

            $("#bmi").val(bmi);
            
        }
    }
    function fillCompleteStep(steps){
        if(steps['medical']){
            $(".medical-info-step").removeClass('pending-color');
            $(".medical-info-step").addClass('theme-color');
        }else{
            $(".medical-info-step").removeClass('theme-color');
            $(".medical-info-step").addClass('pending-color');
        }
        if(steps['admission']){
            $(".admission-info-step").removeClass('pending-color');
            $(".admission-info-step").addClass('theme-color');
        }else{
            $(".admission-info-step").removeClass('theme-color');
            $(".admission-info-step").addClass('pending-color');
        }
        if(steps['treatment']){
            $(".treatment-step").removeClass('pending-color');
            $(".treatment-step").addClass('theme-color');
        }else{
            $(".treatment-step").removeClass('theme-color');
            $(".treatment-step").addClass('pending-color');
        }
    }
</script>
@endpush
