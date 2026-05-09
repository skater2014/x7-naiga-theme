// =====================================
// Store Reservation: safe bind (二重実行防止 / デバッグしやすい版)
// =====================================
(function ($) {
  'use strict';

  if (window.__SR_BIND_DONE__) return;
  window.__SR_BIND_DONE__ = true;

  // 既存ハンドラを剥がす（旧 scripts.js 対策）
  $('#to-confirm-modal, #to-confirm-page, #submit-reservation, #back-to-input').off('click');
  $('#store-reservation-form').off('submit');

  // 共通: 例外を潰さず見える化
  function safeRun(label, fn) {
    try {
      fn();
    } catch (err) {
      console.error(`[${label}]`, err);
      if (err && err.stack) console.error(err.stack);

      const msg = (err && (err.message || String(err))) || 'unknown error';
      alert(`${label} でJSエラー: ${msg}`);
    }
  }

  // ✅ 確認ボタン
  $(document)
    .off('click.srConfirm', '#to-confirm-modal, #to-confirm-page')
    .on('click.srConfirm', '#to-confirm-modal, #to-confirm-page', function (e) {
      e.preventDefault();
      // e.stopImmediatePropagation(); // ← まず外す（無反応化を防ぐ）
      // e.stopPropagation();          // 必要ならこっちにする

      console.log('[srConfirm] click', this);
      safeRun('確認ボタン処理', () => handleConfirmation(this));
    });

  // ✅ 戻るボタン
  $(document)
    .off('click.srBack', '#back-to-input')
    .on('click.srBack', '#back-to-input', function (e) {
      e.preventDefault();
      // e.stopImmediatePropagation(); // ← まず外す

      console.log('[srBack] click', this);
      safeRun('戻るボタン処理', () => handleBackToInput(this));
    });

  // ✅ 送信ボタン
  $(document)
    .off('click.srSubmit', '#submit-reservation')
    .on('click.srSubmit', '#submit-reservation', function (e) {
      e.preventDefault();
      // e.stopImmediatePropagation(); // ← まず外す

      console.log('[srSubmit] click', this);
      safeRun('送信ボタン処理', () => handleFormSubmit(this));
    });

  // ✅ form の通常 submit を止める（AJAX運用時）
  // ※ デバッグ中に submit 挙動を見たいなら、このブロックを一時コメントアウト
  $(document)
    .off('submit.srBlock', '#store-reservation-form')
    .on('submit.srBlock', '#store-reservation-form', function (e) {
      e.preventDefault();
      // e.stopImmediatePropagation(); // ← まず外す
      console.log('[srBlock] native submit blocked');
      return false;
    });
})(jQuery);

/**
 * 確認画面を表示
 */
function handleConfirmation(button) {
  const $btn = jQuery(button);
  const buttonId = $btn.attr('id');

  const $parent = $btn.closest('.store-reservation-modal-content, .store-reservation-page-content');
  if (!$parent.length) {
    throw new Error('handleConfirmation: parent not found');
  }

  const selector = $parent.hasClass('store-reservation-modal-content')
    ? '.store-reservation-modal-content'
    : '.store-reservation-page-content';

  if (typeof validateForm !== 'function') throw new Error('validateForm is not defined');
  if (typeof getFormData !== 'function') throw new Error('getFormData is not defined');
  if (typeof showConfirmationScreen !== 'function')
    throw new Error('showConfirmationScreen is not defined');

  if (!validateForm($parent)) return;
  const formData = getFormData($parent);
  console.log('[HC] formData =', formData);


  try {
    console.log('[HC] before showConfirmationScreen', {
      buttonId,
      selector,
      parentLen: $parent.length,
      stepInput: $parent.find('#step-input').length,
      stepConfirm: $parent.find('#step-confirm').length,
      formData,
    });

    if (buttonId === 'to-confirm-modal') {
      showConfirmationScreen(formData, '.store-reservation-modal-content');
    } else {
      showConfirmationScreen(formData, '.store-reservation-page-content');
    }

    console.log('[HC] showConfirmationScreen OK');
  } catch (e) {
    console.error('[HC] showConfirmationScreen error', e);
    if (e && e.stack) console.error(e.stack);

    // デバッグ中フォールバック（画面だけ進める）
    $parent.find('#step-input').hide();
    $parent.find('#step-confirm').show();

    throw e;
  }
}

/**
 * 戻るボタン処理
 */
function handleBackToInput(button) {
  const parent = jQuery(button).closest(
    '.store-reservation-modal-content, .store-reservation-page-content',
  );

  if (!parent.length) {
    console.error('handleBackToInput: parentが見つからない');
    return;
  }

  parent.find('#step-confirm').hide();
  parent.find('#step-input').show();
  console.log('handleBackToInput: OK');
}

// ⚠ resetForm は ajaxHandler.js 側を使う（このファイルでは定義しない）
