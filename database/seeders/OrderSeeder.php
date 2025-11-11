<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ShippingAddress;
use App\Models\Payment;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $orders = Order::factory()->count(30)->create();

        foreach ($orders as $order) {
            ShippingAddress::factory()->create([
                'order_id' => $order->id,
            ]);

            $orderItems = OrderItem::factory()->count(rand(1, 5))->create([
                'order_id' => $order->id,
            ]);

            $totalProducts = $orderItems->sum(fn($item) => $item->price * $item->quantity);
            $order->total_amount = $totalProducts + $order->shipping_fee;
            $order->save();

            Payment::factory()->create([
                'order_id' => $order->id,
                'amount' => $order->total_amount,
            ]);
        }
    }
}
