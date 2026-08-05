<?php
/**
 * =========================================================
 * hub/pages/iezukuri/inc/functions-iezukuri.php
 * =========================================================
 *
 * 家づくり専用 functions。
 *
 * 役割:
 * - /iezukuri/ トップ
 * - /iezukuri/ 配下サブページ
 * - iez_plan 一覧・詳細
 * の CSS / JS 読み込みをここで管理する。
 *
 * 読み込み順:
 * functions.php
 *   ↓
 * hub/pages/iezukuri/inc/loader.php
 *   ↓
 * this file
 *
 * 注意:
 * - functions.php には CSS / JS を書かない。
 * - iezukuri-service-house.css は使わない。
 * - トップ本文は iezukuri.css。
 * - サブページ本文は iezukuri-subpage.css。
 * - 新築住宅本文は iezukuri-new-house.css。
 * - hero は ch-hero.css。
 * =========================================================
 */

if (!defined('ABSPATH')) {
    exit;
}


/**
 * 家づくり参考プランに属するtaxonomyページか。
 *
 * taxonomyごとに別CSSを作らず、
 * 通常の参考プランアーカイブと同じCSSを使用するための判定。
 */
if (!function_exists('naigai_iezukuri_is_plan_taxonomy')) {
    function naigai_iezukuri_is_plan_taxonomy(): bool
    {
        return is_tax(array(
            'iez_plan_type',
            'iez_plan_size',
            'iez_plan_feature',
            'iez_plan_structure',
            'iez_plan_scope',
            'iez_plan_building_form',
            'iez_plan_layout',
            'iez_plan_area',
        ));
    }
}

/**
 * /iezukuri/ 系ページかどうかを判定する。
 */
if (!function_exists('naigai_iezukuri_is_target_request')) {
    function naigai_iezukuri_is_target_request() {
        if (is_admin()) {
            return false;
        }

        if (is_singular('iez_plan') || is_post_type_archive('iez_plan') || naigai_iezukuri_is_plan_taxonomy()) {
            return true;
        }

        if (!is_page()) {
            return false;
        }

        $post_id = get_queried_object_id();
        if (!$post_id) {
            return false;
        }

        $uri = trim((string) get_page_uri($post_id), '/');

        return ($uri === 'iezukuri' || str_starts_with($uri, 'iezukuri/'));
    }
}

/**
 * 現在ページの /iezukuri/ 内 URI を返す。
 */
if (!function_exists('naigai_iezukuri_current_uri')) {
    function naigai_iezukuri_current_uri() {
        if (!is_page()) {
            return '';
        }

        $post_id = get_queried_object_id();
        return $post_id ? trim((string) get_page_uri($post_id), '/') : '';
    }
}

/**
 * CSS を存在確認つきで読む。
 */
if (!function_exists('naigai_iezukuri_enqueue_style_file')) {
    function naigai_iezukuri_enqueue_style_file($handle, $rel_path, $deps = array()) {
        $path = get_template_directory() . $rel_path;

        if (!file_exists($path)) {
            return;
        }

        wp_enqueue_style(
            $handle,
            get_template_directory_uri() . $rel_path,
            $deps,
            filemtime($path)
        );
    }
}

/**
 * JS を存在確認つきで読む。
 */
if (!function_exists('naigai_iezukuri_enqueue_script_file')) {
    function naigai_iezukuri_enqueue_script_file($handle, $rel_path, $deps = array()) {
        $path = get_template_directory() . $rel_path;

        if (!file_exists($path)) {
            return;
        }

        wp_enqueue_script(
            $handle,
            get_template_directory_uri() . $rel_path,
            $deps,
            filemtime($path),
            true
        );
    }
}

/**
 * 家づくり CSS / JS 読み込み本体。
 */
if (!function_exists('naigai_iezukuri_enqueue_dedicated_assets')) {
    function naigai_iezukuri_enqueue_dedicated_assets() {
        if (!naigai_iezukuri_is_target_request()) {
            return;
        }

        $uri = naigai_iezukuri_current_uri();

        $is_top       = ($uri === 'iezukuri');
        $is_subpage   = (!$is_top);
        $is_new_house = in_array($uri, array('iezukuri/new-house', 'iezukuri/new-house'), true);

        if (is_singular('iez_plan') || is_post_type_archive('iez_plan') || naigai_iezukuri_is_plan_taxonomy()) {
            $is_subpage = true;
        }

        /**
         * トップ・サブページ共通CSS
         */
        naigai_iezukuri_enqueue_style_file(
            'naigai-iezukuri-base',
            '/hub/pages/iezukuri/css/base/base.css'
        );

        naigai_iezukuri_enqueue_style_file(
            'naigai-iezukuri-main',
            '/hub/pages/iezukuri/css/top/top.css',
            array('naigai-iezukuri-base')
        );

        naigai_iezukuri_enqueue_style_file(
            'naigai-iezukuri-hero',
            '/hub/pages/iezukuri/css/common/hero.css',
            array('naigai-iezukuri-base', 'naigai-iezukuri-main')
        );

        naigai_iezukuri_enqueue_style_file(
            'naigai-iezukuri-nav',
            '/hub/pages/iezukuri/css/common/nav.css',
            array('naigai-iezukuri-base', 'naigai-iezukuri-main')
        );

        /**
         * サブページ共通CSS
         */
        if ($is_subpage) {
            naigai_iezukuri_enqueue_style_file(
                'naigai-iezukuri-subpage',
                '/hub/pages/iezukuri/css/subpage/subpage.css',
                array(
                    'naigai-iezukuri-base',
                    'naigai-iezukuri-main',
                    'naigai-iezukuri-hero',
                    'naigai-iezukuri-nav',
                )
            );
        }

        /**
         * 新築住宅専用CSS
         */
        if ($is_new_house) {
            naigai_iezukuri_enqueue_style_file(
                'naigai-iezukuri-new-house',
                '/hub/pages/iezukuri/css/page-styles/new-house.css',
                array('naigai-iezukuri-subpage')
            );
        }

        /**
         * プラン系CSS。
         * ファイルがある場合だけ読む。
         */
        if (is_post_type_archive('iez_plan') || naigai_iezukuri_is_plan_taxonomy()) {
            naigai_iezukuri_enqueue_style_file(
                'naigai-iezukuri-plan-colors',
                '/hub/pages/iezukuri/css/plan/plan-colors.css',
                array('naigai-iezukuri-subpage')
            );

            naigai_iezukuri_enqueue_style_file(
                'naigai-iezukuri-plan-archive',
                '/hub/pages/iezukuri/css/page-styles/plan-archive.css',
                array('naigai-iezukuri-plan-colors')
            );
        }

        if (is_singular('iez_plan')) {
            naigai_iezukuri_enqueue_style_file(
                'naigai-iezukuri-plan-detail',
                '/hub/pages/iezukuri/css/iezukuri-plan-detail.css',
                array('naigai-iezukuri-subpage')
            );
        }

        /**
         * JS
         */
        naigai_iezukuri_enqueue_script_file(
            'naigai-iezukuri-nav',
            '/hub/pages/iezukuri/js/iezukuri-nav.js',
            array()
        );

        naigai_iezukuri_enqueue_script_file(
            'naigai-iezukuri-main',
            '/hub/pages/iezukuri/js/iezukuri.js',
            array()
        );
    }
}
add_action('wp_enqueue_scripts', 'naigai_iezukuri_enqueue_dedicated_assets', 900);


/* =========================================================
 * NAIGAI IEZUKURI ASSET NORMALIZER START
 *
 * 役割:
 * - /iezukuri 配下の CSS / JS 読み込みをこの functions-iezukuri.php に集約する。
 * - functions.php には追加しない。
 * - 重複 enqueue があっても、最後にここで順番を正す。
 *
 * CSS順:
 * 1. iezukuri-base.css    : 変数・土台
 * 2. ch-hero.css          : hero専用
 * 3. iezukuri-nav.css     : header / localnav専用
 * 4. iezukuri.css         : トップ本文・カード・CTA。トップでは最後に読む
 * 5. subpage / detail CSS : 子ページだけ最後に読む
 * ========================================================= */

if (!function_exists('naigai_iezukuri_is_page_scope')) {
    function naigai_iezukuri_is_page_scope(): bool
    {
        /*
         * IEZUKURI_PLAN_TAXONOMY_PAGE_SCOPE
         *
         * 参考プランtaxonomyも通常アーカイブと同じ
         * 家づくりCSS/JSの対象にする。
         */
        if (
            is_singular('iez_plan')
            || is_post_type_archive('iez_plan')
            || naigai_iezukuri_is_plan_taxonomy()
        ) {
            return true;
        }

        if (is_page_template(array(
            'template-construction-hub.php',
            'page-construction-hub-sub.php',
            'template-iezukuri-used-house.php',
            'template-iezukuri-two-family.php',
        ))) {
            return true;
        }

        $post_id = get_queried_object_id();
        if (!$post_id) {
            return false;
        }

        $post = get_post($post_id);
        if (!$post) {
            return false;
        }

        if ($post->post_name === 'iezukuri') {
            return true;
        }

        $ancestors = get_post_ancestors($post_id);
        foreach ($ancestors as $ancestor_id) {
            $ancestor = get_post($ancestor_id);
            if ($ancestor && $ancestor->post_name === 'iezukuri') {
                return true;
            }
        }

        return false;
    }
}

if (!function_exists('naigai_iezukuri_enqueue_asset_file')) {
    function naigai_iezukuri_enqueue_asset_file(string $handle, string $rel, array $deps = array(), string $type = 'style'): void
    {
        $path = get_template_directory() . $rel;
        $uri  = get_template_directory_uri() . $rel;

        if (!file_exists($path)) {
            return;
        }

        $ver = filemtime($path);

        if ($type === 'script') {
            wp_enqueue_script($handle, $uri, $deps, $ver, true);
            return;
        }

        wp_enqueue_style($handle, $uri, $deps, $ver);
    }
}

add_action('wp_enqueue_scripts', function () {
    if (!naigai_iezukuri_is_page_scope()) {
        return;
    }

    /*
     * 既存の重複・順番違いをいったん外す。
     * ファイルは消さない。読み込み順だけここで正す。
     */
    $style_handles = array(
        'naigai-iezukuri',
        'naigai-iezukuri-css',
        'naigai-iezukuri-base',
        'naigai-iezukuri-main',
        'naigai-iezukuri-hero',
        'naigai-iezukuri-nav',
        'naigai-iezukuri-subpage',
        'naigai-iezukuri-subpage-css',
        'naigai-iezukuri-new-house',
        'naigai-iezukuri-used-house',
        'naigai-iezukuri-two-family',
        'naigai-iezukuri-plan-archive',
        'naigai-iezukuri-plan-archive-css',
        'naigai-iezukuri-plan-detail',
    );

    foreach ($style_handles as $handle) {
        wp_dequeue_style($handle);
        wp_deregister_style($handle);
    }

    $script_handles = array(
        'naigai-iezukuri',
        'naigai-iezukuri-main',
        'naigai-iezukuri-nav',
        'naigai-iezukuri-swiper',
    );

    foreach ($script_handles as $handle) {
        wp_dequeue_script($handle);
        wp_deregister_script($handle);
    }

    /*
     * CSS: この順番が基準。
     */
    naigai_iezukuri_enqueue_asset_file(
        'naigai-iezukuri-base',
        '/hub/pages/iezukuri/css/base/base.css'
    );

    naigai_iezukuri_enqueue_asset_file(
        'naigai-iezukuri-hero',
        '/hub/pages/iezukuri/css/common/hero.css',
        array('naigai-iezukuri-base')
    );

    naigai_iezukuri_enqueue_asset_file(
        'naigai-iezukuri-nav',
        '/hub/pages/iezukuri/css/common/nav.css',
        array('naigai-iezukuri-base', 'naigai-iezukuri-hero')
    );

    naigai_iezukuri_enqueue_asset_file(
        'naigai-iezukuri-main',
        '/hub/pages/iezukuri/css/top/top.css',
        array('naigai-iezukuri-base', 'naigai-iezukuri-hero', 'naigai-iezukuri-nav')
    );

    /*
     * 子ページ系。
     * トップページには subpage CSS を読ませない。
     */
    $post_id = get_queried_object_id();
    $slug    = $post_id ? get_post_field('post_name', $post_id) : '';

    if ($slug && $slug !== 'iezukuri') {
        naigai_iezukuri_enqueue_asset_file(
            'naigai-iezukuri-subpage',
            '/hub/pages/iezukuri/css/subpage/subpage.css',
            array('naigai-iezukuri-main')
        );
    }

    if (is_page_template('template-iezukuri-used-house.php')) {
        /*
         * 中古住宅ページは専用テンプレート。
         * CSSファイルは増やさず、class は iezukuri.css 側で管理。
         */
    }

    if (is_page_template('template-iezukuri-two-family.php')) {
        /*
         * 二世帯住宅ページは専用テンプレート。
         * CSSファイルは増やさず、class は iezukuri.css 側で管理。
         */
    }

    if (is_post_type_archive('iez_plan') || naigai_iezukuri_is_plan_taxonomy() || $slug === 'plans') {
        naigai_iezukuri_enqueue_asset_file(
            'naigai-iezukuri-plan-archive',
            '/hub/pages/iezukuri/css/page-styles/plan-archive.css',
            array('naigai-iezukuri-main')
        );
    }

    if (is_singular('iezukuri_plan')) {
        naigai_iezukuri_enqueue_asset_file(
            'naigai-iezukuri-plan-detail',
            '/hub/pages/iezukuri/css/iezukuri-plan-detail.css',
            array('naigai-iezukuri-main')
        );
    }

    /*
     * JSもここで管理。
     */
    naigai_iezukuri_enqueue_asset_file(
        'naigai-iezukuri-nav',
        '/hub/pages/iezukuri/js/iezukuri-nav.js',
        array(),
        'script'
    );

    naigai_iezukuri_enqueue_asset_file(
        'naigai-iezukuri-main',
        '/hub/pages/iezukuri/js/iezukuri.js',
        array('naigai-iezukuri-nav'),
        'script'
    );
}, 99);

/* NAIGAI IEZUKURI ASSET NORMALIZER END */


/* =========================================================
 * IEZUKURI FINAL ASSET MANAGER START
 *
 * /iezukuri 関連のCSS/JS最終管理。
 * functions.phpには書かない。
 *
 * 読み込み順:
 * 1. iezukuri-base.css
 * 2. ch-hero.css
 * 3. iezukuri-nav.css
 * 4. iezukuri.css
 * 5. iezukuri-subpage.css ※子ページだけ
 *
 * chuko / nisetai は専用CSSを増やさず iezukuri.css で管理。
 * ========================================================= */

if (!function_exists('naigai_iezukuri_final_is_scope')) {
    function naigai_iezukuri_final_is_scope(): bool
    {
        if (is_page_template(array(
            'template-construction-hub.php',
            'page-construction-hub-sub.php',
            'template-iezukuri-chuko.php',
            'template-iezukuri-nisetai.php',
            'template-iezukuri-new-house.php',
        ))) {
            return true;
        }

        $post_id = get_queried_object_id();
        if (!$post_id) {
            return false;
        }

        $post = get_post($post_id);
        if (!$post) {
            return false;
        }

        if ($post->post_name === 'iezukuri') {
            return true;
        }

        foreach (get_post_ancestors($post_id) as $ancestor_id) {
            $ancestor = get_post($ancestor_id);
            if ($ancestor && $ancestor->post_name === 'iezukuri') {
                return true;
            }
        }

        return false;
    }
}

if (!function_exists('naigai_iezukuri_final_enqueue_file')) {
    function naigai_iezukuri_final_enqueue_file(string $handle, string $rel, array $deps = array(), string $type = 'style'): void
    {
        $path = get_template_directory() . $rel;
        if (!file_exists($path)) {
            return;
        }

        $uri = get_template_directory_uri() . $rel;
        $ver = filemtime($path);

        if ($type === 'script') {
            wp_enqueue_script($handle, $uri, $deps, $ver, true);
            return;
        }

        wp_enqueue_style($handle, $uri, $deps, $ver);
    }
}

add_action('wp_enqueue_scripts', function () {
    if (!naigai_iezukuri_final_is_scope()) {
        return;
    }

    /*
     * 既存の重複・順番違いをここで整理。
     * ファイルは消さず、読み込み順だけ最終決定する。
     */
    foreach (array(
        'naigai-iezukuri',
        'naigai-iezukuri-css',
        'naigai-iezukuri-base',
        'naigai-iezukuri-main',
        'naigai-iezukuri-hero',
        'naigai-iezukuri-nav',
        'naigai-iezukuri-subpage',
        'naigai-iezukuri-subpage-css',
        'naigai-iezukuri-used-house',
        'naigai-iezukuri-two-family',
        'naigai-iezukuri-service-house',
        'naigai-iezukuri-chuko',
        'naigai-iezukuri-nisetai',
        'naigai-iezukuri-plan-archive',
        'naigai-iezukuri-plan-detail',
    ) as $handle) {
        wp_dequeue_style($handle);
        wp_deregister_style($handle);
    }

    foreach (array(
        'naigai-iezukuri',
        'naigai-iezukuri-main',
        'naigai-iezukuri-nav',
    ) as $handle) {
        wp_dequeue_script($handle);
        wp_deregister_script($handle);
    }

    naigai_iezukuri_final_enqueue_file(
        'naigai-iezukuri-base',
        '/hub/pages/iezukuri/css/base/base.css'
    );

    naigai_iezukuri_final_enqueue_file(
        'naigai-iezukuri-hero',
        '/hub/pages/iezukuri/css/common/hero.css',
        array('naigai-iezukuri-base')
    );

    naigai_iezukuri_final_enqueue_file(
        'naigai-iezukuri-nav',
        '/hub/pages/iezukuri/css/common/nav.css',
        array('naigai-iezukuri-base', 'naigai-iezukuri-hero')
    );

    naigai_iezukuri_final_enqueue_file(
        'naigai-iezukuri-main',
        '/hub/pages/iezukuri/css/top/top.css',
        array('naigai-iezukuri-base', 'naigai-iezukuri-hero', 'naigai-iezukuri-nav')
    );

    $post_id = get_queried_object_id();
    $slug = $post_id ? get_post_field('post_name', $post_id) : '';

    if ($slug && $slug !== 'iezukuri') {
        naigai_iezukuri_final_enqueue_file(
            'naigai-iezukuri-subpage',
            '/hub/pages/iezukuri/css/subpage/subpage.css',
            array('naigai-iezukuri-main')
        );
    }

    if ($slug === 'plans' || is_post_type_archive('iez_plan') || naigai_iezukuri_is_plan_taxonomy()) {
        naigai_iezukuri_final_enqueue_file(
            'naigai-iezukuri-plan-archive',
            '/hub/pages/iezukuri/css/page-styles/plan-archive.css',
            array('naigai-iezukuri-main')
        );
    }

    if (is_singular('iezukuri_plan')) {
        naigai_iezukuri_final_enqueue_file(
            'naigai-iezukuri-plan-detail',
            '/hub/pages/iezukuri/css/iezukuri-plan-detail.css',
            array('naigai-iezukuri-main')
        );
    }

    naigai_iezukuri_final_enqueue_file(
        'naigai-iezukuri-nav',
        '/hub/pages/iezukuri/js/iezukuri-nav.js',
        array(),
        'script'
    );

    naigai_iezukuri_final_enqueue_file(
        'naigai-iezukuri-main',
        '/hub/pages/iezukuri/js/iezukuri.js',
        array('naigai-iezukuri-nav'),
        'script'
    );
}, 999);

/* IEZUKURI FINAL ASSET MANAGER END */









/**
 * =========================================================
 * IEZUKURI CSS LOAD FINAL NORMALIZER
 * =========================================================
 *
 * 目的:
 * - /iezukuri/ トップだけ iezukuri.css を読む
 * - /iezukuri/ 配下サブページからトップ用 iezukuri.css を外す
 * - subpage.css の依存から main を外す
 * - concept / company / contact の専用CSSだけ読む
 *
 * 管理:
 * - 色: iezukuri-base.css
 * - Hero: ch-hero.css
 * - Nav: iezukuri-nav.css
 * - Top: iezukuri.css
 * - Subpage: iezukuri-subpage.css
 * =========================================================
 */
function naigai_iezukuri_css_load_final_normalizer() {
    if (is_admin()) {
        return;
    }

    $post_id = get_queried_object_id();
    if (!$post_id || !is_page()) {
        return;
    }

    $path = wp_parse_url(get_permalink($post_id), PHP_URL_PATH);
    $path = is_string($path) ? trailingslashit($path) : '';

    $is_iezukuri_top   = ($path === '/iezukuri/');
    $is_iezukuri_child = (!$is_iezukuri_top && strpos($path, '/iezukuri/') === 0);

    /*
     * chuko / nisetai は iezukuri.css 側で管理している。
     * サブページ扱いでも main CSS を外してはいけない。
     */
    $slug = get_post_field('post_name', $post_id);
    $is_iezukuri_service_page = in_array($slug, array('chuko', 'nisetai'), true);

    if (!$is_iezukuri_top && !$is_iezukuri_child) {
        return;
    }

    /*
     * サブページではトップ用CSSを外す。
     * さらに subpage.css の deps から main を外す。
     */
    if ($is_iezukuri_child && !$is_iezukuri_service_page) {
        global $wp_styles;

        if ($wp_styles && isset($wp_styles->registered['naigai-iezukuri-subpage'])) {
            $wp_styles->registered['naigai-iezukuri-subpage']->deps = array_values(array_diff(
                (array) $wp_styles->registered['naigai-iezukuri-subpage']->deps,
                array('naigai-iezukuri-main')
            ));
        }

        wp_dequeue_style('naigai-iezukuri-main');
        wp_deregister_style('naigai-iezukuri-main');
    }

    /*
     * ページ専用CSS。
     * 存在するファイルだけ読む。
     */
    if ($is_iezukuri_child) {
        $slug = get_post_field('post_name', $post_id);

        $page_css_map = array('concept' => 'concept.css',
            'company' => 'company.css',
            'contact' => 'contact.css',
            'chuko'   => 'chuko.css',
            'nisetai' => 'nisetai.css',
        );

        if (isset($page_css_map[$slug])) {
            $rel = '/hub/pages/iezukuri/css/page-styles/' . $page_css_map[$slug];
            $abs = get_template_directory() . $rel;

            if (file_exists($abs)) {
                wp_enqueue_style(
                    'naigai-iezukuri-page-' . $slug,
                    get_template_directory_uri() . $rel,
                    array('naigai-iezukuri-subpage'),
                    filemtime($abs)
                );
            }
        }
    }
}

/*
 * wp_enqueue_scripts だけだと、依存関係で main が復活する場合がある。
 * そのため wp_print_styles 直前でも同じ整理をかける。
 */
add_action('wp_enqueue_scripts', 'naigai_iezukuri_css_load_final_normalizer', 9999);
add_action('wp_print_styles', 'naigai_iezukuri_css_load_final_normalizer', 1);

/* === IEZUKURI TEMPLATE ROUTE SINGLE SOURCE START ===
 *
 * /iezukuri 系テンプレート制御の正本。
 *
 * 役割:
 * - /iezukuri/ はトップ専用 template-iezukuri.php を読む。
 * - /iezukuri/ 配下の固定ページは共通母体 template-iezukuri-subpage.php を読む。
 * - new-house / chuko / nisetai も専用テンプレートへ逃がさず、共通母体で読む。
 *
 * 注意:
 * - functions.php は触らない。
 * - base.css / ch-hero.css / hero.css は触らない。
 * - 本文の出し分けは template-iezukuri-subpage.php 側で slug 判定する。
 * ========================================================= */
add_filter('template_include', function ($template) {
    if (!is_page()) {
        return $template;
    }

    $page_id = get_queried_object_id();
    if (!$page_id) {
        return $template;
    }

    $uri = trim((string) get_page_uri($page_id), '/');

    if ($uri === 'iezukuri') {
        $file = get_template_directory() . '/hub/pages/iezukuri/templates/template-iezukuri.php';
        return file_exists($file) ? $file : $template;
    }

    if (strpos($uri, 'iezukuri/') === 0) {
        $file = get_template_directory() . '/hub/pages/iezukuri/templates/template-iezukuri-subpage.php';
        return file_exists($file) ? $file : $template;
    }

    return $template;
}, 999);
/* === IEZUKURI TEMPLATE ROUTE SINGLE SOURCE END === */

/* IEZUKURI COMMON FOOTER CSS ENQUEUE START */

/*
 * 家づくり共通footer CSS。
 * top/sub/plan/chuko/nisetai 全部この1本だけ。
 */
add_action('wp_enqueue_scripts', function () {
    $request_path = isset($_SERVER['REQUEST_URI'])
        ? trim((string) parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/')
        : '';

    $is_iezukuri = (
        $request_path === 'iezukuri'
        || strpos($request_path, 'iezukuri/') === 0
        || is_post_type_archive('iez_plan')
        || is_singular('iez_plan')
    );

    if (!$is_iezukuri) {
        return;
    }

    $rel = '/hub/pages/iezukuri/css/common/footer.css';
    $abs = get_template_directory() . $rel;

    wp_enqueue_style(
        'iezukuri-common-footer',
        get_template_directory_uri() . $rel,
        array(),
        file_exists($abs) ? filemtime($abs) : null
    );
}, 120);

/* IEZUKURI COMMON FOOTER CSS ENQUEUE END */

