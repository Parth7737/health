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
                <div class="col-md-6">
                    <div class="row g-0 align-items-center h-100 justify-content-end">
                        <div class="col-md-9">
                        <div class="login-container ">
                        <h3 class="text-success ">SHA UTTARAKHAND</h3>
                        <h5 class="text-success mb-4">(Claim Management System)</h5>
                        <h4 class="text-warning mb-4">LOGIN</h4>
                        
                        <form id="loginForm" autocomplete="off">
                            <div class="mb-3">
                                <label class="form-label">Captcha*</label>
                                <div class="input-group">
                                <!-- onChange="validateCaptcha();" -->
                                    <input type="text" class="form-control " id="captchaInput" placeholder="Enter the text above" >
                                    <span class="input-group-text captcha-box">            
                                        <img id="captchaImage" src="{{route('generateCaptcha')}}" alt="CAPTCHA">
                                    </span>
                                    <button class="btn btn-outline-secondary captchabtn2" onClick="reloadCaptcha();" type="button">
                                        <i class="fa fa-refresh"></i>
                                    </button>
                                </div>
                                <span id="captcha-error" class="text-danger"></span>
                            </div>
                            
                            <div class="email_verification_block">
                                <div class="mb-3 " >
                                    <label class="form-label">Registered Email/UserId*</label>
                                    <div class="input-group">
                                        <input type="text" name="email"  id="emailInput" oninput="sanitize(this, 'email');"  class="form-control email" required placeholder="Type here" disabled>
                                        <button class="btn btn-default emailbutton"disabled type="button" onclick="CheckUser();" title="validate"><i class="fa fa-check"></i></button>
                                    </div>
                                    <span id="email-error" class="text-red text-danger"></span>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label">Authentication Mode*</label>
                                    <select class="form-select ekyc_mode" onChange="showPasswordBlock();" disabled>
                                        <option selected>Select</option>
                                        <option value="Password">Password</option>
                                    </select>
                                </div>
                            </div>
                            <div class="password-block" style="display:none;">
                                <div class="mb-3 " >
                                    <label class="form-label">Enter Password*</label>
                                    <input type="password" name="password"  id="password"  class="form-control password" required placeholder="Type here" disabled>
                                    <span id="password-error" class="text-danger"></span>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Captcha*</label>
                                    <div class="input-group">
                                    <!-- onChange="validateCaptcha2();" -->
                                        <input type="text" class="form-control " oninput="sanitize(this, 'b',6);" id="captchaInput2" placeholder="Enter the text above" >
                                        <span class="input-group-text captcha-box">            
                                            <img id="captchaImage2" src="{{route('generateCaptcha')}}" alt="CAPTCHA">
                                        </span>
                                        <button class="btn btn-outline-secondary captchabtn" onClick="reloadCaptcha2();" type="button">
                                            <i class="fa fa-refresh"></i>
                                        </button>
                                    </div>
                                    <span id="captcha-error2" class="text-danger"></span>
                                </div>
                            </div>

                            
                            <a href="{{route('password.request')}}" class="text-decoration-none " style="float:right;">Forgot Password</a>
                            <button type="button" class="btn btn-secondary w-100 mb-3 submitbtn" disabled >SUBMIT</button>

                            <div class="text-center">
                                <p class="mb-1">If you don't have an account, please <a href="{{route('hospital.signup')}}" class="text-decoration-none">SIGNUP</a></p>
                            </div>
                        </form>
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
    <script src="{{asset('public/front/assets/vendor/libs/toastr/toastr.js') }}"></script>
    <script src="{{asset('public/front/assets/js/ui-toasts.js') }}"></script>
    <script src="{{asset('public/front/assets/js/sanitize.js') }}"></script>

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
        // Reload CAPTCHA image
        function reloadCaptcha() {
            let random = Math.floor(Math.random() * 100000); // or use Date.now()
            let captchaUrl = "{{ route('generateCaptcha') }}" + "?" + random;
            $('#captchaImage').attr('src', captchaUrl);
        }

        function reloadCaptcha2() {
            let random = Math.floor(Math.random() * 100000); // or use Date.now()
            let captchaUrl = "{{ route('generateCaptcha') }}" + "?" + random;
            $('#captchaImage2').attr('src', captchaUrl);
            // $('#captchaImage2').attr('src', '{{route('generateCaptcha', ['' => now()->timestamp * 1000])}}');
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
            sanitize(this, 'b',6);
            if($(this).val().length == 6){
                validateCaptcha();
            }
        })
        function validateCaptcha() {
            let captcha = $("#captchaInput").val();
            $("#captcha-error").text("");
            verifyReCaptcha(captcha, function(data) {
                if (data.success) {
                    $('#emailInput').removeAttr('disabled');
                    $('.emailbutton').removeAttr('disabled');
                    $("#emailInput").focus();
                    $('.captchabtn2').attr('disabled', true);
                    $('#captchaInput').attr('disabled', true);
                    successMessage(data.message);
                } else {
                    errorMessage(data.message);

                    $("#captcha-error").text(data.message);
                    $('#emailInput').attr('disabled', true);
                    $('.emailbutton').attr('disabled', true);
                }
            });
        }
        $("#captchaInput2").on("input",function(){
            sanitize(this, 'b',6);
            if($(this).val().length == 6){
                validateCaptcha2();
            }
        })
        function validateCaptcha2() {
            let captcha = $("#captchaInput2").val();
            $("#captcha-error2").text("");
            verifyReCaptcha(captcha, function(data) {
                if (data.success) {
                    $('.submitbtn').removeAttr('disabled');
                    $('.submitbtn').addClass('btn-success');
                    $('.submitbtn').removeClass('btn-secondary');
                    $('.submitbtn').focus();
                    $('.captchabtn').attr('disabled', true);
                    $('#captchaInput2').attr('disabled', true);
                    successMessage(data.message);
                } else {
                    errorMessage(data.message);

                    $("#captcha-error2").text(data.message);
                }
            });
        }


        function CheckUser() {
            $(".loader-overlay").show();
            let email = $('#emailInput').val();
            if(email != "" && email.trim() !== '') {
                $("#email-error").text("");
                fetch('{{route("login.checkUser")}}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ email })
                })
                .then(response => response.json())
                .then(data => {
                    console.log(data.success);
                    $(".loader-overlay").hide();
                    if (data.success) {
                        successMessage(data.message);

                        $('.ekyc_mode').removeAttr('disabled');
                        $(".emailbutton").removeClass('btn-default').addClass('btn-success');
                        $("#emailInput").attr('disabled', true);
                        $('.emailbutton').attr('disabled', true);
                    } else {
                        $("#email-error").text(data.message);
                    }
                });
            } else {
                $(".loader-overlay").hide();
                $("#email-error").text('Please Enter Email!!');
                // errorMessage('Please Enter Email!!');
            }
           
        }

        function showPasswordBlock() {
            var ekyc_mode = $(".ekyc_mode").val();
            if(ekyc_mode == "Password") {
                reloadCaptcha2();
                setTimeout(() => {
                    $(".password-block").show();
                    $("#password").removeAttr("disabled");       
                }, 1000);        
            }
        }

        $('.submitbtn').click(function () {
            $(".loader-overlay").show();
            // Create a FormData object
            var formData = new FormData($('#loginForm')[0]);
            formData.append('email', $("#emailInput").val());
            // Send an AJAX request
            $.ajax({
                url: '{{route("portal.login")}}', // Replace with your server endpoint
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                type: 'POST',
                data: formData,
                processData: false, // Prevent jQuery from automatically processing the data
                contentType: false, // Prevent jQuery from automatically setting content type
                success: function (response) {
                    // alert('Form submitted successfully!');
                    $(".loader-overlay").hide();
                    successMessage(response.message);

                    window.location.href = response.url;
                  
                },
                error: function (xhr) {
                    $(".loader-overlay").hide();
                    $('.error').remove();
                    
                    if (xhr.status === 422) { 
                        let errors = xhr.responseJSON.errors;
                        for (let field in errors) {
                            $(`[name="${field}"]`).after(`<div class="error text-danger">${errors[field][0]}</div>`);
                        }
                    } else {
                        errorMessage('Something went wrong. Please try again later.');
                    }
                }
            });
        });
    </script>
</body>
</html>