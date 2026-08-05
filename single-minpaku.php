<?php

/**
 * =========================================================
 * single-minpaku.php
 * 民泊詳細ページ
 * =========================================================
 *
 * このファイルの役割:
 * 1. 民泊詳細ページ本体を表示する
 * 2. 右側の予約カードを表示する
 * 3. JS が使う data-* を #mnpk-booking-card にまとめる
 * 4. モバイル用の支払いモーダル・下部予約バーを読み込む
 *
 * 重要:
 * - PC / タブレット:
 *   右予約カードの「確認とお支払いへ」→ /checkout/ 専用ページへ遷移
 * - モバイル:
 *   下部予約バーの「予約」→ 詳細ページ内モーダルを開く
 *
 * 注意:
 * - /checkout/ テンプレート自体を読み込むのはこのファイルではない
 * - /checkout/ のテンプレート切り替えは
 *   functions-minpaku-checkout-endpoint.php 側の役割
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

        $is_true = static function ($value) {
            return in_array(
                strtolower(trim((string) $value)),
                array('1', 'true', 'yes', 'on', 'available', 'あり', '有', '○', '〇'),
                true
            );
        };

        $money = static function ($value) {
            return '¥' . number_format((float) $value);
        };

        $value_or_dash = static function ($value, $suffix = '') {
            if ($value === '' || $value === null) {
                return '—';
            }

            if (is_numeric($value) && $suffix !== '') {
                return $value . $suffix;
            }

            return $value;
        };

        /**
         * ------------------------------------------------------------
         * 画像取得
         * 既存メタ:
         * _iframe_image_url_1..10
         * _iframe_image_title_1..10
         * _iframe_image_text_1..10
         * ------------------------------------------------------------
         */
        $gallery_images = array();

        for ($i = 1; $i <= 10; $i++) {
            $url   = trim((string) $meta('_iframe_image_url_' . $i));
            $title = trim((string) $meta('_iframe_image_title_' . $i));
            $text  = trim((string) $meta('_iframe_image_text_' . $i));

            if ($url !== '') {
                $gallery_images[] = array(
                    'url'   => $url,
                    'title' => $title,
                    'text'  => $text,
                );
            }
        }

        /**
         * 画像が1枚も無いときはアイキャッチを代替に使う
         */
        if (empty($gallery_images) && has_post_thumbnail()) {
            $thumb_url = get_the_post_thumbnail_url($post_id, 'full');
            if ($thumb_url) {
                $gallery_images[] = array(
                    'url'   => $thumb_url,
                    'title' => get_the_title(),
                    'text'  => '',
                );
            }
        }

        /**
         * ------------------------------------------------------------
         * パノラマ取得
         * 既存メタ:
         * _iframe_panorama_image_url_1..10
         * _iframe_panorama_image_title_1..10
         * _iframe_panorama_image_text_1..10
         * ------------------------------------------------------------
         */
        $panorama_items = array();

        for ($i = 1; $i <= 10; $i++) {
            $url   = trim((string) $meta('_iframe_panorama_image_url_' . $i));
            $title = trim((string) $meta('_iframe_panorama_image_title_' . $i));
            $text  = trim((string) $meta('_iframe_panorama_image_text_' . $i));

            if ($url !== '') {
                $panorama_items[] = array(
                    'url'   => $url,
                    'title' => $title !== '' ? $title : 'パノラマ ' . $i,
                    'text'  => $text,
                );
            }
        }

        /**
         * ------------------------------------------------------------
         * 民泊メタ
         * ------------------------------------------------------------
         */
        $nightly_price    = (float) $meta('_mnpk_nightly_price', 0);
        $weekend_price    = (float) $meta('_mnpk_weekend_price', $nightly_price);
        $cleaning_fee     = (float) $meta('_mnpk_cleaning_fee', 0);
        $capacity         = max(1, (int) $meta('_mnpk_capacity', 4));
        $bedrooms         = (int) $meta('_mnpk_bedrooms', 0);
        $beds             = (int) $meta('_mnpk_beds', 0);
        $bath             = trim((string) $meta('_mnpk_bath', ''));
        $toilet           = trim((string) $meta('_mnpk_toilet', ''));
        $min_nights       = max(1, (int) $meta('_mnpk_min_nights', 1));
        $checkin_time     = trim((string) $meta('_mnpk_checkin_time', '15:00'));
        $checkout_time    = trim((string) $meta('_mnpk_checkout_time', '10:00'));
        $cancel_policy    = trim((string) $meta('_mnpk_cancel_policy', ''));
        $payment_note     = trim((string) $meta('_mnpk_payment_note', ''));
        $booking_note     = trim((string) $meta('_mnpk_booking_note', ''));
        $amenities_raw    = trim((string) $meta('_mnpk_amenities', ''));

        /**
         * カレンダー payload
         * - 営業開始日
         * - 清掃バッファ
         * - 空き状況イベント
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
         * 追加人数料金用
         */
        $base_guests     = max(1, (int) $meta('_mnpk_base_guests', min(2, $capacity)));
        $extra_guest_fee = (float) $meta('_mnpk_extra_guest_fee', 0);

        if ($weekend_price <= 0) {
            $weekend_price = $nightly_price;
        }

        if ($base_guests > $capacity) {
            $base_guests = $capacity;
        }

        /**
         * ------------------------------------------------------------
         * 設備・アメニティ整理
         * ------------------------------------------------------------
         */
        $amenity_flags = array(
            '_mnpk_wifi'    => 'Wi-Fi',
            '_mnpk_parking' => '駐車場',
            '_mnpk_kitchen' => 'キッチン',
            '_mnpk_aircon'  => 'エアコン',
            '_mnpk_washer'  => '洗濯機',
        );

        $policy_flags = array(
            '_mnpk_smoking'  => '喫煙可',
            '_mnpk_pet'      => 'ペット可',
            '_mnpk_children' => '子ども可',
        );

        $amenities = array();

        foreach ($amenity_flags as $key => $label) {
            if ($is_true($meta($key))) {
                $amenities[] = $label;
            }
        }

        foreach ($policy_flags as $key => $label) {
            if ($is_true($meta($key))) {
                $amenities[] = $label;
            }
        }

        if ($amenities_raw !== '') {
            $custom_amenities = preg_split('/\r\n|\r|\n|、|,/', $amenities_raw);
            if (!empty($custom_amenities)) {
                foreach ($custom_amenities as $item) {
                    $item = trim((string) $item);
                    if ($item !== '') {
                        $amenities[] = $item;
                    }
                }
            }
        }

        $amenities = array_values(array_unique($amenities));

        /**
         * ------------------------------------------------------------
         * 支払い方法の表示用配列
         * ------------------------------------------------------------
         * ここは「ユーザー向けの案内表示」
         * 実際の Apple Pay / Google Pay の最終表示は
         * 端末・ブラウザ・Stripe 設定に依存する
         */
        $payment_card_methods = array(
            'Visa',
            'Mastercard',
            'JCB',
            'American Express',
            'Discover',
            'Diners Club',
            'China UnionPay',
        );

        $payment_wallet_methods = array(
            'Apple Pay',
            'Google Pay',
        );

        /**
         * ------------------------------------------------------------
         * 概要テーブル
         * ------------------------------------------------------------
         */
        $overview_items = array();

        $overview_items[] = array('label' => '定員', 'value' => $capacity . '名');

        if ($bedrooms > 0) {
            $overview_items[] = array('label' => '寝室', 'value' => $bedrooms . '室');
        }

        if ($beds > 0) {
            $overview_items[] = array('label' => 'ベッド', 'value' => $beds . '台');
        }

        if ($bath !== '') {
            $overview_items[] = array(
                'label' => '浴室',
                'value' => $value_or_dash($bath, is_numeric($bath) ? 'か所' : '')
            );
        }

        if ($toilet !== '') {
            $overview_items[] = array(
                'label' => 'トイレ',
                'value' => $value_or_dash($toilet, is_numeric($toilet) ? 'か所' : '')
            );
        }

        $overview_items[] = array('label' => '最低宿泊日数', 'value' => $min_nights . '泊');
        $overview_items[] = array('label' => 'Wi-Fi', 'value' => $is_true($meta('_mnpk_wifi')) ? 'あり' : 'なし');
        $overview_items[] = array('label' => '駐車場', 'value' => $is_true($meta('_mnpk_parking')) ? 'あり' : 'なし');
        $overview_items[] = array('label' => 'キッチン', 'value' => $is_true($meta('_mnpk_kitchen')) ? 'あり' : 'なし');
        $overview_items[] = array('label' => 'ペット', 'value' => $is_true($meta('_mnpk_pet')) ? '可' : '不可');
        $overview_items[] = array('label' => '喫煙', 'value' => $is_true($meta('_mnpk_smoking')) ? '可' : '不可');

        /**
         * ------------------------------------------------------------
         * ギャラリー表示クラス
         * ------------------------------------------------------------
         */
        $gallery_total         = count($gallery_images);
        $desktop_preview_count = min($gallery_total, 6);
        $desktop_preview       = array_slice($gallery_images, 0, $desktop_preview_count);

        $gallery_class = 'mnpk-gallery-grid--' . max(1, $desktop_preview_count);
        if ($gallery_total > 6) {
            $gallery_class .= ' mnpk-gallery-grid--more';
        }

        /**
         * ------------------------------------------------------------
         * タイトル下の補助情報
         * ------------------------------------------------------------
         */
        $meta_line_parts   = array();
        $meta_line_parts[] = '最大' . $capacity . '名';

        if ($bedrooms > 0) {
            $meta_line_parts[] = '寝室 ' . $bedrooms . '室';
        }

        if ($beds > 0) {
            $meta_line_parts[] = 'ベッド ' . $beds . '台';
        }

        $meta_line_parts[] = '最低 ' . $min_nights . '泊';

        $lead_text = '';
        if (has_excerpt()) {
            $lead_text = get_the_excerpt();
        }

        /**
         * ------------------------------------------------------------
         * checkout ページへ渡す補助データ
         * ------------------------------------------------------------
         */
        $checkout_url = trailingslashit(get_permalink($post_id)) . 'checkout/';
        $detail_url   = get_permalink($post_id);

        $lead_image_url = '';
        if (!empty($gallery_images) && !empty($gallery_images[0]['url'])) {
            $lead_image_url = $gallery_images[0]['url'];
        } elseif (has_post_thumbnail()) {
            $lead_image_url = get_the_post_thumbnail_url($post_id, 'large');
        }

        /**
         * 詳細 → checkout に遷移した時に復元する初期値
         */
        $initial_checkin  = isset($_GET['checkin']) ? sanitize_text_field(wp_unslash($_GET['checkin'])) : '';
        $initial_checkout = isset($_GET['checkout']) ? sanitize_text_field(wp_unslash($_GET['checkout'])) : '';
        $initial_adults   = isset($_GET['adults']) ? max(1, (int) $_GET['adults']) : 1;
        $initial_children = 0;

        $stay_meta_text = !empty($meta_line_parts) ? implode(' / ', $meta_line_parts) : '';
?>

        <main id="primary" class="site-main mnpk-single-page">
            <article id="post-<?php the_ID(); ?>" <?php post_class('mnpk-single-article'); ?>>

                <!-- =========================================================
                     タイトルエリア
                     ========================================================= -->
                <section class="mnpk-head">
                    <div class="mnpk-shell">
                        <div class="mnpk-head__row">
                            <div class="mnpk-head__main">
                                <?php
                                /*
                                 * =====================================================
                                 * NAIGAI_MINPAKU_SINGLE_BACKLINK_BEGINNER_GUIDE
                                 * 民泊詳細「前のページに戻る」
                                 *
                                 * 【初心者向け】
                                 * 民泊詳細にも以前からback linkがありました。
                                 *
                                 * そのHTMLを別にもう1個追加すると二重表示になるため、
                                 * 現在は共通back link部品をこの位置から呼び出します。
                                 *
                                 * 表示位置と既存CSSは維持しています。
                                 * =====================================================
                                 *
                                 * 【既存back linkを維持】
                                 *
                                 * この位置には以前から
                                 * .mnpk-back-wrap / .mnpk-back-link が存在していた。
                                 *
                                 * 新しいback linkを追加して二重表示にせず、
                                 * 表示位置・デザインはそのままで、
                                 * 判定処理だけ民泊共通部品へ一本化する。
                                 *
                                 * サイト内から来た場合だけ表示。
                                 *
                                 * URL直接入力・ブックマーク・外部サイトから
                                 * 開いた場合には表示しない。
                                 * =====================================================
                                 */
                                get_template_part(
                                    'template-parts/common/minpaku-internal-back-link'
                                );
                                ?>

                                <h1 class="mnpk-title"><?php the_title(); ?></h1>

                                <?php if (!empty($meta_line_parts)) : ?>
                                    <div class="mnpk-meta-line">
                                        <?php echo esc_html(implode(' / ', $meta_line_parts)); ?>
                                    </div>
                                <?php endif; ?>

                                <?php if ($lead_text !== '') : ?>
                                    <p class="mnpk-lead">
                                        <?php echo esc_html($lead_text); ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- =========================================================
                     画像ギャラリー
                     ========================================================= -->
                <section class="mnpk-hero">
                    <div class="mnpk-hero__inner">

                        <?php if (!empty($gallery_images)) : ?>
                            <div class="mnpk-gallery-desktop">
                                <div class="mnpk-gallery-toolbar">
                                    <button type="button" class="mnpk-gallery-toolbar__button" data-open-gallery data-gallery-index="0">
                                        すべての写真を見る
                                    </button>
                                </div>

                                <div class="mnpk-gallery-grid <?php echo esc_attr($gallery_class); ?>">
                                    <?php foreach ($desktop_preview as $index => $image) : ?>
                                        <button
                                            type="button"
                                            class="mnpk-gallery-item"
                                            data-open-gallery
                                            data-gallery-index="<?php echo esc_attr($index); ?>"
                                            aria-label="<?php echo esc_attr(($index + 1) . '枚目の写真を開く'); ?>">
                                            <img
                                                src="<?php echo esc_url($image['url']); ?>"
                                                alt="<?php echo esc_attr($image['title'] !== '' ? $image['title'] : get_the_title()); ?>"
                                                loading="<?php echo $index === 0 ? 'eager' : 'lazy'; ?>">

                                            <?php if ($gallery_total > 6 && $index === 5) : ?>
                                                <span class="mnpk-gallery-count-badge">
                                                    +<?php echo esc_html($gallery_total - 6); ?>枚
                                                </span>
                                            <?php endif; ?>
                                        </button>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <div class="mnpk-gallery-mobile">
                                <div class="swiper mnpk-gallery-swiper">
                                    <div class="swiper-wrapper">
                                        <?php foreach ($gallery_images as $index => $image) : ?>
                                            <div class="swiper-slide">
                                                <button
                                                    type="button"
                                                    class="mnpk-gallery-mobile__button"
                                                    data-open-gallery
                                                    data-gallery-index="<?php echo esc_attr($index); ?>"
                                                    aria-label="<?php echo esc_attr(($index + 1) . '枚目の写真を開く'); ?>">
                                                    <img
                                                        src="<?php echo esc_url($image['url']); ?>"
                                                        alt="<?php echo esc_attr($image['title'] !== '' ? $image['title'] : get_the_title()); ?>"
                                                        loading="<?php echo $index === 0 ? 'eager' : 'lazy'; ?>">
                                                </button>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>

                                    <div class="swiper-pagination mnpk-gallery-swiper__pagination"></div>
                                </div>

                                <button type="button" class="mnpk-gallery-mobile__all" data-open-gallery data-gallery-index="0">
                                    すべての写真を見る
                                </button>
                            </div>
                        <?php else : ?>
                            <div class="mnpk-gallery-empty">
                                <p>画像がまだ登録されていません。</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </section>

                <!-- =========================================================
                     本文 + 右予約カード
                     ========================================================= -->
                <section class="mnpk-main">
                    <div class="mnpk-shell mnpk-main-grid">

                        <!-- 左コンテンツ -->
                        <div class="mnpk-content">

                            <section class="mnpk-card mnpk-summary-card">
                                <header class="mnpk-section-head">
                                    <span class="mnpk-section-kicker">OVERVIEW</span>
                                    <h2 class="mnpk-section-title">宿泊概要</h2>
                                </header>

                                <div class="mnpk-summary-chips">
                                    <span class="mnpk-chip">最大 <?php echo esc_html($capacity); ?>名</span>

                                    <?php if ($bedrooms > 0) : ?>
                                        <span class="mnpk-chip">寝室 <?php echo esc_html($bedrooms); ?>室</span>
                                    <?php endif; ?>

                                    <?php if ($beds > 0) : ?>
                                        <span class="mnpk-chip">ベッド <?php echo esc_html($beds); ?>台</span>
                                    <?php endif; ?>

                                    <span class="mnpk-chip">最低 <?php echo esc_html($min_nights); ?>泊</span>
                                </div>

                                <dl class="mnpk-overview-grid">
                                    <?php foreach ($overview_items as $item) : ?>
                                        <div class="mnpk-overview-grid__item">
                                            <dt><?php echo esc_html($item['label']); ?></dt>
                                            <dd><?php echo esc_html($item['value']); ?></dd>
                                        </div>
                                    <?php endforeach; ?>
                                </dl>
                            </section>

                            <section class="mnpk-card">
                                <header class="mnpk-section-head">
                                    <span class="mnpk-section-kicker">ROOM</span>
                                    <h2 class="mnpk-section-title">お部屋について</h2>
                                </header>

                                <div class="mnpk-content-body">
                                    <?php the_content(); ?>
                                </div>
                            </section>

                            <?php if (!empty($amenities)) : ?>
                                <section class="mnpk-card">
                                    <header class="mnpk-section-head">
                                    <span class="mnpk-section-kicker">AMENITIES</span>
                                    <h2 class="mnpk-section-title">設備・アメニティ</h2>
                                </header>

                                    <div class="mnpk-amenities">
                                        <?php foreach ($amenities as $amenity) : ?>
                                            <span class="mnpk-amenity"><?php echo esc_html($amenity); ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                </section>
                            <?php endif; ?>

                            <?php if (!empty($panorama_items)) : ?>
                                <section class="mnpk-card">
                                    <header class="mnpk-section-head">
                                    <span class="mnpk-section-kicker">PANORAMA</span>
                                    <h2 class="mnpk-section-title">パノラマビュー</h2>
                                </header>

                                    <div class="mnpk-panorama">
                                        <div class="mnpk-panorama__viewer-wrap">
                                            <div id="mnpk-panorama-viewer" class="mnpk-panorama__viewer"></div>

                                            <div class="mnpk-panorama__meta">
                                                <h3 class="mnpk-panorama__title" data-panorama-title>
                                                    <?php echo esc_html($panorama_items[0]['title']); ?>
                                                </h3>

                                                <p class="mnpk-panorama__text" data-panorama-text>
                                                    <?php echo esc_html($panorama_items[0]['text']); ?>
                                                </p>
                                            </div>
                                        </div>

                                        <div class="mnpk-panorama__thumbs">
                                            <?php foreach ($panorama_items as $index => $item) : ?>
                                                <button
                                                    type="button"
                                                    class="mnpk-panorama-thumb<?php echo $index === 0 ? ' is-active' : ''; ?>"
                                                    data-panorama-index="<?php echo esc_attr($index); ?>"
                                                    data-panorama-url="<?php echo esc_url($item['url']); ?>"
                                                    data-panorama-title="<?php echo esc_attr($item['title']); ?>"
                                                    data-panorama-text="<?php echo esc_attr($item['text']); ?>">
                                                    <?php echo esc_html($item['title']); ?>
                                                </button>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </section>
                            <?php endif; ?>

                            <?php if ($cancel_policy !== '' || $payment_note !== '' || $booking_note !== '') : ?>
                                <section class="mnpk-card">
                                    <header class="mnpk-section-head">
                                    <span class="mnpk-section-kicker">NOTES</span>
                                    <h2 class="mnpk-section-title">ご予約前にご確認ください</h2>
                                </header>

                                    <div class="mnpk-notes">
                                        <?php if ($cancel_policy !== '') : ?>
                                            <div class="mnpk-note-block">
                                                <h3>キャンセルポリシー</h3>
                                                <p><?php echo nl2br(esc_html($cancel_policy)); ?></p>
                                            </div>
                                        <?php endif; ?>

                                        <?php if ($payment_note !== '') : ?>
                                            <div class="mnpk-note-block">
                                                <h3>お支払いについて</h3>
                                                <p><?php echo nl2br(esc_html($payment_note)); ?></p>
                                            </div>
                                        <?php endif; ?>

                                        <?php if ($booking_note !== '') : ?>
                                            <div class="mnpk-note-block">
                                                <h3>ご予約メモ</h3>
                                                <p><?php echo nl2br(esc_html($booking_note)); ?></p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </section>
                            <?php endif; ?>
                        </div>

                        <!-- 右コンテンツ: 予約カード -->
                        <aside class="mnpk-sidebar">
                            <div
                                id="mnpk-booking-card"
                                class="mnpk-booking-card"
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
                                data-checkout-url="<?php echo esc_url($checkout_url); ?>"
                                data-detail-url="<?php echo esc_url($detail_url); ?>"
                                data-lead-image="<?php echo esc_url($lead_image_url); ?>"
                                data-stay-title="<?php echo esc_attr(get_the_title()); ?>"
                                data-stay-meta="<?php echo esc_attr($stay_meta_text); ?>"
                                data-initial-checkin="<?php echo esc_attr($initial_checkin); ?>"
                                data-initial-checkout="<?php echo esc_attr($initial_checkout); ?>"
                                data-initial-adults="<?php echo esc_attr($initial_adults); ?>"
                                data-initial-children="0">

                                <!-- 上部価格 -->
                                <div class="mnpk-booking-card__top">
                                    <div class="mnpk-booking-price">
                                        <strong><?php echo esc_html($money($nightly_price)); ?></strong>
                                        <span>/ 泊〜</span>
                                    </div>

                                    <?php if ($weekend_price > 0 && $weekend_price !== $nightly_price) : ?>
                                        <div class="mnpk-booking-subprice">
                                            週末料金 <?php echo esc_html($money($weekend_price)); ?> / 泊
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- 日付・人数 -->
                                <div class="mnpk-booking-fields">
                                    <button type="button" class="mnpk-field-button mnpk-field-button--dates" data-open-dates>
                                        <span class="mnpk-field-button__label">日程</span>
                                        <strong class="mnpk-field-button__value" data-selection-dates>日付を選択</strong>
                                    </button>

                                    <button type="button" class="mnpk-field-button mnpk-field-button--guests" data-open-guests>
                                        <span class="mnpk-field-button__label">人数</span>
                                        <strong class="mnpk-field-button__value" data-selection-guests>1名</strong>
                                    </button>
                                </div>

                                <!-- 条件 -->
                                <div class="mnpk-booking-meta">
                                    <span>最低 <?php echo esc_html($min_nights); ?>泊</span>
                                    <span>最大 <?php echo esc_html($capacity); ?>名</span>
                                </div>

                                <!-- 金額内訳 -->
                                <div class="mnpk-price-breakdown">
                                    <div class="mnpk-price-row">
                                        <span>宿泊料金</span>
                                        <strong data-price-room>—</strong>
                                    </div>

                                    <div class="mnpk-price-row">
                                        <span>追加人数料金</span>
                                        <strong data-price-guest>0円</strong>
                                    </div>

                                    <div class="mnpk-price-row">
                                        <span>清掃料金</span>
                                        <strong data-price-cleaning><?php echo esc_html($cleaning_fee > 0 ? $money($cleaning_fee) : '0円'); ?></strong>
                                    </div>

                                    <div class="mnpk-price-row mnpk-price-row--total">
                                        <span>合計</span>
                                        <strong data-price-total>—</strong>
                                    </div>
                                </div>

                                <!-- エラー表示 -->
                                <p class="mnpk-booking-error" data-booking-error hidden></p>

                                <!-- 予約ボタン -->
                                <button type="button" class="mnpk-booking-submit" data-booking-submit>
                                    確認とお支払いへ
                                </button>

                                <!-- 支払い方法の案内 -->
                                <div class="mnpk-accepted-methods" style="margin-top: 14px;">
                                    <h3 class="mnpk-accepted-methods__label">ご利用いただけるお支払い方法</h3>

                                    <div class="mnpk-payment-methods">
                                        <?php foreach ($payment_card_methods as $brand) : ?>
                                            <span class="mnpk-payment-pill"><?php echo esc_html($brand); ?></span>
                                        <?php endforeach; ?>

                                        <?php foreach ($payment_wallet_methods as $wallet) : ?>
                                            <span class="mnpk-payment-pill mnpk-payment-pill--wallet">
                                                <?php echo esc_html($wallet); ?>
                                            </span>
                                        <?php endforeach; ?>
                                    </div>

                                    <p class="mnpk-payment-methods-note">
                                        Apple Pay / Google Pay は、対応端末・対応ブラウザ・Stripe 側設定がそろった場合に表示されます。
                                    </p>
                                </div>

                                <!-- 清掃料金の注記 -->
                                <?php if ($cleaning_fee > 0) : ?>
                                    <p class="mnpk-booking-note">
                                        清掃料金 <?php echo esc_html($money($cleaning_fee)); ?> が別途かかります。
                                    </p>
                                <?php endif; ?>

                                <!-- 任意の予約メモ -->
                                <?php if ($booking_note !== '') : ?>
                                    <p class="mnpk-booking-note">
                                        <?php echo nl2br(esc_html($booking_note)); ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </aside>
                    </div>
                </section>

                <!-- =========================================================
                     写真モーダル
                     ========================================================= -->
                <?php if (!empty($gallery_images)) : ?>
                    <div class="mnpk-modal" id="mnpk-photo-modal" aria-hidden="true">
                        <div class="mnpk-modal__backdrop" data-close-modal></div>

                        <div class="mnpk-modal__dialog mnpk-modal__dialog--photo" role="dialog" aria-modal="true" aria-labelledby="mnpk-photo-modal-title">
                            <button type="button" class="mnpk-modal__close" data-close-modal aria-label="閉じる">×</button>

                            <div class="mnpk-modal__header">
                                <h2 id="mnpk-photo-modal-title">写真一覧</h2>
                                <span><?php echo esc_html($gallery_total); ?>枚</span>
                            </div>

                            <div class="swiper mnpk-photo-modal-swiper">
                                <div class="swiper-wrapper">
                                    <?php foreach ($gallery_images as $image) : ?>
                                        <div class="swiper-slide">
                                            <div class="mnpk-photo-slide">
                                                <img
                                                    src="<?php echo esc_url($image['url']); ?>"
                                                    alt="<?php echo esc_attr($image['title'] !== '' ? $image['title'] : get_the_title()); ?>"
                                                    loading="lazy">

                                                <?php if ($image['title'] !== '' || $image['text'] !== '') : ?>
                                                    <div class="mnpk-photo-slide__caption">
                                                        <?php if ($image['title'] !== '') : ?>
                                                            <h3><?php echo esc_html($image['title']); ?></h3>
                                                        <?php endif; ?>

                                                        <?php if ($image['text'] !== '') : ?>
                                                            <p><?php echo esc_html($image['text']); ?></p>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                                <div class="swiper-button-prev mnpk-photo-prev"></div>
                                <div class="swiper-button-next mnpk-photo-next"></div>
                                <div class="swiper-pagination mnpk-photo-pagination"></div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- =========================================================
                     日付モーダル
                     ========================================================= -->
                <div class="mnpk-modal" id="mnpk-date-modal" aria-hidden="true">
                    <div class="mnpk-modal__backdrop" data-close-modal></div>

                    <div class="mnpk-modal__dialog mnpk-modal__dialog--calendar" role="dialog" aria-modal="true" aria-labelledby="mnpk-date-modal-title">
                        <button type="button" class="mnpk-modal__close" data-close-modal aria-label="閉じる">×</button>

                        <div class="mnpk-modal__header">
                            <h2 id="mnpk-date-modal-title">日付を選択</h2>
                            <span>最低 <?php echo esc_html($min_nights); ?>泊</span>
                        </div>

                        <div class="mnpk-modal__body">
                        
<div class="mnpk-date-summary" aria-label="日付の入力先を選択">
    <button type="button"
        class="mnpk-date-summary__box mnpk-date-summary__button is-active"
        data-date-field="checkin"
        aria-pressed="true">
        <span>チェックイン</span>
        <strong data-calendar-checkin-label>未選択</strong>
    </button>

    <button type="button"
        class="mnpk-date-summary__box mnpk-date-summary__button"
        data-date-field="checkout"
        aria-pressed="false">
        <span>チェックアウト</span>
        <strong data-calendar-checkout-label>未選択</strong>
    </button>
</div>

<div class="mnpk-date-direct" aria-label="日付を直接入力">
    <label class="mnpk-date-direct__field">
        <span>チェックインを直接選択</span>
        <input type="date" data-direct-checkin>
    </label>

    <label class="mnpk-date-direct__field">
        <span>チェックアウトを直接選択</span>
        <input type="date" data-direct-checkout>
    </label>
</div>

<div class="mnpk-calendar-toolbar">

                            <button type="button" class="mnpk-calendar-toolbar__button" data-calendar-prev>‹</button>
                            <button type="button" class="mnpk-calendar-toolbar__button" data-calendar-next>›</button>
                        </div>

                        <div class="mnpk-calendar-legend">
                            <span><i class="mnpk-calendar-mark mnpk-calendar-mark--available"></i> 空き</span>
                            <span><i class="mnpk-calendar-mark mnpk-calendar-mark--reserved">×</i> 予約済み</span>
                            <span><i class="mnpk-calendar-mark mnpk-calendar-mark--cleaning">清</i> 清掃</span>
                            <span><i class="mnpk-calendar-mark mnpk-calendar-mark--blocked">—</i> 停止 / 営業前</span>
                        </div>

                        <div class="mnpk-calendar-grid">
                            <div class="mnpk-calendar-month" data-calendar-month="0"></div>
                            <div class="mnpk-calendar-month" data-calendar-month="1"></div>
                        </div>

                        <input type="hidden" id="mnpk-checkin-input" name="checkin" value="">
                        <input type="hidden" id="mnpk-checkout-input" name="checkout" value="">

                        <p class="mnpk-form-help" data-calendar-help>
                            営業開始日以前は選択できません。
                        </p>

                        <p class="mnpk-booking-error" data-calendar-error hidden></p>

                        </div>

                        <div class="mnpk-modal__actions">
                            <button type="button" class="mnpk-button mnpk-button--ghost" data-calendar-clear>選択をクリア</button>
                            <button type="button" class="mnpk-button mnpk-button--ghost" data-close-modal>閉じる</button>
                            <button type="button" class="mnpk-button" data-apply-dates>この日付を使う</button>
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
                            <span>最大 <?php echo esc_html($capacity); ?>名</span>
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

                            <p class="mnpk-form-help" data-guest-help>合計 1名</p>
                        </div>

                        <div class="mnpk-modal__actions">
                            <button type="button" class="mnpk-button mnpk-button--ghost" data-close-modal>閉じる</button>
                            <button type="button" class="mnpk-button" data-apply-guests>この人数を使う</button>
                        </div>
                    </div>
                </div>

                <!-- =========================================================
                     モバイル専用: 支払いモーダル
                     ========================================================= -->
                <?php get_template_part('template-parts/minpaku/payment-modal-mobile'); ?>

                <!-- =========================================================
                     モバイル専用: 下部予約バー
                     ========================================================= -->
                <div id="mnpk-mobile-bookbar" class="mnpk-mobile-bookbar" aria-label="民泊予約バー">
                    <div class="mnpk-mobile-bookbar__meta">
                        <strong class="mnpk-mobile-bookbar__price">
                            <?php echo esc_html($money($nightly_price)); ?>
                        </strong>
                        <span class="mnpk-mobile-bookbar__sub">
                            / 泊〜
                            最低 <?php echo esc_html($min_nights); ?>泊
                        </span>
                    </div>

                    <button type="button" class="mnpk-mobile-bookbar__button" data-mnpk-mobile-booking-open>
                        予約
                    </button>
                </div>

            </article>
        </main>

<?php
    endwhile;
endif;

get_footer();
