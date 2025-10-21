<?php
$news = $data['news'] ?? [];
$stats = $data['stats'] ?? [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage News - Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/public/css/pages/admin-dashboard.css">
    <link rel="stylesheet" href="/public/css/pages/admin-news.css">
</head>
<body>

<div class="admin-dashboard">
    <?php 
    // Set active page and optional badge counts
    $activePage = 'news';
    $newsCount = count($news);
    $userCount = 892;
    $groundCount = 45;
    $coachCount = 23;
    $shopCount = 156;
    
    // Include the reusable sidebar component
    include __DIR__ . '/../../components/admin-sidebar.php';
    ?>

    <!-- Main Content -->
    <main class="admin-main">
        <!-- Top Header -->
        <header class="admin-header">
            <div class="header-left">
                <h1 class="page-title">Manage News Articles</h1>
            </div>
            <div class="header-right">
                <a href="/admin/news/create" class="btn-primary">
                    <i class="fas fa-plus"></i> Create News Article
                </a>
            </div>
        </header>

        <!-- Dashboard Content -->
        <div class="dashboard-content">
            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon blue">
                        <i class="fas fa-newspaper"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Total Articles</h3>
                        <p class="stat-number"><?= $stats['total_articles'] ?? 0 ?></p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon green">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Published</h3>
                        <p class="stat-number"><?= $stats['published_articles'] ?? 0 ?></p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon orange">
                        <i class="fas fa-edit"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Drafts</h3>
                        <p class="stat-number"><?= $stats['draft_articles'] ?? 0 ?></p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon purple">
                        <i class="fas fa-eye"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Total Views</h3>
                        <p class="stat-number"><?= number_format($stats['total_views'] ?? 0) ?></p>
                    </div>
                </div>
            </div>

            <!-- News Table -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h3>All News Articles</h3>
                    <div class="filter-group">
                        <select id="statusFilter" onchange="filterNews()">
                            <option value="all">All Status</option>
                            <option value="published">Published</option>
                            <option value="draft">Draft</option>
                            <option value="archived">Archived</option>
                        </select>
                        <select id="categoryFilter" onchange="filterNews()">
                            <option value="all">All Categories</option>
                            <option value="Cricket">Cricket</option>
                            <option value="Football">Football</option>
                            <option value="Basketball">Basketball</option>
                            <option value="Tennis">Tennis</option>
                            <option value="Swimming">Swimming</option>
                            <option value="General Sports">General Sports</option>
                        </select>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="news-table">
                        <thead>
                            <tr>
                                <th>Featured Image</th>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Author</th>
                                <th>Status</th>
                                <th>Views</th>
                                <th>Published</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="newsTableBody">
                            <?php if (empty($news)): ?>
                            <tr>
                                <td colspan="8" class="text-center">
                                    <p style="padding: 40px 0;">No news articles found. <a href="/admin/news/create">Create your first article</a></p>
                                </td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($news as $article): ?>
                            <tr data-status="<?= htmlspecialchars($article['status']) ?>" data-category="<?= htmlspecialchars($article['category']) ?>">
                                <td>
                                    <img src="<?= htmlspecialchars($article['featured_image'] ?? '/public/assets/images/placeholder-news.svg') ?>" 
                                         alt="<?= htmlspecialchars($article['title']) ?>" 
                                         class="news-thumbnail">
                                </td>
                                <td>
                                    <div class="news-title-cell">
                                        <strong><?= htmlspecialchars($article['title']) ?></strong>
                                        <small><?= htmlspecialchars(substr($article['excerpt'] ?? '', 0, 80)) ?>...</small>
                                    </div>
                                </td>
                                <td>
                                    <span class="category-badge"><?= htmlspecialchars($article['category']) ?></span>
                                </td>
                                <td>
                                    <?php 
                                    $authorName = 'Unknown';
                                    if (!empty($article['first_name']) || !empty($article['last_name'])) {
                                        $authorName = trim($article['first_name'] . ' ' . $article['last_name']);
                                    }
                                    echo htmlspecialchars($authorName);
                                    ?>
                                </td>
                                <td>
                                    <span class="status-badge status-<?= htmlspecialchars($article['status']) ?>">
                                        <?= ucfirst(htmlspecialchars($article['status'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="views-count">
                                        <i class="fas fa-eye"></i> <?= number_format($article['views'] ?? 0) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($article['published_at']): ?>
                                        <?= date('M j, Y', strtotime($article['published_at'])) ?>
                                    <?php else: ?>
                                        <span class="text-muted">Not published</span>
                                    <?php endif; ?>
                                </td>
                                <td class="actions-cell">
                                    <a href="/news/<?= htmlspecialchars($article['slug']) ?>" 
                                       class="btn-icon" 
                                       title="View" 
                                       target="_blank">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="/admin/news/edit/<?= $article['id'] ?>" 
                                       class="btn-icon btn-edit" 
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button onclick="deleteNews(<?= $article['id'] ?>)" 
                                            class="btn-icon btn-delete" 
                                            title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>

<script src="/public/js/pages/admin-news.js"></script>
<script>
function filterNews() {
    const statusFilter = document.getElementById('statusFilter').value;
    const categoryFilter = document.getElementById('categoryFilter').value;
    const rows = document.querySelectorAll('#newsTableBody tr[data-status]');
    
    rows.forEach(row => {
        const status = row.getAttribute('data-status');
        const category = row.getAttribute('data-category');
        
        const statusMatch = statusFilter === 'all' || status === statusFilter;
        const categoryMatch = categoryFilter === 'all' || category === categoryFilter;
        
        if (statusMatch && categoryMatch) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

function deleteNews(id) {
    if (!confirm('Are you sure you want to delete this news article? This action cannot be undone.')) {
        return;
    }
    
    fetch(`/admin/news/delete/${id}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while deleting the news article');
    });
}
</script>
</body>
</html>