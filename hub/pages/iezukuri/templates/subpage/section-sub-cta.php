<?php
/**
 * 家づくりサブページ共通CTA
 *
 * 母体:
 * hub/pages/iezukuri/templates/template-iezukuri-subpage.php
 *
 * chuko / nisetai / その他で文言を切り替える。
 */

if (!defined('ABSPATH')) {
    exit;
}

$page_id = get_queried_object_id();
$slug = $page_id ? (string) get_post_field('post_name', $page_id) : '';

$default = array(
    'kicker' => 'CONTACT',
    'title'  => '理想の住まいづくりを、最初の一歩から。',
    'text'   => '土地のこと、資金のこと、間取りのこと。注文住宅に関するご相談を丁寧にお伺いします。',
    'primary_label' => '家づくり相談をする',
    'primary_url'   => home_url('/iezukuri/contact/'),
    'secondary_label' => '注文住宅トップへ戻る',
    'secondary_url'   => home_url('/iezukuri/'),
);

$by_slug = array(
    'chuko' => array(
        'kicker' => 'REPAIR CONSULTATION',
        'title'  => '中古住宅の修理について相談する',
        'text'   => '雨漏り、屋根、外壁、水回り、床下、断熱、内装まで、修理の優先順位を整理して相談できます。',
        'primary_label' => '修理について相談する',
        'primary_url'   => home_url('/iezukuri/contact/'),
        'secondary_label' => '注文住宅トップへ戻る',
        'secondary_url'   => home_url('/iezukuri/'),
    ),
    'nisetai' => array(
        'kicker' => 'TWO FAMILY CONSULTATION',
        'title'  => '二世帯住宅の進め方を相談する',
        'text'   => '親世帯と子世帯の距離感、共有部分、資金計画、将来の使い方まで整理して相談できます。',
        'primary_label' => '二世帯住宅を相談する',
        'primary_url'   => home_url('/iezukuri/contact/'),
        'secondary_label' => '注文住宅トップへ戻る',
        'secondary_url'   => home_url('/iezukuri/'),
    ),
);

$data = isset($by_slug[$slug]) ? $by_slug[$slug] : $default;

$kicker = get_post_meta($page_id, '_iezukuri_sub_cta_kicker', true);
$title  = get_post_meta($page_id, '_iezukuri_sub_cta_title', true);
$text   = get_post_meta($page_id, '_iezukuri_sub_cta_text', true);

$primary_label = get_post_meta($page_id, '_iezukuri_sub_cta_primary_label', true);
$primary_url   = get_post_meta($page_id, '_iezukuri_sub_cta_primary_url', true);

$secondary_label = get_post_meta($page_id, '_iezukuri_sub_cta_secondary_label', true);
$secondary_url   = get_post_meta($page_id, '_iezukuri_sub_cta_secondary_url', true);

$kicker = $kicker ?: $data['kicker'];
$title  = $title ?: $data['title'];
$text   = $text ?: $data['text'];

$primary_label = $primary_label ?: $data['primary_label'];
$primary_url   = $primary_url ?: $data['primary_url'];

$secondary_label = $secondary_label ?: $data['secondary_label'];
$secondary_url   = $secondary_url ?: $data['secondary_url'];
?>

<section class="iez-sub-cta" id="iez-cta">
    <div class="iez-sub-cta__inner">
        <p class="iez-sub-cta__kicker"><?php echo esc_html($kicker); ?></p>

        <h2 class="iez-sub-cta__title">
            <?php echo esc_html($title); ?>
        </h2>

        <p class="iez-sub-cta__text">
            <?php echo esc_html($text); ?>
        </p>

        <div class="iez-sub-cta__actions">
            <a class="iez-sub-btn iez-sub-btn--primary" href="<?php echo esc_url($primary_url); ?>">
                <?php echo esc_html($primary_label); ?>
            </a>

            <a class="iez-sub-btn iez-sub-btn--ghost" href="<?php echo esc_url($secondary_url); ?>">
                <?php echo esc_html($secondary_label); ?>
            </a>
        </div>
    </div>
</section>
