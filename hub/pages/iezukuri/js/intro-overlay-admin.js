(function ($) {
  'use strict';

  if (window.__NAIGAI_IEZ_INTRO_ADMIN_INIT__) {
    return;
  }
  window.__NAIGAI_IEZ_INTRO_ADMIN_INIT__ = true;

  function findField(selectors) {
    for (var i = 0; i < selectors.length; i++) {
      var el = document.querySelector(selectors[i]);
      if (el) return el;
    }
    return null;
  }

  function getLines(field) {
    if (!field) return [];
    return field.value
      .split(/\r?\n/)
      .map(function (line) { return line.trim(); })
      .filter(Boolean);
  }

  function setLines(field, lines) {
    if (!field) return;

    var unique = [];
    lines.forEach(function (url) {
      if (url && unique.indexOf(url) === -1) unique.push(url);
    });

    field.value = unique.join("\n");
    field.dispatchEvent(new Event('input', { bubbles: true }));
    field.dispatchEvent(new Event('change', { bubbles: true }));
  }

  function appendLine(field, url) {
    var lines = getLines(field);
    if (lines.indexOf(url) === -1) {
      lines.push(url);
      setLines(field, lines);
    }
  }

  function removeLine(field, url) {
    setLines(field, getLines(field).filter(function (line) {
      return line !== url;
    }));
  }

  function fileNameFromUrl(url) {
    try {
      return decodeURIComponent(url.split('/').pop().split('?')[0]);
    } catch (e) {
      return url;
    }
  }

  function createStyleOnce() {
    if (document.getElementById('naigai-iez-intro-admin-style')) return;

    var style = document.createElement('style');
    style.id = 'naigai-iez-intro-admin-style';
    style.textContent = [
      '.iez-intro-admin-summary{margin:14px 0 18px;padding:14px 16px;border:1px solid #dcdcde;border-left:4px solid #2271b1;background:#fff;}',
      '.iez-intro-admin-summary.is-warning{border-left-color:#d63638;background:#fff8f8;}',
      '.iez-intro-admin-summary strong{display:inline-block;margin-right:16px;}',
      '.iez-intro-admin-summary .button{margin-left:8px;}',
      '.iez-intro-admin-block{margin:18px 0 22px;}',
      '.iez-intro-admin-block__head{display:flex;justify-content:space-between;align-items:center;gap:12px;margin-bottom:10px;}',
      '.iez-intro-admin-block__head strong{font-size:14px;}',
      '.iez-intro-admin-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:12px;}',
      '.iez-intro-admin-empty{padding:14px;border:1px dashed #b8c0c8;background:#f6f7f7;color:#646970;}',
      '.iez-intro-admin-card{border:1px solid #dcdcde;border-radius:8px;background:#fff;overflow:hidden;box-shadow:0 1px 2px rgba(0,0,0,.05);}',
      '.iez-intro-admin-card img{display:block;width:100%;aspect-ratio:16/10;object-fit:cover;background:#f0f0f1;}',
      '.iez-intro-admin-card__body{padding:8px;}',
      '.iez-intro-admin-card__name{display:block;min-height:34px;font-size:11px;line-height:1.45;color:#50575e;word-break:break-all;}',
      '.iez-intro-admin-card__meta{display:flex;justify-content:space-between;align-items:center;margin-top:8px;gap:8px;}',
      '.iez-intro-admin-badge{display:inline-block;padding:2px 6px;border-radius:999px;font-size:10px;line-height:1.4;background:#edf7ed;color:#0a5c1f;border:1px solid #b7d9b9;}',
      '.iez-intro-admin-card__meta .button{width:100%;justify-content:center;}',
      '.iez-intro-admin-tools{margin:8px 0 10px;}',
      '.iez-intro-admin-tools .button{margin-right:8px;margin-bottom:6px;}'
    ].join("\n");
    document.head.appendChild(style);
  }

  function mediaUrlFromAttachment(attachment) {
    if (!attachment) return '';
    if (attachment.sizes && attachment.sizes.large && attachment.sizes.large.url) return attachment.sizes.large.url;
    if (attachment.sizes && attachment.sizes.full && attachment.sizes.full.url) return attachment.sizes.full.url;
    return attachment.url || '';
  }

  function setupMediaButton(field, label, multiple, type) {
    if (!field || field.dataset.iezMediaReady === '1') return;
    field.dataset.iezMediaReady = '1';

    var tools = document.createElement('div');
    tools.className = 'iez-intro-admin-tools';

    var addButton = document.createElement('button');
    addButton.type = 'button';
    addButton.className = 'button button-secondary';
    addButton.textContent = label + 'をメディアから追加';

    var clearButton = document.createElement('button');
    clearButton.type = 'button';
    clearButton.className = 'button';
    clearButton.textContent = '一覧を空にする';

    tools.appendChild(addButton);
    tools.appendChild(clearButton);
    field.parentNode.insertBefore(tools, field);

    addButton.addEventListener('click', function (e) {
      e.preventDefault();

      var frame = wp.media({
        title: label + 'を選択',
        button: { text: multiple ? '選択した画像を追加' : 'このファイルを使う' },
        library: type ? { type: type } : undefined,
        multiple: multiple
      });

      frame.on('select', function () {
        if (multiple) {
          var current = getLines(field);
          frame.state().get('selection').each(function (item) {
            var url = mediaUrlFromAttachment(item.toJSON());
            if (url && current.indexOf(url) === -1) current.push(url);
          });
          setLines(field, current);
        } else {
          var first = frame.state().get('selection').first().toJSON();
          field.value = mediaUrlFromAttachment(first);
          field.dispatchEvent(new Event('input', { bubbles: true }));
          field.dispatchEvent(new Event('change', { bubbles: true }));
        }
      });

      frame.open();
    });

    clearButton.addEventListener('click', function (e) {
      e.preventDefault();

      if (window.confirm(label + 'の一覧を空にします。画像ファイル自体は削除されません。')) {
        if (field.tagName.toLowerCase() === 'textarea') {
          setLines(field, []);
        } else {
          field.value = '';
          field.dispatchEvent(new Event('input', { bubbles: true }));
          field.dispatchEvent(new Event('change', { bubbles: true }));
        }
      }
    });
  }

  function createBlock(field, titleText, kind, stockUrls) {
    if (!field) return null;

    var wrap = document.createElement('div');
    wrap.className = 'iez-intro-admin-block iez-intro-admin-block--' + kind;

    var head = document.createElement('div');
    head.className = 'iez-intro-admin-block__head';

    var title = document.createElement('strong');
    title.textContent = titleText;

    var count = document.createElement('span');
    count.className = 'iez-intro-admin-block__count';

    head.appendChild(title);
    head.appendChild(count);

    var grid = document.createElement('div');
    grid.className = 'iez-intro-admin-grid';

    wrap.appendChild(head);
    wrap.appendChild(grid);
    field.parentNode.insertBefore(wrap, field);

    function render() {
      var lines = getLines(field);
      grid.innerHTML = '';
      count.textContent = lines.length + '枚';

      if (!lines.length) {
        var empty = document.createElement('div');
        empty.className = 'iez-intro-admin-empty';
        empty.textContent = (window.NaigaiIezIntroAdmin && window.NaigaiIezIntroAdmin.labels && window.NaigaiIezIntroAdmin.labels.empty) || 'まだ画像が入っていません。';
        grid.appendChild(empty);
        return;
      }

      lines.forEach(function (url, index) {
        var card = document.createElement('div');
        card.className = 'iez-intro-admin-card';

        var img = document.createElement('img');
        img.src = url;
        img.alt = titleText + ' ' + (index + 1);
        img.loading = 'lazy';

        var body = document.createElement('div');
        body.className = 'iez-intro-admin-card__body';

        var name = document.createElement('span');
        name.className = 'iez-intro-admin-card__name';
        name.textContent = (index + 1) + '. ' + fileNameFromUrl(url);

        var meta = document.createElement('div');
        meta.className = 'iez-intro-admin-card__meta';

        var badge = document.createElement('span');
        badge.className = 'iez-intro-admin-badge';
        badge.textContent = '使用中';

        var button = document.createElement('button');
        button.type = 'button';
        button.className = 'button button-small';
        button.textContent = 'イントロから外す';
        button.addEventListener('click', function () {
          removeLine(field, url);
        });

        meta.appendChild(badge);
        meta.appendChild(button);
        body.appendChild(name);
        body.appendChild(meta);
        card.appendChild(img);
        card.appendChild(body);
        grid.appendChild(card);
      });
    }

    field.addEventListener('input', render);
    field.addEventListener('change', render);
    render();

    return {
      render: render,
      count: function () { return getLines(field).length; },
      lines: function () { return getLines(field); },
      set: function (lines) { setLines(field, lines); },
      field: field
    };
  }

  function createStockBlock(field, titleText, side, stockUrls) {
    if (!field) return null;

    var wrap = document.createElement('div');
    wrap.className = 'iez-intro-admin-block iez-intro-admin-block--stock-' + side;

    var head = document.createElement('div');
    head.className = 'iez-intro-admin-block__head';

    var title = document.createElement('strong');
    title.textContent = titleText;

    var count = document.createElement('span');
    count.className = 'iez-intro-admin-block__count';

    head.appendChild(title);
    head.appendChild(count);

    var grid = document.createElement('div');
    grid.className = 'iez-intro-admin-grid';

    wrap.appendChild(head);
    wrap.appendChild(grid);

    field.parentNode.insertBefore(wrap, field);

    function render() {
      var active = getLines(field);
      var allStock = Array.isArray(stockUrls) ? stockUrls : [];

      grid.innerHTML = '';
      count.textContent = allStock.length + '枚';

      if (!allStock.length) {
        var empty = document.createElement('div');
        empty.className = 'iez-intro-admin-empty';
        empty.textContent = 'ストック画像がありません。';
        grid.appendChild(empty);
        return;
      }

      allStock.forEach(function (url) {
        var inUse = active.indexOf(url) !== -1;

        var card = document.createElement('div');
        card.className = 'iez-intro-admin-card';

        var img = document.createElement('img');
        img.src = url;
        img.alt = titleText;
        img.loading = 'lazy';

        var body = document.createElement('div');
        body.className = 'iez-intro-admin-card__body';

        var name = document.createElement('span');
        name.className = 'iez-intro-admin-card__name';
        name.textContent = fileNameFromUrl(url);

        var meta = document.createElement('div');
        meta.className = 'iez-intro-admin-card__meta';

        var badge = document.createElement('span');
        badge.className = 'iez-intro-admin-badge';
        badge.textContent = inUse ? '使用中' : '未使用';

        var button = document.createElement('button');
        button.type = 'button';
        button.className = 'button button-small';
        button.textContent = inUse ? '使用中' : 'イントロに戻す';
        button.disabled = inUse;

        if (!inUse) {
          button.addEventListener('click', function () {
            appendLine(field, url);
          });
        }

        meta.appendChild(badge);
        meta.appendChild(button);
        body.appendChild(name);
        body.appendChild(meta);
        card.appendChild(img);
        card.appendChild(body);
        grid.appendChild(card);
      });
    }

    field.addEventListener('input', render);
    field.addEventListener('change', render);
    render();

    return { render: render };
  }

  function createSummary(nasuPreview, tokyoPreview) {
    if (!nasuPreview || !tokyoPreview) return;
    if (document.querySelector('.iez-intro-admin-summary')) return;

    var summary = document.createElement('div');
    summary.className = 'iez-intro-admin-summary';

    var text = document.createElement('span');

    var trimButton = document.createElement('button');
    trimButton.type = 'button';
    trimButton.className = 'button button-secondary';
    trimButton.textContent = '少ない枚数に揃える';

    summary.appendChild(text);
    summary.appendChild(trimButton);

    var form = document.querySelector('form');
    var wrap = document.querySelector('.wrap');
    if (wrap && form) {
      wrap.insertBefore(summary, form);
    }

    function render() {
      var nasuCount = nasuPreview.count();
      var tokyoCount = tokyoPreview.count();
      var matched = nasuCount === tokyoCount;

      summary.classList.toggle('is-warning', !matched);

      text.innerHTML =
        '<strong>那須: ' + nasuCount + '枚</strong>' +
        '<strong>東京: ' + tokyoCount + '枚</strong>' +
        (matched ? '枚数は揃っています。' : '枚数が違います。左右を揃えるなら同数がおすすめです。');

      trimButton.style.display = matched ? 'none' : 'inline-block';
    }

    trimButton.addEventListener('click', function () {
      var nasu = nasuPreview.lines();
      var tokyo = tokyoPreview.lines();
      var min = Math.min(nasu.length, tokyo.length);

      if (!min) return;

      if (window.confirm('画像ファイルは削除せず、イントロに使う一覧だけを ' + min + '枚に揃えます。よろしいですか？')) {
        nasuPreview.set(nasu.slice(0, min));
        tokyoPreview.set(tokyo.slice(0, min));
        render();
      }
    });

    nasuPreview.field.addEventListener('input', render);
    nasuPreview.field.addEventListener('change', render);
    tokyoPreview.field.addEventListener('input', render);
    tokyoPreview.field.addEventListener('change', render);

    render();
  }

  document.addEventListener('DOMContentLoaded', function () {
    createStyleOnce();

    var stock = (window.NaigaiIezIntroAdmin && window.NaigaiIezIntroAdmin.stock) || { nasu: [], tokyo: [] };

    if (!Array.isArray(stock.nasu)) stock.nasu = [];
    if (!Array.isArray(stock.tokyo)) stock.tokyo = [];

    var stockRenderers = [];

    document.addEventListener('naigai-iez-intro-stock-uploaded', function (event) {
      var detail = event.detail || {};
      var side = detail.side;
      var url = detail.url;

      if (!url || (side !== 'nasu' && side !== 'tokyo')) {
        return;
      }

      if (!Array.isArray(stock[side])) {
        stock[side] = [];
      }

      if (stock[side].indexOf(url) === -1) {
        stock[side].push(url);
      }

      stockRenderers.forEach(function (renderer) {
        if (renderer && typeof renderer.render === 'function') {
          renderer.render();
        }
      });
    });

    var nasuField = findField([
      'textarea[name="naigai_iez_intro[nasu_images]"]',
      'textarea[name="naigai_iezukuri_intro_options[nasu_images]"]',
      '#iez_intro_nasu_images'
    ]);

    var tokyoField = findField([
      'textarea[name="naigai_iez_intro[tokyo_images]"]',
      'textarea[name="naigai_iezukuri_intro_options[tokyo_images]"]',
      '#iez_intro_tokyo_images'
    ]);

    var bgmField = findField([
      'input[name="naigai_iez_intro[bgm_url]"]',
      'input[name="naigai_iezukuri_intro_options[bgm_url]"]',
      '#iez_intro_bgm_url'
    ]);

    setupMediaButton(nasuField, '那須側画像', true, 'image');
    setupMediaButton(tokyoField, '東京側画像', true, 'image');
    setupMediaButton(bgmField, 'BGM', false, 'audio');

    var nasuPreview = createBlock(nasuField, '那須側：現在イントロで使用中', 'active-nasu');
    var tokyoPreview = createBlock(tokyoField, '東京側：現在イントロで使用中', 'active-tokyo');

    var nasuStockPreview = createStockBlock(nasuField, '那須側：ストック画像一覧', 'nasu', stock.nasu || []);
    var tokyoStockPreview = createStockBlock(tokyoField, '東京側：ストック画像一覧', 'tokyo', stock.tokyo || []);

    stockRenderers.push(nasuStockPreview);
    stockRenderers.push(tokyoStockPreview);

    createSummary(nasuPreview, tokyoPreview);
  });
})(jQuery);
