<?php
/**
 * =========================================================
 * Hub common admin helpers
 * =========================================================
 *
 * hub/inc/admin.php
 *
 * 役割:
 * - Hub共通の管理画面 helper だけ
 * - /iezukuri / customhome / 注文住宅 固有コードは禁止
 * - /iezukuri 固有コードは hub/pages/iezukuri/inc/ 側で管理
 * =========================================================
 */

if (!defined("ABSPATH")) {
    exit;
}

/**
 * 画像ID CSV を正規化
 */
if (!function_exists('naigai_hub_normalize_image_ids_csv')) {
    function naigai_hub_normalize_image_ids_csv($raw)
    {
        $raw = (string) $raw;

        if ($raw === '') {
            return '';
        }

        $parts = preg_split('/\s*,\s*/', $raw, -1, PREG_SPLIT_NO_EMPTY);
        $ids = array();

        foreach ($parts as $part) {
            $id = absint($part);

            if ($id > 0) {
                $ids[$id] = $id;
            }
        }

        return implode(',', array_values($ids));
    }
}

/**
 * 複数画像 preview
 */
if (!function_exists('naigai_hub_get_admin_image_preview_html')) {
    function naigai_hub_get_admin_image_preview_html($csv)
    {
        $csv = naigai_hub_normalize_image_ids_csv($csv);

        if ($csv === '') {
            return '';
        }

        $html = '';

        foreach (explode(',', $csv) as $id) {
            $id = absint($id);
            $src = wp_get_attachment_image_url($id, 'thumbnail');
            $name = get_the_title($id);

            if (!$src) {
                continue;
            }

            $html .= '<span style="display:inline-flex;width:72px;height:72px;overflow:hidden;border:1px solid #dcdcde;border-radius:8px;background:#fff;">';
            $html .= '<img src="' . esc_url($src) . '" alt="' . esc_attr($name) . '" style="display:block;width:100%;height:100%;object-fit:cover;">';
            $html .= '</span>';
        }

        return $html;
    }
}

/**
 * 複数画像 selector
 */
if (!function_exists('naigai_render_hub_image_selector')) {
    function naigai_render_hub_image_selector($field_name, $raw_value = '')
    {
        $csv = naigai_hub_normalize_image_ids_csv($raw_value);

        echo '<div class="naigai-hub-media-field">';
        echo '<input type="hidden" class="js-hub-media-ids" name="' . esc_attr($field_name) . '" value="' . esc_attr($csv) . '">';
        echo '<p>';
        echo '<button type="button" class="button js-hub-media-open">画像を選択</button> ';
        echo '<button type="button" class="button js-hub-media-clear">画像をクリア</button>';
        echo '</p>';
        echo '<div class="js-hub-media-preview" style="display:flex;flex-wrap:wrap;gap:8px;">' . naigai_hub_get_admin_image_preview_html($csv) . '</div>';
        echo '</div>';
    }
}

/**
 * 単一ファイル preview
 */
if (!function_exists('naigai_hub_get_admin_single_media_preview_html')) {
    function naigai_hub_get_admin_single_media_preview_html($attachment_id)
    {
        $attachment_id = absint($attachment_id);

        if ($attachment_id <= 0) {
            return '';
        }

        $url = wp_get_attachment_url($attachment_id);
        $mime = get_post_mime_type($attachment_id);
        $name = get_the_title($attachment_id);

        if (!$url) {
            return '';
        }

        if (wp_attachment_is_image($attachment_id)) {
            $thumb = wp_get_attachment_image_url($attachment_id, 'thumbnail');

            if (!$thumb) {
                $thumb = $url;
            }

            return '<span style="display:inline-flex;width:96px;height:72px;overflow:hidden;border:1px solid #dcdcde;border-radius:8px;background:#fff;"><img src="' . esc_url($thumb) . '" alt="' . esc_attr($name) . '" style="display:block;width:100%;height:100%;object-fit:cover;"></span>';
        }

        $html  = '<div style="display:grid;gap:4px;padding:10px 12px;border:1px solid #dcdcde;border-radius:8px;background:#fff;">';
        $html .= '<strong style="line-height:1.4;">' . esc_html($name !== '' ? $name : basename($url)) . '</strong>';
        $html .= '<span style="color:#646970;">' . esc_html((string) $mime) . '</span>';
        $html .= '</div>';

        return $html;
    }
}

/**
 * 単一ファイル selector
 */
if (!function_exists('naigai_render_hub_single_media_selector')) {
    function naigai_render_hub_single_media_selector($field_name, $attachment_id = 0, $button_label = 'ファイルを選択', $library_type = '')
    {
        $attachment_id = absint($attachment_id);

        echo '<div class="naigai-hub-single-media-field" data-library-type="' . esc_attr($library_type) . '">';
        echo '<input type="hidden" class="js-hub-single-media-id" name="' . esc_attr($field_name) . '" value="' . esc_attr((string) $attachment_id) . '">';
        echo '<p>';
        echo '<button type="button" class="button js-hub-single-media-open" data-library-type="' . esc_attr($library_type) . '">' . esc_html($button_label) . '</button> ';
        echo '<button type="button" class="button js-hub-single-media-clear">クリア</button>';
        echo '</p>';
        echo '<div class="js-hub-single-media-preview" style="display:flex;flex-wrap:wrap;gap:8px;">' . naigai_hub_get_admin_single_media_preview_html($attachment_id) . '</div>';
        echo '</div>';
    }
}

/**
 * 固定ページプルダウン
 */
if (!function_exists('naigai_render_hub_page_dropdown')) {
    function naigai_render_hub_page_dropdown($name, $selected = 0)
    {
        $selected = (int) $selected;

        wp_dropdown_pages(
            array(
                'name'              => $name,
                'id'                => $name,
                'selected'          => $selected,
                'show_option_none'  => '— 固定ページを選択してください —',
                'option_none_value' => '0',
                'sort_column'       => 'menu_order,post_title',
                'class'             => 'widefat',
            )
        );
    }
}

/**
 * Hub 管理画面用JS
 */
if (!function_exists('naigai_enqueue_hub_admin_assets')) {
    function naigai_enqueue_hub_admin_assets($hook_suffix)
    {
        if ($hook_suffix !== 'post.php' && $hook_suffix !== 'post-new.php') {
            return;
        }

        $screen = function_exists('get_current_screen') ? get_current_screen() : null;

        if (!$screen || $screen->post_type !== 'page') {
            return;
        }

        wp_enqueue_media();

        $relative_path = '/inc/functions/hub/admin/hub-admin-media.js';
        $absolute_path = get_template_directory() . $relative_path;

        if (file_exists($absolute_path)) {
            wp_enqueue_script(
                'naigai-hub-admin-media',
                get_template_directory_uri() . $relative_path,
                array('jquery'),
                filemtime($absolute_path),
                true
            );
        }
    }

    add_action('admin_enqueue_scripts', 'naigai_enqueue_hub_admin_assets');
}
