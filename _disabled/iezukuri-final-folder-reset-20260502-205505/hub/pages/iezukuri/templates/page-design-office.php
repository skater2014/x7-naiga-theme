<?php
/**
 * =========================================================
 * design-office 型レイアウト
 *
 * 重要:
 * - このテンプレートは「レイアウト」だけを提供する
 * - 文書・画像・ボタン・リンクは、現在表示中のページIDのメタだけ読む
 * - /iezukuri/design-office のメタを他ページへ引き継がない
 * =========================================================
 */

if (!defined('ABSPATH')) {
    exit;
}

$post_id = get_queried_object_id();

$meta = function ($key, $default = '') use ($post_id) {
    $value = get_post_meta($post_id, $key, true);
    return ($value !== '' && $value !== array() && $value !== null) ? $value : $default;
};

$page_url = function ($page_key, $url_key, $default = '') use ($post_id) {
    $page_id = absint(get_post_meta($post_id, $page_key, true));
    if ($page_id) {
        $url = get_permalink($page_id);
        if ($url) {
            return $url;
        }
    }

    $manual = trim((string) get_post_meta($post_id, $url_key, true));
    return $manual !== '' ? $manual : $default;
};

$image_tag = function ($key, $alt = '', $size = 'large') use ($post_id) {
    $image_id = absint(get_post_meta($post_id, $key, true));
    if (!$image_id) {
        return '';
    }

    return wp_get_attachment_image($image_id, $size, false, array(
        'alt' => $alt,
        'loading' => 'lazy',
    ));
};

$content_html = apply_filters('the_content', get_post_field('post_content', $post_id));

$intro_eyebrow = $meta('_ch_design_office_intro_eyebrow', 'DESIGN & DETAIL');
$intro_title   = $meta('_ch_design_office_intro_title', get_the_title($post_id));
$intro_text    = $meta('_ch_design_office_intro_text', '');

$split_eyebrow = $meta('_ch_design_office_split_eyebrow', 'STYLE');
$split_title   = $meta('_ch_design_office_split_title', '');
$split_text    = $meta('_ch_design_office_split_text', '');
$split_btn     = $meta('_ch_design_office_split_btn_label', '相談する');
$split_url     = $page_url('_ch_design_office_split_btn_page_id', '_ch_design_office_split_btn_url', home_url('/iezukuri/contact/'));

$plan_title = $meta('_ch_design_office_plan_intro_title', '平屋プラン一覧');
$plan_text  = $meta('_ch_design_office_plan_intro_text', '暮らし方に合わせて選べる、3つの間取りプランをご用意しました。');

$show_plan_tabs = (string) get_post_meta($post_id, '_ch_show_plan_tabs', true) === '1';
?>

<div class="ch-page-stack ch-design-office-page">

    <section class="ch-page-intro">
        <?php if ($intro_eyebrow !== '') : ?>
            <span class="ch-eyebrow"><?php echo esc_html($intro_eyebrow); ?></span>
        <?php endif; ?>

        <?php if ($intro_title !== '') : ?>
            <h2><?php echo esc_html($intro_title); ?></h2>
        <?php endif; ?>

        <?php if ($intro_text !== '') : ?>
            <div class="ch-intro-prose">
                <p><?php echo nl2br(esc_html($intro_text)); ?></p>
            </div>
        <?php elseif (trim(wp_strip_all_tags($content_html)) !== '') : ?>
            <div class="ch-intro-prose">
                <?php echo $content_html; ?>
            </div>
        <?php endif; ?>
    </section>

    <?php if ($split_title !== '' || $split_text !== '' || absint(get_post_meta($post_id, '_ch_design_office_split_image_id', true))) : ?>
        <section class="ch-split">
            <div class="ch-split__body">
                <?php if ($split_eyebrow !== '') : ?>
                    <span class="ch-eyebrow"><?php echo esc_html($split_eyebrow); ?></span>
                <?php endif; ?>

                <?php if ($split_title !== '') : ?>
                    <h2><?php echo esc_html($split_title); ?></h2>
                <?php endif; ?>

                <?php if ($split_text !== '') : ?>
                    <p><?php echo nl2br(esc_html($split_text)); ?></p>
                <?php endif; ?>

                <?php if ($split_btn !== '' && $split_url !== '') : ?>
                    <a class="ch-btn ch-btn--primary" href="<?php echo esc_url($split_url); ?>">
                        <?php echo esc_html($split_btn); ?>
                    </a>
                <?php endif; ?>
            </div>

            <?php $split_image = $image_tag('_ch_design_office_split_image_id', $split_title); ?>
            <?php if ($split_image !== '') : ?>
                <figure class="ch-split__media">
                    <?php echo $split_image; ?>
                </figure>
            <?php endif; ?>
        </section>
    <?php endif; ?>

    <?php if ($show_plan_tabs) : ?>
        <?php
        /*
         * section-plan-tabs.php も現在の $post_id のメタを見る前提。
         * ここで design-office ページのメタを引き継がない。
         */
        $GLOBALS['naigai_current_customhome_subpage_id'] = $post_id;
        ?>
        <section class="ch-section-block ch-design-office-plan-section">
            <div class="ch-section-head ch-section-head--center">
                <?php if ($plan_title !== '') : ?>
                    <h2><?php echo esc_html($plan_title); ?></h2>
                <?php endif; ?>
                <?php if ($plan_text !== '') : ?>
                    <p><?php echo nl2br(esc_html($plan_text)); ?></p>
                <?php endif; ?>
            </div>
            <?php require get_template_directory() . '/hub/partials/section-plan-tabs.php'; ?>
        </section>
    <?php endif; ?>

</div>


