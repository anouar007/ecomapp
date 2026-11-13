@extends('layouts.app')

@section('title', 'Insights Business & Villes de Livraison')

@push('styles')
<style>
    .insight-kpi-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 1.1rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        position: relative;
        overflow: hidden;
        height: 100%;
    }
    .insight-kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.06);
    }
    .insight-kpi-card::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: var(--kpi-accent, #0ea5e9);
    }
    .kpi-sky { --kpi-accent: #0ea5e9; }
    .kpi-emerald { --kpi-accent: #10b981; }
    .kpi-indigo { --kpi-accent: #6366f1; }
    .kpi-amber { --kpi-accent: #f59e0b; }
    .kpi-rose { --kpi-accent: #f43f5e; }

    .insight-panel {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.03);
    }
    .insight-kpi-label {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #64748b;
    }
    .insight-kpi-value {
        font-size: 1.35rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.25;
        margin-top: 0.25rem;
    }
    .insight-kpi-icon {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
        flex-shrink: 0;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-3">
    <!-- En-tête -->
    <div class="brand-header mb-3">
        <div class="d-flex align-items-center gap-2.5">
            <div class="brand-header-icon flex-shrink-0">
                <i class="fas fa-map-location-dot"></i>
            </div>
            <div>
                <h1 class="brand-title">Insights Business &amp; Villes de Livraison</h1>
                <p class="brand-subtitle mb-0">
                    Période : <strong class="text-dark">{{ $periodLabel }}</strong>
                    <span class="text-muted">({{ \Carbon\Carbon::parse($startDateStr)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDateStr)->format('d/m/Y') }})</span>
                </p>
            </div>
        </div>
        <div class="brand-header-actions d-flex flex-wrap align-items-center gap-2">
            <a href="{{ route('analytics.insights.export', ['period' => $period, 'start_date' => $startDateStr, 'end_date' => $endDateStr]) }}"
               class="btn-brand-outline" data-no-spa="true">
                <i class="fas fa-download me-1.5 text-success"></i> <span>Exporter CSV</span>
            </a>
        </div>
    </div>

    @include('partials.subnav.analytics')

    <!-- Barre de filtres période & dates personnalisées -->
    <div class="insight-panel p-3 mb-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex flex-wrap align-items-center gap-1.5">
                @php
                    $periods = [
                        'today' => "Aujourd'hui",
                        'week' => '7 jours',
                        'month' => '30 jours',
                        'quarter' => '3 mois',
                        'year' => '12 mois',
                        'all' => 'Tout',
                    ];
                @endphp
                @foreach($periods as $pKey => $pLabel)
                    <a href="{{ route('analytics.insights', ['period' => $pKey]) }}"
                       class="btn btn-sm rounded-pill px-3 py-1.5 fw-semibold {{ $period === $pKey ? 'btn-primary text-white shadow-sm' : 'btn-light text-secondary border' }}">
                        {{ $pLabel }}
                    </a>
                @endforeach
            </div>

            <form action="{{ route('analytics.insights') }}" method="GET" class="d-flex flex-wrap align-items-center gap-2">
                <input type="hidden" name="period" value="custom">
                <div class="input-group input-group-sm" style="width: auto;">
                    <span class="input-group-text bg-light text-muted fw-semibold">Du</span>
                    <input type="date" name="start_date" value="{{ $startDateStr }}" class="form-control form-control-sm">
                </div>
                <div class="input-group input-group-sm" style="width: auto;">
                    <span class="input-group-text bg-light text-muted fw-semibold">Au</span>
                    <input type="date" name="end_date" value="{{ $endDateStr }}" class="form-control form-control-sm">
                </div>
                <button type="submit" class="btn btn-sm btn-dark rounded-pill px-3 fw-semibold">
                    <i class="fas fa-filter me-1"></i> Appliquer
                </button>
            </form>
        </div>
    </div>

    @php
        $summary = $insights['summary'];
        $topCity = $summary['top_city'];
        $retention = $insights['retention'];
        $timing = $insights['timing'];
    @endphp

    <!-- 5 KPI Cards -->
    <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-5 g-3 mb-4">
        <div class="col">
            <div class="insight-kpi-card kpi-sky">
                <div class="d-flex align-items-start justify-content-between gap-2">
                    <div class="insight-kpi-label">Villes Desservies</div>
                    <div class="insight-kpi-icon bg-info-subtle text-info"><i class="fas fa-city"></i></div>
                </div>
                <div class="insight-kpi-value">{{ $summary['total_cities'] }}</div>
                <div class="text-muted mt-1.5" style="font-size: 0.72rem;">
                    Sur {{ number_format($summary['total_orders'], 0, ',', ' ') }} commande(s)
                </div>
            </div>
        </div>

        <div class="col">
            <div class="insight-kpi-card kpi-emerald">
                <div class="d-flex align-items-start justify-content-between gap-2">
                    <div class="insight-kpi-label">Ville N°1</div>
                    <div class="insight-kpi-icon bg-success-subtle text-success"><i class="fas fa-trophy"></i></div>
                </div>
                <div class="insight-kpi-value text-truncate">{{ $topCity->city ?? '—' }}</div>
                <div class="text-muted mt-1.5" style="font-size: 0.72rem;">
                    @if($topCity)
                        <strong>{{ $topCity->orders_count }}</strong> cmd ({{ $topCity->revenue_share }}% du CA)
                    @else
                        Aucune donnée
                    @endif
                </div>
            </div>
        </div>

        <div class="col">
            <div class="insight-kpi-card kpi-indigo">
                <div class="d-flex align-items-start justify-content-between gap-2">
                    <div class="insight-kpi-label">Clients Fidèles</div>
                    <div class="insight-kpi-icon bg-primary-subtle text-primary"><i class="fas fa-repeat"></i></div>
                </div>
                <div class="insight-kpi-value">{{ $retention['repeat_rate'] }}%</div>
                <div class="text-muted mt-1.5" style="font-size: 0.72rem;">
                    {{ $retention['repeat_buyers'] }} récurrents / {{ $retention['unique_buyers'] }} acheteurs
                </div>
            </div>
        </div>

        <div class="col">
            <div class="insight-kpi-card kpi-amber">
                <div class="d-flex align-items-start justify-content-between gap-2">
                    <div class="insight-kpi-label">Pic d'Activité</div>
                    <div class="insight-kpi-icon bg-warning-subtle text-warning"><i class="fas fa-clock"></i></div>
                </div>
                <div class="insight-kpi-value">
                    {{ $timing['peak_day']['label'] ?? '—' }} &bull; {{ $timing['peak_hour']['label'] ?? '—' }}
                </div>
                <div class="text-muted mt-1.5" style="font-size: 0.72rem;">
                    Meilleur jour &amp; heure de commande
                </div>
            </div>
        </div>

        <div class="col">
            <div class="insight-kpi-card kpi-rose">
                <div class="d-flex align-items-start justify-content-between gap-2">
                    <div class="insight-kpi-label">Livraison &amp; Remises</div>
                    <div class="insight-kpi-icon bg-danger-subtle text-danger"><i class="fas fa-truck-fast"></i></div>
                </div>
                <div class="insight-kpi-value">{{ currency($summary['total_shipping']) }}</div>
                <div class="text-muted mt-1.5" style="font-size: 0.72rem;">
                    Remises accordées : <strong class="text-danger">-{{ currency($summary['total_discount']) }}</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques Villes & Heures de pointe -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-lg-6">
            <div class="insight-panel p-3 p-md-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="fw-bold text-dark mb-0"><i class="fas fa-map-pin text-info me-2"></i>Top 10 Villes de Livraison</h5>
                        <small class="text-muted">Nombre de commandes expédiées par ville</small>
                    </div>
                </div>
                <div style="height: 280px; position: relative;">
                    <canvas id="topCitiesChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="insight-panel p-3 p-md-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="fw-bold text-dark mb-0"><i class="fas fa-business-time text-warning me-2"></i>Heures de Pointe (00h - 23h)</h5>
                        <small class="text-muted">Moments où les clients passent le plus de commandes</small>
                    </div>
                </div>
                <div style="height: 280px; position: relative;">
                    <canvas id="peakHoursChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau détaillé des Villes de Livraison -->
    <div class="insight-panel p-3 p-md-4 mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
            <div>
                <h5 class="fw-bold text-dark mb-0"><i class="fas fa-truck text-primary me-2"></i>Détail des Villes de Livraison</h5>
                <small class="text-muted">Performance logistique, taux de livraison, panier moyen et part du chiffre d'affaires</small>
            </div>
            <span class="badge bg-light text-secondary border px-2.5 py-1">{{ $insights['cities']->count() }} ville(s)</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.875rem;">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Ville</th>
                        <th class="text-center">Commandes</th>
                        <th class="text-center">Livrées</th>
                        <th class="text-center">Taux Livraison</th>
                        <th class="text-end">Panier Moyen</th>
                        <th class="text-end">Livraison Moy.</th>
                        <th class="text-end">CA Généré</th>
                        <th class="text-end">Part CA</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($insights['cities']->take(15) as $idx => $city)
                        <tr>
                            <td class="fw-bold text-muted">{{ $idx + 1 }}</td>
                            <td class="fw-semibold text-dark">
                                <i class="fas fa-location-dot text-danger me-1.5 opacity-75"></i>{{ $city->city }}
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary-subtle text-primary rounded-pill px-2.5 fw-bold">{{ $city->orders_count }}</span>
                            </td>
                            <td class="text-center fw-semibold text-success">{{ $city->delivered_count }}</td>
                            <td class="text-center">
                                <span class="badge {{ $city->delivery_rate >= 70 ? 'bg-success-subtle text-success' : ($city->delivery_rate >= 40 ? 'bg-warning-subtle text-warning' : 'bg-light text-secondary border') }}">
                                    {{ $city->delivery_rate }}%
                                </span>
                            </td>
                            <td class="text-end">{{ currency($city->avg_order_value) }}</td>
                            <td class="text-end text-muted">{{ currency($city->avg_shipping) }}</td>
                            <td class="text-end fw-bold text-dark">{{ currency($city->revenue) }}</td>
                            <td class="text-end" style="min-width: 110px;">
                                <div class="d-flex align-items-center justify-content-end gap-2">
                                    <div class="progress flex-grow-1" style="height: 5px; max-width: 60px;">
                                        <div class="progress-bar bg-info" style="width: {{ min(100, $city->revenue_share) }}%"></div>
                                    </div>
                                    <span class="fw-semibold text-muted" style="font-size: 0.78rem;">{{ $city->revenue_share }}%</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">Aucune commande sur cette période.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bottom Row: Paiements, Catégories & Jours de la semaine -->
    <div class="row g-3">
        <div class="col-12 col-lg-4">
            <div class="insight-panel p-3 p-md-4 h-100">
                <h6 class="fw-bold text-dark mb-3"><i class="fas fa-credit-card text-success me-2"></i>Modes de Paiement &amp; Encaissement</h6>
                <div class="d-flex flex-column gap-2.5">
                    @forelse($insights['payments'] as $pay)
                        <div class="p-2.5 rounded-3 border bg-light-subtle">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <strong class="text-dark" style="font-size: 0.85rem;">{{ $pay->label }}</strong>
                                <span class="fw-bold text-dark" style="font-size: 0.85rem;">{{ currency((float) $pay->revenue) }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center text-muted" style="font-size: 0.75rem;">
                                <span>{{ $pay->orders_count }} cmd &bull; {{ $pay->paid_count }} payée(s)</span>
                                <span class="badge bg-success-subtle text-success">{{ $pay->collection_rate }}% encaissé</span>
                            </div>
                        </div>
                    @empty
                        <div class="text-muted small py-3 text-center">Aucune donnée de paiement.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="insight-panel p-3 p-md-4 h-100">
                <h6 class="fw-bold text-dark mb-3"><i class="fas fa-layer-group text-primary me-2"></i>Top Catégories Vendues</h6>
                @php
                    $maxCatRev = max(1, (float) ($insights['categories']->max('revenue') ?? 1));
                @endphp
                <div class="d-flex flex-column gap-2.5">
                    @forelse($insights['categories'] as $cat)
                        <div>
                            <div class="d-flex justify-content-between align-items-center small mb-1">
                                <span class="fw-semibold text-dark text-truncate">{{ $cat->category_name }}</span>
                                <span class="text-muted">{{ $cat->units_sold }} pcs &bull; <strong class="text-dark">{{ currency((float) $cat->revenue) }}</strong></span>
                            </div>
                            <div class="progress" style="height: 5px;">
                                <div class="progress-bar bg-primary" style="width: {{ round(((float) $cat->revenue / $maxCatRev) * 100) }}%"></div>
                            </div>
                        </div>
                    @empty
                        <div class="text-muted small py-3 text-center">Aucune vente par catégorie.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="insight-panel p-3 p-md-4 h-100">
                <h6 class="fw-bold text-dark mb-3"><i class="fas fa-calendar-week text-danger me-2"></i>Commandes par Jour de Semaine</h6>
                <div style="height: 215px; position: relative;">
                    <canvas id="peakDaysChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof Chart === 'undefined') return;

    const topCities = @json($insights['cities']->take(10)->values());
    const citiesCanvas = document.getElementById('topCitiesChart');
    if (citiesCanvas) {
        new Chart(citiesCanvas, {
            type: 'bar',
            data: {
                labels: topCities.map(c => c.city),
                datasets: [{
                    label: 'Commandes',
                    data: topCities.map(c => c.orders_count),
                    backgroundColor: 'rgba(14, 165, 233, 0.75)',
                    borderColor: '#0284c7',
                    borderWidth: 1,
                    borderRadius: 6,
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 11 } } },
                    y: { grid: { display: false }, ticks: { font: { size: 11 } } }
                }
            }
        });
    }

    const byHour = @json($insights['timing']['by_hour']);
    const hoursCanvas = document.getElementById('peakHoursChart');
    if (hoursCanvas) {
        new Chart(hoursCanvas, {
            type: 'bar',
            data: {
                labels: byHour.map(h => h.label),
                datasets: [{
                    label: 'Commandes',
                    data: byHour.map(h => h.orders),
                    backgroundColor: 'rgba(245, 158, 11, 0.7)',
                    borderColor: '#d97706',
                    borderWidth: 1,
                    borderRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { maxTicksLimit: 12, font: { size: 10 } } },
                    y: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 11 } } }
                }
            }
        });
    }

    const byDay = @json($insights['timing']['by_day']);
    const daysCanvas = document.getElementById('peakDaysChart');
    if (daysCanvas) {
        new Chart(daysCanvas, {
            type: 'bar',
            data: {
                labels: byDay.map(d => d.label),
                datasets: [{
                    label: 'Commandes',
                    data: byDay.map(d => d.orders),
                    backgroundColor: 'rgba(220, 38, 38, 0.75)',
                    borderColor: '#dc2626',
                    borderWidth: 1,
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { size: 11 } } },
                    y: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 11 } } }
                }
            }
        });
    }
});
</script>
@endpush

