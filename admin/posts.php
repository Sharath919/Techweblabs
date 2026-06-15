<?php
/**
 * Blog Posts List Page
 */
define('ADMIN_PANEL', true);
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/config/db_config.php';
require_once __DIR__ . '/includes/seo-helper.php';
require_once __DIR__ . '/includes/blog-helper.php';
requireLogin();

$db = getDB();

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 20;
$offset = ($page - 1) * $perPage;

// Filters
$status = $_GET['status'] ?? '';
$search = $_GET['search'] ?? '';

// Build query
$where = [];
$params = [];

if ($status) {
    $where[] = "p.status = ?";
    $params[] = $status;
}

if ($search) {
    $where[] = "(p.title LIKE ? OR p.slug LIKE ?)";
    $searchParam = "%$search%";
    $params[] = $searchParam;
    $params[] = $searchParam;
}

$whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

// Get total count
$countSql = "SELECT COUNT(*) as total FROM blog_posts p $whereClause";
$countStmt = $db->prepare($countSql);
$countStmt->execute($params);
$totalPosts = $countStmt->fetch()['total'];
$totalPages = ceil($totalPosts / $perPage);

// Get posts
$sql = "SELECT p.*, u.username as author_name 
        FROM blog_posts p 
        LEFT JOIN admin_users u ON p.author_id = u.id 
        $whereClause
        ORDER BY p.created_at DESC 
        LIMIT $perPage OFFSET $offset";

if (!empty($params)) {
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
} else {
    $stmt = $db->query($sql);
}

$posts = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Posts - Admin Panel</title>
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="admin-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="main-content">
            <div class="page-header">
                <h1>Blog Posts</h1>
                <a href="post-new.php" class="btn btn-primary">+ New Post</a>
            </div>
            
            <!-- Filters -->
            <div class="content-section mb-2">
                <form method="GET" action="" style="display: flex; gap: 1rem; align-items: flex-end;">
                    <div class="form-group" style="flex: 1; margin-bottom: 0;">
                        <label>Search</label>
                        <input type="text" name="search" placeholder="Search posts..." value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <div class="form-group" style="width: 200px; margin-bottom: 0;">
                        <label>Status</label>
                        <select name="status">
                            <option value="">All Status</option>
                            <option value="published" <?php echo $status == 'published' ? 'selected' : ''; ?>>Published</option>
                            <option value="draft" <?php echo $status == 'draft' ? 'selected' : ''; ?>>Draft</option>
                            <option value="archived" <?php echo $status == 'archived' ? 'selected' : ''; ?>>Archived</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <?php if ($search || $status): ?>
                        <a href="posts.php" class="btn">Clear</a>
                    <?php endif; ?>
                </form>
            </div>
            
            <!-- Posts Table -->
            <div class="content-section">
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Slug</th>
                                <th>Author</th>
                                <th>Status</th>
                                <th>SEO</th>
                                <th>Views <span class="live-badge" title="Updates every 15 seconds">Live</span></th>
                                <th>Published</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($posts)): ?>
                                <tr>
                                    <td colspan="8" class="text-center">No posts found. <a href="post-new.php">Create your first post</a></td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($posts as $post):
                                    $articleData = [
                                        'title' => $post['title'] ?? '',
                                        'meta_title' => $post['meta_title'] ?? '',
                                        'meta_description' => $post['meta_description'] ?? '',
                                        'content' => $post['content'] ?? '',
                                        'seo_focus_keyword' => $post['seo_focus_keyword'] ?? '',
                                    ];
                                    $seo = calculateSEOScore($articleData);
                                    $pct = $seo['percentage'] ?? 0;
                                    $grade = $seo['grade'] ?? 'F';
                                    $gradeColor = $seo['grade_color'] ?? '#ef4444';
                                ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($post['title']); ?></strong>
                                            <?php if ($post['meta_title']): ?>
                                                <br><small class="text-muted">Meta: <?php echo htmlspecialchars(substr($post['meta_title'], 0, 50)); ?>...</small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <code style="font-size: 0.75rem; background: #f3f4f6; padding: 0.25rem 0.5rem; border-radius: 4px;">
                                                <?php echo htmlspecialchars($post['slug']); ?>
                                            </code>
                                        </td>
                                        <td><?php echo htmlspecialchars($post['author_name']); ?></td>
                                        <td>
                                            <span class="badge badge-<?php echo $post['status']; ?>">
                                                <?php echo ucfirst($post['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span style="display: inline-flex; align-items: center; gap: 0.25rem; font-weight: 600; color: <?php echo htmlspecialchars($gradeColor); ?>;" title="Grade: <?php echo htmlspecialchars($grade); ?>">
                                                <span style="min-width: 2rem;"><?php echo (int)$pct; ?>%</span>
                                                <span style="font-size: 0.75rem; background: <?php echo htmlspecialchars($gradeColor); ?>20; padding: 0.15rem 0.35rem; border-radius: 4px;"><?php echo htmlspecialchars($grade); ?></span>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="post-views" data-post-id="<?php echo (int) $post['id']; ?>" data-views="<?php echo (int) $post['views']; ?>">
                                                <?php echo number_format($post['views']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($post['published_at']): ?>
                                                <?php echo date('M d, Y', strtotime($post['published_at'])); ?>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <?php if ($post['status'] === 'published'): ?>
                                                    <a href="<?php echo htmlspecialchars(getBlogPostUrl($post['slug'])); ?>" target="_blank" class="btn-icon" title="View on Frontend" style="background: #10b981; color: white; border-color: #10b981;">
                                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                            <circle cx="12" cy="12" r="3"></circle>
                                                        </svg>
                                                    </a>
                                                <?php endif; ?>
                                                <?php $sharePost = $post; include __DIR__ . '/includes/share-buttons.php'; ?>
                                                <a href="post-edit.php?id=<?php echo $post['id']; ?>" class="btn-icon" title="Edit">
                                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                                    </svg>
                                                </a>
                                                <a href="post-delete.php?id=<?php echo $post['id']; ?>" class="btn-icon btn-danger" title="Delete" onclick="return confirm('Are you sure?');">
                                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <polyline points="3 6 5 6 21 6"></polyline>
                                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                    </svg>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <div style="margin-top: 1.5rem; display: flex; justify-content: center; gap: 0.5rem;">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?php echo $page - 1; ?><?php echo $status ? '&status=' . urlencode($status) : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" class="btn">Previous</a>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <?php if ($i == 1 || $i == $totalPages || ($i >= $page - 2 && $i <= $page + 2)): ?>
                                <a href="?page=<?php echo $i; ?><?php echo $status ? '&status=' . urlencode($status) : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" 
                                   class="btn <?php echo $i == $page ? 'btn-primary' : ''; ?>">
                                    <?php echo $i; ?>
                                </a>
                            <?php elseif ($i == $page - 3 || $i == $page + 3): ?>
                                <span>...</span>
                            <?php endif; ?>
                        <?php endfor; ?>
                        
                        <?php if ($page < $totalPages): ?>
                            <a href="?page=<?php echo $page + 1; ?><?php echo $status ? '&status=' . urlencode($status) : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>" class="btn">Next</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
    
    <script src="assets/js/admin.js"></script>
    <script src="assets/js/post-analytics.js"></script>
</body>
</html>
