<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('ngw_wakugumi_template_slug')) {
    function ngw_wakugumi_template_slug()
    {
        return 'page-wakugumi.php';
    }
}

if (!function_exists('ngw_is_wakugumi_template')) {
    function ngw_is_wakugumi_template($post_id = 0)
    {
        $post_id = absint($post_id);
        return $post_id && get_post_type($post_id) === 'page' && get_page_template_slug($post_id) === ngw_wakugumi_template_slug();
    }
}

if (!function_exists('ngw_wakugumi_fields')) {
    function ngw_wakugumi_fields()
    {
        return array(
            'hero_eyebrow'            => 'text',
            'hero_title'              => 'text',
            'hero_text'               => 'textarea',
            'hero_primary_text'       => 'text',
            'hero_primary_url'        => 'url',
            'hero_secondary_text'     => 'text',
            'hero_secondary_url'      => 'url',
            'hero_image_pc_id'        => 'image',
            'hero_image_sp_id'        => 'image',
            'hero_image_id'           => 'image',

            'guide_eyebrow'           => 'text',
            'guide_title'             => 'text',
            'guide_text'              => 'textarea',
            'guide_image_id'          => 'image',

            'design_eyebrow'          => 'text',
            'design_title'            => 'text',
            'design_text'             => 'textarea',
            'design_image_id'         => 'image',

            'performance_eyebrow'     => 'text',
            'performance_title'       => 'text',
            'performance_text'        => 'textarea',
            'performance_image_id'    => 'image',

            'lifestyle_eyebrow'       => 'text',
            'lifestyle_title'         => 'text',
            'lifestyle_text'          => 'textarea',
            'lifestyle_image_id'      => 'image',
            'lifestyle_primary_text'  => 'text',
            'lifestyle_primary_url'   => 'url',
            'lifestyle_secondary_text' => 'text',
            'lifestyle_secondary_url' => 'url',

            'flow_eyebrow'            => 'text',
            'flow_title'              => 'text',
            'flow_step_1'             => 'text',
            'flow_step_2'             => 'text',
            'flow_step_3'             => 'text',
            'flow_step_4'             => 'text',
            'flow_step_5'             => 'text',

            'contact_eyebrow'         => 'text',
            'contact_title'           => 'text',
            'contact_text'            => 'textarea',
            'contact_cta_text'        => 'text',
            'contact_cta_url'         => 'url',
            'contact_image_id'        => 'image',
        );
    }
}

if (!function_exists('ngw_get_wakugumi_meta')) {
    function ngw_get_wakugumi_meta($post_id, $key, $default = '')
    {
        $value = get_post_meta($post_id, $key, true);
        return ($value !== '' && $value !== null) ? $value : $default;
    }
}

if (!function_exists('ngw_wakugumi_defaults')) {
    function ngw_wakugumi_defaults()
    {
        return array(
            'hero_eyebrow'             => 'North American Style',
            'hero_title'               => '北米住宅の住まい',
            'hero_text'                => '広がりのある空間、ガレージやデッキのある暮らし、那須の自然に合う住まい方をご提案します。',
            'hero_primary_text'        => '来店予約',
            'hero_primary_url'         => home_url('/reservation'),
            'hero_secondary_text'      => 'お部屋のギャラリーを見る',
            'hero_secondary_url'       => home_url('/room-gallary'),

            'guide_eyebrow'            => 'Guide',
            'guide_title'              => '北米住宅とは',
            'guide_text'               => '外観デザインだけでなく、ゆとりある間取りや屋外とのつながりを大切にする住まいです。那須での別荘・定住・二拠点生活にも相性のよい考え方です。',

            'design_eyebrow'           => 'Design',
            'design_title'             => '開放感のある設計',
            'design_text'              => '吹き抜けや大きな開口部、ガレージ、ウッドデッキなど、暮らしを楽しむための空間づくりに向いた住まいです。',

            'performance_eyebrow'      => 'Performance',
            'performance_title'        => '那須の気候に合わせた計画',
            'performance_text'         => '断熱性・気密性・動線計画は、デザインだけでなく地域性に合わせた検討が大切です。暮らし方に合うバランスを考えながらご提案します。',

            'lifestyle_eyebrow'        => 'Lifestyle',
            'lifestyle_title'          => '那須での暮らしに合う住まい',
            'lifestyle_text'           => '自然を感じながらゆったり過ごしたい方、趣味の時間やアウトドアも楽しみたい方に向いた住まい方です。',
            'lifestyle_primary_text'   => '施工実例を見る',
            'lifestyle_primary_url'    => home_url('/sekou-jirei'),
            'lifestyle_secondary_text' => '自然素材の住まいを見る',
            'lifestyle_secondary_url'  => home_url('/zairai-kouhou'),

            'flow_eyebrow'             => 'Flow',
            'flow_title'               => '家づくりの流れ',
            'flow_step_1'              => 'ご相談',
            'flow_step_2'              => 'プランのご提案',
            'flow_step_3'              => '設計・仕様調整',
            'flow_step_4'              => '着工',
            'flow_step_5'              => '完成・お引き渡し',

            'contact_eyebrow'          => 'Contact',
            'contact_title'            => '住まいづくりのご相談はこちら',
            'contact_text'             => '北米住宅の考え方や、那須での住まいづくりについてお気軽にご相談ください。',
            'contact_cta_text'         => 'ご相談はこちら',
            'contact_cta_url'          => home_url('/reservation'),
        );
    }
}

if (!function_exists('ngw_meta_with_default')) {
    function ngw_meta_with_default($post_id, $key, $fallback = '')
    {
        $defaults = ngw_wakugumi_defaults();
        $default = array_key_exists($key, $defaults) ? $defaults[$key] : $fallback;
        return ngw_get_wakugumi_meta($post_id, $key, $default);
    }
}

if (!function_exists('ngw_render_media_field')) {
    function ngw_render_media_field($name, $value = 0)
    {
        $value   = absint($value);
        $preview = $value ? wp_get_attachment_image_url($value, 'medium') : '';
?>
        <div class="ngw-media-field">
            <input type="hidden" name="<?php echo esc_attr($name); ?>" value="<?php echo esc_attr($value); ?>" class="ngw-media-input">
            <div class="ngw-media-preview" style="margin:8px 0;">
                <?php if ($preview) : ?>
                    <img src="<?php echo esc_url($preview); ?>" alt="" style="max-width:240px;height:auto;border:1px solid #d0d7de;border-radius:8px;display:block;">
                <?php else : ?>
                    <div style="padding:14px;border:1px dashed #c3cad4;border-radius:8px;color:#667085;background:#fafbfc;">画像未選択</div>
                <?php endif; ?>
            </div>
            <p style="display:flex;gap:8px;flex-wrap:wrap;margin:0;">
                <button type="button" class="button button-secondary ngw-media-select">画像を選択</button>
                <button type="button" class="button ngw-media-remove">画像を外す</button>
            </p>
        </div>
    <?php
    }
}

if (!function_exists('ngw_render_text_field')) {
    function ngw_render_text_field($name, $label, $value = '')
    {
    ?>
        <p style="margin:0 0 16px;">
            <label style="display:block;font-weight:600;margin:0 0 6px;"><?php echo esc_html($label); ?></label>
            <input type="text" name="<?php echo esc_attr($name); ?>" value="<?php echo esc_attr($value); ?>" style="width:100%;">
        </p>
    <?php
    }
}

if (!function_exists('ngw_render_textarea_field')) {
    function ngw_render_textarea_field($name, $label, $value = '', $rows = 4)
    {
    ?>
        <p style="margin:0 0 16px;">
            <label style="display:block;font-weight:600;margin:0 0 6px;"><?php echo esc_html($label); ?></label>
            <textarea name="<?php echo esc_attr($name); ?>" rows="<?php echo (int) $rows; ?>" style="width:100%;"><?php echo esc_textarea($value); ?></textarea>
        </p>
    <?php
    }
}

if (!function_exists('ngw_add_wakugumi_metabox')) {
    function ngw_add_wakugumi_metabox($post)
    {
        if (!$post instanceof WP_Post || $post->post_type !== 'page' || !ngw_is_wakugumi_template($post->ID)) {
            return;
        }

        add_meta_box('ngw_wakugumi_metabox', '北米住宅ページ設定', 'ngw_render_wakugumi_metabox', 'page', 'normal', 'high');
    }
}
add_action('add_meta_boxes_page', 'ngw_add_wakugumi_metabox');

if (!function_exists('ngw_render_wakugumi_metabox')) {
    function ngw_render_wakugumi_metabox($post)
    {
        wp_nonce_field('ngw_save_wakugumi_metabox', 'ngw_wakugumi_nonce');

        $defaults = ngw_wakugumi_defaults();
        $values = array();
        foreach ($defaults as $key => $default) {
            $values[$key] = ngw_meta_with_default($post->ID, $key, $default);
        }

        foreach (['hero_image_pc_id', 'hero_image_sp_id', 'hero_image_id', 'guide_image_id', 'design_image_id', 'performance_image_id', 'lifestyle_image_id', 'contact_image_id'] as $image_key) {
            $values[$image_key] = absint(get_post_meta($post->ID, $image_key, true));
        }
    ?>
        <div style="display:grid;gap:20px;">
            <section style="padding:16px;border:1px solid #d0d7de;border-radius:10px;background:#fff;">
                <h3 style="margin:0 0 16px;">Hero</h3>
                <?php ngw_render_text_field('hero_eyebrow', 'アイブロー', $values['hero_eyebrow']); ?>
                <?php ngw_render_text_field('hero_title', 'タイトル', $values['hero_title']); ?>
                <?php ngw_render_textarea_field('hero_text', '説明文', $values['hero_text'], 4); ?>
                <?php ngw_render_text_field('hero_primary_text', 'Hero CTA1文言（来店予約）', $values['hero_primary_text']); ?>
                <?php ngw_render_text_field('hero_primary_url', 'Hero CTA1 URL', $values['hero_primary_url']); ?>
                <?php ngw_render_text_field('hero_secondary_text', 'Hero CTA2文言（お部屋のギャラリー）', $values['hero_secondary_text']); ?>
                <?php ngw_render_text_field('hero_secondary_url', 'Hero CTA2 URL', $values['hero_secondary_url']); ?>
                <p style="margin:0 0 8px;font-weight:600;">Hero画像（PC）</p>
                <?php ngw_render_media_field('hero_image_pc_id', $values['hero_image_pc_id']); ?>
                <p style="margin:16px 0 8px;font-weight:600;">Hero画像（SP）</p>
                <?php ngw_render_media_field('hero_image_sp_id', $values['hero_image_sp_id']); ?>
            </section>

            <section style="padding:16px;border:1px solid #d0d7de;border-radius:10px;background:#fff;">
                <h3 style="margin:0 0 16px;">Guide</h3>
                <?php ngw_render_text_field('guide_eyebrow', 'アイブロー', $values['guide_eyebrow']); ?>
                <?php ngw_render_text_field('guide_title', '見出し', $values['guide_title']); ?>
                <?php ngw_render_textarea_field('guide_text', '本文', $values['guide_text'], 5); ?>
                <p style="margin:0 0 8px;font-weight:600;">Guide画像</p>
                <?php ngw_render_media_field('guide_image_id', $values['guide_image_id']); ?>
            </section>

            <section style="padding:16px;border:1px solid #d0d7de;border-radius:10px;background:#fff;">
                <h3 style="margin:0 0 16px;">Design</h3>
                <?php ngw_render_text_field('design_eyebrow', 'アイブロー', $values['design_eyebrow']); ?>
                <?php ngw_render_text_field('design_title', '見出し', $values['design_title']); ?>
                <?php ngw_render_textarea_field('design_text', '本文', $values['design_text'], 5); ?>
                <p style="margin:0 0 8px;font-weight:600;">Design画像</p>
                <?php ngw_render_media_field('design_image_id', $values['design_image_id']); ?>
            </section>

            <section style="padding:16px;border:1px solid #d0d7de;border-radius:10px;background:#fff;">
                <h3 style="margin:0 0 16px;">Performance</h3>
                <?php ngw_render_text_field('performance_eyebrow', 'アイブロー', $values['performance_eyebrow']); ?>
                <?php ngw_render_text_field('performance_title', '見出し', $values['performance_title']); ?>
                <?php ngw_render_textarea_field('performance_text', '本文', $values['performance_text'], 5); ?>
                <p style="margin:0 0 8px;font-weight:600;">Performance画像</p>
                <?php ngw_render_media_field('performance_image_id', $values['performance_image_id']); ?>
            </section>

            <section style="padding:16px;border:1px solid #d0d7de;border-radius:10px;background:#fff;">
                <h3 style="margin:0 0 16px;">Lifestyle</h3>
                <?php ngw_render_text_field('lifestyle_eyebrow', 'アイブロー', $values['lifestyle_eyebrow']); ?>
                <?php ngw_render_text_field('lifestyle_title', '見出し', $values['lifestyle_title']); ?>
                <?php ngw_render_textarea_field('lifestyle_text', '本文', $values['lifestyle_text'], 5); ?>
                <p style="margin:0 0 8px;font-weight:600;">Lifestyle画像</p>
                <?php ngw_render_media_field('lifestyle_image_id', $values['lifestyle_image_id']); ?>
                <?php ngw_render_text_field('lifestyle_primary_text', 'CTA1文言', $values['lifestyle_primary_text']); ?>
                <?php ngw_render_text_field('lifestyle_primary_url', 'CTA1 URL', $values['lifestyle_primary_url']); ?>
                <?php ngw_render_text_field('lifestyle_secondary_text', 'CTA2文言', $values['lifestyle_secondary_text']); ?>
                <?php ngw_render_text_field('lifestyle_secondary_url', 'CTA2 URL', $values['lifestyle_secondary_url']); ?>
            </section>

            <section style="padding:16px;border:1px solid #d0d7de;border-radius:10px;background:#fff;">
                <h3 style="margin:0 0 16px;">Flow</h3>
                <?php ngw_render_text_field('flow_eyebrow', 'アイブロー', $values['flow_eyebrow']); ?>
                <?php ngw_render_text_field('flow_title', '見出し', $values['flow_title']); ?>
                <?php ngw_render_text_field('flow_step_1', 'STEP 1', $values['flow_step_1']); ?>
                <?php ngw_render_text_field('flow_step_2', 'STEP 2', $values['flow_step_2']); ?>
                <?php ngw_render_text_field('flow_step_3', 'STEP 3', $values['flow_step_3']); ?>
                <?php ngw_render_text_field('flow_step_4', 'STEP 4', $values['flow_step_4']); ?>
                <?php ngw_render_text_field('flow_step_5', 'STEP 5', $values['flow_step_5']); ?>
            </section>

            <section style="padding:16px;border:1px solid #d0d7de;border-radius:10px;background:#fff;">
                <h3 style="margin:0 0 16px;">Contact</h3>
                <?php ngw_render_text_field('contact_eyebrow', 'アイブロー', $values['contact_eyebrow']); ?>
                <?php ngw_render_text_field('contact_title', '見出し', $values['contact_title']); ?>
                <?php ngw_render_textarea_field('contact_text', '本文', $values['contact_text'], 4); ?>
                <?php ngw_render_text_field('contact_cta_text', 'CTA文言', $values['contact_cta_text']); ?>
                <?php ngw_render_text_field('contact_cta_url', 'CTA URL', $values['contact_cta_url']); ?>
                <p style="margin:0 0 8px;font-weight:600;">CTA背景画像</p>
                <?php ngw_render_media_field('contact_image_id', $values['contact_image_id']); ?>
            </section>
        </div>
    <?php
    }
}

if (!function_exists('ngw_save_wakugumi_metabox')) {
    function ngw_save_wakugumi_metabox($post_id)
    {
        if (!isset($_POST['ngw_wakugumi_nonce'])) {
            return;
        }
        $nonce = sanitize_text_field(wp_unslash($_POST['ngw_wakugumi_nonce']));
        if (!wp_verify_nonce($nonce, 'ngw_save_wakugumi_metabox')) {
            return;
        }
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        if (wp_is_post_revision($post_id) || get_post_type($post_id) !== 'page' || !current_user_can('edit_page', $post_id)) {
            return;
        }
        $template = isset($_POST['_wp_page_template']) ? sanitize_text_field(wp_unslash($_POST['_wp_page_template'])) : get_page_template_slug($post_id);
        if ($template !== ngw_wakugumi_template_slug()) {
            return;
        }

        foreach (ngw_wakugumi_fields() as $field_key => $field_type) {
            $raw = isset($_POST[$field_key]) ? wp_unslash($_POST[$field_key]) : '';
            switch ($field_type) {
                case 'textarea':
                    $value = sanitize_textarea_field($raw);
                    break;
                case 'url':
                    $value = esc_url_raw($raw);
                    break;
                case 'image':
                    $value = absint($raw);
                    break;
                default:
                    $value = sanitize_text_field($raw);
                    break;
            }
            update_post_meta($post_id, $field_key, $value);
        }
    }
}
add_action('save_post_page', 'ngw_save_wakugumi_metabox');

if (!function_exists('ngw_wakugumi_admin_enqueue')) {
    function ngw_wakugumi_admin_enqueue($hook)
    {
        if ($hook !== 'post.php' && $hook !== 'post-new.php') {
            return;
        }
        $screen = get_current_screen();
        if (!$screen || $screen->post_type !== 'page') {
            return;
        }
        wp_enqueue_media();
    }
}
add_action('admin_enqueue_scripts', 'ngw_wakugumi_admin_enqueue');

if (!function_exists('ngw_wakugumi_admin_footer_script')) {
    function ngw_wakugumi_admin_footer_script()
    {
        $screen = get_current_screen();
        if (!$screen || $screen->post_type !== 'page') {
            return;
        }
    ?>
        <script>
            jQuery(function($) {
                $(document).on('click', '.ngw-media-select', function(e) {
                    e.preventDefault();
                    const $field = $(this).closest('.ngw-media-field');
                    const $input = $field.find('.ngw-media-input');
                    const $preview = $field.find('.ngw-media-preview');
                    const frame = wp.media({
                        title: '画像を選択',
                        button: {
                            text: 'この画像を使う'
                        },
                        multiple: false
                    });
                    frame.on('select', function() {
                        const attachment = frame.state().get('selection').first().toJSON();
                        const imageUrl = attachment.sizes && attachment.sizes.medium ? attachment.sizes.medium.url : attachment.url;
                        $input.val(attachment.id);
                        $preview.html('<img src="' + imageUrl + '" alt="" style="max-width:240px;height:auto;border:1px solid #d0d7de;border-radius:8px;display:block;">');
                    });
                    frame.open();
                });

                $(document).on('click', '.ngw-media-remove', function(e) {
                    e.preventDefault();
                    const $field = $(this).closest('.ngw-media-field');
                    $field.find('.ngw-media-input').val('');
                    $field.find('.ngw-media-preview').html('<div style="padding:14px;border:1px dashed #c3cad4;border-radius:8px;color:#667085;background:#fafbfc;">画像未選択</div>');
                });
            });
        </script>
<?php
    }
}
add_action('admin_footer-post.php', 'ngw_wakugumi_admin_footer_script');
add_action('admin_footer-post-new.php', 'ngw_wakugumi_admin_footer_script');
