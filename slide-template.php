<?php
$type = get_post_meta(get_the_ID(), 'page_featured_type', true);
?>

<div class="swiper-slide">
    <div class="home-slide">
        <div class="home-slide-feature">
            <?php
            switch ($type) {
                case 'youtube':
                    $video_id = get_post_meta(get_the_ID(), 'page_video_id', true);
                    if (!empty($video_id)) {
                        echo '<lite-youtube videoid="' . esc_attr($video_id) . '" playlabel="' . esc_attr(get_the_title()) . '"></lite-youtube>';
                    }
                    break;

                case 'vimeo':
                    $video_id = get_post_meta(get_the_ID(), 'page_video_id', true);
                    if (!empty($video_id)) {
                        echo '<lite-vimeo videoid="' . esc_attr($video_id) . '"><div class="ltv-playbtn"></div></lite-vimeo>';
                    }
                    break;

                default:
                    echo '<div class="home-slide-image">';
                    echo '<a href="' . esc_url(get_permalink()) . '">';
                    if (has_post_thumbnail()) {
                        the_post_thumbnail('large');
                    } else {
                        echo '<img src="' . esc_url(get_template_directory_uri() . '/images/no-image.jpg') . '" alt="' . esc_attr(get_the_title()) . '">';
                    }
                    echo '</a>';
                    echo '</div>';
                    break;
            }
            ?>
        </div><!-- /.home-slide-feature -->

        <div class="home-slide-info">
            <h3>
                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
            </h3>

            <div class="home-slide-text">
                <?php echo wp_kses_post(dess_get_excerpt(60)); ?>
            </div>

            <?php
            if (locate_template('property-info-table.php')) {
                get_template_part('property-info-table');
            } else {
                echo '<p>物件情報がありません</p>';
            }
            ?>
        </div><!-- /.home-slide-info -->
    </div><!-- /.home-slide -->
</div><!-- /.swiper-slide -->