<?php
/**
 * Front home-box
 *
 * 目的:
 * - 既存JSが拾う旧クラス home-cats-selection / home-posts / archive-posts / archive-post-box を維持
 * - カスタマイザーで選択したカテゴリーをできる限り自動取得
 * - 各カードに term-{ID} クラスを付け、カテゴリー別タブ切り替えを動かす
 * - 価格 / 土地面積 / 建物面積 をカード内に表示
 */

if (!defined('ABSPATH')) {
  exit;
}

/**
 * 候補メタキーから最初に値が入っているものを返す
 */
if (!function_exists('naigai_front_homebox_first_meta')) {
  function naigai_front_homebox_first_meta($post_id, array $keys, $default = '') {
    foreach ($keys as $key) {
      $value = get_post_meta($post_id, $key, true);
      if ($value !== '' && $value !== null && $value !== array()) {
        return is_array($value) ? implode(', ', array_filter($value)) : $value;
      }
    }
    return $default;
  }
}

/**
 * 数値っぽい価格を表示用に整える
 */
if (!function_exists('naigai_front_homebox_format_price')) {
  function naigai_front_homebox_format_price($value) {
    $value = trim((string) $value);
    if ($value === '') {
      return '要確認';
    }

    if (preg_match('/円|万円|税込|相談|未定|要確認/u', $value)) {
      return $value;
    }

    $num = preg_replace('/[^\d.]/', '', $value);
    if ($num !== '' && is_numeric($num)) {
      return number_format((float) $num) . '万円';
    }

    return $value;
  }
}

/**
 * 面積の単位を補う
 */
if (!function_exists('naigai_front_homebox_format_area')) {
  function naigai_front_homebox_format_area($value) {
    $value = trim((string) $value);
    if ($value === '') {
      return '—';
    }

    if (preg_match('/㎡|m2|坪/u', $value)) {
      return $value;
    }

    return $value . '㎡';
  }
}

/**
 * カスタマイザー上の home/category 系 theme_mod を広めに拾う
 */
$home_cat_ids = array();
$mods = get_theme_mods();

if (is_array($mods)) {
  foreach ($mods as $key => $value) {
    $key_lc = strtolower((string) $key);

    if (
      strpos($key_lc, 'home') !== false &&
      (
        strpos($key_lc, 'cat') !== false ||
        strpos($key_lc, 'category') !== false
      )
    ) {
      $values = is_array($value) ? $value : preg_split('/[,|]/', (string) $value);

      foreach ($values as $v) {
        $v = trim((string) $v);
        if ($v !== '' && ctype_digit($v)) {
          $home_cat_ids[] = (int) $v;
        }
      }
    }
  }
}

$home_cat_ids = array_values(array_unique(array_filter($home_cat_ids)));

/**
 * 何も拾えない場合は、投稿数のあるカテゴリーをフォールバック表示
 */
if (empty($home_cat_ids)) {
  $fallback_cats = get_categories(array(
    'hide_empty' => true,
    'number'     => 6,
    'orderby'    => 'count',
    'order'      => 'DESC',
  ));

  foreach ($fallback_cats as $cat) {
    $home_cat_ids[] = (int) $cat->term_id;
  }
}

$home_cats = array();
foreach ($home_cat_ids as $cat_id) {
  $cat = get_category($cat_id);
  if ($cat && !is_wp_error($cat)) {
    $home_cats[] = $cat;
  }
}

/**
 * 公開post_typeだけを対象にする。
 * 存在しないpost_typeを避ける。
 */
$public_post_types = get_post_types(array('public' => true), 'names');
$wanted_post_types = array('post', 'house', 'land', 'realestate', 'estate', 'property', 'rent', 'minpaku', 'blog');
$post_types = array_values(array_intersect($wanted_post_types, $public_post_types));

if (empty($post_types)) {
  $post_types = array('post');
}

$query_args = array(
  'post_type'           => $post_types,
  'post_status'         => 'publish',
  'posts_per_page'      => 12,
  'ignore_sticky_posts' => true,
);

if (!empty($home_cat_ids)) {
  $query_args['category__in'] = $home_cat_ids;
}

$home_query = new WP_Query($query_args);

if (!$home_query->have_posts()) {
  return;
}
?>

<section class="front-home-box" id="front-home-box" aria-labelledby="front-home-box-title">
  <div class="front-home-box__inner">
    <div class="front-home-box__head">
      <p class="front-home-box__kicker">Nasu Life Search</p>
      <h2 class="front-home-box__title" id="front-home-box-title">那須での暮らしを探す</h2>
      <p class="front-home-box__lead">
        土地・建物・賃貸・家づくりを、目的別に探せます。
      </p>
    </div>

    <?php if (!empty($home_cats)) : ?>
      <nav class="home-cats-selection front-home-box__tabs" aria-label="カテゴリー別に表示">
        <ul>
          <li>
            <a href="#front-home-box" class="active" data-filter="all" data-term="all" data-cat-id="all">
              すべて
            </a>
          </li>

          <?php foreach ($home_cats as $cat) : ?>
            <li>
              <a
                href="#front-home-box"
                data-filter=".term-<?php echo esc_attr($cat->term_id); ?>"
                data-term="term-<?php echo esc_attr($cat->term_id); ?>"
                data-cat-id="<?php echo esc_attr($cat->term_id); ?>"
              >
                <?php echo esc_html($cat->name); ?>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      </nav>
    <?php endif; ?>

    <div class="home-posts front-home-box__posts">
      <div class="archive-posts front-home-box__grid">
        <?php
        while ($home_query->have_posts()) :
          $home_query->the_post();

          $post_id = get_the_ID();

          $cats = get_the_category($post_id);
          $term_class = '';
          if (!empty($cats)) {
            foreach ($cats as $cat) {
              $term_class .= ' term-' . (int) $cat->term_id;
            }
          }

          $price = naigai_front_homebox_first_meta($post_id, array(
            'price',
            '_price',
            'property_price',
            '_property_price',
            'land_price',
            '_land_price',
            'house_price',
            '_house_price',
            'sale_price',
            '_sale_price',
            'kakaku',
            '_kakaku',
          ));

          $land_area = naigai_front_homebox_first_meta($post_id, array(
            'land_area',
            '_land_area',
            'property_land_area',
            '_property_land_area',
            'tochi_area',
            '_tochi_area',
            'site_area',
            '_site_area',
          ));

          $building_area = naigai_front_homebox_first_meta($post_id, array(
            'building_area',
            '_building_area',
            'property_building_area',
            '_property_building_area',
            'tatemono_area',
            '_tatemono_area',
            'floor_area',
            '_floor_area',
          ));

          $feature_type = get_post_meta($post_id, 'page_featured_type', true);
          $video_id     = get_post_meta($post_id, 'page_video_id', true);
          ?>
          <article class="archive-post-box front-home-card<?php echo esc_attr($term_class); ?>" data-terms="<?php echo esc_attr(trim($term_class)); ?>">
            <a class="front-home-card__link" href="<?php the_permalink(); ?>">
              <div class="archive-post-feature front-home-card__media">
                <?php if ($feature_type === 'youtube' && $video_id) : ?>
                  <lite-youtube videoid="<?php echo esc_attr($video_id); ?>"></lite-youtube>
                <?php elseif ($feature_type === 'vimeo' && $video_id) : ?>
                  <iframe
                    src="https://player.vimeo.com/video/<?php echo esc_attr($video_id); ?>"
                    loading="lazy"
                    allow="autoplay; fullscreen; picture-in-picture"
                    allowfullscreen
                  ></iframe>
                <?php elseif (has_post_thumbnail($post_id)) : ?>
                  <?php echo get_the_post_thumbnail($post_id, 'large', array('class' => 'front-home-card__image')); ?>
                <?php else : ?>
                  <div class="front-home-card__noimage">No Image</div>
                <?php endif; ?>

                <?php if (!empty($cats)) : ?>
                  <span class="front-home-card__badge">
                    <?php echo esc_html($cats[0]->name); ?>
                  </span>
                <?php endif; ?>
              </div>

              <div class="front-home-card__body">
                <h3 class="front-home-card__title"><?php the_title(); ?></h3>

                <dl class="front-home-card__meta">
                  <div>
                    <dt>価格</dt>
                    <dd><?php echo esc_html(naigai_front_homebox_format_price($price)); ?></dd>
                  </div>
                  <div>
                    <dt>土地面積</dt>
                    <dd><?php echo esc_html(naigai_front_homebox_format_area($land_area)); ?></dd>
                  </div>
                  <div>
                    <dt>建物面積</dt>
                    <dd><?php echo esc_html(naigai_front_homebox_format_area($building_area)); ?></dd>
                  </div>
                </dl>

                <span class="front-home-card__more">詳しく見る</span>
              </div>
            </a>
          </article>
        <?php endwhile; ?>
      </div>
    </div>
  </div>
</section>

<script>
(function () {
  function ready(fn) {
    if (document.readyState !== 'loading') fn();
    else document.addEventListener('DOMContentLoaded', fn);
  }

  ready(function () {
    var root = document.getElementById('front-home-box');
    if (!root) return;

    var tabs = root.querySelectorAll('.home-cats-selection a');
    var cards = root.querySelectorAll('.archive-post-box');

    if (!tabs.length || !cards.length) return;

    tabs.forEach(function (tab) {
      tab.addEventListener('click', function (event) {
        event.preventDefault();

        tabs.forEach(function (t) {
          t.classList.remove('active');
          t.setAttribute('aria-selected', 'false');
        });

        tab.classList.add('active');
        tab.setAttribute('aria-selected', 'true');

        var term = tab.getAttribute('data-term') || 'all';
        var filter = tab.getAttribute('data-filter') || 'all';

        cards.forEach(function (card) {
          var show = false;

          if (term === 'all' || filter === 'all') {
            show = true;
          } else if (term && card.classList.contains(term)) {
            show = true;
          } else if (filter && filter.charAt(0) === '.' && card.matches(filter)) {
            show = true;
          }

          card.hidden = !show;
          card.classList.toggle('is-hidden', !show);
        });
      });
    });
  });
})();
</script>

<?php
wp_reset_postdata();
