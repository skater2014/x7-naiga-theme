<?php
// 管理メニューに新しいページを追加
add_action('admin_menu', 'custom_table_menu');
function custom_table_menu() {
    add_menu_page(
        'Custom Table', // ページのタイトル
        'Custom Table', // メニューのタイトル
        'manage_options', // 権限
        'custom-table', // メニューのスラッグ
        'custom_table_page', // コールバック関数
        'dashicons-editor-table', // アイコン
        20 // メニューの位置
    );
}

// 管理ページテンプレート
function custom_table_page() {
    ?>
    <div class="wrap">
        <h1>Custom Table</h1>
        <form id="custom-table-form">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Text</th>
                        <th>Image</th>
                        <th>Additional Column 1</th>
                        <th>Additional Column 2</th>
                        <!-- ここに必要なだけ列を追加 -->
                    </tr>
                </thead>
                <tbody>
                    <!-- 初期フィールドの追加 -->
                    <tr>
                        <td><textarea name="text_area[]"></textarea></td>
                        <td>
                            <div class="image-preview"></div>
                            <button type="button" class="upload-image-button button">Select Image</button>
                            <input type="hidden" name="image_url[]" value="">
                        </td>
                        <td><textarea name="additional_text_1[]"></textarea></td>
                        <td><div class="additional-image-preview-1"></div><button type="button" class="additional-upload-image-button-1 button">Select Image</button><input type="hidden" name="additional_image_url_1[]" value=""></td>
                        <!-- 必要なだけ列を追加 -->
                    </tr>
                </tbody>
            </table>
            <button type="button" id="add-row" class="button">Add Row</button>
            <input type="submit" value="Save" class="button button-primary">
        </form>
    </div>

    <!-- JavaScript -->
    <script>
    jQuery(document).ready(function($){
        // 画像選択ボタンのクリックイベント
        $(document).on('click', '.upload-image-button', function(e) {
            e.preventDefault();
            var button = $(this);
            var imageField = button.prev('input[type="hidden"]');
            var imagePreview = button.prevAll('.image-preview');

            // メディアライブラリーを開く
            var image = wp.media({
                title: 'Select Image',
                multiple: false
            }).open().on('select', function(){
                var uploadedImage = image.state().get('selection').first();
                var imageUrl = uploadedImage.toJSON().url;
                imageField.val(imageUrl);
                imagePreview.html('<img src="' + imageUrl + '" style="max-width: 150px;">');
            });
        });

        // 追加の画像選択ボタンのクリックイベント
        $(document).on('click', '.additional-upload-image-button-1', function(e) {
            e.preventDefault();
            var button = $(this);
            var imageField = button.next('input[type="hidden"]');
            var imagePreview = button.nextAll('.additional-image-preview-1');

            // メディアライブラリーを開く
            var image = wp.media({
                title: 'Select Image',
                multiple: false
            }).open().on('select', function(){
                var uploadedImage = image.state().get('selection').first();
                var imageUrl = uploadedImage.toJSON().url;
                imageField.val(imageUrl);
                imagePreview.html('<img src="' + imageUrl + '" style="max-width: 150px;">');
            });
        });

        // 行を追加するボタンのクリックイベント
        $('#add-row').click(function() {
            $('.custom-table tbody').append('<tr><td><textarea name="text_area[]"></textarea></td><td><div class="image-preview"></div><button type="button" class="upload-image-button button">Select Image</button><input type="hidden" name="image_url[]" value=""></td><td><textarea name="additional_text_1[]"></textarea></td><td><div class="additional-image-preview-1"></div><button type="button" class="additional-upload-image-button-1 button">Select Image</button><input type="hidden" name="additional_image_url_1[]" value=""></td></tr>');
        });

        // フォームの送信イベント
        $('#custom-table-form').submit(function(e) {
            e.preventDefault();
            var formData = $(this).serialize();

            $.post(ajaxurl, {
                action: 'save_custom_table',
                data: formData
            }, function(response) {
                alert('Data saved: ' + response);
                location.reload();
            });
        });
    });
    </script>
    <?php
}

// AJAXハンドラの登録
add_action('wp_ajax_save_custom_table', 'save_custom_table');
function save_custom_table() {
    if (isset($_POST['data'])) {
        parse_str($_POST['data'], $formData);

        // データを構造化して保存
        $custom_table_data = array();
        for ($i = 0; $i < count($formData['text_area']); $i++) {
            $custom_table_data[] = array(
                'text' => sanitize_text_field($formData['text_area'][$i]),
                'image' => esc_url_raw($formData['image_url'][$i]),
                'additional_text_1' => sanitize_text_field($formData['additional_text_1'][$i]),
                'additional_image_1' => esc_url_raw($formData['additional_image_url_1'][$i])
                // 追加の列があれば同様に追加
            );
        }

        // オプションとして保存
        update_option('custom_table_data', $custom_table_data);

        echo 'Custom fields saved successfully.';
    } else {
        echo '
No data received.';
    }
    wp_die();
}

// メディアライブラリのスクリプトを管理画面で使用できるようにする
add_action('admin_enqueue_scripts', 'enqueue_media_uploader');
function enqueue_media_uploader() {
    wp_enqueue_media();
}
?>
