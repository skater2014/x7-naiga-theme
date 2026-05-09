<?php
/**
 * =========================================================
 * minpaku/inc/loader.php
 * =========================================================
 *
 * 民泊機能の本体入口。
 *
 * 役割:
 * - 民泊に関係するPHPをここへ集約して読み込む。
 * - functions.php に民泊用コードを直接増やさない。
 *
 * 現在ここで読むもの:
 * - minpaku/init.php
 * - minpaku/inc/assets-b2c-contact.php
 *
 * 重要:
 * - minpaku/init.php は既に民泊本体の入口。
 * - context / enqueue / common-core / booking / checkout / admin などは
 *   minpaku/init.php 側から読み込まれている。
 *
 * 注意:
 * - 一気に全部移動しない。
 * - 1ブロックずつ移し、表示確認しながら進める。
 * =========================================================
 */

if (!defined('ABSPATH')) {
    exit;
}

$naigai_minpaku_init = get_template_directory() . '/minpaku/init.php';
if (file_exists($naigai_minpaku_init)) {
    require_once $naigai_minpaku_init;
}

$naigai_minpaku_b2c_contact_assets = __DIR__ . '/assets-b2c-contact.php';
if (file_exists($naigai_minpaku_b2c_contact_assets)) {
    require_once $naigai_minpaku_b2c_contact_assets;
}
