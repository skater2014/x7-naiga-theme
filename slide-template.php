<?php
$type = get_post_meta(get_the_ID(), 'page_featured_type', true);
$is_video = in_array($type, array('youtube', 'vimeo'), true);
?>

<div class="swiper-slide">
    <div class="home-slide">

        <div class="home-slide-media-column">
            <div class="home-slide-feature<?php echo $is_video ? ' has-video' : ''; ?>">

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

                <div class="home-slide-overlay">
                    <div class="home-slide-overlay__panel">
                        <?php if ($is_video) : ?>
                            <button type="button" class="home-slide-overlay__close" aria-label="オーバーレイを閉じる">×</button>
                        <?php endif; ?>

                        <h3 class="home-slide-overlay__title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h3>

                        <div class="home-slide-overlay__text">
                            <?php echo wp_kses_post(dess_get_excerpt(60)); ?>
                        </div>
                    </div>
                </div>

            </div><!-- /.home-slide-feature -->
        </div><!-- /.home-slide-media-column -->

        <div class="home-slide-info">
            <?php
            if (locate_template('property-info-table.php')) {
                get_template_part('property-info-table');
            } else {
                echo '<p>物件情報がありません</p>';
            }
            ?>

            <div class="home-slide-actions">
                <a class="home-slide-link" href="<?php the_permalink(); ?>">物件詳細</a>

                <button
                    type="button"
                    class="home-slide-cta naigai-property-row__cta"
                    data-post-id="<?php echo (int) get_the_ID(); ?>"
                    data-permalink="<?php echo esc_url(get_permalink()); ?>">
                    来店予約
                </button>
            </div>
        </div><!-- /.home-slide-info -->

    </div><!-- /.home-slide -->
</div><!-- /.swiper-slide -->