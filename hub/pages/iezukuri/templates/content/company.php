<?php
/**
 * hub/pages/iezukuri/templates/content/company.php
 *
 * 役割:
 * - 会社概要 ページ専用の本文エリア
 * - 存在しない common/section-content は呼ばない
 * - このファイルでは <main>

を出さない
 */

if (!defined('ABSPATH')) {
    exit;
}

$page_id = get_queried_object_id();

if (!$page_id) {
    $page_id = get_the_ID();
}

$content = get_post_field('post_content', $page_id);
?>

<section class="iez-sub-section iez-sub-section--company" id="company-content">
    <div class="iez-sub-section__inner">
        <div class="iez-sub-section__body">
            <?php
$naigai_hide_company_auto_section_title = (
  function_exists('is_page')
  && is_page()
  && trim(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH), '/') === 'iezukuri/company'
);

if (!$naigai_hide_company_auto_section_title) :
?>
<h2 class="iez-sub-section__title">会社概要</h2>
<?php endif; ?>

            <?php if (!empty($content)) : ?>
                <div class="iez-sub-section__text">
                    <?php echo apply_filters('the_content', $content); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
