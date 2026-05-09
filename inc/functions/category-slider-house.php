<?php

//get_header('home'); //Slick navi
get_header('77'); //Regular Menu Bootstrap navi

$slide_ids = array();

$slider = array(
    'post_type' => 'house',
    'meta_key' => 'show_in_slider',
    'meta_value' => 'yes',
    'posts_per_page' => -1,
    'ignore_sticky_posts' => true
);

$the_query = new WP_Query($slider);

if ($the_query->have_posts()) :

?>

    <div class="home-slider invisibility">

        <div class="article">

            <div id="home-slider">

                <?php

                while ($the_query->have_posts()) : $the_query->the_post();

                    array_push($slide_ids, $post->ID);

                    get_template_part('slide', 'template');

                endwhile;

                ?>

            </div><!-- end home-slider -->

        </div> <!--end article -->

    </div><!--end home-slider invisibility -->

<?php endif; ?>

<?php
//get_footer('home1');
?>
