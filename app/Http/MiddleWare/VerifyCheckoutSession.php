<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifyCheckoutSession
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Verify user has items in cart
        if (auth()->check() && auth()->user()->cartItems()->count() === 0) {
            return redirect()->route('client.cart')
                ->with('error', 'Giỏ hàng của bạn đang trống');
        }

        // Verify checkout session hasn't expired
        $checkoutStarted = session('checkout_started_at');
        if ($checkoutStarted && now()->diffInMinutes($checkoutStarted) > config('checkout.session_timeout', 30)) {
            session()->forget('checkout_started_at');
            return redirect()->route('client.cart')
                ->with('error', 'Phiên checkout đã hết hạn. Vui lòng thử lại.');
        }

        // Set checkout session
        if (!$checkoutStarted) {
            session(['checkout_started_at' => now()]);
        }

        return $next($request);
    }
}
