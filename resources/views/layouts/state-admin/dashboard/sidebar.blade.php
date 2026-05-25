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
                    <div class="l2">State Command Centre</div>
                </div>
            </div>
        </div>
        <div class="state-badge">
            <div class="sb-dot"></div>
            <div class="sb-txt">Uttarakhand State Health<span class="sb-sub">Office of Health Secretary</span></div>
        </div>

        <div class="nav-grp">
            <div class="nav-grp-title">Overview</div>
            <a class="nav-item active" onclick="showTab('overview')"><span class="ni"><i
                        class="fas fa-tachometer-alt"></i></span>State Dashboard</a>
            <a class="nav-item" onclick="showTab('map')"><span class="ni"><i
                        class="fas fa-map-marked-alt"></i></span>Health Heatmap</a>
            <a class="nav-item" onclick="showTab('districts')"><span class="ni"><i
                        class="fas fa-table"></i></span>District Scorecard</a>
        </div>
        <div class="nav-grp">
            <div class="nav-grp-title">Clinical</div>
            <a class="nav-item" onclick="showTab('opd')"><span class="ni"><i class="fas fa-user-md"></i></span>OPD / IPD
                Analytics</a>
            <a class="nav-item" onclick="showTab('disease')"><span class="ni"><i class="fas fa-virus"></i></span>Disease
                Surveillance<span class="nav-badge">3</span></a>
            <a class="nav-item" onclick="showTab('mch')"><span class="ni"><i class="fas fa-baby"></i></span>MCH &
                Immunisation</a>
            <a class="nav-item" onclick="showTab('lab')"><span class="ni"><i class="fas fa-flask"></i></span>Lab &
                Diagnostics</a>
            <a class="nav-item" onclick="showTab('pharma')"><span class="ni"><i class="fas fa-pills"></i></span>Pharmacy
                & Drugs</a>
        </div>
        <div class="nav-grp">
            <div class="nav-grp-title">Finance</div>
            <a class="nav-item" onclick="showTab('revenue')"><span class="ni"><i
                        class="fas fa-rupee-sign"></i></span>Revenue & Billing</a>
            <a class="nav-item" onclick="showTab('ab')"><span class="ni"><i
                        class="fas fa-hospital-user"></i></span>Ayushman Bharat<span
                    class="nav-badge amber">12</span></a>
        </div>
        <div class="nav-grp">
            <div class="nav-grp-title">Operations</div>
            <a class="nav-item" onclick="showTab('facilities')"><span class="ni"><i
                        class="fas fa-hospital"></i></span>Facilities</a>
            <a class="nav-item" onclick="showTab('hr')"><span class="ni"><i class="fas fa-users"></i></span>HR &
                Workforce</a>
            <a class="nav-item" onclick="showTab('ambulance')"><span class="ni"><i
                        class="fas fa-ambulance"></i></span>Ambulance / 108</a>
            <a class="nav-item" onclick="showTab('inventory')"><span class="ni"><i
                        class="fas fa-boxes"></i></span>Inventory & Drugs</a>
        </div>
        <div class="nav-grp">
            <div class="nav-grp-title">Admin</div>
            <a class="nav-item" href="{{ route('state-admin.onboard-facility') }}"><span class="ni"><i class="fas fa-plus-circle"></i></span>Onboard
                Facility<span class="nav-badge green">New</span></a>
            <a class="nav-item" href="javascript:;" onClick="document.getElementById('logoutform').submit()"><span class="ni"><i class="fas fa-sign-out-alt"></i></span>Logout</a>
            <form action="{{ route('logout') }}" id="logoutform" method="POST">
                @csrf
            </form>
        </div>
    </nav>
<!-- Page Sidebar Ends-->