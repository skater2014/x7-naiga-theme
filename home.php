<?php
/****************************************

  Template Name: Home.php


*****************************************/
?>
<?php

get_header('77'); 
?>


<div class="home-box">

  <!--<div class="container">-->
      <div class="article">


<?php

  $sel_cats = explode(',', dess_setting('dess_home_cats') );

  $blog_args = array(

          'post_type' => array('post', 'recruitment', 'blog'),

          'posts_per_page' => -1,

          'paged' => ( get_query_var('paged') ? get_query_var('paged') : 1),

          'ignore_sticky_posts' => true,

          'meta_key'   => 'show_in_homepage', // メタボックス

          'meta_value' => 'Yes', // メタボックス この行を追加して 'Yes' の投稿のみを取得


          //'category__in' => $sel_cats,

          //'post__not_in' => $slide_ids

        );

  

  $blog = new WP_Query( $blog_args );

  if ( $blog->have_posts() ) :

?>

      <div class="home-cats-selection">

        <ul>
          <!-- "All" カテゴリー -->
          <li><a class="term-item active" href="*">All</a></li>

          <?php
              // 選択されたカテゴリーをループして処理
              foreach ($sel_cats as $sel_cat) {
          ?>
          <!-- 各カテゴリー -->
          <li><a class="term-item" href=".term-<?php echo $sel_cat; ?>"><?php echo get_cat_name($sel_cat); ?></a></li>
          <?php } ?>
      </ul>


      </div><!-- home-cats-selection -->

      <div class="home-posts">

        <?php

          while ( $blog->have_posts() ) : $blog->the_post();

            //get_template_part( 'post', 'template' );

            get_template_part( 'templates/post', 'template' );


          endwhile;

        ?>

      </div><!-- home-posts -->

<?php endif; ?>

  </div><!--end article -->

</div><!-- home-box -->


<?php
get_footer('home1');

?>