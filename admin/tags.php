<?php
/**
 * Tags Management Page
 */
define('ADMIN_PANEL', true);
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/config/db_config.php';
requireLogin();

$db = getDB();
$error = '';
$success = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add') {
            $name = sanitizeInput($_POST['name'] ?? '');
            $slug = sanitizeInput($_POST['slug'] ?? '');
            
            if (empty($name)) {
                $error = 'Tag name is required.';
            } else {
                if (empty($slug)) {
                    $slug = generateSlug($name);
                }
                
                try {
                    $stmt = $db->prepare("INSERT INTO blog_tags (name, slug) VALUES (?, ?)");
                    $stmt->execute([$name, $slug]);
                    $success = 'Tag added successfully!';
                } catch (PDOException $e) {
                    if (strpos($e->getMessage(), 'Duplicate') !== false) {
                        $error = 'Tag already exists.';
                    } else {
                        $error = 'Error adding tag: ' . $e->getMessage();
                    }
                }
            }
        } elseif ($_POST['action'] === 'delete' && isset($_POST['id'])) {
            try {
                $db->prepare("DELETE FROM blog_tags WHERE id = ?")->execute([(int)$_POST['id']]);
                $success = 'Tag deleted successfully!';
            } catch (PDOException $e) {
                $error = 'Error deleting tag: ' . $e->getMessage();
            }
        }
    }
}

// Get all tags
$tags = $db->query("SELECT t.*, COUNT(pt.post_id) as post_count 
                    FROM blog_tags t 
                    LEFT JOIN blog_post_tags pt ON t.id = pt.tag_id 
                    GROUP BY t.id 
                    ORDER BY t.name")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tags - Admin Panel</title>
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="admin-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="main-content">
            <div class="page-header">
                <h1>Tags</h1>
            </div>
            
            <?php if ($error): ?>
                <div class="content-section" style="background: #fee; border: 1px solid #fcc; color: #c33; margin-bottom: 1rem;">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="content-section" style="background: #efe; border: 1px solid #cfc; color: #3c3; margin-bottom: 1rem;">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>
            
            <!-- Add Tag Form -->
            <div class="content-section mb-2">
                <h2 style="margin-bottom: 1rem;">Add New Tag</h2>
                <form method="POST" action="">
                    <input type="hidden" name="action" value="add">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="tag-name">Tag Name *</label>
                            <input type="text" id="tag-name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="tag-slug">Slug</label>
                            <input type="text" id="tag-slug" name="slug" placeholder="Auto-generated from name">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Tag</button>
                </form>
            </div>
            
            <!-- Tags List -->
            <div class="content-section">
                <h2 style="margin-bottom: 1rem;">All Tags</h2>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Slug</th>
                                <th>Posts</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($tags)): ?>
                                <tr>
                                    <td colspan="4" class="text-center">No tags found.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($tags as $tag): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($tag['name']); ?></strong></td>
                                        <td><code><?php echo htmlspecialchars($tag['slug']); ?></code></td>
                                        <td><?php echo $tag['post_count']; ?></td>
                                        <td>
                                            <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this tag?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?php echo $tag['id']; ?>">
                                                <button type="submit" class="btn-icon btn-danger" title="Delete">
                                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <polyline points="3 6 5 6 21 6"></polyline>
                                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
    
    <script src="assets/js/admin.js"></script>
    <script>
        // Auto-generate slug from tag name
        document.getElementById('tag-name').addEventListener('input', function() {
            const slugInput = document.getElementById('tag-slug');
            if (!slugInput.dataset.manual) {
                let slug = this.value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
                slugInput.value = slug;
            }
        });
        
        document.getElementById('tag-slug').addEventListener('input', function() {
            this.dataset.manual = 'true';
        });
    </script>
</body>
</html>
