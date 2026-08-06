<?php
/**
 * /iezukuri/contact/
 *
 * ご相談・資料請求フォーム専用ページ。
 *
 * FAQは /iezukuri/faq/ に分離。
 * フォームは既存共通パーツをそのまま使用する。
 */

if (!defined('ABSPATH')) {
    exit;
}

$contact_form_part = locate_template(
    'hub/pages/iezukuri/templates/shared/section-contact-form.php'
);
?>

<section class="ch-subpage-section">
    <div class="ch-shell">

        <?php
        if ($contact_form_part) {
            include $contact_form_part;
        }
        ?>

    </div>
</section>
