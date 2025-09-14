<?php
require_once 'config/config.php';
require_once 'config/database.php';

// ดึงข้อมูลการตั้งค่า
$settings = getSiteSettings($pdo);

// ดึงเนื้อหาหน้าแรก
$page = getPageContent($pdo, 'home');
if (!$page) {
    header('Location: /404.php');
    exit;
}

// ดึง Gallery images สำหรับหน้าแรก (แสดง 8 รูปล่าสุด)
$stmt = $pdo->query("SELECT * FROM gallery WHERE is_active = 1 ORDER BY id DESC LIMIT 8");
$gallery_images = $stmt->fetchAll();

// SEO Meta Tags
$meta_title = $page['meta_title'] ?: $page['page_title'] . ' - ' . $settings['site_name'];
$meta_description = $page['meta_description'] ?: $settings['meta_description'];
$meta_keywords = $page['meta_keywords'] ?: $settings['meta_keywords'];
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($meta_title) ?></title>
    <meta name="description" content="<?= htmlspecialchars($meta_description) ?>">
    <meta name="keywords" content="<?= htmlspecialchars($meta_keywords) ?>">
    
    <!-- Open Graph Tags -->
    <meta property="og:title" content="<?= htmlspecialchars($meta_title) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($meta_description) ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= htmlspecialchars($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>">
    
    <!-- CSS -->
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/dynamic-styles.php') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <style>
        /* Gallery Styles */
        .gallery-section {
            padding: 4rem 0;
            background: var(--dark-surface);
            margin: 3rem 0;
            border-radius: 16px;
        }
        
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }
        
        .gallery-item {
            position: relative;
            border-radius: 12px;
            overflow: hidden;
            aspect-ratio: 1;
            background: var(--dark-bg);
            cursor: pointer;
            transition: transform 0.3s ease;
        }
        
        .gallery-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        
        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .gallery-item:hover img {
            transform: scale(1.1);
        }
        
        .gallery-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
            padding: 1.5rem 1rem 1rem;
            transform: translateY(100%);
            transition: transform 0.3s ease;
        }
        
        .gallery-item:hover .gallery-overlay {
            transform: translateY(0);
        }
        
        .gallery-title {
            color: white;
            font-size: 0.9rem;
            font-weight: 600;
        }
        
        .section-header {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .section-header h2 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .section-header p {
            font-size: 1.125rem;
            color: var(--text-secondary);
        }
        
        .view-all-btn {
            text-align: center;
            margin-top: 2rem;
        }
        
        /* Hero without slider styles */
        .hero-section {
            padding: 4rem 0;
            text-align: center;
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.1), rgba(236, 72, 153, 0.1));
            border: 1px solid rgba(99, 102, 241, 0.3);
            border-radius: 16px;
            margin-bottom: 3rem;
        }
        
        @media (max-width: 768px) {
            .gallery-grid {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
                gap: 1rem;
            }
            
            .section-header h2 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <!-- Image Slider (ถ้ามี) -->
    <?php 
    // Check if sliders exist
    $stmt = $pdo->query("SELECT COUNT(*) FROM sliders WHERE is_active = 1");
    $has_sliders = $stmt->fetchColumn() > 0;
    
    if($has_sliders) {
        include 'includes/slider.php';
    }
    ?>
    
    <main>
        <div class="container">
            <!-- Hero Section (ถ้าไม่มี slider) -->
            <?php if(!$has_sliders): ?>
            <div class="hero-section content-card">
                <h1 style="font-size: 3rem; margin-bottom: 1rem;">
                    <?= htmlspecialchars($settings['site_name']) ?>
                </h1>
                <p style="font-size: 1.25rem; color: var(--text-secondary); margin-bottom: 2rem;">
                    <?= htmlspecialchars($settings['site_tagline']) ?>
                </p>
                <div>
                    <a href="<?= url('services') ?>" class="btn btn-primary" style="margin-right: 1rem;">ดูบริการของเรา</a>
                    <a href="<?= url('contact') ?>" class="btn btn-secondary">ติดต่อเรา</a>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Main Content -->
            <div class="content-card">
                <?= $page['page_content'] ?>
            </div>
            
            <!-- Features Grid -->
            <div class="dashboard-cards">
                <div class="content-card" style="text-align: center;">
                    <div style="font-size: 3rem; margin-bottom: 1rem;">💪</div>
                    <h3>อุปกรณ์ทันสมัย</h3>
                    <p>นำเข้าจากแบรนด์ชั้นนำระดับโลก</p>
                </div>
                <div class="content-card" style="text-align: center;">
                    <div style="font-size: 3rem; margin-bottom: 1rem;">👥</div>
                    <h3>เทรนเนอร์มืออาชีพ</h3>
                    <p>ผ่านการรับรองมาตรฐานสากล</p>
                </div>
                <div class="content-card" style="text-align: center;">
                    <div style="font-size: 3rem; margin-bottom: 1rem;">🏃</div>
                    <h3>คลาสหลากหลาย</h3>
                    <p>Yoga, Zumba, Boxing และอื่นๆ</p>
                </div>
                <div class="content-card" style="text-align: center;">
                    <div style="font-size: 3rem; margin-bottom: 1rem;">⏰</div>
                    <h3>เปิดทุกวัน</h3>
                    <p>บริการตั้งแต่เช้าถึงดึก</p>
                </div>
            </div>
            
            <!-- Gallery Section -->
            <?php if(count($gallery_images) > 0): ?>
            <div class="gallery-section">
                <div class="section-header">
                    <h2>📸 แกลเลอรี</h2>
                    <p>ภาพบรรยากาศภายในศูนย์ฟิตเนสของเรา</p>
                </div>
                
                <div class="gallery-grid">
                    <?php foreach($gallery_images as $image): ?>
                    <div class="gallery-item" onclick="openLightbox('<?= htmlspecialchars($image['image_path']) ?>')">
                        <img src="/demo0/<?= htmlspecialchars($image['image_path']) ?>" 
                             alt="<?= htmlspecialchars($image['title'] ?: 'Gallery Image') ?>"
                             loading="lazy">
                        <?php if($image['title']): ?>
                        <div class="gallery-overlay">
                            <div class="gallery-title"><?= htmlspecialchars($image['title']) ?></div>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="view-all-btn">
                    <a href="<?= url('gallery') ?>" class="btn btn-primary">
                        ดูรูปภาพทั้งหมด →
                    </a>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </main>
    
    <!-- Lightbox Modal -->
    <div id="lightbox" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.9); z-index: 9999; cursor: pointer;" onclick="closeLightbox()">
        <img id="lightbox-img" src="" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); max-width: 90%; max-height: 90%; border-radius: 8px;">
        <span style="position: absolute; top: 20px; right: 40px; color: white; font-size: 40px; cursor: pointer;">&times;</span>
    </div>
    
    <?php include 'includes/footer.php'; ?>
    
    <script src="<?= asset('js/main.js') ?>"></script>
    <script>
        // Lightbox functions
        function openLightbox(imageSrc) {
            document.getElementById('lightbox').style.display = 'block';
            document.getElementById('lightbox-img').src = imageSrc;
            document.body.style.overflow = 'hidden';
        }
        
        function closeLightbox() {
            document.getElementById('lightbox').style.display = 'none';
            document.body.style.overflow = 'auto';
        }
        
        // ESC key to close lightbox
        document.addEventListener('keydown', function(e) {
            if(e.key === 'Escape') {
                closeLightbox();
            }
        });
    </script>
</body>
</html>
