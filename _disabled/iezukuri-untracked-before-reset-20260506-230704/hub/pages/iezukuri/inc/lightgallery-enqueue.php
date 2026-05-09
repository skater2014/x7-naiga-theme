<?php
/**
 * hub/pages/iezukuri/inc/lightgallery-enqueue.php
 *
 * iez_plan 詳細ページだけ lightGallery v2 を読み込む。
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('wp_enqueue_scripts', function () {
    if (!is_singular('iez_plan')) {
        return;
    }

    $base_dir = get_template_directory() . '/hub/pages/iezukuri';
    $base_uri = get_template_directory_uri() . '/hub/pages/iezukuri';

    $vendor_css = '/vendor/lightgallery/css/lightgallery-bundle.min.css';
    $vendor_js  = '/vendor/lightgallery/js/lightgallery.umd.min.js';
    $thumb_js   = '/vendor/lightgallery/js/plugins/thumbnail/lg-thumbnail.umd.min.js';
    $zoom_js    = '/vendor/lightgallery/js/plugins/zoom/lg-zoom.umd.min.js';
    $init_js    = '/js/iezukuri-lightgallery-init.js';

    if (file_exists($base_dir . $vendor_css)) {
        wp_enqueue_style(
            'naigai-iez-lightgallery',
            $base_uri . $vendor_css,
            array(),
            filemtime($base_dir . $vendor_css)
        );
    }

    if (file_exists($base_dir . $vendor_js)) {
        wp_enqueue_script(
            'naigai-iez-lightgallery',
            $base_uri . $vendor_js,
            array(),
            filemtime($base_dir . $vendor_js),
            true
        );
    }

    if (file_exists($base_dir . $thumb_js)) {
        wp_enqueue_script(
            'naigai-iez-lightgallery-thumbnail',
            $base_uri . $thumb_js,
            array('naigai-iez-lightgallery'),
            filemtime($base_dir . $thumb_js),
            true
        );
    }

    if (file_exists($base_dir . $zoom_js)) {
        wp_enqueue_script(
            'naigai-iez-lightgallery-zoom',
            $base_uri . $zoom_js,
            array('naigai-iez-lightgallery'),
            filemtime($base_dir . $zoom_js),
            true
        );
    }

    if (file_exists($base_dir . $init_js)) {
        wp_enqueue_script(
            'naigai-iez-lightgallery-init',
            $base_uri . $init_js,
            array(
                'naigai-iez-lightgallery',
                'naigai-iez-lightgallery-thumbnail',
                'naigai-iez-lightgallery-zoom',
            ),
            filemtime($base_dir . $init_js),
            true
        );
    }
}, 40);
