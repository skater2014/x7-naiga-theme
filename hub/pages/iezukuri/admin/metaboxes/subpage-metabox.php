<?php
/**
 * hub/pages/iezukuri/admin/metaboxes/subpage-metabox.php
 *
 * 家づくり 固定ページ入力
 *
 * 対象:
 * - post_type = page
 * - /iezukuri/ トップページ
 * - /iezukuri/ 配下の固定ページ
 *
 * 対象外:
 * - 間取り詳細 CPT: iez_plan
 *
 * 役割:
 * - 固定ページの Hero / トップ本文 / 詳細本文 / CTA を編集する。
 * - 管理画面CSS/JSは admin/enqueue.php に任せる。
 * - ここでは wp_enqueue_* を書かない。
 */

if (!defined('ABSPATH')) {
    exit;
}

$naigai_iez_metabox_part_files = array(
    __DIR__ . '/parts/hero-metabox.php',
    __DIR__ . '/parts/top-metabox.php',
    __DIR__ . '/chuko-metabox.php',
);

foreach ($naigai_iez_metabox_part_files as $naigai_iez_metabox_part_file) {
    if (file_exists($naigai_iez_metabox_part_file)) {
        require_once $naigai_iez_metabox_part_file;
    }
}


if (!function_exists('naigai_iez_admin_is_fixed_page_target')) {
    function naigai_iez_admin_is_fixed_page_target($post) {
        if (!$post || $post->post_type !== 'page') {
            return false;
        }

        $uri = get_page_uri($post);

        return $uri === 'iezukuri' || strpos($uri, 'iezukuri/') === 0;
    }
}

if (!function_exists('naigai_iez_admin_is_top_page')) {
    function naigai_iez_admin_is_top_page($post) {
        return $post && $post->post_type === 'page' && get_page_uri($post) === 'iezukuri';
    }
}

if (!function_exists('naigai_iez_admin_get_meta')) {
    function naigai_iez_admin_get_meta($post_id, $key, $default = '') {
        $value = get_post_meta($post_id, $key, true);

        if ($value !== '') {
            return $value;
        }

        $fallbacks = array(
            '_ch_hero_gallery_ids' => array('_hub_ch_hero_gallery_ids', '_hub_hero_gallery_ids'),
            '_ch_hero_image_id' => array('_ch_subpage_hero_image_id', '_hub_ch_hero_image_id'),
            '_ch_hero_video_mp4_id' => array('_hub_ch_hero_video_mp4_id', '_mpb_hero_video_mp4_id'),
        );

        if (!empty($fallbacks[$key])) {
            foreach ($fallbacks[$key] as $old_key) {
                $old_value = get_post_meta($post_id, $old_key, true);
                if ($old_value !== '') {
                    return $old_value;
                }
            }
        }

        return $default;
    }
}

if (!function_exists('naigai_iez_admin_icon_choices')) {
    function naigai_iez_admin_icon_choices() {
        if (function_exists('naigai_iez_icon_choices')) {
            return naigai_iez_icon_choices();
        }

        return array(
            'new-house'       => '新築住宅',
            'hiraya'          => '平屋',
            'two-story'       => '二階建て住宅',
            'two-family'      => '二世帯住宅',
            'renovation'      => '住宅リフォーム',
            'used-renovation' => '中古住宅リフォーム',
            'dual-life'       => '二拠点生活',
            'consultation'    => '相談',
        );
    }
}

if (!function_exists('naigai_iez_admin_text_input')) {
    function naigai_iez_admin_text_input($id, $label, $value, $description = '') {
        ?>
        <tr>
            <th scope="row"><label for="<?php echo esc_attr($id); ?>"><?php echo esc_html($label); ?></label></th>
            <td>
                <input type="text" id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($id); ?>" value="<?php echo esc_attr($value); ?>" class="regular-text">
                <?php if ($description !== '') : ?>
                    <p class="description"><?php echo esc_html($description); ?></p>
                <?php endif; ?>
            </td>
        </tr>
        <?php
    }
}

if (!function_exists('naigai_iez_admin_url_input')) {
    function naigai_iez_admin_url_input($id, $label, $value, $description = '') {
        ?>
        <tr>
            <th scope="row"><label for="<?php echo esc_attr($id); ?>"><?php echo esc_html($label); ?></label></th>
            <td>
                <input type="url" id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($id); ?>" value="<?php echo esc_attr($value); ?>" class="regular-text">
                <?php if ($description !== '') : ?>
                    <p class="description"><?php echo esc_html($description); ?></p>
                <?php endif; ?>
            </td>
        </tr>
        <?php
    }
}

if (!function_exists('naigai_iez_admin_textarea')) {
    function naigai_iez_admin_textarea($id, $label, $value, $rows = 5, $description = '') {
        ?>
        <tr>
            <th scope="row"><label for="<?php echo esc_attr($id); ?>"><?php echo esc_html($label); ?></label></th>
            <td>
                <textarea id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($id); ?>" rows="<?php echo esc_attr($rows); ?>" class="large-text"><?php echo esc_textarea($value); ?></textarea>
                <?php if ($description !== '') : ?>
                    <p class="description"><?php echo esc_html($description); ?></p>
                <?php endif; ?>
            </td>
        </tr>
        <?php
    }
}

if (!function_exists('naigai_iez_admin_media_input')) {
    function naigai_iez_admin_media_input($id, $label, $value, $media_type = 'image', $multiple = false, $description = '') {
        $button_label = $media_type === 'video' ? 'MP4を選択' : ($multiple ? '画像を複数選択' : '画像を選択');
        ?>
        <tr>
            <th scope="row"><label for="<?php echo esc_attr($id); ?>"><?php echo esc_html($label); ?></label></th>
            <td>
                <input type="text" id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($id); ?>" value="<?php echo esc_attr($value); ?>" class="regular-text">
                <button
                    type="button"
                    class="button <?php echo $multiple ? 'button-primary' : ''; ?>"
                    data-iez-media-target="#<?php echo esc_attr($id); ?>"
                    data-iez-media-type="<?php echo esc_attr($media_type); ?>"
                    <?php echo $multiple ? 'data-iez-media-multiple="1"' : ''; ?>
                    data-iez-media-title="<?php echo esc_attr($label); ?>"
                    data-iez-media-button="このメディアを使う"
                ><?php echo esc_html($button_label); ?></button>
                <button
                    type="button"
                    class="button"
                    data-iez-media-clear="#<?php echo esc_attr($id); ?>"
                >クリア</button>

                <div class="naigai-iez-media-preview" data-iez-preview-for="<?php echo esc_attr($id); ?>"></div>

                <?php if ($description !== '') : ?>
                    <p class="description"><?php echo esc_html($description); ?></p>
                <?php endif; ?>
            </td>
        </tr>
        <?php
    }
}

if (!function_exists('naigai_iez_admin_icon_select')) {
    function naigai_iez_admin_icon_select($id, $label, $value) {
        $choices = naigai_iez_admin_icon_choices();
        ?>
        <tr>
            <th scope="row"><label for="<?php echo esc_attr($id); ?>"><?php echo esc_html($label); ?></label></th>
            <td>
                <select id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($id); ?>">
                    <?php foreach ($choices as $key => $name) : ?>
                        <option value="<?php echo esc_attr($key); ?>" <?php selected($value, $key); ?>>
                            <?php echo esc_html($name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <p class="description">SVGアイコンは inc/icon-catalog.php で管理します。</p>
            </td>
        </tr>
        <?php
    }
}

add_action('add_meta_boxes', function ($post_type, $post) {
    if ($post_type !== 'page') {
        return;
    }

    if (!naigai_iez_admin_is_fixed_page_target($post)) {
        return;
    }

    add_meta_box(
        'naigai_iez_fixed_page_input',
        '家づくり 固定ページ入力',
        'naigai_iez_render_fixed_page_input_metabox',
        'page',
        'normal',
        'high'
    );
}, 10, 2);

if (!function_exists('naigai_iez_render_fixed_page_input_metabox')) {
    function naigai_iez_render_fixed_page_input_metabox($post) {
        wp_nonce_field('naigai_iez_save_fixed_page_input', 'naigai_iez_fixed_page_nonce');

        $get = function ($key, $default = '') use ($post) {
            return naigai_iez_admin_get_meta($post->ID, $key, $default);
        };

        $is_top = naigai_iez_admin_is_top_page($post);
        $is_chuko = function_exists('naigai_iez_admin_is_chuko_page') && naigai_iez_admin_is_chuko_page($post);

        if (function_exists('naigai_iez_admin_render_hero_input')) {
            naigai_iez_admin_render_hero_input($post, $get);
        }

        if ($is_top && function_exists('naigai_iez_admin_render_top_input')) {
            naigai_iez_admin_render_top_input($post, $get);
        } elseif ($is_chuko && function_exists('naigai_iez_admin_render_chuko_input')) {
            naigai_iez_admin_render_chuko_input($post);
        } else {
            ?>
            <div class="naigai-iez-admin-section">
                <h3>現在ページの本文</h3>

                <table class="form-table naigai-iez-admin-table">
                    <tbody>
                        <?php
                        naigai_iez_admin_text_input('_ch_intro_kicker', '導入キッカー', $get('_ch_intro_kicker', ''));
                        naigai_iez_admin_text_input('_ch_intro_title', '導入見出し', $get('_ch_intro_title', ''));
                        naigai_iez_admin_textarea('_ch_intro_text', '導入本文', $get('_ch_intro_text', ''), 5);

                        naigai_iez_admin_text_input('_ch_body_title', '本文見出し', $get('_ch_body_title', ''));
                        naigai_iez_admin_textarea('_ch_body_text', '本文', $get('_ch_body_text', ''), 8);
                        naigai_iez_admin_media_input('_ch_body_image_id', '本文画像ID', $get('_ch_body_image_id', ''), 'image');
                        naigai_iez_admin_media_input('_ch_gallery_ids', 'ギャラリー画像ID', $get('_ch_gallery_ids', ''), 'image', true, '複数画像はカンマ区切りで保存されます。');
                        ?>
                    </tbody>
                </table>
            </div>
            <?php
        }
        ?>

        <div class="naigai-iez-admin-section">
            <h3>CTA</h3>

            <table class="form-table naigai-iez-admin-table">
                <tbody>
                    <?php
                    naigai_iez_admin_text_input('_ch_cta_title', 'CTA見出し', $get('_ch_cta_title', ''));
                    naigai_iez_admin_textarea('_ch_cta_text', 'CTA本文', $get('_ch_cta_text', ''), 4);
                    naigai_iez_admin_text_input('_ch_cta_button_text', 'ボタン文言', $get('_ch_cta_button_text', ''));
                    naigai_iez_admin_url_input('_ch_cta_button_url', 'ボタンURL', $get('_ch_cta_button_url', ''));
                    naigai_iez_admin_media_input('_ch_cta_image_id', 'CTA画像ID', $get('_ch_cta_image_id', ''), 'image');
                    ?>
                </tbody>
            </table>
        </div>
        <?php
    }
}


add_action('save_post_page', function ($post_id) {
    if (!isset($_POST['naigai_iez_fixed_page_nonce'])) {
        return;
    }

    if (!wp_verify_nonce($_POST['naigai_iez_fixed_page_nonce'], 'naigai_iez_save_fixed_page_input')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_page', $post_id)) {
        return;
    }

    $post = get_post($post_id);

    if (!naigai_iez_admin_is_fixed_page_target($post)) {
        return;
    }

    $fields = array(
        '_ch_hero_kicker' => 'text',
        '_ch_hero_title' => 'text',
        '_ch_hero_lead' => 'textarea',
        '_ch_hero_image_id' => 'number',
        '_ch_hero_gallery_ids' => 'ids',
        '_ch_hero_engine' => 'key',
        '_ch_hero_video_mp4_id' => 'number',
        '_ch_hero_motion' => 'key',
        '_ch_hero_interval' => 'number',

        '_ch_top_intro_kicker' => 'text',
        '_ch_top_intro_title' => 'text',
        '_ch_top_intro_text' => 'textarea',
        '_ch_top_site_reading_title' => 'text',
        '_ch_top_site_reading_text' => 'textarea',
        '_ch_top_site_reading_image_id' => 'number',
        '_ch_top_works_title' => 'text',
        '_ch_top_works_text' => 'textarea',
        '_ch_top_works_image_id' => 'number',
        '_ch_top_plans_title' => 'text',
        '_ch_top_plans_text' => 'textarea',
        '_ch_top_plans_image_id' => 'number',
        '_ch_top_flow_title' => 'text',
        '_ch_top_flow_text' => 'textarea',

        '_ch_intro_kicker' => 'text',
        '_ch_intro_title' => 'text',
        '_ch_intro_text' => 'textarea',
        '_ch_body_title' => 'text',
        '_ch_body_text' => 'textarea',
        '_ch_body_image_id' => 'number',
        '_ch_gallery_ids' => 'ids',

        '_ch_cta_title' => 'text',
        '_ch_cta_text' => 'textarea',
        '_ch_cta_button_text' => 'text',
        '_ch_cta_button_url' => 'url',
        '_ch_cta_image_id' => 'number',
    );

    $fields = apply_filters('naigai_iez_admin_fixed_page_fields', $fields, $post);

    for ($i = 1; $i <= 3; $i++) {
        $fields["_ch_top_route_{$i}_icon"] = 'key';
        $fields["_ch_top_route_{$i}_title"] = 'text';
        $fields["_ch_top_route_{$i}_text"] = 'textarea';
        $fields["_ch_top_route_{$i}_url"] = 'url';
        $fields["_ch_top_route_{$i}_image_id"] = 'number';
    }

    foreach ($fields as $field => $type) {
        if (!isset($_POST[$field])) {
            continue;
        }

        $value = wp_unslash($_POST[$field]);

        if ($type === 'number') {
            $value = preg_replace('/[^0-9]/', '', $value);
        } elseif ($type === 'ids') {
            $value = preg_replace('/[^0-9,]/', '', $value);
            $value = trim($value, ',');
        } elseif ($type === 'key') {
            $value = sanitize_key($value);
        } elseif ($type === 'url') {
            $value = esc_url_raw($value);
        } elseif ($type === 'textarea') {
            $value = sanitize_textarea_field($value);
        } else {
            $value = sanitize_text_field($value);
        }

        update_post_meta($post_id, $field, $value);
    }

    /*
     * 旧キーにもミラー保存。
     * 目的:
     * - 既存 renderer / template が旧キーを読んでいても反映させる。
     * - 移行中の「選んだのに表示されない」を防ぐ。
     */
    $gallery_ids = get_post_meta($post_id, '_ch_hero_gallery_ids', true);
    if ($gallery_ids !== '') {
        update_post_meta($post_id, '_hub_ch_hero_gallery_ids', $gallery_ids);
        update_post_meta($post_id, '_hub_hero_gallery_ids', $gallery_ids);
    }

    $image_id = get_post_meta($post_id, '_ch_hero_image_id', true);
    if ($image_id !== '') {
        update_post_meta($post_id, '_ch_subpage_hero_image_id', $image_id);
        update_post_meta($post_id, '_hub_ch_hero_image_id', $image_id);
    }

    $video_id = get_post_meta($post_id, '_ch_hero_video_mp4_id', true);
    if ($video_id !== '') {
        update_post_meta($post_id, '_hub_ch_hero_video_mp4_id', $video_id);
    }
});


/* IEZ_HERO_EXISTING_METABOX_UI_START */
/**
 * 既存メタボックス内 Hero UI 統合
 *
 * 役割:
 * - CTA用の別メタボックスは作らない。
 * - 既存の Hero CTA URL 入力欄の横に固定ページURL選択を出す。
 * - 編集画面をコンパクトにする。
 * - CTA保存メタキーは _ch_hero_* に統一する。
 */
if (!function_exists('naigai_iez_existing_metabox_hero_engine')) {
    function naigai_iez_existing_metabox_hero_engine($engine) {
        $engine = sanitize_key((string) $engine);

        if ($engine === 'fade') {
            return 'burns';
        }

        $allowed = array('image', 'swiper', 'video', 'burns');
        return in_array($engine, $allowed, true) ? $engine : 'burns';
    }
}

if (!function_exists('naigai_iez_existing_metabox_hero_save')) {
    function naigai_iez_existing_metabox_hero_save($post_id) {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        if (
            !isset($_POST['naigai_iezukuri_subpage_nonce']) ||
            !wp_verify_nonce(
                sanitize_text_field(wp_unslash($_POST['naigai_iezukuri_subpage_nonce'])),
                'naigai_iezukuri_subpage_save'
            )
        ) {
            return;
        }

        $text_fields = array(
            '_ch_hero_cta_text',
            '_ch_hero_sub_cta_text',
        );

        foreach ($text_fields as $key) {
            $value = isset($_POST[$key]) ? sanitize_text_field(wp_unslash($_POST[$key])) : '';

            if ($value === '') {
                delete_post_meta($post_id, $key);
            } else {
                update_post_meta($post_id, $key, $value);
            }
        }

        $url_fields = array(
            '_ch_hero_cta_url',
            '_ch_hero_sub_cta_url',
        );

        foreach ($url_fields as $key) {
            $value = isset($_POST[$key]) ? esc_url_raw(wp_unslash($_POST[$key])) : '';

            if ($value === '') {
                delete_post_meta($post_id, $key);
            } else {
                update_post_meta($post_id, $key, $value);
            }
        }

        if (isset($_POST['_ch_hero_engine'])) {
            update_post_meta(
                $post_id,
                '_ch_hero_engine',
                naigai_iez_existing_metabox_hero_engine(wp_unslash($_POST['_ch_hero_engine']))
            );
        }
    }
}

add_action('save_post_page', 'naigai_iez_existing_metabox_hero_save', 40);

if (!function_exists('naigai_iez_existing_metabox_hero_admin_ui')) {
    function naigai_iez_existing_metabox_hero_admin_ui() {
        global $post;

        if (!$post || $post->post_type !== 'page') {
            return;
        }

        $pages = get_pages(array(
            'sort_column' => 'menu_order,post_title',
            'sort_order'  => 'ASC',
            'post_status' => array('publish', 'draft', 'private'),
        ));

        $choices = array();

        foreach ($pages as $page) {
            $url = get_permalink($page->ID);
            $path = trim((string) parse_url($url, PHP_URL_PATH), '/');

            $choices[] = array(
                'url'   => $url,
                'label' => ($path !== '' ? '/' . $path . '/' : $page->post_title) . ' - ' . $page->post_title,
            );
        }
        ?>
        <script>
        document.addEventListener('DOMContentLoaded', function () {
            var choices = <?php echo wp_json_encode($choices); ?>;

            function addPicker(targetName) {
                var input =
                    document.getElementById(targetName) ||
                    document.querySelector('[name="' + targetName + '"]');

                if (!input) {
                    return;
                }

                if (
                    input.nextElementSibling &&
                    input.nextElementSibling.classList &&
                    input.nextElementSibling.classList.contains('naigai-iez-existing-url-picker')
                ) {
                    return;
                }

                var select = document.createElement('select');
                select.className = 'naigai-iez-existing-url-picker';
                select.setAttribute('data-target', targetName);

                var first = document.createElement('option');
                first.value = '';
                first.textContent = '固定ページからURLを選択';
                select.appendChild(first);

                choices.forEach(function (choice) {
                    var option = document.createElement('option');
                    option.value = choice.url;
                    option.textContent = choice.label;
                    select.appendChild(option);
                });

                select.addEventListener('change', function () {
                    if (!select.value) {
                        return;
                    }

                    input.value = select.value;
                    input.dispatchEvent(new Event('change', { bubbles: true }));
                });

                input.insertAdjacentElement('afterend', select);
            }

            addPicker('_ch_hero_cta_url');
            addPicker('_ch_hero_sub_cta_url');
        });
        </script>
        <?php
    }
}

add_action('admin_footer-post.php', 'naigai_iez_existing_metabox_hero_admin_ui');
add_action('admin_footer-post-new.php', 'naigai_iez_existing_metabox_hero_admin_ui');
/* IEZ_HERO_EXISTING_METABOX_UI_END */


/* IEZ_HERO_MOTION_ADMIN_FINAL_START */
/**
 * Hero motion UI
 *
 * 画像:
 * - kenburns-top / bottom / left / right / none
 *
 * 見出し:
 * - caption / h1 / lead だけ
 *
 * CTA:
 * - 動かさない
 */
if (!function_exists('naigai_iez_admin_sanitize_motion_value')) {
    function naigai_iez_admin_sanitize_motion_value($motion) {
        $motion = sanitize_key((string) $motion);

        $legacy = array(
            'zoom-in'   => 'kenburns-top',
            'zoom-out'  => 'kenburns-bottom',
            'pan-left'  => 'kenburns-left',
            'pan-right' => 'kenburns-right',
            'pan-up'    => 'kenburns-top',
            'pan-down'  => 'kenburns-bottom',
            'fade'      => 'kenburns-top',
        );

        if (isset($legacy[$motion])) {
            return $legacy[$motion];
        }

        $allowed = array('none', 'kenburns-top', 'kenburns-bottom', 'kenburns-left', 'kenburns-right');
        return in_array($motion, $allowed, true) ? $motion : 'kenburns-top';
    }
}

if (!function_exists('naigai_iez_admin_sanitize_caption_motion')) {
    function naigai_iez_admin_sanitize_caption_motion($motion) {
        $motion = sanitize_key((string) $motion);
        $allowed = array('none', 'focus', 'slide-up');

        return in_array($motion, $allowed, true) ? $motion : 'none';
    }
}

add_action('save_post_page', function ($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (
        !isset($_POST['naigai_iezukuri_subpage_nonce']) ||
        !wp_verify_nonce(
            sanitize_text_field(wp_unslash($_POST['naigai_iezukuri_subpage_nonce'])),
            'naigai_iezukuri_subpage_save'
        )
    ) {
        return;
    }

    if (isset($_POST['_ch_hero_motion'])) {
        update_post_meta(
            $post_id,
            '_ch_hero_motion',
            naigai_iez_admin_sanitize_motion_value(wp_unslash($_POST['_ch_hero_motion']))
        );
    }

    if (isset($_POST['_ch_hero_caption_motion'])) {
        update_post_meta(
            $post_id,
            '_ch_hero_caption_motion',
            naigai_iez_admin_sanitize_caption_motion(wp_unslash($_POST['_ch_hero_caption_motion']))
        );
    }
}, 70);

if (!function_exists('naigai_iez_admin_motion_ui_final')) {
    function naigai_iez_admin_motion_ui_final() {
        global $post;

        if (!$post || $post->post_type !== 'page') {
            return;
        }

        $caption_motion = get_post_meta($post->ID, '_ch_hero_caption_motion', true);
        if ($caption_motion === '') {
            $caption_motion = 'none';
        }
        ?>
        <script>
        document.addEventListener('DOMContentLoaded', function () {
            var motion = document.querySelector('[name="_ch_hero_motion"]');

            if (motion) {
                var current = motion.value || 'kenburns-top';
                var map = {
                    'zoom-in': 'kenburns-top',
                    'zoom-out': 'kenburns-bottom',
                    'pan-left': 'kenburns-left',
                    'pan-right': 'kenburns-right',
                    'pan-up': 'kenburns-top',
                    'pan-down': 'kenburns-bottom',
                    'fade': 'kenburns-top',
                    'none': 'none',
                    'kenburns-top': 'kenburns-top',
                    'kenburns-bottom': 'kenburns-bottom',
                    'kenburns-left': 'kenburns-left',
                    'kenburns-right': 'kenburns-right'
                };

                current = map[current] || 'kenburns-top';

                motion.innerHTML = ''
                    + '<option value="kenburns-top">kenburns-top：上基準でゆっくり拡大</option>'
                    + '<option value="kenburns-bottom">kenburns-bottom：下基準でゆっくり拡大</option>'
                    + '<option value="kenburns-left">kenburns-left：左へ流しながら拡大</option>'
                    + '<option value="kenburns-right">kenburns-right：右へ流しながら拡大</option>'
                    + '<option value="none">none：動かさない</option>';

                motion.value = current;
            }

            if (!document.querySelector('[name="_ch_hero_caption_motion"]')) {
                var anchor =
                    document.querySelector('[name="_ch_hero_motion"]') ||
                    document.querySelector('[name="_ch_hero_interval"]');

                if (anchor) {
                    var tr = anchor.closest('tr');
                    var next = document.createElement('tr');

                    next.innerHTML = ''
                        + '<th scope="row"><label for="_ch_hero_caption_motion">Hero 見出しモーション</label></th>'
                        + '<td>'
                        + '<select id="_ch_hero_caption_motion" name="_ch_hero_caption_motion" class="regular-text">'
                        + '<option value="none">none：動かさない</option>'
                        + '<option value="focus">focus：文字をフォーカス表示</option>'
                        + '<option value="slide-up">slide-up：下から表示</option>'
                        + '</select>'
                        + '<p class="description">caption / H1 / lead だけに適用。CTAボタンは固定。</p>'
                        + '</td>';

                    tr.insertAdjacentElement('afterend', next);
                    next.querySelector('select').value = <?php echo wp_json_encode($caption_motion); ?>;
                }
            }
        });
        </script>
        <?php
    }
}

add_action('admin_footer-post.php', 'naigai_iez_admin_motion_ui_final');
add_action('admin_footer-post-new.php', 'naigai_iez_admin_motion_ui_final');
/* IEZ_HERO_MOTION_ADMIN_FINAL_END */


/* IEZ_HERO_SAVE_NEW_KEYS_ONLY_START */
/**
 * Hero保存先統一
 *
 * 方針:
 * - 新規保存は _ch_hero_* のみに統一。
 * - 旧 _hub_ch_hero_* / _ch_subpage_hero_* には保存しない。
 * - 追加メタボックスは作らない。
 */
if (!function_exists('naigai_iez_save_hero_new_keys_only')) {
    function naigai_iez_save_hero_new_keys_only($post_id) {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        if (
            !isset($_POST['naigai_iezukuri_subpage_nonce']) ||
            !wp_verify_nonce(
                sanitize_text_field(wp_unslash($_POST['naigai_iezukuri_subpage_nonce'])),
                'naigai_iezukuri_subpage_save'
            )
        ) {
            return;
        }

        $text_fields = array(
            '_ch_hero_engine',
            '_ch_hero_title',
            '_ch_hero_lead',
            '_ch_hero_eyebrow',
            '_ch_hero_motion',
            '_ch_hero_caption_motion',
            '_ch_hero_cta_text',
            '_ch_hero_sub_cta_text',
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

        $textarea_fields = array(
            '_ch_hero_lead',
        );

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

        $url_fields = array(
            '_ch_hero_cta_url',
            '_ch_hero_sub_cta_url',
        );

        foreach ($url_fields as $key) {
            if (!isset($_POST[$key])) {
                continue;
            }

            $value = esc_url_raw(wp_unslash($_POST[$key]));

            if ($value === '') {
                delete_post_meta($post_id, $key);
            } else {
                update_post_meta($post_id, $key, $value);
            }
        }

        $id_fields = array(
            '_ch_hero_image_id',
            '_ch_hero_video_mp4_id',
        );

        foreach ($id_fields as $key) {
            if (!isset($_POST[$key])) {
                continue;
            }

            $value = absint(wp_unslash($_POST[$key]));

            if (!$value) {
                delete_post_meta($post_id, $key);
            } else {
                update_post_meta($post_id, $key, $value);
            }
        }

        if (isset($_POST['_ch_hero_gallery_ids'])) {
            $raw = wp_unslash($_POST['_ch_hero_gallery_ids']);
            $ids = array_values(array_unique(array_filter(array_map('absint', explode(',', (string) $raw)))));

            if (empty($ids)) {
                delete_post_meta($post_id, '_ch_hero_gallery_ids');
            } else {
                update_post_meta($post_id, '_ch_hero_gallery_ids', implode(',', $ids));
            }
        }
    }
}

add_action('save_post_page', 'naigai_iez_save_hero_new_keys_only', 90);
/* IEZ_HERO_SAVE_NEW_KEYS_ONLY_END */


/* IEZ_HERO_PER_SLIDE_TEXT_UI_START */
/**
 * Hero画像ごとの本文・CodePen風motion UI
 * 保存メタキー: _ch_hero_slides_json
 */
if (!function_exists('naigai_iez_save_per_slide_text_ui')) {
    function naigai_iez_save_per_slide_text_ui($post_id) {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        if (
            !isset($_POST['naigai_iezukuri_subpage_nonce']) ||
            !wp_verify_nonce(
                sanitize_text_field(wp_unslash($_POST['naigai_iezukuri_subpage_nonce'])),
                'naigai_iezukuri_subpage_save'
            )
        ) {
            return;
        }

        if (!isset($_POST['_ch_hero_slides_json'])) {
            return;
        }

        $rows = json_decode((string) wp_unslash($_POST['_ch_hero_slides_json']), true);

        if (!is_array($rows)) {
            delete_post_meta($post_id, '_ch_hero_slides_json');
            return;
        }

        $clean = array();

        foreach ($rows as $row) {
            if (!is_array($row)) {
                continue;
            }

            $image_id = absint($row['image_id'] ?? 0);

            if (!$image_id) {
                continue;
            }

            $motion = sanitize_key($row['motion'] ?? 'kenburns-top');
            if (!in_array($motion, array('none', 'kenburns-top', 'kenburns-bottom', 'kenburns-left', 'kenburns-right'), true)) {
                $motion = 'kenburns-top';
            }

            $caption_motion = sanitize_key($row['caption_motion'] ?? 'none');
            if (!in_array($caption_motion, array('none', 'focus', 'slide-up'), true)) {
                $caption_motion = 'none';
            }

            $clean[] = array(
                'image_id'       => $image_id,
                'caption'        => sanitize_text_field($row['caption'] ?? ''),
                'title'          => sanitize_text_field($row['title'] ?? ''),
                'lead'           => sanitize_textarea_field($row['lead'] ?? ''),
                'motion'         => $motion,
                'caption_motion' => $caption_motion,
            );
        }

        if (empty($clean)) {
            delete_post_meta($post_id, '_ch_hero_slides_json');
        } else {
            update_post_meta($post_id, '_ch_hero_slides_json', wp_json_encode($clean, JSON_UNESCAPED_UNICODE));
        }
    }
}

add_action('save_post_page', 'naigai_iez_save_per_slide_text_ui', 95);

if (!function_exists('naigai_iez_render_per_slide_text_ui')) {
    function naigai_iez_render_per_slide_text_ui() {
        global $post;

        if (!$post || $post->post_type !== 'page') {
            return;
        }

        $gallery = get_post_meta($post->ID, '_ch_hero_gallery_ids', true);
        $single = get_post_meta($post->ID, '_ch_hero_image_id', true);

        $ids = array_values(array_filter(array_map('absint', explode(',', (string) $gallery))));

        if (empty($ids) && $single) {
            $ids = array(absint($single));
        }

        $saved = json_decode((string) get_post_meta($post->ID, '_ch_hero_slides_json', true), true);
        $map = array();

        if (is_array($saved)) {
            foreach ($saved as $row) {
                $image_id = absint($row['image_id'] ?? 0);
                if ($image_id) {
                    $map[$image_id] = $row;
                }
            }
        }

        $rows = array();

        foreach ($ids as $index => $image_id) {
            $row = $map[$image_id] ?? array();

            $rows[] = array(
                'image_id'       => $image_id,
                'thumb'          => wp_get_attachment_image_url($image_id, 'thumbnail'),
                'caption'        => $row['caption'] ?? '',
                'title'          => $row['title'] ?? '',
                'lead'           => $row['lead'] ?? '',
                'motion'         => $row['motion'] ?? array('kenburns-top', 'kenburns-bottom', 'kenburns-left', 'kenburns-right')[$index % 4],
                'caption_motion' => $row['caption_motion'] ?? 'none',
            );
        }
        ?>
        <script>
        document.addEventListener('DOMContentLoaded', function () {
          var rows = <?php echo wp_json_encode($rows, JSON_UNESCAPED_UNICODE); ?>;

          if (!rows.length) {
            return;
          }

          var anchor =
            document.querySelector('[name="_ch_hero_gallery_ids"]') ||
            document.querySelector('[name="_ch_hero_image_id"]');

          if (!anchor || document.querySelector('.naigai-iez-slide-text-box')) {
            return;
          }

          function esc(value) {
            return String(value || '')
              .replace(/&/g, '&amp;')
              .replace(/</g, '&lt;')
              .replace(/>/g, '&gt;')
              .replace(/"/g, '&quot;')
              .replace(/'/g, '&#039;');
          }

          function opt(value, label, current) {
            return '<option value="' + value + '"' + (value === current ? ' selected' : '') + '>' + label + '</option>';
          }

          var box = document.createElement('div');
          box.className = 'naigai-iez-slide-text-box';

          var html = '';
          html += '<h4>Hero 画像ごとの本文・動き</h4>';
          html += '<p class="description">Hero複数画像に合わせて、画像ごとの小見出し・H1・本文・動きを設定します。H1 / p の動きは画像切替間隔を基準にします。</p>';
          html += '<input type="hidden" id="_ch_hero_slides_json" name="_ch_hero_slides_json">';

          rows.forEach(function (row, index) {
            html += '<div class="naigai-iez-slide-text-row" data-image-id="' + row.image_id + '">';
            html += '<div>';
            if (row.thumb) {
              html += '<img src="' + row.thumb + '" alt="">';
            }
            html += '<p style="margin:6px 0 0;font-size:11px;color:#646970;">ID: ' + row.image_id + '</p>';
            html += '</div>';

            html += '<div>';
            html += '<label>小見出し</label>';
            html += '<input type="text" data-field="caption" value="' + esc(row.caption) + '">';
            html += '<label style="margin-top:8px;">見出し（H1）</label>';
            html += '<input type="text" data-field="title" value="' + esc(row.title) + '">';
            html += '<label style="margin-top:8px;">本文（p）</label>';
            html += '<textarea data-field="lead">' + esc(row.lead) + '</textarea>';
            html += '</div>';

            html += '<div>';
            html += '<label>画像の動き</label>';
            html += '<select data-field="motion">';
            html += opt('kenburns-top', 'kenburns-top：上基準', row.motion);
            html += opt('kenburns-bottom', 'kenburns-bottom：下基準', row.motion);
            html += opt('kenburns-left', 'kenburns-left：左へ流す', row.motion);
            html += opt('kenburns-right', 'kenburns-right：右へ流す', row.motion);
            html += opt('none', 'none：動かさない', row.motion);
            html += '</select>';

            html += '<label style="margin-top:8px;">文字の動き（H1 / p）</label>';
            html += '<select data-field="caption_motion">';
            html += opt('none', 'none：動かさない', row.caption_motion);
            html += opt('focus', 'focus：ゆっくりフォーカス', row.caption_motion);
            html += opt('slide-up', 'slide-up：ゆっくり下から表示', row.caption_motion);
            html += '</select>';
            html += '</div>';

            html += '</div>';
          });

          box.innerHTML = html;

          var tr = anchor.closest('tr');

          if (tr && tr.parentNode && String(tr.tagName).toLowerCase() === 'tr') {
            var wrapRow = document.createElement('tr');
            wrapRow.className = 'naigai-iez-slide-text-table-row';

            var wrapCell = document.createElement('td');
            wrapCell.colSpan = 2;
            wrapCell.appendChild(box);

            wrapRow.appendChild(wrapCell);
            tr.insertAdjacentElement('afterend', wrapRow);
          } else {
            anchor.insertAdjacentElement('afterend', box);
          }

          function sync() {
            var output = [];

            box.querySelectorAll('.naigai-iez-slide-text-row').forEach(function (row) {
              var item = {
                image_id: parseInt(row.getAttribute('data-image-id'), 10),
                caption: '',
                title: '',
                lead: '',
                motion: 'kenburns-top',
                caption_motion: 'none'
              };

              row.querySelectorAll('[data-field]').forEach(function (field) {
                item[field.getAttribute('data-field')] = field.value || '';
              });

              output.push(item);
            });

            document.getElementById('_ch_hero_slides_json').value = JSON.stringify(output);
          }

          box.addEventListener('input', sync);
          box.addEventListener('change', sync);
          sync();
        });
        </script>
        <?php
    }
}

add_action('admin_footer-post.php', 'naigai_iez_render_per_slide_text_ui');
add_action('admin_footer-post-new.php', 'naigai_iez_render_per_slide_text_ui');
/* IEZ_HERO_PER_SLIDE_TEXT_UI_END */

