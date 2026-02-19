<?php
// Minimal PHP test - no HTML, no complex code
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Step 1: PHP is executing\n";
echo "PHP Version: " . phpversion() . "\n";
echo "Document Root: " . (isset($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : 'NOT SET') . "\n";
echo "Current File: " . __FILE__ . "\n";
echo "Current Dir: " . __DIR__ . "\n";

if (file_exists(__DIR__ . '/config.php')) {
    echo "Step 2: config.php exists\n";
    try {
        require_once(__DIR__ . '/config.php');
        echo "Step 3: config.php loaded\n";
        if (defined('ROOT_DIR')) {
            echo "ROOT_DIR: " . ROOT_DIR . "\n";
        } else {
            echo "ERROR: ROOT_DIR not defined\n";
        }
    } catch (Exception $e) {
        echo "ERROR loading config.php: " . $e->getMessage() . "\n";
    }
} else {
    echo "ERROR: config.php not found\n";
}
?>

