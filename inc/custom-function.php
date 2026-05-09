<?php
// セクションの説明を出力する関数
function my_original_menu_section_func() {
    echo '<p>メディアライブラリーから画像を選択し、テキストロゴを入力して設定します。</p>';
}

// 画像のURLフィールドのHTMLを出力する関数
function my_original_menu_url_func() {
    $logo_url = esc_attr(get_option('my-original-menu-url'));
    echo '<input type="text" id="my-original-menu-url" name="my-original-menu-url" value="' . $logo_url . '" placeholder="画像のURLを入力してください" />';
    echo '<input type="button" id="upload_image_button" class="button" value="画像を選択" />';
}

// 画像形式の選択肢を提供する関数
function my_original_menu_image_format_func() {
    $image_format = esc_attr(get_option('my-original-menu-image-format'));
    $image_formats = array(
        'jpeg' => 'JPEG',
        'png' => 'PNG',
        'webp' => 'WebP',
    );
    echo '<select id="my-original-menu-image-format" name="my-original-menu-image-format">';
    foreach ($image_formats as $format_key => $format_value) {
        echo '<option value="' . $format_key . '" ' . selected($image_format, $format_key, false) . '>' . $format_value . '</option>';
    }
    echo '</select>';
}

// 幅のフィールドのHTMLを出力する関数 type=number step=1 にするとマークが表示 
function my_original_menu_width_func() {
    $width = esc_attr(get_option('my-original-menu-width'));
    echo '<input type="number" id="my-original-menu-width" name="my-original-menu-width" value="' . $width . '" placeholder="幅" step="1" />';
}

// 高さのフィールドのHTMLを出力する関数
function my_original_menu_height_func() {
    $height = esc_attr(get_option('my-original-menu-height'));
    echo '<input type="number" id="my-original-menu-height" name="my-original-menu-height" value="' . $height . '" placeholder="高さ" step="1" />';
}

// テキストロゴのフィールドのHTMLを出力する関数 
function my_original_menu_text_logo_func() {
    $text_logo = esc_attr(get_option('my-original-menu-text-logo')); // テキストロゴを取得
    $logo_url = esc_url(get_option('my-original-menu-url')); // 画像のURLを取得

    echo '<input type="text" id="my-original-menu-text-logo" name="my-original-menu-text-logo" value="' . $text_logo . '" placeholder="テキストロゴを入力してください" />';

    // ロゴの削除ボタン
    if ($logo_url) {
        echo '<button type="button" class="button remove_image_button" id="remove_image_button">画像を削除</button>';
    }
}

// フォントサイズのフィールドのHTMLを出力する関数
function my_original_menu_font_size_func() {
    $font_size = esc_attr(get_option('my-original-menu-font-size', '12')); // フォントサイズが未設定の場合はデフォルトで12になるように設定
    echo '<input type="number" id="my-original-menu-font-size" name="my-original-menu-font-size" value="' . $font_size . '" placeholder="フォントサイズ" step="1" />'; // step 属性の値を修正
}


// フォントカラーのフィールドのHTMLを出力する関数
function my_original_menu_font_color_func() {
    $font_color = esc_attr(get_option('my-original-menu-font-color'));
    echo '<input type="text" id="my-original-menu-font-color" name="my-original-menu-font-color" value="' . $font_color . '" placeholder="フォントカラー" class="my-color-field" />';
}

// 背景色のフィールドのHTMLを出力する関数
function my_original_menu_background_color_func() {
    $background_color = esc_attr(get_option('my-original-menu-background-color'));
    echo '<input type="text" id="my-original-menu-background-color" name="my-original-menu-background-color" value="' . $background_color . '" placeholder="背景色" class="my-color-field" />';
}

// リンク先URLフィールドのHTMLを出力する関数
function my_original_menu_link_url_func() {
    $link_url = esc_attr(get_option('my-original-menu-link-url'));
    echo '<select id="my-original-menu-link-url" name="my-original-menu-link-url">';
    echo '<option value="">--リンク先URLを選択--</option>'; // デフォルトの選択肢
    // 固定ページの取得
    $pages = get_pages();
    foreach ($pages as $page) {
        echo '<option value="' . get_page_link($page->ID) . '" ' . selected($link_url, get_page_link($page->ID), false) . '>' . esc_html($page->post_title) . '</option>';
    }
    // 投稿ページの取得
    $posts = get_posts(array('post_type' => 'post'));
    foreach ($posts as $post) {
        echo '<option value="' . get_permalink($post->ID) . '" ' . selected($link_url, get_permalink($post->ID), false) . '>' . esc_html($post->post_title) . '</option>';
    }
    // カテゴリーページの取得
    $categories = get_categories();
    foreach ($categories as $category) {
        echo '<option value="' . get_category_link($category->term_id) . '" ' . selected($link_url, get_category_link($category->term_id), false) . '>' . esc_html($category->name) . '</option>';
    }
    // アーカイブページの取得
    $archives = get_archives(array(
        'type' => 'monthly',
        'format' => 'option',
    ));
    foreach ($archives as $archive) {
        echo '<option value="' . esc_url(get_month_link($archive->year, $archive->month)) . '" ' . selected($link_url, esc_url(get_month_link($archive->year, $archive->month)), false) . '>' . esc_html($archive->label) . '</option>';
    }
    echo '</select>';
}

// フォントウェイトのフィールドのHTMLを出力する関数
function my_original_menu_font_weight_func() {
    // フォントウェイトの取得
    $font_weight = esc_attr(get_option('my-original-menu-font-weight'));
    // フォントウェイトの選択ボタンを追加
    echo '<select id="my-original-menu-font-weight" name="my-original-menu-font-weight">';
    echo '<option value="200" ' . selected($font_weight, '200', false) . '>200</option>';
    echo '<option value="400" ' . selected($font_weight, '400', false) . '>400</option>';
    echo '<option value="600" ' . selected($font_weight, '600', false) . '>600</option>';
    echo '</select>';
}

// Ajaxを使用してフォントウェイトを保存するアクションを追加
add_action('wp_ajax_nopriv_save_font_weight', 'save_font_weight_callback');
add_action('wp_ajax_save_font_weight', 'save_font_weight_callback'); // 管理画面で編集するユーザーだけを対象にするために追加

function save_font_weight_callback() {
    if (isset($_POST['fontWeight'])) {
        $font_weight = sanitize_text_field($_POST['fontWeight']);
        update_option('my-original-menu-font-weight', $font_weight);
        echo 'フォントウェイトが保存されました';
    }
    wp_die();
}


// ボーダーカラーのフィールドのHTMLを出力する関数
function my_original_menu_border_color_func() {
    // ボーダーカラーの取得
    $border_color = esc_attr(get_option('my-original-menu-border-color'));
    echo '<input type="text" id="my-original-menu-border-color" name="my-original-menu-border-color" value="' . $border_color . '" placeholder="ボーダーカラーを入力してください" class="my-color-field" />';
}

// 画像を削除するボタンのHTMLを出力する関数
function my_original_menu_remove_image_button_func() {
    // 画像を削除するボタンのHTML
    echo '<button type="button" class="button remove_image_button" id="remove_image_button">画像を削除</button>';
}


// フォントファミリーの選択肢を提供する関数
function my_original_menu_font_family_func() {
    $font_family = esc_attr(get_option('my-original-menu-font-family'));
    $font_families = array(
        '' => '選択してください', // 空の選択肢を追加
        'Arial' => 'Arial',
        'Helvetica' => 'Helvetica',
        'Times New Roman' => 'Times New Roman',
        // 他のフォントファミリーを追加する場合はここに追加してください
    );
    echo '<select id="my-original-menu-font-family" name="my-original-menu-font-family">';
    foreach ($font_families as $font_key => $font_value) {
        echo '<option value="' . $font_key . '" ' . selected($font_family, $font_key, false) . '>' . $font_value . '</option>';
    }
    echo '</select>';
}

// フォントスタイルの選択肢を提供する関数
function my_original_menu_font_style_func() {
    $font_style = esc_attr(get_option('my-original-menu-font-style'));
    $font_styles = array(
        'normal' => '通常',
        'bold' =>   'bold',
        'italic' => '斜体',
        'inherit' => '親要素のスタイルを継承',
    );
    echo '<select id="my-original-menu-font-style" name="my-original-menu-font-style">';
    foreach ($font_styles as $style_key => $style_value) {
        echo '<option value="' . $style_key . '" ' . selected($font_style, $style_key, false) . '>' . $style_value . '</option>';
    }
    echo '</select>';
}

// メインのコンテンツを表示
function render_custom_page_content() {
    echo '<div class="wrap">';
    echo '<h2>Custom Page</h2>';

    echo '<div class="form-wrap">'; // フォームのwrap
    echo '<form method="post" action="options.php">';
    settings_fields('my-original-menu'); // セクションの出力
    do_settings_sections('my-custom-page'); // セクション内の設定項目の出力
    submit_button('保存'); // 保存ボタンの出力
    echo '</form>';
    echo '</div>'; // フォームのwrapの終了

    echo '<div class="preview-wrap">'; // プレビューのwrap
    echo '<h2>プレビュー</h2>';

    // 追加の項目
    $url = get_option('my-original-menu-url');
    $text_logo = get_option('my-original-menu-text-logo');
    $width = get_option('my-original-menu-width');
    $height = get_option('my-original-menu-height'); // 高さを取得
    $font_color = get_option('my-original-menu-font-color');
    $background_color = get_option('my-original-menu-background-color');
    $font_size = get_option('my-original-menu-font-size'); // 追加：テキストロゴのフォントサイズを取得
    $font_style = get_option('my-original-menu-font-style'); // フォントスタイルを取得
    $font_family = get_option('my-original-menu-font-family'); // 追加：フォントファミリーを取得
    $border = get_option('my-original-menu-border-color'); // ボーダーの色を取得

    // デフォルトの幅を500pxに設定
    if (!$width) {
        $width = 500;
    }
        if (!$height) {
        $height = 100; // デフォルトの高さを設定
    }

    // プレビューの表示
        if ($url || $text_logo) {
            echo '<div id="preview-image-wrapper" class="logo" style="color: ' . $font_color . '; background-color: ' . $background_color . '; width: ' . $width . 'px; height: ' . $height . 'px;">';
            if ($url) {
                echo '<img id="preview-image" src="' . esc_url($url) . '" alt="Preview Image" style="width: 100%;">';
            }
            echo '</div>';

            if ($text_logo) {
                echo '<div id="preview-text-logo-wrapper" class="logo" style="color: ' . $font_color . '; background-color: ' . $background_color . '; width: ' . $width . 'px; height: ' . $height . 'px; font-family: ' . $font_family . ';">';
                echo '<span id="preview-text-logo" style="font-size: ' . $font_size . 'px; border: 2px solid ' . $border . ';">' . esc_html($text_logo) . '</span>';
                echo '</div>';
            }
        } else {
            echo '<p>プレビューがありません。</p>';
        }
        
    echo '</div>'; // previewの終了


    echo '</div>'; // メインwrapの終了
}  



function custom_admin_styles() {
    wp_enqueue_style('custom-admin-style', get_template_directory_uri() . '/inc/css/function-style.css');
}
add_action('admin_enqueue_scripts', 'custom_admin_styles');



// カスタムページに必要なスクリプトを追加する
function add_custom_page_scripts() {
    // WordPressのcolor pickerを有効化するスクリプト
    wp_enqueue_script('wp-color-picker');

    // メディアアップローダーを初期化するスクリプト
    wp_enqueue_script('site-logo-upload', get_template_directory_uri() . '/inc/js/site-logo-upload.js', array('jquery', 'wp-color-picker'), null, true);
}

add_action('admin_enqueue_scripts', 'add_custom_page_scripts');


// Ajaxを使用して画像の幅を保存するアクションを追加
add_action('wp_ajax_save_image_size', 'save_image_size_callback');
function save_image_size_callback() {
    if (isset($_POST['width'])) {
        $new_width = intval($_POST['width']);
        update_option('my-original-menu-width', $new_width);
        echo 'サイズが保存されました';
    }
    wp_die();
}
?>
