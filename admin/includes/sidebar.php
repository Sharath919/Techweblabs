<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<aside class="admin-sidebar">
    <nav>
        <ul class="sidebar-menu">
            <li>
                <a href="index.php" class="<?php echo $currentPage == 'index.php' ? 'active' : ''; ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                    </svg>
                    Dashboard
                </a>
            </li>
            <li>
                <a href="posts.php" class="<?php echo in_array($currentPage, ['posts.php', 'post-new.php', 'post-edit.php']) ? 'active' : ''; ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                    </svg>
                    Posts
                </a>
            </li>
            <li>
                <a href="categories.php" class="<?php echo $currentPage == 'categories.php' ? 'active' : ''; ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h9l2 2h7a2 2 0 0 1 2 2z"></path>
                    </svg>
                    Categories
                </a>
            </li>
            <li>
                <a href="tags.php" class="<?php echo $currentPage == 'tags.php' ? 'active' : ''; ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                        <line x1="7" y1="7" x2="7.01" y2="7"></line>
                    </svg>
                    Tags
                </a>
            </li>
            <li>
                <a href="ai-article-generator.php" class="<?php echo $currentPage == 'ai-article-generator.php' ? 'active' : ''; ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
                        <path d="M2 17l10 5 10-5"></path>
                        <path d="M2 12l10 5 10-5"></path>
                    </svg>
                    <span>🤖 AI Generator</span>
                </a>
            </li>
            <?php if (isAdmin()): ?>
            <li>
                <a href="users.php" class="<?php echo $currentPage == 'users.php' ? 'active' : ''; ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                    Users
                </a>
            </li>
            <?php endif; ?>
            <li>
                <a href="change-password.php" class="<?php echo $currentPage == 'change-password.php' ? 'active' : ''; ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>
                    Change Password
                </a>
            </li>
            <li>
                <a href="/" target="_blank">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                        <polyline points="15 3 21 3 21 9"></polyline>
                        <line x1="10" y1="14" x2="21" y2="3"></line>
                    </svg>
                    View Site
                </a>
            </li>
        </ul>
    </nav>
</aside>
