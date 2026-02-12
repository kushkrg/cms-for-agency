<?php
/**
 * Evolvcode CMS - Router for PHP Built-in Server
 * 
 * Usage: php -S localhost:8000 router.php
 * 
 * This file handles URL routing when using PHP's built-in development server.
 * In production with Apache, .htaccess handles this automatically.
 */

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// If the request is for an existing file or directory, serve it directly
if ($uri !== '/' && file_exists(__DIR__ . $uri)) {
    // Check if it's a PHP file that should be executed
    if (pathinfo($uri, PATHINFO_EXTENSION) === 'php') {
        include __DIR__ . $uri;
        return true;
    }
    // For static files (CSS, JS, images), let PHP's built-in server handle them
    return false;
}

// API routes
if (preg_match('#^/api/(.+)$#', $uri, $matches)) {
    $apiFile = __DIR__ . '/api/' . $matches[1];
    if (!pathinfo($matches[1], PATHINFO_EXTENSION)) {
        $apiFile .= '.php';
    }
    if (file_exists($apiFile)) {
        include $apiFile;
        return true;
    }
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'API endpoint not found']);
    return true;
}

// Admin routes - serve admin files directly
if (preg_match('#^/admin/?(.*)$#', $uri, $matches)) {
    $adminPath = $matches[1] ?: 'index';
    $adminFile = __DIR__ . '/admin/' . $adminPath;
    
    // If it's a file request without extension, add .php
    if (!pathinfo($adminPath, PATHINFO_EXTENSION) && file_exists($adminFile . '.php')) {
        include $adminFile . '.php';
        return true;
    }
    
    // If exact file exists
    if (file_exists($adminFile)) {
        if (is_dir($adminFile)) {
            include $adminFile . '/index.php';
        } else {
            include $adminFile;
        }
        return true;
    }
    
    // 404 for admin
    http_response_code(404);
    include __DIR__ . '/404.php';
    return true;
}

// Map clean URLs to PHP files
$routes = [
    '/' => 'index.php',
    '/about' => 'about.php',
    '/services' => 'services.php',
    '/portfolio' => 'portfolio.php',
    '/blog' => 'blog.php',
    '/contact' => 'contact.php',
    '/career' => 'career.php',
    '/sitemap' => 'sitemap.php',
    '/sitemap.xml' => 'sitemap.php',
];

// Check for exact route match
if (isset($routes[$uri])) {
    include __DIR__ . '/' . $routes[$uri];
    return true;
}

// Check for dynamic routes (service, project, post detail pages)
// /service/{slug}
if (preg_match('#^/service/([a-zA-Z0-9-]+)/?$#', $uri, $matches)) {
    $_GET['slug'] = $matches[1];
    include __DIR__ . '/service.php';
    return true;
}

// /project/{slug}
if (preg_match('#^/project/([a-zA-Z0-9-]+)/?$#', $uri, $matches)) {
    $_GET['slug'] = $matches[1];
    include __DIR__ . '/project.php';
    return true;
}

// /post/{slug} or /blog/{slug}
if (preg_match('#^/(?:post|blog)/([a-zA-Z0-9-]+)/?$#', $uri, $matches)) {
    $_GET['slug'] = $matches[1];
    include __DIR__ . '/post.php';
    return true;
}

// Assets and uploads - let the server handle them
if (preg_match('#^/(assets|uploads)/#', $uri)) {
    return false;
}

// Dynamic pages from DB (e.g., /thank-you, /career, etc.)
$slug = ltrim($uri, '/');
if ($slug && !str_contains($slug, '/')) {
    $_GET['slug'] = $slug;
    include __DIR__ . '/page.php';
    return true;
}

// If nothing matched, show 404
http_response_code(404);
include __DIR__ . '/404.php';
return true;
