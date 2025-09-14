<?php
header("Content-type: text/css; charset: UTF-8");

// ป้องกันการเข้าถึงโดยตรง
$config_file = '../../config/database.php';
if (!file_exists($config_file)) {
    die('/* Configuration file not found */');
}

require_once $config_file;

// ดึงการตั้งค่า Theme
try {
    $theme = getThemeSettings($pdo);
} catch(Exception $e) {
    // ใช้ค่า default ถ้าเกิดข้อผิดพลาด
    $theme = [
        'theme_mode' => 'dark',
        'primary_color' => '#6366f1',
        'secondary_color' => '#8b5cf6',
        'accent_color' => '#ec4899',
        'bg_gradient_start' => '#0f172a',
        'bg_gradient_end' => '#1a1f3a',
        'bg_type' => 'gradient',
        'bg_image' => '',
        'bg_overlay_opacity' => '0.7',
        'font_family' => 'Inter',
        'heading_font' => 'Inter',
        'font_size_base' => '16px',
        'custom_css' => ''
    ];
}

// กำหนดค่า fallback
$primary = $theme['primary_color'] ?: '#6366f1';
$secondary = $theme['secondary_color'] ?: '#8b5cf6';
$accent = $theme['accent_color'] ?: '#ec4899';
$bgStart = $theme['bg_gradient_start'] ?: '#0f172a';
$bgEnd = $theme['bg_gradient_end'] ?: '#1a1f3a';
$fontFamily = $theme['font_family'] ?: 'Inter';
$headingFont = $theme['heading_font'] ?: 'Inter';
$fontSize = $theme['font_size_base'] ?: '16px';
$bgType = $theme['bg_type'] ?: 'gradient';
$bgImage = $theme['bg_image'] ?: '';
$bgOverlay = $theme['bg_overlay_opacity'] ?: '0.7';

// กำหนดสีตามโหมด
if($theme['theme_mode'] == 'light') {
    $textPrimary = '#1e293b';
    $textSecondary = '#475569';
    $darkSurface = '#ffffff';
    $darkBg = '#f8fafc';
    $borderColor = '#e2e8f0';
} else {
    $textPrimary = '#f1f5f9';
    $textSecondary = '#94a3b8';
    $darkSurface = '#1e293b';
    $darkBg = '#0f172a';
    $borderColor = '#334155';
}

// Function to convert hex to RGB
function hexToRgb($hex) {
    $hex = str_replace('#', '', $hex);
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    return "$r, $g, $b";
}

// Google Fonts map
$fonts_map = [
    'Inter' => 'Inter:wght@400;500;600;700',
    'Roboto' => 'Roboto:wght@400;500;700',
    'Open Sans' => 'Open+Sans:wght@400;600;700',
    'Lato' => 'Lato:wght@400;700',
    'Montserrat' => 'Montserrat:wght@400;600;700;800',
    'Poppins' => 'Poppins:wght@400;500;600;700',
    'Raleway' => 'Raleway:wght@400;600;700',
    'Playfair Display' => 'Playfair+Display:wght@400;700;900',
    'Bebas Neue' => 'Bebas+Neue',
    'Oswald' => 'Oswald:wght@400;600;700',
    'Quicksand' => 'Quicksand:wght@400;600;700',
    'Kanit' => 'Kanit:wght@400;500;600;700',
    'Prompt' => 'Prompt:wght@400;500;600;700',
    'Sarabun' => 'Sarabun:wght@400;600;700',
    'Noto Sans Thai' => 'Noto+Sans+Thai:wght@400;500;700'
];

$fontUrl = isset($fonts_map[$fontFamily]) ? $fonts_map[$fontFamily] : 'Inter:wght@400;500;600;700';
$headingFontUrl = isset($fonts_map[$headingFont]) ? $fonts_map[$headingFont] : 'Inter:wght@400;500;600;700';
?>

/* Import Google Fonts */
@import url('https://fonts.googleapis.com/css2?family=<?= $fontUrl ?>&display=swap');
<?php if($headingFont != $fontFamily): ?>
@import url('https://fonts.googleapis.com/css2?family=<?= $headingFontUrl ?>&display=swap');
<?php endif; ?>

/* CSS Variables */
:root {
    --primary-color: <?= $primary ?>;
    --secondary-color: <?= $secondary ?>;
    --accent-color: <?= $accent ?>;
    --dark-bg: <?= $darkBg ?>;
    --dark-surface: <?= $darkSurface ?>;
    --text-primary: <?= $textPrimary ?>;
    --text-secondary: <?= $textSecondary ?>;
    --border-color: <?= $borderColor ?>;
    --success: #10b981;
    --warning: #f59e0b;
    --error: #ef4444;
    --font-family: '<?= $fontFamily ?>', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    --heading-font: '<?= $headingFont ?>', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    --font-size-base: <?= $fontSize ?>;
    
    /* RGB values for opacity */
    --primary-rgb: <?= hexToRgb($primary) ?>;
    --secondary-rgb: <?= hexToRgb($secondary) ?>;
    --accent-rgb: <?= hexToRgb($accent) ?>;
}

/* Body Background Styles */
body {
    font-family: var(--font-family);
    color: var(--text-primary);
    font-size: var(--font-size-base);
    min-height: 100vh;
    position: relative;
    
    <?php if($bgType == 'gradient'): ?>
    /* Gradient Background */
    background: linear-gradient(135deg, <?= $bgStart ?> 0%, <?= $bgEnd ?> 100%);
    background-attachment: fixed;
    
    <?php elseif($bgType == 'solid'): ?>
    /* Solid Color Background */
    background: <?= $bgStart ?>;
    
    <?php elseif($bgType == 'image' && $bgImage): ?>
    /* Image Background */
    background: url('..<?= $bgImage ?>') center/cover fixed;
    
    <?php elseif($bgType == 'pattern'): ?>
    /* Pattern Background */
    background-color: <?= $bgStart ?>;
    background-image: 
        repeating-linear-gradient(45deg, transparent, transparent 35px, rgba(<?= hexToRgb($bgEnd) ?>, 0.1) 35px, rgba(<?= hexToRgb($bgEnd) ?>, 0.1) 70px),
        repeating-linear-gradient(-45deg, transparent, transparent 35px, rgba(<?= hexToRgb($primary) ?>, 0.05) 35px, rgba(<?= hexToRgb($primary) ?>, 0.05) 70px);
    <?php endif; ?>
}

<?php if($bgType == 'image' && $bgImage): ?>
/* Image Background Overlay */
body::before {
    content: '';
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, 
        rgba(<?= hexToRgb($bgStart) ?>, <?= $bgOverlay ?>) 0%, 
        rgba(<?= hexToRgb($bgEnd) ?>, <?= $bgOverlay ?>) 100%);
    pointer-events: none;
    z-index: 0;
}

body > * {
    position: relative;
    z-index: 1;
}
<?php endif; ?>

/* Typography */
h1, h2, h3, h4, h5, h6 {
    font-family: var(--heading-font);
    font-weight: 700;
    line-height: 1.2;
}

h1 {
    font-size: calc(var(--font-size-base) * 2.5);
    margin-bottom: 1.5rem;
}

h2 {
    font-size: calc(var(--font-size-base) * 1.875);
    margin: 2rem 0 1rem;
}

h3 {
    font-size: calc(var(--font-size-base) * 1.5);
    margin: 1.5rem 0 1rem;
}

h4 {
    font-size: calc(var(--font-size-base) * 1.25);
    margin: 1rem 0 0.75rem;
}

p {
    font-size: var(--font-size-base);
    line-height: calc(var(--font-size-base) * 1.8);
    margin-bottom: 1rem;
}

/* Dynamic Button Styles */
.btn {
    font-family: var(--font-family);
    font-size: calc(var(--font-size-base) * 0.9375);
}

.btn-primary {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    box-shadow: 0 4px 6px -1px rgba(var(--primary-rgb), 0.3);
}

.btn-primary:hover {
    box-shadow: 0 10px 15px -3px rgba(var(--primary-rgb), 0.4);
    transform: translateY(-2px);
}

.btn-secondary {
    background: var(--dark-surface);
    border: 2px solid var(--border-color);
}

.btn-secondary:hover {
    background: var(--border-color);
    border-color: var(--primary-color);
}

/* Content Cards */
.content-card {
    background: var(--dark-surface);
    border: 1px solid var(--border-color);
    <?php if($theme['theme_mode'] == 'light'): ?>
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    <?php else: ?>
    background: rgba(30, 41, 59, 0.8);
    backdrop-filter: blur(10px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3);
    <?php endif; ?>
}

/* Header */
header {
    <?php if($theme['theme_mode'] == 'light'): ?>
    background: rgba(255, 255, 255, 0.98);
    backdrop-filter: blur(10px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    <?php else: ?>
    background: rgba(15, 23, 42, 0.95);
    backdrop-filter: blur(10px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3);
    <?php endif; ?>
}

/* Footer */
footer {
    <?php if($theme['theme_mode'] == 'light'): ?>
    background: white;
    <?php else: ?>
    background: var(--dark-surface);
    <?php endif; ?>
}

/* Form Elements */
input[type="text"],
input[type="email"],
input[type="password"],
input[type="tel"],
input[type="number"],
textarea,
select {
    font-family: var(--font-family);
    font-size: var(--font-size-base);
    background: var(--dark-bg);
    color: var(--text-primary);
    border-color: var(--border-color);
}

input:focus,
textarea:focus,
select:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(var(--primary-rgb), 0.1);
}

/* Tables */
.table-container {
    background: var(--dark-surface);
}

th {
    background: var(--dark-bg);
    color: var(--text-primary);
}

td {
    color: var(--text-secondary);
}

tr:hover {
    background: rgba(var(--primary-rgb), 0.05);
}

/* Admin Sidebar */
.admin-sidebar {
    background: var(--dark-surface);
    <?php if($theme['theme_mode'] == 'light'): ?>
    background: white;
    <?php endif; ?>
}

.admin-nav a:hover,
.admin-nav a.active {
    background: var(--dark-bg);
    color: var(--primary-color);
    border-left-color: var(--primary-color);
}

/* Alerts */
.alert-success {
    background: rgba(16, 185, 129, 0.1);
    border-color: var(--success);
    color: var(--success);
}

.alert-error {
    background: rgba(239, 68, 68, 0.1);
    border-color: var(--error);
    color: var(--error);
}

.alert-warning {
    background: rgba(245, 158, 11, 0.1);
    border-color: var(--warning);
    color: var(--warning);
}

/* Light Mode Specific */
<?php if($theme['theme_mode'] == 'light'): ?>
code {
    background: #f1f5f9;
    color: #0f172a;
}

.dashboard-card {
    background: white;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.slider-card {
    background: white;
}

.gallery-item {
    background: white;
}
<?php endif; ?>

/* Custom CSS from Admin */
<?= $theme['custom_css'] ?: '' ?>
