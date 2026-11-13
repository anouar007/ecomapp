@extends('layouts.app')

@section('title', 'Accounting Dashboard')

@section('content')
    <!-- Page Header (Clean & Pro) -->
    <div class="brand-header">
        <div class="d-flex align-items-center gap-2.5">
            <div class="brand-header-icon flex-shrink-0">
                <i class="fas fa-book-open"></i>
            </div>
            <div>
                <h1 class="brand-title">Comptabilité & Vue Financière</h1>
                <p class="brand-subtitle">Gérez vos comptes, bilans et suivez la santé financière de l'entreprise</p>
            </div>
        </div>
        <div class="brand-header-actions">
            <a href="{{ route('accounting.reports') }}" class="btn-brand-outline">
                <i class="fas fa-chart-bar me-1.5" style="color: var(--primary-color)"></i> Rapports
            </a>
            <a href="{{ route('accounting.entries.create') }}" class="btn-brand-primary">
                <i class="fas fa-plus me-1.5"></i> Nouvelle Écriture
            </a>
        </div>
    </div>

    @include('partials.subnav.finance')

    <!-- Statistics Cards (Product Section Standard) -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="stat-kpi-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label">Actif Total</div>
                        <div class="stat-kpi-value text-dark">{{ currency($totalAssets) }}</div>
                        <div class="stat-kpi-sub text-muted">Total Assets</div>
                    </div>
                    <div class="stat-kpi-icon bg-info-subtle text-info">
                        <i class="fas fa-university"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-6 col-lg-3">
            <div class="stat-kpi-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label">Passif Total</div>
                        <div class="stat-kpi-value text-danger">{{ currency($totalLiabilities) }}</div>
                        <div class="stat-kpi-sub text-muted">Total Liabilities</div>
                    </div>
                    <div class="stat-kpi-icon bg-danger-subtle text-danger">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-6 col-lg-3">
            <div class="stat-kpi-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label">Capitaux Propres</div>
                        <div class="stat-kpi-value text-primary">{{ currency($totalEquity) }}</div>
                        <div class="stat-kpi-sub text-muted">Equity</div>
                    </div>
                    <div class="stat-kpi-icon bg-primary-subtle text-primary">
                        <i class="fas fa-balance-scale"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-6 col-lg-3">
            <div class="stat-kpi-card" style="border-left: 3.5px solid var(--success-color, #10b981);">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label">Résultat Net (YTD)</div>
                        <div class="stat-kpi-value text-success">{{ currency($netIncome) }}</div>
                        <div class="stat-kpi-sub text-muted">Exercice en cours</div>
                    </div>
                    <div class="stat-kpi-icon bg-success-subtle text-success">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts & Quick Actions Row -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="brand-table-card h-100 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold text-dark m-0">Revenue vs Expenses ({{ date('Y') }})</h5>
                    <div class="badge bg-light text-primary py-2 px-3" style="border-radius: 8px;">YTD Performance</div>
                </div>
                <div style="height: 300px;">
                    <canvas id="myAreaChart"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="brand-table-card h-100 p-4">
                <h5 class="fw-bold text-dark mb-4">Quick Actions</h5>
                <div class="d-flex flex-column gap-3">
                    <a href="{{ route('accounting.accounts') }}" class="btn-brand-outline justify-content-start">
                        <i class="fas fa-book me-2"></i>
                        <span>Chart of Accounts</span>
                    </a>
                    <a href="{{ route('accounting.reports') }}" class="btn-brand-outline justify-content-start">
                        <i class="fas fa-file-alt me-2"></i>
                        <span>Financial Reports</span>
                    </a>
                    <a href="{{ route('accounting.entries.create') }}" class="btn-brand-outline justify-content-start">
                        <i class="fas fa-plus-circle me-2"></i>
                        <span>Record Entry</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions Row -->
    <div class="row">
        <div class="col-12">
            <div class="brand-table-card">
                <div class="d-flex justify-content-between align-items-center p-3 p-md-4 border-bottom">
                    <h5 class="fw-bold text-dark m-0 fs-6 fs-md-5">Recent Journal Entries</h5>
                    <a href="{{ route('accounting.entries') }}" class="btn-brand-light btn-sm">View All</a>
                </div>

                {{-- Desktop Table (d-none d-md-block) --}}
                <div class="table-responsive d-none d-md-block">
                    <table class="brand-table mb-0 align-middle">
                        <thead>
                            <tr>
                                <th style="padding-left: 1.5rem;">Date</th>
                                <th>Ref</th>
                                <th>Description</th>
                                <th>Type</th>
                                <th class="text-end" style="padding-right: 1.5rem;">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentEntries as $entry)
                            <tr>
                                <td style="padding-left: 1.5rem;">{{ $entry->date->format('M d, Y') }}</td>
                                <td><span class="badge bg-light text-primary font-monospace py-2 px-3" style="border-radius: 8px;">{{ $entry->reference }}</span></td>
                                <td>{{ $entry->description }}</td>
                                <td><span class="brand-badge secondary">{{ $entry->journal_type }}</span></td>
                                <td class="text-end fw-bold text-dark" style="padding-right: 1.5rem;">{{ currency($entry->total_debit) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5">
                                    <div class="text-center py-5">
                                        <i class="fas fa-file-alt text-muted opacity-25 fs-1 mb-3"></i>
                                        <p class="text-muted">No recent entries found.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Mobile Touch Cards (d-block d-md-none, zero horizontal scroll) --}}
                <div class="d-block d-md-none p-2.5">
                    @forelse($recentEntries as $entry)
                    <div class="card border rounded-3 p-3 mb-2 bg-white shadow-xs">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="badge bg-light text-primary font-monospace border px-2 py-1" style="font-size: 0.76rem;">
                                {{ $entry->reference }}
                            </span>
                            <span class="brand-badge secondary" style="font-size: 0.70rem;">
                                {{ $entry->journal_type }}
                            </span>
                        </div>
                        <div class="fw-semibold text-dark small mb-2 text-truncate">{{ $entry->description }}</div>
                        <div class="d-flex align-items-center justify-content-between pt-1 border-top">
                            <span class="text-muted small" style="font-size: 0.72rem;">
                                <i class="far fa-calendar-alt me-1"></i>{{ $entry->date->format('d/m/Y') }}
                            </span>
                            <span class="fw-bold text-dark fs-6">{{ currency($entry->total_debit) }}</span>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4">
                        <p class="text-muted small mb-0">No recent entries found.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var ctx = document.getElementById("myAreaChart");
        new Chart(ctx, {
          type: 'line',
          data: {
            labels: @json($chartData['labels']),
            datasets: [{
              label: "Revenue",
              tension: 0.4,
              backgroundColor: "rgba(99, 102, 241, 0.05)",
              borderColor: "#6366f1",
              pointRadius: 4,
              pointBackgroundColor: "#fff",
              pointBorderColor: "#6366f1",
              pointBorderWidth: 2,
              pointHoverRadius: 4,
              pointHoverBackgroundColor: "#6366f1",
              pointHoverBorderColor: "#fff",
              pointHitRadius: 10,
              fill: true,
              data: @json($chartData['revenue']),
            },
            {
              label: "Expenses",
              tension: 0.4,
              backgroundColor: "rgba(239, 68, 68, 0.05)",
              borderColor: "#ef4444",
              pointRadius: 4,
              pointBackgroundColor: "#fff",
              pointBorderColor: "#ef4444",
              pointBorderWidth: 2,
              pointHoverRadius: 4,
              pointHoverBackgroundColor: "#ef4444",
              pointHoverBorderColor: "#fff",
              pointHitRadius: 10,
              fill: true,
              data: @json($chartData['expense']),
            }],
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
              legend: {
                display: true,
                position: 'top',
                labels: {
                  usePointStyle: true,
                  font: { size: 11 }
                }
              },
              tooltip: {
                backgroundColor: "rgb(255,255,255)",
                bodyColor: "#64748b",
                titleColor: '#334155',
                titleFont: { size: 12, weight: 'bold' },
                borderColor: '#e2e8f0',
                borderWidth: 1,
                padding: 12,
                displayColors: false,
                intersect: false,
                mode: 'index',
              }
            },
            scales: {
              x: {
                grid: {
                  display: false
                },
                ticks: {
                  color: '#94a3b8',
                  font: { size: 10 }
                }
              },
              y: {
                beginAtZero: true,
                grid: {
                  color: '#f1f5f9',
                  borderDash: [5, 5]
                },
                ticks: {
                  color: '#94a3b8',
                  font: { size: 10 }
                }
              }
            }
          }
        });
    });
</script>
@endpush
