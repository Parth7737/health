@php 
    $logo = App\Models\BusinessSetting::where('key','front_logo')->value('value');
    if($logo){
        $logo = asset('public/storage/'.$logo);
    }else{
        $logo = asset('public/front/assets/img/paracare-logo.png');
    }
@endphp 
<!-- Page Sidebar Start-->
    <nav class="sidebar">
        <div class="sidebar-logo">
            <div class="logo-wrap">
                <div class="logo-orb"><i class="fas fa-landmark"></i></div>
                <div class="logo-txt">
                    <div class="l1">ParaCare+ HMIS</div>
                    <div class="l2">Facility Onboarding</div>
                </div>
            </div>
        </div>
        <div class="nav-grp">
            <div class="nav-grp-title">Onboarding</div>
            <a class="nav-item active" href="#"><span class="ni"><i class="fas fa-plus-circle"></i></span>New
                Onboarding</a>
            <a class="nav-item" href="#" onclick="switchView('tracker')"><span class="ni"><i
                        class="fas fa-tasks"></i></span>Track Applications</a>
            <a class="nav-item" href="#" onclick="switchView('register')"><span class="ni"><i
                        class="fas fa-hospital"></i></span>Facility Register</a>
        </div>
        <div class="nav-grp">
            <div class="nav-grp-title">Navigation</div>
            <a class="nav-item" href="{{ route('state-admin.dashboard.index') }}"><span class="ni"><i
                        class="fas fa-tachometer-alt"></i></span>State Dashboard</a>
            <a class="nav-item" href="javascript:;" onClick="document.getElementById('logoutform').submit()"><span class="ni"><i class="fas fa-sign-out-alt"></i></span>Logout</a>
            <form action="{{ route('logout') }}" id="logoutform" method="POST">
                @csrf
            </form>
        </div>
    </nav>
<!-- Page Sidebar Ends-->