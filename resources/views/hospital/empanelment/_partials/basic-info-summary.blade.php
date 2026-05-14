<div class="eo-card mb-3">
    <div class="eo-card-hdr">
        <h3 class="eo-card-title"><i class="fas fa-id-badge"></i> Account holder (read-only)</h3>
    </div>
    <div class="eo-card-body">
        <div class="eo-readonly-grid">
            <div>
                <div class="eo-k">Name</div>
                <div class="eo-v">{{ $user->name }}</div>
            </div>
            <div>
                <div class="eo-k">Email</div>
                <div class="eo-v">{{ $user->email }}</div>
            </div>
            <div>
                <div class="eo-k">Mobile</div>
                <div class="eo-v">{{ $user->mobile_no }}</div>
            </div>
            <div>
                <div class="eo-k">State</div>
                <div class="eo-v">{{ $user->state }}</div>
            </div>
        </div>
    </div>
</div>
