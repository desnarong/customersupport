<?php
// ดึงข้อมูลเมนู
$menu_items = getMenuItems($pdo);
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<header>
    <div class="container">
        <div class="header-content">
            <a href="/demo0/" class="logo">
                <?php if($settings['logo_path']): ?>
                    <img src="/demo0/<?= htmlspecialchars($settings['logo_path']) ?>" alt="<?= htmlspecialchars($settings['site_name']) ?>">
                <?php else: ?>
                    <span style="font-size: 2rem;">🏋️</span>
                <?php endif; ?>
                <h1><?= htmlspecialchars($settings['site_name']) ?></h1>
            </a>
            
            <button class="menu-toggle" onclick="toggleMenu()">☰</button>
            
            <nav id="mainNav">
                <ul>
                    <?php foreach($menu_items as $item): ?>
                        <li>
                            <a href="/demo0/<?= $item['page_slug'] ?>.php" 
                               class="<?= ($current_page == $item['page_slug']) ? 'active' : '' ?>">
                                <?= htmlspecialchars($item['menu_title']) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </nav>
        </div>
    </div>
</header>
