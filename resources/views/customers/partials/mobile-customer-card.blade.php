@php
    $statusClasses = [
        'active' => ['class' => 'success', 'dot' => 'dot-green', 'label' => 'Actif'],
        'inactive' => ['class' => 'warning', 'dot' => 'dot-amber', 'label' => 'Inactif'],
        'blocked' => ['class' => 'danger', 'dot' => 'dot-red', 'label' => 'Bloqué'],
    ];
    $statusInfo = $statusClasses[$customer->status] ?? ['class' => 'secondary', 'dot' => 'dot-gray', 'label' => ucfirst($customer->status_label)];
    $initials = collect(explode(' ', $customer->name))->map(fn($w) => mb_substr($w, 0, 1))->take(2)->join('');
@endphp

<div class="mobile-customer-card">
    <!-- Header: Avatar + Identity + Status -->
    <div class="d-flex align-items-start justify-content-between gap-2.5 mb-2.5">
        <div class="d-flex align-items-center gap-2.5 flex-grow-1 min-w-0">
            <div class="mobile-cust-avatar">
                {{ strtoupper($initials ?: 'CL') }}
            </div>
            <div class="min-w-0 flex-grow-1">
                <a href="{{ route('customers.show', $customer) }}" class="text-decoration-none d-block">
                    <h6 class="fw-bold text-dark mb-0.5 text-truncate" style="font-size: 0.96rem;">{{ $customer->name }}</h6>
                </a>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge bg-light text-secondary border font-monospace px-1.5 py-0.5" style="font-size: 0.72rem;">
                        {{ $customer->customer_code }}
                    </span>
                    @if($customer->customerGroup)
                        <span class="badge px-1.5 py-0.5 rounded-pill" style="font-size: 0.7rem; background-color: {{ $customer->customerGroup->color }}18; color: {{ $customer->customerGroup->color }}; border: 1px solid {{ $customer->customerGroup->color }}40;">
                            <i class="fas fa-tag me-1" style="font-size: 0.65rem;"></i>{{ $customer->customerGroup->name }}
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Status Dot Pill -->
        <div class="flex-shrink-0">
            <span class="badge bg-{{ $statusInfo['class'] }}-subtle text-{{ $statusInfo['class'] }} border border-{{ $statusInfo['class'] }}-subtle rounded-pill px-2 py-1 d-inline-flex align-items-center gap-1.5" style="font-size: 0.72rem; font-weight: 600;">
                <span class="status-dot {{ $statusInfo['dot'] }}"></span>
                <span>{{ $statusInfo['label'] }}</span>
            </span>
        </div>
    </div>

    <!-- Contact Info -->
    <div class="d-flex flex-column gap-1 mb-3 pt-1 border-top" style="border-color: #f1f5f9 !important;">
        @if($customer->email)
            <div class="d-flex align-items-center gap-2 text-truncate" style="font-size: 0.8rem;">
                <i class="fas fa-envelope text-muted flex-shrink-0" style="width: 14px;"></i>
                <a href="mailto:{{ $customer->email }}" class="text-secondary text-truncate text-decoration-none">
                    {{ $customer->email }}
                </a>
            </div>
        @endif
        @if($customer->phone)
            <div class="d-flex align-items-center gap-2" style="font-size: 0.8rem;">
                <i class="fas fa-phone text-muted flex-shrink-0" style="width: 14px;"></i>
                <a href="tel:{{ $customer->phone }}" class="text-secondary text-decoration-none font-monospace">
                    {{ $customer->phone }}
                </a>
            </div>
        @endif
        @if($customer->city || $customer->country)
            <div class="d-flex align-items-center gap-2 text-muted" style="font-size: 0.78rem;">
                <i class="fas fa-map-marker-alt text-muted flex-shrink-0" style="width: 14px;"></i>
                <span class="text-truncate">{{ implode(', ', array_filter([$customer->city, $customer->country])) }}</span>
            </div>
        @endif
    </div>

    <!-- 3 Quick KPI Pills -->
    <div class="d-flex gap-2 mb-3">
        <div class="mobile-cust-stat-pill">
            <div class="mobile-cust-stat-label">Commandes</div>
            <div class="mobile-cust-stat-value text-primary">{{ $customer->total_orders }}</div>
        </div>
        <div class="mobile-cust-stat-pill">
            <div class="mobile-cust-stat-label">Dépensé</div>
            <div class="mobile-cust-stat-value text-success" style="font-size: 0.82rem;">{{ $customer->formatted_total_spent }}</div>
        </div>
        <div class="mobile-cust-stat-pill">
            <div class="mobile-cust-stat-label">Points</div>
            <div class="mobile-cust-stat-value text-purple" style="color: #8b5cf6;">{{ number_format($customer->loyalty_points) }}</div>
        </div>
    </div>

    <!-- Bottom Actions Row -->
    <div class="d-flex align-items-center justify-content-between pt-2 border-top gap-2" style="border-color: #f1f5f9 !important;">
        <div class="d-flex align-items-center gap-1.5 flex-grow-1">
            <a href="{{ route('customers.show', $customer) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1.5 d-inline-flex align-items-center justify-content-center gap-1.5 flex-grow-1 fw-semibold" style="font-size: 0.78rem;">
                <i class="fas fa-eye"></i> <span>Détails</span>
            </a>
            <a href="{{ route('customers.edit', $customer) }}" class="btn btn-sm btn-light border rounded-pill px-2.5 py-1.5 d-inline-flex align-items-center justify-content-center gap-1 text-secondary" style="font-size: 0.78rem;" title="Modifier">
                <i class="fas fa-edit"></i>
            </a>
            @if($customer->phone)
                <a href="tel:{{ $customer->phone }}" class="btn btn-sm btn-outline-success rounded-pill px-2.5 py-1.5 d-inline-flex align-items-center justify-content-center" style="font-size: 0.78rem;" title="Appeler">
                    <i class="fas fa-phone-alt"></i>
                </a>
            @endif
        </div>

        <form action="{{ route('customers.destroy', $customer) }}" method="POST" class="d-inline" onsubmit="return confirm('Voulez-vous vraiment supprimer le client {{ $customer->name }} ?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-outline-danger border-0 rounded-circle p-1.5 d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="Supprimer">
                <i class="fas fa-trash-alt" style="font-size: 0.78rem;"></i>
            </button>
        </form>
    </div>
</div>
