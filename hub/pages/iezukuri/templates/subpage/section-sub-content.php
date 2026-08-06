<?php
/**
 * ============================================================
 * 家づくりサブページ
 * 共通「サブコンテンツ（ページ本文）」
 * ============================================================
 *
 * 管理画面の
 *
 *     サブコンテンツ（ページ本文）
 *
 * と1対1で対応する共通HTMLパーツ。
 *
 *
 * ============================================================
 * 使用する正式メタキー
 * ============================================================
 *
 * 導入:
 *
 *     _ch_intro_kicker
 *     _ch_intro_title
 *     _ch_intro_text
 *
 * 本文:
 *
 *     _ch_body_title
 *     _ch_body_text
 *     _ch_body_image_id
 *     _ch_gallery_ids
 *
 *
 * ============================================================
 * 他の共通メタボックスとは完全に別
 * ============================================================
 *
 * Hero:
 *
 *     _ch_hero_*
 *     → section-sub-hero.php
 *
 * サブコンテンツ:
 *
 *     _ch_intro_*
 *     _ch_body_*
 *     → この section-sub-content.php
 *
 * Footer直前 サブCTA:
 *
 *     _ch_sub_cta_*
 *     → section-sub-cta.php
 *
 *
 * ============================================================
 * HTML上の位置
 * ============================================================
 *
 * Hero
 * ↓
 * このサブコンテンツ
 * ↓
 * 中間ナビ / ページ固有コンテンツ
 * ↓
 * Footer直前CTA
 *
 *
 * Heroの<section>内部には入れない。
 * Heroとは兄弟関係の独立sectionとして表示する。
 * ============================================================
 */

if (!defined('ABSPATH')) {
    exit;
}

$page_id = get_queried_object_id();

if (!$page_id) {
    $page_id = get_the_ID();
}


/*
 * ============================================================
 * renderer読込
 * ============================================================
 *
 * rendererはHTML本文を生成する処理。
 * このパーツが実際の呼出窓口になる。
 */

$renderer_file =
    get_template_directory()
    . '/hub/pages/iezukuri/inc/page-content-renderer.php';

if (
    !function_exists(
        'naigai_iezukuri_render_page_content_from_meta'
    )
    && is_readable($renderer_file)
) {
    require_once $renderer_file;
}


/*
 * ============================================================
 * 現在ページのメタから本文を出力
 * ============================================================
 *
 * 全項目空ならrenderer側で何も出力しない。
 */

if (
    $page_id
    && function_exists(
        'naigai_iezukuri_render_page_content_from_meta'
    )
) {
    naigai_iezukuri_render_page_content_from_meta(
        $page_id
    );
}
