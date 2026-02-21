<?php

/**
 * category.php（物件カテゴリ統合 + 価格フィルター対応）
 * - ?min_price=xxx&max_price=yyy で Price(meta) を BETWEEN 絞り込み
 * - カテゴリ別デフォルト価格
 * - 必要カテゴリのみ Price=0（売却済み）も混ぜる
 */

$term = get_queried_object();
$slug = ($term && !is_wp_error($term) && isset($term->slug)) ? $term->slug : '';

// 物件カテゴリだけ header-77 を使う（他カテゴリに影響出したくないならこの分岐は必須）
$property_slugs = ['naigai-construction', 'naigai-tochi', 'house'];
if (in_array($slug, $property_slugs, true)) {
    get_header('77');
} else {
    get_header();
}

// ビュー切替ボタン（存在するなら）
if (function_exists('display_view_toggle_buttons')) {
    display_view_toggle_buttons();
}
?>
<div id="main">
    <?php
    // -----------------------------
    // カテゴリ別：デフォルト価格 & 売却済み(Price=0)の扱い
    // -----------------------------
    $price_defaults = [
        'naigai-construction' => ['min' => 1000, 'max' => 4000, 'include_zero' => true],
        'house'               => ['min' => 1000, 'max' => 4000, 'include_zero' => true],
        'naigai-tochi'        => ['min' => 100,  'max' => 3000, 'include_zero' => false],
    ];
    $defaults = $price_defaults[$slug] ?? ['min' => 300, 'max' => 4000, 'include_zero' => false];

    // GET（無ければカテゴリデフォルト）
    $currentMin = isset($_GET['min_price']) ? (int) $_GET['min_price'] : (int) $defaults['min'];
    $currentMax = isset($_GET['max_price']) ? (int) $_GET['max_price'] : (int) $defaults['max'];

    // clamp（スライダー想定 0〜10000）
    $currentMin = max(0, min(10000, $currentMin));
    $currentMax = max($currentMin, min(10000, $currentMax));

    $paged = (int) max(1, get_query_var('paged') ?: 1);

    // meta_query（Price 範囲 or Price=0）
    if (!empty($defaults['include_zero'])) {
        $meta_query = [
            'relation' => 'OR',
            [
                'key'     => 'Price',
                'value'   => [$currentMin, $currentMax],
                'compare' => 'BETWEEN',
                'type'    => 'NUMERIC',
            ],
            [
                'key'     => 'Price',
                'value'   => 0,
                'compare' => '=',
                'type'    => 'NUMERIC',
            ],
        ];
    } else {
        $meta_query = [
            [
                'key'     => 'Price',
                'value'   => [$currentMin, $currentMax],
                'compare' => 'BETWEEN',
                'type'    => 'NUMERIC',
            ],
        ];
    }

    // クエリ（カテゴリそのものを対象）
    $query = new WP_Query([
        'post_type'      => 'post',
        'posts_per_page' => 10,
        'paged'          => $paged,
        'cat'            => $term ? (int) $term->term_id : 0,
        'meta_query'     => $meta_query,
    ]);

    if ($query->have_posts()) :
        while ($query->have_posts()) : $query->the_post();
    ?>
            <div id="post-<?php the_ID(); ?>" <?php post_class('main-custom-post'); ?>>

                <!-- Post Meta -->
                <div class="post-meta">
                    <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
                        <h1><?php the_title(); ?></h1>
                    </a>

                    <p class="post-meta-row">
                        <svg class="icon icon-price-tags" aria-hidden="true">
                            <use href="#icon-price-tags"></use>
                        </svg>

                        <span class="category">
                            <?php echo do_shortcode('[category_taxonomy_links]'); ?>
                        </span>

                        <span class="sidebar-comment-num">
                            <?php comments_popup_link('<i class="far fa-comments"></i> : 0', '<i class="far fa-comments"></i> : 1', '<i class="far fa-comments"></i> : %'); ?>
                        </span>
                    </p>
                </div>

                <!-- Thumbnail Box -->
                <div class="blog-thumbnail-box">
                    <a href="<?php the_permalink(); ?>" title="「<?php the_title(); ?>」Read">
                        <?php
                        // NEW マーク
                        $days  = 14;
                        $now   = (int) current_time('timestamp');
                        $entry = (int) get_the_time('U');
                        $diff_days = ($now - $entry) / 86400;
                        if ($days > $diff_days) :
                            echo '<div class="box new-post"><div class="ribbon ribbon-top-left"><span>New</span></div></div>';
                        endif;

                        // サムネ背景
                        if (has_post_thumbnail()) {
                            $thumb = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), [600, 600]);
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

                    <!-- ハート -->
                    <div class="heart-icon" data-post-id="<?php echo esc_attr(get_the_ID()); ?>">
                        <svg class="icon icon-heart <?php echo get_post_meta(get_the_ID(), 'liked', true) ? 'liked' : ''; ?>" data-post-id="<?php echo esc_attr(get_the_ID()); ?>">
                            <use xlink:href="#icon-heart"></use>
                        </svg>
                    </div>
                </div>

                <!-- Content Box -->
                <div class="content-box">
                    <?php
                    $table_tpl = locate_template('property-info-table.php', false, false);
                    if ($table_tpl) {
                        get_template_part('property-info-table');
                    } else {
                        echo '<p>物件情報がありません</p>';
                    }
                    ?>
                    <p class="custom-excerpt"><?php echo wp_kses_post(dess_get_excerpt(80)); ?></p>
                </div>

                <!-- More Link -->
                <div class="more-link-wrapper">
                    <a href="<?php the_permalink(); ?>" title="Read more" class="more-link">この物件の詳細を見る &raquo;</a>
                    <a href="#store-reservation"
                        title="来店予約に進む"
                        class="store-reserve-link"
                        data-post-id="<?php echo esc_attr(get_the_ID()); ?>">
                        来店予約に進む »
                    </a>
                </div>

            </div>
        <?php
        endwhile;
    else :
        echo '<p>投稿が見つかりませんでした</p>';
    endif;

    wp_reset_postdata();

    // ページネーション
    if ($query->max_num_pages > 1) : ?>
        <div class="blog-pagination">
            <?php
            echo paginate_links([
                'base'      => str_replace(999999999, '%#%', esc_url(get_pagenum_link(999999999))),
                'format'    => '?paged=%#%',
                'current'   => max(1, $paged),
                'total'     => $query->max_num_pages,
                'prev_text' => '&#8592;',
                'next_text' => '&#8594;',
            ]);
            ?>
        </div>
    <?php endif; ?>
</div><!-- /#main -->

<!-- サイドバー -->
<div id="single">
    <?php get_template_part('sidebar-land'); ?>
</div>

<?php get_footer(); ?>