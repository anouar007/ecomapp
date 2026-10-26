@push('styles')
<style>
/* ==========================================================================
   SMART PRODUCT DESCRIPTION STUDIO (VISUAL WYSIWYG + REAL PDP STYLES)
   ========================================================================== */
.smart-studio-container {
    border: 1px solid #cbd5e1;
    border-radius: 14px;
    background: #ffffff;
    box-shadow: 0 4px 18px rgba(15, 23, 42, 0.05);
    overflow: hidden;
    position: relative;
    transition: all 0.2s ease;
}

.smart-studio-container:focus-within {
    border-color: #dc2626;
    box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.12);
}

/* Studio Header & Mode Tabs */
.smart-studio-header {
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    padding: 10px 16px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}

.smart-mode-tabs {
    display: inline-flex;
    background: #e2e8f0;
    border-radius: 10px;
    padding: 3px;
    gap: 3px;
}

.smart-mode-tab {
    border: none;
    background: transparent;
    color: #475569;
    font-size: 12.5px;
    font-weight: 600;
    padding: 6px 14px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    cursor: pointer;
    transition: all 0.18s ease;
}

.smart-mode-tab:hover {
    color: #0f172a;
}

.smart-mode-tab.active {
    background: #ffffff;
    color: #dc2626;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
}

.smart-helper-actions {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
}

.smart-action-btn {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    color: #334155;
    font-size: 12px;
    font-weight: 600;
    border-radius: 8px;
    padding: 6px 12px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    cursor: pointer;
    transition: all 0.18s ease;
}

.smart-action-btn:hover {
    background: #f1f5f9;
    border-color: #94a3b8;
    color: #0f172a;
    transform: translateY(-1px);
}

.smart-action-btn-danger {
    background: #fef2f2;
    border-color: #fecaca;
    color: #dc2626;
}

.smart-action-btn-danger:hover {
    background: #fee2e2;
    border-color: #fca5a5;
    color: #b91c1c;
}

/* Formatting Toolbar */
.smart-format-toolbar {
    background: #ffffff;
    border-bottom: 1px solid #e2e8f0;
    padding: 8px 14px;
    display: flex;
    align-items: center;
    gap: 4px;
    flex-wrap: wrap;
}

.smart-format-btn {
    border: 1px solid transparent;
    background: transparent;
    color: #475569;
    border-radius: 6px;
    width: 32px;
    height: 32px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    cursor: pointer;
    transition: all 0.15s ease;
}

.smart-format-btn:hover {
    background: #f1f5f9;
    border-color: #cbd5e1;
    color: #0f172a;
}

.smart-format-btn.active {
    background: #fee2e2;
    color: #dc2626;
    border-color: #fecaca;
}

.smart-format-select {
    border: 1px solid #cbd5e1;
    background: #ffffff;
    color: #334155;
    font-size: 12px;
    font-weight: 600;
    border-radius: 6px;
    padding: 4px 8px;
    height: 32px;
    outline: none;
    cursor: pointer;
}

.smart-format-divider {
    width: 1px;
    height: 20px;
    background: #e2e8f0;
    margin: 0 4px;
}

/* ==========================================================================
   AUTHENTIC PDP STYLES (MATCHES EXACT FRONTEND CAMERA & CINEMA DESIGN)
   ========================================================================== */
.smart-visual-editor {
    min-height: 480px;
    max-height: 800px;
    overflow-y: auto !important;
    overflow-x: hidden !important;
    position: relative !important;
    isolation: isolate;
    contain: layout paint style;
    padding: 24px 28px;
    outline: none;
    background: #ffffff;
    color: #1e293b;
    font-family: inherit;
    line-height: 1.7;
    font-size: 14.5px;
    box-sizing: border-box !important;
}

/* Explicit bold styling inside editor and preview to guarantee crisp bold rendering */
.smart-visual-editor b,
.smart-visual-editor strong,
.smart-visual-editor [style*="font-weight: bold"],
.smart-visual-editor [style*="font-weight:bold"],
.smart-visual-editor [style*="font-weight: 700"],
.smart-preview-container b,
.smart-preview-container strong,
.smart-preview-container [style*="font-weight: bold"],
.smart-preview-container [style*="font-weight:bold"],
.smart-preview-container [style*="font-weight: 700"] {
    font-weight: 700 !important;
    color: #0f172a !important;
}

.smart-visual-editor i,
.smart-visual-editor em,
.smart-preview-container i,
.smart-preview-container em {
    font-style: italic !important;
}

.smart-visual-editor u,
.smart-preview-container u {
    text-decoration: underline !important;
}

.smart-visual-editor s,
.smart-visual-editor strike,
.smart-preview-container s,
.smart-preview-container strike {
    text-decoration: line-through !important;
}

/* Bulletproof CSS Sandbox: Prevent ANY internal elements from escaping or breaking interface */
.smart-visual-editor * {
    max-width: 100% !important;
    box-sizing: border-box !important;
}

/* Neutralize rogue fixed/absolute/modal styling */
.smart-visual-editor [style*="position: fixed"],
.smart-visual-editor [style*="position:fixed"],
.smart-visual-editor .fixed {
    position: relative !important;
    inset: auto !important;
}

.smart-visual-editor [style*="position: absolute"],
.smart-visual-editor [style*="position:absolute"],
.smart-visual-editor .absolute {
    position: relative !important;
    inset: auto !important;
}

.smart-visual-editor [style*="z-index"],
.smart-visual-editor [class*="z-"] {
    z-index: 1 !important;
}

.smart-visual-editor [style*="100vw"],
.smart-visual-editor .w-screen,
.smart-visual-editor [class*="w-screen"] {
    width: 100% !important;
    max-width: 100% !important;
}

.smart-visual-editor [style*="100vh"],
.smart-visual-editor [style*="100svh"],
.smart-visual-editor .h-svh,
.smart-visual-editor .h-screen,
.smart-visual-editor [class*="h-svh"],
.smart-visual-editor [class*="h-screen"] {
    height: auto !important;
    min-height: 0 !important;
}

.smart-visual-editor dialog {
    display: block !important;
    position: static !important;
    border: 1px dashed #cbd5e1 !important;
    padding: 10px !important;
    background: transparent !important;
}

.smart-visual-editor:focus {
    outline: none;
}

/* 1. Lead Intro Banner */
.smart-visual-editor .pdp-enrich-lead {
    background: #021962;
    border: 1px solid #021962;
    border-left: 4px solid #021962;
    border-radius: 12px;
    padding: 16px 20px;
    margin-bottom: 24px;
    box-shadow: 0 4px 14px rgba(2, 25, 98, 0.12);
    color: #ffffff;
}

.smart-visual-editor .pdp-enrich-lead p {
    font-size: 1.05rem;
    line-height: 1.65;
    color: #ffffff;
    margin: 0;
}

/* 2. Headings with Red Accent Icons */
.smart-visual-editor .pdp-enrich-heading {
    font-size: 1.25rem;
    font-weight: 700;
    color: #0f172a;
    margin: 28px 0 16px 0;
    padding-bottom: 8px;
    border-bottom: 2px solid #f1f5f9;
    display: flex;
    align-items: center;
    gap: 8px;
}

.smart-visual-editor .pdp-enrich-heading i {
    color: #dc2626 !important;
}

/* 3. Specifications Card Grid */
.smart-visual-editor .pdp-specs-block {
    margin-bottom: 28px;
}

.smart-visual-editor .pdp-specs-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    gap: 12px;
    margin: 16px 0 24px 0;
}

.smart-visual-editor .pdp-spec-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 12px 16px;
    display: flex;
    flex-direction: column;
    gap: 4px;
    position: relative;
    transition: all 0.2s ease;
}

.smart-visual-editor .pdp-spec-card:hover {
    background: #ffffff;
    border-color: #cbd5e1;
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.06);
}

.smart-visual-editor .pdp-spec-card .pdp-spec-label {
    font-size: 0.76rem;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    color: #64748b;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 6px;
}

.smart-visual-editor .pdp-spec-card .pdp-spec-label i {
    color: #dc2626;
    font-size: 0.85rem;
}

.smart-visual-editor .pdp-spec-card .pdp-spec-value {
    font-size: 0.94rem;
    color: #0f172a;
    font-weight: 600;
    word-break: break-word;
}

/* Interactive delete button on hover for spec card in editor */
.smart-visual-editor .pdp-spec-card .spec-remove-btn {
    position: absolute;
    top: 6px;
    right: 8px;
    border: none;
    background: #fee2e2;
    color: #dc2626;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    font-size: 11px;
    display: none;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    line-height: 1;
    z-index: 5;
}

.smart-visual-editor .pdp-spec-card:hover .spec-remove-btn {
    display: inline-flex;
}

/* 4. Presentation Body & Diagrams */
.smart-visual-editor .pdp-enrich-body {
    margin-bottom: 28px;
}

.smart-visual-editor .pdp-body-text {
    color: #334155;
    font-size: 0.96rem;
    line-height: 1.75;
}

.smart-visual-editor .pdp-diagram-wrapper {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 16px;
    margin: 24px 0;
    text-align: center;
    box-shadow: 0 4px 16px rgba(15, 23, 42, 0.04);
}

.smart-visual-editor img,
.smart-visual-editor .pdp-diagram-img {
    max-width: 100%;
    height: auto;
    border-radius: 10px;
    object-fit: contain;
    cursor: pointer;
    transition: outline 0.15s ease;
}

.smart-visual-editor img:hover,
.smart-visual-editor img.img-selected {
    outline: 3px solid #dc2626 !important;
    outline-offset: 3px;
}

/* 5. Compatibility Block */
.smart-visual-editor .pdp-compatibility-card {
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    border-radius: 14px;
    padding: 16px 20px;
    margin: 22px 0;
}

.smart-visual-editor .pdp-compat-tag {
    display: inline-block;
    background: #ffffff;
    border: 1px solid #86efac;
    color: #166534;
    border-radius: 6px;
    padding: 4px 10px;
    font-size: 0.82rem;
    font-weight: 600;
    margin: 3px;
}

/* 6. Visual Showcase Gallery */
.smart-visual-editor .pdp-visual-showcase {
    margin-top: 28px;
    padding: 16px;
    border-top: 1px solid #f1f5f9;
    position: relative;
    border-radius: 12px;
    border: 1.5px dashed transparent;
    transition: all 0.2s ease;
}

.smart-visual-editor .pdp-visual-showcase:hover {
    border-color: #cbd5e1;
    background: #f8fafc;
}

.smart-visual-editor .pdp-visual-showcase .showcase-section-remove-btn {
    position: absolute;
    top: 12px;
    right: 12px;
    border: 1px solid #fecaca;
    background: #fff1f2;
    color: #e11d48;
    border-radius: 8px;
    padding: 5px 12px;
    font-size: 12px;
    font-weight: 700;
    display: none;
    align-items: center;
    gap: 6px;
    cursor: pointer;
    z-index: 20;
    box-shadow: 0 4px 12px rgba(225, 29, 72, 0.12);
    transition: all 0.15s;
}

.smart-visual-editor .pdp-visual-showcase:hover .showcase-section-remove-btn {
    display: inline-flex;
}

.smart-visual-editor .pdp-visual-showcase .showcase-section-remove-btn:hover {
    background: #e11d48;
    color: #ffffff;
    border-color: #e11d48;
    transform: translateY(-1px);
}

.smart-visual-editor .pdp-showcase-grid {
    display: grid;
    grid-template-columns: repeat(12, 1fr);
    gap: 16px;
    margin-top: 14px;
}

.smart-visual-editor .pdp-showcase-card {
    grid-column: span 3;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    box-shadow: 0 2px 8px rgba(15, 23, 42, 0.03);
    position: relative;
    transition: border-color 0.2s, box-shadow 0.2s;
}

.smart-visual-editor .pdp-showcase-card[data-size="25%"] {
    grid-column: span 3;
}
.smart-visual-editor .pdp-showcase-card[data-size="50%"] {
    grid-column: span 6;
}
.smart-visual-editor .pdp-showcase-card[data-size="75%"] {
    grid-column: span 9;
}
.smart-visual-editor .pdp-showcase-card[data-size="100%"] {
    grid-column: span 12;
}

.smart-visual-editor .pdp-showcase-card:hover {
    border-color: #cbd5e1;
    box-shadow: 0 6px 16px rgba(0,0,0,0.06);
}

.smart-visual-editor .pdp-showcase-card .showcase-remove-btn {
    position: absolute;
    top: 8px;
    right: 8px;
    border: none;
    background: #fee2e2;
    color: #dc2626;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    font-size: 13px;
    font-weight: 700;
    display: none;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    line-height: 1;
    z-index: 10;
    box-shadow: 0 2px 6px rgba(0,0,0,0.15);
    transition: all 0.2s;
}

.smart-visual-editor .pdp-showcase-card:hover .showcase-remove-btn {
    display: inline-flex;
}

.smart-visual-editor .pdp-showcase-card .showcase-remove-btn:hover {
    background: #dc2626;
    color: #ffffff;
    transform: scale(1.15);
}

.smart-visual-editor .pdp-showcase-card .showcase-upload-btn {
    position: absolute;
    top: 8px;
    left: 8px;
    border: none;
    background: #e0f2fe;
    color: #0284c7;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    font-size: 11px;
    display: none;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    line-height: 1;
    z-index: 10;
    box-shadow: 0 2px 6px rgba(0,0,0,0.15);
    transition: all 0.2s;
}

.smart-visual-editor .pdp-showcase-card:hover .showcase-upload-btn {
    display: inline-flex;
}

.smart-visual-editor .pdp-showcase-card .showcase-upload-btn:hover {
    background: #0284c7;
    color: #ffffff;
    transform: scale(1.15);
}

.smart-visual-editor .pdp-showcase-card .showcase-size-indicator {
    position: absolute;
    bottom: 34px;
    right: 8px;
    background: rgba(15, 23, 42, 0.75);
    color: #ffffff;
    border-radius: 6px;
    font-size: 10px;
    font-weight: 700;
    padding: 2px 6px;
    pointer-events: none;
    display: none;
    z-index: 5;
}

.smart-visual-editor .pdp-showcase-card:hover .showcase-size-indicator {
    display: block;
}

.smart-visual-editor .pdp-showcase-img-wrap {
    width: 100%;
    min-height: 180px;
    background: #f8fafc;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    padding: 10px;
}

.smart-visual-editor .pdp-showcase-card[data-size="50%"] .pdp-showcase-img-wrap {
    min-height: 240px;
}

.smart-visual-editor .pdp-showcase-card[data-size="75%"] .pdp-showcase-img-wrap {
    min-height: 290px;
}

.smart-visual-editor .pdp-showcase-card[data-size="100%"] .pdp-showcase-img-wrap {
    min-height: 350px;
}

.smart-visual-editor .pdp-showcase-img-wrap img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
    cursor: pointer;
}

.smart-visual-editor .pdp-showcase-caption {
    padding: 8px 12px;
    font-size: 0.78rem;
    font-weight: 600;
    color: #64748b;
    text-align: center;
    background: #f8fafc;
    border-top: 1px solid #f1f5f9;
}

.smart-visual-editor .pdp-showcase-caption[contenteditable="true"]:hover {
    background: #f1f5f9;
    outline: 1px dashed #cbd5e1;
    cursor: text;
}

@media (max-width: 991.98px) {
    .smart-visual-editor .pdp-showcase-card,
    .smart-visual-editor .pdp-showcase-card[data-size="25%"],
    .smart-visual-editor .pdp-showcase-card[data-size="50%"] {
        grid-column: span 6;
    }
    .smart-visual-editor .pdp-showcase-card[data-size="75%"],
    .smart-visual-editor .pdp-showcase-card[data-size="100%"] {
        grid-column: span 12;
    }
}

@media (max-width: 575.98px) {
    .smart-visual-editor .pdp-showcase-card,
    .smart-visual-editor .pdp-showcase-card[data-size="25%"],
    .smart-visual-editor .pdp-showcase-card[data-size="50%"],
    .smart-visual-editor .pdp-showcase-card[data-size="75%"],
    .smart-visual-editor .pdp-showcase-card[data-size="100%"] {
        grid-column: span 12 !important;
    }
}

/* Raw HTML View */
.smart-raw-textarea {
    display: none;
    width: 100%;
    min-height: 480px;
    font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
    font-size: 13px;
    line-height: 1.6;
    background: #090d16;
    color: #38bdf8;
    border: none;
    padding: 22px;
    outline: none;
    resize: vertical;
}

/* Live Store Preview View */
.smart-preview-container {
    display: none;
    min-height: 480px;
    max-height: 800px;
    overflow-y: auto;
    padding: 28px;
    background: #ffffff;
}

/* Floating Image Control Bar */
.smart-img-float-bar {
    position: absolute;
    background: #0f172a;
    border-radius: 8px;
    padding: 5px 8px;
    display: none;
    align-items: center;
    gap: 4px;
    z-index: 100;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.4);
}

.smart-img-float-btn {
    border: none;
    background: transparent;
    color: #cbd5e1;
    border-radius: 4px;
    padding: 4px 7px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.15s ease;
}

.smart-img-float-btn:hover {
    background: rgba(255, 255, 255, 0.2);
    color: #ffffff;
}

.smart-img-float-btn.active {
    background: #2563eb !important;
    color: #ffffff !important;
}

.smart-img-float-btn.btn-del {
    color: #f87171;
}

.smart-img-float-btn.btn-del:hover {
    background: #ef4444;
    color: #ffffff;
}

/* Showcase Size Selection Tiles */
.showcase-size-tile {
    transition: all 0.15s ease;
    border: 1.5px solid #e2e8f0;
}
.showcase-size-tile:hover {
    border-color: #3b82f6;
    background: #f8fafc;
}
.showcase-size-tile.active {
    border-color: #2563eb !important;
    background: #eff6ff !important;
    box-shadow: 0 0 0 1px #2563eb;
}

/* Bottom Stats Bar */
.smart-studio-footer {
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    padding: 8px 16px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 12px;
    color: #64748b;
}
</style>
@endpush

<div class="form-group mb-4">
    <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
        <label for="description" class="form-label mb-0 fw-semibold text-dark">
            <i class="fas fa-magic text-danger me-1.5"></i> Description Enrichie Officielle & Fiche Technique
        </label>
        <span class="badge bg-danger-subtle text-danger border border-danger-subtle small px-2.5 py-1">
            <i class="fas fa-check-circle me-1"></i>Format Officiel Cinema & Photo
        </span>
    </div>

    <div class="smart-studio-container" id="smartStudioWrapper">
        {{-- Top Navigation & Mode Tabs --}}
        <div class="smart-studio-header">
            <div class="smart-mode-tabs" role="tablist">
                <button type="button" class="smart-mode-tab active" id="tabModeVisual" onclick="switchStudioMode('visual')">
                    <i class="fas fa-eye text-danger"></i> <span>Éditeur Visuel (Styles réels)</span>
                </button>
                <button type="button" class="smart-mode-tab" id="tabModeHtml" onclick="switchStudioMode('html')">
                    <i class="fas fa-code text-primary"></i> <span>Code HTML Source</span>
                </button>
                <button type="button" class="smart-mode-tab" id="tabModePreview" onclick="switchStudioMode('preview')">
                    <i class="fas fa-desktop text-success"></i> <span>Aperçu Fiche Client</span>
                </button>
            </div>

            {{-- Quick Component Inserters --}}
            <div class="smart-helper-actions">
                <button type="button" class="smart-action-btn" onclick="insertNewSpecCard()" title="Ajouter une ligne de spécification technique">
                    <i class="fas fa-plus-circle text-danger"></i> <span>+ Spécification</span>
                </button>
                <button type="button" class="smart-action-btn" onclick="triggerDiagramUpload()" title="Insérer un schéma ou visuel officiel">
                    <i class="fas fa-image text-primary"></i> <span>+ Image / Schéma</span>
                </button>
                <button type="button" class="smart-action-btn" onclick="insertCompatibilityBlock()" title="Ajouter un encadré d'écosystème et compatibilité">
                    <i class="fas fa-plug text-success"></i> <span>+ Compatibilité</span>
                </button>
                <button type="button" class="smart-action-btn" onclick="insertShowcaseBlock()" title="Ajouter une carte de showcase visuel">
                    <i class="fas fa-camera text-warning"></i> <span>+ Showcase</span>
                </button>
                <button type="button" class="smart-action-btn" id="btnDeleteShowcase" onclick="removeAllShowcases()" title="Supprimer toute la galerie Showcase de la description" style="display: none; color: #dc2626; border-color: #fecaca; background: #fff1f2;">
                    <i class="fas fa-trash-alt text-danger"></i> <span>Supprimer Showcase</span>
                </button>
            </div>
        </div>

        {{-- Visual Formatting Toolbar --}}
        <div class="smart-format-toolbar" id="smartFormatToolbar">
            <select class="smart-format-select" id="selectFormatBlock" onchange="applyFormatBlock(this.value)">
                <option value="p">Paragraphe</option>
                <option value="h3">Titre Section (H3)</option>
                <option value="h4">Sous-Titre (H4)</option>
                <option value="blockquote">Citation / Note</option>
            </select>

            <div class="smart-format-divider"></div>

            <button type="button" id="btnFormatBold" class="smart-format-btn" onmousedown="event.preventDefault()" onclick="execCmd('bold')" title="Gras (Ctrl+B)">
                <i class="fas fa-bold"></i>
            </button>
            <button type="button" id="btnFormatItalic" class="smart-format-btn" onmousedown="event.preventDefault()" onclick="execCmd('italic')" title="Italique (Ctrl+I)">
                <i class="fas fa-italic"></i>
            </button>
            <button type="button" id="btnFormatUnderline" class="smart-format-btn" onmousedown="event.preventDefault()" onclick="execCmd('underline')" title="Souligné (Ctrl+U)">
                <i class="fas fa-underline"></i>
            </button>
            <button type="button" id="btnFormatStrike" class="smart-format-btn" onmousedown="event.preventDefault()" onclick="execCmd('strikeThrough')" title="Barré">
                <i class="fas fa-strikethrough"></i>
            </button>

            <div class="smart-format-divider"></div>

            <button type="button" id="btnFormatUl" class="smart-format-btn" onmousedown="event.preventDefault()" onclick="execCmd('insertUnorderedList')" title="Liste à puces">
                <i class="fas fa-list-ul"></i>
            </button>
            <button type="button" id="btnFormatOl" class="smart-format-btn" onmousedown="event.preventDefault()" onclick="execCmd('insertOrderedList')" title="Liste numérotée">
                <i class="fas fa-list-ol"></i>
            </button>

            <div class="smart-format-divider"></div>

            <button type="button" class="smart-format-btn" onmousedown="event.preventDefault()" onclick="execCmd('justifyLeft')" title="Aligner à gauche">
                <i class="fas fa-align-left"></i>
            </button>
            <button type="button" class="smart-format-btn" onmousedown="event.preventDefault()" onclick="execCmd('justifyCenter')" title="Centrer">
                <i class="fas fa-align-center"></i>
            </button>
            <button type="button" class="smart-format-btn" onmousedown="event.preventDefault()" onclick="execCmd('justifyRight')" title="Aligner à droite">
                <i class="fas fa-align-right"></i>
            </button>

            <div class="smart-format-divider"></div>

            <button type="button" class="smart-format-btn" onmousedown="event.preventDefault()" onclick="promptInsertLink()" title="Insérer un lien">
                <i class="fas fa-link"></i>
            </button>
            <button type="button" class="smart-format-btn" onmousedown="event.preventDefault()" onclick="execCmd('unlink')" title="Supprimer le lien">
                <i class="fas fa-unlink"></i>
            </button>
            <button type="button" class="smart-format-btn" onmousedown="event.preventDefault()" onclick="execCmd('removeFormat')" title="Effacer le style">
                <i class="fas fa-eraser"></i>
            </button>

            <div class="smart-format-divider"></div>

            <button type="button" class="smart-format-btn" onmousedown="event.preventDefault()" onclick="execCmd('undo')" title="Annuler (Ctrl+Z)">
                <i class="fas fa-undo"></i>
            </button>
            <button type="button" class="smart-format-btn" onmousedown="event.preventDefault()" onclick="execCmd('redo')" title="Rétablir (Ctrl+Y)">
                <i class="fas fa-redo"></i>
            </button>
        </div>

        {{-- Hidden Image Uploader Inputs --}}
        <input type="file" id="studioImageInput" accept="image/*" style="display: none;" onchange="handleStudioImageUpload(event)">
        <input type="file" id="replaceImageFileInput" accept="image/*" style="display: none;" onchange="handleReplaceImageFileInput(event)">

        {{-- 1. Main Visual ContentEditable Studio (Loaded safely via JS from escaped textarea) --}}
        <div id="smartVisualEditor" class="smart-visual-editor" contenteditable="true" spellcheck="false"></div>

        {{-- 2. Raw HTML Code Mode --}}
        <textarea id="smartRawHtmlArea" class="smart-raw-textarea" spellcheck="false">{{ $value ?? '' }}</textarea>

        {{-- 3. Live 1:1 Preview Mode --}}
        <div id="smartPreviewContainer" class="smart-preview-container"></div>

        {{-- Floating Image Resizer Bar (populated dynamically by selectImage) --}}
        <div id="smartImgFloatBar" class="smart-img-float-bar"></div>

        {{-- Modal pour ajouter/téléverser une image Showcase --}}
        <div id="modalShowcaseInsert" style="display: none; position: fixed; inset: 0; z-index: 99999; background: rgba(15, 23, 42, 0.72); backdrop-filter: blur(6px); align-items: center; justify-content: center; padding: 20px;">
            <div style="background: #ffffff; border-radius: 16px; max-width: 520px; width: 100%; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); overflow: hidden;">
                <div class="d-flex align-items-center justify-content-between p-3 border-bottom" style="background: #f8fafc;">
                    <div class="d-flex align-items-center gap-2">
                        <span class="rounded-circle d-flex align-items-center justify-content-center" style="width: 34px; height: 34px; background: #fef2f2; color: #dc2626;">
                            <i class="fas fa-camera"></i>
                        </span>
                        <div>
                            <h6 class="mb-0 fw-bold text-dark">Ajouter au Showcase Visuel</h6>
                            <small class="text-muted" style="font-size: 0.72rem;">Galerie de photos haute résolution de la description</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" onclick="closeShowcaseModal()" aria-label="Fermer"></button>
                </div>

                <div class="p-3">
                    {{-- Onglets Source: Upload ou URL --}}
                    <div class="d-flex gap-2 mb-3 p-1 rounded bg-light border">
                        <button type="button" class="btn btn-sm flex-fill fw-semibold btn-primary" id="btnTabShowcaseUpload" onclick="switchShowcaseModalTab('upload')">
                            <i class="fas fa-upload me-1"></i> Téléverser un fichier
                        </button>
                        <button type="button" class="btn btn-sm flex-fill fw-semibold btn-light text-secondary" id="btnTabShowcaseUrl" onclick="switchShowcaseModalTab('url')">
                            <i class="fas fa-link me-1"></i> Par lien URL
                        </button>
                    </div>

                    {{-- Section 1: Upload Fichier --}}
                    <div id="sectionShowcaseUpload">
                        <div id="showcaseDropzone" class="p-4 border border-2 border-dashed rounded-3 text-center bg-light" style="cursor: pointer; transition: all 0.2s;" onclick="document.getElementById('modalShowcaseFileInput').click()">
                            <i class="fas fa-cloud-upload-alt fa-2x text-primary mb-2"></i>
                            <div class="fw-semibold text-dark mb-1">Cliquez pour choisir une photo</div>
                            <div class="text-muted small">ou glissez-déposez ici (JPG, PNG, WEBP — max 12 Mo)</div>
                            <input type="file" id="modalShowcaseFileInput" accept="image/*" class="d-none" onchange="previewShowcaseFile(event)">
                        </div>
                    </div>

                    {{-- Section 2: Lien URL --}}
                    <div id="sectionShowcaseUrl" style="display: none;">
                        <label class="form-label fw-semibold small text-dark">URL directe de l'image</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light"><i class="fas fa-globe text-muted"></i></span>
                            <input type="url" id="modalShowcaseUrlInput" class="form-control" placeholder="https://..." oninput="previewShowcaseUrl(this.value)">
                        </div>
                    </div>

                    {{-- Preview Box --}}
                    <div id="showcasePreviewBox" class="mt-3 p-2 rounded bg-light border text-center" style="display: none; position: relative;">
                        <img id="showcasePreviewImg" src="" alt="Aperçu" style="max-height: 140px; max-width: 100%; object-fit: contain; border-radius: 8px;">
                        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 rounded-circle" style="width: 24px; height: 24px; padding: 0; line-height: 1;" onclick="clearShowcasePreview()" title="Supprimer la sélection">×</button>
                    </div>

                    {{-- Légende --}}
                    <div class="mt-3">
                        <label for="modalShowcaseCaptionInput" class="form-label fw-semibold small text-dark mb-1">
                            Légende de la photo
                        </label>
                        <input type="text" id="modalShowcaseCaptionInput" class="form-control form-control-sm" placeholder="Ex: Capteur plein format 4K avec autofocus IA">
                    </div>

                    {{-- Sélection de la taille / largeur --}}
                    <div class="mt-3">
                        <label class="form-label fw-semibold small text-dark mb-1 d-flex justify-content-between">
                            <span>Largeur de la carte</span>
                            <span class="text-muted fw-normal" style="font-size: 0.72rem;">(Modifiable aussi en 1 clic dans l'éditeur)</span>
                        </label>
                        <div class="row g-2" id="showcaseSizeOptions">
                            <div class="col-3">
                                <label class="border rounded p-2 text-center w-100 showcase-size-tile active" data-size="25%" style="cursor: pointer;">
                                    <input type="radio" name="showcaseCardSize" value="25%" class="d-none" checked>
                                    <div class="fw-bold small text-dark">25%</div>
                                    <div class="text-muted" style="font-size: 0.65rem;">1 col (standard)</div>
                                </label>
                            </div>
                            <div class="col-3">
                                <label class="border rounded p-2 text-center w-100 showcase-size-tile" data-size="50%" style="cursor: pointer;">
                                    <input type="radio" name="showcaseCardSize" value="50%" class="d-none">
                                    <div class="fw-bold small text-dark">50%</div>
                                    <div class="text-muted" style="font-size: 0.65rem;">2 colonnes</div>
                                </label>
                            </div>
                            <div class="col-3">
                                <label class="border rounded p-2 text-center w-100 showcase-size-tile" data-size="75%" style="cursor: pointer;">
                                    <input type="radio" name="showcaseCardSize" value="75%" class="d-none">
                                    <div class="fw-bold small text-dark">75%</div>
                                    <div class="text-muted" style="font-size: 0.65rem;">3 colonnes</div>
                                </label>
                            </div>
                            <div class="col-3">
                                <label class="border rounded p-2 text-center w-100 showcase-size-tile" data-size="100%" style="cursor: pointer;">
                                    <input type="radio" name="showcaseCardSize" value="100%" class="d-none">
                                    <div class="fw-bold small text-dark">100%</div>
                                    <div class="text-muted" style="font-size: 0.65rem;">Pleine largeur</div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex align-items-center justify-content-end gap-2 p-3 border-top" style="background: #f8fafc;">
                    <button type="button" class="btn btn-sm btn-outline-secondary px-3" onclick="closeShowcaseModal()">Annuler</button>
                    <button type="button" class="btn btn-sm btn-primary px-3 fw-semibold" id="btnConfirmInsertShowcase" onclick="submitShowcaseModal()">
                        <i class="fas fa-check me-1"></i> Ajouter au Showcase
                    </button>
                </div>
            </div>
        </div>

        {{-- Hidden Form Input for Backend Submission --}}
        <textarea id="description" name="description" style="display: none;">{{ $value ?? '' }}</textarea>

        {{-- Studio Footer / Status --}}
        <div class="smart-studio-footer">
            <span id="studioStatus">
                <i class="fas fa-check-circle text-success me-1"></i>
                <span>Tous les styles et visuels officiels sont chargés et modifiables en direct.</span>
            </span>
            <span id="studioWordCount" class="badge bg-light text-secondary border">0 mot</span>
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentStudioMode = 'visual';
let currentSelectedImg = null;

const visualEditor = document.getElementById('smartVisualEditor');
const rawHtmlArea = document.getElementById('smartRawHtmlArea');
const previewContainer = document.getElementById('smartPreviewContainer');
const hiddenTextarea = document.getElementById('description');
const formatToolbar = document.getElementById('smartFormatToolbar');
const imgFloatBar = document.getElementById('smartImgFloatBar');
const wordCountBadge = document.getElementById('studioWordCount');

// Safe in-browser HTML sanitization to neutralize rogue scripts, styles and broken elements
function safeSanitizeForEditor(rawHtml) {
    if (!rawHtml || typeof rawHtml !== 'string') return '';
    try {
        const parser = new DOMParser();
        const doc = parser.parseFromString(rawHtml, 'text/html');

        // 1. Remove dangerous script and style elements entirely
        doc.querySelectorAll('script, style').forEach(el => el.remove());

        // 2. Disarm any inline event handlers (onerror, onload, onclick, etc.)
        doc.querySelectorAll('*').forEach(el => {
            Array.from(el.attributes).forEach(attr => {
                if (attr.name.toLowerCase().startsWith('on')) {
                    el.removeAttribute(attr.name);
                }
            });

            // 3. Reset any rogue fixed/absolute inline styles
            if (el.style) {
                if (el.style.position === 'fixed' || el.style.position === 'absolute') {
                    el.style.position = 'relative';
                }
                if (el.style.zIndex && parseInt(el.style.zIndex) > 10) {
                    el.style.zIndex = '1';
                }
            }
        });

        return doc.body.innerHTML;
    } catch (err) {
        console.warn('Sanitizer fallback:', err);
        return rawHtml;
    }
}

document.addEventListener('DOMContentLoaded', function () {
    try {
        // 1. Initial Content Setup: parse safely inside sandbox to protect entire page DOM
        const initialHtml = hiddenTextarea.value || '';
        visualEditor.innerHTML = safeSanitizeForEditor(initialHtml);
        syncContentFromVisual();
        attachInteractiveListeners();
    } catch (e) {
        console.error('Erreur chargement studio visuel :', e);
        visualEditor.innerText = hiddenTextarea.value || '';
    }

    // 2. Realtime sync on typing/pasting in visual editor
    visualEditor.addEventListener('input', function () {
        try {
            syncContentFromVisual();
        } catch (e) {
            console.warn('Sync visual warning:', e);
        }
    });

    // 3. Sync on editing raw HTML
    rawHtmlArea.addEventListener('input', function () {
        try {
            hiddenTextarea.value = rawHtmlArea.value;
            visualEditor.innerHTML = safeSanitizeForEditor(rawHtmlArea.value);
            updateWordCount();
            attachInteractiveListeners();
        } catch (e) {
            console.warn('Sync raw html warning:', e);
        }
    });

    // 4. Ensure form submit always has fresh HTML
    const parentForm = visualEditor.closest('form');
    if (parentForm) {
        parentForm.addEventListener('submit', function () {
            if (currentStudioMode === 'html') {
                hiddenTextarea.value = rawHtmlArea.value;
            } else {
                // Clean temp editor artifacts before posting
                cleanEditorArtifacts();
                hiddenTextarea.value = visualEditor.innerHTML;
            }
        });
    }

    // 5. Click outside deselects image
    document.addEventListener('click', function (e) {
        if (!e.target.closest('#smartImgFloatBar') && !e.target.closest('.smart-visual-editor img')) {
            deselectImage();
        }
    });

    // Reposition floating bar on scroll
    visualEditor.addEventListener('scroll', function () {
        if (currentSelectedImg) updateFloatBarPosition();
    });

    // Keyboard shortcuts for formatting (Ctrl/Cmd + B for bold, I for italic, U for underline)
    visualEditor.addEventListener('keydown', function (e) {
        if (e.ctrlKey || e.metaKey) {
            const k = e.key.toLowerCase();
            if (k === 'b') {
                e.preventDefault();
                execCmd('bold');
            } else if (k === 'i') {
                e.preventDefault();
                execCmd('italic');
            } else if (k === 'u') {
                e.preventDefault();
                execCmd('underline');
            }
        }
    });

    // Update active toolbar button states on cursor position or text selection
    visualEditor.addEventListener('keyup', updateToolbarActiveStates);
    visualEditor.addEventListener('mouseup', updateToolbarActiveStates);
    document.addEventListener('selectionchange', function () {
        if (document.activeElement === visualEditor || visualEditor.contains(document.activeElement)) {
            updateToolbarActiveStates();
        }
    });
});

/* ==========================================================================
   MODE SWITCHING (VISUAL, HTML, PREVIEW)
   ========================================================================== */
function switchStudioMode(mode) {
    currentStudioMode = mode;
    deselectImage();

    // Update tab styles
    document.getElementById('tabModeVisual').classList.toggle('active', mode === 'visual');
    document.getElementById('tabModeHtml').classList.toggle('active', mode === 'html');
    document.getElementById('tabModePreview').classList.toggle('active', mode === 'preview');

    if (mode === 'visual') {
        visualEditor.innerHTML = hiddenTextarea.value;
        visualEditor.style.display = 'block';
        rawHtmlArea.style.display = 'none';
        previewContainer.style.display = 'none';
        formatToolbar.style.display = 'flex';
        attachInteractiveListeners();
        updateWordCount();
    } else if (mode === 'html') {
        cleanEditorArtifacts();
        rawHtmlArea.value = formatHtmlCode(visualEditor.innerHTML);
        visualEditor.style.display = 'none';
        rawHtmlArea.style.display = 'block';
        previewContainer.style.display = 'none';
        formatToolbar.style.display = 'none';
    } else if (mode === 'preview') {
        cleanEditorArtifacts();
        previewContainer.innerHTML = visualEditor.innerHTML;
        visualEditor.style.display = 'none';
        rawHtmlArea.style.display = 'none';
        previewContainer.style.display = 'block';
        formatToolbar.style.display = 'none';
    }
}

/* ==========================================================================
   SYNCHRONIZATION & WORD COUNT
   ========================================================================== */
const REMOVE_BTNS_SELECTOR = '.spec-remove-btn, .showcase-remove-btn, .showcase-section-remove-btn, .showcase-upload-btn, .showcase-size-indicator';

function syncContentFromVisual() {
    // Clone node to strip editor temporary UI like remove buttons before storing
    const clone = visualEditor.cloneNode(true);
    clone.querySelectorAll(REMOVE_BTNS_SELECTOR).forEach(b => b.remove());
    clone.querySelectorAll('.img-selected').forEach(img => img.classList.remove('img-selected'));

    const cleanHtml = clone.innerHTML;
    hiddenTextarea.value = cleanHtml;
    rawHtmlArea.value = cleanHtml;
    updateWordCount();

    const btnDelShowcase = document.getElementById('btnDeleteShowcase');
    if (btnDelShowcase) {
        const hasShowcase = !!visualEditor.querySelector('.pdp-visual-showcase, .pdp-showcase-card');
        btnDelShowcase.style.display = hasShowcase ? 'inline-flex' : 'none';
    }
}

function cleanEditorArtifacts() {
    visualEditor.querySelectorAll(REMOVE_BTNS_SELECTOR).forEach(b => b.remove());
    visualEditor.querySelectorAll('.img-selected').forEach(img => img.classList.remove('img-selected'));
}

function updateWordCount() {
    const text = visualEditor.innerText.trim();
    const words = text ? text.split(/\s+/).length : 0;
    if (wordCountBadge) {
        wordCountBadge.textContent = words + (words > 1 ? ' mots' : ' mot');
    }
}

/* ==========================================================================
   VISUAL FORMATTING COMMANDS
   ========================================================================== */
function execCmd(command, value = null) {
    if (currentStudioMode !== 'visual') {
        switchStudioMode('visual');
    }
    visualEditor.focus();
    document.execCommand(command, false, value);
    syncContentFromVisual();
    updateToolbarActiveStates();
}

function updateToolbarActiveStates() {
    if (currentStudioMode !== 'visual') return;
    try {
        const isBold = document.queryCommandState('bold');
        const isItalic = document.queryCommandState('italic');
        const isUnderline = document.queryCommandState('underline');
        const isStrike = document.queryCommandState('strikeThrough');
        const isUl = document.queryCommandState('insertUnorderedList');
        const isOl = document.queryCommandState('insertOrderedList');

        const btnB = document.getElementById('btnFormatBold');
        const btnI = document.getElementById('btnFormatItalic');
        const btnU = document.getElementById('btnFormatUnderline');
        const btnS = document.getElementById('btnFormatStrike');
        const btnUl = document.getElementById('btnFormatUl');
        const btnOl = document.getElementById('btnFormatOl');

        if (btnB) btnB.classList.toggle('active', !!isBold);
        if (btnI) btnI.classList.toggle('active', !!isItalic);
        if (btnU) btnU.classList.toggle('active', !!isUnderline);
        if (btnS) btnS.classList.toggle('active', !!isStrike);
        if (btnUl) btnUl.classList.toggle('active', !!isUl);
        if (btnOl) btnOl.classList.toggle('active', !!isOl);

        const formatSelect = document.getElementById('selectFormatBlock');
        if (formatSelect) {
            const blockVal = document.queryCommandValue('formatBlock');
            if (blockVal) {
                const tag = blockVal.toLowerCase().replace(/<|>/g, '');
                if (['p', 'h3', 'h4', 'blockquote'].includes(tag)) {
                    formatSelect.value = tag;
                }
            }
        }
    } catch (e) {
        // queryCommandState graceful catch
    }
}

function applyFormatBlock(tag) {
    visualEditor.focus();
    if (tag === 'p') {
        document.execCommand('formatBlock', false, '<p>');
    } else if (tag === 'h3') {
        document.execCommand('formatBlock', false, '<h3>');
    } else if (tag === 'h4') {
        document.execCommand('formatBlock', false, '<h4>');
    } else if (tag === 'blockquote') {
        document.execCommand('formatBlock', false, '<blockquote>');
    }
    syncContentFromVisual();
    updateToolbarActiveStates();
}

function promptInsertLink() {
    const url = prompt('Entrez l\'URL du lien :', 'https://');
    if (url && url !== 'https://') {
        execCmd('createLink', url);
    }
}

/* ==========================================================================
   SMART COMPONENT INSERTERS
   ========================================================================== */
// 1. Add Technical Spec Card
function insertNewSpecCard() {
    const label = prompt('Nom de la spécification technique (ex: Résolution, Capteur, Poids, Monture) :', 'Spécification');
    if (!label) return;
    const value = prompt(`Valeur pour "${label}" (ex: 4K 120fps, Plein Format, 840g) :`, 'Valeur');
    if (!value) return;

    let specsGrid = visualEditor.querySelector('.pdp-specs-grid');

    if (!specsGrid) {
        // Create the entire specs block if not present
        const specsBlock = document.createElement('div');
        specsBlock.className = 'pdp-specs-block mb-4';
        specsBlock.innerHTML = `
            <h3 class="pdp-enrich-heading">
                <i class="fas fa-sliders-h me-2 text-danger"></i>Spécifications Techniques Officielles
            </h3>
            <div class="pdp-specs-grid"></div>
        `;
        visualEditor.appendChild(specsBlock);
        specsGrid = specsBlock.querySelector('.pdp-specs-grid');
    }

    const newCard = document.createElement('div');
    newCard.className = 'pdp-spec-card';
    newCard.innerHTML = `
        <div class="pdp-spec-label"><i class="fas fa-tag"></i> ${escapeHtml(label)}</div>
        <div class="pdp-spec-value">${escapeHtml(value)}</div>
    `;

    specsGrid.appendChild(newCard);
    attachInteractiveListeners();
    syncContentFromVisual();
    newCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
}

// 2. Add Diagram / Official Visual
function triggerDiagramUpload() {
    const choice = confirm("Cliquez sur 'OK' pour insérer une image par lien URL, ou 'Annuler' pour téléverser un fichier depuis votre ordinateur.");
    if (choice) {
        const url = prompt("Entrez l'URL de l'image officielle ou du schéma technique :", "https://");
        if (url && url !== "https://") {
            insertDiagramElement(url);
        }
    } else {
        document.getElementById('studioImageInput').click();
    }
}

function handleStudioImageUpload(e) {
    const file = e.target.files[0];
    if (!file) return;

    const formData = new FormData();
    formData.append('image', file);
    formData.append('_token', '{{ csrf_token() }}');

    const statusEl = document.getElementById('studioStatus');
    statusEl.innerHTML = '<i class="fas fa-spinner fa-spin text-primary me-1"></i> Téléversement de l\'image en cours...';

    // Post to editor image upload endpoint
    const uploadUrl = '{{ Route::has("products.upload-editor-image") ? route("products.upload-editor-image") : (Route::has("media.store") ? route("media.store") : url("/products/upload-editor-image")) }}';
    fetch(uploadUrl, {
        method: 'POST',
        body: formData,
        headers: { 'Accept': 'application/json' }
    })
    .then(res => res.json())
    .then(data => {
        const url = data.url || data.path || ('/storage/' + data.file);
        insertDiagramElement(url);
        statusEl.innerHTML = '<i class="fas fa-check-circle text-success me-1"></i> Image insérée avec succès !';
    })
    .catch(() => {
        // Fallback local FileReader preview
        const reader = new FileReader();
        reader.onload = function(evt) {
            insertDiagramElement(evt.target.result);
            statusEl.innerHTML = '<i class="fas fa-check-circle text-success me-1"></i> Image locale insérée.';
        };
        reader.readAsDataURL(file);
    });
    
    e.target.value = '';
}

function insertDiagramElement(url) {
    const diagramWrapper = document.createElement('div');
    diagramWrapper.className = 'pdp-diagram-wrapper my-4';
    diagramWrapper.innerHTML = `<img src="${url}" class="pdp-diagram-img img-fluid rounded-3 shadow-sm" alt="Schéma technique">`;
    
    // Insert into body or append
    const bodyText = visualEditor.querySelector('.pdp-body-text') || visualEditor;
    bodyText.appendChild(diagramWrapper);
    attachInteractiveListeners();
    syncContentFromVisual();
    diagramWrapper.scrollIntoView({ behavior: 'smooth', block: 'center' });
}

// 3. Add Compatibility Block
function insertCompatibilityBlock() {
    const title = prompt("Titre de compatibilité :", "Compatibilité Officielle & Écosystème Recommandé :");
    if (!title) return;
    const items = prompt("Liste des matériels compatibles séparés par des virgules (ex: Sony E-Mount, Cage SmallRig, DJI RS3) :", "Sony E-Mount, Cages SmallRig, DJI RS 3 / RS 4");
    if (!items) return;

    const tags = items.split(',').map(s => s.trim()).filter(s => s.length > 0);
    const tagsHtml = tags.map(t => `<span class="pdp-compat-tag"><i class="fas fa-check me-1"></i>${escapeHtml(t)}</span>`).join(' ');

    const card = document.createElement('div');
    card.className = 'pdp-compatibility-card';
    card.innerHTML = `
        <div class="fw-bold text-dark mb-2"><i class="fas fa-link text-success me-2"></i>${escapeHtml(title)}</div>
        ${tagsHtml}
    `;

    const bodyText = visualEditor.querySelector('.pdp-body-text') || visualEditor;
    bodyText.appendChild(card);
    syncContentFromVisual();
    card.scrollIntoView({ behavior: 'smooth', block: 'center' });
}

// 4. Add Showcase Card / Modal
let currentReplaceTargetImg = null;
let showcasePendingFile = null;

function openShowcaseModal() {
    const modal = document.getElementById('modalShowcaseInsert');
    if (!modal) return;
    
    // Reset inputs
    clearShowcasePreview();
    const captionInput = document.getElementById('modalShowcaseCaptionInput');
    if (captionInput) captionInput.value = '';
    const urlInput = document.getElementById('modalShowcaseUrlInput');
    if (urlInput) urlInput.value = '';
    const fileInput = document.getElementById('modalShowcaseFileInput');
    if (fileInput) fileInput.value = '';
    
    // Default to 25%
    setShowcaseModalSizeRadio('25%');
    switchShowcaseModalTab('upload');
    
    modal.style.display = 'flex';
}

function closeShowcaseModal() {
    const modal = document.getElementById('modalShowcaseInsert');
    if (modal) modal.style.display = 'none';
    clearShowcasePreview();
}

function switchShowcaseModalTab(tab) {
    const btnUpload = document.getElementById('btnTabShowcaseUpload');
    const btnUrl = document.getElementById('btnTabShowcaseUrl');
    const secUpload = document.getElementById('sectionShowcaseUpload');
    const secUrl = document.getElementById('sectionShowcaseUrl');

    if (tab === 'upload') {
        if (btnUpload) btnUpload.className = 'btn btn-sm flex-fill fw-semibold btn-primary';
        if (btnUrl) btnUrl.className = 'btn btn-sm flex-fill fw-semibold btn-light text-secondary';
        if (secUpload) secUpload.style.display = 'block';
        if (secUrl) secUrl.style.display = 'none';
    } else {
        if (btnUpload) btnUpload.className = 'btn btn-sm flex-fill fw-semibold btn-light text-secondary';
        if (btnUrl) btnUrl.className = 'btn btn-sm flex-fill fw-semibold btn-primary';
        if (secUpload) secUpload.style.display = 'none';
        if (secUrl) secUrl.style.display = 'block';
        const urlInput = document.getElementById('modalShowcaseUrlInput');
        if (urlInput) urlInput.focus();
    }
}

function previewShowcaseFile(event) {
    const file = event.target.files && event.target.files[0];
    if (!file) return;
    showcasePendingFile = file;

    const reader = new FileReader();
    reader.onload = function(e) {
        showPreviewImage(e.target.result);
    };
    reader.readAsDataURL(file);
}

function previewShowcaseUrl(url) {
    showcasePendingFile = null;
    if (url && url.trim().length > 5) {
        showPreviewImage(url.trim());
    } else {
        clearShowcasePreview();
    }
}

function showPreviewImage(src) {
    const box = document.getElementById('showcasePreviewBox');
    const img = document.getElementById('showcasePreviewImg');
    if (box && img) {
        img.src = src;
        box.style.display = 'block';
    }
}

function clearShowcasePreview() {
    showcasePendingFile = null;
    const box = document.getElementById('showcasePreviewBox');
    const img = document.getElementById('showcasePreviewImg');
    const fileInput = document.getElementById('modalShowcaseFileInput');
    if (box) box.style.display = 'none';
    if (img) img.src = '';
    if (fileInput) fileInput.value = '';
}

function setShowcaseModalSizeRadio(size) {
    document.querySelectorAll('#showcaseSizeOptions .showcase-size-tile').forEach(tile => {
        const isMatch = tile.getAttribute('data-size') === size;
        tile.classList.toggle('active', isMatch);
        const radio = tile.querySelector('input[type="radio"]');
        if (radio) radio.checked = isMatch;
    });
}

function getSelectedShowcaseModalSize() {
    const checked = document.querySelector('input[name="showcaseCardSize"]:checked');
    return checked ? checked.value : '25%';
}

function submitShowcaseModal() {
    const captionInput = document.getElementById('modalShowcaseCaptionInput');
    const caption = captionInput ? captionInput.value.trim() : '';
    const size = getSelectedShowcaseModalSize();
    const btnSubmit = document.getElementById('btnConfirmInsertShowcase');

    // Case 1: Pending File to upload
    if (showcasePendingFile) {
        const originalHtml = btnSubmit ? btnSubmit.innerHTML : '';
        if (btnSubmit) {
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Téléversement...';
        }

        const formData = new FormData();
        formData.append('image', showcasePendingFile);
        formData.append('_token', '{{ csrf_token() }}');

        const uploadUrl = '{{ Route::has("products.upload-editor-image") ? route("products.upload-editor-image") : (Route::has("media.store") ? route("media.store") : url("/products/upload-editor-image")) }}';
        fetch(uploadUrl, {
            method: 'POST',
            body: formData,
            headers: { 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(data => {
            const url = data.url || data.path || ('/storage/' + data.file);
            insertShowcaseCardElement(url, caption, size);
            closeShowcaseModal();
        })
        .catch(() => {
            // Fallback: FileReader data URL
            const reader = new FileReader();
            reader.onload = function(evt) {
                insertShowcaseCardElement(evt.target.result, caption, size);
                closeShowcaseModal();
            };
            reader.readAsDataURL(showcasePendingFile);
        })
        .finally(() => {
            if (btnSubmit) {
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = originalHtml;
            }
        });
        return;
    }

    // Case 2: Direct URL
    const urlInput = document.getElementById('modalShowcaseUrlInput');
    const url = urlInput ? urlInput.value.trim() : '';
    if (url) {
        insertShowcaseCardElement(url, caption, size);
        closeShowcaseModal();
        return;
    }

    alert("Veuillez sélectionner une image sur votre ordinateur ou renseigner une URL valide.");
}

function insertShowcaseBlock() {
    openShowcaseModal();
}

function insertShowcaseCardElement(url, caption, size = '25%') {
    if (!url) return;

    let showcase = visualEditor.querySelector('.pdp-visual-showcase');
    if (!showcase) {
        showcase = document.createElement('div');
        showcase.className = 'pdp-visual-showcase mb-4';
        showcase.innerHTML = `
            <h3 class="pdp-enrich-heading">
                <i class="fas fa-camera-retro me-2 text-danger"></i>Aperçu Visuel & Détails Officiels
            </h3>
            <div class="pdp-showcase-grid"></div>
        `;
        visualEditor.appendChild(showcase);
    }

    const grid = showcase.querySelector('.pdp-showcase-grid');
    const card = document.createElement('div');
    card.className = 'pdp-showcase-card';
    card.setAttribute('data-size', size);

    // Grid column span according to 12-column grid
    let span = 'span 3';
    let minH = '180px';
    if (size === '50%') { span = 'span 6'; minH = '240px'; }
    else if (size === '75%') { span = 'span 9'; minH = '280px'; }
    else if (size === '100%') { span = 'span 12'; minH = '320px'; }

    card.style.gridColumn = span;

    const safeCaption = caption || 'Détail produit';
    card.innerHTML = `
        <div class="pdp-showcase-img-wrap" style="min-height: ${minH};">
            <img src="${url}" alt="${escapeHtml(safeCaption)}">
        </div>
        <div class="pdp-showcase-caption" contenteditable="true">${escapeHtml(safeCaption)}</div>
    `;

    grid.appendChild(card);
    attachInteractiveListeners();
    syncContentFromVisual();
    card.scrollIntoView({ behavior: 'smooth', block: 'center' });
}

// Global modal tile listeners
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('#showcaseSizeOptions .showcase-size-tile').forEach(tile => {
        tile.addEventListener('click', function() {
            const size = this.getAttribute('data-size');
            setShowcaseModalSizeRadio(size);
        });
    });

    const dropzone = document.getElementById('showcaseDropzone');
    if (dropzone) {
        ['dragenter', 'dragover'].forEach(eventName => {
            dropzone.addEventListener(eventName, function(e) {
                e.preventDefault();
                e.stopPropagation();
                dropzone.style.borderColor = '#dc2626';
                dropzone.style.background = '#fef2f2';
            }, false);
        });
        ['dragleave', 'drop'].forEach(eventName => {
            dropzone.addEventListener(eventName, function(e) {
                e.preventDefault();
                e.stopPropagation();
                dropzone.style.borderColor = '';
                dropzone.style.background = '';
            }, false);
        });
        dropzone.addEventListener('drop', function(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            if (files && files.length > 0) {
                showcasePendingFile = files[0];
                const reader = new FileReader();
                reader.onload = function(evt) {
                    showPreviewImage(evt.target.result);
                };
                reader.readAsDataURL(files[0]);
            }
        }, false);
    }
});

/* ==========================================================================
   INTERACTIVE SPEC REMOVE BUTTONS & IMAGE HANDLERS
   ========================================================================== */
function attachInteractiveListeners() {
    // 1. Spec Cards: add quick remove button
    visualEditor.querySelectorAll('.pdp-spec-card').forEach(card => {
        if (!card.querySelector('.spec-remove-btn')) {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'spec-remove-btn';
            btn.innerHTML = '&times;';
            btn.title = 'Supprimer cette spécification';
            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                if (confirm('Supprimer cette spécification ?')) {
                    const gridParent = card.closest('.pdp-specs-grid');
                    const specsContainer = card.closest('.pdp-technical-specs');
                    card.remove();
                    if (specsContainer && gridParent && !gridParent.querySelector('.pdp-spec-card')) {
                        specsContainer.remove();
                    }
                    syncContentFromVisual();
                }
            });
            card.appendChild(btn);
        }
    });

    // 2. Showcase Cards: add quick remove button, upload button, and size indicator on each card
    visualEditor.querySelectorAll('.pdp-showcase-card').forEach(card => {
        // Remove button
        if (!card.querySelector('.showcase-remove-btn')) {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'showcase-remove-btn';
            btn.innerHTML = '&times;';
            btn.title = 'Supprimer cette photo du showcase';
            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                if (confirm('Supprimer cette image du showcase ?')) {
                    const showcaseParent = card.closest('.pdp-visual-showcase');
                    card.remove();
                    if (showcaseParent && !showcaseParent.querySelector('.pdp-showcase-card')) {
                        showcaseParent.remove();
                    }
                    syncContentFromVisual();
                }
            });
            card.appendChild(btn);
        }

        // Upload / Replace button on card
        if (!card.querySelector('.showcase-upload-btn')) {
            const uploadBtn = document.createElement('button');
            uploadBtn.type = 'button';
            uploadBtn.className = 'showcase-upload-btn';
            uploadBtn.innerHTML = '<i class="fas fa-camera"></i>';
            uploadBtn.title = 'Remplacer / Téléverser une photo pour cette carte';
            uploadBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                const img = card.querySelector('img');
                if (img) selectImage(img);
                triggerReplaceImage();
            });
            card.appendChild(uploadBtn);
        }

        // Size indicator badge on card
        const currentSize = card.getAttribute('data-size') || '25%';
        let sizeBadge = card.querySelector('.showcase-size-indicator');
        if (!sizeBadge) {
            sizeBadge = document.createElement('span');
            sizeBadge.className = 'showcase-size-indicator';
            sizeBadge.title = 'Cliquer pour basculer la largeur (25% / 50% / 75% / 100%)';
            sizeBadge.addEventListener('click', function (e) {
                e.stopPropagation();
                const img = card.querySelector('img');
                if (img) selectImage(img);
                const sizes = ['25%', '50%', '75%', '100%'];
                const cur = card.getAttribute('data-size') || '25%';
                const next = sizes[(sizes.indexOf(cur) + 1) % sizes.length];
                setShowcaseCardSize(next, card);
            });
            card.appendChild(sizeBadge);
        }
        sizeBadge.textContent = currentSize;

        // Editable caption
        const caption = card.querySelector('.pdp-showcase-caption');
        if (caption) {
            caption.setAttribute('contenteditable', 'true');
            if (!caption.dataset.hasListener) {
                caption.dataset.hasListener = 'true';
                caption.addEventListener('input', function () {
                    syncContentFromVisual();
                });
            }
        }
    });

    // 3. Entire Showcase Gallery: add top section remove button
    visualEditor.querySelectorAll('.pdp-visual-showcase').forEach(showcase => {
        if (!showcase.querySelector('.showcase-section-remove-btn')) {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'showcase-section-remove-btn';
            btn.innerHTML = '<i class="fas fa-trash-alt me-1"></i> Supprimer la galerie Showcase';
            btn.title = 'Supprimer toute la section Showcase et ses photos';
            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                if (confirm('Supprimer toute la galerie Showcase et toutes ses photos ?')) {
                    showcase.remove();
                    syncContentFromVisual();
                }
            });
            showcase.insertBefore(btn, showcase.firstChild);
        }
    });

    // 4. Update toolbar "Supprimer Showcase" button visibility
    const btnDelShowcase = document.getElementById('btnDeleteShowcase');
    if (btnDelShowcase) {
        const hasShowcase = !!visualEditor.querySelector('.pdp-visual-showcase, .pdp-showcase-card');
        btnDelShowcase.style.display = hasShowcase ? 'inline-flex' : 'none';
    }

    // 5. Images: Click to select and float toolbar
    visualEditor.querySelectorAll('img').forEach(img => {
        if (!img.dataset.hasListener) {
            img.dataset.hasListener = 'true';
            img.addEventListener('click', function (e) {
                e.stopPropagation();
                selectImage(img);
            });
        }
    });
}

function removeAllShowcases() {
    if (confirm("Supprimer toute la galerie Showcase et toutes ses photos de la fiche produit ?")) {
        visualEditor.querySelectorAll('.pdp-visual-showcase, .pdp-showcase-card').forEach(el => el.remove());
        deselectImage();
        syncContentFromVisual();
        attachInteractiveListeners();
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Showcase supprimé',
                showConfirmButton: false,
                timer: 2000
            });
        }
    }
}

function selectImage(img) {
    if (currentSelectedImg) {
        currentSelectedImg.classList.remove('img-selected');
    }
    currentSelectedImg = img;
    img.classList.add('img-selected');

    const showcaseCard = img.closest('.pdp-showcase-card');
    if (showcaseCard) {
        const currentSize = showcaseCard.getAttribute('data-size') || '25%';
        imgFloatBar.innerHTML = `
            <span class="smart-img-float-label"><i class="fas fa-camera text-warning me-1"></i> Showcase</span>
            <button type="button" class="smart-img-float-btn ${currentSize === '25%' ? 'active' : ''}" onclick="setShowcaseCardSize('25%')" title="Largeur 25% (1 col)">25%</button>
            <button type="button" class="smart-img-float-btn ${currentSize === '50%' ? 'active' : ''}" onclick="setShowcaseCardSize('50%')" title="Largeur 50% (2 col)">50%</button>
            <button type="button" class="smart-img-float-btn ${currentSize === '75%' ? 'active' : ''}" onclick="setShowcaseCardSize('75%')" title="Largeur 75% (3 col)">75%</button>
            <button type="button" class="smart-img-float-btn ${currentSize === '100%' ? 'active' : ''}" onclick="setShowcaseCardSize('100%')" title="Largeur 100% (pleine largeur)">100%</button>
            <span class="text-muted mx-1" style="opacity: 0.3;">|</span>
            <button type="button" class="smart-img-float-btn" onclick="setShowcaseHeight('180px')" title="Hauteur compacte (180px)">H:180</button>
            <button type="button" class="smart-img-float-btn" onclick="setShowcaseHeight('260px')" title="Hauteur standard (260px)">H:260</button>
            <button type="button" class="smart-img-float-btn" onclick="setShowcaseHeight('360px')" title="Hauteur haute (360px)">H:360</button>
            <span class="text-muted mx-1" style="opacity: 0.3;">|</span>
            <button type="button" class="smart-img-float-btn" onclick="triggerReplaceImage()" title="Téléverser une nouvelle photo pour cette carte"><i class="fas fa-upload me-1"></i> Changer photo</button>
            <button type="button" class="smart-img-float-btn" onclick="editShowcaseCaption()" title="Modifier la légende"><i class="fas fa-edit me-1"></i> Légende</button>
            <button type="button" class="smart-img-float-btn delete" onclick="deleteSelectedImg()" title="Supprimer la carte"><i class="fas fa-trash-alt"></i></button>
        `;
    } else {
        imgFloatBar.innerHTML = `
            <button type="button" class="smart-img-float-btn" onclick="setImgWidth('25%')">25%</button>
            <button type="button" class="smart-img-float-btn" onclick="setImgWidth('50%')">50%</button>
            <button type="button" class="smart-img-float-btn" onclick="setImgWidth('75%')">75%</button>
            <button type="button" class="smart-img-float-btn" onclick="setImgWidth('100%')">100%</button>
            <span class="text-muted mx-1" style="opacity: 0.3;">|</span>
            <button type="button" class="smart-img-float-btn" onclick="setImgAlign('left')" title="Aligner à gauche"><i class="fas fa-align-left"></i></button>
            <button type="button" class="smart-img-float-btn" onclick="setImgAlign('center')" title="Centrer"><i class="fas fa-align-center"></i></button>
            <button type="button" class="smart-img-float-btn" onclick="setImgAlign('right')" title="Aligner à droite"><i class="fas fa-align-right"></i></button>
            <span class="text-muted mx-1" style="opacity: 0.3;">|</span>
            <button type="button" class="smart-img-float-btn" onclick="triggerReplaceImage()" title="Téléverser une image de remplacement"><i class="fas fa-sync-alt me-1"></i> Remplacer</button>
            <button type="button" class="smart-img-float-btn delete" onclick="deleteSelectedImg()" title="Supprimer"><i class="fas fa-trash-alt"></i></button>
        `;
    }

    updateFloatBarPosition();
    imgFloatBar.style.display = 'flex';
}

function setShowcaseCardSize(size, targetCard = null) {
    const card = targetCard || (currentSelectedImg ? currentSelectedImg.closest('.pdp-showcase-card') : null);
    if (!card) return;

    card.setAttribute('data-size', size);
    let span = 'span 3';
    if (size === '50%') span = 'span 6';
    else if (size === '75%') span = 'span 9';
    else if (size === '100%') span = 'span 12';

    card.style.gridColumn = span;

    const badge = card.querySelector('.showcase-size-indicator');
    if (badge) badge.textContent = size;

    // Update active state in float bar buttons
    imgFloatBar.querySelectorAll('.smart-img-float-btn').forEach(btn => {
        if (['25%', '50%', '75%', '100%'].includes(btn.innerText.trim())) {
            btn.classList.toggle('active', btn.innerText.trim() === size);
        }
    });

    syncContentFromVisual();
    updateFloatBarPosition();
}

function setShowcaseHeight(height, targetCard = null) {
    const card = targetCard || (currentSelectedImg ? currentSelectedImg.closest('.pdp-showcase-card') : null);
    if (!card) return;

    const wrap = card.querySelector('.pdp-showcase-img-wrap');
    if (wrap) {
        wrap.style.height = height;
        wrap.style.minHeight = height;
    }

    syncContentFromVisual();
    updateFloatBarPosition();
}

function editShowcaseCaption() {
    if (!currentSelectedImg) return;
    const card = currentSelectedImg.closest('.pdp-showcase-card');
    if (!card) return;
    const captionEl = card.querySelector('.pdp-showcase-caption');
    const oldCaption = captionEl ? captionEl.innerText.trim() : '';
    const newCaption = prompt("Modifier la légende de l'image Showcase :", oldCaption);
    if (newCaption !== null) {
        if (captionEl) {
            captionEl.innerText = newCaption;
        } else {
            const newCapEl = document.createElement('div');
            newCapEl.className = 'pdp-showcase-caption';
            newCapEl.contentEditable = 'true';
            newCapEl.innerText = newCaption;
            card.appendChild(newCapEl);
        }
        syncContentFromVisual();
        attachInteractiveListeners();
    }
}

function triggerReplaceImage() {
    if (!currentSelectedImg) return;
    currentReplaceTargetImg = currentSelectedImg;
    const fileInput = document.getElementById('replaceImageFileInput');
    if (fileInput) {
        fileInput.value = '';
        fileInput.click();
    }
}

function handleReplaceImageFileInput(event) {
    const file = event.target.files && event.target.files[0];
    if (!file || !currentReplaceTargetImg) return;

    const formData = new FormData();
    formData.append('image', file);
    formData.append('_token', '{{ csrf_token() }}');

    const statusEl = document.getElementById('studioStatus');
    if (statusEl) {
        statusEl.innerHTML = '<i class="fas fa-spinner fa-spin text-primary me-1"></i> Téléversement de la nouvelle photo...';
    }

    const uploadUrl = '{{ Route::has("products.upload-editor-image") ? route("products.upload-editor-image") : (Route::has("media.store") ? route("media.store") : url("/products/upload-editor-image")) }}';
    fetch(uploadUrl, {
        method: 'POST',
        body: formData,
        headers: { 'Accept': 'application/json' }
    })
    .then(res => res.json())
    .then(data => {
        const url = data.url || data.path || ('/storage/' + data.file);
        if (currentReplaceTargetImg) {
            currentReplaceTargetImg.src = url;
            syncContentFromVisual();
            updateFloatBarPosition();
        }
        if (statusEl) {
            statusEl.innerHTML = '<i class="fas fa-check-circle text-success me-1"></i> Photo mise à jour avec succès !';
        }
    })
    .catch(() => {
        // Fallback local FileReader
        const reader = new FileReader();
        reader.onload = function(evt) {
            if (currentReplaceTargetImg) {
                currentReplaceTargetImg.src = evt.target.result;
                syncContentFromVisual();
                updateFloatBarPosition();
            }
            if (statusEl) {
                statusEl.innerHTML = '<i class="fas fa-check-circle text-success me-1"></i> Photo locale chargée.';
            }
        };
        reader.readAsDataURL(file);
    });

    event.target.value = '';
}

function deselectImage() {
    if (currentSelectedImg) {
        currentSelectedImg.classList.remove('img-selected');
        currentSelectedImg = null;
    }
    imgFloatBar.style.display = 'none';
}

function updateFloatBarPosition() {
    if (!currentSelectedImg || !currentSelectedImg.isConnected) {
        deselectImage();
        return;
    }

    const containerRect = document.getElementById('smartStudioWrapper').getBoundingClientRect();
    const imgRect = currentSelectedImg.getBoundingClientRect();

    const top = imgRect.top - containerRect.top - 42;
    const left = Math.max(10, (imgRect.left - containerRect.left) + (imgRect.width / 2) - 130);

    imgFloatBar.style.top = Math.max(10, top) + 'px';
    imgFloatBar.style.left = left + 'px';
}

function setImgWidth(width) {
    if (!currentSelectedImg) return;
    const showcaseCard = currentSelectedImg.closest('.pdp-showcase-card');
    if (showcaseCard) {
        setShowcaseCardSize(width, showcaseCard);
        return;
    }
    currentSelectedImg.style.width = width;
    currentSelectedImg.style.height = 'auto';
    syncContentFromVisual();
    updateFloatBarPosition();
}

function setImgAlign(align) {
    if (!currentSelectedImg) return;
    if (align === 'left') {
        currentSelectedImg.style.float = 'left';
        currentSelectedImg.style.margin = '8px 18px 14px 0';
        currentSelectedImg.style.display = 'inline-block';
    } else if (align === 'center') {
        currentSelectedImg.style.float = 'none';
        currentSelectedImg.style.margin = '16px auto';
        currentSelectedImg.style.display = 'block';
    } else if (align === 'right') {
        currentSelectedImg.style.float = 'right';
        currentSelectedImg.style.margin = '8px 0 14px 18px';
        currentSelectedImg.style.display = 'inline-block';
    }
    syncContentFromVisual();
    updateFloatBarPosition();
}

function deleteSelectedImg() {
    if (!currentSelectedImg) return;
    if (confirm("Supprimer cette image de la description ?")) {
        const showcaseCard = currentSelectedImg.closest('.pdp-showcase-card');
        const showcaseParent = currentSelectedImg.closest('.pdp-visual-showcase');
        const diagramWrapper = currentSelectedImg.closest('.pdp-diagram-wrapper');

        if (showcaseCard) {
            showcaseCard.remove();
            if (showcaseParent && !showcaseParent.querySelector('.pdp-showcase-card')) {
                showcaseParent.remove();
            }
        } else if (diagramWrapper) {
            diagramWrapper.remove();
        } else {
            currentSelectedImg.remove();
        }

        deselectImage();
        syncContentFromVisual();
        attachInteractiveListeners();
    }
}

/* ==========================================================================
   HELPERS
   ========================================================================== */
function escapeHtml(text) {
    const div = document.createElement('div');
    div.innerText = text;
    return div.innerHTML;
}

function formatHtmlCode(html) {
    if (!html) return '';
    let tab = '  ';
    let result = '';
    let indent = 0;

    html.split(/>\s*</).forEach(function(element) {
        element = element.trim();
        if (!element) return;

        if (element.startsWith('/')) {
            indent = Math.max(0, indent - 1);
        }
        result += tab.repeat(indent) + '<' + element + '>\n';
        const isSelfClosing = element.endsWith('/') || 
                              element.startsWith('img') || 
                              element.startsWith('br') || 
                              element.startsWith('hr') || 
                              element.startsWith('input') || 
                              element.startsWith('meta');
        if (!element.startsWith('/') && !element.startsWith('!') && !isSelfClosing) {
            indent++;
        }
    });

    return result.trim();
}
</script>
@endpush
