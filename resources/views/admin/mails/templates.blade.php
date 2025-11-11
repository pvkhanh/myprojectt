templates.blade.php
@extends('layouts.admin')

@section('title', 'Thư Viện Templates')

@push('styles')
    <style>
        .template-card {
            transition: all 0.3s;
            cursor: pointer;
        }

        .template-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }
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
                            <i class="fa-solid fa-file-lines text-primary me-2"></i>
                            Thư Viện Templates
                        </h2>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.mails.dashboard') }}">Mail System</a>
                                </li>
                                <li class="breadcrumb-item active">Templates</li>
                            </ol>
                        </nav>
                    </div>
                    <div>
                        <a href="{{ route('admin.mails.index') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="fa-solid fa-arrow-left me-2"></i> Quay lại
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Templates Grid -->
        <div class="row g-4">
            @forelse($templates as $templateKey => $mails)
                @php
                    $firstMail = $mails->first();
                    $usageCount = $mails->count();
                @endphp
                <div class="col-lg-4 col-md-6">
                    <div class="card border-0 shadow-sm h-100 template-card">
                        <div class="card-header bg-white border-bottom py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0 fw-semibold text-primary">
                                    <i class="fa-solid fa-file-code me-2"></i>{{ $templateKey }}
                                </h5>
                                <span class="badge bg-primary">{{ $usageCount }}x</span>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <label class="small text-muted fw-semibold">Tiêu đề mẫu:</label>
                                <div class="fw-semibold">{{ $firstMail->subject }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="small text-muted fw-semibold">Loại:</label>
                                <div>
                                    @php
                                        $typeConfig = [
                                            'system' => ['class' => 'primary', 'text' => 'System'],
                                            'user' => ['class' => 'info', 'text' => 'User'],
                                            'marketing' => ['class' => 'success', 'text' => 'Marketing'],
                                        ];
                                        $config = $typeConfig[$firstMail->type->value] ?? [
                                            'class' => 'secondary',
                                            'text' => $firstMail->type->value,
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $config['class'] }}">{{ $config['text'] }}</span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="small text-muted fw-semibold">Nội dung mẫu:</label>
                                <div class="small text-muted" style="max-height: 100px; overflow: hidden;">
                                    {{ Str::limit(strip_tags($firstMail->content), 150) }}
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="small text-muted fw-semibold">Lần sử dụng gần nhất:</label>
                                <div class="small">
                                    {{ $mails->sortByDesc('created_at')->first()->created_at->format('d/m/Y H:i') }}</div>
                            </div>
                        </div>
                        <div class="card-footer bg-white border-top py-3">
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.mails.create', ['template' => $templateKey]) }}"
                                    class="btn btn-primary btn-sm flex-fill">
                                    <i class="fa-solid fa-plus me-1"></i> Sử dụng Template
                                </a>
                                <a href="{{ route('admin.mails.preview', $firstMail->id) }}" target="_blank"
                                    class="btn btn-outline-info btn-sm">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center py-5">
                            <i class="fa-solid fa-inbox fs-1 text-muted opacity-50 d-block mb-3"></i>
                            <h5 class="text-muted">Chưa có template nào</h5>
                            <p class="text-muted mb-4">Tạo mail với template_key để xây dựng thư viện template</p>
                            <a href="{{ route('admin.mails.create') }}" class="btn btn-primary">
                                <i class="fa-solid fa-plus me-2"></i> Tạo Mail Mới
                            </a>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
@endsection
