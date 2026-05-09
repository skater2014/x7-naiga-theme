<?php
/**
 * hub/pages/iezukuri/templates/shared/section-faq.php
 *
 * 役割:
 * - /iezukuri 配下で再利用するFAQ共通パーツ
 * - 呼び出し側で $faq_items を渡す
 *
 * 例:
 * $faq_items = array(
 *   array('q' => '質問', 'a' => '回答'),
 * );
 * include locate_template('hub/pages/iezukuri/templates/shared/section-faq.php');
 */

if (!defined('ABSPATH')) {
    exit;
}

if (empty($faq_items) || !is_array($faq_items)) {
    return;
}
?>

<section class="iez-common-faq" id="iez-common-faq">
    <h2 class="iez-common-faq__title">よくある質問</h2>

    <div class="iez-common-faq__list">
        <?php foreach ($faq_items as $index => $faq) : ?>
            <?php
            $q = isset($faq['q']) ? trim((string) $faq['q']) : '';
            $a = isset($faq['a']) ? trim((string) $faq['a']) : '';

            if ($q === '' || $a === '') {
                continue;
            }
            ?>

            <details class="iez-common-faq__item" <?php echo $index === 0 ? ' open' : ''; ?>>
                <summary><?php echo esc_html($q); ?></summary>
                <div class="iez-common-faq__answer">
                    <p><?php echo nl2br(esc_html($a)); ?></p>
                </div>
            </details>
        <?php endforeach; ?>
    </div>
</section>
