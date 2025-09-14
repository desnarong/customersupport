<?php
// Image Slider Component
// Include this file where you want to display the slider

// Get active sliders
$stmt = $pdo->query("SELECT * FROM sliders WHERE is_active = 1 ORDER BY sort_order, id");
$sliders = $stmt->fetchAll();

if(count($sliders) > 0):
?>
<div class="hero-slider">
    <div class="slider-container">
        <?php foreach($sliders as $index => $slider): ?>
        <div class="slide <?= $index === 0 ? 'active' : '' ?>" data-slide="<?= $index ?>">
            <div class="slide-bg" style="background-image: url('/demo0/<?= htmlspecialchars($slider['image_path']) ?>');"></div>
            <div class="slide-overlay"></div>
            <div class="slide-content">
                <div class="container">
                    <h1 class="slide-title animate-fade-up"><?= htmlspecialchars($slider['title']) ?></h1>
                    <?php if($slider['subtitle']): ?>
                        <p class="slide-subtitle animate-fade-up" style="animation-delay: 0.2s;">
                            <?= htmlspecialchars($slider['subtitle']) ?>
                        </p>
                    <?php endif; ?>
                    <?php if($slider['button_text'] && $slider['button_link']): ?>
                        <a href="/demo/<?= htmlspecialchars($slider['button_link']) ?>" class="btn btn-primary btn-lg animate-fade-up" style="animation-delay: 0.4s;">
                            <?= htmlspecialchars($slider['button_text']) ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    
    <!-- Navigation -->
    <?php if(count($sliders) > 1): ?>
    <button class="slider-prev" onclick="changeSlide(-1)">❮</button>
    <button class="slider-next" onclick="changeSlide(1)">❯</button>
    
    <!-- Dots -->
    <div class="slider-dots">
        <?php foreach($sliders as $index => $slider): ?>
        <span class="dot <?= $index === 0 ? 'active' : '' ?>" onclick="currentSlide(<?= $index ?>)"></span>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<style>
.hero-slider {
    position: relative;
    height: 600px;
    overflow: hidden;
    margin-bottom: 3rem;
}

.slider-container {
    position: relative;
    height: 100%;
}

.slide {
    position: absolute;
    width: 100%;
    height: 100%;
    opacity: 0;
    transition: opacity 1s ease-in-out;
}

.slide.active {
    opacity: 1;
}

.slide-bg {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-size: cover;
    background-position: center;
    transform: scale(1.1);
    transition: transform 10s ease;
}

.slide.active .slide-bg {
    transform: scale(1);
}

.slide-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.3) 100%);
}

.slide-content {
    position: relative;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    z-index: 2;
}

.slide-title {
    font-size: 3.5rem;
    font-weight: 800;
    color: white;
    margin-bottom: 1rem;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
}

.slide-subtitle {
    font-size: 1.5rem;
    color: rgba(255,255,255,0.9);
    margin-bottom: 2rem;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
}

.btn-lg {
    padding: 1rem 2rem;
    font-size: 1.125rem;
}

/* Animation */
@keyframes fadeUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-up {
    animation: fadeUp 0.8s ease forwards;
}

/* Navigation Buttons */
.slider-prev,
.slider-next {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(255,255,255,0.1);
    backdrop-filter: blur(10px);
    color: white;
    border: 1px solid rgba(255,255,255,0.2);
    padding: 1rem 1.5rem;
    font-size: 1.5rem;
    cursor: pointer;
    transition: all 0.3s ease;
    z-index: 10;
}

.slider-prev:hover,
.slider-next:hover {
    background: rgba(255,255,255,0.2);
}

.slider-prev {
    left: 2rem;
}

.slider-next {
    right: 2rem;
}

/* Dots */
.slider-dots {
    position: absolute;
    bottom: 2rem;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    gap: 0.75rem;
    z-index: 10;
}

.dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: rgba(255,255,255,0.3);
    border: 2px solid rgba(255,255,255,0.5);
    cursor: pointer;
    transition: all 0.3s ease;
}

.dot.active,
.dot:hover {
    background: white;
    transform: scale(1.2);
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .hero-slider {
        height: 400px;
    }
    
    .slide-title {
        font-size: 2rem;
    }
    
    .slide-subtitle {
        font-size: 1.125rem;
    }
    
    .slider-prev,
    .slider-next {
        padding: 0.75rem 1rem;
        font-size: 1.25rem;
    }
    
    .slider-prev {
        left: 1rem;
    }
    
    .slider-next {
        right: 1rem;
    }
}
</style>

<script>
let currentSlideIndex = 0;
const slides = document.querySelectorAll('.slide');
const dots = document.querySelectorAll('.dot');
const totalSlides = slides.length;

// Auto play slider
let slideInterval = setInterval(() => changeSlide(1), 5000);

function changeSlide(direction) {
    slides[currentSlideIndex].classList.remove('active');
    dots[currentSlideIndex]?.classList.remove('active');
    
    currentSlideIndex = (currentSlideIndex + direction + totalSlides) % totalSlides;
    
    slides[currentSlideIndex].classList.add('active');
    dots[currentSlideIndex]?.classList.add('active');
    
    // Reset interval
    clearInterval(slideInterval);
    slideInterval = setInterval(() => changeSlide(1), 5000);
}

function currentSlide(index) {
    slides[currentSlideIndex].classList.remove('active');
    dots[currentSlideIndex]?.classList.remove('active');
    
    currentSlideIndex = index;
    
    slides[currentSlideIndex].classList.add('active');
    dots[currentSlideIndex]?.classList.add('active');
    
    // Reset interval
    clearInterval(slideInterval);
    slideInterval = setInterval(() => changeSlide(1), 5000);
}
</script>
<?php endif; ?>
