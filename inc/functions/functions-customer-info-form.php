<?php
if (!defined('ABSPATH')) {
    exit;
}

function naigai_customer_info_job_options()
{
    return array(
        ''   => '',
        '1'  => '社長 / 代表者',
        '2'  => '会社員',
        '3'  => '公務員',
        '4'  => '自営業',
        '5'  => '契約社員',
        '6'  => '派遣社員',
        '7'  => 'パート / アルバイト',
        '8'  => '専業主婦 / 主夫',
        '9'  => '学生',
        '99' => 'その他',
    );
}

function naigai_is_customer_info_form_page()
{
    if (!is_page()) {
        return false;
    }

    $post_id = get_queried_object_id();
    if (!$post_id) {
        return false;
    }

    $template_slug = get_page_template_slug($post_id);
    if (!$template_slug) {
        return false;
    }

    $template_basename = wp_basename($template_slug);

    /*
     * 既存 /contact 用テンプレート
     */
    if (in_array($template_basename, array(
        'page-customer-info-form.php',
        'customer-info-form.php',
    ), true)) {
        return true;
    }

    /*
     * /iezukuri/contact 用:
     * page-construction-hub-sub.php の contact レイアウトでも
     * 既存 customer-info-form の CSS/JS を読み込む。
     */
    if ($template_basename === 'page-construction-hub-sub.php') {
        $layout = (string) get_post_meta($post_id, '_ch_subpage_template', true);

        if ($layout === 'contact') {
            return true;
        }

        if ($layout === 'builder') {
            $raw = get_post_meta($post_id, '_ch_builder_sections_json', true);
            $sections = is_string($raw) && $raw !== '' ? json_decode($raw, true) : array();

            if (is_array($sections)) {
                foreach ($sections as $section) {
                    if (is_array($section) && ($section['type'] ?? '') === 'contact-form' && (!isset($section['enabled']) || $section['enabled'])) {
                        return true;
                    }
                }
            }
        }
    }

    return false;
}

function naigai_customer_info_asset_version($relative_path)
{
    $file = get_template_directory() . $relative_path;
    return file_exists($file) ? (string) filemtime($file) : null;
}

function naigai_customer_info_form_enqueue_assets()
{
    if (!naigai_is_customer_info_form_page()) {
        return;
    }

    $theme_uri = get_template_directory_uri();

    wp_enqueue_style(
        'naigai-customer-info-form',
        $theme_uri . '/css/customer-info-form.css',
        array(),
        naigai_customer_info_asset_version('/css/customer-info-form.css')
    );

    wp_enqueue_script(
        'naigai-customer-info-form',
        $theme_uri . '/js/customer-info-form.js',
        array(),
        naigai_customer_info_asset_version('/js/customer-info-form.js'),
        true
    );

    $config = array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('customer_info_submit_action'),
    );

    wp_add_inline_script(
        'naigai-customer-info-form',
        'window.naigaiCustomerInfoForm = ' . wp_json_encode($config) . ';',
        'before'
    );
}
add_action('wp_enqueue_scripts', 'naigai_customer_info_form_enqueue_assets', 20);

add_action('wp_ajax_customer_info_submit', 'naigai_handle_customer_info_submit');
add_action('wp_ajax_nopriv_customer_info_submit', 'naigai_handle_customer_info_submit');

function naigai_customer_info_substr($text, $start, $length)
{
    if (function_exists('mb_substr')) {
        return mb_substr($text, $start, $length);
    }

    return substr($text, $start, $length);
}

function naigai_handle_customer_info_submit()
{
    if (
        !isset($_POST['security']) ||
        !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['security'])), 'customer_info_submit_action')
    ) {
        wp_send_json_error(array('message' => 'セキュリティエラーです。'), 403);
    }

    $data = array(
        'name'            => isset($_POST['name']) ? sanitize_text_field(wp_unslash($_POST['name'])) : '',
        'email'           => isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '',
        'phone'           => isset($_POST['phone']) ? sanitize_text_field(wp_unslash($_POST['phone'])) : '',
        'prefecture'      => isset($_POST['prefecture']) ? sanitize_text_field(wp_unslash($_POST['prefecture'])) : '',
        'city'            => isset($_POST['city']) ? sanitize_text_field(wp_unslash($_POST['city'])) : '',
        'address_number'  => isset($_POST['address_number']) ? sanitize_text_field(wp_unslash($_POST['address_number'])) : '',
        'residence_years' => isset($_POST['residence_years']) ? absint($_POST['residence_years']) : 0,
        'job'             => isset($_POST['job']) ? sanitize_text_field(wp_unslash($_POST['job'])) : '',
        'age'             => isset($_POST['age']) ? absint($_POST['age']) : 0,
        'gender'          => isset($_POST['gender']) ? sanitize_text_field(wp_unslash($_POST['gender'])) : '',
        'other_address'   => isset($_POST['other_address'])
            ? naigai_customer_info_substr(sanitize_textarea_field(wp_unslash($_POST['other_address'])), 0, 500)
            : '',
    );

    if (
        $data['name'] === '' ||
        $data['email'] === '' ||
        $data['prefecture'] === '' ||
        $data['city'] === '' ||
        $data['address_number'] === '' ||
        $data['residence_years'] <= 0 ||
        $data['gender'] === ''
    ) {
        wp_send_json_error(array('message' => '必須項目が不足しています。'));
    }

    if (!is_email($data['email'])) {
        wp_send_json_error(array('message' => 'メールアドレスの形式が正しくありません。'));
    }

    if (!in_array($data['gender'], array('male', 'female'), true)) {
        wp_send_json_error(array('message' => '性別の選択が正しくありません。'));
    }

    $job_options = naigai_customer_info_job_options();
    if ($data['job'] !== '' && !isset($job_options[$data['job']])) {
        $data['job'] = '';
    }

    $job_label    = $data['job'] !== '' ? $job_options[$data['job']] : '';
    $gender_label = $data['gender'] === 'male' ? '男性' : '女性';
    $full_address = trim($data['prefecture'] . ' ' . $data['city'] . ' ' . $data['address_number']);

    $to      = 'contact@naigaicorp.net';
    $subject = '【お客様情報】' . $data['name'] . ' 様より';

    $message  = '<html><body>';
    $message .= '<h2>お客様情報フォーム送信</h2>';
    $message .= '<p><strong>お名前:</strong> ' . esc_html($data['name']) . '</p>';
    $message .= '<p><strong>メールアドレス:</strong> ' . esc_html($data['email']) . '</p>';
    $message .= '<p><strong>電話番号:</strong> ' . esc_html($data['phone']) . '</p>';
    $message .= '<p><strong>住所:</strong> ' . esc_html($full_address) . '</p>';
    $message .= '<p><strong>居住年数:</strong> ' . esc_html($data['residence_years']) . '年</p>';

    if ($job_label !== '') {
        $message .= '<p><strong>職業:</strong> ' . esc_html($job_label) . '</p>';
    }

    if (!empty($data['age'])) {
        $message .= '<p><strong>年齢:</strong> ' . esc_html($data['age']) . '</p>';
    }

    $message .= '<p><strong>性別:</strong> ' . esc_html($gender_label) . '</p>';

    if ($data['other_address'] !== '') {
        $message .= '<p><strong>メッセージ:</strong><br>' . nl2br(esc_html($data['other_address'])) . '</p>';
    }

    $message .= '</body></html>';

    $headers = array(
        'Content-Type: text/html; charset=UTF-8',
        'Reply-To: ' . $data['name'] . ' <' . $data['email'] . '>',
    );

    $mail_sent = wp_mail($to, $subject, $message, $headers);

    if (!$mail_sent) {
        wp_send_json_error(array('message' => 'メール送信に失敗しました。'));
    }

    wp_send_json_success(array(
        'message' => '送信が完了しました。',
    ));
}

/**
 * =========================================================
 * Minpaku B2C form mode support
 * =========================================================
 *
 * 目的:
 * - page-minpaku-b2c.php の CTA 表示モードが form の時だけ、
 *   既存 customer-info-form.css の body 条件を満たす。
 *
 * 注意:
 * - 新しいフォームCSSは作らない
 * - 既存 css/customer-info-form.css を使う
 */
add_filter('body_class', function ($classes) {
    if (is_admin() || !is_page()) {
        return $classes;
    }

    $post_id = get_queried_object_id();

    if (!$post_id) {
        return $classes;
    }

    if (get_page_template_slug($post_id) !== 'page-minpaku-b2c.php') {
        return $classes;
    }

    $cta_mode = (string) get_post_meta($post_id, '_mpb_cta_mode', true);

    if ($cta_mode !== 'form') {
        return $classes;
    }

    $classes[] = 'page-template-page-customer-info-form';
    $classes[] = 'page-template-page-customer-info-form-php';
    $classes[] = 'customer-info-form-page';

    return array_unique($classes);
}, 30);

