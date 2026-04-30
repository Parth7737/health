@php 
    $logo = App\Models\BusinessSetting::where('key','front_logo')->value('value');
    if($logo){
        $logo = asset('public/storage/'.$logo);
    }else{
        $logo = asset('public/front/assets/img/paracare-logo.png');
    }
@endphp 
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Paracare - Sakhuja Hospital">
    <meta name="keywords" content="Paracare - Sakhuja Hospital">
    <meta name="author" content="Paracare - Sakhuja Hospital">
    <!-- <link rel="icon" href="{{$logo}}" type="image/x-icon">
    <link rel="shortcut icon" href="{{$logo}}" type="image/x-icon"> -->
    <title>@yield('title') - Paracare</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="" />
    <x-route-js :routes="$routes ?? []" />
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{asset('public/front/assets/images/favicon.png')}}" />
    @include('layouts.hospital.partials.head')
</head>

<body>
    @php
        $sidebarVariant = trim($__env->yieldContent('sidebar_variant', 'auto'));
        $user = auth()->user();

        $isPrivilegedUser = $user && (
            $user->roles->count() > 1 ||
            $user->hasAnyRole(['Master Admin', 'Admin', 'Chairman']) ||
            $user->can('manage-roles') ||
            $user->can('view-hospital-data')
        );

        $useDoctorSidebar = false;
        if ($sidebarVariant === 'doctor') {
            $useDoctorSidebar = !$isPrivilegedUser;
        } elseif ($sidebarVariant === 'auto') {
            $useDoctorSidebar = $user && $user->hasRole('Doctor') && !$isPrivilegedUser;
        }
    @endphp
    @php
        $hidePageHeader = filter_var(trim($__env->yieldContent('hide_page_header', 'false')), FILTER_VALIDATE_BOOLEAN);
    @endphp
    <div class="hims-shell" id="pageWrapper">
        @if($useDoctorSidebar)
            @include('layouts.hospital.partials.sidebar-doctor')
        @else
            @include('layouts.hospital.partials.sidebar')
        @endif
        <div class="hims-sidebar-backdrop" id="govSidebarBackdrop"></div>

        <div class="hims-main">
            @include('layouts.hospital.partials.header')
            @if(!$hidePageHeader)
            <div class="gov-topbar" style="margin-left:0">
                <div class="govbar-left">
                    <div class="govbar-seal">
                        <svg viewBox="0 0 40 40" width="40" height="40">
                            <circle cx="20" cy="20" r="18" fill="none" stroke="#c8a84b" stroke-width="1.5"/>
                            <circle cx="20" cy="20" r="14" fill="#1a3a6b"/>
                            <text x="20" y="24" font-size="13" fill="#c8a84b" text-anchor="middle" font-weight="700">अ</text>
                        </svg>
                    </div>
                    <div class="govbar-text">
                        <div class="gt1">Government of Uttarakhand — Health &amp; Family Welfare</div>
                        <div class="gt2">{{ auth()->user()->hospital->name ?? 'District Hospital Dehradun' }} | ParaCare+ HIMS v3.0</div>
                        <div class="gt3">उत्तराखण्ड शासन · स्वास्थ्य एवं परिवार कल्याण विभाग</div>
                    </div>
                </div>
                <div class="govbar-right">
                    <div class="govbar-badge green">● System Online</div>
                    <div class="govbar-badge blue">📅 <span id="govTopbarDate"></span></div>
                    <div class="govbar-badge saffron">🔒 ABDM Synced</div>
                </div>
            </div>
            @endif

            <main class="hims-body {{ @$patient_360 ? 'patient-360-body' : '' }}">
            @if(!@$is_header_hiden && !$hidePageHeader)
                <div class="page-header">
                    <div>
                        <div class="page-title">@yield('title')</div>
                        <div class="page-subtitle">@yield('page_subtitle', 'State-wide health system performance — real-time aggregated view')</div>
                    </div>
                    <div class="page-actions">
                        @hasSection('page_header_actions')
                            @yield('page_header_actions')
                        @else
                            @if($is_dashbaord ?? false)
                                <button class="btn btn-secondary btn-sm" type="button">📍 All Districts</button>
                                <button class="btn btn-primary btn-sm" type="button">⬇ Export Report</button>
                            @endif
                        @endif
                    </div>
                </div>
            @endif
            <div class="{{ (@$is_header_hiden || $hidePageHeader) ? '' : 'content-area' }}">
                @yield('content')
            </div>
            </main>
        </div>

        <!-- Status Modal -->
        <div class="modal fade view_modal_data" id="view_modal_dataModel" tabindex="-1" aria-labelledby="view_modal_dataModelLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content" id="ajax_view_modal"></div>
            </div>
        </div>

        <!-- Add Modal -->
        <div class="modal fade add-datamodal" id="add-datamodal" tabindex="-1" aria-labelledby="add-dataModalLabel" aria-hidden="true" data-bs-focus="false">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content" id="ajaxdata"></div>
            </div>
        </div>
        </div>

        <script>
            (function () {
                function updateGovDate() {
                    var el = document.getElementById('govTopbarDate');
                    if (!el) {
                        return;
                    }
                    el.textContent = new Date().toLocaleDateString('en-IN', {
                        weekday: 'long',
                        day: '2-digit',
                        month: 'long',
                        year: 'numeric'
                    });
                }

                updateGovDate();
                setInterval(updateGovDate, 60000);
            })();
        </script>
    <div class="full-page-loader">
      <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Loading...</span>
      </div>
    </div>
    @include('layouts.hospital.partials.scripts')
</body>
</html>
