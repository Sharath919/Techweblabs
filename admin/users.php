<?php
/**
 * Users Management Page (Admin Only)
 */
define('ADMIN_PANEL', true);
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/config/db_config.php';
requireAdmin(); // Only admins can manage users

$db = getDB();
$error = '';
$success = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add') {
            $username = sanitizeInput($_POST['username'] ?? '');
            $email = sanitizeInput($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $fullName = sanitizeInput($_POST['full_name'] ?? '');
            $role = sanitizeInput($_POST['role'] ?? 'editor');
            
            if (empty($username) || empty($email) || empty($password)) {
                $error = 'Username, email, and password are required.';
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                try {
                    $stmt = $db->prepare("INSERT INTO admin_users (username, email, password_hash, full_name, role, status) VALUES (?, ?, ?, ?, ?, 'active')");
                    $stmt->execute([$username, $email, $hash, $fullName, $role]);
                    $success = 'User added successfully!';
                } catch (PDOException $e) {
                    $error = 'Error adding user: ' . $e->getMessage();
                }
            }
        } elseif ($_POST['action'] === 'delete' && isset($_POST['id'])) {
            $userId = (int)$_POST['id'];
            // Prevent deleting yourself
            if ($userId == getCurrentUserId()) {
                $error = 'You cannot delete your own account.';
            } else {
                try {
                    $db->prepare("DELETE FROM admin_users WHERE id = ?")->execute([$userId]);
                    $success = 'User deleted successfully!';
                } catch (PDOException $e) {
                    $error = 'Error deleting user: ' . $e->getMessage();
                }
            }
        } elseif ($_POST['action'] === 'toggle_status' && isset($_POST['id'])) {
            $userId = (int)$_POST['id'];
            // Prevent deactivating yourself
            if ($userId == getCurrentUserId()) {
                $error = 'You cannot deactivate your own account.';
            } else {
                try {
                    $stmt = $db->prepare("UPDATE admin_users SET status = IF(status='active', 'inactive', 'active') WHERE id = ?");
                    $stmt->execute([$userId]);
                    $success = 'User status updated!';
                } catch (PDOException $e) {
                    $error = 'Error updating user: ' . $e->getMessage();
                }
            }
        }
    }
}

// Get all users
$users = $db->query("SELECT * FROM admin_users ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users - Admin Panel</title>
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="admin-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="main-content">
            <div class="page-header">
                <h1>User Management</h1>
            </div>
            
            <?php if ($error): ?>
                <div class="content-section" style="background: #fee; border: 1px solid #fcc; color: #c33; margin-bottom: 1rem;">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="content-section" style="background: #efe; border: 1px solid #cfc; color: #3c3; margin-bottom: 1rem;">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>
            
            <!-- Add User Form -->
            <div class="content-section mb-2">
                <h2 style="margin-bottom: 1rem;">Add New User</h2>
                <form method="POST" action="">
                    <input type="hidden" name="action" value="add">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="user-username">Username *</label>
                            <input type="text" id="user-username" name="username" required>
                        </div>
                        <div class="form-group">
                            <label for="user-email">Email *</label>
                            <input type="email" id="user-email" name="email" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="user-password">Password *</label>
                            <input type="password" id="user-password" name="password" required>
                        </div>
                        <div class="form-group">
                            <label for="user-full-name">Full Name</label>
                            <input type="text" id="user-full-name" name="full_name">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="user-role">Role</label>
                        <select id="user-role" name="role">
                            <option value="editor">Editor</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Add User</button>
                </form>
            </div>
            
            <!-- Users List -->
            <div class="content-section">
                <h2 style="margin-bottom: 1rem;">All Users</h2>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Full Name</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Last Login</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($users)): ?>
                                <tr>
                                    <td colspan="7" class="text-center">No users found.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($user['username']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td><?php echo htmlspecialchars($user['full_name'] ?? '-'); ?></td>
                                        <td><span class="badge"><?php echo ucfirst($user['role']); ?></span></td>
                                        <td>
                                            <span class="badge badge-<?php echo $user['status'] === 'active' ? 'published' : 'draft'; ?>">
                                                <?php echo ucfirst($user['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo $user['last_login'] ? date('M d, Y', strtotime($user['last_login'])) : 'Never'; ?></td>
                                        <td>
                                            <div class="action-buttons">
                                                <?php if ($user['id'] != getCurrentUserId()): ?>
                                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure?');">
                                                        <input type="hidden" name="action" value="toggle_status">
                                                        <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                                                        <button type="submit" class="btn-icon" title="Toggle Status">
                                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                                                                <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                                        <input type="hidden" name="action" value="delete">
                                                        <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                                                        <button type="submit" class="btn-icon btn-danger" title="Delete">
                                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                                <polyline points="3 6 5 6 21 6"></polyline>
                                                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                <?php else: ?>
                                                    <span class="text-muted">(You)</span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
    
    <script src="assets/js/admin.js"></script>
</body>
</html>
