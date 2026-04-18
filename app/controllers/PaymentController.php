<?php

namespace App\Controllers;

use Core\Request;
use Core\Response;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Cart;
use App\Models\ShopOwnerBalance;

/**
 * Payment Controller
 * 
 * Handles complete checkout and payment flow
 */
class PaymentController extends BaseController
{
    private Order $orderModel;
    private Payment $paymentModel;
    private Cart $cartModel;

    public function __construct()
    {
        $this->orderModel = new Order();
        $this->paymentModel = new Payment();
        $this->cartModel = new Cart();
    }

    /**
     * Display contact details page (Step 1)
     */
    public function contactDetails(Request $request): Response
    {
<<<<<<< HEAD
=======
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            // Store return URL in session to redirect back after login
            // Redirect to cart checkout so user can review items before contact details
            $_SESSION['return_after_login'] = '/cart/checkout';
            
            // Redirect to login page
            return $this->redirect('/login');
        }

>>>>>>> 18941f66cb081928b3385b630a63cc878d80563e
        // Ensure user has items in cart
        $session = $this->getCartSession();
        $cart = $this->cartModel->getOrCreateCart($session['user_id'], $session['session_id']);
        $cartDetails = $this->cartModel->getCartDetails($cart['id']);

        if (empty($cartDetails['items'])) {
            return $this->redirect('/shop');
        }

        // Load previously saved contact details from session if available
<<<<<<< HEAD
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
=======
>>>>>>> 18941f66cb081928b3385b630a63cc878d80563e
        $savedContact = $_SESSION['checkout_contact'] ?? [];

        return $this->view('checkout/contact-details', ['savedContact' => $savedContact]);
    }

    /**
     * Save contact details to session
     */
    public function saveContactDetails(Request $request): Response
    {
        try {
            $data = $request->getJsonBody();

            // Validate required fields
            $required = ['fullName', 'email', 'phone', 'address', 'city', 'postalCode'];
            foreach ($required as $field) {
                if (empty($data[$field])) {
                    return $this->json([
                        'success' => false,
                        'message' => ucfirst($field) . ' is required'
                    ], 400);
                }
            }

            // Validate email
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                return $this->json([
                    'success' => false,
                    'message' => 'Invalid email address'
                ], 400);
            }

            // Save to session
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            $_SESSION['checkout_contact'] = [
                'fullName' => $data['fullName'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'address' => $data['address'],
                'city' => $data['city'],
                'postalCode' => $data['postalCode'],
                'province' => $data['province'] ?? 'Western',
                'notes' => $data['notes'] ?? ''
            ];

            return $this->json([
                'success' => true,
                'message' => 'Contact details saved'
            ]);

        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to save contact details: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display payment method selection page (Step 2)
     */
    public function paymentMethod(Request $request): Response
    {
<<<<<<< HEAD
        // Ensure contact details are saved
=======
>>>>>>> 18941f66cb081928b3385b630a63cc878d80563e
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

<<<<<<< HEAD
=======
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            // Redirect to cart checkout so user can review items and redo contact details if needed
            $_SESSION['return_after_login'] = '/cart/checkout';
            return $this->redirect('/login');
        }

        // Ensure contact details are saved
>>>>>>> 18941f66cb081928b3385b630a63cc878d80563e
        if (!isset($_SESSION['checkout_contact'])) {
            return $this->redirect('/checkout/contact-details');
        }

        // Ensure cart has items
        $session = $this->getCartSession();
        $cart = $this->cartModel->getOrCreateCart($session['user_id'], $session['session_id']);
        $cartDetails = $this->cartModel->getCartDetails($cart['id']);

        if (empty($cartDetails['items'])) {
            return $this->redirect('/shop');
        }

        return $this->view('checkout/payment-method');
    }

    /**
     * Display card payment processing page
     */
    public function paymentProcessing(Request $request): Response
    {
<<<<<<< HEAD
        // Ensure contact details are saved
=======
        // Ensure user is logged in
>>>>>>> 18941f66cb081928b3385b630a63cc878d80563e
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

<<<<<<< HEAD
=======
        if (!isset($_SESSION['user_id'])) {
            // Redirect to cart checkout so user can review items if needed
            $_SESSION['return_after_login'] = '/cart/checkout';
            return $this->redirect('/login');
        }

        // Ensure contact details are saved
>>>>>>> 18941f66cb081928b3385b630a63cc878d80563e
        if (!isset($_SESSION['checkout_contact'])) {
            return $this->redirect('/checkout/contact-details');
        }

        return $this->view('checkout/payment-processing');
    }

    /**
     * Process Cash on Delivery order
     */
    public function processCashOnDelivery(Request $request): Response
    {
        try {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            // Validate session data
            if (!isset($_SESSION['checkout_contact'])) {
                return $this->json([
                    'success' => false,
                    'message' => 'Contact details not found'
                ], 400);
            }

            // Get cart details
            $session = $this->getCartSession();
            $cart = $this->cartModel->getOrCreateCart($session['user_id'], $session['session_id']);
            $cartDetails = $this->cartModel->getCartDetails($cart['id']);

            if (empty($cartDetails['items'])) {
                return $this->json([
                    'success' => false,
                    'message' => 'Cart is empty'
                ], 400);
            }

            // Calculate totals
            $totals = $this->orderModel->calculateOrderTotals($cartDetails['items']);

            // Prepare order data
            $contactDetails = $_SESSION['checkout_contact'];
            $orderData = [
                'user_id' => $session['user_id'] ?? null,
                'order_type' => 'product',
                'subtotal' => $totals['subtotal'],
                'tax_amount' => $totals['tax_amount'],
                'shipping_amount' => $totals['shipping_amount'],
                'discount_amount' => $totals['discount_amount'],
                'total_amount' => $totals['total_amount'],
                'currency' => 'LKR',
                'status' => 'pending',
                'payment_status' => 'pending',
                'payment_method' => 'cash',
                'shipping_address' => json_encode([
                    'name' => $contactDetails['fullName'],
                    'phone' => $contactDetails['phone'],
                    'address' => $contactDetails['address'],
                    'city' => $contactDetails['city'],
                    'postal_code' => $contactDetails['postalCode'],
                    'province' => $contactDetails['province']
                ]),
                'billing_address' => json_encode([
                    'name' => $contactDetails['fullName'],
                    'email' => $contactDetails['email'],
                    'phone' => $contactDetails['phone']
                ]),
                'notes' => $contactDetails['notes']
            ];

            // Create order
            $orderId = $this->orderModel->createOrder($orderData, $cartDetails['items']);

            if (!$orderId) {
                return $this->json([
                    'success' => false,
                    'message' => 'Failed to create order'
                ], 500);
            }

            // Get order details
            $order = $this->orderModel->find($orderId);

            // Create payment record
            $this->paymentModel->createPayment([
                'order_id' => $orderId,
                'payment_method' => 'cash',
                'amount' => $totals['total_amount'],
                'currency' => 'LKR',
                'status' => 'pending'
            ]);

<<<<<<< HEAD
            // Credit shop owners for COD orders
            $this->creditShopOwnersForOrder($order);
=======
            // Hold COD earnings in pending until delivery is confirmed
            $this->holdCODEarningsForOrder($order);
>>>>>>> 18941f66cb081928b3385b630a63cc878d80563e
            $this->sendOrderNotifications($order);

            // Clear cart
            $this->cartModel->clearCart($cart['id']);

            // Clear session data
            unset($_SESSION['checkout_contact']);

            return $this->json([
                'success' => true,
                'message' => 'Order placed successfully',
                'orderNumber' => $order['order_number'],
                'orderId' => $orderId
            ]);

        } catch (\Exception $e) {
            error_log('COD Order Error: ' . $e->getMessage());
            return $this->json([
                'success' => false,
                'message' => 'Failed to process order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Initialize PayHere Payment
     * Creates order and returns PayHere payment data
     */
    public function initializePayHerePayment(Request $request): Response
    {
        try {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            // Validate session data
            if (!isset($_SESSION['checkout_contact'])) {
                return $this->json([
                    'success' => false,
                    'message' => 'Contact details not found'
                ], 400);
            }

            // Get cart details
            $session = $this->getCartSession();
            $cart = $this->cartModel->getOrCreateCart($session['user_id'], $session['session_id']);
            $cartDetails = $this->cartModel->getCartDetails($cart['id']);

            if (empty($cartDetails['items'])) {
                return $this->json([
                    'success' => false,
                    'message' => 'Cart is empty'
                ], 400);
            }

            // Calculate totals
            $totals = $this->orderModel->calculateOrderTotals($cartDetails['items']);
            $contactDetails = $_SESSION['checkout_contact'];

            // Create order with pending payment status
            $orderData = [
                'user_id' => $session['user_id'] ?? null,
                'order_type' => 'product',
                'subtotal' => $totals['subtotal'],
                'tax_amount' => $totals['tax_amount'],
                'shipping_amount' => $totals['shipping_amount'],
                'discount_amount' => $totals['discount_amount'],
                'total_amount' => $totals['total_amount'],
                'currency' => 'LKR',
                'status' => 'pending',
                'payment_status' => 'pending',
                'payment_method' => 'card',
                'shipping_address' => json_encode([
                    'name' => $contactDetails['fullName'],
                    'phone' => $contactDetails['phone'],
                    'address' => $contactDetails['address'],
                    'city' => $contactDetails['city'],
                    'postal_code' => $contactDetails['postalCode'],
                    'province' => $contactDetails['province']
                ]),
                'billing_address' => json_encode([
                    'name' => $contactDetails['fullName'],
                    'email' => $contactDetails['email'],
                    'phone' => $contactDetails['phone']
                ]),
                'notes' => $contactDetails['notes']
            ];

            // Create order
            $orderId = $this->orderModel->createOrder($orderData, $cartDetails['items']);

            if (!$orderId) {
                return $this->json([
                    'success' => false,
                    'message' => 'Failed to create order'
                ], 500);
            }

            // Get order details
            $order = $this->orderModel->find($orderId);

            // Create pending payment record
            $this->paymentModel->createPayment([
                'order_id' => $orderId,
                'payment_method' => 'credit_card',
                'amount' => $totals['total_amount'],
                'currency' => 'LKR',
                'status' => 'pending'
            ]);

            // Store payment session token for security verification
            $_SESSION['payhere_session_' . $order['order_number']] = [
                'token' => bin2hex(random_bytes(16)),
                'timestamp' => time(),
                'order_id' => $order['order_number']
            ];

            // Load PayHere config
            $payhereConfig = require __DIR__ . '/../../config/payhere.php';
            
            // Prepare PayHere payment data
            $merchantId = $payhereConfig['merchant_id'];
            $merchantSecret = $payhereConfig['merchant_secret'];
            $orderId = $order['order_number'];
            $amount = number_format($totals['total_amount'], 2, '.', '');
            $currency = $payhereConfig['currency'];
            
            // Generate hash
            $hash = $this->generatePayHereHash(
                $merchantId,
                $orderId,
                $amount,
                $currency,
                $merchantSecret
            );

            // Prepare payment data for frontend
            // Detect protocol (http or https)
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443 ? 'https' : 'http';
            $baseUrl = $protocol . '://' . $_SERVER['HTTP_HOST'];
            
            $paymentData = [
                'merchant_id' => $merchantId,
                'return_url' => $baseUrl . $payhereConfig['return_url'],
                'cancel_url' => $baseUrl . $payhereConfig['cancel_url'],
                'notify_url' => $baseUrl . $payhereConfig['notify_url'],
                'order_id' => $orderId,
                'items' => 'GoPlay Products Order',
                'currency' => $currency,
                'amount' => $amount,
                'first_name' => explode(' ', $contactDetails['fullName'])[0],
                'last_name' => explode(' ', $contactDetails['fullName'], 2)[1] ?? '',
                'email' => $contactDetails['email'],
                'phone' => $contactDetails['phone'],
                'address' => $contactDetails['address'],
                'city' => $contactDetails['city'],
                'country' => 'Sri Lanka',
                'hash' => $hash,
                'sandbox' => $payhereConfig['mode'] === 'sandbox'
            ];

            return $this->json([
                'success' => true,
                'paymentData' => $paymentData
            ]);

        } catch (\Exception $e) {
            error_log('PayHere Initialization Error: ' . $e->getMessage());
            return $this->json([
                'success' => false,
                'message' => 'Failed to initialize payment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * PayHere Payment Notification Handler
     * Called by PayHere when payment is completed
     */
    public function payHereNotify(Request $request): Response
    {
        try {
            // Get POST data from PayHere
            $merchantId = $_POST['merchant_id'] ?? '';
            $orderId = $_POST['order_id'] ?? '';
            $paymentId = $_POST['payment_id'] ?? '';
            $payhereAmount = $_POST['payhere_amount'] ?? '';
            $payhereCurrency = $_POST['payhere_currency'] ?? '';
            $statusCode = $_POST['status_code'] ?? '';
            $md5sig = $_POST['md5sig'] ?? '';
            $method = $_POST['method'] ?? '';
            $statusMessage = $_POST['status_message'] ?? '';
            $cardHolderName = $_POST['card_holder_name'] ?? '';
            $cardNo = $_POST['card_no'] ?? '';

            // Log notification
            error_log('PayHere Notification: ' . json_encode($_POST));

            // Load config
            $payhereConfig = require __DIR__ . '/../../config/payhere.php';
            $merchantSecret = $payhereConfig['merchant_secret'];

            // Verify hash
            $localMd5sig = strtoupper(
                md5(
                    $merchantId . 
                    $orderId . 
                    $payhereAmount . 
                    $payhereCurrency . 
                    $statusCode . 
                    strtoupper(md5($merchantSecret))
                )
            );

            if ($localMd5sig !== $md5sig) {
                error_log('PayHere hash verification failed');
                return $this->json(['error' => 'Invalid hash'], 400);
            }

            // Find order
            $order = $this->orderModel->findByOrderNumber($orderId);
            if (!$order) {
                error_log('Order not found: ' . $orderId);
                return $this->json(['error' => 'Order not found'], 404);
            }

            // Update order and payment based on status
            if ($statusCode == '2') {
                // Payment successful
                $this->orderModel->update($order['id'], [
                    'payment_status' => 'paid',
                    'status' => 'processing'
                ]);

                // Update payment record
                $payment = $this->paymentModel->findByOrderId($order['id']);
                if ($payment) {
                    $this->paymentModel->update($payment['id'], [
                        'transaction_id' => $paymentId,
                        'status' => 'completed',
                        'gateway_response' => json_encode($_POST),
                        'processed_at' => date('Y-m-d H:i:s')
                    ]);
                }

                // Clear cart if user session exists
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                if (!empty($order['user_id'])) {
                    $userCarts = $this->cartModel->getUserCarts($order['user_id']);
                    foreach ($userCarts as $cart) {
                        $this->cartModel->clearCart($cart['id']);
                    }
                }

                // Credit shop owners and send notifications
                $this->creditShopOwnersForOrder($order);
                $this->sendOrderNotifications($order);

                error_log('Payment successful for order: ' . $orderId);
            } else {
                // Payment failed or cancelled
                $this->orderModel->update($order['id'], [
                    'payment_status' => 'failed',
                    'status' => 'cancelled'
                ]);

                $payment = $this->paymentModel->findByOrderId($order['id']);
                if ($payment) {
                    $this->paymentModel->update($payment['id'], [
                        'status' => 'failed',
                        'gateway_response' => json_encode($_POST)
                    ]);
                }

                error_log('Payment failed for order: ' . $orderId);
            }

            return $this->json(['status' => 'received']);

        } catch (\Exception $e) {
            error_log('PayHere Notify Error: ' . $e->getMessage());
            return $this->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * PayHere Return URL Handler
     * Called when customer returns from PayHere
     */
    public function payHereReturn(Request $request): Response
    {
        try {
            $orderId = $_GET['order_id'] ?? null;
            $paymentId = $_GET['payment_id'] ?? null;
            $statusCode = $_GET['status_code'] ?? null;
            $md5sig = $_GET['md5sig'] ?? null;
            $payhereAmount = $_GET['payhere_amount'] ?? null;
            $payhereCurrency = $_GET['payhere_currency'] ?? null;
            
            if (!$orderId) {
                return $this->redirect('/checkout/payment-method?error=missing_order');
            }

            // Find order
            $order = $this->orderModel->findByOrderNumber($orderId);
            
            if (!$order) {
                return $this->redirect('/checkout/payment-method?error=order_not_found');
            }

            // Load PayHere config
            $payhereConfig = require __DIR__ . '/../../config/payhere.php';
            $merchantSecret = $payhereConfig['merchant_secret'];
            $merchantId = $payhereConfig['merchant_id'];
            $isSandbox = $payhereConfig['mode'] === 'sandbox';

            // If payment details are provided and payment is not already marked as paid
            if ($statusCode && $md5sig && $order['payment_status'] !== 'paid') {
                // Verify hash
                $localMd5sig = strtoupper(
                    md5(
                        $merchantId . 
                        $orderId . 
                        $payhereAmount . 
                        $payhereCurrency . 
                        $statusCode . 
                        strtoupper(md5($merchantSecret))
                    )
                );

                if ($localMd5sig === $md5sig && $statusCode == '2') {
                    // Payment successful - update order
                    $this->orderModel->update($order['id'], [
                        'payment_status' => 'paid',
                        'status' => 'processing'
                    ]);

                    // Update payment record
                    $payment = $this->paymentModel->findByOrderId($order['id']);
                    if ($payment) {
                        $this->paymentModel->update($payment['id'], [
                            'transaction_id' => $paymentId ?? 'SANDBOX-' . time(),
                            'status' => 'completed',
                            'gateway_response' => json_encode($_GET),
                            'processed_at' => date('Y-m-d H:i:s')
                        ]);
                    }

                    // Clear cart
                    if (session_status() === PHP_SESSION_NONE) {
                        session_start();
                    }
                    if (!empty($order['user_id'])) {
                        $userCarts = $this->cartModel->getUserCarts($order['user_id']);
                        foreach ($userCarts as $cart) {
                            $this->cartModel->clearCart($cart['id']);
                        }
                    }

                    // Clear session checkout data
                    unset($_SESSION['checkout_contact']);

                    error_log('Payment completed via return URL for order: ' . $orderId);

                    // Redirect to success page
                    return $this->redirect('/checkout/order-success?order=' . $orderId);
                } else if ($statusCode != '2') {
                    // Payment failed
                    $this->orderModel->update($order['id'], [
                        'payment_status' => 'failed',
                        'status' => 'cancelled'
                    ]);

                    $payment = $this->paymentModel->findByOrderId($order['id']);
                    if ($payment) {
                        $this->paymentModel->update($payment['id'], [
                            'status' => 'failed',
                            'gateway_response' => json_encode($_GET)
                        ]);
                    }

                    return $this->redirect('/checkout/payment-method?error=payment_failed&order=' . $orderId);
                } else {
                    // Hash verification failed
                    error_log('PayHere hash verification failed for order: ' . $orderId);
                    return $this->redirect('/checkout/payment-method?error=verification_failed&order=' . $orderId);
                }
            }

            // For sandbox/local testing: if user returned but no verification params provided
            // and payment is still pending, verify session token before auto-completing
            if ($isSandbox && $order['payment_status'] === 'pending') {
                // Verify session token for security
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                
                $sessionKey = 'payhere_session_' . $orderId;
                $sessionData = $_SESSION[$sessionKey] ?? null;
                
                // Check if session token exists and is valid (within 30 minutes)
                if (!$sessionData || !isset($sessionData['token']) || (time() - $sessionData['timestamp']) > 1800) {
                    error_log('Invalid or expired payment session for order: ' . $orderId);
                    return $this->redirect('/checkout/payment-method?error=invalid_session&order=' . $orderId);
                }
                
                // Clear the session token (one-time use)
                unset($_SESSION[$sessionKey]);
                
                error_log('Sandbox mode: auto-completing payment for order: ' . $orderId);
                
                // Mark payment as successful
                $this->orderModel->update($order['id'], [
                    'payment_status' => 'paid',
                    'status' => 'processing'
                ]);

                // Update payment record
                $payment = $this->paymentModel->findByOrderId($order['id']);
                if ($payment) {
                    $this->paymentModel->update($payment['id'], [
                        'transaction_id' => 'SANDBOX-' . time() . '-' . mt_rand(1000, 9999),
                        'status' => 'completed',
                        'gateway_response' => json_encode(['mode' => 'sandbox_auto_complete', 'order_id' => $orderId]),
                        'processed_at' => date('Y-m-d H:i:s')
                    ]);
                }

                // Clear cart
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                if (!empty($order['user_id'])) {
                    $userCarts = $this->cartModel->getUserCarts($order['user_id']);
                    foreach ($userCarts as $cart) {
                        $this->cartModel->clearCart($cart['id']);
                    }
                }

                // Clear session checkout data
                unset($_SESSION['checkout_contact']);

                // Credit shop owners and send notifications
                $this->creditShopOwnersForOrder($order);
                $this->sendOrderNotifications($order);

                // Redirect to success page
                return $this->redirect('/checkout/order-success?order=' . $orderId);
            }

            // Check payment status (if already updated by notify callback)
            if ($order['payment_status'] === 'paid') {
                // Clear session checkout data
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                unset($_SESSION['checkout_contact']);

                // Redirect to success page
                return $this->redirect('/checkout/order-success?order=' . $orderId);
            } else {
                // Payment not confirmed yet or failed
                error_log('Payment status check failed for order: ' . $orderId . ' - Status: ' . $order['payment_status']);
                return $this->redirect('/checkout/payment-method?error=payment_pending&order=' . $orderId);
            }

        } catch (\Exception $e) {
            error_log('PayHere Return Error: ' . $e->getMessage());
            return $this->redirect('/checkout/payment-method?error=system_error');
        }
    }

    /**
     * Generate PayHere payment hash
     */
    private function generatePayHereHash(string $merchantId, string $orderId, string $amount, string $currency, string $merchantSecret): string
    {
        $hashedSecret = strtoupper(md5($merchantSecret));
        $amountFormated = number_format($amount, 2, '.', '');
        $hash = strtoupper(
            md5(
                $merchantId . 
                $orderId . 
                $amountFormated . 
                $currency . 
                $hashedSecret
            )
        );
        return $hash;
    }

    /**
     * Process Card Payment (Legacy - kept for backward compatibility)
     */
    public function processCardPayment(Request $request): Response
    {
        // Redirect to PayHere initialization
        return $this->initializePayHerePayment($request);
    }

    /**
     * Display order success page
     */
    public function orderSuccess(Request $request): Response
    {
        return $this->view('checkout/order-success');
    }

    /**
     * Payment webhook handler (for payment gateway callbacks)
     */
    public function paymentWebhook(Request $request): Response
    {
        try {
            $payload = $request->getBody();
            $signature = $request->getHeader('X-Webhook-Signature');

            // Verify webhook signature
            // $secret = getenv('PAYMENT_WEBHOOK_SECRET');
            // if (!$this->paymentModel->verifyWebhookSignature($payload, $signature, $secret)) {
            //     return $this->json(['error' => 'Invalid signature'], 401);
            // }

            $data = json_decode($payload, true);

            // Handle different webhook events
            if (isset($data['event_type'])) {
                switch ($data['event_type']) {
                    case 'payment.completed':
                        $this->handlePaymentCompleted($data);
                        break;
                    case 'payment.failed':
                        $this->handlePaymentFailed($data);
                        break;
                }
            }

            return $this->json(['status' => 'received']);

        } catch (\Exception $e) {
            error_log('Webhook Error: ' . $e->getMessage());
            return $this->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get cart session info
     */
    private function getCartSession(): array
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        return [
            'user_id' => $_SESSION['user_id'] ?? null,
            'session_id' => session_id()
        ];
    }

    /**
     * Handle payment completed webhook
     */
    private function handlePaymentCompleted(array $data): void
    {
        // Update order and payment status
        // Implementation depends on your payment gateway
    }

    /**
     * Handle payment failed webhook
     */
    private function handlePaymentFailed(array $data): void
    {
        // Update payment status and notify user
        // Implementation depends on your payment gateway
    }

    /**
<<<<<<< HEAD
     * Credit shop owners for a completed order.
     * Calculates each shop owner's share from their items,
     * deducts platform fee, and credits their balance.
=======
     * Credit shop owners for a CONFIRMED (online-paid) order.
     * Releases directly to available_balance — payment already received.
>>>>>>> 18941f66cb081928b3385b630a63cc878d80563e
     */
    private function creditShopOwnersForOrder(array $order): void
    {
        try {
            $orderId = $order['id'];
            $balanceModel = new ShopOwnerBalance();
            $feePercentage = $balanceModel->getServiceFeePercentage();
<<<<<<< HEAD
            
            $shopOwnerShares = $balanceModel->getShopOwnerSharesByOrder($orderId);
            
            foreach ($shopOwnerShares as $share) {
                $shopOwnerId = (int)$share['shop_owner_id'];
                $grossAmount = (float)$share['owner_total'];
                $feeAmount = round($grossAmount * ($feePercentage / 100), 2);
                
                $balanceModel->creditSale($shopOwnerId, $orderId, $grossAmount, $feeAmount);
                
                error_log("Credited shop owner {$shopOwnerId}: gross={$grossAmount}, fee={$feeAmount}, net=" . ($grossAmount - $feeAmount));
            }
            
=======

            $shopOwnerShares = $balanceModel->getShopOwnerSharesByOrder($orderId);

            foreach ($shopOwnerShares as $share) {
                $shopOwnerId = (int)$share['shop_owner_id'];
                $grossAmount = (float)$share['owner_total'];
                $feeAmount   = round($grossAmount * ($feePercentage / 100), 2);

                $balanceModel->creditSale($shopOwnerId, $orderId, $grossAmount, $feeAmount);

                error_log("Online credit — shop owner {$shopOwnerId}: gross={$grossAmount}, fee={$feeAmount}, net=" . ($grossAmount - $feeAmount));
            }

>>>>>>> 18941f66cb081928b3385b630a63cc878d80563e
        } catch (\Exception $e) {
            error_log('Failed to credit shop owners for order ' . ($order['id'] ?? 'unknown') . ': ' . $e->getMessage());
        }
    }

    /**
<<<<<<< HEAD
=======
     * Hold COD earnings in pending_balance until delivery is confirmed.
     * Cash has NOT been collected — do not release to available_balance yet.
     */
    private function holdCODEarningsForOrder(array $order): void
    {
        try {
            $orderId = $order['id'];
            $balanceModel = new ShopOwnerBalance();
            $feePercentage = $balanceModel->getServiceFeePercentage();

            $shopOwnerShares = $balanceModel->getShopOwnerSharesByOrder($orderId);

            foreach ($shopOwnerShares as $share) {
                $shopOwnerId = (int)$share['shop_owner_id'];
                $grossAmount = (float)$share['owner_total'];
                $feeAmount   = round($grossAmount * ($feePercentage / 100), 2);

                $balanceModel->creditSalePending($shopOwnerId, $orderId, $grossAmount, $feeAmount);

                error_log("COD hold — shop owner {$shopOwnerId}: gross={$grossAmount}, fee={$feeAmount}, net=" . ($grossAmount - $feeAmount));
            }

        } catch (\Exception $e) {
            error_log('Failed to hold COD earnings for order ' . ($order['id'] ?? 'unknown') . ': ' . $e->getMessage());
        }
    }

    /**
>>>>>>> 18941f66cb081928b3385b630a63cc878d80563e
     * Send order notifications to customer and shop owner(s).
     */
    private function sendOrderNotifications(array $order): void
    {
        try {
            $orderId = $order['id'];
            $orderNumber = $order['order_number'] ?? 'N/A';
            $totalAmount = $order['total_amount'] ?? 0;
            
            $balanceModel = new ShopOwnerBalance();
            
            // 1. Notify customer
            if (!empty($order['user_id'])) {
                $balanceModel->insertNotification(
                    $order['user_id'],
                    'payment_success',
                    'Order Confirmed',
                    'Your order #' . $orderNumber . ' has been confirmed. Total: LKR ' . number_format($totalAmount, 2),
                    ['order_id' => $orderId, 'order_number' => $orderNumber]
                );
            }
            
            // 2. Notify each shop owner
            $owners = $balanceModel->getShopOwnersByOrder($orderId);
            
            foreach ($owners as $owner) {
                $balanceModel->insertNotification(
                    $owner['shop_owner_id'],
                    'new_order',
                    'New Order Received',
                    'You have a new order #' . $orderNumber . '. Check your orders page for details.',
                    ['order_id' => $orderId, 'order_number' => $orderNumber]
                );
            }
            
            error_log("Order notifications sent for order #{$orderNumber}");
            
        } catch (\Exception $e) {
            error_log('Failed to send order notifications: ' . $e->getMessage());
        }
    }
}