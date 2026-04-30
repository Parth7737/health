<div class="card">
    <div class="card-body">
        <div class="nav flex-lg-column nav-pills nav-primary">
            @can('view-building')
                <a class="nav-link {{ request()->routeIs('hospital.settings.beds.building.index') ? 'active' : '' }}" href="{{ route('hospital.settings.beds.building.index') }}">Buildings</a>
            @endcan
            @can('view-floor')
                <a class="nav-link {{ request()->routeIs('hospital.settings.beds.floor.index') ? 'active' : '' }}" href="{{ route('hospital.settings.beds.floor.index') }}">Floors</a>
            @endcan
            @can('view-ward')
                <a class="nav-link {{ request()->routeIs('hospital.settings.beds.ward.index') ? 'active' : '' }}" href="{{ route('hospital.settings.beds.ward.index') }}">Wards</a>
            @endcan
            @can('view-room')
                <a class="nav-link {{ request()->routeIs('hospital.settings.beds.room.index') ? 'active' : '' }}" href="{{ route('hospital.settings.beds.room.index') }}">Rooms</a>
            @endcan
            @can('view-bed-type')
                <a class="nav-link {{ request()->routeIs('hospital.settings.beds.bed-type.*') ? 'active' : '' }}" href="{{ route('hospital.settings.beds.bed-type.index') }}">Bed Types</a>
            @endcan
            @can('view-bed')
                <!-- <a class="nav-link {{ request()->routeIs('hospital.settings.beds.bed-dashboard') ? 'active' : '' }}" href="{{ route('hospital.settings.beds.bed-dashboard') }}">Bed Dashboard</a> -->
            @endcan
            @can('view-bed')
                <a class="nav-link {{ request()->routeIs('hospital.settings.beds.bed.index') ? 'active' : '' }}" href="{{ route('hospital.settings.beds.bed.index') }}">Beds</a>
            @endcan
        </div>
    </div>
</div>
