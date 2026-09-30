<tr>
    <td>
        <div class="d-flex align-items-center gap-3" style="{{ $level > 0 ? 'padding-left: ' . ($level * 40) . 'px;' : '' }}">
            @if($level > 0)
                <span class="text-muted fw-bold" style="opacity: 0.5;">└─</span>
            @endif
            <div class="brand-avatar">
                @if($category->image)
                    <img src="{{ $category->image_url }}" alt="{{ $category->name }}" onerror="this.onerror=null; this.src='{{ asset('images/camera/cat_cameras.jpg') }}';">
                @elseif($category->icon)
                    <i class="{{ $category->icon }}"></i>
                @else
                    <i class="fas fa-folder"></i>
                @endif
            </div>
            <div>
                <div class="fw-bold text-dark">{{ $category->name }}</div>
                @if($category->description)
                    <div class="text-muted small" style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                        {{ $category->description }}
                    </div>
                @endif
                @if($category->hasChildren())
                    <div class="mt-1" style="font-size: 0.65rem; color: var(--primary-color); font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">
                        <i class="fas fa-sitemap me-1"></i> {{ $category->children->count() }} sous-catégorie(s)
                    </div>
                @endif
            </div>
        </div>
    </td>
    <td>
        <span class="badge bg-light text-secondary font-monospace" style="font-size: 0.7rem; border: 1px solid #e2e8f0;">
            {{ $category->slug }}
        </span>
    </td>
    <td class="text-center">
        <span class="brand-badge info">
            <i class="fas fa-box me-1"></i> {{ $category->products_count }} {{ $category->products_count > 1 ? 'Produits' : 'Produit' }}
        </span>
    </td>
    <td class="text-center">
        @php
            $catStatus = strtolower($category->status);
            $catStatusText = match($catStatus) {
                'active', 'actif' => 'Actif',
                'inactive', 'inactif' => 'Inactif',
                default => ucfirst($category->status)
            };
        @endphp
        <span class="brand-badge {{ $catStatus === 'active' || $catStatus === 'actif' ? 'success' : 'secondary' }}">
            {{ $catStatusText }}
        </span>
    </td>
    <td style="padding-right: 1.5rem;">
        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('categories.edit', $category) }}" class="btn-action-icon" title="Modifier la catégorie">
                <i class="fas fa-edit"></i>
            </a>
            @if($category->products_count == 0 && !$category->hasChildren())
                <form method="POST"
                      action="{{ route('categories.destroy', $category->id) }}"
                      style="display: inline;"
                      data-confirm-delete="true"
                      data-item-type="category"
                      data-item-name="{{ $category->name }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-action-icon danger" title="Supprimer">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
            @endif
        </div>
    </td>
</tr>
