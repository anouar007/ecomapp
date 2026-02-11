@extends('layouts.app')

@section('title', isset($page) ? 'Edit Page' : 'Create Page')

@section('content')
<style>
    /* Builder Layout */
    .builder-container {
        display: flex;
        height: calc(100vh - 100px);
        background: #f8f9fa;
        overflow: hidden;
        border: 1px solid #dee2e6;
        border-radius: 8px;
    }
    .builder-sidebar {
        width: 250px;
        background: white;
        border-right: 1px solid #dee2e6;
        overflow-y: auto;
        flex-shrink: 0;
    }
    .builder-properties {
        width: 320px;
        background: white;
        border-left: 1px solid #dee2e6;
        overflow-y: auto;
        flex-shrink: 0;
    }
    .builder-canvas-wrapper {
        flex-grow: 1;
        padding: 20px;
        overflow-y: auto;
        background: #e9ecef;
        position: relative;
    }
    .builder-canvas {
        background: white;
        min-height: 800px;
        box-shadow: 0 0 15px rgba(0,0,0,0.1);
        margin: 0 auto;
        max-width: 100%;
        position: relative; /* For absolute positioning context */
    }
    
    /* Elements Library */
    .element-btn {
        display: flex;
        align-items: center;
        width: 100%;
        padding: 10px 15px;
        border: 1px solid #eee;
        background: white;
        margin-bottom: 8px;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s;
        text-align: left;
    }
    .element-btn:hover {
        background: #f1f5f9;
        border-color: #cbd5e1;
        transform: translateX(2px);
    }
    .element-icon {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f1f5f9;
        border-radius: 6px;
        margin-right: 12px;
        color: #475569;
    }

    /* Canvas Items */
    .canvas-block {
        position: relative;
        border: 2px solid transparent;
        transition: border-color 0.2s;
    }
    .canvas-block.selected {
        border-color: #3b82f6;
        z-index: 1000 !important; /* Ensure selected is visible */
    }
    .canvas-block:hover::after {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        border: 1px dashed #3b82f6;
        pointer-events: none;
    }
    .block-actions {
        position: absolute;
        top: -24px;
        right: 0;
        display: none;
        background: #3b82f6;
        border-radius: 4px 4px 0 0;
        padding: 2px 6px;
        z-index: 1001;
    }
    .canvas-block.selected .block-actions {
        display: flex;
        gap: 5px;
    }
    .action-btn {
        color: white;
        font-size: 12px;
        cursor: pointer;
        padding: 2px 4px;
    }
    .action-btn:hover {
        opacity: 0.8;
    }

    /* Properties */
    .prop-group {
        border-bottom: 1px solid #f1f1f1;
        padding: 15px;
    }
    .prop-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        color: #64748b;
        font-weight: 600;
        margin-bottom: 8px;
        display: block;
    }
    .no-selection {
        text-align: center;
        padding: 40px 20px;
        color: #94a3b8;
    }
</style>

<div class="brand-header mb-3">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="brand-title">
                <div class="brand-header-icon"><i class="fas fa-magic"></i></div>
                {{ isset($page) ? 'Edit Page: ' . $page->title : 'Create New Page' }}
            </h1>
        </div>
        <div>
            <a href="{{ route('pages.index') }}" class="btn btn-light me-2">Back</a>
            <button type="button" class="btn btn-primary" onclick="savePage()">
                <i class="fas fa-save me-2"></i> Save Page
            </button>
        </div>
    </div>
</div>

<form id="page-form" action="{{ isset($page) ? route('pages.update', $page) : route('pages.store') }}" method="POST">
    @csrf
    @if(isset($page)) @method('PUT') @endif
    
    <!-- Hidden Inputs for Page Metadata -->
    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <input type="text" name="title" class="form-control" placeholder="Page Title" value="{{ old('title', $page->title ?? '') }}" required id="input-title">
        </div>
        <div class="col-md-6">
            <div class="input-group">
                <span class="input-group-text">{{ url('/') }}/</span>
                <input type="text" name="slug" class="form-control" placeholder="slug" value="{{ old('slug', $page->slug ?? '') }}" required id="input-slug">
            </div>
        </div>
        <input type="hidden" name="layout" value="{{ old('layout', $page->layout ?? 'default') }}">
        <input type="hidden" name="is_published" value="1">
    </div>

    <!-- The Page Builder Interface -->
    <div class="builder-container">
        <!-- LEFT: Library -->
        <div class="builder-sidebar custom-scrollbar">
            <div class="p-3 border-bottom">
                <h6 class="fw-bold m-0"><i class="fas fa-layer-group me-2"></i>Elements</h6>
            </div>
            <div class="p-2">
                <div class="element-btn" onclick="addBlock('group')">
                    <div class="element-icon"><i class="fas fa-object-group"></i></div>
                    <div>
                        <div class="fw-bold small">Group / Container</div>
                        <div class="text-muted" style="font-size: 10px;">Flexbox container</div>
                    </div>
                </div>
                <div class="element-btn" onclick="addBlock('hero')">
                    <div class="element-icon"><i class="fas fa-image"></i></div>
                    <div>
                        <div class="fw-bold small">Hero Section</div>
                        <div class="text-muted" style="font-size: 10px;">Large banner with CTA</div>
                    </div>
                </div>
                <div class="element-btn" onclick="addBlock('heading')">
                    <div class="element-icon"><i class="fas fa-heading"></i></div>
                    <div>
                        <div class="fw-bold small">Heading</div>
                        <div class="text-muted" style="font-size: 10px;">H1-H6 Titles</div>
                    </div>
                </div>
                <div class="element-btn" onclick="addBlock('content')">
                    <div class="element-icon"><i class="fas fa-paragraph"></i></div>
                    <div>
                        <div class="fw-bold small">Text Block</div>
                        <div class="text-muted" style="font-size: 10px;">Rich text content</div>
                    </div>
                </div>
                <div class="element-btn" onclick="addBlock('image')">
                    <div class="element-icon"><i class="far fa-image"></i></div>
                    <div>
                        <div class="fw-bold small">Image</div>
                        <div class="text-muted" style="font-size: 10px;">Single image display</div>
                    </div>
                </div>
                <div class="element-btn" onclick="addBlock('features')">
                    <div class="element-icon"><i class="fas fa-list-ul"></i></div>
                    <div>
                        <div class="fw-bold small">Features List</div>
                        <div class="text-muted" style="font-size: 10px;">Icon grid layout</div>
                    </div>
                </div>
                <div class="element-btn" onclick="addBlock('cta')">
                    <div class="element-icon"><i class="fas fa-bullhorn"></i></div>
                    <div>
                        <div class="fw-bold small">Call to Action</div>
                        <div class="text-muted" style="font-size: 10px;">Promotional banner</div>
                    </div>
                </div>
                <hr>
                <div class="text-muted small px-2 mb-2 fw-bold">STOREFRONT</div>
                <div class="element-btn" onclick="addBlock('product-grid')">
                    <div class="element-icon"><i class="fas fa-shopping-bag"></i></div>
                    <div>
                        <div class="fw-bold small">Product Grid</div>
                        <div class="text-muted" style="font-size: 10px;">Latest products</div>
                    </div>
                </div>
                <div class="element-btn" onclick="addBlock('category-grid')">
                    <div class="element-icon"><i class="fas fa-th-large"></i></div>
                    <div>
                        <div class="fw-bold small">Categories</div>
                        <div class="text-muted" style="font-size: 10px;">Category boxes</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CENTER: Canvas -->
        <div class="builder-canvas-wrapper custom-scrollbar">
            <div id="canvas-area" class="builder-canvas p-4">
                <!-- Blocks rendered here via JS -->
            </div>
        </div>

        <!-- RIGHT: Properties -->
        <div class="builder-properties custom-scrollbar">
            <div class="p-3 border-bottom bg-light">
                <h6 class="fw-bold m-0"><i class="fas fa-sliders-h me-2"></i>Properties</h6>
            </div>
            <div id="properties-panel">
                <div class="no-selection">
                    <i class="fas fa-mouse-pointer fa-3x mb-3 opacity-25"></i>
                    <p>Select an element on the canvas to edit its properties.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden container for form submission -->
    <div id="form-data-container"></div>
</form>

@endsection

@push('scripts')
<!-- Media Manager Modal -->
<div class="modal fade" id="mediaManagerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Media Library</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="d-flex h-100">
                    <!-- Upload Sidebar -->
                    <div class="p-4 border-end bg-light" style="width: 300px;">
                        <h6 class="fw-bold mb-3">Upload New</h6>
                        <div class="upload-area p-4 border border-2 border-dashed rounded bg-white text-center mb-3" id="upload-dropzone">
                            <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                            <p class="small text-muted mb-2">Drag & Drop or Click to Upload</p>
                            <input type="file" id="media-upload-input" class="d-none" accept="image/*">
                            <button class="btn btn-primary btn-sm w-100" onclick="document.getElementById('media-upload-input').click()">Select File</button>
                        </div>
                        <div id="upload-progress" class="progress d-none mb-3" style="height: 5px;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 100%"></div>
                        </div>
                        <div id="upload-status"></div>
                    </div>
                    
                    <!-- Gallery Area -->
                    <div class="flex-grow-1 p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold m-0">Library</h6>
                            <button class="btn btn-sm btn-outline-secondary" onclick="loadMediaGallery()"><i class="fas fa-sync-alt"></i> Refresh</button>
                        </div>
                        <div class="row g-3" id="media-gallery-container">
                            <!-- Images loaded via JS -->
                            <div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="media-select-btn" disabled>Select Image</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Initialize state
    // Ensure all blocks have IDs and children arrays
    function migrateBlocks(items) {
        if(!Array.isArray(items)) return [];
        return items.map(b => {
            if(!b.id) b.id = crypto.randomUUID();
            if(!b.children) b.children = [];
            // Recursive migration
            if(b.children.length > 0) b.children = migrateBlocks(b.children);
            return b;
        });
    }

    let blocks = migrateBlocks(@json(old('content_blocks', $page->content ?? [])));
    let selectedBlockId = null; 
    let currentImageTarget = null; 

    // Initial render
    document.addEventListener('DOMContentLoaded', () => {
        renderCanvas();
        setupMediaUploader();
        
        // Auto-slug
        const titleInput = document.getElementById('input-title');
        const slugInput = document.getElementById('input-slug');
        if(titleInput && slugInput && !slugInput.value) {
            titleInput.addEventListener('input', (e) => {
                slugInput.value = e.target.value.toLowerCase()
                    .replace(/[^\w ]+/g, '')
                    .replace(/ +/g, '-');
            });
        }
    });

    // --- Media Manager Logic (UNCHANGED) ---
    const mediaModal = new bootstrap.Modal(document.getElementById('mediaManagerModal'));
    let selectedMediaUrl = null;

    function openMediaManager(targetProperty) {
        currentImageTarget = targetProperty;
        selectedMediaUrl = null;
        document.getElementById('media-select-btn').disabled = true;
        loadMediaGallery();
        mediaModal.show();
    }

    function loadMediaGallery() {
        const container = document.getElementById('media-gallery-container');
        container.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div></div>';

        fetch('{{ route("media.index") }}')
            .then(res => res.json())
            .then(images => {
                container.innerHTML = '';
                if(images.length === 0) {
                    container.innerHTML = '<div class="col-12 text-center text-muted py-5">No images found. Upload one!</div>';
                    return;
                }
                images.forEach(img => {
                    const col = document.createElement('div');
                    col.className = 'col-6 col-md-4 col-lg-3';
                    col.innerHTML = `
                        <div class="card h-100 border media-item" onclick="selectMedia(this, '${img.url}')">
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center overflow-hidden" style="height: 120px;">
                                <img src="${img.url}" class="img-fluid" style="object-fit: contain; max-height: 100%;" loading="lazy">
                            </div>
                            <div class="card-footer p-2 small text-truncate bg-white border-top-0">${img.name}</div>
                        </div>
                    `;
                    container.appendChild(col);
                });
            })
            .catch(err => {
                container.innerHTML = '<div class="text-danger text-center">Failed to load gallery.</div>';
                console.error(err);
            });
    }

    function selectMedia(el, url) {
        document.querySelectorAll('.media-item').forEach(i => i.classList.remove('border-primary', 'shadow-sm'));
        el.classList.add('border-primary', 'shadow-sm');
        selectedMediaUrl = url;
        document.getElementById('media-select-btn').disabled = false;
    }

    document.getElementById('media-select-btn').addEventListener('click', () => {
        if(selectedMediaUrl && selectedBlockId) {
            updateProperty(currentImageTarget, selectedMediaUrl);
            mediaModal.hide();
        }
    });

    function setupMediaUploader() {
        const input = document.getElementById('media-upload-input');
        const status = document.getElementById('upload-status');
        const progress = document.getElementById('upload-progress');
        const bar = progress.querySelector('.progress-bar');

        input.addEventListener('change', () => {
            if(!input.files.length) return;
            
            const file = input.files[0];
            const formData = new FormData();
            formData.append('file', file);
            formData.append('_token', '{{ csrf_token() }}');

            progress.classList.remove('d-none');
            bar.style.width = '0%';
            status.innerHTML = '';

            let w = 0;
            const timer = setInterval(() => { if(w<90) { w+=10; bar.style.width=w+'%'; } }, 100);

            fetch('{{ route("media.store") }}', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                clearInterval(timer);
                bar.style.width = '100%';
                setTimeout(() => progress.classList.add('d-none'), 500);

                if(data.success) {
                    status.innerHTML = '<div class="text-success small mt-2"><i class="fas fa-check"></i> Uploaded!</div>';
                    loadMediaGallery();
                } else {
                    status.innerHTML = `<div class="text-danger small mt-2">${data.message}</div>`;
                }
            })
            .catch(err => {
                clearInterval(timer);
                progress.classList.add('d-none');
                status.innerHTML = '<div class="text-danger small mt-2">Upload failed.</div>';
                console.error(err);
            });
        });
    }

    // --- Core Logic (Refactored for Nested Blocks) ---

    // Find a block by ID in the tree
    function findBlockById(id, itemList = blocks) {
        for (let b of itemList) {
            if (b.id === id) return b;
            if (b.children && b.children.length > 0) {
                const found = findBlockById(id, b.children);
                if (found) return found;
            }
        }
        return null;
    }

    // Find parent array and index of a block
    function findBlockParent(id, itemList = blocks, parent = null) {
        for (let i = 0; i < itemList.length; i++) {
            if (itemList[i].id === id) {
                return { list: itemList, index: i, parentBlock: parent };
            }
            if (itemList[i].children && itemList[i].children.length > 0) {
                const result = findBlockParent(id, itemList[i].children, itemList[i]);
                if (result) return result;
            }
        }
        return null;
    }

    function addBlock(type) {
        const newBlock = {
            id: crypto.randomUUID(),
            type: type,
            bg_color: 'transparent',
            text_color: '#inherit',
            width: '',
            height: '',
            position: 'static', 
            top: '', bottom: '', left: '', right: '',
            z_index: '',
            class: '',
            body: getDefaultBody(type),
            image_url: '', 
            bg_image: '',  
            cta_link: '#', 
            cta_text: 'Click Me',
            animation: 'none',
            children: [], // For nested items
            // Flex defaults for Group
            flex_dir: 'row',
            justify_content: 'flex-start',
            align_items: 'stretch',
            gap: '0'
        };

        // If a Group is selected, add INSIDE it. Otherwise add to root.
        if (selectedBlockId) {
            const selected = findBlockById(selectedBlockId);
            if (selected && selected.type === 'group') {
                selected.children.push(newBlock);
            } else {
                // Sibling or root? Let's add to root for simplicity or parent of selected
                const result = findBlockParent(selectedBlockId);
                if(result && result.parentBlock) {
                    // Add as sibling
                    result.list.splice(result.index + 1, 0, newBlock);
                } else {
                    blocks.push(newBlock);
                }
            }
        } else {
            blocks.push(newBlock);
        }

        renderCanvas();
        selectBlock(newBlock.id);
    }

    function removeBlock(id, e) {
        if(e) e.stopPropagation();
        if(confirm('Delete this block?')) {
            const res = findBlockParent(id);
            if(res) {
                res.list.splice(res.index, 1);
                if(selectedBlockId === id) {
                    selectedBlockId = null;
                    renderProperties();
                }
                renderCanvas();
            }
        }
    }

    function selectBlock(id) {
        selectedBlockId = id;
        renderCanvas(); 
        renderProperties();
    }

    function moveBlock(id, direction, e) {
        if(e) e.stopPropagation();
        const res = findBlockParent(id);
        if(!res) return;

        const list = res.list;
        const index = res.index;

        if(direction === 'up' && index > 0) {
            [list[index], list[index-1]] = [list[index-1], list[index]];
        } else if(direction === 'down' && index < list.length - 1) {
            [list[index], list[index+1]] = [list[index+1], list[index]];
        }
        renderCanvas();
    }

    function updateProperty(key, value) {
        if(selectedBlockId) {
            const b = findBlockById(selectedBlockId);
            if(b) {
                b[key] = value;
                renderCanvas(); 
            }
        }
    }

    // --- Recursive Rendering ---

    function renderCanvas() {
        const container = document.getElementById('canvas-area');
        container.innerHTML = '';

        if(blocks.length === 0) {
            container.innerHTML = `
                <div class="text-center py-5 text-muted opacity-50">
                    <i class="fas fa-layer-group fa-3x mb-3"></i>
                    <h4>Canvas Empty</h4>
                    <p>Click elements on the left to add them</p>
                </div>
            `;
            return;
        }

        // Render recursive tree
        blocks.forEach(block => {
            container.appendChild(createBlockElement(block));
        });
    }

    function createBlockElement(block) {
        const el = document.createElement('div');
        el.className = `canvas-block ${block.id === selectedBlockId ? 'selected' : ''} ${block.class || ''}`;
        el.id = block.id || '';
        
        // Stop bubbling when clicking a child
        el.onclick = (e) => {
            e.preventDefault();
            e.stopPropagation(); 
            selectBlock(block.id);
        };

        // Basic Styles
        Object.assign(el.style, {
            backgroundColor: block.bg_color !== 'transparent' ? block.bg_color : '',
            color: block.text_color !== 'inherit' ? block.text_color : '',
            width: block.width,
            height: block.height,
            position: block.position,
            zIndex: block.z_index,
            top: block.top, bottom: block.bottom, left: block.left, right: block.right,
            marginBottom: block.position === 'absolute' || block.position === 'fixed' ? '0' : '15px'
        });

        // Group Flex Styles
        if (block.type === 'group') {
            el.style.display = 'flex';
            el.style.flexDirection = block.flex_dir || 'row';
            el.style.justifyContent = block.justify_content || 'flex-start';
            el.style.alignItems = block.align_items || 'stretch';
            el.style.gap = (block.gap || '0') + 'px';
            el.style.padding = '10px'; // Visual help
            el.style.border = '1px dashed #ccc'; // Visual help
            el.style.minHeight = '50px';
        }
        
        // Background Image
        if(block.bg_image) {
            el.style.backgroundImage = `url('${block.bg_image}')`;
            el.style.backgroundSize = 'cover';
            el.style.backgroundPosition = 'center';
        }

        // Actions
        const actions = `
            <div class="block-actions">
                <span class="action-btn" onclick="moveBlock('${block.id}', 'up', event)" title="Move Up"><i class="fas fa-chevron-up"></i></span>
                <span class="action-btn" onclick="moveBlock('${block.id}', 'down', event)" title="Move Down"><i class="fas fa-chevron-down"></i></span>
                <span class="action-btn bg-danger" onclick="removeBlock('${block.id}', event)" title="Remove"><i class="fas fa-trash"></i></span>
                <small class="ms-2 text-white opacity-75">${block.type.toUpperCase()}</small>
            </div>
        `;

        // Content
        let contentHTML = '';
        if (block.type === 'group') {
            // Render children container
             const childrenContainer = document.createElement('div');
             childrenContainer.style.display = 'contents'; // Let flexbox handle layout
             if (block.children && block.children.length > 0) {
                 block.children.forEach(child => {
                     childrenContainer.appendChild(createBlockElement(child));
                 });
             } else {
                 contentHTML = '<div class="text-muted small p-2 w-100 text-center" style="border: 1px dotted #eee;">Empty Group</div>';
             }
             el.innerHTML = actions + contentHTML;
             if(block.children && block.children.length > 0) {
                 el.appendChild(childrenContainer);
             }
        } else {
             el.innerHTML = actions + getPreviewHTML(block);
        }

        return el;
    }

    function renderProperties() {
        const panel = document.getElementById('properties-panel');
        if(!selectedBlockId) {
            panel.innerHTML = `<div class="no-selection"><i class="fas fa-mouse-pointer fa-3x mb-3 opacity-25"></i><p>Select an element to edit.</p></div>`;
            return;
        }

        const b = findBlockById(selectedBlockId);
        if(!b) return; // Should not happen
        
        let html = `
            <div class="prop-group bg-light">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="fw-bold">${b.type.toUpperCase()} SETTINGS</span>
                </div>
            </div>
        `;

        // Type Specific
        if (b.type === 'group') {
             html += `
                <div class="prop-group">
                    <label class="prop-label"><i class="fas fa-object-group me-1"></i> Layout (Flex)</label>
                    <div class="mb-2">
                        <label class="small text-muted">Direction</label>
                        <select class="form-select form-select-sm" onchange="updateProperty('flex_dir', this.value)">
                            <option value="row" ${b.flex_dir === 'row' ? 'selected' : ''}>Row (Horizontal)</option>
                            <option value="column" ${b.flex_dir === 'column' ? 'selected' : ''}>Column (Vertical)</option>
                        </select>
                    </div>
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="small text-muted">Justify Content</label>
                            <select class="form-select form-select-sm" onchange="updateProperty('justify_content', this.value)">
                                <option value="flex-start" ${b.justify_content === 'flex-start' ? 'selected' : ''}>Start</option>
                                <option value="center" ${b.justify_content === 'center' ? 'selected' : ''}>Center</option>
                                <option value="flex-end" ${b.justify_content === 'flex-end' ? 'selected' : ''}>End</option>
                                <option value="space-between" ${b.justify_content === 'space-between' ? 'selected' : ''}>Space Between</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="small text-muted">Align Items</label>
                            <select class="form-select form-select-sm" onchange="updateProperty('align_items', this.value)">
                                <option value="stretch" ${b.align_items === 'stretch' ? 'selected' : ''}>Stretch</option>
                                <option value="flex-start" ${b.align_items === 'flex-start' ? 'selected' : ''}>Start</option>
                                <option value="center" ${b.align_items === 'center' ? 'selected' : ''}>Center</option>
                                <option value="flex-end" ${b.align_items === 'flex-end' ? 'selected' : ''}>End</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="small text-muted">Gap (px)</label>
                        <input type="number" class="form-control form-control-sm" value="${b.gap || 0}" onchange="updateProperty('gap', this.value)">
                    </div>
                </div>
             `;
        } else if(b.type === 'hero' || b.type === 'cta') {
            html += `
                <div class="prop-group">
                    <label class="prop-label"><i class="fas fa-star me-1"></i> ${b.type === 'hero' ? 'Hero' : 'CTA'} Options</label>
                    <div class="mb-2">
                        <label class="small text-muted">Background Image</label>
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control" value="${b.bg_image || ''}" placeholder="Image URL..." readonly>
                            <button class="btn btn-outline-secondary" type="button" onclick="openMediaManager('bg_image')"><i class="fas fa-image"></i></button>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="small text-muted">Title Text</label>
                        <input type="text" class="form-control form-control-sm" value="${b.body || ''}" oninput="updateProperty('body', this.value)">
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="small text-muted">Button Text</label>
                            <input type="text" class="form-control form-control-sm" value="${b.cta_text || ''}" oninput="updateProperty('cta_text', this.value)">
                        </div>
                        <div class="col-6">
                            <label class="small text-muted">Button Link</label>
                            <input type="text" class="form-control form-control-sm" value="${b.cta_link || ''}" oninput="updateProperty('cta_link', this.value)">
                        </div>
                    </div>
                </div>
            `;
        } else if(b.type === 'image') {
            html += `
                <div class="prop-group">
                    <label class="prop-label"><i class="fas fa-image me-1"></i> Image Options</label>
                    <div class="text-center mb-3 border p-2 bg-white rounded">
                        <img src="${b.image_url || 'https://placehold.co/600x400?text=No+Image'}" class="img-fluid" style="max-height: 150px;">
                    </div>
                    <div class="d-grid gap-2">
                         <button type="button" class="btn btn-primary btn-sm" onclick="openMediaManager('image_url')"><i class="fas fa-upload me-2"></i> Select / Upload Image</button>
                    </div>
                    <div class="mt-2">
                        <label class="small text-muted">Alt Text</label>
                        <input type="text" class="form-control form-control-sm" value="${b.body || ''}" placeholder="Image description" oninput="updateProperty('body', this.value)">
                    </div>
                </div>
            `;
        } else if(b.type === 'heading') {
            html += `
                <div class="prop-group">
                    <label class="prop-label"><i class="fas fa-heading me-1"></i> Typography</label>
                    <div class="mb-2">
                        <label class="small text-muted">Heading Text</label>
                        <input type="text" class="form-control form-control-sm" value="${b.body || ''}" oninput="updateProperty('body', this.value)">
                    </div>
                    <div class="mb-2">
                        <label class="small text-muted">Level</label>
                        <select class="form-select form-select-sm" onchange="updateProperty('level', this.value)">
                            <option value="h1" ${b.level === 'h1' ? 'selected' : ''}>H1 (Main Title)</option>
                            <option value="h2" ${b.level === 'h2' || !b.level ? 'selected' : ''}>H2 (Section)</option>
                            <option value="h3" ${b.level === 'h3' ? 'selected' : ''}>H3 (Subsection)</option>
                            <option value="h4" ${b.level === 'h4' ? 'selected' : ''}>H4</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="small text-muted">Alignment</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="alignbtn" id="align-left" autocomplete="off" ${b.align === 'text-start' ? 'checked' : ''} onclick="updateProperty('align', 'text-start')">
                            <label class="btn btn-outline-secondary btn-sm" for="align-left"><i class="fas fa-align-left"></i></label>

                            <input type="radio" class="btn-check" name="alignbtn" id="align-center" autocomplete="off" ${b.align === 'text-center' ? 'checked' : ''} onclick="updateProperty('align', 'text-center')">
                            <label class="btn btn-outline-secondary btn-sm" for="align-center"><i class="fas fa-align-center"></i></label>

                            <input type="radio" class="btn-check" name="alignbtn" id="align-right" autocomplete="off" ${b.align === 'text-end' ? 'checked' : ''} onclick="updateProperty('align', 'text-end')">
                            <label class="btn btn-outline-secondary btn-sm" for="align-right"><i class="fas fa-align-right"></i></label>
                        </div>
                    </div>
                </div>
            `;
        } else {
            // Default "Content" Block
            html += `
                <div class="prop-group">
                    <label class="prop-label"><i class="fas fa-edit me-1"></i> Content Configuration</label>
                    <textarea class="form-control form-control-sm mb-2" rows="6" placeholder="Block content or helper text..." oninput="updateProperty('body', this.value)">${b.body || ''}</textarea>
                </div>
            `;
        }

        // Layout & Position (Standard)
        html += `
            <div class="prop-group">
                <label class="prop-label"><i class="fas fa-ruler-combined me-1"></i> Dimensions & Position</label>
                <div class="row g-2 mb-2">
                    <div class="col-6">
                        <input type="text" class="form-control form-control-sm" placeholder="Width (e.g. 100%)" value="${b.width || ''}" oninput="updateProperty('width', this.value)">
                    </div>
                    <div class="col-6">
                        <input type="text" class="form-control form-control-sm" placeholder="Height (e.g. 300px)" value="${b.height || ''}" oninput="updateProperty('height', this.value)">
                    </div>
                </div>
                <div class="mb-2">
                    <select class="form-select form-select-sm" onchange="updateProperty('position', this.value)">
                        <option value="static" ${b.position === 'static' ? 'selected' : ''}>Static (Default)</option>
                        <option value="relative" ${b.position === 'relative' ? 'selected' : ''}>Relative</option>
                        <option value="absolute" ${b.position === 'absolute' ? 'selected' : ''}>Absolute</option>
                        <option value="fixed" ${b.position === 'fixed' ? 'selected' : ''}>Fixed</option>
                    </select>
                </div>
                ${b.position !== 'static' ? `
                <div class="row g-2 mb-2 bg-light p-2 rounded">
                    <div class="col-6"><input type="text" class="form-control form-control-sm" placeholder="Top" value="${b.top || ''}" onchange="updateProperty('top', this.value)"></div>
                    <div class="col-6"><input type="text" class="form-control form-control-sm" placeholder="Right" value="${b.right || ''}" onchange="updateProperty('right', this.value)"></div>
                    <div class="col-6"><input type="text" class="form-control form-control-sm" placeholder="Bottom" value="${b.bottom || ''}" onchange="updateProperty('bottom', this.value)"></div>
                    <div class="col-6"><input type="text" class="form-control form-control-sm" placeholder="Left" value="${b.left || ''}" onchange="updateProperty('left', this.value)"></div>
                    <div class="col-12"><input type="number" class="form-control form-control-sm" placeholder="Z-Index" value="${b.z_index || ''}" onchange="updateProperty('z_index', this.value)"></div>
                </div>
                ` : ''}
            </div>
        `;

        // Identity & Style
        html += `
            <div class="prop-group">
                <label class="prop-label"><i class="fas fa-fingerprint me-1"></i> Identity & Colors</label>
                <div class="row g-2 mb-2">
                    <div class="col-6">
                        <input type="text" class="form-control form-control-sm" placeholder="ID Attribute" value="${b.id || ''}" onchange="updateProperty('id', this.value)">
                    </div>
                    <div class="col-6">
                        <input type="text" class="form-control form-control-sm" placeholder="CSS Classes" value="${b.class || ''}" onchange="updateProperty('class', this.value)">
                    </div>
                </div>
                <div class="row g-2">
                    <div class="col-6">
                        <label class="small text-muted">Backgrnd</label>
                        <input type="color" class="form-control form-control-color w-100" value="${b.bg_color || '#ffffff'}" oninput="updateProperty('bg_color', this.value)">
                    </div>
                    <div class="col-6">
                        <label class="small text-muted">Text</label>
                        <input type="color" class="form-control form-control-color w-100" value="${b.text_color || '#333333'}" oninput="updateProperty('text_color', this.value)">
                    </div>
                </div>
            </div>
        `;

        panel.innerHTML = html;
    }

    function getPreviewHTML(block) {
        switch(block.type) {
            case 'hero':
                return `<div class="p-5 text-center border rounded d-flex flex-column justify-content-center align-items-center" style="min-height: 200px; ${block.bg_image ? '' : 'background: #f8f9fa;'}">
                    <h2 class="fw-bold mb-3">${block.body || 'Hero Title'}</h2>
                    <button class="btn btn-primary btn-sm px-4 rounded-pill">${block.cta_text || 'Button'}</button>
                </div>`;
            case 'heading':
                 const level = block.level || 'h2';
                 const align = block.align || 'text-start';
                 return `<div class="${align}"><${level} class="fw-bold m-0">${block.body || 'Heading Text'}</${level}></div>`;
            case 'image':
                if(block.image_url) {
                    return `<div class="text-center"><img src="${block.image_url}" class="img-fluid rounded" style="max-width: 100%;"></div>`;
                }
                return `<div class="text-center p-4 bg-light border border-dashed text-muted"><i class="far fa-image fa-3x mb-2"></i><br>Select Image</div>`;
            case 'product-grid':
                return `<div class="p-2 border border-dashed text-center bg-light text-primary fw-bold"><i class="fas fa-shopping-bag me-2"></i>${block.body || 'Latest Products'}</div>`;
            case 'category-grid':
                return `<div class="p-2 border border-dashed text-center bg-light text-success fw-bold"><i class="fas fa-th-large me-2"></i>${block.body || 'Categories'}</div>`;
            default:
                return `<div class="p-2">${block.body || 'Content Block'}</div>`;
        }
    }

    function getDefaultBody(type) {
        const defaults = {
            'hero': 'Welcome to Our Store',
            'heading': 'Section Title',
            'content': 'Lorem ipsum dolor sit amet...',
            'image': 'Image Description',
            'cta': 'Get Started Now',
            'product-grid': 'Featured Products',
            'category-grid': 'Shop by Category',
            'group': ''
        };
        return defaults[type] || '';
    }

    // --- Form Submission (Recursive Flattening/Saving) ---
    // Note: We preserve the tree structure in JSON
    window.savePage = function() {
        const form = document.getElementById('page-form');
        const hiddenContainer = document.getElementById('form-data-container');
        hiddenContainer.innerHTML = ''; 

        // Important: We need a way to send complex JSON structure.
        // Laravel default array notation `name="content_blocks[0][key]"` handles deep nesting 
        // IF we recurse properly. But simpler is to send the whole JSON string if standard array binding is too complex for recursive depth.
        // HOWEVER, the existing controller expects array.
        
        // Strategy: We will recurse and build dot notation or bracket notation keys.
        // `content_blocks[0][children][0][key]`
        
        function appendBlockInputs(block, prefix) {
            for (const [key, value] of Object.entries(block)) {
                if(key === 'children') {
                    // Recurse
                    if(Array.isArray(value)) {
                         value.forEach((child, i) => {
                             appendBlockInputs(child, `${prefix}[children][${i}]`);
                         });
                    }
                } else {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = `${prefix}[${key}]`;
                    input.value = value || ''; 
                    hiddenContainer.appendChild(input);
                }
            }
        }

        blocks.forEach((block, index) => {
            appendBlockInputs(block, `content_blocks[${index}]`);
        });

        form.submit();
    }

</script>
@endpush

