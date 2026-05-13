<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SHA Uttarakhand</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
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
                        <h4 class="text-warning mb-4">ADMIN LOGIN</h4>
                        
                        <form id="" method="POST" action="{{ route('login') }}">
                          @csrf
                          <div class="form-floating form-floating-outline mb-5">
                            <input
                              type="email"
                              class="form-control {{ $errors->get('email')?'is-invalid':'' }}"
                              id="email"
                              name="email"
                              placeholder="Enter your email or username" value="{{ old('email') }}"
                              autofocus />
                            <label for="email">Email or Username</label>
                            <div class="text-danger">
                              @if ($errors->has('email'))
                                  @foreach ($errors->get('email') as $error)
                                      <div>{{ $error }}</div>
                                  @endforeach
                              @endif
                            </div>
                          </div>

                          <div class="mb-5">
                            <div class="form-password-toggle">
                              <div class="input-group input-group-merge">
                                <div class="form-floating form-floating-outline">
                                  <input
                                    type="password"
                                    id="password"
                                    class="form-control {{ $errors->get('password')?'is-invalid':'' }}"
                                    name="password"
                                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                    aria-describedby="password"/>
                                  <label for="password">Password</label>
                                </div>
                              </div>
                              <div class="text-danger">
                                @if ($errors->has('password'))
                                    @foreach ($errors->get('password') as $error)
                                        <div>{{ $error }}</div>
                                    @endforeach
                                @endif
                              </div>
                            </div>
                          </div>
                          <button type="submit" class="btn btn-secondary w-100 mb-3">Sign in</button>

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
    <script>
        // Reload CAPTCHA image
        function reloadCaptcha() {
            $('#captchaImage').attr('src', '{{route('generateCaptcha', ['' => now()->timestamp * 1000])}}');
        }

        function reloadCaptcha2() {
            $('#captchaImage2').attr('src', '{{route('generateCaptcha', ['' => now()->timestamp * 1000])}}');
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

        // Validate CAPTCHA and Enable Email Field
        function validateCaptcha() {
            let captcha = $("#captchaInput").val();
            $("#captcha-error").text("");
            verifyReCaptcha(captcha, function(data) {
                if (data.success) {
                    $('#emailInput').removeAttr('disabled');
                    $('.emailbutton').removeAttr('disabled');
                    $('.captchabtn').attr('disabled', true);
                    $('#captchaInput').attr('disabled', true);
                } else {
                    $("#captcha-error").text(data.message);
                    $('#emailInput').attr('disabled', true);
                    $('.emailbutton').attr('disabled', true);
                }
            });
        }

        function validateCaptcha2() {
            let captcha = $("#captchaInput2").val();
            $("#captcha-error2").text("");
            verifyReCaptcha(captcha, function(data) {
                if (data.success) {
                    $('.submitbtn').removeAttr('disabled');
                    $('.captchabtn2').attr('disabled', true);
                    $('#captchaInput2').attr('disabled', true);
                } else {
                    $("#captcha-error2").text(data.message);
                }
            });
        }


        function CheckUser() {
            $(".loader-overlay").show();
            let email = $('#emailInput').val();
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
                $(".loader-overlay").hide();
                if (data.success) {
                    $('.ekyc_mode').removeAttr('disabled');
                    $(".emailbutton").removeClass('btn-default').addClass('btn-success');
                    $("#emailInput").attr('disabled', true);
                    $('.emailbutton').attr('disabled', true);
                } else {
                    $("#email-error").text(data.message);
                }
            });
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
                        alert('Something went wrong. Please try again later.');
                    }
                }
            });
        });
    </script>
</body>
</html>