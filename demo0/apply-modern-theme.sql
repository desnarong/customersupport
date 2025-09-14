-- Update Theme Settings สำหรับ Modern Fitness Theme
UPDATE theme_settings SET
    theme_mode = 'light',
    primary_color = '#FF6B35',
    secondary_color = '#004E89', 
    accent_color = '#00D9FF',
    bg_gradient_start = '#F7F9FB',
    bg_gradient_end = '#FFFFFF',
    bg_type = 'gradient',
    font_family = 'Montserrat',
    heading_font = 'Bebas Neue',
    font_size_base = '16px'
WHERE id = 1;

-- Update Site Settings
UPDATE settings SET
    site_name = 'MODERN FITNESS',
    site_tagline = 'Transform Your Body, Transform Your Life',
    meta_description = 'Premium fitness center with state-of-the-art equipment, expert trainers, and personalized programs to help you achieve your fitness goals.',
    meta_keywords = 'modern fitness, gym, personal training, group classes, fitness center, health club, workout, exercise'
WHERE id = 1;

-- Update Homepage Content
UPDATE pages SET
    page_content = '<div style="text-align: center; padding: 3rem 0;">
<h2 style="font-family: Bebas Neue, sans-serif; font-size: 3rem; letter-spacing: 2px;">WELCOME TO MODERN FITNESS</h2>
<p style="font-size: 1.25rem; color: #718096; margin: 2rem auto; max-width: 800px;">
We are not just a gym - we are a community dedicated to helping you achieve your fitness goals. With cutting-edge equipment, expert trainers, and a motivating atmosphere, we provide everything you need to transform your body and mind.
</p>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; margin: 3rem 0;">
    <div style="text-align: center;">
        <h3 style="color: #FF6B35; font-size: 3rem; margin-bottom: 0.5rem;">500+</h3>
        <p style="text-transform: uppercase; letter-spacing: 1px;">Active Members</p>
    </div>
    <div style="text-align: center;">
        <h3 style="color: #FF6B35; font-size: 3rem; margin-bottom: 0.5rem;">15+</h3>
        <p style="text-transform: uppercase; letter-spacing: 1px;">Expert Trainers</p>
    </div>
    <div style="text-align: center;">
        <h3 style="color: #FF6B35; font-size: 3rem; margin-bottom: 0.5rem;">50+</h3>
        <p style="text-transform: uppercase; letter-spacing: 1px;">Classes Per Week</p>
    </div>
    <div style="text-align: center;">
        <h3 style="color: #FF6B35; font-size: 3rem; margin-bottom: 0.5rem;">24/7</h3>
        <p style="text-transform: uppercase; letter-spacing: 1px;">Support Available</p>
    </div>
</div>',
    meta_title = 'Modern Fitness - Premium Fitness Center',
    meta_description = 'Transform your body at Modern Fitness. State-of-the-art equipment, expert trainers, and personalized programs.'
WHERE page_slug = 'home';

-- Update About Page
UPDATE pages SET
    page_content = '<h1 style="font-family: Bebas Neue, sans-serif; font-size: 3rem; letter-spacing: 2px; text-align: center;">ABOUT MODERN FITNESS</h1>

<div style="max-width: 800px; margin: 2rem auto;">
<p style="font-size: 1.125rem; line-height: 1.8;">
Founded in 2020, Modern Fitness has quickly become the premier destination for fitness enthusiasts who demand excellence. We believe that fitness is not just about physical transformation - it\'s about building confidence, discipline, and a community of like-minded individuals.
</p>

<h2 style="color: #FF6B35; margin-top: 3rem;">Our Mission</h2>
<p>To provide a world-class fitness experience that empowers our members to achieve their personal best, both physically and mentally.</p>

<h2 style="color: #FF6B35; margin-top: 2rem;">Our Core Values</h2>
<ul style="line-height: 2;">
    <li><strong>Excellence:</strong> We maintain the highest standards in everything we do</li>
    <li><strong>Community:</strong> We foster a supportive and inclusive environment</li>
    <li><strong>Innovation:</strong> We continuously upgrade our facilities and programs</li>
    <li><strong>Results:</strong> We are committed to helping you achieve your goals</li>
</ul>

<h2 style="color: #FF6B35; margin-top: 2rem;">Why Choose Modern Fitness?</h2>
<ul style="line-height: 2;">
    <li>State-of-the-art equipment from leading fitness brands</li>
    <li>Certified trainers with proven track records</li>
    <li>Wide variety of group classes for all fitness levels</li>
    <li>Clean, spacious, and motivating environment</li>
    <li>Flexible membership plans to suit your needs</li>
    <li>Nutritional guidance and meal planning support</li>
</ul>
</div>',
    meta_title = 'About Us - Modern Fitness',
    meta_description = 'Learn about Modern Fitness, our mission, values, and commitment to helping you achieve your fitness goals.'
WHERE page_slug = 'about';

-- Update Services Page
UPDATE pages SET
    page_content = '<h1 style="font-family: Bebas Neue, sans-serif; font-size: 3rem; letter-spacing: 2px; text-align: center;">OUR SERVICES</h1>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; margin: 3rem 0;">

<div style="padding: 2rem; background: linear-gradient(135deg, #FF6B35, #F7931E); color: white; border-radius: 15px;">
    <h2>💪 PERSONAL TRAINING</h2>
    <p>One-on-one sessions with certified trainers who create customized workout plans tailored to your specific goals.</p>
    <ul style="margin-top: 1rem;">
        <li>Initial fitness assessment</li>
        <li>Personalized workout program</li>
        <li>Nutritional guidance</li>
        <li>Progress tracking</li>
    </ul>
</div>

<div style="padding: 2rem; background: linear-gradient(135deg, #004E89, #00D9FF); color: white; border-radius: 15px;">
    <h2>🏃 GROUP CLASSES</h2>
    <p>High-energy group sessions that make working out fun and motivating.</p>
    <ul style="margin-top: 1rem;">
        <li>HIIT Training</li>
        <li>Yoga & Pilates</li>
        <li>Zumba Dance</li>
        <li>Body Combat</li>
        <li>Spinning</li>
    </ul>
</div>

<div style="padding: 2rem; background: linear-gradient(135deg, #10B981, #059669); color: white; border-radius: 15px;">
    <h2>🎯 SPECIALIZED PROGRAMS</h2>
    <p>Targeted programs designed for specific goals and needs.</p>
    <ul style="margin-top: 1rem;">
        <li>Weight Loss Program</li>
        <li>Muscle Building</li>
        <li>Athletic Performance</li>
        <li>Post-Rehabilitation</li>
        <li>Senior Fitness</li>
    </ul>
</div>

<div style="padding: 2rem; background: linear-gradient(135deg, #8B5CF6, #EC4899); color: white; border-radius: 15px;">
    <h2>🥗 NUTRITION COACHING</h2>
    <p>Complete nutritional support to complement your fitness journey.</p>
    <ul style="margin-top: 1rem;">
        <li>Meal planning</li>
        <li>Dietary consultation</li>
        <li>Supplement guidance</li>
        <li>Body composition analysis</li>
    </ul>
</div>

</div>

<div style="text-align: center; margin: 3rem 0;">
    <h2 style="color: #FF6B35;">Ready to Start Your Transformation?</h2>
    <p style="font-size: 1.125rem; margin: 1rem 0;">Contact us today for a free consultation and facility tour!</p>
</div>',
    meta_title = 'Our Services - Modern Fitness',
    meta_description = 'Explore our comprehensive fitness services including personal training, group classes, specialized programs, and nutrition coaching.'
WHERE page_slug = 'services';

-- Update Contact Page
UPDATE pages SET
    page_content = '<h1 style="font-family: Bebas Neue, sans-serif; font-size: 3rem; letter-spacing: 2px; text-align: center;">GET IN TOUCH</h1>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 3rem; margin: 3rem 0;">

<div>
    <h2 style="color: #FF6B35;">Visit Us</h2>
    <p style="font-size: 1.125rem; line-height: 1.8;">
        <strong>Modern Fitness Center</strong><br>
        123 Sukhumvit Road<br>
        Klongtoey, Bangkok 10110<br>
        Thailand
    </p>
    
    <h3 style="color: #004E89; margin-top: 2rem;">Opening Hours</h3>
    <table style="width: 100%; line-height: 2;">
        <tr>
            <td>Monday - Friday:</td>
            <td><strong>06:00 - 22:00</strong></td>
        </tr>
        <tr>
            <td>Saturday:</td>
            <td><strong>08:00 - 20:00</strong></td>
        </tr>
        <tr>
            <td>Sunday:</td>
            <td><strong>08:00 - 20:00</strong></td>
        </tr>
    </table>
</div>

<div>
    <h2 style="color: #FF6B35;">Contact Information</h2>
    
    <div style="margin: 1.5rem 0;">
        <h3 style="color: #004E89;">📞 Phone</h3>
        <p style="font-size: 1.25rem;">02-123-4567</p>
    </div>
    
    <div style="margin: 1.5rem 0;">
        <h3 style="color: #004E89;">✉️ Email</h3>
        <p style="font-size: 1.125rem;">info@modernfitness.com</p>
    </div>
    
    <div style="margin: 1.5rem 0;">
        <h3 style="color: #004E89;">📱 LINE</h3>
        <p style="font-size: 1.125rem;">@modernfitness</p>
    </div>
    
    <div style="margin: 1.5rem 0;">
        <h3 style="color: #004E89;">Follow Us</h3>
        <p>Facebook: /modernfitnessth<br>
        Instagram: @modernfitness_th</p>
    </div>
</div>

</div>

<div style="background: linear-gradient(135deg, #FF6B35, #F7931E); padding: 3rem; border-radius: 20px; text-align: center; color: white; margin: 3rem 0;">
    <h2 style="font-family: Bebas Neue, sans-serif; font-size: 2.5rem; letter-spacing: 2px;">START YOUR FREE TRIAL</h2>
    <p style="font-size: 1.25rem; margin: 1rem 0;">Experience Modern Fitness for 3 days absolutely free!</p>
    <p style="margin: 2rem 0;">No credit card required • No commitment • Full access to all facilities</p>
</div>',
    meta_title = 'Contact Us - Modern Fitness',
    meta_description = 'Get in touch with Modern Fitness. Visit our premium fitness center in Bangkok or contact us for more information.'
WHERE page_slug = 'contact';

-- Add sample sliders if needed
INSERT INTO sliders (title, subtitle, button_text, button_link, sort_order, is_active, image_path) VALUES
('TRANSFORM YOUR BODY', 'Join the fitness revolution at Modern Fitness', 'START TODAY', '/contact', 1, 1, ''),
('EXPERT TRAINERS', 'Get personalized guidance from certified professionals', 'MEET OUR TEAM', '/about', 2, 1, ''),
('GROUP CLASSES', 'Fun and energetic classes for all fitness levels', 'VIEW SCHEDULE', '/services', 3, 1, '')
ON DUPLICATE KEY UPDATE title=VALUES(title);
