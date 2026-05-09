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

        $title = sanitize_text_field(naigai_iez_hero_meta($post_id, '_ch_hero_title', get_the_title($post_id)));
        $lead = sanitize_textarea_field(naigai_iez_hero_meta($post_id, '_ch_hero_lead', ''));
        $eyebrow = sanitize_text_field(naigai_iez_hero_meta($post_id, '_ch_hero_eyebrow', ''));

        $motion = naigai_iez_sanitize_hero_motion(naigai_iez_hero_meta($post_id, '_ch_hero_motion', ''));
        $caption_motion = naigai_iez_sanitize_caption_motion(naigai_iez_hero_meta($post_id, '_ch_hero_caption_motion', 'none'));

        $gallery_ids = naigai_iez_hero_meta($post_id, '_ch_hero_gallery_ids', '');
        $items = naigai_iez_hero_items($post_id, $gallery_ids, $title, $lead, $motion, $caption_motion);

        if (empty($items)) {
            $image_id = absint(naigai_iez_hero_meta($post_id, '_ch_hero_image_id', 0));

            if (!$image_id) {
                $image_id = get_post_thumbnail_id($post_id);
            }

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
