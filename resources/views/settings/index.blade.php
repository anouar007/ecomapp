@extends('layouts.app')

@section('title', 'Paramètres de la Boutique')

@section('content')
    <!-- En-tête de page (Clean & Pro Executive Brand Header - Aligned with Invoices, Orders & Inventory) -->
    <div class="brand-header">
        <div class="d-flex align-items-center gap-2.5">
            <div class="brand-header-icon flex-shrink-0">
                <i class="fas fa-cog"></i>
            </div>
            <div>
                <h1 class="brand-title">Paramètres de la Boutique</h1>
                <p class="brand-subtitle">Configurez l'identité d'entreprise, la fiscalité, les garanties, le thème et la boutique</p>
            </div>
        </div>
        <div class="brand-header-actions">
            <a href="{{ route('custom-codes.index') }}" class="btn btn-outline-secondary shadow-sm rounded-pill px-3 py-2 d-inline-flex align-items-center justify-content-center gap-1.5 flex-grow-1 flex-md-grow-0 fw-semibold" style="font-size: 0.84rem;">
                <i class="fas fa-code text-primary"></i> <span>Codes & Scripts</span>
            </a>
            <button type="submit" form="settings-form" class="btn-brand-primary flex-grow-1 flex-md-grow-0 justify-content-center text-nowrap px-3.5 py-2 shadow-sm rounded-pill" style="font-size: 0.84rem;">
                <i class="fas fa-save me-1.5"></i> <span>Enregistrer</span>
            </button>
        </div>
    </div>

    {{-- Flash Notifications --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 rounded-4 border-0 shadow-sm mb-4" role="alert">
            <i class="fas fa-check-circle fs-5 text-success"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(isset($errors) && $errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
            <div class="fw-bold mb-1"><i class="fas fa-exclamation-triangle me-1"></i> Veuillez corriger les erreurs suivantes :</div>
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Executive KPI Metrics Row --}}
    <div class="row g-3 mb-4">
        {{-- Boutique Active --}}
        <div class="col-6 col-lg-3">
            <div class="stat-kpi-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label">Boutique Active</div>
                        <div class="stat-kpi-value text-dark text-truncate" style="max-width: 170px;" title="{{ setting('app_name', 'Full Frame House') }}">
                            {{ setting('app_name', 'Full Frame House') }}
                        </div>
                        <div class="stat-kpi-sub text-muted">
                            <span class="badge bg-light text-secondary border">Nom commercial</span>
                        </div>
                    </div>
                    <div class="stat-kpi-icon bg-danger-subtle text-danger">
                        <i class="fas fa-store"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Devise & Taxe --}}
        <div class="col-6 col-lg-3">
            <div class="stat-kpi-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label">Devise & Fiscalité</div>
                        <div class="stat-kpi-value text-success">
                            {{ setting('currency_code', 'MAD') }} &bull; {{ setting('tax_rate', 20) }}%
                        </div>
                        <div class="stat-kpi-sub text-muted">
                            <i class="fas fa-receipt text-success me-1"></i>Taux {{ setting('tax_label', 'TVA') }}
                        </div>
                    </div>
                    <div class="stat-kpi-icon bg-success-subtle text-success">
                        <i class="fas fa-coins"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Cachet & Signature --}}
        <div class="col-6 col-lg-3">
            <div class="stat-kpi-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label">Cachet & Signature</div>
                        <div class="stat-kpi-value {{ setting('company_stamp') ? 'text-info' : 'text-secondary' }}">
                            {{ setting('company_stamp') ? 'Actif' : 'Non défini' }}
                        </div>
                        <div class="stat-kpi-sub text-muted">
                            <i class="fas fa-file-invoice text-info me-1"></i>Factures & Devis
                        </div>
                    </div>
                    <div class="stat-kpi-icon bg-info-subtle text-info">
                        <i class="fas fa-stamp"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Garantie & SAV --}}
        <div class="col-6 col-lg-3">
            <div class="stat-kpi-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-kpi-label">Garantie & SAV</div>
                        <div class="stat-kpi-value text-warning">
                            {{ setting('guarantee_period_default', '12 Mois') }}
                        </div>
                        <div class="stat-kpi-sub text-muted">
                            <i class="fas fa-shield-alt text-warning me-1"></i>Durée par défaut
                        </div>
                    </div>
                    <div class="stat-kpi-icon bg-warning-subtle text-warning">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Settings Layout --}}
    <div class="row g-4">
        {{-- Navigation Sidebar (Pills on Mobile, Sticky Vertical Card on Desktop) --}}
        <div class="col-12 col-lg-3 col-xl-3">
            <div class="settings-nav-card">
                <button type="button" class="settings-nav-btn active" data-tab="general" onclick="switchSettingsTab('general')">
                    <span class="d-inline-flex align-items-center gap-2">
                        <i class="fas fa-sliders-h tab-icon"></i>
                        <span>Général</span>
                    </span>
                    <i class="fas fa-chevron-right tab-chevron small"></i>
                </button>
                <button type="button" class="settings-nav-btn" data-tab="company" onclick="switchSettingsTab('company')">
                    <span class="d-inline-flex align-items-center gap-2">
                        <i class="fas fa-building tab-icon"></i>
                        <span>Entreprise</span>
                    </span>
                    <i class="fas fa-chevron-right tab-chevron small"></i>
                </button>
                <button type="button" class="settings-nav-btn" data-tab="guarantee" onclick="switchSettingsTab('guarantee')">
                    <span class="d-inline-flex align-items-center gap-2">
                        <i class="fas fa-shield-alt tab-icon"></i>
                        <span>Garantie & SAV</span>
                    </span>
                    <i class="fas fa-chevron-right tab-chevron small"></i>
                </button>
                <button type="button" class="settings-nav-btn" data-tab="theme" onclick="switchSettingsTab('theme')">
                    <span class="d-inline-flex align-items-center gap-2">
                        <i class="fas fa-palette tab-icon"></i>
                        <span>Thème & Couleurs</span>
                    </span>
                    <i class="fas fa-chevron-right tab-chevron small"></i>
                </button>
                <button type="button" class="settings-nav-btn" data-tab="social" onclick="switchSettingsTab('social')">
                    <span class="d-inline-flex align-items-center gap-2">
                        <i class="fas fa-share-alt tab-icon"></i>
                        <span>Réseaux sociaux</span>
                    </span>
                    <i class="fas fa-chevron-right tab-chevron small"></i>
                </button>
                <button type="button" class="settings-nav-btn" data-tab="frontend" onclick="switchSettingsTab('frontend')">
                    <span class="d-inline-flex align-items-center gap-2">
                        <i class="fas fa-desktop tab-icon"></i>
                        <span>Boutique en ligne</span>
                    </span>
                    <i class="fas fa-chevron-right tab-chevron small"></i>
                </button>
                <button type="button" class="settings-nav-btn" data-tab="localization" onclick="switchSettingsTab('localization')">
                    <span class="d-inline-flex align-items-center gap-2">
                        <i class="fas fa-globe tab-icon"></i>
                        <span>Langue & Région</span>
                    </span>
                    <i class="fas fa-chevron-right tab-chevron small"></i>
                </button>
                <button type="button" class="settings-nav-btn" data-tab="advanced" onclick="switchSettingsTab('advanced')">
                    <span class="d-inline-flex align-items-center gap-2">
                        <i class="fas fa-cogs tab-icon"></i>
                        <span>Avancé</span>
                    </span>
                    <i class="fas fa-chevron-right tab-chevron small"></i>
                </button>

                <div class="d-none d-lg-block border-top pt-3 mt-2 px-2">
                    <button type="submit" form="reset-settings-form" class="btn btn-sm btn-outline-danger w-100 rounded-pill d-inline-flex align-items-center justify-content-center gap-1.5" onclick="return confirm('Êtes-vous sûr de vouloir réinitialiser tous les paramètres aux valeurs par défaut ?')">
                        <i class="fas fa-undo"></i>
                        <span>Réinitialiser par défaut</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Settings Content Area --}}
        <div class="col-12 col-lg-9 col-xl-9">
            <div class="settings-content-card">
                <form action="{{ route('settings.update') }}" method="POST" id="settings-form">
                    @csrf
                    @method('PUT')

                    {{-- 1. General Tab --}}
                    <div class="tab-pane d-none" id="general-tab">
                        <div class="settings-section-card">
                            <h3 class="settings-section-title">
                                <i class="fas fa-info-circle text-danger"></i>
                                <span>Informations Générales de l'Application</span>
                            </h3>
                            <p class="settings-section-desc">Définissez le nom public et la description générale de votre plateforme e-commerce.</p>
                            
                            @php
                                $generalSettings = $settings->get('general', collect());
                            @endphp
                            
                            <div class="row g-3 mb-4">
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-bold small text-dark">Nom de l'application / Boutique</label>
                                    <input type="text" name="settings[app_name]" class="form-control rounded-3" 
                                           value="{{ $generalSettings->where('key', 'app_name')->first()?->value ?? '' }}" placeholder="Ex: Full Frame House">
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-bold small text-dark">Description courte</label>
                                    <input type="text" name="settings[app_description]" class="form-control rounded-3" 
                                           value="{{ $generalSettings->where('key', 'app_description')->first()?->value ?? '' }}" placeholder="Ex: Équipements audiovisuels professionnels">
                                </div>
                            </div>
                        </div>

                        {{-- Logo Section --}}
                        <div class="settings-section-card">
                            <h3 class="settings-section-title">
                                <i class="fas fa-image text-danger"></i>
                                <span>Logo Officiel de la Boutique</span>
                            </h3>
                            <p class="settings-section-desc">Le logo s'affiche dans l'en-tête, les factures et les communications clients.</p>
                            
                            @if(setting('app_logo'))
                                <div class="p-3 bg-light rounded-4 text-center mb-3 border">
                                    <img src="{{ asset('storage/' . setting('app_logo')) }}" alt="Logo" class="img-fluid rounded-2 mb-2" style="max-height: 80px; object-fit: contain;">
                                    <div>
                                        <button type="submit" form="remove-logo-form" class="btn btn-sm btn-brand-soft-danger rounded-pill px-3 py-1.5" onclick="return confirm('Supprimer le logo actuel ?')">
                                            <i class="fas fa-trash me-1"></i> Supprimer le logo
                                        </button>
                                    </div>
                                </div>
                            @endif

                            <div class="settings-upload-dropzone" onclick="document.getElementById('logo-input').click()">
                                <i class="fas fa-cloud-upload-alt text-danger mb-2" style="font-size: 2.5rem;"></i>
                                <p class="fw-bold text-dark mb-1">Cliquez pour téléverser un nouveau logo</p>
                                <p class="small text-muted mb-0">Formats supportés : PNG (fond transparent recommandé), JPG, SVG jusqu'à 2 Mo</p>
                                <input type="file" id="logo-input" form="logo-upload-form" name="logo" accept="image/*" class="d-none" onchange="document.getElementById('logo-upload-form').submit()">
                            </div>
                        </div>

                        {{-- Regional Settings --}}
                        <div class="settings-section-card mt-4">
                            <h3 class="settings-section-title">
                                <i class="fas fa-clock text-danger"></i>
                                <span>Fuseau Horaire & Formats de Date</span>
                            </h3>
                            
                            <div class="row g-3">
                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-bold small text-dark">Fuseau horaire (Timezone)</label>
                                    <select name="settings[timezone]" class="form-select rounded-3">
                                        <option value="Africa/Casablanca" {{ setting('timezone') == 'Africa/Casablanca' ? 'selected' : '' }}>Africa/Casablanca (GMT+1)</option>
                                        <option value="UTC" {{ setting('timezone') == 'UTC' ? 'selected' : '' }}>UTC</option>
                                        <option value="Europe/Paris" {{ setting('timezone') == 'Europe/Paris' ? 'selected' : '' }}>Europe/Paris</option>
                                        <option value="America/New_York" {{ setting('timezone') == 'America/New_York' ? 'selected' : '' }}>America/New York</option>
                                        <option value="Asia/Dubai" {{ setting('timezone') == 'Asia/Dubai' ? 'selected' : '' }}>Asia/Dubai</option>
                                    </select>
                                </div>

                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-bold small text-dark">Format de date</label>
                                    <select name="settings[date_format]" class="form-select rounded-3">
                                        <option value="d/m/Y" {{ setting('date_format') == 'd/m/Y' ? 'selected' : '' }}>11/01/2026 (d/m/Y)</option>
                                        <option value="Y-m-d" {{ setting('date_format') == 'Y-m-d' ? 'selected' : '' }}>2026-01-11 (Y-m-d)</option>
                                        <option value="d M Y" {{ setting('date_format') == 'd M Y' ? 'selected' : '' }}>11 Jan 2026 (d M Y)</option>
                                        <option value="m/d/Y" {{ setting('date_format') == 'm/d/Y' ? 'selected' : '' }}>01/11/2026 (m/d/Y)</option>
                                    </select>
                                </div>

                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-bold small text-dark">Format de l'heure</label>
                                    <select name="settings[time_format]" class="form-select rounded-3">
                                        <option value="H:i" {{ setting('time_format') == 'H:i' ? 'selected' : '' }}>24 heures (14:30)</option>
                                        <option value="h:i A" {{ setting('time_format') == 'h:i A' ? 'selected' : '' }}>12 heures (02:30 PM)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 2. Company Info Tab --}}
                    <div class="tab-pane d-none" id="company-tab">
                        <div class="settings-section-card">
                            <h3 class="settings-section-title">
                                <i class="fas fa-building text-danger"></i>
                                <span>Identité Légale & Coordonnées de l'Entreprise</span>
                            </h3>
                            <p class="settings-section-desc">Ces informations légales s'impriment automatiquement sur toutes les factures, devis et bons de commande.</p>
                            
                            @php
                                $companySettings = $settings->get('company', collect());
                            @endphp

                            <div class="row g-3 mb-4">
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-bold small text-dark">Raison Sociale / Nom Entreprise</label>
                                    <input type="text" name="settings[company_name]" class="form-control rounded-3" 
                                           value="{{ $companySettings->where('key', 'company_name')->first()?->value ?? '' }}" placeholder="Ex: Full Frame House SARL">
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-bold small text-dark">Adresse du Siège Social</label>
                                    <input type="text" name="settings[company_address]" class="form-control rounded-3" 
                                           value="{{ $companySettings->where('key', 'company_address')->first()?->value ?? '' }}" placeholder="Ex: 123 Boulevard d'Anfa, Casablanca">
                                </div>

                                <div class="col-12 col-md-3">
                                    <label class="form-label fw-bold small text-dark">Identifiant Commun (ICE)</label>
                                    <input type="text" name="settings[company_tax_id]" class="form-control rounded-3 font-monospace" 
                                           value="{{ $companySettings->where('key', 'company_tax_id')->first()?->value ?? '' }}" placeholder="001569874000089">
                                </div>

                                <div class="col-12 col-md-3">
                                    <label class="form-label fw-bold small text-dark">Registre de Commerce (RC)</label>
                                    <input type="text" name="settings[company_registry_id]" class="form-control rounded-3 font-monospace" 
                                           value="{{ $companySettings->where('key', 'company_registry_id')->first()?->value ?? '' }}" placeholder="Ex: 12345">
                                </div>

                                <div class="col-12 col-md-3">
                                    <label class="form-label fw-bold small text-dark">Taxe Professionnelle (Patente)</label>
                                    <input type="text" name="settings[company_patente]" class="form-control rounded-3 font-monospace" 
                                           value="{{ $companySettings->where('key', 'company_patente')->first()?->value ?? '' }}" placeholder="Ex: 45891236">
                                </div>

                                <div class="col-12 col-md-3">
                                    <label class="form-label fw-bold small text-dark">Identifiant Fiscal (IF)</label>
                                    <input type="text" name="settings[company_fiscal_id]" class="form-control rounded-3 font-monospace" 
                                           value="{{ $companySettings->where('key', 'company_fiscal_id')->first()?->value ?? '' }}" placeholder="Ex: 33221144">
                                </div>

                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-bold small text-dark">Email de contact</label>
                                    <input type="email" name="settings[company_email]" class="form-control rounded-3" 
                                           value="{{ $companySettings->where('key', 'company_email')->first()?->value ?? '' }}" placeholder="contact@fullframehouse.com">
                                </div>

                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-bold small text-dark">Téléphone</label>
                                    <input type="text" name="settings[company_phone]" class="form-control rounded-3" 
                                           value="{{ $companySettings->where('key', 'company_phone')->first()?->value ?? '' }}" placeholder="+212 5 22 00 00 00">
                                </div>

                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-bold small text-dark">Site web officiel</label>
                                    <input type="url" name="settings[company_website]" class="form-control rounded-3" 
                                           value="{{ $companySettings->where('key', 'company_website')->first()?->value ?? '' }}" placeholder="https://fullframehouse.com">
                                </div>
                            </div>
                        </div>

                        {{-- Stamp & Signature Section --}}
                        <div class="settings-section-card mt-4">
                            <h3 class="settings-section-title">
                                <i class="fas fa-stamp text-danger"></i>
                                <span>Cachet & Signature de l'Entreprise</span>
                            </h3>
                            <p class="settings-section-desc">Ce cachet officiel sera imprimé sur l'ensemble de vos factures, devis et certificats de garantie.</p>
                            
                            @if(setting('company_stamp'))
                                <div class="p-3 bg-light rounded-4 text-center mb-3 border">
                                    <img src="{{ asset('storage/' . setting('company_stamp')) }}" alt="Cachet" class="img-fluid rounded-2 mb-2" style="max-height: 120px; object-fit: contain;">
                                    <div>
                                        <button type="submit" form="remove-stamp-form" class="btn btn-sm btn-brand-soft-danger rounded-pill px-3 py-1.5" onclick="return confirm('Supprimer le cachet d\'entreprise ?')">
                                            <i class="fas fa-trash me-1"></i> Supprimer le cachet
                                        </button>
                                    </div>
                                </div>
                            @endif

                            <div class="settings-upload-dropzone mb-3" onclick="document.getElementById('stamp-input').click()">
                                <i class="fas fa-stamp text-danger mb-2" style="font-size: 2.5rem;"></i>
                                <p class="fw-bold text-dark mb-1">Cliquez pour téléverser votre cachet / signature</p>
                                <p class="small text-muted mb-0">PNG transparent recommandé, JPG, SVG, WebP jusqu'à 2 Mo</p>
                                <input type="file" id="stamp-input" form="stamp-upload-form" name="stamp" accept="image/*" class="d-none" onchange="document.getElementById('stamp-upload-form').submit()">
                            </div>

                            {{-- Stamp Scale Control --}}
                            <div class="p-3 bg-light rounded-4 border">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <label class="form-label fw-bold small text-dark mb-0">
                                        <i class="fas fa-search-plus text-danger me-1"></i>
                                        Échelle d'affichage sur les documents
                                    </label>
                                    <span class="badge bg-dark text-white rounded-pill px-2.5 py-1">
                                        <span id="stamp-scale-value" class="fw-bold">{{ setting('company_stamp_scale', 100) }}</span> %
                                    </span>
                                </div>

                                <input type="range" id="stamp-scale-range"
                                       name="settings[company_stamp_scale]"
                                       min="25" max="200" step="5"
                                       value="{{ setting('company_stamp_scale', 100) }}"
                                       class="form-range"
                                       oninput="updateStampSize()">

                                <div class="d-flex justify-content-between small text-muted">
                                    <span>25% (Compact)</span>
                                    <span>100% (Standard)</span>
                                    <span>200% (Large)</span>
                                </div>

                                @if(setting('company_stamp'))
                                    @php $initScale = intval(setting('company_stamp_scale', 100)) / 100; @endphp
                                    <div class="mt-3 text-center p-3 bg-white rounded-3 border">
                                        <div class="small text-muted mb-2 text-uppercase fw-semibold" style="font-size: 11px;">Aperçu direct en taille réelle</div>
                                        <div id="stamp-preview-wrap" class="d-inline-block">
                                            <img id="stamp-preview-img"
                                                 src="{{ asset('storage/' . setting('company_stamp')) }}"
                                                 alt="Aperçu Cachet"
                                                 style="width: {{ round(160 * $initScale) }}px; height: auto; max-width: 100%; transition: width 0.15s;">
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- 3. Guarantee & Warranty Tab --}}
                    <div class="tab-pane d-none" id="guarantee-tab">
                        <div class="settings-section-card">
                            <h3 class="settings-section-title">
                                <i class="fas fa-shield-alt text-danger"></i>
                                <span>Paramètres de Garantie & SAV</span>
                            </h3>
                            <p class="settings-section-desc">Personnalisez les clauses, durées et mentions légales éditées sur vos certificats de garantie officiels.</p>

                            <div class="row g-3 mb-4">
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-bold small text-dark">Titre officiel du document de garantie</label>
                                    <input type="text" name="settings[guarantee_title]" class="form-control rounded-3" 
                                           value="{{ setting('guarantee_title', 'CERTIFICAT DE GARANTIE') }}" 
                                           placeholder="Ex: CERTIFICAT DE GARANTIE">
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-bold small text-dark">Durée de garantie par défaut</label>
                                    <input type="text" name="settings[guarantee_period_default]" class="form-control rounded-3" 
                                           value="{{ setting('guarantee_period_default', '12 Mois') }}" 
                                           placeholder="Ex: 12 Mois, 24 Mois, 2 Ans">
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-bold small text-dark">Clauses & Conditions de la garantie</label>
                                    <textarea name="settings[guarantee_terms]" class="form-control rounded-3 font-monospace small" rows="8" placeholder="Clauses de garantie...">{{ setting('guarantee_terms', "1. La présente garantie couvre tous les défauts de fabrication et de fonctionnement du matériel pour la durée spécifiée à compter de la date d'émission de la facture.\n2. La garantie s'applique uniquement sur présentation du présent bon de garantie accompagné de la facture d'achat originale.\n3. Sont exclus de la garantie : les dommages dus à une mauvaise utilisation, à une négligence, aux surtensions électriques, aux dégâts des eaux, ou à toute intervention technique non autorisée.\n4. En cas de panne couverte par la garantie, notre service après-vente procédera gratuitement à la réparation ou au remplacement du composant défectueux dans les meilleurs délais.\n5. Les consommables, accessoires d'usure et pièces externes ne sont pas couverts par la garantie.") }}</textarea>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-bold small text-dark">Mentions d'assistance SAV & Bas de page</label>
                                    <textarea name="settings[guarantee_notes]" class="form-control rounded-3" rows="3" placeholder="Notes SAV...">{{ setting('guarantee_notes', 'Pour toute assistance technique ou réclamation sous garantie, veuillez vous munir de votre numéro de facture et contacter notre service après-vente.') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 4. Theme & Appearance Tab --}}
                    <div class="tab-pane d-none" id="theme-tab">
                        <div class="settings-section-card">
                            <h3 class="settings-section-title">
                                <i class="fas fa-palette text-danger"></i>
                                <span>Palette de Couleurs & Thème</span>
                            </h3>
                            <p class="settings-section-desc">Personnalisez les teintes principales utilisées à travers les interfaces et documents.</p>

                            <div class="row g-3 mb-4">
                                <div class="col-6 col-md-4">
                                    <div class="settings-color-card d-flex align-items-center gap-2.5">
                                        <div class="settings-color-preview-slot" style="background-color: {{ setting('primary_color', '#dc2626') }};">
                                            <input type="color" name="settings[primary_color]" value="{{ setting('primary_color', '#dc2626') }}" onchange="updateColorSlot(this)">
                                        </div>
                                        <div>
                                            <div class="small fw-bold text-dark">Couleur Primaire</div>
                                            <div class="small text-muted font-monospace color-text">{{ setting('primary_color', '#dc2626') }}</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-6 col-md-4">
                                    <div class="settings-color-card d-flex align-items-center gap-2.5">
                                        <div class="settings-color-preview-slot" style="background-color: {{ setting('secondary_color', '#0f172a') }};">
                                            <input type="color" name="settings[secondary_color]" value="{{ setting('secondary_color', '#0f172a') }}" onchange="updateColorSlot(this)">
                                        </div>
                                        <div>
                                            <div class="small fw-bold text-dark">Couleur Sombre</div>
                                            <div class="small text-muted font-monospace color-text">{{ setting('secondary_color', '#0f172a') }}</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-6 col-md-4">
                                    <div class="settings-color-card d-flex align-items-center gap-2.5">
                                        <div class="settings-color-preview-slot" style="background-color: {{ setting('accent_color', '#8b5cf6') }};">
                                            <input type="color" name="settings[accent_color]" value="{{ setting('accent_color', '#8b5cf6') }}" onchange="updateColorSlot(this)">
                                        </div>
                                        <div>
                                            <div class="small fw-bold text-dark">Couleur Accent</div>
                                            <div class="small text-muted font-monospace color-text">{{ setting('accent_color', '#8b5cf6') }}</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-6 col-md-4">
                                    <div class="settings-color-card d-flex align-items-center gap-2.5">
                                        <div class="settings-color-preview-slot" style="background-color: {{ setting('success_color', '#10b981') }};">
                                            <input type="color" name="settings[success_color]" value="{{ setting('success_color', '#10b981') }}" onchange="updateColorSlot(this)">
                                        </div>
                                        <div>
                                            <div class="small fw-bold text-dark">Succès</div>
                                            <div class="small text-muted font-monospace color-text">{{ setting('success_color', '#10b981') }}</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-6 col-md-4">
                                    <div class="settings-color-card d-flex align-items-center gap-2.5">
                                        <div class="settings-color-preview-slot" style="background-color: {{ setting('warning_color', '#f59e0b') }};">
                                            <input type="color" name="settings[warning_color]" value="{{ setting('warning_color', '#f59e0b') }}" onchange="updateColorSlot(this)">
                                        </div>
                                        <div>
                                            <div class="small fw-bold text-dark">Avertissement</div>
                                            <div class="small text-muted font-monospace color-text">{{ setting('warning_color', '#f59e0b') }}</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-6 col-md-4">
                                    <div class="settings-color-card d-flex align-items-center gap-2.5">
                                        <div class="settings-color-preview-slot" style="background-color: {{ setting('danger_color', '#ef4444') }};">
                                            <input type="color" name="settings[danger_color]" value="{{ setting('danger_color', '#ef4444') }}" onchange="updateColorSlot(this)">
                                        </div>
                                        <div>
                                            <div class="small fw-bold text-dark">Danger / Erreur</div>
                                            <div class="small text-muted font-monospace color-text">{{ setting('danger_color', '#ef4444') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Typography & Radius --}}
                        <div class="settings-section-card mt-4">
                            <h3 class="settings-section-title">
                                <i class="fas fa-font text-danger"></i>
                                <span>Typographie & Arrondis d'Interface</span>
                            </h3>

                            <div class="row g-3">
                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-bold small text-dark">Police de caractères (Font)</label>
                                    <select name="settings[font_family]" class="form-select rounded-3">
                                        <option value="Inter, system-ui, sans-serif" {{ setting('font_family') == 'Inter, system-ui, sans-serif' ? 'selected' : '' }}>Inter (Recommandé)</option>
                                        <option value="Roboto, sans-serif" {{ setting('font_family') == 'Roboto, sans-serif' ? 'selected' : '' }}>Roboto</option>
                                        <option value="Open Sans, sans-serif" {{ setting('font_family') == 'Open Sans, sans-serif' ? 'selected' : '' }}>Open Sans</option>
                                        <option value="Poppins, sans-serif" {{ setting('font_family') == 'Poppins, sans-serif' ? 'selected' : '' }}>Poppins</option>
                                        <option value="Montserrat, sans-serif" {{ setting('font_family') == 'Montserrat, sans-serif' ? 'selected' : '' }}>Montserrat</option>
                                    </select>
                                </div>

                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-bold small text-dark">Taille de police de base (px)</label>
                                    <input type="number" name="settings[font_size_base]" class="form-control rounded-3" min="12" max="20"
                                           value="{{ setting('font_size_base', 14) }}">
                                </div>

                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-bold small text-dark">Rayon de bordure (Border Radius px)</label>
                                    <input type="number" name="settings[border_radius]" class="form-control rounded-3" min="0" max="24"
                                           value="{{ setting('border_radius', 12) }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 5. Social Media Tab --}}
                    <div class="tab-pane d-none" id="social-tab">
                        <div class="settings-section-card">
                            <h3 class="settings-section-title">
                                <i class="fas fa-share-alt text-danger"></i>
                                <span>Profils & Réseaux Sociaux</span>
                            </h3>
                            <p class="settings-section-desc">Liens intégrés dans le pied de page de la boutique et les documents numériques.</p>

                            <div class="row g-3">
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-bold small text-dark"><i class="fab fa-facebook text-primary me-1"></i> Page Facebook</label>
                                    <input type="text" name="settings[social_facebook]" class="form-control rounded-3" value="{{ setting('social_facebook', '#') }}">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-bold small text-dark"><i class="fab fa-instagram text-danger me-1"></i> Compte Instagram</label>
                                    <input type="text" name="settings[social_instagram]" class="form-control rounded-3" value="{{ setting('social_instagram', '#') }}">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-bold small text-dark"><i class="fab fa-x-twitter text-dark me-1"></i> Compte X (Twitter)</label>
                                    <input type="text" name="settings[social_twitter]" class="form-control rounded-3" value="{{ setting('social_twitter', '#') }}">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-bold small text-dark"><i class="fab fa-linkedin text-info me-1"></i> Page LinkedIn</label>
                                    <input type="text" name="settings[social_linkedin]" class="form-control rounded-3" value="{{ setting('social_linkedin', '#') }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold small text-dark"><i class="fab fa-whatsapp text-success me-1"></i> Numéro WhatsApp Pro</label>
                                    <input type="text" name="settings[social_whatsapp]" class="form-control rounded-3" value="{{ setting('social_whatsapp') }}" placeholder="Ex: 212600000000 (format international sans +)">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 6. Frontend Tab --}}
                    <div class="tab-pane d-none" id="frontend-tab">
                        <div class="settings-section-card">
                            <h3 class="settings-section-title">
                                <i class="fas fa-desktop text-danger"></i>
                                <span>Expérience Visuelle de la Boutique</span>
                            </h3>

                            <div class="row g-3">
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-bold small text-dark">Barre de navigation fixe (Sticky Header)</label>
                                    <select name="settings[frontend_sticky_header]" class="form-select rounded-3">
                                        <option value="1" {{ setting('frontend_sticky_header') == '1' ? 'selected' : '' }}>Activée (Fixe au défilement)</option>
                                        <option value="0" {{ setting('frontend_sticky_header') == '0' ? 'selected' : '' }}>Désactivée</option>
                                    </select>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-bold small text-dark">Animations fluides au défilement (AOS)</label>
                                    <select name="settings[frontend_enable_animations]" class="form-select rounded-3">
                                        <option value="1" {{ setting('frontend_enable_animations') == '1' ? 'selected' : '' }}>Activées</option>
                                        <option value="0" {{ setting('frontend_enable_animations') == '0' ? 'selected' : '' }}>Désactivées</option>
                                    </select>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-bold small text-dark">Police Google Fonts Principale</label>
                                    <input type="text" name="settings[frontend_primary_font]" class="form-control rounded-3" value="{{ setting('frontend_primary_font', 'Inter') }}">
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-bold small text-dark">Texte de présentation / Slogan du pied de page</label>
                                    <textarea name="settings[frontend_footer_text]" class="form-control rounded-3" rows="3" placeholder="Slogan ou présentation de la boutique dans le footer...">{{ setting('frontend_footer_text') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 7. Localization & Tax Tab --}}
                    <div class="tab-pane d-none" id="localization-tab">
                        <div class="settings-section-card">
                            <h3 class="settings-section-title">
                                <i class="fas fa-globe text-danger"></i>
                                <span>Langue & Pays par Défaut</span>
                            </h3>

                            <div class="row g-3 mb-4">
                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-bold small text-dark">Langue de l'interface</label>
                                    <select name="settings[language]" class="form-select rounded-3">
                                        <option value="fr" {{ setting('language') == 'fr' ? 'selected' : '' }}>Français (French)</option>
                                        <option value="en" {{ setting('language') == 'en' ? 'selected' : '' }}>English</option>
                                        <option value="ar" {{ setting('language') == 'ar' ? 'selected' : '' }}>العربية (Arabic)</option>
                                    </select>
                                </div>

                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-bold small text-dark">Sens de lecture</label>
                                    <select name="settings[text_direction]" class="form-select rounded-3">
                                        <option value="ltr" {{ setting('text_direction') == 'ltr' ? 'selected' : '' }}>De gauche à droite (LTR)</option>
                                        <option value="rtl" {{ setting('text_direction') == 'rtl' ? 'selected' : '' }}>De droite à gauche (RTL)</option>
                                    </select>
                                </div>

                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-bold small text-dark">Pays par défaut</label>
                                    <select name="settings[default_country]" class="form-select rounded-3">
                                        <option value="MA" {{ setting('default_country', 'MA') == 'MA' ? 'selected' : '' }}>Maroc (Morocco)</option>
                                        <option value="FR" {{ setting('default_country') == 'FR' ? 'selected' : '' }}>France</option>
                                        <option value="US" {{ setting('default_country') == 'US' ? 'selected' : '' }}>États-Unis</option>
                                        <option value="AE" {{ setting('default_country') == 'AE' ? 'selected' : '' }}>Émirats Arabes Unis</option>
                                        <option value="SA" {{ setting('default_country') == 'SA' ? 'selected' : '' }}>Arabie Saoudite</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- Currency Configuration --}}
                        <div class="settings-section-card mt-4">
                            <h3 class="settings-section-title">
                                <i class="fas fa-coins text-danger"></i>
                                <span>Configuration de la Devise Commerciale</span>
                            </h3>

                            <div class="row g-3 mb-4">
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-bold small text-dark">Sélection de la devise</label>
                                    <div class="mb-2">
                                        <input type="text" id="currency-search" class="form-control rounded-3 form-control-sm" placeholder="Rechercher une devise (ex: MAD, EUR, USD)...">
                                    </div>
                                    <select name="settings[currency_code]" id="currency-select" class="form-select rounded-3">
                                        @foreach($currencies as $code => $data)
                                            <option value="{{ $code }}" 
                                                    data-symbol="{{ $data['symbol'] }}"
                                                    {{ setting('currency_code', 'MAD') == $code ? 'selected' : '' }}>
                                                {{ $code }} - {{ $data['name'] }} ({{ $data['symbol'] }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-bold small text-dark">Symbole monétaire</label>
                                    <input type="text" name="settings[currency_symbol]" class="form-control rounded-3" maxlength="5"
                                           value="{{ setting('currency_symbol', 'DH') }}" placeholder="DH">
                                </div>

                                <div class="col-12 col-md-3">
                                    <label class="form-label fw-bold small text-dark">Position du symbole</label>
                                    <select name="settings[currency_position]" class="form-select rounded-3">
                                        <option value="after" {{ setting('currency_position', 'after') == 'after' ? 'selected' : '' }}>Après le montant (100 DH)</option>
                                        <option value="before" {{ setting('currency_position') == 'before' ? 'selected' : '' }}>Avant le montant (DH 100)</option>
                                    </select>
                                </div>

                                <div class="col-12 col-md-3">
                                    <label class="form-label fw-bold small text-dark">Décimales</label>
                                    <input type="number" name="settings[currency_decimals]" class="form-control rounded-3" min="0" max="4"
                                           value="{{ setting('currency_decimals', 2) }}">
                                </div>

                                <div class="col-12 col-md-3">
                                    <label class="form-label fw-bold small text-dark">Séparateur décimal</label>
                                    <input type="text" name="settings[decimal_separator]" class="form-control rounded-3 text-center font-monospace" maxlength="1"
                                           value="{{ setting('decimal_separator', ',') }}" placeholder=",">
                                </div>

                                <div class="col-12 col-md-3">
                                    <label class="form-label fw-bold small text-dark">Séparateur de milliers</label>
                                    <input type="text" name="settings[thousands_separator]" class="form-control rounded-3 text-center font-monospace" maxlength="1"
                                           value="{{ setting('thousands_separator', ' ') }}" placeholder="espace">
                                </div>
                            </div>
                        </div>

                        {{-- Tax Configuration --}}
                        <div class="settings-section-card mt-4">
                            <h3 class="settings-section-title">
                                <i class="fas fa-file-invoice-dollar text-danger"></i>
                                <span>Fiscalité & Taxe sur la Valeur Ajoutée (TVA)</span>
                            </h3>

                            <div class="row g-3">
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-bold small text-dark">Taux de Taxe (%) / Taux TVA (%)</label>
                                    <div class="input-group">
                                        <input type="number" name="settings[tax_rate]" class="form-control rounded-start-3" min="0" max="100" step="0.01"
                                               value="{{ setting('tax_rate', 20) }}">
                                        <span class="input-group-text bg-light text-muted rounded-end-3 fw-bold">%</span>
                                    </div>
                                    <div class="form-text small text-muted">Ce taux s'applique automatiquement sur tous les devis, factures, commandes et POS.</div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-bold small text-dark">Libellé officiel de la taxe</label>
                                    <input type="text" name="settings[tax_label]" class="form-control rounded-3"
                                           value="{{ setting('tax_label', 'TVA') }}" placeholder="Ex: TVA">
                                    <div class="form-text small text-muted">Libellé officiel imprimé sur les factures et récapitulatifs (ex: TVA).</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 8. Advanced Tab --}}
                    <div class="tab-pane d-none" id="advanced-tab">
                        <div class="settings-section-card">
                            <h3 class="settings-section-title">
                                <i class="fas fa-cogs text-danger"></i>
                                <span>Seuils & Valeurs Système par Défaut</span>
                            </h3>

                            <div class="row g-3 mb-4">
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-bold small text-dark">Articles par page (Pagination)</label>
                                    <input type="number" name="settings[items_per_page]" class="form-control rounded-3" min="5" max="100"
                                           value="{{ setting('items_per_page', 10) }}">
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-bold small text-dark">Seuil d'alerte stock faible</label>
                                    <input type="number" name="settings[low_stock_threshold]" class="form-control rounded-3" min="0"
                                           value="{{ setting('low_stock_threshold', 10) }}">
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-bold small text-dark">Statut de commande par défaut</label>
                                    <select name="settings[default_order_status]" class="form-select rounded-3">
                                        <option value="pending" {{ setting('default_order_status') == 'pending' ? 'selected' : '' }}>En attente (Pending)</option>
                                        <option value="processing" {{ setting('default_order_status') == 'processing' ? 'selected' : '' }}>En traitement (Processing)</option>
                                        <option value="shipped" {{ setting('default_order_status') == 'shipped' ? 'selected' : '' }}>Expédiée (Shipped)</option>
                                    </select>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-bold small text-dark">Statut de paiement par défaut</label>
                                    <select name="settings[default_payment_status]" class="form-select rounded-3">
                                        <option value="pending" {{ setting('default_payment_status') == 'pending' ? 'selected' : '' }}>En attente (Pending)</option>
                                        <option value="paid" {{ setting('default_payment_status') == 'paid' ? 'selected' : '' }}>Payé (Paid)</option>
                                        <option value="failed" {{ setting('default_payment_status') == 'failed' ? 'selected' : '' }}>Échoué (Failed)</option>
                                    </select>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-bold small text-dark">Durée du cache système (secondes)</label>
                                    <input type="number" name="settings[cache_duration]" class="form-control rounded-3" min="0"
                                           value="{{ setting('cache_duration', 3600) }}">
                                    <div class="form-text small text-muted">Mettre à 0 pour désactiver le cache en environnement de développement.</div>
                                </div>
                            </div>
                        </div>

                        {{-- Danger Zone --}}
                        <div class="settings-section-card mt-4 p-3 bg-danger-subtle bg-opacity-25 rounded-4 border border-danger-subtle">
                            <h3 class="settings-section-title text-danger border-danger-subtle">
                                <i class="fas fa-exclamation-triangle"></i>
                                <span>Zone Sensible (Danger Zone)</span>
                            </h3>
                            <p class="small text-muted mb-3">Réinitialise toutes les variables de configuration à leurs valeurs d'origine pré-établies.</p>
                            
                            <button type="submit" form="reset-settings-form" class="btn btn-outline-danger rounded-pill px-3 py-2 fw-semibold" onclick="return confirm('Êtes-vous sûr de vouloir réinitialiser tous les paramètres aux valeurs d\'usine ?')">
                                <i class="fas fa-undo me-1.5"></i> Réinitialiser tous les paramètres par défaut
                            </button>
                        </div>
                    </div>

                    {{-- Form Actions Bar --}}
                    <div class="d-flex align-items-center justify-content-end gap-2 pt-4 border-top mt-4 flex-wrap">
                        <button type="button" class="btn btn-light rounded-pill px-3.5 py-2 fw-semibold" onclick="window.location.reload()">
                            <i class="fas fa-times me-1.5"></i> Annuler
                        </button>
                        <button type="submit" class="btn btn-brand-primary rounded-pill px-4 py-2 fw-semibold shadow-sm">
                            <i class="fas fa-save me-1.5"></i> Enregistrer les modifications
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Hidden Forms for Uploads and Resets -->
    <form id="remove-logo-form" action="{{ route('settings.logo.remove') }}" method="POST" class="d-none">
        @csrf
        @method('DELETE')
    </form>

    <form id="logo-upload-form" action="{{ route('settings.logo') }}" method="POST" enctype="multipart/form-data" class="d-none">
        @csrf
    </form>

    <form id="remove-stamp-form" action="{{ route('settings.stamp.remove') }}" method="POST" class="d-none">
        @csrf
        @method('DELETE')
    </form>

    <form id="stamp-upload-form" action="{{ route('settings.stamp') }}" method="POST" enctype="multipart/form-data" class="d-none">
        @csrf
    </form>

    <form id="reset-settings-form" action="{{ route('settings.reset') }}" method="POST" class="d-none">
        @csrf
    </form>
@endsection

@push('scripts')
<script>
    function switchSettingsTab(tabName) {
        // Update nav buttons
        document.querySelectorAll('.settings-nav-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        const activeBtn = document.querySelector(`.settings-nav-btn[data-tab="${tabName}"]`);
        if (activeBtn) activeBtn.classList.add('active');

        // Update tab content panes
        document.querySelectorAll('.settings-content-card .tab-pane').forEach(pane => {
            pane.classList.add('d-none');
            pane.classList.remove('active');
        });
        const activePane = document.getElementById(tabName + '-tab');
        if (activePane) {
            activePane.classList.remove('d-none');
            activePane.classList.add('active');
        }

        // Store active tab
        sessionStorage.setItem('active_settings_tab', tabName);
        history.replaceState(null, null, '#' + tabName);
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tab based on URL hash or sessionStorage
        const hash = window.location.hash.replace('#', '');
        const stored = sessionStorage.getItem('active_settings_tab');
        const initialTab = hash || stored || 'general';
        switchSettingsTab(initialTab);

        // Color input changes
        document.querySelectorAll('.settings-color-preview-slot input[type="color"]').forEach(input => {
            input.addEventListener('input', function() {
                updateColorSlot(this);
            });
        });

        // Currency Search Filter
        const currencySearch = document.getElementById('currency-search');
        const currencySelect = document.getElementById('currency-select');
        const currencySymbolInput = document.querySelector('input[name="settings[currency_symbol]"]');

        if (currencySearch && currencySelect) {
            currencySearch.addEventListener('input', function() {
                const term = this.value.toLowerCase();
                const options = currencySelect.options;
                for (let i = 0; i < options.length; i++) {
                    const text = options[i].text.toLowerCase();
                    options[i].hidden = !text.includes(term);
                }
            });

            currencySelect.addEventListener('change', function() {
                const opt = this.options[this.selectedIndex];
                const sym = opt.getAttribute('data-symbol');
                if (sym && currencySymbolInput) {
                    currencySymbolInput.value = sym;
                }
            });
        }
    });

    function updateColorSlot(input) {
        const slot = input.closest('.settings-color-preview-slot');
        if (slot) slot.style.backgroundColor = input.value;
        const card = input.closest('.settings-color-card');
        if (card) {
            const text = card.querySelector('.color-text');
            if (text) text.textContent = input.value;
        }
    }

    function updateStampSize() {
        const range = document.getElementById('stamp-scale-range');
        const val   = document.getElementById('stamp-scale-value');
        const img   = document.getElementById('stamp-preview-img');
        if (!range) return;

        const pct = parseInt(range.value, 10);
        if (val) val.textContent = pct;
        if (img) img.style.width = Math.round(160 * pct / 100) + 'px';
    }
</script>
@endpush
