<?php
/**
 * iez_plan 追加タクソノミー
 *
 * 目的:
 * - 新築 / リフォームを iez_plan_type に混ぜない
 * - 平屋 / 2階建てを iez_plan_type に混ぜない
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('init', 'naigai_iez_plan_register_extra_taxonomies', 20);

function naigai_iez_plan_register_extra_taxonomies() {
    register_taxonomy('iez_plan_scope', array('iez_plan'), array(
        'labels' => array(
            'name'          => '工事区分',
            'singular_name' => '工事区分',
            'search_items'  => '工事区分を検索',
            'all_items'     => 'すべての工事区分',
            'edit_item'     => '工事区分を編集',
            'update_item'   => '工事区分を更新',
            'add_new_item'  => '工事区分を追加',
            'new_item_name' => '新しい工事区分',
            'menu_name'     => '工事区分',
        ),
        'public'            => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        'hierarchical'      => false,
        'rewrite'           => array('slug' => 'iez-plan-scope'),
        'show_in_rest'      => true,
    ));

    register_taxonomy('iez_plan_building_form', array('iez_plan'), array(
        'labels' => array(
            'name'          => '建物形状',
            'singular_name' => '建物形状',
            'search_items'  => '建物形状を検索',
            'all_items'     => 'すべての建物形状',
            'edit_item'     => '建物形状を編集',
            'update_item'   => '建物形状を更新',
            'add_new_item'  => '建物形状を追加',
            'new_item_name' => '新しい建物形状',
            'menu_name'     => '建物形状',
        ),
        'public'            => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        'hierarchical'      => false,
        'rewrite'           => array('slug' => 'iez-plan-building-form'),
        'show_in_rest'      => true,
    ));

    naigai_iez_plan_ensure_extra_terms();
}

function naigai_iez_plan_ensure_extra_terms() {
    $terms = array(
        'iez_plan_scope' => array(
            'new-build'  => '新築',
            'renovation' => 'リフォーム',
        ),
        'iez_plan_building_form' => array(
            'hiraya'    => '平屋',
            'two-story' => '2階建て',
        ),
    );

    foreach ($terms as $taxonomy => $items) {
        foreach ($items as $slug => $name) {
            if (!term_exists($slug, $taxonomy)) {
                wp_insert_term($name, $taxonomy, array('slug' => $slug));
            }
        }
    }
}
