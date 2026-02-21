// =====================================
// Store Reservation: safe bind (二重実行防止)
// =====================================
(function ($) {
  'use strict';

  // ✅ このファイルが2回読み込まれても1回しかバインドしない
  if (window.__SR_BIND_DONE__) return;
  window.__SR_BIND_DONE__ = true;

  // ✅ 既存の click/submit を強制的に剥がす（scripts.js側の古いバインド潰し）
  $('#to-confirm-modal, #to-confirm-page, #submit-reservation, #back-to-input').off('click');
  $('#store-reservation-form').off('submit');

  // ✅ “委譲 + namespace” で再バインド（DOM差し替えにも強い）
  $(document)
    .off('click.srConfirm', '#to-confirm-modal, #to-confirm-page')
    .on('click.srConfirm', '#to-confirm-modal, #to-confirm-page', function (e) {
      e.preventDefault();
      e.stopImmediatePropagation(); // ✅ 同一要素に別ハンドラが居ても止める
      handleConfirmation(this);
    });

  $(document)
    .off('click.srBack', '#back-to-input')
    .on('click.srBack', '#back-to-input', function (e) {
      e.preventDefault();
      e.stopImmediatePropagation();
      handleBackToInput(this);
    });

  $(document)
    .off('click.srSubmit', '#submit-reservation')
    .on('click.srSubmit', '#submit-reservation', function (e) {
      e.preventDefault();
      e.stopImmediatePropagation();
      handleFormSubmit(this);
    });

  // ✅ 何かが form.submit() しても admin-ajax を叩かせない（400の原因潰し）
  $(document)
    .off('submit.srBlock', '#store-reservation-form')
    .on('submit.srBlock', '#store-reservation-form', function (e) {
      e.preventDefault();
      e.stopImmediatePropagation();
      return false;
    });
})(jQuery);

/**
 * 確認画面を表示
 */
function handleConfirmation(button) {
  const $btn = jQuery(button);
  const buttonId = $btn.attr('id');

  const parent = $btn.closest('.store-reservation-modal-content, .store-reservation-page-content');
  if (parent.length === 0) {
    console.error('handleConfirmation: parentが見つからない');
    return;
  }

  // ✅ バリデーション（validateFormは selector文字列のまま使う）
  const selector = parent.hasClass('store-reservation-modal-content')
    ? '.store-reservation-modal-content'
    : '.store-reservation-page-content';

  if (!validateForm(selector)) return;

  // フォームデータ取得＆確認画面更新
  const formData = getFormData(parent);
  updateConfirmationData(formData);

  // 表示
  if (buttonId === 'to-confirm-modal') {
    showConfirmationScreen(formData, '.store-reservation-modal-content');
  } else {
    showConfirmationScreen(formData, '.store-reservation-page-content');
  }
}

/**
 * 戻るボタン処理
 */
function handleBackToInput(button) {
  const parent = jQuery(button).closest(
    '.store-reservation-modal-content, .store-reservation-page-content',
  );
  parent.find('#step-confirm').hide();
  parent.find('#step-input').show();
}

/**
 * フォームをリセットする関数
 */
function resetForm(parent) {
  console.log('フォームをリセットします');

  parent
    .find(
      'input[type="text"], input[type="email"], input[type="tel"], input[type="number"], textarea',
    )
    .val('');
  parent.find('input[type="file"]').val('');
  parent.find('input[type="checkbox"], input[type="radio"]').prop('checked', false);

  parent.find('select').each(function () {
    const defaultVal =
      jQuery(this).find('option[selected]').val() || jQuery(this).find('option:first').val();
    jQuery(this).val(defaultVal).trigger('change');
  });

  uploadedImages = [];
  displayUploadedImages(uploadedImages, '.image-preview-container');
  displayUploadedImages(uploadedImages, '#confirm-image-container');

  const formData = getFormData(parent);
  updateConfirmationData(formData);

  console.log('フォームリセット完了');
}
