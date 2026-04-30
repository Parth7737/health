@extends('layouts.hospital.empanelment.app')
@section('title','Dashboard | Hospital Registration')
@section('content')
@php
    $icon = asset('public/complete.svg');
@endphp
<style>
        /* Print-specific styles */
        @media print {
            body {
                visibility: hidden;
            }

            .modal-body {
                visibility: visible;
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                height: auto;
            }

            .table-responsive {
                overflow: visible !important;
                max-height: none !important;
                height: auto !important;
            }

            .table {
                width: 100%;
                border-collapse: collapse;
            }
        }
    </style>
<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y mb-5">
   <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
         <li class="breadcrumb-item">
            <a href="javascript:void(0);">Hospital Registration</a>
         </li>
      </ol>
   </nav>
    <div class="bs-stepper-content">
        <div class="bs-stepper wizard-numbered mt-2">
            <div class="bs-stepper-header">
                <div class="step completed crossed" data-target="#account-details">
                    <button type="button" class="step-trigger">
                        <span class="bs-stepper-circle"><i class="ri-check-line"></i></span>
                        <span class="bs-stepper-label">
                        <!-- <span class="bs-stepper-number">01</span> -->
                        <span class="d-flex flex-column gap-1 ms-2">
                        <span class="bs-stepper-title">Account Created</span>
                        <span class="bs-stepper-subtitle">({{auth()->user()->created_at->format('d/m/Y')}})</span>
                        </span>
                        </span>
                    </button>
                </div>
                <div class="line active"></div>
                <div class="step active {{ @$hospital->is_approve?'crossed':'' }}" data-target="#personal-info">
                    <button type="button" class="step-trigger">
                        <span class="bs-stepper-circle"><i class="ri-check-line"></i></span>
                        <span class="bs-stepper-label">
                        <!-- <span class="bs-stepper-number">02</span> -->
                        <span class="d-flex flex-column gap-1 ms-2">
                            <span class="bs-stepper-title">Empanelment Form</span>
                        </span>
                        </span>
                    </button>
                </div>
                <div class="line {{ @$hospital->is_approve?'crossed':'' }}"></div>
                <div class="step {{ @$hospital->is_approve?'crossed':'' }}" data-target="#personal-info">
                    <button type="button" class="step-trigger">
                        <span class="bs-stepper-circle"><i class="ri-check-line"></i></span>
                        <span class="bs-stepper-label">
                        <!-- <span class="bs-stepper-number">02</span> -->
                        <span class="d-flex flex-column gap-1 ms-2">
                            <span class="bs-stepper-title">Admin Approve</span>
                        </span>
                        </span>
                    </button>
                </div>
            </div>
            @if(@$hospital && $hospital->status == 'Rejected' && $hospital->reject_reason)
                <div class="alert alert-danger alert-dismissible fade show m-4" role="alert">
                    <h6 class="alert-heading"><i class="ri-error-warning-line me-2"></i>Your application was rejected</h6>
                    <p class="mb-0"><strong>Reason:</strong> {{ $hospital->reject_reason }}</p>
                    <p class="mb-0 mt-2 small">Please make the necessary changes and resubmit your application.</p>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            <div class="bs-stepper-content">
                <!-- Account Details -->
                <div id="account-details" class="content">
                    <h6 class="mb-0">First Step</h6>
                </div>
                <!-- Personal Info -->
                <div id="personal-info" class="content active">

                    @php
                        $empanelment_step_status = auth()->user()->enable_step ?json_decode(auth()->user()->enable_step):json_decode(\App\CentralLogics\Helpers::get_settings('empanelment_step_status'));
                    @endphp
                    <div class="card shadow-none border-0 p-0 mb-6">
                        <div class="card-header p-0">
                        <div class="nav-align-top">
                            <ul class="nav nav-tabs ct-tabs" role="tablist">
                                <li class="nav-item">
                                    <button type="button"
                                    class="nav-link navstep1" role="tab"
                                    data-bs-toggle="tab"
                                    data-bs-target="#tab-basic"
                                    aria-controls="tab-basic"
                                    aria-selected="true" onclick="loadStep(1);">
                                    Basic Information
                                     <span class="step1Icon">
                                        &nbsp;<i class="ri-checkbox-circle-fill theme-color"></i>
                                    </span>
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button type="button" class="nav-link navstep2"
                                    role="tab" data-bs-toggle="tab"
                                    data-bs-target="#tab-hospital-info"
                                    aria-controls="tab-hospital-info"
                                    aria-selected="false" onclick="loadStep(2);">
                                    Hospital Information
                                    @if(@$hospital)
                                        @php($step2img = true)
                                    @else
                                        @php($step2img = false)
                                    @endif
                                    <span class="step2Icon"  @if(!$step2img) style="display:none;" @endif>
                                        &nbsp;<i class="ri-checkbox-circle-fill theme-color"></i>
                                    </span>
                                    </button>
                                </li>
                                
                                @if($empanelment_step_status && $empanelment_step_status->speciality_status == 1)
                                    <li class="nav-item">
                                        <button type="button" class="nav-link navstep3"
                                        role="tab" data-bs-toggle="tab"
                                        data-bs-target="#tab-hospital-speciality"
                                        aria-controls="tab-hospital-speciality"
                                        aria-selected="false" onclick="loadStep(3);">
                                        Specialities
                                        <?php if(@$hospital &&  @$hospital->specialities()->count() > 0){
                                            $step3 = true;
                                        }else{
                                            $step3 = false;
                                        } ?>
                                        <span class="step3Icon"  @if(!$step3) style="display:none;" @endif>
                                            &nbsp;<i class="ri-checkbox-circle-fill theme-color"></i>
                                        </span>
                                        </button>
                                    </li>
                                @endif
                                
                                @if($empanelment_step_status && $empanelment_step_status->service_status == 1)
                                    <li class="nav-item">
                                        <button type="button" class="nav-link navstep4"
                                        role="tab" data-bs-toggle="tab"
                                        data-bs-target="#tab-hospital-services"
                                        aria-controls="tab-hospital-services"
                                        aria-selected="false" onclick="loadStep(4);">
                                        Services
                                        <?php if(@$hospital && @$hospital->services()->count() > 0){
                                            $step4 = true;
                                        }else{
                                            $step4 = false;
                                        } ?>
                                        <span class="step4Icon"  @if(!$step4) style="display:none;" @endif>
                                            &nbsp;<i class="ri-checkbox-circle-fill theme-color"></i>
                                        </span>
                                        </button>
                                    </li>
                                @endif
                                
                                @if($empanelment_step_status && $empanelment_step_status->licenses_status == 1)
                                    <li class="nav-item">
                                        <button type="button" class="nav-link navstep5"
                                        role="tab" data-bs-toggle="tab"
                                        data-bs-target="#tab-hospital-licenses"
                                        aria-controls="tab-hospital-licenses"
                                        aria-selected="false" onclick="loadStep(5);">
                                        Licenses
                                        <?php if(@$hospital && @$hospital->licenses()->count() > 0){
                                            $step5 = true;
                                        }else{
                                            $step5 = false;
                                        } ?>
                                        <span class="step5Icon"  @if(!$step5) style="display:none;" @endif>
                                            &nbsp;<i class="ri-checkbox-circle-fill theme-color"></i>
                                        </span>
                                        </button>
                                    </li>
                                @endif
                                <li class="nav-item">
                                    <button type="button" class="nav-link navstep6"
                                    role="tab" data-bs-toggle="tab"
                                    data-bs-target="#tab-hospital-documents"
                                    aria-controls="tab-hospital-documents"
                                    aria-selected="false" onclick="loadStep(6);">
                                    Hospital Documents
                                    @if(@$hospital && @$hospital->documents()->count() > 0)
                                        @php($step6img = true)
                                    @else
                                        @php($step6img = false)
                                    @endif
                                    <span class="step6Icon" @if(!$step6img) style="display:none;" @endif>
                                        &nbsp;<i class="ri-checkbox-circle-fill theme-color"></i>
                                    </span>
                                    </button>
                                </li>
                            </ul>
                        </div>
                        </div>
                        <div class="card-body px-0 pt-5">
                            <div class="tab-content p-0">
                                <div class="tab-pane fade step1" id="tab-basic" role="tabpanel">
                                </div>
                                <div class="tab-pane fade step2" id="tab-hospital-info"
                                    role="tabpanel">
                                </div>
                                <div class="tab-pane fade step3" id="tab-hospital-speciality"
                                    role="tabpanel">
                                </div>
                                <div class="tab-pane fade step4" id="tab-hospital-services"
                                    role="tabpanel">
                                </div>
                                <div class="tab-pane fade step5" id="tab-hospital-licenses"
                                    role="tabpanel">
                                </div>
                                <div class="tab-pane fade step6" id="tab-hospital-documents"
                                    role="tabpanel">
                                </div>
                            </div>
                        </div>
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
            </div>
        </div>
    </div>
   <!--/ On route vehicles Table -->
</div>
<!--/ Content -->

@endsection
@push('scripts')
<script>
    @if($user)
        loadStep('{{$step}}');
    @endif

    $(document).ready(function () {
        // Initialize the current step variable
        let currentStep = '{{$step}}';
        $('#next-btn').on('click', function () {
            // Increment the step
            currentStep++;
            loadStep(currentStep);
        });

        $('#prev-btnn').on('click', function() {
            currentStep--;
            loadStep(currentStep);
        });
    });
    function loadStep(step) {
        ldrshow();
        $('.nav-link').removeClass('active');
        $('.tab-pane').removeClass('show active');
        $(`.step${step}`).addClass('show active');
        $(`.navstep${step}`).addClass('active');
        if(step != ""){
            $.ajax({
                url: '{{route("hospital.empanelmentRegistration.stepLoad", [$uuid])}}', 
                type: 'POST',
                data: {
                    '_token': '{{csrf_token()}}',
                    'step' : step
                },
                success: function (data) {
                    // Hide loader (if ldrhide() is implemented)
                    ldrhide();
                    if(step == 1) {
                        $('.step1button').attr('disabled', true);
                        $('.lastStepButton').removeAttr('disabled');

                    }

                    if(step >= 2 && step <= 7) {
                        $('.lastStepButton').removeAttr('disabled');
                        $('.step1button').removeAttr('disabled');
                    }

                    if(step == 8) {
                        $('.lastStepButton').attr('disabled', true);
                        $('.step1button').removeAttr('disabled');
                    }
                    // Update active states for navigation and content
                    
                    $(`.step${step}`).on('click', function(event) {
                        if (event.target.closest('.nav-item .active')) {
                            setSlider(event.target.closest('.nav-item'));
                        }
                    });

                    // Populate the content of the step
                    $(`.step${step}`).html(data.html || data);
                    loadSelect2();
                    
                },
                error: function (xhr, status, error) {
                    ldrhide(); // Hide loader on error
                    console.error("Error loading step:", error);
                    alert("Failed to load the step. Please try again.");
                }
            });
        }
    }
</script>
@endpush