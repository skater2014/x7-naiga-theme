<?php
if (!defined("ABSPATH")) {
    exit;
}

/**
 * =========================================================
 * Hub 補助コンテンツ共通 partial
 * 役割:
 * - 総合窓口 / 不動産 / 注文住宅 の補助導線をカードで描画する
 * - label / title / text / href を受け取って、文脈別に使い分ける
 * =========================================================
 */
$section_class = isset($args['section_class']) ? trim((string) $args['section_class']) : '';
$title         = isset($args['title']) ? (string) $args['title'] : '';
$lead          = isset($args['lead']) ? (string) $args['lead'] : '';
$items         = isset($args['items']) && is_array($args['items']) ? $args['items'] : array();

if ($title === '' && $lead === '' && empty($items)) {
    return;
}
?>
<section class="hub-section hub-section--support <?php echo esc_attr($section_class); ?>">
  <div class="hub-section__inner hub-container">
    <?php if ($title !== '' || $lead !== '') : ?>
      <div class="hub-section-head">
        <?php if ($title !== '') : ?>
          <h2 class="hub-heading is-lg"><?php echo esc_html($title); ?></h2>
        <?php endif; ?>

        <?php if ($lead !== '') : ?>
          <p class="hub-lead"><?php echo nl2br(esc_html($lead)); ?></p>
        <?php endif; ?>
      </div>
    <?php endif; ?>

    <?php if (!empty($items)) : ?>
      <div class="hub-support-grid">
        <?php foreach ($items as $item) : ?>
          <?php
          $label = isset($item['label']) ? (string) $item['label'] : '';
          $card_title = isset($item['title']) ? (string) $item['title'] : '';
          $text = isset($item['text']) ? (string) $item['text'] : '';
          $href = isset($item['href']) ? (string) $item['href'] : '';
          $tag  = $href !== '' ? 'a' : 'div';
          ?>
          <<?php echo $tag; ?>
            class="hub-support-card"
            <?php echo ($href !== '') ? 'href="' . esc_url($href) . '"' : ''; ?>
          >
            <?php if ($label !== '') : ?>
              <span class="hub-support-card__label"><?php echo esc_html($label); ?></span>
            <?php endif; ?>

            <?php if ($card_title !== '') : ?>
              <h3 class="hub-support-card__title"><?php echo esc_html($card_title); ?></h3>
            <?php endif; ?>

            <?php if ($text !== '') : ?>
              <p class="hub-support-card__text"><?php echo nl2br(esc_html($text)); ?></p>
            <?php endif; ?>
          </<?php echo $tag; ?>>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>
