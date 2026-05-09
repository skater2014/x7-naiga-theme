<?php
/**
 * =========================================================
 * Iezukuri fixed page content metabox
 * ---------------------------------------------------------
 * 対象:
 * - /iezukuri/new-house/
 * - /iezukuri/chuko/
 * - /iezukuri/nisetai/
 *
 * 役割:
 * - 固定ページ内に直書きされていた内容を、ページ編集画面で編集できるようにする
 *
 * 保存メタ:
 * - _ch_page_hero_json
 * - _ch_page_sections_json
 * - _ch_page_cta_json
 *
 * 注意:
 * - /iezukuri/ トップページには表示しない
 * - iez_plan の _ch_plan_* とは混ぜない
 * =========================================================
 */

if (!defined('ABSPATH')) {
    exit;
}

function naigai_iezukuri_page_content_target_slugs() {
    return array(
        'new-house',
        'chuko',
        'nisetai',
    );
}

function naigai_iezukuri_page_content_is_target($post_id) {
    $post = get_post($post_id);

    if (!$post || $post->post_type !== 'page') {
        return false;
    }

    return in_array($post->post_name, naigai_iezukuri_page_content_target_slugs(), true);
}

add_action('add_meta_boxes_page', function ($post) {
    if (!naigai_iezukuri_page_content_is_target($post->ID)) {
        return;
    }

    add_meta_box(
        'naigai_iezukuri_page_content_metabox',
        '家づくりページ本文設定',
        'naigai_iezukuri_render_page_content_metabox',
        'page',
        'normal',
        'high'
    );
});

function naigai_iezukuri_json_textarea_value($post_id, $key, $default) {
    $raw = get_post_meta($post_id, $key, true);

    if ($raw !== '') {
        $decoded = json_decode((string) $raw, true);

        if (is_array($decoded)) {
            return wp_json_encode($decoded, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        }

        return $raw;
    }

    return wp_json_encode($default, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
}

function naigai_iezukuri_render_page_content_metabox($post) {
    wp_nonce_field('naigai_iezukuri_save_page_content_meta', 'naigai_iezukuri_page_content_nonce');

    $hero_default = array(
        'eyebrow' => get_the_title($post),
        'title'   => get_the_title($post),
        'lead'    => '',
    );

    $sections_default = array(
        array(
            'type'   => 'text',
            'kicker' => '',
            'title'  => '見出し',
            'body'   => '本文を入力します。',
            'items'  => array(),
        ),
    );

    $cta_default = array(
        'title'       => '住まいづくりを相談する',
        'body'        => '計画前の段階から相談できます。',
        'button_text' => '無料相談・資料請求',
        'button_url'  => home_url('/contact/'),
    );

    $hero_value = naigai_iezukuri_json_textarea_value($post->ID, '_ch_page_hero_json', $hero_default);
    $sections_value = naigai_iezukuri_json_textarea_value($post->ID, '_ch_page_sections_json', $sections_default);
    $cta_value = naigai_iezukuri_json_textarea_value($post->ID, '_ch_page_cta_json', $cta_default);
    ?>
    <div class="iez-page-admin">

        <details class="iez-page-admin__panel" open>
            <summary>1. ヒーロー</summary>
            <textarea name="_ch_page_hero_json" rows="8" spellcheck="false"><?php echo esc_textarea($hero_value); ?></textarea>
        </details>

        <details class="iez-page-admin__panel" open>
            <summary>2. 本文セクション</summary>
            <textarea name="_ch_page_sections_json" rows="18" spellcheck="false"><?php echo esc_textarea($sections_value); ?></textarea>
            <p class="description">
                type は text / cards など。items に title と body を入れるとカード表示になります。
            </p>
        </details>

        <details class="iez-page-admin__panel">
            <summary>3. CTA</summary>
            <textarea name="_ch_page_cta_json" rows="8" spellcheck="false"><?php echo esc_textarea($cta_value); ?></textarea>
        </details>
    </div>
    <?php
}

add_action('save_post_page', function ($post_id) {
    if (!isset($_POST['naigai_iezukuri_page_content_nonce'])) {
        return;
    }

    if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['naigai_iezukuri_page_content_nonce'])), 'naigai_iezukuri_save_page_content_meta')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (!naigai_iezukuri_page_content_is_target($post_id)) {
        return;
    }

    $keys = array(
        '_ch_page_hero_json',
        '_ch_page_sections_json',
        '_ch_page_cta_json',
    );

    foreach ($keys as $key) {
        if (!isset($_POST[$key])) {
            continue;
        }

        $raw = trim((string) wp_unslash($_POST[$key]));

        if ($raw === '') {
            delete_post_meta($post_id, $key);
            continue;
        }

        $decoded = json_decode($raw, true);

        if (is_array($decoded)) {
            update_post_meta(
                $post_id,
                $key,
                wp_json_encode($decoded, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
            );
        } else {
            update_post_meta($post_id, $key, sanitize_textarea_field($raw));
        }
    }
});

add_action('admin_enqueue_scripts', function ($hook) {
    if ($hook !== 'post.php' && $hook !== 'post-new.php') {
        return;
    }

    $screen = get_current_screen();

    if (!$screen || $screen->post_type !== 'page') {
        return;
    }

    wp_enqueue_style(
        'naigai-iezukuri-page-content-admin',
        get_template_directory_uri() . '/hub/pages/iezukuri/admin/assets/css/page-content-metabox.css',
        array(),
        filemtime(get_template_directory() . '/hub/pages/iezukuri/admin/assets/css/page-content-metabox.css')
    );
});
