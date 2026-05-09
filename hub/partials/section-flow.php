<?php
if (!defined("ABSPATH")) {
    exit;
}

/**
 * =========================================================
 * Hub Flow セクション共通部品
 * 役割:
 * - title / lead / items を受け取って Flow を描画する
 * - テンプレート側の直書きを減らす
 * =========================================================
 */
$section_class = isset($args['section_class']) ? trim((string) $args['section_class']) : '';
$title         = isset($args['title']) ? (string) $args['title'] : '';
$lead          = isset($args['lead']) ? (string) $args['lead'] : '';
$aria_label    = isset($args['aria_label']) ? (string) $args['aria_label'] : '';
$items         = isset($args['items']) && is_array($args['items']) ? $args['items'] : array();

if ($title === '' && $lead === '' && empty($items)) {
    return;
}
?>
<section class="hub-section hub-section--flow <?php echo esc_attr($section_class); ?>">
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
      <ol class="hub-flow-list" <?php echo ($aria_label !== '') ? 'aria-label="' . esc_attr($aria_label) . '"' : ''; ?>>
        <?php foreach ($items as $item) : ?>
          <li>
            <?php if (!empty($item['step'])) : ?>
              <span class="hub-flow-step"><?php echo esc_html($item['step']); ?></span>
            <?php endif; ?>

            <?php if (!empty($item['title'])) : ?>
              <h3 class="hub-flow-title"><?php echo esc_html($item['title']); ?></h3>
            <?php endif; ?>

            <?php if (!empty($item['text'])) : ?>
              <p class="hub-flow-text"><?php echo nl2br(esc_html($item['text'])); ?></p>
            <?php endif; ?>
          </li>
        <?php endforeach; ?>
      </ol>
    <?php endif; ?>
  </div>
</section>
