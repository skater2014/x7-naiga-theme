<?php
if (!defined('ABSPATH')) exit;

if (!function_exists('naigai_minpaku_asset_ver')) {
    function naigai_minpaku_asset_ver($abs_path) {
        return file_exists($abs_path) ? (string) filemtime($abs_path) : null;
    }
}

if (!function_exists('naigai_minpaku_enqueue_assets')) {
    function naigai_minpaku_enqueue_assets() {
        $dir = get_template_directory();
        $uri = get_template_directory_uri();

        $is_b2c      = is_page_template('page-minpaku-b2c.php');
        $is_support  = is_page_template('page-minpaku-support.php') || is_page('minpaku');
        $is_archive  = is_post_type_archive('minpaku');
        $is_single   = is_singular('minpaku');
        $is_checkout = is_singular('minpaku') && (bool) get_query_var('checkout');

        $is_minpaku = $is_b2c || $is_support || $is_archive || $is_single || $is_checkout;
        if (!$is_minpaku) {
            return;
        }

        $base_rel = null;
        if (file_exists($dir . '/minpaku/common/css/minpaku-base.css')) {
            $base_rel = '/minpaku/common/css/minpaku-base.css';
        } else {
            $base_rel = '';
        }

        if ($base_rel) {
            wp_enqueue_style(
                'minpaku-css',
                $uri . $base_rel,
                array(),
                naigai_minpaku_asset_ver($dir . $base_rel)
            );
        }

        if (($is_b2c || $is_support) && file_exists($dir . '/minpaku/b2c/css/minpaku-b2c.css')) {
            wp_enqueue_style(
                'minpaku-b2c-css',
                $uri . '/minpaku/b2c/css/minpaku-b2c.css',
                array('minpaku-css'),
                naigai_minpaku_asset_ver($dir . '/minpaku/b2c/css/minpaku-b2c.css')
            );
        }

        if ($is_archive && file_exists($dir . '/minpaku/common/css/minpaku-archive.css')) {
            wp_enqueue_style(
                'minpaku-archive-css',
                $uri . '/minpaku/common/css/minpaku-archive.css',
                array('minpaku-css'),
                naigai_minpaku_asset_ver($dir . '/minpaku/common/css/minpaku-archive.css')
            );
        }

        if ($is_single && file_exists($dir . '/minpaku/common/css/minpaku-single.css')) {
            wp_enqueue_style(
                'minpaku-single-css',
                $uri . '/minpaku/common/css/minpaku-single.css',
                array('minpaku-css'),
                naigai_minpaku_asset_ver($dir . '/minpaku/common/css/minpaku-single.css')
            );
        }

        if (($is_single || $is_checkout || $is_archive) && file_exists($dir . '/minpaku/common/css/minpaku-booking.css')) {
            wp_enqueue_style(
                'minpaku-booking-css',
                $uri . '/minpaku/common/css/minpaku-booking.css',
                array('minpaku-css'),
                naigai_minpaku_asset_ver($dir . '/minpaku/common/css/minpaku-booking.css')
            );
        }

        if (($is_b2c || $is_support) && file_exists($dir . '/minpaku/b2c/js/minpaku-b2c.js')) {
            wp_enqueue_script(
                'minpaku-b2c-js',
                $uri . '/minpaku/b2c/js/minpaku-b2c.js',
                array(),
                naigai_minpaku_asset_ver($dir . '/minpaku/b2c/js/minpaku-b2c.js'),
                true
            );
        }

        if ($is_archive && file_exists($dir . '/minpaku/common/js/minpaku-archive-booking.js')) {
            wp_enqueue_script(
                'minpaku-archive-booking-js',
                $uri . '/minpaku/common/js/minpaku-archive-booking.js',
                array(),
                naigai_minpaku_asset_ver($dir . '/minpaku/common/js/minpaku-archive-booking.js'),
                true
            );
        }

        /*
         * MINPAKU_SINGLE_BOOKING_JS
         * ---------------------------------------------------------
         * 民泊詳細 / checkout 用 JS。
         *
         * 役割:
         * - Stripe.js を読む。
         * - minpaku/common/js/minpaku-single.js を読む。
         * - 予約カード / カレンダー / 決済モーダルの動きを担当する。
         *
         * 読み込み対象:
         * - 民泊詳細: is_singular('minpaku')
         * - checkout endpoint: mnpk_is_checkout_endpoint()
         *
         * 注意:
         * - mnpkBooking の wp_localize_script は common-core.php 側で行う。
         * - functions.php には民泊詳細JSを書かない。
         */
        $is_detail_or_checkout = $is_single || $is_checkout || (function_exists('mnpk_is_checkout_endpoint') && mnpk_is_checkout_endpoint());

        if ($is_detail_or_checkout) {
            wp_enqueue_script(
                'stripe-js',
                'https://js.stripe.com/v3/',
                array(),
                null,
                true
            );

            $single_js = '/minpaku/common/js/minpaku-single.js';
            if (file_exists($dir . $single_js)) {
                wp_enqueue_script(
                    'minpaku-single-js',
                    $uri . $single_js,
                    array(
                        'jquery',
                        'swiper-slider',
                        'pannellum',
                        'calendar-js',
                        'stripe-js',
                    ),
                    naigai_minpaku_asset_ver($dir . $single_js),
                    true
                );
            }
        }

        if (($is_b2c || $is_support || $is_archive) && file_exists($dir . '/minpaku/common/css/minpaku-footer-nav.css')) {
            wp_enqueue_style(
                'minpaku-footer-nav-css',
                $uri . '/minpaku/common/css/minpaku-footer-nav.css',
                array('minpaku-css'),
                naigai_minpaku_asset_ver($dir . '/minpaku/common/css/minpaku-footer-nav.css')
            );
        }
    }
    add_action('wp_enqueue_scripts', 'naigai_minpaku_enqueue_assets', 20);
}
