<?php
// get_header('home'); // 旧Slick用
get_header('77'); // Regular Menu

$slide_ids = array();

$slider = array(
  'post_type'           => 'post',
  'meta_key'            => 'show_in_slider',
  'meta_value'          => 'yes',
  'posts_per_page'      => -1,
  'ignore_sticky_posts' => true,
);

$the_query = new WP_Query($slider);

if ($the_query->have_posts()) :
?>
  <div class="home-slider swiper-home invisibility">
    <div class="article">
      <div class="swiper home-swiper" id="home-swiper">
        <div class="swiper-wrapper">
          <?php
          while ($the_query->have_posts()) :
            $the_query->the_post();
            $slide_ids[] = get_the_ID();
            get_template_part('slide', 'template');
          endwhile;
          ?>
        </div>

        <div class="swiper-button-prev" aria-label="前へ"></div>
        <div class="swiper-button-next" aria-label="次へ"></div>
        <div class="swiper-pagination"></div>
      </div>
    </div>
  </div>
<?php
endif;

wp_reset_postdata();

// get_footer('home1');
?>