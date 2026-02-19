<?php
/**
 * Debug script to test blog routing
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Blog Debug Information</h1>";

echo "<h2>Request Info:</h2>";
echo "<pre>";
echo "REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? 'N/A') . "\n";
echo "QUERY_STRING: " . ($_SERVER['QUERY_STRING'] ?? 'N/A') . "\n";
echo "GET params: ";
print_r($_GET);
echo "</pre>";

echo "<h2>Database Test:</h2>";

define('ADMIN_PANEL', true);
require_once __DIR__ . '/../../admin/config/db_config.php';

try {
    $db = getDB();
    echo "<p style='color: green;'>✓ Database connection successful</p>";
    
    // Test query for test-post
    $stmt = $db->prepare("SELECT * FROM blog_posts WHERE slug = ?");
    $stmt->execute(['test-post']);
    $post = $stmt->fetch();
    
    if ($post) {
        echo "<p style='color: green;'>✓ Post found:</p>";
        echo "<pre>";
        print_r($post);
        echo "</pre>";
        echo "<p><strong>Status:</strong> " . $post['status'] . "</p>";
        if ($post['status'] !== 'published') {
            echo "<p style='color: orange;'>⚠ Post exists but status is not 'published'. Change status to 'published' to view.</p>";
        }
    } else {
        echo "<p style='color: red;'>✗ Post with slug 'test-post' not found</p>";
    }
    
    // List all posts
    echo "<h2>All Blog Posts:</h2>";
    $allPosts = $db->query("SELECT id, slug, title, status FROM blog_posts ORDER BY created_at DESC");
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>ID</th><th>Slug</th><th>Title</th><th>Status</th></tr>";
    while ($p = $allPosts->fetch()) {
        echo "<tr>";
        echo "<td>" . $p['id'] . "</td>";
        echo "<td>" . htmlspecialchars($p['slug']) . "</td>";
        echo "<td>" . htmlspecialchars($p['title']) . "</td>";
        echo "<td>" . htmlspecialchars($p['status']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";
echo "<p><a href='/blogs'>Go to Blog Listing</a></p>";
echo "<p><a href='/admin/posts.php'>Go to Admin Posts</a></p>";
?>
