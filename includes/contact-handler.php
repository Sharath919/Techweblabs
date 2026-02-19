<?php
/**
 * Contact Form Handler
 * 
 * This file handles contact form submissions with proper validation and security.
 * Include this file in your contact form processing.
 * 
 * Usage:
 * 1. Include this file in your contact page
 * 2. Call handleContactForm() after form submission
 * 3. Display success/error messages to user
 */

// Prevent direct access
if (!defined('ROOT_DIR')) {
    define('ROOT_DIR', $_SERVER['DOCUMENT_ROOT'] . '/');
}

/**
 * Generate CSRF token
 */
function generateCSRFToken() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verifyCSRFToken($token) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Sanitize input data
 */
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Validate email
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate phone number (basic validation)
 */
function validatePhone($phone) {
    // Remove all non-numeric characters
    $phone = preg_replace('/[^0-9]/', '', $phone);
    // Check if it's between 10-15 digits
    return strlen($phone) >= 10 && strlen($phone) <= 15;
}

/**
 * Rate limiting - prevent spam
 */
function checkRateLimit($ip, $maxAttempts = 5, $timeWindow = 3600) {
    $rateLimitFile = ROOT_DIR . 'includes/rate_limit_' . md5($ip) . '.txt';
    $currentTime = time();
    
    if (file_exists($rateLimitFile)) {
        $data = json_decode(file_get_contents($rateLimitFile), true);
        if ($data && ($currentTime - $data['first_attempt']) < $timeWindow) {
            if ($data['attempts'] >= $maxAttempts) {
                return false; // Rate limit exceeded
            }
            $data['attempts']++;
        } else {
            // Reset if time window passed
            $data = ['first_attempt' => $currentTime, 'attempts' => 1];
        }
    } else {
        $data = ['first_attempt' => $currentTime, 'attempts' => 1];
    }
    
    file_put_contents($rateLimitFile, json_encode($data));
    return true;
}

/**
 * Send email notification
 */
function sendContactEmail($name, $email, $phone, $message, $requirement) {
    $to = "info@techweblabs.com"; // Change to your email
    $subject = "New Contact Form Submission from " . $name;
    
    $body = "New contact form submission:\n\n";
    $body .= "Name: " . $name . "\n";
    $body .= "Email: " . $email . "\n";
    $body .= "Phone: " . $phone . "\n";
    $body .= "Requirement: " . $requirement . "\n";
    $body .= "Message: " . $message . "\n";
    $body .= "\nSubmitted on: " . date('Y-m-d H:i:s') . "\n";
    $body .= "IP Address: " . $_SERVER['REMOTE_ADDR'] . "\n";
    
    $headers = "From: " . $name . " <" . $email . ">\r\n";
    $headers .= "Reply-To: " . $email . "\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();
    
    return mail($to, $subject, $body, $headers);
}

/**
 * Main contact form handler
 */
function handleContactForm() {
    $response = [
        'success' => false,
        'message' => '',
        'errors' => []
    ];
    
    // Check if form was submitted
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $response['message'] = 'Invalid request method.';
        return $response;
    }
    
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        $response['message'] = 'Security token verification failed. Please refresh and try again.';
        return $response;
    }
    
    // Rate limiting
    $ip = $_SERVER['REMOTE_ADDR'];
    if (!checkRateLimit($ip)) {
        $response['message'] = 'Too many requests. Please try again later.';
        return $response;
    }
    
    // Get and sanitize form data
    $name = isset($_POST['name']) ? sanitizeInput($_POST['name']) : '';
    $email = isset($_POST['email']) ? sanitizeInput($_POST['email']) : '';
    $phone = isset($_POST['mobile']) ? sanitizeInput($_POST['mobile']) : '';
    $message = isset($_POST['message']) ? sanitizeInput($_POST['message']) : '';
    $requirement = isset($_POST['Dtype']) ? sanitizeInput($_POST['Dtype']) : '';
    
    // Validation
    if (empty($name)) {
        $response['errors']['name'] = 'Name is required.';
    } elseif (strlen($name) < 2) {
        $response['errors']['name'] = 'Name must be at least 2 characters.';
    } elseif (strlen($name) > 100) {
        $response['errors']['name'] = 'Name must be less than 100 characters.';
    }
    
    if (empty($email)) {
        $response['errors']['email'] = 'Email is required.';
    } elseif (!validateEmail($email)) {
        $response['errors']['email'] = 'Please enter a valid email address.';
    }
    
    if (empty($phone)) {
        $response['errors']['mobile'] = 'Phone number is required.';
    } elseif (!validatePhone($phone)) {
        $response['errors']['mobile'] = 'Please enter a valid phone number.';
    }
    
    if (empty($message)) {
        $response['errors']['message'] = 'Message is required.';
    } elseif (strlen($message) < 10) {
        $response['errors']['message'] = 'Message must be at least 10 characters.';
    } elseif (strlen($message) > 1000) {
        $response['errors']['message'] = 'Message must be less than 1000 characters.';
    }
    
    if (empty($requirement)) {
        $response['errors']['Dtype'] = 'Please select a requirement.';
    }
    
    // If there are errors, return them
    if (!empty($response['errors'])) {
        $response['message'] = 'Please correct the errors below.';
        return $response;
    }
    
    // Send email
    if (sendContactEmail($name, $email, $phone, $message, $requirement)) {
        $response['success'] = true;
        $response['message'] = 'Thank you! Your message has been sent successfully. We will get back to you soon.';
        
        // Optional: Save to database
        // saveContactToDatabase($name, $email, $phone, $message, $requirement);
    } else {
        $response['message'] = 'Sorry, there was an error sending your message. Please try again later.';
    }
    
    return $response;
}

// If this file is accessed directly via POST, handle the form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_form_submit'])) {
    header('Content-Type: application/json');
    echo json_encode(handleContactForm());
    exit;
}


