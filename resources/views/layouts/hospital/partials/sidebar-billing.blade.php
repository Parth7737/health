@php
    $roleName = auth()->user()->getRoleNames()->first() ?? 'Doctor';
    $userName = auth()->user()->name ?? 'Doctor User';
    $initials = collect(explode(' ', trim($userName)))
        ->filter()
        ->take(2)
        ->map(fn ($name) => strtoupper(substr($name, 0, 1)))
        ->implode('');
    if ($initials === '') {
        $initials = 'DR';
    }
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
        <div class="nav-section-title">Billing</div>
        <li class="sidebar-list">
            <a class="sidebar-link sidebar-title active" onclick="switchTab('dashboard')">
                <span class="nav-icon"><i class="fas fa-chart-pie"></i></span>
                <span class="nav-label">Dashboard</span>
            </a>
        </li>

        <li class="sidebar-list">
            <a class="sidebar-link sidebar-title" onclick="switchTab('invoice')">
                <span class="nav-icon"><i class="fas fa-plus"></i></span>
                <span class="nav-label"> New Bill / Invoice</span>
            </a>
        </li>

        <li class="sidebar-list">
            <a class="sidebar-link sidebar-title" onclick="switchTab('records')">
                <span class="nav-icon"><i class="fas fa-list"></i></span>
                <span class="nav-label">Bill Records <span class="nav-badge">8</span></span>
            </a>
        </li>

        <li class="sidebar-list">
            <a class="sidebar-link sidebar-title" onclick="switchTab('payment')">
                <span class="nav-icon"><i class="fas fa-money-bill-wave"></i></span>
                <span class="nav-label">Payments</span>
            </a>
        </li>

        <li class="sidebar-list">
            <a class="sidebar-link sidebar-title" onclick="switchTab('insurance')">
                <span class="nav-icon"><i class="fas fa-shield-alt"></i></span>
                <span class="nav-label">Insurance / TPA</span>
            </a>
        </li>

        <li class="sidebar-list">
            <a class="sidebar-link sidebar-title" onclick="switchTab('credit')">
                <span class="nav-icon"><i class="fas fa-university"></i></span>
                <span class="nav-label">Credit / Advances</span>
            </a>
        </li>

        <li class="sidebar-list">
            <a class="sidebar-link sidebar-title" onclick="switchTab('refunds')">
                <span class="nav-icon"><i class="fas fa-undo"></i></span>
                <span class="nav-label">Refunds</span>
            </a>
        </li>

        <li class="sidebar-list">
            <a class="sidebar-link sidebar-title" onclick="switchTab('revenue')">
                <span class="nav-icon"><i class="fas fa-chart-bar"></i></span>
                <span class="nav-label">Revenue Reports</span>
            </a>
        </li>

        <li class="sidebar-list">
            <a class="sidebar-link sidebar-title" onclick="switchTab('my-schedule')">
                <span class="nav-icon">📅</span>
                <span class="nav-label">My Schedule</span>
            </a>
        </li>

        <li class="sidebar-list">
            <a class="sidebar-link sidebar-title" onclick="switchTab('patient-bills')">
                <span class="nav-icon">💳</span>
                <span class="nav-label">Patient Bills</span>
            </a>
        </li>

        <li class="sidebar-list">
            <a class="sidebar-link sidebar-title" href="{{ route('hospital.dashboard') }}">
                <span class="nav-icon">🩸</span>
                <span class="nav-label">Blood Requests</span>
            </a>
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
