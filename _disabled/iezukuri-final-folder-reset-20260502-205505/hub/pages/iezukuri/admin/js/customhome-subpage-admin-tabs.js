(function () {
  'use strict';

  var RUN_FLAG = 'naigaiChSimpleEditorRendered';

  function ready(fn) {
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', fn);
    } else {
      fn();
    }
  }

  function getBox(id) {
    return document.getElementById(id);
  }

  function renderEditor() {
    if (window[RUN_FLAG] || document.getElementById('naigai-ch-tab-editor')) {
      return true;
    }

    var admin = window.naigaiChAdmin || {};

    var boxes = {
      mode: getBox('naigai_ch_builder_mode'),
      content: getBox('naigai_ch_subpage_layout_fields'),
      cta: getBox('naigai_ch_subpage_cta_background'),
      contact: getBox('naigai_ch_contact_admin_fields'),
      company: getBox('naigai_ch_company_info_box')
    };

    var hasAny = boxes.mode || boxes.content || boxes.cta || boxes.contact || boxes.company;
    if (!hasAny) return false;

    var shell = document.createElement('div');
    shell.id = 'naigai-ch-tab-editor';
    shell.className = 'naigai-ch-tab-editor';

    shell.innerHTML =
      '<div class="naigai-ch-tab-editor__head">' +
        '<p class="naigai-ch-tab-editor__eyebrow">CUSTOM HOME PAGE EDITOR</p>' +
        '<h2>注文住宅ページ編集パネル</h2>' +
        '<p>このページに必要な項目だけを編集します。表示順・合算は未接続のため一旦非表示です。</p>' +
      '</div>' +
      '<div class="naigai-ch-tab-editor__nav"></div>' +
      '<div class="naigai-ch-tab-editor__body"></div>';

    var target = document.getElementById('post-body-content') || document.getElementById('poststuff') || document.body;
    target.parentNode.insertBefore(shell, target);

    var nav = shell.querySelector('.naigai-ch-tab-editor__nav');
    var body = shell.querySelector('.naigai-ch-tab-editor__body');

    var tabs = [];

    if (boxes.mode) {
      tabs.push({
        key: 'basic',
        label: '1. 基本設定',
        lead: 'このページのレイアウト型を確認します。',
        boxes: [boxes.mode]
      });
    }

    if (boxes.content) {
      tabs.push({
        key: 'content',
        label: '2. 本ページ入力',
        lead: 'このページ自身の見出し・本文・画像だけを入力します。',
        boxes: [boxes.content]
      });
    }

    if (boxes.company) {
      tabs.push({
        key: 'company',
        label: '3. 会社情報',
        lead: '/company の会社情報を参照するか、このページ専用にするかを選びます。',
        boxes: [boxes.company]
      });
    }

    if (boxes.contact) {
      tabs.push({
        key: 'contact',
        label: '3. Flow / Form / CTA',
        lead: 'お問い合わせページのFlow、フォーム見出し、CTA文言を編集します。',
        boxes: [boxes.contact]
      });
    }

    if (boxes.cta) {
      tabs.push({
        key: 'cta',
        label: '3. CTA / 画像 / 動画',
        lead: 'CTA背景画像、mp4、Swiperを編集します。',
        boxes: [boxes.cta]
      });
    }

    tabs.forEach(function (tab, index) {
      var btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'naigai-ch-tab-editor__tab' + (index === 0 ? ' is-active' : '');
      btn.dataset.tab = tab.key;
      btn.textContent = tab.label;
      nav.appendChild(btn);

      var panel = document.createElement('section');
      panel.className = 'naigai-ch-tab-editor__panel' + (index === 0 ? ' is-active' : '');
      panel.dataset.panel = tab.key;
      panel.innerHTML =
        '<div class="naigai-ch-tab-editor__guide">' +
          '<strong>' + tab.label + '</strong>' +
          '<p>' + tab.lead + '</p>' +
        '</div>';

      tab.boxes.forEach(function (box) {
        if (!box) return;
        panel.appendChild(box);
      });

      body.appendChild(panel);
    });

    nav.addEventListener('click', function (event) {
      var btn = event.target.closest('[data-tab]');
      if (!btn) return;

      var key = btn.dataset.tab;

      nav.querySelectorAll('[data-tab]').forEach(function (b) {
        b.classList.toggle('is-active', b === btn);
      });

      body.querySelectorAll('[data-panel]').forEach(function (p) {
        p.classList.toggle('is-active', p.dataset.panel === key);
      });
    });

    hideIrrelevantFields(admin.layout || admin.slug || '');

    document.body.classList.add('naigai-ch-tab-editor-active');
    window[RUN_FLAG] = true;
    return true;
  }

  function hideIrrelevantFields(layout) {
    if (!layout || layout === 'builder') return;

    var prefixes = {
      'concept': ['_ch_concept_'],
      'design-policy': ['_ch_design_policy_'],
      'nasu-house': ['_ch_nasu_'],
      'design-office': ['_ch_design_office_', '_ch_plan_'],
      'company': ['_ch_company_'],
      'contact': ['_ch_contact_', '_hub_ch_cta_']
    };

    var allowed = prefixes[layout] || [];
    if (!allowed.length) return;

    var all = [];
    Object.keys(prefixes).forEach(function (k) {
      all = all.concat(prefixes[k]);
    });

    document.querySelectorAll('#naigai_ch_subpage_layout_fields input[name], #naigai_ch_subpage_layout_fields textarea[name], #naigai_ch_subpage_layout_fields select[name]').forEach(function (field) {
      var name = field.getAttribute('name') || '';
      var known = all.some(function (p) { return name.indexOf(p) === 0; });
      var ok = allowed.some(function (p) { return name.indexOf(p) === 0; });

      if (known && !ok) {
        var row = field.closest('tr, p, div');
        if (row) row.style.display = 'none';
      }
    });
  }

  ready(function () {
    var tries = 0;
    var timer = setInterval(function () {
      tries++;
      if (renderEditor() || tries > 40) clearInterval(timer);
    }, 250);
  });
})();
