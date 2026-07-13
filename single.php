<?php

/****************************************
    single.php
    個別記事ページ（物件詳細）
 *****************************************/
get_header('77');
?>

<div id="main" class="border-line">

    <?php if (have_posts()) : ?>
        <?php while (have_posts()) : the_post(); ?>

            <div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

                <div class="back-link">
                    <a href="<?php
                                $categories = get_the_category();
                                if (!empty($categories)) {
                                    $category = $categories[0];
                                    if (!empty($category->parent)) {
                                        $parent_category = get_category($category->parent);
                                        echo esc_url(get_category_link($parent_category->term_id));
                                    } else {
                                        echo esc_url(get_category_link($category->term_id));
                                    }
                                }
                                ?>">
                        <svg class="icon icon-arrow-left2">
                            <use xlink:href="#icon-arrow-left2"></use>
                        </svg>
                        <span>前のページに戻る</span>
                    </a>
                </div>

                <h1 class="post-title"><?php the_title(); ?></h1>

                <p class="post-meta">
                    <span class="category">
                        <svg class="icon icon-price-tags" aria-hidden="true" focusable="false">
                            <use href="#icon-price-tags"></use>
                        </svg>
                        <?php echo do_shortcode('[category_taxonomy_links]'); ?>
                    </span>

                    <span class="sidebar-comment-num">
                        <?php
                        $comment_icon = '<i class="far fa-comments"></i>';
                        comments_popup_link("$comment_icon : 0", "$comment_icon : 1", "$comment_icon : %");
                        ?>
                    </span>
                </p>

                <?php
                // =========================
                // タブ（画像 / パノラマ）
                // =========================
                ob_start();
                get_template_part('single-template-parts/image-tab');
                $image_tab_content = ob_get_clean();

                ob_start();
                get_template_part('single-template-parts/panorama-tab');
                $panorama_tab_content = ob_get_clean();

                // 「空」判定は strip_tags までやる（改行/コメントだけで true になる事故防止）
                $has_image = (trim(strip_tags($image_tab_content)) !== '');
                $has_pano  = (trim(strip_tags($panorama_tab_content)) !== '');
                $active_tab = $has_image ? 'image-tab' : ($has_pano ? 'panorama-tab' : '');

                // 土地判定（functions.php に naigai_property_kind() があるならそれ優先）
                $kind = function_exists('naigai_property_kind') ? naigai_property_kind(get_the_ID()) : (in_category('naigai-tochi') ? 'land' : 'other');
                $is_land = ($kind === 'land');
                ?>

                <?php if ($active_tab !== '') : ?>

                    <?php
                    // H2文言を出し分け（土地だけ文言を変える）
                    if ($has_image && $has_pano) {
                        $tab_h2 = $is_land ? '土地写真・360°パノラマ' : '物件写真・360°パノラマ';
                    } elseif ($has_image) {
                        $tab_h2 = $is_land ? '土地写真・区画図（現地・周辺）' : '物件写真一覧（外観・内観・設備）';
                    } else { // panoだけ
                        $tab_h2 = $is_land ? '360°パノラマ（現地）' : '360°パノラマ（室内）';
                    }
                    ?>

                    <h2 class="section-title"><?php echo esc_html($tab_h2); ?></h2>

                    <ul class="tab-links">
                        <?php if ($has_image) : ?>
                            <li class="tab-link <?php echo ($active_tab === 'image-tab') ? 'active' : ''; ?>" data-tab="image-tab">
                                画像ビュー
                            </li>
                        <?php endif; ?>

                        <?php if ($has_pano) : ?>
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

                <?php the_content(); ?>

                <?php
                $kind = function_exists('naigai_property_kind')
                    ? naigai_property_kind(get_the_ID())
                    : (in_category('naigai-tochi') ? 'land' : 'construction');

                $summary_h2 = ($kind === 'land')
                    ? '土地概要（価格・面積・用途地域など）'
                    : '物件概要（価格・面積・築年数など）';
                ?>

                <h2 class="section-title"><?php echo esc_html($summary_h2); ?></h2>
                <?php
                if (function_exists('display_custom_fields_table_1')) {
                    display_custom_fields_table_1($post);
                }
                ?>

                <!-- 設備仕様（タグ） -->
                <div class="widget tag-widget">
                    <h2 class="side-title"><?php echo is_single() ? '設備仕様と特徴' : '関連トピックを探す'; ?></h2>

                    <?php
                    $post_tags = get_the_tags();
                    if ($post_tags) {
                        $tags = [];
                        foreach ($post_tags as $tag) {
                            $tags[$tag->term_id] = $tag; // 重複排除
                        }
                        if (!empty($tags)) {
                            echo '<ul class="tagcloud">';
                            foreach ($tags as $tag) {
                                echo '<li><a href="' . esc_url(get_tag_link($tag->term_id)) . '">' . esc_html($tag->name) . '</a></li>';
                            }
                            echo '</ul>';
                        }
                    } else {
                        echo '<p>関連するタグがありません。</p>';
                    }
                    ?>
                </div>

                <?php
                // =========================
                // 地図 / 追加テーブル
                // =========================
                $google_map_iframe_1 = get_post_meta(get_the_ID(), '_google_map_iframe_1', true);
                if ($google_map_iframe_1) {
                    echo '<h2 class="section-title">所在地とアクセス</h2>';
                    echo '<div class="google-map-iframe">' . $google_map_iframe_1 . '</div>';
                }

                // ★ここが元コードのバグ：table_2 なのに table_1 を見てた → 修正
                if (function_exists('display_custom_fields_table_2')) {
                    display_custom_fields_table_2($post);
                }

                $google_map_iframe_2 = get_post_meta(get_the_ID(), '_google_map_iframe_2', true);
                if ($google_map_iframe_2) {
                    echo '<h2 class="section-title">周辺地図</h2>';
                    echo '<div class="google-map-iframe">' . $google_map_iframe_2 . '</div>';
                }

                if (function_exists('display_custom_fields_table_3')) {
                    display_custom_fields_table_3($post);
                }
                ?>

                <h2 class="section-title">内見・イベント</h2>
                <div id="calendar"></div>

                <div id="naigai-property-list" class="naigai-property-list"></div>

                <?php //require get_template_directory() . '/single-template-parts/event-modal-template.php'; 
                ?>

                <?php
                // =========================
                // 追加画像スライダー
                // =========================
                $slider_images = get_post_meta(get_the_ID(), '_slider_images', true);
                if (!empty($slider_images) && is_array($slider_images) && function_exists('display_slider_images')) {
                    echo '<h2 class="section-title">追加写真ギャラリー</h2>';
                    display_slider_images();
                }
                ?>

            </div><!-- /post -->

            <!-- pager（そのまま残す） -->
            <?php require get_template_directory() . '/pager-post-navigation.php'; ?>

            <?php
            // =========================
            // 関連記事
            // =========================
            $categories  = get_the_category(get_the_ID());
            $category_ID = [];
            foreach ((array)$categories as $category) {
                $category_ID[] = $category->cat_ID;
            }

            $args = [
                'post__not_in'   => [get_the_ID()],
                'category__in'   => $category_ID,
                'posts_per_page' => 3,
                'orderby'        => 'rand',
            ];
            $my_query = new WP_Query($args);
            ?>

            <div class="related-posts">
                <h2 class="side-title">関連記事</h2>

                <?php if ($my_query->have_posts()) : ?>
                    <ul id="related-posts">
                        <?php while ($my_query->have_posts()) : $my_query->the_post(); ?>
                            <li class="clearfix">
                                <div class="main-custom-post">

                                    <div id="post-<?php the_ID(); ?>" <?php post_class('custom-post'); ?>>
                                        <div class="post-meta">
                                            <h2 class="related-post-title">
                                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                            </h2>

                                            <p class="post-meta-row">
                                                <svg class="icon icon-price-tags">
                                                    <use xlink:href="#icon-price-tags"></use>
                                                    <span class="category"><?php the_category(', '); ?></span>
                                                </svg>
                                                <span class="sidebar-comment-num">
                                                    <?php comments_popup_link('<i class="far fa-comments"></i> : 0', '<i class="far fa-comments"></i> : 1', '<i class="far fa-comments"></i> : %'); ?>
                                                </span>
                                            </p>
                                        </div>

                                        <div class="blog-thumbnail-box">
                                            <a href="<?php the_permalink(); ?>" title="「<?php the_title(); ?>」Read">
                                                <?php
                                                $days  = 14;
                                                $now   = date_i18n('U');
                                                $entry = get_the_time('U');
                                                $term  = date('U', ($now - $entry)) / 86400;
                                                if ($days > $term) {
                                                    echo '<div class="box new-post"><div class="ribbon ribbon-top-left"><span>New</span></div></div>';
                                                }

                                                if (has_post_thumbnail()) {
                                                    $blog_thumbnail = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), [600, 600]);
                                                    echo '<div class="blog-post-image" style="background-image:url(' . esc_url($blog_thumbnail[0]) . ');">';
                                                } else {
                                                    $noimage = get_template_directory_uri() . '/images/noimage.gif';
                                                    echo '<div class="blog-post-image" style="background-image:url(' . esc_url($noimage) . ');">';
                                                }
                                                ?>
                                                <div class="thumbnail-overlay">
                                                    <span class="thumbnail-title"><?php echo esc_html(get_the_title()); ?></span>
                                                </div>
                                        </div><!-- /.blog-post-image -->
                                        </a>
                                        <div class="heart-icon <?php echo get_post_meta(get_the_ID(), 'liked', true) ? 'active' : ''; ?>"
                                            data-post-id="<?php echo esc_attr(get_the_ID()); ?>">
                                            <svg class="icon icon-heart" aria-hidden="true" focusable="false">
                                                <use xlink:href="#icon-heart"></use>
                                            </svg>
                                        </div>

                                    </div>

                                </div><!-- /.custom-post -->

                                <div class="content-box">
                                    <?php get_template_part('property-info-table'); ?>
                                </div>

                                <div class="more-link-wrapper">
                                    <a href="<?php the_permalink(); ?>" title="Read more" class="more-link">この物件の詳細を見る &raquo;</a>
                                    <a href="#store-reservation" title="来店予約に進む" class="store-reserve-link" data-post-id="<?php echo esc_attr(get_the_ID()); ?>">
                                        来店予約に進む »
                                    </a>
                                </div>

            </div><!-- /.main-custom-post -->
            </li>
        <?php endwhile; ?>
        </ul>
    <?php else : ?>
        <p>関連する記事はありませんでした ...</p>
    <?php endif; ?>
</div><!-- /related-posts -->

<?php wp_reset_postdata(); ?>

<?php endwhile; ?>
<?php else : ?>
    <div class="post">
        <h2>記事はありません</h2>
        <p>お探しの記事は見つかりませんでした。</p>
    </div>
<?php endif; ?>

<?php
// ループ外でも使えるようにIDはクエリから取る
$post_id = get_queried_object_id();
?>

<h2 id="store-reservation" class="section-title">来店予約・口コミ</h2>

<ul class="custom-tab-links">
    <li class="custom-tab-link active" data-tab="reservation">来店予約フォーム</li>
    <li class="custom-tab-link" data-tab="comments">口コミレビュー</li>
</ul>

<div class="custom-tab-content">
    <div id="tab-reservation" class="custom-tab-pane">
        <?php get_template_part('reservation-form-content', null, ['post_id' => $post_id]); ?>
    </div>

    <div id="tab-comments" class="custom-tab-pane" style="display:none;">
        <?php
        if (comments_open()) {
            comments_template();
        }
        ?>
    </div>
</div>

</div><!-- /main -->

<!--sidebar-->
<div id="single">
    <?php if (in_category('naigai-tochi')) : ?>

        <?php
        // sidebar-land.php 自身が #sidebar を出力する。
        get_template_part('sidebar-land');
        ?>

    <?php elseif (in_category('naigai-construction')) : ?>

        <?php
        // sidebar-house.php 自身が #sidebar を出力する。
        get_template_part('sidebar-house');
        ?>

    <?php elseif (in_category('recommended-land-and-house')) : ?>

        <?php
        // 推奨土地・住宅カテゴリーページと同じ sidebar-land.php を使用する。
        // sidebar-land.php 自身が #sidebar を出力する。
        get_template_part('sidebar-land');
        ?>

    <?php else : ?>

        <div id="sidebar" class="clearfix">
            <?php
            // sidebar.php は #sidebar を持たないため、
            // この分岐だけここで #sidebar を付ける。
            get_template_part('sidebar');
            ?>
        </div>

    <?php endif; ?>
</div>
<!--/sidebar-->

<?php get_footer(); ?>