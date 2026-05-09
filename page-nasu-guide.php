<?php

/**
 * Template Name: 那須の住まい案内
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header('77');

$post_id = get_the_ID();
$ngu = function_exists('ngu_get_page_data') ? ngu_get_page_data($post_id) : [];

if (empty($ngu)) {
    get_footer();
    return;
}

/**
 * ------------------------------------------------------------
 * 分譲地セクション専用の表示用変数
 * ------------------------------------------------------------
 *
 * 目的:
 * - 見出し側と本文側で、同じ title / text を重複出力しない
 * - 新しい専用キーがあればそちらを優先
 * - まだ管理画面や getter を直していない場合でも既存キーで崩れない
 *
 * 想定する新キー:
 * - land_head_title : 分譲地セクション上部の見出しタイトル
 * - land_head_text  : 分譲地セクション上部の説明文
 *
 * 既存キー:
 * - land_sec_title  : 分譲地カード側タイトル
 * - land_sec_text   : 分譲地カード側本文
 */

$land_head_title = '';
$land_head_text  = '';
$land_body_title = '';
$land_body_text  = '';

if (!empty($ngu['land_head_title'])) {
    $land_head_title = $ngu['land_head_title'];
} elseif (!empty($ngu['land_sec_title'])) {
    $land_head_title = $ngu['land_sec_title'];
}

if (!empty($ngu['land_head_text'])) {
    $land_head_text = $ngu['land_head_text'];
}

if (!empty($ngu['land_sec_title'])) {
    $land_body_title = $ngu['land_sec_title'];
}

if (!empty($ngu['land_sec_text'])) {
    $land_body_text = $ngu['land_sec_text'];
}

/**
 * 見出しと本文タイトルが同じなら、本文側 h3 は省略する
 */
$show_land_body_title = (
    $land_body_title !== ''
    && $land_body_title !== $land_head_title
);
?>

<main id="primary" class="site-main nasu-guide-page">

    <!-- HERO -->
    <section
        class="nasu-guide-hero"
        <?php if (!empty($ngu['hero_bg_url'])) : ?>
        style="background-image:url('<?php echo esc_url($ngu['hero_bg_url']); ?>');"
        <?php else : ?>
        style="background-image:linear-gradient(135deg, #31485d 0%, #203242 100%);"
        <?php endif; ?>>
        <div class="inner">
            <div class="nasu-guide-hero__content">
                <p class="nasu-guide-hero__eyebrow"><?php echo esc_html($ngu['hero_eyebrow']); ?></p>
                <h1 class="nasu-guide-hero__title"><?php echo esc_html($ngu['hero_title']); ?></h1>
                <p class="nasu-guide-hero__lead"><?php echo nl2br(esc_html($ngu['hero_lead'])); ?></p>

                <div class="nasu-guide-hero__buttons">
                    <?php echo ngu_btn_html($ngu['hero_btn1_text'], $ngu['hero_btn1_url'], 'ngu-btn--primary'); ?>
                    <?php echo ngu_btn_html($ngu['hero_btn2_text'], $ngu['hero_btn2_url'], 'ngu-btn--outline'); ?>
                </div>
            </div>
        </div>
    </section>

    <!-- 導入 -->
    <section class="ngu-intro">
        <div class="inner">
            <div class="ngu-section-head">
                <p class="ngu-section-head__eyebrow"><?php echo esc_html($ngu['intro_eyebrow']); ?></p>
                <h2><?php echo esc_html($ngu['intro_title']); ?></h2>
            </div>

            <div class="ngu-intro__box">
                <p><?php echo nl2br(esc_html($ngu['intro_text'])); ?></p>
            </div>
        </div>
    </section>

    <!-- 2導線 -->
    <section class="ngu-branches">
        <div class="inner">
            <div class="ngu-section-head">
                <p class="ngu-section-head__eyebrow"><?php echo esc_html($ngu['branch_eyebrow']); ?></p>
                <h2><?php echo esc_html($ngu['branch_title']); ?></h2>
                <p><?php echo nl2br(esc_html($ngu['branch_text'])); ?></p>
            </div>

            <div class="ngu-card-grid ngu-card-grid--2">
                <article id="land-guide" class="ngu-card ngu-branch-anchor">
                    <?php if (!empty($ngu['land_branch_img_url'])) : ?>
                        <div class="ngu-card__img">
                            <img
                                src="<?php echo esc_url($ngu['land_branch_img_url']); ?>"
                                alt="<?php echo esc_attr(ngu_image_alt($ngu['land_branch_img_id'], $ngu['land_branch_title'])); ?>">
                        </div>
                    <?php endif; ?>

                    <div class="ngu-card__body">
                        <h3><?php echo esc_html($ngu['land_branch_title']); ?></h3>
                        <p><?php echo nl2br(esc_html($ngu['land_branch_text'])); ?></p>

                        <div class="ngu-card__actions">
                            <?php echo ngu_btn_html($ngu['land_branch_btn_text'], $ngu['land_branch_btn_url'], 'ngu-btn--primary'); ?>
                        </div>
                    </div>
                </article>

                <article id="style-guide" class="ngu-card ngu-style-anchor">
                    <?php if (!empty($ngu['style_branch_img_url'])) : ?>
                        <div class="ngu-card__img">
                            <img
                                src="<?php echo esc_url($ngu['style_branch_img_url']); ?>"
                                alt="<?php echo esc_attr(ngu_image_alt($ngu['style_branch_img_id'], $ngu['style_branch_title'])); ?>">
                        </div>
                    <?php endif; ?>

                    <div class="ngu-card__body">
                        <h3><?php echo esc_html($ngu['style_branch_title']); ?></h3>
                        <p><?php echo nl2br(esc_html($ngu['style_branch_text'])); ?></p>

                        <div class="ngu-card__actions">
                            <?php echo ngu_btn_html($ngu['style_branch_btn_text'], $ngu['style_branch_btn_url'], 'ngu-btn--primary'); ?>
                        </div>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <!-- 分譲地 -->
    <section class="ngu-land">
        <div class="inner">
            <div class="ngu-section-head">
                <p class="ngu-section-head__eyebrow"><?php echo esc_html($ngu['land_eyebrow']); ?></p>

                <?php if ($land_head_title !== '') : ?>
                    <h2><?php echo esc_html($land_head_title); ?></h2>
                <?php endif; ?>

                <?php if ($land_head_text !== '') : ?>
                    <p><?php echo nl2br(esc_html($land_head_text)); ?></p>
                <?php endif; ?>
            </div>

            <div class="ngu-land__single">
                <article class="ngu-card">
                    <?php if (!empty($ngu['land_sec_img_url'])) : ?>
                        <div class="ngu-card__img">
                            <img
                                src="<?php echo esc_url($ngu['land_sec_img_url']); ?>"
                                alt="<?php echo esc_attr(ngu_image_alt($ngu['land_sec_img_id'], $land_body_title !== '' ? $land_body_title : $land_head_title)); ?>">
                        </div>
                    <?php endif; ?>
                </article>

                <article class="ngu-card">
                    <div class="ngu-card__body">
                        <?php if ($show_land_body_title) : ?>
                            <h3><?php echo esc_html($land_body_title); ?></h3>
                        <?php endif; ?>

                        <?php if ($land_body_text !== '') : ?>
                            <p><?php echo nl2br(esc_html($land_body_text)); ?></p>
                        <?php endif; ?>

                        <div class="ngu-card__actions">
                            <?php echo ngu_btn_html($ngu['land_sec_btn_text'], $ngu['land_sec_btn_url'], 'ngu-btn--primary'); ?>
                        </div>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <!-- 住まいスタイル -->
    <section class="ngu-style">
        <div class="inner">
            <div class="ngu-section-head">
                <p class="ngu-section-head__eyebrow"><?php echo esc_html($ngu['style_eyebrow']); ?></p>
                <h2><?php echo esc_html($ngu['style_sec_title']); ?></h2>
                <p><?php echo nl2br(esc_html($ngu['style_sec_text'])); ?></p>
            </div>

            <div class="ngu-card-grid ngu-card-grid--2">
                <article class="ngu-card">
                    <?php if (!empty($ngu['north_img_url'])) : ?>
                        <div class="ngu-card__img">
                            <img
                                src="<?php echo esc_url($ngu['north_img_url']); ?>"
                                alt="<?php echo esc_attr(ngu_image_alt($ngu['north_img_id'], $ngu['north_title'])); ?>">
                        </div>
                    <?php endif; ?>

                    <div class="ngu-card__body">
                        <h3><?php echo esc_html($ngu['north_title']); ?></h3>
                        <p><?php echo nl2br(esc_html($ngu['north_text'])); ?></p>

                        <div class="ngu-card__actions">
                            <?php echo ngu_btn_html($ngu['north_btn_text'], $ngu['north_btn_url'], 'ngu-btn--primary'); ?>
                        </div>
                    </div>
                </article>

                <article class="ngu-card">
                    <?php if (!empty($ngu['postbeam_img_url'])) : ?>
                        <div class="ngu-card__img">
                            <img
                                src="<?php echo esc_url($ngu['postbeam_img_url']); ?>"
                                alt="<?php echo esc_attr(ngu_image_alt($ngu['postbeam_img_id'], $ngu['postbeam_title'])); ?>">
                        </div>
                    <?php endif; ?>

                    <div class="ngu-card__body">
                        <h3><?php echo esc_html($ngu['postbeam_title']); ?></h3>
                        <p><?php echo nl2br(esc_html($ngu['postbeam_text'])); ?></p>

                        <div class="ngu-card__actions">
                            <?php echo ngu_btn_html($ngu['postbeam_btn_text'], $ngu['postbeam_btn_url'], 'ngu-btn--primary'); ?>
                        </div>
                    </div>
                </article>
            </div>
        </div>
    </section>


    <!-- 中古住宅リノベ導線 -->
    <section class="ngu-used-renovation" id="used-renovation-guide">
        <div class="inner">
            <div class="ngu-section-head">
                <p class="ngu-section-head__eyebrow"><?php echo esc_html($ngu['used_eyebrow']); ?></p>
                <h2><?php echo esc_html($ngu['used_title']); ?></h2>
                <p><?php echo nl2br(esc_html($ngu['used_text'])); ?></p>
            </div>

            <div class="ngu-land__single">
                <article class="ngu-card">
                    <?php if (!empty($ngu['used_img_url'])) : ?>
                        <div class="ngu-card__img">
                            <img
                                src="<?php echo esc_url($ngu['used_img_url']); ?>"
                                alt="<?php echo esc_attr(ngu_image_alt($ngu['used_img_id'], $ngu['used_title'])); ?>">
                        </div>
                    <?php endif; ?>
                </article>

                <article class="ngu-card">
                    <div class="ngu-card__body">
                        <?php if (!empty($ngu['used_body_title'])) : ?>
                            <h3><?php echo esc_html($ngu['used_body_title']); ?></h3>
                        <?php endif; ?>

                        <?php if (!empty($ngu['used_body_text'])) : ?>
                            <p><?php echo nl2br(esc_html($ngu['used_body_text'])); ?></p>
                        <?php endif; ?>

                        <div class="ngu-card__actions">
                            <?php echo ngu_btn_html($ngu['used_btn_text'], $ngu['used_btn_url'], 'ngu-btn--primary'); ?>
                        </div>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <!-- B2C / B2B -->
    <section class="ngu-biz">
        <div class="inner">
            <div class="ngu-section-head">
                <p class="ngu-section-head__eyebrow"><?php echo esc_html($ngu['support_eyebrow']); ?></p>
                <h2><?php echo esc_html($ngu['biz_title']); ?></h2>
            </div>

            <div class="ngu-biz-grid">
                <article class="ngu-biz-card">
                    <h3><?php echo esc_html($ngu['b2c_title']); ?></h3>
                    <p><?php echo nl2br(esc_html($ngu['b2c_text'])); ?></p>
                </article>

                <article class="ngu-biz-card">
                    <h3><?php echo esc_html($ngu['b2b_title']); ?></h3>
                    <p><?php echo nl2br(esc_html($ngu['b2b_text'])); ?></p>
                </article>
            </div>
        </div>
    </section>

    <!-- 会社情報 -->
    <section class="ngu-company">
        <div class="inner">
            <div class="ngu-company__grid">
                <div class="ngu-company__img">
                    <?php if (!empty($ngu['company_img_url'])) : ?>
                        <img
                            src="<?php echo esc_url($ngu['company_img_url']); ?>"
                            alt="<?php echo esc_attr(ngu_image_alt($ngu['company_img_id'], $ngu['company_title'])); ?>">
                    <?php endif; ?>
                </div>

                <div class="ngu-company__body">
                    <h2><?php echo esc_html($ngu['company_title']); ?></h2>
                    <p><?php echo nl2br(esc_html($ngu['company_text'])); ?></p>

                    <div class="ngu-company__actions">
                        <?php echo ngu_btn_html($ngu['company_btn_text'], $ngu['company_btn_url'], 'ngu-btn--primary'); ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ -->
    <?php if (!empty($ngu['faq_items'])) : ?>
        <section class="ngu-faq">
            <div class="inner">
                <div class="ngu-section-head">
                    <p class="ngu-section-head__eyebrow"><?php echo esc_html($ngu['faq_eyebrow']); ?></p>
                    <h2><?php echo esc_html($ngu['faq_title']); ?></h2>
                </div>

                <div class="ngu-faq__list">
                    <?php foreach ($ngu['faq_items'] as $index => $item) : ?>
                        <?php $faq_id = 'ngu-faq-answer-' . ($index + 1); ?>
                        <div class="ngu-faq__item<?php echo $index === 0 ? ' is-open' : ''; ?>">
                            <button
                                class="ngu-faq__question"
                                type="button"
                                aria-expanded="<?php echo $index === 0 ? 'true' : 'false'; ?>"
                                aria-controls="<?php echo esc_attr($faq_id); ?>">
                                <?php echo esc_html($item['q']); ?>
                            </button>

                            <div
                                id="<?php echo esc_attr($faq_id); ?>"
                                class="ngu-faq__answer"
                                <?php if ($index !== 0) : ?>hidden<?php endif; ?>>
                                <div class="ngu-faq__answer-inner">
                                    <?php echo wpautop(esc_html($item['a'])); ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- CTA -->
    <section class="ngu-cta">
        <div class="inner">
            <div class="ngu-cta__box">
                <h2><?php echo esc_html($ngu['cta_title']); ?></h2>
                <p><?php echo nl2br(esc_html($ngu['cta_text'])); ?></p>

                <div class="nasu-guide-cta__buttons">
                    <?php echo ngu_btn_html($ngu['cta_btn1_text'], $ngu['cta_btn1_url'], 'ngu-btn--primary'); ?>
                    <?php echo ngu_btn_html($ngu['cta_btn2_text'], $ngu['cta_btn2_url'], 'ngu-btn--outline'); ?>
                </div>
            </div>
        </div>
    </section>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var faqButtons = document.querySelectorAll('.ngu-faq__question');

        faqButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                var item = button.closest('.ngu-faq__item');
                var answerId = button.getAttribute('aria-controls');
                var answer = answerId ? document.getElementById(answerId) : null;
                var isOpen = item.classList.contains('is-open');

                item.classList.toggle('is-open', !isOpen);
                button.setAttribute('aria-expanded', isOpen ? 'false' : 'true');

                if (answer) {
                    answer.hidden = isOpen;
                }
            });
        });
    });
</script>

<?php get_footer(); ?>