<?php
/**
 * =========================================================
 * minpaku/inc/assets-b2c-contact.php
 * =========================================================
 *
 * Minpaku B2C CTA / contact form assets.
 *
 * 役割:
 * - B2C固定ページで contact form の元CSS/JSを読む。
 * - CTA Swiper の初期化はここでは行わない。
 * - CTA Swiper は minpaku/b2c/js/minpaku-b2c.js に集約する。
 *
 * 対象:
 * - page-minpaku-b2c.php
 *
 * 読み込むもの:
 * - css/customer-info-form.css
 * - js/customer-info-form.js
 *
 * 注意:
 * - functions.php から移動した処理。
 * - 民泊関連なので minpaku/inc/loader.php 経由で読む。
 * =========================================================
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('naigai_b2c_cta_contact_assets_final')) {
    function naigai_b2c_cta_contact_assets_final()
    {
        if (!is_page_template('page-minpaku-b2c.php')) {
            return;
        }

        $theme_dir = get_template_directory();
        $theme_uri = get_template_directory_uri();

        $form_css = $theme_dir . '/css/customer-info-form.css';
        if (file_exists($form_css)) {
            wp_enqueue_style(
                'naigai-customer-info-form',
                $theme_uri . '/css/customer-info-form.css',
                array(),
                filemtime($form_css)
            );
        }

        $form_js = $theme_dir . '/js/customer-info-form.js';
        if (file_exists($form_js)) {
            wp_enqueue_script(
                'naigai-customer-info-form',
                $theme_uri . '/js/customer-info-form.js',
                array('jquery'),
                filemtime($form_js),
                true
            );
        }
    }

    add_action('wp_enqueue_scripts', 'naigai_b2c_cta_contact_assets_final', 99);
}
