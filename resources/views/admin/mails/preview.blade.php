{{-- <!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview: {{ $mail->subject }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .preview-container {
            max-width: 800px;
            margin: 0 auto;
        }

        .email-preview {
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }

        .email-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .email-body {
            padding: 40px;
            line-height: 1.8;
        }

        .email-footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #dee2e6;
        }

        .info-bar {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }

        .btn-action {
            margin: 5px;
        }

        @media print {
            body {
                background: white;
            }

            .info-bar,
            .btn-action {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="preview-container">
        <!-- Info Bar -->
        <div class="info-bar">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h5 class="mb-2 fw-bold">
                        <i class="fa-solid fa-eye text-primary me-2"></i>
                        Email Preview
                    </h5>
                    <div class="small text-muted">
                        <i class="fa-solid fa-clock me-1"></i>
                        Tạo lúc: {{ $mail->created_at->format('d/m/Y H:i:s') }}
                    </div>
                </div>
                <div>
                    <button onclick="window.print()" class="btn btn-primary btn-action">
                        <i class="fa-solid fa-print me-2"></i> In
                    </button>
                    <button onclick="window.close()" class="btn btn-secondary btn-action">
                        <i class="fa-solid fa-times me-2"></i> Đóng
                    </button>
                </div>
            </div>
        </div>

        <!-- Email Preview -->
        <div class="email-preview">
            <!-- Email Header -->
            <div class="email-header">
                <div class="mb-3">
                    <i class="fa-solid fa-envelope fs-1"></i>
                </div>
                <h2 class="fw-bold mb-2">{{ $mail->subject }}</h2>
                <div class="small">
                    <i class="fa-solid fa-paper-plane me-2"></i>
                    Từ: {{ $mail->sender_email }}
                </div>
            </div>

            <!-- Email Body -->
            <div class="email-body">
                {!! $mail->content !!}
            </div>

            <!-- Email Footer -->
            <div class="email-footer">
                <div class="mb-3">
                    <strong>{{ config('app.name') }}</strong>
                </div>
                <div class="small text-muted mb-3">
                    Email này được gửi từ hệ thống quản lý mail
                </div>
                <div class="d-flex justify-content-center gap-3">
                    <a href="#" class="text-muted"><i class="fa-brands fa-facebook"></i></a>
                    <a href="#" class="text-muted"><i class="fa-brands fa-twitter"></i></a>
                    <a href="#" class="text-muted"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#" class="text-muted"><i class="fa-brands fa-linkedin"></i></a>
                </div>
            </div>
        </div>

        <!-- Mail Info -->
        <div class="info-bar mt-3">
            <h6 class="fw-bold mb-3">
                <i class="fa-solid fa-info-circle text-info me-2"></i>
                Thông Tin Mail
            </h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="small text-muted">Loại Mail:</label>
                    <div>
                        @php
                            $typeConfig = [
                                'system' => ['class' => 'primary', 'text' => 'System'],
                                'user' => ['class' => 'info', 'text' => 'User'],
                                'marketing' => ['class' => 'success', 'text' => 'Marketing'],
                            ];
                            $config = $typeConfig[$mail->type->value] ?? [
                                'class' => 'secondary',
                                'text' => $mail->type->value,
                            ];
                        @endphp
                        <span class="badge bg-{{ $config['class'] }}">{{ $config['text'] }}</span>
                    </div>
                </div>
                @if ($mail->template_key)
                    <div class="col-md-6">
                        <label class="small text-muted">Template Key:</label>
                        <div><code>{{ $mail->template_key }}</code></div>
                    </div>
                @endif
                <div class="col-md-6">
                    <label class="small text-muted">Tổng người nhận:</label>
                    <div class="fw-bold text-primary">{{ $mail->recipients->count() }} người</div>
                </div>
                <div class="col-md-6">
                    <label class="small text-muted">Đã gửi:</label>
                    <div class="fw-bold text-success">{{ $mail->recipients->where('status', 'sent')->count() }} mail
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html> --}}



<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview: {{ $mail->subject }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .preview-container {
            max-width: 800px;
            margin: 0 auto;
        }

        .email-preview {
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }

        .email-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .email-body {
            padding: 40px;
            line-height: 1.8;
        }

        .email-footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #dee2e6;
        }

        .info-bar {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }

        .btn-action {
            margin: 5px;
        }

        @media print {
            body {
                background: white;
            }

            .info-bar,
            .btn-action {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="preview-container">
        <!-- Info Bar -->
        <div class="info-bar">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h5 class="mb-2 fw-bold">
                        <i class="fa-solid fa-eye text-primary me-2"></i>
                        Email Preview
                    </h5>
                    <div class="small text-muted">
                        <i class="fa-solid fa-clock me-1"></i>
                        Tạo lúc: {{ $mail->created_at->format('d/m/Y H:i:s') }}
                    </div>
                </div>
                <div>
                    <button onclick="window.print()" class="btn btn-primary btn-action">
                        <i class="fa-solid fa-print me-2"></i> In
                    </button>
                    <button onclick="window.close()" class="btn btn-secondary btn-action">
                        <i class="fa-solid fa-times me-2"></i> Đóng
                    </button>
                </div>
            </div>
        </div>

        <!-- Email Preview -->
        <div class="email-preview">
            <!-- Email Header -->
            <div class="email-header">
                <div class="mb-3">
                    <i class="fa-solid fa-envelope fs-1"></i>
                </div>
                <h2 class="fw-bold mb-2">{{ $mail->subject }}</h2>
                <div class="small">
                    <i class="fa-solid fa-paper-plane me-2"></i>
                    Từ: {{ $mail->sender_email }}
                </div>
            </div>

            <!-- Email Body -->
            <div class="email-body">
                {!! $content !!}
            </div>

            <!-- Email Footer -->
            <div class="email-footer">
                <div class="mb-3">
                    <strong>{{ config('app.name') }}</strong>
                </div>
                <div class="small text-muted mb-3">
                    Email này được gửi từ hệ thống quản lý mail
                </div>
                <div class="d-flex justify-content-center gap-3">
                    <a href="#" class="text-muted"><i class="fa-brands fa-facebook"></i></a>
                    <a href="#" class="text-muted"><i class="fa-brands fa-twitter"></i></a>
                    <a href="#" class="text-muted"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#" class="text-muted"><i class="fa-brands fa-linkedin"></i></a>
                </div>
            </div>
        </div>

        <!-- Mail Info -->
        <div class="info-bar mt-3">
            <h6 class="fw-bold mb-3">
                <i class="fa-solid fa-info-circle text-info me-2"></i>
                Thông Tin Mail
            </h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="small text-muted">Loại Mail:</label>
                    <div>
                        @php
                            $typeConfig = [
                                'system' => ['class' => 'primary', 'text' => 'System'],
                                'user' => ['class' => 'info', 'text' => 'User'],
                                'marketing' => ['class' => 'success', 'text' => 'Marketing'],
                            ];
                            $config = $typeConfig[$mail->type->value] ?? [
                                'class' => 'secondary',
                                'text' => $mail->type->value,
                            ];
                        @endphp
                        <span class="badge bg-{{ $config['class'] }}">{{ $config['text'] }}</span>
                    </div>
                </div>
                @if ($mail->template_key)
                    <div class="col-md-6">
                        <label class="small text-muted">Template Key:</label>
                        <div><code>{{ $mail->template_key }}</code></div>
                    </div>
                @endif
                <div class="col-md-6">
                    <label class="small text-muted">Tổng người nhận:</label>
                    <div class="fw-bold text-primary">{{ $mail->recipients->count() }} người</div>
                </div>
                <div class="col-md-6">
                    <label class="small text-muted">Trạng thái người nhận:</label>
                    <div class="d-flex flex-wrap gap-1">
                        @foreach ($mail->recipients as $recipient)
                            @php
                                // Lấy giá trị string từ Enum nếu là Enum
                                $statusValue =
                                    $recipient->status instanceof \App\Enums\MailRecipientStatus
                                        ? $recipient->status->value
                                        : $recipient->status ?? 'pending';

                                // Gán màu tương ứng
                                $statusClass = match ($statusValue) {
                                    'sent' => 'success',
                                    'failed' => 'danger',
                                    'pending' => 'secondary',
                                    default => 'secondary',
                                };

                                // Viết hoa chữ cái đầu
                                $statusText = ucfirst($statusValue);
                            @endphp

                            <span class="badge bg-{{ $statusClass }}">
                                {{ $statusText }}
                            </span>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
