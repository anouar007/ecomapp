@extends('layouts.frontend')

@section('meta_title', 'تأكيد الطلب — ' . setting('app_name', 'Hijab Princesses'))

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<style>
/* ── Checkout Mobile-First Layout ────────────────── */
.checkout-page {
    background: linear-gradient(135deg, #f8f9fc 0%, #eef2ff 100%);
    min-height: 100vh;
    padding: 1.5rem 0 3rem;
}

.checkout-wrapper {
    max-width: 540px;
    margin: 0 auto;
    padding: 0 1rem;
}

/* ── Page Header ─────────────────────────────────── */
.checkout-header {
    text-align: center;
    margin-bottom: 2rem;
}
.checkout-header h1 {
    font-size: 1.6rem;
    font-weight: 900;
    color: #1e293b;
    margin-bottom: 0.25rem;
}
.checkout-header p {
    font-size: 0.875rem;
    color: #94a3b8;
}

/* ── Order Summary Card ───────────────────────────── */
.summary-card {
    background: #fff;
    border-radius: 1.25rem;
    box-shadow: 0 4px 24px rgba(0,0,0,0.07);
    overflow: hidden;
    margin-bottom: 1.25rem;
}
.summary-header {
    background: linear-gradient(135deg, #c5a059, #a07840);
    color: #fff;
    padding: 1rem 1.25rem;
    font-weight: 800;
    font-size: 0.95rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.summary-body {
    padding: 1.25rem;
}
.cart-row {
    display: flex;
    align-items: center;
    gap: 0.875rem;
    padding: 0.75rem 0;
    border-bottom: 1px solid #f1f5f9;
}
.cart-row:last-child { border-bottom: none; }
.cart-img-wrap {
    position: relative;
    flex-shrink: 0;
}
.cart-img {
    width: 80px;
    height: 120px;
    object-fit: cover;
    border-radius: 0.75rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.cart-img-placeholder {
    width: 60px;
    height: 75px;
    background: #f1f5f9;
    border-radius: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #cbd5e1;
    font-size: 1.25rem;
}
.cart-qty-badge {
    position: absolute;
    top: -6px;
    right: -6px;
    width: 20px;
    height: 20px;
    background: #c5a059;
    color: #fff;
    border-radius: 50%;
    font-size: 0.7rem;
    font-weight: 800;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid #fff;
}
.cart-info { flex: 1; min-width: 0; }
.cart-name {
    font-weight: 700;
    font-size: 0.9rem;
    color: #1e293b;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.cart-variants {
    display: flex;
    gap: 0.375rem;
    flex-wrap: wrap;
    margin-top: 0.375rem;
}
.variant-tag {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    color: #64748b;
    font-size: 0.7rem;
    font-weight: 600;
    padding: 2px 8px;
    border-radius: 100px;
}
.cart-price {
    font-weight: 800;
    color: #c5a059;
    font-size: 0.95rem;
    flex-shrink: 0;
}

/* ── Totals ──────────────────────────────────────── */
.totals-section {
    border-top: 2px dashed #e2e8f0;
    padding-top: 1rem;
    margin-top: 1rem;
}
.totals-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
    color: #64748b;
}
.totals-row.grand {
    font-size: 1.1rem;
    font-weight: 900;
    color: #1e293b;
    border-top: 1px solid #e2e8f0;
    padding-top: 0.75rem;
    margin-top: 0.5rem;
}
.totals-row.grand .val { color: #c5a059; }

/* ── Form Card ───────────────────────────────────── */
.form-card {
    background: #fff;
    border-radius: 1.25rem;
    box-shadow: 0 4px 24px rgba(0,0,0,0.07);
    overflow: hidden;
    margin-bottom: 1.25rem;
}
.form-header {
    background: #f8fafc;
    padding: 1rem 1.25rem;
    font-weight: 800;
    font-size: 0.95rem;
    color: #1e293b;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    border-bottom: 1px solid #f1f5f9;
}
.form-body { padding: 1.25rem; }

.field-group { margin-bottom: 1.25rem; }
.field-label {
    display: block;
    font-size: 0.8rem;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 0.5rem;
}
.field-input {
    width: 100%;
    background: #f8fafc;
    border: 1.5px solid #e2e8f0;
    border-radius: 0.875rem;
    padding: 0.875rem 1rem;
    font-size: 1rem;
    color: #1e293b;
    transition: border-color 0.2s, box-shadow 0.2s;
    outline: none;
    font-family: inherit;
}
.field-input:focus {
    border-color: #c5a059;
    box-shadow: 0 0 0 3px rgba(197,160,89,0.15);
    background: #fff;
}
.field-input::placeholder { color: #cbd5e1; }

/* TomSelect overrides */
.ts-wrapper { width: 100%; }
.ts-control {
    background: #f8fafc !important;
    border: 1.5px solid #e2e8f0 !important;
    border-radius: 0.875rem !important;
    padding: 0.875rem 1rem !important;
    font-size: 1rem !important;
    min-height: unset !important;
    box-shadow: none !important;
    cursor: pointer;
}
.ts-control:focus-within,
.ts-wrapper.focus .ts-control {
    border-color: #c5a059 !important;
    box-shadow: 0 0 0 3px rgba(197,160,89,0.15) !important;
    background: #fff !important;
}
.ts-control input { font-size: 1rem !important; }
.ts-dropdown {
    border-radius: 0.875rem !important;
    box-shadow: 0 10px 40px rgba(0,0,0,0.12) !important;
    border: 1px solid #e2e8f0 !important;
    overflow: hidden;
}
.ts-dropdown .option { padding: 0.75rem 1rem; font-size: 0.9rem; }
.ts-dropdown .option.active { background: rgba(197,160,89,0.12) !important; color: #a07840 !important; }
.ts-dropdown .option:hover { background: #fdf8f0 !important; }

/* ── Payment Method ──────────────────────────────── */
.payment-card {
    background: linear-gradient(135deg, #fff9f0, #fff);
    border: 1.5px solid #f0e0c0;
    border-radius: 1.25rem;
    padding: 1.25rem;
    text-align: center;
    margin-bottom: 1.25rem;
}
.payment-icon {
    width: 56px; height: 56px;
    background: linear-gradient(135deg, #c5a059, #a07840);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-size: 1.25rem;
    margin: 0 auto 0.75rem;
}
.payment-label {
    font-weight: 800; font-size: 1rem; color: #1e293b;
    margin-bottom: 0.25rem;
}
.payment-note { font-size: 0.8rem; color: #94a3b8; }

/* ── Submit Button ───────────────────────────────── */
.btn-checkout {
    width: 100%;
    background: linear-gradient(135deg, #c5a059, #a07840);
    color: #fff;
    border: none;
    border-radius: 1rem;
    padding: 1.25rem;
    font-size: 1.1rem;
    font-weight: 800;
    letter-spacing: 0.02em;
    cursor: pointer;
    transition: transform 0.15s, box-shadow 0.15s;
    box-shadow: 0 4px 20px rgba(197,160,89,0.4);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    margin-bottom: 1rem;
}
.btn-checkout:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 28px rgba(197,160,89,0.45);
}
.btn-checkout:active { transform: translateY(0); }

.back-link {
    display: block;
    text-align: center;
    font-size: 0.875rem;
    color: #94a3b8;
    text-decoration: none;
}
.back-link:hover { color: #64748b; }

/* ── Trust Pills ─────────────────────────────────── */
.trust-row {
    display: flex;
    justify-content: center;
    gap: 1.5rem;
    margin-top: 1.5rem;
    padding-top: 1.5rem;
    border-top: 1px solid #e2e8f0;
}
.trust-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.375rem;
    color: #94a3b8;
    font-size: 0.75rem;
    font-weight: 600;
}
.trust-item i { font-size: 1.25rem; color: #c5a059; }

.checkout-grid {
    display: flex;
    flex-direction: column-reverse; /* Cart before Form on Mobile */
    gap: 1.5rem;
}

/* ── Desktop: show order summary beside form ─────── */
@media (min-width: 992px) {
    .checkout-page { padding: 3rem 0 4rem; }
    .checkout-wrapper { max-width: 1100px; }
    .checkout-header h1 { font-size: 2.2rem; }
    
    .checkout-grid {
        display: grid;
        grid-template-columns: 1fr 380px;
        gap: 2.5rem;
        align-items: start;
    }
}
</style>
@endpush

@section('content')
<div class="checkout-page">
    <div class="checkout-wrapper">

        {{-- Header --}}
        <div class="checkout-header">
            <h1>تأكيد الطلب</h1>
            <p class="mb-2">شحن سريع لجميع مدن المغرب 🇲🇦</p>
            <p class="small text-gold fw-bold mb-0"><i class="fas fa-info-circle me-1"></i> التوصيل: من 20 د.م. إلى 40 د.م. حسب المدينة</p>
        </div>

        <div class="checkout-grid">
            <div class="checkout-main">
                {{-- Delivery Info Form --}}
                <div class="form-card">
                    <div class="form-header">
                        <i class="fas fa-map-marker-alt text-primary"></i> معلومات التوصيل
                    </div>
                    <div class="form-body">
                        <form action="{{ route('checkout.store') }}" method="POST" id="checkout-form">
                            @csrf

                            <div class="field-group">
                                <label class="field-label" for="customer_name">الاسم الكامل</label>
                                <input type="text" id="customer_name" name="customer_name"
                                       class="field-input" placeholder="مثال: فاطمة الزهراء" required
                                       value="{{ old('customer_name') }}">
                            </div>

                            <div class="field-group">
                                <label class="field-label" for="customer_phone">رقم الهاتف</label>
                                <input type="tel" id="customer_phone" name="customer_phone"
                                       class="field-input" placeholder="06 XX XX XX XX" required
                                       value="{{ old('customer_phone') }}">
                            </div>

                            <div class="field-group">
                                <label class="field-label" for="shipping_address">العنوان</label>
                                <input type="text" id="shipping_address" name="shipping_address"
                                       class="field-input" placeholder="الحي، الشارع، رقم المنزل..." required
                                       value="{{ old('shipping_address') }}">
                            </div>

                            <div class="field-group">
                                <label class="field-label" for="city-select">المدينة</label>
                                <select name="shipping_city" id="city-select" required>
                                    <option value="">ابحثي عن مدينتك...</option>
                                </select>
                            </div>
                        </form>

                        <button type="submit" form="checkout-form" class="btn-checkout">
                            <i class="fas fa-check-circle"></i> تأكيد الطلب الآن
                        </button>

                        <a href="{{ route('cart.index') }}" class="back-link">
                            <i class="fas fa-arrow-right me-1"></i> العودة للسلة
                        </a>
                    </div>
                </div>
            </div>

            <div class="checkout-side">
                {{-- Order Summary --}}
                <div class="summary-card">
                    <div class="summary-header">
                        <i class="fas fa-shopping-bag"></i> ملخص الطلب
                    </div>
                    <div class="summary-body">
                        @foreach($cart as $key => $details)
                        <div class="cart-row">
                            <div class="cart-img-wrap">
                                @if($details['image'])
                                    <img src="{{ Storage::url($details['image']) }}" alt="{{ $details['name'] }}" class="cart-img">
                                @else
                                    <div class="cart-img-placeholder"><i class="fas fa-image"></i></div>
                                @endif
                                <div class="cart-qty-badge">{{ $details['quantity'] }}</div>
                            </div>
                            <div class="cart-info">
                                <div class="cart-name">{{ $details['name'] }}</div>
                                <div class="cart-variants d-flex align-items-center gap-2 mt-1">
                                    @if($details['image'])
                                        <div class="rounded-circle border border-gold-light overflow-hidden shadow-sm" style="width: 35px; height: 35px;">
                                            <img src="{{ Storage::url($details['image']) }}" alt="Style" class="w-100 h-100 object-fit-cover">
                                        </div>
                                    @endif
                                    @if($details['size'] ?? null)
                                        <span class="variant-tag">{{ $details['size'] }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="cart-price">{{ currency($details['price'] * $details['quantity']) }}</div>
                        </div>
                        @endforeach

                        <div class="totals-section">
                            <div class="totals-row">
                                <span>المجموع الفرعي</span>
                                <span class="fw-bold">{{ currency($total) }}</span>
                            </div>
                             <div class="totals-row">
                                <span>التوصيل</span>
                                <span class="fw-bold" id="shipping-cost-display">حدد المدينة...</span>
                            </div>
                            <div class="totals-row grand">
                                <span>الإجمالي</span>
                                <span class="val fs-6 text-muted fw-normal" id="grand-total-display">يُحدد بعد اختيار المدينة</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Payment Method --}}
                <div class="payment-card">
                    <div class="payment-icon"><i class="fas fa-money-bill-wave"></i></div>
                    <div class="payment-label">الدفع عند الاستلام</div>
                    <div class="payment-note">سيتواصل معك فريقنا لتأكيد الطلب قبل الإرسال</div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Inject cities from PHP
    const moroccanCities = @json($cities);

    const options = moroccanCities.map(c => ({
        value: c.arabic_name,
        label: c.arabic_name,
        fr: c.name,
        price: parseFloat(c.price),
        text: c.arabic_name + ' ' + c.name,
    }));

    const subtotal = {{ $total }};
    const shippingDisplay = document.getElementById('shipping-cost-display');
    const totalDisplay = document.getElementById('grand-total-display');

    const ts = new TomSelect('#city-select', {
        options: options,
        items: [],
        valueField: 'value',
        labelField: 'label',
        searchField: ['text'],
        render: {
            option: function (data, escape) {
                return '<div class="d-flex align-items-center justify-content-between gap-2">' +
                    '<div>' +
                        '<span class="fw-bold">' + escape(data.label) + '</span>' +
                        '<span class="text-muted small ms-2 opacity-75">' + escape(data.fr || '') + '</span>' +
                    '</div>' +
                    '<span class="badge bg-light text-dark fw-normal" style="font-size: 0.75rem;">' + escape(data.price) + ' د.م.</span>' +
                '</div>';
            },
            item: function (data, escape) {
                return '<div>' + escape(data.label) + '</div>';
            },
        },
        placeholder: 'ابحثي عن مدينتك... / Chercher...',
        create: false, // Don't allow custom cities to ensure we have a price
        maxOptions: 500,
        onChange: function(value) {
            const city = options.find(o => o.value === value);
            updateShipping(city);
        }
    });

    function updateShipping(city) {
        if (!city) {
            shippingDisplay.textContent = 'حدد المدينة...';
            totalDisplay.textContent = 'يُحدد بعد اختيار المدينة';
            totalDisplay.classList.add('fs-6', 'text-muted', 'fw-normal');
            return;
        }

        const cost = city.price;
        const total = subtotal + cost;

        shippingDisplay.textContent = cost.toFixed(2) + ' د.م.';
        totalDisplay.textContent = total.toFixed(2) + ' د.م.';
        totalDisplay.classList.remove('fs-6', 'text-muted', 'fw-normal');
        
        shippingDisplay.classList.add('text-primary');
        setTimeout(() => shippingDisplay.classList.remove('text-primary'), 500);
    }
});
</script>
@endpush
