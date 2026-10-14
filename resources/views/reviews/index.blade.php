@extends('layouts.app')

@section('title', 'Avis & Évaluations Clients')

@section('content')
    <!-- En-tête de page (Executive Brand Header) -->
    <div class="brand-header">
        <div class="d-flex align-items-center gap-2.5">
            <div class="brand-header-icon flex-shrink-0">
                <i class="fas fa-star-half-stroke"></i>
            </div>
            <div>
                <h1 class="brand-title">Avis & Évaluations Clients</h1>
                <p class="brand-subtitle">Modérez les commentaires, analysez la satisfaction et valorisez les retours clients</p>
            </div>
        </div>
        <div class="brand-header-actions d-flex align-items-center gap-2 flex-wrap">
            @if(($stats['pending_reviews'] ?? 0) > 0)
                <a href="{{ route('reviews.index', ['status' => 'pending']) }}" class="btn btn-outline-warning shadow-sm rounded-pill px-3 py-2 d-inline-flex align-items-center gap-1.5 fw-semibold" style="font-size: 0.84rem;">
                    <i class="fas fa-clock text-warning"></i>
                    <span>À Modérer</span>
                    <span class="badge bg-warning text-dark rounded-pill px-1.5" style="font-size: 0.72rem;" data-stat="pending-badge">{{ $stats['pending_reviews'] }}</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Alertes Flash -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Cartes KPI Métriques (Row of 4) -->
    <div class="row g-3 mb-4">
        <!-- Total Avis -->
        <div class="col-6 col-lg-3">
            <a href="{{ route('reviews.index') }}" class="stat-kpi-card text-decoration-none {{ !request('status') && !request('search') && !request('rating') && !request('product_id') ? 'active-filter' : '' }}">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label">Total Avis</div>
                        <div class="stat-kpi-value text-dark" data-stat="total">{{ number_format($stats['total_reviews'] ?? 0, 0, ',', ' ') }}</div>
                        <div class="stat-kpi-sub text-muted">
                            <span class="badge bg-light text-secondary border">Avis recueillis</span>
                        </div>
                    </div>
                    <div class="stat-kpi-icon bg-primary-subtle text-primary">
                        <i class="fas fa-comments"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- En Attente -->
        <div class="col-6 col-lg-3">
            <a href="{{ route('reviews.index', ['status' => 'pending']) }}" class="stat-kpi-card text-decoration-none {{ request('status') === 'pending' ? 'active-filter' : '' }}">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label">En Attente</div>
                        <div class="stat-kpi-value text-warning" data-stat="pending">{{ number_format($stats['pending_reviews'] ?? 0, 0, ',', ' ') }}</div>
                        <div class="stat-kpi-sub text-muted">
                            <i class="fas fa-clock text-warning me-1"></i>À modérer
                        </div>
                    </div>
                    <div class="stat-kpi-icon bg-warning-subtle text-warning">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- Approuvés -->
        <div class="col-6 col-lg-3">
            <a href="{{ route('reviews.index', ['status' => 'approved']) }}" class="stat-kpi-card text-decoration-none {{ request('status') === 'approved' ? 'active-filter' : '' }}">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label">Avis Approuvés</div>
                        <div class="stat-kpi-value text-success" data-stat="approved">{{ number_format($stats['approved_reviews'] ?? 0, 0, ',', ' ') }}</div>
                        <div class="stat-kpi-sub text-muted">
                            <i class="fas fa-check-circle text-success me-1"></i>Publiés en ligne
                        </div>
                    </div>
                    <div class="stat-kpi-icon bg-success-subtle text-success">
                        <i class="fas fa-circle-check"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- Note Moyenne -->
        <div class="col-6 col-lg-3">
            <div class="stat-kpi-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label">Note Moyenne</div>
                        <div class="stat-kpi-value text-dark" style="font-size: clamp(0.95rem, 3.2vw, 1.35rem);" data-stat="average">
                            {{ $stats['average_rating'] }} <span class="fs-6 text-muted fw-normal">/ 5</span>
                        </div>
                        <div class="stat-kpi-sub text-muted">
                            <i class="fas fa-star text-warning me-1"></i>Satisfaction globale
                        </div>
                    </div>
                    <div class="stat-kpi-icon bg-warning-subtle text-warning">
                        <i class="fas fa-star"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Barre de filtres unifiée (Desktop & Mobile) -->
    <div class="brand-filter-bar mb-3">
        <form method="GET" action="{{ route('reviews.index') }}" id="reviewFilterForm">
            <!-- Row 1 : Recherche + Bouton Drawer Mobile + Actions Desktop -->
            <div class="d-flex align-items-center mb-3 gap-2 flex-wrap">
                <div class="brand-search-wrapper flex-grow-1" style="min-width: 240px;">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" class="form-control" 
                           placeholder="Rechercher par client, email, titre, commentaire ou produit..."
                           value="{{ request('search') }}">
                </div>

                @php
                    $activeFilterKeys = ['status', 'rating', 'product_id'];
                    $hasActiveFilters = collect($activeFilterKeys)->some(fn($k) => request()->filled($k));
                    $activeFilterCount = collect($activeFilterKeys)->filter(fn($k) => request()->filled($k))->count();
                @endphp

                <!-- Bouton Drawer Filtres Mobile -->
                <button type="button" 
                        class="btn d-md-none {{ $hasActiveFilters ? 'btn-brand-primary' : 'btn-brand-light' }} d-inline-flex align-items-center gap-1.5"
                        data-bs-toggle="collapse" 
                        data-bs-target="#reviewFiltersCollapse" 
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
                        <a href="{{ route('reviews.index') }}" class="btn-brand-light btn-sm px-2.5 py-2 text-decoration-none" title="Réinitialiser tous les filtres">
                            <i class="fas fa-redo"></i>
                        </a>
                    @endif
                    <span class="badge bg-light text-secondary border px-3 py-2 fw-semibold" style="font-size: 0.8rem;">
                        {{ $reviews->total() }} {{ Str::plural('avis', $reviews->total()) }}
                    </span>
                </div>
            </div>

            <!-- Row 2 / Drawer Filtres Mobile & Filtres Avancés Desktop -->
            <div class="collapse d-md-block" id="reviewFiltersCollapse">
                <div class="row g-2 pt-2 pt-md-0 border-top border-md-0 mt-2 mt-md-0">
                    <!-- Statut -->
                    <div class="col-6 col-md-3">
                        <label class="form-label text-muted small fw-semibold mb-1" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.04em;">Statut</label>
                        <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">Tous les statuts</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>En attente</option>
                            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approuvé</option>
                            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejeté</option>
                        </select>
                    </div>

                    <!-- Note -->
                    <div class="col-6 col-md-3">
                        <label class="form-label text-muted small fw-semibold mb-1" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.04em;">Note</label>
                        <select name="rating" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">Toutes les notes</option>
                            <option value="5" {{ request('rating') == '5' ? 'selected' : '' }}>★★★★★ (5 étoiles)</option>
                            <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>★★★★☆ (4 étoiles)</option>
                            <option value="3" {{ request('rating') == '3' ? 'selected' : '' }}>★★★☆☆ (3 étoiles)</option>
                            <option value="2" {{ request('rating') == '2' ? 'selected' : '' }}>★★☆☆☆ (2 étoiles)</option>
                            <option value="1" {{ request('rating') == '1' ? 'selected' : '' }}>★☆☆☆☆ (1 étoile)</option>
                        </select>
                    </div>

                    <!-- Produit -->
                    <div class="col-12 col-md-4">
                        <label class="form-label text-muted small fw-semibold mb-1" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.04em;">Produit</label>
                        <select name="product_id" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">Tous les produits</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                                    {{ Str::limit($product->name, 40) }}
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
                                <a href="{{ route('reviews.index') }}" class="btn btn-light btn-sm border">
                                    <i class="fas fa-redo me-1"></i> Réinitialiser
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- VUE MOBILE (Cartes tactiles fluides) -->
    <div class="d-block d-md-none">
        @forelse($reviews as $review)
            @include('reviews.partials.mobile-review-card', ['review' => $review])
        @empty
            <div class="card border-0 shadow-sm rounded-4 p-4 text-center my-3 bg-white">
                <div class="brand-avatar mx-auto mb-3" style="width: 56px; height: 56px; font-size: 20px; background: #f8fafc; color: #94a3b8; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                    <i class="fas fa-star-half-stroke"></i>
                </div>
                <h6 class="fw-bold text-dark mb-1">Aucun avis trouvé</h6>
                <p class="text-muted small mb-3">Aucun commentaire ne correspond à vos filtres actuels.</p>
                <div>
                    <a href="{{ route('reviews.index') }}" class="btn btn-sm btn-brand-light rounded-pill px-3">
                        <i class="fas fa-redo me-1"></i> Réinitialiser les filtres
                    </a>
                </div>
            </div>
        @endforelse

        @if($reviews->hasPages())
            <div class="my-3 d-flex justify-content-center">
                {{ $reviews->links() }}
            </div>
        @endif
    </div>

    <!-- VUE DESKTOP (Tableau Haute Définition - Zéro scroll horizontal) -->
    <div class="d-none d-md-block">
        <div class="brand-table-card reviews-table-card shadow-sm border" style="overflow: hidden !important;">
            <div style="overflow-x: hidden !important; width: 100% !important;">
                <table class="brand-table align-middle" style="table-layout: fixed !important; width: 100% !important; margin: 0 !important;">
                    <thead>
                        <tr>
                            <th style="width: 22%; padding-left: 1.25rem;">Produit</th>
                            <th style="width: 18%;">Client</th>
                            <th style="width: 11%;">Note</th>
                            <th style="width: 24%;">Avis & Commentaire</th>
                            <th style="width: 8%;">Date</th>
                            <th style="width: 8%;" class="text-center">Statut</th>
                            <th style="width: 9%; padding-right: 1.25rem;" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reviews as $review)
                        @php
                            $statusClasses = [
                                'approved' => ['class' => 'success', 'dot' => 'dot-green', 'label' => 'Approuvé'],
                                'pending'  => ['class' => 'warning', 'dot' => 'dot-amber', 'label' => 'En attente'],
                                'rejected' => ['class' => 'danger',  'dot' => 'dot-red',   'label' => 'Rejeté'],
                            ];
                            $statusInfo = $statusClasses[$review->status] ?? ['class' => 'secondary', 'dot' => 'dot-gray', 'label' => ucfirst($review->status)];
                            $initials = collect(explode(' ', $review->customer_name))->map(fn($w) => mb_substr($w, 0, 1))->take(2)->join('');
                        @endphp
                        <tr data-review-id="{{ $review->id }}">
                            <!-- Produit: Miniature + Nom -->
                            <td style="padding-left: 1.25rem;">
                                <div class="d-flex align-items-center gap-2 min-w-0">
                                    <div class="flex-shrink-0" style="width: 36px; height: 36px; border-radius: 8px; overflow: hidden; background: #ffffff; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center;">
                                        @if($review->product)
                                            <img src="{{ $review->product->thumbnail }}" alt="{{ $review->product->name }}" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.onerror=null; this.src='{{ asset('images/camera/cat_cameras.jpg') }}';">
                                        @else
                                            <i class="fas fa-box text-muted"></i>
                                        @endif
                                    </div>
                                    <div class="min-w-0 flex-grow-1" style="max-width: calc(100% - 44px);">
                                        @if($review->product)
                                            <a href="{{ route('products.edit', $review->product) }}" class="fw-bold text-dark text-decoration-none d-block text-truncate" style="font-size: 0.85rem;" title="{{ $review->product->name }}">
                                                {{ $review->product->name }}
                                            </a>
                                            <span class="text-muted font-monospace" style="font-size: 0.68rem;">ID: #{{ $review->product->id }}</span>
                                        @else
                                            <span class="text-muted small fst-italic">Produit supprimé</span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Client: Avatar + Nom + Email -->
                            <td>
                                <div class="d-flex align-items-center gap-2 min-w-0">
                                    <div class="mobile-rev-avatar" style="width: 32px; height: 32px; min-width: 32px; font-size: 0.78rem;">
                                        {{ strtoupper($initials ?: 'AV') }}
                                    </div>
                                    <div class="min-w-0 flex-grow-1" style="max-width: calc(100% - 40px);">
                                        <div class="d-flex align-items-center gap-1">
                                            <span class="fw-semibold text-dark text-truncate d-block" style="font-size: 0.84rem;">{{ $review->customer_name }}</span>
                                            @if($review->verified_purchase)
                                                <i class="fas fa-check-circle text-success" style="font-size: 0.70rem;" title="Achat vérifié"></i>
                                            @endif
                                        </div>
                                        <div class="text-muted text-truncate" style="font-size: 0.72rem;">{{ $review->customer_email }}</div>
                                    </div>
                                </div>
                            </td>

                            <!-- Note -->
                            <td>
                                <div class="star-rating-amber">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="{{ $i <= $review->rating ? 'fas' : 'far' }} fa-star"></i>
                                    @endfor
                                </div>
                                <div class="text-muted fw-semibold mt-0.5" style="font-size: 0.70rem;">{{ $review->rating }}/5</div>
                            </td>

                            <!-- Avis & Commentaire -->
                            <td>
                                <div class="min-w-0" style="max-width: 100%;">
                                    @if($review->title)
                                        <div class="fw-bold text-dark text-truncate mb-0.5" style="font-size: 0.82rem;" title="{{ $review->title }}">
                                            {{ $review->title }}
                                        </div>
                                    @endif
                                    <div class="text-secondary text-truncate" style="font-size: 0.78rem;" title="{{ $review->comment }}">
                                        {{ $review->comment }}
                                    </div>
                                </div>
                            </td>

                            <!-- Date -->
                            <td>
                                <div class="text-dark small" style="font-size: 0.78rem;">{{ $review->created_at->format('d/m/Y') }}</div>
                                <div class="text-muted" style="font-size: 0.68rem;">{{ $review->created_at->diffForHumans() }}</div>
                            </td>

                            <!-- Statut -->
                            <td class="text-center" data-status>
                                <span class="badge bg-{{ $statusInfo['class'] }}-subtle text-{{ $statusInfo['class'] }} border border-{{ $statusInfo['class'] }}-subtle rounded-pill px-2 py-1 d-inline-flex align-items-center gap-1.5" style="font-size: 0.72rem; font-weight: 600;">
                                    <span class="status-dot {{ $statusInfo['dot'] }}"></span>
                                    <span>{{ $statusInfo['label'] }}</span>
                                </span>
                            </td>

                            <!-- Actions -->
                            <td style="padding-right: 1.25rem;" class="text-end">
                                <div class="action-buttons-group">
                                    @if($review->status == 'pending')
                                        <button type="button" class="action-btn action-btn-success btn-act-approve" onclick="approveReview({{ $review->id }})" title="Approuver l'avis">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button type="button" class="action-btn action-btn-warning btn-act-reject" onclick="rejectReview({{ $review->id }})" title="Rejeter l'avis">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @endif
                                    <button type="button" class="action-btn action-btn-danger" onclick="deleteReview({{ $review->id }})" title="Supprimer l'avis">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7">
                                <div class="text-center py-5">
                                    <div class="brand-avatar mx-auto mb-3" style="width: 56px; height: 56px; font-size: 22px; background: #f8fafc; color: #94a3b8; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                                        <i class="fas fa-star-half-stroke"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1">Aucun avis client trouvé</h6>
                                    <p class="text-muted small mb-3">Aucun commentaire ne correspond à vos filtres actuels.</p>
                                    <a href="{{ route('reviews.index') }}" class="btn btn-sm btn-brand-light rounded-pill px-3">
                                        <i class="fas fa-redo me-1"></i> Réinitialiser les filtres
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($reviews->hasPages())
            <div class="px-4 py-3 border-top">
                {{ $reviews->links() }}
            </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
<script>
/**
 * Update real-time statistics cards
 */
function updateReviewStats(stats) {
    if (!stats) return;
    const totalEl = document.querySelector('[data-stat="total"]');
    if (totalEl && stats.total_reviews !== undefined) {
        totalEl.textContent = new Intl.NumberFormat('fr-FR').format(stats.total_reviews);
    }
    const pendingEl = document.querySelector('[data-stat="pending"]');
    if (pendingEl && stats.pending_reviews !== undefined) {
        pendingEl.textContent = new Intl.NumberFormat('fr-FR').format(stats.pending_reviews);
    }
    const pendingBadge = document.querySelector('[data-stat="pending-badge"]');
    if (pendingBadge && stats.pending_reviews !== undefined) {
        pendingBadge.textContent = stats.pending_reviews;
        if (stats.pending_reviews === 0) {
            pendingBadge.closest('.btn')?.remove();
        }
    }
    const approvedEl = document.querySelector('[data-stat="approved"]');
    if (approvedEl && stats.approved_reviews !== undefined) {
        approvedEl.textContent = new Intl.NumberFormat('fr-FR').format(stats.approved_reviews);
    }
    const avgEl = document.querySelector('[data-stat="average"]');
    if (avgEl && stats.average_rating !== undefined) {
        avgEl.innerHTML = stats.average_rating + ' <span class="fs-6 text-muted fw-normal">/ 5</span>';
    }
}

/**
 * Approve review via AJAX
 */
function approveReview(reviewId) {
    fetch(`/reviews/${reviewId}/approve`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateReviewStats(data.stats);

            // Update Desktop Row
            const row = document.querySelector(`tr[data-review-id="${reviewId}"]`);
            if (row) {
                const statusCell = row.querySelector('[data-status]');
                if (statusCell) {
                    statusCell.innerHTML = `
                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1 d-inline-flex align-items-center gap-1.5" style="font-size: 0.72rem; font-weight: 600;">
                            <span class="status-dot dot-green"></span>
                            <span>Approuvé</span>
                        </span>
                    `;
                }
                row.querySelector('.btn-act-approve')?.remove();
                row.querySelector('.btn-act-reject')?.remove();
            }

            // Update Mobile Card
            const card = document.querySelector(`.mobile-review-card[data-review-id="${reviewId}"]`);
            if (card) {
                const mobStatus = card.querySelector('[data-status]');
                if (mobStatus) {
                    mobStatus.innerHTML = `
                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1 d-inline-flex align-items-center gap-1.5" style="font-size: 0.72rem; font-weight: 600;">
                            <span class="status-dot dot-green"></span>
                            <span>Approuvé</span>
                        </span>
                    `;
                }
                card.querySelector('.btn-mob-approve')?.remove();
                card.querySelector('.btn-mob-reject')?.remove();
            }

            Swal.fire({
                icon: 'success',
                title: 'Avis Approuvé !',
                text: 'Le commentaire est désormais visible publiquement en boutique.',
                timer: 2000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Erreur',
            text: 'Impossible d\'approuver cet avis.',
            toast: true,
            position: 'top-end',
            timer: 3000,
            showConfirmButton: false
        });
    });
}

/**
 * Reject review via AJAX
 */
function rejectReview(reviewId) {
    fetch(`/reviews/${reviewId}/reject`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateReviewStats(data.stats);

            // Update Desktop Row
            const row = document.querySelector(`tr[data-review-id="${reviewId}"]`);
            if (row) {
                const statusCell = row.querySelector('[data-status]');
                if (statusCell) {
                    statusCell.innerHTML = `
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2 py-1 d-inline-flex align-items-center gap-1.5" style="font-size: 0.72rem; font-weight: 600;">
                            <span class="status-dot dot-red"></span>
                            <span>Rejeté</span>
                        </span>
                    `;
                }
                row.querySelector('.btn-act-approve')?.remove();
                row.querySelector('.btn-act-reject')?.remove();
            }

            // Update Mobile Card
            const card = document.querySelector(`.mobile-review-card[data-review-id="${reviewId}"]`);
            if (card) {
                const mobStatus = card.querySelector('[data-status]');
                if (mobStatus) {
                    mobStatus.innerHTML = `
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2 py-1 d-inline-flex align-items-center gap-1.5" style="font-size: 0.72rem; font-weight: 600;">
                            <span class="status-dot dot-red"></span>
                            <span>Rejeté</span>
                        </span>
                    `;
                }
                card.querySelector('.btn-mob-approve')?.remove();
                card.querySelector('.btn-mob-reject')?.remove();
            }

            Swal.fire({
                icon: 'warning',
                title: 'Avis Rejeté',
                text: 'L\'avis a été marqué comme rejeté.',
                timer: 2000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Erreur',
            text: 'Impossible de rejeter cet avis.',
            toast: true,
            position: 'top-end',
            timer: 3000,
            showConfirmButton: false
        });
    });
}

/**
 * Delete review via AJAX with confirmation
 */
function deleteReview(reviewId) {
    Swal.fire({
        title: 'Supprimer cet avis ?',
        text: 'Cette action est irréversible et supprimera définitivement le retour client.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Oui, supprimer',
        cancelButtonText: 'Annuler',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/reviews/${reviewId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateReviewStats(data.stats);

                    // Remove Desktop Row with animation
                    const row = document.querySelector(`tr[data-review-id="${reviewId}"]`);
                    if (row) {
                        row.style.transition = 'opacity 0.25s, transform 0.25s';
                        row.style.opacity = '0';
                        row.style.transform = 'scale(0.96)';
                        setTimeout(() => row.remove(), 250);
                    }

                    // Remove Mobile Card with animation
                    const card = document.querySelector(`.mobile-review-card[data-review-id="${reviewId}"]`);
                    if (card) {
                        card.style.transition = 'opacity 0.25s, transform 0.25s';
                        card.style.opacity = '0';
                        card.style.transform = 'scale(0.96)';
                        setTimeout(() => card.remove(), 250);
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Avis Supprimé',
                        text: 'L\'avis a été supprimé de la base de données.',
                        timer: 2000,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: 'Impossible de supprimer cet avis.',
                    toast: true,
                    position: 'top-end',
                    timer: 3000,
                    showConfirmButton: false
                });
            });
        }
    });
}
</script>
@endpush
