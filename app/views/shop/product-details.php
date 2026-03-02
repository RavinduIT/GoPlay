<?php
$title = ($product['name'] ?? 'Product') . ' - GoPlay Sports Shop';
$additionalCSS = ['/public/css/components/cart.css'];
$additionalJS  = ['/public/js/cart-api.js'];
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
/* ── Variables ───────────────────────────────────────────── */
:root {
    --blue:        #2563eb;
    --blue-dark:   #1e40af;
    --blue-light:  #eff6ff;
    --blue-mid:    #dbeafe;
    --gold:        #f59e0b;
    --green:       #10b981;
    --green-bg:    #d1fae5;
    --green-text:  #065f46;
    --red:         #ef4444;
    --red-bg:      #fee2e2;
    --red-text:    #991b1b;
    --amber-bg:    #fef3c7;
    --amber-text:  #92400e;
    --t1:          #0f172a;
    --t2:          #475569;
    --t3:          #94a3b8;
    --border:      #e2e8f0;
    --bg:          #f8fafc;
    --white:       #ffffff;
    --radius:      10px;
    --radius-lg:   16px;
    --shadow-sm:   0 1px 3px rgba(0,0,0,.07);
    --shadow-md:   0 4px 16px rgba(0,0,0,.10);
    --shadow-lg:   0 8px 32px rgba(0,0,0,.13);
    --ease:        cubic-bezier(.4,0,.2,1);
}

* { margin:0; padding:0; box-sizing:border-box; }

body {
    font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    background: var(--bg);
    color: var(--t1);
    line-height: 1.6;
    -webkit-font-smoothing: antialiased;
}

.pd-wrap { max-width: 1200px; margin: 0 auto; padding: 0 1.5rem; }

/* ── Loading / Error ───────────────────────────────────────── */
.pd-loading {
    display: flex; flex-direction: column; align-items: center;
    justify-content: center; min-height: 60vh; gap: 1rem;
    color: var(--t2);
}
.spinner {
    width: 44px; height: 44px;
    border: 3px solid var(--border);
    border-top-color: var(--blue);
    border-radius: 50%;
    animation: spin .8s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

.pd-error {
    display: none; flex-direction: column; align-items: center;
    justify-content: center; min-height: 60vh; gap: 1rem; text-align: center;
}
.pd-error i { font-size: 3rem; color: var(--t3); }
.pd-error h2 { font-size: 1.5rem; font-weight: 700; }
.pd-error p { color: var(--t2); }

/* ── Breadcrumb ─────────────────────────────────────────────── */
.breadcrumb-bar {
    background: var(--white);
    border-bottom: 1px solid var(--border);
    padding: .75rem 0;
}
.breadcrumb-nav {
    display: flex; align-items: center; gap: .4rem;
    font-size: .82rem; color: var(--t2);
}
.breadcrumb-nav a { color: var(--t2); text-decoration: none; transition: color .2s; }
.breadcrumb-nav a:hover { color: var(--blue); }
.breadcrumb-nav .sep { color: var(--t3); font-size: .65rem; }
.breadcrumb-nav .crumb-current { color: var(--t1); font-weight: 600; }

/* ── Product Hero ───────────────────────────────────────────── */
.pd-hero {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2.5rem;
    padding: 2rem 0;
    align-items: start;
}

/* Gallery */
.pd-gallery { position: sticky; top: 1.5rem; }

.gallery-main {
    position: relative;
    background: var(--white);
    border-radius: var(--radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    border: 1px solid var(--border);
    aspect-ratio: 1 / 1;
    cursor: zoom-in;
}
.gallery-main img {
    width: 100%; height: 100%;
    object-fit: cover;
    transition: transform .5s var(--ease);
}
.gallery-main:hover img { transform: scale(1.06); }

.gallery-badge {
    position: absolute; top: .75rem; left: .75rem;
    padding: .25rem .65rem;
    border-radius: 20px;
    font-size: .72rem; font-weight: 700;
    letter-spacing: .3px;
    pointer-events: none;
}
.gallery-badge.sale { background: var(--red); color: #fff; }
.gallery-badge.featured { background: var(--blue); color: #fff; }

.gallery-thumbs {
    display: flex; gap: .6rem; margin-top: .75rem; flex-wrap: wrap;
}
.gallery-thumb {
    width: 68px; height: 68px;
    border-radius: var(--radius);
    overflow: hidden;
    border: 2px solid var(--border);
    cursor: pointer;
    transition: border-color .2s, transform .2s;
    background: var(--white);
    flex-shrink: 0;
}
.gallery-thumb:hover { transform: translateY(-2px); border-color: var(--blue-mid); }
.gallery-thumb.active { border-color: var(--blue); box-shadow: 0 0 0 2px var(--blue-light); }
.gallery-thumb img { width: 100%; height: 100%; object-fit: cover; }

/* Product Info */
.pd-info { display: flex; flex-direction: column; gap: 1.25rem; }

.pd-meta-row {
    display: flex; align-items: center; gap: .75rem; flex-wrap: wrap;
}
.pd-category-pill {
    display: inline-flex; align-items: center; gap: .35rem;
    background: var(--blue-light); color: var(--blue);
    padding: .25rem .75rem; border-radius: 20px;
    font-size: .75rem; font-weight: 700; text-transform: uppercase; letter-spacing: .5px;
}
.pd-brand-pill {
    display: inline-flex; align-items: center; gap: .35rem;
    background: var(--bg); color: var(--t2);
    padding: .25rem .75rem; border-radius: 20px;
    font-size: .75rem; font-weight: 600;
    border: 1px solid var(--border);
}
.pd-sku { font-size: .72rem; color: var(--t3); margin-left: auto; }

.pd-title {
    font-size: 1.85rem; font-weight: 800;
    color: var(--t1); line-height: 1.2;
}

/* Rating row */
.pd-rating-row {
    display: flex; align-items: center; gap: .75rem; flex-wrap: wrap;
}
.stars-row { display: flex; gap: 2px; }
.stars-row i { color: var(--gold); font-size: 1rem; }
.rating-num { font-weight: 700; font-size: .95rem; }
.rating-link {
    color: var(--t2); font-size: .88rem;
    text-decoration: none; border-bottom: 1px dashed var(--border);
    transition: color .2s, border-color .2s;
}
.rating-link:hover { color: var(--blue); border-color: var(--blue); }
.rating-sep { color: var(--border); }
.rating-sales { font-size: .82rem; color: var(--t2); }

/* Short description */
.pd-short-desc { font-size: .95rem; color: var(--t2); line-height: 1.7; }

/* Divider */
.pd-divider { height: 1px; background: var(--border); }

/* Price */
.pd-price-block { display: flex; align-items: baseline; gap: .75rem; flex-wrap: wrap; }
.pd-price { font-size: 2.2rem; font-weight: 800; color: var(--blue); }
.pd-price-orig { font-size: 1.1rem; color: var(--t3); text-decoration: line-through; }
.pd-price-save {
    background: var(--red-bg); color: var(--red);
    font-size: .78rem; font-weight: 700;
    padding: .2rem .55rem; border-radius: 6px;
}

/* Stock */
.pd-stock {
    display: inline-flex; align-items: center; gap: .5rem;
    padding: .4rem .9rem; border-radius: 8px;
    font-size: .85rem; font-weight: 600; width: fit-content;
}
.pd-stock.in-stock  { background: var(--green-bg); color: var(--green-text); }
.pd-stock.low-stock { background: var(--amber-bg); color: var(--amber-text); }
.pd-stock.out-stock { background: var(--red-bg);   color: var(--red-text); }

/* Qty + CTA */
.pd-qty-row { display: flex; align-items: center; gap: 1rem; flex-wrap: wrap; }
.pd-qty-label { font-size: .88rem; font-weight: 600; color: var(--t2); }
.pd-qty-ctrl {
    display: flex; align-items: center;
    border: 1.5px solid var(--border); border-radius: var(--radius);
    overflow: hidden; background: var(--white);
}
.pd-qty-btn {
    background: none; border: none;
    padding: .55rem .85rem;
    font-size: 1rem; cursor: pointer; color: var(--t2);
    transition: background .15s, color .15s;
}
.pd-qty-btn:hover { background: var(--bg); color: var(--blue); }
.pd-qty-input {
    border: none; width: 52px; text-align: center;
    font-size: .95rem; font-weight: 700;
    background: var(--white); color: var(--t1);
    border-left: 1.5px solid var(--border);
    border-right: 1.5px solid var(--border);
    padding: .55rem 0;
}
.pd-qty-input:focus { outline: none; }

.pd-cta-row { display: flex; gap: .75rem; flex-wrap: wrap; }

.btn-cart {
    flex: 1; min-width: 160px;
    padding: .85rem 1.5rem;
    background: var(--blue); color: #fff;
    border: none; border-radius: var(--radius);
    font-size: 1rem; font-weight: 700;
    cursor: pointer; transition: background .2s, transform .15s, box-shadow .2s;
    display: flex; align-items: center; justify-content: center; gap: .5rem;
}
.btn-cart:hover { background: var(--blue-dark); transform: translateY(-1px); box-shadow: 0 4px 12px rgba(37,99,235,.35); }
.btn-cart:active { transform: none; }
.btn-cart:disabled { background: var(--t3); cursor: not-allowed; transform: none; box-shadow: none; }

.btn-back {
    padding: .85rem 1.25rem;
    background: var(--white); color: var(--t2);
    border: 1.5px solid var(--border); border-radius: var(--radius);
    font-size: .95rem; font-weight: 600;
    cursor: pointer; transition: border-color .2s, color .2s;
    display: flex; align-items: center; gap: .4rem;
    text-decoration: none;
}
.btn-back:hover { border-color: var(--blue); color: var(--blue); }

/* Feature badges */
.pd-features {
    display: flex; gap: .6rem; flex-wrap: wrap;
    padding: .85rem; background: var(--bg);
    border-radius: var(--radius); border: 1px solid var(--border);
}
.pd-feature-item {
    display: flex; align-items: center; gap: .4rem;
    font-size: .78rem; color: var(--t2); font-weight: 500;
}
.pd-feature-item i { color: var(--green); font-size: .75rem; }
.pd-feature-sep { color: var(--border); }

/* ── Sold By Card ───────────────────────────────────────────── */
.sold-by-card {
    display: none;
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    padding: 1.25rem 1.5rem;
    margin: .5rem 0 1.5rem;
    align-items: center; gap: 1.25rem; flex-wrap: wrap;
    box-shadow: var(--shadow-sm);
}
.sold-by-card.visible { display: flex; }

.sb-logo {
    width: 56px; height: 56px;
    border-radius: 12px;
    background: var(--blue-light);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.6rem; color: var(--blue);
    flex-shrink: 0; overflow: hidden;
    border: 1px solid var(--blue-mid);
}
.sb-logo img { width: 100%; height: 100%; object-fit: cover; border-radius: 11px; }
.sb-logo i { display: flex; }

.sb-body { flex: 1; min-width: 180px; }
.sb-eyebrow {
    font-size: .7rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: 1px; color: var(--t3); margin-bottom: .2rem;
}
.sb-name { font-size: 1.05rem; font-weight: 700; color: var(--t1); margin-bottom: .25rem; }
.sb-meta {
    display: flex; flex-wrap: wrap; gap: .85rem;
    font-size: .8rem; color: var(--t2);
}
.sb-meta span { display: flex; align-items: center; gap: .3rem; }
.sb-meta i { color: var(--blue); }
.sb-stars { color: var(--gold); }
.sb-desc { font-size: .82rem; color: var(--t2); margin-top: .4rem; line-height: 1.5; }

.sb-socials { display: flex; gap: .4rem; }
.sb-social {
    width: 30px; height: 30px; border-radius: 50%;
    background: var(--bg); border: 1px solid var(--border);
    display: flex; align-items: center; justify-content: center;
    color: var(--t2); font-size: .8rem; text-decoration: none;
    transition: background .2s, color .2s;
}
.sb-social:hover { background: var(--blue); color: #fff; border-color: var(--blue); }

.sb-visit {
    padding: .6rem 1.1rem;
    background: var(--blue); color: #fff;
    border: none; border-radius: var(--radius);
    font-size: .85rem; font-weight: 700;
    cursor: pointer; transition: background .2s;
    display: flex; align-items: center; gap: .4rem;
    text-decoration: none; white-space: nowrap;
}
.sb-visit:hover { background: var(--blue-dark); }

/* ── Tabs ───────────────────────────────────────────────────── */
.pd-tabs-wrap {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    margin-bottom: 2rem;
}
.pd-tab-bar {
    display: flex;
    border-bottom: 1px solid var(--border);
    background: var(--bg);
    overflow-x: auto;
}
.pd-tab-btn {
    padding: .9rem 1.5rem;
    background: none; border: none;
    border-bottom: 3px solid transparent;
    margin-bottom: -1px;
    font-size: .88rem; font-weight: 600;
    color: var(--t2); cursor: pointer;
    transition: color .2s, border-color .2s;
    display: flex; align-items: center; gap: .4rem;
    white-space: nowrap;
}
.pd-tab-btn .tab-count {
    background: var(--border); color: var(--t2);
    font-size: .7rem; font-weight: 700;
    padding: .05rem .45rem; border-radius: 10px;
    transition: background .2s, color .2s;
}
.pd-tab-btn:hover { color: var(--blue); }
.pd-tab-btn.active {
    color: var(--blue);
    border-bottom-color: var(--blue);
    background: var(--white);
}
.pd-tab-btn.active .tab-count { background: var(--blue); color: #fff; }

.pd-tab-pane { display: none; padding: 1.75rem 2rem; }
.pd-tab-pane.active { display: block; }

/* Description */
.pd-description { font-size: .95rem; color: var(--t2); line-height: 1.8; }
.pd-description p { margin-bottom: .75rem; }

/* Specifications */
.pd-specs-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    gap: .6rem;
}
.pd-spec-item {
    display: flex; justify-content: space-between; align-items: center;
    padding: .65rem .85rem;
    background: var(--bg); border-radius: 8px;
    border: 1px solid var(--border); font-size: .88rem;
}
.pd-spec-label { color: var(--t2); font-weight: 500; }
.pd-spec-value { font-weight: 700; color: var(--t1); text-align: right; max-width: 55%; }

.pd-features-section { margin-top: 1.5rem; }
.pd-features-section h4 {
    font-size: .82rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: .7px; color: var(--t3); margin-bottom: .75rem;
}
.pd-features-list {
    display: flex; flex-wrap: wrap; gap: .5rem; list-style: none;
}
.pd-features-list li {
    display: flex; align-items: center; gap: .4rem;
    background: var(--green-bg); color: var(--green-text);
    padding: .35rem .8rem; border-radius: 6px;
    font-size: .82rem; font-weight: 600;
}
.pd-features-list li i { font-size: .72rem; }

/* Reviews */
.reviews-header {
    display: flex; gap: 2rem; flex-wrap: wrap;
    padding-bottom: 1.5rem;
    margin-bottom: 1.5rem;
    border-bottom: 1px solid var(--border);
}
.reviews-score { text-align: center; min-width: 100px; }
.reviews-score .big-num {
    font-size: 3.5rem; font-weight: 800; color: var(--t1); line-height: 1;
}
.reviews-score .big-stars { color: var(--gold); font-size: 1rem; margin: .35rem 0; }
.reviews-score .big-count { font-size: .8rem; color: var(--t2); }

.reviews-bars { flex: 1; min-width: 200px; }
.bar-row {
    display: flex; align-items: center; gap: .6rem;
    margin-bottom: .4rem; font-size: .8rem;
}
.bar-label { width: 30px; text-align: right; color: var(--t2); font-weight: 600; }
.bar-track {
    flex: 1; height: 8px;
    background: var(--bg); border-radius: 10px;
    overflow: hidden; border: 1px solid var(--border);
}
.bar-fill {
    height: 100%; border-radius: 10px;
    background: var(--gold);
    transition: width .6s var(--ease);
}
.bar-count { width: 28px; color: var(--t3); }

.review-note {
    display: flex; align-items: center; gap: .65rem;
    background: var(--blue-light); border-radius: var(--radius);
    padding: .75rem 1rem; margin-bottom: 1.25rem;
    font-size: .85rem; color: var(--t2);
}
.review-note i { color: var(--blue); flex-shrink: 0; }
.review-note a { color: var(--blue); font-weight: 700; text-decoration: none; }
.review-note a:hover { text-decoration: underline; }

.review-card { padding: 1.25rem 0; border-bottom: 1px solid var(--border); }
.review-card:last-child { border-bottom: none; }
.review-card-top {
    display: flex; justify-content: space-between; align-items: flex-start;
    margin-bottom: .5rem; flex-wrap: wrap; gap: .5rem;
}
.reviewer-info { display: flex; align-items: center; gap: .65rem; }
.reviewer-avatar {
    width: 36px; height: 36px; border-radius: 50%;
    background: var(--blue-mid); color: var(--blue);
    display: flex; align-items: center; justify-content: center;
    font-size: .85rem; font-weight: 800; flex-shrink: 0;
}
.reviewer-name { font-weight: 700; font-size: .9rem; }
.review-stars { color: var(--gold); font-size: .8rem; margin-top: .1rem; }
.review-date { font-size: .78rem; color: var(--t3); }
.review-title { font-weight: 700; font-size: .92rem; margin-bottom: .25rem; }
.review-body { font-size: .88rem; color: var(--t2); line-height: 1.6; }
.verified-badge {
    display: inline-flex; align-items: center; gap: .3rem;
    background: var(--green-bg); color: var(--green-text);
    font-size: .72rem; font-weight: 700;
    padding: .15rem .5rem; border-radius: 5px; margin-top: .5rem;
}

.reviews-empty { text-align: center; padding: 2rem; color: var(--t2); }
.reviews-empty i { font-size: 2.5rem; color: var(--border); display: block; margin-bottom: .75rem; }

/* ── Related Products ───────────────────────────────────────── */
.pd-related { margin-bottom: 3rem; }
.pd-section-header {
    display: flex; align-items: center; gap: .65rem;
    margin-bottom: 1.25rem;
}
.pd-section-header h3 {
    font-size: 1.2rem; font-weight: 800; color: var(--t1);
}
.pd-section-line { flex: 1; height: 1px; background: var(--border); }

.related-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 1rem;
}
.related-card {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    overflow: hidden;
    cursor: pointer;
    transition: transform .2s, box-shadow .2s, border-color .2s;
}
.related-card:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-md);
    border-color: var(--blue-mid);
}
.related-img {
    height: 150px; overflow: hidden;
    background: var(--bg);
}
.related-img img {
    width: 100%; height: 100%; object-fit: cover;
    transition: transform .4s var(--ease);
}
.related-card:hover .related-img img { transform: scale(1.07); }
.related-body { padding: .85rem; }
.related-cat { font-size: .7rem; font-weight: 700; text-transform: uppercase; letter-spacing: .4px; color: var(--blue); margin-bottom: .2rem; }
.related-name { font-size: .9rem; font-weight: 700; color: var(--t1); line-height: 1.3; margin-bottom: .3rem; }
.related-price { font-size: .95rem; font-weight: 800; color: var(--blue); }
.related-orig { font-size: .78rem; color: var(--t3); text-decoration: line-through; margin-left: .3rem; }

/* ── Responsive ─────────────────────────────────────────────── */
@media (max-width: 900px) {
    .pd-hero { grid-template-columns: 1fr; gap: 1.5rem; }
    .pd-gallery { position: static; }
    .gallery-main { aspect-ratio: 4/3; }
}
@media (max-width: 600px) {
    .pd-wrap { padding: 0 1rem; }
    .pd-title { font-size: 1.45rem; }
    .pd-price { font-size: 1.75rem; }
    .pd-tab-pane { padding: 1.25rem; }
    .reviews-header { flex-direction: column; }
}
</style>

<!-- Breadcrumb -->
<div class="breadcrumb-bar">
    <div class="pd-wrap">
        <nav class="breadcrumb-nav">
            <a href="/">Home</a>
            <span class="sep"><i class="fas fa-chevron-right" style="font-size:.6rem;"></i></span>
            <a href="/shop">Shop</a>
            <span class="sep"><i class="fas fa-chevron-right" style="font-size:.6rem;"></i></span>
            <span id="bc-category">Category</span>
            <span class="sep"><i class="fas fa-chevron-right" style="font-size:.6rem;"></i></span>
            <span class="crumb-current" id="bc-name">Product</span>
        </nav>
    </div>
</div>

<!-- Loading -->
<div id="pd-loading" class="pd-wrap pd-loading">
    <div class="spinner"></div>
    <p>Loading product…</p>
</div>

<!-- Error -->
<div id="pd-error" class="pd-wrap pd-error">
    <i class="fas fa-box-open"></i>
    <h2>Product Not Found</h2>
    <p>This product doesn't exist or has been removed.</p>
    <a href="/shop" style="margin-top:.5rem;padding:.75rem 1.5rem;background:var(--blue);color:#fff;border-radius:var(--radius);text-decoration:none;font-weight:700;display:inline-flex;align-items:center;gap:.4rem;">
        <i class="fas fa-store"></i> Browse Shop
    </a>
</div>

<!-- Main content (hidden until loaded) -->
<div id="pd-main" style="display:none;">
    <div class="pd-wrap">

        <!-- ── Product Hero ── -->
        <div class="pd-hero">

            <!-- Gallery -->
            <div class="pd-gallery">
                <div class="gallery-main" onclick="openLightbox()">
                    <img id="main-img" src="" alt="Product Image">
                    <span id="gallery-badge" class="gallery-badge" style="display:none;"></span>
                </div>
                <div class="gallery-thumbs" id="gallery-thumbs"></div>
            </div>

            <!-- Info -->
            <div class="pd-info">
                <!-- Meta row -->
                <div class="pd-meta-row">
                    <span class="pd-category-pill" id="info-category">
                        <i class="fas fa-tag"></i> <span id="info-category-text">Category</span>
                    </span>
                    <span class="pd-brand-pill" id="info-brand" style="display:none;">
                        <i class="fas fa-certificate"></i> <span id="info-brand-text"></span>
                    </span>
                    <span class="pd-sku" id="info-sku"></span>
                </div>

                <h1 class="pd-title" id="info-title">Product Name</h1>

                <!-- Rating -->
                <div class="pd-rating-row">
                    <div class="stars-row" id="info-stars"></div>
                    <span class="rating-num" id="info-rating-num">0</span>
                    <span class="rating-sep">·</span>
                    <a href="#" class="rating-link" id="info-reviews-link"
                       onclick="switchTab('reviews',document.querySelector('[data-tab=reviews]'));return false;">
                        0 reviews
                    </a>
                    <span class="rating-sep" id="sales-sep" style="display:none;">·</span>
                    <span class="rating-sales" id="info-sales" style="display:none;"></span>
                </div>

                <!-- Short description -->
                <p class="pd-short-desc" id="info-short-desc" style="display:none;"></p>

                <div class="pd-divider"></div>

                <!-- Price -->
                <div class="pd-price-block">
                    <span class="pd-price" id="info-price">LKR 0</span>
                    <span class="pd-price-orig" id="info-orig" style="display:none;"></span>
                    <span class="pd-price-save" id="info-save" style="display:none;"></span>
                </div>

                <!-- Stock -->
                <div id="info-stock" class="pd-stock in-stock">
                    <i class="fas fa-check-circle"></i>
                    <span id="info-stock-text">In Stock</span>
                </div>

                <!-- Qty + CTA -->
                <div class="pd-qty-row">
                    <span class="pd-qty-label">Qty:</span>
                    <div class="pd-qty-ctrl">
                        <button class="pd-qty-btn" onclick="changeQty(-1)"><i class="fas fa-minus"></i></button>
                        <input type="number" id="qty-input" class="pd-qty-input" value="1" min="1" max="99">
                        <button class="pd-qty-btn" onclick="changeQty(1)"><i class="fas fa-plus"></i></button>
                    </div>
                </div>

                <div class="pd-cta-row">
                    <button id="btn-add-cart" class="btn-cart" onclick="doAddToCart()">
                        <i class="fas fa-cart-plus"></i> Add to Cart
                    </button>
                    <a href="/shop" class="btn-back">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>

                <!-- Features -->
                <div class="pd-features">
                    <span class="pd-feature-item"><i class="fas fa-truck"></i> Free delivery over LKR 5,000</span>
                    <span class="pd-feature-sep">|</span>
                    <span class="pd-feature-item"><i class="fas fa-undo"></i> 30-day returns</span>
                    <span class="pd-feature-sep">|</span>
                    <span class="pd-feature-item"><i class="fas fa-shield-alt"></i> Secure checkout</span>
                </div>
            </div>
        </div><!-- /.pd-hero -->

        <!-- ── Sold By Card ── -->
        <div id="sold-by-card" class="sold-by-card">
            <div class="sb-logo" id="sb-logo"><i class="fas fa-store"></i></div>
            <div class="sb-body">
                <div class="sb-eyebrow">Sold By</div>
                <div class="sb-name" id="sb-name"></div>
                <div class="sb-meta" id="sb-meta"></div>
                <div class="sb-desc" id="sb-desc" style="display:none;"></div>
            </div>
            <div class="sb-socials" id="sb-socials"></div>
            <button id="sb-visit-btn" class="sb-visit" style="display:none;">
                <i class="fas fa-store"></i> Visit Store
            </button>
        </div>

        <!-- ── Tabs ── -->
        <div class="pd-tabs-wrap">
            <div class="pd-tab-bar">
                <button class="pd-tab-btn active" data-tab="description"
                        onclick="switchTab('description',this)">
                    <i class="fas fa-align-left"></i> Description
                </button>
                <button class="pd-tab-btn" data-tab="specifications"
                        onclick="switchTab('specifications',this)">
                    <i class="fas fa-list-ul"></i> Specifications
                </button>
                <button class="pd-tab-btn" data-tab="reviews"
                        onclick="switchTab('reviews',this)">
                    <i class="fas fa-star"></i> Reviews
                    <span class="tab-count" id="tab-review-count">0</span>
                </button>
            </div>

            <!-- Description -->
            <div id="tab-description" class="pd-tab-pane active">
                <div id="info-description" class="pd-description">Loading…</div>
            </div>

            <!-- Specifications -->
            <div id="tab-specifications" class="pd-tab-pane">
                <div id="info-specs" class="pd-specs-grid"></div>
                <div id="info-features-wrap" class="pd-features-section" style="display:none;">
                    <h4><i class="fas fa-check-circle" style="color:var(--green);margin-right:.35rem;"></i>Features</h4>
                    <ul id="info-features" class="pd-features-list"></ul>
                </div>
            </div>

            <!-- Reviews -->
            <div id="tab-reviews" class="pd-tab-pane">
                <div class="reviews-header" id="reviews-header"></div>
                <div class="review-note">
                    <i class="fas fa-info-circle"></i>
                    <span>Purchased this product? Leave a review from <a href="/my-orders">My Orders</a></span>
                </div>
                <div id="reviews-list"></div>
            </div>
        </div>

        <!-- ── Related Products ── -->
        <div class="pd-related" id="related-wrap" style="display:none;">
            <div class="pd-section-header">
                <h3>You May Also Like</h3>
                <div class="pd-section-line"></div>
            </div>
            <div class="related-grid" id="related-grid"></div>
        </div>

    </div><!-- /.pd-wrap -->
</div><!-- /#pd-main -->

<script>
/* ── PHP data ─────────────────────────────────────────────── */
const productData    = <?php echo json_encode($product       ?? null, JSON_HEX_TAG | JSON_INVALID_UTF8_SUBSTITUTE); ?>;
const relatedData    = <?php echo json_encode($relatedProducts ?? [], JSON_HEX_TAG | JSON_INVALID_UTF8_SUBSTITUTE); ?>;
const reviewsData    = <?php echo json_encode($reviews        ?? [], JSON_HEX_TAG | JSON_INVALID_UTF8_SUBSTITUTE); ?>;

/* ── Init ─────────────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', () => {
    if (!productData) { showError(); return; }
    renderProduct(productData);
    renderReviews(reviewsData);
    renderRelated(relatedData);
    showPage();
});

/* ── Helpers ──────────────────────────────────────────────── */
function esc(str) {
    const d = document.createElement('div');
    d.textContent = str || '';
    return d.innerHTML;
}

function stars(rating, size = '1rem') {
    const full = Math.floor(rating || 0);
    const half = (rating || 0) % 1 >= .5;
    let h = '';
    for (let i = 0; i < full; i++) h += `<i class="fas fa-star" style="font-size:${size};color:var(--gold);"></i>`;
    if (half) h += `<i class="fas fa-star-half-alt" style="font-size:${size};color:var(--gold);"></i>`;
    for (let i = 0; i < 5 - full - (half?1:0); i++) h += `<i class="far fa-star" style="font-size:${size};color:var(--border);"></i>`;
    return h;
}

function productImage(p) {
    if (p.images && Array.isArray(p.images) && p.images.length > 0) {
        const img = p.images[0];
        if (img.startsWith('http') || img.startsWith('/')) return img;
        return '/public/uploads/products/' + img;
    }
    const catMap = {
        football:   '/public/assets/images/products/football.jpg',
        cricket:    '/public/assets/images/products/cricket-bat.jpg',
        tennis:     '/public/assets/images/products/tennis-racket.jpg',
        basketball: '/public/assets/images/products/basketball.jpg',
        badminton:  '/public/assets/images/products/badminton-racket.jpg',
    };
    return catMap[(p.category_slug||'').toLowerCase()] || '/public/assets/images/products/football.jpg';
}

function allImages(p) {
    if (p.images && Array.isArray(p.images) && p.images.length > 0) {
        return p.images.map(img => {
            if (img.startsWith('http') || img.startsWith('/')) return img;
            return '/public/uploads/products/' + img;
        });
    }
    return [productImage(p)];
}

function fmtDate(str) {
    if (!str) return '';
    return new Date(str).toLocaleDateString('en-US', { year:'numeric', month:'short', day:'numeric' });
}

/* ── Render product ───────────────────────────────────────── */
function renderProduct(p) {
    // Breadcrumb & title
    document.title = p.name + ' - GoPlay Sports Shop';
    document.getElementById('bc-name').textContent = p.name;
    document.getElementById('bc-category').textContent = p.category_name || 'Products';

    // Category / brand / SKU
    document.getElementById('info-category-text').textContent = (p.category_name || 'Product').toUpperCase();
    if (p.brand) {
        document.getElementById('info-brand-text').textContent = p.brand;
        document.getElementById('info-brand').style.display = 'inline-flex';
    }
    if (p.sku) document.getElementById('info-sku').textContent = 'SKU: ' + p.sku;

    // Title
    document.getElementById('info-title').textContent = p.name;

    // Rating
    const rating = parseFloat(p.rating) || 0;
    document.getElementById('info-stars').innerHTML = stars(rating);
    document.getElementById('info-rating-num').textContent = rating.toFixed(1);
    const revCount = parseInt(p.total_reviews) || 0;
    document.getElementById('info-reviews-link').textContent = revCount + ' review' + (revCount !== 1 ? 's' : '');

    if (p.total_sales && parseInt(p.total_sales) > 0) {
        document.getElementById('sales-sep').style.display = '';
        const s = document.getElementById('info-sales');
        s.textContent = parseInt(p.total_sales).toLocaleString() + ' sold';
        s.style.display = '';
    }

    // Short description
    if (p.short_description) {
        const el = document.getElementById('info-short-desc');
        el.textContent = p.short_description;
        el.style.display = '';
    }

    // Price
    const price = parseFloat(p.price);
    const origPrice = parseFloat(p.original_price || p.compare_price || 0);
    document.getElementById('info-price').textContent = 'LKR ' + price.toLocaleString();
    if (origPrice > price) {
        document.getElementById('info-orig').textContent = 'LKR ' + origPrice.toLocaleString();
        document.getElementById('info-orig').style.display = '';
        const pct = Math.round(((origPrice - price) / origPrice) * 100);
        document.getElementById('info-save').textContent = pct + '% OFF';
        document.getElementById('info-save').style.display = '';
    }

    // Stock
    const qty = parseInt(p.stock_quantity || 0);
    const stockEl = document.getElementById('info-stock');
    const stockTxt = document.getElementById('info-stock-text');
    const cartBtn = document.getElementById('btn-add-cart');
    stockEl.className = 'pd-stock';
    if (qty <= 0) {
        stockEl.classList.add('out-stock');
        stockTxt.innerHTML = '<i class="fas fa-times-circle"></i> Out of Stock';
        cartBtn.disabled = true;
        cartBtn.innerHTML = '<i class="fas fa-ban"></i> Out of Stock';
    } else if (qty <= 5) {
        stockEl.classList.add('low-stock');
        stockTxt.innerHTML = `<i class="fas fa-exclamation-triangle"></i> Only ${qty} left`;
    } else {
        stockEl.classList.add('in-stock');
        stockTxt.innerHTML = '<i class="fas fa-check-circle"></i> In Stock';
    }
    document.getElementById('qty-input').max = Math.min(qty, 99);

    // Gallery badge
    const badge = document.getElementById('gallery-badge');
    if (origPrice > price) {
        const pct = Math.round(((origPrice - price) / origPrice) * 100);
        badge.textContent = pct + '% OFF';
        badge.className = 'gallery-badge sale';
        badge.style.display = '';
    } else if (p.is_featured) {
        badge.textContent = 'Featured';
        badge.className = 'gallery-badge featured';
        badge.style.display = '';
    }

    // Gallery images
    const imgs = allImages(p);
    document.getElementById('main-img').src = imgs[0];
    document.getElementById('main-img').alt = p.name;
    window._productImages = imgs;

    const thumbWrap = document.getElementById('gallery-thumbs');
    if (imgs.length > 1) {
        thumbWrap.innerHTML = imgs.map((src, i) => `
            <div class="gallery-thumb ${i===0?'active':''}" onclick="setMainImg('${src}',${i})">
                <img src="${src}" alt="View ${i+1}" loading="lazy"
                     onerror="this.src='/public/assets/images/products/football.jpg'">
            </div>`).join('');
    }

    // Description
    document.getElementById('info-description').innerHTML =
        p.description ? p.description.replace(/\n\n/g,'</p><p>').replace(/\n/g,'<br>') : '<p style="color:var(--t3);">No description available.</p>';

    // Specifications
    renderSpecs(p);

    // Sold By
    renderSoldBy(p);
}

/* ── Gallery ──────────────────────────────────────────────── */
function setMainImg(src, idx) {
    document.getElementById('main-img').src = src;
    document.querySelectorAll('.gallery-thumb').forEach((t, i) => t.classList.toggle('active', i === idx));
}

function openLightbox() { /* simple: open image in new tab */ window.open(document.getElementById('main-img').src, '_blank'); }

/* ── Specifications ───────────────────────────────────────── */
function renderSpecs(p) {
    const specsEl   = document.getElementById('info-specs');
    const featsWrap = document.getElementById('info-features-wrap');
    const featsEl   = document.getElementById('info-features');

    // Build spec list: start with DB specifications JSON, then fill gaps with known fields
    let specItems = [];

    // 1. Use product.specifications if it's a non-empty object/array
    if (p.specifications && typeof p.specifications === 'object' && !Array.isArray(p.specifications)) {
        Object.entries(p.specifications).forEach(([k,v]) => {
            if (v !== null && v !== '') specItems.push({ label: capitalize(k.replace(/_/g,' ')), value: String(v) });
        });
    } else if (Array.isArray(p.specifications)) {
        p.specifications.forEach(s => {
            if (s.label && s.value) specItems.push({ label: s.label, value: String(s.value) });
        });
    }

    // 2. Always include core fields if not already covered
    const coreSpecs = [
        { label: 'SKU',      value: p.sku            || null },
        { label: 'Brand',    value: p.brand          || null },
        { label: 'Category', value: p.category_name  || null },
        { label: 'Weight',   value: p.weight ? p.weight + ' kg' : null },
        { label: 'Stock',    value: p.stock_quantity !== undefined ? parseInt(p.stock_quantity) + ' units' : null },
    ];
    const existingLabels = new Set(specItems.map(s => s.label.toLowerCase()));
    coreSpecs.forEach(s => {
        if (s.value && !existingLabels.has(s.label.toLowerCase())) specItems.push(s);
    });

    specsEl.innerHTML = specItems.length
        ? specItems.map(s => `
            <div class="pd-spec-item">
                <span class="pd-spec-label">${esc(s.label)}</span>
                <span class="pd-spec-value">${esc(s.value)}</span>
            </div>`).join('')
        : '<p style="color:var(--t3);font-size:.9rem;">No specifications available.</p>';

    // 3. Features list
    let features = [];
    if (Array.isArray(p.features) && p.features.length > 0) {
        features = p.features.filter(Boolean);
    }
    if (features.length > 0) {
        featsEl.innerHTML = features.map(f => `
            <li><i class="fas fa-check-circle"></i> ${esc(String(f))}</li>`).join('');
        featsWrap.style.display = '';
    }
}

function capitalize(str) {
    return str.replace(/\b\w/g, c => c.toUpperCase());
}

/* ── Sold By ──────────────────────────────────────────────── */
function renderSoldBy(p) {
    const card = document.getElementById('sold-by-card');
    if (!p.shop_owner_id && !p.shop_name && !p.business_name) return;

    card.classList.add('visible');

    // Logo
    const logoEl = document.getElementById('sb-logo');
    if (p.shop_logo) {
        logoEl.innerHTML = `<img src="${p.shop_logo}" alt="Shop" onerror="this.style.display='none';this.parentElement.querySelector('i').style.display='flex';">
                            <i class="fas fa-store" style="display:none;"></i>`;
    }

    // Name
    const ownerName = [p.owner_first_name, p.owner_last_name].filter(Boolean).join(' ');
    const shopName = p.shop_name || p.business_name || ownerName || 'GoPlay Store';
    document.getElementById('sb-name').textContent = shopName;

    // Meta
    let meta = '';
    if (p.average_rating && parseFloat(p.average_rating) > 0) {
        meta += `<span><span class="sb-stars">${stars(p.average_rating, '.78rem')}</span> ${parseFloat(p.average_rating).toFixed(1)} (${p.total_reviews||0})</span>`;
    }
    if (p.shop_city) {
        meta += `<span><i class="fas fa-map-marker-alt"></i> ${esc([p.shop_city, p.shop_state].filter(Boolean).join(', '))}</span>`;
    }
    if (p.business_phone) meta += `<span><i class="fas fa-phone"></i> ${esc(p.business_phone)}</span>`;
    if (p.business_email) meta += `<span><i class="fas fa-envelope"></i> ${esc(p.business_email)}</span>`;
    document.getElementById('sb-meta').innerHTML = meta;

    // Description
    if (p.business_description) {
        const d = document.getElementById('sb-desc');
        d.textContent = p.business_description.substring(0, 140) + (p.business_description.length > 140 ? '…' : '');
        d.style.display = '';
    }

    // Socials
    let socials = '';
    if (p.facebook)    socials += `<a href="${p.facebook}"    class="sb-social" target="_blank" title="Facebook"><i class="fab fa-facebook-f"></i></a>`;
    if (p.instagram)   socials += `<a href="${p.instagram}"   class="sb-social" target="_blank" title="Instagram"><i class="fab fa-instagram"></i></a>`;
    if (p.twitter)     socials += `<a href="${p.twitter}"     class="sb-social" target="_blank" title="Twitter"><i class="fab fa-twitter"></i></a>`;
    if (p.website_url) socials += `<a href="${p.website_url}" class="sb-social" target="_blank" title="Website"><i class="fas fa-globe"></i></a>`;
    if (socials) document.getElementById('sb-socials').innerHTML = socials;

    // Visit btn
    if (p.shop_owner_id) {
        const btn = document.getElementById('sb-visit-btn');
        btn.style.display = 'flex';
        btn.onclick = () => window.location.href = `/store/${p.shop_owner_id}`;
    }
}

/* ── Reviews ──────────────────────────────────────────────── */
function renderReviews(reviews) {
    const count = reviews ? reviews.length : 0;
    document.getElementById('tab-review-count').textContent = count;
    document.getElementById('info-reviews-link').textContent = count + ' review' + (count !== 1 ? 's' : '');

    // Rating breakdown header
    const headerEl = document.getElementById('reviews-header');
    if (count > 0) {
        const avg = reviews.reduce((s, r) => s + parseInt(r.rating||0), 0) / count;
        const dist = [5,4,3,2,1].map(star => ({
            star,
            cnt: reviews.filter(r => parseInt(r.rating) === star).length
        }));
        headerEl.innerHTML = `
            <div class="reviews-score">
                <div class="big-num">${avg.toFixed(1)}</div>
                <div class="big-stars">${stars(avg)}</div>
                <div class="big-count">${count} review${count !== 1 ? 's' : ''}</div>
            </div>
            <div class="reviews-bars">
                ${dist.map(d => `
                    <div class="bar-row">
                        <span class="bar-label">${d.star}★</span>
                        <div class="bar-track">
                            <div class="bar-fill" style="width:${count ? Math.round(d.cnt/count*100) : 0}%"></div>
                        </div>
                        <span class="bar-count">${d.cnt}</span>
                    </div>`).join('')}
            </div>`;
    } else {
        headerEl.style.display = 'none';
    }

    // Reviews list
    const listEl = document.getElementById('reviews-list');
    if (!count) {
        listEl.innerHTML = `<div class="reviews-empty"><i class="far fa-comment-dots"></i><p>No reviews yet. Be the first!</p></div>`;
        return;
    }

    listEl.innerHTML = reviews.map(r => {
        const initials = (r.user_name || 'A').split(' ').map(n => n[0]).join('').substring(0,2).toUpperCase();
        return `
            <div class="review-card">
                <div class="review-card-top">
                    <div class="reviewer-info">
                        <div class="reviewer-avatar">${initials}</div>
                        <div>
                            <div class="reviewer-name">${esc(r.user_name || 'Anonymous')}</div>
                            <div class="review-stars">${stars(parseInt(r.rating||0), '.82rem')}</div>
                        </div>
                    </div>
                    <div class="review-date">${fmtDate(r.created_at)}</div>
                </div>
                ${r.title ? `<div class="review-title">${esc(r.title)}</div>` : ''}
                ${r.review_text ? `<div class="review-body">${esc(r.review_text).replace(/\n/g,'<br>')}</div>` : ''}
                ${r.is_verified_purchase ? `<span class="verified-badge"><i class="fas fa-check-circle"></i> Verified Purchase</span>` : ''}
            </div>`;
    }).join('');
}

/* ── Related products ─────────────────────────────────────── */
function renderRelated(related) {
    if (!related || !related.length) return;
    document.getElementById('related-wrap').style.display = '';

    document.getElementById('related-grid').innerHTML = related.map(p => {
        const img = productImage(p);
        const price = parseFloat(p.price);
        const orig  = parseFloat(p.compare_price || p.original_price || 0);
        return `
            <div class="related-card" onclick="location.href='/product/${p.id}'">
                <div class="related-img">
                    <img src="${img}" alt="${esc(p.name)}" loading="lazy"
                         onerror="this.src='/public/assets/images/products/football.jpg'">
                </div>
                <div class="related-body">
                    <div class="related-cat">${esc(p.category_name || '')}</div>
                    <div class="related-name">${esc(p.name)}</div>
                    <div>
                        <span class="related-price">LKR ${price.toLocaleString()}</span>
                        ${orig > price ? `<span class="related-orig">LKR ${orig.toLocaleString()}</span>` : ''}
                    </div>
                </div>
            </div>`;
    }).join('');
}

/* ── Tabs ─────────────────────────────────────────────────── */
function switchTab(name, btn) {
    document.querySelectorAll('.pd-tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.pd-tab-pane').forEach(p => p.classList.remove('active'));
    document.getElementById('tab-' + name).classList.add('active');
    if (btn) btn.classList.add('active');
}

/* ── Quantity ─────────────────────────────────────────────── */
function changeQty(delta) {
    const inp = document.getElementById('qty-input');
    inp.value = Math.max(1, Math.min(parseInt(inp.max) || 99, parseInt(inp.value) + delta));
}

/* ── Add to cart ──────────────────────────────────────────── */
function doAddToCart() {
    if (!productData) return;
    const qty = parseInt(document.getElementById('qty-input').value) || 1;
    addToCart(productData.id, qty);
}

/* ── Show / hide states ───────────────────────────────────── */
function showPage() {
    document.getElementById('pd-loading').style.display = 'none';
    document.getElementById('pd-main').style.display = '';
}
function showError() {
    document.getElementById('pd-loading').style.display = 'none';
    document.getElementById('pd-error').style.display = 'flex';
}
</script>
