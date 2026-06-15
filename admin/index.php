<?php
/**
 * Admin Dashboard
 */
define('ADMIN_PANEL', true);
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/config/db_config.php';
require_once __DIR__ . '/includes/blog-helper.php';
requireLogin();

$db = getDB();
$user = getCurrentUser();

// Get statistics
$stats = [];
$stats['total_posts'] = $db->query("SELECT COUNT(*) as count FROM blog_posts")->fetch()['count'];
$stats['published_posts'] = $db->query("SELECT COUNT(*) as count FROM blog_posts WHERE status = 'published'")->fetch()['count'];
$stats['draft_posts'] = $db->query("SELECT COUNT(*) as count FROM blog_posts WHERE status = 'draft'")->fetch()['count'];
$stats['total_views'] = $db->query("SELECT SUM(views) as total FROM blog_posts")->fetch()['total'] ?? 0;

// Get recent posts
$recentPosts = $db->query("
    SELECT p.*, u.username as author_name 
    FROM blog_posts p 
    LEFT JOIN admin_users u ON p.author_id = u.id 
    ORDER BY p.created_at DESC 
    LIMIT 5
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Admin Panel</title>
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="admin-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="main-content">
            <div class="page-header">
                <h1>Dashboard</h1>
                <p>Welcome back, <?php echo htmlspecialchars($user['full_name'] ?? $user['username']); ?>!</p>
            </div>
            
            <!-- Statistics Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                            <polyline points="10 9 9 9 8 9"></polyline>
                        </svg>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $stats['total_posts']; ?></h3>
                        <p>Total Posts</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                        </svg>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $stats['published_posts']; ?></h3>
                        <p>Published</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                        </svg>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $stats['draft_posts']; ?></h3>
                        <p>Drafts</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </div>
                    <div class="stat-content">
                        <h3 id="total-views-stat" data-views="<?php echo (int) $stats['total_views']; ?>"><?php echo number_format($stats['total_views']); ?></h3>
                        <p>Total Views <span class="live-badge" title="Updates every 15 seconds">Live</span></p>
                    </div>
                </div>
            </div>
            
            <!-- Recent Posts -->
            <div class="content-section">
                <div class="section-header">
                    <h2>Recent Posts</h2>
                    <a href="posts.php" class="btn-link">View All</a>
                </div>
                
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Author</th>
                                <th>Status</th>
                                <th>Views <span class="live-badge" title="Updates every 15 seconds">Live</span></th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recentPosts)): ?>
                                <tr>
                                    <td colspan="6" class="text-center">No posts yet. <a href="post-new.php">Create your first post</a></td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recentPosts as $post): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($post['title']); ?></strong>
                                            <br>
                                            <small class="text-muted"><?php echo htmlspecialchars($post['slug']); ?></small>
                                        </td>
                                        <td><?php echo htmlspecialchars($post['author_name']); ?></td>
                                        <td>
                                            <span class="badge badge-<?php echo $post['status']; ?>">
                                                <?php echo ucfirst($post['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="post-views" data-post-id="<?php echo (int) $post['id']; ?>" data-views="<?php echo (int) $post['views']; ?>">
                                                <?php echo number_format($post['views']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('M d, Y', strtotime($post['created_at'])); ?></td>
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
            </div>
        </main>
    </div>
    
    <script src="assets/js/admin.js"></script>
    <script src="assets/js/post-analytics.js"></script>
</body>
</html>
