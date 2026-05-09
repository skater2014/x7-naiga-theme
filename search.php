<?php

/**
 * search.php
 * =========================================================
 * このテンプレートの役割
 * ---------------------------------------------------------
 * 1. 検索結果ページで post / house を同時に表示する
 * 2. 必要なときだけ house-type / region / 価格で絞り込む
 * 3. 価格フィルター未指定時は、Price メタ未設定の house も落とさない
 *
 * 重要
 * ---------------------------------------------------------
 * 以前のコードでは meta_query を常に適用していたため、
 * Price メタが無い house 投稿は検索結果から除外されていた。
 *
 * 今回の修正版では、
 * 「min_price / max_price が実際に指定されたときだけ」
 * Price の meta_query を入れるようにしている。
 */

get_header('77');

/* =========================================================
 * ビュー切替ボタン
 * ---------------------------------------------------------
 * 存在する場合だけ表示
 * ========================================================= */
if (function_exists('display_view_toggle_buttons')) {
    display_view_toggle_buttons();
}
?>

<div id="main">
    <?php
    /* =====================================================
     * 1) 現在の検索条件を取得
     * ===================================================== */

    /* キーワード検索 */
    $search_keyword = get_search_query();

    /* ページ番号 */
    $paged = (int) max(1, get_query_var('paged') ? get_query_var('paged') : 1);

    /* -----------------------------------------------------
     * 金額フィルター
     * -----------------------------------------------------
     * ここでは「値が指定されたかどうか」と
     * 「実際の数値」を分けて持つ
     * ----------------------------------------------------- */
    $has_min_price = isset($_GET['min_price']) && $_GET['min_price'] !== '';
    $has_max_price = isset($_GET['max_price']) && $_GET['max_price'] !== '';

    $currentMin = $has_min_price ? (int) $_GET['min_price'] : 0;
    $currentMax = $has_max_price ? (int) $_GET['max_price'] : 10000;

    /* 数値の暴走防止 */
    $currentMin = max(0, min(10000, $currentMin));
    $currentMax = max($currentMin, min(10000, $currentMax));

    /* =====================================================
     * 2) tax_query を組み立てる
     * -----------------------------------------------------
     * house-type / region が指定された時だけ使う
     * ===================================================== */
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

    /* =====================================================
     * 3) 基本クエリ
     * -----------------------------------------------------
     * 検索対象は post / house
     * ===================================================== */
    $args = array(
        'post_type'      => array('post', 'house'),
        'posts_per_page' => 10,
        'paged'          => $paged,
        's'              => $search_keyword,
    );

    /* =====================================================
     * 4) tax_query を追加
     * ===================================================== */
    if (!empty($tax_query)) {
        $args['tax_query'] = array_merge(array('relation' => 'AND'), $tax_query);
    }

    /* =====================================================
     * 5) 価格フィルターを「指定時だけ」追加
     * -----------------------------------------------------
     * 以前はこれを常に入れていたため、
     * Price メタが無い house が全部落ちていた。
     *
     * 今回は min/max が来た時だけ Price で絞る。
     * ===================================================== */
    if ($has_min_price || $has_max_price) {
        $args['meta_query'] = array(
            'relation' => 'OR',

            /* 指定価格帯に入る物件 */
            array(
                'key'     => 'Price',
                'value'   => array($currentMin, $currentMax),
                'compare' => 'BETWEEN',
                'type'    => 'NUMERIC',
            ),

            /* Price = 0 は売却済み扱いとして残す */
            array(
                'key'     => 'Price',
                'value'   => 0,
                'compare' => '=',
                'type'    => 'NUMERIC',
            ),
        );
    }

    /* 実行 */
    $custom_query = new WP_Query($args);

    /* =====================================================
     * 6) ループ開始
     * ===================================================== */
    if ($custom_query->have_posts()) :
        while ($custom_query->have_posts()) :
            $custom_query->the_post();
            $post_id = get_the_ID();
    ?>
            <div id="post-<?php the_ID(); ?>" <?php post_class('main-custom-post'); ?>>

                <!-- タイトル -->
                <a href="<?php the_permalink(); ?>">
                    <h1><?php the_title(); ?></h1>
                </a>

                <!-- メタ情報 -->
                <div class="post-meta">
                    <?php
                    /* =====================================================
     * 投稿タイプごとの表示ラベル
     * -----------------------------------------------------
     * post  の場合 → 通常カテゴリーを表示
     * house の場合 → 「住宅情報」という固定ラベルを表示
     *
     * 理由
     * -----------------------------------------------------
     * 今までのコードは [category_taxonomy_links] だけを出していたため、
     * post のカテゴリーは見えても、
     * house そのものの投稿タイプ名は見えなかった。
     * ===================================================== */
                    $current_post_type = get_post_type(get_the_ID());
                    ?>

                    <?php if ($current_post_type === 'post') : ?>
                        <!-- -------------------------------------------------
                        post の時
                        通常カテゴリーを表示
                    -------------------------------------------------- -->
                        <span class="category">
                            <?php echo do_shortcode('[category_taxonomy_links]'); ?>
                        </span>

                    <?php elseif ($current_post_type === 'house') : ?>
                        <!-- -------------------------------------------------
                    house の時
                    カスタム投稿タイプ用の固定ラベルを表示
                    「house」そのままではなく、日本語で「住宅情報」にする
                -------------------------------------------------- -->
                        <span class="category house-label">住宅情報</span>
                    <?php endif; ?>

                    <!-- コメント数 -->
                    <span class="sidebar-comment-num">
                        <?php comments_popup_link(
                            '<i class="far fa-comments"></i> : 0',
                            '<i class="far fa-comments"></i> : 1',
                            '<i class="far fa-comments"></i> : %'
                        ); ?>
                    </span>
                </div>

                <!-- サムネイル / 動画 -->
                <div class="blog-thumbnail-box1">
                    <?php
                    /* NEWマーク表示 */
                    $days      = 14;
                    $now       = (int) current_time('timestamp');
                    $entry     = (int) get_the_time('U');
                    $diff_days = ($now - $entry) / 86400;

                    if ($days > $diff_days) {
                        echo '<div class="box new-post"><div class="ribbon ribbon-top-left"><span>New</span></div></div>';
                    }

                    /* 動画タイプ取得 */
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
                        echo '</div>';
                        echo '</a>';
                    }
                    ?>

                    <!-- ハート -->
                    <div class="heart-icon" data-post-id="<?php echo esc_attr($post_id); ?>">
                        <svg class="icon icon-heart <?php echo get_post_meta($post_id, 'liked', true) ? 'liked' : ''; ?>" data-post-id="<?php echo esc_attr($post_id); ?>">
                            <use xlink:href="#icon-heart"></use>
                        </svg>
                    </div>
                </div>

                <!-- 本文側 -->
                <div class="content-box">
                    <?php get_template_part('property-info-table'); ?>
                    <p class="custom-excerpt"><?php echo wp_kses_post(dess_get_excerpt(80)); ?></p>
                </div>

                <!-- リンク -->
                <div class="more-link-wrapper">
                    <a href="<?php the_permalink(); ?>" title="Read more" class="more-link">この物件の詳細を見る &raquo;</a>

                    <a
                        href="#store-reservation"
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

        /* =====================================================
         * 7) ページネーション
         * -----------------------------------------------------
         * 検索条件を次ページへ維持する
         * ===================================================== */
        $add_args = array(
            's'          => $search_keyword ?: '',
            'house_type' => $_GET['house_type'] ?? '',
            'region'     => $_GET['region'] ?? '',
            'view'       => $_GET['view'] ?? 'list',
            'min_price'  => $has_min_price ? $_GET['min_price'] : '',
            'max_price'  => $has_max_price ? $_GET['max_price'] : '',
        );

        /* 空値は落とす。ただし s は残す */
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