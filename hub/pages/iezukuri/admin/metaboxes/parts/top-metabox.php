<?php
/**
 * hub/pages/iezukuri/admin/metaboxes/parts/top-metabox.php
 *
 * /iezukuri トップページ専用入力
 *
 * 役割:
 * - トップページ本文と入口カードだけを管理する。
 * - Heroは hero-metabox.php に分ける。
 * - フロント描画はしない。
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('naigai_iez_admin_top_field_map')) {
    function naigai_iez_admin_top_field_map() {
        $fields = array(
            /*
             * 共通ヘッダーナビデザイン。
             * /iezukuri/ 配下すべての header-customhome.php がこの値を読む。
             */
            '_hub_ch_header_menu_style' => 'key',

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

            '_hub_ch_cta_eyebrow' => 'text',
            '_hub_ch_cta_title' => 'text',
            '_hub_ch_cta_text' => 'textarea',
            '_hub_ch_cta_btn1_label' => 'text',
            '_hub_ch_cta_btn1_url' => 'url',
            '_hub_ch_cta_secondary_override_label' => 'text',
            '_hub_ch_cta_secondary_override_url' => 'url',
            '_hub_ch_cta_image_id' => 'number',
            '_hub_ch_cta_video_mp4_id' => 'number',
            '_hub_ch_cta_gallery_ids' => 'text',
            '_hub_ch_cta_swiper_enabled' => 'text',
            '_hub_ch_cta_swiper_nav' => 'text',
            '_hub_ch_cta_swiper_pagination' => 'text',
            '_hub_ch_cta_video_controls' => 'text',
        );

        for ($i = 1; $i <= 3; $i++) {
            $fields["_ch_top_route_{$i}_icon"] = 'key';
            $fields["_ch_top_route_{$i}_title"] = 'text';
            $fields["_ch_top_route_{$i}_text"] = 'textarea';
            $fields["_ch_top_route_{$i}_url"] = 'url';
            $fields["_ch_top_route_{$i}_image_id"] = 'number';
        }

        return $fields;
    }
}

add_filter('naigai_iez_admin_fixed_page_fields', function ($fields, $post) {
    if (!function_exists('naigai_iez_admin_is_top_page') || !naigai_iez_admin_is_top_page($post)) {
        return $fields;
    }

    return array_merge($fields, naigai_iez_admin_top_field_map());
}, 10, 2);

if (!function_exists('naigai_iez_admin_render_top_input')) {
    function naigai_iez_admin_render_top_input($post, $get) {
        ?>
        <div class="naigai-iez-admin-section">
            <h3>家づくり入口</h3>

            <?php /* IEZUKURI COMMON HEADER STYLE ADMIN START */ ?>
            <details class="naigai-iez-admin-subsection" open>
                <summary><strong>00 共通ヘッダーナビ設定</strong></summary>

                <p class="description">
                    ここで選んだヘッダーナビデザインは、/iezukuri/ トップだけでなく、
                    会社概要・相談・コンセプトなど家づくり配下の全ページに反映されます。
                    サブページ側に同じ設定欄を作るとページごとにズレるため、トップページを正本にします。
                </p>

                <table class="form-table naigai-iez-admin-table">
                    <tbody>
                        <tr>
                            <th scope="row">
                                <label for="hub-ch-header-menu-style">ヘッダーナビデザイン</label>
                            </th>
                            <td>
                                <?php
                                $header_style_value = $get('_hub_ch_header_menu_style', 'default');

                                if (!in_array($header_style_value, array('default', 'pipe', 'minimal'), true)) {
                                    $header_style_value = 'default';
                                }
                                ?>
                                <select id="hub-ch-header-menu-style" name="_hub_ch_header_menu_style">
                                    <option value="default" <?php selected($header_style_value, 'default'); ?>>標準 / サブページ共通</option>
                                    <option value="pipe" <?php selected($header_style_value, 'pipe'); ?>>パイプ区切り</option>
                                    <option value="minimal" <?php selected($header_style_value, 'minimal'); ?>>ミニマル</option>
                                </select>
                                <p class="description">
                                    初期値は「標準 / サブページ共通」です。トップとサブページの見た目を揃える場合はこれを使います。
                                </p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </details>
<?php /* IEZUKURI COMMON HEADER STYLE ADMIN END */ ?>

            <details class="naigai-iez-admin-subsection" open>
                <summary><strong>01 導入</strong></summary>
                <table class="form-table naigai-iez-admin-table">
                    <tbody>
                        <?php
                        naigai_iez_admin_text_input('_ch_top_intro_kicker', '導入キッカー', $get('_ch_top_intro_kicker', ''));
                        naigai_iez_admin_text_input('_ch_top_intro_title', '導入見出し', $get('_ch_top_intro_title', ''));
                        naigai_iez_admin_textarea('_ch_top_intro_text', '導入本文', $get('_ch_top_intro_text', ''), 5);
                        ?>
                    </tbody>
                </table>
            </details>

            <details class="naigai-iez-admin-subsection">
                <summary><strong>02 Site Reading</strong></summary>
                <table class="form-table naigai-iez-admin-table">
                    <tbody>
                        <?php
                        naigai_iez_admin_text_input('_ch_top_site_reading_title', '見出し', $get('_ch_top_site_reading_title', ''));
                        naigai_iez_admin_textarea('_ch_top_site_reading_text', '本文', $get('_ch_top_site_reading_text', ''), 5);
                        naigai_iez_admin_media_input('_ch_top_site_reading_image_id', '画像ID', $get('_ch_top_site_reading_image_id', ''), 'image');
                        ?>
                    </tbody>
                </table>
            </details>

            <details class="naigai-iez-admin-subsection">
                <summary><strong>03 Works</strong></summary>
                <table class="form-table naigai-iez-admin-table">
                    <tbody>
                        <?php
                        naigai_iez_admin_text_input('_ch_top_works_title', '見出し', $get('_ch_top_works_title', ''));
                        naigai_iez_admin_textarea('_ch_top_works_text', '本文', $get('_ch_top_works_text', ''), 5);
                        naigai_iez_admin_media_input('_ch_top_works_image_id', '画像ID', $get('_ch_top_works_image_id', ''), 'image');
                        ?>
                    </tbody>
                </table>
            </details>

            <details class="naigai-iez-admin-subsection">
                <summary><strong>04 Plans</strong></summary>
                <table class="form-table naigai-iez-admin-table">
                    <tbody>
                        <?php
                        naigai_iez_admin_text_input('_ch_top_plans_title', '見出し', $get('_ch_top_plans_title', ''));
                        naigai_iez_admin_textarea('_ch_top_plans_text', '本文', $get('_ch_top_plans_text', ''), 5);
                        naigai_iez_admin_media_input('_ch_top_plans_image_id', '画像ID', $get('_ch_top_plans_image_id', ''), 'image');
                        ?>
                    </tbody>
                </table>
            </details>

            <details class="naigai-iez-admin-subsection">
                <summary><strong>05 Flow</strong></summary>
                <table class="form-table naigai-iez-admin-table">
                    <tbody>
                        <?php
                        naigai_iez_admin_text_input('_ch_top_flow_title', '見出し', $get('_ch_top_flow_title', ''));
                        naigai_iez_admin_textarea('_ch_top_flow_text', '本文', $get('_ch_top_flow_text', ''), 5);
                        ?>
                    </tbody>
                </table>
            </details>
            <details class="naigai-iez-admin-subsection">
                <summary><strong>06 入口カード</strong></summary>

            <?php
            $route_defaults = array(
                1 => array('label' => 'カード1', 'icon' => 'new-house'),
                2 => array('label' => 'カード2', 'icon' => 'two-family'),
                3 => array('label' => 'カード3', 'icon' => 'used-renovation'),
            );

            foreach ($route_defaults as $i => $route) :
                ?>
                <details class="naigai-iez-admin-subsection" <?php echo $i === 1 ? 'open' : ''; ?>>
                    <summary><strong><?php echo esc_html($route['label']); ?></strong></summary>
                    <table class="form-table naigai-iez-admin-table">
                        <tbody>
                            <?php
                            naigai_iez_admin_icon_select("_ch_top_route_{$i}_icon", 'アイコン', $get("_ch_top_route_{$i}_icon", $route['icon']));
                            naigai_iez_admin_text_input("_ch_top_route_{$i}_title", '見出し', $get("_ch_top_route_{$i}_title", ''));
                            naigai_iez_admin_textarea("_ch_top_route_{$i}_text", '説明文', $get("_ch_top_route_{$i}_text", ''), 4);
                            naigai_iez_admin_url_input("_ch_top_route_{$i}_url", 'リンクURL', $get("_ch_top_route_{$i}_url", ''));
                            naigai_iez_admin_media_input("_ch_top_route_{$i}_image_id", '画像ID', $get("_ch_top_route_{$i}_image_id", ''), 'image');
                            ?>
                        </tbody>
                    </table>
                </details>
                <?php
            endforeach;
            ?>
            </details>

            <details class="naigai-iez-admin-subsection" open>
                <summary><strong>07 CTA</strong></summary>
                <table class="form-table naigai-iez-admin-table">
                    <tbody>
                        <?php
                        naigai_iez_admin_text_input('_hub_ch_cta_eyebrow', 'CTAキッカー', $get('_hub_ch_cta_eyebrow', 'CONTACT'));
                        naigai_iez_admin_text_input('_hub_ch_cta_title', 'CTA見出し', $get('_hub_ch_cta_title', ''));
                        naigai_iez_admin_textarea('_hub_ch_cta_text', 'CTA本文', $get('_hub_ch_cta_text', ''), 5);
                        naigai_iez_admin_text_input('_hub_ch_cta_btn1_label', 'メインボタン文言', $get('_hub_ch_cta_btn1_label', ''));
                        naigai_iez_admin_url_input('_hub_ch_cta_btn1_url', 'メインボタンURL', $get('_hub_ch_cta_btn1_url', ''));
                        naigai_iez_admin_text_input('_hub_ch_cta_secondary_override_label', 'サブボタン文言', $get('_hub_ch_cta_secondary_override_label', ''));
                        naigai_iez_admin_url_input('_hub_ch_cta_secondary_override_url', 'サブボタンURL', $get('_hub_ch_cta_secondary_override_url', ''));

                        naigai_iez_admin_media_input('_hub_ch_cta_image_id', 'CTA単体画像', $get('_hub_ch_cta_image_id', ''), 'image');
                        naigai_iez_admin_media_input('_hub_ch_cta_video_mp4_id', 'CTA単体MP4動画', $get('_hub_ch_cta_video_mp4_id', ''), 'video');
                        naigai_iez_admin_media_input('_hub_ch_cta_gallery_ids', 'CTA Swiper画像', $get('_hub_ch_cta_gallery_ids', ''), 'image', true, '複数画像はメディアライブラリーから選択。');
                        ?>
                        <tr>
                            <th scope="row">Swiper / 動画設定</th>
                            <td>
                                <label><input type="checkbox" name="_hub_ch_cta_swiper_enabled" value="1" <?php checked($get('_hub_ch_cta_swiper_enabled', '1'), '1'); ?>> Swiper有効</label><br>
                                <label><input type="checkbox" name="_hub_ch_cta_swiper_nav" value="1" <?php checked($get('_hub_ch_cta_swiper_nav', '1'), '1'); ?>> 矢印表示</label><br>
                                <label><input type="checkbox" name="_hub_ch_cta_swiper_pagination" value="1" <?php checked($get('_hub_ch_cta_swiper_pagination', '1'), '1'); ?>> ドット表示</label><br>
                                <label><input type="checkbox" name="_hub_ch_cta_video_controls" value="1" <?php checked($get('_hub_ch_cta_video_controls', '0'), '1'); ?>> 動画コントロール表示</label>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </details>


        </div>
        <?php
    }
}


/* top CTA media normalize */
if (!function_exists('naigai_iez_admin_save_top_cta_media_items')) {
    function naigai_iez_admin_save_top_cta_media_items($post_id)
    {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        $post = get_post($post_id);
        if (!$post || !function_exists('naigai_iez_admin_is_top_page') || !naigai_iez_admin_is_top_page($post)) {
            return;
        }

        update_post_meta($post_id, '_hub_ch_cta_swiper_enabled', isset($_POST['_hub_ch_cta_swiper_enabled']) ? '1' : '0');
        update_post_meta($post_id, '_hub_ch_cta_swiper_nav', isset($_POST['_hub_ch_cta_swiper_nav']) ? '1' : '0');
        update_post_meta($post_id, '_hub_ch_cta_swiper_pagination', isset($_POST['_hub_ch_cta_swiper_pagination']) ? '1' : '0');
        update_post_meta($post_id, '_hub_ch_cta_video_controls', isset($_POST['_hub_ch_cta_video_controls']) ? '1' : '0');

        $items = array();

        $video_id = isset($_POST['_hub_ch_cta_video_mp4_id']) ? absint($_POST['_hub_ch_cta_video_mp4_id']) : 0;
        if ($video_id > 0) {
            $video_url = wp_get_attachment_url($video_id);
            if ($video_url) {
                $items[] = array(
                    'type'          => 'video',
                    'id'            => $video_id,
                    'attachment_id' => $video_id,
                    'url'           => esc_url_raw($video_url),
                    'mime'          => get_post_mime_type($video_id) ?: 'video/mp4',
                    'alt'           => '',
                );
            }
        }

        $gallery_raw = isset($_POST['_hub_ch_cta_gallery_ids']) ? sanitize_text_field(wp_unslash($_POST['_hub_ch_cta_gallery_ids'])) : '';
        $gallery_ids = array_filter(array_map('absint', explode(',', $gallery_raw)));

        foreach ($gallery_ids as $image_id) {
            $image_url = wp_get_attachment_url($image_id);
            if (!$image_url) {
                continue;
            }

            $items[] = array(
                'type'          => 'image',
                'id'            => $image_id,
                'attachment_id' => $image_id,
                'url'           => esc_url_raw($image_url),
                'mime'          => get_post_mime_type($image_id) ?: 'image/jpeg',
                'alt'           => get_post_meta($image_id, '_wp_attachment_image_alt', true),
            );
        }

        if (empty($items)) {
            $image_id = isset($_POST['_hub_ch_cta_image_id']) ? absint($_POST['_hub_ch_cta_image_id']) : 0;
            if ($image_id > 0) {
                $image_url = wp_get_attachment_url($image_id);
                if ($image_url) {
                    $items[] = array(
                        'type'          => 'image',
                        'id'            => $image_id,
                        'attachment_id' => $image_id,
                        'url'           => esc_url_raw($image_url),
                        'mime'          => get_post_mime_type($image_id) ?: 'image/jpeg',
                        'alt'           => get_post_meta($image_id, '_wp_attachment_image_alt', true),
                    );
                }
            }
        }

        update_post_meta($post_id, '_hub_ch_cta_media_items', $items);
    }
}
add_action('save_post_page', 'naigai_iez_admin_save_top_cta_media_items', 120);
