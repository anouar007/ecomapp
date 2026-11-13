<div class="subnav-pills-bar">
    <a href="{{ route('customers.index') }}" class="subnav-pill {{ request()->routeIs('customers.*') && request('filter') !== 'blacklist' ? 'active' : '' }}">
        <i class="fas fa-users"></i>
        <span>Fiches & Profils Clients</span>
        @if(isset($stats['total_customers']))
            <span class="subnav-pill-badge">{{ $stats['total_customers'] }}</span>
        @endif
    </a>
    <a href="{{ route('client-credits.index') }}" class="subnav-pill {{ request()->routeIs('client-credits.index') ? 'active' : '' }}">
        <i class="fas fa-wallet text-danger"></i>
        <span>Crédits, Créances & Dettes</span>
        <span class="subnav-pill-badge" style="background: rgba(220, 38, 38, 0.12); color: #dc2626;">Trésorerie</span>
    </a>
    <a href="{{ route('debtors.index') }}" class="subnav-pill {{ request()->routeIs('debtors.*') ? 'active' : '' }}">
        <i class="fas fa-hand-holding-dollar text-warning"></i>
        <span>Analyse Débiteurs & Risques</span>
    </a>
    <a href="{{ route('client-credits.transactions') }}" class="subnav-pill {{ request()->routeIs('client-credits.transactions') ? 'active' : '' }}">
        <i class="fas fa-list-check text-primary"></i>
        <span>Grand Livre des Règlements</span>
    </a>
    <a href="{{ route('customers.index', ['filter' => 'blacklist']) }}" class="subnav-pill {{ request()->routeIs('customers.index') && request('filter') === 'blacklist' ? 'active' : '' }}">
        <i class="fas fa-ban text-secondary"></i>
        <span>Liste Noire & Litiges</span>
        @if(($stats['blacklisted_customers'] ?? 0) > 0)
            <span class="subnav-pill-badge" style="background: #fee2e2; color: #991b1b;">{{ $stats['blacklisted_customers'] }}</span>
        @endif
    </a>
</div>

