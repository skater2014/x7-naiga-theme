// **アップロードされた画像を管理するグローバル変数**
window.uploadedImages = window.uploadedImages || [];  // 既に定義されていれば使う

// **画像アップロードのイベントリスナー**
jQuery(document).on('change', '#property_image', function (event) {
    getUploadedImages(event.target);
});

/**
 * 画像アップロードの取得（File オブジェクトで管理）
 */
function getUploadedImages(fileInput) {
    if (!fileInput) {
        console.error("fileInput が取得できませんでした。");
        return;
    }

    let maxFiles = 5;
    let maxSize = 5 * 1024 * 1024;
    let previewContainer = jQuery('.image-preview-container');

    let currentImages = Array.from(fileInput.files);

    // **現在の画像数 + 新規追加画像数 > 5枚の場合、警告**
    if (uploadedImages.length + currentImages.length > maxFiles) {
        alert("アップロードできる画像は最大5枚までです。");
        return;
    }

    currentImages.forEach((file) => {
        if (file.size > maxSize) {
            alert(`「${file.name}」のファイルサイズが5MBを超えています。`);
            return;
        }

        // **すでに追加された画像かチェック**
        if (!uploadedImages.some(img => img.name === file.name)) {
            uploadedImages.push(file);
            displayUploadedImages(uploadedImages, '.image-preview-container');
        }
    });

    fileInput.value = "";
}

/**
 * 画像のプレビュー更新
 */
function displayUploadedImages(images, containerSelector) {
    let imageContainer = jQuery(containerSelector);
    imageContainer.empty();

    if (images.length > 0) {
        imageContainer.show();
        images.forEach(imageFile => {
            let imageUrl = URL.createObjectURL(imageFile);

            let wrapper = jQuery('<div>').addClass('image-wrapper');
            let imgElement = jQuery('<img>').attr('src', imageUrl).addClass('image-preview');

            let deleteButton = jQuery('<button>')
                .text('削除')
                .addClass('delete-btn')
                .on('click', function () {
                    removeUploadedImage(imageFile);
                    wrapper.remove();
                });

            wrapper.append(imgElement).append(deleteButton);
            imageContainer.append(wrapper);
        });
    } else {
        imageContainer.hide();
    }
}

/**
 * アップロード画像を削除
 */
function removeUploadedImage(imageFile) {
    uploadedImages = uploadedImages.filter(img => img !== imageFile);

    // **フォーム側 & 確認画面側のプレビューを更新**
    displayUploadedImages(uploadedImages, '.image-preview-container');
    displayUploadedImages(uploadedImages, '#confirm-image-container');
}

/**
 * 画像アップロード処理（AJAX）
 */
function uploadImagesAndSubmitForm(formData, parent) {
    if (uploadedImages.length > 0) {
        uploadImages(uploadedImages, function (uploadedUrls) {
            formData.uploaded_images = uploadedUrls;
            sendReservationData(formData, parent);
        });
    } else {
        formData.uploaded_images = [];
        sendReservationData(formData, parent);
    }
}

/**
 * 画像をアップロード（Fileオブジェクトをサーバーに送信）
 */
function uploadImages(images, callback) {
    let formData = new FormData();
    formData.append('action', 'upload_images');

    images.forEach((image, index) => {
        formData.append('images[]', image);  // ✅ `File` データを送信
    });

    console.log("📤 送信する画像データ:", formData.getAll('images[]'));  // ✅ デバッグ

    jQuery.ajax({
        url: customAjax.ajaxurl,
        type: 'POST',
        data: formData,
        processData: false,  // ✅ `FormData` をそのまま送る
        contentType: false,  // ✅ `multipart/form-data` にする
        success: function (response) {
            console.log("✅ サーバーからのレスポンス:", response);  // ✅ デバッグ

            if (response.success && response.uploaded_image_urls.length > 0) {
                callback(response.uploaded_image_urls);
            } else {
                alert('画像アップロードに失敗しました');
                callback([]);
            }
        },
        error: function () {
            alert('画像アップロード時に通信エラーが発生しました');
            callback([]);
        }
    });
}

