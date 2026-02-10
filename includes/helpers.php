<?php
/**
 * Evolvcode CMS - Helper Functions
 * 
 * Common utility functions used throughout the application.
 */

/**
 * Shorthand for Security::escape()
 */
function e(mixed $value): string
{
    return Security::escape($value);
}

/**
 * Create a URL-friendly slug from a string
 */
function createSlug(string $text): string
{
    return Security::slugify($text);
}

/**
 * Get site setting from database
 */
function getSetting(string $key, mixed $default = null): mixed
{
    static $settings = null;
    
    if ($settings === null) {
        $db = Database::getInstance();
        $results = $db->fetchAll("SELECT setting_key, setting_value FROM settings");
        $settings = [];
        foreach ($results as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
    }
    
    return $settings[$key] ?? $default;
}

/**
 * Update site setting
 */
function updateSetting(string $key, mixed $value): bool
{
    $db = Database::getInstance();
    
    if ($db->exists('settings', 'setting_key = ?', [$key])) {
        return $db->update('settings', ['setting_value' => $value], 'setting_key = ?', [$key]) > 0;
    } else {
        return $db->insert('settings', ['setting_key' => $key, 'setting_value' => $value]) > 0;
    }
}

/**
 * Get all published services
 */
function getServices(int $limit = 0): array
{
    $db = Database::getInstance();
    $sql = "SELECT * FROM services WHERE status = 'published' ORDER BY sort_order ASC, id ASC";
    if ($limit > 0) {
        $sql .= " LIMIT " . (int) $limit;
    }
    return $db->fetchAll($sql);
}

/**
 * Get single service by slug
 */
function getServiceBySlug(string $slug): ?array
{
    $db = Database::getInstance();
    return $db->fetchOne("SELECT * FROM services WHERE slug = ? AND status = 'published'", [$slug]);
}

/**
 * Get all published projects
 */
function getProjects(int $limit = 0, bool $featuredOnly = false): array
{
    $db = Database::getInstance();
    $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug 
            FROM projects p 
            LEFT JOIN portfolio_categories c ON p.category_id = c.id 
            WHERE p.status = 'published'";
    
    if ($featuredOnly) {
        $sql .= " AND p.is_featured = 1";
    }
    
    $sql .= " ORDER BY p.created_at DESC";
    
    if ($limit > 0) {
        $sql .= " LIMIT " . (int) $limit;
    }
    
    return $db->fetchAll($sql);
}

/**
 * Get single project by slug
 */
function getProjectBySlug(string $slug): ?array
{
    $db = Database::getInstance();
    return $db->fetchOne(
        "SELECT p.*, c.name as category_name, c.slug as category_slug 
         FROM projects p 
         LEFT JOIN portfolio_categories c ON p.category_id = c.id 
         WHERE p.slug = ? AND p.status = 'published'",
        [$slug]
    );
}

/**
 * Get portfolio categories
 */
function getPortfolioCategories(): array
{
    $db = Database::getInstance();
    return $db->fetchAll("SELECT * FROM portfolio_categories ORDER BY name ASC");
}

/**
 * Get all published posts
 */
function getPosts(int $limit = 0, int $offset = 0, ?int $categoryId = null): array
{
    $db = Database::getInstance();
    $params = [];
    
    $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug 
            FROM posts p 
            LEFT JOIN blog_categories c ON p.category_id = c.id 
            WHERE p.status = 'published'";
    
    if ($categoryId !== null) {
        $sql .= " AND p.category_id = ?";
        $params[] = $categoryId;
    }
    
    $sql .= " ORDER BY p.published_at DESC";
    
    if ($limit > 0) {
        $sql .= " LIMIT " . (int) $limit;
        if ($offset > 0) {
            $sql .= " OFFSET " . (int) $offset;
        }
    }
    
    return $db->fetchAll($sql, $params);
}

/**
 * Get single post by slug
 */
function getPostBySlug(string $slug): ?array
{
    $db = Database::getInstance();
    return $db->fetchOne(
        "SELECT p.*, c.name as category_name, c.slug as category_slug 
         FROM posts p 
         LEFT JOIN blog_categories c ON p.category_id = c.id 
         WHERE p.slug = ? AND p.status = 'published'",
        [$slug]
    );
}

/**
 * Get blog categories
 */
function getBlogCategories(): array
{
    $db = Database::getInstance();
    return $db->fetchAll(
        "SELECT c.*, COUNT(p.id) as post_count 
         FROM blog_categories c 
         LEFT JOIN posts p ON c.id = p.category_id AND p.status = 'published'
         GROUP BY c.id 
         ORDER BY c.name ASC"
    );
}

/**
 * Get page by slug
 */
function getPageBySlug(string $slug): ?array
{
    $db = Database::getInstance();
    return $db->fetchOne("SELECT * FROM pages WHERE slug = ? AND status = 'published'", [$slug]);
}

/**
 * Get active team members
 */
function getTeamMembers(): array
{
    $db = Database::getInstance();
    return $db->fetchAll("SELECT * FROM team_members WHERE status = 'active' ORDER BY sort_order ASC, id ASC");
}

/**
 * Count unread contact submissions
 */
function getUnreadMessagesCount(): int
{
    $db = Database::getInstance();
    return $db->count('contact_submissions', 'is_read = 0');
}

/**
 * Format date for display
 */
function formatDate(string $date, string $format = 'F j, Y'): string
{
    return date($format, strtotime($date));
}

/**
 * Create excerpt from content
 */
function createExcerpt(string $content, int $length = 150): string
{
    $text = strip_tags($content);
    $text = trim($text);
    
    if (strlen($text) <= $length) {
        return $text;
    }
    
    $text = substr($text, 0, $length);
    $lastSpace = strrpos($text, ' ');
    
    if ($lastSpace !== false) {
        $text = substr($text, 0, $lastSpace);
    }
    
    return $text . '...';
}

/**
 * Generate pagination HTML
 */
function paginate(int $totalItems, int $currentPage, int $perPage, string $baseUrl): string
{
    $totalPages = ceil($totalItems / $perPage);
    
    if ($totalPages <= 1) {
        return '';
    }
    
    $html = '<nav class="pagination" aria-label="Page navigation">';
    $html .= '<ul class="pagination-list">';
    
    // Previous button
    if ($currentPage > 1) {
        $html .= '<li><a href="' . e($baseUrl) . '?page=' . ($currentPage - 1) . '" class="pagination-link prev">&laquo; Previous</a></li>';
    }
    
    // Page numbers
    $start = max(1, $currentPage - 2);
    $end = min($totalPages, $currentPage + 2);
    
    if ($start > 1) {
        $html .= '<li><a href="' . e($baseUrl) . '?page=1" class="pagination-link">1</a></li>';
        if ($start > 2) {
            $html .= '<li><span class="pagination-ellipsis">...</span></li>';
        }
    }
    
    for ($i = $start; $i <= $end; $i++) {
        $activeClass = $i === $currentPage ? ' active' : '';
        $html .= '<li><a href="' . e($baseUrl) . '?page=' . $i . '" class="pagination-link' . $activeClass . '">' . $i . '</a></li>';
    }
    
    if ($end < $totalPages) {
        if ($end < $totalPages - 1) {
            $html .= '<li><span class="pagination-ellipsis">...</span></li>';
        }
        $html .= '<li><a href="' . e($baseUrl) . '?page=' . $totalPages . '" class="pagination-link">' . $totalPages . '</a></li>';
    }
    
    // Next button
    if ($currentPage < $totalPages) {
        $html .= '<li><a href="' . e($baseUrl) . '?page=' . ($currentPage + 1) . '" class="pagination-link next">Next &raquo;</a></li>';
    }
    
    $html .= '</ul>';
    $html .= '</nav>';
    
    return $html;
}

/**
 * Set flash message
 */
function setFlash(string $type, string $message): void
{
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Get and clear flash message
 */
function getFlash(): ?array
{
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Display flash message HTML
 */
function displayFlash(): string
{
    $flash = getFlash();
    if (!$flash) {
        return '';
    }
    
    return '<div class="alert alert-' . e($flash['type']) . '">' . e($flash['message']) . '</div>';
}

/**
 * Check if current URL matches path
 */
function isCurrentPage(string $path): bool
{
    $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    return $currentPath === $path;
}

/**
 * Get active class for navigation
 */
function getActiveClass(string $path): string
{
    return isCurrentPage($path) ? 'active' : '';
}

/**
 * Generate meta tags
 */
function generateMetaTags(string $title, string $description = '', string $keywords = '', string $image = ''): string
{
    $siteName = getSetting('site_name', 'Evolvcode');
    $fullTitle = $title ? $title . ' | ' . $siteName : $siteName;
    $description = $description ?: getSetting('site_description', '');
    
    $html = '<title>' . e($fullTitle) . '</title>' . "\n";
    $html .= '<meta name="description" content="' . e($description) . '">' . "\n";
    
    if ($keywords) {
        $html .= '<meta name="keywords" content="' . e($keywords) . '">' . "\n";
    }
    
    // Open Graph
    $html .= '<meta property="og:title" content="' . e($fullTitle) . '">' . "\n";
    $html .= '<meta property="og:description" content="' . e($description) . '">' . "\n";
    $html .= '<meta property="og:type" content="website">' . "\n";
    $html .= '<meta property="og:url" content="' . e(SITE_URL . $_SERVER['REQUEST_URI']) . '">' . "\n";
    
    if ($image) {
        $html .= '<meta property="og:image" content="' . e(SITE_URL . $image) . '">' . "\n";
    }
    
    // Twitter Card
    $html .= '<meta name="twitter:card" content="summary_large_image">' . "\n";
    $html .= '<meta name="twitter:title" content="' . e($fullTitle) . '">' . "\n";
    $html .= '<meta name="twitter:description" content="' . e($description) . '">' . "\n";
    
    if ($image) {
        $html .= '<meta name="twitter:image" content="' . e(SITE_URL . $image) . '">' . "\n";
    }
    
    return $html;
}

/**
 * Convert features string to array
 */
function parseFeatures(string $features): array
{
    if (empty($features)) {
        return [];
    }
    return array_map('trim', explode(',', $features));
}

/**
 * Get reading time estimate
 */
function getReadingTime(string $content): int
{
    $wordCount = str_word_count(strip_tags($content));
    $minutes = ceil($wordCount / 200); // Average reading speed
    return max(1, $minutes);
}
/**
 * Get menu items by location
 */
function getMenu(string $location): array
{
    $db = Database::getInstance();
    
    // Get menu ID
    $menu = $db->fetchOne("SELECT id FROM menus WHERE location = ?", [$location]);
    
    if (!$menu) {
        return [];
    }
    
    // Get menu items - fetch as array
    $items = $db->fetchAll(
        "SELECT * FROM menu_items WHERE menu_id = ? AND status = 'active' ORDER BY sort_order ASC",
        [$menu['id']]
    );
    
    // Build tree structure
    $tree = [];
    $lookup = [];
    
    // Initialize lookup with children array
    foreach ($items as $item) {
        $item['children'] = [];
        $lookup[$item['id']] = $item;
    }
    
    // Build tree using references
    foreach ($lookup as $id => &$item) {
        if ($item['parent_id'] && isset($lookup[$item['parent_id']])) {
            $lookup[$item['parent_id']]['children'][] = &$item;
        } else {
            $tree[] = &$item;
        }
    }
    
    return $tree;
}
