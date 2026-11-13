<div class="subnav-pills-bar">
    @if(auth()->check() && (auth()->user()->hasRole('Admin') || auth()->user()->can('view_profitability')))
    <a href="{{ route('reports.profitability') }}" class="subnav-pill {{ request()->routeIs('reports.profitability') ? 'active' : '' }}">
        <i class="fas fa-chart-pie text-danger"></i>
        <span>Bénéfices & Marges</span>
    </a>
    <a href="{{ route('reports.profitability.costs') }}" class="subnav-pill {{ request()->routeIs('reports.profitability.costs') ? 'active' : '' }}">
        <i class="fas fa-coins text-warning"></i>
        <span>Gestion des Coûts d'Achat</span>
    </a>
    @endif
    @if(auth()->check() && (auth()->user()->hasRole('Admin') || auth()->user()->can('manage_expenses') || auth()->user()->can('manage_accounting')))
    <a href="{{ route('expenses.index') }}" class="subnav-pill {{ request()->routeIs('expenses.*') ? 'active' : '' }}">
        <i class="fas fa-file-invoice-dollar text-danger"></i>
        <span>Dépenses de l'Entreprise</span>
    </a>
    @endif
    @if(auth()->check() && (auth()->user()->hasRole('Admin') || auth()->user()->can('view_profitability') || auth()->user()->can('view_reports')))
    <a href="{{ route('shipments.index') }}" class="subnav-pill {{ request()->routeIs('shipments.*') ? 'active' : '' }}">
        <i class="fas fa-truck-ramp-box text-primary"></i>
        <span>Arrivages & Coûts Douane</span>
    </a>
    @endif
    @if(auth()->check() && (auth()->user()->hasRole('Admin') || auth()->user()->can('manage_accounting')))
    <a href="{{ route('accounting.index') }}" class="subnav-pill {{ request()->routeIs('accounting.*') ? 'active' : '' }}">
        <i class="fas fa-scale-balanced text-success"></i>
        <span>Comptabilité & Journaux</span>
    </a>
    @endif
</div>

