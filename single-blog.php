<?php
/*
 * Template Name: Single Blog
 * Template Post Type: blog
 *
 * ------------------------------------------------------------
 * このテンプレートで直した点
 * ------------------------------------------------------------
 * 1. category 表示をやめて blog_genre 表示に統一
 * 2. 関連記事の抽出条件を category__in から blog_genre の tax_query に変更
 * 3. タグ表示も post_tag が不要なら消せるようにコメントを整理
 * 4. 何を参照しているか分かるように解説コメントを追加
 */
get_header('77');

$post_id = get_queried_object_id();

/**
 * ------------------------------------------------------------
 * 現在の記事に付いている blog_genre を取得
 * ------------------------------------------------------------
 * - single 上のジャンル表示に使う
 * - 関連記事の抽出条件にも使う
 */
$blog_genres = get_the_terms($post_id, 'blog_genre');
$blog_genre_ids = array();

if (!empty($blog_genres) && !is_wp_error($blog_genres)) {
    foreach ($blog_genres as $genre) {
        $blog_genre_ids[] = (int) $genre->term_id;
    }
}
?>

<div id="breadcrumbs">
    <?php
    // breadcrumb2();
    // パンくず側も category ではなく blog_genre を見るように
    // 別ファイルで直す必要があるなら、そちらも修正対象
    ?>
</div>

<div id="main">

    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>

            <div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

                <div class="back-link">
                    <a href="<?php echo esc_url(home_url('/fudosan-column/')); ?>">
                        <svg class="icon icon-arrow-left2" aria-hidden="true" focusable="false">
                            <use xlink:href="#icon-arrow-left2"></use>
                        </svg>
                        <span>前のページに戻る</span>
                    </a>
                </div>

                <h1 class="post-meta"><?php the_title(); ?></h1>

                <p class="post-meta">
                    <svg class="icon icon-price-tags" aria-hidden="true" focusable="false">
                        <use xlink:href="#icon-price-tags"></use>
                    </svg>

                    <span class="category">
                        <?php
                        /**
                         * ------------------------------------------------------------
                         * blog_genre を表示
                         * ------------------------------------------------------------
                         * - 以前の do_shortcode('[category_taxonomy_links]') は
                         *   category を参照している可能性が高いので使わない
                         * - ここで blog_genre を直接出力する
                         */
                        if (!empty($blog_genres) && !is_wp_error($blog_genres)) {
                            $genre_links = array();

                            foreach ($blog_genres as $genre) {
                                $genre_link = get_term_link($genre);

                                if (!is_wp_error($genre_link)) {
                                    $genre_links[] = '<a href="' . esc_url($genre_link) . '">' . esc_html($genre->name) . '</a>';
                                }
                            }

                            echo implode(', ', $genre_links);
                        } else {
                            echo '<span class="no-genre">ジャンル未設定</span>';
                        }
                        ?>
                    </span>

                    <span class="sidebar-comment-num">
                        <?php comments_popup_link('<i class="far fa-comments"></i> : 0', '<i class="far fa-comments"></i> : 1', '<i class="far fa-comments"></i> : %'); ?>
                    </span>
                </p>

                <?php
                // ------------------------------------------------------------
                // タブ（画像 / パノラマ）
                // ------------------------------------------------------------
                ob_start();
                get_template_part('single-template-parts/image-tab');
                $image_tab_content = ob_get_clean();

                ob_start();
                get_template_part('single-template-parts/panorama-tab');
                $panorama_tab_content = ob_get_clean();

                // 空判定は strip_tags まで行う
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
                    <?php if (trim(get_the_content()) !== '') : ?>
                        <?php the_content(); ?>
                    <?php else : ?>
                        <p>コンテンツはまだありません。</p>
                    <?php endif; ?>
                </div>

                <?php
                /**
                 * ------------------------------------------------------------
                 * 追加コンテンツ
                 * ------------------------------------------------------------
                 * - 既存の関数がある場合だけ表示
                 */
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
                    <?php
                    /**
                     * ------------------------------------------------------------
                     * タグ表示
                     * ------------------------------------------------------------
                     * - blog で post_tag をやめるなら、この the_tags() は消してよい
                     * - まだ post_tag を使うならこのままでよい
                     */
                    the_tags('Tag : ', ', ');
                    ?>
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
            <?php get_template_part('reservation-form-content', null, array('post_id' => $post_id)); ?>
        </div>

        <div id="tab-comments" class="custom-tab-pane" style="display:none;">
            <?php
            if (comments_open()) {
                comments_template();
            }
            ?>
        </div>
    </div>

    <?php
    /**
     * ------------------------------------------------------------
     * 関連記事（blog CPT）
     * ------------------------------------------------------------
     * 以前:
     * - get_the_category($post_id)
     * - category__in
     *
     * 修正後:
     * - get_the_terms($post_id, 'blog_genre')
     * - tax_query で blog_genre を参照
     */
    $related_args = array(
        'post_type'      => 'blog',
        'post__not_in'   => array($post_id),
        'posts_per_page' => 3,
        'orderby'        => 'rand',
    );

    if (!empty($blog_genre_ids)) {
        $related_args['tax_query'] = array(
            array(
                'taxonomy' => 'blog_genre',
                'field'    => 'term_id',
                'terms'    => $blog_genre_ids,
            ),
        );
    }

    $my_query = new WP_Query($related_args);
    ?>

    <div class="related-posts">
        <h2 class="side-title">関連記事</h2>

        <?php if ($my_query->have_posts()) : ?>
            <ul id="related-posts">
                <?php while ($my_query->have_posts()) : $my_query->the_post(); ?>

                    <?php
                    /**
                     * ------------------------------------------------------------
                     * 関連記事側の blog_genre を取得
                     * ------------------------------------------------------------
                     */
                    $related_genres = get_the_terms(get_the_ID(), 'blog_genre');
                    ?>

                    <li class="clearfix">
                        <div class="main-custom-post">

                            <div id="post-<?php the_ID(); ?>" <?php post_class('custom-post'); ?>>

                                <div class="post-meta">
                                    <h3 class="related-post-title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h3>

                                    <p class="post-meta-row">
                                    <svg class="icon icon-price-tags" aria-hidden="true" focusable="false">
                                        <use xlink:href="#icon-price-tags"></use>
                                    </svg>

                                    <span class="category">
                                        <?php
                                        if (!empty($related_genres) && !is_wp_error($related_genres)) {
                                            $related_genre_links = array();

                                            foreach ($related_genres as $related_genre) {
                                                $related_genre_link = get_term_link($related_genre);

                                                if (!is_wp_error($related_genre_link)) {
                                                    $related_genre_links[] = '<a href="' . esc_url($related_genre_link) . '">' . esc_html($related_genre->name) . '</a>';
                                                }
                                            }

                                            echo implode(', ', $related_genre_links);
                                        } else {
                                            echo '<span class="no-genre">ジャンル未設定</span>';
                                        }
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
                                        $now   = date_i18n('U');
                                        $entry = get_the_time('U');
                                        $term  = date('U', ($now - $entry)) / 86400;

                                        if ($days > $term) {
                                            echo '<div class="box new-post"><div class="ribbon ribbon-top-left"><span>New</span></div></div>';
                                        }

                                        if (has_post_thumbnail()) {
                                            $thumb = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), array(600, 600));
                                            $bg = $thumb ? $thumb[0] : '';
                                        } else {
                                            $bg = get_template_directory_uri() . '/images/noimage.gif';
                                        }
                                        ?>
                                        <div class="blog-post-image" style="background-image:url('<?php echo esc_url($bg); ?>');">
                                            <div class="thumbnail-overlay">
                                                <span class="thumbnail-title"><?php echo esc_html(get_the_title()); ?></span>
                                            </div>
                                        </div>
                                    </a>

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
                                /**
                                 * ------------------------------------------------------------
                                 * 抜粋
                                 * ------------------------------------------------------------
                                 * - 抜粋があればそれを使う
                                 * - 無ければ本文から生成
                                 */
                                $excerpt = get_the_excerpt();

                                if (!$excerpt) {
                                    $raw = strip_shortcodes(get_the_content());
                                    $raw = wp_strip_all_tags($raw);
                                    $excerpt = wp_trim_words($raw, 60, '…');
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