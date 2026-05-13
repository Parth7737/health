@extends('layouts.preauth.app')
@section('title','Pre-Authorization')
@section('content')

<div class="container-xxl flex-grow-1 container-p-y mb-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="javascript:void(0);">Home</a>
            </li>
            <li class="breadcrumb-item active">Pre Authorization ({{ $preauth_register->register_id }})</li>
        </ol>
    </nav>
    <div class="row">
        <div class="bs-stepper-content">
            <div class="card mb-6 ps-0 border border-primary">
                <div class="card-body">
                    <div class="row row-cols-5">
                        <div class="col">
                            <div
                                class="d-flex text-center justify-content-center flex-column border-end border-secondary">
                                <div class="position-relative image-overlay">
                                    <img src="{{ $preauth_register->benificiary->image_url }}" width="80" alt="avatar"
                                        class="mb-3 rounded-circle" />
                                </div>
                                <span class="number-3 mb-2">{{ @$preauth_register->benificiary->name }}</span>
                                <span class="number-2 mb-2">{{ @$preauth_register->benificiary->age }} Yr / {{ @$preauth_register->benificiary->gender }}</span>
                                @if(@$preauth_register->is_new_born_baby == 1)
                                    <strong><span class="number-3 mt-2">New Born Baby</span></strong>
                                    <span>{{ $preauth_register->born_baby_name }}</span>
                                    <span class="number-2 mb-2">DOB : {{ @$preauth_register->born_baby_dob }}  / {{ @$preauth_register->born_baby_gender }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="col">
                            <div class="infodata">
                                <label>Care Plan</label>
                                <p><strong>{{ @$preauth_register->benificiary->care_plan }}</strong></p>
                                <label>SGHS ID</label>
                                <p><strong>{{ @$preauth_register->benificiary->card_id }}</strong></p>
                                <label>ABHA Number</label>
                                <p><strong>{{ @$preauth_register->benificiary->aabha_id }}</strong></p>
                            </div>
                        </div>
                        <div class="col">
                            <div class="infodata">
                                <label>Mobile Number</label>
                                <p>{{ @$preauth_register->benificiary->mobile_no }}</p>
                                <label>Address</label>
                                <p>{{ @$preauth_register->benificiary->address }} | {{ @$preauth_register->city }}, {{ @$preauth_register->district->name }}, {{ @$preauth_register->state->name }} - {{ @$preauth_register->pincode }}</p>
                            </div>
                        </div>
                        <div class="col">
                            <div class="infodata">
                                <label>Registration ID</label>
                                <p>{{ $preauth_register->register_id }}</p>
                                <label>Registration Date</label>
                                <p><strong>{{ date("d/m/Y h:i A",strtotime($preauth_register->created_at)) }}</strong></p>
                            </div>
                        </div>
                        <!-- <div class="col">
                            <div class="infodata">
                                <label>Total Wallet Amount</label>
                                <p class="colored text-info">₹ 5,00,000.00</p>
                                <label>Wallet Balance</label>
                                <p class="colored text-info">₹ 5,00,000.00</p>
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
                @include('preauth._partials.preauth-step')
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
                                        aria-expanded="true" aria-controls="accordionPopoutOne">
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
                                                    Information <span class="status-dot">
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            height="24px" viewBox="0 -960 960 960"
                                                            width="24px" fill="undefined">
                                                            <path
                                                                d="M400-304 240-464l56-56 104 104 264-264 56 56-320 320Z" />
                                                        </svg>
                                                    </span></h4>
                                                <div class="row g-5">
                                                    <div class="col-md-6 col-lg-3">
                                                        <div
                                                            class="form-floating form-floating-outline">
                                                            <input type="number" id="temprature" name="temprature" oninput="sanitize(this, 'n','4');"
                                                                class="form-control" value="{{ @$general_info->temprature??'' }}"
                                                                placeholder="" />
                                                            <label
                                                                for="temprature">Temperature(°F) <span class="text-danger">*</span></label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-lg-3">
                                                        <div
                                                            class="form-floating form-floating-outline">
                                                            <input type="text" id="pulserate" name="pulserate" oninput="sanitize(this, 'n','4');" value="{{ @$general_info->pulserate??'' }}"
                                                                class="form-control"
                                                                placeholder="" />
                                                            <label for="pulserate">Pulse Rate Per
                                                                Minute (BPM)<span class="text-danger">*</span></label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-lg-3">
                                                        <div
                                                            class="form-floating form-floating-outline">
                                                            <input type="number" id="height" name="height" oninput="sanitize(this, 'n','4');" value="{{ @$general_info->height??'' }}"
                                                                class="form-control"
                                                                placeholder="" />
                                                            <label for="height">Height (In
                                                                CM) <span class="text-danger">*</span></label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-lg-3">
                                                        <div
                                                            class="form-floating form-floating-outline">
                                                            <input type="number" id="weight" name="weight" oninput="sanitize(this, 'n','5');" value="{{ @$general_info->weight??'' }}"
                                                                class="form-control"
                                                                placeholder="" />
                                                            <label for="weight">Weight (In
                                                                KG)</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-lg-3">
                                                        <div
                                                            class="form-floating form-floating-outline">
                                                            <input type="text" id="bmi" name="bmi" value="{{ @$general_info->bmi??'' }}"
                                                                class="form-control"
                                                                placeholder="" readonly/>
                                                            <label for="bmi">BMI</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-lg-3">
                                                        <label for="cyanosis"
                                                            class="mb-2">Cyanosis</label>
                                                        <div class="d-flex">
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
                                                        <label for="pallor" class="mb-2">Pallor</label>
                                                        <div class="d-flex">
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
                                                            class="mb-2">Malnutration</label>
                                                        <div class="d-flex">
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
                                                        <label for="oedema" class="mb-2">Oedema in
                                                            Feet</label>
                                                        <div class="d-flex">
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
                                                    <span class="status-dot">
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            height="24px" viewBox="0 -960 960 960"
                                                            width="24px" fill="undefined">
                                                            <path
                                                                d="M400-304 240-464l56-56 104 104 264-264 56 56-320 320Z" />
                                                        </svg>
                                                    </span>
                                                </h4>
                                                <div class="row g-5">
                                                    <div class="col-md-6 col-lg-3">
                                                        <div
                                                            class="form-floating form-floating-outline">
                                                            @php $diabetes_arr = App\CentralLogics\Helpers::getCommanData('Diabetes'); @endphp
                                                            <select class="form-select select2" id="diabetes" name="diabetes"
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
                                                            class="form-floating form-floating-outline">
                                                            @php $hypertension_arr = App\CentralLogics\Helpers::getCommanData('Hypertension'); @endphp
                                                            <select class="form-select select2"
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
                                                            class="form-floating form-floating-outline">
                                                            @php $heartdiseases = App\CentralLogics\Helpers::getCommanData('HeartDisease'); @endphp
                                                            <select class="form-select select2"
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
                                                            class="form-floating form-floating-outline">
                                                            @php $strokes = App\CentralLogics\Helpers::getCommanData('Stroke'); @endphp
                                                            <select class="form-select select2" id="stroke"
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
                                                            class="form-floating form-floating-outline">
                                                            @php $cancers = App\CentralLogics\Helpers::getCommanData('Cancer'); @endphp
                                                            <select class="form-select select2" id="cancer"
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
                                                            class="form-floating form-floating-outline">
                                                            @php $tuberculosises = App\CentralLogics\Helpers::getCommanData('Tuberculosis'); @endphp
                                                            <select class="form-select select2"
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
                                                            class="form-floating form-floating-outline">
                                                            @php $asthma_arr = App\CentralLogics\Helpers::getCommanData('Asthma'); @endphp
                                                            <select class="form-select select2" id="asthma"
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
                                                    <span class="status-dot">
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            height="24px" viewBox="0 -960 960 960"
                                                            width="24px" fill="undefined">
                                                            <path
                                                                d="M400-304 240-464l56-56 104 104 264-264 56 56-320 320Z" />
                                                        </svg>
                                                    </span>
                                                </h4>
                                                <div class="row g-5">
                                                    <div class="col-md-6 col-lg-3">
                                                        <div
                                                            class="form-floating form-floating-outline">
                                                            @php $appetites = App\CentralLogics\Helpers::getCommanData('Appetite'); @endphp
                                                            <select class="form-select select2" id="appetite"
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
                                                            class="form-floating form-floating-outline">
                                                            @php $bowels = App\CentralLogics\Helpers::getCommanData('Bowels'); @endphp
                                                            <select class="form-select select2" id="bowels"
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
                                                            class="form-floating form-floating-outline">
                                                            @php $nutrition_arr = App\CentralLogics\Helpers::getCommanData('Nutrition'); @endphp
                                                            <select class="form-select select2" id="nutrition"
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
                                                            class="form-floating form-floating-outline">
                                                            @php $dietes = App\CentralLogics\Helpers::getCommanData('Diet'); @endphp
                                                            <select class="form-select select2" id="diet"
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
                                                        <label for="known_allergies" class="mb-2">Known
                                                            Allergies <span class="text-danger">*</span></label>
                                                        <div class="d-flex">
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
                                                            class="form-floating form-floating-outline">
                                                            <input type="text" id="allergy_detail" oninput="sanitize(this, 't');" name="allergy_detail"
                                                                class="form-control"
                                                                placeholder="" value="{{ @$personal_history->allergy_detail }}"/>
                                                            <label for="allergy_detail">Allergy
                                                                Details</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-lg-3">
                                                        <label for="habits"
                                                            class="mb-2">Habits/Addictions <span class="text-danger">*</span></label>
                                                        <div class="d-flex">
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
                                                            class="form-floating form-floating-outline">
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
                                                    Consent <span class="status-dot">
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            height="24px" viewBox="0 -960 960 960"
                                                            width="24px" fill="undefined">
                                                            <path
                                                                d="M400-304 240-464l56-56 104 104 264-264 56 56-320 320Z" />
                                                        </svg>
                                                    </span>
                                                </h4>
                                                <div class="row align-items-end g-5">
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
                                                                <i class="fas fa-trash "></i>
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
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-lg-3">
                                                        <div
                                                            class="form-floating form-floating-outline">
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
                                                    <span class="status-dot">
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            height="24px" viewBox="0 -960 960 960"
                                                            width="24px" fill="undefined">
                                                            <path
                                                                d="M400-304 240-464l56-56 104 104 264-264 56 56-320 320Z" />
                                                        </svg>
                                                    </span>
                                                </h4>
                                                <div class="row g-5">
                                                    <div class="col-md-6 col-lg-3">
                                                        <div
                                                            class="form-floating form-floating-outline">
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
                                                            class="form-floating form-floating-outline">
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
                                                            class="form-floating form-floating form-floating-outline">
                                                            @php $admission_types = App\CentralLogics\Helpers::getCommanData('AdmissionType'); @endphp
                                                            <select class="form-select select2"
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
                                                        <label for="legal_case" class="mb-2">Medico Legal
                                                            Case <span class="text-danger">*</span></label>
                                                        <div class="d-flex">
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
                                                                <i class="fas fa-trash "></i>
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
                                            <h4 class="colored-verticle-title">Diagnosis <span
                                                    class="status-dot">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        height="24px" viewBox="0 -960 960 960"
                                                        width="24px" fill="undefined">
                                                        <path
                                                            d="M400-304 240-464l56-56 104 104 264-264 56 56-320 320Z" />
                                                    </svg>
                                                </span>
                                            </h4>
                                            <form onSubmit="return false" id="diagnosisForm">
                                                <div class="row g-5">
                                                    <div class="col-md-6 col-lg-3">
                                                        <div
                                                            class="form-floating form-floating-outline">
                                                            @php $diagnosis_arr = App\CentralLogics\Helpers::getCommanData('Diagnosis'); @endphp
                                                            <select class="form-select select2"
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
                                                            class="form-floating form-floating-outline">
                                                            <input type="text" id="other_diagnosis" name="other_diagnosis" oninput="sanitize(this, 't');"
                                                                class="form-control" 
                                                                placeholder="" />
                                                            <label
                                                                for="other_diagnosis">Other Diagnosis <span class="text-danger">*</span></label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 col-lg-3">
                                                        <label for="BMI" class="mb-2">Diagnosis
                                                            Type</label>
                                                        <div class="d-flex">
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
                                            <div class="row g-5">
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
                                                                @include('preauth._partials.diagnosis')
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="inside-left-info-box {{ (@$procedures->count() > 0)?'success':'pending' }} mt-3">
                                            <form onSubmit="return false" id="procedureForm">
                                                <h4 class="colored-verticle-title">Treatment Plan
                                                    <span class="status-dot">
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            height="24px" viewBox="0 -960 960 960"
                                                            width="24px" fill="undefined">
                                                            <path
                                                                d="M400-304 240-464l56-56 104 104 264-264 56 56-320 320Z" />
                                                        </svg>
                                                    </span>
                                                </h4>
                                                <div class="row justify-content-center">
                                                    <div class="col-md-8 mb-4">
                                                        <div
                                                            class="form-floating form-floating-outline">
                                                            <select class="form-select select2" id="specialitys"
                                                                name="speciality_id">
                                                                <option value=""></option>
                                                                @foreach($hospital_speciality as $hospital_spe)
                                                                    <option value="{{ $hospital_spe->speciality_id }}">{{ @$hospital_spe->speciality->name }}</option>
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
                                                            class="form-floating form-floating-outline">
                                                            <select class="form-select select2" id="procedure_id"
                                                                name="procedure_id">
                                                                <option value=""></option>
                                                            </select>
                                                            <label for="procedure_id">Procedure</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-8 mb-4 implant-field d-none">
                                                        <div
                                                            class="form-floating form-floating-outline">
                                                            <select class="form-select select2" id="implant_id"
                                                                name="implant_id">
                                                                <option value=""></option>
                                                            </select>
                                                            <label for="implant_id">Implant</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-8 mb-4 implant-field d-none">
                                                        <div
                                                            class="form-floating form-floating-outline">
                                                            <input type="text" id="implant_qty" name="implant_qty"
                                                                class="form-control"
                                                                placeholder="" readonly />
                                                            <label for="implant_qty">Quantity</label>
                                                        </div>
                                                        <div id="implant-qty-error"></div>
                                                    </div>
                                                    <div class="col-md-8 mb-4 stratification-field d-none">
                                                        <div
                                                            class="form-floating form-floating-outline">
                                                            <select class="form-select select2" id="stratification_id"
                                                                name="stratification_id">
                                                                <option value=""></option>
                                                            </select>
                                                            <label for="stratification_id">Stratification</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-8 mb-4">
                                                        <div
                                                            class="form-floating form-floating-outline">
                                                            <input type="text" id="no_of_days" name="no_of_days" oninput="sanitize(this, 'm','10');"
                                                                class="form-control"
                                                                placeholder="" />
                                                            <label for="no_of_days">No of
                                                                Days/Units</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-8 mb-4 u100-field d-none">
                                                        <div
                                                            class="form-floating form-floating-outline">
                                                            <input type="number" id="u100_amount" name="u100_amount" oninput="sanitize(this, 'n','10');"
                                                                class="form-control"
                                                                placeholder="" />
                                                            <label for="u100_amount">Unverfied Amount</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-8 mb-4">
                                                        <div
                                                            class="form-floating form-floating-outline">
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
                                                                    <th>Actions</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody
                                                                class="table-border-bottom-0 procedure-body">
                                                                @include('preauth._partials.procedures')
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="inside-left-info-box {{ $preauth_investigation_status?'success':'pending' }} mt-3">
                                            <form onSubmit="return false" id="investigationForm">
                                                <h4 class="colored-verticle-title">Investigation
                                                    <span class="status-dot">
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            height="24px" viewBox="0 -960 960 960"
                                                            width="24px" fill="undefined">
                                                            <path
                                                                d="M400-304 240-464l56-56 104 104 264-264 56 56-320 320Z" />
                                                        </svg>
                                                    </span>
                                                </h4>
                                                <div class="row justify-content-center">
                                                    <div class="col-12">
                                                        <div
                                                            class="table-responsive mt-5 text-nowrap">
                                                            <table class="table">
                                                                <thead class="table-dark">
                                                                    <tr>
                                                                        <th>No.</th>
                                                                        <th>Name</th>
                                                                        <th>Attachment</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody
                                                                    class="table-border-bottom-0 investigation-body">
                                                                    @include('preauth._partials.investigations',['preauth_register_id'=>$preauth_register->id])
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
                                                    <span class="status-dot">
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            height="24px" viewBox="0 -960 960 960"
                                                            width="24px" fill="undefined">
                                                            <path
                                                                d="M400-304 240-464l56-56 104 104 264-264 56 56-320 320Z" />
                                                        </svg>
                                                    </span>
                                                </h4>
                                                <div class="row justify-content-center">
                                                    <div class="col-md-6 mb-4">
                                                        <div
                                                            class="form-floating form-floating-outline">
                                                            <select class="form-select select2" id="care_team_id"
                                                                name="care_team_id">
                                                                <option value="">Select Team</option>
                                                                @foreach($teams as $team)
                                                                    <option value="{{ $team->id }}">{{ $team->name }}</option>
                                                                @endforeach
                                                            </select>
                                                            <label for="care_team_id">Select</label>
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
                                                            @include('preauth._partials.teams')
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
                                        <div class="row g-5">
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
                                                            @include('preauth._partials.finance')
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <ul class="d-flex listing-right finance-total-body">
                                                    @include('preauth._partials.finance-total')
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
                        <div class="row g-5">
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
                        <div class="row g-5">
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
</div>


<div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header ">
                <h4 class="modal-title" id="previewModalLabel3">Preview</h4>
                <button type="button" class="btn-primary btn ms-4" id="print-form">Print Form</button>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="card mb-6 p-0">
                    <div class="card-body">
                        <div class="row row-cols-5">
                            <div class="col">
                                <div
                                    class="d-flex text-center justify-content-center flex-column border-end border-secondary">
                                    <div class="position-relative image-overlay">
                                        <img src="{{ $preauth_register->benificiary->image_url }}" width="80" alt="avatar"
                                            class="mb-3 rounded-circle" />
                                    </div>
                                    <span class="number-3 mb-2">{{ @$preauth_register->benificiary->name }}</span>
                                    <span class="number-2">{{ @$preauth_register->benificiary->age }} Yr / {{ @$preauth_register->benificiary->gender }}</span>
                                </div>
                            </div>
                            <div class="col">
                                <div class="infodata">
                                    <label>Care Plan</label>
                                    <p><strong>{{ @$preauth_register->benificiary->care_plan }}</strong></p>
                                    <label>SGHS ID</label>
                                    <p><strong>{{ @$preauth_register->benificiary->card_id }}</strong></p>
                                    <label>ABHA Number</label>
                                    <p><strong>{{ @$preauth_register->benificiary->aabha_id }}</strong></p>
                                </div>
                            </div>
                            <div class="col">
                                <div class="infodata">
                                    <label>Mobile Number</label>
                                    <p>{{ @$preauth_register->benificiary->mobile_no }}</p>
                                    <label>Address</label>
                                    <p>{{ @$preauth_register->benificiary->address }} | {{ @$preauth_register->city }}, {{ @$preauth_register->district->name }}, {{ @$preauth_register->state->name }} - {{ @$preauth_register->pincode }}</p>
                                </div>
                            </div>
                            <div class="col">
                                <div class="infodata">
                                    <label>Registration ID</label>
                                    <p>{{ $preauth_register->register_id }}</p>
                                    <label>Registration Date</label>
                                    <p><strong>{{ date("d/m/Y h:i A",strtotime($preauth_register->created_at)) }}</strong></p>
                                </div>
                            </div>
                            <!-- <div class="col">
                                <div class="infodata">
                                    <label>Total Wallet Amount</label>
                                    <p class="colored text-info">₹ 5,00,000.00</p>
                                    <label>Wallet Balance</label>
                                    <p class="colored text-info">₹ 5,00,000.00</p>
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
<script>
    $(document).ready(function () {
        let registrationDate = moment("{{ @$preauth_register->created_at }}", "YYYY-MM-DD");
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
            url: '{{route("preauth.general-information.store")}}',
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
            url: '{{route("preauth.family-history.store")}}',
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
            url: '{{route("preauth.personal-history.store")}}',
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
            url: '{{route("preauth.authentication-consent.store")}}',
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
            url: '{{route("preauth.admission-details.store")}}',
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
            url: '{{route("preauth.diagnosis.store")}}',
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
            url: '{{route("preauth.diagnosis.destroy")}}',
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
    $("#specialitys").on("change",function(){
        var id = $(this).val();
        $(".loader-overlay").show();
        if(id != ''){
            $.ajax({
                url: '{{route("preauth.get-procedures")}}',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                type: 'POST',
                data: {id:id},
                success: function (response) {
                    $(".loader-overlay").hide();
                    $("#procedure_id").html(response.html);
                    $("#procedure-btn").attr('disabled',true);
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
            $("#procedure-btn").attr('disabled',true);
        }
    })
    
    $("#procedure_id").on("change",function(){
        var id = $(this).val();
        $(".loader-overlay").show();
        if(id != ''){
            $.ajax({
                url: '{{route("preauth.get-procedure-details")}}',
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
                url: '{{route("preauth.get-implant-details")}}',
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
                url: '{{route("preauth.get-stratification-details")}}',
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
            url: '{{route("preauth.procedure.store")}}',
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
            url: '{{route("preauth.procedure.destroy")}}',
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
            url: '{{route("preauth.procedure.delete-implant")}}',
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
            url: '{{route("preauth.care-team.store")}}',
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
            url: '{{route("preauth.care-team.destroy")}}',
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
            url: '{{route("preauth.investigation.store")}}',
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
        $.ajax({
            url: '{{route("preauth.validate-form")}}',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            type: 'POST',
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
        
        swal({
            title: "Are you sure?",
            text: 'Initiate Preauthorization Request. ',
            type: "warning",
            buttons: {
            cancel: {
                visible: true,
                text: "No, cancel!",
                className: "btn btn-danger",
            },
            confirm: {
                text: "Yes!",
                className: "btn btn-success",
            },
            },
        }).then((willDelete) => {
            if (willDelete) {
                $(".loader-overlay").show();
                $.ajax({
                    url: '{{route("preauth.request-form-sumbit")}}',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    type: 'POST',
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
                                window.location.href = '{{ route("preauth.dashboard") }}';
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
    $("#print-form").on("click", function () {
        var printContents = document.querySelector(".modal-body").innerHTML;
        var originalContents = document.body.innerHTML;

        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;

        location.reload();
    });

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