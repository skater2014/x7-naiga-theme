<?php

/****************************************

  archive-house.php
  アーカイブページを作成する際には、通常、index.php をコピー、カスタム投稿のスラッグに基づいたファイル名に変更するのが一般的です。
  例えば、カスタム投稿タイプが "genshin_update" の場合、archive-genshin_updated.php という名前のファイルを作成すると、
  WordPressはそのファイルを "genshin_update" カスタム投稿タイプのアーカイブページとして認識します。
　作成したファイル内で、アーカイブの表示方法をカスタマイズできます。これには、ループや表示するコンテンツの制御、ぱんくずリストの追加などが含まれます。先ほど提供した archive-genshin_update.php の例もその一例です。
　アーカイブページを作成したら、WordPressはカスタム投稿タイプのアーカイブが存在する場合はそれを優先して表示します。存在しない場合は、通常の投稿やページのアーカイブが表示されます。
　カスタム投稿やタクソノミーを作成するのにパーマリンクの更新を行う。
アーカイブのページレイアウトに合わせて通常のカテゴリー記事一欄を表示してある。
検索フォームからの検索語句も「post」「house」のページ情報から表示。

 *****************************************/
get_header('77');

/*
 * ============================================================
 * 一覧階層の「前のページに戻る」
 * ============================================================
 *
 * このテンプレートには従来back linkが無かったため、
 * 共通部品をここで読み込む。
 *
 * back link本体の判定やHTMLを各ファイルへ複製せず、
 * template-parts/common/internal-back-link.php を正本にする。
 *
 * 一覧・検索・ページネーション等の既存処理は変更しない。
 */
get_template_part(
    'template-parts/common/internal-back-link'
);

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

    // クエリの設定
    $args = array(
        'post_type' => array('house'), // 通常投稿とカスタム投稿タイプ 'house'
        'posts_per_page' => 10, // 1ページあたりの投稿数
        'paged' => get_query_var('paged') ? get_query_var('paged') : 1, // ページネーション
        'meta_query' => array(
            'relation' => 'OR', // OR条件で価格範囲内または0円を取得
            // 価格が範囲内の投稿
            array(
                'key' => 'Price', // 価格情報が格納されているカスタムフィールド
                'value' => array($currentMin, $currentMax),
                'compare' => 'BETWEEN',
                'type' => 'NUMERIC',
            ),
            // 価格が0円の投稿（売却済み）
            array(
                'key' => 'Price', // 価格情報が格納されているカスタムフィールド
                'value' => '0',
                'compare' => '=', // 価格が0の投稿を取得
                'type' => 'NUMERIC', // 数値として比較
            ),
        ),
    );

    // カスタムクエリの実行
    $custom_query = new WP_Query($args);
    ?>


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

                <!-- Thumbnail Box -->
                <div class="blog-thumbnail-box1">
                    <?php
                    // NEWマークを表示
                    $days = 14;  // NEWマークを表示する日数
                    $now = date_i18n('U');  // 現在時間
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
                    // 動画タイプを確認
                    $type = get_post_meta($post->ID, 'page_featured_type', true);
                    $video_id = get_post_meta($post->ID, 'page_video_id', true); // 動画IDを取得

                    if ($type == 'youtube' && !empty($video_id)) {
                        // YouTube動画の場合
                        echo '<div class="blog-post-image youtube" style="aspect-ratio: 16 / 9;">';  // アスペクト比を設定
                        echo '<lite-youtube videoid="' . esc_attr($video_id) . '" playlabel="Play: ' . esc_html(get_the_title()) . '" style="max-width:100%; height:auto;"></lite-youtube>';
                        echo '</div>';
                    } elseif ($type == 'vimeo' && !empty($video_id)) {
                        // Vimeo動画の場合
                        echo '<div class="blog-post-image vimeo" style="aspect-ratio: 16 / 9;">';  // アスペクト比を設定
                        echo '<lite-vimeo videoid="' . esc_attr($video_id) . '">
                <div class="ltv-playbtn"></div>
            </lite-vimeo>';
                        echo '</div>';
                    } else {
                        // 動画がない場合はサムネイル画像を表示
                        if (has_post_thumbnail()) {
                            $blog_thumbnail = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), array(600, 600));
                            echo '<a href="' . esc_url(get_permalink()) . '" class="thumbnail-link">';
                            echo '<div class="blog-post-image" style="background-image:url(' . esc_url($blog_thumbnail[0]) . '); aspect-ratio: 16 / 9;">';
                            echo '<div class="thumbnail-overlay">';
                            echo '<h3>' . esc_html(get_the_title()) . '</h3>';
                            echo '</div>';
                            echo '</div>';
                            echo '</a>'; // サムネイル画像リンク終了
                        } else {
                            // サムネイルがない場合、「noimage」を表示
                            $noimage = get_template_directory_uri() . '/images/noimage.gif';
                            echo '<a href="' . esc_url(get_permalink()) . '" class="thumbnail-link">';
                            echo '<div class="blog-post-image" style="background-image:url(' . esc_url($noimage) . '); aspect-ratio: 16 / 9;">';
                            echo '<div class="thumbnail-overlay">';
                            echo '<h3>' . esc_html(get_the_title()) . '</h3>';
                            echo '</div>';
                            echo '</div>';
                            echo '</a>'; // noimageリンク終了
                        }
                    }
                    ?>

                    <!-- ハートアイコン（いいね） -->
                    <div class="heart-icon" data-post-id="<?php echo get_the_ID(); ?>">
                        <svg class="icon icon-heart <?php echo get_post_meta(get_the_ID(), 'liked', true) ? 'liked' : ''; ?>" data-post-id="<?php echo get_the_ID(); ?>">
                            <use xlink:href="#icon-heart"></use>
                        </svg>
                    </div>
                </div> <!-- End blog-thumbnail-box -->


                <!-- Content Box (Property Info Table) -->
                <div class="content-box1">
                    <?php
                    // Debugging: Check if template part is loaded
                    //get_template_part('property-info-table');
                    include(locate_template('property-info-table.php'));

                    //var_dump('property-info-table template part loaded');
                    ?>
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
            <!-- end post -->

        <?php endwhile; ?>

        <!-- Pagination -->
        <div class="blog-pagination">
            <?php
            echo paginate_links(array(
                'total' => $custom_query->max_num_pages,
                'prev_text' => '&#8592;',
                'next_text' => '&#8594;',
            ));
            ?>
        </div>

    <?php else : ?>
        <p>物件が見つかりませんでした。</p>
    <?php endif; ?>

</div> <!-- #main -->

<div id="single">
    <?php get_template_part('sidebar-land'); ?>
</div>

<?php get_footer(); ?>