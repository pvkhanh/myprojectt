@extends('client.layouts.master')

@section('title', 'Quản lý địa chỉ')

@section('content')

<div class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">Địa chỉ giao hàng</h3>
        <a href="{{ route('client.addresses.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Thêm địa chỉ mới
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($addresses->count())
        <div class="row g-3">
            @foreach($addresses as $address)
                <div class="col-md-6">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h5 class="fw-bold mb-1">
                                        {{ $address->name }}
                                        @if($address->is_default)
                                            <span class="badge bg-success">Mặc định</span>
                                        @endif
                                    </h5>
                                    <p class="mb-0">
                                        {{ $address->address }}, {{ $address->city }}, {{ $address->province }}, {{ $address->country }}
                                    </p>
                                    <p class="mb-0 text-muted">
                                        Điện thoại: {{ $address->phone }}
                                    </p>
                                </div>
                                <div class="text-end">
                                    <a href="{{ route('client.addresses.edit', $address->id) }}" class="btn btn-sm btn-outline-primary mb-1">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('client.addresses.destroy', $address->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Bạn có chắc muốn xóa địa chỉ này?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            @include('client.components.pagination', ['paginator' => $addresses])
        </div>
    @else
        <div class="text-center text-muted py-5">
            <i class="bi bi-geo-alt" style="font-size: 4rem;"></i>
            <p class="mt-3">Bạn chưa có địa chỉ giao hàng nào.</p>
            <a href="{{ route('client.addresses.create') }}" class="btn btn-primary mt-2">
                <i class="bi bi-plus-lg"></i> Thêm địa chỉ mới
            </a>
        </div>
    @endif

</div>

@endsection
