<?php
/**
 * 家づくり共通レイアウトCSS
 *
 * 役割:
 * - /iezukuri/ と /iezukuri/* で共通の .ch-shell 幅を読む
 * - functions.php には書かない
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('naigai_iezukuri_should_enqueue_common_layout')) {
    function naigai_iezukuri_should_enqueue_common_layout() {
        if (is_singular('iez_plan')) {
            return true;
        }

        if (!is_page()) {
            return false;
        }

        $post = get_queried_object();

        if (!$post instanceof WP_Post) {
            return false;
        }

        $uri = trim((string) get_page_uri($post), '/');

        return $uri === 'iezukuri' || strpos($uri, 'iezukuri/') === 0;
    }
}

add_action('wp_enqueue_scripts', function () {
    if (!naigai_iezukuri_should_enqueue_common_layout()) {
        return;
    }

    $path = get_template_directory() . '/hub/pages/iezukuri/css/common/layout.css';
    $uri  = get_template_directory_uri() . '/hub/pages/iezukuri/css/common/layout.css';

    wp_enqueue_style(
        'naigai-iezukuri-common-layout',
        $uri,
        array(),
        file_exists($path) ? filemtime($path) : null
    );
}, 30);
