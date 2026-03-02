<?php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/../../../app/models/Category.php';
require_once __DIR__ . '/../../../app/models/Product.php';

use App\Models\Category;
use App\Models\Product;

$categoryModel = new Category();
$categories    = $categoryModel->getActiveCategories();

$productModel  = new Product();
$shopOwnerId   = $_SESSION['user_id'] ?? 0;

$filters = array_filter([
    'search'   => $_GET['search']   ?? '',
    'category' => $_GET['category'] ?? '',
    'status'   => $_GET['status']   ?? '',
    'stock'    => $_GET['stock']    ?? '',
]);

$products = $productModel->getProductsByShopOwner($shopOwnerId, $filters);
$stats    = $productModel->getShopOwnerStats($shopOwnerId);

// Build JS-safe product array
$productsForJs = array_map(function ($p) {
    $images = is_array($p['images'])
        ? $p['images']
        : (json_decode($p['images'] ?? '[]', true) ?: []);
    return [
        'id'             => (int)$p['id'],
        'name'           => $p['name'],
        'sku'            => $p['sku'],
        'brand'          => $p['brand'] ?? '',
        'category_id'    => (int)$p['category_id'],
        'category_name'  => $p['category_name'] ?? 'General',
        'price'          => (float)$p['price'],
        'compare_price'  => $p['compare_price'] ? (float)$p['compare_price'] : null,
        'stock_quantity' => (int)$p['stock_quantity'],
        'min_stock_level'=> (int)($p['min_stock_level'] ?? 10),
        'status'         => $p['status'],
        'rating'         => (float)($p['rating'] ?? 0),
        'total_reviews'  => (int)($p['total_reviews'] ?? 0),
        'description'    => $p['description'] ?? '',
        'images'         => $images,
        'updated_at'     => $p['updated_at'],
    ];
}, $products);

$title         = 'Products Management - Shop Owner Dashboard';
$additionalCSS = ['/public/css/pages/shop-owner-dashboard.css', '/public/css/pages/shop-owner-products.css'];
$additionalJS  = ['/public/js/shop-owner-sidebar.js'];
?>

<link rel="stylesheet" href="/public/css/pages/shop-owner-dashboard.css">
<link rel="stylesheet" href="/public/css/pages/shop-owner-products.css">

<!-- Flash Messages -->
<?php if (isset($_SESSION['success'])): ?>
<div class="sp-alert sp-alert-success">
    <i class="fas fa-check-circle"></i>
    <?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
    <button class="sp-alert-close" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>
</div>
<?php endif; ?>
<?php if (isset($_SESSION['error'])): ?>
<div class="sp-alert sp-alert-error">
    <i class="fas fa-exclamation-circle"></i>
    <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
    <button class="sp-alert-close" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>
</div>
<?php endif; ?>

<!-- Product data for JS -->
<script>
window.shopProducts   = <?= json_encode($productsForJs, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
window.shopCategories = <?= json_encode($categories,    JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
</script>

<div class="shop-owner-dashboard">
    <!-- Sidebar -->
    <?php include __DIR__ . '/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="dashboard-main">

        <!-- Header -->
        <header class="dashboard-header">
            <div class="header-left">
                <button class="sidebar-toggle" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="page-title-section">
                    <h1 class="page-title">Products Management</h1>
                    <p class="page-subtitle">Manage your inventory and product listings</p>
                </div>
            </div>
            <div class="header-right">
                <div class="sp-total-chip">
                    <i class="fas fa-boxes"></i>
                    <span><?= count($products) ?> Products</span>
                </div>
                <button class="btn-primary sp-add-btn" onclick="openAddProductModal()">
                    <i class="fas fa-plus"></i>
                    Add Product
                </button>
            </div>
        </header>

        <!-- Dashboard Content -->
        <div class="dashboard-content">

            <!-- Stats Grid -->
            <div class="sp-stats-grid">
                <div class="sp-stat-card sp-stat-purple">
                    <div class="sp-stat-icon"><i class="fas fa-boxes"></i></div>
                    <div class="sp-stat-body">
                        <div class="sp-stat-value"><?= $stats['total_products'] ?? 0 ?></div>
                        <div class="sp-stat-label">Total Products</div>
                        <div class="sp-stat-sub"><?= $stats['active_products'] ?? 0 ?> active listings</div>
                    </div>
                </div>
                <div class="sp-stat-card sp-stat-green">
                    <div class="sp-stat-icon"><i class="fas fa-check-circle"></i></div>
                    <div class="sp-stat-body">
                        <div class="sp-stat-value"><?= $stats['in_stock'] ?? 0 ?></div>
                        <div class="sp-stat-label">In Stock</div>
                        <div class="sp-stat-sub">Ready to sell</div>
                    </div>
                </div>
                <div class="sp-stat-card sp-stat-amber">
                    <div class="sp-stat-icon"><i class="fas fa-exclamation-triangle"></i></div>
                    <div class="sp-stat-body">
                        <div class="sp-stat-value"><?= $stats['low_stock'] ?? 0 ?></div>
                        <div class="sp-stat-label">Low Stock</div>
                        <div class="sp-stat-sub">Need restocking</div>
                    </div>
                </div>
                <div class="sp-stat-card sp-stat-red">
                    <div class="sp-stat-icon"><i class="fas fa-times-circle"></i></div>
                    <div class="sp-stat-body">
                        <div class="sp-stat-value"><?= $stats['out_of_stock'] ?? 0 ?></div>
                        <div class="sp-stat-label">Out of Stock</div>
                        <div class="sp-stat-sub">Unavailable</div>
                    </div>
                </div>
            </div>

            <!-- Toolbar -->
            <div class="sp-toolbar">
                <div class="sp-toolbar-left">
                    <div class="sp-search-wrap">
                        <i class="fas fa-search sp-search-icon"></i>
                        <input type="text" id="productSearch" class="sp-search-input"
                               placeholder="Search by name, SKU, or brand…">
                        <button class="sp-search-clear" id="searchClearBtn" onclick="clearSearch()" style="display:none">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="sp-toolbar-filters">
                    <select id="categoryFilter" class="sp-filter-select">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= (int)$cat['id'] ?>">
                                <?= htmlspecialchars($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <select id="stockFilter" class="sp-filter-select">
                        <option value="">All Stock</option>
                        <option value="in-stock">In Stock</option>
                        <option value="low-stock">Low Stock</option>
                        <option value="out-of-stock">Out of Stock</option>
                    </select>
                    <select id="sortFilter" class="sp-filter-select">
                        <option value="newest">Newest First</option>
                        <option value="oldest">Oldest First</option>
                        <option value="name-asc">Name A–Z</option>
                        <option value="name-desc">Name Z–A</option>
                        <option value="price-low">Price: Low–High</option>
                        <option value="price-high">Price: High–Low</option>
                        <option value="stock-low">Stock: Low–High</option>
                    </select>
                    <button class="sp-btn-clear" onclick="clearFilters()">
                        <i class="fas fa-redo"></i> Reset
                    </button>
                </div>
                <div class="sp-toolbar-right">
                    <span class="sp-results-count" id="resultsCount"></span>
                    <div class="sp-view-toggle">
                        <button id="tableViewBtn" class="sp-view-btn active" onclick="setView('table')" title="Table view">
                            <i class="fas fa-list"></i>
                        </button>
                        <button id="gridViewBtn" class="sp-view-btn" onclick="setView('grid')" title="Grid view">
                            <i class="fas fa-th-large"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Table View -->
            <div id="tableView" class="sp-table-wrap">
                <table class="sp-table">
                    <thead>
                        <tr>
                            <th style="width:40px"><input type="checkbox" id="selectAll" class="sp-checkbox"></th>
                            <th>Product</th>
                            <th>SKU</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th>Rating</th>
                            <th>Updated</th>
                            <th style="width:100px">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="productsTableBody">
                        <!-- Rendered by JS -->
                    </tbody>
                </table>
            </div>

            <!-- Grid View -->
            <div id="gridView" class="sp-products-grid" style="display:none">
                <!-- Rendered by JS -->
            </div>

            <!-- Pagination -->
            <div class="sp-pagination">
                <div class="sp-page-info" id="paginationInfo"></div>
                <div class="sp-page-btns" id="paginationBtns"></div>
            </div>

        </div><!-- /.dashboard-content -->
    </main>
</div>

<!-- ============================
     ADD PRODUCT MODAL
     ============================ -->
<div id="addProductModal" class="sp-modal">
    <div class="sp-modal-backdrop" onclick="closeAddProductModal()"></div>
    <div class="sp-modal-box">
        <div class="sp-modal-header">
            <div class="sp-modal-title">
                <div class="sp-modal-icon sp-icon-purple"><i class="fas fa-plus"></i></div>
                <div>
                    <h2>Add New Product</h2>
                    <p>Fill in the details to list a new product</p>
                </div>
            </div>
            <button class="sp-modal-close" onclick="closeAddProductModal()"><i class="fas fa-times"></i></button>
        </div>

        <form id="addProductForm" class="sp-product-form" method="POST"
              action="/shop-owner/products/create" enctype="multipart/form-data">

            <!-- Section 1: Basic Info -->
            <div class="sp-form-section">
                <div class="sp-form-section-title">
                    <i class="fas fa-tag"></i> Basic Information
                </div>
                <div class="sp-form-grid">
                    <div class="sp-form-group sp-col-2">
                        <label>Product Name <span class="sp-required">*</span></label>
                        <input type="text" name="name" id="productName" required placeholder="e.g. Professional Cricket Bat">
                    </div>
                    <div class="sp-form-group">
                        <label>SKU <span class="sp-muted">(optional)</span></label>
                        <input type="text" name="sku" id="productSku" placeholder="Auto-generated if empty">
                    </div>
                    <div class="sp-form-group">
                        <label>Brand <span class="sp-required">*</span></label>
                        <input type="text" name="brand" id="brand" required placeholder="e.g. GM, Adidas, Nike">
                    </div>
                    <div class="sp-form-group">
                        <label>Category <span class="sp-required">*</span></label>
                        <select name="category_id" id="category" required>
                            <option value="">Select category…</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= (int)$cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="sp-form-group">
                        <label>Status <span class="sp-required">*</span></label>
                        <select name="status" id="status" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="out_of_stock">Out of Stock</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Section 2: Pricing & Inventory -->
            <div class="sp-form-section">
                <div class="sp-form-section-title">
                    <i class="fas fa-tag"></i> Pricing &amp; Inventory
                </div>
                <div class="sp-form-grid">
                    <div class="sp-form-group">
                        <label>Price (LKR) <span class="sp-required">*</span></label>
                        <div class="sp-input-prefix">
                            <span class="sp-prefix">LKR</span>
                            <input type="number" name="price" id="price" required min="0" step="0.01" placeholder="0.00">
                        </div>
                    </div>
                    <div class="sp-form-group">
                        <label>Compare Price (LKR) <span class="sp-muted">(original/crossed-out)</span></label>
                        <div class="sp-input-prefix">
                            <span class="sp-prefix">LKR</span>
                            <input type="number" name="compare_price" id="comparePrice" min="0" step="0.01" placeholder="0.00">
                        </div>
                    </div>
                    <div class="sp-form-group">
                        <label>Stock Quantity <span class="sp-required">*</span></label>
                        <input type="number" name="stock_quantity" id="stock" required min="0" placeholder="0">
                    </div>
                    <div class="sp-form-group">
                        <label>Low-Stock Alert Level</label>
                        <input type="number" name="min_stock_level" id="minStock" min="0" value="10" placeholder="10">
                    </div>
                </div>
            </div>

            <!-- Section 3: Description & Images -->
            <div class="sp-form-section">
                <div class="sp-form-section-title">
                    <i class="fas fa-image"></i> Description &amp; Images
                </div>
                <div class="sp-form-group">
                    <label>Product Description</label>
                    <textarea name="description" id="description" rows="4"
                              placeholder="Describe your product — materials, features, ideal use…"></textarea>
                </div>
                <div class="sp-form-group">
                    <label>Product Images <span class="sp-muted">(up to 5, max 5MB each)</span></label>
                    <div class="sp-upload-zone" onclick="document.getElementById('productImages').click()">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <span>Click to upload or drag &amp; drop images</span>
                        <small>JPEG, PNG, WebP accepted</small>
                    </div>
                    <input type="file" id="productImages" name="images[]" accept="image/*" multiple
                           onchange="previewImages(this, 'imagePreviewAdd')" style="display:none">
                    <div id="imagePreviewAdd" class="sp-img-preview-grid"></div>
                </div>
            </div>

            <div class="sp-modal-footer">
                <button type="button" class="sp-btn-secondary" onclick="closeAddProductModal()">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button type="submit" class="sp-btn-primary">
                    <i class="fas fa-plus"></i> Add Product
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ============================
     EDIT PRODUCT MODAL
     ============================ -->
<div id="editProductModal" class="sp-modal">
    <div class="sp-modal-backdrop" onclick="closeEditProductModal()"></div>
    <div class="sp-modal-box">
        <div class="sp-modal-header">
            <div class="sp-modal-title">
                <div class="sp-modal-icon sp-icon-blue"><i class="fas fa-edit"></i></div>
                <div>
                    <h2>Edit Product</h2>
                    <p>Update product details and inventory</p>
                </div>
            </div>
            <button class="sp-modal-close" onclick="closeEditProductModal()"><i class="fas fa-times"></i></button>
        </div>

        <form id="editProductForm" class="sp-product-form" method="POST"
              action="/shop-owner/products/update" enctype="multipart/form-data">
            <input type="hidden" id="editProductId" name="product_id">

            <!-- Section 1: Basic Info -->
            <div class="sp-form-section">
                <div class="sp-form-section-title">
                    <i class="fas fa-tag"></i> Basic Information
                </div>
                <div class="sp-form-grid">
                    <div class="sp-form-group sp-col-2">
                        <label>Product Name <span class="sp-required">*</span></label>
                        <input type="text" name="name" id="editProductName" required>
                    </div>
                    <div class="sp-form-group">
                        <label>SKU</label>
                        <input type="text" name="sku" id="editProductSku" readonly class="sp-readonly">
                    </div>
                    <div class="sp-form-group">
                        <label>Brand <span class="sp-required">*</span></label>
                        <input type="text" name="brand" id="editBrand" required>
                    </div>
                    <div class="sp-form-group">
                        <label>Category <span class="sp-required">*</span></label>
                        <select name="category_id" id="editCategory" required>
                            <option value="">Select category…</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= (int)$cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="sp-form-group">
                        <label>Status <span class="sp-required">*</span></label>
                        <select name="status" id="editStatus" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="out_of_stock">Out of Stock</option>
                            <option value="discontinued">Discontinued</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Section 2: Pricing & Inventory -->
            <div class="sp-form-section">
                <div class="sp-form-section-title">
                    <i class="fas fa-dollar-sign"></i> Pricing &amp; Inventory
                </div>
                <div class="sp-form-grid">
                    <div class="sp-form-group">
                        <label>Price (LKR) <span class="sp-required">*</span></label>
                        <div class="sp-input-prefix">
                            <span class="sp-prefix">LKR</span>
                            <input type="number" name="price" id="editPrice" required min="0" step="0.01">
                        </div>
                    </div>
                    <div class="sp-form-group">
                        <label>Compare Price (LKR) <span class="sp-muted">(original/crossed-out)</span></label>
                        <div class="sp-input-prefix">
                            <span class="sp-prefix">LKR</span>
                            <input type="number" name="compare_price" id="editComparePrice" min="0" step="0.01" placeholder="0.00">
                        </div>
                    </div>
                    <div class="sp-form-group">
                        <label>Stock Quantity <span class="sp-required">*</span></label>
                        <input type="number" name="stock_quantity" id="editStock" required min="0">
                    </div>
                    <div class="sp-form-group">
                        <label>Low-Stock Alert Level</label>
                        <input type="number" name="min_stock_level" id="editMinStock" min="0">
                    </div>
                </div>
            </div>

            <!-- Section 3: Description & Images -->
            <div class="sp-form-section">
                <div class="sp-form-section-title">
                    <i class="fas fa-image"></i> Description &amp; Images
                </div>
                <div class="sp-form-group">
                    <label>Product Description</label>
                    <textarea name="description" id="editDescription" rows="4"></textarea>
                </div>
                <div class="sp-form-group">
                    <label>Current Images</label>
                    <div id="editCurrentImages" class="sp-current-images" style="display:none"></div>
                </div>
                <div class="sp-form-group">
                    <label>Upload New Images <span class="sp-muted">(up to 5, max 5MB each)</span></label>
                    <div class="sp-upload-zone" onclick="document.getElementById('editProductImages').click()">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <span>Click to upload or drag &amp; drop</span>
                        <small>JPEG, PNG, WebP accepted</small>
                    </div>
                    <input type="file" id="editProductImages" name="images[]" accept="image/*" multiple
                           onchange="previewImages(this, 'imagePreviewEdit')" style="display:none">
                    <div id="imagePreviewEdit" class="sp-img-preview-grid"></div>
                    <label class="sp-checkbox-label" style="margin-top:10px">
                        <input type="checkbox" name="replace_images" value="1">
                        Replace all existing images with new uploads
                    </label>
                </div>
            </div>

            <div class="sp-modal-footer">
                <button type="button" class="sp-btn-secondary" onclick="closeEditProductModal()">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button type="submit" class="sp-btn-primary">
                    <i class="fas fa-save"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ============================
     DELETE CONFIRM MODAL
     ============================ -->
<div id="deleteProductModal" class="sp-modal">
    <div class="sp-modal-backdrop" onclick="closeDeleteProductModal()"></div>
    <div class="sp-modal-box sp-modal-sm">
        <div class="sp-modal-header sp-modal-header-danger">
            <div class="sp-modal-title">
                <div class="sp-modal-icon sp-icon-red"><i class="fas fa-trash-alt"></i></div>
                <div>
                    <h2>Delete Product</h2>
                    <p>This action cannot be undone</p>
                </div>
            </div>
            <button class="sp-modal-close" onclick="closeDeleteProductModal()"><i class="fas fa-times"></i></button>
        </div>
        <div class="sp-modal-body">
            <div class="sp-delete-warning">
                <i class="fas fa-exclamation-triangle"></i>
                <p>Are you sure you want to permanently delete:</p>
                <div class="sp-delete-product-name" id="deleteProductName"></div>
                <p class="sp-delete-note">All associated data will be removed.</p>
            </div>
        </div>
        <form id="deleteProductForm" method="POST" action="/shop-owner/products/delete">
            <input type="hidden" id="deleteProductId" name="product_id">
            <div class="sp-modal-footer">
                <button type="button" class="sp-btn-secondary" onclick="closeDeleteProductModal()">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button type="submit" class="sp-btn-danger">
                    <i class="fas fa-trash-alt"></i> Delete Product
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ============================
     BULK ACTION BAR
     ============================ -->
<div id="spBulkBar" class="sp-bulk-bar">
    <div class="sp-bulk-bar-inner">
        <div class="sp-bulk-count">
            <span class="sp-bulk-count-badge" id="spBulkCount">0</span>
            <span>selected</span>
        </div>
        <div class="sp-bulk-sep"></div>
        <div class="sp-bulk-actions">
            <button class="sp-bulk-btn sp-bulk-btn-activate" onclick="bulkSetStatus('active')">
                <i class="fas fa-check-circle"></i> Activate
            </button>
            <button class="sp-bulk-btn sp-bulk-btn-deactivate" onclick="bulkSetStatus('inactive')">
                <i class="fas fa-minus-circle"></i> Deactivate
            </button>
            <button class="sp-bulk-btn sp-bulk-btn-export" onclick="bulkExportCsv()">
                <i class="fas fa-download"></i> Export CSV
            </button>
            <button class="sp-bulk-btn sp-bulk-btn-delete" onclick="bulkDelete()">
                <i class="fas fa-trash-alt"></i> Delete
            </button>
        </div>
        <div class="sp-bulk-sep"></div>
        <button class="sp-bulk-btn sp-bulk-btn-clear" onclick="clearBulkSelection()">
            <i class="fas fa-times"></i> Clear
        </button>
    </div>
</div>

<script src="/public/js/pages/shop-owner-products.js"></script>
