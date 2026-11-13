@extends('layouts.app')

@section('title', 'Gestion des Clients')

@section('content')
    <!-- En-tête de page (Executive Brand Header) -->
    <div class="brand-header">
        <div class="d-flex align-items-center gap-2.5">
            <div class="brand-header-icon flex-shrink-0">
                <i class="fas fa-users"></i>
            </div>
            <div>
                <h1 class="brand-title">Gestion des Clients</h1>
                <p class="brand-subtitle d-flex align-items-center gap-2 flex-wrap mb-0">
                    <span>Gérez vos relations clients, fidélisation et liste noire</span>
                    <span class="badge bg-light text-dark border px-2 py-0.5"><i class="fas fa-wallet text-success me-1"></i> <strong style="color: #059669;">{{ currency($stats['total_revenue']) }}</strong> de volume d'affaires</span>
                    <span class="badge bg-light text-dark border px-2 py-0.5"><i class="fas fa-shopping-basket text-primary me-1"></i> Panier moyen : <strong>{{ currency($stats['avg_order_value']) }}</strong></span>
                </p>
            </div>
        </div>
        <div class="brand-header-actions d-flex align-items-center gap-2 flex-wrap">
            <button type="button" class="btn btn-outline-danger shadow-sm rounded-pill px-3 py-2 d-inline-flex align-items-center justify-content-center gap-1.5 fw-semibold" style="font-size: 0.84rem;" data-bs-toggle="modal" data-bs-target="#newBlacklistModal">
                <i class="fas fa-ban"></i> <span>+ Liste Noire</span>
            </button>
            <a href="{{ route('export.customers') }}" class="btn btn-outline-secondary shadow-sm rounded-pill px-3 py-2 d-none d-md-inline-flex align-items-center justify-content-center gap-1.5 fw-semibold" style="font-size: 0.84rem;">
                <i class="fas fa-file-csv text-success"></i> <span>Exporter CSV</span>
            </a>
            <a href="{{ route('customers.create') }}" class="btn-brand-primary rounded-pill px-3.5 py-2 d-inline-flex align-items-center gap-1.5 fw-semibold shadow-sm text-decoration-none" style="font-size: 0.84rem;">
                <i class="fas fa-plus"></i> <span>Nouveau Client</span>
            </a>
        </div>
    </div>

    <!-- Barres de Navigation Croisée ERP (Clients <-> Crédits <-> Grand Livre) -->
    @include('partials.subnav.clients')

    <!-- Cartes KPI Statistiques Spécialisées (Row of 4) -->
    <div class="row g-2.5 g-md-3 mb-4">
        <!-- 1. Total Clients -->
        <div class="col-6 col-lg-3">
            <a href="{{ route('customers.index') }}" class="stat-kpi-card text-decoration-none {{ empty($filter) && !request('status') && !request('search') && !request('customer_group_id') ? 'active-filter' : '' }}">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label">Total Clients</div>
                        <div class="stat-kpi-value text-dark">{{ number_format($stats['total_customers'], 0, ',', ' ') }}</div>
                        <div class="stat-kpi-sub text-muted">
                            <span class="badge bg-light text-secondary border">Tous les comptes inscrits</span>
                        </div>
                    </div>
                    <div class="stat-kpi-icon bg-primary-subtle text-primary">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- 2. Clients Fidèles / VIP -->
        <div class="col-6 col-lg-3">
            <a href="{{ route('customers.index', ['filter' => 'vip']) }}" class="stat-kpi-card text-decoration-none {{ request('filter') === 'vip' ? 'active-filter' : '' }}" style="{{ request('filter') === 'vip' ? 'border-color: #f59e0b !important; box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.2) !important;' : '' }}">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label" style="color: #d97706;">Fidèles / VIP</div>
                        <div class="stat-kpi-value text-warning">{{ number_format($stats['vip_customers'], 0, ',', ' ') }}</div>
                        <div class="stat-kpi-sub text-muted">
                            <i class="fas fa-crown text-warning me-1"></i>Or, Platine & Diamant
                        </div>
                    </div>
                    <div class="stat-kpi-icon" style="background: #fef3c7; color: #d97706;">
                        <i class="fas fa-crown"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- 3. Clients à relancer (Dormants 30+ jours) -->
        <div class="col-6 col-lg-3">
            <a href="{{ route('customers.index', ['filter' => 'dormant']) }}" class="stat-kpi-card text-decoration-none {{ request('filter') === 'dormant' ? 'active-filter' : '' }}" style="{{ request('filter') === 'dormant' ? 'border-color: #6366f1 !important; box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2) !important;' : '' }}" title="Clients habituels sans commande depuis 30+ jours">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label" style="color: #6366f1;">À Relancer</div>
                        <div class="stat-kpi-value" style="color: #4f46e5;">{{ number_format($stats['dormant_customers'], 0, ',', ' ') }}</div>
                        <div class="stat-kpi-sub text-muted">
                            <i class="fas fa-bell text-indigo me-1"></i>Inactifs 30+ jours
                        </div>
                    </div>
                    <div class="stat-kpi-icon" style="background: #ede9fe; color: #6366f1;">
                        <i class="fas fa-user-clock"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- 4. Liste Noire (Blacklist) -->
        <div class="col-6 col-lg-3">
            <a href="{{ route('customers.index', ['filter' => 'blacklist']) }}" class="stat-kpi-card text-decoration-none {{ request('filter') === 'blacklist' ? 'active-filter' : '' }}" style="{{ request('filter') === 'blacklist' ? 'border-color: #ef4444 !important; box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.2) !important;' : '' }}" title="Clients sur liste noire">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label text-danger">Liste Noire</div>
                        <div class="stat-kpi-value text-danger">{{ number_format($stats['blacklisted_customers'], 0, ',', ' ') }}</div>
                        <div class="stat-kpi-sub text-muted">
                            <i class="fas fa-ban text-danger me-1"></i>Bloqués & Signalés
                        </div>
                    </div>
                    <div class="stat-kpi-icon" style="background: #fee2e2; color: #dc2626;">
                        <i class="fas fa-ban"></i>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Tabs Navigation Pills (Filtres Rapides) -->
    <div class="d-flex align-items-center gap-2 mb-3 overflow-auto pb-1 flex-nowrap" style="scrollbar-width: thin;">
        <a href="{{ route('customers.index') }}" class="btn btn-sm rounded-pill px-3 py-1.5 fw-semibold text-nowrap {{ empty($filter) && !request('status') ? 'btn-brand-primary' : 'btn-light border text-secondary' }}">
            <i class="fas fa-list me-1"></i> Tous ({{ $stats['total_customers'] }})
        </a>
        <a href="{{ route('customers.index', ['filter' => 'vip']) }}" class="btn btn-sm rounded-pill px-3 py-1.5 fw-semibold text-nowrap {{ request('filter') === 'vip' ? 'btn-warning text-dark shadow-2xs' : 'btn-light border text-secondary' }}">
            <i class="fas fa-crown text-warning me-1"></i> Fidèles & VIP ({{ $stats['vip_customers'] }})
        </a>
        <a href="{{ route('customers.index', ['filter' => 'dormant']) }}" class="btn btn-sm rounded-pill px-3 py-1.5 fw-semibold text-nowrap {{ request('filter') === 'dormant' ? 'text-white shadow-2xs' : 'btn-light border text-secondary' }}" style="{{ request('filter') === 'dormant' ? 'background: #4f46e5; border-color: #4f46e5;' : '' }}">
            <i class="fas fa-user-clock me-1"></i> À Relancer ({{ $stats['dormant_customers'] }})
        </a>
        <a href="{{ route('customers.index', ['filter' => 'blacklist']) }}" class="btn btn-sm rounded-pill px-3 py-1.5 fw-semibold text-nowrap {{ request('filter') === 'blacklist' ? 'btn-danger text-white shadow-2xs' : 'btn-light border text-secondary' }}">
            <i class="fas fa-ban me-1"></i> Liste Noire ({{ $stats['blacklisted_customers'] }})
        </a>
        @if(request('search') || request('status') || request('customer_group_id') || request('filter'))
            <a href="{{ route('customers.index') }}" class="btn btn-sm btn-link text-muted text-decoration-none ms-auto text-nowrap">
                <i class="fas fa-times me-1"></i>Effacer filtres
            </a>
        @endif
    </div>

    <!-- Barre de filtres unifiée (Desktop & Mobile) -->
    <div class="brand-filter-bar mb-3">
        <form method="GET" action="{{ route('customers.index') }}" id="customerFilterForm">
            @if(request('filter'))
                <input type="hidden" name="filter" value="{{ request('filter') }}">
            @endif

            <!-- Row 1 : Recherche + Bouton Drawer Mobile + Actions Desktop -->
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <div class="brand-search-wrapper flex-grow-1" style="min-width: 220px;">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" class="form-control" 
                           placeholder="Rechercher par nom, email, téléphone ou code client..."
                           value="{{ request('search') }}">
                </div>

                @php
                    $activeFilterKeys = ['status', 'customer_group_id'];
                    $hasActiveFilters = collect($activeFilterKeys)->some(fn($k) => request()->filled($k));
                    $activeFilterCount = collect($activeFilterKeys)->filter(fn($k) => request()->filled($k))->count();
                @endphp

                <!-- Bouton Drawer Filtres Mobile -->
                <button type="button" 
                        class="btn d-md-none {{ $hasActiveFilters ? 'btn-brand-primary' : 'btn-brand-light' }} d-inline-flex align-items-center gap-1.5"
                        data-bs-toggle="collapse" 
                        data-bs-target="#customerFiltersCollapse" 
                        aria-expanded="{{ $hasActiveFilters ? 'true' : 'false' }}">
                    <i class="fas fa-sliders-h"></i>
                    <span>Filtres</span>
                    @if($activeFilterCount > 0)
                        <span class="badge bg-white text-dark rounded-pill px-1.5" style="font-size: 0.7rem;">{{ $activeFilterCount }}</span>
                    @endif
                </button>

                <!-- Actions Desktop Principales -->
                <div class="d-none d-md-flex align-items-center gap-2 ms-md-auto">
                    <button type="submit" class="btn-brand-primary btn-sm px-3 py-2 d-inline-flex align-items-center gap-1.5" title="Appliquer les filtres">
                        <i class="fas fa-filter"></i> <span>Filtrer</span>
                    </button>
                    @if(request('search') || $hasActiveFilters || request('filter'))
                        <a href="{{ route('customers.index') }}" class="btn-brand-light btn-sm px-2.5 py-2 text-decoration-none" title="Réinitialiser tous les filtres">
                            <i class="fas fa-redo"></i>
                        </a>
                    @endif
                    <span class="badge bg-light text-secondary border px-3 py-2 fw-semibold" style="font-size: 0.8rem;">
                        {{ $customers->total() }} {{ Str::plural('client', $customers->total()) }}
                    </span>
                </div>
            </div>

            <!-- Row 2 / Drawer Filtres Mobile & Filtres Avancés Desktop -->
            <div class="collapse d-md-block" id="customerFiltersCollapse">
                <div class="row g-2 pt-2 pt-md-3 border-top border-md-0 mt-2 mt-md-0 align-items-center">
                    <!-- Statut Compte -->
                    <div class="col-6 col-md-3">
                        <label class="form-label text-muted small fw-semibold mb-1" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.04em;">Statut Compte</label>
                        <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">Tous les statuts</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Actif</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactif</option>
                            <option value="blocked" {{ request('status') === 'blocked' ? 'selected' : '' }}>Bloqué</option>
                        </select>
                    </div>

                    <!-- Groupe Client -->
                    <div class="col-6 col-md-3">
                        <label class="form-label text-muted small fw-semibold mb-1" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.04em;">Groupe client</label>
                        <select name="customer_group_id" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">Tous les groupes</option>
                            @foreach($customerGroups as $group)
                                <option value="{{ $group->id }}" {{ request('customer_group_id') == $group->id ? 'selected' : '' }}>
                                    {{ $group->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Actions Mobile Drawer -->
                    <div class="col-12 d-flex d-md-none align-items-center justify-content-between gap-2 mt-2 pt-2 border-top">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-brand-primary btn-sm px-3">
                                <i class="fas fa-check me-1"></i> Appliquer
                            </button>
                            @if(request('search') || $hasActiveFilters || request('filter'))
                                <a href="{{ route('customers.index') }}" class="btn btn-light btn-sm border">
                                    <i class="fas fa-redo me-1"></i> Réinitialiser
                                </a>
                            @endif
                        </div>
                        <a href="{{ route('export.customers') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-file-csv text-success me-1"></i> CSV
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- VUE MOBILE (Cartes tactiles fluides) -->
    <div class="d-block d-md-none">
        @forelse($customers as $customer)
            @include('customers.partials.mobile-customer-card', ['customer' => $customer])
        @empty
            <div class="card border-0 shadow-sm rounded-4 p-4 text-center my-3 bg-white">
                <div class="brand-avatar mx-auto mb-3" style="width: 56px; height: 56px; font-size: 20px; background: #f8fafc; color: #94a3b8; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                    @if(request('filter') === 'blacklist')
                        <i class="fas fa-shield-halved text-success"></i>
                    @elseif(request('filter') === 'dormant')
                        <i class="fas fa-user-check text-primary"></i>
                    @else
                        <i class="fas fa-users-slash"></i>
                    @endif
                </div>
                <h6 class="fw-bold text-dark mb-1">
                    @if(request('filter') === 'blacklist')
                        Aucun client sur la liste noire
                    @elseif(request('filter') === 'dormant')
                        Aucun client à relancer
                    @else
                        Aucun client trouvé
                    @endif
                </h6>
                <p class="text-muted small mb-3">
                    @if(request('filter') === 'blacklist')
                        Félicitations, tous vos clients ont un statut sain sans incident signalé !
                    @elseif(request('filter') === 'dormant')
                        Tous vos clients réguliers sont actifs et ont commandé récemment.
                    @else
                        Aucun compte ne correspond à vos filtres actuels.
                    @endif
                </p>
                <div>
                    <a href="{{ route('customers.index') }}" class="btn btn-sm btn-brand-light rounded-pill px-3">
                        <i class="fas fa-redo me-1"></i> Voir tous les clients
                    </a>
                </div>
            </div>
        @endforelse

        @if($customers->hasPages())
            <div class="my-3 d-flex justify-content-center">
                {{ $customers->links() }}
            </div>
        @endif
    </div>

    <!-- VUE DESKTOP (Tableau Haute Définition - Contenu Complet) -->
    <div class="d-none d-md-block">
        <div class="brand-table-card customers-table-card shadow-sm border">
            <div class="table-responsive">
                <table class="brand-table align-middle mb-0" style="width: 100%; min-width: 1060px;">
                    <thead>
                        <tr>
                            <th style="min-width: 230px; padding-left: 1.25rem;">Client & Rang</th>
                            <th style="min-width: 210px;">Contact</th>
                            <th style="min-width: 120px;">Groupe</th>
                            <th style="min-width: 100px;" class="text-center">Commandes</th>
                            <th style="min-width: 130px;" class="text-end">Dépensé (LTV)</th>
                            <th style="min-width: 130px;" class="text-center">Statut</th>
                            <th style="min-width: 170px; padding-right: 1.25rem;" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $customer)
                        @php
                            $statusClasses = [
                                'active' => ['class' => 'success', 'dot' => 'dot-green', 'label' => 'Actif'],
                                'inactive' => ['class' => 'warning', 'dot' => 'dot-amber', 'label' => 'Inactif'],
                                'blocked' => ['class' => 'danger', 'dot' => 'dot-red', 'label' => 'Bloqué'],
                            ];
                            $statusInfo = $statusClasses[$customer->status] ?? ['class' => 'secondary', 'dot' => 'dot-gray', 'label' => ucfirst($customer->status_label)];
                            $initials = collect(explode(' ', $customer->name))->map(fn($w) => mb_substr($w, 0, 1))->take(2)->join('');
                        @endphp
                        <tr class="hover-row">
                            <!-- Client: Avatar + Nom + Tier Badge + Code -->
                            <td style="padding-left: 1.25rem;">
                                <div class="d-flex align-items-center gap-2.5">
                                    <div class="mobile-cust-avatar" style="width: 38px; height: 38px; min-width: 38px; font-size: 0.88rem; border-radius: 10px; {{ $customer->is_blacklisted ? 'background: #fee2e2; color: #dc2626; border: 1.5px solid #fca5a5;' : '' }}">
                                        @if($customer->is_blacklisted)
                                            <i class="fas fa-ban"></i>
                                        @else
                                            {{ strtoupper($initials ?: 'CL') }}
                                        @endif
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                            <a href="{{ route('customers.show', $customer) }}" class="text-decoration-none fw-bold text-dark" style="font-size: 0.88rem; line-height: 1.35;" title="{{ $customer->name }}">
                                                {{ $customer->name }}
                                            </a>
                                            @if($customer->is_blacklisted)
                                                <span class="badge bg-danger text-white fw-bold py-0.5 px-1.5" style="font-size: 0.65rem;" title="Liste Noire : {{ $customer->blacklist_reason ?: 'Sans motif spécifié' }}">
                                                    <i class="fas fa-ban me-0.5"></i>Blacklist
                                                </span>
                                            @endif
                                            @php $tier = $customer->tier; @endphp
                                            <span class="badge {{ $tier['badge_class'] }} py-0.5 px-1.5" style="font-size: 0.65rem;" title="{{ $tier['description'] }}">
                                                <i class="fas {{ $tier['icon'] }} me-0.5"></i>{{ $tier['short'] }}
                                            </span>
                                        </div>
                                        <span class="badge bg-light text-secondary border font-monospace px-1.5 py-0.5 mt-0.5" style="font-size: 0.7rem;">
                                            {{ $customer->customer_code }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <!-- Contact: Email + Téléphone + Ville -->
                            <td>
                                <div>
                                    <div style="font-size: 0.82rem; word-break: break-all;">
                                        <a href="mailto:{{ $customer->email }}" class="text-dark text-decoration-none" title="{{ $customer->email }}">
                                            <i class="fas fa-envelope text-muted me-1" style="font-size: 0.72rem;"></i>{{ $customer->email }}
                                        </a>
                                    </div>
                                    @if($customer->phone)
                                        <div class="text-muted font-monospace mt-0.5 d-flex align-items-center gap-1.5 flex-wrap" style="font-size: 0.74rem;">
                                            <a href="tel:{{ $customer->phone }}" class="text-secondary text-decoration-none text-nowrap">
                                                <i class="fas fa-phone-alt me-1 text-muted" style="font-size: 0.65rem;"></i>{{ $customer->phone }}
                                            </a>
                                            @if($customer->city)
                                                <span class="badge bg-light text-muted border px-1.5 py-0.5 text-nowrap" style="font-size: 0.65rem;">
                                                    <i class="fas fa-map-marker-alt text-danger me-0.5"></i>{{ $customer->city }}
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </td>

                            <!-- Groupe Client -->
                            <td>
                                <div>
                                    @if($customer->customerGroup)
                                        <span class="badge rounded-pill px-2 py-1 d-inline-block" style="font-size: 0.74rem; background-color: {{ $customer->customerGroup->color }}18; color: {{ $customer->customerGroup->color }}; border: 1px solid {{ $customer->customerGroup->color }}40;">
                                            {{ $customer->customerGroup->name }}
                                        </span>
                                    @else
                                        <span class="badge bg-light text-muted border rounded-pill px-2 py-0.5" style="font-size: 0.72rem;">Standard</span>
                                    @endif
                                </div>
                            </td>

                            <!-- Commandes -->
                            <td class="text-center">
                                <a href="{{ route('customers.show', $customer) }}" class="badge bg-light text-dark border rounded-pill px-2.5 py-1 fw-bold text-decoration-none hover-primary" style="font-size: 0.80rem;" title="Voir historique">
                                    {{ $customer->total_orders }}
                                </a>
                            </td>

                            <!-- Total Dépensé (LTV) -->
                            <td class="text-end">
                                <div class="fw-bold text-dark text-nowrap" style="font-size: 0.88rem;">{{ $customer->formatted_total_spent }}</div>
                                @if($customer->loyalty_points > 0)
                                    <div class="mt-0.5 text-nowrap" style="font-size: 0.68rem; color: #8b5cf6 !important; font-weight: 600;">
                                        <i class="fas fa-star me-0.5" style="font-size: 0.62rem;"></i>{{ number_format($customer->loyalty_points) }} pts
                                    </div>
                                @endif
                            </td>

                            <!-- Statut / Blacklist / Dormant -->
                            <td class="text-center">
                                @if($customer->is_blacklisted)
                                    <span class="badge bg-danger text-white rounded-pill px-2 py-1 fw-bold text-nowrap" style="font-size: 0.70rem;" title="{{ $customer->blacklist_reason }}">
                                        <i class="fas fa-ban me-1"></i>Liste Noire
                                    </span>
                                @elseif($customer->is_dormant)
                                    <span class="badge rounded-pill px-2 py-1 text-nowrap" style="font-size: 0.70rem; background: #ede9fe; color: #4f46e5; border: 1px solid #c7d2fe;" title="Dernière commande il y a {{ $customer->days_since_last_order }} jours">
                                        <i class="fas fa-user-clock me-1"></i>Inactif ({{ $customer->days_since_last_order }}j)
                                    </span>
                                @else
                                    <span class="badge bg-{{ $statusInfo['class'] }}-subtle text-{{ $statusInfo['class'] }} border border-{{ $statusInfo['class'] }}-subtle rounded-pill px-2 py-1 d-inline-flex align-items-center gap-1.5 text-nowrap" style="font-size: 0.72rem; font-weight: 600;">
                                        <span class="status-dot {{ $statusInfo['dot'] }}"></span>
                                        <span>{{ $statusInfo['label'] }}</span>
                                    </span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td style="padding-right: 1.25rem;" class="text-end">
                                <div class="action-buttons-group justify-content-end flex-nowrap" style="gap: 4px;">
                                    @if($customer->phone)
                                        @php
                                            $cleanPhone = preg_replace('/[^0-9]/', '', $customer->phone);
                                            if (str_starts_with($cleanPhone, '0')) {
                                                $cleanPhone = '212' . substr($cleanPhone, 1);
                                            }
                                            $waMsg = urlencode("Bonjour {$customer->name}, l'équipe WINA SHOP espère que vous allez bien ! Avez-vous besoin d'un renseignement sur nos nouveaux produits ?");
                                        @endphp
                                        <a href="https://wa.me/{{ $cleanPhone }}?text={{ $waMsg }}" target="_blank" class="action-btn text-success" title="Contacter sur WhatsApp" style="background: #f0fdf4; border-color: #bbf7d0;">
                                            <i class="fab fa-whatsapp"></i>
                                        </a>
                                    @endif
                                    <a href="{{ route('customers.show', $customer) }}" class="action-btn action-btn-primary" title="Consulter l'historique et profil 360">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <form action="{{ route('customers.toggle-blacklist', $customer) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ $customer->is_blacklisted ? 'Retirer ce client de la liste noire ?' : 'Placer ce client sur liste noire ?' }}');">
                                        @csrf
                                        <button type="submit" class="action-btn {{ $customer->is_blacklisted ? 'text-success' : 'text-danger' }}" title="{{ $customer->is_blacklisted ? 'Retirer de la Liste Noire' : 'Mettre sur Liste Noire' }}">
                                            <i class="fas {{ $customer->is_blacklisted ? 'fa-check' : 'fa-ban' }}"></i>
                                        </button>
                                    </form>
                                    <a href="{{ route('customers.edit', $customer) }}" class="action-btn" title="Modifier le client">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('customers.destroy', $customer) }}" method="POST" class="d-inline" onsubmit="return confirm('Voulez-vous vraiment supprimer ce client ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-btn action-btn-danger" title="Supprimer">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7">
                                <div class="text-center py-5">
                                    <div class="brand-avatar mx-auto mb-3" style="width: 56px; height: 56px; font-size: 22px; background: #f8fafc; color: #94a3b8; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                                        @if(request('filter') === 'blacklist')
                                            <i class="fas fa-shield-halved text-success"></i>
                                        @elseif(request('filter') === 'dormant')
                                            <i class="fas fa-user-check text-primary"></i>
                                        @else
                                            <i class="fas fa-users-slash"></i>
                                        @endif
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1">
                                        @if(request('filter') === 'blacklist')
                                            Aucun client sur la liste noire
                                        @elseif(request('filter') === 'dormant')
                                            Aucun client à relancer
                                        @else
                                            Aucun client trouvé
                                        @endif
                                    </h6>
                                    <p class="text-muted small mb-3">
                                        @if(request('filter') === 'blacklist')
                                            Tous vos comptes clients ont un statut régulier et sain.
                                        @elseif(request('filter') === 'dormant')
                                            Vos clients réguliers ont tous passé commande récemment !
                                        @else
                                            Aucun enregistrement ne correspond à vos filtres actuels.
                                        @endif
                                    </p>
                                    <a href="{{ route('customers.index') }}" class="btn btn-sm btn-brand-light rounded-pill px-3">
                                        <i class="fas fa-redo me-1"></i> Réinitialiser les filtres
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($customers->hasPages())
            <div class="px-4 py-3 border-top">
                {{ $customers->links() }}
            </div>
            @endif
        </div>
    </div>

    <!-- Modal : Ajouter un client à la Liste Noire -->
    <div class="modal fade" id="newBlacklistModal" tabindex="-1" aria-labelledby="newBlacklistModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                <div class="modal-header border-bottom px-4 pt-4 pb-3" style="background: #fef2f2; border-top-left-radius: 16px; border-top-right-radius: 16px;">
                    <div class="d-flex align-items-center gap-2.5">
                        <div style="width: 38px; height: 38px; border-radius: 10px; background: #fee2e2; color: #dc2626; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; border: 1px solid #fecaca;">
                            <i class="fas fa-ban"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-danger mb-0" id="newBlacklistModalLabel">Inscrire un Client sur Liste Noire</h5>
                            <div class="text-muted small">Ce profil sera bloqué et signalé lors de toute commande future</div>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <form method="POST" action="{{ route('customers.blacklist.store') }}">
                    @csrf
                    <div class="modal-body p-4">
                        @if(isset($allCustomersForBlacklist) && $allCustomersForBlacklist->count() > 0)
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark small">Choisir un client existant (Optionnel)</label>
                            <select class="form-select form-select-sm" onchange="
                                if (this.value) {
                                    const opt = this.options[this.selectedIndex];
                                    document.getElementById('blNameInput').value = opt.dataset.name || '';
                                    document.getElementById('blPhoneInput').value = opt.dataset.phone || '';
                                    document.getElementById('blEmailInput').value = opt.dataset.email || '';
                                }
                            ">
                                <option value="">-- Ou saisir manuellement ci-dessous --</option>
                                @foreach($allCustomersForBlacklist as $ac)
                                    <option value="{{ $ac->id }}" data-name="{{ $ac->name }}" data-phone="{{ $ac->phone }}" data-email="{{ $ac->email }}">
                                        {{ $ac->name }} ({{ $ac->phone ?: $ac->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @endif

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark small">Nom ou Raison Sociale <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="blNameInput" class="form-control" placeholder="Ex: Mohammed Alami" required>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label fw-semibold text-dark small">Numéro de Téléphone</label>
                                <input type="text" name="phone" id="blPhoneInput" class="form-control font-monospace" placeholder="Ex: 0612345678">
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold text-dark small">Adresse Email</label>
                                <input type="email" name="email" id="blEmailInput" class="form-control" placeholder="client@domaine.com">
                            </div>
                            <div class="col-12 form-text small">Renseignez au moins le téléphone ou l'email pour que le système identifie le client lors de futures commandes.</div>
                        </div>

                        <div class="mb-2">
                            <label class="form-label fw-semibold text-dark small">Motif du blacklistage <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm mb-2" onchange="if(this.value){ document.getElementById('modalBlacklistReason').value = this.value; }">
                                <option value="">-- Choisir un motif prédéfini --</option>
                                <option value="Colis refusé à la livraison sans motif">Colis refusé à la livraison sans motif</option>
                                <option value="Fausse commande / Numéro injoignable">Fausse commande / Numéro injoignable</option>
                                <option value="Impayé / Chèque rejeté">Impayé / Chèque rejeté</option>
                                <option value="Comportement abusif / Harcèlement">Comportement abusif / Harcèlement</option>
                                <option value="Retours répétés injustifiés">Retours répétés injustifiés</option>
                            </select>
                            <textarea name="reason" id="modalBlacklistReason" class="form-control" rows="3" placeholder="Décrivez le motif..." required>Colis refusé à la livraison sans motif</textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-top px-4 py-3 bg-light" style="border-bottom-left-radius: 16px; border-bottom-right-radius: 16px;">
                        <button type="button" class="btn btn-sm btn-light border rounded-pill px-3 py-1.5" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-sm btn-danger rounded-pill px-4 py-1.5 fw-semibold shadow-xs" style="background: #dc2626; border-color: #dc2626;">
                            <i class="fas fa-ban me-1.5"></i> Enregistrer sur Liste Noire
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
