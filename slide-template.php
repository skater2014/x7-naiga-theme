<div class="home-slide">
    <div class="home-slide-feature">
        <?php
        $type = get_post_meta($post->ID, 'page_featured_type', true);

        switch ($type) {
            // case 'youtube-iframe':
            // echo '<iframe width="560" height="315" src="https://www.youtube.com/embed/'.get_post_meta( get_the_ID(), 'page_video_id', true ).'?wmode=transparent" frameborder="0" allowfullscreen></iframe>';
            
            case 'youtube':
                echo '<lite-youtube videoid="' . get_post_meta(get_the_ID(), 'page_video_id', true) . '" playlabel="Play: Keynote (Google I/O \'18)" style="max-width:100% height="517px; height:auto;"></lite-youtube>';
                break;

            case 'vimeo':
                // echo '<iframe src="https://player.vimeo.com/video/'.get_post_meta( get_the_ID(), 'page_video_id', true ).'?title=0&amp;byline=0&amp;portrait=0&amp;color=03b3fc" width="500" height="338" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
                echo '<lite-vimeo videoid="' . get_post_meta(get_the_ID(), 'page_video_id', true) . '">
                    <div class="ltv-playbtn"></div>
                </lite-vimeo>';
                break;

            default:
                echo '<div class="home-slide-image">';
                echo '<a href="'.get_permalink().'">';
                the_post_thumbnail('large');
                echo '</a>';
                echo '</div>';
                break;
        }
        ?>
    </div><!-- home-slide-feature -->

    <div class="home-slide-info">
        <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>

        <div class="home-slide-text">
            <?php echo dess_get_excerpt(60); ?>
            <?php 
            // 物件情報テーブルを表示
            if (file_exists(get_template_directory() . '/property-info-table.php')) {
                get_template_part('property-info-table'); // 物件情報テーブルの読み込み
            } else {
                echo '<p>物件情報がありません</p>';
            }
            ?>
        </div><!-- home-slide-text -->
    </div><!-- home-slide-info -->
</div><!-- home-slide -->
