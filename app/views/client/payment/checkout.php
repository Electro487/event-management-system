<?php
// Prevent direct access simulation
if (!isset($booking)) {
    header('Location: /EventManagementSystem/public/client/events');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Checkout - e-Plan</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/checkout.css?v=<?php echo time(); ?>">
</head>
<body>

    <div class="checkout-wrapper">
        <!-- Order Summary -->
        <div class="order-summary">
            <h2>Order Summary</h2>
            <div class="summary-amount">Rs. <?php echo number_format($booking['total_amount'], 2); ?></div>
            
            <div class="summary-item">
                <span class="label">Event</span>
                <span class="value"><?php echo htmlspecialchars($booking['event_title']); ?></span>
            </div>
            <div class="summary-item">
                <span class="label">Package</span>
                <span class="value"><?php echo ucfirst(htmlspecialchars($booking['package_tier'])); ?> Package</span>
            </div>
            <div class="summary-item">
                <span class="label">Date</span>
                <span class="value"><?php echo date('M d, Y', strtotime($booking['event_date'])); ?></span>
            </div>
            <div class="summary-item">
                <span class="label">Guests</span>
                <span class="value"><?php echo htmlspecialchars($booking['guest_count']); ?></span>
            </div>
            <div class="summary-item">
                <span class="label">Client Name</span>
                <span class="value"><?php echo htmlspecialchars($booking['full_name']); ?></span>
            </div>

            <div class="divider"></div>

            <div class="summary-item" style="font-weight: 600; color: #1a202c;">
                <span class="label">Total Due</span>
                <span class="value">Rs. <?php echo number_format($booking['total_amount'], 2); ?></span>
            </div>
        </div>

        <!-- Payment Form -->
        <div class="payment-section">
            <h1>Payment Details</h1>
            <p class="subtitle">Complete your booking by providing your payment details below.</p>

            <form id="payment-form" action="/EventManagementSystem/public/client/payment/process" method="POST">
                
                <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">

                <div class="form-group">
                    <label>Cardholder Name</label>
                    <div class="input-container" id="name-container">
                        <i class="fa-regular fa-user"></i>
                        <input type="text" name="card_name" id="cardName" class="stripe-input" placeholder="e.g. Jane Doe" required value="<?php echo htmlspecialchars($booking['full_name']); ?>">
                    </div>
                    <div class="error-msg" id="name-error">Please enter the cardholder name.</div>
                </div>

                <div class="form-group">
                    <label>Card Number</label>
                    <div class="input-container" id="card-container">
                        <i class="fa-regular fa-credit-card"></i>
                        <input type="text" name="card_number" id="cardNumber" class="stripe-input" placeholder="0000 0000 0000 0000" maxlength="19" required>
                    </div>
                    <div class="error-msg" id="card-error">Please enter a valid 16-digit card number.</div>
                </div>

                <div class="row">
                    <div class="col form-group">
                        <label>Expiry Date</label>
                        <div class="input-container" id="exp-container">
                            <i class="fa-regular fa-calendar"></i>
                            <input type="text" id="cardExp" class="stripe-input" placeholder="MM/YY" maxlength="5" required>
                        </div>
                        <div class="error-msg" id="exp-error">Invalid expiry.</div>
                    </div>
                    <div class="col form-group">
                        <label>CVC</label>
                        <div class="input-container" id="cvc-container">
                            <i class="fa-solid fa-lock"></i>
                            <input type="text" id="cardCvc" class="stripe-input" placeholder="123" maxlength="4" required>
                        </div>
                        <div class="error-msg" id="cvc-error">Invalid CVC.</div>
                    </div>
                </div>

                <button type="submit" class="btn-pay" id="payButton">
                    <span class="btn-text">Pay Rs. <?php echo number_format($booking['total_amount'], 2); ?></span>
                    <div class="spinner"></div>
                </button>
                
                <div style="text-align: center; margin-top: 20px; font-size: 12px; color: #a0aec0; display: flex; justify-content: center; align-items: center; gap: 5px;">
                    <i class="fa-solid fa-lock" style="font-size: 10px;"></i> Payments are secure and encrypted.
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('payment-form');
            const cardInput = document.getElementById('cardNumber');
            const expInput = document.getElementById('cardExp');
            const cvcInput = document.getElementById('cardCvc');
            const payBtn = document.getElementById('payButton');

            // Format Card Number (groups of 4)
            cardInput.addEventListener('input', function(e) {
                let val = this.value.replace(/\D/g, ''); // Remove non-digits
                let formatted = val.match(/.{1,4}/g)?.join(' ') || val;
                this.value = formatted;
                
                if (val.length === 16) {
                    document.getElementById('card-container').classList.remove('invalid');
                    document.getElementById('card-error').style.display = 'none';
                    this.classList.remove('invalid');
                }
            });

            // Format Expiry (MM/YY)
            expInput.addEventListener('input', function(e) {
                let val = this.value.replace(/\D/g, '');
                if (val.length > 2) {
                    val = val.substring(0, 2) + '/' + val.substring(2, 4);
                }
                this.value = val;

                if (val.length === 5) {
                    document.getElementById('exp-container').classList.remove('invalid');
                    document.getElementById('exp-error').style.display = 'none';
                }
            });

            // Format CVC
            cvcInput.addEventListener('input', function(e) {
                this.value = this.value.replace(/\D/g, '');
                if (this.value.length >= 3) {
                    document.getElementById('cvc-container').classList.remove('invalid');
                    document.getElementById('cvc-error').style.display = 'none';
                }
            });

            form.addEventListener('submit', function(e) {
                e.preventDefault();
                let isValid = true;

                // Validate Name
                const name = document.getElementById('cardName').value.trim();
                if (!name) {
                    document.getElementById('name-container').classList.add('invalid');
                    document.getElementById('name-error').style.display = 'block';
                    isValid = false;
                }

                // Validate Card (must be 16 digits)
                const cardRaw = cardInput.value.replace(/\D/g, '');
                if (cardRaw.length !== 16) {
                    document.getElementById('card-container').classList.add('invalid');
                    document.getElementById('card-error').style.display = 'block';
                    cardInput.classList.add('invalid');
                    isValid = false;
                }

                // Validate Expiry
                if (expInput.value.length !== 5) {
                    document.getElementById('exp-container').classList.add('invalid');
                    document.getElementById('exp-error').style.display = 'block';
                    isValid = false;
                }

                // Validate CVC
                if (cvcInput.value.length < 3) {
                    document.getElementById('cvc-container').classList.add('invalid');
                    document.getElementById('cvc-error').style.display = 'block';
                    isValid = false;
                }

                if (isValid) {
                    // Start Loading UI
                    payBtn.classList.add('loading');
                    payBtn.disabled = true;
                    
                    // Simulate network delay to make it feel real before letting the form naturally submit
                    setTimeout(() => {
                        form.submit();
                    }, 2000);
                }
            });
        });
    </script>
</body>
</html>
