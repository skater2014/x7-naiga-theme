<?php
/**
 * =========================================================
 * hub/partials/section-plan-tabs.php
 *
 * 重要:
 * - このパーツは現在表示中のページIDのメタだけ読む
 * - design-office 本体ページの文章・画像は引き継がない
 * - 未入力ならデフォルト文書を出さない
 * - 同じ _ch_plan_* メタキーでも post_id が違えば別データ
 * =========================================================
 */

if (!defined('ABSPATH')) {
    exit;
}

$post_id = isset($GLOBALS['naigai_current_customhome_subpage_id'])
    ? absint($GLOBALS['naigai_current_customhome_subpage_id'])
    : get_queried_object_id();

if (!$post_id) {
    return;
}

$plan_meta = static function ($key, $default = '') use ($post_id) {
    $value = get_post_meta($post_id, $key, true);
    return ($value !== '' && $value !== array() && $value !== null) ? $value : $default;
};

$plan_image_url = static function ($url_key, $image_id_key = '') use ($post_id, $plan_meta) {
    if ($image_id_key !== '') {
        $image_id = absint(get_post_meta($post_id, $image_id_key, true));
        if ($image_id) {
            $url = wp_get_attachment_image_url($image_id, 'large');
            if ($url) {
                return $url;
            }
        }
    }

    $raw = trim((string) $plan_meta($url_key, ''));

    if ($raw === '') {
        return '';
    }

    if (ctype_digit($raw)) {
        $url = wp_get_attachment_image_url((int) $raw, 'large');
        return $url ?: '';
    }

    return esc_url_raw($raw);
};

$plan_link_url = static function ($url_key, $page_id_key = '') use ($post_id, $plan_meta) {
    if ($page_id_key !== '') {
        $page_id = absint(get_post_meta($post_id, $page_id_key, true));
        if ($page_id) {
            $url = get_permalink($page_id);
            if ($url) {
                return $url;
            }
        }
    }

    return trim((string) $plan_meta($url_key, ''));
};

$section_title = $plan_meta('_ch_design_office_plan_intro_title', '');
$section_lead  = $plan_meta('_ch_design_office_plan_intro_text', '');

$plans = array();

foreach (array('a', 'b', 'c') as $key) {
    $prefix = '_ch_plan_' . $key . '_';

    $label      = $plan_meta($prefix . 'label', '');
    $type       = $plan_meta($prefix . 'type', '');
    $area       = $plan_meta($prefix . 'area', '');
    $build_area = $plan_meta($prefix . 'build_area', '');
    $hero       = $plan_image_url($prefix . 'hero', $prefix . 'hero_image_id');
    $plan       = $plan_image_url($prefix . 'plan', $prefix . 'plan_image_id');
    $desc       = $plan_meta($prefix . 'desc', '');
    $point1     = $plan_meta($prefix . 'point1', '');
    $point2     = $plan_meta($prefix . 'point2', '');
    $point3     = $plan_meta($prefix . 'point3', '');
    $detail_url = $plan_link_url($prefix . 'detail_url', $prefix . 'detail_page_id');

    $has_any = false;
    foreach (array($label, $type, $area, $build_area, $hero, $plan, $desc, $point1, $point2, $point3, $detail_url) as $v) {
        if ($v !== '') {
            $has_any = true;
            break;
        }
    }

    if (!$has_any) {
        continue;
    }

    if ($label === '') {
        $label = 'PLAN ' . strtoupper($key);
    }

    $plans[$key] = array(
        'label'      => $label,
        'type'       => $type,
        'area'       => $area,
        'build_area' => $build_area,
        'hero'       => $hero,
        'plan'       => $plan,
        'desc'       => $desc,
        'points'     => array_filter(array($point1, $point2, $point3), static function ($v) {
            return $v !== '';
        }),
        'detail_url' => $detail_url,
    );
}

if (empty($plans) && $section_title === '' && $section_lead === '') {
    return;
}
?>

<section id="lineup-plans" class="ch-section ch-section--white ch-plan-tabs">
    <div class="ch-shell">
        <?php if ($section_title !== '' || $section_lead !== '') : ?>
            <div class="ch-head">
                <span class="ch-eyebrow">LINEUP</span>
                <?php if ($section_title !== '') : ?>
                    <h2 class="ch-section-title"><?php echo esc_html($section_title); ?></h2>
                <?php endif; ?>
                <?php if ($section_lead !== '') : ?>
                    <p class="ch-plan-tabs__lead"><?php echo nl2br(esc_html($section_lead)); ?></p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($plans)) : ?>
            <div class="ch-plan-tabs__box">
                <?php foreach ($plans as $key => $plan) : ?>
                    <input class="ch-plan-tabs__radio" type="radio" name="plan-tabs-<?php echo esc_attr($post_id); ?>" id="plan-tab-<?php echo esc_attr($post_id . '-' . $key); ?>" <?php echo $key === array_key_first($plans) ? 'checked' : ''; ?>>
                <?php endforeach; ?>

                <div class="ch-plan-tabs__nav" role="tablist" aria-label="プラン切り替え">
                    <?php foreach ($plans as $key => $plan) : ?>
                        <label class="ch-plan-tabs__tab" for="plan-tab-<?php echo esc_attr($post_id . '-' . $key); ?>">
                            <span class="ch-plan-tabs__tab-badge"><?php echo esc_html($plan['label']); ?></span>
                            <?php if ($plan['type'] !== '') : ?>
                                <span class="ch-plan-tabs__tab-type"><?php echo esc_html($plan['type']); ?></span>
                            <?php endif; ?>
                        </label>
                    <?php endforeach; ?>
                </div>

                <div class="ch-plan-tabs__panels">
                    <?php foreach ($plans as $key => $plan) : ?>
                        <section class="ch-plan-tabs__panel ch-plan-tabs__panel--<?php echo esc_attr($key); ?>">
                            <div class="ch-plan-tabs__panel-grid">
                                <div class="ch-plan-tabs__media">
                                    <?php if ($plan['hero'] !== '') : ?>
                                        <figure class="ch-plan-tabs__hero-image">
                                            <img src="<?php echo esc_url($plan['hero']); ?>" alt="<?php echo esc_attr($plan['label'] . ' 外観イメージ'); ?>">
                                        </figure>
                                    <?php endif; ?>

                                    <?php if ($plan['area'] !== '' || $plan['build_area'] !== '' || $plan['type'] !== '') : ?>
                                        <div class="ch-plan-tabs__summary-card">
                                            <div class="ch-plan-tabs__summary-head">
                                                <span class="ch-plan-tabs__summary-badge"><?php echo esc_html($plan['label']); ?></span>
                                                <?php if ($plan['type'] !== '') : ?>
                                                    <strong><?php echo esc_html($plan['type']); ?></strong>
                                                <?php endif; ?>
                                            </div>

                                            <dl class="ch-plan-tabs__stats">
                                                <?php if ($plan['area'] !== '') : ?>
                                                    <div>
                                                        <dt>延床面積</dt>
                                                        <dd><?php echo esc_html($plan['area']); ?></dd>
                                                    </div>
                                                <?php endif; ?>

                                                <?php if ($plan['build_area'] !== '') : ?>
                                                    <div>
                                                        <dt>建築面積</dt>
                                                        <dd><?php echo esc_html($plan['build_area']); ?></dd>
                                                    </div>
                                                <?php endif; ?>
                                            </dl>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="ch-plan-tabs__detail">
                                    <?php if ($plan['plan'] !== '') : ?>
                                        <figure class="ch-plan-tabs__plan-image">
                                            <img src="<?php echo esc_url($plan['plan']); ?>" alt="<?php echo esc_attr($plan['label'] . ' 間取り図'); ?>">
                                        </figure>
                                    <?php endif; ?>

                                    <div class="ch-plan-tabs__text">
                                        <?php if ($plan['desc'] !== '') : ?>
                                            <h3><?php echo esc_html($plan['desc']); ?></h3>
                                        <?php endif; ?>

                                        <?php if (!empty($plan['points'])) : ?>
                                            <ul class="ch-plan-tabs__points">
                                                <?php foreach ($plan['points'] as $point) : ?>
                                                    <li><?php echo esc_html($point); ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php endif; ?>

                                        <?php if ($plan['detail_url'] !== '') : ?>
                                            <a class="ch-btn ch-btn--primary" href="<?php echo esc_url($plan['detail_url']); ?>">詳しく見る</a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </section>
                    <?php endforeach; ?>
                </div>

                <?php
                $plans_with_thumbs = array_filter($plans, static function ($plan) {
                    return $plan['hero'] !== '';
                });
                ?>

                <?php if (!empty($plans_with_thumbs)) : ?>
                    <div class="ch-plan-tabs__thumbs">
                        <?php foreach ($plans_with_thumbs as $key => $plan) : ?>
                            <label class="ch-plan-tabs__thumb" for="plan-tab-<?php echo esc_attr($post_id . '-' . $key); ?>">
                                <img src="<?php echo esc_url($plan['hero']); ?>" alt="<?php echo esc_attr($plan['label']); ?>">
                                <div class="ch-plan-tabs__thumb-meta">
                                    <strong><?php echo esc_html($plan['label']); ?></strong>
                                    <?php if ($plan['type'] !== '' || $plan['area'] !== '') : ?>
                                        <span><?php echo esc_html(trim($plan['type'] . ' / ' . $plan['area'], ' /')); ?></span>
                                    <?php endif; ?>
                                </div>
                            </label>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
