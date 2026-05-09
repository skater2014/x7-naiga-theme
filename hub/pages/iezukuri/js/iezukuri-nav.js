/* =========================================================
 * hub/pages/iezukuri/js/iezukuri-nav.js
 *
 * 対象:
 * - header-customhome.php
 * - /iezukuri/ 配下の注文住宅ヘッダー
 *
 * 役割:
 * - モバイルメニューの開閉だけを管理する
 * - hidden / aria-expanded / body class を同期する
 * ========================================================= */

(function () {
  'use strict';

  function qs(selector) {
    return document.querySelector(selector);
  }

  function getToggle() {
    return qs('[data-ch-menu-toggle]') || qs('.ch-site-header__toggle');
  }

  function getDrawer() {
    return qs('[data-ch-menu-drawer]') || qs('#ch-site-header-drawer') || qs('#iezukuri-menu-drawer');
  }

  function isOpen() {
    var toggle = getToggle();
    var drawer = getDrawer();

    if (!toggle || !drawer) {
      return false;
    }

    return toggle.getAttribute('aria-expanded') === 'true' || !drawer.hasAttribute('hidden');
  }

  function setOpen(open) {
    var toggle = getToggle();
    var drawer = getDrawer();

    if (!toggle || !drawer) {
      return;
    }

    toggle.setAttribute('aria-expanded', open ? 'true' : 'false');

    if (open) {
      drawer.removeAttribute('hidden');
      document.body.classList.add('ch-menu-is-open');
      document.body.classList.add('is-customhome-drawer-open');
    } else {
      drawer.setAttribute('hidden', '');
      document.body.classList.remove('ch-menu-is-open');
      document.body.classList.remove('is-customhome-drawer-open');
    }
  }

  document.addEventListener('click', function (event) {
    var toggle = event.target.closest('[data-ch-menu-toggle], .ch-site-header__toggle');
    var close = event.target.closest('[data-ch-menu-close], .ch-site-header__drawer-close');

    if (toggle) {
      event.preventDefault();
      event.stopPropagation();
      event.stopImmediatePropagation();
      setOpen(!isOpen());
      return;
    }

    if (close) {
      event.preventDefault();
      event.stopPropagation();
      event.stopImmediatePropagation();
      setOpen(false);
      return;
    }

    if (event.target.closest('.ch-site-header__drawer a')) {
      setOpen(false);
    }
  }, true);

  document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') {
      setOpen(false);
    }
  });

  window.addEventListener('resize', function () {
    if (window.innerWidth > 900) {
      setOpen(false);
    }
  });

  document.addEventListener('DOMContentLoaded', function () {
    setOpen(false);
  });
})();
