<?php
/**
 * Legacy front-page-v2 loader
 * 実体は hub/pages/frontpage/templates/frontpage.php
 */

if (!defined('ABSPATH')) {
    exit;
}

$frontpage_template = get_template_directory() . '/hub/pages/frontpage/templates/frontpage.php';

if (file_exists($frontpage_template)) {
    require $frontpage_template;
}
