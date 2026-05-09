<?php
/**
 * =========================================================
 * hub/pages/iezukuri/admin/metaboxes/hero-fields.php
 *
 * 役割:
 * - 家づくり共通Heroの入力項目を整理するための補助ファイル。
 *
 * 対象:
 * - /iezukuri/ トップ
 * - 家づくり固定ページ
 * - /iezukuri/plans/ アーカイブ
 *
 * 対象外:
 * - single-iez_plan 詳細ページ
 * - PDF
 *
 * 表示方式:
 * - fade   : 複数画像をフェード切替
 * - swiper : Swiperで画像切替
 * - video  : MP4動画
 *
 * 画像ごとのmotion:
 * - none
 * - zoom-in
 * - zoom-out
 * - pan-left
 * - pan-right
 * - pan-up
 * - pan-down
 *
 * 方針:
 * - HeroのHTML出力は inc/hero-renderer.php に集約。
 * - Heroの見た目は css/common/hero.css に集約。
 * - Heroの動作は js/iezukuri-hero.js に集約。
 * - 固定ページの既存Hero入力は subpage-metabox.php 側を段階的に拡張する。
 * - アーカイブHeroは post meta ではなく option 管理に分ける。
 * =========================================================
 */

if (!defined('ABSPATH')) {
    exit;
}
