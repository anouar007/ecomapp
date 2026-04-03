@extends('layouts.customer')

@section('dashboard_content')
<div class="d-flex flex-column mb-4" data-aos="fade-up">
    <h3 class="brand-heading h2 mb-2">تاريخ طلباتكِ</h3>
    <div class="bg-gold rounded" style="width: 40px; height: 3px;"></div>
</div>

<div class="brand-card border-0 shadow-sm overflow-hidden bg-white" data-aos="fade-up">
    <div class="table-responsive">
        <table class="table align-middle mb-0 font-body">
            <thead class="bg-gold-light border-bottom border-gold-subtle">
                <tr>
                    <th class="ps-4 py-3 small text-muted fw-bold">رقم الطلب</th>
                    <th class="py-3 small text-muted fw-bold">التاريخ</th>
                    <th class="py-3 small text-muted fw-bold">الحالة</th>
                    <th class="py-3 small text-muted fw-bold">الإجمالي</th>
                    <th class="pe-4 py-3 text-end small text-muted fw-bold">الإجراء</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
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
                        <a href="{{ route('customer.orders.show', $order) }}" class="btn-brand-outline py-1 px-3 small text-decoration-none">
                            التفاصيل <i class="fas fa-search ms-1 small"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5">
                        <div class="opacity-25 mb-3"><i class="fas fa-shopping-bag fa-4x"></i></div>
                        <h6 class="text-muted mb-4 font-body">لم نجد أي طلبات سابقة في حسابكِ.</h6>
                        <a href="{{ route('shop.index') }}" class="btn-brand-primary px-4">اكتشفي التشكيلة الآن</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($orders->hasPages())
    <div class="card-footer bg-white p-4 border-top-0">
        {{ $orders->links() }}
    </div>
    @endif
</div>
@endsection
