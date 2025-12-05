{{-- @if ($cartItems->isEmpty())
    <p class="text-center text-muted">Chưa có sản phẩm nào</p>
@else
    @foreach ($cartItems as $item)
        <div class="d-flex mb-3">
            <img src="{{ asset('storage/' . ($item->product->images->first()->path ?? 'default.jpg')) }}" width="55"
                class="rounded">

            <div class="ms-2">
                <p class="mb-0">{{ $item->product->name }}</p>
                <small>x{{ $item->quantity }}</small>
            </div>
        </div>
    @endforeach
@endif --}}
<div class="mini-cart p-3">
    @forelse($cartItems as $item)
        <div class="d-flex mb-3 gap-3">
            <img src="{{ $item->product->main_image }}" width="70" height="70" class="rounded">

            <div class="flex-grow-1">
                <h6 class="mb-1">{{ $item->product->name }}</h6>
                <small class="text-muted">
                    SL: {{ $item->quantity }} × {{ number_format($item->price) }}đ
                </small>
            </div>
        </div>
    @empty
        <p class="text-center text-muted my-3">Giỏ hàng trống</p>
    @endforelse

    <a href="{{ route('cart.index') }}" class="btn btn-primary w-100 mt-3">
        Xem giỏ hàng
    </a>
</div>
