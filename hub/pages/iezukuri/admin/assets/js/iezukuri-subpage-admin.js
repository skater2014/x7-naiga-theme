(function ($) {
  'use strict';

  function parseJson(value) {
    try {
      var parsed = JSON.parse(value || '[]');
      return Array.isArray(parsed) ? parsed : [];
    } catch (e) {
      return [];
    }
  }

  function esc(value) {
    return $('<div>').text(value || '').html();
  }

  function syncRows(wrapSelector, inputSelector) {
    var rows = [];

    $(wrapSelector).find('.iez-repeat-row').each(function () {
      var row = {};
      $(this).find('[data-field]').each(function () {
        row[$(this).data('field')] = $(this).val();
      });
      rows.push(row);
    });

    $(inputSelector).val(JSON.stringify(rows));
  }

  function mediaPicker(callback, type) {
    var frame = wp.media({
      title: 'メディアを選択',
      button: { text: 'このメディアを使う' },
      library: type ? { type: type } : {},
      multiple: false
    });

    frame.on('select', function () {
      callback(frame.state().get('selection').first().toJSON());
    });

    frame.open();
  }

  function renderHero() {
    var input = $('#_iez_sub_hero_items_json');
    var wrap = $('#iez-hero-repeat');
    var rows = parseJson(input.val());

    wrap.empty();

    rows.forEach(function (row, index) {
      wrap.append(
        '<div class="iez-repeat-row">' +
          '<div class="iez-repeat-row__head">' +
            '<strong>Heroメディア ' + (index + 1) + '</strong>' +
            '<button type="button" class="button" data-remove-row>削除</button>' +
          '</div>' +
          '<div class="iez-mini-grid">' +
            '<label>種類</label>' +
            '<select data-field="type">' +
              '<option value="image">画像</option>' +
              '<option value="mp4">mp4</option>' +
            '</select>' +

            '<label>画像ID</label>' +
            '<div><input type="text" data-field="image_id" value="' + esc(row.image_id) + '"> <button type="button" class="button" data-pick-image>画像選択</button></div>' +

            '<label>mp4 ID</label>' +
            '<div><input type="text" data-field="mp4_id" value="' + esc(row.mp4_id) + '"> <button type="button" class="button" data-pick-mp4>mp4選択</button></div>' +

            '<label>タイトル</label>' +
            '<input type="text" data-field="title" value="' + esc(row.title) + '">' +

            '<label>説明</label>' +
            '<textarea data-field="text" rows="3">' + esc(row.text) + '</textarea>' +
          '</div>' +
        '</div>'
      );

      wrap.find('.iez-repeat-row').last().find('[data-field="type"]').val(row.type || 'image');
    });

    syncRows('#iez-hero-repeat', '#_iez_sub_hero_items_json');
  }

  function renderSections() {
    var input = $('#_iez_sub_sections_json');
    var wrap = $('#iez-section-repeat');
    var rows = parseJson(input.val());

    wrap.empty();

    rows.forEach(function (row, index) {
      wrap.append(
        '<div class="iez-repeat-row">' +
          '<div class="iez-repeat-row__head">' +
            '<strong>セクション ' + (index + 1) + '</strong>' +
            '<button type="button" class="button" data-remove-row>削除</button>' +
          '</div>' +
          '<div class="iez-mini-grid">' +
            '<label>アンカーID</label>' +
            '<input type="text" data-field="key" value="' + esc(row.key || ('section-' + (index + 1))) + '">' +

            '<label>ナビ名</label>' +
            '<input type="text" data-field="label" value="' + esc(row.label) + '">' +

            '<label>見出し</label>' +
            '<input type="text" data-field="title" value="' + esc(row.title) + '">' +

            '<label>本文</label>' +
            '<textarea data-field="body" rows="5">' + esc(row.body) + '</textarea>' +

            '<label>画像ID</label>' +
            '<div><input type="text" data-field="image_id" value="' + esc(row.image_id) + '"> <button type="button" class="button" data-pick-section-image>画像選択</button></div>' +
          '</div>' +
        '</div>'
      );
    });

    syncRows('#iez-section-repeat', '#_iez_sub_sections_json');
  }

  $(function () {
    if (!$('#iez-hero-repeat').length) {
      return;
    }

    renderHero();
    renderSections();

    $(document).on('click', '[data-iez-add-hero]', function () {
      var rows = parseJson($('#_iez_sub_hero_items_json').val());
      rows.push({ type: 'image', image_id: '', mp4_id: '', title: '', text: '' });
      $('#_iez_sub_hero_items_json').val(JSON.stringify(rows));
      renderHero();
    });

    $(document).on('click', '[data-iez-add-section]', function () {
      var rows = parseJson($('#_iez_sub_sections_json').val());
      rows.push({ key: 'section-' + (rows.length + 1), label: '', title: '', body: '', image_id: '' });
      $('#_iez_sub_sections_json').val(JSON.stringify(rows));
      renderSections();
    });

    $(document).on('click', '[data-remove-row]', function () {
      $(this).closest('.iez-repeat-row').remove();
      syncRows('#iez-hero-repeat', '#_iez_sub_hero_items_json');
      syncRows('#iez-section-repeat', '#_iez_sub_sections_json');
    });

    $(document).on('change input', '#iez-hero-repeat [data-field]', function () {
      syncRows('#iez-hero-repeat', '#_iez_sub_hero_items_json');
    });

    $(document).on('change input', '#iez-section-repeat [data-field]', function () {
      syncRows('#iez-section-repeat', '#_iez_sub_sections_json');
    });

    $(document).on('click', '[data-pick-image]', function () {
      var input = $(this).siblings('input');
      mediaPicker(function (media) {
        input.val(media.id).trigger('change');
      }, 'image');
    });

    $(document).on('click', '[data-pick-mp4]', function () {
      var input = $(this).siblings('input');
      mediaPicker(function (media) {
        input.val(media.id).trigger('change');
      }, 'video');
    });

    $(document).on('click', '[data-pick-section-image]', function () {
      var input = $(this).siblings('input');
      mediaPicker(function (media) {
        input.val(media.id).trigger('change');
      }, 'image');
    });
  });
})(jQuery);
