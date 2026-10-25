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
    overflow-y: auto;
    overflow-x: hidden;
    position: relative;
    contain: layout style;
    padding: 24px 28px;
    outline: none;
    background: #ffffff;
    color: #1e293b;
    font-family: inherit;
    line-height: 1.7;
    font-size: 14.5px;
}

.smart-visual-editor .w-screen,
.smart-visual-editor [class*="w-screen"] {
    width: 100% !important;
    max-width: 100% !important;
}
.smart-visual-editor .h-svh,
.smart-visual-editor [class*="h-svh"] {
    height: auto !important;
    min-height: 0 !important;
}

.smart-visual-editor:focus {
    outline: none;
}

/* 1. Lead Intro Banner */
.smart-visual-editor .pdp-enrich-lead {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border-left: 4px solid #dc2626;
    border-radius: 12px;
    padding: 16px 20px;
    margin-bottom: 24px;
    box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
}

.smart-visual-editor .pdp-enrich-lead p {
    font-size: 1.05rem;
    line-height: 1.65;
    color: #1e293b;
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
    padding-top: 20px;
    border-top: 1px solid #f1f5f9;
}

.smart-visual-editor .pdp-showcase-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(210px, 1fr));
    gap: 16px;
    margin-top: 14px;
}

.smart-visual-editor .pdp-showcase-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    box-shadow: 0 2px 8px rgba(15, 23, 42, 0.03);
}

.smart-visual-editor .pdp-showcase-img-wrap {
    width: 100%;
    height: 180px;
    background: #f8fafc;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    padding: 10px;
}

.smart-visual-editor .pdp-showcase-img-wrap img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
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

.smart-img-float-btn.btn-del {
    color: #f87171;
}

.smart-img-float-btn.btn-del:hover {
    background: #ef4444;
    color: #ffffff;
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
            </div>
        </div>

        {{-- Visual Formatting Toolbar --}}
        <div class="smart-format-toolbar" id="smartFormatToolbar">
            <select class="smart-format-select" onchange="applyFormatBlock(this.value)">
                <option value="p">Paragraphe</option>
                <option value="h3">Titre Section (H3)</option>
                <option value="h4">Sous-Titre (H4)</option>
                <option value="blockquote">Citation / Note</option>
            </select>

            <div class="smart-format-divider"></div>

            <button type="button" class="smart-format-btn" onclick="execCmd('bold')" title="Gras (Ctrl+B)">
                <i class="fas fa-bold"></i>
            </button>
            <button type="button" class="smart-format-btn" onclick="execCmd('italic')" title="Italique (Ctrl+I)">
                <i class="fas fa-italic"></i>
            </button>
            <button type="button" class="smart-format-btn" onclick="execCmd('underline')" title="Souligné (Ctrl+U)">
                <i class="fas fa-underline"></i>
            </button>
            <button type="button" class="smart-format-btn" onclick="execCmd('strikeThrough')" title="Barré">
                <i class="fas fa-strikethrough"></i>
            </button>

            <div class="smart-format-divider"></div>

            <button type="button" class="smart-format-btn" onclick="execCmd('insertUnorderedList')" title="Liste à puces">
                <i class="fas fa-list-ul"></i>
            </button>
            <button type="button" class="smart-format-btn" onclick="execCmd('insertOrderedList')" title="Liste numérotée">
                <i class="fas fa-list-ol"></i>
            </button>

            <div class="smart-format-divider"></div>

            <button type="button" class="smart-format-btn" onclick="execCmd('justifyLeft')" title="Aligner à gauche">
                <i class="fas fa-align-left"></i>
            </button>
            <button type="button" class="smart-format-btn" onclick="execCmd('justifyCenter')" title="Centrer">
                <i class="fas fa-align-center"></i>
            </button>
            <button type="button" class="smart-format-btn" onclick="execCmd('justifyRight')" title="Aligner à droite">
                <i class="fas fa-align-right"></i>
            </button>

            <div class="smart-format-divider"></div>

            <button type="button" class="smart-format-btn" onclick="promptInsertLink()" title="Insérer un lien">
                <i class="fas fa-link"></i>
            </button>
            <button type="button" class="smart-format-btn" onclick="execCmd('unlink')" title="Supprimer le lien">
                <i class="fas fa-unlink"></i>
            </button>
            <button type="button" class="smart-format-btn" onclick="execCmd('removeFormat')" title="Effacer le style">
                <i class="fas fa-eraser"></i>
            </button>

            <div class="smart-format-divider"></div>

            <button type="button" class="smart-format-btn" onclick="execCmd('undo')" title="Annuler (Ctrl+Z)">
                <i class="fas fa-undo"></i>
            </button>
            <button type="button" class="smart-format-btn" onclick="execCmd('redo')" title="Rétablir (Ctrl+Y)">
                <i class="fas fa-redo"></i>
            </button>
        </div>

        {{-- Hidden Image Uploader Input --}}
        <input type="file" id="studioImageInput" accept="image/*" style="display: none;" onchange="handleStudioImageUpload(event)">

        {{-- 1. Main Visual ContentEditable Studio (Loaded safely via JS from escaped textarea) --}}
        <div id="smartVisualEditor" class="smart-visual-editor" contenteditable="true" spellcheck="false"></div>

        {{-- 2. Raw HTML Code Mode --}}
        <textarea id="smartRawHtmlArea" class="smart-raw-textarea" spellcheck="false">{{ $value ?? '' }}</textarea>

        {{-- 3. Live 1:1 Preview Mode --}}
        <div id="smartPreviewContainer" class="smart-preview-container"></div>

        {{-- Floating Image Resizer Bar --}}
        <div id="smartImgFloatBar" class="smart-img-float-bar">
            <button type="button" class="smart-img-float-btn" onclick="setImgWidth('25%')">25%</button>
            <button type="button" class="smart-img-float-btn" onclick="setImgWidth('50%')">50%</button>
            <button type="button" class="smart-img-float-btn" onclick="setImgWidth('75%')">75%</button>
            <button type="button" class="smart-img-float-btn" onclick="setImgWidth('100%')">100%</button>
            <div style="width: 1px; height: 16px; background: rgba(255,255,255,0.2); margin: 0 2px;"></div>
            <button type="button" class="smart-img-float-btn" onclick="setImgAlign('left')" title="Aligner à gauche"><i class="fas fa-align-left"></i></button>
            <button type="button" class="smart-img-float-btn" onclick="setImgAlign('center')" title="Centrer"><i class="fas fa-align-center"></i></button>
            <button type="button" class="smart-img-float-btn" onclick="setImgAlign('right')" title="Aligner à droite"><i class="fas fa-align-right"></i></button>
            <div style="width: 1px; height: 16px; background: rgba(255,255,255,0.2); margin: 0 2px;"></div>
            <button type="button" class="smart-img-float-btn btn-del" onclick="deleteSelectedImg()" title="Supprimer l'image"><i class="fas fa-trash-alt"></i></button>
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

document.addEventListener('DOMContentLoaded', function () {
    // 1. Initial Content Setup (safely parse into visual container)
    visualEditor.innerHTML = hiddenTextarea.value;
    syncContentFromVisual();
    attachInteractiveListeners();

    // 2. Realtime sync on typing/pasting in visual editor
    visualEditor.addEventListener('input', function () {
        syncContentFromVisual();
    });

    // 3. Sync on editing raw HTML
    rawHtmlArea.addEventListener('input', function () {
        hiddenTextarea.value = rawHtmlArea.value;
        visualEditor.innerHTML = rawHtmlArea.value;
        updateWordCount();
        attachInteractiveListeners();
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
function syncContentFromVisual() {
    // Clone node to strip editor temporary UI like remove buttons before storing
    const clone = visualEditor.cloneNode(true);
    clone.querySelectorAll('.spec-remove-btn').forEach(b => b.remove());
    clone.querySelectorAll('.img-selected').forEach(img => img.classList.remove('img-selected'));

    const cleanHtml = clone.innerHTML;
    hiddenTextarea.value = cleanHtml;
    rawHtmlArea.value = cleanHtml;
    updateWordCount();
}

function cleanEditorArtifacts() {
    visualEditor.querySelectorAll('.spec-remove-btn').forEach(b => b.remove());
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
    visualEditor.focus();
    document.execCommand(command, false, value);
    syncContentFromVisual();
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

// 4. Add Showcase Card
function insertShowcaseBlock() {
    const url = prompt("Entrez l'URL de l'image pour la galerie Showcase :", "https://");
    if (!url) return;
    const caption = prompt("Légende de l'image :", "Vue détaillée de face");

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
    card.innerHTML = `
        <div class="pdp-showcase-img-wrap">
            <img src="${url}" alt="${escapeHtml(caption)}">
        </div>
        <div class="pdp-showcase-caption">${escapeHtml(caption)}</div>
    `;

    grid.appendChild(card);
    attachInteractiveListeners();
    syncContentFromVisual();
    card.scrollIntoView({ behavior: 'smooth', block: 'center' });
}

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
                    card.remove();
                    syncContentFromVisual();
                }
            });
            card.appendChild(btn);
        }
    });

    // 2. Images: Click to select and float toolbar
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

function selectImage(img) {
    if (currentSelectedImg) {
        currentSelectedImg.classList.remove('img-selected');
    }
    currentSelectedImg = img;
    img.classList.add('img-selected');
    updateFloatBarPosition();
    imgFloatBar.style.display = 'flex';
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
        const parentWrapper = currentSelectedImg.closest('.pdp-diagram-wrapper') || currentSelectedImg.closest('.pdp-showcase-card');
        if (parentWrapper) {
            parentWrapper.remove();
        } else {
            currentSelectedImg.remove();
        }
        deselectImage();
        syncContentFromVisual();
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
