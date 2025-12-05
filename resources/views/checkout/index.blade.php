@extends('layouts.app')

@section('title', 'Thanh toán')

@section('content')
    <div x-data="checkoutApp()" x-init="init()" class="min-h-screen bg-gray-50">
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
                        <div class="w-8 h-8 rounded-full flex items-center justify-center"
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
                    <div class="flex-1 h-1 bg-gray-200">
                        <div class="h-full bg-blue-500 transition-all"
                            :style="'width: ' + (step === 'payment' ? '100%' : '0')"></div>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center"
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

    @push('scripts')
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
                        receiver_name: '',
                        phone: '',
                        address: '',
                        ward: '',
                        district: '',
                        province: '',
                        note: '',
                        payment_method: 'card'
                    },

                    orderSummary: {
                        subtotal: {{ $cartItems->sum(fn($item) => $item->product->price * $item->quantity) }},
                        shippingFee: 30000,
                        total: {{ $cartItems->sum(fn($item) => $item->product->price * $item->quantity) + 30000 }}
                    },

                    init() {
                        // Initialize Stripe
                        this.stripe = Stripe('{{ config('services.stripe.key') }}');

                        // Setup Stripe Elements
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
                            this.error = 'Số điện thoại không hợp lệ';
                            return false;
                        }

                        return true;
                    },

                    continueToPayment() {
                        this.error = '';

                        if (this.validateForm()) {
                            this.step = 'payment';

                            // Mount card element khi chuyển sang payment step
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
                            const orderResponse = await fetch('{{ route('checkout.store') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Authorization': 'Bearer ' + localStorage.getItem('token')
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

                            // Step 2: Confirm payment with Stripe
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
                            window.location.href = '{{ url('/checkout/success') }}/' + orderData.order.id;

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
@endsection



{{-- 
@extends('layouts.app')

@section('title', 'Thanh toán')

@section('content')
    <div x-data="checkoutApp()" x-init="init()" class="min-h-screen bg-gray-50">
        <!-- Header -->
        <div class="bg-white shadow-sm border-b">
            <div class="max-w-6xl mx-auto px-4 py-4 flex items-center justify-between">
                <h1 class="text-2xl font-bold text-gray-800">Thanh toán</h1>
                <div class="flex items-center gap-2 text-sm text-gray-600">
                    <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    <span>Bảo mật bởi Stripe</span>
                </div>
            </div>
        </div>

        <!-- Progress -->
        <div class="max-w-6xl mx-auto px-4 py-6 flex items-center gap-4">
            <div class="flex items-center gap-2">
                <div :class="step === 'info' ? 'bg-blue-500 text-white' : 'bg-green-500 text-white'"
                    class="w-8 h-8 rounded-full flex items-center justify-center">
                    <template x-if="step === 'info'">1</template>
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
            <div class="flex-1 h-1 bg-gray-200">
                <div class="h-full bg-blue-500 transition-all" :style="'width: ' + (step === 'payment' ? '100%' : '0')">
                </div>
            </div>
            <div class="flex items-center gap-2">
                <div :class="step === 'payment' ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-500'"
                    class="w-8 h-8 rounded-full flex items-center justify-center">2</div>
                <span class="text-sm font-medium text-gray-700">Thanh toán</span>
            </div>
        </div>

        <div class="max-w-6xl mx-auto px-4 py-8 grid lg:grid-cols-3 gap-8">
            <!-- Main -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Step 1: Info -->
                <div x-show="step === 'info'" class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-xl font-bold mb-6 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Thông tin giao hàng
                    </h2>

                    <div x-show="error" class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg flex items-start gap-3">
                        <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                clip-rule="evenodd" />
                        </svg>
                        <p class="text-sm text-red-700" x-text="error"></p>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium mb-2">Họ tên người nhận *</label>
                            <input type="text" x-model="formData.receiver_name"
                                class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Nguyễn Văn A">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">Số điện thoại *</label>
                            <input type="tel" x-model="formData.phone"
                                class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="0912345678">
                        </div>
                        <div class="grid md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-2">Tỉnh/Thành *</label>
                                <select x-model="formData.province"
                                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Chọn tỉnh/thành</option>
                                    <option value="Hà Nội">Hà Nội</option>
                                    <option value="Hồ Chí Minh">Hồ Chí Minh</option>
                                    <option value="Đà Nẵng">Đà Nẵng</option>
                                    <option value="Hải Phòng">Hải Phòng</option>
                                    <option value="Cần Thơ">Cần Thơ</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">Quận/Huyện *</label>
                                <select x-model="formData.district"
                                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Chọn quận/huyện</option>
                                    <option value="Ba Đình">Ba Đình</option>
                                    <option value="Hoàn Kiếm">Hoàn Kiếm</option>
                                    <option value="Hai Bà Trưng">Hai Bà Trưng</option>
                                    <option value="Đống Đa">Đống Đa</option>
                                    <option value="Cầu Giấy">Cầu Giấy</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">Phường/Xã *</label>
                                <select x-model="formData.ward"
                                    class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">Chọn phường/xã</option>
                                    <option value="Phường 1">Phường 1</option>
                                    <option value="Phường 2">Phường 2</option>
                                    <option value="Phường 3">Phường 3</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">Địa chỉ chi tiết *</label>
                            <input type="text" x-model="formData.address"
                                class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Số nhà, tên đường...">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">Ghi chú</label>
                            <textarea x-model="formData.note" rows="3"
                                class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
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
                <div x-show="step === 'payment'" class="bg-white rounded-xl shadow-sm p-6 space-y-4">
                    <h2 class="text-xl font-bold mb-6 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                        Thông tin thanh toán
                    </h2>

                    <div x-show="error"
                        class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg flex items-start gap-3">
                        <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                clip-rule="evenodd" />
                        </svg>
                        <p class="text-sm text-red-700" x-text="error"></p>
                    </div>

                    <!-- Stripe Card -->
                    <form id="payment-form" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium mb-2">Thông tin thẻ</label>
                            <div id="card-element" class="p-4 border rounded-lg bg-white"></div>
                        </div>
                        <button type="button" @click="processPayment()" :disabled="loading"
                            class="w-full mt-6 bg-green-500 text-white py-4 rounded-lg font-semibold hover:bg-green-600 flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                            <template x-if="!loading"><span>Thanh toán <span
                                        x-text="formatCurrency(orderSummary.total)"></span></span></template>
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
                        <button @click="step = 'info'"
                            class="w-full mt-3 text-gray-600 py-3 rounded-lg font-medium hover:bg-gray-100">← Quay
                            lại</button>
                    </form>
                </div>
            </div>

            <!-- Sidebar Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm p-6 sticky top-4 space-y-4">
                    <h3 class="text-lg font-bold">Đơn hàng của bạn</h3>
                    <div class="space-y-4 max-h-64 overflow-y-auto">
                        @foreach ($cartItems as $item)
                            <div class="flex gap-3">
                                <img src="{{ $item->product->image_url ?? 'https://via.placeholder.com/60' }}"
                                    alt="{{ $item->product->name }}" class="w-16 h-16 object-cover rounded-lg">
                                <div class="flex-1">
                                    <h4 class="text-sm font-medium">{{ $item->product->name }}</h4>
                                    @if ($item->variant)
                                        <p class="text-xs text-gray-500">{{ $item->variant->name }}</p>
                                    @endif
                                    <div class="flex justify-between mt-1">
                                        <span class="text-xs text-gray-500">x{{ $item->quantity }}</span>
                                        <span
                                            class="text-sm font-medium">{{ number_format($item->product->price * $item->quantity) }}đ</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="border-t pt-4 space-y-2">
                        <div class="flex justify-between text-sm"><span>Tạm tính</span><span
                                x-text="formatCurrency(orderSummary.subtotal)"></span></div>
                        <div class="flex justify-between text-sm"><span>Phí vận chuyển</span><span
                                x-text="formatCurrency(orderSummary.shippingFee)"></span></div>
                        <div class="border-t pt-2 flex justify-between"><span class="font-bold">Tổng cộng</span><span
                                class="font-bold text-blue-600 text-lg"
                                x-text="formatCurrency(orderSummary.total)"></span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
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
                        receiver_name: '',
                        phone: '',
                        address: '',
                        ward: '',
                        district: '',
                        province: '',
                        note: '',
                        payment_method: 'card'
                    },
                    orderSummary: {
                        subtotal: {{ $cartItems->sum(fn($item) => $item->product->price * $item->quantity) }},
                        shippingFee: 30000,
                        total: {{ $cartItems->sum(fn($item) => $item->product->price * $item->quantity) + 30000 }}
                    },
                    init() {
                        this.stripe = Stripe('{{ config('services.stripe.key') }}');
                        const elements = this.stripe.elements();
                        this.cardElement = elements.create('card', {
                            style: {
                                base: {
                                    fontSize: '16px',
                                    color: '#32325d',
                                    fontFamily: '-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,sans-serif',
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
                        if (!this.formData.receiver_name || !this.formData.phone || !this.formData.address || !this.formData
                            .ward || !this.formData.district || !this.formData.province) {
                            this.error = 'Vui lòng điền đầy đủ thông tin giao hàng';
                            return false;
                        }
                        if (!/^[0-9]{10}$/.test(this.formData.phone)) {
                            this.error = 'Số điện thoại không hợp lệ';
                            return false;
                        }
                        return true;
                    },
                    continueToPayment() {
                        this.error = '';
                        if (!this.validateForm()) return;
                        this.step = 'payment';
                        this.$nextTick(() => {
                            if (!this.cardElement._parent) this.cardElement.mount('#card-element');
                        });
                        window.scrollTo({
                            top: 0,
                            behavior: 'smooth'
                        });
                    },
                    async processPayment() {
                        this.loading = true;
                        this.error = '';
                        try {
                            const response = await fetch('{{ route('checkout.store') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    ...this.formData,
                                    shipping_fee: this.orderSummary.shippingFee
                                })
                            });
                            const data = await response.json();
                            if (!data.success) throw new Error(data.message || 'Có lỗi khi tạo đơn hàng');
                            const orderId = data.order?.id;
                            if (!orderId) throw new Error('Order ID không tồn tại');
                            if (data.payment?.gateway_response?.client_secret) {
                                this.clientSecret = data.payment.gateway_response.client_secret;
                                const {
                                    error: stripeError,
                                    paymentIntent
                                } = await this.stripe.confirmCardPayment(this.clientSecret, {
                                    payment_method: {
                                        card: this.cardElement,
                                        billing_details: {
                                            name: this.formData.receiver_name,
                                            phone: this.formData.phone
                                        }
                                    }
                                });
                                if (stripeError) throw new Error(stripeError.message);
                                console.log('PaymentIntent:', paymentIntent);
                            }
                            window.location.href = '{{ url('/checkout/success') }}/' + orderId;
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
@endsection --}}
