<!-- ================================================================
TEMPLATE: WELCOME EMAIL
Template Key: welcome-email
Use Case: Gửi mail chào mừng người dùng mới đăng ký
Author: ChatGPT (optimized for Laravel Mail)
================================================================ -->
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chào mừng đến với {{ app_name }}</title>
</head>

<body style="margin:0;padding:0;background-color:#f8fafc;font-family:'Segoe UI',Roboto,Helvetica,Arial,sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f8fafc;padding:30px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0"
                    style="background-color:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 6px 16px rgba(0,0,0,0.08);">

                    <!-- HEADER -->
                    <tr>
                        <td align="center"
                            style="background:linear-gradient(135deg,#6c63ff 0%,#48c6ef 100%);padding:45px 20px;">
                            <img src="{{ asset('logo.png') }}" alt="{{ app_name }}" width="80"
                                style="margin-bottom:15px;border-radius:50%;">
                            <h1 style="color:#fff;font-size:28px;margin:0;">Chào mừng đến với {{ app_name }} 🎉</h1>
                        </td>
                    </tr>

                    <!-- BODY -->
                    <tr>
                        <td style="padding:40px 50px;">
                            <h2 style="color:#2d3748;font-size:22px;margin-bottom:15px;">
                                Xin chào {{ username }},
                            </h2>
                            <p style="color:#4a5568;font-size:16px;line-height:1.7;margin:0 0 20px;">
                                Cảm ơn bạn đã đăng ký tài khoản tại <strong>{{ app_name }}</strong>!
                                Chúng tôi rất vui mừng được chào đón bạn đến với cộng đồng của chúng tôi 💙
                            </p>
                            <p style="color:#4a5568;font-size:16px;line-height:1.7;margin:0 0 30px;">
                                Từ bây giờ, bạn có thể truy cập, khám phá sản phẩm và trải nghiệm những tính năng tuyệt vời mà chúng tôi mang lại.
                            </p>

                            <div style="text-align:center;">
                                <a href="{{ url('/') }}"
                                    style="background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);
                                    color:#fff;text-decoration:none;padding:14px 40px;
                                    border-radius:6px;font-weight:bold;font-size:16px;
                                    display:inline-block;">
                                    Bắt đầu ngay →
                                </a>
                            </div>
                        </td>
                    </tr>

                    <!-- QUOTE / MOTIVATION -->
                    <tr>
                        <td align="center" style="background-color:#f1f5f9;padding:25px 40px;">
                            <blockquote style="margin:0;font-style:italic;color:#718096;font-size:15px;">
                                “Thành công bắt đầu bằng một bước nhỏ — và bạn đã bước đầu tiên rồi.”
                            </blockquote>
                        </td>
                    </tr>

                    <!-- FOOTER -->
                    <tr>
                        <td align="center"
                            style="background-color:#ffffff;padding:25px 20px;border-top:1px solid #e2e8f0;">
                            <p style="color:#a0aec0;font-size:14px;margin:0 0 6px;">
                                © {{ date('Y') }} {{ app_name }}. Mọi quyền được bảo lưu.
                            </p>
                            <p style="color:#a0aec0;font-size:13px;margin:0;">
                                Email: {{ email }} · Hotline: 0123 456 789
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>

</html>

{{-- 
<img src="https://yourdomain.com/images/logo.png" alt="{{ app_name }}"> --}}
