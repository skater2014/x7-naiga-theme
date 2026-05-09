<?php
/**
 * =========================================================
 * 民泊運営サポートLP 専用メタボックス
 * =========================================================
 *
 * 対象:
 * - page-minpaku-support.php を選んだ固定ページ
 * - 既存運用中の /minpaku, /minpaku-support 固定ページ
 *
 * 方針:
 * - 管理メニューは追加しない
 * - 他ページには出さない
 * - page-minpaku-support.php が読んでいる _mps_* キーだけ編集する
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('naigai_mps_admin_is_target_page')) {
    function naigai_mps_admin_is_target_page($post_id)
    {
        $post_id = absint($post_id);

        if (!$post_id || get_post_type($post_id) !== 'page') {
            return false;
        }

        $template = (string) get_page_template_slug($post_id);
        $slug     = (string) get_post_field('post_name', $post_id);

        return (
            $template === 'page-minpaku-support.php'
            || $slug === 'minpaku'
            || $slug === 'minpaku-support'
        );
    }
}

if (!function_exists('naigai_mps_admin_fields')) {
    function naigai_mps_admin_fields()
    {
        return array(
            'hero' => array(
                'title'  => 'Hero',
                'fields' => array(
                    '_mps_hero_eyebrow'        => array('label' => 'Hero 小見出し', 'type' => 'text'),
                    '_mps_hero_title'          => array('label' => 'Hero タイトル', 'type' => 'text'),
                    '_mps_hero_text'           => array('label' => 'Hero 本文', 'type' => 'textarea'),
                    '_mps_hero_primary_text'   => array('label' => 'Hero ボタン1 テキスト', 'type' => 'text'),
                    '_mps_hero_primary_url'    => array('label' => 'Hero ボタン1 URL', 'type' => 'url'),
                    '_mps_hero_secondary_text' => array('label' => 'Hero ボタン2 テキスト', 'type' => 'text'),
                    '_mps_hero_secondary_url'  => array('label' => 'Hero ボタン2 URL', 'type' => 'url'),
                    '_mps_hero_image_pc_id'    => array('label' => 'Hero PC画像', 'type' => 'image'),
                    '_mps_hero_image_sp_id'    => array('label' => 'Hero SP画像', 'type' => 'image'),
                    '_mps_hero_image_id'       => array('label' => 'Hero 共通画像 fallback', 'type' => 'image'),
                ),
            ),

            'concept' => array(
                'title'  => 'Concept',
                'fields' => array(
                    '_mps_concept_eyebrow'  => array('label' => 'Concept 小見出し', 'type' => 'text'),
                    '_mps_concept_title'    => array('label' => 'Concept タイトル', 'type' => 'text'),
                    '_mps_concept_text'     => array('label' => 'Concept 本文', 'type' => 'textarea'),
                    '_mps_concept_image_id' => array('label' => 'Concept 画像', 'type' => 'image'),
                ),
            ),

            'operation' => array(
                'title'  => 'Operation',
                'fields' => array(
                    '_mps_operation_eyebrow'  => array('label' => 'Operation 小見出し', 'type' => 'text'),
                    '_mps_operation_title'    => array('label' => 'Operation タイトル', 'type' => 'text'),
                    '_mps_operation_text'     => array('label' => 'Operation 本文', 'type' => 'textarea'),
                    '_mps_operation_image_id' => array('label' => 'Operation 画像', 'type' => 'image'),
                ),
            ),

            'detail' => array(
                'title'  => 'Stay detail',
                'fields' => array(
                    '_mps_detail_eyebrow'        => array('label' => 'Detail 小見出し', 'type' => 'text'),
                    '_mps_detail_title'          => array('label' => 'Detail タイトル', 'type' => 'text'),
                    '_mps_detail_text'           => array('label' => 'Detail 本文', 'type' => 'textarea'),
                    '_mps_detail_image_id'       => array('label' => 'Detail 画像', 'type' => 'image'),
                    '_mps_detail_primary_text'   => array('label' => 'Detail ボタン1 テキスト', 'type' => 'text'),
                    '_mps_detail_primary_url'    => array('label' => 'Detail ボタン1 URL', 'type' => 'url'),
                    '_mps_detail_secondary_text' => array('label' => 'Detail ボタン2 テキスト', 'type' => 'text'),
                    '_mps_detail_secondary_url'  => array('label' => 'Detail ボタン2 URL', 'type' => 'url'),
                ),
            ),

            'support' => array(
                'title'  => 'Support',
                'fields' => array(
                    '_mps_support_eyebrow'        => array('label' => 'Support 小見出し', 'type' => 'text'),
                    '_mps_support_title'          => array('label' => 'Support タイトル', 'type' => 'text'),
                    '_mps_support_text'           => array('label' => 'Support 本文', 'type' => 'textarea'),
                    '_mps_support_image_id'       => array('label' => 'Support 画像', 'type' => 'image'),
                    '_mps_support_primary_text'   => array('label' => 'Support ボタン1 テキスト', 'type' => 'text'),
                    '_mps_support_primary_url'    => array('label' => 'Support ボタン1 URL', 'type' => 'url'),
                    '_mps_support_secondary_text' => array('label' => 'Support ボタン2 テキスト', 'type' => 'text'),
                    '_mps_support_secondary_url'  => array('label' => 'Support ボタン2 URL', 'type' => 'url'),
                ),
            ),

            'flow' => array(
                'title'  => 'Flow',
                'fields' => array(
                    '_mps_flow_eyebrow' => array('label' => 'Flow 小見出し', 'type' => 'text'),
                    '_mps_flow_title'   => array('label' => 'Flow タイトル', 'type' => 'text'),
                    '_mps_flow_step_1'  => array('label' => 'STEP 1', 'type' => 'text'),
                    '_mps_flow_step_2'  => array('label' => 'STEP 2', 'type' => 'text'),
                    '_mps_flow_step_3'  => array('label' => 'STEP 3', 'type' => 'text'),
                    '_mps_flow_step_4'  => array('label' => 'STEP 4', 'type' => 'text'),
                    '_mps_flow_step_5'  => array('label' => 'STEP 5', 'type' => 'text'),
                ),
            ),

            'contact' => array(
                'title'  => 'Contact',
                'fields' => array(
                    '_mps_contact_eyebrow'   => array('label' => 'Contact 小見出し', 'type' => 'text'),
                    '_mps_contact_title'     => array('label' => 'Contact タイトル', 'type' => 'text'),
                    '_mps_contact_text'      => array('label' => 'Contact 本文', 'type' => 'textarea'),
                    '_mps_contact_cta_text'  => array('label' => 'Contact ボタン テキスト', 'type' => 'text'),
                    '_mps_contact_cta_url'   => array('label' => 'Contact ボタン URL', 'type' => 'url'),
                    '_mps_contact_image_id'  => array('label' => 'Contact 背景画像', 'type' => 'image'),
                ),
            ),
        );
    }
}

add_action('add_meta_boxes_page', function ($post) {
    if (!($post instanceof WP_Post)) {
        return;
    }

    if (!naigai_mps_admin_is_target_page($post->ID)) {
        return;
    }

    add_meta_box(
        'naigai-mps-support-settings',
        '民泊運営サポートLP 設定',
        'naigai_mps_admin_render_metabox',
        'page',
        'normal',
        'high'
    );
});

if (!function_exists('naigai_mps_admin_render_metabox')) {
    function naigai_mps_admin_render_metabox($post)
    {
        wp_nonce_field('naigai_mps_admin_save', 'naigai_mps_admin_nonce');

        echo '<style>
            .mps-admin-box { display:grid; gap:18px; }
            .mps-admin-section { border:1px solid #dcdcde; border-radius:10px; background:#fff; padding:16px; }
            .mps-admin-section h3 { margin:0 0 14px; font-size:16px; }
            .mps-admin-grid { display:grid; grid-template-columns: 1fr 1fr; gap:14px; }
            .mps-admin-field.is-wide { grid-column:1 / -1; }
            .mps-admin-field label { display:block; margin-bottom:6px; font-weight:700; }
            .mps-admin-field input[type="text"],
            .mps-admin-field input[type="url"],
            .mps-admin-field textarea { width:100%; }
            .mps-admin-field textarea { min-height:90px; }
            .mps-image-field { display:grid; gap:8px; }
            .mps-image-preview img { display:block; max-width:220px; height:auto; border-radius:8px; border:1px solid #dcdcde; }
            @media (max-width: 782px) { .mps-admin-grid { grid-template-columns:1fr; } }
        </style>';

        echo '<div class="mps-admin-box">';

        foreach (naigai_mps_admin_fields() as $section) {
            echo '<section class="mps-admin-section">';
            echo '<h3>' . esc_html($section['title']) . '</h3>';
            echo '<div class="mps-admin-grid">';

            foreach ($section['fields'] as $key => $field) {
                naigai_mps_admin_render_field($post->ID, $key, $field);
            }

            echo '</div>';
            echo '</section>';
        }

        echo '</div>';
    }
}

if (!function_exists('naigai_mps_admin_render_field')) {
    function naigai_mps_admin_render_field($post_id, $key, $field)
    {
        $type  = $field['type'] ?? 'text';
        $label = $field['label'] ?? $key;
        $value = get_post_meta($post_id, $key, true);

        $wide_class = ($type === 'textarea' || $type === 'image') ? ' is-wide' : '';

        echo '<div class="mps-admin-field' . esc_attr($wide_class) . '">';
        echo '<label for="' . esc_attr($key) . '">' . esc_html($label) . '</label>';

        if ($type === 'textarea') {
            echo '<textarea id="' . esc_attr($key) . '" name="mps_meta[' . esc_attr($key) . ']">' . esc_textarea($value) . '</textarea>';
        } elseif ($type === 'url') {
            echo '<input type="url" id="' . esc_attr($key) . '" name="mps_meta[' . esc_attr($key) . ']" value="' . esc_attr($value) . '">';
        } elseif ($type === 'image') {
            $image_id  = absint($value);
            $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'medium') : '';

            echo '<div class="mps-image-field">';
            echo '<input class="mps-image-id" type="hidden" id="' . esc_attr($key) . '" name="mps_meta[' . esc_attr($key) . ']" value="' . esc_attr($image_id) . '">';
            echo '<div class="mps-image-preview">';
            if ($image_url) {
                echo '<img src="' . esc_url($image_url) . '" alt="">';
            }
            echo '</div>';
            echo '<p>';
            echo '<button type="button" class="button mps-image-select">画像を選択</button> ';
            echo '<button type="button" class="button mps-image-clear">画像を削除</button>';
            echo '</p>';
            echo '</div>';
        } else {
            echo '<input type="text" id="' . esc_attr($key) . '" name="mps_meta[' . esc_attr($key) . ']" value="' . esc_attr($value) . '">';
        }

        echo '</div>';
    }
}

add_action('admin_enqueue_scripts', function ($hook) {
    if (!in_array($hook, array('post.php', 'post-new.php'), true)) {
        return;
    }

    $screen = get_current_screen();
    if (!$screen || $screen->post_type !== 'page') {
        return;
    }

    $post_id = isset($_GET['post']) ? absint($_GET['post']) : 0;
    if ($post_id && !naigai_mps_admin_is_target_page($post_id)) {
        return;
    }

    wp_enqueue_media();

    wp_add_inline_script('jquery-core', <<<JS
jQuery(function($) {
  $(document).on('click', '.mps-image-select', function(e) {
    e.preventDefault();

    var \$wrap = $(this).closest('.mps-image-field');
    var \$input = \$wrap.find('.mps-image-id');
    var \$preview = \$wrap.find('.mps-image-preview');

    var frame = wp.media({
      title: '画像を選択',
      button: { text: 'この画像を使う' },
      multiple: false
    });

    frame.on('select', function() {
      var attachment = frame.state().get('selection').first().toJSON();
      var url = attachment.url;

      if (attachment.sizes && attachment.sizes.medium) {
        url = attachment.sizes.medium.url;
      }

      \$input.val(attachment.id);
      \$preview.html('<img src="' + url + '" alt="">');
    });

    frame.open();
  });

  $(document).on('click', '.mps-image-clear', function(e) {
    e.preventDefault();

    var \$wrap = $(this).closest('.mps-image-field');
    \$wrap.find('.mps-image-id').val('');
    \$wrap.find('.mps-image-preview').empty();
  });
});
JS);
});

add_action('save_post_page', function ($post_id) {
    $post_id = absint($post_id);

    if (!$post_id) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (wp_is_post_revision($post_id)) {
        return;
    }

    if (!current_user_can('edit_page', $post_id)) {
        return;
    }

    if (
        empty($_POST['naigai_mps_admin_nonce'])
        || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['naigai_mps_admin_nonce'])), 'naigai_mps_admin_save')
    ) {
        return;
    }

    if (!naigai_mps_admin_is_target_page($post_id)) {
        return;
    }

    $posted = isset($_POST['mps_meta']) && is_array($_POST['mps_meta'])
        ? wp_unslash($_POST['mps_meta'])
        : array();

    $fields = array();

    foreach (naigai_mps_admin_fields() as $section) {
        foreach ($section['fields'] as $key => $field) {
            $fields[$key] = $field['type'] ?? 'text';
        }
    }

    foreach ($fields as $key => $type) {
        $raw = $posted[$key] ?? '';

        if ($type === 'image') {
            $value = absint($raw);
        } elseif ($type === 'url') {
            $value = esc_url_raw(trim((string) $raw));
        } elseif ($type === 'textarea') {
            $value = sanitize_textarea_field((string) $raw);
        } else {
            $value = sanitize_text_field((string) $raw);
        }

        if ($value === '' || $value === 0) {
            delete_post_meta($post_id, $key);
        } else {
            update_post_meta($post_id, $key, $value);
        }
    }
});
