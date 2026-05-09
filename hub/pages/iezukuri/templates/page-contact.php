<?php
/**
 * hub/pages/iezukuri/templates/page-contact.php
 *
 * 役割:
 * - /iezukuri/contact 専用テンプレート
 * - Gutenberg / クラシックエディタ本文は使わない
 * - FAQ と contact form は common パーツから読み込む
 * - 表示順: FAQ → フォーム
 */

if (!defined('ABSPATH')) {
    exit;
}

$faq_part          = locate_template('hub/pages/iezukuri/templates/shared/section-faq.php');
$contact_form_part = locate_template('hub/pages/iezukuri/templates/shared/section-contact-form.php');

$faq_items = array(
    array(
        'q' => '新築住宅や注文住宅の相談はできますか？',
        'a' => 'はい。新築住宅、注文住宅、別荘、二拠点住宅など、住まいづくりに関するご相談を承ります。',
    ),
    array(
        'q' => 'まだ具体的な内容が決まっていなくても相談できますか？',
        'a' => 'はい。ご希望の暮らし方、建物のイメージ、ご予算感などを伺いながら、一緒に整理できます。',
    ),
    array(
        'q' => '建て替えやリフォームの相談もできますか？',
        'a' => '内容により対応可能です。建物の状況やご希望内容を確認しながら、進め方をご案内します。',
    ),
    array(
        'q' => '相談前に準備するものはありますか？',
        'a' => '希望する建物イメージ、現在のお悩み、ご予算感などがあればスムーズです。未準備でもご相談いただけます。',
    ),
);
?>

<section class="ch-section ch-section--white ch-subpage-section iez-contact-template" id="contact-content">
    <div class="ch-shell">

        <?php
        /*
         * 1. FAQ
         * フォームより上に表示する。
         */
        if ($faq_part) {
            include $faq_part;
        }
        ?>

        <?php
        /*
         * 2. Contact Form
         * FAQの下に表示する。
         */
        if ($contact_form_part) {
            include $contact_form_part;
        } else {
            echo '<p>コンタクトフォームパーツが見つかりません。</p>';
        }
        ?>

    </div>
</section>
