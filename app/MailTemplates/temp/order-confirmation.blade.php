<!--
  ================================================================
  TEMPLATE: ORDER CONFIRMATION
  Template Key: order-confirmation
  Use Case: Xác nhận đơn hàng
  ================================================================
-->
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Xác nhận đơn hàng</title>
</head>

<body style="margin:0;padding:0;font-family:Arial,sans-serif;background-color:#f4f4f4;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f4f4;padding:20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0"
                    style="background-color:#ffffff;border-radius:10px;overflow:hidden;">
                    <!-- Header -->
                    <tr>
                        <td style="background-color:#28a745;padding:30px;text-align:center;">
                            <h1 style="color:#ffffff;margin:0;font-size:26px;">✓ Đơn Hàng Đã Được Xác Nhận</h1>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding:40px;">
                            <h2 style="color:#333;margin:0 0 20px;">Xin chào {{ username }}!</h2>
                            <p style="color:#666;line-height:1.6;margin:0 0 20px;">
                                Cảm ơn bạn đã đặt hàng! Đơn hàng của bạn đã được xác nhận và đang được xử lý.
                            </p>

                            <!-- Order Details -->
                            <table width="100%" cellpadding="10"
                                style="border:1px solid #dee2e6;border-radius:5px;margin:20px 0;">
                                <tr style="background-color:#f8f9fa;">
                                    <td colspan="2" style="font-weight:bold;color:#333;">Thông tin đơn hàng</td>
                                </tr>
                                <tr>
                                    <td style="color:#666;">Mã đơn hàng:</td>
                                    <td style="color:#333;font-weight:bold;">#ORD12345</td>
                                </tr>
                                <tr style="background-color:#f8f9fa;">
                                    <td style="color:#666;">Ngày đặt:</td>
                                    <td style="color:#333;">06/11/2024</td>
                                </tr>
                                <tr>
                                    <td style="color:#666;">Tổng tiền:</td>
                                    <td style="color:#28a745;font-weight:bold;font-size:18px;">1.500.000đ</td>
                                </tr>
                            </table>

                            <div style="text-align:center;margin-top:30px;">
                                <a href="#"
                                    style="display:inline-block;background-color:#28a745;color:#fff;padding:15px 40px;text-decoration:none;border-radius:5px;font-weight:bold;">
                                    Xem Chi Tiết Đơn Hàng
                                </a>
                            </div>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color:#f8f9fa;padding:30px;text-align:center;">
                            <p style="color:#999;margin:0;font-size:14px;">
                                Nếu có thắc mắc, vui lòng liên hệ: {{ email }}
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
