<?php
/**
 * Template Name: 家づくり - 二世帯住宅
 * Template Post Type: page
 *
 * /iezukuri/nisetai/ 専用テンプレート。
 * 中古住宅とはテンプレートを分ける。
 */

if (!defined('ABSPATH')) {
    exit;
}

add_filter('body_class', function ($classes) {
    $classes[] = 'iezukuri-page';
    $classes[] = 'hub-customhome-page';
    $classes[] = 'iezukuri-service-page';
    $classes[] = 'iezukuri-nisetai-page';
    return array_values(array_unique($classes));
});

get_header('customhome');

$post_id  = get_queried_object_id();
$title    = get_the_title($post_id) ?: '二世帯住宅';
$lead     = get_post_meta($post_id, '_ch_nisetai_lead', true);
$image_id = (int) get_post_thumbnail_id($post_id);

if (!$lead) {
    $lead = '親世帯と子世帯の距離感、共有と分離、玄関、水回り、生活音、将来の使い方。二世帯住宅は間取りだけでなく、暮らし方の設計が大切です。';
}
?>

<main id="main" class="ch-service-page ch-service-page--nisetai">
    <section class="ch-service-hero">
        <div class="ch-service-shell">
            <div class="ch-service-hero__grid">
                <div class="ch-service-hero__body">
                    <p class="ch-service-kicker">Two-family House</p>
                    <h1 class="ch-service-title"><?php echo esc_html($title); ?></h1>
                    <p class="ch-service-lead"><?php echo esc_html($lead); ?></p>

                    <div class="ch-service-actions">
                        <a class="ch-btn ch-btn--primary" href="<?php echo esc_url(home_url('/contact/')); ?>">二世帯住宅について相談する</a>
                        <a class="ch-btn ch-btn--ghost" href="<?php echo esc_url(home_url('/iezukuri/chuko/')); ?>">中古住宅を見る</a>
                    </div>
                </div>

                <div class="ch-service-hero__media">
                    <?php if ($image_id) : ?>
                        <?php echo wp_get_attachment_image($image_id, 'large'); ?>
                    <?php else : ?>
                        <div class="ch-service-hero__placeholder">Nisetai</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <section class="ch-service-section is-white">
        <div class="ch-service-shell">
            <div class="ch-service-head">
                <p class="ch-service-kicker">Planning Points</p>
                <h2>二世帯住宅は、距離感の設計が重要</h2>
                <p>完全分離、部分共有、同居型。家族の関係性と生活時間を前提に、共有する場所と分ける場所を整理します。</p>
            </div>

            <div class="ch-service-grid">
                <article class="ch-service-card">
                    <span>01</span>
                    <h3>共有と分離</h3>
                    <p>玄関、キッチン、浴室、洗濯、収納など、共有する場所と分ける場所を決めます。</p>
                </article>

                <article class="ch-service-card">
                    <span>02</span>
                    <h3>生活音と動線</h3>
                    <p>寝室、水回り、階段、上下階の音など、生活リズムの違いを間取りに反映します。</p>
                </article>

                <article class="ch-service-card">
                    <span>03</span>
                    <h3>将来の使い方</h3>
                    <p>介護、子どもの独立、空き部屋、賃貸転用など、家族構成の変化まで考えます。</p>
                </article>
            </div>
        </div>
    </section>

    <?php if (trim(get_post_field('post_content', $post_id)) !== '') : ?>
        <section class="ch-service-section">
            <div class="ch-service-shell">
                <div class="ch-service-content">
                    <?php
                    while (have_posts()) :
                        the_post();
                        the_content();
                    endwhile;
                    ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <section class="ch-service-section">
        <div class="ch-service-shell">
            <div class="ch-service-cta">
                <p class="ch-service-kicker">Contact</p>
                <h2>二世帯住宅の間取り相談から対応します</h2>
                <p>完全分離にするか、部分共有にするか。最初の整理で暮らしやすさが大きく変わります。</p>
                <div class="ch-service-actions">
                    <a class="ch-btn ch-btn--primary" href="<?php echo esc_url(home_url('/contact/')); ?>">問い合わせる</a>
                    <a class="ch-btn ch-btn--ghost" href="<?php echo esc_url(home_url('/iezukuri/')); ?>">家づくりトップへ</a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php
get_footer();
