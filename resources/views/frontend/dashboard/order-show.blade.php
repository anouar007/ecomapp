@extends('layouts.customer')

@section('dashboard_content')
<div class="d-flex justify-content-between align-items-center mb-4" data-aos="fade-up">
    <div>
        <h3 class="brand-heading h2 mb-1">تفاصيل الطلب #{{ $order->order_number }}</h3>
        <div class="bg-gold rounded" style="width: 40px; height: 3px;"></div>
    </div>
    <a href="{{ route('customer.orders') }}" class="btn-brand-outline py-1 px-3 small text-decoration-none hvr-backward">
        <i class="fas fa-arrow-right me-1 small"></i> العودة لطلباتي
    </a>
</div>

<div class="row g-4 font-body">
    <div class="col-lg-8" data-aos="fade-up" data-aos-delay="100">
        <div class="brand-card border-0 shadow-sm overflow-hidden mb-4 bg-white">
            <div class="card-header bg-gold-light p-4 border-bottom border-gold-subtle">
                <h5 class="fw-bold m-0 small text-uppercase ls-1">المنتجات المطلوبة</h5>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <tbody>
                        @foreach($order->items as $item)
                        <tr class="border-bottom border-light">
                            <td class="ps-4 py-4">
                                <div class="d-flex align-items-center">
                                    <div class="bg-gold-light rounded shadow-sm d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 75px;">
                                        @if($item->product && $item->product->main_image)
                                            <img src="{{ Storage::url($item->product->main_image) }}" class="rounded h-100 w-100 object-fit-cover">
                                        @else
                                            <i class="fas fa-crown text-gold opacity-50"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-1 text-dark">{{ $item->product_name }}</h6>
                                        <div class="small text-muted">
                                            <span>الكمية: {{ $item->quantity }}</span>
                                            @if($item->variant_id)
                                                @php $v = \App\Models\ProductVariant::find($item->variant_id); @endphp
                                                @if($v)
                                                    <span class="ms-2">| {{ $v->color }} - {{ $v->size }}</span>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-end pe-4 fw-bold text-dark">
                                {{ currency($item->subtotal) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gold-light border-top border-gold-subtle">
                        <tr>
                            <td class="text-end pe-4 py-3 border-0 small text-muted">المجموع الفرعي</td>
                            <td class="text-end pe-4 py-3 border-0 fw-bold text-dark">{{ currency($order->subtotal) }}</td>
                        </tr>
                        <tr>
                            <td class="text-end pe-4 py-2 border-0 small text-muted">التوصيل</td>
                            <td class="text-end pe-4 py-2 border-0 text-gold fw-bold">مجاني</td>
                        </tr>
                        <tr>
                            <td class="text-end pe-4 py-3 border-0 h5 fw-bold text-dark">الإجمالي النهائي</td>
                            <td class="text-end pe-4 py-3 border-0 h4 fw-bold text-gold">{{ $order->formatted_total }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4" data-aos="fade-up" data-aos-delay="200">
        <div class="brand-card border-0 shadow-sm mb-4 bg-white p-4">
            <h6 class="fw-bold text-muted mb-4 small text-uppercase ls-2">عنوان التوصيل</h6>
            <div class="d-flex gap-3">
                <i class="fas fa-map-marker-alt text-gold mt-1"></i>
                <div>
                    <p class="mb-1 fw-bold text-dark">{{ $order->shipping_address }}</p>
                    <p class="mb-0 text-muted">{{ $order->shipping_city }}</p>
                    <p class="mb-0 text-muted">{{ $order->shipping_state }} {{ $order->shipping_zip }}</p>
                </div>
            </div>
        </div>

        <div class="brand-card border-0 shadow-sm bg-white p-4">
            <h6 class="fw-bold text-muted mb-4 small text-uppercase ls-2">معلومات الطلب</h6>
            <div class="d-flex justify-content-between mb-3 align-items-center">
                <span class="text-muted small">حالة الطلب</span>
                @php
                    $statusMap = [
                        'pending' => ['bg' => 'bg-warning-subtle text-warning', 'text' => 'قيد الانتظار'],
                        'confirmed' => ['bg' => 'bg-info-subtle text-info', 'text' => 'تم التأكيد'],
                        'shipping' => ['bg' => 'bg-primary-subtle text-primary', 'text' => 'جاري الشحن'],
                        'delivered' => ['bg' => 'bg-success-subtle text-success', 'text' => 'تم التوصيل'],
                        'cancelled' => ['bg' => 'bg-danger-subtle text-danger', 'text' => 'ملغى'],
                    ];
                    $s = $statusMap[$order->status] ?? ['bg' => 'bg-secondary-subtle text-secondary', 'text' => $order->status];
                @endphp
                <span class="badge {{ $s['bg'] }} rounded-pill px-3 py-1 fw-normal" style="font-size: 0.7rem;">{{ $s['text'] }}</span>
            </div>
            <div class="d-flex justify-content-between mb-3 align-items-center">
                <span class="text-muted small">طريقة الدفع</span>
                <span class="badge bg-gold-light text-dark rounded-pill px-3 py-1 fw-normal text-uppercase" style="font-size: 0.7rem;">عند الاستلام</span>
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <span class="text-muted small">تاريخ الطلب</span>
                <span class="fw-bold text-dark small">{{ $order->created_at->format('d M Y') }}</span>
            </div>
        </div>
    </div>
</div>
@endsection
