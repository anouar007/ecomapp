@php
    $statusClasses = [
        'approved' => ['class' => 'success', 'dot' => 'dot-green', 'label' => 'Approuvé'],
        'pending'  => ['class' => 'warning', 'dot' => 'dot-amber', 'label' => 'En attente'],
        'rejected' => ['class' => 'danger',  'dot' => 'dot-red',   'label' => 'Rejeté'],
    ];
    $statusInfo = $statusClasses[$review->status] ?? ['class' => 'secondary', 'dot' => 'dot-gray', 'label' => ucfirst($review->status)];
    $initials = collect(explode(' ', $review->customer_name))->map(fn($w) => mb_substr($w, 0, 1))->take(2)->join('');
@endphp

<div class="mobile-review-card" data-review-id="{{ $review->id }}">
    <!-- Top Row: Client Avatar + Name + Status Pill -->
    <div class="d-flex align-items-start justify-content-between gap-2 mb-2.5">
        <div class="d-flex align-items-center gap-2 flex-grow-1 min-w-0">
            <div class="mobile-rev-avatar">
                {{ strtoupper($initials ?: 'AV') }}
            </div>
            <div class="min-w-0 flex-grow-1">
                <div class="d-flex align-items-center gap-1.5 flex-wrap">
                    <span class="fw-bold text-dark text-truncate" style="font-size: 0.90rem;">{{ $review->customer_name }}</span>
                    @if($review->verified_purchase)
                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-1.5 py-0.5" style="font-size: 0.65rem;" title="Achat vérifié">
                            <i class="fas fa-check-circle me-0.5"></i>Vérifié
                        </span>
                    @endif
                </div>
                <div class="text-muted text-truncate" style="font-size: 0.74rem;">
                    {{ $review->customer_email }}
                </div>
            </div>
        </div>

        <div class="flex-shrink-0" data-status>
            <span class="badge bg-{{ $statusInfo['class'] }}-subtle text-{{ $statusInfo['class'] }} border border-{{ $statusInfo['class'] }}-subtle rounded-pill px-2 py-1 d-inline-flex align-items-center gap-1.5" style="font-size: 0.72rem; font-weight: 600;">
                <span class="status-dot {{ $statusInfo['dot'] }}"></span>
                <span>{{ $statusInfo['label'] }}</span>
            </span>
        </div>
    </div>

    <!-- Product Referenced -->
    <div class="d-flex align-items-center gap-2 p-2 rounded-3 bg-light mb-2.5">
        <div class="flex-shrink-0" style="width: 32px; height: 32px; border-radius: 8px; overflow: hidden; background: #ffffff; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center;">
            @if($review->product)
                <img src="{{ $review->product->thumbnail }}" alt="{{ $review->product->name }}" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.onerror=null; this.src='{{ asset('images/camera/cat_cameras.jpg') }}';">
            @else
                <i class="fas fa-box text-muted"></i>
            @endif
        </div>
        <div class="min-w-0 flex-grow-1">
            @if($review->product)
                <a href="{{ route('products.edit', $review->product) }}" class="fw-semibold text-dark text-decoration-none d-block text-truncate" style="font-size: 0.82rem;">
                    {{ $review->product->name }}
                </a>
            @else
                <span class="text-muted small fst-italic">Produit supprimé</span>
            @endif
        </div>
        <div class="text-muted small flex-shrink-0 font-monospace" style="font-size: 0.70rem;">
            {{ $review->created_at->format('d/m/Y') }}
        </div>
    </div>

    <!-- Stars & Review Content -->
    <div class="mb-3">
        <div class="d-flex align-items-center gap-1.5 mb-1.5">
            <div class="star-rating-amber">
                @for($i = 1; $i <= 5; $i++)
                    <i class="{{ $i <= $review->rating ? 'fas' : 'far' }} fa-star"></i>
                @endfor
            </div>
            <span class="fw-bold text-dark small">{{ $review->rating }}/5</span>
        </div>

        @if($review->title)
            <div class="fw-bold text-dark mb-1" style="font-size: 0.88rem;">
                {{ $review->title }}
            </div>
        @endif

        <p class="text-secondary small mb-0" style="line-height: 1.5; font-size: 0.80rem;">
            {{ $review->comment }}
        </p>
    </div>

    <!-- Actions Row -->
    <div class="d-flex align-items-center justify-content-between pt-2 border-top gap-2" style="border-color: #f1f5f9 !important;">
        <div class="action-buttons-group d-flex align-items-center gap-1.5 flex-grow-1">
            @if($review->status == 'pending')
                <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3 py-1 d-inline-flex align-items-center gap-1 fw-semibold btn-mob-approve" onclick="approveReview({{ $review->id }})" style="font-size: 0.76rem;">
                    <i class="fas fa-check"></i> <span>Approuver</span>
                </button>
                <button type="button" class="btn btn-sm btn-outline-warning rounded-pill px-2.5 py-1 d-inline-flex align-items-center gap-1 text-dark fw-semibold btn-mob-reject" onclick="rejectReview({{ $review->id }})" style="font-size: 0.76rem;">
                    <i class="fas fa-times"></i> <span>Rejeter</span>
                </button>
            @endif
        </div>

        <button type="button" class="btn btn-sm btn-outline-danger border-0 rounded-circle p-1.5 d-inline-flex align-items-center justify-content-center" onclick="deleteReview({{ $review->id }})" style="width: 32px; height: 32px;" title="Supprimer">
            <i class="fas fa-trash-alt" style="font-size: 0.78rem;"></i>
        </button>
    </div>
</div>
