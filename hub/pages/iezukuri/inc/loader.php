<?php
/**
 * =========================================================
 * hub/pages/iezukuri/inc/loader.php
 * =========================================================
 *
 * 家づくり機能の「入口」ファイル。
 *
 * 初心者向けに言うと:
 * - functions.php から最初に呼ばれる、家づくり専用の玄関。
 * - このファイル自身は画面を作らない。
 * - CSS/JSも直接読み込まない。
 * - helpers / plan / assets / admin など、必要な部品ファイルを順番に require_once する。
 *
 * functions.php との関係:
 * - functions.php
 *   ↓
 * - hub/pages/iezukuri/inc/loader.php
 *   ↓
 * - inc/helpers.php
 * - inc/assets.php
 * - inc/plan-home-switch-data.php
 * - admin/loader.php など
 *
 * フロント側CSS/JS:
 * - hub/pages/iezukuri/inc/assets.php に集約する。
 * - loader.php の中に wp_enqueue_style() / wp_enqueue_script() を増やさない。
 *
 * 管理画面側CSS/JS:
 * - hub/pages/iezukuri/admin/enqueue.php に集約する。
 *
 * 今回の3カード切替:
 * - この loader.php は、assets.php と plan-home-switch-data.php を読み込むだけ。
 * - 実際にCSS/JSを出すのは inc/assets.php。
 * - 実際にJSONデータを作るのは inc/plan-home-switch-data.php。
 *
 * 注意:
 * - 下部にあった naigai-iezukuri-plan-detail-switch の重複 enqueue は停止済み。
 * - 同じCSS/JSを loader.php と assets.php の両方で読むと二重読み込みになる。
 * - A方式では assets.php 側の window.NAIGAI_IEZ_PLAN_HOME が必要なので assets.php を正とする。
 * =========================================================
 */

if (!defined('ABSPATH')) {
    exit;
}
if (!function_exists('naigai_iezukuri_require_once')) {
    function naigai_iezukuri_require_once($relative_path) {
        $file = get_template_directory() . '/hub/pages/iezukuri/' . ltrim($relative_path, '/');
        if (file_exists($file)) {
            require_once $file;
        }
    }
}
/**
 * 01. 共通関数
 */
naigai_iezukuri_require_once('inc/helpers.php');
naigai_iezukuri_require_once('inc/icon-catalog.php');
naigai_iezukuri_require_once('inc/footer-fixed-cta.php');
/**
 * 02. 家づくり専用 functions
 * CSS / JS 読み込みはここ。
 */
naigai_iezukuri_require_once('inc/functions-iezukuri.php');
/**
 * 03. 表示系
 */
naigai_iezukuri_require_once('inc/block-renderer.php');
naigai_iezukuri_require_once('inc/hero-renderer.php');
/**
 * 04. プラン系
 */
naigai_iezukuri_require_once('inc/plan-post-type.php');
naigai_iezukuri_require_once('inc/plan-archive.php');
naigai_iezukuri_require_once('inc/plan-pdf-template.php');
/**
 * 05. 管理画面系
 */
if (is_admin()) {
}
/* === IEZUKURI PLAN DETAIL SWITCH START === */
/**
 * /iezukuri/ トップの3カード下に、詳細表示用JS/CSSを読み込む。
 * 既存テンプレート全体は触らない。
 */
if (!function_exists('naigai_iezukuri_plan_detail_switch_assets')) {
    function naigai_iezukuri_plan_detail_switch_assets_legacy_disabled() {
        if (!is_page('iezukuri')) {
            return;
        }
        $theme_dir = get_template_directory();
        $theme_uri = get_template_directory_uri();
        $css_rel = '/hub/pages/iezukuri/css/iezukuri-plan-detail-switch.css';
        $js_rel  = '/hub/pages/iezukuri/js/iezukuri-plan-detail-switch.js';
        // Disabled: duplicate old plan switch CSS. New asset loader is inc/assets.php.
        // wp_enqueue_style('naigai-iezukuri-plan-detail-switch', ...);
        // Disabled: duplicate old plan switch JS. New dynamic loader is plan-home-switch-data.php.
        // wp_enqueue_script('naigai-iezukuri-plan-detail-switch', ...);
    }
}
// Disabled: old asset action. CSS/JS enqueue is centralized in inc/assets.php.
// add_action('wp_enqueue_scripts', 'naigai_iezukuri_plan_detail_switch_assets', 80);
/* === IEZUKURI PLAN DETAIL SWITCH END === */
require_once __DIR__ . '/plan-extra-taxonomies.php';
require_once __DIR__ . '/plan-feature-catalog.php';
require_once __DIR__ . '/plan-display-metabox.php';
require_once __DIR__ . '/plan-home-switch-data.php';
require_once __DIR__ . '/assets.php';
require_once __DIR__ . '/lightgallery-enqueue.php';
/* IEZ_ADMIN_LOADER_REQUIRE_START */
/**
 * 管理画面側入口
 *
 * inc/loader.php は家づくり全体の入口。
 * 管理画面専用のメタボックス / admin CSS / admin JS は admin/loader.php に集約する。
 */
if (is_admin()) {
    naigai_iezukuri_require_once('admin/loader.php');
}
/* IEZ_ADMIN_LOADER_REQUIRE_END */

/**
 * 固定ページ本文レンダラー
 * - _ch_page_hero_json
 * - _ch_page_sections_json
 * - _ch_page_cta_json
 */
$naigai_iezukuri_page_content_renderer = __DIR__ . '/page-content-renderer.php';
if (file_exists($naigai_iezukuri_page_content_renderer)) {
    require_once $naigai_iezukuri_page_content_renderer;
}
require_once __DIR__ . '/plan-seo.php';

// 共通レイアウトCSS: /iezukuri/ と /iezukuri/* の .ch-shell 幅を一元管理
$naigai_iezukuri_layout_enqueue = __DIR__ . '/layout-enqueue.php';
if (file_exists($naigai_iezukuri_layout_enqueue)) {
    require_once $naigai_iezukuri_layout_enqueue;
}

// SEO: 家づくりページのtitle/meta/JSON-LD
require_once __DIR__ . '/seo.php';

/*
 * disabled:
 * - /iezukuri/ トップ 3カード下の詳細表示CSS/JSは inc/assets.php 側で読み込む。
 * - loader.php 側でも同じ iezukuri-plan-detail-switch.css/js を読むと二重読み込みになるため停止。
 * - A方式では assets.php 側の window.NAIGAI_IEZ_PLAN_HOME が必要なので、assets.php 側を正とする。
 */


// 家づくりイントロ
$naigai_iez_intro_overlay = __DIR__ . '/intro-overlay.php';
if (file_exists($naigai_iez_intro_overlay)) {
    require_once $naigai_iez_intro_overlay;
}

// 家づくりプランの構造・工法タクソノミー。
// 木造 / 在来工法 / 2×4 / 2×6 / 鉄骨造 / RC造 などを住宅タイプと分けて管理する。
$naigai_iezukuri_plan_structure_taxonomy = __DIR__ . '/plan-structure-taxonomy.php';
if (file_exists($naigai_iezukuri_plan_structure_taxonomy)) {
    require_once $naigai_iezukuri_plan_structure_taxonomy;
}

// iez_plan 編集画面のPDF生成ボタン。
// 既存の pdf-service /generate を呼び出し、A4 PDFを生成する。
$naigai_iezukuri_plan_pdf_generator_metabox = dirname(__DIR__) . '/admin/metaboxes/plan-pdf-generator-metabox.php';
if (file_exists($naigai_iezukuri_plan_pdf_generator_metabox)) {
    require_once $naigai_iezukuri_plan_pdf_generator_metabox;
}

// iez_plan 一覧画面のPDF一括生成。
// 個別編集画面のPDF生成ボタンとは別に、一覧で選択生成・全件生成を行う。
$naigai_iezukuri_plan_pdf_bulk_actions = dirname(__DIR__) . '/admin/plan-pdf-bulk-actions.php';
if (is_admin() && file_exists($naigai_iezukuri_plan_pdf_bulk_actions)) {
    require_once $naigai_iezukuri_plan_pdf_bulk_actions;
}
