<?php
$_base = defined('BASE_URL') ? BASE_URL : '';
// This file serves as both create.php and edit.php
$news = $data['news'] ?? null;
$isEdit = !empty($news);
$pageTitle = $isEdit ? 'Edit News Article' : 'Create News Article';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= $_base ?>/public/css/pages/admin-dashboard.css">
    <link rel="stylesheet" href="<?= $_base ?>/public/css/pages/admin-news-form.css">
    <script>window.BASE_URL = '<?= $_base ?>';</script>
    <style>
        .alert {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 0.5rem;
            font-weight: 500;
        }
        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #6ee7b7;
        }
        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }
        .alert-info {
            background: #dbeafe;
            color: #1e40af;
            border: 1px solid #93c5fd;
        }
    </style>
</head>
<body>

<div class="admin-dashboard">
    <?php include __DIR__ . '/../../components/admin-sidebar.php'; ?>

    <!-- Main Content -->
    <main class="admin-main">
        <!-- Top Header -->
        <header class="admin-header">
            <div class="header-left">
                <h1 class="page-title"><?= $pageTitle ?></h1>
            </div>
            <div class="header-right">
                <a href="<?= $_base ?>/admin/news" class="btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to News
                </a>
            </div>
        </header>

        <!-- Form Content -->
        <div class="dashboard-content">
            <div id="alertContainer"></div>
            
            <div class="news-form-container">
                <form id="newsForm" enctype="multipart/form-data">
                    <input type="hidden" id="newsId" name="id" value="<?= $isEdit ? $news['id'] : '' ?>">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="title">Title *</label>
                            <input type="text" 
                                   id="title" 
                                   name="title" 
                                   class="form-control" 
                                   value="<?= $isEdit ? htmlspecialchars($news['title']) : '' ?>" 
                                   required
                                   placeholder="Enter article title">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-6">
                            <label for="category">Category *</label>
                            <select id="category" name="category" class="form-control" required>
                                <option value="">Select Category</option>
                                <option value="Cricket" <?= $isEdit && $news['category'] === 'Cricket' ? 'selected' : '' ?>>Cricket</option>
                                <option value="Football" <?= $isEdit && $news['category'] === 'Football' ? 'selected' : '' ?>>Football</option>
                                <option value="Basketball" <?= $isEdit && $news['category'] === 'Basketball' ? 'selected' : '' ?>>Basketball</option>
                                <option value="Tennis" <?= $isEdit && $news['category'] === 'Tennis' ? 'selected' : '' ?>>Tennis</option>
                                <option value="Swimming" <?= $isEdit && $news['category'] === 'Swimming' ? 'selected' : '' ?>>Swimming</option>
                                <option value="General Sports" <?= $isEdit && $news['category'] === 'General Sports' ? 'selected' : '' ?>>General Sports</option>
                            </select>
                        </div>

                        <div class="form-group col-6">
                            <label for="status">Status *</label>
                            <select id="status" name="status" class="form-control" required>
                                <option value="draft" <?= $isEdit && $news['status'] === 'draft' ? 'selected' : '' ?>>Draft</option>
                                <option value="published" <?= $isEdit && $news['status'] === 'published' ? 'selected' : '' ?>>Published</option>
                                <option value="archived" <?= $isEdit && $news['status'] === 'archived' ? 'selected' : '' ?>>Archived</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="excerpt">Excerpt</label>
                            <textarea id="excerpt" 
                                      name="excerpt" 
                                      class="form-control" 
                                      rows="3"
                                      maxlength="200"
                                      placeholder="Brief summary (optional - auto-generated if empty)"><?= $isEdit ? htmlspecialchars($news['excerpt'] ?? '') : '' ?></textarea>
                            <small class="form-text">
                                <span id="excerptCount">0</span>/200 characters
                            </small>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="content">Content *</label>
                            <textarea id="content" 
                                      name="content" 
                                      class="form-control" 
                                      rows="15" 
                                      required
                                      placeholder="Write your article content here..."><?= $isEdit ? htmlspecialchars($news['content']) : '' ?></textarea>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="featured_image">Featured Image</label>
                            <input type="file" 
                                   id="featured_image" 
                                   name="featured_image" 
                                   class="form-control-file" 
                                   accept="image/jpeg,image/jpg,image/png,image/gif,image/webp"
                                   onchange="previewImage(this)">
                            <small class="form-text">Recommended: 1200x630px. Max: 5MB. Formats: JPG, PNG, GIF, WebP</small>
                            
                            <div id="imagePreview" class="image-preview">
                                <?php if ($isEdit && !empty($news['featured_image'])): ?>
                                <img src="<?= htmlspecialchars($news['featured_image']) ?>" alt="Current featured image">
                                <p class="preview-text">Current image</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="button" onclick="window.location.href='<?= $_base ?>/admin/news'" class="btn-secondary">
                            Cancel
                        </button>
                        <button type="submit" class="btn-primary" id="submitBtn">
                            <i class="fas fa-save"></i> <?= $isEdit ? 'Update Article' : 'Create Article' ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>

<script>
// Show alert message
function showAlert(message, type = 'info') {
    const alertContainer = document.getElementById('alertContainer');
    const alert = document.createElement('div');
    alert.className = `alert alert-${type}`;
    alert.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
        ${message}
    `;
    alertContainer.innerHTML = '';
    alertContainer.appendChild(alert);
    
    // Scroll to top
    window.scrollTo({ top: 0, behavior: 'smooth' });
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        alert.style.opacity = '0';
        alert.style.transition = 'opacity 0.3s';
        setTimeout(() => alert.remove(), 300);
    }, 5000);
}

// Preview image
function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    
    if (input.files && input.files[0]) {
        const file = input.files[0];
        
        // Validate file size
        if (file.size > 5 * 1024 * 1024) {
            showAlert('Image file is too large. Maximum size is 5MB.', 'error');
            input.value = '';
            return;
        }
        
        // Validate file type
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        if (!allowedTypes.includes(file.type)) {
            showAlert('Invalid file type. Please upload JPG, PNG, GIF, or WebP image.', 'error');
            input.value = '';
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `
                <img src="${e.target.result}" alt="Preview">
                <p class="preview-text">New image preview</p>
            `;
        }
        reader.readAsDataURL(file);
    }
}

// Update excerpt character count
document.getElementById('excerpt').addEventListener('input', function() {
    const count = this.value.length;
    document.getElementById('excerptCount').textContent = count;
    
    if (count > 200) {
        this.value = this.value.substring(0, 200);
        document.getElementById('excerptCount').textContent = '200';
    }
});

// Initialize character count
document.addEventListener('DOMContentLoaded', function() {
    const excerpt = document.getElementById('excerpt');
    document.getElementById('excerptCount').textContent = excerpt.value.length;
});

// Form submission
document.getElementById('newsForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const submitBtn = document.getElementById('submitBtn');
    const newsId = document.getElementById('newsId').value;
    const isEdit = newsId !== '';
    
    // Validate form
    const title = document.getElementById('title').value.trim();
    const category = document.getElementById('category').value;
    const content = document.getElementById('content').value.trim();
    
    if (!title) {
        showAlert('Please enter a title', 'error');
        document.getElementById('title').focus();
        return;
    }
    
    if (!category) {
        showAlert('Please select a category', 'error');
        document.getElementById('category').focus();
        return;
    }
    
    if (!content) {
        showAlert('Please enter article content', 'error');
        document.getElementById('content').focus();
        return;
    }
    
    // Disable submit button
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
    
    // Create FormData
    const formData = new FormData(this);
    
    // Determine URL
    const url = isEdit ? `${window.BASE_URL||''}/admin/news/update/${newsId}` : `${window.BASE_URL||''}/admin/news/store`;
    
    try {
        console.log('Submitting to:', url);
        console.log('Is Edit:', isEdit);
        
        const response = await fetch(url, {
            method: 'POST',
            body: formData
        });
        
        console.log('Response status:', response.status);
        
        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
            const data = await response.json();
            console.log('Response data:', data);
            
            if (data.success) {
                showAlert(data.message, 'success');
                setTimeout(() => {
                    window.location.href=(window.BASE_URL||'')+'/admin/news';
                }, 1500);
            } else {
                showAlert('Error: ' + data.message, 'error');
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-save"></i> ' + (isEdit ? 'Update Article' : 'Create Article');
            }
        } else {
            const text = await response.text();
            console.error('Non-JSON response:', text);
            showAlert('Server returned an invalid response. Please check the error logs.', 'error');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-save"></i> ' + (isEdit ? 'Update Article' : 'Create Article');
        }
    } catch (error) {
        console.error('Fetch error:', error);
        showAlert('Network error: ' + error.message, 'error');
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-save"></i> ' + (isEdit ? 'Update Article' : 'Create Article');
    }
});

// Auto-resize content textarea
document.getElementById('content').addEventListener('input', function() {
    this.style.height = 'auto';
    this.style.height = (this.scrollHeight) + 'px';
});

// Form dirty check (warn before leaving if unsaved changes)
let formDirty = false;
document.querySelectorAll('#newsForm input, #newsForm textarea, #newsForm select').forEach(field => {
    field.addEventListener('change', () => formDirty = true);
});

window.addEventListener('beforeunload', function(e) {
    if (formDirty) {
        e.preventDefault();
        e.returnValue = '';
        return '';
    }
});

document.getElementById('newsForm').addEventListener('submit', function() {
    formDirty = false;
});
</script>
</body>
</html>