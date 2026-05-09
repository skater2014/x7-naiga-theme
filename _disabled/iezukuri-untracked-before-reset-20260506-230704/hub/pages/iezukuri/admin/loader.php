<?php
/**
 * hub/pages/iezukuri/admin/loader.php
 *
 * 家づくり 管理画面入口
 *
 * 役割:
 * - 管理画面専用PHPだけを読み込む。
 * - フロント表示用CSS/JSはここでは読まない。
 *
 * 対象:
 * - 固定ページ page の /iezukuri/ 配下入力欄
 * - 間取り詳細 CPT: iez_plan の入力欄
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
