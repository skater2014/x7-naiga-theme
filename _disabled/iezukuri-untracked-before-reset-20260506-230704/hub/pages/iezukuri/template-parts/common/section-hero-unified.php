<?php
/**
 * 共通Hero入口
 *
 * 同じHero処理を使う。
 * ただし画像・H1・P・CTAはページごとの post_id の _ch_hero_* を読む。
 */

if (!defined('ABSPATH')) {
    exit;
}

$post_id = isset($args['post_id']) ? absint($args['post_id']) : 0;

if (!$post_id) {
    $post_id = get_queried_object_id();
}

if (!$post_id && isset($post) && $post instanceof WP_Post) {
    $post_id = (int) $post->ID;
}

$context = isset($args['context']) ? sanitize_key((string) $args['context']) : 'page';

if (
    $post_id &&
    function_exists('naigai_iez_get_page_hero_data') &&
    function_exists('naigai_iez_render_hero')
) {
    $hero_data = naigai_iez_get_page_hero_data($post_id);
    $hero_data['post_id'] = $post_id;
    $hero_data['context'] = $context;

    naigai_iez_render_hero($hero_data);
    return;
}

$title = $post_id ? get_the_title($post_id) : '';
?>
<section class="iez-hero iez-hero--fallback iez-hero--<?php echo esc_attr($context); ?> is-no-media" data-iez-hero data-iez-hero-engine="image">
    <div class="iez-hero__inner">
        <div class="iez-hero__content">
            <?php if ($title !== '') : ?>
                <h1 class="iez-hero__title"><?php echo esc_html($title); ?></h1>
            <?php endif; ?>
        </div>
    </div>
</section>
