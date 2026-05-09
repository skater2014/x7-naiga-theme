document.addEventListener('DOMContentLoaded', function () {
  'use strict';

  function ensureModal() {
    var modal = document.getElementById('googleMapModal');

    if (modal) return modal;

    modal = document.createElement('div');
    modal.id = 'googleMapModal';
    modal.className = 'google-map-modal';
    modal.setAttribute('data-map-modal', '');
    modal.setAttribute('aria-hidden', 'true');
    modal.style.display = 'none';

    modal.innerHTML =
      '<div class="google-map-modal-content">' +
        '<button type="button" class="google-map-modal-close" id="closeModal" data-map-close aria-label="閉じる">&times;</button>' +
        '<div class="google-map-modal-header">' +
          '<h2 class="google-map-modal-title js-google-map-modal-title"></h2>' +
        '</div>' +
        '<div class="google-map-modal-body js-google-map-modal-body"></div>' +
      '</div>';

    document.body.appendChild(modal);
    return modal;
  }

  function openModal(modal) {
    if (!modal) return;
    modal.style.display = 'block';
    modal.setAttribute('aria-hidden', 'false');
    document.body.classList.add('is-map-modal-open');
  }

  function closeModal(modal) {
    if (!modal) return;
    modal.style.display = 'none';
    modal.setAttribute('aria-hidden', 'true');
    document.body.classList.remove('is-map-modal-open');
  }

  function decodeBase64(value) {
    try {
      return atob(value);
    } catch (e) {
      console.error('data-map-html の decode に失敗:', e);
      return '';
    }
  }

  function resolveModalFromTrigger(trigger) {
    if (!trigger) return null;

    var target = trigger.getAttribute('data-map-target');
    if (target) {
      return document.querySelector(target);
    }

    return (
      document.getElementById('googleMapModal') ||
      document.querySelector('[data-map-modal]') ||
      document.querySelector('.google-map-modal')
    );
  }

  document.addEventListener('click', function (event) {
    var trigger = event.target.closest(
      '#openMapLink, #iconLocation, .icon-location, .google-location-link, .google-location-trigger, [data-map-open]'
    );
    if (!trigger) return;

    event.preventDefault();

    var modal = resolveModalFromTrigger(trigger) || ensureModal();
    if (!modal) return;

    var encodedHtml = trigger.getAttribute('data-map-html');
    var mapTitle = trigger.getAttribute('data-map-title') || 'Googleマップ';

    if (encodedHtml) {
      var body = modal.querySelector('.js-google-map-modal-body');
      var title = modal.querySelector('.js-google-map-modal-title');
      var decoded = decodeBase64(encodedHtml);

      if (body) body.innerHTML = decoded;
      if (title) title.textContent = mapTitle;
    }

    openModal(modal);
  });

  document.addEventListener('click', function (event) {
    var closeBtn = event.target.closest(
      '#closeModal, .google-map-modal-close, [data-map-close]'
    );
    if (!closeBtn) return;

    event.preventDefault();

    var modal =
      closeBtn.closest('[data-map-modal]') ||
      closeBtn.closest('.google-map-modal') ||
      document.getElementById('googleMapModal');

    closeModal(modal);
  });

  document.addEventListener('click', function (event) {
    var modal =
      event.target.matches('[data-map-modal], .google-map-modal')
        ? event.target
        : null;

    if (modal && event.target === modal) {
      closeModal(modal);
    }
  });

  document.addEventListener('keydown', function (event) {
    if (event.key !== 'Escape') return;

    document
      .querySelectorAll('[data-map-modal], .google-map-modal')
      .forEach(function (modal) {
        closeModal(modal);
      });
  });
});
