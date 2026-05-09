<?php
/**
 * =========================================================
 * hub/inc/customhome-contact-form-bridge.php
 *
 * /iezukuri/contact 内に既存お問い合わせフォーム本体を表示する。
 *
 * 重要:
 * - 誘導リンクは出さない
 * - /contact と同じフォーム共通パーツを読み込む
 * - 送信処理は既存 AJAX customer_info_submit を使う
 * =========================================================
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('naigai_ch_render_existing_contact_form_bridge')) {
    function naigai_ch_render_existing_contact_form_bridge($post_id = 0)
    {
        $template = locate_template('template-parts/contact/customer-info-form-inner.php');

        if (!$template) {
            return '<div class="ch-contact-form-missing"><p>フォーム共通パーツが見つかりません。</p></div>';
        }

        ob_start();
        include $template;
        $html = ob_get_clean();

        return '<div class="ch-contact-form-embedded ch-contact-form-embedded--inline">'
            . $html
            . '</div>';
    }
}

if (!function_exists('naigai_ch_existing_contact_form_shortcode')) {
    function naigai_ch_existing_contact_form_shortcode()
    {
        return naigai_ch_render_existing_contact_form_bridge(get_queried_object_id());
    }

    add_shortcode('naigai_existing_contact_form', 'naigai_ch_existing_contact_form_shortcode');
}
