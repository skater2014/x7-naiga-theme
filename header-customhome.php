<?php
if (!defined('ABSPATH')) {
    exit;
}

$post_id = get_queried_object_id();

/* IEZUKURI_COMMON_HEADER_LOGO_SOURCE
 *
 * /iezukuri/ 配下のロゴは、家づくりトップページの設定を共通使用する。
 * アーカイブでは投稿IDが取得できないため、トップページIDを正本にする。
 */
$brand_source_id = $post_id;

$request_uri  = isset($_SERVER['REQUEST_URI'])
    ? wp_unslash($_SERVER['REQUEST_URI'])
    : '';

$request_path = trim(
    (string) wp_parse_url($request_uri, PHP_URL_PATH),
    '/'
);

$is_iezukuri_request = (
    $request_path === 'iezukuri'
    || strpos($request_path, 'iezukuri/') === 0
);

if ($is_iezukuri_request) {
    $iezukuri_top_page = get_page_by_path(
        'iezukuri',
        OBJECT,
        'page'
    );

    if ($iezukuri_top_page instanceof WP_Post) {
        $brand_source_id = (int) $iezukuri_top_page->ID;
    }
}


$brand_logo_id = absint(get_post_meta($brand_source_id, '_hub_ch_brand_logo_id', true));
$brand_logo_url = $brand_logo_id ? wp_get_attachment_image_url($brand_logo_id, 'medium') : '';
$brand_text = get_post_meta($brand_source_id, '_hub_ch_brand_text', true);
$brand_subtext = get_post_meta($brand_source_id, '_hub_ch_brand_subtext', true);
$header_style = get_post_meta($post_id, '_hub_ch_header_menu_style', true);

if ($brand_text === '') {
    $brand_text = '内外グループ';
}
if ($brand_subtext === '') {
    $brand_subtext = '';
}
if ($header_style === '') {
    $header_style = 'default';
}

$home_url = home_url('/');

$room_url    = function_exists('naigai_customhome_find_page_url') ? naigai_customhome_find_page_url(array('room-gallery', 'room-gallary'), '/room-gallery/') : home_url('/room-gallery/');
$hokubei_url = function_exists('naigai_customhome_find_page_url') ? naigai_customhome_find_page_url(array('hokubei-jutaku'), '/hokubei-jutaku/') : home_url('/hokubei-jutaku/');
$natural_url = function_exists('naigai_customhome_find_page_url') ? naigai_customhome_find_page_url(array('zairai-kouhou'), '/zairai-kouhou/') : home_url('/zairai-kouhou/');
$ideal_url   = function_exists('naigai_customhome_find_page_url') ? naigai_customhome_find_page_url(array('nasu-ideal-home'), '/nasu-ideal-home/') : home_url('/nasu-ideal-home/');
$contact_url = function_exists('naigai_customhome_find_page_url') ? naigai_customhome_find_page_url(array('contact', 'customer-info-form'), '/contact/') : home_url('/contact/');

$header_fallback = static function () use ($room_url, $hokubei_url, $natural_url, $ideal_url, $contact_url) {
    echo '<ul class="ch-site-header__list">';
    echo '<li><a href="' . esc_url(home_url('/iezukuri/concept/')) . '">注文住宅の考え方</a></li>';
    echo '<li><a href="' . esc_url(home_url('/iezukuri/design-policy/')) . '">設計姿勢</a></li>';
    echo '<li><a href="' . esc_url(home_url('/iezukuri/nasu-house/')) . '">那須での家づくり</a></li>';
    echo '<li><a href="' . esc_url(home_url('/iezukuri/design-office/')) . '">デザインと設計</a></li>';
    echo '<li><a href="' . esc_url(home_url('/iezukuri/')) . '">家づくり</a></li>';
    echo '<li><a href="' . esc_url(home_url('/iezukuri/contact/')) . '">ご相談・資料請求</a></li>';
    echo '</ul>';
};
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>

<body <?php body_class('customhome-header-body'); ?>>
    <?php wp_body_open(); ?>
    <div id="page" class="site ch-site">

        <?php
/* IEZ_CUSTOMHOME_HEADER_FALLBACK_START */
/**
 * Custom Home Header fallback
 *
 * 役割:
 * - /iezukuri/plans/ のようなアーカイブページで header 引数が空でも、
 *   ヘッダーのブランド名とvariantを空にしない。
 *
 * 方針:
 * - Headerの主表示は「内外グループ」に統一。
 * - 英字 CUSTOM HOME は重複しやすいため、Headerでは使わない。
 */
if (!isset($args) || !is_array($args)) {
    $args = array();
}

$args['brand_main'] = trim((string)($args['brand_main'] ?? ''));
$args['brand_sub']  = trim((string)($args['brand_sub'] ?? ''));
$args['variant']    = trim((string)($args['variant'] ?? ''));

if ($args['brand_main'] === '') {
    $args['brand_main'] = '内外グループ';
}

if ($args['variant'] === '') {
    $args['variant'] = 'default';
}

/*
 * header-customhome.php 内で別変数名を使っている場合にも効くように、
 * よく使う変数名にも同じ値を入れておく。
 */
$brand_main        = trim((string)($brand_main ?? $args['brand_main']));
$header_brand_main = trim((string)($header_brand_main ?? $args['brand_main']));
$ch_brand_main     = trim((string)($ch_brand_main ?? $args['brand_main']));
$customhome_brand_main = trim((string)($customhome_brand_main ?? $args['brand_main']));

if ($brand_main === '') {
    $brand_main = '内外グループ';
}
if ($header_brand_main === '') {
    $header_brand_main = '内外グループ';
}
if ($ch_brand_main === '') {
    $ch_brand_main = '内外グループ';
}
if ($customhome_brand_main === '') {
    $customhome_brand_main = '内外グループ';
}

$brand_sub        = trim((string)($brand_sub ?? $args['brand_sub']));
$header_brand_sub = trim((string)($header_brand_sub ?? $args['brand_sub']));
$ch_brand_sub     = trim((string)($ch_brand_sub ?? $args['brand_sub']));

$variant        = trim((string)($variant ?? $args['variant']));
$header_variant = trim((string)($header_variant ?? $args['variant']));

if ($variant === '') {
    $variant = 'default';
}
if ($header_variant === '') {
    $header_variant = 'default';
}
/* IEZ_CUSTOMHOME_HEADER_FALLBACK_END */
?>
<header class="ch-site-header ch-site-header--<?php echo esc_attr($header_style); ?>" role="banner">
            <div class="ch-site-header__inner">
                <a class="ch-site-header__brand" href="<?php echo esc_url($home_url); ?>">
                    <?php if ($brand_logo_url) : ?>
                        <img class="ch-site-header__brand-logo" src="<?php echo esc_url($brand_logo_url); ?>" alt="<?php echo esc_attr($brand_text); ?>">
                    <?php else : ?>
                        <span class="ch-site-header__brand-main"><?php echo esc_html($brand_text); ?></span>
                        <span class="ch-site-header__brand-sub"><?php echo esc_html($brand_subtext); ?></span>
                    <?php endif; ?>
                </a>

                <nav class="ch-site-header__nav ch-site-header__nav--desktop" aria-label="注文住宅ヘッダーナビゲーション">
                    <?php
                    if (has_nav_menu('customhome-header-menu')) {
                        wp_nav_menu(array(
                            'theme_location' => 'customhome-header-menu',
                            'container'      => false,
                            'menu_class'     => 'ch-site-header__list',
                            'fallback_cb'    => false,
                        ));
                    } else {
                        $header_fallback();
                    }
                    ?>
                </nav>

                <button
                    type="button"
                    class="ch-site-header__toggle"
                    aria-expanded="false"
                    aria-controls="ch-site-header-drawer"
                    data-ch-menu-toggle>
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>

            <div id="ch-site-header-drawer" class="ch-site-header__drawer" hidden data-ch-menu-drawer>
                <div class="ch-site-header__drawer-inner">
                    <nav class="ch-site-header__nav ch-site-header__nav--mobile" aria-label="注文住宅モバイルナビゲーション">
                        <?php
                        if (has_nav_menu('customhome-header-menu')) {
                            wp_nav_menu(array(
                                'theme_location' => 'customhome-header-menu',
                                'container'      => false,
                                'menu_class'     => 'ch-site-header__drawer-list',
                                'fallback_cb'    => false,
                            ));
                        } else {
                            echo '<ul class="ch-site-header__drawer-list">';
                            echo '<li><a href="' . esc_url($room_url) . '">お部屋ギャラリー</a></li>';
                            echo '<li><a href="' . esc_url($hokubei_url) . '">北米住宅の住まい</a></li>';
                            echo '<li><a href="' . esc_url($natural_url) . '">自然素材の住まい</a></li>';
                            echo '<li><a href="' . esc_url($ideal_url) . '">理想の住まいを考える</a></li>';
                            echo '<li><a href="' . esc_url($contact_url) . '">お問い合わせ</a></li>';
                            echo '</ul>';
                        }
                        ?>
                    </nav>
                </div>
            </div>
        </header>
<?php
/* =========================================================
 * IEZUKURI BREADCRUMB START
 *
 * 【このブロックの役割】
 * 家づくり専用ヘッダーの直下で、
 * サイト共通パンくず関数 breadcrumb1() を呼び出す。
 *
 * 【表示対象】
 * 実際にアクセスしているURLが次に該当するページ。
 *
 * ・/iezukuri/
 * ・/iezukuri/ 以下のすべてのページ
 *
 * 固定ページ、間取り一覧、間取り詳細をすべて含む。
 *
 * 【これまで表示されなかった理由】
 * 以前はget_page_uri()で固定ページの親子関係を調べていた。
 *
 * その方法では、URLが/iezukuri/contact/であっても、
 * 管理画面で「iezukuri」が親ページに設定されていなければ、
 * 内部URIが「contact」になり、対象外と判定される。
 *
 * ここではファイル上部で作成済みの
 * $is_iezukuri_requestを使用する。
 *
 * $is_iezukuri_request:
 * 実際のアクセスURLが/iezukuri/以下ならtrueになる変数。
 *
 * 【重要】
 * パンくずの文字と階層はここへ直接書かない。
 * inc/functions/bread1-comment.phpで一元管理する。
 * ========================================================= */


/*
 * $naigai_show_iezukuri_breadcrumb
 *
 * 家づくりパンくずを表示するかどうかを保存する変数。
 *
 * true:
 *   /iezukuri/ またはその配下
 *
 * false:
 *   家づくり以外のページ
 */
$naigai_show_iezukuri_breadcrumb =
    !empty($is_iezukuri_request);
?>


<?php if (
    $naigai_show_iezukuri_breadcrumb
    && function_exists('breadcrumb1')
) : ?>

    <div
        class="iezukuri-breadcrumb"
        data-iezukuri-breadcrumb
    >
        <?php
        /*
         * breadcrumb1()
         *
         * 現在表示しているページ種類を判定し、
         * パンくずのHTMLを出力する共通関数。
         *
         * 固定ページ、間取り一覧、間取り詳細の
         * 条件分岐はbread1-comment.php側に置く。
         */
        breadcrumb1();
        ?>
    </div>

<?php endif; ?>

<!-- IEZUKURI BREADCRUMB END -->
<?php
/* IEZUKURI_COMMON_BACK_LINK_RENDER_START */

$naigai_request_uri =
    isset($_SERVER['REQUEST_URI'])
        ? wp_unslash($_SERVER['REQUEST_URI'])
        : '';

$naigai_request_path = trim(
    (string) wp_parse_url(
        $naigai_request_uri,
        PHP_URL_PATH
    ),
    '/'
);

$naigai_is_iezukuri = (
    $naigai_request_path === 'iezukuri'
    || strpos(
        $naigai_request_path,
        'iezukuri/'
    ) === 0
);

if ($naigai_is_iezukuri) {
    $naigai_back_link_file =
        get_template_directory()
        . '/hub/pages/iezukuri/templates/shared/back-link.php';

    if (is_file($naigai_back_link_file)) {
        require $naigai_back_link_file;
    }
}

/* IEZUKURI_COMMON_BACK_LINK_RENDER_END */
?>

