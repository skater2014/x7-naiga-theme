<?php
if (!defined('ABSPATH')) exit;

/**
 * 民泊機能の入口
 * functions.php からはこれ1本だけ読む
 */

require_once __DIR__ . '/inc/context.php';
require_once __DIR__ . '/inc/enqueue.php';

/* === MINPAKU B2C CONTACT ASSETS START === */

/**
 * ============================================================
 * 民泊B2Cページ：お問い合わせフォーム用CSS / JS
 * ============================================================
 *
 * 対象:
 * - page-minpaku-b2c.php を利用する民泊固定ページ
 *
 * 読み込む実ファイル:
 * - minpaku/inc/assets-b2c-contact.php
 *
 * そのファイルから、
 *
 * - css/customer-info-form.css
 * - js/customer-info-form.js
 *
 * を必要なページだけenqueueする。
 *
 *
 * 【読み込み構造】
 *
 * functions.php
 *      ↓
 * minpaku/init.php
 *      ↓
 * inc/assets-b2c-contact.php
 *
 *
 *
 * 民泊機能の入口を minpaku/init.php に一本化し、
 * functions.php に民泊固有処理を増やさない。
 */
$naigai_minpaku_b2c_contact_assets =
    __DIR__ . '/inc/assets-b2c-contact.php';

if (file_exists($naigai_minpaku_b2c_contact_assets)) {
    require_once $naigai_minpaku_b2c_contact_assets;
}

/* === MINPAKU B2C CONTACT ASSETS END === */

if (file_exists(__DIR__ . '/inc/common-core.php')) {
    require_once __DIR__ . '/inc/common-core.php';
}
if (file_exists(__DIR__ . '/inc/admin/archive-settings.php')) {
    require_once __DIR__ . '/inc/admin/archive-settings.php';
}
if (file_exists(__DIR__ . '/inc/booking.php')) {
    require_once __DIR__ . '/inc/booking.php';
}
if (file_exists(__DIR__ . '/inc/checkout-endpoint.php')) {
    require_once __DIR__ . '/inc/checkout-endpoint.php';
}
if (file_exists(__DIR__ . '/inc/admin/b2c-admin.php')) {
    require_once __DIR__ . '/inc/admin/b2c-admin.php';
}

/*
 * 民泊運営サポートLP 管理画面
 * 対象: slug=minpaku の固定ページ
 */
if (file_exists(__DIR__ . '/inc/admin/page-support-admin.php')) {
    require_once __DIR__ . '/inc/admin/page-support-admin.php';
}


if (file_exists(__DIR__ . '/inc/admin/page-support-url-select.php')) {
    require_once __DIR__ . '/inc/admin/page-support-url-select.php';
}

if (file_exists(__DIR__ . '/inc/admin/page-support-metabox-cleanup.php')) {
    require_once __DIR__ . '/inc/admin/page-support-metabox-cleanup.php';
}


/* B2B管理画面はB2Cと衝突中のため一時停止 */

/*
 * NAIGAI_MINPAKU_FRONTEND_LOCALIZE_LOADER
 *
 * Stripe公開可能キー、Ajax URL、nonceを
 * minpaku-single-jsへ渡す既存処理を読み込む。
 */
$naigai_minpaku_frontend_localize =
    __DIR__ . '/inc/frontend/localize.php';

if (file_exists($naigai_minpaku_frontend_localize)) {
    require_once $naigai_minpaku_frontend_localize;
}
