<?php
/**
 * =========================================================
 * inc/functions/page-assets-loader.php
 * 固定ページ専用CSSローダー
 *
 * 目的:
 * - style.css にページ専用CSSを戻さない
 * - assets/css/pages/*.css を対象ページだけで読む
 * - header top 色調整も assets/css/pages/page-body-header.css で管理
 * - HTML構造は変えない
 * =========================================================
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('wp_enqueue_scripts', function () {
    if (!is_page()) {
        return;
    }

    $post_id = get_queried_object_id();

    if (!$post_id) {
        return;
    }

    $slug     = (string) get_post_field('post_name', $post_id);
    $template = (string) get_page_template_slug($post_id);

    $assets = array(
        'naigai-page-company' => array(
            'file'      => 'assets/css/pages/company.css',
            'slugs'     => array('company'),
            'templates' => array('page-company.php'),
        ),

        'naigai-page-wakugui' => array(
            'file'      => 'assets/css/pages/wakugui.css',
            'slugs'     => array(
                'hokubei-jutaku',
                'nasu-used-renovation',
                'wakugui',
                'hokubei',
                'hokubei-jyutaku',
                'used-renovation',
                'chuko-jutaku',
            ),
            'templates' => array(
                'page-wakugumi.php',
                'page-wakugui.php',
                'page-used-renovation.php',
                'page-north-american-house.php',
            ),
        ),

        'naigai-page-nasu-guide' => array(
            'file'      => 'assets/css/pages/nasu-guide.css',
            'slugs'     => array(
                'nasu-ideal-home',
                'nasu-guide',
                'nasu-kurashi',
            ),
            'templates' => array(
                'page-nasu-guide.php',
            ),
        ),

        'naigai-page-zairai' => array(
            'file'      => 'assets/css/pages/zairai.css',
            'slugs'     => array(
                'zairai-kouhou',
                'zairai',
                'zairai-jutaku',
                'traditional',
            ),
            'templates' => array(
                'page-zairai.php',
                'page-zairai-kouhou.php',
            ),
        ),

        'naigai-page-sekou' => array(
            'file'      => 'assets/css/pages/sekou.css',
            'slugs'     => array(
                'sekou-jirei',
                'sekou',
                'sekou-example',
            ),
            'templates' => array(
                'page-sekoujirei.php',
                'page-sekou.php',
            ),
        ),
    );

    $matched = false;
    $body_header_matched = false;

    foreach ($assets as $handle => $rule) {
        if (!in_array($slug, $rule['slugs'], true) && !in_array($template, $rule['templates'], true)) {
            continue;
        }

        $matched = true;

        // /company は body / header top の色上書き対象から外す。
        if ($handle !== 'naigai-page-company') {
            $body_header_matched = true;
        }

        $relative_path = $rule['file'];
        $absolute_path = get_template_directory() . '/' . $relative_path;

        if (!file_exists($absolute_path)) {
            continue;
        }

        wp_enqueue_style(
            $handle,
            get_template_directory_uri() . '/' . $relative_path,
            array(),
            filemtime($absolute_path)
        );
    }

    // /minpaku は minpaku-support-page wakugui-page 構造だが、本文CSSは既存側に任せる。
// ここでは body と header__top 色だけ追加する。
$body_header_only_slugs = array('minpaku');
$body_header_only_templates = array('page-minpaku-support.php');

if (in_array($slug, $body_header_only_slugs, true) || in_array($template, $body_header_only_templates, true)) {
    $body_header_matched = true;
}

    if ($body_header_matched) {
        $header_relative_path = 'assets/css/pages/page-body-header.css';
        $header_absolute_path = get_template_directory() . '/' . $header_relative_path;

        if (file_exists($header_absolute_path)) {
            wp_enqueue_style(
                'naigai-page-body-header',
                get_template_directory_uri() . '/' . $header_relative_path,
                array(),
                filemtime($header_absolute_path)
            );
        }
    }
}, 30);
