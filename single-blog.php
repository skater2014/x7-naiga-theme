<?php
/*
 * Template Name: Single Blog
 * Template Post Type: blog
 */
get_header('77');

$post_id = get_queried_object_id();
?>

<div id="breadcrumbs">
    <?php // breadcrumb2(); 
    ?>
</div>

<div id="main">

    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>

            <div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

                <div class="back-link">
                    <a href="<?php echo esc_url(home_url('/blog/')); ?>">
                        <svg class="icon icon-arrow-left2" aria-hidden="true" focusable="false">
                            <use xlink:href="#icon-arrow-left2"></use>
                        </svg>
                        <span>前のページに戻る</span>
                    </a>
                </div>

                <h1 class="post-title"><?php the_title(); ?></h1>

                <p class="post-meta">
                    <svg class="icon icon-price-tags" aria-hidden="true" focusable="false">
                        <use xlink:href="#icon-price-tags"></use>
                    </svg>
                    <span class="category">
                        <?php echo do_shortcode('[category_taxonomy_links]'); ?>
                    </span>

                    <span class="sidebar-comment-num">
                        <?php comments_popup_link('<i class="far fa-comments"></i> : 0', '<i class="far fa-comments"></i> : 1', '<i class="far fa-comments"></i> : %'); ?>
                    </span>
                </p>

                <?php
                // -------------------------
                // タブ（画像 / パノラマ）
                // -------------------------
                ob_start();
                get_template_part('single-template-parts/image-tab');
                $image_tab_content = ob_get_clean();

                ob_start();
                get_template_part('single-template-parts/panorama-tab');
                $panorama_tab_content = ob_get_clean();

                // 空判定は strip_tags までやる（改行だけで true にならないように）
                $has_image = (trim(strip_tags($image_tab_content)) !== '');
                $has_pano  = (trim(strip_tags($panorama_tab_content)) !== '');
                $active_tab = $has_image ? 'image-tab' : ($has_pano ? 'panorama-tab' : '');
                ?>

                <?php if ($active_tab !== '') : ?>
                    <ul class="tab-links">
                        <?php if ($has_image) : ?>
                            <li class="tab-link <?php echo ($active_tab === 'image-tab') ? 'active' : ''; ?>" data-tab="image-tab">画像ビュー</li>
                        <?php endif; ?>
                        <?php if ($has_pano) : ?>
                            <li class="tab-link <?php echo ($active_tab === 'panorama-tab') ? 'active' : ''; ?>" data-tab="panorama-tab">360°パノラマビュー</li>
                        <?php endif; ?>
                    </ul>

                    <div id="tab-content">
                        <?php if ($has_image) echo $image_tab_content; ?>
                        <?php if ($has_pano)  echo $panorama_tab_content; ?>
                    </div>
                <?php endif; ?>

                <div class="blog-main-content">
                    <?php if (trim(get_the_content()) !== '') : the_content();
                    else : ?>
                        <p>コンテンツはまだありません。</p>
                    <?php endif; ?>
                </div>

                <?php
                if (function_exists('display_blog_lifestyle_slider')) {
                    display_blog_lifestyle_slider();
                }

                if (function_exists('display_blog_lifestyle_table')) {
                    display_blog_lifestyle_table();
                }
                ?>

                <div id="calendar"></div>

                <?php require get_template_directory() . '/pager-post-navigation.php'; ?>

                <p class="footer-post-meta">
                    <?php the_tags('Tag : ', ', '); ?>
                    <span class="post-author">
                        Author :
                        <a href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>">
                            <?php the_author(); ?>
                        </a>
                    </span>
                </p>

            </div><!-- /post -->

    <?php endwhile;
    endif; ?>

    <!-- 来店予約・口コミ -->
    <ul class="custom-tab-links">
        <li class="custom-tab-link active" data-tab="reservation">来店予約フォーム</li>
        <li class="custom-tab-link" data-tab="comments">口コミレビュー</li>
    </ul>

    <div class="custom-tab-content">
        <div id="tab-reservation" class="custom-tab-pane">
            <?php get_template_part('reservation-form-content', null, ['post_id' => $post_id]); ?>
        </div>
        <div id="tab-comments" class="custom-tab-pane" style="display:none;">
            <?php if (comments_open()) {
                comments_template();
            } ?>
        </div>
    </div>

    <?php
    // -------------------------
    // 関連記事（blog CPT）
    // -------------------------
    $cats = get_the_category($post_id);
    $cat_ids = [];

    if (!empty($cats)) {
        foreach ($cats as $c) $cat_ids[] = (int)$c->term_id;
    }

    $my_query = new WP_Query([
        'post_type'      => 'blog',
        'post__not_in'   => [$post_id],
        'posts_per_page' => 3,
        'orderby'        => 'rand',
        'category__in'   => $cat_ids,
    ]);
    ?>

    <div class="related-posts">
        <h2 class="side-title">関連記事</h2>

        <?php if ($my_query->have_posts()) : ?>
            <ul id="related-posts">
                <?php while ($my_query->have_posts()) : $my_query->the_post(); ?>
                    <li class="clearfix">
                        <div class="main-custom-post">

                            <div id="post-<?php the_ID(); ?>" <?php post_class('custom-post'); ?>>

                                <h3 class="related-post-title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h3>

                                <p class="post-meta">
                                    <svg class="icon icon-price-tags" aria-hidden="true" focusable="false">
                                        <use xlink:href="#icon-price-tags"></use>
                                    </svg>
                                    <span class="category"><?php the_category(', '); ?></span>

                                    <span class="sidebar-comment-num">
                                        <?php comments_popup_link('<i class="far fa-comments"></i> : 0', '<i class="far fa-comments"></i> : 1', '<i class="far fa-comments"></i> : %'); ?>
                                    </span>
                                </p>

                                <!-- Thumbnail Box（★relative基準） -->
                                <div class="blog-thumbnail-box">
                                    <a href="<?php the_permalink(); ?>" title="<?php echo esc_attr(get_the_title()); ?>">
                                        <?php
                                        $days  = 14;
                                        $now   = date_i18n('U');
                                        $entry = get_the_time('U');
                                        $term  = date('U', ($now - $entry)) / 86400;

                                        if ($days > $term) {
                                            echo '<div class="box new-post"><div class="ribbon ribbon-top-left"><span>New</span></div></div>';
                                        }

                                        if (has_post_thumbnail()) {
                                            $thumb = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), [600, 600]);
                                            $bg = $thumb ? $thumb[0] : '';
                                        } else {
                                            $bg = get_template_directory_uri() . '/images/noimage.gif';
                                        }
                                        ?>
                                        <div class="blog-post-image" style="background-image:url('<?php echo esc_url($bg); ?>');">
                                            <div class="thumbnail-overlay">
                                                <span class="thumbnail-title"><?php echo esc_html(get_the_title()); ?></span>
                                            </div>
                                        </div><!-- /.blog-post-image -->
                                    </a>

                                    <!-- ハート（★blog-thumbnail-boxの中に置く） -->
                                    <div class="heart-icon <?php echo get_post_meta(get_the_ID(), 'liked', true) ? 'active' : ''; ?>"
                                        data-post-id="<?php echo esc_attr(get_the_ID()); ?>">
                                        <svg class="icon icon-heart" aria-hidden="true" focusable="false">
                                            <use xlink:href="#icon-heart"></use>
                                        </svg>
                                    </div>

                                </div><!-- /.blog-thumbnail-box -->

                            </div><!-- /.custom-post -->

                            <div class="content-box">
                                <?php
                                // 抜粋があればそれ、無ければ本文から生成
                                $excerpt = get_the_excerpt();
                                if (!$excerpt) {
                                    $raw = strip_shortcodes(get_the_content());
                                    $raw = wp_strip_all_tags($raw);
                                    $excerpt = wp_trim_words($raw, 60, '…'); // 60語（日本語なら体感100〜140文字くらい）
                                }

                                echo '<p class="related-excerpt">' . esc_html($excerpt) . '</p>';
                                ?>
                            </div>


                            <div class="more-link-wrapper">
                                <a href="<?php the_permalink(); ?>" class="more-link">この物件の詳細を見る &raquo;</a>
                                <a href="#store-reservation" class="store-reserve-link" data-post-id="<?php echo esc_attr(get_the_ID()); ?>">
                                    来店予約に進む »
                                </a>
                            </div>

                        </div><!-- /.main-custom-post -->
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else : ?>
            <p>関連する記事はありませんでした ...</p>
        <?php endif; ?>

        <?php wp_reset_postdata(); ?>
    </div><!-- /.related-posts -->

</div><!-- /#main -->

<div id="single">
    <?php get_template_part('sidebar-land'); ?>
</div>

<?php get_footer(); ?>