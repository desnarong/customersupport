<?php
require_once 'config/config.php';
require_once 'config/database.php';

// ดึงข้อมูลการตั้งค่า
$settings = getSiteSettings($pdo);

// ดึงเนื้อหาหน้าแรก
$page = getPageContent($pdo, 'home');
if (!$page) {
    // สร้างเนื้อหาเริ่มต้นถ้าไม่มี
    $page = [
        'page_title' => 'หน้าแรก',
        'page_content' => '<h1>Welcome to Modern Fitness</h1>',
        'meta_title' => 'Modern Fitness - Your Journey Starts Here',
        'meta_description' => 'Premium fitness center with state-of-the-art equipment and professional trainers',
        'meta_keywords' => 'fitness, gym, training, health'
    ];
}

// ดึง Sliders
$stmt = $pdo->query("SELECT * FROM sliders WHERE is_active = 1 ORDER BY sort_order, id");
$sliders = $stmt->fetchAll();

// ดึง Gallery 
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
    
    <!-- Open Graph -->
    <meta property="og:title" content="<?= htmlspecialchars($meta_title) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($meta_description) ?>">
    <meta property="og:type" content="website">
    
    <!-- CSS -->
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/modern-fitness.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/dynamic-styles.php') ?>">
</head>
<body>
    <!-- Loading Screen -->
    <div class="loading" id="loading">
        <div class="loading-spinner"></div>
    </div>

    <!-- Header -->
    <header id="header">
        <div class="container">
            <div class="header-content">
                <a href="<?= url('/') ?>" class="logo">
                    <span style="font-size: 2.5rem;">💪</span>
                    <h1><?= htmlspecialchars($settings['site_name']) ?></h1>
                </a>
                
                <nav>
                    <ul>
                        <?php
                        $menu_items = getMenuItems($pdo);
                        foreach($menu_items as $item): 
                        ?>
                        <li>
                            <a href="<?= url($item['page_slug']) ?>"><?= htmlspecialchars($item['menu_title']) ?></a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </nav>
                
                <button class="menu-toggle" onclick="toggleMenu()">☰</button>
            </div>
        </div>
    </header>

    <!-- Hero Slider -->
    <?php if(count($sliders) > 0): ?>
    <section class="hero-slider">
        <?php foreach($sliders as $index => $slider): ?>
        <div class="slide <?= $index === 0 ? 'active' : '' ?>">
            <div class="slide-bg" style="background-image: url('<?= htmlspecialchars($slider['image_path']) ?>');"></div>
            <div class="slide-content">
                <h1 class="slide-title"><?= htmlspecialchars($slider['title']) ?></h1>
                <?php if($slider['subtitle']): ?>
                <p class="slide-subtitle"><?= htmlspecialchars($slider['subtitle']) ?></p>
                <?php endif; ?>
                <?php if($slider['button_text'] && $slider['button_link']): ?>
                <a href="<?= htmlspecialchars($slider['button_link']) ?>" class="btn btn-primary">
                    <?= htmlspecialchars($slider['button_text']) ?>
                </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
        
        <!-- Slider Controls -->
        <?php if(count($sliders) > 1): ?>
        <button class="slider-prev" onclick="changeSlide(-1)">‹</button>
        <button class="slider-next" onclick="changeSlide(1)">›</button>
        <div class="slider-dots">
            <?php foreach($sliders as $index => $slider): ?>
            <span class="dot <?= $index === 0 ? 'active' : '' ?>" onclick="currentSlide(<?= $index ?>)"></span>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </section>
    <?php else: ?>
    <!-- Hero Section (No Slider) -->
    <section class="hero-slider" style="height: 500px; display: flex; align-items: center; background: var(--gradient-hero);">
        <div class="container" style="text-align: center; color: white;">
            <h1 class="impact-heading" style="font-size: 4rem; margin-bottom: 1rem;">
                TRANSFORM YOUR BODY
            </h1>
            <p style="font-size: 1.5rem; margin-bottom: 2rem;">
                <?= htmlspecialchars($settings['site_tagline']) ?>
            </p>
            <a href="<?= url('services') ?>" class="btn btn-primary">GET STARTED</a>
        </div>
    </section>
    <?php endif; ?>

    <!-- Main Content -->
    <main>
        <div class="container">
            <!-- Features Section -->
            <section style="padding: 5rem 0;">
                <div class="section-header">
                    <h2 class="impact-heading">Why Choose Us</h2>
                    <p style="font-size: 1.125rem; color: var(--text-light);">
                        Experience the difference with our premium facilities
                    </p>
                </div>
                
                <div class="features-grid">
                    <div class="feature-card">
                        <div class="feature-icon">🏋️</div>
                        <h3>Modern Equipment</h3>
                        <p>State-of-the-art fitness equipment imported from leading brands worldwide</p>
                    </div>
                    
                    <div class="feature-card">
                        <div class="feature-icon">👨‍🏫</div>
                        <h3>Expert Trainers</h3>
                        <p>Certified professional trainers with years of experience</p>
                    </div>
                    
                    <div class="feature-card">
                        <div class="feature-icon">🎯</div>
                        <h3>Personal Programs</h3>
                        <p>Customized workout plans designed for your specific goals</p>
                    </div>
                    
                    <div class="feature-card">
                        <div class="feature-icon">⏰</div>
                        <h3>Flexible Hours</h3>
                        <p>Open early morning to late night, 7 days a week</p>
                    </div>
                </div>
            </section>

            <!-- CTA Section -->
            <section style="padding: 4rem 0;">
                <div style="background: var(--gradient-cta); border-radius: 30px; padding: 4rem 2rem; text-align: center; color: white;">
                    <h2 class="impact-heading" style="font-size: 3rem; margin-bottom: 1rem;">
                        START YOUR FITNESS JOURNEY TODAY
                    </h2>
                    <p style="font-size: 1.25rem; margin-bottom: 2rem; opacity: 0.9;">
                        Join thousands of members who have transformed their lives
                    </p>
                    <a href="<?= url('contact') ?>" class="btn" style="background: white; color: var(--fitness-secondary);">
                        JOIN NOW
                    </a>
                </div>
            </section>

            <!-- Gallery Section -->
            <?php if(count($gallery_images) > 0): ?>
            <section class="gallery-section" style="margin: 0 -2rem; padding: 5rem 2rem;">
                <div class="section-header">
                    <h2 class="impact-heading">Our Gallery</h2>
                    <p>Take a look at our world-class facilities</p>
                </div>
                
                <div class="gallery-grid">
                    <?php foreach($gallery_images as $image): ?>
                    <div class="gallery-item" onclick="openLightbox('<?= htmlspecialchars($image['image_path']) ?>')">
                        <img src="<?= htmlspecialchars($image['image_path']) ?>" 
                             alt="<?= htmlspecialchars($image['title'] ?: 'Gallery') ?>" 
                             loading="lazy">
                        <?php if($image['title']): ?>
                        <div class="gallery-overlay">
                            <h4 style="color: white; margin: 0;"><?= htmlspecialchars($image['title']) ?></h4>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <div style="text-align: center; margin-top: 3rem;">
                    <a href="<?= url('gallery') ?>" class="btn btn-primary">VIEW ALL PHOTOS</a>
                </div>
            </section>
            <?php endif; ?>

            <!-- Custom Content from Database -->
            <?php if($page['page_content']): ?>
            <section style="padding: 3rem 0;">
                <div class="content-card">
                    <?= $page['page_content'] ?>
                </div>
            </section>
            <?php endif; ?>
        </div>
    </main>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3><?= htmlspecialchars($settings['site_name']) ?></h3>
                    <p><?= htmlspecialchars($settings['site_tagline']) ?></p>
                    <div class="social-links">
                        <?php if($settings['facebook_url']): ?>
                        <a href="<?= htmlspecialchars($settings['facebook_url']) ?>" target="_blank">f</a>
                        <?php endif; ?>
                        <?php if($settings['instagram_url']): ?>
                        <a href="<?= htmlspecialchars($settings['instagram_url']) ?>" target="_blank">📷</a>
                        <?php endif; ?>
                        <?php if($settings['line_id']): ?>
                        <a href="https://line.me/R/ti/p/<?= htmlspecialchars($settings['line_id']) ?>" target="_blank">L</a>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="footer-section">
                    <h3>Quick Links</h3>
                    <?php foreach($menu_items as $item): ?>
                    <a href="<?= url($item['page_slug']) ?>" style="display: block; color: rgba(255,255,255,0.7); margin-bottom: 0.5rem;">
                        <?= htmlspecialchars($item['menu_title']) ?>
                    </a>
                    <?php endforeach; ?>
                </div>
                
                <div class="footer-section">
                    <h3>Contact Info</h3>
                    <p>📍 <?= nl2br(htmlspecialchars($settings['address'])) ?></p>
                    <p>📞 <?= htmlspecialchars($settings['phone']) ?></p>
                    <p>✉️ <?= htmlspecialchars($settings['email']) ?></p>
                </div>
                
                <div class="footer-section">
                    <h3>Opening Hours</h3>
                    <p>Monday - Friday: 06:00 - 22:00</p>
                    <p>Saturday - Sunday: 08:00 - 20:00</p>
                </div>
            </div>
            
            <div style="text-align: center; padding-top: 2rem; margin-top: 2rem; border-top: 1px solid rgba(255,255,255,0.1);">
                <p style="color: rgba(255,255,255,0.5);">
                    &copy; <?= date('Y') ?> <?= htmlspecialchars($settings['site_name']) ?>. All rights reserved.
                </p>
            </div>
        </div>
    </footer>

    <!-- Lightbox -->
    <div id="lightbox" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.95); z-index: 9999; cursor: pointer;" onclick="closeLightbox()">
        <img id="lightbox-img" src="" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); max-width: 90%; max-height: 90%; border-radius: 10px;">
        <span style="position: absolute; top: 20px; right: 40px; color: white; font-size: 40px;">&times;</span>
    </div>

    <!-- Scripts -->
    <script>
        // Remove loading screen
        window.addEventListener('load', function() {
            document.getElementById('loading').style.display = 'none';
        });

        // Header scroll effect
        window.addEventListener('scroll', function() {
            const header = document.getElementById('header');
            if (window.scrollY > 50) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });

        // Slider functionality
        let currentSlideIndex = 0;
        const slides = document.querySelectorAll('.slide');
        const dots = document.querySelectorAll('.dot');

        function changeSlide(direction) {
            slides[currentSlideIndex].classList.remove('active');
            if(dots[currentSlideIndex]) dots[currentSlideIndex].classList.remove('active');
            
            currentSlideIndex = (currentSlideIndex + direction + slides.length) % slides.length;
            
            slides[currentSlideIndex].classList.add('active');
            if(dots[currentSlideIndex]) dots[currentSlideIndex].classList.add('active');
        }

        function currentSlide(index) {
            slides[currentSlideIndex].classList.remove('active');
            if(dots[currentSlideIndex]) dots[currentSlideIndex].classList.remove('active');
            
            currentSlideIndex = index;
            
            slides[currentSlideIndex].classList.add('active');
            if(dots[currentSlideIndex]) dots[currentSlideIndex].classList.add('active');
        }

        // Auto-play slider
        if(slides.length > 1) {
            setInterval(() => changeSlide(1), 5000);
        }

        // Lightbox
        function openLightbox(src) {
            document.getElementById('lightbox').style.display = 'block';
            document.getElementById('lightbox-img').src = src;
            document.body.style.overflow = 'hidden';
        }

        function closeLightbox() {
            document.getElementById('lightbox').style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        // Mobile menu
        function toggleMenu() {
            const nav = document.querySelector('nav');
            nav.classList.toggle('active');
        }
    </script>
</body>
</html>
