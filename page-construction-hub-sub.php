<?php

/**
 * =========================================================
 * page-construction-hub-sub.php
 *
 * Template Name: Construction Hub Sub Page
 * Template Post Type: page
 *
 * 対象URL:
 * - /iezukuri/concept/
 * - /iezukuri/design-policy/
 * - /iezukuri/nasu-house/
 * - /iezukuri/design-office/
 * - /iezukuri/company/
 * - /iezukuri/contact/
 *
 * 役割:
 * - 家づくりサブページ共通テンプレート
 * - hero / localnav / 本文テンプレート呼び出しを管理する
 *
 * 注意:
 * - CSS/JS はここに書かない
 * - CSS/JS は hub/pages/iezukuri/inc/enqueue.php で管理する
 * =========================================================
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * =========================================================
 * 基本情報
 * =========================================================
 */
$post_id      = get_the_ID();
$post_slug    = get_post_field('post_name', $post_id);
$template_key = trim((string) get_post_meta($post_id, '_ch_subpage_template', true));
$title        = get_the_title($post_id);

/**
 * =========================================================
 * hero メタ
 * =========================================================
 */
$hero_kicker = trim((string) get_post_meta($post_id, '_ch_hero_kicker', true));
if ($hero_kicker === '') {
    $hero_kicker = 'CUSTOM HOME HUB';
}

$hero_title = trim((string) get_post_meta($post_id, '_ch_hero_title', true));
if ($hero_title === '') {
    $hero_title = $title;
}

$lead = trim((string) get_post_meta($post_id, '_ch_hero_lead', true));
if ($lead === '' && has_excerpt($post_id)) {
    $lead = get_the_excerpt($post_id);
}

$hero_image_id = (int) get_post_meta($post_id, '_ch_hero_image_id', true);
$hero_image    = $hero_image_id
    ? wp_get_attachment_image_url($hero_image_id, 'full')
    : get_the_post_thumbnail_url($post_id, 'full');

$parent_url = trim((string) get_post_meta($post_id, '_ch_back_url', true));
if ($parent_url === '') {
    $parent_url = home_url('/iezukuri/');
}

$contact_url = trim((string) get_post_meta($post_id, '_ch_contact_url', true));
if ($contact_url === '') {
    $contact_url = home_url('/iezukuri/contact/');
}

$hero_primary_label = trim((string) get_post_meta($post_id, '_ch_hero_primary_label', true));
if ($hero_primary_label === '') {
    $hero_primary_label = '無料相談・資料請求';
}

$hero_secondary_label = trim((string) get_post_meta($post_id, '_ch_hero_secondary_label', true));
if ($hero_secondary_label === '') {
    $hero_secondary_label = '注文住宅トップへ戻る';
}

/**
 * =========================================================
 * 本文テンプレート
 *
 * 優先順:
 * 1. _ch_subpage_template
 * 2. 固定ページ slug
 *
 * 注意:
 * - file_exists() で存在確認してから require する
 * - 無い場合は通常本文 the_content() を表示する
 * =========================================================
 */
$template_map = array(
    'concept'       => get_template_directory() . '/hub/pages/iezukuri/templates/page-concept.php',
    'design-policy' => get_template_directory() . '/hub/pages/iezukuri/templates/page-design-policy.php',
    'nasu-house'    => get_template_directory() . '/hub/pages/iezukuri/templates/page-nasu-house.php',
    'design-office' => get_template_directory() . '/hub/pages/iezukuri/templates/page-design-office.php',
    'company'       => get_template_directory() . '/hub/pages/iezukuri/templates/page-company.php',
    'contact'       => get_template_directory() . '/hub/pages/iezukuri/templates/page-contact.php',
);

$lookup_key   = $template_key !== '' ? $template_key : $post_slug;
$partial_file = '';

if (isset($template_map[$lookup_key]) && file_exists($template_map[$lookup_key])) {
    $partial_file = $template_map[$lookup_key];
}

/**
 * =========================================================
 * 共通表示オプション
 * =========================================================
 */
$layout_mode = trim((string) get_post_meta($post_id, '_ch_layout_mode', true));
if ($layout_mode === '') {
    $layout_mode = 'stack';
}

$layout_class = 'is-layout-' . sanitize_html_class($layout_mode);

$use_parent_works = (string) get_post_meta($post_id, '_ch_use_parent_works', true) === '1';
$use_parent_flow  = (string) get_post_meta($post_id, '_ch_use_parent_flow', true) === '1';
$show_common_form = (string) get_post_meta($post_id, '_ch_show_common_form', true) === '1';
$show_common_faq  = (string) get_post_meta($post_id, '_ch_show_common_faq', true) === '1';

$common_form_shortcode = trim((string) get_post_meta($post_id, '_ch_common_form_shortcode', true));

/**
 * =========================================================
 * FAQ メタ
 * =========================================================
 */
$faq_items = array();

for ($i = 1; $i <= 5; $i++) {
    $q = trim((string) get_post_meta($post_id, '_ch_faq_' . $i . '_q', true));
    $a = trim((string) get_post_meta($post_id, '_ch_faq_' . $i . '_a', true));

    if ($q !== '' && $a !== '') {
        $faq_items[] = array(
            'q' => $q,
            'a' => $a,
        );
    }
}

$show_common_hero = true;

/**
 * =========================================================
 * Header
 * =========================================================
 */
get_header('customhome');
?>

<main
    class="hub-customhome-page hub-customhome-subpage hub-customhome-subpage--<?php echo esc_attr($post_slug); ?> <?php echo $template_key !== '' ? 'hub-customhome-template--' . esc_attr($template_key) : ''; ?> <?php echo esc_attr($layout_class); ?>"
    data-localnav-mode="hero_to_cta">
    <?php if ($show_common_hero) : ?>
        <section class="ch-hero ch-fullbleed ch-subpage-hero">
            <div class="ch-hero__media">
                <?php if ($hero_image) : ?>
                    <img
                        class="ch-hero__image"
                        src="<?php echo esc_url($hero_image); ?>"
                        alt="<?php echo esc_attr($hero_title); ?>">
                <?php endif; ?>

                <div class="ch-hero__overlay"></div>
            </div>

            <div class="ch-shell ch-hero__inner">
                <div class="ch-hero__content">
                    <div class="ch-kicker"><?php echo esc_html($hero_kicker); ?></div>

                    <h1 class="ch-hero__title"><?php echo esc_html($hero_title); ?></h1>

                    <?php if ($lead !== '') : ?>
                        <p class="ch-hero__lead"><?php echo nl2br(esc_html($lead)); ?></p>
                    <?php endif; ?>

                    <div class="ch-hero__actions">
                        <a class="ch-btn ch-btn--primary" href="<?php echo esc_url($contact_url); ?>">
                            <?php echo esc_html($hero_primary_label); ?>
                        </a>

                        <a class="ch-btn ch-btn--ghost" href="<?php echo esc_url($parent_url); ?>">
                            <?php echo esc_html($hero_secondary_label); ?>
                        </a>
                    </div>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <nav class="ch-localnav ch-localnav--construction" data-customhome-localnav aria-label="注文住宅固定ナビ">
        <div class="ch-shell">
            <?php if (has_nav_menu('customhome-page-menu')) : ?>
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'customhome-page-menu',
                    'container'      => false,
                    'menu_class'     => 'ch-localnav__list',
                    'fallback_cb'    => false,
                ));
                ?>
            <?php else : ?>
                <ul class="ch-localnav__list">
                    <li><a href="<?php echo esc_url(home_url('/iezukuri/concept/')); ?>">注文住宅の考え方</a></li>
                    <li><a href="<?php echo esc_url(home_url('/iezukuri/design-policy/')); ?>">設計姿勢</a></li>
                    <li><a href="<?php echo esc_url(home_url('/iezukuri/nasu-house/')); ?>">那須での家づくり</a></li>
                    <li><a href="<?php echo esc_url(home_url('/iezukuri/design-office/')); ?>">デザインと設計</a></li>
                    <li><a href="<?php echo esc_url(home_url('/iezukuri/company/')); ?>">会社概要</a></li>
                    <li><a href="<?php echo esc_url(home_url('/iezukuri/contact/')); ?>">ご相談・資料請求</a></li>
                </ul>
            <?php endif; ?>
        </div>
    </nav>

    <?php if ($partial_file !== '') : ?>
        <?php require $partial_file; ?>
    <?php else : ?>
        <section class="ch-section ch-section--white ch-subpage-section">
            <div class="ch-shell">
                <article class="ch-subpage-main ch-subpage-main--fallback">
                    <?php
                    while (have_posts()) :
                        the_post();
                        the_content();
                    endwhile;
                    ?>
                </article>
            </div>
        </section>
    <?php endif; ?>

    <?php
    /**
     * =========================================================
     * Builder sections
     *
     * _ch_subpage_template が builder の場合だけ、
     * セクション構成ビルダーの順番で描画する。
     * =========================================================
     */
    $naigai_ch_current_layout_key = isset($lookup_key) ? (string) $lookup_key : '';

    if ($naigai_ch_current_layout_key === '') {
        $naigai_ch_current_layout_key = (string) get_post_meta($post_id, '_ch_subpage_template', true);
    }

    if ($naigai_ch_current_layout_key === 'builder' && function_exists('naigai_ch_render_builder_sections')) {
        naigai_ch_render_builder_sections($post_id);
    }
    ?>

    <?php if ($show_common_faq && !empty($faq_items)) : ?>
        <?php
        $faq_part = locate_template('hub/pages/iezukuri/template-parts/common/section-faq.php');

        if ($faq_part) {
            include $faq_part;
        }
        ?>
    <?php endif; ?>
</main>

<?php
get_footer('customhome');
