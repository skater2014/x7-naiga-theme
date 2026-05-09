<?php
/**
 * =========================================================
 * B2C FOOTER LINKS INCLUDE NOTE
 * ---------------------------------------------------------
 * このファイルは B2C 内から共通 footer nav を呼ぶだけの薄いincludeです。
 *
 * レイアウト崩れ防止:
 * - ここで .mnpk-page-shell / .mnpk-shell / 独自wrapperを追加しない
 * - 幅管理は呼び出し元の minpaku/b2c/page.php の <main class="mnpk-page-shell"> に任せる
 * - HTML本体は minpaku/common/templates/minpaku-footer-nav.php に集約する
 * =========================================================
 */
if (!defined('ABSPATH')) exit;

$common_footer = get_template_directory() . '/minpaku/common/templates/minpaku-footer-nav.php';

if (file_exists($common_footer)) {
    require $common_footer;
}
