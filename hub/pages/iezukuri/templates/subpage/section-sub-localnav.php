<?php
if (!defined('ABSPATH')) {
  exit;
}

$post_id  = get_queried_object_id();
$sections = naigai_iez_sub_json($post_id, '_iez_sub_sections_json');

$nav_items = array();

foreach ($sections as $section) {
  $key   = isset($section['key']) ? sanitize_title($section['key']) : '';
  $label = isset($section['label']) ? trim((string) $section['label']) : '';

  if ($key && $label) {
    $nav_items[] = array(
      'key' => $key,
      'label' => $label,
    );
  }
}

if (empty($nav_items)) {
  return;
}
?>

<nav class="iez-sub-localnav" aria-label="ページ内見出し">
  <div class="iez-sub-localnav__inner">
    <?php foreach ($nav_items as $item) : ?>
      <a class="iez-sub-localnav__link" href="#<?php echo esc_attr($item['key']); ?>">
        <?php echo esc_html($item['label']); ?>
      </a>
    <?php endforeach; ?>
  </div>
</nav>
