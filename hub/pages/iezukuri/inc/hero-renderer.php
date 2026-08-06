<?php
/**
 * =========================================================
 * hub/pages/iezukuri/inc/hero-renderer.php
 *
 * 役割:
 * - トップHero / サブHero 共通描画。
 *
 * 方針:
 * - 同じ処理を使うが、画像・H1・P・CTAは post_id ごとに別。
 * - フロント表示は _ch_hero_* を読む。
 * - 画像名・メディアタイトルをHero見出しに使わない。
 * - H1 + P は同じスライドのセットで出す。
 * =========================================================
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('naigai_iez_sanitize_hero_engine')) {
    function naigai_iez_sanitize_hero_engine($engine) {
        $engine = sanitize_key((string) $engine);

        if ($engine === 'fade') {
            return 'burns';
        }

        $allowed = array('image', 'swiper', 'video', 'burns');
        return in_array($engine, $allowed, true) ? $engine : 'burns';
    }
}

if (!function_exists('naigai_iez_sanitize_hero_motion')) {
    function naigai_iez_sanitize_hero_motion($motion, $index = 0) {
        $motion = sanitize_key((string) $motion);

        $legacy = array(
            'zoom-in'   => 'kenburns-top',
            'zoom-out'  => 'kenburns-bottom',
            'pan-left'  => 'kenburns-left',
            'pan-right' => 'kenburns-right',
            'pan-up'    => 'kenburns-top',
            'pan-down'  => 'kenburns-bottom',
            'fade'      => 'kenburns-top',
        );

        if (isset($legacy[$motion])) {
            return $legacy[$motion];
        }

        $allowed = array('none', 'kenburns-top', 'kenburns-bottom', 'kenburns-left', 'kenburns-right');

        if (in_array($motion, $allowed, true)) {
            return $motion;
        }

        $cycle = array('kenburns-top', 'kenburns-bottom', 'kenburns-left', 'kenburns-right');
        return $cycle[$index % count($cycle)];
    }
}

if (!function_exists('naigai_iez_sanitize_caption_motion')) {
    function naigai_iez_sanitize_caption_motion($motion) {
        $motion = sanitize_key((string) $motion);
        $allowed = array('none', 'focus', 'slide-up');

        return in_array($motion, $allowed, true) ? $motion : 'none';
    }
}

if (!function_exists('naigai_iez_hero_meta')) {
    function naigai_iez_hero_meta($post_id, $key, $default = '') {
        $value = get_post_meta(absint($post_id), $key, true);
        return ($value !== '' && $value !== null) ? $value : $default;
    }
}

if (!function_exists('naigai_iez_hero_ids')) {
    function naigai_iez_hero_ids($value) {
        $raw = is_array($value) ? $value : explode(',', (string) $value);
        return array_values(array_unique(array_filter(array_map('absint', $raw))));
    }
}

if (!function_exists('naigai_iez_hero_slide_rows')) {
    function naigai_iez_hero_slide_rows($post_id) {
        $json = get_post_meta(absint($post_id), '_ch_hero_slides_json', true);
        $rows = json_decode((string) $json, true);

        if (!is_array($rows)) {
            return array();
        }

        $map = array();

        foreach ($rows as $row) {
            if (!is_array($row)) {
                continue;
            }

            $image_id = absint($row['image_id'] ?? 0);

            if (!$image_id) {
                continue;
            }

            $map[$image_id] = array(
                'caption'        => sanitize_text_field($row['caption'] ?? ''),
                'title'          => sanitize_text_field($row['title'] ?? ''),
                'lead'           => sanitize_textarea_field($row['lead'] ?? ''),
                'motion'         => naigai_iez_sanitize_hero_motion($row['motion'] ?? '', 0),
                'caption_motion' => naigai_iez_sanitize_caption_motion($row['caption_motion'] ?? 'none'),
            );
        }

        return $map;
    }
}

if (!function_exists('naigai_iez_hero_items')) {
    function naigai_iez_hero_items($post_id, $ids, $fallback_title, $fallback_lead, $default_motion, $default_caption_motion) {
        $items = array();
        $rows = naigai_iez_hero_slide_rows($post_id);

        foreach (naigai_iez_hero_ids($ids) as $index => $image_id) {
            $url = wp_get_attachment_image_url($image_id, 'full');

            if (!$url) {
                continue;
            }

            $row = $rows[$image_id] ?? array();

            $items[] = array(
                'image_id'       => $image_id,
                'url'            => $url,
                'caption'        => sanitize_text_field($row['caption'] ?? ''),
                'title'          => sanitize_text_field(($row['title'] ?? '') !== '' ? $row['title'] : $fallback_title),
                'lead'           => sanitize_textarea_field(($row['lead'] ?? '') !== '' ? $row['lead'] : $fallback_lead),
                'motion'         => naigai_iez_sanitize_hero_motion($row['motion'] ?? $default_motion, $index),
                'caption_motion' => naigai_iez_sanitize_caption_motion($row['caption_motion'] ?? $default_caption_motion),
            );
        }

        return $items;
    }
}

if (!function_exists('naigai_iez_get_page_hero_data')) {
    function naigai_iez_get_page_hero_data($post_id) {
        $post_id = absint($post_id);

        if (!$post_id) {
            return array();
        }

        $engine = naigai_iez_sanitize_hero_engine(naigai_iez_hero_meta($post_id, '_ch_hero_engine', 'burns'));
        $interval = max(4000, absint(naigai_iez_hero_meta($post_id, '_ch_hero_interval', 9000)));

        $title = sanitize_text_field(naigai_iez_hero_meta($post_id, '_ch_hero_title', ''));
        $lead = sanitize_textarea_field(naigai_iez_hero_meta($post_id, '_ch_hero_lead', ''));
        $eyebrow = sanitize_text_field(naigai_iez_hero_meta($post_id, '_ch_hero_kicker', ''));

        $motion = naigai_iez_sanitize_hero_motion(naigai_iez_hero_meta($post_id, '_ch_hero_motion', ''));
        $caption_motion = naigai_iez_sanitize_caption_motion(naigai_iez_hero_meta($post_id, '_ch_hero_caption_motion', 'none'));

        $gallery_ids = naigai_iez_hero_meta($post_id, '_ch_hero_gallery_ids', '');
        $items = naigai_iez_hero_items($post_id, $gallery_ids, $title, $lead, $motion, $caption_motion);

        if (empty($items)) {
            $image_id = absint(naigai_iez_hero_meta($post_id, '_ch_hero_image_id', 0));


            /*
             * ====================================================
             * IEZ_HERO_NO_FEATURED_IMAGE_FALLBACK_20260806
             * ====================================================
             *
             * Hero画像はHero管理画面で設定した画像だけを使う。
             *
             * WordPressのアイキャッチ画像を
             * Heroへ自動利用しない。
             *
             * 理由:
             *
             * 管理画面でHero画像を削除
             *      ↓
             * _ch_hero_image_id が空
             *      ↓
             * アイキャッチ画像が勝手にHeroへ復活
             *
             * という状態を防ぐため。
             * ====================================================
             */
if ($image_id) {
                $items = naigai_iez_hero_items($post_id, array($image_id), $title, $lead, $motion, $caption_motion);
            }
        }

        $video_id = absint(naigai_iez_hero_meta($post_id, '_ch_hero_video_mp4_id', 0));
        $video_url = $video_id ? wp_get_attachment_url($video_id) : '';

        if ($engine === 'video' && !$video_url) {
            $engine = !empty($items) ? 'burns' : 'image';
        }

        if ($engine === 'swiper' && empty($items)) {
            $engine = 'image';
        }

        return array(
            'post_id'        => $post_id,
            'context'        => 'page',
            'engine'         => $engine,
            'title'          => $title,
            'lead'           => $lead,
            'eyebrow'        => $eyebrow,
            'caption_motion' => $caption_motion,
            'interval'       => $interval,
            'video_id'       => $video_id,
            'video_url'      => $video_url,
            'items'          => $items,
            'cta_text'       => sanitize_text_field(naigai_iez_hero_meta($post_id, '_ch_hero_cta_text', '')),
            'cta_url'        => esc_url_raw(naigai_iez_hero_meta($post_id, '_ch_hero_cta_url', '')),
            'sub_cta_text'   => sanitize_text_field(naigai_iez_hero_meta($post_id, '_ch_hero_sub_cta_text', '')),
            'sub_cta_url'    => esc_url_raw(naigai_iez_hero_meta($post_id, '_ch_hero_sub_cta_url', '')),
        );
    }
}

if (!function_exists('naigai_iez_render_hero')) {
    function naigai_iez_render_hero($data) {

    /*
     * ========================================================
     * IEZ_HERO_EMPTY_GUARD_20260806
     * 家づくり固定ページ「空Hero」防止
     * ========================================================
     *
     * Heroの正式な入力データは _ch_hero_*。
     *
     * 固定ページタイトルはHeroデータではない。
     *
     * Hero用の文字・CTA・画像・動画が完全に空なら、
     * <section class="iez-hero"> 自体を出力しない。
     *
     * 画像が無くてもHeroタイトル等が入力されている場合は、
     * 背景色だけのHeroとして表示してよい。
     * ========================================================
     */

    $hero_context = isset($data['context'])
        ? (string) $data['context']
        : '';

    /*
     * この安全装置は固定ページHeroだけに適用。
     *
     * 注文住宅トップHero等の別コンテキストには
     * ここでは触れない。
     */
    if ($hero_context === 'page') {

        /*
         * rendererへ渡されたpost_idを優先。
         *
         * 無い場合のみ現在表示中の固定ページIDを取得。
         */
        $hero_post_id = !empty($data['post_id'])
            ? absint($data['post_id'])
            : absint(get_queried_object_id());

        if ($hero_post_id > 0) {

            /*
             * ------------------------------------------------
             * Hero文字
             * ------------------------------------------------
             */

            $hero_kicker = trim(
                (string) get_post_meta(
                    $hero_post_id,
                    '_ch_hero_kicker',
                    true
                )
            );

            $hero_title = trim(
                (string) get_post_meta(
                    $hero_post_id,
                    '_ch_hero_title',
                    true
                )
            );

            $hero_lead = trim(
                (string) get_post_meta(
                    $hero_post_id,
                    '_ch_hero_lead',
                    true
                )
            );


            /*
             * ------------------------------------------------
             * メインCTA
             * ------------------------------------------------
             *
             * CTAは「文言」と「URL」の両方が入力されて
             * 初めて有効とする。
             *
             * 片方しかない場合はHero表示理由にしない。
             */

            $hero_cta_text = trim(
                (string) get_post_meta(
                    $hero_post_id,
                    '_ch_hero_cta_text',
                    true
                )
            );

            $hero_cta_url = trim(
                (string) get_post_meta(
                    $hero_post_id,
                    '_ch_hero_cta_url',
                    true
                )
            );

            $hero_has_cta = (
                $hero_cta_text !== ''
                && $hero_cta_url !== ''
            );


            /*
             * ------------------------------------------------
             * サブCTA
             * ------------------------------------------------
             */

            $hero_sub_cta_text = trim(
                (string) get_post_meta(
                    $hero_post_id,
                    '_ch_hero_sub_cta_text',
                    true
                )
            );

            $hero_sub_cta_url = trim(
                (string) get_post_meta(
                    $hero_post_id,
                    '_ch_hero_sub_cta_url',
                    true
                )
            );

            $hero_has_sub_cta = (
                $hero_sub_cta_text !== ''
                && $hero_sub_cta_url !== ''
            );


            /*
             * ------------------------------------------------
             * 単体Hero画像
             * ------------------------------------------------
             */

            $hero_image_id = absint(
                get_post_meta(
                    $hero_post_id,
                    '_ch_hero_image_id',
                    true
                )
            );

            $hero_has_image = ($hero_image_id > 0);


            /*
             * ------------------------------------------------
             * Heroギャラリー
             * ------------------------------------------------
             *
             * 保存形式が
             * ・配列
             * ・JSON
             * ・カンマ区切り
             *
             * のどれでも判定できるようにする。
             */

            $hero_gallery_raw = get_post_meta(
                $hero_post_id,
                '_ch_hero_gallery_ids',
                true
            );

            $hero_gallery_ids = array();

            if (is_array($hero_gallery_raw)) {

                $hero_gallery_ids = $hero_gallery_raw;

            } elseif (
                is_string($hero_gallery_raw)
                && trim($hero_gallery_raw) !== ''
            ) {

                $decoded = json_decode(
                    $hero_gallery_raw,
                    true
                );

                if (is_array($decoded)) {

                    $hero_gallery_ids = $decoded;

                } else {

                    $hero_gallery_ids = preg_split(
                        '/[\s,]+/',
                        trim($hero_gallery_raw)
                    );
                }
            }

            $hero_gallery_ids = array_values(
                array_filter(
                    array_map(
                        'absint',
                        $hero_gallery_ids
                    )
                )
            );

            $hero_has_gallery = !empty(
                $hero_gallery_ids
            );


            /*
             * ------------------------------------------------
             * MP4 Hero動画
             * ------------------------------------------------
             */

            $hero_video_id = absint(
                get_post_meta(
                    $hero_post_id,
                    '_ch_hero_video_mp4_id',
                    true
                )
            );

            $hero_has_video = ($hero_video_id > 0);


            /*
             * ------------------------------------------------
             * Hero表示対象が何か1つでも存在するか。
             * ------------------------------------------------
             */

            $hero_has_content = (
                $hero_kicker !== ''
                || $hero_title !== ''
                || $hero_lead !== ''
                || $hero_has_cta
                || $hero_has_sub_cta
                || $hero_has_image
                || $hero_has_gallery
                || $hero_has_video
            );


            /*
             * ------------------------------------------------
             * 全部空ならここで終了。
             * ------------------------------------------------
             *
             * この return によって
             *
             * <section class="iez-hero ... is-no-media">
             *
             * そのものがHTMLへ出なくなる。
             *
             * CSSで display:none にして隠すのではなく、
             * PHP側で不要なHeroを生成しない。
             * ------------------------------------------------
             */

            /*
 * ========================================================
 * IEZ_HERO_THREE_STATE_POLICY_20260806
 * 家づくり固定ページ Hero 3状態ルール
 * ========================================================
 *
 * Heroは、次の3状態だけで動かす。
 *
 *
 * --------------------------------------------------------
 * 【状態1】Hero画像・動画がある
 * --------------------------------------------------------
 *
 * 例:
 *
 * _ch_hero_image_id
 * _ch_hero_gallery_ids
 * _ch_hero_video_mp4_id
 *
 * のいずれかに有効なメディアがある。
 *
 *      ↓
 *
 * 通常の画像 / 動画 Hero を表示する。
 *
 * 背景として実際の画像・動画を使うため、
 * 「画像なし用の茶系背景」を表示条件にはしない。
 *
 *
 * --------------------------------------------------------
 * 【状態2】画像はないが、Hero用の要素がある
 * --------------------------------------------------------
 *
 * 例:
 *
 * _ch_hero_kicker
 * _ch_hero_title
 * _ch_hero_lead
 *
 * または
 *
 * CTA文言 + CTA URL
 *
 * が設定されている。
 *
 *      ↓
 *
 * .iez-hero.is-no-media
 *
 * を出力する。
 *
 * この状態ではCSS側の茶系背景を使用する。
 *
 *
 * 重要:
 *
 * 「画像が無いから茶色」ではない。
 *
 * 正しくは、
 *
 *     画像は無い
 *     ＋
 *     Hero用の文字・CTA等が存在する
 *
 * 場合だけ茶系Heroを表示する。
 *
 *
 * --------------------------------------------------------
 * 【状態3】Heroが完全に空
 * --------------------------------------------------------
 *
 * ・画像なし
 * ・動画なし
 * ・Heroタイトルなし
 * ・キッカーなし
 * ・リードなし
 * ・有効CTAなし
 *
 *      ↓
 *
 * 茶系Heroは表示しない。
 *
 * 代わりに、
 *
 * WordPress固定ページタイトル
 *
 *     get_the_title($hero_post_id)
 *
 * を通常のページタイトル <h1> として表示する。
 *
 *
 * 例:
 *
 * /iezukuri/contact/
 *
 * WordPress固定ページタイトル:
 *
 *     ご相談・資料請求
 *
 *      ↓
 *
 * <h1>ご相談・資料請求</h1>
 *
 * としてフォームの上へ表示する。
 *
 *
 * --------------------------------------------------------
 * Heroタイトルと固定ページタイトルは別物
 * --------------------------------------------------------
 *
 * _ch_hero_title
 *
 * はあくまで「Hero内部へ表示する任意の文字」。
 *
 * WordPressの post_title を
 * _ch_hero_title へコピー・保存・自動補完してはいけない。
 *
 * Heroが空の場合だけ、
 * post_title を通常ページのH1として利用する。
 *
 * これによって、
 *
 * 固定ページタイトルが勝手に茶色Heroへ入る
 *
 * という以前の問題を防止する。
 * ========================================================
 */

if (!$hero_has_content) {

    /*
     * ----------------------------------------------------
     * Heroが完全に空なので、Hero section は作らない。
     * ----------------------------------------------------
     *
     * ここでは
     *
     * <section class="iez-hero">
     *
     * を出力しない。
     *
     * そのため、hero.css の茶系背景も発生しない。
     */


    /*
     * ----------------------------------------------------
     * 通常ページのH1を取得
     * ----------------------------------------------------
     *
     * これはHeroタイトルではない。
     *
     * WordPress固定ページ編集画面の一番上にある
     * post_title を使用する。
     */
    $page_h1 = trim(
        (string) get_the_title(
            $hero_post_id
        )
    );


    /*
     * 固定ページタイトルまで空の場合は
     * 出力するものがないため終了する。
     */
    if ($page_h1 === '') {
        return;
    }


    /*
     * ----------------------------------------------------
     * 通常ページH1を表示
     * ----------------------------------------------------
     *
     * Heroではないため、
     *
     * ・iez-hero
     * ・iez-hero__overlay
     * ・茶系Hero背景
     *
     * は一切使用しない。
     *
     * このheaderの直後に、
     * contactページなら既存フォームが続く。
     */
    ?>
    <header
        class="iez-page-heading"
        data-iez-page-heading
    >
        <div class="iez-page-heading__inner">

            <h1 class="iez-page-heading__title">
                <?php echo esc_html($page_h1); ?>
            </h1>

        </div>
    </header>
    <?php

    /*
     * 通常H1を表示したので、
     * この後のHero renderer本体は実行しない。
     */
    return;
}
        }
    }

        if (!is_array($data)) {
            return;
        }

        $post_id = absint($data['post_id'] ?? 0);
        $context = sanitize_key((string) ($data['context'] ?? 'page'));
        $engine = naigai_iez_sanitize_hero_engine($data['engine'] ?? 'burns');
        $interval = max(4000, absint($data['interval'] ?? 9000));
        $caption_motion = naigai_iez_sanitize_caption_motion($data['caption_motion'] ?? 'none');

        $title = sanitize_text_field($data['title'] ?? '');
        $lead = sanitize_textarea_field($data['lead'] ?? '');
        $eyebrow = sanitize_text_field($data['eyebrow'] ?? '');

        $items = is_array($data['items'] ?? null) ? $data['items'] : array();

        $first = $items[0] ?? array();
        $initial_caption = sanitize_text_field($first['caption'] ?? $eyebrow);
        $initial_title = sanitize_text_field(($first['title'] ?? '') !== '' ? $first['title'] : $title);
        $initial_lead = sanitize_textarea_field(($first['lead'] ?? '') !== '' ? $first['lead'] : $lead);

        $video_url = esc_url($data['video_url'] ?? '');
        $video_id = absint($data['video_id'] ?? 0);

        $cta_text = sanitize_text_field($data['cta_text'] ?? '');
        $cta_url = esc_url($data['cta_url'] ?? '');
        $sub_cta_text = sanitize_text_field($data['sub_cta_text'] ?? '');
        $sub_cta_url = esc_url($data['sub_cta_url'] ?? '');

        $has_media = ($engine === 'video' && $video_url !== '') || !empty($items);

        $classes = array(
            'iez-hero',
            'iez-hero--' . $engine,
            'iez-hero--' . $context,
            $has_media ? 'is-has-media' : 'is-no-media',
        );
        ?>
        <section
            class="<?php echo esc_attr(implode(' ', $classes)); ?>"
            data-iez-hero
            data-iez-hero-post-id="<?php echo esc_attr($post_id); ?>"
            data-iez-hero-context="<?php echo esc_attr($context); ?>"
            data-iez-hero-engine="<?php echo esc_attr($engine); ?>"
            data-iez-hero-interval="<?php echo esc_attr($interval); ?>"
            data-iez-caption-motion="<?php echo esc_attr($caption_motion); ?>"
        >
            <?php if ($engine === 'video' && $video_url !== '') : ?>
                <div class="iez-hero__media iez-hero__media--video">
                    <figure
                        class="iez-hero__slide is-active"
                        data-iez-hero-slide
                        data-caption="<?php echo esc_attr($initial_caption); ?>"
                        data-title="<?php echo esc_attr($initial_title); ?>"
                        data-lead="<?php echo esc_attr($initial_lead); ?>"
                        data-caption-motion="<?php echo esc_attr($caption_motion); ?>"
                    >
                        <video
                            class="iez-hero__video"
                            src="<?php echo esc_url($video_url); ?>"
                            autoplay
                            muted
                            loop
                            playsinline
                            <?php if ($video_id) : ?>
                                data-video-id="<?php echo esc_attr($video_id); ?>"
                            <?php endif; ?>
                        ></video>
                    </figure>
                </div>
            <?php elseif ($engine === 'swiper' && !empty($items)) : ?>
                <div class="iez-hero__media iez-hero__media--swiper">
                    <div class="swiper iez-hero__swiper">
                        <div class="swiper-wrapper iez-hero__track">
                            <?php foreach ($items as $index => $item) : ?>
                                <figure
                                    class="swiper-slide iez-hero__slide <?php echo $index === 0 ? 'is-active' : ''; ?>"
                                    data-iez-hero-slide
                                    data-caption="<?php echo esc_attr($item['caption'] ?? ''); ?>"
                                    data-title="<?php echo esc_attr($item['title'] ?? $title); ?>"
                                    data-lead="<?php echo esc_attr($item['lead'] ?? $lead); ?>"
                                    data-caption-motion="<?php echo esc_attr($item['caption_motion'] ?? $caption_motion); ?>"
                                    data-motion="<?php echo esc_attr($item['motion'] ?? 'kenburns-top'); ?>"
                                >
                                    <img class="iez-hero__image" src="<?php echo esc_url($item['url'] ?? ''); ?>" alt="" loading="<?php echo $index === 0 ? 'eager' : 'lazy'; ?>">
                                </figure>
                            <?php endforeach; ?>
                        </div>
                        <div class="swiper-pagination iez-hero__swiper-pagination"></div>
                        <div class="swiper-button-prev iez-hero__swiper-prev"></div>
                        <div class="swiper-button-next iez-hero__swiper-next"></div>
                    </div>
                </div>
            <?php elseif (!empty($items)) : ?>
                <div class="iez-hero__media iez-hero__media--<?php echo esc_attr($engine === 'image' ? 'image' : 'burns'); ?>">
                    <?php foreach ($items as $index => $item) : ?>
                        <figure
                            class="iez-hero__slide <?php echo $index === 0 ? 'is-active' : ''; ?>"
                            data-iez-hero-slide
                            data-caption="<?php echo esc_attr($item['caption'] ?? ''); ?>"
                            data-title="<?php echo esc_attr($item['title'] ?? $title); ?>"
                            data-lead="<?php echo esc_attr($item['lead'] ?? $lead); ?>"
                            data-caption-motion="<?php echo esc_attr($item['caption_motion'] ?? $caption_motion); ?>"
                            data-motion="<?php echo esc_attr($item['motion'] ?? 'kenburns-top'); ?>"
                        >
                            <img class="iez-hero__image" src="<?php echo esc_url($item['url'] ?? ''); ?>" alt="" loading="<?php echo $index === 0 ? 'eager' : 'lazy'; ?>">
                        </figure>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="iez-hero__overlay" aria-hidden="true"></div>

            <div class="iez-hero__inner">
                <div class="iez-hero__content">
                    <?php if ($initial_caption !== '') : ?>
                        <p class="iez-hero__caption" data-iez-hero-caption><?php echo esc_html($initial_caption); ?></p>
                    <?php else : ?>
                        <p class="iez-hero__caption" data-iez-hero-caption hidden></p>
                    <?php endif; ?>

                    <?php if ($initial_title !== '') : ?>
                        <h1 class="iez-hero__title" data-iez-hero-title><?php echo esc_html($initial_title); ?></h1>
                    <?php endif; ?>

                    <?php if ($initial_lead !== '') : ?>
                        <p class="iez-hero__lead" data-iez-hero-lead><?php echo esc_html($initial_lead); ?></p>
                    <?php else : ?>
                        <p class="iez-hero__lead" data-iez-hero-lead hidden></p>
                    <?php endif; ?>

                    <?php if (($cta_text !== '' && $cta_url !== '') || ($sub_cta_text !== '' && $sub_cta_url !== '')) : ?>
                        <div class="iez-hero__actions">
                            <?php if ($cta_text !== '' && $cta_url !== '') : ?>
                                <a class="iez-hero__btn iez-hero__btn--primary" href="<?php echo esc_url($cta_url); ?>"><?php echo esc_html($cta_text); ?></a>
                            <?php endif; ?>

                            <?php if ($sub_cta_text !== '' && $sub_cta_url !== '') : ?>
                                <a class="iez-hero__btn iez-hero__btn--secondary" href="<?php echo esc_url($sub_cta_url); ?>"><?php echo esc_html($sub_cta_text); ?></a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
        <?php
    }
}
