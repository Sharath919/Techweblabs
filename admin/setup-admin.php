<?php
/**
 * Setup Admin User Script
 * Run this once to create/update the admin user
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

define('ADMIN_PANEL', true);
require_once __DIR__ . '/config/db_config.php';

echo "<h1>Admin User Setup</h1>";

try {
    $db = getDB();
    echo "<p style='color: green;'>✓ Database connection successful</p>";
    
    // Check if admin user exists
    $stmt = $db->query("SELECT * FROM admin_users WHERE username = 'admin'");
    $existing = $stmt->fetch();
    
    if ($existing) {
        echo "<p>Admin user already exists. Updating password...</p>";
        
        // Update password
        $newPassword = 'admin123';
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        
        $updateStmt = $db->prepare("UPDATE admin_users SET password_hash = ?, status = 'active' WHERE username = 'admin'");
        $updateStmt->execute([$hash]);
        
        echo "<p style='color: green;'>✓ Password updated successfully!</p>";
        echo "<p><strong>Username:</strong> admin</p>";
        echo "<p><strong>Password:</strong> admin123</p>";
        
    } else {
        echo "<p>Creating new admin user...</p>";
        
        // Create admin user
        $password = 'admin123';
        $hash = password_hash($password, PASSWORD_DEFAULT);
        
        $insertStmt = $db->prepare("
            INSERT INTO admin_users (username, email, password_hash, full_name, role, status) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        $insertStmt->execute([
            'admin',
            'admin@techweblabs.com',
            $hash,
            'Administrator',
            'admin',
            'active'
        ]);
        
        echo "<p style='color: green;'>✓ Admin user created successfully!</p>";
        echo "<p><strong>Username:</strong> admin</p>";
        echo "<p><strong>Password:</strong> admin123</p>";
    }
    
    // Verify the password works
    echo "<hr>";
    echo "<h2>Verifying Password:</h2>";
    
    $verifyStmt = $db->prepare("SELECT password_hash FROM admin_users WHERE username = 'admin'");
    $verifyStmt->execute();
    $user = $verifyStmt->fetch();
    
    if ($user && password_verify('admin123', $user['password_hash'])) {
        echo "<p style='color: green;'>✓ Password verification successful!</p>";
    } else {
        echo "<p style='color: red;'>✗ Password verification failed!</p>";
    }
    
    // Show all admin users
    echo "<hr>";
    echo "<h2>All Admin Users:</h2>";
    $allUsers = $db->query("SELECT id, username, email, role, status FROM admin_users");
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Role</th><th>Status</th></tr>";
    while ($user = $allUsers->fetch()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($user['id']) . "</td>";
        echo "<td>" . htmlspecialchars($user['username']) . "</td>";
        echo "<td>" . htmlspecialchars($user['email']) . "</td>";
        echo "<td>" . htmlspecialchars($user['role']) . "</td>";
        echo "<td>" . htmlspecialchars($user['status']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<hr>";
    echo "<p><a href='login.php'>Go to Login Page</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<h2>Troubleshooting:</h2>";
    echo "<ul>";
    echo "<li>Make sure the database 'techweb_blog' exists</li>";
    echo "<li>Make sure you've imported database.sql</li>";
    echo "<li>Check database credentials in config/db_config.php</li>";
    echo "<li>Verify the admin_users table exists</li>";
    echo "</ul>";
}
?>
