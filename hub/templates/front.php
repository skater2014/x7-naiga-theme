<?php
/**
 * Hub front template
 */

if (!defined('ABSPATH')) {
    exit;
}

$frontpage_template = get_template_directory() . '/hub/pages/frontpage/templates/frontpage.php';

if (file_exists($frontpage_template)) {
    require $frontpage_template;
}
