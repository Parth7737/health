<div class="card mb-3">
    <div class="card-body py-2">
        <div class="nav nav-pills nav-primary gap-2">
            @can('view-pharmacy-sale')
                <a class="nav-link {{ request()->routeIs('hospital.pharmacy.sale.*') ? 'active' : '' }}" href="{{ route('hospital.pharmacy.sale.index') }}">Sale Bills</a>
            @endcan
            @can('view-pharmacy-purchase')
                <a class="nav-link {{ request()->routeIs('hospital.pharmacy.purchase.*') ? 'active' : '' }}" href="{{ route('hospital.pharmacy.purchase.index') }}">Purchase Bills</a>
            @endcan
            @can('view-pharmacy-stock')
                <a class="nav-link {{ request()->routeIs('hospital.pharmacy.stock.*') ? 'active' : '' }}" href="{{ route('hospital.pharmacy.stock.index') }}">Stock Register</a>
            @endcan
            @can('view-pharmacy-expiry')
                <a class="nav-link {{ request()->routeIs('hospital.pharmacy.expiry.*') ? 'active' : '' }}" href="{{ route('hospital.pharmacy.expiry.index') }}">Expiry Management</a>
            @endcan
        </div>
    </div>
</div>
