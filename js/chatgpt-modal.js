jQuery(function ($) {
  var modalOpen = false;
  var modalClosed = false;
  var isSending = false;

  function reservationModalIsOpen() {
    var $modal = $('#store-reservation-modal');
    return $modal.length && ($modal.is(':visible') || $modal.hasClass('active'));
  }

  function canOpenChatgptModal() {
    return !window.__disableChatgptModalAutoOpen && !reservationModalIsOpen();
  }

  function escapeHtml(text) {
    return $('<div>').text(text || '').html();
  }

  function scrollMessagesToBottom() {
    var box = $('#chatgpt-messages').get(0);
    if (box) {
      box.scrollTop = box.scrollHeight;
    }
  }

  function appendMessage(className, html) {
    $('#chatgpt-messages').append('<div class="' + className + '">' + html + '</div>');
    scrollMessagesToBottom();
  }

  function setSendingState(flag) {
    isSending = flag;
    $('#send_message').prop('disabled', flag);
    $('#user_message').prop('disabled', flag);
  }

  function openModal() {
    if (modalOpen || modalClosed) return;
    if (!canOpenChatgptModal()) return;

    modalOpen = true;
    $('#chatgpt-modal').stop(true, true).fadeIn(250);

    var welcomeMessage = `
      <div class="gpt-response">
        こんにちは。内外土地開発（株）です。那須の不動産の物件でしたらご案内ができます。以下のリンクをご参照ください。
        <br>土地の案内ページ:
        <a href="https://naigaicorp.net/naigai-tochi" target="_blank" rel="noopener noreferrer">リンク</a>
        <br>建物の案内ページ:
        <a href="https://naigaicorp.net/naigai-construction" target="_blank" rel="noopener noreferrer">リンク</a>
        <br>ほかに何かわからないことがございましたら何でも聞いてくださいね。よろしくお願いいたします。
      </div>
    `;

    $('#chatgpt-messages').html(welcomeMessage);
    scrollMessagesToBottom();
  }

  function closeModal(markClosed) {
    $('#chatgpt-modal').stop(true, true).fadeOut(250);
    modalOpen = false;
    if (markClosed !== false) {
      modalClosed = true;
    }
  }

  function sendMessage() {
    if (isSending) return;

    var userMessage = $.trim($('#user_message').val());
    if (!userMessage) return;

    appendMessage('user-message', escapeHtml(userMessage));
    $('#user_message').val('');
    setSendingState(true);

    $.ajax({
      url: chatgptAjax.ajaxurl,
      type: 'POST',
      dataType: 'json',
      data: {
        action: 'chatgpt_request',
        security: chatgptAjax.nonce,
        user_message: userMessage
      }
    })
      .done(function (response) {
        if (response && response.success && response.data && response.data.message) {
          appendMessage('gpt-response', response.data.message);
        } else if (response && response.data && response.data.message) {
          appendMessage('gpt-response', 'エラー: ' + escapeHtml(response.data.message));
        } else {
          appendMessage('gpt-response', 'エラー: 応答を取得できませんでした。');
        }
      })
      .fail(function (xhr) {
        var msg = 'エラーが発生しました。';
        if (xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.message) {
          msg = 'エラー: ' + escapeHtml(xhr.responseJSON.data.message);
        }
        appendMessage('gpt-response', msg);
      })
      .always(function () {
        setSendingState(false);
        $('#user_message').focus();
      });
  }

  $('.close-btn').on('click', function (e) {
    e.preventDefault();
    closeModal(true);
  });

  $('#send_message').on('click', function (e) {
    e.preventDefault();
    sendMessage();
  });

  $('#user_message').on('keydown', function (event) {
    if (event.key === 'Enter' && !event.shiftKey) {
      event.preventDefault();
      sendMessage();
    }
  });

  $(window).on('scroll.chatgptModal', function () {
    if ($(window).scrollTop() > 100 && !modalOpen && !modalClosed && canOpenChatgptModal()) {
      openModal();
    }
  });

  $('#chatgpt-modal').on('click', function (event) {
    if ($(event.target).is('#chatgpt-modal')) {
      closeModal(true);
    }
  });

  $(document).on('storeReservationModal:open', function () {
    window.__disableChatgptModalAutoOpen = true;
    closeModal(false);
  });

  $(document).on('storeReservationModal:close', function () {
    window.__disableChatgptModalAutoOpen = false;
  });
});
