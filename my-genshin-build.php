<?php
/*
 * Template Name: My Genshin Build
 * Template Post Type: genshin-build
 * WordPressでは、新しい投稿タイプやタクソノミーを追加した際には、パーマリンクの再構築 設定の管理画面でパーマリンクをpost名でボタンを押す必要なことがあります。
 */
?>



<?php get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        <?php
        // Start the loop.
        while ( have_posts() ) :
            the_post();

            // Include the single post content template.
            get_template_part( 'template-parts/content', 'single-genshin_build' );

            // If comments are open or we have at least one comment, load up the comment template.
            if ( comments_open() || get_comments_number() ) :
                comments_template();
            endif;

            // End of the loop.
        endwhile;
        ?>
    </main>
</div>

<?php get_footer(); ?>
