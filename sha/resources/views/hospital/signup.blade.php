<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SHA Uttarakhand</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
    <link rel="stylesheet" href="{{asset('public/front/assets/vendor/libs/toastr/toastr.css') }}" />
    <style>
        .bg-gradient-overlay {
            background: linear-gradient(135deg, rgba(0,128,128,0.9), rgba(0,128,128,0.6));
            min-height: 100vh;
        }
        .login-container {
            background-color: white;
            border-radius: 8px;
            padding: 2rem;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .logo-container img {
            max-height: 80px;
            margin: 10px;
        }
        .language-selector {
            position: absolute;
            top: 20px;
            right: 20px;
        }
        .captcha-box {
            border: 1px solid #ddd;
            padding: 5px;
            border-radius: 4px;
            background: #f8f9fa;
        }
        .footer-text {
    color: white;
    font-size: 0.9rem;
    position: absolute;
    z-index: 99;
}
        .loader-overlay {
            position: fixed;
            width: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 99999999999;
            top: 0;
            left: 0;
            height: 100vh;
        }
        .footer-text .footer-link{
            font-size: 20px;
            text-align: center;
            text-decoration: none;
        }
        .overlay-login{
            position: relative;
            z-index: 10;
        }
        .overlay-login:after{
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(50 ,81 ,132 ,0.7);
            z-index: -1;
        }
        @media (max-width: 767px){
            .footer-text {
                position: relative;
            }
            .login-container {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
<div class="main overlay-login d-flex align-items-center justify-content-center min-vh-100 " style="background-image: url('{{asset('public/images/background1.png')}}');background-repeat: no-repeat;background-size: cover;background-position: center center;">        
    <div class="container">
    <div class="row g-0 align-items-center justify-content-center">
    <!-- Left Column -->
        <div class="col-md-6 text-white position-relative" >
                    <!-- <h1>Hospital Engagement Module</h1> -->
                    <!-- <p class="text-white">THis is loram ipusm dummy text</p> -->
                    <div class="logo-container d-flex justify-content-center align-items-center mb-4">
                        <img src="{{asset('public/front/assets/img/n_logo-removebg-preview.png')}}" alt="National Health Authority" class="me-3">
                        <!-- <img src="{{asset('public/images/pmJay.png')}}" alt="Ayushman Bharat Logo"> -->
                    </div>
                </div>

                <!-- Right Column -->
                <div class="col-md-6">
                    <div class="row g-0 align-items-center h-100 justify-content-end">
                        <div class="col-md-9">
                    <div class="login-container">
                        <h3 class="text-success ">SHA UTTARAKHAND</h3>
                        <h5 class="text-success mb-4">(Claim Management System)</h5>
                        <h4 class="text-warning mb-4">SIGNUP</h4>
                        
                        <form>
                            <div class="mb-3">
                                <label class="form-label">Captcha*</label>
                                <div class="input-group">
                                <!-- onChange="validateCaptcha();" -->
                                    <input type="text" class="form-control " oninput="sanitize(this, 'b');" id="captchaInput" placeholder="Enter the text above" >
                                    <span class="input-group-text captcha-box">            
                                        <img id="captchaImage" src="{{route('generateCaptcha')}}" alt="CAPTCHA">
                                    </span>
                                    <button class="btn btn-outline-secondary captchabtn" onClick="reloadCaptcha();" type="button">
                                        <i class="fa fa-refresh"></i>
                                    </button>
                                </div>
                                <span id="captcha-error" class="text-danger"></span>
                            </div>

                            <div class="d-flex mb-3">
                                <div class="form-check">
                                    <input class="form-check-input signupoption" disabled type="radio" name="signupoption" id="option1"  value="withaadhaar">
                                    <label class="form-check-label" for="option1">
                                        With Aadhaar
                                    </label>
                                </div>
                                <div class="form-check ms-4">
                                    <input class="form-check-input signupoption" disabled type="radio" name="signupoption" id="option2" value="withemail">
                                    <label class="form-check-label" for="option2">
                                        With Email & Mobile Number
                                    </label>
                                </div>
                            </div>
                            
                            <div class="email_verification_block withemail" style="display:none;">
                                <div class="mb-3 " >
                                    <label class="form-label">Email*</label>
                                    <div class="input-group submiterror">
                                        <input type="text" name="email"  id="emailInput"  oninput="sanitize(this, 'email');" class="form-control email" placeholder="Type here" disabled>
                                        <button class="btn btn-default emailbutton"disabled type="button"  onclick="CheckEmail();" title="validate"><i class="fa fa-check"></i></button>
                                    </div>
                                    <span class="email-error text-danger"></span>
                                </div>
                                <div class="mb-3 " id="email_otp_block" style="display:none">
                                    <label class="form-label">Enter Email OTP*</label>
                                    <input type="text" name="email_otp" id="email_otp" class="form-control submiterror" onchange="OTPVerification();" placeholder="Type here">
                                    <a href="javascript:;" onClick="ResendOtp('Email')" class="resendEmailOtp" style="float:right">Resend OTP</a>
                                    <span class="emailotpverify text-danger"></span>
                                </div>


                                <div class="mb-3 " >
                                    <label class="form-label">Mobile*</label>
                                    <div class="input-group submiterror">
                                        <input type="text" name="mobile_no"  id="mobileInput"  class="form-control mobile_no" oninput="validateMobileNo(this);" placeholder="Type here" disabled>
                                        <button class="btn btn-default mobilebutton" disabled type="button" onclick="CheckMobile();" title="validate"><i class="fa fa-check"></i></button>
                                    </div>
                                    <span class="mobile-error text-danger"></span>
                                </div>
                                <div class="mb-3 " id="mobile_otp_block" style="display:none">
                                    <label class="form-label">Enter Mobile OTP*</label>
                                    <input type="text" name="mobile_otp" id="mobile_otp" class="form-control" onChange="MobileOTPVerification();" placeholder="Type here">
                                    <a href="javascript:;" onClick="ResendOtp('Mobile')" class="resendMobileOtp" style="float:right">Resend OTP</a>
                                    <span class="mobileotpverify text-danger"></span>
                                </div>
                            </div>
                            
                            <div class="aadhaar_verification_block withaadhaar" style="display:none;">
                                
                                <div class="mb-3">
                                    <label class="form-label">Aadhaar Number*</label>
                                    <input type="text" name="aadhaar_no" oninput="validatAadhaarNo(this);" id="aadhaar_no" class="form-control" placeholder="Type here" disabled>
                                    <input type="hidden" name="reference_id" id="reference_id">
                                </div>

                                <div class="mb-4">
                                    <label class="form-label">E-KYC Mode*</label>
                                    <select class="form-select ekyc_mode" onChange="SendAadhaarOtp();" disabled>
                                        <option selected>Select</option>
                                        <option value="Aadhaar_OTP">Aadhaar OTP</option>
                                    </select>
                                </div>

                                <div class="mb-3 aadhaar_otp_block" style="display:none">
                                    <label class="form-label">Enter aadhaar OTP*</label>
                                    <input type="text" name="aadhaar_otp" id="aadhaar_otp" class="form-control submiterror" placeholder="Type here" onChange="AadhaarOTPVerification();" >
                                    <span class="aadhaarotpverify text-danger"></span>
                                    <a href="javascript:;" onClick="ResendOtp('Aadhaar')" class="resendAadhaarOtp" style="float:right">Resend OTP</a>
                                </div>
                            </div>

                            <button type="button" class="btn btn-secondary w-100 mb-3 submitbtn" disabled onClick="submitData();">SUBMIT</button>

                            <div class="text-center">
                                <p class="mb-1">If you already have an account, please <a href="{{route('hospital.login')}}" class="text-decoration-none">LOGIN</a></p>
                            </div>
                        </form>
                    </div>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <div class="footer-text text-center bottom-0 mb-4 p-3 p-lg-4">
        <div class="row">
                    <!-- Left Column -->
                    <div class="col-md-12 text-white position-relative" >
                            <a href="https://parakore.in/" target="_blank" class="footer-link text-center text-white">Designed &amp; Developed By Parakore Enterprises Pvt. Ltd.</a>
                    </div>
        </div>
    </div>
    <div class="loader-overlay" style="display:none">
        <div class="spinner-grow text-success" style="width: 5rem; height: 5rem;"  role="status">
          </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{asset('public/front/assets/js/sanitize.js') }}"></script>
    <script src="{{asset('public/front/assets/vendor/libs/toastr/toastr.js') }}"></script>
    <script src="{{asset('public/front/assets/js/ui-toasts.js') }}"></script>

    <script>

        function errorMessage(msg){            
            var shortCutFunction = 'error',
            title = 'Error',
            
            prePositionClass =
            typeof toastr.options.positionClass === 'undefined' ? 'toast-top-right' : toastr.options.positionClass;
            toastr.options.showDuration = 300;
            toastr.options = {
                maxOpened: 1,
                autoDismiss: true,
                closeButton: true,
                newestOnTop: true,
                progressBar:true,
                positionClass: 'toast-top-right',
                onclick: null,
            };    
            var $toast = toastr[shortCutFunction](msg, title); // Wire up an event handler to a button in the toast, if it exists
            $toastlast = $toast;
        }
        function successMessage(msg){
            var shortCutFunction = 'success',
            title = 'Success',
            
            prePositionClass =
            typeof toastr.options.positionClass === 'undefined' ? 'toast-top-right' : toastr.options.positionClass;
            toastr.options.showDuration = 300;
            toastr.options = {
                maxOpened: 1,
                autoDismiss: true,
                closeButton: true,
                newestOnTop: true,
                progressBar:true,
                positionClass: 'toast-top-right',
                onclick: null,
            };    
            var $toast = toastr[shortCutFunction](msg, title); // Wire up an event handler to a button in the toast, if it exists
            $toastlast = $toast;
        }

        function validatAadhaarNo(input) {
            input.value = input.value.replace(/[^0-9]/g, '');

            // Limit the length to 12 digits
            if (input.value.length > 12) {
                input.value = input.value.slice(0, 12);
            }
        }

        function validateMobileNo(input) {
            // Remove any non-numeric characters
            input.value = input.value.replace(/[^0-9]/g, '');

            // Limit the length to 10 digits
            if (input.value.length > 10) {
                input.value = input.value.slice(0, 10);
            }
        }

        $(".signupoption").on('change', function(){
            var selectedValue = this.value;
            if(selectedValue == "withemail") {
                $('.withaadhaar').hide();
                $('.withemail').show();
                $('#aadhaar_no').attr('disabled', true);
                $('#aadhaar_no').removeAttr('required');
                $(".ekyc_mode").attr('disabled', true);
                $(".ekyc_mode").removeAttr('required');
            } else {
                $(".ekyc_mode").removeAttr('disabled');
                $(".ekyc_mode").attr('required', true);                
                $('#aadhaar_no').removeAttr('disabled');
                $('#aadhaar_no').attr('required',  true);
                $('.withaadhaar').show();
                $('.withemail').hide();
            }
        });

        // Reload CAPTCHA image
        function reloadCaptcha() {
            let random = Math.floor(Math.random() * 100000); // or use Date.now()
            let captchaUrl = "{{ route('generateCaptcha') }}" + "?" + random;
            $('#captchaImage').attr('src', captchaUrl);
            // $('#captchaImage').attr('src', '{{route('generateCaptcha', ['' => now()->timestamp * 1000])}}');
        }

        function verifyReCaptcha(captcha, callback) {
            $(".loader-overlay").show();
            fetch('{{route("verifyCaptcha")}}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ captcha })
            })
            .then(response => response.json())
            .then(data => {
                $(".loader-overlay").hide();
                if (callback && typeof callback === 'function') {
                    callback(data);
                }
            }) 
        }
        $("#captchaInput").on("input",function(){
            if($(this).val().length == 6){
                validateCaptcha();
            }
        })
        function validateCaptcha() {
            let captcha = $("#captchaInput").val();
            $("#captcha-error").text("");
            verifyReCaptcha(captcha, function(data) {
                if (data.success) {
                    $('.signupoption').removeAttr('disabled');
                    $('#emailInput').removeAttr('disabled');
                    $('.emailbutton').removeAttr('disabled');
                    $('.captchabtn').attr('disabled', true);
                    $('#captchaInput').attr('disabled', true);
                    successMessage(data.message);
                } else {
                    errorMessage(data.message);
                    $("#captcha-error").text(data.message);
                    $('#emailInput').attr('disabled', true);
                    $('.emailbutton').attr('disabled', true);
                    $('#aadhaar_no').attr('disabled', true);
                }
            });
        }

        // Send OTP to Email
        function CheckEmail() {
            $(".loader-overlay").show();
            let email = $('#emailInput').val();
            $(".email-error").text("");
            fetch('{{route("sendEmailMail")}}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ email })
            })
            .then(response => response.json())
            .then(data => {
                $(".loader-overlay").hide();
                if (data.success) {
                    $('#email_otp_block').show();
                    $('#email_otp').removeAttr('disabled');
                    $("#emailInput").attr('disabled', true);
                    $('.emailbutton').attr('disabled', true);
                    successMessage(data.message);
                } else {
                    $(".email-error").text(data.message);
                    errorMessage(data.message);
                }
            });
        }

        function CheckMobile() {
            $(".loader-overlay").show();
            let mobile_no = $('.mobile_no').val();
            let email = $('#emailInput').val();
            $(".mobile-error").text("");
            fetch('{{route("sendMobileMail")}}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ email, mobile_no })
            })
            .then(response => response.json())
            .then(data => {
                $(".loader-overlay").hide();
                if (data.success) {
                    $('#mobile_otp_block').show();
                    $('#mobile_otp').removeAttr('disabled');
                    $("#mobileInput").attr('disabled', true);
                    $(".mobile_no").attr('required', true);
                    $('.mobilebutton').attr('disabled', true);
                    successMessage(data.message);
                } else {
                    $(".mobile-error").text(data.message);
                    errorMessage(data.message);
                }
            });
        }

        function ResendOtp(type) {
            $(".loader-overlay").show();
            let email = $('#emailInput').val();
            if(type == "Aadhaar") {
                email = $('#aadhaar_no').val();
            }

            fetch('{{route("resendOTP")}}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ email, type })
            })
            .then(response => response.json())
            .then(data => {
                $(".loader-overlay").hide();
                if (data.success) {
                    $('#email_otp_block').show();
                    $('#email_otp').removeAttr('disabled');
                    if(type == "Aadhaar") {
                        $('#reference_id').val(data.reference_id);
                    }
                    successMessage(data.message);
                } else {
                    $(".email-error").text(data.message);
                    errorMessage(data.message);
                }
            });
        }

        function VerifyOTP(otp, type, email='', aadhaar='', mobile_no = '', callback) {
            $(".loader-overlay").show();
            let reference_id = '';
            if(type == "Aadhaar") {
                reference_id = $("#reference_id").val();
            }
            fetch('{{route("verifyEmailOtp")}}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ otp, type, email, mobile_no, aadhaar, reference_id })
            })
            .then(response => response.json())
            .then(data => {
                $(".loader-overlay").hide();
                if (callback && typeof callback === 'function') {
                    callback(data);
                }
            });
        }

        // Verify OTP
        function MobileOTPVerification() {
            let otp = $("#mobile_otp").val();
            let mobile_no = $('#mobileInput').val();
            $(".mobileotpverify").text("");
            VerifyOTP(otp, 'Mobile', '', '', mobile_no, function(data) {
                console.log(data);
                if(data.success) {
                    console.log('hello');
                    $("#mobile_otp").attr('disabled', true);
                    $(".mobilebutton").removeClass('btn-default').addClass('btn-success');
                    $(".resendMobileOtp").hide();
                    $(".mobile_no").removeAttr('disabled');
                    $(".submitbtn").removeAttr('disabled');
                    $('.submitbtn').addClass('btn-success');
                    $('.submitbtn').removeClass('btn-secondary');
                    $('.submitbtn').focus();
                    successMessage(data.message);
                } else {
                    $(".mobileotpverify").text(data.message);
                    errorMessage(data.message);
                }
            });
        };

        function OTPVerification() {
            let otp = $("#email_otp").val();
            let email = $('#emailInput').val();
            $(".emailotpverify").text("");
            VerifyOTP(otp, 'Email', email, '', '', function(data) {
                console.log(data);
                if(data.success) {
                    console.log('hello');
                    $("#email_otp").attr('disabled', true);
                    $(".emailbutton").removeClass('btn-default').addClass('btn-success');
                    $(".resendEmailOtp").hide();
                    $(".mobile_no").removeAttr('disabled');
                    $(".mobilebutton").removeAttr('disabled');
                    successMessage(data.message);
                } else {
                    $(".emailotpverify").text(data.message);
                    errorMessage(data.message);
                }
            });
        };

        // function OTPVerification() {
        //     let otp = $("#email_otp").val();
        //     let email = $('#emailInput').val();
        //     $(".emailotpverify").text("");
        //     VerifyOTP(otp, 'Email', email, '', function(data) {
        //         console.log(data);
        //         if(data.success) {
        //             console.log('hello');
        //             $("#email_otp").attr('disabled', true);
        //             $(".emailbutton").removeClass('btn-default').addClass('btn-success');
        //             $(".resendEmailOtp").hide();
        //             $("#aadhaar_no").removeAttr('disabled');
        //             $(".ekyc_mode").removeAttr('disabled');
        //         } else {
        //             $(".emailotpverify").text(data.message);
        //         }
        //     });
        // };
        function SendAadhaarOtp() {
            $(".loader-overlay").show();
            let email = $('#emailInput').val();
            let aadhaar = $('#aadhaar_no').val();
            $(".aadhaar_otp_block").show();
            $("#aadhaar_no").attr('disabled', true);
            $(".email-error").text("");
            fetch('{{route("sendAadhaarMail")}}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ email, aadhaar })
            })
            .then(response => response.json())
            .then(data => {
                $(".loader-overlay").hide();
                if (data.success) {
                    $('#email_otp_block').show();
                    $('#email_otp').removeAttr('disabled');
                    $("#emailInput").attr('disabled', true);
                    $('.emailbutton').attr('disabled', true);
                    $('#reference_id').val(data.reference_id);
                    successMessage(data.message);
                } else {
                    $(".email-error").text(data.message);
                    $("#aadhaar_no").removeAttr('disabled', false);
                    errorMessage(data.message);
                }
            });
        }

        function AadhaarOTPVerification() {
            let otp = $("#aadhaar_otp").val();
            let email = $('#emailInput').val();
            let aadhaar = $('#aadhaar_no').val();
            $(".aadhaarotpverify").text("");
            VerifyOTP(otp, 'Aadhaar', email, aadhaar, '', function(data) {
                if(data.success) {
                    $("#aadhaar_otp").attr('disabled', true);
                    $(".emailbutton").removeClass('btn-default').addClass('btn-success');
                    $(".resendAadhaarOtp").hide();
                    $("#aadhaar_no").removeAttr('disabled');
                    $(".submitbtn").removeAttr('disabled');
                    $('.submitbtn').addClass('btn-success');
                    $('.submitbtn').removeClass('btn-secondary');
                    $('.submitbtn').focus();
                    successMessage(data.message);
                } else {
                    $(".aadhaarotpverify").text(data.message);
                    errorMessage(data.message);
                }
            });
        }

        function submitData() {
            $(".loader-overlay").show();
            const formData = {
                captcha: $('#captchaInput').val(),
                email: $('#emailInput').val(),
                email_otp: $('#email_otp').val(),
                aadhaar_no: $('#aadhaar_no').val(),
                aadhaar_otp: $('#aadhaar_otp').val(),
                ekyc_mode: $('.ekyc_mode').val(),
                mobile_no: $('.mobile_no').val(),
                signupoption: $("input[name='signupoption']:checked").val(),
            };

            $.ajax({
                url: '{{ route("register.store") }}', // Your route here
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                contentType: 'application/json',
                data: JSON.stringify(formData),
                success: function(response) {
                    $(".loader-overlay").hide();
                    if (response.success) {
                        console.log("Registration successful!");
                        // Optionally redirect
                        window.location.href = response.route;
                    } else {
                        alert("Registration failed: " + response.message);
                    }
                },
                error: function (xhr) {
                    $(".loader-overlay").hide();
                    // Clear previous errors
                    $('.error').remove();
                    
                    if (xhr.status === 422) { // Laravel validation error
                        let errors = xhr.responseJSON.errors;
                        for (let field in errors) {
                            // Append error message below the respective input
                            $(`[name="${field}"]`).closest('.submiterror').after(`<div class="error text-danger">${errors[field][0]}</div>`);
                        }
                    } else {
                        alert('Something went wrong. Please try again later.');
                    }
                }                
            });
        }
    </script>
</body>
</html>