<?php $_base = defined('BASE_URL') ? BASE_URL : ''; ?>
<?php
/**
 * Admin Promotions/Banners Management View
 */
$activePage = $activePage ?? 'promotions';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Promotions & Banners - GoPlay Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= $_base ?>/public/css/pages/admin-promotions.css?v=<?= time() ?>">
</head>
<body>
    <div class="admin-dashboard">
        <?php include APP_PATH . '/views/components/admin-sidebar.php'; ?>

        <main class="admin-main">
            <header class="admin-header">
                <div class="header-left">
                    <button class="sidebar-toggle" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>
                    <div>
                        <h1>Promotions & Banners</h1>
                        <p class="header-subtitle">Manage promotional content across the platform</p>
                    </div>
                </div>
                <div class="header-right">
                    <button class="btn btn-primary" onclick="openCreateModal()">
                        <i class="fas fa-plus"></i> New Promotion
                    </button>
                </div>
            </header>

            <div class="admin-content">
                <!-- Stats -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon" style="background: rgba(59,130,246,0.1); color: #3b82f6;"><i class="fas fa-bullhorn"></i></div>
                        <div class="stat-info"><span class="stat-number" id="statTotal">0</span><span class="stat-label">Total Promotions</span></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background: rgba(16,185,129,0.1); color: #10b981;"><i class="fas fa-eye"></i></div>
                        <div class="stat-info"><span class="stat-number" id="statActive">0</span><span class="stat-label">Active</span></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background: rgba(239,68,68,0.1); color: #ef4444;"><i class="fas fa-eye-slash"></i></div>
                        <div class="stat-info"><span class="stat-number" id="statInactive">0</span><span class="stat-label">Inactive</span></div>
                    </div>
                </div>

                <!-- Filter Tabs -->
                <div class="filter-tabs">
                    <button class="filter-tab active" onclick="filterPromotions('all', this)">All</button>
                    <button class="filter-tab" onclick="filterPromotions('active', this)">Active</button>
                    <button class="filter-tab" onclick="filterPromotions('inactive', this)">Inactive</button>
                </div>

                <!-- Promotions Table -->
                <div class="table-container">
                    <table class="data-table" id="promotionsTable">
                        <thead>
                            <tr>
                                <th>Preview</th>
                                <th>Title</th>
                                <th>Position</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Schedule</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="promotionsBody">
                            <tr><td colspan="7" class="loading-cell"><i class="fas fa-spinner fa-spin"></i> Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Create/Edit Modal -->
    <div class="modal-overlay" id="promoModal">
        <div class="modal modal-lg" style="max-height:85vh;overflow:hidden;">
            <div class="modal-header">
                <h2 id="promoModalTitle">New Promotion</h2>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <form id="promoForm" onsubmit="handlePromoSubmit(event)" style="display:flex;flex-direction:column;flex:1;min-height:0;overflow:hidden;">
                <div class="modal-body" style="overflow-y:auto;flex:1;min-height:0;">
                    <input type="hidden" id="promoId" value="">
                    <div class="form-row">
                        <div class="form-group flex-2">
                            <label for="promoTitle">Title <span class="required">*</span></label>
                            <input type="text" id="promoTitle" name="title" placeholder="Promotion headline" required>
                        </div>
                        <div class="form-group flex-1">
                            <label for="promoPosition">Position</label>
                            <select id="promoPosition" name="position">
                                <option value="hero">Hero Banner</option>
                                <option value="sidebar">Sidebar</option>
                                <option value="footer">Footer</option>
                                <option value="popup">Popup</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="promoSubtitle">Subtitle / Description</label>
                        <textarea id="promoSubtitle" name="subtitle" rows="2" placeholder="Optional subtitle text"></textarea>
                    </div>
                    <div class="form-row">
                        <div class="form-group flex-1">
                            <label for="promoLinkUrl">Link URL</label>
                            <input type="text" id="promoLinkUrl" name="link_url" placeholder="/shop or https://...">
                        </div>
                        <div class="form-group flex-1">
                            <label for="promoLinkText">Button Text</label>
                            <input type="text" id="promoLinkText" name="link_text" value="Learn More">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group flex-1">
                            <label for="promoBgColor">Background Color</label>
                            <div class="color-input-wrap">
                                <input type="color" id="promoBgColor" name="bg_color" value="#3b82f6">
                                <span id="bgColorLabel">#3b82f6</span>
                            </div>
                        </div>
                        <div class="form-group flex-1">
                            <label for="promoTextColor">Text Color</label>
                            <div class="color-input-wrap">
                                <input type="color" id="promoTextColor" name="text_color" value="#ffffff">
                                <span id="textColorLabel">#ffffff</span>
                            </div>
                        </div>
                        <div class="form-group flex-1">
                            <label for="promoPriority">Priority</label>
                            <input type="number" id="promoPriority" name="priority" value="0" min="0" max="100">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group flex-1">
                            <label for="promoStartsAt">Starts At</label>
                            <input type="datetime-local" id="promoStartsAt" name="starts_at">
                        </div>
                        <div class="form-group flex-1">
                            <label for="promoEndsAt">Ends At</label>
                            <input type="datetime-local" id="promoEndsAt" name="ends_at">
                        </div>
                    </div>

                    <!-- Active Toggle -->
                    <div class="form-group">
                        <label class="toggle-label">
                            <input type="checkbox" id="promoIsActive" name="is_active" value="1" checked>
                            <span class="toggle-switch"></span>
                            <span class="toggle-text">Active — visible on the website</span>
                        </label>
                    </div>

                    <!-- Image Upload -->
                    <div class="form-group">
                        <label for="promoImage">Banner Image</label>
                        <div id="currentImageWrap" class="current-image-wrap" style="display:none;">
                            <img id="currentImage" src="" alt="Current banner">
                            <small>Current image. Upload a new one to replace it.</small>
                        </div>
                        <input type="file" id="promoImage" name="image" accept="image/jpeg,image/png,image/webp,image/gif">
                        <p class="form-hint">Recommended: 1200×400px. JPG, PNG, WebP, GIF. Max 5MB. Leave empty to use color background.</p>
                    </div>

                    <!-- Live Preview -->
                    <div class="form-group">
                        <label>Live Preview</label>
                        <div class="banner-preview" id="bannerPreview" style="background: #3b82f6; color: #ffffff;">
                            <h3 id="previewTitle">Your Title Here</h3>
                            <p id="previewSubtitle">Your subtitle text</p>
                            <span class="preview-btn" id="previewBtn">Learn More</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="promoSubmitBtn"><i class="fas fa-save"></i> Save</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal-overlay" id="deleteConfirmOverlay">
        <div class="modal" style="max-width:440px;">
            <div class="modal-header" style="border-bottom:none; padding-bottom:0;">
                <h2 style="color:#ef4444;"><i class="fas fa-exclamation-triangle"></i> Delete Promotion</h2>
                <button class="modal-close" onclick="closeDeleteConfirm()">&times;</button>
            </div>
            <div class="modal-body" style="text-align:center; padding-top:8px;">
                <p style="font-size:15px; color:#475569;">Are you sure you want to permanently delete<br><strong id="deletePromoName"></strong>?</p>
                <p style="font-size:13px; color:#94a3b8; margin-top:8px;">This action cannot be undone.</p>
            </div>
            <div class="modal-footer" style="justify-content:center; gap:16px;">
                <button class="btn btn-secondary" onclick="closeDeleteConfirm()">Cancel</button>
                <button class="btn btn-danger" id="confirmDeleteBtn"><i class="fas fa-trash"></i> Delete</button>
            </div>
        </div>
    </div>

    <script>window.BASE_URL = '<?= $_base ?>';</script>
    <script src="<?= $_base ?>/public/js/pages/admin-promotions.js?v=<?= time() ?>"></script>
</body>
</html>
