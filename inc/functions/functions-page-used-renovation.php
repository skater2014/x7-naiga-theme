<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * ============================================================
 * 中古住宅リノベLP 用
 * テンプレート判定 / メタボックス / 保存処理
 *
 * 目的:
 * - 北米住宅ページの仕組みを流用しつつ、関数名・メタキー名を完全分離する
 * - 他LPの hero_title などと衝突させない
 * - 中古住宅 / 別荘 / リノベーション向けの初期文言を持たせる
 *
 * 使い方:
 * 1. functions.php でこのファイルを require_once する
 * 2. 固定ページに template = page-used-renovation.php を設定する
 * 3. 表示されたメタボックスに内容を入力する
 * ============================================================
 */

if (!function_exists('ngrh_used_renovation_template_slug')) {
    function ngrh_used_renovation_template_slug()
    {
        return 'page-used-renovation.php';
    }
}

if (!function_exists('ngrh_is_used_renovation_template')) {
    function ngrh_is_used_renovation_template($post_id = 0)
    {
        $post_id = absint($post_id);
        return $post_id && get_post_type($post_id) === 'page' && get_page_template_slug($post_id) === ngrh_used_renovation_template_slug();
    }
}

if (!function_exists('ngrh_used_renovation_fields')) {
    function ngrh_used_renovation_fields()
    {
        return array(
            '_ngrh_hero_eyebrow'            => 'text',
            '_ngrh_hero_title'              => 'text',
            '_ngrh_hero_text'               => 'textarea',
            '_ngrh_hero_primary_text'       => 'text',
            '_ngrh_hero_primary_url'        => 'url',
            '_ngrh_hero_secondary_text'     => 'text',
            '_ngrh_hero_secondary_url'      => 'url',
            '_ngrh_hero_image_pc_id'        => 'image',
            '_ngrh_hero_image_sp_id'        => 'image',
            '_ngrh_hero_image_id'           => 'image',

            '_ngrh_concept_eyebrow'         => 'text',
            '_ngrh_concept_title'           => 'text',
            '_ngrh_concept_text'            => 'textarea',
            '_ngrh_concept_image_id'        => 'image',

            '_ngrh_renovation_eyebrow'      => 'text',
            '_ngrh_renovation_title'        => 'text',
            '_ngrh_renovation_text'         => 'textarea',
            '_ngrh_renovation_image_id'     => 'image',

            '_ngrh_villa_eyebrow'           => 'text',
            '_ngrh_villa_title'             => 'text',
            '_ngrh_villa_text'              => 'textarea',
            '_ngrh_villa_image_id'          => 'image',

            '_ngrh_support_eyebrow'         => 'text',
            '_ngrh_support_title'           => 'text',
            '_ngrh_support_text'            => 'textarea',
            '_ngrh_support_image_id'        => 'image',
            '_ngrh_support_primary_text'    => 'text',
            '_ngrh_support_primary_url'     => 'url',
            '_ngrh_support_secondary_text'  => 'text',
            '_ngrh_support_secondary_url'   => 'url',

            '_ngrh_flow_eyebrow'            => 'text',
            '_ngrh_flow_title'              => 'text',
            '_ngrh_flow_step_1'             => 'text',
            '_ngrh_flow_step_2'             => 'text',
            '_ngrh_flow_step_3'             => 'text',
            '_ngrh_flow_step_4'             => 'text',
            '_ngrh_flow_step_5'             => 'text',

            '_ngrh_contact_eyebrow'         => 'text',
            '_ngrh_contact_title'           => 'text',
            '_ngrh_contact_text'            => 'textarea',
            '_ngrh_contact_cta_text'        => 'text',
            '_ngrh_contact_cta_url'         => 'url',
            '_ngrh_contact_image_id'        => 'image',
        );
    }
}

if (!function_exists('ngrh_get_used_renovation_meta')) {
    function ngrh_get_used_renovation_meta($post_id, $key, $default = '')
    {
        $value = get_post_meta($post_id, $key, true);
        return ($value !== '' && $value !== null) ? $value : $default;
    }
}

if (!function_exists('ngrh_used_renovation_defaults')) {
    function ngrh_used_renovation_defaults()
    {
        return array(
            '_ngrh_hero_eyebrow'           => 'Renovation Residence',
            '_ngrh_hero_title'             => '那須で見つける、中古住宅リノベと別荘の暮らし',
            '_ngrh_hero_text'              => '中古住宅や別荘をそのまま買うのではなく、那須での暮らし方に合わせて整え直す。物件探しからリノベーションのご相談まで、まとめてご案内します。',
            '_ngrh_hero_primary_text'      => '中古住宅・別荘を相談する',
            '_ngrh_hero_primary_url'       => home_url('/reservation'),
            '_ngrh_hero_secondary_text'    => '施工実例を見る',
            '_ngrh_hero_secondary_url'     => home_url('/sekou-jirei'),

            '_ngrh_concept_eyebrow'        => 'Concept',
            '_ngrh_concept_title'          => '中古住宅を、那須で心地よく暮らせる住まいへ',
            '_ngrh_concept_text'           => '立地や建物の状態を見ながら、購入後にどこを整えると暮らしやすくなるかを考えるのが、中古住宅リノベの魅力です。定住用にも、週末住宅や別荘にも合わせてご提案できます。',

            '_ngrh_renovation_eyebrow'     => 'Renovation',
            '_ngrh_renovation_title'       => 'リフォーム済みの完成イメージまで見据えてご提案',
            '_ngrh_renovation_text'        => '内装の更新だけでなく、間取りの見直し、断熱・設備更新、外装やデッキまわりまで含めて、購入前から完成後のイメージを整理しやすいLP構成にしています。',

            '_ngrh_villa_eyebrow'          => 'Villa Life',
            '_ngrh_villa_title'            => '別荘・二拠点生活にも合う中古住宅リノベ',
            '_ngrh_villa_text'             => '那須では、居住用だけでなく別荘やセカンドハウスとして中古住宅を探す方も多くいます。過ごし方や利用頻度に応じて、必要な改修範囲を考えながらご相談いただけます。',

            '_ngrh_support_eyebrow'        => 'Support',
            '_ngrh_support_title'          => '物件探しから改修相談まで、一体で進めやすい構成',
            '_ngrh_support_text'           => '中古住宅・別荘探し、購入判断、リノベの方向性整理までを、ページ内で流れよく案内できるようにしています。LPとして使いやすいよう、CTAも専用で持たせています。',
            '_ngrh_support_primary_text'   => '来店予約',
            '_ngrh_support_primary_url'    => home_url('/reservation'),
            '_ngrh_support_secondary_text' => 'お部屋ギャラリーを見る',
            '_ngrh_support_secondary_url'  => home_url('/room-gallary'),

            '_ngrh_flow_eyebrow'           => 'Flow',
            '_ngrh_flow_title'             => 'ご相談からご案内までの流れ',
            '_ngrh_flow_step_1'            => 'ご希望条件の整理',
            '_ngrh_flow_step_2'            => '中古住宅・別荘のご紹介',
            '_ngrh_flow_step_3'            => 'リノベ内容の方向性確認',
            '_ngrh_flow_step_4'            => '購入・改修のご相談',
            '_ngrh_flow_step_5'            => '完成後の暮らしをイメージ',

            '_ngrh_contact_eyebrow'        => 'Contact',
            '_ngrh_contact_title'          => '那須の中古住宅リノベについて相談する',
            '_ngrh_contact_text'           => '中古住宅の購入、別荘活用、リノベーションの方向性など、お気軽にご相談ください。',
            '_ngrh_contact_cta_text'       => 'ご相談はこちら',
            '_ngrh_contact_cta_url'        => home_url('/reservation'),
        );
    }
}

if (!function_exists('ngrh_used_renovation_meta_with_default')) {
    function ngrh_used_renovation_meta_with_default($post_id, $key, $fallback = '')
    {
        $defaults = ngrh_used_renovation_defaults();
        $default  = array_key_exists($key, $defaults) ? $defaults[$key] : $fallback;
        return ngrh_get_used_renovation_meta($post_id, $key, $default);
    }
}

if (!function_exists('ngrh_render_media_field')) {
    function ngrh_render_media_field($name, $value = 0)
    {
        $value   = absint($value);
        $preview = $value ? wp_get_attachment_image_url($value, 'medium') : '';
        ?>
        <div class="ngrh-media-field">
            <input type="hidden" name="<?php echo esc_attr($name); ?>" value="<?php echo esc_attr($value); ?>" class="ngrh-media-input">
            <div class="ngrh-media-preview" style="margin:8px 0;">
                <?php if ($preview) : ?>
                    <img src="<?php echo esc_url($preview); ?>" alt="" style="max-width:240px;height:auto;border:1px solid #d0d7de;border-radius:8px;display:block;">
                <?php else : ?>
                    <div style="padding:14px;border:1px dashed #c3cad4;border-radius:8px;color:#667085;background:#fafbfc;">画像未選択</div>
                <?php endif; ?>
            </div>
            <p style="display:flex;gap:8px;flex-wrap:wrap;margin:0;">
                <button type="button" class="button button-secondary ngrh-media-select">画像を選択</button>
                <button type="button" class="button ngrh-media-remove">画像を外す</button>
            </p>
        </div>
        <?php
    }
}

if (!function_exists('ngrh_render_text_field')) {
    function ngrh_render_text_field($name, $label, $value = '')
    {
        ?>
        <p style="margin:0 0 16px;">
            <label style="display:block;font-weight:600;margin:0 0 6px;"><?php echo esc_html($label); ?></label>
            <input type="text" name="<?php echo esc_attr($name); ?>" value="<?php echo esc_attr($value); ?>" style="width:100%;">
        </p>
        <?php
    }
}

if (!function_exists('ngrh_render_textarea_field')) {
    function ngrh_render_textarea_field($name, $label, $value = '', $rows = 4)
    {
        ?>
        <p style="margin:0 0 16px;">
            <label style="display:block;font-weight:600;margin:0 0 6px;"><?php echo esc_html($label); ?></label>
            <textarea name="<?php echo esc_attr($name); ?>" rows="<?php echo (int) $rows; ?>" style="width:100%;"><?php echo esc_textarea($value); ?></textarea>
        </p>
        <?php
    }
}

if (!function_exists('ngrh_add_used_renovation_metabox')) {
    function ngrh_add_used_renovation_metabox($post)
    {
        if (!$post instanceof WP_Post || $post->post_type !== 'page' || !ngrh_is_used_renovation_template($post->ID)) {
            return;
        }

        add_meta_box(
            'ngrh_used_renovation_metabox',
            '中古住宅リノベLP設定',
            'ngrh_render_used_renovation_metabox',
            'page',
            'normal',
            'high'
        );
    }
}
add_action('add_meta_boxes_page', 'ngrh_add_used_renovation_metabox');

if (!function_exists('ngrh_render_used_renovation_metabox')) {
    function ngrh_render_used_renovation_metabox($post)
    {
        wp_nonce_field('ngrh_save_used_renovation_metabox', 'ngrh_used_renovation_nonce');

        $defaults = ngrh_used_renovation_defaults();
        $values   = array();

        foreach ($defaults as $key => $default) {
            $values[$key] = ngrh_used_renovation_meta_with_default($post->ID, $key, $default);
        }

        foreach (array(
            '_ngrh_hero_image_pc_id',
            '_ngrh_hero_image_sp_id',
            '_ngrh_hero_image_id',
            '_ngrh_concept_image_id',
            '_ngrh_renovation_image_id',
            '_ngrh_villa_image_id',
            '_ngrh_support_image_id',
            '_ngrh_contact_image_id',
        ) as $image_key) {
            $values[$image_key] = absint(get_post_meta($post->ID, $image_key, true));
        }
        ?>
        <div style="display:grid;gap:20px;">

            <section style="padding:16px;border:1px solid #d0d7de;border-radius:10px;background:#fff;">
                <h3 style="margin:0 0 16px;">Hero</h3>
                <?php ngrh_render_text_field('_ngrh_hero_eyebrow', 'アイブロー', $values['_ngrh_hero_eyebrow']); ?>
                <?php ngrh_render_text_field('_ngrh_hero_title', 'タイトル', $values['_ngrh_hero_title']); ?>
                <?php ngrh_render_textarea_field('_ngrh_hero_text', '説明文', $values['_ngrh_hero_text'], 4); ?>
                <?php ngrh_render_text_field('_ngrh_hero_primary_text', 'CTA1文言', $values['_ngrh_hero_primary_text']); ?>
                <?php ngrh_render_text_field('_ngrh_hero_primary_url', 'CTA1 URL', $values['_ngrh_hero_primary_url']); ?>
                <?php ngrh_render_text_field('_ngrh_hero_secondary_text', 'CTA2文言', $values['_ngrh_hero_secondary_text']); ?>
                <?php ngrh_render_text_field('_ngrh_hero_secondary_url', 'CTA2 URL', $values['_ngrh_hero_secondary_url']); ?>
                <p style="margin:0 0 8px;font-weight:600;">Hero画像（PC）</p>
                <?php ngrh_render_media_field('_ngrh_hero_image_pc_id', $values['_ngrh_hero_image_pc_id']); ?>
                <p style="margin:16px 0 8px;font-weight:600;">Hero画像（SP）</p>
                <?php ngrh_render_media_field('_ngrh_hero_image_sp_id', $values['_ngrh_hero_image_sp_id']); ?>
            </section>

            <section style="padding:16px;border:1px solid #d0d7de;border-radius:10px;background:#fff;">
                <h3 style="margin:0 0 16px;">Concept</h3>
                <?php ngrh_render_text_field('_ngrh_concept_eyebrow', 'アイブロー', $values['_ngrh_concept_eyebrow']); ?>
                <?php ngrh_render_text_field('_ngrh_concept_title', '見出し', $values['_ngrh_concept_title']); ?>
                <?php ngrh_render_textarea_field('_ngrh_concept_text', '本文', $values['_ngrh_concept_text'], 5); ?>
                <p style="margin:0 0 8px;font-weight:600;">Concept画像</p>
                <?php ngrh_render_media_field('_ngrh_concept_image_id', $values['_ngrh_concept_image_id']); ?>
            </section>

            <section style="padding:16px;border:1px solid #d0d7de;border-radius:10px;background:#fff;">
                <h3 style="margin:0 0 16px;">Renovation</h3>
                <?php ngrh_render_text_field('_ngrh_renovation_eyebrow', 'アイブロー', $values['_ngrh_renovation_eyebrow']); ?>
                <?php ngrh_render_text_field('_ngrh_renovation_title', '見出し', $values['_ngrh_renovation_title']); ?>
                <?php ngrh_render_textarea_field('_ngrh_renovation_text', '本文', $values['_ngrh_renovation_text'], 5); ?>
                <p style="margin:0 0 8px;font-weight:600;">リノベ完成イメージ画像</p>
                <?php ngrh_render_media_field('_ngrh_renovation_image_id', $values['_ngrh_renovation_image_id']); ?>
            </section>

            <section style="padding:16px;border:1px solid #d0d7de;border-radius:10px;background:#fff;">
                <h3 style="margin:0 0 16px;">Villa / Second House</h3>
                <?php ngrh_render_text_field('_ngrh_villa_eyebrow', 'アイブロー', $values['_ngrh_villa_eyebrow']); ?>
                <?php ngrh_render_text_field('_ngrh_villa_title', '見出し', $values['_ngrh_villa_title']); ?>
                <?php ngrh_render_textarea_field('_ngrh_villa_text', '本文', $values['_ngrh_villa_text'], 5); ?>
                <p style="margin:0 0 8px;font-weight:600;">別荘イメージ画像</p>
                <?php ngrh_render_media_field('_ngrh_villa_image_id', $values['_ngrh_villa_image_id']); ?>
            </section>

            <section style="padding:16px;border:1px solid #d0d7de;border-radius:10px;background:#fff;">
                <h3 style="margin:0 0 16px;">Support</h3>
                <?php ngrh_render_text_field('_ngrh_support_eyebrow', 'アイブロー', $values['_ngrh_support_eyebrow']); ?>
                <?php ngrh_render_text_field('_ngrh_support_title', '見出し', $values['_ngrh_support_title']); ?>
                <?php ngrh_render_textarea_field('_ngrh_support_text', '本文', $values['_ngrh_support_text'], 5); ?>
                <p style="margin:0 0 8px;font-weight:600;">Support画像</p>
                <?php ngrh_render_media_field('_ngrh_support_image_id', $values['_ngrh_support_image_id']); ?>
                <?php ngrh_render_text_field('_ngrh_support_primary_text', 'CTA1文言', $values['_ngrh_support_primary_text']); ?>
                <?php ngrh_render_text_field('_ngrh_support_primary_url', 'CTA1 URL', $values['_ngrh_support_primary_url']); ?>
                <?php ngrh_render_text_field('_ngrh_support_secondary_text', 'CTA2文言', $values['_ngrh_support_secondary_text']); ?>
                <?php ngrh_render_text_field('_ngrh_support_secondary_url', 'CTA2 URL', $values['_ngrh_support_secondary_url']); ?>
            </section>

            <section style="padding:16px;border:1px solid #d0d7de;border-radius:10px;background:#fff;">
                <h3 style="margin:0 0 16px;">Flow</h3>
                <?php ngrh_render_text_field('_ngrh_flow_eyebrow', 'アイブロー', $values['_ngrh_flow_eyebrow']); ?>
                <?php ngrh_render_text_field('_ngrh_flow_title', '見出し', $values['_ngrh_flow_title']); ?>
                <?php ngrh_render_text_field('_ngrh_flow_step_1', 'STEP 1', $values['_ngrh_flow_step_1']); ?>
                <?php ngrh_render_text_field('_ngrh_flow_step_2', 'STEP 2', $values['_ngrh_flow_step_2']); ?>
                <?php ngrh_render_text_field('_ngrh_flow_step_3', 'STEP 3', $values['_ngrh_flow_step_3']); ?>
                <?php ngrh_render_text_field('_ngrh_flow_step_4', 'STEP 4', $values['_ngrh_flow_step_4']); ?>
                <?php ngrh_render_text_field('_ngrh_flow_step_5', 'STEP 5', $values['_ngrh_flow_step_5']); ?>
            </section>

            <section style="padding:16px;border:1px solid #d0d7de;border-radius:10px;background:#fff;">
                <h3 style="margin:0 0 16px;">Contact</h3>
                <?php ngrh_render_text_field('_ngrh_contact_eyebrow', 'アイブロー', $values['_ngrh_contact_eyebrow']); ?>
                <?php ngrh_render_text_field('_ngrh_contact_title', '見出し', $values['_ngrh_contact_title']); ?>
                <?php ngrh_render_textarea_field('_ngrh_contact_text', '本文', $values['_ngrh_contact_text'], 4); ?>
                <?php ngrh_render_text_field('_ngrh_contact_cta_text', 'CTA文言', $values['_ngrh_contact_cta_text']); ?>
                <?php ngrh_render_text_field('_ngrh_contact_cta_url', 'CTA URL', $values['_ngrh_contact_cta_url']); ?>
                <p style="margin:0 0 8px;font-weight:600;">CTA背景画像</p>
                <?php ngrh_render_media_field('_ngrh_contact_image_id', $values['_ngrh_contact_image_id']); ?>
            </section>

        </div>
        <?php
    }
}

if (!function_exists('ngrh_save_used_renovation_metabox')) {
    function ngrh_save_used_renovation_metabox($post_id)
    {
        if (!isset($_POST['ngrh_used_renovation_nonce'])) {
            return;
        }

        $nonce = sanitize_text_field(wp_unslash($_POST['ngrh_used_renovation_nonce']));
        if (!wp_verify_nonce($nonce, 'ngrh_save_used_renovation_metabox')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (wp_is_post_revision($post_id) || get_post_type($post_id) !== 'page' || !current_user_can('edit_page', $post_id)) {
            return;
        }

        $template = isset($_POST['_wp_page_template'])
            ? sanitize_text_field(wp_unslash($_POST['_wp_page_template']))
            : get_page_template_slug($post_id);

        if ($template !== ngrh_used_renovation_template_slug()) {
            return;
        }

        foreach (ngrh_used_renovation_fields() as $field_key => $field_type) {
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
add_action('save_post_page', 'ngrh_save_used_renovation_metabox');

if (!function_exists('ngrh_used_renovation_admin_enqueue')) {
    function ngrh_used_renovation_admin_enqueue($hook)
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
add_action('admin_enqueue_scripts', 'ngrh_used_renovation_admin_enqueue');

if (!function_exists('ngrh_used_renovation_admin_footer_script')) {
    function ngrh_used_renovation_admin_footer_script()
    {
        $screen = get_current_screen();
        if (!$screen || $screen->post_type !== 'page') {
            return;
        }
        ?>
        <script>
            jQuery(function($) {
                $(document).on('click', '.ngrh-media-select', function(e) {
                    e.preventDefault();

                    const $field   = $(this).closest('.ngrh-media-field');
                    const $input   = $field.find('.ngrh-media-input');
                    const $preview = $field.find('.ngrh-media-preview');

                    const frame = wp.media({
                        title: '画像を選択',
                        button: {
                            text: 'この画像を使う'
                        },
                        multiple: false
                    });

                    frame.on('select', function() {
                        const attachment = frame.state().get('selection').first().toJSON();
                        const imageUrl   = attachment.sizes && attachment.sizes.medium ? attachment.sizes.medium.url : attachment.url;

                        $input.val(attachment.id);
                        $preview.html('<img src="' + imageUrl + '" alt="" style="max-width:240px;height:auto;border:1px solid #d0d7de;border-radius:8px;display:block;">');
                    });

                    frame.open();
                });

                $(document).on('click', '.ngrh-media-remove', function(e) {
                    e.preventDefault();

                    const $field = $(this).closest('.ngrh-media-field');
                    $field.find('.ngrh-media-input').val('');
                    $field.find('.ngrh-media-preview').html('<div style="padding:14px;border:1px dashed #c3cad4;border-radius:8px;color:#667085;background:#fafbfc;">画像未選択</div>');
                });
            });
        </script>
        <?php
    }
}
add_action('admin_footer-post.php', 'ngrh_used_renovation_admin_footer_script');
add_action('admin_footer-post-new.php', 'ngrh_used_renovation_admin_footer_script');
