<?php
/**
 * Template Name: 中古住宅リノベページ
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header('77');

$post_id = get_the_ID();

/**
 * ============================================================
 * 中古住宅リノベLP 用 フロント出力
 *
 * 方針:
 * - 既存の wakugui 系CSSクラスを再利用して、CSS追加なしでも形を崩しにくくする
 * - ただし、関数名・メタキー名はこのLP専用に分離する
 * - トップレベル class に used-renovation-page を追加し、後から個別調整しやすくする
 * ============================================================
 */

if (!function_exists('ngrh_page_meta')) {
    function ngrh_page_meta($post_id, $key, $default = '')
    {
        $value = get_post_meta($post_id, $key, true);
        return ($value !== '' && $value !== null) ? $value : $default;
    }
}

if (!function_exists('ngrh_front_meta')) {
    function ngrh_front_meta($post_id, $key, $fallback = '')
    {
        if (function_exists('ngrh_used_renovation_meta_with_default')) {
            return ngrh_used_renovation_meta_with_default($post_id, $key, $fallback);
        }

        return ngrh_page_meta($post_id, $key, $fallback);
    }
}

if (!function_exists('ngrh_page_image_url')) {
    function ngrh_page_image_url($post_id, $key, $size = 'full')
    {
        $attachment_id = absint(get_post_meta($post_id, $key, true));
        if (!$attachment_id) {
            return '';
        }

        return wp_get_attachment_image_url($attachment_id, $size);
    }
}

if (!function_exists('ngrh_first_available_image_alt')) {
    function ngrh_first_available_image_alt($post_id, $keys, $fallback = '')
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

if (!function_exists('ngrh_page_image_alt')) {
    function ngrh_page_image_alt($post_id, $key, $fallback = '')
    {
        return ngrh_first_available_image_alt($post_id, array($key), $fallback);
    }
}

if (!function_exists('ngrh_safe_url')) {
    function ngrh_safe_url($url, $default = '')
    {
        $url = trim((string) $url);
        return $url !== '' ? $url : $default;
    }
}

if (!function_exists('ngrh_btn')) {
    function ngrh_btn($url, $text, $class = 'is-primary')
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

/* ------------------------------------------------------------
 * Hero
 * ------------------------------------------------------------ */
$hero_eyebrow        = ngrh_front_meta($post_id, '_ngrh_hero_eyebrow');
$hero_title          = ngrh_front_meta($post_id, '_ngrh_hero_title');
$hero_text           = ngrh_front_meta($post_id, '_ngrh_hero_text');
$hero_primary_text   = ngrh_front_meta($post_id, '_ngrh_hero_primary_text', '中古住宅・別荘を相談する');
$hero_primary_url    = ngrh_safe_url(ngrh_front_meta($post_id, '_ngrh_hero_primary_url', home_url('/reservation')));
$hero_secondary_text = ngrh_front_meta($post_id, '_ngrh_hero_secondary_text', '施工実例を見る');
$hero_secondary_url  = ngrh_safe_url(ngrh_front_meta($post_id, '_ngrh_hero_secondary_url', home_url('/sekou-jirei')));

$hero_pc     = ngrh_page_image_url($post_id, '_ngrh_hero_image_pc_id', 'full');
$hero_sp     = ngrh_page_image_url($post_id, '_ngrh_hero_image_sp_id', 'full');
$hero_legacy = ngrh_page_image_url($post_id, '_ngrh_hero_image_id', 'full');

$hero_image_pc  = $hero_pc ?: $hero_legacy ?: $hero_sp;
$hero_image_sp  = $hero_sp ?: $hero_pc ?: $hero_legacy;
$hero_image_alt = ngrh_first_available_image_alt($post_id, array('_ngrh_hero_image_pc_id', '_ngrh_hero_image_sp_id', '_ngrh_hero_image_id'), $hero_title);

/* ------------------------------------------------------------
 * 各セクション
 * ------------------------------------------------------------ */
$concept_eyebrow = ngrh_front_meta($post_id, '_ngrh_concept_eyebrow');
$concept_title   = ngrh_front_meta($post_id, '_ngrh_concept_title');
$concept_text    = ngrh_front_meta($post_id, '_ngrh_concept_text');
$concept_image   = ngrh_page_image_url($post_id, '_ngrh_concept_image_id', 'large');

$renovation_eyebrow = ngrh_front_meta($post_id, '_ngrh_renovation_eyebrow');
$renovation_title   = ngrh_front_meta($post_id, '_ngrh_renovation_title');
$renovation_text    = ngrh_front_meta($post_id, '_ngrh_renovation_text');
$renovation_image   = ngrh_page_image_url($post_id, '_ngrh_renovation_image_id', 'large');

$villa_eyebrow = ngrh_front_meta($post_id, '_ngrh_villa_eyebrow');
$villa_title   = ngrh_front_meta($post_id, '_ngrh_villa_title');
$villa_text    = ngrh_front_meta($post_id, '_ngrh_villa_text');
$villa_image   = ngrh_page_image_url($post_id, '_ngrh_villa_image_id', 'large');

$support_eyebrow        = ngrh_front_meta($post_id, '_ngrh_support_eyebrow');
$support_title          = ngrh_front_meta($post_id, '_ngrh_support_title');
$support_text           = ngrh_front_meta($post_id, '_ngrh_support_text');
$support_image          = ngrh_page_image_url($post_id, '_ngrh_support_image_id', 'large');
$support_primary_text   = ngrh_front_meta($post_id, '_ngrh_support_primary_text', '来店予約');
$support_primary_url    = ngrh_safe_url(ngrh_front_meta($post_id, '_ngrh_support_primary_url', home_url('/reservation')));
$support_secondary_text = ngrh_front_meta($post_id, '_ngrh_support_secondary_text', 'お部屋ギャラリーを見る');
$support_secondary_url  = ngrh_safe_url(ngrh_front_meta($post_id, '_ngrh_support_secondary_url', home_url('/room-gallary')));

$flow_eyebrow = ngrh_front_meta($post_id, '_ngrh_flow_eyebrow');
$flow_title   = ngrh_front_meta($post_id, '_ngrh_flow_title');
$flow_steps   = array_values(array_filter(array(
    ngrh_front_meta($post_id, '_ngrh_flow_step_1'),
    ngrh_front_meta($post_id, '_ngrh_flow_step_2'),
    ngrh_front_meta($post_id, '_ngrh_flow_step_3'),
    ngrh_front_meta($post_id, '_ngrh_flow_step_4'),
    ngrh_front_meta($post_id, '_ngrh_flow_step_5'),
), static function ($value) {
    return $value !== '';
}));

$contact_eyebrow  = ngrh_front_meta($post_id, '_ngrh_contact_eyebrow');
$contact_title    = ngrh_front_meta($post_id, '_ngrh_contact_title');
$contact_text     = ngrh_front_meta($post_id, '_ngrh_contact_text');
$contact_cta_text = ngrh_front_meta($post_id, '_ngrh_contact_cta_text');
$contact_cta_url  = ngrh_safe_url(ngrh_front_meta($post_id, '_ngrh_contact_cta_url'));
$contact_image    = ngrh_page_image_url($post_id, '_ngrh_contact_image_id', 'full');

$feature_sections = array(
    array(
        'eyebrow' => $concept_eyebrow,
        'title'   => $concept_title,
        'text'    => $concept_text,
        'image'   => $concept_image,
        'key'     => '_ngrh_concept_image_id',
        'reverse' => false,
    ),
    array(
        'eyebrow' => $renovation_eyebrow,
        'title'   => $renovation_title,
        'text'    => $renovation_text,
        'image'   => $renovation_image,
        'key'     => '_ngrh_renovation_image_id',
        'reverse' => false,
    ),
    array(
        'eyebrow' => $villa_eyebrow,
        'title'   => $villa_title,
        'text'    => $villa_text,
        'image'   => $villa_image,
        'key'     => '_ngrh_villa_image_id',
        'reverse' => true,
    ),
    array(
        'eyebrow'        => $support_eyebrow,
        'title'          => $support_title,
        'text'           => $support_text,
        'image'          => $support_image,
        'key'            => '_ngrh_support_image_id',
        'reverse'        => false,
        'primary_text'   => $support_primary_text,
        'primary_url'    => $support_primary_url,
        'secondary_text' => $support_secondary_text,
        'secondary_url'  => $support_secondary_url,
    ),
);
?>

<div class="used-renovation-page wakugui-page">
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
                $hero_primary_btn   = ngrh_btn($hero_primary_url, $hero_primary_text, 'is-primary');
                $hero_secondary_btn = ngrh_btn($hero_secondary_url, $hero_secondary_text, 'is-secondary');
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
        <?php foreach ($feature_sections as $section) : ?>
            <?php
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
                        $section_primary_btn   = ngrh_btn($section['primary_url'] ?? '', $section['primary_text'] ?? '', 'is-primary');
                        $section_secondary_btn = ngrh_btn($section['secondary_url'] ?? '', $section['secondary_text'] ?? '', 'is-secondary');
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
                                <img src="<?php echo esc_url($section['image']); ?>" alt="<?php echo esc_attr(ngrh_page_image_alt($post_id, $section['key'], $section['title'])); ?>">
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
                        <img src="<?php echo esc_url($contact_image); ?>" alt="<?php echo esc_attr(ngrh_page_image_alt($post_id, '_ngrh_contact_image_id', $contact_title)); ?>">
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
                            <?php echo ngrh_btn($contact_cta_url, $contact_cta_text, 'is-primary'); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </div>
</div>

<?php get_footer(); ?>
