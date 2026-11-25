<div class="subnav-pills-bar">
    <a href="{{ route('inventory.index') }}" class="subnav-pill {{ request()->routeIs('inventory.index') ? 'active' : '' }}">
        <i class="fas fa-boxes"></i>
        <span>Inventaire Global Articles</span>
    </a>
    <a href="{{ route('stores.stock') }}" class="subnav-pill {{ request()->routeIs('stores.stock') ? 'active' : '' }}">
        <i class="fas fa-table-cells text-primary"></i>
        <span>Matrice par Dépôt & Rayons</span>
    </a>
    <a href="{{ route('stores.index') }}" class="subnav-pill {{ request()->routeIs('stores.index') ? 'active' : '' }}">
        <i class="fas fa-warehouse text-danger"></i>
        <span>Magasins & Points de Vente</span>
    </a>
    <a href="{{ route('inventory.movements') }}" class="subnav-pill {{ request()->routeIs('inventory.movements') ? 'active' : '' }}">
        <i class="fas fa-arrows-rotate text-info"></i>
        <span>Journal Mouvements</span>
    </a>
    @if(auth()->check() && (auth()->user()->hasRole('Admin') || auth()->user()->can('view_profitability')))
    <a href="{{ route('inventory.arrivals', ['status' => 'all']) }}" class="subnav-pill {{ request()->routeIs('inventory.arrivals*') ? 'active' : '' }}">
        <i class="fas fa-layer-group text-success"></i>
        <span>Arrivages &amp; Lots FIFO</span>
    </a>
    @endif
    <a href="{{ route('inventory.alerts') }}" class="subnav-pill {{ request()->routeIs('inventory.alerts') ? 'active' : '' }}">
        <i class="fas fa-bell text-warning"></i>
        <span>Alertes Stock</span>
    </a>
</div>

