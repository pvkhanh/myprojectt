<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Order;
use App\Models\User;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;

class PaymentFactory extends Factory
{
    public function definition(): array
    {
        // Chọn phương thức thanh toán thật
        $method = $this->faker->randomElement(PaymentMethod::values());

        // Sinh trạng thái thanh toán thực tế (xác suất)
        $status = $this->faker->randomElement([
            PaymentStatus::Success->value,
            PaymentStatus::Pending->value,
            PaymentStatus::Failed->value,
        ]);

        // Xác định xem có cần xác minh thủ công không (COD thường có)
        $requiresManual = $method === 'cod';

        // Nếu online thì có cổng thanh toán cụ thể
        $gateway = match ($method) {
            'card'   => $this->faker->randomElement(['VNPay', 'Visa', 'MasterCard']),
            'wallet' => $this->faker->randomElement(['MoMo', 'ZaloPay']),
            'bank'   => $this->faker->randomElement(['Vietcombank', 'Techcombank', 'ACB']),
            default  => null, // COD không có gateway
        };

        // Nếu thành công thì có ngày thanh toán và người xác minh
        $isVerified = $status === 'success' || $method !== 'cod';
        $verifiedAt = $isVerified ? $this->faker->dateTimeBetween('-3 days', 'now') : null;
        $verifiedBy = $isVerified ? User::inRandomOrder()->value('id') ?? 1 : null;

        // Giả lập dữ liệu JSON phản hồi thực tế từ gateway
        $gatewayResponse = match ($status) {
            'success' => [
                'code' => '00',
                'message' => 'Giao dịch thành công',
                'bank_code' => $this->faker->randomElement(['VCB', 'TCB', 'ACB']),
                'transaction_ref' => strtoupper($this->faker->bothify('INV#######')),
            ],
            'failed' => [
                'code' => '99',
                'message' => 'Giao dịch thất bại do lỗi hệ thống hoặc hủy bỏ',
            ],
            default => [
                'code' => '02',
                'message' => 'Đang chờ xử lý tại cổng thanh toán',
            ],
        };

        return [
            // Khóa ngoại: order_id
            'order_id' => Order::inRandomOrder()->value('id') ?? 1,

            // Cách thanh toán (enum)
            'payment_method' => $method,

            // Tên cổng thanh toán (chỉ nếu không phải COD)
            'payment_gateway' => $gateway,

            // Mã giao dịch (mã thật kiểu “TXN-20241104XXXX”)
            'transaction_id' => 'TXN-' . now()->format('Ymd') . '-' . $this->faker->unique()->numerify('######'),

            // Số tiền thanh toán (từ 100k đến 10 triệu)
            'amount' => $this->faker->randomFloat(2, 100000, 10000000),

            // Thời điểm thanh toán thành công (nếu có)
            'paid_at' => $status === 'success'
                ? $this->faker->dateTimeBetween('-5 days', 'now')
                : null,

            // Trạng thái thanh toán
            'status' => $status,

            // Có cần xác minh thủ công không
            'requires_manual_verification' => $requiresManual,

            // Đã xác minh chưa
            'is_verified' => $isVerified,

            // Thời điểm xác minh (nếu có)
            'verified_at' => $verifiedAt,

            // Người xác minh
            'verified_by' => $verifiedBy,

            // Ghi chú xác minh
            'verification_note' => $isVerified
                ? $this->faker->randomElement([
                    'Đã đối soát thành công',
                    'Thanh toán tự động từ hệ thống',
                    'Đã xác minh qua đối soát ngân hàng'
                ])
                : ($requiresManual ? 'Đang chờ nhân viên xác minh COD' : null),

            // JSON phản hồi từ gateway
            'gateway_response' => json_encode($gatewayResponse, JSON_UNESCAPED_UNICODE),

            // Timestamps
            'created_at' => $this->faker->dateTimeBetween('-10 days', '-1 day'),
            'updated_at' => now(),
        ];
    }
}