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
        </div>
        <?php
    }
}
