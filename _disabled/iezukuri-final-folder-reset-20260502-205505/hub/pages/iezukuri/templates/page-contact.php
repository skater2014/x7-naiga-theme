<?php
/**
 * =========================================================
 * /iezukuri/contact
 * ご相談・資料請求
 *
 * - Flow を表示
 * - 既存 /contact フォームを引用
 * - FAQ / CTA は既存の共通仕組みに任せる
 * =========================================================
 */

if (!defined('ABSPATH')) {
    exit;
}

$post_id = get_queried_object_id();

$meta = function ($key, $default = '') use ($post_id) {
    $v = get_post_meta($post_id, $key, true);
    return ($v !== '' && $v !== array() && $v !== null) ? $v : $default;
};

$content_html = apply_filters('the_content', get_post_field('post_content', $post_id));

$eyebrow = $meta('_ch_contact_intro_eyebrow', 'CONTACT');
$title   = $meta('_ch_contact_intro_title', 'ご相談・資料請求');
$text    = $meta('_ch_contact_intro_text', '土地探し、資金計画、間取りの考え方、那須での暮らし方まで、お気軽にご相談ください。');
?>

<div class="ch-page-stack ch-contact-page">
    <section class="ch-page-intro">
        <?php if ($eyebrow !== '') : ?><span class="ch-eyebrow"><?php echo esc_html($eyebrow); ?></span><?php endif; ?>
        <?php if ($title !== '') : ?><h2><?php echo esc_html($title); ?></h2><?php endif; ?>

        <div class="ch-intro-prose">
            <?php if ($text !== '') : ?>
                <p><?php echo nl2br(esc_html($text)); ?></p>
            <?php else : ?>
                <?php echo $content_html; ?>
            <?php endif; ?>
        </div>
    </section>

    <?php
    if (function_exists('naigai_ch_render_contact_flow_section')) {
        naigai_ch_render_contact_flow_section($post_id);
    }

    if (function_exists('naigai_ch_render_contact_form_section')) {
        naigai_ch_render_contact_form_section($post_id);
    }
    ?>

    <?php
    $has_faq = false;
    for ($i = 1; $i <= 5; $i++) {
        if ($meta("_ch_contact_faq{$i}_q", '') !== '' || $meta("_ch_contact_faq{$i}_a", '') !== '') {
            $has_faq = true;
            break;
        }
    }
    ?>

    <?php if ($has_faq) : ?>
        <section class="ch-section-block">
            <div class="ch-section-head">
                <h2>よくあるご質問</h2>
            </div>

            <div class="ch-card-grid ch-card-grid--2">
                <?php for ($i = 1; $i <= 5; $i++) : ?>
                    <?php
                    $q = $meta("_ch_contact_faq{$i}_q", '');
                    $a = $meta("_ch_contact_faq{$i}_a", '');
                    ?>
                    <?php if ($q !== '' || $a !== '') : ?>
                        <article class="ch-surface-card">
                            <?php if ($q !== '') : ?><h3><?php echo esc_html($q); ?></h3><?php endif; ?>
                            <?php if ($a !== '') : ?><p><?php echo nl2br(esc_html($a)); ?></p><?php endif; ?>
                        </article>
                    <?php endif; ?>
                <?php endfor; ?>
            </div>
        </section>
    <?php endif; ?>
</div>


