<?php
/**
 * =========================================================
 * inc/loaders/fudousan-loader.php
 * =========================================================
 *
 * 不動産機能の読み込み入口。
 *
 * 役割:
 * - functions.php を大きくしすぎないための中継ファイル。
 * - functions.php からこのファイルを1回だけ読む。
 * - このファイルが hub/pages/fudousan/inc/loader.php を読む。
 *
 * 注意:
 * - ここには wp_enqueue_style() / wp_enqueue_script() を直接書かない。
 * - 不動産の本体処理は hub/pages/fudousan/inc/loader.php 側に集約する。
 * =========================================================
 */

if (!defined('ABSPATH')) {
    exit;
}

$naigai_fudousan_loader = get_template_directory() . '/hub/pages/fudousan/inc/loader.php';

if (file_exists($naigai_fudousan_loader)) {
    require_once $naigai_fudousan_loader;
}
