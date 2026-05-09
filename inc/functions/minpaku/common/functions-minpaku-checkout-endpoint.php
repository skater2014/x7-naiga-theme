<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * =========================================================
 * 民泊 checkout endpoint
 * =========================================================
 *
 * このファイルの目的:
 * - 民泊詳細URLの末尾に /checkout/ を付けた仮想URLを有効にする
 * - そのURLのときだけ checkout 専用テンプレートを表示する
 *
 * 例:
 * 通常の詳細ページ
 * /minpaku-stay/room/test-minpaku1/
 *
 * checkout 用の仮想URL
 * /minpaku-stay/room/test-minpaku1/checkout/
 *
 * 重要:
 * - 固定ページは作らない
 * - 管理画面で checkout というページを作るわけでもない
 * - single-minpaku-checkout.php は
 *   WordPress が自動で見つけるテンプレート名ではない
 * - このファイルで template_include を使って
 *   「checkout のときだけこのファイルを使え」と指示する
 */


/**
 * ---------------------------------------------------------
 * 1. checkout endpoint を登録
 * ---------------------------------------------------------
 *
 * add_rewrite_endpoint() の役割:
 * - 投稿URLの末尾に /checkout/ を付けた形を
 *   WordPress が受け取れるようにする
 *
 * ここでは EP_PERMALINK を使っているので、
 * 個別ページURLの末尾に /checkout/ を追加できるようになる
 *
 * 例:
 * /minpaku-stay/room/test-minpaku1/checkout/
 */
if (!function_exists('mnpk_register_checkout_endpoint')) {
    function mnpk_register_checkout_endpoint()
    {
        add_rewrite_endpoint('checkout', EP_PERMALINK);
    }
}
add_action('init', 'mnpk_register_checkout_endpoint', 20);


/**
 * ---------------------------------------------------------
 * 2. 今のURLが checkout endpoint かどうか判定
 * ---------------------------------------------------------
 *
 * この関数の役割:
 * - いま表示しようとしているURLが
 *   「民泊詳細ページの checkout 付きURL」かどうかを判定する
 *
 * 判定の流れ:
 * 1. まず民泊詳細ページベースかどうか調べる
 *    → is_singular('minpaku')
 *
 * 2. その上で、URL末尾に checkout が付いているか調べる
 *    → $wp_query->query_vars['checkout'] が存在するか
 *
 * true になるURL例:
 * /minpaku-stay/room/test-minpaku1/checkout/
 *
 * false になるURL例:
 * /minpaku-stay/room/test-minpaku1/
 */
if (!function_exists('mnpk_is_checkout_endpoint')) {
    function mnpk_is_checkout_endpoint()
    {
        // まず民泊詳細ページベースでなければ checkout 扱いしない
        if (!is_singular('minpaku')) {
            return false;
        }

        global $wp_query;

        // endpoint が付いていれば query_vars に checkout が入る
        return isset($wp_query->query_vars['checkout']);
    }
}


/**
 * ---------------------------------------------------------
 * 3. checkout のときだけ専用テンプレートへ切り替える
 * ---------------------------------------------------------
 *
 * template_include は、
 * WordPress が「最終的にどのテンプレートファイルを使うか」
 * を決める直前に割り込める filter。
 *
 * 通常:
 * - 民泊詳細ページなら single-minpaku.php が使われる
 *
 * checkout のとき:
 * - single-minpaku-checkout.php を使うように上書きする
 */
if (!function_exists('mnpk_include_checkout_template')) {
    function mnpk_include_checkout_template($template)
    {
        // checkout URL でなければ、元のテンプレートをそのまま使う
        if (!mnpk_is_checkout_endpoint()) {
            return $template;
        }

        // checkout 専用テンプレートの実ファイルパス
        $checkout_template = get_template_directory() . '/single-minpaku-checkout.php';

        // ファイルが存在するときだけ checkout テンプレートへ切り替える
        if (file_exists($checkout_template)) {
            return $checkout_template;
        }

        // 無ければ元のテンプレートに戻す
        return $template;
    }
}
add_filter('template_include', 'mnpk_include_checkout_template', 99);


/**
 * ---------------------------------------------------------
 * 4. rewrite を一度だけ更新
 * ---------------------------------------------------------
 *
 * endpoint を追加しただけでは、
 * WordPress が古いURLルールを覚えたままのことがある。
 *
 * そのため rewrite ルールの更新が必要になる。
 *
 * 方法は2つある:
 * 1. 管理画面の「設定 > パーマリンク」で保存する
 * 2. このコードで flush_rewrite_rules(false) を一度だけ実行する
 *
 * この関数は 2 の自動版。
 *
 * ポイント:
 * - 毎回 flush すると重いので、一度だけにする
 * - $version を変えたときだけ、もう一度 flush される
 */
if (!function_exists('mnpk_maybe_flush_checkout_endpoint_rules')) {
    function mnpk_maybe_flush_checkout_endpoint_rules()
    {
        // endpoint のURLルールを更新したいときは、この文字列を変更する
        $version = '20260416_checkout_01';

        // すでに同じバージョンで処理済みなら何もしない
        if (get_option('mnpk_checkout_endpoint_version') === $version) {
            return;
        }

        // endpoint を登録してから rewrite ルールを更新する
        mnpk_register_checkout_endpoint();
        flush_rewrite_rules(false);

        // 今回のバージョンで更新済みとして保存
        update_option('mnpk_checkout_endpoint_version', $version);
    }
}
add_action('init', 'mnpk_maybe_flush_checkout_endpoint_rules', 99);
