<?php
// Footer จะใช้ข้อมูล $settings ที่ดึงมาจากหน้าหลัก
?>
<footer>
    <div class="container">
        <div class="footer-content">
            <div class="footer-section">
                <h3>เกี่ยวกับเรา</h3>
                <p><?= htmlspecialchars($settings['site_tagline']) ?></p>
                <div class="social-links">
                    <?php if($settings['facebook_url']): ?>
                        <a href="<?= htmlspecialchars($settings['facebook_url']) ?>" target="_blank" title="Facebook">f</a>
                    <?php endif; ?>
                    <?php if($settings['instagram_url']): ?>
                        <a href="<?= htmlspecialchars($settings['instagram_url']) ?>" target="_blank" title="Instagram">📷</a>
                    <?php endif; ?>
                    <?php if($settings['line_id']): ?>
                        <a href="https://line.me/R/ti/p/<?= htmlspecialchars($settings['line_id']) ?>" target="_blank" title="Line">L</a>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="footer-section">
                <h3>เมนูลัด</h3>
                <?php foreach($menu_items as $item): ?>
                    <a href="<?= $item['page_slug'] ?>.php"><?= htmlspecialchars($item['menu_title']) ?></a>
                <?php endforeach; ?>
            </div>
            
            <div class="footer-section">
                <h3>ติดต่อเรา</h3>
                <p>📍 <?= nl2br(htmlspecialchars($settings['address'])) ?></p>
                <p>📞 <?= htmlspecialchars($settings['phone']) ?></p>
                <p>✉️ <?= htmlspecialchars($settings['email']) ?></p>
            </div>
            
            <div class="footer-section">
                <h3>เวลาทำการ</h3>
                <p>จันทร์ - ศุกร์: 06:00 - 22:00</p>
                <p>เสาร์ - อาทิตย์: 08:00 - 20:00</p>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; <?= date('Y') ?> <?= htmlspecialchars($settings['site_name']) ?>. All rights reserved.</p>
        </div>
    </div>
</footer>
