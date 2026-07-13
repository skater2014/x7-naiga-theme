<div id="sidebar">
    <?php
    // ==================================================
    // 現在URLを取得
    // ※ taxonomy名の判定ズレがあっても、URLでも止めるため
    // ==================================================
    $request_uri = isset($_SERVER['REQUEST_URI']) ? wp_unslash($_SERVER['REQUEST_URI']) : '';

    // --------------------------------------------------
    // price-range を非表示にしたい条件
    // --------------------------------------------------

    // category/travel を非表示
    // 1) WordPress条件分岐
    // 2) URL文字列でも判定
    $hide_price_range_travel =
        is_category('travel') ||
        (strpos($request_uri, '/category/travel') !== false);

    // blog taxonomy / blog-genre taxonomy を非表示
    // taxonomy名が blog / blog-genre のどちらでも止める
    // さらに URL が /blog-genre/ を含めば強制的に止める
    $hide_price_range_blog_tax =
        is_tax('blog') ||
        is_tax('blog-genre') ||
        (strpos($request_uri, '/blog-genre/') !== false);

    // 最終的な非表示条件
    $hide_price_range = (
        $hide_price_range_travel ||
        $hide_price_range_blog_tax
    );

    // ==================================================
    // price-range サイドバー表示条件
    // ==================================================
    if (
        is_active_sidebar('price-range') &&

        // 固定ページ company / sample-page では非表示
        !is_page(array('company', 'sample-page')) &&

        // 通常投稿の詳細ページでは非表示
        !is_single() &&

        // single-house の詳細ページでは非表示
        !is_singular('single-house') &&

        // blog カスタム投稿タイプのアーカイブでは非表示
        !is_post_type_archive('blog') &&

        // 上で定義した非表示条件に当てはまる場合は出さない
        !$hide_price_range
    ) :
    ?>
        <!--
          価格帯の出力経路:
          1) この #price-range-sidebar は価格帯ウィジェットの外箱。
          2) dynamic_sidebar('price-range') が WordPress のウィジェットエリア price-range を呼び出す。
          3) 実際のHTMLは Price_Range_Widget.php の widget() から出力される。
        -->
        <div id="price-range-sidebar" class="">
            <?php dynamic_sidebar('price-range'); ?>
        </div>
    <?php endif; ?>

    <!-- 既存のサイドバー -->
    <?php dynamic_sidebar('sidebar-2'); ?>

    <?php get_template_part('template-sidebar-parts/social-icons'); ?>
    <?php get_template_part('template-sidebar-parts/material-post'); ?>
    <?php get_template_part('template-sidebar-parts/popular-post'); ?>
    <?php get_template_part('template-sidebar-parts/artifacts-post'); ?>

    <?php
    // モバイル以外のみタグクラウド表示
    if (!is_mobile_device()) :
        get_template_part('template-sidebar-parts/tag-cloud');
    endif;
    ?>

    <?php dynamic_sidebar('sidebar-1'); ?>
</div>
<!-- End / Sidebar -->