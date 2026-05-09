/**
 * hub/pages/iezukuri/admin/assets/js/hero-metabox.js
 *
 * 役割:
 * - 固定ページ編集画面の Hero メディア選択を動かす。
 * - Hero画像 / Swiper複数画像 / MP4 の選択とプレビューを担当する。
 */
(function () {
  "use strict";

  function getIds(value) {
    return String(value || "")
      .split(",")
      .map(function (id) { return id.trim(); })
      .filter(Boolean);
  }

  function ensurePreview(input) {
    var key = input.id || input.name;
    var preview = document.querySelector('[data-iez-preview-for="' + key + '"]');

    if (!preview) {
      preview = document.createElement("div");
      preview.className = "naigai-iez-media-preview";
      preview.setAttribute("data-iez-preview-for", key);
      input.insertAdjacentElement("afterend", preview);
    }

    preview.style.display = "flex";
    preview.style.flexWrap = "wrap";
    preview.style.gap = "10px";
    preview.style.marginTop = "10px";

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
    item.style.width = "120px";
    item.style.border = "1px solid #ccd0d4";
    item.style.borderRadius = "6px";
    item.style.overflow = "hidden";
    item.style.background = "#fff";

    var img = document.createElement("img");
    img.src = thumb;
    img.alt = "";
    img.style.display = "block";
    img.style.width = "100%";
    img.style.height = "80px";
    img.style.objectFit = "cover";

    var cap = document.createElement("div");
    cap.textContent = "ID: " + data.id;
    cap.style.fontSize = "11px";
    cap.style.padding = "4px 6px";
    cap.style.color = "#50575e";

    item.appendChild(img);
    item.appendChild(cap);
    preview.appendChild(item);
  }

  function renderVideo(preview, attachment) {
    var data = attachment.attributes || attachment;
    var url = data.url || "";

    if (!url) return;

    var item = document.createElement("div");
    item.style.maxWidth = "360px";

    var video = document.createElement("video");
    video.src = url;
    video.controls = true;
    video.muted = true;
    video.style.width = "100%";
    video.style.borderRadius = "8px";
    video.style.display = "block";

    var cap = document.createElement("div");
    cap.textContent = "ID: " + data.id;
    cap.style.fontSize = "12px";
    cap.style.marginTop = "4px";
    cap.style.color = "#50575e";

    item.appendChild(video);
    item.appendChild(cap);
    preview.appendChild(item);
  }

  function renderExisting(input, type) {
    if (!window.wp || !wp.media || !wp.media.attachment) return;

    var ids = getIds(input.value);
    var preview = ensurePreview(input);

    preview.innerHTML = "";

    ids.forEach(function (id) {
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

  function openFrame(button) {
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
      var ids = [];
      var preview = ensurePreview(input);

      preview.innerHTML = "";

      frame.state().get("selection").each(function (attachment) {
        ids.push(attachment.id);

        if (type === "video") {
          renderVideo(preview, attachment);
        } else {
          renderImage(preview, attachment);
        }
      });

      input.value = ids.join(",");
      input.dispatchEvent(new Event("change", { bubbles: true }));
    });

    frame.open();
  }

  document.addEventListener("click", function (event) {
    var button = event.target.closest("[data-iez-media-target]");
    if (!button) return;

    event.preventDefault();
    openFrame(button);
  });

  document.addEventListener("DOMContentLoaded", function () {
    var gallery = document.getElementById("_ch_hero_gallery_ids");
    if (gallery) {
      renderExisting(gallery, "image");
    }

    var video = document.getElementById("_ch_hero_video_mp4_id");
    if (video) {
      renderExisting(video, "video");
    }
  });
})();
