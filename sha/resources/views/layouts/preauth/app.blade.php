<!doctype html>

<html lang="en" class="light-style layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default"
    data-assets-path="{{asset('public/front/assets/')}}" data-template="horizontal-menu-template-no-customizer" data-style="light">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>@yield('title')</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{asset('public/front/assets/img/favicon/favicon.ico')}}" />
    @include('layouts.preauth.head')
</head>

<body>
<div class="layout-wrapper layout-navbar-full layout-horizontal layout-without-menu">
        <div class="layout-container">
            @include('layouts.preauth.header')
            <div class="layout-page">
                <div class="content-wrapper">

                    <aside id="layout-menu" class="layout-menu-horizontal menu-horizontal menu bg-menu-theme flex-grow-0">
                        <div class="w-100 h-100">
                            <div class="row g-0">
                                <div class="col-md-5">
                                <div class="d-flex align-items-center bg-theme-color arrow">
                                        <ul class="menu-list mb-0 py-2  d-flex">
                                            <li class="menu-item">
                                            <a href="{{ route('preauth.dashboard') }}" class="menu-link bottom-menu-icons">
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
                                    <div class="row">
                                        <div class="col-md-5">
                                            @if(isset($case_profile))
                                                <button class="btn rounded-pill btn-outline-primary waves-effect btn-xs mt-4" onclick="caseLog('{{ $case_profile }}')">Case Log</button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </aside>
                    @yield('content')

                    @include('layouts.preauth.footer')

                    <div class="content-backdrop fade"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="layout-overlay layout-menu-toggle"></div>
    <div class="drag-target"></div>

    <div class="modal fade" id="caseLogModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header bg-primary text-white">
                    <h4 class="modal-title mb-4 text-white" id="caseLogModalLabel3">Case Logs</h4>
                    <button type="button" class="btn-close mb-4 text-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <!-- Modal Body -->
                <div class="modal-body p-0">
                    <div class="card">
                        <div class="card-body case-log-body">
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->
    @include('layouts.preauth.scripts')
    <script>
        @if(isset($case_profile))
            function caseLog(case_id){
                $(".loader-overlay").show();
                $.ajax({
                    url: '{{route("case-log")}}',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    type: 'POST',
                    data: { case_id:case_id },
                    success: function (response) {
                        $(".loader-overlay").hide();
                        if(response.success){
                            $("#caseLogModal").modal("show");
                            $(".case-log-body").html(response.html);
                        }else{
                            errorMessage(response.message);
                        }
                    },
                    error: function (xhr) {
                        $(".loader-overlay").hide();
                        $('.error').remove();
                        errorMessage('Something went wrong. Please try again later.');
                    }
                });
            }
        @endif
    </script>
</body>
</html>
