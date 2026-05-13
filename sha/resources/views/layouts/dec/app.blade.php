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
    @include('layouts.dec.head')
</head>

<body>
<div class="layout-wrapper layout-navbar-full layout-horizontal layout-without-menu">
        <div class="layout-container">
            @include('layouts.dec.header')
            <div class="layout-page">
                <div class="content-wrapper">

                    @yield('content')

                    @include('layouts.dec.footer')

                    <div class="content-backdrop fade"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="layout-overlay layout-menu-toggle"></div>
    <div class="drag-target"></div>

    <!-- <div class="modal fade" id="smallModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">

                        <div class="col-md-12 mb-6 mt-2">
                            <div class="form-floating form-floating-outline">
                                <select id="select2Basic" class="select2 form-select form-select-lg"
                                    data-allow-clear="true">
                                    
                                </select>
                                <label for="select2Basic">Remarks</label>
                            </div>
                        </div>
                        <div class="col-md-12 mt-2">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="nameSmall" class="form-control"
                                    placeholder="Type Remarks Here" />
                                <label for="nameSmall">Reason</label>
                            </div>
                            <span class="fs-xsmall mt-1">Charector limit: 0/100</span>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="button" class="btn btn-primary">Camcel Registration</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="bigModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog " role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <h5 class="mb-3">KAMLESH DEVI</h5>
                    <p class="mb-2">65yr | Female | <span>Program ID:</span><strong>asdsad</strong>
                    </p>
                    <p class="mb-2">Register ID: <strong>qweqeqweqeq</strong></p>
                    <p class="mb-2">Register Date:<strong>19/12/2024</strong></p>
                    <div class="balance-card p-2">
                        <div class="d-block">
                            <h6>Wallet Balance:50000</h6>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-gradient-new" role="progressbar" style="width: 100%;"
                                    aria-valuenow="50" aria-valuemin="0" aria-valuemax="100">
                                </div>
                            </div>
                        </div>
                        <div class="icons d-flex mb-3">
                            <button class="angle-right" data-bs-toggle="modal" data-bs-target="#smallModal">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512">
                                    <path
                                        d="M278.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-160 160c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L210.7 256 73.4 118.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l160 160z" />
                                </svg>
                            </button>
                            <button class="angle-right">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                                    <path
                                        d="M135.2 17.7L128 32 32 32C14.3 32 0 46.3 0 64S14.3 96 32 96l384 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-96 0-7.2-14.3C307.4 6.8 296.3 0 284.2 0L163.8 0c-12.1 0-23.2 6.8-28.6 17.7zM416 128L32 128 53.2 467c1.6 25.3 22.6 45 47.9 45l245.8 0c25.3 0 46.3-19.7 47.9-45L416 128z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-6 mt-2">
                            <div class="form-floating form-floating-outline">
                                <select id="select2Basic" class="select2 form-select form-select-lg"
                                    data-allow-clear="true">
                                    <option value="AK">Alaska</option>
                                    <
                                </select>
                                <label for="select2Basic">Select Reason</label>
                            </div>
                        </div>
                        <div class="col-md-12 mt-2">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="nameSmall" class="form-control"
                                    placeholder="Type Remarks Here" />
                                <label for="nameSmall">Cancelled</label>
                            </div>
                            <span class="fs-xsmall mt-1">Charector limit: 9/100</span>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="button" class="btn btn-primary">Camcel Registration</button>
                </div>
            </div>
        </div>
    </div> -->
    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->
    @include('layouts.hospital.scripts')
</body>
</html>
