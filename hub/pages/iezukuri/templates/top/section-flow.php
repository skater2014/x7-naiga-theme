<?php
/**
 * hub/pages/iezukuri/templates/top/section-flow.php
 * 役割: 家づくりの流れ
 * 元データ: 分割実行時点の template-iezukuri.php
 */
if (!defined('ABSPATH')) {
    exit;
}
?>
<section id="customhome-flow" class="ch-section" data-nav-section="flow">
        <div class="ch-shell">
            <div class="ch-head">
                <div class="ch-eyebrow"><?php echo esc_html($flow_eyebrow); ?></div>
                <h2 class="ch-section-title"><?php echo esc_html($flow_title); ?></h2>
            </div>

            <ol class="ch-flow-list">
                <?php foreach ($flows as $flow) : ?>
                    <li class="ch-flow-item">
                        <div class="ch-flow-item__num"><?php echo esc_html($flow['num']); ?></div>
                        <div class="ch-flow-item__icon" aria-hidden="true"><?php echo $svg_icon($flow['icon']); ?></div>
                        <h3 class="ch-flow-item__title"><?php echo esc_html($flow['title']); ?></h3>
                        <p class="ch-flow-item__text"><?php echo esc_html($flow['text']); ?></p>
                    </li>
                <?php endforeach; ?>
            </ol>
        </div>
    </section>
