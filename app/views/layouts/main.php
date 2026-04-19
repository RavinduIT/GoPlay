<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? \Core\SiteSettings::get('site_name', 'GoPlay Sports Platform') ?></title>
    
    <!-- CSS -->
     <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/components/navbar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"> 
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/pages/news-index.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/pages/mobile-fixes.css">
    
    <?php if (isset($additionalCSS)): ?>
        <?php foreach ($additionalCSS as $css): ?>
            <link rel="stylesheet" href="<?= BASE_URL . $css ?>">
        <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- Meta tags -->
    <meta name="description" content="<?= $description ?? \Core\SiteSettings::get('site_description', 'GoPlay - Premier Sports Booking Platform') ?>">
    <meta name="keywords" content="sports, booking, facilities, coaches, equipment">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= BASE_URL ?>/public/assets/images/favicon.ico">

    <!-- Global BASE_URL for JavaScript -->
    <script>window.BASE_URL = '<?= BASE_URL ?>';</script>
</head>
<body>
    <?php
    // Detect admin/owner pages — skip public navbar and footer
    $_uri = $_SERVER['REQUEST_URI'] ?? '';
    $_isAdmin = strpos($_uri, '/admin') !== false;
    $_isOwnerPanel = strpos($_uri, '/shop-owner') !== false 
                  || strpos($_uri, '/ground-owner') !== false 
                  || strpos($_uri, '/coach/') !== false
                  || preg_match('#/coach$#', $_uri);
    $_hideNavFooter = $_isAdmin || $_isOwnerPanel;
    ?>
    
    <!-- Navigation (hidden on admin pages) -->
    <?php if (!$_hideNavFooter): ?>
        <?php include __DIR__ . '/../components/navbar.php'; ?>
    <?php endif; ?>
    
    <!-- Main Content -->
    <main id="main-content" <?= $_hideNavFooter ? 'style="padding:0;margin:0;max-width:none;"' : '' ?>>
        <?= $content ?? '' ?>
    </main>
    
    <!-- Footer (hidden on admin pages) -->
    <?php if (!$_hideNavFooter): ?>
        <?php include __DIR__ . '/../components/footer.php'; ?>
    <?php endif; ?>
    
   
    <?php if (isset($additionalJS)): ?>
        <?php foreach ($additionalJS as $js): ?>
            <script src="<?= BASE_URL . $js ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
