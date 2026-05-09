<?php
/**
 * hub/pages/iezukuri/inc/plan-seo.php
 *
 * iez_plan SEO description
 *
 * 対象:
 * - /iezukuri/plans/
 * - /iezukuri/plan/{slug}/
 *
 * 役割:
 * - 画面に見えるH1や本文は出さない。
 * - <head> 内の meta description / og:description / twitter:description だけを出す。
 *
 * 詳細ページ:
 * - Gutenberg編集画面の本文 post_content をSEO説明文に使う。
 * - post_excerpt は使わない。
 * - _ch_plan_description は使わない。
 *
 * アーカイブ:
 * - /iezukuri/plans/ 用の固定SEO説明文を出す。
 *
 * 画面表示の管理場所:
 * - 詳細ページのH1・本文:
 *   hub/pages/iezukuri/templates/components/block-plan-viewer.php
 *
 * - アーカイブ画面のH1・本文:
 *   hub/pages/iezukuri/templates/components/block-plan-list.php
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * SEO description 用に文字列を整える。
 */
if (!function_exists('naigai_iez_plan_seo_trim')) {
    function naigai_iez_plan_seo_trim($text, $length = 130) {
        $text = wp_strip_all_tags((string) $text);
        $text = preg_replace('/\s+/u', ' ', $text);
        $text = trim($text);

        if ($text === '') {
            return '';
        }

        if (function_exists('mb_strlen') && mb_strlen($text, 'UTF-8') > $length) {
            return mb_substr($text, 0, $length, 'UTF-8') . '…';
        }

        if (!function_exists('mb_strlen') && strlen($text) > $length) {
            return substr($text, 0, $length) . '…';
        }

        return $text;
    }
}

/**
 * /iezukuri/plans/ アーカイブ用SEO説明文。
 *
 * これは画面表示ではなく、head内のmeta description用。
 */
if (!function_exists('naigai_iez_plan_archive_description')) {
    function naigai_iez_plan_archive_description() {
        return '那須での家づくり参考プラン一覧です。平屋、配置図、外観、間取り、二世帯住宅、三世代住宅、二拠点生活など、暮らし方に合わせた住まいの方向性を確認できます。';
    }
}

/**
 * /iezukuri/plan/{slug}/ 詳細ページ用SEO説明文。
 *
 * Gutenberg編集画面の本文 post_content を使う。
 */
if (!function_exists('naigai_iez_plan_single_description')) {
    function naigai_iez_plan_single_description($post_id) {
        $content = get_post_field('post_content', $post_id);
        $description = trim(wp_strip_all_tags(do_blocks($content)));

        if ($description !== '') {
            return naigai_iez_plan_seo_trim($description, 130);
        }

        return '那須での暮らし方に合わせた家づくりプランです。間取り、外観、配置図、平面図、暮らしやすさを確認できます。';
    }
}

/**
 * 現在のページ種別に応じてSEO metaを出す。
 */
if (!function_exists('naigai_iez_plan_output_seo_meta')) {
    function naigai_iez_plan_output_seo_meta() {
        if (is_post_type_archive('iez_plan')) {
            $description = naigai_iez_plan_archive_description();
        } elseif (is_singular('iez_plan')) {
            $description = naigai_iez_plan_single_description(get_queried_object_id());
        } else {
            return;
        }

        if ($description === '') {
            return;
        }

        echo "\n" . '<meta name="description" content="' . esc_attr($description) . '">' . "\n";
        echo '<meta property="og:description" content="' . esc_attr($description) . '">' . "\n";
        echo '<meta name="twitter:description" content="' . esc_attr($description) . '">' . "\n";
    }
}

add_action('wp_head', 'naigai_iez_plan_output_seo_meta', 1);
