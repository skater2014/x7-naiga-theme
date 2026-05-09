<?php
/**
 * =========================================================
 * inc/loaders/hub-loader.php
 * =========================================================
 *
 * Hub機能の読み込み入口。
 *
 * 役割:
 * - functions.php を大きくしすぎないための中継ファイル。
 * - functions.php からこのファイルを1回だけ読む。
 * - このファイルが hub/init.php を読む。
 *
 * 読み込みの流れ:
 * functions.php
 *   ↓
 * inc/loaders/hub-loader.php
 *   ↓
 * hub/init.php
 *   ↓
 * hub/inc/common.php
 * hub/inc/context.php
 * hub/inc/admin.php
 * hub/inc/assets.php
 * hub/pages/*
 *
 * 注意:
 * - ここには wp_enqueue_style() / wp_enqueue_script() を直接書かない。
 * - Hub本体の処理は hub/init.php 側に集約する。
 * - functions.php から hub/init.php を直接読ませない。
 * =========================================================
 */

if (!defined('ABSPATH')) {
    exit;
}

$naigai_hub_loader = get_template_directory() . '/hub/init.php';

if (file_exists($naigai_hub_loader)) {
    require_once $naigai_hub_loader;
}
