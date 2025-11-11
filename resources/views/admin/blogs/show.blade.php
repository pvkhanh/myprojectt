@extends('layouts.admin')

@section('title', 'Chi tiết Blog')

@section('content')
<div class="container-fluid py-4">
    <h1>{{ $blog->title }}</h1>
    <p><strong>Tác giả:</strong> {{ $blog->author->name }}</p>
    <p><strong>Trạng thái:</strong> {{ $blog->status->label() }}</p>
    <div>
        {!! $blog->content !!}
    </div>

    <h3>Danh mục</h3>
    <ul>
        @foreach ($blog->categories as $category)
            <li>{{ $category->name }}</li>
        @endforeach
    </ul>

    <h3>Ảnh</h3>
    <div class="row">
        @foreach ($blog->images as $image)
            <div class="col-3 mb-2">
                <img src="{{ asset('storage/' . $image->path) }}" class="img-fluid" alt="">
            </div>
        @endforeach
    </div>

    <a href="{{ route('admin.blogs.index') }}" class="btn btn-secondary mt-3">Quay lại</a>
</div>
@endsection
