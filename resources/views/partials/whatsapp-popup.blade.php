@php
    $rawWa = function_exists('setting') ? setting('social_whatsapp', config('cooperative.whatsapp', '212661000000')) : config('cooperative.whatsapp', '212661000000');
    $waPhone = preg_replace('/[^0-9]/', '', $rawWa);
    if(empty($waPhone)) {
        $waPhone = '212661000000';
    }
    $defaultMsg = urlencode("Bonjour Coopérative Aït Oumdis, je souhaite des informations sur vos soins 100% bio.");
@endphp

<!-- WhatsApp Floating Button & Popup Widget -->
<style>
.sp-wa-widget {
    position: fixed;
    bottom: 26px;
    right: 26px;
    z-index: 9999;
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
}

/* Floating Trigger */
.sp-wa-trigger-wrapper {
    display: flex;
    align-items: center;
    gap: 10px;
    justify-content: flex-end;
}

.sp-wa-pill-label {
    background: #0c261e;
    color: #ffffff;
    font-size: 0.82rem;
    font-weight: 600;
    padding: 8px 14px;
    border-radius: 9999px;
    box-shadow: 0 4px 18px rgba(12, 38, 30, 0.22);
    display: inline-flex;
    align-items: center;
    gap: 7px;
    border: 1px solid rgba(226, 173, 80, 0.3);
    cursor: pointer;
    transition: all 0.25s ease;
    user-select: none;
}

.sp-wa-pill-label:hover {
    background: #133a2e;
    transform: translateY(-2px);
    box-shadow: 0 6px 22px rgba(12, 38, 30, 0.3);
}

.sp-wa-pulsing-badge {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #25d366;
    display: inline-block;
    box-shadow: 0 0 0 0 rgba(37, 211, 102, 0.7);
    animation: spWaPulse 1.8s infinite;
}

@keyframes spWaPulse {
    0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(37, 211, 102, 0.7); }
    70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(37, 211, 102, 0); }
    100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(37, 211, 102, 0); }
}

.sp-wa-trigger-btn {
    width: 58px;
    height: 58px;
    border-radius: 50%;
    background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);
    color: #ffffff;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 8px 24px rgba(37, 211, 102, 0.45);
    position: relative;
    transition: all 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.sp-wa-trigger-btn:hover {
    transform: scale(1.08) translateY(-3px);
    box-shadow: 0 12px 30px rgba(37, 211, 102, 0.6);
}

.sp-wa-trigger-btn:active {
    transform: scale(0.96);
}

.sp-wa-online-ping {
    position: absolute;
    top: 2px;
    right: 2px;
    width: 14px;
    height: 14px;
    background: #ffffff;
    border-radius: 50%;
    border: 3px solid #25d366;
}

/* Chat Popup Window */
.sp-wa-popup {
    position: absolute;
    bottom: 74px;
    right: 0;
    width: 350px;
    max-width: calc(100vw - 36px);
    background: #ffffff;
    border-radius: 20px;
    box-shadow: 0 16px 48px rgba(12, 38, 30, 0.25);
    border: 1px solid rgba(12, 38, 30, 0.08);
    overflow: hidden;
    opacity: 0;
    visibility: hidden;
    transform: translateY(18px) scale(0.95);
    transition: all 0.28s cubic-bezier(0.16, 1, 0.3, 1);
}

.sp-wa-popup.is-open {
    opacity: 1;
    visibility: visible;
    transform: translateY(0) scale(1);
}

.sp-wa-header {
    background: linear-gradient(135deg, #0c261e 0%, #173d2f 100%);
    color: #ffffff;
    padding: 16px 18px;
    display: flex;
    align-items: center;
    gap: 12px;
    position: relative;
}

.sp-wa-avatar-box {
    position: relative;
    width: 44px;
    height: 44px;
    flex-shrink: 0;
}

.sp-wa-avatar {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    object-fit: cover;
    background: #faf7f2;
    padding: 3px;
    border: 1.5px solid #e2ad50;
}

.sp-wa-status-dot {
    position: absolute;
    bottom: 1px;
    right: 1px;
    width: 11px;
    height: 11px;
    border-radius: 50%;
    background: #25d366;
    border: 2px solid #0c261e;
}

.sp-wa-header-info {
    flex: 1;
    min-width: 0;
}

.sp-wa-name {
    font-size: 0.95rem;
    font-weight: 700;
    color: #ffffff;
    line-height: 1.25;
}

.sp-wa-status {
    font-size: 0.72rem;
    color: rgba(255, 255, 255, 0.78);
    margin-top: 2px;
}

.sp-wa-close-btn {
    background: transparent;
    border: none;
    color: rgba(255, 255, 255, 0.65);
    cursor: pointer;
    padding: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
    transition: all 0.2s;
}

.sp-wa-close-btn:hover {
    color: #ffffff;
    background: rgba(255, 255, 255, 0.12);
}

.sp-wa-body {
    padding: 18px 16px;
    background-color: #faf7f2;
    background-image: radial-gradient(rgba(12, 38, 30, 0.04) 1px, transparent 1px);
    background-size: 16px 16px;
    max-height: 320px;
    overflow-y: auto;
}

.sp-wa-msg-bubble {
    background: #ffffff;
    border-radius: 4px 16px 16px 16px;
    padding: 12px 14px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
    border: 1px solid rgba(12, 38, 30, 0.05);
    margin-bottom: 14px;
}

.sp-wa-msg-author {
    font-size: 0.72rem;
    font-weight: 700;
    color: #c28d32;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 4px;
}

.sp-wa-msg-text {
    font-size: 0.84rem;
    color: #1a2e26;
    line-height: 1.45;
    margin: 0 0 6px 0;
}

.sp-wa-msg-time {
    font-size: 0.68rem;
    color: #8c9b93;
    text-align: right;
}

.sp-wa-quick-actions {
    display: flex;
    flex-direction: column;
    gap: 7px;
}

.sp-wa-chip {
    background: #ffffff;
    border: 1px solid rgba(12, 38, 30, 0.12);
    border-radius: 9999px;
    padding: 8px 14px;
    font-size: 0.78rem;
    font-weight: 600;
    color: #0c261e;
    text-decoration: none;
    transition: all 0.2s ease;
    display: block;
    box-shadow: 0 2px 6px rgba(0,0,0,0.02);
}

.sp-wa-chip:hover {
    background: #0c261e;
    color: #e2ad50;
    border-color: #0c261e;
    transform: translateX(3px);
}

.sp-wa-footer {
    padding: 14px 16px;
    background: #ffffff;
    border-top: 1px solid rgba(12, 38, 30, 0.08);
}

.sp-wa-cta-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    width: 100%;
    padding: 12px 18px;
    background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);
    color: #ffffff;
    font-size: 0.88rem;
    font-weight: 700;
    border-radius: 9999px;
    text-decoration: none;
    box-shadow: 0 4px 16px rgba(37, 211, 102, 0.35);
    transition: all 0.22s ease;
}

.sp-wa-cta-btn:hover {
    color: #ffffff;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(37, 211, 102, 0.5);
}

@media (max-width: 576px) {
    .sp-wa-widget {
        bottom: 18px;
        right: 18px;
    }
    .sp-wa-pill-label {
        display: none;
    }
    .sp-wa-trigger-btn {
        width: 52px;
        height: 52px;
    }
}
</style>

<div id="spWhatsappWidget" class="sp-wa-widget">
    <!-- Popup Chat Window -->
    <div id="spWaPopup" class="sp-wa-popup" aria-hidden="true">
        <!-- Header -->
        <div class="sp-wa-header">
            <div class="sp-wa-avatar-box">
                <img src="{{ app_logo_url() }}" alt="Aït Oumdis" class="sp-wa-avatar">
                <span class="sp-wa-status-dot"></span>
            </div>
            <div class="sp-wa-header-info">
                <div class="sp-wa-name">Coopérative Aït Oumdis</div>
                <div class="sp-wa-status">En ligne • Répond en quelques minutes</div>
            </div>
            <button type="button" class="sp-wa-close-btn" id="spWaCloseBtn" aria-label="Fermer la discussion">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>

        <!-- Body with message bubbles & quick topics -->
        <div class="sp-wa-body">
            <div class="sp-wa-msg-bubble">
                <div class="sp-wa-msg-author">Conseillère Aït Oumdis</div>
                <p class="sp-wa-msg-text">
                    Bonjour et bienvenue ! 🌿<br>
                    Avez-vous besoin d'un conseil pour vos soins bio ou souhaitez-vous passer commande directement ?
                </p>
                <div class="sp-wa-msg-time">{{ date('H:i') }}</div>
            </div>

            <div class="sp-wa-quick-actions">
                <a href="https://wa.me/{{ $waPhone }}?text={{ urlencode('Bonjour, je souhaite commander un soin.') }}" target="_blank" rel="noopener" class="sp-wa-chip">
                    🛍️ Commander un soin
                </a>
                <a href="https://wa.me/{{ $waPhone }}?text={{ urlencode("Bonjour, j'ai besoin d'un conseil beauté pour mes cheveux.") }}" target="_blank" rel="noopener" class="sp-wa-chip">
                    🌿 Conseil Capillaire
                </a>
                <a href="https://wa.me/{{ $waPhone }}?text={{ urlencode('Bonjour, je souhaite des infos sur la crème solaire SunPure.') }}" target="_blank" rel="noopener" class="sp-wa-chip">
                    ☀️ Info SunPure SPF 50+
                </a>
            </div>
        </div>

        <!-- Footer CTA -->
        <div class="sp-wa-footer">
            <a href="https://wa.me/{{ $waPhone }}?text={{ $defaultMsg }}" target="_blank" rel="noopener" class="sp-wa-cta-btn" id="spWaStartChat">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.316 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.818-.981z"/></svg>
                <span>Démarrer la discussion</span>
            </a>
        </div>
    </div>

    <!-- Floating Trigger Button -->
    <div class="sp-wa-trigger-wrapper">
        <span class="sp-wa-pill-label" id="spWaPillLabel">
            <span class="sp-wa-pulsing-badge"></span>
            WhatsApp Direct
        </span>
        <button type="button" class="sp-wa-trigger-btn" id="spWaTriggerBtn" aria-label="Ouvrir WhatsApp" title="Contacter la coopérative sur WhatsApp">
            <svg class="sp-wa-icon" width="28" height="28" viewBox="0 0 24 24" fill="currentColor">
                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.316 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.818-.981z"/>
            </svg>
            <span class="sp-wa-online-ping"></span>
        </button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var triggerBtn = document.getElementById('spWaTriggerBtn');
    var pillLabel = document.getElementById('spWaPillLabel');
    var popup = document.getElementById('spWaPopup');
    var closeBtn = document.getElementById('spWaCloseBtn');

    function toggleWaPopup(e) {
        if (e) e.stopPropagation();
        if (popup) {
            popup.classList.toggle('is-open');
        }
    }

    if (triggerBtn) {
        triggerBtn.addEventListener('click', toggleWaPopup);
    }
    if (pillLabel) {
        pillLabel.addEventListener('click', toggleWaPopup);
    }
    if (closeBtn) {
        closeBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            if (popup) popup.classList.remove('is-open');
        });
    }

    // Close when clicking outside
    document.addEventListener('click', function(e) {
        if (popup && popup.classList.contains('is-open')) {
            if (!popup.contains(e.target) && !triggerBtn.contains(e.target) && (!pillLabel || !pillLabel.contains(e.target))) {
                popup.classList.remove('is-open');
            }
        }
    });
});
</script>
