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

$base = get_template_directory() . '/hub/pages/iezukuri/templates';

$hero_part     = $base . '/subpage/section-sub-hero.php';
$localnav_part = $base . '/subpage/section-sub-localnav.php';
$page_part     = $base . '/content/' . $slug . '.php';
$cta_part      = $base . '/subpage/section-sub-cta.php';
?>

<main
    id="primary"
    class="hub-customhome-subpage hub-customhome-subpage--<?php echo esc_attr($slug); ?> iezukuri-subpage iezukuri-subpage--<?php echo esc_attr($slug); ?>"
    data-iezukuri-page="subpage"
    data-iezukuri-slug="<?php echo esc_attr($slug); ?>"
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

<?php
get_footer();
