<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Mail;
use App\Enums\MailType;

class MailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'subject' => 'Xác nhận đơn hàng #{{order_number}}',
                'template_key' => 'order-confirmation',
                'type' => MailType::Order->value,
                'description' => 'Email xác nhận đơn hàng mới được tạo',
            ],
            [
                'subject' => 'Đơn hàng #{{order_number}} đã được thanh toán',
                'template_key' => 'order-paid',
                'type' => MailType::Order->value,
                'description' => 'Email thông báo thanh toán thành công',
            ],
            [
                'subject' => 'Đơn hàng #{{order_number}} đang được giao',
                'template_key' => 'order-shipped',
                'type' => MailType::Order->value,
                'description' => 'Email thông báo đơn hàng đang giao',
            ],
            [
                'subject' => 'Đơn hàng #{{order_number}} đã hoàn thành',
                'template_key' => 'order-completed',
                'type' => MailType::Order->value,
                'description' => 'Email xác nhận đơn hàng hoàn tất',
            ],
            [
                'subject' => 'Đơn hàng #{{order_number}} đã bị hủy',
                'template_key' => 'order-cancelled',
                'type' => MailType::Order->value,
                'description' => 'Email thông báo đơn hàng bị hủy',
            ],
        ];

        foreach ($templates as $template) {
            // Đọc nội dung HTML từ file
            $htmlFile = app_path("MailTemplates/{$template['template_key']}.html");
            
            if (!file_exists($htmlFile)) {
                $this->command->warn("⚠️  Template file not found: {$template['template_key']}.html");
                continue;
            }

            $content = file_get_contents($htmlFile);

            // Tạo hoặc cập nhật template
            Mail::updateOrCreate(
                ['template_key' => $template['template_key']],
                [
                    'subject' => $template['subject'],
                    'content' => $content,
                    'type' => $template['type'],
                    'sender_email' => config('mail.from.address'),
                    'variables' => json_encode([
                        'order_number' => 'Mã đơn hàng',
                        'customer_name' => 'Tên khách hàng',
                        'order_date' => 'Ngày đặt hàng',
                        'total_amount' => 'Tổng tiền',
                        'payment_method' => 'Phương thức thanh toán',
                        'shipping_address' => 'Địa chỉ giao hàng',
                        'order_items' => 'Danh sách sản phẩm',
                        'app_name' => 'Tên ứng dụng',
                        'app_url' => 'URL website',
                    ]),
                ]
            );

            $this->command->info("✓ Seeded: {$template['template_key']}");
        }

        $this->command->info("\n✅ Mail templates seeded successfully!");
    }
}