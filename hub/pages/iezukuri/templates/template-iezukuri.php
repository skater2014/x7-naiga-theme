<?php
/**
 * /iezukuri トップテンプレート
 *
 * 役割:
 * - ページ全体の骨組み
 * - templates/top/ だけ読む
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header('customhome');
?>
<main id="primary" class="hub-customhome-page hub-customhome-top">
<?php
include get_template_directory() . '/hub/pages/iezukuri/templates/top/context.php';
include get_template_directory() . '/hub/pages/iezukuri/templates/top/section-hero.php';
/*
 * トップ中間ナビの読み込み。
 * section-localnav.php は WordPress nav location ではなく、
 * テンプレート内HTML/PHPで表示する中間導線。
 * footer の iezukuri_footer_menu とは別管理。
 */
include get_template_directory() . '/hub/pages/iezukuri/templates/top/section-localnav.php';
include get_template_directory() . '/hub/pages/iezukuri/templates/top/section-feature.php';
include get_template_directory() . '/hub/pages/iezukuri/templates/top/section-works.php';
include get_template_directory() . '/hub/pages/iezukuri/templates/top/section-flow.php';
include get_template_directory() . '/hub/pages/iezukuri/templates/top/section-cta.php';
?>
</main>

<?php get_footer('iezukuri'); ?>
