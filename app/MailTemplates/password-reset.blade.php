<!--
  ================================================================
  TEMPLATE: PASSWORD RESET
  Template Key: password-reset
  Use Case: Đặt lại mật khẩu
  ================================================================
-->
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Đặt lại mật khẩu</title>
</head>

<body style="margin:0;padding:0;font-family:Arial,sans-serif;background-color:#f4f4f4;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f4f4;padding:20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0"
                    style="background-color:#ffffff;border-radius:10px;overflow:hidden;">

                    <!-- Header -->
                    <tr>
                        <td style="background-color:#dc3545;padding:30px;text-align:center;">
                            <h1 style="color:#ffffff;margin:0;font-size:26px;">🔒 Đặt Lại Mật Khẩu</h1>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding:40px;">
                            <h2 style="color:#333;margin:0 0 20px;">Xin chào {{ username }}!</h2>
                            <p style="color:#666;line-height:1.6;margin:0 0 20px;">
                                Chúng tôi nhận được yêu cầu đặt lại mật khẩu cho tài khoản của bạn.
                            </p>
                            <p style="color:#666;line-height:1.6;margin:0 0 30px;">
                                Nếu bạn không yêu cầu thay đổi này, vui lòng bỏ qua email này.
                            </p>

                            <div
                                style="background-color:#fff3cd;border-left:4px solid #ffc107;padding:15px;margin:20px 0;">
                                <p style="color:#856404;margin:0;font-size:14px;">
                                    ⚠️ Link này sẽ hết hạn sau 60 phút
                                </p>
                            </div>

                            <div style="text-align:center;margin-top:30px;">
                                <a href="#"
                                    style="display:inline-block;background-color:#dc3545;color:#fff;padding:15px 40px;text-decoration:none;border-radius:5px;font-weight:bold;">
                                    Đặt Lại Mật Khẩu
                                </a>
                            </div>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color:#f8f9fa;padding:30px;text-align:center;">
                            <p style="color:#999;margin:0;font-size:14px;">
                                Nếu bạn gặp vấn đề với nút trên, copy link này vào trình duyệt:<br>
                                <span
                                    style="color:#666;word-break:break-all;">https://example.com/reset-password?token=abc123</span>
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>

</html>
