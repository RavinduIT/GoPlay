<?php
$title = 'Session Payment - GoPlay Sports Platform';
$additionalCSS = [];
$additionalJS = [];

// Get booking data from URL parameters
$coach_id = $_GET['coach_id'] ?? null;
$coach_name = $_GET['coach_name'] ?? 'Selected Coach';
$session_type = $_GET['session_type'] ?? 'Individual';
$date = $_GET['date'] ?? '';
$time = $_GET['time'] ?? '';
$duration = intval($_GET['duration'] ?? 60);
$total_amount = floatval($_GET['total_amount'] ?? 0);
$hourly_rate = floatval($_GET['hourly_rate'] ?? 0);
$special_requests = $_GET['special_requests'] ?? '';

// Calculate totals
$subtotal = $total_amount; // Use the calculated total_amount from booking page
$service_fee = round($subtotal * 0.05, 2); // 5% service fee
$total = $subtotal + $service_fee;

// Format date and time
$formatted_date = $date ? date('F j, Y', strtotime($date)) : '';
$formatted_time = '';
if ($time) {
    $time_parts = explode(':', $time);
    $hour = intval($time_parts[0]);
    $minute = $time_parts[1];
    $ampm = $hour >= 12 ? 'PM' : 'AM';
    $hour12 = $hour % 12 ?: 12;
    $formatted_time = "{$hour12}:{$minute} {$ampm}";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2563eb;
            --primary-dark: #1d4ed8;
            --primary-light: #dbeafe;
            --secondary-color: #0891b2;
            --success-color: #059669;
            --warning-color: #d97706;
            --danger-color: #dc2626;
            --text-primary: #1f2937;
            --text-secondary: #6b7280;
            --text-light: #9ca3af;
            --background-light: #f9fafb;
            --background-white: #ffffff;
            --border-color: #e5e7eb;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            --border-radius: 8px;
            --transition: all 0.2s ease-in-out;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Helvetica Neue', sans-serif;
            line-height: 1.6;
            color: var(--text-primary);
            background: var(--background-light);
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 2rem;
        }

        .header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 2rem;
            border-radius: var(--border-radius);
            text-align: center;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-lg);
        }

        .header h1 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .header p {
            opacity: 0.9;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-secondary);
            text-decoration: none;
            margin-bottom: 2rem;
            transition: var(--transition);
        }

        .back-link:hover {
            color: var(--primary-color);
        }

        .payment-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            align-items: start;
        }

        .card {
            background: var(--background-white);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-lg);
            overflow: hidden;
        }

        .card-header {
            background: var(--primary-color);
            color: white;
            padding: 1.5rem;
            text-align: center;
        }

        .card-header h2 {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .card-content {
            padding: 2rem;
        }

        /* Booking Summary */
        .session-details {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1.5rem;
            background: var(--primary-light);
            border-radius: var(--border-radius);
            margin-bottom: 1.5rem;
        }

        .coach-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: 600;
            flex-shrink: 0;
        }

        .session-info h3 {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .session-meta {
            font-size: 0.9rem;
            color: var(--text-secondary);
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid var(--border-color);
        }

        .summary-row:last-child {
            border-bottom: none;
            font-weight: 600;
            font-size: 1.1rem;
            color: var(--primary-color);
            margin-top: 0.5rem;
            padding-top: 1rem;
            border-top: 2px solid var(--border-color);
        }

        .summary-row label {
            color: var(--text-secondary);
        }

        .summary-row span {
            font-weight: 600;
            color: var(--text-primary);
        }

        /* Payment Form */
        .payment-steps {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .step {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .step-number {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }

        .step-number.inactive {
            background: var(--border-color);
            color: var(--text-light);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        label {
            display: block;
            font-weight: 500;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .required {
            color: var(--danger-color);
        }

        input, select {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid var(--border-color);
            border-radius: var(--border-radius);
            font-size: 1rem;
            transition: var(--transition);
            background: var(--background-white);
        }

        input:focus, select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .input-group {
            position: relative;
        }

        .input-group i {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-light);
        }

        .input-group input {
            padding-left: 2.5rem;
        }

        .btn {
            width: 100%;
            padding: 1rem;
            border: none;
            border-radius: var(--border-radius);
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            font-size: 1rem;
        }

        .btn-primary {
            background: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        .btn-success {
            background: var(--success-color);
            color: white;
        }

        .security-badges {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 1rem;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid var(--border-color);
            color: var(--text-light);
            font-size: 0.8rem;
        }

        .alert {
            padding: 1rem;
            border-radius: var(--border-radius);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #6ee7b7;
        }

        @media (max-width: 768px) {
            .payment-container {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }

            .container {
                padding: 1rem;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .session-details {
                flex-direction: column;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="/app/views/booking/book-session.php" class="back-link">
            <i class="fas fa-arrow-left"></i>
            Back to Booking
        </a>

        <div class="header">
            <h1><i class="fas fa-credit-card"></i> Secure Payment</h1>
            <p>Complete your training session booking</p>
        </div>

        <div class="payment-container">
            <!-- Booking Summary -->
            <div class="card">
                <div class="card-header">
                    <h2><i class="fas fa-receipt"></i> Booking Summary</h2>
                    <p>Review your session details</p>
                </div>
                <div class="card-content">
                    <div class="session-details">
                        <div class="coach-avatar">
                            <?= strtoupper(substr($coach_name, 0, 1)) ?>
                        </div>
                        <div class="session-info">
                            <h3><?= htmlspecialchars($coach_name) ?></h3>
                            <div class="session-meta">
                                <?= ucfirst($session_type) ?> Training Session
                            </div>
                        </div>
                    </div>

                    <div class="summary-row">
                        <label>Session Date:</label>
                        <span><?= htmlspecialchars($formatted_date) ?></span>
                    </div>
                    <div class="summary-row">
                        <label>Session Time:</label>
                        <span><?= htmlspecialchars($formatted_time) ?></span>
                    </div>
                    <div class="summary-row">
                        <label>Duration:</label>
                        <span><?= $duration ?> minutes</span>
                    </div>
                    <div class="summary-row">
                        <label>Session Type:</label>
                        <span><?= ucfirst($session_type) ?></span>
                    </div>
                    <?php if ($special_requests): ?>
                    <div class="summary-row">
                        <label>Special Requests:</label>
                        <span><?= htmlspecialchars($special_requests) ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="summary-row">
                        <label>Session Fee:</label>
                        <span>LKR <?= number_format($subtotal, 2) ?></span>
                    </div>
                    <div class="summary-row">
                        <label>Service Fee:</label>
                        <span>LKR <?= number_format($service_fee, 2) ?></span>
                    </div>
                    <div class="summary-row">
                        <label>Total Amount:</label>
                        <span>LKR <?= number_format($total, 2) ?></span>
                    </div>
                </div>
            </div>

            <!-- Payment Form -->
            <div class="card">
                <div class="card-header">
                    <h2><i class="fas fa-lock"></i> Payment Details</h2>
                    <p>Enter your payment information</p>
                </div>
                <div class="card-content">
                    <div class="payment-steps">
                        <div class="step">
                            <div class="step-number">1</div>
                            <span>Contact Info</span>
                        </div>
                        <i class="fas fa-arrow-right" style="color: var(--text-light);"></i>
                        <div class="step">
                            <div class="step-number inactive">2</div>
                            <span>Payment</span>
                        </div>
                    </div>

                    <form id="paymentForm">
                        <!-- Contact Information -->
                        <div id="contactStep">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="firstName">First Name <span class="required">*</span></label>
                                    <input type="text" id="firstName" required placeholder="John">
                                </div>
                                <div class="form-group">
                                    <label for="lastName">Last Name <span class="required">*</span></label>
                                    <input type="text" id="lastName" required placeholder="Doe">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="email">Email Address <span class="required">*</span></label>
                                    <input type="email" id="email" required placeholder="john@example.com">
                                </div>
                                <div class="form-group">
                                    <label for="phone">Phone Number <span class="required">*</span></label>
                                    <input type="tel" id="phone" required placeholder="+94 77 123 4567">
                                </div>
                            </div>
                            <button type="button" class="btn btn-primary" onclick="proceedToPayment()">
                                Continue to Payment <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>

                        <!-- Payment Information -->
                        <div id="paymentStep" style="display: none;">
                            <div class="form-group">
                                <label for="cardNumber">Card Number <span class="required">*</span></label>
                                <div class="input-group">
                                    <i class="fas fa-credit-card"></i>
                                    <input type="text" id="cardNumber" required placeholder="1234 5678 9012 3456" maxlength="19">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="expiryDate">Expiry Date <span class="required">*</span></label>
                                    <input type="text" id="expiryDate" required placeholder="MM/YY" maxlength="5">
                                </div>
                                <div class="form-group">
                                    <label for="cvv">CVV <span class="required">*</span></label>
                                    <input type="text" id="cvv" required placeholder="123" maxlength="4">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="cardName">Name on Card <span class="required">*</span></label>
                                <input type="text" id="cardName" required placeholder="John Doe">
                            </div>
                            <button type="button" class="btn btn-success" onclick="processPayment()">
                                <i class="fas fa-lock"></i>
                                Pay LKR <?= number_format($total, 2) ?>
                            </button>
                        </div>
                    </form>

                    <div class="security-badges">
                        <span><i class="fas fa-shield-alt"></i> Secure Payment</span>
                        <span><i class="fas fa-lock"></i> SSL Encrypted</span>
                        <span><i class="fab fa-cc-visa"></i> <i class="fab fa-cc-mastercard"></i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function proceedToPayment() {
            // Validate contact information
            const firstName = document.getElementById('firstName').value.trim();
            const lastName = document.getElementById('lastName').value.trim();
            const email = document.getElementById('email').value.trim();
            const phone = document.getElementById('phone').value.trim();

            if (!firstName || !lastName || !email || !phone) {
                alert('Please fill in all required fields');
                return;
            }

            // Email validation
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                alert('Please enter a valid email address');
                return;
            }

            // Hide contact step and show payment step
            document.getElementById('contactStep').style.display = 'none';
            document.getElementById('paymentStep').style.display = 'block';

            // Update step indicators
            document.querySelector('.payment-steps .step:first-child .step-number').classList.add('inactive');
            document.querySelector('.payment-steps .step:last-child .step-number').classList.remove('inactive');

            // Auto-fill card name
            document.getElementById('cardName').value = `${firstName} ${lastName}`;
        }

        function processPayment() {
            // Validate payment information
            const cardNumber = document.getElementById('cardNumber').value.replace(/\s/g, '');
            const expiryDate = document.getElementById('expiryDate').value;
            const cvv = document.getElementById('cvv').value;
            const cardName = document.getElementById('cardName').value.trim();

            if (!cardNumber || !expiryDate || !cvv || !cardName) {
                alert('Please fill in all payment fields');
                return;
            }

            // Basic card number validation (length check)
            if (cardNumber.length < 13 || cardNumber.length > 19) {
                alert('Please enter a valid card number');
                return;
            }

            // CVV validation
            if (cvv.length < 3 || cvv.length > 4) {
                alert('Please enter a valid CVV');
                return;
            }

            // Show loading state
            const payButton = document.querySelector('#paymentStep button');
            const originalText = payButton.innerHTML;
            payButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            payButton.disabled = true;

            // Simulate payment processing
            setTimeout(() => {
                // Show success and redirect
                alert('Payment successful! Your session has been booked.');
                window.location.href = '/app/views/payment/success.php?session_booked=1';
            }, 2000);
        }

        // Format card number input
        document.getElementById('cardNumber').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\s/g, '');
            let formattedValue = value.replace(/(.{4})/g, '$1 ').trim();
            if (formattedValue !== e.target.value) {
                e.target.value = formattedValue;
            }
        });

        // Format expiry date input
        document.getElementById('expiryDate').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length >= 2) {
                value = value.substring(0, 2) + '/' + value.substring(2, 4);
            }
            e.target.value = value;
        });

        // Only allow numbers in CVV
        document.getElementById('cvv').addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/\D/g, '');
        });
    </script>
</body>
</html>