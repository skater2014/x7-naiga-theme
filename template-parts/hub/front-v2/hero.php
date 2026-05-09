<?php
if (!defined('ABSPATH')) exit;

$hero_query = new WP_Query(array(
  'post_type' => array('post', 'house', 'blog', 'minpaku', 'recruitment'),
  'post_status' => 'publish',
  'posts_per_page' => 1,
  'ignore_sticky_posts' => true,
  'meta_query' => array(
    array(
      'key' => 'show_in_slider',
      'value' => 'Yes',
      'compare' => '=',
    ),
  ),
));

$hero_post = $hero_query->have_posts() ? $hero_query->posts[0] : null;
?>

<main id="front-v2" class="front-v2">
  <section class="front-v2-hero<?php echo $hero_post ? '' : ' is-no-media'; ?>">
    <?php if ($hero_post) : ?>
      <div class="front-v2-hero__media">
        <?php echo front_v2_post_media($hero_post->ID, 'full'); ?>
      </div>
    <?php endif; ?>

    <div class="front-v2-hero__overlay"></div>

    <div class="front-v2-shell front-v2-hero__content">
      <p class="front-v2-kicker">NAIGAI CORPORATION</p>
      <h1>那須の暮らし・不動産・家づくりの総合窓口</h1>
      <p>土地・建物・注文住宅・リフォーム・民泊活用まで、目的に合わせて相談できる入口です。</p>

      <div class="front-v2-hero__buttons">
        <a href="<?php echo esc_url(home_url('/fudousan/')); ?>">不動産を探す</a>
        <a href="<?php echo esc_url(home_url('/iezukuri/')); ?>">家づくりを見る</a>
        <a href="<?php echo esc_url(home_url('/contact/')); ?>">相談する</a>
      </div>
    </div>
  </section>
