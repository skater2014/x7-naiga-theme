<?php
/**
 * =========================================================
 * Hub common assets
 * =========================================================
 *
 * hub共通の読み込みだけ。
 * /iezukuri 専用は hub/pages/iezukuri/inc/assets/assets.php で管理。
 * =========================================================
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('naigai_hub_enqueue_common_assets')) {
    function naigai_hub_enqueue_common_assets()
    {
        if (is_admin()) {
            return;
        }

        $theme_dir = get_template_directory();
        $theme_uri = get_template_directory_uri();

        $css = '/hub/common/css/hub.css';

        if (file_exists($theme_dir . $css)) {
            wp_enqueue_style(
                'naigai-hub-common',
                $theme_uri . $css,
                array(),
                filemtime($theme_dir . $css)
            );
        }

        $js = '/hub/common/js/hub-swiper-init.js';

        if (file_exists($theme_dir . $js)) {
            wp_enqueue_script(
                'naigai-hub-swiper-init',
                $theme_uri . $js,
                array(),
                filemtime($theme_dir . $js),
                true
            );
        }
    }

    add_action('wp_enqueue_scripts', 'naigai_hub_enqueue_common_assets', 20);
}
