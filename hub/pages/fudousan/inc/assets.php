<?php
/**
 * =========================================================
 * hub/pages/fudousan/inc/assets.php
 * =========================================================
 *
 * /fudousan/ 専用 CSS / JS 読み込みファイル。
 *
 * 初心者向けに言うと:
 * - /fudousan/ ページで必要な「見た目」と「動き」を読む係。
 * - functions.php に直接 CSS / JS 読み込みを書かないために、このファイルへ分離する。
 *
 * このファイルが読むもの:
 *
 * 1. hub/pages/fudousan/css/fudousan.css
 *    役割:
 *    - /fudousan/ ページの見た目。
 *    - ヒーロー、導線カード、カテゴリータブ、物件カード、一覧レイアウトなどを整える。
 *
 * 2. hub/pages/fudousan/js/fudousan-page.js
 *    役割:
 *    - /fudousan/ ページ専用の動き。
 *    - .fudousan-page 内のUIだけを対象にする。
 *    - タブ、ページ内操作、物件カードまわりの補助動作を担当する。
 *
 * 3. js/front-map-modal.js
 *    役割:
 *    - Google Map モーダル用JS。
 *    - .google-location-trigger / [data-map-open] をクリックした時に動く。
 *    - data-map-html に入っている地図HTMLをモーダル内へ表示する。
 *
 * 紐づくテンプレート:
 * - hub/pages/fudousan/template.php
 *
 * 読み込みの流れ:
 * functions.php
 *   ↓
 * inc/loaders/fudousan-loader.php
 *   ↓
 * hub/pages/fudousan/inc/loader.php
 *   ↓
 * hub/pages/fudousan/inc/assets.php
 *
 * 注意:
 * - ここは /fudousan/ 専用。
 * - 民泊、家づくり、トップページのCSS/JSはここに書かない。
 * - テーマ共通 scripts.js は functions.php 側の共通読み込みに残す。
 * =========================================================
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('naigai_fudousan_enqueue_assets')) {
    function naigai_fudousan_enqueue_assets()
    {
        $theme_dir = get_template_directory();
        $theme_uri = get_template_directory_uri();

        $map_js = '/js/front-map-modal.js';

        if (file_exists($theme_dir . $map_js)) {
            wp_enqueue_script(
                'naigai-front-map-modal',
                $theme_uri . $map_js,
                array(),
                filemtime($theme_dir . $map_js),
                true
            );
        }

        if (!is_page('fudousan') && !is_page_template('template-realestate-hub.php')) {
            return;
        }

        $css = '/hub/pages/fudousan/css/fudousan.css';
        if (file_exists($theme_dir . $css)) {
            wp_enqueue_style(
                'naigai-fudousan-page',
                $theme_uri . $css,
                array(),
                filemtime($theme_dir . $css)
            );
        }

        /**
         * Google Map モーダルJS。
         *
         * 役割:
         * - .google-location-trigger / [data-map-open] をクリックした時に動く。
         * - data-map-html に入っている地図HTMLをモーダル表示する。
         *
         * 読み込み順:
         * - fudousan-page.js より先に読む。
         */
        $fudousan_js_deps = array(
            'jquery',
            'isotope',
            'naigai-front-map-modal',
        );

        /**
         * Isotope 依存関係。
         *
         * 役割:
         * - Isotope 本体は functions.php 側で既に読んでいる。
         * - handle 名は functions.php の $enqueue('isotope', ...) の isotope。
         * - この assets.php では Isotope 本体を二重に enqueue しない。
         * - /fudousan/ 専用の fudousan-page.js が $.fn.isotope を使うため、
         *   $fudousan_js_deps に 'isotope' を入れて読み込み順だけ保証する。
         *
         * 注意:
         * - scripts.js は Isotope本体ではない。
         * - scripts.js は $.fn.isotope() を呼ぶ共通処理。
         * - /fudousan/ のタブ処理は hub/pages/fudousan/js/fudousan-page.js 側で管理する。
         */

        /**
         * /fudousan/ ページ専用JS。
         *
         * 役割:
         * - .fudousan-page 内のUIだけを対象にする。
         * - タブ、ページ内操作、物件カードまわりの補助動作を担当する。
         */
        $js = '/hub/pages/fudousan/js/fudousan-page.js';
        if (file_exists($theme_dir . $js)) {
            wp_enqueue_script(
                'naigai-fudousan-page',
                $theme_uri . $js,
                $fudousan_js_deps,
                filemtime($theme_dir . $js),
                true
            );
        }
    }

    add_action('wp_enqueue_scripts', 'naigai_fudousan_enqueue_assets', 60);
}
