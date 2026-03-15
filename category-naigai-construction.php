<?php
get_header('77');
?>


<?php
// カテゴリーページでビュー切り替えボタンを表示
display_view_toggle_buttons();
?>
<div id="main">

    <?php
    // 現在の価格範囲をGETパラメータから取得
    $currentMin = isset($_GET['min_price']) ? (int)$_GET['min_price'] : 1000;  // デフォルト最小価格
    $currentMax = isset($_GET['max_price']) ? (int)$_GET['max_price'] : 4000;  // デフォルト最大価格

    // 内外建設カテゴリーの投稿を取得するクエリ
    $house_query = new WP_Query(array(
        'category_name' => 'naigai-construction',  // 内外建設カテゴリーのスラッグ
        'posts_per_page' => 10,  // 1ページあたりの投稿数
        'paged' => get_query_var('paged') ? get_query_var('paged') : 1,  // ページネーションの処理
        'meta_query' => array(
            'relation' => 'OR',  // OR条件で価格範囲内または0円を取得
            // 価格が範囲内の投稿
            array(
                'key' => 'Price',  // 価格情報が格納されているカスタムフィールド
                'value' => array($currentMin, $currentMax),
                'compare' => 'BETWEEN',
                'type' => 'NUMERIC',
            ),
            // 価格が0円の投稿（売却済み）
            array(
                'key' => 'Price',  // 価格情報が格納されているカスタムフィールド
                'value' => '0',
                'compare' => '=',  // 価格が0の投稿を取得
                'type' => 'NUMERIC',  // 数値として比較
            ),
        ),
    ));


    // ループ
    if ($house_query->have_posts()) :
        while ($house_query->have_posts()) : $house_query->the_post();
    ?>
            <div id="post-<?php the_ID(); ?>" <?php post_class('main-custom-post'); ?>>

                <!-- Post Meta -->
                <div class="post-meta">
                    <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
                        <h1><?php the_title(); ?></h1>
                    </a>
                    <p class="post-meta-row">
                        <svg class="icon icon-price-tags">
                            <use href="#icon-price-tags"></use>
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
                    </p>
                </div>

                <!-- Thumbnail Box -->
                <div class="blog-thumbnail-box">
                    <a href="<?php the_permalink(); ?>" title="「<?php the_title(); ?>」Read">
                        <?php
                        // NEWマークを表示
                        $days = 14;  // NEWマークを表示する日数
                        $now = date_i18n('U');  // 現在時間
                        $entry = get_the_time('U');  // 投稿日時
                        $term = date('U', ($now - $entry)) / 86400;
                        if ($days > $term) {
                            echo '<div class="box new-post">';  // ここで"new-post"クラスを追加
                            echo '<div class="ribbon ribbon-top-left">';
                            echo '<span>New</span>';
                            echo '</div>';
                            echo '</div>';
                        }
                        ?>

                        <?php
                        // サムネイル画像の表示
                        if (has_post_thumbnail()) :
                            $blog_thumbnail = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), array(600, 600));
                            echo '<div class="blog-post-image" style="background-image:url(' . $blog_thumbnail[0] . ');">';
                        else :
                            $noimage = get_template_directory_uri() . '/images/noimage.gif';
                            echo '<div class="blog-post-image" style="background-image:url(' . $noimage . ');">';
                        endif;
                        ?>

                        <!-- タイトル -->
                        <div class="thumbnail-overlay">
                            <h3><?php echo esc_html(get_the_title()); ?></h3>
                        </div>
                </div> <!-- End blog-post-image -->
                </a>

                <!-- ハートアイコン（いいね） -->
                <div class="heart-icon" data-post-id="<?php echo get_the_ID(); ?>">
                    <svg class="icon icon-heart <?php echo get_post_meta(get_the_ID(), 'liked', true) ? 'liked' : ''; ?>" data-post-id="<?php echo get_the_ID(); ?>">
                        <use xlink:href="#icon-heart"></use>
                    </svg>
                </div>
            </div>

            <!-- Content Box (Property Info Table) -->
            <div class="content-box">
                <?php
                // property-info-table.php を使いたい場合、そのファイルが存在するか確認してください
                if (file_exists(get_template_directory() . '/property-info-table.php')) {
                    get_template_part('property-info-table'); // 物件情報テーブルの読み込み
                } else {
                    // テーブルが無い場合は、簡単な出力を
                    echo '<p>物件情報がありません</p>';
                }
                ?>
                <!-- 投稿の抜粋を表示 -->
                <p class="custom-excerpt"><?php echo dess_get_excerpt(120); ?></p>
            </div>



            <!-- More Link -->
            <div class="more-link-wrapper">
                <a href="<?php the_permalink(); ?>" title="Read more" class="more-link">この物件の詳細を見る &raquo;</a>
                <a href="#store-reservation"
                    title="来店予約に進む"
                    class="store-reserve-link"
                    data-post-id="<?php echo get_the_ID(); ?>">
                    来店予約に進む »
                </a>
            </div>

</div>
<!--end post-->

<?php
        endwhile;
    else :
        echo '<p>投稿が見つかりませんでした</p>';
    endif;

    wp_reset_postdata();
?>

<?php // 投稿が10件以上ある場合のみページネーションを表示
if ($house_query->found_posts > 10): ?>
    <div class="blog-pagination">
        <?php
        echo paginate_links(array(
            'base' => str_replace(999999999, '%#%', esc_url(get_pagenum_link(999999999))),
            'format' => '?paged=%#%', // ページ番号を ?paged=1 のように設定
            'current' => max(1, get_query_var('paged')), // 現在のページ番号
            'total' => $house_query->max_num_pages, // 最大ページ数
            'prev_text' => '&#8592;', // 前のページリンク
            'next_text' => '&#8594;', // 次のページリンク
        ));
        ?>
    </div>
<?php endif; ?>

</div>
<!-- main id end -->

<!-- サイドバー -->
<div id="single">
    <?php get_template_part('sidebar-land'); // 不動産関連のサイドバー 
    ?>
</div>
<!-- サイドバー終了 -->

<?php //get_template_part('templates/store-reservation-modal'); 
?>


<?php get_footer(); ?>