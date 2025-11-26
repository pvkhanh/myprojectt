@extends('layouts.client')

@section('title', 'Thanh toán đơn hàng')

@push('styles')
<style>
.payment-container {
    max-width: 600px;
    margin: 60px auto;
    padding: 40px;
    background: white;
    border-radius: 16px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.1);
}

.payment-header {
    text-align: center;
    margin-bottom: 40px;
}

.payment-amount {
    font-size: 48px;
    font-weight: bold;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    margin-bottom: 8px;
}

.order-info {
    background: #f7fafc;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 30px;
}

.info-row {
    display: flex;
    justify-content: space-between;
    padding: 12px 0;
    border-bottom: 1px solid #e2e8f0;
}

.info-row:last-child {
    border-bottom: none;
}

#card-element {
    padding: 16px;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    margin-bottom: 20px;
}

#card-element.StripeElement--focus {
    border-color: #667eea;
}

#card-errors {
    color: #e53e3e;
    font-size: 14px;
    margin-top: 10px;
}

.btn-pay {
    width: 100%;
    padding: 18px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 12px;
    font-size: 18px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-pay:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
}

.btn-pay:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.secure-badge {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-top: 20px;
    color: #718096;
    font-size: 14px;
}

.spinner {
    display: none;
    width: 20px;
    height: 20px;
    border: 3px solid rgba(255,255,255,0.3);
    border-top-color: white;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
    margin-right: 10px;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}
</style>
@endpush

@section('content')
<div class="payment-container">
    <div class="payment-header">
        <div class="payment-amount">{{ number_format($order->total_amount) }}₫</div>
        <p class="text-muted">Đơn hàng #{{ $order->order_number }}</p>
    </div>

    <div class="order-info">
        <div class="info-row">
            <span class="text-muted">Sản phẩm:</span>
            <span class="fw-bold">{{ $order->orderItems->count() }} sản phẩm</span>
        </div>
        <div class="info-row">
            <span class="text-muted">Phương thức:</span>
            <span class="fw-bold">
                <i class="fas fa-credit-card me-2"></i>Thẻ tín dụng/Ghi nợ
            </span>
        </div>
    </div>

    <form id="payment-form">
        <div id="card-element"></div>
        <div id="card-errors" role="alert"></div>

        <button type="submit" id="submit-button" class="btn-pay">
            <span class="spinner"></span>
            <span class="button-text">Thanh toán ngay</span>
        </button>

        <div class="secure-badge">
            <i class="fas fa-shield-alt me-2"></i>
            Thanh toán được bảo mật bởi Stripe
        </div>
    </form>
</div>

@push('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
const stripe = Stripe('{{ $stripePublicKey }}');
const elements = stripe.elements();

// Custom styling
const style = {
    base: {
        fontSize: '16px',
        color: '#32325d',
        fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
        '::placeholder': {
            color: '#aab7c4'
        }
    },
    invalid: {
        color: '#e53e3e',
        iconColor: '#e53e3e'
    }
};

const cardElement = elements.create('card', { style });
cardElement.mount('#card-element');

// Handle real-time validation errors
cardElement.on('change', function(event) {
    const displayError = document.getElementById('card-errors');
    if (event.error) {
        displayError.textContent = event.error.message;
    } else {
        displayError.textContent = '';
    }
});

// Handle form submission
const form = document.getElementById('payment-form');
const submitButton = document.getElementById('submit-button');
const spinner = submitButton.querySelector('.spinner');
const buttonText = submitButton.querySelector('.button-text');

form.addEventListener('submit', async function(event) {
    event.preventDefault();

    // Disable button and show spinner
    submitButton.disabled = true;
    spinner.style.display = 'inline-block';
    buttonText.textContent = 'Đang xử lý...';

    const { error, paymentIntent } = await stripe.confirmCardPayment(
        '{{ $clientSecret }}',
        {
            payment_method: {
                card: cardElement,
                billing_details: {
                    name: '{{ $order->user->name }}'
                }
            }
        }
    );

    if (error) {
        // Show error to customer
        const errorElement = document.getElementById('card-errors');
        errorElement.textContent = error.message;

        // Re-enable button
        submitButton.disabled = false;
        spinner.style.display = 'none';
        buttonText.textContent = 'Thanh toán ngay';
    } else {
        // Payment successful
        if (paymentIntent.status === 'succeeded') {
            window.location.href = '{{ route("client.checkout.success", $order->id) }}';
        }
    }
});
</script>
@endpush
@endsection
