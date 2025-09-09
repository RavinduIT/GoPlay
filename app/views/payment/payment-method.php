<?php
// app/views/payment/payment-method.php (example path)
$title = 'Choose Payment Method - GoPlay Sports Platform';

// ---- Example order values (replace with your own) ----
$name     = $_GET['name']  ?? 'Professional Basketball';
$qty      = intval($_GET['qty'] ?? 1);
$price    = floatval($_GET['price'] ?? 89.00);
$subtotal = $price * $qty;
$shipping = 4.00; // default radio selected; user can change
$tax      = round($subtotal * 0.08, 2);
$total    = $subtotal + $tax + $shipping;

// ---- Shared components (navbar/footer) ----
// current file is .../app/views/payment/payment-method.php
$VIEWS = dirname(__DIR__); // -> .../app/views
require_once $VIEWS . '/components/navbar.php';
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
/* Keep the exact palette you used in payment.php */
:root{
  --primary-color:#2563eb;--primary-dark:#64748b;--primary-light:#f1f5f9;
  --secondary-color:#0891b2;--accent-color:#ffc107;
  --text-primary:#2c3e50;--text-secondary:#6c757d;--text-light:#adb5bd;--text-white:#fff;
  --background-white:#fff;--background-light:#f8f9fa;--background-dark:#343a40;
  --border-color:#e9ecef;--shadow-light:0 2px 8px rgba(0,0,0,.08);--shadow-medium:0 4px 16px rgba(0,0,0,.12);
  --border-radius:8px;--border-radius-lg:12px;--border-radius-xl:16px;--transition:all .25s ease;
}
*{box-sizing:border-box}
body{font-family:Inter,system-ui,Segoe UI,Roboto,Arial,sans-serif;color:var(--text-primary);background:var(--background-white);margin:0}
.wrapper{max-width:960px;margin:0 auto;padding:24px 16px}
.center{display:flex;justify-content:center}
.card{background:#fff;border:1px solid var(--border-color);border-radius:12px;box-shadow:var(--shadow-light);max-width:680px;width:100%}
.card-body{padding:24px}
.header{text-align:center;margin:16px 0 8px}
.header .icon{width:52px;height:52px;border-radius:999px;display:flex;align-items:center;justify-content:center;
  background:rgba(37,99,235,.08);color:var(--secondary-color);margin:0 auto 10px;font-size:22px}
.h-title{margin:0;font-size:28px;font-weight:800;color:var(--primary-color)} /* green-ish success tone from screenshot */
.h-sub{margin:6px 0 0;color:var(--text-secondary);font-size:.95rem}

.grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-top:18px}
@media (max-width: 820px){.grid{grid-template-columns:1fr}}

.block{border:1px solid var(--border-color);border-radius:10px;background:var(--background-white)}
.block h4{margin:0;padding:14px 16px;border-bottom:1px solid var(--border-color);font-size:1rem}
.block-body{padding:14px 16px}

.item-row{display:flex;justify-content:space-between;align-items:center;margin:6px 0;color:var(--text-secondary)}
.total-row{display:flex;justify-content:space-between;align-items:center;margin-top:10px;padding-top:10px;border-top:1px dashed var(--border-color);font-weight:800}
.badge{font-size:.75rem;padding:2px 8px;border-radius:999px;background:var(--primary-light);color:var(--primary-dark)}

.radio{display:flex;align-items:center;gap:10px;padding:12px;border:1px solid var(--border-color);border-radius:10px;background:#fff;cursor:pointer}
.radio + .radio{margin-top:10px}
.radio input{accent-color:var(--primary-color)}
.radio.active{border-color:var(--primary-color);box-shadow:0 0 0 3px rgba(37,99,235,.08)}
.pill{font-size:.8rem;padding:2px 8px;border-radius:999px;background:var(--background-light);color:var(--text-secondary)}

.help{background:var(--primary-light);border:1px solid var(--border-color);padding:10px 12px;border-radius:8px;color:var(--text-secondary);font-size:.9rem}

.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-top:12px}
.form-grid .full{grid-column:1/-1}
.input{width:100%;padding:10px 12px;border:1px solid var(--border-color);border-radius:10px}
.label{display:block;font-weight:600;font-size:.9rem;margin-bottom:6px}

.methods{margin-top:8px}
.method-panel{display:none;margin-top:10px}
.method-panel.show{display:block}

.buttons{display:flex;gap:10px;margin-top:16px}
.btn{border:none;border-radius:10px;padding:12px 14px;font-weight:800;cursor:pointer;transition:var(--transition)}
.btn-primary{background:linear-gradient(135deg,var(--primary-color),var(--secondary-color));color:#fff}
.btn-primary:hover{box-shadow:var(--shadow-medium);transform:translateY(-1px)}
.btn-ghost{background:var(--background-light);border:1px solid var(--border-color);color:var(--text-primary)}

.note{font-size:.85rem;color:var(--text-secondary);margin-top:10px}
.small{font-size:.85rem;color:var(--text-secondary)}
</style>

<div class="wrapper">
  <div class="center">
    <div class="card">
      <div class="card-body">

        <!-- Header (like your first screenshot) -->
        <div class="header">
          <div class="icon"><i class="fa fa-credit-card"></i></div>
          <h2 class="h-title">Choose Your Payment Method</h2>
          <p class="h-sub">Secure checkout · Your information is protected</p>
        </div>

        <div class="grid">
          <!-- Left: Order summary and shipping -->
          <div class="block">
            <h4>Order Summary</h4>
            <div class="block-body">
              <div class="item-row">
                <div>
                  <strong><?= htmlspecialchars(urldecode($name)) ?></strong>
                  <span class="badge">Qty: <?= $qty ?></span>
                </div>
                <div><strong>$<?= number_format($subtotal,2) ?></strong></div>
              </div>
              <div class="item-row"><span>Subtotal</span><span>$<?= number_format($subtotal,2) ?></span></div>
              <div class="item-row"><span>Tax (8%)</span><span>$<?= number_format($tax,2) ?></span></div>

              <div class="item-row" style="align-items:flex-start;gap:10px;flex-direction:column;margin-top:10px">
                <strong>Shipping</strong>
                <label class="radio"><input type="radio" name="ship" value="4.00" checked> Domestic Shipping: $4.00</label>
                <label class="radio"><input type="radio" name="ship" value="9.99"> Domestic Priority: $9.99</label>
              </div>

              <div class="total-row"><span>Total</span><span id="grandTotal">$<?= number_format($total,2) ?></span></div>
            </div>
          </div>

          <!-- Right: Payment methods -->
          <div class="block">
            <h4>Payment</h4>
            <div class="block-body">

              <div class="methods" id="methodList">
                <!-- COD -->
                <label class="radio active" data-target="#codPanel">
                  <input type="radio" name="pm" value="cod" checked>
                  <span><strong>Cash on Delivery (COD)</strong></span>
                  <span class="pill">Pay at delivery</span>
                </label>

                <!-- Card -->
                <label class="radio" data-target="#cardPanel">
                  <input type="radio" name="pm" value="card">
                  <span><strong>Credit / Debit Card</strong></span>
                  <span class="small"><i class="fa-brands fa-cc-visa"></i> <i class="fa-brands fa-cc-mastercard"></i> <i class="fa-brands fa-cc-amex"></i></span>
                </label>

                <!-- PayPal (optional) -->
                <label class="radio" data-target="#paypalPanel">
                  <input type="radio" name="pm" value="paypal">
                  <span><strong>PayPal</strong></span>
                  <span class="small"><i class="fa-brands fa-paypal"></i></span>
                </label>
              </div>

              <!-- Panels -->
              <div id="codPanel" class="method-panel show">
                <div class="help"><i class="fa fa-circle-info"></i> Your order will be paid in cash upon delivery. Please prepare the exact amount if possible.</div>
                <div class="note">* COD may not be available in all regions.</div>
              </div>

              <div id="cardPanel" class="method-panel">
                <div class="form-grid">
                  <div class="full">
                    <label class="label">Card Number</label>
                    <input class="input" placeholder="4242 4242 4242 4242" maxlength="19">
                  </div>
                  <div>
                    <label class="label">Expiration (MM/YY)</label>
                    <input class="input" placeholder="MM/YY" maxlength="5">
                  </div>
                  <div>
                    <label class="label">Card Security Code (CVC)</label>
                    <input class="input" placeholder="CVC" maxlength="4">
                  </div>
                  <div class="full">
                    <label class="label">Name on Card</label>
                    <input class="input" placeholder="Full name">
                  </div>
                </div>
                <div class="note"><input type="checkbox" id="saveCard"> <label for="saveCard" class="small">Securely save this card for next time</label></div>
              </div>

              <div id="paypalPanel" class="method-panel">
                <div class="help"><i class="fa fa-circle-info"></i> You’ll be redirected to PayPal to complete your purchase securely.</div>
              </div>

              <!-- Terms -->
              <div class="note"><input type="checkbox" id="terms"> <label for="terms" class="small">I’ve agreed to the terms and conditions</label></div>

              <!-- Buttons -->
              <div class="buttons">
                <button class="btn btn-primary" id="placeOrder">Place Order</button>
                <a class="btn btn-ghost" href="/shop">Continue Shopping</a>
              </div>

            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

<script>
// Shipping total updates
const shipRadios = document.querySelectorAll('input[name="ship"]');
const grandTotalEl = document.getElementById('grandTotal');
const base = <?= json_encode($subtotal + $tax) ?>;
function updateTotal(){
  const ship = parseFloat(document.querySelector('input[name="ship"]:checked').value);
  const total = (base + ship).toFixed(2);
  grandTotalEl.textContent = `$${total}`;
}
shipRadios.forEach(r=>r.addEventListener('change',updateTotal));

// Method switching
const methodList = document.getElementById('methodList');
methodList.addEventListener('change', (e)=>{
  if(e.target.name !== 'pm') return;
  document.querySelectorAll('.radio').forEach(r=>r.classList.remove('active'));
  e.target.closest('.radio').classList.add('active');
  document.querySelectorAll('.method-panel').forEach(p=>p.classList.remove('show'));
  const target = e.target.closest('.radio').dataset.target;
  document.querySelector(target).classList.add('show');
});

// Place order button (demo)
document.getElementById('placeOrder').addEventListener('click', ()=>{
  const terms = document.getElementById('terms').checked;
  if(!terms){ alert('Please agree to the terms and conditions.'); return; }

  const pm = document.querySelector('input[name="pm"]:checked').value;
  // You can POST to your backend here; for now we simulate success:
  window.location.href = '/payment-success.php';
});
</script>

<?php
// Footer include
require_once $VIEWS . '/components/footer.php';
?>
