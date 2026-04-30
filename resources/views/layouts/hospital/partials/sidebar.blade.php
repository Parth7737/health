@php
    $roleName = auth()->user()->getRoleNames()->first() ?? 'Administrator';
    $userName = auth()->user()->name ?? 'Admin User';
    $initials = collect(explode(' ', trim($userName)))
        ->filter()
        ->take(2)
        ->map(fn ($name) => strtoupper(substr($name, 0, 1)))
        ->implode('');
    if ($initials === '') $initials = 'AD';
@endphp

<nav class="gov-sidebar hims-sidebar">
    <div class="sidebar-brand">
        <div class="brand-logo">🏥</div>
        <div class="brand-text">
            <div class="brand-name">ParaCare+ HIMS</div>
            <div class="brand-sub">Govt. Uttarakhand · DHD-01</div>
        </div>
    </div>

    <div class="sidebar-role-badge">
        <div class="role-avatar">{{ $initials }}</div>
        <div class="role-info">
            <div class="role-name">{{ $userName }}</div>
            <div class="role-type">{{ $roleName }}</div>
        </div>
        <div class="role-dot"></div>
    </div>

    <ul class="gov-sidebar-links sidebar-nav" id="hospitalGovSidebarMenu">
        <li class="sidebar-list">
            <a class="sidebar-link sidebar-title {{ request()->routeIs('hospital.dashboard') || request()->routeIs('hospital.doctor-dashboard') ? 'active' : '' }}" href="{{ route('hospital.dashboard') }}">
                <span class="nav-icon">🏠</span>
                <span class="nav-label">Executive Dashboard</span>
            </a>
        </li>

        @can('view-appointments')
        <!-- <li class="sidebar-list">
            <a class="sidebar-link sidebar-title {{ request()->routeIs('hospital.front-office.*') ? 'active' : '' }}" href="{{ route('hospital.front-office.index') }}">
                <span class="nav-icon">🏢</span>
                <span class="nav-label">Front Office</span>
            </a>
        </li> -->
        @endcan
        @if(auth()->user()->can('view-patient-management') || auth()->user()->can('view-patient-management'))
        <li class="sidebar-list">
            <a class="sidebar-link sidebar-title {{ request()->routeIs('hospital.patient-management.*') ? 'active' : '' }}" href="{{ route('hospital.patient-management.index') }}">
                <span class="nav-icon">👤</span>
                <span class="nav-label">Patient Registration</span>
            </a>
        </li>
        @endif
        @can('view-patient-management')
        <li class="sidebar-list">
            <a class="sidebar-link sidebar-title {{ request()->routeIs('hospital.patient-management.*') ? 'active' : '' }}" href="{{ route('hospital.patient-management.index') }}">
                <span class="nav-icon">🩺</span>
                <span class="nav-label">OPD Management</span>
            </a>
        </li>
        @endcan

        @can('view-patient-management')
        <li class="sidebar-list">
            <a class="sidebar-link sidebar-title {{ request()->routeIs('hospital.patient-management.*') ? 'active' : '' }}" href="{{ route('hospital.patient-management.index') }}">
                <span class="nav-icon">🛏️</span>
                <span class="nav-label">IPD / ADT</span>
            </a>
        </li>
        @endcan

        @can('view-billing-and-finance')
            <li class="sidebar-list">
                <a class="sidebar-link sidebar-title" href="{{ route('hospital.billing.index') }}">
                    <span class="nav-icon">💳</span>
                    <span class="nav-label">Billing & Finance</span>
                </a>
            </li>
        @endcan

        <li class="sidebar-list">
            <a class="sidebar-link sidebar-title" href="{{ route('hospital.dashboard') }}">
                <span class="nav-icon">📄</span>
                <span class="nav-label">Patient Claims</span>
            </a>
        </li>

        <li class="sidebar-list">
            <a class="sidebar-link sidebar-title {{ request()->is('hospital/pharmacy/*') ? 'active' : '' }}" href="{{ route('hospital.pharmacy.sale.index') }}">
                <span class="nav-icon">💊</span>
                <span class="nav-label">Pharmacy</span>
            </a>
        </li>

        @can('view-pathology-report')
        <!-- route('hospital.pathology.worklist.index') -->
        <li class="sidebar-list">
            <a class="sidebar-link sidebar-title {{ request()->routeIs('hospital.lab.*') ? 'active' : '' }}" href="{{ route('hospital.lab') }}">
                <span class="nav-icon">🧪</span>
                <span class="nav-label">Lab / Pathology</span>
            </a>
        </li>
        @endcan

        @can('view-radiology-report')
        <li class="sidebar-list">
            <a class="sidebar-link sidebar-title {{ request()->routeIs('hospital.radiology.worklist.*') ? 'active' : '' }}" href="{{ route('hospital.radiology.worklist.index') }}">
                <span class="nav-icon">🩻</span>
                <span class="nav-label">Radiology / RIS</span>
            </a>
        </li>
        @endcan

        <li class="sidebar-list"><a class="sidebar-link sidebar-title" href="{{ route('hospital.dashboard') }}"><span class="nav-icon">🔬</span><span class="nav-label">Operation Theatre</span></a></li>
        <li class="sidebar-list"><a class="sidebar-link sidebar-title" href="{{ route('hospital.dashboard') }}"><span class="nav-icon">🩸</span><span class="nav-label">Blood Bank</span></a></li>
        <li class="sidebar-list"><a class="sidebar-link sidebar-title" href="{{ route('hospital.dashboard') }}"><span class="nav-icon">💉</span><span class="nav-label">Vaccine</span></a></li>

        <li class="sidebar-list has-submenu">
            <a class="sidebar-link sidebar-title" href="#">
                <span class="nav-icon">🎥</span>
                <span class="nav-label">Live Consultation</span>
            </a>
            <ul class="sidebar-submenu">
                <li><a href="javascript:;">Live Consultation</a></li>
                <li><a href="javascript:;">Live Meeting</a></li>
            </ul>
        </li>

        @can('view-tpa')
        <li class="sidebar-list">
            <a class="sidebar-link sidebar-title {{ request()->is('hospital/tpa-management/*') ? 'active' : '' }}" href="{{ route('hospital.tpa-management.tpas.index') }}">
                <span class="nav-icon">🛡️</span>
                <span>TPA Management</span>
            </a>
        </li>
        @endcan

        <li class="sidebar-list has-submenu">
            <a class="sidebar-link sidebar-title" href="#"><span class="nav-icon">💰</span><span class="nav-label">Finance</span></a>
            <ul class="sidebar-submenu">
                <li><a href="javascript:;">Income</a></li>
                <li><a href="javascript:;">Expenses</a></li>
                <li><a href="javascript:;">Finance Summary</a></li>
            </ul>
        </li>

        <li class="sidebar-list"><a class="sidebar-link sidebar-title" href="{{ route('hospital.dashboard') }}"><span class="nav-icon">🚑</span><span>Ambulance</span></a></li>
        <li class="sidebar-list"><a class="sidebar-link sidebar-title" href="{{ route('hospital.dashboard') }}"><span class="nav-icon">📝</span><span>Birth &amp; Death Record</span></a></li>
        <li class="sidebar-list"><a class="sidebar-link sidebar-title {{ request()->is('hospital/hr/*') ? 'active' : '' }}" href="{{ route('hospital.hr.staff.index') }}"><span class="nav-icon">👨‍⚕️</span><span>Human Resources</span></a></li>
        <li class="sidebar-list"><a class="sidebar-link sidebar-title" href="{{ route('hospital.dashboard') }}"><span class="nav-icon">💬</span><span>Messaging</span></a></li>
        <li class="sidebar-list"><a class="sidebar-link sidebar-title" href="{{ route('hospital.dashboard') }}"><span class="nav-icon">⬇️</span><span>Download Center</span></a></li>
        <li class="sidebar-list"><a class="sidebar-link sidebar-title" href="{{ route('hospital.dashboard') }}"><span class="nav-icon">📦</span><span>Inventory</span></a></li>
        <li class="sidebar-list"><a class="sidebar-link sidebar-title" href="{{ route('hospital.dashboard') }}"><span class="nav-icon">🖥️</span><span>Front CMS</span></a></li>
        <li class="sidebar-list"><a class="sidebar-link sidebar-title" href="{{ route('hospital.dashboard') }}"><span class="nav-icon">👤</span><span>Patient</span></a></li>

        <li class="sidebar-list has-submenu">
            <a class="sidebar-link sidebar-title" href="#"><span class="nav-icon">📊</span><span class="nav-label">Reports</span></a>
            <ul class="sidebar-submenu">
                <li><a href="javascript:;">Transaction Report</a></li>
                <li><a href="javascript:;">Appointment Report</a></li>
                <li><a href="javascript:;">OPD Report</a></li>
                <li><a href="javascript:;">IPD Report</a></li>
                <li><a href="javascript:;">OPD Balance Report</a></li>
                <li><a href="javascript:;">IPD Balance Report</a></li>
                <li><a href="javascript:;">Discharge Patient</a></li>
                <li><a href="javascript:;">Pharmacy Bill Report</a></li>
                <li><a href="javascript:;">Expiry Medicine Report</a></li>
                <li><a href="javascript:;">Pathology Patient Report</a></li>
                <li><a href="javascript:;">Radiology Patient Report</a></li>
                <li><a href="javascript:;">OT Report</a></li>
                <li><a href="javascript:;">Blood Issue Report</a></li>
                <li><a href="javascript:;">Blood Donor Report</a></li>
                <li><a href="javascript:;">Live Consultantion Report</a></li>
                <li><a href="javascript:;">Live Meeting Report</a></li>
                <li><a href="javascript:;">TPA Report</a></li>
                <li><a href="javascript:;">Income Report</a></li>
                <li><a href="javascript:;">Income Group Report</a></li>
                <li><a href="javascript:;">Expense Report</a></li>
                <li><a href="javascript:;">Expense Group Report</a></li>
                <li><a href="javascript:;">Ambulance Report</a></li>
                <li><a href="javascript:;">Birth Report</a></li>
                <li><a href="javascript:;">Death Report</a></li>
                <li><a href="javascript:;">Staff Attendance Report</a></li>
                <li><a href="javascript:;">Selfie Attendance Report</a></li>
                <li><a href="javascript:;">User Log</a></li>
                <li><a href="javascript:;">Patient Login Credential</a></li>
                <li><a href="javascript:;">Email/SMS Log</a></li>
                <li><a href="javascript:;">Inventory Stock Report</a></li>
                <li><a href="javascript:;">Inventory Item Report</a></li>
                <li><a href="javascript:;">Inventory Issue Report</a></li>
            </ul>
        </li>

        <li class="sidebar-list has-submenu">
            <a class="sidebar-link sidebar-title" href="#"><span class="nav-icon">⚙️</span><span class="nav-label">Master Setup</span></a>
            <ul class="sidebar-submenu">
                @can('view-hospital-data')
                <li><a class="{{ request()->is('hospital/settings/general-setting*') ? 'active' : '' }}" href="{{ route('hospital.settings.general-setting.index') }}">Hospital Data</a></li>
                @endcan

                @if(auth()->user()->can('view-patient-category') || auth()->user()->can('view-religion') || auth()->user()->can('view-dietary') || auth()->user()->can('view-allergy') || auth()->user()->can('view-habits') || auth()->user()->can('view-diseases') || auth()->user()->can('view-disease-types') || auth()->user()->can('view-symptoms') || auth()->user()->can('view-symptoms-type'))
                    @php $masterUrl = '';
                        if(auth()->user()->can('view-patient-category')){
                            $masterUrl = route('hospital.masters.patient-category.index');
                        }elseif(auth()->user()->can('view-religion')){
                            $masterUrl = route('hospital.masters.religion.index');
                        }elseif(auth()->user()->can('view-dietary')){
                            $masterUrl = route('hospital.masters.dietary.index');
                        }elseif(auth()->user()->can('view-allergy')){
                            $masterUrl = route('hospital.masters.allergy.index');
                        }elseif(auth()->user()->can('view-habits')){
                            $masterUrl = route('hospital.masters.habits.index');
                        }elseif(auth()->user()->can('view-diseases')){
                            $masterUrl = route('hospital.masters.diseases.index');
                        }elseif(auth()->user()->can('view-disease-types')){
                            $masterUrl = route('hospital.masters.disease-type.index');
                        }elseif(auth()->user()->can('view-symptoms')){
                            $masterUrl = route('hospital.masters.symptoms.symptoms-head.index');
                        }elseif(auth()->user()->can('view-symptoms-type')){
                            $masterUrl = route('hospital.masters.symptoms.type.index');
                        }
                    @endphp
                    <li><a class="{{ request()->is('hospital/masters/*') ? 'active' : '' }}" href="{{ $masterUrl }}">Masters</a></li>
                @endif

                @if(auth()->user()->can('view-doctor-opd-charges') || auth()->user()->can('view-charge-masters'))
                    @php $chargesUrl = '';
                        if(auth()->user()->can('view-doctor-opd-charges')){
                            $chargesUrl = route('hospital.charges.doctor-opd-charges.index');
                        }elseif(auth()->user()->can('view-charge-masters')){
                            $chargesUrl = route('hospital.charges.charge-masters.index');
                        }
                    @endphp
                    <li><a class="{{ request()->is('hospital/charges/*') ? 'active' : '' }}" href="{{ $chargesUrl }}">Hospital Charges</a></li>
                @endif

                @if(auth()->user()->can('view-building') || auth()->user()->can('view-floor') || auth()->user()->can('view-ward') || auth()->user()->can('view-room') || auth()->user()->can('view-bed-type') || auth()->user()->can('view-bed'))
                    @php $bedsUrl = '';
                        if(auth()->user()->can('view-building')){
                            $bedsUrl = route('hospital.settings.beds.building.index');
                        }elseif(auth()->user()->can('view-floor')){
                            $bedsUrl = route('hospital.settings.beds.floor.index');
                        }elseif(auth()->user()->can('view-ward')){
                            $bedsUrl = route('hospital.settings.beds.ward.index');
                        }elseif(auth()->user()->can('view-room')){
                            $bedsUrl = route('hospital.settings.beds.room.index');
                        }elseif(auth()->user()->can('view-bed-type')){
                            $bedsUrl = route('hospital.settings.beds.bed-type.index');
                        }elseif(auth()->user()->can('view-bed')){
                            $bedsUrl = route('hospital.settings.beds.bed.index');
                        }
                    @endphp
                    <li><a class="{{ request()->is('hospital/settings/beds/*') ? 'active' : '' }}" href="{{ $bedsUrl }}">Beds Management</a></li>
                @endif

                @can('view-header-footer')
                <li><a class="{{ request()->is('hospital/settings/header-footer*') ? 'active' : '' }}" href="{{ route('hospital.settings.header-footer.index') }}">Print Header Footer</a></li>
                @endcan

                @if(auth()->user()->can('view-visitor-purposes') || auth()->user()->can('view-complain-types') || auth()->user()->can('view-complain-sources') || auth()->user()->can('view-appointment-priorities'))
                    @php $frontOfficeUrl = '';
                        if(auth()->user()->can('view-visitor-purposes')){
                            $frontOfficeUrl = route('hospital.settings.front-office.visitor-purposes.index');
                        }elseif(auth()->user()->can('view-complain-types')){
                            $frontOfficeUrl = route('hospital.settings.front-office.complain-types.index');
                        }elseif(auth()->user()->can('view-complain-sources')){
                            $frontOfficeUrl = route('hospital.settings.front-office.complain-sources.index');
                        }elseif(auth()->user()->can('view-appointment-priorities')){
                            $frontOfficeUrl = route('hospital.settings.front-office.appointment-priorities.index');
                        }
                    @endphp
                    <li><a class="{{ request()->is('hospital/settings/front-office/*') ? 'active' : '' }}" href="{{ $frontOfficeUrl }}">Front Office</a></li>
                @endif

                @if(auth()->user()->can('view-medicine') || auth()->user()->can('view-medicine-category') || auth()->user()->can('view-medicine-dosage') || auth()->user()->can('view-medicine-instructions') || auth()->user()->can('view-frequency'))
                    @php $pharmacyUrl = '';
                        if(auth()->user()->can('view-medicine')){
                            $pharmacyUrl = route('hospital.settings.pharmacy.medicine.index');
                        }elseif(auth()->user()->can('view-medicine-category')){
                            $pharmacyUrl = route('hospital.settings.pharmacy.medicine-category.index');
                        }elseif(auth()->user()->can('view-medicine-dosage')){
                            $pharmacyUrl = route('hospital.settings.pharmacy.medicine-dosage.index');
                        }elseif(auth()->user()->can('view-medicine-instructions')){
                            $pharmacyUrl = route('hospital.settings.pharmacy.medicine-instructions.index');
                        }elseif(auth()->user()->can('view-frequency')){
                            $pharmacyUrl = route('hospital.settings.pharmacy.frequency.index');
                        }
                    @endphp
                    <li><a class="{{ request()->is('hospital/settings/pharmacy/*') ? 'active' : '' }}" href="{{ $pharmacyUrl }}">Pharmacy</a></li>
                @endif

                @if(auth()->user()->can('view-pathology-test') || auth()->user()->can('view-pathology-category') || auth()->user()->can('view-pathology-unit') || auth()->user()->can('view-pathology-parameter') || auth()->user()->can('view-pathology-age-group'))
                    @php $pathologyUrl = '';
                        if(auth()->user()->can('view-pathology-test')){
                            $pathologyUrl = route('hospital.settings.pathology.test.index');
                        }elseif(auth()->user()->can('view-pathology-category')){
                            $pathologyUrl = route('hospital.settings.pathology.category.index');
                        }elseif(auth()->user()->can('view-pathology-unit')){
                            $pathologyUrl = route('hospital.settings.pathology.unit.index');
                        }elseif(auth()->user()->can('view-pathology-parameter')){
                            $pathologyUrl = route('hospital.settings.pathology.parameter.index');
                        }elseif(auth()->user()->can('view-pathology-age-group')){
                            $pathologyUrl = route('hospital.settings.pathology.age-group.index');
                        }
                    @endphp
                    <li><a class="{{ request()->is('hospital/settings/pathology/*') ? 'active' : '' }}" href="{{ $pathologyUrl }}">Pathology</a></li>
                @endif

                @if(auth()->user()->can('view-radiology-test') || auth()->user()->can('view-radiology-category') || auth()->user()->can('view-radiology-unit') || auth()->user()->can('view-radiology-parameter'))
                    @php $radiologyUrl = '';
                        if(auth()->user()->can('view-radiology-test')){
                            $radiologyUrl = route('hospital.settings.radiology.test.index');
                        }elseif(auth()->user()->can('view-radiology-category')){
                            $radiologyUrl = route('hospital.settings.radiology.category.index');
                        }elseif(auth()->user()->can('view-radiology-unit')){
                            $radiologyUrl = route('hospital.settings.radiology.unit.index');
                        }elseif(auth()->user()->can('view-radiology-parameter')){
                            $radiologyUrl = route('hospital.settings.radiology.parameter.index');
                        }
                    @endphp
                    <li><a class="{{ request()->is('hospital/settings/radiology/*') ? 'active' : '' }}" href="{{ $radiologyUrl }}">Radiology</a></li>
                @endif

                @if(auth()->user()->can('view-hr-department') || auth()->user()->can('view-hr-department-unit') || auth()->user()->can('view-hr-designation') || auth()->user()->can('view-hr-specialist') || auth()->user()->can('view-hr-leave-type'))
                    @php $hrUrl = '';
                        if(auth()->user()->can('view-hr-department')){
                            $hrUrl = route('hospital.settings.hr.department.index');
                        }elseif(auth()->user()->can('view-hr-department-unit')){
                            $hrUrl = route('hospital.settings.hr.department-unit.index');
                        }elseif(auth()->user()->can('view-hr-designation')){
                            $hrUrl = route('hospital.settings.hr.designation.index');
                        }elseif(auth()->user()->can('view-hr-specialist')){
                            $hrUrl = route('hospital.settings.hr.specialist.index');
                        }elseif(auth()->user()->can('view-hr-leave-type')){
                            $hrUrl = route('hospital.settings.hr.leave-type.index');
                        }
                    @endphp
                    <li><a class="{{ request()->is('hospital/hr/*') ? 'active' : '' }}" href="{{ $hrUrl }}">Human Resource</a></li>
                @endif

                @can('manage-roles')
                <li><a class="{{ request()->is('hospital/settings/role-management*') ? 'active' : '' }}" href="{{ route('hospital.settings.role-management.index') }}">Role &amp; Access (RBAC)</a></li>
                @endcan
            </ul>
        </li>
    </ul>

    <div class="sidebar-footer">
        <div class="sidebar-footer-links">
            <a href="javascript:;" class="sf-btn" onclick="document.getElementById('logoutform').submit();">🚪 Logout</a>
            <a href="javascript:;" class="sf-btn" onclick="window.dispatchEvent(new CustomEvent('hims:help-open'));">❓ Help</a>
        </div>
    </div>
</nav>

<style>
    .gov-sidebar .gov-sidebar-links {
        margin: 0;
        padding: 6px 0 16px;
    }

    .gov-sidebar .sidebar-list {
        list-style: none;
        margin: 0;
    }

    .gov-sidebar .sidebar-link {
        display: flex;
        align-items: center;
        gap: 9px;
        padding: 7px 14px 7px 18px;
        margin: 1px 8px;
        border-radius: var(--r-sm);
        font-size: 12.5px;
        font-weight: 500;
        color: var(--sidebar-text);
        text-decoration: none;
        border: 1px solid transparent;
        transition: all var(--t-fast);
        position: relative;
    }

    .gov-sidebar .sidebar-link:hover {
        background: var(--sidebar-hover);
        color: var(--sidebar-text-active);
        text-decoration: none;
    }

    .gov-sidebar .sidebar-link.active,
    .gov-sidebar .sidebar-submenu a.active {
        background: var(--sidebar-active);
        border-color: rgba(21,101,192,.3);
        color: var(--sidebar-text-active);
        font-weight: 600;
    }

    .gov-sidebar .sidebar-link.active::before {
        content: '';
        position: absolute;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        width: 3px;
        height: 70%;
        background: var(--sidebar-active-border);
        border-radius: 0 3px 3px 0;
    }

    .gov-sidebar .has-submenu > .sidebar-title::after {
        content: '▾';
        color: var(--sidebar-muted);
        font-size: 10px;
        transition: transform var(--t-fast);
        margin-left: auto;
    }

    .gov-sidebar .nav-icon {
        width: 18px;
        text-align: center;
        flex-shrink: 0;
        font-size: 13px;
        opacity: .95;
    }

    .gov-sidebar .has-submenu.open > .sidebar-title::after {
        transform: rotate(180deg);
    }

    .gov-sidebar .sidebar-submenu {
        display: none;
        margin: 3px 0 6px;
        padding: 0 8px 0 18px;
    }

    .gov-sidebar .has-submenu.open > .sidebar-submenu {
        display: block;
    }

    .gov-sidebar .sidebar-submenu li {
        list-style: none;
        margin: 1px 0;
    }

    .gov-sidebar .sidebar-submenu a {
        display: block;
        padding: 7px 10px;
        border-radius: 7px;
        color: var(--sidebar-text);
        font-size: 12px;
        text-decoration: none;
        border: 1px solid transparent;
        transition: all var(--t-fast);
    }

    .gov-sidebar .sidebar-submenu a:hover {
        background: var(--sidebar-hover);
        color: var(--sidebar-text-active);
    }

    .gov-sidebar .gov-sidebar-links {
        flex: 1;
        overflow-y: auto;
        overflow-x: hidden;
        padding-bottom: 10px;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const menu = document.getElementById('hospitalGovSidebarMenu');
        const shell = document.getElementById('pageWrapper');
        const sidebar = document.querySelector('.gov-sidebar');
        const toggleBtn = document.getElementById('govMenuToggle');
        const backdrop = document.getElementById('govSidebarBackdrop');

        function isMobileView() {
            return window.matchMedia('(max-width: 768px)').matches;
        }

        function closeSidebar() {
            if (!sidebar) {
                return;
            }

            sidebar.classList.remove('open');
            document.body.classList.remove('sidebar-open');

            if (shell) {
                shell.classList.remove('sidebar-open');
            }

            if (toggleBtn) {
                toggleBtn.setAttribute('aria-expanded', 'false');
            }
        }

        function openSidebar() {
            if (!sidebar) {
                return;
            }

            sidebar.classList.add('open');
            document.body.classList.add('sidebar-open');

            if (shell) {
                shell.classList.add('sidebar-open');
            }

            if (toggleBtn) {
                toggleBtn.setAttribute('aria-expanded', 'true');
            }
        }

        if (toggleBtn && sidebar) {
            toggleBtn.addEventListener('click', function () {
                if (sidebar.classList.contains('open')) {
                    closeSidebar();
                } else {
                    openSidebar();
                }
            });
        }

        if (backdrop) {
            backdrop.addEventListener('click', closeSidebar);
        }

        window.addEventListener('resize', function () {
            if (!isMobileView()) {
                closeSidebar();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeSidebar();
            }
        });

        if (!menu) {
            return;
        }

        const submenuParents = menu.querySelectorAll('.has-submenu');

        submenuParents.forEach(function (parent) {
            const trigger = parent.querySelector(':scope > .sidebar-title');
            const submenu = parent.querySelector(':scope > .sidebar-submenu');
            if (!trigger || !submenu) {
                return;
            }

            if (submenu.querySelector('a.active')) {
                parent.classList.add('open');
            }

            trigger.addEventListener('click', function (event) {
                event.preventDefault();
                parent.classList.toggle('open');
            });
        });

        const menuLinks = menu.querySelectorAll('a');
        menuLinks.forEach(function (link) {
            link.addEventListener('click', function () {
                if (!isMobileView()) {
                    return;
                }

                if (link.closest('.has-submenu') && link.classList.contains('sidebar-title')) {
                    return;
                }

                closeSidebar();
            });
        });
    });
</script>
