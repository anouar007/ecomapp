<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', __('Dashboard')); ?> - <?php echo e(setting('app_name', 'E-commerce')); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Cairo:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo e(asset('css/dashboard.css')); ?>">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.7.32/sweetalert2.min.css">
    
    <!-- Dynamic Theme CSS Variables -->
    <style>
        :root {
            --primary-color: <?php echo e(setting('primary_color', '#3b82f6')); ?>;
            --secondary-color: <?php echo e(setting('secondary_color', '#10b981')); ?>;
            --accent-color: <?php echo e(setting('accent_color', '#8b5cf6')); ?>;
            --success-color: <?php echo e(setting('success_color', '#10b981')); ?>;
            --warning-color: <?php echo e(setting('warning_color', '#f59e0b')); ?>;
            --danger-color: <?php echo e(setting('danger_color', '#ef4444')); ?>;
            --font-family: <?php echo e(setting('font_family', "'Cairo', 'Inter', system-ui, sans-serif")); ?>;
            --font-size-base: <?php echo e(setting('font_size_base', '14')); ?>px;
            --border-radius: <?php echo e(setting('border_radius', '12')); ?>px;
        }
        
        body {
            font-family: var(--font-family);
            font-size: var(--font-size-base);
        }
    </style>
    
    <link rel="stylesheet" href="<?php echo e(asset('css/driver.css')); ?>">
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body>
    <!-- Mobile Header -->
    <header class="mobile-header d-lg-none">
        <button class="sidebar-toggle" id="mobile-sidebar-toggle">
            <i class="fas fa-bars"></i>
        </button>
        <div class="mobile-brand">
            <?php echo e(setting('app_name', 'E-commerce')); ?>

        </div>
        <div class="mobile-user" id="user-menu-trigger-mobile">
            <?php echo e(strtoupper(substr(auth()->user()->name, 0, 1))); ?>

        </div>
    </header>

    <!-- Navbar (Desktop) -->
    <nav class="navbar d-none d-lg-flex">
        <div class="navbar-container">
            <button onclick="startTour()" class="btn btn-sm btn-outline-primary" style="margin-right: 12px; border-radius: 20px; display: flex; align-items: center; gap: 6px;">
                <i class="fas fa-question-circle"></i>
                <span class="d-none d-md-inline"><?php echo e(__('Help & Guide')); ?></span>
            </button>

            <div class="user-avatar" id="user-menu-trigger">
                <?php echo e(strtoupper(substr(auth()->user()->name, 0, 1))); ?>

            </div>
            <div class="user-dropdown">
                <div class="user-dropdown-header">
                    <div class="user-dropdown-name"><?php echo e(auth()->user()->name); ?></div>
                    <div class="user-dropdown-email"><?php echo e(auth()->user()->email); ?></div>
                </div>
                <a href="<?php echo e(route('profile.show')); ?>" class="user-dropdown-item">
                    <i class="fas fa-user"></i>
                    <?php echo e(__('My Profile')); ?>

                </a>
                <a href="<?php echo e(route('profile.edit')); ?>" class="user-dropdown-item">
                    <i class="fas fa-edit"></i>
                    <?php echo e(__('Edit Profile')); ?>

                </a>
                <form method="POST" action="<?php echo e(route('logout')); ?>" style="margin: 0;">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="user-dropdown-item" style="width: 100%;">
                        <i class="fas fa-sign-out-alt"></i>
                        <?php echo e(__('Logout')); ?>

                    </button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <aside class="sidebar">
        <ul class="sidebar-menu">
            <li class="sidebar-menu-item">
                <a href="<?php echo e(route('dashboard')); ?>" class="sidebar-menu-link active">
                    <i class="fas fa-home"></i>
                    <span><?php echo e(__('Dashboard')); ?></span>
                </a>
            </li>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage_products')): ?>
            <li class="sidebar-menu-item">
                <a href="<?php echo e(route('products.index')); ?>" class="sidebar-menu-link">
                    <i class="fas fa-box"></i>
                    <span><?php echo e(__('Products')); ?></span>
                </a>
            </li>
            <?php endif; ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage_categories')): ?>
            <li class="sidebar-menu-item">
                <a href="<?php echo e(route('categories.index')); ?>" class="sidebar-menu-link">
                    <i class="fas fa-folder-tree"></i>
                    <span><?php echo e(__('Categories')); ?></span>
                </a>
            </li>
            <?php endif; ?>
            

            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage_orders')): ?>
            <li class="sidebar-menu-item">
                <a href="<?php echo e(route('orders.index')); ?>" class="sidebar-menu-link">
                    <i class="fas fa-shopping-cart"></i>
                    <span><?php echo e(__('Orders')); ?></span>
                </a>
            </li>
            <?php endif; ?>


            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage_inventory')): ?>
            <li class="sidebar-menu-item">
                <a href="<?php echo e(route('inventory.index')); ?>" class="sidebar-menu-link">
                    <i class="fas fa-boxes"></i>
                    <span><?php echo e(__('Inventory')); ?></span>
                </a>
            </li>
            <?php endif; ?>

            

            

            







        </ul>
    </aside>
    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebar-overlay"></div>

    <!-- Main Content -->
    <main class="main-content">
        <?php echo $__env->yieldContent('content'); ?>
    </main>

    <!-- SweetAlert2 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.7.32/sweetalert2.all.min.js"></script>
    <!-- SweetAlert Helpers -->
    <script src="<?php echo e(asset('js/sweetalert-helpers.js')); ?>"></script>
    <!-- Delete Confirmation Handler -->
    <script src="<?php echo e(asset('js/delete-confirmation.js')); ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo e(asset('js/app.js')); ?>"></script>
    <script src="<?php echo e(asset('js/driver.js')); ?>"></script>
    <script src="<?php echo e(asset('js/tour.js')); ?>"></script>
    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        <?php if(session('success')): ?>
            Toast.fire({
                icon: 'success',
                title: '<?php echo e(session('success')); ?>'
            });
        <?php endif; ?>

        <?php if(session('error')): ?>
            Toast.fire({
                icon: 'error',
                title: '<?php echo e(session('error')); ?>'
            });
        <?php endif; ?>

        // Mobile Sidebar Toggle
        const mobileToggle = document.getElementById('mobile-sidebar-toggle');
        const sidebar = document.querySelector('.sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        const userMenuMobile = document.getElementById('user-menu-trigger-mobile');
        const userDropdown = document.querySelector('.user-dropdown');

        if (mobileToggle) {
            mobileToggle.addEventListener('click', () => {
                sidebar.classList.add('show');
                overlay.classList.add('show');
            });
        }

        if (overlay) {
            overlay.addEventListener('click', () => {
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
            });
        }

        if (userMenuMobile) {
            userMenuMobile.addEventListener('click', (e) => {
                e.stopPropagation();
                userDropdown.classList.toggle('active');
            });
        }
    </script>
    <?php echo $__env->yieldPushContent('scripts'); ?>

<!-- ==========================================
     AI CHATBOT WIDGET (Admin)
     ========================================== -->
<div id="ai-chat-widget">
    <!-- Chat Panel -->
    <div id="ai-chat-panel" class="shadow-lg">
        <div class="ai-chat-header text-white" style="background: var(--primary-color, #3b82f6);">
            <div class="d-flex align-items-center gap-2">
                <div class="ai-avatar"><i class="fas fa-robot"></i></div>
                <div>
                    <h6 class="mb-0 fw-bold"><?php echo e(__('Assistant IA')); ?></h6>
                    <small class="opacity-75" style="font-size: 0.75rem;"><?php echo e(__('Toujours disponible')); ?></small>
                </div>
            </div>
            <button type="button" class="btn-close btn-close-white" id="ai-chat-close"></button>
        </div>

        <div class="ai-chat-body" id="ai-chat-body">
            <div class="ai-msg ai-msg-bot">
                <?php echo e(__('Bonjour')); ?> <?php echo e(auth()->user()->name); ?> ! 🤖 <?php echo e(__("Je suis votre assistant IA. Je peux vous aider à utiliser le tableau de bord, gérer les commandes, les produits, les clients, et répondre à toute question sur l'application. Comment puis-je vous aider ?")); ?>

            </div>
            <div class="ai-chat-suggestions mt-3" id="ai-chat-suggestions">
                <button class="ai-chip" onclick="sendSuggestedMessage('<?php echo e(__('Comment ajouter un produit ?')); ?>')"><?php echo e(__('Comment ajouter un produit ?')); ?></button>
                <button class="ai-chip" onclick="sendSuggestedMessage('<?php echo e(__('Créer un coupon')); ?>')"><?php echo e(__('Créer un coupon')); ?></button>
                <button class="ai-chip" onclick="sendSuggestedMessage('<?php echo e(__('Gérer les commandes')); ?>')"><?php echo e(__('Gérer les commandes')); ?></button>
            </div>
        </div>

        <div class="ai-chat-footer">
                <input type="text" id="ai-chat-input" class="form-control" placeholder="<?php echo e(__('Posez votre question...')); ?>" autocomplete="off" required>
                    <i class="fas fa-paper-plane"></i>
                </button>
            </form>
        </div>
    </div>

    <!-- Floating Toggle Button -->
    <button id="ai-chat-toggle" class="btn shadow-lg" style="background: var(--primary-color, #3b82f6);" title="<?php echo e(__('Assistant IA')); ?>">
        <i class="fas fa-comment-dots"></i>
    </button>
</div>

<style>
#ai-chat-widget {
    position: fixed;
    bottom: 24px;
    left: 24px;
    z-index: 9999;
    font-family: var(--font-family, system-ui, -apple-system, sans-serif);
}
#ai-chat-toggle {
    width: 58px; height: 58px;
    border-radius: 50%;
    color: white;
    font-size: 1.4rem;
    display: flex; align-items: center; justify-content: center;
    border: none;
    transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275), box-shadow .2s;
}
#ai-chat-toggle:hover { transform: scale(1.1); color: white; box-shadow: 0 8px 24px rgba(59,130,246,.45) !important; }
#ai-chat-panel {
    position: absolute;
    bottom: 74px; left: 0;
    width: 360px; height: 500px;
    max-height: calc(100vh - 110px);
    background: white;
    border-radius: 20px;
    display: flex; flex-direction: column;
    overflow: hidden;
    opacity: 0; visibility: hidden;
    transform: translateY(20px) scale(0.95);
    transform-origin: bottom left;
    transition: all 0.3s cubic-bezier(0.19, 1, 0.22, 1);
}
#ai-chat-panel.active { opacity: 1; visibility: visible; transform: translateY(0) scale(1); }
@media (max-width: 576px) {
    #ai-chat-panel { position: fixed; bottom: 0; right: 0; left: 0; top: 0; width: 100%; height: 100%; max-height: 100vh; border-radius: 0; }
    #ai-chat-widget { bottom: 16px; right: 16px; }
}
.ai-chat-header { padding: 16px 20px; display: flex; align-items: center; justify-content: space-between; }
.ai-avatar { width: 40px; height: 40px; background: rgba(255,255,255,.22); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; }
.ai-chat-body { flex: 1; padding: 20px; overflow-y: auto; background: #f4f6f9; display: flex; flex-direction: column; gap: 12px; }
.ai-msg { max-width: 85%; padding: 12px 16px; border-radius: 16px; font-size: 0.88rem; line-height: 1.55; animation: aiMsgIn .3s ease; word-wrap: break-word; }
.ai-msg-bot { background: white; color: #1e293b; align-self: flex-start; border-bottom-left-radius: 4px; box-shadow: 0 2px 6px rgba(0,0,0,.06); }
.ai-msg-bot strong { font-weight: 700; color: var(--primary-color, #3b82f6); }
.ai-msg-user { background: var(--primary-color, #3b82f6); color: white; align-self: flex-end; border-bottom-left-radius: 4px; }
.ai-chat-suggestions { display: flex; flex-wrap: wrap; gap: 7px; }
.ai-chip { background: transparent; border: 1.5px solid var(--primary-color, #3b82f6); color: var(--primary-color, #3b82f6); padding: 5px 13px; border-radius: 20px; font-size: 0.78rem; cursor: pointer; transition: all .2s; }
.ai-chip:hover { background: var(--primary-color, #3b82f6); color: white; }
.ai-chat-footer { padding: 14px; background: white; border-top: 1px solid #e8eaf0; }
#ai-chat-input { border-radius: 24px; padding: 11px 46px 11px 18px; border: 1.5px solid #dde1ea; background: #f4f6f9; font-size: 0.88rem; box-shadow: none !important; }
#ai-chat-input:focus { border-color: var(--primary-color, #3b82f6); background: white; }
.ai-chat-send { position: absolute; left: 6px; top: 50%; transform: translateY(-50%); width: 34px; height: 34px; border-radius: 50%; background: var(--primary-color, #3b82f6); color: white; border: none; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: opacity .2s; }
.ai-chat-send:hover { opacity: .85; }
.ai-chat-send:disabled { background: #c5cad3; cursor: not-allowed; }
.typing-indicator { display: flex; align-items: center; gap: 4px; padding: 12px 16px; background: white; border-radius: 16px; border-bottom-left-radius: 4px; align-self: flex-start; box-shadow: 0 2px 6px rgba(0,0,0,.06); }
.typing-dot { width: 6px; height: 6px; background: #94a3b8; border-radius: 50%; animation: typingBounce 1.4s infinite ease-in-out both; }
.typing-dot:nth-child(1) { animation-delay: -.32s; }
.typing-dot:nth-child(2) { animation-delay: -.16s; }
@keyframes aiMsgIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
@keyframes typingBounce { 0%, 80%, 100% { transform: scale(0); } 40% { transform: scale(1); } }
</style>

<script>
(function() {
    const toggleBtn = document.getElementById('ai-chat-toggle');
    const closeBtn  = document.getElementById('ai-chat-close');
    const panel     = document.getElementById('ai-chat-panel');
    const form      = document.getElementById('ai-chat-form');
    const input     = document.getElementById('ai-chat-input');
    const body      = document.getElementById('ai-chat-body');
    const suggestEl = document.getElementById('ai-chat-suggestions');
    const sendBtn   = document.getElementById('ai-chat-send-btn');

    const toggleChat = () => {
        panel.classList.toggle('active');
        if (panel.classList.contains('active')) setTimeout(() => input.focus(), 300);
    };
    toggleBtn.addEventListener('click', toggleChat);
    closeBtn.addEventListener('click', toggleChat);

    const fmt = (text) => {
        let h = text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
        h = h.replace(/(https?:\/\/[^\s]+)/g, '<a href="$1" target="_blank">$1</a>');
        return h.replace(/\n/g, '<br>');
    };
    const scrollBot = () => { body.scrollTop = body.scrollHeight; };
    const appendMsg = (text, sender) => {
        const d = document.createElement('div');
        d.className = `ai-msg ai-msg-${sender}`;
        d.innerHTML = sender === 'bot' ? fmt(text) : text;
        body.insertBefore(d, suggestEl);
        scrollBot();
    };
    const showTyping = () => {
        const d = document.createElement('div');
        d.className = 'typing-indicator'; d.id = 'ai-typing';
        d.innerHTML = '<div class="typing-dot"></div><div class="typing-dot"></div><div class="typing-dot"></div>';
        body.insertBefore(d, suggestEl); scrollBot();
    };
    const hideTyping = () => { const el = document.getElementById('ai-typing'); if (el) el.remove(); };

    window.sendSuggestedMessage = (text) => { input.value = text; form.dispatchEvent(new Event('submit')); };

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const msg = input.value.trim();
        if (!msg) return;
        if (suggestEl) suggestEl.style.display = 'none';
        appendMsg(msg, 'user');
        input.value = ''; input.disabled = true;
        sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        showTyping();
        try {
            const res = await fetch('<?php echo e(route('chatbot.ask')); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ message: msg })
            });
            const data = await res.json();
            hideTyping();
            appendMsg(data.reply || "<?php echo e(__('Erreur de connexion. Veuillez réessayer.')); ?>", 'bot');
        } catch (err) {
            hideTyping();
            appendMsg("<?php echo e(__('Impossible de joindre le serveur. Réessayez plus tard.')); ?>", 'bot');
        } finally {
            input.disabled = false;
            sendBtn.innerHTML = '<i class="fas fa-paper-plane"></i>';
            input.focus();
        }
    });
})();
</script>
</body>
</html>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/speed/resources/views/layouts/app.blade.php ENDPATH**/ ?>