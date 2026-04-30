@extends('layouts.hospital.empanelment.app')
@section('title','Dashboard | Hospital Engagement Module')
@section('content')
<aside id="layout-menu" class="layout-menu-horizontal menu-horizontal menu bg-menu-theme flex-grow-0">
   <div class="w-100 h-100">
         <div class="row g-0">
            <div class="col-md-5">
               <div class="d-flex align-items-center bg-theme-color arrow">
                     <ul class="menu-list mb-0 py-2  d-flex">
                        <li class="menu-item">
                           <a href="{{route('hospital.dashboard')}}" class="menu-link bottom-menu-icons">
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
            </div>
         </div>
   </div>
</aside>
<div class="container-xxl flex-grow-1 container-p-y mb-5">
   <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
         <li class="breadcrumb-item">
               <a href="{{route('hospital.dashboard')}}">Home</a>
         </li>
         <li class="breadcrumb-item active">Facility Information</li>
      </ol>
   </nav>
   <div class="bs-stepper-content">
      <div class="step1">
         <div class="accordion accordion-popout mt-4" id="accordionPopout">
            <div class="accordion-item establishment  @if(!empty($hospitalDetail) && $hospitalDetail->is_added == 0) active @elseif(empty($hospitalDetail)) active @endif">
               <h2 class="accordion-header" id="headingPopoutOne">
                     <button type="button" class="accordion-button btn1 theme-color  @if(!empty($hospitalDetail) && $hospitalDetail->is_added == 1) collapsed @endif"
                        data-bs-toggle="collapse"
                        data-bs-target="#accordionPopoutOne"
                        aria-expanded="true" aria-controls="accordionPopoutOne">
                        Establishment Details @if(!empty($hospitalDetail) && $hospitalDetail->is_added == 1) <i class="tf-icons ri-check-fill text-green"></i>@endif
                     </button>
               </h2>
               <div id="accordionPopoutOne" class="accordion-collapse collapse @if(!empty($hospitalDetail) && $hospitalDetail->is_added == 0) show @elseif(empty($hospitalDetail)) show @endif" aria-labelledby="headingPopoutOne" data-bs-parent="#accordionPopout">
                  <div class="accordion-body">
                     <form id="establishmentDetails">
                        <!-- Communication Address -->
                        <div class="card mb-6 border border-primary">
                           <div class="card-body">  
                              <div class="Estmessage text-success"></div>               
                              <div class="row g-5">
                                 
                                 <div class="col-sm-3">
                                    <label class="mb-3">Facility Name <span class="text-danger">*</span></label>
                                    <input type="text" id="facility_name" oninput="sanitize(this, 't');" name="facility_name" class="form-control errormesage"
                                    placeholder="Facility Name" value="{{!empty($hospitalDetail) ? $hospitalDetail->facility_name : ''}}" required />
                                 </div>

                                 <div class="col-sm-3">
                                    <label class="mb-3">Facility Type <span class="text-danger">*</span></label>
                                    <select name="facility_type" class="select2 form-select form-select-lg errormesage" data-allow-clear="true" required>
                                       <option value="">Select</option>
                                       @foreach($facilityTypes as $key => $value)
                                          <option value="{{$value->id}}" {{!empty($hospitalDetail) &&  $hospitalDetail->facility_type == $value->id ? 'selected': ''}}>{{$value->name}}</option>
                                       @endforeach
                                    </select>
                                 </div>

                                 <div class="col-sm-3">
                                    <label class="mb-3">Facility Speciality Type <span class="text-danger">*</span></label>
                                    <select name="facility_speciality_type" class="select2 form-select form-select-lg errormesage"  data-allow-clear="true" required>
                                       <option value="">Select</option>
                                       @foreach($FacilitySpecialityType as $key => $value)
                                          <option value="{{$value->id}}" {{!empty($hospitalDetail) &&  $hospitalDetail->facility_speciality_type == $value->id ? 'selected': ''}}>{{$value->name}}</option>
                                       @endforeach
                                    </select>
                                 </div>

                                 <div class="col-sm-3">
                                    <label class="mb-3">Facility Ownership Type <span class="text-danger">*</span></label>
                                    <select name="facility_ownership_type" class="select2 form-select form-select-lg errormesage" id="facility_ownership_type" onchange="fetchSubType();"  required>
                                       <option value="">Select</option>
                                       @foreach($FacilityOwnershipType as $key => $value)
                                          <option value="{{$value->id}}" {{!empty($hospitalDetail) &&  $hospitalDetail->facility_ownership_type == $value->id ? 'selected': ''}} >{{$value->name}}</option>
                                       @endforeach
                                    </select>
                                 </div>

                                 <div class="col-sm-3">
                                    <label class="mb-3">Facility Ownership Sub Type - 1<span class="text-danger">*</span></label>
                                    <select name="facility_ownership_sub_type1"  class="select2 form-select form-select-lg errormesage" id="facility_ownership_sub_type1" data-allow-clear="true" required onchange="fetchSubTypetwo();">
                                       <option value="">Select</option>
                                    </select>
                                 </div>

                                 <div class="col-sm-3 d-none subtypecertificate">
                                    <label for="formFile" id="certificateName" class="form-label">Certificate <span class="text-danger">*</span></label>
                                    <input type="hidden" name="sub_type_certificate_name" id="sub_type_certificate_name">
                                    <div class="file-upload-section imgerror">
                                       <div class="file-upload-wrapper">
                                          <svg xmlns="http://www.w3.org/2000/svg"
                                             height="24px"
                                             viewBox="0 -960 960 960"
                                             width="24px" fill="#6200ea">
                                             <path
                                                d="M260-160q-91 0-155.5-63T40-377q0-78 47-139t123-78q25-92 100-149t170-57q117 0 198.5 81.5T760-520q69 8 114.5 59.5T920-340q0 75-52.5 127.5T740-160H520q-33 0-56.5-23.5T440-240v-206l-64 62-56-56 160-160 160 160-56 56-64-62v206h220q42 0 71-29t29-71q0-42-29-71t-71-29h-60v-80q0-83-58.5-141.5T480-720q-83 0-141.5 58.5T280-520h-20q-58 0-99 41t-41 99q0 58 41 99t99 41h100v80H260Zm220-280Z" />
                                          </svg>
                                          <p><strong>Browse</strong></p>
                                       </div>
                                       <input type="file"
                                          class="file-input d-none" name="sub_type_certificate"/>
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
                                    <div class="preivew-certificate">
                                       @if(@$hospitalDetail->sub_type_certificate)
                                          <label class="mt-2"><a href="{{ asset('public/storage/'.@$hospitalDetail->sub_type_certificate) }}" target="_blank" class="btn btn-outline-primary btn-sm">{{$hospitalDetail->sub_type_certificate_name}}</a></label>
                                       @endif
                                    </div>
                                 </div>



                                 <div class="col-sm-3 d-none propcertificate">
                                    <label for="formFile" id="propcertificateName" class="form-label">Legal Entity Certificate in Case of PAN Card Issued on Individual Name  <span class="text-danger">*</span></label>
                                    <input type="hidden" name="propritership_document_name" value="Legal Entity Certificate in Case of PAN Card Issued on Individual Name" id="propritership_document_name">
                                    <div class="file-upload-section imgerror">
                                       <div class="file-upload-wrapper">
                                          <svg xmlns="http://www.w3.org/2000/svg"
                                             height="24px"
                                             viewBox="0 -960 960 960"
                                             width="24px" fill="#6200ea">
                                             <path
                                                d="M260-160q-91 0-155.5-63T40-377q0-78 47-139t123-78q25-92 100-149t170-57q117 0 198.5 81.5T760-520q69 8 114.5 59.5T920-340q0 75-52.5 127.5T740-160H520q-33 0-56.5-23.5T440-240v-206l-64 62-56-56 160-160 160 160-56 56-64-62v206h220q42 0 71-29t29-71q0-42-29-71t-71-29h-60v-80q0-83-58.5-141.5T480-720q-83 0-141.5 58.5T280-520h-20q-58 0-99 41t-41 99q0 58 41 99t99 41h100v80H260Zm220-280Z" />
                                          </svg>
                                          <p><strong>Browse</strong></p>
                                       </div>
                                       <input type="file"
                                          class="file-input d-none" name="propritership_document"/>
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
                                    <div class="preivew-certificate">
                                       @if(@$hospitalDetail->sub_type_certificate)
                                          <label class="mt-2"><a href="{{ asset('public/storage/'.@$hospitalDetail->sub_type_certificate) }}" target="_blank" class="btn btn-outline-primary btn-sm">{{$hospitalDetail->propritership_document_name}}</a></label>
                                       @endif
                                    </div>
                                 </div>
                                 

                                 <div class="col-sm-3">
                                    <label class="mb-3">Facility Ownership Sub Type - 2<span class="text-danger">*</span></label>
                                    <select name="facility_ownership_sub_type2" class="select2 form-select form-select-lg errormesage" id="facility_ownership_sub_type2" data-allow-clear="true" required onchange="fetchSubTypethree();">
                                       <option value="">Select</option>
                                    </select>
                                 </div>

                                 <div class="col-sm-3 subtype3dropdown d-none">
                                    <label class="mb-3">Facility Ownership Sub Type - 3<span class="text-danger">*</span></label>
                                    <select name="facility_ownership_sub_type3" class="select2 form-select form-select-lg errormesage" id="facility_ownership_sub_type3" data-allow-clear="true"  >
                                       <option value="">Select</option>
                                    </select>
                                 </div>

                                 <div class="col-sm-3 subtype3text d-none">
                                    <label class="mb-3">Facility Ownership Sub Type - 3<span class="text-danger">*</span></label>
                                    <input type="text" name="facility_ownership_sub_type3text" id="facility_ownership_sub_type3text" value="{{!empty($hospitalDetail) &&  is_string($hospitalDetail->facility_ownership_sub_type3) ? $hospitalDetail->facility_ownership_sub_type3 : ''}}" class="form-control errormesage">
                                 </div>

                                 <div class="col-sm-3">
                                    <label class="mb-3">Date Of Establishment<span class="text-danger">*</span></label>
                                    <input type="text" id="bs-rangepicker-singlee" name="date_of_establishment" value="{{!empty($hospitalDetail) ? $hospitalDetail->date_of_establishment : ''}}" class="form-control errormesage" required />
                                 </div>

                                 <div class="col-sm-3">
                                    <label class="mb-3">Facility Registration Certificate<span class="text-danger">*</span></label>
                                    <select name="facility_registration_certificate"  class="select2 form-select form-select-lg errormesage" id="facility_registration_certificate" data-allow-clear="true" required>
                                       <option value="">Select</option>
                                       @foreach($FacilityRegistrationCertificate as $key => $value)
                                          <option value="{{$value->id}}" {{!empty($hospitalDetail) &&  $hospitalDetail->facility_registration_certificate == $value->id ? 'selected': ''}} >{{$value->name}}</option>
                                       @endforeach
                                    </select>
                                 </div>

                                 <div class="col-sm-3">
                                    <label class="mb-3">Facility Registration Number<span class="text-danger">*</span></label>
                                    <input type="text" oninput="sanitize(this, 'b');" id="facility_registration_number" name="facility_registration_number" class="form-control errormesage"
                                    placeholder="" value="{{!empty($hospitalDetail) ? $hospitalDetail->facility_registration_number : ''}}" required />
                                 </div>
                                 
                                 <div class="col-sm-3">
                                    <label class="mb-3">Registration Certificate Expiry Date<span class="text-danger">*</span></label>
                                    <input type="text" id="bs-rangepicker-singlee-2" name="registration_certificate_expiry" value="{{!empty($hospitalDetail) ? $hospitalDetail->registration_certificate_expiry : ''}}"  class="form-control errormesage" required />
                                 </div>

                                 <div class="col-sm-3">
                                    <label class="mb-3">System(s) of Medicine<span class="text-danger">*</span></label>
                                    <select name="system_of_medicine" class="select2 form-select form-select-lg errormesage"  id="system_of_medicine" data-allow-clear="true" required >
                                       <option value="">Select</option>
                                       @foreach($SystemMedicine as $key => $value)
                                          <option value="{{$value->id}}" {{!empty($hospitalDetail) &&  $hospitalDetail->system_of_medicine == $value->id ? 'selected': ''}}>{{$value->name}}</option>
                                       @endforeach
                                    </select>
                                 </div>

                                 <div class="col-sm-3">
                                    <label class="mb-3">Government Benefits/Concessions</label>
                                    <select name="gov_benifits" class="select2 form-select form-select-lg errormesage" id="gov_benifits" data-allow-clear="true" >
                                       <option value="">Select</option>
                                       @foreach($GovermentBenefits as $key => $value)
                                          <option value="{{$value->id}}" {{!empty($hospitalDetail) &&  $hospitalDetail->gov_benifits == $value->id ? 'selected': ''}} >{{$value->name}}</option>
                                       @endforeach
                                    </select>
                                 </div>

                                 <div class="col-sm-3">
                                    <label class="mb-3">ROHINI ID(ID as allotted by IIB)</label>
                                    <input type="text" id="rohini_id" value="{{!empty($hospitalDetail) ? $hospitalDetail->rohini_id : ''}}" name="rohini_id" class="form-control errormesage"
                                    placeholder=""  />
                                 </div>

                                 <div class="col-sm-3">
                                    <label class="mb-3">Group ID</label>
                                    <input type="text" id="group_id" value="{{!empty($hospitalDetail) ? $hospitalDetail->group_id : ''}}" name="group_id" class="form-control errormesage"
                                    placeholder=""  />
                                 </div>

                                 <div class="col-sm-3">
                                    <label class="mb-3">Name Of Group</label>
                                    <input type="text" id="name_od_group" oninput="sanitize(this, 'b');" value="{{!empty($hospitalDetail) ? $hospitalDetail->name_od_group : ''}}" name="name_od_group" class="form-control errormesage"
                                    placeholder=""  />
                                 </div>


                                 <div class="col-sm-3">
                                    <label for="BMI" class="mb-2">Does this facility has PG/DNB?</label>
                                    <div class="d-flex errormesage">
                                       <div class="form-check">
                                          <input class="form-check-input"
                                                type="radio" name="pg_dnb"
                                                id="option1" value="Y" @if($hospitalDetail && $hospitalDetail->pg_dnb == 'Y') checked @endif>
                                          <label class="form-check-label"
                                                for="option1">
                                                Yes
                                          </label>
                                       </div>
                                       <div class="form-check ms-4">
                                          <input class="form-check-input"
                                                type="radio" name="pg_dnb"
                                                id="option2" value="N"  @if($hospitalDetail && $hospitalDetail->pg_dnb == 'N') checked @endif>
                                          <label class="form-check-label"
                                                for="option2">
                                                No
                                          </label>
                                       </div>
                                    </div>
                                 </div>

                                 <div class="col-12">
                                 <div class="d-flex justify-content-end">
                                       <button class="btn btn-primary" type="button" id="saveEstablishment">Save</button>
                                 </div>
                                 </div>

                              </div>
                           </div>
                        </div>
                     </form>
                  </div>
               </div>
            </div>
            <div class="accordion-item Addressform  @if(!empty($hospitalDetail) && $hospitalDetail->is_added == 1 && empty($address)) active @endif">
               <h2 class="accordion-header" id="headingAddress">
                     <button type="button" class="accordion-button btn2 theme-color @if(!empty($hospitalDetail) && $hospitalDetail->is_added == 1 && empty($address)) collapsed @endif"
                        data-bs-toggle="collapse"
                        data-bs-target="#accordionAddress"
                        aria-expanded="true" aria-controls="accordionAddress">
                        Address @if(!empty($address) && $address->is_added == 1) <i class="tf-icons ri-check-fill text-green"></i>@endif
                     </button>
               </h2>
               <div id="accordionAddress" class="accordion-collapse collapse @if(!empty($hospitalDetail) && $hospitalDetail->is_added == 1 && empty($address)) show @endif" aria-labelledby="headingAddress" data-bs-parent="#accordionPopout">
                  <div class="accordion-body" class="showAddress">
                     <form id="empanelmentaddress">
                        <!-- Communication Address -->
                        <div class="card mb-6 border border-primary">
                           <div class="card-body">                  
                              <div class="row g-5">
                                 <div class="addressmessage text-success"></div>
                                 <div class="col-sm-3">
                                    <label class="mb-3">Address <span class="text-danger">*</span></label>
                                    <input type="text" id="address" oninput="sanitize(this, 'm');" value="{{!empty($address) ? $address->address : ''}}" name="address" class="form-control aerrormesage"
                                    placeholder="Address" required />
                                 </div>
                                 <div class="col-sm-3">
                                    <label class="mb-3">Pincode <span class="text-danger">*</span></label>
                                    <input type="text" id="pincode" oninput="sanitize(this, 'n');" value="{{!empty($address) ? $address->pincode : ''}}" name="pincode" class="form-control aerrormesage"
                                    placeholder="Pincode" required />
                                 </div>
                                 <!-- <div class="col-sm-3">
                                    <label class="mb-3">Block <span class="text-danger">*</span></label>
                                    <input type="text" id="block" value="{{!empty($address) ? $address->block : ''}}" name="block" class="form-control aerrormesage"
                                    placeholder="Block" required />
                                 </div> -->
                                
                                 <div class="col-sm-3">
                                    <label class="mb-3">City/Town <span class="text-danger">*</span></label>
                                    <input type="text" id="city_town" oninput="sanitize(this, 'b');" value="{{!empty($address) ? $address->city : ''}}" name="city" class="form-control aerrormesage"
                                    placeholder="City/Town" required />
                                 </div>

                                 <div class="col-sm-3">
                                    <label class="mb-3">State <span class="text-danger">*</span></label>
                                    <select name="state" id="state" class="select2 form-select form-select-lg aerrormesage" data-allow-clear="true" required onchange="fetchDistrict();">
                                       <option value="">Select</option>
                                       @foreach($state as $key => $value)
                                          <option value="{{$value->id}}" {{!empty($address) && $address->state == $value->id ? 'selected' : ''}}>{{$value->name}}</option>
                                       @endforeach
                                    </select>
                                 </div>

                                 <div class="col-sm-3">
                                    <label class="mb-3">District <span class="text-danger">*</span></label>
                                    <select name="district" id="district" class="select2 form-select form-select-lg aerrormesage" data-allow-clear="true" required onchange="fetchblock();">
                                       <option value="">Select</option>
                                       
                                    </select>
                                 </div>

                                 <div class="col-sm-3">
                                    <label class="mb-3">Block <span class="text-danger">*</span></label>
                                    <select name="block" id="block" class="select2 form-select form-select-lg aerrormesage" data-allow-clear="true" required onchange="fetchVillage();">
                                       <option value="">Select</option>
                                       
                                    </select>
                                 </div>

                                 <div class="col-sm-3">
                                    <label class="mb-3">Village <span class="text-danger">*</span></label>
                                    <select name="village" id="village" class="select2 form-select form-select-lg aerrormesage" data-allow-clear="true" required>
                                       <option value="">Select</option>
                                       
                                    </select>
                                 </div>

                                 <div class="col-sm-3">
                                    <label class="mb-3">Landmark </label>
                                    <input type="text" id="landmark" value="{{!empty($address) ? $address->landmark : ''}}" name="landmark" class="form-control aerrormesage"
                                    placeholder="Landmark"  />
                                 </div>

                                 <div class="col-sm-3">
                                    <label class="mb-3">Telephone With STD Code </label>
                                    <div>
                                    <input type="text" id="std_code" value="{{!empty($address) ? $address->std_code : ''}}" name="std_code" class="form-control aerrormesage"
                                    placeholder="Std Code"  />
                                    <input type="text" id="telephone" value="{{!empty($address) ? $address->telephone : ''}}" name="telephone" class="form-control aerrormesage"
                                    placeholder="Telephone"  />
                                    </div>
                                 </div>

                                 <div class="col-sm-3">
                                    <label class="mb-3">Mobile No <span class="text-danger">*</span></label>
                                    <div class="form-password-toggle aerrormesage">
                                          <input type="number" id="mobile_no" value="{{!empty($address) ? $address->mobile_no : $hfrdata->mobile_no}}" @if(!empty($address) || $hfrdata->mobile_no) readonly @endif name="mobile_no" class="form-control" placeholder="Mobile No" required maxlength="10"/>
                                          
                                    </div>
                                 </div>

                                 <div class="col-sm-3">
                                    <label class="mb-3">Email Id <span class="text-danger">*</span></label>
                                    <input type="text" id="email_id" oninput="sanitize(this, 'email');" value="{{!empty($address) ? $address->email : ''}}" name="email" class="form-control aerrormesage"
                                    placeholder="Email" required />
                                 </div>

                                 <div class="col-sm-3">
                                    <label class="mb-3">Website</label>
                                    <input type="text" id="website" value="{{!empty($address) ? $address->website : ''}}" name="website" class="form-control aerrormesage"
                                    placeholder="Website" />
                                 </div>

                                 <div class="col-sm-3">
                                    <label class="mb-3">Local Police Station <span class="text-danger">*</span></label>
                                    <input type="text" id="local_police_station" oninput="sanitize(this, 't');" name="police_station" class="form-control aerrormesage"
                                    placeholder="Local Police Station" value="{{!empty($address) ? $address->police_station : ''}}" required />
                                 </div>

                                 
                                 <div class="col-sm-3">
                                    <label class="mb-3">Latitude <span class="text-danger">*</span></label>
                                    <input type="text" id="latitude" value="{{!empty($address) ? $address->latitude : ''}}"  name="latitude" class="form-control aerrormesage"
                                    placeholder="Latitude" required />
                                 </div>

                                 <div class="col-sm-3">
                                    <label class="mb-3">Longitude <span class="text-danger">*</span></label>
                                    <input type="text" id="longitude" value="{{!empty($address) ? $address->longitude : ''}}"  name="longitude" class="form-control aerrormesage"
                                    placeholder="Longitude" required />
                                 </div>

                                 <div class="col-sm-3">
                                    <label for="BMI" class="mb-2">Locality <span class="text-danger">*</span></label>
                                    <div class="d-flex aerrormesage">
                                       <div class="form-check">
                                          <input class="form-check-input"
                                                type="radio" name="locality"
                                                id="option3" {{!empty($address) && $address->locality == "Rural" ? 'checked' : ''}} value="Rural">
                                          <label class="form-check-label"
                                                for="option3">
                                                Rural
                                          </label>
                                       </div>
                                       <div class="form-check ms-4">
                                          <input class="form-check-input"
                                                type="radio" name="locality"
                                                id="option4" {{!empty($address) && $address->locality == "Urban" ? 'checked' : ''}}  value="Urban">
                                          <label class="form-check-label"
                                                for="option4">
                                                Urban
                                          </label>
                                       </div>
                                    </div>
                                 </div>


                                 <div class="col-12">
                                    <div class="d-flex justify-content-end">
                                       <button type="button" class="btn btn-primary saveAddress" >Save</button>
                                    </div>
                                 </div>                                
                              </div>
                           </div>
                        </div>
                     </form>
                  </div>
               </div>
            </div>
         </div>

         

         <div class="col-12 ShowStep mt-2" style="@if(!empty($hospitalDetail) && !empty($address))display:block; @else display:none @endif">
            <div class="d-flex justify-content-end">               
               <!-- <button class="btn btn-outline-primary btn-primary ms-3">Preview</button> -->
               <a class="btn btn-primary ms-2 " id="NextStepButton" href="{{route('hospital.empanelmentRegistration.schemeDetails', $uuid)}}">Next</a>
            </div>
         </div>
         
      </div>
   </div>
</div>
@endsection

@push('scripts')

<script>

   bsRangePickerSingle = $('#bs-rangepicker-singlee'),
   bsRangePickerSingle2 = $('#bs-rangepicker-singlee-2');
	if (bsRangePickerSingle.length) {
      bsRangePickerSingle.daterangepicker({
         locale: {
            format: 'YYYY-MM-DD',
            cancelLabel: 'Clear'
         },
         singleDatePicker: true,
         maxDate: moment(),
         autoUpdateInput: true,
         showDropdowns: true,
         opens: 'left'
      });
      @if(!$hospitalDetail)
         bsRangePickerSingle.val('');
      @endif

      bsRangePickerSingle.on('cancel.daterangepicker', function(ev, picker) {
         $(this).val('');
      });
	}

   if (bsRangePickerSingle2.length) {
      bsRangePickerSingle2.daterangepicker({
         locale: {
            format: 'YYYY-MM-DD',
            cancelLabel: 'Clear'
         },
         singleDatePicker: true,
         minDate: moment().add(1, 'days'), 
         autoUpdateInput: true,
         showDropdowns: true,
         opens: 'left'
      });
      
      @if(!$hospitalDetail)
         bsRangePickerSingle2.val('');
      @endif

      bsRangePickerSingle2.on('cancel.daterangepicker', function(ev, picker) {
         $(this).val('');
      });
	}

   @if(!empty($hospitalDetail))
      fetchSubType('{{$hospitalDetail->facility_ownership_type}}', '{{$hospitalDetail->facility_ownership_sub_type1}}', '{{$hospitalDetail->facility_ownership_sub_type2}}');

      fetchSubTypetwo('{{$hospitalDetail->facility_ownership_type}}', '{{$hospitalDetail->facility_ownership_sub_type1}}', '{{$hospitalDetail->facility_ownership_sub_type2}}');

      setTimeout(() => {
         fetchSubTypethree('{{$hospitalDetail->facility_ownership_type}}', '{{$hospitalDetail->facility_ownership_sub_type1}}', '{{$hospitalDetail->facility_ownership_sub_type2}}', '{{$hospitalDetail->facility_ownership_sub_type3}}');         
      }, 2000);

   @endif
   @if(!empty($address))
      fetchDistrict('$address->state', '{{$address->district}}');
      fetchblock('$address->state', '{{$address->district}}', '{{$address->block}}');
      fetchVillage('$address->state', '{{$address->district}}', '{{$address->village}}', '{{$address->block}}');
   @endif
   function fetchSubType(type = '', type1 = '', type2 = '') {
      
      let typeId = $('#facility_ownership_type').val(); // Get selected type ID
      if(!typeId) {
         typeId = type;
      }
      if (typeId) {
         $.ajax({
               url: '{{route("hospital.empanelmentRegistration.facility_ownership_sub_type")}}', 
               type: 'POST',
               data: {
                  '_token': '{{csrf_token()}}',
                  'facility_ownership_type_id' : typeId
               },
               dataType: 'json',
               success: function (data) {
                  // Clear previous options
                  $('#facility_ownership_sub_type1').empty().append('<option value="">Select</option>');
                  $('#facility_ownership_sub_type2').empty().append('<option value="">Select</option>');

                  // Populate new options
                  $.each(data.type1, function (key, subType) {
                     var selected = '';
                     var selected2 = '';
                     if(type1 == subType.id) {
                        selected = 'selected';
                     }
                     if(type2 == subType.id) {
                        selected2 = 'selected';
                     }
                     $('#facility_ownership_sub_type1').append(`<option value="${subType.id}" data-title="${subType.name}" ${selected} data-type1="${subType.name}">${subType.name}</option>`);
                     
                     if(type1 == subType.id) {
                        var selectOption = $("#facility_ownership_sub_type1").find('option:selected');
                        var title = selectOption.data('title');
                        $('.propcertificate').addClass('d-none');
                        $('.subtypecertificate').addClass('d-none');

                        if(title == "Partnership" || title == "Society" || title == "Trust") {
                           $('#certificateName').text(title + " Certificate");
                           $('#sub_type_certificate_name').val(title + " Certificate");
                           $('.subtypecertificate').removeClass('d-none');
                        } else {
                           $('.subtypecertificate').addClass('d-none');
                        }

                        if(title == "Propiertship") {
                           $(".propcertificate").removeClass('d-none');
                        } else {
                           $('.propcertificate').addClass('d-none');
                        }
                     }

                  });
                  $.each(data.type2, function (key, subType) {
                     var selected = '';
                     var selected2 = '';
                     if(type1 == subType.id) {
                        selected = 'selected';
                     }
                     if(type2 == subType.id) {
                        selected2 = 'selected';
                     }
                     $('#facility_ownership_sub_type2').append(`<option value="${subType.id}" ${selected2}>${subType.name}</option>`);
                  });
               },
               error: function () {
                  alert('Failed to fetch subtypes. Please try again.');
               }
         });
         
      } else {
         // Clear subtypes if no type is selected
         $('#facility_ownership_sub_type1').empty().append('<option value="">Select</option>');
         $('#facility_ownership_sub_type2').empty().append('<option value="">Select</option>');
      }
   }

   function fetchSubTypetwo(ownershiptype = '', type1 = '', type2 = '') {
      
      let typeId = $('#facility_ownership_type').val(); // Get selected type ID
      let typ1 = $('#facility_ownership_sub_type1').val(); // Get selected type ID
      if(!typ1) {
         typ1 = type1;
      }
      if (typ1) {
         $.ajax({
               url: '{{route("hospital.empanelmentRegistration.facility_ownership_sub_type2")}}', 
               type: 'POST',
               data: {
                  '_token': '{{csrf_token()}}',
                  'facility_ownership_type_id' : typeId,
                  'type1' : typ1
               },
               dataType: 'json',
               success: function (data) {
                  // Clear previous options
                  $('#facility_ownership_sub_type2').empty().append('<option value="">Select</option>');

                  $.each(data, function (key, subType) {
                     var selected = '';
                     var selected2 = '';
                     if(type1 == subType.id) {
                        selected = 'selected';
                     }
                     if(type2 == subType.id) {
                        selected2 = 'selected';
                     }
                     $('#facility_ownership_sub_type2').append(`<option value="${subType.id}" ${selected2} data-title="${subType.name}">${subType.name}</option>`);
                  });
               },
               error: function () {
                  alert('Failed to fetch subtypes. Please try again.');
               }
         });

         var selectOption = $("#facility_ownership_sub_type1").find('option:selected');
         var title = selectOption.data('title');
         $('.propcertificate').addClass('d-none');
         $('.subtypecertificate').addClass('d-none');

         if(title == "Partnership" || title == "Society" || title == "Trust") {
            $('#certificateName').text(title + " Certificate");
            $('#sub_type_certificate_name').val(title + " Certificate");
            $('.subtypecertificate').removeClass('d-none');
         } else {
            $('.subtypecertificate').addClass('d-none');
         }

         if(title == "Propiertship") {
            $(".propcertificate").removeClass('d-none');
         } else {
            $('.propcertificate').addClass('d-none');
         }
         
      } else {
         // Clear subtypes if no type is selected
         $('#facility_ownership_sub_type2').empty().append('<option value="">Select</option>');
      }
   }

   function fetchSubTypethree(ownershiptype = '', type1 = '', type2 = '', type3 = '') {
      @if(!$hospitalDetail)
      $('.subtype3text').addClass('d-none');
      $('.subtype3dropdown').addClass('d-none');
      @endif
      $("#facility_ownership_sub_type3").val("");
      $("#facility_ownership_sub_type3text").val("");
      let typeId = $('#facility_ownership_type').val(); 
      let typ1 = $('#facility_ownership_sub_type1').val(); 
      let typ2 = $('#facility_ownership_sub_type2').val(); 

      var selectedOption = $("#facility_ownership_sub_type2").find('option:selected');  
      var title = selectedOption.data('title');

      var selectedOptiontype1 = $("#facility_ownership_sub_type1").find('option:selected');  
      var type1text = selectedOptiontype1.data('type1');
      
      if(type1text == "Central" && (title == "PSU" || title == 'psu')) {
         $('.subtype3text').removeClass('d-none');
         $('.subtype3dropdown').addClass('d-none');
         @if($hospitalDetail)
            $("#facility_ownership_sub_type3text").val('{{$hospitalDetail->facility_ownership_sub_type3}}');
         @endif
      } else {
         var title = selectedOption.data('title'); 
         if(!typ2) {
            typ2 = type2;
         }
         if (typ2) {
            $.ajax({
                  url: '{{route("hospital.empanelmentRegistration.facility_ownership_sub_type3")}}', 
                  type: 'POST',
                  data: {
                     '_token': '{{csrf_token()}}',
                     'facility_ownership_type_id' : typeId,
                     'type1' : typ1,
                     'type2' : $("#facility_ownership_sub_type2").val(), 
                  },
                  dataType: 'json',
                  success: function (data) {
                     if(data.length > 0) {
                        @if($hospitalDetail)
                           $("#facility_ownership_sub_type3text").val('');
                        @endif
                        $('.subtype3dropdown').removeClass('d-none');
                        $('#facility_ownership_sub_type3').empty().append('<option value="">Select</option>');

                        $.each(data, function (key, subType) {
                           var selected = '';
                           var selected2 = '';
                           if(type3 == subType.id) {
                              selected2 = 'selected';
                           }
                           $('#facility_ownership_sub_type3').append(`<option value="${subType.id}" ${selected2} >${subType.name}</option>`);
                        });
                     } else {
                        $('.subtype3dropdown').addClass('d-none');
                     }                 
                  },
                  error: function () {
                     alert('Failed to fetch subtypes. Please try again.');
                     $('.subtype3dropdown').addClass('d-none');
                  }
               });
         } else {
            // Clear subtypes if no type is selected
            $('#facility_ownership_sub_type3').empty().append('<option value="">Select</option>');
            $('.subtype3dropdown').addClass('d-none');
         }
      }
   }

   function fetchDistrict(state = '', district = '') {
      let state_id = $('#state').val(); // Get selected type ID
      if(!state_id) {
         state_id = state_id;
      }
      if (state_id) {
         $.ajax({
               url: '{{route("hospital.empanelmentRegistration.getDistrict")}}', 
               type: 'POST',
               data: {
                  '_token': '{{csrf_token()}}',
                  'state_id' : state_id
               },
               dataType: 'json',
               success: function (data) {
                  // Clear previous options
                  $('#district').empty().append('<option value="">Select</option>');
                  $('#district').empty().append('<option value="">Select</option>');

                  // Populate new options
                  $.each(data, function (key, subType) {
                     var selected = '';
                     var selected2 = '';
                     if(district == subType.id) {
                        selected = 'selected';
                     }
                     
                     $('#district').append(`<option value="${subType.id}" ${selected}>${subType.name}</option>`);
                  });
               },
               error: function () {
                  alert('Failed to fetch subtypes. Please try again.');
               }
         });
      } else {
         // Clear subtypes if no type is selected
         $('#district').empty().append('<option value="">Select</option>');
      }
   }

   function fetchVillage(state = '', district = '', village = '', block = '') {
      let district_id = $('#district').val(); // Get selected type ID
      let block_id = $('#block').val(); // Get selected type ID
      if(!district_id) {
         district_id = district;
      }

      if(!block_id) {
         block_id = block;
      }
      if (district_id) {
         $.ajax({
               url: '{{route("hospital.empanelmentRegistration.getVillage")}}', 
               type: 'POST',
               data: {
                  '_token': '{{csrf_token()}}',
                  'district_id' : district_id,
                  'block_id' : block_id
               },
               dataType: 'json',
               success: function (data) {
                  // Clear previous options
                  $('#village').empty().append('<option value="">Select</option>');

                  // Populate new options
                  $.each(data, function (key, subType) {
                     var selected = '';
                     var selected2 = '';
                     if(village == subType.id) {
                        selected = 'selected';
                     }
                     
                     $('#village').append(`<option value="${subType.id}" ${selected}>${subType.name}</option>`);
                  });
               },
               error: function () {
                  alert('Failed to fetch subtypes. Please try again.');
               }
         });
      } else {
         // Clear subtypes if no type is selected
         $('#village').empty().append('<option value="">Select</option>');
      }
   }
   
   function fetchblock(state = '', district = '', block = '') {
      let district_id = $('#district').val(); // Get selected type ID
      if(!district_id) {
         district_id = district;
      }
      if (district_id) {
         $.ajax({
               url: '{{route("hospital.empanelmentRegistration.getBlocks")}}', 
               type: 'POST',
               data: {
                  '_token': '{{csrf_token()}}',
                  'district_id' : district_id
               },
               dataType: 'json',
               success: function (data) {
                  // Clear previous options
                  $('#block').empty().append('<option value="">Select</option>');

                  // Populate new options
                  $.each(data, function (key, subType) {
                     var selected = '';
                     var selected2 = '';
                     if(block == subType.id) {
                        selected = 'selected';
                     }
                     
                     $('#block').append(`<option value="${subType.id}" ${selected}>${subType.name}</option>`);
                  });
               },
               error: function () {
                  alert('Failed to fetch subtypes. Please try again.');
               }
         });
      } else {
         // Clear subtypes if no type is selected
         $('#block').empty().append('<option value="">Select</option>');
      }
   }

   $('#saveEstablishment').click(function () {
      ldrshow();
      $('.error').remove();
        // Create a FormData object
        var formData = new FormData($('#establishmentDetails')[0]);
        // Send an AJAX request
        $.ajax({
            url: '{{route("hospital.empanelmentRegistration.hospitalsCreate", $uuid)}}', // Replace with your server endpoint
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            type: 'POST',
            data: formData,
            processData: false, // Prevent jQuery from automatically processing the data
            contentType: false, // Prevent jQuery from automatically setting content type
            success: function (response) {
               ldrhide();
                // alert('Form submitted successfully!');
               $('.Estmessage').text(response.message);
               $('.Addressform').addClass('active');
               $('.establishment').removeClass('active');
               $('#accordionAddress').addClass('show');
               $('#accordionPopoutOne').removeClass('show');
               $('.btn1').removeClass('show').addClass('theme-color collapsed');
               successMessage(response.message);
            },
            error: function (xhr) {
               ldrhide();
                $('.error').remove();
                
                if (xhr.status === 422) { 
                    let errors = xhr.responseJSON.errors;
                    let errorMessages = [];
                    for (let field in errors) {
                     let fieldElement = $(`[name="${field}"]`);
                        if (fieldElement.length > 0) {
                              if (fieldElement.attr('type') === 'file') {
                                 // Handle file input errors differently if needed
                                 fieldElement.closest('.imgerror').after().append(`<div class="error text-danger">${errors[field][0]}</div>`);
                              } else if ($(`select[name="${field}"]`).length > 0) {
                                 fieldElement.parent().append(`<div class="error text-danger">${errors[field][0]}</div>`);
                              } else {
                                 fieldElement.after(`<div class="error text-danger">${errors[field][0]}</div>`);
                              }
                        }
                        errorMessages.push(errors[field][0]);
                    }
                    if (errorMessages.length > 0) {
                        errorMessage(errorMessages.join('<br>'));
                    }
                } else {
                    alert('Something went wrong. Please try again later.');
                }
            }
        });
    });

    $('.saveAddress').click(function () {
         ldrshow();
         $('.error').remove();
         // Create a FormData object
         var formData = new FormData($('#empanelmentaddress')[0]);
         // Send an AJAX request
         $.ajax({
            url: '{{route("hospital.empanelmentRegistration.hospitalsAddressCreate", $uuid)}}', // Replace with your server endpoint
            headers: {
               'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            type: 'POST',
            data: formData,
            processData: false, // Prevent jQuery from automatically processing the data
            contentType: false, // Prevent jQuery from automatically setting content type
            success: function (response) {
               ldrhide();
               // alert('Form submitted successfully!');
               $('.addressmessage').text(response.message);
               $('.Addressform').removeClass('active');
               $('.establishment').removeClass('active');
               $('#accordionAddress').removeClass('show');
               $('#accordionPopoutOne').removeClass('show');
               $('.btn1').removeClass('show').addClass('theme-color collapsed');
               $('.btn2').removeClass('show').addClass('theme-color collapsed');

               $(".ShowStep").show();
               successMessage(response.message);
            },
            error: function (xhr) {
               ldrhide();
               $('.error').remove();
               
               if (xhr.status === 422) { 
                  let errors = xhr.responseJSON.errors;
                  for (let field in errors) {
                        $(`[name="${field}"]`).closest('.aerrormesage').after(`<div class="error text-danger">${errors[field][0]}</div>`);
                  }
               } else {
                  alert('Something went wrong. Please try again later.');
               }
            }
         });
    });
    
</script>
@endpush