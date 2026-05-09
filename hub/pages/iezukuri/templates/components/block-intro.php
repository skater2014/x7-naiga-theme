<?php
if (!defined('ABSPATH')) {
    exit;
}

$kicker = $args['kicker'] ?? 'INTRO';
$title  = $args['title'] ?? get_the_title($page_id);
$lead   = $args['lead'] ?? '';
?>

<section class="iez-block iez-block-intro">
    <div class="iez-block__inner">
        <p class="iez-block__kicker"><?php echo esc_html($kicker); ?></p>

        <h2 class="iez-block__title">
            <?php echo esc_html($title); ?>
        </h2>

        <?php if ($lead !== '') : ?>
            <p class="iez-block__lead">
                <?php echo esc_html($lead); ?>
            </p>
        <?php endif; ?>
    </div>
</section>
