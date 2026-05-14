@php
    $ht = trim((string) ($user->hospital_type ?? ''));
@endphp

<div class="eo-panel-title"><i class="fas fa-user-circle" style="color:#81c784"></i> Basic information</div>
<p class="eo-panel-sub">Profile linked to this empanelment request. Facility category is taken from your registration and drives the checklist in later steps.</p>

<div class="eo-card">
    <div class="eo-card-hdr">
        <h3 class="eo-card-title"><i class="fas fa-building"></i> Facility type (from registration)</h3>
    </div>
    <div class="eo-card-body">
        <div class="eo-type-grid">
            <div class="eo-type-card is-selected">
                <div class="tc-icon" style="background:linear-gradient(135deg,#1565c0,#42a5f5)"><i class="fas fa-hospital"></i></div>
                <div class="tc-name">{{ $ht !== '' ? $ht : 'Facility' }}</div>
                <div class="tc-sub">This category was chosen at sign-up and cannot be changed here.</div>
            </div>
        </div>
        <div class="mt-3 p-3 rounded"
            style="background:rgba(21,101,192,.08);border:1px solid rgba(21,101,192,.2);font-size:12px;color:var(--eo-muted2)">
            <i class="fas fa-info-circle" style="color:#60a5fa;margin-right:6px"></i>
            Selected type: <strong style="color:#60a5fa">{{ $ht !== '' ? $ht : 'Not set' }}</strong>. Continue to enter official hospital details, infrastructure and documents in the following steps.
        </div>
    </div>
</div>

<div class="eo-card">
    <div class="eo-card-hdr">
        <h3 class="eo-card-title"><i class="fas fa-id-badge"></i> Account summary</h3>
    </div>
    <div class="eo-card-body">
        <div class="eo-readonly-grid">
            <div>
                <div class="eo-k">Name</div>
                <div class="eo-v">{{ @$user->name }}</div>
            </div>
            <div>
                <div class="eo-k">Gender</div>
                <div class="eo-v">{{ @$user->gender }}</div>
            </div>
            <div>
                <div class="eo-k">State</div>
                <div class="eo-v">{{ @$user->state }}</div>
            </div>
            <div>
                <div class="eo-k">Email</div>
                <div class="eo-v">{{ @$user->email }}</div>
            </div>
            <div>
                <div class="eo-k">Mobile number</div>
                <div class="eo-v">{{ @$user->mobile_no }}</div>
            </div>
        </div>
    </div>
</div>

<div class="eo-step-nav">
    <a href="{{ \App\CentralLogics\Helpers::getDashboardRedirect(auth()->user()) }}" class="eo-tb-btn"><i
            class="fas fa-arrow-left"></i> Dashboard</a>
    <span class="eo-nav-info">Review your profile, then continue.</span>
    <button type="button" class="eo-tb-btn primary" onclick="loadStep(2)">Next: Hospital information <i
            class="fas fa-arrow-right"></i></button>
</div>
