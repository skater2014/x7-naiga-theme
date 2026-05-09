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
include get_template_directory() . '/hub/pages/iezukuri/templates/top/section-localnav.php';
include get_template_directory() . '/hub/pages/iezukuri/templates/top/section-feature.php';
include get_template_directory() . '/hub/pages/iezukuri/templates/top/section-works.php';
include get_template_directory() . '/hub/pages/iezukuri/templates/top/section-flow.php';
include get_template_directory() . '/hub/pages/iezukuri/templates/top/section-cta.php';
include get_template_directory() . '/hub/pages/iezukuri/templates/top/section-footer.php';
?>
</main>
<?php get_footer(); ?>
