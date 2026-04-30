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

<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-md-6">
            <img src="{{asset('public/front/assets/img/newleft.png')}}" class="w-100" alt="logo" />
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header border-bottom border-primary">
                    <h5 class="card-title theme-color mb-0">Empanel New Hospital</h5>
                </div>
                <div class="card-body pt-4">
                    <div class="row">
                        <div class="col-12">
                            <p>Is your facility registered with ayushman Bharat Digital Health Mission (ABDM)?</p>
                            <div class="d-flex mb-3">
                                <div class="form-check">
                                    <input class="form-check-input new_hospital" type="radio" name="new_hospital" id="option1"  value="Y">
                                    <label class="form-check-label" for="option1">
                                        Yes
                                    </label>
                                </div>
                                <div class="form-check ms-4">
                                    <input class="form-check-input new_hospital" type="radio" name="new_hospital" id="option2" value="N">
                                    <label class="form-check-label" for="option2">
                                        No
                                    </label>
                                </div>
                            </div>
                        </div>                        
                    </div>
                    <div class="no-hfr" style="display:none;">
                        <p>Please create the Health Facility Registry ID <a href="https://facility.ndhm.gov.in/" target="_blank">https://facility.ndhm.gov.in</a></p>
                        <hr>
                        <label><strong>Note:</strong></label>
                        <p>Kindly click on the link to fill the details in ABDM-HFR. Once the form is submitted kindly return to the page to continue with the facility empanelment process.</p>
                    </div>
                    <div class="yes-hfr" style="display:none;">
                        <form id="hfr-submit">
                        <div class="d-flex mb-3">
                            <div class="form-check">
                                <input class="form-check-input selectOption" type="radio" name="selectOption" id="option3"  value="hfrId">
                                <label class="form-check-label" for="option3">
                                    With Hfr ID
                                </label>
                            </div>
                            <div class="form-check ms-4">
                                <input class="form-check-input selectOption" type="radio" name="selectOption" id="option4" value="MobileNo">
                                <label class="form-check-label" for="option4">
                                    With Mobile No
                                </label>
                            </div>
                        </div>
                        <div class="hfrdiv" style="display:none;">
                           
                            <div class="col-md-6">
                                <label class="mb-3">Health Faculty Registration ID <span class="text-danger">*</span></label>
                                <div class="input-group errorcode">
                                    <input type="text" class="form-control"
                                        placeholder="Recipient's username" oninput="sanitize(this, 'b');" name="hfr_id" id="hfr_id"  maxlength="12"
                                        oninput="validateHfrId(this)"/>
                                    <button class="btn btn-outline-primary submitbtnn" type="button" id="button-addon2">Verify</button>
                                </div>
                            </div>
                        </div>

                        <div class="mobilediv" style="display:none;">
                            <div class="col-sm-6">
                                <label class="mb-3">Mobile No <span class="text-danger">*</span></label>
                                <div class="form-password-toggle">
                                    <div class="input-group input-group-merge errorcode">
                                        <input type="number" id="mobile_no" name="mobile_no" oninput="validatemobileNo(this);" class="form-control" placeholder="Mobile No" required/>
                                        <button type="button" class="input-group-text cursor-pointer theme-color btntext" onclick="SendOTPOnMobile();">VERIFY</button>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-6 otpInput" style="display:none;">
                                <label class="mb-3">Enter OTP</label>                                
                                <div class="form-password-toggle">
                                    <div class="input-group input-group-merge errorcode">
                                        <div class="form-floating form-floating-outline">
                                            <input type="number" class="form-control" id="mobile_otp" name="mobile_otp" placeholder="123456" onchange="VerifyOtp();">
                                            <label for="basic-default-password12">Enter OTP</label>
                                        </div>
                                        <button type="button" class="input-group-text cursor-pointer theme-color otptext" onclick="ReSendOTPOnMobile()">RESEND OTP</button>
                                    </div>
                                    <span id="otp-error"></span>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-5">
                            <button class="btn btn-primary btn-lg submitbtn" disabled type="button"
                                id="button-addon2">Confirm</button>
                        </div>
                        
                        </form>
                        <hr>
                        <label><strong>Note:</strong></label>
                        <p>Kindly enter the ABDM-HFR ID to continue with the facility empanelment process.</p>
                    </div>                    
                </div>
            </div>
        </div>
    </div>
</div>
<!--/ Content -->

@endsection

@push('scripts')

<script>

    function validatemobileNo(input) {
        // Allow only alphanumeric characters and limit to 12
        input.value = input.value.replace(/[^0-9]/g, '');
        if (input.value.length > 10) {
            input.value = input.value.slice(0, 10);
        }
    }

    // Send OTP to Mobile
    function SendOTPOnMobile() {
        ldrshow();
        let mobile = $('#mobile_no').val();
        if(mobile != ''){
            $(".mobile-error").text("");
            fetch('{{route("hospital.SendOTPOnMobile")}}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ mobile })
            })
            .then(response => response.json())
            .then(data => {
                ldrhide();
                if (data.success) {
                    $(".otpInput").show();
                    $("#mobile_otp").val(data.otp);
                    $("#mobile_no").attr('readonly', true);
                    $(".btntext").html('<i class="tf-icons ri-check-fill text-green"></i>')
                    successMessage(data.message);
                    VerifyOtp();
                } else {
                    $("#mobile_no").removeAttr('readonly');
                    $(".email-error").text(data.message);
                    errorMessage(data.message);
                }
            });
        } else {
            ldrhide();
            errorMessage('Please Enter Mobile no.');

        }
    }
    // Re-Send OTP to Mobile
   function ReSendOTPOnMobile() {
      let mobile = $('#mobile_no').val();
      if(mobile != ''){
        ldrshow();
         $(".mobile-error").text("");
         fetch('{{route("hospital.reSendOTPOnMobile")}}', {
               method: 'POST',
               headers: {
                  'Content-Type': 'application/json',
                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
               },
               body: JSON.stringify({ mobile })
         })
         .then(response => response.json())
         .then(data => {
                ldrhide();

               if (data.success) {
                    successMessage(data.message);

                  $("#mobile_otp").val(data.otp);
                  VerifyOtp();
               } else {
                 errorMessage(data.message);
                  $(".email-error").text(data.message);
               }
         });
      }
   }

   function VerifyOtp() {

      let mobile_no = $('#mobile_no').val();
      let otp = $('#mobile_otp').val();
      if(mobile_no != ''){
        ldrshow();
         $(".otp-error").text("");
         fetch('{{route("hospital.VerifyOtp")}}', {
               method: 'POST',
               headers: {
                  'Content-Type': 'application/json',
                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
               },
               body: JSON.stringify({ mobile_no, otp })
         })
         .then(response => response.json())
         .then(data => {
                ldrhide();

               if (data.success) {
                  // $("#mobile_otp").val(data.otp);
                  $('.submitbtn').removeAttr('disabled')
                  $(".otptext").html('<i class="tf-icons ri-check-fill text-green"></i>')
                  setTimeout(() => {
                     $('.otpInput').hide();
                  }, 500);
                  successMessage(data.message);

               } else {
                    errorMessage(data.message);
                    $("#otp-error").text(data.message);
               }
         });
      }
   }


    $(".new_hospital").on('change', function(){
        var selectedValue = this.value;
        if(selectedValue == "Y") {
            $('.no-hfr').hide();
            $('.yes-hfr').show();
        } else {
            $('.no-hfr').show();
            $('.yes-hfr').hide();
        }
    });

    $(".selectOption").on('change', function(){
        var selectedValue = this.value;
        if(selectedValue == "hfrId") {
            $('.mobilediv').hide();
            $('.hfrdiv').show();
        } else {
            $('.mobilediv').show();
            $('.hfrdiv').hide();
        }
    });   

    $('.submitbtn').click(function () {
        ldrshow();
        $('.error').remove();
        // Create a FormData object
        var formData = new FormData($('#hfr-submit')[0]);
        // Send an AJAX request
        $.ajax({
            url: '{{route("hospital.empanelmentRegistration.addHfrId")}}', // Replace with your server endpoint
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            type: 'POST',
            data: formData,
            processData: false, // Prevent jQuery from automatically processing the data
            contentType: false, // Prevent jQuery from automatically setting content type
            success: function (response) {
                // alert('Form submitted successfully!');
                ldrhide();
                if(response.success) {
                    successMessage(response.message);
    
                    window.location.href = response.url;
                } else {
                    errorMessage(response.message);
                }
                
            },
            error: function (xhr) {
                ldrhide();
                $('.error').remove();
                
                if (xhr.status === 422) { 
                    let errors = xhr.responseJSON.errors;
                    for (let field in errors) {
                        $(`[name="${field}"]`).closest('.errorcode').after(`<div class="error text-danger">${errors[field][0]}</div>`);
                    }
                } else {
                    alert('Something went wrong. Please try again later.');
                }
            }
        });
    });

    function validateHfrId(input) {
        // Allow only alphanumeric characters and limit to 12
        const regex = /^[a-zA-Z0-9]*$/;
        if (!regex.test(input.value)) {
            input.value = input.value.replace(/[^a-zA-Z0-9]/g, ''); // Remove invalid characters
        }
    }

</script>
@endpush