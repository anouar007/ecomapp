@extends('layouts.customer')

@section('dashboard_content')
<div class="d-flex flex-column mb-4" data-aos="fade-up">
    <h3 class="brand-heading h2 mb-2">مرحباً بكِ في عالمنا</h3>
    <div class="bg-gold rounded" style="width: 40px; height: 3px;"></div>
</div>

{{-- Stats Grid --}}
<div class="row g-3 g-lg-4 mb-5">
    <div class="col-6 col-md-6" data-aos="fade-up" data-aos-delay="50">
        <div class="brand-card border-0 shadow-sm overflow-hidden bg-gold-gradient text-white h-100 p-4 relative">
            <i class="fas fa-shopping-bag position-absolute opacity-10" style="right: -10px; bottom: -10px; font-size: 5rem;"></i>
            <h6 class="opacity-75 mb-3 font-body small fw-bold">إجمالي الطلبات</h6>
            <h2 class="brand-heading display-5 mb-0">{{ $user->orders()->count() }}</h2>
        </div>
    </div>
    <div class="col-6 col-md-6" data-aos="fade-up" data-aos-delay="100">
        <div class="brand-card border-0 shadow-sm bg-white h-100 p-4 d-flex flex-column justify-content-center">
            <h6 class="text-muted fw-bold mb-3 font-body small">حسابكِ الملكي</h6>
            <p class="mb-1 fw-bold text-dark font-body">{{ $user->name }}</p>
            <p class="text-muted mb-0 small font-body text-truncate">{{ $user->email }}</p>
            <a href="{{ route('customer.profile') }}" class="text-gold text-decoration-none small fw-bold mt-2 font-body hvr-forward">تعديل الملف الشخصي <i class="fas fa-arrow-left ms-1 small"></i></a>
        </div>
    </div>
</div>

{{-- Recent Orders --}}
<div class="brand-card border-0 shadow-sm overflow-hidden bg-white" data-aos="fade-up" data-aos-delay="150">
    <div class="card-header bg-white p-4 border-bottom border-light d-flex justify-content-between align-items-center">
        <h5 class="brand-heading m-0">طلباتكِ الأخيرة</h5>
        <a href="{{ route('customer.orders') }}" class="btn-brand-outline py-1 px-3 small">عرض الكل</a>
    </div>
    <div class="table-responsive">
        <table class="table align-middle mb-0 font-body">
            <thead class="bg-gold-light border-bottom border-gold-subtle">
                <tr>
                    <th class="ps-4 py-3 small text-muted fw-bold">رقم الطلب</th>
                    <th class="py-3 small text-muted fw-bold">التاريخ</th>
                    <th class="py-3 small text-muted fw-bold">الحالة</th>
                    <th class="py-3 small text-muted fw-bold">الإجمالي</th>
                    <th class="pe-4 py-3 text-end small text-muted fw-bold">التفاصيل</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentOrders as $order)
                <tr class="border-bottom border-light hover-gold-subtle transition-300">
                    <td class="ps-4 fw-bold text-dark">#{{ $order->order_number }}</td>
                    <td class="text-muted small">{{ $order->created_at->format('d M Y') }}</td>
                    <td>
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
                        <span class="badge {{ $s['bg'] }} rounded-pill px-3 py-1 font-body fw-normal" style="font-size: 0.7rem;">{{ $s['text'] }}</span>
                    </td>
                    <td class="fw-bold text-dark">{{ $order->formatted_total }}</td>
                    <td class="pe-4 text-end">
                        <a href="{{ route('customer.orders.show', $order) }}" class="btn btn-sm btn-light hvr-grow rounded-circle p-2">
                            <i class="fas fa-eye text-gold"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5">
                        <div class="opacity-25 mb-3"><i class="fas fa-shopping-bag fa-3x"></i></div>
                        <div class="text-muted mb-4 font-body">لم تقومي بطلب أي عباية أو خمار بعد.</div>
                        <a href="{{ route('shop.index') }}" class="btn-brand-primary px-4">ابدئي التسوق الآن</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
