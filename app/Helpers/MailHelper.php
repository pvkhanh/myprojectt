<?php

namespace App\Helpers;

use App\Models\Mail;
use App\Models\MailRecipient;
use App\Models\Order;
use App\Enums\MailRecipientStatus;
use Illuminate\Support\Facades\Mail as MailFacade;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Log;

class MailHelper
{
    /**
     * Replace variables in content
     */
    public static function replaceVariables(string $content, array $variables = []): string
    {
        return str_replace(array_keys($variables), array_values($variables), $content);
    }

    /**
     * Get default variables from recipient/user
     */
    private static function getDefaultVariables(MailRecipient $recipient): array
    {
        $user = $recipient->user;

        return [
            '{{customer_name}}' => $recipient->name,
            '{{username}}' => $recipient->name,
            '{{email}}' => $recipient->email,
            '{{first_name}}' => $user->first_name ?? '',
            '{{last_name}}' => $user->last_name ?? '',
            '{{app_name}}' => config('app.name'),
            '{{app_url}}' => config('app.url'),
        ];
    }

    /**
     * Get HTML content from template file
     */
    public static function getTemplateHtml(string $templateKey, array $variables = []): string
    {
        $path = app_path("MailTemplates/{$templateKey}.html");

        if (!file_exists($path)) {
            throw new \Exception("Template {$templateKey} không tồn tại!");
        }

        $html = file_get_contents($path);
        return self::replaceVariables($html, $variables);
    }

    /**
     * Send mail to single recipient
     */
    // public static function sendToRecipient(Mail $mail, MailRecipient $recipient, array $customVariables = []): bool
    // {
    //     try {
    //         $variables = array_merge(
    //             self::getDefaultVariables($recipient),
    //             $customVariables,
    //             $mail->variables ?? []
    //         );

    //         // Lấy content: DB template > file template > raw content
    //         if (!empty($mail->template_key)) {
    //             try {
    //                 $content = self::getTemplateHtml($mail->template_key, $variables);
    //             } catch (\Exception $e) {
    //                 $content = self::replaceVariables($mail->content, $variables);
    //             }
    //         } else {
    //             $content = self::replaceVariables($mail->content, $variables);
    //         }

    //         // Add tracking pixel
    //         $content .= self::generateTrackingPixel($recipient);

    //         $subject = self::replaceVariables($mail->subject, $variables);

    //         // Send email
    //         MailFacade::html($content, function (Message $message) use ($recipient, $subject, $mail) {
    //             $message->to($recipient->email, $recipient->name)
    //                     ->subject($subject)
    //                     ->from($mail->sender_email, config('app.name'));
    //         });

    //         // Update recipient status
    //         $recipient->update([
    //             'status'    => MailRecipientStatus::Sent->value,
    //             'error_log' => null,
    //         ]);

    //         return true;

    //     } catch (\Exception $e) {
    //         $recipient->update([
    //             'status'    => MailRecipientStatus::Failed->value,
    //             'error_log' => $e->getMessage(),
    //         ]);

    //         Log::error("Failed to send mail to {$recipient->email}: {$e->getMessage()}");
    //         return false;
    //     }
    // }

    public static function sendToRecipient(Mail $mail, MailRecipient $recipient, array $customVariables = []): bool
    {
        try {
            // Fix: decode JSON từ DB thành mảng
            $variablesFromDb = [];
            if (is_string($mail->variables)) {
                $variablesFromDb = json_decode($mail->variables, true);
                if (!is_array($variablesFromDb)) {
                    $variablesFromDb = [];
                }
            } elseif (is_array($mail->variables)) {
                $variablesFromDb = $mail->variables;
            }

            // Merge tất cả biến
            $variables = array_merge(
                self::getDefaultVariables($recipient),
                $customVariables,
                $variablesFromDb
            );

            // Lấy content: DB template > file template > raw content
            if (!empty($mail->template_key)) {
                try {
                    $content = self::getTemplateHtml($mail->template_key, $variables);
                } catch (\Exception $e) {
                    $content = self::replaceVariables($mail->content, $variables);
                }
            } else {
                $content = self::replaceVariables($mail->content, $variables);
            }

            // Add tracking pixel
            $content .= self::generateTrackingPixel($recipient);

            $subject = self::replaceVariables($mail->subject, $variables);

            // Send email
            MailFacade::html($content, function (Message $message) use ($recipient, $subject, $mail) {
                $message->to($recipient->email, $recipient->name)
                    ->subject($subject)
                    ->from($mail->sender_email, config('app.name'));
            });

            // Update recipient status
            $recipient->update([
                'status' => MailRecipientStatus::Sent->value,
                'error_log' => null,
            ]);

            return true;
        } catch (\Exception $e) {
            $recipient->update([
                'status' => MailRecipientStatus::Failed->value,
                'error_log' => $e->getMessage(),
            ]);

            Log::error("Failed to send mail to {$recipient->email}: {$e->getMessage()}");
            return false;
        }
    }


    /**
     * Send bulk mail
     */
    public static function sendBulk(Mail $mail, $recipients = null): array
    {
        if (!$recipients) {
            $recipients = $mail->recipients()
                ->where('status', MailRecipientStatus::Pending->value)
                ->get();
        }

        $stats = [
            'total' => $recipients->count(),
            'success' => 0,
            'failed' => 0,
        ];

        foreach ($recipients as $recipient) {
            if (self::sendToRecipient($mail, $recipient)) {
                $stats['success']++;
            } else {
                $stats['failed']++;
            }
        }

        return $stats;
    }

    /**
     * Generate tracking pixel
     */
    public static function generateTrackingPixel(MailRecipient $recipient): string
    {
        $token = md5($recipient->id . $recipient->email . config('app.key'));
        return sprintf(
            '<img src="%s/mail-tracking/%s" width="1" height="1" style="display:none;" />',
            config('app.url'),
            $token
        );
    }

    /**
     * Validate email
     */
    public static function validateEmail(string $email): bool
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        if (config('mail-system.validation.check_dns')) {
            $domain = substr(strrchr($email, "@"), 1);
            if (!checkdnsrr($domain, "MX")) {
                return false;
            }
        }

        return true;
    }

    /**
     * Send order mail
     */
    public static function sendOrderMail(Order $order, string $templateKey): void
    {
        $mailTemplate = Mail::where('template_key', $templateKey)->first();
        if (!$mailTemplate) {
            Log::warning("Mail template not found: {$templateKey}");
            return;
        }

        $user = $order->user;
        $shippingAddress = $order->shippingAddress;

        $recipient = MailRecipient::create([
            'mail_id' => $mailTemplate->id,
            'user_id' => $user->id,
            'email' => $user->email,
            'name' => trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')),
            'status' => MailRecipientStatus::Pending->value,
        ]);

        $variables = self::prepareOrderVariables($order, $shippingAddress);

        self::sendToRecipient($mailTemplate, $recipient, $variables);
    }

    /**
     * Prepare order variables for email
     */
    private static function prepareOrderVariables(Order $order, $shippingAddress): array
    {
        $itemsHtml = self::formatOrderItems($order->orderItems);

        return [
            '{{order_number}}' => $order->order_number,
            '{{order_date}}' => $order->created_at->format('d/m/Y H:i'),
            '{{payment_method}}' => $order->payment_method_label, // string
            '{{payment_status}}' => $order->payment_label,        // string
            '{{order_items}}' => $itemsHtml,
            '{{subtotal}}' => number_format($order->subtotal, 0, ',', '.') . 'đ',
            '{{shipping_fee}}' => number_format($order->shipping_fee ?? 0, 0, ',', '.') . 'đ',
            '{{total_amount}}' => number_format($order->total_amount, 0, ',', '.') . 'đ',
            '{{shipping_name}}' => $shippingAddress->name ?? '',
            '{{shipping_phone}}' => $shippingAddress->phone ?? '',
            '{{shipping_address}}' => $shippingAddress
                ? "{$shippingAddress->address}, {$shippingAddress->ward}, {$shippingAddress->district}, {$shippingAddress->city}"
                : '',
            //'{{order_url}}'          => route('orders.show', $order->id),
            '{{order_url}}' => url('/orders/' . $order->id),
            '{{tracking_url}}' => '#',
            '{{tracking_number}}' => 'TBA',
            '{{estimated_delivery}}' => now()->addDays(3)->format('d/m/Y'),
            '{{payment_time}}' => $order->paid_at?->format('d/m/Y H:i') ?? '',
            '{{delivery_time}}' => $order->delivered_at?->format('d/m/Y H:i') ?? '',
            '{{cancel_reason}}' => $order->admin_note ?? 'Không có lý do cụ thể',
            '{{cancel_time}}' => $order->cancelled_at?->format('d/m/Y H:i') ?? '',
            // '{{review_url}}'         => route('orders.show', $order->id) . '#review',
            '{{review_url}}' => url('/orders/' . $order->id . '#review'),
            //'{{shop_url}}'           => route('home'),
            '{{shop_url}}' => url('/'),
            '{{shop_name}}' => config('app.name'),
            '{{discount_code}}' => 'THANKS10',
            '{{discount_value}}' => '10%',
            '{{discount_expiry}}' => now()->addDays(30)->format('d/m/Y'),
        ];
    }

    /**
     * Format order items to HTML
     */
    private static function formatOrderItems($items): string
    {
        $html = '';
        foreach ($items as $item) {
            $productName = $item->product->name;
            if ($item->variant)
                $productName .= " - {$item->variant->name}";

            $unitPrice = number_format($item->price, 0, ',', '.');
            $totalPrice = number_format($item->price * $item->quantity, 0, ',', '.');

            $html .= <<<HTML
<div style="padding:15px;border-bottom:1px solid #dee2e6;display:flex;justify-content:space-between;align-items:center;">
    <div style="flex:1;">
        <strong>{$productName}</strong><br>
        <span style="color:#6c757d;font-size:14px;">SL: {$item->quantity} x {$unitPrice}đ</span>
    </div>
    <div style="font-weight:bold;color:#667eea;">
        {$totalPrice}đ
    </div>
</div>
HTML;
        }
        return $html;
    }
}