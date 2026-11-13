@php
    $selectedIds = collect($selectedPermissions ?? [])->map(fn ($id) => (int) $id)->all();
@endphp

<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3 p-3 rounded-3 bg-light border">
    <div class="d-flex align-items-center gap-2 flex-grow-1" style="max-width: 380px;">
        <div class="input-group input-group-sm">
            <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
            <input type="text" id="permSearchInput" class="form-control border-start-0"
                   placeholder="Rechercher une section, action ou permission...">
        </div>
    </div>
    <div class="d-flex align-items-center gap-2">
        <span class="badge bg-dark-subtle text-dark px-2.5 py-1.5" id="globalPermCount">
            {{ count($selectedIds) }} / {{ $permissions->count() }} sélectionnées
        </span>
        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold" id="selectAllGlobalBtn">
            <i class="fas fa-check-double me-1"></i> Tout cocher
        </button>
        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 fw-semibold" id="deselectAllGlobalBtn">
            <i class="fas fa-times me-1"></i> Tout décocher
        </button>
    </div>
</div>

<div class="row g-3" id="permissionModulesContainer">
    @foreach($permissionModules as $modKey => $module)
        <div class="col-12 col-lg-6 perm-module-card" data-module="{{ $modKey }}">
            <div class="card h-100 border shadow-xs rounded-4 overflow-hidden">
                <div class="card-header bg-white py-2.5 px-3 d-flex align-items-center justify-content-between gap-2 border-bottom"
                     style="border-top: 3px solid {{ $module['color'] }};">
                    <div class="d-flex align-items-center gap-2">
                        <span class="d-inline-flex align-items-center justify-content-center rounded-2"
                              style="width: 30px; height: 30px; background: {{ $module['color'] }}18; color: {{ $module['color'] }};">
                            <i class="{{ $module['icon'] }}"></i>
                        </span>
                        <div>
                            <div class="fw-bold text-dark" style="font-size: 0.9rem;">{{ $module['title'] }}</div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-light text-secondary border module-count-badge" style="font-size: 0.72rem;">0 / 0</span>
                        <button type="button" class="btn btn-xs btn-light border rounded-pill px-2 py-0.5 fw-semibold toggle-module-btn"
                                style="font-size: 0.72rem;">
                            Tout cocher
                        </button>
                    </div>
                </div>
                <div class="card-body p-2.5">
                    <div class="d-flex flex-column gap-1.5">
                        @foreach($module['permissions'] as $permName => $permDescription)
                            @php
                                $permModel = $permissionsByName->get($permName);
                            @endphp
                            @if($permModel)
                                @php
                                    $isChecked = in_array((int) $permModel->id, $selectedIds, true);
                                @endphp
                                <label class="perm-item d-flex align-items-start gap-2.5 p-2 rounded-3 border {{ $isChecked ? 'bg-primary-subtle border-primary-subtle' : 'bg-white' }}"
                                       style="cursor: pointer; transition: background 0.15s ease;"
                                       data-search="{{ mb_strtolower($permName . ' ' . $permDescription . ' ' . $module['title']) }}">
                                    <input type="checkbox"
                                           class="form-check-input mt-1 perm-checkbox flex-shrink-0"
                                           name="permissions[]"
                                           value="{{ $permModel->id }}"
                                           {{ $isChecked ? 'checked' : '' }}>
                                    <div class="min-w-0 flex-grow-1">
                                        <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap">
                                            <span class="fw-semibold text-dark" style="font-size: 0.83rem;">{{ $permDescription }}</span>
                                            <code class="text-muted bg-light px-1.5 py-0.5 rounded" style="font-size: 0.68rem;">{{ $permName }}</code>
                                        </div>
                                    </div>
                                </label>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('permissionModulesContainer');
    if (!container) return;

    function refreshCounts() {
        const allBoxes = container.querySelectorAll('.perm-checkbox');
        const checkedBoxes = container.querySelectorAll('.perm-checkbox:checked');
        const globalBadge = document.getElementById('globalPermCount');
        if (globalBadge) {
            globalBadge.textContent = `${checkedBoxes.length} / ${allBoxes.length} sélectionnées`;
        }

        container.querySelectorAll('.perm-module-card').forEach(card => {
            const boxes = card.querySelectorAll('.perm-checkbox');
            const checked = card.querySelectorAll('.perm-checkbox:checked');
            const badge = card.querySelector('.module-count-badge');
            const toggleBtn = card.querySelector('.toggle-module-btn');
            if (badge) badge.textContent = `${checked.length} / ${boxes.length}`;
            if (toggleBtn) {
                toggleBtn.textContent = (boxes.length > 0 && checked.length === boxes.length) ? 'Tout décocher' : 'Tout cocher';
            }
        });
    }

    container.addEventListener('change', function (e) {
        if (!e.target.classList.contains('perm-checkbox')) return;
        const item = e.target.closest('.perm-item');
        if (item) {
            item.classList.toggle('bg-primary-subtle', e.target.checked);
            item.classList.toggle('border-primary-subtle', e.target.checked);
            item.classList.toggle('bg-white', !e.target.checked);
        }
        refreshCounts();
    });

    container.querySelectorAll('.toggle-module-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const card = btn.closest('.perm-module-card');
            const boxes = Array.from(card.querySelectorAll('.perm-checkbox'));
            const allChecked = boxes.every(b => b.checked);
            boxes.forEach(b => {
                b.checked = !allChecked;
                b.dispatchEvent(new Event('change', { bubbles: true }));
            });
        });
    });

    document.getElementById('selectAllGlobalBtn')?.addEventListener('click', () => {
        container.querySelectorAll('.perm-checkbox').forEach(b => {
            b.checked = true;
            b.dispatchEvent(new Event('change', { bubbles: true }));
        });
    });

    document.getElementById('deselectAllGlobalBtn')?.addEventListener('click', () => {
        container.querySelectorAll('.perm-checkbox').forEach(b => {
            b.checked = false;
            b.dispatchEvent(new Event('change', { bubbles: true }));
        });
    });

    document.getElementById('permSearchInput')?.addEventListener('input', function () {
        const q = this.value.trim().toLowerCase();
        container.querySelectorAll('.perm-module-card').forEach(card => {
            let visibleCount = 0;
            card.querySelectorAll('.perm-item').forEach(item => {
                const hay = item.dataset.search || '';
                const match = !q || hay.includes(q);
                item.classList.toggle('d-none', !match);
                if (match) visibleCount++;
            });
            card.classList.toggle('d-none', visibleCount === 0);
        });
    });

    refreshCounts();
});
</script>

