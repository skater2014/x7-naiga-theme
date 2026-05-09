<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('naigai_customhome_remove_meta_box_on_other_pages')) {
    function naigai_customhome_remove_meta_box_on_other_pages()
    {
        global $post;

        if (!$post || $post->post_type !== 'page') {
            return;
        }

        if (!function_exists('naigai_customhome_page_matches')) {
            return;
        }

        if (naigai_customhome_page_matches($post->ID)) {
            return;
        }

        remove_meta_box('naigai_customhome_meta_box', 'page', 'normal');
    }
    add_action('add_meta_boxes', 'naigai_customhome_remove_meta_box_on_other_pages', 999);
}
