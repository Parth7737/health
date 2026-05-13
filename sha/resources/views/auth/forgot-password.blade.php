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
                        <h4 class="text-warning mb-4">Reset Password</h4>
                        <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                            {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
                        </div>
                        <x-auth-session-status class="mb-4" :status="session('status')" />
                        <form method="POST" action="{{ route('password.email') }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Email*</label>
                                <input type="email" class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" id="email" required name="email" placeholder="Enter the email" value="{{ old('email') }}">
                                <x-input-error :messages="$errors->get('email')" class="mt-2 text-danger" />

                            </div>
                            
                            <button type="submit" class="btn btn-secondary w-100 mb-3 submitbtn" >SUBMIT</button>

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
      

    </script>
</body>
</html>