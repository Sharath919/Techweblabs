<?php
/**
 * Server Compatibility Test
 * Upload this file to your server and access it via browser
 * It will show you what's wrong
 */

// Enable error display
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Server Compatibility Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .success { color: green; }
        .error { color: red; }
        .warning { color: orange; }
        pre { background: #f5f5f5; padding: 10px; border: 1px solid #ddd; }
    </style>
</head>
<body>
    <h1>Server Compatibility Test</h1>
    
    <h2>1. PHP Version</h2>
    <?php
    $phpVersion = phpversion();
    $minVersion = '7.4';
    if (version_compare($phpVersion, $minVersion, '>=')) {
        echo "<p class='success'>✅ PHP Version: $phpVersion (Required: $minVersion+)</p>";
    } else {
        echo "<p class='error'>❌ PHP Version: $phpVersion (Required: $minVersion+)</p>";
    }
    ?>
    
    <h2>2. Server Information</h2>
    <pre>
Document Root: <?php echo $_SERVER['DOCUMENT_ROOT']; ?>

Script File: <?php echo __FILE__; ?>

Script Directory: <?php echo __DIR__; ?>

Current Working Directory: <?php echo getcwd(); ?>
    </pre>
    
    <h2>3. Path Detection Test</h2>
    <?php
    // Test config.php
    if (file_exists(__DIR__ . '/config.php')) {
        echo "<p class='success'>✅ config.php found</p>";
        require_once(__DIR__ . '/config.php');
        
        if (defined('ROOT_DIR')) {
            echo "<p class='success'>✅ ROOT_DIR defined: " . ROOT_DIR . "</p>";
            
            // Test if homepage folder exists
            if (is_dir(ROOT_DIR . 'homepage')) {
                echo "<p class='success'>✅ homepage folder found</p>";
            } else {
                echo "<p class='error'>❌ homepage folder NOT found at: " . ROOT_DIR . "homepage</p>";
            }
            
            // Test if header.php exists
            if (file_exists(ROOT_DIR . 'homepage/header.php')) {
                echo "<p class='success'>✅ header.php found</p>";
            } else {
                echo "<p class='error'>❌ header.php NOT found at: " . ROOT_DIR . "homepage/header.php</p>";
            }
        } else {
            echo "<p class='error'>❌ ROOT_DIR not defined after loading config.php</p>";
        }
    } else {
        echo "<p class='error'>❌ config.php NOT found</p>";
    }
    ?>
    
    <h2>4. Required PHP Extensions</h2>
    <?php
    $required = ['mbstring', 'json', 'curl', 'xml'];
    foreach ($required as $ext) {
        if (extension_loaded($ext)) {
            echo "<p class='success'>✅ $ext extension loaded</p>";
        } else {
            echo "<p class='error'>❌ $ext extension NOT loaded</p>";
        }
    }
    ?>
    
    <h2>5. File Permissions</h2>
    <?php
    $testFile = __FILE__;
    $perms = fileperms($testFile);
    $readable = is_readable($testFile);
    $writable = is_writable($testFile);
    
    echo "<p>File Permissions: " . substr(sprintf('%o', $perms), -4) . "</p>";
    echo "<p class='" . ($readable ? 'success' : 'error') . "'>" . ($readable ? '✅' : '❌') . " File is readable</p>";
    echo "<p class='" . ($writable ? 'success' : 'error') . "'>" . ($writable ? '✅' : '❌') . " File is writable</p>";
    ?>
    
    <h2>6. Directory Structure Check</h2>
    <?php
    $dirs = ['homepage', 'pages', 'images', 'css', 'js', 'includes'];
    foreach ($dirs as $dir) {
        $path = __DIR__ . '/' . $dir;
        if (is_dir($path)) {
            echo "<p class='success'>✅ $dir/ directory exists</p>";
        } else {
            echo "<p class='error'>❌ $dir/ directory NOT found</p>";
        }
    }
    ?>
    
    <h2>7. .htaccess Test</h2>
    <?php
    if (file_exists(__DIR__ . '/.htaccess')) {
        echo "<p class='success'>✅ .htaccess file exists</p>";
        
        // Check if mod_rewrite is available
        if (function_exists('apache_get_modules')) {
            $modules = apache_get_modules();
            if (in_array('mod_rewrite', $modules)) {
                echo "<p class='success'>✅ mod_rewrite is enabled</p>";
            } else {
                echo "<p class='warning'>⚠️ mod_rewrite module not found (may not be available via PHP)</p>";
            }
        } else {
            echo "<p class='warning'>⚠️ Cannot check Apache modules (function not available)</p>";
        }
    } else {
        echo "<p class='warning'>⚠️ .htaccess file NOT found</p>";
    }
    ?>
    
    <h2>8. Test Include</h2>
    <?php
    if (defined('ROOT_DIR')) {
        $headerPath = ROOT_DIR . 'homepage/header.php';
        if (file_exists($headerPath)) {
            echo "<p class='success'>✅ header.php path exists: $headerPath</p>";
            echo "<p>Attempting to include...</p>";
            try {
                ob_start();
                include($headerPath);
                $output = ob_get_clean();
                echo "<p class='success'>✅ header.php included successfully</p>";
            } catch (Throwable $e) {
                echo "<p class='error'>❌ Error including header.php: " . $e->getMessage() . "</p>";
            }
        } else {
            echo "<p class='error'>❌ header.php NOT found at: $headerPath</p>";
        }
    }
    ?>
    
    <h2>9. Recommendations</h2>
    <ul>
        <li>If ROOT_DIR is wrong, update config.php</li>
        <li>If folders are missing, upload all files</li>
        <li>If permissions are wrong, set directories to 755 and files to 644</li>
        <li>If mod_rewrite is missing, contact hosting support</li>
        <li>Check server error logs for specific PHP errors</li>
    </ul>
    
    <hr>
    <p><strong>Delete this file after testing for security!</strong></p>
</body>
</html>

