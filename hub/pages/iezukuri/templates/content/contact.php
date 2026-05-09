<?php
/**
 * hub/pages/iezukuri/templates/content/contact.php
 *
 * 役割:
 * - /iezukuri/contact 専用の本文エリア
 * - <main> は出さない
 * - Gutenberg / クラシックエディタ本文は出さない
 * - フォーム本体は既存の customer-info-form-inner.php を使う
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<section class="iez-sub-section iez-sub-section--contact" id="contact-content">
    <div class="iez-sub-section__inner">
        <div class="iez-sub-section__body">

            <h2 class="iez-sub-section__title">ご相談・資料請求</h2>

            <div class="iez-contact-form-area">
                <?php
                $form_inner = locate_template('template-parts/contact/customer-info-form-inner.php');

                if ($form_inner) {
                    include $form_inner;
                } else {
                    echo '<p>フォームテンプレートが見つかりません。</p>';
                }
                ?>
            </div>

            <section class="iez-contact-faq" id="iez-contact-faq">
                <h2><?php echo esc_html(get_post_meta(get_the_ID(), '_ch_text_contact_001', true) ?: 'よくあるご質問'); ?></h2>

                <?php
                /**
                 * FAQはページ編集画面のメタから読む。
                 *
                 * 対象メタ:
                 * - _ch_contact_faq1_q / _ch_contact_faq1_a
                 * - _ch_contact_faq2_q / _ch_contact_faq2_a
                 * - _ch_contact_faq3_q / _ch_contact_faq3_a
                 *
                 * これによりテンプレート直書きではなく、編集画面の入力を反映できる。
                 */
                $faq_items = array();

                for ($i = 1; $i <= 6; $i++) {
                    $q = trim((string) get_post_meta(get_the_ID(), "_ch_contact_faq{$i}_q", true));
                    $a = trim((string) get_post_meta(get_the_ID(), "_ch_contact_faq{$i}_a", true));

                    if ($q !== '' && $a !== '') {
                        $faq_items[] = array(
                            'q' => $q,
                            'a' => $a,
                        );
                    }
                }

                if (empty($faq_items)) {
                    $faq_items = array(
                        array(
                            'q' => 'まだ土地が決まっていなくても相談できますか？',
                            'a' => 'はい。土地探しの段階から、建物計画や資金計画を含めてご相談いただけます。',
                        ),
                        array(
                            'q' => '那須での定住と二拠点利用の相談はできますか？',
                            'a' => '可能です。利用頻度や暮らし方に合わせて、住まい方を一緒に整理します。',
                        ),
                        array(
                            'q' => '資料請求だけでも大丈夫ですか？',
                            'a' => '大丈夫です。検討段階に合わせて必要な資料をご案内します。',
                        ),
                    );
                }
                ?>

                <?php foreach ($faq_items as $index => $faq) : ?>
                    <details <?php echo $index === 0 ? 'open' : ''; ?>>
                        <summary><?php echo esc_html($faq['q']); ?></summary>
                        <p><?php echo esc_html($faq['a']); ?></p>
                    </details>
                <?php endforeach; ?>
            </section>

        </div>
    </div>
</section>
