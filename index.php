<?php
get_header('77');  // ヘッダーを表示

// ページネーション用の変数
$paged = get_query_var('paged') ? get_query_var('paged') : 1;

// 現在の価格範囲をGETパラメータから取得
$currentMin = isset($_GET['min_price']) ? (int)$_GET['min_price'] : 300;
$currentMax = isset($_GET['max_price']) ? (int)$_GET['max_price'] : 4000;

// 現在のカテゴリIDを取得（親カテゴリーとその子カテゴリーを設定）
$cat_id = get_queried_object_id();

// 親カテゴリIDを配列に追加
$category_ids = array($cat_id);

// 子カテゴリーを取得してIDに追加
$category_args = array(
    'child_of' => $cat_id
);
$categories = get_categories($category_args);

foreach ($categories as $category) {
    $category_ids[] = $category->term_id;
}

// 投稿のクエリ設定
$args = array(
    'posts_per_page' => 10,           
    'paged' => $paged,                
);

// タグページの場合、タグでフィルタリング
if (is_tag()) {
    $tag = get_queried_object();
    if ($tag) {
        $args['tax_query'][] = array(
            'taxonomy' => 'post_tag',      
            'field'    => 'slug',          
            'terms'    => $tag->slug,      
            'operator' => 'IN',            
        );
    }
}

// カテゴリーページでタグに関連する投稿を表示
if (is_category()) {
    $category_name = get_queried_object()->slug;
    $args['tax_query'][] = array(
        'taxonomy' => 'category',            
        'field'    => 'slug',                
        'terms'    => $category_name,       
        'operator' => 'IN',                   
    );
}


// クエリ実行
$query = new WP_Query($args);
?>




<?php
// カテゴリーページでビュー切り替えボタンを表示
display_view_toggle_buttons();  // この関数が定義されていない場合は削除または定義を追加
?>

<!-- ここに Main ID コンテンツ -->
<div id="main">

<!-- WP query loop starts -->
<?php if ($query->have_posts()) : ?>
    <?php while ($query->have_posts()) : $query->the_post(); ?>

    <div id="post-<?php the_ID(); ?>" <?php post_class('main-custom-post'); ?>> 

        <!-- Post Meta -->
        <div class="post-meta">
            <a href="<?php the_permalink(); ?>">
                <h2><?php the_title(); ?></h2>
            </a>
            <p>
                <svg class="icon icon-price-tags">
                    <use xlink:href="#icon-price-tags"></use>
                    <span class="category">
                        <?php
                            // 投稿のカテゴリーを取得
                            $categories = get_the_category();
                            // カテゴリー名ごとに色を設定
                            foreach ($categories as $category) {
                                // カテゴリーのIDを使って色を取得
                                $color = get_term_meta($category->term_id, 'category_color', true);
                                // 色が設定されていない場合はデフォルト色 (#007BFF) を設定
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
                        echo '<div class="box new-post">';
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

    </div>
    <!--end post--> 

    <?php endwhile; ?>

<div class="blog-pagination">
    <?php
    // ページネーション用の大きな数字
    $big = 999999999; 

    // ページネーションリンクの生成
    echo paginate_links(array(
        'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
        'format' => '?paged=%#%',
        'current' => max(1, get_query_var('paged', 1)),  // 現在のページ番号を取得
        'total' => $query->max_num_pages, // 総ページ数
        'prev_text' => '« 前へ',  // 前のページへのリンク
        'next_text' => '次へ »',  // 次のページへのリンク
    ));
    ?>
</div>


<?php else : ?>

    <!-- 記事が見つからなかった場合のメッセージ -->
    <div class="no-posts">
        <h2>お探しのページは見つかりませんでした。</h2>
        <p>現在表示できる記事はありません。他のページをお試しください。</p>
        <p><a href="<?php echo esc_url(home_url()); ?>">ホームページに戻る</a></p>
    </div>

<?php endif; ?>

</div><!-- main id end -->

<?php wp_reset_postdata(); // グローバルな$postデータをリセット ?>

<!-- サイドバー -->
<div id="single">
<?php
// カテゴリーによってサイドバーの表示を変更
if (in_category('naigai-tochi')) :
    get_template_part('sidebar-land');
elseif (in_category('naigai-construction')) :
    get_template_part('sidebar-house');
else :
    get_template_part('sidebar');
endif;
?>
</div><!-- single id end -->

<?php get_footer(); ?>
