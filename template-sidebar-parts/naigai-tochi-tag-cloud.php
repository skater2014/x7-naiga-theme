<!-- Tag Cloud Widget -->
<div class="widget">
    <div class="side-title">Tag Cloud</div>
    <?php
    // 現在のカテゴリーを取得
    $category = get_queried_object();

    // カテゴリに応じてタグを表示
    if ($category->slug == 'naigai-tochi') {
        $args = array(
            'smallest' => 14,
            'largest'  => 18,
            'unit'     => 'px',
            'number'   => 0,
            'format'   => 'flat',
            'taxonomy' => 'post_tag',
            'echo'     => true,
        );
    }
    ?>
    <ul class="tagcloud">
        <?php wp_tag_cloud($args); ?>
    </ul>
</div>
<!-- /Tag Cloud -->
