<?php
// 投稿に関連付けられているカテゴリーを取得
$cats = get_the_category();

// カテゴリーに関連付けられた CSS クラスを初期化
$class = '';

// カテゴリーが存在する場合は処理を行う
if (!empty($cats)) {
    // 各カテゴリーについて処理を行う
    foreach ($cats as $cat) {
        // 各カテゴリーの ID をクラス名に追加する
        $class .= ' term-' . esc_attr($cat->term_id);
    }
}
?>

<div class="archive-post-box <?php echo esc_attr($class); ?>">
    <div class="archive-post-feature">

    <?php
        $type = get_post_meta(get_the_ID(), 'page_featured_type', true);

        switch ($type) {
            case 'youtube':
                echo '<lite-youtube videoid="' . esc_attr(get_post_meta(get_the_ID(), 'page_video_id', true)) . '" playlabel="Play: Keynote (Google I/O \'18)" style="max-width:100%; height:auto;"></lite-youtube>';
                break;

            case 'vimeo':
                echo '<lite-vimeo videoid="' . esc_attr(get_post_meta(get_the_ID(), 'page_video_id', true)) . '">
                    <div class="ltv-playbtn"></div>
                    </lite-vimeo>';
                break;

            default:
                // サムネイルが存在する場合
                if (has_post_thumbnail()) {
                    echo '<div class="archive-post-image">';
                    echo '<a href="' . esc_url(get_permalink()) . '">';
                    the_post_thumbnail('home-thumb');
                    echo '<div class="thumbnail-overlay">';
                    echo '<h3>' . esc_html(get_the_title()) . '</h3>';
                    echo '</div>'; // オーバーレイ終了
                    echo '</a>';
                    echo '</div>';
                } else {
                    // サムネイルがない場合は noimage を背景として表示
                    $noimage = get_template_directory_uri() . '/images/noimage.gif';
                    echo '<div class="archive-post-image">';
                    echo '<a href="' . esc_url(get_permalink()) . '" class="thumbnail-link">';
                    echo '<div class="archive-post-image" style="background-image:url(' . esc_url($noimage) . '); aspect-ratio: 16 / 9;">';
                    echo '<div class="thumbnail-overlay">'; // オーバーレイ開始
                    echo '<h3>' . esc_html(get_the_title()) . '</h3>';
                    echo '</div>'; // オーバーレイ終了
                    echo '</div>'; // archive-post-image
                    echo '</a>';
                    echo '</div>';
                }
                break;
        }
    ?>

    </div><!-- archive-post-feature -->

    <div class="archive-post-info">
        <?php echo '<h3><a href="' . esc_url(get_the_permalink()) . '">' . esc_html(get_the_title()) . '</a></h3>'; ?>
    </div>
</div><!-- archive-post-box -->
