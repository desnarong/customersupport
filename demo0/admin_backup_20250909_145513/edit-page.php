<?php
session_start();
require_once '../config/database.php';

// ตรวจสอบการ login
if(!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$message = '';
$error = '';

// ตรวจสอบ ID
if(!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: pages.php');
    exit;
}

$page_id = $_GET['id'];

// ดึงข้อมูลหน้า
$stmt = $pdo->prepare("SELECT * FROM pages WHERE id = ?");
$stmt->execute([$page_id]);
$page = $stmt->fetch();

if(!$page) {
    header('Location: pages.php');
    exit;
}

// อัพเดทข้อมูล
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $sql = "UPDATE pages SET 
                page_title = ?,
                menu_title = ?,
                page_content = ?,
                meta_title = ?,
                meta_description = ?,
                meta_keywords = ?,
                sort_order = ?
                WHERE id = ?";
                
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $_POST['page_title'],
            $_POST['menu_title'],
            $_POST['page_content'],
            $_POST['meta_title'],
            $_POST['meta_description'],
            $_POST['meta_keywords'],
            $_POST['sort_order'],
            $page_id
        ]);
        
        $message = 'บันทึกข้อมูลเรียบร้อยแล้ว';
        
        // โหลดข้อมูลใหม่
        $stmt = $pdo->prepare("SELECT * FROM pages WHERE id = ?");
        $stmt->execute([$page_id]);
        $page = $stmt->fetch();
        
    } catch(Exception $e) {
        $error = 'เกิดข้อผิดพลาด: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขหน้า - <?= htmlspecialchars($page['page_title']) ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dynamic-styles.php">
    <style>
        /* Editor Toolbar */
        .editor-toolbar {
            background: var(--dark-bg);
            border: 1px solid var(--border-color);
            border-bottom: none;
            border-radius: 8px 8px 0 0;
            padding: 0.75rem;
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        
        .editor-btn {
            background: var(--dark-surface);
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            padding: 0.5rem 0.75rem;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.875rem;
            transition: all 0.2s ease;
        }
        
        .editor-btn:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-1px);
        }
        
        .editor-btn.active {
            background: var(--primary-color);
            color: white;
        }
        
        .editor-separator {
            width: 1px;
            background: var(--border-color);
            margin: 0 0.5rem;
        }
        
        /* Editor Area */
        .editor-container {
            position: relative;
        }
        
        #editor {
            min-height: 400px;
            padding: 1rem;
            background: var(--dark-bg);
            border: 1px solid var(--border-color);
            border-radius: 0 0 8px 8px;
            color: var(--text-primary);
            font-family: var(--font-family);
            font-size: 1rem;
            line-height: 1.6;
            overflow-y: auto;
            max-height: 600px;
        }
        
        #editor:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }
        
        #editor h1, #editor h2, #editor h3 {
            margin: 1rem 0;
            font-weight: bold;
        }
        
        #editor h1 { font-size: 2rem; }
        #editor h2 { font-size: 1.5rem; }
        #editor h3 { font-size: 1.25rem; }
        
        #editor p {
            margin: 1rem 0;
        }
        
        #editor ul, #editor ol {
            margin: 1rem 0;
            padding-left: 2rem;
        }
        
        #editor blockquote {
            border-left: 4px solid var(--primary-color);
            padding-left: 1rem;
            margin: 1rem 0;
            color: var(--text-secondary);
        }
        
        #editor a {
            color: var(--primary-color);
            text-decoration: underline;
        }
        
        /* Source Code Toggle */
        .source-toggle {
            margin-top: 0.5rem;
        }
        
        #source-code {
            display: none;
            width: 100%;
            min-height: 400px;
            padding: 1rem;
            background: var(--dark-bg);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            color: var(--text-primary);
            font-family: 'Courier New', monospace;
            font-size: 0.9rem;
            resize: vertical;
        }
        
        /* Preview Mode */
        .preview-container {
            display: none;
            min-height: 400px;
            padding: 1rem;
            background: var(--dark-surface);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            margin-top: 1rem;
        }
        
        .mode-tabs {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }
        
        .mode-tab {
            padding: 0.5rem 1rem;
            background: var(--dark-surface);
            border: 1px solid var(--border-color);
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .mode-tab.active {
            background: var(--primary-color);
            color: white;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>        
        <!-- Main Content -->
        <div class="admin-content">
            <h1>แก้ไขหน้า: <?= htmlspecialchars($page['page_title']) ?></h1>
            
            <?php if($message): ?>
                <div class="alert alert-success">✅ <?= htmlspecialchars($message) ?></div>
            <?php endif; ?>
            
            <?php if($error): ?>
                <div class="alert alert-error">❌ <?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <form method="POST" id="pageForm">
                <div class="content-card">
                    <h2>ข้อมูลหน้า</h2>
                    
                    <div class="form-group">
                        <label for="page_title">ชื่อหน้า</label>
                        <input type="text" id="page_title" name="page_title" value="<?= htmlspecialchars($page['page_title']) ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="menu_title">ชื่อเมนู</label>
                        <input type="text" id="menu_title" name="menu_title" value="<?= htmlspecialchars($page['menu_title']) ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="sort_order">ลำดับการแสดงผล</label>
                        <input type="number" id="sort_order" name="sort_order" value="<?= $page['sort_order'] ?>" min="0">
                    </div>
                    
                    <div class="form-group">
                        <label for="page_content">เนื้อหา</label>
                        
                        <!-- Editor Mode Tabs -->
                        <div class="mode-tabs">
                            <div class="mode-tab active" onclick="switchMode('editor')">✏️ Editor</div>
                            <div class="mode-tab" onclick="switchMode('source')">📝 HTML</div>
                            <div class="mode-tab" onclick="switchMode('preview')">👁️ Preview</div>
                        </div>
                        
                        <!-- Editor Toolbar -->
                        <div class="editor-toolbar" id="toolbar">
                            <button type="button" class="editor-btn" onclick="formatText('bold')" title="Bold">
                                <strong>B</strong>
                            </button>
                            <button type="button" class="editor-btn" onclick="formatText('italic')" title="Italic">
                                <em>I</em>
                            </button>
                            <button type="button" class="editor-btn" onclick="formatText('underline')" title="Underline">
                                <u>U</u>
                            </button>
                            
                            <div class="editor-separator"></div>
                            
                            <button type="button" class="editor-btn" onclick="formatText('formatBlock', '<h1>')" title="Heading 1">
                                H1
                            </button>
                            <button type="button" class="editor-btn" onclick="formatText('formatBlock', '<h2>')" title="Heading 2">
                                H2
                            </button>
                            <button type="button" class="editor-btn" onclick="formatText('formatBlock', '<h3>')" title="Heading 3">
                                H3
                            </button>
                            <button type="button" class="editor-btn" onclick="formatText('formatBlock', '<p>')" title="Paragraph">
                                P
                            </button>
                            
                            <div class="editor-separator"></div>
                            
                            <button type="button" class="editor-btn" onclick="formatText('insertUnorderedList')" title="Bullet List">
                                • List
                            </button>
                            <button type="button" class="editor-btn" onclick="formatText('insertOrderedList')" title="Numbered List">
                                1. List
                            </button>
                            <button type="button" class="editor-btn" onclick="formatText('formatBlock', '<blockquote>')" title="Quote">
                                " "
                            </button>
                            
                            <div class="editor-separator"></div>
                            
                            <button type="button" class="editor-btn" onclick="createLink()" title="Insert Link">
                                🔗 Link
                            </button>
                            <button type="button" class="editor-btn" onclick="insertImage()" title="Insert Image">
                                🖼️ Image
                            </button>
                            <button type="button" class="editor-btn" onclick="insertHR()" title="Horizontal Line">
                                ─ HR
                            </button>
                            
                            <div class="editor-separator"></div>
                            
                            <button type="button" class="editor-btn" onclick="formatText('justifyLeft')" title="Align Left">
                                ⬅
                            </button>
                            <button type="button" class="editor-btn" onclick="formatText('justifyCenter')" title="Align Center">
                                ⬌
                            </button>
                            <button type="button" class="editor-btn" onclick="formatText('justifyRight')" title="Align Right">
                                ➡
                            </button>
                            
                            <div class="editor-separator"></div>
                            
                            <button type="button" class="editor-btn" onclick="formatText('removeFormat')" title="Clear Format">
                                🗑️ Clear
                            </button>
                        </div>
                        
                        <!-- Editor Area -->
                        <div class="editor-container">
                            <div id="editor" contenteditable="true"><?= $page['page_content'] ?></div>
                            <textarea id="source-code" name="page_content"><?= htmlspecialchars($page['page_content']) ?></textarea>
                            <div id="preview" class="preview-container"></div>
                        </div>
                    </div>
                </div>
                
                <div class="content-card">
                    <h2>SEO Settings</h2>
                    
                    <div class="form-group">
                        <label for="meta_title">Meta Title (ปล่อยว่างเพื่อใช้ชื่อหน้า)</label>
                        <input type="text" id="meta_title" name="meta_title" value="<?= htmlspecialchars($page['meta_title']) ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="meta_description">Meta Description</label>
                        <textarea id="meta_description" name="meta_description" rows="3"><?= htmlspecialchars($page['meta_description']) ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="meta_keywords">Meta Keywords (คั่นด้วยเครื่องหมายจุลภาค)</label>
                        <textarea id="meta_keywords" name="meta_keywords" rows="2"><?= htmlspecialchars($page['meta_keywords']) ?></textarea>
                    </div>
                </div>
                
                <div style="display: flex; gap: 1rem;">
                    <button type="submit" class="btn btn-primary">💾 บันทึก</button>
                    <a href="pages.php" class="btn btn-secondary">ยกเลิก</a>
                    <a href="../<?= $page['page_slug'] ?>.php" target="_blank" class="btn btn-secondary">👁️ ดูหน้านี้</a>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        let currentMode = 'editor';
        const editor = document.getElementById('editor');
        const sourceCode = document.getElementById('source-code');
        const preview = document.getElementById('preview');
        const toolbar = document.getElementById('toolbar');
        
        // Format text commands
        function formatText(command, value = null) {
            document.execCommand(command, false, value);
            editor.focus();
        }
        
        // Create link
        function createLink() {
            const url = prompt('Enter URL:');
            if (url) {
                formatText('createLink', url);
            }
        }
        
        // Insert image
        function insertImage() {
            const url = prompt('Enter image URL:');
            if (url) {
                formatText('insertImage', url);
            }
        }
        
        // Insert horizontal rule
        function insertHR() {
            formatText('insertHorizontalRule');
        }
        
        // Switch editor mode
        function switchMode(mode) {
            // Update tab styles
            document.querySelectorAll('.mode-tab').forEach(tab => {
                tab.classList.remove('active');
            });
            event.target.classList.add('active');
            
            // Switch mode
            if (mode === 'editor') {
                editor.style.display = 'block';
                sourceCode.style.display = 'none';
                preview.style.display = 'none';
                toolbar.style.display = 'flex';
                
                // Update editor from source if switching from source mode
                if (currentMode === 'source') {
                    editor.innerHTML = sourceCode.value;
                }
            } else if (mode === 'source') {
                editor.style.display = 'none';
                sourceCode.style.display = 'block';
                preview.style.display = 'none';
                toolbar.style.display = 'none';
                
                // Update source from editor
                sourceCode.value = editor.innerHTML;
            } else if (mode === 'preview') {
                editor.style.display = 'none';
                sourceCode.style.display = 'none';
                preview.style.display = 'block';
                toolbar.style.display = 'none';
                
                // Update preview
                preview.innerHTML = editor.innerHTML;
            }
            
            currentMode = mode;
        }
        
        // Sync editor content to textarea before submit
        document.getElementById('pageForm').addEventListener('submit', function(e) {
            if (currentMode === 'editor') {
                sourceCode.value = editor.innerHTML;
            }
        });
        
        // Handle paste - clean up formatting
        editor.addEventListener('paste', function(e) {
            e.preventDefault();
            const text = e.clipboardData.getData('text/plain');
            document.execCommand('insertText', false, text);
        });
    </script>
</body>
</html>
