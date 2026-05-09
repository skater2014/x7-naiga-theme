jQuery(function ($) {
  'use strict';

  var $modal = $('#chatgpt-modal');
  var $launcher = $('#chatgpt-launcher');

  if (!$modal.length || !$launcher.length) return;

  var $content = $modal.find('.chatgpt-modal-content');
  var $closeBtn = $modal.find('.chatgpt-close-btn');
  var $messages = $('#chatgpt-messages');
  var $textarea = $('#chatgpt-message');
  var $sendBtn = $('#send_message');
  var $errorBox = $('#chatgpt-error-box');
  var $intent = $('#chatgpt-intent');
  var $intentRadios = $modal.find('input[name="chatgpt_intent_choice"]');

  var isSending = false;
  var isComposing = false;
  var isOpen = false;
  var activeXhr = null;
  var loadingId = 'chatgpt-loading-message';
  var lastAutoIntent = '';

  $textarea.attr('placeholder', 'ご質問ください。');

  function escapeHtml(text) {
    return String(text || '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  function nl2br(text) {
    return String(text || '').replace(/\n/g, '<br>');
  }

  /* モーダル全体を一番下へ */
  function scrollModalToBottom() {
    if ($content[0]) {
      $content[0].scrollTop = $content[0].scrollHeight;
    }
  }

  function autoResizeTextarea() {
    if (!$textarea[0]) return;

    var el = $textarea[0];
    el.style.height = 'auto';
    el.style.height = Math.min(Math.max(el.scrollHeight, 52), 92) + 'px';
  }

  function showModal() {
    isOpen = true;
    $modal.addClass('is-open').attr('aria-hidden', 'false');
    $launcher.attr('aria-expanded', 'true').hide();
    autoResizeTextarea();
    scrollModalToBottom();
  }

  function hideModal() {
    isOpen = false;
    $modal.removeClass('is-open').attr('aria-hidden', 'true');
    $launcher.attr('aria-expanded', 'false').show();
  }

  function showError(message) {
    $errorBox.html(escapeHtml(message || '通信エラーが発生しました。')).prop('hidden', false);
    scrollModalToBottom();
  }

  function hideError() {
    $errorBox.prop('hidden', true).empty();
  }

  function appendUserMessage(message) {
    $messages.append('<div class="gpt-user-message">' + nl2br(escapeHtml(message)) + '</div>');
    scrollModalToBottom();
  }

  function appendAiMessage(html) {
    $messages.append('<div class="gpt-response">' + String(html || '') + '</div>');
    scrollModalToBottom();
  }

  function clearMessages() {
    $messages.empty();
    if ($content[0]) {
      $content[0].scrollTop = 0;
    }
  }

  function showLoading() {
    removeLoading();

    $messages.append(
      '<div id="' +
        loadingId +
        '" class="gpt-loading">' +
        '<span class="gpt-loading-text">回答を作成中...</span>' +
        '</div>',
    );

    scrollModalToBottom();
  }

  function removeLoading() {
    $('#' + loadingId).remove();
  }

  function abortActiveRequest() {
    if (activeXhr && activeXhr.readyState !== 4) {
      activeXhr.abort();
    }

    activeXhr = null;
    isSending = false;
    removeLoading();
    $sendBtn.prop('disabled', false).text('送信');
  }

  function setIntentValue(intent) {
    $intent.val(intent || '');
  }

  function getIntentValue() {
    return $.trim($intent.val() || '');
  }

  function getIntentConfig(intent) {
    var map = {
      search_land: {
        autoMessage: '土地を探しています。案内ページを教えてください。',
      },
      search_building: {
        autoMessage: '建物を探しています。案内ページを教えてください。',
      },
      assessment_land: {
        autoMessage: '土地の売却・買取・査定を相談したいです。案内ページを教えてください。',
      },
      assessment_building: {
        autoMessage: '建物の売却・買取・査定を相談したいです。案内ページを教えてください。',
      },
    };

    return map[intent] || null;
  }

  function applyIntentSelection(intent) {
    var config = getIntentConfig(intent);

    setIntentValue(intent);
    hideError();

    if (!config) {
      lastAutoIntent = '';
      clearMessages();
      return;
    }

    if (lastAutoIntent === intent && $messages.children().length) {
      return;
    }

    lastAutoIntent = intent;

    abortActiveRequest();
    clearMessages();

    sendMessage({
      message: config.autoMessage,
      silentUser: true,
    });
  }

  function buildMessageFromInput() {
    return $.trim($textarea.val() || '');
  }

  function sendMessage(options) {
    options = options || {};

    if (isSending) return;

    var message = $.trim(options.message || buildMessageFromInput());
    var silentUser = !!options.silentUser;

    if (!message) {
      $textarea.trigger('focus');
      return;
    }

    if (
      typeof naigaiAiChatAjax === 'undefined' ||
      !naigaiAiChatAjax ||
      !naigaiAiChatAjax.ajaxurl ||
      !naigaiAiChatAjax.nonce
    ) {
      showError('Ajax設定の読み込みに失敗しました。');
      return;
    }

    hideError();

    if (!silentUser) {
      appendUserMessage(message);
    }

    isSending = true;
    $sendBtn.prop('disabled', true).text('送信中...');
    showLoading();

    activeXhr = $.ajax({
      url: naigaiAiChatAjax.ajaxurl,
      type: 'POST',
      dataType: 'json',
      data: {
        action: 'naigai_ai_chat_request',
        security: naigaiAiChatAjax.nonce,
        user_message: message,
        user_intent: getIntentValue(),
      },
    })
      .done(function (response) {
        removeLoading();

        if (response && response.success && response.data && response.data.message) {
          appendAiMessage(response.data.message);

          if (!silentUser) {
            $textarea.val('');
            autoResizeTextarea();
          }

          scrollModalToBottom();
          return;
        }

        showError(
          response && response.data && response.data.message
            ? response.data.message
            : '応答の取得に失敗しました。',
        );
      })
      .fail(function (xhr, textStatus) {
        removeLoading();

        if (textStatus === 'abort') {
          return;
        }

        var errorMessage = '通信に失敗しました。';

        if (xhr && xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.message) {
          errorMessage = xhr.responseJSON.data.message;
        }

        showError(errorMessage);
      })
      .always(function () {
        activeXhr = null;
        isSending = false;
        $sendBtn.prop('disabled', false).text('送信');
        scrollModalToBottom();
      });
  }

  $launcher.on('click', function (e) {
    e.preventDefault();
    showModal();
  });

  $closeBtn.on('click', function (e) {
    e.preventDefault();
    hideModal();
  });

  $modal.on('click', function (e) {
    if (e.target === $modal[0]) {
      hideModal();
    }
  });

  $intentRadios.on('change', function () {
    applyIntentSelection($(this).val() || '');
  });

  $sendBtn.on('click', function (e) {
    e.preventDefault();
    sendMessage();
  });

  $textarea.on('input', function () {
    autoResizeTextarea();
  });

  $textarea.on('compositionstart', function () {
    isComposing = true;
  });

  $textarea.on('compositionend', function () {
    isComposing = false;
  });

  $textarea.on('keydown', function (e) {
    var composingNow = isComposing || e.isComposing || e.keyCode === 229;

    if (composingNow) return;

    if (e.key === 'Enter' && !e.shiftKey) {
      e.preventDefault();
      sendMessage();
    }
  });

  $(document).on('keydown', function (e) {
    if (e.key === 'Escape' && isOpen) {
      hideModal();
    }
  });

  hideError();
  hideModal();
  autoResizeTextarea();
});
