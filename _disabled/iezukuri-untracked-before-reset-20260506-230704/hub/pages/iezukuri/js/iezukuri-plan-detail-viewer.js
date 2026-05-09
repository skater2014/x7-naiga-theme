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
