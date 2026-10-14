@extends('layouts.app')

@section('title', 'Modifier le Client - ' . $customer->name)

@section('content')
    <!-- En-tête de page -->
    <div class="brand-header">
        <div class="d-flex align-items-center gap-2.5">
            <div class="brand-header-icon flex-shrink-0">
                <i class="fas fa-user-edit"></i>
            </div>
            <div>
                <h1 class="brand-title">Modifier le Client</h1>
                <p class="brand-subtitle">{{ $customer->name }} &bull; <span class="font-monospace">{{ $customer->customer_code }}</span></p>
            </div>
        </div>
        <div class="brand-header-actions d-flex align-items-center gap-2">
            <a href="{{ route('customers.show', $customer) }}" class="btn btn-outline-secondary shadow-sm rounded-pill px-3 py-2 d-inline-flex align-items-center gap-1.5 fw-semibold" style="font-size: 0.84rem;">
                <i class="fas fa-eye"></i> <span>Voir le Profil</span>
            </a>
            <a href="{{ route('customers.index') }}" class="btn btn-light border shadow-sm rounded-pill px-3 py-2 d-inline-flex align-items-center gap-1.5 fw-semibold text-secondary" style="font-size: 0.84rem;">
                <i class="fas fa-arrow-left"></i> <span>Retour</span>
            </a>
        </div>
    </div>

    <!-- Formulaire de Modification -->
    <form action="{{ route('customers.update', $customer) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <!-- Colonne Principale: Informations & Adresse -->
            <div class="col-12 col-lg-8">
                <!-- Bloc 1: Informations Personnelles & Contact -->
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
                    <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                        <i class="fas fa-id-card text-primary" style="font-size: 1.1rem;"></i>
                        <h6 class="fw-bold text-dark mb-0" style="font-size: 0.95rem;">Informations Générales & Contact</h6>
                    </div>

                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label for="name" class="form-label fw-bold small text-muted text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.04em;">Nom complet <span class="text-danger">*</span></label>
                            <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $customer->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="email" class="form-label fw-bold small text-muted text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.04em;">Adresse E-mail <span class="text-danger">*</span></label>
                            <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $customer->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="phone" class="form-label fw-bold small text-muted text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.04em;">Téléphone</label>
                            <input type="tel" id="phone" name="phone" class="form-control font-monospace @error('phone') is-invalid @enderror" value="{{ old('phone', $customer->phone) }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="ice" class="form-label fw-bold small text-muted text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.04em;">Identifiant Commun de l'Entreprise (ICE)</label>
                            <input type="text" id="ice" name="ice" class="form-control font-monospace @error('ice') is-invalid @enderror" value="{{ old('ice', $customer->ice) }}" placeholder="Ex: 001234567890000">
                            @error('ice')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="date_of_birth" class="form-label fw-bold small text-muted text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.04em;">Date de Naissance</label>
                            <input type="date" id="date_of_birth" name="date_of_birth" class="form-control @error('date_of_birth') is-invalid @enderror" value="{{ old('date_of_birth', $customer->date_of_birth?->format('Y-m-d')) }}">
                            @error('date_of_birth')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Bloc 2: Adresse & Localisation -->
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
                    <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                        <i class="fas fa-map-marker-alt text-danger" style="font-size: 1.1rem;"></i>
                        <h6 class="fw-bold text-dark mb-0" style="font-size: 0.95rem;">Adresse & Coordonnées de Facturation</h6>
                    </div>

                    <div class="row g-3">
                        <div class="col-12">
                            <label for="address" class="form-label fw-bold small text-muted text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.04em;">Adresse complète</label>
                            <textarea id="address" name="address" class="form-control @error('address') is-invalid @enderror" rows="2">{{ old('address', $customer->address) }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="city" class="form-label fw-bold small text-muted text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.04em;">Ville</label>
                            <input type="text" id="city" name="city" class="form-control @error('city') is-invalid @enderror" value="{{ old('city', $customer->city) }}">
                            @error('city')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="state" class="form-label fw-bold small text-muted text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.04em;">Région / Province</label>
                            <input type="text" id="state" name="state" class="form-control @error('state') is-invalid @enderror" value="{{ old('state', $customer->state) }}">
                            @error('state')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="zip" class="form-label fw-bold small text-muted text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.04em;">Code Postal</label>
                            <input type="text" id="zip" name="zip" class="form-control font-monospace @error('zip') is-invalid @enderror" value="{{ old('zip', $customer->zip) }}">
                            @error('zip')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="country" class="form-label fw-bold small text-muted text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.04em;">Pays</label>
                            <input type="text" id="country" name="country" class="form-control @error('country') is-invalid @enderror" value="{{ old('country', $customer->country ?: 'Maroc') }}">
                            @error('country')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Bloc 3: Remarques & Notes Internes -->
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                    <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                        <i class="fas fa-sticky-note text-warning" style="font-size: 1.1rem;"></i>
                        <h6 class="fw-bold text-dark mb-0" style="font-size: 0.95rem;">Notes & Instructions Internes</h6>
                    </div>
                    <textarea id="notes" name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3">{{ old('notes', $customer->notes) }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Colonne Latérale: Statut, Groupe & Limite de Crédit -->
            <div class="col-12 col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
                    <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                        <i class="fas fa-sliders-h text-primary" style="font-size: 1.1rem;"></i>
                        <h6 class="fw-bold text-dark mb-0" style="font-size: 0.95rem;">Paramètres & Classification</h6>
                    </div>

                    <!-- Statut du Compte -->
                    <div class="mb-3">
                        <label for="status" class="form-label fw-bold small text-muted text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.04em;">Statut du Compte <span class="text-danger">*</span></label>
                        <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="active" {{ old('status', $customer->status) == 'active' ? 'selected' : '' }}>Actif (Autorisé)</option>
                            <option value="inactive" {{ old('status', $customer->status) == 'inactive' ? 'selected' : '' }}>Inactif</option>
                            <option value="blocked" {{ old('status', $customer->status) == 'blocked' ? 'selected' : '' }}>Bloqué (Interdit)</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Groupe Client -->
                    <div class="mb-3">
                        <label for="customer_group_id" class="form-label fw-bold small text-muted text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.04em;">Groupe Client</label>
                        <select id="customer_group_id" name="customer_group_id" class="form-select @error('customer_group_id') is-invalid @enderror">
                            <option value="">Aucun groupe (Standard)</option>
                            @foreach($customerGroups as $group)
                                <option value="{{ $group->id }}" {{ old('customer_group_id', $customer->customer_group_id) == $group->id ? 'selected' : '' }}>
                                    {{ $group->name }} ({{ $group->formatted_discount }} remise)
                                </option>
                            @endforeach
                        </select>
                        @error('customer_group_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Limite de Crédit -->
                    <div class="mb-3">
                        <label for="credit_limit" class="form-label fw-bold small text-muted text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.04em;">Limite de Crédit Accordée</label>
                        <div class="input-group">
                            <input type="number" step="0.01" min="0" id="credit_limit" name="credit_limit" class="form-control font-monospace @error('credit_limit') is-invalid @enderror" value="{{ old('credit_limit', $customer->credit_limit) }}">
                            <span class="input-group-text bg-light text-muted fw-semibold">MAD</span>
                        </div>
                        <div class="form-text small text-muted">Solde d'encours actuel: <strong>{{ currency($customer->current_balance) }}</strong></div>
                        @error('credit_limit')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Boutons d'Action -->
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white text-center">
                    <button type="submit" class="btn btn-brand-primary w-100 rounded-pill py-2.5 fw-semibold mb-2 shadow-sm d-inline-flex align-items-center justify-content-center gap-1.5">
                        <i class="fas fa-save"></i> <span>Mettre à jour le Client</span>
                    </button>
                    <a href="{{ route('customers.show', $customer) }}" class="btn btn-light border w-100 rounded-pill py-2 text-secondary">
                        Annuler
                    </a>
                </div>
            </div>
        </div>
    </form>
@endsection
