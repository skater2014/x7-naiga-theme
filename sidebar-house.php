<?php
/****************************************

  sidebar-house.php

  Popular Post for sidebar 
  
  Genshin Tekken カテゴリー別でサイドバーに表示。
  該当するカテゴリーがないなら sidebar.php 表示。
  サイドバーを表示するための
  テンプレートファイルです。
  カスタマイズしたサイドバーです。
  /themes/your-theme/
│
├── /template-parts/
│   ├── social-icons.php
│   ├── material-post.php
│   ├── popular-post.php
│   ├── artifacts-post.php
│   └── tag-cloud.php
│
├── sidebar.php
└── functions.php

single.php 配置
<div id="single">
    <?php if (in_category('naigai-construction')) : ?> <?//カスタム投稿タイプ「news」の場合?>
        <?php get_template_part('sidebar-house'); ?>
    <?php elseif (in_category('naigai-tochi')) : ?> <?//*カテゴリー「trend」の場合?>
        <?php get_template_part('sidebar-land'); ?>
    <?php else : ?> <?//それ以外のページの場合?>
        <?php get_template_part('sidebar'); ?><?//昔のサイドバーコードがやや違う?>
    <?php endif; ?>
</div>
*****************************************/
?>

<div id="sidebar">
<?php 
    // 'price-range' サイドバーが有効であり、指定された条件を満たす場合にのみ表示
    if ( is_active_sidebar( 'price-range' ) && 
        // 'company' 固定ページと 'sample-page' 固定ページでは表示しない
        !is_page(array('company', 'sample-page')) && 
        // 投稿ページ（'single.php'）では表示しない
        !is_single() && 
        // 'single-house' カスタム投稿タイプの投稿ページでは表示しない
        !is_singular('single-house') && 
        // 'blog' カスタム投稿タイプのアーカイブページ（一覧）では表示しない ← ★今回の追加条件
        !is_post_type_archive('blog')
    ) :
?>
    <!-- 価格範囲サイドバーを表示 -->
    <div id="price-range-sidebar" class="">
        <?php dynamic_sidebar( 'price-range' ); ?>
    </div>
<?php endif; ?>




    <!-- 既存のサイドバーコード -->
    <?php dynamic_sidebar('sidebar-2'); ?>
    <?php get_template_part('template-sidebar-parts/social-icons'); ?>

    <?php get_template_part('template-sidebar-parts/material1-post');?>

    <?php get_template_part('template-sidebar-parts/popular-post'); ?>
    
    <?php get_template_part('template-sidebar-parts/artifacts-post'); ?>

    <?php
    if ( !is_mobile_device() ) :
        get_template_part('template-sidebar-parts/tag-cloud');
    endif;
    ?>

    <!-- カスタムウィジェットを表示 -->
    <?php //dynamic_sidebar('widget1'); ?>
    <?php //dynamic_sidebar('widget'); ?>

    <!-- 通常のサイドバーウィジェットのコード -->
    <?php //dynamic_sidebar('Custom Ad Banner Widget Area'); ?>
    <?php dynamic_sidebar('sidebar-1'); ?>

    <?php //dynamic_sidebar('weekly-farming-schedule'); //635行 function.php ?>



<?php //ここにdiv 追加するとFooter までWrapper が囲まれなくない?>
</div>
<!--End/Sidebar-->
