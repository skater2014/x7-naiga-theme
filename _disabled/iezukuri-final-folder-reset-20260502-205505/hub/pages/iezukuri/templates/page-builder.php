<?php
/**
 * =========================================================
 * Builder layout
 * =========================================================
 * 注文住宅サブページ構成ビルダーの順番通りに表示する。
 */

if (!defined('ABSPATH')) {
    exit;
}

$post_id = get_queried_object_id();

if (function_exists('naigai_ch_render_builder_sections')) {
    naigai_ch_render_builder_sections($post_id);
}
