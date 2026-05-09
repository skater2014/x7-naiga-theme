<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * =========================================================
 * nasu-house 専用テンプレート
 * 方針:
 * - 共通 .ch-subpage-layout は使わない
 * - .ch-nasu-shot-* 専用HTMLで組む
 * - 画像や文言はまず安全なフォールバック付き
 * - 必要なら後でメタキー読込へ差し替え可能
 * =========================================================
 */

$post_id   = get_the_ID();
$post_slug = $post->post_name ?? '';

$title = get_the_title($post_id);
$intro_title = get_post_meta($post_id, '_ch_nasu_intro_title', true);
$intro_text  = get_post_meta($post_id, '_ch_nasu_intro_text', true);

if ($intro_title === '') {
    $intro_title = '那須で家を建てるという選択';
}
if ($intro_text === '') {
    $intro_text = '那須の自然や暮らし方に寄り添いながら、住まいの性能・使いやすさ・景色とのつながりを丁寧に整えるための家づくりをご案内します。';
}

$feature_title = get_post_meta($post_id, '_ch_nasu_feature_title', true);
$feature_text  = get_post_meta($post_id, '_ch_nasu_feature_text', true);

if ($feature_title === '') {
    $feature_title = '自然と住まいの距離を、近づける。';
}
if ($feature_text === '') {
    $feature_text = '眺望・光・風・動線を整理しながら、別荘のような開放感と日常の使いやすさを両立する住まいを考えます。';
}

$cta_title = get_post_meta($post_id, '_ch_nasu_cta_title', true);
$cta_text  = get_post_meta($post_id, '_ch_nasu_cta_text', true);

if ($cta_title === '') {
    $cta_title = '那須での家づくりを、具体的に相談する';
}
if ($cta_text === '') {
    $cta_text = '土地探し・資金計画・間取りの考え方まで、那須での暮らしを前提にした家づくり相談を承ります。';
}

$hero_image_id    = (int) get_post_meta($post_id, '_ch_nasu_hero_image_id', true);
$feature_image_id = (int) get_post_meta($post_id, '_ch_nasu_feature_image_id', true);
$card1_image_id   = (int) get_post_meta($post_id, '_ch_nasu_card1_image_id', true);
$card2_image_id   = (int) get_post_meta($post_id, '_ch_nasu_card2_image_id', true);
$card3_image_id   = (int) get_post_meta($post_id, '_ch_nasu_card3_image_id', true);

$hero_image    = $hero_image_id ? wp_get_attachment_image_url($hero_image_id, 'full') : '';
$feature_image = $feature_image_id ? wp_get_attachment_image_url($feature_image_id, 'large') : '';
$card1_image   = $card1_image_id ? wp_get_attachment_image_url($card1_image_id, 'large') : '';
$card2_image   = $card2_image_id ? wp_get_attachment_image_url($card2_image_id, 'large') : '';
$card3_image   = $card3_image_id ? wp_get_attachment_image_url($card3_image_id, 'large') : '';

if ($hero_image === '') {
    $hero_image = get_template_directory_uri() . '/images/noimage.png';
}
if ($feature_image === '') {
    $feature_image = get_template_directory_uri() . '/images/noimage.png';
}
if ($card1_image === '') {
    $card1_image = get_template_directory_uri() . '/images/noimage.png';
}
if ($card2_image === '') {
    $card2_image = get_template_directory_uri() . '/images/noimage.png';
}
if ($card3_image === '') {
    $card3_image = get_template_directory_uri() . '/images/noimage.png';
}

$card1_title = get_post_meta($post_id, '_ch_nasu_card1_title', true) ?: '自然とつながる設計';
$card1_text  = get_post_meta($post_id, '_ch_nasu_card1_text', true) ?: '外の景色や光を暮らしの中へ取り込み、那須らしい開放感を住まいの質に変えていきます。';

$card2_title = get_post_meta($post_id, '_ch_nasu_card2_title', true) ?: '季節に配慮した住環境';
$card2_text  = get_post_meta($post_id, '_ch_nasu_card2_text', true) ?: '寒暖差や湿度の変化を見据えて、断熱・通風・動線を整え、快適さを支える住環境をつくります。';

$card3_title = get_post_meta($post_id, '_ch_nasu_card3_title', true) ?: '長く使いやすい間取り';
$card3_text  = get_post_meta($post_id, '_ch_nasu_card3_text', true) ?: '定住にも二拠点にも対応しやすい、無理のない広さと使い勝手を大切にしたプランをご提案します。';

$point1_title = get_post_meta($post_id, '_ch_nasu_point1_title', true) ?: '景色';
$point1_text  = get_post_meta($post_id, '_ch_nasu_point1_text', true) ?: '窓の取り方と抜け感';

$point2_title = get_post_meta($post_id, '_ch_nasu_point2_title', true) ?: '温熱';
$point2_text  = get_post_meta($post_id, '_ch_nasu_point2_text', true) ?: '寒暖差への備え';

$point3_title = get_post_meta($post_id, '_ch_nasu_point3_title', true) ?: '動線';
$point3_text  = get_post_meta($post_id, '_ch_nasu_point3_text', true) ?: '使いやすい回遊計画';

$point4_title = get_post_meta($post_id, '_ch_nasu_point4_title', true) ?: '素材';
$point4_text  = get_post_meta($post_id, '_ch_nasu_point4_text', true) ?: '那須の空気感に合う質感';

$primary_url   = home_url('/contact/');
$primary_label = '無料相談・資料請求';
$secondary_url = home_url('/iezukuri/');
$secondary_label = '注文住宅トップへ戻る';
?>

<main class="hub-customhome-page hub-customhome-subpage hub-customhome-subpage--<?php echo esc_attr($post_slug); ?>">
    <section class="ch-hero ch-subpage-hero">
        <div class="ch-hero__media">
            <img class="ch-hero__image" src="<?php echo esc_url($hero_image); ?>" alt="<?php echo esc_attr($title); ?>">
            <div class="ch-hero__overlay"></div>
        </div>

        <div class="ch-shell">
            <div class="ch-hero__inner">
                <div class="ch-hero__content">
                    <span class="ch-eyebrow ch-eyebrow--light">CUSTOM HOME IN NASU</span>
                    <h1 class="ch-hero__title"><?php echo esc_html($title); ?></h1>
                    <p class="ch-hero__lead"><?php echo esc_html($intro_text); ?></p>
                    <div class="ch-hero__actions">
                        <a class="ch-btn ch-btn--primary" href="<?php echo esc_url($primary_url); ?>"><?php echo esc_html($primary_label); ?></a>
                        <a class="ch-btn ch-btn--ghost-light" href="<?php echo esc_url($secondary_url); ?>"><?php echo esc_html($secondary_label); ?></a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <nav class="ch-localnav ch-localnav--underline" aria-label="那須の家づくりページ内ナビ">
        <div class="ch-shell">
            <ul class="ch-localnav__list">
                <li><a href="#intro">導入</a></li>
                <li><a href="#cards">特徴</a></li>
                <li><a href="#feature">考え方</a></li>
                <li><a href="#values">大切にすること</a></li>
                <li><a href="#cta">相談</a></li>
            </ul>
        </div>
    </nav>

    <section id="intro" class="ch-subpage-section">
        <div class="ch-nasu-shot-page">

            <section class="ch-nasu-shot-intro">
                <div class="ch-nasu-shot-intro__header">
                    <span class="ch-eyebrow">INTRODUCTION</span>
                    <h2><?php echo esc_html($intro_title); ?></h2>
                    <p><?php echo esc_html($intro_text); ?></p>
                </div>
            </section>

            <section id="cards" class="ch-nasu-shot-cards-wrap">
                <h3 class="ch-nasu-shot-section-title">那須での住まいに求めたい <span>3つの視点</span></h3>
                <div class="ch-nasu-shot-cards">
                    <article class="ch-nasu-shot-card">
                        <div class="ch-nasu-shot-card__icon">01</div>
                        <h4><?php echo esc_html($card1_title); ?></h4>
                        <p><?php echo esc_html($card1_text); ?></p>
                        <figure class="ch-nasu-shot-card__image">
                            <img src="<?php echo esc_url($card1_image); ?>" alt="<?php echo esc_attr($card1_title); ?>">
                        </figure>
                    </article>

                    <article class="ch-nasu-shot-card">
                        <div class="ch-nasu-shot-card__icon">02</div>
                        <h4><?php echo esc_html($card2_title); ?></h4>
                        <p><?php echo esc_html($card2_text); ?></p>
                        <figure class="ch-nasu-shot-card__image">
                            <img src="<?php echo esc_url($card2_image); ?>" alt="<?php echo esc_attr($card2_title); ?>">
                        </figure>
                    </article>

                    <article class="ch-nasu-shot-card">
                        <div class="ch-nasu-shot-card__icon">03</div>
                        <h4><?php echo esc_html($card3_title); ?></h4>
                        <p><?php echo esc_html($card3_text); ?></p>
                        <figure class="ch-nasu-shot-card__image">
                            <img src="<?php echo esc_url($card3_image); ?>" alt="<?php echo esc_attr($card3_title); ?>">
                        </figure>
                    </article>
                </div>
            </section>

            <section id="feature" class="ch-nasu-shot-feature">
                <div class="ch-nasu-shot-feature__text">
                    <span class="ch-eyebrow">FEATURE</span>
                    <h3><?php echo esc_html($feature_title); ?></h3>
                    <p><?php echo esc_html($feature_text); ?></p>
                    <p>土地条件や景観、冬の過ごし方、家族構成などをふまえながら、那須で無理なく暮らせる住まいの形を整理していきます。</p>
                    <a class="ch-btn ch-btn--primary" href="<?php echo esc_url($primary_url); ?>"><?php echo esc_html($primary_label); ?></a>
                </div>

                <figure class="ch-nasu-shot-feature__image">
                    <img src="<?php echo esc_url($feature_image); ?>" alt="<?php echo esc_attr($feature_title); ?>">
                </figure>
            </section>

            <section id="values" class="ch-nasu-shot-values">
                <h3 class="ch-nasu-shot-section-title">住まいづくりで大切にしたいこと</h3>
                <div class="ch-nasu-shot-values__grid">
                    <article class="ch-nasu-shot-value">
                        <div class="ch-nasu-shot-value__icon">A</div>
                        <h4><?php echo esc_html($point1_title); ?></h4>
                        <p><?php echo esc_html($point1_text); ?></p>
                    </article>
                    <article class="ch-nasu-shot-value">
                        <div class="ch-nasu-shot-value__icon">B</div>
                        <h4><?php echo esc_html($point2_title); ?></h4>
                        <p><?php echo esc_html($point2_text); ?></p>
                    </article>
                    <article class="ch-nasu-shot-value">
                        <div class="ch-nasu-shot-value__icon">C</div>
                        <h4><?php echo esc_html($point3_title); ?></h4>
                        <p><?php echo esc_html($point3_text); ?></p>
                    </article>
                    <article class="ch-nasu-shot-value">
                        <div class="ch-nasu-shot-value__icon">D</div>
                        <h4><?php echo esc_html($point4_title); ?></h4>
                        <p><?php echo esc_html($point4_text); ?></p>
                    </article>
                </div>
            </section>
        </div>
    </section>
</main>
