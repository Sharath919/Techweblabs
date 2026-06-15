<?php
/**
 * Create New Blog Post
 */
define('ADMIN_PANEL', true);
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/config/db_config.php';
require_once __DIR__ . '/includes/upload-helper.php';
requireLogin();

$db = getDB();
$error = '';
$success = '';
$featuredImageValue = '';
$ogImageValue = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitizeInput($_POST['title'] ?? '');
    $slug = sanitizeInput($_POST['slug'] ?? '');
    $content = $_POST['content'] ?? '';
    $excerpt = sanitizeInput($_POST['excerpt'] ?? '');
    $metaTitle = sanitizeInput($_POST['meta_title'] ?? '');
    $metaDescription = sanitizeInput($_POST['meta_description'] ?? '');
    $metaKeywords = sanitizeInput($_POST['meta_keywords'] ?? '');
    $focusKeyword = sanitizeInput($_POST['focus_keyword'] ?? '');
    $ogImage = sanitizeInput($_POST['og_image'] ?? '');
    $status = sanitizeInput($_POST['status'] ?? 'draft');
    $schemaType = sanitizeInput($_POST['schema_type'] ?? 'Article');
    
    // Generate slug from title if not provided
    if (empty($slug) && !empty($title)) {
        $slug = generateSlug($title);
    }

    $featuredResult = resolveFeaturedImage(
        $_POST['featured_image'] ?? '',
        $_FILES['featured_image_upload'] ?? null,
        $slug
    );
    $featuredImage = $featuredResult['url'];
    
    // Validate
    if ($featuredResult['error']) {
        $error = $featuredResult['error'];
    } elseif (empty($title) || empty($content)) {
        $error = 'Title and content are required.';
    } elseif (empty($slug)) {
        $error = 'Slug is required.';
    } else {
        // Make slug unique
        $slug = makeSlugUnique($slug);
        
        // Auto-set meta title if not provided
        if (empty($metaTitle)) {
            $metaTitle = $title . ' | TechWebLabs';
        }
        
        // Auto-generate excerpt if not provided
        if (empty($excerpt)) {
            $textContent = strip_tags($content);
            $excerpt = substr($textContent, 0, 200) . (strlen($textContent) > 200 ? '...' : '');
        }
        
        // Calculate reading time (average 200 words per minute)
        $wordCount = str_word_count(strip_tags($content));
        $readingTime = max(1, ceil($wordCount / 200));
        
        // Set published_at if publishing
        $publishedAt = null;
        if ($status === 'published') {
            $publishedAt = date('Y-m-d H:i:s');
        }
        
        try {
            $stmt = $db->prepare("
                INSERT INTO blog_posts (
                    slug, title, meta_title, meta_description, meta_keywords, 
                    excerpt, content, featured_image, og_image, author_id, 
                    status, published_at, seo_focus_keyword, schema_type, reading_time
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $slug, $title, $metaTitle, $metaDescription, $metaKeywords,
                $excerpt, $content, $featuredImage, $ogImage, getCurrentUserId(),
                $status, $publishedAt, $focusKeyword, $schemaType, $readingTime
            ]);
            
            $postId = $db->lastInsertId();
            
            // Handle categories
            if (!empty($_POST['categories'])) {
                $catStmt = $db->prepare("INSERT INTO blog_post_categories (post_id, category_id) VALUES (?, ?)");
                foreach ($_POST['categories'] as $catId) {
                    $catStmt->execute([$postId, (int)$catId]);
                }
            }
            
            // Handle tags
            if (!empty($_POST['tags'])) {
                $tagInput = $_POST['tags'];
                if (is_string($tagInput)) {
                    $tagNames = array_map('trim', explode(',', $tagInput));
                    $tagStmt = $db->prepare("INSERT INTO blog_post_tags (post_id, tag_id) VALUES (?, ?)");
                    
                    foreach ($tagNames as $tagName) {
                        if (empty($tagName)) continue;
                        
                        // Get or create tag
                        $tagSlug = generateSlug($tagName);
                        $checkTag = $db->prepare("SELECT id FROM blog_tags WHERE slug = ?");
                        $checkTag->execute([$tagSlug]);
                        $tag = $checkTag->fetch();
                        
                        if (!$tag) {
                            $insertTag = $db->prepare("INSERT INTO blog_tags (name, slug) VALUES (?, ?)");
                            $insertTag->execute([$tagName, $tagSlug]);
                            $tagId = $db->lastInsertId();
                        } else {
                            $tagId = $tag['id'];
                        }
                        
                        $tagStmt->execute([$postId, $tagId]);
                    }
                }
            }
            
            $success = 'Post created successfully!';
            header('Location: post-edit.php?id=' . $postId . '&success=created');
            exit;
        } catch (PDOException $e) {
            $error = 'Error creating post: ' . $e->getMessage();
        }
    }

    $featuredImageValue = !empty($featuredImage) ? $featuredImage : trim($_POST['featured_image'] ?? '');
    $ogImageValue = $_POST['og_image'] ?? '';
}

// Get categories for dropdown
$categories = $db->query("SELECT * FROM blog_categories ORDER BY name")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Post - Admin Panel</title>
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="admin-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="main-content">
            <div class="page-header">
                <h1>New Blog Post</h1>
                <a href="posts.php" class="btn-link">← Back to Posts</a>
            </div>
            
            <?php if ($error): ?>
                <div class="content-section" style="background: #fee; border: 1px solid #fcc; color: #c33;">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="" class="content-section" enctype="multipart/form-data">
                <div class="form-row">
                    <div class="form-group" style="flex: 2;">
                        <label for="post-title">Post Title *</label>
                        <input type="text" id="post-title" name="title" required value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>">
                        <div class="help-text">The title of your blog post</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="post-status">Status</label>
                        <select id="post-status" name="status">
                            <option value="draft" <?php echo ($_POST['status'] ?? 'draft') == 'draft' ? 'selected' : ''; ?>>Draft</option>
                            <option value="published" <?php echo ($_POST['status'] ?? '') == 'published' ? 'selected' : ''; ?>>Published</option>
                            <option value="archived" <?php echo ($_POST['status'] ?? '') == 'archived' ? 'selected' : ''; ?>>Archived</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="post-slug">URL Slug *</label>
                    <div style="display: flex; gap: 0.5rem;">
                        <input type="text" id="post-slug" name="slug" required value="<?php echo htmlspecialchars($_POST['slug'] ?? ''); ?>" style="flex: 1;">
                        <button type="button" id="generate-slug" class="btn">Generate</button>
                    </div>
                    <div class="help-text">SEO-friendly URL: /blogs/your-slug-here</div>
                </div>
                
                <div class="form-group">
                    <label for="post-content">Content *</label>
                    <textarea id="post-content" name="content" required rows="15"><?php echo htmlspecialchars($_POST['content'] ?? ''); ?></textarea>
                    <div class="help-text">Full blog post content</div>
                </div>
                
                <div class="form-group">
                    <label for="post-excerpt">Excerpt</label>
                    <div style="display: flex; gap: 0.5rem; margin-bottom: 0.5rem;">
                        <textarea id="post-excerpt" name="excerpt" rows="3" style="flex: 1;"><?php echo htmlspecialchars($_POST['excerpt'] ?? ''); ?></textarea>
                        <button type="button" id="generate-excerpt" class="btn">Auto-generate</button>
                    </div>
                    <div class="help-text">Short summary (used in listings and meta descriptions)</div>
                </div>
                
                <hr style="margin: 2rem 0; border: none; border-top: 1px solid var(--border);">
                
                <h2 style="margin-bottom: 1.5rem; font-size: 1.25rem;">SEO Settings</h2>
                
                <div class="form-group">
                    <label for="meta-title">Meta Title</label>
                    <input type="text" id="meta-title" name="meta_title" value="<?php echo htmlspecialchars($_POST['meta_title'] ?? ''); ?>" maxlength="60">
                    <div class="help-text">Leave empty to use post title. Recommended: 50-60 characters</div>
                </div>
                
                <div class="form-group">
                    <label for="meta-description">Meta Description</label>
                    <textarea id="meta-description" name="meta_description" rows="3" maxlength="160"><?php echo htmlspecialchars($_POST['meta_description'] ?? ''); ?></textarea>
                    <div class="help-text" id="meta-desc-counter">0 / 160 characters (Recommended: 150-160)</div>
                </div>
                
                <div class="form-group">
                    <label for="meta-keywords">Meta Keywords</label>
                    <input type="text" id="meta-keywords" name="meta_keywords" value="<?php echo htmlspecialchars($_POST['meta_keywords'] ?? ''); ?>" placeholder="keyword1, keyword2, keyword3">
                    <div class="help-text">Comma-separated keywords</div>
                </div>
                
                <div class="form-group">
                    <label for="focus-keyword">Focus Keyword</label>
                    <input type="text" id="focus-keyword" name="focus_keyword" value="<?php echo htmlspecialchars($_POST['focus_keyword'] ?? ''); ?>">
                    <div class="help-text">Primary keyword for this post</div>
                </div>
                
                <?php include __DIR__ . '/includes/featured-image-field.php'; ?>
                
                <div class="form-group">
                    <label for="schema-type">Schema Type</label>
                    <select id="schema-type" name="schema_type">
                        <option value="Article">Article</option>
                        <option value="BlogPosting">Blog Posting</option>
                        <option value="NewsArticle">News Article</option>
                        <option value="TechArticle">Tech Article</option>
                    </select>
                </div>
                
                <hr style="margin: 2rem 0; border: none; border-top: 1px solid var(--border);">
                
                <h2 style="margin-bottom: 1.5rem; font-size: 1.25rem;">Categories & Tags</h2>
                
                <div class="form-group">
                    <label>Categories</label>
                    <div style="display: flex; flex-wrap: wrap; gap: 1rem;">
                        <?php foreach ($categories as $cat): ?>
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="checkbox" name="categories[]" value="<?php echo $cat['id']; ?>">
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="tags">Tags</label>
                    <input type="text" id="tags" name="tags" placeholder="tag1, tag2, tag3" value="<?php echo htmlspecialchars($_POST['tags'] ?? ''); ?>">
                    <div class="help-text">Comma-separated tags</div>
                </div>
                
                <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                    <button type="submit" class="btn btn-primary">Save Post</button>
                    <a href="posts.php" class="btn">Cancel</a>
                </div>
            </form>
        </main>
    </div>
    
    <script src="assets/js/admin.js"></script>
</body>
</html>
