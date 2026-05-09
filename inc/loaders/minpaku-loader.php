<?php
/**
 * =========================================================
 * inc/loaders/minpaku-loader.php
 * =========================================================
 *
 * 民泊機能の読み込み入口。
 *
 * 役割:
 * - functions.php を大きくしすぎないための中継ファイル。
 * - functions.php からこのファイルを1回だけ読む。
 * - このファイルが minpaku/inc/loader.php を読む。
 *
 * 読み込みの流れ:
 * functions.php
 *   ↓
 * inc/loaders/minpaku-loader.php
 *   ↓
 * minpaku/inc/loader.php
 *
 * 注意:
 * - ここには wp_enqueue_style() / wp_enqueue_script() を直接書かない。
 * - 民泊の本体処理は minpaku/inc/loader.php 側に集約する。
 * =========================================================
 */

if (!defined('ABSPATH')) {
    exit;
}

$naigai_minpaku_loader = get_template_directory() . '/minpaku/inc/loader.php';

if (file_exists($naigai_minpaku_loader)) {
    require_once $naigai_minpaku_loader;
}
