<?php
if (!defined("ABSPATH")) {
    exit;
}

/**
 * =========================================================
 * Construction Intro Hero
 * 役割:
 * - construction 専用の先頭動画ヒーロー
 * - 既存の section-hero の前に追加して使う
 * =========================================================
 */
$kicker          = isset($args['kicker']) ? (string) $args['kicker'] : '';
$title           = isset($args['title']) ? (string) $args['title'] : '';
$lead            = isset($args['lead']) ? (string) $args['lead'] : '';
$video_mp4       = isset($args['video_mp4']) ? (string) $args['video_mp4'] : '';
$video_webm      = isset($args['video_webm']) ? (string) $args['video_webm'] : '';
$poster          = isset($args['poster']) ? (string) $args['poster'] : '';
$primary_label   = isset($args['primary_label']) ? (string) $args['primary_label'] : '';
$primary_url     = isset($args['primary_url']) ? (string) $args['primary_url'] : '';
$secondary_label = isset($args['secondary_label']) ? (string) $args['secondary_label'] : '';
$secondary_url   = isset($args['secondary_url']) ? (string) $args['secondary_url'] : '';

if ($title === '' && $lead === '' && $video_mp4 === '' && $video_webm === '' && $poster === '') {
    return;
}
?>
<section class="hub-section hub-section--intro-hero hub-section--construction-intro-hero">
  <div class="hub-hero__inner">
    <div class="hub-intro-hero">
      <div class="hub-intro-hero__media">
        <?php if ($video_mp4 !== '' || $video_webm !== '') : ?>
          <video autoplay muted loop playsinline preload="metadata" <?php echo ($poster !== '') ? 'poster="' . esc_url($poster) . '"' : ''; ?>>
            <?php if ($video_webm !== '') : ?>
              <source src="<?php echo esc_url($video_webm); ?>" type="video/webm">
            <?php endif; ?>
            <?php if ($video_mp4 !== '') : ?>
              <source src="<?php echo esc_url($video_mp4); ?>" type="video/mp4">
            <?php endif; ?>
          </video>
        <?php elseif ($poster !== '') : ?>
          <img src="<?php echo esc_url($poster); ?>" alt="">
        <?php endif; ?>
      </div>

      <div class="hub-intro-hero__inner">
        <div class="hub-intro-hero__body">
          <?php if ($kicker !== '') : ?>
            <span class="hub-kicker"><?php echo esc_html($kicker); ?></span>
          <?php endif; ?>

          <?php if ($title !== '') : ?>
            <h1 class="hub-intro-hero__title"><?php echo nl2br(esc_html($title)); ?></h1>
          <?php endif; ?>

          <?php if ($lead !== '') : ?>
            <p class="hub-intro-hero__lead"><?php echo nl2br(esc_html($lead)); ?></p>
          <?php endif; ?>

          <div class="hub-intro-hero__actions">
            <?php if ($primary_label !== '' && $primary_url !== '') : ?>
              <a class="hub-btn" href="<?php echo esc_url($primary_url); ?>"><?php echo esc_html($primary_label); ?></a>
            <?php endif; ?>
            <?php if ($secondary_label !== '' && $secondary_url !== '') : ?>
              <a class="hub-btn is-secondary" href="<?php echo esc_url($secondary_url); ?>"><?php echo esc_html($secondary_label); ?></a>
            <?php endif; ?>
          </div>
        </div>

        <div class="hub-intro-hero__side">
          <div class="hub-intro-hero__panel">
            <span class="hub-intro-hero__panel-label">DESIGN</span>
            <h2 class="hub-intro-hero__panel-title">暮らしから逆算して注文住宅を考える</h2>
            <p class="hub-intro-hero__panel-text">デザイン・性能・間取り・素材を分けず、住まい全体の質として整理する入口です。</p>
          </div>

          <div class="hub-intro-hero__panel">
            <span class="hub-intro-hero__panel-label">CONSULT</span>
            <h2 class="hub-intro-hero__panel-title">一覧ではなく、創る相談へつなげる</h2>
            <p class="hub-intro-hero__panel-text">建売や売却済み一覧の受け皿ではなく、注文住宅として考え始める相談導線を前面に出します。</p>
          </div>

          <nav class="hub-intro-hero__nav" aria-label="注文住宅ページ内ナビゲーション">
            <a class="hub-intro-hero__nav-link" href="#hub-custom-home-works">施工実例を見る</a>
            <a class="hub-intro-hero__nav-link" href="#hub-custom-home-spec">工法・性能を見る</a>
            <a class="hub-intro-hero__nav-link" href="#hub-custom-home-cta">無料相談へ進む</a>
          </nav>
        </div>
      </div>
    </div>
  </div>
</section>
