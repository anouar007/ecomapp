@extends('layouts.frontend')

@section('content')
<div class="bg-surface py-5 min-vh-100">
    <div class="container px-xl-5">
        <div class="row g-4">
            {{-- Sidebar --}}
            <div class="col-lg-3">
                <div class="brand-card border-0 shadow-sm overflow-hidden bg-white sticky-top" style="top: 100px;">
                    <div class="p-4 bg-gold-gradient text-white text-center">
                        <div class="avatar-circle bg-white text-gold rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3 shadow-sm" style="width: 70px; height: 70px; font-family: 'Playfair Display', serif; font-size: 28px; font-weight: bold; border: 2px solid rgba(255,255,255,0.3);">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                        <h6 class="brand-heading h5 mb-0 text-white">{{ auth()->user()->name }}</h6>
                        <small class="opacity-75 font-body" style="font-size: 0.75rem;">{{ auth()->user()->email }}</small>
                    </div>
                    <div class="list-group list-group-flush font-body">
                        <a href="{{ route('customer.dashboard') }}" class="list-group-item list-group-item-action p-3 border-0 d-flex align-items-center gap-3 {{ request()->routeIs('customer.dashboard') ? 'bg-gold-light text-gold fw-bold border-start-gold' : 'text-muted' }}">
                            <i class="fas fa-columns opacity-50"></i> {{ __('Dashboard') }}
                        </a>
                        <a href="{{ route('customer.orders') }}" class="list-group-item list-group-item-action p-3 border-0 d-flex align-items-center gap-3 {{ request()->routeIs('customer.orders*') ? 'bg-gold-light text-gold fw-bold border-start-gold' : 'text-muted' }}">
                            <i class="fas fa-shopping-bag opacity-50"></i> {{ __('My Orders') }}
                        </a>
                        <a href="{{ route('customer.profile') }}" class="list-group-item list-group-item-action p-3 border-0 d-flex align-items-center gap-3 {{ request()->routeIs('customer.profile') ? 'bg-gold-light text-gold fw-bold border-start-gold' : 'text-muted' }}">
                            <i class="fas fa-user-crown opacity-50"></i> {{ __('Account Settings') }}
                        </a>
                        <div class="bg-light mx-3" style="height: 1px;"></div>
                        <form action="{{ route('logout') }}" method="POST" class="m-0">
                            @csrf
                            <button type="submit" class="list-group-item list-group-item-action p-3 border-0 text-danger d-flex align-items-center gap-3 bg-transparent">
                                <i class="fas fa-sign-out-alt opacity-50"></i> {{ __('Logout') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Content --}}
            <div class="col-lg-9">
                @if(session('success'))
                    <div class="alert bg-gold-light text-dark border-gold-subtle alert-dismissible fade show rounded-3 mb-4 font-body" role="alert">
                        <i class="fas fa-check-circle me-2 text-gold"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('dashboard_content')
            </div>
        </div>
    </div>
</div>

{{-- ==========================================
     AI CHATBOT WIDGET (Princess Edition)
     ========================================== --}}
<div id="ai-chat-widget">
    <div id="ai-chat-panel" class="brand-card shadow-lg border-gold-subtle">
        <div class="ai-chat-header bg-gold-gradient text-white">
            <div class="d-flex align-items-center gap-2">
                <div class="ai-avatar bg-white text-gold"><i class="fas fa-crown"></i></div>
                <div>
                    <h6 class="mb-0 brand-heading" style="font-size: 1rem;">مساعدة الأميرات</h6>
                    <small class="opacity-75 font-body" style="font-size: 0.7rem;">نحن هنا لخدمتكِ دائماً</small>
                </div>
            </div>
            <button type="button" class="btn-close btn-close-white" id="ai-chat-close"></button>
        </div>
        
        <div class="ai-chat-body font-body" id="ai-chat-body">
            <div class="ai-msg ai-msg-bot">
                مرحباً بكِ أميرتي {{ auth()->user()->name }}! 👋 أنا مساعدتكِ الشخصية في <span class="font-corsiva">**{{ setting('app_name', 'Hijab Princesses') }}**</span>. كيف يمكنني جعل تجربتكِ أكثر أناقة اليوم؟
            </div>
            
            <div class="ai-chat-suggestions mt-3" id="ai-chat-suggestions">
                <button class="ai-chip" onclick="sendSuggestedMessage('أين هو طلبي؟')">أين هو طلبي؟</button>
                <button class="ai-chip" onclick="sendSuggestedMessage('هل التوصيل مجاني؟')">هل التوصيل مجاني؟</button>
                <button class="ai-chip" onclick="sendSuggestedMessage('كيف أختار مقاسي؟')">كيف أختار مقاسي؟</button>
            </div>
        </div>

        <div class="ai-chat-footer">
            <form id="ai-chat-form" class="m-0 position-relative">
                <input type="text" id="ai-chat-input" class="form-control font-body" placeholder="اكتبي سؤالكِ هنا..." autocomplete="off" required>
                <button type="submit" class="ai-chat-send" id="ai-chat-send-btn">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </form>
        </div>
    </div>

    <button id="ai-chat-toggle" class="btn shadow-lg hvr-grow" title="تحتاجين مساعدة؟">
        <i class="fas fa-comment-dots"></i>
    </button>
</div>

<style>
#ai-chat-widget { position: fixed; bottom: 24px; left: 24px; z-index: 1050; }
#ai-chat-toggle { width: 60px; height: 60px; border-radius: 50%; background: var(--brand-gold); color: white; font-size: 1.5rem; display: flex; align-items: center; justify-content: center; border: none; }
#ai-chat-panel { position: absolute; bottom: 80px; left: 0; width: 350px; height: 500px; background: white; border-radius: 20px; display: flex; flex-direction: column; overflow: hidden; opacity: 0; visibility: hidden; transform: translateY(20px) scale(0.95); transform-origin: bottom left; transition: all 0.3s; }
#ai-chat-panel.active { opacity: 1; visibility: visible; transform: translateY(0) scale(1); }
.ai-chat-header { padding: 15px 20px; display: flex; align-items: center; justify-content: space-between; }
.ai-avatar { width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.9rem; }
.ai-chat-body { flex: 1; padding: 20px; overflow-y: auto; background: var(--bg-surface); display: flex; flex-direction: column; gap: 12px; }
.ai-msg { max-width: 85%; padding: 10px 15px; border-radius: 15px; font-size: 0.85rem; line-height: 1.6; }
.ai-msg-bot { background: white; color: #333; align-self: flex-start; border-bottom-left-radius: 2px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
.ai-msg-user { background: var(--brand-gold); color: white; align-self: flex-end; border-bottom-right-radius: 2px; }
.ai-chip { background: transparent; border: 1px solid var(--brand-gold); color: var(--brand-gold); padding: 5px 12px; border-radius: 20px; font-size: 0.75rem; cursor: pointer; transition: 0.2s; }
.ai-chip:hover { background: var(--brand-gold); color: white; }
.ai-chat-footer { padding: 15px; background: white; border-top: 1px solid #eee; }
#ai-chat-input { border-radius: 24px; padding: 10px 20px; background: #f8f9fa; font-size: 0.85rem; border: 1px solid #ddd; }
.ai-chat-send { position: absolute; left: 20px; top: 50%; transform: translateY(-50%); color: var(--brand-gold); border: none; background: transparent; cursor: pointer; font-size: 1.1rem; }
.border-start-gold { border-left: 4px solid var(--brand-gold) !important; padding-left: 12px !important; }

@media (max-width: 576px) {
    #ai-chat-panel { position: fixed; bottom: 0; left: 0; right: 0; top: 0; width: 100%; height: 100%; border-radius: 0; }
}
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
    
    toggleBtn.addEventListener('click', () => { panel.classList.toggle('active'); input.focus(); });
    closeBtn.addEventListener('click', () => panel.classList.remove('active'));

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const msg = input.value.trim();
        if (!msg) return;
        if (suggestions) suggestions.style.display = 'none';

        const uMsg = document.createElement('div');
        uMsg.className = 'ai-msg ai-msg-user';
        uMsg.textContent = msg;
        body.appendChild(uMsg);
        input.value = '';
        body.scrollTop = body.scrollHeight;

        try {
            const response = await fetch('{{ route('chatbot.ask') }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                body: JSON.stringify({ message: msg })
            });
            const data = await response.json();
            const bMsg = document.createElement('div');
            bMsg.className = 'ai-msg ai-msg-bot';
            bMsg.innerHTML = data.reply || "عذراً أميرتي، حدث خطأ ما. يرجى المحاولة لاحقاً.";
            body.appendChild(bMsg);
            body.scrollTop = body.scrollHeight;
        } catch (e) { console.error(e); }
    });
    
    window.sendSuggestedMessage = (t) => { input.value = t; form.dispatchEvent(new Event('submit')); };
});
</script>
@endsection
