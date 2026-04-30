   <!-- Fonts -->
   <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&ampdisplay=swap"
        rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="{{asset('public/front/assets/vendor/fonts/remixicon/remixicon.css')}}" />
    <link rel="stylesheet" href="{{asset('public/front/assets/vendor/fonts/flag-icons.css')}}" />

    <!-- Menu waves for no-customizer fix -->
    <link rel="stylesheet" href="{{asset('public/front/assets/vendor/libs/node-waves/node-waves.css')}}" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{asset('public/front/assets/vendor/css/rtl/core.css')}}" />
    <link rel="stylesheet" href="{{asset('public/front/assets/vendor/css/rtl/theme-default.css')}}" />
    <link rel="stylesheet" href="{{asset('public/front/assets/css/demo.css')}}" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{asset('public/front/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css')}}" />
    <link rel="stylesheet" href="{{asset('public/front/assets/vendor/libs/typeahead-js/typeahead.css')}}" />
    <link rel="stylesheet" href="{{asset('public/front/assets/vendor/libs/apex-charts/apex-charts.css')}}" />
    <link rel="stylesheet" href="{{asset('public/front/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}" />
    <link rel="stylesheet" href="{{asset('public/front/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}" />
    <link rel="stylesheet" href="{{asset('public/front/assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css')}}" />
    <link rel="stylesheet" href="{{asset('public/front/assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.css')}}" />
    <link rel="stylesheet" href="{{asset('public/front/assets/vendor/libs/pickr/pickr-themes.css')}}" />
    <link rel="stylesheet" href="{{asset('public/front/assets/vendor/css/pages/app-logistics-dashboard.css')}}" />
    <link rel="stylesheet" href="{{asset('public/front/assets/vendor/libs/bs-stepper/bs-stepper.css')}}" />
    <script src="{{asset('public/front/assets/vendor/js/helpers.js')}}"></script>
    <script src="{{asset('public/front/assets/js/config.js')}}"></script>
    <link rel="stylesheet" href="{{asset('public/front/assets/vendor/libs/select2/select2.css')}}" />
    <link rel="stylesheet" href="{{asset('public/front/assets/vendor/libs/toastr/toastr.css') }}" />
    <link rel="stylesheet" href="{{asset('public/front/assets/vendor/css/pages/page-profile.css')}}" />

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

    @stack('css')