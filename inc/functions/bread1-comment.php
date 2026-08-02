<?php

/**
 * ------------------------------------------------------------
 * パンくずリスト
 * ファイル: inc/functions/bread1-comment.php
 * blog から category を外した前提の整理版
 * ------------------------------------------------------------
 *
 * このファイルの目的
 * - サイト全体のパンくずを breadcrumb1() だけで出力する
 * - フロントページは「ホーム」を先頭に出す
 * - 固定ページは、可能な限り実際の hero 見出しに合わせる
 * - blog 投稿は category を使わず、
 *   「ホーム > 那須の不動産コラム > blog_genre > 記事タイトル」
 *   の流れで出す
 * - house / recruitment も投稿タイプごとに自然な親階層を出す
 *
 * 今回の前提
 * - blog 投稿タイプから category を削除する
 * - blog の分類は blog_genre のみで行う
 *
 * そのため削除したもの
 * - blog と category を共有していた前提の判定処理
 * - category アーカイブで blog 文脈を推測する処理
 *
 * この変更の効果
 * - Google やパンくずで、
 *   blog の親として category が混ざりにくくなる
 * - 不動産コラムの親階層をシンプルに保ちやすくなる
 */


/**
 * ------------------------------------------------------------
 * パンくず1項目を配列へ追加する
 * ------------------------------------------------------------
 *
 * 役割:
 * - 表示名とリンクURLを1件ずつ $items に追加する
 * - 名前が空のときは追加しない
 */
if (!function_exists('ng_breadcrumb_add_item')) {
    function ng_breadcrumb_add_item(&$items, $name, $url = '')
    {
        if ($name === '' || $name === null) {
            return;
        }

        $items[] = array(
            'name' => $name,
            'url'  => $url,
        );
    }
}


/**
 * ------------------------------------------------------------
 * タームリンクを安全に返す
 * ------------------------------------------------------------
 *
 * 役割:
 * - get_term_link() が WP_Error を返しても空文字に落とす
 * - パンくず生成中の崩れを防ぐ
 */
if (!function_exists('ng_get_safe_term_link')) {
    function ng_get_safe_term_link($term, $taxonomy = '')
    {
        if (!$term || is_wp_error($term)) {
            return '';
        }

        $link = ($taxonomy !== '')
            ? get_term_link($term, $taxonomy)
            : get_term_link($term);

        return is_wp_error($link) ? '' : $link;
    }
}


/**
 * ------------------------------------------------------------
 * フロントページのパンくず表示名
 * ------------------------------------------------------------
 *
 * 役割:
 * - パンくず先頭に出す短い名前を固定で返す
 */
if (!function_exists('ng_get_front_page_breadcrumb_label')) {
    function ng_get_front_page_breadcrumb_label()
    {
        return 'ホーム';
    }
}


/**
 * ------------------------------------------------------------
 * パンくず配列を HTML として出力する
 * ------------------------------------------------------------
 *
 * 役割:
 * - Schema.org の BreadcrumbList 形式で出力する
 * - 通常は最後の項目だけリンクを外す
 * - フロントページだけは「ホーム」を <a> のまま出して
 *   既存CSSのホームアイコンを効かせる
 */
if (!function_exists('ng_breadcrumb_render')) {
    function ng_breadcrumb_render($items)
    {
        if (empty($items)) {
            return;
        }

        $html = '<div id="breadcrumb" class="clearfix" itemscope itemtype="https://schema.org/BreadcrumbList">';
        $html .= '<ol>';

        $position = 1;
        $last_index = count($items) - 1;

        /**
         * フロントページのみ
         * 「ホーム」1件だけでも <a> のまま出す
         */
        $is_front_only = is_front_page() && count($items) === 1;

        foreach ($items as $index => $item) {
            $html .= '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';

            if (!empty($item['url']) && ($index !== $last_index || $is_front_only)) {
                $html .= '<a href="' . esc_url($item['url']) . '" itemprop="item">';
                $html .= '<span itemprop="name">' . esc_html($item['name']) . '</span>';
                $html .= '</a>';
            } else {
                $html .= '<span itemprop="name">' . esc_html($item['name']) . '</span>';
            }

            $html .= '<meta itemprop="position" content="' . absint($position) . '" />';
            $html .= '</li>';

            $position++;
        }

        $html .= '</ol>';
        $html .= '</div>';

        echo $html;
    }
}


/**
 * ------------------------------------------------------------
 * 投稿タイプアーカイブの表示名を返す
 * ------------------------------------------------------------
 *
 * 役割:
 * - 投稿タイプごとの親ラベル名を統一する
 * - パンくずの親項目として使う
 */
if (!function_exists('ng_get_post_type_archive_label')) {
    function ng_get_post_type_archive_label($post_type)
    {
        $custom_labels = array(
            'house'       => '那須の新築・建売住宅情報',
            'blog'        => '那須の不動産コラム',
            'recruitment' => '採用情報',
        );

        if (isset($custom_labels[$post_type])) {
            return $custom_labels[$post_type];
        }

        $obj = get_post_type_object($post_type);

        if ($obj && !empty($obj->labels->name)) {
            return $obj->labels->name;
        }

        return $post_type;
    }
}


/**
 * ------------------------------------------------------------
 * 固定ページのパンくず表示名を返す
 * ------------------------------------------------------------
 *
 * 役割:
 * - 固定ページはテンプレートごとの hero 見出しを優先する
 * - 保存メタが空なら、テンプレートの既定見出しへ戻す
 * - それも無ければ固定ページタイトルを使う
 */
if (!function_exists('ng_get_page_breadcrumb_label')) {
    function ng_get_page_breadcrumb_label($post_id)
    {
        $post_id = absint($post_id);

        if (!$post_id) {
            return '';
        }

        if (get_post_type($post_id) !== 'page') {
            return get_the_title($post_id);
        }

        $template_slug = get_page_template_slug($post_id);

        $template_meta_map = array(
            'page-company.php'      => array('company_hero_title'),
            'page-nasu-guide.php'   => array('_ngu_hero_title'),
            'page-zairai.php'       => array('_zairai_hero_title'),
            'page-wakugumi.php'     => array('hero_title'),
            'page-gallery-room.php' => array('_rg_slide_hero_title_1', '_rg_hero_title'),
            'page-sekoujirei.php'   => array('ngs_sekou_hero_title'),
        );

        $template_default_title_map = array(
            'page-company.php'      => '会社概要',
            'page-gallery-room.php' => 'お部屋ギャラリー',
            'page-sekoujirei.php'   => '施工実例',
        );

        if ($template_slug && isset($template_meta_map[$template_slug])) {
            foreach ($template_meta_map[$template_slug] as $meta_key) {
                $value = get_post_meta($post_id, $meta_key, true);

                if (!is_string($value)) {
                    continue;
                }

                $value = trim(wp_strip_all_tags($value));

                if ($value !== '') {
                    return $value;
                }
            }
        }

        if ($template_slug && isset($template_default_title_map[$template_slug])) {
            return $template_default_title_map[$template_slug];
        }

        return get_the_title($post_id);
    }
}


/**
 * ------------------------------------------------------------
 * 指定タクソノミーが存在するか確認する
 * ------------------------------------------------------------
 */
if (!function_exists('ng_taxonomy_exists_safe')) {
    function ng_taxonomy_exists_safe($taxonomy)
    {
        return $taxonomy !== '' && taxonomy_exists($taxonomy);
    }
}


/**
 * ------------------------------------------------------------
 * 指定タクソノミーの中で最も深いタームを返す
 * ------------------------------------------------------------
 *
 * 役割:
 * - 複数タームが付いていても、一番深いものを代表にする
 */
if (!function_exists('ng_get_deepest_term')) {
    function ng_get_deepest_term($post_id, $taxonomy)
    {
        if (!ng_taxonomy_exists_safe($taxonomy)) {
            return null;
        }

        $terms = get_the_terms($post_id, $taxonomy);

        if (empty($terms) || is_wp_error($terms)) {
            return null;
        }

        $deepest = null;
        $max_depth = -1;

        foreach ($terms as $term) {
            $depth = count(get_ancestors($term->term_id, $taxonomy));

            if ($depth > $max_depth) {
                $max_depth = $depth;
                $deepest = $term;
            }
        }

        return $deepest;
    }
}


/**
 * ------------------------------------------------------------
 * 候補タクソノミー群から最初に見つかった最深タームを返す
 * ------------------------------------------------------------
 *
 * 役割:
 * - 同用途なのに名前が違うタクソノミーを吸収する
 * - recruitment 系で使用
 */
if (!function_exists('ng_get_first_deepest_term')) {
    function ng_get_first_deepest_term($post_id, $taxonomies)
    {
        if (!is_array($taxonomies)) {
            $taxonomies = array($taxonomies);
        }

        foreach ($taxonomies as $taxonomy) {
            $term = ng_get_deepest_term($post_id, $taxonomy);

            if ($term) {
                return $term;
            }
        }

        return null;
    }
}


/**
 * ------------------------------------------------------------
 * タームの親階層をパンくずに追加する
 * ------------------------------------------------------------
 *
 * 役割:
 * - 親 > 子 > 孫 の順にパンくずへ追加する
 */
if (!function_exists('ng_add_term_ancestors_to_breadcrumb')) {
    function ng_add_term_ancestors_to_breadcrumb(&$items, $term, $taxonomy)
    {
        if (!$term || is_wp_error($term) || !ng_taxonomy_exists_safe($taxonomy)) {
            return;
        }

        $ancestors = array_reverse(get_ancestors($term->term_id, $taxonomy));

        foreach ($ancestors as $ancestor_id) {
            $ancestor = get_term($ancestor_id, $taxonomy);

            if ($ancestor && !is_wp_error($ancestor)) {
                ng_breadcrumb_add_item(
                    $items,
                    $ancestor->name,
                    ng_get_safe_term_link($ancestor, $taxonomy)
                );
            }
        }
    }
}


/**
 * ------------------------------------------------------------
 * タクソノミーに対応する親投稿タイプを返す
 * ------------------------------------------------------------
 *
 * 役割:
 * - カスタムタクソノミーアーカイブのとき、
 *   先頭にどの投稿タイプアーカイブを出すか決める
 */
if (!function_exists('ng_get_taxonomy_parent_post_type')) {
    function ng_get_taxonomy_parent_post_type($taxonomy)
    {
        $map = array(
            'blog_genre'           => 'blog',
            'house-type'           => 'house',
            'region'               => 'house',
            'parent_taxonomy'      => 'house',
            'recruitment_category' => 'recruitment',
            'recruitment_tag'      => 'recruitment',
            'job_category'         => 'recruitment',
            'job_categories'       => 'recruitment',
            'job_tag'              => 'recruitment',
            'job_tags'             => 'recruitment',
        );

        if (isset($map[$taxonomy])) {
            return $map[$taxonomy];
        }

        $tax_obj = get_taxonomy($taxonomy);

        if ($tax_obj && !empty($tax_obj->object_type)) {
            foreach ($tax_obj->object_type as $post_type) {
                if (post_type_exists($post_type) && get_post_type_archive_link($post_type)) {
                    return $post_type;
                }
            }
        }

        return '';
    }
}


/**
 * ------------------------------------------------------------
 * 現在の投稿タイプアーカイブ対象を返す
 * ------------------------------------------------------------
 */
if (!function_exists('ng_get_current_archive_post_type')) {
    function ng_get_current_archive_post_type()
    {
        $post_type = get_query_var('post_type');

        if (is_array($post_type)) {
            $post_type = reset($post_type);
        }

        if (!empty($post_type)) {
            return $post_type;
        }

        $obj = get_queried_object();

        if ($obj && !empty($obj->name) && post_type_exists($obj->name)) {
            return $obj->name;
        }

        return '';
    }
}


/**
 * ------------------------------------------------------------
 * 指定投稿タイプの親アーカイブをパンくずへ追加する
 * ------------------------------------------------------------
 *
 * 役割:
 * - blog / house / recruitment などの親一覧を共通処理で追加する
 */
if (!function_exists('ng_add_post_type_archive_to_breadcrumb')) {
    function ng_add_post_type_archive_to_breadcrumb(&$items, $post_type)
    {
        if (!$post_type || !post_type_exists($post_type)) {
            return;
        }

        $archive_link = get_post_type_archive_link($post_type);
        $archive_name = ng_get_post_type_archive_label($post_type);

        if ($archive_link) {
            ng_breadcrumb_add_item($items, $archive_name, $archive_link);
        } else {
            ng_breadcrumb_add_item($items, $archive_name);
        }
    }
}


/**
 * ------------------------------------------------------------
 * パンくず本体
 * ------------------------------------------------------------
 *
 * 役割:
 * - 各ページ種別ごとにパンくず配列を組み立てる
 * - 最後に ng_breadcrumb_render() で出力する
 */
if (!function_exists('breadcrumb1')) {
    function breadcrumb1()
    {
        global $post, $wp_query;

        if (is_admin()) {
            return;
        }

        $items = array();

        /**
         * 1段目は常にホーム
         */
        ng_breadcrumb_add_item(
            $items,
            ng_get_front_page_breadcrumb_label(),
            home_url('/')
        );

        /**
         * フロントページ
         */
        if (is_front_page()) {
            ng_breadcrumb_render($items);
            return;
        }

        /**
         * 投稿一覧ページ
         */
        if (is_home()) {
            ng_breadcrumb_render($items);
            return;
        }

        /**
         * 検索結果
         */
        if (is_search()) {
            $search_query = get_search_query();

            $house_type = isset($_GET['house_type']) && $_GET['house_type'] !== ''
                ? get_term_by('slug', sanitize_text_field(wp_unslash($_GET['house_type'])), 'house-type')
                : null;

            $region = isset($_GET['region']) && $_GET['region'] !== ''
                ? get_term_by('slug', sanitize_text_field(wp_unslash($_GET['region'])), 'region')
                : null;

            if ($house_type || $region) {
                $label = '検索結果';
                $parts = array();

                if ($house_type && !is_wp_error($house_type)) {
                    $parts[] = $house_type->name;
                }

                if ($region && !is_wp_error($region)) {
                    $parts[] = $region->name;
                }

                ng_breadcrumb_add_item($items, $label . ': ' . implode(' / ', $parts));
            } elseif ($search_query === '') {
                ng_breadcrumb_add_item($items, '全ての検索結果');
            } elseif (isset($wp_query->found_posts) && (int) $wp_query->found_posts < 1) {
                ng_breadcrumb_add_item($items, '検索結果なし: ' . $search_query);
            } else {
                ng_breadcrumb_add_item($items, '検索結果: ' . $search_query);
            }

            ng_breadcrumb_render($items);
            return;
        }

        /**
         * 404
         */
        if (is_404()) {
            ng_breadcrumb_add_item($items, '404 Not Found');
            ng_breadcrumb_render($items);
            return;
        }


        /* =====================================================
         * IEZUKURI_FIXED_URL_BREADCRUMB_START
         *
         * 【この条件分岐の役割】
         * /iezukuri/配下にある固定ページの
         * パンくず階層を組み立てる。
         *
         * 【表示例】
         *
         * 家づくりトップ:
         * ホーム ＞ 那須の注文住宅
         *
         * ご相談・資料請求:
         * ホーム ＞ 那須の注文住宅 ＞ ご相談・資料請求
         *
         * 二世帯住宅:
         * ホーム ＞ 那須の注文住宅 ＞ 二世帯住宅
         *
         * 【重要】
         * WordPress管理画面の親ページ設定だけに依存せず、
         * 実際のアクセスURLも使って家づくりページを判定する。
         * ===================================================== */


        /*
         * $ng_iezukuri_request_uri
         *
         * 現在アクセスしているURLを保存する変数。
         */
        $ng_iezukuri_request_uri =
            isset($_SERVER['REQUEST_URI'])
                ? wp_unslash($_SERVER['REQUEST_URI'])
                : '';


        /*
         * $ng_iezukuri_request_path
         *
         * URLからドメイン、クエリ文字列、前後の
         * スラッシュを除いたパス。
         *
         * 例:
         * /iezukuri/contact/?test=1
         * ↓
         * iezukuri/contact
         */
        $ng_iezukuri_request_path = trim(
            (string) wp_parse_url(
                $ng_iezukuri_request_uri,
                PHP_URL_PATH
            ),
            '/'
        );


        /*
         * $ng_is_iezukuri_fixed_page
         *
         * 現在のページが固定ページであり、
         * 実際のURLが/iezukuri/以下ならtrue。
         */
        $ng_is_iezukuri_fixed_page =
            is_page()
            && (
                $ng_iezukuri_request_path === 'iezukuri'
                || strpos(
                    $ng_iezukuri_request_path,
                    'iezukuri/'
                ) === 0
            );


        if ($ng_is_iezukuri_fixed_page) {
            /*
             * $current_page_id
             *
             * 現在表示している固定ページのID。
             */
            $current_page_id =
                get_queried_object_id();


            /*
             * $iezukuri_root_page
             *
             * スラッグ「iezukuri」の家づくりトップ固定ページ。
             */
            $iezukuri_root_page =
                get_page_by_path(
                    'iezukuri',
                    OBJECT,
                    'page'
                );


            /*
             * $iezukuri_root_id
             *
             * 家づくりトップ固定ページの投稿ID。
             */
            $iezukuri_root_id =
                $iezukuri_root_page instanceof WP_Post
                    ? (int) $iezukuri_root_page->ID
                    : 0;


            /*
             * $iezukuri_root_url
             *
             * 家づくりトップへのリンクURL。
             *
             * 固定ページを取得できなかった場合も、
             * /iezukuri/を予備URLとして使う。
             */
            $iezukuri_root_url =
                $iezukuri_root_id
                    ? get_permalink($iezukuri_root_id)
                    : home_url('/iezukuri/');


            /*
             * $iezukuri_root_label
             *
             * パンくずへ表示する家づくりトップの名前。
             *
             * ng_get_page_breadcrumb_label()は、
             * ページタイトルや保存済みhero見出しから
             * 適切な表示名を返す既存関数。
             */
            if (
                $iezukuri_root_id
                && function_exists(
                    'ng_get_page_breadcrumb_label'
                )
            ) {
                $iezukuri_root_label =
                    ng_get_page_breadcrumb_label(
                        $iezukuri_root_id
                    );
            } elseif ($iezukuri_root_id) {
                $iezukuri_root_label =
                    get_the_title($iezukuri_root_id);
            } else {
                $iezukuri_root_label =
                    '那須の注文住宅';
            }

            if ($iezukuri_root_label === '') {
                $iezukuri_root_label =
                    '那須の注文住宅';
            }


            /*
             * $current_page_label
             *
             * 現在表示している固定ページのパンくず名。
             */
            if (
                $current_page_id
                && function_exists(
                    'ng_get_page_breadcrumb_label'
                )
            ) {
                $current_page_label =
                    ng_get_page_breadcrumb_label(
                        $current_page_id
                    );
            } else {
                $current_page_label =
                    get_the_title($current_page_id);
            }

            if ($current_page_label === '') {
                $current_page_label =
                    '現在のページ';
            }


            /*
             * 家づくりトップ自身を表示している場合。
             *
             * $itemsにはすでに「ホーム」が入っているので、
             * 現在地として家づくりトップ名だけを追加する。
             */
            if (
                $ng_iezukuri_request_path === 'iezukuri'
                || (
                    $iezukuri_root_id
                    && $current_page_id === $iezukuri_root_id
                )
            ) {
                ng_breadcrumb_add_item(
                    $items,
                    $iezukuri_root_label
                );

                ng_breadcrumb_render($items);
                return;
            }


            /*
             * 家づくりサブページの場合。
             *
             * 2階層目へ家づくりトップをリンクとして追加する。
             */
            ng_breadcrumb_add_item(
                $items,
                $iezukuri_root_label,
                $iezukuri_root_url
            );


            /*
             * $ancestor_ids
             *
             * WordPress管理画面で親ページが設定されている場合の
             * 親固定ページID一覧。
             *
             * 古い親から順番に並べ替えて追加する。
             */
            $ancestor_ids = array_reverse(
                get_post_ancestors($current_page_id)
            );

            foreach ($ancestor_ids as $ancestor_id) {
                /*
                 * 家づくりトップはすでに追加したため、
                 * 同じ項目を二重に表示しない。
                 */
                if (
                    (int) $ancestor_id
                    === $iezukuri_root_id
                ) {
                    continue;
                }

                $ancestor_label =
                    function_exists(
                        'ng_get_page_breadcrumb_label'
                    )
                        ? ng_get_page_breadcrumb_label(
                            $ancestor_id
                        )
                        : get_the_title($ancestor_id);

                if ($ancestor_label === '') {
                    continue;
                }

                ng_breadcrumb_add_item(
                    $items,
                    $ancestor_label,
                    get_permalink($ancestor_id)
                );
            }


            /*
             * 最後に現在のページを追加する。
             *
             * 現在地なのでリンクURLは付けない。
             */
            ng_breadcrumb_add_item(
                $items,
                $current_page_label
            );


            /*
             * 完成した配列をパンくずHTMLとして出力する。
             */
            ng_breadcrumb_render($items);

            /*
             * 家づくり固定ページ用の処理が完了したため、
             * 後ろにある通常固定ページ処理へ進ませない。
             */
            return;
        }

        /* IEZUKURI_FIXED_URL_BREADCRUMB_END */

        /**
         * IEZ_PLAN_ARCHIVE_BREADCRUMB_START
         * --------------------------------------------------
         * 間取り一覧ページ
         * --------------------------------------------------
         *
         * is_post_type_archive('iez_plan')
         *
         * 現在表示しているページが、
         * iez_planカスタム投稿の一覧か判定する。
         *
         * 表示:
         * ホーム ＞ 那須の注文住宅 ＞ 間取り
         */
        if (is_post_type_archive('iez_plan')) {
            /*
             * $iezukuri_page
             *
             * スラッグ「iezukuri」の固定ページ情報。
             */
            $iezukuri_page = get_page_by_path(
                'iezukuri',
                OBJECT,
                'page'
            );

            /*
             * $iezukuri_url
             *
             * 家づくりトップへのURL。
             */
            $iezukuri_url =
                $iezukuri_page instanceof WP_Post
                    ? get_permalink($iezukuri_page->ID)
                    : home_url('/iezukuri/');

            /*
             * $iezukuri_label
             *
             * 家づくりトップのパンくず表示名。
             *
             * 既存の専用関数がある場合はそれを使い、
             * なければ固定ページタイトルを使う。
             */
            if (
                $iezukuri_page instanceof WP_Post
                && function_exists(
                    'ng_get_page_breadcrumb_label'
                )
            ) {
                $iezukuri_label =
                    ng_get_page_breadcrumb_label(
                        $iezukuri_page->ID
                    );
            } elseif (
                $iezukuri_page instanceof WP_Post
            ) {
                $iezukuri_label =
                    get_the_title(
                        $iezukuri_page->ID
                    );
            } else {
                $iezukuri_label =
                    '那須の注文住宅';
            }

            if ($iezukuri_label === '') {
                $iezukuri_label =
                    '那須の注文住宅';
            }

            /*
             * 家づくりトップをリンクとして追加。
             */
            ng_breadcrumb_add_item(
                $items,
                $iezukuri_label,
                $iezukuri_url
            );

            /*
             * 現在地「間取り」を追加。
             * 最後の項目なのでリンクは付けない。
             */
            ng_breadcrumb_add_item(
                $items,
                '間取り'
            );

            ng_breadcrumb_render($items);
            return;
        }
        /* IEZ_PLAN_ARCHIVE_BREADCRUMB_END */


        /**
         * 投稿タイプアーカイブ
         */
        if (is_post_type_archive()) {
            $post_type = ng_get_current_archive_post_type();

            if ($post_type) {
                ng_add_post_type_archive_to_breadcrumb($items, $post_type);
            }

            ng_breadcrumb_render($items);
            return;
        }

        /**
         * カテゴリーアーカイブ
         *
         * ここは通常投稿専用として扱う。
         * blog から category を外したため、
         * もう blog 文脈判定は不要。
         */
        if (is_category()) {
            $cat = get_queried_object();

            if ($cat && !is_wp_error($cat)) {
                $ancestors = array_reverse(get_ancestors($cat->term_id, 'category'));

                foreach ($ancestors as $ancestor_id) {
                    $ancestor = get_category($ancestor_id);

                    if ($ancestor && !is_wp_error($ancestor)) {
                        ng_breadcrumb_add_item(
                            $items,
                            $ancestor->name,
                            get_category_link($ancestor->term_id)
                        );
                    }
                }

                ng_breadcrumb_add_item($items, $cat->name);
            }

            ng_breadcrumb_render($items);
            return;
        }

        /**
         * タグアーカイブ
         */
        if (is_tag()) {
            ng_breadcrumb_add_item($items, single_tag_title('', false));
            ng_breadcrumb_render($items);
            return;
        }

        /**
         * カスタムタクソノミーアーカイブ
         *
         * 例:
         * - blog_genre アーカイブ
         * - house-type アーカイブ
         * - region アーカイブ
         */
        if (is_tax()) {
            $term = get_queried_object();

            if ($term && !is_wp_error($term)) {
                $parent_post_type = ng_get_taxonomy_parent_post_type($term->taxonomy);

                if ($parent_post_type) {
                    ng_add_post_type_archive_to_breadcrumb($items, $parent_post_type);
                }

                ng_add_term_ancestors_to_breadcrumb($items, $term, $term->taxonomy);
                ng_breadcrumb_add_item($items, $term->name);
            }

            ng_breadcrumb_render($items);
            return;
        }

        /**
         * 日付アーカイブ
         */
        if (is_date()) {
            if (is_year()) {
                ng_breadcrumb_add_item($items, get_query_var('year') . '年');
            } elseif (is_month()) {
                ng_breadcrumb_add_item($items, get_query_var('year') . '年', get_year_link(get_query_var('year')));
                ng_breadcrumb_add_item($items, get_query_var('monthnum') . '月');
            } elseif (is_day()) {
                ng_breadcrumb_add_item($items, get_query_var('year') . '年', get_year_link(get_query_var('year')));
                ng_breadcrumb_add_item($items, get_query_var('monthnum') . '月', get_month_link(get_query_var('year'), get_query_var('monthnum')));
                ng_breadcrumb_add_item($items, get_query_var('day') . '日');
            }

            ng_breadcrumb_render($items);
            return;
        }

        /**
         * 投稿者アーカイブ
         */
        if (is_author()) {
            $author = get_queried_object();

            if ($author && !is_wp_error($author)) {
                ng_breadcrumb_add_item($items, $author->display_name);
            }

            ng_breadcrumb_render($items);
            return;
        }

        /**
         * 固定ページ
         */
        if (is_page()) {
            if ($post && $post->post_parent) {
                $ancestors = array_reverse(get_post_ancestors($post->ID));

                foreach ($ancestors as $ancestor_id) {
                    $ancestor_post = get_post($ancestor_id);

                    if ($ancestor_post) {
                        ng_breadcrumb_add_item(
                            $items,
                            ng_get_page_breadcrumb_label($ancestor_post->ID),
                            get_permalink($ancestor_post->ID)
                        );
                    }
                }
            }

            if ($post) {
                ng_breadcrumb_add_item(
                    $items,
                    ng_get_page_breadcrumb_label($post->ID)
                );
            }

            ng_breadcrumb_render($items);
            return;
        }

        /**
         * 添付ファイル
         */
        if (is_attachment()) {
            if ($post && $post->post_parent) {
                $parent_id = $post->post_parent;
                $parent_type = get_post_type($parent_id);

                if ($parent_type === 'post') {
                    $cat = ng_get_deepest_term($parent_id, 'category');

                    if ($cat) {
                        $ancestors = array_reverse(get_ancestors($cat->term_id, 'category'));

                        foreach ($ancestors as $ancestor_id) {
                            $ancestor = get_category($ancestor_id);

                            if ($ancestor && !is_wp_error($ancestor)) {
                                ng_breadcrumb_add_item(
                                    $items,
                                    $ancestor->name,
                                    get_category_link($ancestor->term_id)
                                );
                            }
                        }

                        ng_breadcrumb_add_item($items, $cat->name);
                    }
                } else {
                    ng_add_post_type_archive_to_breadcrumb($items, $parent_type);
                }
            }

            ng_breadcrumb_render($items);
            return;
        }

        /**
         * 個別投稿
         *
         * 役割:
         * - post / blog / house / recruitment ごとに分岐する
         */
        if (is_singular()) {
            $post_type = get_post_type();

            /**
             * IEZ_PLAN_SINGLE_BREADCRUMB_IN_BREAD1_START
             * --------------------------------------------------
             * 間取り詳細ページ
             * --------------------------------------------------
             *
             * $post_type === 'iez_plan'
             *
             * 現在表示している個別投稿の投稿タイプが
             * iez_planかを判定する。
             *
             * 表示:
             * ホーム
             * ＞ 那須の注文住宅
             * ＞ 間取り
             * ＞ 現在の間取りタイトル
             */
            if ($post_type === 'iez_plan') {
                /*
                 * 家づくりトップ固定ページ。
                 */
                $iezukuri_page = get_page_by_path(
                    'iezukuri',
                    OBJECT,
                    'page'
                );

                /*
                 * 家づくりトップURL。
                 */
                $iezukuri_url =
                    $iezukuri_page instanceof WP_Post
                        ? get_permalink(
                            $iezukuri_page->ID
                        )
                        : home_url('/iezukuri/');

                /*
                 * 家づくりトップの表示名。
                 */
                if (
                    $iezukuri_page instanceof WP_Post
                    && function_exists(
                        'ng_get_page_breadcrumb_label'
                    )
                ) {
                    $iezukuri_label =
                        ng_get_page_breadcrumb_label(
                            $iezukuri_page->ID
                        );
                } elseif (
                    $iezukuri_page instanceof WP_Post
                ) {
                    $iezukuri_label =
                        get_the_title(
                            $iezukuri_page->ID
                        );
                } else {
                    $iezukuri_label =
                        '那須の注文住宅';
                }

                if ($iezukuri_label === '') {
                    $iezukuri_label =
                        '那須の注文住宅';
                }

                /*
                 * 間取り一覧のURL。
                 *
                 * get_post_type_archive_link()
                 * = カスタム投稿一覧URLを取得する関数。
                 */
                $plan_archive_url =
                    get_post_type_archive_link(
                        'iez_plan'
                    );

                if (!$plan_archive_url) {
                    $plan_archive_url =
                        home_url(
                            '/iezukuri/plans/'
                        );
                }

                /*
                 * 現在表示している間取り投稿のタイトル。
                 */
                $plan_title = get_the_title(
                    get_queried_object_id()
                );

                if ($plan_title === '') {
                    $plan_title =
                        '間取り詳細';
                }

                /*
                 * 家づくりトップを追加。
                 */
                ng_breadcrumb_add_item(
                    $items,
                    $iezukuri_label,
                    $iezukuri_url
                );

                /*
                 * 間取り一覧へのリンクを追加。
                 */
                ng_breadcrumb_add_item(
                    $items,
                    '間取り',
                    $plan_archive_url
                );

                /*
                 * 現在の間取りタイトルを追加。
                 * 最後の項目なのでリンクは付けない。
                 */
                ng_breadcrumb_add_item(
                    $items,
                    $plan_title
                );

                ng_breadcrumb_render($items);
                return;
            }
            /* IEZ_PLAN_SINGLE_BREADCRUMB_IN_BREAD1_END */


            /**
             * 通常投稿
             *
             * category を親階層として使う
             */
            if ($post_type === 'post') {
                $cat = ng_get_deepest_term($post->ID, 'category');

                if ($cat) {
                    $ancestors = array_reverse(get_ancestors($cat->term_id, 'category'));

                    foreach ($ancestors as $ancestor_id) {
                        $ancestor = get_category($ancestor_id);

                        if ($ancestor && !is_wp_error($ancestor)) {
                            ng_breadcrumb_add_item(
                                $items,
                                $ancestor->name,
                                get_category_link($ancestor->term_id)
                            );
                        }
                    }

                    ng_breadcrumb_add_item($items, $cat->name);
                }

                ng_breadcrumb_add_item($items, get_the_title($post->ID));
                ng_breadcrumb_render($items);
                return;
            }

            /**
             * blog 投稿
             *
             * 出したい形:
             * - ホーム
             * - 那須の不動産コラム
             * - blog_genre の親子
             * - 記事タイトル
             *
             * 重要:
             * - blog では category を使わない
             * - そのため、Google に category を親として拾わせにくくする
             */
            if ($post_type === 'blog') {
                ng_add_post_type_archive_to_breadcrumb($items, 'blog');

                $term = ng_get_deepest_term($post->ID, 'blog_genre');

                if ($term) {
                    ng_add_term_ancestors_to_breadcrumb($items, $term, 'blog_genre');
                    ng_breadcrumb_add_item($items, $term->name, ng_get_safe_term_link($term, 'blog_genre'));
                }

                ng_breadcrumb_add_item($items, get_the_title($post->ID));
                ng_breadcrumb_render($items);
                return;
            }

            /**
             * house 投稿
             */
            if ($post_type === 'house') {
                ng_add_post_type_archive_to_breadcrumb($items, 'house');

                $house_type = ng_get_deepest_term($post->ID, 'house-type');
                $region = ng_get_deepest_term($post->ID, 'region');

                if ($house_type) {
                    ng_add_term_ancestors_to_breadcrumb($items, $house_type, 'house-type');
                    ng_breadcrumb_add_item($items, $house_type->name, ng_get_safe_term_link($house_type, 'house-type'));
                }

                if ($region) {
                    ng_add_term_ancestors_to_breadcrumb($items, $region, 'region');
                    ng_breadcrumb_add_item($items, $region->name, ng_get_safe_term_link($region, 'region'));
                }

                ng_breadcrumb_add_item($items, get_the_title($post->ID));
                ng_breadcrumb_render($items);
                return;
            }

            /**
             * recruitment 投稿
             */
            if ($post_type === 'recruitment') {
                ng_add_post_type_archive_to_breadcrumb($items, 'recruitment');

                $recruitment_taxonomies = array(
                    'recruitment_category',
                    'job_category',
                    'job_categories',
                );

                $term = ng_get_first_deepest_term($post->ID, $recruitment_taxonomies);

                if ($term) {
                    ng_add_term_ancestors_to_breadcrumb($items, $term, $term->taxonomy);
                    ng_breadcrumb_add_item($items, $term->name, ng_get_safe_term_link($term, $term->taxonomy));
                }

                ng_breadcrumb_add_item($items, get_the_title($post->ID));
                ng_breadcrumb_render($items);
                return;
            }

            /**
             * その他のカスタム投稿
             */
            ng_add_post_type_archive_to_breadcrumb($items, $post_type);
            ng_breadcrumb_add_item($items, get_the_title($post->ID));
            ng_breadcrumb_render($items);
            return;
        }

        /**
         * その他
         */
        ng_breadcrumb_render($items);
    }
}
