<?php
if (!defined('ABSPATH')) exit;

$info = new WP_Query(array(
  'post_type' => array('post', 'blog'),
  'post_status' => 'publish',
  'posts_per_page' => 6,
  'ignore_sticky_posts' => true,
  'orderby' => 'date',
  'order' => 'DESC',
));
?>

<section class="front-v2-info">
  <div class="front-v2-shell">
    <div class="front-v2-section-head">
      <div>
        <p class="front-v2-kicker">NEWS / EVENT</p>
        <h2>イベント・告知・お知らせ</h2>
      </div>
      <a class="front-v2-list-link" href="<?php echo esc_url(home_url('/blog/')); ?>">お知らせ一覧</a>
    </div>

    <div class="front-v2-info__grid">
      <?php if ($info->have_posts()) : ?>
        <?php while ($info->have_posts()) : $info->the_post(); ?>
          <a class="front-v2-info-card" href="<?php the_permalink(); ?>">
            <time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date('Y.m.d')); ?></time>
            <h3><?php the_title(); ?></h3>
            <p><?php echo esc_html(wp_trim_words(get_the_excerpt(), 32, '…')); ?></p>
          </a>
        <?php endwhile; wp_reset_postdata(); ?>
      <?php endif; ?>
    </div>
  </div>
</section>
