@extends('layouts.storefront')
@section('title', 'تأكيد الطلب | تعاونية آيت أومديس')
@section('body-class', 'checkout-page-body')
@section('content')
<section class="checkout-hero">
<div class="checkout-hero-copy"><h1>تأكيد الطلب</h1><p>يرجى مراجعة معلوماتك وإتمام الدفع</p></div>
<div class="checkout-hero-note">خير الأطلس<br>بين يديك<span></span></div>
</section>

<main class="checkout-page">
<form action="{{ route('checkout.store') }}" method="post" id="checkout-form" class="checkout-layout">
@csrf

<aside class="checkout-summary checkout-panel">
<div class="checkout-panel-heading">
<div><h2>ملخص الطلب</h2><p>يرجى مراجعة تفاصيل طلبك</p></div>
<i data-lucide="shopping-bag" aria-hidden="true"></i>
</div>
<div class="checkout-items">
@foreach($cart as $key => $item)
<article class="checkout-item">
<img src="{{ \App\Support\Storefront::image($item['image'] ?? null) }}" alt="{{ $item['name'] }}">
<div><h3>{{ $item['name'] }}</h3><small>{{ $item['size'] ?? 'الحجم القياسي' }}@if(!empty($item['color'])) · {{ $item['color'] }}@endif</small><span>الكمية {{ $item['quantity'] }}</span></div>
<b>{{ \App\Support\Storefront::money($item['price'] * $item['quantity']) }}</b>
</article>
@endforeach
</div>
<div class="checkout-totals">
<div><span>المجموع الفرعي</span><b>{{ \App\Support\Storefront::money($total) }}</b></div>
<div><span>مصاريف التوصيل</span><b class="checkout-free">مجاني</b></div>
<div class="checkout-total"><strong>المجموع الكلي</strong><b>{{ \App\Support\Storefront::money($total) }}</b></div>
</div>
<button type="submit" class="checkout-submit"><i data-lucide="lock-keyhole" aria-hidden="true"></i> تأكيد الطلب بقيمة {{ \App\Support\Storefront::money($total) }}</button>
<p class="checkout-secure"><i data-lucide="shield-check" aria-hidden="true"></i> عملية طلب آمنة ومشفرة</p>
</aside>

<div class="checkout-details">
@if($errors->any())
<div class="checkout-errors" role="alert"><b>يرجى تصحيح المعلومات التالية:</b><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
@endif

<section class="checkout-panel checkout-delivery">
<div class="checkout-panel-heading">
<div><h2>معلومات التوصيل</h2><p>سيتم توصيل طلبك إلى العنوان التالي</p></div>
<i data-lucide="map-pin" aria-hidden="true"></i>
</div>
<div class="checkout-form-grid">
<label><span>الاسم الكامل <em>*</em></span><input type="text" name="customer_name" value="{{ old('customer_name', auth()->user()?->name) }}" autocomplete="name" required></label>
<label><span>رقم الهاتف <em>*</em></span><span class="checkout-phone"><span aria-hidden="true">🇲🇦</span><b dir="ltr">+212</b><input type="tel" name="customer_phone" value="{{ old('customer_phone') }}" placeholder="6 12 34 56 78" autocomplete="tel-national" inputmode="tel" required></span></label>
<label class="checkout-field-wide"><span>العنوان الكامل <em>*</em></span><input type="text" name="shipping_address" value="{{ old('shipping_address') }}" placeholder="مثال: شارع الحسن الثاني، رقم 25، حي النخيل" autocomplete="street-address" required></label>
<label><span>المدينة <em>*</em></span><select name="shipping_city" autocomplete="address-level2" required><option value="">اختر المدينة</option>@foreach(['الدار البيضاء', 'الرباط', 'مراكش', 'طنجة', 'فاس', 'أكادير', 'مكناس', 'وجدة', 'القنيطرة', 'تطوان', 'تمارة', 'آسفي', 'المحمدية', 'بني ملال', 'الجديدة', 'الناظور', 'سطات', 'تازة', 'الخميسات', 'العرائش', 'العيون', 'الداخلة'] as $city)<option value="{{ $city }}" @selected(old('shipping_city') === $city)>{{ $city }}</option>@endforeach</select></label>
<label><span>الجهة</span><select name="shipping_state" autocomplete="address-level1"><option value="">اختر الجهة</option>@foreach(['طنجة - تطوان - الحسيمة', 'الشرق', 'فاس - مكناس', 'الرباط - سلا - القنيطرة', 'بني ملال - خنيفرة', 'الدار البيضاء - سطات', 'مراكش - آسفي', 'درعة - تافيلالت', 'سوس - ماسة', 'كلميم - واد نون', 'العيون - الساقية الحمراء', 'الداخلة - وادي الذهب'] as $region)<option value="{{ $region }}" @selected(old('shipping_state') === $region)>{{ $region }}</option>@endforeach</select></label>
<label><span>الرمز البريدي</span><input type="text" name="shipping_zip" value="{{ old('shipping_zip') }}" inputmode="numeric" autocomplete="postal-code" maxlength="20"></label>
<label class="checkout-field-wide"><span>ملاحظات إضافية (اختياري)</span><textarea name="notes" placeholder="مثال: تعليمات خاصة بالتوصيل ...">{{ old('notes') }}</textarea></label>
</div>
</section>

<section class="checkout-panel checkout-payment">
<div class="checkout-panel-heading">
<div><h2>طريقة الدفع</h2><p>طريقة الدفع المتاحة لطلبك</p></div>
<i data-lucide="credit-card" aria-hidden="true"></i>
</div>
<div class="payment-options">
<input type="hidden" name="payment_method" value="cod">
<div class="payment-option payment-option-static"><i data-lucide="hand-coins" aria-hidden="true"></i><span><b>الدفع عند الاستلام</b><small>ادفع نقداً عند استلام طلبك</small></span><i data-lucide="circle-check" aria-hidden="true"></i></div>
</div>
</section>

<a class="checkout-back" href="{{ route('cart.index') }}"><i data-lucide="arrow-right" aria-hidden="true"></i> العودة إلى السلة</a>
</div>
</form>
</main>
@endsection
