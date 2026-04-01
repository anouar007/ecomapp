<!-- Newsletter Popup Overlay -->
<div id="newsletter-popup" class="newsletter-overlay" style="display: none;">
    <div class="newsletter-content shadow-lg rounded-4 overflow-hidden border">
        <button type="button" class="close-popup" onclick="closeNewsletter()">&times;</button>
        <div class="row g-0">
            <div class="col-md-5 d-none d-md-block" style="background: url('{{ asset('images/newsletter-bg.jpg') }}') center/cover;">
            </div>
            <div class="col-md-7 p-4 p-lg-5 text-center bg-white">
                <div class="mb-3">
                    <img src="{{ asset('storage/' . setting('app_logo')) }}" alt="Logo" style="height: 30px;" class="mb-3">
                </div>
                <h3 class="fw-black mb-2">{{ __('The Artisanal Club') }}</h3>
                <p class="text-muted small mb-4">
                    {{ __('Join our inner circle for exclusive early access to unique handcrafted collections and artisanal stories.') }}
                </p>
                
                <form id="popup-subscribe-form" onsubmit="submitNewsletter(event)">
                    @csrf
                    <div class="mb-3 text-start">
                        <input type="email" name="email" class="form-control py-3 rounded-pill bg-light border-0 px-4" 
                               placeholder="{{ __('Enter your email address') }}" required>
                    </div>
                    <button type="submit" class="btn btn-dark w-100 py-3 rounded-pill fw-black text-uppercase ls-1">
                        {{ __('Join Now') }}
                    </button>
                    <div class="mt-3 text-muted" style="font-size: 10px;">
                        *{{ __('By joining, you agree to receive our periodic craftsmanship updates.') }}
                    </div>
                </form>

                <div id="popup-success-msg" class="mt-4 text-success fw-bold" style="display: none;">
                    <i class="fas fa-check-circle me-1"></i> {{ __('Welcome to the Club!') }}
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .newsletter-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.6);
        background-backdrop-filter: blur(5px);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    }
    .newsletter-content {
        max-width: 800px;
        width: 90%;
        position: relative;
        animation: slideUp 0.5s ease-out;
    }
    .close-popup {
        position: absolute;
        top: 10px;
        right: 15px;
        background: none;
        border: none;
        font-size: 24px;
        color: #999;
        cursor: pointer;
        z-index: 10;
        transition: color 0.3s;
    }
    .close-popup:hover { color: #000; }
    
    @keyframes slideUp {
        from { transform: translateY(50px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    @media (max-width: 767px) {
        .newsletter-content { width: 95%; max-width: 400px; }
    }
</style>

<script>
    function closeNewsletter() {
        document.getElementById('newsletter-popup').style.display = 'none';
        localStorage.setItem('newsletter_dismissed', Date.now());
    }

    function submitNewsletter(e) {
        e.preventDefault();
        const form = e.target;
        const btn = form.querySelector('button');
        const email = form.email.value;
        
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

        fetch('{{ route("newsletter.subscribe") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ email: email })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                form.style.display = 'none';
                document.getElementById('popup-success-msg').style.display = 'block';
                localStorage.setItem('newsletter_subscribed', Date.now());
                setTimeout(closeNewsletter, 3000);
            } else {
                Swal.fire({ icon: 'error', text: data.message || 'Error subscribing' });
                btn.disabled = false;
                btn.innerHTML = 'Join Now';
            }
        })
        .catch(err => {
            console.error(err);
            btn.disabled = false;
            btn.innerHTML = 'Join Now';
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        const lastDismissed = localStorage.getItem('newsletter_dismissed');
        const lastSubscribed = localStorage.getItem('newsletter_subscribed');
        const now = Date.now();
        const thirtyDays = 30 * 24 * 60 * 60 * 1000;

        if (lastSubscribed) return;
        if (lastDismissed && (now - lastDismissed < thirtyDays)) return;

        // Show after 10 seconds or on mouse exit (exit-intent)
        let shown = false;
        const showPopup = () => {
            if (shown) return;
            document.getElementById('newsletter-popup').style.display = 'flex';
            shown = true;
        };

        setTimeout(showPopup, 10000);

        document.addEventListener('mouseleave', (e) => {
            if (e.clientY < 0) showPopup();
        });
    });
</script>
