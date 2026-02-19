<?php
/**
 * Test Database Connection
 * Use this file to test if your database connection is working
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Database Connection Test</h1>";

define('ADMIN_PANEL', true);
require_once __DIR__ . '/config/db_config.php';

try {
    $db = getDB();
    echo "<p style='color: green;'>✓ Database connection successful!</p>";
    
    // Test query
    $stmt = $db->query("SELECT DATABASE() as db_name");
    $result = $stmt->fetch();
    echo "<p>Connected to database: <strong>" . htmlspecialchars($result['db_name']) . "</strong></p>";
    
    // Check if tables exist
    $tables = ['admin_users', 'blog_posts', 'blog_categories', 'blog_tags'];
    echo "<h2>Checking Tables:</h2><ul>";
    foreach ($tables as $table) {
        try {
            $stmt = $db->query("SELECT COUNT(*) as count FROM $table");
            $result = $stmt->fetch();
            echo "<li style='color: green;'>✓ Table '$table' exists ({$result['count']} rows)</li>";
        } catch (PDOException $e) {
            echo "<li style='color: red;'>✗ Table '$table' does not exist or has errors</li>";
        }
    }
    echo "</ul>";
    
    // Check admin user
    echo "<h2>Checking Admin User:</h2>";
    try {
        $stmt = $db->query("SELECT COUNT(*) as count FROM admin_users");
        $result = $stmt->fetch();
        if ($result['count'] > 0) {
            echo "<p style='color: green;'>✓ Admin user exists</p>";
        } else {
            echo "<p style='color: orange;'>⚠ No admin users found. Please run database.sql</p>";
        }
    } catch (PDOException $e) {
        echo "<p style='color: red;'>✗ Error checking admin users: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<h2>Troubleshooting:</h2>";
    echo "<ul>";
    echo "<li>Check DB_HOST, DB_USER, DB_PASS, DB_NAME in config/db_config.php</li>";
    echo "<li>Ensure MySQL server is running</li>";
    echo "<li>Verify database exists: CREATE DATABASE techweb_blog;</li>";
    echo "<li>Check user has proper permissions</li>";
    echo "</ul>";
}

echo "<hr>";
echo "<p><a href='login.php'>Go to Login Page</a></p>";
?>
