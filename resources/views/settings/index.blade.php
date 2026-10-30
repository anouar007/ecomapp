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
            <a href="{{ route('settings.logos-icons') }}" class="btn btn-outline-danger shadow-sm rounded-pill px-3 py-2 d-inline-flex align-items-center justify-content-center gap-1.5 flex-grow-1 flex-md-grow-0 fw-semibold" style="font-size: 0.84rem;">
                <i class="fas fa-icons"></i> <span>Logos & Icônes du Site</span>
            </a>
            <a href="{{ route('custom-codes.index') }}" class="btn btn-outline-secondary shadow-sm rounded-pill px-3 py-2 d-inline-flex align-items-center justify-content-center gap-1.5 flex-grow-1 flex-md-grow-0 fw-semibold" style="font-size: 0.84rem;">
                <i class="fas fa-code text-primary"></i> <span>Codes & Scripts</span>
            </a>
            <button type="submit" form="settings-form" id="btn-save-settings-top" class="btn-brand-primary flex-grow-1 flex-md-grow-0 justify-content-center text-nowrap px-3.5 py-2 shadow-sm rounded-pill" style="font-size: 0.84rem;">
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
                <button type="button" class="settings-nav-btn" data-tab="email" onclick="switchSettingsTab('email')">
                    <span class="d-inline-flex align-items-center gap-2">
                        <i class="fas fa-envelope-open-text tab-icon"></i>
                        <span>Emails & Notifications</span>
                    </span>
                    <i class="fas fa-chevron-right tab-chevron small"></i>
                </button>
                <button type="button" class="settings-nav-btn" data-tab="advanced" onclick="switchSettingsTab('advanced')">
                    <span class="d-inline-flex align-items-center gap-2">
                        <i class="fas fa-cogs tab-icon"></i>
                        <span>Avancé</span>
                    </span>
                    <i class="fas fa-chevron-right tab-chevron small"></i>
                <button type="button" class="settings-nav-btn" data-tab="seo" onclick="switchSettingsTab('seo')">
                    <span class="d-inline-flex align-items-center gap-2">
                        <i class="fab fa-google tab-icon text-danger"></i>
                        <span>Google Console & SEO</span>
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
                <form action="{{ route('settings.update') }}" method="POST" id="settings-form" novalidate>
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

                        {{-- Mode Bientôt Disponible / Coming Soon --}}
                        <div class="settings-section-card border-warning-subtle shadow-xs">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <h3 class="settings-section-title m-0">
                                    <i class="fas fa-traffic-light text-warning"></i>
                                    <span>Mode "Bientôt Disponible / Coming Soon"</span>
                                </h3>
                                <span class="badge {{ setting('coming_soon_enabled', false) ? 'bg-warning text-dark' : 'bg-success' }} rounded-pill px-3 py-1.5 fw-bold">
                                    {{ setting('coming_soon_enabled', false) ? 'ACTIF (Site masqué au public)' : 'INACTIF (Site public en ligne)' }}
                                </span>
                            </div>
                            <p class="settings-section-desc">
                                Activez ce mode pour afficher une page d'attente soignée à vos clients pendant que vous finalisez le catalogue. L'administration et la connexion restent 100% accessibles.
                            </p>

                            <div class="row g-3 mb-3">
                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-bold small text-dark">Statut d'accès public</label>
                                    <select name="settings[coming_soon_enabled]" class="form-select rounded-3 fw-semibold">
                                        <option value="0" {{ !setting('coming_soon_enabled', false) ? 'selected' : '' }}>🟢 Site Ouvert (En ligne public)</option>
                                        <option value="1" {{ setting('coming_soon_enabled', false) ? 'selected' : '' }}>🔒 Mode Coming Soon (Page d'attente)</option>
                                    </select>
                                </div>

                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-bold small text-dark">Clé secrète d'accès équipe (Preview)</label>
                                    <input type="text" name="settings[coming_soon_secret]" class="form-control rounded-3 font-monospace" 
                                           value="{{ setting('coming_soon_secret', 'wina2026') }}" placeholder="wina2026">
                                    <div class="form-text small">Permet de tester le site public via <code>?preview=wina2026</code></div>
                                </div>

                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-bold small text-dark">Date cible d'ouverture (Compte à rebours)</label>
                                    <input type="datetime-local" name="settings[coming_soon_launch_date]" class="form-control rounded-3" 
                                           value="{{ setting('coming_soon_launch_date') ? date('Y-m-d\TH:i', strtotime(setting('coming_soon_launch_date'))) : date('Y-m-d\TH:i', strtotime('+7 days')) }}">
                                </div>
                            </div>

                            <div class="p-2.5 bg-light rounded-3 border small d-flex flex-wrap align-items-center justify-content-between gap-2">
                                <div>
                                    <i class="fas fa-link text-primary me-1"></i>
                                    <strong>Lien direct pour prévisualiser le site :</strong> 
                                    <a href="{{ url('/?preview=' . setting('coming_soon_secret', 'wina2026')) }}" target="_blank" class="font-monospace text-decoration-none">
                                        {{ url('/?preview=' . setting('coming_soon_secret', 'wina2026')) }}
                                    </a>
                                </div>
                                <span class="text-muted"><i class="fas fa-terminal me-1"></i>Ou activez via le terminal : <code>php artisan coming-soon enable</code></span>
                            </div>
                        </div>

                        {{-- Logo Section --}}
                        <div class="settings-section-card">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                                <h3 class="settings-section-title m-0">
                                    <i class="fas fa-image text-danger"></i>
                                    <span>Logo Officiel de la Boutique</span>
                                </h3>
                                <a href="{{ route('settings.logos-icons') }}" class="btn btn-sm btn-danger rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5">
                                    <i class="fas fa-icons"></i>
                                    <span>Gérer tous les Logos & Icônes (Header, Mobile, Footer, Favicon...)</span>
                                </a>
                            </div>
                            <p class="settings-section-desc">Le logo principal s'affiche par défaut dans l'en-tête, les factures et les communications clients. Pour personnaliser chaque emplacement séparément et toutes les icônes du site, ouvrez le studio <strong>Logos & Icônes</strong>.</p>
                            
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
                                    <input type="text" name="settings[company_website]" class="form-control rounded-3" 
                                           value="{{ $companySettings->where('key', 'company_website')->first()?->value ?? '' }}" placeholder="https://winashop.ma">
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

                    {{-- 9. Email & SMTP Notifications Tab --}}
                    <div class="tab-pane d-none" id="email-tab">
                        {{-- Notification Header Card --}}
                        <div class="settings-section-card">
                            <h3 class="settings-section-title">
                                <i class="fas fa-bell text-danger"></i>
                                <span>Notifications de Nouvelles Commandes</span>
                            </h3>
                            <p class="settings-section-desc">Recevez une alerte email instantanée avec le récapitulatif complet (client, téléphone, ville, articles, total) dès qu'un client passe commande sur le site.</p>

                            <div class="row g-3">
                                <div class="col-12 col-md-5">
                                    <label class="form-label fw-bold small text-dark">Statut des alertes</label>
                                    <div class="form-check form-switch mt-2">
                                        <input type="hidden" name="settings[order_notifications_enabled]" value="0">
                                        <input class="form-check-input" type="checkbox" name="settings[order_notifications_enabled]" value="1" id="order_notifications_enabled" {{ setting('order_notifications_enabled', true) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold text-dark small" for="order_notifications_enabled">
                                            Activer l'envoi d'email à chaque nouvelle commande
                                        </label>
                                    </div>
                                </div>

                                <div class="col-12 col-md-7">
                                    <label class="form-label fw-bold small text-dark">Email(s) qui reçoivent les alertes <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fas fa-inbox text-danger"></i></span>
                                        <input type="text" name="settings[order_notification_email]" class="form-control rounded-end-3" 
                                               value="{{ setting('order_notification_email', setting('contact_email', 'contact@winashop.ma')) }}" 
                                               placeholder="ex: contact@winashop.ma ou manager@winashop.ma">
                                    </div>
                                    <div class="form-text small text-muted">
                                        L'adresse où vous recevrez les commandes. Vous pouvez en saisir plusieurs séparées par une virgule.
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- SMTP Server Configuration Card --}}
                        <div class="settings-section-card mt-4">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                                <h3 class="settings-section-title mb-0">
                                    <i class="fas fa-server text-danger"></i>
                                    <span>Configuration du Serveur d'Envoi (SMTP)</span>
                                </h3>
                                <div class="d-flex align-items-center gap-1.5">
                                    <span class="badge bg-light text-dark border">
                                        <i class="fas fa-lock text-success me-1"></i>Connexion Sécurisée
                                    </span>
                                </div>
                            </div>
                            <p class="settings-section-desc">Identifiants de la boîte email utilisée par votre site pour expédier les notifications.</p>

                            {{-- Quick Preset Buttons --}}
                            <div class="d-flex align-items-center gap-2 mb-3 flex-wrap bg-light p-2.5 rounded-3 border">
                                <span class="small text-muted fw-bold"><i class="fas fa-magic text-warning me-1"></i> Pré-remplir pour :</span>
                                <button type="button" class="btn btn-sm btn-white bg-white border shadow-sm rounded-pill px-3 py-1" onclick="applySmtpPreset('hostinger')">
                                    <i class="fas fa-cloud me-1 text-primary"></i> <strong>Hostinger Webmail</strong> (Recommandé)
                                </button>
                                <button type="button" class="btn btn-sm btn-white bg-white border shadow-sm rounded-pill px-3 py-1" onclick="applySmtpPreset('gmail')">
                                    <i class="fab fa-google me-1 text-danger"></i> <strong>Gmail SMTP</strong>
                                </button>
                            </div>

                            <div class="row g-3">
                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-bold small text-dark">Email d'envoi / Compte SMTP <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fas fa-user-circle text-muted"></i></span>
                                        <input type="text" name="settings[mail_username]" id="mail_username_input" class="form-control rounded-end-3" 
                                               value="{{ setting('mail_username', '') }}" 
                                               placeholder="ex: contact@anouar.top ou no-reply@winashop.ma"
                                               oninput="syncFromAddress(this.value)">
                                    </div>
                                    <div class="form-text small text-muted">L'adresse email complète créée sur votre hébergement.</div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-bold small text-dark">Mot de passe de la boîte email <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="fas fa-key text-muted"></i></span>
                                        <input type="password" name="settings[mail_password]" id="mail_password_input" class="form-control" 
                                               value="{{ setting('mail_password', '') }}" 
                                               placeholder="Mot de passe de la boîte email">
                                        <button class="btn btn-outline-secondary rounded-end-3" type="button" onclick="togglePasswordVisibility('mail_password_input', this)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <div class="form-text small text-muted">Mot de passe secret utilisé pour envoyer les emails.</div>
                                </div>

                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-bold small text-dark">Serveur SMTP (Host)</label>
                                    <input type="text" name="settings[mail_host]" id="mail_host_input" class="form-control rounded-3" 
                                           value="{{ setting('mail_host', 'smtp.hostinger.com') }}" 
                                           placeholder="smtp.hostinger.com">
                                </div>

                                <div class="col-6 col-md-4">
                                    <label class="form-label fw-bold small text-dark">Port SMTP</label>
                                    <input type="number" name="settings[mail_port]" id="mail_port_input" class="form-control rounded-3" 
                                           value="{{ setting('mail_port', 465) }}" 
                                           placeholder="465">
                                </div>

                                <div class="col-6 col-md-4">
                                    <label class="form-label fw-bold small text-dark">Chiffrement (Encryption)</label>
                                    <select name="settings[mail_encryption]" id="mail_encryption_input" class="form-select rounded-3">
                                        <option value="ssl" {{ setting('mail_encryption', 'ssl') == 'ssl' ? 'selected' : '' }}>SSL (Port 465 - Recommandé)</option>
                                        <option value="tls" {{ setting('mail_encryption') == 'tls' ? 'selected' : '' }}>TLS (Port 587)</option>
                                        <option value="none" {{ setting('mail_encryption') == 'none' ? 'selected' : '' }}>Aucun (Non sécurisé)</option>
                                    </select>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-bold small text-dark">Email expéditeur affiché aux destinataires</label>
                                    <input type="email" name="settings[mail_from_address]" id="mail_from_address_input" class="form-control rounded-3" 
                                           value="{{ setting('mail_from_address', setting('mail_username', 'contact@winashop.ma')) }}" 
                                           placeholder="ex: no-reply@winashop.ma">
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-bold small text-dark">Nom de l'expéditeur affiché</label>
                                    <input type="text" name="settings[mail_from_name]" class="form-control rounded-3" 
                                           value="{{ setting('mail_from_name', setting('app_name', 'Wina Shop')) }}" 
                                           placeholder="ex: Wina Shop Maroc">
                                </div>
                            </div>
                        </div>

                        {{-- Test SMTP Diagnostic Card --}}
                        <div class="settings-section-card mt-4 border-primary shadow-sm" style="background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                                <div>
                                    <h4 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2" style="font-size: 1.05rem;">
                                        <i class="fas fa-paper-plane text-primary"></i>
                                        <span>Tester la configuration et envoyer un email test</span>
                                    </h4>
                                    <p class="small text-muted mb-0">
                                        N'oubliez pas de cliquer sur <strong>"Enregistrer les modifications"</strong> avant de lancer le test.
                                    </p>
                                </div>
                                <div>
                                    <button type="submit" form="test-email-form" class="btn btn-primary rounded-pill px-4 py-2.5 fw-semibold shadow-sm">
                                        <i class="fas fa-vial me-1.5"></i> Tester l'envoi immédiat
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 10. SEO & Google Search Console Tab --}}
                    <div class="tab-pane d-none" id="seo-tab">
                        {{-- Google Console Card --}}
                        <div class="settings-section-card">
                            <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                                <h3 class="settings-section-title mb-0">
                                    <i class="fab fa-google text-danger"></i>
                                    <span>Google Search Console & Indexation</span>
                                </h3>
                                <a href="https://search.google.com/search-console" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5">
                                    <i class="fas fa-external-link-alt"></i> Ouvrir Google Search Console
                                </a>
                            </div>
                            <p class="settings-section-desc">
                                Connectez votre boutique à <strong>Google Search Console</strong> pour indexer instantanément vos produits, surveiller vos positions de recherche et être le premier résultat sur Google.
                            </p>

                            <div class="row g-3 mb-4">
                                <div class="col-12">
                                    <label class="form-label fw-bold small text-dark">
                                        <i class="fas fa-shield-alt text-primary me-1"></i> Code de Validation Google (Balise Meta)
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light text-muted font-monospace small">&lt;meta name="google-site-verification" content="</span>
                                        <input type="text" name="settings[google_site_verification]" class="form-control font-monospace" 
                                               value="{{ setting('google_site_verification', '') }}" 
                                               placeholder="Ex: d1c6999fa_abc123XYZ456...">
                                        <span class="input-group-text bg-light text-muted font-monospace small">"&gt;</span>
                                    </div>
                                    <div class="form-text small text-muted">
                                        Dans Google Search Console, choisissez la méthode <strong>"Balise HTML"</strong>, copiez la valeur de <code>content="..."</code> et collez-la ici.
                                    </div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-bold small text-dark">
                                        <i class="fas fa-chart-line text-success me-1"></i> Google Analytics 4 (ID de Mesure GA4)
                                    </label>
                                    <input type="text" name="settings[google_analytics_id]" class="form-control rounded-3" 
                                           value="{{ setting('google_analytics_id', '') }}" 
                                           placeholder="G-XXXXXXXXXX">
                                    <div class="form-text small text-muted">Exemple : <code>G-ABC1234567</code>. Permet de suivre les visiteurs et conversions depuis Google.</div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label fw-bold small text-dark">
                                        <i class="fab fa-microsoft text-info me-1"></i> Validation Bing Webmaster Tools
                                    </label>
                                    <input type="text" name="settings[bing_site_verification]" class="form-control rounded-3" 
                                           value="{{ setting('bing_site_verification', '') }}" 
                                           placeholder="Code de validation Bing (msvalidate.01)">
                                    <div class="form-text small text-muted">Indexation sur Bing, Yahoo et les moteurs de recherche Microsoft.</div>
                                </div>
                            </div>
                        </div>

                        {{-- Sitemaps & Crawler Endpoints Card --}}
                        <div class="settings-section-card mt-4">
                            <h3 class="settings-section-title">
                                <i class="fas fa-sitemap text-danger"></i>
                                <span>URLs du Sitemap & Directives d'Indexation pour Google</span>
                            </h3>
                            <p class="settings-section-desc">
                                Transmettez ces adresses directement dans l'onglet <strong>"Sitemaps"</strong> de votre Google Search Console.
                            </p>

                            <div class="row g-3 mb-3">
                                {{-- Sitemap XML --}}
                                <div class="col-12">
                                    <div class="p-3 bg-light rounded-3 border d-flex align-items-center justify-content-between flex-wrap gap-2">
                                        <div>
                                            <div class="fw-bold text-dark d-flex align-items-center gap-2">
                                                <i class="fas fa-file-code text-danger"></i>
                                                <span>Sitemap Principal XML (Produits, Catégories, Images HD)</span>
                                                <span class="badge bg-success-subtle text-success border border-success-subtle">Actif & Dynamique</span>
                                            </div>
                                            <div class="font-monospace text-muted small mt-1">
                                                <a href="{{ url('/sitemap.xml') }}" target="_blank" class="text-decoration-none text-primary">{{ url('/sitemap.xml') }}</a>
                                            </div>
                                        </div>
                                        <div class="d-flex gap-2">
                                            <a href="{{ url('/sitemap.xml') }}" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                                                <i class="fas fa-eye me-1"></i> Voir
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="navigator.clipboard.writeText('{{ url('/sitemap.xml') }}'); alert('URL du sitemap copiée !');">
                                                <i class="far fa-copy me-1"></i> Copier l'URL
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                {{-- Robots TXT --}}
                                <div class="col-12">
                                    <div class="p-3 bg-light rounded-3 border d-flex align-items-center justify-content-between flex-wrap gap-2">
                                        <div>
                                            <div class="fw-bold text-dark d-flex align-items-center gap-2">
                                                <i class="fas fa-robot text-primary"></i>
                                                <span>Fichier Robots.txt (Autorisations Googlebot & Google-Extended)</span>
                                                <span class="badge bg-info-subtle text-info border border-info-subtle">Optimisé Google</span>
                                            </div>
                                            <div class="font-monospace text-muted small mt-1">
                                                <a href="{{ url('/robots.txt') }}" target="_blank" class="text-decoration-none text-primary">{{ url('/robots.txt') }}</a>
                                            </div>
                                        </div>
                                        <div class="d-flex gap-2">
                                            <a href="{{ url('/robots.txt') }}" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                                                <i class="fas fa-eye me-1"></i> Voir
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="navigator.clipboard.writeText('{{ url('/robots.txt') }}'); alert('URL du robots.txt copiée !');">
                                                <i class="far fa-copy me-1"></i> Copier l'URL
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                {{-- LLMs / AI Search Discovery --}}
                                <div class="col-12">
                                    <div class="p-3 bg-light rounded-3 border d-flex align-items-center justify-content-between flex-wrap gap-2">
                                        <div>
                                            <div class="fw-bold text-dark d-flex align-items-center gap-2">
                                                <i class="fas fa-brain text-primary"></i>
                                                <span>Catalogue AI Search Engine (Google Gemini, Perplexity, ChatGPT)</span>
                                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle">GEO IA Actif</span>
                                            </div>
                                            <div class="font-monospace text-muted small mt-1">
                                                <a href="{{ url('/llms.txt') }}" target="_blank" class="text-decoration-none text-primary">{{ url('/llms.txt') }}</a>
                                            </div>
                                        </div>
                                        <div class="d-flex gap-2">
                                            <a href="{{ url('/llms.txt') }}" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                                                <i class="fas fa-eye me-1"></i> Voir
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="navigator.clipboard.writeText('{{ url('/llms.txt') }}'); alert('URL llms.txt copiée !');">
                                                <i class="far fa-copy me-1"></i> Copier l'URL
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- SEO Ranking Strategy Guide --}}
                        <div class="settings-section-card mt-4 p-3.5 bg-success-subtle bg-opacity-10 border border-success-subtle rounded-4">
                            <h4 class="fw-bold text-success mb-2 d-flex align-items-center gap-2">
                                <i class="fas fa-trophy"></i>
                                <span>Comment être #1 sur Google pour vos produits ?</span>
                            </h4>
                            <div class="small text-dark lh-lg">
                                <ul class="mb-0 ps-3">
                                    <li><strong>Validation Google Search Console :</strong> Choisissez <em>Validation par balise HTML</em> ou <em>Fichier HTML</em> (le système valide automatiquement n'importe quel fichier <code>/google*.html</code>).</li>
                                    <li><strong>Soumettez votre Sitemap :</strong> Dans Google Search Console &gt; Sitemaps, entrez simplement <code>sitemap.xml</code> et cliquez sur "Envoyer".</li>
                                    <li><strong>Titres des Produits :</strong> Incluez toujours la marque et le modèle exact (ex: <em>"Sony Alpha 7 IV"</em> ou <em>"DJI Osmo Mobile 6"</em>). Le système génère automatiquement les métadonnées de localisation (Casablanca, Maroc, prix en MAD, disponibilité et garantie).</li>
                                    <li><strong>Données Structurées Google Shopping :</strong> Vos fiches produits génèrent automatiquement les balises Schema.org <code>Product</code>, <code>Offer</code>, <code>BreadcrumbList</code> et <code>FAQPage</code> pour afficher les avis étoiles, le prix et le badge "En stock" directement dans les résultats Google !</li>
                                </ul>
                            </div>
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

    <form id="test-email-form" action="{{ route('settings.test-email') }}" method="POST" class="d-none">
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

        // Top save button handler
        const topBtn = document.getElementById('btn-save-settings-top');
        if (topBtn) {
            topBtn.addEventListener('click', function(e) {
                const form = document.getElementById('settings-form');
                if (form) {
                    e.preventDefault();
                    if (typeof form.requestSubmit === 'function') {
                        form.requestSubmit();
                    } else {
                        form.submit();
                    }
                }
            });
        }

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

    function togglePasswordVisibility(inputId, btn) {
        const input = document.getElementById(inputId);
        if (!input) return;
        const icon = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            if (icon) { icon.classList.remove('fa-eye'); icon.classList.add('fa-eye-slash'); }
        } else {
            input.type = 'password';
            if (icon) { icon.classList.remove('fa-eye-slash'); icon.classList.add('fa-eye'); }
        }
    }

    function syncFromAddress(val) {
        const fromInput = document.getElementById('mail_from_address_input');
        if (fromInput && (!fromInput.value || fromInput.value.includes('@'))) {
            fromInput.value = val;
        }
    }

    function applySmtpPreset(preset) {
        const host = document.getElementById('mail_host_input');
        const port = document.getElementById('mail_port_input');
        const enc = document.getElementById('mail_encryption_input');
        if (preset === 'hostinger') {
            if (host) host.value = 'smtp.hostinger.com';
            if (port) port.value = 465;
            if (enc) enc.value = 'ssl';
        } else if (preset === 'gmail') {
            if (host) host.value = 'smtp.gmail.com';
            if (port) port.value = 465;
            if (enc) enc.value = 'ssl';
        }
    }
</script>
@endpush
