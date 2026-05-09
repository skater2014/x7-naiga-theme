<?php
if (!defined('ABSPATH')) exit;

$selected_cat_ids = front_v2_selected_category_ids();

$terms = !empty($selected_cat_ids)
  ? get_categories(array('include' => $selected_cat_ids, 'hide_empty' => false, 'orderby' => 'include'))
  : get_categories(array('hide_empty' => true, 'orderby' => 'name', 'order' => 'ASC'));

$query_args = array(
  'post_type' => array('post', 'house', 'blog', 'minpaku', 'recruitment'),
  'post_status' => 'publish',
  'posts_per_page' => -1,
  'ignore_sticky_posts' => true,
  'orderby' => 'date',
  'order' => 'DESC',
);

if (!empty($selected_cat_ids)) {
  $query_args['category__in'] = $selected_cat_ids;
}

$q = new WP_Query($query_args);
?>

<section class="front-v2-posts">
  <div class="front-v2-shell">
    <div class="front-v2-section-head">
      <div>
        <p class="front-v2-kicker">PICK UP</p>
        <h2>那須での暮らしを探す</h2>
      </div>
      <a class="front-v2-list-link" href="<?php echo esc_url(home_url('/fudousan/')); ?>">一覧を見る</a>
    </div>

    <?php if (!empty($terms)) : ?>
      <nav class="front-v2-tabs" aria-label="カテゴリー切り替え">
        <ul id="front_v2_cats">
          <li><a href="*" class="active">ALL</a></li>
          <?php foreach ($terms as $term) : ?>
            <li><a href=".term-<?php echo esc_attr($term->term_id); ?>"><?php echo esc_html($term->name); ?></a></li>
          <?php endforeach; ?>
        </ul>
      </nav>
    <?php endif; ?>

    <div class="front-v2-grid" data-front-v2-grid>
      <?php if ($q->have_posts()) : ?>
        <?php while ($q->have_posts()) : $q->the_post(); ?>
          <?php
          $pid = get_the_ID();
          $cats = get_the_category($pid);
          $term_classes = '';

          foreach ((array) $cats as $cat) {
            $term_classes .= ' term-' . (int) $cat->term_id;
          }

          $meta = front_v2_property_meta($pid);
          ?>

          <article class="front-v2-card<?php echo esc_attr($term_classes); ?>">
            <a class="front-v2-card__image" href="<?php the_permalink(); ?>">
              <?php echo front_v2_post_media($pid, 'large'); ?>
            </a>

            <div class="front-v2-card__body">
              <?php if (!empty($cats)) : ?>
                <div class="front-v2-card__badges">
                  <?php foreach (array_slice($cats, 0, 2) as $cat) : ?>
                    <span><?php echo esc_html($cat->name); ?></span>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>

              <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>

              <?php if ($meta['price'] || $meta['land'] || $meta['building'] || $meta['address']) : ?>
                <dl class="front-v2-card__meta">
                  <?php if ($meta['price']) : ?><div><dt>価格</dt><dd><?php echo esc_html($meta['price']); ?></dd></div><?php endif; ?>
                  <?php if ($meta['land']) : ?><div><dt>土地面積</dt><dd><?php echo esc_html($meta['land']); ?></dd></div><?php endif; ?>
                  <?php if ($meta['building']) : ?><div><dt>建物面積</dt><dd><?php echo esc_html($meta['building']); ?></dd></div><?php endif; ?>
                  <?php if ($meta['address']) : ?><div><dt>所在地</dt><dd><?php echo esc_html($meta['address']); ?></dd></div><?php endif; ?>
                </dl>
              <?php endif; ?>

              <p><?php echo esc_html(wp_trim_words(get_the_excerpt(), 38, '…')); ?></p>

              <div class="more-link-wrapper">
                <a href="<?php the_permalink(); ?>" class="more-link">詳細を見る</a>
                <a href="#store-reservation" class="store-reserve-link" data-post-id="<?php echo esc_attr($pid); ?>">来店予約</a>
              </div>
            </div>
          </article>
        <?php endwhile; wp_reset_postdata(); ?>
      <?php else : ?>
        <p class="front-v2-empty">表示できる記事・物件がありません。</p>
      <?php endif; ?>
    </div>
  </div>
</section>
