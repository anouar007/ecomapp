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
                <p class="brand-subtitle">Gérez vos relations clients, groupes, limites de crédit et fidélité</p>
            </div>
        </div>
        <div class="brand-header-actions d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('export.customers') }}" class="btn btn-outline-secondary shadow-sm rounded-pill px-3 py-2 d-none d-md-inline-flex align-items-center justify-content-center gap-1.5 fw-semibold" style="font-size: 0.84rem;">
                <i class="fas fa-file-csv text-success"></i> <span>Exporter CSV</span>
            </a>
            <a href="{{ route('customers.create') }}" class="btn-brand-primary rounded-pill px-3.5 py-2 d-inline-flex align-items-center gap-1.5 fw-semibold shadow-sm text-decoration-none" style="font-size: 0.84rem;">
                <i class="fas fa-plus"></i> <span>Nouveau Client</span>
            </a>
        </div>
    </div>

    <!-- Cartes KPI Statistiques (Row of 4) -->
    <div class="row g-3 mb-4">
        <!-- Total Clients -->
        <div class="col-6 col-lg-3">
            <a href="{{ route('customers.index') }}" class="stat-kpi-card text-decoration-none {{ !request('status') && !request('search') && !request('customer_group_id') ? 'active-filter' : '' }}">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label">Total Clients</div>
                        <div class="stat-kpi-value text-dark">{{ number_format($stats['total_customers'], 0, ',', ' ') }}</div>
                        <div class="stat-kpi-sub text-muted">
                            <span class="badge bg-light text-secondary border">Inscrits au registre</span>
                        </div>
                    </div>
                    <div class="stat-kpi-icon bg-primary-subtle text-primary">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- Clients Actifs -->
        <div class="col-6 col-lg-3">
            <a href="{{ route('customers.index', ['status' => 'active']) }}" class="stat-kpi-card text-decoration-none {{ request('status') === 'active' ? 'active-filter' : '' }}">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label">Clients Actifs</div>
                        <div class="stat-kpi-value text-success">{{ number_format($stats['active_customers'], 0, ',', ' ') }}</div>
                        <div class="stat-kpi-sub text-muted">
                            <i class="fas fa-check-circle text-success me-1"></i>Comptes actifs
                        </div>
                    </div>
                    <div class="stat-kpi-icon bg-success-subtle text-success">
                        <i class="fas fa-user-check"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- Volume d'Affaires -->
        <div class="col-6 col-lg-3">
            <div class="stat-kpi-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label">Volume d'Affaires</div>
                        <div class="stat-kpi-value text-info" style="font-size: clamp(0.95rem, 3.2vw, 1.35rem);">{{ currency($stats['total_revenue']) }}</div>
                        <div class="stat-kpi-sub text-muted">
                            <i class="fas fa-chart-line text-info me-1"></i>Total facturé
                        </div>
                    </div>
                    <div class="stat-kpi-icon bg-info-subtle text-info">
                        <i class="fas fa-wallet"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panier Moyen -->
        <div class="col-6 col-lg-3">
            <div class="stat-kpi-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label">Panier Moyen</div>
                        <div class="stat-kpi-value text-warning" style="font-size: clamp(0.95rem, 3.2vw, 1.35rem);">{{ currency($stats['avg_order_value'] ?? 0) }}</div>
                        <div class="stat-kpi-sub text-muted">
                            <i class="fas fa-calculator text-warning me-1"></i>Moy. / commande
                        </div>
                    </div>
                    <div class="stat-kpi-icon bg-warning-subtle text-warning">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Barre de filtres unifiée (Desktop & Mobile) -->
    <div class="brand-filter-bar mb-3">
        <form method="GET" action="{{ route('customers.index') }}" id="customerFilterForm">
            <!-- Row 1 : Recherche + Bouton Drawer Mobile + Actions Desktop -->
            <div class="d-flex align-items-center mb-3 gap-2 flex-wrap">
                <div class="brand-search-wrapper flex-grow-1" style="min-width: 240px;">
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
                <div class="d-none d-md-flex align-items-center gap-2">
                    <button type="submit" class="btn-brand-primary btn-sm px-3 py-2 d-inline-flex align-items-center gap-1.5" title="Appliquer les filtres">
                        <i class="fas fa-filter"></i> <span>Filtrer</span>
                    </button>
                    @if(request('search') || $hasActiveFilters)
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
                <div class="row g-2 pt-2 pt-md-0 border-top border-md-0 mt-2 mt-md-0">
                    <!-- Statut -->
                    <div class="col-6 col-md-3">
                        <label class="form-label text-muted small fw-semibold mb-1" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.04em;">Statut</label>
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
                            @if(request('search') || $hasActiveFilters)
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
            <div class="card border-0 shadow-sm rounded-4 p-4 text-center my-3">
                <div class="brand-avatar mx-auto mb-3" style="width: 56px; height: 56px; font-size: 20px; background: #f8fafc; color: #94a3b8; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                    <i class="fas fa-users-slash"></i>
                </div>
                <h6 class="fw-bold text-dark mb-1">Aucun client trouvé</h6>
                <p class="text-muted small mb-3">Aucun profil ne correspond à vos critères de recherche.</p>
                <div>
                    <a href="{{ route('customers.index') }}" class="btn btn-sm btn-brand-light rounded-pill px-3">
                        <i class="fas fa-redo me-1"></i> Réinitialiser les filtres
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

    <!-- VUE DESKTOP (Tableau Haute Définition - Zéro scroll horizontal) -->
    <div class="d-none d-md-block">
        <div class="brand-table-card customers-table-card shadow-sm border" style="overflow: hidden !important;">
            <div style="overflow-x: hidden !important; width: 100% !important;">
                <table class="brand-table align-middle" style="table-layout: fixed !important; width: 100% !important; margin: 0 !important;">
                    <thead>
                        <tr>
                            <th style="width: 25%; padding-left: 1.25rem;">Client</th>
                            <th style="width: 21%;">Contact</th>
                            <th style="width: 13%;">Groupe</th>
                            <th style="width: 8%;" class="text-center">Commandes</th>
                            <th style="width: 12%;" class="text-end">Dépensé</th>
                            <th style="width: 9%;" class="text-center">Statut</th>
                            <th style="width: 12%; padding-right: 1.25rem;" class="text-end">Actions</th>
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
                        <tr>
                            <!-- Client: Avatar + Nom + Code -->
                            <td style="padding-left: 1.25rem;">
                                <div class="d-flex align-items-center gap-2.5 min-w-0">
                                    <div class="mobile-cust-avatar" style="width: 36px; height: 36px; min-width: 36px; font-size: 0.85rem; border-radius: 10px;">
                                        {{ strtoupper($initials ?: 'CL') }}
                                    </div>
                                    <div class="min-w-0 flex-grow-1" style="max-width: calc(100% - 46px);">
                                        <a href="{{ route('customers.show', $customer) }}" class="text-decoration-none fw-bold text-dark text-truncate d-block" style="font-size: 0.88rem;" title="{{ $customer->name }}">
                                            {{ $customer->name }}
                                        </a>
                                        <span class="badge bg-light text-secondary border font-monospace px-1.5 py-0.5 mt-0.5" style="font-size: 0.7rem;">
                                            {{ $customer->customer_code }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <!-- Contact: Email + Téléphone -->
                            <td>
                                <div class="min-w-0" style="max-width: 100%;">
                                    <div class="text-truncate" style="font-size: 0.82rem;">
                                        <a href="mailto:{{ $customer->email }}" class="text-dark text-decoration-none" title="{{ $customer->email }}">
                                            <i class="fas fa-envelope text-muted me-1" style="font-size: 0.72rem;"></i>{{ $customer->email }}
                                        </a>
                                    </div>
                                    @if($customer->phone)
                                        <div class="text-muted font-monospace mt-0.5 text-truncate" style="font-size: 0.74rem;">
                                            <i class="fas fa-phone-alt me-1 text-muted" style="font-size: 0.65rem;"></i>{{ $customer->phone }}
                                        </div>
                                    @endif
                                </div>
                            </td>

                            <!-- Groupe Client -->
                            <td>
                                <div class="text-truncate">
                                    @if($customer->customerGroup)
                                        <span class="badge rounded-pill px-2 py-1 text-truncate d-inline-block" style="max-width: 100%; font-size: 0.74rem; background-color: {{ $customer->customerGroup->color }}18; color: {{ $customer->customerGroup->color }}; border: 1px solid {{ $customer->customerGroup->color }}40;">
                                            {{ $customer->customerGroup->name }}
                                        </span>
                                    @else
                                        <span class="badge bg-light text-muted border rounded-pill px-2 py-0.5" style="font-size: 0.72rem;">Standard</span>
                                    @endif
                                </div>
                            </td>

                            <!-- Commandes -->
                            <td class="text-center">
                                <span class="badge bg-light text-dark border rounded-pill px-2.5 py-1 fw-bold" style="font-size: 0.80rem;">
                                    {{ $customer->total_orders }}
                                </span>
                            </td>

                            <!-- Total Dépensé -->
                            <td class="text-end">
                                <div class="fw-bold text-dark" style="font-size: 0.88rem;">{{ $customer->formatted_total_spent }}</div>
                                @if($customer->loyalty_points > 0)
                                    <div class="mt-0.5" style="font-size: 0.68rem; color: #8b5cf6 !important; font-weight: 600;">
                                        <i class="fas fa-star me-0.5" style="font-size: 0.62rem;"></i>{{ number_format($customer->loyalty_points) }} pts
                                    </div>
                                @endif
                            </td>

                            <!-- Statut -->
                            <td class="text-center">
                                <span class="badge bg-{{ $statusInfo['class'] }}-subtle text-{{ $statusInfo['class'] }} border border-{{ $statusInfo['class'] }}-subtle rounded-pill px-2 py-1 d-inline-flex align-items-center gap-1.5" style="font-size: 0.72rem; font-weight: 600;">
                                    <span class="status-dot {{ $statusInfo['dot'] }}"></span>
                                    <span>{{ $statusInfo['label'] }}</span>
                                </span>
                            </td>

                            <!-- Actions -->
                            <td style="padding-right: 1.25rem;" class="text-end">
                                <div class="action-buttons-group">
                                    <a href="{{ route('customers.show', $customer) }}" class="action-btn action-btn-primary" title="Consulter le profil 360">
                                        <i class="fas fa-eye"></i>
                                    </a>
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
                                        <i class="fas fa-users-slash"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1">Aucun client trouvé</h6>
                                    <p class="text-muted small mb-3">Aucun enregistrement ne correspond à vos filtres actuels.</p>
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
@endsection
