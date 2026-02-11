@extends('layouts.app')

@section('title', 'Journal Entries')

@section('content')
    <!-- Page Header -->
    <div class="brand-header">
        <div>
            <h1 class="brand-title">
                <div class="brand-header-icon">
                    <i class="fas fa-receipt"></i>
                </div>
                Journal Entries
            </h1>
            <p class="brand-subtitle">Track all financial transactions and accounting entries</p>
        </div>
        <a href="{{ route('accounting.entries.create') }}" class="btn-brand-primary">
            <i class="fas fa-plus me-2"></i> New Entry
        </a>
    </div>

    <!-- Filter Form -->
    <div class="brand-table-card mb-4">
        <div class="p-4 border-bottom">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-filter text-primary"></i>
                <h5 class="fw-bold text-dark m-0">Filter Entries</h5>
            </div>
        </div>
        <div class="p-4">
            <form method="GET" action="{{ route('accounting.entries') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-semibold small text-muted">DATE FROM</label>
                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}" style="border-radius: 8px;">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold small text-muted">DATE TO</label>
                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}" style="border-radius: 8px;">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold small text-muted">JOURNAL TYPE</label>
                    <select name="journal_type" class="form-select" style="border-radius: 8px;">
                        <option value="">All Types</option>
                        @foreach(['SALES', 'PURCHASES', 'BANK', 'CASH', 'OD', 'GENERAL'] as $type)
                            <option value="{{ $type }}" {{ request('journal_type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold small text-muted">SEARCH</label>
                    <input type="text" name="search" class="form-control" placeholder="Ref or Description" value="{{ request('search') }}" style="border-radius: 8px;">
                </div>
                <div class="col-12 d-flex gap-2 justify-content-end">
                    <a href="{{ route('accounting.entries') }}" class="btn-brand-outline px-4">
                        <i class="fas fa-redo me-2"></i> Reset
                    </a>
                    <button type="submit" class="btn-brand-primary px-4">
                        <i class="fas fa-search me-2"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>


    <!-- Quick Stats Summary -->
    @if($entries->total() > 0)
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-white-50 mb-1 small">Total Entries</p>
                            <h4 class="text-white fw-bold mb-0">{{ $entries->total() }}</h4>
                        </div>
                        <div class="brand-avatar" style="background: rgba(255,255,255,0.2); color: white; width: 48px; height: 48px; font-size: 20px;">
                            <i class="fas fa-receipt"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border-radius: 12px;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-white-50 mb-1 small">This Page</p>
                            <h4 class="text-white fw-bold mb-0">{{ $entries->count() }}</h4>
                        </div>
                        <div class="brand-avatar" style="background: rgba(255,255,255,0.2); color: white; width: 48px; height: 48px; font-size: 20px;">
                            <i class="fas fa-file-invoice"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); border-radius: 12px;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-white-50 mb-1 small">Current Page</p>
                            <h4 class="text-white fw-bold mb-0">{{ $entries->currentPage() }} / {{ $entries->lastPage() }}</h4>
                        </div>
                        <div class="brand-avatar" style="background: rgba(255,255,255,0.2); color: white; width: 48px; height: 48px; font-size: 20px;">
                            <i class="fas fa-layer-group"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); border-radius: 12px;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-white-50 mb-1 small">Filters Active</p>
                            <h4 class="text-white fw-bold mb-0">{{ (request('start_date') || request('end_date') || request('journal_type') || request('search')) ? 'Yes' : 'No' }}</h4>
                        </div>
                        <div class="brand-avatar" style="background: rgba(255,255,255,0.2); color: white; width: 48px; height: 48px; font-size: 20px;">
                            <i class="fas fa-filter"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Entries Table -->
    <div class="brand-table-card">
        <div class="table-responsive">
            <table class="brand-table">
                <thead>
                    <tr>
                        <th style="padding-left: 1.5rem; width: 50px;">
                            <button class="btn btn-sm" style="background: transparent; border: none; color: #6366f1; padding: 0;" title="Expand/Collapse All" onclick="toggleAllRows()">
                                <i class="fas fa-chevron-down" id="toggleAllIcon"></i>
                            </button>
                        </th>
                        <th style="width: 120px;">Date</th>
                        <th style="width: 180px;">Reference</th>
                        <th>Description</th>
                        <th style="width: 110px;">Journal</th>
                        <th style="width: 120px; text-align: center;">Balance</th>
                        <th style="width: 140px; text-align: center; padding-right: 1.5rem;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($entries as $entry)
                    <tr class="journal-entry-row" style="border-bottom: 1px solid #f1f5f9;" data-entry-id="{{ $entry->id }}">
                        <td style="padding: 1.25rem 0 1.25rem 1.5rem;">
                            <button class="btn btn-sm toggle-details" style="background: transparent; border: none; color: #6366f1; padding: 0; transition: transform 0.2s;" onclick="toggleDetails({{ $entry->id }})" title="Show/Hide Details">
                                <i class="fas fa-chevron-right" id="icon-{{ $entry->id }}"></i>
                            </button>
                        </td>
                        <td style="padding: 1.25rem 0; white-space: nowrap;">
                            <div class="d-flex flex-column">
                                <span class="text-dark fw-bold" style="font-size: 0.9rem;">{{ $entry->date->format('M d, Y') }}</span>
                                <span class="text-muted small">{{ $entry->date->format('D') }}</span>
                            </div>
                        </td>
                        <td style="padding: 1.25rem 0;">
                            <span class="badge bg-light text-primary font-monospace py-2 px-3 shadow-sm" style="border-radius: 8px; border: 1px solid #e0e7ff; font-size: 0.8rem; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.borderColor='#6366f1'; this.style.backgroundColor='#f0f0ff';" onmouseout="this.style.borderColor='#e0e7ff'; this.style.backgroundColor='';" title="Click to view details">
                                {{ $entry->reference }}
                            </span>
                        </td>
                        <td style="padding: 1.25rem 0;">
                            <div class="d-flex flex-column">
                                <span class="text-dark fw-semibold">{{ $entry->description }}</span>
                                <span class="text-muted small">
                                    <i class="fas fa-list-ul me-1" style="font-size: 0.7rem;"></i>
                                    {{ $entry->lines->count() }} line item{{ $entry->lines->count() > 1 ? 's' : '' }}
                                </span>
                            </div>
                        </td>
                        <td style="padding: 1.25rem 0;">
                            <span class="brand-badge 
                                @if($entry->journal_type === 'SALES') success
                                @elseif($entry->journal_type === 'PURCHASES') warning
                                @elseif(in_array($entry->journal_type, ['BANK', 'CASH'])) info
                                @else secondary
                                @endif" style="font-size: 0.75rem; font-weight: 600;">
                                {{ $entry->journal_type }}
                            </span>
                        </td>
                        <td style="padding: 1.25rem 0; text-align: center;">
                            <div class="d-flex flex-column align-items-center gap-1">
                                <span class="badge shadow-sm" style="background: #dcfce7; color: #166534; font-weight: 600; font-size: 0.75rem; min-width: 80px;">
                                    {{ number_format($entry->total_debit, 2) }}
                                </span>
                                <span class="badge shadow-sm" style="background: #fee2e2; color: #991b1b; font-weight: 600; font-size: 0.75rem; min-width: 80px;">
                                    {{ number_format($entry->total_credit, 2) }}
                                </span>
                            </div>
                        </td>
                        <td style="padding: 1.25rem 1.5rem 1.25rem 0; text-align: center;">
                            <div class="d-flex gap-1 justify-content-center">
                                <button class="btn btn-sm shadow-sm" style="background: white; border: 1px solid #e5e7eb; color: #6366f1; padding: 0.4rem 0.7rem; border-radius: 6px; transition: all 0.2s;" onmouseover="this.style.background='#f0f0ff'; this.style.borderColor='#6366f1';" onmouseout="this.style.background='white'; this.style.borderColor='#e5e7eb';" title="View Entry Details" onclick="toggleDetails({{ $entry->id }})">
                                    <i class="fas fa-eye" style="font-size: 0.85rem;"></i>
                                </button>
                                {{-- Edit button commented out until edit route is created
                                <a href="{{ route('accounting.entries.edit', $entry->id) }}" class="btn btn-sm shadow-sm" style="background: white; border: 1px solid #e5e7eb; color: #059669; padding: 0.4rem 0.7rem; border-radius: 6px; transition: all 0.2s; text-decoration: none;" onmouseover="this.style.background='#ecfdf5'; this.style.borderColor='#059669';" onmouseout="this.style.background='white'; this.style.borderColor='#e5e7eb';" title="Edit This Entry">
                                    <i class="fas fa-edit" style="font-size: 0.85rem;"></i>
                                </a>
                                --}}
                                <button class="btn btn-sm shadow-sm" style="background: white; border: 1px solid #e5e7eb; color: #dc2626; padding: 0.4rem 0.7rem; border-radius: 6px; transition: all 0.2s;" onmouseover="this.style.background='#fef2f2'; this.style.borderColor='#dc2626';" onmouseout="this.style.background='white'; this.style.borderColor='#e5e7eb';" title="Delete Entry (Ctrl+D)" onclick="confirmDelete({{ $entry->id }})">
                                    <i class="fas fa-trash-alt" style="font-size: 0.85rem;"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <!-- Expandable Details Row -->
                    <tr id="details-{{ $entry->id }}" class="details-row" style="display: none; background: linear-gradient(135deg, #f8f9ff 0%, #fafbfc 100%);">
                        <td colspan="7" class="p-0">
                            <div class="p-4 pb-3" style="border-left: 4px solid #6366f1;">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <h6 class="fw-bold text-dark m-0 d-flex align-items-center gap-2" style="font-size: 0.95rem;">
                                        <div style="width: 28px; height: 28px; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-list-alt text-white" style="font-size: 0.75rem;"></i>
                                        </div>
                                        <span>Transaction Details</span>
                                    </h6>
                                    <span class="badge" style="background: linear-gradient(135deg, #f0f0ff 0%, #e8e8ff 100%); color: #6366f1; font-weight: 600; padding: 0.4rem 0.8rem; border-radius: 6px; font-size: 0.75rem;">
                                        <i class="fas fa-layer-group me-1" style="font-size: 0.65rem;"></i>
                                        {{ $entry->lines->count() }} Line{{ $entry->lines->count() > 1 ? 's' : '' }}
                                    </span>
                                </div>
                                
                                <div style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.04); border: 1px solid #e5e7eb;">
                                    <table class="table table-sm m-0">
                                        <thead style="background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); border-bottom: 2px solid #e2e8f0;">
                                            <tr>
                                                <th style="padding: 0.85rem 1rem; font-weight: 700; color: #1e293b; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                                    <i class="fas fa-building me-2" style="color: #6366f1; font-size: 0.65rem;"></i>Account
                                                </th>
                                                <th class="text-end" style="padding: 0.85rem 1rem; font-weight: 700; color: #1e293b; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px; width: 180px;">
                                                    <i class="fas fa-arrow-up me-2" style="color: #059669; font-size: 0.65rem;"></i>Debit
                                                </th>
                                                <th class="text-end" style="padding: 0.85rem 1rem; font-weight: 700; color: #1e293b; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px; width: 180px;">
                                                    <i class="fas fa-arrow-down me-2" style="color: #dc2626; font-size: 0.65rem;"></i>Credit
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($entry->lines as $index => $line)
                                            <tr class="transaction-line-row" style="border-bottom: 1px solid #f1f5f9; transition: all 0.2s;">
                                                <td style="padding: 0.75rem 1rem;">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div style="width: 30px; height: 30px; background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); border-radius: 6px; display: flex; align-items: center; justify-content: center; border: 1px solid #e5e7eb;">
                                                            <i class="fas fa-wallet" style="color: #6366f1; font-size: 0.75rem;"></i>
                                                        </div>
                                                        <div>
                                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                                <span class="badge font-monospace" style="background: linear-gradient(135deg, #1e293b 0%, #334155 100%); color: white; font-size: 0.65rem; padding: 0.25rem 0.5rem; font-weight: 600; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                                                                    {{ $line->account->code }}
                                                                </span>
                                                            </div>
                                                            <div class="text-dark fw-semibold" style="font-size: 0.85rem;">{{ $line->account->name }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-end" style="padding: 0.75rem 1rem; vertical-align: middle;">
                                                    @if($line->debit > 0)
                                                        <span class="badge" style="background: linear-gradient(135deg, #dcfce7 0%, #d1fae5 100%); color: #166534; font-weight: 700; font-size: 0.75rem; padding: 0.4rem 0.8rem; border-radius: 6px; border: 1px solid #86efac; box-shadow: 0 1px 3px rgba(22,101,52,0.08);">
                                                            <i class="fas fa-plus-circle me-1" style="font-size: 0.65rem;"></i>
                                                            {{ number_format($line->debit, 2) }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted" style="font-size: 0.8rem; opacity: 0.5;">—</span>
                                                    @endif
                                                </td>
                                                <td class="text-end" style="padding: 0.75rem 1rem; vertical-align: middle;">
                                                    @if($line->credit > 0)
                                                        <span class="badge" style="background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); color: #991b1b; font-weight: 700; font-size: 0.75rem; padding: 0.4rem 0.8rem; border-radius: 6px; border: 1px solid #fca5a5; box-shadow: 0 1px 3px rgba(153,27,27,0.08);">
                                                            <i class="fas fa-minus-circle me-1" style="font-size: 0.65rem;"></i>
                                                            {{ number_format($line->credit, 2) }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted" style="font-size: 0.8rem; opacity: 0.5;">—</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                            <tr style="background: linear-gradient(135deg, #f8fafc 0%, #eef2ff 100%); border-top: 2px solid #6366f1;">
                                                <td class="text-end fw-bold" style="padding: 0.85rem 1rem; color: #1e293b; font-size: 0.85rem;">
                                                    <div style="display: inline-flex; align-items: center; gap: 0.6rem;">
                                                        <div style="width: 30px; height: 30px; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); border-radius: 6px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(99,102,241,0.25);">
                                                            <i class="fas fa-calculator text-white" style="font-size: 0.7rem;"></i>
                                                        </div>
                                                        <span style="font-weight: 700; font-size: 0.85rem;">TOTAL</span>
                                                    </div>
                                                </td>
                                                <td class="text-end fw-bold" style="padding: 0.85rem 1rem;">
                                                    <span class="badge" style="background: linear-gradient(135deg, #059669 0%, #047857 100%); color: white; font-weight: 700; font-size: 0.8rem; padding: 0.5rem 1rem; border-radius: 8px; box-shadow: 0 2px 6px rgba(5,150,105,0.2); letter-spacing: 0.3px;">
                                                        {{ number_format($entry->total_debit, 2) }}
                                                    </span>
                                                </td>
                                                <td class="text-end fw-bold" style="padding: 0.85rem 1rem;">
                                                    <span class="badge" style="background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%); color: white; font-weight: 700; font-size: 0.8rem; padding: 0.5rem 1rem; border-radius: 8px; box-shadow: 0 2px 6px rgba(220,38,38,0.2); letter-spacing: 0.3px;">
                                                        {{ number_format($entry->total_credit, 2) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7">
                            <div class="text-center py-5">
                                <div class="brand-avatar mx-auto mb-3" style="background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%); color: #9ca3af; width: 80px; height: 80px; font-size: 32px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
                                    <i class="fas fa-receipt"></i>
                                </div>
                                <h6 class="fw-bold text-dark mb-2" style="font-size: 1.1rem;">No Entries Found</h6>
                                <p class="text-muted small mb-4">There are no journal entries matching your criteria.<br>Create your first entry to start tracking transactions.</p>
                                <a href="{{ route('accounting.entries.create') }}" class="btn-brand-primary shadow" style="padding: 0.65rem 1.5rem;">
                                    <i class="fas fa-plus me-2"></i> Create First Entry
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($entries->hasPages())
        <div class="p-4 border-top" style="background: #fafbfc;">
            {{ $entries->links() }}
        </div>
        @endif
    </div>

    <style>
        .journal-entry-row {
            transition: all 0.2s ease;
        }
        .journal-entry-row:hover {
            background: linear-gradient(to right, #fafbff, #ffffff) !important;
            box-shadow: 0 2px 8px rgba(99, 102, 241, 0.08);
        }
        .toggle-details {
            cursor: pointer;
        }
        .toggle-details i {
            transition: transform 0.2s ease;
        }
        .toggle-details.expanded i {
            transform: rotate(90deg);
        }
        .details-row td {
            padding: 0 !important;
        }
        .transaction-line-row:hover {
            background: linear-gradient(to right, #fafcff, #f8f9ff) !important;
            box-shadow: 0 1px 4px rgba(99, 102, 241, 0.06);
        }
    </style>

    <script>
        function toggleDetails(entryId) {
            const detailsRow = document.getElementById('details-' + entryId);
            const icon = document.getElementById('icon-' + entryId);
            const button = icon.closest('.toggle-details');
            
            if (detailsRow.style.display === 'none') {
                detailsRow.style.display = '';
                icon.classList.remove('fa-chevron-right');
                icon.classList.add('fa-chevron-down');
                button.classList.add('expanded');
            } else {
                detailsRow.style.display = 'none';
                icon.classList.remove('fa-chevron-down');
                icon.classList.add('fa-chevron-right');
                button.classList.remove('expanded');
            }
        }

        function toggleAllRows() {
            const detailsRows = document.querySelectorAll('.details-row');
            const icons = document.querySelectorAll('.toggle-details i');
            const toggleAllIcon = document.getElementById('toggleAllIcon');
            const allExpanded = Array.from(detailsRows).every(row => row.style.display !== 'none');
            
            detailsRows.forEach((row, index) => {
                if (allExpanded) {
                    row.style.display = 'none';
                    icons[index].classList.remove('fa-chevron-down');
                    icons[index].classList.add('fa-chevron-right');
                    icons[index].closest('.toggle-details').classList.remove('expanded');
                } else {
                    row.style.display = '';
                    icons[index].classList.remove('fa-chevron-right');
                    icons[index].classList.add('fa-chevron-down');
                    icons[index].closest('.toggle-details').classList.add('expanded');
                }
            });

            if (allExpanded) {
                toggleAllIcon.classList.remove('fa-chevron-up');
                toggleAllIcon.classList.add('fa-chevron-down');
            } else {
                toggleAllIcon.classList.remove('fa-chevron-down');
                toggleAllIcon.classList.add('fa-chevron-up');
            }
        }

        function confirmDelete(entryId) {
            if (confirm('Are you sure you want to delete this journal entry? This action cannot be undone.')) {
                // Add your delete logic here
                console.log('Deleting entry:', entryId);
                // Example: window.location.href = '/accounting/entries/' + entryId + '/delete';
            }
        }

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl/Cmd + N for new entry
            if ((e.ctrlKey || e.metaKey) && e.key === 'n') {
                e.preventDefault();
                window.location.href = '{{ route('accounting.entries.create') }}';
            }
        });
    </script>
@endsection
