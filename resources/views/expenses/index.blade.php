@extends('layouts.app')

@section('title', 'Gestion des Dépenses & Charges d\'Exploitation')

@push('styles')
<style>
/* ==========================================================================
   EXPENSES & CHARGES STYLES - PRO EXECUTIVE ALIGNMENT
   ========================================================================== */
.expense-kpi-card {
    border-radius: 16px;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    padding: 1.25rem 1.35rem;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.expense-kpi-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 24px rgba(15, 23, 42, 0.06);
}
.kpi-icon-pill {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.15rem;
    flex-shrink: 0;
}
.chips-scroll-expense {
    display: flex;
    gap: 0.5rem;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
    padding-bottom: 2px;
}
.chips-scroll-expense::-webkit-scrollbar {
    display: none;
}
.expense-pill-btn {
    white-space: nowrap;
    border-radius: 9999px !important;
    padding: 0.4rem 0.95rem;
    font-size: 0.8rem;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.15s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    color: #475569;
    flex-shrink: 0;
}
.expense-pill-btn:hover {
    background: #f1f5f9;
    color: #0f172a;
}
.expense-pill-btn.active {
    background: #0f172a;
    color: #ffffff;
    border-color: #0f172a;
    box-shadow: 0 2px 6px rgba(15, 23, 42, 0.2);
}
.proof-thumbnail-btn {
    width: 36px;
    height: 36px;
    object-fit: cover;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    cursor: pointer;
    transition: transform 0.15s ease;
}
.proof-thumbnail-btn:hover {
    transform: scale(1.1);
}
.expense-mobile-card {
    border-radius: 16px !important;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
}
</style>
@endpush

@section('content')
<div class="container-fluid py-4">

    <!-- Top Executive Header -->
    <div class="card border-0 rounded-4 shadow-sm mb-4" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: #ffffff;">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-danger bg-opacity-25 border border-danger border-opacity-50 text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width: 52px; height: 52px; font-size: 1.35rem;">
                        <i class="fas fa-file-invoice-dollar text-white"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                            <span class="badge bg-danger rounded-pill px-2.5 py-0.5 fw-semibold" style="font-size: 0.72rem;">
                                CHARGES & DÉPENSES
                            </span>
                            <span class="text-white-50 small">• Suivi des Coûts d'Exploitation</span>
                        </div>
                        <h1 class="h3 fw-bold text-white mb-0">Gestion des Dépenses de l'Entreprise</h1>
                        <p class="text-white-50 small mb-0 mt-1">Enregistrez tous vos frais (loyer, salaires, publicité, transport, électricité) pour calculer votre <strong>bénéfice net réel global</strong>.</p>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <button type="button" class="btn btn-warning text-dark fw-bold rounded-pill px-3.5 py-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#newRecurringExpenseModal" style="font-size: 0.85rem;">
                        <i class="fas fa-repeat me-1.5"></i> Charge Mensuelle
                    </button>
                    <a href="{{ route('reports.profitability', ['period' => $period]) }}" class="btn btn-outline-light rounded-pill px-3.5 py-2 fw-semibold shadow-xs" style="font-size: 0.85rem;">
                        <i class="fas fa-chart-pie me-1.5 text-warning"></i> Rentabilité & Bénéfice Net
                    </a>
                    <a href="{{ route('expenses.export', request()->all()) }}" class="btn btn-outline-light rounded-pill px-3.5 py-2 fw-semibold shadow-xs" style="font-size: 0.85rem;">
                        <i class="fas fa-file-excel text-success me-1.5"></i> Exporter CSV
                    </a>
                    <button type="button" class="btn btn-danger rounded-pill px-3.5 py-2 fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#newExpenseModal" style="font-size: 0.85rem;">
                        <i class="fas fa-plus-circle me-1.5"></i> Nouvelle Dépense
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert / Flash message -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-xs mb-4" role="alert">
        <div class="d-flex align-items-center gap-2">
            <i class="fas fa-circle-check fs-5"></i>
            <div>{{ session('success') }}</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-xs mb-4" role="alert">
        <div class="d-flex align-items-center gap-2">
            <i class="fas fa-triangle-exclamation fs-5"></i>
            <div>
                <strong>Erreur lors de l'enregistrement :</strong>
                <ul class="mb-0 ps-3 small mt-1">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
    </div>
    @endif

    @if(session('info'))
    <div class="alert alert-info alert-dismissible fade show rounded-4 border-0 shadow-xs mb-4" role="alert">
        <div class="d-flex align-items-center gap-2">
            <i class="fas fa-circle-info fs-5"></i>
            <div>{{ session('info') }}</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
    </div>
    @endif

    <!-- Pending Recurring Expenses Generator Notification Banner -->
    @if($pendingRecurringCount > 0)
    <div class="card border-0 rounded-4 shadow-sm mb-4" style="background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%); border-left: 5px solid #f59e0b !important;">
        <div class="card-body p-3.5 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-warning text-dark d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px; font-size: 1.15rem;">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div>
                    <div class="fw-bold text-dark fs-6">Charges Récurrentes de ce mois ({{ now()->translatedFormat('F Y') }})</div>
                    <div class="text-muted small">
                        <strong>{{ $pendingRecurringCount }}</strong> charge(s) récurrente(s) configurée(s) pour un total de <strong>{{ currency($pendingRecurringAmount) }}</strong> n'ont pas encore été générées pour ce mois-ci.
                    </div>
                </div>
            </div>
            <form method="POST" action="{{ route('expenses.recurring.generate-month') }}" class="flex-shrink-0">
                @csrf
                <button type="submit" class="btn btn-warning text-dark fw-bold rounded-pill px-3.5 py-2 shadow-xs">
                    <i class="fas fa-wand-magic-sparkles me-1.5"></i> Générer les charges du mois (1-Clic)
                </button>
            </form>
        </div>
    </div>
    @endif
    <div class="row g-3 mb-4">
        <!-- 1. Total Dépenses Période -->
        <div class="col-6 col-xl-3">
            <div class="expense-kpi-card" style="background: linear-gradient(135deg, #7f1d1d 0%, #991b1b 100%); color: #ffffff;">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-white-50 fw-semibold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.05em;">Dépenses Période</span>
                        <div class="kpi-icon-pill bg-white text-danger">
                            <i class="fas fa-arrow-trend-down"></i>
                        </div>
                    </div>
                    <div class="h3 fw-bold text-white mb-1">{{ currency($periodExpenses) }}</div>
                </div>
                <div class="pt-2 border-top border-white-50 mt-2 text-white-50 small" style="font-size: 0.74rem;">
                    <span>{{ $periodCount }} écriture(s) sur la période</span>
                </div>
            </div>
        </div>

        <!-- 2. Dépenses Ce Mois-ci -->
        <div class="col-6 col-xl-3">
            <div class="expense-kpi-card">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.05em;">Ce Mois-ci</span>
                        <div class="kpi-icon-pill bg-warning-subtle text-warning">
                            <i class="fas fa-calendar-days"></i>
                        </div>
                    </div>
                    <div class="h3 fw-bold text-dark mb-1">{{ currency($thisMonthExpenses) }}</div>
                </div>
                <div class="pt-2 border-top mt-2 text-muted small" style="font-size: 0.74rem;">
                    <span>Mois de {{ now()->translatedFormat('F Y') }}</span>
                </div>
            </div>
        </div>

        <!-- 3. Moyenne par Dépense -->
        <div class="col-6 col-xl-3">
            <div class="expense-kpi-card">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.05em;">Moyenne par Écriture</span>
                        <div class="kpi-icon-pill bg-primary-subtle text-primary">
                            <i class="fas fa-calculator"></i>
                        </div>
                    </div>
                    <div class="h3 fw-bold text-dark mb-1">{{ currency($avgExpense) }}</div>
                </div>
                <div class="pt-2 border-top mt-2 text-muted small" style="font-size: 0.74rem;">
                    <span>Aujourd'hui : <strong>{{ currency($todayExpenses) }}</strong></span>
                </div>
            </div>
        </div>

        <!-- 4. Top Poste de Dépense -->
        <div class="col-6 col-xl-3">
            <div class="expense-kpi-card">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.05em;">Poste Principal</span>
                        <div class="kpi-icon-pill bg-info-subtle text-info">
                            <i class="fas fa-chart-pie"></i>
                        </div>
                    </div>
                    @if($topCategory)
                        <div class="h5 fw-bold text-dark mb-1 text-truncate" title="{{ $topCategory->label }}">{{ $topCategory->label }}</div>
                        <div class="text-muted small" style="font-size: 0.74rem;">
                            <strong>{{ currency($topCategory->total) }}</strong> ({{ $topCategory->share }}% du total)
                        </div>
                    @else
                        <div class="h5 fw-bold text-muted mb-1">—</div>
                        <div class="text-muted small" style="font-size: 0.74rem;">Aucune dépense enregistrée</div>
                    @endif
                </div>
                <div class="pt-2 border-top mt-2 text-muted small" style="font-size: 0.74rem;">
                    <span>{{ $categoryBreakdown->count() }} poste(s) actif(s)</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Category Breakdown Visual Cards (If expenses exist) -->
    @if($categoryBreakdown->count() > 0)
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3.5">
            <h6 class="fw-bold mb-2.5 text-dark" style="font-size: 0.88rem;">
                <i class="fas fa-layer-group text-danger me-1.5"></i> Répartition des Dépenses par Catégorie
            </h6>
            <div class="row g-2">
                @foreach($categoryBreakdown as $cb)
                <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                    <div class="p-2.5 rounded-3 bg-light border d-flex align-items-center justify-content-between">
                        <div class="min-w-0 me-2">
                            <div class="d-flex align-items-center gap-1.5 mb-1">
                                <span class="badge {{ $cb->badge }} rounded-pill" style="font-size: 0.65rem;">
                                    <i class="{{ $cb->icon }} me-1"></i> {{ $cb->label }}
                                </span>
                            </div>
                            <div class="fw-bold text-dark" style="font-size: 0.88rem;">{{ currency($cb->total) }}</div>
                        </div>
                        <div class="text-end flex-shrink-0">
                            <div class="badge bg-white text-dark border rounded-pill px-2 py-1 fw-bold" style="font-size: 0.72rem;">
                                {{ $cb->share }}%
                            </div>
                            <div class="text-muted small mt-0.5" style="font-size: 0.68rem;">{{ $cb->count }} dépense(s)</div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Navigation Tabs (Grand Livre vs Charges Récurrentes) -->
    <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2 flex-wrap gap-2">
        <ul class="nav nav-pills gap-1.5" id="expensesTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link rounded-pill px-3.5 py-2 fw-semibold {{ request('tab') !== 'recurring' ? 'active' : '' }}" id="tab-ledger-btn" data-bs-toggle="pill" data-bs-target="#tab-ledger" type="button" role="tab" style="font-size: 0.85rem;">
                    <i class="fas fa-book me-1.5"></i> Grand Livre des Dépenses ({{ $expenses->total() }})
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link rounded-pill px-3.5 py-2 fw-semibold {{ request('tab') === 'recurring' ? 'active' : '' }}" id="tab-recurring-btn" data-bs-toggle="pill" data-bs-target="#tab-recurring" type="button" role="tab" style="font-size: 0.85rem;">
                    <i class="fas fa-repeat me-1.5 text-warning"></i> Charges Récurrentes & Mensuelles ({{ $recurringCount }})
                </button>
            </li>
        </ul>

        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-dark rounded-pill px-3 py-2 fw-normal" style="font-size: 0.78rem;">
                <i class="fas fa-fire-flame-curved text-danger me-1"></i> Charges Fixes / Burn Rate : <strong class="text-white">{{ currency($recurringMonthlyTotal) }}/mois</strong>
            </span>
        </div>
    </div>

    <!-- Tab Panes Container -->
    <div class="tab-content" id="expensesTabsContent">

        <!-- TAB 1: GRAND LIVRE DES DÉPENSES -->
        <div class="tab-pane fade {{ request('tab') !== 'recurring' ? 'show active' : '' }}" id="tab-ledger" role="tabpanel">

    <!-- Period & Filter Toolbar -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <!-- Preset Touch Period Pills -->
            <div class="chips-scroll-expense mb-3">
                <a href="{{ route('expenses.index', array_merge(request()->except('period', 'start_date', 'end_date', 'page'), ['period' => 'today'])) }}" 
                   class="expense-pill-btn {{ $period === 'today' ? 'active' : '' }}">
                    Aujourd'hui
                </a>
                <a href="{{ route('expenses.index', array_merge(request()->except('period', 'start_date', 'end_date', 'page'), ['period' => 'yesterday'])) }}" 
                   class="expense-pill-btn {{ $period === 'yesterday' ? 'active' : '' }}">
                    Hier
                </a>
                <a href="{{ route('expenses.index', array_merge(request()->except('period', 'start_date', 'end_date', 'page'), ['period' => 'week'])) }}" 
                   class="expense-pill-btn {{ $period === 'week' ? 'active' : '' }}">
                    7 Derniers Jours
                </a>
                <a href="{{ route('expenses.index', array_merge(request()->except('period', 'start_date', 'end_date', 'page'), ['period' => 'month'])) }}" 
                   class="expense-pill-btn {{ $period === 'month' ? 'active' : '' }}">
                    Ce Mois-ci
                </a>
                <a href="{{ route('expenses.index', array_merge(request()->except('period', 'start_date', 'end_date', 'page'), ['period' => 'last_month'])) }}" 
                   class="expense-pill-btn {{ $period === 'last_month' ? 'active' : '' }}">
                    Mois Dernier
                </a>
                <a href="{{ route('expenses.index', array_merge(request()->except('period', 'start_date', 'end_date', 'page'), ['period' => 'year'])) }}" 
                   class="expense-pill-btn {{ $period === 'year' ? 'active' : '' }}">
                    Cette Année ({{ date('Y') }})
                </a>
            </div>

            <!-- Filters Form -->
            <form method="GET" action="{{ route('expenses.index') }}" class="row g-2 align-items-center">
                <input type="hidden" name="period" value="{{ $period }}">

                <!-- Search Input -->
                <div class="col-12 col-md-3">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search" value="{{ $search }}" class="form-control bg-light border-start-0" placeholder="Rechercher titre, réf, fournisseur...">
                    </div>
                </div>

                <!-- Category Filter -->
                <div class="col-6 col-md-2">
                    <select name="category" class="form-select form-select-sm bg-light" onchange="this.form.submit()">
                        <option value="">Toutes Catégories</option>
                        @foreach($categories as $key => $meta)
                            <option value="{{ $key }}" {{ $category === $key ? 'selected' : '' }}>{{ $meta['label'] }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Store Filter -->
                <div class="col-6 col-md-2">
                    <select name="store_id" class="form-select form-select-sm bg-light" onchange="this.form.submit()">
                        <option value="">Toutes Boutiques</option>
                        @foreach($stores as $st)
                            <option value="{{ $st->id }}" {{ $storeId == $st->id ? 'selected' : '' }}>{{ $st->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Payment Method Filter -->
                <div class="col-6 col-md-2">
                    <select name="payment_method" class="form-select form-select-sm bg-light" onchange="this.form.submit()">
                        <option value="">Tous Règlements</option>
                        @foreach($paymentMethods as $mKey => $mMeta)
                            <option value="{{ $mKey }}" {{ $paymentMethod === $mKey ? 'selected' : '' }}>{{ $mMeta['label'] }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Custom Date Range Form if Custom -->
                <div class="col-6 col-md-2 d-flex gap-1">
                    <button type="submit" class="btn btn-sm btn-dark w-100 fw-semibold rounded-3">Filtrer</button>
                    @if($search || $category || $storeId || $paymentMethod || $period !== 'month')
                        <a href="{{ route('expenses.index') }}" class="btn btn-sm btn-light border rounded-3" title="Réinitialiser">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Expenses Matrix -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        
        <!-- Table Header Bar -->
        <div class="p-3 border-bottom bg-white d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <h6 class="fw-bold mb-0 text-dark">Livre des Dépenses ({{ $expenses->total() }} enregistrements)</h6>
                <span class="text-muted small">Période du {{ $startDate->translatedFormat('d M Y') }} au {{ $endDate->translatedFormat('d M Y') }}</span>
            </div>
            <div class="fw-bold text-danger fs-6">
                Total : {{ currency($periodExpenses) }}
            </div>
        </div>

        <!-- 1. DESKTOP VIEW (d-none d-lg-block) -->
        <div class="table-responsive d-none d-lg-block">
            <table class="table align-middle mb-0">
                <thead class="bg-light border-bottom">
                    <tr>
                        <th class="ps-3 py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.72rem; width: 12%;">Date & Réf</th>
                        <th class="py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.72rem; width: 28%;">Intitulé & Bénéficiaire</th>
                        <th class="py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.72rem; width: 16%;">Catégorie</th>
                        <th class="py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.72rem; width: 14%;">Règlement</th>
                        <th class="py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.72rem; width: 10%;">Preuve</th>
                        <th class="py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.72rem; width: 12%;">Montant</th>
                        <th class="pe-3 py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.72rem; width: 8%;">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($expenses as $e)
                    <tr class="hover-bg-light">
                        <!-- Date & Ref -->
                        <td class="ps-3 py-3">
                            <div class="fw-bold text-dark" style="font-size: 0.85rem;">
                                {{ $e->expense_date->format('d/m/Y') }}
                            </div>
                            <div class="text-muted font-monospace" style="font-size: 0.7rem;">
                                {{ $e->expense_number }}
                            </div>
                        </td>

                        <!-- Title & Payee -->
                        <td class="py-3">
                            <div class="fw-bold text-dark" style="font-size: 0.9rem;">
                                {{ $e->title }}
                            </div>
                            <div class="d-flex align-items-center gap-1.5 text-muted small mt-0.5 flex-wrap" style="font-size: 0.72rem;">
                                @if($e->payee)
                                    <span><i class="fas fa-user-tag me-0.5 opacity-50"></i>{{ $e->payee }}</span>
                                @endif
                                @if($e->reference_number)
                                    <span>• Réf: <span class="font-monospace">{{ $e->reference_number }}</span></span>
                                @endif
                                @if($e->store)
                                    <span>• <span class="badge bg-light text-secondary border">{{ $e->store->name }}</span></span>
                                @else
                                    <span>• <span class="badge bg-light text-muted border">Siège / Général</span></span>
                                @endif
                            </div>
                        </td>

                        <!-- Category -->
                        <td class="py-3">
                            <span class="badge {{ $e->category_badge_class }} rounded-pill px-2.5 py-1 fw-semibold" style="font-size: 0.74rem;">
                                <i class="{{ $e->category_icon }} me-1"></i> {{ $e->category_label }}
                            </span>
                        </td>

                        <!-- Payment Method -->
                        <td class="py-3">
                            <div class="small fw-semibold text-dark">
                                <i class="{{ $e->payment_method_icon }} text-secondary me-1"></i> {{ $e->payment_method_label }}
                            </div>
                            <div class="text-muted" style="font-size: 0.68rem;">
                                {{ $e->status === 'paid' ? 'Payé' : 'En attente' }}
                            </div>
                        </td>

                        <!-- Proof Image -->
                        <td class="py-3">
                            @if($e->receipt_url)
                                <img src="{{ $e->receipt_url }}" alt="Preuve" class="proof-thumbnail-btn shadow-xs"
                                     onclick="previewProofModal('{{ $e->receipt_url }}', '{{ $e->expense_number }}', '{{ addslashes($e->title) }}')"
                                     title="Cliquer pour agrandir le justificatif">
                            @else
                                <span class="text-muted small fst-italic" style="font-size: 0.72rem;">—</span>
                            @endif
                        </td>

                        <!-- Amount -->
                        <td class="py-3 text-end">
                            <div class="fw-bold fs-6 text-danger">
                                -{{ currency($e->amount) }}
                            </div>
                        </td>

                        <!-- Actions -->
                        <td class="pe-3 py-3 text-end text-nowrap">
                            <div class="d-inline-flex align-items-center gap-1.5">
                                <button type="button" class="btn btn-sm btn-light border rounded-circle text-secondary d-inline-flex align-items-center justify-content-center"
                                        style="width: 30px; height: 30px; padding: 0;"
                                        onclick="openEditExpenseModal({{ json_encode($e) }})"
                                        title="Modifier la dépense">
                                    <i class="fas fa-pencil" style="font-size: 0.72rem;"></i>
                                </button>
                                <form method="POST" action="{{ route('expenses.destroy', $e->id) }}" onsubmit="return confirm('Confirmer la suppression de cette dépense ({{ $e->expense_number }}) ?');" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light border rounded-circle text-danger d-inline-flex align-items-center justify-content-center"
                                            style="width: 30px; height: 30px; padding: 0;"
                                            title="Supprimer">
                                        <i class="fas fa-trash" style="font-size: 0.72rem;"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fas fa-receipt fa-3x mb-3 opacity-25"></i>
                            <p class="mb-0 fw-semibold">Aucune dépense enregistrée sur cette période.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- 2. MOBILE VIEW (d-block d-lg-none) -->
        <div class="d-block d-lg-none p-3 space-y-3 bg-light bg-opacity-50">
            @forelse($expenses as $e)
            <div class="expense-mobile-card p-3 bg-white">
                <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                    <div class="min-w-0">
                        <div class="d-flex align-items-center gap-1.5 mb-1 flex-wrap">
                            <span class="badge {{ $e->category_badge_class }} rounded-pill" style="font-size: 0.68rem;">
                                <i class="{{ $e->category_icon }} me-1"></i> {{ $e->category_label }}
                            </span>
                            <span class="badge bg-light text-secondary border font-monospace" style="font-size: 0.65rem;">
                                {{ $e->expense_number }}
                            </span>
                        </div>
                        <div class="fw-bold text-dark" style="font-size: 0.95rem;">{{ $e->title }}</div>
                        <div class="text-muted small" style="font-size: 0.72rem;">
                            {{ $e->expense_date->format('d/m/Y') }}
                            @if($e->payee) • {{ $e->payee }} @endif
                            @if($e->store) • {{ $e->store->name }} @endif
                        </div>
                    </div>
                    <div class="text-end flex-shrink-0">
                        <div class="fw-bold fs-6 text-danger">-{{ currency($e->amount) }}</div>
                        <div class="text-muted small" style="font-size: 0.68rem;">{{ $e->payment_method_label }}</div>
                    </div>
                </div>

                @if($e->receipt_url)
                <div class="d-flex align-items-center gap-2 p-1.5 rounded-3 bg-light border mb-2">
                    <img src="{{ $e->receipt_url }}" alt="Preuve" class="proof-thumbnail-btn shadow-xs"
                         onclick="previewProofModal('{{ $e->receipt_url }}', '{{ $e->expense_number }}', '{{ addslashes($e->title) }}')">
                    <div class="small">
                        <span class="fw-semibold text-dark">Justificatif attaché</span>
                        <a href="javascript:void(0)" class="d-block text-primary text-decoration-none" style="font-size: 0.72rem;"
                           onclick="previewProofModal('{{ $e->receipt_url }}', '{{ $e->expense_number }}', '{{ addslashes($e->title) }}')">
                            Agrandir le document
                        </a>
                    </div>
                </div>
                @endif

                <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                    <span class="text-muted small" style="font-size: 0.72rem;">
                        {{ $e->status === 'paid' ? 'Payé' : 'En attente' }}
                    </span>
                    <div class="d-flex align-items-center gap-1.5">
                        <button type="button" class="btn btn-sm btn-light border rounded-pill px-2.5 py-1 text-secondary"
                                onclick="openEditExpenseModal({{ json_encode($e) }})">
                            <i class="fas fa-pencil me-1"></i> Modifier
                        </button>
                        <form method="POST" action="{{ route('expenses.destroy', $e->id) }}" onsubmit="return confirm('Supprimer cette dépense ?');" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-light border rounded-pill px-2.5 py-1 text-danger">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-5 text-muted">
                <p class="mb-0 fw-semibold">Aucune dépense trouvée.</p>
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($expenses->hasPages())
        <div class="p-3 border-top bg-light d-flex align-items-center justify-content-center">
            {{ $expenses->links() }}
        </div>
        @endif
    </div>

        </div> <!-- /#tab-ledger -->

        <!-- TAB 2: CHARGES RÉCURRENTES & MENSUELLES -->
        <div class="tab-pane fade {{ request('tab') === 'recurring' ? 'show active' : '' }}" id="tab-recurring" role="tabpanel">

            <!-- Recurring Overview & Quick Actions Card -->
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-3.5">
                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                        <div>
                            <h6 class="fw-bold mb-1 text-dark">
                                <i class="fas fa-repeat text-warning me-1.5"></i> Modèles de Charges Fixes & Dépenses Répétées
                            </h6>
                            <p class="text-muted small mb-0">
                                Configurez vos charges récurrentes (loyer, salaires, abonnements, syndic) <strong>une seule fois</strong>. Vous n'avez plus besoin de les recréer chaque mois.
                            </p>
                        </div>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <form method="POST" action="{{ route('expenses.recurring.generate-month') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-warning text-dark fw-bold rounded-pill px-3 py-2 shadow-xs">
                                    <i class="fas fa-wand-magic-sparkles me-1"></i> Générer ce mois ({{ now()->translatedFormat('M Y') }})
                                </button>
                            </form>
                            <button type="button" class="btn btn-sm btn-dark rounded-pill px-3.5 py-2 fw-semibold shadow-xs" data-bs-toggle="modal" data-bs-target="#newRecurringExpenseModal">
                                <i class="fas fa-plus me-1"></i> Nouvelle Charge Récurrente
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recurring Expenses Matrix -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="p-3 border-bottom bg-white d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <h6 class="fw-bold mb-0 text-dark">Liste des Modèles Récurrents ({{ $recurringCount }})</h6>
                    </div>
                    <div class="text-muted small">
                        Charges fixes actives : <strong class="text-danger">{{ currency($recurringMonthlyTotal) }}/mois</strong>
                    </div>
                </div>

                <!-- Desktop Recurring Table -->
                <div class="table-responsive d-none d-lg-block">
                    <table class="table align-middle mb-0">
                        <thead class="bg-light border-bottom">
                            <tr>
                                <th class="ps-3 py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.72rem; width: 25%;">Intitulé & Catégorie</th>
                                <th class="py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.72rem; width: 14%;">Montant & Fréquence</th>
                                <th class="py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.72rem; width: 14%;">Jour d'Échéance</th>
                                <th class="py-3 text-secondary text-uppercase fw-semibold" style="font-size: 0.72rem; width: 16%;">Boutique / Bénéficiaire</th>
                                <th class="py-3 text-secondary text-uppercase fw-semibold text-center" style="font-size: 0.72rem; width: 15%;">Ce Mois-ci</th>
                                <th class="pe-3 py-3 text-secondary text-uppercase fw-semibold text-end" style="font-size: 0.72rem; width: 16%;">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse($recurringExpenses as $rec)
                            @php
                                $isGen = $rec->isGeneratedForMonth(now());
                            @endphp
                            <tr class="hover-bg-light {{ !$rec->is_active ? 'opacity-75 bg-light' : '' }}">
                                <!-- Title & Category -->
                                <td class="ps-3 py-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="min-w-0">
                                            <div class="fw-bold text-dark" style="font-size: 0.9rem;">
                                                {{ $rec->title }}
                                                @if(!$rec->is_active)
                                                    <span class="badge bg-secondary-subtle text-secondary rounded-pill ms-1" style="font-size: 0.65rem;">En pause</span>
                                                @endif
                                            </div>
                                            <div class="d-flex align-items-center gap-1.5 mt-0.5">
                                                <span class="badge {{ $rec->category_badge_class }} rounded-pill" style="font-size: 0.68rem;">
                                                    <i class="{{ $rec->category_icon }} me-1"></i> {{ $rec->category_label }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Amount & Frequency -->
                                <td class="py-3 text-end">
                                    <div class="fw-bold fs-6 text-danger">
                                        {{ currency($rec->amount) }}
                                    </div>
                                    <div class="text-muted small" style="font-size: 0.72rem;">
                                        {{ $rec->frequency_label }}
                                    </div>
                                </td>

                                <!-- Day of month -->
                                <td class="py-3">
                                    <div class="fw-semibold text-dark small">
                                        <i class="fas fa-calendar-day text-secondary me-1"></i>
                                        Le {{ $rec->day_of_month }}{{ $rec->day_of_month == 1 ? 'er' : '' }} du mois
                                    </div>
                                    <div class="text-muted" style="font-size: 0.7rem;">
                                        Début : {{ $rec->start_date ? $rec->start_date->format('d/m/Y') : '—' }}
                                    </div>
                                </td>

                                <!-- Store / Payee -->
                                <td class="py-3">
                                    <div class="small fw-semibold text-dark">
                                        {{ $rec->store->name ?? 'Siège / Général' }}
                                    </div>
                                    <div class="text-muted text-truncate" style="font-size: 0.72rem; max-width: 180px;">
                                        {{ $rec->payee ?? '—' }}
                                    </div>
                                </td>

                                <!-- Generated this month status -->
                                <td class="py-3 text-center">
                                    @if($isGen)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1" style="font-size: 0.72rem;">
                                            <i class="fas fa-circle-check me-1"></i> Généré ce mois
                                        </span>
                                    @elseif(!$rec->is_active)
                                        <span class="badge bg-light text-muted border rounded-pill px-2.5 py-1" style="font-size: 0.72rem;">
                                            Désactivé
                                        </span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2.5 py-1" style="font-size: 0.72rem;">
                                            <i class="fas fa-hourglass-half me-1"></i> En attente
                                        </span>
                                    @endif
                                </td>

                                <!-- Actions -->
                                <td class="pe-3 py-3 text-end text-nowrap">
                                    <div class="d-inline-flex align-items-center gap-1.5">
                                        @if(!$isGen && $rec->is_active)
                                        <form method="POST" action="{{ route('expenses.recurring.generate-month') }}" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="recurring_expense_id" value="{{ $rec->id }}">
                                            <button type="submit" class="btn btn-sm btn-warning text-dark fw-bold rounded-pill px-2 py-1 shadow-xs" style="font-size: 0.72rem;" title="Générer l'écriture pour ce mois-ci">
                                                <i class="fas fa-bolt me-1"></i> Générer
                                            </button>
                                        </form>
                                        @endif

                                        <!-- Toggle Pause/Active -->
                                        <form method="POST" action="{{ route('expenses.recurring.toggle', $rec->id) }}" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-light border rounded-circle {{ $rec->is_active ? 'text-secondary' : 'text-success' }} d-inline-flex align-items-center justify-content-center"
                                                    style="width: 28px; height: 28px; padding: 0;"
                                                    title="{{ $rec->is_active ? 'Mettre en pause' : 'Réactiver' }}">
                                                <i class="fas {{ $rec->is_active ? 'fa-pause' : 'fa-play' }}" style="font-size: 0.68rem;"></i>
                                            </button>
                                        </form>

                                        <!-- Edit Modal -->
                                        <button type="button" class="btn btn-sm btn-light border rounded-circle text-secondary d-inline-flex align-items-center justify-content-center"
                                                style="width: 28px; height: 28px; padding: 0;"
                                                onclick="openEditRecurringModal({{ json_encode($rec) }})"
                                                title="Modifier le modèle">
                                            <i class="fas fa-pencil" style="font-size: 0.68rem;"></i>
                                        </button>

                                        <!-- Delete -->
                                        <form method="POST" action="{{ route('expenses.recurring.destroy', $rec->id) }}" onsubmit="return confirm('Supprimer définitivement ce modèle récurrent ?');" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-light border rounded-circle text-danger d-inline-flex align-items-center justify-content-center"
                                                    style="width: 28px; height: 28px; padding: 0;"
                                                    title="Supprimer">
                                                <i class="fas fa-trash" style="font-size: 0.68rem;"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fas fa-repeat fa-3x mb-3 opacity-25"></i>
                                    <p class="mb-2 fw-semibold">Aucune charge récurrente configurée.</p>
                                    <button type="button" class="btn btn-sm btn-warning text-dark fw-bold rounded-pill px-3 py-1.5" data-bs-toggle="modal" data-bs-target="#newRecurringExpenseModal">
                                        <i class="fas fa-plus me-1"></i> Créer votre première charge mensuelle (Loyer, Salaire...)
                                    </button>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Recurring Cards -->
                <div class="d-block d-lg-none p-3 space-y-3 bg-light bg-opacity-50">
                    @forelse($recurringExpenses as $rec)
                    @php $isGen = $rec->isGeneratedForMonth(now()); @endphp
                    <div class="expense-mobile-card p-3 bg-white">
                        <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                            <div>
                                <span class="badge {{ $rec->category_badge_class }} rounded-pill" style="font-size: 0.68rem;">
                                    <i class="{{ $rec->category_icon }} me-1"></i> {{ $rec->category_label }}
                                </span>
                                <div class="fw-bold text-dark mt-1" style="font-size: 0.95rem;">{{ $rec->title }}</div>
                                <div class="text-muted small" style="font-size: 0.72rem;">
                                    Le {{ $rec->day_of_month }} du mois • {{ $rec->store->name ?? 'Général' }}
                                </div>
                            </div>
                            <div class="text-end flex-shrink-0">
                                <div class="fw-bold fs-6 text-danger">{{ currency($rec->amount) }}</div>
                                <div class="text-muted small" style="font-size: 0.68rem;">{{ $rec->frequency_label }}</div>
                            </div>
                        </div>

                        <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                            <div>
                                @if($isGen)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0.5" style="font-size: 0.68rem;">
                                        ✓ Généré
                                    </span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2 py-0.5" style="font-size: 0.68rem;">
                                        ⏳ En attente
                                    </span>
                                @endif
                            </div>
                            <div class="d-flex align-items-center gap-1.5">
                                @if(!$isGen && $rec->is_active)
                                <form method="POST" action="{{ route('expenses.recurring.generate-month') }}" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="recurring_expense_id" value="{{ $rec->id }}">
                                    <button type="submit" class="btn btn-sm btn-warning text-dark fw-bold rounded-pill px-2.5 py-1" style="font-size: 0.72rem;">
                                        <i class="fas fa-bolt"></i> Générer
                                    </button>
                                </form>
                                @endif
                                <button type="button" class="btn btn-sm btn-light border rounded-pill px-2.5 py-1 text-secondary"
                                        onclick="openEditRecurringModal({{ json_encode($rec) }})">
                                    <i class="fas fa-pencil"></i>
                                </button>
                                <form method="POST" action="{{ route('expenses.recurring.destroy', $rec->id) }}" onsubmit="return confirm('Supprimer ?');" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light border rounded-pill px-2.5 py-1 text-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4 text-muted small">
                        Aucune charge récurrente.
                    </div>
                    @endforelse
                </div>
            </div>

        </div> <!-- /#tab-recurring -->

    </div> <!-- /#expensesTabsContent -->

</div>

<!-- MODAL: NOUVELLE DÉPENSE -->
<div class="modal fade" id="newExpenseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form method="POST" action="{{ route('expenses.store') }}" enctype="multipart/form-data" class="modal-content border-0 shadow-lg rounded-4">
            @csrf
            <div class="modal-header bg-dark text-white py-3 px-4 border-0">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-danger bg-opacity-25 border border-danger border-opacity-50 text-white d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                        <i class="fas fa-plus text-white"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold mb-0">Enregistrer une Nouvelle Dépense</h6>
                        <span class="text-white-50 small">Déduite automatiquement du bénéfice net global</span>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>

            <div class="modal-body p-4">
                <div class="row g-3">
                    <!-- Title -->
                    <div class="col-12 col-md-8">
                        <label class="form-label small fw-bold text-dark mb-1">Intitulé / Objet de la dépense <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control rounded-3" placeholder="Ex: Loyer Magasin Casablanca, Facture Facebook Ads, etc." required>
                    </div>

                    <!-- Category -->
                    <div class="col-12 col-md-4">
                        <label class="form-label small fw-bold text-dark mb-1">Catégorie <span class="text-danger">*</span></label>
                        <select name="category" class="form-select rounded-3" required>
                            @foreach($categories as $key => $meta)
                                <option value="{{ $key }}">{{ $meta['label'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Amount -->
                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-bold text-dark mb-1">Montant (DH) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" step="0.01" min="0.01" name="amount" class="form-control rounded-start-3 fw-bold fs-6" placeholder="0.00" required>
                            <span class="input-group-text bg-light fw-bold text-secondary">DH</span>
                        </div>
                    </div>

                    <!-- Date -->
                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-bold text-dark mb-1">Date de la Dépense <span class="text-danger">*</span></label>
                        <input type="date" name="expense_date" value="{{ date('Y-m-d') }}" class="form-control rounded-3" required>
                    </div>

                    <!-- Payment Method -->
                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-bold text-dark mb-1">Mode de Règlement <span class="text-danger">*</span></label>
                        <select name="payment_method" class="form-select rounded-3" required>
                            @foreach($paymentMethods as $mKey => $mMeta)
                                <option value="{{ $mKey }}">{{ $mMeta['label'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Store / Boutique -->
                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-bold text-dark mb-1">Boutique / Lieu concerné</label>
                        <select name="store_id" class="form-select rounded-3">
                            <option value="">Général / Siège de l'entreprise</option>
                            @foreach($stores as $st)
                                <option value="{{ $st->id }}">{{ $st->name }} ({{ $st->city }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Payee / Vendor -->
                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-bold text-dark mb-1">Bénéficiaire / Fournisseur</label>
                        <input type="text" name="payee" class="form-control rounded-3" placeholder="Ex: Propriétaire local, Meta Ads, Maroc Telecom...">
                    </div>

                    <!-- Reference Number -->
                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-bold text-dark mb-1">N° de Facture / Réf Chèque</label>
                        <input type="text" name="reference_number" class="form-control rounded-3" placeholder="Ex: FAC-2026-88, CHQ-48201...">
                    </div>

                    <!-- Status -->
                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-bold text-dark mb-1">Statut du Paiement <span class="text-danger">*</span></label>
                        <select name="status" class="form-select rounded-3" required>
                            <option value="paid" selected>Payé / Réglé</option>
                            <option value="pending">En attente de paiement</option>
                        </select>
                    </div>

                    <!-- Receipt Image Upload -->
                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-bold text-dark mb-1">Justificatif / Reçu Photo</label>
                        <input type="file" name="receipt_image" accept="image/*" class="form-control rounded-3">
                        <div class="form-text" style="font-size: 0.72rem;">Photo du reçu, facturette ou bordereau (JPEG, PNG).</div>
                    </div>

                    <!-- Notes -->
                    <div class="col-12">
                        <label class="form-label small fw-bold text-dark mb-1">Notes / Détails</label>
                        <textarea name="notes" rows="2" class="form-control rounded-3" placeholder="Commentaires ou précisions éventuelles..."></textarea>
                    </div>
                </div>
            </div>

            <div class="modal-footer bg-light px-4 py-2.5 border-0">
                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" class="btn btn-sm btn-danger rounded-pill px-4 fw-semibold">
                    <i class="fas fa-check me-1"></i> Enregistrer la Dépense
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: MODIFIER DÉPENSE -->
<div class="modal fade" id="editExpenseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form method="POST" id="editExpenseForm" action="" enctype="multipart/form-data" class="modal-content border-0 shadow-lg rounded-4">
            @csrf
            @method('PUT')
            <div class="modal-header bg-dark text-white py-3 px-4 border-0">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-pencil text-warning"></i>
                    <div>
                        <h6 class="modal-title fw-bold mb-0">Modifier la Dépense</h6>
                        <span class="text-white-50 small" id="editExpenseNumber">—</span>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>

            <div class="modal-body p-4">
                <div class="row g-3">
                    <div class="col-12 col-md-8">
                        <label class="form-label small fw-bold text-dark mb-1">Intitulé <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="editTitle" class="form-control rounded-3" required>
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label small fw-bold text-dark mb-1">Catégorie <span class="text-danger">*</span></label>
                        <select name="category" id="editCategory" class="form-select rounded-3" required>
                            @foreach($categories as $key => $meta)
                                <option value="{{ $key }}">{{ $meta['label'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-bold text-dark mb-1">Montant (DH) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" step="0.01" min="0.01" name="amount" id="editAmount" class="form-control rounded-start-3 fw-bold" required>
                            <span class="input-group-text bg-light fw-bold text-secondary">DH</span>
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-bold text-dark mb-1">Date <span class="text-danger">*</span></label>
                        <input type="date" name="expense_date" id="editDate" class="form-control rounded-3" required>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-bold text-dark mb-1">Mode de Règlement <span class="text-danger">*</span></label>
                        <select name="payment_method" id="editPaymentMethod" class="form-select rounded-3" required>
                            @foreach($paymentMethods as $mKey => $mMeta)
                                <option value="{{ $mKey }}">{{ $mMeta['label'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-bold text-dark mb-1">Boutique</label>
                        <select name="store_id" id="editStoreId" class="form-select rounded-3">
                            <option value="">Général / Siège</option>
                            @foreach($stores as $st)
                                <option value="{{ $st->id }}">{{ $st->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-bold text-dark mb-1">Bénéficiaire / Fournisseur</label>
                        <input type="text" name="payee" id="editPayee" class="form-control rounded-3">
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-bold text-dark mb-1">N° de Facture / Réf</label>
                        <input type="text" name="reference_number" id="editReferenceNumber" class="form-control rounded-3">
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-bold text-dark mb-1">Statut <span class="text-danger">*</span></label>
                        <select name="status" id="editStatus" class="form-select rounded-3" required>
                            <option value="paid">Payé</option>
                            <option value="pending">En attente</option>
                        </select>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-bold text-dark mb-1">Remplacer le Justificatif</label>
                        <input type="file" name="receipt_image" accept="image/*" class="form-control rounded-3">
                    </div>

                    <div class="col-12">
                        <label class="form-label small fw-bold text-dark mb-1">Notes</label>
                        <textarea name="notes" id="editNotes" rows="2" class="form-control rounded-3"></textarea>
                    </div>
                </div>
            </div>

            <div class="modal-footer bg-light px-4 py-2.5 border-0">
                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" class="btn btn-sm btn-primary rounded-pill px-4 fw-semibold">
                    <i class="fas fa-save me-1"></i> Mettre à Jour
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: APERÇU DU JUSTIFICATIF -->
<div class="modal fade" id="previewProofModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-dark text-white py-3 px-4 border-0">
                <div>
                    <h6 class="modal-title fw-bold mb-0" id="proofModalTitle">Justificatif de Dépense</h6>
                    <span class="text-white-50 small" id="proofModalRef">—</span>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body p-0 text-center bg-light">
                <img src="" id="proofModalImage" class="img-fluid" style="max-height: 80vh; object-fit: contain;" alt="Justificatif">
            </div>
            <div class="modal-footer bg-white px-4 py-2.5 border-0 d-flex justify-content-between">
                <a href="#" id="proofModalDownload" target="_blank" class="btn btn-sm btn-outline-dark rounded-pill px-3">
                    <i class="fas fa-arrow-down me-1"></i> Ouvrir en plein écran
                </a>
                <button type="button" class="btn btn-sm btn-dark rounded-pill px-4" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: NOUVELLE CHARGE RÉCURRENTE MENSUELLE -->
<div class="modal fade" id="newRecurringExpenseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form method="POST" action="{{ route('expenses.recurring.store') }}" class="modal-content border-0 shadow-lg rounded-4">
            @csrf
            <div class="modal-header bg-dark text-white py-3 px-4 border-0">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-warning text-dark d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                        <i class="fas fa-repeat"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold mb-0">Nouvelle Charge Récurrente Fixe</h6>
                        <span class="text-white-50 small">Créez-la une fois, elle se générera chaque mois sans ressaisie</span>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>

            <div class="modal-body p-4">
                <div class="row g-3">
                    <!-- Title -->
                    <div class="col-12 col-md-8">
                        <label class="form-label small fw-bold text-dark mb-1">Intitulé de la charge fixe <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control rounded-3" placeholder="Ex: Loyer Magasin, Salaire Employé X, Fibre Internet..." required>
                    </div>

                    <!-- Category -->
                    <div class="col-12 col-md-4">
                        <label class="form-label small fw-bold text-dark mb-1">Catégorie <span class="text-danger">*</span></label>
                        <select name="category" class="form-select rounded-3" required>
                            @foreach($categories as $key => $meta)
                                <option value="{{ $key }}" {{ $key === 'rent' ? 'selected' : '' }}>{{ $meta['label'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Amount -->
                    <div class="col-12 col-md-4">
                        <label class="form-label small fw-bold text-dark mb-1">Montant récurrent (DH) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" step="0.01" min="0.01" name="amount" class="form-control rounded-start-3 fw-bold fs-6" placeholder="0.00" required>
                            <span class="input-group-text bg-light fw-bold text-secondary">DH</span>
                        </div>
                    </div>

                    <!-- Frequency -->
                    <div class="col-12 col-md-4">
                        <label class="form-label small fw-bold text-dark mb-1">Fréquence de répétition <span class="text-danger">*</span></label>
                        <select name="frequency" class="form-select rounded-3" required>
                            <option value="monthly" selected>Mensuelle (Chaque mois)</option>
                            <option value="quarterly">Trimestrielle (Tous les 3 mois)</option>
                            <option value="yearly">Annuelle (Tous les ans)</option>
                            <option value="weekly">Hebdomadaire (Chaque semaine)</option>
                        </select>
                    </div>

                    <!-- Day of month -->
                    <div class="col-12 col-md-4">
                        <label class="form-label small fw-bold text-dark mb-1">Jour du mois d'échéance <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fas fa-calendar-day text-muted"></i></span>
                            <input type="number" name="day_of_month" min="1" max="31" value="1" class="form-control rounded-end-3" required>
                        </div>
                        <div class="form-text" style="font-size: 0.72rem;">Ex: 1 (le 1er du mois), 5 (le 5 du mois)...</div>
                    </div>

                    <!-- Start Date -->
                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-bold text-dark mb-1">Date de début d'application <span class="text-danger">*</span></label>
                        <input type="date" name="start_date" value="{{ date('Y-m-01') }}" class="form-control rounded-3" required>
                    </div>

                    <!-- End Date (Optional) -->
                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-bold text-dark mb-1">Date de fin (Optionnel)</label>
                        <input type="date" name="end_date" class="form-control rounded-3" placeholder="Laisser vide si permanent">
                    </div>

                    <!-- Payment Method -->
                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-bold text-dark mb-1">Mode de Règlement habituel <span class="text-danger">*</span></label>
                        <select name="payment_method" class="form-select rounded-3" required>
                            @foreach($paymentMethods as $mKey => $mMeta)
                                <option value="{{ $mKey }}" {{ $mKey === 'bank_transfer' ? 'selected' : '' }}>{{ $mMeta['label'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Store / Boutique -->
                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-bold text-dark mb-1">Boutique / Lieu concerné</label>
                        <select name="store_id" class="form-select rounded-3">
                            <option value="">Général / Siège de l'entreprise</option>
                            @foreach($stores as $st)
                                <option value="{{ $st->id }}">{{ $st->name }} ({{ $st->city }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Payee / Vendor -->
                    <div class="col-12">
                        <label class="form-label small fw-bold text-dark mb-1">Bénéficiaire / Propriétaire / Prestataire</label>
                        <input type="text" name="payee" class="form-control rounded-3" placeholder="Ex: Bailleur local, Maroc Telecom, Nom employé...">
                    </div>

                    <!-- Notes -->
                    <div class="col-12">
                        <label class="form-label small fw-bold text-dark mb-1">Notes / Référence du contrat</label>
                        <textarea name="notes" rows="2" class="form-control rounded-3" placeholder="Ex: Bail commercial contrat N°12, prélèvement automatique..."></textarea>
                    </div>

                    <!-- Immediate Generation Checkbox -->
                    <div class="col-12">
                        <div class="p-3 rounded-3 bg-warning bg-opacity-10 border border-warning border-opacity-25 d-flex align-items-center gap-2">
                            <input class="form-check-input mt-0 flex-shrink-0" type="checkbox" name="generate_now" value="1" id="recGenerateNow" checked>
                            <label class="form-check-label small fw-semibold text-dark" for="recGenerateNow">
                                <strong>Générer immédiatement l'écriture pour ce mois-ci ({{ now()->translatedFormat('F Y') }})</strong>
                                <span class="d-block text-muted" style="font-size: 0.72rem;">Créera automatiquement la ligne dans le Grand Livre et l'intégrera aux calculs de rentabilité.</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer bg-light px-4 py-2.5 border-0">
                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" class="btn btn-sm btn-warning text-dark fw-bold rounded-pill px-4">
                    <i class="fas fa-check me-1"></i> Créer la Charge Récurrente
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: MODIFIER CHARGE RÉCURRENTE -->
<div class="modal fade" id="editRecurringExpenseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form method="POST" id="editRecurringForm" action="" class="modal-content border-0 shadow-lg rounded-4">
            @csrf
            @method('PUT')
            <div class="modal-header bg-dark text-white py-3 px-4 border-0">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-pencil text-warning"></i>
                    <div>
                        <h6 class="modal-title fw-bold mb-0">Modifier la Charge Récurrente</h6>
                        <span class="text-white-50 small" id="editRecurringTitleHeader">—</span>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>

            <div class="modal-body p-4">
                <div class="row g-3">
                    <div class="col-12 col-md-8">
                        <label class="form-label small fw-bold text-dark mb-1">Intitulé <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="editRecTitle" class="form-control rounded-3" required>
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label small fw-bold text-dark mb-1">Catégorie <span class="text-danger">*</span></label>
                        <select name="category" id="editRecCategory" class="form-select rounded-3" required>
                            @foreach($categories as $key => $meta)
                                <option value="{{ $key }}">{{ $meta['label'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label small fw-bold text-dark mb-1">Montant (DH) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" step="0.01" min="0.01" name="amount" id="editRecAmount" class="form-control rounded-start-3 fw-bold" required>
                            <span class="input-group-text bg-light fw-bold text-secondary">DH</span>
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label small fw-bold text-dark mb-1">Fréquence <span class="text-danger">*</span></label>
                        <select name="frequency" id="editRecFrequency" class="form-select rounded-3" required>
                            <option value="monthly">Mensuelle (Chaque mois)</option>
                            <option value="quarterly">Trimestrielle (Tous les 3 mois)</option>
                            <option value="yearly">Annuelle (Tous les ans)</option>
                            <option value="weekly">Hebdomadaire (Chaque semaine)</option>
                        </select>
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label small fw-bold text-dark mb-1">Jour d'échéance <span class="text-danger">*</span></label>
                        <input type="number" name="day_of_month" id="editRecDayOfMonth" min="1" max="31" class="form-control rounded-3" required>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-bold text-dark mb-1">Date de début <span class="text-danger">*</span></label>
                        <input type="date" name="start_date" id="editRecStartDate" class="form-control rounded-3" required>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-bold text-dark mb-1">Date de fin</label>
                        <input type="date" name="end_date" id="editRecEndDate" class="form-control rounded-3">
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-bold text-dark mb-1">Règlement <span class="text-danger">*</span></label>
                        <select name="payment_method" id="editRecPaymentMethod" class="form-select rounded-3" required>
                            @foreach($paymentMethods as $mKey => $mMeta)
                                <option value="{{ $mKey }}">{{ $mMeta['label'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-bold text-dark mb-1">Boutique</label>
                        <select name="store_id" id="editRecStoreId" class="form-select rounded-3">
                            <option value="">Général / Siège</option>
                            @foreach($stores as $st)
                                <option value="{{ $st->id }}">{{ $st->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 col-md-8">
                        <label class="form-label small fw-bold text-dark mb-1">Bénéficiaire</label>
                        <input type="text" name="payee" id="editRecPayee" class="form-control rounded-3">
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label small fw-bold text-dark mb-1">Statut</label>
                        <select name="is_active" id="editRecIsActive" class="form-select rounded-3">
                            <option value="1">Actif (En service)</option>
                            <option value="0">En pause (Désactivé)</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label small fw-bold text-dark mb-1">Notes</label>
                        <textarea name="notes" id="editRecNotes" rows="2" class="form-control rounded-3"></textarea>
                    </div>
                </div>
            </div>

            <div class="modal-footer bg-light px-4 py-2.5 border-0">
                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" class="btn btn-sm btn-primary rounded-pill px-4 fw-semibold">
                    <i class="fas fa-save me-1"></i> Enregistrer les Modifications
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openEditRecurringModal(rec) {
    document.getElementById('editRecurringForm').action = `/expenses/recurring/${rec.id}`;
    document.getElementById('editRecurringTitleHeader').textContent = rec.title;
    document.getElementById('editRecTitle').value = rec.title || '';
    document.getElementById('editRecCategory').value = rec.category || 'other';
    document.getElementById('editRecAmount').value = rec.amount || '';
    document.getElementById('editRecFrequency').value = rec.frequency || 'monthly';
    document.getElementById('editRecDayOfMonth').value = rec.day_of_month || 1;
    
    if (rec.start_date) {
        const d = new Date(rec.start_date);
        document.getElementById('editRecStartDate').value = d.toISOString().split('T')[0];
    }
    if (rec.end_date) {
        const d = new Date(rec.end_date);
        document.getElementById('editRecEndDate').value = d.toISOString().split('T')[0];
    } else {
        document.getElementById('editRecEndDate').value = '';
    }

    document.getElementById('editRecPaymentMethod').value = rec.payment_method || 'bank_transfer';
    document.getElementById('editRecStoreId').value = rec.store_id || '';
    document.getElementById('editRecPayee').value = rec.payee || '';
    document.getElementById('editRecIsActive').value = rec.is_active ? '1' : '0';
    document.getElementById('editRecNotes').value = rec.notes || '';

    const modal = new bootstrap.Modal(document.getElementById('editRecurringExpenseModal'));
    modal.show();
}
function openEditExpenseModal(expense) {
    document.getElementById('editExpenseForm').action = `/expenses/${expense.id}`;
    document.getElementById('editExpenseNumber').textContent = expense.expense_number;
    document.getElementById('editTitle').value = expense.title || '';
    document.getElementById('editCategory').value = expense.category || 'other';
    document.getElementById('editAmount').value = expense.amount || '';
    
    // Format date YYYY-MM-DD
    if (expense.expense_date) {
        const d = new Date(expense.expense_date);
        document.getElementById('editDate').value = d.toISOString().split('T')[0];
    }

    document.getElementById('editPaymentMethod').value = expense.payment_method || 'cash';
    document.getElementById('editStoreId').value = expense.store_id || '';
    document.getElementById('editPayee').value = expense.payee || '';
    document.getElementById('editReferenceNumber').value = expense.reference_number || '';
    document.getElementById('editStatus').value = expense.status || 'paid';
    document.getElementById('editNotes').value = expense.notes || '';

    const modal = new bootstrap.Modal(document.getElementById('editExpenseModal'));
    modal.show();
}

function previewProofModal(imageUrl, refNumber, title) {
    document.getElementById('proofModalImage').src = imageUrl;
    document.getElementById('proofModalTitle').textContent = title;
    document.getElementById('proofModalRef').textContent = refNumber;
    document.getElementById('proofModalDownload').href = imageUrl;

    const modal = new bootstrap.Modal(document.getElementById('previewProofModal'));
    modal.show();
}
</script>
@endpush
@endsection

