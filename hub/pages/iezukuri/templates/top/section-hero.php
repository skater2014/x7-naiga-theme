<?php
/**
 * hub/pages/iezukuri/templates/top/section-hero.php
 * 役割: Hero / mp4 / poster / メインビジュアル
 * 元データ: 分割実行時点の template-iezukuri.php
 */
if (!defined('ABSPATH')) {
    exit;
}
?>
<?php
/**
 * IEZ_TOP_COMMON_HERO_SWITCH_START
 *
 * /iezukuri/ トップHeroも共通Hero rendererへ寄せる。
 *
 * 目的:
 * - 管理画面の _ch_hero_engine / _ch_hero_gallery_ids / _ch_hero_video_mp4_id / _ch_hero_motion を反映する。
 * - 古い _hub_ch_hero_video_mp4_id の固定MP4だけが出る状態を避ける。
 */
$iez_top_page_id = isset($post_id) ? absint($post_id) : get_queried_object_id();

if (
    $iez_top_page_id
    && function_exists('naigai_iez_get_page_hero_data')
    && function_exists('naigai_iez_render_hero')
) {
    $iez_top_hero_data = naigai_iez_get_page_hero_data($iez_top_page_id);

    if (
        !empty($iez_top_hero_data)
        && (
            !empty($iez_top_hero_data['items'])
            || !empty($iez_top_hero_data['video_url'])
        )
    ) {
        naigai_iez_render_hero($iez_top_hero_data);
        return;
    }
}
/* IEZ_TOP_COMMON_HERO_SWITCH_END */
?>

<section class="ch-hero ch-fullbleed" data-customhome-hero>
        <div class="ch-hero__media">
            <?php if ($hero_video_mp4 || $hero_video_webm) : ?>
                <video
                    class="ch-hero__video"
                    autoplay
                    muted
                    loop
                    playsinline
                    <?php if ($hero_poster) : ?>poster="<?php echo esc_url($hero_poster); ?>"<?php endif; ?>
                >
                    <?php if ($hero_video_webm) : ?><source src="<?php echo esc_url($hero_video_webm); ?>" type="video/webm"><?php endif; ?>
                    <?php if ($hero_video_mp4) : ?><source src="<?php echo esc_url($hero_video_mp4); ?>" type="video/mp4"><?php endif; ?>
                </video>
            <?php elseif ($hero_fallback) : ?>
                <img class="ch-hero__image" src="<?php echo esc_url($hero_fallback); ?>" alt="<?php echo esc_attr(get_the_title($post_id)); ?>">
            <?php endif; ?>
            <div class="ch-hero__overlay"></div>
        </div>

        <div class="ch-shell ch-hero__inner">
            <div class="ch-hero__content">
                <?php if ($brand_logo_url) : ?>
                    <div class="ch-hero__brand-logo-wrap">
                        <img class="ch-hero__brand-logo" src="<?php echo esc_url($brand_logo_url); ?>" alt="<?php echo esc_attr($brand_text); ?>">
                    </div>
                <?php endif; ?>

                <?php if ($hero_company !== '') : ?>
                    <p class="ch-hero__company"><?php echo esc_html($hero_company); ?></p>
                <?php endif; ?>

                <?php if ($brand_subtext !== '') : ?>
                    <p class="ch-hero__company-sub"><?php echo esc_html($brand_subtext); ?></p>
                <?php endif; ?>

                <?php if ($hero_kicker !== '') : ?>
                    <div class="ch-eyebrow"><?php echo esc_html($hero_kicker); ?></div>
                <?php endif; ?>

                <h1 class="ch-hero__title"><?php echo nl2br(esc_html($hero_title)); ?></h1>

                <?php if ($hero_lead !== '') : ?>
                    <p class="ch-hero__lead"><?php echo nl2br(esc_html($hero_lead)); ?></p>
                <?php endif; ?>

                <div class="ch-hero__actions">
                    <a class="ch-btn ch-btn--primary" href="<?php echo esc_url($hero_btn1_url); ?>"><?php echo esc_html($hero_btn1_label); ?></a>
                    <a class="ch-btn ch-btn--ghost" href="<?php echo esc_url($hero_btn2_url); ?>"><?php echo esc_html($hero_btn2_label); ?></a>
                </div>
            </div>
        </div>
    </section>
