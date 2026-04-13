<?php $_base = defined('BASE_URL') ? BASE_URL : ''; ?>
<?php
/**
 * Admin Sports Categories Management View
 */
$activePage = $activePage ?? 'categories';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sports Categories - GoPlay Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= $_base ?>/public/css/pages/admin-categories.css">
</head>
<body>
    <div class="admin-dashboard">
        <?php include APP_PATH . '/views/components/admin-sidebar.php'; ?>

        <main class="admin-main">
            <header class="admin-header">
                <div class="header-left">
                    <button class="sidebar-toggle" onclick="toggleSidebar()">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div>
                        <h1>Sports Categories</h1>
                        <p class="header-subtitle">Manage sports available on the platform</p>
                    </div>
                </div>
                <div class="header-right">
                    <button class="btn btn-primary" onclick="openAddModal()">
                        <i class="fas fa-plus"></i> Add Category
                    </button>
                </div>
            </header>

            <div class="admin-content">
                <!-- Stats Row -->
                <div class="stats-grid" id="statsGrid">
                    <div class="stat-card">
                        <div class="stat-icon" style="background: rgba(59,130,246,0.1); color: #3b82f6;">
                            <i class="fas fa-layer-group"></i>
                        </div>
                        <div class="stat-info">
                            <span class="stat-number" id="statTotal">0</span>
                            <span class="stat-label">Total Categories</span>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background: rgba(16,185,129,0.1); color: #10b981;">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-info">
                            <span class="stat-number" id="statActive">0</span>
                            <span class="stat-label">Active</span>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background: rgba(239,68,68,0.1); color: #ef4444;">
                            <i class="fas fa-times-circle"></i>
                        </div>
                        <div class="stat-info">
                            <span class="stat-number" id="statInactive">0</span>
                            <span class="stat-label">Inactive</span>
                        </div>
                    </div>
                </div>

                <!-- Categories Grid -->
                <div class="categories-container">
                    <div class="categories-grid" id="categoriesGrid">
                        <div class="loading-state">
                            <i class="fas fa-spinner fa-spin"></i>
                            <p>Loading categories...</p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Add/Edit Modal -->
    <div class="modal-overlay" id="categoryModal">
        <div class="modal">
            <div class="modal-header">
                <h2 id="modalTitle">Add Sports Category</h2>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <form id="categoryForm" onsubmit="handleFormSubmit(event)">
                <div class="modal-body">
                    <input type="hidden" id="categoryId" value="">

                    <div class="form-group">
                        <label for="categoryName">Category Name <span class="required">*</span></label>
                        <input type="text" id="categoryName" name="name" placeholder="e.g., Table Tennis" required>
                    </div>

                    <div class="form-group">
                        <label for="categoryDescription">Description</label>
                        <textarea id="categoryDescription" name="description" rows="3" placeholder="Brief description of this sport category"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="categoryIcon">Icon Class (Font Awesome)</label>
                        <input type="text" id="categoryIcon" name="icon" placeholder="fas fa-trophy" value="fas fa-trophy">
                        <p class="form-hint">Browse icons at <a href="https://fontawesome.com/icons" target="_blank" rel="noopener">fontawesome.com/icons</a></p>
                        <div class="icon-preview">
                            <i id="iconPreview" class="fas fa-trophy"></i>
                            <span>Preview</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="fas fa-save"></i> Save Category
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="<?= $_base ?>/public/js/pages/admin-categories.js"></script>
</body>
</html>
