<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Admin Dashboard - GoPlay' ?></title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="/public/css/main.css">
    <link rel="stylesheet" href="/public/css/admin/admin-base.css">
    <?php if (isset($additionalCSS)): ?>
        <?php foreach ($additionalCSS as $css): ?>
            <link rel="stylesheet" href="<?= $css ?>">
        <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- Chart.js -->
    <script src="/public/lib/chart.min.js"></script>
</head>
<body class="admin-layout">
    <!-- Admin Sidebar -->
    <aside class="admin-sidebar">
        <div class="sidebar-header">
            <h2>GoPlay Admin</h2>
        </div>
        <nav class="sidebar-nav">
            <ul>
                <li><a href="/admin/dashboard">Dashboard</a></li>
                <li><a href="/admin/analytics">Analytics</a></li>
                <li><a href="/admin/coaches">Coaches</a></li>
                <li><a href="/admin/grounds">Grounds</a></li>
                <li><a href="/admin/shop">Shop</a></li>
                <li><a href="/admin/users">Users</a></li>
                <li><a href="/admin/settings">Settings</a></li>
            </ul>
        </nav>
    </aside>
    
    <!-- Admin Main Content -->
    <main class="admin-main">
        <!-- Admin Header -->
        <header class="admin-header">
            <h1><?= $pageTitle ?? 'Dashboard' ?></h1>
            <div class="admin-user">
                <span>Welcome, Admin</span>
                <a href="/logout">Logout</a>
            </div>
        </header>
        
        <!-- Admin Content -->
        <div class="admin-content">
            <?= $content ?? '' ?>
        </div>
    </main>
    
    <!-- JavaScript -->
    <script src="/public/js/main.js"></script>
    <?php if (isset($additionalJS)): ?>
        <?php foreach ($additionalJS as $js): ?>
            <script src="<?= $js ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>