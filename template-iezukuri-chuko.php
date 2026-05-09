<?php
/**
 * Template Name: 家づくり - 中古住宅
 * Template Post Type: page
 *
 * /iezukuri/chuko/ 専用テンプレート。
 * 二世帯住宅とはテンプレートを分ける。
 */

if (!defined('ABSPATH')) {
    exit;
}

add_filter('body_class', function ($classes) {
    $classes[] = 'iezukuri-page';
    $classes[] = 'hub-customhome-page';
    $classes[] = 'iezukuri-service-page';
    $classes[] = 'iezukuri-chuko-page';
    return array_values(array_unique($classes));
});

get_header('customhome');

$post_id  = get_queried_object_id();
$title    = get_the_title($post_id) ?: '中古住宅';
$lead     = get_post_meta($post_id, '_ch_chuko_lead', true);
$image_id = (int) get_post_thumbnail_id($post_id);

if (!$lead) {
    $lead = '中古住宅の購入前確認、建物状態、修繕範囲、リノベーション費用まで。新築とは違う判断軸で、無理のない住まいづくりを整理します。';
}
?>

<main id="main" class="ch-service-page ch-service-page--chuko">
    <section class="ch-service-hero">
        <div class="ch-service-shell">
            <div class="ch-service-hero__grid">
                <div class="ch-service-hero__body">
                    <p class="ch-service-kicker">Used House / Renovation</p>
                    <h1 class="ch-service-title"><?php echo esc_html($title); ?></h1>
                    <p class="ch-service-lead"><?php echo esc_html($lead); ?></p>

                    <div class="ch-service-actions">
                        <a class="ch-btn ch-btn--primary" href="<?php echo esc_url(home_url('/contact/')); ?>">中古住宅について相談する</a>
                        <a class="ch-btn ch-btn--ghost" href="<?php echo esc_url(home_url('/iezukuri/nisetai/')); ?>">二世帯住宅を見る</a>
                    </div>
                </div>

                <div class="ch-service-hero__media">
                    <?php if ($image_id) : ?>
                        <?php echo wp_get_attachment_image($image_id, 'large'); ?>
                    <?php else : ?>
                        <div class="ch-service-hero__placeholder">Chuko</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <section class="ch-service-section is-white">
        <div class="ch-service-shell">
            <div class="ch-service-head">
                <p class="ch-service-kicker">Check Points</p>
                <h2>中古住宅は、価格だけで判断しない</h2>
                <p>建物の状態、修繕履歴、断熱、耐震、給排水、購入後の改修費まで含めて、総額で考える必要があります。</p>
            </div>

            <div class="ch-service-grid">
                <article class="ch-service-card">
                    <span>01</span>
                    <h3>建物状態の確認</h3>
                    <p>雨漏り、基礎、屋根、外壁、設備など、購入前に確認すべき部分を整理します。</p>
                </article>

                <article class="ch-service-card">
                    <span>02</span>
                    <h3>改修範囲の整理</h3>
                    <p>全部直すのではなく、暮らしに必要な部分から優先順位を決めます。</p>
                </article>

                <article class="ch-service-card">
                    <span>03</span>
                    <h3>予算配分</h3>
                    <p>物件価格とリノベーション費用を分けず、住み始めるまでの総額で計画します。</p>
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
                <h2>中古住宅の購入前から相談できます</h2>
                <p>候補物件がある段階でも、まだ探し始めでも大丈夫です。購入と改修をまとめて整理します。</p>
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
