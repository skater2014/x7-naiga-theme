<?php
/**
 * iez_plan 表示設定メタボックス
 *
 * show in home / archive / detail / card / primary
 * option keys
 * feature catalog list
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('add_meta_boxes', 'naigai_iez_plan_add_display_metabox');
add_action('save_post_iez_plan', 'naigai_iez_plan_save_display_metabox', 20, 2);

function naigai_iez_plan_add_display_metabox() {
    add_meta_box(
        'naigai_iez_plan_display_settings',
        '表示設定・特徴アイコン',
        'naigai_iez_plan_render_display_metabox',
        'iez_plan',
        'side',
        'default'
    );
}

function naigai_iez_plan_render_display_metabox($post) {
    wp_nonce_field('naigai_iez_plan_display_settings', 'naigai_iez_plan_display_settings_nonce');

    $checks = array(
        '_ch_plan_show_in_home'    => 'show in home / 家づくりトップに表示',
        '_ch_plan_show_in_archive' => 'show in archive / 一覧に表示',
        '_ch_plan_show_in_detail'  => 'show in detail / 詳細に特徴を表示',
        '_ch_plan_show_in_cards'   => 'show in cards / 3カード切替対象',
        '_ch_plan_is_card_primary' => 'primary / この分類の代表プラン',
    );

    echo '<p style="margin-top:0;color:#666;">表示先をチェックで制御します。分類だけではフロントに出しません。</p>';

    foreach ($checks as $key => $label) {
        $checked = get_post_meta($post->ID, $key, true) === '1';
        echo '<p><label>';
        echo '<input type="checkbox" name="' . esc_attr($key) . '" value="1" ' . checked($checked, true, false) . '> ';
        echo esc_html($label);
        echo '</label></p>';
    }

    $option_keys = array(
        'deck'       => 'ウッドデッキ',
        'parking'    => '駐車スペース',
        'work_space' => 'ワークスペース',
        'solar'      => '太陽光対応',
        'pet'        => 'ペット対応',
    );

    $saved_options = function_exists('naigai_iez_plan_parse_key_list')
        ? naigai_iez_plan_parse_key_list(get_post_meta($post->ID, '_ch_plan_option_keys', true))
        : array();

    echo '<hr>';
    echo '<p><strong>追加オプション</strong></p>';

    foreach ($option_keys as $key => $label) {
        echo '<p><label>';
        echo '<input type="checkbox" name="_ch_plan_option_keys[]" value="' . esc_attr($key) . '" ' . checked(in_array($key, $saved_options, true), true, false) . '> ';
        echo esc_html($label);
        echo '</label></p>';
    }

    echo '<hr>';
    echo '<p><strong>SVGアイコン一覧</strong></p>';
    echo '<p style="color:#666;">SVGはDBに保存せず、PHP固定カタログから表示します。</p>';

    if (function_exists('naigai_iez_plan_feature_catalog')) {
        echo '<div style="max-height:220px;overflow:auto;border:1px solid #ddd;padding:8px;background:#fff;">';
        foreach (naigai_iez_plan_feature_catalog() as $key => $item) {
            echo '<p style="margin:0 0 6px;"><code>' . esc_html($key) . '</code><br>';
            echo esc_html($item['title']) . ' / icon: <code>' . esc_html($item['icon']) . '</code></p>';
        }
        echo '</div>';
    }
}

function naigai_iez_plan_save_display_metabox($post_id, $post) {
    if (!isset($_POST['naigai_iez_plan_display_settings_nonce'])) {
        return;
    }

    if (!wp_verify_nonce($_POST['naigai_iez_plan_display_settings_nonce'], 'naigai_iez_plan_display_settings')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    $checks = array(
        '_ch_plan_show_in_home',
        '_ch_plan_show_in_archive',
        '_ch_plan_show_in_detail',
        '_ch_plan_show_in_cards',
        '_ch_plan_is_card_primary',
    );

    foreach ($checks as $key) {
        update_post_meta($post_id, $key, isset($_POST[$key]) ? '1' : '0');
    }

    $option_keys = array();

    if (isset($_POST['_ch_plan_option_keys']) && is_array($_POST['_ch_plan_option_keys'])) {
        $option_keys = array_values(array_unique(array_filter(array_map('sanitize_key', $_POST['_ch_plan_option_keys']))));
    }

    update_post_meta($post_id, '_ch_plan_option_keys', implode(',', $option_keys));

    if (get_post_meta($post_id, '_ch_plan_show_in_detail', true) === '') {
        update_post_meta($post_id, '_ch_plan_show_in_detail', '1');
    }
}
