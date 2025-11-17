@extends('layouts.admin')

@section('title', 'Preview Mail')

@push('styles')
    <style>
        /* Reset toàn bộ styles cho preview container */
        .preview-container {
            background: #f4f4f4;
            min-height: 100vh;
            padding: 20px;
        }

        .preview-wrapper {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .preview-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 30px;
            border-bottom: 3px solid #5568d3;
        }

        .preview-header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }

        .preview-header .meta {
            margin-top: 10px;
            font-size: 14px;
            opacity: 0.9;
        }

        /* CRITICAL: Scope CSS chỉ cho mail content */
        .mail-content-scope {
            /* Reset tất cả để không bị ảnh hưởng bởi CSS global */
            all: initial;
            
            /* Set lại các styles cơ bản */
            display: block;
            padding: 30px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background: white;
        }

        /* Revert lại các styles cho các elements bên trong */
        .mail-content-scope * {
            all: revert;
        }

        /* Styles cho images trong mail */
        .mail-content-scope img {
            max-width: 100%;
            height: auto;
            display: block;
            margin: 10px 0;
            border-radius: 4px;
        }

        /* Styles cho tables trong mail */
        .mail-content-scope table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        .mail-content-scope table td,
        .mail-content-scope table th {
            padding: 10px;
            border: 1px solid #dee2e6;
        }

        /* Styles cho links trong mail */
        .mail-content-scope a {
            color: #667eea;
            text-decoration: none;
        }

        .mail-content-scope a:hover {
            text-decoration: underline;
        }

        /* Styles cho headings trong mail */
        .mail-content-scope h1,
        .mail-content-scope h2,
        .mail-content-scope h3 {
            margin-top: 20px;
            margin-bottom: 10px;
            font-weight: 600;
            color: #333;
        }

        /* Styles cho paragraphs trong mail */
        .mail-content-scope p {
            margin: 10px 0;
        }

        /* Styles cho lists trong mail */
        .mail-content-scope ul,
        .mail-content-scope ol {
            margin: 10px 0;
            padding-left: 30px;
        }

        .mail-content-scope li {
            margin: 5px 0;
        }

        /* Preview footer */
        .preview-footer {
            background: #f8f9fa;
            padding: 20px 30px;
            border-top: 1px solid #dee2e6;
            text-align: center;
        }

        /* Action buttons */
        .preview-actions {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            display: flex;
            gap: 10px;
        }

        .preview-actions .btn {
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        /* Device preview toggle */
        .device-toggle {
            text-align: center;
            padding: 15px;
            background: white;
            border-bottom: 1px solid #dee2e6;
        }

        .device-toggle .btn-group {
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        /* Mobile preview */
        @media (max-width: 600px) {
            .preview-wrapper {
                margin: 0;
                border-radius: 0;
            }
            
            .mail-content-scope {
                padding: 15px;
            }
        }
    </style>
@endpush

@section('content')
    <div class="preview-container">
        <!-- Action Buttons -->
        <div class="preview-actions">
            <a href="{{ route('admin.mails.edit', $mail->id) }}" class="btn btn-warning">
                <i class="fa-solid fa-pen me-2"></i> Chỉnh sửa
            </a>
            <a href="{{ route('admin.mails.show', $mail->id) }}" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left me-2"></i> Quay lại
            </a>
            <button onclick="window.print()" class="btn btn-info">
                <i class="fa-solid fa-print me-2"></i> In
            </button>
        </div>

        <!-- Device Toggle -->
        <div class="device-toggle">
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-outline-primary active" onclick="setPreviewWidth('100%')">
                    <i class="fa-solid fa-desktop me-1"></i> Desktop
                </button>
                <button type="button" class="btn btn-outline-primary" onclick="setPreviewWidth('600px')">
                    <i class="fa-solid fa-tablet me-1"></i> Tablet
                </button>
                <button type="button" class="btn btn-outline-primary" onclick="setPreviewWidth('375px')">
                    <i class="fa-solid fa-mobile me-1"></i> Mobile
                </button>
            </div>
        </div>

        <!-- Preview Wrapper -->
        <div class="preview-wrapper" id="previewWrapper">
            <!-- Mail Header Info -->
            <div class="preview-header">
                <h1>{{ $mail->subject }}</h1>
                <div class="meta">
                    <div><strong>Từ:</strong> {{ $mail->sender_email }}</div>
                    <div><strong>Loại:</strong> {{ ucfirst($mail->type->value) }}</div>
                    <div><strong>Ngày tạo:</strong> {{ $mail->created_at->format('d/m/Y H:i') }}</div>
                    @if($mail->template_key)
                        <div><strong>Template:</strong> {{ $mail->template_key }}</div>
                    @endif
                </div>
            </div>

            <!-- Mail Content - SCOPED CSS -->
            <div class="mail-content-scope">
                {!! $wrappedContent ?? $content !!}
            </div>

            <!-- Mail Footer Info -->
            <div class="preview-footer">
                <p class="mb-2 text-muted">
                    <i class="fa-solid fa-info-circle me-1"></i>
                    Đây là bản preview. Email thực tế có thể hiển thị khác tùy thuộc vào email client.
                </p>
                <p class="mb-0">
                    <small class="text-muted">
                        Mail ID: #{{ $mail->id }} | 
                        {{ $mail->recipients->count() }} người nhận
                    </small>
                </p>
            </div>
        </div>

        <!-- Variables Info (if any) -->
        @if($mail->variables)
            <div class="preview-wrapper mt-4">
                <div class="p-4">
                    <h5 class="mb-3">
                        <i class="fa-solid fa-code me-2"></i>Variables được sử dụng
                    </h5>
                    <pre class="bg-light p-3 rounded"><code>{{ json_encode($mail->variables, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        function setPreviewWidth(width) {
            const wrapper = document.getElementById('previewWrapper');
            wrapper.style.maxWidth = width;
            wrapper.style.transition = 'all 0.3s ease';
            
            // Update active button
            document.querySelectorAll('.device-toggle .btn').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.closest('.btn').classList.add('active');
        }

        // Print styles
        window.onbeforeprint = function() {
            document.querySelector('.preview-actions').style.display = 'none';
            document.querySelector('.device-toggle').style.display = 'none';
        };

        window.onafterprint = function() {
            document.querySelector('.preview-actions').style.display = 'flex';
            document.querySelector('.device-toggle').style.display = 'block';
        };
    </script>
@endpush