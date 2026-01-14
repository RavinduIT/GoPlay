<?php

namespace App\Controllers;

use Core\Request;
use Core\Response;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Cart;

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
        // Ensure user has items in cart
        $session = $this->getCartSession();
        $cart = $this->cartModel->getOrCreateCart($session['user_id'], $session['session_id']);
        $cartDetails = $this->cartModel->getCartDetails($cart['id']);

        if (empty($cartDetails['items'])) {
            return $this->redirect('/shop');
        }

        // Load previously saved contact details from session if available
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
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
        // Ensure contact details are saved
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

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
        // Ensure contact details are saved
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

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

            // Clear cart
            $this->cartModel->clearCart($cart['id']);

            // Clear session data
            unset($_SESSION['checkout_contact']);

            // TODO: Send order confirmation email
            // $this->sendOrderConfirmationEmail($order, $contactDetails);

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
     * Process Card Payment
     */
    public function processCardPayment(Request $request): Response
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

            // Process payment through gateway
            $paymentResult = $this->paymentModel->processCardPayment([
                'amount' => $totals['total_amount'],
                'currency' => 'LKR'
            ]);

            if (!$paymentResult['success']) {
                return $this->json([
                    'success' => false,
                    'message' => $paymentResult['message']
                ], 400);
            }

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
                'payment_status' => 'paid',
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

            // Create payment record
            $this->paymentModel->createPayment([
                'order_id' => $orderId,
                'transaction_id' => $paymentResult['transaction_id'],
                'payment_method' => 'credit_card',
                'amount' => $totals['total_amount'],
                'currency' => 'LKR',
                'status' => 'completed',
                'gateway_response' => json_encode($paymentResult),
                'processed_at' => date('Y-m-d H:i:s')
            ]);

            // Clear cart
            $this->cartModel->clearCart($cart['id']);

            // Clear session data
            unset($_SESSION['checkout_contact']);

            // TODO: Send order confirmation email
            // $this->sendOrderConfirmationEmail($order, $contactDetails);

            return $this->json([
                'success' => true,
                'message' => 'Payment successful',
                'orderNumber' => $order['order_number'],
                'orderId' => $orderId,
                'transactionId' => $paymentResult['transaction_id']
            ]);

        } catch (\Exception $e) {
            error_log('Card Payment Error: ' . $e->getMessage());
            return $this->json([
                'success' => false,
                'message' => 'Payment failed: ' . $e->getMessage()
            ], 500);
        }
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
}