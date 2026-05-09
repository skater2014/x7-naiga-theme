<?php
/**
 * =========================================================
 * hub/pages/iezukuri/admin/loader.php
 * =========================================================
 *
 * 家づくり 管理画面専用の入口。
 *
 * 初心者向けに言うと:
 * - WordPress管理画面の入力欄やメタボックスを読み込む係。
 * - フロント表示のCSS/JSはここでは読まない。
 * - /iezukuri/ の公開ページ表示とは別。
 *
 * 読み込むもの:
 * - admin/enqueue.php
 *   → 管理画面用CSS/JS
 *
 * - admin/metaboxes/subpage-metabox.php
 *   → 固定ページ側の入力欄
 *
 * - admin/metaboxes/plans-metabox.php
 *   → iez_plan 側の入力欄
 *
 * - admin/metaboxes/page-content-metabox.php
 *   → /iezukuri/new-house/ /chuko/ /nisetai/ などの本文入力欄
 *
 * 注意:
 * - ここは管理画面だけ。
 * - 3カード切替のフロントJS/CSSは inc/assets.php 側で読む。
 * =========================================================
 */

if (!defined('ABSPATH')) {
    exit;
}

$iez_admin_enqueue = __DIR__ . '/enqueue.php';
if (file_exists($iez_admin_enqueue)) {
    require_once $iez_admin_enqueue;
}

$iez_subpage_metabox = __DIR__ . '/metaboxes/subpage-metabox.php';
if (file_exists($iez_subpage_metabox)) {
    require_once $iez_subpage_metabox;
}

$iez_plans_metabox = __DIR__ . '/metaboxes/plans-metabox.php';
if (file_exists($iez_plans_metabox)) {
    require_once $iez_plans_metabox;
}

/**
 * 固定ページ本文メタボックス
 * - /iezukuri/new-house/
 * - /iezukuri/chuko/
 * - /iezukuri/nisetai/
 */
$naigai_iezukuri_page_content_metabox = __DIR__ . '/metaboxes/page-content-metabox.php';
if (file_exists($naigai_iezukuri_page_content_metabox)) {
    require_once $naigai_iezukuri_page_content_metabox;
}
