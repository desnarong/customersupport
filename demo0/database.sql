-- สร้างฐานข้อมูล
CREATE DATABASE IF NOT EXISTS fitness_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE fitness_db;

-- ตารางผู้ดูแลระบบ
CREATE TABLE IF NOT EXISTS admin (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- เพิ่มข้อมูล admin เริ่มต้น (username: admin, password: admin123)
INSERT INTO admin (username, password) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- ตารางการตั้งค่าเว็บไซต์
CREATE TABLE IF NOT EXISTS settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    site_name VARCHAR(255) DEFAULT 'FitLife Gym',
    site_tagline VARCHAR(500) DEFAULT 'Your Journey to Fitness Starts Here',
    logo_path VARCHAR(500) DEFAULT NULL,
    email VARCHAR(255) DEFAULT 'info@fitlife.com',
    phone VARCHAR(50) DEFAULT '02-123-4567',
    address TEXT DEFAULT '123 ถนนสุขุมวิท กรุงเทพฯ 10110',
    facebook_url VARCHAR(500) DEFAULT '#',
    instagram_url VARCHAR(500) DEFAULT '#',
    line_id VARCHAR(100) DEFAULT '@fitlife',
    meta_description TEXT DEFAULT 'FitLife Gym - ศูนย์ฟิตเนสครบวงจร พร้อมเทรนเนอร์มืออาชีพ',
    meta_keywords TEXT DEFAULT 'ฟิตเนส, ยิม, ออกกำลังกาย, สุขภาพ, เทรนเนอร์',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- เพิ่มข้อมูลการตั้งค่าเริ่มต้น
INSERT INTO settings (id) VALUES (1);

-- เพิ่มข้อมูล Theme เริ่มต้น
INSERT INTO theme_settings (id) VALUES (1);

-- ตารางหน้าเว็บ
CREATE TABLE IF NOT EXISTS pages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    page_slug VARCHAR(100) UNIQUE NOT NULL,
    page_title VARCHAR(255) NOT NULL,
    menu_title VARCHAR(100) NOT NULL,
    page_content LONGTEXT,
    meta_title VARCHAR(255),
    meta_description TEXT,
    meta_keywords TEXT,
    is_active BOOLEAN DEFAULT 1,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- เพิ่มข้อมูลหน้าเริ่มต้น
INSERT INTO pages (page_slug, page_title, menu_title, page_content, meta_title, sort_order) VALUES
('home', 'หน้าแรก', 'หน้าแรก', '<h1>ยินดีต้อนรับสู่ FitLife Gym</h1>
<p>เปลี่ยนแปลงชีวิตคุณวันนี้ด้วยการออกกำลังกายที่ถูกต้อง</p>
<h2>ทำไมต้องเลือกเรา?</h2>
<ul>
<li>อุปกรณ์ทันสมัยนำเข้าจากต่างประเทศ</li>
<li>เทรนเนอร์มืออาชีพดูแลใกล้ชิด</li>
<li>บรรยากาศสะอาด ปลอดภัย</li>
<li>คลาสออกกำลังกายหลากหลาย</li>
</ul>', 'FitLife Gym - ศูนย์ฟิตเนสครบวงจร', 1),

('about', 'เกี่ยวกับเรา', 'เกี่ยวกับเรา', '<h1>เกี่ยวกับ FitLife Gym</h1>
<p>FitLife Gym ก่อตั้งขึ้นในปี 2020 ด้วยความมุ่งมั่นที่จะสร้างพื้นที่ออกกำลังกายที่ทุกคนเข้าถึงได้</p>
<h2>วิสัยทัศน์</h2>
<p>เป็นศูนย์ฟิตเนสชั้นนำที่ทำให้ทุกคนมีสุขภาพที่ดี</p>
<h2>พันธกิจ</h2>
<ul>
<li>ให้บริการฟิตเนสที่มีคุณภาพ</li>
<li>สร้างชุมชนคนรักสุขภาพ</li>
<li>พัฒนาบุคลากรอย่างต่อเนื่อง</li>
</ul>', 'เกี่ยวกับเรา - FitLife Gym', 2),

('services', 'บริการของเรา', 'บริการ', '<h1>บริการของเรา</h1>
<h2>Personal Training</h2>
<p>เทรนเนอร์ส่วนตัวดูแลแบบ 1:1 ออกแบบโปรแกรมเฉพาะบุคคล</p>
<h2>Group Classes</h2>
<p>คลาสกลุ่มหลากหลาย: Yoga, Zumba, Body Combat, Spinning</p>
<h2>Weight Training</h2>
<p>โซนเวทเทรนนิ่งพร้อมอุปกรณ์ครบครัน</p>
<h2>Cardio Zone</h2>
<p>ลู่วิ่ง จักรยาน และเครื่องคาร์ดิโอมากกว่า 50 เครื่อง</p>', 'บริการ - FitLife Gym', 3),

('contact', 'ติดต่อเรา', 'ติดต่อ', '<h1>ติดต่อเรา</h1>
<h2>ที่อยู่</h2>
<p>123 ถนนสุขุมวิท แขวงคลองเตย เขตคลองเตย กรุงเทพฯ 10110</p>
<h2>เวลาทำการ</h2>
<p>จันทร์ - ศุกร์: 06:00 - 22:00<br>
เสาร์ - อาทิตย์: 08:00 - 20:00</p>
<h2>ช่องทางติดต่อ</h2>
<p>โทร: 02-123-4567<br>
Email: info@fitlife.com<br>
Line: @fitlife</p>', 'ติดต่อเรา - FitLife Gym', 4);
