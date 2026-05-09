<?php

/****************************************

  Template Name: Home.php

 *****************************************/
?>
<?php

get_header('77');

$home_excerpt = get_the_excerpt(get_the_ID());
?>

<div class="home-box">

  <div class="article">

    <?php if (!empty($home_excerpt)) : ?>
      <div class="home-intro">
        <?php echo wpautop($home_excerpt); ?>

        <!-- ========================================================
             ホーム本文内の内部リンク
             目的:
             - AIOSEO が本文中の内部リンクとして認識しやすい形にする
             - 見た目が不自然な「リンクだけの行」をやめて、
               説明文の続きとして自然に見せる
        ========================================================= -->
        <p class="home-intro-links">
          <a href="<?php echo esc_url(home_url('/naigai-tochi')); ?>">那須の土地情報</a>や
          <a href="<?php echo esc_url(home_url('/nasu-jutaku')); ?>">新築・建売住宅情報</a>、
          <a href="<?php echo esc_url(home_url('/fudosan-column/')); ?>">不動産コラム</a>
          もあわせてご覧いただけます。
        </p>
      </div>
    <?php endif; ?>

    <?php

    $sel_cats = explode(',', dess_setting('dess_home_cats'));

    $blog_args = array(

      'post_type' => array('post', 'recruitment', 'blog'),

      'posts_per_page' => -1,

      'paged' => (get_query_var('paged') ? get_query_var('paged') : 1),

      'ignore_sticky_posts' => true,

      'meta_key'   => 'show_in_homepage',
      'meta_value' => 'Yes',

    );

    $blog = new WP_Query($blog_args);

    if ($blog->have_posts()) :

    ?>

      <div class="home-cats-selection">

        <ul>
          <li><a class="term-item active" href="*">All</a></li>

          <?php
          foreach ($sel_cats as $sel_cat) {
          ?>
            <li><a class="term-item" href=".term-<?php echo $sel_cat; ?>"><?php echo get_cat_name($sel_cat); ?></a></li>
          <?php } ?>
        </ul>

      </div><!-- home-cats-selection -->

      <div class="home-posts">

        <?php

        while ($blog->have_posts()) : $blog->the_post();

          get_template_part('templates/post', 'template');

        endwhile;

        ?>

      </div><!-- home-posts -->

    <?php endif; ?>

    <?php wp_reset_postdata(); ?>

  </div><!--end article -->

</div><!-- home-box -->


<?php
get_footer('home1');
?>