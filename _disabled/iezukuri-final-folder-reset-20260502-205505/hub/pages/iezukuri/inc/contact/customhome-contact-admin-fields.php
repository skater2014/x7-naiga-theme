<?php
/**
 * =========================================================
 * hub/inc/customhome-contact-admin-fields.php
 *
 * /iezukuri/contact 専用 管理画面
 *
 * 役割:
 * - CTA背景・画像・動画メニューの代わりに、
 *   ご相談の流れ / お問い合わせフォーム / CTA文言を整理して表示する。
 * =========================================================
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('naigai_ch_contact_admin_is_target')) {
    function naigai_ch_contact_admin_is_target($post_id)
    {
        $post_id = absint($post_id);
        if (!$post_id) {
            return false;
        }

        $post = get_post($post_id);
        if (!$post || $post->post_type !== 'page') {
            return false;
        }

        if (get_page_template_slug($post_id) !== 'page-construction-hub-sub.php') {
            return false;
        }

        $slug   = (string) get_post_field('post_name', $post_id);
        $layout = (string) get_post_meta($post_id, '_ch_subpage_template', true);

        return $slug === 'contact' || $layout === 'contact';
    }
}

if (!function_exists('naigai_ch_contact_admin_page_options')) {
    function naigai_ch_contact_admin_page_options()
    {
        $pages = get_pages(array(
            'post_status' => array('publish', 'draft', 'private'),
            'sort_column' => 'menu_order,post_title',
            'sort_order'  => 'ASC',
        ));

        $out = array();

        foreach ($pages as $p) {
            $out[] = array(
                'id'    => (int) $p->ID,
                'label' => $p->post_title . ' / ' . get_page_uri($p->ID),
            );
        }

        return $out;
    }
}

if (!function_exists('naigai_ch_contact_admin_text')) {
    function naigai_ch_contact_admin_text($post_id, $key, $label, $type = 'text')
    {
        $value = get_post_meta($post_id, $key, true);
        ?>
        <div class="naigai-admin-field">
            <label for="<?php echo esc_attr($key); ?>"><?php echo esc_html($label); ?></label>
            <?php if ($type === 'textarea') : ?>
                <textarea id="<?php echo esc_attr($key); ?>" name="<?php echo esc_attr($key); ?>" rows="3" class="large-text"><?php echo esc_textarea($value); ?></textarea>
            <?php else : ?>
                <input id="<?php echo esc_attr($key); ?>" name="<?php echo esc_attr($key); ?>" type="<?php echo esc_attr($type); ?>" class="regular-text" value="<?php echo esc_attr($value); ?>">
            <?php endif; ?>
        </div>
        <?php
    }
}

if (!function_exists('naigai_ch_contact_admin_page_select')) {
    function naigai_ch_contact_admin_page_select($post_id, $key, $label)
    {
        $selected = absint(get_post_meta($post_id, $key, true));
        ?>
        <div class="naigai-admin-field">
            <label for="<?php echo esc_attr($key); ?>"><?php echo esc_html($label); ?></label>
            <select id="<?php echo esc_attr($key); ?>" name="<?php echo esc_attr($key); ?>">
                <option value="0">選択しない / URL手入力を使う</option>
                <?php foreach (naigai_ch_contact_admin_page_options() as $page) : ?>
                    <option value="<?php echo esc_attr($page['id']); ?>" <?php selected($selected, $page['id']); ?>>
                        <?php echo esc_html($page['label']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php
    }
}

if (!function_exists('naigai_ch_contact_admin_box')) {
    function naigai_ch_contact_admin_box($post)
    {
        wp_nonce_field('naigai_ch_contact_admin_save', 'naigai_ch_contact_admin_nonce');
        ?>
        <div class="naigai-admin-guide naigai-admin-guide--contact">
            <strong>このメニューは /iezukuri/contact 専用です。</strong>
            <p>このページでは CTA背景画像・mp4 ではなく、「ご相談の流れ」「CTA文言」「お問い合わせフォーム」を編集します。</p>
        </div>

        <div class="naigai-admin-section">
            <h3>③-1 ご相談・資料請求の流れ</h3>
            <?php
            naigai_ch_contact_admin_text($post->ID, '_ch_contact_flow_title', 'Flow 見出し');
            naigai_ch_contact_admin_text($post->ID, '_ch_contact_flow_lead', 'Flow 説明文', 'textarea');

            for ($i = 1; $i <= 4; $i++) {
                echo '<div class="naigai-admin-subcard">';
                echo '<h4>STEP ' . esc_html((string) $i) . '</h4>';
                naigai_ch_contact_admin_text($post->ID, "_ch_contact_flow_{$i}_label", 'ラベル');
                naigai_ch_contact_admin_text($post->ID, "_ch_contact_flow_{$i}_title", 'タイトル');
                naigai_ch_contact_admin_text($post->ID, "_ch_contact_flow_{$i}_text", '説明文', 'textarea');
                echo '</div>';
            }
            ?>
        </div>

        <div class="naigai-admin-section">
            <h3>③-2 お問い合わせフォーム</h3>
            <p class="description">フォーム本体は /contact と同じ共通パーツを表示します。ここでは見出しだけ編集します。</p>
            <?php
            naigai_ch_contact_admin_text($post->ID, '_ch_contact_form_title', 'フォーム見出し');
            naigai_ch_contact_admin_text($post->ID, '_ch_contact_form_text', 'フォーム説明文', 'textarea');
            ?>
        </div>

        <div class="naigai-admin-section">
            <h3>④ CTA文言・ボタン</h3>
            <p class="description">/iezukuri/contact では背景画像・mp4は使わず、フォーム前のCTA文言とボタンだけ編集します。</p>
            <?php
            naigai_ch_contact_admin_text($post->ID, '_hub_ch_cta_eyebrow', 'CTA ラベル');
            naigai_ch_contact_admin_text($post->ID, '_hub_ch_cta_title', 'CTA タイトル', 'textarea');
            naigai_ch_contact_admin_text($post->ID, '_hub_ch_cta_text', 'CTA 説明文', 'textarea');

            naigai_ch_contact_admin_text($post->ID, '_hub_ch_cta_btn1_label', '主ボタン文言');
            naigai_ch_contact_admin_page_select($post->ID, '_hub_ch_cta_btn1_page_id', '主ボタンリンク先ページ');
            naigai_ch_contact_admin_text($post->ID, '_hub_ch_cta_btn1_url', '主ボタンURL', 'text');

            naigai_ch_contact_admin_text($post->ID, '_hub_ch_cta_btn2_label', '副ボタン文言');
            naigai_ch_contact_admin_page_select($post->ID, '_hub_ch_cta_btn2_page_id', '副ボタンリンク先ページ');
            naigai_ch_contact_admin_text($post->ID, '_hub_ch_cta_btn2_url', '副ボタンURL', 'text');
            ?>
        </div>
        <?php
    }
}

if (!function_exists('naigai_ch_contact_admin_add_box')) {
    function naigai_ch_contact_admin_add_box()
    {
        global $post;

        if (!$post || !naigai_ch_contact_admin_is_target($post->ID)) {
            return;
        }

        add_meta_box(
            'naigai_ch_contact_admin_fields',
            '③ お問い合わせフォーム・Flow・CTA設定',
            'naigai_ch_contact_admin_box',
            'page',
            'normal',
            'high'
        );
    }

    // DISABLED: unified /iezukuri reflected metabox handles these fields.

    // add_action('add_meta_boxes', 'naigai_ch_contact_admin_add_box');
}

if (!function_exists('naigai_ch_contact_admin_save')) {
    function naigai_ch_contact_admin_save($post_id)
    {
        if (!naigai_ch_contact_admin_is_target($post_id)) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        if (!isset($_POST['naigai_ch_contact_admin_nonce']) || !wp_verify_nonce($_POST['naigai_ch_contact_admin_nonce'], 'naigai_ch_contact_admin_save')) {
            return;
        }

        $text_keys = array(
            '_ch_contact_flow_title',
            '_ch_contact_flow_lead',
            '_ch_contact_form_title',
            '_ch_contact_form_text',
            '_hub_ch_cta_eyebrow',
            '_hub_ch_cta_title',
            '_hub_ch_cta_text',
            '_hub_ch_cta_btn1_label',
            '_hub_ch_cta_btn1_url',
            '_hub_ch_cta_btn2_label',
            '_hub_ch_cta_btn2_url',
        );

        for ($i = 1; $i <= 4; $i++) {
            $text_keys[] = "_ch_contact_flow_{$i}_label";
            $text_keys[] = "_ch_contact_flow_{$i}_title";
            $text_keys[] = "_ch_contact_flow_{$i}_text";
        }

        foreach ($text_keys as $key) {
            $value = isset($_POST[$key]) ? wp_kses_post(wp_unslash($_POST[$key])) : '';
            update_post_meta($post_id, $key, $value);
        }

        foreach (array('_hub_ch_cta_btn1_page_id', '_hub_ch_cta_btn2_page_id') as $key) {
            update_post_meta($post_id, $key, isset($_POST[$key]) ? absint($_POST[$key]) : 0);
        }
    }

    add_action('save_post_page', 'naigai_ch_contact_admin_save', 35);
}
