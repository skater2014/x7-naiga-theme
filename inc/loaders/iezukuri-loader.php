<?php
/**
 * =========================================================
 * inc/loaders/iezukuri-loader.php
 * =========================================================
 *
 * 家づくり機能の正規入口を読み込むための中継 loader。
 *
 * 初心者向けに言うと:
 * - functions.php を大きくしすぎないための整理ファイル。
 * - functions.php からこのファイルを1回だけ読む。
 * - このファイルが hub/pages/iezukuri/inc/loader.php を読む。
 *
 * 読み込みの流れ:
 * functions.php
 *   ↓
 * inc/loaders/iezukuri-loader.php
 *   ↓
 * hub/pages/iezukuri/inc/loader.php
 *   ↓
 * 家づくり用 helpers / assets / plan / admin など
 *
 * 役割:
 * - 家づくり関連の入口を functions.php から分離する。
 * - CSS/JSの enqueue はここには書かない。
 * - フロントCSS/JSは hub/pages/iezukuri/inc/assets.php に任せる。
 * - 管理画面CSS/JSは hub/pages/iezukuri/admin/enqueue.php に任せる。
 *
 * 注意:
 * - ここは require_once 専用。
 * - wp_enqueue_style() / wp_enqueue_script() は書かない。
 * - 家づくり機能の本体は hub/pages/iezukuri/inc/loader.php 側で管理する。
 * =========================================================
 */

if (!defined('ABSPATH')) {
    exit;
}

$naigai_iezukuri_loader = get_template_directory() . '/hub/pages/iezukuri/inc/loader.php';

if (file_exists($naigai_iezukuri_loader)) {
    require_once $naigai_iezukuri_loader;
}
