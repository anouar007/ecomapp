@extends('layouts.app')

@section('title', 'Avis clients')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/management.css') }}">
@endpush

@section('content')
<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 class="page-title"><i class="fas fa-star"></i> Avis clients</h1>
            <p class="page-subtitle">Gérez les évaluations et retours d'expérience de vos clients</p>
        </div>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="alert alert-danger">
    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
</div>
@endif

<!-- Cartes statistiques -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 32px;">
    <div style="background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); border-radius: 16px; padding: 24px; border: 1px solid #e2e8f0;">
        <div style="display: flex; align-items: center; gap: 16px;">
            <div style="width: 56px; height: 56px; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-star" style="color: white; font-size: 28px;"></i>
            </div>
            <div>
                <p style="color: #64748b; font-size: 13px; margin: 0 0 4px 0; font-weight: 600;">Total des avis</p>
                <p style="font-size: 28px; font-weight: 700; color: #1e293b; margin: 0;" data-stat="total">{{ number_format($stats['total_reviews']) }}</p>
            </div>
        </div>
    </div>

    <div style="background: linear-gradient(135deg, #ffffff 0%, #fef3c7 100%); border-radius: 16px; padding: 24px; border: 1px solid #fde68a;">
        <div style="display: flex; align-items: center; gap: 16px;">
            <div style="width: 56px; height: 56px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-clock" style="color: white; font-size: 28px;"></i>
            </div>
            <div>
                <p style="color: #92400e; font-size: 13px; margin: 0 0 4px 0; font-weight: 600;">En attente de modération</p>
                <p style="font-size: 28px; font-weight: 700; color: #b45309; margin: 0;" data-stat="pending">{{ number_format($stats['pending_reviews']) }}</p>
            </div>
        </div>
    </div>

    <div style="background: linear-gradient(135deg, #ffffff 0%, #d1fae5 100%); border-radius: 16px; padding: 24px; border: 1px solid #a7f3d0;">
        <div style="display: flex; align-items: center; gap: 16px;">
            <div style="width: 56px; height: 56px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-check-circle" style="color: white; font-size: 28px;"></i>
            </div>
            <div>
                <p style="color: #166534; font-size: 13px; margin: 0 0 4px 0; font-weight: 600;">Avis approuvés</p>
                <p style="font-size: 28px; font-weight: 700; color: #15803d; margin: 0;" data-stat="approved">{{ number_format($stats['approved_reviews']) }}</p>
            </div>
        </div>
    </div>

    <div style="background: linear-gradient(135deg, #ffffff 0%, #ddd6fe 100%); border-radius: 16px; padding: 24px; border: 1px solid #c4b5fd;">
        <div style="display: flex; align-items: center; gap: 16px;">
            <div style="width: 56px; height: 56px; background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-chart-line" style="color: white; font-size: 28px;"></i>
            </div>
            <div>
                <p style="color: #5b21b6; font-size: 13px; margin: 0 0 4px 0; font-weight: 600;">Note moyenne</p>
                <p style="font-size: 28px; font-weight: 700; color: #6d28d9; margin: 0;" data-stat="average">{{ $stats['average_rating'] }} <span style="font-size: 16px;">/ 5</span></p>
            </div>
        </div>
    </div>
</div>

<!-- Filtres -->
<div class="card" style="margin-bottom: 24px;">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-filter"></i> Filtres</h3>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('reviews.index') }}" style="display: flex; gap: 12px; flex-wrap: wrap;">
            <input type="text" name="search" class="form-control" style="flex: 1; min-width: 200px;" 
                   placeholder="Rechercher dans les avis..." value="{{ request('search') }}">
            
            <select name="status" class="form-control" style="width: auto; min-width: 150px;">
                <option value="">Tous les statuts</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approuvé</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejeté</option>
            </select>
            
            <select name="rating" class="form-control" style="width: auto; min-width: 150px;">
                <option value="">Toutes les notes</option>
                <option value="5" {{ request('rating') == '5' ? 'selected' : '' }}>★★★★★ (5 étoiles)</option>
                <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>★★★★☆ (4 étoiles)</option>
                <option value="3" {{ request('rating') == '3' ? 'selected' : '' }}>★★★☆☆ (3 étoiles)</option>
                <option value="2" {{ request('rating') == '2' ? 'selected' : '' }}>★★☆☆☆ (2 étoiles)</option>
                <option value="1" {{ request('rating') == '1' ? 'selected' : '' }}>★☆☆☆☆ (1 étoile)</option>
            </select>
            
            <select name="product_id" class="form-control" style="width: auto; min-width: 200px;">
                <option value="">Tous les produits</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                        {{ $product->name }}
                    </option>
                @endforeach
            </select>
            
            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Filtrer</button>
            <a href="{{ route('reviews.index') }}" class="btn btn-secondary"><i class="fas fa-redo"></i> Réinitialiser</a>
        </form>
    </div>
</div>

<!-- Tableau des avis -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-list"></i> Avis clients ({{ $reviews->total() }})</h3>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Produit</th>
                    <th>Client</th>
                    <th>Note</th>
                    <th>Avis</th>
                    <th>Date</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reviews as $review)
                <tr data-review-id="{{ $review->id }}">
                    <td>
                        <strong>{{ $review->product->name }}</strong>
                        <br><small class="text-muted">ID: #{{ $review->product->id }}</small>
                    </td>
                    <td>
                        {{ $review->customer_name }}
                        <br><small class="text-muted">{{ $review->customer_email }}</small>
                    </td>
                    <td>
                        <div style="color: #f59e0b; font-size: 16px;">
                            {!! str_repeat('★', $review->rating) !!}{!! str_repeat('☆', 5 - $review->rating) !!}
                        </div>
                        <small class="text-muted">{{ $review->rating }}/5</small>
                    </td>
                    <td style="max-width: 300px;">
                        <strong>{{ $review->title }}</strong>
                        <br><small>{{ Str::limit($review->comment, 80) }}</small>
                    </td>
                    <td>
                        <small>{{ $review->created_at->format('d/m/Y') }}</small>
                        <br><small class="text-muted">{{ $review->created_at->diffForHumans() }}</small>
                    </td>
                    <td data-status>
                        @if($review->status == 'approved')
                            <span class="badge badge-success"><i class="fas fa-check-circle"></i> Approuvé</span>
                        @elseif($review->status == 'pending')
                            <span class="badge badge-warning"><i class="fas fa-clock"></i> En attente</span>
                        @else
                            <span class="badge badge-danger"><i class="fas fa-times-circle"></i> Rejeté</span>
                        @endif
                    </td>
                    <td>
                        <div class="action-buttons">
                            @if($review->status == 'pending')
                            <form action="{{ route('reviews.approve', $review) }}" method="POST" style="display: inline;" onsubmit="event.preventDefault(); approveReview({{ $review->id }});">
                                @csrf
                                <button type="submit" class="btn-action btn-action-success" title="Approuver l'avis">
                                    <i class="fas fa-check"></i>
                                </button>
                            </form>
                            <form action="{{ route('reviews.reject', $review) }}" method="POST" style="display: inline;" onsubmit="event.preventDefault(); rejectReview({{ $review->id }});">
                                @csrf
                                <button type="submit" class="btn-action btn-action-warning" title="Rejeter l'avis">
                                    <i class="fas fa-times"></i>
                                </button>
                            </form>
                            @endif
                            <form action="{{ route('reviews.destroy', $review) }}" method="POST" style="display: inline;" onsubmit="event.preventDefault(); deleteReview({{ $review->id }});">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-action btn-action-delete" title="Supprimer l'avis">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="empty-state">
                        <i class="fas fa-star"></i>
                        <p>Aucun avis trouvé</p>
                        <small class="text-muted">Les avis des clients apparaîtront ici dès leur soumission.</small>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($reviews->hasPages())
    <div class="card-footer">
        {{ $reviews->links() }}
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
// Update statistics cards
function updateStats(stats) {
    // Update each stat card
    document.querySelector('[data-stat="total"]').textContent = stats.total_reviews.toLocaleString();
    document.querySelector('[data-stat="pending"]').textContent = stats.pending_reviews.toLocaleString();
    document.querySelector('[data-stat="approved"]').textContent = stats.approved_reviews.toLocaleString();
    document.querySelector('[data-stat="average"]').innerHTML = stats.average_rating + ' <span style="font-size: 16px;">/ 5</span>';
}

// Approve review
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
            // Update statistics
            updateStats(data.stats);
            
            // Update row
            const row = document.querySelector(`tr[data-review-id="${reviewId}"]`);
            
            // Update status badge
            const statusCell = row.querySelector('[data-status]');
            statusCell.innerHTML = '<span class="badge badge-success"><i class="fas fa-check-circle"></i> Approved</span>';
            
            // Update actions - remove approve/reject buttons
            const actionsCell = row.querySelector('.action-buttons');
            actionsCell.querySelector('form[action*="approve"]')?.remove();
            actionsCell.querySelector('form[action*="reject"]')?.remove();
            
            // Show success message
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: data.message,
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
            title: 'Error!',
            text: 'Failed to approve review',
            toast: true,
            position: 'top-end',
            timer: 3000,
            showConfirmButton: false
        });
    });
}

// Reject review
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
            // Update statistics
            updateStats(data.stats);
            
            // Update row
            const row = document.querySelector(`tr[data-review-id="${reviewId}"]`);
            
            // Update status badge
            const statusCell = row.querySelector('[data-status]');
            statusCell.innerHTML = '<span class="badge badge-danger"><i class="fas fa-times-circle"></i> Rejected</span>';
            
            // Update actions - remove approve/reject buttons
            const actionsCell = row.querySelector('.action-buttons');
            actionsCell.querySelector('form[action*="approve"]')?.remove();
            actionsCell.querySelector('form[action*="reject"]')?.remove();
            
            // Show success message
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: data.message,
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
            title: 'Error!',
            text: 'Failed to reject review',
            toast: true,
            position: 'top-end',
            timer: 3000,
            showConfirmButton: false
        });
    });
}

// Delete review
function deleteReview(reviewId) {
    Swal.fire({
        title: 'Delete Review?',
        text: "This action cannot be undone!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
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
                    // Update statistics
                    updateStats(data.stats);
                    
                    // Remove row with animation
                    const row = document.querySelector(`tr[data-review-id="${reviewId}"]`);
                    row.style.transition = 'opacity 0.3s';
                    row.style.opacity = '0';
                    setTimeout(() => {
                        row.remove();
                        
                        // Check if table is empty
                        const tbody = document.querySelector('table tbody');
                        if (tbody.children.length === 0) {
                            tbody.innerHTML = `
                                <tr>
                                    <td colspan="7" class="empty-state">
                                        <i class="fas fa-star"></i>
                                        <p>No reviews found</p>
                                        <small class="text-muted">Customer reviews will appear here once submitted</small>
                                    </td>
                                </tr>
                            `;
                        }
                    }, 300);
                    
                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: data.message,
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
                    title: 'Error!',
                    text: 'Failed to delete review',
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

