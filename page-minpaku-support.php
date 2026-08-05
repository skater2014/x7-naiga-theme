<?php

/**
 * Template Name: 民泊運営サポートLP
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header('77');

/*
 * ============================================================
 * NAIGAI_BACKLINK_MINPAKU_20260803
 * 民泊トップ「前のページへ戻る」
 * ============================================================
 *
 * フロントページなど自サイト内から /minpaku へ来た時だけ表示。
 * URL直打ち・ブックマーク・外部サイトから来た場合は表示しない。
 *
 * CSSは新しく作らず、民泊詳細ですでに使用している
 * .mnpk-back-wrap / .mnpk-back-link をそのまま使用する。
 *
 * wp_get_referer() で実際の直前ページを取得し、
 * クリック時は history.back() で本当に1つ前へ戻す。
 */
$naigai_minpaku_referer = wp_get_referer();

$naigai_minpaku_home_host = wp_parse_url(
    home_url('/'),
    PHP_URL_HOST
);

$naigai_minpaku_ref_host = $naigai_minpaku_referer
    ? wp_parse_url($naigai_minpaku_referer, PHP_URL_HOST)
    : '';

$naigai_minpaku_current = home_url(
    isset($_SERVER['REQUEST_URI'])
        ? wp_unslash($_SERVER['REQUEST_URI'])
        : '/'
);

$naigai_minpaku_show_back = (
    $naigai_minpaku_referer
    && $naigai_minpaku_home_host
    && $naigai_minpaku_ref_host
    && strtolower($naigai_minpaku_home_host) === strtolower($naigai_minpaku_ref_host)
    && untrailingslashit($naigai_minpaku_referer) !== untrailingslashit($naigai_minpaku_current)
);

if ($naigai_minpaku_show_back) {
    echo '<div class="mnpk-back-wrap">';
    echo '<a class="mnpk-back-link" href="' . esc_url($naigai_minpaku_referer) . '" onclick="window.history.back(); return false;">';
    echo '← 前のページへ戻る';
    echo '</a>';
    echo '</div>';
}


$post_id = get_the_ID();

/**
 * ============================================================
 * 民泊運営サポートLP 用 フロント出力
 *
 * 方針:
 * - 中古住宅リノベLPと同じ wakugui 系レイアウトを流用する
 * - CSS追加なしで崩れにくい構成に寄せる
 * - 民泊向けに文言、メタキー、ボタン導線だけを差し替える
 * ============================================================
 */

if (!function_exists('mps_page_meta')) {
    function mps_page_meta($post_id, $key, $default = '')
    {
        $value = get_post_meta($post_id, $key, true);
        return ($value !== '' && $value !== null) ? $value : $default;
    }
}

if (!function_exists('mps_front_meta')) {
    function mps_front_meta($post_id, $key, $fallback = '')
    {
        if (function_exists('mps_meta_with_default')) {
            return mps_meta_with_default($post_id, $key, $fallback);
        }

        return mps_page_meta($post_id, $key, $fallback);
    }
}

if (!function_exists('mps_page_image_url')) {
    function mps_page_image_url($post_id, $key, $size = 'full')
    {
        $attachment_id = absint(get_post_meta($post_id, $key, true));
        if (!$attachment_id) {
            return '';
        }

        return wp_get_attachment_image_url($attachment_id, $size);
    }
}

if (!function_exists('mps_first_available_image_alt')) {
    function mps_first_available_image_alt($post_id, $keys, $fallback = '')
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

if (!function_exists('mps_page_image_alt')) {
    function mps_page_image_alt($post_id, $key, $fallback = '')
    {
        return mps_first_available_image_alt($post_id, array($key), $fallback);
    }
}

if (!function_exists('mps_safe_url')) {
    function mps_safe_url($url, $default = '')
    {
        $url = trim((string) $url);
        return $url !== '' ? $url : $default;
    }
}

if (!function_exists('mps_btn')) {
    function mps_btn($url, $text, $class = 'is-primary')
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
$hero_eyebrow        = mps_front_meta($post_id, '_mps_hero_eyebrow', 'MINPAKU SUPPORT');
$hero_title          = mps_front_meta($post_id, '_mps_hero_title', '首都圏から那須で民泊運営を始めたい方へ');
$hero_text           = mps_front_meta($post_id, '_mps_hero_text', '別荘、中古住宅、遊休不動産、土地活用まで。那須での民泊運営を考える方へ、物件探しから改修、建築、運営準備まで一貫してご案内します。');
$hero_primary_text   = mps_front_meta($post_id, '_mps_hero_primary_text', '民泊運営を相談する');
$hero_primary_url    = mps_safe_url(mps_front_meta($post_id, '_mps_hero_primary_url', home_url('/contact/')));
$hero_secondary_text = mps_front_meta($post_id, '_mps_hero_secondary_text', '宿泊施設を見る');
$hero_secondary_url  = mps_safe_url(mps_front_meta($post_id, '_mps_hero_secondary_url', home_url('/minpaku-stay/')));

$hero_pc     = mps_page_image_url($post_id, '_mps_hero_image_pc_id', 'full');
$hero_sp     = mps_page_image_url($post_id, '_mps_hero_image_sp_id', 'full');
$hero_legacy = mps_page_image_url($post_id, '_mps_hero_image_id', 'full');

$hero_image_pc  = $hero_pc ?: $hero_legacy ?: $hero_sp;
$hero_image_sp  = $hero_sp ?: $hero_pc ?: $hero_legacy;
$hero_image_alt = mps_first_available_image_alt($post_id, array('_mps_hero_image_pc_id', '_mps_hero_image_sp_id', '_mps_hero_image_id'), $hero_title);

/* ------------------------------------------------------------
 * 各セクション
 * ------------------------------------------------------------ */
$concept_eyebrow = mps_front_meta($post_id, '_mps_concept_eyebrow', 'Concept');
$concept_title   = mps_front_meta($post_id, '_mps_concept_title', '那須での民泊運営を、物件探しから整理したい方へ');
$concept_text    = mps_front_meta($post_id, '_mps_concept_text', '東京圏から那須で宿泊事業を始めたい方に向けて、立地、物件、改修、建築、運営方法まで全体を整理しながらご案内します。');
$concept_image   = mps_page_image_url($post_id, '_mps_concept_image_id', 'large');

$operation_eyebrow = mps_front_meta($post_id, '_mps_operation_eyebrow', 'Operation');
$operation_title   = mps_front_meta($post_id, '_mps_operation_title', '自社で宿泊運営ができる体制があります');
$operation_text    = mps_front_meta($post_id, '_mps_operation_text', '物件のご紹介だけでなく、自社でも宿泊運営に取り組んでいるからこそ、実際の運営を踏まえた視点でご相談いただけます。');
$operation_image   = mps_page_image_url($post_id, '_mps_operation_image_id', 'large');

$detail_eyebrow = mps_front_meta($post_id, '_mps_detail_eyebrow', 'Stay detail');
$detail_title   = mps_front_meta($post_id, '_mps_detail_title', '民泊詳細ページから予約・オンライン決済へつなげます');
$detail_text    = mps_front_meta($post_id, '_mps_detail_text', '宿泊施設の特徴、設備、利用方法、料金案内を整理した民泊詳細ページを用意し、予約からオンライン決済までつながる導線を整えます。');
$detail_image   = mps_page_image_url($post_id, '_mps_detail_image_id', 'large');
$detail_primary_text   = mps_front_meta($post_id, '_mps_detail_primary_text', '宿泊施設を見る');
$detail_primary_url    = mps_safe_url(mps_front_meta($post_id, '_mps_detail_primary_url', home_url('/minpaku-stay/')));
$detail_secondary_text = mps_front_meta($post_id, '_mps_detail_secondary_text', 'オンライン決済を見る');
$detail_secondary_url  = mps_safe_url(mps_front_meta($post_id, '_mps_detail_secondary_url', home_url('/minpaku-stay/room/test-minpaku1/')));

$support_eyebrow        = mps_front_meta($post_id, '_mps_support_eyebrow', 'Support');
$support_title          = mps_front_meta($post_id, '_mps_support_title', '物件探し・改修・建築までまとめて相談できます');
$support_text           = mps_front_meta($post_id, '_mps_support_text', '中古住宅を活かす方法、別荘を整える方法、土地から新たに建てる方法など、条件に合わせて物件探しから改修、建築までご相談いただけます。');
$support_image          = mps_page_image_url($post_id, '_mps_support_image_id', 'large');
$support_primary_text   = mps_front_meta($post_id, '_mps_support_primary_text', '物件一覧を見る');
$support_primary_url    = mps_safe_url(mps_front_meta($post_id, '_mps_support_primary_url', home_url('/fudosan-column/')));
$support_secondary_text = mps_front_meta($post_id, '_mps_support_secondary_text', '民泊運営を相談する');
$support_secondary_url  = mps_safe_url(mps_front_meta($post_id, '_mps_support_secondary_url', home_url('/contact/')));

$flow_eyebrow = mps_front_meta($post_id, '_mps_flow_eyebrow', 'Flow');
$flow_title   = mps_front_meta($post_id, '_mps_flow_title', '運営開始までの流れ');
$flow_steps   = array_values(array_filter(array(
    mps_front_meta($post_id, '_mps_flow_step_1', '目的を整理する'),
    mps_front_meta($post_id, '_mps_flow_step_2', '物件や土地を検討する'),
    mps_front_meta($post_id, '_mps_flow_step_3', '行政・自治体への申請を進める'),
    mps_front_meta($post_id, '_mps_flow_step_4', '必要な設備を整える'),
    mps_front_meta($post_id, '_mps_flow_step_5', '予約・オンライン決済を設定する'),
), static function ($value) {
    return $value !== '';
}));

$contact_eyebrow  = mps_front_meta($post_id, '_mps_contact_eyebrow', 'Contact');
$contact_title    = mps_front_meta($post_id, '_mps_contact_title', '那須で民泊運営を考える方へ');
$contact_text     = mps_front_meta($post_id, '_mps_contact_text', '別荘、中古住宅、遊休不動産、投資物件、建築、改修、運営準備まで。那須での民泊運営を具体的に考えたい方は、お気軽にご相談ください。');
$contact_cta_text = mps_front_meta($post_id, '_mps_contact_cta_text', '民泊運営を相談する');
$contact_cta_url  = mps_safe_url(mps_front_meta($post_id, '_mps_contact_cta_url', home_url('/contact/')));
$contact_image    = mps_page_image_url($post_id, '_mps_contact_image_id', 'full');

$feature_sections = array(
    array(
        'eyebrow' => $concept_eyebrow,
        'title'   => $concept_title,
        'text'    => $concept_text,
        'image'   => $concept_image,
        'key'     => '_mps_concept_image_id',
        'reverse' => false,
    ),
    array(
        'eyebrow' => $operation_eyebrow,
        'title'   => $operation_title,
        'text'    => $operation_text,
        'image'   => $operation_image,
        'key'     => '_mps_operation_image_id',
        'reverse' => false,
    ),
    array(
        'eyebrow'        => $detail_eyebrow,
        'title'          => $detail_title,
        'text'           => $detail_text,
        'image'          => $detail_image,
        'key'            => '_mps_detail_image_id',
        'reverse'        => true,
        'primary_text'   => $detail_primary_text,
        'primary_url'    => $detail_primary_url,
        'secondary_text' => $detail_secondary_text,
        'secondary_url'  => $detail_secondary_url,
    ),
    array(
        'eyebrow'        => $support_eyebrow,
        'title'          => $support_title,
        'text'           => $support_text,
        'image'          => $support_image,
        'key'            => '_mps_support_image_id',
        'reverse'        => false,
        'primary_text'   => $support_primary_text,
        'primary_url'    => $support_primary_url,
        'secondary_text' => $support_secondary_text,
        'secondary_url'  => $support_secondary_url,
    ),
);
?>

<div class="minpaku-support-page wakugui-page">
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
                $hero_primary_btn   = mps_btn($hero_primary_url, $hero_primary_text, 'is-primary');
                $hero_secondary_btn = mps_btn($hero_secondary_url, $hero_secondary_text, 'is-secondary');
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
                        $section_primary_btn   = mps_btn($section['primary_url'] ?? '', $section['primary_text'] ?? '', 'is-primary');
                        $section_secondary_btn = mps_btn($section['secondary_url'] ?? '', $section['secondary_text'] ?? '', 'is-secondary');
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
                                <img src="<?php echo esc_url($section['image']); ?>" alt="<?php echo esc_attr(mps_page_image_alt($post_id, $section['key'], $section['title'])); ?>">
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
                        <img src="<?php echo esc_url($contact_image); ?>" alt="<?php echo esc_attr(mps_page_image_alt($post_id, '_mps_contact_image_id', $contact_title)); ?>">
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
                            <?php echo mps_btn($contact_cta_url, $contact_cta_text, 'is-primary'); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </div>
</div>

<?php get_footer(); ?>