@extends('layouts.frontend')

@section('meta_title', 'إتمام الطلب — ' . setting('app_name', 'Hijab Princesses'))

@section('content')
<div class="bg-surface section-py min-vh-100">
    <div class="container">
        <h1 class="fw-black mb-5 h2 border-start-primary ps-3">تأكيد الطلب</h1>

        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                    <div class="card-body p-4 p-md-5">
                        <h4 class="fw-bold mb-4 h5">معلومات التوصيل</h4>
                        <form action="{{ route('checkout.store') }}" method="POST" id="checkout-form">
                            @csrf
                            <div class="row g-4">
                                <div class="col-12">
                                    <label class="form-label small fw-bold text-muted">الاسم الكامل</label>
                                    <input type="text" name="customer_name" class="form-control bg-light border-0 py-3 rounded-3" placeholder="اكتبي اسمك الكامل هنا" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted">البريد الإلكتروني (اختياري)</label>
                                    <input type="email" name="customer_email" class="form-control bg-light border-0 py-3 rounded-3" placeholder="لتلقي تفاصيل الطلب">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted">رقم الهاتف</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-0 rounded-start-3" dir="ltr">+212</span>
                                        <input type="tel" name="customer_phone" class="form-control bg-light border-0 py-3 rounded-end-3" 
                                               placeholder="6 XX XX XX XX" 
                                               pattern="[0-9]{9}" 
                                               required>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-bold text-muted">العنوان السكني</label>
                                    <input type="text" name="shipping_address" class="form-control bg-light border-0 py-3 rounded-3" placeholder="مثال: حي الرياض، شارع النخيل، رقم 12" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted">المدينة</label>
                                    <select name="shipping_city" class="form-select bg-light border-0 py-3 rounded-3" required>
                                        <option value="">اختاري المدينة</option>
                                        @foreach(['الدار البيضاء', 'الرباط', 'مراكش', 'طنجة', 'فاس', 'أكادير', 'مكناس', 'وجدة', 'القنيطرة', 'تطوان', 'تمارة', 'آسفي', 'المحمدية', 'بني ملال', 'الجديدة', 'الناظور', 'سطات', 'تازة', 'الخميسات', 'العرائش', 'العيون', 'الداخلة'] as $city)
                                            <option value="{{ $city }}">{{ $city }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted">الجهة</label>
                                    <input type="text" name="shipping_state" class="form-control bg-light border-0 py-3 rounded-3" placeholder="اختياري">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-body p-4 p-md-5 text-center">
                        <i class="fas fa-truck fa-3x text-primary mb-3"></i>
                        <h4 class="fw-bold mb-3 h5">طريقة الدفع</h4>
                        <div class="p-4 border rounded-4 bg-light-primary-subtle border-primary d-inline-block px-5">
                            <span class="fw-bold h6 m-0"><i class="fas fa-money-bill-wave me-2"></i> الدفع عند الاستلام</span>
                        </div>
                        <p class="text-muted small mt-3">سوف تتواصل معك إحدى عضوات فريقنا لتأكيد الطلب قبل الإرسال.</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden sticky-top" style="top: 100px;">
                    <div class="card-header bg-white p-4 border-bottom-0">
                        <h5 class="fw-black m-0">ملخص الطلب</h5>
                    </div>
                    <div class="card-body p-4 pt-0">
                        @foreach($cart as $key => $details)
                        <div class="d-flex align-items-center mb-4">
                            <div class="me-3 position-relative">
                                @if($details['image'])
                                <img src="{{ Storage::url($details['image']) }}" alt="{{ $details['name'] }}" class="rounded-3 shadow-sm" style="width: 70px; height: 90px; object-fit: cover;">
                                @else
                                <div class="bg-light rounded-3 d-flex align-items-center justify-content-center" style="width: 70px; height: 90px;">
                                    <i class="fas fa-image text-muted opacity-25 fa-2x"></i>
                                </div>
                                @endif
                                <span class="position-absolute top-0 start-0 translate-middle badge rounded-pill bg-primary border-0 shadow-sm">{{ $details['quantity'] }}</span>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="fw-bold mb-1 text-truncate" style="max-width: 180px;">{{ $details['name'] }}</h6>
                                <div class="small text-muted">
                                    @if(($details['color'] ?? null)) {{ $details['color'] }} @endif
                                    @if(($details['color'] ?? null) && ($details['size'] ?? null)) | @endif
                                    @if(($details['size'] ?? null)) {{ $details['size'] }} @endif
                                </div>
                            </div>
                            <div class="fw-bold text-primary">{{ currency($details['price'] * $details['quantity']) }}</div>
                        </div>
                        @endforeach
                        
                        <div class="bg-light p-4 rounded-4 mt-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">المجموع الفرعي</span>
                                <span class="fw-bold">{{ currency($total) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-4">
                                <span class="text-muted">التوصيل</span>
                                <span class="text-success fw-bold">مجاني</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center pt-3 border-top border-secondary-subtle">
                                <span class="h5 fw-black mb-0">الإجمالي</span>
                                <span class="h3 fw-black text-primary mb-0">{{ currency($total) }}</span>
                            </div>
                        </div>

                        <button type="submit" form="checkout-form" class="btn btn-primary btn-lg w-100 rounded-pill py-3 fw-bold shadow mt-4">
                            تأكيد الطلب الآن <i class="fas fa-check-circle ms-2"></i>
                        </button>
                        
                        <div class="text-center mt-4">
                            <a href="{{ route('cart.index') }}" class="text-muted text-decoration-none small">
                                <i class="fas fa-arrow-right me-1"></i> العودة للسلة
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
