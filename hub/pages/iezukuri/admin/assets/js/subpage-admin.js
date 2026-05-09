/**
 * hub/pages/iezukuri/admin/assets/js/subpage-admin.js
 *
 * 家づくり固定ページ 管理画面JS
 *
 * 役割:
 * - 画像選択
 * - Swiper複数画像選択
 * - MP4選択
 * - プレビュー表示
 */
(function () {
  "use strict";

  function splitIds(value) {
    return String(value || "")
      .split(",")
      .map(function (id) { return id.trim(); })
      .filter(Boolean);
  }

  function getPreview(input) {
    var key = input.id || input.name;
    var preview = document.querySelector('[data-iez-preview-for="' + key + '"]');

    if (!preview) {
      preview = document.createElement("div");
      preview.className = "naigai-iez-media-preview";
      preview.setAttribute("data-iez-preview-for", key);
      input.insertAdjacentElement("afterend", preview);
    }

    return preview;
  }

  function renderImage(preview, attachment) {
    var data = attachment.attributes || attachment;
    var url = data.url || "";
    var thumb = url;

    if (data.sizes && data.sizes.thumbnail && data.sizes.thumbnail.url) {
      thumb = data.sizes.thumbnail.url;
    }

    if (!thumb) return;

    var item = document.createElement("div");
    item.className = "naigai-iez-media-preview__item";

    var img = document.createElement("img");
    img.src = thumb;
    img.alt = "";
    img.className = "naigai-iez-media-preview__image";

    var cap = document.createElement("div");
    cap.className = "naigai-iez-media-preview__caption";
    cap.textContent = "ID: " + data.id;

    item.appendChild(img);
    item.appendChild(cap);
    preview.appendChild(item);
  }

  function renderVideo(preview, attachment) {
    var data = attachment.attributes || attachment;
    var url = data.url || "";

    if (!url) return;

    var item = document.createElement("div");
    item.className = "naigai-iez-media-preview__video";

    var video = document.createElement("video");
    video.src = url;
    video.controls = true;
    video.muted = true;

    var cap = document.createElement("div");
    cap.className = "naigai-iez-media-preview__caption";
    cap.textContent = "ID: " + data.id;

    item.appendChild(video);
    item.appendChild(cap);
    preview.appendChild(item);
  }

  function guessType(input) {
    var id = input.id || input.name || "";
    return id.indexOf("video") !== -1 || id.indexOf("mp4") !== -1 ? "video" : "image";
  }

  function renderExisting(input) {
    if (!window.wp || !wp.media || !wp.media.attachment) return;

    var preview = getPreview(input);
    var type = guessType(input);

    preview.innerHTML = "";

    splitIds(input.value).forEach(function (id) {
      var attachment = wp.media.attachment(id);

      attachment.fetch().then(function () {
        if (type === "video") {
          renderVideo(preview, attachment);
        } else {
          renderImage(preview, attachment);
        }
      });
    });
  }

  function openMedia(button) {
    var target = button.getAttribute("data-iez-media-target");
    var input = document.querySelector(target);

    if (!input) {
      alert("保存先inputが見つかりません: " + target);
      return;
    }

    if (!window.wp || !wp.media) {
      alert("wp.media が読み込まれていません。");
      return;
    }

    var type = button.getAttribute("data-iez-media-type") || "image";
    var multiple = button.getAttribute("data-iez-media-multiple") === "1";

    var frame = wp.media({
      title: button.getAttribute("data-iez-media-title") || "メディアを選択",
      button: {
        text: button.getAttribute("data-iez-media-button") || "このメディアを使う"
      },
      library: {
        type: type
      },
      multiple: multiple
    });

    frame.on("select", function () {
      var selectedIds = [];
      var preview = getPreview(input);

      preview.innerHTML = "";

      frame.state().get("selection").each(function (attachment) {
        selectedIds.push(attachment.id);

        if (type === "video") {
          renderVideo(preview, attachment);
        } else {
          renderImage(preview, attachment);
        }
      });

      input.value = selectedIds.join(",");
      input.dispatchEvent(new Event("change", { bubbles: true }));
    });

    frame.open();
  }

  document.addEventListener("click", function (event) {
    var button = event.target.closest("[data-iez-media-target]");
    if (!button) return;

    event.preventDefault();
    openMedia(button);
  });


  document.addEventListener("click", function (event) {
    var clearButton = event.target.closest("[data-iez-media-clear]");
    if (!clearButton) return;

    event.preventDefault();

    var target = clearButton.getAttribute("data-iez-media-clear");
    var input = document.querySelector(target);

    if (!input) {
      alert("クリア対象inputが見つかりません: " + target);
      return;
    }

    input.value = "";
    input.dispatchEvent(new Event("change", { bubbles: true }));

    var key = input.id || input.name;
    var preview = document.querySelector('[data-iez-preview-for="' + key + '"]');

    if (preview) {
      preview.innerHTML = "";
    }
  });

  document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll("[data-iez-preview-for]").forEach(function (preview) {
      var key = preview.getAttribute("data-iez-preview-for");
      var input = document.getElementById(key);

      if (input) {
        renderExisting(input);
      }
    });
  });
})();
