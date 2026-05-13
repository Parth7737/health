@extends('layouts.preauth.app')
@section('title','Register Patient')
@section('content')
<div class="container-xxl flex-grow-1 container-p-y mb-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="javascript:void(0);">Home</a>
            </li>
            <li class="breadcrumb-item active">Register Patient</li>
        </ol>
    </nav>
    <div class="row">
        <div class="bs-stepper-content mt-4">
            <div class="card mb-6 ps-0 border border-primary">
                <div class="card-body">
                    <div class="row row-cols-5">
                        <div class="col">
                            <div
                                class="d-flex text-center justify-content-center flex-column border-end border-secondary">
                                <div class="position-relative image-overlay">
                                    <img src="{{ url('public/storage/benificiary/'.$benificiary->image) }}" width="80"
                                        alt="avatar" class="mb-3 rounded-circle" />
                                </div>
                                <span class="number-3 mb-2">{{ $benificiary->name }}</span>
                                <span class="number-2">{{ $benificiary->age }} Yr / {{ $benificiary->gender }}</span>
                            </div>
                        </div>
                        <div class="col">
                            <div class="infodata">
                                <label>Mobile Number</label>
                                <p>{{ $benificiary->mobile_no }}</p>
                            </div>
                        </div>
                        <div class="col">
                            <div class="infodata">
                                <label>SGHS ID</label>
                                <p><strong>{{ $benificiary->card_id }}</strong></p>
                            </div>
                        </div>
                        <div class="col">
                            <div class="infodata">
                                <label>Address</label>
                                <p>{{ $benificiary->address }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="bs-stepper-content">
                    <form id="registerForm" onSubmit="return false" method="post" enctype='multipart/form-data'>
                        @csrf
                        <!-- Communication Address -->
                        <div class="mb-6">
                            
                            <div class="inside-left-info-box pending communication-address">
                                
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" value="1" name="new_born_baby"
                                        id="new_born_baby" />
                                    <label class="form-check-label" for="new_born_baby">New Born Baby</label>
                                </div>
                                <div class="row g-5 born-baby-field d-none">
                                    <div class="card-title header-elements">
                                        <h5 class="m-0 me-2 theme-color">Born Baby Details</h5>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" id="born_baby_dob" oninput="sanitize(this, 'd');" class="form-control" name="born_baby_dob"
                                                placeholder=""/>
                                            <label for="born_baby_dob">DOB<span class="text-danger">*</span></label>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" id="born_baby_name" oninput="sanitize(this, 't');" class="form-control" name="born_baby_name"
                                                placeholder=""/>
                                            <label for="born_baby_name">Full Name<span class="text-danger">*</span></label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div
                                            class="form-floating form-floating-outline">
                                            <select class="form-select select2"
                                                id="born_baby_gender"
                                                name="born_baby_gender">
                                                <option value="">Select Gender</option>
                                                <option value="Male">Male</option>
                                                <option value="Female">Female</option>
                                                <option value="Other">Other</option>
                                            </select>
                                            <label for="born_baby_gender">Gender</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-6">
                                        <label for="formFile"
                                            class="form-label">Birth Certificate<span class="text-danger">*</span></label>
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
                                            <input type="file" name="born_baby_birth_certificate"
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
                                        </div>
                                    </div>
                                </div>
                                <div class="card-title header-elements">
                                    <h5 class="m-0 me-2 theme-color">Communication Address</h5>
                                </div>
                                <div class="row g-5">
                                    <div class="col-12">
                                        <div class="form-check mt-4">
                                            <input class="form-check-input" type="checkbox" value="1"
                                                id="card_address" />
                                            <label class="form-check-label" for="card_address"> Current
                                                Communication address is same as card address</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" id="pincode" oninput="sanitize(this, 'n','8');" class="form-control" name="pincode"
                                                placeholder="250001" required/>
                                            <label for="pincode">Pincode<span class="text-danger">*</span></label>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-floating form-floating-outline">
                                            <select id="state_id" name="state_id" class="select2 form-select form-select-lg"  aria-label="" required>
                                            <option value="">Select State</option>
                                                @foreach($states as $key => $value)
                                                <option value="{{$value->id}}">{{$value->name}}</option>
                                                @endforeach
                                            </select>
                                            <label for="state_id">State<span class="text-danger">*</span></label>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-floating form-floating-outline">
                                            <select id="district_id" name="district_id" class="select2 form-select form-select-lg"  aria-label="" required>
                                                <option value="">Select District</option>
                                                @foreach($districts as $key => $value)
                                                <option value="{{$value->id}}">{{$value->name}}</option>
                                                @endforeach
                                            </select>
                                            <label for="district_id">District<span class="text-danger">*</span></label>
                                        </div>
                                    </div>
                                    <div class="col-sm-3 form-password-toggle">
                                        <div class="input-group input-group-merge">
                                            <div class="form-floating form-floating-outline">
                                                <input type="text" id="city" name="city" oninput="sanitize(this, 't');" class="form-control" placeholder="Haryana" required/>
                                                <label for="Village | Town | City">Village | Town | City<span class="text-danger">*</span></label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" id="address" name="address" oninput="sanitize(this, 'm');" class="form-control"
                                                placeholder="Type here" required/>
                                            <label for="Pincode">Address Line<span class="text-danger">*</span></label>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" id="address_2" name="address_2" oninput="sanitize(this, 'm');" class="form-control"
                                                placeholder="Type here"/>
                                            <label for="Pincode">Address Line2</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-6">
                            <div class="inside-left-info-box theme-color care-plan">
                                <div class="card-title header-elements">
                                    <h5 class="m-0 me-2 theme-color mb-3">Care Plan</h5>
                                </div>

                                <div class="row g-5">
                                    <div class="col-sm-6">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" id="care_plan" name="care_plan" class="form-control" value="{{ $benificiary->care_plan }}"
                                                placeholder="SGHS for SECC Beneficiaries" readonly />
                                            <label for="Care Plan">Scheme Type</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-floating form-floating-outline">
                                            <select id="patient_type" name="patient_type"
                                                class="select2 form-select form-select-lg"
                                                data-allow-clear="true">
                                                <option value="Inpatient">Inpatient</option>
                                                <option value="Outpatient">Outpatient</option>
                                            </select>
                                            <label for="patient_type">Patient Status Type<span class="text-danger">*</span></label>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="mb-6">
                            <div class="inside-left-info-box pending patient-attendant-details">
                                <div class="card-title header-elements">
                                    <h5 class="m-0 me-2 theme-color mb-3">Patient Attendant Details</h5>
                                </div>
                                <div class="row g-5">
                                    <div class="col-sm-12">
                                        <div class="d-flex">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="attendant_patient"
                                                    id="self" value="self" checked>
                                                <label class="form-check-label" for="self">
                                                    Self
                                                </label>
                                            </div>
                                            <div class="form-check ms-4">
                                                <input class="form-check-input" type="radio" name="attendant_patient"
                                                    id="other" value="other">
                                                <label class="form-check-label" for="other">
                                                    Other
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="row">
                                            <div class="col-sm-4">
                                                <div class="form-floating form-floating-outline">
                                                    <input type="text" id="full_name" name="full_name" oninput="sanitize(this, 't');" class="form-control" value="{{ $benificiary->name }}" readonly
                                                        placeholder="Full Name" />
                                                    <label for="Full Name">Full Name</label>
                                                </div>
                                            </div>
                                            <div class="col-sm-4 other-relation-field d-none">
                                                <div class="form-floating form-floating-outline">
                                                    <select id="relationship" name="relationship"
                                                        class="select2 form-select form-select-lg"
                                                        data-allow-clear="true">
                                                        <option value="">Select Relation</option>
                                                        <option value="Father">Father</option>
                                                        <option value="Son">Son</option>
                                                        <option value="Daughter">Daughter</option>
                                                        <option value="Wife">Wife</option>
                                                        <option value="Husband">Husband</option>
                                                        <option value="Mother-in-law">Mother-in-law</option>
                                                        <option value="Father-in-law">Father-in-law</option>
                                                        <option value="Other">Other</option>
                                                    </select>
                                                    <label for="relationship">Relationship<span class="text-danger">*</span></label>
                                                </div>
                                            </div>
                                            <div class="col-sm-4 other-relationship-field d-none">
                                                <div class="form-floating form-floating-outline">
                                                    <input type="text" id="other_relation" name="other_relation" oninput="sanitize(this, 't');" class="form-control"
                                                        placeholder="Other Relation" />
                                                    <label for="other_relation">Other Relation<span class="text-danger">*</span></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4 ">
                                        <div class="form-password-toggle">
                                            <div class="input-group input-group-merge">
                                                <div class="form-floating form-floating-outline">
                                                    <input type="number" class="form-control"
                                                        id="mobile_no" name="mobile_no" required  oninput="sanitize(this, 'n','10');"
                                                        placeholder="1234567890">
                                                    <label for="basic-default-password12">Mobile
                                                        Number<span class="text-danger">*</span></label>
                                                </div>
                                                <button type="button" class="input-group-text cursor-pointer theme-color" disabled id="verify" onclick="SendOTPOnMobile()">VERIFY</button>
                                                <!-- <i class="ri-checkbox-fill theme-color"></i> -->
                                            </div>
                                            <span id="mobile-error" class="text-danger"></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-4 aadhar-otp-field d-none">
                                        <div class="form-password-toggle">
                                            <div class="input-group input-group-merge">
                                                <div class="form-floating form-floating-outline">
                                                    <input type="number" class="form-control" oninput="sanitize(this, 'n','8');" id="mobile_otp" name="mobile_otp" placeholder="123456">
                                                    <label for="basic-default-password12">Enter OTP<span class="text-danger">*</span></label>
                                                </div>
                                                <button type="button" class="input-group-text cursor-pointer theme-color" disabled id="resend-otp" onclick="ReSendOTPOnMobile()">RESEND OTP</button>
                                            </div>
                                            <span id="otp-error" class="text-danger"></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-4 aadhar-otp-field d-none">
                                        <button type="button" class="otp-btn" id="verify-otp" disabled onclick="VerifyOtp()">VERIFY OTP</button>
                                    </div>

                                </div>
                            </div>
                        </div>
                        @if($kyc_type == 'without_auth')
                            <div class="mb-6">
                                <div class="inside-left-info-box pending authentications-consent">
                                    <div class="card-title header-elements">
                                        <h5 class="m-0 me-2 theme-color mb-3">Authentications Consent</h5>
                                    </div>
                                    <div class="row align-items-center g-5">
                                        <div class="col-sm-4">
                                            <label for="Remarks" class="mb-3">Hospital Declaration Form<span class="text-danger">*</span><br/> <small>(Allow only pdf format with maximux file size should be 2MB)</small></label>
                                            <div class="mb-4">
                                                <div class="file-upload-section">
                                                    <div class="file-upload-wrapper">
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            height="24px"
                                                            viewBox="0 -960 960 960"
                                                            width="24px" fill="#6200ea">
                                                            <path
                                                                d="M260-160q-91 0-155.5-63T40-377q0-78 47-139t123-78q25-92 100-149t170-57q117 0 198.5 81.5T760-520q69 8 114.5 59.5T920-340q0 75-52.5 127.5T740-160H520q-33 0-56.5-23.5T440-240v-206l-64 62-56-56 160-160 160 160-56 56-64-62v206h220q42 0 71-29t29-71q0-42-29-71t-71-29h-60v-80q0-83-58.5-141.5T480-720q-83 0-141.5 58.5T280-520h-20q-58 0-99 41t-41 99q0 58 41 99t99 41h100v80H260Zm220-280Z" />
                                                        </svg>
                                                        <p>Drag your file here or
                                                            <strong>Browse</strong></p>
                                                    </div>
                                                    <input type="file"
                                                        class="file-input d-none" name="hospital_declaration_form" />
                                                    <div
                                                        class="uploaded-file file-upload-display d-none">
                                                        <span
                                                            class="file-name">Sample.pdf</span>
                                                        <i class="fas fa-trash "></i>
                                                        <button type="button"
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
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-floating form-floating-outline">
                                                <input type="text" id="remarks" name="remarks" class="form-control"
                                                    placeholder="Remarks" />
                                                <label for="remarks">Remarks<span class="text-danger">*</span></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="col-12">
                            <div class="form-check mt-4">
                                <input class="form-check-input" type="checkbox" value="1" id="is_own_number"
                                    />
                                <label class="form-check-label" for="is_own_number"> This mobile number is owned by benificary/benificiary family and not by PMAM.</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-check mt-4">
                                <input class="form-check-input" type="checkbox" value="" id="terms"
                                    />
                                <label class="form-check-label" for="terms">I consent to share my Personally Identifiable Information (PII) including heath data with State Heath Authority (SHA) in order to available services under State Government Health Scheme (SGHS). I understand that my Personally Identifiable Information (PII) including health data will be securely stored with SHA on permanent retention period. I have been duly informed that my information as stated above will be shared SHA empaneled hospitals, Insurance Agencies (ISAs) and State Health Agencies (SHA) for SGHS operations. I agree to receive feedback/survey calls & SMS on the number shared, made by third party on behalf of SHA.</label>
                            </div>
                        </div>
                    </form>
                    <div class="col-12">
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-primary submitbtn" disabled>Register</button>
                            <a href="{{ route('preauth.dashboard') }}" class="btn btn-outline-primary btn-primary ms-3">Back</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
    $(".select2").select2();
    flag=0;
    $("#card_address").on("change",function(){
        if($(this).is(":checked")){
            $("#pincode").val("{{ $benificiary->pincode }}");
            $("#state_id option").filter(function() {
                return $(this).text().trim().toLowerCase() === "{{ strtolower($benificiary->state_name) }}";
            }).prop("selected", true).change();
            $("#district_id option").filter(function() {
                return $(this).text().trim().toLowerCase() === "{{ strtolower($benificiary->dist_name) }}";
            }).prop("selected", true).change();
            // $("#city").val("{{ $benificiary->city }}");
            $("#address").val("{{ $benificiary->address }}");
            
        }
    })
    $("input[name='attendant_patient']").on("change",function(){
        if($("input[name='attendant_patient']:checked").val() == 'self'){
            $(".other-relation-field").addClass("d-none");
            $(".other-relationship-field").addClass("d-none");
            $("#full_name").attr('readonly',true);
            $("#relationship").attr('required',false);
            $("#relationship").val('');
            $("#other_relation").val('');
        }else{
            $(".other-relation-field").removeClass("d-none");
            $("#full_name").attr('readonly',false);
            $("#relationship").attr('required',true);
        }
    });

    $("#relationship").on("change",function(){
        if($(this).val() == 'Other'){
            $(".other-relationship-field").removeClass("d-none");
            $("#other_relation").attr('required',true);
        }else{
            $(".other-relationship-field").addClass("d-none");
            $("#other_relation").attr('required',false);
        }
    })
    $("#mobile_no").on("change",function(){
        $("#verify").attr("disabled",false);
        $("#resend-otp").attr("disabled",true);
        $("#verify-otp").attr("disabled",true);
        
    })
    // Send OTP to Mobile
    function SendOTPOnMobile() {
        let mobile = $('#mobile_no').val();
        if(mobile != ''){
            $("#mobile-error").text("");
            fetch('{{route("preauth.send-mobile-otp")}}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ mobile })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    $(".aadhar-otp-field").removeClass('d-none');
                    $("#mobile_otp").val(data.otp);
                    $("#verify").attr('disabled',true);
                    $("#resend-otp").attr("disabled",false);
                    $("#verify-otp").attr("disabled",false);
                } else {
                    $("#mobile-error").text(data.message);
                }
            });
        }else{
            $("#mobile-error").text("Please enter mobile number");
        }
    }
    // Re-Send OTP to Mobile
    function ReSendOTPOnMobile() {
        let mobile = $('#mobile_no').val();
        if(mobile != ''){
            $("#mobile-error").text("");
            fetch('{{route("preauth.resend-mobile-otp")}}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ mobile })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    $("#mobile_otp").val(data.otp);
                } else {
                    $("#mobile-error").text(data.message);
                }
            });
        }else{
            $("#mobile-error").text("Please enter mobile number");
        }
    }
    function VerifyOtp(){
        
        let mobile = $('#mobile_no').val();
        let otp = $('#mobile_otp').val();
        if(otp != ''){
            $("#otp-error").text("");
            fetch('{{route("preauth.verify-mobile-otp")}}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ mobile,otp })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    $("#mobile_no").attr("disabled",true);
                    $("#mobile_otp").attr("disabled",true);
                    $("#verify").attr("disabled",true);
                    $("#verify").html("<i class='ri-checkbox-fill theme-color'></i>");
                    $("#resend-otp").html("<i class='ri-checkbox-fill theme-color'></i>");
                    $("#resend-otp").attr("disabled",true);
                    $("#verify-otp").attr("disabled",true);
                    successMessage('OTP Verifed');
                    $(".aadhar-otp-field").addClass("d-none");
                    flag=1;
                } else {
                    $("#otp-error").text(data.message);
                }
            });
        }else{
            $("#otp-error").text("Please enter OTP");
        }
    }
    $('.submitbtn').click(function () {
        $('.error').remove();
        // Create a FormData object
        var formData = new FormData($('#registerForm')[0]);
        formData.append('mobile_no', $("#mobile_no").val());
        // Send an AJAX request
        if(flag != 1){
            errorMessage("Please Verify a mobile otp.");
            return false;
        }
        $.ajax({
            url: '{{route("preauth.register-patient.store")}}', // Replace with your server endpoint
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            type: 'POST',
            data: formData,
            processData: false, // Prevent jQuery from automatically processing the data
            contentType: false, // Prevent jQuery from automatically setting content type
            success: function (response) {
                Swal.fire({
                    title: response.message,
                    text: ' Registration Number: '+response.register_id,
                    icon: 'success',
                    customClass: {
                    confirmButton: 'btn btn-primary waves-effect waves-light'
                    },
                    buttonsStyling: false
                }).then(() => {
                    window.location.href = '{{ route("preauth.dashboard") }}';
                });
            },
            error: function (xhr) {
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
    });
    $("#terms").on("change",function(){
        if($(this).is(":checked")){
            $(".submitbtn").attr('disabled',false);
        }else{
            $(".submitbtn").attr('disabled',true);
        }
    })
    $(".communication-address input, .communication-address select").on("change", function () {
        let isValid = true;

        $(".communication-address input[type='text'][required]").each(function () {
            if ($(this).val().trim() === '') {
                isValid = false;
                return false; // Break out of loop early
            }
        });

        if (isValid) {
            $(".communication-address select[required]").each(function () {
                if ($(this).val().trim() === '') {
                    isValid = false;
                    return false; // Break out of loop early
                }
            });
        }

        if (isValid) {
            $(".communication-address").addClass('theme-color').removeClass('pending');
        } else {
            $(".communication-address").removeClass('theme-color').addClass('pending');
        }
    });

    $(".patient-attendant-details input, .patient-attendant-details select").on("change", function () {
        let isValid = true;

        $(".patient-attendant-details input[type='text'][required]").each(function () {
            if ($(this).val().trim() === '') {
                isValid = false;
                return false; // Break out of loop early
            }
        });

        if (isValid) {
            $(".patient-attendant-details select[required]").each(function () {
                if ($(this).val().trim() === '') {
                    isValid = false;
                    return false; // Break out of loop early
                }
            });
        }

        if (isValid) {
            $(".patient-attendant-details").addClass('theme-color').removeClass('pending');
        } else {
            $(".patient-attendant-details").removeClass('theme-color').addClass('pending');
        }
    });
    $("#new_born_baby").on("change",function(){
        if($(this).is(":checked")){
            $(".born-baby-field").removeClass("d-none");
        }else{
            $(".born-baby-field").addClass("d-none");
        }
    })
    let maxAllowedDate = moment();
    $('#born_baby_dob').daterangepicker({
        singleDatePicker: true,
        locale: {
            format: 'YYYY-MM-DD'
        },
        maxDate: maxAllowedDate,
        opens: isRtl ? 'left' : 'right'
    });
</script>
@endpush