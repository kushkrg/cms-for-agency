<?php
/**
 * Evolvcode CMS - Admin Header
 */

require_once __DIR__ . '/../../includes/config.php';

// Require authentication for all admin pages
Auth::requireAuth();

// Get current page
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$currentAdmin = Auth::getCurrentAdmin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Dashboard') ?> - Admin | <?= e(getSetting('site_name', 'Evolvcode')) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?= e(ADMIN_URL) ?>/assets/css/admin.css">
</head>
<body class="admin-body">
    <!-- Sidebar -->
    <aside class="admin-sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="<?= e(ADMIN_URL) ?>" class="sidebar-logo">
                <span class="logo-text">EVOLVCODE</span>
            </a>
            <button class="sidebar-toggle" id="sidebar-toggle">
                <i class="fas fa-bars"></i>
            </button>
        </div>
        
        <nav class="sidebar-nav">
            <ul class="nav-list">
                <li class="nav-item">
                    <a href="<?= e(ADMIN_URL) ?>" class="nav-link <?= $currentPage === 'index' ? 'active' : '' ?>">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                
                <?php if (Auth::hasPermission('pages') || Auth::hasPermission('services') || Auth::hasPermission('projects') || Auth::hasPermission('posts')): ?>
                <li class="nav-section">Content</li>
                <?php endif; ?>
                
                <?php if (Auth::hasPermission('pages')): ?>
                <li class="nav-item">
                    <a href="<?= e(ADMIN_URL) ?>/pages.php" class="nav-link <?= $currentPage === 'pages' ? 'active' : '' ?>">
                        <i class="fas fa-file-alt"></i>
                        <span>Pages</span>
                    </a>
                </li>
                <?php endif; ?>
                
                <?php if (Auth::hasPermission('services')): ?>
                <li class="nav-item">
                    <a href="<?= e(ADMIN_URL) ?>/services.php" class="nav-link <?= $currentPage === 'services' ? 'active' : '' ?>">
                        <i class="fas fa-concierge-bell"></i>
                        <span>Services</span>
                    </a>
                </li>
                <?php endif; ?>
                
                <?php if (Auth::hasPermission('projects')): ?>
                <li class="nav-item">
                    <a href="<?= e(ADMIN_URL) ?>/projects.php" class="nav-link <?= $currentPage === 'projects' ? 'active' : '' ?>">
                        <i class="fas fa-briefcase"></i>
                        <span>Portfolio</span>
                    </a>
                </li>
                <?php endif; ?>
                
                <?php if (Auth::hasPermission('posts')): ?>
                <li class="nav-item has-submenu <?= in_array($currentPage, ['posts', 'blog-categories']) ? 'open' : '' ?>">
                    <a href="#" class="nav-link submenu-toggle <?= in_array($currentPage, ['posts', 'blog-categories']) ? 'active' : '' ?>">
                        <i class="fas fa-newspaper"></i>
                        <span>Blog</span>
                        <i class="fas fa-chevron-right submenu-arrow"></i>
                    </a>
                    <ul class="submenu">
                        <li>
                            <a href="<?= e(ADMIN_URL) ?>/posts.php" class="<?= $currentPage === 'posts' ? 'active' : '' ?>">
                                <i class="fas fa-pen-to-square"></i> All Posts
                            </a>
                        </li>
                        <li>
                            <a href="<?= e(ADMIN_URL) ?>/blog-categories.php" class="<?= $currentPage === 'blog-categories' ? 'active' : '' ?>">
                                <i class="fas fa-folder"></i> Categories
                            </a>
                        </li>
                    </ul>
                </li>
                <?php endif; ?>
                
                <?php if (Auth::hasPermission('homepage') || Auth::hasPermission('menus')): ?>
                <li class="nav-section">Theme</li>
                <?php endif; ?>
                
                <?php if (Auth::hasPermission('homepage')): ?>
                <li class="nav-item">
                    <a href="<?= e(ADMIN_URL) ?>/theme-homepage.php" class="nav-link <?= $currentPage === 'theme-homepage' ? 'active' : '' ?>">
                        <i class="fas fa-home"></i>
                        <span>Homepage</span>
                    </a>
                </li>
                <?php endif; ?>
                
                <?php if (Auth::hasPermission('menus')): ?>
                <li class="nav-item">
                    <a href="<?= e(ADMIN_URL) ?>/theme-menus.php" class="nav-link <?= $currentPage === 'theme-menus' ? 'active' : '' ?>">
                        <i class="fas fa-bars"></i>
                        <span>Menus</span>
                    </a>
                </li>
                <?php endif; ?>

                <li class="nav-section">Management</li>
                
                <?php if (Auth::hasPermission('forms')): ?>
                <li class="nav-item">
                    <a href="<?= e(ADMIN_URL) ?>/forms.php" class="nav-link <?= $currentPage === 'forms' || $currentPage === 'form-fields' ? 'active' : '' ?>">
                        <i class="fas fa-wpforms"></i>
                        <span>Forms</span>
                    </a>
                </li>
                <?php endif; ?>

                <?php if (Auth::hasPermission('submissions')): ?>
                <li class="nav-item">
                    <a href="<?= e(ADMIN_URL) ?>/form-submissions.php" class="nav-link <?= $currentPage === 'form-submissions' ? 'active' : '' ?>">
                        <i class="fas fa-inbox"></i>
                        <span>Submissions</span>
                        <?php 
                        $unreadFormCount = Database::getInstance()->count('form_submissions', "status = 'unread'");
                        if ($unreadFormCount > 0): 
                        ?>
                        <span class="badge"><?= $unreadFormCount ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <?php endif; ?>
                
                <?php if (Auth::hasPermission('contacts')): ?>
                <li class="nav-item">
                    <a href="<?= e(ADMIN_URL) ?>/contacts.php" class="nav-link <?= $currentPage === 'contacts' ? 'active' : '' ?>">
                        <i class="fas fa-envelope"></i>
                        <span>Contact Messages</span>
                        <?php 
                        $unreadCount = Database::getInstance()->count('contact_submissions', "is_read = 0");
                        if ($unreadCount > 0): 
                        ?>
                        <span class="badge"><?= $unreadCount ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <?php endif; ?>
                
                <?php if (Auth::hasPermission('media')): ?>
                <li class="nav-item">
                    <a href="<?= e(ADMIN_URL) ?>/media.php" class="nav-link <?= $currentPage === 'media' ? 'active' : '' ?>">
                        <i class="fas fa-images"></i>
                        <span>Media Library</span>
                    </a>
                </li>
                <?php endif; ?>
                
                <?php if (Auth::hasPermission('settings')): ?>
                <li class="nav-item">
                    <a href="<?= e(ADMIN_URL) ?>/settings.php" class="nav-link <?= $currentPage === 'settings' ? 'active' : '' ?>">
                        <i class="fas fa-cog"></i>
                        <span>Settings</span>
                    </a>
                </li>
                <?php endif; ?>

                <?php if (Auth::hasPermission('security')): ?>
                <li class="nav-item">
                    <a href="<?= e(ADMIN_URL) ?>/security.php" class="nav-link <?= $currentPage === 'security' ? 'active' : '' ?>">
                        <i class="fas fa-shield-alt"></i>
                        <span>Security</span>
                    </a>
                </li>
                <?php endif; ?>

                <?php if (Auth::hasPermission('users')): ?>
                <li class="nav-item">
                    <a href="<?= e(ADMIN_URL) ?>/users.php" class="nav-link <?= $currentPage === 'users' ? 'active' : '' ?>">
                        <i class="fas fa-users-cog"></i>
                        <span>Users</span>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </nav>
        
        <div class="sidebar-footer">
            <a href="<?= e(SITE_URL) ?>" class="view-site" target="_blank">
                <i class="fas fa-external-link-alt"></i>
                <span>View Website</span>
            </a>
        </div>
    </aside>
    
    <!-- Main Content -->
    <main class="admin-main">
        <!-- Top Bar -->
        <header class="admin-topbar">
            <button class="mobile-toggle" id="mobile-toggle">
                <i class="fas fa-bars"></i>
            </button>
            
            <div class="topbar-right">
                <div class="admin-dropdown">
                    <button class="dropdown-toggle">
                        <span class="admin-avatar">
                            <?= strtoupper(substr($currentAdmin['username'], 0, 1)) ?>
                        </span>
                        <span class="admin-name">
                            <?= e($currentAdmin['username']) ?>
                            <small style="display: block; font-size: 0.7rem; color: var(--color-gray-400); font-weight: 400;"><?= e(Auth::roleName()) ?></small>
                        </span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="dropdown-menu">
                        <a href="<?= e(ADMIN_URL) ?>/profile.php">
                            <i class="fas fa-user"></i> Profile
                        </a>
                        <a href="<?= e(ADMIN_URL) ?>/logout.php">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </div>
                </div>
            </div>
        </header>
        
        <!-- Page Content -->
        <div class="admin-content">

<?php if (isset($_SESSION['flash_error'])): ?>
<div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= e($_SESSION['flash_error']) ?></div>
<?php unset($_SESSION['flash_error']); endif; ?>
