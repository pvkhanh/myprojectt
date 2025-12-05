@extends('client.layouts.master')

@section('title', 'Đánh giá sản phẩm')

@section('content')

    <div class="container py-5">

        <div class="row">
            <div class="col-md-8">

                <h3 class="fw-bold mb-4">Đánh giá sản phẩm: {{ $product->title }}</h3>

                {{-- Tổng quan rating --}}
                <div class="mb-4 d-flex align-items-center">
                    @include('client.components.rating-stars', [
                        'rating' => $product->avg_rating,
                        'count' => $product->reviews_count,
                    ])
                </div>

                {{-- Danh sách đánh giá --}}
                @forelse($reviews as $review)
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-bold">{{ $review->user->name ?? 'Khách' }}</span>
                                <span class="text-muted" style="font-size: 0.85rem">
                                    {{ $review->created_at->format('d/m/Y H:i') }}
                                </span>
                            </div>
                            <div class="mb-2">
                                @include('client.components.rating-stars', [
                                    'rating' => $review->rating,
                                ])
                            </div>
                            <p>{{ $review->comment }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-muted">Chưa có đánh giá nào cho sản phẩm này.</p>
                @endforelse

                {{-- Phân trang --}}
                @if ($reviews->hasPages())
                    @include('client.components.pagination', ['paginator' => $reviews])
                @endif

            </div>

            {{-- Form gửi review --}}
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">

                        <h5 class="fw-bold mb-3">Viết đánh giá</h5>

                        @auth
                            <form action="{{ route('client.reviews.store', $product->id) }}" method="POST">
                                @csrf

                                <div class="mb-3">
                                    <label class="form-label">Đánh giá</label>
                                    <select name="rating" class="form-select" required>
                                        <option value="">Chọn sao</option>
                                        @for ($i = 1; $i <= 5; $i++)
                                            <option value="{{ $i }}">{{ $i }} sao</option>
                                        @endfor
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Bình luận</label>
                                    <textarea name="comment" class="form-control" rows="4" required></textarea>
                                </div>

                                <button class="btn btn-primary w-100">Gửi đánh giá</button>
                            </form>
                        @else
                            <p>Vui lòng <a href="{{ route('login') }}">đăng nhập</a> để viết đánh giá.</p>
                        @endauth

                    </div>
                </div>
            </div>

        </div>

    </div>

@endsection
