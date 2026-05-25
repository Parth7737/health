@php 
    $logo = App\Models\BusinessSetting::where('key','front_logo')->value('value');
    if($logo){
        $logo = asset('public/storage/'.$logo);
    }else{
        $logo = asset('public/front/assets/img/paracare-logo.png');
    }
@endphp
<div class="topbar">
    <div class="topbar-title">
        <i class="fas fa-plus-circle" style="color:#81c784"></i>
        Facility Onboarding
        <span class="module-chip">STATE ADMIN</span>
    </div>
    <button class="tb-btn" onclick="switchView('tracker')"><i class="fas fa-tasks"></i> Track
        Applications</button>
    <button class="tb-btn primary" onclick="switchView('wizard')"><i class="fas fa-plus"></i> New
        Facility</button>
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
