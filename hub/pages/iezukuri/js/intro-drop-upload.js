(function () {
  'use strict';

  function findNasuField() {
    return document.querySelector('#iez_intro_nasu_images')
      || document.querySelector('textarea[name="naigai_iezukuri_intro_options[nasu_images]"]')
      || document.querySelector('textarea[name="naigai_iez_intro[nasu_images]"]');
  }

  function findTokyoField() {
    return document.querySelector('#iez_intro_tokyo_images')
      || document.querySelector('textarea[name="naigai_iezukuri_intro_options[tokyo_images]"]')
      || document.querySelector('textarea[name="naigai_iez_intro[tokyo_images]"]');
  }

  function findBgmField() {
    return document.querySelector('#iez_intro_bgm_url')
      || document.querySelector('input[name="naigai_iezukuri_intro_options[bgm_url]"]')
      || document.querySelector('input[name="naigai_iez_intro[bgm_url]"]');
  }

  function appendUrl(field, url) {
    if (!field || !url) return;

    if (field.tagName.toLowerCase() === 'textarea') {
      var lines = field.value.trim()
        ? field.value.trim().split(/\r?\n/).map(function (line) { return line.trim(); }).filter(Boolean)
        : [];

      if (lines.indexOf(url) === -1) {
        lines.push(url);
      }

      field.value = lines.join("\n");
    } else {
      field.value = url;
    }

    field.dispatchEvent(new Event('input', { bubbles: true }));
    field.dispatchEvent(new Event('change', { bubbles: true }));
  }

  function notifyStockUploaded(side, url, file) {
    document.dispatchEvent(new CustomEvent('naigai-iez-intro-stock-uploaded', {
      detail: {
        side: side,
        url: url,
        file: file || ''
      }
    }));
  }

  function createDropZone(label, side, field, acceptText) {
    if (!field || field.dataset.iezDropReady === '1') return;

    field.dataset.iezDropReady = '1';

    var zone = document.createElement('div');
    zone.className = 'iez-intro-drop-zone';
    zone.innerHTML =
      '<strong>' + label + '</strong>' +
      '<span>ここにファイルをドラッグ&ドロップ</span>' +
      '<em>' + acceptText + '</em>' +
      '<small></small>';

    zone.style.border = '2px dashed #9aa4af';
    zone.style.background = '#f6f7f7';
    zone.style.padding = '18px';
    zone.style.margin = '10px 0';
    zone.style.borderRadius = '8px';
    zone.style.cursor = 'copy';

    Array.prototype.forEach.call(zone.querySelectorAll('span, em, small'), function (el) {
      el.style.display = 'block';
      el.style.marginTop = '6px';
      el.style.color = '#646970';
    });

    field.parentNode.insertBefore(zone, field);

    function setMessage(message) {
      var small = zone.querySelector('small');
      if (small) small.textContent = message || '';
    }

    function uploadFile(file) {
      var form = new FormData();
      form.append('action', 'naigai_iez_intro_drop_upload');
      form.append('nonce', NaigaiIezIntroDropUpload.nonce);
      form.append('side', side);
      form.append('file', file);

      setMessage('アップロード中: ' + file.name);

      fetch(NaigaiIezIntroDropUpload.ajaxUrl, {
        method: 'POST',
        credentials: 'same-origin',
        body: form
      })
        .then(function (res) { return res.json(); })
        .then(function (json) {
          if (!json || !json.success) {
            var msg = json && json.data && json.data.message ? json.data.message : 'アップロードに失敗しました。';
            setMessage(msg);
            return;
          }

          var url = json.data.url;

          if (side === 'bgm') {
            appendUrl(field, url);
            setMessage('BGMを設定しました: ' + url);
            return;
          }

          /*
           * 画像は stock フォルダーに保存したあと、
           * 1. ストック一覧に即時追加
           * 2. イントロ使用中一覧にも追加
           *
           * もし「アップロードだけして未使用にしたい」場合は、
           * 追加後に「イントロから外す」を押せば stock には残ります。
           */
          notifyStockUploaded(side, url, json.data.file);
          appendUrl(field, url);

          setMessage('追加しました: ' + url);
        })
        .catch(function () {
          setMessage('アップロードに失敗しました。');
        });
    }

    zone.addEventListener('dragover', function (e) {
      e.preventDefault();
      zone.style.background = '#eef6ff';
      zone.style.borderColor = '#2271b1';
    });

    zone.addEventListener('dragleave', function () {
      zone.style.background = '#f6f7f7';
      zone.style.borderColor = '#9aa4af';
    });

    zone.addEventListener('drop', function (e) {
      e.preventDefault();
      zone.style.background = '#f6f7f7';
      zone.style.borderColor = '#9aa4af';

      var files = e.dataTransfer && e.dataTransfer.files ? Array.prototype.slice.call(e.dataTransfer.files) : [];

      if (!files.length) {
        setMessage('ファイルがありません。');
        return;
      }

      files.forEach(uploadFile);
    });
  }

  document.addEventListener('DOMContentLoaded', function () {
    createDropZone('那須側画像アップロード', 'nasu', findNasuField(), 'jpg / png / webp を保存: uploads/iezukuri-intro-stock/nasu/');
    createDropZone('東京側画像アップロード', 'tokyo', findTokyoField(), 'jpg / png / webp を保存: uploads/iezukuri-intro-stock/tokyo/');
    createDropZone('BGMアップロード', 'bgm', findBgmField(), 'mp3 / m4a / wav を保存: uploads/iezukuri-intro-stock/bgm/');
  });
})();
