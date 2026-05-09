(function () {
  'use strict';

  function getFormValue(form, name) {
    var field = form.querySelector('[name="' + name + '"]');

    if (!field) {
      return '';
    }

    if (field.type === 'radio') {
      var checked = form.querySelector('[name="' + name + '"]:checked');
      return checked ? checked.value : '';
    }

    if (field.tagName === 'SELECT') {
      return field.options[field.selectedIndex] ? field.options[field.selectedIndex].text : '';
    }

    return field.value || '';
  }

  function showStep(id) {
    document.querySelectorAll('.customer-info-step').forEach(function (step) {
      step.hidden = true;
    });

    var target = document.getElementById(id);
    if (target) {
      target.hidden = false;
      target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  }

  function setMessage(selector, text, isError) {
    var target = document.querySelector(selector);

    if (!target) {
      return;
    }

    target.textContent = text || '';
    target.classList.toggle('is-error', !!isError);
    target.classList.toggle('is-success', !isError && !!text);
  }

  function fillConfirm(form) {
    var keys = [
      'name',
      'email',
      'phone',
      'prefecture',
      'city',
      'address_number',
      'residence_years',
      'job',
      'age',
      'gender',
      'other_address'
    ];

    keys.forEach(function (key) {
      var target = document.querySelector('[data-confirm="' + key + '"]');

      if (target) {
        target.textContent = getFormValue(form, key);
      }
    });
  }

  document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('customer-info-form');
    var openConfirm = document.querySelector('.js-customer-info-open-confirm');
    var back = document.querySelector('.js-customer-info-back');
    var reset = document.querySelector('.js-customer-info-reset');
    var send = document.querySelector('.js-customer-info-send');
    var countTarget = document.querySelector('.js-message-count');
    var message = document.getElementById('other_address');

    if (!form || !openConfirm) {
      return;
    }

    if (message && countTarget) {
      message.addEventListener('input', function () {
        countTarget.textContent = String(message.value.length);
      });
    }

    openConfirm.addEventListener('click', function () {
      setMessage('.js-customer-info-error', '', false);

      if (!form.reportValidity()) {
        return;
      }

      fillConfirm(form);
      showStep('customer-info-step-confirm');
    });

    if (back) {
      back.addEventListener('click', function () {
        setMessage('.js-customer-info-message', '', false);
        showStep('customer-info-step-input');
      });
    }

    if (reset) {
      reset.addEventListener('click', function () {
        form.reset();

        if (countTarget) {
          countTarget.textContent = '0';
        }

        setMessage('.js-customer-info-error', '', false);
        setMessage('.js-customer-info-message', '', false);
        showStep('customer-info-step-input');
      });
    }

    if (send) {
      send.addEventListener('click', function () {
        var config = window.naigaiCustomerInfoForm || {};

        if (!config.ajaxurl || !config.nonce) {
          setMessage('.js-customer-info-message', '送信設定が読み込まれていません。ページを再読み込みしてください。', true);
          return;
        }

        if (!form.reportValidity()) {
          showStep('customer-info-step-input');
          return;
        }

        send.disabled = true;
        setMessage('.js-customer-info-message', '送信中です...', false);

        var formData = new FormData(form);
        formData.append('action', 'customer_info_submit');
        formData.append('security', config.nonce);

        fetch(config.ajaxurl, {
          method: 'POST',
          credentials: 'same-origin',
          body: formData
        })
          .then(function (response) {
            return response.json();
          })
          .then(function (json) {
            if (!json || !json.success) {
              var errorMessage = json && json.data && json.data.message
                ? json.data.message
                : '送信に失敗しました。';

              throw new Error(errorMessage);
            }

            setMessage('.js-customer-info-message', '', false);
            showStep('customer-info-step-thanks');
          })
          .catch(function (error) {
            setMessage('.js-customer-info-message', error.message || '送信に失敗しました。', true);
          })
          .finally(function () {
            send.disabled = false;
          });
      });
    }
  });
})();
