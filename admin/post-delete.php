<?php
/**
 * Delete Blog Post
 */
define('ADMIN_PANEL', true);
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/config/db_config.php';
requireLogin();

$db = getDB();
$postId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$postId) {
    header('Location: posts.php');
    exit;
}

// Check if post exists
$stmt = $db->prepare("SELECT id, title FROM blog_posts WHERE id = ?");
$stmt->execute([$postId]);
$post = $stmt->fetch();

if (!$post) {
    header('Location: posts.php');
    exit;
}

// Delete post (cascade will handle related records)
try {
    $db->prepare("DELETE FROM blog_posts WHERE id = ?")->execute([$postId]);
    header('Location: posts.php?deleted=1');
    exit;
} catch (PDOException $e) {
    header('Location: posts.php?error=' . urlencode('Error deleting post'));
    exit;
}
?>
