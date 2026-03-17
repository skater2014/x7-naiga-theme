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
        'ヒーロースライダー画像 / スライド別文言（最大20枚）',
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
        'ヒーロー共通 fallback 文言',
        'rg_render_hero_text_metabox',
        'page',
        'normal',
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
 * 1. ヒーロースライダー画像 / スライド別文言
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
?>
    <style>
        .rg-slider-image-list {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .rg-slider-image-row {
            display: flex;
            gap: 16px;
            align-items: flex-start;
            padding: 14px;
            border: 1px solid #dcdcde;
            border-radius: 8px;
            background: #fff;
        }

        .rg-slider-image-media {
            flex: 0 0 180px;
            width: 180px;
        }

        .rg-slider-image-preview {
            display: block;
            width: 100%;
            height: auto;
            aspect-ratio: 16 / 10;
            object-fit: cover;
            border: 1px solid #ddd;
            border-radius: 6px;
            background: #f6f7f7;
        }

        .rg-slider-image-empty {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            aspect-ratio: 16 / 10;
            border: 1px dashed #c3c4c7;
            border-radius: 6px;
            background: #f6f7f7;
            color: #646970;
            font-size: 12px;
            text-align: center;
            padding: 10px;
            box-sizing: border-box;
        }

        .rg-slider-image-body {
            flex: 1 1 auto;
            min-width: 0;
        }

        .rg-slider-image-title {
            margin: 0 0 8px;
            font-size: 14px;
            font-weight: 700;
        }

        .rg-slider-image-note {
            margin: 0 0 10px;
            color: #646970;
        }

        .rg-slider-image-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 14px;
        }

        .rg-slider-text-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 10px;
        }

        .rg-slider-field {
            margin: 0;
        }

        .rg-slider-field label {
            display: block;
            margin: 0 0 4px;
            font-weight: 600;
        }

        @media (max-width: 782px) {
            .rg-slider-image-row {
                flex-direction: column;
            }

            .rg-slider-image-media {
                width: 100%;
                max-width: 260px;
            }
        }
    </style>

    <p>上部 Hero Slider 用の画像です。最大20枚まで設定できます。</p>
    <p style="margin:0 0 16px;color:#646970;">
        各スライドごとに文言を設定できます。2枚目以降が未入力なら、1枚目 → 共通 fallback 文言 → ページタイトル の順で補完します。
    </p>

    <div class="rg-slider-image-list">
        <?php for ($i = 1; $i <= 20; $i++) : ?>
            <?php
            $image_id  = absint(get_post_meta($post->ID, "slider_image_{$i}", true));
            $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'medium') : '';

            $eyebrow = get_post_meta($post->ID, "_rg_slide_hero_eyebrow_{$i}", true);
            $title   = get_post_meta($post->ID, "_rg_slide_hero_title_{$i}", true);
            $lead    = get_post_meta($post->ID, "_rg_slide_hero_lead_{$i}", true);
            $text    = get_post_meta($post->ID, "_rg_slide_hero_text_{$i}", true);
            ?>
            <div class="rg-slider-image-row">
                <div class="rg-slider-image-media">
                    <input
                        type="hidden"
                        name="slider_image_<?php echo esc_attr($i); ?>"
                        id="slider_image_<?php echo esc_attr($i); ?>"
                        value="<?php echo esc_attr($image_id); ?>">

                    <?php if ($image_url) : ?>
                        <img
                            id="slider_preview_<?php echo esc_attr($i); ?>"
                            class="rg-slider-image-preview"
                            src="<?php echo esc_url($image_url); ?>"
                            alt="">
                        <div
                            id="slider_empty_<?php echo esc_attr($i); ?>"
                            class="rg-slider-image-empty"
                            style="display:none;">
                            画像未設定
                        </div>
                    <?php else : ?>
                        <img
                            id="slider_preview_<?php echo esc_attr($i); ?>"
                            class="rg-slider-image-preview"
                            src=""
                            alt=""
                            style="display:none;">
                        <div
                            id="slider_empty_<?php echo esc_attr($i); ?>"
                            class="rg-slider-image-empty">
                            画像未設定
                        </div>
                    <?php endif; ?>
                </div>

                <div class="rg-slider-image-body">
                    <p class="rg-slider-image-title">スライド <?php echo esc_html($i); ?></p>
                    <p class="rg-slider-image-note">この順番でヒーロースライダーに表示されます。</p>

                    <div class="rg-slider-image-actions">
                        <button
                            type="button"
                            class="button button-primary rg-select-single-image"
                            data-target="<?php echo esc_attr($i); ?>">
                            画像を選択
                        </button>

                        <button
                            type="button"
                            class="button rg-remove-single-image"
                            data-target="<?php echo esc_attr($i); ?>">
                            削除
                        </button>
                    </div>

                    <div class="rg-slider-text-grid">
                        <p class="rg-slider-field">
                            <label for="rg_slide_hero_eyebrow_<?php echo esc_attr($i); ?>">上部小見出し</label>
                            <input
                                type="text"
                                id="rg_slide_hero_eyebrow_<?php echo esc_attr($i); ?>"
                                name="rg_slide_hero_eyebrow_<?php echo esc_attr($i); ?>"
                                value="<?php echo esc_attr($eyebrow); ?>"
                                class="widefat"
                                placeholder="NASU ROOM STYLE">
                        </p>

                        <p class="rg-slider-field">
                            <label for="rg_slide_hero_title_<?php echo esc_attr($i); ?>">タイトル</label>
                            <input
                                type="text"
                                id="rg_slide_hero_title_<?php echo esc_attr($i); ?>"
                                name="rg_slide_hero_title_<?php echo esc_attr($i); ?>"
                                value="<?php echo esc_attr($title); ?>"
                                class="widefat"
                                placeholder="未入力ならページタイトル">
                        </p>

                        <p class="rg-slider-field">
                            <label for="rg_slide_hero_lead_<?php echo esc_attr($i); ?>">リード文</label>
                            <textarea
                                id="rg_slide_hero_lead_<?php echo esc_attr($i); ?>"
                                name="rg_slide_hero_lead_<?php echo esc_attr($i); ?>"
                                rows="3"
                                class="widefat"
                                placeholder="2枚目以降未入力なら1枚目の値を使用"><?php echo esc_textarea($lead); ?></textarea>
                        </p>

                        <p class="rg-slider-field">
                            <label for="rg_slide_hero_text_<?php echo esc_attr($i); ?>">説明文</label>
                            <textarea
                                id="rg_slide_hero_text_<?php echo esc_attr($i); ?>"
                                name="rg_slide_hero_text_<?php echo esc_attr($i); ?>"
                                rows="4"
                                class="widefat"
                                placeholder="2枚目以降未入力なら1枚目の値を使用"><?php echo esc_textarea($text); ?></textarea>
                        </p>
                    </div>
                </div>
            </div>
        <?php endfor; ?>
    </div>

    <script>
        jQuery(function($) {
            $(document)
                .off('click.rgSelectSingle')
                .on('click.rgSelectSingle', '.rg-select-single-image', function(e) {
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
                        $('#slider_empty_' + target).hide();
                    });

                    frame.open();
                });

            $(document)
                .off('click.rgRemoveSingle')
                .on('click.rgRemoveSingle', '.rg-remove-single-image', function(e) {
                    e.preventDefault();

                    const target = $(this).data('target');
                    $('#slider_image_' + target).val('');
                    $('#slider_preview_' + target).attr('src', '').hide();
                    $('#slider_empty_' + target).show();
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
    <style>
        .rg-gallery-toolbar {
            margin: 12px 0 16px;
        }

        .rg-gallery-list {
            display: flex;
            flex-direction: column;
            gap: 14px;
            margin: 0;
            padding: 0;
        }

        .rg-gallery-card {
            display: flex;
            gap: 16px;
            align-items: flex-start;
            padding: 14px;
            border: 1px solid #dcdcde;
            background: #fff;
            border-radius: 8px;
        }

        .rg-gallery-card-media {
            flex: 0 0 140px;
            width: 140px;
        }

        .rg-gallery-card-media img {
            display: block;
            width: 100%;
            height: auto;
            aspect-ratio: 4 / 3;
            object-fit: cover;
            border-radius: 6px;
            border: 1px solid #ddd;
            background: #f6f7f7;
        }

        .rg-gallery-card-body {
            flex: 1 1 auto;
            min-width: 0;
        }

        .rg-gallery-card-title {
            margin: 0 0 10px;
            font-size: 14px;
            font-weight: 700;
        }

        .rg-gallery-field {
            margin: 0 0 10px;
        }

        .rg-gallery-field:last-child {
            margin-bottom: 0;
        }

        .rg-gallery-field label {
            display: block;
            margin: 0 0 4px;
            font-weight: 600;
        }

        .rg-gallery-actions {
            margin-top: 10px;
        }

        @media (max-width: 782px) {
            .rg-gallery-card {
                flex-direction: column;
            }

            .rg-gallery-card-media {
                width: 100%;
                max-width: 220px;
            }
        }
    </style>

    <p>下部カルーセル用の画像です。最大20枚まで設定できます。</p>

    <div class="rg-gallery-toolbar">
        <button type="button" id="rg-add-gallery-images" class="button button-primary">画像を追加</button>
    </div>

    <input
        type="hidden"
        id="room_gallery_images"
        name="room_gallery_images"
        value="<?php echo esc_attr(implode(',', $gallery_images)); ?>">

    <div id="rg-gallery-list" class="rg-gallery-list">
        <?php foreach ($gallery_images as $index => $image_id) : ?>
            <?php
            $img_url = wp_get_attachment_image_url($image_id, 'medium');
            if (!$img_url) {
                continue;
            }

            $meta_index  = $index + 1;
            $label_value = get_post_meta($post->ID, "_rg_gallery_label_{$meta_index}", true);
            $alt_value   = get_post_meta($post->ID, "_rg_gallery_alt_{$meta_index}", true);
            ?>
            <div class="rg-gallery-card" data-id="<?php echo esc_attr($image_id); ?>">
                <div class="rg-gallery-card-media">
                    <img src="<?php echo esc_url($img_url); ?>" alt="">
                </div>

                <div class="rg-gallery-card-body">
                    <p class="rg-gallery-card-title">画像<?php echo esc_html($meta_index); ?></p>

                    <p class="rg-gallery-field">
                        <label for="rg_gallery_label_<?php echo esc_attr($meta_index); ?>">ラベル</label>
                        <input
                            type="text"
                            id="rg_gallery_label_<?php echo esc_attr($meta_index); ?>"
                            name="rg_gallery_label_<?php echo esc_attr($meta_index); ?>"
                            value="<?php echo esc_attr($label_value); ?>"
                            class="widefat"
                            placeholder="例: living / kitchen / bathroom">
                    </p>

                    <p class="rg-gallery-field">
                        <label for="rg_gallery_alt_<?php echo esc_attr($meta_index); ?>">alt補助文</label>
                        <input
                            type="text"
                            id="rg_gallery_alt_<?php echo esc_attr($meta_index); ?>"
                            name="rg_gallery_alt_<?php echo esc_attr($meta_index); ?>"
                            value="<?php echo esc_attr($alt_value); ?>"
                            class="widefat"
                            placeholder="画像altが無いときの補助文">
                    </p>

                    <div class="rg-gallery-actions">
                        <button type="button" class="button-link-delete rg-remove-gallery-image">削除</button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <script>
        jQuery(function($) {
            function rebuildNames() {
                $('#rg-gallery-list .rg-gallery-card').each(function(index) {
                    const num = index + 1;

                    $(this).find('.rg-gallery-card-title').text('画像' + num);

                    $(this).find('[name^="rg_gallery_label_"]')
                        .attr('name', 'rg_gallery_label_' + num)
                        .attr('id', 'rg_gallery_label_' + num);

                    $(this).find('[name^="rg_gallery_alt_"]')
                        .attr('name', 'rg_gallery_alt_' + num)
                        .attr('id', 'rg_gallery_alt_' + num);

                    $(this).find('label[for^="rg_gallery_label_"]').attr('for', 'rg_gallery_label_' + num);
                    $(this).find('label[for^="rg_gallery_alt_"]').attr('for', 'rg_gallery_alt_' + num);
                });
            }

            function syncIds() {
                const ids = [];
                $('#rg-gallery-list .rg-gallery-card').each(function() {
                    const id = String($(this).data('id') || '');
                    if (id) {
                        ids.push(id);
                    }
                });
                $('#room_gallery_images').val(ids.join(','));
            }

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

                        const thumbUrl = attachment.sizes && attachment.sizes.medium ?
                            attachment.sizes.medium.url :
                            (attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url);

                        const nextNum = $('#rg-gallery-list .rg-gallery-card').length + 1;

                        $('#rg-gallery-list').append(
                            '<div class="rg-gallery-card" data-id="' + id + '">' +
                            '<div class="rg-gallery-card-media">' +
                            '<img src="' + thumbUrl + '" alt="">' +
                            '</div>' +
                            '<div class="rg-gallery-card-body">' +
                            '<p class="rg-gallery-card-title">画像' + nextNum + '</p>' +
                            '<p class="rg-gallery-field">' +
                            '<label for="rg_gallery_label_' + nextNum + '">ラベル</label>' +
                            '<input type="text" id="rg_gallery_label_' + nextNum + '" name="rg_gallery_label_' + nextNum + '" class="widefat" placeholder="例: living / kitchen / bathroom">' +
                            '</p>' +
                            '<p class="rg-gallery-field">' +
                            '<label for="rg_gallery_alt_' + nextNum + '">alt補助文</label>' +
                            '<input type="text" id="rg_gallery_alt_' + nextNum + '" name="rg_gallery_alt_' + nextNum + '" class="widefat" placeholder="画像altが無いときの補助文">' +
                            '</p>' +
                            '<div class="rg-gallery-actions">' +
                            '<button type="button" class="button-link-delete rg-remove-gallery-image">削除</button>' +
                            '</div>' +
                            '</div>' +
                            '</div>'
                        );
                    });

                    syncIds();
                    rebuildNames();
                });

                frame.open();
            });

            $(document).off('click.rgRemoveGallery').on('click.rgRemoveGallery', '.rg-remove-gallery-image', function(e) {
                e.preventDefault();

                $(this).closest('.rg-gallery-card').remove();
                syncIds();
                rebuildNames();
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
 * 4. ヒーロー共通 fallback 文言
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
    <style>
        .rg-hero-text-box {
            padding: 14px;
            border: 1px solid #dcdcde;
            border-radius: 8px;
            background: #fff;
        }

        .rg-hero-text-field {
            margin: 0 0 14px;
        }

        .rg-hero-text-field:last-child {
            margin-bottom: 0;
        }

        .rg-hero-text-field label {
            display: block;
            margin: 0 0 6px;
            font-weight: 700;
        }

        .rg-hero-text-note {
            margin: 0 0 12px;
            color: #646970;
        }
    </style>

    <div class="rg-hero-text-box">
        <p class="rg-hero-text-note">
            各スライド個別文言が未入力のときに使う共通 fallback 文言です。
        </p>

        <p class="rg-hero-text-field">
            <label for="rg_hero_eyebrow">上部小見出し</label>
            <input
                type="text"
                id="rg_hero_eyebrow"
                name="rg_hero_eyebrow"
                value="<?php echo esc_attr($eyebrow); ?>"
                class="widefat"
                placeholder="NASU ROOM STYLE">
        </p>

        <p class="rg-hero-text-field">
            <label for="rg_hero_lead">リード文</label>
            <textarea
                id="rg_hero_lead"
                name="rg_hero_lead"
                rows="3"
                class="widefat"
                placeholder="東京と那須、二つの魅力を取り入れた暮らしを実現したい方へ。"><?php echo esc_textarea($lead); ?></textarea>
        </p>

        <p class="rg-hero-text-field">
            <label for="rg_hero_text">説明文</label>
            <textarea
                id="rg_hero_text"
                name="rg_hero_text"
                rows="4"
                class="widefat"
                placeholder="二拠点生活・移住・別荘購入を検討する方に向けて、理想の住まいと暮らし方をご提案します。"><?php echo esc_textarea($text); ?></textarea>
        </p>
    </div>
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

    $defaults = array(
        'rg_media_title'   => '北米住宅仕様',
        'rg_media_lead'    => '住まいの考え方や空間づくりの参考として、建築仕様や雰囲気をご覧いただけます。',
        'rg_youtube_title' => '分譲地紹介',
        'rg_youtube_lead'  => '那須での暮らしや周辺環境のイメージを深めたい方に向けて、分譲地の魅力をご紹介します。',
        'rg_related_title' => '関連する住まい・暮らしのご紹介',
        'rg_related_lead'  => '那須での住まいづくりや暮らしの参考になる情報をご覧いただけます。',
        'rg_gallery_title' => '那須で描く、これからの暮らし',
        'rg_gallery_lead'  => '住まいの雰囲気や空間づくりの参考として、写真を大きくご覧いただけます。',
        'rg_cta_text'      => '住まいや暮らしに関するご相談は、下記メールアドレスからお問い合わせください。',
        'rg_cta_email'     => 'contact@naigaicorp.net',
    );

    echo '<p style="margin:0 0 12px;color:#646970;">未入力の場合は既定値を使用します。</p>';

    foreach ($fields as $key => $label) {
        $value = get_post_meta($post->ID, '_' . $key, true);
        $placeholder = isset($defaults[$key]) ? $defaults[$key] : '';

        echo '<p>';
        echo '<label for="' . esc_attr($key) . '"><strong>' . esc_html($label) . '</strong></label>';

        if ($key === 'rg_cta_email') {
            echo '<input type="email" id="' . esc_attr($key) . '" name="' . esc_attr($key) . '" value="' . esc_attr($value) . '" class="widefat" placeholder="' . esc_attr($placeholder) . '">';
        } elseif (strpos($key, '_title') !== false) {
            echo '<input type="text" id="' . esc_attr($key) . '" name="' . esc_attr($key) . '" value="' . esc_attr($value) . '" class="widefat" placeholder="' . esc_attr($placeholder) . '">';
        } else {
            echo '<textarea id="' . esc_attr($key) . '" name="' . esc_attr($key) . '" rows="3" class="widefat" placeholder="' . esc_attr($placeholder) . '">' . esc_textarea($value) . '</textarea>';
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

    /* ---------- スライドごとのヒーロー文言 ---------- */
    for ($i = 1; $i <= 20; $i++) {
        $eyebrow_key = "_rg_slide_hero_eyebrow_{$i}";
        $title_key   = "_rg_slide_hero_title_{$i}";
        $lead_key    = "_rg_slide_hero_lead_{$i}";
        $text_key    = "_rg_slide_hero_text_{$i}";

        $eyebrow_val = isset($_POST["rg_slide_hero_eyebrow_{$i}"])
            ? rg_clean_text($_POST["rg_slide_hero_eyebrow_{$i}"])
            : '';

        $title_val = isset($_POST["rg_slide_hero_title_{$i}"])
            ? rg_clean_text($_POST["rg_slide_hero_title_{$i}"])
            : '';

        $lead_val = isset($_POST["rg_slide_hero_lead_{$i}"])
            ? rg_clean_textarea($_POST["rg_slide_hero_lead_{$i}"])
            : '';

        $text_val = isset($_POST["rg_slide_hero_text_{$i}"])
            ? rg_clean_textarea($_POST["rg_slide_hero_text_{$i}"])
            : '';

        if ($eyebrow_val !== '') {
            update_post_meta($post_id, $eyebrow_key, $eyebrow_val);
        } else {
            delete_post_meta($post_id, $eyebrow_key);
        }

        if ($title_val !== '') {
            update_post_meta($post_id, $title_key, $title_val);
        } else {
            delete_post_meta($post_id, $title_key);
        }

        if ($lead_val !== '') {
            update_post_meta($post_id, $lead_key, $lead_val);
        } else {
            delete_post_meta($post_id, $lead_key);
        }

        if ($text_val !== '') {
            update_post_meta($post_id, $text_key, $text_val);
        } else {
            delete_post_meta($post_id, $text_key);
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

    /* ---------- 下部ギャラリー ラベル / alt補助文 ---------- */
    for ($i = 1; $i <= 20; $i++) {
        $label_key = "_rg_gallery_label_{$i}";
        $alt_key   = "_rg_gallery_alt_{$i}";

        $label_val = isset($_POST["rg_gallery_label_{$i}"])
            ? rg_clean_text($_POST["rg_gallery_label_{$i}"])
            : '';

        $alt_val = isset($_POST["rg_gallery_alt_{$i}"])
            ? rg_clean_text($_POST["rg_gallery_alt_{$i}"])
            : '';

        if ($label_val !== '') {
            update_post_meta($post_id, $label_key, $label_val);
        } else {
            delete_post_meta($post_id, $label_key);
        }

        if ($alt_val !== '') {
            update_post_meta($post_id, $alt_key, $alt_val);
        } else {
            delete_post_meta($post_id, $alt_key);
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

    /* ---------- 共通 fallback ヒーロー文言 ---------- */
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

/* =========================================================
 * フロント表示: MP4 / MP3
 * ========================================================= */
if (!function_exists('display_media_mp4')) {
    function display_media_mp4($post_id = null)
    {
        $post_id = $post_id ?: get_the_ID();

        $url = get_post_meta($post_id, '_media_file', true);
        if ($url === '') {
            $url = get_post_meta($post_id, '_media_url', true);
        }

        if (!$url) {
            return;
        }

        $path = (string) parse_url($url, PHP_URL_PATH);
        $ext  = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        $audio_mimes = array(
            'mp3' => 'audio/mpeg',
            'm4a' => 'audio/mp4',
            'aac' => 'audio/aac',
            'wav' => 'audio/wav',
            'ogg' => 'audio/ogg',
        );

        echo '<div class="room-media-block">';

        if (isset($audio_mimes[$ext])) {
            /* -----------------------------------------
             * 音声ファイル:
             * スクロール位置で再生/停止したいので
             * js-scroll-autoplay-media クラスを付与
             * ----------------------------------------- */
            echo '<audio class="room-audio-player js-scroll-autoplay-media" controls preload="metadata">';
            echo '<source src="' . esc_url($url) . '" type="' . esc_attr($audio_mimes[$ext]) . '">';
            echo 'お使いのブラウザは audio 再生に対応していません。';
            echo '</audio>';
        } else {
            /* -----------------------------------------
             * 動画ファイル:
             * スクロール位置で再生/停止したいので
             * js-scroll-autoplay-media クラスを付与
             * 自動再生制限対策として muted / playsinline を付与
             * ----------------------------------------- */
            echo '<div class="custom-video custom-video--bg">';
            echo '<video class="room-video-player room-video-player--bg js-scroll-autoplay-media" controls playsinline muted preload="metadata">';
            echo '<source src="' . esc_url($url) . '" type="video/mp4">';
            echo 'お使いのブラウザは video 再生に対応していません。';
            echo '</video>';
            echo '</div>';
        }

        echo '</div>';
    }
}

/* =========================================================
 * フロント表示: YouTube
 * ========================================================= */
if (!function_exists('display_media_youtube')) {
    function display_media_youtube($post_id = null)
    {
        $post_id = $post_id ?: get_the_ID();

        $youtube_ids = array();

        for ($i = 1; $i <= 5; $i++) {
            $id = trim((string) get_post_meta($post_id, "_youtube_id_{$i}", true));
            $id = preg_replace('/[^A-Za-z0-9_-]/', '', $id);

            if ($id !== '') {
                $youtube_ids[] = $id;
            }
        }

        $youtube_ids = array_values(array_unique($youtube_ids));

        if (empty($youtube_ids)) {
            return;
        }

        $has_multi = count($youtube_ids) > 1;

        echo '<div class="youtube-video-shell">';

        if ($has_multi) {
            echo '<button type="button" class="youtube-video-nav youtube-video-nav-prev" aria-label="前の動画"><span aria-hidden="true">‹</span></button>';
        }

        echo '<div class="swiper youtube-video-slider">';
        echo '<div class="swiper-wrapper">';

        foreach ($youtube_ids as $index => $id) {
            echo '<div class="swiper-slide">';
            echo '<div class="youtube-video">';
            echo '<lite-youtube'
                . ' videoid="' . esc_attr($id) . '"'
                . ' title="' . esc_attr('YouTube動画 ' . ($index + 1)) . '"'
                . ' playlabel="' . esc_attr('YouTube動画を再生') . '"'
                . ' params="' . esc_attr('rel=0&playsinline=1') . '"'
                . '></lite-youtube>';
            echo '</div>';
            echo '</div>';
        }

        echo '</div>';

        if ($has_multi) {
            echo '<div class="youtube-video-pagination"></div>';
        }

        echo '</div>';

        if ($has_multi) {
            echo '<button type="button" class="youtube-video-nav youtube-video-nav-next" aria-label="次の動画"><span aria-hidden="true">›</span></button>';
        }

        echo '</div>';
    }
}
