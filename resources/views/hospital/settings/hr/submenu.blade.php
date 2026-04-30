
<div class="card">
    <div class="card-body">
        <div class="nav flex-lg-column nav-pills nav-primary">
            @can('view-hr-department')
                <a class="nav-link {{ request()->routeIs('hospital.settings.hr.department.index') ? 'active' : '' }}" href="{{ route('hospital.settings.hr.department.index') }}">Departments</a>
            @endcan
            @can('view-hr-department-unit')
                <a class="nav-link {{ request()->routeIs('hospital.settings.hr.department-unit.index') ? 'active' : '' }}" href="{{ route('hospital.settings.hr.department-unit.index') }}">Department Units</a>
            @endcan
            @can('view-hr-designation')
                <a class="nav-link {{ request()->routeIs('hospital.settings.hr.designation.index') ? 'active' : '' }}" href="{{ route('hospital.settings.hr.designation.index') }}">Designations</a>
            @endcan
             @can('view-hr-specialist')
                <a class="nav-link {{ request()->routeIs('hospital.settings.hr.specialist.index') ? 'active' : '' }}" href="{{ route('hospital.settings.hr.specialist.index') }}">Specialists</a>
            @endcan
            @can('view-hr-leave-type')
                <a class="nav-link {{ request()->routeIs('hospital.settings.hr.leave-type.index') ? 'active' : '' }}" href="{{ route('hospital.settings.hr.leave-type.index') }}">Leave Types</a>
            @endcan
        </div>
    </div>
</div>