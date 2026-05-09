<?php // セクションと設定を追加
function add_original_settings() {
    // セクションの追加
    add_settings_section(
        'my-original-menu-section', // セクションID
        'メディアライブラリー画像設定', // セクションのタイトル
        'my_original_menu_section_func', // セクションの説明を出力する関数名
        'my-custom-page' // ページスラッグ
    );

    // 各設定項目の追加
    add_settings_field('my-original-menu-image-format', '画像形式', 'my_original_menu_image_format_func', 'my-custom-page', 'my-original-menu-section');
    add_settings_field('my-original-menu-url', '画像のURL', 'my_original_menu_url_func', 'my-custom-page', 'my-original-menu-section');
    add_settings_field('my-original-menu-width', '幅', 'my_original_menu_width_func', 'my-custom-page', 'my-original-menu-section');
    add_settings_field('my-original-menu-height', '高さ', 'my_original_menu_height_func', 'my-custom-page', 'my-original-menu-section'); // 追加：高さの設定項目
    add_settings_field('my-original-menu-text-logo', 'テキストロゴ', 'my_original_menu_text_logo_func', 'my-custom-page', 'my-original-menu-section');
    add_settings_field('my-original-menu-font-size', 'フォントサイズ', 'my_original_menu_font_size_func', 'my-custom-page', 'my-original-menu-section');
    add_settings_field('my-original-menu-link-url', 'リンク先URL', 'my_original_menu_link_url_func', 'my-custom-page', 'my-original-menu-section');
    add_settings_field('my-original-menu-font-color', 'フォントカラー', 'my_original_menu_font_color_func', 'my-custom-page', 'my-original-menu-section');
    add_settings_field('my-original-menu-background-color', '背景色', 'my_original_menu_background_color_func', 'my-custom-page', 'my-original-menu-section');
    add_settings_field('my-original-menu-font-family', 'フォントファミリー', 'my_original_menu_font_family_func', 'my-custom-page', 'my-original-menu-section');
    add_settings_field('my-original-menu-font-style', 'フォントスタイル', 'my_original_menu_font_style_func', 'my-custom-page', 'my-original-menu-section');
    add_settings_field('my-original-menu-font-weight', 'フォントウェイト', 'my_original_menu_font_weight_func', 'my-custom-page', 'my-original-menu-section');
    add_settings_field('my-original-menu-border-color', 'ボーダーカラー', 'my_original_menu_border_color_func', 'my-custom-page', 'my-original-menu-section');
    add_settings_field('my-original-menu-remove-image-button', '画像を削除する', 'my_original_menu_remove_image_button_func', 'my-custom-page', 'my-original-menu-section');

    // 設定の登録
    $settings = array(
        'my-original-menu-text-logo',
        'my-original-menu-image-format',
        'my-original-menu-link-url',
        'my-original-menu-url',
        'my-original-menu-width',
        'my-original-menu-height', // 追加：高さの設定項目
        'my-original-menu-font-size',
        'my-original-menu-font-color',
        'my-original-menu-background-color',
        'my-original-menu-font-family',
        'my-original-menu-font-style',
        'my-original-menu-font-weight',
        'my-original-menu-border-color'
    );

    foreach ($settings as $setting) {
        register_setting('my-original-menu', $setting);
    }
}
add_action('admin_init', 'add_original_settings');