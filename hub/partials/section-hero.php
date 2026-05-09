<?php
if (!defined('ABSPATH')) {
  exit;
}



/* === NAIGAI FRONT HERO BRANCH START === */
$naigai_front_context = isset($args['context']) ? (string) $args['context'] : '';

if ($naigai_front_context === 'front') {
  $front_page_id = isset($args['page_id']) ? (int) $args['page_id'] : get_queried_object_id();

  $front_slide_defaults = array(
    1 => array(
      'label' => 'LAND',
      'title' => '土地を探す',
      'text'  => '那須エリアの土地情報を探す入口です。',
      'url'   => home_url('/tochi/'),
    ),
    2 => array(
      'label' => 'RENT',
      'title' => '家を借りる',
      'text'  => '賃貸・貸別荘など、利用目的に合わせて探します。',
      'url'   => home_url('/rent/'),
    ),
    3 => array(
      'label' => 'BUY',
      'title' => '家を買う',
      'text'  => '中古別荘・住宅・土地建物の購入相談はこちら。',
      'url'   => home_url('/nasu-jutaku/'),
    ),
    4 => array(
      'label' => 'BUILD',
      'title' => '家づくり',
      'text'  => '那須らしい暮らしを叶える住まいをご提案します。',
      'url'   => home_url('/construction-hub/'),
    ),
    5 => array(
      'label' => 'STAY',
      'title' => '民泊・貸別荘',
      'text'  => '別荘の有効活用や民泊運営をサポートします。',
      'url'   => home_url('/minpaku-stay/'),
    ),
  );

  $front_slides = array();

  foreach ($front_slide_defaults as $i => $default) {
    $enabled = get_post_meta($front_page_id, "_front_slide_{$i}_enabled", true);
    if ($enabled === '0') {
      continue;
    }

    $image_id   = (int) get_post_meta($front_page_id, "_front_slide_{$i}_image_id", true);
    $video_type = get_post_meta($front_page_id, "_front_slide_{$i}_video_type", true);
    $video_id   = get_post_meta($front_page_id, "_front_slide_{$i}_video_id", true);

    $front_slides[] = array(
      'label'      => get_post_meta($front_page_id, "_front_slide_{$i}_label", true) ?: $default['label'],
      'title'      => get_post_meta($front_page_id, "_front_slide_{$i}_title", true) ?: $default['title'],
      'text'       => get_post_meta($front_page_id, "_front_slide_{$i}_text", true) ?: $default['text'],
      'url'        => get_post_meta($front_page_id, "_front_slide_{$i}_url", true) ?: $default['url'],
      'image_url'  => $image_id ? wp_get_attachment_image_url($image_id, 'full') : '',
      'video_type' => $video_type ?: '',
      'video_id'   => $video_id ?: '',
    );
  }

  if (empty($front_slides)) {
    $front_slides[] = $front_slide_defaults[1] + array(
      'image_url'  => get_the_post_thumbnail_url($front_page_id, 'full'),
      'video_type' => '',
      'video_id'   => '',
    );
  }

  $first_slide = $front_slides[0];
  $is_swiper = count($front_slides) > 1;
  ?>
  <section class="front-hub-hero" data-front-hub-hero>
    <div class="front-hub-hero__media <?php echo $is_swiper ? 'swiper js-hub-swiper' : ''; ?>">
      <div class="<?php echo $is_swiper ? 'swiper-wrapper' : 'front-hub-hero__single'; ?>">
        <?php foreach ($front_slides as $slide) : ?>
          <div class="<?php echo $is_swiper ? 'swiper-slide' : 'front-hub-hero__slide'; ?>">
            <?php if ($slide['video_type'] === 'youtube' && $slide['video_id'] !== '') : ?>
              <lite-youtube videoid="<?php echo esc_attr($slide['video_id']); ?>"></lite-youtube>
            <?php elseif ($slide['video_type'] === 'vimeo' && $slide['video_id'] !== '') : ?>
              <lite-vimeo videoid="<?php echo esc_attr($slide['video_id']); ?>"></lite-vimeo>
            <?php elseif ($slide['video_type'] === 'mp4' && $slide['video_id'] !== '') : ?>
              <?php
              $mp4_url = is_numeric($slide['video_id'])
                ? wp_get_attachment_url((int) $slide['video_id'])
                : $slide['video_id'];
              ?>
              <?php if ($mp4_url) : ?>
                <video autoplay muted loop playsinline preload="metadata">
                  <source src="<?php echo esc_url($mp4_url); ?>" type="video/mp4">
                </video>
              <?php endif; ?>
            <?php elseif (!empty($slide['image_url'])) : ?>
              <img src="<?php echo esc_url($slide['image_url']); ?>" alt="<?php echo esc_attr($slide['title']); ?>">
            <?php else : ?>
              <div class="front-hub-hero__fallback"></div>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>
      <?php if ($is_swiper) : ?>
        <div class="swiper-pagination"></div>
      <?php endif; ?>
    </div>

    <div class="front-hub-hero__overlay"></div>

    <div class="front-hub-hero__inner">
      <div class="front-hub-hero__content">
        <p class="front-hub-hero__kicker"><?php echo esc_html($first_slide['label']); ?></p>
        <h1 class="front-hub-hero__title"><?php echo esc_html($first_slide['title']); ?></h1>
        <p class="front-hub-hero__lead"><?php echo esc_html($first_slide['text']); ?></p>
        <div class="front-hub-hero__actions">
          <a class="front-hub-hero__btn front-hub-hero__btn--white" href="<?php echo esc_url($first_slide['url']); ?>">詳しく見る</a>
          <a class="front-hub-hero__btn front-hub-hero__btn--blue" href="<?php echo esc_url(home_url('/contact/')); ?>">無料相談をする</a>
        </div>
      </div>
    </div>
  </section>
  <?php
  return;
}
/* === NAIGAI FRONT HERO BRANCH END === */

/**
 * =========================================================
 * 受け取りIDの正規化
 * 役割:
 * - 旧 front は post_id を渡す
 * - 新 Hub 構成は page_id を渡す
 * - どちらでも同じ partial が動くようにする
 * =========================================================
 */
$page_id = isset($args['page_id']) ? (int) $args['page_id'] : 0;
if ($page_id <= 0 && isset($args['post_id'])) {
  $page_id = (int) $args['post_id'];
}
if ($page_id <= 0) {
  return;
}

$front_page_id   = (int) get_option('page_on_front');
$is_front_target = ($front_page_id > 0 && $front_page_id === $page_id);

$kicker = function_exists('naigai_hub_get')
  ? (string) naigai_hub_get($page_id, '_hub_kicker', '')
  : '';

$title = function_exists('naigai_hub_get')
  ? (string) naigai_hub_get($page_id, '_hub_title', get_the_title($page_id))
  : (string) get_the_title($page_id);

$lead = function_exists('naigai_hub_get')
  ? (string) naigai_hub_get($page_id, '_hub_lead', '')
  : '';

$noimage_src = get_template_directory_uri() . '/images/noimage.gif';
$slides      = array();

/**
 * =========================================================
 * front hero の基準メタ
 * 役割:
 * - 管理画面の「フロント feature 物件ID」を最優先で使う
 * - 管理画面の「フロント hero YouTube ID」は、
 *   feature スライドの動画として使う
 * =========================================================
 */
$front_feature_post_id = $is_front_target ? (int) get_post_meta($page_id, '_hub_feature_post_id', true) : 0;
$front_hero_video_id   = $is_front_target ? trim((string) get_post_meta($page_id, '_hub_hero_video_id', true)) : '';

/**
 * =========================================================
 * feature 投稿の補助HTMLを組み立てる
 * 役割:
 * - property-info-table.php
 * - Google位置ボタン
 * を feature 投稿から作る
 * =========================================================
 */
$build_front_feature_extra = function ($target_post_id) {
  $extra_html = '';

  $table_template = locate_template(
    array(
      'property-info-table.php',
      'template-parts/property-info-table.php',
      'template-parts/front/property-info-table.php',
      'template-parts/realestate/property-info-table.php',
    ),
    false,
    false
  );

  if ($table_template !== '') {
    $target_post = get_post($target_post_id);

    if ($target_post instanceof WP_Post) {
      global $post;
      $original_post = isset($post) ? $post : null;

      ob_start();

      $property_post_id     = $target_post_id;
      $hub_property_post_id = $target_post_id;

      $post = $target_post;
      setup_postdata($post);

      include $table_template;

      $extra_html .= ob_get_clean();

      $post = $original_post;
      if ($post instanceof WP_Post) {
        setup_postdata($post);
      } else {
    
    /**
     * =========================================================
     * front hero primary slide
     * 役割:
     * - Show in Slider の投稿群から、
     *   feature 投稿を先頭に固定する
     * - 左タイトル / リードは front ページの Hub 文言を使う
     * - 右動画は front hero YouTube ID を最優先に使う
     * =========================================================
     */
    if ($is_front_target && !empty($slides)) {
      $primary_slide = null;
      $other_slides  = array();

      foreach ($slides as $slide) {
        $slide_post_id = !empty($slide['post_id']) ? (int) $slide['post_id'] : 0;

        if ($front_feature_post_id > 0 && $slide_post_id === $front_feature_post_id && $primary_slide === null) {
          $primary_slide = $slide;
          continue;
        }

        $other_slides[] = $slide;
      }

      if ($primary_slide === null) {
        $primary_slide = $slides[0];
        $other_slides  = array_slice($slides, 1);
      }

      if (!empty($primary_slide)) {
        $primary_slide['title'] = $title;
        $primary_slide['lead']  = $lead;

        if ($front_hero_video_id !== '') {
          $primary_slide['type']     = 'youtube';
          $primary_slide['video_id'] = $front_hero_video_id;
        }

        $slides = array_merge(array($primary_slide), $other_slides);
      }
    }

    /**
     * =========================================================
     * front hero fallback
     * 役割:
     * - slider に何も無いときでも
     *   hero YouTube ID があれば最初の hero を成立させる
     * =========================================================
     */
    if ($is_front_target && empty($slides) && $front_hero_video_id !== '') {
      $slides[] = array(
        'type'         => 'youtube',
        'post_id'      => ($front_feature_post_id > 0 ? $front_feature_post_id : $page_id),
        'video_id'     => $front_hero_video_id,
        'src'          => '',
        'alt'          => '',
        'srcset'       => '',
        'sizes'        => '',
        'title'        => $title,
        'lead'         => $lead,
        'permalink'    => ($front_feature_post_id > 0 ? get_permalink($front_feature_post_id) : ''),
        'term_classes' => '',
        'extra_html'   => '',
      );
    }

    wp_reset_postdata();
      }
    }
  }

  $google_embed_code = get_post_meta($target_post_id, 'NewGoogleEmbedcode', true);
  if ($google_embed_code === '') {
    $google_embed_code = get_post_meta($target_post_id, 'GoogleEmbedcode', true);
  }
  if ($google_embed_code === '') {
    $google_embed_code = get_post_meta($target_post_id, '_google_map_iframe_1', true);
  }

  if ($google_embed_code !== '') {
    $google_iframe_html = '';

    if (strpos($google_embed_code, '<iframe') !== false) {
      $google_iframe_html = wp_kses($google_embed_code, array(
        'iframe' => array(
          'src'             => true,
          'width'           => true,
          'height'          => true,
          'style'           => true,
          'allowfullscreen' => true,
          'loading'         => true,
          'referrerpolicy'  => true,
        ),
      ));
    } else {
      $google_iframe_html = '<iframe src="' . esc_url($google_embed_code) . '" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>';
    }

    if ($google_iframe_html !== '') {
      $extra_html .= '<div class="archive-post-location-action hub-front-hero__map-action">';
      $extra_html .= '<button type="button" class="google-location-trigger hub-btn is-secondary" data-map-html="' . esc_attr(base64_encode($google_iframe_html)) . '" data-map-title="' . esc_attr(get_the_title($target_post_id)) . '">';
      $extra_html .= '<svg class="icon-location" aria-hidden="true" focusable="false" width="20" height="20"><use xlink:href="#icon-location"></use></svg>';
      $extra_html .= '<span>Google位置</span>';
      $extra_html .= '</button>';
      $extra_html .= '</div>';
    }
  }

  return $extra_html;
};

/**
 * =========================================================
 * feature 投稿の代表画像を取る
 * =========================================================
 */
$build_front_feature_image = function ($target_post_id) use ($noimage_src) {
  $thumb_id     = get_post_thumbnail_id($target_post_id);
  $image_src    = $thumb_id ? wp_get_attachment_image_url($thumb_id, 'large') : '';
  $image_alt    = $thumb_id ? (string) get_post_meta($thumb_id, '_wp_attachment_image_alt', true) : '';
  $image_srcset = $thumb_id ? wp_get_attachment_image_srcset($thumb_id, 'large') : '';
  $image_sizes  = $thumb_id ? wp_get_attachment_image_sizes($thumb_id, 'large') : '';

  if ($image_src === '') {
    $image_src    = $noimage_src;
    $image_alt    = get_the_title($target_post_id);
    $image_srcset = '';
    $image_sizes  = '';
  }

  return array(
    'src'    => $image_src,
    'alt'    => $image_alt,
    'srcset' => $image_srcset,
    'sizes'  => $image_sizes,
  );
};


if ($is_front_target) {

  /**
   * =========================================================
   * feature スライドを最優先で追加
   * 役割:
   * - front ページの説明どおり、
   *   feature 物件IDを hero の基準にする
   * - 左の総合窓口タイトルは front ページ自身の文言を使い、
   *   物件表 / 地図 / 右側ビジュアルだけを feature 投稿から作る
   * =========================================================
   */
  if ($front_feature_post_id > 0) {
    $feature_type = 'image';
    $feature_video_id = '';
    $feature_image = $build_front_feature_image($front_feature_post_id);

    if ($front_hero_video_id !== '') {
      $feature_type = 'youtube';
      $feature_video_id = $front_hero_video_id;
    } else {
      $raw_feature_type = strtolower(trim((string) get_post_meta($front_feature_post_id, 'page_featured_type', true)));
      $raw_feature_video_id = trim((string) get_post_meta($front_feature_post_id, 'page_video_id', true));

      if (in_array($raw_feature_type, array('youtube', 'vimeo'), true) && $raw_feature_video_id !== '') {
        $feature_type = $raw_feature_type;
        $feature_video_id = $raw_feature_video_id;
      }
    }

    $slides[] = array(
      'type'         => $feature_type,
      'post_id'      => $front_feature_post_id,
      'video_id'     => $feature_video_id,
      'src'          => (string) $feature_image['src'],
      'alt'          => (string) $feature_image['alt'],
      'srcset'       => (string) $feature_image['srcset'],
      'sizes'        => (string) $feature_image['sizes'],
      'title'        => $title,
      'lead'         => $lead,
      'permalink'    => get_permalink($front_feature_post_id),
      'term_classes' => '',
      'extra_html'   => $build_front_feature_extra($front_feature_post_id),
    );
  }

  $slider_query = new WP_Query(array(
    'post_type'           => array('post', 'house', 'recruitment', 'blog', 'minpaku', 'artifacts', 'Artifacts'),
    'post_status'         => 'publish',
    'posts_per_page'      => -1,
    'ignore_sticky_posts' => false,
    'meta_key'            => 'show_in_slider',
    'meta_value'          => 'Yes',
    'orderby'             => 'date',
    'order'               => 'DESC',
  ));

  if ($slider_query->have_posts()) {
    while ($slider_query->have_posts()) {
      $slider_query->the_post();

      $slider_post_id = get_the_ID();

      /**
       * feature 投稿は先頭で追加済みなので、
       * slider query 側では重複させない
       */
      if ($front_feature_post_id > 0 && $slider_post_id === $front_feature_post_id) {
        continue;
      }

      $type = strtolower(trim((string) get_post_meta($slider_post_id, 'page_featured_type', true)));
      if (!in_array($type, array('youtube', 'vimeo'), true)) {
        $type = 'image';
      }

      $video_id = trim((string) get_post_meta($slider_post_id, 'page_video_id', true));

      $slide_title = trim(wp_strip_all_tags(get_the_title($slider_post_id)));

      $slide_lead = get_the_excerpt($slider_post_id);
      if (!is_string($slide_lead) || trim($slide_lead) === '') {
        $slide_lead = wp_trim_words(wp_strip_all_tags((string) get_post_field('post_content', $slider_post_id)), 38, '…');
      }
      $slide_lead = trim(preg_replace('/\s+/u', ' ', wp_strip_all_tags($slide_lead)));

      /**
       * =========================================================
       * front の代表スライドは feature 投稿を優先
       * 役割:
       * - 左タイトル / リードは front ページの Hub 文言を使う
       * - 右動画は front hero YouTube ID を優先する
       * =========================================================
       */
      if ($is_front_target && $front_feature_post_id > 0 && $slider_post_id === $front_feature_post_id) {
        $slide_title = $title;
        $slide_lead  = $lead;

        if ($front_hero_video_id !== '') {
          $type = 'youtube';
          $video_id = $front_hero_video_id;
        }
      }

      $term_classes = '';
      $cats = get_the_category($slider_post_id);
      if (!empty($cats)) {
        foreach ($cats as $cat) {
          if (!empty($cat->term_id)) {
            $term_classes .= ' term-' . sanitize_html_class((string) $cat->term_id);
          }
        }
      }

      $thumb_id     = get_post_thumbnail_id($slider_post_id);
      $image_src    = $thumb_id ? wp_get_attachment_image_url($thumb_id, 'large') : '';
      $image_alt    = $thumb_id ? (string) get_post_meta($thumb_id, '_wp_attachment_image_alt', true) : '';
      $image_srcset = $thumb_id ? wp_get_attachment_image_srcset($thumb_id, 'large') : '';
      $image_sizes  = $thumb_id ? wp_get_attachment_image_sizes($thumb_id, 'large') : '';

      if ($image_src === '') {
        $image_src    = $noimage_src;
        $image_alt    = $slide_title;
        $image_srcset = '';
        $image_sizes  = '';
      }

      $extra_html = '';

      $table_template = locate_template(
        array(
          'property-info-table.php',
          'template-parts/property-info-table.php',
          'template-parts/front/property-info-table.php',
          'template-parts/realestate/property-info-table.php',
        ),
        false,
        false
      );

      if ($table_template !== '') {
        $target_post = get_post($slider_post_id);

        if ($target_post instanceof WP_Post) {
          global $post;
          $original_post = isset($post) ? $post : null;

          ob_start();

          $property_post_id     = $slider_post_id;
          $hub_property_post_id = $slider_post_id;

          $post = $target_post;
          setup_postdata($post);

          include $table_template;

          $extra_html .= ob_get_clean();

          $post = $original_post;
          if ($post instanceof WP_Post) {
            setup_postdata($post);
          } else {
            wp_reset_postdata();
          }
        }
      }

      $google_embed_code = get_post_meta($slider_post_id, 'NewGoogleEmbedcode', true);
      if ($google_embed_code === '') {
        $google_embed_code = get_post_meta($slider_post_id, 'GoogleEmbedcode', true);
      }
      if ($google_embed_code === '') {
        $google_embed_code = get_post_meta($slider_post_id, '_google_map_iframe_1', true);
      }

      if ($google_embed_code !== '') {
        $google_iframe_html = '';

        if (strpos($google_embed_code, '<iframe') !== false) {
          $google_iframe_html = wp_kses($google_embed_code, array(
            'iframe' => array(
              'src'             => true,
              'width'           => true,
              'height'          => true,
              'style'           => true,
              'allowfullscreen' => true,
              'loading'         => true,
              'referrerpolicy'  => true,
            ),
          ));
        } else {
          $google_iframe_html = '<iframe src="' . esc_url($google_embed_code) . '" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>';
        }

        if ($google_iframe_html !== '') {
          $extra_html .= '<div class="archive-post-location-action hub-front-hero__map-action">';
          $extra_html .= '<button type="button" class="google-location-trigger hub-btn is-secondary" data-map-html="' . esc_attr(base64_encode($google_iframe_html)) . '" data-map-title="' . esc_attr(get_the_title($slider_post_id)) . '">';
          $extra_html .= '<svg class="icon-location" aria-hidden="true" focusable="false" width="20" height="20"><use xlink:href="#icon-location"></use></svg>';
          $extra_html .= '<span>Google位置</span>';
          $extra_html .= '</button>';
          $extra_html .= '</div>';
        }
      }

      $slides[] = array(
        'type'         => $type,
        'post_id'      => $slider_post_id,
        'video_id'     => $video_id,
        'src'          => $image_src,
        'alt'          => $image_alt,
        'srcset'       => $image_srcset,
        'sizes'        => $image_sizes,
        'title'        => $slide_title,
        'lead'         => $slide_lead,
        'permalink'    => get_permalink($slider_post_id),
        'term_classes' => $term_classes,
        'extra_html'   => $extra_html,
      );
    }
  }

  wp_reset_postdata();

  /**
   * =========================================================
   * front hero fallback
   * 役割:
   * - show_in_slider 対象投稿が無いときでも
   *   front ページ自身の hero 動画 / hero 画像 を使って表示を成立させる
   * =========================================================
   */
  if (empty($slides)) {
    $front_video_id = function_exists('naigai_hub_get')
      ? (string) naigai_hub_get($page_id, '_hub_hero_video_id', '')
      : '';

    if ($front_video_id !== '') {
      $slides[] = array(
        'type'         => 'youtube',
        'post_id'      => $page_id,
        'video_id'     => $front_video_id,
        'src'          => '',
        'alt'          => '',
        'srcset'       => '',
        'sizes'        => '',
        'title'        => $title,
        'lead'         => $lead,
        'permalink'    => '',
        'term_classes' => '',
        'extra_html'   => '',
      );
    } else {
      $front_images = function_exists('naigai_hub_get_images_from_meta')
        ? naigai_hub_get_images_from_meta($page_id, '_hub_hero_image_ids', 'large')
        : array();

      if (is_array($front_images)) {
        foreach ($front_images as $image) {
          if (empty($image['src'])) {
            continue;
          }

          $slides[] = array(
            'type'         => 'image',
            'post_id'      => $page_id,
            'video_id'     => '',
            'src'          => (string) $image['src'],
            'alt'          => isset($image['alt']) ? (string) $image['alt'] : '',
            'srcset'       => isset($image['srcset']) ? (string) $image['srcset'] : '',
            'sizes'        => isset($image['sizes']) ? (string) $image['sizes'] : '',
            'title'        => $title,
            'lead'         => $lead,
            'permalink'    => '',
            'term_classes' => '',
            'extra_html'   => '',
          );
        }
      }
    }
  }
} else {
  $images = function_exists('naigai_hub_get_images_from_meta')
    ? naigai_hub_get_images_from_meta($page_id, '_hub_hero_image_ids', 'large')
    : array();

  if (is_array($images)) {
    foreach ($images as $image) {
      if (empty($image['src'])) {
        continue;
      }

      $slides[] = array(
        'type'      => 'image',
        'video_id'  => '',
        'src'       => (string) $image['src'],
        'alt'       => isset($image['alt']) ? (string) $image['alt'] : '',
        'srcset'    => isset($image['srcset']) ? (string) $image['srcset'] : '',
        'sizes'     => isset($image['sizes']) ? (string) $image['sizes'] : '',
        'title'     => '',
        'lead'      => '',
        'permalink' => '',
      );
    }
  }
}

$has_media   = !empty($slides);
$slide_count = count($slides);
$first_slide = $has_media ? $slides[0] : null;

$initial_extra_html = ($is_front_target && !empty($first_slide['extra_html']))
  ? $first_slide['extra_html']
  : '';
?>

<section class="hub-section hub-hero-section<?php echo $is_front_target ? ' is-front-hero-redesign' : ''; ?>">
  <div class="hub-section__inner hub-container">
    <div class="hub-hero__grid<?php echo $has_media ? ' has-media' : ''; ?><?php echo $is_front_target ? ' is-front-layout' : ''; ?>">

      <div class="hub-hero__body">

        <?php if ($is_front_target) : ?>
          <div
            class="hub-front-hero js-front-hero-content"
            data-default-title="<?php echo esc_attr($title); ?>"
            data-default-lead="<?php echo esc_attr($lead); ?>">

            <?php if ($kicker !== '') : ?>
              <p class="hub-kicker hub-front-hero__kicker"><?php echo esc_html($kicker); ?></p>
            <?php endif; ?>

            <div class="hub-front-hero__intro">
              <?php if ($title !== '') : ?>
                <h1 class="hub-title hub-front-hero__title js-front-hero-title"><?php echo esc_html($title); ?></h1>
              <?php endif; ?>
            </div>

            <div class="hub-front-hero__facts">
              <div class="hub-front-hero__facts-shell js-front-hero-extra">
                <?php echo $initial_extra_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
                ?>
              </div>
            </div>

            <?php if ($lead !== '') : ?>
              <div class="hub-front-hero__summary">
                <p class="hub-lead hub-front-hero__lead js-front-hero-lead"><?php echo nl2br(esc_html($lead)); ?></p>
              </div>
            <?php else : ?>
              <div class="hub-front-hero__summary" hidden>
                <p class="hub-lead hub-front-hero__lead js-front-hero-lead" hidden></p>
              </div>
            <?php endif; ?>
          </div>

          <?php foreach ($slides as $slide_index => $slide) : ?>
            <template id="front-hero-extra-<?php echo esc_attr((string) $slide_index); ?>">
              <?php echo !empty($slide['extra_html']) ? $slide['extra_html'] : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
              ?>
            </template>
          <?php endforeach; ?>

        <?php else : ?>

          <?php if ($kicker !== '') : ?>
            <p class="hub-kicker"><?php echo esc_html($kicker); ?></p>
          <?php endif; ?>

          <?php if ($title !== '') : ?>
            <h1 class="hub-title"><?php echo esc_html($title); ?></h1>
          <?php endif; ?>

          <?php if ($lead !== '') : ?>
            <p class="hub-lead"><?php echo nl2br(esc_html($lead)); ?></p>
          <?php endif; ?>

        <?php endif; ?>

      </div>

      <?php if ($has_media) : ?>

        <?php if ($slide_count > 1) : ?>
          <div
            class="hub-hero__media hub-hero__media--slider swiper js-hub-swiper<?php echo $is_front_target ? ' hub-front-hero__media-slider' : ''; ?>"
            <?php if ($is_front_target) : ?>data-front-hero="1" <?php endif; ?>>

            <div class="swiper-wrapper">
              <?php foreach ($slides as $slide_index => $slide) : ?>
                <div
                  class="swiper-slide<?php echo !empty($slide['term_classes']) ? esc_attr($slide['term_classes']) : ''; ?>"
                  data-slide-index="<?php echo esc_attr((string) $slide_index); ?>"
                  data-slide-title="<?php echo esc_attr((string) ($slide['title'] ?? '')); ?>"
                  data-slide-lead="<?php echo esc_attr((string) ($slide['lead'] ?? '')); ?>"
                  data-featured-type="<?php echo esc_attr((string) ($slide['type'] ?? 'image')); ?>"
                  data-video-id="<?php echo esc_attr((string) ($slide['video_id'] ?? '')); ?>">

                  <article class="hub-hero__slide-card<?php echo $is_front_target ? ' hub-front-hero__slide-card' : ''; ?>">
                    <div class="hub-hero__slide-media">
                      <?php if ($slide['type'] === 'youtube' && $slide['video_id'] !== '') : ?>
                        <lite-youtube
                          class="hub-hero__lite-youtube"
                          videoid="<?php echo esc_attr($slide['video_id']); ?>"
                          playlabel="<?php echo esc_attr($slide['title']); ?>"
                          style="max-width:100%; height:auto;"></lite-youtube>

                      <?php elseif ($slide['type'] === 'vimeo' && $slide['video_id'] !== '') : ?>
                        <lite-vimeo
                          class="hub-hero__lite-vimeo"
                          videoid="<?php echo esc_attr($slide['video_id']); ?>">
                          <div class="ltv-playbtn"></div>
                        </lite-vimeo>

                      <?php else : ?>
                        <?php if (!empty($slide['permalink'])) : ?>
                          <a class="hub-hero__slide-link" href="<?php echo esc_url($slide['permalink']); ?>">
                          <?php endif; ?>

                          <img
                            class="hub-hero__img"
                            src="<?php echo esc_url($slide['src']); ?>"
                            alt="<?php echo esc_attr($slide['alt']); ?>"
                            <?php if (!empty($slide['srcset'])) : ?>srcset="<?php echo esc_attr($slide['srcset']); ?>" <?php endif; ?>
                            <?php if (!empty($slide['sizes'])) : ?>sizes="<?php echo esc_attr($slide['sizes']); ?>" <?php endif; ?>
                            loading="lazy">

                          <?php if (!empty($slide['permalink'])) : ?>
                          </a>
                        <?php endif; ?>
                      <?php endif; ?>
                    </div>

                    <?php if (!$is_front_target && !empty($slide['title'])) : ?>
                      <div class="hub-hero__slide-info">
                        <?php if (!empty($slide['permalink'])) : ?>
                          <h3 class="hub-hero__slide-title">
                            <a href="<?php echo esc_url($slide['permalink']); ?>">
                              <?php echo esc_html($slide['title']); ?>
                            </a>
                          </h3>
                        <?php else : ?>
                          <h3 class="hub-hero__slide-title"><?php echo esc_html($slide['title']); ?></h3>
                        <?php endif; ?>
                      </div>
                    <?php endif; ?>
                  </article>
                </div>
              <?php endforeach; ?>
            </div>

            <div class="swiper-button-prev hub-front-media__nav hub-front-media__nav--prev" aria-label="前へ"></div>
            <div class="swiper-button-next hub-front-media__nav hub-front-media__nav--next" aria-label="次へ"></div>
            <div class="swiper-pagination"></div>
          </div>

        <?php else : ?>
          <?php $slide = $slides[0]; ?>

          <div
            class="hub-hero__media<?php echo $is_front_target ? ' hub-front-hero__media-slider' : ''; ?>"
            <?php if ($is_front_target) : ?>data-front-hero="1" <?php endif; ?>>

            <article class="hub-hero__slide-card<?php echo $is_front_target ? ' hub-front-hero__slide-card' : ''; ?>">
              <div class="hub-hero__slide-media">
                <?php if ($slide['type'] === 'youtube' && $slide['video_id'] !== '') : ?>
                  <lite-youtube
                    class="hub-hero__lite-youtube"
                    videoid="<?php echo esc_attr($slide['video_id']); ?>"
                    playlabel="<?php echo esc_attr($slide['title']); ?>"
                    style="max-width:100%; height:auto;"></lite-youtube>

                <?php elseif ($slide['type'] === 'vimeo' && $slide['video_id'] !== '') : ?>
                  <lite-vimeo
                    class="hub-hero__lite-vimeo"
                    videoid="<?php echo esc_attr($slide['video_id']); ?>">
                    <div class="ltv-playbtn"></div>
                  </lite-vimeo>

                <?php else : ?>
                  <?php if (!empty($slide['permalink'])) : ?>
                    <a class="hub-hero__slide-link" href="<?php echo esc_url($slide['permalink']); ?>">
                    <?php endif; ?>

                    <img
                      class="hub-hero__img"
                      src="<?php echo esc_url($slide['src']); ?>"
                      alt="<?php echo esc_attr($slide['alt']); ?>"
                      <?php if (!empty($slide['srcset'])) : ?>srcset="<?php echo esc_attr($slide['srcset']); ?>" <?php endif; ?>
                      <?php if (!empty($slide['sizes'])) : ?>sizes="<?php echo esc_attr($slide['sizes']); ?>" <?php endif; ?>
                      loading="lazy">

                    <?php if (!empty($slide['permalink'])) : ?>
                    </a>
                  <?php endif; ?>
                <?php endif; ?>
              </div>

              <?php if (!$is_front_target && !empty($slide['title'])) : ?>
                <div class="hub-hero__slide-info">
                  <?php if (!empty($slide['permalink'])) : ?>
                    <h3 class="hub-hero__slide-title">
                      <a href="<?php echo esc_url($slide['permalink']); ?>">
                        <?php echo esc_html($slide['title']); ?>
                      </a>
                    </h3>
                  <?php else : ?>
                    <h3 class="hub-hero__slide-title"><?php echo esc_html($slide['title']); ?></h3>
                  <?php endif; ?>
                </div>
              <?php endif; ?>
            </article>
          </div>
        <?php endif; ?>

      <?php endif; ?>

    </div>
  </div>
</section>