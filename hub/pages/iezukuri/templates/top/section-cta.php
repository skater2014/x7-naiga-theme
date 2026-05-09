<?php
/**
 * hub/pages/iezukuri/templates/top/section-cta.php
 * 役割: CTA / 相談導線 / ボタン
 * 元データ: 分割実行時点の template-iezukuri.php
 */
if (!defined('ABSPATH')) {
    exit;
}
?>
<section id="customhome-contact" class="ch-cta" data-customhome-cta data-nav-section="contact">
        <div class="ch-shell">
            <div class="ch-cta__grid">
                <div class="ch-cta__media">
                    <?php if (!empty($cta_media_items)) : ?>
                        <?php if (count($cta_media_items) === 1) : ?>
                            <?php $media = $cta_media_items[0]; ?>
                            <?php if ($media['type'] === 'video') : ?>
                                <video class="ch-cta__image ch-cta__video" autoplay muted loop playsinline <?php if ($cta_video_controls === '1') : ?>controls<?php endif; ?>>
                                    <source src="<?php echo esc_url($media['url']); ?>" type="<?php echo esc_attr($media['mime']); ?>">
                                </video>
                            <?php else : ?>
                                <img class="ch-cta__image" src="<?php echo esc_url($media['url']); ?>" alt="<?php echo esc_attr($cta_title); ?>">
                            <?php endif; ?>
                        <?php else : ?>
                            <div class="swiper ch-cta-swiper" data-customhome-cta-swiper data-swiper-enabled="<?php echo esc_attr($cta_swiper_enabled); ?>" data-swiper-navigation="1" data-swiper-pagination="1" data-video-controls="<?php echo esc_attr($cta_video_controls); ?>">
                                <div class="swiper-wrapper">
                                    <?php foreach ($cta_media_items as $media) : ?>
                                        <div class="swiper-slide">
                                            <?php if ($media['type'] === 'video') : ?>
                                                <video class="ch-cta__image ch-cta__video" autoplay muted loop playsinline <?php if ($cta_video_controls === '1') : ?>controls<?php endif; ?>>
                                                    <source src="<?php echo esc_url($media['url']); ?>" type="<?php echo esc_attr($media['mime']); ?>">
                                                </video>
                                            <?php else : ?>
                                                <img class="ch-cta__image" src="<?php echo esc_url($media['url']); ?>" alt="<?php echo esc_attr($cta_title); ?>">
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <div class="swiper-pagination"></div>
                                <div class="swiper-button-prev"></div>
                                <div class="swiper-button-next"></div>
                            </div>
                        <?php endif; ?>
                    <?php elseif ($cta_image) : ?>
                        <img class="ch-cta__image" src="<?php echo esc_url($cta_image); ?>" alt="<?php echo esc_attr($cta_title); ?>">
                    <?php endif; ?>
                </div>

                <div class="ch-cta__body">
<h2 class="ch-cta__title"><?php echo esc_html($cta_title); ?></h2>
                    <p class="ch-cta__text"><?php echo esc_html($cta_text); ?></p>

                    <div class="ch-cta__actions">
                        <a class="ch-btn ch-btn--primary" href="<?php echo esc_url($cta_btn1_url); ?>"><?php echo esc_html($cta_btn1_label); ?></a>
                        <a class="ch-btn ch-btn--ghost-light" href="<?php echo esc_url($cta_btn2_url); ?>"><?php echo esc_html($cta_btn2_label); ?></a>
                    </div>
                </div>
            </div>
        </div>
    </section>
