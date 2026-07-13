<?php
/*
 * Template Name: Single Recruitment
 * Template Post Type: recruitment
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
                $archive_link = home_url('/recruitment/');
                ?>

                <!-- 戻るリンクを追加 -->
                <div class="back-link">
                    <a href="<?php echo esc_url(home_url('recruitment')); ?>">
                        <svg class="icon icon-arrow-left2">
                            <use xlink:href="#icon-arrow-left2"></use>
                        </svg>
                        <span>前のページに戻る</span>
                    </a>
                </div>


                <h1 class="entry-title">
                    <a href="<?php the_permalink(); ?>">
                        <?php the_title(); ?>
                    </a>
                </h1>

                <p class="post-meta">
                    <span class="meta-item meta-category">
                        <svg class="icon icon-price-tags" aria-hidden="true" focusable="false">
                            <use xlink:href="#icon-price-tags"></use>
                        </svg>

                        <span class="category">
                            <?php echo do_shortcode('[category_taxonomy_links]'); ?>
                        </span>
                    </span>

                    <span class="meta-item sidebar-comment-num">
                        <?php comments_popup_link(
                            '<i class="far fa-comments"></i> : 0',
                            '<i class="far fa-comments"></i> : 1',
                            '<i class="far fa-comments"></i> : %'
                        ); ?>
                    </span>
                </p>

                <?php
                ob_start();
                get_template_part('single-template-parts/image-tab');
                $image_tab_content = trim(ob_get_clean());

                ob_start();
                get_template_part('single-template-parts/panorama-tab');
                $panorama_tab_content = trim(ob_get_clean());

                // 中身があるかどうかで判定（←これが重要）
                $has_image = ($image_tab_content !== '');
                $has_pano  = ($panorama_tab_content !== '');

                if ($has_image || $has_pano) :

                    // 初期アクティブ
                    $active_tab = $has_image ? 'image-tab' : 'panorama-tab';
                ?>
                    <ul class="tab-links">
                        <?php if ($has_image): ?>
                            <li class="tab-link <?php echo ($active_tab === 'image-tab') ? 'active' : ''; ?>" data-tab="image-tab">
                                画像ビュー
                            </li>
                        <?php endif; ?>

                        <?php if ($has_pano): ?>
                            <li class="tab-link <?php echo ($active_tab === 'panorama-tab') ? 'active' : ''; ?>" data-tab="panorama-tab">
                                360°パノラマビュー
                            </li>
                        <?php endif; ?>
                    </ul>

                    <div id="tab-content">
                        <?php if ($has_image) echo $image_tab_content; ?>
                        <?php if ($has_pano)  echo $panorama_tab_content; ?>
                    </div>

                <?php endif; ?>



                <?php
                // display_custom_fields_table_0 が存在するか確認
                if (function_exists('display_recruitment_fields_table_0')) {
                    display_recruitment_fields_table_0();  // 引数なしで呼び出し
                } else {
                    echo '<p>display_recruitment_fields_table_0 関数が見つかりません。</p>';
                }

                $content = get_the_content(); // 投稿のコンテンツを取得

                if (!empty($content)) : ?>
                    <?php the_content(); ?>
                <?php else : ?>
                    <p>コンテンツはまだありません。</p>
                <?php endif; ?>

                <div id="tab-content">
                    <?php
                    // 画像タブのファイルを読み込む
                    get_template_part('single-template-parts/image-tab');

                    // パノラマタブのファイルを読み込む
                    get_template_part('single-template-parts/panorama-tab');
                    ?>
                </div>

                <?php
                // display_custom_fields_table_1 が存在するか確認
                if (function_exists('display_recruitment_fields_table_1')) {
                    display_recruitment_fields_table_1();  // 引数なしで呼び出し
                } else {
                    echo '<p>display_recruitment_fields_table_1 関数が見つかりません。</p>';
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

                <div id="calendar"></div>

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

            if (get_the_terms($post->ID, 'recruitment_category')) : // カスタムタクソノミー名

                $terms = get_the_terms($post->ID, 'recruitment_category'); // タクソノミーのタームを取得
                $term_slugs = wp_list_pluck($terms, 'slug'); // タームのスラッグを取得

                $args = array(
                    'post_type'         => 'recruitment',  // カスタム投稿タイプのスラッグ
                    'post__not_in'      => array($post->ID),
                    'orderby'           => 'rand',
                    'posts_per_page'    => 6,
                    'tax_query'         => array(
                        'relation'      => 'OR',
                        array(
                            'taxonomy'  => 'recruitment_category', // カスタムタクソノミーのスラッグ
                            'field'     => 'slug',
                            'terms'     => $term_slugs,  // 取得したタームのスラッグを使う
                        ),
                        array(
                            'taxonomy'  => 'recruitment_tag', // カスタムタクソノミーのスラッグ
                            'field'     => 'slug',
                            'terms'     => get_my_terms_array('recruitment_tag'), // 追加したタグタクソノミーの取得
                        )
                    )
                );
                $related = new WP_Query($args);
            ?>

                <div class="related-posts">
                    <div class="side-title">関連する採用詳細記事</div>

                    <?php if ($related->have_posts()) : ?>
                        <ul id="related-posts">
                            <?php while ($related->have_posts()) : $related->the_post(); ?>
                                <li <?php post_class('clearfix custom-post'); ?>>
                                    <div class="post-meta">
                                        <h2 class="related-post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>

                                        <p class="post-meta-row">
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
                                    </div>

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
                                    <div class="heart-icon" data-post-id="<?php echo get_the_ID(); ?>">
                                        <svg class="icon icon-heart <?php echo get_post_meta(get_the_ID(), 'liked', true) ? 'liked' : ''; ?>" data-post-id="<?php echo get_the_ID(); ?>">
                                            <use xlink:href="#icon-heart"></use>
                                        </svg>
                                    </div>
                </div> <!-- End blog-thumbnail-box -->

                <!-- Content Box (Property Info Table) -->
                <div class="content-box1">
                    <?php get_template_part('recruit-info-table'); ?>
                </div>
                <!-- More Link -->
                <div class="more-link-wrapper">
                    <a href="<?php the_permalink(); ?>" title="Read more" class="more-link">この採用の詳細を見る &raquo;</a>
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

<!-- ここからタブの内容を追加 -->
<div class="custom-tab-links">
    <ul class="custom-tab-links">
        <li class="custom-tab-link active" data-tab="reservation">来店予約フォーム</li>
        <li class="custom-tab-link" data-tab="comments">口コミレビュー</li>
    </ul>
</div>

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

<!-- タブの内容の追加がここまで -->
</div> <!-- /#main -->
<!-- サイドバー -->
<div id="single">
    <?php get_template_part('sidebar-house'); ?>
</div>




<?php get_footer(); ?>