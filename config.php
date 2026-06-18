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

if (!defined('COMPANY_ADDRESS')) {
    define('COMPANY_ADDRESS', 'Sy No 83/1, T-Hub, Plot No 1/C, Panmaktha Knowledge City Rd, Timber Lake Colony, Prashant Hills, Gachibowli, Rai Durg, Hyderabad, Telangana 500032');
    define('COMPANY_STREET_ADDRESS', 'Sy No 83/1, T-Hub, Plot No 1/C, Panmaktha Knowledge City Rd, Timber Lake Colony, Prashant Hills, Gachibowli, Rai Durg');
    define('COMPANY_ADDRESS_LOCALITY', 'Hyderabad');
    define('COMPANY_ADDRESS_REGION', 'Telangana');
    define('COMPANY_POSTAL_CODE', '500032');
}
?>
 