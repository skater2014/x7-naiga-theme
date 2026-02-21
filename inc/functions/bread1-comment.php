<?php 
/* フォントアイコンは、CSSで類似要素で読み込んでいる。
   after before ホームと階層のアイコンで表示 
   このコードは、Schemaで対応中 
*/

function breadcrumb1() {
    global $post;

    // ホームページや管理画面では処理しない
    if (is_home() || is_admin()) {
        return;
    }

    // BreadcrumbList 開始
    $str = '<div id="breadcrumb" class="clearfix" itemscope itemtype="http://schema.org/BreadcrumbList">';
    $str .= '<ol>';

    // ホームへのリンク
    $str .= '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
                <a href="' . esc_url(home_url('/')) . '" itemprop="item">
                    <span itemprop="name">' . get_bloginfo('name') . '</span>
                </a>
                <meta itemprop="position" content="1" />
             </li>';

            // 🔹 検索結果ページ
            if (is_search()) {
                $search_query = esc_html(get_search_query());
                $house_type = isset($_GET['house_type']) && $_GET['house_type'] !== '' ? get_term_by('slug', sanitize_text_field($_GET['house_type']), 'house-type') : null;
                $region = isset($_GET['region']) && $_GET['region'] !== '' ? get_term_by('slug', sanitize_text_field($_GET['region']), 'region') : null;

                // 🔹 `house-type` または `region` がある場合 → 「タクソノミーの検索結果」
                if ($house_type || $region) {
                    $str .= '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
                                <span itemprop="name">検索結果: ' . 
                                ($house_type ? esc_html($house_type->name) : '') . 
                                ($house_type && $region ? ' / ' : '') . 
                                ($region ? esc_html($region->name) : '') . 
                                '</span>
                                <meta itemprop="position" content="2" />
                             </li>';

                // 🔹 `house-type` や `region` が指定されず、検索クエリも空の場合 → 「全ての検索結果」
                } elseif (empty($search_query)) {
                    $str .= '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
                                <span itemprop="name">全ての検索結果</span>
                                <meta itemprop="position" content="2" />
                             </li>';

                // 🔹 `house-type` や `region` が指定されず、検索ワードでの検索結果なし → 「検索結果なし: {検索ワード}」
                } elseif (!have_posts()) {
                    $str .= '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
                                <span itemprop="name">検索結果なし: ' . $search_query . '</span>
                                <meta itemprop="position" content="2" />
                             </li>';

                // 🔹 `house-type` や `region` なし、検索ワードありで検索結果あり → 「検索結果: {検索ワード}」
                } else {
                    $str .= '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
                                <span itemprop="name">検索結果: ' . $search_query . '</span>
                                <meta itemprop="position" content="2" />
                             </li>';
                }
        

    // 🔹 404エラーページ
    } elseif (is_404()) {
        $str .= '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
                    <span itemprop="name">404 Not Found</span>
                    <meta itemprop="position" content="2" />
                 </li>';

    // 🔹 各種アーカイブやページの処理
    } else {
        $crumbs = [];

        // 🔹 カテゴリーアーカイブ
        if (is_category()) {
            $cat = get_queried_object();
            if ($cat && !is_wp_error($cat) && $cat->parent != 0) {
                $ancestors = array_reverse(get_ancestors($cat->cat_ID, 'category'));
                foreach ($ancestors as $ancestor) {
                    $ancestor_category = get_category($ancestor);
                    if ($ancestor_category) {
                        $crumbs[] = '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
                                        <a href="' . esc_url(get_category_link($ancestor_category->term_id)) . '" itemprop="item">
                                            <span itemprop="name">' . esc_html($ancestor_category->name) . '</span>
                                        </a>
                                        <meta itemprop="position" content="' . (count($crumbs) + 2) . '" />
                                     </li>';
                    }
                }
            }
            $crumbs[] = '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
                            <span itemprop="name">' . esc_html(single_cat_title('', false)) . '</span>
                            <meta itemprop="position" content="' . (count($crumbs) + 2) . '" />
                         </li>';
        }

       // 🔹 投稿ページ（通常の投稿 + カスタム投稿含む）
        if (is_singular()) {
            $post_type = get_post_type();
            $categories = get_the_category($post->ID);

            // 通常の投稿タイプ "post" のカテゴリパンくず
            if ($post_type === 'post' && !empty($categories)) {
                $cat = $categories[0];
                $crumbs[] = '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
                                <a href="' . esc_url(get_category_link($cat->term_id)) . '" itemprop="item">
                                    <span itemprop="name">' . esc_html($cat->cat_name) . '</span>
                                </a>
                                <meta itemprop="position" content="' . (count($crumbs) + 2) . '" />
                             </li>';
            }

            // カスタム投稿タイプ "house"
            if ($post_type === 'house') {
                $house_types = get_the_terms($post->ID, 'house-type');
                $regions = get_the_terms($post->ID, 'region');

                if (!empty($house_types) && !is_wp_error($house_types)) {
                    $term = $house_types[0];
                    $crumbs[] = '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
                                    <a href="' . esc_url(get_term_link($term->term_id, 'house-type')) . '" itemprop="item">
                                        <span itemprop="name">' . esc_html($term->name) . '</span>
                                    </a>
                                    <meta itemprop="position" content="' . (count($crumbs) + 2) . '" />
                                 </li>';
                }

                if (!empty($regions) && !is_wp_error($regions)) {
                    $term = $regions[0];
                    $crumbs[] = '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
                                    <a href="' . esc_url(get_term_link($term->term_id, 'region')) . '" itemprop="item">
                                        <span itemprop="name">' . esc_html($term->name) . '</span>
                                    </a>
                                    <meta itemprop="position" content="' . (count($crumbs) + 2) . '" />
                                 </li>';
                }
            }

            // 🔹 カスタム投稿タイプ "blog"
            if ($post_type === 'blog') {
                $crumbs[] = '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
                                <a href="' . esc_url(get_post_type_archive_link('blog')) . '" itemprop="item">
                                    <span itemprop="name">ブログ</span>
                                </a>
                                <meta itemprop="position" content="' . (count($crumbs) + 2) . '" />
                             </li>';
            }

            // 最後に投稿タイトルを追加
            $crumbs[] = '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
                            <span itemprop="name">' . esc_html(get_the_title()) . '</span>
                            <meta itemprop="position" content="' . (count($crumbs) + 2) . '" />
                         </li>';
        }


        // 🔹 固定ページ
        if (is_page() && $post->post_parent) {
            $ancestors = array_reverse(get_post_ancestors($post->ID));
            foreach ($ancestors as $ancestor) {
                $ancestor_post = get_post($ancestor);
                if ($ancestor_post) {
                    $crumbs[] = '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
                                    <a href="' . esc_url(get_permalink($ancestor_post->ID)) . '" itemprop="item">
                                        <span itemprop="name">' . esc_html($ancestor_post->post_title) . '</span>
                                    </a>
                                    <meta itemprop="position" content="' . (count($crumbs) + 2) . '" />
                                 </li>';
                }
            }
        }

        // 追加したパンくずリストを表示
        if (!empty($crumbs)) {
            foreach ($crumbs as $crumb) {
                $str .= $crumb;
            }
        }
    }

    // BreadcrumbList 終了
    $str .= '</ol>';
    $str .= '</div>';
    
    echo $str; 
}
