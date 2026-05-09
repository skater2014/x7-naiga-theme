<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('ngs_sekou_template_slug')) {
    function ngs_sekou_template_slug()
    {
        return 'page-sekoujirei.php';
    }
}

if (!function_exists('ngs_is_sekou_template')) {
    function ngs_is_sekou_template($post_id)
    {
        $post_id = absint($post_id);

        if (!$post_id) {
            return false;
        }

        return get_post_type($post_id) === 'page'
            && get_page_template_slug($post_id) === ngs_sekou_template_slug();
    }
}

if (!function_exists('ngs_sekou_section_map')) {
    function ngs_sekou_section_map()
    {
        return [
            'ngs_sekou_foundation' => '基礎工事セクション',
            'ngs_sekou_zairai'     => '在来工法セクション',
            'ngs_sekou_wakugumi'   => '2×4・枠組み工法セクション',
        ];
    }
}

if (!function_exists('ngs_sekou_defaults')) {
    function ngs_sekou_defaults()
    {
        return [
            'ngs_sekou_hero_eyebrow' => 'Works',
            'ngs_sekou_hero_title'   => '施工実例',
            'ngs_sekou_hero_lead'    => '基礎工事から在来工法、2×4・枠組み工法まで、住まいづくりの実例をご紹介します。',

            'ngs_sekou_intro_title'  => '施工実例について',
            'ngs_sekou_intro_text'   => '住まいの完成後だけでなく、基礎・構造・外観・内装・外構まで、住まいづくりの流れと雰囲気が分かる施工実例をまとめています。',

            'ngs_sekou_foundation_eyebrow'    => 'Foundation',
            'ngs_sekou_foundation_title'      => '基礎工事',
            'ngs_sekou_foundation_text'       => '布基礎・ベタ基礎など、住まいを支える基礎工事の実例です。',
            'ngs_sekou_foundation_button_text' => '基礎工事の実例を見る',
            'ngs_sekou_foundation_button_url' => home_url('/contact'),

            'ngs_sekou_zairai_eyebrow'        => 'Zairai',
            'ngs_sekou_zairai_title'          => '在来工法',
            'ngs_sekou_zairai_text'           => '自由度の高い設計に対応しやすく、和の要素から洋風住宅まで幅広く表現しやすい工法です。',
            'ngs_sekou_zairai_button_text'    => '在来工法の実例を見る',
            'ngs_sekou_zairai_button_url'     => home_url('/contact'),

            'ngs_sekou_wakugumi_eyebrow'      => '2×4 / Frame',
            'ngs_sekou_wakugumi_title'        => '2×4・枠組み工法',
            'ngs_sekou_wakugumi_text'         => '面で支える構造が特徴で、洋風デザインや整った外観との相性が良い工法です。',
            'ngs_sekou_wakugumi_button_text'  => '2×4・枠組み工法の実例を見る',
            'ngs_sekou_wakugumi_button_url'   => home_url('/contact'),

            'ngs_sekou_cta_title'             => '住まいづくりのご相談はこちら',
            'ngs_sekou_cta_text'              => '施工実例を見ながら、ご希望のデザインや暮らし方に合わせた住まいづくりをご相談いただけます。',
            'ngs_sekou_cta_button_text'       => 'お問い合わせはこちら',
            'ngs_sekou_cta_button_url'        => home_url('/contact'),
        ];
    }
}

if (!function_exists('ngs_sekou_get_meta_default')) {
    function ngs_sekou_get_meta_default($key)
    {
        $defaults = ngs_sekou_defaults();
        return $defaults[$key] ?? '';
    }
}

if (!function_exists('ngs_sekou_fields')) {
    function ngs_sekou_fields()
    {
        return [
            'ngs_sekou_hero_eyebrow'       => 'text',
            'ngs_sekou_hero_title'         => 'text',
            'ngs_sekou_hero_lead'          => 'textarea',
            'ngs_sekou_hero_image_pc'      => 'image',
            'ngs_sekou_hero_image_sp'      => 'image',

            'ngs_sekou_intro_title'        => 'text',
            'ngs_sekou_intro_text'         => 'textarea',

            'ngs_sekou_foundation_eyebrow'     => 'text',
            'ngs_sekou_foundation_title'       => 'text',
            'ngs_sekou_foundation_text'        => 'textarea',
            'ngs_sekou_foundation_button_text' => 'text',
            'ngs_sekou_foundation_button_url'  => 'url',
            'ngs_sekou_foundation_gallery'     => 'gallery',

            'ngs_sekou_zairai_eyebrow'         => 'text',
            'ngs_sekou_zairai_title'           => 'text',
            'ngs_sekou_zairai_text'            => 'textarea',
            'ngs_sekou_zairai_button_text'     => 'text',
            'ngs_sekou_zairai_button_url'      => 'url',
            'ngs_sekou_zairai_gallery'         => 'gallery',

            'ngs_sekou_wakugumi_eyebrow'       => 'text',
            'ngs_sekou_wakugumi_title'         => 'text',
            'ngs_sekou_wakugumi_text'          => 'textarea',
            'ngs_sekou_wakugumi_button_text'   => 'text',
            'ngs_sekou_wakugumi_button_url'    => 'url',
            'ngs_sekou_wakugumi_gallery'       => 'gallery',

            'ngs_sekou_cta_title'          => 'text',
            'ngs_sekou_cta_text'           => 'textarea',
            'ngs_sekou_cta_button_text'    => 'text',
            'ngs_sekou_cta_button_url'     => 'url',
            'ngs_sekou_cta_image'          => 'image',
        ];
    }
}

if (!function_exists('ngs_sekou_admin_enqueue_media')) {
    function ngs_sekou_admin_enqueue_media($hook)
    {
        if (!in_array($hook, ['post.php', 'post-new.php'], true)) {
            return;
        }

        $screen = get_current_screen();
        if (!$screen || $screen->post_type !== 'page') {
            return;
        }

        wp_enqueue_media();
    }
}
add_action('admin_enqueue_scripts', 'ngs_sekou_admin_enqueue_media');

if (!function_exists('ngs_sekou_add_metabox')) {
    function ngs_sekou_add_metabox($post)
    {
        if (!$post || !ngs_is_sekou_template($post->ID)) {
            return;
        }

        add_meta_box(
            'ngs-sekou-metabox',
            '施工実例ページ設定',
            'ngs_sekou_render_metabox',
            'page',
            'normal',
            'high'
        );
    }
}
add_action('add_meta_boxes_page', 'ngs_sekou_add_metabox');

if (!function_exists('ngs_sekou_render_text')) {
    function ngs_sekou_render_text($post_id, $key, $label)
    {
        $value = get_post_meta($post_id, $key, true);
        if ($value === '') {
            $value = ngs_sekou_get_meta_default($key);
        }
?>
        <div class="ngs-field">
            <label for="<?php echo esc_attr($key); ?>"><strong><?php echo esc_html($label); ?></strong></label>
            <input type="text" id="<?php echo esc_attr($key); ?>" name="<?php echo esc_attr($key); ?>" value="<?php echo esc_attr($value); ?>" class="widefat">
        </div>
    <?php
    }
}

if (!function_exists('ngs_sekou_render_textarea')) {
    function ngs_sekou_render_textarea($post_id, $key, $label, $rows = 4)
    {
        $value = get_post_meta($post_id, $key, true);
        if ($value === '') {
            $value = ngs_sekou_get_meta_default($key);
        }
    ?>
        <div class="ngs-field">
            <label for="<?php echo esc_attr($key); ?>"><strong><?php echo esc_html($label); ?></strong></label>
            <textarea id="<?php echo esc_attr($key); ?>" name="<?php echo esc_attr($key); ?>" rows="<?php echo (int) $rows; ?>" class="widefat"><?php echo esc_textarea($value); ?></textarea>
        </div>
    <?php
    }
}

if (!function_exists('ngs_sekou_render_url')) {
    function ngs_sekou_render_url($post_id, $key, $label)
    {
        $value = get_post_meta($post_id, $key, true);
        if ($value === '') {
            $value = ngs_sekou_get_meta_default($key);
        }
    ?>
        <div class="ngs-field">
            <label for="<?php echo esc_attr($key); ?>"><strong><?php echo esc_html($label); ?></strong></label>
            <input type="url" id="<?php echo esc_attr($key); ?>" name="<?php echo esc_attr($key); ?>" value="<?php echo esc_attr($value); ?>" class="widefat">
        </div>
    <?php
    }
}

if (!function_exists('ngs_sekou_render_image')) {
    function ngs_sekou_render_image($post_id, $key, $label)
    {
        $attachment_id = absint(get_post_meta($post_id, $key, true));
        $image_url     = $attachment_id ? wp_get_attachment_image_url($attachment_id, 'medium') : '';
    ?>
        <div class="ngs-field ngs-field--image">
            <strong><?php echo esc_html($label); ?></strong>
            <div class="ngs-image-box">
                <input type="hidden" name="<?php echo esc_attr($key); ?>" value="<?php echo esc_attr($attachment_id); ?>" class="ngs-image-id">
                <div class="ngs-image-preview">
                    <?php if ($image_url) : ?>
                        <img src="<?php echo esc_url($image_url); ?>" alt="">
                    <?php else : ?>
                        <span>画像が未選択です</span>
                    <?php endif; ?>
                </div>
                <p class="ngs-image-actions">
                    <button type="button" class="button button-secondary ngs-image-pick">画像を選択</button>
                    <button type="button" class="button-link-delete ngs-image-remove">画像を削除</button>
                </p>
            </div>
        </div>
    <?php
    }
}

if (!function_exists('ngs_sekou_get_gallery_ids')) {
    function ngs_sekou_get_gallery_ids($post_id, $key)
    {
        $raw = get_post_meta($post_id, $key, true);
        $ids = [];

        if (is_array($raw)) {
            $ids = $raw;
        } elseif (is_string($raw) && $raw !== '') {
            $ids = explode(',', $raw);
        }

        $ids = array_values(array_filter(array_map('absint', $ids)));
        return $ids;
    }
}

if (!function_exists('ngs_sekou_get_legacy_gallery_ids')) {
    function ngs_sekou_get_legacy_gallery_ids($post_id, $prefix)
    {
        $ids = [];

        for ($i = 1; $i <= 3; $i++) {
            $id = absint(get_post_meta($post_id, "{$prefix}_image_{$i}_pc", true));
            if (!$id) {
                $id = absint(get_post_meta($post_id, "{$prefix}_image_{$i}_sp", true));
            }
            if ($id) {
                $ids[] = $id;
            }
        }

        return array_values(array_unique($ids));
    }
}

if (!function_exists('ngs_sekou_get_current_gallery_ids')) {
    function ngs_sekou_get_current_gallery_ids($post_id, $prefix)
    {
        $gallery_key = "{$prefix}_gallery";
        $ids = ngs_sekou_get_gallery_ids($post_id, $gallery_key);

        if (!empty($ids)) {
            return $ids;
        }

        return ngs_sekou_get_legacy_gallery_ids($post_id, $prefix);
    }
}

if (!function_exists('ngs_sekou_render_gallery')) {
    function ngs_sekou_render_gallery($post_id, $key, $label, $legacy_prefix = '')
    {
        $ids = ngs_sekou_get_gallery_ids($post_id, $key);

        if (empty($ids) && $legacy_prefix) {
            $ids = ngs_sekou_get_legacy_gallery_ids($post_id, $legacy_prefix);
        }
    ?>
        <div class="ngs-field ngs-field--full ngs-field--gallery">
            <strong><?php echo esc_html($label); ?></strong>
            <div class="ngs-gallery-box" data-name="<?php echo esc_attr($key); ?>">
                <input type="hidden" name="<?php echo esc_attr($key); ?>" value="<?php echo esc_attr(implode(',', $ids)); ?>" class="ngs-gallery-ids">
                <div class="ngs-gallery-preview">
                    <?php if (!empty($ids)) : ?>
                        <?php foreach ($ids as $attachment_id) :
                            $image_url = wp_get_attachment_image_url($attachment_id, 'medium');
                            if (!$image_url) {
                                continue;
                            }
                        ?>
                            <div class="ngs-gallery-thumb" data-id="<?php echo esc_attr($attachment_id); ?>">
                                <img src="<?php echo esc_url($image_url); ?>" alt="">
                            </div>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <span>画像が未選択です</span>
                    <?php endif; ?>
                </div>
                <p class="description">Hero以外は共通画像を並べます。複数枚選択でき、PC/SPの分岐はしません。</p>
                <p class="ngs-image-actions">
                    <button type="button" class="button button-secondary ngs-gallery-pick">画像を複数選択</button>
                    <button type="button" class="button-link-delete ngs-gallery-remove">すべて削除</button>
                </p>
            </div>
        </div>
    <?php
    }
}

if (!function_exists('ngs_sekou_render_gallery_group')) {
    function ngs_sekou_render_gallery_group($post_id, $prefix, $title)
    {
    ?>
        <section class="ngs-group">
            <h3><?php echo esc_html($title); ?></h3>
            <div class="ngs-grid">
                <?php ngs_sekou_render_text($post_id, "{$prefix}_eyebrow", '英字見出し'); ?>
                <?php ngs_sekou_render_text($post_id, "{$prefix}_title", '見出し'); ?>
                <div class="ngs-field ngs-field--full">
                    <?php ngs_sekou_render_textarea($post_id, "{$prefix}_text", '説明文', 3); ?>
                </div>
                <?php ngs_sekou_render_text($post_id, "{$prefix}_button_text", 'ボタン文言'); ?>
                <?php ngs_sekou_render_url($post_id, "{$prefix}_button_url", 'ボタンURL'); ?>
                <?php ngs_sekou_render_gallery($post_id, "{$prefix}_gallery", '施工画像ギャラリー', $prefix); ?>
            </div>
        </section>
    <?php
    }
}

if (!function_exists('ngs_sekou_render_metabox')) {
    function ngs_sekou_render_metabox($post)
    {
        wp_nonce_field('ngs_sekou_save', 'ngs_sekou_nonce');
    ?>
        <style>
            .ngs-wrap {
                display: grid;
                gap: 24px;
            }

            .ngs-group {
                padding: 20px;
                border: 1px solid #d8dee5;
                border-radius: 12px;
                background: #fff;
            }

            .ngs-grid {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 16px;
            }

            .ngs-field {
                display: grid;
                gap: 8px;
            }

            .ngs-field--full {
                grid-column: 1/-1;
            }

            .ngs-image-preview,
            .ngs-gallery-preview {
                min-height: 120px;
                border: 1px dashed #c5ccd3;
                border-radius: 10px;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 12px;
                background: #f8fafc;
            }

            .ngs-image-preview img {
                display: block;
                max-width: 100%;
                height: auto;
                border-radius: 8px;
            }

            .ngs-gallery-preview {
                justify-content: flex-start;
                align-items: flex-start;
                flex-wrap: wrap;
                gap: 10px;
            }

            .ngs-gallery-thumb {
                width: 110px;
                aspect-ratio: 1/1;
                border-radius: 8px;
                overflow: hidden;
                background: #fff;
                border: 1px solid #d8dee5;
            }

            .ngs-gallery-thumb img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                display: block;
            }

            .ngs-image-actions {
                display: flex;
                gap: 12px;
                align-items: center;
                margin: 8px 0 0;
            }

            @media (max-width: 960px) {
                .ngs-grid {
                    grid-template-columns: 1fr;
                }
            }
        </style>

        <div class="ngs-wrap">
            <section class="ngs-group">
                <h3>Hero設定</h3>
                <div class="ngs-grid">
                    <?php ngs_sekou_render_text($post->ID, 'ngs_sekou_hero_eyebrow', '英字見出し'); ?>
                    <?php ngs_sekou_render_text($post->ID, 'ngs_sekou_hero_title', 'タイトル'); ?>
                    <div class="ngs-field ngs-field--full">
                        <?php ngs_sekou_render_textarea($post->ID, 'ngs_sekou_hero_lead', 'リード文', 3); ?>
                    </div>
                    <?php ngs_sekou_render_image($post->ID, 'ngs_sekou_hero_image_pc', 'Hero画像（PC）'); ?>
                    <?php ngs_sekou_render_image($post->ID, 'ngs_sekou_hero_image_sp', 'Hero画像（SP）'); ?>
                </div>
            </section>

            <section class="ngs-group">
                <h3>導入文設定</h3>
                <div class="ngs-grid">
                    <?php ngs_sekou_render_text($post->ID, 'ngs_sekou_intro_title', '導入見出し'); ?>
                    <div class="ngs-field ngs-field--full">
                        <?php ngs_sekou_render_textarea($post->ID, 'ngs_sekou_intro_text', '導入文', 4); ?>
                    </div>
                </div>
            </section>

            <?php foreach (ngs_sekou_section_map() as $prefix => $label) : ?>
                <?php ngs_sekou_render_gallery_group($post->ID, $prefix, $label); ?>
            <?php endforeach; ?>

            <section class="ngs-group">
                <h3>CTA設定</h3>
                <div class="ngs-grid">
                    <?php ngs_sekou_render_text($post->ID, 'ngs_sekou_cta_title', 'CTAタイトル'); ?>
                    <?php ngs_sekou_render_text($post->ID, 'ngs_sekou_cta_button_text', 'CTAボタン文言'); ?>
                    <div class="ngs-field ngs-field--full">
                        <?php ngs_sekou_render_textarea($post->ID, 'ngs_sekou_cta_text', 'CTA本文', 3); ?>
                    </div>
                    <?php ngs_sekou_render_url($post->ID, 'ngs_sekou_cta_button_url', 'CTAリンクURL'); ?>
                    <?php ngs_sekou_render_image($post->ID, 'ngs_sekou_cta_image', 'CTA背景画像'); ?>
                </div>
            </section>
        </div>
    <?php
    }
}

if (!function_exists('ngs_sekou_sanitize_gallery_ids')) {
    function ngs_sekou_sanitize_gallery_ids($raw)
    {
        if (is_array($raw)) {
            $raw = implode(',', $raw);
        }

        $raw = is_string($raw) ? wp_unslash($raw) : '';
        $parts = preg_split('/\s*,\s*/', $raw, -1, PREG_SPLIT_NO_EMPTY);
        $ids = array_values(array_unique(array_filter(array_map('absint', $parts))));

        return implode(',', $ids);
    }
}

if (!function_exists('ngs_sekou_save_metabox')) {
    function ngs_sekou_save_metabox($post_id)
    {
        if (!isset($_POST['ngs_sekou_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['ngs_sekou_nonce'])), 'ngs_sekou_save')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (get_post_type($post_id) !== 'page') {
            return;
        }

        if (!current_user_can('edit_page', $post_id)) {
            return;
        }

        $current_template = ngs_is_sekou_template($post_id);
        $posted_template  = isset($_POST['_wp_page_template']) && $_POST['_wp_page_template'] === ngs_sekou_template_slug();

        if (!$current_template && !$posted_template) {
            return;
        }

        foreach (ngs_sekou_fields() as $key => $type) {
            $raw = $_POST[$key] ?? '';
            $raw = is_string($raw) ? wp_unslash($raw) : $raw;

            switch ($type) {
                case 'image':
                    $value = absint($raw);
                    break;

                case 'url':
                    $value = esc_url_raw((string) $raw);
                    break;

                case 'textarea':
                    $value = sanitize_textarea_field((string) $raw);
                    break;

                case 'gallery':
                    $value = ngs_sekou_sanitize_gallery_ids($raw);
                    break;

                default:
                    $value = sanitize_text_field((string) $raw);
                    break;
            }

            if ($value === '' || $value === 0) {
                delete_post_meta($post_id, $key);
            } else {
                update_post_meta($post_id, $key, $value);
            }
        }
    }
}
add_action('save_post_page', 'ngs_sekou_save_metabox');

if (!function_exists('ngs_sekou_admin_media_script')) {
    function ngs_sekou_admin_media_script()
    {
        $screen = get_current_screen();
        if (!$screen || $screen->post_type !== 'page') {
            return;
        }
    ?>
        <script>
            jQuery(function($) {
                function renderGalleryPreview($box, attachments) {
                    const $preview = $box.find('.ngs-gallery-preview');

                    if (!attachments.length) {
                        $preview.html('<span>画像が未選択です</span>');
                        return;
                    }

                    const html = attachments.map(function(attachment) {
                        const previewUrl = attachment.sizes && attachment.sizes.medium ? attachment.sizes.medium.url : attachment.url;
                        return '<div class="ngs-gallery-thumb" data-id="' + attachment.id + '"><img src="' + previewUrl + '" alt=""></div>';
                    }).join('');

                    $preview.html(html);
                }

                $(document).on('click', '.ngs-image-pick', function(e) {
                    e.preventDefault();

                    const $box = $(this).closest('.ngs-image-box');
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
                        const previewUrl = attachment.sizes && attachment.sizes.medium ? attachment.sizes.medium.url : attachment.url;

                        $box.find('.ngs-image-id').val(attachment.id);
                        $box.find('.ngs-image-preview').html('<img src="' + previewUrl + '" alt="">');
                    });

                    frame.open();
                });

                $(document).on('click', '.ngs-image-remove', function(e) {
                    e.preventDefault();
                    const $box = $(this).closest('.ngs-image-box');
                    $box.find('.ngs-image-id').val('');
                    $box.find('.ngs-image-preview').html('<span>画像が未選択です</span>');
                });

                $(document).on('click', '.ngs-gallery-pick', function(e) {
                    e.preventDefault();

                    const $box = $(this).closest('.ngs-gallery-box');
                    const frame = wp.media({
                        title: '画像を複数選択',
                        button: {
                            text: 'この画像を使用'
                        },
                        library: {
                            type: 'image'
                        },
                        multiple: true
                    });

                    frame.on('select', function() {
                        const selection = frame.state().get('selection').toJSON();
                        const ids = selection.map(function(item) {
                            return item.id;
                        });
                        $box.find('.ngs-gallery-ids').val(ids.join(','));
                        renderGalleryPreview($box, selection);
                    });

                    frame.open();
                });

                $(document).on('click', '.ngs-gallery-remove', function(e) {
                    e.preventDefault();
                    const $box = $(this).closest('.ngs-gallery-box');
                    $box.find('.ngs-gallery-ids').val('');
                    $box.find('.ngs-gallery-preview').html('<span>画像が未選択です</span>');
                });
            });
        </script>
<?php
    }
}
add_action('admin_footer-post.php', 'ngs_sekou_admin_media_script');
add_action('admin_footer-post-new.php', 'ngs_sekou_admin_media_script');
