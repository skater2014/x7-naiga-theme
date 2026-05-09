<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('naigai_iez_intro_is_admin_page')) {
    function naigai_iez_intro_is_admin_page(string $hook): bool
    {
        return in_array($hook, array(
            'toplevel_page_naigai-iez-intro',
            'toplevel_page_naigai-iezukuri-intro',
        ), true);
    }
}

if (!function_exists('naigai_iez_intro_stock_urls')) {
    function naigai_iez_intro_stock_urls(string $side): array
    {
        $side = sanitize_key($side);

        if (!in_array($side, array('nasu', 'tokyo'), true)) {
            return array();
        }

        $upload = wp_upload_dir();
        $dir = trailingslashit($upload['basedir']) . 'iezukuri-intro-stock/' . $side;
        $url = trailingslashit($upload['baseurl']) . 'iezukuri-intro-stock/' . $side;

        if (!is_dir($dir)) {
            return array();
        }

        $files = glob($dir . '/*.{jpg,jpeg,png,webp,gif}', GLOB_BRACE);
        if (!$files) {
            return array();
        }

        sort($files, SORT_NATURAL);

        $items = array();
        foreach ($files as $file) {
            $items[] = $url . '/' . basename($file);
        }

        return $items;
    }
}

if (!function_exists('naigai_iez_intro_admin_enqueue_stock')) {
    function naigai_iez_intro_admin_enqueue_stock(string $hook): void
    {
        if (!naigai_iez_intro_is_admin_page($hook)) {
            return;
        }

        wp_enqueue_media();

        $script_rel = '/hub/pages/iezukuri/js/intro-overlay-admin.js';
        $script_abs = get_template_directory() . $script_rel;
        $script_url = get_template_directory_uri() . $script_rel;

        wp_enqueue_script(
            'naigai-iez-intro-overlay-admin',
            $script_url,
            array('jquery'),
            file_exists($script_abs) ? (string) filemtime($script_abs) : null,
            true
        );

        wp_localize_script(
            'naigai-iez-intro-overlay-admin',
            'NaigaiIezIntroAdmin',
            array(
                'stock' => array(
                    'nasu'  => naigai_iez_intro_stock_urls('nasu'),
                    'tokyo' => naigai_iez_intro_stock_urls('tokyo'),
                ),
                'labels' => array(
                    'inUse'   => '使用中',
                    'restore' => 'イントロに戻す',
                    'remove'  => 'イントロから外す',
                    'empty'   => 'まだ画像が入っていません。',
                ),
            )
        );
    }
}
add_action('admin_enqueue_scripts', 'naigai_iez_intro_admin_enqueue_stock');
