<?php
/**
 * Smart Root Directory Detection
 * Works on both local development and production servers
 */
if (!defined('ROOT_DIR')) {
    // Get the directory where config.php is located
    $configDir = __DIR__;
    
    // Check if homepage folder exists in current directory (root)
    if (is_dir($configDir . '/homepage')) {
        define('ROOT_DIR', $configDir . '/');
    }
    // Check if homepage folder exists in parent directory
    elseif (is_dir(dirname($configDir) . '/homepage')) {
        define('ROOT_DIR', dirname($configDir) . '/');
    }
    // Fallback: Use document root or current directory
    else {
        $docRoot = isset($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : $configDir;
        // Remove trailing slash if present, then add it back
        $docRoot = rtrim($docRoot, '/\\');
        define('ROOT_DIR', $docRoot . '/');
    }
}
?>
 