<?php
if (!defined('ABSPATH')) { exit; }
?>

<section class="ch-section ch-section--white ch-top-routes" aria-labelledby="ch-top-routes-title">
    <div class="ch-shell">
        <div class="ch-head">
            <p class="ch-eyebrow">Entrance</p>
            <h2 id="ch-top-routes-title" class="ch-section-title">暮らし方から選ぶ住まいの入口</h2>
            <p class="ch-top-routes__lead">
                まずは近い暮らし方を選んでください。写真・間取り・改善ポイントを見ながら、次に見る内容へ進めます。
            </p>
        </div>

        <div class="ch-route-grid">
            <?php foreach ($gateway_items as $item) : ?>
                <article class="ch-route-card">
                    <a href="<?php echo esc_url($item['url']); ?>">
                        <div class="ch-route-card__image">
                            <?php if (!empty($item['image'])) : ?>
                                <img src="<?php echo esc_url($item['image']); ?>" alt="<?php echo esc_attr($item['title']); ?>">
                            <?php else : ?>
                                <span><?php echo esc_html($item['label']); ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="ch-route-card__body">
                            <p class="ch-route-card__label"><?php echo esc_html($item['label']); ?></p>
                            <h3><?php echo esc_html($item['title']); ?></h3>
                            <p><?php echo esc_html($item['text']); ?></p>
                            <b>詳しく見る</b>
                        </div>
                    </a>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
