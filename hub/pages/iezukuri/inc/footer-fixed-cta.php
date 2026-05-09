<?php
/**
 * hub/pages/iezukuri/inc/footer-fixed-cta.php
 *
 * iezukuri 配下の footer fixed CTA 差し替え
 *
 * 目的:
 * - 共通フッターの固定帯は、通常ページでは
 *   「資料請求 / 来店予約 / 売却査定依頼」を表示している。
 *
 * - ただし /iezukuri/ 配下では、不動産売却ではなく
 *   家づくり・中古住宅リフォーム相談へ誘導したい。
 *
 * 方針:
 * - WordPress管理画面のメニュー本体は変更しない。
 * - iezukuri 配下で表示される時だけ、既存の
 *   「売却査定依頼」メニュー項目を
 *   「リフォームの相談」へ差し替える。
 *
 * 注意:
 * - 不動産売買・査定・媒介の導線に見えないようにする。
 * - グループ不動産会社の売却相談とは分ける。
 * - 建築・リフォーム相談の導線として扱う。
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('naigai_iezukuri_is_footer_fixed_cta_context')) {
    /**
     * footer固定CTAを家づくり用へ切り替える対象ページか判定する。
     *
     * 対象:
     * - /iezukuri/
     * - /iezukuri/ 配下の固定ページ
     * - iez_plan 詳細ページ
     * - iez_plan アーカイブ
     */
    function naigai_iezukuri_is_footer_fixed_cta_context() {
        if (is_singular('iez_plan') || is_post_type_archive('iez_plan')) {
            return true;
        }

        if (!is_page()) {
            return false;
        }

        $post = get_post();

        if (!$post) {
            return false;
        }

        $template = get_page_template_slug($post);

        if (is_string($template) && strpos($template, 'iezukuri') !== false) {
            return true;
        }

        if ($post->post_name === 'iezukuri') {
            return true;
        }

        $ancestors = get_post_ancestors($post);

        foreach ($ancestors as $ancestor_id) {
            $ancestor = get_post($ancestor_id);

            if ($ancestor && $ancestor->post_name === 'iezukuri') {
                return true;
            }
        }

        return false;
    }
}

if (!function_exists('naigai_iezukuri_footer_fixed_contact_url')) {
    /**
     * リフォーム相談のリンク先。
     *
     * /iezukuri/contact/ があればそこへ。
     * なければ共通 /contact/ に逃がす。
     */
    function naigai_iezukuri_footer_fixed_contact_url() {
        $iez_contact = get_page_by_path('iezukuri/contact');

        if ($iez_contact) {
            return get_permalink($iez_contact);
        }

        return home_url('/contact/');
    }
}

if (!function_exists('naigai_iezukuri_replace_footer_fixed_menu_item')) {
    /**
     * footer固定メニューの「売却査定依頼」を
     * iezukuri 配下だけ「リフォームの相談」へ差し替える。
     *
     * 判定:
     * - menu item ID 528
     * - title に「売却査定」が含まれる
     * - URL に /satei が含まれる
     *
     * どれかに当たれば差し替える。
     */
    function naigai_iezukuri_replace_footer_fixed_menu_item($items, $args) {
        if (!naigai_iezukuri_is_footer_fixed_cta_context()) {
            return $items;
        }

        foreach ($items as $item) {
            $title = isset($item->title) ? wp_strip_all_tags((string) $item->title) : '';
            $url   = isset($item->url) ? (string) $item->url : '';

            $is_sale_assessment_item =
                (isset($item->ID) && (int) $item->ID === 528)
                || (strpos($title, '売却査定') !== false)
                || (strpos($url, '/satei') !== false);

            if (!$is_sale_assessment_item) {
                continue;
            }

            $item->title = 'リフォームの相談';
            $item->url   = naigai_iezukuri_footer_fixed_contact_url();

            if (empty($item->classes) || !is_array($item->classes)) {
                $item->classes = array();
            }

            $item->classes[] = 'footer-fixed__link--iezukuri-remodel';
        }

        return $items;
    }

    add_filter('wp_nav_menu_objects', 'naigai_iezukuri_replace_footer_fixed_menu_item', 20, 2);
}
