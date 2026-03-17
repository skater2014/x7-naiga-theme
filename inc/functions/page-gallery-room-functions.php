<?php

if (!defined('ABSPATH')) {
    exit;
}

error_log('✅ function-page.php が読み込まれました！');

/* =========================================================
 * 共通判定
 * ========================================================= */

/**
 * Room Gallery テンプレート判定
 *
 * @param WP_Post|int|null $post
 * @return bool
 */
function rg_is_room_gallery_page($post = null)
{
    if (is_numeric($post)) {
        $post = get_post((int) $post);
    } elseif (!$post) {
        $post = get_post();
    }

    return $post
        && $post->post_type === 'page'
        && get_page_template_slug($post->ID) === 'page-gallery-room.php';
}

/**
 * カンマ区切り画像IDを正規化
 *
 * @param string|array $raw
 * @param int $limit
 * @return string
 */
function rg_sanitize_csv_ids($raw, $limit = 20)
{
    if (is_array($raw)) {
        $parts = $raw;
    } else {
        $parts = explode(',', (string) $raw);
    }

    $ids = array();

    foreach ($parts as $part) {
        $id = absint($part);
        if ($id > 0) {
            $ids[] = $id;
        }
    }

    $ids = array_values(array_unique($ids));

    if ($limit > 0) {
        $ids = array_slice($ids, 0, $limit);
    }

    return implode(',', $ids);
}

/**
 * 単行テキスト整形
 *
 * @param mixed $value
 * @return string
 */
function rg_clean_text($value)
{
    return sanitize_text_field((string) $value);
}

/**
 * 複数行テキスト整形
 *
 * @param mixed $value
 * @return string
 */
function rg_clean_textarea($value)
{
    return sanitize_textarea_field((string) $value);
}

/* =========================================================
 * 管理画面アセット
 * ========================================================= */

/**
 * 固定ページ編集画面だけ media uploader を有効化
 *
 * @param string $hook
 * @return void
 */
function rg_admin_enqueue_assets($hook)
{
    if (!in_array($hook, array('post.php', 'post-new.php'), true)) {
        return;
    }

    $screen = get_current_screen();
    if (!$screen || $screen->post_type !== 'page') {
        return;
    }

    wp_enqueue_media();
}
add_action('admin_enqueue_scripts', 'rg_admin_enqueue_assets');

/* =========================================================
 * メタボックス登録
 * ========================================================= */

/**
 * Room Gallery 用メタボックス登録
 *
 * @return void
 */
function rg_register_room_gallery_metaboxes()
{
    add_meta_box(
        'rg_room_gallery_slider_images',
        'ヒーロースライダー画像（最大20枚）',
        'rg_render_slider_images_metabox',
        'page',
        'normal',
        'high'
    );

    add_meta_box(
        'rg_room_gallery_gallery_images',
        '下部ギャラリー画像（最大20枚）',
        'rg_render_gallery_images_metabox',
        'page',
        'normal',
        'high'
    );

    add_meta_box(
        'rg_room_gallery_video',
        '動画設定（MP4 / MP3 / YouTube）',
        'rg_render_video_metabox',
        'page',
        'normal',
        'high'
    );

    add_meta_box(
        'rg_room_gallery_hero_text',
        'ヒーロー文言設定',
        'rg_render_hero_text_metabox',
        'page',
        'side',
        'default'
    );

    add_meta_box(
        'rg_room_gallery_section_text',
        'セクション文言設定',
        'rg_render_section_text_metabox',
        'page',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes_page', 'rg_register_room_gallery_metaboxes');

/* =========================================================
 * Room Gallery テンプレート以外では非表示
 * ========================================================= */

/**
 * page-gallery-room.php 以外では対象メタボックスを外す
 *
 * @return void
 */
function rg_filter_room_gallery_metaboxes()
{
    $post = get_post();

    if (rg_is_room_gallery_page($post)) {
        return;
    }

    $ids = array(
        'rg_room_gallery_slider_images',
        'rg_room_gallery_gallery_images',
        'rg_room_gallery_video',
        'rg_room_gallery_hero_text',
        'rg_room_gallery_section_text',
    );

    foreach ($ids as $id) {
        remove_meta_box($id, 'page', 'normal');
        remove_meta_box($id, 'page', 'side');
    }
}
add_action('do_meta_boxes', 'rg_filter_room_gallery_metaboxes', 99);

/* =========================================================
 * 1. ヒーロースライダー画像
 * ========================================================= */

/**
 * ヒーロースライダー画像メタボックス
 *
 * @param WP_Post $post
 * @return void
 */
function rg_render_slider_images_metabox($post)
{
    wp_nonce_field('rg_room_gallery_save', 'rg_room_gallery_nonce');

    echo '<p>上部 Hero Slider 用の画像です。最大20枚まで設定できます。</p>';
    echo '<div class="rg-slider-image-list">';

    for ($i = 1; $i <= 20; $i++) {
        $image_id  = absint(get_post_meta($post->ID, "slider_image_{$i}", true));
        $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'medium') : '';

        echo '<div class="rg-slider-image-row" style="margin-bottom:16px;padding:12px;border:1px solid #ddd;">';
        echo '<p style="margin:0 0 8px;"><strong>画像' . esc_html($i) . '</strong></p>';
        echo '<input type="hidden" name="slider_image_' . esc_attr($i) . '" id="slider_image_' . esc_attr($i) . '" value="' . esc_attr($image_id) . '">';
        echo '<img id="slider_preview_' . esc_attr($i) . '" src="' . esc_url($image_url) . '" style="max-width:220px;display:' . ($image_url ? 'block' : 'none') . ';margin-bottom:8px;">';
        echo '<button type="button" class="button rg-select-single-image" data-target="' . esc_attr($i) . '">画像を選択</button> ';
        echo '<button type="button" class="button rg-remove-single-image" data-target="' . esc_attr($i) . '">削除</button>';
        echo '</div>';
    }

    echo '</div>';
?>
    <script>
        jQuery(function($) {
            $(document).off('click.rgSelectSingle').on('click.rgSelectSingle', '.rg-select-single-image', function(e) {
                e.preventDefault();

                const target = $(this).data('target');
                const frame = wp.media({
                    title: '画像を選択',
                    button: {
                        text: 'この画像を使用'
                    },
                    library: {
                        type: 'image'
                    },
                    multiple: false
                });

                frame.on('select', function() {
                    const attachment = frame.state().get('selection').first().toJSON();
                    $('#slider_image_' + target).val(attachment.id);
                    $('#slider_preview_' + target).attr('src', attachment.url).show();
                });

                frame.open();
            });

            $(document).off('click.rgRemoveSingle').on('click.rgRemoveSingle', '.rg-remove-single-image', function(e) {
                e.preventDefault();

                const target = $(this).data('target');
                $('#slider_image_' + target).val('');
                $('#slider_preview_' + target).attr('src', '').hide();
            });
        });
    </script>
<?php
}

/* =========================================================
 * 2. 下部ギャラリー画像
 * ========================================================= */

/**
 * 下部ギャラリー画像メタボックス
 *
 * @param WP_Post $post
 * @return void
 */
function rg_render_gallery_images_metabox($post)
{
    wp_nonce_field('rg_room_gallery_save', 'rg_room_gallery_nonce');

    $gallery_images = get_post_meta($post->ID, 'room_gallery_images', true);
    $gallery_images = $gallery_images ? explode(',', (string) $gallery_images) : array();
    $gallery_images = array_slice(array_values(array_filter(array_map('absint', $gallery_images))), 0, 20);

?>
    <p>下部カルーセル用の画像です。最大20枚まで設定できます。</p>

    <div id="rg-gallery-container">
        <ul id="rg-gallery-list" style="display:flex;flex-wrap:wrap;gap:12px;padding:0;margin:12px 0;list-style:none;">
            <?php foreach ($gallery_images as $image_id) : ?>
                <?php $img_url = wp_get_attachment_image_url($image_id, 'thumbnail'); ?>
                <?php if (!$img_url) continue; ?>
                <li data-id="<?php echo esc_attr($image_id); ?>" style="width:120px;">
                    <img src="<?php echo esc_url($img_url); ?>" style="display:block;width:100%;height:auto;margin-bottom:6px;">
                    <button type="button" class="button-link-delete rg-remove-gallery-image">削除</button>
                </li>
            <?php endforeach; ?>
        </ul>

        <input
            type="hidden"
            id="room_gallery_images"
            name="room_gallery_images"
            value="<?php echo esc_attr(implode(',', $gallery_images)); ?>">

        <button type="button" id="rg-add-gallery-images" class="button">画像を追加</button>
    </div>

    <script>
        jQuery(function($) {
            $(document).off('click.rgAddGallery').on('click.rgAddGallery', '#rg-add-gallery-images', function(e) {
                e.preventDefault();

                const frame = wp.media({
                    title: 'ギャラリー画像を選択',
                    button: {
                        text: '画像を追加'
                    },
                    library: {
                        type: 'image'
                    },
                    multiple: true
                });

                frame.on('select', function() {
                    let ids = $('#room_gallery_images').val() ? $('#room_gallery_images').val().split(',') : [];
                    ids = ids.filter(Boolean);

                    frame.state().get('selection').each(function(item) {
                        const attachment = item.toJSON();
                        const id = String(attachment.id);

                        if (ids.indexOf(id) !== -1) {
                            return;
                        }

                        if (ids.length >= 20) {
                            return;
                        }

                        ids.push(id);

                        const thumbUrl = attachment.sizes && attachment.sizes.thumbnail ?
                            attachment.sizes.thumbnail.url :
                            attachment.url;

                        $('#rg-gallery-list').append(
                            '<li data-id="' + id + '" style="width:120px;">' +
                            '<img src="' + thumbUrl + '" style="display:block;width:100%;height:auto;margin-bottom:6px;">' +
                            '<button type="button" class="button-link-delete rg-remove-gallery-image">削除</button>' +
                            '</li>'
                        );
                    });

                    $('#room_gallery_images').val(ids.join(','));
                });

                frame.open();
            });

            $(document).off('click.rgRemoveGallery').on('click.rgRemoveGallery', '.rg-remove-gallery-image', function(e) {
                e.preventDefault();

                const $li = $(this).closest('li');
                const id = String($li.data('id'));
                $li.remove();

                let ids = $('#room_gallery_images').val() ? $('#room_gallery_images').val().split(',') : [];
                ids = ids.filter(function(item) {
                    return item && item !== id;
                });

                $('#room_gallery_images').val(ids.join(','));
            });
        });
    </script>
<?php
}

/* =========================================================
 * 3. 動画設定
 * ========================================================= */

/**
 * 動画設定メタボックス
 *
 * @param WP_Post $post
 * @return void
 */
function rg_render_video_metabox($post)
{
    wp_nonce_field('rg_room_gallery_save', 'rg_room_gallery_nonce');

    $media_url  = get_post_meta($post->ID, '_media_url', true);
    $media_file = get_post_meta($post->ID, '_media_file', true);

?>
    <p><strong>MP4 / MP3</strong></p>
    <p>外部URLを入れるか、メディアアップローダーで動画・音声ファイルを選択してください。</p>

    <p>
        <label for="media_url">外部URL</label><br>
        <input type="text" name="media_url" id="media_url" value="<?php echo esc_attr($media_url); ?>" class="widefat" placeholder="https://example.com/video.mp4">
    </p>

    <p>
        <label for="media_file">アップロードファイルURL</label><br>
        <input type="text" name="media_file" id="media_file" value="<?php echo esc_attr($media_file); ?>" class="widefat" readonly>
    </p>

    <p>
        <button type="button" class="button" id="rg-media-upload-button">動画 / 音声を選択</button>
        <button type="button" class="button" id="rg-media-clear-button">クリア</button>
    </p>

    <hr>

    <p><strong>YouTube 動画ID（最大5件）</strong></p>
    <?php for ($i = 1; $i <= 5; $i++) : ?>
        <?php $youtube_id = get_post_meta($post->ID, "_youtube_id_{$i}", true); ?>
        <p>
            <label for="youtube_id_<?php echo esc_attr($i); ?>">YouTube ID <?php echo esc_html($i); ?></label><br>
            <input type="text" name="youtube_id_<?php echo esc_attr($i); ?>" id="youtube_id_<?php echo esc_attr($i); ?>" value="<?php echo esc_attr($youtube_id); ?>" class="widefat" placeholder="YouTube ID">
        </p>
    <?php endfor; ?>

    <script>
        jQuery(function($) {
            $(document).off('click.rgMediaUpload').on('click.rgMediaUpload', '#rg-media-upload-button', function(e) {
                e.preventDefault();

                const frame = wp.media({
                    title: '動画または音声を選択',
                    button: {
                        text: 'このファイルを使用'
                    },
                    library: {
                        type: ['video', 'audio']
                    },
                    multiple: false
                });

                frame.on('select', function() {
                    const attachment = frame.state().get('selection').first().toJSON();
                    $('#media_file').val(attachment.url);
                });

                frame.open();
            });

            $(document).off('click.rgMediaClear').on('click.rgMediaClear', '#rg-media-clear-button', function(e) {
                e.preventDefault();
                $('#media_file').val('');
            });
        });
    </script>
<?php
}

/* =========================================================
 * 4. ヒーロー文言
 * ========================================================= */

/**
 * ヒーロー文言メタボックス
 *
 * @param WP_Post $post
 * @return void
 */
function rg_render_hero_text_metabox($post)
{
    wp_nonce_field('rg_room_gallery_save', 'rg_room_gallery_nonce');

    $eyebrow = get_post_meta($post->ID, '_rg_hero_eyebrow', true);
    $lead    = get_post_meta($post->ID, '_rg_hero_lead', true);
    $text    = get_post_meta($post->ID, '_rg_hero_text', true);

?>
    <p>
        <label for="rg_hero_eyebrow"><strong>上部小見出し</strong></label>
        <input type="text" id="rg_hero_eyebrow" name="rg_hero_eyebrow" value="<?php echo esc_attr($eyebrow); ?>" class="widefat" placeholder="NASU ROOM STYLE">
    </p>

    <p>
        <label for="rg_hero_lead"><strong>リード文</strong></label>
        <textarea id="rg_hero_lead" name="rg_hero_lead" rows="3" class="widefat" placeholder="東京と那須、二つの魅力を取り入れた暮らしを実現したい方へ。"><?php echo esc_textarea($lead); ?></textarea>
    </p>

    <p>
        <label for="rg_hero_text"><strong>説明文</strong></label>
        <textarea id="rg_hero_text" name="rg_hero_text" rows="4" class="widefat" placeholder="二拠点生活・移住・別荘購入を検討する方に向けて、理想の住まいと暮らし方をご提案します。"><?php echo esc_textarea($text); ?></textarea>
    </p>
<?php
}

/* =========================================================
 * 5. セクション文言
 * ========================================================= */

/**
 * セクション文言メタボックス
 *
 * @param WP_Post $post
 * @return void
 */
function rg_render_section_text_metabox($post)
{
    wp_nonce_field('rg_room_gallery_save', 'rg_room_gallery_nonce');

    $fields = array(
        'rg_media_title'   => 'MP4 / MP3 セクション見出し',
        'rg_media_lead'    => 'MP4 / MP3 セクション説明',
        'rg_youtube_title' => 'YouTube セクション見出し',
        'rg_youtube_lead'  => 'YouTube セクション説明',
        'rg_related_title' => '関連セクション見出し',
        'rg_related_lead'  => '関連セクション説明',
        'rg_gallery_title' => '下部ギャラリー見出し',
        'rg_gallery_lead'  => '下部ギャラリー説明',
        'rg_cta_text'      => 'CTA テキスト',
        'rg_cta_email'     => 'CTA メールアドレス',
    );

    foreach ($fields as $key => $label) {
        $value = get_post_meta($post->ID, '_' . $key, true);

        echo '<p>';
        echo '<label for="' . esc_attr($key) . '"><strong>' . esc_html($label) . '</strong></label>';

        if ($key === 'rg_cta_email') {
            echo '<input type="email" id="' . esc_attr($key) . '" name="' . esc_attr($key) . '" value="' . esc_attr($value) . '" class="widefat" placeholder="contact@naigaicorp.net">';
        } elseif (strpos($key, '_title') !== false) {
            echo '<input type="text" id="' . esc_attr($key) . '" name="' . esc_attr($key) . '" value="' . esc_attr($value) . '" class="widefat">';
        } else {
            echo '<textarea id="' . esc_attr($key) . '" name="' . esc_attr($key) . '" rows="3" class="widefat">' . esc_textarea($value) . '</textarea>';
        }

        echo '</p>';
    }
}

/* =========================================================
 * 一括保存
 * ========================================================= */

/**
 * Room Gallery 用メタを一括保存
 *
 * @param int $post_id
 * @return void
 */
function rg_save_room_gallery_metaboxes($post_id)
{
    if (!isset($_POST['rg_room_gallery_nonce'])) {
        return;
    }

    if (!wp_verify_nonce($_POST['rg_room_gallery_nonce'], 'rg_room_gallery_save')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (!rg_is_room_gallery_page($post_id)) {
        return;
    }

    /* ---------- ヒーロースライダー画像 ---------- */
    for ($i = 1; $i <= 20; $i++) {
        $key = "slider_image_{$i}";
        $val = isset($_POST[$key]) ? absint($_POST[$key]) : 0;

        if ($val > 0) {
            update_post_meta($post_id, $key, $val);
        } else {
            delete_post_meta($post_id, $key);
        }
    }

    /* ---------- 下部ギャラリー ---------- */
    if (isset($_POST['room_gallery_images'])) {
        $gallery_ids = rg_sanitize_csv_ids($_POST['room_gallery_images'], 20);

        if ($gallery_ids !== '') {
            update_post_meta($post_id, 'room_gallery_images', $gallery_ids);
        } else {
            delete_post_meta($post_id, 'room_gallery_images');
        }
    }

    /* ---------- 動画URL ---------- */
    if (isset($_POST['media_url'])) {
        $media_url = esc_url_raw((string) $_POST['media_url']);
        if ($media_url !== '') {
            update_post_meta($post_id, '_media_url', $media_url);
        } else {
            delete_post_meta($post_id, '_media_url');
        }
    }

    if (isset($_POST['media_file'])) {
        $media_file = esc_url_raw((string) $_POST['media_file']);
        if ($media_file !== '') {
            update_post_meta($post_id, '_media_file', $media_file);
        } else {
            delete_post_meta($post_id, '_media_file');
        }
    }

    /* ---------- YouTube ID ---------- */
    for ($i = 1; $i <= 5; $i++) {
        $field = "youtube_id_{$i}";
        $raw   = isset($_POST[$field]) ? (string) $_POST[$field] : '';
        $val   = preg_replace('/[^A-Za-z0-9_-]/', '', $raw);

        if ($val !== '') {
            update_post_meta($post_id, "_youtube_id_{$i}", $val);
        } else {
            delete_post_meta($post_id, "_youtube_id_{$i}");
        }
    }

    /* ---------- ヒーロー文言 ---------- */
    $hero_fields = array(
        '_rg_hero_eyebrow' => isset($_POST['rg_hero_eyebrow']) ? rg_clean_text($_POST['rg_hero_eyebrow']) : '',
        '_rg_hero_lead'    => isset($_POST['rg_hero_lead']) ? rg_clean_textarea($_POST['rg_hero_lead']) : '',
        '_rg_hero_text'    => isset($_POST['rg_hero_text']) ? rg_clean_textarea($_POST['rg_hero_text']) : '',
    );

    foreach ($hero_fields as $meta_key => $meta_value) {
        if ($meta_value !== '') {
            update_post_meta($post_id, $meta_key, $meta_value);
        } else {
            delete_post_meta($post_id, $meta_key);
        }
    }

    /* ---------- セクション文言 ---------- */
    $section_fields = array(
        '_rg_media_title'   => isset($_POST['rg_media_title']) ? rg_clean_text($_POST['rg_media_title']) : '',
        '_rg_media_lead'    => isset($_POST['rg_media_lead']) ? rg_clean_textarea($_POST['rg_media_lead']) : '',
        '_rg_youtube_title' => isset($_POST['rg_youtube_title']) ? rg_clean_text($_POST['rg_youtube_title']) : '',
        '_rg_youtube_lead'  => isset($_POST['rg_youtube_lead']) ? rg_clean_textarea($_POST['rg_youtube_lead']) : '',
        '_rg_related_title' => isset($_POST['rg_related_title']) ? rg_clean_text($_POST['rg_related_title']) : '',
        '_rg_related_lead'  => isset($_POST['rg_related_lead']) ? rg_clean_textarea($_POST['rg_related_lead']) : '',
        '_rg_gallery_title' => isset($_POST['rg_gallery_title']) ? rg_clean_text($_POST['rg_gallery_title']) : '',
        '_rg_gallery_lead'  => isset($_POST['rg_gallery_lead']) ? rg_clean_textarea($_POST['rg_gallery_lead']) : '',
        '_rg_cta_text'      => isset($_POST['rg_cta_text']) ? rg_clean_textarea($_POST['rg_cta_text']) : '',
        '_rg_cta_email'     => isset($_POST['rg_cta_email']) ? sanitize_email($_POST['rg_cta_email']) : '',
    );

    foreach ($section_fields as $meta_key => $meta_value) {
        if ($meta_value !== '') {
            update_post_meta($post_id, $meta_key, $meta_value);
        } else {
            delete_post_meta($post_id, $meta_key);
        }
    }
}
add_action('save_post_page', 'rg_save_room_gallery_metaboxes');
