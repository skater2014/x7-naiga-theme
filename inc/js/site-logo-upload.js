    // custom-function.php に必要なスクリプト
    jQuery(document).ready(function($) {
        // フォントカラーと背景色のcolor pickerを有効化
        $('.my-color-field').wpColorPicker();

        $('#upload_image_button').click(function() {
            var mediaUploader;
            if (mediaUploader) {
                mediaUploader.open();
                return;
            }
            mediaUploader = wp.media.frames.file_frame = wp.media({
                title: '画像を選択',
                button: {
                    text: '画像を選択'
                },
                multiple: false
            });
            mediaUploader.on('select', function() {
                attachment = mediaUploader.state().get('selection').first().toJSON();
                $('#my-original-menu-url').val(attachment.url);
                // 画像プレビューを更新
                $('#preview-image').attr('src', attachment.url);
            });
            mediaUploader.open();
        });

        // 画像の幅を変更する処理
        $('#my-original-menu-width').on('change input wheel', function(e) {
            var newWidth = parseInt($(this).val());
            if (e.type === 'change' || (e.type === 'wheel' && e.originalEvent.deltaY !== 0)) {
                $('#preview-wrapper').css('width', newWidth + 'px');
            }
        });

       // 画像の高さを変更する処理
        $('#my-original-menu-height').on('change input wheel', function(e) {
            var newHeight = parseInt($(this).val());
            if (e.type === 'change' || (e.type === 'wheel' && e.originalEvent.deltaY !== 0)) {
                $('#preview-wrapper').css('height', newHeight + 'px');
            }
        });


        // 画像を削除するボタンのクリックイベント
        $('#remove_image_button').click(function() {
            $('#my-original-menu-url').val(''); // 画像URLを空にする
            $('#preview-image').attr('src', ''); // プレビュー画像を空にする
        });

        // 画像を削除するボタンのクリックイベント
        $('.remove_image_button').click(function() {
            $('#my-original-menu-url').val(''); // 画像URLを空にする
            $('#preview-image').attr('src', ''); // プレビュー画像を空にする
        });

    });

jQuery(document).ready(function($) {
    // フォームの値が変更されたときにプレビューを更新
    $('#my-original-menu-width').on('change input wheel', function(e) {
        var newWidth = parseInt($(this).val());
        if (e.type === 'change' || (e.type === 'wheel' && e.originalEvent.deltaY !== 0)) {
            $('#preview-wrapper').css('width', newWidth + 'px');
            // 画像の幅をAjaxを使用して保存
            saveImageSize(newWidth);
        }
    });

    // Ajaxを使用して画像の幅を保存する関数
    function saveImageSize(width) {
        $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
                action: 'save_image_size',
                width: width,
            },
            success: function(response) {
                console.log(response);
            }
        });
    }
});





jQuery(document).ready(function($) {
    // フォントカラーと背景色のcolor pickerを有効化
    $('.my-color-field').wpColorPicker();

    // 画像のURLフィールドの変更イベントを監視
    $('#my-original-menu-url').on('change', function() {
        var imageUrl = $(this).val();
        $('#preview-image').attr('src', imageUrl);
    });

    // テキストロゴのフィールドの変更イベントを監視
    $('#my-original-menu-text-logo').on('input', function() {
        var textLogo = $(this).val();
        $('#preview-text-logo').text(textLogo);
    });

    // 幅のフィールドの変更イベントを監視
    $('#my-original-menu-width').on('change input', function() {
        var width = $(this).val();
        $('#preview-image-wrapper').css('width', width + 'px');
        $('#preview-text-logo-wrapper').css('width', width + 'px');
        saveImageSize(width); // 画像の幅を保存
    });

    // フォントサイズのフィールドの変更イベントを監視
    $('#my-original-menu-font-size').on('change input wheel', function(e) {
        var newSize = parseInt($(this).val());
        $('#preview-text-logo').css('font-size', newSize + 'px');
    });

    // フォントカラーのフィールドの変更イベントを監視
    $('#my-original-menu-font-color').on('change', function() {
        var fontColor = $(this).val();
        $('#preview-text-logo').css('color', fontColor);
    });

    // 背景色のフィールドの変更イベントを監視
    $('#my-original-menu-background-color').on('change', function() {
        var backgroundColor = $(this).val();
        $('#preview-image-wrapper, #preview-text-logo-wrapper').css('background-color', backgroundColor);
    });

    // ボーダーカラーのフィールドの変更イベントを監視
    $('#my-original-menu-border-color').on('change', function() {
        var borderColor = $(this).val();
        $('#preview-text-logo').css('border-color', borderColor);
    });

    // フォントスタイルのフィールドの変更イベントを監視
    $('#my-original-menu-font-style').on('change', function() {
        var fontStyle = $(this).val();
        $('#preview-text-logo').css('font-style', fontStyle);
    });

    // フォントファミリーのフィールドの変更イベントを監視
    $('#my-original-menu-font-family').on('change', function() {
        var fontFamily = $(this).val();
        $('#preview-text-logo').css('font-family', fontFamily);
    });
});




