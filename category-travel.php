<?php
echo '<!-- category-travel.php loaded -->';

/**
 * category-travel.php
 * travelカテゴリ専用テンプレート
 */
get_header('77');

if (function_exists('display_view_toggle_buttons')) {
    display_view_toggle_buttons();
}

$current_category = get_queried_object();
$paged = get_query_var('paged') ? (int) get_query_var('paged') : 1;

$travel_query = new WP_Query(array(
    'cat'                 => ($current_category && isset($current_category->term_id)) ? (int) $current_category->term_id : 0,
    'post_type'           => 'blog',
    'post_status'         => 'publish',
    'posts_per_page'      => 10,
    'paged'               => max(1, $paged),
    'ignore_sticky_posts' => true,
));
?>

<div id="main">

    <?php echo '<!-- travel_found_posts=' . intval($travel_query->found_posts) . ' -->'; ?>
    <?php echo '<!-- travel_max_pages=' . intval($travel_query->max_num_pages) . ' -->'; ?>
    <?php if ($travel_query->have_posts()) : ?>
        <?php while ($travel_query->have_posts()) : $travel_query->the_post(); ?>
            <div id="post-<?php the_ID(); ?>" <?php post_class('main-custom-post'); ?>>

                <div class="post-meta">
                    <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
                        <h2><?php the_title(); ?></h2>
                    </a>

                    <p class="post-meta-row">
                        <svg class="icon icon-price-tags" aria-hidden="true">
                            <use href="#icon-price-tags"></use>
                        </svg>

                        <span class="category">
                            <?php
                            $parts = array();

                            $categories = get_the_category();
                            if (!empty($categories)) {
                                $first_cat = $categories[0];
                                $parts[] = '<a href="' . esc_url(get_category_link($first_cat->term_id)) . '" class="category-name category-link category-badge meta-badge">' . esc_html($first_cat->name) . '</a>';
                            }

                            $genre_terms = get_the_terms(get_the_ID(), 'blog_genre');
                            if ($genre_terms && !is_wp_error($genre_terms)) {
                                $genre_links = array();
                                foreach (array_slice($genre_terms, 0, 3) as $genre_term) {
                                    $genre_links[] = '<a href="' . esc_url(get_term_link($genre_term)) . '" class="blog-feature-badge genre-badge category-badge meta-badge">' . esc_html($genre_term->name) . '</a>';
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
                </div>

                <div class="blog-thumbnail-box">
                    <a href="<?php the_permalink(); ?>" title="<?php echo esc_attr(get_the_title()); ?>">
                        <?php
                        $days  = 14;
                        $now   = current_time('timestamp');
                        $entry = get_the_time('U');
                        $term  = ($now - $entry) / DAY_IN_SECONDS;

                        if ($days > $term) {
                            echo '<div class="box new-post"><div class="ribbon ribbon-top-left"><span>New</span></div></div>';
                        }

                        if (has_post_thumbnail()) {
                            $thumb = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'large');
                            $bg = $thumb ? $thumb[0] : '';
                        } else {
                            $bg = get_template_directory_uri() . '/images/noimage.gif';
                        }
                        ?>
                        <div class="blog-post-image" style="background-image:url('<?php echo esc_url($bg); ?>');">
                            <div class="thumbnail-overlay">
                                <h3><?php echo esc_html(get_the_title()); ?></h3>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="content-box">
                    <p class="custom-excerpt"><?php echo esc_html(dess_get_excerpt(180)); ?></p>
                </div>

                <div class="more-link-wrapper">
                    <a href="<?php the_permalink(); ?>" title="続きを読む" class="more-link">続きを読む &raquo;</a>
                </div>

            </div>
        <?php endwhile; ?>

        <div class="blog-pagination">
            <?php
            echo paginate_links(array(
                'total'     => $travel_query->max_num_pages,
                'current'   => max(1, $paged),
                'prev_text' => '&#8592;',
                'next_text' => '&#8594;',
            ));
            ?>
        </div>
    <?php else : ?>
        <p>投稿が見つかりませんでした。</p>
    <?php endif; ?>

    <?php wp_reset_postdata(); ?>
</div>

<div id="single">
    <?php get_template_part('sidebar-land'); ?>
</div>

<?php get_footer(); ?>