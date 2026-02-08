<?php
// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load required models
require_once __DIR__ . '/../../../app/models/Category.php';
require_once __DIR__ . '/../../../app/models/Product.php';

use App\Models\Category;
use App\Models\Product;

$categoryModel = new Category();
$categories = $categoryModel->getActiveCategories();

$productModel = new Product();
$shopOwnerId = $_SESSION['user_id'] ?? 0;

// Get filters from URL
$filters = [
    'search' => $_GET['search'] ?? '',
    'category' => $_GET['category'] ?? '',
    'status' => $_GET['status'] ?? '',
    'stock' => $_GET['stock'] ?? ''
];

// Remove empty filters
$filters = array_filter($filters);

$products = $productModel->getProductsByShopOwner($shopOwnerId, $filters);
$stats = $productModel->getShopOwnerStats($shopOwnerId);

// Debug: Check what's in products array
if (!empty($products)) {
    error_log('First product keys: ' . implode(', ', array_keys($products[0])));
    error_log('First product ID: ' . ($products[0]['id'] ?? 'MISSING'));
}

// Debug: Uncomment to see what's being loaded
// echo "<!-- DEBUG: Shop Owner ID: $shopOwnerId -->";
// echo "<!-- DEBUG: Products Count: " . count($products) . " -->";
// echo "<!-- DEBUG: Total Products Stat: " . ($stats['total_products'] ?? 0) . " -->";

$title = 'Products Management - Shop Owner Dashboard';
$additionalCSS = ['/public/css/pages/shop-owner-dashboard.css', '/public/css/pages/shop-owner-products.css'];
$additionalJS = ['/public/js/shop-owner-sidebar.js', '/public/js/pages/shop-owner-products.js'];
?>
<link rel="stylesheet" href="/public/css/pages/shop-owner-dashboard.css">
<link rel="stylesheet" href="/public/css/pages/shop-owner-products.css">

<div class="shop-owner-dashboard">
    <!-- Sidebar -->
    <?php include __DIR__ . '/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="dashboard-main">
        <!-- Top Header -->
        <header class="dashboard-header">
            <div class="header-left">
                <button class="sidebar-toggle" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="page-title-section">
                    <h1 class="page-title">Products Management</h1>
                    <p class="page-subtitle">Manage your product inventory and listings</p>
                </div>
            </div>
            <div class="header-right">
                <button class="btn-primary" onclick="openAddProductModal()">
                    <i class="fas fa-plus"></i>
                    Add New Product
                </button>
            </div>
        </header>

        <!-- Dashboard Content -->
        <div class="dashboard-content">
            <!-- Stats Overview -->
            <div class="stats-overview">
                <div class="stat-card blue">
                    <div class="stat-icon">
                        <i class="fas fa-box"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Total Products</h3>
                        <p class="stat-value"><?= $stats['total_products'] ?? 0 ?></p>
                        <span class="stat-label">Active listings</span>
                    </div>
                </div>

                <div class="stat-card green">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-content">
                        <h3>In Stock</h3>
                        <p class="stat-value"><?= $stats['in_stock'] ?? 0 ?></p>
                        <span class="stat-label">Available now</span>
                    </div>
                </div>

                <div class="stat-card orange">
                    <div class="stat-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Low Stock</h3>
                        <p class="stat-value"><?= $stats['low_stock'] ?? 0 ?></p>
                        <span class="stat-label">Need restock</span>
                    </div>
                </div>

                <div class="stat-card red">
                    <div class="stat-icon">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Out of Stock</h3>
                        <p class="stat-value"><?= $stats['out_of_stock'] ?? 0 ?></p>
                        <span class="stat-label">Unavailable</span>
                    </div>
                </div>
            </div>

            <?php if (empty($products) && empty($filters)): ?>
            <!-- Info Alert for Empty State -->
            <div class="alert alert-info" style="margin: 1.5rem 0; padding: 1rem 1.5rem; background: #dbeafe; border-left: 4px solid #3b82f6; border-radius: 8px;">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <i class="fas fa-info-circle" style="color: #3b82f6; font-size: 1.5rem;"></i>
                    <div>
                        <strong style="color: #1e40af;">No Products Yet</strong>
                        <p style="margin: 0.25rem 0 0 0; color: #1e40af;">Get started by adding your first product using the "Add New Product" button above.</p>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Filters and Search -->
            <div class="filters-section">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="productSearch" placeholder="Search by product name, ID, or brand...">
                </div>

                <div class="filter-controls">
                    <select class="filter-select" id="categoryFilter">
                        <option value="">All Categories</option>
                        <option value="cricket">Cricket</option>
                        <option value="football">Football</option>
                        <option value="tennis">Tennis</option>
                        <option value="basketball">Basketball</option>
                        <option value="badminton">Badminton</option>
                    </select>

                    <select class="filter-select" id="stockFilter">
                        <option value="">All Stock Status</option>
                        <option value="in-stock">In Stock</option>
                        <option value="low-stock">Low Stock</option>
                        <option value="out-of-stock">Out of Stock</option>
                    </select>

                    <select class="filter-select" id="sortFilter">
                        <option value="newest">Newest First</option>
                        <option value="oldest">Oldest First</option>
                        <option value="name-asc">Name (A-Z)</option>
                        <option value="name-desc">Name (Z-A)</option>
                        <option value="price-low">Price: Low to High</option>
                        <option value="price-high">Price: High to Low</option>
                    </select>

                    <button class="btn-filter-clear" onclick="clearFilters()">
                        <i class="fas fa-redo"></i>
                        Clear Filters
                    </button>
                </div>
            </div>

            <!-- Products Table -->
            <div class="products-table-container">
                <table class="products-table">
                    <thead>
                        <tr>
                            <th>
                                <input type="checkbox" id="selectAll">
                            </th>
                            <th>Product</th>
                            <th>Product ID</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th>Rating</th>
                            <th>Last Updated</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="productsTableBody">
                        <?php if (!empty($products)): ?>
                            <?php foreach ($products as $product): 
                                // Debug: Check if ID exists
                                if (empty($product['id'])) {
                                    error_log('Product missing ID: ' . print_r($product, true));
                                    continue; // Skip products without ID
                                }
                                
                                // Handle images - might be array or JSON string
                                $images = [];
                                if (!empty($product['images'])) {
                                    $images = is_array($product['images']) ? $product['images'] : json_decode($product['images'], true);
                                }
                                $firstImage = !empty($images) ? $images[0] : '/public/assets/images/placeholder-product.svg';
                                
                                // Determine stock status
                                $stockClass = 'in-stock';
                                if ($product['stock_quantity'] == 0) {
                                    $stockClass = 'out-of-stock';
                                } elseif ($product['stock_quantity'] <= $product['min_stock_level']) {
                                    $stockClass = 'low-stock';
                                }
                                
                                // Format date
                                $updatedDate = new DateTime($product['updated_at']);
                                $now = new DateTime();
                                $diff = $now->diff($updatedDate);
                                
                                if ($diff->days == 0) {
                                    if ($diff->h == 0) {
                                        $timeAgo = 'Just now';
                                    } else {
                                        $timeAgo = $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
                                    }
                                } elseif ($diff->days == 1) {
                                    $timeAgo = 'Yesterday';
                                } else {
                                    $timeAgo = $diff->days . ' day' . ($diff->days > 1 ? 's' : '') . ' ago';
                                }
                            ?>
                            <tr class="product-row" 
                                data-product-id="<?= $product['id'] ?>"
                                data-product-name="<?= htmlspecialchars($product['name'], ENT_QUOTES) ?>"
                                data-product-sku="<?= htmlspecialchars($product['sku'], ENT_QUOTES) ?>"
                                data-product-brand="<?= htmlspecialchars($product['brand'] ?? '', ENT_QUOTES) ?>"
                                data-product-category-id="<?= $product['category_id'] ?>"
                                data-product-price="<?= $product['price'] ?>"
                                data-product-stock="<?= $product['stock_quantity'] ?>"
                                data-product-status="<?= $product['status'] ?>"
                                data-product-description="<?= htmlspecialchars($product['description'] ?? '', ENT_QUOTES) ?>">
                                <td>
                                    <input type="checkbox" class="product-checkbox" value="<?= $product['id'] ?>">
                                </td>
                                <td>
                                    <div class="product-info">
                                        <img src="<?= htmlspecialchars($firstImage) ?>" 
                                             alt="<?= htmlspecialchars($product['name']) ?>" 
                                             class="product-image"
                                             onerror="this.src='/public/assets/images/placeholder-product.svg';">
                                        <div class="product-details">
                                            <h4><?= htmlspecialchars($product['name']) ?></h4>
                                            <span class="brand-tag"><?= htmlspecialchars($product['brand'] ?? 'No Brand') ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="product-id"><?= htmlspecialchars($product['sku']) ?></span></td>
                                <td><span class="category-badge"><?= htmlspecialchars($product['category_name'] ?? 'General') ?></span></td>
                                <td><strong>LKR <?= number_format($product['price'], 2) ?></strong></td>
                                <td>
                                    <div class="stock-indicator <?= $stockClass ?>">
                                        <span class="stock-dot"></span>
                                        <span class="stock-count"><?= $product['stock_quantity'] ?> units</span>
                                    </div>
                                </td>
                                <td><span class="status-badge <?= $product['status'] ?>"><?= ucfirst($product['status']) ?></span></td>
                                <td>
                                    <div class="rating-stars">
                                        <i class="fas fa-star"></i>
                                        <span><?= number_format($product['rating'] ?? 0, 1) ?> (<?= $product['total_reviews'] ?? 0 ?>)</span>
                                    </div>
                                </td>
                                <td><?= $timeAgo ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-action edit" 
                                                onclick="editProduct(<?= $product['id'] ?>)" 
                                                title="Edit Product">
                                            <i class="fas fa-edit"></i>
                                            Edit
                                        </button>
                                        <button class="btn-action delete" 
                                                onclick="deleteProduct(<?= $product['id'] ?>, '<?= htmlspecialchars($product['name'], ENT_QUOTES) ?>')" 
                                                title="Delete Product">
                                            <i class="fas fa-trash-alt"></i>
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="10" style="text-align: center; padding: 3rem;">
                                    <i class="fas fa-box-open" style="font-size: 3rem; color: var(--gray-400); margin-bottom: 1rem; display: block;"></i>
                                    <p style="color: var(--gray-600); font-size: 1.1rem; margin-bottom: 1rem;">No products found</p>
                                    <button class="btn-primary" onclick="openAddProductModal()" style="margin-top: 1rem;">
                                        <i class="fas fa-plus"></i> Add Your First Product
                                    </button>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="pagination-container">
                <div class="pagination-info">
                    Showing <strong>1-5</strong> of <strong>156</strong> products
                </div>
                <div class="pagination-controls">
                    <button class="pagination-btn" disabled>
                        <i class="fas fa-chevron-left"></i>
                        Previous
                    </button>
                    <button class="pagination-btn active">1</button>
                    <button class="pagination-btn">2</button>
                    <button class="pagination-btn">3</button>
                    <button class="pagination-btn">...</button>
                    <button class="pagination-btn">32</button>
                    <button class="pagination-btn">
                        Next
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Add Product Modal -->
<div id="addProductModal" class="modal">
    <div class="modal-overlay" onclick="closeAddProductModal()"></div>
    <div class="modal-content">
        <div class="modal-header">
            <h2>Add New Product</h2>
            <button class="modal-close" onclick="closeAddProductModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="addProductForm" class="product-form" method="POST" action="/shop-owner/products/create" enctype="multipart/form-data">
            <div class="form-row">
                <div class="form-group">
                    <label for="productName">Product Name <span class="required">*</span></label>
                    <input type="text" id="productName" name="name" required placeholder="Enter product name">
                </div>
                <div class="form-group">
                    <label for="productSku">SKU <span class="text-muted">(optional)</span></label>
                    <input type="text" id="productSku" name="sku" placeholder="Auto-generated if empty">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="category">Category <span class="required">*</span></label>
                    <select id="category" name="category_id" required>
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= htmlspecialchars($category['id']) ?>">
                                <?= htmlspecialchars($category['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="brand">Brand <span class="required">*</span></label>
                    <input type="text" id="brand" name="brand" required placeholder="Enter brand name">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="price">Price (LKR) <span class="required">*</span></label>
                    <input type="number" id="price" name="price" required min="0" step="0.01" placeholder="0.00">
                </div>
                <div class="form-group">
                    <label for="stock">Stock Quantity <span class="required">*</span></label>
                    <input type="number" id="stock" name="stock_quantity" required min="0" placeholder="0">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="comparePrice">Compare Price (LKR)</label>
                    <input type="number" id="comparePrice" name="compare_price" min="0" step="0.01" placeholder="0.00">
                </div>
                <div class="form-group">
                    <label for="minStock">Min Stock Level</label>
                    <input type="number" id="minStock" name="min_stock_level" min="0" value="5" placeholder="5">
                </div>
            </div>

            <div class="form-group full-width">
                <label for="description">Product Description</label>
                <textarea id="description" name="description" rows="4" placeholder="Enter product description..."></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="status">Status <span class="required">*</span></label>
                    <select id="status" name="status" required>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="out_of_stock">Out of Stock</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="productImages">Product Images</label>
                    <input type="file" id="productImages" name="images[]" accept="image/*" multiple onchange="previewImages(this, 'imagePreviewAdd')">
                    <small class="form-hint">Max 5 images, 5MB each</small>
                </div>
            </div>

            <div id="imagePreviewAdd" class="image-preview-container"></div>

            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeAddProductModal()">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-plus"></i>
                    Add Product
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Product Modal -->
<div id="editProductModal" class="modal">
    <div class="modal-overlay" onclick="closeEditProductModal()"></div>
    <div class="modal-content">
        <div class="modal-header">
            <h2>Edit Product</h2>
            <button class="modal-close" onclick="closeEditProductModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="editProductForm" class="product-form" method="POST" action="/shop-owner/products/update" enctype="multipart/form-data">
            <input type="hidden" id="editProductId" name="product_id">

            <div class="form-row">
                <div class="form-group">
                    <label for="editProductName">Product Name <span class="required">*</span></label>
                    <input type="text" id="editProductName" name="name" required placeholder="Enter product name">
                </div>
                <div class="form-group">
                    <label for="editProductSku">SKU</label>
                    <input type="text" id="editProductSku" name="sku" readonly>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="editCategory">Category <span class="required">*</span></label>
                    <select id="editCategory" name="category_id" required>
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= htmlspecialchars($category['id']) ?>">
                                <?= htmlspecialchars($category['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="editBrand">Brand <span class="required">*</span></label>
                    <input type="text" id="editBrand" name="brand" required placeholder="Enter brand name">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="editPrice">Price (LKR) <span class="required">*</span></label>
                    <input type="number" id="editPrice" name="price" required min="0" step="0.01" placeholder="0.00">
                </div>
                <div class="form-group">
                    <label for="editStock">Stock Quantity <span class="required">*</span></label>
                    <input type="number" id="editStock" name="stock_quantity" required min="0" placeholder="0">
                </div>
            </div>

            <div class="form-group full-width">
                <label for="editDescription">Product Description</label>
                <textarea id="editDescription" name="description" rows="4" placeholder="Enter product description..."></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="editStatus">Status <span class="required">*</span></label>
                    <select id="editStatus" name="status" required>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="out_of_stock">Out of Stock</option>
                        <option value="discontinued">Discontinued</option>
                    </select>
                </div>
                <div class="form-group full-width">
                    <label for="editProductImages">Product Images</label>
                    <input type="file" id="editProductImages" name="images[]" accept="image/*" multiple onchange="previewImages(this, 'imagePreviewEdit')">
                    <div style="margin-top: 8px;">
                        <label style="display: inline-flex; align-items: center; font-weight: normal; cursor: pointer;">
                            <input type="checkbox" name="replace_images" value="1" style="margin-right: 8px;">
                            Replace existing images (uncheck to add more images)
                        </label>
                    </div>
                    <small class="form-hint">Upload new images. Check the box above to replace all existing images, or leave unchecked to add more.</small>
                </div>
            </div>

            <div id="imagePreviewEdit" class="image-preview-container"></div>

            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeEditProductModal()">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save"></i>
                    Update Product
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteProductModal" class="modal modal-sm">
    <div class="modal-overlay" onclick="closeDeleteProductModal()"></div>
    <div class="modal-content">
        <div class="modal-header danger">
            <h2>Delete Product</h2>
            <button class="modal-close" onclick="closeDeleteProductModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="delete-warning">
                <i class="fas fa-exclamation-triangle"></i>
                <p>Are you sure you want to delete this product?</p>
                <p class="product-name-delete" id="deleteProductName"></p>
                <p class="warning-text">This action cannot be undone.</p>
            </div>
        </div>
        <form id="deleteProductForm" method="POST" action="/shop-owner/products/delete">
            <input type="hidden" id="deleteProductId" name="product_id">
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeDeleteProductModal()">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button type="submit" class="btn-danger">
                    <i class="fas fa-trash"></i>
                    Delete Product
                </button>
            </div>
        </form>
    </div>
</div>

<?php if (isset($_SESSION['success'])): ?>
<div class="alert alert-success" id="successAlert">
    <i class="fas fa-check-circle"></i>
    <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
</div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
<div class="alert alert-error" id="errorAlert">
    <i class="fas fa-exclamation-circle"></i>
    <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
</div>
<?php endif; ?>

<script src="/public/js/pages/shop-owner-products.js"></script>
