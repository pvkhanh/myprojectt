<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\OrderStatus;
use App\Models\Scopes\OrderScopes;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus; // nếu dùng PaymentStatus enum
use Illuminate\Support\Facades\DB;


class Order extends Model
{
    use HasFactory, SoftDeletes, OrderScopes;

    protected $fillable = [
        'user_id',
        'order_number',
        'total_amount',
        'shipping_fee',
        'customer_note',
        'admin_note',
        'status',
        'paid_at',
        'shipped_at',
        'delivered_at',
        'completed_at',
        'cancelled_at'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'shipping_fee' => 'decimal:2',
        'paid_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'status' => OrderStatus::class,
    ];
    // Thêm vào Order.php nếu chưa có
    protected $with = ['shippingAddress', 'orderItems.product', 'orderItems.variant'];
    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shippingAddress()
    {
        return $this->hasOne(ShippingAddress::class);
    }

   // Cách 2: Thêm alias items() trong model Order Thêm ngày 13/11 để chạy cho show shipping do đang gọi đến item mà item nằm trong bảng OrderItem
   public function items()
{
    return $this->orderItems();
}

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // Computed Attributes
    public function getSubtotalAttribute(): float
    {
        return (float) $this->orderItems->sum(function ($item) {
            return $item->price * $item->quantity;
        });
    }

    public function getTotalAmountAttribute($value): float
    {
        // Nếu đã có giá trị trong DB, ưu tiên dùng giá trị đó
        if ($value > 0) {
            return (float) $value;
        }

        // Nếu chưa có, tính động từ items + shipping
        return $this->subtotal + ($this->shipping_fee ?? 0);
    }

    // Tính toán và cập nhật total_amount
    // public function calculateAndUpdateTotal(): void
    // {
    //     $subtotal = $this->subtotal;
    //     $shippingFee = $this->shipping_fee ?? 0;
    //     $total = $subtotal + $shippingFee;

    //     $this->update(['total_amount' => $total]);
    // }

    public function calculateAndUpdateTotal(): void
    {
        $total = $this->orderItems->sum(fn($i) => $i->price * $i->quantity) + ($this->shipping_fee ?? 0);
        $this->update(['total_amount' => $total]);
    }

    // Events
    protected static function booted()
    {
        // // Tự động tính total khi lưu order
        // static::saving(function ($order) {
        //     if ($order->isDirty('shipping_fee')) {
        //         $subtotal = $order->orderItems->sum(fn($item) => $item->price * $item->quantity);
        //         $order->total_amount = $subtotal + ($order->shipping_fee ?? 0);
        //     }
        // });

        // // Tự động cập nhật timestamp khi thay đổi status
        // static::updating(function ($order) {
        //     if ($order->isDirty('status')) {
        //         match ($order->status) {
        //             OrderStatus::Paid => $order->paid_at = $order->paid_at ?? now(),
        //             OrderStatus::Shipped => $order->shipped_at = $order->shipped_at ?? now(),
        //             OrderStatus::Completed => $order->completed_at = $order->completed_at ?? now(),
        //             OrderStatus::Cancelled => $order->cancelled_at = $order->cancelled_at ?? now(),
        //             default => null,
        //         };
        //     }
        // });


        // static::saving(function ($order) {
        //     // Tính tổng tiền tự động
        //     $order->total_amount = $order->orderItems->sum(fn($item) => $item->price * $item->quantity)
        //         + ($order->shipping_fee ?? 0);
        // });

        static::updating(function ($order) {
            // Cập nhật timestamp khi status thay đổi
            if ($order->isDirty('status')) {
                match ($order->status) {
                    OrderStatus::Paid->value => $order->paid_at = $order->paid_at ?? now(),
                    OrderStatus::Shipped->value => $order->shipped_at = $order->shipped_at ?? now(),
                    OrderStatus::Completed->value => $order->completed_at = $order->completed_at ?? now(),
                    OrderStatus::Cancelled->value => $order->cancelled_at = $order->cancelled_at ?? now(),
                    default => null,
                };
            }
        });



        //Thêm ngày 11/11/2025
        // Tự động tính subtotal và total_amount khi lưu order
        // static::saving(function ($order) {
        //     // Luôn tính lại subtotal và total_amount
        //     $subtotal = $order->orderItems()->sum(DB::raw('price * quantity'));
        //     $order->subtotal = $subtotal;
        //     $order->total_amount = $subtotal + ($order->shipping_fee ?? 0);
        // });

        // static::updating(function ($order) {
        //     if ($order->isDirty('status')) {
        //         match ($order->status) {
        //             OrderStatus::Paid => $order->paid_at = $order->paid_at ?? now(),
        //             OrderStatus::Shipped => $order->shipped_at = $order->shipped_at ?? now(),
        //             OrderStatus::Completed => $order->completed_at = $order->completed_at ?? now(),
        //             OrderStatus::Cancelled => $order->cancelled_at = $order->cancelled_at ?? now(),
        //             default => null,
        //         };
        //     }
        // });


        // 🔁 Tự động cập nhật Payment Status khi Order đổi trạng thái (Suy nghĩ thêm)
        // static::updated(function ($order) {
        //     if ($order->isDirty('status')) {
        //         $payment = $order->payments()->latest()->first();

        //         if ($payment) {
        //             match ($order->status) {
        //                 \App\Enums\OrderStatus::Completed => $payment->update(['status' => \App\Enums\PaymentStatus::Success]),
        //                 \App\Enums\OrderStatus::Cancelled => $payment->update(['status' => \App\Enums\PaymentStatus::Failed]),
        //                 \App\Enums\OrderStatus::Paid => $payment->update(['status' => \App\Enums\PaymentStatus::Success]),
        //                 default => null,
        //             };
        //         }
        //     }
        // });

    }
    // ===== AUTO PAYMENT STATUS =====
    // public function getPaymentStatusAttribute(): PaymentStatus|string
    // {
    //     $payment = $this->payments->sortByDesc('created_at')->first();

    //     return $payment?->status ?? PaymentStatus::Pending;
    // }

    // public function getPaymentMethodAttribute(): PaymentMethod|string
    // {
    //     $payment = $this->payments->sortByDesc('created_at')->first();

    //     return $payment?->payment_method ?? PaymentMethod::COD;
    // }

    // public function getPaymentLabelAttribute(): string
    // {
    //     return $this->payment_status instanceof PaymentStatus
    //         ? $this->payment_status->label()
    //         : 'Chờ thanh toán';
    // }

    // public function getPaymentMethodLabelAttribute(): string
    // {
    //     return $this->payment_method instanceof PaymentMethod
    //         ? $this->payment_method->label()
    //         : 'Không rõ';
    // }

    // public function getPaymentBadgeClassAttribute(): string
    // {
    //     return match($this->payment_status->value ?? 'pending') {
    //         'success' => 'success',
    //         'failed'  => 'danger',
    //         'pending' => 'warning text-dark',
    //         default   => 'secondary',
    //     };
    // }

    /**
     * Trạng thái thanh toán mới nhất
     */
    public function getPaymentStatusAttribute(): PaymentStatus
    {
        $payment = $this->payments->sortByDesc('created_at')->first();
        return $payment?->status ?? PaymentStatus::Pending;
    }

    /**
     * Phương thức thanh toán mới nhất
     */
    public function getPaymentMethodAttribute(): PaymentMethod
    {
        $payment = $this->payments->sortByDesc('created_at')->first();
        return $payment?->payment_method ?? PaymentMethod::COD;
    }

    /**
     * Nhãn trạng thái thanh toán
     */
    public function getPaymentLabelAttribute(): string
    {
        return $this->payment_status->label();
    }

    /**
     * Nhãn phương thức thanh toán
     */
    public function getPaymentMethodLabelAttribute(): string
    {
        return $this->payment_method->label();
    }

    /**
     * Lớp badge cho trạng thái thanh toán
     */
    public function getPaymentBadgeClassAttribute(): string
    {
        return match ($this->payment_status->value ?? 'pending') {
            'success' => 'success',
            'failed'  => 'danger',
            'pending' => 'warning text-dark',
            default   => 'secondary',
        };
    }
}

// <!-- namespace App\Models;
// use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\SoftDeletes;
// use App\Enums\OrderStatus;
// use App\Models\Scopes\OrderScopes;

// class Order extends Model
// {
// use HasFactory, SoftDeletes, OrderScopes;

// protected $fillable = [
// 'user_id',
// 'order_number',
// 'total_amount',
// 'shipping_fee',
// 'customer_note',
// 'admin_note',
// 'status',
// 'delivered_at',
// 'completed_at',
// 'cancelled_at'
// ];

// protected $casts = [
// 'total_amount' => 'decimal:2',
// 'shipping_fee' => 'decimal:2',
// 'delivered_at' => 'datetime',
// 'completed_at' => 'datetime',
// 'cancelled_at' => 'datetime',
// 'status' => OrderStatus::class,
// ];

// public function user()
// {
// return $this->belongsTo(User::class);
// }

// public function shippingAddress()
// {
// return $this->hasOne(ShippingAddress::class);
// }

// public function orderItems()
// {
// return $this->hasMany(OrderItem::class);
// }

// public function payments()
// {
// return $this->hasMany(Payment::class);
// }
// public function getSubtotalAttribute(): float
// {
// return $this->orderItems->sum(function ($item) {
// return $item->price * $item->quantity;
// });
// }
// public function getTotalAmountAttribute($value): float
// {
// // Nếu DB đã có giá trị -> ưu tiên hiển thị
// if ($value > 0) {
// return (float) $value;
// }

// // Nếu chưa có, tính động theo item + shipping_fee
// return (float) ($this->subtotal + $this->shipping_fee);
// }
// protected static function booted()
// {
// static::saving(function ($order) {
// $subtotal = $order->orderItems->sum(fn($item) => $item->price * $item->quantity);
// $order->total_amount = $subtotal + $order->shipping_fee;
// });
// }

// } -->