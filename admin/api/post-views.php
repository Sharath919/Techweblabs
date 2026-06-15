<?php
/**
 * JSON API: realtime blog post view counts
 */
define('ADMIN_PANEL', true);

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate');

require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/db_config.php';

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

try {
    $db = getDB();
    $ids = [];

    if (!empty($_GET['ids'])) {
        $ids = array_values(array_filter(array_map('intval', explode(',', $_GET['ids']))));
    }

    if (!empty($ids)) {
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $db->prepare("SELECT id, views FROM blog_posts WHERE id IN ($placeholders)");
        $stmt->execute($ids);
        $rows = $stmt->fetchAll();
    } else {
        $rows = $db->query("SELECT id, views FROM blog_posts ORDER BY id DESC")->fetchAll();
    }

    $posts = [];
    foreach ($rows as $row) {
        $posts[(string) $row['id']] = (int) $row['views'];
    }

    $totalViews = (int) ($db->query("SELECT COALESCE(SUM(views), 0) AS total FROM blog_posts")->fetch()['total'] ?? 0);

    echo json_encode([
        'success' => true,
        'posts' => $posts,
        'total_views' => $totalViews,
        'updated_at' => date('c'),
    ]);
} catch (Exception $e) {
    http_response_code(500);
    error_log('post-views API error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Failed to load view counts']);
}
