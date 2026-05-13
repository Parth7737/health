@php
    $ceo = $hospital->ceo;
@endphp
<div class="inside-left-info-box @if(!empty($ceo) && $ceo->is_detail_added == 1) success @else pending @endif ceopanel">
    <h4 class="colored-verticle-title">
        Head of the Organization/CEO 
        <span class="status-dot">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="undefined">
                <path d="M400-304 240-464l56-56 104 104 264-264 56 56-320 320Z" />
            </svg>
        </span>
    </h4>
    <form id="ceoform">
        <div class="row g-5">
            <input type="hidden" name="mobileverify" id="mobileverify" value="{{!empty($ceo) && $ceo->is_detail_added == 1 ? 1 : 0}}">
            <input type="hidden" name="emailverify" id="emailverify" value="{{!empty($ceo) && $ceo->is_detail_added == 1 ? 1 : 0}}">
            <div class="col-md-6 col-lg-3 ceoerror">
                <div class="form-floating form-floating-outline">
                    <input type="text" id="name" name="name" oninput="sanitize(this, 't');" class="form-control" placeholder="CEO" value="{{!empty($ceo) && $ceo->is_detail_added == 1 ? $ceo->name : ''}}" />
                    <label for="name">Name</label>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 ceoerror">
                <div class="form-floating form-floating-outline">
                    <input type="text" id="designation" oninput="sanitize(this, 't');" name="designation" class="form-control" placeholder="CEO" value="{{!empty($ceo) && $ceo->is_detail_added == 1 ? $ceo->designation : ''}}" />
                    <label for="designation">Designation</label>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 ceoerror">
                <div class="input-group input-group-merge">
                    <div class="form-floating form-floating-outline">
                        <input type="email" class="form-control" id="email" oninput="sanitize(this, 'email');" placeholder="john.doe" name="email" aria-label="Recipient's username" aria-describedby="email" value="{{!empty($ceo) && $ceo->is_detail_added == 1 ? $ceo->email : ''}}" {{!empty($ceo) && $ceo->is_detail_added == 1 ? 'readonly' : ''}}>
                        <label for="email">Email ID</label>
                    </div>
                    @if(empty($ceo)) 
                    <button class="input-group-text btnemail" type="button" onclick="SendOTPOnEmail()">Verify</button>
                    @endif
                </div>
                <div class="email-error text-danger"></div>
            </div>
            @if(empty($ceo))
            <div class="col-md-6 col-lg-3 emailOtp_block ceoerror" style="display:none">
                <div class="input-group input-group-merge">
                    <div class="form-floating form-floating-outline">
                        <input type="text" id="email_otp" name="email_otp" class="form-control" placeholder="CEO" />
                        <label for="email_otp" >OTP</label>
                    </div>
                    <button class="input-group-text btnotpverifyemail" type="button"  onclick="verifiyEmailOtp()">Verify</button>
                </div>
                <div class="email_otperror text-danger"></div>
                <a href="javascript:;" onclick="ReSendOTPOnEmail();" class="resendEmailOtp">Resend Otp</a>
            </div>
            @endif
            <div class="col-md-6 col-lg-3 ceoerror">
                <div class="input-group input-group-merge">
                    <div class="form-floating form-floating-outline">
                        <input type="text" id="mobile_no" name="mobile_no" class="form-control" placeholder="CEO" value="{{!empty($ceo) && $ceo->is_detail_added == 1 ? $ceo->mobile_no : ''}}" oninput="mobileinput(this);" {{!empty($ceo) && $ceo->is_detail_added == 1 ? 'readonly' : ''}}/>
                        <label for="mobile_no">Mobile No</label>
                    </div>
                    @if(empty($ceo))
                        <button class="input-group-text btnmobile_no" type="button" onclick="SendOTPOnMobile()">Verify</button>
                    @endif
                </div>
                <div class="mobile-error text-danger"></div>
            </div>
            @if(empty($ceo))
            <div class="col-md-6 col-lg-3 mobileOtp_block ceoerror" style="display:none">
                <div class="input-group input-group-merge">
                    <div class="form-floating form-floating-outline">
                        <input type="text" id="mobile_otp" name="mobile_otp" class="form-control" placeholder="CEO" />
                        <label for="mobile_otp" >OTP</label>
                    </div>
                    <button class="input-group-text btnotpverify" type="button"  onclick="verifiyMobileOtp()">Verify</button>
                </div>
                <a href="javascript:;" class="resendMobileOtp" onclick="ReSendOTPOnMobile();">Resend Otp</a>
                <div class="mobile_otperror text-danger"></div>
            </div>
            @endif
            <!-- <div class="col-md-12">
                <div class="row">
                    <div class="col-md-6 col-lg-3">
                        <div class="input-group input-group-merge">
                            <div class="form-floating form-floating-outline">
                                <input type="text" class="form-control" id="basic-addon13"
                                    placeholder="john.doe" aria-label="Recipient's username"
                                    aria-describedby="basic-addon13">
                                <label for="basic-addon13">Enter Aadhaar</label>
                            </div>
                            <button class="input-group-text">Verify</button>
                        </div>
                    </div>
                    <div
                        class="col-md-6 col-lg-3">
                        <div
                            class="input-group input-group-merge">
                            <div
                                class="form-floating form-floating-outline">
                                <input
                                    type="text"
                                    class="form-control"
                                    id="basic-addon13"
                                    placeholder="john.doe"
                                    aria-label="Recipient's username"
                                    aria-describedby="basic-addon13">
                                <label
                                    for="basic-addon13">OTP</label>
                            </div>
                            <button
                                class="input-group-text">Resend
                            OTP</button>
                        </div>
                    </div>
                    <div
                        class="col-md-6 col-lg-3">
                        <div
                            class="form-floating form-floating-outline">
                            <input
                                type="text"
                                id="Email Id"
                                class="form-control"
                                placeholder="CEO" />
                            <label
                                for="Email Id">Admin
                            OTP</label>
                        </div>
                    </div>
                </div>
            </div> -->
            <div class="col-md-12">
                
                @if($hospital->status != 'Empannelled' && @$hospital->status != 'Empanelment Not Recommended by DEC')
                    <div class="d-flex justify-content-end">
                        <button class="btn btn-primary" {{!empty($ceo) && $ceo->is_detail_added == 1 ? '' : 'disabled' }} type="button" id="saveCEO">SAVE</button>
                    </div>
                @endif
            </div>
        </div>
    </form>
</div>

<script>
    // Send OTP to Mobile
    function SendOTPOnMobile() {
      
        let mobile = $('#mobile_no').val();
        if(mobile != ''){
            ldrshow();
            $(".mobile-error").text("");
            fetch('{{route("SendOTPOnMobile")}}', {
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
                    $("#mobile_otp").val(data.otp);
                    $(".mobileOtp_block").show();
                    $("#mobile_no").attr('readonly', true);
                    $(".btnmobile_no").attr('disabled', true);
                    $(".btnmobile_no").html('<i class="tf-icons ri-check-fill text-green"></i>')
                    successMessage(data.message);
                    // VerifyOtp();
                } else {
                    $("#mobile_no").removeAttr('readonly');
                    $(".mobile-error").text(data.message);
                    errorMessage(data.message);
                }
            });
        } else {
            errorMessage("Please Enter Mobile Number");
        }
    }
    // Re-Send OTP to Mobile
    function ReSendOTPOnMobile() {
            ldrshow();
        let mobile = $('#mobile_no').val();
        if(mobile != ''){
            $(".mobile-error").text("");
            fetch('{{route("reSendOTPOnMobile")}}', {
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
                    //   VerifyOtp();
                } else {
                    errorMessage(data.message);
                    $(".mobile-error").text(data.message);
                }
            });
        }
    }

    function verifiyMobileOtp() {
            ldrshow();

        let mobile_no = $('#mobile_no').val();
        let otp = $('#mobile_otp').val();
        if(mobile_no != ''){
            $(".mobile_otperror").text("");
            fetch('{{route("verifiyMobileOtp")}}', {
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
                    //   $('.submitbtn').removeAttr('disabled')
                    $(".btnotpverify").html('<i class="tf-icons ri-check-fill text-green"></i>')
                    $(".btnotpverify").attr('disabled', true);
                    $(".resendMobileOtp").hide();
                    setTimeout(() => {
                        $('.mobileOtp_block').hide();
                    }, 500);
                    $("#mobileverify").val(1);
                    successMessage(data.message);
                    if($('#emailverify').val() == 1 && $("#mobileverify").val() == 1) {
                        $("#saveCEO").removeAttr('disabled', true);
                    }
                } else {
                    errorMessage(data.message);
                    $("#mobile_otperrorr").text(data.message);
                }
            });
        }
    }

    // Send OTP to Mobile
    function SendOTPOnEmail() {
        let email = $('#email').val();
        if(email != ''){
            ldrshow();
            $(".email-error").text("");
            fetch('{{route("SendOTPOnEmail")}}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ email })
            })
            .then(response => response.json())
            .then(data => {
                ldrhide();
                if (data.success) {
                    $("#email_otp").val(data.otp);
                    $(".emailOtp_block").show();
                    $("#email").attr('readonly', true);
                    $(".btnemail").attr('disabled', true);
                    $(".btnemail").html('<i class="tf-icons ri-check-fill text-green"></i>')
                    successMessage(data.message);
                    // VerifyOtp();
                } else {
                    $("#email").removeAttr('readonly');
                    $(".email-error").text(data.message);
                    errorMessage(data.message);
                }
            });
        } else {
            errorMessage("Please Enter Email");
        }
    }
    // Re-Send OTP to Mobile
    function ReSendOTPOnEmail() {
        let email = $('#email').val();
        if(email != ''){
            ldrshow();
            $(".email-error").text("");
            fetch('{{route("ReSendOTPOnEmail")}}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ email })
            })
            .then(response => response.json())
            .then(data => {
                ldrhide();

                if (data.success) {
                        successMessage(data.message);

                    $("#email_otp").val(data.otp);
                    //   VerifyOtp();
                } else {
                    errorMessage(data.message);
                    $(".email-error").text(data.message);
                }
            });
        }
    }

    function verifiyEmailOtp() {

        let email = $('#email').val();
        let otp = $('#email_otp').val();
        if(email != ''){
            ldrshow();
            $(".email_otperror").text("");
            fetch('{{route("verifiyEmailOtp")}}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ email, otp })
            })
            .then(response => response.json())
            .then(data => {
                    ldrhide();

                if (data.success) {
                    // $("#mobile_otp").val(data.otp);
                    //   $('.submitbtn').removeAttr('disabled')
                    $(".btnotpverifyemail").html('<i class="tf-icons ri-check-fill text-green"></i>')
                    $(".btnotpverifyemail").attr('disabled', true);
                    $(".resendEmailOtp").hide();
                    setTimeout(() => {
                        $('.emailOtp_block').hide();
                    }, 500);
                    $("#emailverify").val(1);
                    successMessage(data.message);

                    if($('#emailverify').val() == 1 && $("#mobileverify").val() == 1) {
                        $("#saveCEO").removeAttr('disabled', true);
                    }

                } else {
                    errorMessage(data.message);
                    $("#email_otperrorr").text(data.message);
                }
            });
        }
    }

    $('#saveCEO').click(function () {
        if($('#emailverify').val() != 1 && $("#mobileverify").val() != 1) {
            errorMessage('Please First Verify Mobile and Email First');
        }
      ldrshow();
      $('.error').remove();

      // Create a FormData object
      var formData = new FormData($('#ceoform')[0]);
     
      // Send an AJAX request
      $.ajax({
         url: '{{route("hospital.empanelmentRegistration.saveCEO", [$uuid, $hospital_id])}}', // Replace with your server endpoint
         headers: {
               'X-CSRF-TOKEN': '{{ csrf_token() }}'
         },
         type: 'POST',
         data: formData,
         processData: false, // Prevent jQuery from automatically processing the data
         contentType: false, // Prevent jQuery from automatically setting content type
         success: function (response) {
            ldrhide();
            $('.ceopanel').removeClass('pending').addClass('success');
            successMessage(response.message);
            if(response.completedStep['medicalstep'] && response.completedStep['servicestep'] && response.completedStep['specialiststep']) {
                CheckHumanResourceStepCompleteOrNot(6, true);
            } else {
                CheckHumanResourceStepCompleteOrNot(5, false);
            }
            
         },
         error: function (xhr) {
            ldrhide();
            $('.error').remove();
            
            if (xhr.status === 422) { 
               let errors = xhr.responseJSON.errors;
               for (let field in errors) {
                  $(`[name="${field}"]`).closest('.ceoerror').after(`<div class="error text-danger">${errors[field][0]}</div>`);
               }
            } else {
               alert('Something went wrong. Please try again later.');
            }
         }
      });
   });
</script>