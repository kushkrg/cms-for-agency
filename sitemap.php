<?php
/**
 * Evolvcode CMS - XML Sitemap Generator
 */

require_once __DIR__ . '/includes/config.php';

// Set XML content type
header('Content-Type: application/xml; charset=utf-8');

$db = Database::getInstance();

// Get all published content
$pages = $db->fetchAll("SELECT slug, updated_at FROM pages WHERE status = 'published'");
$services = $db->fetchAll("SELECT slug, updated_at FROM services WHERE status = 'published'");
$projects = $db->fetchAll("SELECT slug, updated_at FROM projects WHERE status = 'published'");
$posts = $db->fetchAll("SELECT slug, updated_at, published_at FROM posts WHERE status = 'published'");

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <!-- Static Pages -->
    <url>
        <loc><?= e(SITE_URL) ?>/</loc>
        <changefreq>weekly</changefreq>
        <priority>1.0</priority>
    </url>
    <url>
        <loc><?= e(SITE_URL) ?>/about</loc>
        <changefreq>monthly</changefreq>
        <priority>0.8</priority>
    </url>
    <url>
        <loc><?= e(SITE_URL) ?>/services</loc>
        <changefreq>weekly</changefreq>
        <priority>0.9</priority>
    </url>
    <url>
        <loc><?= e(SITE_URL) ?>/portfolio</loc>
        <changefreq>weekly</changefreq>
        <priority>0.9</priority>
    </url>
    <url>
        <loc><?= e(SITE_URL) ?>/blog</loc>
        <changefreq>daily</changefreq>
        <priority>0.9</priority>
    </url>
    <url>
        <loc><?= e(SITE_URL) ?>/contact</loc>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
    </url>
    
    <!-- Dynamic Pages -->
    <?php foreach ($pages as $page): ?>
    <url>
        <loc><?= e(SITE_URL) ?>/<?= e($page['slug']) ?></loc>
        <lastmod><?= date('Y-m-d', strtotime($page['updated_at'])) ?></lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.6</priority>
    </url>
    <?php endforeach; ?>
    
    <!-- Services -->
    <?php foreach ($services as $service): ?>
    <url>
        <loc><?= e(SITE_URL) ?>/service/<?= e($service['slug']) ?></loc>
        <lastmod><?= date('Y-m-d', strtotime($service['updated_at'])) ?></lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.8</priority>
    </url>
    <?php endforeach; ?>
    
    <!-- Projects -->
    <?php foreach ($projects as $project): ?>
    <url>
        <loc><?= e(SITE_URL) ?>/project/<?= e($project['slug']) ?></loc>
        <lastmod><?= date('Y-m-d', strtotime($project['updated_at'])) ?></lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
    </url>
    <?php endforeach; ?>
    
    <!-- Blog Posts -->
    <?php foreach ($posts as $post): ?>
    <url>
        <loc><?= e(SITE_URL) ?>/post/<?= e($post['slug']) ?></loc>
        <lastmod><?= date('Y-m-d', strtotime($post['updated_at'])) ?></lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.6</priority>
    </url>
    <?php endforeach; ?>
</urlset>
