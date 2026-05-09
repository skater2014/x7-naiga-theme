<?php
/**
 * Front Page Direct V2
 * WordPressトップで必ず読まれる front-page.php。
 */

if (!defined('ABSPATH')) {
  exit;
}

$page_id = get_queried_object_id();

add_filter('body_class', function ($classes) {
  $classes[] = 'front-v2-page';
  $classes[] = 'hub-page';
  $classes[] = 'hub-context-front';
  return array_unique($classes);
});

get_header('77');

echo "\n<!-- FRONT_PAGE_DIRECT_V2_ACTIVE -->\n";

get_template_part('template-parts/hub/front-page-v2', null, array(
  'post_id' => $page_id,
));

get_footer();
