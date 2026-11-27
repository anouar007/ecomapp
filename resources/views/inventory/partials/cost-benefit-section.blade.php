@php
    $prefix = $prefix ?? 'modal';
    $currencySymbol = setting('currency_symbol', 'DH');
    $currencyDecimals = (int) (setting('currency_decimals', 2) ?? 2);
    $decimalSeparator = setting('decimal_separator', '.') ?? '.';
    $thousandsSeparator = setting('thousands_separator', ',') ?? ',';
    $currencyPosition = setting('currency_position', 'after');
@endphp

<div class="mb-3 p-3 rounded-4 border bg-white inv-cost-section-card" id="{{ $prefix }}CostSection" style="background: #ffffff !important; border-color: #d1fae5 !important;">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
        <label class="form-label text-dark small fw-bold mb-0 d-flex align-items-center gap-1.5 flex-wrap" style="font-size: 0.78rem; font-weight: 700 !important;">
            <i class="fas fa-coins text-success"></i>
            <span>Coût de cette nouvelle quantité</span>
            <span id="{{ $prefix }}CostVariantBadge" class="badge bg-primary-subtle text-primary border border-primary-subtle d-none" style="font-size: 0.65rem; font-weight: 600 !important;"></span>
        </label>
        <span class="inv-cost-opt-pill">Optionnel · PMP &amp; Bénéfice auto</span>
    </div>

    <input type="hidden" name="cost_input_mode" id="{{ $prefix }}CostInputMode" value="unit">

    <div class="inv-cost-mode-switch mb-2" role="group" aria-label="Mode de saisie du coût">
        <button type="button"
                id="{{ $prefix }}CostModeUnitBtn"
                class="inv-cost-mode-btn is-active"
                onclick="window.__invCostEngine.setMode('{{ $prefix }}', 'unit')">
            <i class="fas fa-tag me-1"></i><span>Unitaire ({{ $currencySymbol }}/u.)</span>
        </button>
        <button type="button"
                id="{{ $prefix }}CostModeTotalBtn"
                class="inv-cost-mode-btn"
                onclick="window.__invCostEngine.setMode('{{ $prefix }}', 'total')">
            <i class="fas fa-layer-group me-1"></i><span>Total lot ({{ $currencySymbol }})</span>
        </button>
    </div>

    <div class="inv-cost-input-wrap mb-2">
        <i class="fas fa-calculator text-muted flex-shrink-0" style="font-size: 0.85rem;"></i>
        <input type="number"
               step="0.01"
               min="0"
               inputmode="decimal"
               name="unit_cost"
               id="{{ $prefix }}UnitCostInput"
               class="inv-cost-number-input"
               placeholder="Optionnel — ex: 1 500.00"
               onfocus="this.select()"
               oninput="window.__invCostEngine.onInput('{{ $prefix }}')">
        <span class="inv-cost-currency-tag" id="{{ $prefix }}CostCurrencySuffix">{{ $currencySymbol }}/u.</span>
    </div>

    <div class="d-flex flex-wrap align-items-center gap-1.5 mb-2" id="{{ $prefix }}CostQuickActions">
        <button type="button"
                id="{{ $prefix }}PrefillCostBtn"
                class="badge bg-white text-primary border border-primary-subtle py-1 px-2 text-decoration-none d-none"
                style="cursor: pointer; font-size: 0.68rem;"
                onclick="window.__invCostEngine.prefill('{{ $prefix }}')">
            <i class="fas fa-magic me-1"></i>Utiliser PMP actuel (<span id="{{ $prefix }}PrefillCostLabel">0</span>)
        </button>
        <button type="button"
                id="{{ $prefix }}ClearCostBtn"
                class="badge bg-white text-muted border py-1 px-2 text-decoration-none d-none"
                style="cursor: pointer; font-size: 0.68rem;"
                onclick="window.__invCostEngine.clear('{{ $prefix }}')">
            <i class="fas fa-times me-1"></i>Effacer
        </button>
    </div>

    <!-- Optional Arrival Date for FIFO ordering -->
    <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap mb-2.5 p-2 rounded-3 bg-light border" style="border-color: #e2e8f0 !important;">
        <label for="{{ $prefix }}ArrivedAtInput" class="form-label mb-0 text-dark d-flex align-items-center gap-1.5" style="font-size: 0.72rem; font-weight: 600 !important;">
            <i class="far fa-calendar-check text-primary"></i>
            <span>Date d'arrivée du lot</span>
        </label>
        <div class="d-flex align-items-center gap-1.5 flex-grow-1 justify-content-end" style="min-width: 170px;">
            <input type="date"
                   name="arrived_at"
                   id="{{ $prefix }}ArrivedAtInput"
                   class="form-control form-control-sm bg-white"
                   style="max-width: 155px; font-size: 0.78rem; min-height: 32px;">
        </div>
        <div class="w-100 text-muted" style="font-size: 0.66rem; line-height: 1.25;">
            Vide = aujourd'hui (utilisé pour l'ordre FIFO : le plus ancien arrivage est vendu en premier).
        </div>
    </div>

    <!-- Dynamic Benefit & Weighted-Average Cost Live Simulator -->
    <div class="rounded-3 border bg-white p-2.5" id="{{ $prefix }}BenefitPreview" style="font-size: 0.74rem; border-color: #e2e8f0 !important;">
        <div class="d-flex align-items-center justify-content-between text-muted pb-1.5 mb-1.5 border-bottom" style="font-size: 0.71rem;">
            <span>Prix de vente : <strong class="text-dark" id="{{ $prefix }}PreviewSellPrice">—</strong></span>
            <span>PMP actuel : <strong class="text-dark" id="{{ $prefix }}PreviewOldPmp">—</strong></span>
        </div>

        <div id="{{ $prefix }}PreviewCalculatedState" class="d-none">
            <div class="row g-2 text-start inv-cost-metrics-grid">
                <div class="col-6">
                    <div class="p-2 rounded-2 bg-light border h-100 inv-cost-tile">
                        <div class="text-muted text-uppercase" style="font-size: 0.61rem; letter-spacing: 0.03em;">Coût de ce lot</div>
                        <div class="inv-cost-val text-dark" id="{{ $prefix }}PreviewBatchTotal">—</div>
                        <div class="text-muted inv-cost-sub" id="{{ $prefix }}PreviewBatchUnit">—</div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="p-2 rounded-2 border h-100 inv-cost-tile" id="{{ $prefix }}PreviewBatchBenefitBox" style="background: #ecfdf5; border-color: #a7f3d0 !important;">
                        <div class="text-muted text-uppercase" style="font-size: 0.61rem; letter-spacing: 0.03em;">Bénéfice sur ce lot</div>
                        <div class="inv-cost-val text-success" id="{{ $prefix }}PreviewBatchBenefit">—</div>
                        <div class="inv-cost-sub fw-semibold" id="{{ $prefix }}PreviewBatchMargin">—</div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="p-2 rounded-2 bg-light border h-100 inv-cost-tile">
                        <div class="text-muted text-uppercase" style="font-size: 0.61rem; letter-spacing: 0.03em;">Nouveau PMP (CUMP)</div>
                        <div class="inv-cost-val text-primary" id="{{ $prefix }}PreviewNewPmp">—</div>
                        <div class="text-muted inv-cost-sub" id="{{ $prefix }}PreviewPmpDelta">—</div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="p-2 rounded-2 border h-100 inv-cost-tile" id="{{ $prefix }}PreviewAvgBenefitBox" style="background: #eff6ff; border-color: #bfdbfe !important;">
                        <div class="text-muted text-uppercase" style="font-size: 0.61rem; letter-spacing: 0.03em;">Bénéfice unitaire après ajout</div>
                        <div class="inv-cost-val text-dark" id="{{ $prefix }}PreviewNewUnitBenefit">—</div>
                        <div class="text-muted inv-cost-sub" id="{{ $prefix }}PreviewProjectedVal">—</div>
                    </div>
                </div>
            </div>
        </div>

        <div id="{{ $prefix }}PreviewIdleState" class="text-muted" style="font-size: 0.71rem;">
            <i class="fas fa-info-circle text-primary me-1"></i>
            <span id="{{ $prefix }}PreviewIdleText">Saisissez le coût unitaire ou total pour calculer le bénéfice et le nouveau PMP en temps réel.</span>
        </div>
    </div>
</div>

@once
<style>
    .inv-cost-opt-pill {
        display: inline-flex;
        align-items: center;
        padding: 2px 8px;
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #cbd5e1;
        border-radius: 999px !important;
        font-size: 0.64rem;
        font-weight: 600 !important;
    }
    .inv-cost-mode-switch {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 4px;
        padding: 3px;
        background: #e2e8f0;
        border-radius: 10px !important;
    }
    .inv-cost-mode-btn {
        appearance: none;
        border: none;
        background: transparent;
        color: #475569;
        min-height: 36px;
        padding: 6px 8px;
        border-radius: 8px !important;
        font-size: 0.74rem;
        font-weight: 600 !important;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        white-space: nowrap;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .inv-cost-mode-btn.is-active {
        background: #0f172a;
        color: #ffffff;
        box-shadow: 0 1px 4px rgba(15, 23, 42, 0.18);
    }
    .inv-cost-input-wrap {
        display: flex;
        align-items: center;
        gap: 8px;
        min-height: 44px;
        padding: 4px 10px;
        background: #ffffff;
        border: 1.5px solid #cbd5e1;
        border-radius: 10px !important;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }
    .inv-cost-input-wrap:focus-within {
        border-color: #059669;
        box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.12);
    }
    .inv-cost-number-input {
        flex: 1 1 auto;
        min-width: 0;
        border: none !important;
        outline: none !important;
        box-shadow: none !important;
        background: transparent !important;
        font-size: 16px !important;
        font-weight: 700 !important;
        font-variant-numeric: tabular-nums;
        color: #0f172a;
        padding: 0;
        -moz-appearance: textfield;
    }
    .inv-cost-number-input::-webkit-inner-spin-button,
    .inv-cost-number-input::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    .inv-cost-currency-tag {
        flex-shrink: 0;
        padding: 3px 8px;
        background: #f1f5f9;
        color: #334155;
        border-radius: 6px !important;
        font-size: 0.72rem;
        font-weight: 700 !important;
    }
    .inv-cost-metrics-grid {
        font-variant-numeric: tabular-nums;
    }
    .inv-cost-tile {
        min-width: 0;
        overflow-wrap: anywhere;
    }
    .inv-cost-val {
        font-size: 0.84rem;
        font-weight: 700 !important;
        line-height: 1.25;
        margin-top: 2px;
    }
    .inv-cost-sub {
        font-size: 0.65rem;
        line-height: 1.3;
        margin-top: 1px;
    }
</style>
<script>
    window.__invCurrencyCfg = {
        symbol: @json($currencySymbol),
        decimals: @json($currencyDecimals),
        decSep: @json($decimalSeparator),
        thouSep: @json($thousandsSeparator),
        position: @json($currencyPosition)
    };

    window.formatInvCurrency = function (amount) {
        const cfg = window.__invCurrencyCfg || { symbol: 'DH', decimals: 2, decSep: '.', thouSep: ',', position: 'after' };
        const num = Number(amount);
        const safe = Number.isFinite(num) ? num : 0;
        const neg = safe < 0;
        const abs = Math.abs(safe).toFixed(cfg.decimals);
        const parts = abs.split('.');
        const intPart = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, cfg.thouSep);
        const formatted = (parts.length > 1 ? intPart + cfg.decSep + parts[1] : intPart);
        const signed = (neg ? '-' : '') + formatted;
        return cfg.position === 'before' ? `${cfg.symbol} ${signed}` : `${signed} ${cfg.symbol}`;
    };

    window.__invCostContexts = window.__invCostContexts || {};
    window.__invCostEngine = {
        getMode(prefix) {
            return document.getElementById(prefix + 'CostInputMode')?.value || 'unit';
        },
        setMode(prefix, mode) {
            const modeInput = document.getElementById(prefix + 'CostInputMode');
            if (modeInput) modeInput.value = mode;
            const unitBtn = document.getElementById(prefix + 'CostModeUnitBtn');
            const totalBtn = document.getElementById(prefix + 'CostModeTotalBtn');
            const suffix = document.getElementById(prefix + 'CostCurrencySuffix');
            const costInput = document.getElementById(prefix + 'UnitCostInput');
            const sym = window.__invCurrencyCfg?.symbol || 'DH';

            if (unitBtn) {
                unitBtn.classList.toggle('is-active', mode === 'unit');
            }
            if (totalBtn) {
                totalBtn.classList.toggle('is-active', mode === 'total');
            }
            if (suffix) {
                suffix.textContent = mode === 'total' ? sym : `${sym}/u.`;
            }
            if (costInput) {
                costInput.placeholder = mode === 'total'
                    ? 'Optionnel — coût total du lot'
                    : 'Optionnel — ex: 1 500.00';
            }
            this.recalc(prefix);
        },
        prefill(prefix) {
            const ctx = window.__invCostContexts[prefix] || {};
            const oldCost = ctx.existingCost !== null && ctx.existingCost !== undefined ? parseFloat(ctx.existingCost) : null;
            if (oldCost === null || !(oldCost >= 0)) return;
            const qty = Math.max(1, parseInt(ctx.addedQty || 1, 10));
            const mode = this.getMode(prefix);
            const input = document.getElementById(prefix + 'UnitCostInput');
            if (!input) return;
            input.value = mode === 'total' ? (oldCost * qty).toFixed(2) : oldCost.toFixed(2);
            this.recalc(prefix);
        },
        clear(prefix) {
            const input = document.getElementById(prefix + 'UnitCostInput');
            if (input) input.value = '';
            this.recalc(prefix);
        },
        onInput(prefix) {
            this.recalc(prefix);
        },
        recalc(prefix, overrideCtx = null) {
            if (overrideCtx) {
                window.__invCostContexts[prefix] = Object.assign(window.__invCostContexts[prefix] || {}, overrideCtx);
            }
            const ctx = window.__invCostContexts[prefix] || {};
            const section = document.getElementById(prefix + 'CostSection');
            const input = document.getElementById(prefix + 'UnitCostInput');
            const arrivedInput = document.getElementById(prefix + 'ArrivedAtInput');
            if (!section || !input) return;

            const op = ctx.op || 'in';
            if (op !== 'in') {
                section.style.display = 'none';
                input.disabled = true;
                if (arrivedInput) arrivedInput.disabled = true;
                return;
            }
            section.style.display = 'block';
            input.disabled = false;
            if (arrivedInput) arrivedInput.disabled = false;

            const sellingPrice = parseFloat(ctx.sellingPrice || 0) || 0;
            const existingStock = Math.max(0, parseInt(ctx.existingStock || 0, 10) || 0);
            const existingCost = (ctx.existingCost !== null && ctx.existingCost !== undefined && ctx.existingCost !== '' && Number(ctx.existingCost) > 0)
                ? parseFloat(ctx.existingCost)
                : null;
            const addedQty = Math.max(0, parseInt(ctx.addedQty || 0, 10) || 0);
            const mode = this.getMode(prefix);

            const variantBadgeEl = document.getElementById(prefix + 'CostVariantBadge');
            if (variantBadgeEl) {
                const vName = String(ctx.variantName || '').trim();
                if (vName !== '') {
                    variantBadgeEl.textContent = `Variante : ${vName}`;
                    variantBadgeEl.classList.remove('d-none');
                } else {
                    variantBadgeEl.textContent = '';
                    variantBadgeEl.classList.add('d-none');
                }
            }

            const sellEl = document.getElementById(prefix + 'PreviewSellPrice');
            const oldPmpEl = document.getElementById(prefix + 'PreviewOldPmp');
            if (sellEl) sellEl.textContent = window.formatInvCurrency(sellingPrice);
            if (oldPmpEl) oldPmpEl.textContent = existingCost !== null ? `${window.formatInvCurrency(existingCost)}/u.` : 'Non défini';

            const prefillBtn = document.getElementById(prefix + 'PrefillCostBtn');
            const prefillLbl = document.getElementById(prefix + 'PrefillCostLabel');
            if (prefillBtn && prefillLbl) {
                if (existingCost !== null) {
                    prefillBtn.classList.remove('d-none');
                    prefillLbl.textContent = window.formatInvCurrency(existingCost);
                } else {
                    prefillBtn.classList.add('d-none');
                }
            }

            const rawVal = String(input.value || '').trim();
            const clearBtn = document.getElementById(prefix + 'ClearCostBtn');
            if (clearBtn) {
                clearBtn.classList.toggle('d-none', rawVal === '');
            }

            const calcBox = document.getElementById(prefix + 'PreviewCalculatedState');
            const idleBox = document.getElementById(prefix + 'PreviewIdleState');
            const idleText = document.getElementById(prefix + 'PreviewIdleText');

            const parsed = rawVal !== '' ? parseFloat(rawVal) : NaN;
            if (rawVal === '' || !Number.isFinite(parsed) || parsed < 0 || addedQty <= 0) {
                if (calcBox) calcBox.classList.add('d-none');
                if (idleBox) idleBox.classList.remove('d-none');
                if (idleText) {
                    if (existingCost !== null && addedQty > 0) {
                        const estUnitBen = sellingPrice - existingCost;
                        const estBatchBen = estUnitBen * addedQty;
                        const estMargin = sellingPrice > 0 ? ((estUnitBen / sellingPrice) * 100).toFixed(1) : '0.0';
                        const sign = estBatchBen >= 0 ? '+' : '';
                        idleText.innerHTML = `Sans saisie de coût, le PMP reste <strong>${window.formatInvCurrency(existingCost)}</strong> (bénéfice estimé sur +${addedQty} u. : <strong class="${estBatchBen >= 0 ? 'text-success' : 'text-danger'}">${sign}${window.formatInvCurrency(estBatchBen)}</strong> / ${estMargin}%).`;
                    } else {
                        idleText.textContent = 'Saisissez le coût unitaire ou total pour calculer le bénéfice et le nouveau PMP en temps réel.';
                    }
                }
                return;
            }

            const batchTotalCost = mode === 'total' ? parsed : (parsed * addedQty);
            const batchUnitCost = addedQty > 0 ? (batchTotalCost / addedQty) : parsed;
            const newPmp = (existingStock > 0 && existingCost !== null)
                ? (((existingStock * existingCost) + batchTotalCost) / (existingStock + addedQty))
                : batchUnitCost;

            const batchUnitBen = sellingPrice - batchUnitCost;
            const batchTotalBen = batchUnitBen * addedQty;
            const batchMargin = sellingPrice > 0 ? ((batchUnitBen / sellingPrice) * 100).toFixed(1) : '0.0';

            const newUnitBen = sellingPrice - newPmp;
            const newMargin = sellingPrice > 0 ? ((newUnitBen / sellingPrice) * 100).toFixed(1) : '0.0';
            const totalAfterQty = existingStock + addedQty;
            const projectedStockVal = totalAfterQty * newPmp;

            if (idleBox) idleBox.classList.add('d-none');
            if (calcBox) calcBox.classList.remove('d-none');

            const batchTotEl = document.getElementById(prefix + 'PreviewBatchTotal');
            const batchUnitEl = document.getElementById(prefix + 'PreviewBatchUnit');
            const batchBenEl = document.getElementById(prefix + 'PreviewBatchBenefit');
            const batchMrgEl = document.getElementById(prefix + 'PreviewBatchMargin');
            const batchBoxEl = document.getElementById(prefix + 'PreviewBatchBenefitBox');
            const newPmpEl = document.getElementById(prefix + 'PreviewNewPmp');
            const pmpDeltaEl = document.getElementById(prefix + 'PreviewPmpDelta');
            const newUniEl = document.getElementById(prefix + 'PreviewNewUnitBenefit');
            const avgBoxEl = document.getElementById(prefix + 'PreviewAvgBenefitBox');
            const projValEl = document.getElementById(prefix + 'PreviewProjectedVal');

            if (batchTotEl) batchTotEl.textContent = window.formatInvCurrency(batchTotalCost);
            if (batchUnitEl) batchUnitEl.textContent = `${window.formatInvCurrency(batchUnitCost)} / u. (×${addedQty})`;

            if (batchBenEl) {
                batchBenEl.innerHTML = `${batchTotalBen < 0 ? '<i class="fas fa-triangle-exclamation me-1"></i>' : ''}${batchTotalBen >= 0 ? '+' : ''}${window.formatInvCurrency(batchTotalBen)}`;
                batchBenEl.className = batchTotalBen >= 0 ? 'inv-cost-val text-success' : 'inv-cost-val text-danger';
            }
            if (batchMrgEl) {
                batchMrgEl.textContent = `${batchUnitBen >= 0 ? '+' : ''}${window.formatInvCurrency(batchUnitBen)}/u · Marge ${batchMargin}%`;
                batchMrgEl.className = batchTotalBen >= 0 ? 'inv-cost-sub fw-semibold text-success' : 'inv-cost-sub fw-semibold text-danger';
            }
            if (batchBoxEl) {
                batchBoxEl.style.background = batchTotalBen >= 0 ? '#ecfdf5' : '#fef2f2';
                batchBoxEl.style.borderColor = batchTotalBen >= 0 ? '#a7f3d0' : '#fecaca';
            }

            if (newPmpEl) newPmpEl.textContent = `${window.formatInvCurrency(newPmp)} / u.`;
            if (pmpDeltaEl) {
                if (existingCost !== null) {
                    const diff = newPmp - existingCost;
                    const diffSign = diff >= 0 ? '+' : '';
                    pmpDeltaEl.textContent = `${window.formatInvCurrency(existingCost)} → ${window.formatInvCurrency(newPmp)} (${diffSign}${window.formatInvCurrency(diff)})`;
                } else {
                    pmpDeltaEl.textContent = 'Coût initial enregistré';
                }
            }

            if (newUniEl) {
                newUniEl.innerHTML = `${newUnitBen < 0 ? '<i class="fas fa-triangle-exclamation me-1"></i>' : ''}${newUnitBen >= 0 ? '+' : ''}${window.formatInvCurrency(newUnitBen)} / u. (${newMargin}%)`;
                newUniEl.className = newUnitBen >= 0 ? 'inv-cost-val text-success' : 'inv-cost-val text-danger';
            }
            if (avgBoxEl) {
                avgBoxEl.style.background = newUnitBen >= 0 ? '#eff6ff' : '#fef2f2';
                avgBoxEl.style.borderColor = newUnitBen >= 0 ? '#bfdbfe' : '#fecaca';
            }
            if (projValEl) {
                projValEl.textContent = `Valeur stock (${totalAfterQty} u.) : ${window.formatInvCurrency(projectedStockVal)}`;
            }
        }
    };
</script>
@endonce
