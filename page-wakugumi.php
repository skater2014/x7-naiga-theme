<?php

/**
 * Template Name: 北米住宅ページ
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header('77');

$post_id = get_the_ID();

if (!function_exists('ngw_page_meta')) {
    function ngw_page_meta($post_id, $key, $default = '')
    {
        $value = get_post_meta($post_id, $key, true);
        return ($value !== '' && $value !== null) ? $value : $default;
    }
}

if (!function_exists('ngw_front_meta')) {
    function ngw_front_meta($post_id, $key, $fallback = '')
    {
        if (function_exists('ngw_meta_with_default')) {
            return ngw_meta_with_default($post_id, $key, $fallback);
        }

        return ngw_page_meta($post_id, $key, $fallback);
    }
}

if (!function_exists('ngw_page_image_url')) {
    function ngw_page_image_url($post_id, $key, $size = 'full')
    {
        $attachment_id = absint(get_post_meta($post_id, $key, true));
        if (!$attachment_id) {
            return '';
        }

        return wp_get_attachment_image_url($attachment_id, $size);
    }
}

if (!function_exists('ngw_first_available_image_alt')) {
    function ngw_first_available_image_alt($post_id, $keys, $fallback = '')
    {
        foreach ((array) $keys as $key) {
            $attachment_id = absint(get_post_meta($post_id, $key, true));
            if (!$attachment_id) {
                continue;
            }

            $alt = trim((string) get_post_meta($attachment_id, '_wp_attachment_image_alt', true));
            if ($alt !== '') {
                return $alt;
            }

            $title = get_the_title($attachment_id);
            if ($title) {
                return $title;
            }
        }

        return $fallback;
    }
}

if (!function_exists('ngw_page_image_alt')) {
    function ngw_page_image_alt($post_id, $key, $fallback = '')
    {
        return ngw_first_available_image_alt($post_id, [$key], $fallback);
    }
}

if (!function_exists('ngw_safe_url')) {
    function ngw_safe_url($url, $default = '')
    {
        $url = trim((string) $url);
        return $url !== '' ? $url : $default;
    }
}

if (!function_exists('ngw_btn')) {
    function ngw_btn($url, $text, $class = 'is-primary')
    {
        if (!$url || !$text) {
            return '';
        }

        return sprintf(
            '<a class="ngw-btn wakugui-btn %1$s" href="%2$s">%3$s</a>',
            esc_attr($class),
            esc_url($url),
            esc_html($text)
        );
    }
}

$hero_eyebrow        = ngw_front_meta($post_id, 'hero_eyebrow');
$hero_title          = ngw_front_meta($post_id, 'hero_title');
$hero_text           = ngw_front_meta($post_id, 'hero_text');
$hero_primary_text   = ngw_front_meta($post_id, 'hero_primary_text', '来店予約');
$hero_primary_url    = ngw_safe_url(ngw_front_meta($post_id, 'hero_primary_url', home_url('/reservation')));
$hero_secondary_text = ngw_front_meta($post_id, 'hero_secondary_text', 'お部屋のギャラリーを見る');
$hero_secondary_url  = ngw_safe_url(ngw_front_meta($post_id, 'hero_secondary_url', home_url('/room-gallary')));

$hero_pc     = ngw_page_image_url($post_id, 'hero_image_pc_id', 'full');
$hero_sp     = ngw_page_image_url($post_id, 'hero_image_sp_id', 'full');
$hero_legacy = ngw_page_image_url($post_id, 'hero_image_id', 'full');

$hero_image_pc  = $hero_pc ?: $hero_legacy ?: $hero_sp;
$hero_image_sp  = $hero_sp ?: $hero_pc ?: $hero_legacy;
$hero_image_alt = ngw_first_available_image_alt($post_id, ['hero_image_pc_id', 'hero_image_sp_id', 'hero_image_id'], $hero_title);

$guide_eyebrow = ngw_front_meta($post_id, 'guide_eyebrow');
$guide_title   = ngw_front_meta($post_id, 'guide_title');
$guide_text    = ngw_front_meta($post_id, 'guide_text');
$guide_image   = ngw_page_image_url($post_id, 'guide_image_id', 'large');

$design_eyebrow = ngw_front_meta($post_id, 'design_eyebrow');
$design_title   = ngw_front_meta($post_id, 'design_title');
$design_text    = ngw_front_meta($post_id, 'design_text');
$design_image   = ngw_page_image_url($post_id, 'design_image_id', 'large');

$performance_eyebrow = ngw_front_meta($post_id, 'performance_eyebrow');
$performance_title   = ngw_front_meta($post_id, 'performance_title');
$performance_text    = ngw_front_meta($post_id, 'performance_text');
$performance_image   = ngw_page_image_url($post_id, 'performance_image_id', 'large');

$lifestyle_eyebrow        = ngw_front_meta($post_id, 'lifestyle_eyebrow');
$lifestyle_title          = ngw_front_meta($post_id, 'lifestyle_title');
$lifestyle_text           = ngw_front_meta($post_id, 'lifestyle_text');
$lifestyle_image          = ngw_page_image_url($post_id, 'lifestyle_image_id', 'large');
$lifestyle_primary_text   = ngw_front_meta($post_id, 'lifestyle_primary_text', '施工実例を見る');
$lifestyle_primary_url    = ngw_safe_url(ngw_front_meta($post_id, 'lifestyle_primary_url', home_url('/sekou-jirei')));
$lifestyle_secondary_text = ngw_front_meta($post_id, 'lifestyle_secondary_text', '自然素材の住まいを見る');
$lifestyle_secondary_url  = ngw_safe_url(ngw_front_meta($post_id, 'lifestyle_secondary_url', home_url('/zairai-kouhou')));

$flow_eyebrow = ngw_front_meta($post_id, 'flow_eyebrow');
$flow_title   = ngw_front_meta($post_id, 'flow_title');
$flow_steps   = array_values(array_filter(array(
    ngw_front_meta($post_id, 'flow_step_1'),
    ngw_front_meta($post_id, 'flow_step_2'),
    ngw_front_meta($post_id, 'flow_step_3'),
    ngw_front_meta($post_id, 'flow_step_4'),
    ngw_front_meta($post_id, 'flow_step_5'),
), static function ($value) {
    return $value !== '';
}));

$contact_eyebrow   = ngw_front_meta($post_id, 'contact_eyebrow');
$contact_title     = ngw_front_meta($post_id, 'contact_title');
$contact_text      = ngw_front_meta($post_id, 'contact_text');
$contact_cta_text  = ngw_front_meta($post_id, 'contact_cta_text');
$contact_cta_url   = ngw_safe_url(ngw_front_meta($post_id, 'contact_cta_url'));
$contact_image     = ngw_page_image_url($post_id, 'contact_image_id', 'full');

$feature_sections = [
    [
        'eyebrow' => $guide_eyebrow,
        'title'   => $guide_title,
        'text'    => $guide_text,
        'image'   => $guide_image,
        'key'     => 'guide_image_id',
        'reverse' => false,
    ],
    [
        'eyebrow' => $design_eyebrow,
        'title'   => $design_title,
        'text'    => $design_text,
        'image'   => $design_image,
        'key'     => 'design_image_id',
        'reverse' => false,
    ],
    [
        'eyebrow' => $performance_eyebrow,
        'title'   => $performance_title,
        'text'    => $performance_text,
        'image'   => $performance_image,
        'key'     => 'performance_image_id',
        'reverse' => true,
    ],
    [
        'eyebrow'        => $lifestyle_eyebrow,
        'title'          => $lifestyle_title,
        'text'           => $lifestyle_text,
        'image'          => $lifestyle_image,
        'key'            => 'lifestyle_image_id',
        'reverse'        => false,
        'primary_text'   => $lifestyle_primary_text,
        'primary_url'    => $lifestyle_primary_url,
        'secondary_text' => $lifestyle_secondary_text,
        'secondary_url'  => $lifestyle_secondary_url,
    ],
];
?>

<div class="wakugui-page">
    <section class="wakugui-hero-shell">
        <div class="wakugui-hero">
            <?php if ($hero_image_pc || $hero_image_sp) : ?>
                <div class="wakugui-hero__media">
                    <picture>
                        <?php if ($hero_image_sp) : ?>
                            <source media="(max-width: 767px)" srcset="<?php echo esc_url($hero_image_sp); ?>">
                        <?php endif; ?>
                        <img src="<?php echo esc_url($hero_image_pc ?: $hero_image_sp); ?>" alt="<?php echo esc_attr($hero_image_alt); ?>">
                    </picture>
                </div>
            <?php endif; ?>

            <div class="wakugui-hero__overlay"></div>

            <div class="wakugui-hero__content">
                <?php if ($hero_eyebrow) : ?>
                    <p class="wakugui-eyebrow"><?php echo esc_html($hero_eyebrow); ?></p>
                <?php endif; ?>

                <h1 class="wakugui-hero__title"><?php echo esc_html($hero_title); ?></h1>

                <?php if ($hero_text) : ?>
                    <p class="wakugui-hero__text"><?php echo esc_html($hero_text); ?></p>
                <?php endif; ?>

                <?php
                $hero_primary_btn   = ngw_btn($hero_primary_url, $hero_primary_text, 'is-primary');
                $hero_secondary_btn = ngw_btn($hero_secondary_url, $hero_secondary_text, 'is-secondary');
                ?>
                <?php if ($hero_primary_btn || $hero_secondary_btn) : ?>
                    <div class="wakugui-hero__actions">
                        <?php echo $hero_primary_btn; ?>
                        <?php echo $hero_secondary_btn; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <div class="wakugui-inner">
        <?php foreach ($feature_sections as $section) :
            if ($section['title'] === '' && $section['text'] === '' && $section['image'] === '') {
                continue;
            }
            $panel_class = $section['reverse'] ? 'wakugui-split is-reverse' : 'wakugui-split';
        ?>
            <section class="wakugui-section">
                <div class="<?php echo esc_attr($panel_class); ?>">
                    <div class="wakugui-card wakugui-card--copy">
                        <?php if ($section['eyebrow']) : ?>
                            <p class="wakugui-eyebrow is-accent"><?php echo esc_html($section['eyebrow']); ?></p>
                        <?php endif; ?>

                        <div class="wakugui-head">
                            <h2 class="wakugui-head__title"><?php echo esc_html($section['title']); ?></h2>
                            <?php if ($section['text']) : ?>
                                <p class="wakugui-head__text"><?php echo esc_html($section['text']); ?></p>
                            <?php endif; ?>
                        </div>

                        <?php
                        $section_primary_btn   = ngw_btn($section['primary_url'] ?? '', $section['primary_text'] ?? '', 'is-primary');
                        $section_secondary_btn = ngw_btn($section['secondary_url'] ?? '', $section['secondary_text'] ?? '', 'is-secondary');
                        ?>
                        <?php if ($section_primary_btn || $section_secondary_btn) : ?>
                            <div class="wakugui-card__actions">
                                <?php echo $section_primary_btn; ?>
                                <?php echo $section_secondary_btn; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="wakugui-card wakugui-card--media">
                        <div class="wakugui-card__media-frame">
                            <?php if ($section['image']) : ?>
                                <img src="<?php echo esc_url($section['image']); ?>" alt="<?php echo esc_attr(ngw_page_image_alt($post_id, $section['key'], $section['title'])); ?>">
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </section>
        <?php endforeach; ?>

        <?php if (!empty($flow_steps)) : ?>
            <section class="wakugui-section">
                <div class="wakugui-flow-panel">
                    <div class="wakugui-head wakugui-head--center">
                        <?php if ($flow_eyebrow) : ?>
                            <p class="wakugui-eyebrow is-accent"><?php echo esc_html($flow_eyebrow); ?></p>
                        <?php endif; ?>
                        <h2 class="wakugui-head__title"><?php echo esc_html($flow_title); ?></h2>
                    </div>

                    <div class="wakugui-flow">
                        <?php $step_index = 1; ?>
                        <?php foreach ($flow_steps as $flow_step) : ?>
                            <div class="wakugui-flow__item">
                                <span class="wakugui-flow__step">STEP <?php echo (int) $step_index; ?></span>
                                <span class="wakugui-flow__label"><?php echo esc_html($flow_step); ?></span>
                            </div>
                            <?php $step_index++; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <section class="wakugui-section wakugui-section--contact">
            <div class="wakugui-contact<?php echo $contact_image ? ' wakugui-contact--has-image' : ''; ?>">
                <?php if ($contact_image) : ?>
                    <div class="wakugui-contact__media">
                        <img src="<?php echo esc_url($contact_image); ?>" alt="<?php echo esc_attr(ngw_page_image_alt($post_id, 'contact_image_id', $contact_title)); ?>">
                    </div>
                    <div class="wakugui-contact__overlay"></div>
                <?php endif; ?>

                <div class="wakugui-contact__inner">
                    <?php if ($contact_eyebrow) : ?>
                        <p class="wakugui-eyebrow is-accent"><?php echo esc_html($contact_eyebrow); ?></p>
                    <?php endif; ?>

                    <div class="wakugui-head wakugui-head--center">
                        <h2 class="wakugui-head__title"><?php echo esc_html($contact_title); ?></h2>
                        <?php if ($contact_text) : ?>
                            <p class="wakugui-head__text"><?php echo esc_html($contact_text); ?></p>
                        <?php endif; ?>
                    </div>

                    <?php if ($contact_cta_url && $contact_cta_text) : ?>
                        <div class="wakugui-contact__actions">
                            <?php echo ngw_btn($contact_cta_url, $contact_cta_text, 'is-primary'); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </div>
</div>

<?php get_footer(); ?>