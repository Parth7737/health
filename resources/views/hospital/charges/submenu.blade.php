
<div class="card">
    <div class="card-body">
        <div class="nav flex-lg-column nav-pills nav-primary">
            @can('view-doctor-opd-charges')
                <a class="nav-link {{ request()->routeIs('hospital.charges.doctor-opd-charges.index') ? 'active' : '' }}" href="{{ route('hospital.charges.doctor-opd-charges.index') }}">Doctor OPD Charges</a>
            @endcan
            @can('view-charge-masters')
                 <a class="nav-link {{ request()->routeIs('hospital.charges.charge-masters.index') ? 'active' : '' }}" href="{{ route('hospital.charges.charge-masters.index') }}">Charge Masters</a>
            @endcan
        </div>
    </div>
</div>