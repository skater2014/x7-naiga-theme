<?php

/**
 * Template Name: page-zairai.php
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header('77');

$post_id = get_the_ID();
$page    = zairai_page_data($post_id);

$hero       = $page['hero'];
$intro      = $page['intro'];
$guide_plus = $page['guide_plus'];
$feature1   = $page['feature1'];
$feature2   = $page['feature2'];
$feature3   = $page['feature3'];
$flow       = $page['flow'];
$cta        = $page['cta'];
?>

<div class="zairai-page">

    <section class="zairai-hero zairai-fullbleed">
        <?php if ($hero['image']) : ?>
            <div class="zairai-hero-media">
                <img src="<?php echo esc_url($hero['image']); ?>" alt="<?php echo esc_attr($hero['image_alt']); ?>">
            </div>
        <?php endif; ?>

        <div class="zairai-hero-overlay"></div>

        <div class="zairai-hero-inner">
            <div class="zairai-hero-copy">
                <?php if ($hero['eyebrow']) : ?>
                    <p class="zairai-kicker"><?php echo esc_html($hero['eyebrow']); ?></p>
                <?php endif; ?>

                <?php if ($hero['title']) : ?>
                    <h1 class="zairai-hero-title"><?php echo esc_html($hero['title']); ?></h1>
                <?php endif; ?>

                <?php if ($hero['text']) : ?>
                    <p class="zairai-hero-text"><?php echo zairai_text_html($hero['text']); ?></p>
                <?php endif; ?>

                <?php
                $hero_primary   = zairai_btn($hero['primary_btn_url'], $hero['primary_btn_text'], 'zairai-btn-primary');
                $hero_secondary = zairai_btn($hero['secondary_btn_url'], $hero['secondary_btn_text'], 'zairai-btn-secondary');
                ?>
                <?php if ($hero_primary || $hero_secondary) : ?>
                    <div class="zairai-hero-actions">
                        <?php echo $hero_primary; ?>
                        <?php echo $hero_secondary; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section id="zairai-guide" class="zairai-section">
        <div class="zairai-shell zairai-grid">
            <div class="zairai-copy">
                <?php if ($intro['eyebrow']) : ?>
                    <p class="zairai-kicker"><?php echo esc_html($intro['eyebrow']); ?></p>
                <?php endif; ?>

                <?php if ($intro['title']) : ?>
                    <h2 class="zairai-heading"><?php echo esc_html($intro['title']); ?></h2>
                <?php endif; ?>

                <?php if ($intro['text']) : ?>
                    <p class="zairai-text"><?php echo zairai_text_html($intro['text']); ?></p>
                <?php endif; ?>
            </div>

            <?php if ($intro['image']) : ?>
                <div class="zairai-media">
                    <div class="zairai-media-frame is-wide">
                        <img src="<?php echo esc_url($intro['image']); ?>" alt="<?php echo esc_attr($intro['image_alt']); ?>">
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="zairai-section">
        <div class="zairai-shell zairai-grid is-reverse">
            <?php if ($feature1['image']) : ?>
                <div class="zairai-media">
                    <div class="zairai-media-frame">
                        <img src="<?php echo esc_url($feature1['image']); ?>" alt="<?php echo esc_attr($feature1['image_alt']); ?>">
                    </div>
                </div>
            <?php endif; ?>

            <div class="zairai-copy">
                <?php if ($feature1['eyebrow']) : ?>
                    <p class="zairai-kicker"><?php echo esc_html($feature1['eyebrow']); ?></p>
                <?php endif; ?>

                <?php if ($feature1['title']) : ?>
                    <h2 class="zairai-heading"><?php echo esc_html($feature1['title']); ?></h2>
                <?php endif; ?>

                <?php if ($feature1['text']) : ?>
                    <p class="zairai-text"><?php echo zairai_text_html($feature1['text']); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section class="zairai-section">
        <div class="zairai-shell zairai-grid">
            <div class="zairai-copy">
                <?php if ($feature2['eyebrow']) : ?>
                    <p class="zairai-kicker"><?php echo esc_html($feature2['eyebrow']); ?></p>
                <?php endif; ?>

                <?php if ($feature2['title']) : ?>
                    <h2 class="zairai-heading"><?php echo esc_html($feature2['title']); ?></h2>
                <?php endif; ?>

                <?php if ($feature2['text']) : ?>
                    <p class="zairai-text"><?php echo zairai_text_html($feature2['text']); ?></p>
                <?php endif; ?>
            </div>

            <?php if ($feature2['image']) : ?>
                <div class="zairai-media">
                    <div class="zairai-media-frame">
                        <img src="<?php echo esc_url($feature2['image']); ?>" alt="<?php echo esc_attr($feature2['image_alt']); ?>">
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="zairai-section">
        <div class="zairai-shell zairai-grid is-reverse">
            <?php if ($feature3['image']) : ?>
                <div class="zairai-media">
                    <div class="zairai-media-frame is-wide">
                        <img src="<?php echo esc_url($feature3['image']); ?>" alt="<?php echo esc_attr($feature3['image_alt']); ?>">
                    </div>
                </div>
            <?php endif; ?>

            <div class="zairai-copy">
                <?php if ($feature3['eyebrow']) : ?>
                    <p class="zairai-kicker"><?php echo esc_html($feature3['eyebrow']); ?></p>
                <?php endif; ?>

                <?php if ($feature3['title']) : ?>
                    <h2 class="zairai-heading"><?php echo esc_html($feature3['title']); ?></h2>
                <?php endif; ?>

                <?php if ($feature3['text']) : ?>
                    <p class="zairai-text"><?php echo zairai_text_html($feature3['text']); ?></p>
                <?php endif; ?>

                <?php
                $feature3_primary   = zairai_btn($feature3['btn_url'], $feature3['btn_text'], 'zairai-btn-dark');
                $feature3_secondary = zairai_btn($feature3['sub_btn_url'], $feature3['sub_btn_text'], 'zairai-btn-outline-dark');
                ?>
                <?php if ($feature3_primary || $feature3_secondary) : ?>
                    <div class="zairai-copy-actions">
                        <?php echo $feature3_primary; ?>
                        <?php echo $feature3_secondary; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section class="zairai-section zairai-flow-section">
        <div class="zairai-shell zairai-flow-layout">
            <div class="zairai-flow-main">
                <div class="zairai-copy">
                    <?php if ($flow['eyebrow']) : ?>
                        <p class="zairai-kicker"><?php echo esc_html($flow['eyebrow']); ?></p>
                    <?php endif; ?>

                    <?php if ($flow['title']) : ?>
                        <h2 class="zairai-heading"><?php echo esc_html($flow['title']); ?></h2>
                    <?php endif; ?>

                    <?php if (!empty($flow['steps'])) : ?>
                        <div class="zairai-flow-box">
                            <div class="zairai-flow-list">
                                <?php foreach ($flow['steps'] as $step) : ?>
                                    <div class="zairai-flow-step"><?php echo esc_html($step); ?></div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <aside class="zairai-flow-side">
                <div class="zairai-guide-plus-box">
                    <?php if ($flow['side_image']) : ?>
                        <div class="zairai-guide-plus-box__media">
                            <img src="<?php echo esc_url($flow['side_image']); ?>" alt="<?php echo esc_attr($flow['side_image_alt']); ?>">
                        </div>
                    <?php endif; ?>

                    <div class="zairai-guide-plus-box__body">
                        <?php if ($guide_plus['eyebrow']) : ?>
                            <p class="zairai-kicker"><?php echo esc_html($guide_plus['eyebrow']); ?></p>
                        <?php endif; ?>

                        <?php if ($guide_plus['title']) : ?>
                            <h3 class="zairai-guide-plus-box__title"><?php echo esc_html($guide_plus['title']); ?></h3>
                        <?php endif; ?>

                        <?php if ($guide_plus['text']) : ?>
                            <p class="zairai-guide-plus-box__text"><?php echo zairai_text_html($guide_plus['text']); ?></p>
                        <?php endif; ?>

                        <div class="zairai-guide-plus-box__points">
                            <?php if ($guide_plus['point1_title'] || $guide_plus['point1_text']) : ?>
                                <article class="zairai-guide-plus-box__card">
                                    <span class="zairai-guide-plus-box__num">01</span>
                                    <?php if ($guide_plus['point1_title']) : ?>
                                        <h4><?php echo esc_html($guide_plus['point1_title']); ?></h4>
                                    <?php endif; ?>
                                    <?php if ($guide_plus['point1_text']) : ?>
                                        <p><?php echo zairai_text_html($guide_plus['point1_text']); ?></p>
                                    <?php endif; ?>
                                </article>
                            <?php endif; ?>

                            <?php if ($guide_plus['point2_title'] || $guide_plus['point2_text']) : ?>
                                <article class="zairai-guide-plus-box__card">
                                    <span class="zairai-guide-plus-box__num">02</span>
                                    <?php if ($guide_plus['point2_title']) : ?>
                                        <h4><?php echo esc_html($guide_plus['point2_title']); ?></h4>
                                    <?php endif; ?>
                                    <?php if ($guide_plus['point2_text']) : ?>
                                        <p><?php echo zairai_text_html($guide_plus['point2_text']); ?></p>
                                    <?php endif; ?>
                                </article>
                            <?php endif; ?>

                            <?php if ($guide_plus['point3_title'] || $guide_plus['point3_text']) : ?>
                                <article class="zairai-guide-plus-box__card">
                                    <span class="zairai-guide-plus-box__num">03</span>
                                    <?php if ($guide_plus['point3_title']) : ?>
                                        <h4><?php echo esc_html($guide_plus['point3_title']); ?></h4>
                                    <?php endif; ?>
                                    <?php if ($guide_plus['point3_text']) : ?>
                                        <p><?php echo zairai_text_html($guide_plus['point3_text']); ?></p>
                                    <?php endif; ?>
                                </article>
                            <?php endif; ?>
                        </div>

                        <?php
                        $guide_plus_button = zairai_btn($guide_plus['btn_url'], $guide_plus['btn_text'], 'zairai-btn-primary');
                        ?>
                        <?php if ($guide_plus_button) : ?>
                            <div class="zairai-guide-plus-box__actions">
                                <?php echo $guide_plus_button; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </aside>
        </div>
    </section>

    <section class="zairai-section">
        <div class="zairai-shell">
            <div class="zairai-cta">
                <?php if ($cta['image']) : ?>
                    <div class="zairai-cta-media">
                        <img src="<?php echo esc_url($cta['image']); ?>" alt="<?php echo esc_attr($cta['image_alt']); ?>">
                    </div>
                <?php endif; ?>

                <div class="zairai-cta-overlay"></div>

                <div class="zairai-cta-inner">
                    <div class="zairai-cta-content">
                        <?php if ($cta['eyebrow']) : ?>
                            <p class="zairai-kicker"><?php echo esc_html($cta['eyebrow']); ?></p>
                        <?php endif; ?>

                        <?php if ($cta['title']) : ?>
                            <h2 class="zairai-heading"><?php echo esc_html($cta['title']); ?></h2>
                        <?php endif; ?>

                        <?php if ($cta['text']) : ?>
                            <p class="zairai-text"><?php echo zairai_text_html($cta['text']); ?></p>
                        <?php endif; ?>

                        <?php
                        $cta_button = zairai_btn($cta['url'], $cta['btn_text'], 'zairai-btn-primary');
                        ?>
                        <?php if ($cta_button) : ?>
                            <div class="zairai-cta-actions">
                                <?php echo $cta_button; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

</div>

<?php get_footer(); ?>