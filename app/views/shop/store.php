<?php
$shopName = $shopOwner['shop_name'] ?? $shopOwner['business_name'] ?? $shopOwner['owner_name'] ?? 'Store';
$title    = $shopName . ' - GoPlay Sports';
$additionalCSS = ['/public/css/components/cart.css'];
$additionalJS  = ['/public/js/cart-api.js'];
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
/* ── Store Page ───────────────────────────────────────────── */
:root {
    --primary: #2563eb;
    --primary-dark: #1e40af;
    --primary-light: #eff6ff;
    --accent: #ffc107;
    --success: #10b981;
    --text-primary: #1e293b;
    --text-secondary: #64748b;
    --text-light: #94a3b8;
    --bg-light: #f8fafc;
    --bg-white: #ffffff;
    --border: #e2e8f0;
    --shadow-sm: 0 1px 3px rgba(0,0,0,.08);
    --shadow-md: 0 4px 16px rgba(0,0,0,.10);
    --radius: 12px;
    --radius-lg: 16px;
    --transition: all .25s cubic-bezier(.4,0,.2,1);
}

* { margin: 0; padding: 0; box-sizing: border-box; }

body {
    font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    background: var(--bg-light);
    color: var(--text-primary);
    line-height: 1.6;
}

.store-wrap { max-width: 1200px; margin: 0 auto; padding: 0 1.5rem 3rem; }

/* ── Breadcrumb ── */
.breadcrumb-bar {
    background: var(--bg-white);
    border-bottom: 1px solid var(--border);
    padding: .75rem 0;
    margin-bottom: 0;
}
.breadcrumb-bar .store-wrap { padding-top: 0; padding-bottom: 0; }
.breadcrumb-nav { display: flex; align-items: center; gap: .5rem; font-size: .875rem; }
.breadcrumb-nav a { color: var(--text-secondary); text-decoration: none; }
.breadcrumb-nav a:hover { color: var(--primary); }
.breadcrumb-nav .sep { color: var(--text-light); }
.breadcrumb-nav .current { color: var(--text-primary); font-weight: 600; }

/* ── Store Header ── */
.store-header {
    background: linear-gradient(135deg, #1e40af 0%, #2563eb 60%, #3b82f6 100%);
    color: #fff;
    padding: 2.5rem 0 0;
    margin-bottom: 2rem;
}
.store-header-inner {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1.5rem;
}
.store-hero { display: flex; align-items: flex-start; gap: 1.75rem; flex-wrap: wrap; margin-bottom: 2rem; }

.store-logo {
    width: 90px;
    height: 90px;
    border-radius: 16px;
    background: rgba(255,255,255,.15);
    border: 3px solid rgba(255,255,255,.4);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    color: #fff;
    flex-shrink: 0;
    overflow: hidden;
}
.store-logo img { width: 100%; height: 100%; object-fit: cover; border-radius: 13px; }

.store-meta { flex: 1; min-width: 220px; }
.store-meta h1 { font-size: 2rem; font-weight: 800; margin-bottom: .3rem; }
.store-meta .business-name { font-size: .95rem; opacity: .8; margin-bottom: .75rem; }

.store-badges { display: flex; flex-wrap: wrap; gap: .5rem; margin-bottom: .85rem; }
.store-badge {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    background: rgba(255,255,255,.18);
    border: 1px solid rgba(255,255,255,.3);
    border-radius: 20px;
    padding: .3rem .85rem;
    font-size: .82rem;
    font-weight: 600;
}
.store-badge i { font-size: .75rem; }

.store-desc { font-size: .95rem; opacity: .85; max-width: 640px; line-height: 1.6; }

.store-actions { display: flex; align-items: center; gap: .75rem; flex-wrap: wrap; padding-top: .5rem; }
.store-contact-item { display: flex; align-items: center; gap: .4rem; font-size: .875rem; opacity: .85; }
.store-contact-item a { color: #fff; text-decoration: none; }
.store-contact-item a:hover { text-decoration: underline; }
.store-social-links { display: flex; gap: .5rem; margin-left: auto; }
.store-social-link {
    width: 34px; height: 34px;
    border-radius: 50%;
    background: rgba(255,255,255,.2);
    border: 1px solid rgba(255,255,255,.3);
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-size: .9rem; text-decoration: none;
    transition: var(--transition);
}
.store-social-link:hover { background: rgba(255,255,255,.35); }

/* Tab strip below header */
.store-tabs-bar {
    background: rgba(255,255,255,.12);
    border-top: 1px solid rgba(255,255,255,.2);
    margin-top: 1rem;
}
.store-tabs-inner {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1.5rem;
    display: flex;
    gap: 0;
}
.store-tab {
    padding: .85rem 1.25rem;
    font-size: .9rem;
    font-weight: 600;
    color: rgba(255,255,255,.7);
    border-bottom: 3px solid transparent;
    cursor: pointer;
    transition: var(--transition);
    background: none;
    border-left: none;
    border-right: none;
    border-top: none;
    white-space: nowrap;
}
.store-tab.active,
.store-tab:hover { color: #fff; border-bottom-color: #fff; }

/* ── Body layout ── */
.store-body { display: grid; grid-template-columns: 220px 1fr; gap: 1.75rem; }

/* ── Sidebar ── */
.store-sidebar { display: flex; flex-direction: column; gap: 1.25rem; }

.sidebar-card {
    background: var(--bg-white);
    border-radius: var(--radius);
    box-shadow: var(--shadow-sm);
    padding: 1.25rem;
}
.sidebar-card-title {
    font-size: .8rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .8px;
    color: var(--text-secondary);
    margin-bottom: .85rem;
    padding-bottom: .5rem;
    border-bottom: 1px solid var(--border);
}

.category-filter-list { list-style: none; }
.category-filter-list li { margin-bottom: .1rem; }
.category-filter-list button {
    width: 100%;
    background: none;
    border: none;
    text-align: left;
    padding: .45rem .6rem;
    border-radius: 8px;
    font-size: .9rem;
    color: var(--text-secondary);
    cursor: pointer;
    transition: var(--transition);
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.category-filter-list button:hover,
.category-filter-list button.active { background: var(--primary-light); color: var(--primary); font-weight: 600; }
.cat-count { font-size: .75rem; background: var(--border); border-radius: 10px; padding: .1rem .45rem; }
.category-filter-list button.active .cat-count { background: var(--primary); color: #fff; }

.store-stat { display: flex; justify-content: space-between; align-items: center; padding: .4rem 0; font-size: .9rem; }
.store-stat .label { color: var(--text-secondary); }
.store-stat .value { font-weight: 700; color: var(--text-primary); }

.stars-inline { color: var(--accent); font-size: .85rem; }

/* ── Products area ── */
.products-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.25rem;
    flex-wrap: wrap;
    gap: .75rem;
}
.products-header h2 { font-size: 1.1rem; font-weight: 700; color: var(--text-primary); }
.products-count { font-size: .85rem; color: var(--text-secondary); }

.sort-select {
    padding: .4rem .75rem;
    border: 1.5px solid var(--border);
    border-radius: 8px;
    font-size: .875rem;
    color: var(--text-primary);
    background: var(--bg-white);
    cursor: pointer;
}
.sort-select:focus { outline: none; border-color: var(--primary); }

.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 1.25rem;
}

/* ── Product card ── */
.product-card {
    background: var(--bg-white);
    border-radius: var(--radius);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
    transition: var(--transition);
    cursor: pointer;
    display: flex;
    flex-direction: column;
}
.product-card:hover { transform: translateY(-3px); box-shadow: var(--shadow-md); }

.product-img {
    width: 100%;
    height: 180px;
    overflow: hidden;
    background: var(--bg-light);
    position: relative;
}
.product-img img {
    width: 100%; height: 100%;
    object-fit: cover;
    transition: transform .4s ease;
}
.product-card:hover .product-img img { transform: scale(1.05); }

.product-badge-tag {
    position: absolute;
    top: .6rem; left: .6rem;
    background: var(--primary);
    color: #fff;
    font-size: .7rem;
    font-weight: 700;
    padding: .2rem .55rem;
    border-radius: 20px;
}
.product-badge-tag.sale { background: #ef4444; }

.product-body { padding: 1rem; flex: 1; display: flex; flex-direction: column; gap: .35rem; }
.product-category-tag { font-size: .72rem; font-weight: 600; text-transform: uppercase; letter-spacing: .5px; color: var(--primary); }
.product-name { font-size: .975rem; font-weight: 700; color: var(--text-primary); line-height: 1.3; }
.product-brand { font-size: .8rem; color: var(--text-secondary); }

.product-rating { display: flex; align-items: center; gap: .35rem; margin-top: .15rem; }
.product-rating .stars { color: var(--accent); font-size: .75rem; }
.product-rating .count { font-size: .75rem; color: var(--text-light); }

.product-footer {
    padding: .75rem 1rem;
    border-top: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: .5rem;
}
.price-current { font-size: 1.1rem; font-weight: 800; color: var(--primary); }
.price-original { font-size: .82rem; color: var(--text-light); text-decoration: line-through; }

.btn-add-cart {
    padding: .45rem .9rem;
    background: var(--primary);
    color: #fff;
    border: none;
    border-radius: 8px;
    font-size: .82rem;
    font-weight: 600;
    cursor: pointer;
    transition: var(--transition);
    display: flex;
    align-items: center;
    gap: .3rem;
    white-space: nowrap;
}
.btn-add-cart:hover { background: var(--primary-dark); }
.btn-add-cart:disabled { background: var(--text-light); cursor: not-allowed; }

/* Empty / Loading */
.empty-state {
    grid-column: 1 / -1;
    text-align: center;
    padding: 4rem 2rem;
    color: var(--text-secondary);
}
.empty-state i { font-size: 3rem; margin-bottom: 1rem; opacity: .3; }
.empty-state h3 { font-size: 1.2rem; margin-bottom: .5rem; }

/* ── Responsive ── */
@media (max-width: 768px) {
    .store-body { grid-template-columns: 1fr; }
    .store-sidebar { display: none; }
    .store-hero { gap: 1rem; }
    .store-meta h1 { font-size: 1.5rem; }
    .store-logo { width: 64px; height: 64px; font-size: 1.8rem; }
}
</style>

<!-- Breadcrumb -->
<div class="breadcrumb-bar">
    <div class="store-wrap">
        <nav class="breadcrumb-nav">
            <a href="/">Home</a>
            <span class="sep"><i class="fas fa-chevron-right" style="font-size:.65rem;"></i></span>
            <a href="/shop">Shop</a>
            <span class="sep"><i class="fas fa-chevron-right" style="font-size:.65rem;"></i></span>
            <span class="current"><?php echo htmlspecialchars($shopName); ?></span>
        </nav>
    </div>
</div>

<!-- Store Header -->
<div class="store-header">
    <div class="store-header-inner">
        <div class="store-hero">
            <!-- Logo -->
            <div class="store-logo">
                <?php if (!empty($shopOwner['shop_logo'])): ?>
                    <img src="<?php echo htmlspecialchars($shopOwner['shop_logo']); ?>"
                         alt="<?php echo htmlspecialchars($shopName); ?>"
                         onerror="this.style.display='none';this.parentElement.querySelector('i').style.display='block';">
                    <i class="fas fa-store" style="display:none;"></i>
                <?php else: ?>
                    <i class="fas fa-store"></i>
                <?php endif; ?>
            </div>

            <!-- Info -->
            <div class="store-meta">
                <h1><?php echo htmlspecialchars($shopName); ?></h1>
                <?php if (!empty($shopOwner['business_name']) && $shopOwner['business_name'] !== $shopName): ?>
                    <div class="business-name"><?php echo htmlspecialchars($shopOwner['business_name']); ?></div>
                <?php endif; ?>

                <div class="store-badges">
                    <?php if (!empty($shopOwner['shop_city'])): ?>
                        <span class="store-badge"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($shopOwner['shop_city']); ?></span>
                    <?php endif; ?>
                    <?php if (!empty($shopOwner['year_established'])): ?>
                        <span class="store-badge"><i class="fas fa-calendar"></i> Est. <?php echo (int)$shopOwner['year_established']; ?></span>
                    <?php endif; ?>
                    <?php if (!empty($shopOwner['average_rating']) && $shopOwner['average_rating'] > 0): ?>
                        <span class="store-badge"><i class="fas fa-star"></i> <?php echo number_format((float)$shopOwner['average_rating'], 1); ?> rating</span>
                    <?php endif; ?>
                    <span class="store-badge"><i class="fas fa-box"></i> <?php echo count($products); ?> products</span>
                </div>

                <?php if (!empty($shopOwner['business_description'])): ?>
                    <div class="store-desc"><?php echo htmlspecialchars($shopOwner['business_description']); ?></div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Contact + socials -->
        <div class="store-actions">
            <?php if (!empty($shopOwner['business_phone'])): ?>
                <span class="store-contact-item"><i class="fas fa-phone"></i> <a href="tel:<?php echo htmlspecialchars($shopOwner['business_phone']); ?>"><?php echo htmlspecialchars($shopOwner['business_phone']); ?></a></span>
            <?php endif; ?>
            <?php if (!empty($shopOwner['business_email'])): ?>
                <span class="store-contact-item"><i class="fas fa-envelope"></i> <a href="mailto:<?php echo htmlspecialchars($shopOwner['business_email']); ?>"><?php echo htmlspecialchars($shopOwner['business_email']); ?></a></span>
            <?php endif; ?>

            <div class="store-social-links">
                <?php if (!empty($shopOwner['facebook'])): ?>
                    <a href="<?php echo htmlspecialchars($shopOwner['facebook']); ?>" target="_blank" class="store-social-link" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                <?php endif; ?>
                <?php if (!empty($shopOwner['instagram'])): ?>
                    <a href="<?php echo htmlspecialchars($shopOwner['instagram']); ?>" target="_blank" class="store-social-link" title="Instagram"><i class="fab fa-instagram"></i></a>
                <?php endif; ?>
                <?php if (!empty($shopOwner['twitter'])): ?>
                    <a href="<?php echo htmlspecialchars($shopOwner['twitter']); ?>" target="_blank" class="store-social-link" title="Twitter"><i class="fab fa-twitter"></i></a>
                <?php endif; ?>
                <?php if (!empty($shopOwner['website_url'])): ?>
                    <a href="<?php echo htmlspecialchars($shopOwner['website_url']); ?>" target="_blank" class="store-social-link" title="Website"><i class="fas fa-globe"></i></a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="store-tabs-bar">
        <div class="store-tabs-inner">
            <button class="store-tab active">All Products</button>
        </div>
    </div>
</div>

<!-- Body -->
<div class="store-wrap" style="padding-top:2rem;">
    <div class="store-body">

        <!-- Sidebar -->
        <aside class="store-sidebar">
            <!-- Category filter -->
            <?php if (!empty($categories)): ?>
            <div class="sidebar-card">
                <div class="sidebar-card-title"><i class="fas fa-tag" style="margin-right:.4rem;"></i>Categories</div>
                <?php
                    $catCounts = array_count_values(array_filter(array_column($products, 'category_name')));
                ?>
                <ul class="category-filter-list">
                    <li>
                        <button class="active" onclick="filterCat('', this)">
                            All <span class="cat-count"><?php echo count($products); ?></span>
                        </button>
                    </li>
                    <?php foreach ($categories as $cat): ?>
                    <li>
                        <button onclick="filterCat(<?php echo htmlspecialchars(json_encode($cat)); ?>, this)">
                            <?php echo htmlspecialchars($cat); ?>
                            <span class="cat-count"><?php echo $catCounts[$cat] ?? 0; ?></span>
                        </button>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>

            <!-- Store stats -->
            <div class="sidebar-card">
                <div class="sidebar-card-title"><i class="fas fa-chart-bar" style="margin-right:.4rem;"></i>Store Info</div>
                <?php if (!empty($shopOwner['average_rating']) && $shopOwner['average_rating'] > 0): ?>
                <div class="store-stat">
                    <span class="label">Rating</span>
                    <span class="value">
                        <span class="stars-inline"><?php
                            $r = round((float)$shopOwner['average_rating']);
                            for ($i = 1; $i <= 5; $i++) echo $i <= $r ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>';
                        ?></span>
                        <?php echo number_format((float)$shopOwner['average_rating'], 1); ?>
                    </span>
                </div>
                <?php endif; ?>
                <?php if (!empty($shopOwner['total_reviews'])): ?>
                <div class="store-stat">
                    <span class="label">Reviews</span>
                    <span class="value"><?php echo (int)$shopOwner['total_reviews']; ?></span>
                </div>
                <?php endif; ?>
                <div class="store-stat">
                    <span class="label">Products</span>
                    <span class="value"><?php echo count($products); ?></span>
                </div>
                <?php if (!empty($shopOwner['year_established'])): ?>
                <div class="store-stat">
                    <span class="label">Since</span>
                    <span class="value"><?php echo (int)$shopOwner['year_established']; ?></span>
                </div>
                <?php endif; ?>
            </div>
        </aside>

        <!-- Products area -->
        <div>
            <div class="products-header">
                <div>
                    <h2 id="products-heading">All Products</h2>
                    <div class="products-count" id="products-count"><?php echo count($products); ?> items</div>
                </div>
                <select class="sort-select" id="sort-select" onchange="sortProducts(this.value)">
                    <option value="rating">Top Rated</option>
                    <option value="price_asc">Price: Low to High</option>
                    <option value="price_desc">Price: High to Low</option>
                    <option value="newest">Newest</option>
                    <option value="name">Name A–Z</option>
                </select>
            </div>

            <div class="products-grid" id="products-grid"></div>
        </div>
    </div>
</div>

<script>
const allProducts = <?php echo json_encode(array_values($products), JSON_HEX_TAG | JSON_INVALID_UTF8_SUBSTITUTE); ?>;
let filteredProducts = [...allProducts];
let activeCat = '';

const catImages = {
    football: '/public/assets/images/products/football.jpg',
    cricket:  '/public/assets/images/products/cricket-bat.jpg',
    tennis:   '/public/assets/images/products/tennis-racket.jpg',
    basketball: '/public/assets/images/products/basketball.jpg',
    badminton: '/public/assets/images/products/badminton-racket.jpg',
};

function getProductImage(product) {
    if (product.images && Array.isArray(product.images) && product.images.length > 0) {
        const img = product.images[0];
        if (img.startsWith('http')) return img;
        if (img.startsWith('/')) return img;
        return '/public/uploads/products/' + img;
    }
    const slug = (product.category_slug || '').toLowerCase();
    return catImages[slug] || '/public/assets/images/products/football.jpg';
}

function generateStars(rating) {
    const full = Math.floor(rating || 0);
    const half = (rating || 0) % 1 >= .5;
    let s = '';
    for (let i = 0; i < full; i++) s += '<i class="fas fa-star"></i>';
    if (half) s += '<i class="fas fa-star-half-alt"></i>';
    for (let i = 0; i < 5 - full - (half ? 1 : 0); i++) s += '<i class="far fa-star"></i>';
    return s;
}

function renderGrid(list) {
    const grid = document.getElementById('products-grid');
    document.getElementById('products-count').textContent = list.length + ' item' + (list.length !== 1 ? 's' : '');

    if (!list.length) {
        grid.innerHTML = `<div class="empty-state"><i class="fas fa-search"></i><h3>No products found</h3><p>Try a different category.</p></div>`;
        return;
    }

    grid.innerHTML = list.map(p => {
        const img = getProductImage(p);
        const price = parseFloat(p.price).toLocaleString();
        const origPrice = p.original_price && parseFloat(p.original_price) > parseFloat(p.price)
            ? `<span class="price-original">LKR ${parseFloat(p.original_price).toLocaleString()}</span>` : '';
        const discount = p.original_price && parseFloat(p.original_price) > parseFloat(p.price)
            ? Math.round(((p.original_price - p.price) / p.original_price) * 100) : 0;
        const badge = discount > 0
            ? `<span class="product-badge-tag sale">${discount}% OFF</span>`
            : (p.is_featured ? `<span class="product-badge-tag">Featured</span>` : '');
        const outOfStock = parseInt(p.stock_quantity || 0) <= 0;

        return `
            <div class="product-card" onclick="window.location.href='/product/${p.id}'">
                <div class="product-img">
                    <img src="${img}" alt="${escHtml(p.name)}" loading="lazy"
                         onerror="this.src='/public/assets/images/products/football.jpg'">
                    ${badge}
                </div>
                <div class="product-body">
                    <div class="product-category-tag">${escHtml(p.category_name || '')}</div>
                    <div class="product-name">${escHtml(p.name)}</div>
                    ${p.brand ? `<div class="product-brand">${escHtml(p.brand)}</div>` : ''}
                    <div class="product-rating">
                        <span class="stars">${generateStars(p.rating)}</span>
                        <span class="count">(${p.total_reviews || 0})</span>
                    </div>
                </div>
                <div class="product-footer">
                    <div>
                        <div class="price-current">LKR ${price}</div>
                        ${origPrice}
                    </div>
                    <button class="btn-add-cart" ${outOfStock ? 'disabled' : ''}
                            onclick="event.stopPropagation(); addToCart(${p.id}, 1)"
                            title="${outOfStock ? 'Out of stock' : 'Add to cart'}">
                        ${outOfStock ? '<i class="fas fa-ban"></i>' : '<i class="fas fa-cart-plus"></i>'}
                    </button>
                </div>
            </div>`;
    }).join('');
}

function filterCat(cat, btn) {
    activeCat = cat;
    document.querySelectorAll('.category-filter-list button').forEach(b => b.classList.remove('active'));
    if (btn) btn.classList.add('active');

    filteredProducts = cat ? allProducts.filter(p => p.category_name === cat) : [...allProducts];
    document.getElementById('products-heading').textContent = cat || 'All Products';
    sortProducts(document.getElementById('sort-select').value);
}

function sortProducts(val) {
    const list = [...filteredProducts];
    if (val === 'price_asc')  list.sort((a,b) => a.price - b.price);
    if (val === 'price_desc') list.sort((a,b) => b.price - a.price);
    if (val === 'rating')     list.sort((a,b) => (b.rating||0) - (a.rating||0));
    if (val === 'newest')     list.sort((a,b) => new Date(b.created_at) - new Date(a.created_at));
    if (val === 'name')       list.sort((a,b) => a.name.localeCompare(b.name));
    renderGrid(list);
}

function escHtml(str) {
    const d = document.createElement('div');
    d.textContent = str || '';
    return d.innerHTML;
}

document.addEventListener('DOMContentLoaded', () => renderGrid(allProducts));
</script>
