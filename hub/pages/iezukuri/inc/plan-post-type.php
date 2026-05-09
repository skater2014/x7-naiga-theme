<?php
/**
 * 家づくり 参考プラン CPT / Taxonomy
 *
 * 一覧:
 * /iezukuri/plans/
 *
 * 詳細:
 * /iezukuri/plan/{slug}/
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('init', function () {
    register_post_type('iez_plan', array(
        'labels' => array(
            'name'               => '家づくりプラン',
            'singular_name'      => '家づくりプラン',
            'add_new'            => '新規追加',
            'add_new_item'       => '家づくりプランを追加',
            'edit_item'          => '家づくりプランを編集',
            'new_item'           => '新しい家づくりプラン',
            'view_item'          => '家づくりプランを見る',
            'search_items'       => '家づくりプランを検索',
            'not_found'          => '家づくりプランはありません',
            'menu_name'          => '家づくりプラン',
        ),
        'public'        => true,
        'show_ui'       => true,
        'show_in_menu'  => true,
        'menu_position' => 21,
        'menu_icon'     => 'dashicons-admin-home',
        'supports'      => array('title', 'editor', 'thumbnail', 'excerpt', 'page-attributes'),
        'has_archive'   => false,
        'rewrite'       => array(
            'slug'       => 'iezukuri/plan',
            'with_front' => false,
        ),
        'show_in_rest'  => true,
    ));

    register_taxonomy('iez_plan_type', array('iez_plan'), array(
        'labels' => array(
            'name'          => '住宅タイプ',
            'singular_name' => '住宅タイプ',
            'menu_name'     => '住宅タイプ',
        ),
        'public'            => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        'hierarchical'      => true,
        'rewrite'           => array(
            'slug'       => 'iezukuri/plan-type',
            'with_front' => false,
        ),
        'show_in_rest'      => true,
    ));

    register_taxonomy('iez_plan_size', array('iez_plan'), array(
        'labels' => array(
            'name'          => '坪数・面積帯',
            'singular_name' => '坪数・面積帯',
            'menu_name'     => '坪数・面積帯',
        ),
        'public'            => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        'hierarchical'      => true,
        'rewrite'           => array(
            'slug'       => 'iezukuri/plan-size',
            'with_front' => false,
        ),
        'show_in_rest'      => true,
    ));

    register_taxonomy('iez_plan_feature', array('iez_plan'), array(
        'labels' => array(
            'name'          => '特徴タグ',
            'singular_name' => '特徴タグ',
            'menu_name'     => '特徴タグ',
        ),
        'public'            => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        'hierarchical'      => false,
        'rewrite'           => array(
            'slug'       => 'iezukuri/plan-feature',
            'with_front' => false,
        ),
        'show_in_rest'      => true,
    ));
});
