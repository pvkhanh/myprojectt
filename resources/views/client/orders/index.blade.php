@extends('layouts.client')

@section('title', 'Đơn hàng của tôi')

@push('styles')
<style>
.orders-container {
    max-width: 1200px;
    margin: 40px auto;
    padding: 0 20px;
}

.page-header {
    margin-bottom: 30px;
}

.page-title {
    font-size: 32px;
    font-weight: bold;
    color: #2d3748;
    margin-bottom: 10px;
}

.stats-bar {
    display: flex;
    gap: 16px;
    margin-bottom: 30px;
    overflow-x: auto;
    padding-bottom: 10px;
}

.stat-card {
    flex: 1;
    min-width: 150px;
    padding: 20px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    text-align: center;
    cursor: pointer;
    transition: all 0.3s;
    border: 2px solid transparent;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(0,0,0,0.12);
}

.stat-card.active {
    border-color: #667eea;
    background: linear-gradient(135deg, rgba(102,126,234,0.1) 0%, rgba(118,75,162,0.1) 100%);
}

.stat-number {
    font-size: 28px;
    font-weight: bold;
    color: #667eea;
    margin-bottom: 5px;
}

.stat-label {
    font-size: 14px;
    color: #718096;
}

.order-card {
    background: white;
    border-radius: 12px;
    padding: 24px;
    margin-bottom: 20px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    transition: all 0.3s;
}

.order-card:hover {
    box-shadow: 0 4px 20px rgba(0,0,0,0.12);
}

.order-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 16px;
    border-bottom: 2px solid #f0f0f0;
}

.order-number {
    font-size: 18px;
    font-weight: bold;
    color: #2d3748;
}

.order-date {
    color: #718096;
    font-size: 14px;
}

.order-items {
    margin-bottom: 20px;
}

.order-item {
    display: flex;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid #f0f0f0;
}

.order-item:last-child {
    border-bottom: none;
}

.item-image {
    width: 60px;
    height: 60px;
    border-radius: 8px;
    object-fit: cover;
    margin-right: 16px;
}

.item-info {
    flex: 1;
}

.item-name {
    font-weight: 500;
    color: #2d3748;
    margin-bottom: 4px;
}

.item-quantity {
    font-size: 14px;
    color: #718096;
}

.order-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.order-total {
    font-size: 20px;
    font-weight: bold;
    color: #667eea;
}

.order-actions {
    display: flex;
    gap: 10px;
}

.btn {
    padding: 10px 20px;
    border-radius: 8px;
    border: none;
    cursor: pointer;
    font-weight: 500;
    transition: all 0.3s;
    text-decoration: none;
    display: inline-block;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102,126,234,0.4);
}

.btn-outline {
    background: white;
    color: #667eea;
    border: 2px solid #667eea;
}

.btn-outline:hover {
    background: #667eea;
    color: white;
}

.badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
}

.empty-icon {
    font-size: 64px;
    color: #cbd5e0;
    margin-bottom: 20px;
}

.empty-title {
    font-size: 24px;
    font-weight: bold;
    color: #2d3748;
    margin-bottom: 10px;
}

.empty-text {
    color: #718096;
    margin-bottom: 30px;
}
</style>
@endpush

@section('content')
<div class="orders-container">
    <div class="page-header">
        <h1 class="page-title">Đơn hàng của tôi</h1>
        <p class="text-muted">Quản lý và theo dõi đơn hàng của bạn</p>
    </div>

    <!-- Stats Bar -->
    <div class="stats-bar">
        <a href="{{ route('client.orders') }}" class="stat-card {{ !request('status') ? 'active' : '' }}">
            <div class="stat-number">{{ $stats['all'] }}</div>
            <div class="stat-label">Tất cả</div>
        </a>

        <a href="{{ route('client.orders', ['status' => 'pending']) }}"
           class="stat-card {{ request('status') === 'pending' ? 'active' : '' }}">
            <div class="stat-number">{{ $stats['pending'] }}</div>
            <div class="stat-label">Chờ xử lý</div>
        </a>

        <a href="{{ route('client.orders', ['status' => 'paid']) }}"
           class="stat-card {{ request('status') === 'paid' ? 'active' : '' }}">
            <div class="stat-number">{{ $stats['paid'] }}</div>
            <div class="stat-label">Đã thanh toán</div>
        </a>

        <a href="{{ route('client.orders', ['status' => 'shipped']) }}"
           class="stat-card {{ request('status') === 'shipped' ? 'active' : '' }}">
            <div class="stat-number">{{ $stats['shipped'] }}</div>
            <div class="stat-label">Đang giao</div>
        </a>

        <a href="{{ route('client.orders', ['status' => 'completed']) }}"
           class="stat-card {{ request('status') === 'completed' ? 'active' : '' }}">
            <div class="stat-number">{{ $stats['completed'] }}</div>
            <div class="stat-label">Hoàn thành</div>
        </a>

        <a href="{{ route('client.orders', ['status' => 'cancelled']) }}"
           class="stat-card {{ request('status') === 'cancelled' ? 'active' : '' }}">
            <div class="stat-number">{{ $stats['cancelled'] }}</div>
            <div class="stat-label">Đã hủy</div>
        </a>
    </div>

    <!-- Orders List -->
    @forelse($orders as $order)
    <div class="order-card">
        <div class="order-header">
            <div>
                <div class="order-number">Đơn hàng #{{ $order->order_number }}</div>
                <div class="order-date">
                    <i class="far fa-calendar me-1"></i>
                    {{ $order->created_at->format('d/m/Y H:i') }}
                </div>
            </div>
            <div>
                <span class="badge bg-{{ $order->status->color() }}">
                    {{ $order->status->label() }}
                </span>
            </div>
        </div>

        <div class="order-items">
            @foreach($order->orderItems->take(3) as $item)
            <div class="order-item">
                @if($item->product->image)
                <img src="{{ asset('storage/' . $item->product->image) }}"
                     alt="{{ $item->product->name }}" class="item-image">
                @else
                <div class="item-image" style="background: #f0f0f0; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-image text-muted"></i>
                </div>
                @endif

                <div class="item-info">
                    <div class="item-name">{{ $item->product->name }}</div>
                    <div class="item-quantity">Số lượng: {{ $item->quantity }}</div>
                </div>

                <div class="fw-bold">{{ number_format($item->price * $item->quantity) }}₫</div>
            </div>
            @endforeach

            @if($order->orderItems->count() > 3)
            <div class="text-muted small mt-2">
                Và {{ $order->orderItems->count() - 3 }} sản phẩm khác...
            </div>
            @endif
        </div>

        <div class="order-footer">
            <div>
                <div class="text-muted small mb-1">Tổng thanh toán:</div>
                <div class="order-total">{{ number_format($order->total_amount) }}₫</div>
            </div>

            <div class="order-actions">
                <a href="{{ route('client.orders.show', $order->id) }}" class="btn btn-outline">
                    <i class="fas fa-eye me-1"></i> Chi tiết
                </a>

                @if(in_array($order->status->value, ['pending', 'paid']))
                <button type="button" class="btn btn-outline text-danger border-danger"
                        onclick="cancelOrder({{ $order->id }})">
                    <i class="fas fa-times me-1"></i> Hủy đơn
                </button>
                @endif

                @if($order->status->value === 'completed')
                <a href="{{ route('client.orders.reorder', $order->id) }}" class="btn btn-primary">
                    <i class="fas fa-redo me-1"></i> Mua lại
                </a>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="empty-state">
        <div class="empty-icon">📦</div>
        <div class="empty-title">Chưa có đơn hàng nào</div>
        <div class="empty-text">Bạn chưa có đơn hàng nào. Hãy bắt đầu mua sắm ngay!</div>
        <a href="{{ route('client.products.index') }}" class="btn btn-primary">
            <i class="fas fa-shopping-bag me-2"></i>Khám phá sản phẩm
        </a>
    </div>
    @endforelse

    <!-- Pagination -->
    @if($orders->hasPages())
    <div class="mt-4">
        {{ $orders->links() }}
    </div>
    @endif
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function cancelOrder(orderId) {
    Swal.fire({
        title: 'Hủy đơn hàng?',
        text: 'Bạn có chắc chắn muốn hủy đơn hàng này?',
        icon: 'warning',
        input: 'textarea',
        inputPlaceholder: 'Nhập lý do hủy đơn...',
        inputAttributes: {
            required: true
        },
        showCancelButton: true,
        confirmButtonText: 'Xác nhận hủy',
        cancelButtonText: 'Đóng',
        confirmButtonColor: '#dc3545',
        preConfirm: (reason) => {
            if (!reason) {
                Swal.showValidationMessage('Vui lòng nhập lý do hủy đơn');
            }
            return reason;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/client/orders/${orderId}/cancel`;

            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = '{{ csrf_token() }}';

            const reasonInput = document.createElement('input');
            reasonInput.type = 'hidden';
            reasonInput.name = 'reason';
            reasonInput.value = result.value;

            form.appendChild(csrf);
            form.appendChild(reasonInput);
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>
@endpush
@endsection
