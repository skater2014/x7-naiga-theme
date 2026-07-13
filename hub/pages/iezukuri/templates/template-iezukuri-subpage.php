<?php
/**
 * 家づくり サブページ本体
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header('customhome');

$page_id = get_queried_object_id();

if (!$page_id) {
    $page_id = get_the_ID();
}

$slug = get_post_field('post_name', $page_id);

if (!$slug) {
    $slug = 'subpage';
}

/*
 * サブページ側のナビ表示メタ。
 *
 * 管理画面:
 * - ヘッダーナビデザイン
 * - ページメニュー表示
 * - 中間ナビ表示モード
 *
 * ここで post_meta を読み、<main> の class / data属性に出す。
 * CSSはその class / data属性を見て切り替える。
 */
$header_style  = get_post_meta($page_id, '_hub_ch_header_menu_style', true);
$page_style    = get_post_meta($page_id, '_hub_ch_page_menu_style', true);
$localnav_mode = get_post_meta($page_id, '_hub_ch_localnav_mode', true);

if ($header_style === '') {
    $header_style = 'default';
}
if ($page_style === '') {
    $page_style = 'default';
}
if ($localnav_mode === '') {
    $localnav_mode = 'hero_to_cta';
}

$header_style_class  = sanitize_html_class('iez-header-menu--' . $header_style);
$page_style_class    = sanitize_html_class('iez-page-menu--' . $page_style);
$localnav_mode_class = sanitize_html_class('iez-localnav-mode--' . $localnav_mode);

$base = get_template_directory() . '/hub/pages/iezukuri/templates';

$hero_part     = $base . '/subpage/section-sub-hero.php';
/*
 * サブページ中間ナビの読み込み。
 * section-sub-localnav.php は WordPress nav location ではなく、
 * ページ内アンカー href="#..." を出す中間導線。
 * footer の iezukuri_footer_menu とは別管理。
 */
$localnav_part = $base . '/subpage/section-sub-localnav.php';
$page_part     = $base . '/content/' . $slug . '.php';
$cta_part      = $base . '/subpage/section-sub-cta.php';
?>

<main
    id="primary"
    class="hub-customhome-subpage hub-customhome-subpage--<?php echo esc_attr($slug); ?> iezukuri-subpage iezukuri-subpage--<?php echo esc_attr($slug); ?> <?php echo esc_attr($header_style_class); ?> <?php echo esc_attr($page_style_class); ?> <?php echo esc_attr($localnav_mode_class); ?>"
    data-iezukuri-page="subpage"
    data-iezukuri-slug="<?php echo esc_attr($slug); ?>"
    data-header-menu-style="<?php echo esc_attr($header_style); ?>"
    data-page-menu-style="<?php echo esc_attr($page_style); ?>"
    data-localnav-mode="<?php echo esc_attr($localnav_mode); ?>"
>

    <?php
    if (file_exists($hero_part)) {
        include $hero_part;
    }

    if (file_exists($localnav_part)) {
        include $localnav_part;
    }

    if (file_exists($page_part)) {
        include $page_part;
    }

    if (file_exists($cta_part)) {
        include $cta_part;
    }
    ?>
</main>

<?php get_footer('iezukuri'); ?>
