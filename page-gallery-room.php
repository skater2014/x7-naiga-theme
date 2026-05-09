<?php

/****************************************
 * Template Name: page-gallery-room.php
 ****************************************/

if (!defined('ABSPATH')) {
    exit;
}

get_header('77');

$post_id    = get_the_ID();
$page_title = get_the_title($post_id);

/* =========================================================
 * 管理画面から設定する文言
 * ========================================================= */
$hero_eyebrow = get_post_meta($post_id, '_rg_hero_eyebrow', true);
$hero_title   = get_post_meta($post_id, '_rg_hero_title', true);
$hero_lead    = get_post_meta($post_id, '_rg_hero_lead', true);
$hero_text    = get_post_meta($post_id, '_rg_hero_text', true);

$media_title   = get_post_meta($post_id, '_rg_media_title', true);
$media_lead    = get_post_meta($post_id, '_rg_media_lead', true);
$youtube_title = get_post_meta($post_id, '_rg_youtube_title', true);
$youtube_lead  = get_post_meta($post_id, '_rg_youtube_lead', true);
$related_title = get_post_meta($post_id, '_rg_related_title', true);
$related_lead  = get_post_meta($post_id, '_rg_related_lead', true);
$gallery_title = get_post_meta($post_id, '_rg_gallery_title', true);
$gallery_lead  = get_post_meta($post_id, '_rg_gallery_lead', true);
$cta_text      = get_post_meta($post_id, '_rg_cta_text', true);
$cta_email     = get_post_meta($post_id, '_rg_cta_email', true);

/* ---------- デフォルト値 ---------- */
$hero_eyebrow = $hero_eyebrow !== '' ? $hero_eyebrow : 'NASU ROOM STYLE';
$hero_title   = $hero_title !== '' ? $hero_title : 'お部屋ギャラリー';
$hero_lead    = $hero_lead !== '' ? $hero_lead : '東京と那須、二つの魅力を取り入れた暮らしを実現したい方へ。';
$hero_text    = $hero_text !== '' ? $hero_text : '二拠点生活・移住・別荘購入を検討する方に向けて、理想の住まいと暮らし方をご提案します。';

$media_title   = $media_title !== '' ? $media_title : '北米住宅仕様';
$media_lead    = $media_lead !== '' ? $media_lead : '住まいの考え方や空間づくりの参考として、建築仕様や雰囲気をご覧いただけます。';
$youtube_title = $youtube_title !== '' ? $youtube_title : '分譲地紹介';
$youtube_lead  = $youtube_lead !== '' ? $youtube_lead : '那須での暮らしや周辺環境のイメージを深めたい方に向けて、分譲地の魅力をご紹介します。';
$related_title = $related_title !== '' ? $related_title : '関連する住まい・暮らしのご紹介';
$related_lead  = $related_lead !== '' ? $related_lead : '那須での住まいづくりや暮らしの参考になる情報をご覧いただけます。';
$gallery_title = $gallery_title !== '' ? $gallery_title : '那須で描く、これからの暮らし';
$gallery_lead  = $gallery_lead !== '' ? $gallery_lead : '住まいの雰囲気や空間づくりの参考として、写真を大きくご覧いただけます。';
$cta_text      = $cta_text !== '' ? $cta_text : '住まいや暮らしに関するご相談は、下記メールアドレスからお問い合わせください。';
$cta_email     = $cta_email !== '' ? $cta_email : 'contact@naigaicorp.net';

/* =========================================================
 * 共通ヘルパー
 * ========================================================= */
$normalize_label = static function ($label) {
    $label = trim((string) $label);

    if ($label === '' || strtolower($label) === 'test') {
        return '';
    }

    return $label;
};

$get_attachment_alt = static function ($attachment_id, $fallback = '') {
    $alt = trim((string) get_post_meta($attachment_id, '_wp_attachment_image_alt', true));
    if ($alt !== '') {
        return $alt;
    }

    $title = get_the_title($attachment_id);
    if (!empty($title)) {
        return $title;
    }

    return $fallback;
};

/* =========================================================
 * Hero Slider データ
 * - 各スライドごとの文言を取得
 * - 2枚目以降未入力時は 1枚目 → 共通ヒーロー文言 の順で補完
 * ========================================================= */
$hero_slides = array();

$slide1_eyebrow = trim((string) get_post_meta($post_id, '_rg_slide_hero_eyebrow_1', true));
$slide1_title   = trim((string) get_post_meta($post_id, '_rg_slide_hero_title_1', true));
$slide1_lead    = trim((string) get_post_meta($post_id, '_rg_slide_hero_lead_1', true));
$slide1_text    = trim((string) get_post_meta($post_id, '_rg_slide_hero_text_1', true));

for ($i = 1; $i <= 20; $i++) {
    $image_id = absint(get_post_meta($post_id, "slider_image_{$i}", true));
    if (!$image_id) {
        continue;
    }

    $image_url = wp_get_attachment_image_url($image_id, 'large');
    if (!$image_url) {
        continue;
    }

    $slide_eyebrow_raw = trim((string) get_post_meta($post_id, "_rg_slide_hero_eyebrow_{$i}", true));
    $slide_title_raw   = trim((string) get_post_meta($post_id, "_rg_slide_hero_title_{$i}", true));
    $slide_lead_raw    = trim((string) get_post_meta($post_id, "_rg_slide_hero_lead_{$i}", true));
    $slide_text_raw    = trim((string) get_post_meta($post_id, "_rg_slide_hero_text_{$i}", true));

    $slide_eyebrow = $slide_eyebrow_raw !== ''
        ? $slide_eyebrow_raw
        : ($slide1_eyebrow !== '' ? $slide1_eyebrow : $hero_eyebrow);

    $slide_title = $slide_title_raw !== ''
        ? $slide_title_raw
        : ($slide1_title !== '' ? $slide1_title : $hero_title);

    $slide_lead = $slide_lead_raw !== ''
        ? $slide_lead_raw
        : ($slide1_lead !== '' ? $slide1_lead : $hero_lead);

    $slide_text = $slide_text_raw !== ''
        ? $slide_text_raw
        : ($slide1_text !== '' ? $slide1_text : $hero_text);

    $hero_slides[] = array(
        'image_id' => $image_id,
        'alt'      => $get_attachment_alt($image_id, $slide_title !== '' ? $slide_title : $hero_title),
        'eyebrow'  => $slide_eyebrow,
        'title'    => $slide_title,
        'lead'     => $slide_lead,
        'text'     => $slide_text,
    );
}

/* =========================================================
 * 下部ギャラリー画像データ
 * ========================================================= */
$gallery_images = get_post_meta($post_id, 'room_gallery_images', true);

if (!empty($gallery_images) && !is_array($gallery_images)) {
    $gallery_images = explode(',', (string) $gallery_images);
}

$gallery_images = is_array($gallery_images)
    ? array_slice(array_filter(array_map('absint', $gallery_images)), 0, 20)
    : array();

$room_gallery_items = array();

if (!empty($gallery_images)) {
    foreach ($gallery_images as $index => $image_id) {
        $full_url = wp_get_attachment_image_url($image_id, 'full');
        if (!$full_url) {
            continue;
        }

        $meta_index = $index + 1;

        $saved_label = get_post_meta($post_id, "_rg_gallery_label_{$meta_index}", true);
        $saved_alt   = get_post_meta($post_id, "_rg_gallery_alt_{$meta_index}", true);

        $label = $normalize_label($saved_label);

        $fallback = $saved_alt !== ''
            ? $saved_alt
            : ($label !== '' ? $label : $page_title);

        $alt_text = $get_attachment_alt($image_id, $fallback);

        $room_gallery_items[] = array(
            'slide_index' => count($room_gallery_items),
            'image_id'    => $image_id,
            'alt'         => $alt_text,
            'label'       => $label,
            'full'        => $full_url,
        );
    }
}

/* =========================================================
 * 関連記事 / 関連物件
 * ========================================================= */
$related_items = array();

$related_query = new WP_Query(array(
    'post_type'           => array('post', 'house'),
    'post_status'         => 'publish',
    'posts_per_page'      => 7,
    'post__not_in'        => array($post_id),
    'ignore_sticky_posts' => true,
));

if ($related_query->have_posts()) {
    while ($related_query->have_posts()) {
        $related_query->the_post();

        $related_post_id = get_the_ID();
        $post_url        = get_permalink($related_post_id);
        $thumbnail       = get_the_post_thumbnail_url($related_post_id, 'large');

        $type     = get_post_meta($related_post_id, 'page_featured_type', true);
        $video_id = trim((string) get_post_meta($related_post_id, 'page_video_id', true));

        if ($type === 'youtube' && $video_id !== '') {
            $thumbnail = 'https://i.ytimg.com/vi/' . rawurlencode($video_id) . '/hqdefault.jpg';
        } elseif ($type === 'vimeo' && $video_id !== '') {
            $thumbnail = 'https://vumbnail.com/' . rawurlencode($video_id) . '.jpg';
        }

        if (empty($thumbnail)) {
            $thumbnail = get_template_directory_uri() . '/images/no-image.jpg';
        }

        $related_items[] = array(
            'title'     => get_the_title($related_post_id),
            'url'       => $post_url,
            'thumbnail' => $thumbnail,
        );
    }
}
wp_reset_postdata();

$related_groups = array(
    array(
        'items'   => array_slice($related_items, 0, 3),
        'classes' => array('mason-box', 'mason-box small', 'mason-box small'),
    ),
    array(
        'items'   => array_slice($related_items, 3, 1),
        'classes' => array('mason-box full'),
    ),
    array(
        'items'   => array_slice($related_items, 4, 3),
        'classes' => array('mason-box small', 'mason-box small', 'mason-box'),
    ),
);

?>
<style>
    .room-gallery-page,
    .room-gallery-page * {
        box-sizing: border-box;
    }

    .room-gallery-page .post-container {
        width: 100%;
    }

    .room-gallery-page .section-heading {
        max-width: 1200px;
        margin: 0 auto 24px;
        padding: 0 16px;
    }

    .room-gallery-page .section-heading--center {
        text-align: center;
    }

    .room-gallery-page .section-title {
        margin: 0 0 12px;
        font-size: clamp(1.6rem, 2.3vw, 2.35rem);
        line-height: 1.35;
        font-weight: 800;
        color: #222;
    }

    .room-gallery-page .section-lead {
        margin: 0;
        font-size: 1rem;
        line-height: 1.9;
        color: #555;
    }

    .room-gallery-page .post-container .content p {
        max-width: 1200px;
        margin: 0 auto 1.2rem;
        padding: 0 16px;
        line-height: 1.95;
        word-break: break-word;
        color: #333;
    }

    .room-gallery-page .post-container .content>h2,
    .room-gallery-page .post-container .content>h3 {
        max-width: 1200px;
        margin-left: auto;
        margin-right: auto;
        padding-left: 16px;
        padding-right: 16px;
    }

    body.dark-theme .room-gallery-page .section-title,
    body.dark-theme .room-gallery-page .post-container .content>h2,
    body.dark-theme .room-gallery-page .post-container .content>h3,
    body.dark-theme .room-gallery-page .thumbnail-overlay h3 {
        color: var(--color-heading);
    }

    body.dark-theme .room-gallery-page .section-lead,
    body.dark-theme .room-gallery-page .post-container .content p,
    body.dark-theme .room-gallery-page .post-container .content,
    body.dark-theme .room-gallery-page .room-mail-cta-text {
        color: var(--color-text);
    }

    body.dark-theme .room-gallery-page .full-width-image-carousel {
        background: #161616;
    }

    body.dark-theme .room-gallery-page .room-main-swiper .swiper-button-next,
    body.dark-theme .room-gallery-page .room-main-swiper .swiper-button-prev,
    body.dark-theme .room-gallery-page .room-lightbox .swiper-button-next,
    body.dark-theme .room-gallery-page .room-lightbox .swiper-button-prev,
    body.dark-theme .room-gallery-page .room-gallery-nav,
    body.dark-theme .room-gallery-page .youtube-video-nav,
    body.dark-theme .room-gallery-page .related-mobile-nav {
        color: #fff;
    }

    body.dark-theme .room-gallery-page .room-main-swiper .swiper-slide img {
        filter: brightness(1.04) saturate(1.02);
    }

    body.dark-theme .room-gallery-page .room-hero-overlay {
        background: linear-gradient(to top,
                rgba(0, 0, 0, 0.32) 0%,
                rgba(0, 0, 0, 0.12) 42%,
                rgba(0, 0, 0, 0) 100%);
    }

    body.dark-theme .room-gallery-page .room-lightbox-close {
        background: rgba(255, 255, 255, 0.18);
        color: #fff;
        border: 1px solid rgba(255, 255, 255, 0.28);
    }

    body.dark-theme .room-gallery-page .main-slider-thumbnails .thumb-item img {
        border-color: #555;
    }

    body.dark-theme .room-gallery-page .main-slider-thumbnails .thumb-item:hover img,
    body.dark-theme .room-gallery-page .main-slider-thumbnails .thumb-item.is-active img {
        border-color: #fff;
    }

    body.dark-theme .room-gallery-page .room-gallery-label,
    body.dark-theme .room-gallery-page .room-lightbox-caption,
    body.dark-theme .room-gallery-page .room-lightbox-thumb-label {
        background: rgba(0, 0, 0, 0.72);
        color: #fff;
    }

    body.dark-theme .room-gallery-page .room-mail-cta-link {
        border-color: #fff;
        color: #fff;
    }

    body.dark-theme .room-gallery-page .room-mail-cta-link:hover {
        background: #fff;
        color: #111;
    }

    body.dark-theme .room-gallery-page .youtube-video-pagination .swiper-pagination-bullet,
    body.dark-theme .room-gallery-page .related-mobile-pagination .swiper-pagination-bullet,
    body.dark-theme .room-gallery-page .room-gallery-pagination .swiper-pagination-bullet {
        background: #fff;
        opacity: 0.45;
    }

    body.dark-theme .room-gallery-page .youtube-video-pagination .swiper-pagination-bullet-active,
    body.dark-theme .room-gallery-page .related-mobile-pagination .swiper-pagination-bullet-active,
    body.dark-theme .room-gallery-page .room-gallery-pagination .swiper-pagination-bullet-active {
        background: #fff;
        opacity: 1;
    }

    body.dark-theme .room-gallery-page .youtube-video-nav,
    body.dark-theme .room-gallery-page .related-mobile-nav,
    body.dark-theme .room-gallery-page .room-gallery-nav {
        background: rgba(255, 255, 255, 0.16);
        color: #fff;
        border: 1px solid rgba(255, 255, 255, 0.32);
    }

    body.dark-theme .room-gallery-page .youtube-video-nav:hover,
    body.dark-theme .room-gallery-page .related-mobile-nav:hover,
    body.dark-theme .room-gallery-page .room-gallery-nav:hover {
        background: rgba(255, 255, 255, 0.26);
    }

    .room-gallery-page .room-hero {
        position: relative;
        margin-bottom: 24px;
    }

    .room-gallery-page .room-main-swiper,
    .room-gallery-page .room-hero-static {
        position: relative;
        width: 100%;
        height: 560px;
        overflow: hidden;
        background: #111;
    }

    .room-gallery-page .room-main-swiper .swiper-slide {
        position: relative;
        width: 100%;
        height: 100%;
    }

    .room-gallery-page .room-main-swiper .swiper-slide img,
    .room-gallery-page .room-hero-static-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        filter: brightness(1.14) saturate(1.04);
        transform: scale(1.001);
    }

    .room-gallery-page .room-hero-static-image {
        background:
            linear-gradient(rgba(0, 0, 0, 0.18), rgba(0, 0, 0, 0.18)),
            url('http://127.0.0.1:8080/wp-content/themes/x7-naigaicorp/images/no-image.jpg') center center / cover no-repeat;
    }

    .room-gallery-page .room-hero-overlay {
        position: absolute;
        inset: 0;
        z-index: 5;
        display: flex;
        align-items: flex-end;
        pointer-events: none;
        background: linear-gradient(to top,
                rgba(0, 0, 0, 0.22) 0%,
                rgba(0, 0, 0, 0.08) 42%,
                rgba(0, 0, 0, 0) 100%);
    }

    .room-gallery-page .room-hero-copy {
        width: 100%;
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 24px 40px;
        color: #fff;
        text-align: left;
    }

    .room-gallery-page .room-hero-eyebrow,
    .room-gallery-page .room-hero-title,
    .room-gallery-page .room-hero-lead,
    .room-gallery-page .room-hero-text {
        text-align: left;
        margin-left: 0;
        margin-right: auto;
    }

    .room-gallery-page .room-hero-eyebrow {
        margin: 0 0 10px;
        font-size: 12px;
        line-height: 1.4;
        letter-spacing: 0.18em;
        font-weight: 700;
        text-transform: uppercase;
        color: #fff;
        text-shadow: 0 2px 14px rgba(0, 0, 0, 0.28);
    }

    .room-gallery-page .room-hero-title {
        margin: 0 0 12px;
        font-size: clamp(2rem, 5vw, 4rem);
        line-height: 1.1;
        font-weight: 800;
        color: #fff;
        text-shadow: 0 2px 10px rgba(0, 0, 0, 0.16);
    }

    .room-gallery-page .room-hero-lead {
        margin: 0 0 10px;
        max-width: 920px;
        font-size: clamp(1rem, 1.5vw, 1.4rem);
        line-height: 1.6;
        font-weight: 700;
        color: #fff;
        text-shadow: 0 2px 8px rgba(0, 0, 0, 0.14);
    }

    .room-gallery-page .room-hero-text {
        margin: 0;
        max-width: 920px;
        font-size: 1rem;
        line-height: 1.9;
        color: rgba(255, 255, 255, 0.98);
        text-shadow: 0 1px 6px rgba(0, 0, 0, 0.12);
    }

    .room-gallery-page .room-main-swiper .swiper-button-next,
    .room-gallery-page .room-main-swiper .swiper-button-prev {
        color: #fff;
    }

    .room-gallery-page .room-main-swiper .swiper-pagination-bullet {
        background: #fff;
        opacity: 0.75;
    }

    .room-gallery-page .room-main-swiper .swiper-pagination-bullet-active {
        opacity: 1;
    }

    .room-gallery-page .room-main-swiper.is-static .swiper-button-next,
    .room-gallery-page .room-main-swiper.is-static .swiper-button-prev,
    .room-gallery-page .room-main-swiper.is-static .swiper-pagination {
        display: none;
    }

    .room-gallery-page .main-slider-thumbnails {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-top: 1rem;
        flex-wrap: wrap;
        padding: 0 16px;
    }

    .room-gallery-page .main-slider-thumbnails .thumb-item {
        appearance: none;
        width: 100px;
        height: 70px;
        overflow: hidden;
        border: 0;
        border-radius: 8px;
        padding: 0;
        background: transparent;
        cursor: pointer;
    }

    .room-gallery-page .main-slider-thumbnails .thumb-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        border: 2px solid #ccc;
        border-radius: 8px;
        transition: border-color 0.2s ease, transform 0.2s ease;
    }

    .room-gallery-page .main-slider-thumbnails .thumb-item:hover img,
    .room-gallery-page .main-slider-thumbnails .thumb-item.is-active img {
        border-color: #222;
        transform: translateY(-1px);
    }

    .room-gallery-page .full-width-media {
        width: 100%;
        margin: 28px 0 40px;
        padding: 0;
    }

    .room-gallery-page .full-width-media .room-media-block {
        width: 100%;
        max-width: none;
        margin: 0;
        padding: 0;
    }

    .room-gallery-page .full-width-media .custom-video {
        position: relative;
        width: 100%;
        height: clamp(220px, 38vw, 420px);
        min-height: 220px;
        max-height: 420px;
        border-radius: 12px;
        overflow: hidden;
        background: #000;
    }

    .room-gallery-page .full-width-media .room-video-player,
    .room-gallery-page .full-width-media .room-video-player--bg {
        position: absolute;
        inset: 0;
        display: block;
        width: 100%;
        height: 100%;
        border-radius: 12px;
        background: #000;
        object-fit: cover;
    }

    .room-gallery-page .full-width-media .room-audio-player {
        display: block;
        width: 100%;
        max-width: 100%;
        height: 44px;
        min-height: 44px;
        margin: 0;
    }

    .room-gallery-page .full-width-media .room-media-block audio {
        inline-size: 100%;
        block-size: 44px;
    }

    .room-gallery-page .youtube-container {
        width: 100%;
        margin: 40px auto;
    }

    .room-gallery-page .youtube-video-shell {
        position: relative;
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 56px;
    }

    .room-gallery-page .youtube-video-slider {
        position: relative;
        width: 100%;
        overflow: hidden;
    }

    .room-gallery-page .youtube-video-slider .swiper-wrapper {
        align-items: stretch;
    }

    .room-gallery-page .youtube-video-slider .swiper-slide {
        height: auto;
        box-sizing: border-box;
    }

    .room-gallery-page .youtube-video {
        position: relative;
        width: 100%;
        height: 100%;
    }

    .room-gallery-page .youtube-video lite-youtube,
    .room-gallery-page .youtube-video iframe {
        display: block;
        width: 100%;
        max-width: 100%;
        aspect-ratio: 16 / 9;
        border: 0;
        border-radius: 12px;
        overflow: hidden;
        background: #000;
    }

    .room-gallery-page .youtube-video lite-youtube {
        --lite-youtube-frame-border-radius: 12px;
        background-position: center center;
        background-size: cover;
    }

    .room-gallery-page .youtube-video-nav {
        position: absolute;
        top: 50%;
        z-index: 30;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 46px;
        height: 46px;
        margin: 0;
        padding: 0;
        border: 0;
        border-radius: 999px;
        background: rgba(0, 0, 0, 0.72);
        color: #fff;
        cursor: pointer;
        transform: translateY(-50%);
        transition: background 0.2s ease, opacity 0.2s ease;
        -webkit-tap-highlight-color: transparent;
        touch-action: manipulation;
    }

    .room-gallery-page .youtube-video-nav:hover {
        background: rgba(0, 0, 0, 0.88);
    }

    .room-gallery-page .youtube-video-nav:focus-visible {
        outline: 2px solid #fff;
        outline-offset: 2px;
    }

    .room-gallery-page .youtube-video-nav-prev {
        left: 0;
    }

    .room-gallery-page .youtube-video-nav-next {
        right: 0;
    }

    .room-gallery-page .youtube-video-nav span {
        display: block;
        font-size: 24px;
        line-height: 1;
        font-weight: 700;
        pointer-events: none;
    }

    .room-gallery-page .youtube-video-nav.is-hidden {
        opacity: 0.32;
        pointer-events: none;
    }

    .room-gallery-page .youtube-video-pagination {
        margin-top: 16px;
        text-align: center;
    }

    .room-gallery-page .youtube-video-pagination .swiper-pagination-bullet {
        background: #222;
        opacity: 0.35;
    }

    .room-gallery-page .youtube-video-pagination .swiper-pagination-bullet-active {
        opacity: 1;
    }

    .room-gallery-page .mason-grid-community-list {
        margin-bottom: 28px;
    }

    .room-gallery-page .related-desktop-only {
        display: block;
    }

    .room-gallery-page .related-mobile-only {
        display: none;
    }

    .room-gallery-page .mason-grid-wrap.main-mason {
        position: relative;
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 16px;
        overflow: visible;
    }

    .room-gallery-page .mason-grid-slider {
        --masonGap: 1.25rem;
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: var(--masonGap);
        align-items: start;
    }

    .room-gallery-page .mason-grid-item {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: var(--masonGap);
        min-width: 0;
        align-content: start;
    }

    .room-gallery-page .mason-box {
        position: relative;
        display: block;
        min-width: 0;
        overflow: hidden;
        border-radius: 1.5rem;
        background: #e6e6e6;
        aspect-ratio: 1 / 1;
        text-decoration: none;
    }

    .room-gallery-page .mason-box:not(.small) {
        grid-column: span 2;
        aspect-ratio: 16 / 10;
    }

    .room-gallery-page .mason-box.full {
        grid-column: span 2;
        aspect-ratio: auto;
    }

    .room-gallery-page .mason-box.full::before {
        content: "";
        display: block;
        padding-top: calc(112% + var(--masonGap));
    }

    .room-gallery-page .mason-box>img {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .room-gallery-page .thumbnail-overlay {
        position: absolute;
        inset: auto 0 0 0;
        z-index: 2;
        padding: 18px 18px 14px;
        background: linear-gradient(to top,
                rgba(0, 0, 0, 0.62) 0%,
                rgba(0, 0, 0, 0) 100%);
    }

    .room-gallery-page .thumbnail-overlay h3 {
        margin: 0;
        font-size: 1rem;
        line-height: 1.5;
        font-weight: 700;
        color: #fff;
    }

    .room-gallery-page .mason-scrollbar {
        display: none !important;
    }

    .room-gallery-page .related-mobile-slider-shell {
        position: relative;
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 24px 0 14px;
    }

    .room-gallery-page .related-mobile-slider {
        width: 100%;
        overflow: hidden;
    }

    .room-gallery-page .related-mobile-slider .swiper-slide {
        height: auto;
    }

    .room-gallery-page .related-mobile-card {
        position: relative;
        display: block;
        width: 100%;
        overflow: hidden;
        border-radius: 1.5rem;
        background: #e6e6e6;
        aspect-ratio: 16 / 10;
        text-decoration: none;
    }

    .room-gallery-page .related-mobile-card>img {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .room-gallery-page .related-mobile-nav {
        position: absolute;
        top: 50%;
        z-index: 20;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 42px;
        height: 42px;
        padding: 0;
        margin: 0;
        border: 0;
        border-radius: 999px;
        background: rgba(0, 0, 0, 0.72);
        color: #fff;
        cursor: pointer;
        transform: translateY(-50%);
    }

    .room-gallery-page .related-mobile-nav-prev {
        left: 0;
    }

    .room-gallery-page .related-mobile-nav-next {
        right: 0;
    }

    .room-gallery-page .related-mobile-nav span {
        display: block;
        font-size: 22px;
        line-height: 1;
        font-weight: 700;
    }

    .room-gallery-page .related-mobile-pagination {
        margin-top: 16px;
        text-align: center;
    }

    .room-gallery-page .related-mobile-pagination .swiper-pagination-bullet {
        background: #222;
        opacity: 0.35;
    }

    .room-gallery-page .related-mobile-pagination .swiper-pagination-bullet-active {
        opacity: 1;
    }

    .room-gallery-page .full-width-image-carousel {
        background-color: rgba(235, 235, 235, 0.5);
        padding: 48px 0;
    }

    .room-gallery-page .room-mail-cta {
        max-width: 1200px;
        margin: 18px auto 26px;
        padding: 0 16px;
        text-align: center;
    }

    .room-gallery-page .room-mail-cta-text {
        margin: 0 0 10px;
        color: #444;
        line-height: 1.85;
    }

    .room-gallery-page .room-mail-cta-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 46px;
        padding: 0 20px;
        border: 1px solid #222;
        border-radius: 999px;
        color: #222;
        text-decoration: none;
        font-weight: 700;
        transition: background 0.2s ease, color 0.2s ease;
    }

    .room-gallery-page .room-mail-cta-link:hover {
        background: #222;
        color: #fff;
    }

    .room-gallery-page .room-gallery-shell {
        position: relative;
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 56px;
    }

    .room-gallery-page .img-carousel-wrap.fwi-carousel {
        position: relative;
        display: block;
        width: 100%;
        overflow: hidden;
    }

    .room-gallery-page .img-wrap {
        display: flex;
    }

    .room-gallery-page .full-width-image-carousel .img-item {
        position: relative;
        user-select: none;
        border-radius: 1.25rem;
        overflow: hidden;
        background: #e6e6e6;
    }

    .room-gallery-page .full-width-image-carousel .img-item::before {
        content: "";
        display: block;
        padding-top: 72%;
    }

    .room-gallery-page .full-width-image-carousel .img-item>a {
        position: absolute;
        inset: 0;
        display: block;
        width: 100%;
        height: 100%;
        border-radius: inherit;
        text-decoration: none;
    }

    .room-gallery-page .full-width-image-carousel .img-item img {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .room-gallery-page .room-gallery-label {
        position: absolute;
        top: 12px;
        left: 12px;
        z-index: 3;
        display: inline-flex;
        align-items: center;
        min-height: 32px;
        padding: 6px 12px;
        border-radius: 999px;
        background: rgba(0, 0, 0, 0.68);
        color: #fff;
        font-size: 0.85rem;
        line-height: 1.2;
        font-weight: 700;
        letter-spacing: 0.04em;
        pointer-events: none;
    }

    .room-gallery-page .room-gallery-nav {
        position: absolute;
        top: 50%;
        z-index: 20;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 46px;
        height: 46px;
        padding: 0;
        margin: 0;
        border: 0;
        border-radius: 999px;
        background: rgba(0, 0, 0, 0.72);
        color: #fff;
        cursor: pointer;
        transform: translateY(-50%);
        transition: background 0.2s ease, opacity 0.2s ease;
        -webkit-tap-highlight-color: transparent;
        touch-action: manipulation;
    }

    .room-gallery-page .room-gallery-nav:hover {
        background: rgba(0, 0, 0, 0.86);
    }

    .room-gallery-page .room-gallery-nav:focus-visible {
        outline: 2px solid #fff;
        outline-offset: 2px;
    }

    .room-gallery-page .room-gallery-nav-prev {
        left: 0;
    }

    .room-gallery-page .room-gallery-nav-next {
        right: 0;
    }

    .room-gallery-page .room-gallery-nav span {
        display: block;
        font-size: 24px;
        line-height: 1;
        font-weight: 700;
    }

    .room-gallery-page .room-gallery-nav.is-hidden {
        opacity: 0.32;
        pointer-events: none;
    }

    .room-gallery-page .room-gallery-pagination {
        margin-top: 16px;
        text-align: center;
    }

    .room-gallery-page .room-gallery-pagination .swiper-pagination-bullet {
        background: #222;
        opacity: 0.35;
    }

    .room-gallery-page .room-gallery-pagination .swiper-pagination-bullet-active {
        opacity: 1;
    }

    .room-gallery-page .room-lightbox {
        display: none !important;
    }

    @media (min-width: 1025px) {
        .room-gallery-page .room-lightbox.is-open {
            position: fixed;
            inset: 0;
            z-index: 99999;
            display: flex !important;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background: rgba(0, 0, 0, 0.9);
        }

        .room-gallery-page .room-lightbox-dialog {
            position: relative;
            width: min(1380px, 96vw);
            height: min(84vh, 860px);
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .room-gallery-page .room-lightbox-close {
            position: absolute;
            top: -10px;
            right: -10px;
            width: 48px;
            height: 48px;
            border: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.16);
            color: #fff;
            font-size: 28px;
            line-height: 1;
            cursor: pointer;
            z-index: 30;
        }

        .room-gallery-page .room-lightbox-main {
            position: relative;
            flex: 1 1 auto;
            min-height: 0;
            border-radius: 18px;
            overflow: hidden;
            background: #050505;
        }

        .room-gallery-page .room-lightbox-swiper {
            width: 100%;
            height: 100%;
            overflow: hidden;
        }

        .room-gallery-page .room-lightbox-swiper .swiper-wrapper,
        .room-gallery-page .room-lightbox-swiper .swiper-slide {
            height: 100%;
        }

        .room-gallery-page .room-lightbox-swiper .swiper-slide {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 18px;
            background: #050505;
        }

        .room-gallery-page .room-lightbox-swiper .swiper-slide img {
            width: auto;
            height: auto;
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            display: block;
            background: #050505;
        }

        .room-gallery-page .room-lightbox-caption {
            position: absolute;
            top: 18px;
            left: 18px;
            z-index: 5;
            display: inline-flex;
            align-items: center;
            min-height: 36px;
            padding: 8px 14px;
            border-radius: 999px;
            background: rgba(0, 0, 0, 0.72);
            color: #fff;
            font-size: 0.9rem;
            line-height: 1.2;
            font-weight: 700;
            letter-spacing: 0.04em;
            pointer-events: none;
        }

        .room-gallery-page .room-lightbox .swiper-button-next,
        .room-gallery-page .room-lightbox .swiper-button-prev {
            color: #fff;
        }

        .room-gallery-page .room-lightbox .swiper-pagination {
            bottom: 12px !important;
        }

        .room-gallery-page .room-lightbox-thumbs-wrap {
            flex: 0 0 108px;
            min-height: 108px;
        }

        .room-gallery-page .room-lightbox-thumbs-swiper {
            width: 100%;
            height: 108px;
            overflow: hidden;
        }

        .room-gallery-page .room-lightbox-thumbs-swiper .swiper-wrapper {
            align-items: center;
        }

        .room-gallery-page .room-lightbox-thumbs-swiper .swiper-slide {
            width: 154px;
            height: 108px;
        }

        .room-gallery-page .room-lightbox-thumb {
            position: relative;
            width: 100%;
            height: 100%;
            border: 0;
            padding: 0;
            margin: 0;
            border-radius: 14px;
            overflow: hidden;
            cursor: pointer;
            background: #111;
            opacity: 0.62;
            transition: opacity 0.2s ease, transform 0.2s ease;
        }

        .room-gallery-page .room-lightbox-thumb:hover {
            opacity: 1;
        }

        .room-gallery-page .room-lightbox-thumb.is-active {
            opacity: 1;
            transform: translateY(-2px);
            outline: 2px solid rgba(255, 255, 255, 0.92);
            outline-offset: -2px;
        }

        .room-gallery-page .room-lightbox-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .room-gallery-page .room-lightbox-thumb-label {
            position: absolute;
            left: 8px;
            right: 8px;
            bottom: 8px;
            z-index: 2;
            padding: 6px 8px;
            border-radius: 8px;
            background: rgba(0, 0, 0, 0.72);
            color: #fff;
            font-size: 0.76rem;
            line-height: 1.3;
            font-weight: 700;
            text-align: left;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        body.is-room-lightbox-open {
            overflow: hidden;
        }
    }

    @media (max-width: 1024px) {

        .room-gallery-page .youtube-container .section-heading,
        .room-gallery-page .mason-grid-community-list .section-heading,
        .room-gallery-page .full-width-image-carousel .section-heading,
        .room-gallery-page .full-width-media .section-heading {
            text-align: center;
        }

        .room-gallery-page .section-heading {
            padding-left: 14px;
            padding-right: 14px;
            margin-bottom: 20px;
        }

        .room-gallery-page .section-lead {
            max-width: 42rem;
            margin-left: auto;
            margin-right: auto;
        }

        .room-gallery-page .room-main-swiper,
        .room-gallery-page .room-hero-static {
            height: 420px;
        }

        .room-gallery-page .room-hero-copy {
            padding: 0 20px 28px;
        }

        .room-gallery-page .room-hero-text {
            font-size: 0.95rem;
            line-height: 1.8;
        }

        .room-gallery-page .full-width-media .custom-video {
            height: clamp(200px, 42vw, 320px);
            min-height: 200px;
            max-height: 320px;
        }

        .room-gallery-page .youtube-video-shell {
            padding: 0 24px 0 14px;
        }

        .room-gallery-page .room-gallery-shell {
            padding: 0 50px;
        }

        .room-gallery-page .youtube-video-nav,
        .room-gallery-page .room-gallery-nav,
        .room-gallery-page .related-mobile-nav {
            width: 42px;
            height: 42px;
        }

        .room-gallery-page .youtube-video-nav span,
        .room-gallery-page .room-gallery-nav span,
        .room-gallery-page .related-mobile-nav span {
            font-size: 22px;
        }

        .room-gallery-page .related-desktop-only {
            display: none;
        }

        .room-gallery-page .related-mobile-only {
            display: block;
        }

        .room-gallery-page .mason-grid-wrap.main-mason.swiper {
            overflow: hidden;
        }

        .room-gallery-page .mason-grid-wrap.main-mason .mason-grid-slider {
            display: flex;
            gap: 0;
        }

        .room-gallery-page .mason-grid-wrap.main-mason .mason-grid-item {
            width: 100%;
            display: grid;
            grid-template-columns: 1fr;
            gap: 16px;
            min-height: 0;
        }

        .room-gallery-page .mason-grid-wrap.main-mason .mason-grid-item.swiper-slide {
            height: auto;
        }

        .room-gallery-page .mason-grid-wrap.main-mason .mason-box,
        .room-gallery-page .mason-grid-wrap.main-mason .mason-box.small,
        .room-gallery-page .mason-grid-wrap.main-mason .mason-box.full,
        .room-gallery-page .mason-grid-wrap.main-mason .mason-box:not(.small) {
            grid-column: auto;
            aspect-ratio: 4 / 3;
            height: auto;
        }
    }

    @media (max-width: 768px) {
        .room-gallery-page .section-heading {
            padding-left: 14px;
            padding-right: 14px;
            margin-bottom: 18px;
        }

        .room-gallery-page .section-title {
            font-size: 2rem;
            line-height: 1.3;
        }

        .room-gallery-page .section-lead {
            font-size: 0.95rem;
            line-height: 1.8;
        }

        .room-gallery-page .post-container .content p,
        .room-gallery-page .post-container .content>h2,
        .room-gallery-page .post-container .content>h3 {
            padding-left: 14px;
            padding-right: 14px;
        }

        .room-gallery-page .room-main-swiper,
        .room-gallery-page .room-hero-static {
            height: 240px;
        }

        .room-gallery-page .room-hero-copy {
            padding: 0 16px 16px;
        }

        .room-gallery-page .room-hero-eyebrow {
            margin-bottom: 6px;
            font-size: 10px;
        }

        .room-gallery-page .room-hero-title {
            margin-bottom: 8px;
            font-size: 2rem;
        }

        .room-gallery-page .room-hero-lead {
            margin-bottom: 0;
            font-size: 0.95rem;
            line-height: 1.5;
        }

        .room-gallery-page .room-hero-text {
            display: none;
        }

        .room-gallery-page .main-slider-thumbnails .thumb-item {
            width: 72px;
            height: 52px;
        }

        .room-gallery-page .full-width-media .custom-video {
            height: clamp(180px, 52vw, 240px);
            min-height: 180px;
            max-height: 240px;
            border-radius: 10px;
        }

        .room-gallery-page .full-width-media .room-video-player,
        .room-gallery-page .full-width-media .room-video-player--bg {
            border-radius: 10px;
        }

        .room-gallery-page .full-width-media .room-audio-player {
            height: 42px;
            min-height: 42px;
        }

        .room-gallery-page .youtube-video-shell {
            padding: 0 20px 0 14px;
        }

        .room-gallery-page .related-mobile-slider-shell,
        .room-gallery-page .room-gallery-shell {
            padding: 0 42px;
        }

        .room-gallery-page .youtube-video-nav,
        .room-gallery-page .room-gallery-nav,
        .room-gallery-page .related-mobile-nav {
            width: 38px;
            height: 38px;
        }

        .room-gallery-page .youtube-video-nav span,
        .room-gallery-page .room-gallery-nav span,
        .room-gallery-page .related-mobile-nav span {
            font-size: 20px;
        }

        .room-gallery-page .full-width-image-carousel .img-item::before {
            padding-top: 68%;
        }

        .room-gallery-page .room-gallery-label {
            top: 10px;
            left: 10px;
            min-height: 28px;
            padding: 5px 10px;
            font-size: 0.78rem;
        }

        .room-gallery-page .room-mail-cta {
            margin-top: 14px;
            margin-bottom: 22px;
        }

        .room-gallery-page .room-mail-cta-link {
            width: min(100%, 320px);
        }

        .room-gallery-page .thumbnail-overlay {
            padding: 14px 14px 12px;
        }

        .room-gallery-page .thumbnail-overlay h3 {
            font-size: 0.95rem;
        }
    }
</style>

<div class="room-gallery-page">
    <article class="post-container">
        <section class="room-hero">
            <?php if (!empty($hero_slides)) : ?>
                <div class="swiper room-main-swiper<?php echo count($hero_slides) <= 1 ? ' is-static' : ''; ?>">
                    <div class="swiper-wrapper">
                        <?php foreach ($hero_slides as $index => $slide) : ?>
                            <div class="swiper-slide">
                                <?php
                                echo wp_get_attachment_image(
                                    $slide['image_id'],
                                    'large',
                                    false,
                                    array(
                                        'alt'           => $slide['alt'],
                                        'loading'       => $index === 0 ? 'eager' : 'lazy',
                                        'fetchpriority' => $index === 0 ? 'high' : 'auto',
                                    )
                                );
                                ?>

                                <div class="room-hero-overlay">
                                    <div class="room-hero-copy">
                                        <?php if ($slide['eyebrow'] !== '') : ?>
                                            <p class="room-hero-eyebrow"><?php echo esc_html($slide['eyebrow']); ?></p>
                                        <?php endif; ?>

                                        <h1 class="room-hero-title"><?php echo esc_html($slide['title'] !== '' ? $slide['title'] : $hero_title); ?></h1>

                                        <?php if ($slide['lead'] !== '') : ?>
                                            <p class="room-hero-lead"><?php echo esc_html($slide['lead']); ?></p>
                                        <?php endif; ?>

                                        <?php if ($slide['text'] !== '') : ?>
                                            <p class="room-hero-text"><?php echo esc_html($slide['text']); ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="swiper-button-next" aria-label="次のスライド"></div>
                    <div class="swiper-button-prev" aria-label="前のスライド"></div>
                    <div class="swiper-pagination"></div>
                </div>

                <?php if (count($hero_slides) > 1) : ?>
                    <div class="main-slider-thumbnails" aria-label="メイン画像サムネイル">
                        <?php foreach ($hero_slides as $index => $slide) : ?>
                            <button
                                type="button"
                                class="thumb-item<?php echo $index === 0 ? ' is-active' : ''; ?>"
                                data-index="<?php echo esc_attr($index); ?>"
                                aria-label="<?php echo esc_attr(($index + 1) . '枚目の画像'); ?>">
                                <?php
                                echo wp_get_attachment_image(
                                    $slide['image_id'],
                                    'thumbnail',
                                    false,
                                    array(
                                        'alt'     => $slide['alt'],
                                        'loading' => 'lazy',
                                    )
                                );
                                ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

            <?php else : ?>
                <div class="room-hero-static">
                    <div class="room-hero-static-image"></div>
                    <div class="room-hero-overlay">
                        <div class="room-hero-copy">
                            <p class="room-hero-eyebrow"><?php echo esc_html($hero_eyebrow); ?></p>
                            <h1 class="room-hero-title"><?php echo esc_html($hero_title); ?></h1>
                            <p class="room-hero-lead"><?php echo esc_html($hero_lead); ?></p>
                            <p class="room-hero-text"><?php echo esc_html($hero_text); ?></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </section>

        <div class="content">
            <?php the_content(); ?>
            <p>都会の喧騒から離れ、四季折々の美しい風景や温泉、レジャーを満喫できる那須。リモートワークが進む今だからこそ、東京と那須を行き来する“ハイブリッドな暮らし”が、現実的な選択肢となりました。日常を東京で、週末や長期休暇を那須で過ごすワーケーション型の生活から、完全な移住、さらには資産価値としての別荘購入まで。生活スタイルやご希望に合わせた土地探し・住宅設計・建築施工・リフォーム・管理まで、ワンストップでお手伝いいたします。「都市」と「自然」、「働く」と「癒し」が共存する、これからの暮らしを那須で実現してみませんか？</p>

            <p>まずはお気軽にご相談ください。</p>
            <section class="full-width-media">
                <div class="section-heading">
                    <h2 class="section-title"><?php echo esc_html($media_title); ?></h2>
                    <p class="section-lead"><?php echo esc_html($media_lead); ?></p>
                </div>
                <?php
                if (function_exists('display_media_mp4')) {
                    display_media_mp4($post_id);
                }
                ?>
            </section>

            <section class="youtube-container">
                <div class="section-heading">
                    <h2 class="section-title"><?php echo esc_html($youtube_title); ?></h2>
                    <p class="section-lead"><?php echo esc_html($youtube_lead); ?></p>
                </div>
                <?php
                if (function_exists('display_media_youtube')) {
                    display_media_youtube($post_id);
                }
                ?>
            </section>
        </div>
    </article>

    <?php if (!empty($related_items)) : ?>
        <section class="mason-grid-community-list">
            <div class="section-heading">
                <h2 class="section-title"><?php echo esc_html($related_title); ?></h2>
                <p class="section-lead"><?php echo esc_html($related_lead); ?></p>
            </div>

            <div class="mason-grid-wrap main-mason related-desktop-only">
                <div class="mason-grid-slider">
                    <?php foreach ($related_groups as $group) : ?>
                        <?php if (empty($group['items'])) : ?>
                            <?php continue; ?>
                        <?php endif; ?>

                        <div class="mason-grid-item">
                            <?php foreach ($group['items'] as $i => $item) : ?>
                                <?php $box_class = isset($group['classes'][$i]) ? $group['classes'][$i] : 'mason-box small'; ?>
                                <a href="<?php echo esc_url($item['url']); ?>" class="<?php echo esc_attr($box_class); ?>">
                                    <img src="<?php echo esc_url($item['thumbnail']); ?>" alt="<?php echo esc_attr($item['title']); ?>" loading="lazy">
                                    <div class="thumbnail-overlay">
                                        <h3><?php echo esc_html($item['title']); ?></h3>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="mason-scrollbar"></div>
            </div>

            <div class="related-mobile-slider-shell related-mobile-only">
                <button type="button" class="related-mobile-nav related-mobile-nav-prev" aria-label="前の画像">
                    <span aria-hidden="true">‹</span>
                </button>

                <div class="swiper related-mobile-slider">
                    <div class="swiper-wrapper">
                        <?php foreach ($related_items as $item) : ?>
                            <div class="swiper-slide">
                                <a href="<?php echo esc_url($item['url']); ?>" class="related-mobile-card">
                                    <img src="<?php echo esc_url($item['thumbnail']); ?>" alt="<?php echo esc_attr($item['title']); ?>" loading="lazy">
                                    <div class="thumbnail-overlay">
                                        <h3><?php echo esc_html($item['title']); ?></h3>
                                    </div>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="related-mobile-pagination"></div>
                </div>

                <button type="button" class="related-mobile-nav related-mobile-nav-next" aria-label="次の画像">
                    <span aria-hidden="true">›</span>
                </button>
            </div>
        </section>
    <?php endif; ?>

    <?php if (!empty($room_gallery_items)) : ?>
        <section class="full-width-image-carousel space-sm">
            <div class="section-heading section-heading--center">
                <h2 class="section-title"><?php echo esc_html($gallery_title); ?></h2>
                <p class="section-lead"><?php echo esc_html($gallery_lead); ?></p>
            </div>

            <div class="room-mail-cta">
                <p class="room-mail-cta-text"><?php echo esc_html($cta_text); ?></p>
                <a class="room-mail-cta-link" href="mailto:<?php echo antispambot(esc_attr($cta_email)); ?>">
                    <?php echo esc_html(antispambot($cta_email)); ?>
                </a>
            </div>

            <div class="room-gallery-shell">
                <button type="button" class="room-gallery-nav room-gallery-nav-prev" aria-label="前のスライド">
                    <span aria-hidden="true">‹</span>
                </button>

                <div class="img-carousel-wrap fwi-carousel swiper">
                    <div class="img-wrap swiper-wrapper">
                        <?php foreach ($room_gallery_items as $item) : ?>
                            <div class="img-item swiper-slide">
                                <a
                                    href="<?php echo esc_url($item['full']); ?>"
                                    class="js-room-lightbox"
                                    data-room-index="<?php echo esc_attr($item['slide_index']); ?>"
                                    aria-label="<?php echo esc_attr($item['label'] !== '' ? $item['label'] : $page_title); ?>">

                                    <?php if ($item['label'] !== '') : ?>
                                        <span class="room-gallery-label"><?php echo esc_html($item['label']); ?></span>
                                    <?php endif; ?>

                                    <?php
                                    echo wp_get_attachment_image(
                                        $item['image_id'],
                                        'medium_large',
                                        false,
                                        array(
                                            'alt'     => $item['alt'],
                                            'loading' => 'lazy',
                                        )
                                    );
                                    ?>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="room-gallery-pagination"></div>
                </div>

                <button type="button" class="room-gallery-nav room-gallery-nav-next" aria-label="次のスライド">
                    <span aria-hidden="true">›</span>
                </button>
            </div>
        </section>

        <div id="room-lightbox" class="room-lightbox" aria-hidden="true">
            <div class="room-lightbox-dialog">
                <button type="button" class="room-lightbox-close" aria-label="閉じる">×</button>

                <div class="room-lightbox-main">
                    <div class="swiper room-lightbox-swiper">
                        <div class="swiper-wrapper">
                            <?php foreach ($room_gallery_items as $item) : ?>
                                <div class="swiper-slide">
                                    <?php if ($item['label'] !== '') : ?>
                                        <div class="room-lightbox-caption"><?php echo esc_html($item['label']); ?></div>
                                    <?php endif; ?>

                                    <?php
                                    echo wp_get_attachment_image(
                                        $item['image_id'],
                                        'full',
                                        false,
                                        array(
                                            'alt'     => $item['alt'],
                                            'loading' => 'lazy',
                                        )
                                    );
                                    ?>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="swiper-button-next" aria-label="次の画像"></div>
                        <div class="swiper-button-prev" aria-label="前の画像"></div>
                        <div class="swiper-pagination"></div>
                    </div>
                </div>

                <div class="room-lightbox-thumbs-wrap">
                    <div class="swiper room-lightbox-thumbs-swiper">
                        <div class="swiper-wrapper">
                            <?php foreach ($room_gallery_items as $item) : ?>
                                <div class="swiper-slide">
                                    <button
                                        type="button"
                                        class="room-lightbox-thumb"
                                        data-room-thumb-index="<?php echo esc_attr($item['slide_index']); ?>"
                                        aria-label="<?php echo esc_attr($item['label'] !== '' ? $item['label'] : 'サブ画像'); ?>">

                                        <?php
                                        echo wp_get_attachment_image(
                                            $item['image_id'],
                                            'medium_large',
                                            false,
                                            array(
                                                'alt'     => $item['alt'],
                                                'loading' => 'lazy',
                                            )
                                        );
                                        ?>

                                        <?php if ($item['label'] !== '') : ?>
                                            <span class="room-lightbox-thumb-label"><?php echo esc_html($item['label']); ?></span>
                                        <?php endif; ?>
                                    </button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        /* =========================================================
         * 0. 前提チェック
         * - Swiper が未読込なら処理しない
         * - このテンプレートのルート要素が無ければ処理しない
         * ========================================================= */
        if (typeof Swiper === 'undefined') {
            return;
        }

        const root = document.querySelector('.room-gallery-page');
        if (!root) {
            return;
        }

        /* =========================================================
         * 共通: resize の連続発火を間引く
         * - window resize 時に Swiper を何度も destroy/build しないため
         * ========================================================= */
        function debounce(fn, wait) {
            let timer = null;
            return function() {
                const args = arguments;
                clearTimeout(timer);
                timer = window.setTimeout(function() {
                    fn.apply(null, args);
                }, wait);
            };
        }

        /* =========================================================
         * 1. Hero Slider
         * 対応HTML:
         * - .room-main-swiper
         * - .swiper-button-next / .swiper-button-prev
         * - .swiper-pagination
         * - .main-slider-thumbnails .thumb-item
         *
         * 処理内容:
         * - 上部 hero スライダーを初期化
         * - サムネイルの active 状態を同期
         * - サムネイルクリックで該当スライドへ移動
         * ========================================================= */
        (function initHeroSlider() {
            const mainSwiperEl = root.querySelector('.room-main-swiper');
            if (!mainSwiperEl) {
                return;
            }

            const slideCount = mainSwiperEl.querySelectorAll('.swiper-slide').length;
            const nextEl = mainSwiperEl.querySelector('.swiper-button-next');
            const prevEl = mainSwiperEl.querySelector('.swiper-button-prev');
            const paginationEl = mainSwiperEl.querySelector('.swiper-pagination');
            const thumbs = Array.from(root.querySelectorAll('.main-slider-thumbnails .thumb-item'));

            function syncThumb(index) {
                thumbs.forEach(function(thumb, thumbIndex) {
                    thumb.classList.toggle('is-active', thumbIndex === index);
                });
            }

            /* スライドが1枚以下なら Swiper は組まず、サムネイル状態だけ合わせる */
            if (slideCount <= 1) {
                syncThumb(0);
                return;
            }

            const heroSwiper = new Swiper(mainSwiperEl, {
                loop: true,
                effect: 'fade',
                fadeEffect: {
                    crossFade: true
                },
                autoplay: {
                    delay: 3200,
                    disableOnInteraction: false
                },
                pagination: {
                    el: paginationEl,
                    clickable: true
                },
                navigation: {
                    nextEl: nextEl,
                    prevEl: prevEl
                },
                on: {
                    init: function() {
                        syncThumb(this.realIndex);
                    },
                    slideChange: function() {
                        syncThumb(this.realIndex);
                    }
                }
            });

            thumbs.forEach(function(thumb, index) {
                thumb.addEventListener('click', function() {
                    heroSwiper.slideToLoop(index);
                });
            });
        })();

        /* =========================================================
         * 2. Media Scroll Autoplay
         * 対応HTML:
         * - .js-scroll-autoplay-media
         *   （display_media_mp4() で audio / video に付与）
         *
         * 処理内容:
         * - 画面内に入ったら再生
         * - 画面外に出たら停止
         * - タブ非表示時も停止
         *
         * 注意:
         * - video は muted + playsinline を強制
         * - audio はブラウザ制限で自動再生できない場合あり
         * ========================================================= */
        (function initScrollAutoplayMedia() {
            const mediaEls = root.querySelectorAll('.js-scroll-autoplay-media');

            if (!mediaEls.length || typeof IntersectionObserver === 'undefined') {
                return;
            }

            /* 再生処理
             * - play() は Promise を返すことがある
             * - 自動再生制限エラーは catch で握りつぶす
             */
            function playMedia(el) {
                if (!el || typeof el.play !== 'function') {
                    return;
                }

                const playPromise = el.play();
                if (playPromise && typeof playPromise.catch === 'function') {
                    playPromise.catch(function() {
                        /* 自動再生制限時は何もしない */
                    });
                }
            }

            /* 停止処理 */
            function pauseMedia(el) {
                if (!el || typeof el.pause !== 'function') {
                    return;
                }
                el.pause();
            }

            /* IntersectionObserver:
             * - 要素の 45% 以上が見えたら再生
             * - それ未満なら停止
             */
            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    const el = entry.target;

                    if (entry.isIntersecting && entry.intersectionRatio >= 0.45) {
                        playMedia(el);
                    } else {
                        pauseMedia(el);
                    }
                });
            }, {
                threshold: [0, 0.25, 0.45, 0.7, 1]
            });

            mediaEls.forEach(function(el) {
                /* video は自動再生成功率を上げるため属性を強制 */
                if (el.tagName === 'VIDEO') {
                    el.muted = true;
                    el.setAttribute('muted', '');
                    el.setAttribute('playsinline', '');
                }

                observer.observe(el);
            });

            /* タブが非表示になったら全停止 */
            document.addEventListener('visibilitychange', function() {
                if (document.hidden) {
                    mediaEls.forEach(function(el) {
                        pauseMedia(el);
                    });
                }
            });
        })();

        /* =========================================================
         * 3. YouTube Slider
         * 対応HTML:
         * - .youtube-video-shell
         * - .youtube-video-slider
         * - .youtube-video-nav-prev / .youtube-video-nav-next
         * - .youtube-video-pagination
         *
         * 処理内容:
         * - YouTube セクションの Swiper を初期化
         * - スライド切替時に lite-youtube の iframe をリセット
         * - 1件しかない場合は矢印を隠す
         * ========================================================= */
        (function initYouTubeSlider() {
            const shells = Array.from(root.querySelectorAll('.youtube-video-shell'));

            if (!shells.length) {
                return;
            }

            shells.forEach(function(shell) {
                const sliderEl = shell.querySelector('.youtube-video-slider');
                const prevBtn = shell.querySelector('.youtube-video-nav-prev');
                const nextBtn = shell.querySelector('.youtube-video-nav-next');
                const paginationEl = shell.querySelector('.youtube-video-pagination');

                if (!sliderEl) {
                    return;
                }

                const slideCount = sliderEl.querySelectorAll('.swiper-slide').length;

                if (sliderEl.swiper) {
                    sliderEl.swiper.destroy(true, true);
                }

                /* lite-youtube 内で生成済み iframe を破棄して再生状態をリセット */
                function resetLitePlayers() {
                    sliderEl.querySelectorAll('lite-youtube').forEach(function(player) {
                        if (player.querySelector('iframe')) {
                            const clone = player.cloneNode(true);
                            player.parentNode.replaceChild(clone, player);
                        }
                    });
                }

                /* ナビボタン押下時にドラッグ判定へ誤伝播しないように止める */
                [prevBtn, nextBtn].forEach(function(btn) {
                    if (!btn) return;

                    ['pointerdown', 'mousedown', 'touchstart'].forEach(function(type) {
                        btn.addEventListener(type, function(e) {
                            e.stopPropagation();
                        }, true);
                    });
                });

                if (slideCount <= 1) {
                    if (prevBtn) prevBtn.classList.add('is-hidden');
                    if (nextBtn) nextBtn.classList.add('is-hidden');
                    return;
                }

                new Swiper(sliderEl, {
                    loop: false,
                    rewind: true,
                    speed: 450,
                    slidesPerView: Math.min(1.15, slideCount),
                    slidesPerGroup: 1,
                    spaceBetween: 16,
                    watchOverflow: true,
                    observer: true,
                    observeParents: true,
                    allowTouchMove: true,
                    grabCursor: true,
                    navigation: (prevBtn && nextBtn) ? {
                        nextEl: nextBtn,
                        prevEl: prevBtn
                    } : undefined,
                    pagination: paginationEl ? {
                        el: paginationEl,
                        clickable: true
                    } : undefined,
                    breakpoints: {
                        768: {
                            slidesPerView: Math.min(2.12, slideCount),
                            slidesPerGroup: 1,
                            spaceBetween: 18
                        },
                        1025: {
                            slidesPerView: Math.min(3, slideCount),
                            slidesPerGroup: 1,
                            spaceBetween: 20
                        }
                    },
                    on: {
                        slideChangeTransitionStart: function() {
                            resetLitePlayers();
                        }
                    }
                });
            });
        })();

        /* =========================================================
         * 4. Related Mobile Slider
         * 対応HTML:
         * - .related-mobile-slider-shell
         * - .related-mobile-slider
         * - .related-mobile-nav-prev / .related-mobile-nav-next
         * - .related-mobile-pagination
         *
         * 処理内容:
         * - TB/SP のみ関連記事スライダーを初期化
         * - PC幅では destroy
         * ========================================================= */
        (function initRelatedMobileSlider() {
            const shell = root.querySelector('.related-mobile-slider-shell');
            const sliderEl = shell ? shell.querySelector('.related-mobile-slider') : null;
            const prevBtn = shell ? shell.querySelector('.related-mobile-nav-prev') : null;
            const nextBtn = shell ? shell.querySelector('.related-mobile-nav-next') : null;
            const paginationEl = shell ? shell.querySelector('.related-mobile-pagination') : null;

            if (!shell || !sliderEl) {
                return;
            }

            const slideCount = sliderEl.querySelectorAll('.swiper-slide').length;
            let relatedMobileSwiper = null;

            function destroySlider() {
                if (relatedMobileSwiper) {
                    relatedMobileSwiper.destroy(true, true);
                    relatedMobileSwiper = null;
                }
            }

            function buildSlider() {
                destroySlider();

                /* PC幅では mobile 用 slider を作らない */
                if (window.innerWidth > 1024) {
                    return;
                }

                if (slideCount <= 1) {
                    return;
                }

                relatedMobileSwiper = new Swiper(sliderEl, {
                    slidesPerView: Math.min(1.15, slideCount),
                    slidesPerGroup: 1,
                    spaceBetween: 14,
                    speed: 420,
                    loop: slideCount > 1,
                    watchOverflow: true,
                    observer: true,
                    observeParents: true,
                    navigation: (prevBtn && nextBtn) ? {
                        nextEl: nextBtn,
                        prevEl: prevBtn
                    } : undefined,
                    pagination: paginationEl ? {
                        el: paginationEl,
                        clickable: true
                    } : undefined,
                    breakpoints: {
                        768: {
                            slidesPerView: Math.min(2.12, slideCount),
                            slidesPerGroup: 1,
                            spaceBetween: 18
                        }
                    }
                });
            }

            buildSlider();
            window.addEventListener('resize', debounce(buildSlider, 180), {
                passive: true
            });
        })();

        /* =========================================================
         * 5. Bottom Gallery Slider
         * 対応HTML:
         * - .room-gallery-shell
         * - .img-carousel-wrap.fwi-carousel
         * - .room-gallery-nav-prev / .room-gallery-nav-next
         * - .room-gallery-pagination
         *
         * 処理内容:
         * - 下部ギャラリーの Swiper を初期化
         * - 画面幅に応じて slidesPerView を切替
         * - 1件しかない場合は矢印を隠す
         * ========================================================= */
        (function initBottomGallery() {
            const shell = root.querySelector('.room-gallery-shell');
            const carouselEl = shell ? shell.querySelector('.img-carousel-wrap.fwi-carousel') : null;
            const prevBtn = shell ? shell.querySelector('.room-gallery-nav-prev') : null;
            const nextBtn = shell ? shell.querySelector('.room-gallery-nav-next') : null;
            const paginationEl = shell ? shell.querySelector('.room-gallery-pagination') : null;

            if (!shell || !carouselEl || !prevBtn || !nextBtn) {
                return;
            }

            const slideCount = carouselEl.querySelectorAll('.swiper-slide').length;
            let gallerySwiper = null;

            function getSlidesPerView() {
                if (window.innerWidth >= 1200) return Math.min(4.25, slideCount);
                if (window.innerWidth >= 992) return Math.min(3.25, slideCount);
                if (window.innerWidth >= 769) return Math.min(2.25, slideCount);
                return Math.min(1.15, slideCount);
            }

            function showNav() {
                prevBtn.classList.remove('is-hidden');
                nextBtn.classList.remove('is-hidden');
            }

            function hideNav() {
                prevBtn.classList.add('is-hidden');
                nextBtn.classList.add('is-hidden');
            }

            function build() {
                if (gallerySwiper) {
                    gallerySwiper.destroy(true, true);
                    gallerySwiper = null;
                }

                if (slideCount <= 1) {
                    hideNav();
                    return;
                }

                showNav();

                gallerySwiper = new Swiper(carouselEl, {
                    slidesPerView: getSlidesPerView(),
                    slidesPerGroup: 1,
                    spaceBetween: 12,
                    speed: 420,
                    loop: slideCount > 1,
                    rewind: false,
                    watchOverflow: false,
                    observer: true,
                    observeParents: true,
                    navigation: {
                        nextEl: nextBtn,
                        prevEl: prevBtn
                    },
                    pagination: paginationEl ? {
                        el: paginationEl,
                        clickable: true
                    } : undefined
                });
            }

            build();
            window.addEventListener('resize', debounce(build, 180), {
                passive: true
            });
        })();

        /* =========================================================
         * 6. Lightbox
         * 対応HTML:
         * - #room-lightbox
         * - .js-room-lightbox
         * - .room-lightbox-swiper
         * - .room-lightbox-thumbs-swiper
         * - .room-lightbox-thumb
         *
         * 処理内容:
         * - PC時のみライトボックスを開く
         * - メイン画像と下部サムネイルを同期
         * - 背景クリック / 閉じるボタン / ESC で閉じる
         * ========================================================= */
        (function initLightbox() {
            const lightbox = root.querySelector('#room-lightbox');
            const triggers = Array.from(root.querySelectorAll('.js-room-lightbox'));

            if (!lightbox || !triggers.length) {
                return;
            }

            const closeBtn = lightbox.querySelector('.room-lightbox-close');
            const lightboxSwiperEl = lightbox.querySelector('.room-lightbox-swiper');
            const thumbsSwiperEl = lightbox.querySelector('.room-lightbox-thumbs-swiper');
            const lightboxNextEl = lightbox.querySelector('.room-lightbox .swiper-button-next');
            const lightboxPrevEl = lightbox.querySelector('.room-lightbox .swiper-button-prev');
            const paginationEl = lightbox.querySelector('.room-lightbox .swiper-pagination');
            const thumbButtons = Array.from(lightbox.querySelectorAll('.room-lightbox-thumb'));
            const slideCount = lightbox.querySelectorAll('.room-lightbox-swiper .swiper-slide').length;

            const thumbsSwiper = new Swiper(thumbsSwiperEl, {
                slidesPerView: 'auto',
                spaceBetween: 12,
                freeMode: true,
                watchSlidesProgress: true,
                watchOverflow: true
            });

            const lightboxSwiper = new Swiper(lightboxSwiperEl, {
                loop: false,
                rewind: slideCount > 1,
                speed: 320,
                navigation: {
                    nextEl: lightboxNextEl,
                    prevEl: lightboxPrevEl
                },
                pagination: {
                    el: paginationEl,
                    clickable: true
                },
                on: {
                    init: function() {
                        syncThumbState(this.activeIndex);
                    },
                    slideChange: function() {
                        syncThumbState(this.activeIndex);
                    }
                }
            });

            function syncThumbState(activeIndex) {
                thumbButtons.forEach(function(btn) {
                    const btnIndex = parseInt(btn.dataset.roomThumbIndex || '0', 10);
                    btn.classList.toggle('is-active', btnIndex === activeIndex);
                });

                if (thumbButtons[activeIndex]) {
                    thumbsSwiper.slideTo(Math.max(activeIndex - 1, 0), 300);
                }
            }

            function isDesktopLightbox() {
                return window.innerWidth >= 1025;
            }

            function openLightbox(index) {
                if (!isDesktopLightbox()) {
                    return;
                }

                lightbox.classList.add('is-open');
                lightbox.setAttribute('aria-hidden', 'false');
                document.body.classList.add('is-room-lightbox-open');

                lightboxSwiper.slideTo(index, 0);
                lightboxSwiper.update();
                thumbsSwiper.update();
                syncThumbState(index);
            }

            function closeLightbox() {
                lightbox.classList.remove('is-open');
                lightbox.setAttribute('aria-hidden', 'true');
                document.body.classList.remove('is-room-lightbox-open');
            }

            triggers.forEach(function(trigger) {
                trigger.addEventListener('click', function(e) {
                    const index = parseInt(this.dataset.roomIndex || '0', 10);

                    if (isDesktopLightbox()) {
                        e.preventDefault();
                        openLightbox(index);
                        return;
                    }

                    /* TB/SPでは元画像リンクへ飛ばさない */
                    e.preventDefault();
                });
            });

            thumbButtons.forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const index = parseInt(this.dataset.roomThumbIndex || '0', 10);
                    lightboxSwiper.slideTo(index, 300);
                });
            });

            if (closeBtn) {
                closeBtn.addEventListener('click', closeLightbox);
            }

            lightbox.addEventListener('click', function(e) {
                if (e.target === lightbox) {
                    closeLightbox();
                }
            });

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && lightbox.classList.contains('is-open')) {
                    closeLightbox();
                }
            });
        })();
    });
</script>

<?php get_footer(); ?>