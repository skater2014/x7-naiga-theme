<?php
if (!defined('ABSPATH')) { exit; }

$features  = !empty($item['features']) && is_array($item['features']) ? $item['features'] : array();
$equipment = !empty($item['equipment']) && is_array($item['equipment']) ? $item['equipment'] : array();
?>

<section class="ch-service-story">
    <p class="ch-eyebrow">Story</p>
    <h3><?php echo esc_html($item['story_title'] ?? '暮らし方から住まいを考える。'); ?></h3>
    <p><?php echo esc_html($item['story_text'] ?? '間取り、収納、水回り、設備、将来の使い方まで含めて、暮らしやすい住まいを整理します。'); ?></p>
</section>

<div class="ch-service-detail-grid">
    <section>
        <h4>間取りの特徴</h4>
        <ul>
            <?php foreach ($features as $feature) : ?>
                <li><?php echo esc_html($feature); ?></li>
            <?php endforeach; ?>
        </ul>
    </section>

    <section>
        <h4>設備・性能・メンテナンス</h4>
        <ul>
            <?php foreach ($equipment as $point) : ?>
                <li><?php echo esc_html($point); ?></li>
            <?php endforeach; ?>
        </ul>
    </section>
</div>
