<?php

/**
 * Template Name: 施工実例ページ
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header('77');

$post_id = get_the_ID();

if (!function_exists('ngs_sekou_get_meta')) {
    function ngs_sekou_get_meta($post_id, $key, $default = '')
    {
        $value = get_post_meta($post_id, $key, true);
        return $value !== '' ? $value : $default;
    }
}

if (!function_exists('ngs_sekou_image_url')) {
    function ngs_sekou_image_url($post_id, $key, $size = 'full')
    {
        $attachment_id = absint(get_post_meta($post_id, $key, true));
        if (!$attachment_id) {
            return '';
        }

        $url = wp_get_attachment_image_url($attachment_id, $size);
        return $url ?: '';
    }
}

if (!function_exists('ngs_sekou_attachment_alt')) {
    function ngs_sekou_attachment_alt($attachment_id, $fallback = '')
    {
        $attachment_id = absint($attachment_id);
        if (!$attachment_id) {
            return $fallback;
        }

        $alt = get_post_meta($attachment_id, '_wp_attachment_image_alt', true);
        if ($alt !== '') {
            return $alt;
        }

        $title = get_the_title($attachment_id);
        return $title ?: $fallback;
    }
}

if (!function_exists('ngs_sekou_gallery_ids')) {
    function ngs_sekou_gallery_ids($post_id, $prefix)
    {
        $raw = get_post_meta($post_id, "{$prefix}_gallery", true);
        $ids = [];

        if (is_array($raw)) {
            $ids = $raw;
        } elseif (is_string($raw) && $raw !== '') {
            $ids = explode(',', $raw);
        }

        $ids = array_values(array_filter(array_map('absint', $ids)));
        if (!empty($ids)) {
            return $ids;
        }

        for ($i = 1; $i <= 3; $i++) {
            $legacy_id = absint(get_post_meta($post_id, "{$prefix}_image_{$i}_pc", true));
            if (!$legacy_id) {
                $legacy_id = absint(get_post_meta($post_id, "{$prefix}_image_{$i}_sp", true));
            }
            if ($legacy_id) {
                $ids[] = $legacy_id;
            }
        }

        return array_values(array_unique($ids));
    }
}

if (!function_exists('ngs_sekou_btn')) {
    function ngs_sekou_btn($url, $text, $class = '')
    {
        if (!$url || !$text) {
            return '';
        }

        $class_name = trim('sekou-btn ' . $class);

        return sprintf(
            '<a class="%1$s" href="%2$s">%3$s</a>',
            esc_attr($class_name),
            esc_url($url),
            esc_html($text)
        );
    }
}

$hero_eyebrow = ngs_sekou_get_meta($post_id, 'ngs_sekou_hero_eyebrow', 'Works');
$hero_title   = ngs_sekou_get_meta($post_id, 'ngs_sekou_hero_title', '施工実例');
$hero_lead    = ngs_sekou_get_meta($post_id, 'ngs_sekou_hero_lead', '基礎工事から在来工法、2×4・枠組み工法まで、住まいづくりの実例をご紹介します。');
$hero_pc      = ngs_sekou_image_url($post_id, 'ngs_sekou_hero_image_pc');
$hero_sp      = ngs_sekou_image_url($post_id, 'ngs_sekou_hero_image_sp');

$intro_title  = ngs_sekou_get_meta($post_id, 'ngs_sekou_intro_title', '施工実例について');
$intro_text   = ngs_sekou_get_meta($post_id, 'ngs_sekou_intro_text', '住まいの完成後だけでなく、基礎・構造・外観・内装・外構まで、住まいづくりの流れと雰囲気が分かる施工実例をまとめています。');

$sections = [
    'foundation' => [
        'prefix'  => 'ngs_sekou_foundation',
        'eyebrow' => ngs_sekou_get_meta($post_id, 'ngs_sekou_foundation_eyebrow', 'Foundation'),
        'title'   => ngs_sekou_get_meta($post_id, 'ngs_sekou_foundation_title', '基礎工事'),
        'text'    => ngs_sekou_get_meta($post_id, 'ngs_sekou_foundation_text', '布基礎・ベタ基礎など、住まいを支える基礎工事の実例です。'),
    ],
    'zairai' => [
        'prefix'  => 'ngs_sekou_zairai',
        'eyebrow' => ngs_sekou_get_meta($post_id, 'ngs_sekou_zairai_eyebrow', 'Zairai'),
        'title'   => ngs_sekou_get_meta($post_id, 'ngs_sekou_zairai_title', '在来工法'),
        'text'    => ngs_sekou_get_meta($post_id, 'ngs_sekou_zairai_text', '自由度の高い設計に対応しやすく、和の要素から洋風住宅まで幅広く表現しやすい工法です。'),
    ],
    'wakugumi' => [
        'prefix'  => 'ngs_sekou_wakugumi',
        'eyebrow' => ngs_sekou_get_meta($post_id, 'ngs_sekou_wakugumi_eyebrow', '2×4 / Frame'),
        'title'   => ngs_sekou_get_meta($post_id, 'ngs_sekou_wakugumi_title', '2×4・枠組み工法'),
        'text'    => ngs_sekou_get_meta($post_id, 'ngs_sekou_wakugumi_text', '面で支える構造が特徴で、洋風デザインや整った外観との相性が良い工法です。'),
    ],
];

$cta_title       = ngs_sekou_get_meta($post_id, 'ngs_sekou_cta_title', '住まいづくりのご相談はこちら');
$cta_text        = ngs_sekou_get_meta($post_id, 'ngs_sekou_cta_text', '施工実例を見ながら、ご希望のデザインや暮らし方に合わせた住まいづくりをご相談いただけます。');
$cta_button_text = ngs_sekou_get_meta($post_id, 'ngs_sekou_cta_button_text', 'お問い合わせはこちら');
$cta_button_url  = ngs_sekou_get_meta($post_id, 'ngs_sekou_cta_button_url', home_url('/contact'));
$cta_image       = ngs_sekou_image_url($post_id, 'ngs_sekou_cta_image');
?>

<main class="sekou-page">
    <section class="sekou-hero">
        <?php if ($hero_pc || $hero_sp) : ?>
            <div class="sekou-hero__media">
                <picture>
                    <?php if ($hero_sp) : ?>
                        <source media="(max-width: 767px)" srcset="<?php echo esc_url($hero_sp); ?>">
                    <?php endif; ?>
                    <img src="<?php echo esc_url($hero_pc ?: $hero_sp); ?>" alt="<?php echo esc_attr($hero_title); ?>">
                </picture>
            </div>
        <?php endif; ?>

        <div class="sekou-hero__overlay"></div>

        <div class="sekou-shell sekou-hero__inner">
            <?php if ($hero_eyebrow) : ?>
                <p class="sekou-kicker"><?php echo esc_html($hero_eyebrow); ?></p>
            <?php endif; ?>

            <h1 class="sekou-hero__title"><?php echo esc_html($hero_title); ?></h1>

            <?php if ($hero_lead) : ?>
                <p class="sekou-hero__lead"><?php echo nl2br(esc_html($hero_lead)); ?></p>
            <?php endif; ?>
        </div>
    </section>

    <section class="sekou-section sekou-section--intro">
        <div class="sekou-shell">
            <div class="sekou-intro-card">
                <p class="sekou-kicker">Overview</p>
                <h2 class="sekou-heading"><?php echo esc_html($intro_title); ?></h2>
                <?php if ($intro_text) : ?>
                    <p class="sekou-intro__text"><?php echo nl2br(esc_html($intro_text)); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <?php foreach ($sections as $slug => $section) :
        $gallery_ids = ngs_sekou_gallery_ids($post_id, $section['prefix']);
    ?>
        <section class="sekou-section sekou-section--<?php echo esc_attr($slug); ?>">
            <div class="sekou-shell">
                <div class="sekou-panel">
                    <div class="sekou-band-head">
                        <?php if ($section['eyebrow']) : ?>
                            <p class="sekou-kicker"><?php echo esc_html($section['eyebrow']); ?></p>
                        <?php endif; ?>

                        <div class="sekou-band-title-row">
                            <span class="sekou-band-line" aria-hidden="true"></span>
                            <h2 class="sekou-band-title"><?php echo esc_html($section['title']); ?></h2>
                            <span class="sekou-band-line" aria-hidden="true"></span>
                        </div>

                        <?php if ($section['text']) : ?>
                            <p class="sekou-band-copy"><?php echo nl2br(esc_html($section['text'])); ?></p>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($gallery_ids)) : ?>
                        <div class="sekou-gallery">
                            <?php foreach ($gallery_ids as $attachment_id) :
                                $image_url = wp_get_attachment_image_url($attachment_id, 'large');
                                if (!$image_url) {
                                    continue;
                                }
                                $image_alt = ngs_sekou_attachment_alt($attachment_id, $section['title']);
                            ?>
                                <figure class="sekou-thumb">
                                    <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($image_alt); ?>" loading="lazy">
                                </figure>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    <?php endforeach; ?>

    <section class="sekou-section sekou-section--cta">
        <div class="sekou-shell">
            <div class="sekou-cta">
                <?php if ($cta_image) : ?>
                    <div class="sekou-cta__media">
                        <img src="<?php echo esc_url($cta_image); ?>" alt="<?php echo esc_attr($cta_title); ?>">
                    </div>
                    <div class="sekou-cta__overlay"></div>
                <?php endif; ?>

                <div class="sekou-cta__inner">
                    <p class="sekou-kicker">Contact</p>
                    <h2 class="sekou-cta__title"><?php echo esc_html($cta_title); ?></h2>
                    <?php if ($cta_text) : ?>
                        <p class="sekou-cta__text"><?php echo nl2br(esc_html($cta_text)); ?></p>
                    <?php endif; ?>
                    <div class="sekou-cta__actions">
                        <?php echo ngs_sekou_btn($cta_button_url, $cta_button_text); ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php get_footer(); ?>