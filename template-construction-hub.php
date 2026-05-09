<?php
/**
 * Template Name: Iezukuri
 * Template Post Type: page
 *
 * WordPress管理画面用の入口。
 * 本体は hub/pages/iezukuri/templates/template-iezukuri.php。
 */

if (!defined('ABSPATH')) {
    exit;
}

$template = get_template_directory() . '/hub/pages/iezukuri/templates/template-iezukuri.php';

if (file_exists($template)) {
    require $template;
    return;
}

wp_die('Iezukuri template not found.');
