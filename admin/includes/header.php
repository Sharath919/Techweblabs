<?php
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}
$user = getCurrentUser();
?>
<header class="admin-header">
    <div class="logo">TechWebLabs Blog</div>
    <div class="user-menu">
        <div class="user-info">
            <span><?php echo htmlspecialchars($user['full_name'] ?? $user['username']); ?></span>
            <span style="color: #9ca3af;"><?php echo htmlspecialchars($user['role']); ?></span>
        </div>
        <a href="change-password.php" class="btn" style="padding: 0.5rem 1rem; font-size: 0.875rem; background: var(--info-color); color: white; text-decoration: none; margin-right: 0.5rem;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline-block; vertical-align: middle; margin-right: 0.25rem;">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
            </svg>
            Change Password
        </a>
        <a href="logout.php" class="btn btn-danger" style="padding: 0.5rem 1rem; font-size: 0.875rem;">Logout</a>
    </div>
</header>
