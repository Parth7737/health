@php 
    $logo = App\Models\BusinessSetting::where('key','front_logo')->value('value');
    $background_image = App\Models\BusinessSetting::where('key','background_image')->value('value');
    if($logo){
        $logo = asset('public/storage/'.$logo);
    }else{
        $logo = asset('public/front/assets/img/paracare-logo.png');
    }
    if($background_image){
        $background_image = asset('public/storage/'.$background_image);
    }
@endphp 
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Signup - {{ App\Models\BusinessSetting::where('key','site_title')->value('value') }}">
    <meta name="keywords" content="Signup - {{ App\Models\BusinessSetting::where('key','site_title')->value('value') }}">
    <meta name="author" content="Signup - {{ App\Models\BusinessSetting::where('key','site_title')->value('value') }}">
    <link rel="icon" href="{{asset('public/front/assets/images/favicon.png')}}" type="image/x-icon">
    <link rel="shortcut icon" href="{{$logo}}" type="image/x-icon">
    <title>Signup - {{ App\Models\BusinessSetting::where('key','site_title')->value('value') }}</title>
    <!-- Google font-->
    <link href="https://fonts.googleapis.com/css?family=Rubik:400,400i,500,500i,700,700i&amp;display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,500,500i,700,700i,900&amp;display=swap" rel="stylesheet">
    <!-- Font Awesome-->
    <link rel="stylesheet" type="text/css" href="{{asset('public/front/assets/css/fontawesome.css')}}">
    <!-- ico-font-->
    <link rel="stylesheet" type="text/css" href="{{asset('public/front/assets/css/vendors/icofont.css')}}">
    <!-- Themify icon-->
    <link rel="stylesheet" type="text/css" href="{{asset('public/front/assets/css/vendors/themify.css')}}">
    <!-- Flag icon-->
    <link rel="stylesheet" type="text/css" href="{{asset('public/front/assets/css/vendors/flag-icon.css')}}">
    <!-- Feather icon-->
    <link rel="stylesheet" type="text/css" href="{{asset('public/front/assets/css/vendors/feather-icon.css')}}">
    <!-- Plugins css start-->
    <!-- Plugins css Ends-->
    <!-- Bootstrap css-->
    <link rel="stylesheet" type="text/css" href="{{asset('public/front/assets/css/vendors/bootstrap.css')}}">
    <!-- App css-->
    <link rel="stylesheet" type="text/css" href="{{asset('public/front/assets/css/style.css')}}">
    <link id="color" rel="stylesheet" href="{{asset('public/front/assets/css/color-1.css')}}" media="screen">
    <!-- Responsive css-->
    <link rel="stylesheet" type="text/css" href="{{asset('public/front/assets/css/responsive.css')}}">
    <link rel="stylesheet" href="{{asset('public/front/assets/vendor/libs/toastr/toastr.css') }}" />
  </head>
  <body>
    <!-- login page start-->
    <div class="container-fluid">
      <div class="row">
        <div class="col-xl-7">
          @if($background_image)
            <img class="bg-img-cover bg-center" src="{{$background_image}}" alt="looginpage">
          @endif
        </div>
        <div class="col-xl-5 p-0">
          <div class="login-card login-dark">
            <div>
              <div><a class="logo text-center" href=""><img width="150px" class="img-fluid for-light" src="{{$logo}}" alt="looginpage"><img width="150px"  class="img-fluid for-dark" src="{{$logo}}" alt="looginpage"></a></div>
              <div class="login-main">
                <form id="loginForm" method="POST" class="theme-form">
                    @csrf
                    <h4>Sign up to account</h4>
                    <div class="form-group">
                        <!-- <label class="col-form-label">Email Address</label>
                        <input class="form-control submiterror email"  oninput="sanitize(this, 'email');" type="email" required="" value="{{ old('email') }}" name="email" id="email" placeholder=""> -->
                        <label class="form-label">Email</label>
                        <div class="input-group submiterror">
                            <input type="text" name="email"  id="emailInput"  oninput="sanitize(this, 'email');" class="form-control email" placeholder="Type here">
                            <button class="btn btn-success emailbutton" type="button"  onclick="CheckEmail();" title="validate"><i class="fa fa-check"></i></button>
                        </div>
                        <span class="email-error text-danger"></span>
                    </div>
                    <div class="form-group">
                        <div id="email_otp_block" style="display:none">
                            <label class="form-label">Enter Email OTP</label>
                            <input type="text" name="email_otp" id="email_otp" class="form-control submiterror" placeholder="Type here">
                            <a href="javascript:;" onClick="ResendOtp('Email')" class="resendEmailOtp" style="float:right">Resend OTP</a>
                            <span class="emailotpverify text-danger"></span>
                        </div>            
                    </div>
                    <div class="form-group">
                        <label class="form-label">Mobile</label>
                        <div class="input-group submiterror">
                            <input type="text" name="mobile_no"  id="mobileInput"  class="form-control mobile_no" oninput="validateMobileNo(this);" placeholder="Type here">
                        </div>
                        <span class="mobile-error text-danger"></span>         
                    </div>
                    <div class="form-group mb-0">
                        <button class="btn btn-primary btn-block w-100 mt-3 submitbtn" disabled onClick="submitData();" type="button">Signup</button>
                    </div>
                    <p class="mt-4 mb-0 text-center">You have account?<a class="ms-2" href="{{route('hospital.login')}}">Login</a></p>
                </form>
                </div>
            </div>
          </div>
        </div>
      </div>
      <!-- latest jquery-->
      <script src="{{asset('public/front/assets/js/jquery.min.js')}}"></script>
      <!-- Bootstrap js-->
      <script src="{{asset('public/front/assets/js/bootstrap/bootstrap.bundle.min.js')}}"></script>
      <!-- feather icon js-->
      <script src="{{asset('public/front/assets/js/icons/feather-icon/feather.min.js')}}"></script>
      <script src="{{asset('public/front/assets/js/icons/feather-icon/feather-icon.js')}}"></script>
      <!-- scrollbar js-->
      <!-- Sidebar jquery-->
      <script src="{{asset('public/front/assets/js/config.js')}}"></script>
      <!-- Plugins JS start-->
      <!-- Plugins JS Ends-->
      <!-- Theme js-->
      <script src="{{asset('public/front/assets/js/script.js')}}"></script>
      <script src="{{asset('public/front/assets/js/script1.js')}}"></script>
      <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
      <script src="{{asset('public/front/assets/vendor/libs/toastr/toastr.js') }}"></script>
      <script src="{{asset('public/front/assets/js/ui-toasts.js') }}"></script>
      <script src="{{asset('public/front/assets/js/sanitize.js') }}"></script>

      <script>
        $(document).ready(function(){
            $("#emailInput").focus();
        })
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
        
        $("#email_otp").on("input",function(){
            sanitize(this, 'b',6);
            if($(this).val().length == 6){
                OTPVerification();
            }
        })
        $("#mobileinput").on("input",function(){
            sanitize(this, 'b',10);
            if($(this).val().length == 10){
                $(".submitbtn").focus();
            }
        })
        function validateMobileNo(input) {
            // Remove any non-numeric characters
            input.value = input.value.replace(/[^0-9]/g, '');

            // Limit the length to 10 digits
            if (input.value.length > 10) {
                input.value = input.value.slice(0, 10);
            }
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
                    $("#email_otp").focus();
                    successMessage(data.message);
                } else {
                    $(".email-error").text(data.message);
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

        function VerifyOTP(otp, type, email='', callback) {
            $(".loader-overlay").show();
            fetch('{{route("verifyEmailOtp")}}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ otp, type, email })
            })
            .then(response => response.json())
            .then(data => {
                $(".loader-overlay").hide();
                if (callback && typeof callback === 'function') {
                    callback(data);
                }
            });
        }

        function OTPVerification() {
            let otp = $("#email_otp").val();
            let email = $('#emailInput').val();
            $(".emailotpverify").text("");
            VerifyOTP(otp, 'Email', email,  function(data) {
                if(data.success) {
                    $("#email_otp").attr('disabled', true);
                    $(".emailbutton").removeClass('btn-default').addClass('btn-success');
                    $(".resendEmailOtp").hide();
                    $(".mobile_no").removeAttr('disabled');
                    $(".mobilebutton").removeAttr('disabled');
                    $(".submitbtn").removeAttr('disabled');
                    $(".submitbtn").addClass('btn-success');
                    $(".submitbtn").removeClass('btn-secondary');
                    $("#mobileInput").focus();
                    successMessage(data.message);
                } else {
                    $(".emailotpverify").text(data.message);
                    errorMessage(data.message);
                }
            });
        };


        function submitData() {
            $(".loader-overlay").show();
            const formData = {
                hospital_name: $('#hospital_name').val(),
                email: $('#emailInput').val(),
                email_otp: $('#email_otp').val(),
                mobile_no: $('.mobile_no').val(),
            };

            $.ajax({
                url: '{{ route("register.store") }}',
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
    </div>
  </body>
</html>