@php 
    $logo = App\Models\BusinessSetting::where('key','front_logo')->value('value');
    if($logo){
        $logo = asset('public/storage/'.$logo);
    }else{
        $logo = asset('public/front/assets/img/paracare-logo.png');
    }
@endphp
<!-- TOP BAR -->
<div class="topbar">
    <div class="topbar-title">
        <i class="fas fa-landmark" style="color:#60a5fa"></i>
        State Health Command Centre
        <span class="module-chip">SUPER ADMIN</span>
    </div>
    <div class="topbar-filters">
        <select class="filter-select" id="fYear" onchange="refreshData()">
            <option>FY 2024-25</option>
            <option>FY 2023-24</option>
            <option>FY 2022-23</option>
        </select>
        <select class="filter-select" id="fMonth" onchange="refreshData()">
            <option value="all">All Months</option>
            <option>Apr</option>
            <option>May</option>
            <option>Jun</option>
            <option>Jul</option>
            <option>Aug</option>
            <option>Sep</option>
            <option>Oct</option>
            <option>Nov</option>
            <option>Dec</option>
            <option>Jan</option>
            <option>Feb</option>
            <option selected>Mar</option>
        </select>
        <select class="filter-select" id="fDistrict" onchange="refreshData()">
            <option value="all">All Districts</option>
            <option>Dehradun</option>
            <option>Haridwar</option>
            <option>Nainital</option>
            <option>Udham Singh Nagar</option>
            <option>Almora</option>
            <option>Chamoli</option>
            <option>Champawat</option>
            <option>Bageshwar</option>
            <option>Pithoragarh</option>
            <option>Rudraprayag</option>
            <option>Tehri</option>
            <option>Uttarkashi</option>
            <option>Pauri</option>
        </select>
        <select class="filter-select" id="fFacType" onchange="refreshData()">
            <option value="all">All Facility Types</option>
            <option>Medical College</option>
            <option>District Hospital</option>
            <option>CHC</option>
            <option>PHC</option>
            <option>Sub-Centre</option>
        </select>
        <div class="live-chip"><span></span>LIVE</div>
        <button class="tb-btn primary" onclick="exportReport()"><i class="fas fa-download"></i> Export</button>
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
