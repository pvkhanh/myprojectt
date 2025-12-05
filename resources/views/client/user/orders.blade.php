@extends('client.layouts.master')

@section('title', 'Đơn hàng của tôi')

@section('content')

    <div class="container py-5">

        <h3 class="fw-bold mb-4">Đơn hàng của tôi</h3>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($orders->count())
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Mã đơn</th>
                            <th>Ngày đặt</th>
                            <th>Trạng thái</th>
                            <th>Tổng tiền</th>
                            <th>Thanh toán</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                            <tr>
                                <td>{{ $order->code }}</td>
                                <td>{{ $order->created_at->format('d/m/Y') }}</td>
                                <td>
                                    <span
                                        class="badge
                                    @switch($order->status)
                                        @case('pending') bg-warning @break
                                        @case('processing') bg-info @break
                                        @case('shipping') bg-primary @break
                                        @case('completed') bg-success @break
                                        @case('cancelled') bg-danger @break
                                        @default bg-secondary
                                    @endswitch
                                ">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td>{{ number_format($order->total_amount, 0, '.', ',') }} đ</td>
                                <td>
                                    @if ($order->payment_status == 'paid')
                                        <span class="text-success">Đã thanh toán</span>
                                    @else
                                        <span class="text-danger">Chưa thanh toán</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('client.orders.show', $order->id) }}"
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> Xem
                                    </a>
                                    {{-- Nếu chưa thanh toán và thanh toán Stripe --}}
                                    @if ($order->payment_status != 'paid' && $order->payment_method == 'stripe')
                                        <a href="{{ route('client.payment.stripe', $order->id) }}"
                                            class="btn btn-sm btn-danger">
                                            <i class="bi bi-credit-card"></i> Thanh toán
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-4">
                @include('client.components.pagination', ['paginator' => $orders])
            </div>
        @else
            <div class="text-center py-5 text-muted">
                <i class="bi bi-bag-check" style="font-size: 4rem;"></i>
                <p class="mt-3">Bạn chưa có đơn hàng nào.</p>
                <a href="{{ route('client.home') }}" class="btn btn-primary mt-2">
                    <i class="bi bi-shop"></i> Tiếp tục mua sắm
                </a>
            </div>
        @endif

    </div>

@endsection
