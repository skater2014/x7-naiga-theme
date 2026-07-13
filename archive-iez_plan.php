<?php
/**
 * =========================================================
 * archive-iez_plan.php
 *
 * 対象URL:
 * - /iezukuri/plans/
 *
 * 対象投稿タイプ:
 * - iez_plan
 *
 * 役割:
 * - 家づくり参考プランの一覧アーカイブを表示する
 *
 * 読み込む部品:
 * - hub/pages/iezukuri/templates/components/block-plan-list.php
 *
 * 注意:
 * - CSS/JS はここに直書きしない
 * - CSS/JS は hub/pages/iezukuri/inc/enqueue.php で管理する
 * - /iezukuri/plan/{slug}/ の詳細は single-iez_plan.php が担当
 * - PDFは single-iez_plan.php の ?plan_pdf=1 分岐が担当
 * =========================================================
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header('customhome');
?>

<!-- IEZUKURI_PLAN_ARCHIVE_TEMPLATE_ACTIVE -->

<main
    id="primary"
    class="hub-customhome-subpage iezukuri-subpage iezukuri-plans-page iezukuri-plan-archive"
    data-iezukuri-page="plans-archive"
>
    <?php
    $block = get_template_directory() . '/hub/pages/iezukuri/templates/components/block-plan-list.php';

    if (file_exists($block)) {
        include $block;
    } else {
        echo '<section class="iez-block"><div class="iez-block__inner"><p>参考プラン一覧ブロックが見つかりません。</p></div></section>';
    }
    ?>
</main>

<?php get_footer('iezukuri'); ?>
