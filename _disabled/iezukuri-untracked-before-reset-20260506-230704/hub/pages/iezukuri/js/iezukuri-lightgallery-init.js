/* =========================================================
 * hub/pages/iezukuri/js/iezukuri-lightgallery-init.js
 *
 * 役割:
 * - iez_plan 詳細ページの画像リンクを lightGallery 化
 * - thumbnail / zoom を有効化
 * ========================================================= */

(function () {
  "use strict";

  function getLightGallery() {
    return window.lightGallery || window.LightGallery || null;
  }

  function initIezPlanLightGallery() {
    var lightGallery = getLightGallery();

    if (typeof lightGallery !== "function") {
      return;
    }

    document.querySelectorAll(".iez-plan-detail-board").forEach(function (board) {
      if (board.dataset.iezLgReady === "1") {
        return;
      }

      if (!board.querySelector("a.js-iez-lg-item")) {
        return;
      }

      board.dataset.iezLgReady = "1";

      lightGallery(board, {
        selector: "a.js-iez-lg-item",
        plugins: [
          window.lgThumbnail,
          window.lgZoom
        ].filter(Boolean),

        thumbnail: true,
        animateThumb: true,
        showThumbByDefault: true,

        zoom: true,
        actualSize: false,

        download: false,
        counter: true,
        controls: true,
        closable: true,
        escKey: true,
        keyPress: true,

        speed: 280,
        licenseKey: "0000-0000-000-0000",

        mobileSettings: {
          controls: true,
          showCloseIcon: true,
          download: false
        }
      });
    });
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initIezPlanLightGallery, { once: true });
  } else {
    initIezPlanLightGallery();
  }
}());
