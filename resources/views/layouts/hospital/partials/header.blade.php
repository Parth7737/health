@php
    $hospital = auth()->user()->hospital;
    $hospitalName = $hospital && $hospital->name ? $hospital->name : 'District Hospital Dehradun';
    $roleName = auth()->user()->getRoleNames()->first() ?? 'Administrator';
    $userName = auth()->user()->name ?? 'Admin User';
    $shortName = collect(explode(' ', trim($userName)))->take(2)->implode(' ');
    $initials = collect(explode(' ', trim($userName)))
        ->filter()
        ->take(2)
        ->map(fn ($name) => strtoupper(substr($name, 0, 1)))
        ->implode('');

    if ($initials === '') {
        $initials = 'AD';
    }
@endphp

<header class="hims-header" id="himsHeader">
    <div class="header-breadcrumb">
        <button class="hdr-icon-btn" id="govMenuToggle" type="button" title="Toggle menu" aria-label="Toggle sidebar">☰</button>
        <div class="breadcrumb-module">
            <span class="mod-icon">📊</span>
            <span>@yield('title', 'Executive Dashboard')</span>
        </div>
        <span class="breadcrumb-sep">›</span>
        <span class="breadcrumb-sub">{{ $hospitalName }}</span>
    </div>

    <div class="header-actions">
        <div class="header-search">
            <span class="search-icon">🔍</span>
            <input type="text" placeholder="Search patient, MRN, bill..." id="globalSearch" />
        </div>
        <button class="hdr-icon-btn" type="button" title="Notifications" onclick="if(typeof toggleNotifPanel==='function'){toggleNotifPanel();}">🔔<span class="notif-badge">5</span></button>
        <button class="hdr-icon-btn" type="button" title="Quick Add" onclick="if(typeof showQuickAdd==='function'){showQuickAdd();}">➕</button>
        <button class="hdr-icon-btn" type="button" title="Settings">⚙️</button>

        <button class="header-user" type="button" id="openProfileModalBtn">
            <div class="user-avatar">{{ $initials }}</div>
            <div>
                <div class="user-name">{{ $shortName }}</div>
                <div class="user-role">{{ $roleName }}</div>
            </div>
            <span style="font-size:10px;color:var(--text-muted)">▾</span>
        </button>
    </div>
</header>

<div class="hims-profile-overlay" id="profileModal" aria-hidden="true">
    <div class="hims-profile-modal">
        <div class="modal-header">
            <div class="modal-title">👤 User Profile</div>
            <button class="modal-close" type="button" id="closeProfileModalBtn">✕</button>
        </div>
        <div class="modal-body">
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;">
                <div style="width:50px;height:50px;border-radius:50%;background:linear-gradient(135deg,#1565c0,#42a5f5);display:flex;align-items:center;justify-content:center;font-size:24px;font-weight:700;color:#fff;">{{ $initials }}</div>
                <div>
                    <div style="font-weight:600;font-size:14px;">{{ $userName }}</div>
                    <div style="font-size:12px;color:var(--text-muted);">{{ $roleName }} — {{ $hospitalName }}</div>
                    <div style="font-size:11px;color:var(--text-muted);margin-top:2px;">🏥 {{ $hospitalName }}</div>
                    <div style="font-size:11px;color:var(--text-muted);">🕐 Shift: Morning (08:00-14:00)</div>
                </div>
            </div>
            <div style="display:flex;gap:8px;justify-content:flex-start;">
                <button class="btn btn-danger btn-sm" type="button" onclick="document.getElementById('logoutform').submit();" style="min-width:108px;font-weight:600;">🚪 Logout</button>
                <button class="btn btn-secondary btn-sm" type="button" id="profileCloseBtn" style="min-width:92px;font-weight:500;background:#eef3f9;border-color:#cdd9e8;color:#2a4a6d;">✕ Close</button>
            </div>
        </div>
    </div>
</div>

<form action="{{ route('logout') }}" id="logoutform" method="POST" class="d-none">
    @csrf
</form>

<script>
    function openProfileModal() {
        var modal = document.getElementById('profileModal');
        if (!modal) {
            return;
        }
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    }

    function closeProfileModal() {
        var modal = document.getElementById('profileModal');
        if (!modal) {
            return;
        }
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }

    function toggleProfileModal() {
        var modal = document.getElementById('profileModal');
        if (!modal) {
            return;
        }
        if (modal.classList.contains('is-open')) {
            closeProfileModal();
            return;
        }
        openProfileModal();
    }

    document.addEventListener('DOMContentLoaded', function () {
        var profileModal = document.getElementById('profileModal');
        var openBtn = document.getElementById('openProfileModalBtn');
        var closeBtn = document.getElementById('closeProfileModalBtn');
        var footerCloseBtn = document.getElementById('profileCloseBtn');

        if (openBtn) {
            openBtn.addEventListener('click', openProfileModal);
        }

        if (closeBtn) {
            closeBtn.addEventListener('click', closeProfileModal);
        }

        if (footerCloseBtn) {
            footerCloseBtn.addEventListener('click', closeProfileModal);
        }

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && profileModal && profileModal.classList.contains('is-open')) {
                closeProfileModal();
            }
        });

        if (profileModal) {
            profileModal.addEventListener('click', function (event) {
                if (event.target === profileModal) {
                    closeProfileModal();
                }
            });
        }
    });
</script>
