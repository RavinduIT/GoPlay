<?php
$_base = defined('BASE_URL') ? BASE_URL : '';
$title = 'Booking Confirmed | GoPlay';
$additionalCSS = [];
?>
<div style="min-height:60vh;display:flex;align-items:center;justify-content:center;padding:2rem;">
  <div style="text-align:center;max-width:480px;width:100%;">
    <div style="width:80px;height:80px;background:#dcfce7;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem;">
      <i class="fas fa-check" style="font-size:2rem;color:#16a34a;"></i>
    </div>
    <h1 style="font-size:1.75rem;font-weight:700;color:#111827;margin-bottom:.5rem;">Booking Confirmed!</h1>
    <p style="color:#6b7280;margin-bottom:1.5rem;">
      Your payment was successful and your booking is now confirmed.<br>
      <?php if (!empty($order['order_number'])): ?>
        Reference: <strong><?= htmlspecialchars($order['order_number']) ?></strong>
      <?php endif; ?>
    </p>
    <div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap;">
      <a href="/my-bookings" style="background:#3b82f6;color:#fff;padding:.6rem 1.4rem;border-radius:8px;text-decoration:none;font-weight:600;">
        <i class="fas fa-calendar-check"></i> My Bookings
      </a>
      <a href="/my-coach-sessions" style="background:#10b981;color:#fff;padding:.6rem 1.4rem;border-radius:8px;text-decoration:none;font-weight:600;">
        <i class="fas fa-user-tie"></i> My Sessions
      </a>
      <a href="/" style="background:#f3f4f6;color:#374151;padding:.6rem 1.4rem;border-radius:8px;text-decoration:none;font-weight:600;">
        <i class="fas fa-home"></i> Home
      </a>
    </div>
  </div>
</div>
