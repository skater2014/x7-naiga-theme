<div class="popular-post">
    <div class="side-title">レビュー投稿</div>
    <?php
    $args = array(
        'posts_per_page' => 2,
        'orderby'        => 'comment_count',
    );
    $my_query = new WP_Query($args);
    if ($my_query->have_posts()) :
    ?>
        <div class="popular-post-slider">
            <!-- Add the slick slider container -->
            <ul class="sidebar-posts sidebar-recent-posts popular-slick-slider slick-slider">
                <?php while ($my_query->have_posts()) : $my_query->the_post(); ?>
                    <li class="sidebar-post-item">
                        <div id="post-<?php the_ID(); ?>" <?php post_class('custom-post'); ?>>
                            
                            <div class="sidebar-recent-posts-title">
                                <h2>
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h2>
                            </div>
                            <div class="sidebar-thumbnail-box">
                                <a href="<?php the_permalink(); ?>">

                                    <!-- NEWマークの表示 -->
                                    <?php
                                        $days = 14;  // NEWマークを表示する日数
                                        $now = time();  // 現在のUNIXタイムスタンプ
                                        $entry = get_the_time('U');  // 投稿日時のUNIXタイムスタンプ
                                        $term = ($now - $entry) / 86400;  // 投稿からの経過日数
                                        if ($days > $term) {
                                            echo '<div class="box new-post">';  // "new-post"クラスを追加
                                            echo '<div class="ribbon ribbon-top-left">';
                                            echo '<span>New</span>';
                                            echo '</div>';
                                            echo '</div>';
                                        }
                                    ?>
                                    
                                    <div class="post-thumbnail" style="height: 200px; overflow: hidden;">
                                        <?php
                                        $thumbnail_id = get_post_thumbnail_id();
                                        $thumbnail_url = wp_get_attachment_image_src($thumbnail_id, 'medium')[0] ?? get_template_directory_uri() . '/images/noimage.gif';
                                        $high_res_url = wp_get_attachment_image_src($thumbnail_id, 'full')[0] ?? '';
                                        ?>
                                        <img src="<?php echo esc_url($thumbnail_url); ?>" data-src="<?php echo esc_url($high_res_url); ?>" alt="<?php the_title(); ?>" class="lazyload uniform-thumbnail" style="width: 100%; height: 100%; object-fit: cover;" />
                                    </div>

                                </a>

                                <div class="heart-icon" data-post-id="<?php echo get_the_ID(); ?>">
                                    <svg class="icon icon-heart <?php echo get_post_meta(get_the_ID(), 'liked', true) ? 'liked' : ''; ?>">
                                        <use xlink:href="#icon-heart"></use>
                                    </svg>
                                </div>
                            </div>
                            <p class="post-meta">
                                <span class="post-date"><?php the_time(get_option('date_format')); ?></span>
                                <span class="sidebar-comment-num">
                                    <?php comments_popup_link('<i class="far fa-comments"></i> : 0', '<i class="far fa-comments"></i> : 1', '<i class="far fa-comments"></i> : %'); ?>
                                </span>
                            </p>
                            <p class="custom-excerpt">
                                <?php
                                    // 投稿の最初の 30 単語を取得（HTMLタグを除去）
                                    $excerpt = wp_trim_words(get_the_content(), 30);  // 30 単語に制限
                                    echo esc_html($excerpt);  // HTMLタグを除去して表示
                                ?>
                            </p>

                            <div class="more-link-wrapper">
                                <a href="<?php the_permalink(); ?>" title="Read more" class="more-link">この物件の詳細を見る &raquo;</a>
                            </div>

                        </div>
                    </li>
                <?php endwhile; ?>
            </ul>
            <!-- Slider buttons outside the <ul> -->
            <div class="slider-buttons">
                <button class="popular-slider-prev">&lt;</button>
                <button class="popular-slider-next">&gt;</button>
            </div>
        </div>
    <?php endif; ?>
</div>
