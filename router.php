<?php
// Router for PHP built-in server to support friendly slugs
// Usage: php -S localhost:8000 router.php

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$docRoot = __DIR__;

// Set DOCUMENT_ROOT for PHP built-in server (required by some pages)
if (!isset($_SERVER['DOCUMENT_ROOT']) || empty($_SERVER['DOCUMENT_ROOT'])) {
    $_SERVER['DOCUMENT_ROOT'] = $docRoot;
}

// Remove trailing slash (except for root)
if ($uri !== '/' && substr($uri, -1) === '/') {
    $uri = rtrim($uri, '/');
    header("Location: $uri", true, 301);
    exit;
}

// Serve static files directly (images, CSS, JS, etc.)
$staticFile = realpath($docRoot . $uri);
if ($staticFile && is_file($staticFile) && strpos($staticFile, $docRoot) === 0) {
    return false; // let built-in server handle static files
}

// Homepage
if ($uri === '/' || $uri === '') {
    require $docRoot . '/index.php';
    return true;
}

// Try mapping top-level slug to ondemand PHP file
// e.g. /pharmeasy-clone -> /pages/ondemand/pharmeasy-clone.php
$slug = trim($uri, '/');
$candidate = $docRoot . '/pages/ondemand/' . $slug . '.php';
if (is_file($candidate)) {
    try {
        require $candidate;
        return true;
    } catch (Throwable $e) {
        // Log error but don't expose to user in production
        error_log("Router error loading $candidate: " . $e->getMessage());
        // Still return true to prevent fallthrough
        return true;
    }
}

// Common special-case routes that exist under ondemand
$special = [
    'about' => '/pages/ondemand/about.php',
    'careers' => '/pages/ondemand/careers.php',
    'contact' => '/pages/ondemand/contact.php',
    'full-stack-development-course-with-guaranteed-placement' => '/pages/ondemand/full-stack-development.php',
    // New URL mappings for SEO-optimized slugs
    'food-delivery-app-development' => '/pages/ondemand/food-delivery-app.php',
    'grocery-app-development' => '/pages/ondemand/grocery-delivery-app.php',
    'on-demand-home-services-app-development' => '/pages/ondemand/home-services-app.php',
    'on-demand-taxi-booking-app-development' => '/pages/ondemand/Ride-SharingApp.php',
    'healthcare-mobile-app-development' => '/pages/ondemand/healthcare-apps.php',
    'handyman-mobile-app-development' => '/pages/ondemand/professional-services-app.php',
    'fitness-mobile-app-development-company' => '/pages/ondemand/fitness-app.php',
    'pet-care-app-development' => '/pages/ondemand/pet-care-app.php',
    'beauty-salon-app-development-company' => '/pages/ondemand/beauty-salon-app.php',
    'education-app-development' => '/pages/ondemand/education-app.php',
    'real-estate-app-development' => '/pages/ondemand/real-estate.php',
    'ecommerce-app-development' => '/pages/ondemand/e-commerce-app.php',
    'car-rental-app-development' => '/pages/ondemand/automotive-app.php',
    'beauty-services-app-development' => '/pages/ondemand/beauty-salon-app.php',
    'doctor-consultation-app-development' => '/pages/ondemand/consultation-app.php',
    'social-media-app-development' => '/pages/ondemand/socialmedia-app.php',
    'logistics-transportation-app-development' => '/pages/ondemand/logistics-app.php',
    'e-learning-app-development' => '/pages/ondemand/learning-education-app.php',
    'job-portal-app-development' => '/pages/ondemand/job-boards-apps.php',
    'cashback-app-development' => '/pages/ondemand/cashback-coupons.php',
    'ott-platform-app-development' => '/pages/ondemand/ott-applications-app.php',
    'dating-app-development' => '/pages/ondemand/dating-applications.php',
    'chat-app-development-company' => '/pages/ondemand/chat-applications.app.php',
    'erp-software-development' => '/pages/ondemand/Enterprise-Resource-Planning.php',
    'crm-software-development' => '/pages/ondemand/coustmer-relationship.php',
    'hrm-software-development' => '/pages/ondemand/human-resource-app.php',
    'inventory-management-software-development' => '/pages/ondemand/inventory-management.php',
];
if (isset($special[$slug]) && is_file($docRoot . $special[$slug])) {
    require $docRoot . $special[$slug];
    return true;
}

// Fallback: try direct PHP file under project root
$directPhp = $docRoot . '/' . $slug . '.php';
if (is_file($directPhp)) {
    require $directPhp;
    return true;
}

// Blog routes - SEO-friendly URLs
if (strpos($slug, 'blogs/') === 0) {
    $blogSlug = substr($slug, 6); // Remove 'blogs/' prefix
    
    // Try to load blog post
    if (!defined('ADMIN_PANEL')) {
        define('ADMIN_PANEL', true); // Required by db_config
    }
    require_once $docRoot . '/admin/config/db_config.php';
    try {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM blog_posts WHERE slug = ? AND status = 'published'");
        $stmt->execute([$blogSlug]);
        $post = $stmt->fetch();
        
        if ($post) {
            // Increment view count
            $db->prepare("UPDATE blog_posts SET views = views + 1 WHERE id = ?")->execute([$post['id']]);
            
            // Set post in global scope for template
            $GLOBALS['blog_post'] = $post;
            
            // Load blog post template
            if (is_file($docRoot . '/pages/blogs/post.php')) {
                require $docRoot . '/pages/blogs/post.php';
                return true;
            }
        }
    } catch (Exception $e) {
        // Database error - continue to 404
        error_log("Blog post error: " . $e->getMessage());
    }
}

// Blog listing page
if ($slug === 'blogs' || $slug === 'blog') {
    if (is_file($docRoot . '/pages/blogs/index.php')) {
        require $docRoot . '/pages/blogs/index.php';
        return true;
    }
}

// Last resort: serve homepage
require $docRoot . '/index.php';
return true;