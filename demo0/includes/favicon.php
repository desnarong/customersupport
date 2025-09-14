<?php
// Favicon and PWA meta tags
// Include this file in the <head> section of all pages
?>
<!-- Favicon -->
<link rel="icon" type="image/x-icon" href="/assets/images/favicon.ico">
<link rel="icon" type="image/png" sizes="32x32" href="/assets/images/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/assets/images/favicon-16x16.png">
<link rel="apple-touch-icon" sizes="180x180" href="/assets/images/apple-touch-icon.png">
<link rel="manifest" href="/manifest.json">

<!-- PWA & Mobile -->
<meta name="theme-color" content="#6366f1">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="<?= htmlspecialchars($settings['site_name']) ?>">
<meta name="application-name" content="<?= htmlspecialchars($settings['site_name']) ?>">
<meta name="msapplication-TileColor" content="#6366f1">
<meta name="msapplication-config" content="/browserconfig.xml">

<!-- Additional Meta Tags -->
<meta name="format-detection" content="telephone=no">
<meta name="robots" content="index, follow">
<meta name="googlebot" content="index, follow">
<meta name="author" content="<?= htmlspecialchars($settings['site_name']) ?>">
<link rel="canonical" href="<?= htmlspecialchars($_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>">

<!-- Social Media Preview -->
<meta property="og:site_name" content="<?= htmlspecialchars($settings['site_name']) ?>">
<meta property="og:locale" content="th_TH">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:site" content="@fitlife">

<!-- Preconnect for Performance -->
<link rel="dns-prefetch" href="//fonts.googleapis.com">
<link rel="dns-prefetch" href="//fonts.gstatic.com">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
