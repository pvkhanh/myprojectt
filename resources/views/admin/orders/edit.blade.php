{{-- @extends('layouts.admin')

@section('title', 'Chỉnh sửa đơn hàng #' . $order->order_number)

@section('content')
    <div class="container-fluid px-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="fw-bold text-dark mb-2">
                            <i class="fa-solid fa-pen-to-square text-warning me-2"></i>
                            Chỉnh sửa đơn hàng #{{ $order->order_number }}
                        </h2>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}">Đơn hàng</a></li>
                                <li class="breadcrumb-item"><a
                                        href="{{ route('admin.orders.show', $order->id) }}">#{{ $order->order_number }}</a>
                                </li>
                                <li class="breadcrumb-item active">Chỉnh sửa</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-secondary btn-lg">
                            <i class="fa-solid fa-arrow-left me-2"></i> Quay lại
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.orders.update', $order->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row g-4">
                <!-- Left Column -->
                <div class="col-lg-8">
                    <!-- Order Items (Read Only) -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h5 class="mb-0 fw-semibold">
                                <i class="fa-solid fa-box text-primary me-2"></i>Sản phẩm đã đặt
                                <span class="badge bg-primary ms-2">{{ $order->orderItems->count() }} sản phẩm</span>
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="px-4 py-3">Sản phẩm</th>
                                            <th class="px-4 py-3 text-center">Đơn giá</th>
                                            <th class="px-4 py-3 text-center">Số lượng</th>
                                            <th class="px-4 py-3 text-end">Thành tiền</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($order->orderItems as $item)
                                            <tr>
                                                <td class="px-4 py-3">
                                                    <div class="d-flex align-items-center">
                                                        @if ($item->product->image)
                                                            <img src="{{ asset('storage/' . $item->product->image) }}"
                                                                alt="{{ $item->product->name }}" class="rounded me-3"
                                                                style="width: 50px; height: 50px; object-fit: cover;">
                                                        @else
                                                            <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center"
                                                                style="width: 50px; height: 50px;">
                                                                <i class="fa-solid fa-image text-muted"></i>
                                                            </div>
                                                        @endif
                                                        <div>
                                                            <div class="fw-semibold">{{ $item->product->name }}</div>
                                                            @if ($item->variant)
                                                                <div class="small text-muted">{{ $item->variant->name }}
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    {{ number_format($item->price) }}đ
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    <span class="badge bg-primary px-3 py-2">{{ $item->quantity }}</span>
                                                </td>
                                                <td class="px-4 py-3 text-end fw-bold text-primary">
                                                    {{ number_format($item->price * $item->quantity) }}đ
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="bg-light">
                                        <tr>
                                            <td colspan="3" class="px-4 py-3 text-end fw-semibold">Tạm tính:</td>
                                            <td class="px-4 py-3 text-end fw-bold" id="subtotal">
                                                {{ number_format($order->orderItems->sum(fn($item) => $item->price * $item->quantity)) }}đ
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Shipping Fee -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h5 class="mb-0 fw-semibold">
                                <i class="fa-solid fa-truck text-primary me-2"></i>Phí vận chuyển
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row align-items-end">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        Phí ship <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group input-group-lg">
                                        <input type="number" name="shipping_fee"
                                            class="form-control @error('shipping_fee') is-invalid @enderror"
                                            value="{{ old('shipping_fee', $order->shipping_fee) }}" min="0"
                                            step="1000" id="shippingFeeInput">
                                        <span class="input-group-text">đ</span>
                                        @error('shipping_fee')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <small class="text-muted">Nhập số tiền phí vận chuyển</small>
                                </div>
                                <div class="col-md-6">
                                    <div class="alert alert-info mb-0">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <strong>Tổng đơn hàng:</strong>
                                            <span class="fs-4 fw-bold" id="totalAmount">
                                                {{ number_format($order->total_amount) }}đ
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom py-3">
                            <h5 class="mb-0 fw-semibold">
                                <i class="fa-solid fa-note-sticky text-primary me-2"></i>Ghi chú
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Ghi chú của khách hàng</label>
                                    <textarea name="customer_note" class="form-control @error('customer_note') is-invalid @enderror" rows="3"
                                        placeholder="Ghi chú từ khách hàng..." readonly>{{ old('customer_note', $order->customer_note) }}</textarea>
                                    @error('customer_note')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Ghi chú nội bộ (Admin)</label>
                                    <textarea name="admin_note" class="form-control @error('admin_note') is-invalid @enderror" rows="3"
                                        placeholder="Ghi chú dành cho admin...">{{ old('admin_note', $order->admin_note) }}</textarea>
                                    @error('admin_note')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="col-lg-4">
                    <!-- Status -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h5 class="mb-0 fw-semibold">
                                <i class="fa-solid fa-flag text-primary me-2"></i>Trạng thái đơn hàng
                            </h5>
                        </div>
                        <div class="card-body">
                            <label class="form-label fw-semibold">
                                Trạng thái <span class="text-danger">*</span>
                            </label>
                            <select name="status"
                                class="form-select form-select-lg @error('status') is-invalid @enderror" required>
                                @foreach ($statuses as $status)
                                    <option value="{{ $status->value }}"
                                        {{ old('status', $order->status->value) == $status->value ? 'selected' : '' }}>
                                        {{ $status->label() }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <div class="mt-3 p-3 bg-light rounded">
                                <small class="text-muted">
                                    <i class="fa-solid fa-info-circle me-1"></i>
                                    Thay đổi trạng thái sẽ cập nhật timeline đơn hàng
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Customer Info (Read Only) -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h5 class="mb-0 fw-semibold">
                                <i class="fa-solid fa-user text-primary me-2"></i>Khách hàng
                            </h5>
                        </div>
                        <div class="card-body">
                            @if ($order->user)
                                <div class="text-center">
                                    <div class="avatar-lg mx-auto mb-3">
                                        <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center"
                                            style="width: 70px; height: 70px;">
                                            <i class="fa-solid fa-user text-primary fs-2"></i>
                                        </div>
                                    </div>
                                    <h6 class="fw-bold mb-1">{{ $order->user->name }}</h6>
                                    <p class="text-muted small mb-0">{{ $order->user->email }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Payment Info (Read Only) -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h5 class="mb-0 fw-semibold">
                                <i class="fa-solid fa-credit-card text-primary me-2"></i>Thanh toán
                            </h5>
                        </div>
                        <div class="card-body">
                            @php $payment = $order->payments->first(); @endphp
                            @if ($payment)
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted">Phương thức:</span>
                                    <span class="fw-semibold">{{ $payment->payment_method->label() }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted">Trạng thái:</span>
                                    <span class="badge bg-{{ $payment->status->color() }} px-3 py-2">
                                        {{ $payment->status->label() }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fa-solid fa-save me-2"></i> Lưu thay đổi
                                </button>
                                <a href="{{ route('admin.orders.show', $order->id) }}"
                                    class="btn btn-outline-secondary btn-lg">
                                    <i class="fa-solid fa-times me-2"></i> Hủy bỏ
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const subtotal = {{ $order->orderItems->sum(fn($item) => $item->price * $item->quantity) }};
                const shippingFeeInput = document.getElementById('shippingFeeInput');
                const totalAmountEl = document.getElementById('totalAmount');

                shippingFeeInput.addEventListener('input', function() {
                    const shippingFee = parseFloat(this.value) || 0;
                    const total = subtotal + shippingFee;
                    totalAmountEl.textContent = total.toLocaleString('vi-VN') + 'đ';
                });
            });
        </script>
    @endpush
@endsection --}}


{{--
@extends('layouts.admin')

@section('title', 'Chỉnh sửa đơn hàng #' . $order->order_number)

@section('content')
    <div class="container-fluid px-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="fw-bold text-dark mb-2">
                            <i class="fa-solid fa-pen-to-square text-warning me-2"></i>
                            Chỉnh sửa đơn hàng #{{ $order->order_number }}
                        </h2>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}">Đơn hàng</a></li>
                                <li class="breadcrumb-item"><a
                                        href="{{ route('admin.orders.show', $order->id) }}">#{{ $order->order_number }}</a>
                                </li>
                                <li class="breadcrumb-item active">Chỉnh sửa</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-secondary btn-lg">
                            <i class="fa-solid fa-arrow-left me-2"></i> Quay lại
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.orders.update', $order->id) }}" method="POST">
            @csrf
            @method('PUT')
            <!-- Order Timeline -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4">
                        <i class="fa-solid fa-timeline text-primary me-2"></i>Trạng thái đơn hàng
                    </h5>

                    @if ($order->status->value === 'cancelled')
                        <div class="alert alert-danger">
                            <div class="d-flex align-items-center">
                                <i class="fa-solid fa-ban fs-3 me-3"></i>
                                <div>
                                    <strong>Đơn hàng đã bị hủy</strong>
                                    @if ($order->cancelled_at)
                                        <p class="mb-0 small">Thời gian: {{ $order->cancelled_at->format('d/m/Y H:i') }}</p>
                                    @endif
                                    @if ($order->admin_note)
                                        <p class="mb-0 small">Lý do: {{ $order->admin_note }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="timeline-container">
                            <div class="d-flex justify-content-between">
                                @php
                                    $steps = [
                                        [
                                            'key' => 'pending',
                                            'icon' => 'clock',
                                            'label' => 'Chờ xử lý',
                                            'time' => $order->created_at,
                                        ],
                                        [
                                            'key' => 'paid',
                                            'icon' => 'credit-card',
                                            'label' => 'Đã thanh toán',
                                            'time' => $order->paid_at,
                                        ],
                                        [
                                            'key' => 'shipped',
                                            'icon' => 'truck',
                                            'label' => 'Đang giao',
                                            'time' => $order->shipped_at,
                                        ],
                                        [
                                            'key' => 'completed',
                                            'icon' => 'check-circle',
                                            'label' => 'Hoàn thành',
                                            'time' => $order->completed_at,
                                        ],
                                    ];
                                    $currentStep = array_search($order->status->value, array_column($steps, 'key'));
                                @endphp

                                @foreach ($steps as $index => $step)
                                    @php
                                        $isCompleted = $index < $currentStep;
                                        $isActive = $index === $currentStep;
                                    @endphp
                                    <div
                                        class="timeline-step {{ $isCompleted ? 'completed' : '' }} {{ $isActive ? 'active' : '' }}">
                                        <div class="timeline-icon">
                                            <i class="fa-solid fa-{{ $step['icon'] }}"></i>
                                        </div>
                                        <div class="fw-semibold mb-1">{{ $step['label'] }}</div>
                                        @if ($step['time'])
                                            <div class="small text-muted">{{ $step['time']->format('d/m/Y H:i') }}</div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="row g-4">
                <!-- Left Column -->
                <div class="col-lg-8">
                    <!-- Order Items (Read Only) -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h5 class="mb-0 fw-semibold">
                                <i class="fa-solid fa-box text-primary me-2"></i>Sản phẩm đã đặt
                                <span class="badge bg-primary ms-2">{{ $order->orderItems->count() }} sản phẩm</span>
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="px-4 py-3">Sản phẩm</th>
                                            <th class="px-4 py-3 text-center">Đơn giá</th>
                                            <th class="px-4 py-3 text-center">Số lượng</th>
                                            <th class="px-4 py-3 text-end">Thành tiền</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($order->orderItems as $item)
                                            <tr>
                                                <td class="px-4 py-3">
                                                    <div class="d-flex align-items-center">
                                                        @if ($item->product->image)
                                                            <img src="{{ asset('storage/' . $item->product->image) }}"
                                                                alt="{{ $item->product->name }}" class="rounded me-3"
                                                                style="width: 50px; height: 50px; object-fit: cover;">
                                                        @else
                                                            <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center"
                                                                style="width: 50px; height: 50px;">
                                                                <i class="fa-solid fa-image text-muted"></i>
                                                            </div>
                                                        @endif
                                                        <div>
                                                            <div class="fw-semibold">{{ $item->product->name }}</div>
                                                            @if ($item->variant)
                                                                <div class="small text-muted">{{ $item->variant->name }}
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    {{ number_format($item->price) }}₫
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    <span class="badge bg-primary px-3 py-2">{{ $item->quantity }}</span>
                                                </td>
                                                <td class="px-4 py-3 text-end fw-bold text-primary">
                                                    {{ number_format($item->price * $item->quantity) }}₫
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="bg-light">
                                        <tr>
                                            <td colspan="3" class="px-4 py-3 text-end fw-semibold">Tạm tính:</td>
                                            <td class="px-4 py-3 text-end fw-bold" id="subtotal">
                                                {{ number_format($order->orderItems->sum(fn($item) => $item->price * $item->quantity)) }}₫
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Shipping Fee -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h5 class="mb-0 fw-semibold">
                                <i class="fa-solid fa-truck text-primary me-2"></i>Phí vận chuyển
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row align-items-end">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        Phí ship <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group input-group-lg">
                                        <input type="number" name="shipping_fee"
                                            class="form-control @error('shipping_fee') is-invalid @enderror"
                                            value="{{ old('shipping_fee', $order->shipping_fee) }}" min="0"
                                            step="1000" id="shippingFeeInput">
                                        <span class="input-group-text">₫</span>
                                        @error('shipping_fee')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <small class="text-muted">Nhập số tiền phí vận chuyển</small>
                                </div>
                                <div class="col-md-6">
                                    <div class="alert alert-info mb-0">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <strong>Tổng đơn hàng:</strong>
                                            <span class="fs-4 fw-bold" id="totalAmount">
                                                {{ number_format($order->total_amount) }}₫
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom py-3">
                            <h5 class="mb-0 fw-semibold">
                                <i class="fa-solid fa-note-sticky text-primary me-2"></i>Ghi chú
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Ghi chú của khách hàng</label>
                                    <textarea name="customer_note" class="form-control @error('customer_note') is-invalid @enderror" rows="3"
                                        placeholder="Ghi chú từ khách hàng..." readonly>{{ old('customer_note', $order->customer_note) }}</textarea>
                                    @error('customer_note')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Ghi chú nội bộ (Admin)</label>
                                    <textarea name="admin_note" class="form-control @error('admin_note') is-invalid @enderror" rows="3"
                                        placeholder="Ghi chú dành cho admin...">{{ old('admin_note', $order->admin_note) }}</textarea>
                                    @error('admin_note')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="col-lg-4">
                    <!-- Status -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h5 class="mb-0 fw-semibold">
                                <i class="fa-solid fa-flag text-primary me-2"></i>Trạng thái đơn hàng
                            </h5>
                        </div>
                        <div class="card-body">
                            <label class="form-label fw-semibold">
                                Trạng thái <span class="text-danger">*</span>
                            </label>
                            <select name="status"
                                class="form-select form-select-lg @error('status') is-invalid @enderror" required>
                                @foreach ($statuses as $status)
                                    <option value="{{ $status->value }}"
                                        {{ old('status', $order->status->value) == $status->value ? 'selected' : '' }}>
                                        {{ $status->label() }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <div class="mt-3 p-3 bg-light rounded">
                                <small class="text-muted">
                                    <i class="fa-solid fa-info-circle me-1"></i>
                                    Thay đổi trạng thái sẽ cập nhật timeline đơn hàng
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Management -->
                    @php $payment = $order->payments->first(); @endphp
                    @if ($payment)
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-white border-bottom py-3">
                                <h5 class="mb-0 fw-semibold">
                                    <i class="fa-solid fa-credit-card text-primary me-2"></i>Quản lý thanh toán
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="text-muted small mb-1">Phương thức</label>
                                    <div class="fw-semibold">
                                        @switch($payment->payment_method->value)
                                            @case('cod')
                                                <i class="fa-solid fa-money-bill-wave text-success me-2"></i>COD
                                            @break

                                            @case('bank')
                                                <i class="fa-solid fa-university text-info me-2"></i>Ngân hàng
                                            @break

                                            @case('card')
                                                <i class="fa-solid fa-credit-card text-primary me-2"></i>Thẻ
                                            @break

                                            @case('wallet')
                                                <i class="fa-solid fa-wallet text-warning me-2"></i>Ví điện tử
                                            @break
                                        @endswitch
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="text-muted small mb-1">Trạng thái thanh toán</label>
                                    <div>
                                        <span class="badge bg-{{ $payment->status->color() }} px-3 py-2">
                                            {{ $payment->status->label() }}
                                        </span>
                                    </div>
                                </div>

                                @if ($payment->requires_manual_verification && !$payment->is_verified)
                                    <div class="alert alert-warning">
                                        <i class="fa-solid fa-exclamation-triangle me-2"></i>
                                        <strong>Cần xác nhận thanh toán!</strong>
                                    </div>
                                @endif

                                @if ($payment->canBeVerified())
                                    <div class="d-grid gap-2">
                                        <button type="button" class="btn btn-success" data-bs-toggle="modal"
                                            data-bs-target="#confirmPaymentModal">
                                            <i class="fa-solid fa-check me-2"></i>Xác nhận thanh toán
                                        </button>
                                        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal"
                                            data-bs-target="#rejectPaymentModal">
                                            <i class="fa-solid fa-times me-2"></i>Từ chối thanh toán
                                        </button>
                                    </div>
                                @endif

                                @if ($payment->is_verified)
                                    <div class="alert alert-success mt-3 mb-0">
                                        <i class="fa-solid fa-shield-check me-2"></i>
                                        <strong>Đã xác nhận</strong>
                                        @if ($payment->verifier)
                                            <div class="small mt-1">Bởi: {{ $payment->verifier->name }}</div>
                                            <div class="small">{{ $payment->verified_at->format('d/m/Y H:i') }}</div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Customer Info (Read Only) -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h5 class="mb-0 fw-semibold">
                                <i class="fa-solid fa-user text-primary me-2"></i>Khách hàng
                            </h5>
                        </div>
                        <div class="card-body">
                            @if ($order->user)
                                <div class="text-center">
                                    <div class="avatar-lg mx-auto mb-3">
                                        <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center"
                                            style="width: 70px; height: 70px;">
                                            <i class="fa-solid fa-user text-primary fs-2"></i>
                                        </div>
                                    </div>
                                    <h6 class="fw-bold mb-1">{{ $order->user->name }}</h6>
                                    <p class="text-muted small mb-0">{{ $order->user->email }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fa-solid fa-save me-2"></i> Lưu thay đổi
                                </button>
                                <a href="{{ route('admin.orders.show', $order->id) }}"
                                    class="btn btn-outline-secondary btn-lg">
                                    <i class="fa-solid fa-times me-2"></i> Hủy bỏ
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Confirm Payment Modal -->
    <div class="modal fade" id="confirmPaymentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.orders.confirm-payment', $order->id) }}" method="POST">
                    @csrf
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">
                            <i class="fa-solid fa-check-circle me-2"></i>Xác nhận thanh toán
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fa-solid fa-info-circle me-2"></i>
                            Xác nhận đơn hàng <strong>#{{ $order->order_number }}</strong> đã thanh toán thành công
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Mã giao dịch (tùy chọn)</label>
                            <input type="text" name="transaction_id" class="form-control"
                                placeholder="Nhập mã giao dịch nếu có...">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Ghi chú xác nhận</label>
                            <textarea name="verification_note" class="form-control" rows="3"
                                placeholder="Ghi chú về việc xác nhận thanh toán..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fa-solid fa-check me-2"></i>Xác nhận
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Reject Payment Modal -->
    <div class="modal fade" id="rejectPaymentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.orders.reject-payment', $order->id) }}" method="POST">
                    @csrf
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">
                            <i class="fa-solid fa-times-circle me-2"></i>Từ chối thanh toán
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-danger">
                            <i class="fa-solid fa-exclamation-triangle me-2"></i>
                            Từ chối thanh toán sẽ tự động hủy đơn hàng <strong>#{{ $order->order_number }}</strong>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Lý do từ chối <span class="text-danger">*</span></label>
                            <textarea name="reason" class="form-control" rows="4" required
                                placeholder="Nhập lý do từ chối thanh toán..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fa-solid fa-times me-2"></i>Từ chối
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const subtotal = {{ $order->orderItems->sum(fn($item) => $item->price * $item->quantity) }};
            const shippingFeeInput = document.getElementById('shippingFeeInput');
            const totalAmountEl = document.getElementById('totalAmount');

            shippingFeeInput.addEventListener('input', function() {
                const shippingFee = parseFloat(this.value) || 0;
                const total = subtotal + shippingFee;
                totalAmountEl.textContent = total.toLocaleString('vi-VN') + '₫';
            });
        });
    </script>
@endpush

@push('styles')
    <style>
        .timeline-container {
            position: relative;
            padding: 40px 0;
        }

        .timeline-step {
            position: relative;
            text-align: center;
            flex: 1;
        }

        .timeline-step::before {
            content: '';
            position: absolute;
            top: 40px;
            left: 50%;
            right: -50%;
            height: 4px;
            background: #e0e0e0;
            z-index: 0;
        }

        .timeline-step:last-child::before {
            display: none;
        }

        .timeline-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 30px;
            color: white;
            position: relative;
            z-index: 1;
            background: #e0e0e0;
            border: 5px solid #fff;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .timeline-step.active .timeline-icon {
            animation: pulse 2s infinite;
            box-shadow: 0 6px 25px rgba(102, 126, 234, 0.4);
        }

        .timeline-step.completed::before {
            background: linear-gradient(to right, #28a745, #20c997);
        }

        .timeline-step.completed .timeline-icon {
            background: linear-gradient(135deg, #28a745, #20c997);
        }

        .timeline-step.active .timeline-icon {
            background: linear-gradient(135deg, #667eea, #764ba2);
            transform: scale(1.1);
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1.1);
            }

            50% {
                transform: scale(1.15);
            }
        }

        .action-btn-group {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .action-card {
            transition: transform 0.2s;
        }

        .action-card:hover {
            transform: translateY(-3px);
        }

        .payment-verification-alert {
            border-left: 4px solid #ffc107;
            animation: glow 2s ease-in-out infinite;
        }

        @keyframes glow {

            0%,
            100% {
                box-shadow: 0 0 5px rgba(255, 193, 7, 0.3);
            }

            50% {
                box-shadow: 0 0 20px rgba(255, 193, 7, 0.6);
            }
        }
    </style>
@endpush --}}



@extends('layouts.admin')

@section('title', 'Chỉnh sửa đơn hàng #' . $order->order_number)

@push('styles')
<style>
    .readonly-section { background: #f8f9fa; border-left: 4px solid #6c757d; }
    .editable-section { border-left: 4px solid #0d6efd; }
    .edit-badge { background: #ffc107; color: #000; padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; }
    .info-label { font-size: 0.85rem; color: #6c757d; font-weight: 600; margin-bottom: 4px; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold text-dark mb-2">
                        <i class="fa-solid fa-pen-to-square text-warning me-2"></i>
                        Chỉnh sửa đơn hàng #{{ $order->order_number }}
                        <span class="edit-badge ms-2">
                            <i class="fa-solid fa-edit me-1"></i>Chế độ chỉnh sửa
                        </span>
                    </h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}">Đơn hàng</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.orders.show', $order->id) }}">#{{ $order->order_number }}</a></li>
                            <li class="breadcrumb-item active">Chỉnh sửa</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-secondary btn-lg">
                        <i class="fa-solid fa-arrow-left me-2"></i> Quay lại
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="alert alert-info border-0 shadow-sm mb-4">
        <div class="d-flex align-items-center">
            <i class="fa-solid fa-info-circle fs-3 me-3"></i>
            <div>
                <strong>Lưu ý:</strong> Bạn chỉ có thể chỉnh sửa trạng thái đơn hàng, phí vận chuyển và ghi chú.
                Thông tin sản phẩm và khách hàng không thể thay đổi.
            </div>
        </div>
    </div>

    <form action="{{ route('admin.orders.update', $order->id) }}" method="POST" id="orderEditForm">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <!-- Left Column - Read Only Information -->
            <div class="col-lg-8">
                <!-- Order Items (Read Only) -->
                <div class="card border-0 shadow-sm mb-4 readonly-section">
                    <div class="card-header bg-light border-bottom py-3">
                        <h5 class="mb-0 fw-semibold text-muted">
                            <i class="fa-solid fa-lock me-2"></i>Sản phẩm đã đặt
                            <span class="badge bg-secondary ms-2">{{ $order->orderItems->count() }} - Không thể chỉnh sửa</span>
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="px-4 py-3">Sản phẩm</th>
                                        <th class="px-4 py-3 text-center">Đơn giá</th>
                                        <th class="px-4 py-3 text-center">Số lượng</th>
                                        <th class="px-4 py-3 text-end">Thành tiền</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($order->orderItems as $item)
                                        <tr>
                                            <td class="px-4 py-3">
                                                <div class="d-flex align-items-center">
                                                    @if ($item->product->image)
                                                        <img src="{{ asset('storage/' . $item->product->image) }}"
                                                            alt="{{ $item->product->name }}" class="rounded me-3"
                                                            style="width: 50px; height: 50px; object-fit: cover;">
                                                    @else
                                                        <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center"
                                                            style="width: 50px; height: 50px;">
                                                            <i class="fa-solid fa-image text-muted"></i>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <div class="fw-semibold">{{ $item->product->name }}</div>
                                                        @if ($item->variant)
                                                            <div class="small text-muted">{{ $item->variant->name }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                {{ number_format($item->price) }}₫
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <span class="badge bg-primary px-3 py-2">{{ $item->quantity }}</span>
                                            </td>
                                            <td class="px-4 py-3 text-end fw-bold text-primary">
                                                {{ number_format($item->price * $item->quantity) }}₫
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-light">
                                    <tr>
                                        <td colspan="3" class="px-4 py-3 text-end fw-semibold">Tạm tính:</td>
                                        <td class="px-4 py-3 text-end fw-bold" id="subtotal">
                                            {{ number_format($order->orderItems->sum(fn($item) => $item->price * $item->quantity)) }}₫
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Shipping Fee (Editable) -->
                <div class="card border-0 shadow-sm mb-4 editable-section">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-semibold">
                            <i class="fa-solid fa-truck text-primary me-2"></i>Phí vận chuyển
                            <span class="badge bg-primary ms-2">Có thể chỉnh sửa</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row align-items-end">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Phí ship <span class="text-danger">*</span>
                                </label>
                                <div class="input-group input-group-lg">
                                    <input type="number" name="shipping_fee"
                                        class="form-control @error('shipping_fee') is-invalid @enderror"
                                        value="{{ old('shipping_fee', $order->shipping_fee) }}"
                                        min="0" step="1000" id="shippingFeeInput">
                                    <span class="input-group-text">₫</span>
                                    @error('shipping_fee')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="text-muted">Nhập số tiền phí vận chuyển</small>
                            </div>
                            <div class="col-md-6">
                                <div class="alert alert-primary mb-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <strong>Tổng đơn hàng:</strong>
                                        <span class="fs-4 fw-bold" id="totalAmount">
                                            {{ number_format($order->total_amount) }}₫
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Customer Note (Read Only) + Admin Note (Editable) -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-semibold">
                            <i class="fa-solid fa-note-sticky text-primary me-2"></i>Ghi chú
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <!-- Customer Note - Read Only -->
                            <div class="col-12">
                                <div class="readonly-section p-3 rounded">
                                    <label class="form-label fw-semibold text-muted mb-2">
                                        <i class="fa-solid fa-lock me-1"></i>Ghi chú của khách hàng (Không thể sửa)
                                    </label>
                                    <div class="p-2 bg-white rounded">
                                        {{ $order->customer_note ?: 'Không có ghi chú' }}
                                    </div>
                                </div>
                            </div>

                            <!-- Admin Note - Editable -->
                            <div class="col-12">
                                <div class="editable-section p-3 rounded">
                                    <label class="form-label fw-semibold">
                                        <i class="fa-solid fa-edit me-1"></i>Ghi chú nội bộ (Admin)
                                        <span class="badge bg-primary">Có thể chỉnh sửa</span>
                                    </label>
                                    <textarea name="admin_note"
                                        class="form-control @error('admin_note') is-invalid @enderror"
                                        rows="4"
                                        placeholder="Nhập ghi chú nội bộ cho đơn hàng này...">{{ old('admin_note', $order->admin_note) }}</textarea>
                                    @error('admin_note')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Ghi chú này chỉ hiển thị cho admin</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Status & Actions -->
            <div class="col-lg-4">
                <!-- Status (Editable) -->
                <div class="card border-0 shadow-sm mb-4 editable-section">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-semibold">
                            <i class="fa-solid fa-flag text-primary me-2"></i>Trạng thái đơn hàng
                            <span class="badge bg-primary">Có thể chỉnh sửa</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <label class="form-label fw-semibold">
                            Chọn trạng thái <span class="text-danger">*</span>
                        </label>
                        <select name="status"
                            class="form-select form-select-lg @error('status') is-invalid @enderror"
                            required id="statusSelect">
                            @foreach ($statuses as $status)
                                <option value="{{ $status->value }}"
                                    {{ old('status', $order->status->value) == $status->value ? 'selected' : '' }}>
                                    {{ $status->label() }}
                                </option>
                            @endforeach
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        <div class="mt-3 p-3 bg-info bg-opacity-10 rounded">
                            <small class="text-info">
                                <i class="fa-solid fa-lightbulb me-1"></i>
                                <strong>Lưu ý:</strong> Thay đổi trạng thái sẽ gửi email tự động đến khách hàng
                            </small>
                        </div>

                        <!-- Current Status Display -->
                        <div class="mt-3 p-3 bg-light rounded">
                            <div class="small text-muted mb-1">Trạng thái hiện tại:</div>
                            <span class="badge bg-{{ $order->status->color() }} px-3 py-2">
                                {{ $order->status->label() }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Payment Info (Read Only) -->
                @php $payment = $order->payments->first(); @endphp
                @if ($payment)
                <div class="card border-0 shadow-sm mb-4 readonly-section">
                    <div class="card-header bg-light border-bottom py-3">
                        <h5 class="mb-0 fw-semibold text-muted">
                            <i class="fa-solid fa-lock me-2"></i>Thanh toán
                            <span class="badge bg-secondary ms-2">Không thể sửa</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="info-label">Phương thức</div>
                            <div class="fw-semibold">{{ $payment->payment_method->label() }}</div>
                        </div>
                        <div class="mb-3">
                            <div class="info-label">Trạng thái thanh toán</div>
                            <span class="badge bg-{{ $payment->status->color() }} px-3 py-2">
                                {{ $payment->status->label() }}
                            </span>
                        </div>

                        @if ($payment->canBeVerified())
                        <div class="alert alert-warning mt-3">
                            <small>
                                <i class="fa-solid fa-exclamation-triangle me-1"></i>
                                Thanh toán cần xác nhận. Vui lòng xử lý trong trang chi tiết.
                            </small>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Customer Info (Read Only) -->
                <div class="card border-0 shadow-sm mb-4 readonly-section">
                    <div class="card-header bg-light border-bottom py-3">
                        <h5 class="mb-0 fw-semibold text-muted">
                            <i class="fa-solid fa-lock me-2"></i>Khách hàng
                            <span class="badge bg-secondary ms-2">Không thể sửa</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        @if ($order->user)
                            <div class="text-center">
                                <div class="avatar-lg mx-auto mb-3">
                                    <div class="rounded-circle bg-secondary bg-opacity-10 d-flex align-items-center justify-content-center"
                                        style="width: 70px; height: 70px;">
                                        <i class="fa-solid fa-user text-secondary fs-2"></i>
                                    </div>
                                </div>
                                <h6 class="fw-bold mb-1 text-muted">{{ $order->user->name }}</h6>
                                <p class="text-muted small mb-0">{{ $order->user->email }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fa-solid fa-save me-2"></i> Lưu thay đổi
                            </button>
                            <a href="{{ route('admin.orders.show', $order->id) }}"
                                class="btn btn-outline-secondary btn-lg">
                                <i class="fa-solid fa-times me-2"></i> Hủy bỏ
                            </a>
                        </div>

                        <hr class="my-3">

                        <div class="small text-muted">
                            <i class="fa-solid fa-info-circle me-1"></i>
                            <strong>Các thay đổi có thể thực hiện:</strong>
                            <ul class="mt-2 mb-0 ps-3">
                                <li>Trạng thái đơn hàng</li>
                                <li>Phí vận chuyển</li>
                                <li>Ghi chú admin</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const subtotal = {{ $order->orderItems->sum(fn($item) => $item->price * $item->quantity) }};
    const shippingFeeInput = document.getElementById('shippingFeeInput');
    const totalAmountEl = document.getElementById('totalAmount');
    const form = document.getElementById('orderEditForm');

    // Update total amount when shipping fee changes
    shippingFeeInput.addEventListener('input', function() {
        const shippingFee = parseFloat(this.value) || 0;
        const total = subtotal + shippingFee;
        totalAmountEl.textContent = total.toLocaleString('vi-VN') + '₫';
    });

    // Confirm before submit
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const currentStatus = '{{ $order->status->value }}';
        const newStatus = document.getElementById('statusSelect').value;
        const statusChanged = currentStatus !== newStatus;

        let message = 'Bạn có chắc chắn muốn lưu các thay đổi?';
        if (statusChanged) {
            const statusNames = {
                'pending': 'Chờ xử lý',
                'paid': 'Đã thanh toán',
                'shipped': 'Đang giao',
                'completed': 'Hoàn thành',
                'cancelled': 'Đã hủy'
            };
            message = `Trạng thái đơn hàng sẽ thay đổi thành <strong>${statusNames[newStatus]}</strong><br>
                      <small class="text-muted">Email thông báo sẽ được gửi tự động đến khách hàng</small>`;
        }

        Swal.fire({
            title: 'Xác nhận lưu thay đổi',
            html: message,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Lưu thay đổi',
            cancelButtonText: 'Hủy',
            confirmButtonColor: '#0d6efd',
            cancelButtonColor: '#6c757d'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });

    // Warning when leaving page with unsaved changes
    let formChanged = false;
    form.querySelectorAll('input, select, textarea').forEach(element => {
        element.addEventListener('change', () => {
            formChanged = true;
        });
    });

    window.addEventListener('beforeunload', (e) => {
        if (formChanged) {
            e.preventDefault();
            e.returnValue = '';
        }
    });

    // Don't warn after successful submit
    form.addEventListener('submit', () => {
        formChanged = false;
    });
});
</script>
@endpush
