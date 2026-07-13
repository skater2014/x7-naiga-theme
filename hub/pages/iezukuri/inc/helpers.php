<?php
/**
 * hub/pages/iezukuri/inc/helpers.php
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('naigai_iezukuri_root_slug')) {
    function naigai_iezukuri_root_slug()
    {
        return 'iezukuri';
    }
}

if (!function_exists('naigai_iezukuri_is_page')) {
    function naigai_iezukuri_is_page($post_id = null)
    {
        $post_id = $post_id ?: get_queried_object_id();
        $post = get_post($post_id);

        if (!$post || $post->post_type !== 'page') {
            return false;
        }

        if ($post->post_name === naigai_iezukuri_root_slug()) {
            return true;
        }

        foreach (get_post_ancestors($post) as $ancestor_id) {
            if (get_post_field('post_name', $ancestor_id) === naigai_iezukuri_root_slug()) {
                return true;
            }
        }

        return false;
    }
}

if (!function_exists('naigai_iezukuri_is_subpage')) {
    function naigai_iezukuri_is_subpage($post_id = null)
    {
        $post_id = $post_id ?: get_queried_object_id();
        $post = get_post($post_id);

        return $post && $post->post_name !== naigai_iezukuri_root_slug() && naigai_iezukuri_is_page($post_id);
    }
}

if (!function_exists('naigai_iezukuri_slug')) {
    function naigai_iezukuri_slug($post_id = null)
    {
        $post_id = $post_id ?: get_queried_object_id();
        return (string) get_post_field('post_name', $post_id);
    }
}

if (!function_exists('naigai_iezukuri_meta')) {
    function naigai_iezukuri_meta($post_id, $key, $default = '')
    {
        $value = get_post_meta($post_id, $key, true);
        return $value !== '' ? $value : $default;
    }
}

add_filter('body_class', function ($classes) {
    if (!is_page()) {
        
    if (is_singular('iez_plan')) {
        $classes[] = 'customhome-header-body';
        $classes[] = 'iezukuri-page';
        $classes[] = 'iezukuri-subpage';
        $classes[] = 'hub-customhome-subpage';
        $classes[] = 'iezukuri-plan-single-body';

        $post_id = get_queried_object_id();
        if ($post_id) {
            $slug = get_post_field('post_name', $post_id);
            if ($slug) {
                $classes[] = 'iezukuri-plan-single-body--' . sanitize_html_class($slug);
            }
        }

        $classes = array_values(array_unique($classes));
    }

return $classes;
    }

    $page_id = get_queried_object_id();

    if (!$page_id) {
        return $classes;
    }

    $slug = get_post_field('post_name', $page_id);

    /**
     * /iezukuri/ トップ判定
     */
    $is_iezukuri_top = ($slug === 'iezukuri');

    /**
     * /iezukuri/ 配下のサブページ判定
     */
    $is_iezukuri_subpage = false;

    $ancestors = get_post_ancestors($page_id);

    foreach ($ancestors as $ancestor_id) {
        if (get_post_field('post_name', $ancestor_id) === 'iezukuri') {
            $is_iezukuri_subpage = true;
            break;
        }
    }

    if (!$is_iezukuri_top && !$is_iezukuri_subpage) {
        return $classes;
    }

    /**
     * 共通
     */
    $classes[] = 'iezukuri-page';

    /**
     * トップページだけ
     */
    if ($is_iezukuri_top) {
        $classes[] = 'hub-customhome-page';
    }

    /**
     * サブページだけ
     */
    if ($is_iezukuri_subpage) {
        $classes[] = 'iezukuri-subpage';
        $classes[] = 'iezukuri-subpage--' . sanitize_html_class($slug);
        $classes[] = 'hub-customhome-subpage';
        $classes[] = 'hub-customhome-subpage--' . sanitize_html_class($slug);

        /**
         * 他の処理で混入していても、サブページではトップ用クラスを消す。
         */
        $classes = array_diff($classes, [
            'hub-customhome-page',
        ]);
    }

    return array_values(array_unique($classes));
});


/* IEZUKURI WP NAV START */

add_action('after_setup_theme', function () {
    register_nav_menus(array(
        'customhome-header-menu' => '注文住宅ヘッダーメニュー',
        'customhome-page-menu'   => '注文住宅ページ内メニュー',
    ));
});

if (!function_exists('naigai_iezukuri_default_nav_items')) {
    function naigai_iezukuri_default_nav_items()
    {
        return array(
            array('label' => '注文住宅トップ',     'url' => home_url('/iezukuri/')),
            array('label' => '注文住宅の考え方', 'url' => home_url('/iezukuri/concept/')),
            array('label' => '設計姿勢',         'url' => home_url('/iezukuri/design-policy/')),
            array('label' => '那須での家づくり', 'url' => home_url('/iezukuri/nasu-house/')),
            array('label' => 'デザインと設計',   'url' => home_url('/iezukuri/design-office/')),
            array('label' => '会社概要',         'url' => home_url('/iezukuri/company/')),
            array('label' => 'ご相談・資料請求', 'url' => home_url('/iezukuri/contact/')),
        );
    }
}

if (!function_exists('naigai_iezukuri_render_wp_menu')) {
    function naigai_iezukuri_render_wp_menu($theme_location, $menu_class)
    {
        if (has_nav_menu($theme_location)) {
            wp_nav_menu(array(
                'theme_location' => $theme_location,
                'container'      => false,
                'menu_class'     => $menu_class,
                'depth'          => 1,
                'fallback_cb'    => false,
            ));
            return;
        }

        echo '<ul class="' . esc_attr($menu_class) . '">';
        foreach (naigai_iezukuri_default_nav_items() as $item) {
            echo '<li><a href="' . esc_url($item['url']) . '">' . esc_html($item['label']) . '</a></li>';
        }
        echo '</ul>';
    }
}

/* IEZUKURI WP NAV END */


/* IEZUKURI LOCAL NAV ITEMS START */

if (!function_exists('naigai_iezukuri_get_localnav_items')) {
    function naigai_iezukuri_get_localnav_items($post_id = 0)
    {
        $post_id = $post_id ? (int) $post_id : get_queried_object_id();
        $slug = get_post_field('post_name', $post_id);

        /*
         * /iezukuri トップ
         */
        if ($slug === 'iezukuri' || is_page_template('template-construction-hub.php')) {
            return array(
                array('label' => 'コンセプト',     'href' => '#iez-top-intro'),
                array('label' => '特徴',           'href' => '#iez-top-feature'),
                array('label' => 'できること',     'href' => '#iez-top-purpose'),
                array('label' => '内容を見る',     'href' => '#iez-top-works'),
                array('label' => '流れ',           'href' => '#iez-top-flow'),
                array('label' => '平屋プラン',     'href' => '#iez-top-plan'),
                array('label' => '那須の家づくり', 'href' => '#iez-top-area'),
                array('label' => '会社',           'href' => '#iez-top-company'),
                array('label' => 'FAQ',            'href' => '#iez-top-faq'),
                array('label' => '相談',           'href' => '#iez-cta'),
            );
        }

        /*
         * /iezukuri/nasu-house
         */
        if ($slug === 'nasu-house') {
            return array(
                array('label' => '概要',           'href' => '#iez-content'),
                array('label' => '那須の気候',     'href' => '#iez-nasu-climate'),
                array('label' => '土地条件',       'href' => '#iez-nasu-land'),
                array('label' => '施工の考え方',   'href' => '#iez-nasu-build'),
                array('label' => '相談',           'href' => '#iez-cta'),
            );
        }

        /*
         * /iezukuri/contact
         */
        if ($slug === 'contact') {
            return array(
                array('label' => '相談の流れ',     'href' => '#iez-contact-flow'),
                array('label' => 'フォーム',       'href' => '#iez-contact-form'),
                array('label' => 'FAQ',            'href' => '#iez-contact-faq'),
                array('label' => '相談',           'href' => '#iez-cta'),
            );
        }

        /*
         * /iezukuri/company
         */
        if ($slug === 'company') {
            return array(
                array('label' => '会社概要',       'href' => '#iez-content'),
                array('label' => 'アクセス',       'href' => '#iez-company-map'),
                array('label' => '相談',           'href' => '#iez-cta'),
            );
        }

        /*
         * その他サブページ
         */
        return array(
            array('label' => '本文', 'href' => '#iez-content'),
            array('label' => '相談', 'href' => '#iez-cta'),
        );
    }
}

/* IEZUKURI LOCAL NAV ITEMS END */

/**
 * naigai_iez_sub_json
 *
 * サブページ用のJSONメタを安全に配列で返す。
 *
 * 使い方:
 * - section-sub-localnav.php
 * - section-sub-content.php
 *
 * 返り値:
 * - メタがJSON配列なら array
 * - 空 / 壊れたJSON / 未設定なら $default
 */
if (!function_exists('naigai_iez_sub_json')) {
    function naigai_iez_sub_json($post_id, $key, $default = array()) {
        $raw = get_post_meta($post_id, $key, true);

        if ($raw === '' || $raw === null) {
            return $default;
        }

        if (is_array($raw)) {
            return $raw;
        }

        if (!is_string($raw)) {
            return $default;
        }

        $decoded = json_decode($raw, true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
            return $default;
        }

        return $decoded;
    }
}


/**
 * ---------------------------------------------------------
 * 旧URLリダイレクト
 * ---------------------------------------------------------
 *
 * 旧:
 * - /iezukuri/new-house/
 * - /iezukuri/nisetai/
 * - /iezukuri/chuko/
 *
 * 新:
 * - /iezukuri/new-house/
 * - /iezukuri/nisetai/
 * - /iezukuri/chuko/
 *
 * 役割:
 * - 古い導線やキャッシュが残っていても 404 にしない。
 */
if (!function_exists('naigai_iezukuri_legacy_route_redirects')) {
    function naigai_iezukuri_legacy_route_redirects() {
        if (is_admin()) {
            return;
        }

        $path = isset($_SERVER['REQUEST_URI']) ? parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) : '';
        $path = trim((string) $path, '/');

        $home_path = trim((string) parse_url(home_url('/'), PHP_URL_PATH), '/');

        if ($home_path !== '' && str_starts_with($path, $home_path . '/')) {
            $path = substr($path, strlen($home_path) + 1);
        }

        $map = array(
            'iezukuri/two-family' => '/iezukuri/nisetai',
            'iezukuri/renovation' => '/iezukuri/chuko',
        );

        if (isset($map[$path])) {
            wp_safe_redirect(home_url($map[$path]), 301);
            exit;
        }
    }
}
add_action('template_redirect', 'naigai_iezukuri_legacy_route_redirects', 1);

/* === IEZUKURI CHUKO NISETai BODY CLASS START === */

add_filter('body_class', function ($classes) {
    if (!is_page()) {
        return $classes;
    }

    $page_id = get_queried_object_id();

    if (!$page_id) {
        return $classes;
    }

    $slug = (string) get_post_field('post_name', $page_id);

    if ($slug === 'chuko') {
        $classes[] = 'iezukuri-service-page';
        $classes[] = 'iezukuri-chuko-page';
        $classes[] = 'iezukuri-used-house-page';
    }

    if ($slug === 'nisetai') {
        $classes[] = 'iezukuri-service-page';
        $classes[] = 'iezukuri-nisetai-page';
        $classes[] = 'iezukuri-two-family-page';
    }

    return array_values(array_unique($classes));
}, 30);

/* === IEZUKURI CHUKO NISETai BODY CLASS END === */

/* === IEZUKURI THREE SERVICE BODY CLASS START === */

add_filter('body_class', function ($classes) {
    if (!is_page()) {
        return $classes;
    }

    $page_id = get_queried_object_id();
    if (!$page_id) {
        return $classes;
    }

    $slug = (string) get_post_field('post_name', $page_id);

    if (in_array($slug, array('chuko', 'new-house', 'nisetai'), true)) {
        $classes[] = 'iezukuri-service-page';
        $classes[] = 'iezukuri-service-page--' . $slug;
    }

    if ($slug === 'chuko') {
        $classes[] = 'iezukuri-chuko-page';
        $classes[] = 'iezukuri-used-house-page';
    }

    if ($slug === 'new-house') {
        $classes[] = 'iezukuri-new-house-page';
    }

    if ($slug === 'nisetai') {
        $classes[] = 'iezukuri-nisetai-page';
        $classes[] = 'iezukuri-two-family-page';
    }

    return array_values(array_unique($classes));
}, 40);

/* === IEZUKURI THREE SERVICE BODY CLASS END === */

/* === IEZUKURI FOOTER MENU START === */

/**
 * いえづくり footer メニュー。
 *
 * footer:
 * - 全ページ共通のサイトマップ
 * - 主要ページをすべて表示する
 *
 * 中間ナビ:
 * - ページ途中の回遊導線
 * - footerとは別役割
 */
if (!function_exists('naigai_iezukuri_footer_menu_items')) {
    function naigai_iezukuri_footer_menu_items() {
        return array(
            array('label' => 'トップ',             'url' => home_url('/iezukuri/')),
            array('label' => '家づくりの考え方',   'url' => home_url('/iezukuri/concept/')),
            array('label' => '設計方針',           'url' => home_url('/iezukuri/design-policy/')),
            array('label' => '新築住宅',           'url' => home_url('/iezukuri/new-house/')),
            array('label' => '中古住宅',           'url' => home_url('/iezukuri/chuko/')),
            array('label' => '二世帯住宅',         'url' => home_url('/iezukuri/nisetai/')),
            array('label' => '住まいリフォーム',   'url' => home_url('/iezukuri/renovation/')),
            array('label' => '間取り・プラン',     'url' => home_url('/iezukuri/plans/')),
            array('label' => '暮らしのポイント',   'url' => home_url('/iezukuri/point/')),
            array('label' => '会社概要',           'url' => home_url('/iezukuri/company/')),
            array('label' => 'お問い合わせ',       'url' => home_url('/iezukuri/contact/')),
        );
    }
}

add_action('after_setup_theme', function () {
    register_nav_menus(array(
        'iezukuri_footer_menu' => '家づくりFooter：全メニュー',
    ));
}, 20);

/* === IEZUKURI FOOTER MENU END === */


/* === IEZUKURI GLOBAL HEADER MENU STYLE START === */

/**
 * 上部ヘッダーナビデザインの全ページ共通反映。
 *
 * 目的:
 * - 管理画面「ヘッダーナビデザイン」の選択を、
 *   /iezukuri/ トップだけでなく、サブページ・アーカイブ・single・カスタムテンプレートにも反映する。
 *
 * 対象:
 * - 画面最上部の .ch-site-header
 * - 内外グループ / 注文住宅トップ / 会社概要 / ご相談・資料請求 / 間取り図 / ハンバーガー
 *
 * 正本:
 * - 設定値の正本は /iezukuri/ トップページの _hub_ch_header_menu_style。
 * - サブページごとの post_meta を正本にしない。
 *
 * 理由:
 * - サブページや archive は現在ページの post_id がトップページと違う。
 * - archive では通常の固定ページ post_meta を直接読めない。
 * - そのため現在ページの _hub_ch_header_menu_style を読む方式だと、トップだけ反映される。
 *
 * CSS連携:
 * - このPHPは body class に iez-header-menu--default / pipe / minimal を出す。
 * - 見た目の正本は hub/pages/iezukuri/css/common/nav.css。
 * - top.css には .ch-site-header 系CSSを置かない。
 *
 * 注意:
 * - これは中間ナビではない。
 * - section-localnav.php / section-sub-localnav.php とは別。
 * - footerメニューとも別。
 * - <main> の data属性では、<main> より前に出る header へCSSを当てられないため body class を使う。
 */
if (!function_exists('naigai_iezukuri_header_menu_allowed_styles')) {
    function naigai_iezukuri_header_menu_allowed_styles() {
        return array('default', 'standard', 'pipe', 'minimal');
    }
}

if (!function_exists('naigai_iezukuri_is_global_header_menu_target')) {
    function naigai_iezukuri_is_global_header_menu_target() {
        $request_path = isset($_SERVER['REQUEST_URI'])
            ? trim((string) parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/')
            : '';

        return (
            $request_path === 'iezukuri'
            || strpos($request_path, 'iezukuri/') === 0
            || is_post_type_archive('iez_plan')
            || is_singular('iez_plan')
        );
    }
}

if (!function_exists('naigai_iezukuri_get_global_header_menu_style')) {
    function naigai_iezukuri_get_global_header_menu_style() {
        $allowed = naigai_iezukuri_header_menu_allowed_styles();
        $style = '';

        /*
         * 1. /iezukuri/ トップページの設定を正本にする。
         */
        $top_page = get_page_by_path('iezukuri');

        if ($top_page && !is_wp_error($top_page)) {
            $style = get_post_meta($top_page->ID, '_hub_ch_header_menu_style', true);
        }

        /*
         * 2. 保険として option を見る。
         */
        if ($style === '') {
            $style = get_option('naigai_iez_header_menu_style', '');
        }

        /*
         * 3. さらに保険として現在ページのメタを見る。
         */
        if ($style === '') {
            $post_id = get_queried_object_id();

            if (!$post_id && function_exists('get_the_ID')) {
                $post_id = get_the_ID();
            }

            if ($post_id) {
                $style = get_post_meta($post_id, '_hub_ch_header_menu_style', true);
            }
        }

        if ($style === '') {
            $style = 'default';
        }

        if (!in_array($style, $allowed, true)) {
            $style = 'default';
        }

        return $style;
    }
}

/**
 * トップページや各ページで保存した値を option にも同期する。
 * archive / custom page 用の保険。
 */
add_action('save_post', function ($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (wp_is_post_revision($post_id) || wp_is_post_autosave($post_id)) {
        return;
    }

    if (!isset($_POST['_hub_ch_header_menu_style'])) {
        return;
    }

    $style = sanitize_key((string) $_POST['_hub_ch_header_menu_style']);
    $allowed = naigai_iezukuri_header_menu_allowed_styles();

    if (!in_array($style, $allowed, true)) {
        $style = 'default';
    }

    update_option('naigai_iez_header_menu_style', $style, false);
}, 99);

/**
 * 全いえづくりページの body class に上部ヘッダーナビデザインを出す。
 */
add_filter('body_class', function ($classes) {
    if (!naigai_iezukuri_is_global_header_menu_target()) {
        return $classes;
    }

    $style = naigai_iezukuri_get_global_header_menu_style();

    $classes[] = sanitize_html_class('iez-header-menu--' . $style);

    if ($style === 'standard') {
        $classes[] = 'iez-header-menu--default';
    }

    return array_values(array_unique($classes));
}, 45);

/* === IEZUKURI GLOBAL HEADER MENU STYLE END === */

