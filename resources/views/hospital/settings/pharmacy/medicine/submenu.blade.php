@can('view-medicine')
    <a class="nav-link {{ request()->routeIs('hospital.settings.pharmacy.medicine.index') ? 'active' : '' }}" href="{{ route('hospital.settings.pharmacy.medicine.index') }}">Medicines</a>
@endcan
