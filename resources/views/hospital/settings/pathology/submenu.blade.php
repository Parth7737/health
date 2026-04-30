
<div class="card">
    <div class="card-body">
        <div class="nav flex-lg-column nav-pills nav-primary">
            @can('view-pathology-test')
                <a class="nav-link {{ request()->routeIs('hospital.settings.pathology.test.*') ? 'active' : '' }}" href="{{ route('hospital.settings.pathology.test.index') }}">Pathology Test</a>
            @endcan
            @can('view-pathology-category')
            <a class="nav-link {{ request()->routeIs('hospital.settings.pathology.category.index') ? 'active' : '' }}" href="{{ route('hospital.settings.pathology.category.index') }}">Pathology Category</a>
            @endcan
            @can('view-pathology-unit')
                <a class="nav-link {{ request()->routeIs('hospital.settings.pathology.unit.index') ? 'active' : '' }}" href="{{ route('hospital.settings.pathology.unit.index') }}">Pathology Unit</a>
            @endcan
            @can('view-pathology-parameter')
                <a class="nav-link {{ request()->routeIs('hospital.settings.pathology.parameter.index') ? 'active' : '' }}" href="{{ route('hospital.settings.pathology.parameter.index') }}">Pathology Parameter</a>
            @endcan
            @can('view-pathology-status')
                <!-- <a class="nav-link {{ request()->routeIs('hospital.settings.pathology.status.index') ? 'active' : '' }}" href="{{ route('hospital.settings.pathology.status.index') }}">Pathology Status</a> -->
            @endcan
            @can('view-pathology-age-group')
                <a class="nav-link {{ request()->routeIs('hospital.settings.pathology.age-group.index') ? 'active' : '' }}" href="{{ route('hospital.settings.pathology.age-group.index') }}">Pathology Age Group</a>
            @endcan
        </div>
    </div>
</div>