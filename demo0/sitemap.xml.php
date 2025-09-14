<?php
header("Content-Type: application/xml; charset=utf-8");
require_once 'config/database.php';

// ดึง URL เว็บไซต์
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$domain = $protocol . "://" . $_SERVER['HTTP_HOST'];

// ดึงหน้าที่ active
$stmt = $pdo->query("SELECT page_slug, updated_at FROM pages WHERE is_active = 1 ORDER BY sort_order");
$pages = $stmt->fetchAll();

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
        http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
    
    <!-- Homepage -->
    <url>
        <loc><?= $domain ?>/</loc>
        <lastmod><?= date('Y-m-d') ?></lastmod>
        <changefreq>weekly</changefreq>
        <priority>1.0</priority>
    </url>
    
    <!-- Dynamic Pages -->
    <?php foreach($pages as $page): ?>
    <url>
        <loc><?= $domain ?>/<?= $page['page_slug'] ?></loc>
        <lastmod><?= date('Y-m-d', strtotime($page['updated_at'])) ?></lastmod>
        <changefreq><?= $page['page_slug'] == 'home' ? 'weekly' : 'monthly' ?></changefreq>
        <priority><?= $page['page_slug'] == 'home' ? '1.0' : '0.8' ?></priority>
    </url>
    <?php endforeach; ?>
</urlset>
