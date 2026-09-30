@extends('layouts.app')

@section('title', 'Gestion des pages')

@section('content')
    <div class="brand-header">
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

    <div class="brand-table-card">
        <div class="table-responsive">
            <table class="brand-table">
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
                        <td style="padding-right: 1.5rem;">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('pages.edit', $page) }}" class="btn-action-icon" title="Modifier la page">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" action="{{ route('pages.destroy', $page) }}" 
                                      style="display: inline;"
                                      data-confirm-delete="true"
                                      data-item-type="page"
                                      data-item-name="{{ $page->title }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action-icon danger" title="Supprimer">
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
@endsection
