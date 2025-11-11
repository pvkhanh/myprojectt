<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Mail;
use App\Enums\MailType;

class OrderMailTemplatesSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'template_key' => 'order-confirmation',
                'subject' => 'Xác nhận đơn hàng #{{order_number}}',
                'type' => MailType::System,
                'content' => $this->getOrderConfirmationTemplate(),
            ],
            [
                'template_key' => 'order-paid',
                'subject' => 'Đơn hàng #{{order_number}} đã thanh toán thành công',
                'type' => MailType::System,
                'content' => $this->getOrderPaidTemplate(),
            ],
            [
                'template_key' => 'order-processing',
                'subject' => 'Đơn hàng #{{order_number}} đang được xử lý',
                'type' => MailType::System,
                'content' => $this->getOrderProcessingTemplate(),
            ],
            [
                'template_key' => 'order-shipped',
                'subject' => 'Đơn hàng #{{order_number}} đã được giao cho đơn vị vận chuyển',
                'type' => MailType::System,
                'content' => $this->getOrderShippedTemplate(),
            ],
            [
                'template_key' => 'order-delivered',
                'subject' => 'Đơn hàng #{{order_number}} đã được giao thành công',
                'type' => MailType::System,
                'content' => $this->getOrderDeliveredTemplate(),
            ],
            [
                'template_key' => 'order-completed',
                'subject' => 'Cảm ơn bạn đã mua hàng - Đơn #{{order_number}}',
                'type' => MailType::System,
                'content' => $this->getOrderCompletedTemplate(),
            ],
            [
                'template_key' => 'order-cancelled',
                'subject' => 'Đơn hàng #{{order_number}} đã bị hủy',
                'type' => MailType::System,
                'content' => $this->getOrderCancelledTemplate(),
            ],
        ];

        foreach ($templates as $template) {
            Mail::updateOrCreate(
                ['template_key' => $template['template_key']],
                [
                    'subject' => $template['subject'],
                    'content' => $template['content'],
                    'type' => $template['type'],
                    'sender_email' => config('mail.from.address'),
                ]
            );
        }

        $this->command->info('✅ Order mail templates created successfully!');
    }

    private function getOrderConfirmationTemplate(): string
    {
        return <<<HTML
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; background: #ffffff;">
            <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px; text-align: center;">
                <h1 style="color: white; margin: 0;">Xác Nhận Đơn Hàng</h1>
            </div>
            
            <div style="padding: 30px;">
                <p>Xin chào <strong>{{customer_name}}</strong>,</p>
                
                <p>Cảm ơn bạn đã đặt hàng tại <strong>{{shop_name}}</strong>!</p>
                
                <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;">
                    <h3 style="margin-top: 0; color: #667eea;">Thông Tin Đơn Hàng</h3>
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="padding: 8px 0;"><strong>Mã đơn hàng:</strong></td>
                            <td style="padding: 8px 0; text-align: right;">{{order_number}}</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 0;"><strong>Ngày đặt:</strong></td>
                            <td style="padding: 8px 0; text-align: right;">{{order_date}}</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 0;"><strong>Phương thức thanh toán:</strong></td>
                            <td style="padding: 8px 0; text-align: right;">{{payment_method}}</td>
                        </tr>
                    </table>
                </div>

                <h3 style="color: #667eea;">Chi Tiết Sản Phẩm</h3>
                <div style="border: 1px solid #dee2e6; border-radius: 8px; overflow: hidden;">
                    {{order_items}}
                </div>

                <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;">
                    <table style="width: 100%;">
                        <tr>
                            <td style="padding: 8px 0;">Tạm tính:</td>
                            <td style="padding: 8px 0; text-align: right;">{{subtotal}}</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 0;">Phí vận chuyển:</td>
                            <td style="padding: 8px 0; text-align: right;">{{shipping_fee}}</td>
                        </tr>
                        <tr style="border-top: 2px solid #dee2e6;">
                            <td style="padding: 8px 0;"><strong>Tổng cộng:</strong></td>
                            <td style="padding: 8px 0; text-align: right; color: #667eea; font-size: 20px;"><strong>{{total_amount}}</strong></td>
                        </tr>
                    </table>
                </div>

                <h3 style="color: #667eea;">Địa Chỉ Giao Hàng</h3>
                <div style="background: #f8f9fa; padding: 20px; border-radius: 8px;">
                    <p style="margin: 5px 0;"><strong>{{shipping_name}}</strong></p>
                    <p style="margin: 5px 0;">{{shipping_phone}}</p>
                    <p style="margin: 5px 0;">{{shipping_address}}</p>
                </div>

                <div style="text-align: center; margin: 30px 0;">
                    <a href="{{order_url}}" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 15px 40px; text-decoration: none; border-radius: 8px; display: inline-block; font-weight: bold;">
                        Xem Chi Tiết Đơn Hàng
                    </a>
                </div>

                <p style="color: #6c757d; font-size: 14px; margin-top: 30px;">
                    Nếu bạn có bất kỳ câu hỏi nào, vui lòng liên hệ với chúng tôi qua email hoặc hotline.
                </p>
            </div>

            <div style="background: #f8f9fa; padding: 20px; text-align: center; border-top: 1px solid #dee2e6;">
                <p style="margin: 0; color: #6c757d; font-size: 14px;">
                    © 2024 {{shop_name}}. All rights reserved.
                </p>
            </div>
        </div>
HTML;
    }

    private function getOrderPaidTemplate(): string
    {
        return <<<HTML
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; background: #ffffff;">
            <div style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); padding: 30px; text-align: center;">
                <div style="font-size: 60px; margin-bottom: 10px;">✅</div>
                <h1 style="color: white; margin: 0;">Thanh Toán Thành Công</h1>
            </div>
            
            <div style="padding: 30px;">
                <p>Xin chào <strong>{{customer_name}}</strong>,</p>
                
                <p>Chúng tôi đã nhận được thanh toán cho đơn hàng <strong>#{{order_number}}</strong> của bạn.</p>
                
                <div style="background: #d1f2eb; padding: 20px; border-radius: 8px; border-left: 4px solid #28a745; margin: 20px 0;">
                    <p style="margin: 0; color: #155724;">
                        <strong>✓ Thanh toán thành công</strong><br>
                        Số tiền: <strong style="font-size: 18px;">{{total_amount}}</strong><br>
                        Phương thức: {{payment_method}}<br>
                        Thời gian: {{payment_time}}
                    </p>
                </div>

                <p>Đơn hàng của bạn sẽ được xử lý và giao trong thời gian sớm nhất.</p>

                <div style="text-align: center; margin: 30px 0;">
                    <a href="{{order_url}}" style="background: #28a745; color: white; padding: 15px 40px; text-decoration: none; border-radius: 8px; display: inline-block; font-weight: bold;">
                        Xem Đơn Hàng
                    </a>
                </div>
            </div>

            <div style="background: #f8f9fa; padding: 20px; text-align: center; border-top: 1px solid #dee2e6;">
                <p style="margin: 0; color: #6c757d; font-size: 14px;">
                    © 2024 {{shop_name}}. All rights reserved.
                </p>
            </div>
        </div>
HTML;
    }

    private function getOrderProcessingTemplate(): string
    {
        return <<<HTML
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; background: #ffffff;">
            <div style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); padding: 30px; text-align: center;">
                <div style="font-size: 60px; margin-bottom: 10px;">📦</div>
                <h1 style="color: white; margin: 0;">Đơn Hàng Đang Xử Lý</h1>
            </div>
            
            <div style="padding: 30px;">
                <p>Xin chào <strong>{{customer_name}}</strong>,</p>
                
                <p>Đơn hàng <strong>#{{order_number}}</strong> của bạn đang được xử lý.</p>
                
                <div style="background: #fff3cd; padding: 20px; border-radius: 8px; border-left: 4px solid #ffc107; margin: 20px 0;">
                    <p style="margin: 0; color: #856404;">
                        <strong>🔄 Đang xử lý</strong><br>
                        Chúng tôi đang chuẩn bị sản phẩm cho đơn hàng của bạn.<br>
                        Bạn sẽ nhận được thông báo khi đơn hàng được giao cho đơn vị vận chuyển.
                    </p>
                </div>

                <div style="text-align: center; margin: 30px 0;">
                    <a href="{{order_url}}" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; padding: 15px 40px; text-decoration: none; border-radius: 8px; display: inline-block; font-weight: bold;">
                        Theo Dõi Đơn Hàng
                    </a>
                </div>
            </div>

            <div style="background: #f8f9fa; padding: 20px; text-align: center; border-top: 1px solid #dee2e6;">
                <p style="margin: 0; color: #6c757d; font-size: 14px;">
                    © 2024 {{shop_name}}. All rights reserved.
                </p>
            </div>
        </div>
HTML;
    }

    private function getOrderShippedTemplate(): string
    {
        return <<<HTML
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; background: #ffffff;">
            <div style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); padding: 30px; text-align: center;">
                <div style="font-size: 60px; margin-bottom: 10px;">🚚</div>
                <h1 style="color: white; margin: 0;">Đơn Hàng Đã Giao Vận Chuyển</h1>
            </div>
            
            <div style="padding: 30px;">
                <p>Xin chào <strong>{{customer_name}}</strong>,</p>
                
                <p>Tin vui! Đơn hàng <strong>#{{order_number}}</strong> của bạn đã được giao cho đơn vị vận chuyển.</p>
                
                <div style="background: #cfe2ff; padding: 20px; border-radius: 8px; border-left: 4px solid #0d6efd; margin: 20px 0;">
                    <p style="margin: 0; color: #084298;">
                        <strong>🚚 Đang vận chuyển</strong><br>
                        Mã vận đơn: <strong>{{tracking_number}}</strong><br>
                        Đơn vị vận chuyển: {{shipping_carrier}}<br>
                        Dự kiến giao: {{estimated_delivery}}
                    </p>
                </div>

                <div style="text-align: center; margin: 30px 0;">
                    <a href="{{tracking_url}}" style="background: #0d6efd; color: white; padding: 15px 40px; text-decoration: none; border-radius: 8px; display: inline-block; font-weight: bold; margin-right: 10px;">
                        Theo Dõi Vận Đơn
                    </a>
                    <a href="{{order_url}}" style="background: transparent; color: #0d6efd; padding: 15px 40px; text-decoration: none; border-radius: 8px; display: inline-block; font-weight: bold; border: 2px solid #0d6efd;">
                        Xem Đơn Hàng
                    </a>
                </div>
            </div>

            <div style="background: #f8f9fa; padding: 20px; text-align: center; border-top: 1px solid #dee2e6;">
                <p style="margin: 0; color: #6c757d; font-size: 14px;">
                    © 2024 {{shop_name}}. All rights reserved.
                </p>
            </div>
        </div>
HTML;
    }

    private function getOrderDeliveredTemplate(): string
    {
        return <<<HTML
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; background: #ffffff;">
            <div style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); padding: 30px; text-align: center;">
                <div style="font-size: 60px; margin-bottom: 10px;">🎉</div>
                <h1 style="color: white; margin: 0;">Giao Hàng Thành Công</h1>
            </div>
            
            <div style="padding: 30px;">
                <p>Xin chào <strong>{{customer_name}}</strong>,</p>
                
                <p>Đơn hàng <strong>#{{order_number}}</strong> đã được giao thành công!</p>
                
                <div style="background: #d1f2eb; padding: 20px; border-radius: 8px; border-left: 4px solid #28a745; margin: 20px 0;">
                    <p style="margin: 0; color: #155724;">
                        <strong>✅ Đã giao hàng</strong><br>
                        Thời gian giao: {{delivery_time}}<br>
                        Người nhận: {{receiver_name}}
                    </p>
                </div>

                <p>Hy vọng bạn hài lòng với sản phẩm của chúng tôi. Nếu có bất kỳ vấn đề gì, vui lòng liên hệ với chúng tôi ngay!</p>

                <div style="text-align: center; margin: 30px 0;">
                    <a href="{{review_url}}" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white; padding: 15px 40px; text-decoration: none; border-radius: 8px; display: inline-block; font-weight: bold;">
                        Đánh Giá Sản Phẩm
                    </a>
                </div>
            </div>

            <div style="background: #f8f9fa; padding: 20px; text-align: center; border-top: 1px solid #dee2e6;">
                <p style="margin: 0; color: #6c757d; font-size: 14px;">
                    © 2024 {{shop_name}}. All rights reserved.
                </p>
            </div>
        </div>
HTML;
    }

    private function getOrderCompletedTemplate(): string
    {
        return <<<HTML
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; background: #ffffff;">
            <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px; text-align: center;">
                <div style="font-size: 60px; margin-bottom: 10px;">🌟</div>
                <h1 style="color: white; margin: 0;">Cảm Ơn Bạn Đã Mua Hàng</h1>
            </div>
            
            <div style="padding: 30px;">
                <p>Xin chào <strong>{{customer_name}}</strong>,</p>
                
                <p>Cảm ơn bạn đã tin tưởng và mua sắm tại <strong>{{shop_name}}</strong>!</p>
                
                <p>Đơn hàng <strong>#{{order_number}}</strong> của bạn đã hoàn tất. Chúng tôi hy vọng bạn hài lòng với sản phẩm.</p>

                <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0; text-align: center;">
                    <h3 style="color: #667eea; margin-top: 0;">Chia sẻ trải nghiệm của bạn</h3>
                    <p>Đánh giá của bạn sẽ giúp chúng tôi cải thiện dịch vụ tốt hơn!</p>
                    <a href="{{review_url}}" style="background: #ffc107; color: #000; padding: 12px 30px; text-decoration: none; border-radius: 8px; display: inline-block; font-weight: bold; margin-top: 10px;">
                        ⭐ Đánh Giá Ngay
                    </a>
                </div>

                <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 20px; border-radius: 8px; margin: 20px 0; text-align: center; color: white;">
                    <h3 style="margin-top: 0;">🎁 Ưu đãi dành cho bạn</h3>
                    <p>Sử dụng mã <strong style="font-size: 20px; background: rgba(255,255,255,0.2); padding: 5px 15px; border-radius: 4px;">{{discount_code}}</strong></p>
                    <p style="margin: 5px 0; font-size: 14px;">Giảm {{discount_value}} cho đơn hàng tiếp theo</p>
                    <p style="margin: 5px 0; font-size: 12px; opacity: 0.8;">Có hiệu lực đến {{discount_expiry}}</p>
                </div>

                <div style="text-align: center; margin: 30px 0;">
                    <a href="{{shop_url}}" style="background: transparent; color: #667eea; padding: 15px 40px; text-decoration: none; border-radius: 8px; display: inline-block; font-weight: bold; border: 2px solid #667eea;">
                        Tiếp Tục Mua Sắm
                    </a>
                </div>
            </div>

            <div style="background: #f8f9fa; padding: 20px; text-align: center; border-top: 1px solid #dee2e6;">
                <p style="margin: 0; color: #6c757d; font-size: 14px;">
                    © 2024 {{shop_name}}. All rights reserved.
                </p>
            </div>
        </div>
HTML;
    }

    private function getOrderCancelledTemplate(): string
    {
        return <<<HTML
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; background: #ffffff;">
            <div style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); padding: 30px; text-align: center;">
                <div style="font-size: 60px; margin-bottom: 10px;">❌</div>
                <h1 style="color: white; margin: 0;">Đơn Hàng Đã Bị Hủy</h1>
            </div>
            
            <div style="padding: 30px;">
                <p>Xin chào <strong>{{customer_name}}</strong>,</p>
                
                <p>Đơn hàng <strong>#{{order_number}}</strong> của bạn đã bị hủy.</p>
                
                <div style="background: #f8d7da; padding: 20px; border-radius: 8px; border-left: 4px solid #dc3545; margin: 20px 0;">
                    <p style="margin: 0; color: #721c24;">
                        <strong>❌ Đơn hàng đã hủy</strong><br>
                        Lý do: {{cancel_reason}}<br>
                        Thời gian: {{cancel_time}}
                    </p>
                </div>

                <p>Nếu bạn đã thanh toán, số tiền sẽ được hoàn lại trong vòng 3-5 ngày làm việc.</p>

                <p>Nếu bạn có bất kỳ thắc mắc nào, vui lòng liên hệ với chúng tôi qua hotline hoặc email.</p>

                <div style="text-align: center; margin: 30px 0;">
                    <a href="{{shop_url}}" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 15px 40px; text-decoration: none; border-radius: 8px; display: inline-block; font-weight: bold;">
                        Tiếp Tục Mua Sắm
                    </a>
                </div>
            </div>

            <div style="background: #f8f9fa; padding: 20px; text-align: center; border-top: 1px solid #dee2e6;">
                <p style="margin: 0; color: #6c757d; font-size: 14px;">
                    © 2024 {{shop_name}}. All rights reserved.
                </p>
            </div>
        </div>
HTML;
    }
}