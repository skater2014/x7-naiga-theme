<?php
/**
 * hub/pages/iezukuri/admin/enqueue.php
 *
 * 家づくり 管理画面CSS/JS
 *
 * 役割:
 * - WordPress管理画面だけで使うCSS/JSを読み込む。
 * - フロント用CSS/JSは inc/assets.php で管理する。
 *
 * 対象:
 * - post_type=page の固定ページ編集画面
 * - post_type=iez_plan の間取り詳細編集画面
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('admin_enqueue_scripts', function ($hook) {
    if (!in_array($hook, array('post.php', 'post-new.php'), true)) {
        return;
    }

    $screen = get_current_screen();

    if (!$screen) {
        return;
    }

    /**
     * 固定ページ / 間取り詳細の両方で WordPress media uploader を使う。
     */
    if (in_array($screen->post_type, array('page', 'iez_plan'), true)) {
        wp_enqueue_media();
    }

    /**
     * /iezukuri/ 固定ページ用 admin JS/CSS
     */
    if ($screen->post_type === 'page') {
        $css_path = get_template_directory() . '/hub/pages/iezukuri/admin/assets/css/subpage-admin.css';
        $css_uri  = get_template_directory_uri() . '/hub/pages/iezukuri/admin/assets/css/subpage-admin.css';

        if (file_exists($css_path)) {
            wp_enqueue_style(
                'naigai-iez-subpage-admin',
                $css_uri,
                array(),
                filemtime($css_path)
            );
        }

        $js_path = get_template_directory() . '/hub/pages/iezukuri/admin/assets/js/subpage-admin.js';
        $js_uri  = get_template_directory_uri() . '/hub/pages/iezukuri/admin/assets/js/subpage-admin.js';

        if (file_exists($js_path)) {
            wp_enqueue_script(
                'naigai-iez-subpage-admin',
                $js_uri,
                array(),
                filemtime($js_path),
                true
            );
        }
    }

    /**
     * 間取り詳細 iez_plan 用 admin JS/CSS
     */
    if ($screen->post_type === 'iez_plan') {
        $plan_css_path = get_template_directory() . '/hub/pages/iezukuri/admin/assets/css/plans-metabox.css';
        $plan_css_uri  = get_template_directory_uri() . '/hub/pages/iezukuri/admin/assets/css/plans-metabox.css';

        if (file_exists($plan_css_path)) {
            wp_enqueue_style(
                'naigai-iez-plans-metabox',
                $plan_css_uri,
                array(),
                filemtime($plan_css_path)
            );
        }

        $plan_js_path = get_template_directory() . '/hub/pages/iezukuri/admin/assets/js/plans-metabox.js';
        $plan_js_uri  = get_template_directory_uri() . '/hub/pages/iezukuri/admin/assets/js/plans-metabox.js';

        if (file_exists($plan_js_path)) {
            wp_enqueue_script(
                'naigai-iez-plans-metabox',
                $plan_js_uri,
                array(),
                filemtime($plan_js_path),
                true
            );
        }
    }
});

/**
 * 家づくり固定ページメタボックス用 admin CSS
 */
if (!function_exists('naigai_iez_admin_enqueue_subpage_admin_css')) {
    function naigai_iez_admin_enqueue_subpage_admin_css($hook) {
        if (!in_array($hook, array('post.php', 'post-new.php'), true)) {
            return;
        }

        $post_id = isset($_GET['post']) ? absint($_GET['post']) : 0;

        if (!$post_id) {
            global $post;
            $post_id = $post && !empty($post->ID) ? absint($post->ID) : 0;
        }

        if (!$post_id) {
            return;
        }

        $target_post = get_post($post_id);

        if (!$target_post || $target_post->post_type !== 'page') {
            return;
        }

        $uri = get_page_uri($target_post);

        if ($uri !== 'iezukuri' && strpos($uri, 'iezukuri/') !== 0) {
            return;
        }

        $css_path = get_template_directory() . '/hub/pages/iezukuri/admin/assets/css/subpage-admin.css';

        if (!file_exists($css_path)) {
            return;
        }

        wp_enqueue_style(
            'naigai-iez-subpage-admin',
            get_template_directory_uri() . '/hub/pages/iezukuri/admin/assets/css/subpage-admin.css',
            array(),
            filemtime($css_path)
        );
    }
}

add_action('admin_enqueue_scripts', 'naigai_iez_admin_enqueue_subpage_admin_css');
