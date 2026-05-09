<?php
/**
 * 家づくりプラン: 構造・工法タクソノミー
 *
 * 役割:
 * - 住宅タイプとは分けて、建物の構造・工法を管理する。
 * - 例: 木造 / 在来工法 / 2×4工法 / 2×6工法 / 鉄骨造 / RC造
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('init', function () {
    register_taxonomy(
        'iez_plan_structure',
        array('iez_plan'),
        array(
            'labels' => array(
                'name'              => '構造・工法',
                'singular_name'     => '構造・工法',
                'search_items'      => '構造・工法を検索',
                'all_items'         => 'すべての構造・工法',
                'edit_item'         => '構造・工法を編集',
                'update_item'       => '構造・工法を更新',
                'add_new_item'      => '新しい構造・工法を追加',
                'new_item_name'     => '新しい構造・工法名',
                'menu_name'         => '構造・工法',
            ),
            'public'            => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'show_in_rest'      => true,
            'hierarchical'      => true,
            'rewrite'           => array(
                'slug'       => 'iezukuri/structure',
                'with_front' => false,
            ),
        )
    );
});
