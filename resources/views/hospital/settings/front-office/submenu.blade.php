
<div class="card">
    <div class="card-body">
        <div class="nav flex-lg-column nav-pills nav-primary">
            @can('view-visitor-purposes')
                <a class="nav-link {{ request()->routeIs('hospital.settings.front-office.visitor-purposes.index') ? 'active' : '' }}" href="{{ route('hospital.settings.front-office.visitor-purposes.index') }}">Visitor Purposes</a>
            @endcan
            @can('view-complain-types')
                <a class="nav-link {{ request()->routeIs('hospital.settings.front-office.complain-types.index') ? 'active' : '' }}" href="{{ route('hospital.settings.front-office.complain-types.index') }}">Complain Types</a>
            @endcan
            @can('view-complain-sources')
                <a class="nav-link {{ request()->routeIs('hospital.settings.front-office.complain-sources.index') ? 'active' : '' }}" href="{{ route('hospital.settings.front-office.complain-sources.index') }}">Complain Sources</a>
            @endcan
            @can('view-appointment-priorities')
                <a class="nav-link {{ request()->routeIs('hospital.settings.front-office.appointment-priorities.index') ? 'active' : '' }}" href="{{ route('hospital.settings.front-office.appointment-priorities.index') }}">Appointment Priority</a>
            @endcan
        </div>
    </div>
</div>