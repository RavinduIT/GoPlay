<?php
$_base = defined('BASE_URL') ? BASE_URL : '';
// This file serves as both create.php and edit.php
// For edit.php, $data['news'] will be populated
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
            <div class="news-form-container">
                <form id="newsForm" enctype="multipart/form-data">
                    <input type="hidden" id="newsId" value="<?= $isEdit ? $news['id'] : '' ?>">
                    
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
                                      placeholder="Brief summary of the article (optional - will be auto-generated from content if left empty)"><?= $isEdit ? htmlspecialchars($news['excerpt'] ?? '') : '' ?></textarea>
                            <small class="form-text">Maximum 200 characters recommended</small>
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
                                   accept="image/*"
                                   onchange="previewImage(this)">
                            <small class="form-text">Recommended size: 1200x630px. Max file size: 5MB</small>
                            
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
function toggleSidebar() {
    document.getElementById('adminSidebar').classList.toggle('active');
}

function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.innerHTML = `
                <img src="${e.target.result}" alt="Preview">
                <p class="preview-text">New image preview</p>
            `;
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}

document.getElementById('newsForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const submitBtn = document.getElementById('submitBtn');
    const newsId = document.getElementById('newsId').value;
    const isEdit = newsId !== '';
    
    // Disable submit button
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
    
    // Create FormData
    const formData = new FormData();
    formData.append('title', document.getElementById('title').value);
    formData.append('category', document.getElementById('category').value);
    formData.append('status', document.getElementById('status').value);
    formData.append('excerpt', document.getElementById('excerpt').value);
    formData.append('content', document.getElementById('content').value);
    
    // Add file if selected
    const fileInput = document.getElementById('featured_image');
    if (fileInput.files.length > 0) {
        formData.append('featured_image', fileInput.files[0]);
    }
    
    // Determine URL and method
    const url = isEdit ? `${window.BASE_URL||''}/admin/news/update/${newsId}` : `${window.BASE_URL||''}/admin/news/store`;
    const method = 'POST';
    
    if (isEdit) {
        formData.append('id', newsId);
    }
    
    try {
        const response = await fetch(url, {
            method: method,
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert(data.message);
            window.location.href=(window.BASE_URL||'')+'/admin/news';
        } else {
            alert('Error: ' + data.message);
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-save"></i> ' + (isEdit ? 'Update Article' : 'Create Article');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred while saving the article');
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-save"></i> ' + (isEdit ? 'Update Article' : 'Create Article');
    }
});

// Auto-resize textarea
document.getElementById('content').addEventListener('input', function() {
    this.style.height = 'auto';
    this.style.height = (this.scrollHeight) + 'px';
});

// Character counter for excerpt
document.getElementById('excerpt').addEventListener('input', function() {
    const maxLength = 200;
    const currentLength = this.value.length;
    
    if (currentLength > maxLength) {
        this.value = this.value.substring(0, maxLength);
    }
});
</script>