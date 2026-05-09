/**
 * 家づくりプラン詳細: LightGallery 初期化
 *
 * 方針:
 * - 詳細ページ1枚ごとではなく、同じ .iez-plan-detail-board 内の
 *   .js-iez-lg-item を1グループとして lightbox 化する
 * - これにより、画像を開いたあと「次へ / 前へ」で進める
 */
(function () {
  'use strict';

  function collectPlugins() {
    var plugins = [];

    if (window.lgThumbnail) {
      plugins.push(window.lgThumbnail);
    }

    if (window.lgZoom) {
      plugins.push(window.lgZoom);
    }

    return plugins;
  }

  function initPlanLightGallery() {
    if (typeof window.lightGallery !== 'function') {
      return;
    }

    var boards = document.querySelectorAll('.iez-plan-detail-board');

    boards.forEach(function (board) {
      if (!board || board.dataset.iezLgReady === '1') {
        return;
      }

      var items = board.querySelectorAll('.js-iez-lg-item');

      if (!items.length) {
        return;
      }

      board.dataset.iezLgReady = '1';

      window.lightGallery(board, {
        selector: '.js-iez-lg-item',
        plugins: collectPlugins(),
        controls: true,
        counter: true,
        download: false,
        thumbnail: true,
        closable: true,
        escKey: true,
        hideBarsDelay: 3000,
        speed: 300,
        preload: 2,
        mobileSettings: {
          controls: true,
          showCloseIcon: true,
          download: false
        }
      });
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initPlanLightGallery);
  } else {
    initPlanLightGallery();
  }
})();
