</div><!--/End page wrapper-->

<!-- Start footer -->
<div id="footer">
    <p class="footer-wrapper">
        &copy; <?php bloginfo('name'); ?> All Rights Reserved.
        <?php if (get_privacy_policy_url()) : ?>
            <a href="<?php echo esc_url(get_privacy_policy_url()); ?>" style="margin-left: 1em;">プライバシーポリシー</a>
        <?php endif; ?>
    </p>
</div>



<?php
// 2番目のフッターメニューを表示する条件：store-reservation-page.php 以外
if (!is_page_template('page-store-reservation.php')) :
?>
<ul id="js-footer-fixed" class="footer-fixed show">
    <?php
    if (is_singular('post') || is_singular('house')) {
        wp_nav_menu(array(
            'theme_location' => 'post_footer_menu',
            'container'      => false,
            'items_wrap'     => '%3$s',
            'link_class'     => 'footer-fixed__link',
            'fallback_cb'    => false,
            'walker'         => class_exists('Custom_Walker_Nav_Menu') ? new Custom_Walker_Nav_Menu() : null,
        ));
    } else {
        wp_nav_menu(array(
            'theme_location' => 'footer_menu',
            'container'      => false,
            'items_wrap'     => '%3$s',
            'link_class'     => 'footer-fixed__link',
            'fallback_cb'    => false,
            'walker'         => class_exists('Custom_Walker_Nav_Menu') ? new Custom_Walker_Nav_Menu() : null,
        ));
    }
    ?>
</ul>
<?php endif; ?>

<?php
// ページのテンプレートが "予約フォームページ" かどうかを確認
if (!is_page_template('page-store-reservation.php')) :
    // store-reservation-modalテンプレートを表示
    get_template_part('templates/store-reservation-modal');
endif;
?>

<?php wp_footer(); ?> <!-- wp_footer()をここに配置 -->

<!-- SVG FILE https://icomoon.io/ file -->
<?php include_once get_template_directory() . '/images/symbol-defs.svg'; ?>
<!-- SVG FILE End -->

</body>
</html>
<!-- /footer.php -->
