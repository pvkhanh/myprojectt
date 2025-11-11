<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Mail;
use App\Models\MailRecipient;
use App\Models\User;
use App\Enums\MailType;
use App\Enums\MailRecipientStatus;

class MailSeeder extends Seeder
{
    public function run(): void
    {
        // ================================
        // 1️⃣ Welcome Email Template
        // ================================
        $welcomeMail = Mail::create([
            'subject' => 'Chào mừng bạn đến với ' . config('app.name'),
            'content' => $this->getWelcomeTemplate(),
            'template_key' => 'welcome-email',
            'type' => MailType::System,
            'sender_email' => config('mail.from.address'),
            'variables' => ['app_name' => config('app.name')],
        ]);

        // ================================
        // 2️⃣ Order Confirmation Template
        // ================================
        $orderMail = Mail::create([
            'subject' => 'Xác nhận đơn hàng #{{order_number}}',
            'content' => $this->getOrderConfirmationTemplate(),
            'template_key' => 'order-confirmation',
            'type' => MailType::System,
            'sender_email' => config('mail.from.address'),
            'variables' => null,
        ]);

        // ================================
        // 3️⃣ Password Reset Template
        // ================================
        $resetMail = Mail::create([
            'subject' => 'Yêu cầu đặt lại mật khẩu',
            'content' => $this->getPasswordResetTemplate(),
            'template_key' => 'password-reset',
            'type' => MailType::System,
            'sender_email' => config('mail.from.address'),
            'variables' => null,
        ]);

        // ================================
        // 4️⃣ Promotional Email
        // ================================
        $promoMail = Mail::create([
            'subject' => '🎉 FLASH SALE 50% - Chỉ hôm nay!',
            'content' => $this->getPromoTemplate(),
            'template_key' => 'promo-discount',
            'type' => MailType::Marketing,
            'sender_email' => config('mail.from.address'),
            'variables' => ['promo_code' => 'SAVE50', 'discount' => '50%'],
        ]);

        // ================================
        // 5️⃣ Newsletter
        // ================================
        $newsletterMail = Mail::create([
            'subject' => '📰 Bản tin tháng ' . now()->format('m/Y'),
            'content' => $this->getNewsletterTemplate(),
            'template_key' => 'newsletter',
            'type' => MailType::Marketing,
            'sender_email' => config('mail.from.address'),
            'variables' => ['month' => now()->format('m/Y')],
        ]);

        // ================================
        // Gắn recipient cho từng mail
        // ================================
        $users = User::limit(10)->get();

        foreach ([$welcomeMail, $orderMail, $resetMail, $promoMail, $newsletterMail] as $mail) {
            foreach ($users as $user) {
                MailRecipient::create([
                    'mail_id' => $mail->id,
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'name' => $user->username ?? 'User',
                    'status' => $this->randomStatus(),
                    'error_log' => null,
                ]);
            }
        }

        $this->command->info('✅ MailSeeder completed successfully!');
    }

    private function randomStatus()
    {
        $rand = rand(1, 100);
        if ($rand <= 60) return MailRecipientStatus::Sent->value;
        if ($rand <= 90) return MailRecipientStatus::Pending->value;
        return MailRecipientStatus::Failed->value;
    }

    // ================================
    // Template Mail
    // ================================
    private function getWelcomeTemplate(): string
    {
        return <<<'HTML'
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0;padding:0;font-family:Arial,sans-serif;background-color:#f4f4f4;">
<table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f4f4;padding:20px;">
<tr>
<td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="background-color:#fff;border-radius:10px;overflow:hidden;box-shadow:0 4px 6px rgba(0,0,0,0.1);">
<tr>
<td style="background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);padding:40px;text-align:center;">
<h1 style="color:#fff;margin:0;font-size:28px;">Chào mừng bạn!</h1>
</td>
</tr>
<tr>
<td style="padding:40px;">
<h2 style="color:#333;margin:0 0 20px;">Xin chào {{username}}!</h2>
<p style="color:#666;line-height:1.6;margin:0 0 20px;">
Cảm ơn bạn đã đăng ký tài khoản. Chúng tôi rất vui được chào đón bạn!
</p>
<div style="text-align:center;">
<a href="#" style="display:inline-block;background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:#fff;padding:15px 40px;text-decoration:none;border-radius:5px;font-weight:bold;">
Bắt đầu ngay
</a>
</div>
</td>
</tr>
<tr>
<td style="background-color:#f8f9fa;padding:30px;text-align:center;border-top:1px solid #dee2e6;">
<p style="color:#999;margin:0;font-size:14px;">© 2024 All rights reserved.</p>
</td>
</tr>
</table>
</td>
</tr>
</table>
</body>
</html>
HTML;
    }

    private function getOrderConfirmationTemplate(): string
    {
        return <<<'HTML'
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;font-family:Arial,sans-serif;background-color:#f4f4f4;">
<table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f4f4;padding:20px;">
<tr>
<td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="background-color:#fff;border-radius:10px;">
<tr>
<td style="background-color:#28a745;padding:30px;text-align:center;">
<h1 style="color:#fff;margin:0;font-size:26px;">✓ Đơn Hàng Đã Xác Nhận</h1>
</td>
</tr>
<tr>
<td style="padding:40px;">
<h2 style="color:#333;">Xin chào {{username}}!</h2>
<p style="color:#666;line-height:1.6;">Đơn hàng của bạn đã được xác nhận và đang được xử lý.</p>
<div style="text-align:center;margin-top:30px;">
<a href="#" style="display:inline-block;background-color:#28a745;color:#fff;padding:15px 40px;text-decoration:none;border-radius:5px;font-weight:bold;">
Xem Chi Tiết
</a>
</div>
</td>
</tr>
</table>
</td>
</tr>
</table>
</body>
</html>
HTML;
    }

    private function getPasswordResetTemplate(): string
    {
        return <<<'HTML'
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;font-family:Arial,sans-serif;background-color:#f4f4f4;">
<table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f4f4;padding:20px;">
<tr>
<td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="background-color:#fff;border-radius:10px;">
<tr>
<td style="background-color:#dc3545;padding:30px;text-align:center;">
<h1 style="color:#fff;margin:0;">🔒 Đặt Lại Mật Khẩu</h1>
</td>
</tr>
<tr>
<td style="padding:40px;">
<h2 style="color:#333;">Xin chào {{username}}!</h2>
<p style="color:#666;line-height:1.6;">
Chúng tôi nhận được yêu cầu đặt lại mật khẩu cho tài khoản của bạn.
</p>
<div style="text-align:center;margin-top:30px;">
<a href="#" style="display:inline-block;background-color:#dc3545;color:#fff;padding:15px 40px;text-decoration:none;border-radius:5px;font-weight:bold;">
Đặt Lại Mật Khẩu
</a>
</div>
</td>
</tr>
</table>
</td>
</tr>
</table>
</body>
</html>
HTML;
    }

    private function getPromoTemplate(): string
    {
        return <<<'HTML'
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;font-family:Arial,sans-serif;background-color:#f4f4f4;">
<table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f4f4;padding:20px;">
<tr>
<td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="background-color:#fff;border-radius:10px;">
<tr>
<td style="background:linear-gradient(135deg,#f093fb 0%,#f5576c 100%);padding:50px;text-align:center;">
<h1 style="color:#fff;margin:0;font-size:36px;">🎉 FLASH SALE 50%</h1>
</td>
</tr>
<tr>
<td style="padding:40px;">
<h2 style="color:#333;text-align:center;">Xin chào {{username}}!</h2>
<div style="background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);border-radius:10px;padding:30px;text-align:center;margin:30px 0;">
<p style="color:#fff;margin:0 0 10px;">Mã giảm giá:</p>
<h2 style="color:#fff;margin:0;font-size:36px;">SAVE50</h2>
</div>
<div style="text-align:center;">
<a href="#" style="display:inline-block;background:linear-gradient(135deg,#f093fb 0%,#f5576c 100%);color:#fff;padding:15px 50px;text-decoration:none;border-radius:50px;font-weight:bold;">
MUA NGAY
</a>
</div>
</td>
</tr>
</table>
</td>
</tr>
</table>
</body>
</html>
HTML;
    }

    private function getNewsletterTemplate(): string
    {
        return <<<'HTML'
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;font-family:Arial,sans-serif;background-color:#f4f4f4;">
<table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f4f4;padding:20px;">
<tr>
<td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="background-color:#fff;border-radius:10px;">
<tr>
<td style="padding:40px;text-align:center;border-bottom:2px solid #dee2e6;">
<h1 style="color:#333;margin:0;">📰 Bản Tin Tháng {{month}}</h1>
</td>
</tr>
<tr>
<td style="padding:40px;">
<p style="color:#666;">Xin chào <strong>{{username}}</strong>!</p>
<div style="margin:30px 0;padding:30px;background-color:#f8f9fa;border-radius:5px;">
<h3 style="color:#333;">Cập nhật mới nhất</h3>
<p style="color:#666;line-height:1.6;">Khám phá những tính năng và nội dung mới nhất từ chúng tôi...</p>
</div>
</td>
</tr>
</table>
</td>
</tr>
</table>
</body>
</html>
HTML;
    }
}