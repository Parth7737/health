<nav class="layout-navbar navbar navbar-expand-xl align-items-center bg-navbar-theme" id="layout-navbar">
    <div class="container-xxl">
        <div class="navbar-brand app-brand demo d-none d-xl-flex py-0 me-6">
            <a href="{{\App\CentralLogics\Helpers::getDashboardRedirect(auth()->user())}}" class="app-brand-link gap-2">
                <span class="app-brand-logo demo">
                    <span style="color: var(--bs-primary)">
                        <img src="{{asset('public/front/assets/img/n_logo-removebg-preview.png')}}" class="web-logo" alt="logo" />
                    </span>
                </span>
                <img src="{{asset('public/front/assets/img/PMJAY-1.png')}}" class="pmjay-logo" alt="logo" />
                <span class="app-brand-text demo menu-text text-white fw-semibold">SHA: UTTARAKHAND</span>
            </a>

            <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-xl-none">
                <i class="ri-close-fill align-middle"></i>
            </a>
        </div>

        <!-- <div class="layout-menu-toggle navbar-nav align-items-xl-center me-4 me-xl-0 d-xl-none">
            <a class="nav-item nav-link px-0 me-xl-6" href="javascript:void(0)">
                <i class="ri-menu-fill ri-22px"></i>
            </a>
        </div> -->

        <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
            <ul class="navbar-nav flex-row align-items-center ms-auto">
                <li class="nav-item navbar-search-wrapper me-2">
                    <a class="nav-link btn buttons-aplus fw-normal" id="zoomIn" href="javascript:void(0);">
                        A+
                    </a>
                </li>
                <li class="nav-item navbar-search-wrapper me-2">
                    <a class="nav-link btn buttons-aplus fw-normal" id="resetZoom" href="javascript:void(0);">
                        A
                    </a>
                </li>
                <li class="nav-item navbar-search-wrapper me-2">
                    <a class="nav-link btn buttons-aplus fw-normal" id="zoomOut" href="javascript:void(0);">
                        A-
                    </a>
                </li>
                <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-4 me-xl-1">
                    <a class="nav-link btn btn-text-secondary rounded-pill btn-icon dropdown-toggle hide-arrow"
                        href="javascript:void(0);" data-bs-toggle="dropdown" data-bs-auto-close="outside"
                        aria-expanded="false">
                        <i class="ri-notification-2-line ri-22px"></i>
                        <span
                            class="position-absolute top-0 start-50 translate-middle-y badge badge-dot bg-danger mt-2 border"></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end py-0">
                        
                    </ul>
                </li>
                <li class="nav-item navbar-dropdown dropdown-user dropdown">
                    <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);"
                        data-bs-toggle="dropdown">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-online">
                                <img src="{{ auth()->user()->profile_image }}" class="rounded-circle" alt="logo" />
                            </div>
                            <div class="ms-3">
                                <h5 class="mb-0 text-white"> {{auth()->user()->name}}</h5>
                                <p class="mb-0 fs-xsmall text-light-white"> {{auth()->user()->role->name}}</p>
                            </div>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="pages-account-settings-account.html">
                                <div class="d-flex">
                                    <div class="flex-shrink-0 me-2">
                                        <div class="avatar avatar-online">
                                            <img src="{{ auth()->user()->profile_image }}" class="rounded-circle" alt="logo" />
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <span class="fw-medium d-block small">{{auth()->user()->name}}</span>
                                        <small class="text-muted">{{auth()->user()->role->name}}</small>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li>
                            <div class="dropdown-divider"></div>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                <i class="ri-user-3-line ri-22px me-3"></i><span class="align-middle">My
                                    Profile</span>
                            </a>
                        </li>
                        
                        
                        <li>
                            <div class="d-grid px-4 pt-2 pb-1">
                                <a class="btn btn-sm btn-danger d-flex" href="javascript:;" onClick="document.getElementById('logoutform').submit()">
                                    <small class="align-middle">Logout</small>
                                    <i class="ri-logout-box-r-line ms-2 ri-16px"></i>
                                </a>

                                <form action="{{ route('logout') }}" id="logoutform" method="POST">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    </ul>
                </li>
                <!--/ User -->
            </ul>
        </div>

        <!-- Search Small Screens -->
        <!-- <div class="navbar-search-wrapper search-input-wrapper container-xxl d-none">
            <input type="text" class="form-control search-input border-0" placeholder="Search..."
                aria-label="Search..." />
            <i class="ri-close-fill search-toggler cursor-pointer"></i>
        </div> -->
    </div>
</nav>