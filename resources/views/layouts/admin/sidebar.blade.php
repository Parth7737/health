@php 
    $logo = App\Models\BusinessSetting::where('key','front_logo')->value('value');
    if($logo){
        $logo = asset('public/storage/'.$logo);
    }else{
        $logo = asset('public/front/assets/img/paracare-logo.png');
    }
@endphp 
<!-- Page Sidebar Start-->
    <div class="sidebar-wrapper" data-sidebar-layout="stroke-svg">
        <div>
            <div class="logo-wrapper"><a href="{{ route('admin.dashboard.index') }}"><img class="img-fluid for-light" src="{{asset('public/front/assets/images/logo/logo.png')}}" alt=""><img class="img-fluid for-dark" src="{{asset('public/front/assets/images/logo/logo_dark.png')}}" alt=""></a>
            <div class="back-btn"><i class="fa-solid fa-angle-left"></i></div>
            <div class="toggle-sidebar"><i class="status_toggle middle sidebar-toggle" data-feather="grid"> </i></div>
            </div>
            <div class="logo-icon-wrapper"><a href="{{ route('admin.dashboard.index') }}"><img class="img-fluid" src="{{asset('public/front/assets/images/logo/logo-icon.png')}}" alt=""></a></div>
            <nav class="sidebar-main">
            <div class="left-arrow" id="left-arrow"><i data-feather="arrow-left"></i></div>
              <div id="sidebar-menu">
                  <ul class="sidebar-links" id="simple-bar">
                    <li class="back-btn"><a href="{{ route('admin.dashboard.index') }}"><img class="img-fluid" src="{{asset('public/front/assets/images/logo/logo-icon.png')}}" alt=""></a>
                        <div class="mobile-back text-end"><span>Back</span><i class="fa-solid fa-angle-right ps-2" aria-hidden="true"></i></div>
                    </li>
                    <li class="pin-title sidebar-main-title">
                        <div> 
                          <h6>Pinned</h6>
                        </div>
                    </li>
                    <li class="sidebar-main-title">
                        <div>
                        <h6 class="lan-1">General</h6>
                        </div>
                    </li>
                    <li class="sidebar-list"><i class="fa-solid fa-thumbtack"></i>
                        <label class="badge badge-light-primary">13</label><a class="sidebar-link sidebar-title" href="#">
                        <svg class="stroke-icon">
                            <use href="{{asset('public/front/assets/svg/icon-sprite.svg#stroke-home')}}"></use>
                        </svg>
                        <svg class="fill-icon">
                            <use href="{{asset('public/front/assets/svg/icon-sprite.svg#fill-home')}}"></use>
                        </svg>
                        <span class="lan-3">Dashboard</span></a>
                        <ul class="sidebar-submenu">
                          <li><a class="" href="{{ route('admin.hospitals.index') }}">Hospitals</a></li>
                        </ul>
                    </li>

                    <li class="sidebar-list"><i class="fa-solid fa-thumbtack"></i><a class="sidebar-link sidebar-title" href="#">
                        <svg class="stroke-icon">
                            <use href="{{asset('public/front/assets/svg/icon-sprite.svg#stroke-widget')}}"></use>
                        </svg>
                        <svg class="fill-icon">
                            <use href="{{asset('public/front/assets/svg/icon-sprite.svg#fill-widget')}}"></use>
                        </svg><span class="lan-6">Roles & Permissions</span></a>
                        <ul class="sidebar-submenu">
                          <li><a href="{{ route('admin.roles.index') }}">Roles</a></li>
                          <li><a href="{{ route('admin.modules.index') }}">Modules</a></li>
                          <li><a href="{{ route('admin.permissions.index') }}">Permissions</a></li>
                        </ul>
                    </li>
                    <li class="sidebar-main-title">
                      <div>
                        <h6 class="lan-8">Applications</h6>
                      </div>
                    </li>
                    <li class="sidebar-list"><i class="fa-solid fa-thumbtack"> </i>
                        <a class="sidebar-link sidebar-title" href="#">
                          <svg class="stroke-icon">
                              <use href="{{asset('public/front/assets/svg/icon-sprite.svg#stroke-project')}}"></use>
                          </svg>
                          <svg class="fill-icon">
                              <use href="{{asset('public/front/assets/svg/icon-sprite.svg#fill-project')}}"></use>
                          </svg>
                          <span>Masters</span>
                        </a>
                        <ul class="sidebar-submenu">
                          <li><a href="{{route('admin.hospitaltypes.index')}}">Hospital Type</a></li>
                          <li><a href="{{route('admin.hospital-documents.index')}}">Hospital Document</a></li>
                          <li><a href="{{route('admin.specialities.index')}}">Specialities</a></li>
                          <li><a href="{{route('admin.services.index')}}">Services</a></li>
                          <li><a href="{{route('admin.sub-services.index')}}">Sub Services</a></li>
                          <li><a href="{{route('admin.licenses.index')}}">Licenses</a></li>
                          <li><a href="{{route('admin.license-types.index')}}">License Types</a></li>
                        </ul>
                    </li>
                    <li class="sidebar-list"><i class="fa-solid fa-thumbtack"></i>
                        <a class="sidebar-link sidebar-title link-nav" href="{{ route('admin.settings.index') }}">
                            <svg class="stroke-icon">
                                <use href="{{asset('public/front/assets/svg/icon-sprite.svg#setting')}}"></use>
                            </svg>
                            <svg class="fill-icon">
                                <use href="{{asset('public/front/assets/svg/icon-sprite.svg#setting')}}"> </use>
                            </svg>
                            <span>Settings</span><div class="according-menu"><i class="fa-solid fa-angle-right"></i></div>
                        </a>
                    </li>
                  </ul>
              </div>
              <div class="right-arrow" id="right-arrow"><i data-feather="arrow-right"></i></div>
            </nav>
        </div>
    </div>
<!-- Page Sidebar Ends-->