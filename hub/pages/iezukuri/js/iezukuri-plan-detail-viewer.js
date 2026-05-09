/* =========================================================
 * hub/pages/iezukuri/js/iezukuri-plan-detail-viewer.js
 *
 * 役割:
 * - iez_plan 詳細ページの平面図タブ切り替え
 *
 * 画像拡大:
 * - lightGallery に任せる
 * - Lightbox2 のDOMは触らない
 * ========================================================= */

(function () {
  "use strict";

  function initPlanDrawingTabs() {
    document.addEventListener("click", function (event) {
      var tab = event.target.closest(".iez-plan-detail-board__tab");

      if (!tab) {
        return;
      }

      var root = tab.closest("[data-iez-plan-drawing-root]");
      var key = tab.getAttribute("data-iez-plan-tab");

      if (!root || !key) {
        return;
      }

      root.querySelectorAll(".iez-plan-detail-board__tab").forEach(function (button) {
        button.classList.toggle("is-active", button === tab);
      });

      root.querySelectorAll(".iez-plan-detail-board__panel").forEach(function (panel) {
        panel.classList.toggle(
          "is-active",
          panel.getAttribute("data-iez-plan-panel") === key
        );
      });
    });
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initPlanDrawingTabs, { once: true });
  } else {
    initPlanDrawingTabs();
  }
}());

/* === PLAN FEATURE MOBILE MORE START ===
 * モバイル時のみ、住宅の特徴を5件表示＋残り展開にする。
 * HTMLは触らず、既存の .iez-plan-detail-board__feature-grid にボタンを追加する。
 */
(function () {
  function setupFeatureMore() {
    var grids = document.querySelectorAll('.iez-plan-detail-board__feature-grid');

    grids.forEach(function (grid, index) {
      var cards = grid.querySelectorAll('.iez-plan-detail-board__feature-card');

      if (cards.length <= 5) {
        return;
      }

      if (grid.dataset.featureMoreReady === '1') {
        return;
      }

      grid.dataset.featureMoreReady = '1';
      grid.classList.remove('is-expanded');

      var wrap = document.createElement('div');
      wrap.className = 'iez-plan-detail-board__feature-more';

      var button = document.createElement('button');
      button.type = 'button';
      button.className = 'iez-plan-detail-board__feature-more-button';
      button.setAttribute('aria-expanded', 'false');
      button.setAttribute('aria-controls', 'iez-plan-feature-grid-' + index);
      button.textContent = 'その他の特徴を見る';

      if (!grid.id) {
        grid.id = 'iez-plan-feature-grid-' + index;
      }

      button.addEventListener('click', function () {
        var expanded = grid.classList.toggle('is-expanded');
        button.setAttribute('aria-expanded', expanded ? 'true' : 'false');
        button.textContent = expanded ? '特徴を閉じる' : 'その他の特徴を見る';
      });

      wrap.appendChild(button);
      grid.insertAdjacentElement('afterend', wrap);
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', setupFeatureMore);
  } else {
    setupFeatureMore();
  }
})();
 /* === PLAN FEATURE MOBILE MORE END === */
