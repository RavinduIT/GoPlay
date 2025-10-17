<?php
$title = 'Products Management - Shop Owner Dashboard';
$additionalCSS = ['/public/css/pages/shop-owner-products.css'];
$additionalJS = ['/public/js/pages/shop-owner-products.js'];
?>
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
                        <p class="stat-value">156</p>
                        <span class="stat-label">Active listings</span>
                    </div>
                </div>

                <div class="stat-card green">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-content">
                        <h3>In Stock</h3>
                        <p class="stat-value">142</p>
                        <span class="stat-label">91% availability</span>
                    </div>
                </div>

                <div class="stat-card orange">
                    <div class="stat-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Low Stock</h3>
                        <p class="stat-value">14</p>
                        <span class="stat-label">Need restock</span>
                    </div>
                </div>

                <div class="stat-card red">
                    <div class="stat-icon">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Out of Stock</h3>
                        <p class="stat-value">5</p>
                        <span class="stat-label">Unavailable</span>
                    </div>
                </div>
            </div>

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
                        <!-- Product Row 1 -->
                        <tr class="product-row">
                            <td>
                                <input type="checkbox" class="product-checkbox">
                            </td>
                            <td>
                                <div class="product-info">
                                    <img src="/public/assets/images/products/cricket-bat-pro.jpg" alt="Cricket Bat" class="product-image">
                                    <div class="product-details">
                                        <h4>Cricket Bat - Professional Grade</h4>
                                        <span class="brand-tag">Nike</span>
                                    </div>
                                </div>
                            </td>
                            <td><span class="product-id">CB-001</span></td>
                            <td><span class="category-badge cricket">Cricket</span></td>
                            <td><strong>LKR 3,500</strong></td>
                            <td>
                                <div class="stock-indicator in-stock">
                                    <span class="stock-dot"></span>
                                    <span class="stock-count">45 units</span>
                                </div>
                            </td>
                            <td><span class="status-badge active">Active</span></td>
                            <td>
                                <div class="rating-stars">
                                    <i class="fas fa-star"></i>
                                    <span>4.8 (23)</span>
                                </div>
                            </td>
                            <td>2 hours ago</td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-action edit" title="Edit Product">
                                        <i class="fas fa-edit"></i>
                                        Edit
                                    </button>
                                    <button class="btn-action delete" title="Delete Product">
                                        <i class="fas fa-trash-alt"></i>
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Product Row 2 -->
                        <tr class="product-row">
                            <td>
                                <input type="checkbox" class="product-checkbox">
                            </td>
                            <td>
                                <div class="product-info">
                                    <img src="/public/assets/images/products/football-official.jpg" alt="Football" class="product-image">
                                    <div class="product-details">
                                        <h4>Football - Official Match Ball</h4>
                                        <span class="brand-tag">Adidas</span>
                                    </div>
                                </div>
                            </td>
                            <td><span class="product-id">FB-001</span></td>
                            <td><span class="category-badge football">Football</span></td>
                            <td><strong>LKR 1,200</strong></td>
                            <td>
                                <div class="stock-indicator in-stock">
                                    <span class="stock-dot"></span>
                                    <span class="stock-count">67 units</span>
                                </div>
                            </td>
                            <td><span class="status-badge active">Active</span></td>
                            <td>
                                <div class="rating-stars">
                                    <i class="fas fa-star"></i>
                                    <span>4.5 (45)</span>
                                </div>
                            </td>
                            <td>5 hours ago</td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-action edit" title="Edit Product">
                                        <i class="fas fa-edit"></i>
                                        Edit
                                    </button>
                                    <button class="btn-action delete" title="Delete Product">
                                        <i class="fas fa-trash-alt"></i>
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Product Row 3 -->
                        <tr class="product-row">
                            <td>
                                <input type="checkbox" class="product-checkbox">
                            </td>
                            <td>
                                <div class="product-info">
                                    <img src="/public/assets/images/products/tennis-racket.jpg" alt="Tennis Racket" class="product-image">
                                    <div class="product-details">
                                        <h4>Tennis Racket - Carbon Fiber</h4>
                                        <span class="brand-tag">Wilson</span>
                                    </div>
                                </div>
                            </td>
                            <td><span class="product-id">TR-015</span></td>
                            <td><span class="category-badge tennis">Tennis</span></td>
                            <td><strong>LKR 4,200</strong></td>
                            <td>
                                <div class="stock-indicator low-stock">
                                    <span class="stock-dot"></span>
                                    <span class="stock-count">8 units</span>
                                </div>
                            </td>
                            <td><span class="status-badge active">Active</span></td>
                            <td>
                                <div class="rating-stars">
                                    <i class="fas fa-star"></i>
                                    <span>4.9 (67)</span>
                                </div>
                            </td>
                            <td>1 day ago</td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-action edit" title="Edit Product">
                                        <i class="fas fa-edit"></i>
                                        Edit
                                    </button>
                                    <button class="btn-action delete" title="Delete Product">
                                        <i class="fas fa-trash-alt"></i>
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Product Row 4 -->
                        <tr class="product-row">
                            <td>
                                <input type="checkbox" class="product-checkbox">
                            </td>
                            <td>
                                <div class="product-info">
                                    <img src="/public/assets/images/products/basketball.jpg" alt="Basketball" class="product-image">
                                    <div class="product-details">
                                        <h4>Basketball - Indoor/Outdoor</h4>
                                        <span class="brand-tag">Spalding</span>
                                    </div>
                                </div>
                            </td>
                            <td><span class="product-id">BB-014</span></td>
                            <td><span class="category-badge basketball">Basketball</span></td>
                            <td><strong>LKR 1,450</strong></td>
                            <td>
                                <div class="stock-indicator out-of-stock">
                                    <span class="stock-dot"></span>
                                    <span class="stock-count">0 units</span>
                                </div>
                            </td>
                            <td><span class="status-badge inactive">Inactive</span></td>
                            <td>
                                <div class="rating-stars">
                                    <i class="fas fa-star"></i>
                                    <span>4.2 (12)</span>
                                </div>
                            </td>
                            <td>3 days ago</td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-action edit" title="Edit Product">
                                        <i class="fas fa-edit"></i>
                                        Edit
                                    </button>
                                    <button class="btn-action delete" title="Delete Product">
                                        <i class="fas fa-trash-alt"></i>
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Product Row 5 -->
                        <tr class="product-row">
                            <td>
                                <input type="checkbox" class="product-checkbox">
                            </td>
                            <td>
                                <div class="product-info">
                                    <img src="/public/assets/images/products/badminton-set.jpg" alt="Badminton Set" class="product-image">
                                    <div class="product-details">
                                        <h4>Badminton Racket Set (2 Rackets)</h4>
                                        <span class="brand-tag">Yonex</span>
                                    </div>
                                </div>
                            </td>
                            <td><span class="product-id">BR-028</span></td>
                            <td><span class="category-badge badminton">Badminton</span></td>
                            <td><strong>LKR 1,500</strong></td>
                            <td>
                                <div class="stock-indicator in-stock">
                                    <span class="stock-dot"></span>
                                    <span class="stock-count">32 units</span>
                                </div>
                            </td>
                            <td><span class="status-badge active">Active</span></td>
                            <td>
                                <div class="rating-stars">
                                    <i class="fas fa-star"></i>
                                    <span>4.6 (34)</span>
                                </div>
                            </td>
                            <td>1 week ago</td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-action edit" title="Edit Product">
                                        <i class="fas fa-edit"></i>
                                        Edit
                                    </button>
                                    <button class="btn-action delete" title="Delete Product">
                                        <i class="fas fa-trash-alt"></i>
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
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
        <form id="addProductForm" class="product-form">
            <div class="form-row">
                <div class="form-group">
                    <label for="productName">Product Name <span class="required">*</span></label>
                    <input type="text" id="productName" name="productName" required placeholder="Enter product name">
                </div>
                <div class="form-group">
                    <label for="productId">Product ID <span class="required">*</span></label>
                    <input type="text" id="productId" name="productId" required placeholder="e.g., CB-001">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="category">Category <span class="required">*</span></label>
                    <select id="category" name="category" required>
                        <option value="">Select Category</option>
                        <option value="cricket">Cricket</option>
                        <option value="football">Football</option>
                        <option value="tennis">Tennis</option>
                        <option value="basketball">Basketball</option>
                        <option value="badminton">Badminton</option>
                        <option value="volleyball">Volleyball</option>
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
                    <input type="number" id="stock" name="stock" required min="0" placeholder="0">
                </div>
            </div>

            <div class="form-group full-width">
                <label for="description">Product Description</label>
                <textarea id="description" name="description" rows="3" placeholder="Enter product description..."></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="status">Status <span class="required">*</span></label>
                    <select id="status" name="status" required>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="productImage">Product Image</label>
                    <input type="file" id="productImage" name="productImage" accept="image/*">
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeAddProductModal()">Cancel</button>
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
        <form id="editProductForm" class="product-form">
            <input type="hidden" id="editProductRowId" name="editProductRowId">

            <div class="form-row">
                <div class="form-group">
                    <label for="editProductName">Product Name <span class="required">*</span></label>
                    <input type="text" id="editProductName" name="editProductName" required placeholder="Enter product name">
                </div>
                <div class="form-group">
                    <label for="editProductId">Product ID <span class="required">*</span></label>
                    <input type="text" id="editProductId" name="editProductId" required placeholder="e.g., CB-001" readonly>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="editCategory">Category <span class="required">*</span></label>
                    <select id="editCategory" name="editCategory" required>
                        <option value="">Select Category</option>
                        <option value="cricket">Cricket</option>
                        <option value="football">Football</option>
                        <option value="tennis">Tennis</option>
                        <option value="basketball">Basketball</option>
                        <option value="badminton">Badminton</option>
                        <option value="volleyball">Volleyball</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="editBrand">Brand <span class="required">*</span></label>
                    <input type="text" id="editBrand" name="editBrand" required placeholder="Enter brand name">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="editPrice">Price (LKR) <span class="required">*</span></label>
                    <input type="number" id="editPrice" name="editPrice" required min="0" step="0.01" placeholder="0.00">
                </div>
                <div class="form-group">
                    <label for="editStock">Stock Quantity <span class="required">*</span></label>
                    <input type="number" id="editStock" name="editStock" required min="0" placeholder="0">
                </div>
            </div>

            <div class="form-group full-width">
                <label for="editDescription">Product Description</label>
                <textarea id="editDescription" name="editDescription" rows="3" placeholder="Enter product description..."></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="editStatus">Status <span class="required">*</span></label>
                    <select id="editStatus" name="editStatus" required>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="editProductImage">Product Image</label>
                    <input type="file" id="editProductImage" name="editProductImage" accept="image/*">
                    <small class="form-hint">Leave empty to keep current image</small>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeEditProductModal()">Cancel</button>
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
        <div class="modal-footer">
            <button type="button" class="btn-secondary" onclick="closeDeleteProductModal()">Cancel</button>
            <button type="button" class="btn-danger" onclick="confirmDeleteProduct()">
                <i class="fas fa-trash"></i>
                Delete Product
            </button>
        </div>
    </div>
</div>

<script src="/public/js/pages/shop-owner-products.js"></script>
