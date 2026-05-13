@extends('layouts.hospital.app')
@section('title','Dashboard | Hospital Engagement Module')
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
<!-- Menu -->
@include('hospital.base.topheader')
<!-- / Menu -->
<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y mb-5">
    <div class="bg-white rounded-3 box-shadow p-5">
        @include('hospital.base.topbarmenu')
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="javascript:void(0);">Home</a>
                </li>
                <li class="breadcrumb-item active itemName">Scheme</li>
            </ol>
        </nav>
        <div class="row">
                <div class="bs-stepper-content">        
                    <div class="bs-stepper wizard-numbered mt-2">
                        <div class="bs-stepper-content">
                            <!-- Account Details -->
                            <div id="account-details" class="content">
                                <h6 class="mb-0">First Step</h6>
                            </div>
                            <!-- Personal Info -->
                            <div id="personal-info" class="content active">

                                <div class="accordion accordion-popout mt-4" id="accordionPopout">
                                    <div class="accordion-item active">
                                    <h2 class="accordion-header" id="headingPopoutOne">
                                        <button type="button" class="accordion-button theme-color"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#accordionPopoutOne" aria-expanded="true"
                                            aria-controls="accordionPopoutOne">
                                        Facility Information
                                        </button>
                                    </h2>
                                    <div id="accordionPopoutOne"
                                        class="accordion-collapse collapse show"
                                        aria-labelledby="headingPopoutOne"
                                        data-bs-parent="#accordionPopout">
                                        <div class="accordion-body">
                                            <div class="card shadow-none border-0 p-0 mb-6">
                                                <div class="card-header p-0">
                                                <div class="nav-align-top">
                                                    <ul class="nav nav-tabs ct-tabs" role="tablist">
                                                        <li class="nav-item">
                                                            <button type="button"
                                                            class="nav-link navstep1" role="tab"
                                                            data-bs-toggle="tab"
                                                            data-bs-target="#tab-scheme"
                                                            aria-controls="tab-scheme"
                                                            aria-selected="true" onclick="loadStep(1);">
                                                            Establishment Details
                                                            </button>
                                                        </li>
                                                        <li class="nav-item">
                                                            <button type="button"
                                                            class="nav-link navstep2" role="tab"
                                                            data-bs-toggle="tab"
                                                            data-bs-target="#tab-scheme"
                                                            aria-controls="tab-scheme"
                                                            aria-selected="true" onclick="loadStep(2);">
                                                            Address Details
                                                            </button>
                                                        </li>
                                                        <li class="nav-item">
                                                            <button type="button"
                                                            class="nav-link navstep3" role="tab"
                                                            data-bs-toggle="tab"
                                                            data-bs-target="#tab-scheme"
                                                            aria-controls="tab-scheme"
                                                            aria-selected="true" onclick="loadStep(3);">
                                                            Scheme
                                                            </button>
                                                        </li>
                                                        <li class="nav-item">
                                                            <button type="button" class="nav-link navstep4"
                                                            role="tab" data-bs-toggle="tab"
                                                            data-bs-target="#tab-speciality"
                                                            aria-controls="tab-speciality"
                                                            aria-selected="false" onclick="loadStep(4);">
                                                            Speciality
                                                            </button>
                                                        </li>
                                                        <li class="nav-item">
                                                            <button type="button" class="nav-link navstep5"
                                                            role="tab" data-bs-toggle="tab"
                                                            data-bs-target="#tab-services"
                                                            aria-controls="tab-services"
                                                            aria-selected="false" onclick="loadStep(5);">
                                                            Services
                                                            </button>
                                                        </li>
                                                        <li class="nav-item">
                                                            <button type="button" class="nav-link navstep6"
                                                            role="tab" data-bs-toggle="tab"
                                                            data-bs-target="#tab-lincences"
                                                            aria-controls="tab-lincences"
                                                            aria-selected="false" onclick="loadStep(6);">
                                                            Statutory Licences
                                                            </button>
                                                        </li>
                                                        <li class="nav-item">
                                                            <button type="button" class="nav-link navstep7"
                                                            role="tab" data-bs-toggle="tab"
                                                            data-bs-target="#tab-human"
                                                            aria-controls="tab-human"
                                                            aria-selected="false" onclick="loadStep(7);">
                                                            Human Resources
                                                            </button>
                                                        </li>
                                                        <li class="nav-item">
                                                            <button type="button" class="nav-link navstep8"
                                                            role="tab" data-bs-toggle="tab"
                                                            data-bs-target="#tab-quality-accerditation"
                                                            aria-controls="tab-quality-accerditation"
                                                            aria-selected="false" onclick="loadStep(8);">
                                                            Quality & Accreditation
                                                            </button>
                                                        </li>
                                                        <li class="nav-item">
                                                            <button type="button" class="nav-link navstep9"
                                                            role="tab" data-bs-toggle="tab"
                                                            data-bs-target="#tab-financial"
                                                            aria-controls="tab-financial"
                                                            aria-selected="false" onclick="loadStep(9);">
                                                            Financial Information
                                                            </button>
                                                        </li>
                                                        <li class="nav-item">
                                                            <button type="button" class="nav-link navstep10"
                                                            role="tab" data-bs-toggle="tab"
                                                            data-bs-target="#tab-documents"
                                                            aria-controls="tab-documents"
                                                            aria-selected="false" onclick="loadStep(10);">
                                                            Status
                                                            </button>
                                                        </li>
                                                    </ul>
                                                </div>
                                                </div>
                                                <div class="card-body px-0 pt-5">
                                                    <div class="tab-content p-0">
                                                        <div class="tab-pane fade step1" id="tab-establishment" role="tabpanel">
                                                        </div>
                                                        <div class="tab-pane fade step2" id="tab-address" role="tabpanel">
                                                        </div>
                                                        <div class="tab-pane fade step3" id="tab-scheme" role="tabpanel">
                                                        </div>
                                                        <div class="tab-pane fade step4" id="tab-speciality" role="tabpanel">
                                                        </div>
                                                        <div class="tab-pane fade step5" id="tab-services" role="tabpanel">
                                                        </div>
                                                        <div class="tab-pane fade step6" id="tab-lincences" role="tabpanel">
                                                        </div>
                                                        <div class="tab-pane fade step7" id="tab-human" role="tabpanel">
                                                        </div>
                                                        <div class="tab-pane fade step8" id="tab-quality-accerditation" role="tabpanel">
                                                        </div>
                                                        <div class="tab-pane fade step9" id="tab-financial" role="tabpanel">
                                                        </div>
                                                        <div class="tab-pane fade step10" id="tab-documents" role="tabpanel">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                                <!-- <div class="d-flex justify-content-end mt-3">
                                    <button class="btn btn-outline-primary rounded-0 me-3 step1button" id="prev-btnn" {{ $step == 1 ? 'disabled' : ''}}>BACK</button>
                                    <button class="btn btn-primary rounded-0 me-3" type="button" id="preview">PREVIEW</button>
                                    <button class="btn btn-outline-primary rounded-0 me-3 lastStepButton"  id="next-btn" {{$step == 8 ? 'disabled' : ''}}>NEXT</button>
                                </div> -->
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
   <!--/ On route vehicles Table -->
</div>
<!--/ Content -->

<div class="modal fade" id="declModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header ">
                <h4 class="modal-title" id="declModalLabel3">Confirmation</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">    
                <form id="declform">    
                    <label for="is_acceptt"><input type="checkbox" name="is_acceptt" id="is_acceptt">&nbsp;&nbsp;<strong>I hereby declare that all information provided in this empanelment form is true, accurate, and complete to the best of my knowledge. I understand that any false or missing information may lead to rejection of this application or termination of empanelment, and may be subject to legal consequenses as per applicable laws and regulations.</strong></label>
                    <button type="button" class="btn btn-outline-primary rounded-0 declsubmit">SUBMIT</button>
                </form>
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
                <h5 class="theme-color mt-3">Basic Information</h5>
                <div class="card mb-6 p-0">
                    <div class="card-header">
                        <strong>Establishment Details</strong>
                    </div>                                
                    <div class="card-body">
                        <div class="row row-cols-4">
                            <div class="col">
                                <div class="infodata">
                                    <label>Name of the Facility</label>
                                    <p><strong>{{ @$hospital->facility_name }}</strong></p>
                                    <label>Facility Type</label>
                                    <p><strong>{{ @$hospital->facilityType->name }}</strong></p>
                                    <label>Facility Speciality Type</label>
                                    <p><strong>{{ @$hospital->facilitySpecialityType->name }}</strong></p>
                                    <label>Facility Ownership Type</label>
                                    <p><strong>{{ @$hospital->facilityOwnershipType->name }}</strong></p>    
                                    <label class="mb-3">Government Benefits/Concessions</label>
                                    <p><strong>{{ @$hospital->govermentBenefits->name }}</strong></p>                       
                                </div>
                            </div>
                            <div class="col">
                                <div class="infodata">                            
                                    <label>Facility Ownership Sub Type - 1</label>
                                    <p><strong>{{ @$hospital->facilityOwnershipSubType1->name }}</strong></p>                           
                                    <label>Facility Ownership Sub Type - 2</label>
                                    <p><strong>{{ @$hospital->facilityOwnershipSubType2->name }}</strong></p>
                                    <label>Facility Registration Certificate</label>
                                    <p><strong>{{ @$hospital->facilityRegistrationCertificate->name }}</strong></p>
                                    <label>System(s) of Medicine</label>
                                    <p><strong>{{ @$hospital->systemMedicine->name }}</strong></p>                                                
                                </div>
                            </div>
                            <div class="col">
                                <div class="infodata">
                                    
                                    <label>Does this facility has PG/DNB?</label>
                                    <p><strong>{{ @$hospital->pg_dnb ? 'Yes' : 'No' }}</strong></p>
                                    <label>Facility Registration Number</label>
                                    <p><strong>{{ @$hospital->facility_registration_number }}</strong></p>
                                    <label>Registration Certificate Expiry Date</label>
                                    <p><strong>{{ @$hospital->registration_certificate_expiry }}</strong></p>
                                    <label>Establishment Year</label>
                                    <p><strong>{{ @$hospital->date_of_establishment }}</strong></p>                            
                                </div>
                            </div>
                            <div class="col">
                                <div class="infodata">
                                    @if(@$hospital->sub_type_certificate_name && @$hospital->sub_type_certificate)
                                        <label class="mt-2"><strong>{{@$hospital->sub_type_certificate_name}}</strong>&nbsp; <br><a href="{{ asset('public/storage/'.@$hospital->sub_type_certificate) }}" target="_blank" class="btn btn-outline-primary btn-sm">View Document</a></label> <br>
                                    @endif
                                    @if(@$hospital->facilityOwnershipSubType3->name)
                                        <label>Facility Ownership Sub Type - 3</label>
                                        <p><strong>{{ @$hospital->facilityOwnershipSubType3->name }}</strong></p>    
                                    @endif      
                                    @if(@$hospital->rohini_id)
                                        <label>ROHINI ID</label>
                                        <p><strong>{{ @$hospital->rohini_id }}</strong></p>
                                    @endif  
                                    @if(@$hospital->name_od_group)
                                        <label>Group Name</label>
                                        <p><strong>{{ @$hospital->name_od_group }}</strong></p>
                                    @endif
                                    @if(@$hospital->group_id)
                                        <label>Group ID</label>
                                        <p><strong>{{ @$hospital->group_id }}</strong></p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mb-6 p-0">                
                    <div class="card-header"><strong>Address Details</strong></div>                                
                    <div class="card-body">
                        <div class="row row-cols-5">
                            <div class="col">
                                <div class="infodata">
                                    <label>Address</label>
                                    <p><strong>{{ @$hospital->hospitalAddress->address }}</strong></p>
                                    <label>City/Town</label>
                                    <p><strong>{{ @$hospital->hospitalAddress->city }}</strong></p>
                                    <label>District</label>
                                    <p><strong>{{ @$hospital->hospitalAddress->districts->name }}</strong></p>
                                    <label>Mobile No</label>
                                    <p><strong>{{ @$hospital->hospitalAddress->mobile_no }}</strong></p>                                           
                                </div>
                            </div>
                            <div class="col">
                                <div class="infodata">
                                    <label>Pincode</label>
                                    <p><strong>{{ @$hospital->hospitalAddress->pincode }}</strong></p>
                                    <label>Block</label>
                                    <p><strong>{{ @$hospital->hospitalAddress->blockdata->name }}</strong></p>
                                    <label>Telephone with STD Code</label>
                                    <p><strong>{{ @$hospital->hospitalAddress->std_code }}{{ @$hospital->hospitalAddress->telephone }}</strong></p>
                                    
                                </div>
                            </div>
                            <div class="col">
                                <div class="infodata">
                                    <label>Village</label>
                                    <p><strong>{{@$hospital->hospitalAddress->villages->name }}</strong></p>
                                    <label>State</label>
                                    <p><strong>{{ @$hospital->hospitalAddress->states->name }}</strong></p>
                                    <label>Email ID</label>
                                    <p><strong>{{ @$hospital->hospitalAddress->email  }}</strong></p>
                                </div>
                            </div>
                            <div class="col">
                                <div class="infodata">
                                    <label>LandMark</label>
                                    <p><strong>{{ @$hospital->hospitalAddress->landmark }}</strong></p>
                                    <label>Website</label>
                                    <p><strong>{{ @$hospital->hospitalAddress->website }}</strong></p>
                                    <label>Local Police Station</label>
                                    <p><strong>{{ @$hospital->hospitalAddress->police_station }}</strong></p>
                                    <label>Locality</label>
                                    <p><strong>{{ @$hospital->hospitalAddress->locality }}</strong></p>
                                </div>
                            </div>
                            <div class="col">
                                <div class="infodata">
                                    <label>Latitude</label>
                                    <p><strong>{{ @$hospital->hospitalAddress->latitude }}</strong></p>
                                    <label>Longitude</label>
                                    <p><strong>{{ @$hospital->hospitalAddress->longitude }}</strong></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="preview-data"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
    <script>
        @if($hospital)
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

        $(".declsubmit").on('click', function() {
            if (!$("#is_acceptt").is(":checked")) {
                swal({
                    title: "Declaration Required",
                    text: "You must accept the declaration before submitting.",
                    type: "error",
                    buttons: {
                        confirm: {
                            text: "Ok",
                            className: "btn btn-danger",
                        },
                    },
                });
                return; // Stop execution if checkbox is not checked
            }
            
            ldrshow();
            $.ajax({
                url: '{{route("hospital.hospitalReSubmit", [$uuid, $hospital->main_hospitalid])}}',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                type: 'POST',
                processData: false,
                contentType: false,
                success: function (response) {
                    ldrhide();
                    if(response.success) {
                        swal({
                            title: "Hospital Submitted!!",
                            text: response.message,
                            type: "success",
                            buttons: {
                            confirm: {
                                text: "Ok!",
                                className: "btn btn-success",
                            },
                            },
                        }).then((willDelete) => {
                            if (willDelete) {
                                successMessage(response.message);
                                $("#declModal").modal('hide');
                                setTimeout(() => {
                                    window.location.href = response.url;
                                }, 1000);
                            }
                        });
                        
                    } else {
                        errorMessage(response.message);
                    }
                },
                error: function (xhr) {
                    ldrhide();
                    $('.error').remove();
                    errorMessage('Something went wrong. Please try again later.');
                }
            });
        });

        $(".previewsubmit").on('click', function() {
            swal({
                title: "Confirm Submission?",
                text: 'Are you sure you want to submit and proceed with the payment?',
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
                    ldrshow();
                    $.ajax({
                        url: '{{route("hospital.empanelmentRegistration.hospitalSubmit", [$uuid, $hospital->main_hospitalid])}}',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        type: 'POST',
                        processData: false,
                        contentType: false,
                        success: function (response) {
                            ldrhide();
                            if(response.success) {
                                swal({
                                    title: "Hospital Submitted!!",
                                    text: response.message,
                                    type: "success",
                                    buttons: {
                                    confirm: {
                                        text: "Ok!",
                                        className: "btn btn-success",
                                    },
                                    },
                                }).then((willDelete) => {
                                    if (willDelete) {
                                        successMessage(response.message);
                                        setTimeout(() => {
                                            window.location.href = response.url;
                                        }, 1000);
                                    }
                                });
                                
                            } else {
                                errorMessage(response.message);
                            }
                        },
                        error: function (xhr) {
                            ldrhide();
                            $('.error').remove();
                            errorMessage('Something went wrong. Please try again later.');
                        }
                    });
                }
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
                    url: '{{route("hospital.update-application-stepLoad", [$uuid, $hospital->main_hospitalid])}}', 
                    type: 'POST',
                    data: {
                        '_token': '{{csrf_token()}}',
                        'step' : step
                    },
                    success: function (data) {
                        // Hide loader (if ldrhide() is implemented)
                        ldrhide();
                        // if(step == 1) {
                        //     $('.step1button').attr('disabled', true);
                        //     $('.lastStepButton').removeAttr('disabled');

                        // }

                        // if(step >= 2 && step <= 7) {
                        //     $('.lastStepButton').removeAttr('disabled');
                        //     $('.step1button').removeAttr('disabled');
                        // }

                        // if(step == 8) {
                        //     $('.lastStepButton').attr('disabled', true);
                        //     $('.step1button').removeAttr('disabled');
                        // }
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

        $("#preview").on("click",function(){
            ldrshow();
            $.ajax({
                url: '{{route("hospital.empanelmentRegistration.preview", [$uuid, $hospital->main_hospitalid])}}',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                type: 'POST',
                processData: false,
                contentType: false,
                success: function (response) {
                    ldrhide();
                    console.log(response);
                    $("#preview-data").html(response.html || response);
                    $("#previewModal").modal("show");                    
                },
                error: function (xhr) {
                    ldrhide();
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
        
        $("#print-form").on("click", function () {
            var printContents = document.querySelector(".modal-body").cloneNode(true);
            var originalContents = document.body.innerHTML;

            // Remove scroll styles for printing
            printContents.querySelectorAll(".table-responsive").forEach(function (el) {
                el.style.overflow = "visible"; 
                el.style.maxHeight = "none"; 
                el.style.height = "auto"; 
            });

            document.body.innerHTML = printContents.innerHTML;
            window.print();
            document.body.innerHTML = originalContents;

            location.reload(); // Restore the page
        });
      
    </script>
@endpush