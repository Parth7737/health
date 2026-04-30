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
    <!-- Bootstrap css -->
    <link rel="stylesheet" type="text/css" href="{{asset('public/front/assets/css/vendors/bootstrap.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('public/front/assets/css/vendors/select2.min.css')}}">
    <!-- App css -->
    <link rel="stylesheet" type="text/css" href="{{asset('public/front/assets/css/style.css')}}">
    <link id="color" rel="stylesheet" href="{{asset('public/front/assets/css/color-1.css')}}" media="screen">
    <!-- Responsive css -->
    <link rel="stylesheet" type="text/css" href="{{asset('public/front/assets/css/responsive.css')}}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Noto+Sans+Devanagari:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- HIMS css must load last so dashboard shell styles win -->
    <link rel="stylesheet" type="text/css" href="{{asset('public/front/assets/css/hims.css')}}">
    
    <style>
        /* Keep Bootstrap modal behavior deterministic. */
        .modal.fade:not(.show) {
            display: none !important;
        }

        .modal.show {
            display: block !important;
        }

        /* ─── FULL PAGE LOADER ─── */
        .full-page-loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.9);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        /* ─── SIDEBAR STYLING ─── */
        .gov-sidebar {
            width: var(--sidebar-w);
            min-width: var(--sidebar-w);
            background: var(--sidebar-bg);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            z-index: 200;
            overflow-y: auto;
            overflow-x: hidden;
            border-right: 1px solid rgba(255, 255, 255, 0.05);
            transition: transform var(--t-base);
        }

        .sidebar-facility-tag {
            margin: 10px 14px;
            padding: 8px 12px;
            border-radius: var(--r-md);
            background: rgba(21, 101, 192, .15);
            border: 1px solid rgba(21, 101, 192, .25);
        }

        .sidebar-facility-tag .facility-name {
            font-size: 11.5px;
            font-weight: 600;
            color: #c8e0f4;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sidebar-facility-tag .facility-type {
            font-size: 10px;
            color: var(--sidebar-muted);
            text-transform: uppercase;
            letter-spacing: .05em;
        }

        .hims-sidebar-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(2, 8, 18, 0.56);
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            transition: opacity .18s ease, visibility .18s ease;
            z-index: 190;
        }

        .hims-sidebar-backdrop.open {
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
        }

        #govMenuToggle {
            display: none;
        }

        @media (max-width: 900px) {
            #govMenuToggle {
                display: inline-flex;
            }

            .gov-sidebar {
                transform: translateX(-104%);
            }

            .gov-sidebar.open {
                transform: translateX(0);
            }

            .hims-main {
                margin-left: 0;
            }

            body.sidebar-open {
                overflow: hidden;
            }
        }

        #cog-click {
            display: none !important;
        }

        /* Header profile popup (separate from Bootstrap .modal). */
        .hims-profile-overlay {
            position: fixed;
            inset: 0;
            z-index: 1000;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            animation: overlayIn 0.2s ease;
        }

        .hims-profile-overlay.hidden {
            display: none !important;
        }

        @keyframes overlayIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .hims-profile-modal {
            background: var(--surface);
            border-radius: var(--r-xl);
            box-shadow: var(--shadow-xl);
            width: 100%;
            max-width: 440px;
            max-height: 90vh;
            display: flex;
            flex-direction: column;
            animation: modalIn var(--t-slow) cubic-bezier(.34,1.56,.64,1);
        }

        #profileModal {
            display: none;
        }

        #profileModal.is-open {
            display: flex !important;
        }
    </style>