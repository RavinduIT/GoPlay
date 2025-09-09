<?php

$ROOT = defined('ROOT_PATH') ? ROOT_PATH : dirname(__DIR__, 3); // project root
$title = 'Secure Payment - GoPlay Sports Platform';
$additionalCSS = [];
$additionalJS = [];

// include header/navbar
require $ROOT . '/app/views/components/navbar.php';
$product = [
  "id" => $_GET['id'] ?? 101,
  "brand" => "Nike",
  "name" => "Professional Basketball",
  "price" => 89.00,
  "oldPrice" => 120.00,
  "rating" => 4.8,
  "reviews" => 156,
  "stock" => 25,
  "colors" => ["Orange","Brown","White"],
  "sizes" => ["Official Size","Youth Size"],
  "images" => [
    "/public/images/products/bball-1.jpg",
    "/public/images/products/bball-2.jpg",
    "/public/images/products/bball-3.jpg",
    "/public/images/products/bball-4.jpg",
  ],
  "features" => ["Premium Leather","Official Size","Deep Channel Design","Moisture Wicking"],
  "description" => "High-quality professional basketball made with premium leather for excellent grip and durability. Perfect for indoor and outdoor courts."
];
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
/* use your palette */
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
.breadcrumb{background:var(--background-light);padding:1rem 0}
.breadcrumb a{color:var(--text-secondary);text-decoration:none}
.breadcrumb a:hover{color:var(--primary-color)}
.breadcrumb .current{font-weight:600;color:var(--text-primary)}
.page{padding:1.5rem 0 3rem}
.grid{display:grid;grid-template-columns:1.3fr 1fr;gap:2rem}
.card{background:#fff;border:1px solid var(--border-color);border-radius:var(--border-radius-lg);box-shadow:var(--shadow-light)}
.card-body{padding:1.25rem}
.gallery-main{height:380px;border-radius:12px;overflow:hidden}
.gallery-main img{width:100%;height:100%;object-fit:cover;transition:transform .4s}
.gallery-main:hover img{transform:scale(1.03)}
.thumbs{display:grid;grid-template-columns:repeat(4,1fr);gap:.5rem;margin-top:.75rem}
.thumbs img{width:100%;height:80px;object-fit:cover;border-radius:8px;border:2px solid transparent;cursor:pointer}
.thumbs img.active, .thumbs img:hover{border-color:var(--primary-color);background:var(--primary-light)}
.title{font-size:1.5rem;font-weight:700;margin:0}
.brand{color:var(--text-secondary);font-size:.95rem;margin:.25rem 0 1rem}
.rating{display:flex;align-items:center;gap:.5rem;margin:.25rem 0 1rem}
.star{color:var(--accent-color)}
.price-wrap{display:flex;align-items:baseline;gap:.75rem;margin:.5rem 0 1rem}
.price{font-size:1.75rem;font-weight:800;color:var(--primary-color)}
.old{color:var(--text-light);text-decoration:line-through}
.badge{font-size:.75rem;padding:.25rem .5rem;border-radius:999px;background:var(--primary-light);color:var(--primary-dark)}
.options{display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin:1rem 0}
.opt label{font-weight:600;font-size:.9rem;margin-bottom:.35rem;display:block}
.select, .input{width:100%;border:1px solid var(--border-color);border-radius:8px;padding:.65rem .75rem;background:#fff}
.qty{display:flex;align-items:center;gap:.5rem}
.qty button{width:34px;height:34px;border:1px solid var(--border-color);background:var(--background-light);border-radius:8px;cursor:pointer}
.actions{display:flex;gap:.75rem;margin-top:1rem}
.btn{border:none;border-radius:10px;padding:.9rem 1rem;font-weight:700;cursor:pointer;transition:var(--transition)}
.btn-primary{background:linear-gradient(135deg,var(--primary-color),var(--secondary-color));color:#fff}
.btn-primary:hover{box-shadow:var(--shadow-medium);transform:translateY(-1px)}
.btn-ghost{background:var(--background-light);color:var(--text-primary);border:1px solid var(--border-color)}
.section{margin-top:1.25rem}
.features{display:flex;flex-wrap:wrap;gap:.5rem}
.feature-chip{background:var(--background-light);border:1px solid var(--border-color);padding:.4rem .6rem;border-radius:999px;font-size:.85rem}
.review-box{margin-top:1.25rem}
.textarea{width:100%;min-height:110px;border:1px solid var(--border-color);border-radius:10px;padding:.75rem}
.submit{margin-top:.75rem}
.sidebar .card + .card{margin-top:1rem}
.meta{display:flex;align-items:center;gap:.5rem;font-size:.9rem;color:var(--text-secondary)}
@media (max-width: 900px){.grid{grid-template-columns:1fr}}
</style>

<!-- Breadcrumb -->
<div class="breadcrumb">
  <div class="container">
    <a href="/">Home</a> <i class="fa fa-chevron-right" style="margin:0 .5rem"></i>
    <a href="/shop">Sports Shop</a> <i class="fa fa-chevron-right" style="margin:0 .5rem"></i>
    <span class="current">Product Details</span>
  </div>
</div>

<div class="container page">
  <a href="/shop" style="text-decoration:none;color:var(--text-secondary)"><i class="fa fa-arrow-left"></i> Back to Shop</a>
  <div class="grid" style="margin-top:1rem">
    <!-- Left: Gallery -->
    <div class="card">
      <div class="card-body">
        <div class="gallery-main"><img id="mainImg" src="<?= $product['images'][0] ?>" alt="<?= htmlspecialchars($product['name']) ?>"></div>
        <div class="thumbs" id="thumbs">
          <?php foreach($product['images'] as $i=>$img): ?>
            <img class="<?= $i===0?'active':'' ?>" src="<?= $img ?>" data-img="<?= $img ?>" alt="thumb">
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <!-- Right: Info -->
    <div class="sidebar">
      <div class="card">
        <div class="card-body">
          <h1 class="title"><?= htmlspecialchars($product['name']) ?></h1>
          <div class="brand"><?= htmlspecialchars($product['brand']) ?></div>

          <div class="rating">
            <span>
              <?php
                $full = floor($product['rating']); $half = ($product['rating'] - $full) >= 0.5;
                for($i=0;$i<$full;$i++) echo '<i class="fa fa-star star"></i>';
                if($half) echo '<i class="fa fa-star-half-stroke star"></i>';
                for($i=0;$i<5-$full-($half?1:0);$i++) echo '<i class="fa-regular fa-star star"></i>';
              ?>
            </span>
            <strong><?= number_format($product['rating'],1) ?></strong>
            <span class="meta"> (<?= $product['reviews'] ?> reviews)</span>
            <span class="badge">In stock: <?= $product['stock'] ?></span>
          </div>

          <div class="price-wrap">
            <div class="price">$<?= number_format($product['price'],2) ?></div>
            <div class="old">$<?= number_format($product['oldPrice'],2) ?></div>
          </div>

          <div class="options">
            <div class="opt">
              <label>Color</label>
              <select class="select" id="color">
                <?php foreach($product['colors'] as $c): ?><option><?= $c ?></option><?php endforeach; ?>
              </select>
            </div>
            <div class="opt">
              <label>Size</label>
              <select class="select" id="size">
                <?php foreach($product['sizes'] as $s): ?><option><?= $s ?></option><?php endforeach; ?>
              </select>
            </div>
            <div class="opt">
              <label>Quantity</label>
              <div class="qty">
                <button type="button" id="minus">-</button>
                <input class="input" style="max-width:90px;text-align:center" id="qty" type="number" min="1" value="1">
                <button type="button" id="plus">+</button>
              </div>
            </div>
          </div>

          <div class="actions">
            <button class="btn btn-ghost" id="addCart"><i class="fa fa-cart-plus"></i> Add to Cart</button>
            <button class="btn btn-primary" id="buyNow"><i class="fa fa-bolt"></i> Buy Now</button>
          </div>

          <div class="section">
            <h3 style="margin:.5rem 0">Description</h3>
            <p style="color:var(--text-secondary)"><?= htmlspecialchars($product['description']) ?></p>
          </div>

          <div class="section">
            <h3 style="margin:.5rem 0">Features</h3>
            <div class="features">
              <?php foreach($product['features'] as $f): ?>
                <span class="feature-chip"><?= $f ?></span>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>

      <!-- Reviews -->
      <div class="card">
        <div class="card-body">
          <h3 style="margin-top:0">Customer Reviews</h3>
          <div class="review-box">
            <label class="opt" style="margin-bottom:.5rem">Write a Review</label>
            <textarea class="textarea" placeholder="Share your experience with this product..."></textarea>
            <button class="btn btn-primary submit"><i class="fa fa-paper-plane"></i> Submit Review</button>
          </div>

          <div class="section">
            <div class="meta"><strong>John Smith</strong> · <span>2024-07-15</span></div>
            <div class="star" aria-hidden="true"><?php for($i=0;$i<5;$i++) echo '<i class="fa fa-star star"></i>'; ?></div>
            <p style="color:var(--text-secondary)">Excellent quality basketball! Great grip and feels professional.</p>
            <hr style="border:none;border-top:1px solid var(--border-color);margin:1rem 0">
            <div class="meta"><strong>Sarah Johnson</strong> · <span>2024-09-19</span></div>
            <div><?php for($i=0;$i<4;$i++) echo '<i class="fa fa-star star"></i>'; ?><i class="fa-regular fa-star star"></i></div>
            <p style="color:var(--text-secondary)">Good quality but slightly expensive. Overall satisfied with the purchase.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
const thumbs = document.getElementById('thumbs');
thumbs?.addEventListener('click', e=>{
  if(e.target.tagName.toLowerCase()==='img'){
    document.querySelectorAll('#thumbs img').forEach(im=>im.classList.remove('active'));
    e.target.classList.add('active');
    document.getElementById('mainImg').src = e.target.dataset.img;
  }
});
document.getElementById('minus').onclick=()=>{const q=document.getElementById('qty');q.value=Math.max(1,parseInt(q.value||1)-1);};
document.getElementById('plus').onclick =()=>{const q=document.getElementById('qty');q.value=parseInt(q.value||1)+1;};
document.getElementById('addCart').onclick=()=>alert('Added to cart!');
document.getElementById('buyNow').onclick=()=>{
  const q=document.getElementById('qty').value;
  const id="<?= $product['id'] ?>";
  const name="<?= urlencode($product['name']) ?>";
  const price="<?= $product['price'] ?>";
  location.href=`/payment.php?product_id=${id}&name=${name}&qty=${q}&price=${price}`;
};
</script>
<?php
require $ROOT . '/app/views/components/footer.php';
