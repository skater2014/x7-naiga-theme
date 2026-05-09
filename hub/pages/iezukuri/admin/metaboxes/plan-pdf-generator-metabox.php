<?php
/**
 * iez_plan 編集画面: PDF専用表示の確認 / PDF作成
 *
 * 方針:
 * - 確認ボタンのURLがPDFフォーマット本体
 * - 実行ボタンはそのURLを pdf-service に渡してPDF化する
 * - CSSは追加しない
 */

if (!defined('ABSPATH')) {
    exit;
}


add_action('admin_enqueue_scripts', 'naigai_iez_plan_pdf_enqueue_media');
function naigai_iez_plan_pdf_enqueue_media($hook) {
    $screen = function_exists('get_current_screen') ? get_current_screen() : null;

    if (!$screen || $screen->post_type !== 'iez_plan') {
        return;
    }

    wp_enqueue_media();
}

add_action('add_meta_boxes_iez_plan', 'naigai_iez_plan_pdf_add_metabox');

function naigai_iez_plan_pdf_add_metabox($post) {
    add_meta_box(
        'naigai-iez-plan-pdf-box',
        'PDF',
        'naigai_iez_plan_pdf_render_metabox',
        'iez_plan',
        'side',
        'high'
    );
}

function naigai_iez_plan_pdf_preview_url($post_id) {
    return add_query_arg(
        'plan_pdf',
        '1',
        trailingslashit(get_permalink($post_id))
    );
}

function naigai_iez_plan_pdf_file_path($slug) {
    $upload_dir = wp_upload_dir();
    return trailingslashit($upload_dir['basedir']) . 'iezukuri-pdf/' . $slug . '.pdf';
}

function naigai_iez_plan_pdf_file_url($slug) {
    /*
     * wp_upload_dir()['baseurl'] は環境によって本番URLを返すことがある。
     * ローカル編集画面では home_url() から必ず現在のWordPress URLでPDFリンクを作る。
     */
    return home_url('/wp-content/uploads/iezukuri-pdf/' . rawurlencode($slug) . '.pdf');
}

function naigai_iez_plan_pdf_render_metabox($post) {
    if (!$post || $post->post_type !== 'iez_plan') {
        return;
    }

    $post_id     = (int) $post->ID;
    $slug        = sanitize_title($post->post_name);
    $preview_url = naigai_iez_plan_pdf_preview_url($post_id);

    $pdf_path = naigai_iez_plan_pdf_file_path($slug);
    $pdf_url  = naigai_iez_plan_pdf_file_url($slug);
    $exists   = ($slug && file_exists($pdf_path) && filesize($pdf_path) > 0);

    $nonce = wp_create_nonce('naigai_iez_plan_pdf_generate_' . $post_id);

    $selected_pdf_id  = (int) get_post_meta($post_id, '_ch_plan_pdf_id', true);
    $selected_pdf_url = $selected_pdf_id ? wp_get_attachment_url($selected_pdf_id) : '';
    $selected_pdf_title = $selected_pdf_id ? get_the_title($selected_pdf_id) : '';

    ?>
    <p style="margin-top:0;">
        <a class="button" href="<?php echo esc_url($preview_url); ?>" target="_blank" rel="noopener">
            PDF専用表示を確認
        </a>
    </p>

    <p>
        <button
            type="button"
            class="button button-primary"
            id="naigai-iez-plan-pdf-generate"
            data-post-id="<?php echo esc_attr($post_id); ?>"
            data-preview-url="<?php echo esc_url($preview_url); ?>"
            data-nonce="<?php echo esc_attr($nonce); ?>"
        >
            PDFを作成 / 更新
        </button>
    </p>


    <?php wp_nonce_field('naigai_iez_plan_pdf_upload_' . $post_id, 'naigai_iez_plan_pdf_upload_nonce'); ?>

    <hr>

    <p style="margin:0 0 8px;">
        <strong>PDFアップロード / 選択</strong>
    </p>

    <input
        type="hidden"
        id="naigai-iez-plan-pdf-id"
        name="naigai_iez_plan_pdf_id"
        value="<?php echo esc_attr($selected_pdf_id); ?>"
    >

    <p>
        <button type="button" class="button" id="naigai-iez-plan-pdf-select">
            PDFをアップロード / 選択
        </button>
        <button type="button" class="button" id="naigai-iez-plan-pdf-clear">
            解除
        </button>
    </p>

    <p id="naigai-iez-plan-pdf-selected" style="word-break:break-all;">
        <?php if ($selected_pdf_url) : ?>
            選択中: <a href="<?php echo esc_url($selected_pdf_url); ?>" target="_blank" rel="noopener"><?php echo esc_html($selected_pdf_title ?: basename($selected_pdf_url)); ?></a>
        <?php else : ?>
            PDF未選択
        <?php endif; ?>
    </p>

    <script>
    (function() {
        const selectPdfButton = document.getElementById('naigai-iez-plan-pdf-select');
        const clearPdfButton = document.getElementById('naigai-iez-plan-pdf-clear');
        const pdfIdField = document.getElementById('naigai-iez-plan-pdf-id');
        const selectedPdfText = document.getElementById('naigai-iez-plan-pdf-selected');

        if (selectPdfButton && pdfIdField && selectedPdfText && window.wp && wp.media) {
            selectPdfButton.addEventListener('click', function() {
                const frame = wp.media({
                    title: 'PDFを選択',
                    library: {
                        type: 'application/pdf'
                    },
                    button: {
                        text: 'このPDFを使う'
                    },
                    multiple: false
                });

                frame.on('select', function() {
                    const attachment = frame.state().get('selection').first().toJSON();

                    pdfIdField.value = attachment.id || '';
                    selectedPdfText.innerHTML = attachment.url
                        ? '選択中: <a href="' + attachment.url + '" target="_blank" rel="noopener">' + (attachment.filename || attachment.url) + '</a><br>この投稿を更新すると反映されます。'
                        : 'PDFを選択しました。この投稿を更新すると反映されます。';
                });

                frame.open();
            });
        }

        if (clearPdfButton && pdfIdField && selectedPdfText) {
            clearPdfButton.addEventListener('click', function() {
                pdfIdField.value = '';
                selectedPdfText.textContent = 'PDF選択を解除しました。この投稿を更新すると反映されます。';
            });
        }

        const button = document.getElementById('naigai-iez-plan-pdf-generate');

        if (!button || button.dataset.bound === '1') {
            return;
        }

        button.dataset.bound = '1';

        button.addEventListener('click', async function() {
            const originalText = button.textContent;

            button.disabled = true;
            button.textContent = '作成中...';

            const params = new URLSearchParams();
            params.append('action', 'naigai_iez_plan_generate_pdf');
            params.append('post_id', button.dataset.postId || '');
            params.append('preview_url', button.dataset.previewUrl || '');
            params.append('nonce', button.dataset.nonce || '');

            try {
                const response = await fetch(ajaxurl, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
                    },
                    body: params.toString()
                });

                const json = await response.json();

                if (!json || !json.success) {
                    const message = json && json.data && json.data.message
                        ? json.data.message
                        : 'PDF作成に失敗しました。';
                    throw new Error(message);
                }

                if (json.data && json.data.url) {
                    window.open(json.data.url + '?t=' + Date.now(), '_blank', 'noopener');
                    window.setTimeout(function() {
                        window.location.reload();
                    }, 600);
                }
            } catch (error) {
                alert('PDF作成に失敗しました: ' + error.message);
                console.error(error);
            } finally {
                button.disabled = false;
                button.textContent = originalText;
            }
        });
    })();
    </script>
    <?php
}


function naigai_iez_plan_pdf_register_generated_media($post_id, $pdf_path, $slug) {
    $pdf_path = (string) $pdf_path;
    $slug     = sanitize_title($slug);

    if (!$post_id || !$slug || !file_exists($pdf_path) || filesize($pdf_path) <= 0) {
        return 0;
    }

    $upload_dir = wp_upload_dir();

    if (!empty($upload_dir['error'])) {
        return 0;
    }

    $relative_file = 'iezukuri-pdf/' . $slug . '.pdf';
    $pdf_url       = home_url('/wp-content/uploads/' . $relative_file);

    /*
     * すでに同じPDFがメディア登録されている場合は、それを再利用する。
     * 毎回 attachment を増やさない。
     */
    $existing = get_posts(array(
        'post_type'      => 'attachment',
        'post_status'    => 'inherit',
        'post_mime_type' => 'application/pdf',
        'posts_per_page' => 1,
        'fields'         => 'ids',
        'meta_query'     => array(
            array(
                'key'   => '_wp_attached_file',
                'value' => $relative_file,
            ),
        ),
    ));

    if (!empty($existing[0])) {
        $attachment_id = (int) $existing[0];

        wp_update_post(array(
            'ID'          => $attachment_id,
            'post_parent' => $post_id,
            'post_title'  => get_the_title($post_id) . ' PDF',
            'guid'        => $pdf_url,
        ));

        update_attached_file($attachment_id, $relative_file);

        update_post_meta($post_id, '_ch_plan_pdf_id', $attachment_id);
        update_post_meta($post_id, '_ch_plan_pdf_url', esc_url_raw($pdf_url));
        update_post_meta($post_id, '_ch_plan_pdf_file', $relative_file);
        update_post_meta($post_id, '_ch_plan_pdf_path', $pdf_path);
        update_post_meta($post_id, '_ch_plan_pdf_generated_at', current_time('mysql'));

        return $attachment_id;
    }

    $attachment_id = wp_insert_attachment(array(
        'guid'           => $pdf_url,
        'post_mime_type' => 'application/pdf',
        'post_title'     => get_the_title($post_id) . ' PDF',
        'post_content'   => '',
        'post_status'    => 'inherit',
        'post_parent'    => $post_id,
    ), $pdf_path, $post_id);

    if (is_wp_error($attachment_id) || !$attachment_id) {
        return 0;
    }

    update_attached_file($attachment_id, $relative_file);

    require_once ABSPATH . 'wp-admin/includes/image.php';
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';

    $metadata = wp_generate_attachment_metadata($attachment_id, $pdf_path);
    if (!is_wp_error($metadata) && is_array($metadata)) {
        wp_update_attachment_metadata($attachment_id, $metadata);
    }

    update_post_meta($post_id, '_ch_plan_pdf_id', $attachment_id);
    update_post_meta($post_id, '_ch_plan_pdf_url', esc_url_raw($pdf_url));
    update_post_meta($post_id, '_ch_plan_pdf_file', $relative_file);
    update_post_meta($post_id, '_ch_plan_pdf_path', $pdf_path);
    update_post_meta($post_id, '_ch_plan_pdf_generated_at', current_time('mysql'));

    return (int) $attachment_id;
}

add_action('wp_ajax_naigai_iez_plan_generate_pdf', 'naigai_iez_plan_generate_pdf_ajax');

function naigai_iez_plan_generate_pdf_ajax() {
    $post_id = isset($_POST['post_id']) ? absint($_POST['post_id']) : 0;
    $nonce   = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';

    if (!$post_id || !wp_verify_nonce($nonce, 'naigai_iez_plan_pdf_generate_' . $post_id)) {
        wp_send_json_error(array(
            'message' => '認証に失敗しました。編集画面を更新してから再実行してください。',
        ), 403);
    }

    if (!current_user_can('edit_post', $post_id)) {
        wp_send_json_error(array(
            'message' => '編集権限がありません。',
        ), 403);
    }

    $post = get_post($post_id);

    if (!$post || $post->post_type !== 'iez_plan') {
        wp_send_json_error(array(
            'message' => 'iez_plan が見つかりません。',
        ), 404);
    }

    $slug = sanitize_title($post->post_name);

    if (!$slug) {
        wp_send_json_error(array(
            'message' => 'スラッグが空です。先に投稿を保存してください。',
        ), 400);
    }

    /*
     * ここが重要。
     * 実行ボタンでは、確認ボタンと同じURLをPDFサービスに渡す。
     */
    $preview_url = naigai_iez_plan_pdf_preview_url($post_id);

    $service_urls = array(
        'http://wp-local-naiga-pdf-service:3009/generate',
        'http://pdf-service:3009/generate',
        'http://host.docker.internal:3009/generate',
        'http://127.0.0.1:3009/generate',
    );

    $last_error = '';

    foreach ($service_urls as $service_url) {
        $response = wp_remote_post($service_url, array(
            'timeout' => 120,
            'headers' => array(
                'Content-Type' => 'application/json',
                'x-pdf-token'  => 'local-dev-token',
            ),
            'body' => wp_json_encode(array(
                'slug' => $slug,
                'url'  => $preview_url,
            )),
        ));

        if (is_wp_error($response)) {
            $last_error = $service_url . ' => ' . $response->get_error_message();
            continue;
        }

        $code = (int) wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        $json = json_decode($body, true);

        if ($code < 200 || $code >= 300 || empty($json['ok'])) {
            $last_error = $service_url . ' => HTTP ' . $code . ' / ' . $body;
            continue;
        }

        $pdf_path = naigai_iez_plan_pdf_file_path($slug);
        $pdf_url  = naigai_iez_plan_pdf_file_url($slug);

        clearstatcache(true, $pdf_path);

        if (!file_exists($pdf_path) || filesize($pdf_path) <= 0) {
            $last_error = 'PDFサービスは成功しましたが、PDFファイルが見つかりません: ' . $pdf_path;
            continue;
        }

        /*
         * 生成したPDFを「PDF資料」としてメディア登録し、このプランに紐づける。
         * 以後、詳細ページのPDFダウンロードは _ch_plan_pdf_id を基準にできる。
         */
        $attachment_id = naigai_iez_plan_pdf_register_generated_media($post_id, $pdf_path, $slug);

        if (!$attachment_id) {
            wp_send_json_error(array(
                'message' => 'PDFは作成されましたが、PDF資料への登録に失敗しました。',
            ), 500);
        }

        wp_send_json_success(array(
            'url'    => esc_url_raw($pdf_url),
            'size'   => size_format(filesize($pdf_path)),
            'pdf_id' => $attachment_id,
        ));
    }

    wp_send_json_error(array(
        'message' => 'PDFサービスに接続できませんでした。' . ($last_error ? ' / ' . $last_error : ''),
    ), 500);
}


add_action('save_post_iez_plan', 'naigai_iez_plan_pdf_save_uploaded_pdf', 20, 2);
function naigai_iez_plan_pdf_save_uploaded_pdf($post_id, $post) {
    if (!$post || $post->post_type !== 'iez_plan') {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    $nonce = isset($_POST['naigai_iez_plan_pdf_upload_nonce'])
        ? sanitize_text_field(wp_unslash($_POST['naigai_iez_plan_pdf_upload_nonce']))
        : '';

    if (!$nonce || !wp_verify_nonce($nonce, 'naigai_iez_plan_pdf_upload_' . $post_id)) {
        return;
    }

    if (!array_key_exists('naigai_iez_plan_pdf_id', $_POST)) {
        return;
    }

    $pdf_id = absint($_POST['naigai_iez_plan_pdf_id']);

    if ($pdf_id) {
        $mime = get_post_mime_type($pdf_id);

        if ($mime !== 'application/pdf') {
            return;
        }

        update_post_meta($post_id, '_ch_plan_pdf_id', $pdf_id);

        $url = wp_get_attachment_url($pdf_id);
        if ($url) {
            update_post_meta($post_id, '_ch_plan_pdf_url', esc_url_raw($url));
        }

        return;
    }

    /*
     * 解除時は「アップロードPDFの選択」だけ解除する。
     * 自動生成PDFのURLやファイル情報は残す。
     */
    delete_post_meta($post_id, '_ch_plan_pdf_id');
}

