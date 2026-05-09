<?php
/**
 * /iezukuri トップ
 * 3枚カード押下 → taxonomy term → 紐づく iez_plan をHTMLで返す
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('wp_ajax_naigai_iez_plan_fragment', 'naigai_iezukuri_ajax_plan_fragment');
add_action('wp_ajax_nopriv_naigai_iez_plan_fragment', 'naigai_iezukuri_ajax_plan_fragment');

function naigai_iezukuri_ajax_plan_fragment()
{
    check_ajax_referer('naigai_iez_plan_fragment', 'nonce');

    $iez_plan_term_slug = isset($_POST['term'])
        ? sanitize_title(wp_unslash($_POST['term']))
        : '';

    $template = get_template_directory() . '/hub/pages/iezukuri/templates/top/section-plan-fragment.php';

    if (!file_exists($template)) {
        wp_send_json_error(array(
            'message' => 'section-plan-fragment.php が見つかりません。',
        ), 404);
    }

    ob_start();
    include $template;
    $html = ob_get_clean();

    wp_send_json_success(array(
        'html' => $html,
    ));
}
