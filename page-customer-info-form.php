<?php
/*
Template Name: Customer Information Form
*/

if (!defined('ABSPATH')) {
    exit;
}

get_header('77');

$template = locate_template('template-parts/contact/customer-info-form-inner.php');
if ($template) {
    include $template;
}

get_footer();
