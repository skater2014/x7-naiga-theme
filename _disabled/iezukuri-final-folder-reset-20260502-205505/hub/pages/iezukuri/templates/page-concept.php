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

$intro_eyebrow = $meta('_ch_concept_intro_eyebrow', '');
$intro_title   = $meta('_ch_concept_intro_title', '');
$intro_text    = $meta('_ch_concept_intro_text', '');

$split_eyebrow = $meta('_ch_concept_split_eyebrow', '');
$split_title   = $meta('_ch_concept_split_title', '');
$split_text    = $meta('_ch_concept_split_text', '');
$split_btn     = $meta('_ch_concept_split_btn_label', '');
$split_url     = $url('_ch_concept_split_btn_page_id', '_ch_concept_split_btn_url', '');
?>

<div class="ch-concept-page">
    <div class="ch-page-band ch-page-band--white">
        <div class="ch-page-inner">
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

            <section class="ch-card-grid ch-card-grid--3">
                <?php for ($i = 1; $i <= 3; $i++) : ?>
                    <?php
                    $title = $meta("_ch_concept_card{$i}_title", '');
                    $text  = $meta("_ch_concept_card{$i}_text", '');
                    $img   = $image("_ch_concept_card{$i}_image_id", $title);
                    ?>
                    <?php if ($title !== '' || $text !== '' || $img !== '') : ?>
                        <article class="ch-surface-card">
                            <?php if ($img !== '') : ?><figure class="ch-surface-card__media"><?php echo $img; ?></figure><?php endif; ?>
                            <?php if ($title !== '') : ?><h3><?php echo esc_html($title); ?></h3><?php endif; ?>
                            <?php if ($text !== '') : ?><p><?php echo nl2br(esc_html($text)); ?></p><?php endif; ?>
                        </article>
                    <?php endif; ?>
                <?php endfor; ?>
            </section>
        </div>
    </div>

    <?php if ($split_title !== '' || $split_text !== '' || $image('_ch_concept_split_image_id', $split_title) !== '') : ?>
        <div class="ch-page-band ch-page-band--sand">
            <div class="ch-page-inner">
                <section class="ch-split">
                    <div class="ch-split__body">
                        <?php if ($split_eyebrow !== '') : ?><span class="ch-eyebrow"><?php echo esc_html($split_eyebrow); ?></span><?php endif; ?>
                        <?php if ($split_title !== '') : ?><h2><?php echo esc_html($split_title); ?></h2><?php endif; ?>
                        <?php if ($split_text !== '') : ?><p><?php echo nl2br(esc_html($split_text)); ?></p><?php endif; ?>
                        <?php if ($split_btn !== '' && $split_url !== '') : ?>
                            <a class="ch-btn ch-btn--primary" href="<?php echo esc_url($split_url); ?>"><?php echo esc_html($split_btn); ?></a>
                        <?php endif; ?>
                    </div>
                    <?php $split_img = $image('_ch_concept_split_image_id', $split_title); ?>
                    <?php if ($split_img !== '') : ?><figure class="ch-split__media"><?php echo $split_img; ?></figure><?php endif; ?>
                </section>
            </div>
        </div>
    <?php endif; ?>
</div>


