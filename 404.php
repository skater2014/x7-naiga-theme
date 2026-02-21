<?php get_header('77'); ?>

<div id="content" class="site-content">
    <div class="container">
        <!-- 🔹 パンくずリスト -->
        <?php if (function_exists('breadcrumb1')) : ?>
            <nav class="breadcrumb" aria-label="breadcrumb">
                <?php breadcrumb1(); ?>
            </nav>
        <?php endif; ?>

        <p>申し訳ありません。お探しのページは存在しません。</p>

        <!-- 🔹 トップページへのリンク -->
        <a href="<?php echo home_url(); ?>" class="btn btn-primary">トップページに戻る</a>
    </div>
</div>

<?php get_footer(); ?>
