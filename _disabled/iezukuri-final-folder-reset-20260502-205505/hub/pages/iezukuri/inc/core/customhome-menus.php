<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('naigai_customhome_find_page_url')) {
    function naigai_customhome_find_page_url($slugs, $fallback = '/')
    {
        foreach ((array) $slugs as $slug) {
            $page = get_page_by_path($slug);
            if ($page) {
                return get_permalink($page->ID);
            }
        }
        return home_url($fallback);
    }
}

if (!function_exists('naigai_customhome_register_nav_locations')) {
    function naigai_customhome_register_nav_locations()
    {
        register_nav_menus(array(
            'customhome-header-menu' => 'Custom Home Header Menu',
            'customhome-page-menu'   => 'Custom Home Page Menu',
        ));
    }
    add_action('after_setup_theme', 'naigai_customhome_register_nav_locations');
}

if (!function_exists('naigai_customhome_ensure_menu')) {
    function naigai_customhome_ensure_menu($menu_name, $location, array $items)
    {
        $menu = wp_get_nav_menu_object($menu_name);

        if (!$menu) {
            $menu_id = wp_create_nav_menu($menu_name);
            $menu    = wp_get_nav_menu_object($menu_id);
        }

        if (!$menu || empty($menu->term_id)) {
            return;
        }

        $menu_id        = (int) $menu->term_id;
        $existing_items = wp_get_nav_menu_items($menu_id);

        if (empty($existing_items)) {
            foreach ($items as $index => $item) {
                wp_update_nav_menu_item($menu_id, 0, array(
                    'menu-item-title'    => $item['title'],
                    'menu-item-url'      => $item['url'],
                    'menu-item-status'   => 'publish',
                    'menu-item-type'     => 'custom',
                    'menu-item-object'   => 'custom',
                    'menu-item-position' => $index + 1,
                ));
            }
        }

        $locations = get_theme_mod('nav_menu_locations', array());
        if (!is_array($locations)) {
            $locations = array();
        }

        if (empty($locations[$location]) || (int) $locations[$location] !== $menu_id) {
            $locations[$location] = $menu_id;
            set_theme_mod('nav_menu_locations', $locations);
        }
    }
}

if (!function_exists('naigai_customhome_seed_nav_menus')) {
    function naigai_customhome_seed_nav_menus()
    {
        $room_url     = naigai_customhome_find_page_url(array('room-gallery', 'room-gallary'), '/room-gallery/');
        $hokubei_url  = naigai_customhome_find_page_url(array('hokubei-jutaku'), '/hokubei-jutaku/');
        $natural_url  = naigai_customhome_find_page_url(array('zairai-kouhou'), '/zairai-kouhou/');
        $ideal_url    = naigai_customhome_find_page_url(array('nasu-ideal-home'), '/nasu-ideal-home/');
        $contact_url  = naigai_customhome_find_page_url(array('contact', 'customer-info-form'), '/contact/');

        naigai_customhome_ensure_menu(
            '注文住宅ヘッダーメニュー',
            'customhome-header-menu',
            array(
                array('title' => 'お部屋ギャラリー',     'url' => $room_url),
                array('title' => '北米住宅の住まい',     'url' => $hokubei_url),
                array('title' => '自然素材の住まい',     'url' => $natural_url),
                array('title' => '理想の住まいを考える', 'url' => $ideal_url),
                array('title' => 'お問い合わせ',         'url' => $contact_url),
            )
        );

        naigai_customhome_ensure_menu(
            '注文住宅ページ内メニュー',
            'customhome-page-menu',
            array(
                array('title' => '家づくりの特徴', 'url' => '#customhome-feature'),
                array('title' => '施工事例',       'url' => '#customhome-works'),
                array('title' => '家づくりの流れ', 'url' => '#customhome-flow'),
                array('title' => 'お問い合わせ',   'url' => '#customhome-contact'),
            )
        );
    }
    add_action('init', 'naigai_customhome_seed_nav_menus', 40);
}
