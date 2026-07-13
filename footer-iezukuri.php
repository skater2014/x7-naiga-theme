<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * footer-iezukuri.php
 *
 * 家づくりfooterの正本。
 *
 * この1ファイルだけで管理する:
 * - 黒footerのHTML
 * - wp_footer()
 * - SVG sprite symbol-defs.svg
 * - </body></html>
 *
 * 注意:
 * - shared/section-footer.php はもう読まない。
 * - 各テンプレートは get_footer('iezukuri') だけを呼ぶ。
 */
?>

<?php
/**
 * いえづくり共通 footer
 *
 * 正本:
 * - HTML: hub/pages/iezukuri/templates/shared/section-footer.php
 * - CSS : hub/pages/iezukuri/css/common/footer.css
 *
 * ここに copyright は入れない。
 * customhome footer も呼ばない。
 */

if (!defined('ABSPATH')) {
    exit;
}

$footer_items = function_exists('naigai_iezukuri_footer_menu_items')
    ? naigai_iezukuri_footer_menu_items()
    : array();
?>

<footer class="ch-lp-footer" aria-label="家づくりフッター">
    <div class="ch-shell">
        <div class="ch-lp-footer__grid">
            <div class="ch-lp-footer__lead">
                <p class="ch-lp-footer__label">内外建設株式会社</p>
                <h3>那須で、暮らしに合う住まいを考える。</h3>
                <p>新築・中古・二世帯・リフォームまで、家づくりの情報をまとめています。</p>
            </div>

            <nav class="ch-lp-footer__nav" aria-label="家づくりメニュー">
                <p class="ch-lp-footer__label">MENU</p>
                <h4>家づくりメニュー</h4>

                <?php if (has_nav_menu('iezukuri_footer_menu')) : ?>
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'iezukuri_footer_menu',
                        'container'      => false,
                        'menu_class'     => 'ch-lp-footer__menu',
                        'depth'          => 1,
                        'fallback_cb'    => false,
                    ));
                    ?>
                <?php else : ?>
                    <ul class="ch-lp-footer__menu">
                        <?php foreach ($footer_items as $item) : ?>
                            <li>
                                <a href="<?php echo esc_url($item['url']); ?>">
                                    <?php echo esc_html($item['label']); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </nav>
        </div>
    </div>
</footer>

<?php
wp_footer();

$symbol_defs = get_template_directory() . '/images/symbol-defs.svg';
if (file_exists($symbol_defs)) {
    include_once $symbol_defs;
}
?>

</body>
</html>
