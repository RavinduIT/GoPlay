<?php

//$ROOT = defined('ROOT_PATH') ? ROOT_PATH : dirname(__DIR__, 3); // project root
$title = 'Secure Payment - GoPlay Sports Platform';
$additionalCSS = [];
$additionalJS = [];

// include header/navbar
//require $ROOT . '/app/views/components/navbar.php';

$name   = $_GET['name']  ?? 'Professional Basketball';
$qty    = intval($_GET['qty'] ?? 1);
$price  = floatval($_GET['price'] ?? 89.00);
$subtotal = $price * $qty;
$tax = round($subtotal * 0.08, 2);
$shipping = 9.99;
$total = $subtotal + $tax + $shipping;
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
:root{
  --primary-color:#2563eb;--primary-dark:#64748b;--primary-light:#f1f5f9;
  --secondary-color:#0891b2;--accent-color:#ffc107;
  --text-primary:#2c3e50;--text-secondary:#6c757d;--text-light:#adb5bd;--text-white:#fff;
  --background-white:#fff;--background-light:#f8f9fa;--background-dark:#343a40;
  --border-color:#e9ecef;--shadow-light:0 2px 8px rgba(0,0,0,.08);--shadow-medium:0 4px 16px rgba(0,0,0,.12);
  --border-radius:8px;--border-radius-lg:12px;--border-radius-xl:16px;--transition:all .25s ease;
}
*{box-sizing:border-box} body{font-family:Inter,system-ui,Segoe UI,Roboto,Arial,sans-serif;color:var(--text-primary);background:var(--background-white);margin:0}
.container{max-width:1200px;margin:0 auto;padding:0 1.5rem}
.header{background:var(--background-light);padding:1.25rem 0;text-align:center}
.header h2{margin:0;font-weight:800; color: var(--primary-color)}
.header p{color:var(--text-secondary)}
.page{padding:1.25rem 0 2.5rem}
.grid{display:grid;grid-template-columns:1fr 2fr;gap:1.5rem}
.card{background:#fff;border:1px solid var(--border-color);border-radius:var(--border-radius-lg);box-shadow:var(--shadow-light)}
.card-body{padding:1rem 1.25rem}
.summary h4{margin:.25rem 0 1rem}
.row{display:flex;justify-content:space-between;margin:.35rem 0;color:var(--text-secondary)}
.total{display:flex;justify-content:space-between;font-weight:800;border-top:1px dashed var(--border-color);padding-top:.75rem;margin-top:.5rem}
.item{display:flex;align-items:center;gap:.75rem;margin:.5rem 0}
.item img{width:48px;height:48px;border-radius:8px;object-fit:cover;border:1px solid var(--border-color)}
.stepper{display:flex;align-items:center;gap:1rem;margin-bottom:1rem}
.dot{width:28px;height:28px;border-radius:999px;display:flex;align-items:center;justify-content:center;border:2px solid var(--primary-color);color:var(--primary-color);font-weight:700}
.dot.muted{border-color:var(--border-color);color:var(--text-light)}
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:1rem}
.input, .select{width:100%;padding:.7rem .75rem;border:1px solid var(--border-color);border-radius:10px}
.label{font-weight:600;font-size:.9rem;margin-bottom:.35rem;display:block}
.actions{margin-top:1rem}
.btn{border:none;border-radius:10px;padding:.95rem 1rem;font-weight:800;cursor:pointer;transition:var(--transition);width:100%}
.btn-primary{background:linear-gradient(135deg,var(--primary-color),var(--secondary-color));color:#fff}
.btn-primary:hover{box-shadow:var(--shadow-medium);transform:translateY(-1px)}
.back{display:inline-flex;align-items:center;gap:.5rem;color:var(--text-secondary);text-decoration:none;margin:.5rem 0}
@media (max-width: 900px){.grid{grid-template-columns:1fr}}
</style>

<div class="header">
  <h2>Secure Payment</h2>
  <p>Complete your purchase with confidence</p>
</div>

<div class="container page">
  <a class="back" href="/shop"><i class="fa fa-arrow-left"></i> Back</a>

  <div class="grid">
    <!-- Order Summary -->
    <div class="card summary">
      <div class="card-body">
        <h4>Order Summary</h4>
        <div class="item">
          <img src="/public/images/products/bball-1.jpg" alt="Item">
          <div>
            <div style="font-weight:700"><?= htmlspecialchars(urldecode($name)) ?></div>
            <div style="color:var(--text-secondary);font-size:.9rem">Qty: <?= $qty ?></div>
          </div>
          <div style="margin-left:auto;font-weight:700">$<?= number_format($subtotal,2) ?></div>
        </div>

        <div class="row"><span>Subtotal</span><span>$<?= number_format($subtotal,2) ?></span></div>
        <div class="row"><span>Tax (8%)</span><span>$<?= number_format($tax,2) ?></span></div>
        <div class="row"><span>Shipping</span><span>$<?= number_format($shipping,2) ?></span></div>
        <div class="total"><span>Total</span><span>$<?= number_format($total,2) ?></span></div>
      </div>
    </div>

    <!-- Contact & Payment -->
    <div class="card">
      <div class="card-body">
        <div class="stepper">
          <div class="dot">1</div><span>Contact Details</span>
          <i class="fa fa-ellipsis-h" style="color:var(--text-light)"></i>
          <div class="dot muted">2</div><span>Payment</span>
        </div>

        <form id="checkoutForm" onsubmit="return goToPayment(event)">
          <div class="form-grid">
            <div>
              <label class="label">Full Name</label>
              <input class="input" type="text" name="fullname" required placeholder="Enter your full name">
            </div>
            <div>
              <label class="label">Email Address</label>
              <input class="input" type="email" name="email" required placeholder="you@email.com">
            </div>
            <div>
              <label class="label">Phone Number</label>
              <input class="input" type="tel" name="phone" required placeholder="+1 (555) 123-4567">
            </div>
            <div>
              <label class="label">Country</label>
              <select class="select" name="country" required>
                <option>United States</option><option>United Kingdom</option><option>Sri Lanka</option>
              </select>
            </div>
            <div style="grid-column:1/-1">
              <label class="label">Street Address</label>
              <input class="input" type="text" name="address" required placeholder="123 Main St, Apt 4B">
            </div>
            <div>
              <label class="label">City</label>
              <input class="input" type="text" name="city" required>
            </div>
            <div>
              <label class="label">State/Province</label>
              <input class="input" type="text" name="state" required>
            </div>
            <div>
              <label class="label">ZIP/Postal Code</label>
              <input class="input" type="text" name="zip" required>
            </div>
          </div>

          <div class="actions">
            <!--<button class="btn btn-primary" type="submit">
              Continue to Payment <i class="fa fa-arrow-right" style="margin-left:.5rem"></i>
            </button>-->
            <a class="btn btn-primary"
                href="/app/views/payment/payment-method.php">
                Continue to Payment <i class="fa fa-arrow-right" style="margin-left:.5rem"></i>
            </a>
          </div>
        </form>

        <!-- Mock Payment Step (appears after Continue) -->
        <div id="paymentStep" style="display:none;margin-top:1.25rem">
          <div class="stepper">
            <div class="dot muted">1</div><span>Contact Details</span>
            <i class="fa fa-ellipsis-h" style="color:var(--text-light)"></i>
            <div class="dot">2</div><span>Payment</span>
          </div>

          <div class="form-grid">
            <div style="grid-column:1/-1">
              <label class="label">Card Number</label>
              <input class="input" placeholder="4242 4242 4242 4242" maxlength="19">
            </div>
            <div>
              <label class="label">Expiry</label>
              <input class="input" placeholder="MM/YY" maxlength="5">
            </div>
            <div>
              <label class="label">CVC</label>
              <input class="input" placeholder="CVC" maxlength="4">
            </div>
            <div style="grid-column:1/-1;margin-top:.5rem">
              <button class="btn btn-primary" onclick="finishOrder()">
                Pay $<?= number_format($total,2) ?> <i class="fa fa-lock" style="margin-left:.5rem"></i>
              </button>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

<script>
function goToPayment(e){
  e.preventDefault();
  document.getElementById('checkoutForm').style.display='none';
  document.getElementById('paymentStep').style.display='block';
  return false;
}
function finishOrder(){
  alert('Payment successful! Thank you for your purchase.');
  window.location.href='/order-success.php';
}
</script>
<?php
//require $ROOT . '/app/views/components/footer.php';