<nav class="layout-navbar navbar navbar-expand-xl align-items-center bg-navbar-theme" id="layout-navbar">
    <div class="container-xxl">
        <div class="navbar-brand app-brand demo d-none d-xl-flex py-0 me-6">
            <a href="{{\App\CentralLogics\Helpers::getDashboardRedirect(auth()->user())}}" class="app-brand-link gap-2">
                <span class="app-brand-logo demo">
                    <span style="color: var(--bs-primary)">
                        @php 
                            $logo = App\Models\BusinessSetting::where('key','front_logo')->value('value');
                            if($logo){
                                $logo = asset('public/storage/'.$logo);
                            }else{
                                $logo = asset('public/front/assets/img/paracare-logo.png');
                            }
                        @endphp 
                        <img src="{{ $logo }}" class="web-logo" alt="logo" />
                    </span>
                </span>
            </a>

            <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-xl-none">
                <i class="ri-close-fill align-middle"></i>
            </a>
        </div>

        <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
            <ul class="navbar-nav flex-row align-items-center ms-auto">
                <li class="nav-item navbar-dropdown dropdown-user dropdown">
                    <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);"
                        data-bs-toggle="dropdown">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-online">
                                <img src="{{ auth()->user()->profile_image }}" class="rounded-circle" alt="logo" />
                            </div>
                            <div class="ms-3">
                                <h5 class="mb-0 text-white"> {{auth()->user()->name}}</h5>
                                <p class="mb-0 fs-xsmall text-light-white">{{auth()->user()->getRoleNames()->first() }}</p>
                            </div>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                <div class="d-flex">
                                    <div class="flex-shrink-0 me-2">
                                        <div class="avatar avatar-online">
                                            <img src="{{ auth()->user()->profile_image }}" class="rounded-circle" alt="logo" />
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <span class="fw-medium d-block small">{{auth()->user()->name}}</span>
                                        <p class="mb-0 fs-xsmall">{{auth()->user()->getRoleNames()->first() }}</p>
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

    </div>
</nav>