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
    <link rel="stylesheet" type="text/css" href="{{asset('public/front/assets/css/vendors/slick.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('public/front/assets/css/vendors/slick-theme.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('public/front/assets/css/vendors/scrollbar.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('public/front/assets/css/vendors/select/bootstrap-select.min.css')}}">
    <!-- Plugins css Ends-->
    @stack('css')
    @stack('styles')
    <!-- Bootstrap css-->
    <link rel="stylesheet" type="text/css" href="{{asset('public/front/assets/css/vendors/bootstrap.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('public/front/assets/css/vendors/select2.min.css')}}">
    <!-- App css-->
    <link rel="stylesheet" type="text/css" href="{{asset('public/front/assets/css/style.css')}}">
    <link id="color" rel="stylesheet" href="{{asset('public/front/assets/css/color-1.css')}}" media="screen">
    <!-- Responsive css-->
    <link rel="stylesheet" type="text/css" href="{{asset('public/front/assets/css/responsive.css')}}">

    <style>
        /* Global modal glass effect for consistent project UI */
        .modal-backdrop.show {
            opacity: 1;
            background: rgba(15, 23, 42, 0.34);
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
        }

        .modal.fade .modal-dialog {
            transform: translate(0, -12px) scale(0.98);
            transition: transform 0.22s ease, opacity 0.22s ease;
        }

        .modal.show .modal-dialog {
            transform: translate(0, 0) scale(1);
        }

        .modal-dialog:not(.modal-fullscreen):not([class*='modal-fullscreen-']) .modal-content {
            border: 1px solid rgba(148, 163, 184, 0.35);
            border-radius: 18px;
            box-shadow: 0 20px 60px rgba(2, 6, 23, 0.22);
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: saturate(140%) blur(2px);
            -webkit-backdrop-filter: saturate(140%) blur(2px);
            overflow: hidden;
        }

        .modal .modal-header {
            border-bottom-color: rgba(148, 163, 184, 0.25);
        }

        .modal .modal-footer {
            border-top-color: rgba(148, 163, 184, 0.25);
        }
    </style>
    
