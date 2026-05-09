<?php
if (!defined('ABSPATH')) {
    exit;
}

$meta = static function ($key, $default = '') use ($post_id) {
    $value = get_post_meta($post_id, $key, true);
    return $value !== '' ? $value : $default;
};

$image_tag = static function ($key, $alt = '', $size = 'large') use ($post_id) {
    $image_id = (int) get_post_meta($post_id, $key, true);
    if ($image_id > 0) {
        return wp_get_attachment_image(
            $image_id,
            $size,
            false,
            array(
                'alt'     => $alt,
                'loading' => 'lazy',
            )
        );
    }

    return '<img src="' . esc_url(get_template_directory_uri() . '/images/noimage.png') . '" alt="' . esc_attr($alt) . '">';
};

$body_title = $meta('_ch_nasu_body_title', '那須での家づくり');
$body_html  = apply_filters('the_content', get_post_field('post_content', $post_id));

$intro_eyebrow = $meta('_ch_nasu_intro_eyebrow', 'NASU CUSTOM HOME');
$intro_title   = $meta('_ch_nasu_intro_title', '那須での家づくり');
$intro_text    = $meta('_ch_nasu_intro_text', '那須の自然環境や暮らし方に寄り添いながら、景色・温熱・動線の整え方まで含めて、無理のない住まいの考え方を整理します。');

$cards_title = $meta('_ch_nasu_cards_title', '那須で考えたい <span>3つの視点</span>');

$card1_title = $meta('_ch_nasu_card1_title', '自然とつながる設計');
$card1_text  = $meta('_ch_nasu_card1_text', '景色や光を取り込みながら、那須らしい開放感を住まいの質へつなげるための考え方です。');

$card2_title = $meta('_ch_nasu_card2_title', '季節に配慮した住環境');
$card2_text  = $meta('_ch_nasu_card2_text', '寒暖差や湿度の変化を見据え、断熱・通風・日射の扱いを整えて快適な室内環境を支えます。');

$card3_title = $meta('_ch_nasu_card3_title', '長く使いやすい間取り');
$card3_text  = $meta('_ch_nasu_card3_text', '定住にも二拠点にも対応しやすいように、無理のない広さと日常の使いやすさを両立させます。');

$feature_eyebrow   = $meta('_ch_nasu_feature_eyebrow', 'FEATURE');
$feature_title     = $meta('_ch_nasu_feature_title', '自然と住まいの距離を、近づける。');
$feature_text1     = $meta('_ch_nasu_feature_text1', '眺望・光・風・動線を整理しながら、別荘のような開放感と日常の使いやすさを両立する住まいを考えます。');
$feature_text2     = $meta('_ch_nasu_feature_text2', '土地条件や景観、冬の過ごし方、家族構成をふまえ、那須での住み方に合った住まいをご提案します。');
$feature_btn_label = $meta('_ch_nasu_feature_btn_label', '無料相談・資料請求');
$feature_btn_url   = $meta('_ch_nasu_feature_btn_url', home_url('/iezukuri/contact/'));

$values_title = $meta('_ch_nasu_values_title', '住まいづくりで大切にしたいこと');

$value1_title = $meta('_ch_nasu_value1_title', '景色');
$value1_text  = $meta('_ch_nasu_value1_text', '窓の取り方と抜け感を整える。');

$value2_title = $meta('_ch_nasu_value2_title', '温熱');
$value2_text  = $meta('_ch_nasu_value2_text', '寒暖差への備えを考える。');

$value3_title = $meta('_ch_nasu_value3_title', '動線');
$value3_text  = $meta('_ch_nasu_value3_text', '使いやすい回遊計画を整える。');

$value4_title = $meta('_ch_nasu_value4_title', '素材');
$value4_text  = $meta('_ch_nasu_value4_text', '那須の空気感に合う質感を選ぶ。');

$cta_eyebrow         = $meta('_ch_nasu_cta_eyebrow', 'CONSULTATION');
$cta_title           = $meta('_ch_nasu_cta_title', '那須での家づくりを、具体的に相談する');
$cta_text            = $meta('_ch_nasu_cta_text', '土地探し・資金計画・間取りの考え方まで、那須での暮らしを前提にした家づくり相談を承ります。');
$cta_primary_label   = $meta('_ch_nasu_cta_primary_label', '無料相談・資料請求');
$cta_primary_url     = $meta('_ch_nasu_cta_primary_url', home_url('/iezukuri/contact/'));
$cta_secondary_label = $meta('_ch_nasu_cta_secondary_label', '注文住宅トップへ戻る');
$cta_secondary_url   = $meta('_ch_nasu_cta_secondary_url', home_url('/iezukuri/'));
?>

<div class="ch-nasu-shot-page">

    <section class="ch-nasu-shot-intro" id="intro">
        <div class="ch-nasu-shot-intro__header">
            <span class="ch-eyebrow"><?php echo esc_html($intro_eyebrow); ?></span>
            <h2><?php echo esc_html($intro_title); ?></h2>
            <p><?php echo nl2br(esc_html($intro_text)); ?></p>
        </div>
    </section>

    <?php if (trim(wp_strip_all_tags($body_html)) !== '') : ?>
        <section class="ch-nasu-shot-body" id="body">
            <div class="ch-nasu-shot-body__inner">
                <h3 class="ch-nasu-shot-section-title"><?php echo esc_html($body_title); ?></h3>
                <div class="ch-nasu-shot-body__content">
                    <?php echo $body_html; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <section class="ch-nasu-shot-cards-wrap" id="cards">
        <h3 class="ch-nasu-shot-section-title"><?php echo wp_kses($cards_title, array('span' => array())); ?></h3>

        <div class="ch-nasu-shot-cards">
            <article class="ch-nasu-shot-card">
                <div class="ch-nasu-shot-card__icon">01</div>
                <h4><?php echo esc_html($card1_title); ?></h4>
                <p><?php echo nl2br(esc_html($card1_text)); ?></p>
                <figure class="ch-nasu-shot-card__image">
                    <?php echo $image_tag('_ch_nasu_card1_image_id', $card1_title); ?>
                </figure>
            </article>

            <article class="ch-nasu-shot-card">
                <div class="ch-nasu-shot-card__icon">02</div>
                <h4><?php echo esc_html($card2_title); ?></h4>
                <p><?php echo nl2br(esc_html($card2_text)); ?></p>
                <figure class="ch-nasu-shot-card__image">
                    <?php echo $image_tag('_ch_nasu_card2_image_id', $card2_title); ?>
                </figure>
            </article>

            <article class="ch-nasu-shot-card">
                <div class="ch-nasu-shot-card__icon">03</div>
                <h4><?php echo esc_html($card3_title); ?></h4>
                <p><?php echo nl2br(esc_html($card3_text)); ?></p>
                <figure class="ch-nasu-shot-card__image">
                    <?php echo $image_tag('_ch_nasu_card3_image_id', $card3_title); ?>
                </figure>
            </article>
        </div>
    </section>

    <section class="ch-nasu-shot-feature" id="feature">
        <div class="ch-nasu-shot-feature__text">
            <span class="ch-eyebrow"><?php echo esc_html($feature_eyebrow); ?></span>
            <h3><?php echo esc_html($feature_title); ?></h3>
            <p><?php echo nl2br(esc_html($feature_text1)); ?></p>
            <p><?php echo nl2br(esc_html($feature_text2)); ?></p>
            <a class="ch-btn ch-btn--primary" href="<?php echo esc_url($feature_btn_url); ?>"><?php echo esc_html($feature_btn_label); ?></a>
        </div>

        <figure class="ch-nasu-shot-feature__image">
            <?php echo $image_tag('_ch_nasu_feature_image_id', $feature_title, 'large'); ?>
        </figure>
    </section>

    <section class="ch-nasu-shot-values" id="values">
        <h3 class="ch-nasu-shot-section-title"><?php echo esc_html($values_title); ?></h3>

        <div class="ch-nasu-shot-values__grid">
            <article class="ch-nasu-shot-value">
                <div class="ch-nasu-shot-value__icon">A</div>
                <h4><?php echo esc_html($value1_title); ?></h4>
                <p><?php echo nl2br(esc_html($value1_text)); ?></p>
            </article>

            <article class="ch-nasu-shot-value">
                <div class="ch-nasu-shot-value__icon">B</div>
                <h4><?php echo esc_html($value2_title); ?></h4>
                <p><?php echo nl2br(esc_html($value2_text)); ?></p>
            </article>

            <article class="ch-nasu-shot-value">
                <div class="ch-nasu-shot-value__icon">C</div>
                <h4><?php echo esc_html($value3_title); ?></h4>
                <p><?php echo nl2br(esc_html($value3_text)); ?></p>
            </article>

            <article class="ch-nasu-shot-value">
                <div class="ch-nasu-shot-value__icon">D</div>
                <h4><?php echo esc_html($value4_title); ?></h4>
                <p><?php echo nl2br(esc_html($value4_text)); ?></p>
            </article>
        </div>
    </section>

</div>


