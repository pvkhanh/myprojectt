<!--
  ================================================================
  TEMPLATE: NEWSLETTER
  Template Key: newsletter
  Use Case: Bản tin định kỳ
  ================================================================
-->
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Bản tin tháng {{ month }}</title>
</head>

<body style="margin:0;padding:0;font-family:Arial,sans-serif;background-color:#f4f4f4;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f4f4;padding:20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0"
                    style="background-color:#ffffff;border-radius:10px;overflow:hidden;">
                    <!-- Header -->
                    <tr>
                        <td style="padding:40px;text-align:center;border-bottom:2px solid #dee2e6;">
                            <h1 style="color:#333;margin:0 0 10px;font-size:28px;">📰 Bản Tin Tháng {{ month }}
                            </h1>
                            <p style="color:#666;margin:0;font-size:14px;">Cập nhật mới nhất dành cho bạn</p>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding:40px;">
                            <p style="color:#666;line-height:1.6;margin:0 0 30px;">
                                Xin chào <strong>{{ username }}</strong>!
                            </p>

                            <!-- Article 1 -->
                            <div style="margin-bottom:30px;padding-bottom:30px;border-bottom:1px solid #dee2e6;">
                                <h3 style="color:#333;margin:0 0 15px;">
                                    <a href="#" style="color:#667eea;text-decoration:none;">
                                        Bài viết mới: 10 Mẹo Marketing Hiệu Quả
                                    </a>
                                </h3>
                                <p style="color:#666;line-height:1.6;margin:0 0 15px;">
                                    Khám phá những chiến lược marketing mới nhất giúp doanh nghiệp của bạn phát triển...
                                </p>
                                <a href="#" style="color:#667eea;text-decoration:none;font-weight:bold;">
                                    Đọc thêm →
                                </a>
                            </div>

                            <!-- Article 2 -->
                            <div style="margin-bottom:30px;padding-bottom:30px;border-bottom:1px solid #dee2e6;">
                                <h3 style="color:#333;margin:0 0 15px;">
                                    <a href="#" style="color:#667eea;text-decoration:none;">
                                        Cập nhật tính năng: Dashboard Analytics
                                    </a>
                                </h3>
                                <p style="color:#666;line-height:1.6;margin:0 0 15px;">
                                    Chúng tôi vừa ra mắt bảng điều khiển phân tích mới với nhiều tính năng hữu ích...
                                </p>
                                <a href="#" style="color:#667eea;text-decoration:none;font-weight:bold;">
                                    Xem chi tiết →
                                </a>
                            </div>

                            <!-- CTA -->
                            <div
                                style="text-align:center;background-color:#f8f9fa;padding:30px;border-radius:5px;margin-top:30px;">
                                <p style="color:#333;margin:0 0 20px;font-size:16px;">
                                    Có câu hỏi? Chúng tôi luôn sẵn sàng hỗ trợ!
                                </p>
                                <a href="#"
                                    style="display:inline-block;background-color:#667eea;color:#fff;padding:12px 30px;text-decoration:none;border-radius:5px;font-weight:bold;">
                                    Liên Hệ Ngay
                                </a>
                            </div>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color:#333;color:#fff;padding:30px;text-align:center;">
                            <p style="margin:0 0 15px;font-size:14px;">
                                Theo dõi chúng tôi trên:
                            </p>
                            <div style="margin-bottom:20px;">
                                <a href="#" style="color:#fff;text-decoration:none;margin:0 10px;">Facebook</a> |
                                <a href="#" style="color:#fff;text-decoration:none;margin:0 10px;">Twitter</a> |
                                <a href="#" style="color:#fff;text-decoration:none;margin:0 10px;">LinkedIn</a>
                            </div>
                            <p style="color:#999;margin:0;font-size:12px;">
                                © 2024 {{ app_name }}. All rights reserved.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
