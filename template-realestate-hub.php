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
 * ============================================================
 * NAIGAI_BACKLINK_FUDOUSAN_20260803
 * 不動産トップ「前のページに戻る」
 * ============================================================
 *
 * フロントページなど自サイト内から /fudousan へ来た時だけ表示。
 * URL直打ち・ブックマーク・外部サイトから来た場合は表示しない。
 *
 * 不動産物件ページですでに使用している .back-link を再利用する。
 * そのため、この修正では新しいCSSを追加しない。
 *
 * 戻り先は固定URLではなく実際の直前ページ。
 */
$naigai_fudousan_referer = wp_get_referer();

$naigai_fudousan_home_host = wp_parse_url(
    home_url('/'),
    PHP_URL_HOST
);

$naigai_fudousan_ref_host = $naigai_fudousan_referer
    ? wp_parse_url($naigai_fudousan_referer, PHP_URL_HOST)
    : '';

$naigai_fudousan_current = home_url(
    isset($_SERVER['REQUEST_URI'])
        ? wp_unslash($_SERVER['REQUEST_URI'])
        : '/'
);

$naigai_fudousan_show_back = (
    $naigai_fudousan_referer
    && $naigai_fudousan_home_host
    && $naigai_fudousan_ref_host
    && strtolower($naigai_fudousan_home_host) === strtolower($naigai_fudousan_ref_host)
    && untrailingslashit($naigai_fudousan_referer) !== untrailingslashit($naigai_fudousan_current)
);

if ($naigai_fudousan_show_back) {
    echo '<div class="back-link">';
    echo '<a href="' . esc_url($naigai_fudousan_referer) . '" onclick="window.history.back(); return false;">';
    echo '<svg class="icon icon-arrow-left2" aria-hidden="true"><use xlink:href="#icon-arrow-left2"></use></svg>';
    echo '<span>前のページに戻る</span>';
    echo '</a>';
    echo '</div>';
}


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
