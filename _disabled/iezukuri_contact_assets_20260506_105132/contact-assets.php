<?php
/**
 * hub/pages/iezukuri/inc/contact-assets.php
 *
 * /iezukuri/contact/ 専用 assets
 *
 * 役割:
 * - 既存の contact form partial:
 *   template-parts/contact/customer-info-form-inner.php
 *   を /iezukuri/contact/ で使う場合に、フォーム用CSS/JSを確実に読み込む。
 *
 * - iezukuri 専用の contact.css も読み込む。
 *   ここでは Q&A の ＋ / − 表示など、家づくり contact ページ固有の補正を行う。
 *
 * 方針:
 * - functions.php 側の既存フォーム処理は壊さない。
 * - 既存CSSが存在する場合だけ追加で enqueue する。
 * - ないファイルは無理に読まない。
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('naigai_iezukuri_is_contact_page')) {
    /**
     * /iezukuri/contact/ かどうか判定する。
     */
    function naigai_iezukuri_is_contact_page() {
        if (!is_page()) {
            return false;
        }

        $post = get_post();

        if (!$post) {
            return false;
        }

        if ($post->post_name === 'contact') {
            $ancestors = get_post_ancestors($post);

            foreach ($ancestors as $ancestor_id) {
                $ancestor = get_post($ancestor_id);

                if ($ancestor && $ancestor->post_name === 'iezukuri') {
                    return true;
                }
            }
        }

        return trim((string) get_page_uri($post), '/') === 'iezukuri/contact';
    }
}

if (!function_exists('naigai_iezukuri_enqueue_file_if_exists')) {
    /**
     * テーマ内ファイルが存在する時だけ enqueue する小さい helper。
     */
    function naigai_iezukuri_enqueue_file_if_exists($handle, $relative_path, $type = 'style', $deps = array()) {
        $relative_path = ltrim((string) $relative_path, '/');
        $abs = get_template_directory() . '/' . $relative_path;

        if (!file_exists($abs)) {
            return false;
        }

        $url = get_template_directory_uri() . '/' . $relative_path;
        $ver = filemtime($abs);

        if ($type === 'script') {
            wp_enqueue_script($handle, $url, $deps, $ver, true);
        } else {
            wp_enqueue_style($handle, $url, $deps, $ver);
        }

        return true;
    }
}

if (!function_exists('naigai_iezukuri_enqueue_contact_assets')) {
    function naigai_iezukuri_enqueue_contact_assets() {
        if (!naigai_iezukuri_is_contact_page()) {
            return;
        }

        /**
         * 既存の問い合わせフォームCSS候補。
         *
         * 環境によって置き場所が違っても拾えるように、
         * 代表的な候補を順番に見る。
         */
        $form_css_candidates = array(
            'css/customer-info-form.css',
            'assets/css/customer-info-form.css',
            'template-parts/contact/customer-info-form.css',
            'template-parts/contact/css/customer-info-form.css',
        );

        foreach ($form_css_candidates as $i => $path) {
            naigai_iezukuri_enqueue_file_if_exists(
                'naigai-customer-info-form-' . $i,
                $path,
                'style'
            );
        }

        /**
         * 既存の問い合わせフォームJS候補。
         */
        $form_js_candidates = array(
            'js/customer-info-form.js',
            'assets/js/customer-info-form.js',
            'template-parts/contact/customer-info-form.js',
            'template-parts/contact/js/customer-info-form.js',
        );

        foreach ($form_js_candidates as $i => $path) {
            naigai_iezukuri_enqueue_file_if_exists(
                'naigai-customer-info-form-js-' . $i,
                $path,
                'script',
                array('jquery')
            );
        }

        /**
         * /iezukuri/contact/ 固有CSS。
         * Q&A の ＋ / − や、フォーム表示の最低限の整えをここで行う。
         */
        naigai_iezukuri_enqueue_file_if_exists(
            'naigai-iezukuri-contact',
            'hub/pages/iezukuri/css/pages/contact.css',
            'style'
        );
    }

    add_action('wp_enqueue_scripts', 'naigai_iezukuri_enqueue_contact_assets', 30);
}
