<?php

/**
 * =========================================================
 * single-minpaku-checkout.php
 * 民泊詳細URLの checkout endpoint 用テンプレート
 * =========================================================
 *
 * URL例:
 * /minpaku-stay/room/{slug}/checkout/
 *
 * このファイルの役割:
 * 1. 確認ページの見た目を出す
 * 2. JS が読む data-* を hidden 要素にまとめる
 * 3. 日付 / 人数変更モーダルを出す
 * 4. Stripe Payment Element を表示する
 *
 * 今回の重要修正:
 * - prefill を helper だけに頼らず、URLパラメータからも必ず拾う
 * - 対応ブランドの表示欄を追加
 * - 支払い成功メッセージの表示欄を追加
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header('77');

if (have_posts()) :
    while (have_posts()) :
        the_post();

        $post_id = get_the_ID();

        /**
         * ------------------------------------------------------------
         * 共通ヘルパー
         * ------------------------------------------------------------
         */
        $meta = static function ($key, $default = '') use ($post_id) {
            $value = get_post_meta($post_id, $key, true);
            return ($value === '' || $value === null) ? $default : $value;
        };

        $money = static function ($value) {
            return '¥' . number_format((float) $value);
        };

        /**
         * ------------------------------------------------------------
         * 宿泊料金まわり
         * ------------------------------------------------------------
         */
        $nightly_price   = (float) $meta('_mnpk_nightly_price', 0);
        $weekend_price   = (float) $meta('_mnpk_weekend_price', $nightly_price);
        $cleaning_fee    = (float) $meta('_mnpk_cleaning_fee', 0);
        $capacity        = max(1, (int) $meta('_mnpk_capacity', 4));
        $min_nights      = max(1, (int) $meta('_mnpk_min_nights', 1));
        $checkin_time    = trim((string) $meta('_mnpk_checkin_time', '15:00'));
        $checkout_time   = trim((string) $meta('_mnpk_checkout_time', '10:00'));
        $base_guests     = max(1, (int) $meta('_mnpk_base_guests', min(2, $capacity)));
        $extra_guest_fee = (float) $meta('_mnpk_extra_guest_fee', 0);

        /**
         * ------------------------------------------------------------
         * カレンダー情報
         * ------------------------------------------------------------
         */
        $calendar_payload = function_exists('mnpk_get_calendar_payload')
            ? mnpk_get_calendar_payload($post_id)
            : array(
                'open_start_date'      => '',
                'cleaning_buffer_days' => 0,
                'cleaning_note'        => '',
                'events'               => array(),
            );

        /**
         * ------------------------------------------------------------
         * ギャラリー画像
         * ------------------------------------------------------------
         */
        $gallery_images = array();

        for ($i = 1; $i <= 10; $i++) {
            $url = trim((string) $meta('_iframe_image_url_' . $i));
            if ($url !== '') {
                $gallery_images[] = array('url' => $url);
            }
        }

        if (empty($gallery_images) && has_post_thumbnail()) {
            $thumb_url = get_the_post_thumbnail_url($post_id, 'full');
            if ($thumb_url) {
                $gallery_images[] = array('url' => $thumb_url);
            }
        }

        $lead_image = !empty($gallery_images[0]['url'])
            ? $gallery_images[0]['url']
            : get_template_directory_uri() . '/images/noimage.gif';

        $detail_url = get_permalink($post_id);

        /**
         * ------------------------------------------------------------
         * prefill
         * ------------------------------------------------------------
         * 以前:
         * - helper が無い / 空だと checkout に日付が出ない
         *
         * 今回:
         * - まず URL の query から安全に拾う
         * - helper があれば上書きではなく merge する
         * - こうすることで、どちらか片方が空でも復元できる
         */
        $prefill_fallback = array(
            'checkIn'  => isset($_GET['checkin']) ? sanitize_text_field(wp_unslash($_GET['checkin'])) : '',
            'checkOut' => isset($_GET['checkout']) ? sanitize_text_field(wp_unslash($_GET['checkout'])) : '',
            'adults'   => isset($_GET['adults']) ? max(1, (int) $_GET['adults']) : 1,
            'children' => 0,
        );

        $prefill = $prefill_fallback;

        if (function_exists('mnpk_get_checkout_prefill_from_query')) {
            $prefill = wp_parse_args((array) mnpk_get_checkout_prefill_from_query(), $prefill_fallback);
        }

        /**
         * ------------------------------------------------------------
         * ユーザー向けの支払い方法表示
         * ------------------------------------------------------------
         * これは「画面に見せる案内用」
         * 実際の最終表示は Stripe の対応環境 / 設定にも依存する
         */
        $accepted_cards = array(
            'Visa',
            'Mastercard',
            'JCB',
            'American Express',
            'Discover',
            'Diners Club',
            'China UnionPay',
        );

        $accepted_wallets = array(
            'Apple Pay',
            'Google Pay',
        );
?>

        <main id="primary" class="site-main mnpk-single-page mnpk-checkout-page" data-mnpk-checkout-page>

            <!-- =========================================================
                 ページ上部
                 ========================================================= -->
            <section class="mnpk-checkout-page-head">
                <div class="mnpk-shell">
                    <div class="mnpk-back-wrap">
                        <a class="mnpk-back-link" href="<?php echo esc_url($detail_url); ?>" data-mnpk-history-back>← 前のページへ戻る</a>
                    </div>
                    <h1 class="mnpk-title">確認とお支払い</h1>
                </div>
            </section>

            <!-- =========================================================
                 メイン
                 左: 確認サマリー
                 右: 支払いフォーム
                 ========================================================= -->
            <section class="mnpk-checkout-page-main">
                <div class="mnpk-shell">
                    <div class="mnpk-checkout-layout mnpk-checkout-layout--page">

                        <!-- =====================================================
                             左: 宿泊内容の確認
                             ===================================================== -->
                        <aside class="mnpk-checkout-summary">
                            <div class="mnpk-checkout-stay-card">
                                <div class="mnpk-checkout-stay-card__media">
                                    <img
                                        src="<?php echo esc_url($lead_image); ?>"
                                        alt="<?php echo esc_attr(get_the_title()); ?>"
                                        data-checkout-thumb>
                                </div>

                                <div class="mnpk-checkout-stay-card__body">
                                    <p class="mnpk-checkout-stay-card__kicker">宿泊先</p>
                                    <h2 class="mnpk-checkout-stay-card__title mnpk-checkout-stay-card__title--page" data-checkout-stay-title>
                                        <?php the_title(); ?>
                                    </h2>
                                    <p class="mnpk-checkout-stay-card__meta" data-checkout-stay-meta>
                                        最大 <?php echo esc_html($capacity); ?>名 / 最低 <?php echo esc_html($min_nights); ?>泊
                                    </p>
                                </div>
                            </div>

                            <div class="mnpk-summary-box">
                                <div class="mnpk-checkout-row-head">
                                    <h3>日程</h3>
                                    <button type="button" class="mnpk-link-button" data-checkout-edit="dates">変更</button>
                                </div>
                                <p data-checkout-dates>未選択</p>
                            </div>

                            <div class="mnpk-summary-box">
                                <div class="mnpk-checkout-row-head">
                                    <h3>人数</h3>
                                    <button type="button" class="mnpk-link-button" data-checkout-edit="guests">変更</button>
                                </div>
                                <p data-checkout-guests>未選択</p>
                            </div>

                            <div class="mnpk-summary-box">
                                <h3>料金内訳</h3>
                                <ul class="mnpk-summary-list">
                                    <li><span>宿泊料金</span><strong data-checkout-room-fee>—</strong></li>
                                    <li><span>追加人数料金</span><strong data-checkout-guest-fee>—</strong></li>
                                    <li><span>清掃料金</span><strong data-checkout-cleaning-fee><?php echo esc_html($cleaning_fee > 0 ? $money($cleaning_fee) : '0円'); ?></strong></li>
                                    <li><span>合計</span><strong data-checkout-total>—</strong></li>
                                </ul>
                            </div>
                        </aside>

                        <!-- =====================================================
                             右: 支払いフォーム
                             ===================================================== -->
                        <section class="mnpk-checkout-payment">
                            <div class="mnpk-card mnpk-checkout-payment-card">

                                <!-- 支払い成功メッセージ -->
                                <div class="mnpk-payment-status" data-payment-status hidden></div>

                                <div class="mnpk-card__head">
                                    <h2 class="mnpk-section-title">確認とお支払い</h2>
                                    <p class="mnpk-checkout-payment-card__lead">
                                        お名前とメールアドレスを入力して、お支払い情報を入力してください。
                                    </p>
                                </div>

                                <!-- =============================================
                                     利用可能なお支払い方法
                                     ============================================= -->
                                <div class="mnpk-accepted-methods">
                                    <p class="mnpk-accepted-methods__label">ご利用いただけるお支払い方法</p>

                                    <div class="mnpk-payment-methods">
                                        <?php foreach ($accepted_cards as $brand) : ?>
                                            <span class="mnpk-payment-pill"><?php echo esc_html($brand); ?></span>
                                        <?php endforeach; ?>

                                        <?php foreach ($accepted_wallets as $wallet) : ?>
                                            <span class="mnpk-payment-pill mnpk-payment-pill--wallet"><?php echo esc_html($wallet); ?></span>
                                        <?php endforeach; ?>
                                    </div>

                                    <p class="mnpk-payment-methods-note">
                                        Apple Pay / Google Pay は、対応端末・対応ブラウザ・Stripe 側設定がそろった場合に表示されます。
                                    </p>
                                </div>

                                <!-- =============================================
                                     支払いフォーム
                                     ============================================= -->
                                <form id="mnpk-payment-form" class="mnpk-payment-form" novalidate>
                                    <div class="mnpk-form-grid">
                                        <label class="mnpk-form-field">
                                            <span>お名前</span>
                                            <input
                                                type="text"
                                                id="mnpk-payment-name"
                                                data-payment-name
                                                name="payment_name"
                                                autocomplete="name"
                                                required>
                                        </label>

                                        <label class="mnpk-form-field">
                                            <span>メールアドレス</span>
                                            <input
                                                type="email"
                                                id="mnpk-payment-email"
                                                data-payment-email
                                                name="payment_email"
                                                autocomplete="email"
                                                required>
                                        </label>
                                    </div>

                                    <div class="mnpk-summary-box mnpk-checkout-total-box">
                                        <h3>今回のお支払い</h3>
                                        <p><strong data-payment-total>—</strong></p>
                                    </div>

                                    <!--
                                      Stripe がここに Payment Element を描画する
                                      例: カード入力欄 / Apple Pay / Google Pay など
                                    -->
                                    <div class="mnpk-payment-element-wrap" data-payment-element-wrap>
    <div class="mnpk-payment-skeleton" data-payment-skeleton aria-hidden="true">
        <div class="mnpk-payment-skeleton__row mnpk-payment-skeleton__row--lg"></div>
        <div class="mnpk-payment-skeleton__row"></div>
        <div class="mnpk-payment-skeleton__row"></div>
        <div class="mnpk-payment-skeleton__row mnpk-payment-skeleton__row--sm"></div>
    </div>
    <div id="mnpk-payment-element" class="mnpk-payment-element"></div>
</div>

                                    <!-- 支払いエラー -->
                                    <div class="mnpk-booking-error" data-payment-error hidden></div>

                                    <div class="mnpk-checkout-page-actions">
                                        <a href="<?php echo esc_url($detail_url); ?>" class="mnpk-button mnpk-button--ghost" data-mnpk-history-back>前のページへ戻る</a>
                                        <button type="submit" class="mnpk-button" data-confirm-payment>支払いを確定する</button>
                                    </div>
                                </form>
                            </div>
                        </section>
                    </div>
                </div>
            </section>

            <!-- =========================================================
                 checkout 完了画面
                 ---------------------------------------------------------
                 初期状態では表示しない。

                 Stripe決済が succeeded の時だけ
                 minpaku-single.js が表示する。

                 サンクスHTML本体は、
                 お問い合わせと同じ共通PHP部品を利用する。

                 CV計測はこのHTMLとは別に
                 minpaku_purchase イベントで処理する。
                 ========================================================= -->
            <section
                id="mnpk-checkout-thanks"
                class="mnpk-checkout-thanks-step"
                hidden
            >
                <div class="mnpk-shell">
                    <?php
                    get_template_part(
                        'template-parts/common/thanks-state',
                        null,
                        array(
                            'variant'      => 'minpaku',
                            'heading_tag'  => 'h1',
                            'title'        => 'ご予約ありがとうございます',
                            'message'      => 'お支払いが完了しました。<br>ご予約を受け付けました。',
                            'button_label' => '宿泊施設の詳細へ戻る',
                            'button_url'   => $detail_url,
                            'button_type'  => 'link',
                            'button_class' => 'mnpk-button',
                        )
                    );
                    ?>
                </div>
            </section>

            <!-- =========================================================
                 JS 用 hidden data
                 ========================================================= -->
            <div
                id="mnpk-booking-card"
                class="mnpk-booking-card mnpk-booking-card--hidden-data"
                hidden
                data-post-id="<?php echo esc_attr($post_id); ?>"
                data-nightly-price="<?php echo esc_attr($nightly_price); ?>"
                data-weekend-price="<?php echo esc_attr($weekend_price); ?>"
                data-cleaning-fee="<?php echo esc_attr($cleaning_fee); ?>"
                data-capacity="<?php echo esc_attr($capacity); ?>"
                data-base-guests="<?php echo esc_attr($base_guests); ?>"
                data-extra-guest-fee="<?php echo esc_attr($extra_guest_fee); ?>"
                data-min-nights="<?php echo esc_attr($min_nights); ?>"
                data-checkin-time="<?php echo esc_attr($checkin_time); ?>"
                data-checkout-time="<?php echo esc_attr($checkout_time); ?>"
                data-open-start-date="<?php echo esc_attr($calendar_payload['open_start_date']); ?>"
                data-cleaning-buffer-days="<?php echo esc_attr($calendar_payload['cleaning_buffer_days']); ?>"
                data-cleaning-note="<?php echo esc_attr($calendar_payload['cleaning_note']); ?>"
                data-calendar-events="<?php echo esc_attr(wp_json_encode($calendar_payload['events'], JSON_UNESCAPED_UNICODE)); ?>"
                data-checkout-url="<?php echo esc_url(trailingslashit($detail_url) . 'checkout/'); ?>"
                data-detail-url="<?php echo esc_url($detail_url); ?>"
                data-lead-image="<?php echo esc_url($lead_image); ?>"
                data-stay-title="<?php echo esc_attr(get_the_title()); ?>"
                data-stay-meta="<?php echo esc_attr('最大 ' . $capacity . '名 / 最低 ' . $min_nights . '泊'); ?>"
                data-initial-checkin="<?php echo esc_attr($prefill['checkIn']); ?>"
                data-initial-checkout="<?php echo esc_attr($prefill['checkOut']); ?>"
                data-initial-adults="<?php echo esc_attr($prefill['adults']); ?>"
                data-initial-children="0">
            </div>

            <!--
              hidden input:
              JS 内部で現在値を同期するために置いている
            -->
            <input type="hidden" id="mnpk-checkin-input" value="<?php echo esc_attr($prefill['checkIn']); ?>">
            <input type="hidden" id="mnpk-checkout-input" value="<?php echo esc_attr($prefill['checkOut']); ?>">

            <!-- =========================================================
                 日付モーダル
                 ========================================================= -->
            <div class="mnpk-modal" id="mnpk-date-modal" aria-hidden="true">
                <div class="mnpk-modal__backdrop" data-close-modal></div>

                <div class="mnpk-modal__dialog mnpk-modal__dialog--calendar" role="dialog" aria-modal="true" aria-labelledby="mnpk-date-modal-title">
                    <button type="button" class="mnpk-modal__close" data-close-modal aria-label="閉じる">×</button>

                    <div class="mnpk-modal__header">
                        <h2 id="mnpk-date-modal-title">日付を選択</h2>
                        <span data-calendar-help>営業日として開いている日だけ選択できます。</span>
                    </div>

                    <div class="mnpk-calendar-toolbar">
                        <button type="button" class="mnpk-calendar-toolbar__button" data-calendar-prev>‹</button>
                        <button type="button" class="mnpk-calendar-toolbar__button" data-calendar-next>›</button>
                    </div>

                    <div class="mnpk-date-summary">
                        <div class="mnpk-date-summary__box">
                            <span>チェックイン</span>
                            <strong data-calendar-checkin-label>未選択</strong>
                        </div>
                        <div class="mnpk-date-summary__box">
                            <span>チェックアウト</span>
                            <strong data-calendar-checkout-label>未選択</strong>
                        </div>
                    </div>

                    <p class="mnpk-booking-error" data-calendar-error hidden></p>

                    <div class="mnpk-calendar-grid">
                        <div class="mnpk-calendar-month" data-calendar-month></div>
                        <div class="mnpk-calendar-month" data-calendar-month></div>
                    </div>

                    <div class="mnpk-modal__actions">
                        <button type="button" class="mnpk-button mnpk-button--ghost" data-close-modal>閉じる</button>
                        <button type="button" class="mnpk-button" data-apply-dates>この日程を使う</button>
                    </div>
                </div>
            </div>

            <!-- =========================================================
                 人数モーダル
                 ========================================================= -->
            <div class="mnpk-modal" id="mnpk-guest-modal" aria-hidden="true">
                <div class="mnpk-modal__backdrop" data-close-modal></div>

                <div class="mnpk-modal__dialog mnpk-modal__dialog--compact" role="dialog" aria-modal="true" aria-labelledby="mnpk-guest-modal-title">
                    <button type="button" class="mnpk-modal__close" data-close-modal aria-label="閉じる">×</button>

                    <div class="mnpk-modal__header">
                        <h2 id="mnpk-guest-modal-title">人数を選択</h2>
                    </div>

                    <div class="mnpk-modal__body">
                        <div class="mnpk-guest-rows">
                            <div class="mnpk-guest-row" data-guest-type="adults">
                                <div class="mnpk-guest-row__label">
                                    <strong>人数</strong>
                                    <span>合計人数を選択してください</span>
                                </div>

                                <div class="mnpk-counter">
                                    <button type="button" class="mnpk-counter__button" data-guest-action="minus">−</button>
                                    <span class="mnpk-counter__value" data-guest-value>1</span>
                                    <button type="button" class="mnpk-counter__button" data-guest-action="plus">＋</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <p class="mnpk-form-help" data-guest-help>合計 1名</p>
                </div>

                <div class="mnpk-modal__actions">
                    <button type="button" class="mnpk-button mnpk-button--ghost" data-close-modal>閉じる</button>
                    <button type="button" class="mnpk-button" data-apply-guests>この人数を使う</button>
                </div>
            </div>
            </div>
        </main>

<?php
    endwhile;
endif;

get_footer();
