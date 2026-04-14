<?php
$files = [
    __DIR__ . '/../app/views/provider/ground-owner-form.php',
    __DIR__ . '/../app/views/provider/shop-owner-form.php',
];

foreach ($files as $file) {
    $content = file_get_contents($file);
    
    // Fix: add missing "<?php endif; ?>" and "</head>" 
    // The broken pattern has endforeach followed directly by script then body (no endif, no /head)
    $search = "endforeach; ?" . ">\r\n    \r\n    <script>";
    $replace = "endforeach; ?" . ">\r\n    <" . "?php endif; ?" . ">\r\n    <script>";
    $content = str_replace($search, $replace, $content);
    
    // Fix missing </head> before <body>
    $content = str_replace("</script>\r\n\r\n<body>", "</script>\r\n</head>\r\n<body>", $content);
    
    file_put_contents($file, $content);
    echo "Fixed: " . basename($file) . "\n";
}
echo "Done!\n";
