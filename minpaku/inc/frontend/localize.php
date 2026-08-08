<?php
/**
 * =========================================================
 * minpaku/inc/frontend/localize.php
 * =========================================================
 *
 * 民泊フロントJSへ PHP 側の値を渡すファイル。
 *
 * 役割:
 * - minpaku-single-js に mnpkBooking を渡す。
 * - admin-ajax.php のURLを渡す。
 * - 予約・決済用 nonce を渡す。
 * - Stripe publishable key を渡す。
 *
 * 読み込みの前提:
 * - minpaku/inc/enqueue.php が stripe-js / minpaku-single-js を読む。
 * - このファイルは、そのあとで mnpkBooking を localize する。
 *
 * 注意:
 * - JS本体はここで読まない。
 * - stripe-js / minpaku-single-js の enqueue は minpaku/inc/enqueue.php が担当する。
 * - common-core.php から切り出した処理。
 * =========================================================
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('mnpk_localize_front_script')) {
    function mnpk_localize_front_script()
    {
        $is_minpaku_single = is_singular('minpaku');
        $is_checkout = function_exists('mnpk_is_checkout_endpoint') && mnpk_is_checkout_endpoint();

        if (!$is_minpaku_single && !$is_checkout) {
            return;
        }

        /**
         * ここは、テーマ側ですでに enqueue 済みの
         * minpaku-single-js を前提にしている。
         */
        if (!wp_script_is('minpaku-single-js', 'enqueued')) {
            return;
        }

        wp_localize_script('minpaku-single-js', 'mnpkBooking', array(
            /**
             * Stripe決済の環境全体ON/OFF。
             * PHP側を正本にしてJSへ渡す。
             */
            'paymentEnabled' => (
                function_exists('mnpk_is_payment_feature_enabled')
                && mnpk_is_payment_feature_enabled()
                    ? 1
                    : 0
            ),
            /*
             * Stripe / 予約AJAXは現在表示中ページと
             * 必ず同一オリジンへ送る。
             *
             * localhost と 127.0.0.1 の混在によって
             * WordPressログインCookieが失われ、
             * nonce検証が403になることを防止する。
             */
            'ajaxUrl'        => wp_make_link_relative(
                admin_url('admin-ajax.php')
            ),
            'nonce'          => wp_create_nonce('mnpk_booking_nonce'),
            /* NAIGAI_LOCAL_STRIPE_PUBLISHABLE_FALLBACK */
            'publishableKey' => (
                defined('NAIGAI_STRIPE_PUBLISHABLE_KEY')
                && NAIGAI_STRIPE_PUBLISHABLE_KEY
                    ? (string) NAIGAI_STRIPE_PUBLISHABLE_KEY
                    : (string) get_option('mnpk_stripe_publishable_key', '')
            ),
        ));
    }
}
add_action('wp_enqueue_scripts', 'mnpk_localize_front_script', 100);
