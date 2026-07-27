<?php
require_once 'd:/KKN/public_html/wp-load.php';

echo "=== 1. PERMALINKS & REWRITE RULES ===\n";
global $wp_rewrite;
$wp_rewrite->set_permalink_structure('/%postname%/');
flush_rewrite_rules();
echo "Flushed rewrite rules with /%postname%/ structure!\n";

echo "\n=== 2. PAGE INSPECTION & REPAIR ===\n";
// Ensure Page 2733 is published and has slug 'statistik-desa'
wp_update_post([
    'ID'           => 2733,
    'post_title'   => 'Statistik Desa',
    'post_name'    => 'statistik-desa',
    'post_status'  => 'publish',
    'post_content' => '[statistik_desa]',
]);

// Turn off Elementor edit mode on Page 2733 so shortcode renders natively
delete_post_meta(2733, '_elementor_edit_mode');
delete_post_meta(2733, '_elementor_data');

echo "Page 2733 status: " . get_post_status(2733) . " | Permalink: " . get_permalink(2733) . "\n";

echo "\n=== 3. NAV MENUS INSPECTION & REPAIR ===\n";
$menus = wp_get_nav_menus();
foreach ($menus as $menu) {
    echo "NAV MENU: {$menu->name} (ID: {$menu->term_id})\n";
    $items = wp_get_nav_menu_items($menu->term_id);
    if (!empty($items)) {
        foreach ($items as $item) {
            $title = trim($item->title);
            echo "  - Item ID: {$item->ID} | Title: {$title} | URL: {$item->url} | Object ID: {$item->object_id}\n";
            
            // Check if title contains 'Data dan Infografis' or 'Data'
            if (stripos($title, 'Data dan Infografis') !== false || stripos($title, 'Data & Infografis') !== false || $item->object_id == 2733) {
                // Update menu item title to 'Statistik Desa' and url to page 2733 permalink
                update_post_meta($item->ID, '_menu_item_title', 'Statistik Desa');
                update_post_meta($item->ID, '_menu_item_url', get_permalink(2733));
                update_post_meta($item->ID, '_menu_item_object_id', 2733);
                wp_update_post([
                    'ID'         => $item->ID,
                    'post_title' => 'Statistik Desa'
                ]);
                echo "    >>> REPAIRED Menu Item ID {$item->ID} -> Title: 'Statistik Desa' | URL: " . get_permalink(2733) . "\n";
            }
        }
    }
}

echo "\n=== 4. CHECK ELEMENTOR HEADER / NEXER / THEME HEADERS ===\n";
// Search for Elementor templates or header posts containing "Data dan Infografis"
$header_posts = get_posts([
    'post_type' => ['elementor_library', 'nav_menu_item', 'page', 'header'],
    'posts_per_page' => -1,
    's' => 'Data dan Infografis'
]);
echo "Found " . count($header_posts) . " posts/templates containing 'Data dan Infografis':\n";
foreach ($header_posts as $hp) {
    echo "  - ID: {$hp->ID} | Type: {$hp->post_type} | Title: {$hp->post_title}\n";
    // Replace text in post_content if any
    if (strpos($hp->post_content, 'Data dan Infografis') !== false) {
        $new_content = str_replace('Data dan Infografis', 'Statistik Desa', $hp->post_content);
        wp_update_post(['ID' => $hp->ID, 'post_content' => $new_content]);
        echo "    --> Replaced text in post_content for ID {$hp->ID}\n";
    }
}
