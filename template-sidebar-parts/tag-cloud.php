<!-- Tag Cloud Widget -->
<div class="widget">
    <div class="side-title">Tag Cloud</div>
    <?php
    // 現在のカテゴリースラッグを取得（アーカイブページとカテゴリーページに対応）
    $category_name = '';  // $category_name 変数に空の値をセットして初期化

    // カテゴリーページの場合
    if (is_category()) {
        $category_name = get_queried_object()->slug; // 現在のカテゴリのスラッグ（名前）を取得
        // is_category() はカテゴリーページが表示されているかどうかを判定する WordPress の条件タグです。
    // アーカイブページの場合
    } elseif (is_archive()) {
        $category_name = ''; // アーカイブページの場合、カテゴリースラッグなしでタグを全表示
    }

    // タグ表示用の共通設定
    $args = array(
        'smallest' => 14,
        'largest'  => 18,
        'unit'     => 'px',
        'number'   => 0, // 表示するタグの数、0で制限なし
        'format'   => 'flat', // フラット形式で表示
        'taxonomy' => 'post_tag', // タクソノミーはpost_tag
        'echo'     => true, // 出力
    );

    // カテゴリーページの場合、そのカテゴリに関連するタグを表示
    if (!empty($category_name)) {
        // カテゴリーのIDを取得
        $category = get_term_by('slug', $category_name, 'category');
        $category_id = $category ? $category->term_id : 0;

        // カテゴリーに関連する投稿を取得
        $posts_in_category = get_posts(array(
            'category' => $category_id, // 特定のカテゴリ
            'fields' => 'ids', // 投稿IDのみ取得
        ));

        // 投稿に関連するタグを取得
        if (!empty($posts_in_category)) {
            $related_tags = get_terms(array(
                'taxonomy' => 'post_tag',
                'object_ids' => $posts_in_category, // 上記の投稿IDに関連するタグ
                'fields' => 'ids', // タグIDのみ取得
            ));

            // タグがあれば、タグクラウドを表示
            if (!empty($related_tags)) {
                $args['include'] = $related_tags; // 取得したタグIDをタグクラウドの引数に追加
                ?>
                <ul class="tagcloud">
                    <?php wp_tag_cloud($args); ?>
                </ul>
                <?php
            }
        }
    } else {
        // アーカイブページの場合、すべてのタグを表示
        ?>
        <ul class="tagcloud">
            <?php
            // アーカイブページでは、すべてのタグを表示
            wp_tag_cloud($args);
            ?>
        </ul>
        <?php
    }
    ?>
</div>
<!-- /Tag Cloud -->
