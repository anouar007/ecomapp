@push('styles')
<!-- Quill 2.0 Editor CSS -->
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
<style>
.smart-editor-container {
    position: relative;
    border: 1px solid #cbd5e1;
    border-radius: 12px;
    background: #ffffff;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    overflow: visible;
    transition: all 0.2s ease;
}

.smart-editor-container:focus-within {
    border-color: #4f46e5;
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.12);
}

.smart-editor-helper-bar {
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    border-radius: 12px 12px 0 0;
    padding: 8px 14px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

.smart-editor-tag {
    font-size: 11.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #475569;
    background: #ffffff;
    padding: 3px 9px;
    border-radius: 6px;
    border: 1px solid #e2e8f0;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

.smart-tool-btn {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    color: #334155;
    font-size: 12.5px;
    font-weight: 600;
    border-radius: 7px;
    padding: 5px 11px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    cursor: pointer;
    transition: all 0.18s ease;
}

.smart-tool-btn:hover {
    background: #f1f5f9;
    border-color: #94a3b8;
    color: #0f172a;
    transform: translateY(-1px);
}

.smart-tool-btn.active {
    background: #0f172a;
    color: #ffffff;
    border-color: #0f172a;
}

/* Quill customization */
.ql-toolbar.ql-snow {
    border: none !important;
    border-bottom: 1px solid #e2e8f0 !important;
    background: #ffffff;
    padding: 9px 14px !important;
}

.ql-container.ql-snow {
    border: none !important;
    font-family: inherit !important;
    border-radius: 0 0 12px 12px;
}

.ql-editor {
    min-height: 280px;
    font-size: 14.5px;
    line-height: 1.7;
    color: #1e293b;
    padding: 18px 22px !important;
}

.ql-editor.ql-blank::before {
    color: #94a3b8;
    font-style: normal;
    left: 22px;
}

.ql-editor img {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
    margin: 10px 0;
    cursor: pointer;
    transition: outline 0.15s ease;
    user-select: none;
}

.ql-editor img:hover {
    outline: 2px dashed #4f46e5;
    outline-offset: 2px;
}

.ql-editor img.smart-dragging {
    opacity: 0.4;
}

/* Image Resize & Movement Overlay */
.smart-image-overlay {
    position: absolute;
    pointer-events: none;
    z-index: 100;
}

.smart-image-box {
    position: absolute;
    inset: 0;
    border: 2px solid #4f46e5;
    border-radius: 6px;
    box-shadow: 0 0 0 1px rgba(255, 255, 255, 0.9), 0 4px 14px rgba(79, 70, 229, 0.25);
    pointer-events: none;
}

.smart-handle {
    position: absolute;
    width: 12px;
    height: 12px;
    background: #ffffff;
    border: 2px solid #4f46e5;
    border-radius: 2px;
    pointer-events: auto;
    z-index: 102;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    transition: transform 0.1s ease, background 0.1s ease;
}

.smart-handle:hover {
    background: #4f46e5;
    transform: scale(1.25);
}

.smart-handle-nw { top: -6px; left: -6px; cursor: nwse-resize; }
.smart-handle-ne { top: -6px; right: -6px; cursor: nesw-resize; }
.smart-handle-se { bottom: -6px; right: -6px; cursor: nwse-resize; }
.smart-handle-sw { bottom: -6px; left: -6px; cursor: nesw-resize; }

.smart-size-badge {
    position: absolute;
    bottom: -28px;
    left: 50%;
    transform: translateX(-50%);
    background: rgba(15, 23, 42, 0.92);
    color: #ffffff;
    font-size: 11px;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 4px;
    pointer-events: none;
    white-space: nowrap;
    display: none;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.25);
    z-index: 104;
}

.smart-image-toolbar {
    position: absolute;
    top: -46px;
    left: 50%;
    transform: translateX(-50%);
    background: #0f172a;
    border-radius: 8px;
    padding: 4px 6px;
    display: flex;
    align-items: center;
    gap: 4px;
    pointer-events: auto;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.45);
    z-index: 103;
    white-space: nowrap;
}

.smart-image-toolbar.toolbar-bottom {
    top: auto;
    bottom: -48px;
}

.smart-toolbar-group {
    display: flex;
    align-items: center;
    gap: 2px;
}

.smart-toolbar-divider {
    width: 1px;
    height: 18px;
    background: rgba(255, 255, 255, 0.2);
    margin: 0 3px;
}

.smart-btn-icon,
.smart-btn-text {
    background: transparent;
    border: none;
    color: #cbd5e1;
    border-radius: 5px;
    padding: 5px 8px;
    font-size: 12px;
    cursor: pointer;
    transition: all 0.15s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    line-height: 1;
}

.smart-btn-text {
    font-size: 11px;
    font-weight: 700;
    padding: 4px 7px;
}

.smart-btn-icon:hover,
.smart-btn-text:hover {
    background: rgba(255, 255, 255, 0.2);
    color: #ffffff;
}

.smart-btn-icon.active {
    background: #4f46e5;
    color: #ffffff;
}
</style>
@endpush

<div class="form-group mb-4">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <label for="description" class="form-label mb-0 fw-bold">
            <i class="fas fa-file-signature text-primary me-1"></i> Description du Produit (Smart Rich Editor)
        </label>
        <span class="badge bg-light text-muted border" style="font-weight: 500;">
            <i class="fas fa-expand-arrows-alt text-primary me-1"></i> Cliquez sur l'image pour redimensionner & déplacer
        </span>
    </div>

    <div class="smart-editor-container">
        {{-- Helper Bar with Image and Creative Layout tools --}}
        <div class="smart-editor-helper-bar">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="smart-editor-tag">
                    <i class="fas fa-magic text-warning"></i> Outils Intelligents
                </span>

                <button type="button" class="smart-tool-btn" onclick="triggerSmartImageUpload()" title="Téléverser et insérer une image locale">
                    <i class="fas fa-cloud-upload-alt text-primary"></i> <span>Ajouter Image</span>
                </button>

                <button type="button" class="smart-tool-btn" onclick="insertImageFromUrl()" title="Insérer une image externe par lien URL">
                    <i class="fas fa-link text-info"></i> <span>Image par URL</span>
                </button>

                <div class="dropdown d-inline-block">
                    <button type="button" class="smart-tool-btn dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-layer-group text-danger"></i> <span>Insérer un Modèle</span>
                    </button>
                    <ul class="dropdown-menu shadow-sm border-0" style="font-size: 13px; border-radius: 10px; z-index: 1050;">
                        <li>
                            <a class="dropdown-item py-2" href="javascript:void(0)" onclick="insertSmartTemplate('highlights')">
                                <i class="fas fa-star text-warning me-2"></i> <strong>Points Forts & Atouts</strong>
                                <div class="small text-muted ps-4">Encadré moderne avec coche verte</div>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-2" href="javascript:void(0)" onclick="insertSmartTemplate('specs')">
                                <i class="fas fa-table text-primary me-2"></i> <strong>Tableau de Spécifications</strong>
                                <div class="small text-muted ps-4">Tableau 2 colonnes propre et stylé</div>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-2" href="javascript:void(0)" onclick="insertSmartTemplate('box')">
                                <i class="fas fa-box-open text-success me-2"></i> <strong>Contenu de la Boîte</strong>
                                <div class="small text-muted ps-4">Liste d'accessoires inclus dans le pack</div>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-2" href="javascript:void(0)" onclick="insertSmartTemplate('warranty')">
                                <i class="fas fa-shield-alt text-info me-2"></i> <strong>Garantie & Authenticité</strong>
                                <div class="small text-muted ps-4">Badge officiel et réassurance client</div>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">
                <button type="button" class="smart-tool-btn" id="toggleHtmlModeBtn" onclick="toggleHtmlMode()" title="Basculer en mode code HTML brut">
                    <i class="fas fa-code"></i> <span id="toggleHtmlModeText">Code HTML</span>
                </button>
            </div>
        </div>

        {{-- Hidden Image File Input --}}
        <input type="file" id="smartEditorImageInput" accept="image/*" style="display: none;" onchange="handleSmartImageSelected(event)">

        {{-- Quill Visual Editor Div --}}
        <div id="smartQuillEditor"></div>

        {{-- Interactive Image Resizer & Movement Overlay --}}
        <div id="smartImageOverlay" class="smart-image-overlay" style="display: none;">
            <div class="smart-image-box"></div>
            <div class="smart-handle smart-handle-nw" data-handle="nw" title="Glisser pour redimensionner"></div>
            <div class="smart-handle smart-handle-ne" data-handle="ne" title="Glisser pour redimensionner"></div>
            <div class="smart-handle smart-handle-se" data-handle="se" title="Glisser pour redimensionner"></div>
            <div class="smart-handle smart-handle-sw" data-handle="sw" title="Glisser pour redimensionner"></div>
            <div class="smart-size-badge" id="smartSizeBadge"></div>

            <div class="smart-image-toolbar" id="smartImageToolbar">
                <div class="smart-toolbar-group">
                    <button type="button" class="smart-btn-icon" data-action="align-left" title="Aligner à gauche (Habillage texte)"><i class="fas fa-align-left"></i></button>
                    <button type="button" class="smart-btn-icon" data-action="align-center" title="Centrer"><i class="fas fa-align-center"></i></button>
                    <button type="button" class="smart-btn-icon" data-action="align-right" title="Aligner à droite (Habillage texte)"><i class="fas fa-align-right"></i></button>
                </div>
                <div class="smart-toolbar-divider"></div>
                <div class="smart-toolbar-group">
                    <button type="button" class="smart-btn-icon" data-action="move-up" title="Déplacer vers le haut"><i class="fas fa-arrow-up"></i></button>
                    <button type="button" class="smart-btn-icon" data-action="move-down" title="Déplacer vers le bas"><i class="fas fa-arrow-down"></i></button>
                </div>
                <div class="smart-toolbar-divider"></div>
                <div class="smart-toolbar-group">
                    <button type="button" class="smart-btn-text" data-action="size-25" title="Largeur 25%">25%</button>
                    <button type="button" class="smart-btn-text" data-action="size-50" title="Largeur 50%">50%</button>
                    <button type="button" class="smart-btn-text" data-action="size-75" title="Largeur 75%">75%</button>
                    <button type="button" class="smart-btn-text" data-action="size-100" title="Pleine largeur 100%">100%</button>
                </div>
                <div class="smart-toolbar-divider"></div>
                <div class="smart-toolbar-group">
                    <button type="button" class="smart-btn-icon text-danger" data-action="delete" title="Supprimer l'image"><i class="fas fa-trash-alt"></i></button>
                </div>
            </div>
        </div>

        {{-- Raw HTML Editor Textarea (Toggled on Code HTML click) --}}
        <textarea id="smartRawHtmlArea" class="form-control font-monospace border-0" style="display: none; min-height: 280px; font-size: 13px; line-height: 1.6; background: #0f172a; color: #f8fafc; border-radius: 0 0 12px 12px; padding: 18px 22px;" rows="12"></textarea>

        {{-- Hidden Form Input that posts the description to backend --}}
        <textarea id="description" name="description" style="display: none;">{{ $value ?? '' }}</textarea>
    </div>

    {{-- Bottom helper info --}}
    <div class="d-flex justify-content-between align-items-center mt-2 px-1">
        <span class="text-muted small" style="font-size: 12px;">
            <i class="fas fa-lightbulb text-warning me-1"></i> Cliquez sur n'importe quelle image pour la <strong>redimensionner aux coins</strong> ou la <strong>déplacer / aligner</strong>.
        </span>
        <span id="editorWordCount" class="badge bg-light text-secondary border" style="font-size: 11px;">0 mot</span>
    </div>
</div>

@push('scripts')
<!-- Quill 2.0.2 JS -->
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const initialContent = @json($value ?? '');
    const textarea = document.getElementById('description');
    const rawHtmlArea = document.getElementById('smartRawHtmlArea');
    const toggleBtn = document.getElementById('toggleHtmlModeBtn');
    const toggleText = document.getElementById('toggleHtmlModeText');
    const quillDiv = document.getElementById('smartQuillEditor');
    const wordCountBadge = document.getElementById('editorWordCount');
    const editorContainer = document.querySelector('.smart-editor-container');
    const overlay = document.getElementById('smartImageOverlay');
    const sizeBadge = document.getElementById('smartSizeBadge');
    const imageToolbar = document.getElementById('smartImageToolbar');
    let isHtmlMode = false;
    let selectedImg = null;

    // Custom toolbar options
    const toolbarOptions = [
        [{ 'header': [1, 2, 3, 4, false] }],
        ['bold', 'italic', 'underline', 'strike'],
        [{ 'color': [] }, { 'background': [] }],
        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
        [{ 'align': [] }],
        ['blockquote', 'code-block'],
        ['link', 'image', 'clean']
    ];

    // Initialize Quill
    const quill = new Quill('#smartQuillEditor', {
        theme: 'snow',
        placeholder: 'Rédigez une description attrayante pour votre produit...',
        modules: {
            toolbar: toolbarOptions
        }
    });

    // Custom image handler for toolbar button
    const toolbar = quill.getModule('toolbar');
    toolbar.addHandler('image', function() {
        triggerSmartImageUpload();
    });

    // Set initial content
    if (initialContent) {
        quill.root.innerHTML = initialContent;
        updateWordCount();
        enableImageFeatures();
    }

    // Sync on text change
    quill.on('text-change', function() {
        if (!isHtmlMode) {
            textarea.value = quill.root.innerHTML;
            updateWordCount();
            enableImageFeatures();
            if (selectedImg) updateOverlayPosition();
        }
    });

    // Update word count
    function updateWordCount() {
        const text = quill.getText().trim();
        const words = text ? text.split(/\s+/).length : 0;
        if (wordCountBadge) {
            wordCountBadge.textContent = words + (words > 1 ? ' mots' : ' mot');
        }
    }

    // ==========================================
    // Interactive Image Resizer & Mover Logic
    // ==========================================
    function enableImageFeatures() {
        quill.root.querySelectorAll('img').forEach(img => {
            img.setAttribute('draggable', 'true');
            if (!img.dataset.hasImageListener) {
                img.dataset.hasImageListener = 'true';
                img.addEventListener('click', function(e) {
                    e.stopPropagation();
                    selectImage(img);
                });
            }
        });
    }

    function selectImage(img) {
        selectedImg = img;
        updateOverlayPosition();
        overlay.style.display = 'block';
    }

    function deselectImage() {
        if (selectedImg) {
            selectedImg = null;
            overlay.style.display = 'none';
            sizeBadge.style.display = 'none';
        }
    }

    function updateOverlayPosition() {
        if (!selectedImg || !selectedImg.isConnected) {
            deselectImage();
            return;
        }

        const imgRect = selectedImg.getBoundingClientRect();
        const containerRect = editorContainer.getBoundingClientRect();

        const top = imgRect.top - containerRect.top;
        const left = imgRect.left - containerRect.left;

        overlay.style.top = top + 'px';
        overlay.style.left = left + 'px';
        overlay.style.width = imgRect.width + 'px';
        overlay.style.height = imgRect.height + 'px';

        // Flip toolbar if image is near the top
        if (top < 52) {
            imageToolbar.classList.add('toolbar-bottom');
        } else {
            imageToolbar.classList.remove('toolbar-bottom');
        }
    }

    // Click outside to deselect
    document.addEventListener('click', function(e) {
        if (!e.target.closest('#smartImageOverlay') && !e.target.closest('.ql-editor img') && !e.target.closest('.smart-editor-helper-bar')) {
            deselectImage();
        }
    });

    // Update position on scroll or resize
    quill.root.addEventListener('scroll', function() {
        if (selectedImg) updateOverlayPosition();
    });
    window.addEventListener('resize', function() {
        if (selectedImg) updateOverlayPosition();
    });

    // Handle corner resizing
    let isResizing = false;
    let currentHandle = null;
    let startX = 0;
    let startWidth = 0;

    overlay.querySelectorAll('.smart-handle').forEach(handle => {
        handle.addEventListener('mousedown', function(e) {
            e.preventDefault();
            e.stopPropagation();
            if (!selectedImg) return;

            isResizing = true;
            currentHandle = this.dataset.handle;
            startX = e.clientX;
            startWidth = selectedImg.offsetWidth;
            sizeBadge.style.display = 'block';

            document.addEventListener('mousemove', onHandleMouseMove);
            document.addEventListener('mouseup', onHandleMouseUp);
        });
    });

    function onHandleMouseMove(e) {
        if (!isResizing || !selectedImg) return;

        const deltaX = e.clientX - startX;
        const isRight = currentHandle === 'se' || currentHandle === 'ne';
        const factor = isRight ? 1 : -1;

        const containerW = quill.root.clientWidth - 40;
        let newWidth = startWidth + (deltaX * factor);
        newWidth = Math.max(70, Math.min(containerW, newWidth));

        selectedImg.style.width = Math.round(newWidth) + 'px';
        selectedImg.style.height = 'auto';

        sizeBadge.textContent = `${Math.round(newWidth)} px`;
        updateOverlayPosition();
    }

    function onHandleMouseUp() {
        if (isResizing) {
            isResizing = false;
            sizeBadge.style.display = 'none';
            document.removeEventListener('mousemove', onHandleMouseMove);
            document.removeEventListener('mouseup', onHandleMouseUp);
            textarea.value = quill.root.innerHTML;
            updateWordCount();
        }
    }

    // Image Toolbar Actions (Align, Move, Presets, Delete)
    imageToolbar.addEventListener('click', function(e) {
        const btn = e.target.closest('button[data-action]');
        if (!btn || !selectedImg) return;
        e.stopPropagation();

        const action = btn.dataset.action;

        if (action === 'align-left') {
            selectedImg.style.float = 'left';
            selectedImg.style.margin = '8px 18px 14px 0';
            selectedImg.style.display = 'inline-block';
        } else if (action === 'align-center') {
            selectedImg.style.float = 'none';
            selectedImg.style.margin = '16px auto';
            selectedImg.style.display = 'block';
        } else if (action === 'align-right') {
            selectedImg.style.float = 'right';
            selectedImg.style.margin = '8px 0 14px 18px';
            selectedImg.style.display = 'inline-block';
        } else if (action === 'move-up') {
            // Move image before previous block
            const parent = selectedImg.closest('p, div, h1, h2, h3, h4, blockquote, table, li') || selectedImg;
            if (parent === selectedImg) {
                const prev = selectedImg.previousElementSibling;
                if (prev) prev.parentNode.insertBefore(selectedImg, prev);
            } else {
                const prev = parent.previousElementSibling;
                if (prev) parent.parentNode.insertBefore(parent, prev);
            }
        } else if (action === 'move-down') {
            // Move image after next block
            const parent = selectedImg.closest('p, div, h1, h2, h3, h4, blockquote, table, li') || selectedImg;
            if (parent === selectedImg) {
                const next = selectedImg.nextElementSibling;
                if (next) next.parentNode.insertBefore(selectedImg, next.nextSibling);
            } else {
                const next = parent.nextElementSibling;
                if (next) parent.parentNode.insertBefore(parent, next.nextSibling);
            }
        } else if (action.startsWith('size-')) {
            const pct = action.replace('size-', '');
            selectedImg.style.width = pct + '%';
            selectedImg.style.height = 'auto';
        } else if (action === 'delete') {
            const toDelete = selectedImg;
            deselectImage();
            toDelete.remove();
            textarea.value = quill.root.innerHTML;
            updateWordCount();
            return;
        }

        updateOverlayPosition();
        textarea.value = quill.root.innerHTML;
        updateWordCount();
    });

    // Native Drag and Drop Image Positioning inside Content
    quill.root.addEventListener('dragstart', function(e) {
        if (e.target.tagName === 'IMG') {
            deselectImage();
            e.dataTransfer.setData('text/html', e.target.outerHTML);
            e.target.classList.add('smart-dragging');
            window.draggedImageElement = e.target;
        }
    });

    quill.root.addEventListener('drop', function(e) {
        if (window.draggedImageElement) {
            setTimeout(() => {
                if (window.draggedImageElement && window.draggedImageElement.parentNode) {
                    window.draggedImageElement.remove();
                }
                window.draggedImageElement = null;
                enableImageFeatures();
                textarea.value = quill.root.innerHTML;
                updateWordCount();
            }, 60);
        }
    });

    // ==========================================
    // Upload & URL Image Insertion
    // ==========================================
    window.triggerSmartImageUpload = function() {
        document.getElementById('smartEditorImageInput').click();
    };

    window.handleSmartImageSelected = async function(event) {
        const file = event.target.files[0];
        if (!file) return;
        event.target.value = '';
        await uploadAndInsertImage(file);
    };

    window.insertImageFromUrl = async function() {
        if (typeof Swal !== 'undefined') {
            const { value: url } = await Swal.fire({
                title: 'Insérer une image par URL',
                input: 'url',
                inputLabel: 'Adresse web directe de l\'image (JPEG, PNG, WEBP...)',
                inputPlaceholder: 'https://exemple.com/image.jpg',
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-check me-1"></i> Insérer',
                cancelButtonText: 'Annuler',
                confirmButtonColor: '#4f46e5'
            });

            if (url) {
                const range = quill.getSelection(true) || { index: quill.getLength() };
                quill.insertEmbed(range.index, 'image', url);
                quill.setSelection(range.index + 1);
                enableImageFeatures();
            }
        } else {
            const url = prompt('Entrez l\'URL de l\'image :');
            if (url) {
                const range = quill.getSelection(true) || { index: quill.getLength() };
                quill.insertEmbed(range.index, 'image', url);
                quill.setSelection(range.index + 1);
                enableImageFeatures();
            }
        }
    };

    async function uploadAndInsertImage(file) {
        const formData = new FormData();
        formData.append('image', file);
        formData.append('_token', '{{ csrf_token() }}');

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Téléversement en cours...',
                text: 'Traitement de l\'image...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });
        }

        try {
            const res = await fetch('{{ route("products.upload-editor-image") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: formData
            });

            const data = await res.json();

            if (res.ok && data.success && data.url) {
                const range = quill.getSelection(true) || { index: quill.getLength() };
                quill.insertEmbed(range.index, 'image', data.url);
                quill.setSelection(range.index + 1);
                textarea.value = quill.root.innerHTML;
                enableImageFeatures();

                if (typeof Swal !== 'undefined') {
                    Swal.close();
                    if (typeof Toast !== 'undefined') {
                        Toast.fire({ icon: 'success', title: 'Image ajoutée avec succès !' });
                    }
                }
            } else {
                throw new Error(data.message || 'Erreur lors du téléversement.');
            }
        } catch (err) {
            console.error(err);
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: err.message || 'Impossible de téléverser l\'image.'
                });
            } else {
                alert('Erreur: ' + err.message);
            }
        }
    }

    // Paste Image Handler
    quill.root.addEventListener('paste', function(e) {
        const clipboardData = e.clipboardData || window.clipboardData;
        if (clipboardData && clipboardData.items) {
            for (let i = 0; i < clipboardData.items.length; i++) {
                const item = clipboardData.items[i];
                if (item.type.indexOf('image') !== -1) {
                    const file = item.getAsFile();
                    if (file) {
                        e.preventDefault();
                        uploadAndInsertImage(file);
                        return;
                    }
                }
            }
        }
    });

    // Drop Image Handler
    quill.root.addEventListener('drop', function(e) {
        if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length > 0) {
            const file = e.dataTransfer.files[0];
            if (file.type.startsWith('image/')) {
                e.preventDefault();
                uploadAndInsertImage(file);
            }
        }
    });

    // ==========================================
    // Creative Pre-Styled Templates
    // ==========================================
    const templates = {
        highlights: `
<div class="pro-callout-box" style="background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); border-left: 4px solid #c8102e; padding: 18px 22px; border-radius: 8px; margin: 18px 0;">
    <h3 style="margin-top: 0; margin-bottom: 10px; color: #0f172a; font-size: 1.15rem; font-weight: 700;">⭐ Points Forts & Caractéristiques Clés</h3>
    <ul style="margin-bottom: 0; padding-left: 20px; color: #334155;">
        <li><strong>Performance Professionnelle :</strong> Conçu pour répondre aux exigences les plus pointues des créateurs et techniciens.</li>
        <li><strong>Qualité de Fabrication Supérieure :</strong> Finitions impeccables, matériaux certifiés et robustesse à toute épreuve.</li>
        <li><strong>Intégration & Polyvalence :</strong> Compatibilité optimale avec les standards du marché.</li>
    </ul>
</div><p><br></p>`,
        specs: `
<div style="margin: 18px 0;">
    <h3 style="margin-top: 0; margin-bottom: 12px; color: #0f172a; font-size: 1.15rem; font-weight: 700;">📊 Spécifications Techniques</h3>
    <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
        <thead>
            <tr style="background: #f1f5f9;">
                <th style="border: 1px solid #cbd5e1; padding: 10px 14px; text-align: left; font-weight: 700;">Caractéristique</th>
                <th style="border: 1px solid #cbd5e1; padding: 10px 14px; text-align: left; font-weight: 700;">Détails</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="border: 1px solid #e2e8f0; padding: 9px 14px; font-weight: 600; width: 35%;">Type / Référence</td>
                <td style="border: 1px solid #e2e8f0; padding: 9px 14px;">Matériel Original & Certifié</td>
            </tr>
            <tr style="background: #fafafa;">
                <td style="border: 1px solid #e2e8f0; padding: 9px 14px; font-weight: 600;">Résolution / Performance</td>
                <td style="border: 1px solid #e2e8f0; padding: 9px 14px;">Ultra Haute Définition</td>
            </tr>
            <tr>
                <td style="border: 1px solid #e2e8f0; padding: 9px 14px; font-weight: 600;">Connectique & Ports</td>
                <td style="border: 1px solid #e2e8f0; padding: 9px 14px;">USB-C 3.2, HDMI, Jack Audio</td>
            </tr>
            <tr style="background: #fafafa;">
                <td style="border: 1px solid #e2e8f0; padding: 9px 14px; font-weight: 600;">Garantie</td>
                <td style="border: 1px solid #e2e8f0; padding: 9px 14px;">Garantie Officielle 2 Ans</td>
            </tr>
        </tbody>
    </table>
</div><p><br></p>`,
        box: `
<div style="background: #f0fdf4; border: 1px solid #bbf7d0; padding: 16px 20px; border-radius: 8px; margin: 18px 0; color: #166534;">
    <h3 style="margin-top: 0; margin-bottom: 10px; color: #166534; font-size: 1.15rem; font-weight: 700;">📦 Contenu de la Boîte (In the Box)</h3>
    <ul style="margin-bottom: 0; padding-left: 20px;">
        <li>1x Produit principal authentique</li>
        <li>1x Batterie d'origine haute capacité</li>
        <li>1x Câble d'alimentation / synchronisation USB-C</li>
        <li>1x Guide d'utilisation officiel & Bon de garantie</li>
    </ul>
</div><p><br></p>`,
        warranty: `
<div style="background: #eff6ff; border: 1px solid #bfdbfe; padding: 16px 20px; border-radius: 8px; margin: 18px 0; color: #1e40af;">
    <h4 style="margin-top: 0; margin-bottom: 6px; color: #1e3a8a; font-weight: 700;">🛡️ Garantie Officielle 2 Ans & SAV Casablanca</h4>
    <p style="margin-bottom: 0; font-size: 13.5px; color: #1e40af;">Matériel 100% neuf et authentique. Facturation officielle avec TVA 20% & ICE. Démonstration disponible à notre showroom.</p>
</div><p><br></p>`
    };

    window.insertSmartTemplate = function(type) {
        if (!templates[type]) return;
        const range = quill.getSelection(true) || { index: quill.getLength() };
        quill.clipboard.dangerouslyPasteHTML(range.index, templates[type]);
        textarea.value = quill.root.innerHTML;
        updateWordCount();
        enableImageFeatures();
        if (typeof Toast !== 'undefined') {
            Toast.fire({ icon: 'success', title: 'Modèle inséré avec succès !' });
        }
    };

    // Toggle HTML Mode
    window.toggleHtmlMode = function() {
        deselectImage();
        isHtmlMode = !isHtmlMode;
        if (isHtmlMode) {
            rawHtmlArea.value = quill.root.innerHTML;
            quillDiv.style.display = 'none';
            rawHtmlArea.style.display = 'block';
            toggleBtn.classList.add('active');
            toggleText.textContent = 'Mode Visuel';
        } else {
            quill.root.innerHTML = rawHtmlArea.value;
            rawHtmlArea.style.display = 'none';
            quillDiv.style.display = 'block';
            toggleBtn.classList.remove('active');
            toggleText.textContent = 'Code HTML';
            textarea.value = quill.root.innerHTML;
            updateWordCount();
            enableImageFeatures();
        }
    };

    // Sync on Form Submit
    const form = document.getElementById('productForm');
    if (form) {
        form.addEventListener('submit', function() {
            deselectImage();
            if (isHtmlMode) {
                textarea.value = rawHtmlArea.value;
            } else {
                textarea.value = quill.root.innerHTML;
            }
        });
    }
});
</script>
@endpush
