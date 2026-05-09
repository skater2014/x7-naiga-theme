<!-- ウィジェット部分を追加 -->
<div class="widget-area">
    <?php if ( is_active_sidebar( 'sidebar-widget-area' ) ) : ?>
        <!-- ウィジェットエリアが有効の場合 -->
        <?php dynamic_sidebar( 'sidebar-widget-area' ); ?>
    <?php else : ?>
        <!-- ウィジェットが設定されていない場合 -->
        <p>ウィジェットが設定されていません。</p>
    <?php endif; ?>
</div>
