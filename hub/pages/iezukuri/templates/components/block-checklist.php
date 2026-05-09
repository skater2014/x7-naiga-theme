<?php
if (!defined('ABSPATH')) {
    exit;
}

$title = $args['title'] ?? '確認するポイント';
$items = $args['items'] ?? array();
?>

<section class="iez-block iez-block-checklist">
    <div class="iez-block__inner">
        <h2 class="iez-block__title">
            <?php echo esc_html($title); ?>
        </h2>

        <?php if (!empty($items)) : ?>
            <div class="iez-checklist-grid">
                <?php foreach ($items as $item) : ?>
                    <div class="iez-checklist-card">
                        <span class="iez-checklist-card__mark">✓</span>
                        <p><?php echo esc_html($item); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
