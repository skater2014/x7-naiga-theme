<?php
/**
 * /iezukuri トップ：taxonomy に紐づく iez_plan 詳細一覧 fragment
 */

if (!defined('ABSPATH')) {
    exit;
}

$taxonomy = 'iez_plan_type';

$term_slug = isset($iez_plan_term_slug) ? sanitize_title($iez_plan_term_slug) : '';
$term = null;

if ($term_slug !== '') {
    $term = get_term_by('slug', $term_slug, $taxonomy);
}

if (!$term || is_wp_error($term)) {
    $terms = get_terms(array(
        'taxonomy'   => $taxonomy,
        'hide_empty' => false,
        'number'     => 1,
        'orderby'    => 'term_id',
        'order'      => 'ASC',
    ));

    if (!is_wp_error($terms) && !empty($terms)) {
        $term = $terms[0];
    }
}

if (!$term || is_wp_error($term)) {
    if (current_user_can('edit_posts')) {
        echo '<section class="ch-section"><div class="ch-shell"><p>表示するタームがありません。</p></div></section>';
    }
    return;
}

$term_title = $term->name;
$term_desc = $term->description ? wp_strip_all_tags($term->description) : 'この種類の間取り詳細を確認できます。';

$plan_query = new WP_Query(array(
    'post_type'      => 'iez_plan',
    'posts_per_page' => 6,
    'post_status'    => 'publish',
    'tax_query'      => array(
        array(
            'taxonomy' => $taxonomy,
            'field'    => 'term_id',
            'terms'    => array((int) $term->term_id),
        ),
    ),
));

$plans = array();

if ($plan_query->have_posts()) {
    while ($plan_query->have_posts()) {
        $plan_query->the_post();

        $plans[] = array(
            'id'    => get_the_ID(),
            'title' => get_the_title(),
            'url'   => get_permalink(),
            'text'  => get_the_excerpt()
                ? wp_strip_all_tags(get_the_excerpt())
                : wp_trim_words(wp_strip_all_tags(get_the_content()), 44),
            'image' => has_post_thumbnail() ? get_the_post_thumbnail_url(get_the_ID(), 'large') : '',
        );
    }

    wp_reset_postdata();
}

$flow_items = array(
    array('相談', '希望する暮らし方、人数、予算、土地や建物の状況を整理します。'),
    array('確認', '必要な条件、優先順位、将来の変化を確認します。'),
    array('間取り確認', 'この種類に紐づく間取り詳細を見ながら方向性を決めます。'),
    array('仕様整理', '水回り、収納、動線、性能、素材を整理します。'),
    array('計画', '費用感と工事内容を確認し、具体的な計画にします。'),
    array('相談・資料請求', '必要な資料や相談内容をまとめて次へ進みます。'),
);
?>

<section class="ch-section ch-site-reading" aria-labelledby="ch-site-reading-title">
    <div class="ch-shell ch-site-reading__grid">
        <div>
            <p class="ch-eyebrow">家づくりの考え方</p>
            <h2 id="ch-site-reading-title" class="ch-section-title">
                <?php echo esc_html($term_title); ?><br>から考える住まい
            </h2>
        </div>

        <div class="ch-site-reading__body">
            <p><?php echo esc_html($term_desc); ?></p>

            <p class="ch-site-reading__notice">
                ※上の3枚カードに合わせて、同じ場所で中身だけ切り替わります。
            </p>

            <a class="ch-inline-link" href="<?php echo esc_url(get_term_link($term)); ?>">
                <?php echo esc_html($term_title); ?>の一覧を見る
            </a>
        </div>
    </div>
</section>

<section id="customhome-works" class="ch-section ch-section--white" aria-labelledby="customhome-works-title">
    <div class="ch-shell">
        <div class="ch-head ch-head--with-link">
            <div>
                <p class="ch-eyebrow">Works</p>
                <h2 id="customhome-works-title" class="ch-section-title">
                    <?php echo esc_html($term_title); ?>の関連プラン
                </h2>
            </div>

            <a class="ch-inline-link" href="<?php echo esc_url(get_term_link($term)); ?>">
                一覧を見る
            </a>
        </div>

        <div class="ch-works-grid">
            <?php if (!empty($plans)) : ?>
                <?php foreach (array_slice($plans, 0, 4) as $plan) : ?>
                    <article class="ch-work-card">
                        <a href="<?php echo esc_url($plan['url']); ?>">
                            <div class="ch-work-card__thumb">
                                <?php if (!empty($plan['image'])) : ?>
                                    <img class="ch-work-card__image" src="<?php echo esc_url($plan['image']); ?>" alt="<?php echo esc_attr($plan['title']); ?>">
                                <?php else : ?>
                                    <span><?php echo esc_html($term_title); ?></span>
                                <?php endif; ?>
                                <span class="ch-work-card__badge"><?php echo esc_html($term_title); ?></span>
                            </div>

                            <div class="ch-work-card__body">
                                <h3 class="ch-work-card__title"><?php echo esc_html($plan['title']); ?></h3>
                                <p class="ch-work-card__text"><?php echo esc_html($plan['text']); ?></p>
                            </div>
                        </a>
                    </article>
                <?php endforeach; ?>
            <?php else : ?>
                <p class="ch-top-routes__lead">この種類に紐づく間取り詳細ページはまだありません。</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<section id="iez-top-plan" class="ch-section" aria-labelledby="iez-top-plan-title">
    <div class="ch-shell">
        <div class="ch-head">
            <p class="ch-eyebrow">Plans</p>
            <h2 id="iez-top-plan-title" class="ch-section-title">間取りとプラン</h2>
            <p class="ch-top-routes__lead">
                <?php echo esc_html($term_title); ?>に紐づく間取り詳細ページを表示します。
            </p>
        </div>

        <div class="ch-route-grid">
            <?php if (!empty($plans)) : ?>
                <?php foreach (array_slice($plans, 0, 3) as $index => $plan) : ?>
                    <article class="ch-route-card">
                        <a href="<?php echo esc_url($plan['url']); ?>">
                            <div class="ch-route-card__image">
                                <?php if (!empty($plan['image'])) : ?>
                                    <img src="<?php echo esc_url($plan['image']); ?>" alt="<?php echo esc_attr($plan['title']); ?>">
                                <?php else : ?>
                                    <span><?php echo esc_html(sprintf('%02d', $index + 1)); ?></span>
                                <?php endif; ?>
                            </div>

                            <div class="ch-route-card__body">
                                <p class="ch-route-card__label"><?php echo esc_html($term_title); ?></p>
                                <h3><?php echo esc_html($plan['title']); ?></h3>
                                <p><?php echo esc_html($plan['text']); ?></p>
                                <b>間取り詳細を見る</b>
                            </div>
                        </a>
                    </article>
                <?php endforeach; ?>
            <?php else : ?>
                <p class="ch-top-routes__lead">表示できる間取り詳細ページがありません。</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<section id="customhome-flow" class="ch-section ch-section--white" aria-labelledby="customhome-flow-title">
    <div class="ch-shell">
        <div class="ch-head">
            <p class="ch-eyebrow">Flow</p>
            <h2 id="customhome-flow-title" class="ch-section-title">
                <?php echo esc_html($term_title); ?>の流れ
            </h2>
        </div>

        <ol class="ch-flow-list">
            <?php foreach ($flow_items as $index => $flow) : ?>
                <?php $num = sprintf('%02d', $index + 1); ?>
                <li class="ch-flow-item">
                    <div class="ch-flow-item__num"><?php echo esc_html($num); ?></div>
                    <div class="ch-flow-item__icon" aria-hidden="true"><span><?php echo esc_html($num); ?></span></div>
                    <h3 class="ch-flow-item__title"><?php echo esc_html($flow[0]); ?></h3>
                    <p class="ch-flow-item__text"><?php echo esc_html($flow[1]); ?></p>
                </li>
            <?php endforeach; ?>
        </ol>
    </div>
</section>

<section id="customhome-contact" class="ch-section ch-cta" aria-labelledby="customhome-contact-title">
    <div class="ch-shell">
        <div class="ch-cta__inner">
            <p class="ch-eyebrow">CTA</p>
            <h2 id="customhome-contact-title" class="ch-cta__title">
                <?php echo esc_html($term_title); ?>を相談する
            </h2>
            <p class="ch-cta__text">
                希望する暮らし方や間取りの方向性を整理するところから相談できます。
            </p>

            <div class="ch-cta__actions">
                <a class="ch-button ch-button--primary" href="<?php echo esc_url(home_url('/contact/')); ?>">
                    無料相談・資料請求
                </a>
                <a class="ch-button ch-button--ghost" href="<?php echo esc_url(get_term_link($term)); ?>">
                    一覧を見る
                </a>
            </div>
        </div>
    </div>
</section>
