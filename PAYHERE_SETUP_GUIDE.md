# PayHere Payment Gateway - Setup Guide

## ✅ Implementation Complete

PayHere payment gateway has been successfully integrated into GoPlay platform. This guide will help you configure and test the payment system.

---

## 📋 What's Been Implemented

### 1. **Backend Components**
   - ✅ `config/payhere.php` - PayHere configuration file
   - ✅ `PaymentController.php` - Added PayHere methods:
     - `initializePayHerePayment()` - Creates order and returns payment data
     - `payHereNotify()` - Handles PayHere notification callback
     - `payHereReturn()` - Handles customer return after payment
     - `generatePayHereHash()` - Generates secure payment hash
   - ✅ `Cart.php` - Added `getUserCarts()` method
   - ✅ Routes added to `index.php`:
     - `POST /api/checkout/initialize-payhere`
     - `POST /api/payment/payhere/notify`
     - `GET /api/payment/payhere/return`

### 2. **Frontend Components**
   - ✅ `payment-processing.php` - Updated with PayHere SDK integration
   - ✅ Removed manual card form (PayHere handles card collection)
   - ✅ Added order summary display
   - ✅ Integrated PayHere callbacks (onCompleted, onDismissed, onError)

### 3. **Payment Flow**
   ```
   Customer → Contact Details → Payment Method → "Pay with Card" 
   → PayHere Payment Page → Payment Complete 
   → Order Updated (paid) → Cart Cleared 
   → Updates in My Orders & Shop Owner Orders
   ```

---

## 🔧 Configuration Steps

### Step 1: Get PayHere Credentials

#### For Testing (Sandbox):
1. Visit: https://support.payhere.lk/api-&-mobile-sdk/sandbox-for-payhere
2. Use the default sandbox credentials (already in config):
   - **Merchant ID**: `1221149`
   - **Merchant Secret**: `MzE5MTE0MzMyMzIyNDU4MjI2NjUzMjcyNzAyNDgzNjcxNjE3MDkx`

#### For Production (Live):
1. Sign up at: https://www.payhere.lk/merchant/
2. Complete merchant registration
3. Get your **Merchant ID** and **Merchant Secret**
4. Update `config/payhere.php` with your credentials

### Step 2: Update Configuration File

Open `config/payhere.php` and update:

```php
return [
    // Change to 'live' when ready for production
    'mode' => 'sandbox', // 'sandbox' or 'live'
    
    // Replace with your credentials
    'merchant_id' => 'YOUR_MERCHANT_ID',
    'merchant_secret' => 'YOUR_MERCHANT_SECRET',
    
    // Other settings are already configured
];
```

### Step 3: Configure Notification URLs in PayHere Dashboard

When you have your live account, configure these URLs in PayHere merchant dashboard:

1. **Notify URL**: `https://yourdomain.com/api/payment/payhere/notify`
2. **Return URL**: `https://yourdomain.com/api/payment/payhere/return`
3. **Cancel URL**: `https://yourdomain.com/checkout/payment-method`

**For local testing**, you need to expose your local server using:
- **ngrok**: `ngrok http 80`
- **LocalTunnel**: `lt --port 80`
- Use the public URL provided as your domain

---

## 🧪 Testing Guide

### Test with Sandbox

1. **Start your local server**:
   ```bash
   # Make sure WAMP is running
   # Navigate to: http://localhost/main/GoPlay
   ```

2. **Add products to cart** and proceed to checkout

3. **Fill contact details** and select "Pay with Card"

4. **PayHere test cards**:
   - **Visa**: `4916217501611292`
   - **MasterCard**: `5123456789012346`
   - **Expiry**: Any future date (e.g., `12/25`)
   - **CVV**: Any 3 digits (e.g., `123`)
   - **OTP for testing**: `1234`

5. **Check order updates**:
   - Customer: `/my-orders` - Should show "Paid" status
   - Shop Owner: `/shop-owner/orders` - Should show the order with "Paid" payment status

### What to Check

✅ **After Payment Success:**
- Order status changes from "pending" to "processing"
- Payment status changes from "pending" to "paid"
- Transaction ID is recorded in payments table
- Cart is cleared
- Order appears in customer's "My Orders" page
- Order appears in shop owner's orders page
- Gateway response is stored in `payments.gateway_response` field

✅ **After Payment Failure:**
- Order status changes to "cancelled"
- Payment status is "failed"
- Cart remains intact (customer can retry)

---

## 🗄️ Database Schema

No database changes needed! The system uses existing tables:

### `orders` table
- `payment_status`: Updated to 'paid' when payment succeeds
- `status`: Updated to 'processing' after successful payment

### `payments` table
- `transaction_id`: PayHere payment ID
- `status`: 'completed' for successful payments
- `gateway_response`: JSON with full PayHere response
- `processed_at`: Payment completion timestamp

---

## 🔐 Security Features

✅ **Hash Verification**: All PayHere notifications are verified using MD5 hash
✅ **Double Verification**: Payment verified both in notify and return callbacks
✅ **No Card Storage**: Card details never touch your server (PCI compliance)
✅ **SSL Required**: Payment data transmitted securely
✅ **Order Matching**: Orders verified by order_number before updates

---

## 📱 PayHere Payment Popup

When customer clicks "Pay with PayHere", they'll see:
1. **Payment amount** and order details
2. **Card payment option** (Visa, MasterCard, Amex)
3. **Other options** (if enabled): eZcash, mCash, Lanka QR
4. **Secure payment form** hosted by PayHere
5. **3D Secure verification** (if required by card)

---

## 🐛 Troubleshooting

### Issue: Payment popup doesn't appear
**Solution**: 
- Check browser console for errors
- Verify PayHere SDK is loaded: `https://www.payhere.lk/lib/payhere.js`
- Check if payment initialization API returns valid data

### Issue: Payment succeeds but order not updated
**Solution**:
- Check `notify_url` is accessible (use ngrok for local testing)
- Check error logs at `logs/` folder
- Verify hash secret matches in both config and PayHere dashboard

### Issue: Hash verification failed
**Solution**:
- Ensure `merchant_secret` in config matches PayHere dashboard
- Check for extra spaces or line breaks in merchant_secret
- Amount must be formatted with 2 decimal places

### Issue: Orders not showing in My Orders
**Solution**:
- Check if user is logged in (user_id must be set)
- Verify order was created with correct user_id
- Check browser console for API errors

---

## 🔄 Going Live Checklist

Before going live with real payments:

- [ ] Get live PayHere merchant account
- [ ] Update `config/payhere.php` with live credentials
- [ ] Change mode to `'live'` in config
- [ ] Configure notification URLs in PayHere dashboard
- [ ] Test with real small amounts
- [ ] Enable SSL certificate on your domain
- [ ] Set up proper error logging
- [ ] Configure email notifications for orders
- [ ] Test refund process (if applicable)
- [ ] Set up monitoring for failed payments

---

## 📊 Monitoring Payments

### Check Payment Logs
```bash
# View PHP error logs
tail -f c:/wamp64/logs/php_error.log

# Search for PayHere related logs
grep "PayHere" c:/wamp64/logs/php_error.log
```

### Database Queries

**Check recent payments:**
```sql
SELECT o.order_number, o.payment_status, p.transaction_id, p.status, p.amount, p.created_at
FROM orders o
LEFT JOIN payments p ON o.id = p.order_id
ORDER BY o.created_at DESC
LIMIT 20;
```

**Check failed payments:**
```sql
SELECT * FROM payments WHERE status = 'failed' ORDER BY created_at DESC;
```

---

## 📞 Support

### PayHere Support
- Documentation: https://support.payhere.lk/
- Email: support@payhere.lk
- Phone: +94 11 2 309309

### Testing Resources
- Sandbox: https://sandbox.payhere.lk/
- API Documentation: https://support.payhere.lk/api-&-mobile-sdk/payhere-checkout

---

## 🎉 Success!

Your PayHere payment integration is complete and ready to process payments! 

**Test thoroughly in sandbox mode before going live.**

For any issues or questions about this integration, check the logs and verify your configuration settings.
