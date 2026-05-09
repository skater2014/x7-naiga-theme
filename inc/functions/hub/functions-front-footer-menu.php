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
 * 本番でも自動で footer メニューを作成・割当
 */
add_action('init', function () {
    if (!function_exists('wp_get_nav_menu_object')) {
        return;
    }

    $locations = get_theme_mod('nav_menu_locations', array());

    $menus = array(
        'front_footer_property' => array(
            'name'  => 'フロントフッター：不動産を探す',
            'items' => array(
                array('title' => '物件一覧', 'url' => naigai_front_footer_seed_url('nasu-jutaku')),
                array('title' => '土地を探す', 'url' => naigai_front_footer_seed_url('tochi')),
                array('title' => '中古別荘を探す', 'url' => naigai_front_footer_seed_url('used')),
                array('title' => '売却・査定について', 'url' => naigai_front_footer_seed_url('satei-reservation')),
            ),
        ),
        'front_footer_construction' => array(
            'name'  => 'フロントフッター：那須の住宅・別荘',
            'items' => array(
                array('title' => '注文住宅を見る', 'url' => naigai_front_footer_seed_url('construction-hub')),
                array('title' => '施工事例', 'url' => naigai_front_footer_seed_url('sekoujirei')),
                array('title' => '那須の住宅について', 'url' => naigai_front_footer_seed_url('construction-hub/concept')),
                array('title' => 'お客様の声', 'url' => naigai_front_footer_seed_url('voice')),
            ),
        ),
        'front_footer_house' => array(
            'name'  => 'フロントフッター：家づくり',
            'items' => array(
                array('title' => 'コンセプト', 'url' => naigai_front_footer_seed_url('construction-hub/concept')),
                array('title' => 'デザインポリシー', 'url' => naigai_front_footer_seed_url('construction-hub/design-policy')),
                array('title' => '那須の家づくり', 'url' => naigai_front_footer_seed_url('construction-hub/nasu-house')),
                array('title' => 'モデルハウス', 'url' => naigai_front_footer_seed_url('model-house')),
            ),
        ),
        'front_footer_minpaku' => array(
            'name'  => 'フロントフッター：民泊・貸別荘',
            'items' => array(
                array('title' => '民泊運営サポート', 'url' => naigai_front_footer_seed_url('minpaku-support')),
                array('title' => '運営事例', 'url' => naigai_front_footer_seed_url('minpaku')),
                array('title' => 'よくあるご質問', 'url' => naigai_front_footer_seed_url('minpaku-guide')),
            ),
        ),
        'front_footer_company' => array(
            'name'  => 'フロントフッター：会社案内',
            'items' => array(
                array('title' => '会社概要', 'url' => naigai_front_footer_seed_url('company')),
                array('title' => 'スタッフ紹介', 'url' => naigai_front_footer_seed_url('staff')),
                array('title' => 'アクセス', 'url' => naigai_front_footer_seed_url('access')),
                array('title' => '採用情報', 'url' => naigai_front_footer_seed_url('recruitment')),
            ),
        ),
    );

    foreach ($menus as $location => $menu_data) {
        $menu = wp_get_nav_menu_object($menu_data['name']);

        if (!$menu) {
            $menu_id = wp_create_nav_menu($menu_data['name']);
        } else {
            $menu_id = (int) $menu->term_id;
        }

        if ($menu_id <= 0) {
            continue;
        }

        naigai_front_footer_seed_menu_items($menu_id, $menu_data['items']);

        if (empty($locations[$location])) {
            $locations[$location] = $menu_id;
        }
    }

    set_theme_mod('nav_menu_locations', $locations);
}, 30);
