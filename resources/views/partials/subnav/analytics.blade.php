<div class="subnav-pills-bar">
    @if(auth()->check() && (auth()->user()->hasAnyRole(['Admin', 'Super Admin']) || auth()->user()->can('view_analytics') || auth()->user()->can('view_analytics_views')))
    <a href="{{ route('analytics.views') }}" class="subnav-pill {{ request()->routeIs('analytics.views*') ? 'active' : '' }}">
        <i class="fas fa-eye text-primary"></i>
        <span>Vues & Trafic Site</span>
    </a>
    @endif
    @if(auth()->check() && (auth()->user()->hasAnyRole(['Admin', 'Super Admin']) || auth()->user()->can('view_analytics') || auth()->user()->can('view_analytics_sales')))
    <a href="{{ route('analytics.index') }}" class="subnav-pill {{ request()->routeIs('analytics.index') ? 'active' : '' }}">
        <i class="fas fa-chart-line text-success"></i>
        <span>Analytique Ventes</span>
    </a>
    @endif
    @if(auth()->check() && (auth()->user()->hasAnyRole(['Admin', 'Super Admin']) || auth()->user()->can('view_analytics') || auth()->user()->can('view_analytics_insights')))
    <a href="{{ route('analytics.insights') }}" class="subnav-pill {{ request()->routeIs('analytics.insights*') ? 'active' : '' }}">
        <i class="fas fa-map-location-dot text-info"></i>
        <span>Insights &amp; Livraisons</span>
    </a>
    @endif
    @if(auth()->check() && (auth()->user()->hasAnyRole(['Admin', 'Super Admin']) || auth()->user()->can('view_reports') || auth()->user()->can('view_analytics')))
    <a href="{{ route('reports.index') }}" class="subnav-pill {{ request()->routeIs('reports.index') ? 'active' : '' }}">
        <i class="fas fa-file-chart-line text-warning"></i>
        <span>Rapports & Exports</span>
    </a>
    @endif
    @if(auth()->check() && (auth()->user()->hasAnyRole(['Admin', 'Super Admin']) || auth()->user()->can('view_profitability')))
    <a href="{{ route('reports.profitability') }}" class="subnav-pill {{ request()->routeIs('reports.profitability*') ? 'active' : '' }}">
        <i class="fas fa-chart-pie text-danger"></i>
        <span>Rentabilité P&L</span>
    </a>
    @endif
</div>

