<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('mpb_is_target_template')) {
    function mpb_is_target_template($post_id = 0)
    {
        $post_id = $post_id ?: get_the_ID();

        if (!$post_id) {
            return false;
        }

        return get_post_type($post_id) === 'page'
            && get_page_template_slug($post_id) === 'page-minpaku-b2c.php';
    }
}

if (!function_exists('mpb_admin_text')) {
    function mpb_admin_text($post_id, $key, $default = '')
    {
        $value = get_post_meta($post_id, $key, true);

        if ($value === '' || $value === null) {
            return $default;
        }

        return is_scalar($value) ? (string) $value : $default;
    }
}

if (!function_exists('mpb_admin_bool')) {
    function mpb_admin_bool($post_id, $key, $default = false)
    {
        $value = get_post_meta($post_id, $key, true);

        if ($value === '' || $value === null) {
            return $default;
        }

        return in_array((string) $value, array('1', 'true', 'on', 'yes'), true);
    }
}

if (!function_exists('mpb_admin_json_array')) {
    function mpb_admin_json_array($post_id, $key)
    {
        $raw = get_post_meta($post_id, $key, true);

        if (!is_string($raw) || trim($raw) === '') {
            return array();
        }

        $decoded = json_decode($raw, true);

        return is_array($decoded) ? $decoded : array();
    }
}

if (!function_exists('mpb_attachment_preview_html')) {
    function mpb_attachment_preview_html($attachment_id)
    {
        $attachment_id = absint($attachment_id);

        if ($attachment_id <= 0) {
            return '';
        }

        $thumb = wp_get_attachment_image_url($attachment_id, 'thumbnail');

        if (!$thumb) {
            return '';
        }

        $file = basename((string) get_attached_file($attachment_id));

        return '<div class="mpb-admin__media-card">'
            . '<img src="' . esc_url($thumb) . '" alt="">'
            . '<div class="mpb-admin__media-meta">ID: ' . (int) $attachment_id . '<br>' . esc_html($file) . '</div>'
            . '</div>';
    }
}

if (!function_exists('mpb_gallery_preview_html')) {
    function mpb_gallery_preview_html($ids_csv)
    {
        $ids = array_filter(array_map('absint', preg_split('/[\s,]+/', (string) $ids_csv)));

        if (empty($ids)) {
            return '';
        }

        $html = '<div class="mpb-admin__gallery-grid">';

        foreach ($ids as $attachment_id) {
            $thumb = wp_get_attachment_image_url($attachment_id, 'thumbnail');

            if (!$thumb) {
                continue;
            }

            $html .= '<div class="mpb-admin__gallery-card">';
            $html .= '<img src="' . esc_url($thumb) . '" alt="">';
            $html .= '<div class="mpb-admin__media-meta">ID: ' . (int) $attachment_id . '</div>';
            $html .= '</div>';
        }

        $html .= '</div>';

        return $html;
    }
}

if (!function_exists('mpb_render_text_input')) {
    function mpb_render_text_input($name, $label, $value = '', $placeholder = '')
    {
        echo '<div class="mpb-admin__field">';
        echo '<label for="' . esc_attr($name) . '">' . esc_html($label) . '</label>';
        echo '<input type="text" class="widefat" id="' . esc_attr($name) . '" name="' . esc_attr($name) . '" value="' . esc_attr($value) . '" placeholder="' . esc_attr($placeholder) . '">';
        echo '</div>';
    }
}

if (!function_exists('mpb_render_textarea')) {
    function mpb_render_textarea($name, $label, $value = '', $rows = 4, $placeholder = '')
    {
        echo '<div class="mpb-admin__field">';
        echo '<label for="' . esc_attr($name) . '">' . esc_html($label) . '</label>';
        echo '<textarea class="widefat" rows="' . (int) $rows . '" id="' . esc_attr($name) . '" name="' . esc_attr($name) . '" placeholder="' . esc_attr($placeholder) . '">' . esc_textarea($value) . '</textarea>';
        echo '</div>';
    }
}

if (!function_exists('mpb_render_checkbox')) {
    function mpb_render_checkbox($name, $label, $checked = false)
    {
        echo '<div class="mpb-admin__field">';
        echo '<label class="mpb-admin__checkbox">';
        echo '<input type="checkbox" id="' . esc_attr($name) . '" name="' . esc_attr($name) . '" value="1" ' . checked($checked, true, false) . '>';
        echo ' ' . esc_html($label);
        echo '</label>';
        echo '</div>';
    }
}

if (!function_exists('mpb_render_media_picker')) {
    function mpb_render_media_picker($name, $label, $value = '')
    {
        $attachment_id = absint($value);

        echo '<div class="mpb-admin__field js-mpb-media-field">';
        echo '<label>' . esc_html($label) . '</label>';
        echo '<input type="hidden" class="js-mpb-media-id" name="' . esc_attr($name) . '" value="' . esc_attr($attachment_id) . '">';
        echo '<div class="mpb-admin__actions">';
        echo '<button type="button" class="button button-secondary js-mpb-pick-image">画像を選ぶ</button>';
        echo '<button type="button" class="button js-mpb-clear-image">クリア</button>';
        echo '</div>';
        echo '<div class="mpb-admin__media-preview">' . mpb_attachment_preview_html($attachment_id) . '</div>';
        echo '</div>';
    }
}

if (!function_exists('mpb_render_gallery_picker')) {
    function mpb_render_gallery_picker($name, $label, $value = '')
    {
        echo '<div class="mpb-admin__field js-mpb-gallery-field">';
        echo '<label>' . esc_html($label) . '</label>';
        echo '<input type="hidden" class="js-mpb-gallery-ids" name="' . esc_attr($name) . '" value="' . esc_attr((string) $value) . '">';
        echo '<div class="mpb-admin__actions">';
        echo '<button type="button" class="button button-secondary js-mpb-pick-gallery">複数画像を選ぶ</button>';
        echo '<button type="button" class="button js-mpb-clear-gallery">クリア</button>';
        echo '</div>';
        echo '<div class="mpb-admin__gallery-preview">' . mpb_gallery_preview_html($value) . '</div>';
        echo '<p class="description">2枚以上でフロントは swiper 表示になります。</p>';
        echo '</div>';
    }
}

if (!function_exists('mpb_add_meta_boxes')) {
    function mpb_add_meta_boxes()
    {
        add_meta_box(
            'mpb_b2c_settings',
            '民泊B2C 設定',
            'mpb_render_meta_box',
            'page',
            'normal',
            'high'
        );
    }
    add_action('add_meta_boxes', 'mpb_add_meta_boxes');
}

if (!function_exists('mpb_enqueue_admin_assets')) {
    function mpb_enqueue_admin_assets($hook)
    {
        if (!in_array($hook, array('post.php', 'post-new.php'), true)) {
            return;
        }

        $screen = function_exists('get_current_screen') ? get_current_screen() : null;

        if (!$screen || $screen->post_type !== 'page') {
            return;
        }

        wp_enqueue_media();

        $css_abs = get_template_directory() . '/minpaku/admin/css/minpaku-b2c-admin.css';
        $js_abs  = get_template_directory() . '/minpaku/admin/js/minpaku-b2c-admin.js';

        if (file_exists($css_abs)) {
            wp_enqueue_style(
                'minpaku-b2c-admin-css',
                get_template_directory_uri() . '/minpaku/admin/css/minpaku-b2c-admin.css',
                array(),
                (string) filemtime($css_abs)
            );
        }

        if (file_exists($js_abs)) {
            wp_enqueue_script(
                'minpaku-b2c-admin-js',
                get_template_directory_uri() . '/minpaku/admin/js/minpaku-b2c-admin.js',
                array('jquery'),
                (string) filemtime($js_abs),
                true
            );
        }
    }
    add_action('admin_enqueue_scripts', 'mpb_enqueue_admin_assets');
}

if (!function_exists('mpb_render_meta_box')) {
    function mpb_render_meta_box($post)
    {
        if (!mpb_is_target_template($post->ID)) {
            echo '<p>このテンプレートではありません。</p>';
            return;
        }

        wp_nonce_field('mpb_save_meta_box', 'mpb_meta_box_nonce');

        $feature_items = mpb_admin_json_array($post->ID, '_mpb_feature_items_json');
        $guide_items   = mpb_admin_json_array($post->ID, '_mpb_guide_items_json');
        $flow_items    = mpb_admin_json_array($post->ID, '_mpb_flow_items_json');
        $faq_items     = mpb_admin_json_array($post->ID, '_mpb_faq_items_json');
        $compare       = mpb_admin_json_array($post->ID, '_mpb_compare_table_json');

        $compare_columns = (!empty($compare['columns']) && is_array($compare['columns'])) ? $compare['columns'] : array();
        $compare_rows    = (!empty($compare['rows']) && is_array($compare['rows'])) ? $compare['rows'] : array();

        echo '<div class="mpb-admin">';
        echo '<div class="mpb-admin__tabs">';

        $tabs = array(
            'basic'   => '基本',
            'hero'    => 'Hero',
            'intro'   => 'Intro',
            'feature' => 'Feature',
            'guide'   => 'Guide',
            'compare' => '比較表',
            'flow'    => 'Flow',
            'faq'     => 'FAQ',
            'cta'     => 'CTA',
        );

        foreach ($tabs as $key => $label) {
            echo '<button type="button" class="mpb-admin__tab" data-mpb-tab-target="' . esc_attr($key) . '">' . esc_html($label) . '</button>';
        }

        echo '</div>';

        echo '<section class="mpb-admin__panel" data-mpb-tab-panel="basic">';
        echo '<h2>基本設定</h2>';
        echo '<p class="mpb-admin__help">Quick Edit ではなく、通常の固定ページ編集画面で管理してください。</p>';
        mpb_render_text_input('_mpb_layout_type', 'レイアウト種別', mpb_admin_text($post->ID, '_mpb_layout_type', 'standard'), 'standard / guide / family / group / workation / compare');
        mpb_render_checkbox('_mpb_show_hero', 'Hero を表示', mpb_admin_bool($post->ID, '_mpb_show_hero', true));
        mpb_render_checkbox('_mpb_show_intro', 'Intro を表示', mpb_admin_bool($post->ID, '_mpb_show_intro', true));
        mpb_render_checkbox('_mpb_show_footer_nav', '共通 footer nav を表示', mpb_admin_bool($post->ID, '_mpb_show_footer_nav', true));
        echo '</section>';

        echo '<section class="mpb-admin__panel" data-mpb-tab-panel="hero">';
        echo '<h2>Hero</h2>';
        mpb_render_text_input('_mpb_hero_eyebrow', 'HERO 上小見出し', mpb_admin_text($post->ID, '_mpb_hero_eyebrow', ''));
        mpb_render_text_input('_mpb_hero_title', 'HERO タイトル', mpb_admin_text($post->ID, '_mpb_hero_title', ''));
        mpb_render_textarea('_mpb_hero_lead', 'HERO 説明文', mpb_admin_text($post->ID, '_mpb_hero_lead', ''), 4);
        mpb_render_media_picker('_mpb_hero_image_id', 'HERO 単体画像', mpb_admin_text($post->ID, '_mpb_hero_image_id', ''));
        mpb_render_gallery_picker('_mpb_hero_gallery_ids', 'HERO ギャラリー画像', mpb_admin_text($post->ID, '_mpb_hero_gallery_ids', ''));
        mpb_render_text_input('_mpb_hero_btn1_text', 'HERO ボタン1 テキスト', mpb_admin_text($post->ID, '_mpb_hero_btn1_text', ''));
        mpb_render_text_input('_mpb_hero_btn1_url', 'HERO ボタン1 URL', mpb_admin_text($post->ID, '_mpb_hero_btn1_url', ''));
        mpb_render_text_input('_mpb_hero_btn2_text', 'HERO ボタン2 テキスト', mpb_admin_text($post->ID, '_mpb_hero_btn2_text', ''));
        mpb_render_text_input('_mpb_hero_btn2_url', 'HERO ボタン2 URL', mpb_admin_text($post->ID, '_mpb_hero_btn2_url', ''));
        echo '</section>';

        echo '<section class="mpb-admin__panel" data-mpb-tab-panel="intro">';
        echo '<h2>Intro</h2>';
        mpb_render_text_input('_mpb_intro_title', 'Intro タイトル', mpb_admin_text($post->ID, '_mpb_intro_title', ''));
        mpb_render_textarea('_mpb_intro_text', 'Intro 説明文', mpb_admin_text($post->ID, '_mpb_intro_text', ''), 5);
        mpb_render_media_picker('_mpb_intro_image_id', 'Intro 画像', mpb_admin_text($post->ID, '_mpb_intro_image_id', ''));
        echo '</section>';

        echo '<section class="mpb-admin__panel" data-mpb-tab-panel="feature">';
        echo '<h2>Feature</h2>';
        echo '<p class="mpb-admin__help">最大6件。空の項目は保存しません。</p>';

        for ($i = 0; $i < 6; $i++) {
            $item = isset($feature_items[$i]) && is_array($feature_items[$i]) ? $feature_items[$i] : array();
            $btn1 = !empty($item['btn1']) && is_array($item['btn1']) ? $item['btn1'] : array();
            $btn2 = !empty($item['btn2']) && is_array($item['btn2']) ? $item['btn2'] : array();

            echo '<div class="mpb-admin__item"><h3>Feature ' . ($i + 1) . '</h3>';
            mpb_render_text_input("mpb_feature_{$i}_label", 'ラベル', $item['label'] ?? '');
            mpb_render_text_input("mpb_feature_{$i}_title", 'タイトル', $item['title'] ?? '');
            mpb_render_textarea("mpb_feature_{$i}_text", '説明文', $item['text'] ?? '', 4);
            mpb_render_media_picker("mpb_feature_{$i}_image_id", '画像', $item['image_id'] ?? 0);
            mpb_render_text_input("mpb_feature_{$i}_btn1_text", 'ボタン1 テキスト', $btn1['text'] ?? '');
            mpb_render_text_input("mpb_feature_{$i}_btn1_url", 'ボタン1 URL', $btn1['url'] ?? '');
            mpb_render_text_input("mpb_feature_{$i}_btn2_text", 'ボタン2 テキスト', $btn2['text'] ?? '');
            mpb_render_text_input("mpb_feature_{$i}_btn2_url", 'ボタン2 URL', $btn2['url'] ?? '');
            echo '</div>';
        }

        echo '</section>';

        echo '<section class="mpb-admin__panel" data-mpb-tab-panel="guide">';
        echo '<h2>Guide</h2>';
        echo '<p class="mpb-admin__help">最大6件。空の項目は保存しません。</p>';

        for ($i = 0; $i < 6; $i++) {
            $item = isset($guide_items[$i]) && is_array($guide_items[$i]) ? $guide_items[$i] : array();

            echo '<div class="mpb-admin__item"><h3>Guide ' . ($i + 1) . '</h3>';
            mpb_render_text_input("mpb_guide_{$i}_title", 'タイトル', $item['title'] ?? '');
            mpb_render_textarea("mpb_guide_{$i}_text", '説明文', $item['text'] ?? '', 4);
            mpb_render_media_picker("mpb_guide_{$i}_image_id", '画像', $item['image_id'] ?? 0);
            echo '</div>';
        }

        echo '</section>';

        echo '<section class="mpb-admin__panel" data-mpb-tab-panel="compare">';
        echo '<h2>比較表</h2>';
        echo '<p class="mpb-admin__help">列は最大3件、行は最大8件です。</p>';

        for ($i = 0; $i < 3; $i++) {
            $col = isset($compare_columns[$i]) && is_array($compare_columns[$i]) ? $compare_columns[$i] : array();

            echo '<div class="mpb-admin__item"><h3>列 ' . ($i + 1) . '</h3>';
            mpb_render_text_input("mpb_compare_col_{$i}_title", '列タイトル', $col['title'] ?? '');
            mpb_render_media_picker("mpb_compare_col_{$i}_image_id", '列画像', $col['image_id'] ?? 0);
            echo '</div>';
        }

        for ($i = 0; $i < 8; $i++) {
            $row = isset($compare_rows[$i]) && is_array($compare_rows[$i]) ? $compare_rows[$i] : array();
            $cells = (!empty($row['cells']) && is_array($row['cells'])) ? $row['cells'] : array('', '', '');

            echo '<div class="mpb-admin__item"><h3>行 ' . ($i + 1) . '</h3>';
            mpb_render_text_input("mpb_compare_row_{$i}_label", '行ラベル', $row['label'] ?? '');
            mpb_render_textarea("mpb_compare_row_{$i}_cell_0", '列1 本文', $cells[0] ?? '', 3);
            mpb_render_textarea("mpb_compare_row_{$i}_cell_1", '列2 本文', $cells[1] ?? '', 3);
            mpb_render_textarea("mpb_compare_row_{$i}_cell_2", '列3 本文', $cells[2] ?? '', 3);
            echo '</div>';
        }

        echo '</section>';

        echo '<section class="mpb-admin__panel" data-mpb-tab-panel="flow">';
        echo '<h2>Flow</h2>';
        echo '<p class="mpb-admin__help">最大8件。空の項目は保存しません。</p>';

        for ($i = 0; $i < 8; $i++) {
            $item = isset($flow_items[$i]) && is_array($flow_items[$i]) ? $flow_items[$i] : array();

            echo '<div class="mpb-admin__item"><h3>STEP ' . ($i + 1) . '</h3>';
            mpb_render_text_input("mpb_flow_{$i}_title", 'タイトル', $item['title'] ?? '');
            mpb_render_textarea("mpb_flow_{$i}_text", '説明文', $item['text'] ?? '', 4);
            echo '</div>';
        }

        echo '</section>';

        echo '<section class="mpb-admin__panel" data-mpb-tab-panel="faq">';
        echo '<h2>FAQ</h2>';
        mpb_render_text_input('_mpb_faq_title', 'FAQ タイトル', mpb_admin_text($post->ID, '_mpb_faq_title', ''));
        mpb_render_textarea('_mpb_faq_text', 'FAQ 導入文', mpb_admin_text($post->ID, '_mpb_faq_text', ''), 4);
        echo '<p class="mpb-admin__help">最大12件。空の項目は保存しません。</p>';

        for ($i = 0; $i < 12; $i++) {
            $item = isset($faq_items[$i]) && is_array($faq_items[$i]) ? $faq_items[$i] : array();

            echo '<div class="mpb-admin__item"><h3>FAQ ' . ($i + 1) . '</h3>';
            mpb_render_text_input("mpb_faq_{$i}_q", '質問', $item['q'] ?? '');
            mpb_render_textarea("mpb_faq_{$i}_a", '回答', $item['a'] ?? '', 4);
            echo '</div>';
        }

        echo '</section>';

        echo '<section class="mpb-admin__panel" data-mpb-tab-panel="cta">';
        echo '<h2>CTA</h2>';
        mpb_render_text_input('_mpb_cta_title', 'CTA タイトル', mpb_admin_text($post->ID, '_mpb_cta_title', ''));
        mpb_render_textarea('_mpb_cta_text', 'CTA 説明文', mpb_admin_text($post->ID, '_mpb_cta_text', ''), 4);
        mpb_render_media_picker('_mpb_cta_image_id', 'CTA 画像', mpb_admin_text($post->ID, '_mpb_cta_image_id', ''));
        mpb_render_text_input('_mpb_cta_btn1_text', 'CTA ボタン1 テキスト', mpb_admin_text($post->ID, '_mpb_cta_btn1_text', ''));
        mpb_render_text_input('_mpb_cta_btn1_url', 'CTA ボタン1 URL', mpb_admin_text($post->ID, '_mpb_cta_btn1_url', ''));
        mpb_render_text_input('_mpb_cta_btn2_text', 'CTA ボタン2 テキスト', mpb_admin_text($post->ID, '_mpb_cta_btn2_text', ''));
        mpb_render_text_input('_mpb_cta_btn2_url', 'CTA ボタン2 URL', mpb_admin_text($post->ID, '_mpb_cta_btn2_url', ''));
        echo '</section>';

        echo '</div>';
    }
}

if (!function_exists('mpb_post_text')) {
    function mpb_post_text($key, $default = '')
    {
        if (!isset($_POST[$key])) {
            return $default;
        }

        return is_scalar($_POST[$key]) ? trim((string) wp_unslash($_POST[$key])) : $default;
    }
}

if (!function_exists('mpb_post_int')) {
    function mpb_post_int($key, $default = 0)
    {
        if (!isset($_POST[$key])) {
            return (int) $default;
        }

        return absint($_POST[$key]);
    }
}

if (!function_exists('mpb_post_bool')) {
    function mpb_post_bool($key, $default = false)
    {
        if (!isset($_POST[$key])) {
            return $default ? '1' : '0';
        }

        return '1';
    }
}

if (!function_exists('mpb_json_encode')) {
    function mpb_json_encode($value)
    {
        return wp_json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}

if (!function_exists('mpb_save_meta_box')) {
    function mpb_save_meta_box($post_id)
    {
        if (!isset($_POST['mpb_meta_box_nonce']) || !wp_verify_nonce($_POST['mpb_meta_box_nonce'], 'mpb_save_meta_box')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_page', $post_id)) {
            return;
        }

        if (!mpb_is_target_template($post_id)) {
            return;
        }

        $text_fields = array(
            '_mpb_layout_type',
            '_mpb_hero_eyebrow',
            '_mpb_hero_title',
            '_mpb_hero_lead',
            '_mpb_hero_btn1_text',
            '_mpb_hero_btn2_text',
            '_mpb_intro_title',
            '_mpb_intro_text',
            '_mpb_faq_title',
            '_mpb_faq_text',
            '_mpb_cta_title',
            '_mpb_cta_text',
            '_mpb_cta_btn1_text',
            '_mpb_cta_btn2_text',
            '_mpb_hero_gallery_ids',
        );

        $url_fields = array(
            '_mpb_hero_btn1_url',
            '_mpb_hero_btn2_url',
            '_mpb_cta_btn1_url',
            '_mpb_cta_btn2_url',
        );

        $int_fields = array(
            '_mpb_hero_image_id',
            '_mpb_intro_image_id',
            '_mpb_cta_image_id',
        );

        foreach ($text_fields as $key) {
            update_post_meta($post_id, $key, sanitize_textarea_field(mpb_post_text($key, '')));
        }

        foreach ($url_fields as $key) {
            update_post_meta($post_id, $key, esc_url_raw(mpb_post_text($key, '')));
        }

        foreach ($int_fields as $key) {
            update_post_meta($post_id, $key, mpb_post_int($key, 0));
        }

        update_post_meta($post_id, '_mpb_show_hero', mpb_post_bool('_mpb_show_hero', true));
        update_post_meta($post_id, '_mpb_show_intro', mpb_post_bool('_mpb_show_intro', true));
        update_post_meta($post_id, '_mpb_show_footer_nav', mpb_post_bool('_mpb_show_footer_nav', true));

        $feature_items = array();

        for ($i = 0; $i < 6; $i++) {
            $item = array(
                'label'    => mpb_post_text("mpb_feature_{$i}_label", ''),
                'title'    => mpb_post_text("mpb_feature_{$i}_title", ''),
                'text'     => mpb_post_text("mpb_feature_{$i}_text", ''),
                'image_id' => mpb_post_int("mpb_feature_{$i}_image_id", 0),
            );

            $btn1_text = mpb_post_text("mpb_feature_{$i}_btn1_text", '');
            $btn1_url  = mpb_post_text("mpb_feature_{$i}_btn1_url", '');
            $btn2_text = mpb_post_text("mpb_feature_{$i}_btn2_text", '');
            $btn2_url  = mpb_post_text("mpb_feature_{$i}_btn2_url", '');

            if ($btn1_text !== '' || $btn1_url !== '') {
                $item['btn1'] = array(
                    'text' => $btn1_text,
                    'url'  => $btn1_url,
                );
            }

            if ($btn2_text !== '' || $btn2_url !== '') {
                $item['btn2'] = array(
                    'text' => $btn2_text,
                    'url'  => $btn2_url,
                );
            }

            if ($item['label'] !== '' || $item['title'] !== '' || $item['text'] !== '' || !empty($item['image_id']) || !empty($item['btn1']) || !empty($item['btn2'])) {
                $feature_items[] = $item;
            }
        }

        update_post_meta($post_id, '_mpb_feature_items_json', mpb_json_encode($feature_items));

        $guide_items = array();

        for ($i = 0; $i < 6; $i++) {
            $item = array(
                'title'    => mpb_post_text("mpb_guide_{$i}_title", ''),
                'text'     => mpb_post_text("mpb_guide_{$i}_text", ''),
                'image_id' => mpb_post_int("mpb_guide_{$i}_image_id", 0),
            );

            if ($item['title'] !== '' || $item['text'] !== '' || !empty($item['image_id'])) {
                $guide_items[] = $item;
            }
        }

        update_post_meta($post_id, '_mpb_guide_items_json', mpb_json_encode($guide_items));

        $flow_items = array();

        for ($i = 0; $i < 8; $i++) {
            $item = array(
                'title' => mpb_post_text("mpb_flow_{$i}_title", ''),
                'text'  => mpb_post_text("mpb_flow_{$i}_text", ''),
            );

            if ($item['title'] !== '' || $item['text'] !== '') {
                $flow_items[] = $item;
            }
        }

        update_post_meta($post_id, '_mpb_flow_items_json', mpb_json_encode($flow_items));

        $faq_items = array();

        for ($i = 0; $i < 12; $i++) {
            $item = array(
                'q' => mpb_post_text("mpb_faq_{$i}_q", ''),
                'a' => mpb_post_text("mpb_faq_{$i}_a", ''),
            );

            if ($item['q'] !== '' || $item['a'] !== '') {
                $faq_items[] = $item;
            }
        }

        update_post_meta($post_id, '_mpb_faq_items_json', mpb_json_encode($faq_items));

        $compare_columns = array();

        for ($i = 0; $i < 3; $i++) {
            $col = array(
                'title'    => mpb_post_text("mpb_compare_col_{$i}_title", ''),
                'image_id' => mpb_post_int("mpb_compare_col_{$i}_image_id", 0),
            );

            if ($col['title'] !== '' || !empty($col['image_id'])) {
                $compare_columns[] = $col;
            }
        }

        $compare_rows = array();

        for ($i = 0; $i < 8; $i++) {
            $row = array(
                'label' => mpb_post_text("mpb_compare_row_{$i}_label", ''),
                'cells' => array(
                    mpb_post_text("mpb_compare_row_{$i}_cell_0", ''),
                    mpb_post_text("mpb_compare_row_{$i}_cell_1", ''),
                    mpb_post_text("mpb_compare_row_{$i}_cell_2", ''),
                ),
            );

            $has_text = false;

            foreach ($row['cells'] as $cell_text) {
                if ($cell_text !== '') {
                    $has_text = true;
                    break;
                }
            }

            if ($row['label'] !== '' || $has_text) {
                $compare_rows[] = $row;
            }
        }

        update_post_meta($post_id, '_mpb_compare_table_json', mpb_json_encode(array(
            'columns' => $compare_columns,
            'rows'    => $compare_rows,
        )));
    }
    add_action('save_post_page', 'mpb_save_meta_box');
}
