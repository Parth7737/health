@php
    $logo = App\Models\BusinessSetting::where('key', 'front_logo')->value('value');
    $logo = $logo ? asset('public/storage/' . $logo) : asset('public/front/assets/img/paracare-logo.png');
@endphp
<nav class="eo-onboarding-navbar" id="eoOnboardingNavbar">
    <div class="eo-onb-inner">
        <div class="eo-onb-brand">
            <div class="eo-onb-orb"><i class="fas fa-landmark"></i></div>
            <div class="eo-onb-brand-txt">
                <div class="eo-onb-l1">ParaCare+ HMIS</div>
                <div class="eo-onb-l2">Facility onboarding</div>
            </div>
            <a href="{{ \App\CentralLogics\Helpers::getDashboardRedirect(auth()->user()) }}" class="eo-onb-logo-link ms-2 d-none d-md-inline">
                <img src="{{ $logo }}" alt="Logo" class="eo-onb-logo-img" />
            </a>
        </div>
        <div class="eo-onb-center">
            <i class="fas fa-plus-circle" style="color:#81c784"></i>
            <span class="eo-onb-title">Facility onboarding</span>
            <span class="eo-module-chip">HOSPITAL USER</span>
        </div>
        <div class="eo-onb-actions">
            <a href="{{ \App\CentralLogics\Helpers::getDashboardRedirect(auth()->user()) }}" class="eo-tb-btn"><i
                    class="fas fa-tachometer-alt"></i> Dashboard</a>
            <div class="dropdown">
                <button class="eo-tb-btn eo-onb-user-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="avatar avatar-sm me-1">
                        <img src="{{ auth()->user()->profile_image }}" class="rounded-circle" width="28" height="28"
                            alt="" />
                    </span>
                    <span class="d-none d-sm-inline">{{ \Illuminate\Support\Str::limit(auth()->user()->name, 18) }}</span>
                    <i class="fas fa-chevron-down ms-1 small"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end eo-onb-dd">
                    <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="fas fa-user me-2"></i>My
                            profile</a></li>
                    <li>
                        <hr class="dropdown-divider" />
                    </li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST" class="px-3 py-2 mb-0">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-danger w-100"><i
                                    class="fas fa-sign-out-alt me-1"></i>Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>
