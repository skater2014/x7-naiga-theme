<?php
/**
 * ============================================================
 * サイト内共通「前のページに戻る」
 * ============================================================
 *
 * 【目的】
 * archive / category / taxonomy など、
 * これまでback linkが存在しなかった一覧階層で使用する。
 *
 * 【既存デザインを維持】
 * 新しいCSSは作らず、テーマですでに使われている
 * .back-link のHTML・CSSをそのまま使用する。
 *
 * 【表示する場合】
 * このサイト内の別ページから現在ページへ移動してきた場合。
 *
 * 【表示しない場合】
 * ・URLを直接入力した
 * ・ブックマークから開いた
 * ・外部サイトから来た
 * ・Refererを取得できない
 * ・現在ページ自身がRefererになっている
 *
 * 【重要】
 * 「一覧へ戻る」「詳細ページへ戻る」など、
 * 戻り先に明確な意味がある既存導線はこの部品では変更しない。
 * ============================================================
 */

if (!defined('ABSPATH')) {
    exit;
}

$naigai_internal_back_referer = wp_get_referer();

if (!$naigai_internal_back_referer) {
    return;
}

$naigai_internal_home_host = wp_parse_url(
    home_url('/'),
    PHP_URL_HOST
);

$naigai_internal_ref_host = wp_parse_url(
    $naigai_internal_back_referer,
    PHP_URL_HOST
);

/*
 * 外部サイトから来た場合は表示しない。
 */
if (
    !$naigai_internal_home_host ||
    !$naigai_internal_ref_host ||
    strtolower($naigai_internal_home_host) !== strtolower($naigai_internal_ref_host)
) {
    return;
}

/*
 * 現在ページ自身への戻りリンクを防ぐ。
 */
$naigai_internal_request_uri = isset($_SERVER['REQUEST_URI'])
    ? wp_unslash($_SERVER['REQUEST_URI'])
    : '/';

$naigai_internal_current_url = home_url(
    $naigai_internal_request_uri
);

if (
    untrailingslashit($naigai_internal_current_url)
    ===
    untrailingslashit($naigai_internal_back_referer)
) {
    return;
}
?>

<div class="back-link">
    <a
        href="<?php echo esc_url($naigai_internal_back_referer); ?>"
        onclick="window.history.back(); return false;"
    >
        <svg class="icon icon-arrow-left2" aria-hidden="true">
            <use xlink:href="#icon-arrow-left2"></use>
        </svg>
        <span>前のページに戻る</span>
    </a>
</div>