jQuery(function ($) {
  'use strict';

  /**
   * =========================================================
   * 複数画像セレクタ
   * 役割:
   * - 既存の hero画像 / card画像 の複数選択
   * =========================================================
   */
  function buildImageThumb(att) {
    var thumb = att.attributes.sizes && att.attributes.sizes.thumbnail
      ? att.attributes.sizes.thumbnail.url
      : att.attributes.url;

    return (
      '<span style="display:inline-flex;width:72px;height:72px;overflow:hidden;border:1px solid #dcdcde;border-radius:8px;background:#fff;">' +
      '<img src="' + thumb + '" alt="" style="display:block;width:100%;height:100%;object-fit:cover;">' +
      '</span>'
    );
  }

  $(document).on('click', '.js-hub-media-open', function (e) {
    e.preventDefault();

    var wrap = $(this).closest('.naigai-hub-media-field');
    var input = wrap.find('.js-hub-media-ids');
    var preview = wrap.find('.js-hub-media-preview');

    var frame = wp.media({
      title: '画像を選択',
      button: { text: 'この画像を使う' },
      multiple: true,
      library: { type: 'image' }
    });

    frame.on('select', function () {
      var selection = frame.state().get('selection');
      var ids = [];
      var html = '';

      selection.each(function (att) {
        ids.push(att.id);
        html += buildImageThumb(att);
      });

      input.val(ids.join(','));
      preview.html(html);
    });

    frame.open();
  });

  $(document).on('click', '.js-hub-media-clear', function (e) {
    e.preventDefault();

    var wrap = $(this).closest('.naigai-hub-media-field');
    wrap.find('.js-hub-media-ids').val('');
    wrap.find('.js-hub-media-preview').empty();
  });

  /**
   * =========================================================
   * 単一ファイルセレクタ
   * 役割:
   * - 注文住宅イントロヒーローの mp4 / webm / poster を1件選択
   * - attachment ID を hidden input に保存する
   * =========================================================
   */
  function renderSinglePreview(preview, att) {
    var html = '';
    var mime = att.get('mime') || '';
    var type = att.get('type') || '';
    var url = att.get('url') || '';
    var filename = att.get('filename') || url.split('/').pop() || 'selected-file';

    if (type === 'image') {
      var sizes = att.get('sizes');
      var thumb = sizes && sizes.thumbnail ? sizes.thumbnail.url : url;
      html += '<span style="display:inline-flex;width:96px;height:72px;overflow:hidden;border:1px solid #dcdcde;border-radius:8px;background:#fff;">';
      html += '<img src="' + thumb + '" alt="" style="display:block;width:100%;height:100%;object-fit:cover;">';
      html += '</span>';
    } else {
      html += '<div style="display:grid;gap:4px;padding:10px 12px;border:1px solid #dcdcde;border-radius:8px;background:#fff;">';
      html += '<strong style="line-height:1.4;">' + filename + '</strong>';
      html += '<span style="color:#646970;">' + mime + '</span>';
      html += '</div>';
    }

    preview.html(html);
  }

  $(document).on('click', '.js-hub-single-media-open', function (e) {
    e.preventDefault();

    var button = $(this);
    var wrap = button.closest('.naigai-hub-single-media-field');
    var input = wrap.find('.js-hub-single-media-id');
    var preview = wrap.find('.js-hub-single-media-preview');
    var libraryType = button.data('libraryType') || wrap.data('libraryType') || '';

    var frame = wp.media({
      title: 'ファイルを選択',
      button: { text: 'このファイルを使う' },
      multiple: false,
      library: libraryType ? { type: libraryType } : {}
    });

    frame.on('select', function () {
      var att = frame.state().get('selection').first();
      if (!att) {
        return;
      }
      input.val(att.id);
      renderSinglePreview(preview, att);
    });

    frame.open();
  });

  $(document).on('click', '.js-hub-single-media-clear', function (e) {
    e.preventDefault();

    var wrap = $(this).closest('.naigai-hub-single-media-field');
    wrap.find('.js-hub-single-media-id').val('');
    wrap.find('.js-hub-single-media-preview').empty();
  });
});
