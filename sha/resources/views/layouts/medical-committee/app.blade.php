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
    @include('layouts.medical-committee.head')
</head>

<body>
<div class="layout-wrapper layout-navbar-full layout-horizontal layout-without-menu">
        <div class="layout-container">
            @include('layouts.medical-committee.header')
            <div class="layout-page">
                <div class="content-wrapper">

                    <aside id="layout-menu" class="layout-menu-horizontal menu-horizontal menu bg-menu-theme flex-grow-0">
                        <div class="w-100 h-100">
                            <div class="row g-0">
                                <div class="col-md-5">
                                <div class="d-flex align-items-center bg-theme-color arrow">
                                        <ul class="menu-list mb-0 py-2  d-flex">
                                            <li class="menu-item">
                                            <a href="{{ route('medical-committee.dashboard') }}" class="menu-link bottom-menu-icons">
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
                                            @if(isset($case_profile))
                                                <button class="btn rounded-pill btn-outline-primary waves-effect btn-xs mt-4" onclick="caseProfile('{{ $case_profile }}')">Case Profile</button>
                                            @endif
                                            @if(isset($hospital_profile))
                                                <button class="btn rounded-pill btn-outline-primary waves-effect btn-xs mt-4" onclick="hospitalProfile('{{ $hospital_profile }}')">Hospital Profile</button>
                                            @endif
                                        </div>
                                        @if(isset($pending_since))
                                            <div class="col-md-7">
                                                <div class="d-flex justify-content-center align-items-center">
                                                    <div class="text-center me-3">
                                                        <div class="d-flex justify-content-center mt-2 align-items-center">
                                                            <span class="text-muted"><small>Case pending since:&nbsp;</small></span>
                                                            <div class="countdown-box me-1"><span id="days">00</span><span class="label">Days</span></div>
                                                            <div class="countdown-box me-1"><span id="hours">00</span><span class="label">Hrs</span></div>
                                                            <div class="countdown-box me-1"><span id="minutes">00</span><span class="label">Mins</span></div>
                                                            <div class="countdown-box"><span id="seconds">00</span><span class="label">Secs</span></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </aside>
                    @yield('content')

                    @include('layouts.medical-committee.footer')

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

    <div class="modal fade" id="caseProfileModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header bg-primary text-white">
                    <h4 class="modal-title mb-4 text-white" id="caseProfileModalLabel3">Case Profile</h4>
                    <button type="button" class="btn-close mb-4 text-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <!-- Modal Body -->
                <div class="modal-body p-0">
                    <div class="card">
                        <div class="card-body case-body">
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="hospitalProfileModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header bg-primary text-white">
                    <h4 class="modal-title mb-4 text-white" id="hospitalProfileModalLabel3">Hospital Profile</h4>
                    <button type="button" class="btn-close mb-4 text-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <!-- Modal Body -->
                <div class="modal-body p-0">
                    <div class="card">
                        <div class="card-body hospital-body">
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->
    @include('layouts.medical-committee.scripts')
    <script>
        @if(isset($pending_since))
            $(document).ready(function () {
                let pendingSince = new Date("{{ $pending_since }}");
                let spentTime = 0;

                function updateCountdown() {
                    let now = new Date().getTime();
                    let distance = now - pendingSince;

                    let days = Math.floor(distance / (1000 * 60 * 60 * 24));
                    let hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    let seconds = Math.floor((distance % (1000 * 60)) / 1000);

                    $("#days").text(days.toString().padStart(2, '0'));
                    $("#hours").text(hours.toString().padStart(2, '0'));
                    $("#minutes").text(minutes.toString().padStart(2, '0'));
                    $("#seconds").text(seconds.toString().padStart(2, '0'));

                    // // Updating time spent on case
                    // let spentMins = Math.floor(spentTime / 60);
                    // let spentSecs = spentTime % 60;

                    // $("#spent-minutes").text(spentMins.toString().padStart(2, '0'));
                    // $("#spent-seconds").text(spentSecs.toString().padStart(2, '0'));

                    // spentTime++;
                }

                updateCountdown();
                setInterval(updateCountdown, 1000);
            });
        @endif
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
            function caseProfile(case_id){
                $(".loader-overlay").show();
                $.ajax({
                    url: '{{route("case-profile")}}',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    type: 'POST',
                    data: { case_id:case_id },
                    success: function (response) {
                        $(".loader-overlay").hide();
                        if(response.success){
                            $("#caseProfileModal").modal("show");
                            $(".case-body").html(response.html);
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
        @if(isset($hospital_profile))
            function hospitalProfile(hospital_id){
                $(".loader-overlay").show();
                $.ajax({
                    url: '{{route("hospital-profile")}}',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    type: 'POST',
                    data: { hospital_id:hospital_id },
                    success: function (response) {
                        $(".loader-overlay").hide();
                        if(response.success){
                            $("#hospitalProfileModal").modal("show");
                            $(".hospital-body").html(response.html);
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
