<?php
/*chat GPT  https://schema.org/BreadcrumbList
カスタム投稿タイプ "house" などのsingle-"slug".php　と　通常の投稿single.php　パンくずリスト 
*/
?>
<?php
ob_start(); // 出力バッファを開始します。
function breadcrumb2()
{
    global $wp_query, $post;
    $str = '<nav id="breadcrumb" class="clearfix">' . "\n";
    $str .= '<ol itemscope itemtype="https://schema.org/BreadcrumbList">' . "\n";
    $str .= '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">' . "\n";
    $str .= '<a itemprop="item" href="' . esc_html(home_url('/')) . '">' . "\n";
    $str .= '<span itemprop="name">' . esc_html(get_bloginfo('name')) . '</span>' . "\n";
    $str .= '</a>' . "\n";
    $str .= '<meta itemprop="position" content="1" />' . "\n";
    $str .= '</li>' . "\n";

    if (is_search()) {
        // 検索結果ページ
        $total_results = $wp_query->found_posts;
        $str .= '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">' . "\n";
        $str .= '「' . esc_html(get_search_query()) . '」で検索した結果、' . esc_html($total_results) . '件見つかりました。' . "\n";
        $str .= '<meta itemprop="position" content="2" />' . "\n";
        $str .= '</li>' . "\n";
        wp_reset_postdata();
    } elseif (is_tag()) {
        // タグページ
        $str .= '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">' . "\n";
        $str .= 'タグ : ' . single_tag_title('', false) . "\n";
        $str .= '<meta itemprop="position" content="2" />' . "\n";
        $str .= '</li>' . "\n";
    } elseif (is_404()) {
        // 404エラーページ
        $str .= '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">' . "\n";
        $str .= '404 Not found' . "\n";
        $str .= '<meta itemprop="position" content="2" />' . "\n";
        $str .= '</li>' . "\n";
    } elseif (is_date()) {
        // 日付アーカイブページ
        $str .= '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">' . "\n";
        $str .= '<a itemprop="item" href="' . esc_html(home_url('/')) . '">' . "\n";
        $str .= '<span itemprop="name">Gaming Blog</span>' . "\n";
        $str .= '</a>' . "\n";
        $str .= '<meta itemprop="position" content="2" />' . "\n";
        $str .= '</li>' . "\n";

        if (is_day()) {
            $str .= '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">' . "\n";
            $str .= '<a itemprop="item" href="' . get_year_link(get_query_var('year')) . '">' . get_query_var('year') . '年</a>' . "\n";
            $str .= '<meta itemprop="position" content="3" />' . "\n";
            $str .= '</li>' . "\n";

            $str .= '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">' . "\n";
            $str .= '<a itemprop="item" href="' . get_month_link(get_query_var('year'), get_query_var('monthnum')) . '">' . get_query_var('monthnum') . '月</a>' . "\n";
            $str .= '<meta itemprop="position" content="4" />' . "\n";
            $str .= '</li>' . "\n";

            $str .= '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">' . get_query_var('day') . '日' . "\n";
            $str .= '<meta itemprop="position" content="5" />' . "\n";
            $str .= '</li>' . "\n";
        } elseif (is_month()) {
            $str .= '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">' . "\n";
            $str .= '<a itemprop="item" href="' . get_year_link(get_query_var('year')) . '">' . get_query_var('year') . '年</a>' . "\n";
            $str .= '<meta itemprop="position" content="3" />' . "\n";
            $str .= '</li>' . "\n";

            $str .= '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">' . get_query_var('monthnum') . '月' . "\n";
            $str .= '<meta itemprop="position" content="4" />' . "\n";
            $str .= '</li>' . "\n";
        } elseif (is_year()) {
            $str .= '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">' . get_query_var('year') . '年' . "\n";
            $str .= '<meta itemprop="position" content="3" />' . "\n";
            $str .= '</li>' . "\n";
        }
    } elseif (is_post_type_archive('information')) {
        // カスタム投稿タイプ「information」のアーカイブページ
        $str .= '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">' . "\n";
        $str .= '<span itemprop="name">Information</span>' . "\n";
        $str .= '<meta itemprop="position" content="2" />' . "\n";
        $str .= '</li>' . "\n";
    } elseif (is_post_type_archive('house')) {
        // カスタム投稿タイプ「genshin_updated」のアーカイブページ
        $str .= '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">' . "\n";
        $str .= '<span itemprop="name">建売住宅</span>' . "\n";
        $str .= '<meta itemprop="position" content="2" />' . "\n";
        $str .= '</li>' . "\n";
    } elseif (is_post_type_archive('recruitment')) {
        // カスタム投稿タイプ「genshin_updated」のアーカイブページ
        $str .= '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">' . "\n";
        $str .= '<span itemprop="name">キャリア採用</span>' . "\n";
        $str .= '<meta itemprop="position" content="2" />' . "\n";
        $str .= '</li>' . "\n";
    } elseif (is_post_type_archive('blog')) {
        // カスタム投稿タイプ「blog」のアーカイブページ
        $str .= '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">' . "\n";
        $str .= '<span itemprop="name">ブログ</span>' . "\n";
        $str .= '<meta itemprop="position" content="2" />' . "\n";
        $str .= '</li>' . "\n";
    } elseif (is_category()) {
        // カテゴリーアーカイブページ
        $total_results = $wp_query->found_posts;
        $str .= '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">' . "\n";
        $str .= '<a itemprop="item" href="' . esc_html(home_url('/')) . '">内外土地開発(株)</a>' . "\n";
        $str .= '<meta itemprop="position" content="2" />' . "\n";
        $str .= '</li>' . "\n";

        $cat = get_queried_object();
        if ($cat->parent != 0) {
            $ancestors = array_reverse(get_ancestors($cat->cat_ID, 'category'));
            foreach ($ancestors as $ancestor) {
                $str .= '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">' . "\n";
                $str .= '<a itemprop="item" href="' . esc_url(get_category_link($ancestor)) . '">' . esc_html(get_cat_name($ancestor)) . '</a>' . "\n";
                $str .= '<meta itemprop="position" content="' . ($wp_query->post_count + 3) . '" />' . "\n";
                $str .= '</li>' . "\n";
            }
        }

        $str .= '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">' . "\n";
        $str .= esc_html($cat->cat_name) . '（' . $total_results . '件見つかりました）' . "\n";
        $str .= '<meta itemprop="position" content="' . ($wp_query->post_count + 4) . '" />' . "\n";
        $str .= '</li>' . "\n";
        wp_reset_postdata();
    } elseif (is_author()) {
        // 投稿者アーカイブページ
        $str .= '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">' . "\n";
        $str .= '<a itemprop="item" href="' . esc_url(home_url('/')) . '">' . get_bloginfo('name') . '</a>' . "\n";
        $str .= '<meta itemprop="position" content="2" />' . "\n";
        $str .= '</li>' . "\n";

        $str .= '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">' . "\n";
        $str .= '投稿者 : ' . esc_html(get_the_author_meta('display_name', get_query_var('author'))) . "\n";
        $str .= '<meta itemprop="position" content="3" />' . "\n";
        $str .= '</li>' . "\n";
    } elseif (is_home()) {
        // Gaming Blogページ
        $str .= '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">' . "\n";
        $str .= esc_html(get_bloginfo('name')) . "\n";
        $str .= '<meta itemprop="position" content="2" />' . "\n";
        $str .= '</li>' . "\n";
    } elseif (is_page()) {
        // 固定ページ
        if ($post->post_parent != 0) {
            $ancestors = array_reverse($post->ancestors);
            foreach ($ancestors as $ancestor) {
                $str .= '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">' . "\n";
                $str .= '<a itemprop="item" href="' . esc_url(get_permalink($ancestor)) . '">' . esc_html(get_the_title($ancestor)) . '</a>' . "\n";
                $str .= '<meta itemprop="position" content="' . ($wp_query->post_count + 3) . '" />' . "\n";
                $str .= '</li>' . "\n";
            }
        }

        $str .= '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">' . "\n";
        $str .= esc_html($post->post_title) . "\n";
        $str .= '<meta itemprop="position" content="' . ($wp_query->post_count + 4) . '" />' . "\n";
        $str .= '</li>' . "\n";
    } elseif (is_attachment()) {
        // 添付ファイルページ
        if ($post->post_parent != 0) {
            $str .= '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">' . "\n";
            $str .= '<a itemprop="item" href="' . esc_url(get_permalink($post->post_parent)) . '">' . esc_html(get_the_title($post->post_parent)) . '</a>' . "\n";
            $str .= '<meta itemprop="position" content="' . ($wp_query->post_count + 3) . '" />' . "\n";
            $str .= '</li>' . "\n";
        }

        $str .= '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">' . "\n";
        $str .= esc_html($post->post_title) . "\n";
        $str .= '<meta itemprop="position" content="' . ($wp_query->post_count + 4) . '" />' . "\n";
        $str .= '</li>' . "\n";
    } elseif (is_singular('house')) {
        //genshin_updated カスタム投稿　リダイレクト対応　genshin-impact/genshin_updated/　変更
        $str .= '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">' . "\n";
        $str .= '<a itemprop="item" href="' . esc_html(home_url('/house')) . '">' . "\n";
        $str .= '<span itemprop="name">建売住宅</span>' . "\n";
        $str .= '</a>' . "\n";
        $str .= '<meta itemprop="position" content="2" />' . "\n";
        $str .= '</li>' . "\n";

        $str .= '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">' . "\n";
        $str .= '<span itemprop="name">' . single_post_title('', false) . '</span>' . "\n";
        $str .= '<meta itemprop="position" content="3" />' . "\n";
        $str .= '</li>' . "\n";
    } elseif (is_singular('product')) {
        // カスタム投稿タイプ「product」の個別投稿ページ
        $str .= '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">' . "\n";
        $str .= '<a itemprop="item" href="' . esc_url(home_url('/')) . 'products/">' . "\n";
        $str .= '<span itemprop="name">製品一覧</span>' . "\n";
        $str .= '</a>' . "\n";
        $str .= '<meta itemprop="position" content="2" />' . "\n";
        $str .= '</li>' . "\n";

        $str .= '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">' . single_post_title('', false) . "\n";
        $str .= '<meta itemprop="position" content="3" />' . "\n";
        $str .= '</li>' . "\n";
    } elseif (is_singular('genshin-character')) {
        // カスタム投稿タイプ「genshin-character」の個別投稿ページ
        $str .= '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">' . "\n";
        $str .= '<a itemprop="item" href="' . esc_url(home_url('/')) . 'updated-character/">' . "\n";
        $str .= '<span itemprop="name">updatedキャラクター一覧</span>' . "\n";
        $str .= '</a>' . "\n";
        $str .= '<meta itemprop="position" content="2" />' . "\n";
        $str .= '</li>' . "\n";

        $str .= '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">' . single_post_title('', false) . "\n";
        $str .= '<meta itemprop="position" content="3" />' . "\n";
        $str .= '</li>' . "\n";
    } elseif (is_singular('post')) {
        // 通常投稿ページ
        $str .= '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">' . "\n";
        $categories = get_the_category($post->ID);
        $cat = $categories[0];

        $str .= '<a itemprop="item" href="' . esc_url(home_url('/')) . '">' . "\n";
        $str .= '<span itemprop="name">' . esc_html(get_bloginfo('name')) . '</span>' . "\n";
        $str .= '</a>' . "\n";
        $str .= '<meta itemprop="position" content="2" />' . "\n";

        if ($cat->parent != 0) {
            $ancestors = array_reverse(get_ancestors($cat->cat_ID, 'category'));
            foreach ($ancestors as $ancestor) {
                $str .= '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">' . "\n";
                $str .= '<a itemprop="item" href="' . esc_url(get_category_link($ancestor)) . '">' . esc_html(get_cat_name($ancestor)) . '</a>' . "\n";
                $str .= '<meta itemprop="position" content="' . ($wp_query->post_count + 3) . '" />' . "\n";
                $str .= '</li>' . "\n";
            }
        }

        $str .= '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">' . "\n";
        $str .= '<a itemprop="item" href="' . esc_url(get_category_link($cat->cat_ID)) . '">' . esc_html($cat->cat_name) . '</a>' . "\n";
        $str .= '<meta itemprop="position" content="' . ($wp_query->post_count + 4) . '" />' . "\n";
        $str .= '</li>' . "\n";

        $str .= '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">' . esc_html($post->post_title) . "\n";
        $str .= '<meta itemprop="position" content="' . ($wp_query->post_count + 5) . '" />' . "\n";
        $str .= '</li>' . "\n";
    } elseif (is_singular('blog')) {
        // カスタム投稿タイプ「blog」の個別投稿ページ
        $str .= '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">' . "\n";
        $str .= '<a itemprop="item" href="' . esc_url(home_url('/fudosan-column')) . '">' . "\n";
        $str .= '<span itemprop="name">不動産コラム</span>' . "\n";
        $str .= '</a>' . "\n";
        $str .= '<meta itemprop="position" content="2" />' . "\n";
        $str .= '</li>' . "\n";

        $str .= '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">' . "\n";
        $str .= '<span itemprop="name">' . esc_html($post->post_title) . '</span>' . "\n";
        $str .= '<meta itemprop="position" content="3" />' . "\n";
        $str .= '</li>' . "\n";
    } elseif (is_tax()) {
        // タクソノミーアーカイブページ　リダイレクト対応　genshin-impact/genshin_updated/ 変更
        $current_term = get_queried_object();
        $str .= '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">' . "\n";
        $str .= '<a itemprop="item" href="' . esc_url(home_url('/')) . 'house/">' . "\n";
        $str .= '<span itemprop="name">建売住宅</span>' . "\n";
        $str .= '</a>' . "\n";
        $str .= '<meta itemprop="position" content="2" />' . "\n";
        $str .= '</li>' . "\n";

        $str .= '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">' . "\n";
        $str .= '<span itemprop="name">' . single_cat_title('', false) . '</span>' . "\n";
        $str .= '<meta itemprop="position" content="3" />' . "\n";
        $str .= '</li>' . "\n";
    } else {
        // その他のページ
        $str .= '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">' . "\n";
        $str .= '<span itemprop="name">' . wp_title('', true) . '</span>' . "\n";
        $str .= '<meta itemprop="position" content="2" />' . "\n";
        $str .= '</li>' . "\n";
    }

    $str .= '</ol>' . "\n";
    $str .= '</nav>' . "\n";

    echo $str;
}
?> 