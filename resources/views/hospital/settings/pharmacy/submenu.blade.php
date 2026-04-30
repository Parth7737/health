
<div class="card">
    <div class="card-body">
        <div class="nav flex-lg-column nav-pills nav-primary">
            @can('view-medicine')
                <a class="nav-link {{ request()->routeIs('hospital.settings.pharmacy.medicine.index') ? 'active' : '' }}" href="{{ route('hospital.settings.pharmacy.medicine.index') }}">Medicines</a>
            @endcan
            @can('view-medicine-category')
                <a class="nav-link {{ request()->routeIs('hospital.settings.pharmacy.medicine-category.index') ? 'active' : '' }}" href="{{ route('hospital.settings.pharmacy.medicine-category.index') }}">Medicine Categories</a>
            @endcan
            @can('view-medicine-dosage')
                <a class="nav-link {{ request()->routeIs('hospital.settings.pharmacy.medicine-dosage.index') ? 'active' : '' }}" href="{{ route('hospital.settings.pharmacy.medicine-dosage.index') }}">Medicine Dosage</a>
            @endcan
             @can('view-medicine-instructions')
                <a class="nav-link {{ request()->routeIs('hospital.settings.pharmacy.medicine-instructions.index') ? 'active' : '' }}" href="{{ route('hospital.settings.pharmacy.medicine-instructions.index') }}">Medicine Instructions</a>
            @endcan
            @can('view-frequency')
                <a class="nav-link {{ request()->routeIs('hospital.settings.pharmacy.frequency.index') ? 'active' : '' }}" href="{{ route('hospital.settings.pharmacy.frequency.index') }}">Frequency</a>
                <a class="nav-link {{ request()->routeIs('hospital.settings.pharmacy.medicine-route.index') ? 'active' : '' }}" href="{{ route('hospital.settings.pharmacy.medicine-route.index') }}">Medicine Routes</a>
            @endcan
            @can('view-pharmacy-supplier')
                <a class="nav-link {{ request()->routeIs('hospital.settings.pharmacy.supplier.index') ? 'active' : '' }}" href="{{ route('hospital.settings.pharmacy.supplier.index') }}">Suppliers</a>
            @endcan
        </div>
    </div>
</div>