

<?php
/*
Template Name: category-new-house.php
*/
get_header('77');
?>



    <?php
    // カテゴリーページでビュー切り替えボタンを表示 金額を入れないと記事一欄に表示されない。金額フィルター条件があるから。
    display_view_toggle_buttons();
    ?>


<div id="main">

    <?php
    // 現在の価格範囲をGETパラメータから取得
    $currentMin = isset($_GET['min_price']) ? (int)$_GET['min_price'] : 100;  // デフォルト最小価格
    $currentMax = isset($_GET['max_price']) ? (int)$_GET['max_price'] : 2000;  // デフォルト最大価格

    // 内外土地カテゴリーの投稿を取得するクエリ
    $new_house_query = new WP_Query(array(
        'category_name' => 'new-house',  // 内外土地カテゴリーのスラッグ
        'posts_per_page' => 10,  // 1ページあたりの投稿数
        'paged' => get_query_var('paged') ? get_query_var('paged') : 1,  // ページネーションの処理
        'meta_query' => array(
            array(
                'key' => 'Price',  // 価格情報が格納されているカスタムフィールド
                'value' => array($currentMin, $currentMax),
                'compare' => 'BETWEEN',
                'type' => 'NUMERIC',
            ),
        ),
    ));

    // ループ
    if ($new_house_query->have_posts()) : 
        while ($new_house_query->have_posts()) : $new_house_query->the_post();
    ?>
<div id="post-<?php the_ID(); ?>" <?php post_class('main-custom-post'); ?>>

                                <!-- Post Meta -->
                                <div class="post-meta">
                                    <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
                                        <h1><?php the_title(); ?></h1>
                                    </a>
                                    <p>
                                        <svg class="icon icon-price-tags">
                                            <use href="#icon-price-tags"></use>
                                            <span class="category">
                                                <?php
                                                    // 投稿のカテゴリーを取得
                                                    $categories = get_the_category();
                                                    // カテゴリー名ごとに色を設定
                                                    foreach ($categories as $category) {
                                                        // カテゴリーのIDを使って色を取得
                                                        $color = get_term_meta($category->term_id, 'category_color', true);
                                                        // 色が設定されていない場合はデフォルト色（青）を設定
                                                        $color = $color ? $color : '#007BFF';
                                                        // カテゴリー名をリンクとともに表示
                                                        echo '<a href="' . esc_url(get_category_link($category->term_id)) . '" style="color:' . esc_attr($color) . '; text-decoration: none;" class="category-name">' . esc_html($category->name) . '</a>, ';
                                                    }
                                                ?>
                                            </span>
                                        </svg>
                                        <span class="sidebar-comment-num">
                                            <?php comments_popup_link('<i class="far fa-comments"></i> : 0', '<i class="far fa-comments"></i> : 1', '<i class="far fa-comments"></i> : %'); ?>
                                        </span>
                                    </p>
                                </div>

                                <!-- Thumbnail Box -->
                                <div class="blog-thumbnail-box">
                                    <a href="<?php the_permalink(); ?>" title="「<?php the_title(); ?>」Read">
                                        <!-- NEWマークを表示 -->
                                        <?php
                                            $days = 14;  // NEWマークを表示する日数
                                            $now = time();  // 現在のUNIXタイムスタンプ
                                            $entry = get_the_time('U');  // 投稿日時のUNIXタイムスタンプ
                                            $term = ($now - $entry) / 86400;  // 投稿からの経過日数
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
                // 物件情報テーブルを表示
                if (file_exists(get_template_directory() . '/property-info-table.php')) {
                    get_template_part('property-info-table'); // 物件情報テーブルの読み込み
                } else {
                    echo '<p>物件情報がありません</p>';
                }
                ?>
                <!-- 投稿の抜粋を表示 -->
                <p class="custom-excerpt"><?php echo dess_get_excerpt(80); ?></p>
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



        </div><!-- end post -->

    <?php 
        endwhile;
    else :
        echo '<p>投稿が見つかりませんでした。</p>';
    endif;

    // 投稿データのリセット
    wp_reset_postdata();
    ?>

    <div class="blog-pagination">
        <?php
        echo paginate_links(array(
            'base' => str_replace(999999999, '%#%', esc_url(get_pagenum_link(999999999))),
            'format' => '?paged=%#%', // ページ番号を ?paged=1 のように設定
            'current' => max(1, get_query_var('paged')), // 現在のページ番号
            'total' => $new_house_query->max_num_pages, // 最大ページ数
            'prev_text' => '&#8592;', // 前のページリンク
            'next_text' => '&#8594;', // 次のページリンク
        ));
        ?>
    </div>

</div><!-- main id end -->

<!-- サイドバー -->
<div id="single">
    <?php get_template_part('sidebar-land'); // 不動産関連のサイドバー ?>
</div>
<!-- サイドバー終了 -->

<?php //get_template_part('templates/store-reservation-modal'); ?>


<?php get_footer(); ?>
