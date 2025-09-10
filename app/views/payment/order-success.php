<?php
// app/views/payment/payment-success.php
$title = 'Payment Successful - GoPlay Sports Platform';

// Include shared components (adjust path if needed)
$VIEWS = dirname(__DIR__); // -> .../app/views
require_once $VIEWS . '/components/navbar.php';
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
/* Same palette as your other pages */
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

.wrapper{max-width:900px;margin:0 auto;padding:60px 16px}
.center{display:flex;justify-content:center;align-items:center}
.card{background:#fff;border:1px solid var(--border-color);border-radius:12px;box-shadow:var(--shadow-light);max-width:480px;width:100%;min-height: 500px}
.card-body{padding:26px}

.icon-wrap{width:56px;height:56px;border-radius:999px;display:flex;align-items:center;justify-content:center;
  background:rgba(37,99,235,.10); color:var(--primary-color); margin:8px auto 10px; font-size:22px}
.h-title{margin:0;text-align:center;font-size:22px;font-weight:800;color:var(--primary-color);} /* blue heading */
.h-sub{margin:6px 0 0;text-align:center;color:var(--text-secondary);font-size:.95rem}

.status{margin:25px 0 0;border:1px solid var(--border-color);background:var(--primary-light);border-radius:10px}
.status .row{display:flex;gap:10px;align-items:flex-start;padding:12px 14px;color:var(--text-secondary)}
.status .row i{color:var(--primary-dark);margin-top:2px}

.btns{display:flex;gap:10px;margin-top:16px}
.btn{border:none;border-radius:10px;padding:12px 14px;font-weight:800;cursor:pointer;transition:var(--transition);width:100%}
.btn-primary{background:linear-gradient(135deg,var(--primary-color),var(--secondary-color));color:#fff}
.btn-primary:hover{box-shadow:var(--shadow-medium);transform:translateY(-1px)}
.btn-ghost{background:var(--background-light);border:1px solid var(--border-color);color:var(--text-primary)}

.footer-note{margin-top:10px;text-align:center;color:var(--text-light);font-size:.85rem}
</style>

<div class="wrapper">
  <div class="center">
    <div class="card">
      <div class="card-body">

        <div class="icon-wrap"><i class="fa fa-check"></i></div>
        <h2 class="h-title">Payment Successful!</h2>
        <p class="h-sub">Thank you for your purchase! Your order has been successfully placed.</p>

        <div class="status">
          <div class="row">
            <i class="fa fa-receipt"></i>
            <div>
              <strong>Order Status</strong>
              <div style="margin-top:4px">
                Your order is being processed and you will receive a confirmation email shortly.
                The shop owner will contact you within 24 hours to arrange delivery.
              </div>
            </div>
          </div>
        </div>

        <div class="btns">
          <a class="btn btn-primary" href="/shop">Continue Shopping</a>
          <a class="btn btn-ghost" href="/"><i class="fa fa-arrow-left" style="margin-right:6px"></i>Back to Home</a>
        </div>

        <div class="footer-note">Need help? <a href="/contact" style="color:var(--primary-color);text-decoration:none">Contact support</a></div>

      </div>
    </div>
  </div>
</div>

<?php
require_once $VIEWS . '/components/footer.php';
?>
