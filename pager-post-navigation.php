<div class="navigation">
    <style>
        .navigation .alignleft, .navigation .alignright {
            display: flex;
            flex-direction: column;
        }
        .navigation .alignleft img, .navigation .alignright img {
            width: 100%;
            height: auto;
        }
        .navigation .alignleft a, .navigation .alignright a {
            display: block;
            text-decoration: none;
            color: inherit;
        }
        .navigation .alignleft span, .navigation .alignright span {
            display: block;
        }
        .dark-theme .alignleft, .dark-theme .alignright {
            color: var(--link-color-blue);
        }

        /* パソコン向け */
@media (min-width: 768px) {
    .navigation .alignleft img, .navigation .alignright img {
        width: 30%;
        height: auto;
    }
}

/* モバイル向け */
@media (max-width: 767px) {
    .navigation .alignleft img, .navigation .alignright img {
        width: 100%;
        height: auto;
    }
}
    </style>

    <?php
    $prevpost = get_adjacent_post(true, '', true);  // 前の記事
    $nextpost = get_adjacent_post(true, '', false); // 次の記事

    if ($prevpost || $nextpost) :
    ?>
    <div class="navigation-links">
        <?php 
        // 次の記事
        if ($nextpost) :
            $next_thumbnail = has_post_thumbnail($nextpost->ID) ? wp_get_attachment_image_src(get_post_thumbnail_id($nextpost->ID), 'large') : [get_template_directory_uri() . '/images/noimage.gif'];
        ?>
        <div class="alignright">
            <a href="<?php echo get_permalink($nextpost->ID); ?>">
                <span>次の記事
                    <svg class="icon icon-arrow-right2"><use xlink:href="#icon-arrow-right2"></use></svg>
                </span>
                <img src="<?php echo esc_url($next_thumbnail[0]); ?>" loading="lazy" alt="<?php echo esc_attr(get_the_title($nextpost->ID)); ?>">
            </a>
            <div><a href="<?php echo get_permalink($nextpost->ID); ?>"><?php echo esc_html(get_the_title($nextpost->ID)); ?></a></div>
        </div>
        <?php endif; ?>

        <?php 
        // 前の記事
        if ($prevpost) :
            $prev_thumbnail = has_post_thumbnail($prevpost->ID) ? wp_get_attachment_image_src(get_post_thumbnail_id($prevpost->ID), 'large') : [get_template_directory_uri() . '/images/noimage.gif'];
        ?>
        <div class="alignleft">
            <a href="<?php echo get_permalink($prevpost->ID); ?>">
                <span>前の記事
                    <svg class="icon icon-arrow-left2"><use xlink:href="#icon-arrow-left2"></use></svg>
                </span>
                <img src="<?php echo esc_url($prev_thumbnail[0]); ?>" loading="lazy" alt="<?php echo esc_attr(get_the_title($prevpost->ID)); ?>">
            </a>
            <div><a href="<?php echo get_permalink($prevpost->ID); ?>"><?php echo esc_html(get_the_title($prevpost->ID)); ?></a></div>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

<?php
    // 記事がない場合はカテゴリに戻るリンクを表示
    if (!$nextpost || !$prevpost) :
        $category_link = '';
        $category_text = '';

        // 投稿タイプやカテゴリに基づいてリンクとテキストを設定
        if (is_singular('naigai-tochi') || has_term('naigai-tochi', 'category')) {
            $category_link = get_category_link(get_term_by('slug', 'naigai-tochi', 'category')->term_id);
            $category_text = 'Naigai Tochi 記事一覧に戻る';
        } elseif (is_singular('naigai-construction') || has_term('naigai-construction', 'category')) {
            $category_link = get_category_link(get_term_by('slug', 'naigai-construction', 'category')->term_id);
            $category_text = 'Naigai Construction 記事一覧に戻る';
        } elseif (is_singular('house')) {
            $category_link = get_post_type_archive_link('house');
            $category_text = 'House アーカイブに戻る';
        }

        // リンクが設定されている場合のみ表示
        if ($category_link) :
        ?>
        <div class="alignleft">
            <a href="<?php echo esc_url($category_link); ?>"><?php echo esc_html($category_text); ?></a>
        </div>
        <?php
        endif;
    endif;
?>

</div>
