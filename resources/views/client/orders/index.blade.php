{{-- resources/views/client/orders/index.blade.php --}}
@extends('client.layouts.master')

@section('title', 'Đơn hàng của tôi')

@push('styles')
    <style>
        .orders-page {
            padding: 60px 0;
            background: #f8fafc;
        }

        .page-header {
            background: white;
            border-radius: 16px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .page-title {
            font-size: 32px;
            font-weight: 800;
            color: #1e293b;
            margin: 0;
        }

        .order-filters {
            background: white;
            border-radius: 16px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .filter-tabs {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .filter-tab {
            padding: 12px 24px;
            background: white;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            color: #64748b;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .filter-tab:hover {
            border-color: var(--primary-color);
            color: var(--primary-color);
        }

        .filter-tab.active {
            background: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }

        .order-card {
            background: white;
            border-radius: 16px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            transition: all 0.3s;
        }

        .order-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 20px;
            border-bottom: 2px solid #e2e8f0;
            margin-bottom: 20px;
        }

        .order-id {
            font-size: 18px;
            font-weight: 700;
            color: #1e293b;
        }

        .order-date {
            color: #64748b;
            font-size: 14px;
            margin-top: 5px;
        }

        .order-status {
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .status-confirmed {
            background: #dbeafe;
            color: #1e40af;
        }

        .status-shipping {
            background: #e0e7ff;
            color: #4338ca;
        }

        .status-delivered {
            background: #d1fae5;
            color: #065f46;
        }

        .status-cancelled {
            background: #fee2e2;
            color: #991b1b;
        }

        .order-items {
            margin-bottom: 20px;
        }

        .order-item {
            display: flex;
            gap: 15px;
            padding: 15px;
            background: #f8fafc;
            border-radius: 12px;
            margin-bottom: 12px;
        }

        .item-image {
            width: 80px;
            height: 80px;
            border-radius: 10px;
            overflow: hidden;
        }

        .item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .item-info {
            flex: 1;
        }

        .item-name {
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 5px;
        }

        .item-variant {
            color: #64748b;
            font-size: 13px;
            margin-bottom: 8px;
        }

        .item-price {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .order-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 20px;
            border-top: 2px solid #e2e8f0;
        }

        .order-total {
            font-size: 20px;
            font-weight: 700;
        }

        .total-label {
            color: #64748b;
            margin-right: 10px;
        }

        .total-amount {
            color: #ef4444;
        }

        .order-actions {
            display: flex;
            gap: 10px;
        }

        .btn-order {
            padding: 10px 20px;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-view {
            background: #eff6ff;
            color: #2563eb;
        }

        .btn-view:hover {
            background: #2563eb;
            color: white;
        }

        .btn-cancel {
            background: #fee2e2;
            color: #ef4444;
        }

        .btn-cancel:hover {
            background: #ef4444;
            color: white;
        }

        .btn-reorder {
            background: #f0fdf4;
            color: #16a34a;
        }

        .btn-reorder:hover {
            background: #16a34a;
            color: white;
        }

        .empty-orders {
            text-align: center;
            padding: 100px 20px;
            background: white;
            border-radius: 16px;
        }

        .empty-icon {
            font-size: 120px;
            color: #cbd5e1;
            margin-bottom: 30px;
        }
    </style>
@endpush

@section('content')
    <div class="orders-page">
        <div class="container">
            <!-- Page Header -->
            <div class="page-header">
                <h1 class="page-title">
                    <i class="fas fa-shopping-bag me-3"></i>
                    Đơn hàng của tôi
                </h1>
            </div>

            <!-- Order Filters -->
            <div class="order-filters">
                <div class="filter-tabs">
                    <button class="filter-tab active" data-status="all">
                        Tất cả
                    </button>
                    <button class="filter-tab" data-status="pending">
                        Chờ xác nhận
                    </button>
                    <button class="filter-tab" data-status="confirmed">
                        Đã xác nhận
                    </button>
                    <button class="filter-tab" data-status="shipping">
                        Đang giao
                    </button>
                    <button class="filter-tab" data-status="delivered">
                        Đã giao
                    </button>
                    <button class="filter-tab" data-status="cancelled">
                        Đã hủy
                    </button>
                </div>
            </div>

            <!-- Orders List -->
            @forelse($orders ?? [] as $order)
                <div class="order-card" data-status="{{ $order->status }}">
                    <div class="order-header">
                        <div>
                            <div class="order-id">
                                <i class="fas fa-hashtag"></i>
                                Đơn hàng #{{ $order->code }}
                            </div>
                            <div class="order-date">
                                <i class="far fa-clock me-1"></i>
                                {{ $order->created_at->format('d/m/Y H:i') }}
                            </div>
                        </div>
                        <span class="order-status status-{{ $order->status }}">
                            @switch($order->status)
                                @case('pending')
                                    Chờ xác nhận
                                @break

                                @case('confirmed')
                                    Đã xác nhận
                                @break

                                @case('shipping')
                                    Đang giao hàng
                                @break

                                @case('delivered')
                                    Đã giao hàng
                                @break

                                @case('cancelled')
                                    Đã hủy
                                @break

                                @default
                                    {{ $order->status }}
                            @endswitch
                        </span>
                    </div>

                    <div class="order-items">
                        @foreach ($order->items as $item)
                            <div class="order-item">
                                <div class="item-image">
                                    <img src="{{ $item->product->main_image ?? 'https://via.placeholder.com/80' }}"
                                        alt="{{ $item->product_name }}">
                                </div>
                                <div class="item-info">
                                    <div class="item-name">{{ $item->product_name }}</div>
                                    @if ($item->variant)
                                        <div class="item-variant">{{ $item->variant }}</div>
                                    @endif
                                    <div class="item-price">
                                        <span class="text-muted">x{{ $item->quantity }}</span>
                                        <strong class="text-danger">{{ number_format($item->price) }}đ</strong>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="order-footer">
                        <div class="order-total">
                            <span class="total-label">Tổng tiền:</span>
                            <span class="total-amount">{{ number_format($order->total) }}đ</span>
                        </div>

                        <div class="order-actions">
                            <a href="{{ route('client.orders.show', $order->id) }}" class="btn-order btn-view">
                                <i class="fas fa-eye me-1"></i>Xem chi tiết
                            </a>

                            @if ($order->status === 'pending')
                                <button class="btn-order btn-cancel" onclick="cancelOrder({{ $order->id }})">
                                    <i class="fas fa-times me-1"></i>Hủy đơn
                                </button>
                            @endif

                            @if ($order->status === 'delivered')
                                <button class="btn-order btn-reorder" onclick="reorder({{ $order->id }})">
                                    <i class="fas fa-redo me-1"></i>Mua lại
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                    <div class="empty-orders">
                        <div class="empty-icon">
                            <i class="fas fa-shopping-bag"></i>
                        </div>
                        <h2 class="mb-3">Chưa có đơn hàng nào</h2>
                        <p class="text-muted mb-4">Bạn chưa có đơn hàng nào. Hãy bắt đầu mua sắm ngay!</p>
                        <a href="{{ route('client.products.index') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-shopping-cart me-2"></i>Khám phá sản phẩm
                        </a>
                    </div>
                @endforelse

                <!-- Pagination -->
                @if (isset($orders) && $orders->hasPages())
                    <div class="mt-4">
                        {{ $orders->links('components.pagination') }}
                    </div>
                @endif
            </div>
        </div>
    @endsection

    @push('scripts')
        <script>
            $(document).ready(function() {
                // Filter orders by status
                $('.filter-tab').click(function() {
                    $('.filter-tab').removeClass('active');
                    $(this).addClass('active');

                    const status = $(this).data('status');

                    if (status === 'all') {
                        $('.order-card').show();
                    } else {
                        $('.order-card').hide();
                        $('.order-card[data-status="' + status + '"]').show();
                    }
                });
            });

            function cancelOrder(orderId) {
                if (!confirm('Bạn có chắc muốn hủy đơn hàng này?')) {
                    return;
                }

                $.ajax({
                    url: '/orders/' + orderId + '/cancel',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            showToast('Đã hủy đơn hàng thành công', 'success');
                            location.reload();
                        } else {
                            showToast(response.message || 'Không thể hủy đơn hàng', 'error');
                        }
                    },
                    error: function() {
                        showToast('Có lỗi xảy ra', 'error');
                    }
                });
            }

            function reorder(orderId) {
                $.ajax({
                    url: '/orders/' + orderId + '/reorder',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            showToast('Đã thêm sản phẩm vào giỏ hàng', 'success');
                            window.location.href = '{{ route('client.cart.index') }}';
                        }
                    },
                    error: function() {
                        showToast('Có lỗi xảy ra', 'error');
                    }
                });
            }

            function showToast(message, type) {
                alert(message);
            }
        </script>
    @endpush
