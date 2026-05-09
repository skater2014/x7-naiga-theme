<?php
/*
 * Template Name: Single House
 * Template Post Type: house
 */
?>

<?php get_header('77'); ?>

<!-- ぱんくずリストの追加 -->
<div id="breadcrumbs">
    <?php //breadcrumb2(); // カスタム投稿用のパンくず読み込み
    ?>
</div>

<div id="main">

    <?php if (have_posts()) :
        /** WordPress ループ（メインループ） */
        while (have_posts()) : the_post();
            /** 繰り返し処理開始 */ ?>

            <div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <?php
                // ここでアーカイブページへのリンクを直接指定
                // "archive-house.php" に戻るリンクを作成
                $archive_link = home_url('/house/');
                ?>

                <!-- 戻るリンクを追加 -->
                <div class="back-link">
                    <a href="<?php echo esc_url(home_url('house')); ?>">
                        <svg class="icon icon-arrow-left2">
                            <use xlink:href="#icon-arrow-left2"></use>
                        </svg>
                        <span>前のページに戻る</span>
                    </a>
                </div>


                <h1 class="post-title"><?php the_title(); ?></h1>
                <p class="post-meta">
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
                </p>

                <?php
                $is_land = false; // houseは全部「物件」扱い固定

                // H2文言を出し分け（土地だけ文言を変える） ← これを残すなら land 分岐は常に false
                if ($has_image && $has_pano) {
                    $tab_h2 = '物件写真・360°パノラマ';
                } elseif ($has_image) {
                    $tab_h2 = '物件写真一覧（外観・内観・設備）';
                } else {
                    $tab_h2 = '360°パノラマ（室内）';
                }
                ?>


                <?php
                ob_start();
                get_template_part('single-template-parts/image-tab');
                $image_html = trim(ob_get_clean());

                ob_start();
                get_template_part('single-template-parts/panorama-tab');
                $panorama_html = trim(ob_get_clean());

                // どっちも無ければ何も出さない
                if ($image_html === '' && $panorama_html === '') {
                    // single本体なら return は避けて「何もしない」でOK
                } else {
                    $active = ($image_html !== '') ? 'image-tab' : 'panorama-tab';
                ?>
                    <ul class="tab-links">
                        <?php if ($image_html !== ''): ?>
                            <li class="tab-link <?php echo ($active === 'image-tab') ? 'active' : ''; ?>" data-tab="image-tab">画像ビュー</li>
                        <?php endif; ?>

                        <?php if ($panorama_html !== ''): ?>
                            <li class="tab-link <?php echo ($active === 'panorama-tab') ? 'active' : ''; ?>" data-tab="panorama-tab">360°パノラマビュー</li>
                        <?php endif; ?>
                    </ul>

                <?php
                    echo $image_html;
                    echo $panorama_html;
                }
                ?>

                <?php
                // display_custom_fields_table_1 が存在するか確認
                if (function_exists('display_custom_fields_table_1')) {
                    display_custom_fields_table_1($post);
                } else {
                    echo '<p>display_custom_fields_table_1 関数が見つかりません。</p>';
                }

                // タグ
                if (!is_mobile_device()) :
                    get_template_part('template-sidebar-parts/tekken7-tag-cloud');
                endif;

                // Google Map iframe 1 のカスタムフィールドを取得
                $google_map_iframe_1 = get_post_meta(get_the_ID(), '_google_map_iframe_1', true);

                // iframe が存在すれば表示
                if ($google_map_iframe_1) :
                    echo '<h2>所在地</h2>';
                    echo '<div class="google-map-iframe">';
                    echo $google_map_iframe_1;
                    echo '</div>';
                endif;

                // display_custom_fields_table_2 が存在する場合に表示
                if (function_exists('display_custom_fields_table_2')) {
                    display_custom_fields_table_2($post);
                }

                // Google Map iframe 2 のカスタムフィールドを取得
                $google_map_iframe_2 = get_post_meta(get_the_ID(), '_google_map_iframe_2', true);

                // iframe が存在すれば表示
                if ($google_map_iframe_2) :
                    echo '<h2>所在地</h2>';
                    echo '<div class="google-map-iframe">';
                    echo $google_map_iframe_2;
                    echo '</div>';
                endif;

                // display_custom_fields_table_3 が存在する場合に表示
                if (function_exists('display_custom_fields_table_3')) {
                    display_custom_fields_table_3($post);
                }
                ?>

                <h2 class="section-title">内見・イベント</h2>
                <div id="calendar"></div>

                <div id="naigai-property-list" class="naigai-property-list"></div>

                <!--new pager-->
                <?php require get_template_directory() . '/pager-post-navigation.php'; ?>
                <!--end new pager-->

                <p class="footer-post-meta">
                    <?php the_tags('Tag : ', ', '); ?>
                    <span class="post-author">Author : <a href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>"><?php the_author(); ?></a></span>
                </p>
            </div>

            <?php
            /** ここからカスタム投稿用の関連記事の表示 */
            /** カテゴリーIDの取得 */

            if (get_the_terms($post->ID, 'house')) : // カスタムタクソノミー名

                $terms = get_the_terms($post->ID, 'house'); // タクソノミーのタームを取得
                $term_slugs = wp_list_pluck($terms, 'slug'); // タームのスラッグを取得

                $args = array(
                    'post_type'         => 'house',
                    'post__not_in'      => array($post->ID),
                    'orderby'           => 'rand',
                    'posts_per_page'    => 6,
                    'tax_query'         => array(
                        'relation'      => 'OR',
                        array(
                            'taxonomy'  => 'house-type', // カスタムタクソノミーのスラッグ
                            'field'     => 'slug',
                            'terms'     => get_my_terms_array('house-type'),
                        ),
                        array(
                            'taxonomy'  => 'region', // カスタムタクソノミーのスラッグ
                            'field'     => 'slug',
                            'terms'     => get_my_terms_array('region'),
                        )
                    )
                );
                $related = new WP_Query($args);
            ?>

                <div class="related-posts">
                    <div class="side-title">関連する物件詳細の記事</div>

                    <?php if ($related->have_posts()) : ?>
                        <ul id="related-posts">
                            <?php while ($related->have_posts()) : $related->the_post(); ?>
                                <li <?php post_class('main-custom-post custom-post'); ?>>
                                    <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>

                                    <p class="post-meta">
                                        <svg class="icon icon-price-tags">
                                            <use xlink:href="#icon-price-tags">
                                                <span class="category">
                                                    <?php
                                                    $terms_new_character = get_the_term_list($post->ID, 'house-type', '', ', ', '');
                                                    $terms_news = get_the_term_list($post->ID, 'region', '', ', ', '');

                                                    echo $terms_new_character;

                                                    if ($terms_new_character && $terms_news) {
                                                        echo ', ';
                                                    }

                                                    echo $terms_news;
                                                    ?>
                                                </span>
                                            </use>
                                        </svg>

                                        <span class="sidebar-comment-num">
                                            <?php comments_popup_link('<i class="far fa-comments"></i> : 0', '<i class="far fa-comments"></i> : 1', '<i class="far fa-comments"></i> : %'); ?>
                                        </span>
                                    </p>
                                    <!-- Thumbnail Box -->
                                    <div class="blog-thumbnail-box1">
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
                                        // 動画タイプを確認
                                        $type = get_post_meta($post->ID, 'page_featured_type', true);
                                        $video_id = get_post_meta($post->ID, 'page_video_id', true); // 動画IDを取得

                                        if ($type == 'youtube' && !empty($video_id)) {
                                            // YouTube動画の場合
                                            echo '<div class="blog-post-image youtube" style="aspect-ratio: 16 / 9;">';  // アスペクト比を設定
                                            echo '<lite-youtube videoid="' . esc_attr($video_id) . '" playlabel="Play: ' . esc_html(get_the_title()) . '" style="max-width:100%; height:auto;"></lite-youtube>';
                                        } elseif ($type == 'vimeo' && !empty($video_id)) {
                                            // Vimeo動画の場合
                                            echo '<div class="blog-post-image vimeo" style="aspect-ratio: 16 / 9;">';  // アスペクト比を設定
                                            echo '<lite-vimeo videoid="' . esc_attr($video_id) . '">
                                            <div class="ltv-playbtn"></div>
                                            </lite-vimeo>';
                                        } else {
                                            // 動画がない場合はサムネイル画像を表示
                                            if (has_post_thumbnail()) :
                                                $blog_thumbnail = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), array(600, 600));
                                                echo '<div class="blog-post-image" style="background-image:url(' . esc_url($blog_thumbnail[0]) . '); aspect-ratio: 16 / 9;">';  // アスペクト比を設定
                                            else :
                                                // サムネイルがない場合、「noimage」を表示
                                                $noimage = get_template_directory_uri() . '/images/noimage.gif';
                                                echo '<div class="blog-post-image" style="background-image:url(' . esc_url($noimage) . '); aspect-ratio: 16 / 9;">';  // アスペクト比を設定
                                            endif;
                                        }
                                        ?>

                                        <!-- タイトル -->
                                        <?php if ($type !== 'youtube' && $type !== 'vimeo') : ?>
                                            <div class="thumbnail-overlay">
                                                <h3><?php echo esc_html(get_the_title()); ?></h3>
                                            </div>
                                        <?php endif; ?>

                                    </div> <!-- End blog-post-image -->

                                    <!-- ハートアイコン（いいね） -->
                                    <div class="heart-icon <?php echo get_post_meta(get_the_ID(), 'liked', true) ? 'active' : ''; ?>"
                                        data-post-id="<?php echo esc_attr(get_the_ID()); ?>">
                                        <svg class="icon icon-heart" aria-hidden="true" focusable="false">
                                            <use xlink:href="#icon-heart"></use>
                                        </svg>
                                    </div>
                </div> <!-- End blog-thumbnail-box -->

                <!-- Content Box -->
                <div class="content-box">
                    <?php get_template_part('property-info-table'); ?>
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
                </li>
            <?php endwhile; ?>
            </ul>
        <?php endif; ?>
</div> <!-- ここで関連記事のループを終了 -->

<?php endif; ?>

<?php endwhile; ?>
<?php endif; ?>

<ul class="custom-tab-links">
    <li class="custom-tab-link active" data-tab="reservation">来店予約フォーム</li>
    <li class="custom-tab-link" data-tab="comments">口コミレビュー</li>
</ul>

<div class="custom-tab-content">
    <!-- 予約フォームのタブ -->
    <div id="tab-reservation" class="custom-tab-pane">
        <?php
        // 現在の投稿IDを取得
        $post_id = get_the_ID();
        // 予約フォームを呼び出し、post_idを渡す
        get_template_part('reservation-form-content', null, array('post_id' => $post_id));
        ?>
    </div>

    <!-- コメントフォームのタブ -->
    <div id="tab-comments" class="custom-tab-pane" style="display: none;">
        <?php
        // コメントフォームを常に表示
        if (comments_open()) {
            comments_template(); // comment-form.php も含めて、コメントフォームとリストを表示
        }
        ?>
    </div>
</div>

</div> <!-- /#main -->

<!-- サイドバー -->
<div id="single">
    <?php get_template_part('sidebar-house'); ?>
</div>




<?php get_footer(); ?>