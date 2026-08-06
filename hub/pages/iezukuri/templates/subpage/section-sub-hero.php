<?php
/**
 * 家づくりサブページ共通hero
 *
 * 母体:
 * hub/pages/iezukuri/templates/template-iezukuri-subpage.php
 *
 * slug ごとに hero の初期表示を切り替える。
 */

if (!defined('ABSPATH')) {
    exit;
}

/* IEZ_COMMON_HERO_SUBPAGE_SWITCH_START */
/**
 * 固定ページHero共通化。
 *
 * 役割:
 * - 既存の固定ページHeroメタを共通Hero rendererで出す。
 * - 共通Hero関数がなければ、下の既存Heroをそのまま使う。
 *
 * 対象:
 * - /iezukuri/contact/ など固定ページ
 *
 * 対象外:
 * - /iezukuri/ トップ
 * - /iezukuri/plans/ アーカイブ
 * - single-iez_plan
 */
$page_id_for_common_hero = get_queried_object_id();

if (
    $page_id_for_common_hero
    && function_exists('naigai_iez_get_page_hero_data')
    && function_exists('naigai_iez_render_hero')
) {
    $common_hero_data = naigai_iez_get_page_hero_data($page_id_for_common_hero);

    /*
     * 管理画面の _ch_hero_* を唯一の表示元にする。
     *
     * 画像が0枚でも旧Heroへフォールバックしない。
     * これにより管理画面でCTA未入力の場合、
     * 固定文言のCTAが勝手に表示されることもない。
     */
    naigai_iez_render_hero($common_hero_data);
    return;
}
/* IEZ_COMMON_HERO_SUBPAGE_SWITCH_END */

$page_id = get_queried_object_id();
$slug    = $page_id ? (string) get_post_field('post_name', $page_id) : '';
$title   = $page_id ? get_the_title($page_id) : '';

$defaults = array(
    'chuko' => array(
        'kicker' => 'HOME REPAIR',
        'title'  => '住み慣れた住まいを、安心して使い続けるために。',
        'lead'   => '雨漏り、屋根、外壁、水回り、床下、断熱、内装まで。築年数が経過したお住まいの傷みを確認し、必要な修理を優先順位で整理します。',
        'primary_label'   => '修理について相談する',
        'primary_url'     => home_url('/iezukuri/contact/'),
        'secondary_label' => '修理箇所を見る',
        'secondary_url'   => home_url('/iezukuri/chuko#chuko-repair-check'),
    ),
    'nisetai' => array(
        'kicker' => 'TWO FAMILY HOUSE',
        'title'  => '二世帯住宅の進め方を考える。',
        'lead'   => '親世帯と子世帯の距離感、共有部分、資金計画、将来の使い方まで整理します。',
        'primary_label'   => '二世帯住宅を相談する',
        'primary_url'     => home_url('/iezukuri/contact/'),
        'secondary_label' => '注文住宅トップへ戻る',
        'secondary_url'   => home_url('/iezukuri/'),
    ),
    'default' => array(
        'kicker' => '',
        'title'  => $title ?: '家づくり',
        'lead'   => '',
        'primary_label'   => '家づくり相談をする',
        'primary_url'     => home_url('/iezukuri/contact/'),
        'secondary_label' => '注文住宅トップへ戻る',
        'secondary_url'   => home_url('/iezukuri/'),
    ),
);

$data = isset($defaults[$slug]) ? $defaults[$slug] : $defaults['default'];

/**
 * chuko は古いメタが残っても反映されるように、slug初期値を優先する。
 * その他ページはメタがあればメタを優先する。
 */
$force_slug_default = in_array($slug, array('chuko'), true);

$meta_or_default = function ($keys, $default) use ($page_id, $force_slug_default) {
    if ($force_slug_default || !$page_id) {
        return $default;
    }

    foreach ((array) $keys as $key) {
        $value = get_post_meta($page_id, $key, true);
        if ($value !== '') {
            return $value;
        }
    }

    return $default;
};

$kicker = $meta_or_default(array('_iezukuri_sub_hero_kicker', '_ch_sub_hero_kicker', '_iezukuri_hero_kicker'), $data['kicker']);
$hero_title = $meta_or_default(array('_iezukuri_sub_hero_title', '_ch_sub_hero_title', '_iezukuri_hero_title'), $data['title']);
$lead = $meta_or_default(array('_iezukuri_sub_hero_lead', '_ch_sub_hero_lead', '_iezukuri_hero_lead'), $data['lead']);

$primary_label = $meta_or_default(array('_iezukuri_sub_hero_primary_label', '_ch_sub_hero_primary_label'), $data['primary_label']);
$primary_url   = $meta_or_default(array('_iezukuri_sub_hero_primary_url', '_ch_sub_hero_primary_url'), $data['primary_url']);

$secondary_label = $meta_or_default(array('_iezukuri_sub_hero_secondary_label', '_ch_sub_hero_secondary_label'), $data['secondary_label']);
$secondary_url   = $meta_or_default(array('_iezukuri_sub_hero_secondary_url', '_ch_sub_hero_secondary_url'), $data['secondary_url']);

$image_id = 0;

foreach (array('_iezukuri_sub_hero_image_id', '_ch_sub_hero_image_id', '_iezukuri_hero_image_id') as $image_key) {
    $candidate = $page_id ? (int) get_post_meta($page_id, $image_key, true) : 0;
    if ($candidate) {
        $image_id = $candidate;
        break;
    }
}

if (!$image_id && $page_id) {
    $image_id = (int) get_post_thumbnail_id($page_id);
}

$image_url = $image_id ? wp_get_attachment_image_url($image_id, 'full') : '';
$image_alt = $image_id ? get_post_meta($image_id, '_wp_attachment_image_alt', true) : '';
if (!$image_alt) {
    $image_alt = $hero_title;
}

$classes = 'iez-sub-hero';
if ($image_url) {
    $classes .= ' is-has-media';
} else {
    $classes .= ' is-no-media';
}
?>

<section class="<?php echo esc_attr($classes); ?>">
    <?php if ($image_url) : ?>
        <div class="iez-sub-hero__media">
            <img class="iez-sub-hero__image" src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($image_alt); ?>">
        </div>
    <?php endif; ?>

    <div class="iez-sub-hero__overlay" aria-hidden="true"></div>

    <div class="iez-sub-hero__inner">
        <div class="iez-sub-hero__content">
            <p class="iez-sub-hero__kicker">
                <?php echo esc_html($kicker); ?>
            </p>

            <h1 class="iez-sub-hero__title">
                <?php echo esc_html($hero_title); ?>
            </h1>

            <?php if ($lead !== '') : ?>
                <p class="iez-sub-hero__lead">
                    <?php echo esc_html($lead); ?>
                </p>
            <?php endif; ?>

            <div class="iez-sub-hero__actions">
                <a class="iez-sub-btn iez-sub-btn--primary" href="<?php echo esc_url($primary_url); ?>">
                    <?php echo esc_html($primary_label); ?>
                </a>

                <a class="iez-sub-btn iez-sub-btn--ghost" href="<?php echo esc_url($secondary_url); ?>">
                    <?php echo esc_html($secondary_label); ?>
                </a>
            </div>
        </div>
    </div>
</section>
