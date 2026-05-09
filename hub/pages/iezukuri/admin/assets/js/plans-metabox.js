/* =========================================================
 * hub/pages/iezukuri/admin/assets/js/plans-metabox.js
 *
 * 役割:
 * - iez_plan 編集画面の画像/PDF選択
 * - 画像IDごとの表示名を _ch_plan_image_labels_json に保存
 *
 * 対応HTML:
 * - 通常画像:
 *   data-iez-plan-image-box
 *   data-iez-plan-preview
 *   data-iez-plan-image-id
 *   data-iez-plan-select
 *   data-iez-plan-remove
 *
 * - 複数内装写真:
 *   data-iez-plan-gallery-box
 *   data-iez-plan-gallery-input
 *   data-iez-plan-gallery-preview
 *   data-iez-plan-gallery-select
 *   data-iez-plan-gallery-clear
 *
 * 方針:
 * - 画像選択は multiple:true
 * - 通常画像欄で複数選択した場合、1枚目はその欄、2枚目以降は内装写真へ追加
 * - PDFだけ multiple:false
 * ========================================================= */

(function () {
  "use strict";

  window.NaigaiIezPlansMetaboxReady = true;

  var labels = Object.assign({}, window.NaigaiIezPlanImageLabels || {});

  function closest(target, selector) {
    return target ? target.closest(selector) : null;
  }

  function isFormControl(target) {
    return !!closest(target, "input, textarea, select, button, a, label");
  }

  function escapeHtml(value) {
    return String(value || "")
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;");
  }

  function getTitleFromAttachment(attachment, fallback) {
    return attachment.title || attachment.caption || attachment.alt || attachment.filename || fallback || "";
  }

  function getAttachmentThumb(attachment) {
    if (!attachment) {
      return "";
    }

    if (attachment.sizes && attachment.sizes.thumbnail && attachment.sizes.thumbnail.url) {
      return attachment.sizes.thumbnail.url;
    }

    if (attachment.sizes && attachment.sizes.medium && attachment.sizes.medium.url) {
      return attachment.sizes.medium.url;
    }

    return attachment.url || "";
  }

  function getLabelsInput() {
    var input = document.querySelector("[data-iez-plan-image-labels-json]");

    if (!input) {
      var form = document.querySelector("form#post") || document.querySelector("form");

      if (!form) {
        return null;
      }

      input = document.createElement("input");
      input.type = "hidden";
      input.name = "_ch_plan_image_labels_json";
      input.setAttribute("data-iez-plan-image-labels-json", "1");
      form.appendChild(input);
    }

    return input;
  }

  function syncLabelsInput() {
    var input = getLabelsInput();

    if (input) {
      input.value = JSON.stringify(labels);
    }
  }

  function setLabel(id, label) {
    id = String(id || "").trim();
    label = String(label || "").trim();

    if (!id) {
      return;
    }

    if (label) {
      labels[id] = label;
    } else {
      delete labels[id];
    }

    syncLabelsInput();
  }

  function makeLabelField(id, fallbackLabel) {
    id = String(id || "").trim();

    if (!id) {
      return "";
    }

    var value = labels[id] || fallbackLabel || "";

    return '' +
      '<label class="iez-plan-admin__image-label" data-iez-plan-image-label="' + escapeHtml(id) + '">' +
        '<span>画像名</span>' +
        '<input type="text" value="' + escapeHtml(value) + '" data-iez-plan-image-label-input data-id="' + escapeHtml(id) + '" placeholder="例：LDK 16.9帖 / 2LDK 平面図">' +
      '</label>';
  }

  function getGalleryParts() {
    var box = document.querySelector("[data-iez-plan-gallery-box]");

    if (!box) {
      return null;
    }

    return {
      box: box,
      input: box.querySelector("[data-iez-plan-gallery-input]"),
      preview: box.querySelector("[data-iez-plan-gallery-preview]")
    };
  }

  function getExistingGalleryIds(input) {
    if (!input || !input.value) {
      return [];
    }

    return input.value.split(",").map(function (id) {
      return id.trim();
    }).filter(Boolean);
  }

  function uniqueIds(ids) {
    var seen = {};
    var result = [];

    ids.forEach(function (id) {
      id = String(id || "").trim();

      if (!id || seen[id]) {
        return;
      }

      seen[id] = true;
      result.push(id);
    });

    return result;
  }

  function removeGalleryEmpty(preview) {
    var empty = preview ? preview.querySelector(".iez-plan-admin__empty") : null;

    if (empty) {
      empty.remove();
    }
  }

  function appendGalleryAttachments(attachments) {
    var parts = getGalleryParts();

    if (!parts || !parts.input || !parts.preview || !attachments.length) {
      return;
    }

    var ids = getExistingGalleryIds(parts.input);
    var map = {};

    ids.forEach(function (id) {
      map[id] = true;
    });

    removeGalleryEmpty(parts.preview);

    attachments.forEach(function (attachment) {
      var id = String(attachment.id || "");

      if (!id || map[id]) {
        return;
      }

      map[id] = true;
      ids.push(id);

      var thumb = getAttachmentThumb(attachment);
      var label = getTitleFromAttachment(attachment, "内装写真");

      setLabel(id, labels[id] || label);

      parts.preview.insertAdjacentHTML(
        "beforeend",
        '<span class="iez-plan-gallery-admin__item">' +
          '<img src="' + escapeHtml(thumb) + '" alt="" data-id="' + escapeHtml(id) + '">' +
          makeLabelField(id, label) +
        '</span>'
      );
    });

    parts.input.value = uniqueIds(ids).join(",");
    syncLabelsInput();
  }

  function setSingleImage(box, attachment) {
    var input = box.querySelector("[data-iez-plan-image-id]");
    var preview = box.querySelector("[data-iez-plan-preview]");

    if (!input || !preview || !attachment) {
      return;
    }

    var id = String(attachment.id || "");
    var thumb = getAttachmentThumb(attachment);
    var label = getTitleFromAttachment(attachment, "画像");

    input.value = id;
    setLabel(id, labels[id] || label);

    preview.innerHTML =
      '<img src="' + escapeHtml(thumb) + '" alt="" data-id="' + escapeHtml(id) + '">' +
      makeLabelField(id, label);

    syncLabelsInput();
  }

  function clearSingleImage(box) {
    var input = box.querySelector("[data-iez-plan-image-id]");
    var preview = box.querySelector("[data-iez-plan-preview]");

    if (input) {
      input.value = "";
    }

    if (preview) {
      preview.innerHTML = '<span class="iez-plan-admin__empty">画像未設定</span>';
    }

    syncLabelsInput();
  }

  function openImageFrame(onSelect) {
    var frame = wp.media({
      title: "画像を選択",
      library: {
        type: "image"
      },
      multiple: true
    });

    frame.on("select", function () {
      var attachments = [];

      frame.state().get("selection").each(function (attachment) {
        attachments.push(attachment.toJSON());
      });

      onSelect(attachments);
    });

    frame.open();
  }

  function openSingleImagePicker(box) {
    openImageFrame(function (attachments) {
      if (!attachments.length) {
        return;
      }

      setSingleImage(box, attachments[0]);

      if (attachments.length > 1) {
        appendGalleryAttachments(attachments.slice(1));
      }
    });
  }

  function openGalleryPicker() {
    openImageFrame(function (attachments) {
      appendGalleryAttachments(attachments);
    });
  }

  function ensureSinglePreviewLabel(box) {
    var input = box.querySelector("[data-iez-plan-image-id]");
    var preview = box.querySelector("[data-iez-plan-preview]");

    if (!input || !preview || !input.value || input.value === "0") {
      return;
    }

    var id = String(input.value).trim();

    if (!id || preview.querySelector('[data-iez-plan-image-label="' + id + '"]')) {
      return;
    }

    var img = preview.querySelector("img");

    if (img) {
      img.setAttribute("data-id", id);
    }

    preview.insertAdjacentHTML("beforeend", makeLabelField(id, ""));
  }

  function ensureGalleryPreviewLabels() {
    var parts = getGalleryParts();

    if (!parts || !parts.input || !parts.preview) {
      return;
    }

    var ids = getExistingGalleryIds(parts.input);
    var imgs = Array.prototype.slice.call(parts.preview.querySelectorAll("img"));

    ids.forEach(function (id, index) {
      var img = parts.preview.querySelector('img[data-id="' + id + '"]') || imgs[index];

      if (!img) {
        return;
      }

      img.setAttribute("data-id", id);

      var parent = img.parentElement;

      if (!parent) {
        return;
      }

      if (!parent.classList.contains("iez-plan-gallery-admin__item")) {
        var wrapper = document.createElement("span");
        wrapper.className = "iez-plan-gallery-admin__item";
        parent.insertBefore(wrapper, img);
        wrapper.appendChild(img);
        parent = wrapper;
      }

      if (!parent.querySelector('[data-iez-plan-image-label="' + id + '"]')) {
        parent.insertAdjacentHTML("beforeend", makeLabelField(id, ""));
      }
    });
  }

  function ensureExistingLabelInputs() {
    document.querySelectorAll("[data-iez-plan-image-box]").forEach(function (box) {
      ensureSinglePreviewLabel(box);
    });

    ensureGalleryPreviewLabels();
    syncLabelsInput();
  }

  document.addEventListener("input", function (event) {
    var input = event.target.closest("[data-iez-plan-image-label-input]");

    if (!input) {
      return;
    }

    setLabel(input.getAttribute("data-id"), input.value);
  });

  document.addEventListener("click", function (event) {
    var selectButton = closest(event.target, "[data-iez-plan-select]");
    var removeButton = closest(event.target, "[data-iez-plan-remove]");
    var imagePreview = closest(event.target, "[data-iez-plan-preview]");
    var gallerySelect = closest(event.target, "[data-iez-plan-gallery-select]");
    var galleryClear = closest(event.target, "[data-iez-plan-gallery-clear]");
    var galleryPreview = closest(event.target, "[data-iez-plan-gallery-preview]");
    var pdfSelect = closest(event.target, "[data-iez-plan-pdf-select]");
    var pdfClear = closest(event.target, "[data-iez-plan-pdf-clear]");

    if (selectButton) {
      event.preventDefault();

      var imageBoxFromButton = closest(selectButton, "[data-iez-plan-image-box]");

      if (imageBoxFromButton && typeof wp !== "undefined" && wp.media) {
        openSingleImagePicker(imageBoxFromButton);
      }

      return;
    }

    if (imagePreview && !isFormControl(event.target)) {
      event.preventDefault();

      var imageBoxFromPreview = closest(imagePreview, "[data-iez-plan-image-box]");

      if (imageBoxFromPreview && typeof wp !== "undefined" && wp.media) {
        openSingleImagePicker(imageBoxFromPreview);
      }

      return;
    }

    if (removeButton) {
      event.preventDefault();

      var imageBoxFromRemove = closest(removeButton, "[data-iez-plan-image-box]");

      if (imageBoxFromRemove) {
        clearSingleImage(imageBoxFromRemove);
      }

      return;
    }

    if (gallerySelect) {
      event.preventDefault();

      if (typeof wp !== "undefined" && wp.media) {
        openGalleryPicker();
      }

      return;
    }

    if (galleryPreview && !isFormControl(event.target)) {
      event.preventDefault();

      if (typeof wp !== "undefined" && wp.media) {
        openGalleryPicker();
      }

      return;
    }

    if (galleryClear) {
      event.preventDefault();

      var parts = getGalleryParts();

      if (parts && parts.input) {
        parts.input.value = "";
      }

      if (parts && parts.preview) {
        parts.preview.innerHTML = '<span class="iez-plan-admin__empty">内装写真は未設定です</span>';
      }

      syncLabelsInput();
      return;
    }

    if (pdfSelect) {
      event.preventDefault();

      var pdfBox = closest(pdfSelect, "[data-iez-plan-pdf-box]");

      if (!pdfBox || typeof wp === "undefined" || !wp.media) {
        return;
      }

      var pdfFrame = wp.media({
        title: "PDFを選択",
        library: {
          type: "application/pdf"
        },
        multiple: false
      });

      pdfFrame.on("select", function () {
        var attachment = pdfFrame.state().get("selection").first();

        if (!attachment) {
          return;
        }

        var data = attachment.toJSON();
        var input = pdfBox.querySelector("[data-iez-plan-pdf-input]");
        var preview = pdfBox.querySelector("[data-iez-plan-pdf-preview]");

        if (input) {
          input.value = data.id || "";
        }

        if (preview) {
          preview.innerHTML = '<span class="iez-plan-admin__pdf-name">' + escapeHtml(data.filename || data.title || "PDF") + '</span>';
        }
      });

      pdfFrame.open();
      return;
    }

    if (pdfClear) {
      event.preventDefault();

      var clearPdfBox = closest(pdfClear, "[data-iez-plan-pdf-box]");

      if (clearPdfBox) {
        var pdfInput = clearPdfBox.querySelector("[data-iez-plan-pdf-input]");
        var pdfPreview = clearPdfBox.querySelector("[data-iez-plan-pdf-preview]");

        if (pdfInput) {
          pdfInput.value = "";
        }

        if (pdfPreview) {
          pdfPreview.innerHTML = '<span class="iez-plan-admin__empty">PDFは未設定です</span>';
        }
      }
    }
  });

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", ensureExistingLabelInputs, { once: true });
  } else {
    ensureExistingLabelInputs();
  }
}());
