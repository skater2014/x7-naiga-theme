<?php
/**
 * iez_plan 一覧画面: PDF一括生成
 *
 * 対象:
 * - 管理画面 > 家づくりプラン一覧
 * - post_type=iez_plan
 *
 * 役割:
 * - PDF列を追加
 * - 行ごとのPDF生成 / PDF確認
 * - 一括操作で選択プランのPDF生成
 * - 公開プラン全件のPDF生成
 * - 生成済みPDFをメディア添付ファイルとして登録し、_ch_plan_pdf_id に保存
 *
 * 前提:
 * - pdf-service の POST /generate が使えること
 * - 生成先は wp-content/uploads/iezukuri-pdf/{slug}.pdf
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('naigai_iez_plan_bulk_pdf_service_url')) {
    function naigai_iez_plan_bulk_pdf_service_url() {
        if (defined('NAIGAI_IEZ_PDF_SERVICE_URL') && NAIGAI_IEZ_PDF_SERVICE_URL) {
            return NAIGAI_IEZ_PDF_SERVICE_URL;
        }

        $env = getenv('NAIGAI_IEZ_PDF_SERVICE_URL');
        if ($env) {
            return $env;
        }

        /**
         * Docker上のWordPressコンテナからホスト側pdf-serviceへ到達する想定。
         * 本番では wp-config.php などで NAIGAI_IEZ_PDF_SERVICE_URL を定義する。
         */
        return 'http://host.docker.internal:3009/generate';
    }
}

if (!function_exists('naigai_iez_plan_bulk_pdf_token')) {
    function naigai_iez_plan_bulk_pdf_token() {
        if (defined('NAIGAI_IEZ_PDF_TOKEN') && NAIGAI_IEZ_PDF_TOKEN) {
            return NAIGAI_IEZ_PDF_TOKEN;
        }

        $env = getenv('NAIGAI_IEZ_PDF_TOKEN');
        if ($env) {
            return $env;
        }

        return 'local-dev-token';
    }
}

if (!function_exists('naigai_iez_plan_bulk_pdf_file_info')) {
    function naigai_iez_plan_bulk_pdf_file_info($post_id) {
        $post = get_post($post_id);

        if (!$post || $post->post_type !== 'iez_plan') {
            return array(
                'path'   => '',
                'url'    => '',
                'exists' => false,
                'size'   => 0,
            );
        }

        $slug = $post->post_name;
        $upload_dir = wp_upload_dir();

        $path = trailingslashit($upload_dir['basedir']) . 'iezukuri-pdf/' . $slug . '.pdf';
        $url  = trailingslashit($upload_dir['baseurl']) . 'iezukuri-pdf/' . rawurlencode($slug) . '.pdf';

        clearstatcache(true, $path);

        return array(
            'path'   => $path,
            'url'    => $url,
            'exists' => file_exists($path) && filesize($path) > 0,
            'size'   => file_exists($path) ? (int) filesize($path) : 0,
        );
    }
}

if (!function_exists('naigai_iez_plan_bulk_attach_pdf')) {
    function naigai_iez_plan_bulk_attach_pdf($post_id, $pdf_path, $pdf_url) {
        if (!file_exists($pdf_path)) {
            return 0;
        }

        $current_id = (int) get_post_meta($post_id, '_ch_plan_pdf_id', true);

        if ($current_id > 0 && get_post($current_id)) {
            update_post_meta($current_id, '_wp_attached_file', _wp_relative_upload_path($pdf_path));
            update_post_meta($current_id, '_naigai_iez_plan_pdf_for_post', $post_id);

            update_post_meta($post_id, '_ch_plan_pdf_url', esc_url_raw($pdf_url));
            update_post_meta($post_id, '_ch_plan_pdf_file', _wp_relative_upload_path($pdf_path));
            update_post_meta($post_id, '_ch_plan_pdf_path', $pdf_path);

            return $current_id;
        }

        $existing = get_posts(array(
            'post_type'      => 'attachment',
            'post_status'    => 'inherit',
            'posts_per_page' => 1,
            'fields'         => 'ids',
            'meta_key'       => '_naigai_iez_plan_pdf_for_post',
            'meta_value'     => $post_id,
        ));

        if (!empty($existing)) {
            $attachment_id = (int) $existing[0];

            update_post_meta($attachment_id, '_wp_attached_file', _wp_relative_upload_path($pdf_path));
            update_post_meta($post_id, '_ch_plan_pdf_id', $attachment_id);
            update_post_meta($post_id, '_ch_plan_pdf_url', esc_url_raw($pdf_url));
            update_post_meta($post_id, '_ch_plan_pdf_file', _wp_relative_upload_path($pdf_path));
            update_post_meta($post_id, '_ch_plan_pdf_path', $pdf_path);

            return $attachment_id;
        }

        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';

        $post = get_post($post_id);
        $title = $post ? get_the_title($post_id) . ' PDF' : basename($pdf_path);

        $attachment_id = wp_insert_attachment(array(
            'post_mime_type' => 'application/pdf',
            'post_title'     => sanitize_text_field($title),
            'post_content'   => '',
            'post_status'    => 'inherit',
        ), $pdf_path, $post_id);

        if (is_wp_error($attachment_id) || !$attachment_id) {
            return 0;
        }

        $metadata = wp_generate_attachment_metadata($attachment_id, $pdf_path);
        if (!is_wp_error($metadata) && is_array($metadata)) {
            wp_update_attachment_metadata($attachment_id, $metadata);
        }

        update_post_meta($attachment_id, '_naigai_iez_plan_pdf_for_post', $post_id);

        update_post_meta($post_id, '_ch_plan_pdf_id', $attachment_id);
        update_post_meta($post_id, '_ch_plan_pdf_url', esc_url_raw($pdf_url));
        update_post_meta($post_id, '_ch_plan_pdf_file', _wp_relative_upload_path($pdf_path));
        update_post_meta($post_id, '_ch_plan_pdf_path', $pdf_path);

        return (int) $attachment_id;
    }
}

if (!function_exists('naigai_iez_plan_bulk_generate_pdf')) {
    function naigai_iez_plan_bulk_generate_pdf($post_id) {
        $post = get_post($post_id);

        if (!$post || $post->post_type !== 'iez_plan') {
            return new WP_Error('invalid_post', 'iez_plan が見つかりません。');
        }

        $slug = $post->post_name;

        if (!$slug) {
            return new WP_Error('missing_slug', 'slug がありません。');
        }

        $service_url = naigai_iez_plan_bulk_pdf_service_url();
        $token       = naigai_iez_plan_bulk_pdf_token();

        $response = wp_remote_post($service_url, array(
            'timeout' => 180,
            'headers' => array(
                'Content-Type' => 'application/json',
                'x-pdf-token'  => $token,
            ),
            'body' => wp_json_encode(array(
                'slug' => $slug,
            )),
        ));

        if (is_wp_error($response)) {
            return $response;
        }

        $code = (int) wp_remote_retrieve_response_code($response);
        $body = (string) wp_remote_retrieve_body($response);
        $json = json_decode($body, true);

        if ($code < 200 || $code >= 300) {
            return new WP_Error('pdf_service_http_error', 'PDFサービスHTTPエラー: ' . $code . ' / ' . $body);
        }

        if (!is_array($json) || empty($json['ok'])) {
            return new WP_Error('pdf_service_error', 'PDFサービスの応答が不正です: ' . $body);
        }

        $info = naigai_iez_plan_bulk_pdf_file_info($post_id);

        /*
         * pdf-service のファイル書き込み直後に stat が追いつかない場合があるため短く待つ。
         */
        for ($i = 0; $i < 8; $i++) {
            clearstatcache(true, $info['path']);

            if (file_exists($info['path']) && filesize($info['path']) > 0) {
                break;
            }

            usleep(250000);
        }

        $info = naigai_iez_plan_bulk_pdf_file_info($post_id);

        if (!$info['exists']) {
            return new WP_Error('pdf_file_missing', 'PDF生成後のファイルが見つかりません: ' . $info['path']);
        }

        $attachment_id = naigai_iez_plan_bulk_attach_pdf($post_id, $info['path'], $info['url']);

        update_post_meta($post_id, '_ch_plan_pdf_generated_at', current_time('mysql'));
        update_post_meta($post_id, '_ch_plan_pdf_url', esc_url_raw($info['url']));
        update_post_meta($post_id, '_ch_plan_pdf_file', _wp_relative_upload_path($info['path']));
        update_post_meta($post_id, '_ch_plan_pdf_path', $info['path']);

        if ($attachment_id) {
            update_post_meta($post_id, '_ch_plan_pdf_id', $attachment_id);
        }

        return array(
            'post_id'       => $post_id,
            'slug'          => $slug,
            'pdf_url'       => $info['url'],
            'pdf_path'      => $info['path'],
            'size'          => $info['size'],
            'attachment_id' => $attachment_id,
        );
    }
}

if (!function_exists('naigai_iez_plan_bulk_generate_many')) {
    function naigai_iez_plan_bulk_generate_many($post_ids) {
        $result = array(
            'success' => 0,
            'failed'  => 0,
            'errors'  => array(),
        );

        foreach ((array) $post_ids as $post_id) {
            $post_id = (int) $post_id;

            if (!$post_id || !current_user_can('edit_post', $post_id)) {
                $result['failed']++;
                $result['errors'][] = '権限なし: ' . $post_id;
                continue;
            }

            $generated = naigai_iez_plan_bulk_generate_pdf($post_id);

            if (is_wp_error($generated)) {
                $result['failed']++;
                $result['errors'][] = get_the_title($post_id) . ': ' . $generated->get_error_message();
                continue;
            }

            $result['success']++;
        }

        return $result;
    }
}

if (!function_exists('naigai_iez_plan_bulk_notice_key')) {
    function naigai_iez_plan_bulk_notice_key() {
        return 'naigai_iez_plan_pdf_bulk_result_' . get_current_user_id();
    }
}

/**
 * 一覧カラム追加
 */
add_filter('manage_edit-iez_plan_columns', function ($columns) {
    $new = array();

    foreach ($columns as $key => $label) {
        $new[$key] = $label;

        if ($key === 'title') {
            $new['naigai_iez_plan_pdf'] = 'PDF';
        }
    }

    if (!isset($new['naigai_iez_plan_pdf'])) {
        $new['naigai_iez_plan_pdf'] = 'PDF';
    }

    return $new;
});

add_action('manage_iez_plan_posts_custom_column', function ($column, $post_id) {
    if ($column !== 'naigai_iez_plan_pdf') {
        return;
    }

    $info = naigai_iez_plan_bulk_pdf_file_info($post_id);
    $generated_at = get_post_meta($post_id, '_ch_plan_pdf_generated_at', true);

    $create_url = wp_nonce_url(
        admin_url('admin-post.php?action=naigai_iez_plan_generate_pdf&post_id=' . (int) $post_id),
        'naigai_iez_plan_generate_pdf_' . (int) $post_id
    );

    echo '<div class="naigai-iez-plan-pdf-column">';

    if ($info['exists']) {
        echo '<p style="margin:0 0 6px;"><strong>作成済み</strong>';
        if ($info['size'] > 0) {
            echo ' / ' . esc_html(size_format($info['size']));
        }
        echo '</p>';

        if ($generated_at) {
            echo '<p style="margin:0 0 6px;color:#666;">' . esc_html($generated_at) . '</p>';
        }

        echo '<p style="margin:0 0 8px;"><a href="' . esc_url($info['url']) . '" target="_blank" rel="noopener">PDF確認</a></p>';
    } else {
        echo '<p style="margin:0 0 8px;color:#b32d2e;"><strong>未作成</strong></p>';
    }

    echo '<p style="margin:0;"><a class="button button-small" href="' . esc_url($create_url) . '">PDF作成 / 更新</a></p>';
    echo '</div>';
}, 10, 2);

/**
 * 行アクションにもPDF作成を追加
 */
add_filter('post_row_actions', function ($actions, $post) {
    if (!$post || $post->post_type !== 'iez_plan') {
        return $actions;
    }

    if (!current_user_can('edit_post', $post->ID)) {
        return $actions;
    }

    $create_url = wp_nonce_url(
        admin_url('admin-post.php?action=naigai_iez_plan_generate_pdf&post_id=' . (int) $post->ID),
        'naigai_iez_plan_generate_pdf_' . (int) $post->ID
    );

    $actions['naigai_pdf_generate'] = '<a href="' . esc_url($create_url) . '">PDF作成</a>';

    $info = naigai_iez_plan_bulk_pdf_file_info($post->ID);
    if ($info['exists']) {
        $actions['naigai_pdf_view'] = '<a href="' . esc_url($info['url']) . '" target="_blank" rel="noopener">PDF確認</a>';
    }

    return $actions;
}, 10, 2);

/**
 * 一括操作
 */
add_filter('bulk_actions-edit-iez_plan', function ($bulk_actions) {
    $bulk_actions['naigai_generate_plan_pdf'] = 'PDFを生成 / 更新';
    return $bulk_actions;
});

add_filter('handle_bulk_actions-edit-iez_plan', function ($redirect_to, $doaction, $post_ids) {
    if ($doaction !== 'naigai_generate_plan_pdf') {
        return $redirect_to;
    }

    if (!current_user_can('edit_posts')) {
        return $redirect_to;
    }

    $result = naigai_iez_plan_bulk_generate_many($post_ids);
    set_transient(naigai_iez_plan_bulk_notice_key(), $result, 90);

    return add_query_arg('naigai_iez_plan_pdf_bulk_done', '1', $redirect_to);
}, 10, 3);

/**
 * 行ごとのPDF作成
 */
add_action('admin_post_naigai_iez_plan_generate_pdf', function () {
    $post_id = isset($_GET['post_id']) ? (int) $_GET['post_id'] : 0;

    if (!$post_id) {
        wp_die('post_id がありません。');
    }

    check_admin_referer('naigai_iez_plan_generate_pdf_' . $post_id);

    if (!current_user_can('edit_post', $post_id)) {
        wp_die('権限がありません。');
    }

    $result = naigai_iez_plan_bulk_generate_many(array($post_id));
    set_transient(naigai_iez_plan_bulk_notice_key(), $result, 90);

    wp_safe_redirect(admin_url('edit.php?post_type=iez_plan&naigai_iez_plan_pdf_bulk_done=1'));
    exit;
});

/**
 * 公開プラン全件生成
 */
add_action('admin_post_naigai_iez_plan_generate_all_pdfs', function () {
    check_admin_referer('naigai_iez_plan_generate_all_pdfs');

    if (!current_user_can('edit_posts')) {
        wp_die('権限がありません。');
    }

    $ids = get_posts(array(
        'post_type'      => 'iez_plan',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'orderby'        => 'ID',
        'order'          => 'ASC',
    ));

    $result = naigai_iez_plan_bulk_generate_many($ids);
    set_transient(naigai_iez_plan_bulk_notice_key(), $result, 90);

    wp_safe_redirect(admin_url('edit.php?post_type=iez_plan&naigai_iez_plan_pdf_bulk_done=1'));
    exit;
});

/**
 * 一覧上部に全件生成ボタン
 */
add_action('restrict_manage_posts', function ($post_type) {
    if ($post_type !== 'iez_plan') {
        return;
    }

    if (!current_user_can('edit_posts')) {
        return;
    }

    $url = wp_nonce_url(
        admin_url('admin-post.php?action=naigai_iez_plan_generate_all_pdfs'),
        'naigai_iez_plan_generate_all_pdfs'
    );

    echo '<a href="' . esc_url($url) . '" class="button" style="margin-left:8px;">公開プランPDFを全生成</a>';
});

/**
 * 結果通知
 */
add_action('admin_notices', function () {
    if (!is_admin()) {
        return;
    }

    $screen = function_exists('get_current_screen') ? get_current_screen() : null;
    if (!$screen || $screen->id !== 'edit-iez_plan') {
        return;
    }

    if (empty($_GET['naigai_iez_plan_pdf_bulk_done'])) {
        return;
    }

    $key = naigai_iez_plan_bulk_notice_key();
    $result = get_transient($key);
    delete_transient($key);

    if (!is_array($result)) {
        return;
    }

    $success = (int) ($result['success'] ?? 0);
    $failed  = (int) ($result['failed'] ?? 0);
    $errors  = (array) ($result['errors'] ?? array());

    if ($failed > 0) {
        echo '<div class="notice notice-error is-dismissible">';
        echo '<p><strong>PDF生成:</strong> 成功 ' . esc_html($success) . ' 件 / 失敗 ' . esc_html($failed) . ' 件</p>';

        if ($errors) {
            echo '<ul style="margin-left:1.4em;list-style:disc;">';
            foreach (array_slice($errors, 0, 10) as $error) {
                echo '<li>' . esc_html($error) . '</li>';
            }
            echo '</ul>';
        }

        echo '</div>';
        return;
    }

    echo '<div class="notice notice-success is-dismissible">';
    echo '<p><strong>PDF生成:</strong> ' . esc_html($success) . ' 件を作成 / 更新しました。</p>';
    echo '</div>';
});
