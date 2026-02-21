<div class="artifacts-post">
    <div class="side-title">内外建設㈱</div>

    <?php
    // 現在のカテゴリースラッグを取得（アーカイブページとカテゴリーページに対応）
    $category_name = '';  // $category_name 変数に空の値をセットして初期化

    // カテゴリーページの場合
    if (is_category()) {
        $category_name = get_queried_object()->slug; // 現在のカテゴリのスラッグ（名前）を取得
        // is_category() はカテゴリーページが表示されているかどうかを判定する WordPress の条件タグです。
    // アーカイブページの場合
    } elseif (is_archive()) { 
        // is_archive() は、アーカイブページ（カテゴリ、タグ、日付、著者などのアーカイブページ）であるかどうかを判定する条件タグです。
        $category_name = 'naigai-construction'; // アーカイブページの場合に表示するカテゴリーを指定（'naigai-construction' というカテゴリースラッグを指定）
        // ここで指定しているカテゴリースラッグは、アーカイブページで表示したいデフォルトのカテゴリ名です。
    }

    // クエリの設定
    $args = array(
        'post_type'      => 'post',
        'posts_per_page' => 4,
        'paged'          => (get_query_var('paged') ? get_query_var('paged') : 1),
        'category_name'  => $category_name  // 現在のカテゴリに基づいて表示
    );
    $blog = new WP_Query($args);
    ?>
    <div class="post_content">
        <?php if ($blog->have_posts()) : ?>
            <div class="blog">
                <div class="blog-posts">
                    <?php
                    while ($blog->have_posts()) : $blog->the_post();
                        ?>
                        <div class="featured-box1">
                            <div class="port-image">
                                <?php
                                $type = get_post_meta($post->ID, 'page_featured_type', true);
                                switch ($type) {
                                    case 'youtube':
                                        echo '<lite-youtube videoid="' . get_post_meta(get_the_ID(), 'page_video_id', true) . '" playlabel="Play: Keynote (Google I/O \'18)" style="max-width:100%; height:auto;"></lite-youtube>';
                                        break;
                                    case 'vimeo':
                                        echo '<lite-vimeo videoid="' . get_post_meta(get_the_ID(), 'page_video_id', true) . '">
                                                <div class="ltv-playbtn"></div>
                                              </lite-vimeo>';
                                        break;
                                    default:
                                        // サムネイル画像の取得
                                        $thumbnail = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), array(600, 600));
                                        
                                        // サムネイルがない場合のデフォルト画像設定
                                        if (!$thumbnail) {
                                            $thumbnail = array(get_template_directory_uri() . '/images/noimage.gif'); // デフォルト画像（noimage.gif）
                                        }

                                        // サムネイル画像を背景として設定
                                        echo '<a href="' . get_permalink() . '" style="background-image: url(' . $thumbnail[0] . ')"></a>';
                                        break;
                                }
                                ?>

                                <!-- ハートアイコン（いいね） -->
                                <div class="heart-icon" data-post-id="<?php echo get_the_ID(); ?>">
                                    <svg class="icon icon-heart <?php echo get_post_meta(get_the_ID(), 'liked', true) ? 'liked' : ''; ?>" data-post-id="<?php echo get_the_ID(); ?>">
                                        <use xlink:href="#icon-heart"></use>
                                    </svg>
                                </div>
                            </div>
                            <div class="port-body">
                                <!--<p class="port-date">
                                    <?php //the_time('F d, Y'); ?>
                                </p>-->
                                <h3>
                                    <a href="<?php the_permalink(); ?>">
                                        <?php echo the_title(); ?>
                                    </a>
                                </h3>
                            </div>
                        </div>
                    <?php
                    endwhile;
                    ?>
                </div>
            </div>
        <?php else : echo '<p>' . __('Sorry, no blog posts found. Please create a post and assign it in "blog" category.', 'creator') . '</p>';
        endif; ?>
    </div>
</div>
