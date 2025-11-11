<!--
  ================================================================
  TEMPLATE: WELCOME EMAIL
  Template Key: welcome-email
  Use Case: Chào mừng người dùng mới đăng ký
  ================================================================
-->
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chào mừng đến với {{ app_name }}</title>
</head>

<body style="margin:0;padding:0;font-family:Arial,sans-serif;background-color:#f4f4f4;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f4f4;padding:20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0"
                    style="background-color:#ffffff;border-radius:10px;overflow:hidden;box-shadow:0 4px 6px rgba(0,0,0,0.1);">

                    <!-- Header -->
                    <tr>
                        <td
                            style="background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);padding:40px;text-align:center;">
                            <h1 style="color:#fff;margin:0;font-size:28px;">Chào mừng bạn!</h1>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding:40px;">
                            <h2 style="color:#333;margin:0 0 20px;">Xin chào {{ username }}!</h2>
                            <p style="color:#666;line-height:1.6;margin:0 0 20px;">
                                Cảm ơn bạn đã đăng ký tài khoản tại <strong>{{ app_name }}</strong>.
                                Chúng tôi rất vui được chào đón bạn!
                            </p>
                            <p style="color:#666;line-height:1.6;margin:0 0 30px;">
                                Bắt đầu khám phá những tính năng tuyệt vời của chúng tôi ngay hôm nay.
                            </p>
                            <div style="text-align:center;">
                                <a href="#"
                                    style="display:inline-block;background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:#fff;padding:15px 40px;text-decoration:none;border-radius:5px;font-weight:bold;">
                                    Bắt đầu ngay
                                </a>
                            </div>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td
                            style="background-color:#f8f9fa;padding:30px;text-align:center;border-top:1px solid #dee2e6;">
                            <p style="color:#999;margin:0 0 10px;font-size:14px;">
                                © 2024 {{ app_name }}. All rights reserved.
                            </p>
                            <p style="color:#999;margin:0;font-size:12px;">
                                Email: {{ email }} | Số điện thoại: 0123-456-789
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>

</html>
