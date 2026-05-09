<?php
// 管理画面用のメニュー項目を追加
function add_custom_admin_page() {
    add_menu_page(
        'Custom Page',              // ページのタイトル
        'Custom Page',              // メニューのタイトル
        'manage_options',           // 必要な権限
        'my-custom-page',           // ページスラッグ
        'render_custom_page_content', // コンテンツを表示するためのコールバック関数
        'dashicons-admin-generic',  // アイコン
        99                          // メニューの位置
    );
}
add_action('admin_menu', 'add_custom_admin_page');

// フォーム設定ファイルの読み込み
require_once get_template_directory() . '/inc/settings.php';

// フォーム設定ファイルの読み込み
require_once get_template_directory() . '/inc/custom-function.php';