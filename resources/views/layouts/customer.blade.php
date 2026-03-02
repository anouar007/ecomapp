@extends('layouts.frontend')

@section('content')
<div class="bg-light py-5">
    <div class="container">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-3 mb-4">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-body p-0">
                        <div class="p-4 bg-primary text-white text-center">
                            <div class="avatar-circle bg-white text-primary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 64px; height: 64px; font-size: 24px; font-weight: bold;">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>
                            <h6 class="fw-bold mb-0 text-white">{{ auth()->user()->name }}</h6>
                            <small class="opacity-75">{{ auth()->user()->email }}</small>
                        </div>
                        <div class="list-group list-group-flush">
                            <a href="{{ route('customer.dashboard') }}" class="list-group-item list-group-item-action p-3 {{ request()->routeIs('customer.dashboard') ? 'active fw-bold' : '' }}">
                                <i class="fas fa-home me-2 opacity-50"></i> Tableau de bord
                            </a>
                            <a href="{{ route('customer.orders') }}" class="list-group-item list-group-item-action p-3 {{ request()->routeIs('customer.orders*') ? 'active fw-bold' : '' }}">
                                <i class="fas fa-shopping-bag me-2 opacity-50"></i> Mes commandes
                            </a>
                            <a href="{{ route('customer.profile') }}" class="list-group-item list-group-item-action p-3 {{ request()->routeIs('customer.profile') ? 'active fw-bold' : '' }}">
                                <i class="fas fa-user-cog me-2 opacity-50"></i> Paramètres du profil
                            </a>
                            <form action="{{ route('logout') }}" method="POST" class="border-top">
                                @csrf
                                <button type="submit" class="list-group-item list-group-item-action p-3 text-danger">
                                    <i class="fas fa-sign-out-alt me-2 opacity-50"></i> Déconnexion
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="col-lg-9">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('dashboard_content')
            </div>
        </div>
    </div>
</div>

<!-- ==========================================
     AI CHATBOT WIDGET
     ========================================== -->
<div id="ai-chat-widget">
    <!-- Chat Panel -->
    <div id="ai-chat-panel" class="shadow-lg">
        <div class="ai-chat-header bg-primary text-white">
            <div class="d-flex align-items-center gap-2">
                <div class="ai-avatar"><i class="fas fa-robot"></i></div>
                <div>
                    <h6 class="mb-0 fw-bold">Assistant IA</h6>
                    <small class="opacity-75" style="font-size: 0.75rem;">Toujours en ligne</small>
                </div>
            </div>
            <button type="button" class="btn-close btn-close-white" id="ai-chat-close"></button>
        </div>
        
        <div class="ai-chat-body" id="ai-chat-body">
            <div class="ai-msg ai-msg-bot">
                Bonjour {{ auth()->user()->name }} ! 👋 Je suis l'assistant virtuel de {{ setting('app_name', 'notre boutique') }}. Je peux répondre à vos questions sur nos équipements d'impression, livraisons, commandes, ou votre espace client. Comment puis-je vous aider ?
            </div>
            
            <!-- Suggested chips (fade out after first message) -->
            <div class="ai-chat-suggestions mt-3" id="ai-chat-suggestions">
                <button class="ai-chip" onclick="sendSuggestedMessage('Où est ma commande ?')">Où est ma commande ?</button>
                <button class="ai-chip" onclick="sendSuggestedMessage('Livrez-vous partout au Maroc ?')">Livrez-vous partout au Maroc ?</button>
                <button class="ai-chip" onclick="sendSuggestedMessage('La formation est-elle incluse ?')">La formation est-elle incluse ?</button>
            </div>
        </div>

        <div class="ai-chat-footer">
            <form id="ai-chat-form" class="m-0 position-relative">
                <input type="text" id="ai-chat-input" class="form-control" placeholder="Posez votre question..." autocomplete="off" required>
                <button type="submit" class="ai-chat-send" id="ai-chat-send-btn">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </form>
        </div>
    </div>

    <!-- Floating Toggle Button -->
    <button id="ai-chat-toggle" class="btn shadow-lg" title="Besoin d'aide ?">
        <i class="fas fa-comment-dots"></i>
    </button>
</div>

<style>
#ai-chat-widget {
    position: fixed;
    bottom: 24px;
    right: 24px;
    z-index: 1050;
    font-family: var(--font-body, system-ui, -apple-system, sans-serif);
}

/* Toggle Button */
#ai-chat-toggle {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: var(--accent, #e94560);
    color: white;
    font-size: 1.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    border: none;
    transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}
#ai-chat-toggle:hover { transform: scale(1.1); color: white; }

/* Panel */
#ai-chat-panel {
    position: absolute;
    bottom: 80px;
    right: 0;
    width: 360px;
    height: 500px;
    max-height: calc(100vh - 120px);
    background: white;
    border-radius: 20px;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    opacity: 0;
    visibility: hidden;
    transform: translateY(20px) scale(0.95);
    transform-origin: bottom right;
    transition: all 0.3s cubic-bezier(0.19, 1, 0.22, 1);
}
#ai-chat-panel.active {
    opacity: 1;
    visibility: visible;
    transform: translateY(0) scale(1);
}
/* Mobile tweak */
@media (max-width: 576px) {
    #ai-chat-panel {
        position: fixed;
        bottom: 0; right: 0; left: 0; top: 0;
        width: 100%; height: 100%; max-height: 100vh;
        border-radius: 0;
    }
    #ai-chat-widget { bottom: 16px; right: 16px; }
}

/* Header */
.ai-chat-header {
    padding: 16px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.ai-avatar {
    width: 40px; height: 40px;
    background: rgba(255,255,255,0.2);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.2rem;
}

/* Body */
.ai-chat-body {
    flex: 1;
    padding: 20px;
    overflow-y: auto;
    background: #f8f9fa;
    display: flex;
    flex-direction: column;
    gap: 12px;
}
.ai-msg {
    max-width: 85%;
    padding: 12px 16px;
    border-radius: 16px;
    font-size: 0.9rem;
    line-height: 1.5;
    animation: fadeIn 0.3s ease;
    word-wrap: break-word;
}
.ai-msg-bot {
    background: white;
    color: #333;
    align-self: flex-start;
    border-bottom-left-radius: 4px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
}
.ai-msg-bot strong { font-weight: 700; color: var(--primary); }
.ai-msg-user {
    background: var(--primary, #0f172a);
    color: white;
    align-self: flex-end;
    border-bottom-right-radius: 4px;
}

/* Suggestions */
.ai-chat-suggestions {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    transition: opacity 0.3s;
}
.ai-chip {
    background: transparent;
    border: 1px solid var(--accent, #e94560);
    color: var(--accent, #e94560);
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 0.8rem;
    cursor: pointer;
    transition: all 0.2s;
}
.ai-chip:hover {
    background: var(--accent, #e94560);
    color: white;
}

/* Footer & Input */
.ai-chat-footer {
    padding: 16px;
    background: white;
    border-top: 1px solid #eee;
}
#ai-chat-input {
    border-radius: 24px;
    padding: 12px 48px 12px 20px;
    border: 1px solid #ddd;
    background: #f8f9fa;
    font-size: 0.9rem;
    box-shadow: none !important;
}
#ai-chat-input:focus { border-color: var(--accent, #e94560); background: white; }
.ai-chat-send {
    position: absolute;
    right: 6px;
    top: 50%;
    transform: translateY(-50%);
    width: 36px; height: 36px;
    border-radius: 50%;
    background: var(--accent, #e94560);
    color: white;
    border: none;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer;
    transition: background 0.2s;
}
.ai-chat-send:hover { background: #d03d55; }
.ai-chat-send:disabled { background: #ccc; cursor: not-allowed; }

/* Typing Indicator */
.typing-indicator {
    display: flex; align-items: center; gap: 4px;
    padding: 12px 16px;
    background: white;
    border-radius: 16px;
    border-bottom-left-radius: 4px;
    align-self: flex-start;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    margin-bottom: 8px;
}
.typing-dot {
    width: 6px; height: 6px;
    background: #bbb;
    border-radius: 50%;
    animation: typingBounce 1.4s infinite ease-in-out both;
}
.typing-dot:nth-child(1) { animation-delay: -0.32s; }
.typing-dot:nth-child(2) { animation-delay: -0.16s; }

@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
@keyframes typingBounce { 0%, 80%, 100% { transform: scale(0); } 40% { transform: scale(1); } }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggleBtn = document.getElementById('ai-chat-toggle');
    const closeBtn = document.getElementById('ai-chat-close');
    const panel = document.getElementById('ai-chat-panel');
    const form = document.getElementById('ai-chat-form');
    const input = document.getElementById('ai-chat-input');
    const body = document.getElementById('ai-chat-body');
    const suggestions = document.getElementById('ai-chat-suggestions');
    const sendBtn = document.getElementById('ai-chat-send-btn');
    
    // Toggle widget
    const toggleChat = () => {
        panel.classList.toggle('active');
        if (panel.classList.contains('active')) {
            setTimeout(() => input.focus(), 300);
        }
    };
    toggleBtn.addEventListener('click', toggleChat);
    closeBtn.addEventListener('click', toggleChat);

    // Auto HTML link parser
    const formatBotMessage = (text) => {
        // Convert bold to strong
        let html = text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
        // Convert URLs to clickable links
        html = html.replace(/(https?:\/\/[^\s]+)/g, '<a href="$1" target="_blank" class="text-accent">$1</a>');
        // Convert newlines to br
        return html.replace(/\n/g, '<br>');
    };

    // Scroll to bottom
    const scrollToBottom = () => {
        body.scrollTop = body.scrollHeight;
    };

    const appendMessage = (text, sender) => {
        const div = document.createElement('div');
        div.className = `ai-msg ai-msg-${sender}`;
        div.innerHTML = sender === 'bot' ? formatBotMessage(text) : text;
        body.insertBefore(div, suggestions); // Insert before suggestions so they stay at bottom if visible
        scrollToBottom();
    };

    const showTyping = () => {
        const div = document.createElement('div');
        div.className = 'typing-indicator';
        div.id = 'ai-typing';
        div.innerHTML = '<div class="typing-dot"></div><div class="typing-dot"></div><div class="typing-dot"></div>';
        body.insertBefore(div, suggestions);
        scrollToBottom();
    };

    const hideTyping = () => {
        const indicator = document.getElementById('ai-typing');
        if (indicator) indicator.remove();
    };

    // Export for chips
    window.sendSuggestedMessage = (text) => {
        input.value = text;
        form.dispatchEvent(new Event('submit'));
    };

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const msg = input.value.trim();
        if (!msg) return;

        // Hide suggestions after first message
        if (suggestions) suggestions.style.display = 'none';

        // Add user msg
        appendMessage(msg, 'user');
        input.value = '';
        input.disabled = true;
        sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

        showTyping();

        try {
            const response = await fetch('{{ route('chatbot.ask') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ message: msg })
            });

            const data = await response.json();
            hideTyping();
            
            if (response.ok && data.reply) {
                appendMessage(data.reply, 'bot');
            } else {
                appendMessage(data.reply || "Erreur de connexion. Veuillez réessayer.", 'bot');
            }
        } catch (error) {
            hideTyping();
            appendMessage("Désolé, je ne parviens pas à joindre le serveur. Veuillez réessayer plus tard.", 'bot');
        } finally {
            input.disabled = false;
            sendBtn.innerHTML = '<i class="fas fa-paper-plane"></i>';
            input.focus();
        }
    });
});
</script>
@endsection
