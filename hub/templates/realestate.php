<?php

/**
 * =========================================================
 * hub/templates/realestate.php
 * /fudousan 那須中心の不動産ページ
 * =========================================================
 *
 * 方針:
 * - 新規ファイルは作らない
 * - 既存JSは触らない
 * - 既存の Isotope 用クラスを使う
 *   .home-cats-selection
 *   .home-posts
 *   .archive-post-box
 * - 一覧カードはこのテンプレート内の helper で軽量表示する
 * - property-info-table.php は詳細ページ側の主表示に残し、一覧では重くしない
 * - Google Map は _google_map_iframe_1 を使い、
 *   既存 front-map-modal.js が拾う .google-location-trigger / data-map-html に合わせる
 */

if (!defined('ABSPATH')) {
  exit;
}







/**
 * ---------------------------------------------------------
 * /fudousan card meta helpers
 * ---------------------------------------------------------
 * 役割:
 * - 価格メタが複数混在しているため、表示用に一本化する
 * - 売却済みは「0円」だけで判定しない
 * - house-type / region を badge 表示する
 * - Google Map iframe をカードから開けるようにする
 */

/**
 * ---------------------------------------------------------
 * /fudousan hero meta helpers
 * ---------------------------------------------------------
 * 管理画面:
 * hub/pages/fudousan/admin/fudousan-admin.php
 *
 * 使用メタ:
 * - _fudo_hero_kicker
 * - _fudo_hero_title
 * - _fudo_hero_lead
 * - _fudo_hero_image_ids
 * - _fudo_hero_mp4_id
 * - _fudo_hero_youtube_id
 * ---------------------------------------------------------
 */
if (!function_exists('naigai_fudo_page_meta')) {
  function naigai_fudo_page_meta($key, $default = '')
  {
    $post_id = get_queried_object_id();
    $value = $post_id ? get_post_meta($post_id, $key, true) : '';

    if ($value === '' || $value === null) {
      return $default;
    }

    return $value;
  }
}

if (!function_exists('naigai_fudo_clean_media_ids')) {
  function naigai_fudo_clean_media_ids($value)
  {
    if (is_array($value)) {
      $ids = $value;
    } else {
      $ids = preg_split('/[\s,]+/', (string) $value);
    }

    $ids = array_map('absint', $ids);
    $ids = array_filter($ids);

    return array_values(array_unique($ids));
  }
}

if (!function_exists('naigai_fudo_youtube_id_from_value')) {
  function naigai_fudo_youtube_id_from_value($value)
  {
    $value = trim((string) $value);

    if ($value === '') {
      return '';
    }

    if (preg_match('~(?:youtu\.be/|youtube\.com/(?:watch\?v=|embed/|shorts/))([A-Za-z0-9_-]{6,})~', $value, $m)) {
      return $m[1];
    }

    if (preg_match('/^[A-Za-z0-9_-]{6,}$/', $value)) {
      return $value;
    }

    return '';
  }
}

if (!function_exists('naigai_fudo_render_hero_media')) {
  function naigai_fudo_render_hero_media()
  {
    $post_id = get_queried_object_id();

    if (!$post_id) {
      return '';
    }

    $image_ids = naigai_fudo_clean_media_ids(get_post_meta($post_id, '_fudo_hero_image_ids', true));
    $mp4_id    = absint(get_post_meta($post_id, '_fudo_hero_mp4_id', true));
    $youtube   = naigai_fudo_youtube_id_from_value(get_post_meta($post_id, '_fudo_hero_youtube_id', true));

    $slides = array();

    $poster_url = '';
    if (!empty($image_ids[0])) {
      $poster_url = wp_get_attachment_image_url($image_ids[0], 'large');
    }

    if ($mp4_id) {
      $mp4_url = wp_get_attachment_url($mp4_id);

      if ($mp4_url) {
        $poster_attr = $poster_url ? ' poster="' . esc_url($poster_url) . '"' : '';

        $slides[] = '<div class="swiper-slide fudo-hero-media-slide fudo-hero-media-slide--video">'
          . '<video class="fudo-hero__video" src="' . esc_url($mp4_url) . '"' . $poster_attr . ' controls muted playsinline preload="metadata"></video>'
          . '</div>';
      }
    }

    if ($youtube !== '') {
      $slides[] = '<div class="swiper-slide fudo-hero-media-slide fudo-hero-media-slide--youtube">'
        . '<lite-youtube videoid="' . esc_attr($youtube) . '"></lite-youtube>'
        . '</div>';
    }

    foreach ($image_ids as $image_id) {
      $image_html = wp_get_attachment_image(
        $image_id,
        'large',
        false,
        array(
          'class'    => 'fudo-hero__image',
          'loading'  => 'eager',
          'decoding' => 'async',
        )
      );

      if ($image_html) {
        $slides[] = '<div class="swiper-slide fudo-hero-media-slide fudo-hero-media-slide--image">' . $image_html . '</div>';
      }
    }

    if (empty($slides)) {
      return '<div class="fudo-hero__fallback">'
        . '<p class="fudo-hero__fallback-label">Nasu Area</p>'
        . '<p>管理画面でHero画像またはmp4を選択してください。</p>'
        . '</div>';
    }

    if (count($slides) === 1) {
      return '<div class="fudo-hero__single-media">' . preg_replace('/^\s*<div class="swiper-slide[^"]*"[^>]*>|<\/div>\s*$/', '', $slides[0]) . '</div>';
    }

    return '<div class="swiper fudo-hero-swiper js-fudo-hero-swiper" data-fudo-hero-swiper="1">'
      . '<div class="swiper-wrapper">'
      . implode('', $slides)
      . '</div>'
      . '<div class="swiper-pagination fudo-hero-swiper__pagination"></div>'
      . '<button class="swiper-button-prev fudo-hero-swiper__nav fudo-hero-swiper__nav--prev" type="button" aria-label="前へ"></button>'
      . '<button class="swiper-button-next fudo-hero-swiper__nav fudo-hero-swiper__nav--next" type="button" aria-label="次へ"></button>'
      . '</div>';
  }
}

if (!function_exists('naigai_fudo_meta_first')) {
  function naigai_fudo_meta_first($post_id, $keys)
  {
    foreach ((array) $keys as $key) {
      $value = get_post_meta($post_id, $key, true);
      if ($value !== '' && $value !== null) {
        return $value;
      }
    }
    return '';
  }
}

if (!function_exists('naigai_fudo_numeric_value')) {
  function naigai_fudo_numeric_value($value)
  {
    if (is_array($value)) {
      return 0;
    }

    $value = trim((string) $value);
    if ($value === '') {
      return 0;
    }

    $value = str_replace(array(',', '万円', '円', 'm²', '㎡', '坪'), '', $value);
    return is_numeric($value) ? (float) $value : 0;
  }
}

if (!function_exists('naigai_fudo_has_term_name_like')) {
  function naigai_fudo_has_term_name_like($post_id, $needles)
  {
    $taxes = get_object_taxonomies(get_post_type($post_id), 'names');

    foreach ($taxes as $tax) {
      $terms = get_the_terms($post_id, $tax);
      if (empty($terms) || is_wp_error($terms)) {
        continue;
      }

      foreach ($terms as $term) {
        $haystack = $term->slug . ' ' . $term->name;
        foreach ((array) $needles as $needle) {
          if ($needle !== '' && mb_stripos($haystack, $needle) !== false) {
            return true;
          }
        }
      }
    }

    return false;
  }
}

if (!function_exists('naigai_fudo_is_sold')) {
  function naigai_fudo_is_sold($post_id, $price_raw = '')
  {
    $post_id = (int) $post_id;

    /*
     * 最優先:
     * カテゴリ/タクソノミーに「売却済」がある場合。
     */
    if (naigai_fudo_has_term_name_like($post_id, array('売却済', 'sold'))) {
      return true;
    }

    /*
     * 明示的な sold-out メタ。
     */
    $sold_meta = naigai_fudo_meta_first($post_id, array('sold-out', 'sold_out', 'SoldOut', '_sold_out'));
    if ($sold_meta !== '' && !in_array(strtolower((string) $sold_meta), array('0', 'no', 'false', 'off'), true)) {
      return true;
    }

    /*
     * Price=0 だけでは売却済みにしない。
     * house / 土地付き建物系で UnitsSold 系がある場合は売却済み扱い。
     */
    $price = naigai_fudo_numeric_value($price_raw);
    $units_sold = naigai_fudo_meta_first($post_id, array('UnitsSold', 'Units_Sold', 'NewUnitsSold', 'NewUnits_Sold'));

    $is_house_like = (
      get_post_type($post_id) === 'house'
      || naigai_fudo_has_term_name_like($post_id, array('おすすめの土地付き建物', 'recommended-land-and-house'))
    );

    if ($price <= 0 && $is_house_like && $units_sold !== '') {
      return true;
    }

    return false;
  }
}

if (!function_exists('naigai_fudo_get_price_raw')) {
  function naigai_fudo_get_price_raw($post_id)
  {
    /*
     * NewPrice は 0 が入っていることがあるため、正の数なら優先。
     */
    $new_price = naigai_fudo_meta_first($post_id, array('NewPrice', '新価格', '_naigai_property_price'));
    if (naigai_fudo_numeric_value($new_price) > 0) {
      return $new_price;
    }

    return naigai_fudo_meta_first($post_id, array(
      'Price',
      '価格',
      '_naigai_viewing_price',
      '_naigai_property_price',
    ));
  }
}

if (!function_exists('naigai_fudo_format_price_label')) {
  function naigai_fudo_format_price_label($post_id)
  {
    $price_raw = naigai_fudo_get_price_raw($post_id);

    if (naigai_fudo_is_sold($post_id, $price_raw)) {
      return '売却済';
    }

    $price = naigai_fudo_numeric_value($price_raw);

    if ($price > 0) {
      return number_format($price) . '万円';
    }

    return '価格未設定';
  }
}

if (!function_exists('naigai_fudo_area_label')) {
  function naigai_fudo_area_label($value)
  {
    $num = naigai_fudo_numeric_value($value);
    if ($num <= 0) {
      return '';
    }
    return rtrim(rtrim(number_format($num, 2), '0'), '.') . '㎡';
  }
}

if (!function_exists('naigai_fudo_render_card_badges')) {
  function naigai_fudo_render_card_badges($post_id)
  {
    $badge_taxonomies = array(
      'house-type' => '間取り',
      'region'     => '地域',
    );

    $html = '';

    foreach ($badge_taxonomies as $tax => $label) {
      $terms = get_the_terms($post_id, $tax);
      if (empty($terms) || is_wp_error($terms)) {
        continue;
      }

      foreach ($terms as $term) {
        $html .= '<span class="fudo-card-badge fudo-card-badge--' . esc_attr($tax) . '">';
        $html .= '<small>' . esc_html($label) . '</small>';
        $html .= esc_html($term->name);
        $html .= '</span>';
      }
    }

    if ($html === '') {
      return '';
    }

    return '<div class="fudo-card-badges">' . $html . '</div>';
  }
}

if (!function_exists('naigai_fudo_render_card_facts')) {
  function naigai_fudo_render_card_facts($post_id)
  {
    $price_label = naigai_fudo_format_price_label($post_id);
    $is_sold = ($price_label === '売却済');

    $layout = naigai_fudo_meta_first($post_id, array('Layout', 'NewLayout'));
    $land   = naigai_fudo_area_label(naigai_fudo_meta_first($post_id, array('LandArea', 'Land_Area', 'NewLandArea', 'NewLand_Area')));
    $build  = naigai_fudo_area_label(naigai_fudo_meta_first($post_id, array('BuildingArea', 'Building_Area', 'NewBuildingArea', 'NewBuilding_Area')));

    $html = '';

    $html .= '<div class="fudo-post-card__fact fudo-post-card__fact--price">';
    $html .= '<dt>価格</dt>';
    $html .= '<dd class="is-price' . ($is_sold ? ' is-sold' : '') . '">' . esc_html($price_label) . '</dd>';
    $html .= '</div>';

    $location = naigai_fudo_meta_first($post_id, array('Location'));

    if ($location !== '') {
      $html .= '<div class="fudo-post-card__fact fudo-post-card__fact--address">';
      $html .= '<dt>住所</dt>';
      $html .= '<dd>' . esc_html($location) . '</dd>';
      $html .= '</div>';
    }

    if ($layout !== '') {
      $html .= '<div class="fudo-post-card__fact">';
      $html .= '<dt>間取り</dt>';
      $html .= '<dd>' . esc_html($layout) . '</dd>';
      $html .= '</div>';
    }

    if ($land !== '') {
      $html .= '<div class="fudo-post-card__fact">';
      $html .= '<dt>土地</dt>';
      $html .= '<dd>' . esc_html($land) . '</dd>';
      $html .= '</div>';
    }

    if ($build !== '') {
      $html .= '<div class="fudo-post-card__fact">';
      $html .= '<dt>建物</dt>';
      $html .= '<dd>' . esc_html($build) . '</dd>';
      $html .= '</div>';
    }

    return $html;
  }
}

/*
 * ---------------------------------------------------------
 * Google Map 表示用helper
 * ---------------------------------------------------------
 *
 * functions.php 側の役割:
 * - 管理画面にGoogle Map入力欄を出す
 * - _google_map_iframe_1 / _google_map_iframe_2 へ保存する
 *
 * このテンプレート側の役割:
 * - 保存済みの地図iframeを取得する
 * - front-map-modal.js が利用する data-map-html へ渡す
 *
 * つまり、functions.php の保存処理とは役割が別なので、
 * このフロント表示用helperは残す。
 */
if (!function_exists('naigai_fudo_get_map_html')) {
  function naigai_fudo_get_map_html($post_id)
  {
    $map = naigai_fudo_meta_first($post_id, array(
      'GoogleEmbedcode',
      'map_embed_code',
      'map_embed_code_1',
      'map_embed_code_2',
      'NewGoogleEmbedcode',
      'google_embed_code',
      'google_map_iframe_1',
      'google_map_iframe_2',
      '_google_map_iframe_1',
      '_google_map_iframe_2',
    ));

    if ($map === '') {
      return '';
    }

    if (stripos((string) $map, '<iframe') === false) {
      return '';
    }

    return (string) $map;
  }
}

if (!function_exists('naigai_fudo_render_map_button')) {
  function naigai_fudo_render_map_button($post_id)
  {
    $map = function_exists('naigai_fudo_get_map_html') ? naigai_fudo_get_map_html($post_id) : '';

    if ($map === '') {
      return '';
    }

    /*
     * js/front-map-modal.js 用。
     * 重要:
     * - scripts.js は触らない
     * - front-map-modal.js は .google-location-trigger / [data-map-open] を拾う
     * - data-map-html は base64 iframe
     * - SVGは巨大化しやすいので使わない
     */
    return '<button type="button" class="fudo-card-btn fudo-card-btn--map google-location-trigger" data-map-open data-map-html="' . esc_attr(base64_encode($map)) . '" data-post-id="' . esc_attr($post_id) . '"><span class="fudo-card-btn__map-mark" aria-hidden="true">📍</span><span>地図を見る</span></button>';
  }
}


/**
 * ---------------------------------------------------------
 * /fudousan カスタマイザー選択カテゴリー取得
 * ---------------------------------------------------------
 * 役割:
 * - functions.php のカスタマイザー設定 dess_home_cats を読む
 * - 既存フロントのカテゴリータブと同じ考え方にする
 * - タブは .term-{カテゴリID}
 * - カード側にも .term-{カテゴリID} を付ける
 */
if (!function_exists('naigai_fudo_get_customizer_categories')) {
  function naigai_fudo_get_customizer_categories()
  {
    $raw = '';

    if (function_exists('dess_setting')) {
      $raw = (string) dess_setting('dess_home_cats');
    }

    if ($raw === '') {
      $raw = (string) get_theme_mod('dess_home_cats', '');
    }

    $cat_ids = array_values(array_unique(array_filter(array_map('absint', explode(',', $raw)))));

    /*
     * カスタマイザー未設定時の保険。
     * 通常は dess_home_cats が入るのでここは使われない。
     */
    if (empty($cat_ids)) {
      $fallback_slugs = array(
        'naigai-tochi',
        'recommended-land',
        'recommended-land-and-house',
        'naigai-construction',
        'new-house',
        'house',
      );

      foreach ($fallback_slugs as $slug) {
        $term = get_category_by_slug($slug);
        if ($term && !is_wp_error($term)) {
          $cat_ids[] = (int) $term->term_id;
        }
      }

      $cat_ids = array_values(array_unique(array_filter($cat_ids)));
    }

    $cats = array();

    foreach ($cat_ids as $cat_id) {
      $cat = get_category($cat_id);
      if ($cat && !is_wp_error($cat)) {
        $cats[] = $cat;
      }
    }

    return $cats;
  }
}

/**
 * ---------------------------------------------------------
 * /fudousan 投稿カード term クラス取得
 * ---------------------------------------------------------
 * 役割:
 * - Isotope のカテゴリー切替用に .term-{カテゴリID} を付ける
 * - fudo-land / fudo-new は補助クラスとして残す
 */
if (!function_exists('naigai_fudo_get_post_term_classes')) {
  function naigai_fudo_get_post_term_classes($post_id)
  {
    $classes = array();
    $cats = get_the_category($post_id);

    if (!empty($cats)) {
      foreach ($cats as $cat) {
        $classes[] = 'term-' . (int) $cat->term_id;
      }
    }

    return implode(' ', array_unique(array_filter($classes)));
  }
}


/**
 * ---------------------------------------------------------
 * /fudousan カード用メディア表示
 * ---------------------------------------------------------
 * 役割:
 * - functions.php 既存メタキーを使う
 * - page_featured_type = youtube / vimeo
 * - page_video_id = 動画ID
 * - 動画があれば lite-youtube / lite-vimeo を表示
 * - 動画がなければアイキャッチ画像
 * - アイキャッチもなければ no-image
 */
if (!function_exists('naigai_fudo_render_card_media')) {
  function naigai_fudo_render_card_media($post_id)
  {
    $post_id  = (int) $post_id;
    $type     = strtolower(trim((string) get_post_meta($post_id, 'page_featured_type', true)));
    $video_id = trim((string) get_post_meta($post_id, 'page_video_id', true));
    $title    = get_the_title($post_id);

    if ($type === 'youtube' && $video_id !== '') {
      return '<lite-youtube videoid="' . esc_attr($video_id) . '" playlabel="' . esc_attr($title) . '"></lite-youtube>';
    }

    if ($type === 'vimeo' && $video_id !== '') {
      return '<lite-vimeo videoid="' . esc_attr($video_id) . '"><div class="ltv-playbtn"></div></lite-vimeo>';
    }

    if (has_post_thumbnail($post_id)) {
      return get_the_post_thumbnail($post_id, 'large', array(
        'loading'  => 'lazy',
        'decoding' => 'async',
        'alt'      => esc_attr($title),
      ));
    }

    return '<img src="' . esc_url(get_template_directory_uri() . '/images/no-image.jpg') . '" alt="' . esc_attr($title) . '" loading="lazy" decoding="async">';
  }
}


/*
 * 予約モーダル用の画像URLは、このテンプレート専用helperを作らない。
 *
 * functions.php の naigai_get_sidebar_thumbnail_url() が、
 * page_featured_type / page_video_id を使って
 * YouTube・Vimeo・アイキャッチ画像を共通判定するため、
 * 予約ボタンでもその共通関数を使用する。
 *
 * naigai_fudo_render_card_media() は、
 * <lite-youtube> / <lite-vimeo> / <img> のHTMLを返す別用途なので残す。
 */

$page_id = get_queried_object_id();

/**
 * ---------------------------------------------------------
 * SVGアイコン
 * ---------------------------------------------------------
 * 画像ではなく inline SVG。
 * 拡大縮小してもぼやけず、CSSで色も変えられる。
 */
if (!function_exists('naigai_fudo_icon')) {
  function naigai_fudo_icon($type)
  {
    $icons = array(
      'land' => '<svg viewBox="0 0 64 64" aria-hidden="true"><path d="M9 47h46L46 22H18L9 47Z"/><path d="M18 22l8 25M46 22l-8 25M14 38h36"/><path d="M23 16h18"/></svg>',
      'house' => '<svg viewBox="0 0 64 64" aria-hidden="true"><path d="M10 31 32 14l22 17"/><path d="M17 29v24h30V29"/><path d="M27 53V38h10v15"/><path d="M22 34h7M35 34h7"/></svg>',
      'used' => '<svg viewBox="0 0 64 64" aria-hidden="true"><path d="M12 31 32 15l20 16"/><path d="M18 29v23h28V29"/><path d="M24 52V40h16v12"/><path d="M45 18h7v10"/><path d="M13 49h-4M55 49h-5"/></svg>',
      'rent' => '<svg viewBox="0 0 64 64" aria-hidden="true"><path d="M16 53V13h26v40"/><path d="M42 28h10v25"/><path d="M23 22h5M33 22h5M23 31h5M33 31h5M23 40h5M33 40h5"/><path d="M10 53h44"/></svg>',
      'sell' => '<svg viewBox="0 0 64 64" aria-hidden="true"><path d="M18 10h23l8 8v36H18V10Z"/><path d="M41 10v10h8"/><path d="M25 30h16M25 38h16M25 46h10"/></svg>',
      'tourism' => '<svg viewBox="0 0 64 64" aria-hidden="true"><path d="M32 54s17-16 17-29a17 17 0 0 0-34 0c0 13 17 29 17 29Z"/><circle cx="32" cy="25" r="6"/></svg>',
      'request' => '<svg viewBox="0 0 64 64" aria-hidden="true"><path d="M12 14h18c5 0 8 3 8 8v28c0-5-3-8-8-8H12V14Z"/><path d="M52 14H38c-5 0-8 3-8 8v28c0-5 3-8 8-8h14V14Z"/><path d="M30 22v28"/></svg>',
    );

    return isset($icons[$type]) ? $icons[$type] : $icons['house'];
  }
}

/**
 * ---------------------------------------------------------
 * 物件メタ取得
 * ---------------------------------------------------------
 * property-info-table.php と同じメタキー。
 */
if (!function_exists('naigai_fudo_get_card_data')) {
  function naigai_fudo_get_card_data($post_id)
  {
    $price         = get_post_meta($post_id, 'Price', true);
    $location      = get_post_meta($post_id, 'Location', true);
    $land_area     = get_post_meta($post_id, 'LandArea', true);
    $building_area = get_post_meta($post_id, 'BuildingArea', true);
    $sold_out      = get_post_meta($post_id, 'sold-out', true);
    $map_html      = get_post_meta($post_id, '_google_map_iframe_1', true);

    $thumb = get_the_post_thumbnail_url($post_id, 'large');
    if (!$thumb) {
      $thumb = get_template_directory_uri() . '/images/no-image.jpg';
    }

    $price_display = '';
    if ($sold_out === '1' || $sold_out === 'yes' || $sold_out === '売却済') {
      $price_display = '売却済み';
    } elseif ($price !== '' && $price !== null) {
      $price_number = preg_replace('/[^0-9.]/', '', (string) $price);
      if ($price_number !== '') {
        $price_display = number_format((float) $price_number) . '万円';
      } else {
        $price_display = (string) $price;
      }
    }

    $land_area_display = '';
    $land_area_number = preg_replace('/[^0-9.]/', '', (string) $land_area);
    if ($land_area_number !== '') {
      $land_area_display = number_format((float) $land_area_number) . '㎡';
    }

    $building_area_display = '';
    $building_area_number = preg_replace('/[^0-9.]/', '', (string) $building_area);
    if ($building_area_number !== '') {
      $building_area_display = number_format((float) $building_area_number) . '㎡';
    }

    return array(
      'thumb'         => $thumb,
      'price'         => $price_display,
      'location'      => $location,
      'land_area'     => $land_area_display,
      'building_area' => $building_area_display,
      'sold_out'      => $sold_out,
      'map_html'      => $map_html,
    );
  }
}

/**
 * ---------------------------------------------------------
 * カテゴリ分類
 * ---------------------------------------------------------
 * 既存カテゴリ slug が完全確定ではないため、候補を広めに見る。
 */
if (!function_exists('naigai_fudo_filter_class')) {
  function naigai_fudo_filter_class($post_id)
  {
    $terms = get_the_terms($post_id, 'category');
    $slugs = array();

    if (!empty($terms) && !is_wp_error($terms)) {
      foreach ($terms as $term) {
        $slugs[] = $term->slug;
      }
    }

    $haystack = implode(' ', $slugs) . ' ' . get_post_type($post_id) . ' ' . get_the_title($post_id);

    if (preg_match('/tochi|land|土地|bunnjyou|分譲|naigai-tochi|recommended-land/u', $haystack)) {
      return 'fudo-land';
    }

    if (preg_match('/new-house|shinchiku|新築|建売|nasu-jutaku/u', $haystack)) {
      return 'fudo-new';
    }

    if (preg_match('/used|chuko|中古|別荘/u', $haystack)) {
      return 'fudo-used';
    }

    if (preg_match('/rent|rental|chintai|賃貸|貸家|貸別荘/u', $haystack)) {
      return 'fudo-rent';
    }

    if (preg_match('/tourism|kanko|観光|column|blog|コラム/u', $haystack)) {
      return 'fudo-tourism';
    }

    return 'fudo-land';
  }
}

$entry_cards = array(
  array(
    'key'   => 'land',
    'icon'  => 'land',
    'title' => '土地一覧',
    'text'  => '那須エリアの売地・分譲地・事業用地を探す',
    'href'  => '#fudousan-list',
  ),
  array(
    'key'   => 'new',
    'icon'  => 'house',
    'title' => '新築一戸建て',
    'text'  => '新築住宅・建売住宅・家づくりの入口',
    'href'  => '#fudousan-list',
  ),
  array(
    'key'   => 'used',
    'icon'  => 'used',
    'title' => '中古住宅',
    'text'  => '中古戸建・別荘・移住向け住宅を探す',
    'href'  => '#fudousan-list',
  ),
  array(
    'key'   => 'rent',
    'icon'  => 'rent',
    'title' => '賃貸物件',
    'text'  => '貸家・アパート・店舗などの賃貸相談',
    'href'  => '#fudousan-list',
  ),
  array(
    'key'   => 'sell',
    'icon'  => 'sell',
    'title' => '売却・買取相談',
    'text'  => '土地・建物・別荘の売却や査定相談',
    'href'  => home_url('/contact/'),
  ),
  array(
    'key'   => 'tourism',
    'icon'  => 'tourism',
    'title' => '観光コラム',
    'text'  => '那須の暮らし・観光・地域情報を見る',
    'href'  => '#fudousan-list',
  ),
  array(
    'key'   => 'request',
    'icon'  => 'request',
    'title' => '資料請求',
    'text'  => '土地・住宅・移住相談の資料を取り寄せる',
    'href'  => home_url('/contact/'),
  ),
);

$fudo_cats = function_exists('naigai_fudo_get_customizer_categories')
  ? naigai_fudo_get_customizer_categories()
  : array();

$fudo_cat_ids = array();

foreach ($fudo_cats as $cat) {
  $fudo_cat_ids[] = (int) $cat->term_id;
}

$tabs = array(
  '*' => 'すべて',
);

/*
 * 初期表示は「すべて」ではなく、おすすめ系カテゴリ。
 * dess_home_cats で選択されたカテゴリから、
 * おすすめ / お勧め / オススメ / recommended / osusume を探す。
 */
$default_filter = '*';

foreach ($fudo_cats as $cat) {
  $filter_key = '.term-' . (int) $cat->term_id;
  $tabs[$filter_key] = $cat->name;

  $tab_text = (string) $cat->name . ' ' . (string) $cat->slug;

  if (
    $default_filter === '*'
    && preg_match('/おすすめ物件|おすすめ|お勧め|オススメ|recommended|recommend|osusume/i', $tab_text)
  ) {
    $default_filter = $filter_key;
  }
}

$public_post_types = get_post_types(array('public' => true), 'names');
$wanted_post_types = array('post', 'house', 'land', 'realestate', 'estate', 'property', 'rent', 'blog');
$post_types = array_values(array_intersect($wanted_post_types, $public_post_types));

if (empty($post_types)) {
  $post_types = array('post');
}

$query_args = array(
  'post_type'           => $post_types,
  'post_status'         => 'publish',
  'posts_per_page'      => 18,
  'orderby'             => 'date',
  'order'               => 'DESC',
  'ignore_sticky_posts' => true,
);

if (!empty($fudo_cat_ids)) {
  $query_args['category__in'] = $fudo_cat_ids;
}

$query = new WP_Query($query_args);

$items = array();
$counts = array(
  'fudo-land'    => 0,
  'fudo-new'     => 0,
  'fudo-used'    => 0,
  'fudo-rent'    => 0,
  'fudo-tourism' => 0,
);

if ($query->have_posts()) {
  while ($query->have_posts()) {
    $query->the_post();

    $post_id = get_the_ID();
    $semantic_class = naigai_fudo_filter_class($post_id);
    $term_class     = function_exists('naigai_fudo_get_post_term_classes') ? naigai_fudo_get_post_term_classes($post_id) : '';
    $class          = trim($term_class . ' ' . $semantic_class);
    foreach (preg_split('/\s+/', trim($class)) as $class_name) {
      if (isset($counts[$class_name])) {
        $counts[$class_name]++;
      }
    }

    $items[] = array(
      'post_id' => $post_id,
      'class'   => $class,
    );
  }
  wp_reset_postdata();
}

?>

<div class="hub-page hub-page-realestate fudousan-page">

  <?php
  $fudo_hero_kicker = naigai_fudo_page_meta('_fudo_hero_kicker', 'Nasu Real Estate');
  $fudo_hero_title  = naigai_fudo_page_meta('_fudo_hero_title', '那須の土地・住まい・不動産相談');
  $fudo_hero_lead   = naigai_fudo_page_meta('_fudo_hero_lead', '那須エリアを中心に、土地探し・住宅購入・賃貸・売却相談まで。暮らしと事業に合わせた不動産情報をご案内します。');
  ?>
  <section class="fudo-hero">
    <div class="fudo-shell fudo-hero__grid">
      <div class="fudo-hero__body">
        <p class="fudo-kicker"><?php echo esc_html($fudo_hero_kicker); ?></p>
        <h1><?php echo nl2br(esc_html($fudo_hero_title)); ?></h1>
        <p class="fudo-hero__lead"><?php echo nl2br(esc_html($fudo_hero_lead)); ?></p>

        <div class="fudo-hero__actions">
          <a href="#fudousan-list" class="fudo-btn fudo-btn--primary">物件を探す</a>
          <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="fudo-btn fudo-btn--ghost">相談する</a>
        </div>
      </div>

      <div class="fudo-hero__panel fudo-hero__panel--media" aria-label="那須エリアの不動産相談">
        <?php echo naigai_fudo_render_hero_media(); ?>
      </div>
    </div>
  </section>


  <section class="fudo-entry">
    <div class="fudo-shell">
      <div class="fudo-section-head">
        <p class="fudo-kicker">Search Menu</p>
        <h2>目的から探す</h2>
      </div>

      <div class="fudo-entry-grid">
        <?php foreach ($entry_cards as $card) : ?>
          <a class="fudo-entry-card fudo-entry-card--<?php echo esc_attr($card['key']); ?>" href="<?php echo esc_url($card['href']); ?>">
            <span class="fudo-entry-card__icon"><?php echo naigai_fudo_icon($card['icon']); ?></span>
            <span class="fudo-entry-card__title"><?php echo esc_html($card['title']); ?></span>
            <span class="fudo-entry-card__text"><?php echo esc_html($card['text']); ?></span>
            <span class="fudo-entry-card__arrow">詳しく見る <span aria-hidden="true">→</span></span>
          </a>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section id="fudousan-list" class="fudo-list-section">
    <div class="fudo-shell">
      <div class="fudo-section-head fudo-section-head--row">
        <div>
          <p class="fudo-kicker">Property & Area Column</p>
          <h2>カテゴリー別で探す</h2>
        </div>
        <p class="fudo-section-note">
          土地・住宅・賃貸・観光コラムを切り替えて表示します。
        </p>
      </div>

      <nav class="home-cats-selection fudo-tabs" aria-label="不動産カテゴリー">
        <ul>
          <?php foreach ($tabs as $filter => $label) : ?>
            <li>
              <a href="<?php echo esc_attr($filter); ?>" class="<?php echo $filter === $default_filter ? 'active' : ''; ?>" data-fudo-default-tab="<?php echo $filter === $default_filter ? '1' : '0'; ?>">
                <?php echo esc_html($label); ?>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      </nav>
      <div class="home-posts archive-posts fudo-posts">
        <?php foreach ($items as $item) : ?>
          <?php
          /*
           * 一覧カードの1件分。
           * ここでは余計な map_payload 等を作らない。
           * 地図ボタンは naigai_fudo_render_map_button() で必要な時だけ生成する。
           */
          $post_id       = isset($item['post_id']) ? (int) $item['post_id'] : 0;
          $card_class    = isset($item['class']) ? (string) $item['class'] : '';
          $is_blog_card  = (strpos($card_class, 'fudo-tourism') !== false);
          $featured_type = strtolower(trim((string) get_post_meta($post_id, 'page_featured_type', true)));
          $is_video_card = in_array($featured_type, array('youtube', 'vimeo'), true);

          if ($post_id <= 0) {
            continue;
          }
          ?>

          <article class="archive-post-box main-custom-post fudo-post-card <?php echo esc_attr($card_class); ?>">
            <?php if ($is_video_card) : ?>
              <?php
              /*
               * 共通 js/scripts.js の予約モーダル処理は、
               * .blog-post-image.youtube / .blog-post-image.vimeo を見て
               * lite-youtube / lite-vimeo から動画サムネイルを判定する。
               *
               * そのため、不動産専用JSで動画画像URLを新しく組み立てず、
               * 既存の共通仕様に合わせて動画種別クラスだけ付ける。
               */
              ?>
              <div class="fudo-post-card__image blog-post-image fudo-post-card__image--video <?php echo esc_attr($featured_type); ?>">
                <?php echo naigai_fudo_render_card_media($post_id); ?>
              </div>
            <?php else : ?>
              <a class="fudo-post-card__image blog-post-image" href="<?php echo esc_url(get_permalink($post_id)); ?>">
                <?php echo naigai_fudo_render_card_media($post_id); ?>
              </a>
            <?php endif; ?>

            <div class="fudo-post-card__body">
              <h2 class="fudo-post-card__title">
                <a href="<?php echo esc_url(get_permalink($post_id)); ?>">
                  <?php echo esc_html(get_the_title($post_id)); ?>
                </a>
              </h2>

              <?php if ($is_blog_card) : ?>
                <p class="fudo-post-card__excerpt">
                  <?php
                  $excerpt = get_the_excerpt($post_id);
                  if ($excerpt === '') {
                    $excerpt = wp_trim_words(
                      wp_strip_all_tags((string) get_post_field('post_content', $post_id)),
                      70,
                      '…'
                    );
                  }
                  echo esc_html($excerpt);
                  ?>
                </p>
              <?php else : ?>
                <?php echo naigai_fudo_render_card_badges($post_id); ?>

                <dl class="fudo-post-card__facts">
                  <?php echo naigai_fudo_render_card_facts($post_id); ?>
                </dl>
              <?php endif; ?>

              <div class="fudo-post-card__actions">
                <a class="fudo-card-btn" href="<?php echo esc_url(get_permalink($post_id)); ?>">
                  <?php echo $is_blog_card ? '記事を見る' : '詳細を見る'; ?>
                </a>

                <?php if (!$is_blog_card) : ?>
                  <?php echo naigai_fudo_render_map_button($post_id); ?>

                  <?php
                  /*
                   * 予約モーダルへ渡す情報。
                   *
                   * 投稿ID:
                   * - WordPress上の予約対象投稿を識別する
                   *
                   * タイトル / 価格:
                   * - 不動産カードの表示内容をモーダルへ渡す
                   *
                   * サムネイル:
                   * - functions.php の共通関数を使う
                   * - YouTube / Vimeo / アイキャッチを共通判定する
                   *
                   * 共通関数が読み込まれていない場合だけ、
                   * アイキャッチ → no-image の順でフォールバックする。
                   */
                  $reservation_title = get_the_title($post_id);
                  $reservation_price = naigai_fudo_format_price_label($post_id);

                  if (function_exists('naigai_get_sidebar_thumbnail_url')) {
                    $reservation_thumb = naigai_get_sidebar_thumbnail_url($post_id);
                  } else {
                    $reservation_thumb = get_the_post_thumbnail_url($post_id, 'large');

                    if (!$reservation_thumb) {
                      $reservation_thumb = get_template_directory_uri() . '/images/no-image.jpg';
                    }
                  }
                  ?>
                  <?php
                  /*
                   * data-reservation-* は /fudousan 専用JSの補助データ。
                   *
                   * 共通 scripts.js もカードDOMから情報を取得できるため、
                   * 動画判定そのものは共通 scripts.js の仕様へ合わせる。
                   *
                   * 既存の不動産ページ側処理との互換性を壊さないため、
                   * data属性は残し、画像URLだけ共通関数の結果に統一する。
                   */
                  ?>
                  <a
                    class="fudo-card-btn fudo-card-btn--ghost store-reserve-link"
                    href="#store-reservation"
                    data-post-id="<?php echo esc_attr($post_id); ?>"
                    data-reservation-post-id="<?php echo esc_attr($post_id); ?>"
                    data-reservation-title="<?php echo esc_attr($reservation_title); ?>"
                    data-reservation-price="<?php echo esc_attr($reservation_price); ?>"
                    data-reservation-thumb="<?php echo esc_url($reservation_thumb); ?>"
                    data-reservation-thumbnail="<?php echo esc_url($reservation_thumb); ?>"
                    data-reservation-image="<?php echo esc_url($reservation_thumb); ?>">
                    来店予約
                  </a>
                <?php endif; ?>
              </div>
            </div>
          </article>
        <?php endforeach; ?>

        <?php if (empty($items)) : ?>
          <article class="archive-post-box main-custom-post fudo-post-card fudo-empty-card">
            <div class="fudo-empty-card__inner">
              <p class="fudo-empty-card__label">掲載準備中</p>
              <h3>現在、表示できる物件がありません。</h3>
              <p>カスタマイザーで選択しているカテゴリー、または公開済み投稿のカテゴリー設定を確認してください。</p>
              <a href="<?php echo esc_url(home_url('/contact/')); ?>">掲載物件について相談する</a>
            </div>
          </article>
        <?php endif; ?>

        <?php if ((int) $counts['fudo-used'] === 0) : ?>
          <article class="archive-post-box main-custom-post fudo-post-card fudo-empty-card fudo-used">
            <div class="fudo-empty-card__inner">
              <p class="fudo-empty-card__label">掲載準備中</p>
              <h3>現在、中古住宅の掲載物件はありません。</h3>
              <p>那須エリアの中古住宅・別荘・移住向け住宅をお探しの場合は、ご希望条件をお知らせください。</p>
              <a href="<?php echo esc_url(home_url('/contact/')); ?>">中古住宅を相談する</a>
            </div>
          </article>
        <?php endif; ?>

        <?php if ((int) $counts['fudo-rent'] === 0) : ?>
          <article class="archive-post-box main-custom-post fudo-post-card fudo-empty-card fudo-rent">
            <div class="fudo-empty-card__inner">
              <p class="fudo-empty-card__label">掲載準備中</p>
              <h3>現在、賃貸物件の掲載物件はありません。</h3>
              <p>貸家・アパート・店舗など、那須エリアの賃貸物件をお探しの場合はご相談ください。</p>
              <a href="<?php echo esc_url(home_url('/contact/')); ?>">賃貸物件を相談する</a>
            </div>
          </article>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <section class="fudo-cta">
    <div class="fudo-shell">
      <div class="fudo-cta__panel">
        <div>
          <p class="fudo-kicker">Contact</p>
          <h2>那須の不動産について相談する</h2>
          <p>
            土地探し、住宅購入、賃貸、売却・買取、資料請求など、
            目的に合わせてご相談ください。
          </p>
        </div>

        <div class="fudo-cta__actions">
          <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="fudo-btn fudo-btn--primary">お問い合わせ</a>
          <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="fudo-btn fudo-btn--ghost">資料請求</a>
        </div>
      </div>
    </div>
  </section>



</div>