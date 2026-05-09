(function ($) {
  'use strict';

  function getField(input) {
    return input.closest('.mpbfix-field') || input.parentElement;
  }

  function getGroup(name) {
    if (name.indexOf('_mpb_hero_') !== -1) return 'hero';
    if (name.indexOf('_mpb_cta_') !== -1) return 'cta';
    return '';
  }

  function findInput(group, suffix) {
    return document.querySelector('input[name="_mpb_' + group + '_' + suffix + '"]');
  }

  function findPreview(field) {
    return field.querySelector('.mpbfix-gallery-preview') ||
           field.querySelector('.mpbfix-video-preview') ||
           field.querySelector('.mpbfix-media-preview');
  }

  function clearPeer(group, suffix) {
    if (!group) return;
    var input = findInput(group, suffix);
    if (!input) return;
    input.value = '';
    var preview = findPreview(getField(input));
    if (preview) preview.innerHTML = '';
  }

  function keepOnlyFirstTwoButtons(field, inputName) {
    var buttons = Array.from(field.querySelectorAll('button.button'));
    buttons.forEach(function (btn, index) {
      if (index >= 2) btn.remove();
    });
    buttons = Array.from(field.querySelectorAll('button.button'));
    if (buttons.length < 2) return null;

    if (inputName.endsWith('_gallery_ids')) {
      buttons[0].textContent = '複数画像を選択';
      buttons[1].textContent = '解除';
    } else if (inputName.endsWith('_video_mp4_id')) {
      buttons[0].textContent = '動画を選択';
      buttons[1].textContent = '解除';
    } else {
      buttons[0].textContent = '画像を選ぶ';
      buttons[1].textContent = 'クリア';
    }

    return { openBtn: buttons[0], clearBtn: buttons[1] };
  }

  function renderGallery(preview, atts) {
    if (!preview) return;
    preview.innerHTML = '';
    atts.forEach(function (att) {
      var url = '';
      if (att.sizes && att.sizes.thumbnail) {
        url = att.sizes.thumbnail.url;
      } else if (att.url) {
        url = att.url;
      }
      if (!url) return;
      preview.insertAdjacentHTML(
        'beforeend',
        '<img src="' + url + '" style="width:96px;height:96px;object-fit:cover;border:1px solid #ddd;border-radius:6px;margin:0 8px 8px 0;">'
      );
    });
  }

  function renderVideo(preview, att) {
    if (!preview) return;
    var url = att && att.url ? att.url : '';
    preview.innerHTML = url
      ? '<div class="mpbfix-video-meta">ID: ' + att.id + '<br>' + (att.title || '') + '<br>' + url + '</div>'
      : '';
  }

  function renderImage(preview, att) {
    if (!preview) return;
    var url = '';
    if (att && att.sizes && att.sizes.thumbnail) {
      url = att.sizes.thumbnail.url;
    } else if (att && att.url) {
      url = att.url;
    }
    preview.innerHTML = url
      ? '<img src="' + url + '" style="width:96px;height:96px;object-fit:cover;border:1px solid #ddd;border-radius:6px;">'
      : '';
  }

  function bindField(input) {
    var field = getField(input);
    if (!field) return;

    var pair = keepOnlyFirstTwoButtons(field, input.name);
    if (!pair) return;

    var openBtn = pair.openBtn;
    var clearBtn = pair.clearBtn;
    var preview = findPreview(field);
    var group = getGroup(input.name);

    if (openBtn.dataset.mpbfixBound === '1') return;
    openBtn.dataset.mpbfixBound = '1';
    clearBtn.dataset.mpbfixBound = '1';

    if (input.name.endsWith('_gallery_ids')) {
      openBtn.addEventListener('click', function (e) {
        e.preventDefault();
        var frame = wp.media({
          title: '複数画像を選択',
          button: { text: 'ギャラリーを設定' },
          library: { type: 'image' },
          multiple: true
        });

        frame.on('select', function () {
          var atts = frame.state().get('selection').toJSON();
          var ids = atts.map(function (att) { return att.id; }).filter(Boolean);
          input.value = ids.join(',');
          renderGallery(preview, atts);
          clearPeer(group, 'video_mp4_id');
        });

        frame.open();
      });

      clearBtn.addEventListener('click', function (e) {
        e.preventDefault();
        input.value = '';
        if (preview) preview.innerHTML = '';
      });
      return;
    }

    if (input.name.endsWith('_video_mp4_id')) {
      openBtn.addEventListener('click', function (e) {
        e.preventDefault();
        var frame = wp.media({
          title: '動画を選択',
          button: { text: '動画を設定' },
          library: { type: 'video' },
          multiple: false
        });

        frame.on('select', function () {
          var att = frame.state().get('selection').first().toJSON();
          input.value = att.id || '';
          renderVideo(preview, att);
          clearPeer(group, 'gallery_ids');
        });

        frame.open();
      });

      clearBtn.addEventListener('click', function (e) {
        e.preventDefault();
        input.value = '';
        if (preview) preview.innerHTML = '';
      });
      return;
    }

    openBtn.addEventListener('click', function (e) {
      e.preventDefault();
      var frame = wp.media({
        title: '画像を選択',
        button: { text: '使用する' },
        library: { type: 'image' },
        multiple: false
      });

      frame.on('select', function () {
        var att = frame.state().get('selection').first().toJSON();
        input.value = att.id || '';
        renderImage(preview, att);
      });

      frame.open();
    });

    clearBtn.addEventListener('click', function (e) {
      e.preventDefault();
      input.value = '';
      if (preview) preview.innerHTML = '';
    });
  }

  $(function () {
    document.querySelectorAll(
      'input[type="hidden"].js-mpbfix-media-id,' +
      'input[type="hidden"][name$="_gallery_ids"],' +
      'input[type="hidden"][name$="_video_mp4_id"]'
    ).forEach(bindField);
  });
})(jQuery);
