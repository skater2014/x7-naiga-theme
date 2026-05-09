<?php
/**
 * Template Name: Realestate Hub
 * Template Post Type: page
 *
 * 役割:
 * - /fudousan 固定ページの入口テンプレート
 * - header-77.php を読み込む
 * - wp_head() / wp_footer() を通して CSS / JS を正しく読み込ませる
 * - 実際の不動産ページ本文は hub/templates/realestate.php に分離する
 *
 * ファイル構造:
 * template-realestate-hub.php
 *   ├─ get_header('77')
 *   ├─ hub/templates/realestate.php
 *   └─ get_footer()
 */

if (!defined('ABSPATH')) {
    exit;
}

/*
 * 役割:
 * header-77.php を使う。
 * ここがないと wp_head() が出ず、fudousan.css / fudousan-swiper.js が読み込まれない。
 */
get_header('77');

/*
 * 役割:
 * /fudousan の本文HTML。
 * レイアウト本体はこのファイルに置く。
 */
$realestate_template = get_template_directory() . '/hub/templates/realestate.php';

if (file_exists($realestate_template)) {
    require $realestate_template;
}

/*
 * 役割:
 * footer.php を読み込む。
 * wp_footer() が出ることでJSも正しく出る。
 */
get_footer();
