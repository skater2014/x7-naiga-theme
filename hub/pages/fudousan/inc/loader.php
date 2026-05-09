<?php
/**
 * =========================================================
 * hub/pages/fudousan/inc/loader.php
 * =========================================================
 *
 * 不動産機能の本体入口。
 *
 * 役割:
 * - /fudousan/ に関係するPHPをここへ集約して読み込む。
 * - functions.php に不動産用コードを直接増やさない。
 *
 * 今後ここへ移す候補:
 * - /fudousan/ 用 helpers
 * - /fudousan/ 用 assets
 * - template-realestate-hub.php 関連補助関数
 * - front-map-modal.js の読み込み整理
 *
 * 注意:
 * - 今日は受け皿だけ作る。
 * - 実コード移動は grep で対象を確認してから行う。
 * =========================================================
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * 01. /fudousan 専用 CSS / JS
 *
 * 役割:
 * - /fudousan の見た目と動きに必要な assets.php を読む。
 * - functions.php へ直接 enqueue を増やさない。
 */
$naigai_fudousan_assets = __DIR__ . '/assets.php';
if (file_exists($naigai_fudousan_assets)) {
    require_once $naigai_fudousan_assets;
}

