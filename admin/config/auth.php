<?php
/**
 * Authentication Functions
 * Secure session management and user authentication
 */

// Prevent direct access
if (!defined('ADMIN_PANEL')) {
    die('Direct access not allowed');
}

// Start secure session if not already started
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
        ini_set('session.cookie_secure', 1);
    }
    @session_start();
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['admin_user_id']) && isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

/**
 * Get current user ID
 */
function getCurrentUserId() {
    return isset($_SESSION['admin_user_id']) ? $_SESSION['admin_user_id'] : null;
}

/**
 * Get current user data
 */
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    if (!function_exists('getDB')) {
        require_once __DIR__ . '/db_config.php';
    }
    try {
        $db = getDB();
    } catch (Exception $e) {
        error_log("Database connection error in getCurrentUser: " . $e->getMessage());
        return null;
    }
    
    try {
        $stmt = $db->prepare("SELECT id, username, email, full_name, role FROM admin_users WHERE id = ? AND status = 'active'");
        $stmt->execute([getCurrentUserId()]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log("Error fetching current user: " . $e->getMessage());
        return null;
    }
}

/**
 * Login user
 */
function login($username, $password) {
    if (!function_exists('getDB')) {
        require_once __DIR__ . '/db_config.php';
    }
    try {
        $db = getDB();
    } catch (Exception $e) {
        error_log("Database connection error in login: " . $e->getMessage());
        return false;
    }
    
    try {
        $stmt = $db->prepare("SELECT id, username, email, password_hash, full_name, role, status FROM admin_users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch();
        
        if ($user && $user['status'] === 'active') {
            if (password_verify($password, $user['password_hash'])) {
                // Regenerate session ID for security
                if (session_status() === PHP_SESSION_ACTIVE) {
                    session_regenerate_id(true);
                }
                
                $_SESSION['admin_user_id'] = $user['id'];
                $_SESSION['admin_username'] = $user['username'];
                $_SESSION['admin_role'] = $user['role'];
                $_SESSION['admin_logged_in'] = true;
                
                // Update last login
                try {
                    $updateStmt = $db->prepare("UPDATE admin_users SET last_login = NOW() WHERE id = ?");
                    $updateStmt->execute([$user['id']]);
                } catch (PDOException $e) {
                    // Log but don't fail login if this fails
                    error_log("Error updating last login: " . $e->getMessage());
                }
                
                return true;
            }
        }
        
        return false;
    } catch (PDOException $e) {
        error_log("Login error: " . $e->getMessage());
        return false;
    }
}

/**
 * Logout user
 */
function logout() {
    $_SESSION = array();
    
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time()-3600, '/');
    }
    
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_destroy();
    }
}

/**
 * Require login - redirect if not logged in
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /admin/login.php');
        exit;
    }
}

/**
 * Check if user has admin role
 */
function isAdmin() {
    return isLoggedIn() && isset($_SESSION['admin_role']) && $_SESSION['admin_role'] === 'admin';
}

/**
 * Require admin role
 */
function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header('Location: /admin/index.php');
        exit;
    }
}

/**
 * Generate secure random token
 */
function generateToken($length = 32) {
    return bin2hex(random_bytes($length));
}

/**
 * Sanitize input
 */
function sanitizeInput($data) {
    if (empty($data)) {
        return '';
    }
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Generate slug from title
 */
function generateSlug($title) {
    if (empty($title)) {
        return '';
    }
    // Convert to lowercase
    $slug = strtolower($title);
    
    // Replace spaces and special characters with hyphens
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    
    // Remove leading/trailing hyphens
    $slug = trim($slug, '-');
    
    // Limit length
    $slug = substr($slug, 0, 255);
    
    return $slug;
}

/**
 * Make slug unique by appending number if needed
 */
function makeSlugUnique($slug, $excludeId = null) {
    if (!function_exists('getDB')) {
        require_once __DIR__ . '/db_config.php';
    }
    
    try {
        $db = getDB();
    } catch (Exception $e) {
        error_log("Database connection error in makeSlugUnique: " . $e->getMessage());
        return $slug;
    }
    
    $originalSlug = $slug;
    $counter = 1;
    
    try {
        while (true) {
            if ($excludeId) {
                $stmt = $db->prepare("SELECT COUNT(*) as count FROM blog_posts WHERE slug = ? AND id != ?");
                $stmt->execute([$slug, $excludeId]);
            } else {
                $stmt = $db->prepare("SELECT COUNT(*) as count FROM blog_posts WHERE slug = ?");
                $stmt->execute([$slug]);
            }
            
            $result = $stmt->fetch();
            
            if ($result['count'] == 0) {
                return $slug;
            }
            
            $slug = $originalSlug . '-' . $counter;
            $counter++;
            
            // Prevent infinite loop
            if ($counter > 1000) {
                return $originalSlug . '-' . time();
            }
        }
    } catch (PDOException $e) {
        error_log("Error checking slug uniqueness: " . $e->getMessage());
        return $slug;
    }
}

?>
