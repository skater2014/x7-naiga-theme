<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * =========================================================
 * Hub 対象ページか判定
 * =========================================================
 *
 * 対象:
 * - フロントページ
 * - template-construction-hub.php
 * - template-realestate-hub.php
 */
function naigai_is_hub_target_page($post_id)
{
    $post_id = (int) $post_id;

    if ($post_id <= 0 || get_post_type($post_id) !== 'page') {
        return false;
    }

    $front_page_id = (int) get_option('page_on_front');
    if ($front_page_id > 0 && $front_page_id === $post_id) {
        return true;
    }

    $template = get_page_template_slug($post_id);

    return in_array(
        $template,
        array(
            'template-construction-hub.php',
            'template-realestate-hub.php',
        ),
        true
    );
}

/**
 * =========================================================
 * Hub メタ取得
 * =========================================================
 */
function naigai_hub_get($post_id, $key, $default = '')
{
    $value = get_post_meta((int) $post_id, $key, true);
    return ($value !== '' && $value !== null) ? $value : $default;
}

/**
 * =========================================================
 * Hub 項目URLを解決
 * =========================================================
 *
 * 優先順位:
 * 1. *_page_id があればその固定ページURL
 * 2. なければ既存の *_url
 *
 * こうしておくことで、
 * 既存のテンプレート側で 'url' を読むだけで
 * page_id 対応へ移行できる
 */
function naigai_hub_resolve_item_url($post_id, $base_key)
{
    $page_id = (int) get_post_meta((int) $post_id, "{$base_key}_page_id", true);

    if ($page_id > 0 && get_post_status($page_id)) {
        $permalink = get_permalink($page_id);
        if (!empty($permalink)) {
            return $permalink;
        }
    }

    return naigai_hub_get($post_id, "{$base_key}_url", '');
}

/**
 * =========================================================
 * Hub カード / リンク配列取得
 * =========================================================
 *
 * 返す配列:
 * - title
 * - text
 * - url       ← 表示側はこれを使えばOK
 * - page_id   ← 必要ならテンプレートで参照できる
 * - raw_url   ← 旧URL / 外部URLの入力値
 */
function naigai_hub_get_items($post_id, $prefix, $count = 4)
{
    $items = array();

    for ($i = 1; $i <= $count; $i++) {
        $base_key = "{$prefix}_{$i}";
        $page_id  = (int) get_post_meta((int) $post_id, "{$base_key}_page_id", true);
        $raw_url  = naigai_hub_get($post_id, "{$base_key}_url", '');

        $items[] = array(
            'title'   => naigai_hub_get($post_id, "{$base_key}_title", ''),
            'text'    => naigai_hub_get($post_id, "{$base_key}_text", ''),
            'url'     => naigai_hub_resolve_item_url($post_id, $base_key),
            'page_id' => $page_id,
            'raw_url' => $raw_url,
        );
    }

    return $items;
}

/**
 * =========================================================
 * Hub メタボックス登録
 * =========================================================
 */
function naigai_register_hub_meta_box()
{
    add_meta_box(
        'naigai-hub-meta-box',
        'Hub ページ設定',
        'naigai_render_hub_meta_box',
        'page',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'naigai_register_hub_meta_box');

/**
 * =========================================================
 * Hub 項目の固定ページ選択UI
 * =========================================================
 *
 * Gutenberg でも普通のメタボックスとして使える
 */
function naigai_render_hub_page_dropdown($name, $selected = 0)
{
    $selected = (int) $selected;

    wp_dropdown_pages(
        array(
            'name'              => $name,
            'id'                => $name,
            'selected'          => $selected,
            'show_option_none'  => '— 固定ページを選択してください —',
            'option_none_value' => '0',
            'sort_column'       => 'menu_order,post_title',
            'post_status'       => array('publish', 'private', 'draft'),
            'echo'              => true,
            'class'             => 'widefat',
        )
    );
}

/**
 * =========================================================
 * Hub メタボックス描画
 * =========================================================
 */
function naigai_render_hub_meta_box($post)
{
    if (!naigai_is_hub_target_page($post->ID)) {
        echo '<p>このメタボックスは、フロントページ・建設業窓口・不動産業窓口で使います。</p>';
        return;
    }

    wp_nonce_field('naigai_hub_meta_box_save', 'naigai_hub_meta_box_nonce');

    $fields = array(
        '_hub_kicker'          => '上部ラベル',
        '_hub_title'           => 'メイン見出し',
        '_hub_lead'            => 'リード文',
        '_hub_gateway_title'   => '第1セクション 見出し',
        '_hub_links_title'     => '第2セクション 見出し',
        '_hub_cta_title'       => 'CTA 見出し',
        '_hub_secondary_title' => '補足セクション 見出し',
        '_hub_secondary_text'  => '補足セクション 本文',
    );

    echo '<table class="form-table"><tbody>';

    foreach ($fields as $key => $label) {
        $value = get_post_meta($post->ID, $key, true);

        echo '<tr>';
        echo '<th scope="row"><label for="' . esc_attr($key) . '">' . esc_html($label) . '</label></th>';
        echo '<td>';

        if (in_array($key, array('_hub_lead', '_hub_secondary_text'), true)) {
            echo '<textarea id="' . esc_attr($key) . '" name="' . esc_attr($key) . '" rows="4" class="large-text">' . esc_textarea($value) . '</textarea>';
        } else {
            echo '<input type="text" id="' . esc_attr($key) . '" name="' . esc_attr($key) . '" value="' . esc_attr($value) . '" class="regular-text" />';
        }

        echo '</td>';
        echo '</tr>';
    }

    echo '</tbody></table>';

    echo '<hr>';
    echo '<h3>カード / リンク設定</h3>';
    echo '<p>基本は「固定ページを選択」を使ってください。外部サイトや任意URLへ飛ばしたい場合のみ、下のURL欄を使います。</p>';

    $card_prefix = ($post->ID === (int) get_option('page_on_front')) ? '_hub_card' : '_hub_link';
    $card_count  = ($card_prefix === '_hub_card') ? 4 : 5;

    echo '<table class="form-table"><tbody>';

    for ($i = 1; $i <= $card_count; $i++) {
        $base_key = "{$card_prefix}_{$i}";

        $title   = get_post_meta($post->ID, "{$base_key}_title", true);
        $text    = get_post_meta($post->ID, "{$base_key}_text", true);
        $url     = get_post_meta($post->ID, "{$base_key}_url", true);
        $page_id = (int) get_post_meta($post->ID, "{$base_key}_page_id", true);

        echo '<tr>';
        echo '<th scope="row">項目 ' . (int) $i . '</th>';
        echo '<td>';

        echo '<p>';
        echo '<label for="' . esc_attr("{$base_key}_title") . '"><strong>タイトル</strong></label><br>';
        echo '<input type="text" id="' . esc_attr("{$base_key}_title") . '" name="' . esc_attr("{$base_key}_title") . '" value="' . esc_attr($title) . '" class="regular-text" placeholder="タイトル">';
        echo '</p>';

        echo '<p>';
        echo '<label for="' . esc_attr("{$base_key}_text") . '"><strong>説明文</strong></label><br>';
        echo '<textarea id="' . esc_attr("{$base_key}_text") . '" name="' . esc_attr("{$base_key}_text") . '" rows="3" class="large-text" placeholder="説明文">' . esc_textarea($text) . '</textarea>';
        echo '</p>';

        echo '<p>';
        echo '<label for="' . esc_attr("{$base_key}_page_id") . '"><strong>固定ページを選択</strong></label><br>';
        naigai_render_hub_page_dropdown("{$base_key}_page_id", $page_id);
        echo '</p>';

        echo '<p>';
        echo '<label for="' . esc_attr("{$base_key}_url") . '"><strong>外部URL / 任意URL</strong></label><br>';
        echo '<input type="text" id="' . esc_attr("{$base_key}_url") . '" name="' . esc_attr("{$base_key}_url") . '" value="' . esc_attr($url) . '" class="widefat" placeholder="https://example.com/">';
        echo '<br><span class="description">固定ページを選んだ場合は、そのページURLが優先されます。外部サイトへ飛ばすときだけ入力してください。</span>';
        echo '</p>';

        $resolved_url = naigai_hub_resolve_item_url($post->ID, $base_key);
        if (!empty($resolved_url)) {
            echo '<p><span class="description">現在の出力URL: ' . esc_html($resolved_url) . '</span></p>';
        }

        echo '</td>';
        echo '</tr>';
    }

    echo '</tbody></table>';
}

/**
 * =========================================================
 * Hub メタ保存
 * =========================================================
 */
function naigai_save_hub_meta_box($post_id)
{
    if (!isset($_POST['naigai_hub_meta_box_nonce'])) {
        return;
    }

    if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['naigai_hub_meta_box_nonce'])), 'naigai_hub_meta_box_save')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (!naigai_is_hub_target_page($post_id)) {
        return;
    }

    $text_fields = array(
        '_hub_kicker',
        '_hub_title',
        '_hub_gateway_title',
        '_hub_links_title',
        '_hub_cta_title',
        '_hub_secondary_title',
    );

    $textarea_fields = array(
        '_hub_lead',
        '_hub_secondary_text',
    );

    foreach ($text_fields as $key) {
        if (!isset($_POST[$key])) {
            continue;
        }

        $value = sanitize_text_field(wp_unslash($_POST[$key]));

        if ($value === '') {
            delete_post_meta($post_id, $key);
        } else {
            update_post_meta($post_id, $key, $value);
        }
    }

    foreach ($textarea_fields as $key) {
        if (!isset($_POST[$key])) {
            continue;
        }

        $value = sanitize_textarea_field(wp_unslash($_POST[$key]));

        if ($value === '') {
            delete_post_meta($post_id, $key);
        } else {
            update_post_meta($post_id, $key, $value);
        }
    }

    $card_prefix = ($post_id === (int) get_option('page_on_front')) ? '_hub_card' : '_hub_link';
    $card_count  = ($card_prefix === '_hub_card') ? 4 : 5;

    for ($i = 1; $i <= $card_count; $i++) {
        $base_key = "{$card_prefix}_{$i}";

        /**
         * -----------------------------
         * title
         * -----------------------------
         */
        $title_key = "{$base_key}_title";
        if (isset($_POST[$title_key])) {
            $value = sanitize_text_field(wp_unslash($_POST[$title_key]));

            if ($value === '') {
                delete_post_meta($post_id, $title_key);
            } else {
                update_post_meta($post_id, $title_key, $value);
            }
        }

        /**
         * -----------------------------
         * text
         * -----------------------------
         */
        $text_key = "{$base_key}_text";
        if (isset($_POST[$text_key])) {
            $value = sanitize_textarea_field(wp_unslash($_POST[$text_key]));

            if ($value === '') {
                delete_post_meta($post_id, $text_key);
            } else {
                update_post_meta($post_id, $text_key, $value);
            }
        }

        /**
         * -----------------------------
         * page_id
         * -----------------------------
         */
        $page_id_key = "{$base_key}_page_id";
        if (isset($_POST[$page_id_key])) {
            $value = absint(wp_unslash($_POST[$page_id_key]));

            if ($value <= 0) {
                delete_post_meta($post_id, $page_id_key);
            } else {
                update_post_meta($post_id, $page_id_key, $value);
            }
        }

        /**
         * -----------------------------
         * url
         * -----------------------------
         * 外部URL / 任意URL 用
         */
        $url_key = "{$base_key}_url";
        if (isset($_POST[$url_key])) {
            $value = esc_url_raw(wp_unslash($_POST[$url_key]));

            if ($value === '') {
                delete_post_meta($post_id, $url_key);
            } else {
                update_post_meta($post_id, $url_key, $value);
            }
        }
    }
}
add_action('save_post_page', 'naigai_save_hub_meta_box');
