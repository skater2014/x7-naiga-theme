<?php
/**
 * =========================================================
 * hub/inc/customhome-cta.php
 *
 * 注文住宅サブページ CTA 表示
 *
 * 方針:
 * - 親 /iezukuri のCTA素材は自動参照しない
 * - 現在ページ自身の post_id に保存された CTA画像 / mp4 / Swiper設定を読む
 * - concept / design-policy / nasu-house / design-office / company / contact / 新規slug で共通利用
 * =========================================================
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('naigai_customhome_cta_meta')) {
    function naigai_customhome_cta_meta($post_id, $key, $default = '')
    {
        $value = get_post_meta((int) $post_id, $key, true);
        return ($value !== '' && $value !== array() && $value !== null) ? $value : $default;
    }
}

if (!function_exists('naigai_customhome_cta_url')) {
    function naigai_customhome_cta_url($post_id, $page_key, $url_key)
    {
        $page_id = absint(get_post_meta($post_id, $page_key, true));
        if ($page_id) {
            $url = get_permalink($page_id);
            if ($url) {
                return $url;
            }
        }

        return trim((string) get_post_meta($post_id, $url_key, true));
    }
}

if (!function_exists('naigai_customhome_cta_media_items')) {
    function naigai_customhome_cta_media_items($post_id)
    {
        $post_id = absint($post_id);

        $items = get_post_meta($post_id, '_hub_ch_cta_media_items', true);

        if (!is_array($items)) {
            $items = array();
        }

        /*
         * fallback:
         * 管理画面の保存形式が image_items / video_items に分かれている場合も読む。
         */
        if (empty($items)) {
            $image_items = get_post_meta($post_id, '_hub_ch_cta_image_items', true);
            $video_items = get_post_meta($post_id, '_hub_ch_cta_video_items', true);

            if (is_array($image_items)) {
                $items = array_merge($items, $image_items);
            }

            if (is_array($video_items)) {
                $items = array_merge($items, $video_items);
            }
        }

        $out = array();

        foreach ($items as $item) {
            if (!is_array($item)) {
                continue;
            }

            $type = isset($item['type']) && $item['type'] === 'video' ? 'video' : 'image';
            $id   = isset($item['attachment_id']) ? absint($item['attachment_id']) : 0;

            if (!$id) {
                continue;
            }

            $url = $type === 'video'
                ? wp_get_attachment_url($id)
                : wp_get_attachment_image_url($id, 'large');

            if (!$url) {
                continue;
            }

            $out[] = array(
                'type' => $type,
                'id'   => $id,
                'url'  => $url,
                'alt'  => get_post_meta($id, '_wp_attachment_image_alt', true),
            );
        }

        return $out;
    }
}

if (!function_exists('naigai_customhome_cta_media_tag')) {
    function naigai_customhome_cta_media_tag($media, $title, $video_controls)
    {
        if (($media['type'] ?? 'image') === 'video') {
            echo '<video class="ch-customhome-cta__media-item ch-customhome-cta__video" autoplay muted loop playsinline preload="metadata"';
            if ($video_controls === '1') {
                echo ' controls';
            }
            echo '>';
            echo '<source src="' . esc_url($media['url']) . '" type="video/mp4">';
            echo '</video>';
            return;
        }

        $alt = !empty($media['alt']) ? $media['alt'] : $title;
        echo '<img class="ch-customhome-cta__media-item ch-customhome-cta__image" src="' . esc_url($media['url']) . '" alt="' . esc_attr($alt) . '">';
    }
}

if (!function_exists('naigai_customhome_cta_shortcode')) {
    function naigai_customhome_cta_shortcode($atts = array())
    {
        global $post;

        $atts = shortcode_atts(array(
            'variant' => '',
            'id'      => 'cta',
        ), $atts, 'customhome_cta');

        $post_id = $post ? (int) $post->ID : get_queried_object_id();
        if (!$post_id) {
            return '';
        }

        $post_obj = get_post($post_id);
        $slug = $post_obj ? (string) $post_obj->post_name : 'subpage';

        $variant = trim((string) $atts['variant']);
        if ($variant === '') {
            $variant = $slug !== '' ? $slug : 'subpage';
        }
        $variant = sanitize_html_class($variant);

        $eyebrow = naigai_customhome_cta_meta($post_id, '_hub_ch_cta_eyebrow', '');
        $title   = naigai_customhome_cta_meta($post_id, '_hub_ch_cta_title', '');
        $text    = naigai_customhome_cta_meta($post_id, '_hub_ch_cta_text', '');

        $btn1_label = naigai_customhome_cta_meta($post_id, '_hub_ch_cta_btn1_label', '');
        $btn1_url   = naigai_customhome_cta_url($post_id, '_hub_ch_cta_btn1_page_id', '_hub_ch_cta_btn1_url');

        $btn2_label = naigai_customhome_cta_meta($post_id, '_hub_ch_cta_btn2_label', '');
        $btn2_url   = naigai_customhome_cta_url($post_id, '_hub_ch_cta_btn2_page_id', '_hub_ch_cta_btn2_url');

        $media_items = naigai_customhome_cta_media_items($post_id);

        $swiper_enabled    = naigai_customhome_cta_meta($post_id, '_hub_ch_cta_swiper_enabled', '0');
        $swiper_nav        = naigai_customhome_cta_meta($post_id, '_hub_ch_cta_swiper_nav', '0');
        $swiper_pagination = naigai_customhome_cta_meta($post_id, '_hub_ch_cta_swiper_pagination', '0');
        $swiper_autoplay   = naigai_customhome_cta_meta($post_id, '_hub_ch_cta_swiper_autoplay', '0');
        $video_controls    = naigai_customhome_cta_meta($post_id, '_hub_ch_cta_video_controls', '0');

        $has_content = ($eyebrow !== '' || $title !== '' || $text !== '' || $btn1_label !== '' || $btn2_label !== '');
        $has_media   = !empty($media_items);

        if (!$has_content && !$has_media) {
            return '';
        }

        $is_swiper = count($media_items) > 1 && $swiper_enabled === '1';

        ob_start();
        ?>
        <section class="ch-nasu-shot-cta ch-customhome-cta ch-customhome-cta--<?php echo esc_attr($variant); ?> <?php echo $has_media ? 'ch-customhome-cta--has-media' : 'ch-customhome-cta--no-media'; ?>" id="<?php echo esc_attr($atts['id']); ?>" data-customhome-cta>
            <?php if ($has_media) : ?>
                <div class="ch-nasu-shot-cta__media" aria-hidden="true">
                    <?php if ($is_swiper) : ?>
                        <div class="swiper ch-cta-swiper ch-nasu-shot-cta__swiper"
                             data-customhome-cta-swiper
                             data-swiper-enabled="<?php echo esc_attr($swiper_enabled); ?>"
                             data-swiper-navigation="<?php echo esc_attr($swiper_nav); ?>"
                             data-swiper-pagination="<?php echo esc_attr($swiper_pagination); ?>"
                             data-swiper-autoplay="<?php echo esc_attr($swiper_autoplay); ?>"
                             data-video-controls="<?php echo esc_attr($video_controls); ?>">
                            <div class="swiper-wrapper">
                                <?php foreach ($media_items as $media) : ?>
                                    <div class="swiper-slide">
                                        <?php naigai_customhome_cta_media_tag($media, $title, $video_controls); ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="swiper-pagination"></div>
                            <div class="swiper-button-prev"></div>
                            <div class="swiper-button-next"></div>
                        </div>
                    <?php else : ?>
                        <?php naigai_customhome_cta_media_tag($media_items[0], $title, $video_controls); ?>
                    <?php endif; ?>
                </div>
                <div class="ch-nasu-shot-cta__overlay" aria-hidden="true"></div>
            <?php endif; ?>

            <?php if ($has_content) : ?>
                <div class="ch-nasu-shot-cta__content">
                    <?php if ($eyebrow !== '') : ?>
                        <span class="ch-eyebrow ch-eyebrow--light"><?php echo esc_html($eyebrow); ?></span>
                    <?php endif; ?>

                    <?php if ($title !== '') : ?>
                        <h3><?php echo esc_html($title); ?></h3>
                    <?php endif; ?>

                    <?php if ($text !== '') : ?>
                        <p><?php echo nl2br(esc_html($text)); ?></p>
                    <?php endif; ?>

                    <?php if (($btn1_label !== '' && $btn1_url !== '') || ($btn2_label !== '' && $btn2_url !== '')) : ?>
                        <div class="ch-hero__actions">
                            <?php if ($btn1_label !== '' && $btn1_url !== '') : ?>
                                <a class="ch-btn ch-btn--primary" href="<?php echo esc_url($btn1_url); ?>"><?php echo esc_html($btn1_label); ?></a>
                            <?php endif; ?>

                            <?php if ($btn2_label !== '' && $btn2_url !== '') : ?>
                                <a class="ch-btn ch-btn--ghost" href="<?php echo esc_url($btn2_url); ?>"><?php echo esc_html($btn2_label); ?></a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </section>
        <?php
        return ob_get_clean();
    }

    add_shortcode('customhome_cta', 'naigai_customhome_cta_shortcode');
}
