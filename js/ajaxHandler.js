/**
 * 予約送信 & フォームリセット
 * フォーム送信時にデータを取得し、AJAXでサーバーに送信する。
 * 送信完了後、5秒後にフォームをリセット。
 */
function handleFormSubmit(button) {
  // **親要素（フォームコンテナ）を取得**
  const parent = jQuery(button).closest(
    '.store-reservation-modal-content, .store-reservation-page-content',
  );

  // **親要素が見つからない場合はエラーを出して終了**
  if (parent.length === 0) {
    console.error('親要素が取得できませんでした');
    return;
  }

  // **入力画面と確認画面を非表示にし、完了画面を表示**
  parent.find('#step-input, #step-confirm').hide();
  parent.find('#step-complete').show();

  // **フォームデータを取得**
  const formData = getFormData(parent);
  console.log('フォームデータ送信: ', formData);

  // **AJAXでフォームデータを送信**
  sendReservationData(formData, parent);

  // **5秒後にフォームをリセット**
  setTimeout(function () {
    console.log('AJAX 完了後、フォームリセットを実行');
    resetForm(parent); // ✅ ここで画像もリセットする

    // **固定ページの場合はフォームを再表示**
    if (parent.hasClass('store-reservation-page-content')) {
      console.log('固定ページ: フォームを再表示');
      parent.find('#step-complete').hide();
      parent.find('#step-input').css({ display: 'block', visibility: 'visible' }).show();
    }

    // **モーダルの場合はモーダルを閉じる**
    if (parent.hasClass('store-reservation-modal-content')) {
      console.log('モーダルを閉じる');
      parent.closest('.store-reservation-modal').fadeOut(300, function () {
        jQuery(this).removeClass('active');
      });
    }

    console.log('フォームリセット完了');
  }, 5000);
}

/**
 * フォームのリセット処理（画像アップロードのリセットを含む）
 * @param {jQuery} parent - リセット対象のフォームの親要素
 */
function resetForm(parent) {
  console.log('フォームリセット処理を実行');

  // **✅ 全ての入力フィールドをリセット**
  parent
    .find(
      "input[type='text'], input[type='email'], input[type='tel'], input[type='number'], textarea",
    )
    .val(''); // 🔹 `input[type='number']` もリセット
  parent.find('select').prop('selectedIndex', 0); // 🔹 セレクトボックスをリセット

  // 来店日時 のリセット
  parent.find('#visit-date').val('');

  // **✅ 動的に追加されたフィールドもリセット**
  parent.find('#dynamic-fields-container input').val(''); // 🔹 追加された `input` をクリア
  parent.find('#dynamic-fields-container select').prop('selectedIndex', 0); // 🔹 追加された `select` をクリア

  // **✅ 画像アップロードのリセット**
  console.log('アップロード画像リセットを実行');
  uploadedImages = []; // **内部の画像リストをリセット**
  parent.find('#uploaded-image-preview').empty(); // **プレビューを削除**
  parent.find("input[type='file']").val(''); // **ファイル入力をリセット**
  parent.find('.image-preview-container').empty().hide(); // **画像プレビューをリセット**
}

/**
 * 売却査定フォームデータを送信（AJAX）
 * サーバーにフォームデータを送信し、処理結果を取得する。
 */
function sendReservationData(formData, parent) {
  console.log('AJAX送信開始 (変換前): ', formData);

  // 🔹 日本語変換 html 書かれている　option 選択する英語をgetMappedValue日本語に変換している。
  // 売却査定フォーム内にある選択項目を英語を日本語に変換
  formData.property_status = getMappedValue(propertyStatusMap, formData.property_status); // 物件状況 マッピング
  //formData.sale_reason      = getMappedValue(saleReasonMap, formData.sale_reason); // 査定を希望する理由 マッピング
  formData.sale_period = getMappedValue(salePeriodMap, formData.sale_period); //売却希望時期 マッピング
  formData.valuation_method = getMappedValue(valuationMethodMap, formData.valuation_method); // 査定方法 マッピング
  // 🔹 動的フィールド　php側で単位・平米などをつけている。
  // 🔧 修正後：送信時には値のみ（確認画面ではJSで単位つける）
  formData.land_area = formData.land_area || '未設定';
  formData.building_area = formData.building_area || '未設定';
  formData.year_built = formData.year_built || '未設定';
  formData.rental_income = formData.rental_income || '未設定';
  // 🔹 バリデーション
  formData.sale_reason_text = formData.sale_reason_text || '';
  if (formData.sale_reason_text.trim() !== '' && containsHTML(formData.sale_reason_text)) {
    alert('売却理由にHTMLタグは使用できません。');
    return;
  }
  if (formData.sale_reason_text.trim() !== '' && formData.sale_reason_text.length > 500) {
    alert('売却理由は500文字以内で入力してください。');
    return;
  }

  // 🔹 FormDataで画像とJSONデータを一緒に送信
  const payload = new FormData();
  payload.append('action', 'store_reservation_submit');
  payload.append('store_reservation_nonce', customAjax.nonce);
  payload.append('data', JSON.stringify(formData));

  // 🔹 画像を送信（File オブジェクトのみ追加）
  if (Array.isArray(uploadedImages)) {
    uploadedImages.forEach((img, i) => {
      if (img instanceof File) {
        payload.append('images[]', img); // 第3引数（img.name）は不要
      }
    });
  }

  jQuery.ajax({
    url: customAjax.ajaxurl,
    type: 'POST',
    data: payload,
    contentType: false,
    processData: false,
    success: function (response) {
      if (response.success) {
        console.log('✅ 予約送信成功: ', response);
      } else {
        alert('送信に失敗しました：' + (response.data.message || '原因不明'));
      }
    },
    error: function (xhr, status, error) {
      console.error('❌ 通信エラー:', xhr.responseText);
      alert('通信エラーが発生しました。再度お試しください。');
    },
  });
}
