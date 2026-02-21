
<? //他の要素　aisde header と重なりswiper動作しなくなるので　paddsing 40px 追加?>
<?php if ( is_page('genshin-impact-characters') || is_page('tekken7-character') || is_page('tekken-gallery') ) : ?>
    <style>

        .home-view {
            padding: 40px;
        }

    </style>

<?php endif; ?>



<div class="swiper">
    <div class="swiper-wrapper">
        <?php
        global $slider_posts;
        $slider_posts = array();

        // カテゴリーをページによって変更
        $category_name = '';
        if (is_page('tekken-gallery') || is_page('tekken7-character')) {
            $category_name = 'tekken7';
        } elseif (is_page('genshin-impact-characters')) {
            $category_name = 'genshin-impact';
        }


        $slider = array(
            'post_type'      => 'post',
            'category_name'  => $category_name,
            //'meta_key'       => 'show_in_slider',
            //'meta_value'     => 'yes',
            'posts_per_page' => 6,
            'orderby'        => 'rand' // 'rand' for random posts, 'date' for latest posts

        );

        $the_query = new WP_Query($slider);

        if ($the_query->have_posts()) :
            while ($the_query->have_posts()) : $the_query->the_post();
                $img_url = get_the_post_thumbnail_url(get_the_ID(), 'full');
        ?>
                <div class="swiper-slide" style="background-image: url('<?php echo $img_url; ?>'); background-size: cover; width: 1280px ;height: 500px;">
                    <div class="slider_overlay"></div>
                    <div class="swiper_slide_text">
                        <h3><?php the_title(); ?></h3>
                        <p><?php echo dess_get_excerpt(120); ?></p>
                        <p class="slide_read_more"><a href="<?php the_permalink(); ?>">READ MORE</a></p>
                    </div>
                </div>
        <?php
                array_push($slider_posts, get_the_ID());
            endwhile;
            wp_reset_postdata();
        endif; // if ($the_query->have_posts())
        ?>
    </div>
        <div class="swiper-pagination"></div>
        <div class="swiper-button-prev"></div>
        <div class="swiper-button-next"></div>
   </div>

<?php //ここに</div> 追加するとwrapperで囲まれなく ?>

<!-- <div class="swiper">
    <div class="swiper-wrapper">
        <?php
        //global $slider_posts;
        //$slider_posts = array();
        //$slider = array(
            //'post_type'      => 'post',
            //'meta_key'       => 'show_in_slider',
            //'meta_value'     => 'yes',
            //'posts_per_page' => 6
        //);
        //$the_query = new WP_Query($slider);
        //if ($the_query->have_posts()) :
            //while ($the_query->have_posts()) : $the_query->the_post();
                //$img_url = get_the_post_thumbnail_url(get_the_ID(), 'full');
        ?>
                <div class="swiper-slide" style="background-image: url('<?php //echo $img_url; ?>');">
                    <div class="slider_overlay"></div>
                    <div class="swiper_slide_text">
                        <h3><?php //the_title(); ?></h3>
                        <p><?php //echo dess_get_excerpt(120); ?></p>
                        <p class="slide_read_more"><a href="<?php //the_permalink(); ?>">READ MORE</a></p>
                    </div>
                </div>
        <?php
                //array_push($slider_posts, get_the_ID());
            //endwhile;
            //wp_reset_postdata();
        //endif; // if ($the_query->have_posts())
        ?>
    </div>
    <div class="swiper-pagination"></div>
    <div class="swiper-button-prev"></div>
    <div class="swiper-button-next"></div>
</div> -->
