@extends('layouts.app')

@section('title', 'Accounting Dashboard')

@section('content')
    <!-- Page Header -->
    <div class="brand-header">
        <div>
            <h1 class="brand-title">
                <div class="brand-header-icon">
                    <i class="fas fa-book-open"></i>
                </div>
                Financial Overview
            </h1>
            <p class="brand-subtitle">Manage your accounts and track financial health.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('accounting.entries.create') }}" class="btn-brand-primary">
                <i class="fas fa-plus me-2"></i> New Entry
            </a>
            <a href="{{ route('accounting.reports') }}" class="btn-brand-outline">
                <i class="fas fa-chart-bar me-2"></i> Reports
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="brand-stats-grid">
        <div class="brand-stat-card">
            <div class="brand-stat-icon info">
                <i class="fas fa-university"></i>
            </div>
            <div class="brand-stat-label">Total Assets</div>
            <div class="brand-stat-value">{{ currency($totalAssets) }}</div>
        </div>
        
        <div class="brand-stat-card">
            <div class="brand-stat-icon danger">
                <i class="fas fa-file-invoice-dollar"></i>
            </div>
            <div class="brand-stat-label">Total Liabilities</div>
            <div class="brand-stat-value">{{ currency($totalLiabilities) }}</div>
        </div>
        
        <div class="brand-stat-card">
            <div class="brand-stat-icon primary">
                <i class="fas fa-balance-scale"></i>
            </div>
            <div class="brand-stat-label">Equity</div>
            <div class="brand-stat-value">{{ currency($totalEquity) }}</div>
        </div>
        
        <div class="brand-stat-card">
            <div class="brand-stat-icon success">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="brand-stat-label">Net Income (YTD)</div>
            <div class="brand-stat-value">{{ currency($netIncome) }}</div>
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
                <div class="d-flex justify-content-between align-items-center p-4 border-bottom">
                    <h5 class="fw-bold text-dark m-0">Recent Journal Entries</h5>
                    <a href="{{ route('accounting.entries') }}" class="btn-brand-light">View All</a>
                </div>
                <div class="table-responsive">
                    <table class="brand-table">
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
