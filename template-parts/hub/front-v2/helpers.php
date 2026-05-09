<?php
if (!defined('ABSPATH')) exit;

function front_v2_meta_first($post_id, $keys, $default = '') {
  foreach ((array) $keys as $key) {
    $value = get_post_meta($post_id, $key, true);
    if ($value !== '' && $value !== null && $value !== array()) {
      return $value;
    }
  }
  return $default;
}

function front_v2_format_price($value) {
  if ($value === '' || $value === null) return '';
  if (is_numeric($value)) return number_format((float) $value) . '万円';
  return $value;
}

function front_v2_format_area($value) {
  if ($value === '' || $value === null) return '';
  if (is_numeric($value)) return rtrim(rtrim(number_format((float) $value, 2), '0'), '.') . '㎡';
  return $value;
}

function front_v2_post_media($post_id, $size = 'large') {
  $type = get_post_meta($post_id, 'page_featured_type', true);
  $video_id = get_post_meta($post_id, 'page_video_id', true);

  if ($type === 'youtube' && $video_id) {
    return '<lite-youtube videoid="' . esc_attr($video_id) . '"></lite-youtube>';
  }

  if ($type === 'vimeo' && $video_id) {
    return '<iframe src="https://player.vimeo.com/video/' . esc_attr($video_id) . '" loading="lazy" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>';
  }

  $mp4_id = front_v2_meta_first($post_id, array(
    'page_video_mp4_id',
    '_page_video_mp4_id',
    '_mpb_hero_video_mp4_id',
    '_hero_video_mp4_id',
    'hero_video_mp4_id',
  ));

  $mp4_url = '';
  if ($mp4_id) {
    $mp4_url = is_numeric($mp4_id) ? wp_get_attachment_url((int) $mp4_id) : $mp4_id;
  }

  if ($mp4_url) {
    return '<video src="' . esc_url($mp4_url) . '" controls muted playsinline preload="metadata"></video>';
  }

  if (has_post_thumbnail($post_id)) {
    return get_the_post_thumbnail($post_id, $size, array(
      'loading' => 'lazy',
      'alt' => esc_attr(get_the_title($post_id)),
    ));
  }

  return '<span class="front-v2-noimage">NO IMAGE</span>';
}

function front_v2_property_meta($post_id) {
  return array(
    'price' => front_v2_format_price(front_v2_meta_first($post_id, array(
      'price', '_price', 'bukkakaku', 'kakaku', 'fudo_kakaku', 'property_price', 'sale_price'
    ))),
    'land' => front_v2_format_area(front_v2_meta_first($post_id, array(
      'land_area', '_land_area', 'tochimenseki', 'tochi_menseki', 'fudo_tochimenseki', 'property_land_area'
    ))),
    'building' => front_v2_format_area(front_v2_meta_first($post_id, array(
      'building_area', '_building_area', 'tatemonomenseki', 'tatemono_menseki', 'fudo_tatemonomenseki', 'property_building_area'
    ))),
    'address' => front_v2_meta_first($post_id, array(
      'address', 'shozaichi', '所在地', 'property_address'
    )),
  );
}

function front_v2_selected_category_ids() {
  $raw = get_theme_mod('dess_home_cats', array());

  if (is_string($raw)) {
    $maybe = maybe_unserialize($raw);
    $raw = is_array($maybe) ? $maybe : array_filter(array_map('trim', explode(',', $raw)));
  }

  return array_values(array_filter(array_map('intval', (array) $raw)));
}
