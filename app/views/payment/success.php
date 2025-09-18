<?php
$title = 'Booking Confirmed - GoPlay Sports Platform';
$session_booked = isset($_GET['session_booked']);
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
            --success-color: #059669;
            --text-primary: #1f2937;
            --text-secondary: #6b7280;
            --background-light: #f9fafb;
            --background-white: #ffffff;
            --border-color: #e5e7eb;
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
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .success-container {
            max-width: 600px;
            margin: 2rem;
            background: var(--background-white);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-lg);
            overflow: hidden;
            text-align: center;
        }

        .success-header {
            background: linear-gradient(135deg, var(--success-color), #10b981);
            color: white;
            padding: 3rem 2rem;
        }

        .success-icon {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2.5rem;
        }

        .success-header h1 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .success-header p {
            opacity: 0.9;
            font-size: 1.1rem;
        }

        .success-content {
            padding: 2rem;
        }

        .booking-details {
            background: var(--background-light);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            margin: 1.5rem 0;
            text-align: left;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
            border-bottom: 1px solid var(--border-color);
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-row label {
            color: var(--text-secondary);
            font-weight: 500;
        }

        .detail-row span {
            font-weight: 600;
            color: var(--text-primary);
        }

        .next-steps {
            text-align: left;
            margin: 1.5rem 0;
        }

        .next-steps h3 {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--text-primary);
        }

        .step-list {
            list-style: none;
            padding: 0;
        }

        .step-list li {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            padding: 0.5rem 0;
            color: var(--text-secondary);
        }

        .step-list li i {
            color: var(--success-color);
            margin-top: 0.2rem;
            flex-shrink: 0;
        }

        .action-buttons {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-top: 2rem;
        }

        .btn {
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
            text-decoration: none;
            font-size: 1rem;
        }

        .btn-primary {
            background: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
        }

        .btn-outline {
            background: transparent;
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
        }

        .btn-outline:hover {
            background: var(--primary-color);
            color: white;
        }

        .alert {
            background: #dbeafe;
            color: #1e40af;
            border: 1px solid #93c5fd;
            border-radius: var(--border-radius);
            padding: 1rem;
            margin: 1rem 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        @media (max-width: 480px) {
            .action-buttons {
                grid-template-columns: 1fr;
            }

            .success-header {
                padding: 2rem 1rem;
            }

            .success-content {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-header">
            <div class="success-icon">
                <i class="fas fa-check"></i>
            </div>
            <h1><?= $session_booked ? 'Session Booked!' : 'Payment Successful!' ?></h1>
            <p><?= $session_booked ? 'Your training session has been confirmed' : 'Your payment has been processed successfully' ?></p>
        </div>

        <div class="success-content">
            <?php if ($session_booked): ?>
            <div class="alert">
                <i class="fas fa-info-circle"></i>
                You will receive a confirmation email shortly with all the session details.
            </div>

            <div class="booking-details">
                <div class="detail-row">
                    <label>Booking Reference:</label>
                    <span>#GP<?= date('Ymd') . rand(1000, 9999) ?></span>
                </div>
                <div class="detail-row">
                    <label>Session Status:</label>
                    <span style="color: var(--success-color);">Confirmed</span>
                </div>
                <div class="detail-row">
                    <label>Payment Status:</label>
                    <span style="color: var(--success-color);">Paid</span>
                </div>
                <div class="detail-row">
                    <label>Confirmation Email:</label>
                    <span>Sent to your registered email</span>
                </div>
            </div>

            <div class="next-steps">
                <h3><i class="fas fa-list-check"></i> What's Next?</h3>
                <ul class="step-list">
                    <li>
                        <i class="fas fa-envelope"></i>
                        <span>Check your email for detailed session information and coach contact details</span>
                    </li>
                    <li>
                        <i class="fas fa-calendar-alt"></i>
                        <span>Add the session to your calendar using the link in the confirmation email</span>
                    </li>
                    <li>
                        <i class="fas fa-dumbbell"></i>
                        <span>Prepare any required equipment as mentioned by your coach</span>
                    </li>
                    <li>
                        <i class="fas fa-clock"></i>
                        <span>Arrive 10-15 minutes early for your session</span>
                    </li>
                    <li>
                        <i class="fas fa-phone"></i>
                        <span>Contact your coach directly if you need to reschedule (24hrs notice required)</span>
                    </li>
                </ul>
            </div>

            <div class="action-buttons">
                <a href="/book-coach" class="btn btn-outline">
                    <i class="fas fa-plus"></i>
                    Book Another Session
                </a>
                <a href="/user/sessions" class="btn btn-primary">
                    <i class="fas fa-list"></i>
                    View My Sessions
                </a>
            </div>

            <?php else: ?>

            <div class="alert">
                <i class="fas fa-receipt"></i>
                Your payment receipt has been sent to your email address.
            </div>

            <div class="booking-details">
                <div class="detail-row">
                    <label>Transaction ID:</label>
                    <span>#TXN<?= date('Ymd') . rand(100000, 999999) ?></span>
                </div>
                <div class="detail-row">
                    <label>Payment Method:</label>
                    <span>Credit Card</span>
                </div>
                <div class="detail-row">
                    <label>Amount Paid:</label>
                    <span>₹2,500.00</span>
                </div>
                <div class="detail-row">
                    <label>Payment Date:</label>
                    <span><?= date('F j, Y \a\t g:i A') ?></span>
                </div>
            </div>

            <div class="action-buttons">
                <a href="/shop" class="btn btn-outline">
                    <i class="fas fa-store"></i>
                    Continue Shopping
                </a>
                <a href="/user/orders" class="btn btn-primary">
                    <i class="fas fa-list"></i>
                    View My Orders
                </a>
            </div>

            <?php endif; ?>
        </div>
    </div>

    <script>
        // Auto-redirect after 30 seconds
        setTimeout(() => {
            if (confirm('Would you like to return to the homepage?')) {
                window.location.href = '/';
            }
        }, 30000);

        // Confetti animation for success
        function createConfetti() {
            for (let i = 0; i < 50; i++) {
                const confetti = document.createElement('div');
                confetti.style.cssText = `
                    position: fixed;
                    width: 10px;
                    height: 10px;
                    background: ${['#2563eb', '#059669', '#d97706', '#dc2626'][Math.floor(Math.random() * 4)]};
                    top: -10px;
                    left: ${Math.random() * 100}vw;
                    animation: fall ${2 + Math.random() * 3}s linear forwards;
                    z-index: 1000;
                `;
                document.body.appendChild(confetti);

                setTimeout(() => confetti.remove(), 5000);
            }
        }

        // Add CSS for confetti animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes fall {
                to {
                    transform: translateY(100vh) rotate(360deg);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);

        // Trigger confetti on page load
        setTimeout(createConfetti, 500);
    </script>
</body>
</html>