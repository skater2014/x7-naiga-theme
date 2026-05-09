<?php
/**
 * hub/pages/iezukuri/templates/shared/section-contact-form.php
 *
 * 役割:
 * - /iezukuri 配下で再利用するコンタクトフォーム共通パーツ
 * - 既存フォーム本体を呼ぶだけ
 * - header/footer は出さない
 *
 * 既存フォーム本体:
 * template-parts/contact/customer-info-form-inner.php
 */

if (!defined('ABSPATH')) {
    exit;
}

$form_inner = locate_template('template-parts/contact/customer-info-form-inner.php');
?>

<section class="iez-common-contact-form" id="iez-common-contact-form">
    <?php
    if ($form_inner) {
        include $form_inner;
    } else {
        echo '<p>フォームテンプレートが見つかりません。</p>';
    }
    ?>
</section>
