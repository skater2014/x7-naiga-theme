<?php
/**
 * =========================================================
 * hub/pages/iezukuri/inc/assets.php
 * /iezukuri 専用 CSS / JS 読み込み管理
 * =========================================================
 *
 * 役割:
 * - /iezukuri トップ用 JS/CSS
 * - /iezukuri/plan/{slug} 詳細ページ用 JS
 *
 * 注意:
 * - loader.php には enqueue を書かない
 * - CSS/JS本体は css/ js/ に置く
 * - 関数名衝突を避けるため v2 prefix で固定
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('naigai_iez_assets_v2_is_home_target')) {
    function naigai_iez_assets_v2_is_home_target() {
        if (is_admin()) {
            return false;
        }

        if (is_page('iezukuri')) {
            return true;
        }

        $post = get_queried_object();

        return ($post && isset($post->post_name) && $post->post_name === 'iezukuri');
    }
}

if (!function_exists('naigai_iez_assets_v2_enqueue_style')) {
    function naigai_iez_assets_v2_enqueue_style($handle, $rel, $deps = array()) {
        $theme_dir = get_template_directory();
        $theme_uri = get_template_directory_uri();

        if (!file_exists($theme_dir . $rel)) {
            return;
        }

        wp_enqueue_style(
            $handle,
            $theme_uri . $rel,
            $deps,
            filemtime($theme_dir . $rel)
        );
    }
}

if (!function_exists('naigai_iez_assets_v2_enqueue_script')) {
    function naigai_iez_assets_v2_enqueue_script($handle, $rel, $deps = array()) {
        $theme_dir = get_template_directory();
        $theme_uri = get_template_directory_uri();

        if (!file_exists($theme_dir . $rel)) {
            return;
        }

        wp_enqueue_script(
            $handle,
            $theme_uri . $rel,
            $deps,
            filemtime($theme_dir . $rel),
            true
        );
    }
}

if (!function_exists('naigai_iez_assets_v2_enqueue_home_switch')) {
    function naigai_iez_assets_v2_enqueue_home_switch() {
        naigai_iez_assets_v2_enqueue_style(
            'naigai-iez-plan-detail-switch',
            '/hub/pages/iezukuri/css/iezukuri-plan-detail-switch.css'
        );

        naigai_iez_assets_v2_enqueue_script(
            'naigai-iez-plan-detail-switch',
            '/hub/pages/iezukuri/js/iezukuri-plan-detail-switch.js'
        );

        if (wp_script_is('naigai-iez-plan-detail-switch', 'enqueued')) {
            $payload = array(
                'terms'       => array(),
                'contact_url' => home_url('/iezukuri/contact/'),
            );

            if (function_exists('naigai_iez_plan_home_switch_payload')) {
                $payload = naigai_iez_plan_home_switch_payload();
            }

            wp_add_inline_script(
                'naigai-iez-plan-detail-switch',
                'window.NAIGAI_IEZ_PLAN_HOME=' . wp_json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . ';',
                'before'
            );
        }
    }
}


if (!function_exists('naigai_iez_assets_v2_enqueue_hero')) {
    function naigai_iez_assets_v2_enqueue_hero() {
        if (
            !is_page() &&
            !is_post_type_archive('iez_plan')
        ) {
            return;
        }

        naigai_iez_assets_v2_enqueue_script(
            'naigai-iezukuri-hero-js',
            '/hub/pages/iezukuri/js/iezukuri-hero.js'
        );
    }
}

if (!function_exists('naigai_iez_assets_v2_enqueue_plan_detail')) {
    function naigai_iez_assets_v2_enqueue_plan_detail() {
        
        naigai_iez_assets_v2_enqueue_style(
            'naigai-iez-plan-detail-viewer-style',
            '/hub/pages/iezukuri/css/subpage/plan-detail-viewer.css',
            array('naigai-iezukuri-base', 'naigai-iezukuri-subpage')
        );

naigai_iez_assets_v2_enqueue_script(
            'naigai-iez-plan-detail-viewer',
            '/hub/pages/iezukuri/js/iezukuri-plan-detail-viewer.js'
        );
    }
}

if (!function_exists('naigai_iez_assets_v2_enqueue')) {
    function naigai_iez_assets_v2_enqueue() {
        naigai_iez_assets_v2_enqueue_hero();
        if (naigai_iez_assets_v2_is_home_target()) {
            naigai_iez_assets_v2_enqueue_home_switch();
        }

        if (is_singular('iez_plan')) {
            naigai_iez_assets_v2_enqueue_plan_detail();
        }
    }
}

add_action('wp_enqueue_scripts', 'naigai_iez_assets_v2_enqueue', 30);













/**
 * /iezukuri/contact/ に既存問い合わせフォームCSS用の互換body classを付ける。
 *
 * 理由:
 * - css/customer-info-form.css は
 *   body.page-template-page-customer-info-form .customer-info-field ...
 *   のようなセレクタを多く持っている。
 * - /iezukuri/contact/ は page-template-page-construction-hub-sub のため、
 *   そのままだと一部の input / select / textarea / button にCSSが当たらない。
 *
 * 方針:
 * - 新しいフォームCSSは作らない。
 * - 既存の css/customer-info-form.css をそのまま使う。
 * - /iezukuri/contact/ の時だけ body class を足して互換させる。
 */
if (!function_exists('naigai_iezukuri_contact_customer_form_body_class')) {
    function naigai_iezukuri_contact_customer_form_body_class($classes) {
        if (!is_page()) {
            return $classes;
        }

        $post = get_post();

        if (!$post || trim((string) get_page_uri($post), '/') !== 'iezukuri/contact') {
            return $classes;
        }

        $classes[] = 'page-template-page-customer-info-form';
        $classes[] = 'page-template-page-customer-info-form-php';

        return array_values(array_unique($classes));
    }

    add_filter('body_class', 'naigai_iezukuri_contact_customer_form_body_class', 20);
}
