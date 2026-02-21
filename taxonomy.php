<?php
/****************************************
  カスタム投稿にカテゴリーを追加したタクソノミーの一覧表示のページです。
  index.php を複製した taxonomy.php です。
*****************************************/

get_header('77');
?>

<?php
// カテゴリーページでビュー切り替えボタンを表示
display_view_toggle_buttons();
?>
<?php
// ③ 現在表示しているターム情報を取得
$term = get_queried_object();

// GETから価格範囲を取得（既存のJSでURLにセットされた値）
$currentMin = isset($_GET['min_price']) ? (int)$_GET['min_price'] : 300;
$currentMax = isset($_GET['max_price']) ? (int)$_GET['max_price'] : 4000;

// ④ WP_Queryに tax_query + meta_query を設定
$args = array(
    'post_type' => array('house', 'post'),
    'tax_query' => array(
        array(
            'taxonomy' => $term->taxonomy,
            'field'    => 'slug',
            'terms'    => $term->slug,
        ),
    ),
    'paged' => get_query_var('paged') ? get_query_var('paged') : 1,
    'meta_query' => array(
        'relation' => 'OR',
        array(
            'key'     => 'Price',
            'value'   => array($currentMin, $currentMax),
            'compare' => 'BETWEEN',
            'type'    => 'NUMERIC',
        ),
        array(
            'key'     => 'Price',
            'value'   => '0',
            'compare' => '=',
            'type'    => 'NUMERIC',
        ),
    ),
);
$custom_query = new WP_Query($args);

?>

<!-- ぱんくずリストの追加 -->
<div id="breadcrumbs">
    <?php //breadcrumb2(); // カスタム投稿用のパンくず読み込み ?> 
</div>

<div id="main">
    <?php
    if ($custom_query->have_posts()) :
        while ($custom_query->have_posts()) : $custom_query->the_post();
    ?>
            <div id="post-<?php the_ID(); ?>" <?php post_class('main-custom-post'); ?>>

                <div class="post-meta">
                    <!-- Title above Post Meta -->
                    <a href="<?php the_permalink(); ?>">
                        <h2><?php the_title(); ?></h2>
                    </a>
                    
                    <svg class="icon icon-price-tags">
                        <use xlink:href="#icon-price-tags">
                                                    <?php
                                                    // ショートコードでカテゴリーとタクソノミーリンクを表示
                                                    // このコードは inc/functions/custom-functions.php で定義されたカスタム関数を使用しています。
                                                    ?>
                                                    <span class="category">
                                                        <?php echo do_shortcode('[category_taxonomy_links]'); ?>
                                                    </span>
                        </use>
                    </svg>

                    <span class="sidebar-comment-num">
                        <?php comments_popup_link('<i class="far fa-comments"></i> : 0', '<i class="far fa-comments"></i> : 1', '<i class="far fa-comments"></i> : %'); ?>
                    </span>
                </div>

<div class="blog-thumbnail-box1">
    <?php
    // NEWマークを表示する
    $days = 14;  // NEWマークを表示する日数
    $now = date_i18n('U');  // 現在の時間
    $entry = get_the_time('U');  // 投稿日時
    $term = ($now - $entry) / 86400;
    if ($days > $term) {
        echo '<div class="box new-post">';  // ここで"new-post"クラスを追加
        echo '<div class="ribbon ribbon-top-left">';
        echo '<span>New</span>';
        echo '</div>';
        echo '</div>';
    }
    ?>

    <?php
    // 追加情報の表示
    $type = get_post_meta($post->ID, 'page_featured_type', true);
    switch ($type) {
        case 'youtube':
            echo '<lite-youtube videoid="' . esc_attr(get_post_meta($post->ID, 'page_video_id', true)) . '" playlabel="Play: ' . esc_html(get_the_title()) . '" style="max-width:100%; height:auto;"></lite-youtube>';
            break;
        case 'vimeo':
            echo '<lite-vimeo videoid="' . esc_attr(get_post_meta($post->ID, 'page_video_id', true)) . '">
                <div class="ltv-playbtn"></div>
            </lite-vimeo>';
            break;
        default:
            if (has_post_thumbnail()) :
                // サムネイル画像がある場合
                $blog_thumbnail = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), array(600, 600));
                echo '<a href="' . esc_url(get_permalink()) . '" class="thumbnail-link">';
                echo '<div class="blog-post-image" style="background-image:url(' . esc_url($blog_thumbnail[0]) . '); aspect-ratio: 16 / 9;">';
                
                // オーバーレイとタイトルを追加
                echo '<div class="thumbnail-overlay">';
                echo '<h3>' . esc_html(get_the_title()) . '</h3>';
                echo '</div>';

                // ハートアイコン（いいね）
                echo '<div class="heart-icon" data-post-id="' . get_the_ID() . '">
                        <svg class="icon icon-heart ' . (get_post_meta(get_the_ID(), 'liked', true) ? 'liked' : '') . '" data-post-id="' . get_the_ID() . '">
                            <use xlink:href="#icon-heart"></use>
                        </svg>
                      </div>';
                echo '</div>';
                echo '</a>'; // リンク終了
            else :
                // サムネイルがない場合、「noimage」を表示
                $noimage = get_template_directory_uri() . '/images/noimage.gif';
                echo '<a href="' . esc_url(get_permalink()) . '" class="thumbnail-link">';
                echo '<div class="blog-post-image" style="background-image:url(' . esc_url($noimage) . '); aspect-ratio: 16 / 9;">';
                echo '<div class="thumbnail-overlay">';
                echo '<h3>' . esc_html(get_the_title()) . '</h3>';
                echo '</div>';
                echo '</div>';
                echo '</a>'; // リンク終了
            endif;
            break;
    }
    ?>
</div> <!-- End blog-thumbnail-box -->


                <div class="content-box1">
                    <?php get_template_part('property-info-table'); ?>

                    <!-- More Link -->
                    <div class="more-link-wrapper">
                        <a href="<?php the_permalink(); ?>" title="Read more" class="more-link">この物件の詳細を見る &raquo;</a>
                        <a href="#store-reservation" 
                           title="来店予約に進む" 
                           class="store-reserve-link" 
                           data-post-id="<?php echo get_the_ID(); ?>">
                           来店予約に進む &raquo;
                        </a>
                    </div>
                </div>
            </div>
    <?php endwhile; else : ?>
        <!-- 記事がない場合のメッセージ -->
        <div class="no-posts-message">
            <p>記事が見つかりませんでした。</p>
        </div>
    <?php endif; wp_reset_postdata(); ?>
    
    <div class="blog-pagination">
        <?php
        $big = 999999999; // need an unlikely integer
        echo paginate_links(array(
            'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
            'format' => '?paged=%#%',
            'current' => max(1, get_query_var('paged')),
            'total' => $custom_query->max_num_pages,
            'prev_text' => '&#8592;',
            'next_text' => '&#8594;'
        ));
        ?>
    </div>
</div>

<!-- サイドバー -->
<div id="single">
    <?php get_template_part('sidebar-house'); ?>
</div>

<?php get_footer(); ?>
