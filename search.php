<?php

/**
 * search.php（archive-house.php複製ベース）
 * - post / house を同時に表示
 * - Price(meta) で金額フィルター（BETWEEN）＋ Price=0（売却済み）も含める
 * - house-type / region で tax 絞り込み
 * - paginate_links で条件を維持（min/max も含める）
 */

get_header('77');

// ビュー切替（存在するなら）
if (function_exists('display_view_toggle_buttons')) {
    display_view_toggle_buttons();
}
?>

<div id="main">
    <?php
    // =====================================================
    // 1) 金額フィルター（デフォルト 100〜4000）
    //    ※ reset で min/max が消えたらデフォルトに戻る
    // =====================================================
    $currentMin = (isset($_GET['min_price']) && $_GET['min_price'] !== '') ? (int) $_GET['min_price'] : 100;
    $currentMax = (isset($_GET['max_price']) && $_GET['max_price'] !== '') ? (int) $_GET['max_price'] : 4000;

    // clamp（0〜10000想定）
    $currentMin = max(0, min(10000, $currentMin));
    $currentMax = max($currentMin, min(10000, $currentMax));

    $paged = (int) max(1, get_query_var('paged') ? get_query_var('paged') : 1);

    // =====================================================
    // 2) tax_query（house-type / region）
    // =====================================================
    $tax_query = array();

    if (!empty($_GET['house_type'])) {
        $tax_query[] = array(
            'taxonomy' => 'house-type',
            'field'    => 'slug',
            'terms'    => sanitize_text_field(wp_unslash($_GET['house_type'])),
            'operator' => 'IN',
        );
    }

    if (!empty($_GET['region'])) {
        $tax_query[] = array(
            'taxonomy' => 'region',
            'field'    => 'slug',
            'terms'    => sanitize_text_field(wp_unslash($_GET['region'])),
            'operator' => 'IN',
        );
    }

    // =====================================================
    // 3) WP_Query（★ここが重要：meta_query で Price を絞る）
    //    - Price が範囲内 OR Price=0（売却済み）
    //    - Price が無い投稿は一致しない → 「金額ある記事しか表示」になる
    // =====================================================
    $args = array(
        'post_type'      => array('post', 'house'),
        'posts_per_page' => 10,
        'paged'          => $paged,
        's'              => get_search_query(),

        'meta_query'     => array(
            'relation' => 'OR',
            array(
                'key'     => 'Price',
                'value'   => array($currentMin, $currentMax),
                'compare' => 'BETWEEN',
                'type'    => 'NUMERIC',
            ),
            array(
                'key'     => 'Price',
                'value'   => 0,
                'compare' => '=',
                'type'    => 'NUMERIC',
            ),
        ),
    );

    if (!empty($tax_query)) {
        $args['tax_query'] = array_merge(array('relation' => 'AND'), $tax_query);
    }

    $custom_query = new WP_Query($args);

    // =====================================================
    // 4) Loop
    // =====================================================
    if ($custom_query->have_posts()) :
        while ($custom_query->have_posts()) : $custom_query->the_post();
    ?>
            <div id="post-<?php the_ID(); ?>" <?php post_class('main-custom-post'); ?>>

                <a href="<?php the_permalink(); ?>">
                    <h1><?php the_title(); ?></h1>
                </a>

                <div class="post-meta">
                    <span class="category">
                        <?php echo do_shortcode('[category_taxonomy_links]'); ?>
                    </span>

                    <span class="sidebar-comment-num">
                        <?php comments_popup_link('<i class="far fa-comments"></i> : 0', '<i class="far fa-comments"></i> : 1', '<i class="far fa-comments"></i> : %'); ?>
                    </span>
                </div>

                <div class="blog-thumbnail-box1">
                    <?php
                    // NEWマーク
                    $days  = 14;
                    $now   = (int) current_time('timestamp');
                    $entry = (int) get_the_time('U');
                    $diff_days = ($now - $entry) / 86400;
                    if ($days > $diff_days) {
                        echo '<div class="box new-post"><div class="ribbon ribbon-top-left"><span>New</span></div></div>';
                    }

                    // 動画タイプ
                    $post_id  = get_the_ID();
                    $type     = get_post_meta($post_id, 'page_featured_type', true);
                    $video_id = get_post_meta($post_id, 'page_video_id', true);

                    if ($type === 'youtube' && !empty($video_id)) {
                        echo '<div class="blog-post-image youtube" style="aspect-ratio: 16 / 9;">';
                        echo '<lite-youtube videoid="' . esc_attr($video_id) . '" playlabel="Play: ' . esc_html(get_the_title()) . '" style="max-width:100%; height:auto;"></lite-youtube>';
                        echo '</div>';
                    } elseif ($type === 'vimeo' && !empty($video_id)) {
                        echo '<div class="blog-post-image vimeo" style="aspect-ratio: 16 / 9;">';
                        echo '<lite-vimeo videoid="' . esc_attr($video_id) . '"><div class="ltv-playbtn"></div></lite-vimeo>';
                        echo '</div>';
                    } else {
                        if (has_post_thumbnail()) {
                            $thumb = wp_get_attachment_image_src(get_post_thumbnail_id($post_id), array(600, 600));
                            $bg = $thumb ? $thumb[0] : '';
                        } else {
                            $bg = get_template_directory_uri() . '/images/noimage.gif';
                        }
                        echo '<a href="' . esc_url(get_permalink()) . '" class="thumbnail-link">';
                        echo '<div class="blog-post-image" style="background-image:url(' . esc_url($bg) . '); aspect-ratio: 16 / 9;">';
                        echo '<div class="thumbnail-overlay"><h3>' . esc_html(get_the_title()) . '</h3></div>';
                        echo '</div></a>';
                    }
                    ?>

                    <div class="heart-icon" data-post-id="<?php echo esc_attr($post_id); ?>">
                        <svg class="icon icon-heart <?php echo get_post_meta($post_id, 'liked', true) ? 'liked' : ''; ?>" data-post-id="<?php echo esc_attr($post_id); ?>">
                            <use xlink:href="#icon-heart"></use>
                        </svg>
                    </div>
                </div>

                <div class="content-box">
                    <?php get_template_part('property-info-table'); ?>
                    <p class="custom-excerpt"><?php echo wp_kses_post(dess_get_excerpt(80)); ?></p>
                </div>

                <div class="more-link-wrapper">
                    <a href="<?php the_permalink(); ?>" title="Read more" class="more-link">この物件の詳細を見る &raquo;</a>
                    <a href="#store-reservation"
                        title="来店予約に進む"
                        class="store-reserve-link"
                        data-post-id="<?php echo esc_attr($post_id); ?>">
                        来店予約に進む »
                    </a>
                </div>

            </div>
        <?php
        endwhile;

        wp_reset_postdata();

        // =====================================================
        // 5) Pagination（条件維持：min/max も必ず渡す）
        // =====================================================
        $add_args = array(
            's'          => get_search_query() ?: '', // 空でも s= を残す（必要なら）
            'house_type' => $_GET['house_type'] ?? '',
            'region'     => $_GET['region'] ?? '',
            'view'       => $_GET['view'] ?? 'list',
            'min_price'  => isset($_GET['min_price']) ? $_GET['min_price'] : '',
            'max_price'  => isset($_GET['max_price']) ? $_GET['max_price'] : '',
        );

        // s以外は空なら消す（0 は消さない）
        $add_args = array_merge(
            array('s' => $add_args['s']),
            array_filter($add_args, function ($v) {
                return $v !== '' && $v !== null;
            })
        );
        ?>
        <div class="blog-pagination">
            <?php
            echo paginate_links(array(
                'base'      => add_query_arg(array('paged' => '%#%')),
                'format'    => '',
                'current'   => max(1, $paged),
                'total'     => $custom_query->max_num_pages,
                'prev_text' => '&#8592;',
                'next_text' => '&#8594;',
                'add_args'  => $add_args,
            ));
            ?>
        </div>

    <?php else : ?>
        <p>物件が見つかりませんでした。</p>
    <?php endif; ?>
</div><!-- /#main -->

<div id="single">
    <?php get_template_part('sidebar-land'); ?>
</div>

<?php get_footer(); ?>