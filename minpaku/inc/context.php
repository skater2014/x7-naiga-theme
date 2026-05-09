<?php
if (!defined('ABSPATH')) exit;

if (!function_exists('naigai_is_minpaku_context')) {
    function naigai_is_minpaku_context() {
        return is_post_type_archive('minpaku')
            || is_singular('minpaku')
            || is_page_template('page-minpaku-b2c.php')
            || is_page_template('page-minpaku-support.php');
    }
}
