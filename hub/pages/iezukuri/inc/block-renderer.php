<?php
/**
 * 家づくり サブページ用ブロック描画
 *
 * page-content/{slug}.php には巨大HTMLを書かず、
 * blocks/block-*.php を組み合わせて表示する。
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('naigai_iezukuri_render_block')) {
    function naigai_iezukuri_render_block($block_name, $args = array()) {
        $block_name = sanitize_key($block_name);

        if ($block_name === '') {
            return;
        }

        $file = get_template_directory()
            . '/hub/pages/iezukuri/templates/components/block-'
            . $block_name
            . '.php';

        if (!file_exists($file)) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                echo "\n<!-- missing iezukuri block: " . esc_html($block_name) . " -->\n";
            }
            return;
        }

        $page_id = get_queried_object_id();

        if (!$page_id) {
            $page_id = get_the_ID();
        }

        include $file;
    }
}

if (!function_exists('naigai_iezukuri_render_blocks')) {
    function naigai_iezukuri_render_blocks($blocks = array()) {
        if (empty($blocks) || !is_array($blocks)) {
            return;
        }

        foreach ($blocks as $block) {
            if (is_string($block)) {
                naigai_iezukuri_render_block($block);
                continue;
            }

            if (!is_array($block) || empty($block['name'])) {
                continue;
            }

            $args = isset($block['args']) && is_array($block['args']) ? $block['args'] : array();

            naigai_iezukuri_render_block($block['name'], $args);
        }
    }
}
