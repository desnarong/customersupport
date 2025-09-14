-- Database Backup
-- Generated: 2025-09-09 21:29:00

-- Table structure for `admin`
DROP TABLE IF EXISTS `admin`;
CREATE TABLE `admin` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `admin`
INSERT INTO `admin` VALUES('3','admin','$2y$10$C5UMmYjeH0GdLP0OxfxmwerbUsSh7hI933bpgRRriGXHZQ5FJLwde','2025-09-09 13:34:40');

-- Table structure for `gallery`
DROP TABLE IF EXISTS `gallery`;
CREATE TABLE `gallery` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `image_path` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'general',
  `sort_order` int DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_gallery_category` (`category`),
  KEY `idx_gallery_active` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `gallery`
INSERT INTO `gallery` VALUES('1','Weight Training Zone','โซนเวทเทรนนิ่งพร้อมอุปกรณ์ครบครัน','/assets/images/gallery1.jpg','equipment','0','1','2025-09-09 14:05:58','2025-09-09 14:05:58');
INSERT INTO `gallery` VALUES('2','Cardio Area','พื้นที่คาร์ดิโอพร้อมลู่วิ่งและจักรยาน','/assets/images/gallery2.jpg','equipment','0','1','2025-09-09 14:05:58','2025-09-09 14:05:58');
INSERT INTO `gallery` VALUES('3','Yoga Class','คลาสโยคะในบรรยากาศที่ผ่อนคลาย','/assets/images/gallery3.jpg','classes','0','1','2025-09-09 14:05:58','2025-09-09 14:05:58');
INSERT INTO `gallery` VALUES('4','Personal Training','เทรนเนอร์ส่วนตัวดูแลอย่างใกล้ชิด','/assets/images/gallery4.jpg','trainers','0','1','2025-09-09 14:05:58','2025-09-09 14:05:58');
INSERT INTO `gallery` VALUES('5','Group Exercise','คลาสออกกำลังกายแบบกลุ่มสนุกสนาน','/assets/images/gallery5.jpg','classes','0','1','2025-09-09 14:05:58','2025-09-09 14:05:58');
INSERT INTO `gallery` VALUES('6','Modern Equipment','อุปกรณ์ทันสมัยนำเข้าจากต่างประเทศ','/assets/images/gallery6.jpg','equipment','0','1','2025-09-09 14:05:58','2025-09-09 14:05:58');
INSERT INTO `gallery` VALUES('7','Member Success','ความสำเร็จของสมาชิก','/assets/images/gallery7.jpg','members','0','1','2025-09-09 14:05:58','2025-09-09 14:05:58');
INSERT INTO `gallery` VALUES('8','Fitness Event','กิจกรรมพิเศษสำหรับสมาชิก','/assets/images/gallery8.jpg','events','0','1','2025-09-09 14:05:58','2025-09-09 14:05:58');

-- Table structure for `pages`
DROP TABLE IF EXISTS `pages`;
CREATE TABLE `pages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `page_slug` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `page_title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `menu_title` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `page_content` longtext COLLATE utf8mb4_unicode_ci,
  `meta_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_description` text COLLATE utf8mb4_unicode_ci,
  `meta_keywords` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) DEFAULT '1',
  `sort_order` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `page_slug` (`page_slug`),
  KEY `idx_pages_slug` (`page_slug`),
  KEY `idx_pages_active` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `pages`
INSERT INTO `pages` VALUES('1','home','หน้าแรก','หน้าแรก','<h1>ยินดีต้อนรับสู่ FitLife Gym</h1>
<p>เปลี่ยนแปลงชีวิตคุณวันนี้ด้วยการออกกำลังกายที่ถูกต้อง</p>
<h2>ทำไมต้องเลือกเรา?</h2>
<ul>
<li>อุปกรณ์ทันสมัยนำเข้าจากต่างประเทศ</li>
<li>เทรนเนอร์มืออาชีพดูแลใกล้ชิด</li>
<li>บรรยากาศสะอาด ปลอดภัย</li>
<li>คลาสออกกำลังกายหลากหลาย</li>
</ul>','FitLife Gym - ศูนย์ฟิตเนสครบวงจร','FitLife Gym - ศูนย์ฟิตเนสครบวงจร','FitLife Gym - ศูนย์ฟิตเนสครบวงจร','1','1','2025-09-09 14:05:16','2025-09-09 14:14:28');
INSERT INTO `pages` VALUES('2','about','เกี่ยวกับเรา','เกี่ยวกับเรา','<h1>เกี่ยวกับ FitLife Gym</h1>
<p>FitLife Gym ก่อตั้งขึ้นในปี 2020 ด้วยความมุ่งมั่นที่จะสร้างพื้นที่ออกกำลังกายที่ทุกคนเข้าถึงได้</p>
<h2>วิสัยทัศน์</h2>
<p>เป็นศูนย์ฟิตเนสชั้นนำที่ทำให้ทุกคนมีสุขภาพที่ดี</p>
<h2>พันธกิจ</h2>
<ul>
<li>ให้บริการฟิตเนสที่มีคุณภาพ</li>
<li>สร้างชุมชนคนรักสุขภาพ</li>
<li>พัฒนาบุคลากรอย่างต่อเนื่อง</li>
</ul>','เกี่ยวกับเรา - FitLife Gym',NULL,NULL,'1','2','2025-09-09 14:05:16','2025-09-09 14:05:16');
INSERT INTO `pages` VALUES('3','services','บริการของเรา','บริการ','<h1>บริการของเรา</h1>
<h2>Personal Training</h2>
<p>เทรนเนอร์ส่วนตัวดูแลแบบ 1:1 ออกแบบโปรแกรมเฉพาะบุคคล</p>
<h2>Group Classes</h2>
<p>คลาสกลุ่มหลากหลาย: Yoga, Zumba, Body Combat, Spinning</p>
<h2>Weight Training</h2>
<p>โซนเวทเทรนนิ่งพร้อมอุปกรณ์ครบครัน</p>
<h2>Cardio Zone</h2>
<p>ลู่วิ่ง จักรยาน และเครื่องคาร์ดิโอมากกว่า 50 เครื่อง</p>','บริการ - FitLife Gym',NULL,NULL,'1','3','2025-09-09 14:05:16','2025-09-09 14:05:16');
INSERT INTO `pages` VALUES('4','contact','ติดต่อเรา','ติดต่อ','<h1>ติดต่อเรา</h1>
<h2>ที่อยู่</h2>
<p>123 ถนนสุขุมวิท แขวงคลองเตย เขตคลองเตย กรุงเทพฯ 10110</p>
<h2>เวลาทำการ</h2>
<p>จันทร์ - ศุกร์: 06:00 - 22:00<br>
เสาร์ - อาทิตย์: 08:00 - 20:00</p>
<h2>ช่องทางติดต่อ</h2>
<p>โทร: 02-123-4567<br>
Email: info@fitlife.com<br>
Line: @fitlife</p>','ติดต่อเรา - FitLife Gym',NULL,NULL,'1','4','2025-09-09 14:05:16','2025-09-09 14:05:16');

-- Table structure for `settings`
DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `site_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'FitLife Gym',
  `site_tagline` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT 'Your Journey to Fitness Starts Here',
  `logo_path` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'info@fitlife.com',
  `phone` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '02-123-4567',
  `address` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT '123 ถนนสุขุมวิท กรุงเทพฯ 10110',
  `facebook_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT '#',
  `instagram_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT '#',
  `line_id` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '@fitlife',
  `meta_description` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT 'FitLife Gym - ศูนย์ฟิตเนสครบวงจร พร้อมเทรนเนอร์มืออาชีพ',
  `meta_keywords` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT 'ฟิตเนส, ยิม, ออกกำลังกาย, สุขภาพ, เทรนเนอร์',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `settings`
INSERT INTO `settings` VALUES('1','FitLife Gym','Your Journey to Fitness Starts Here','/assets/uploads/logo/logo_1757427118.png','info@fitlife.com','02-123-4567','123 ถนนสุขุมวิท กรุงเทพฯ 10110','#','#','@fitlife','FitLife Gym - ศูนย์ฟิตเนสครบวงจร พร้อมเทรนเนอร์มืออาชีพ','ฟิตเนส, ยิม, ออกกำลังกาย, สุขภาพ, เทรนเนอร์','2025-09-09 14:11:58');
INSERT INTO `settings` VALUES('2','FitLife Gym','Your Journey to Fitness Starts Here',NULL,'info@fitlife.com','02-123-4567','123 ถนนสุขุมวิท กรุงเทพฯ 10110','#','#','@fitlife','FitLife Gym - ศูนย์ฟิตเนสครบวงจร พร้อมเทรนเนอร์มืออาชีพ','ฟิตเนส, ยิม, ออกกำลังกาย, สุขภาพ, เทรนเนอร์','2025-09-09 14:03:43');

-- Table structure for `sliders`
DROP TABLE IF EXISTS `sliders`;
CREATE TABLE `sliders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subtitle` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_path` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `button_text` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `button_link` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_sliders_active` (`is_active`),
  KEY `idx_sliders_order` (`sort_order`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `sliders`
INSERT INTO `sliders` VALUES('1','ยินดีต้อนรับสู่ FitLife Gym','เปลี่ยนแปลงชีวิตคุณวันนี้ด้วยการออกกำลังกายที่ถูกต้อง','/assets/images/slider1.jpg','เริ่มต้นเลย','/services','1','1','2025-09-09 14:05:46','2025-09-09 14:05:46');
INSERT INTO `sliders` VALUES('2','Personal Training','เทรนเนอร์ส่วนตัวดูแลแบบ One-on-One','/assets/images/slider2.jpg','ดูรายละเอียด','/services','2','1','2025-09-09 14:05:46','2025-09-09 14:05:46');
INSERT INTO `sliders` VALUES('3','Group Classes','คลาสออกกำลังกายหลากหลายรูปแบบ','/assets/images/slider3.jpg','ดูตารางคลาส','/services','3','1','2025-09-09 14:05:46','2025-09-09 14:05:46');

-- Table structure for `theme_settings`
DROP TABLE IF EXISTS `theme_settings`;
CREATE TABLE `theme_settings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `theme_mode` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'dark',
  `primary_color` varchar(7) COLLATE utf8mb4_unicode_ci DEFAULT '#6366f1',
  `secondary_color` varchar(7) COLLATE utf8mb4_unicode_ci DEFAULT '#8b5cf6',
  `accent_color` varchar(7) COLLATE utf8mb4_unicode_ci DEFAULT '#ec4899',
  `bg_gradient_start` varchar(7) COLLATE utf8mb4_unicode_ci DEFAULT '#0f172a',
  `bg_gradient_end` varchar(7) COLLATE utf8mb4_unicode_ci DEFAULT '#1a1f3a',
  `bg_type` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'gradient',
  `bg_image` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bg_overlay_opacity` decimal(3,2) DEFAULT '0.70',
  `font_family` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'Inter',
  `font_size_base` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT '16px',
  `heading_font` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'Inter',
  `custom_css` text COLLATE utf8mb4_unicode_ci,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `theme_settings`
INSERT INTO `theme_settings` VALUES('1','dark','#6366f1','#8b5cf6','#ec4899','#0f172a','#1a1f3a','gradient',NULL,'0.70','Inter','16px','Inter',NULL,'2025-09-09 14:08:54');

