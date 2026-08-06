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

        <div class="ch-lp-footer__bottom">
            <p>
                <a href="<?php echo esc_url(home_url('/iezukuri/rule/')); ?>">利用規約</a>
                <span aria-hidden="true">｜</span>
                <a href="<?php echo esc_url(home_url('/iezukuri/privacypolicy/')); ?>">プライバシーポリシー</a>
            </p>
        </div>
    </div>
</footer>

<?php
?>
<!-- IEZUKURI FIXED FOOTER START -->
<?php if (is_page('iezukuri') && has_nav_menu('footer_menu')) : ?>
    <ul id="js-footer-fixed" class="footer-fixed show">
        <?php
        wp_nav_menu(array(
            'theme_location' => 'footer_menu',
            'container'      => false,
            'items_wrap'     => '%3$s',
            'fallback_cb'    => false,
            'depth'          => 1,
        ));
        ?>
    </ul>

    <script>
    (() => {
        const adjustIezukuriChat = () => {
            const footer = document.getElementById('js-footer-fixed');

            if (!footer) {
                return;
            }

            const mobile = window.matchMedia('(max-width: 767px)').matches;
            const offset = footer.getBoundingClientRect().height + 12;

            document.querySelectorAll('body *').forEach((element) => {
                if (element.children.length !== 0) {
                    return;
                }

                if (element.textContent.trim() !== 'チャット相談') {
                    return;
                }

                let fixedElement = element;

                while (
                    fixedElement &&
                    fixedElement !== document.body &&
                    getComputedStyle(fixedElement).position !== 'fixed'
                ) {
                    fixedElement = fixedElement.parentElement;
                }

                if (!fixedElement || fixedElement === document.body) {
                    return;
                }

                if (mobile) {
                    fixedElement.style.setProperty(
                        'bottom',
                        offset + 'px',
                        'important'
                    );
                } else {
                    fixedElement.style.removeProperty('bottom');
                }
            });

            if (mobile) {
                document.body.style.paddingBottom =
                    footer.getBoundingClientRect().height + 'px';
            } else {
                document.body.style.removeProperty('padding-bottom');
            }
        };

        window.addEventListener('load', adjustIezukuriChat);
        window.addEventListener('resize', adjustIezukuriChat);

        const observer = new MutationObserver(adjustIezukuriChat);

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });

        adjustIezukuriChat();
    })();
    </script>
<?php endif; ?>
<!-- IEZUKURI FIXED FOOTER END -->
<?php
wp_footer();

$symbol_defs = get_template_directory() . '/images/symbol-defs.svg';
if (file_exists($symbol_defs)) {
    include_once $symbol_defs;
}
?>

</body>
</html>
