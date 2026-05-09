<?php
/**
 * 家づくりプラン PDF専用表示
 *
 * URL:
 * /iezukuri/plan/{slug}/?plan_pdf=1
 *
 * 目的:
 * - ブラウザ印刷より安定したPDF用HTMLを作る
 * - 将来的に Puppeteer / Playwright / wkhtmltopdf でPDF生成する土台
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('template_redirect', function () {
    if (!is_singular('iez_plan')) {
        return;
    }

    if (!isset($_GET['plan_pdf']) || $_GET['plan_pdf'] !== '1') {
        return;
    }

    $post_id = get_queried_object_id();

    if (!$post_id) {
        return;
    }

    status_header(200);
    nocache_headers();

    $template = get_template_directory() . '/hub/pages/iezukuri/templates/pdf-plan.php';

    if (file_exists($template)) {
        include $template;
        exit;
    }

    wp_die('PDFテンプレートが見つかりません。');
});
