<?php
/**
 * /iezukuri/faq/
 *
 * 家づくりFAQ専用ページ。
 *
 * 新しいFAQ部品は作らない。
 * 既存:
 * hub/pages/iezukuri/templates/shared/section-faq.php
 * をそのまま使用する。
 *
 * 横幅も新しく指定せず、
 * 家づくりサブページ共通の .ch-shell を使用する。
 */

if (!defined('ABSPATH')) {
    exit;
}

/*
 * FAQは、これまでcontactページで管理していた
 * 既存メタデータをそのまま読む。
 */
$contact_page = get_page_by_path('iezukuri/contact');

$faq_items = array();

if ($contact_page) {
    for ($i = 1; $i <= 3; $i++) {
        $q = trim((string) get_post_meta(
            $contact_page->ID,
            "_ch_contact_faq{$i}_q",
            true
        ));

        $a = trim((string) get_post_meta(
            $contact_page->ID,
            "_ch_contact_faq{$i}_a",
            true
        ));

        if ($q !== '' && $a !== '') {
            $faq_items[] = array(
                'q' => $q,
                'a' => $a,
            );
        }
    }
}

$faq_part = locate_template(
    'hub/pages/iezukuri/templates/shared/section-faq.php'
);
?>

<section class="ch-subpage-section iez-contact-template">
    <div class="ch-shell">

        <?php
        if ($faq_part) {
            include $faq_part;
        }
        ?>

    </div>
</section>
