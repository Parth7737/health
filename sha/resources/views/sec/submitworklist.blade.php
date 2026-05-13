@extends('layouts.dec.app')
@section('title','Dashboard | SEC Approver')
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
                           <a href="{{route('sec.dashboard')}}" class="menu-link bottom-menu-icons">
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
                        <div class="step {{@$verification->status == 'Physical Verification Completed' ? 'completed disabled crossed' : 'active'}}" data-target="#social-links">
                            <button type="button" class="step-trigger">
                                <span class="bs-stepper-circle"><i class="ri-check-line"></i></span>
                                
                            </button>
                        </div>
                        <div class="line @if(@$verification->status == 'Physical Verification Completed' && @$hospital->hospitalReport->dec_action == '') active @elseif(@$hospital->hospitalReport->dec_action != '') active @endif"></div>
                        <div class="step @if(@$verification->status == 'Physical Verification Completed' && @$hospital->hospitalReport->dec_action == '') active @elseif(@$hospital->hospitalReport->dec_action != '') completed disabled crossed @endif" data-target="#social-links">
                            <button type="button" class="step-trigger">
                                <span class="bs-stepper-circle"><i class="ri-check-line"></i></span>
                                <span class="bs-stepper-label">
                                <!-- <span class="bs-stepper-number">03</span> -->
                                <span class="d-flex flex-column gap-1 ms-2">
                                    <span class="bs-stepper-title">DEC Officer Action</span>
                                    @if(@$hospital->hospitalReport->dec_action != '') <span class="bs-stepper-subtitle">({{$hospital->status}})</span>@endif
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
                        <div class="line @if(@$hospital->hospitalReport->sec_action != '') active @endif"></div>
                        <div class="step @if(@$hospital->hospitalReport->sec_action != '') completed disabled crossed @endif" data-target="#claim-pending">
                            <button type="button" class="step-trigger">
                                <span class="bs-stepper-circle"><i class="ri-check-line"></i></span>
                                <span class="bs-stepper-label">
                                <!-- <span class="bs-stepper-number">03</span> -->
                                <span class="d-flex flex-column gap-1 ms-2">
                                    <span class="bs-stepper-title">Sec Officer Action</span>
                                    @if(@$hospital->hospitalReport->sec_action != '') <span class="bs-stepper-subtitle">({{$hospital->status}})</span>@endif
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
                    <div class="d-flex justify-content-end mt-3 mb-2 me-3">  
                        <button class="btn btn-primary rounded-0 me-3" type="button" id="preview">PREVIEW</button>                     
                        @if(@$verification)
                            @if(@$verification->status == "Physical Verification Pending")
                                <!-- <button class="btn btn-primary rounded-0 me-3" id="preview">EmpanelMent Form</button> -->
                                <label for="" class="text-primary text-center">Physical Verification Pending</label>
                            @elseif(@$verification->status == "Physical Verification Completed")
                                <label for="" class="text-primary text-center">Physical Verification Completed</label>
                            @endif
                        @else
                            <!-- <button class="btn btn-primary rounded-0 me-3" id="preview">EmpanelMent Form</button> -->
                            <!-- <a class="btn btn-outline-primary rounded-0" href="{{route('sec.initiate.verification', [base64_encode($hospital->id), base64_encode($hospital->uuid)])}}">INITIATE PHYSICAL VERIFICATION</a> -->
                        @endif
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
                                                            @if(Helpers::stepCheck(1, $hospital->id, 'establishment_details', 'sec') && Helpers::stepCheck(1, $hospital->id, 'address', 'sec'))
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
                                                            @if(Helpers::stepCheck(2, $hospital->id, 'speciality', 'sec'))
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
                                                            @if(Helpers::stepCheck(3, $hospital->id, 'services', 'sec'))
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
                                                            @if(Helpers::stepCheck(4, $hospital->id, 'statutory_licences', 'sec'))
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
                                                            @if(Helpers::stepCheck(5, $hospital->id, 'ceo', 'sec') && Helpers::stepCheck(5, $hospital->id, 'mhr', 'sec') && Helpers::stepCheck(5, $hospital->id, 'sshr', 'sec') && Helpers::stepCheck(5, $hospital->id, 'specialist', 'sec'))
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
                                                            @if(Helpers::stepCheck(6, $hospital->id, 'quality_accreditation', 'sec'))
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
                                                            @if(Helpers::stepCheck(7, $hospital->id, 'finance_details', 'sec') && Helpers::stepCheck(7, $hospital->id, 'tax_details', 'sec'))
                                                                @php($step7img = true)
                                                            @else
                                                                @php($step7img = false)
                                                            @endif
                                                            <img src="{{$icon}}" alt="" class="step7Icon" @if(!$step7img) style="display:none;" @endif>                    
                                                        </button>
                                                    </li>
                                                    <li class="nav-item">
                                                        <button type="button" class="nav-link navstep8"
                                                        role="tab" data-bs-toggle="tab"
                                                        data-bs-target="#tab-documents"
                                                        aria-controls="tab-documents"
                                                        aria-selected="false" onclick="loadStep(8);">
                                                            @if(@$is_upgrade) Status @else Report @endif     
                                                            @if(Helpers::stepCheck(8, $hospital->id, 'report', 'sec'))
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
        $(document).ready(function () {
            loadStep('{{$step}}');
        });

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
                    url: '{{route("sec.stepLoad", [base64_encode($hospital->id), base64_encode($hospital->uuid)])}}', 
                    type: 'POST',
                    data: {
                        '_token': '{{csrf_token()}}',
                        'step' : step
                    },
                    success: function (data) {
                        // Hide loader (if ldrhide() is implemented)
                        ldrhide();
                        // Update active states for navigation and content
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
                url: '{{route("sec.hospital.preview", [base64_encode($hospital->id), base64_encode($hospital->uuid)])}}',
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

        $(document).ready(function () {
            let table = $('#workFlowList').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('sec.getWorkFlowData', [base64_encode($hospital->id), base64_encode($hospital->uuid)]) }}",
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
