<?php
/**
 * Featured image upload helpers
 */

if (!defined('ADMIN_PANEL')) {
    die('Direct access not allowed');
}

/**
 * Get public site base URL (works on local and production).
 */
function getSiteBaseUrl() {
    if (getenv('SITE_BASE_URL')) {
        return rtrim(getenv('SITE_BASE_URL'), '/');
    }

    if (defined('SITE_BASE_URL') && SITE_BASE_URL) {
        return rtrim(SITE_BASE_URL, '/');
    }

    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/admin'));
    $basePath = str_ends_with($scriptDir, '/admin') ? dirname($scriptDir) : $scriptDir;

    if ($basePath === '/' || $basePath === '.') {
        $basePath = '';
    }

    return rtrim($scheme . '://' . $host . $basePath, '/');
}

/**
 * Get project root directory for file storage.
 */
function getProjectRootDir() {
    if (!defined('ROOT_DIR')) {
        $configPath = __DIR__ . '/../../config.php';
        if (file_exists($configPath)) {
            require_once $configPath;
        }
    }

    if (defined('ROOT_DIR')) {
        return rtrim(ROOT_DIR, '/\\');
    }

    $docRoot = $_SERVER['DOCUMENT_ROOT'] ?? realpath(__DIR__ . '/../..');
    return rtrim($docRoot, '/\\');
}

/**
 * Upload a featured image file. Returns url on success, null if no file, error string on failure.
 *
 * @return array{url: ?string, error: ?string}
 */
function uploadFeaturedImage($file, $slug = '') {
    if (!isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return ['url' => null, 'error' => null];
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['url' => null, 'error' => 'Image upload failed. Please try again.'];
    }

    $maxSize = 5 * 1024 * 1024;
    if ($file['size'] > $maxSize) {
        return ['url' => null, 'error' => 'Image must be 5 MB or smaller.'];
    }

    $allowedTypes = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/gif' => 'gif',
    ];

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = $finfo ? finfo_file($finfo, $file['tmp_name']) : null;
    if ($finfo) {
        finfo_close($finfo);
    }

    if (!$mimeType || !isset($allowedTypes[$mimeType])) {
        return ['url' => null, 'error' => 'Invalid image type. Allowed: JPG, PNG, WebP, GIF.'];
    }

    if (!@getimagesize($file['tmp_name'])) {
        return ['url' => null, 'error' => 'Uploaded file is not a valid image.'];
    }

    $slugSafe = preg_replace('/[^a-z0-9\-]/', '-', strtolower($slug ?: 'featured'));
    $slugSafe = trim(substr($slugSafe, 0, 80), '-');
    if ($slugSafe === '') {
        $slugSafe = 'featured';
    }

    $filename = $slugSafe . '-' . time() . '.' . $allowedTypes[$mimeType];
    $saveDir = getProjectRootDir() . '/assets/blog-featured/';

    if (!is_dir($saveDir) && !@mkdir($saveDir, 0755, true)) {
        error_log('Featured image upload: could not create directory: ' . $saveDir);
        return ['url' => null, 'error' => 'Could not create upload directory.'];
    }

    $filepath = $saveDir . $filename;
    if (!move_uploaded_file($file['tmp_name'], $filepath)) {
        error_log('Featured image upload: failed to save file: ' . $filepath);
        return ['url' => null, 'error' => 'Failed to save uploaded image.'];
    }

    return [
        'url' => getSiteBaseUrl() . '/assets/blog-featured/' . $filename,
        'error' => null,
    ];
}

/**
 * Resolve featured image from upload (priority) or URL field.
 *
 * @return array{url: string, error: ?string}
 */
function resolveFeaturedImage($urlInput, $fileInput, $slug = '') {
    $url = trim((string) ($urlInput ?? ''));

    $upload = uploadFeaturedImage($fileInput, $slug);
    if ($upload['error']) {
        return ['url' => $url, 'error' => $upload['error']];
    }

    if (!empty($upload['url'])) {
        return ['url' => $upload['url'], 'error' => null];
    }

    return ['url' => $url, 'error' => null];
}
