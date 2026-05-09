<?php
if (!defined("ABSPATH")) {
    exit;
}

/**
 * =========================================================
 * Hub CTA セクション共通部品
 * 役割:
 * - CTA 見出し / 補足文 / ボタンを共通描画する
 * - テンプレートごとの直書き重複を減らす
 * =========================================================
 */
$section_class   = isset($args['section_class']) ? trim((string) $args['section_class']) : '';
$banner_class    = isset($args['banner_class']) ? trim((string) $args['banner_class']) : '';
$kicker          = isset($args['kicker']) ? (string) $args['kicker'] : '';
$title           = isset($args['title']) ? (string) $args['title'] : '';
$text            = isset($args['text']) ? (string) $args['text'] : '';
$primary_label   = isset($args['primary_label']) ? (string) $args['primary_label'] : '';
$primary_url     = isset($args['primary_url']) ? (string) $args['primary_url'] : '';
$secondary_label = isset($args['secondary_label']) ? (string) $args['secondary_label'] : '';
$secondary_url   = isset($args['secondary_url']) ? (string) $args['secondary_url'] : '';

if ($title === '' && $text === '' && $primary_url === '' && $secondary_url === '') {
    return;
}
?>
<section class="hub-section hub-section--cta <?php echo esc_attr($section_class); ?>">
  <div class="hub-section__inner hub-container">
    <div class="hub-cta-banner <?php echo esc_attr($banner_class); ?>">
      <div class="hub-cta-banner__content">
        <?php if ($kicker !== '') : ?>
          <p class="hub-kicker"><?php echo esc_html($kicker); ?></p>
        <?php endif; ?>

        <?php if ($title !== '') : ?>
          <h2 class="hub-heading is-lg"><?php echo esc_html($title); ?></h2>
        <?php endif; ?>

        <?php if ($text !== '') : ?>
          <p class="hub-lead"><?php echo nl2br(esc_html($text)); ?></p>
        <?php endif; ?>

        <div class="hub-cta-buttons">
          <?php if ($primary_label !== '' && $primary_url !== '') : ?>
            <a class="hub-cta-button" href="<?php echo esc_url($primary_url); ?>"><?php echo esc_html($primary_label); ?></a>
          <?php endif; ?>

          <?php if ($secondary_label !== '' && $secondary_url !== '') : ?>
            <a class="hub-cta-button hub-cta-button--secondary" href="<?php echo esc_url($secondary_url); ?>"><?php echo esc_html($secondary_label); ?></a>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</section>
