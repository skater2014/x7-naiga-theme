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

$eyebrow = $meta('_ch_company_intro_eyebrow', '');
$title   = $meta('_ch_company_intro_title', '');
$text    = $meta('_ch_company_intro_text', '');

$btn_label = $meta('_ch_company_btn_label', '');
$btn_url   = $url('_ch_company_btn_page_id', '_ch_company_btn_url', '');
?>

<div class="ch-page-stack ch-company-page">
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

    <?php for ($i = 1; $i <= 4; $i++) : ?>
        <?php
        $section_title = $meta("_ch_company_section{$i}_title", '');
        $section_text  = $meta("_ch_company_section{$i}_text", '');
        $section_img   = $image("_ch_company_section{$i}_image_id", $section_title);
        ?>
        <?php if ($section_title !== '' || $section_text !== '' || $section_img !== '') : ?>
            <section class="ch-split">
                <div class="ch-split__body">
                    <?php if ($section_title !== '') : ?><h2><?php echo esc_html($section_title); ?></h2><?php endif; ?>
                    <?php if ($section_text !== '') : ?><p><?php echo nl2br(esc_html($section_text)); ?></p><?php endif; ?>
                    <?php if ($i === 1 && $btn_label !== '' && $btn_url !== '') : ?>
                        <a class="ch-btn ch-btn--primary" href="<?php echo esc_url($btn_url); ?>"><?php echo esc_html($btn_label); ?></a>
                    <?php endif; ?>
                </div>
                <?php if ($section_img !== '') : ?><figure class="ch-split__media"><?php echo $section_img; ?></figure><?php endif; ?>
            </section>
        <?php endif; ?>
    <?php endfor; ?>
</div>

<?php
/* =========================================================
 * CUSTOMHOME COMPANY MAP FINAL
 * /company のGoogle Map情報を /iezukuri/company でも表示
 * ========================================================= */

$company_post_id = isset($post_id) ? (int) $post_id : get_queried_object_id();

$company_map_title  = get_post_meta($company_post_id, '_ch_company_map_title', true);
$company_map_text   = get_post_meta($company_post_id, '_ch_company_map_text', true);
$company_address    = get_post_meta($company_post_id, '_ch_company_address', true);
$company_map_iframe = get_post_meta($company_post_id, '_ch_company_map_iframe', true);

if ($company_map_iframe !== '') :
    $allowed_iframe = array(
        'iframe' => array(
            'src'             => true,
            'width'           => true,
            'height'          => true,
            'style'           => true,
            'allowfullscreen' => true,
            'loading'         => true,
            'referrerpolicy'  => true,
            'title'           => true,
        ),
    );
?>
    <section class="ch-section-block ch-company-map-section">
        <div class="ch-section-head">
            <span class="ch-eyebrow">ACCESS</span>
            <h2><?php echo esc_html($company_map_title !== '' ? $company_map_title : 'アクセス'); ?></h2>

            <?php if ($company_map_text !== '') : ?>
                <p><?php echo nl2br(esc_html($company_map_text)); ?></p>
            <?php endif; ?>

            <?php if ($company_address !== '') : ?>
                <p class="ch-company-map-address"><?php echo nl2br(esc_html($company_address)); ?></p>
            <?php endif; ?>
        </div>

        <div class="ch-company-map-embed">
            <?php echo wp_kses($company_map_iframe, $allowed_iframe); ?>
        </div>
    </section>
<?php endif; ?>


