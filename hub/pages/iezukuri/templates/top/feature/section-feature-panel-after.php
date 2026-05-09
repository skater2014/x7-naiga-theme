<?php
if (!defined('ABSPATH')) { exit; }

$term = $item['term'] ?? '';

$features = !empty($item['features']) && is_array($item['features'])
    ? $item['features']
    : array('暮らし方に合わせて動線を整理', '収納と水回りを使いやすく計画', '将来の使い方まで考える');

$equipment = !empty($item['equipment']) && is_array($item['equipment'])
    ? $item['equipment']
    : array('断熱・窓・換気を確認', '水回りとコンセント位置を整理', 'メンテナンスしやすい状態に整える');
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

<?php include get_template_directory() . '/hub/pages/iezukuri/templates/top/feature/section-feature-compare.php'; ?>

<section class="ch-service-next" aria-label="次に見るプラン">
    <div class="ch-service-next__head">
        <p class="ch-eyebrow">Next Plan</p>
        <h3>次に見るプラン</h3>
    </div>

    <div class="ch-service-next-grid">
        <?php $next_number = 2; ?>

        <?php foreach ($service_items as $next_panel_index => $next_item) : ?>
            <?php if (($next_item['term'] ?? '') === $term) { continue; } ?>
            <?php $next_media = naigai_iez_top_service_media($next_item); ?>

            <article class="ch-service-next-card">
                <label
                    class="ch-service-next-card__select"
                    for="ch-service-panel-<?php echo esc_attr($next_panel_index); ?>"
                    data-iez-next-card>
                    <span class="ch-service-next-card__num"><?php echo esc_html(str_pad((string) $next_number, 2, '0', STR_PAD_LEFT)); ?></span>

                    <span class="ch-service-next-card__image">
                        <?php naigai_iez_top_media_image($next_media['exterior'], ($next_item['subtitle'] ?? $next_item['title']) . ' 外観'); ?>
                    </span>

                    <span class="ch-service-next-card__body">
                        <strong><?php echo esc_html($next_item['title']); ?></strong>
                        <em><?php echo esc_html($next_item['subtitle'] ?? ''); ?></em>
                        <small><?php echo esc_html($next_item['text']); ?></small>
                    </span>
                </label>
            </article>

            <?php $next_number++; ?>
        <?php endforeach; ?>
    </div>
</section>
