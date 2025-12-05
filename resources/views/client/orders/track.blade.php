@extends('client.layouts.master')

@section('title', 'Tra cứu đơn hàng')

@section('content')

<div class="container py-5">

    <div class="row justify-content-center">
        <div class="col-md-7">

            <h2 class="fw-bold text-center mb-4">Tra cứu đơn hàng</h2>

            {{-- Form tìm kiếm --}}
            <div class="card shadow border-0 mb-4">
                <div class="card-body p-4">

                    <form method="GET" action="{{ route('client.order.track') }}">
                        <div class="mb-3">
                            <label class="form-label">Mã đơn hàng</label>
                            <input type="text" name="code" class="form-control"
                                   placeholder="VD: ORD20241234"
                                   value="{{ request('code') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email đặt hàng</label>
                            <input type="email" name="email" class="form-control"
                                   placeholder="name@example.com"
                                   value="{{ request('email') }}" required>
                        </div>

                        <button class="btn btn-primary w-100">
                            <i class="bi bi-search"></i> Tra cứu
                        </button>
                    </form>

                </div>
            </div>

            {{-- Kết quả --}}
            @isset($order)

                <div class="card shadow border-0">
                    <div class="card-body p-4">

                        <h5 class="fw-bold mb-3">Kết quả tra cứu</h5>

                        <div class="mb-3">
                            <strong>Mã đơn hàng:</strong> {{ $order->code }} <br>
                            <strong>Ngày đặt:</strong> {{ $order->created_at->format('d/m/Y') }} <br>
                            <strong>Trạng thái:</strong>
                            <span class="badge bg-success">{{ $order->status_text }}</span>
                        </div>

                        <hr>

                        {{-- TIẾN TRÌNH ĐƠN HÀNG --}}
                        <h6 class="fw-bold mb-3">Tiến trình đơn hàng</h6>

                        <ul class="timeline">
                            <li class="{{ $order->status >= 1 ? 'active' : '' }}">
                                <span class="title">Đã tiếp nhận</span>
                                <span class="date">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                            </li>

                            <li class="{{ $order->status >= 2 ? 'active' : '' }}">
                                <span class="title">Đang xử lý</span>
                            </li>

                            <li class="{{ $order->status >= 3 ? 'active' : '' }}">
                                <span class="title">Đang vận chuyển</span>
                            </li>

                            <li class="{{ $order->status >= 4 ? 'active' : '' }}">
                                <span class="title">Đã giao hàng</span>
                                @if($order->status >= 4)
                                <span class="date">
                                    {{ $order->delivered_at ? $order->delivered_at->format('d/m/Y H:i') : '' }}
                                </span>
                                @endif
                            </li>
                        </ul>

                    </div>
                </div>

            @endisset

            @if(isset($notfound) && $notfound)
                <div class="alert alert-danger text-center mt-4">
                    <i class="bi bi-exclamation-octagon"></i>
                    Không tìm thấy đơn hàng. Vui lòng kiểm tra lại thông tin.
                </div>
            @endif

        </div>
    </div>

</div>

@endsection


{{-- Timeline CSS --}}
@push('styles')
<style>
    ul.timeline {
        list-style: none;
        margin: 0;
        padding: 0;
        border-left: 3px solid #ddd;
        padding-left: 20px;
        position: relative;
    }
    ul.timeline li {
        margin-bottom: 25px;
        position: relative;
    }
    ul.timeline li::before {
        content: "";
        width: 15px;
        height: 15px;
        background: #ddd;
        border-radius: 50%;
        position: absolute;
        left: -28px;
        top: 2px;
    }
    ul.timeline li.active::before {
        background: #0d6efd;
    }
    ul.timeline li .title {
        font-weight: 600;
    }
    ul.timeline li .date {
        font-size: 0.9rem;
        color: #666;
        margin-left: 5px;
    }
</style>
@endpush
