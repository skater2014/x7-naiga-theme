/* =========================================================
 * /fudousan 管理画面 JS
 * hub/pages/fudousan/admin/fudousan-admin.js
 * =========================================================
 *
 * 対象:
 * - 左メニュー「不動産ページ設定」
 *
 * 役割:
 * - WordPressメディアライブラリから画像 / mp4 を選択する。
 * - 選択したメディアIDを hidden input に保存する。
 * - 画像ID番号は画面に出さず、サムネイルや動画プレビューだけを表示する。
 *
 * 注意:
 * - フロント /fudousan のタブ・Isotope・Swiper は扱わない。
 * - フロント用の fudousan-page.js / isotope / scripts.js はここに書かない。
 * ========================================================= */

jQuery(function ($) {
  'use strict';

  function renderPreview($input) {
    var ids = String($input.val() || '')
      .split(',')
      .map(function (value) {
        return value.trim();
      })
      .filter(Boolean);

    var $preview = $($input.data('preview'));
    var type = $input.attr('id') === '_fudo_hero_mp4_id' ? 'video' : 'image';

    if (!$preview.length) {
      return;
    }

    $preview.empty();

    if (!ids.length) {
      $preview.append(
        '<p class="fudo-admin-preview__empty">' +
          (type === 'video' ? 'mp4 が選択されていません。' : '画像が選択されていません。') +
        '</p>'
      );
      return;
    }

    ids.forEach(function (id) {
      var attachment = wp.media.attachment(id);

      attachment.fetch().then(function () {
        var data = attachment.toJSON();
        var url = '';

        if (type === 'video') {
          url = data.url || '';

          if (url) {
            $preview.html('<video src="' + url + '" controls muted playsinline></video>');
          }

          return;
        }

        if (data.sizes && data.sizes.thumbnail) {
          url = data.sizes.thumbnail.url;
        } else if (data.sizes && data.sizes.medium) {
          url = data.sizes.medium.url;
        } else {
          url = data.url || '';
        }

        if (url) {
          $preview.append(
            '<span class="fudo-admin-preview__thumb"><img src="' + url + '" alt=""></span>'
          );
        }
      });
    });
  }

  $(document).on('click', '[data-fudo-select-media]', function (e) {
    e.preventDefault();

    var $btn = $(this);
    var target = $btn.data('target');
    var $input = $(target);
    var type = $btn.data('type') || 'image';
    var multiple = String($btn.data('multiple')) === '1';

    var frame = wp.media({
      title: type === 'video' ? 'mp4を選択' : '画像を選択',
      button: {
        text: 'このメディアを使う'
      },
      multiple: multiple,
      library: {
        type: type
      }
    });

    frame.on('select', function () {
      var ids = [];

      frame.state().get('selection').each(function (attachment) {
        ids.push(attachment.id);
      });

      $input.val(ids.join(',')).trigger('change');
      renderPreview($input);
    });

    frame.open();
  });

  $(document).on('click', '[data-fudo-clear-media]', function (e) {
    e.preventDefault();

    var $input = $($(this).data('target'));

    $input.val('').trigger('change');
    renderPreview($input);
  });
});
