<?php
require_once 'includes/config.php';
require_once 'includes/helpers.php'; // Ensure getPosts is available

$slug = 'best-digital-marketing-agency-in-patna';
$post = getPostBySlug($slug);

if (!$post) {
    echo "Post not found for slug: $slug\n";
    exit;
}

echo "Post ID: " . $post['id'] . "\n";
echo "Category ID: " . ($post['category_id'] ?? 'NULL') . "\n";
echo "Category Name: " . ($post['category_name'] ?? 'NULL') . "\n";

if (!empty($post['category_id'])) {
    echo "\nFetching related posts for category ID: " . $post['category_id'] . "\n";
    $related = getPosts(5, 0, $post['category_id']);
    echo "Found " . count($related) . " posts.\n";
    foreach ($related as $p) {
        echo "- ID: {$p['id']}, Title: {$p['title']}, View URL: /post/{$p['slug']}\n";
    }
} else {
    echo "\nNo category ID, checking if getPosts handles null correctly (it implies all posts).\n";
}

echo "\nChecking all posts in DB:\n";
$db = Database::getInstance();
$all = $db->fetchAll("SELECT id, title, category_id FROM posts");
foreach ($all as $p) {
    echo "ID: {$p['id']}, CatID: {$p['category_id']}, Title: {$p['title']}\n";
}
