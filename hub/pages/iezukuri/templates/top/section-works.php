<?php
/**
 * hub/pages/iezukuri/templates/top/section-works.php
 * 役割: 暮らしのポイント・設計事例
 * 元データ: 分割実行時点の template-iezukuri.php
 */
if (!defined('ABSPATH')) {
    exit;
}
?>
<section id="customhome-works" class="ch-section ch-section--white" data-nav-section="works">
        <div class="ch-shell">
            <div class="ch-head ch-head--with-link">
                <div>
                    <div class="ch-eyebrow"><?php echo esc_html($works_eyebrow); ?></div>
                    <h2 class="ch-section-title"><?php echo esc_html($works_title); ?></h2>
                </div>
            </div>

            <?php
            $works_primary = array_slice($works, 0, 4);
            $works_more    = array_slice($works, 4);
            ?>

            <div class="ch-works-grid">
                <?php foreach ($works_primary as $work) : ?>
                    <article class="ch-work-card">
                        <div class="ch-work-card__thumb">
                            <?php if (!empty($work['image'])) : ?>
                                <img class="ch-work-card__image" src="<?php echo esc_url($work['image']); ?>" alt="<?php echo esc_attr($work['title']); ?>">
                            <?php endif; ?>
                        </div>
                        <div class="ch-work-card__body">
                            <h3 class="ch-work-card__title"><?php echo esc_html($work['title']); ?></h3>
                            <p class="ch-work-card__text"><?php echo esc_html($work['text']); ?></p>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

            <?php if (!empty($works_more)) : ?>
                <div class="ch-more-toggle">
                    <button
                        type="button"
                        class="ch-more-btn"
                        data-customhome-more-btn
                        aria-expanded="false"
                        aria-controls="customhome-works-more"
                    >
                        <span class="ch-more-btn__label">暮らしのポイントをもっと見る</span>
                        <span class="ch-more-btn__loading" aria-hidden="true"></span>
                    </button>
                </div>

                <div id="customhome-works-more" class="ch-more-panel" hidden>
                    <div class="ch-works-grid ch-works-grid--more" data-customhome-more-grid>
                        <?php foreach ($works_more as $work) : ?>
                            <article class="ch-work-card is-more" hidden>
                                <div class="ch-work-card__thumb">
                                    <?php if (!empty($work['image'])) : ?>
                                        <img class="ch-work-card__image" src="<?php echo esc_url($work['image']); ?>" alt="<?php echo esc_attr($work['title']); ?>">
                                    <?php endif; ?>
                                </div>
                                <div class="ch-work-card__body">
                                    <h3 class="ch-work-card__title"><?php echo esc_html($work['title']); ?></h3>
                                    <p class="ch-work-card__text"><?php echo esc_html($work['text']); ?></p>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>
