<?php
/**
 * =========================================================
 * Front Footer Menu Seeder
 * =========================================================
 *
 * 役割:
 * - ローカル / 本番どちらでも footer 用メニューを自動作成する
 * - 既にメニューがある場合は壊さない
 * - ページが存在するものだけメニュー項目に入れる
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * footer 用メニュー位置を登録
 */
add_action('after_setup_theme', function () {
    register_nav_menus(array(
        'front_footer_property'     => 'フロントフッター：不動産を探す',
        'front_footer_construction' => 'フロントフッター：那須の住宅・別荘',
        'front_footer_house'        => 'フロントフッター：家づくり',
        'front_footer_minpaku'      => 'フロントフッター：民泊・貸別荘',
        'front_footer_company'      => 'フロントフッター：会社案内',
    ));
});

/**
 * slug から URL を解決
 */
function naigai_front_footer_seed_url($slug, $fallback = '') {
    $slug = trim((string) $slug, '/');

    if ($slug !== '') {
        $page = get_page_by_path($slug);
        if ($page && !is_wp_error($page)) {
            return get_permalink($page->ID);
        }
    }

    return home_url('/' . trim($fallback ?: $slug, '/') . '/');
}

/**
 * メニュー項目を存在チェックして追加
 */
function naigai_front_footer_seed_menu_items($menu_id, array $items) {
    $existing = wp_get_nav_menu_items($menu_id);
    $existing_titles = array();

    if (!empty($existing)) {
        foreach ($existing as $item) {
            $existing_titles[] = $item->title;
        }
    }

    foreach ($items as $item) {
        $title = isset($item['title']) ? $item['title'] : '';
        $url   = isset($item['url']) ? $item['url'] : '';

        if ($title === '' || $url === '') {
            continue;
        }

        if (in_array($title, $existing_titles, true)) {
            continue;
        }

        wp_update_nav_menu_item($menu_id, 0, array(
            'menu-item-title'  => $title,
            'menu-item-url'    => $url,
            'menu-item-status' => 'publish',
            'menu-item-type'   => 'custom',
        ));
    }
}

/**
 * =========================================================
 * 自動メニュー作成・自動ロケーション割当は使用しない
 * =========================================================
 *
 * WordPress標準の「外観 → メニュー → 位置を管理」で管理する。
 *
 * ここから wp_create_nav_menu() / wp_update_nav_menu_item() /
 * set_theme_mod('nav_menu_locations', ...) を実行しない。
 *
 * テーマ切替時もWordPress標準のtheme_modsに任せる。
 */
