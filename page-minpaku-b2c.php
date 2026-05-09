<?php
/**
 * =========================================================
 * B2C LAYOUT NOTE
 * ---------------------------------------------------------
 * このファイルは B2C 固定ページテンプレートの loader です。
 * 実際のHTML構造は minpaku/b2c/page.php にあります。
 *
 * レイアウト崩れ防止:
 * - ここに footer nav / hero / section HTML を直接追加しない
 * - B2C本文の幅や並びを直す場合は minpaku/b2c/page.php を見る
 * =========================================================
 */

/**
 * Template Name: Minpaku B2C Unified
 */
if (!defined('ABSPATH')) exit;
require get_template_directory() . '/minpaku/b2c/page.php';
