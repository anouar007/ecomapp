@extends('layouts.storefront')
@section('title', 'تواصل معنا | تعاونية آيت أومديس')
@section('body-class', 'contact')
@section('content')
<section class="contact-hero">
<div class="contact-hero-title">
<h1>
تواصل معنا
</h1>
<p>
نحن هنا للإجابة على جميع استفساراتكم<br>معاً نحو نمط حياة أكثر طبيعية وصحة
</p>
</div>
<div class="contact-hero-note">
من قلب الأطلس<br>إلى كل بيت في العالم<span></span>
</div>
</section>
<main class="contact-main">
<section class="contact-visuals">
<div class="contact-photo" role="img" aria-label="متجر تعاونية آيت أومديس">
</div>
<div class="contact-map" role="img" aria-label="موقع التعاونية على الخريطة">
<div class="map-controls">
<button aria-label="تكبير" onclick="zoomMap(1)"><i data-lucide="plus" aria-hidden="true"></i></button><button aria-label="تصغير" onclick="zoomMap(-1)"><i data-lucide="minus" aria-hidden="true"></i></button>
</div>
<span class="map-label">تعاونية آيت أومديس</span><span class="map-pin"><i data-lucide="map-pin" aria-hidden="true"></i></span><b class="map-imlil">إمليل<br>Imlil</b><b class="map-asni">أسني<br>Asni</b>
</div>
</section>
<section class="contact-form-panel panel">
<h2>
أرسل لنا رسالة
</h2>
<p>
املأ النموذج التالي وسنعاود الاتصال بكم في أقرب وقت ممكن
</p>
<form onsubmit="sendContact(event)" data-email="{{ setting('contact_email', 'contact@aitoumdis.ma') }}">
<div class="contact-form-grid">
<label>الاسم الكامل <em>*</em><span><input name="name" autocomplete="name" maxlength="120" required placeholder="أدخل اسمك الكامل"><i data-lucide="user-round" aria-hidden="true"></i></span></label><label>البريد الإلكتروني <em>*</em><span><input name="email" autocomplete="email" type="email" required placeholder="example@domain.com"><i data-lucide="mail" aria-hidden="true"></i></span></label><label>رقم الهاتف<span><input name="phone" type="tel" autocomplete="tel" dir="ltr" placeholder="+212 ..."><i data-lucide="phone" aria-hidden="true"></i></span></label><label>موضوع الرسالة <em>*</em><select name="subject" required><option value="">اختر الموضوع</option><option>استفسار عن منتج</option><option>تتبع طلب</option><option>التعاون والشراكات</option></select></label><label class="message-label">رسالتك <em>*</em><textarea name="message" required maxlength="500" placeholder="اكتب رسالتك هنا ..." oninput="this.nextElementSibling.textContent=this.value.length+'/500'"></textarea><small>0/500</small></label>
</div>
<button class="primary contact-submit" type="submit"><i data-lucide="send" aria-hidden="true"></i> إرسال الرسالة</button>
</form>
</section>
<aside class="contact-info panel">
<h2>
معلومات التواصل
</h2>
<p>
يسعدنا الاستماع إليكم، يمكنكم التواصل معنا عبر<br>الطرق التالية:
</p>
<div class="contact-detail">
<i><i data-lucide="phone" aria-hidden="true"></i></i><span><b>الهاتف</b><a href="tel:+212612345678">+212 6 12 34 56 78</a><small>من الاثنين إلى الجمعة، 9:00 - 18:00</small></span>
</div>
<div class="contact-detail">
<i><i data-lucide="phone" aria-hidden="true"></i></i><span><b>واتساب</b><a href="https://wa.me/212612345678">+212 6 12 34 56 78</a><small>للاستشارات السريعة</small></span>
</div>
<div class="contact-detail">
<i><i data-lucide="mail" aria-hidden="true"></i></i><span><b>البريد الإلكتروني</b><a href="mailto:contact@aitoumdis.ma">contact@aitoumdis.ma</a><small>نرد خلال 24 ساعة</small></span>
</div>
<div class="contact-detail">
<i><i data-lucide="map-pin" aria-hidden="true"></i></i><span><b>العنوان</b><a href="https://maps.google.com/?q=Imlil+Morocco">دوار آيت أومديس، جماعة أسني</a><small>إقليم الحوز، المغرب</small><u>عرض على خرائط جوجل ↗</u></span>
</div>
</aside>
</main>

@endsection
