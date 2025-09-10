<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'GoPlay Sports Platform' ?></title>
    
    <!-- CSS -->
     <link rel="stylesheet" href="/public/css/components/navbar.css">
    <link rel="stylesheet" href="/public/css/main.css">
    <link rel="stylesheet" href="/public/css/components.css">
    <link rel="stylesheet" href="/public/css/pages.css">
    <link rel="stylesheet" href="/public/css/responsive.css">
    <link rel="stylesheet" href="/public/css/pages/news-index.css">
    
    <?php if (isset($additionalCSS)): ?>
        <?php foreach ($additionalCSS as $css): ?>
            <link rel="stylesheet" href="<?= $css ?>">
        <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- Meta tags -->
    <meta name="description" content="<?= $description ?? 'GoPlay - Premier Sports Booking Platform' ?>">
    <meta name="keywords" content="sports, booking, facilities, coaches, equipment">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/public/assets/images/favicon.ico">
</head>
<body>
    <!-- Navigation -->
    <?php include __DIR__ . '/../components/navbar.php'; ?>
    
    <!-- Main Content -->
    <main id="main-content">
        <?= $content ?? '' ?>
    </main>
    
    <!-- Footer -->
    <?php include __DIR__ . '/../components/footer.php'; ?>
    
    <!-- JavaScript -->
    <script src="/public/js/main.js"></script>
    <?php if (isset($additionalJS)): ?>
        <?php foreach ($additionalJS as $js): ?>
            <script src="<?= $js ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>