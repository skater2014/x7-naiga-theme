<?php
/**
 * /iezukuri/nisetai
 * 二世帯住宅ページ本文
 *
 * 母体:
 * hub/pages/iezukuri/templates/template-iezukuri-subpage.php
 *
 * ここに書くもの:
 * - 二世帯住宅の本文HTMLだけ
 *
 * ここに書かないもの:
 * - get_header()
 * - get_footer()
 * - main
 *
 * 方針:
 * - page-content-renderer.php の仮メタ本文はここでは呼ばない。
 * - 幅管理は .ch-page-content で行う。
 * - ページ名ごとの幅classは増やさない。
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="ch-service-page ch-service-page--nisetai" data-iezukuri-service="nisetai">
<section class="ch-service-page__hero">
        <div class="ch-shell">
            <div class="ch-service-page__hero-grid">
                <div class="ch-service-page__content">
                    <p class="ch-service-page__kicker">Two-family House</p>
                    <h1 class="ch-service-page__title"><?php echo esc_html($title ?: '二世帯住宅'); ?></h1>
                    <p class="ch-service-page__lead"><?php echo esc_html($lead); ?></p>
                    <div class="ch-service-page__actions">
                        <a class="ch-btn ch-btn--primary" href="<?php echo esc_url(home_url('/contact/')); ?>">相談する</a>
                        <a class="ch-btn ch-btn--ghost" href="<?php echo esc_url(home_url('/iezukuri/')); ?>">家づくりトップへ</a>
                    </div>
                </div>

                <div class="ch-service-page__media">
                    <?php if ($image_id) : ?>
                        <?php echo wp_get_attachment_image($image_id, 'large'); ?>
                    <?php else : ?>
                        <div class="ch-service-page__media-placeholder">Two Family</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <section class="ch-service-page__section is-white">
        <div class="ch-shell">
            <div class="ch-service-page__head">
                <p class="ch-service-page__kicker">Planning Points</p>
                <h2>二世帯住宅は「距離感」の設計が重要</h2>
                <p>完全分離、部分共有、同居型。家族関係や生活時間の違いを前提に、将来も使いやすい構成にします。</p>
            </div>

            <div class="ch-service-page__grid">
                <article class="ch-service-page__card">
                    <span class="ch-service-page__card-num">01</span>
                    <h3>共有と分離を決める</h3>
                    <p>玄関、キッチン、浴室、洗濯、収納。共有する場所と分ける場所を先に整理します。</p>
                </article>
                <article class="ch-service-page__card">
                    <span class="ch-service-page__card-num">02</span>
                    <h3>音と生活時間に配慮</h3>
                    <p>上下階の音、寝室の位置、水回りの位置など、生活リズムの違いを間取りに反映します。</p>
                </article>
                <article class="ch-service-page__card">
                    <span class="ch-service-page__card-num">03</span>
                    <h3>将来の使い方</h3>
                    <p>子どもの独立、介護、賃貸転用など、家族構成が変わった後の使い方まで考えます。</p>
                </article>
            </div>
        </div>
    </section>

    <section class="ch-service-page__section">
        <div class="ch-shell">
            <div class="ch-service-page__cta">
                <p class="ch-service-page__kicker">Contact</p>
                <h2>二世帯住宅の間取り相談から対応します</h2>
                <p>完全分離にするか、部分共有にするか。最初の整理で住みやすさが大きく変わります。</p>
                <div class="ch-service-page__actions">
                    <a class="ch-btn ch-btn--primary" href="<?php echo esc_url(home_url('/contact/')); ?>">問い合わせる</a>
                    <a class="ch-btn ch-btn--ghost" href="<?php echo esc_url(home_url('/iezukuri/used-house/')); ?>">中古住宅を見る</a>
                </div>
            </div>
        </div>
    </section>
</div>
