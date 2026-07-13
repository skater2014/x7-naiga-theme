<?php

/**
 * archive-blog.php
 * カスタム投稿タイプ "blog" のアーカイブページテンプレート
 */
get_header('77');
?>

<?php display_view_toggle_buttons(); ?>

<div id="main"> <!-- /#main -->

    <?php
    $args = array(
        'post_type' => 'blog',
        'posts_per_page' => 10,
        'paged' => get_query_var('paged') ? get_query_var('paged') : 1,
    );
    $custom_query = new WP_Query($args);
    ?>

    <?php if ($custom_query->have_posts()) : ?>
        <?php while ($custom_query->have_posts()) : $custom_query->the_post(); ?>
            <div id="post-<?php the_ID(); ?>" <?php post_class('main-custom-post'); ?>> <!-- /.main-custom-post -->

                <div class="post-meta"> <!-- /.post-meta -->
                    <a href="<?php the_permalink(); ?>">
                        <h2><?php the_title(); ?></h2>
                    </a>

                    <p class="post-meta-row">
                        <svg class="icon icon-price-tags">
                            <use xlink:href="#icon-price-tags"></use>
                        </svg>

                        <span class="category">
                            <?php
                            $parts = array();

                            // カテゴリー（先頭1件だけ）
                            $categories = get_the_category();
                            if (!empty($categories)) {
                                $first_cat = $categories[0];
                                $parts[] = '<a href="' . esc_url(get_category_link($first_cat->term_id)) . '" class="category-name category-link category-badge meta-badge">' . esc_html($first_cat->name) . '</a>';
                            }

                            // ブログジャンル（最大6件）
                            $genre_terms = get_the_terms(get_the_ID(), 'blog_genre');
                            if ($genre_terms && !is_wp_error($genre_terms)) {
                                $genre_links = array();
                                foreach (array_slice($genre_terms, 0, 6) as $genre_term) {
                                    $genre_links[] = '<a href="' . esc_url(get_term_link($genre_term)) . '" class="category-name genre-badge category-badge meta-badge">' . esc_html($genre_term->name) . '</a>';
                                }
                                if (!empty($genre_links)) {
                                    $parts[] = implode(' ', $genre_links);
                                }
                            }

                            echo implode(' <span class="meta-separator">|</span> ', $parts);
                            ?>
                        </span>

                        <span class="sidebar-comment-num">
                            <?php comments_popup_link('<i class="far fa-comments"></i> : 0', '<i class="far fa-comments"></i> : 1', '<i class="far fa-comments"></i> : %'); ?>
                        </span>
                    </p>
                </div> <!-- /.post-meta -->

                <div class="blog-thumbnail-box1"> <!-- /.blog-thumbnail-box1 -->
                    <?php
                    $days = 14;
                    $now = date_i18n('U');
                    $entry = get_the_time('U');
                    if (($now - $entry) / 86400 < $days) {
                        echo '<div class="box new-post">';
                        echo '<div class="ribbon ribbon-top-left"><span>New</span></div>';
                        echo '</div>';
                    }

                    $type = get_post_meta($post->ID, 'page_featured_type', true);
                    $video_id = get_post_meta($post->ID, 'page_video_id', true);

                    if ($type == 'youtube' && !empty($video_id)) {
                        echo '<div class="blog-post-image youtube" style="aspect-ratio: 16 / 9;">';
                        echo '<lite-youtube videoid="' . esc_attr($video_id) . '" playlabel="Play: ' . esc_html(get_the_title()) . '"></lite-youtube>';
                        echo '</div>';
                    } elseif ($type == 'vimeo' && !empty($video_id)) {
                        echo '<div class="blog-post-image vimeo" style="aspect-ratio: 16 / 9;">';
                        echo '<lite-vimeo videoid="' . esc_attr($video_id) . '"><div class="ltv-playbtn"></div></lite-vimeo>';
                        echo '</div>';
                    } elseif (has_post_thumbnail()) {
                        $thumb = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), array(600, 600));
                        echo '<a href="' . esc_url(get_permalink()) . '" class="thumbnail-link">';
                        echo '<div class="blog-post-image" style="background-image:url(' . esc_url($thumb[0]) . '); aspect-ratio: 16 / 9;">';
                        echo '<div class="thumbnail-overlay"><h3>' . esc_html(get_the_title()) . '</h3></div>';
                        echo '</div></a>';
                    } else {
                        $noimage = get_template_directory_uri() . '/images/noimage.gif';
                        echo '<a href="' . esc_url(get_permalink()) . '" class="thumbnail-link">';
                        echo '<div class="blog-post-image" style="background-image:url(' . esc_url($noimage) . '); aspect-ratio: 16 / 9;">';
                        echo '<div class="thumbnail-overlay"><h3>' . esc_html(get_the_title()) . '</h3></div>';
                        echo '</div></a>';
                    }
                    ?>

                    <div class="heart-icon" data-post-id="<?php echo get_the_ID(); ?>"> <!-- /.heart-icon -->
                        <svg class="icon icon-heart <?php echo get_post_meta(get_the_ID(), 'liked', true) ? 'liked' : ''; ?>">
                            <use xlink:href="#icon-heart"></use>
                        </svg>
                    </div> <!-- /.heart-icon -->
                </div> <!-- /.blog-thumbnail-box1 -->

                <p class="custom-excerpt"><?php echo dess_get_excerpt(180); ?></p> <!-- /.custom-excerpt -->

                <div class="more-link-wrapper"> <!-- /.more-link-wrapper -->
                    <a href="<?php the_permalink(); ?>" title="続きを読む" class="more-link">続きを読む &raquo;</a>
                </div> <!-- /.more-link-wrapper -->

            </div> <!-- /.main-custom-post -->
        <?php endwhile; ?>

        <div class="blog-pagination"> <!-- /.blog-pagination -->
            <?php echo paginate_links(array(
                'total' => $custom_query->max_num_pages,
                'prev_text' => '&#8592;',
                'next_text' => '&#8594;',
            )); ?>
        </div> <!-- /.blog-pagination -->

    <?php else : ?>
        <p>記事が見つかりませんでした。</p>
    <?php endif; ?>

</div> <!-- /#main -->

<div id="single"> <!-- /#single -->
    <?php get_template_part('sidebar-land'); ?>
</div> <!-- /#single -->

<?php get_footer(); ?>