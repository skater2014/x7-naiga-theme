<?php
/**
 * front-page 用の新着・特集記事パーツ
 * home.php の記事一覧ロジックを流用
 */
if (!defined('ABSPATH')) {
    exit;
}

$raw_sel_cats = function_exists('dess_setting') ? (string) dess_setting('dess_home_cats') : '';
$sel_cats = array_values(array_filter(array_map('trim', explode(',', $raw_sel_cats))));

$blog_args = array(
    'post_type'           => array('post', 'recruitment', 'blog'),
    'posts_per_page'      => -1,
    'paged'               => (get_query_var('paged') ? get_query_var('paged') : 1),
    'ignore_sticky_posts' => true,
    'meta_key'            => 'show_in_homepage',
    'meta_value'          => 'Yes',
);

$blog = new WP_Query($blog_args);
?>

<section class="hub-section hub-post-tabs">
  <div class="hub-section__inner">
    <h2>新着・特集記事</h2>

    <?php if ($blog->have_posts()) : ?>

      <?php if (!empty($sel_cats)) : ?>
        <div class="home-cats-selection">
          <ul>
            <li><a class="term-item active" href="*">All</a></li>
            <?php foreach ($sel_cats as $sel_cat) : ?>
              <li>
                <a class="term-item" href=".term-<?php echo esc_attr($sel_cat); ?>">
                  <?php echo esc_html(get_cat_name((int) $sel_cat)); ?>
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <div class="home-posts">
        <?php while ($blog->have_posts()) : $blog->the_post(); ?>
          <?php get_template_part('templates/post', 'template'); ?>
        <?php endwhile; ?>
      </div>

    <?php else : ?>
      <p>表示する記事がありません。</p>
    <?php endif; ?>

    <?php wp_reset_postdata(); ?>
  </div>
</section>
