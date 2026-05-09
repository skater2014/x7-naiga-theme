<?php
/**
 * 家づくりイントロ: ドラッグ&ドロップアップロード
 *
 * 保存先:
 * - wp-content/uploads/iezukuri-intro-stock/nasu/
 * - wp-content/uploads/iezukuri-intro-stock/tokyo/
 * - wp-content/uploads/iezukuri-intro-stock/bgm/
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('naigai_iez_intro_drop_is_admin_page')) {
    function naigai_iez_intro_drop_is_admin_page(string $hook): bool
    {
        return in_array($hook, array(
            'toplevel_page_naigai-iezukuri-intro',
            'toplevel_page_naigai-iez-intro',
        ), true);
    }
}

if (!function_exists('naigai_iez_intro_drop_enqueue')) {
    function naigai_iez_intro_drop_enqueue(string $hook): void
    {
        if (!naigai_iez_intro_drop_is_admin_page($hook)) {
            return;
        }

        $theme_uri = get_template_directory_uri();
        $theme_dir = get_template_directory();
        $js_rel = '/hub/pages/iezukuri/js/intro-drop-upload.js';

        wp_enqueue_script(
            'naigai-iez-intro-drop-upload',
            $theme_uri . $js_rel,
            array(),
            file_exists($theme_dir . $js_rel) ? (string) filemtime($theme_dir . $js_rel) : null,
            true
        );

        wp_localize_script(
            'naigai-iez-intro-drop-upload',
            'NaigaiIezIntroDropUpload',
            array(
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('naigai_iez_intro_drop_upload'),
            )
        );
    }
}
add_action('admin_enqueue_scripts', 'naigai_iez_intro_drop_enqueue');

if (!function_exists('naigai_iez_intro_drop_upload_dir')) {
    function naigai_iez_intro_drop_upload_dir(array $dirs): array
    {
        $side = isset($GLOBALS['naigai_iez_intro_upload_side'])
            ? sanitize_key((string) $GLOBALS['naigai_iez_intro_upload_side'])
            : 'nasu';

        if (!in_array($side, array('nasu', 'tokyo', 'bgm'), true)) {
            $side = 'nasu';
        }

        $subdir = '/iezukuri-intro-stock/' . $side;

        $dirs['subdir'] = $subdir;
        $dirs['path'] = $dirs['basedir'] . $subdir;
        $dirs['url'] = $dirs['baseurl'] . $subdir;

        return $dirs;
    }
}

if (!function_exists('naigai_iez_intro_drop_upload_ajax')) {
    function naigai_iez_intro_drop_upload_ajax(): void
    {
        check_ajax_referer('naigai_iez_intro_drop_upload', 'nonce');

        if (!current_user_can('upload_files')) {
            wp_send_json_error(array('message' => 'アップロード権限がありません。'), 403);
        }

        if (empty($_FILES['file'])) {
            wp_send_json_error(array('message' => 'ファイルがありません。'), 400);
        }

        $side = isset($_POST['side']) ? sanitize_key((string) $_POST['side']) : 'nasu';

        if (!in_array($side, array('nasu', 'tokyo', 'bgm'), true)) {
            wp_send_json_error(array('message' => '保存先が不正です。'), 400);
        }

        require_once ABSPATH . 'wp-admin/includes/file.php';

        $GLOBALS['naigai_iez_intro_upload_side'] = $side;
        add_filter('upload_dir', 'naigai_iez_intro_drop_upload_dir');

        $mimes = $side === 'bgm'
            ? array(
                'mp3' => 'audio/mpeg',
                'm4a' => 'audio/mp4',
                'wav' => 'audio/wav',
                'ogg' => 'audio/ogg',
            )
            : array(
                'jpg|jpeg|jpe' => 'image/jpeg',
                'png' => 'image/png',
                'webp' => 'image/webp',
                'gif' => 'image/gif',
            );

        $uploaded = wp_handle_upload(
            $_FILES['file'],
            array(
                'test_form' => false,
                'mimes' => $mimes,
            )
        );

        remove_filter('upload_dir', 'naigai_iez_intro_drop_upload_dir');
        unset($GLOBALS['naigai_iez_intro_upload_side']);

        if (isset($uploaded['error'])) {
            wp_send_json_error(array('message' => $uploaded['error']), 400);
        }

        wp_send_json_success(array(
            'url' => $uploaded['url'],
            'file' => $uploaded['file'],
            'type' => $uploaded['type'],
            'side' => $side,
        ));
    }
}
add_action('wp_ajax_naigai_iez_intro_drop_upload', 'naigai_iez_intro_drop_upload_ajax');
