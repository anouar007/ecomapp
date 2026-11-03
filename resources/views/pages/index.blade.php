@extends('layouts.app')

@section('title', 'Gestion des pages')

@section('content')
    <div class="brand-header mb-3 mb-md-4">
        <div>
            <h1 class="brand-title">
                <div class="brand-header-icon">
                    <i class="fas fa-file-alt"></i>
                </div>
                Gestion des pages
            </h1>
            <p class="brand-subtitle">Créez et gérez les pages visibles par vos clients sur votre site web</p>
        </div>
        <a href="{{ route('pages.create') }}" class="btn-brand-primary">
            <i class="fas fa-plus me-2"></i> Créer une nouvelle page
        </a>
    </div>

    <!-- Desktop Table (d-none d-md-block, Zero horizontal scroll) -->
    <div class="brand-table-card d-none d-md-block shadow-sm rounded-4 overflow-hidden bg-white mb-4">
        <div class="table-responsive">
            <table class="brand-table mb-0 align-middle">
                <thead>
                    <tr>
                        <th style="padding-left: 1.5rem;">Titre de la page</th>
                        <th>Lien (Slug)</th>
                        <th class="text-center">Statut</th>
                        <th>Dernière mise à jour</th>
                        <th class="text-end" style="padding-right: 1.5rem;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pages as $page)
                    <tr>
                        <td style="padding-left: 1.5rem;">
                            <div class="fw-bold text-dark">{{ $page->title }}</div>
                            <div class="text-muted small">Disposition : {{ ucfirst($page->layout) }}</div>
                        </td>
                        <td>
                            <a href="{{ url($page->slug) }}" target="_blank" class="text-primary text-decoration-none">
                                /{{ $page->slug }} <i class="fas fa-external-link-alt ms-1 small"></i>
                            </a>
                        </td>
                        <td class="text-center">
                            <span class="brand-badge {{ $page->is_published ? 'success' : 'warning' }}">
                                {{ $page->is_published ? 'Publiée' : 'Brouillon' }}
                            </span>
                        </td>
                        <td>
                            <div class="text-muted small">{{ $page->updated_at->format('d/m/Y H:i') }}</div>
                        </td>
                        <td style="padding-right: 1.5rem;" class="text-end">
                            <div class="action-buttons-group justify-content-end">
                                <a href="{{ route('pages.edit', $page) }}" class="action-btn action-btn-edit" title="Modifier la page">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" action="{{ route('pages.destroy', $page) }}" 
                                      style="display: inline;"
                                      data-confirm-delete="true"
                                      data-item-type="page"
                                      data-item-name="{{ $page->title }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn action-btn-delete" title="Supprimer">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5">
                            <div class="text-center py-5">
                                <div class="brand-avatar mx-auto mb-3" style="width: 64px; height: 64px; font-size: 24px;">
                                    <i class="fas fa-file-code text-muted"></i>
                                </div>
                                <h5 class="fw-bold text-dark">Aucune page créée pour le moment</h5>
                                <p class="text-muted">Commencez par créer votre première page client.</p>
                                <a href="{{ route('pages.create') }}" class="btn-brand-primary mt-2">
                                    Créer une première page
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($pages->hasPages())
        <div class="px-4 py-3 border-top">
            {{ $pages->links() }}
        </div>
        @endif
    </div>

    <!-- Mobile Touch Cards (d-block d-md-none, Zero horizontal scroll) -->
    <div class="d-block d-md-none mb-4">
        @forelse($pages as $page)
        <div class="card border rounded-3 p-3 mb-2.5 bg-white shadow-xs">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="badge bg-light text-secondary border px-2 py-0.5 small">
                    <i class="fas fa-layer-group me-1"></i>{{ ucfirst($page->layout) }}
                </span>
                <span class="brand-badge {{ $page->is_published ? 'success' : 'warning' }}" style="font-size: 0.72rem;">
                    {{ $page->is_published ? 'Publiée' : 'Brouillon' }}
                </span>
            </div>

            <h6 class="fw-bold text-dark mb-1">{{ $page->title }}</h6>
            <div class="mb-2.5">
                <a href="{{ url($page->slug) }}" target="_blank" class="text-primary text-decoration-none small d-inline-flex align-items-center gap-1 font-monospace">
                    <span>/{{ $page->slug }}</span>
                    <i class="fas fa-external-link-alt" style="font-size: 0.7rem;"></i>
                </a>
            </div>

            <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                <span class="text-muted small" style="font-size: 0.72rem;">
                    <i class="far fa-clock me-1"></i>{{ $page->updated_at->format('d/m/Y') }}
                </span>
                <div class="d-flex gap-1.5">
                    <a href="{{ route('pages.edit', $page) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1" style="font-size: 0.78rem;">
                        <i class="fas fa-edit me-1"></i> Modifier
                    </a>
                    <form method="POST" action="{{ route('pages.destroy', $page) }}" 
                          class="d-inline"
                          data-confirm-delete="true"
                          data-item-type="page"
                          data-item-name="{{ $page->title }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle" style="width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center;" title="Supprimer">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-5 bg-white rounded-3 border">
            <div class="brand-avatar mx-auto mb-3" style="width: 56px; height: 56px; font-size: 22px;">
                <i class="fas fa-file-code text-muted"></i>
            </div>
            <h6 class="fw-bold text-dark">Aucune page créée</h6>
            <p class="text-muted small">Commencez par créer votre première page client.</p>
            <a href="{{ route('pages.create') }}" class="btn btn-brand-primary btn-sm rounded-pill px-3 mt-2">
                Créer une page
            </a>
        </div>
        @endforelse

        @if($pages->hasPages())
        <div class="p-3 d-flex justify-content-center">
            {{ $pages->links() }}
        </div>
        @endif
    </div>
@endsection
