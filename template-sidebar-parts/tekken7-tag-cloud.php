<div class="widget tag-widget">
    <div class="side-title">
        <?php
        // 現在のテンプレートが single.php の場合
        if (is_single()) {
            echo '設備仕様'; // single.php 用のタイトル
        } else {
            echo '関連トピックを探す'; // サイドバー用のタイトル
        }
        ?>
    </div>
    <?php
    // タクソノミー情報を現在のページの条件に基づいて取得
    $house_type_term = get_queried_object(); // 現在のタクソノミー条件を取得

    // 'archive-house.php' タクソノミー・カテゴリーに関連する投稿の取得
    $args = array(
        'posts_per_page' => -1,
        'post_type'      => 'house', // カスタム投稿タイプ 'house'
        'tax_query' => array(
            'relation' => 'AND', // 両方の条件をANDでつなげる
            array(
                'taxonomy' => 'house-type', // 'house-type' タクソノミー
                'field'    => 'slug', // タクソノミーのスラッグで絞り込む
                'terms'    => $house_type_term->slug, // 現在のタクソノミーのスラッグ
            ),
            array(
                'taxonomy' => 'region', // 'region' タクソノミー
                'field'    => 'slug', // タクソノミーのスラッグで絞り込む
                'terms'    => 'example-region', // 例: 'example-region' というスラッグ
            ),
        ),
    );

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        $tags = array(); // タグIDをキーにする配列を初期化

        while ($query->have_posts()) {
            $query->the_post();
            $post_tags = get_the_tags();
            if ($post_tags) {
                foreach ($post_tags as $tag) {
                    if (!array_key_exists($tag->term_id, $tags)) {
                        $tags[$tag->term_id] = $tag; // タグIDをキーにして追加
                    }
                }
            }
        }

        if (!empty($tags)) {
            echo '<ul class="tagcloud">';
            foreach ($tags as $tag) {
                echo '<li><a href="' . esc_url(get_tag_link($tag->term_id)) . '">' . esc_html($tag->name) . '</a></li>';
            }
            echo '</ul>';
        }
    } else {
        echo '<p>関連する投稿がありません。</p>';
    }

    wp_reset_postdata();
    ?>
</div>
