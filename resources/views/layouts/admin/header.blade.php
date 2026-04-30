@php 
    $logo = App\Models\BusinessSetting::where('key','front_logo')->value('value');
    if($logo){
        $logo = asset('public/storage/'.$logo);
    }else{
        $logo = asset('public/front/assets/img/paracare-logo.png');
    }
@endphp
<div class="page-header">
    <div class="header-wrapper row m-0">
        <form class="form-inline search-full col" action="#" method="get">
        <div class="form-group w-100">
            <div class="Typeahead Typeahead--twitterUsers">
            <div class="u-posRelative">
                <input class="demo-input Typeahead-input form-control-plaintext w-100" type="text" placeholder="Search Anything Here..." name="q" title="" autofocus>
                <div class="spinner-border Typeahead-spinner" role="status"><span class="sr-only">Loading...</span></div><i class="close-search" data-feather="x"></i>
            </div>
            <div class="Typeahead-menu"></div>
            </div>
        </div>
        </form>
        <div class="header-logo-wrapper col-auto p-0">
        <div class="logo-wrapper"><a href="{{ route('new-dashboard') }}"><img class="img-fluid for-light" src="{{asset('public/front/assets/images/logo/logo.png') }}" alt=""><img class="img-fluid for-dark" src="{{asset('public/front/assets/images/logo/logo_dark.png')}}" alt=""></a></div>
        <div class="toggle-sidebar"><i class="status_toggle middle sidebar-toggle" data-feather="align-center"></i></div>
        </div>
        <div class="left-header col-xxl-5 col-xl-6 col-lg-5 col-md-4 col-sm-3 p-0">
            <div class="logo-header">
              <a href="{{ route('admin.dashboard.index') }}">
                <img class="img-fluid" src="{{asset($logo) }}" height="50" alt="Paracare Logo">
              </a>
            </div>
        </div>
        <div class="nav-right col-xxl-7 col-xl-6 col-md-7 col-8 pull-right right-header p-0 ms-auto">
        <ul class="nav-menus">
           
            <li class="fullscreen-body" data-bs-toggle="tooltip" title="Full Screen" data-bs-placement="bottom">                       <span>
                <svg id="maximize-screen">
                <use href="{{asset('public/front/assets/svg/icon-sprite.svg#full-screen')}}"></use>
                </svg></span></li>
            <li data-bs-toggle="tooltip" title="Search" data-bs-placement="bottom">                         <span class="header-search">
                <svg>
                <use href="{{asset('public/front/assets/svg/icon-sprite.svg#search')}}"></use>
                </svg></span></li>
          
            <li data-bs-toggle="tooltip" title="Dark/Light" data-bs-placement="bottom">
            <div class="mode">
                <svg>
                <use href="{{asset('public/front/assets/svg/icon-sprite.svg#moon')}}"></use>
                </svg>
            </div>
            </li>
            <li class="profile-nav onhover-dropdown pe-0 py-0">
                <div class="d-flex profile-media"><img class="b-r-10" src="{{ auth()->user()->profile_image }}" alt="" width="30px" height="30px">
                    <div class="flex-grow-1"><span>{{auth()->user()->name}}</span>
                    <p class="mb-0">{{auth()->user()->getRoleNames()->first() }} <i class="middle fa-solid fa-angle-down"></i></p>
                    </div>
                </div>
                <ul class="profile-dropdown onhover-show-div">
                    <li><a href="{{route('admin.profile')}}"><i data-feather="user"></i><span>Account</span></a></li>
                    <li><a href="{{route('admin.settings.index')}}"><i data-feather="settings"></i><span>Settings</span></a></li>
                    <li><a  href="javascript:;" onClick="document.getElementById('logoutform').submit()"><i data-feather="log-in"> </i><span>Log out</span></a></li>
                    <form action="{{ route('logout') }}" id="logoutform" method="POST">
                        @csrf
                    </form>
                </ul>
            </li>

        </ul>
        </div>
        <script class="result-template" type="text/x-handlebars-template">
        <div class="ProfileCard u-cf">
        <div class="ProfileCard-avatar"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-airplay m-0"><path d="M5 17H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-1"></path><polygon points="12 15 17 21 7 21 12 15"></polygon></svg></div>
        <div class="ProfileCard-details">
        <div class="ProfileCard-realName">Name</div>
        </div>
        </div>
        </script>
        <script class="empty-template" type="text/x-handlebars-template"><div class="EmptyMessage">Your search turned up 0 results. This most likely means the backend is down, yikes!</div></script>
    </div>
</div>
<!-- Page Header End-->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.forEach(function (tooltipTriggerEl) {
            new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
