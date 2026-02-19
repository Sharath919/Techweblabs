<?php
/**
 * Change Password Page
 */
define('ADMIN_PANEL', true);
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/config/db_config.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$user = getCurrentUser();
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        $message = 'All fields are required.';
        $messageType = 'error';
    } elseif ($newPassword !== $confirmPassword) {
        $message = 'New passwords do not match.';
        $messageType = 'error';
    } elseif (strlen($newPassword) < 6) {
        $message = 'New password must be at least 6 characters long.';
        $messageType = 'error';
    } else {
        try {
            $db = getDB();
            
            // Verify current password
            $stmt = $db->prepare("SELECT password_hash FROM admin_users WHERE id = ?");
            $stmt->execute([$user['id']]);
            $userData = $stmt->fetch();
            
            if ($userData && password_verify($currentPassword, $userData['password_hash'])) {
                // Update password
                $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
                $updateStmt = $db->prepare("UPDATE admin_users SET password_hash = ?, updated_at = NOW() WHERE id = ?");
                $result = $updateStmt->execute([$newPasswordHash, $user['id']]);
                
                if ($result) {
                    $message = 'Password changed successfully!';
                    $messageType = 'success';
                } else {
                    $message = 'Failed to update password. Please try again.';
                    $messageType = 'error';
                }
            } else {
                $message = 'Current password is incorrect.';
                $messageType = 'error';
            }
        } catch (Exception $e) {
            error_log("Password change error: " . $e->getMessage());
            $message = 'An error occurred. Please try again.';
            $messageType = 'error';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password | Admin Panel</title>
    <link rel="stylesheet" href="assets/css/admin.css">
    <style>
        .change-password-container {
            max-width: 600px;
            margin: 0 auto;
        }
        
        .password-form {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--dark);
        }
        
        .form-group input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border);
            border-radius: 6px;
            font-size: 1rem;
            transition: border-color 0.2s;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .password-strength {
            margin-top: 0.5rem;
            font-size: 0.875rem;
            color: var(--text-light);
        }
        
        .alert {
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1.5rem;
        }
        
        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #10b981;
        }
        
        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #ef4444;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="admin-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="main-content">
            <div class="page-header">
                <h1>Change Password</h1>
                <p>Update your account password</p>
            </div>
            
            <div class="change-password-container">
                <?php if ($message): ?>
                    <div class="alert alert-<?php echo $messageType; ?>">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>
                
                <div class="password-form">
                    <form method="POST" action="change-password.php">
                        <div class="form-group">
                            <label for="current_password">Current Password</label>
                            <input type="password" id="current_password" name="current_password" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="new_password">New Password</label>
                            <input type="password" id="new_password" name="new_password" required minlength="6">
                            <div class="password-strength">Must be at least 6 characters long</div>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password">Confirm New Password</label>
                            <input type="password" id="confirm_password" name="confirm_password" required minlength="6">
                        </div>
                        
                        <div style="display: flex; gap: 1rem;">
                            <button type="submit" class="btn-ai" style="background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                                Change Password
                            </button>
                            <a href="index.php" class="btn-ai" style="background: var(--text-light); color: white; text-decoration: none;">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
