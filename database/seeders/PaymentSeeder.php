<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create();

        // Nếu chưa có order, tạo 30-50 order mẫu
        if (Order::count() === 0) {
            Order::factory(50)->create();
            $this->command->warn('⚠️ Chưa có Order, hệ thống đã tạo 50 đơn hàng mẫu để gắn Payment.');
        }

        $targetCount = 50; // muốn tạo 50 Payment
        $created = 0;
        $ordersPool = Order::inRandomOrder()->get();

        // Phân bố phương thức thanh toán (COD nhiều, wallet ít)
        $methodsWeighted = collect([
            'cod','cod','cod','cod','cod','cod',     // COD ~50-60%
            'bank','bank','bank',                     // Bank ~20%
            'card','card',                             // Card ~10-15%
            'wallet'                                  // Wallet ~5-10%
        ]);

        // Trạng thái thanh toán
        $statusesWeighted = collect([
            'success','success','success',
            'pending','pending',
            'failed','failed',
        ]);

        while ($created < $targetCount) {
            $order = $ordersPool->random();

            $method = $methodsWeighted->random();
            $status = $statusesWeighted->random();

            // COD thường pending hoặc paid
            if ($method === 'cod') {
                $r = $faker->numberBetween(1, 100);
                if ($r <= 60) $status = 'pending';
                elseif ($r <= 95) $status = 'success';
                else $status = 'failed';
            }

            // Tạo transaction_id kiểu thực tế cho online
            $transactionId = null;
            $paidAt = null;
            $requiresManual = $method === 'cod' ? false : ($method === 'bank' ? $faker->boolean(30) : false);
            $isVerified = false;
            $verifiedAt = null;
            $verificationNote = null;
            $gatewayResponse = [];

            if (in_array($method, ['bank','card','wallet'])) {
                $prefix = strtoupper(substr($method,0,3));
                $transactionId = $prefix.'-'.Str::upper(Str::random(6)).'-'.$faker->numberBetween(1000,9999);

                if ($status === 'success') {
                    $paidAt = $faker->dateTimeBetween($order->created_at ?? '-5 days','now');
                    $isVerified = !$requiresManual || $faker->boolean(50);
                    if ($isVerified) {
                        $verifiedAt = $faker->dateTimeBetween($order->created_at ?? '-3 days','now');
                        $verificationNote = $faker->randomElement([
                            'Đã đối soát thành công',
                            'Thanh toán tự động từ hệ thống',
                            'Đã xác minh qua đối soát ngân hàng'
                        ]);
                    }
                }

                // Gateway response
                $gatewayResponse = match($status) {
                    'success' => [
                        'code'=>'00',
                        'message'=>'Giao dịch thành công',
                        'bank_code'=>$faker->randomElement(['VCB','TCB','ACB']),
                        'transaction_ref'=>strtoupper($faker->bothify('INV#######'))
                    ],
                    'failed' => [
                        'code'=>'99',
                        'message'=>'Giao dịch thất bại do lỗi hệ thống hoặc hủy bỏ',
                    ],
                    default => [
                        'code'=>'02',
                        'message'=>'Đang chờ xử lý tại cổng thanh toán',
                    ],
                };

            } else {
                // COD
                if ($status === 'success') {
                    $paidAt = $order->shipped_at ?? Carbon::parse($order->created_at)->addDays($faker->numberBetween(1,5));
                }
                $gatewayResponse = [
                    'code'=>'COD',
                    'message'=>$status === 'success' ? 'Đã thu COD thành công' : 'Chưa thu COD'
                ];
            }

            Payment::create([
                'order_id' => $order->id,
                'amount' => $order->total_amount ?? $faker->numberBetween(100000,2000000),
                'payment_method' => $method,
                'payment_gateway' => in_array($method,['bank','card','wallet']) ? $method : null,
                'transaction_id' => $transactionId,
                'status' => $status,
                'paid_at' => $paidAt,
                'requires_manual_verification' => $requiresManual,
                'is_verified' => $isVerified,
                'verified_at' => $verifiedAt,
                'verification_note' => $verificationNote,
                'gateway_response' => json_encode($gatewayResponse, JSON_UNESCAPED_UNICODE),
                'created_at' => $faker->dateTimeBetween('-10 days','-1 day'),
                'updated_at' => now(),
            ]);

            $created++;
        }

        $this->command->info("✅ PaymentSeeder: Đã tạo {$created} bản ghi thanh toán mô phỏng thực tế!");
    }
}