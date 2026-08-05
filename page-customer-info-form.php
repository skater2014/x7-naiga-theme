<?php
/*
Template Name: Customer Information Form
*/

if (!defined('ABSPATH')) {
    exit;
}

get_header('77');

/*
 * ============================================================
 * NAIGAI_BACKLINK_CONTACT_20260803
 * お問い合わせ「前のページに戻る」
 * ============================================================
 *
 * フロントページ・不動産・民泊など、
 * 自サイト内のページから /contact へ移動した時だけ表示する。
 *
 * URL直打ち・ブックマーク・外部サイトから来た場合は表示しない。
 *
 * 見た目は不動産で使用している .back-link を再利用する。
 * 新しいCSSは追加しない。
 *
 * そのため、どのページからお問い合わせへ来ても、
 * 固定された特定ページではなく実際の直前ページへ戻れる。
 */
$naigai_contact_referer = wp_get_referer();

$naigai_contact_home_host = wp_parse_url(
    home_url('/'),
    PHP_URL_HOST
);

$naigai_contact_ref_host = $naigai_contact_referer
    ? wp_parse_url($naigai_contact_referer, PHP_URL_HOST)
    : '';

$naigai_contact_current = home_url(
    isset($_SERVER['REQUEST_URI'])
        ? wp_unslash($_SERVER['REQUEST_URI'])
        : '/'
);

$naigai_contact_show_back = (
    $naigai_contact_referer
    && $naigai_contact_home_host
    && $naigai_contact_ref_host
    && strtolower($naigai_contact_home_host) === strtolower($naigai_contact_ref_host)
    && untrailingslashit($naigai_contact_referer) !== untrailingslashit($naigai_contact_current)
);

if ($naigai_contact_show_back) {
    echo '<div class="back-link">';
    echo '<a href="' . esc_url($naigai_contact_referer) . '" onclick="window.history.back(); return false;">';
    echo '<svg class="icon icon-arrow-left2" aria-hidden="true"><use xlink:href="#icon-arrow-left2"></use></svg>';
    echo '<span>前のページに戻る</span>';
    echo '</a>';
    echo '</div>';
}


$template = locate_template('template-parts/contact/customer-info-form-inner.php');
if ($template) {
    include $template;
}

get_footer();
