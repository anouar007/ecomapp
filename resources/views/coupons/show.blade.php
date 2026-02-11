@extends('layouts.app')

@section('title', 'Coupon Details')

@section('content')
    <div class="brand-header">
        <div>
            <h1 class="brand-title">
                <div class="brand-header-icon">
                    <i class="fas fa-ticket-alt"></i>
                </div>
                {{ $coupon->code }}
            </h1>
            <p class="brand-subtitle">{{ $coupon->name }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('coupons.index') }}" class="btn-brand-light">
                <i class="fas fa-arrow-left me-2"></i> Back
            </a>
            <a href="{{ route('coupons.edit', $coupon) }}" class="btn-brand-primary">
                <i class="fas fa-edit me-2"></i> Edit Coupon
            </a>
            <form action="{{ route('coupons.destroy', $coupon) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger h-100">
                    <i class="fas fa-trash-alt me-2"></i> Delete
                </button>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="brand-card mb-4">
                <div class="brand-card-header">
                    <h5 class="brand-card-title">Coupon Information</h5>
                </div>
                <div class="brand-card-body">
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="text-muted small text-uppercase fw-bold mb-1">Type</div>
                            <span class="badge bg-primary text-uppercase">{{ str_replace('_', ' ', $coupon->type) }}</span>
                        </div>
                        <div class="col-md-4">
                            <div class="text-muted small text-uppercase fw-bold mb-1">Value</div>
                            <div class="fs-5 fw-bold text-dark">
                                @if($coupon->type == 'percentage')
                                    {{ $coupon->value }}%
                                @elseif($coupon->type == 'fixed')
                                    {{ currency($coupon->value) }}
                                @elseif($coupon->type == 'free_shipping')
                                    Free Shipping
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-muted small text-uppercase fw-bold mb-1">Status</div>
                            <span class="badge bg-{{ $coupon->status == 'active' ? 'success' : 'secondary' }} text-uppercase">
                                {{ $coupon->status }}
                            </span>
                        </div>
                    </div>

                    @if($coupon->description)
                    <div class="mb-4">
                        <div class="text-muted small text-uppercase fw-bold mb-1">Description</div>
                        <p class="text-dark">{{ $coupon->description }}</p>
                    </div>
                    @endif

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="text-muted small text-uppercase fw-bold mb-1">Validity Period</div>
                            <div class="text-dark">
                                @if($coupon->valid_from)
                                    From: {{ $coupon->valid_from->format('M d, Y') }}<br>
                                @endif
                                @if($coupon->valid_to)
                                    To: {{ $coupon->valid_to->format('M d, Y') }}
                                @else
                                    No expiration
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small text-uppercase fw-bold mb-1">Usage Limits</div>
                            <div class="text-dark">
                                Total: {{ $coupon->usage_limit ?: 'Unlimited' }}<br>
                                Per Customer: {{ $coupon->per_customer_limit ?: 'Unlimited' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Usage History -->
            <div class="brand-card">
                <div class="brand-card-header">
                    <h5 class="brand-card-title">Recent Usage</h5>
                </div>
                <div class="brand-card-body p-0">
                    <div class="table-responsive">
                        <table class="brand-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Order</th>
                                    <th>Discount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($coupon->usages()->latest()->take(5)->get() as $usage)
                                <tr>
                                    <td>{{ $usage->created_at->format('M d, Y H:i') }}</td>
                                    <td>
                                        <a href="{{ route('orders.show', $usage->order_id) }}" class="fw-bold">
                                            #{{ $usage->order_id }}
                                        </a>
                                    </td>
                                    <td>{{ currency($usage->discount_amount) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">
                                        No usage history yet.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="brand-card mb-4">
                <div class="brand-card-header">
                    <h5 class="brand-card-title">Statistics</h5>
                </div>
                <div class="brand-card-body">
                    <div class="d-flex justify-content-between mb-3 border-bottom pb-3">
                        <span class="text-muted">Times Used</span>
                        <span class="fw-bold fs-5">{{ $coupon->usage_count }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Total Discounted</span>
                        <span class="fw-bold fs-5 text-success">{{ currency($coupon->usages()->sum('discount_amount')) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
