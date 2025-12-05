{{-- @extends('layouts.app')

@section('title', 'Thanh toán')

@section('styles')
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
@endsection

@section('content')
    <div x-data="checkoutApp()" x-init="init()" x-cloak class="min-h-screen bg-gray-50">
        <!-- Header -->
        <div class="bg-white shadow-sm border-b">
            <div class="max-w-6xl mx-auto px-4 py-4">
                <div class="flex items-center justify-between">
                    <h1 class="text-2xl font-bold text-gray-800">Thanh toán</h1>
                    <div class="flex items-center gap-2 text-sm">
                        <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        <span class="text-gray-600">Bảo mật bởi Stripe</span>
                    </div>
                </div>

                <!-- Progress Steps -->
                <div class="flex items-center gap-4 mt-6">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center transition-all"
                            :class="step === 'info' ? 'bg-blue-500 text-white' : 'bg-green-500 text-white'">
                            <template x-if="step === 'info'">
                                <span>1</span>
                            </template>
                            <template x-if="step !== 'info'">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                            </template>
                        </div>
                        <span class="text-sm font-medium text-gray-700">Thông tin</span>
                    </div>
                    <div class="flex-1 h-1 bg-gray-200 rounded">
                        <div class="h-full bg-blue-500 transition-all rounded"
                            :style="'width: ' + (step === 'payment' ? '100%' : '0')"></div>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center transition-all"
                            :class="step === 'payment' ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-500'">
                            2
                        </div>
                        <span class="text-sm font-medium text-gray-700">Thanh toán</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-6xl mx-auto px-4 py-8">
            <div class="grid lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2">
                    <!-- Step 1: Shipping Info -->
                    <div x-show="step === 'info'" class="bg-white rounded-xl shadow-sm p-6">
                        <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Thông tin giao hàng
                        </h2>

                        <!-- Error Message -->
                        <div x-show="error"
                            class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg flex items-start gap-3">
                            <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                            <p class="text-sm text-red-700" x-text="error"></p>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Họ tên người nhận *
                                </label>
                                <input type="text" x-model="formData.receiver_name"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="Nguyễn Văn A">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Số điện thoại *
                                </label>
                                <input type="tel" x-model="formData.phone"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="0912345678">
                            </div>

                            <div class="grid md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Tỉnh/Thành *</label>
                                    <select x-model="formData.province"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="">Chọn tỉnh/thành</option>
                                        <option value="Hà Nội">Hà Nội</option>
                                        <option value="Hồ Chí Minh">Hồ Chí Minh</option>
                                        <option value="Đà Nẵng">Đà Nẵng</option>
                                        <option value="Hải Phòng">Hải Phòng</option>
                                        <option value="Cần Thơ">Cần Thơ</option>
                                        <option value="An Giang">An Giang</option>
                                        <option value="Bà Rịa - Vũng Tàu">Bà Rịa - Vũng Tàu</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Quận/Huyện *</label>
                                    <select x-model="formData.district"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="">Chọn quận/huyện</option>
                                        <option value="Ba Đình">Ba Đình</option>
                                        <option value="Hoàn Kiếm">Hoàn Kiếm</option>
                                        <option value="Hai Bà Trưng">Hai Bà Trưng</option>
                                        <option value="Đống Đa">Đống Đa</option>
                                        <option value="Cầu Giấy">Cầu Giấy</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Phường/Xã *</label>
                                    <select x-model="formData.ward"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="">Chọn phường/xã</option>
                                        <option value="Phường 1">Phường 1</option>
                                        <option value="Phường 2">Phường 2</option>
                                        <option value="Phường 3">Phường 3</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Địa chỉ chi tiết *</label>
                                <input type="text" x-model="formData.address"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="Số nhà, tên đường...">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Ghi chú</label>
                                <textarea x-model="formData.note" rows="3"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="Ghi chú cho người bán..."></textarea>
                            </div>
                        </div>

                        <button @click="continueToPayment()"
                            class="w-full mt-6 bg-blue-500 text-white py-4 rounded-lg font-semibold hover:bg-blue-600 transition-colors flex items-center justify-center gap-2">
                            Tiếp tục thanh toán
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                        </button>
                    </div>

                    <!-- Step 2: Payment -->
                    <div x-show="step === 'payment'" class="space-y-6">
                        <div class="bg-white rounded-xl shadow-sm p-6">
                            <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2">
                                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                                Thông tin thanh toán
                            </h2>

                            <!-- Error Message -->
                            <div x-show="error"
                                class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg flex items-start gap-3">
                                <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="currentColor"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                        clip-rule="evenodd" />
                                </svg>
                                <p class="text-sm text-red-700" x-text="error"></p>
                            </div>

                            <!-- Stripe Card Element -->
                            <form id="payment-form">
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Thông tin thẻ</label>
                                        <div id="card-element" class="p-4 border border-gray-300 rounded-lg bg-white">
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-6 p-4 bg-blue-50 rounded-lg flex items-start gap-3">
                                    <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                    <div>
                                        <p class="text-sm font-medium text-blue-900">Thanh toán bảo mật</p>
                                        <p class="text-xs text-blue-700 mt-1">
                                            Thông tin thẻ của bạn được mã hóa và bảo mật bởi Stripe
                                        </p>
                                    </div>
                                </div>

                                <button type="button" @click="processPayment()" :disabled="loading"
                                    class="w-full mt-6 bg-green-500 text-white py-4 rounded-lg font-semibold hover:bg-green-600 transition-colors flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                                    <template x-if="!loading">
                                        <span>Thanh toán <span x-text="formatCurrency(orderSummary.total)"></span></span>
                                    </template>
                                    <template x-if="loading">
                                        <span class="flex items-center gap-2">
                                            <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                                    stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor"
                                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                </path>
                                            </svg>
                                            Đang xử lý...
                                        </span>
                                    </template>
                                </button>
                            </form>

                            <button @click="step = 'info'"
                                class="w-full mt-3 text-gray-600 py-3 rounded-lg font-medium hover:bg-gray-100 transition-colors">
                                ← Quay lại
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Order Summary Sidebar -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-sm p-6 sticky top-4">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">Đơn hàng của bạn</h3>

                        <!-- Cart Items -->
                        <div class="space-y-4 mb-4 max-h-64 overflow-y-auto">
                            @foreach ($cartItems as $item)
                                <div class="flex gap-3">
                                    <img src="{{ $item->product->image_url ?? 'https://via.placeholder.com/60' }}"
                                        alt="{{ $item->product->name }}" class="w-16 h-16 object-cover rounded-lg">
                                    <div class="flex-1">
                                        <h4 class="text-sm font-medium text-gray-800">{{ $item->product->name }}</h4>
                                        @if ($item->variant)
                                            <p class="text-xs text-gray-500">{{ $item->variant->name }}</p>
                                        @endif
                                        <div class="flex items-center justify-between mt-1">
                                            <span class="text-xs text-gray-500">x{{ $item->quantity }}</span>
                                            <span
                                                class="text-sm font-medium text-gray-800">{{ number_format($item->product->price * $item->quantity) }}đ</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="border-t pt-4 space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Tạm tính</span>
                                <span class="font-medium" x-text="formatCurrency(orderSummary.subtotal)"></span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Phí vận chuyển</span>
                                <span class="font-medium" x-text="formatCurrency(orderSummary.shippingFee)"></span>
                            </div>
                            <div class="border-t pt-2 flex justify-between">
                                <span class="font-bold text-gray-800">Tổng cộng</span>
                                <span class="font-bold text-blue-600 text-lg"
                                    x-text="formatCurrency(orderSummary.total)"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        function checkoutApp() {
            return {
                step: 'info',
                loading: false,
                error: '',
                stripe: null,
                cardElement: null,
                clientSecret: '',

                formData: {
                    receiver_name: '{{ $defaultAddress->receiver_name ?? '' }}',
                    phone: '{{ $defaultAddress->phone ?? '' }}',
                    address: '{{ $defaultAddress->address ?? '' }}',
                    ward: '{{ $defaultAddress->ward ?? '' }}',
                    district: '{{ $defaultAddress->district ?? '' }}',
                    province: '{{ $defaultAddress->province ?? '' }}',
                    note: '',
                    payment_method: 'card'
                },

                orderSummary: {
                    subtotal: {{ $subtotal }},
                    shippingFee: {{ $shippingFee }},
                    total: {{ $total }}
                },

                init() {
                    // Initialize Stripe
                    this.stripe = Stripe('{{ config('services.stripe.key') }}');

                    const elements = this.stripe.elements();
                    this.cardElement = elements.create('card', {
                        style: {
                            base: {
                                fontSize: '16px',
                                color: '#32325d',
                                fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
                                '::placeholder': {
                                    color: '#aab7c4'
                                }
                            },
                            invalid: {
                                color: '#fa755a',
                                iconColor: '#fa755a'
                            }
                        }
                    });
                },

                formatCurrency(amount) {
                    return new Intl.NumberFormat('vi-VN', {
                        style: 'currency',
                        currency: 'VND'
                    }).format(amount);
                },

                validateForm() {
                    if (!this.formData.receiver_name || !this.formData.phone ||
                        !this.formData.address || !this.formData.ward ||
                        !this.formData.district || !this.formData.province) {
                        this.error = 'Vui lòng điền đầy đủ thông tin giao hàng';
                        return false;
                    }

                    if (!/^[0-9]{10}$/.test(this.formData.phone)) {
                        this.error = 'Số điện thoại không hợp lệ (10 chữ số)';
                        return false;
                    }

                    return true;
                },

                continueToPayment() {
                    this.error = '';

                    if (this.validateForm()) {
                        this.step = 'payment';

                        this.$nextTick(() => {
                            if (!this.cardElement._parent) {
                                this.cardElement.mount('#card-element');
                            }
                        });

                        window.scrollTo({
                            top: 0,
                            behavior: 'smooth'
                        });
                    }
                },

                async processPayment() {
                    this.loading = true;
                    this.error = '';

                    try {
                        // Step 1: Create order
                        const orderResponse = await fetch('{{ route('client.checkout.process') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            },
                            body: JSON.stringify({
                                ...this.formData,
                                shipping_fee: this.orderSummary.shippingFee
                            })
                        });

                        const orderData = await orderResponse.json();

                        if (!orderData.success) {
                            throw new Error(orderData.message);
                        }

                        this.clientSecret = orderData.payment.gateway_response.client_secret;

                        // Step 2: Confirm payment với Stripe
                        const {
                            error: stripeError,
                            paymentIntent
                        } = await this.stripe.confirmCardPayment(
                            this.clientSecret, {
                                payment_method: {
                                    card: this.cardElement,
                                    billing_details: {
                                        name: this.formData.receiver_name,
                                        phone: this.formData.phone,
                                    }
                                }
                            }
                        );

                        if (stripeError) {
                            throw new Error(stripeError.message);
                        }

                        // Success - redirect to success page
                        window.location.href = '{{ url('/client/checkout/success') }}/' + orderData.order.id;

                    } catch (err) {
                        this.error = err.message || 'Có lỗi xảy ra khi thanh toán';
                        console.error('Payment error:', err);
                    } finally {
                        this.loading = false;
                    }
                }
            }
        }
    </script>
@endsection
 --}}



@extends('client.layouts.master')

@section('title', 'Thanh toán')

@push('styles')
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        [x-cloak] {
            display: none !important;
        }

        body {
            background: #f8fafc;
        }

        .checkout-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 25px 0;
            box-shadow: 0 4px 20px rgba(102, 126, 234, 0.3);
        }

        .progress-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .step-item {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
        }

        .step-number {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 20px;
            transition: all 0.3s;
            z-index: 2;
        }

        .step-line {
            position: absolute;
            top: 25px;
            left: 50%;
            width: 100%;
            height: 4px;
            background: rgba(255, 255, 255, 0.3);
            z-index: 1;
        }

        .step-line-fill {
            height: 100%;
            background: white;
            transition: width 0.5s ease;
        }

        .form-section {
            background: white;
            border-radius: 20px;
            padding: 35px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            margin-bottom: 25px;
        }

        .section-title {
            font-size: 24px;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-weight: 600;
            color: #475569;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .required {
            color: #ef4444;
        }

        .form-control {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 15px;
            transition: all 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        .form-control:invalid {
            border-color: #ef4444;
        }

        .alert {
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            display: flex;
            align-items: start;
            gap: 12px;
            font-weight: 500;
        }

        .alert-danger {
            background: #fee;
            color: #dc2626;
            border-left: 4px solid #ef4444;
        }

        .alert-info {
            background: #dbeafe;
            color: #1e40af;
            border-left: 4px solid #3b82f6;
        }

        #card-element {
            padding: 16px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            background: white;
            transition: all 0.3s;
        }

        #card-element:focus-within {
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        .btn {
            padding: 16px 32px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 16px;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            width: 100%;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-primary:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.5);
        }

        .btn-primary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .btn-success {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            width: 100%;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);
        }

        .btn-success:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.5);
        }

        .btn-secondary {
            background: white;
            color: #667eea;
            border: 2px solid #667eea;
            width: 100%;
        }

        .btn-secondary:hover {
            background: #667eea;
            color: white;
        }

        .order-summary {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            position: sticky;
            top: 20px;
        }

        .summary-title {
            font-size: 22px;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e2e8f0;
        }

        .cart-item-summary {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid #f1f5f9;
        }

        .item-image-small {
            width: 70px;
            height: 70px;
            border-radius: 12px;
            overflow: hidden;
            flex-shrink: 0;
        }

        .item-image-small img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 15px;
        }

        .summary-total {
            display: flex;
            justify-content: space-between;
            font-size: 24px;
            font-weight: 900;
            color: #1e293b;
            margin: 20px 0;
            padding: 20px;
            background: linear-gradient(135deg, #f8fafc, #e2e8f0);
            border-radius: 12px;
        }

        .summary-total .value {
            color: #ef4444;
        }

        .spinner {
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-top: 3px solid white;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .security-badge {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 15px;
            background: #f0fdf4;
            border-radius: 12px;
            margin-top: 20px;
            border: 1px solid #86efac;
        }

        @media (max-width: 1024px) {
            .checkout-container {
                grid-template-columns: 1fr !important;
            }

            .order-summary {
                position: static;
            }
        }
    </style>
@endpush

@section('content')
    <div x-data="checkoutApp()" x-init="init()" x-cloak>
        <!-- Header -->
        <div class="checkout-header">
            <div class="container">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h1 class="text-white mb-0" style="font-size: 28px; font-weight: 800;">
                        <i class="fas fa-credit-card me-2"></i>Thanh Toán
                    </h1>
                    <div class="d-flex align-items-center gap-2" style="color: rgba(255,255,255,0.9);">
                        <i class="fas fa-shield-alt"></i>
                        <small>Bảo mật bởi Stripe</small>
                    </div>
                </div>

                <!-- Progress Steps -->
                <div class="progress-container">
                    <div class="d-flex position-relative">
                        <div class="step-item">
                            <div class="step-number"
                                :style="step === 'info' ? 'background: white; color: #667eea;' :
                                    'background: rgba(255,255,255,0.9); color: #10b981;'">
                                <template x-if="step === 'info'">1</template>
                                <template x-if="step !== 'info'"><i class="fas fa-check"></i></template>
                            </div>
                            <span class="text-white mt-2" style="font-weight: 600;">Thông tin</span>
                        </div>

                        <div class="step-line" style="width: calc(100% - 50px); left: calc(50% + 25px);">
                            <div class="step-line-fill" :style="'width: ' + (step === 'payment' ? '100%' : '0')"></div>
                        </div>

                        <div class="step-item">
                            <div class="step-number"
                                :style="step === 'payment' ? 'background: white; color: #667eea;' :
                                    'background: rgba(255,255,255,0.3); color: white;'">
                                2
                            </div>
                            <span class="text-white mt-2" style="font-weight: 600;">Thanh toán</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container py-5">
            <div class="row g-4">
                <!-- Main Content -->
                <div class="col-lg-8">
                    <!-- Step 1: Shipping Info -->
                    <div x-show="step === 'info'" class="form-section">
                        <h2 class="section-title">
                            <i class="fas fa-map-marker-alt text-primary"></i>
                            Thông tin giao hàng
                        </h2>

                        <div x-show="error" class="alert alert-danger">
                            <i class="fas fa-exclamation-circle"></i>
                            <span x-text="error"></span>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Họ tên người nhận <span class="required">*</span></label>
                                    <input type="text" class="form-control" x-model="formData.receiver_name"
                                        placeholder="Nguyễn Văn A" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Số điện thoại <span class="required">*</span></label>
                                    <input type="tel" class="form-control" x-model="formData.phone"
                                        placeholder="0912345678" required pattern="[0-9]{10}">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Tỉnh/Thành phố <span class="required">*</span></label>
                                    <select class="form-control" x-model="formData.province" required>
                                        <option value="">Chọn tỉnh/thành</option>
                                        <option value="Hà Nội">Hà Nội</option>
                                        <option value="Hồ Chí Minh">Hồ Chí Minh</option>
                                        <option value="Đà Nẵng">Đà Nẵng</option>
                                        <option value="Hải Phòng">Hải Phòng</option>
                                        <option value="Cần Thơ">Cần Thơ</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Quận/Huyện <span class="required">*</span></label>
                                    <select class="form-control" x-model="formData.district" required>
                                        <option value="">Chọn quận/huyện</option>
                                        <option value="Ba Đình">Ba Đình</option>
                                        <option value="Hoàn Kiếm">Hoàn Kiếm</option>
                                        <option value="Hai Bà Trưng">Hai Bà Trưng</option>
                                        <option value="Đống Đa">Đống Đa</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">Phường/Xã <span class="required">*</span></label>
                                    <select class="form-control" x-model="formData.ward" required>
                                        <option value="">Chọn phường/xã</option>
                                        <option value="Phường 1">Phường 1</option>
                                        <option value="Phường 2">Phường 2</option>
                                        <option value="Phường 3">Phường 3</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label">Địa chỉ chi tiết <span class="required">*</span></label>
                                    <input type="text" class="form-control" x-model="formData.address"
                                        placeholder="Số nhà, tên đường..." required>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label class="form-label">Ghi chú</label>
                                    <textarea class="form-control" x-model="formData.note" rows="3"
                                        placeholder="Ghi chú cho người bán (tùy chọn)"></textarea>
                                </div>
                            </div>
                        </div>

                        <button @click="continueToPayment()" class="btn btn-primary mt-4">
                            Tiếp tục thanh toán
                            <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>

                    <!-- Step 2: Payment -->
                    <div x-show="step === 'payment'" class="form-section">
                        <h2 class="section-title">
                            <i class="fas fa-credit-card text-success"></i>
                            Thông tin thanh toán
                        </h2>

                        <div x-show="error" class="alert alert-danger">
                            <i class="fas fa-exclamation-circle"></i>
                            <span x-text="error"></span>
                        </div>

                        <form id="payment-form">
                            <div class="form-group">
                                <label class="form-label">Thông tin thẻ <span class="required">*</span></label>
                                <div id="card-element"></div>
                            </div>

                            <div class="security-badge">
                                <i class="fas fa-lock text-success" style="font-size: 24px;"></i>
                                <div>
                                    <div style="font-weight: 700; color: #059669;">Thanh toán bảo mật</div>
                                    <small style="color: #6b7280;">Thông tin thẻ được mã hóa và bảo mật bởi Stripe</small>
                                </div>
                            </div>

                            <button type="button" @click="processPayment()" :disabled="loading"
                                class="btn btn-success mt-4">
                                <template x-if="!loading">
                                    <span>Thanh toán <span x-text="formatCurrency(orderSummary.total)"></span></span>
                                </template>
                                <template x-if="loading">
                                    <span class="d-flex align-items-center gap-2">
                                        <div class="spinner"></div>
                                        Đang xử lý...
                                    </span>
                                </template>
                            </button>

                            <button type="button" @click="step = 'info'" class="btn btn-secondary mt-3">
                                <i class="fas fa-arrow-left"></i>
                                Quay lại
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="col-lg-4">
                    <div class="order-summary">
                        <h3 class="summary-title">
                            <i class="fas fa-receipt me-2"></i>Đơn hàng của bạn
                        </h3>

                        <div style="max-height: 300px; overflow-y: auto; margin-bottom: 20px;">
                            @foreach ($cartItems as $item)
                                <div class="cart-item-summary">
                                    <div class="item-image-small">
                                        <img src="{{ $item->product->image ?? 'https://via.placeholder.com/70' }}"
                                            alt="{{ $item->product->name }}">
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 style="font-weight: 600; margin-bottom: 4px; font-size: 14px;">
                                            {{ $item->product->name }}
                                        </h6>
                                        @if ($item->variant)
                                            <small class="text-muted d-block mb-1">{{ $item->variant->name }}</small>
                                        @endif
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">x{{ $item->quantity }}</small>
                                            <strong style="color: #ef4444;">
                                                {{ number_format($item->product->price * $item->quantity) }}đ
                                            </strong>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="summary-row">
                            <span class="text-muted">Tạm tính:</span>
                            <strong x-text="formatCurrency(orderSummary.subtotal)"></strong>
                        </div>

                        <div class="summary-row">
                            <span class="text-muted">Phí vận chuyển:</span>
                            <strong x-text="formatCurrency(orderSummary.shippingFee)"></strong>
                        </div>

                        <div class="summary-total">
                            <span>Tổng cộng:</span>
                            <span class="value" x-text="formatCurrency(orderSummary.total)"></span>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <small>Bạn sẽ được chuyển đến trang xác nhận sau khi thanh toán thành công</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        function checkoutApp() {
            return {
                step: 'info',
                loading: false,
                error: '',
                stripe: null,
                cardElement: null,
                clientSecret: '',

                formData: {
                    receiver_name: '{{ $defaultAddress->receiver_name ?? '' }}',
                    phone: '{{ $defaultAddress->phone ?? '' }}',
                    address: '{{ $defaultAddress->address ?? '' }}',
                    ward: '{{ $defaultAddress->ward ?? '' }}',
                    district: '{{ $defaultAddress->district ?? '' }}',
                    province: '{{ $defaultAddress->province ?? '' }}',
                    note: '',
                    payment_method: 'card'
                },

                orderSummary: {
                    subtotal: {{ $subtotal }},
                    shippingFee: {{ $shippingFee }},
                    total: {{ $total }}
                },

                init() {
                    this.stripe = Stripe('{{ config('services.stripe.key') }}');
                    const elements = this.stripe.elements();
                    this.cardElement = elements.create('card', {
                        style: {
                            base: {
                                fontSize: '16px',
                                color: '#1e293b',
                                fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
                                '::placeholder': {
                                    color: '#94a3b8'
                                }
                            },
                            invalid: {
                                color: '#ef4444',
                                iconColor: '#ef4444'
                            }
                        }
                    });
                },

                formatCurrency(amount) {
                    return new Intl.NumberFormat('vi-VN', {
                        style: 'currency',
                        currency: 'VND'
                    }).format(amount);
                },

                validateForm() {
                    if (!this.formData.receiver_name || !this.formData.phone ||
                        !this.formData.address || !this.formData.ward ||
                        !this.formData.district || !this.formData.province) {
                        this.error = 'Vui lòng điền đầy đủ thông tin giao hàng';
                        return false;
                    }

                    if (!/^[0-9]{10}$/.test(this.formData.phone)) {
                        this.error = 'Số điện thoại không hợp lệ (10 chữ số)';
                        return false;
                    }

                    return true;
                },

                continueToPayment() {
                    this.error = '';
                    if (this.validateForm()) {
                        this.step = 'payment';
                        this.$nextTick(() => {
                            if (!this.cardElement._parent) {
                                this.cardElement.mount('#card-element');
                            }
                        });
                        window.scrollTo({
                            top: 0,
                            behavior: 'smooth'
                        });
                    }
                },

                async processPayment() {
                    this.loading = true;
                    this.error = '';

                    try {
                        const orderResponse = await fetch('{{ route('client.checkout.process') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            },
                            body: JSON.stringify({
                                ...this.formData,
                                shipping_fee: this.orderSummary.shippingFee
                            })
                        });

                        const orderData = await orderResponse.json();

                        if (!orderData.success) {
                            throw new Error(orderData.message);
                        }

                        this.clientSecret = orderData.payment.gateway_response.client_secret;

                        const {
                            error: stripeError,
                            paymentIntent
                        } = await this.stripe.confirmCardPayment(
                            this.clientSecret, {
                                payment_method: {
                                    card: this.cardElement,
                                    billing_details: {
                                        name: this.formData.receiver_name,
                                        phone: this.formData.phone,
                                    }
                                }
                            }
                        );

                        if (stripeError) {
                            throw new Error(stripeError.message);
                        }

                        window.location.href = '{{ url('/client/checkout/success') }}/' + orderData.order.id;

                    } catch (err) {
                        this.error = err.message || 'Có lỗi xảy ra khi thanh toán';
                        console.error('Payment error:', err);
                    } finally {
                        this.loading = false;
                    }
                }
            }
        }
    </script>
@endpush
