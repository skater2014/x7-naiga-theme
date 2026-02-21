<?php

/****************************************

        functions.php

        テーマ内で利用する関数を定義したり、
        テーマの設定を行うためのファイルです。

        functions.php のコードに関しては、
        CHAPTER 11, 12 で詳しく解説しています。

 *****************************************/

// functions.phpファイルに追加するコード

//手順1,　wordpress 管理画面パーマリンク　カテゴリー非表示　/%postname%/　→　投稿やページのスラッグだけが URL に表示
//手順2, 下記のコードを作成。カテゴリーページのURLから
//手順3, index.php 複製したcategory.php 作成　カテゴリーのタイトルに合わせた固定ページでカテゴリ用のテンプレレートを読み込ませる。
//手順4, 必ずwordpress 管理画面パーマリンク設定ボタンを保存する
//カテゴリーページのURLから /category/ が取り除かれ、よりクリーンでシンプルなURLが実現
//手順5, 必ず下記のコードの動作をパーマリンク設定を教えてcategory url 消えたか確認する。　
// レンタルサーバーでの設定は関係ない。wordpress 管理画面で完結


//function remove_category_base() {
//global $wp_rewrite;

// カスタム構造が設定されているか確認
//if (isset($wp_rewrite->extra_permastructs['category']['struct'])) {
//$wp_rewrite->extra_permastructs['category']['struct'] = '%category%';

// 変更を適用するためにリライトルールをフラッシュ
//flush_rewrite_rules();
//}
//}

//add_action('init', 'remove_category_base');



// functions.php
if (!function_exists('is_mobile_device')) {
    function is_mobile_device()
    {
        // WP標準の判定を使用（スマホ・タブレットをモバイル扱い）
        return wp_is_mobile();
    }
}


// ✅ 管理バー（上部ツールバー）に「物件チェック」メニューを追加
add_action('admin_bar_menu', function ($wp_admin_bar) {
    if (current_user_can('administrator')) {
        $wp_admin_bar->add_node([
            'id' => 'check-houses',
            'title' => '物件チェック',
            'href' => admin_url('admin.php?page=check-houses') // 管理画面のカスタムページへのリンク
        ]);
    }
}, 100);

// ✅ 管理画面メニューに「物件チェック」を追加
add_action('admin_menu', function () {
    add_menu_page(
        '物件チェック',    // ページタイトル
        '物件チェック',    // メニュータイトル
        'manage_options', // 権限
        'check-houses',   // スラッグ
        'render_house_debug_page' // 表示用関数
    );
});

// ✅ 物件データ確認ページの表示関数
function render_house_debug_page()
{
    global $wpdb;

    // SQLで投稿一覧を取得（投稿タイプ：post または house）
    $results = $wpdb->get_results("
        SELECT 
            p.ID, 
            p.post_title, 
            p.post_type, 
            p.post_status,
            GROUP_CONCAT(t.name) AS categories, -- カテゴリをカンマで連結
            pm.meta_value AS price              -- カスタムフィールド「Price」
        FROM {$wpdb->posts} p
        LEFT JOIN {$wpdb->term_relationships} tr ON tr.object_id = p.ID
        LEFT JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
        LEFT JOIN {$wpdb->terms} t ON tt.term_id = t.term_id AND tt.taxonomy = 'category'
        LEFT JOIN {$wpdb->postmeta} pm ON pm.post_id = p.ID AND pm.meta_key = 'Price'
        WHERE p.post_status = 'publish'
        AND p.post_type IN ('post', 'house')
        GROUP BY p.ID
        ORDER BY p.ID DESC
        LIMIT 50
    ");

    // 管理画面に表示
    echo '<div class="wrap"><h1>物件データ確認</h1>';
    echo '<table class="widefat"><thead><tr><th>ID</th><th>タイトル</th><th>投稿タイプ</th><th>カテゴリ</th><th>価格</th></tr></thead><tbody>';

    foreach ($results as $r) {
        // 編集画面リンクの取得（念のため null 対応）
        $edit_link = get_edit_post_link($r->ID);
        $title_display = esc_html($r->post_title);

        // ❗リンクが null の場合はタイトルのみ表示
        if ($edit_link) {
            $title_html = "<a href='" . esc_url($edit_link) . "' target='_blank'>{$title_display}</a>";
        } else {
            $title_html = $title_display . '（リンクなし）';
        }

        // 価格表示処理（空なら「なし」）
        $price_display = empty($r->price) ? '（なし）' : esc_html($r->price) . ' 万円';

        echo '<tr>';
        echo '<td>' . intval($r->ID) . '</td>';
        echo '<td>' . $title_html . '</td>';
        echo '<td>' . esc_html($r->post_type) . '</td>';
        echo '<td>' . esc_html($r->categories) . '</td>';
        echo '<td>' . $price_display . '</td>';
        echo '</tr>';
    }

    echo '</tbody></table></div>';
}

// 特定のメニューアイテム（mega-menu-item クラスが付いたもの）の sub-menu を mega-sub-menu に変換し、アイキャッチ画像や YouTube/Vimeo 動画を追加する仕組み
function convert_submenu_to_megamenu($menu, $args)
{
    // 画像がない場合のデフォルト画像
    $noimage = get_template_directory_uri() . '/images/noimage.gif';

    // 「NEW」ラベルを表示する日数
    $days = 14;
    $now = date_i18n('U'); // 現在のUNIXタイムスタンプを取得

    // 🌟 `mega-menu-item` の `sub-menu` を `mega-sub-menu` に変更
    $menu = preg_replace_callback(
        '/(<li[^>]*class="[^"]*mega-menu-item[^"]*"[^>]*>.*?<ul[^>]*class=")sub-menu("[^>]*>)/s',
        function ($matches) {
            return $matches[1] . 'mega-sub-menu' . $matches[2]; // `sub-menu` を `mega-sub-menu` に置き換える
        },
        $menu
    );

    // 🌟 変換後の `mega-sub-menu` をすべて取得
    preg_match_all('/<ul[^>]*class="mega-sub-menu"[^>]*>(.*?)<\/ul>/s', $menu, $submenu_matches);

    // 🌟 `theme_location` が設定されていて、存在する場合にメニューを取得
    if (isset($args->theme_location) && array_key_exists($args->theme_location, get_nav_menu_locations())) {
        // `get_nav_menu_locations()` は、管理画面で登録されたメニューの一覧を取得する
        $menu_items = wp_get_nav_menu_items(get_nav_menu_locations()[$args->theme_location]);
    } else {
        $menu_items = []; // メニューが存在しない場合は空の配列を設定
    }

    // 🌟 取得した `mega-sub-menu` の各項目を処理
    foreach ($submenu_matches[1] as $submenu_content) {
        // 各メニュー項目の ID を取得
        preg_match_all('/<li[^>]*id="menu-item-(\d+)"[^>]*class="menu-item[^"]*"[^>]*>/', $submenu_content, $item_matches);

        $content = ''; // `mega-sub-menu` の新しいコンテンツ

        foreach ($item_matches[1] as $menu_item_id) {
            foreach ($menu_items as $item) {
                if ($item->ID == $menu_item_id && $item->object_id) {
                    $post_id = $item->object_id; // メニューアイテムに紐づく投稿のID
                    $entry = get_the_time('U', $post_id); // 記事の公開日
                    $term = ($now - $entry) / 86400; // 記事が何日前に投稿されたか（1日=86400秒）

                    // 🌟 投稿のカスタムフィールドから動画情報を取得
                    $type = get_post_meta($post_id, 'page_featured_type', true);
                    $video_id = get_post_meta($post_id, 'page_video_id', true);

                    $media_html = ''; // メディア（動画・画像）のHTML

                    // 🌟 YouTube動画を埋め込む
                    if ($type == 'youtube' && !empty($video_id)) {
                        $media_html = '<div class="blog-post-media">
                            <lite-youtube videoid="' . esc_attr($video_id) . '"></lite-youtube>
                        </div>';
                    }
                    // 🌟 Vimeo動画を埋め込む
                    elseif ($type == 'vimeo' && !empty($video_id)) {
                        $media_html = '<div class="blog-post-media">
                            <lite-vimeo videoid="' . esc_attr($video_id) . '">
                                <div class="ltv-playbtn"></div>
                            </lite-vimeo>
                        </div>';
                    }
                    // 🌟 アイキャッチ画像を表示（オーバーレイ追加）
                    else {
                        $thumbnail_url = get_the_post_thumbnail_url($post_id, 'full'); // `full`サイズの画像を取得
                        if (!$thumbnail_url) {
                            $thumbnail_url = esc_url($noimage); // 画像がない場合、デフォルト画像を使用
                        }

                        // 画像のオーバーレイを追加
                        $media_html = '<div class="blog-post-media" style="position: relative;">
                            <img src="' . esc_url($thumbnail_url) . '" alt="' . esc_attr(get_the_title($post_id)) . '">
                            <div class="thumbnail-overlay" style="position: absolute; bottom: 0; left: 0; width: 100%; background: rgba(0, 0, 0, 0.5); color: white; text-align: center; padding: 10px;">
                                <h3>' . esc_html(get_the_title($post_id)) . '</h3> <!-- 🔥 記事のタイトルを動的に取得！ -->
                            </div>
                        </div>';
                    }

                    // 🌟 NEWマークを表示する（$days 以内に投稿された記事）
                    $new_label = ($term < $days) ? '<div class="ribbon ribbon-top-left"><span>New</span></div>' : '';

                    // 🌟 記事タイトルをリンク付きで表示
                    $title_html = '<a href="' . esc_url(get_permalink($post_id)) . '" class="blog-post-title">' . esc_html(get_the_title($post_id)) . '</a>';

                    // 🌟 `mega-sub-menu` にアイテム追加
                    $content .= '<li class="menu-item">' . $new_label . $media_html . $title_html . '</li>';
                }
            }
        }

        // 🌟 `mega-sub-menu` のコンテンツを更新
        if (!empty($content)) {
            $menu = str_replace($submenu_content, $content, $menu);
        }
    }

    return $menu;
}

// 🌟 `wp_nav_menu` フィルターを適用
add_filter('wp_nav_menu', 'convert_submenu_to_megamenu', 10, 2);





// Gutenberg Editor 投稿画面内にCSS適用
// ブロックエディター用のスタイルシートを読み込む
// Add support for Block Styles.
add_theme_support('wp-block-styles');

// Add support for responsive embeds.
add_theme_support('responsive-embeds');

// Add support for full and wide align images.
add_theme_support('align-wide');

// 挿入した動画などがレスポンシブ対応（YouTubeなど）
add_theme_support('responsive-embeds');

add_theme_support('post-thumbnails');

// functions.phpでカスタムサイズを追加する
add_image_size('custom-thumbnail', 200, 150, true);


// カスタムメニュー機能を有効にする
add_theme_support('menus');

//管理画面カスタマイズ　format 
//add_theme_support( 'post-formats', array( 'aside', 'gallery','image','chat','link','quote','status','audio','video' ) );

function is_first()
{
    global $wp_query;
    return ($wp_query->current_post === 0);
}



$defaults = array(
    'default-color' => '',
    'default-image' => '',
    'default-repeat' => '',
    'default-position-x' => '',
    'default-attachment' => '',
    'wp-head-callback' => '_custom_background_cb',
    'admin-head-callback' => '',
    'admin-preview-callback' => ''
);
add_theme_support('custom-background', $defaults);

/** <head>内に RSSフィードのリンクを表示するコード */
add_theme_support('automatic-feed-links');

add_image_size('thumb640', 640, 400, true);

// house テーブル表の画像 カスタムの画像サイズを追加する
add_image_size('custom-size', 150, 100, true);

// 投稿画像の指定サイズを決める
// the_post_thumbnail(array(150, 150));

// アイキャッチ画像機能を有効にする
add_theme_support('post-thumbnails');

// 抜粋の[...]を...に変更する
function new_excerpt_more($more)
{
    return ' ... ';
}
add_filter('excerpt_more', 'new_excerpt_more');




function child_category_link_custom($query = array())
{

    if (isset($query['category_name'])) {
        if ($query['category_name'] === 'category-parent' && $query['name'] === 'category-child') {
            unset($query['name']);
            $query['category_name'] = 'category-parent/category-child';
        }
    }
    return $query;
}
add_filter('request', 'child_category_link_custom');


// カスタムメニューの「場所」を設定する

register_nav_menu('header-navi', 'ヘッダーのナビゲーション');
// register_nav_menu('sidebar-navi', 'サイドバーのナビゲーション');

register_nav_menu('footer-navi', 'フッターのナビゲーション');

//register_nav_menu('global-navigation', 'Global Navigation');


// Register Custom Navigation Walker and Custom Menus
//function register_custom_menus()
//{
// カスタムメニューのウォーカーを登録
//require_once get_template_directory() . '/Custom_Mega_Menu_Walker.php';

// ナビゲーションメニューを登録
//register_nav_menus(
//array(
//'global-navigation' => __('Global Navigation', 'global-navigation'), // メニューの識別名を変更
//'mega_menu' => __('Mega Menu', 'mega-menu'),
//)
//);
// BS5 Walkerをデフォルトウォーカーとして登録
//require_once get_template_directory() . '/bs5-navwalker.php';
//}
//add_action('after_setup_theme', 'register_custom_menus');




register_nav_menu('header-test-bootstrap', 'Header Test Bootstrap');


// HTML5 Blank navigation
// https://github.com/wp-bootstrap/wp-bootstrap-navwalker

//テーマのロケーションと一致させる。
// Register HTML5 Blank Navigation
function register_html5_nav()
{
    register_nav_menus(
        array(
            'side-menu' => __('Side Menu', 'html5blank'), // メニューの場所を 'side-menu' に変更
            // 'sidebar-menu' => __('Sidebar Menu', 'html5blank'),  Sidebar Navigation
            // 'extra-menu' => __('Extra Menu', 'html5blank')  Extra Navigation if needed (duplicate as many as you need!)
        )
    );
}

// Add action to call html5blank_nav in wp_footer
add_action('wp_footer', 'register_html5_nav');


// home.php サブネイル
if (function_exists('add_theme_support')) {
    add_theme_support('automatic-feed-links');
    add_theme_support('title-tag');
    add_image_size('home-thumb', 600, 400);
}
// ブロックエディタ用スタイル機能をテーマに追加
add_action('after_setup_theme', function () {
    add_theme_support('editor-styles');
    // ブロックエディタ用CSSの読み込み
    add_editor_style('/css/editor-style.css');
});

// WebP画像サポートを追加
function add_webp_support($content)
{
    $content['webp'] = 'image/webp';
    return $content;
}
add_filter('mime_types', 'add_webp_support');



//サブネイルの画像はアップロードしたタイミングでlazy load で画像が調整されるので、
//2回目以降での新規の画像をアップロードするには、前回と同じ方法でする必要がある。regenerstor というツールもある。


//Notice エラーメッセージが表示されるので下記の関数で回避
// widgets-クラシックエディターの有効化
//function example_theme_support() {
//remove_theme_support( 'widgets-block-editor' );
//}
//add_action( 'after_setup_theme', 'example_theme_support' );


// ブログの編集画面にブロックエディタ用CSSとJSの読み込み
add_action('admin_enqueue_scripts', function ($hook_suffix) {
    if ('post.php' === $hook_suffix || 'post-new.php' === $hook_suffix) {
        // CSSファイルの設定
        $style_path = get_template_directory() . "/css/editor-style.css";
        $style_uri = get_template_directory_uri() . "/css/editor-style.css";
        $style_version = file_exists($style_path) ? filemtime($style_path) : '1.0'; // バージョンを設定

        // JSファイルの設定
        $script_path = get_template_directory() . "/js/editor-style.js";
        $script_uri = get_template_directory_uri() . "/js/editor-style.js";
        $script_version = file_exists($script_path) ? filemtime($script_path) : '1.0'; // バージョンを設定

        // CSSファイルの読み込み
        wp_enqueue_style("smart-style", $style_uri, array(), $style_version);

        // JSファイルの読み込み
        wp_enqueue_script('smart-script', $script_uri, array(), $script_version, true);
    }
});

// ブロックエディタ用JSの読み込み
/*
$editor_script_path = get_template_directory() . "/js/editor-styles.js";
$editor_script_uri = get_template_directory_uri() . "/js/editor-style.js";
$editor_script_version = file_exists($editor_script_path) ? filemtime($editor_script_path) : null;

wp_enqueue_script('new-theme-editor-js', $editor_script_uri, ['wp-element', 'wp-rich-text', 'wp-editor'], $editor_script_version);
*/






// WordPress管理画面に管理画面のエディタ用CSSの読み込み
add_action('admin_enqueue_scripts', function ($hook_suffix) {
    // CSSファイルの設定
    $uri = get_template_directory_uri() . "/css/admin-style.css";

    // CSSファイルの読み込み
    wp_enqueue_style("admin-style", $uri, array(), wp_get_theme()->get('Version'));
});

// WordPress管理画面をCSSで編集
add_action('admin_print_styles', 'my_admin_style');

function my_admin_style()
{
    $style_path = get_template_directory() . '/css/admin-style.css';
    $style_version = filemtime($style_path);

    // CSSファイルを読み込む
    wp_enqueue_style('my-admin-style', get_template_directory_uri() . '/css/admin-style.css', array(), $style_version);
}

//jQuery UIの読み込み：
//jQuery UIのドラッグ可能（draggable）およびドロップ可能（droppable）な機能が使用可能になります
function enqueue_jquery_ui()
{
    // jQuery UI Core
    wp_enqueue_script('jquery-ui-core');

    // jQuery UI Widget (required for draggable)
    wp_enqueue_script('jquery-ui-widget', 'jquery-ui-core', array('jquery-ui-core'), '', true);

    // jQuery UI Mouse (required for draggable)
    wp_enqueue_script('jquery-ui-mouse', 'jquery-ui-widget', array('jquery-ui-widget'), '', true);

    // jQuery UI Draggable
    wp_enqueue_script('jquery-ui-draggable', 'jquery-ui-mouse', array('jquery-ui-mouse'), '', true);

    // jQuery UI Droppable
    wp_enqueue_script('jquery-ui-droppable', 'jquery-ui-draggable', array('jquery-ui-draggable'), '', true);
}

add_action('wp_enqueue_scripts', 'enqueue_jquery_ui');



//一つのアクションフックからファイルを読み込む方法
/*function my_styles() {
  wp_enqueue_style('my-style', get_theme_file_uri('/style.css'));
}
add_action( 'wp_enqueue_scripts', 'my_styles' );
*/


// Google Fonts
//Roboto、Open Sans、Lato、Montserrat、Source Sans Pro、Poppinsの各フォントファミリーをWordPressに追加します。それぞれのフォントファミリーは、400（通常）と700（太字）のウェイトが含まれていますが、必要に応じて変更
function add_google_fonts()
{
    wp_enqueue_style('google_fonts_roboto', 'https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap', array(), null);
    wp_enqueue_style('google_fonts_open_sans', 'https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;700&display=swap', array(), null);
    wp_enqueue_style('google_fonts_lato', 'https://fonts.googleapis.com/css2?family=Lato:wght@400;700&display=swap', array(), null);
    wp_enqueue_style('google_fonts_montserrat', 'https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap', array(), null);
    wp_enqueue_style('google_fonts_source_sans_pro', 'https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@400;700&display=swap', array(), null);
    wp_enqueue_style('google_fonts_poppins', 'https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap', array(), null);
}
add_action('wp_enqueue_scripts', 'add_google_fonts');


// ブラウザ側で更新したスクリプト見に行く。<link rel="stylesheet" id="style-css" href="https://naigaicorp.net/wp-content/themes/Xiaoyu%20Tekken7/style.css?ver=1755117206" type="text/css" media="all">

// Awesome Fonts Css　 * Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com
function load_font_awesome_files()
{
    $path = get_template_directory() . '/css/all.css';
    $ver  = file_exists($path) ? filemtime($path) : null;
    wp_enqueue_style('font-awesome', get_template_directory_uri() . '/css/all.css', [], $ver);
}
add_action('wp_enqueue_scripts', 'load_font_awesome_files');

function themebs_enqueue_styles()
{
    // まず Bootstrap にも mtime を付与
    $bs_rel = '/css/bootstrap.min.css';
    $bs_path = get_template_directory() . $bs_rel;
    $bs_ver  = file_exists($bs_path) ? filemtime($bs_path) : null;
    wp_enqueue_style('bootstrap_min_css', get_template_directory_uri() . $bs_rel, [], $bs_ver);

    // その他のスタイル
    $style_files = [
        // 'bootstrap_custom' => '/css/bootstrap.custom.css',
        'slick-slider'        => '/css/slick.css',
        'swiper-style'        => '/css/swiper-bundle.css',
        'slicknav-custom'     => '/css/slicknav.css',
        'lite-yt-embed'       => '/css/lite-yt-embed.css',
        'lite-vimeo-embed'    => '/css/lite-vimeo-embed.css',
        'lightbox_css'        => '/css/lightbox.css',
        'editor_style'        => '/css/editor-style.css',
        'admin-style'         => '/css/admin-style.css',
        // 'superfish'        => '/css/superfish.css',
        'loan-simulation-css' => '/css/loan-simulation.css',
        'pannellum'           => '/css/pannellum.css',
        'style'               => '/style.css',
    ];

    foreach ($style_files as $handle => $rel) {
        $path = get_template_directory() . $rel;
        $ver  = file_exists($path) ? filemtime($path) : null;
        wp_enqueue_style($handle, get_template_directory_uri() . $rel, ['bootstrap_min_css'], $ver);
    }
}
add_action('wp_enqueue_scripts', 'themebs_enqueue_styles');


// ブラウザ側で更新したスクリプト見に行く。<script type="text/javascript" src="https://naigaicorp.net/wp-content/themes/Xiaoyu%20Tekken7/js/scripts.js?ver=1754491959" id="theme_scripts-js"></script>　など
function themebs_enqueue_scripts()
{
    // WP同梱の jQuery（ヘッダー読み込み）
    wp_enqueue_script('jquery');

    // 小ヘルパー：mtime でバージョン付与
    $enqueue = function ($handle, $relpath, $deps = ['jquery'], $in_footer = true) {
        $path = get_template_directory() . $relpath;
        $uri  = get_template_directory_uri() . $relpath;
        $ver  = file_exists($path) ? filemtime($path) : null;
        wp_enqueue_script($handle, $uri, $deps, $ver, $in_footer);
    };

    // 画像処理 → リスナー → AJAX（すべて mtime 付き）
    $enqueue('image-handler',   '/js/imageHandler.js');
    $enqueue('event-listeners', '/js/eventListeners.js', ['jquery', 'image-handler']);
    $enqueue('ajaxHandler',     '/js/ajaxHandler.js');

    // Popper / Bootstrap（順序・依存を固定）
    $enqueue('popper_min_js',    '/js/popper.min.js', ['jquery']);
    $enqueue('bootstrap_min_js', '/js/bootstrap.min.js', ['jquery', 'popper_min_js']);

    // DataTables / FullCalendar
    $enqueue('datatables_min_js',   '/js/datatables.min.js', ['jquery']);
    $enqueue('fullcalendar_min_js', '/js/index.global.min.js', []); // jQuery不要

    // ライブラリ類
    $enqueue('swiper-slider',     '/js/swiper-bundle.min.js', []);         // jQuery不要
    $enqueue('slicknav',          '/js/jquery.slicknav.min.js', ['jquery']);
    $enqueue('slick-slider',      '/js/slick.min.js',          ['jquery']);
    $enqueue('imagesloaded',      '/js/imagesloaded.pkgd.min.js', []);     // 単体でOK
    $enqueue('isotope',           '/js/isotope.pkgd.min.js', ['imagesloaded']); // 画像読込後に
    $enqueue('dotdot',            '/js/jquery.dotdotdot.min.js', ['jquery']);
    $enqueue('lite-yt-embed',     '/js/lite-yt-embed.js', []);
    $enqueue('lite-vimeo-embed',  '/js/lite-vimeo-embed.js', []);
    $enqueue('lightbox',          '/js/lightbox.min.js', []);
    $enqueue('clipboard',         '/js/clipboard.min.js', []);
    $enqueue('loan-simulation-js', '/js/loan-simulation.js', ['jquery']);
    $enqueue('chatgpt',           '/js/chatgpt-modal.js', []);
    // Pannellum は lib → 本体の順（依存で担保）
    $enqueue('libpannellum', '/js/libpannellum.js', []);
    $enqueue('pannellum',    '/js/pannellum.js',    ['libpannellum']);

    // 自作 scripts.js（存在チェック＋mtime）
    $scripts_path = get_template_directory() . '/js/scripts.js';
    $scripts_ver  = file_exists($scripts_path) ? filemtime($scripts_path) : null;
    wp_enqueue_script(
        'theme_scripts',
        get_template_directory_uri() . '/js/scripts.js',
        ['jquery', 'bootstrap_min_js'], // ここに他の依存があれば追加
        $scripts_ver,
        true
    );

    // calendar.js（任意）
    $enqueue('calendar_js', '/js/calendar.js', ['jquery']);
}
add_action('wp_enqueue_scripts', 'themebs_enqueue_scripts');





//// Bootstrapのドロップダウンメニューアイテムを開閉　
//このスクリプトコードでは、クリックされたメニュー要素の次にある.dropdown-menuを取得し、
//.toggle()メソッドで表示・非表示を切り替えつつ、aria-expanded属性も適切に設定しています。
//jQueryにおいても、$はjQueryのエイリアスとして使用される。
// ドロップダウンメニューのトグル　
//ナビゲーションバー内のメニューアイテムで、子メニューを持っている要素（.menu-item-has-children）の直下のリンク要素（a）を選択する、となります
//クリックイベントハンドラ クリックした際に発生するトリガー・ハンドラのこと。
//on('click', function (e)  これは決まり文句。
//
// bootstrap_custom_menu_js 関数
/*
function bootstrap_custom_menu_js()
{
    ?>

    <script>
        jQuery(document).ready(function ($) {
            // サブメニューを非表示にする
            $('#menu-global-navigation .menu-item-has-children .dropdown-submenu').hide();

            // メニューアイテムをホバーしたときのイベントを設定
            $('#menu-global-navigation .menu-item-has-children').hover(function () {
                // ホバーしたメニューアイテムのサブメニューを表示
                $(this).find('.dropdown-submenu').slideDown();
            }, function () {
                // マウスが離れたときにサブメニューを非表示
                $(this).find('.dropdown-submenu').slideUp();
            });

            // クリックイベントをモバイルおよびデスクトップで共通化
            $('#menu-global-navigation .menu-item-has-children > .nav-link').on('click', function (e) {
                var $parent = $(this).parent('.menu-item-has-children');

                if ($(window).width() > 991) {
                    // デスクトップではクリックでのメニュー表示/非表示
                    if (!$parent.hasClass('open')) {
                        // 閉じている場合は開く
                        $('#menu-global-navigation .menu-item-has-children').removeClass('open'); // 他のサブメニューを閉じる
                        $('#menu-global-navigation .menu-item-has-children .dropdown-menu').not($parent.find('.dropdown-menu')).slideUp(); // 他のサブメニューを閉じる

                        $parent.addClass('open');
                        $parent.find('.dropdown-menu').slideDown();
                        $parent.find('.dropdown-menu').addClass('new-dropdown-menu'); // スタイルを継承しないクラスを追加
                    } else {
                        // 開いている場合は閉じる
                        $parent.removeClass('open');
                        $parent.find('.dropdown-menu').slideUp();
                    }
                    // ページの遷移を行わないようにする
                    e.preventDefault();
                } else {
                    // モバイルではクリックでのサブメニュー表示/非表示
                    $parent.toggleClass('open');
                    $parent.find('.dropdown-menu').slideToggle();
                    // ページの遷移を行わないようにする
                    e.preventDefault();
                }
            });

            // PCでの最初のクリック操作時に.dropdown-submenuを非表示にする
            $('#menu-global-navigation .menu-item-has-children > .nav-link').on('click', function () {
                // サブメニューが表示されている場合のみ、非表示にする
                if ($(this).siblings('.dropdown-menu').is(':visible')) {
                    $('.dropdown-submenu').hide();
                }
            });

            // メニューアイテム内のリンクがクリックされたときのページへの移動
            $('#menu-global-navigation .menu-item-has-children .dropdown-menu li a').on('click', function (e) {
                // メニューアイテムが開いていない場合のみ、ページの移動を行う
                if (!$(this).closest('.menu-item-has-children').hasClass('open')) {
                    // ページの移動を行わないようにする
                    e.preventDefault();
                    // メニューアイテムが開いていない場合は、ページの遷移を行う
                    window.location.href = $(this).attr('href');
                }
            });
        });
    </script>

    <?php
}
*/

// wp_enqueue_scripts フックに登録
add_action('wp_enqueue_scripts', 'themebs_enqueue_scripts');
// wp_footer フックに登録
// add_action('wp_footer', 'bootstrap_custom_menu_js');


// single.php h2 タブリンクの判定
function naigai_property_kind($post_id = null): string
{
    $post_id = $post_id ?: get_the_ID();

    if (in_category('naigai-tochi', $post_id)) return 'land';
    if (in_category('naigai-construction', $post_id)) return 'construction';

    return 'other';
}





//Wordpressサイト内検索を行った際に表示されるのは投稿記事のみ
function my_posy_search($search)
{
    if (is_search()) {
        $search .= " AND post_type = 'post'";
    }
    return $search;
}
add_filter('posts_search', 'my_posy_search');



//SVGをアップロード プラグインなし
function add_svg_upload($file_types)
{
    $add_filetypes = array();
    $add_filetypes['svg'] = 'image/svg+xml';
    $file_types = array_merge($file_types, $add_filetypes);
    return $file_types;
}
add_action('upload_mimes', 'add_svg_upload');


// 抜粋の文字数をPCとスマホそれぞれに設定
//function twpp_change_excerpt_length( $length ) {
//return ( wp_is_mobile() ) ? 30 : 150;
//}
//add_filter( 'excerpt_length', 'twpp_change_excerpt_length', 999 );


function dess_get_excerpt($num_chars)
{
    // コンテンツを取得
    $content = get_the_content();

    // デバッグ用: コンテンツを表示
    error_log('Original Content: ' . $content); // ログに元のコンテンツを出力

    // ショートコードを取り除き、HTMLタグを削除
    $cleaned_content = strip_shortcodes(strip_tags($content));

    // デバッグ用: クリーンなコンテンツを表示
    error_log('Cleaned Content: ' . $cleaned_content); // ログにクリーンなコンテンツを出力

    // コンテンツの長さが0の場合は空の文字列を返す
    if (strlen($cleaned_content) == 0) {
        return '';
    }

    // 指定された文字数までの部分を取得
    $excerpt = mb_substr($cleaned_content, 0, $num_chars);

    // デバッグ用: 抜粋を表示
    error_log('Excerpt Before Processing: ' . $excerpt); // ログに抜粋を出力

    // 最後の単語を確認し、省略記号を付ける
    if (strlen($cleaned_content) > $num_chars) {
        $last_space = strrpos($excerpt, ' '); // 最後の空白の位置を取得
        if ($last_space !== false) {
            $excerpt = mb_substr($excerpt, 0, $last_space); // 最後の空白まで切り取る
        }
        $excerpt .= '...'; // 省略記号を追加
    }

    // デバッグ用: 最終的な抜粋を表示
    error_log('Final Excerpt: ' . $excerpt); // ログに最終的な抜粋を出力

    return $excerpt; // 抜粋を返す
}









/* 管理画面に「サムネイル」「ID」「文字数」を追加 */
function add_posts_columns($columns)
{
    $columns['thumbnail'] = 'サムネイル';
    $columns['postid'] = 'ID';
    $columns['count'] = '文字数';

    echo '';

    return $columns;
}

//投稿一覧管理画にサブネイル投稿一覧を表示
function add_posts_columns_row($column_name, $post_id)
{
    if ('thumbnail' == $column_name) {
        $thumb = get_the_post_thumbnail($post_id, array(100, 100), 'thumbnail');
        echo ($thumb) ? $thumb : '－';
    } elseif ('postid' == $column_name) {
        echo $post_id;
    } elseif ('count' == $column_name) {
        $count = mb_strlen(strip_tags(get_post_field('post_content', $post_id)));
        echo $count;
    }
}
add_filter('manage_posts_columns', 'add_posts_columns');
add_action('manage_posts_custom_column', 'add_posts_columns_row', 10, 2);


//固定ページ一覧画面でサムネイル表示
function add_page_columns($columns)
{
    $columns['thumbnail'] = 'サムネイル';
    return $columns;
}
function add_page_column_row($column_name, $post_id)
{
    if ('thumbnail' == $column_name) {
        $thumb = get_the_post_thumbnail($post_id, array(100, 100), 'thumbnail');
        echo ($thumb) ? $thumb : '－';
    }
}
add_filter('manage_pages_columns', 'add_page_columns');
add_action('manage_pages_custom_column', 'add_page_column_row', 10, 2);

/* 固定ページ一覧にスラッグを追加する */
function add_page_column_slug_title($columns)
{
    $columns['slug'] = "スラッグ";
    return $columns;
}
function add_page_column_slug($column_name, $post_id)
{
    if ($column_name == 'slug') {
        $post = get_post($post_id);
        $slug = $post->post_name;
        echo esc_attr($slug);
    }
}
add_filter('manage_pages_columns', 'add_page_column_slug_title');
add_action('manage_pages_custom_column', 'add_page_column_slug', 10, 2);

//固定ページ一覧に ページIDを管理画面に表示
function add_page_id_column($columns)
{
    $columns['page_id'] = 'Page ID';
    return $columns;
}

function display_page_id_column($column, $post_id)
{
    if ($column === 'page_id') {
        echo $post_id;
    }
}

add_filter('manage_pages_columns', 'add_page_id_column');
add_action('manage_pages_custom_column', 'display_page_id_column', 10, 2);


/** メインカラムの幅を指定する変数。下記は 600px を指定（記述推奨） */
if (!isset($content_width))
    $content_width = 600;



/**
 * サイドバーウィジェットエリアの定義（CHAPTER 11）
 * ウィジェットエリア（サイドバー）の登録: register_sidebar 関数を使用して新しいウィジェットエリアを登録します。この関数にはウィジェットエリアの名前やID、表示される位置やスタイルに関する設定が含まれます。
 */

// サイドバーウィジェットエリア1の定義
register_sidebar(
    array(
        'name' => 'サイドバーウィジット-1',
        'id' => 'sidebar-1',
        'description' => 'サイドバーのウィジットエリアです。デフォルトのサイドバーと丸ごと入れ替えたいときに使ってください。',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
    )
);

// サイドバーウィジェットエリア2の定義
register_sidebar(
    array(
        'name' => 'サイドバーウィジット-2',
        'id' => 'sidebar-2',
        'description' => 'サイドバーのウィジットエリアです。',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
    )
);

// サイドバーウィジェットエリア3の定義
/*register_sidebar(
    array(
        'name' => 'Farming Today',
        'id' => 'weekly-farming-schedule', // -ハイフン スペースはダメ
        'description' => 'サイドバーのウィジットエリアです。',
        'before_widget' => '<div id="%1$s" class="widget %2$s my-custom-class">', // my-custom-class を追加
        'after_widget' => '</div>',
    )
);*/

// サイドバーウィジェットエリア4の定義
register_sidebar(
    array(
        'name'          => '価格範囲サイドバー',  // ウィジェットエリアの名前（管理画面に表示される）
        'id'            => 'price-range',  // ウィジェットエリアのID（使用する名前）
        'before_widget' => '<div class="widget">',  // ウィジェットの前に挿入するHTML
        'after_widget'  => '</div>',  // ウィジェットの後に挿入するHTML
        'before_title'  => '<h2 class="widget-title">',  // ウィジェットタイトル前のHTML
        'after_title'   => '</h2>',  // ウィジェットタイトル後のHTML
    )
);


// ウィジェットエリアの登録を実




// サイドバーウィジェットエリアの登録
function register_custom_ad_banner_widget_area()
{
    register_sidebar(
        array(
            'name' => 'Custom Ad Banner Widget Area',
            'id' => 'custom-ad-banner-widget-area',
            'description' => 'This is the widget area for the custom ad banner widget.',
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget' => '</div>',
            'before_title' => '<h2 class="widget-title">',
            'after_title' => '</h2>',
        )
    );
}
add_action('widgets_init', 'register_custom_ad_banner_widget_area');



/**
 * フッターウィジェットエリアの定義
 */

function dess_widgets_init()
{
    // サイドバーウィジェットの登録などを行う
}

// Footer Widget - Column 1
register_sidebar(
    array(
        'name' => __('Footer Column 1', ''),
        'id' => 'footer-1',
        'before_widget' => '<div id="%1$s" class="widget-box %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="footer-title">',
        'after_title' => '</h3>',
    )
);

// Footer Widget - Column 2
register_sidebar(
    array(
        'name' => __('Footer Column 2', ''),
        'id' => 'footer-2',
        'before_widget' => '<div id="%1$s" class="widget-box %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="footer-title">',
        'after_title' => '</h3>',
    )
);

// Footer Widget - Column 3
register_sidebar(
    array(
        'name' => __('Footer Column 3', ''),
        'id' => 'footer-3',
        'before_widget' => '<div id="%1$s" class="widget-box %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="footer-title">',
        'after_title' => '</h3>',
        'ignore_sticky_posts' => true,
    )
);

// 関数の呼び出しをフックに追加
add_action('widgets_init', 'dess_widgets_init');


// functions.phpに追加
function theme_setup()
{
    register_nav_menus(
        array(
            'primary' => esc_html__('Primary Menu', 'your-theme-slug'),
        )
    );
}
add_action('after_setup_theme', 'theme_setup');








// メタボックスを追加する関数　　画像とパノラマのタブ用のメタボックス
function pannellum_metabox()
{
    add_meta_box(
        'pannellum_metabox_id',               // メタボックスのID
        'パノラマ画像の設定',                  // メタボックスのタイトル
        'pannellum_metabox_content',          // メタボックスのコンテンツを表示する関数
        ['post', 'house', 'recruitment', 'blog'],                   // メタボックスを表示する投稿タイプ
        'normal',                            // メタボックスの表示位置
        'default'                            // メタボックスの優先度
    );
}
// 'add_meta_boxes' フックを使ってメタボックスをWordPressに追加
add_action('add_meta_boxes', 'pannellum_metabox');

// メタボックスの保存処理
function pannellum_metabox_save($post_id)
{
    // 自動保存を防ぐ
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return $post_id;

    // 投稿タイプを取得
    $post_type = get_post_type($post_id);  // get_post_type() を使用して投稿タイプを取得

    // 投稿タイプが'house'または'post'の場合にのみ処理を実行
    if (!in_array($post_type, ['house', 'post', 'blog', 'recruitment'], true)) {
        return $post_id;
    }

    // Nonceの確認
    if (!isset($_POST['pannellum_nonce_field']) || !wp_verify_nonce($_POST['pannellum_nonce_field'], 'pannellum_nonce_action')) {
        return $post_id; // Nonceが無効な場合はここで処理を中止
    }

    // 権限チェック（ユーザーが編集権限を持っていることを確認）
    if (!current_user_can('edit_post', $post_id)) {
        return $post_id;
    }

    // フィールドの保存処理
    for ($i = 1; $i <= 10; $i++) {
        // 通常画像の保存
        if (isset($_POST['iframe_image_url_' . $i])) {
            update_post_meta($post_id, '_iframe_image_url_' . $i, sanitize_text_field($_POST['iframe_image_url_' . $i]));
        }
        if (isset($_POST['iframe_image_title_' . $i])) {
            update_post_meta($post_id, '_iframe_image_title_' . $i, sanitize_text_field($_POST['iframe_image_title_' . $i]));
        }
        if (isset($_POST['iframe_image_text_' . $i])) {
            update_post_meta($post_id, '_iframe_image_text_' . $i, sanitize_textarea_field($_POST['iframe_image_text_' . $i]));
        }
        // パノラマ画像の保存
        if (isset($_POST['iframe_panorama_image_url_' . $i])) {
            update_post_meta($post_id, '_iframe_panorama_image_url_' . $i, sanitize_text_field($_POST['iframe_panorama_image_url_' . $i]));
        }
        if (isset($_POST['iframe_panorama_image_title_' . $i])) {
            update_post_meta($post_id, '_iframe_panorama_image_title_' . $i, sanitize_text_field($_POST['iframe_panorama_image_title_' . $i]));
        }
        if (isset($_POST['iframe_panorama_image_text_' . $i])) {
            update_post_meta($post_id, '_iframe_panorama_image_text_' . $i, sanitize_textarea_field($_POST['iframe_panorama_image_text_' . $i]));
        }
    }

    return $post_id;
}
// 保存処理をフックに追加
add_action('save_post', 'pannellum_metabox_save');

// Metabox content
function pannellum_metabox_content($post)
{
    // セキュリティ対策のため、nonceフィールドを追加。これにより不正なリクエストを防ぐ
    // Nonceの検証
    wp_nonce_field('pannellum_nonce_action', 'pannellum_nonce_field');

    // タブ切り替えボタンを表示（画像ビューとパノラマビュー）
    echo '<div class="metabox-tabs">
            <span class="metabox-tab active" data-tab="image-view-tab-content">Image View</span>
            <span class="metabox-tab" data-tab="panorama-view-tab-content">360° Panorama View</span>
        </div>';

    // 画像ビュータブの内容
    echo '<div class="metabox-tab-content" id="image-view-tab-content">';
    echo '<table class="form-table" style="width:100%; border-collapse: collapse;">';

    // 1から10までの画像URL、タイトル、テキストフィールドを表示
    for ($i = 1; $i <= 10; $i++) {
        // 画像のURL、タイトル、テキストを取得してフォームに表示
        $image_url = esc_url(get_post_meta($post->ID, '_iframe_image_url_' . $i, true));
        $image_title = esc_attr(get_post_meta($post->ID, '_iframe_image_title_' . $i, true));
        $image_text = esc_textarea(get_post_meta($post->ID, '_iframe_image_text_' . $i, true));

        // 画像URL入力フィールドを表示
        echo '<tr>
                <th style="text-align:left; padding: 8px; width: 25%"><label for="iframe_image_url_' . $i . '">Image URL (' . $i . '): </label></th>
                <td style="padding: 8px;">
                    <input type="text" name="iframe_image_url_' . $i . '" id="iframe_image_url_' . $i . '" value="' . $image_url . '" class="regular-text" style="width:100%;" placeholder="画像のURLを入力" />
                    
                    <!-- 画像プレビューを表示 -->
                    <div class="image-preview" id="image-preview-' . $i . '" style="margin-top: 10px;">';
        if ($image_url) {
            echo '<img src="' . $image_url . '" alt="Image Preview" style="max-width: 100px; max-height: 100px;" />';
        }
        echo '</div>

                    <!-- 画像選択と削除ボタンを表示 -->
                    <div class="image-selection" style="margin-top: 10px;">
                        <button type="button" class="upload_image_button button" style="margin-right: 10px;">画像を選択</button>
                        <button type="button" class="delete_image_button button">削除</button>
                    </div>
                    
                    <!-- タイトル入力フィールド -->
                    <label for="iframe_image_title_' . $i . '" style="margin-top: 10px;">Title (' . $i . '):</label>
                    <input type="text" name="iframe_image_title_' . $i . '" id="iframe_image_title_' . $i . '" value="' . $image_title . '" class="regular-text" style="width:100%;" />
                    
                    <!-- テキスト入力フィールド -->
                    <label for="iframe_image_text_' . $i . '" style="margin-top: 10px;">Text (' . $i . '):</label>
                    <textarea name="iframe_image_text_' . $i . '" id="iframe_image_text_' . $i . '" class="regular-text" style="width:100%; height: 100px;">' . $image_text . '</textarea>
                </td>
            </tr>';
    }

    echo '</table>';
    echo '</div>';

    // パノラマビュータブの内容
    echo '<div class="metabox-tab-content" id="panorama-view-tab-content">';
    echo '<table class="form-table" style="width:100%; border-collapse: collapse;">';

    // 1から10までのパノラマ画像URL、タイトル、テキストフィールドを表示
    for ($i = 1; $i <= 10; $i++) {
        // パノラマ画像のURL、タイトル、テキストを取得してフォームに表示
        $panorama_url = esc_url(get_post_meta($post->ID, '_iframe_panorama_image_url_' . $i, true));
        $panorama_title = esc_attr(get_post_meta($post->ID, '_iframe_panorama_image_title_' . $i, true));
        $panorama_text = esc_textarea(get_post_meta($post->ID, '_iframe_panorama_image_text_' . $i, true));

        // パノラマ画像URL入力フィールドを表示
        echo '<tr>
                <th style="text-align:left; padding: 8px; width: 25%"><label for="iframe_panorama_image_url_' . $i . '">Panorama Image URL (' . $i . '): </label></th>
                <td style="padding: 8px;">
                    <input type="text" name="iframe_panorama_image_url_' . $i . '" id="iframe_panorama_image_url_' . $i . '" value="' . $panorama_url . '" class="regular-text" style="width:100%;" placeholder="パノラマ画像のURLを入力" />

                    <!-- パノラマプレビューを表示 -->
                    <div class="panorama-preview" id="panorama-preview-' . $i . '" style="margin-top: 10px;">';
        if ($panorama_url) {
            echo '<img src="' . $panorama_url . '" alt="Panorama Preview" style="max-width: 100px; max-height: 100px;" />';
        }
        echo '</div>

                    <!-- 画像選択と削除ボタンを表示 -->
                    <div class="image-selection" style="margin-top: 10px;">
                        <button type="button" class="upload_image_button button" style="margin-right: 10px;">画像を選択</button>
                        <button type="button" class="delete_image_button button">削除</button>
                    </div>

                    <!-- パノラマ画像タイトル入力フィールド -->
                    <label for="iframe_panorama_image_title_' . $i . '" style="margin-top: 10px;">Title (' . $i . '):</label>
                    <input type="text" name="iframe_panorama_image_title_' . $i . '" id="iframe_panorama_image_title_' . $i . '" value="' . $panorama_title . '" class="regular-text" style="width:100%;" />
                    
                    <!-- パノラマ画像テキスト入力フィールド -->
                    <label for="iframe_panorama_image_text_' . $i . '" style="margin-top: 10px;">Text (' . $i . '):</label>
                    <textarea name="iframe_panorama_image_text_' . $i . '" id="iframe_panorama_image_text_' . $i . '" class="regular-text" style="width:100%; height: 100px;">' . $panorama_text . '</textarea>
                </td>
            </tr>';
    }

    echo '</table>';
    echo '</div>';
}



function pannellum_metabox_script()
{
    if (is_admin()) {
?>
        <script type="text/javascript">
            jQuery(document).ready(function($) {
                // 修正箇所: タブの切り替え
                $('.metabox-tab').on('click', function() {
                    var targetTab = $(this).data('tab'); // クリックされたタブのIDを取得
                    $('.metabox-tab').removeClass('active'); // すべてのタブからアクティブクラスを削除
                    $(this).addClass('active'); // クリックされたタブにアクティブクラスを追加
                    $('.metabox-tab-content').hide(); // すべてのタブコンテンツを非表示
                    $('#' + targetTab).show(); // 対応するタブコンテンツを表示
                });

                // 修正箇所: 画像選択ボタンがクリックされた時の処理
                $(".upload_image_button").click(function(e) {
                    var button = $(this); // クリックされたボタンを取得
                    var inputField = button.closest("td").find("input[type=text]"); // 入力フィールドを取得
                    var frame = wp.media({ // メディアライブラリを呼び出す
                        title: "画像を選択", // ダイアログタイトル
                        button: {
                            text: "画像を選択" // ダイアログボタンのテキスト
                        },
                        multiple: false // 複数選択を無効に設定
                    });

                    // 画像が選択された時の処理
                    frame.on("select", function() {
                        var attachment = frame.state().get("selection").first().toJSON(); // 選択された画像の情報を取得

                        // それぞれのURL入力フィールドを更新
                        button.closest('td').find("input[name^='iframe_panorama_image_url_']").val(attachment.url); // パノラマ画像URLを入力欄に設定
                        button.closest('td').find("input[name^='iframe_image_url_']").val(attachment.url); // 通常画像URLを入力欄に設定

                        // プレビューを表示
                        button.closest('td').find('.image-preview').html('<img src="' + attachment.url + '" style="max-width: 100px; max-height: 100px;" />'); // 通常プレビュー表示
                        button.closest('td').find('.panorama-preview').html('<img src="' + attachment.url + '" style="max-width: 100px; max-height: 100px;" />'); // 360プレビュー表示
                    });

                    frame.open(); // メディアライブラリを開く
                });

                // 修正箇所: 画像削除ボタンがクリックされた時の処理
                $(".delete_image_button").click(function() {
                    var inputField = $(this).closest("td").find("input[type=text]"); // 入力フィールドを取得
                    var imagePreview = $(this).closest("td").find(".image-preview"); // 通常プレビュー部分を取得
                    var panoramaPreview = $(this).closest("td").find(".panorama-preview"); // 360プレビュー部分を取得

                    inputField.val(''); // 入力フィールドを空にする
                    imagePreview.html(''); // 通常プレビューを消す
                    panoramaPreview.html(''); // 360プレビューを消す
                });
            });
        </script>
        <?php
    }
}
add_action('admin_footer', 'pannellum_metabox_script');

function add_custom_admin_css()
{
    // 管理画面用のCSS
    $custom_css = "
        .metabox-tab {
            padding: 10px;
            background-color: #f4f4f4;
            border: 1px solid #ccc;
            cursor: pointer;
            display: inline-block;
            margin-right: 5px;
        }

        .metabox-tab.active {
            background-color: #0073aa;
            color: #fff;
        }

        .metabox-tab-content {
            display: none;
            padding: 20px;
            border-top: 1px solid #ccc;
            background-color: #fff;
        }

        .image-preview, .panorama-preview {
            margin-top: 10px;
        }

        .image-preview img, .panorama-preview img {
            max-width: 100px;
            border: 1px solid #ddd;
        }
    ";

    // 管理画面のフッターにCSSをインラインで追加
    echo '<style type="text/css">' . $custom_css . '</style>';
}
add_action('admin_head', 'add_custom_admin_css');



// OpenAI API 呼び出し関数
function call_openai_api($user_message)
{
    $api_key = OPENAI_API_KEY;
    $url = 'https://api.openai.com/v1/chat/completions';

    $body = json_encode([
        'model' => 'gpt-4',
        'messages' => [
            // 事前メッセージを設定
            ['role' => 'system', 'content' => 'こんにちは。内外土地開発（株）です。那須の不動産の物件でしたらご案内ができます。以下のリンクをご参照ください。'],
            ['role' => 'system', 'content' => '土地の案内ページ: https://naigaicorp.net/naigai-tochi'],
            ['role' => 'system', 'content' => '建物の案内ページ: https://naigaicorp.net/naigai-construction'],
            ['role' => 'system', 'content' => 'ほかに何かわからないことがございましたら何でも聞いてくださいね！よろしくお願い申し上げます。'],
            // ユーザーのメッセージを受け取る
            ['role' => 'user', 'content' => $user_message],
        ],
    ]);

    // APIリクエスト送信
    $response = wp_remote_post($url, [
        'method'    => 'POST',
        'body'      => $body,
        'headers'   => [
            'Content-Type'  => 'application/json',
            'Authorization' => 'Bearer ' . $api_key,
        ],
    ]);

    // エラーチェック
    if (is_wp_error($response)) {
        error_log('APIリクエストエラー: ' . $response->get_error_message());
        return 'エラー: APIリクエストに失敗しました。';
    }

    // レスポンス処理
    $data = wp_remote_retrieve_body($response);
    $result = json_decode($data, true);

    // OpenAI APIからのエラーハンドリング
    if (isset($result['error'])) {
        error_log('APIエラー: ' . $result['error']['message']);
        return 'エラー: ' . $result['error']['message'];
    }

    // 正常なレスポンスがある場合に返す
    if (isset($result['choices'][0]['message']['content'])) {
        return $result['choices'][0]['message']['content'];
    }

    // 予期しないレスポンスの場合
    error_log('予期しないAPIレスポンス: ' . print_r($result, true));
    return 'エラー: 予期しないレスポンスです。';
}



// Ajax ハンドラ
function chatgpt_request_handler()
{
    // Nonceの検証
    if (!isset($_POST['security']) || !wp_verify_nonce($_POST['security'], 'custom-nonce')) {
        wp_send_json_error('不正なリクエストです。');
    }

    // ユーザーのメッセージを取得
    $user_message = isset($_POST['user_message']) ? sanitize_text_field($_POST['user_message']) : '';
    if (empty($user_message)) {
        wp_send_json_error('メッセージが空です。');
    }

    // OpenAI APIを呼び出してレスポンスを取得
    $response = call_openai_api($user_message);

    // レスポンスを送信
    wp_send_json_success(['message' => $response]);
}

add_action('wp_ajax_chatgpt_request', 'chatgpt_request_handler');
add_action('wp_ajax_nopriv_chatgpt_request', 'chatgpt_request_handler');














//Gallery Page for  meta box "show_in_homepage" catefory-functions
//require get_template_directory().'/inc/functions/category-functions.php';


// カスタム　walker 
require get_template_directory() . '/inc/functions/custom-walker.php';

// 採用ページ
require get_template_directory() . '/inc/functions/custom-functions.php';

// 固定ページ　画像３枚　swiper
require get_template_directory() . '/inc/functions/function-page.php';


// 資料請求PDF
//require_once get_template_directory() . '/inc/functions/generate-pdf.php';


//walker_path = get_template_directory() . '/inc/functions/custom-walker.php';
//if (file_exists($walker_path)) {
//require_once $walker_path;
//} else {
//error_log("Warning: custom-walker.php が見つかりません - " . $walker_path);
//}

// single.php パンクズ　投稿ページ　対応
require get_template_directory() . '/inc/functions/bread1-comment.php';

// genshin_build.php　パンクズ　カスタム投稿ページ　対応
require get_template_directory() . '/inc/functions/bread2-comment.php';


//Customize meta box //
//require get_template_directory().'/inc/functions/customizer-metabox-menu.php';

//Comment Forme //　　コメントフォームを送信した後に、ユーザーのmy_comment_list　が表示する仕組み
require get_template_directory() . '/inc/functions/comment-forme.php';



//カスタムメタボックスを通常の投稿タイプと指定のカスタム投稿タイプに追加し、そのメタボックス内に画像や動画などの設定を行うことができるようにしています。
add_action('add_meta_boxes', 'dess_post_meta_box');

// home.php のタクソノミーを表示 通常の投稿タイプ、指定のカスタム投稿タイプ、メタボックスを定義
function dess_post_meta_box()
{
    // 通常の投稿タイプ
    add_meta_box(
        'dess_post_settings',
        __('Post Settings', ''),
        'dess_post_meta_box_callback',
        'post'
    );

    // 新しいカスタム投稿タイプにも同じメタボックスを追加
    add_meta_box(
        'dess_post_settings',
        __('Post Settings', ''),
        'dess_post_meta_box_callback',
        'house'  // カスタム投稿タイプの識別子
    );

    // 別のカスタム投稿タイプにも同じメタボックスを追加
    add_meta_box(
        'dess_post_settings',
        __('Post Settings', ''),
        'dess_post_meta_box_callback',
        'recruitment'  // カスタム投稿タイプの識別子
    );

    // 別のカスタム投稿タイプにも同じメタボックスを追加
    add_meta_box(
        'dess_post_settings',
        __('Post Settings', ''),
        'dess_post_meta_box_callback',
        'blog'  // カスタム投稿タイプの識別子
    );

    // 別のカスタム投稿タイプにも同じメタボックスを追加
    add_meta_box(
        'dess_post_settings',
        __('Post Settings', ''),
        'dess_post_meta_box_callback',
        'Artifacts'  // カスタム投稿タイプの識別子
    );
}
//_callbackコールバック関数は必要に応じて再利用することができます。つまり、同じコールバック関数を複数のメタボックスに使用することができます。

// カスタムメタボックス内の表示とフォームフィールド
function dess_post_meta_box_callback($post)
{
    // nonce（ワンタイムトークン）を生成してフォームのセキュリティを確保
    wp_nonce_field('dess_post_save_meta_box_data', 'dess_post_meta_box_nonce');

    // データベースから既存のメタボックスの値を取得
    $show_in_slider = get_post_meta($post->ID, 'show_in_slider', true);
    $show_in_homepage = get_post_meta($post->ID, 'show_in_homepage', true);
    $type = get_post_meta($post->ID, 'page_featured_type', true);

    // チェックボックスとセレクトボックスを出力
    echo '<p><label for="show_in_slider">' . __('Show in Slider', '') . ': </label>';
    echo '<input type="checkbox" id="show_in_slider" name="show_in_slider" value="Yes" ' . ($show_in_slider == 'Yes' ? 'checked' : '') . ' /></p>';

    echo '<p><label for="show_in_homepage">' . __('Show in Homepage', '') . ': </label>';
    echo '<input type="checkbox" id="show_in_homepage" name="show_in_homepage" value="Yes" ' . ($show_in_homepage == 'Yes' ? 'checked' : '') . ' /></p>';

    echo '<p><label for="video_type">' . __('Featured Type', '') . ': </label><br/>';
    echo '<select id="video_type" name="dess_post[page_featured_type]"><option value="">Image</option><option value="youtube" ' . ($type == 'youtube' ? 'selected="selected"' : '') . '>Youtube</option><option value="vimeo" ' . ($type == 'vimeo' ? 'selected="selected"' : '') . '>Vimeo</option></select></p>';

    echo '<p><label for="video_id">' . __('Video ID', '') . ': </label><br/>';
    echo '<input type="text" id="video_id" name="dess_post[page_video_id]" value="' . get_post_meta($post->ID, 'page_video_id', true) . '" /></p>';
}

// カスタムメタボックスのデータ保存
function dess_post_save_meta_box_data($post_id)
{
    // nonceの確認
    if (!isset($_POST['dess_post_meta_box_nonce'])) {
        return;
    }

    if (!wp_verify_nonce($_POST['dess_post_meta_box_nonce'], 'dess_post_save_meta_box_data')) {
        return;
    }

    // 自動保存中は処理を中断
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // 権限の確認
    if (isset($_POST['post_type']) && 'page' == $_POST['post_type']) {
        if (!current_user_can('edit_page', $post_id)) {
            return;
        }
    } else {
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
    }

    // チェックボックスの値をサニタイズして保存　追加変更
    $show_in_slider = isset($_POST['show_in_slider']) ? sanitize_text_field($_POST['show_in_slider']) : '';
    $show_in_homepage = isset($_POST['show_in_homepage']) ? sanitize_text_field($_POST['show_in_homepage']) : '';
    update_post_meta($post_id, 'show_in_slider', $show_in_slider);
    update_post_meta($post_id, 'show_in_homepage', $show_in_homepage);


    // セレクトボックスとテキストフィールドの値をサニタイズして保存
    $arr = array();
    if (isset($_POST['dess_post'])) {
        $arr = $_POST['dess_post'];
    }

    foreach ($arr as $key => $value) {
        $val = sanitize_text_field($value);
        update_post_meta($post_id, $key, $val);
    }
}

// メタボックスのデータ保存を実行
add_action('save_post', 'dess_post_save_meta_box_data');


// カテゴリーのコントロールを設定

// カスタムコントロールクラスが存在するかどうかを確認してから定義　このif 分は独自のカストマイザー作成には必須です。
if (class_exists('WP_Customize_Control')) {
    // カスタムカスタマイズコントロールクラスを定義
    class DESS_Customize_Control_Checkbox_Multiple extends WP_Customize_Control
    {
        // カスタムコントロールのタイプを定義
        public $type = 'checkbox-multiple';

        // カスタムコントロールに必要なスクリプトを読み込むためのメソッド
        public function enqueue()
        {
            wp_enqueue_script('jt-customize-controls', trailingslashit(get_template_directory_uri()) . 'js/customize-controls.js', array('jquery'));
        }

        // カスタムコントロールの内容を出力するメソッド
        public function render_content()
        {
            // カスタムコントロールに選択肢がない場合は終了
            if (empty($this->choices))
                return; ?>

            <?php if (!empty($this->label)): ?>
                <!-- カスタムコントロールのタイトルを出力 -->
                <span class="customize-control-title"><?php echo esc_html($this->label); ?></span>
            <?php endif; ?>

            <?php if (!empty($this->description)): ?>
                <!-- カスタムコントロールの説明を出力 -->
                <span class="description customize-control-description"><?php echo esc_html($this->description); ?></span>
            <?php endif; ?>

            <?php
            // カスタムコントロールの値を配列に変換
            $multi_values = !is_array($this->value()) ? explode(',', $this->value()) : $this->value();
            ?>

            <!-- チェックボックスのリストを出力 -->
            <ul>
                <?php foreach ($this->choices as $value => $label): ?>
                    <li>
                        <label>
                            <!-- チェックボックスを出力し、選択状態にする -->
                            <input type="checkbox" value="<?php echo esc_attr($value); ?>" <?php checked(in_array($value, $multi_values)); ?> />
                            <!-- チェックボックスのラベルを出力 -->
                            <?php echo esc_html($label); ?>
                        </label>
                    </li>
                <?php endforeach; ?>
            </ul>

            <!-- 選択された値を保存するための隠しフィールドを出力 -->
            <input type="hidden" <?php $this->link(); ?> value="<?php echo esc_attr(implode(',', $multi_values)); ?>" />

            <!-- 選択された値を保存するための隠しフィールドを出力 -->
            <input type="hidden" <?php $this->link(); ?> value="<?php echo esc_attr(implode(',', $multi_values)); ?>" />

        <?php }
    }
}

// Genshin-Impact ロゴセクション用のカスタマイズー
if (class_exists('WP_Customize_Control')) {
    class DESS_Customize_Control_Genshin_Logo extends WP_Customize_Control
    {
        public $type = 'genshin_logo';

        public function render_content()
        {
        ?>
            <p>
                <label for="<?php echo esc_attr($this->id); ?>_width"><?php _e('Width:', 'text-domain'); ?></label>
                <input type="number" min="1" step="1" id="<?php echo esc_attr($this->id); ?>_width" value="<?php echo esc_attr($this->value('width')); ?>" <?php $this->input_attrs(); ?> />
            </p>
            <p>
                <label for="<?php echo esc_attr($this->id); ?>_height"><?php _e('Height:', 'text-domain'); ?></label>
                <input type="number" min="1" step="1" id="<?php echo esc_attr($this->id); ?>_height" value="<?php echo esc_attr($this->value('height')); ?>" <?php $this->input_attrs(); ?> />
            </p>
            <p>
                <label for="<?php echo esc_attr($this->id); ?>_link"><?php _e('Logo Link:', 'text-domain'); ?></label>
                <select id="<?php echo esc_attr($this->id); ?>_link" <?php $this->link(); ?>>
                    <option value=""><?php _e('Select a page', 'text-domain'); ?></option>
                    <?php
                    // カスタマイザーで指定されたURLを取得
                    $selected_url = $this->value();
                    // ページリストを取得
                    $pages = get_posts(array(
                        'post_type'      => 'page',
                        'post_status'    => 'publish',
                        'posts_per_page' => -1,
                    ));
                    foreach ($pages as $page) {
                        // ロゴのリンク先URLを取得
                        $logo_link = get_theme_mod('logo_link_genshin_select', '');
                        // 選択されたページかどうかを確認
                        $selected = selected($selected_url, $logo_link, false);
                        // オプションを出力
                        echo '<option value="' . esc_attr($logo_link) . '" ' . $selected . '>' . esc_html($page->post_title) . '</option>';
                    }
                    ?>
                </select>
            </p>

        <?php
        }
    }
}


// Tekken7 ロゴセクション用のカスタマイズ
if (class_exists('WP_Customize_Control')) {
    class DESS_Customize_Control_Tekken_Logo extends WP_Customize_Control
    {
        public $type = 'tekken7_logo';

        public function render_content()
        {
        ?>
            <p>
                <label for="<?php echo esc_attr($this->id); ?>_width"><?php _e('Width:', 'text-domain'); ?></label>
                <input type="number" min="1" step="1" id="<?php echo esc_attr($this->id); ?>_width" value="<?php echo esc_attr($this->value('width')); ?>" <?php $this->input_attrs(); ?> />
            </p>
            <p>
                <label for="<?php echo esc_attr($this->id); ?>_height"><?php _e('Height:', 'text-domain'); ?></label>
                <input type="number" min="1" step="1" id="<?php echo esc_attr($this->id); ?>_height" value="<?php echo esc_attr($this->value('height')); ?>" <?php $this->input_attrs(); ?> />
            </p>
            <p>
                <label for="<?php echo esc_attr($this->id); ?>_link"><?php _e('Logo Link:', 'text-domain'); ?></label>
                <select id="<?php echo esc_attr($this->id); ?>_link" <?php $this->link(); ?>>
                    <option value=""><?php _e('Select a page', 'text-domain'); ?></option>
                    <?php
                    // カスタマイザーで指定されたURLを取得
                    $selected_url = $this->value();
                    // ページリストを取得
                    $pages = get_posts(array(
                        'post_type'      => 'page',
                        'post_status'    => 'publish',
                        'posts_per_page' => -1,
                    ));
                    foreach ($pages as $page) {
                        // ロゴのリンク先URLを取得
                        $logo_link = get_theme_mod('logo_link_tekken7_select', '');
                        // 選択されたページかどうかを確認
                        $selected = selected($selected_url, $logo_link, false);
                        // オプションを出力
                        echo '<option value="' . esc_attr($logo_link) . '" ' . $selected . '>' . esc_html($page->post_title) . '</option>';
                    }
                    ?>
                </select>
            </p>
    <?php
        }
    }
}








function dess_customize_register($wp_customize)
{

    // セクション: Footer Social Media
    $wp_customize->add_section(
        'sm_section',
        array(
            'title' => __('Footer Social Media', ''),
            'capability' => 'edit_theme_options',
            'description' => __('Allows you to set your social media URLs', ''),
        )
    );

    // ソーシャルメディアと電話番号
    $socials = array('twitter', 'facebook', 'google-plus', 'instagram', 'pinterest', 'linkedin', 'vimeo', 'youtube', 'phone', 'line');

    foreach ($socials as $social) {
        $name = str_replace('-', ' ', ucfirst($social));

        // サニタイズコールバック関数の設定（電話番号のみ 'sanitize_text_field' を使用）
        $sanitize_callback = ($social === 'phone') ? 'sanitize_text_field' : 'dess_sanitize_url';

        $wp_customize->add_setting(
            'dess_' . $social,
            array(
                'capability' => 'edit_theme_options',
                'type' => 'theme_mod',
                'sanitize_callback' => $sanitize_callback,
            )
        );

        $wp_customize->add_control(
            new WP_Customize_Control(
                $wp_customize,
                'dess_' . $social,
                array(
                    'settings' => 'dess_' . $social,
                    'label' => $name . (($social === 'phone') ? '' : ' URL'),
                    'section' => 'sm_section',
                    'type' => 'text',
                )
            )
        );
    }




    // カスタマイズオプションを登録するアクションフック
    add_action('customize_register', 'dess_customize_register');

    // Tekken7ロゴのカスタマイザーセクション追加
    $wp_customize->add_section(
        'tekken7_logo_section',
        array(
            'title' => __('Tekken7 Logo', 'text-domain'),
            'capability' => 'edit_theme_options',
            'description' => __('Allows you to update your theme\'s Tekken7 logo.', 'text-domain'),
        )
    );

    // Tekken7ロゴの設定追加
    $wp_customize->add_setting('dess_logo_tekken7', array(
        'default' => get_template_directory_uri() . '/images/logo.png',
        'type' => 'theme_mod',
        'sanitize_callback' => 'esc_url_raw',
    ));

    // Tekken7ロゴ画像を取得
    $wp_customize->add_control(new WP_Customize_Image_Control(
        $wp_customize,
        'dess_logo_tekken7',
        array(
            'label' => __('Upload Tekken7 Logo', 'text-domain'),
            'section' => 'tekken7_logo_section',
            'settings' => 'dess_logo_tekken7',
        )
    ));

    // Tekken7ロゴの幅と高さの設定
    $wp_customize->add_setting('logo_width_tekken7', array(
        'default' => 100,
        'type' => 'theme_mod',
        'sanitize_callback' => 'absint',
    ));

    $wp_customize->add_setting('logo_height_tekken7', array(
        'default' => 100,
        'type' => 'theme_mod',
        'sanitize_callback' => 'absint',
    ));

    // Tekken7ロゴの幅の調整コントロールを追加
    $wp_customize->add_control('logo_width_tekken7', array(
        'label' => __('Tekken7 Logo Width', 'text-domain'),
        'section' => 'tekken7_logo_section',
        'type' => 'number',
        'input_attrs' => array(
            'min' => 1,
            'step' => 1,
        ),
    ));

    // Tekken7ロゴの高さの調整コントロールを追加
    $wp_customize->add_control('logo_height_tekken7', array(
        'label' => __('Tekken7 Logo Height', 'text-domain'),
        'section' => 'tekken7_logo_section',
        'type' => 'number',
        'input_attrs' => array(
            'min' => 1,
            'step' => 1,
        ),
    ));

    // Tekken7ロゴのリンク先URL設定
    $wp_customize->add_setting('logo_link_tekken7_select', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ));

    // Tekken7に関連するページの一覧を取得
    $tekken7_page_slugs = array();

    $tekken7_category = get_category_by_slug('naigai-construction');
    if ($tekken7_category) {
        $query = new WP_Query(array(
            'post_type' => array('post', 'page'),
            'post_status' => 'publish',
            'category_name' => 'naigai-construction',
            'posts_per_page' => -1,
        ));
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $tekken7_page_slugs[get_permalink()] = get_the_title();
            }
            wp_reset_postdata();
        }
    }

    // Tekken7ロゴのリンク先URL セレクトボックス機能
    $wp_customize->add_control('logo_link_tekken7_select', array(
        'label' => __('Tekken7 Logo Link (Select)', 'text-domain'),
        'section' => 'tekken7_logo_section',
        'type' => 'select',
        'choices' => $tekken7_page_slugs,
    ));

    // Genshin Impactカスタマイザーのロゴセクション追加
    $wp_customize->add_section(
        'genshin_logo_section', // セクションID
        array(
            'title' => __('Genshin Impact Logo', 'text-domain'), // セクションのタイトル
            'capability' => 'edit_theme_options', // テーマオプションを編集できるユーザーの権限
            'description' => __('Allows you to update your theme\'s Genshin Impact logo.', 'text-domain'), // セクションの説明
        )
    );

    // 原神ロゴのサニタイズ
    $wp_customize->add_setting('dess_logo_genshin', array(
        /*'default' => get_template_directory_uri() . '/images/logo.png',*/
        'type' => 'theme_mod',
        'sanitize_callback' => 'esc_url_raw',
    ));

    // 原神ロゴ画像を取得
    $wp_customize->add_control(new WP_Customize_Image_Control(
        $wp_customize,
        'dess_logo_genshin',
        array(
            'label' => __('Upload Genshin Logo', 'text-domain'),
            'section' => 'genshin_logo_section', // ロゴセクションに追加
            'settings' => 'dess_logo_genshin',
        )
    ));

    // 原神ロゴの幅と高さの設定
    $wp_customize->add_setting('logo_width_genshin', array(
        'default' => 100, // デフォルトの幅
        'type' => 'theme_mod',
        'sanitize_callback' => 'absint',
    ));

    $wp_customize->add_setting('logo_height_genshin', array(
        'default' => 100, // デフォルトの高さ
        'type' => 'theme_mod',
        'sanitize_callback' => 'absint',
    ));

    // 原神ロゴの幅と高さの調整コントロールを追加
    $wp_customize->add_control('logo_width_genshin', array(
        'label' => __('Genshin Logo Width', 'text-domain'),
        'section' => 'genshin_logo_section', // ロゴセクションに追加
        'type' => 'number',
        'input_attrs' => array(
            'min' => 1,
            'step' => 1,
        ),
    ));

    // 原神ロゴの高さの調整コントロールを追加
    $wp_customize->add_control('logo_height_genshin', array(
        'label' => __('Genshin Logo Height', 'text-domain'),
        'section' => 'genshin_logo_section', // ロゴセクションに追加
        'type' => 'number',
        'input_attrs' => array(
            'min' => 1,
            'step' => 1,
        ),
    ));

    // 原神ロゴのリンク先URL設定
    $wp_customize->add_setting('logo_link_genshin', array(
        'default' => '', // デフォルトのリンク先URL
        'type' => 'theme_mod',
        'sanitize_callback' => 'esc_url_raw', // URLをエスケープするコールバック
    ));

    // セレクトボックスをセッティングフィールド
    $wp_customize->add_setting('logo_link_genshin_select', array(
        'default' => '',
        'transport' => 'refresh',
        'sanitize_callback' => 'sanitize_text_field', // データのサニタイズ
    ));

    // Genshin Impactに関連するページの一覧を取得
    $genshin_page_slugs = array();

    $genshin_category = get_category_by_slug('naigai-tochi');
    if ($genshin_category) {
        $pages = get_posts(array(
            'post_type' => 'post',
            'post_status' => 'publish',
            'category' => $genshin_category->term_id,
            'posts_per_page' => -1,
        ));
        foreach ($pages as $page) {
            $genshin_page_slugs[get_permalink($page->ID)] = $page->post_title;
        }
    }

    // 原神ロゴのリンク先URL セレクトボックス機能
    $wp_customize->add_control('logo_link_genshin_select', array(
        'label' => __('Genshin Logo Link (Select)', 'text-domain'), // ラベルを変更
        'section' => 'genshin_logo_section', // ロゴセクションに追加
        'type' => 'select', // セレクトボックス
        'choices' => $genshin_page_slugs, // ページのスラッグを選択肢として使用
    ));

    // セクション: ホームページカテゴリー
    $wp_customize->add_section(
        'hc_section',
        array(
            'title' => __('Home Categories', ''),
            'capability' => 'edit_theme_options',
            'description' => __('Allows you to select categories in your theme\'s homepage.', '')
        )
    );

    // ホームページカテゴリーの設定追加
    $cats = get_terms('category');
    $choices = array();
    foreach ($cats as $cat) {
        $choices[$cat->term_id] = $cat->name;
    }

    $wp_customize->add_setting(
        'dess_home_cats',
        array(
            'capability' => 'edit_theme_options',
            'type' => 'theme_mod',
            'sanitize_callback' => 'dess_sanitize_text', // 適切なサニタイズ関数を指定します
        )
    );

    // ホームページカテゴリーのコントロールを追加
    $wp_customize->add_control(
        new DESS_Customize_Control_Checkbox_Multiple(
            $wp_customize,
            'dess_home_cats',
            array(
                'label' => __('Select categories to display in your homepage', ''),
                'section' => 'hc_section',
                'settings' => 'dess_home_cats',
                'choices' => $choices,
                'capability' => 'edit_theme_options', // ここで 'edit_theme_options' 権限を追加します
            )
        )
    );

    // セクション: バックグラウンド
    $wp_customize->add_section(
        'background_section',
        array(
            'title' => __('Backgrounds', ''),
            'capability' => 'edit_theme_options',
            'description' => __('Allows you to change slider and content background colors', ''),
        )
    );

    // 設定とコントロール: スライダー背景色
    $wp_customize->add_setting(
        'dess_slider_bg',
        array(
            'default' => '#464E54',
            'sanitize_callback' => 'sanitize_hex_color',
        )
    );

    // 設定とコントロール: コンテンツ背景色
    $wp_customize->add_setting(
        'dess_content_bg',
        array(
            'default' => '#F9F9F9',
            'sanitize_callback' => 'sanitize_hex_color',
        )
    );

    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize,
            'dess_slider_bg',
            array(
                'settings' => 'dess_slider_bg',
                'label' => __('Slider Background', ''),
                'section' => 'background_section',
            )
        )
    );

    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize,
            'dess_content_bg',
            array(
                'settings' => 'dess_content_bg',
                'label' => __('Content Background', ''),
                'section' => 'background_section',
            )
        )
    );
}

// カスタマイズオプションを登録するためのフック
add_action('customize_register', 'dess_customize_register');



// カスタムCSSを出力する関数
function dess_custom_css()
{
    ?>
    <style>
        .home-slider {
            background-color:
                <?php echo esc_html(dess_setting('dess_slider_bg', '#464E54')); ?>;
        }

        .home-box,
        .content {
            background-color:
                <?php echo esc_html(dess_setting('dess_content_bg', '#F9F9F9')); ?>;
        }
    </style>
<?php
}

// wp_head フックでの呼び出し
add_action('wp_head', 'dess_custom_css');


// カスタマイズオプションの値を取得する関数
function dess_setting($name, $default = false)
{
    return get_theme_mod($name, $default);
}

// 他のサニタイズ関数
function dess_sanitize_html($value)
{
    return wp_filter_post_kses($value);
}

function dess_sanitize_text($value)
{
    return sanitize_text_field($value);
}

function dess_sanitize_url($value)
{
    return esc_url_raw($value);
}


//カストマイザーからURLを入力してSNSアイコンを表示　　header.php  <aside class="header-aside">
//'default' => '' としているので、もし footer_twitter がまだ設定されていない場合は空の文字列がデフォルト値として使われます。
//デフォルト値が空の文字列であるため、footer_twitter がまだ設定されていない場合、空の文字列が返されます。これが $link に代入され、後のコードで使われる

function options_customize_register($wp_customize)
{

    // Side Social Icons セクションの追加
    $wp_customize->add_section(
        'social_section',
        array(
            'title' => 'Side Menu Social Icons',
            'priority' => 1,
        )
    );

    // 各ソーシャルメディアの設定とコントロールの追加
    $social_media = array('twitter', 'facebook', 'google', 'instagram', 'pinterest', 'vimeo', 'youtube', 'linkedin', 'discord');
    foreach ($social_media as $social_icon) {
        $wp_customize->add_setting(
            'footer_' . $social_icon,
            array(
                'default' => ''
            )
        );

        $wp_customize->add_control(
            'footer_' . $social_icon,
            array(
                'label' => ucfirst($social_icon), // ソーシャルメディア名をラベルに使用
                'section' => 'social_section',
                'settings' => 'footer_' . $social_icon,
                'type' => 'text',
                'priority' => 3
            )
        );
    }

    // Footer セクションの追加
    $wp_customize->add_section(
        'footer_section',
        array(
            'title' => __('Footer'),
            'priority' => 2,
        )
    );

    // Footer Copyright 設定とコントロールの追加
    $wp_customize->add_setting(
        'footer_copyright',
        array(
            'default' => ''
        )
    );

    $wp_customize->add_control(
        'footer_copyright',
        array(
            'label' => 'Footer Copyright',
            'section' => 'footer_section',
            'settings' => 'footer_copyright',
            'type' => 'text',
            'priority' => 3
        )
    );

    // Featured Text セクションの追加
    $wp_customize->add_section(
        'featured_text_section',
        array(
            'title' => __('Text Options', ''), // セクションのタイトル
            'capability' => 'edit_theme_options',   // ユーザーがこのセクションを編集できる権限
            'description' => __('Allows you to set your footer settings', 'creator') // セクションの説明
        )
    );

    // Featured Text 設定の追加
    $wp_customize->add_setting(
        'dess_hometext',
        array(
            'capability' => 'edit_theme_options',    // ユーザーがこの設定を編集できる権限
            'type' => 'theme_mod',             // 設定の種類 (theme_mod はテーマの設定を指定します)
            'sanitize_callback' => 'dess_sanitize_html',    // 値を保存する前に適用するコールバック関数
        )
    );

    // Featured Text コントロールの追加
    $wp_customize->add_control(
        new WP_Customize_Control(
            $wp_customize,
            'dess_hometext',
            array(
                'settings' => 'dess_hometext',         // コントロールが紐づく設定の名前
                'label' => __('Featured Text', ''), // コントロールのラベル
                'section' => 'featured_text_section', // コントロールが属するセクションの名前
                'type' => 'textarea',               // コントロールの種類 (textarea は複数行のテキスト入力)
            )
        )
    );
}

// カスタマイズオプションを登録するアクションフック
add_action('customize_register', 'options_customize_register');

/*　上記のforrach if 文を利用しているからこのコードをコメントアウトした。*
function options_customize_register( $wp_customize ) {

  $wp_customize->add_section( 'social_section', array(
    'title'          => 'Social Icons',
    'priority'       => 1,
  ));

    $wp_customize->add_setting( 'footer_twitter', array(
      'default'        => ''
    ));

    $wp_customize->add_control( 'footer_twitter', array(
      'label'   => 'Twitter',
      'section' => 'social_section',
      'settings'   => 'footer_twitter',
      'type'    => 'text',
      'priority' => 3
    ));

    $wp_customize->add_setting( 'footer_facebook', array(
      'default'        => ''
    ));

    $wp_customize->add_control( 'footer_facebook', array(
      'label'   => 'Facebook',
      'section' => 'social_section',
      'settings'   => 'footer_facebook',
      'type'    => 'text',
      'priority' => 3
    ));

    $wp_customize->add_setting( 'footer_google', array(
      'default'        => ''
    ));

    $wp_customize->add_control( 'footer_google', array(
      'label'   => 'Google +',
      'section' => 'social_section',
      'settings'   => 'footer_google',
      'type'    => 'text',
      'priority' => 3
    ));

    $wp_customize->add_setting( 'footer_instagram', array(
      'default'        => ''
    ));

    $wp_customize->add_control( 'footer_instagram', array(
      'label'   => 'Instagram',
      'section' => 'social_section',
      'settings'   => 'footer_instagram',
      'type'    => 'text',
      'priority' => 3
    ));

    $wp_customize->add_setting( 'footer_pinterest', array(
      'default'        => ''
    ));

    $wp_customize->add_control( 'footer_pinterest', array(
      'label'   => 'Pinterest',
      'section' => 'social_section',
      'settings'   => 'footer_pinterest',
      'type'    => 'text',
      'priority' => 3
    ));

    $wp_customize->add_setting( 'footer_vimeo', array(
      'default'        => ''
    ));

    $wp_customize->add_control( 'footer_vimeo', array(
      'label'   => 'Vimeo',
      'section' => 'social_section',
      'settings'   => 'footer_vimeo',
      'type'    => 'text',
      'priority' => 3
    )); 

    $wp_customize->add_setting( 'footer_youtube', array(
      'default'        => ''
    ));

    $wp_customize->add_control( 'footer_youtube', array(
      'label'   => 'Youtube',
      'section' => 'social_section',
      'settings'   => 'footer_youtube',
      'type'    => 'text',
      'priority' => 3
    )); 

    $wp_customize->add_setting( 'footer_linkedin', array(
      'default'        => ''
    ));

    $wp_customize->add_control( 'footer_linkedin', array(
      'label'   => 'LinkedIn',
      'section' => 'social_section',
      'settings'   => 'footer_linkedin',
      'type'    => 'text',
      'priority' => 3
    ));

    
  $wp_customize->add_section( 'footer_section' , array(
    'title'      => __( 'Footer'),
    'priority'   => 2,
  ));

    $wp_customize->add_setting( 'footer_copyright', array(
      'default'        => ''
    ));

    $wp_customize->add_control( 'footer_copyright', array(
      'label'   => 'Footer Copyright',
      'section' => 'footer_section',
      'settings'   => 'footer_copyright',
      'type'    => 'text',
      'priority' => 3
    ));


$wp_customize->add_section(
  'featured_text_section', 
  array( 
    'title' =>  __('Text Options',''), 
    'capability' => 'edit_theme_options', 
    'description' =>  __('Allows you to set your footer settings','creator')
  )
  );
  $wp_customize->add_setting('dess_hometext', array(
    'capability' => 'edit_theme_options',
    'type'       => 'theme_mod',
    'sanitize_callback' => 'dess_sanitize_html',
  ));
  $wp_customize->add_control(new WP_Customize_Control($wp_customize, 'dess_hometext', array(
      'settings' => 'dess_hometext',
      'label'    => __('Featured Text',''),
      'section'  => 'featured_text_section',
      'type'     => 'textarea', 
  )));

}*/

// add_action( 'customize_register', 'options_customize_register' );

// wordpress 投稿を自動で discord に表示。

// このコードでは、投稿が公開されたときにsend_post_to_discord関数が呼び出され、Discordに通知が送信されます。
//メッセージの形式は$message変数で指定しており、ユーザー名やアイコンはWebhook URLに含まれている情報を使用しています。







/**
 * タームのスラッグを取得するオリジナル関数（CHAOTER 25）お勉強
 */

function get_term_slug()
{
    if (is_tax()) {
        $term_name = single_tag_title('', false);
        if (is_tax('roomtype')) {
            $term_properties = get_term_by('name', $term_name, 'roomtype');
        } else {
            $term_properties = get_term_by('name', $term_name, 'item');
        }
        $term_slug = $term_properties->slug;
        return $term_slug;
    } else {
        return false;
    }
}



/**
 * 個別記事のタームを配列に格納するオリジナル関数（CHAPTER 25）
 */

function get_my_terms_array($taxonomy)
{
    global $post;
    $terms_array = array();
    $terms = get_the_terms($post->ID, $taxonomy);
    if ($terms && !is_wp_error($terms)) {
        foreach ($terms as $term) {
            $terms_array[] = $term->slug;
        }
    }
    return $terms_array;
}


function create_my_custom_post_types()
{
    // --- house 投稿タイプ登録 ---
    register_post_type('house', array(
        'label' => 'House',
        'labels' => array(
            'name' => 'Houses',
            'singular_name' => 'House',
            'add_new' => '新規House追加',
            'edit_item' => 'Houseの編集',
            'view_item' => 'Houseを表示',
            'search_items' => 'Houseを検索',
            'not_found' => 'Houseは見つかりませんでした。',
            'not_found_in_trash' => 'ゴミ箱にHouseはありませんでした。',
        ),
        'public' => true,
        'has_archive' => true,
        'hierarchical' => false,
        'show_in_rest' => true,
        'supports' => array(
            'title',
            'editor',
            'thumbnail',
            'excerpt',
            'custom-fields',
            'revisions',
            'comments',
            'trackbacks',
            'page-attributes',
            'post-formats',
        ),
        'menu_position' => 5,
        'rewrite' => array('slug' => 'house'),
    ));

    // --- blog 投稿タイプ登録 ---
    register_post_type('blog', array(
        'label' => 'ブログ',
        'labels' => array(
            'name' => 'ブログ',
            'singular_name' => 'ブログ記事',
            'add_new' => '新規追加',
            'add_new_item' => '新しいブログ記事を追加',
            'edit_item' => 'ブログ記事を編集',
            'new_item' => '新しいブログ記事',
            'view_item' => 'ブログ記事を表示',
            'search_items' => 'ブログ記事を検索',
            'not_found' => '記事が見つかりませんでした',
            'not_found_in_trash' => 'ゴミ箱に記事がありません',
            'all_items' => 'ブログ一覧',
        ),
        'public' => true,
        'has_archive' => true,
        'hierarchical' => false,
        'menu_position' => 6,
        'menu_icon' => 'dashicons-edit',
        'supports' => array('title', 'editor', 'excerpt', 'thumbnail', 'custom-fields', 'revisions', 'comments'),
        'show_in_rest' => true,
        'rewrite' => array('slug' => 'blog'),
        'taxonomies' => array('category', 'post_tag'), // ← これを追加！

    ));

    // --- house-type カテゴリー形式 ---
    register_taxonomy('house-type', array('house', 'post'), array(
        'label' => 'House Type',
        'labels' => array(
            'name' => 'House Types',
            'singular_name' => 'House Type',
            'popular_items' => 'よく使うHouse Type',
            'edit_item' => 'House Typeを編集',
            'add_new_item' => '新規House Typeを追加',
            'search_items' => 'House Typeを検索',
        ),
        'public' => true,
        'hierarchical' => true,
        'show_in_rest' => true,
        'rewrite' => array('slug' => 'house-type'),
    ));

    // --- region カテゴリー形式 ---
    register_taxonomy('region', array('house', 'post'), array(
        'label' => 'Region',
        'labels' => array(
            'name' => 'Regions',
            'singular_name' => 'Region',
            'popular_items' => 'よく使うRegion',
            'edit_item' => 'Regionを編集',
            'add_new_item' => '新規Regionを追加',
            'search_items' => 'Regionを検索',
        ),
        'public' => true,
        'hierarchical' => true,
        'show_in_rest' => true,
        'rewrite' => array('slug' => 'region'),
    ));

    // --- 親タクソノミー（任意） ---
    register_taxonomy('parent_taxonomy', array('house', 'post'), array(
        'label' => '親タクソノミー',
        'hierarchical' => true,
        'show_in_rest' => true,
        'rewrite' => array('slug' => 'houses', 'with_front' => false),
    ));
}
add_action('init', 'create_my_custom_post_types');

// "house" にカテゴリーを有効化
function enable_categories_for_house()
{
    register_taxonomy_for_object_type('category', 'house');
}
add_action('init', 'enable_categories_for_house');

// パーマリンク再構築
function flush_rewrite_rules_on_init()
{
    flush_rewrite_rules();
}
add_action('init', 'flush_rewrite_rules_on_init');





function custom_search_filter($query)
{
    // メインクエリであり、管理画面ではないかつ検索ページの場合
    if ($query->is_main_query() && !is_admin() && $query->is_search()) {

        // 検索ワード（今回は「4ldk」）を取得
        $search_term = get_query_var('s');

        if (!empty($search_term)) {
            // タクソノミーを検索ワードに基づいて絞り込む
            $tax_query = array('relation' => 'OR');  // OR で条件を結びつけ

            // house-type タクソノミーで絞り込む（検索ワードが house-type のスラッグと一致する場合）
            $tax_query[] = array(
                'taxonomy' => 'house-type',
                'field'    => 'slug',
                'terms'    => sanitize_text_field($search_term),
                'operator' => 'LIKE',  // LIKE 演算子で部分一致検索
            );

            // region タクソノミーで絞り込む（検索ワードが region のスラッグと一致する場合）
            $tax_query[] = array(
                'taxonomy' => 'region',
                'field'    => 'slug',
                'terms'    => sanitize_text_field($search_term),
                'operator' => 'LIKE',  // LIKE 演算子で部分一致検索
            );

            // tax_query をクエリに追加
            $query->set('tax_query', $tax_query);
        }
    }
}
add_action('pre_get_posts', 'custom_search_filter');










//Wordpressサイト内検索を行った際に表示されるのは投稿記事のみ
//function my_posy_search($search)
//{
//if (is_search()) {
//$search .= " AND post_type = 'post'";
//}
//return $search;
//}
//add_filter('posts_search', 'my_posy_search');


// 検索クエリに指定したタクソノミーを含める設定
// function modify_search_query($query)
// {
//     // 管理画面ではなく、メインクエリの検索の場合に処理を適用
//     if (!is_admin() && $query->is_search && $query->is_main_query()) {

//         // 検索対象の投稿タイプを指定（'post' と 'house' の投稿タイプを検索対象に）
//         $query->set('post_type', array('post', 'house')); 

//         // 検索キーワードを取得
//         $search_term = $query->query_vars['s'];

//         // 検索キーワードが空でない場合に処理を実行
//         if (!empty($search_term)) {

//             // タクソノミー検索の設定
//             // タクソノミーのどちらかに一致すれば検索結果に含める設定
//             $tax_query = array(
//                 'relation' => 'OR', // 'OR'で、いずれかの条件に一致すればOK
//                 array(
//                     'taxonomy' => 'house-type', // 'house-type' タクソノミー名
//                     'field' => 'name',           // ターム名で検索
//                     'terms' => $search_term,     // 検索キーワード
//                     'operator' => 'LIKE',        // 部分一致検索
//                 ),
//                 array(
//                     'taxonomy' => 'region',      // 'region' タクソノミー名
//                     'field' => 'name',           // ターム名で検索
//                     'terms' => $search_term,     // 検索キーワード
//                     'operator' => 'LIKE',        // 部分一致検索
//                 ),
//             );

//             // タクソノミー条件をクエリに設定
//             $query->set('tax_query', $tax_query);
//         }
//     }
// }
// クエリが実行される前に 'modify_search_query' 関数を実行する
// add_action('pre_get_posts', 'modify_search_query');


// アーカイブページをカスタマイズするためのテンプレートファイルを指定
//function custom_archive_template($archive_template) {
//global $post;

//if (is_post_type_archive('house')) {
// house のアーカイブページ用のテンプレートファイル
//$archive_template = locate_template(array('archive-house.php'));
//}

//return $archive_template;
//}


// アーカイブページ用のテンプレートファイルを読み込むフィルター
//add_filter('archive_template', 'custom_archive_template');


//  管理画面でのタクソノミー検索
function admin_search_by_taxonomy($query)
{
    if (is_admin() && $query->is_main_query() && $query->is_search) {
        // 管理画面の検索クエリをタクソノミーで拡張
        $query->set('tax_query', array(
            'relation' => 'OR',
            array(
                'taxonomy' => 'house-type',
                'field' => 'name',
                'terms' => $query->query_vars['s'],
            ),
            array(
                'taxonomy' => 'region',
                'field' => 'name',
                'terms' => $query->query_vars['s'],
            ),
        ));
        $query->set('s', ''); // タクソノミー検索用にキーワードをクリア
    }
}
add_action('pre_get_posts', 'admin_search_by_taxonomy');



// カスタム投稿タイプとタクソノミーの登録を1つの関数にまとめる
function register_artifacts_custom_post_type_and_taxonomies()
{
    // Artifacts カスタム投稿タイプを登録
    register_post_type(
        'artifacts',
        array(
            'label' => 'Artifacts',
            'labels' => array(
                'add_new' => '新規Artifacts追加',
                'edit_item' => 'Artifactsの編集',
                'view_item' => 'Artifactsを表示',
                'search_items' => 'Artifactsを検索',
                'not_found' => 'Artifactsは見つかりませんでした。',
                'not_found_in_trash' => 'ゴミ箱にArtifactsはありませんでした.'
            ),
            'public' => true,
            'description' => 'カスタム投稿タイプ「Artifacts」の説明文です。',
            'hierarchical' => false,
            'has_archive' => true,
            'show_in_rest' => true,
            'supports' => array(
                'title',
                'editor',
                'thumbnail',
                'excerpt',
                'custom-fields',
                'revisions',
                'comments',
                'trackbacks',
                'page-attributes',
                'post-formats',
            ),
            'menu_position' => 5,
            'rewrite' => array('slug' => 'artifacts'),
            'taxonomies' => array('artifact_category', 'artifact_tag')
        )
    );

    // Artifacts カテゴリータクソノミーの登録
    register_taxonomy(
        'artifact_category',
        'artifacts',
        array(
            'label' => 'Artifact Categories',
            'labels' => array(
                'popular_items' => 'よく使うArtifact Categories',
                'edit_item' => 'Artifact Categoriesを編集',
                'add_new_item' => '新規Artifact Categoriesを追加',
                'search_items' => 'Artifact Categoriesを検索'
            ),
            'public' => true,
            'description' => 'Artifact Categoriesの説明文です。',
            'hierarchical' => true,
            'show_in_rest' => true
        )
    );

    // Artifacts タグタクソノミーの登録
    register_taxonomy(
        'artifact_tag',
        'artifacts',
        array(
            'label' => 'Artifact Tags',
            'labels' => array(
                'popular_items' => 'よく使うArtifact Tags',
                'edit_item' => 'Artifact Tagsを編集',
                'add_new_item' => '新規Artifact Tagsを追加',
                'search_items' => 'Artifact Tagsを検索'
            ),
            'public' => true,
            'description' => 'Artifact Tagsの説明文です。',
            'hierarchical' => false,
            'update_count_callback' => '_update_post_term_count',
            'show_in_rest' => true
        )
    );

    // 通常のカテゴリーを有効にする
    register_taxonomy_for_object_type('category', 'artifacts');
}

// init アクションフックで登録
add_action('init', 'register_artifacts_custom_post_type_and_taxonomies');

// 親タクソノミーの登録
function create_artifact_parent_taxonomy()
{
    register_taxonomy(
        'artifact_parent_category',
        'artifacts',
        array(
            'label' => 'Artifact Parent Category',
            'hierarchical' => true
        )
    );
}
add_action('init', 'create_artifact_parent_taxonomy');




//固定ページの追加キャラクター　Template: character-build.php
//function my_styles()  {
// genshin-impact-ayaka-build　スラッグ　用のCSS
//if ( is_page('genshin-impact-ayaka-build') ) {
//wp_enqueue_style( 'sample', get_template_directory_uri() . '/css/sample.css', array(), '1.0.0' );
//wp_enqueue_style( 'sample', get_template_directory_uri() . '/css/sample.css', array(), '1.0.0' );
//更新・追加..
//}
//}
//add_action( 'wp_enqueue_scripts', 'my_styles' );

//function custom_page_styles() {
// ページIDが9473の場合
//if ( is_page( 9473 ) ) {
//echo '<style>';
//echo '.content{';
//echo 'background-color: #1c1f46;';
//echo '    background-image: url("path/to/your/background-image.jpg");';
//echo '    background-size: cover;';
// 他のスタイルプロパティを必要に応じて追加
//echo '}';
//echo '</style>';
//}
//}
//add_action( 'wp_head', 'custom_page_styles' );






// 原神のキャラクター情報の追加

function get_genshin_characters()
{
    // 既存のキャラクター情報　page-genshin-character.php
    $characters = array(
        "Aloy" => array("element" => "cryo", "rarity" => "rarity-5", "weapon" => "Bow", "tier" => "A"),
        "Albedo" => array("element" => "geo", "rarity" => "rarity-5", "weapon" => "Sword", "tier" => "B"),
        "Alhaitham" => array("element" => "dendro", "rarity" => "rarity-5", "weapon" => "Sword", "tier" => "C"),
        "Amber" => array("element" => "pyro", "rarity" => "rarity-4", "weapon" => "Bow", "tier" => "D"),
        "Ayaka" => array("element" => "cryo", "rarity" => "rarity-5", "weapon" => "Sword", "tier" => "S"),
        "Ayato" => array("element" => "hydro", "rarity" => "rarity-5", "weapon" => "Sword", "tier" => "B"),
        "Barbara" => array("element" => "hydro", "rarity" => "rarity-4", "weapon" => "Catalyst", "tier" => "A"),
        "Baizhu" => array("element" => "dendro", "rarity" => "rarity-5", "weapon" => "Catalyst", "tier" => "B"),
        "Beidou" => array("element" => "electro", "rarity" => "rarity-4", "weapon" => "Claymore", "tier" => "S"),
        "Bennett" => array("element" => "pyro", "rarity" => "rarity-4", "weapon" => "Sword", "tier" => "S"),
        "Candace" => array("element" => "hydro", "rarity" => "rarity-4", "weapon" => "Polearm", "tier" => "A"),
        "Childe" => array("element" => "hydro", "rarity" => "rarity-5", "weapon" => "Bow", "tier" => "A"),
        "Chongyun" => array("element" => "cryo", "rarity" => "rarity-4", "weapon" => "Claymore", "tier" => "A"),
        "Collei" => array("element" => "dendro", "rarity" => "rarity-4", "weapon" => "Bow", "tier" => "A"),
        "Cyno" => array("element" => "electro", "rarity" => "rarity-5", "weapon" => "Polearm", "tier" => "B"),
        "Dehya" => array("element" => "pyro", "rarity" => "rarity-5", "weapon" => "Claymore", "tier" => "A"),
        "Diluc" => array("element" => "pyro", "rarity" => "rarity-5", "weapon" => "Claymore", "tier" => "A"),
        "Diona" => array("element" => "cryo", "rarity" => "rarity-4", "weapon" => "Bow", "tier" => "A"),
        "Dori" => array("element" => "electro", "rarity" => "rarity-4", "weapon" => "Claymore", "tier" => "A"),
        "Eula" => array("element" => "cryo", "rarity" => "rarity-5", "weapon" => "Claymore", "tier" => "B"),
        "Faruzan" => array("element" => "anemo", "rarity" => "rarity-4", "weapon" => "Bow", "tier" => "S"),
        "Fischl" => array("element" => "electro", "rarity" => "rarity-4", "weapon" => "Bow", "tier" => "S"),
        "Freminet" => array("element" => "cryo", "rarity" => "rarity-4", "weapon" => "Claymore", "tier" => "D"),
        "Furina" => array("element" => "hydro", "rarity" => "rarity-5", "weapon" => "Sword", "tier" => "B"),
        "Ganyu" => array("element" => "cryo", "rarity" => "rarity-5", "weapon" => "Bow", "tier" => "A"),
        "Gorou" => array("element" => "geo", "rarity" => "rarity-5", "weapon" => "Bow", "tier" => "S"),
        "Hu Tao" => array("element" => "pyro", "rarity" => "rarity-5", "weapon" => "Polearm", "tier" => "B"),
        "Heizou" => array("element" => "anemo", "rarity" => "rarity-4", "weapon" => "Catalyst", "tier" => "A"),
        "Itto" => array("element" => "geo", "rarity" => "rarity-5", "weapon" => "Claymore", "tier" => "A"),
        "Jean" => array("element" => "anemo", "rarity" => "rarity-5", "weapon" => "Sword", "tier" => "A"),
        "Kaveh" => array("element" => "dendro", "rarity" => "rarity-4", "weapon" => "Claymore", "tier" => "S"),
        "Kaeya" => array("element" => "cryo", "rarity" => "rarity-4", "weapon" => "Sword", "tier" => "B"),
        "Kazuha" => array("element" => "anemo", "rarity" => "rarity-5", "weapon" => "Sword", "tier" => "A"),
        "Keqing" => array("element" => "electro", "rarity" => "rarity-5", "weapon" => "Sword", "tier" => "A"),
        "Kirara" => array("element" => "dendro", "rarity" => "rarity-4", "weapon" => "Sword", "tier" => "A"),
        "Klee" => array("element" => "pyro", "rarity" => "rarity-5", "weapon" => "Catalyst", "tier" => "A"),
        "Kuki Shinobu" => array("element" => "electro", "rarity" => "rarity-4", "weapon" => "Sword", "tier" => "A"),
        "Kokomi" => array("element" => "hydro", "rarity" => "rarity-5", "weapon" => "Catalyst", "tier" => "S"),
        "Layla" => array("element" => "cryo", "rarity" => "rarity-4", "weapon" => "Sword", "tier" => "A"),
        "Lisa" => array("element" => "electro", "rarity" => "rarity-4", "weapon" => "Catalyst", "tier" => "D"),
        "Lynette" => array("element" => "anemo", "rarity" => "rarity-4", "weapon" => "Sword", "tier" => "A"),
        "Lyney" => array("element" => "pyro", "rarity" => "rarity-5", "weapon" => "Bow", "tier" => "S"),
        "Mika" => array("element" => "cryo", "rarity" => "rarity-4", "weapon" => "Polearm", "tier" => "D"),
        "Mona" => array("element" => "hydro", "rarity" => "rarity-5", "weapon" => "Catalyst", "tier" => "A"),
        "Nahida" => array("element" => "dendro", "rarity" => "rarity-5", "weapon" => "Catalyst", "tier" => "S"),
        "Neuvillette" => array("element" => "hydro", "rarity" => "rarity-5", "weapon" => "Catalyst", "tier" => "S"),
        "Nilou" => array("element" => "hydro", "rarity" => "rarity-5", "weapon" => "Sword", "tier" => "A"),
        "Ningguang" => array("element" => "geo", "rarity" => "rarity-4", "weapon" => "Catalyst", "tier" => "C"),
        "Noelle" => array("element" => "geo", "rarity" => "rarity-4", "weapon" => "Claymore", "tier" => "C"),
        "Qiqi" => array("element" => "cryo", "rarity" => "rarity-5", "weapon" => "Sword", "tier" => "B"),
        "Raiden" => array("element" => "electro", "rarity" => "rarity-5", "weapon" => "Polearm", "tier" => "S"),
        "Razor" => array("element" => "electro", "rarity" => "rarity-4", "weapon" => "Claymore", "tier" => "A"),
        "Rosaria" => array("element" => "cryo", "rarity" => "rarity-4", "weapon" => "Polearm", "tier" => "A"),
        "Sara" => array("element" => "electro", "rarity" => "rarity-4", "weapon" => "Bow", "tier" => "A"),
        "Sayu" => array("element" => "anemo", "rarity" => "rarity-4", "weapon" => "Claymore", "tier" => "C"),
        "Sucrose" => array("element" => "anemo", "rarity" => "rarity-4", "weapon" => "Catalyst", "tier" => "S"),
        "Thoma" => array("element" => "pyro", "rarity" => "rarity-4", "weapon" => "Polearm", "tier" => "C"),
        "Xiangling" => array("element" => "pyro", "rarity" => "rarity-4", "weapon" => "Polearm", "tier" => "S"),
        "Xingqiu" => array("element" => "hydro", "rarity" => "rarity-4", "weapon" => "Sword", "tier" => "S"),
        "Xinyan" => array("element" => "pyro", "rarity" => "rarity-4", "weapon" => "Claymore", "tier" => "A"),
        "Yae Miko" => array("element" => "electro", "rarity" => "rarity-5", "weapon" => "Catalyst", "tier" => "S"),
        "Yanfei" => array("element" => "pyro", "rarity" => "rarity-4", "weapon" => "Catalyst", "tier" => "A"),
        "Yaoyao" => array("element" => "dendro", "rarity" => "rarity-4", "weapon" => "Polearm", "tier" => "C"),
        "Yelan" => array("element" => "hydro", "rarity" => "rarity-5", "weapon" => "Bow", "tier" => "S"),
        "Yoimiya" => array("element" => "pyro", "rarity" => "rarity-5", "weapon" => "Bow", "tier" => "C"),
        "Yun Jin" => array("element" => "geo", "rarity" => "rarity-4", "weapon" => "Polearm", "tier" => "S"),
        "Zhongli" => array("element" => "geo", "rarity" => "rarity-5", "weapon" => "Polearm", "tier" => "S"),
        "Wriothesley" => array("element" => "cryo", "rarity" => "rarity-5", "weapon" => "Catalyst", "tier" => "S"),
        "Wanderer" => array("element" => "anemo", "rarity" => "rarity-5", "weapon" => "Catalyst", "tier" => "A"),
        "Venti" => array("element" => "anemo", "rarity" => "rarity-5", "weapon" => "Bow", "tier" => "S"),
        "Tighnari" => array("element" => "dendro", "rarity" => "rarity-5", "weapon" => "Bow", "tier" => "S"),
        "Xiao" => array("element" => "anemo", "rarity" => "rarity-5", "weapon" => "Polearm", "tier" => "A"),
        "Navia" => array("element" => "geo", "rarity" => "rarity-5", "weapon" => "Claymore", "tier" => "S"),
        "Chevreuse" => array("element" => "pyro", "rarity" => "rarity-4", "weapon" => "Polearm", "tier" => "D"),
        "Gaming" => array("element" => "pyro", "rarity" => "rarity-4", "weapon" => "Claymore", "tier" => "A"),
        "Xianyun" => array("element" => "anemo", "rarity" => "rarity-5", "weapon" => "Catalyst", "tier" => "A"),
        "Chiori" => array("element" => "geo", "rarity" => "rarity-5", "weapon" => "Sword", "tier" => "A"),
        "Neuvillette" => array("element" => "hydro", "rarity" => "rarity-5", "weapon" => "Polearm", "tier" => "S", "Character" => ""),
        "Arlecchino" => array("element" => "pyro", "rarity" => "rarity-5", "weapon" => "Polearm", "tier" => "S", "Character" => ""),
        "Clorinde" => array("element" => "electro", "rarity" => "rarity-5", "weapon" => "Sword", "tier" => "S", "Character" => ""),
        "Sethos" => array("element" => "electro", "rarity" => "rarity-4", "weapon" => "Bow", "tier" => "B", "Character" => ""),
        // 新しいキャラクターを既存の配列に手動で移動

        // 不足しているインデックスを追加

    );

    // 新しいキャラクターを追加して、手動で、$characters 配列に追加する。
    $new_character = array(

        "Sigewinne" => array("element" => "hydro", "rarity" => "rarity-5", "weapon" => "Bow", "tier" => "S", "Character" => "New"), // 新しいキャラクターを既存の配列に手動で移動


    );


    // 必要なだけキャラクターを追加してください


    // 新しいキャラクター以外のキャラクターを$charactersから$sorted_charactersに移動
    $sorted_characters = array(); // $sorted_characters 配列を初期化

    // $characters 配列内の各要素に対してループ処理を行う
    foreach ($characters as $name => $info) {
        // もし現在のキャラクターが "Character" インデックスを持ち、その値が 'New' である場合
        if (isset($info['Character']) && $info['Character'] === 'New') {
            // 新しいキャラクターとして $new_character 配列に追加
            $new_character[$name] = $info;
        } else {
            // それ以外の場合、現在のキャラクターを $sorted_characters 配列に追加
            $sorted_characters[$name] = $info;
        }
    }

    // 新しいキャラクターが先頭に来るように再構築
    ksort($new_character);
    ksort($sorted_characters);

    // 新しいキャラクターを$charactersに追加
    $characters = $new_character + $sorted_characters;

    // 更新されたキャラクター配列を返す
    return $characters;
}




// メタボックスから画像を取得して Slickスライダーを表示する関数
function display_slider_images()
{
    $slider_images = get_post_meta(get_the_ID(), '_slider_images', true);

    if (!empty($slider_images)) {
        echo '<div class="naigai-slider-wrap">';
        echo '  <div class="test-slider test-slider-for">';
        foreach ($slider_images as $image) {
            echo '    <div class="test"><img src="' . esc_url($image) . '" alt="Slider Image"></div>';
        }
        echo '  </div>';

        echo '  <div class="slider test-slider-nav">';
        foreach ($slider_images as $image) {
            echo '    <div class="test"><img src="' . esc_url($image) . '" alt="Slider Image"></div>';
        }
        echo '  </div>';
        echo '</div>';
    }
}




// メタボックスの追加 slick 画像用のメタボックス
function add_slider_images_metabox()
{
    $post_types = array('post', 'genshin', 'house', 'artifacts'); // 使用するすべての投稿タイプ　別にこのように必ず記載

    // 投稿タイプをループしてメタボックスを追加
    foreach ($post_types as $post_type) {
        add_meta_box(
            'slider_images_metabox',       // メタボックスのID
            'Slick スライダーイメージ',               // メタボックスのタイトル
            'slider_images_metabox_callback', // コールバック関数
            $post_type,                    // 対象の投稿タイプ
            'normal',                      // 表示位置
            'default'                      // プライオリティ
        );
    }
}
add_action('add_meta_boxes', 'add_slider_images_metabox');


// メタボックスの表示
function slider_images_metabox_callback($post)
{
    // 現在の画像を取得
    $slider_images = get_post_meta($post->ID, '_slider_images', true);

    // nonce フィールドの出力
    wp_nonce_field('slider_images_metabox', 'slider_images_metabox_nonce');

    // メタボックスの内容を表示
    echo '<label for="slider_images">Slider Images:</label>';
    echo '<input type="button" value="Select Images" class="button" id="slider_images_select">';
    echo '<ul id="slider_images_container">';

    if (!empty($slider_images)) {
        foreach ($slider_images as $image) {
            echo '<li><img src="' . esc_url($image) . '" alt="Slider Image">' .
                '<a href="#" class="remove-image">Remove</a></li>';
        }
    }

    echo '</ul>';
    echo '<input type="hidden" name="slider_images" id="slider_images" value="' . esc_attr(wp_json_encode($slider_images)) . '">';

    // JavaScriptの追加
    wp_enqueue_media();
?>



    <script>
        jQuery(document).ready(function($) {
            // images 変数を配列として初期化する
            let images = [];

            // 画像選択ボタンがクリックされたときの処理
            $('#slider_images_select').on('click', function() {
                // メディアアップローダーの設定
                const mediaUploader = wp.media({
                    title: 'Select Images', // ウィンドウのタイトル
                    multiple: true // 複数の画像を選択可能にする
                });

                // 画像が選択されたときの処理
                mediaUploader.on('select', function() {
                    // 選択された画像の情報を取得
                    const attachments = mediaUploader.state().get('selection').toJSON();

                    // 選択された画像を配列に追加
                    attachments.forEach(attachment => {
                        images.push(attachment.url);
                        // 選択された画像を表示する
                        $('#slider_images_container').append(
                            `<li><img src="${attachment.url}" alt="Slider Image">
                    <a href="#" class="remove-image">Remove</a></li>`
                        );
                    });

                    // 更新された画像データをhiddenフィールドにセット
                    $('#slider_images').val(JSON.stringify(images));
                });

                // メディアアップローダーを開く
                mediaUploader.open();
                return false;
            });

            // 画像削除リンクがクリックされたときの処理
            $('#slider_images_container').on('click', '.remove-image', function(e) {
                e.preventDefault(); // デフォルトのリンク動作をキャンセル

                const imageContainer = $(this).parent(); // 削除する画像の親要素を取得
                const imageUrl = imageContainer.find('img').attr('src'); // 画像のURLを取得

                // 削除する画像を配列から除去
                images = images.filter(url => url !== imageUrl);

                // 更新された画像データをhiddenフィールドにセット
                $('#slider_images').val(JSON.stringify(images));

                // 画面上からも画像を削除
                imageContainer.remove();
            });
        });
    </script>

<?php
}


// メタボックスを保存
function save_slider_images_metabox($post_id)
{
    // nonce フィールドの検証
    if (!isset($_POST['slider_images_metabox_nonce']) || !wp_verify_nonce($_POST['slider_images_metabox_nonce'], 'slider_images_metabox')) {
        return;
    }

    // 自動保存を防ぐ
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // 権限を確認
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // 画像データを保存
    if (isset($_POST['slider_images'])) {
        // アンスラッシュを適用
        $slider_images = json_decode(wp_unslash($_POST['slider_images']), true);
        update_post_meta($post_id, '_slider_images', $slider_images);
    }
}
add_action('save_post', 'save_slider_images_metabox', 10, 1);






// ✅ グローバル変数の初期化
$custom_fields = array();

// ✅ カスタムフィールド表示関数
function display_fields_table($post, $table_number)
{
    global $custom_fields;

    if ($table_number == 1) {
        $custom_fields = array(
            'PropertyName' => '物件名',
            'Price' => '価格',
            'UnitsSold' => '販売戸数',
            'TotalUnits' => '総販売戸数',
            'Layout' => '間取り',
            'BuildingArea' => '建物面積',
            'LandArea' => '土地面積',
            'Location' => '所在地',
            'Transport' => '交通',
            'OwnershipType' => '所有権の種類',
            'LandCategory' => '土地の分類',
            'Zoning' => '用途地域',
            'UrbanPlanning' => '都市計画',
            'Remarks' => '備考',
            'BuildingCoverageRatio' => '建ぺい率',
            'FloorAreaRatio' => '容積率',
            'UnitsSoldArea' => '販売区画数',
            'TotalUnitsSoldArea' => '総販売区画数',
            'Image1' => '画像1',
            'Image2' => '画像2',
            'Image3' => '画像3',
            'Image4' => '画像4',
            'Image5' => '画像5',
            'Image6' => '画像6',
            'Image7' => '画像7',
            'Image8' => '画像8',
            'Image9' => '画像9',
            'Image10' => '画像10',
            'GoogleEmbedcode' => 'GoogleMap'
        );
    } elseif ($table_number == 2) {
        $custom_fields = array(
            'NewPropertyName' => '新物件名',
            'NewPrice' => '新価格',
            'NewUnitsSold' => '新販売戸数',
            'NewTotalUnits' => '新総販売戸数',
            'NewLayout' => '新間取り',
            'NewBuildingArea' => '新建物面積',
            'NewLandArea' => '新土地面積',
            'NewLocation' => '新所在地',
            'NewTransport' => '新交通',
            'NewOwnershipType' => '新所有権の種類',
            'NewLandCategory' => '新土地の分類',
            'NewZoning' => '新用途地域',
            'NewUrbanPlanning' => '新都市計画',
            'NewRemarks' => '新備考',
            'NewBuildingCoverageRatio' => '新建ぺい率',
            'NewFloorAreaRatio' => '新容積率',
            'NewUnitsSoldArea' => '新販売区画数',
            'NewTotalUnitsSoldArea' => '新総販売区画数',
            'NewImage1' => '新画像1',
            'NewImage2' => '新画像2',
            'NewImage3' => '新画像3',
            'NewImage4' => '新画像4',
            'NewImage5' => '新画像5',
            'NewImage6' => '新画像6',
            'NewImage7' => '新画像7',
            'NewImage8' => '新画像8',
            'NewImage9' => '新画像9',
            'NewImage10' => '新画像10',
            'NewGoogleEmbedcode' => 'NewGoogleMap'
        );
    } elseif ($table_number == 3) {
        $custom_fields = array(
            'AreaDetail1' => 'エリア詳細1',
            'AreaDetail2' => 'エリア詳細2',
            'AreaDetail3' => 'エリア詳細3',
            'AreaDetail4' => 'エリア詳細4',
            'AreaDetail5' => 'エリア詳細5',
            'AreaDetail6' => 'エリア詳細6',
            'AreaDetail7' => 'エリア詳細7',
            'AreaDetail8' => 'エリア詳細8',
            'AreaDetail9' => 'エリア詳細9',
            'AreaDetail10' => 'エリア詳細10',
            'AreaDetail11' => 'エリア詳細11',
            'AreaDetail12' => 'エリア詳細12',
            'AreaDetail13' => 'エリア詳細13',
            'AreaDetail14' => 'エリア詳細14',
            'AreaDetail15' => 'エリア詳細15',
            'AreaImageDetail1' => 'エリア画像詳細1',
            'AreaImageDetail2' => 'エリア画像詳細2',
            'AreaImageDetail3' => 'エリア画像詳細3',
            'AreaImageDetail4' => 'エリア画像詳細4',
            'AreaImageDetail5' => 'エリア画像詳細5',
            'AreaImageDetail6' => 'エリア画像詳細6',
            'AreaImageDetail7' => 'エリア画像詳細7',
            'AreaImageDetail8' => 'エリア画像詳細8',
            'AreaImageDetail9' => 'エリア画像詳細9',
            'AreaImageDetail10' => 'エリア画像詳細10',
            'AreaImageDetail11' => 'エリア画像詳細11',
            'AreaImageDetail12' => 'エリア画像詳細12',
            'AreaImageDetail13' => 'エリア画像詳細13',
            'AreaImageDetail14' => 'エリア画像詳細14',
            'AreaImageDetail15' => 'エリア画像詳細15'
        );
    }

    // フィールドをテーブル形式で出力
    echo '<table class="form-table">';
    wp_nonce_field('custom_field_nonce_action', 'custom_field_nonce_name');

    foreach ($custom_fields as $field_name => $label) {
        $value = get_post_meta($post->ID, $field_name, true);

        echo '<tr><th><label for="' . esc_attr($field_name) . '">' . esc_html($label) . '</label></th><td>';

        if ($field_name === 'Price') {
            $placeholder = ($value === '' || $value == 0) ? '売却済み' : esc_attr($value);
            echo '<input type="text" name="' . esc_attr($field_name) . '" value="' . esc_attr($value) . '" class="regular-text" placeholder="' . $placeholder . '">';
        } elseif (strpos($field_name, 'Image') !== false) {
            echo '<input type="text" name="' . esc_attr($field_name) . '" id="' . esc_attr($field_name) . '" value="' . esc_attr($value) . '" class="regular-text">';
            echo '<input type="button" class="button button-secondary select-image" data-field="' . esc_attr($field_name) . '" value="画像を選択">';
            echo '<input type="button" class="button button-secondary delete-image" data-field="' . esc_attr($field_name) . '" value="画像を削除">';
            echo '<div id="' . esc_attr($field_name) . '_preview">';
            if (!empty($value)) {
                $image_url = wp_get_attachment_url($value);
                echo '<img src="' . esc_url($image_url) . '" style="max-width:100%; height:auto;">';
            }
            echo '</div>';
        } elseif ($field_name === 'Location') {
            $location_value = esc_attr($value);
            $google_embed_code = get_post_meta($post->ID, 'GoogleEmbedcode', true);
            echo '<input type="text" name="Location" value="' . $location_value . '" class="regular-text" placeholder="所在地の住所を入力"><br>';
            echo '<label for="google_embed_code">Googleマップ埋め込みコード：</label>';
            echo '<textarea name="GoogleEmbedcode" class="regular-text" placeholder="埋め込みiframeコードを入力" rows="4" cols="50">' . esc_textarea($google_embed_code) . '</textarea><br>';
            echo (!empty($google_embed_code)) ? '<div class="google-map-preview" style="border:1px solid #ddd;padding:10px;margin-top:10px;">' . wp_kses_post($google_embed_code) . '</div>' : '<p>埋め込みコードがありません。</p>';
        } else {
            echo '<input type="text" name="' . esc_attr($field_name) . '" value="' . esc_attr($value) . '" class="regular-text">';
        }

        echo '</td></tr>';
    }

    echo '</table>';
}


function enqueue_custom_admin_scripts()
{
    // 管理画面でのみスクリプトを読み込む
    if (is_admin()) {
        wp_enqueue_media(); // WordPressメディアアップローダーを利用するため

        // カスタムJavaScriptをインラインで追加
        $script = "
            jQuery(document).ready(function($) {
                var mediaUploaders = {}; // 各画像用のメディアアップローダーを格納するオブジェクト

                // 画像選択ボタンがクリックされた時の処理
                $(document).on('click', '.select-image', function(e) {
                    e.preventDefault();
                    var button = $(this);
                    var field = button.data('field');

                    // メディアアップローダーの作成または再利用
                    if (!mediaUploaders[field]) {
                        mediaUploaders[field] = wp.media.frames.file_frame = wp.media({
                            title: '画像を選択またはアップロード',
                            button: {
                                text: 'この画像を使用'
                            },
                            multiple: false
                        });

                        // 画像が選択された時の処理
                        mediaUploaders[field].on('select', function() {
                            var attachment = mediaUploaders[field].state().get('selection').first().toJSON();
                            $('#' + field).val(attachment.id); // 画像IDをフィールドに設定
                            $('#' + field + '_preview').html('<img src=\"' + attachment.url + '\" style=\"max-width:100%; height:auto;\">');
                            mediaUploaders[field].close(); // メディアアップローダーを閉じる
                        });
                    }

                    // メディアアップローダーを開く
                    mediaUploaders[field].open();
                });

                // 画像削除ボタンがクリックされた時の処理
                $(document).on('click', '.delete-image', function(e) {
                    e.preventDefault();
                    var button = $(this);
                    var field = button.data('field');

                    $('#' + field).val(''); // 画像IDをクリア
                    $('#' + field + '_preview').html(''); // 画像プレビューを削除
                });
            });
        ";

        // JavaScriptをインラインで出力
        wp_add_inline_script('jquery', $script);
    }
}
add_action('admin_enqueue_scripts', 'enqueue_custom_admin_scripts');

// 画像、テキストのメタボックスを登録
function add_custom_meta_boxes()
{
    global $custom_fields;

    // カスタムフィールドが配列かどうか確認
    if (is_array($custom_fields)) {
        foreach ($custom_fields as $field_name => $label) {

            // 画像用メタボックス
            if (strpos($field_name, 'Image') !== false) {
                add_meta_box(
                    $field_name . '_metabox',  // メタボックスのID
                    ucwords(str_replace('-', ' ', $field_name)) . ' Image',  // メタボックスのタイトル
                    'custom_image_meta_box_callback',  // コールバック関数
                    array('post', 'house'),  // 投稿タイプ
                    'normal',  // 表示位置
                    'high',  // 優先度
                    array('field_name' => $field_name)  // コールバック関数に渡す引数
                );
            } else {
                // テキスト用メタボックス
                add_meta_box(
                    $field_name . '_metabox',  // メタボックスのID
                    ucwords(str_replace('-', ' ', $field_name)) . ' Text',  // メタボックスのタイトル
                    'custom_field_meta_box_callback',  // コールバック関数
                    array('post', 'house'),  // 投稿タイプ
                    'normal',  // 表示位置
                    'high',  // 優先度
                    array('field_name' => $field_name)  // コールバック関数に渡す引数
                );
            }
        }
    } else {
        error_log('$custom_fields は配列ではありません。');  // カスタムフィールドが配列でない場合にエラーをログ出力
    }
}
add_action('add_meta_boxes', 'add_custom_meta_boxes');




// callback 関数は、WordPress の管理画面でメタボックスをレンダリングする役割を担います。この関数で HTML を出力することで、ユーザーがメタボックス内で情報を入力したり、選択したりできるようにします。

// 画像のメタボックスのコールバック関数
function custom_image_meta_box_callback($post, $metabox)
{
    $field_name = $metabox['args']['field_name'];
    $value = get_post_meta($post->ID, $field_name, true);

    // nonceを生成
    wp_nonce_field('save_custom_fields_nonce', 'custom_fields_nonce');
?>
    <!-- フォーム部分 -->
    <label for="<?php echo esc_attr($field_name); ?>"><?php echo $field_name; ?>:</label>
    <?php if ($value): ?>
        <img src="<?php echo esc_url($value); ?>" alt="<?php echo esc_attr($field_name); ?>" style="max-width: 100%; height: auto;">
    <?php endif; ?>
    <input type="file" name="<?php echo esc_attr($field_name); ?>" id="<?php echo esc_attr($field_name); ?>" class="regular-text">
<?php
}

// テキスト入力の為のカスタムフィールドのメタボックスのコールバック関数
function custom_field_meta_box_callback($post, $metabox)
{
    // フィールド名を取得
    $field_name = $metabox['args']['field_name'];
    // 投稿のカスタムフィールドの値を取得
    $value = get_post_meta($post->ID, $field_name, true);

    // nonce を追加
    wp_nonce_field('save_custom_fields_nonce', 'custom_fields_nonce');
?>
    <!-- フィールドのラベルと入力フォーム -->
    <?php //カスタムフィールドの値が_ 含まれているのは　スペースとして扱う　また先頭値を大文字に変換
    ?>
    <label for="<?php echo esc_attr($field_name); ?>"><?php echo ucwords(str_replace('_', ' ', $field_name)); ?>:</label>
    <input type="text" name="<?php echo esc_attr($field_name); ?>" id="<?php echo esc_attr($field_name); ?>" value="<?php echo esc_attr($value); ?>" class="regular-text">
    <?php
}



// $post_id: どの投稿に対してカスタムフィールドを追加または更新するかを示す ID。
// $field_name: meta_key に相当する、カスタムフィールドの名前（例: Property_Name）。
// $sanitized_value: そのフィールドに入力された値、これが meta_value になります。

// ✅ カスタムフィールド保存処理
function save_custom_fields_data($post_id)
{
    error_log('🔍 save_custom_fields_data() 実行: post_id=' . $post_id);

    if ((defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || wp_is_post_revision($post_id)) return;

    $post_type = get_post_type($post_id);
    if (!in_array($post_type, ['post', 'house'])) return;

    if (!isset($_POST['custom_field_nonce_name']) || !wp_verify_nonce($_POST['custom_field_nonce_name'], 'custom_field_nonce_action')) return;

    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';

    $custom_fields = array_merge(
        [
            'PropertyName',
            'Price',
            'UnitsSold',
            'TotalUnits',
            'Layout',
            'BuildingArea',
            'LandArea',
            'Location',
            'Transport',
            'OwnershipType',
            'LandCategory',
            'Zoning',
            'UrbanPlanning',
            'Remarks',
            'BuildingCoverageRatio',
            'FloorAreaRatio',
            'UnitsSoldArea',
            'TotalUnitsSoldArea',
            'Image1',
            'Image2',
            'Image3',
            'Image4',
            'Image5',
            'Image6',
            'Image7',
            'Image8',
            'Image9',
            'Image10',
            'GoogleEmbedcode'
        ],
        [
            'NewPropertyName',
            'NewPrice',
            'NewUnitsSold',
            'NewTotalUnits',
            'NewLayout',
            'NewBuildingArea',
            'NewLandArea',
            'NewLocation',
            'NewTransport',
            'NewOwnershipType',
            'NewLandCategory',
            'NewZoning',
            'NewUrbanPlanning',
            'NewRemarks',
            'NewBuildingCoverageRatio',
            'NewFloorAreaRatio',
            'NewUnitsSoldArea',
            'NewTotalUnitsSoldArea',
            'NewImage1',
            'NewImage2',
            'NewImage3',
            'NewImage4',
            'NewImage5',
            'NewImage6',
            'NewImage7',
            'NewImage8',
            'NewImage9',
            'NewImage10',
            'NewGoogleEmbedcode'
        ],
        [
            'AreaDetail1',
            'AreaDetail2',
            'AreaDetail3',
            'AreaDetail4',
            'AreaDetail5',
            'AreaDetail6',
            'AreaDetail7',
            'AreaDetail8',
            'AreaDetail9',
            'AreaDetail10',
            'AreaDetail11',
            'AreaDetail12',
            'AreaDetail13',
            'AreaDetail14',
            'AreaDetail15',
            'AreaImageDetail1',
            'AreaImageDetail2',
            'AreaImageDetail3',
            'AreaImageDetail4',
            'AreaImageDetail5',
            'AreaImageDetail6',
            'AreaImageDetail7',
            'AreaImageDetail8',
            'AreaImageDetail9',
            'AreaImageDetail10',
            'AreaImageDetail11',
            'AreaImageDetail12',
            'AreaImageDetail13',
            'AreaImageDetail14',
            'AreaImageDetail15'
        ]
    );

    foreach ($custom_fields as $field_name) {
        $value = null;

        if (isset($_FILES[$field_name]) && !empty($_FILES[$field_name]['name'])) {
            $attachment_id = media_handle_upload($field_name, $post_id);
            if (!is_wp_error($attachment_id)) {
                $value = $attachment_id;
                error_log("📷 アップロード成功: {$field_name} = {$attachment_id}");
            } else {
                error_log("❌ アップロード失敗: {$field_name} = " . $attachment_id->get_error_message());
                continue;
            }
        } elseif (isset($_POST[$field_name])) {
            $raw = trim($_POST[$field_name]);

            if (strpos($field_name, 'Image') !== false || strpos($field_name, 'ImageDetail') !== false) {
                $value = intval($raw);
            } elseif (strpos($field_name, 'Price') !== false) {
                $value = ($raw === '') ? 0 : floatval($raw);
            } elseif (strpos($field_name, 'Embedcode') !== false) {
                $value = wp_kses($raw, [
                    'iframe' => [
                        'src' => true,
                        'width' => true,
                        'height' => true,
                        'style' => true,
                        'allowfullscreen' => true,
                        'loading' => true,
                        'referrerpolicy' => true,
                    ]
                ]);
            } else {
                $value = sanitize_text_field($raw);
            }
        }

        if ($value !== null) {
            update_post_meta($post_id, $field_name, $value);
            error_log("✅ {$field_name} 保存済み: {$value}");
        }
    }

    return $post_id;
}
add_action('save_post', 'save_custom_fields_data', 10, 1);









/**
 * ① functions.php に追加：
 * ブログ用の「暮らしの特徴」＋「画像（最大10枚）」のメタボックス定義と保存処理
 */

// コメント機能を blog 投稿タイプに追加（未設定の場合）
function add_blog_post_type_support_comments()
{
    add_post_type_support('blog', 'comments');
}
add_action('init', 'add_blog_post_type_support_comments');

// メタボックス追加
function add_blog_lifestyle_meta_box()
{
    add_meta_box(
        'blog_lifestyle_meta_box',
        '暮らしの特徴（生活提案）＋画像',
        'render_blog_lifestyle_meta_box',
        'blog',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'add_blog_lifestyle_meta_box');

// メタボックス表示
function render_blog_lifestyle_meta_box($post)
{
    // テキスト項目
    $fields = [
        'blog_lifestyle_heading' => '暮らしの特徴 見出しタイトル', // ← 新規追加
        'blog_garden' => '家庭菜園可',
        'blog_all_electric' => 'オール電化',
        'blog_water_supply' => '上下水道完備',
        'blog_rural_life' => '田舎暮らし向け',
        'blog_permanent_residence' => '定住向け',
        'blog_near_school' => '学校が近い',
        'blog_near_hospital' => '病院が近い',
        'blog_dual_life' => '二拠点生活向け',
        'blog_access_tokyo' => 'アクセス良好（新幹線・車）',
        'blog_wifi_ready' => '高速ネット対応',
        'blog_short_stay_ok' => '短期滞在',
        'blog_manage_support' => '分譲管理',
        'blog_silent_env' => '自然環境でリフレッシュ',
        'blog_easy_maintenance' => '建物・敷地の柔軟性',
        'blog_parking_ready' => '駐車スペース有り',
        'blog_family_use' => '家族での敷地と間取り',
        'blog_second_home_cost' => '初期費用・維持費の低減',
        // レジャー・観光系（新規追加）
        'blog_near_restaurant' => '人気レストランが近い',
        'blog_near_resorts' => 'リゾートホテルが多い',
        'blog_near_golf' => 'ゴルフ場が近い',
        'blog_near_camp' => 'キャンプ場が近い',
        'blog_near_outlet' => 'アウトレットモールが近い',
        'blog_near_animal_kingdom' => '那須どうぶつ王国近く',
        'blog_winter_sports' => 'スキー場・雪遊びも可能',
        'blog_near_onsen' => '日帰り温泉施設が充実',
        'blog_trekking_ok' => 'トレッキング・登山が楽しめる',
        'blog_child_friendly' => '子育て環境が整っている',

    ];


    echo '<table class="form-table">';
    foreach ($fields as $key => $label) {
        $value = esc_attr(get_post_meta($post->ID, $key, true));
        echo '<tr><th><label for="' . $key . '">' . $label . '</label></th>';
        echo '<td><input type="text" name="' . $key . '" value="' . $value . '" class="regular-text"></td></tr>';
    }
    echo '</table>';

    echo '<h4>画像（最大10枚まで）</h4>';
    for ($i = 1; $i <= 10; $i++) {
        $image_id = get_post_meta($post->ID, 'blog_image_' . $i, true);
        $image_url = $image_id ? wp_get_attachment_url($image_id) : '';
        echo '<div style="margin-bottom:10px;">';
        echo '<input type="hidden" name="blog_image_' . $i . '" id="blog_image_' . $i . '" value="' . esc_attr($image_id) . '">';
        echo '<img id="blog_image_preview_' . $i . '" src="' . esc_url($image_url) . '" style="max-width:150px;height:auto;"><br>';
        echo '<button type="button" class="button select-blog-image" data-target="' . $i . '">画像を選択</button> ';
        echo '<button type="button" class="button remove-blog-image" data-target="' . $i . '">削除</button>';
        echo '</div>';
    }

    // JS出力
    echo '<script>
    jQuery(document).ready(function($){
        $(".select-blog-image").click(function(e){
            e.preventDefault();
            var target = $(this).data("target");
            var frame = wp.media({ title: "画像を選択", multiple: false });
            frame.on("select", function(){
                var attachment = frame.state().get("selection").first().toJSON();
                $("#blog_image_" + target).val(attachment.id);
                $("#blog_image_preview_" + target).attr("src", attachment.url);
            });
            frame.open();
        });

        $(".remove-blog-image").click(function(e){
            e.preventDefault();
            var target = $(this).data("target");
            $("#blog_image_" + target).val("");
            $("#blog_image_preview_" + target).attr("src", "");
        });
    });
    </script>';
}

// 保存処理
function save_blog_lifestyle_meta_box($post_id)
{
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (get_post_type($post_id) != 'blog') return;

    $keys = [
        'blog_lifestyle_heading', // ← この行を先頭に追加！
        'blog_garden',
        'blog_all_electric',
        'blog_water_supply',
        'blog_rural_life',
        'blog_permanent_residence',
        'blog_near_school',
        'blog_near_hospital',
        'blog_dual_life',
        'blog_access_tokyo',
        'blog_wifi_ready',
        'blog_short_stay_ok',
        'blog_manage_support',
        'blog_silent_env',
        'blog_easy_maintenance',
        'blog_parking_ready',
        'blog_family_use',
        'blog_second_home_cost',
        // レジャー・観光系（新規追加）
        'blog_near_restaurant',
        'blog_near_resorts',
        'blog_near_golf',
        'blog_near_camp',
        'blog_near_outlet',
        'blog_near_animal_kingdom',
        'blog_winter_sports',
        'blog_near_onsen',
        'blog_trekking_ok',
        'blog_child_friendly',

    ];

    foreach ($keys as $key) {
        if (isset($_POST[$key])) {
            update_post_meta($post_id, $key, sanitize_text_field($_POST[$key]));
        }
    }

    for ($i = 1; $i <= 10; $i++) {
        if (isset($_POST['blog_image_' . $i])) {
            update_post_meta($post_id, 'blog_image_' . $i, intval($_POST['blog_image_' . $i]));
        }
    }
}

add_action('save_post', 'save_blog_lifestyle_meta_box');

function display_blog_lifestyle_slider()
{
    global $post;

    $image_urls = [];
    for ($i = 1; $i <= 10; $i++) {
        $image_id = get_post_meta($post->ID, 'blog_image_' . $i, true);
        if ($image_id) {
            $image_urls[] = wp_get_attachment_url($image_id);
        }
    }

    if ($image_urls) :
    ?>
        <!-- ✅ ID変更で既存スクリプトに完全対応 -->
        <div id="slider_3-container" class="swiper-container slider-wrapper-3">
            <h2 class="slider-title">暮らしのイメージ</h2>
            <div id="slide-number" class="slide-number">1 / <?php echo count($image_urls); ?></div>

            <div class="swiper-wrapper">
                <?php foreach ($image_urls as $index => $image_url): ?>
                    <div class="swiper-slide">
                        <img src="<?= esc_url($image_url); ?>" alt="ブログ画像 <?= $index + 1; ?>" class="slider-image" data-index="<?= $index; ?>">
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="swiper-button-next next"></div>
            <div class="swiper-button-prev prev"></div>
        </div>

        <div class="swiper-container thumbnail-swiper-container">
            <div class="swiper-wrapper">
                <?php foreach ($image_urls as $index => $image_url): ?>
                    <div class="swiper-slide">
                        <img src="<?= esc_url($image_url); ?>" alt="サムネイル <?= $index + 1; ?>" class="thumbnail thumbnail-3"
                            data-slider="3" data-index="<?= $index; ?>"
                            style="<?= $index === 0 ? 'border: 2px solid #0073e6;' : ''; ?>">
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php
    endif;
}




// single.php single-house.php に読み込んでいる。
function display_custom_fields_table_1()
{
    global $post;

    // Retrieve custom field values for both standard and "New" fields
    $property_name = get_post_meta($post->ID, 'PropertyName', true);
    $price = get_post_meta($post->ID, 'Price', true);
    $units_sold = get_post_meta($post->ID, 'UnitsSold', true);
    $total_units = get_post_meta($post->ID, 'TotalUnits', true);
    $layout = get_post_meta($post->ID, 'Layout', true);
    $building_area = get_post_meta($post->ID, 'BuildingArea', true);
    $land_area = get_post_meta($post->ID, 'LandArea', true);
    $location = get_post_meta($post->ID, 'Location', true);
    $transport = get_post_meta($post->ID, 'Transport', true);
    $ownership_type = get_post_meta($post->ID, 'OwnershipType', true);
    $land_category = get_post_meta($post->ID, 'LandCategory', true);
    $zoning = get_post_meta($post->ID, 'Zoning', true);
    $urban_planning = get_post_meta($post->ID, 'UrbanPlanning', true);
    $remarks = get_post_meta($post->ID, 'Remarks', true);
    $building_coverage_ratio = get_post_meta($post->ID, 'BuildingCoverageRatio', true);
    $floor_area_ratio = get_post_meta($post->ID, 'FloorAreaRatio', true);
    $units_sold_area = get_post_meta($post->ID, 'UnitsSoldArea', true);
    $total_units_sold_area = get_post_meta($post->ID, 'TotalUnitsSoldArea', true);
    $road_orientation = get_post_meta($post->ID, 'RoadOrientation', true); // 接道状況（道路の向き）
    $sold_out = get_post_meta($post->ID, 'sold-out', true); // 売却済みの状態を取得


    // Check if at least one custom field has a non-empty value
    $custom_fields = [
        $price,
        $units_sold,
        $total_units,
        $layout,
        $building_area,
        $land_area,
        $location,
        $transport,
        $ownership_type,
        $land_category,
        $zoning,
        $urban_planning,
        $remarks,
        $property_name,
        $building_coverage_ratio,
        $floor_area_ratio,
        $units_sold_area,
        $total_units_sold_area
    ];

    $has_custom_field_value = !empty(array_filter($custom_fields));

    // Display table if custom fields contain values
    if ($has_custom_field_value) {
    ?>
        <section class="property-details">
            <div class="property-details-info">
                <h2 class="property-title">物件概要</h2>
                <table class="property-table">
                    <tbody>
                        <tr>
                            <th>物件名</th>
                            <td><?php echo esc_html($property_name); ?></td>
                            <th>所在地</th>
                            <td>
                                <div>
                                    <?php echo esc_html($location); ?>
                                </div>
                                <div style="margin-top: 10px;">
                                    <span class="google-location-link">
                                        <svg class="icon-location" style="cursor:pointer; width: 20px; height: 20px;" id="iconLocation">
                                            <use xlink:href="#icon-location"></use>
                                        </svg>
                                        <a href="javascript:void(0);" id="openMapLink">Google位置</a>
                                    </span>
                                </div>

                                <?php
                                // Googleマップの埋め込みコードがある場合に地図を表示
                                $google_embed_code = get_post_meta($post->ID, 'GoogleEmbedcode', true);
                                if ($google_embed_code) {
                                    $iframe_content = strpos($google_embed_code, '<iframe') !== false
                                        ? $google_embed_code
                                        : '<iframe id="googleMapIframe" src="' . esc_url($google_embed_code) . '" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>';

                                    // モーダルのHTMLを修正
                                    echo '<div id="googleMapModal" class="google-map-modal" style="display: none;">
                                    <div class="google-map-modal-content">
                                        <span class="google-map-modal-close" id="closeModal">&times;</span>
                                        ' . $iframe_content . '
                                    </div>
                                </div>';
                                } else {
                                    echo '<p class="no-margin">Googleマップが設定されていません。</p>';
                                }
                                ?>
                            </td>
                        <tr>
                            <th>権利形態</th>
                            <td><?php echo esc_html($ownership_type); ?></td>
                            <th>価格</th>
                            <td>
                                <div class="cta-container">
                                    <?php
                                    // 価格を取得
                                    $price = get_post_meta($post->ID, 'Price', true);

                                    // 価格が設定されているか、0でない場合のみ価格を表示
                                    if (!empty($price) && (int)$price > 0) {
                                        echo esc_html(number_format((int)$price)) . '万円';
                                    } else {
                                        echo '<span class="sold-out">売却済</span>';
                                    }

                                    // 売却済みの場合はローンシミュレーションを表示しない
                                    if (!empty($price) && (int)$price > 0) {
                                        // URLをチェックして、特定のページでない場合にローンシミュレーションを表示
                                        $current_url = $_SERVER['REQUEST_URI'];
                                        if (strpos($current_url, 'naiga-tochi') === false && strpos($current_url, 'recommended-land') === false) {
                                            echo do_shortcode('[loan_simulation_modal price="' . esc_attr($price) . '"]');
                                        }
                                    }
                                    ?>
                                </div>
                            </td>
                        </tr>

                        <?php
                        $current_url = $_SERVER['REQUEST_URI'];
                        if (
                            (strpos($current_url, 'naigai-tochi') === false && strpos($current_url, 'recommended-land') === false) ||
                            strpos($current_url, 'recommended-land-and-house') !== false
                        ):
                        ?>
                            <tr>
                                <th>販売戸数</th>
                                <td><?php echo esc_html($units_sold); ?></td>
                                <th>総戸数</th>
                                <td><?php echo esc_html($total_units); ?></td>
                            </tr>
                            <tr>
                                <th>間取り</th>
                                <td><?php echo esc_html($layout); ?></td>
                                <th>建物面積</th>
                                <td>
                                    <?php
                                    // カテゴリーが 'naigai-tochi' または 'recommended-land' の場合は表示しない
                                    if (!has_term(array('naigai-tochi', 'recommended-land'), 'category') && $building_area) {
                                        // 「㎡」などの単位を取り除いて数値部分だけを取得
                                        $building_area_numeric = preg_replace('/[^0-9.]/', '', $building_area);

                                        // 数値部分をフォーマットして「㎡」を追加
                                        echo esc_html(number_format((float)$building_area_numeric)) . '㎡';
                                    } else {
                                        // 'naigai-tochi' または 'recommended-land' カテゴリーの場合、または建物面積情報が設定されていない場合
                                        echo '建物面積情報が設定されていません。';
                                    }
                                    ?>
                                </td>


                            </tr>
                        <?php endif; ?>

                        <!-- 土地面積と接道状況を隣り合わせにして表示 -->
                        <tr>
                            <th>土地面積</th>
                            <td>
                                <?php
                                // もし土地面積が設定されている場合
                                if ($land_area) {
                                    // 「㎡」などの単位を取り除いて数値部分だけを取得
                                    $land_area_numeric = preg_replace('/[^0-9.]/', '', $land_area);

                                    // 数値部分をフォーマットして「㎡」を追加
                                    echo esc_html(number_format((float)$land_area_numeric)) . '㎡';
                                } else {
                                    // 土地面積情報が設定されていない場合
                                    echo '土地面積情報が設定されていません。';
                                }
                                ?>
                            </td>
                            <th>接道状況</th>
                            <td>
                                <?php
                                // 接道状況（道路の向き）を取得
                                $road_orientation = get_post_meta($post->ID, 'RoadOrientation', true);

                                // 数値部分のみ取得（例えば「5m」といった場合、「5」の部分を抽出）
                                $road_orientation_numeric = preg_replace('/[^0-9.]/', '', $road_orientation);

                                // 数値が取得できていれば、その数値に「m」を追加して表示
                                if ($road_orientation_numeric !== '') {
                                    echo esc_html($road_orientation_numeric) . 'm'; // 「m」を追加
                                } else {
                                    echo '接道状況情報が設定されていません。'; // 情報がなければその旨を表示
                                }
                                ?>
                            </td>
                        </tr>

                        <!-- 建ぺい率/容積率を後に表示 -->
                        <tr>
                            <th>建ぺい率/容積率</th>
                            <td>
                                <?php
                                if ($building_coverage_ratio && $floor_area_ratio) {
                                    echo esc_html($building_coverage_ratio) . ' / ' . esc_html($floor_area_ratio);
                                } else {
                                    echo '-';
                                }
                                ?>
                            </td>
                            <th>地目</th>
                            <td><?php echo esc_html($land_category); ?></td>
                        </tr>

                        <tr>
                            <th>用途地域</th>
                            <td><?php echo esc_html($zoning); ?></td>
                            <th>都市計画</th>
                            <td><?php echo esc_html($urban_planning); ?></td>
                        </tr>
                        <tr>
                            <th>交通</th>
                            <td><?php echo esc_html($transport); ?></td>
                            <th>備考</th>
                            <td><?php echo esc_html($remarks); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <?php
            // 画像のURLを格納する配列 
            $image_urls = [];

            // 動画タイプを確認 
            $type = get_post_meta($post->ID, 'page_featured_type', true);
            $video_id = get_post_meta($post->ID, 'page_video_id', true); // 動画IDを取得 

            // 動画がメインの場合、サムネイル画像を取得して配列の先頭に追加
            if ($type === 'youtube' && !empty($video_id)) {
                // maxresdefault.jpg 高解像度　のサムネイルを取得　
                $video_thumbnail_url = 'https://img.youtube.com/vi/' . $video_id . '/maxresdefault.jpg';

                // maxresdefault.jpg　中解像度　が存在しない場合にhqdefault.jpgを使用
                if (!@getimagesize($video_thumbnail_url)) {
                    $video_thumbnail_url = 'https://i.ytimg.com/vi/' . $video_id . '/hqdefault.jpg';
                }

                // 取得したサムネイルURLを配列の先頭に追加　Vimeoのサムネイル画像のURLを直接取得
                array_unshift($image_urls, $video_thumbnail_url);
            } elseif ($type === 'vimeo' && !empty($video_id)) {
                // Vimeoの場合のサムネイル画像
                $video_thumbnail_url = 'https://vumbnail.com/' . $video_id . '.jpg';
                array_unshift($image_urls, $video_thumbnail_url);
            }

            // アイキャッチ画像を追加（動画がない場合は最優先）
            if (has_post_thumbnail()) {
                $thumbnail_url = get_the_post_thumbnail_url($post->ID, 'full'); // アイキャッチ画像のURLを取得 
                array_unshift($image_urls, $thumbnail_url);
            }

            // 画像1から10までのカスタムフィールド画像を追加
            for ($i = 1; $i <= 10; $i++) {
                $image_id = get_post_meta($post->ID, 'Image' . $i, true);
                if (!empty($image_id)) {
                    $image_urls[] = wp_get_attachment_url($image_id);
                }
            }

            // メイン画像のスライダー表示
            if (!empty($image_urls)) {
            ?>
                <div class="slider-container" data-slider="1">
                    <h2 class="slider-title">物件ギャラリー</h2>

                    <!-- メインスライダー -->
                    <div class="slider" id="slider_1">
                        <?php foreach ($image_urls as $index => $image_url) { ?>
                            <div class="slide" style="display: <?php echo $index === 0 ? 'block' : 'none'; ?>;">
                                <?php if ($index === 0 && $type === 'youtube' && !empty($video_id)) { ?>
                                    <lite-youtube videoid="<?php echo esc_attr($video_id); ?>" playlabel="Play: <?php echo esc_html(get_the_title()); ?>"></lite-youtube>
                                <?php } elseif ($index === 0 && $type === 'vimeo' && !empty($video_id)) { ?>
                                    <lite-vimeo videoid="<?php echo esc_attr($video_id); ?>"></lite-vimeo>
                                <?php } else { ?>
                                    <img src="<?php echo esc_url($image_url); ?>" alt="Property Image <?php echo $index + 1; ?>" class="slider-image" loading="lazy">
                                <?php } ?>
                            </div>
                        <?php } ?>
                    </div>

                    <!-- ナビゲーションボタン -->
                    <div class="slider-nav">
                        <button class="prev" data-slider="1">&#10094;</button>
                        <button class="next" data-slider="1">&#10095;</button>
                    </div>

                    <!-- スライド番号表示 -->
                    <div class="slide-number">
                        <span id="slide-count-1">1 / <?php echo count($image_urls); ?></span>
                    </div>

                    <!-- サムネイルスライダー -->
                    <div class="thumbnail-container">
                        <div class="thumbnail-slider" id="thumbnail-slider-1">
                            <?php foreach ($image_urls as $index => $image_url) { ?>
                                <?php if (!empty($image_url)) { // 画像URLが空でない場合のみ表示 
                                ?>
                                    <img src="<?php echo esc_url($image_url); ?>" alt="Thumbnail <?php echo $index + 1; ?>"
                                        class="thumbnail"
                                        data-slider="1"
                                        data-index="<?php echo $index; ?>"
                                        loading="lazy">
                                <?php } ?>
                            <?php } ?>
                        </div>
                    </div>
                </div>

            <?php
            }
            ?>

        </section>
    <?php
    }
}




// 管理画面の作成表示　テーブル2　投稿ページに読み込み → 投稿ページ　カスタム投稿ページ（house）に出力
function display_custom_fields_table_2()
{
    global $post;

    // Retrieve "New" custom field values with correct names
    $new_property_name = get_post_meta($post->ID, 'NewPropertyName', true);
    $new_price = get_post_meta($post->ID, 'NewPrice', true);
    $new_units_sold = get_post_meta($post->ID, 'NewUnitsSold', true);
    $new_total_units = get_post_meta($post->ID, 'NewTotalUnits', true);
    $new_layout = get_post_meta($post->ID, 'NewLayout', true);
    $new_building_area = get_post_meta($post->ID, 'NewBuildingArea', true);
    $new_land_area = get_post_meta($post->ID, 'NewLandArea', true);
    $new_location = get_post_meta($post->ID, 'NewLocation', true);
    $new_transport = get_post_meta($post->ID, 'NewTransport', true);
    $new_ownership_type = get_post_meta($post->ID, 'NewOwnershipType', true);
    $new_land_category = get_post_meta($post->ID, 'NewLandCategory', true);
    $new_zoning = get_post_meta($post->ID, 'NewZoning', true);
    $new_urban_planning = get_post_meta($post->ID, 'NewUrbanPlanning', true);
    $new_remarks = get_post_meta($post->ID, 'NewRemarks', true);
    $new_building_coverage_ratio = get_post_meta($post->ID, 'NewBuildingCoverageRatio', true);
    $new_floor_area_ratio = get_post_meta($post->ID, 'NewFloorAreaRatio', true);
    $new_units_sold_area = get_post_meta($post->ID, 'NewUnitsSoldArea', true);
    $new_total_units_sold_area = get_post_meta($post->ID, 'NewTotalUnitsSoldArea', true);
    $new_road_orientation = get_post_meta($post->ID, 'NewRoadOrientation', true); // 接道状況（道路の向き）
    $new_google_embed_code = get_post_meta($post->ID, 'NewGoogleEmbedcode', true);



    // Check if at least one custom field (including "New" fields) has a non-empty value
    $custom_fields = [
        $new_property_name,
        $new_price,
        $new_units_sold,
        $new_total_units,
        $new_layout,
        $new_building_area,
        $new_land_area,
        $new_location,
        $new_transport,
        $new_ownership_type,
        $new_land_category,
        $new_zoning,
        $new_urban_planning,
        $new_remarks,
        $new_building_coverage_ratio,
        $new_floor_area_ratio,
        $new_units_sold_area,
        $new_total_units_sold_area,
        $new_google_embed_code,
        $new_road_orientation
    ];

    // Check if any custom field has a non-empty value
    $has_custom_field_value = !empty(array_filter($custom_fields));

    // Display table if custom fields contain values
    if ($has_custom_field_value) {
    ?>
        <section class="property-details">
            <div class="property-details-info">
                <h2 class="property-title">物件概要</h2>
                <table class="property-table">
                    <tbody>
                        <tr>
                            <th>物件名</th>
                            <td><?php echo esc_html($new_property_name); ?></td>
                            <th>所在地</th>
                            <td>
                                <div>
                                    <?php echo esc_html($new_location); ?>
                                </div>
                                <div style="margin-top: 10px;">
                                    <span class="google-location-link">
                                        <svg class="icon icon-location" style="cursor:pointer; width: 20px; height: 20px;" id="iconLocation">
                                            <use xlink:href="#icon-location"></use>
                                        </svg>
                                        <a href="javascript:void(0);" id="openMapLink">Google位置</a>
                                    </span>
                                </div>

                                <?php
                                $google_embed_code = get_post_meta($post->ID, 'GoogleEmbedcode', true);
                                if ($google_embed_code) {
                                    $iframe_content = strpos($google_embed_code, '<iframe') !== false
                                        ? $google_embed_code
                                        : '<iframe id="googleMapIframe" src="' . esc_url($google_embed_code) . '" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>';
                                    echo '<div id="googleMapModal" class="google-map-modal" style="display: none;">
                                            <div class="google-map-modal-content">
                                                <span class="google-map-modal-close" id="closeModal">&times;</span>
                                                ' . $iframe_content . '
                                            </div>
                                        </div>';
                                } else {
                                    echo '<p class="no-margin">Googleマップが設定されていません。</p>';
                                }
                                ?>
                            </td>

                        </tr>
                        <tr>
                            <th>権利形態</th>
                            <td><?php echo esc_html($new_ownership_type); ?></td>
                            <th>価格</th>
                            <td>
                                <div class="cta-container">
                                    <?php
                                    // 価格を取得
                                    $price = get_post_meta($post->ID, 'NewPrice', true);

                                    // 価格が設定されているか、0でない場合のみ価格を表示
                                    if (!empty($new_price) && (int)$new_price > 0) {
                                        echo esc_html(number_format((int)$new_price)) . '万円';
                                    } else {
                                        echo '<span class="sold-out">売却済</span>';
                                    }

                                    // 売却済みの場合はローンシミュレーションを表示しない
                                    if (!empty($new_price) && (int)$new_price > 0) {
                                        // URLをチェックして、特定のページでない場合にローンシミュレーションを表示
                                        $current_url = $_SERVER['REQUEST_URI'];
                                        if (strpos($current_url, 'naiga-tochi') === false && strpos($current_url, 'recommended-land') === false) {
                                            echo do_shortcode('[loan_simulation_modal price="' . esc_attr($new_price) . '"]');
                                        }
                                    }
                                    ?>
                                </div>
                            </td>
                        </tr>

                        <?php
                        $current_url = $_SERVER['REQUEST_URI'];
                        if (
                            (strpos($current_url, 'naigai-tochi') === false && strpos($current_url, 'recommended-land') === false) ||
                            strpos($current_url, 'recommended-land-and-house') !== false
                        ):
                        ?>
                            <tr>
                                <th>販売戸数</th>
                                <td><?php echo esc_html($new_units_sold); ?></td>
                                <th>総戸数</th>
                                <td><?php echo esc_html($new_total_units); ?></td>
                            </tr>
                            <tr>
                                <th>間取り</th>
                                <td><?php echo esc_html($new_layout); ?></td>
                                <th>建物面積</th>
                                <td>
                                    <?php
                                    // カテゴリーが 'naigai-tochi' または 'recommended-land' の場合は表示しない
                                    if (!has_term(array('naigai-tochi', 'recommended-land'), 'category') && $new_building_area) {
                                        // 「㎡」などの単位を取り除いて数値部分だけを取得
                                        $building_area_numeric = preg_replace('/[^0-9.]/', '', $new_building_area);

                                        // 数値部分をフォーマットして「㎡」を追加
                                        echo esc_html(number_format((float)$building_area_numeric)) . '㎡';
                                    } else {
                                        // 'naigai-tochi' または 'recommended-land' カテゴリーの場合、または建物面積情報が設定されていない場合
                                        echo '建物面積情報が設定されていません。';
                                    }
                                    ?>
                                </td>

                            </tr>
                        <?php endif; ?>

                        <?php
                        $current_url = $_SERVER['REQUEST_URI'];
                        if (
                            (strpos($current_url, 'naigai-construction') === false &&
                                strpos($current_url, 'house') === false) ||
                            strpos($current_url, 'recommended-land-and-house') !== false
                        ):
                        ?>
                            <tr>
                                <th>販売区画数</th>
                                <td><?php echo esc_html($new_units_sold_area); ?></td>
                                <th>総販売区画数</th>
                                <td><?php echo esc_html($new_total_units_sold_area); ?></td>
                            </tr>
                        <?php endif; ?>

                        <tr>
                            <th>建ぺい率/容積率</th>
                            <td>
                                <?php
                                if ($new_building_coverage_ratio && $new_floor_area_ratio) {
                                    echo esc_html($new_building_coverage_ratio) . ' / ' . esc_html($new_floor_area_ratio);
                                } else {
                                    echo '-';
                                }
                                ?>
                            </td>
                            <th>地目</th>
                            <td><?php echo esc_html($new_land_category); ?></td>
                        </tr>
                        <tr>
                            <th>用途地域</th>
                            <td><?php echo esc_html($new_zoning); ?></td>
                            <th>都市計画</th>
                            <td><?php echo esc_html($new_urban_planning); ?></td>
                        </tr>
                        <tr>
                            <th>交通</th>
                            <td><?php echo esc_html($new_transport); ?></td>
                            <th>備考</th>
                            <td><?php echo esc_html($new_remarks); ?></td>
                        </tr>

                        <!-- 土地面積と接道状況を隣り合わせにして表示 -->
                        <tr>
                            <th>土地面積</th>
                            <td>
                                <?php
                                // もし土地面積が設定されている場合
                                if ($new_land_area) {
                                    // 「㎡」などの単位を取り除いて数値部分だけを取得
                                    $land_area_numeric = preg_replace('/[^0-9.]/', '', $new_land_area);

                                    // 数値部分をフォーマットして「㎡」を追加
                                    echo esc_html(number_format((float)$land_area_numeric)) . '㎡';
                                } else {
                                    // 土地面積情報が設定されていない場合
                                    echo '土地面積情報が設定されていません。';
                                }
                                ?>
                            </td>
                            <th>接道状況</th>
                            <td>
                                <?php
                                // 接道状況（道路の向き）を取得
                                $new_road_orientation = get_post_meta($post->ID, 'NewRoadOrientation', true);

                                // 接道状況が設定されていれば表示
                                if ($new_road_orientation) {
                                    echo esc_html($new_road_orientation);
                                } else {
                                    echo '接道状況情報が設定されていません。';
                                }
                                ?>
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>


            <?php
            // Retrieve property image URLs
            $image_urls = [];
            for ($i = 1; $i <= 10; $i++) {
                $image_id = get_post_meta($post->ID, 'NewImage' . $i, true);
                if (!empty($image_id)) {
                    $image_urls[] = wp_get_attachment_url($image_id);
                }
            }

            // Display slider if there are images
            if (!empty($image_urls)) {
            ?>
                <!-- slider-container 2 -->
                <div class="slider-container" data-slider="2">
                    <h2 class="slider-title">物件ギャラリー</h2>

                    <!-- メインスライダー -->
                    <div class="slider" id="slider_2">
                        <?php foreach ($image_urls as $index => $image_url) { ?>
                            <div class="slide" style="display: <?php echo $index === 0 ? 'block' : 'none'; ?>;">
                                <img src="<?php echo esc_url($image_url); ?>" alt="Property Image <?php echo $index + 1; ?>" style="width: 100%; height: auto;">
                            </div>
                        <?php } ?>
                    </div>

                    <!-- slider_2 用のナビゲーションボタン -->
                    <div class="slider-nav">
                        <button class="prev" data-slider="2">&#10094;</button>
                        <button class="next" data-slider="2">&#10095;</button>
                    </div>

                    <!-- slider_2 のスライド番号表示 -->
                    <div class="slide-number">
                        <span id="slide-count-2">1 / <?php echo count($image_urls); ?></span>
                    </div>

                    <!-- サムネイル -->
                    <div class="thumbnail-container">
                        <div class="thumbnail-slider" id="thumbnail-slider-2">
                            <?php foreach ($image_urls as $index => $image_url) { ?>
                                <img src="<?php echo esc_url($image_url); ?>" alt="Thumbnail <?php echo $index + 1; ?>"
                                    class="thumbnail"
                                    data-slider="2"
                                    data-index="<?php echo $index; ?>"
                                    style="width: 100px; height: auto; cursor: pointer; <?php echo $index === 0 ? 'border: 2px solid #000;' : ''; ?>">
                            <?php } ?>
                        </div>
                    </div>
                </div>
            <?php
            }
            ?>
        </section>
    <?php
    }
}

// 管理画面の作成表示　テーブル3 →　投稿ページに読みこみ
function display_custom_fields_table_3()
{
    global $post;

    // エリア詳細のカスタムフィールド名
    $area_details_fields = [];
    for ($i = 1; $i <= 15; $i++) {
        $area_details_fields[] = 'AreaDetail' . $i;
    }

    // 画像詳細のカスタムフィールド名
    $image_details_fields = [];
    for ($i = 1; $i <= 15; $i++) {
        $image_details_fields[] = 'AreaImageDetail' . $i;
    }

    // エリア詳細の値を取得
    $area_details = array_map(function ($field) use ($post) {
        return get_post_meta($post->ID, $field, true);
    }, $area_details_fields);

    // 画像詳細の値を取得
    $image_details = array_map(function ($field) use ($post) {
        return get_post_meta($post->ID, $field, true);
    }, $image_details_fields);

    // 画像関連のフィールド（NewAreaImage）の取得
    $image_urls = [];
    for ($i = 1; $i <= 15; $i++) {
        $image_id = get_post_meta($post->ID, 'AreaImageDetail' . $i, true);
        if ($image_id) {
            $image_urls[] = wp_get_attachment_url($image_id);
        }
    }

    // 画像が1つでもあればスライダーを表示
    if ($image_urls) {
    ?>
        <div id="slider_3-container" class="swiper-container slider-wrapper-3">
            <h2 class="slider-title">近隣ギャラリー</h2>

            <!-- スライド番号を表示するための要素 -->
            <div id="slide-number" class="slide-number">1 / <?php echo count($image_urls); ?></div>

            <!-- メインスライダー -->
            <div class="swiper-wrapper">
                <?php foreach ($image_urls as $index => $image_url): ?>
                    <div class="swiper-slide">
                        <img src="<?= esc_url($image_url); ?>" alt="Property Image <?= $index + 1; ?>" class="slider-image" data-index="<?= $index; ?>">
                        <!-- 画像詳細を追加 -->
                        <?php if (!empty($image_details[$index])): ?>
                            <div class="image-detail" id="image-detail-<?= $index; ?>" style="display:none;">
                                <p><?= esc_html($image_details[$index]); ?></p>
                            </div>
                        <?php endif; ?>
                        <!-- エリア詳細を追加 -->
                        <?php if (!empty($area_details[$index])): ?>
                            <div class="area-detail" id="area-detail-<?= $index; ?>">
                                <p><?= esc_html($area_details[$index]); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- スライドナビゲーションボタン -->
            <div class="swiper-button-next next"></div>
            <div class="swiper-button-prev prev"></div>
        </div>

        <!-- サムネイルスライダー -->
        <div class="swiper-container thumbnail-swiper-container">
            <div class="swiper-wrapper">
                <?php foreach ($image_urls as $index => $image_url): ?>
                    <div class="swiper-slide">
                        <img src="<?= esc_url($image_url); ?>" alt="Thumbnail <?= $index + 1; ?>" class="thumbnail thumbnail-3" data-slider="3" data-index="<?= $index; ?>"
                            style="<?= $index === 0 ? 'border: 2px solid #0073e6;' : ''; ?>">
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

    <?php
    }
}


// カスタムフィールドのメタボックスを追加する関数
function add_custom_fields_meta_boxes()
{
    add_meta_box(
        'tables_meta_box', // メタボックスの識別子
        'テーブル選択', // 投稿編集画面に表示されるメタボックスのタイトル
        'tables_meta_box_callback', // テーブル選択するコールバック関数
        array('post', 'house'),  // 'post' と 'house' の両方のカスタム投稿タイプに追加
        'normal', // メタボックスの位置
        'high' // メタボックスの優先度
    );
}
add_action('add_meta_boxes', 'add_custom_fields_meta_boxes');


// テーブル1 編集フォーム1のフィールドのコールバック関数
function table1_meta_box_callback($post)
{
    echo '<h3>テーブル1のフィールド</h3>';
    display_fields_table($post, 1);
}

// テーブル2 編集フォーム2のフィールドのコールバック関数
function table2_meta_box_callback($post)
{
    echo '<h3>テーブル2のフィールド</h3>';
    display_fields_table($post, 2);
}

// テーブル3 編集フォーム3のフィールドのコールバック関数
function table3_meta_box_callback($post)
{
    echo '<h3>テーブル3のフィールド</h3>';
    display_fields_table($post, 3);
}

// テーブル選択するコールバック関数
function tables_meta_box_callback($post)
{
    echo '<div class="tab-container">';
    echo '<div class="nav-tab-wrapper">';
    echo '<a href="#" id="table1-link" class="nav-tab nav-tab-active">テーブル1</a>'; // テーブル1のタブ
    echo '<a href="#" id="table2-link" class="nav-tab">テーブル2</a>'; // テーブル2のタブ
    echo '<a href="#" id="table3-link" class="nav-tab">テーブル3</a>'; // テーブル3のタブ
    echo '</div>'; // .nav-tab-wrapperの終了

    echo '<div id="tab-content">';
    table1_meta_box_callback($post); // 初期表示はテーブル1の内容
    echo '</div>';
    echo '</div>'; // .tab-containerの終了

    // スクリプトを追加して、タブクリック時の処理を設定する
    echo '<script type="text/javascript">
        jQuery(document).ready(function($) {
            $(".nav-tab").on("click", function(e) {
                e.preventDefault();
                var tabId = $(this).attr("id");
                $(".nav-tab").removeClass("nav-tab-active");
                $(this).addClass("nav-tab-active");

                var newUrl = window.location.href.split(\'?\')[0] + \'?tab=\' + tabId;
                window.history.pushState({}, \'\', newUrl);

                var data = {
                    action: "get_tab_content",
                    tab: tabId,
                    post_id: ' . $post->ID . '
                };

                $.post(ajaxurl, data, function(response) {
                    $("#tab-content").html(response);
                });
            });
        });
    </script>';
}

// Ajaxリクエストを処理する関数
function get_tab_content_callback()
{
    $current_tab = isset($_POST['tab']) ? sanitize_text_field($_POST['tab']) : 'table1-link';
    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;

    if (!$post_id) {
        wp_die();
    }

    $post = get_post($post_id);

    switch ($current_tab) {
        case 'table1-link':
            table1_meta_box_callback($post);
            break;
        case 'table2-link':
            table2_meta_box_callback($post);
            break;
        case 'table3-link':
            table3_meta_box_callback($post);
            break;
        default:
            break;
    }

    wp_die();
}

// Ajaxアクションを追加
add_action('wp_ajax_get_tab_content', 'get_tab_content_callback');

// 保存処理：10個の画像IDとテキストを保存
function save_custom_table_fields($post_id)
{
    // 自動保存処理中はスキップ
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

    // 投稿タイプチェック（'post' または 'house'）
    $post_type = get_post_type($post_id);
    if (!in_array($post_type, ['post', 'house'])) return;

    // AreaDetail1〜10 と AreaImageDetail1〜10 を保存
    for ($i = 1; $i <= 15; $i++) {
        if (isset($_POST['AreaDetail' . $i])) {
            update_post_meta($post_id, 'AreaDetail' . $i, sanitize_text_field($_POST['AreaDetail' . $i]));
        }

        if (isset($_POST['AreaImageDetail' . $i])) {
            // 画像IDなので整数として保存
            update_post_meta($post_id, 'AreaImageDetail' . $i, intval($_POST['AreaImageDetail' . $i]));
        }
    }
}
add_action('save_post', 'save_custom_table_fields');








// lite-youtubeとVimeoの投稿画面に追加するためのメタボックスを追加します。
function add_media_selection_meta_box()
{
    // メタボックスの追加
    add_meta_box(
        'media_selection_meta_box', // メタボックスのID
        '動画フォーマットID入力', // メタボックスのタイトル
        'render_media_selection_meta_box', // メタボックスの表示関数
        array('post', 'page', 'house'), // メタボックスを表示する投稿タイプ
        'normal', // メタボックスの位置
        'default' // メタボックスの優先度
    );
}

// メディア選択メタボックスの表示をレンダリングします。
function render_media_selection_meta_box($post)
{
    // 現在の選択されているメディアの値を取得
    $selectedMedia = get_post_meta($post->ID, 'media_selection', true);

    // メディアタイプの選択肢を表示
    echo '<label for="media_selection">Select Media Type:</label>';
    echo '<select id="media_selection" name="media_selection">';
    echo '<option value="youtube" ' . selected($selectedMedia, 'youtube', false) . '>YouTube</option>';
    echo '<option value="vimeo" ' . selected($selectedMedia, 'vimeo', false) . '>Vimeo</option>';
    echo '</select>';

    // メディアIDの入力フィールドを表示
    echo '<br><br>';
    echo '<label for="media_id">Enter Media ID:</label>';
    echo '<input type="text" id="media_id" name="media_id" value="' . esc_attr(get_post_meta($post->ID, 'media_id', true)) . '" />';

    // メタボックスに nonce を追加
    wp_nonce_field('save_media_selection', 'media_selection_nonce');
}

// メディア選択メタボックスの内容を保存します。
function save_media_selection_meta_box($post_id)
{
    // nonce の検証
    if (!isset($_POST['media_selection_nonce']) || !wp_verify_nonce($_POST['media_selection_nonce'], 'save_media_selection')) {
        return;
    }

    // 自動保存中は処理を終了
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // ユーザーの権限を確認
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // メディア選択とメディアIDを保存
    $selectedMedia = isset($_POST['media_selection']) ? sanitize_text_field($_POST['media_selection']) : '';
    $mediaId = isset($_POST['media_id']) ? sanitize_text_field($_POST['media_id']) : '';

    update_post_meta($post_id, 'media_selection', $selectedMedia);
    update_post_meta($post_id, 'media_id', $mediaId);
}

// 投稿コンテンツに埋め込まれたメディアを表示します。
function display_embedded_media($content)
{
    // 投稿に関連付けられたメディア情報を取得
    $selectedMedia = get_post_meta(get_the_ID(), 'media_selection', true);
    $mediaId = get_post_meta(get_the_ID(), 'media_id', true);

    // メディアが設定されていれば埋め込みコードを追加
    if (!empty($selectedMedia) && !empty($mediaId)) {
        switch ($selectedMedia) {
            case 'youtube':
                // YouTubeの埋め込みコードを追加
                $content .= '<lite-youtube videoid="' . esc_attr($mediaId) . '" playlabel="Play Video" style="max-width:100%; height:auto;"></lite-youtube>';
                break;

            case 'vimeo':
                // Vimeoの埋め込みコードを追加
                $content .= '<lite-vimeo videoid="' . esc_attr($mediaId) . '">
                    <div class="ltv-playbtn"></div>
                    </lite-vimeo>';
                break;
        }
    }

    return $content;
}

// メタボックスを追加するアクションフックを登録します。
add_action('add_meta_boxes', 'add_media_selection_meta_box');
// メタボックスの内容を保存するアクションフックを登録します。
add_action('save_post', 'save_media_selection_meta_box');
// コンテンツフィルターに埋め込みメディアを表示するフィルターフックを登録します。
add_filter('the_content', 'display_embedded_media');




// side-menu widget widget コードの中に、side-menuサイドバーナビゲーション
//目的 該当するスラッグに対してwidgetのコンテンツを表示させている。

function my_custom_menu()
{
    global $wp; // WordPress グローバル変数を利用可能にする

    // 現在のページのURLを取得
    $current_url = home_url(add_query_arg(array(), $wp->request));

    // URLを表示の確認/tekken7にしないといけない（追加した行）
    //echo $current_url;

    // URLに "genshin-impact" が含まれている場合
    if (strpos($current_url, '/naigai-tochi') !== false) {
        $page_slug = 'naigai-tochi';
    }
    // URLに "tekken7" が含まれている場合
    elseif (strpos($current_url, 'naigai-construction') !== false) {
        $page_slug = 'naigai-construction';
    }
    // 上記の条件に当てはまらない場合はデフォルトのスラッグを設定
    else {
        $page_slug = '';
    }

    // カテゴリーに基づいて最新の投稿を取得するためのクエリを準備
    $posts_args = array();

    // カテゴリーに対応する最新の投稿を取得するクエリを設定
    if (!empty($page_slug)) {
        $posts_args = array(
            'category_name' => $page_slug,
            'posts_per_page' => 5
        );
    } else {
        // カテゴリーが設定されていない場合は、全ての投稿を取得
        $posts_args = array('posts_per_page' => 5);
    }

    // 最新の投稿を取得
    $posts = new WP_Query($posts_args);

    // サイドメニューを表示
    wp_nav_menu(
        array(
            'theme_location' => 'side-menu',
            'menu' => 'side-menu',
            'menu_id' => 'header-navigation',
            'container' => 'div',
            'container_id' => '',
            'menu_class' => '',
            'echo' => true,
            'before' => '',
            'after' => '',
            'link_before' => '',
            'link_after' => '',
            'items_wrap' => '<ul class="%2$s sidebar-dropdown-menu">%3$s</ul>',
            'depth' => 3,
        )
    );

    // モバイル検索フォームを表示
    echo '<div class="mobile-search" style="padding-left: 60px">';
    include(get_template_directory() . '/searchform30.php');
    echo '</div>';

    // カスタムウィジェットを表示
    the_widget(
        'Latest_Posts_Widget',
        array(
            'title' => 'Latest Posts',
            'posts' => $posts, // 最新の投稿を渡す
        )
    );
}

// functions.phpに追加するカスタムウィジェットの登録
function register_latest_posts_widget()
{
    register_widget('Latest_Posts_Widget');
}
add_action('widgets_init', 'register_latest_posts_widget');



// Latest_Posts_Widgetクラスの定義
class Latest_Posts_Widget extends WP_Widget
{

    // コンストラクタ
    function __construct()
    {
        parent::__construct(
            'latest_posts_widget',
            __('Latest Posts Widget', 'text_domain'), // ウィジェットの名前
            array('description' => __('Display latest posts with thumbnails', 'text_domain')) // ウィジェットの説明
        );
    }


    // ウィジェットの表示
    public function widget($args, $instance)
    {
        echo $args['before_widget'];
        echo $args['before_title'] . '<div class="widgettitle custom-widget-title side-title">' . apply_filters('widget_title', $instance['title']) . '</div>' . $args['after_title'];

        $posts = $instance['posts']; // 最新の投稿を取得

        if ($posts->have_posts()) {
            echo '<div class="custom-widget-container">';

            $count = 0;

            while ($posts->have_posts()) {
                $posts->the_post();
                echo '<div class="custom-widget-item" style="width: 50%; float: left; height: 200px;">';

                if (has_post_thumbnail()) {
                    echo '<a href="' . get_permalink() . '" title="' . get_the_title() . '">';
                    echo '<img width="150" height="100" src="' . get_the_post_thumbnail_url(get_the_ID(), 'home-thumb') . '" class="attachment-home-thumb size-home-thumb wp-post-image" alt="" decoding="async" loading="lazy">';
                    echo '</a>';
                }

                echo '<a href="' . get_permalink() . '" title="' . get_the_title() . '">' . get_the_title() . '</a>';
                echo '</div>';

                $count++;

                if ($count % 2 === 0) {
                    echo '<div style="clear: both;"></div>';
                }
            }

            echo '</div>';
        } else {
            echo '<p>No posts found</p>';
        }

        wp_reset_postdata();

        echo $args['after_widget'];
    }

    // ウィジェットの設定フォーム
    public function form($instance)
    {
        $title = !empty($instance['title']) ? $instance['title'] : __('Latest Posts', 'text_domain');
        $number_of_posts = !empty($instance['number_of_posts']) ? $instance['number_of_posts'] : 5;
    ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>"
                name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('number_of_posts'); ?>"><?php _e('Number of posts:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('number_of_posts'); ?>"
                name="<?php echo $this->get_field_name('number_of_posts'); ?>" type="number"
                value="<?php echo esc_attr($number_of_posts); ?>" min="1" max="10">
        </p>
    <?php
    }

    // ウィジェットの設定更新
    public function update($new_instance, $old_instance)
    {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['number_of_posts'] = (!empty($new_instance['number_of_posts'])) ? strip_tags($new_instance['number_of_posts']) : '';
        return $instance;
    }
}



// theme_page_templatesフィルターにカスタムテンプレートディレクトリを追加する関数custom_template_directoryをフックする
add_filter('theme_page_templates', 'custom_template_directory');

// カスタムテンプレートディレクトリを取得して、WordPressのテンプレート一覧に追加する関数
function custom_template_directory($templates)
{
    // テーマディレクトリのパスを取得
    $theme_dir = get_template_directory();

    // カスタムテンプレートのディレクトリリスト
    $custom_template_dirs = array(
        $theme_dir . '/templates/character/genshin',
        $theme_dir . '/templates/character/tekken',
        $theme_dir . '/templates/',
    );

    // 各ディレクトリ内のファイルを取得し、テンプレート一覧に追加
    foreach ($custom_template_dirs as $custom_template_dir) {
        // ディレクトリ内のファイルを取得
        $template_files = scandir($custom_template_dir);

        // 各ファイルをループして、PHPファイルのみをテンプレート一覧に追加
        foreach ($template_files as $file) {
            // ファイルがPHPファイルかどうかを確認
            if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                // テンプレートの配列に追加
                $templates[str_replace($theme_dir . '/', '', $custom_template_dir) . '/' . $file] = $file;
            }
        }
    }

    // テンプレート一覧を返す
    return $templates;
}


// Full Carendarに投稿タイトルのデータを表示　そのために　 WordPressのREST API 必要　wp query でデータを取得できなかったから

// WordPressのREST APIを使用してカスタムエンドポイントを作成するものです スクリプトに保存してある。
// カスタムエンドポイントを作成
add_action('rest_api_init', function () {
    register_rest_route('custom/v1', '/events', array(
        'methods' => 'GET',
        'callback' => 'get_post_events', // コールバック関数の指定
        'permission_callback' => '__return_true' // パーミッションコールバックの指定
    ));
});




// WOSPRESS RESET API コントロール リクエストされた投稿のレスポンスをカスタマイズする
//function customize_post_rest_response($data, $post, $request) {
// リンク情報を削除してタイトルと詳細情報のみを残す
//$data->data['link'] = null; // リンクを削除
//$data->data['title'] = $data->data['title']['rendered']; // タイトルのみを残す
//$data->data['content'] = $data->data['content']['rendered']; // 詳細情報のみを残す

//return $data;
//}
//add_filter('rest_prepare_post', 'customize_post_rest_response', 10, 3);



// functions.php もしくはカスタムプラグイン内で以下のコードを追加します。
//JavaScriptのAjaxリクエストで指定したactionパラメーターの値は、WordPressのadd_action関数で定義されたAjaxアクションの名前と一致する必要
//このコードは、Ajaxリクエストがget_post_titlesというアクション名で送信された場合に、custom_ajax_get_post_titlesという関数が実行されるように設定しています。wp_ajax_get_post_titlesは、管理画面からのAjaxリクエストを処理し、wp_ajax_nopriv_get_post_titlesは、非ログインユーザーからのAjaxリクエストを処理します。custom_ajax_get_post_titles関数は、WordPressのWP_Queryを使用して投稿を取得し、取得したデータをJSON形式で出力しています

//したがって、JavaScriptのAjaxリクエストでaction: 'get_post_titles'と指定されている場合、WordPressはそれをwp_ajax_get_post_titlesまたはwp_ajax_nopriv_get_post_titlesのいずれかのフックと関連付け、それに対応する関数であるcustom_ajax_get_post_titlesを呼び出します。

add_image_size('small', 150, 150, true); // 幅150px、高さ150pxのサムネイルサイズを追加

// Ajaxアクションを定義
add_action('wp_ajax_get_post_titles', 'custom_ajax_get_post_titles');
add_action('wp_ajax_nopriv_get_post_titles', 'custom_ajax_get_post_titles');

function custom_ajax_get_post_titles()
{
    $args = array(
        "posts_per_page" => -1,
        'post_type'      => array('post', 'house'), // 投稿タイプを指定
        'post_status'    => 'publish' // 公開された投稿のみを取得する
    );

    $the_query = new WP_Query($args);

    $cdContents = array();

    if ($the_query->have_posts()) {
        while ($the_query->have_posts()) {
            $the_query->the_post();
            $ID = get_the_ID();
            // 記事の抜粋を取得し、20文字に制限する
            $excerpt = wp_trim_words(get_the_excerpt(), 20, '...');
            // 記事の内容を取得し、20文字に制限する
            $content = wp_trim_words(get_the_content(), 20, '...');

            // タイトルからキーワードを削除
            $title = get_the_title(); // HTMLエンコードされた文字列をデコード
            $title = htmlspecialchars_decode($title); // タイトルのHTMLエンコードをデコード
            $title = removeKeywordsFromTitle($title); // キーワードを削除

            $cdContents[] = array(
                "pamalink" => get_permalink(),
                "title"    => $title,
                "start"    => get_the_date("Y-m-d"),
                "allDay"   => true,
                "eyecatch" => get_the_post_thumbnail_url($ID, 'small'), // 追加したサイズの識別子を指定
                "eyecatch_alt" => get_post_meta($ID, '_wp_attachment_image_alt', true), // アイキャッチ画像のaltテキスト
                "excerpt"  => $excerpt, // 記事の抜粋を取得
                "content"  => $content, // 記事の内容を取得
            );
        }
    }

    wp_reset_postdata();

    // JSON形式でデータを出力
    header('Content-Type: application/json');
    echo json_encode($cdContents);
    // WordPressの処理を終了
    wp_die();
}

// キーワードを削除する関数
function removeKeywordsFromTitle($title)
{
    $keywords = array('Genshin Impact', 'Genshin-Impact', 'Tekken7');
    $trimmedTitle = $title;

    foreach ($keywords as $keyword) {
        $regex = '/\b' . preg_quote($keyword, '/') . '\b/i'; // キーワードを正規表現のパターンに変換
        $trimmedTitle = preg_replace($regex, '', $trimmedTitle); // タイトルからキーワードを削除
    }

    return trim($trimmedTitle);
}


// custom-nonce　を　customAjax.nonce として共通している。　
function custom_enqueue_scripts()
{
    wp_enqueue_script(
        'custom-ajax',
        get_template_directory_uri() . '/js/scripts.js',
        array('jquery'),
        '1.0',
        true
    );

    wp_localize_script('custom-ajax', 'customAjax', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        // 予約フォーム用に統一
        'nonce'   => wp_create_nonce('store_reservation_action'),
    ));
}
add_action('wp_enqueue_scripts', 'custom_enqueue_scripts');






// フッターメニューを登録する関数（統合版）
function register_footer_menus()
{
    register_nav_menus(
        array(
            'footer_menu'      => 'Footer Menu (資料請求、来店予約、査定売却)',  // フッターメニューの位置名と表示名
            'post_footer_menu' => '投稿専用フッターメニュー'  // 投稿専用のフッターメニュー
        )
    );
}
add_action('after_setup_theme', 'register_footer_menus');

// ヘッダーメニューを登録する関数
function register_header_menus()
{
    register_nav_menus(
        array(
            'header-menu-pc' => __('PC Header Menu'), // パソコン用ヘッダーメニュー
            'header-menu-mobile' => __('Mobile Header Menu'), // モバイル用ヘッダーメニュー
        )
    );
}
add_action('init', 'register_header_menus');





/*住宅ローンシュミレーション*/
// モーダルのHTMLを含めたショートコード
// loan-simulation.jsとloan-simulation.cssを読み込み
function enqueue_loan_simulation_assets()
{
    // スタイルシートの登録
    wp_enqueue_style(
        'loan-simulation-css',
        get_template_directory_uri() . '/css/loan-simulation.css'
    );

    // スクリプトの登録
    wp_enqueue_script(
        'loan-simulation-js',
        get_template_directory_uri() . '/js/loan-simulation.js',
        array('jquery'), // jQueryに依存
        null,
        true // フッターで読み込む
    );
}
add_action('wp_enqueue_scripts', 'enqueue_loan_simulation_assets');

// 元利均等、元金均等を選択するショートコード
function loan_simulation_modal_shortcode($atts)
{
    // ショートコードに渡された引数を処理
    $atts = shortcode_atts(array(
        'price' => 0, // デフォルトの価格
    ), $atts);

    $price = $atts['price'];
    if (empty($price)) {
        $price = 0;
    }

    $uniq_id = 'modalLoan_' . uniqid();

    ob_start();
    ?>
    <!-- ローンシミュレーションボタン -->
    <button id="openModalLoan_<?php echo $uniq_id; ?>" class="loan-cta-button" data-loan-id="<?php echo $uniq_id; ?>" data-simulation-url="simulation_page_url">ローンシミュレーション</button>

    <!-- モーダルウィンドウ -->
    <div id="modalLoanUnique_<?php echo $uniq_id; ?>" class="modal-loan" style="display: none;">
        <div class="modal-loan-content">
            <span id="closeModalLoanUnique_<?php echo $uniq_id; ?>" class="close-modal">×</span>

            <div class="modal-loan-panel">
                <h2 class="modal-loan-headline">支払いシミュレーション</h2>

                <div id="simulation" class="modal-loan-sectionInner">
                    <div class="calculation">
                        <div class="clearfix">
                            <!-- 返済方法選択 -->
                            <dl>
                                <dt>返済方法</dt>
                                <dd>
                                    <input type="radio" id="equalPrincipal_<?php echo $uniq_id; ?>" name="loanMethod_<?php echo $uniq_id; ?>" value="equalPrincipal"> <label for="equalPrincipal_<?php echo $uniq_id; ?>">元金均等</label>
                                    <input type="radio" id="equalPayment_<?php echo $uniq_id; ?>" name="loanMethod_<?php echo $uniq_id; ?>" value="equalPayment"> <label for="equalPayment_<?php echo $uniq_id; ?>">元利均等</label>
                                </dd>
                            </dl>

                            <!-- 返済期間 -->
                            <dl>
                                <dt>返済期間</dt>
                                <dd>
                                    <button class="minus" data-target="#period_<?php echo $uniq_id; ?>">-</button>
                                    <input id="period_<?php echo $uniq_id; ?>" class="loan" type="text" value="35" data-unit="年">
                                    <button class="plus" data-target="#period_<?php echo $uniq_id; ?>">+</button>
                                </dd>
                            </dl>
                            <!-- 金利 -->
                            <dl>
                                <dt>金利</dt>
                                <dd>
                                    <button class="minus" data-target="#rate_<?php echo $uniq_id; ?>">-</button>
                                    <input id="rate_<?php echo $uniq_id; ?>" class="loan" type="text" value="0.3" data-unit="%">
                                    <button class="plus" data-target="#rate_<?php echo $uniq_id; ?>">+</button>
                                </dd>
                            </dl>
                            <!-- 借入金額 -->
                            <dl>
                                <dt>借入金額</dt>
                                <dd>
                                    <button class="minus" data-target="#borrow_<?php echo $uniq_id; ?>">-</button>
                                    <input id="borrow_<?php echo $uniq_id; ?>" class="loan" type="text" value="<?php echo esc_html(number_format((int)$price)); ?>" data-price="<?php echo esc_attr($price); ?>" data-unit="万円">
                                    <button class="plus" data-target="#borrow_<?php echo $uniq_id; ?>">+</button>
                                </dd>
                            </dl>
                            <!-- 頭金 -->
                            <dl>
                                <dt>頭金</dt>
                                <dd>
                                    <button class="minus" data-target="#downPayment_<?php echo $uniq_id; ?>">-</button>
                                    <input id="downPayment_<?php echo $uniq_id; ?>" class="loan" type="text" value="0" data-unit="万円">
                                    <button class="plus" data-target="#downPayment_<?php echo $uniq_id; ?>">+</button>
                                </dd>
                            </dl>
                            <!-- ボーナス額 -->
                            <dl>
                                <dt>ボーナス額</dt>
                                <dd>
                                    <button class="minus" data-target="#bonusAmount_<?php echo $uniq_id; ?>">-</button>
                                    <input id="bonusAmount_<?php echo $uniq_id; ?>" class="loan" type="text" value="0" data-unit="万円">
                                    <button class="plus" data-target="#bonusAmount_<?php echo $uniq_id; ?>">+</button>
                                </dd>
                            </dl>
                        </div>



                        <!-- 計算結果表示 -->
                        <dl id="answer">
                            <dt>毎月の返済額</dt>
                            <dd><input id="monthPay_<?php echo $uniq_id; ?>" type="text" disabled="disabled" value="" data-unit="万円"></dd>
                            <!--<dt>ボーナス込みの毎月の返済額</dt>
                            <dd><input id="monthPayWithBonus_<?php echo $uniq_id; ?>" type="text" disabled="disabled" value="" data-unit="万円"></dd>-->
                        </dl>

                    </div>
                </div>
            </div>
        </div>
    </div>

<?php

    return ob_get_clean();
}

// ショートコード登録
add_shortcode('loan_simulation_modal', 'loan_simulation_modal_shortcode');


// Google Map Iframe メタボックスの追加
function add_google_map_metabox()
{
    // 通常投稿（post）とカスタム投稿タイプ（house）にメタボックスを追加
    $post_types = array('post', 'house'); // カスタム投稿タイプ名を 'house' に変更

    foreach ($post_types as $post_type) {
        add_meta_box(
            'google_map_metabox', // メタボックスID
            'Google Map 埋め込み', // メタボックスのタイトル
            'render_google_map_metabox', // メタボックスの表示処理
            $post_type, // 投稿タイプ（通常投稿とカスタム投稿）
            'normal', // メタボックスの位置
            'high' // 優先度
        );
    }
}
add_action('add_meta_boxes', 'add_google_map_metabox');

// メタボックスの表示処理
function render_google_map_metabox($post)
{
    // 保存された iframe の値を取得
    $google_map_iframe_1 = get_post_meta($post->ID, '_google_map_iframe_1', true);
    $google_map_iframe_2 = get_post_meta($post->ID, '_google_map_iframe_2', true);

    // nonce を表示してセキュリティを強化
    wp_nonce_field('google_map_nonce_action', 'google_map_nonce');

    // 入力フォームを表示
    echo '<label for="google_map_iframe_1">Google Map 1</label><br>';
    echo '<textarea id="google_map_iframe_1" name="google_map_iframe_1" rows="5" cols="50">' . esc_textarea($google_map_iframe_1) . '</textarea><br>';
    echo '<label for="google_map_iframe_2">Google Map 2</label><br>';
    echo '<textarea id="google_map_iframe_2" name="google_map_iframe_2" rows="5" cols="50">' . esc_textarea($google_map_iframe_2) . '</textarea>';
}

// メタボックスデータの保存
function save_google_map_metabox_data($post_id)
{
    // 自動保存時のチェック
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return $post_id;

    // nonce の確認
    if (!isset($_POST['google_map_nonce']) || !wp_verify_nonce($_POST['google_map_nonce'], 'google_map_nonce_action')) {
        return $post_id;
    }

    // 投稿タイプを確認（通常投稿とカスタム投稿タイプ）
    $post_type = get_post_type($post_id);
    $post_types = array('post', 'house'); // 対応する投稿タイプをリスト（'house' に変更）

    if (!in_array($post_type, $post_types)) {
        return $post_id;
    }

    // Google Map iframe の保存
    if (isset($_POST['google_map_iframe_1'])) {
        // iframe タグを許可するために wp_kses をカスタマイズ
        $allowed_html = array(
            'iframe' => array(
                'src' => true,
                'width' => true,
                'height' => true,
                'style' => true,
                'allowfullscreen' => true,
                'loading' => true,
                'referrerpolicy' => true
            )
        );
        $google_map_iframe_1 = wp_kses($_POST['google_map_iframe_1'], $allowed_html);
        update_post_meta($post_id, '_google_map_iframe_1', $google_map_iframe_1);
    }
    if (isset($_POST['google_map_iframe_2'])) {
        // iframe タグを許可するために wp_kses をカスタマイズ
        $allowed_html = array(
            'iframe' => array(
                'src' => true,
                'width' => true,
                'height' => true,
                'style' => true,
                'allowfullscreen' => true,
                'loading' => true,
                'referrerpolicy' => true
            )
        );
        $google_map_iframe_2 = wp_kses($_POST['google_map_iframe_2'], $allowed_html);
        update_post_meta($post_id, '_google_map_iframe_2', $google_map_iframe_2);
    }

    return $post_id;
}
add_action('save_post', 'save_google_map_metabox_data');


// いいねの状態を取得する関数
function get_like_status()
{
    // Nonceの検証
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'custom-nonce')) {
        wp_send_json_error(['message' => 'Invalid nonce.']);
    }

    // 投稿IDの取得
    $post_id = intval($_POST['post_id']);

    // 投稿IDが無効な場合
    if (!$post_id) {
        wp_send_json_error(['message' => 'Invalid post ID.']);
    }

    // いいねの状態を取得（0: いいねなし, 1: いいねあり）
    $liked = get_post_meta($post_id, 'liked', true);

    // メタデータが存在しない場合、デフォルト値0を設定
    if ($liked === '') {
        $liked = 0;
    }

    // 成功時に返すデータ
    wp_send_json_success(['liked' => $liked]);
}
add_action('wp_ajax_get_like_status', 'get_like_status');           // ログインユーザー用
add_action('wp_ajax_nopriv_get_like_status', 'get_like_status');    // 非ログインユーザー用（必要な場合）

// いいねをトグル（追加・解除）する関数
function toggle_like()
{
    // Nonceの検証
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'custom-nonce')) {
        wp_send_json_error(['message' => 'Invalid nonce.']);
    }

    // 投稿IDと状態の取得
    $post_id = intval($_POST['post_id']);
    $liked = intval($_POST['liked']); // likedが0の場合、いいねを解除

    // 投稿IDが無効な場合
    if (!$post_id) {
        wp_send_json_error(['message' => 'Invalid post ID.']);
    }

    // いいね状態を保存（0の場合は解除）
    update_post_meta($post_id, 'liked', $liked);

    // 成功時に返すデータ
    wp_send_json_success(['liked' => $liked]);
}
add_action('wp_ajax_toggle_like', 'toggle_like');           // ログインユーザー用
add_action('wp_ajax_nopriv_toggle_like', 'toggle_like');    // 非ログインユーザー用（必要な場合）

// 必要なJavaScriptのためにnonceとajaxurlをエクスポート
/*function enqueue_like_script() {
    wp_enqueue_script('like-script', get_template_directory_uri() . '/js/like-script.js', array('jquery'), null, true);

    // JavaScriptに渡すためのデータ
    wp_localize_script('like-script', 'customAjax', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('custom-nonce')
    ));
}
add_action('wp_enqueue_scripts', 'enqueue_like_script');*/






// Price_Range_Widget ウィジェットを登録
/*function register_price_range_widget() {
    include_once get_template_directory() . '/Price_Range_Widget.php'; 
    
    // クラスが読み込まれているか確認
    if ( class_exists( 'Price_Range_Widget' ) ) {
        echo 'Price_Range_Widget クラスが読み込まれました！';  // デバッグメッセージ
    } else {
        echo 'Price_Range_Widget クラスが読み込まれていません';  // デバッグメッセージ
    }

    // Price_Range_Widget を登録
    register_widget( 'Price_Range_Widget' );
}

// ウィジェットを widgets_init アクションフックで登録
add_action( 'widgets_init', 'register_price_range_widget' );*/



// Price_Range_Widget ウィジェットを登録
function register_price_range_widget()
{
    // ウィジェットクラスファイルの読み込み
    require_once get_template_directory() . '/Price_Range_Widget.php';

    // Price_Range_Widget を登録
    register_widget('Price_Range_Widget');
}

// ウィジェットを widgets_init アクションフックで登録
add_action('widgets_init', 'register_price_range_widget');

// 価格範囲フィルタリング機能を追加
function filter_price_range_by_get_params($query)
{
    if (!is_admin() && $query->is_main_query()) {
        // カテゴリーページでの価格フィルタリングを設定
        if (is_category('naigai-construction') || is_category('house')) {
            // デフォルトの価格範囲設定
            $min_price = isset($_GET['min_price']) ? (int)$_GET['min_price'] : 1000;
            $max_price = isset($_GET['max_price']) ? (int)$_GET['max_price'] : 4000;

            // 価格の順番を確認
            if ($min_price > $max_price) {
                return; // 正しい価格範囲でない場合はフィルタしない
            }

            // メタクエリの設定（価格がカスタムフィールドに保存されていると仮定）
            $meta_query = array(
                'relation' => 'AND',
                array(
                    'key' => 'Price',  // 価格フィールド
                    'value' => array($min_price, $max_price),
                    'compare' => 'BETWEEN',
                    'type' => 'NUMERIC',
                ),
            );

            // クエリにメタクエリを設定
            $query->set('meta_query', $meta_query);
        }

        // `naiga-tochi`カテゴリーページの場合（100~2000の範囲）
        if (is_category('naiga-tochi')) {
            $min_price = isset($_GET['min_price']) ? (int)$_GET['min_price'] : 100;
            $max_price = isset($_GET['max_price']) ? (int)$_GET['max_price'] : 2000;

            if ($min_price > $max_price) {
                return;
            }

            $meta_query = array(
                'relation' => 'AND',
                array(
                    'key' => 'Price',
                    'value' => array($min_price, $max_price),
                    'compare' => 'BETWEEN',
                    'type' => 'NUMERIC',
                ),
            );

            $query->set('meta_query', $meta_query);
        }
    }
}
add_action('pre_get_posts', 'filter_price_range_by_get_params');

if (!function_exists('is_mobile_device')) {
    function is_mobile_device()
    {
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';

        if (
            is_string($user_agent) && $user_agent !== '' &&
            preg_match('/(iPhone|iPad|iPod|Android.*Mobile|Windows Phone|BlackBerry|IEMobile|Opera Mini|Mobile)/i', $user_agent)
        ) {
            return true;
        }

        return false;
    }
}



// リストとギャラリービューのラジオボタンを表示
function display_view_toggle_buttons()
{
?>
    <div class="view-toggle-buttons">
        <span class="view-toggle-control" style="--bui-segmented-control-active-scale-x: 66px; --bui-segmented-control-active-transform-x: 3px;">
            <input id="view-list" class="view-toggle-radio" type="radio" name="view" value="list" checked>
            <label for="view-list" class="view-toggle-label">リスト</label>
            <input id="view-grid" class="view-toggle-radio" type="radio" name="view" value="grid">
            <label for="view-grid" class="view-toggle-label">ギャラリー</label>
        </span>
    </div>
<?php
}

// ビュー状態を保存する処理（AJAX）
function set_view_mode()
{
    if (isset($_POST['view_mode'])) {
        $viewMode = sanitize_text_field($_POST['view_mode']);
        // ビュー状態をオプションとして保存
        update_option('viewMode', $viewMode);
        wp_send_json_success(array('viewMode' => $viewMode));
    } else {
        wp_send_json_error(array('message' => 'View mode not set.'));
    }
}

add_action('wp_ajax_set_view_mode', 'set_view_mode');  // ログインユーザー用
add_action('wp_ajax_nopriv_set_view_mode', 'set_view_mode');  // ゲストユーザー用











// function handle_upload_images 画像の保存場所
// /wp-content/uploads/satei/
//├── tarouyamada/
//│   └── 2025/
//│       └── 03/
//│           ├── sample1.jpg
//│           └── sample2.jpg


// 売却査定アップロードとテーマにユーザー属性に応じたフォルダーの生成に画像の保存
/*function handle_upload_images() {
    error_log("【デバッグ】画像アップロード処理開始");

    if (!isset($_POST['store_reservation_nonce']) || !wp_verify_nonce($_POST['store_reservation_nonce'], 'store_reservation_action')) {
        error_log("【エラー】Nonce 検証失敗");
        wp_send_json_error(['message' => '無効なリクエストです。（Nonceエラー）']);
        return;
    }

    // ✅ name_slug（ローマ字）が優先されるように修正
    $user_name = isset($_POST['name_slug']) ? sanitize_file_name($_POST['name_slug']) :
                 (isset($_POST['name']) ? sanitize_file_name($_POST['name']) : 'guest_' . uniqid());

    $user_name = preg_replace('/[^a-zA-Z0-9_\-]/', '', $user_name); // 念のため再サニタイズ


    // ✅ WordPress推奨のアップロードパス取得
    $upload_base = wp_upload_dir(); // baseurl, basedir などが入る配列

    $year = date('Y');
    $month = date('m');
    $upload_dir = $upload_base['basedir'] . "/satei/{$user_name}/{$year}/{$month}";
    $upload_url = $upload_base['baseurl'] . "/satei/{$user_name}/{$year}/{$month}";

    if (!file_exists($upload_dir)) {
        if (!wp_mkdir_p($upload_dir)) {
            error_log("【エラー】フォルダ作成失敗: $upload_dir");
            wp_send_json_error(['message' => 'アップロード先のフォルダ作成に失敗しました']);
            return;
        }
    }

    $uploaded_urls = [];

    foreach ($_FILES['images']['name'] as $key => $name) {
        if ($_FILES['images']['error'][$key] !== UPLOAD_ERR_OK) {
            error_log("【エラー】アップロードエラー: " . $_FILES['images']['error'][$key]);
            continue;
        }

        $tmp_name = $_FILES['images']['tmp_name'][$key];
        $filename = sanitize_file_name($_FILES['images']['name'][$key]);
        $destination = $upload_dir . '/' . $filename;

        if (move_uploaded_file($tmp_name, $destination)) {
            $uploaded_urls[] = $upload_url . '/' . $filename;
        } else {
            error_log("【エラー】画像保存失敗: $filename");
        }
    }

    if (empty($uploaded_urls)) {
        wp_send_json_error(['message' => '画像のアップロードに失敗しました']);
        return;
    }

    error_log("【成功】アップロードURL: " . print_r($uploaded_urls, true));
    wp_send_json_success(['uploaded_image_urls' => $uploaded_urls]);
}



add_action('wp_ajax_upload_images', 'handle_upload_images');
add_action('wp_ajax_nopriv_upload_images', 'handle_upload_images');*/







// 予約フォームモーダルの処理


// 予約フォームの送信処理
// フォームのデータが送信されると、handle_store_reservation 関数が呼ばれます。この関数は、以下の処理を行っています：
// フォームから送信されたデータ（名前、カタカナ、メールアドレス、住所、来店日時、時間帯など）を取得し、サニタイズ（無害化）して、セキュリティを確保します。
// メールアドレスが正しいか確認します（無効な場合はエラーを表示）。
// PHPMailerを使って、指定されたメールアドレスに予約内容を送信します。
// メールが送信されたら、サンクスページにリダイレクトする処理ですが、今回はリダイレクトを行わない設定にしています。
add_action('wp_ajax_store_reservation_submit', 'handle_store_reservation_submit');
add_action('wp_ajax_nopriv_store_reservation_submit', 'handle_store_reservation_submit');

function handle_store_reservation_submit()
{
    error_log('【デバッグ】AJAXリクエストが到達しました');

    $to = 'contact@naigaicorp.net';
    if (empty($to)) {
        error_log('【エラー】送信先メールアドレスが設定されていません');
        wp_send_json_error(array('message' => 'メール送信エラー: 送信先が未設定です。'));
        return;
    }

    // ✅ Nonceチェック
    if (!isset($_POST['store_reservation_nonce']) || !wp_verify_nonce($_POST['store_reservation_nonce'], 'store_reservation_action')) {
        wp_send_json_error(['message' => 'Nonce error'], 403);
        return;
    }


    // ✅ フォームデータの取得
    if (!isset($_POST['data'])) {
        error_log('【エラー】フォームデータが送信されていません');
        wp_send_json_error(array('message' => 'データが送信されていません。'));
        return;
    }

    $form_data = json_decode(stripslashes($_POST['data']), true);
    if (json_last_error() !== JSON_ERROR_NONE || empty($form_data)) {
        error_log('【エラー】JSONデコード失敗: ' . json_last_error_msg());
        wp_send_json_error(array('message' => 'データの解析に失敗しました。'));
        return;
    }

    error_log('【デバッグ】フォームデータ受信: ' . print_r($form_data, true));

    // ✅ URL 判定（面接予約, 売却査定, 来店予約）
    $current_url = $_SERVER['HTTP_REFERER'] ?? '';
    $is_recruitment = strpos($current_url, '/recruitment') !== false;
    $is_satei = strpos($current_url, '/satei') !== false;

    // ------------------------------------
    // ▼ サニタイズ（共通の基本情報）
    // 用途  役割
    // name="name" HTML側で 送信するデータの名前（キー）
    // $form_data['name']  PHP側で POSTされた値を受け取る変数
    // ------------------------------------
    $last_name  = !empty($form_data['last_name']) ? sanitize_text_field($form_data['last_name']) : '';
    $first_name = !empty($form_data['first_name']) ? sanitize_text_field($form_data['first_name']) : '';
    $name = trim($last_name . ' ' . $first_name);

    $last_kana  = !empty($form_data['last_name_kana']) ? sanitize_text_field($form_data['last_name_kana']) : '';
    $first_kana = !empty($form_data['first_name_kana']) ? sanitize_text_field($form_data['first_name_kana']) : '';
    $katakana = trim($last_kana . ' ' . $first_kana); // ← ここが修正箇所


    // 🔹 画像保存用のユーザー名（ローマ字 Yamada_Taro 形式）
    $user_name = !empty($form_data['name_slug']) ? sanitize_file_name($form_data['name_slug']) : 'guest_' . uniqid();
    $user_name = preg_replace('/[^a-zA-Z0-9_]/', '', $user_name); // アルファベットと_だけ許可


    $email            = sanitize_email($form_data['email']);
    $phone            = sanitize_text_field($form_data['phone']);
    $postcode         = sanitize_text_field($form_data['postcode']);
    $address          = sanitize_textarea_field($form_data['address']);

    // ✅ 売却査定情報
    $sale_price = '';
    if (isset($form_data['sale_price'])) {
        $raw_price = sanitize_text_field($form_data['sale_price']);
        // 「未設定」または空なら代入しない
        if ($raw_price !== '未設定' && $raw_price !== '') {
            $sale_price = $raw_price . '万円';
        }
    }


    $property_status  = !empty($form_data['property_status']) ? sanitize_text_field($form_data['property_status']) : '';
    $sale_reason = !empty($form_data['sale_reason_text']) ? sanitize_text_field($form_data['sale_reason_text']) : '';
    $property_address = !empty($form_data['property_address']) ? sanitize_textarea_field($form_data['property_address']) : '';
    $sale_period      = !empty($form_data['sale_period']) ? sanitize_text_field($form_data['sale_period']) : '';
    $valuation_method = !empty($form_data['valuation_method']) ? sanitize_text_field($form_data['valuation_method']) : '';

    $building_area    = !empty($form_data['building_area']) ? sanitize_text_field($form_data['building_area']) . '㎡' : '';
    $land_area        = !empty($form_data['land_area']) ? sanitize_text_field($form_data['land_area']) . '㎡' : '';
    $year_built       = !empty($form_data['year_built']) ? sanitize_text_field($form_data['year_built']) . '年' : '';
    $floor_plan       = !empty($form_data['floor_plan']) ? sanitize_text_field($form_data['floor_plan']) : '';
    $rental_income    = !empty($form_data['rental_income']) ? sanitize_text_field($form_data['rental_income']) . '万円/月' : '';

    // 🔽 JavaScript側から送られてきた画像リスト（実際には使わなくてもOK）
    // フォーム送信時に「何をプレビューしていたか」を記録しているだけの情報です。
    $uploaded_images = isset($form_data['uploaded_images']) ? $form_data['uploaded_images'] : [];

    // 🔽 実際にアップロード成功した画像のURLを保存しておくための配列
    // 後でメール本文にこの画像のリンクを表示するために使います。
    // ✅ ユーザーが画像を送信してきていたら、画像処理をスタート
    $uploaded_image_urls = array();

    if (!empty($_FILES['images']) && is_array($_FILES['images']['name'])) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';
        $upload_overrides = array('test_form' => false);
        date_default_timezone_set('Asia/Tokyo');

        $upload_dir_info = wp_upload_dir();
        $user_name = !empty($form_data['name_slug']) ? sanitize_file_name($form_data['name_slug']) : 'guest';
        $timestamp     = date('Ymd_His');
        $unique_id     = uniqid();
        $unique_folder = $user_name . '_' . $timestamp . '_' . $unique_id;

        $base_dir = $upload_dir_info['basedir'] . '/satei/' . $unique_folder;
        $base_url = $upload_dir_info['baseurl'] . '/satei/' . $unique_folder;

        if (!file_exists($base_dir)) {
            wp_mkdir_p($base_dir);
        }

        foreach ($_FILES['images']['name'] as $index => $image_filename) {
            if ($_FILES['images']['error'][$index] === UPLOAD_ERR_OK) {
                $tmp_name = $_FILES['images']['tmp_name'][$index];
                $mime_type = mime_content_type($tmp_name);
                $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

                if (!in_array($mime_type, $allowed_types)) {
                    error_log("❌ 不正な画像形式: {$mime_type} - {$image_filename}");
                    continue;
                }

                $new_name = wp_unique_filename($base_dir, $image_filename);
                $saved = move_uploaded_file($tmp_name, $base_dir . '/' . $new_name);

                if ($saved) {
                    $uploaded_image_urls[] = $base_url . '/' . $new_name;
                }
            }
        }
    }

    // ✅ 最後に画像URLを $form_data に追加（DB保存用）
    $form_data['uploaded_images'] = $uploaded_image_urls;


    // ✅ メール件名を設定
    if ($is_recruitment) {
        $subject = "【面接予約】{$name} 様の面接予約";
    } elseif ($is_satei) {
        $subject = "【売却査定】{$name} 様の売却査定";
    } else {
        $subject = "【来店予約】{$name} 様の来店予約";
    }

    // ✅ 共通メール本文作成
    $message = "<html><body>";
    $message .= "<h3>" . esc_html($subject) . "</h3>";
    $message .= "<p><strong>お名前:</strong> " . esc_html($name) . " 様</p>";
    $message .= "<p><strong>カタカナ:</strong> " . esc_html($katakana) . "</p>";
    $message .= "<p><strong>メールアドレス:</strong> " . esc_html($email) . "</p>";
    $message .= "<p><strong>電話番号:</strong> " . esc_html($phone) . "</p>";
    $message .= "<p><strong>郵便番号:</strong> " . esc_html($postcode) . "</p>";
    $message .= "<p><strong>住所:</strong> " . esc_html($address) . "</p>";

    // 物件:ID番号　採用:ID番号がここに挿入する。
    if (!empty($form_data['property_id']) && !empty($form_data['property_title'])) {
        $label = !empty($form_data['property_label']) ? $form_data['property_label'] : ($is_recruitment ? '採用' : '物件');

        // ハイパーリンクを生成
        $link_url = get_permalink($form_data['property_id']);
        $link_title = esc_html($form_data['property_title']);
        $link_id = esc_html($form_data['property_id']);

        $message .= "<p><strong>{$label}:</strong> <a href='{$link_url}' target='_blank'>{$link_title}（ID: {$link_id}）</a></p>";
    }




    // ✅ 面接 or 来店予約 → 来店日時を追加　売却を除外している。
    if (!$is_satei) {
        $visit_date = !empty($form_data['visit-date']) ? sanitize_text_field($form_data['visit-date']) : '';
        $time_slot  = !empty($form_data['time-slot']) ? sanitize_text_field($form_data['time-slot']) : '';

        $message .= "<h3>来店予約情報</h3>";
        if ($visit_date) $message .= "<p><strong>来店日:</strong> " . esc_html($visit_date) . "</p>";
        if ($time_slot)  $message .= "<p><strong>時間帯:</strong> " . esc_html($time_slot) . "</p>";
    }

    if ($is_satei) {
        $message .= "<h3>売却査定情報</h3>";
        if ($sale_price)       $message .= "<p><strong>売却希望価格:</strong> " . esc_html($sale_price) . "</p>";
        if ($property_status)  $message .= "<p><strong>物件状況:</strong> " . esc_html($property_status) . "</p>";
        if ($sale_reason)      $message .= "<p><strong>売却理由:</strong> " . esc_html($sale_reason) . "</p>";
        if ($property_address) $message .= "<p><strong>売却予定住所:</strong> " . esc_html($property_address) . "</p>";
        if ($sale_period)      $message .= "<p><strong>売却時期:</strong> " . esc_html($sale_period) . "</p>";
        if ($valuation_method) $message .= "<p><strong>査定方法:</strong> " . esc_html($valuation_method) . "</p>";
        if ($building_area)    $message .= "<p><strong>建物面積:</strong> " . esc_html($building_area) . "</p>";
        if ($land_area)        $message .= "<p><strong>土地面積:</strong> " . esc_html($land_area) . "</p>";
        if ($year_built)       $message .= "<p><strong>築年数:</strong> " . esc_html($year_built) . "</p>";
        if ($floor_plan)       $message .= "<p><strong>間取り:</strong> " . esc_html($floor_plan) . "</p>";
        if ($rental_income)    $message .= "<p><strong>賃料収入:</strong> " . esc_html($rental_income) . "</p>";

        // ✅ 画像URLを追加
        if (!empty($uploaded_image_urls)) {
            $message .= "<h3>査定画像</h3>";
            foreach ($uploaded_image_urls as $image_url) {
                $message .= "<p><a href='" . esc_url($image_url) . "' target='_blank'>" . esc_html($image_url) . "</a></p>";
            }
        }
    }

    $message .= "</body></html>";
    $headers = array('Content-Type: text/html; charset=UTF-8');
    wp_mail($to, $subject, $message, $headers);

    global $wpdb;
    $type = $is_satei ? '査定' : ($is_recruitment ? '面接' : '来店');

    // ログ出力（実際に insert しようとしてる内容を出力）
    error_log('【DB登録テスト】データ挿入準備: ' . print_r([
        'type'    => $type,
        'name'    => $name,
        'email'   => $email,
        'phone'   => $phone,
        'created' => current_time('mysql'),
        'data'    => maybe_serialize($form_data) // ← ここに全てのデータがdbから画像URLも含まれる
    ], true));

    // 実際のデータベース挿入
    $result = $wpdb->insert(
        $wpdb->prefix . 'reservations',
        [
            'type'    => $type,
            'name'    => $name,
            'email'   => $email,
            'phone'   => $phone,
            'created' => current_time('mysql'),
            'data'    => maybe_serialize($form_data)
        ]
    );

    // 挿入失敗時のエラーログ
    if ($result === false) {
        error_log('【DBエラー】挿入失敗: ' . $wpdb->last_error);
    } else {
        error_log('【DB成功】予約情報が保存されました。');
    }


    wp_send_json_success();
}







// メインページ：予約一覧用のラッパー関数
function render_reservation_list_page()
{
    render_reservation_page('active');
}

// ゴミ箱ページ：削除済み予約の表示用ラッパー関数
function render_reservation_trash_page()
{
    render_reservation_page('trash');
}

// 管理画面メニューに追加
add_action('admin_menu', function () {
    add_menu_page('予約・査定一覧', '予約一覧', 'manage_options', 'reservation_list', 'render_reservation_list_page', 'dashicons-list-view', 25);
    add_submenu_page('reservation_list', '予約一覧', '予約一覧', 'manage_options', 'reservation_list', 'render_reservation_list_page');
    add_submenu_page('reservation_list', 'ゴミ箱一覧', 'ゴミ箱一覧', 'manage_options', 'reservation_trash', 'render_reservation_trash_page');
});

// 一覧ページ＆ゴミ箱ページ共通表示処理
function render_reservation_page($status = 'active')
{
    global $wpdb;
    $table = $wpdb->prefix . 'reservations';

    $is_trash = ($status === 'trash');
    $page_slug = $is_trash ? 'reservation_trash' : 'reservation_list';
    $page_title = $is_trash ? '🗑️ ゴミ箱一覧' : '📋 予約・査定一覧';

    // 完全削除処理
    if ($is_trash && isset($_POST['permanent_delete']) && isset($_POST['delete_ids'])) {
        foreach ($_POST['delete_ids'] as $id) {
            $wpdb->delete($table, ['id' => intval($id)]);
        }
        echo '<div class="updated notice"><p>完全に削除しました。</p></div>';
    }

    // 復元処理
    if ($is_trash && isset($_POST['restore']) && isset($_POST['restore_ids'])) {
        foreach ($_POST['restore_ids'] as $id) {
            $wpdb->update($table, ['status' => 'active'], ['id' => intval($id)]);
        }
        echo '<div class="updated notice"><p>復元しました。</p></div>';
    }

    // 論理削除処理（ゴミ箱に移動）
    if (!$is_trash && isset($_POST['delete_ids'])) {
        foreach ($_POST['delete_ids'] as $id) {
            $wpdb->update($table, ['status' => 'trash'], ['id' => intval($id)]);
        }
        echo '<div class="updated notice"><p>削除しました。</p></div>';
    }

    // 検索・絞り込み取得
    $filter = $_GET['type'] ?? '';
    $search = $_GET['s'] ?? '';
    $per_page = 10;
    $paged = max(1, intval($_GET['paged'] ?? 1));
    $offset = ($paged - 1) * $per_page;

    $where = $wpdb->prepare("WHERE status = %s", $status);
    if (!empty($filter)) {
        $where .= $wpdb->prepare(" AND type = %s", sanitize_text_field($filter));
    }
    if (!empty($search)) {
        $like = '%' . $wpdb->esc_like($search) . '%';
        $where .= $wpdb->prepare(" AND (name LIKE %s OR phone LIKE %s OR email LIKE %s)", $like, $like, $like);
    }

    // データ取得
    $total_items = $wpdb->get_var("SELECT COUNT(*) FROM $table $where");
    $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table $where ORDER BY created DESC LIMIT %d OFFSET %d", $per_page, $offset));

    echo '<div class="wrap">';
    echo '<h1>' . esc_html($page_title) . '</h1>';

    // 検索フォーム
    echo '<form method="get" style="margin-bottom:20px;">';
    echo '<input type="hidden" name="page" value="' . esc_attr($page_slug) . '">';
    echo '<input type="text" name="s" value="' . esc_attr($search) . '" placeholder="名前・電話・メール" style="width:250px; margin-right:10px;">';
    echo '<select name="type" onchange="this.form.submit()">';
    echo '<option value="">-- 種別で絞り込み --</option>';
    echo '<option value="来店"' . selected($filter, '来店', false) . '>来店</option>';
    echo '<option value="面接"' . selected($filter, '面接', false) . '>面接</option>';
    echo '<option value="査定"' . selected($filter, '査定', false) . '>査定</option>';
    echo '</select>';
    echo '</form>';

    // CSVダウンロード
    echo '<form method="get" action="' . esc_url(admin_url('admin-post.php')) . '" style="margin-bottom:20px;">';
    echo '<input type="hidden" name="action" value="download_reservation_csv">';
    echo '<input type="hidden" name="status" value="' . esc_attr($status) . '">';
    echo '<input type="hidden" name="type" value="' . esc_attr($filter) . '">';
    echo '<input type="hidden" name="s" value="' . esc_attr($search) . '">';
    echo '<input type="submit" class="button" value="⬇️ CSVダウンロード">';
    echo '</form>';

    // 一括操作フォーム
    echo '<form method="post">';
    echo '<div style="margin-bottom:20px;">';
    if ($is_trash) {
        echo '<input type="submit" name="restore" class="button" value="✔️ 選択を復元">';
        echo '<input type="submit" name="permanent_delete" class="button button-danger" value="🗑️ 完全に削除" onclick="return confirm(\'本当に完全に削除しますか？\')">';
    } else {
        echo '<input type="submit" name="delete" class="button button-danger" value="🗑️ 選択を削除" onclick="return confirm(\'削除しますか？\')">';
    }
    echo '</div>';

    // 表示一覧
    echo '<div style="display:flex; flex-wrap:wrap; gap:20px;">';
    foreach ($results as $row) {
        $data = maybe_unserialize($row->data);
        $images_html = '';

        // 画像一覧表示（あれば）
        if (!empty($data['uploaded_images']) && is_array($data['uploaded_images'])) {
            foreach ($data['uploaded_images'] as $img_url) {
                if (is_array($img_url)) $img_url = reset($img_url);
                $images_html .= '<a href="' . esc_url($img_url) . '" target="_blank"><img src="' . esc_url($img_url) . '" style="width:80px;height:auto;margin-right:5px;border:1px solid #ccc;"></a>';
            }
        }

        echo '<div style="flex:1 1 45%;border:1px solid #ccc;padding:15px;background:#fff;position:relative;">';
        echo '<label style="position:absolute;top:10px;right:10px;"><input type="checkbox" name="' . ($is_trash ? 'restore_ids[]' : 'delete_ids[]') . '" value="' . intval($row->id) . '"> 選択</label>';
        echo '<h2>👤 ' . esc_html($row->name) . '</h2>';
        echo '<p>📱 ' . esc_html($row->phone) . ' ／ 📧 ' . esc_html($row->email) . '</p>';
        echo '<p>🗂️ 種別: ' . esc_html($row->type) . ' ／ 🕒 登録: ' . esc_html($row->created) . '</p>';

        // 基本情報
        if (!empty($data['address'])) echo '<p>📍 住所: ' . esc_html($data['address']) . '</p>';
        if (!empty($data['visit-date']) || !empty($data['time-slot'])) echo '<p>🗓️ 来店: ' . esc_html($data['visit-date']) . ' ／ ' . esc_html($data['time-slot']) . '</p>';

        // 物件・採用タイトルと ID 表示（条件で切り替え）
        if (!empty($data['property_id'])) {
            $post_id = intval($data['property_id']);
            $post_title = get_the_title($post_id);
            if (empty($post_title) && !empty($data['property_title'])) {
                $post_title = esc_html($data['property_title']);
            }
            $label = ($row->type === '面接') ? '採用' : '物件';
            if (!empty($post_title)) {
                echo '<p>🏘️ ' . esc_html($label) . ': ' . esc_html($post_title) . '（ID: ' . $post_id . '）</p>';
            }
            if (!empty($data['from_modal'])) echo '<p>💬 送信元: ' . esc_html($data['from_modal']) . '</p>';
            if (!empty($data['page_url'])) echo '<p>🔗 参照URL: <a href="' . esc_url($data['page_url']) . '" target="_blank">' . esc_html($data['page_url']) . '</a></p>';
        }

        // 査定データ
        if ($row->type === '査定') {
            $fields = [
                '現況' => $data['property_status'] ?? '',
                '希望価格' => isset($data['sale_price']) ? $data['sale_price'] . '万円' : '',
                '査定方法' => $data['valuation_method'] ?? '',
                '建物面積' => isset($data['building_area']) ? $data['building_area'] . '㎡' : '',
                '土地面積' => isset($data['land_area']) ? $data['land_area'] . '㎡' : '',
                '築年数' => isset($data['year_built']) ? $data['year_built'] . '年' : '',
                '間取り' => $data['floor_plan'] ?? '',
                '賃料収入' => isset($data['rental_income']) ? $data['rental_income'] . '万円/月' : '',
                '査定住所' => $data['property_address'] ?? ''
            ];
            echo '<div style="margin-top:10px;padding:10px;background:#f6f6f6;border:1px solid #ddd;">';
            echo '<h3>📑 査定項目</h3>';
            foreach ($fields as $label => $value) {
                if (!empty($value)) {
                    echo '<p><strong>' . esc_html($label) . ':</strong> ' . esc_html($value) . '</p>';
                }
            }
            echo '</div>';
        }

        // 画像ブロック（あれば）
        if ($images_html) {
            echo '<div style="margin-top:10px;"><strong>🖼️ 画像:</strong><br>' . $images_html . '</div>';
        }

        echo '</div>';
    }
    echo '</div>';
    echo '</form>';

    // ページネーション
    $total_pages = ceil($total_items / $per_page);
    if ($total_pages > 1) {
        echo '<div class="pagination" style="margin-top:20px;">';
        for ($i = 1; $i <= $total_pages; $i++) {
            $url = add_query_arg(['paged' => $i, 'page' => $page_slug, 'type' => $filter, 's' => $search]);
            $class = ($i === $paged) ? 'active' : '';
            echo '<a class="' . $class . '" href="' . esc_url($url) . '">' . $i . '</a> ';
        }
        echo '</div>';
    }

    echo '</div>';
}



/**
 * カスタム予約情報テーブル（来店・面接・売却査定）を作成する関数
 */
function create_reservations_table()
{
    global $wpdb;

    // 作成するテーブル名を設定（例：wp_reservations）
    $table_name = $wpdb->prefix . 'reservations';

    // WordPressが推奨する文字コードと照合順序を取得
    $charset_collate = $wpdb->get_charset_collate();

    // SQLでテーブル作成（存在しない場合のみ作成）
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,           -- 各予約の一意なID
        type varchar(20) NOT NULL,                         -- 予約タイプ（来店・面接・査定）
        name varchar(100) NOT NULL,                        -- お客様名
        email varchar(100) NOT NULL,                       -- お客様メールアドレス
        phone varchar(50) NOT NULL,                        -- お客様電話番号
        created datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,-- 登録日時
        data longtext,                                     -- フォームからの送信データ（画像URL含む）
        PRIMARY KEY  (id)                                  -- 主キー設定（ID）
    ) $charset_collate;";

    // DBアップグレード関数を読み込み、テーブルを作成または更新
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}

// WordPress管理画面が初期化される際にテーブルを作成する処理を追加
add_action('admin_init', 'create_reservations_table');





// CSV ダウンロード　予約　売却査定　データ申し込み
function handle_csv_download()
{
    global $wpdb;
    $table = $wpdb->prefix . 'reservations';

    $status = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : 'active';
    $filter = isset($_GET['type']) ? sanitize_text_field($_GET['type']) : '';
    $search = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';

    // WHERE句の組み立て
    $where = $wpdb->prepare("WHERE status = %s", $status);
    if ($filter) {
        $where .= $wpdb->prepare(" AND type = %s", $filter);
    }
    if ($search) {
        $like = '%' . $wpdb->esc_like($search) . '%';
        $where .= $wpdb->prepare(" AND (name LIKE %s OR phone LIKE %s OR email LIKE %s)", $like, $like, $like);
    }

    // データ取得
    $results = $wpdb->get_results("SELECT * FROM $table $where ORDER BY created DESC");

    // CSVファイル名
    $filename = 'reservations_' . $status . '_' . date('Ymd_His') . '.csv';

    // ヘッダー出力
    header('Content-Type: text/csv; charset=Shift_JIS');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');

    $output = fopen('php://output', 'w');

    // ヘッダー行
    $headers = [
        'ID',
        '氏名',
        '電話番号',
        'メール',
        '種別',
        '登録日時',
        '住所',
        '来店日',
        '時間帯',
        '物件ID',
        '採用ID',
        '物件・採用タイトル',
        '送信元',
        '参照URL',
        '画像URL',
        // 査定専用項目：
        '現況',
        '希望価格',
        '査定方法',
        '建物面積',
        '土地面積',
        '築年数',
        '間取り',
        '賃料収入',
        '査定住所'
    ];
    fputcsv($output, array_map(function ($h) {
        return mb_convert_encoding($h, 'SJIS-win', 'UTF-8');
    }, $headers));

    foreach ($results as $row) {
        $data = maybe_unserialize($row->data);
        $property_id = intval($data['property_id'] ?? 0);
        $property_title = get_the_title($property_id);
        if (empty($property_title) && !empty($data['property_title'])) {
            $property_title = $data['property_title'];
        }

        $real_estate_id = '';
        $recruit_id = '';
        if ($row->type === '来店') {
            $real_estate_id = $property_id;
        } elseif ($row->type === '面接') {
            $recruit_id = $property_id;
        }

        $image_urls = '';
        if (!empty($data['uploaded_images']) && is_array($data['uploaded_images'])) {
            $img_urls = array_map(function ($img) {
                return is_array($img) ? reset($img) : $img;
            }, $data['uploaded_images']);
            $image_urls = implode(', ', $img_urls);
        }

        // 査定専用データ
        $fields = [
            $data['property_status'] ?? '',
            isset($data['sale_price']) ? $data['sale_price'] . '万円' : '',
            $data['valuation_method'] ?? '',
            isset($data['building_area']) ? $data['building_area'] . '㎡' : '',
            isset($data['land_area']) ? $data['land_area'] . '㎡' : '',
            isset($data['year_built']) ? $data['year_built'] . '年' : '',
            $data['floor_plan'] ?? '',
            isset($data['rental_income']) ? $data['rental_income'] . '万円/月' : '',
            $data['property_address'] ?? ''
        ];

        $line = [
            $row->id,
            $row->name,
            $row->phone,
            $row->email,
            $row->type,
            $row->created,
            $data['address'] ?? '',
            $data['visit-date'] ?? '',
            $data['time-slot'] ?? '',
            $real_estate_id,
            $recruit_id,
            $property_title,
            $data['from_modal'] ?? '',
            $data['page_url'] ?? '',
            $image_urls
        ];

        // 査定以外は空で埋める
        if ($row->type === '査定') {
            $line = array_merge($line, $fields);
        } else {
            $line = array_merge($line, array_fill(0, count($fields), ''));
        }

        fputcsv($output, array_map(function ($v) {
            return mb_convert_encoding($v, 'SJIS-win', 'UTF-8');
        }, $line));
    }

    fclose($output);
    exit;
}

add_action('admin_post_download_reservation_csv', 'handle_csv_download');





// ✅ カテゴリーごとの地域・間取り・価格を取得する関数
function fetch_category_filters()
{
    if (empty($_POST['category'])) {
        wp_send_json_error(['message' => 'カテゴリが選択されていません']);
    }
    $category_slug = sanitize_text_field($_POST['category']);
    $allowed_categories = ['naigai-construction', 'naigai-tochi', 'house'];
    if (!in_array($category_slug, $allowed_categories)) {
        wp_send_json_error(['message' => '無効なカテゴリ']);
    }

    $filters = [];
    if ($category_slug === 'house' || $category_slug === 'naigai-construction') {
        $filters['house_types'] = get_terms(['taxonomy' => 'house-type', 'hide_empty' => false]);
    } else {
        $filters['house_types'] = [];
    }

    $filters['regions'] = get_terms(['taxonomy' => 'region', 'hide_empty' => false]);
    $filters['prices'] = fetch_unique_price_ranges($category_slug, false);
    wp_send_json_success($filters);
}

// ✅ 間取りに紐づく地域のみ取得する関数
function fetch_regions_by_house_type()
{
    global $wpdb;
    if (empty($_POST['house_type']) || empty($_POST['category'])) {
        wp_send_json_error(['message' => '条件が不正です']);
    }
    $house_type_slug = sanitize_text_field($_POST['house_type']);
    $category_slug = sanitize_text_field($_POST['category']);
    $post_types = ($category_slug === 'house' || $category_slug === 'naigai-construction') ? ['post', 'house'] : ['post'];
    $results = $wpdb->get_results($wpdb->prepare(
        "SELECT DISTINCT t.term_id, t.name, t.slug
        FROM {$wpdb->terms} t
        INNER JOIN {$wpdb->term_taxonomy} tt ON t.term_id = tt.term_id AND tt.taxonomy = 'region'
        INNER JOIN {$wpdb->term_relationships} tr_region ON tr_region.term_taxonomy_id = tt.term_taxonomy_id
        INNER JOIN {$wpdb->posts} p ON tr_region.object_id = p.ID
        WHERE p.ID IN (
            SELECT object_id FROM {$wpdb->term_relationships} tr
            INNER JOIN {$wpdb->term_taxonomy} tt2 ON tr.term_taxonomy_id = tt2.term_taxonomy_id
            WHERE tt2.taxonomy = 'house-type'
            AND tt2.term_id = (SELECT term_id FROM {$wpdb->terms} WHERE slug = %s)
        )
        AND p.post_type IN (" . implode(',', array_fill(0, count($post_types), '%s')) . " )
        AND p.post_status = 'publish'",
        array_merge([$house_type_slug], $post_types)
    ), ARRAY_A);
    wp_send_json_success($results);
}

// ✅ 価格の範囲を取得する関数
// ✅ 価格の範囲を取得する関数（建物カテゴリ対応：house, naigai-construction）
// ✅ 価格の範囲を取得する関数（建物: house, naigai-construction のみを表示し、naigai-tochi を除外）
// ✅ 価格の範囲を取得する関数（建物: house, naigai-construction のみを表示し、naigai-tochi を除外）


function fetch_unique_price_ranges($category_slug = '', $send_response = true)
{
    global $wpdb;

    // 🔧 修正①：POSTから明示的に受け取る（引数の上書き）
    $category_slug     = !empty($_POST['category']) ? sanitize_text_field($_POST['category']) : $category_slug;
    $region_slug       = !empty($_POST['region']) ? sanitize_text_field($_POST['region']) : '';
    $house_type_slug   = !empty($_POST['house_type']) ? sanitize_text_field($_POST['house_type']) : '';

    // 🔧 投稿タイプは常に post + house を対象とする
    $post_types    = ['post', 'house'];
    $query_params  = $post_types;
    $placeholders  = implode(',', array_fill(0, count($post_types), '%s'));

    // 🔧 共通：カテゴリJOIN（house投稿も含めるため LEFT）
    $category_join = "
        LEFT JOIN {$wpdb->term_relationships} tr_cat ON p.ID = tr_cat.object_id
        LEFT JOIN {$wpdb->term_taxonomy} tt_cat ON tr_cat.term_taxonomy_id = tt_cat.term_taxonomy_id
        LEFT JOIN {$wpdb->terms} t_cat ON tt_cat.term_id = t_cat.term_id
    ";

    // 🔧 地域JOIN・フィルター
    $region_join = '';
    $region_filter = '';
    if (!empty($region_slug)) {
        $region_join = "
            INNER JOIN {$wpdb->term_relationships} tr_region ON p.ID = tr_region.object_id
            INNER JOIN {$wpdb->term_taxonomy} tt_region ON tr_region.term_taxonomy_id = tt_region.term_taxonomy_id
            INNER JOIN {$wpdb->terms} t_region ON tt_region.term_id = t_region.term_id
        ";
        $region_filter = "AND tt_region.taxonomy = 'region' AND t_region.slug = %s";
        $query_params[] = $region_slug;
    }

    // 🔧 間取りJOIN・フィルター（建物のみ）
    $house_type_join = '';
    $house_type_filter = '';
    $is_land = ($category_slug === 'naigai-tochi');
    if (!empty($house_type_slug) && !$is_land) {
        $house_type_join = "
            INNER JOIN {$wpdb->term_relationships} tr_type ON p.ID = tr_type.object_id
            INNER JOIN {$wpdb->term_taxonomy} tt_type ON tr_type.term_taxonomy_id = tt_type.term_taxonomy_id
            INNER JOIN {$wpdb->terms} t_type ON tt_type.term_id = t_type.term_id
        ";
        $house_type_filter = "AND tt_type.taxonomy = 'house-type' AND t_type.slug = %s";
        $query_params[] = $house_type_slug;
    }

    // 🔧 フィルター分岐（住宅＝house+naigai-construction / 土地＝naigai-tochi）
    if ($category_slug === 'naigai-tochi') {
        // 土地カテゴリ（post + category = naigai-tochi）
        $category_filter = "
            AND p.post_type = 'post'
            AND tt_cat.taxonomy = 'category'
            AND t_cat.slug = 'naigai-tochi'
        ";
    } elseif ($category_slug === 'house') {
        // 🔥 住宅 → house投稿 + postでcategory=naigai-constructionの両方取得
        $category_filter = "
            AND (
                (p.post_type = 'house')
                OR
                (p.post_type = 'post' AND tt_cat.taxonomy = 'category' AND t_cat.slug = 'naigai-construction')
            )
        ";
    } else {
        // 不正カテゴリ（念のため）
        if ($send_response) {
            wp_send_json_success(['prices' => []]);
        }
        return [];
    }

    // 🔧 SQL構築
    $query = $wpdb->prepare("
        SELECT DISTINCT p.ID AS post_id, pm.meta_value AS price
        FROM {$wpdb->posts} p
        LEFT JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = 'Price'
        $category_join
        $region_join
        $house_type_join
        WHERE p.post_status = 'publish'
          AND p.post_type IN ($placeholders)
          $category_filter
          $region_filter
          $house_type_filter
        ORDER BY CAST(pm.meta_value AS UNSIGNED) ASC
    ", ...$query_params);

    // 🔧 実行
    $results = $wpdb->get_results($query);

    if (empty($results)) {
        if ($send_response) {
            wp_send_json_success(['prices' => []]);
        }
        return [];
    }

    // 🔧 価格の整形
    $prices = [];
    foreach ($results as $row) {
        $value = (int)$row->price;
        $prices[] = [
            'slug' => $value . '-' . $row->post_id,
            'name' => ($value === 0 || empty($row->price)) ? '売却済' : number_format($value) . '万円'
        ];
    }

    if ($send_response) {
        wp_send_json_success(['prices' => $prices]);
    }

    return $prices;
}







function fetch_search_results()
{
    if (empty($_POST['post_id'])) {
        wp_send_json_error(['message' => '物件IDがありません']);
    }

    $post_id = intval($_POST['post_id']);
    $post = get_post($post_id);
    if (!$post) {
        wp_send_json_error(['message' => '該当物件が見つかりません']);
    }

    $price = get_post_meta($post_id, 'Price', true);

    // 🔧 サムネイル画像（最初に featured image を試す）
    $image = get_the_post_thumbnail_url($post_id, 'full');

    if (!$image) {
        // 🔧 動画タイプとIDを取得
        $video_type = get_post_meta($post_id, 'page_featured_type', true);
        $video_id   = get_post_meta($post_id, 'page_video_id', true);

        // 🔧 動画IDがある場合にサムネ画像を作る
        if (!empty($video_type) && !empty($video_id)) {
            if ($video_type === 'youtube') {
                $image = 'https://img.youtube.com/vi/' . esc_attr($video_id) . '/hqdefault.jpg';
            } elseif ($video_type === 'vimeo') {
                // 🔥 VimeoのサムネイルはAPIを使わないと取れない → 仮にnoimageにフォールバック
                $image = get_template_directory_uri() . '/images/noimage.gif'; // または Vimeo対応したいなら別途
            }
        }

        // 🔧 最終フォールバック：noimage
        if (empty($image)) {
            $image = get_template_directory_uri() . '/images/noimage.gif';
        }
    }

    $data = [
        'title'   => get_the_title($post_id),
        'post_id' => $post_id,
        'price'   => ($price === '0' || $price === '') ? '売却済み' : number_format((int)$price) . '万円',
        'image'   => $image
    ];

    wp_send_json_success($data);
}



add_action('wp_ajax_fetch_category_filters', 'fetch_category_filters');
add_action('wp_ajax_nopriv_fetch_category_filters', 'fetch_category_filters');
add_action('wp_ajax_fetch_price_by_filters', 'fetch_unique_price_ranges');
add_action('wp_ajax_nopriv_fetch_price_by_filters', 'fetch_unique_price_ranges');
add_action('wp_ajax_fetch_regions_by_house_type', 'fetch_regions_by_house_type');
add_action('wp_ajax_nopriv_fetch_regions_by_house_type', 'fetch_regions_by_house_type');
add_action('wp_ajax_fetch_search_results', 'fetch_search_results');
add_action('wp_ajax_nopriv_fetch_search_results', 'fetch_search_results');

// 失敗ログ（debug.logに出す）
add_action('wp_mail_failed', function ($wp_error) {
    if (is_wp_error($wp_error)) {
        error_log('【wp_mail_failed】' . print_r($wp_error->get_error_messages(), true));
    } else {
        error_log('【wp_mail_failed】unknown error');
    }
}, 10, 1);

// ローカル（docker）だけSMTPを Mailpit に向ける
add_action('phpmailer_init', function ($phpmailer) {

    // ここは「ローカルだけ」判定。必要なら条件変えてOK。
    $is_local = false;

    // WP_ENVIRONMENT_TYPE を使ってるならこれが一番安全
    if (function_exists('wp_get_environment_type') && wp_get_environment_type() === 'local') {
        $is_local = true;
    }

    // それが無い場合はURLで判定（あなたの compose が 127.0.0.1:8080 なので）
    if (defined('WP_HOME') && strpos(WP_HOME, '127.0.0.1:8080') !== false) {
        $is_local = true;
    }

    if (!$is_local) return;

    $phpmailer->isSMTP();
    $phpmailer->Host       = 'mailpit'; // docker compose の service 名
    $phpmailer->Port       = 1025;
    $phpmailer->SMTPAuth   = false;
    $phpmailer->SMTPSecure = false;

    // From を固定（未設定だと環境によって弾かれることがある）
    $phpmailer->From     = 'no-reply@local.test';
    $phpmailer->FromName = wp_specialchars_decode(get_bloginfo('name'), ENT_QUOTES);
});

// 管理画面「ツール」配下に Mail Test ページ追加
add_action('admin_menu', function () {
    add_management_page(
        'Mail Test',
        'Mail Test',
        'manage_options',
        'naiga-mail-test',
        'naiga_render_mail_test_page'
    );
});

function naiga_render_mail_test_page()
{
    if (!current_user_can('manage_options')) return;

    $sent = isset($_GET['sent']) ? sanitize_text_field($_GET['sent']) : '';
    $msg  = isset($_GET['msg']) ? sanitize_text_field($_GET['msg']) : '';

    echo '<div class="wrap"><h1>Mail Test（wp_mail）</h1>';

    if ($sent === '1') {
        echo '<div class="notice notice-success"><p>送信OK（Mailpitで確認して）</p></div>';
    } elseif ($sent === '0') {
        echo '<div class="notice notice-error"><p>送信失敗：' . esc_html($msg) . '</p></div>';
    }

    $default_to = get_option('admin_email');

    echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '">';
    wp_nonce_field('naiga_mail_test_action', 'naiga_mail_test_nonce');
    echo '<input type="hidden" name="action" value="naiga_send_test_mail">';

    echo '<table class="form-table">';
    echo '<tr><th><label>To</label></th><td><input type="email" name="to" value="' . esc_attr($default_to) . '" class="regular-text" required></td></tr>';
    echo '<tr><th><label>Subject</label></th><td><input type="text" name="subject" value="Test mail from local WP" class="regular-text" required></td></tr>';
    echo '<tr><th><label>Message</label></th><td><textarea name="message" class="large-text" rows="6" required>これはローカルDockerのwp_mailテストです。</textarea></td></tr>';
    echo '</table>';

    submit_button('テスト送信');
    echo '</form>';

    echo '<p>Mailpit受信箱： <code>http://localhost:8025</code></p>';
    echo '</div>';
}

add_action('admin_post_naiga_send_test_mail', function () {
    if (!current_user_can('manage_options')) {
        wp_die('permission denied');
    }
    if (!isset($_POST['naiga_mail_test_nonce']) || !wp_verify_nonce($_POST['naiga_mail_test_nonce'], 'naiga_mail_test_action')) {
        wp_die('nonce error');
    }

    $to      = sanitize_email($_POST['to'] ?? '');
    $subject = sanitize_text_field($_POST['subject'] ?? '');
    $message = wp_kses_post($_POST['message'] ?? '');

    if (!$to) {
        wp_safe_redirect(add_query_arg(['page' => 'naiga-mail-test', 'sent' => '0', 'msg' => 'Invalid To'], admin_url('tools.php')));
        exit;
    }

    $headers = ['Content-Type: text/plain; charset=UTF-8'];

    $ok = wp_mail($to, $subject, $message, $headers);

    if ($ok) {
        wp_safe_redirect(add_query_arg(['page' => 'naiga-mail-test', 'sent' => '1'], admin_url('tools.php')));
    } else {
        wp_safe_redirect(add_query_arg(['page' => 'naiga-mail-test', 'sent' => '0', 'msg' => 'wp_mail returned false'], admin_url('tools.php')));
    }
    exit;
});
