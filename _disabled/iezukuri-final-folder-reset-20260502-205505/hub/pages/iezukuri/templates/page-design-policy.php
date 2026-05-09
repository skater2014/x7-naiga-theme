<?php
if (!defined('ABSPATH')) {
    exit;
}

$post_id = get_queried_object_id();

$meta = function ($key, $default = '') use ($post_id) {
    $v = get_post_meta($post_id, $key, true);
    return ($v !== '' && $v !== array() && $v !== null) ? $v : $default;
};

$url = function ($page_key, $url_key, $default = '') use ($post_id) {
    $page_id = absint(get_post_meta($post_id, $page_key, true));
    if ($page_id) {
        $p = get_permalink($page_id);
        if ($p) return $p;
    }
    $manual = trim((string) get_post_meta($post_id, $url_key, true));
    return $manual !== '' ? $manual : $default;
};

$image = function ($key, $alt = '') use ($post_id) {
    $id = absint(get_post_meta($post_id, $key, true));
    return $id ? wp_get_attachment_image($id, 'large', false, array('alt' => $alt, 'loading' => 'lazy')) : '';
};

$content_html = apply_filters('the_content', get_post_field('post_content', $post_id));

$intro_eyebrow = $meta('_ch_design_policy_intro_eyebrow', '');
$intro_title   = $meta('_ch_design_policy_intro_title', '');
$intro_text    = $meta('_ch_design_policy_intro_text', '');

$steps_title = $meta('_ch_design_policy_steps_title', '');
$steps_lead  = $meta('_ch_design_policy_steps_lead', '');

$detail_eyebrow = $meta('_ch_design_policy_detail_eyebrow', '');
$detail_title   = $meta('_ch_design_policy_detail_title', '');
$detail_text    = $meta('_ch_design_policy_detail_text', '');
$detail_btn     = $meta('_ch_design_policy_detail_btn_label', '');
$detail_url     = $url('_ch_design_policy_detail_btn_page_id', '_ch_design_policy_detail_btn_url', '');
?>

<div class="ch-page-stack ch-design-policy-page">
    <section class="ch-page-intro">
        <?php if ($intro_eyebrow !== '') : ?><span class="ch-eyebrow"><?php echo esc_html($intro_eyebrow); ?></span><?php endif; ?>
        <?php if ($intro_title !== '') : ?><h2><?php echo esc_html($intro_title); ?></h2><?php endif; ?>
        <div class="ch-intro-prose">
            <?php if ($intro_text !== '') : ?>
                <p><?php echo nl2br(esc_html($intro_text)); ?></p>
            <?php else : ?>
                <?php echo $content_html; ?>
            <?php endif; ?>
        </div>
    </section>

    <?php
    $has_steps = false;
    for ($i = 1; $i <= 5; $i++) {
        if ($meta("_ch_design_policy_step{$i}_title", '') !== '' || $meta("_ch_design_policy_step{$i}_text", '') !== '') {
            $has_steps = true;
            break;
        }
    }
    ?>

    <?php if ($has_steps || $steps_title !== '' || $steps_lead !== '') : ?>
        <section>
            <div class="ch-section-head">
                <?php if ($steps_title !== '') : ?><h2><?php echo esc_html($steps_title); ?></h2><?php endif; ?>
                <?php if ($steps_lead !== '') : ?><p><?php echo nl2br(esc_html($steps_lead)); ?></p><?php endif; ?>
            </div>

            <div class="ch-card-grid ch-card-grid--5">
                <?php for ($i = 1; $i <= 5; $i++) : ?>
                    <?php
                    $title = $meta("_ch_design_policy_step{$i}_title", '');
                    $text  = $meta("_ch_design_policy_step{$i}_text", '');
                    ?>
                    <?php if ($title !== '' || $text !== '') : ?>
                        <article class="ch-surface-card ch-step-card">
                            <span class="ch-step-card__num"><?php echo esc_html(str_pad((string) $i, 2, '0', STR_PAD_LEFT)); ?></span>
                            <?php if ($title !== '') : ?><h3><?php echo esc_html($title); ?></h3><?php endif; ?>
                            <?php if ($text !== '') : ?><p><?php echo nl2br(esc_html($text)); ?></p><?php endif; ?>
                        </article>
                    <?php endif; ?>
                <?php endfor; ?>
            </div>
        </section>
    <?php endif; ?>

    <?php if ($detail_title !== '' || $detail_text !== '' || $image('_ch_design_policy_detail_image_id', $detail_title) !== '') : ?>
        <section class="ch-split">
            <div class="ch-split__body">
                <?php if ($detail_eyebrow !== '') : ?><span class="ch-eyebrow"><?php echo esc_html($detail_eyebrow); ?></span><?php endif; ?>
                <?php if ($detail_title !== '') : ?><h2><?php echo esc_html($detail_title); ?></h2><?php endif; ?>
                <?php if ($detail_text !== '') : ?><p><?php echo nl2br(esc_html($detail_text)); ?></p><?php endif; ?>
                <?php if ($detail_btn !== '' && $detail_url !== '') : ?>
                    <a class="ch-btn ch-btn--primary" href="<?php echo esc_url($detail_url); ?>"><?php echo esc_html($detail_btn); ?></a>
                <?php endif; ?>
            </div>
            <?php $detail_img = $image('_ch_design_policy_detail_image_id', $detail_title); ?>
            <?php if ($detail_img !== '') : ?><figure class="ch-split__media"><?php echo $detail_img; ?></figure><?php endif; ?>
        </section>
    <?php endif; ?>
</div>


