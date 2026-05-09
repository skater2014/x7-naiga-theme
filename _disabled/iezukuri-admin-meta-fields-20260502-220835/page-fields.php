<?php
/**
 * =========================================================
 * iezukuri/inc/admin/page-fields.php
 *
 * 役割:
 * - 管理メニュー / 固定ページ編集画面で共通利用する
 * - 固定ページ本体、サムネイル、Hero、既存メタキーをまとめて編集
 * =========================================================
 */

if (!defined('ABSPATH')) {
    exit;
}

function naigai_iez_admin_allowed_meta_key($key)
{
    if (!is_string($key) || $key === '') {
        return false;
    }

    if (in_array($key, array('_thumbnail_id', '_wp_page_template'), true)) {
        return false;
    }

    if (strpos($key, '_iez_') === 0) {
        return true;
    }

    if (strpos($key, '_ch_') === 0) {
        return true;
    }

    if (strpos($key, '_hub_') === 0) {
        return true;
    }

    return false;
}

function naigai_iez_admin_meta_value_to_string($value)
{
    $value = maybe_unserialize($value);

    if (is_array($value) || is_object($value)) {
        return wp_json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }

    return (string) $value;
}

function naigai_iez_admin_render_page_core_fields($post_id)
{
    $post = get_post($post_id);

    if (!$post) {
        return;
    }

    $thumb_id = get_post_thumbnail_id($post_id);
    $template = get_page_template_slug($post_id);
    if ($template === '') {
        $template = 'default';
    }

    echo '<h2>固定ページ 本体設定</h2>';
    echo '<input type="hidden" name="iez_admin_edit_core_fields" value="1">';

    echo '<table class="form-table" role="presentation"><tbody>';

    echo '<tr><th>タイトル</th><td>';
    echo '<input type="text" class="large-text" name="iez_post_title" value="' . esc_attr($post->post_title) . '">';
    echo '</td></tr>';

    echo '<tr><th>スラッグ</th><td>';
    echo '<input type="text" class="regular-text" name="iez_post_name" value="' . esc_attr($post->post_name) . '">';
    echo '<p class="description">URLの最後の部分。例: nasu-house</p>';
    echo '</td></tr>';

    echo '<tr><th>本文</th><td>';
    echo '<textarea class="large-text code" rows="12" name="iez_post_content">' . esc_textarea($post->post_content) . '</textarea>';
    echo '<p class="description">固定ページ本文。Gutenberg側の本文と同じ post_content に保存します。</p>';
    echo '</td></tr>';

    echo '<tr><th>抜粋</th><td>';
    echo '<textarea class="large-text" rows="4" name="iez_post_excerpt">' . esc_textarea($post->post_excerpt) . '</textarea>';
    echo '</td></tr>';

    echo '<tr><th>アイキャッチ / サムネイル</th><td>';
    echo '<input type="number" id="iez_featured_image_id" name="iez_featured_image_id" value="' . esc_attr($thumb_id) . '"> ';
    echo '<button class="button js-iez-pick-image" data-target="#iez_featured_image_id" data-preview="#iez_featured_image_preview">画像を選択</button> ';
    echo '<button class="button js-iez-clear" data-target="#iez_featured_image_id" data-preview="#iez_featured_image_preview">クリア</button>';
    echo '<div id="iez_featured_image_preview" style="margin-top:10px;">';
    if ($thumb_id) {
        echo wp_get_attachment_image($thumb_id, 'medium');
    }
    echo '</div>';
    echo '</td></tr>';

    echo '<tr><th>ページテンプレート</th><td>';
    echo '<select name="iez_page_template">';
    $templates = wp_get_theme()->get_page_templates(null, 'page');
    echo '<option value="default"' . selected($template, 'default', false) . '>デフォルト</option>';
    foreach ($templates as $file => $label) {
        echo '<option value="' . esc_attr($file) . '"' . selected($template, $file, false) . '>' . esc_html($label . ' / ' . $file) . '</option>';
    }
    echo '</select>';
    echo '</td></tr>';

    echo '</tbody></table>';
}

function naigai_iez_admin_render_existing_meta_fields($post_id)
{
    $all = get_post_meta($post_id);
    $keys = array();

    foreach ($all as $key => $values) {
        if (naigai_iez_admin_allowed_meta_key($key)) {
            $keys[] = $key;
        }
    }

    sort($keys);

    echo '<h2>既存メタキー</h2>';
    echo '<p class="description">このページに保存済みの _iez_* / _ch_* / _hub_* を編集します。空で保存すると削除します。</p>';

    echo '<table class="widefat striped" style="max-width:1200px;">';
    echo '<thead><tr><th style="width:280px;">メタキー</th><th>値</th></tr></thead><tbody>';

    if (empty($keys)) {
        echo '<tr><td colspan="2">対象メタキーなし</td></tr>';
    }

    foreach ($keys as $key) {
        $value = isset($all[$key][0]) ? naigai_iez_admin_meta_value_to_string($all[$key][0]) : '';

        echo '<tr>';
        echo '<td><code>' . esc_html($key) . '</code></td>';
        echo '<td><textarea class="large-text code" rows="3" name="iez_existing_meta[' . esc_attr($key) . ']">' . esc_textarea($value) . '</textarea></td>';
        echo '</tr>';
    }

    echo '</tbody></table>';

    echo '<h3>メタキー追加</h3>';
    echo '<table class="form-table" role="presentation"><tbody>';
    echo '<tr><th>追加キー1</th><td><input type="text" class="regular-text" name="iez_new_meta_key_1" placeholder="_ch_xxx"> <textarea class="large-text code" rows="2" name="iez_new_meta_value_1" placeholder="値"></textarea></td></tr>';
    echo '<tr><th>追加キー2</th><td><input type="text" class="regular-text" name="iez_new_meta_key_2" placeholder="_iez_xxx"> <textarea class="large-text code" rows="2" name="iez_new_meta_value_2" placeholder="値"></textarea></td></tr>';
    echo '</tbody></table>';
}

function naigai_iez_admin_render_all_fields($post_id)
{
    naigai_iez_admin_render_page_core_fields($post_id);

    echo '<hr>';
    echo '<h2>Hero設定</h2>';

    if (function_exists('naigai_iez_admin_render_hero_fields')) {
        naigai_iez_admin_render_hero_fields($post_id);
    } else {
        echo '<div class="notice notice-error"><p>hero-fields.php が読み込まれていません。</p></div>';
    }

    echo '<hr>';
    naigai_iez_admin_render_existing_meta_fields($post_id);
}

function naigai_iez_admin_save_page_core_fields($post_id)
{
    if (!isset($_POST['iez_admin_edit_core_fields'])) {
        return;
    }

    static $updating = false;

    if ($updating) {
        return;
    }

    $postarr = array(
        'ID' => $post_id,
    );

    if (isset($_POST['iez_post_title'])) {
        $postarr['post_title'] = sanitize_text_field(wp_unslash($_POST['iez_post_title']));
    }

    if (isset($_POST['iez_post_name'])) {
        $postarr['post_name'] = sanitize_title(wp_unslash($_POST['iez_post_name']));
    }

    if (isset($_POST['iez_post_content'])) {
        $postarr['post_content'] = wp_kses_post(wp_unslash($_POST['iez_post_content']));
    }

    if (isset($_POST['iez_post_excerpt'])) {
        $postarr['post_excerpt'] = sanitize_textarea_field(wp_unslash($_POST['iez_post_excerpt']));
    }

    if (count($postarr) > 1) {
        $updating = true;
        wp_update_post($postarr);
        $updating = false;
    }

    $thumb_id = isset($_POST['iez_featured_image_id']) ? absint($_POST['iez_featured_image_id']) : 0;

    if ($thumb_id) {
        set_post_thumbnail($post_id, $thumb_id);
    } else {
        delete_post_thumbnail($post_id);
    }

    if (isset($_POST['iez_page_template'])) {
        $template = sanitize_text_field(wp_unslash($_POST['iez_page_template']));

        if ($template === 'default') {
            delete_post_meta($post_id, '_wp_page_template');
        } else {
            update_post_meta($post_id, '_wp_page_template', $template);
        }
    }
}

function naigai_iez_admin_save_existing_meta_fields($post_id)
{
    if (isset($_POST['iez_existing_meta']) && is_array($_POST['iez_existing_meta'])) {
        foreach ($_POST['iez_existing_meta'] as $key => $value) {
            $key = sanitize_key($key);

            if (!naigai_iez_admin_allowed_meta_key($key)) {
                continue;
            }

            $value = wp_unslash($value);

            if ((string) $value === '') {
                delete_post_meta($post_id, $key);
            } else {
                update_post_meta($post_id, $key, sanitize_textarea_field($value));
            }
        }
    }

    for ($i = 1; $i <= 2; $i++) {
        $key_name = 'iez_new_meta_key_' . $i;
        $value_name = 'iez_new_meta_value_' . $i;

        $key = isset($_POST[$key_name]) ? sanitize_key(wp_unslash($_POST[$key_name])) : '';
        $value = isset($_POST[$value_name]) ? wp_unslash($_POST[$value_name]) : '';

        if ($key === '' || !naigai_iez_admin_allowed_meta_key($key)) {
            continue;
        }

        if ((string) $value === '') {
            delete_post_meta($post_id, $key);
        } else {
            update_post_meta($post_id, $key, sanitize_textarea_field($value));
        }
    }
}

function naigai_iez_admin_save_all_fields($post_id)
{
    naigai_iez_admin_save_page_core_fields($post_id);

    if (function_exists('naigai_iez_admin_save_hero_fields')) {
        naigai_iez_admin_save_hero_fields($post_id);
    }

    naigai_iez_admin_save_existing_meta_fields($post_id);
}
