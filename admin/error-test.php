<?php
/**
 * Error Test Page
 * This will help identify what's causing the 500 error
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

echo "<h1>Error Test Page</h1>";
echo "<p>If you can see this page, PHP is working.</p>";

echo "<h2>PHP Version:</h2>";
echo "<p>" . phpversion() . "</p>";

echo "<h2>Testing File Includes:</h2>";

echo "<p>1. Testing auth.php...</p>";
try {
    define('ADMIN_PANEL', true);
    require_once __DIR__ . '/config/auth.php';
    echo "<p style='color: green;'>✓ auth.php loaded successfully</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error loading auth.php: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}

echo "<p>2. Testing db_config.php...</p>";
try {
    define('ADMIN_PANEL', true);
    require_once __DIR__ . '/config/db_config.php';
    echo "<p style='color: green;'>✓ db_config.php loaded successfully</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error loading db_config.php: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}

echo "<p>3. Testing database connection...</p>";
try {
    define('ADMIN_PANEL', true);
    require_once __DIR__ . '/config/db_config.php';
    $db = getDB();
    echo "<p style='color: green;'>✓ Database connection successful</p>";
} catch (Exception $e) {
    echo "<p style='color: orange;'>⚠ Database connection failed (this is OK if database isn't set up yet): " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<p>4. Testing session...</p>";
try {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    echo "<p style='color: green;'>✓ Session started successfully</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Session error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<p>5. Testing sanitizeInput function...</p>";
try {
    if (function_exists('sanitizeInput')) {
        $test = sanitizeInput('<script>alert("test")</script>');
        echo "<p style='color: green;'>✓ sanitizeInput function works</p>";
    } else {
        echo "<p style='color: orange;'>⚠ sanitizeInput function not found</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error with sanitizeInput: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";
echo "<p><a href='login.php'>Try Login Page</a></p>";
?>
