<?php
$counter = 0; // カウンターの初期化
$args = array(
    'post_type' => 'post', // 投稿タイプは通常の投稿
    'posts_per_page' => 5, // 最大5件の投稿を取得
    'category_name' => 'naigai-construction', // カテゴリー名は 'naigai-construction'
);

$my_query = new WP_Query($args); // WP_Queryを使って投稿を取得

if ($my_query->have_posts()) : // 投稿がある場合
    global $post;
    ?>
    <ul class="sidebar-posts sidebar-recent-posts">
        <?php while ($my_query->have_posts()) : $my_query->the_post();
            $counter++; // 投稿ごとにカウンターをインクリメント

            // 新着投稿のリボン表示処理
            $days = 14; // 新着投稿として表示する期間（14日間）
            $now = current_time('U'); // 現在時刻をUNIXタイムスタンプで取得
            $entry = get_the_time('U'); // 投稿の公開日をUNIXタイムスタンプで取得
            $term = ($now - $entry) / 86400; // 投稿からの経過日数を計算
            $is_new = $days > $term; // 新着かどうかの判定（14日以内の投稿は新着）

            // サムネイル画像のURL取得
            $thumbnail_url = get_the_post_thumbnail_url($post->ID, 'full');
            if (!$thumbnail_url) {
                // サムネイルが設定されていない場合はデフォルト画像を設定
                $thumbnail_url = get_template_directory_uri() . '/images/noimage.gif';
            }

            // タイトルの抜粋（最大40文字）
            $title_excerpt = mb_substr(get_the_title(), 0, 40);

            // タイトルが40文字を超えていたら「..」を追加
            if (mb_strlen(get_the_title()) > 40) {
                $title_excerpt .= '...';
            }

            // 最初の記事にはコンテンツの抜粋（最大20文字）を表示
            if ($counter === 1) {
                $content_excerpt = mb_substr(get_the_excerpt(), 0, 20);
                if (mb_strlen(get_the_excerpt()) > 20) {
                    $content_excerpt .= '...';
                }
            }

            // デバッグ用にサムネイルURLを出力
            echo '<!-- サムネイルURL: ' . esc_url($thumbnail_url) . ' -->'; // デバッグ用
            ?>
            <li id="post-<?php the_ID(); ?>" class="custom-post <?php echo $counter === 1 ? 'first-post big' : 'small'; ?>">
                <?php if ($counter === 1) : ?>
                    <!-- 1番目の投稿：大きな背景画像 -->
                  <div class="recent-thumbnail-box-big-box" style="background-image:url('<?php echo esc_url($thumbnail_url); ?>'); background-size: cover; background-position: center center; background-repeat: no-repeat;">
                        <?php if ($is_new) : ?>

                            <div class="ribbon ribbon-top-left"><span>New</span></div>
                        <?php endif; ?>
                        <div class="heart-icon" data-post-id="<?php echo get_the_ID(); ?>">
                            <svg class="icon icon-heart <?php echo get_post_meta(get_the_ID(), 'liked', true) ? 'liked' : ''; ?>" data-post-id="<?php echo get_the_ID(); ?>">
                                <use xlink:href="#icon-heart"></use>
                            </svg>
                        </div>
                        <div class="recent-title-overlay">
                            <h3>
                                <a href="<?php the_permalink(); ?>"><?php echo esc_html($title_excerpt); ?></a>
                            </h3>
                        </div>
                    </div>
                    <div class="recent-big-content-box">
                        <h3>
                            <a href="<?php the_permalink(); ?>"><?php echo esc_html($title_excerpt); ?></a>
                        </h3>
                        <div class="post-meta">
                            <svg class="icon icon-price-tags">
                                <use href="#icon-price-tags"></use>
                            </svg>
                            <span class="post-category category"><?php the_category(', '); ?></span>
                            <span class="sidebar-comment-num">
                                <a href="<?php the_permalink(); ?>#respond"><i class="far fa-comments"></i> : <?php echo get_comments_number(); ?></a>
                            </span>
                        </div>
                        <div class="post-excerpt">
                            <p><?php echo wp_trim_words(get_the_content(), 20, '...'); ?></p>
                        </div>
                        <div class="more-link-wrapper">
                            <a href="<?php the_permalink(); ?>" title="Read more" class="more-link">この物件の詳細を見る &raquo;</a>
                        </div>
                    </div>
                <?php else : ?>
                    <!-- 2番目以降の投稿：小さな背景画像 -->
                    <div class="recent-thumbnail-small-box" style="background-image:url('<?php echo esc_url($thumbnail_url); ?>'); background-size: cover; background-position: center center; background-repeat: no-repeat;">
                        <?php if ($is_new) : ?>

                            <div class="ribbon ribbon-top-left"><span>New</span></div>
                        <?php endif; ?>
                        <div class="heart-icon" data-post-id="<?php echo get_the_ID(); ?>">
                            <svg class="icon icon-heart <?php echo get_post_meta(get_the_ID(), 'liked', true) ? 'liked' : ''; ?>" data-post-id="<?php echo get_the_ID(); ?>">
                                <use xlink:href="#icon-heart"></use>
                            </svg>
                        </div>
                        <div class="recent-title-overlay">
                            <h3>
                                <a href="<?php the_permalink(); ?>"><?php echo esc_html($title_excerpt); ?></a>
                            </h3>
                        </div>
                    </div>
                    <div class="recent-content-small-box">
                        <h3>
                            <a href="<?php the_permalink(); ?>"><?php echo esc_html($title_excerpt); ?></a>
                        </h3>
                        <div class="more-link-wrapper">
                            <a href="<?php the_permalink(); ?>" title="Read more" class="more-link2">詳細を見る &raquo;</a>
                        </div>
                    </div>
                <?php endif; ?>
            </li>
        <?php endwhile; ?>
    </ul>
    <?php
    wp_reset_postdata(); // クエリをリセット
else : ?>
    <p>現在、表示できる投稿がありません。</p>
<?php endif; ?>
