<?php
/**
 * Template for /privacypolicy
 *
 * 役割:
 * - 通常の固定ページとして表示する
 * - header/footer は必ず通常テーマのものを読む
 * - CSSや余計な分岐はここでは追加しない
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<main id="primary" class="site-main page-privacypolicy">
    <?php
    while (have_posts()) :
        the_post();
        ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class('privacy-policy-page'); ?>>
            <header class="entry-header">
                <h1 class="entry-title"><?php the_title(); ?></h1>
            </header>

            <div class="entry-content">
                <?php the_content(); ?>
            </div>
        </article>
        <?php
    endwhile;
    ?>
</main>

<?php
get_footer();
