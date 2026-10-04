@extends('layouts.app')

@section('title', 'Gestion des produits')

@section('content')
    <!-- En-tête de page -->
    <div class="brand-header">
        <div>
            <h1 class="brand-title">
                <div class="brand-header-icon">
                    <i class="fas fa-box"></i>
                </div>
                Gestion des produits
            </h1>
            <p class="brand-subtitle">Gérez votre catalogue de produits, les tarifs et la disponibilité en stock</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('products.image-quality') }}" class="btn btn-outline-primary shadow-sm rounded-pill px-3 py-2 d-inline-flex align-items-center gap-2" style="border-color: #8b5cf6; color: #6d28d9; background: #faf5ff; font-weight: 600; text-decoration: none;">
                <i class="fas fa-sparkles text-warning"></i> Studio 4K Hub
                @if(!empty($qualityStats['needs_upgrade']) && $qualityStats['needs_upgrade'] > 0)
                    <span class="badge rounded-pill bg-warning text-dark px-2" style="font-size: 0.72rem;">{{ $qualityStats['needs_upgrade'] }} à améliorer</span>
                @endif
            </a>
            <a href="{{ route('products.create') }}" class="btn-brand-primary">
                <i class="fas fa-plus me-2"></i> Ajouter un nouveau produit
            </a>
        </div>
    </div>

    <!-- Barre de filtres -->
    <div class="brand-filter-bar">
        <form method="GET" action="{{ route('products.index') }}" class="d-flex align-items-center gap-3 flex-wrap">
            <div class="brand-search-wrapper">
                <i class="fas fa-search"></i>
                <input type="text" name="search" class="form-control" 
                       placeholder="Rechercher par nom, SKU..."
                       value="{{ request('search') }}">
            </div>
            
            <select name="category" class="form-select w-auto">
                <option value="">Toutes les catégories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>

            <select name="quality" class="form-select w-auto">
                <option value="">Toutes les résolutions</option>
                <option value="needs_upgrade" {{ request('quality') == 'needs_upgrade' ? 'selected' : '' }}>
                    ⚠️ À améliorer (< 1200px) ({{ $qualityStats['needs_upgrade'] ?? 0 }})
                </option>
                <option value="4k" {{ request('quality') == '4k' ? 'selected' : '' }}>
                    ✨ 4K Ultra-HD ({{ $qualityStats['four_k'] ?? 0 }})
                </option>
                <option value="fhd" {{ request('quality') == 'fhd' ? 'selected' : '' }}>
                    📷 Full HD ({{ $qualityStats['fhd'] ?? 0 }})
                </option>
                <option value="missing" {{ request('quality') == 'missing' ? 'selected' : '' }}>
                    ❌ Sans image / Placeholder
                </option>
            </select>

            <button type="submit" class="btn-brand-primary">
                <i class="fas fa-filter me-1"></i> Filtrer
            </button>
            <a href="{{ route('products.index') }}" class="btn-brand-light" title="Réinitialiser">
                <i class="fas fa-redo"></i>
            </a>
            
            @if(request('search') || request('category'))
            <div class="ms-2">
                <span class="badge bg-light text-secondary px-3 py-2" style="border-radius: 8px;">
                    {{ $products->total() }} résultat(s) trouvé(s)
                </span>
            </div>
            @endif

            <div class="ms-auto d-flex gap-2">
                <button type="button" class="btn-brand-outline" data-bs-toggle="modal" data-bs-target="#importModal">
                    <i class="fas fa-file-upload me-2" style="color: var(--primary-color)"></i>Importer Excel
                </button>
                <a href="{{ route('export.products') }}" class="btn-brand-outline">
                    <i class="fas fa-file-csv me-2" style="color: var(--success-color)"></i>Exporter CSV
                </a>
            </div>
        </form>
    </div>

    <!-- Modal Import -->
    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-0 bg-light">
                    <h5 class="modal-title fw-bold" id="importModalLabel">
                        <i class="fas fa-file-upload me-2 text-primary"></i>Importer des produits depuis Excel
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <form action="{{ route('products.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-4">
                            <label class="form-label fw-bold">Sélectionner le fichier Excel</label>
                            <input type="file" name="file" class="form-control" accept=".xlsx,.xls,.csv" required>
                            <div class="form-text">Formats pris en charge : .xlsx, .xls, .csv (max 10 Mo)</div>
                        </div>
                        
                        <div class="alert alert-info border-0 rounded-3 mb-0">
                            <h6 class="fw-bold mb-2"><i class="fas fa-info-circle me-2"></i>Colonnes requises</h6>
                            <p class="mb-2 small">Votre fichier doit comporter ces en-têtes de colonnes :</p>
                            <code class="d-block bg-white p-2 rounded small">name, sku, description, price, cost_price, stock, min_stock, category, status</code>
                            <p class="mb-0 mt-2 small">
                                <a href="{{ route('products.template') }}" class="fw-bold">
                                    <i class="fas fa-download me-1"></i>Télécharger le modèle type
                                </a>
                            </p>
                        </div>
                    </div>
                    <div class="modal-footer border-0 bg-light">
                        <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn-brand-primary">
                            <i class="fas fa-upload me-2"></i>Importer les produits
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Barre d'actions groupées -->
    <div id="bulkActionsBar" class="bulk-actions-bar" style="display: none;">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3">
                <span class="selected-count fw-bold">
                    <i class="fas fa-check-circle me-2"></i>
                    <span id="selectedCount">0</span> produit(s) sélectionné(s)
                </span>
                <button type="button" class="btn btn-sm btn-light" onclick="clearSelection()">
                    <i class="fas fa-times me-1"></i>Annuler
                </button>
            </div>
            <div class="d-flex align-items-center gap-2">
                <div class="input-group input-group-sm" style="width: 140px;" id="stockAmountGroup" style="display: none;">
                    <span class="input-group-text">Qté</span>
                    <input type="number" id="stockAmount" class="form-control" value="10" min="1" max="9999">
                </div>
                
                <div class="btn-group">
                    <button type="button" class="btn btn-success btn-sm" onclick="executeBulkAction('increase_stock')" title="Ajouter du stock">
                        <i class="fas fa-plus me-1"></i>Ajouter stock
                    </button>
                    <button type="button" class="btn btn-warning btn-sm" onclick="executeBulkAction('decrease_stock')" title="Retirer du stock">
                        <i class="fas fa-minus me-1"></i>Retirer stock
                    </button>
                </div>
                <button type="button" class="btn btn-info btn-sm text-white" onclick="executeBulkAction('duplicate')" title="Dupliquer">
                    <i class="fas fa-copy me-1"></i>Dupliquer
                </button>
                <button type="button" class="btn btn-sm text-white" onclick="bulkUpgradeTo4k()" style="background: linear-gradient(135deg, #6366f1, #8b5cf6); border: none;" title="Mettre à niveau la sélection vers la qualité Studio 4K">
                    <i class="fas fa-sparkles me-1 text-warning"></i>Mettre à niveau 4K
                </button>
                <div class="btn-group">
                    <button type="button" class="btn btn-secondary btn-sm" onclick="executeBulkAction('activate')" title="Activer">
                        <i class="fas fa-check me-1"></i>Activer
                    </button>
                    <button type="button" class="btn btn-secondary btn-sm" onclick="executeBulkAction('deactivate')" title="Désactiver">
                        <i class="fas fa-ban me-1"></i>Désactiver
                    </button>
                </div>
                <button type="button" class="btn btn-danger btn-sm" onclick="executeBulkAction('delete')" title="Supprimer">
                    <i class="fas fa-trash me-1"></i>Supprimer
                </button>
            </div>
        </div>
    </div>

    <!-- Products Table -->
    <div class="brand-table-card">
        <div class="table-responsive">
            <table class="brand-table">
                <thead>
                    <tr>
                        <th style="width: 50px; padding-left: 1.5rem;">
                            <input type="checkbox" class="form-check-input" id="selectAll" onchange="toggleSelectAll(this)">
                        </th>
                        <th>Détails du produit</th>
                        <th>SKU</th>
                        <th>Catégorie</th>
                        <th>Prix</th>
                        <th>Stock & Inventaire</th>
                        <th>Statut</th>
                        <th class="text-end" style="padding-right: 1.5rem;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr data-product-id="{{ $product->id }}" id="product-row-{{ $product->id }}">
                        <td style="padding-left: 1.5rem;">
                            <input type="checkbox" class="form-check-input product-checkbox" 
                                   value="{{ $product->id }}" 
                                   onchange="updateSelection()">
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="position-relative" style="flex-shrink: 0;">
                                    <div class="brand-avatar" id="prod-avatar-wrap-{{ $product->id }}">
                                        <img id="prod-img-{{ $product->id }}" src="{{ $product->thumbnail }}" alt="{{ $product->name }}" onerror="this.onerror=null; this.src='{{ asset('images/camera/cat_cameras.jpg') }}';">
                                    </div>
                                    <button type="button" 
                                            class="btn btn-sm btn-light border shadow-sm position-absolute bottom-0 end-0 p-0 rounded-circle" 
                                            style="width: 20px; height: 20px; transform: translate(20%, 20%); display: flex; align-items: center; justify-content: center; background: #ffffff; z-index: 2;" 
                                            onclick="open4kFinderModal({{ $product->id }}, '{{ addslashes($product->name) }}', '{{ $product->thumbnail }}', '{{ $product->image_width ? $product->image_width . '×' . $product->image_height : 'SD' }}')"
                                            title="Trouver la meilleure image 4K">
                                        <i class="fas fa-sparkles text-warning" style="font-size: 0.6rem;"></i>
                                    </button>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark d-flex align-items-center gap-2 flex-wrap">
                                        <span>{{ $product->name }}</span>
                                        <span id="prod-badge-{{ $product->id }}">{!! $product->image_quality_badge !!}</span>
                                    </div>
                                    @if($product->description)
                                        <div class="text-muted small" style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                            {{ $product->description }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-light text-secondary font-monospace" style="font-size: 0.7rem; border: 1px solid #e2e8f0;">
                                {{ $product->sku }}
                            </span>
                        </td>
                        <td>
                            @if($product->category_name)
                                <span class="brand-badge primary">{{ $product->category_name }}</span>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ currency($product->price) }}</div>
                            @if($product->cost_price)
                                <div class="text-muted" style="font-size: 0.7rem;">Coût : {{ currency($product->cost_price) }}</div>
                            @endif
                        </td>
                        <td>
                            @php
                                $stock = $product->stock;
                                $min = $product->min_stock ?? 5;
                                $badgeClass = 'success';
                                $badgeText = 'En stock : ' . $stock;
                                if ($stock <= 0) {
                                    $badgeClass = 'danger';
                                    $badgeText = 'Rupture de stock';
                                } elseif ($stock <= $min) {
                                    $badgeClass = 'warning';
                                    $badgeText = 'Stock faible : ' . $stock;
                                }
                            @endphp
                            <span class="brand-badge {{ $badgeClass }}">{{ $badgeText }}</span>
                        </td>
                        <td>
                            @php
                                $st = strtolower($product->status);
                                $statusClass = match($st) {
                                    'active', 'actif' => 'success',
                                    'inactive', 'inactif' => 'danger',
                                    default => 'info'
                                };
                                $statusText = match($st) {
                                    'active' => 'Actif',
                                    'inactive' => 'Inactif',
                                    default => ucfirst($product->status)
                                };
                            @endphp
                            <span class="brand-badge {{ $statusClass }}">
                                {{ $statusText }}
                            </span>
                        </td>
                        <td style="padding-right: 1.5rem;">
                            <div class="d-flex justify-content-end gap-2">
                                <button type="button" 
                                        class="btn-action-icon" 
                                        style="color: #8b5cf6;" 
                                        title="Trouver la meilleure image 4K"
                                        onclick="open4kFinderModal({{ $product->id }}, '{{ addslashes($product->name) }}', '{{ $product->thumbnail }}', '{{ $product->image_width ? $product->image_width . '×' . $product->image_height : 'SD' }}')">
                                    <i class="fas fa-sparkles"></i>
                                </button>
                                <a href="{{ route('products.edit', $product) }}" class="btn-action-icon" title="Modifier le produit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" 
                                      action="{{ route('products.destroy', $product->id) }}" 
                                      style="display: inline;"
                                      data-confirm-delete="true"
                                      data-item-type="product"
                                      data-item-name="{{ $product->name }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action-icon danger" title="Supprimer le produit">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8">
                            <div class="text-center py-5">
                                <div class="brand-avatar mx-auto mb-3" style="width: 64px; height: 64px; font-size: 24px;">
                                    <i class="fas fa-search"></i>
                                </div>
                                <h5 class="fw-bold text-dark">Aucun produit trouvé</h5>
                                <p class="text-muted">Essayez d'ajuster vos critères de recherche ou vos filtres.</p>
                                @if(request('search') || request('category'))
                                    <a href="{{ route('products.index') }}" class="btn-brand-primary mt-3">
                                        Effacer tous les filtres
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($products->hasPages())
        <div class="px-4 py-3 border-top">
            {{ $products->links() }}
        </div>
        @endif
    </div>

@push('styles')
<style>
    .bulk-actions-bar {
        background: linear-gradient(135deg, var(--primary-color, #00BFA6) 0%, var(--secondary-color, #00A896) 100%);
        color: white;
        padding: 14px 24px;
        border-radius: var(--border-radius, 12px);
        margin-bottom: 20px;
        box-shadow: 0 4px 15px rgba(0, 191, 166, 0.25);
        animation: slideDown 0.3s ease-out;
    }
    
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .bulk-actions-bar .selected-count {
        color: white;
        font-size: 0.95rem;
    }
    
    .bulk-actions-bar .btn-light {
        background: rgba(255,255,255,0.2);
        border: none;
        color: white;
        border-radius: 8px;
        font-weight: 500;
    }
    
    .bulk-actions-bar .btn-light:hover {
        background: rgba(255,255,255,0.35);
        color: white;
    }
    
    .bulk-actions-bar .input-group-text {
        background: rgba(255,255,255,0.25);
        border: none;
        color: white;
        font-weight: 500;
        border-radius: 8px 0 0 8px;
    }
    
    .bulk-actions-bar .form-control {
        background: rgba(255,255,255,0.95);
        border: none;
        border-radius: 0 8px 8px 0;
        font-weight: 500;
    }
    
    .bulk-actions-bar .btn {
        border-radius: 8px;
        font-weight: 500;
        font-size: 0.85rem;
        padding: 6px 12px;
        border: none;
    }
    
    .bulk-actions-bar .btn-group .btn {
        border-radius: 0;
    }
    
    .bulk-actions-bar .btn-group .btn:first-child {
        border-radius: 8px 0 0 8px;
    }
    
    .bulk-actions-bar .btn-group .btn:last-child {
        border-radius: 0 8px 8px 0;
    }
    
    .bulk-actions-bar .btn-success {
        background: #10b981;
    }
    
    .bulk-actions-bar .btn-warning {
        background: #f59e0b;
        color: white;
    }
    
    .bulk-actions-bar .btn-info {
        background: #3b82f6;
    }
    
    .bulk-actions-bar .btn-secondary {
        background: rgba(255,255,255,0.2);
        color: white;
    }
    
    .bulk-actions-bar .btn-secondary:hover {
        background: rgba(255,255,255,0.35);
        color: white;
    }
    
    .bulk-actions-bar .btn-danger {
        background: #ef4444;
    }
    
    .product-checkbox {
        width: 18px;
        height: 18px;
        cursor: pointer;
        border-radius: 4px;
    }
    
    .product-checkbox:checked {
        background-color: var(--primary-color, #00BFA6);
        border-color: var(--primary-color, #00BFA6);
    }
    
    tr.selected {
        background-color: rgba(0, 191, 166, 0.08) !important;
    }
    
    #selectAll {
        width: 18px;
        height: 18px;
        cursor: pointer;
        border-radius: 4px;
    }
    
    #selectAll:checked {
        background-color: var(--primary-color, #00BFA6);
        border-color: var(--primary-color, #00BFA6);
    }
</style>
@endpush

@push('scripts')
<script>
    let selectedProducts = [];
    
    function toggleSelectAll(checkbox) {
        const checkboxes = document.querySelectorAll('.product-checkbox');
        checkboxes.forEach(cb => {
            cb.checked = checkbox.checked;
        });
        updateSelection();
    }
    
    function updateSelection() {
        selectedProducts = [];
        const checkboxes = document.querySelectorAll('.product-checkbox:checked');
        checkboxes.forEach(cb => {
            selectedProducts.push(parseInt(cb.value));
            cb.closest('tr').classList.add('selected');
        });
        
        // Remove selected class from unchecked rows
        document.querySelectorAll('.product-checkbox:not(:checked)').forEach(cb => {
            cb.closest('tr').classList.remove('selected');
        });
        
        // Update count display
        document.getElementById('selectedCount').textContent = selectedProducts.length;
        
        // Show/hide bulk actions bar
        const bulkActionsBar = document.getElementById('bulkActionsBar');
        if (selectedProducts.length > 0) {
            bulkActionsBar.style.display = 'block';
        } else {
            bulkActionsBar.style.display = 'none';
        }
        
        // Update select all checkbox state
        const allCheckboxes = document.querySelectorAll('.product-checkbox');
        const selectAllCheckbox = document.getElementById('selectAll');
        if (allCheckboxes.length > 0 && selectedProducts.length === allCheckboxes.length) {
            selectAllCheckbox.checked = true;
            selectAllCheckbox.indeterminate = false;
        } else if (selectedProducts.length > 0) {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = true;
        } else {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = false;
        }
    }
    
    function clearSelection() {
        document.querySelectorAll('.product-checkbox').forEach(cb => {
            cb.checked = false;
        });
        document.getElementById('selectAll').checked = false;
        updateSelection();
    }
    
    function executeBulkAction(action) {
        if (selectedProducts.length === 0) {
            Swal.fire('No Selection', 'Please select at least one product.', 'warning');
            return;
        }
        
        const actionLabels = {
            'delete': 'delete',
            'duplicate': 'duplicate',
            'increase_stock': 'increase stock for',
            'decrease_stock': 'decrease stock for',
            'activate': 'activate',
            'deactivate': 'deactivate'
        };
        
        const confirmMessage = `Are you sure you want to ${actionLabels[action]} ${selectedProducts.length} product(s)?`;
        
        Swal.fire({
            title: 'Confirm Action',
            text: confirmMessage,
            icon: action === 'delete' ? 'warning' : 'question',
            showCancelButton: true,
            confirmButtonColor: action === 'delete' ? '#dc3545' : '#4F46E5',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, proceed!'
        }).then((result) => {
            if (result.isConfirmed) {
                performBulkAction(action);
            }
        });
    }
    
    function performBulkAction(action) {
        const stockAmount = document.getElementById('stockAmount').value || 10;
        
        // Show loading
        Swal.fire({
            title: 'Processing...',
            text: 'Please wait while we process your request.',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        fetch('{{ route('products.bulk-action') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                action: action,
                product_ids: selectedProducts,
                stock_amount: parseInt(stockAmount)
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    title: 'Success!',
                    text: data.message,
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    // Reload the page to reflect changes
                    window.location.reload();
                });
            } else {
                Swal.fire('Error', data.message || 'An error occurred.', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', 'An unexpected error occurred.', 'error');
        });
    }

    // 4K Finder Modal State
    let current4kProductId = null;

    function open4kFinderModal(productId, productName, currentImg, currentRes) {
        current4kProductId = productId;
        document.getElementById('modal4kSubtitle').innerText = productName;
        document.getElementById('modalCurrentImg').src = currentImg;
        document.getElementById('modalCurrentRes').innerText = currentRes || 'Résolution standard';
        document.getElementById('query4k').value = productName;
        document.getElementById('modal4k').style.display = 'flex';
        document.body.style.overflow = 'hidden';

        trigger4kSearch();
    }

    function close4kImageModal() {
        document.getElementById('modal4k').style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    function trigger4kSearch() {
        if (!current4kProductId) return;

        const query = document.getElementById('query4k').value.trim();
        if (!query) return;

        const loading = document.getElementById('loading4k');
        const grid = document.getElementById('grid4k');
        const btn = document.getElementById('btnRun4kSearch');

        loading.style.display = 'block';
        grid.innerHTML = '';
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Recherche...';

        fetch(`/products/${current4kProductId}/search-4k-images?q=` + encodeURIComponent(query), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            loading.style.display = 'none';
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-bolt me-1"></i> Rechercher';

            if (!data.candidates || data.candidates.length === 0) {
                grid.innerHTML = `
                    <div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: #64748b;">
                        <i class="fas fa-image" style="font-size: 36px; margin-bottom: 12px; color: #cbd5e1;"></i>
                        <p style="font-weight: 600;">Aucune image studio 4K trouvée pour "${query}"</p>
                        <p style="font-size: 13px;">Essayez de simplifier le nom avec la marque et le numéro de modèle.</p>
                    </div>
                `;
                return;
            }

            data.candidates.forEach((cand) => {
                const card = document.createElement('div');
                card.style.cssText = "background: #ffffff; border: 1.5px solid #e2e8f0; border-radius: 12px; overflow: hidden; display: flex; flex-direction: column; transition: all 0.2s; box-shadow: 0 2px 8px rgba(0,0,0,0.04);";
                card.onmouseenter = () => { card.style.borderColor = '#6366f1'; card.style.boxShadow = '0 8px 20px rgba(99, 102, 241, 0.15)'; };
                card.onmouseleave = () => { card.style.borderColor = '#e2e8f0'; card.style.boxShadow = '0 2px 8px rgba(0,0,0,0.04)'; };

                const is4k = cand.width >= 2000;
                const badgeColor = is4k ? "background: linear-gradient(135deg, #10b981, #059669); color: white;" : "background: #f1f5f9; color: #475569;";

                card.innerHTML = `
                    <div style="position: relative; width: 100%; height: 210px; background: #f8fafc; display: flex; align-items: center; justify-content: center; overflow: hidden; padding: 10px;">
                        <img src="${cand.url}" alt="Preview" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                        <span style="position: absolute; top: 8px; left: 8px; ${badgeColor} font-size: 11px; font-weight: 700; padding: 3px 8px; border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                            ${cand.width}×${cand.height} ${is4k ? '4K UHD' : 'HD'}
                        </span>
                        <span style="position: absolute; bottom: 8px; right: 8px; background: rgba(15,23,42,0.7); color: white; font-size: 10px; padding: 2px 6px; border-radius: 4px;">
                            ${cand.source}
                        </span>
                    </div>
                    <div style="padding: 12px; display: flex; flex-direction: column; gap: 8px; background: #ffffff; border-top: 1px solid #f1f5f9;">
                        <button type="button" onclick="applySelected4kImage('${cand.url.replace(/'/g, "\\'")}', this)" style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); color: white; border: none; border-radius: 8px; padding: 8px 12px; font-size: 12px; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px; transition: all 0.2s;">
                            <i class="fas fa-check-circle"></i> Choisir cette image 4K
                        </button>
                    </div>
                `;
                grid.appendChild(card);
            });
        })
        .catch(err => {
            loading.style.display = 'none';
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-bolt me-1"></i> Rechercher';
            grid.innerHTML = `<div style="grid-column: 1 / -1; color: #ef4444; padding: 20px; text-align: center;">Erreur de recherche : ${err.message}</div>`;
        });
    }

    function applySelected4kImage(imageUrl, button) {
        const origHtml = button.innerHTML;
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Téléchargement 4K...';

        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || document.querySelector('input[name="_token"]')?.value;

        fetch(`/products/${current4kProductId}/apply-4k-image`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ image_url: imageUrl })
        })
        .then(res => res.json())
        .then(data => {
            button.disabled = false;
            button.innerHTML = origHtml;

            if (data.success) {
                // Update product row live
                const prodImg = document.getElementById(`prod-img-${current4kProductId}`);
                if (prodImg) {
                    prodImg.src = data.image_url + '?t=' + new Date().getTime();
                }

                const prodBadge = document.getElementById(`prod-badge-${current4kProductId}`);
                if (prodBadge && data.badge_html) {
                    prodBadge.innerHTML = data.badge_html;
                }

                close4kImageModal();

                Swal.fire({
                    title: 'Image 4K Appliquée !',
                    text: `Résolution : ${data.width}×${data.height} pixels (${data.quality.toUpperCase()})`,
                    icon: 'success',
                    timer: 2500,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            } else {
                Swal.fire('Erreur', data.message || 'Impossible d\'appliquer l\'image 4K', 'error');
            }
        })
        .catch(err => {
            button.disabled = false;
            button.innerHTML = origHtml;
            Swal.fire('Erreur', 'Erreur de téléchargement : ' + err.message, 'error');
        });
    }

    function bulkUpgradeTo4k() {
        if (!selectedProducts || selectedProducts.length === 0) {
            Swal.fire('Aucun produit sélectionné', 'Veuillez cocher au moins un produit à mettre à niveau.', 'info');
            return;
        }

        Swal.fire({
            title: 'Mise à niveau 4K Studio',
            text: `Voulez-vous rechercher et appliquer automatiquement les meilleures images 4K Ultra-HD pour les ${selectedProducts.length} produit(s) sélectionné(s) ?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#4f46e5',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '🚀 Oui, mettre à niveau en 4K !',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Traitement Studio 4K en cours...',
                    text: 'Téléchargement des masters 4K Ultra-HD sans compression...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || document.querySelector('input[name="_token"]')?.value;

                fetch("{{ route('products.bulk-upgrade-4k') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ product_ids: selectedProducts })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Mise à niveau terminée !',
                            text: data.message,
                            icon: 'success'
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire('Erreur', data.message || 'Une erreur est survenue.', 'error');
                    }
                })
                .catch(err => {
                    Swal.fire('Erreur', 'Une erreur inattendue est survenue : ' + err.message, 'error');
                });
            }
        });
    }
</script>
@endpush

<!-- Modal 4K Image Finder -->
<div id="modal4k" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.75); backdrop-filter: blur(8px); z-index: 9999; overflow-y: auto; padding: 20px; align-items: center; justify-content: center;">
    <div style="background: #ffffff; width: 100%; max-width: 900px; border-radius: 16px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); overflow: hidden; margin: auto; border: 1px solid rgba(255, 255, 255, 0.2);">
        <!-- Modal Header -->
        <div style="background: linear-gradient(135deg, #1e1b4b 0%, #312e81 50%, #4338ca 100%); color: white; padding: 20px 24px; display: flex; align-items: center; justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 14px;">
                <div style="width: 44px; height: 44px; border-radius: 12px; background: rgba(255, 255, 255, 0.15); display: flex; align-items: center; justify-content: center; font-size: 20px; color: #fbbf24; border: 1px solid rgba(255, 255, 255, 0.2);">
                    <i class="fas fa-sparkles"></i>
                </div>
                <div>
                    <h4 style="margin: 0; font-size: 18px; font-weight: 700; letter-spacing: -0.01em;">Studio Recherche 4K Ultra-HD</h4>
                    <p style="margin: 3px 0 0; font-size: 13px; color: #c7d2fe;" id="modal4kSubtitle">Sélectionnez la meilleure image studio en haute résolution</p>
                </div>
            </div>
            <button type="button" onclick="close4kImageModal()" style="background: rgba(255,255,255,0.1); border: none; color: white; width: 34px; height: 34px; border-radius: 8px; font-size: 16px; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Current Image & Search Bar -->
        <div style="padding: 16px 24px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; flex-direction: column; gap: 14px;">
            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <img id="modalCurrentImg" src="" alt="Current" style="width: 46px; height: 46px; object-fit: contain; background: white; border: 1px solid #cbd5e1; border-radius: 8px; padding: 2px;">
                    <div>
                        <div style="font-size: 11px; text-transform: uppercase; color: #64748b; font-weight: 700; letter-spacing: 0.05em;">Image actuelle</div>
                        <div id="modalCurrentRes" style="font-size: 13px; font-weight: 600; color: #334155;">-</div>
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 8px; flex-grow: 1; max-width: 500px;">
                    <input type="text" id="query4k" class="form-control" placeholder="Rechercher par nom de produit..." style="border-radius: 10px; font-size: 13px; padding: 9px 14px;">
                    <button type="button" id="btnRun4kSearch" onclick="trigger4kSearch()" class="btn btn-primary" style="background: #4f46e5; border-color: #4f46e5; border-radius: 10px; padding: 9px 18px; font-size: 13px; font-weight: 600; white-space: nowrap;">
                        <i class="fas fa-bolt me-1"></i> Rechercher
                    </button>
                </div>
            </div>
        </div>

        <!-- Candidates Grid -->
        <div style="padding: 24px; max-height: 520px; overflow-y: auto;">
            <div id="loading4k" style="display: none; text-align: center; padding: 50px 20px;">
                <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status"></div>
                <p style="margin-top: 16px; font-weight: 600; color: #475569; font-size: 15px;">Exploration des masters 4K Ultra-HD...</p>
                <p style="font-size: 13px; color: #94a3b8;">Extraction sans compression depuis les serveurs officiels</p>
            </div>
            <div id="grid4k" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 18px;">
                <!-- Candidate cards injected dynamically -->
            </div>
        </div>

        <!-- Modal Footer -->
        <div style="padding: 14px 24px; background: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between;">
            <div style="font-size: 12px; color: #64748b;">
                <i class="fas fa-check-shield text-success me-1"></i> Qualité studio vérifiée &bull; Zéro filigrane &bull; Résolution garantie
            </div>
            <button type="button" onclick="close4kImageModal()" class="btn btn-secondary btn-sm rounded-pill px-4">
                Fermer
            </button>
        </div>
    </div>
</div>
@endsection

