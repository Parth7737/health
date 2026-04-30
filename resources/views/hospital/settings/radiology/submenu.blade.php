
<div class="card">
    <div class="card-body">
        <div class="nav flex-lg-column nav-pills nav-primary">
            @can('view-radiology-test')
                <a class="nav-link {{ request()->routeIs('hospital.settings.radiology.test.*') ? 'active' : '' }}" href="{{ route('hospital.settings.radiology.test.index') }}">Radiology Test</a>
            @endcan
            @can('view-radiology-category')
                <a class="nav-link {{ request()->routeIs('hospital.settings.radiology.category.index') ? 'active' : '' }}" href="{{ route('hospital.settings.radiology.category.index') }}">Radiology Category</a>
            @endcan
            @can('view-radiology-unit')
                <a class="nav-link {{ request()->routeIs('hospital.settings.radiology.unit.index') ? 'active' : '' }}" href="{{ route('hospital.settings.radiology.unit.index') }}">Radiology Unit</a>
            @endcan
            @can('view-radiology-parameter')
                <a class="nav-link {{ request()->routeIs('hospital.settings.radiology.parameter.index') ? 'active' : '' }}" href="{{ route('hospital.settings.radiology.parameter.index') }}">Radiology Parameter</a>
            @endcan
        </div>
    </div>
</div>