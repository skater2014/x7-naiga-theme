// **アップロードされた画像を管理するグローバル変数**
window.uploadedImages = [];

// **画像アップロードのイベントリスナー**
jQuery(document).on('change', '#property_image', function (event) {
    console.log("📂 画像選択イベント発生", event.target.files);
    getUploadedImages(event.target);
});

/**
 * 画像アップロードの取得（File オブジェクトで管理）
 */
function getUploadedImages(fileInput) {
    if (!fileInput) {
        console.error("❌ fileInput が取得できませんでした。");
        return;
    }

    let maxFiles = 5; // 最大5枚まで
    let maxSize = 5 * 1024 * 1024; // 最大5MB
    let currentImages = Array.from(fileInput.files);

    console.log("📤 選択された画像:", currentImages);

    if (uploadedImages.length + currentImages.length > maxFiles) {
        alert("アップロードできる画像は最大5枚までです。");
        return;
    }

    currentImages.forEach((file) => {
        if (file.size > maxSize) {
            alert(`❌ 「${file.name}」のファイルサイズが5MBを超えています。`);
            return;
        }

        // ✅ 画像形式チェック（ここが追加点）　不正な画像をブロックしてpop up で警告表示
        const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!allowedTypes.includes(file.type)) {
            alert(`❌ 「${file.name}」は許可されていない画像形式です。`);
            return;
        }

        // **同じファイルがアップロードされていないかチェック**
        let isDuplicate = uploadedImages.some(img => img.name === file.name && img.size === file.size && img.lastModified === file.lastModified);
        if (!isDuplicate) {
            uploadedImages.push(file);
            console.log("✅ 画像追加:", file);
        } else {
            alert(`❌ 「${file.name}」はすでにアップロードされています。`);
        }
    });

    console.log("📸 現在の画像リスト:", uploadedImages);
    updateAllPreviews();

    // **ファイル入力をリセット（同じファイルが再選択できるように）**
    fileInput.value = "";
}

/**
 * 画像プレビューの表示を一括更新
 */
function updateAllPreviews() {
    displayUploadedImages(uploadedImages, '.image-preview-container');
    updateConfirmationPreview(uploadedImages);
}

/**
 * 画像アップロードのプレビューを表示
 */
function displayUploadedImages(images, containerSelector) {
    let container = jQuery(containerSelector);
    container.empty();

    if (images.length > 0) {
        container.show();
    } else {
        container.hide();
    }

    images.forEach((image, index) => {
        let imageUrl = typeof image === 'string' ? image : URL.createObjectURL(image);
        let imgElement = `
            <div class="uploaded-image image-wrapper" data-index="${index}">
                <img src="${imageUrl}" class="image-preview" alt="アップロード画像">
                <button type="button" class="delete-btn remove-image" data-index="${index}">×</button>
            </div>`;
        container.append(imgElement);
    });

    // **削除ボタンのクリックイベントを設定**
    jQuery('.remove-image').off('click').on('click', function() {
        let indexToRemove = parseInt(jQuery(this).attr('data-index'), 10);
        
        if (!isNaN(indexToRemove) && indexToRemove >= 0) {
            uploadedImages.splice(indexToRemove, 1);
            console.log("❌ 画像削除:", uploadedImages);

            updateAllPreviews(); // **プレビューを一括更新**
        }
    });

    console.log("🛠 画像プレビューを更新しました");
}

/**
 * 画像プレビューを確認画面に表示
 */
function updateConfirmationPreview(imageList) {
    let container = jQuery('#confirm-image-container');
    container.empty();

    if (imageList.length > 0) {
        container.show();
        imageList.forEach((image, index) => {
            let imageUrl = typeof image === 'string' ? image : URL.createObjectURL(image);
            let imgElement = `<div class="uploaded-image image-wrapper" data-index="${index}">
                <img src="${imageUrl}" class="image-preview" alt="アップロード画像">
            </div>`;
            container.append(imgElement);
        });
    } else {
        container.hide();
    }

    console.log("📸 確認画面のプレビューを更新しました");
}

/**
 * 画像をアップロードしてフォームを送信
 */
/*function uploadImagesAndSubmitForm(formData, parent) {
    console.log("📤 フォーム送信開始: ", formData);
    
    if (uploadedImages.length > 0) {
        uploadImages(uploadedImages, function (uploadedUrls) {
            console.log("✅ アップロード画像のURL:", uploadedUrls);

            formData.uploaded_images = uploadedUrls;
            sendReservationData(formData, parent);

            updateConfirmationPreview(uploadedUrls);
        });
    } else {
        formData.uploaded_images = [];
        console.log("📤 画像なしで送信: ", formData);
        sendReservationData(formData, parent);
    }
}

/**
 * 画像をアップロード（Fileオブジェクトをサーバーに送信）
 */
/*function uploadImages(images, callback) {
    let formData = new FormData();
    formData.append('action', 'upload_images');
    formData.append('store_reservation_nonce', customAjax.nonce);

    let customerName = jQuery('#name').val().trim();
    let nameSlug = jQuery('#name_slug').val().trim(); // ✅ ← 追加

    if (!customerName) {
        alert('お名前を入力してください');
        return;
    }
    formData.append('name', customerName);
    formData.append('name_slug', nameSlug); // ✅ ← この1行を追加！！


    images.forEach((image) => {
        formData.append('images[]', image, image.name);
    });

    console.log("📤 送信する画像データ:", formData);

    jQuery.ajax({
        url: customAjax.ajaxurl,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function (response) {
            console.log("✅ サーバーからのレスポンス:", response);
            if (response.success && Array.isArray(response.uploaded_image_urls) && response.uploaded_image_urls.length > 0) {
                callback(response.uploaded_image_urls);
            } else {
                console.error("❌ 画像アップロードに失敗", response);
                alert('画像アップロードに失敗しました');
                callback([]);
            }
        },
        error: function (xhr, status, error) {
            console.error("❌ AJAXエラー:", xhr.responseText);
            alert('画像アップロード時に通信エラーが発生しました');
            callback([]);
        }
    });
}*/

/**
 * フォームのリセット処理
 */
function resetForm(parent) {
    console.log("🛠 フォームリセット処理を実行");

    parent.find("input[type='text'], input[type='email'], input[type='tel'], input[type='number'], textarea").val("");
    parent.find("select").prop("selectedIndex", 0);

    uploadedImages = [];
    parent.find(".image-preview-container").empty().hide();
    jQuery('#confirm-image-container').empty().hide();
    parent.find("input[type='file']").val("");

    console.log("🛠 フォームと画像アップロードがリセットされました");
}
