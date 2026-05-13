@extends('layouts.dec.app')
@section('title','Dashboard | DEC Approver')
@section('content')
@php
    $icon = asset('public/complete.svg');
    use App\CentralLogics\Helpers;
@endphp
<aside id="layout-menu" class="layout-menu-horizontal menu-horizontal menu bg-menu-theme flex-grow-0">
   <div class="w-100 h-100">
         <div class="row g-0">
            <div class="col-md-5">
               <div class="d-flex align-items-center bg-theme-color arrow">
                     <ul class="menu-list mb-0 py-2  d-flex">
                        <li class="menu-item">
                           <a href="{{route('decverifier.dashboard')}}" class="menu-link bottom-menu-icons">
                                 <i class="ri-home-4-line"></i>
                           </a>
                        </li>
                        <li class="menu-item">
                           <a href="javascript:void(0)" onclick="location.reload();" class="menu-link bottom-menu-icons">
                                 <i class="ri-restart-line"></i>
                           </a>
                        </li>
                     </ul>
               </div>
            </div>
            <div class="col-md-7">
                {{$hospital->facility_name}}
            </div>
         </div>
   </div>
</aside>
<div class="container-xxl flex-grow-1 container-p-y">
   <div class="bg-white rounded-3 box-shadow p-5">
        <div class="row">
            <div class="bs-stepper-content">        
                <div class="bs-stepper wizard-numbered mt-2">
                    <div class="bs-stepper-header">
                        <div class="step completed disabled crossed" data-target="#account-details">
                            <button type="button" class="step-trigger">
                                <span class="bs-stepper-circle"><i class="ri-check-line"></i></span>
                                <span class="bs-stepper-label">
                                <!-- <span class="bs-stepper-number">01</span> -->
                                <span class="d-flex flex-column gap-1 ms-2">
                                <span class="bs-stepper-title">Account Created</span>
                                <span class="bs-stepper-subtitle">({{$hospital->created_at->format('d/m/Y')}})</span>
                                </span>
                                </span>
                            </button>
                        </div>
                        <div class="line"></div>
                        <div class="step completed disabled crossed" data-target="#personal-info">
                            <button type="button" class="step-trigger">
                                <span class="bs-stepper-circle"><i class="ri-check-line"></i></span>
                                <span class="bs-stepper-label">
                                <!-- <span class="bs-stepper-number">02</span> -->
                                <span class="d-flex flex-column gap-1 ms-2">
                                    <span class="bs-stepper-title">Empanelment Form</span>
                                    @if(@$hospital->status_update_date)<span class="bs-stepper-subtitle">({{date('d/m/Y', strtotime(@$hospital->status_update_date))}})</span>@endif
                                    <!-- <span class="bs-stepper-subtitle">Add personal info</span> -->
                                </span>
                                </span>
                            </button>
                        </div>
                        <div class="line active"></div>
                        <div class="step active" data-target="#social-links">
                            <button type="button" class="step-trigger">
                                <span class="bs-stepper-circle"><i class="ri-check-line"></i></span>
                                
                            </button>
                        </div>
                        <div class="line"></div>
                        <div class="step " data-target="#social-links">
                            <button type="button" class="step-trigger">
                                <span class="bs-stepper-circle"><i class="ri-check-line"></i></span>
                                <span class="bs-stepper-label">
                                <!-- <span class="bs-stepper-number">03</span> -->
                                <span class="d-flex flex-column gap-1 ms-2">
                                    <span class="bs-stepper-title">DEC Officer Action</span>
                                    <!-- <span class="bs-stepper-subtitle">Add social links</span> -->
                                </span>
                                </span>
                            </button>
                        </div>
                        <!-- <div class="line "></div>
                        <div class="step " data-target="#social-links">
                            <button type="button" class="step-trigger">
                                <span class="bs-stepper-circle"><i class="ri-check-line"></i></span>
                                
                            </button>
                        </div> -->
                        <div class="line"></div>
                        <div class="step" data-target="#claim-pending">
                            <button type="button" class="step-trigger">
                                <span class="bs-stepper-circle"><i class="ri-check-line"></i></span>
                                <span class="bs-stepper-label">
                                <!-- <span class="bs-stepper-number">03</span> -->
                                <span class="d-flex flex-column gap-1 ms-2">
                                    <span class="bs-stepper-title">Sec Officer Action</span>
                                    <!-- <span class="bs-stepper-subtitle">Add social links</span> -->
                                </span>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card mb-6 ps-0 border border-primary">
                    <div class="card-body">
                        <div class="row row-cols-5">
                            <div class="col">
                                <div class="d-flex text-center justify-content-center flex-column border-end border-secondary">
                                    @if(@$hospital->image)
                                        <div class="position-relative image-overlay">
                                            <img src="{{asset('public/storage/'.@$hospital->image)}}" width="80" alt="{{@$hospital->facility_name}}" class="mb-3 rounded-circle">
                                        </div>
                                    @endif
                                <span class="number-3 mb-2">{{@$hospital->facility_name}}</span>
                                <span class="number-2">{{@$hospital->facilityOwnershipType->name}}</span>
                                </div>
                            </div>
                            <div class="col">
                                <div class="infodata">
                                <label>Facility/Reference Id</label>
                                <p><strong>{{@$hospital->hospital_id}}</strong></p>
                                <label>Facility Contact</label>
                                <p><strong>{{@$hospital->hospitalAddress->mobile_no}}</strong></p>
                                <label>Status</label>
                                <p><strong>{{@$hospital->status}}</strong></p>
                                </div>
                            </div>
                            <div class="col">
                                <div class="infodata">
                                <label>Facility Name</label>
                                <p>{{$hospital->facility_name}}</p>
                                <label>Specialities Selected</label>
                                <p>
                                    @php
                                        $specialities = $hospital->specialities()->where('available', 1)->get()->pluck('speciality.name')->toArray();
                                    @endphp
                                    {{ implode(', ', $specialities) }}
                                </p>
                                <label>Health Facility Registry ID</label>
                                <p> </p>
                                </div>
                            </div>
                            <div class="col">
                                <div class="infodata">
                                <label>State</label>
                                <p>{{@$hospital->hospitalAddress->states->name}}</p>
                                <label>Submission Date</label>
                                <p><strong>{{date('d/m/Y', strtotime($hospital->created_at))}}</strong></p>
                            
                                </div>
                            </div>
                            <div class="col">
                                <div class="infodata">
                                <label>District</label>
                                <p class="">{{@$hospital->hospitalAddress->districts->name}}</p>
                                <label>Status Updated Date</label>
                                <p class="">{{date('d/m/Y g:i:A', strtotime(@$hospital->status_update_date))}}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bs-stepper-content">
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
                            <div id="accordionPopoutOne" class="accordion-collapse collapse show" aria-labelledby="headingPopoutOne" data-bs-parent="#accordionPopout">
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
                                                            Basic Information     
                                                            @if(Helpers::stepCheck(1, $hospital->id, 'establishment_details', 'verifier') && Helpers::stepCheck(1, $hospital->id, 'address', 'verifier'))
                                                                @php($step1img = true)
                                                            @else
                                                                @php($step1img = false)
                                                            @endif
                                                            <img src="{{$icon}}" alt="" class="step1Icon" @if(!$step1img) style="display:none;" @endif>
                                                                         
                                                        </button>
                                                    </li>
                                                   
                                                    <li class="nav-item">
                                                        <button type="button" class="nav-link navstep2"
                                                        role="tab" data-bs-toggle="tab"
                                                        data-bs-target="#tab-speciality"
                                                        aria-controls="tab-speciality"
                                                        aria-selected="false"  onclick="loadStep(2);">
                                                            Speciality
                                                            @if(Helpers::stepCheck(2, $hospital->id, 'speciality', 'verifier'))
                                                                @php($step2img = true)
                                                            @else
                                                                @php($step2img = false)
                                                            @endif
                                                            <img src="{{$icon}}" alt="" class="step2Icon" @if(!$step2img) style="display:none;" @endif>
                                                            
                                                        </button>
                                                    </li>
                                                    <li class="nav-item">
                                                        <button type="button" class="nav-link navstep3"
                                                        role="tab" data-bs-toggle="tab"
                                                        data-bs-target="#tab-services"
                                                        aria-controls="tab-services"
                                                        aria-selected="false" onclick="loadStep(3);">
                                                            Services
                                                            @if(Helpers::stepCheck(3, $hospital->id, 'services', 'verifier'))
                                                                @php($step3img = true)
                                                            @else
                                                                @php($step3img = false)
                                                            @endif
                                                            <img src="{{$icon}}" alt="" class="step3Icon" @if(!$step3img) style="display:none;" @endif>
                                                                               
                                                        </button>
                                                    </li>
                                                    <li class="nav-item">
                                                        <button type="button" class="nav-link navstep4"
                                                        role="tab" data-bs-toggle="tab"
                                                        data-bs-target="#tab-lincences"
                                                        aria-controls="tab-lincences"
                                                        aria-selected="false" onclick="loadStep(4);">
                                                            Statutory Licences  
                                                            @if(Helpers::stepCheck(4, $hospital->id, 'statutory_licences', 'verifier'))
                                                                @php($step4img = true)
                                                            @else
                                                                @php($step4img = false)
                                                            @endif
                                                            <img src="{{$icon}}" alt="" class="step4Icon" @if(!$step4img) style="display:none;" @endif>               
                                                        </button>
                                                    </li>
                                                    <li class="nav-item">
                                                        <button type="button" class="nav-link navstep5"
                                                        role="tab" data-bs-toggle="tab"
                                                        data-bs-target="#tab-human"
                                                        aria-controls="tab-human"
                                                        aria-selected="false" onclick="loadStep(5);">
                                                            Human Resources
                                                            @if(Helpers::stepCheck(5, $hospital->id, 'ceo', 'verifier') && Helpers::stepCheck(5, $hospital->id, 'mhr', 'verifier') && Helpers::stepCheck(5, $hospital->id, 'sshr', 'verifier') && Helpers::stepCheck(5, $hospital->id, 'specialist', 'verifier'))
                                                                @php($step5img = true)
                                                            @else
                                                                @php($step5img = false)
                                                            @endif
                                                            <img src="{{$icon}}" alt="" class="step5Icon" @if(!$step5img) style="display:none;" @endif>
                                                        </button>
                                                    </li>
                                                    <li class="nav-item">
                                                        <button type="button" class="nav-link navstep6"
                                                        role="tab" data-bs-toggle="tab"
                                                        data-bs-target="#tab-quality-accerditation"
                                                        aria-controls="tab-quality-accerditation"
                                                        aria-selected="false" onclick="loadStep(6);">
                                                            Quality & Accreditation  
                                                            @if(Helpers::stepCheck(6, $hospital->id, 'quality_accreditation', 'verifier'))
                                                                @php($step6img = true)
                                                            @else
                                                                @php($step6img = false)
                                                            @endif
                                                            <img src="{{$icon}}" alt="" class="step6Icon" @if(!$step6img) style="display:none;" @endif>
                                                                            
                                                        </button>
                                                    </li>
                                                    <li class="nav-item">
                                                        <button type="button" class="nav-link navstep7"
                                                        role="tab" data-bs-toggle="tab"
                                                        data-bs-target="#tab-financial"
                                                        aria-controls="tab-financial"
                                                        aria-selected="false" onclick="loadStep(7);">
                                                            Financial Information   
                                                            @if(Helpers::stepCheck(7, $hospital->id, 'finance_details', 'verifier') && Helpers::stepCheck(7, $hospital->id, 'tax_details', 'verifier'))
                                                                @php($step7img = true)
                                                            @else
                                                                @php($step7img = false)
                                                            @endif
                                                            <img src="{{$icon}}" alt="" class="step7Icon" @if(!$step7img) style="display:none;" @endif>                                                                             
                                                        </button>
                                                    </li>
                                                    <li class="nav-item">
                                                        <button type="button" class="nav-link navstep8" role="tab" data-bs-toggle="tab" data-bs-target="#tab-documents" aria-controls="tab-documents" aria-selected="false" onclick="loadStep(8);">
                                                            Report
                                                            @if(Helpers::stepCheck(8, $hospital->id, 'report', 'verifier'))
                                                                @php($step8img = true)
                                                            @else
                                                                @php($step8img = false)
                                                            @endif
                                                            <img src="{{$icon}}" alt="" class="step8Icon" @if(!$step8img) style="display:none;" @endif>                                                                               
                                                        </button>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="card-body px-0 pt-5">
                                            <div class="tab-content p-0">
                                                <div class="tab-pane fade step1 show active" id="tab-basicinfo" role="tabpanel">
                                                </div>
                                                <div class="tab-pane fade step2" id="tab-speciality" role="tabpanel">
                                                </div>
                                                <div class="tab-pane fade step3" id="tab-services" role="tabpanel">
                                                </div>
                                                <div class="tab-pane fade step4" id="tab-lincences" role="tabpanel">
                                                </div>
                                                <div class="tab-pane fade step5" id="tab-human" role="tabpanel">
                                                </div>
                                                <div class="tab-pane fade step6" id="tab-quality-accerditation" role="tabpanel">
                                                </div>
                                                <div class="tab-pane fade step7" id="tab-financial" role="tabpanel">
                                                </div>
                                                <div class="tab-pane fade step8" id="tab-documents" role="tabpanel">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-3">
                        <button class="btn btn-outline-primary rounded-0 me-3 step1button" id="prev-btnn">BACK</button>
                        <button class="btn btn-outline-primary rounded-0 me-3 lastStepButton"  id="next-btn">NEXT</button>
                    </div>
                </div>
            </div>
        </div>
   </div>
   <div class="bg-white rounded-3 box-shadow p-5 mt-5">
      <div class="card">
         <div class="card-header"><h4>WorkFlow History<h4></div>
         <div class="card-body">
            <div class="table-responsive text-nowrap">
               <table class="table" id="workFlowList">
                  <thead>                      
                     <tr>
                        <th>SR.No</th>
                        <th>Name</th>
                        <th>Action</th>
                        <th>Attachment</th>
                        <th>Remarks</th>
                        <th>Date & Time</th>
                     </tr>
                  </thead>
                  <tbody class="table-border-bottom-0">
                     
                  </tbody>
               </table>
            </div>
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
        
        $(document).ready(function () {
            let table = $('#workFlowList').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('decverifier.getWorkFlowData', [base64_encode($hospital->id), base64_encode($hospital->uuid)]) }}",
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    data: function (d) {
                        let status = null;
                        d.status = status || null; // Pass the selected status as a parameter
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false  }, // Auto-incrementing index
                    { data: 'facility_name', name: 'facility_name' },
                    { data: 'action', name: 'action' },
                    { data: 'attachment', name: 'attachment' },
                    { data: 'remark', name: 'remark' },
                    { data: 'created_at', name: 'created_at' },
                ]
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
                    url: '{{route("decverifier.stepLoad", [base64_encode($hospital->id), base64_encode($hospital->uuid)])}}', 
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
