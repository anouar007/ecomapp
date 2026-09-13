@extends('layouts.storefront')
@section('title', 'تم تأكيد طلبك | تعاونية آيت أومديس')
@section('body-class', 'checkout-success-page')
@section('content')
@php
    $statusLabel = match($order->status) {
        'pending' => 'قيد التجهيز',
        'processing' => 'قيد التجهيز',
        'shipped' => 'تم الشحن',
        'delivered' => 'تم التوصيل',
        'cancelled' => 'ملغي',
        default => 'تم استلام الطلب',
    };
@endphp
<section class="success-hero">
<div class="success-hero-note">خيرات الأطلس<br>بين يديك<span></span></div>
</section>

<main class="success-page">
<div class="success-check" aria-hidden="true"><i data-lucide="check"></i></div>
<div class="success-copy">
<h1>تم تأكيد طلبك!</h1>
<p class="success-thanks">شكراً لك على ثقتك بنا</p>
<p>لقد استلمنا طلبك بنجاح، وسنقوم بتجهيزه وشحنه في أقرب وقت ممكن.</p>
@if($order->customer_email)<p>سنرسل لك حالة طلبك عبر بريدك الإلكتروني.</p>@endif
</div>

<section class="success-order-details" aria-label="تفاصيل الطلب">
<div class="success-order-detail">
<i data-lucide="package" aria-hidden="true"></i>
<span>حالة الطلب</span>
<b class="success-status"><i aria-hidden="true"></i>{{ $statusLabel }}</b>
</div>
<div class="success-order-detail">
<i data-lucide="file-text" aria-hidden="true"></i>
<span>رقم الطلب</span>
<b dir="ltr">#{{ $order->order_number }}</b>
</div>
<div class="success-order-detail">
<i data-lucide="calendar-days" aria-hidden="true"></i>
<span>تاريخ الطلب</span>
<b>{{ $order->created_at->locale('ar')->translatedFormat('j F Y') }}</b>
<small dir="ltr">{{ $order->created_at->format('H:i') }}</small>
</div>
</section>

<a class="success-shop-link" href="{{ route('shop.index') }}">العودة إلى المتجر <i data-lucide="arrow-left" aria-hidden="true"></i></a>
</main>
@endsection
