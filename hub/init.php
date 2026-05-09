<?php
/**
 * hub/init.php
 *
 * Hub bootstrap.
 * ここでは /hub/pages/iezukuri は読まない。
 * /iezukuri 系は必要になった時だけ functions.php から個別 loader を読む。
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * 共通 Hub loader が存在する場合だけ読み込む。
 * 無ければ何もしない。
 */
$hub_loader = get_template_directory() . '/inc/functions/hub/functions-hub-loader.php';

if (file_exists($hub_loader)) {
    require_once $hub_loader;
}
