/* =========================================================
 * 民泊B2C 管理画面
 * - Hero 単体画像
 * - Hero ギャラリー画像
 * - Hero mp4 動画
 * gallery と mp4 は排他
 * ========================================================= */
(function ($) {
  function renderSinglePreview($preview, attachment) {
    if (!attachment) {
      $preview.html('');
      return;
    }

    var img =
      attachment.sizes && attachment.sizes.thumbnail
        ? attachment.sizes.thumbnail.url
        : attachment.url;

    $preview.html(
      '<div class="mpbfix-media-card">' +
        '<img src="' + img + '" alt="">' +
        '<div class="mpbfix-media-meta">ID: ' + attachment.id + '<br>' + (attachment.filename || '') + '</div>' +
      '</div>'
    );
  }

  function renderGalleryPreview($preview, attachments) {
    if (!attachments || !attachments.length) {
      $preview.html('');
      return;
    }

    var html = '<div class="mpbfix-gallery-grid">';

    attachments.forEach(function (att) {
      var img =
        att.sizes && att.sizes.thumbnail
          ? att.sizes.thumbnail.url
          : att.url;

      html += '<div class="mpbfix-gallery-card">';
      html += '<img src="' + img + '" alt="">';
      html += '<div class="mpbfix-media-meta">ID: ' + att.id + '</div>';
      html += '</div>';
    });

    html += '</div>';
    $preview.html(html);
  }

  function renderVideoPreview($preview, attachment) {
    if (!attachment) {
      $preview.html('');
      return;
    }

    $preview.html(
      '<div class="mpbfix-video-card">' +
        '<div class="mpbfix-media-meta">ID: ' + attachment.id + '<br>' + (attachment.filename || '') + '</div>' +
      '</div>'
    );
  }

  function syncHeroMediaExclusivity() {
    var $galleryIds = $('input[name="_mpb_hero_gallery_ids"]');
    var $videoId = $('input[name="_mpb_hero_video_mp4_id"]');

    if (!$galleryIds.length || !$videoId.length) {
      return;
    }

    var hasGallery = $.trim($galleryIds.val()) !== '';
    var hasVideo = parseInt($videoId.val() || '0', 10) > 0;

    var $galleryButtons = $('.js-mpbfix-pick-gallery, .js-mpbfix-clear-gallery');
    var $videoButtons = $('.js-mpbfix-pick-video, .js-mpbfix-clear-video');

    $galleryButtons.prop('disabled', hasVideo).toggleClass('is-disabled', hasVideo);
    $videoButtons.prop('disabled', hasGallery).toggleClass('is-disabled', hasGallery);
  }

  $(document).on('click', '.js-mpbfix-pick-image', function (e) {
    e.preventDefault();

    var $wrap = $(this).closest('.js-mpbfix-media-field');
    var $input = $wrap.find('.js-mpbfix-media-id');
    var $preview = $wrap.find('.mpbfix-media-preview');

    var frame = wp.media({
      title: '画像を選択',
      button: { text: 'この画像を使う' },
      multiple: false,
      library: { type: 'image' }
    });

    frame.on('select', function () {
      var att = frame.state().get('selection').first().toJSON();
      $input.val(att.id);
      renderSinglePreview($preview, att);
    });

    frame.open();
  });

  $(document).on('click', '.js-mpbfix-clear-image', function (e) {
    e.preventDefault();

    var $wrap = $(this).closest('.js-mpbfix-media-field');
    $wrap.find('.js-mpbfix-media-id').val('');
    $wrap.find('.mpbfix-media-preview').html('');
  });

  $(document).on('click', '.js-mpbfix-pick-gallery', function (e) {
    e.preventDefault();

    var $wrap = $(this).closest('.js-mpbfix-gallery-field');
    var $input = $wrap.find('.js-mpbfix-gallery-ids');
    var $preview = $wrap.find('.mpbfix-gallery-preview');

    var $videoInput = $('input[name="_mpb_hero_video_mp4_id"]');
    var $videoPreview = $('.mpbfix-video-preview');

    var frame = wp.media({
      title: 'ギャラリー画像を選択',
      button: { text: 'この画像を使う' },
      multiple: true,
      library: { type: 'image' }
    });

    frame.on('select', function () {
      var selection = frame.state().get('selection').toJSON();
      var ids = selection.map(function (att) {
        return att.id;
      });

      $videoInput.val('');
      $videoPreview.html('');

      $input.val(ids.join(','));
      renderGalleryPreview($preview, selection);
      syncHeroMediaExclusivity();
    });

    frame.open();
  });

  $(document).on('click', '.js-mpbfix-clear-gallery', function (e) {
    e.preventDefault();

    var $wrap = $(this).closest('.js-mpbfix-gallery-field');
    $wrap.find('.js-mpbfix-gallery-ids').val('');
    $wrap.find('.mpbfix-gallery-preview').html('');
    syncHeroMediaExclusivity();
  });

  $(document).on('click', '.js-mpbfix-pick-video', function (e) {
    e.preventDefault();

    var $wrap = $(this).closest('.js-mpbfix-video-field');
    var $input = $wrap.find('.js-mpbfix-video-id');
    var $preview = $wrap.find('.mpbfix-video-preview');

    var $galleryInput = $('input[name="_mpb_hero_gallery_ids"]');
    var $galleryPreview = $('.mpbfix-gallery-preview');

    var frame = wp.media({
      title: 'mp4動画を選択',
      button: { text: 'この動画を使う' },
      multiple: false,
      library: { type: 'video' }
    });

    frame.on('select', function () {
      var att = frame.state().get('selection').first().toJSON();

      $galleryInput.val('');
      $galleryPreview.html('');

      $input.val(att.id);
      renderVideoPreview($preview, att);
      syncHeroMediaExclusivity();
    });

    frame.open();
  });

  $(document).on('click', '.js-mpbfix-clear-video', function (e) {
    e.preventDefault();

    var $wrap = $(this).closest('.js-mpbfix-video-field');
    $wrap.find('.js-mpbfix-video-id').val('');
    $wrap.find('.mpbfix-video-preview').html('');
    syncHeroMediaExclusivity();
  });

  $(function () {
    syncHeroMediaExclusivity();
  });
})(jQuery);


/* =========================================================
 * MPBFIX REPEAT PLUS FIELDS START
 * ---------------------------------------------------------
 * 役割:
 * - Feature / Guide / Flow / FAQ の入力欄を必要な分だけ開く
 * - HTML上は最大件数分のフィールドを持たせる
 * - 既存入力がある行 + 空欄1行だけ初期表示
 * - 「＋追加」で次の空欄を表示
 *
 * 注意:
 * - 非表示行もDOMには存在するため、既存JSONの件数が保存時に消えにくい
 * - 保存側は空行を除外してJSONメタへ保存する
 * ========================================================= */
(function ($) {
  function rowHasValue($row) {
    var hasValue = false;

    $row.find('input, textarea, select').each(function () {
      var $field = $(this);
      var type = String($field.attr('type') || '').toLowerCase();

      if (type === 'button' || type === 'submit' || type === 'reset') {
        return;
      }

      if ($.trim(String($field.val() || '')) !== '') {
        hasValue = true;
        return false;
      }
    });

    return hasValue;
  }

  function setupRepeatSection($section) {
    var $rows = $section.children('.mpbfix-repeat-item');
    var $button = $section.find('.js-mpbfix-repeat-add').first();

    if (!$rows.length || !$button.length) {
      return;
    }

    var lastFilledIndex = -1;

    $rows.each(function (index) {
      if (rowHasValue($(this))) {
        lastFilledIndex = index;
      }
    });

    // 入力済みの最後 + 空欄1件まで表示する。
    var visibleUntil = Math.max(lastFilledIndex + 1, 0);

    $rows.each(function (index) {
      if (index <= visibleUntil) {
        $(this).show();
      } else {
        $(this).hide();
      }
    });

    updateButtonState($section);
  }

  function updateButtonState($section) {
    var $hiddenRows = $section.children('.mpbfix-repeat-item:hidden');
    var $button = $section.find('.js-mpbfix-repeat-add').first();

    if (!$button.length) {
      return;
    }

    if ($hiddenRows.length) {
      $button.prop('disabled', false).text(function (_, currentText) {
        return currentText.replace('（上限）', '');
      });
    } else {
      $button.prop('disabled', true);
      if ($button.text().indexOf('（上限）') === -1) {
        $button.text($button.text() + '（上限）');
      }
    }
  }

  $(function () {
    $('.mpbfix-repeat-section').each(function () {
      setupRepeatSection($(this));
    });
  });

  $(document).on('click', '.js-mpbfix-repeat-add', function (event) {
    event.preventDefault();

    var $section = $(this).closest('.mpbfix-repeat-section');
    var $next = $section.children('.mpbfix-repeat-item:hidden').first();

    if (!$next.length) {
      updateButtonState($section);
      return;
    }

    $next.slideDown(120);
    updateButtonState($section);
  });
})(jQuery);
/* MPBFIX REPEAT PLUS FIELDS END */




/* =========================================================
 * MPBFIX ADMIN SECTION NAV / COLLAPSE START
 * ---------------------------------------------------------
 * B2C管理画面の上部メニューと折りたたみ。
 *
 * 方針:
 * - 基本設定 / Hero は最初から開く
 * - Intro 以降は閉じる
 * - メニュークリック時は対象を開く
 * - 比較表も対象に含める
 * ========================================================= */
(function ($) {
  function slugifyLabel(label, index) {
    return 'mpbfix-section-' + index + '-' + String(label || '')
      .toLowerCase()
      .replace(/\s+/g, '-')
      .replace(/[^\w\-ぁ-んァ-ヶー一-龠]/g, '');
  }

  function setupAdminSectionNav() {
    var $wrap = $('.mpbfix-wrap').first();

    if (!$wrap.length || $wrap.find('.mpbfix-admin-nav').length) {
      return;
    }

    var $sections = $wrap.children('.mpbfix-section');

    if (!$sections.length) {
      return;
    }

    var $nav = $('<nav class="mpbfix-admin-nav" aria-label="B2C管理画面メニュー"></nav>');

    $sections.each(function (index) {
      var $section = $(this);
      var $heading = $section.children('h2').first();

      if (!$heading.length) {
        return;
      }

      var label = $.trim($heading.text());
      var id = $section.attr('id') || slugifyLabel(label, index + 1);

      $section.attr('id', id);
      $section.addClass('mpbfix-is-collapsible');

      // 基本設定とHeroだけ初期表示。Intro以降は閉じる。
      if (index > 1) {
        $section.addClass('mpbfix-is-collapsed');
      } else {
        $section.removeClass('mpbfix-is-collapsed');
      }

      $('<a></a>')
        .attr('href', '#' + id)
        .text(label)
        .appendTo($nav);
    });

    $wrap.prepend($nav);
  }

  $(function () {
    setupAdminSectionNav();
  });

  $(document).on('click', '.mpbfix-is-collapsible > h2', function (event) {
    event.preventDefault();

    var $section = $(this).closest('.mpbfix-section');
    $section.toggleClass('mpbfix-is-collapsed');
  });

  $(document).on('click', '.mpbfix-admin-nav a', function () {
    var target = $(this).attr('href');

    if (!target || target.charAt(0) !== '#') {
      return;
    }

    var $section = $(target);

    if ($section.length) {
      $section.removeClass('mpbfix-is-collapsed');

      setTimeout(function () {
        try {
          document.querySelector(target).scrollIntoView({
            behavior: 'smooth',
            block: 'start'
          });
        } catch (e) {
          window.location.hash = target;
        }
      }, 30);
    }
  });
})(jQuery);
/* MPBFIX ADMIN SECTION NAV / COLLAPSE END */


/* =========================================================
 * MPBFIX ADMIN COUNT BADGES START
 * ---------------------------------------------------------
 * 役割:
 * - Feature / Guide / Flow / FAQ の入力済み件数を数える
 * - 比較表の入力済み列数 / 行数を数える
 * - 上部メニューとセクション見出しに件数を表示する
 *
 * 注意:
 * - 保存処理ではない。見た目の補助だけ。
 * - 非表示フィールドもDOM上にあるため、既存メタ件数も数えられる。
 * ========================================================= */
(function ($) {
  function fieldHasValue($field) {
    var type = String($field.attr('type') || '').toLowerCase();

    if (type === 'button' || type === 'submit' || type === 'reset') {
      return false;
    }

    return $.trim(String($field.val() || '')) !== '';
  }

  function rowHasValue($row) {
    var hasValue = false;

    $row.find('input, textarea, select').each(function () {
      if (fieldHasValue($(this))) {
        hasValue = true;
        return false;
      }
    });

    return hasValue;
  }

  function countRepeatSection($section) {
    var count = 0;

    $section.children('.mpbfix-repeat-item').each(function () {
      if (rowHasValue($(this))) {
        count++;
      }
    });

    return count;
  }

  function countCompareSection($section) {
    var colCount = 0;
    var rowCount = 0;

    // 比較表は既存HTMLのnameに compare / col / row が含まれる想定。
    // name構造が違っても、入力済みのテーブル系フィールドをざっくり拾う。
    $section.find('input, textarea, select').each(function () {
      var $field = $(this);
      var name = String($field.attr('name') || '');

      if (!fieldHasValue($field)) {
        return;
      }

      if (name.indexOf('compare_col') !== -1 || name.indexOf('compare_columns') !== -1 || name.indexOf('column') !== -1) {
        colCount++;
      }

      if (name.indexOf('compare_row') !== -1 || name.indexOf('compare_rows') !== -1 || name.indexOf('row') !== -1) {
        rowCount++;
      }
    });

    // フィールド単位で多く数えすぎる場合があるので、見出し入力っぽいものだけを優先。
    var label = '';

    if (colCount || rowCount) {
      label = '列' + colCount + ' / 行' + rowCount;
    }

    return label;
  }

  function sectionLabel($section) {
    var label = $.trim($section.children('h2').first().clone().children().remove().end().text());

    if (!label) {
      label = $.trim($section.children('h2').first().text());
    }

    return label;
  }

  function getSectionCountLabel($section) {
    var label = sectionLabel($section);

    if ($section.hasClass('mpbfix-repeat-section')) {
      return String(countRepeatSection($section));
    }

    if (label.indexOf('比較表') !== -1) {
      return countCompareSection($section);
    }

    return '';
  }

  function upsertHeadingBadge($section, countLabel) {
    var $h2 = $section.children('h2').first();

    if (!$h2.length) {
      return;
    }

    if (!$h2.children('.mpbfix-section-title-main').length) {
      var plainText = sectionLabel($section);
      $h2.contents().filter(function () {
        return this.nodeType === 3;
      }).remove();

      $h2.prepend($('<span class="mpbfix-section-title-main"></span>').text(plainText));
    }

    var $main = $h2.children('.mpbfix-section-title-main').first();
    var $badge = $main.children('.mpbfix-count-badge').first();

    if (!countLabel) {
      $badge.remove();
      return;
    }

    if (!$badge.length) {
      $badge = $('<span class="mpbfix-count-badge"></span>').appendTo($main);
    }

    $badge
      .text(countLabel)
      .toggleClass('is-empty', countLabel === '0');
  }

  function upsertNavBadge($navLink, countLabel) {
    var $badge = $navLink.children('.mpbfix-count-badge').first();

    if (!countLabel) {
      $badge.remove();
      return;
    }

    if (!$badge.length) {
      $badge = $('<span class="mpbfix-count-badge"></span>').appendTo($navLink);
    }

    $badge
      .text(countLabel)
      .toggleClass('is-empty', countLabel === '0');
  }

  function refreshCountBadges() {
    var $wrap = $('.mpbfix-wrap').first();

    if (!$wrap.length) {
      return;
    }

    var $nav = $wrap.find('.mpbfix-admin-nav').first();

    $wrap.children('.mpbfix-section').each(function () {
      var $section = $(this);
      var id = $section.attr('id');
      var countLabel = getSectionCountLabel($section);

      upsertHeadingBadge($section, countLabel);

      if ($nav.length && id) {
        var $link = $nav.find('a[href="#' + id + '"]').first();
        if ($link.length) {
          upsertNavBadge($link, countLabel);
        }
      }
    });
  }

  $(function () {
    // 既存のセクションNav生成後に反映させる。
    setTimeout(refreshCountBadges, 80);
  });

  $(document).on('input change', '.mpbfix-wrap input, .mpbfix-wrap textarea, .mpbfix-wrap select', function () {
    refreshCountBadges();
  });

  $(document).on('click', '.js-mpbfix-repeat-add', function () {
    setTimeout(refreshCountBadges, 160);
  });
})(jQuery);
/* MPBFIX ADMIN COUNT BADGES END */


/* =========================================================
 * MPBFIX COMPARE BUILDER ADMIN START
 * ---------------------------------------------------------
 * 役割:
 * - 比較表の列/行を必要な分だけ表示する
 * - ＋列追加 / ＋行追加で次の入力欄を開く
 * - 列が増えたら、各行のセル入力欄も表示する
 *
 * 注意:
 * - 保存処理ではない
 * - 保存は b2c-admin.php の _mpb_compare_table_json 処理が担当
 * ========================================================= */
(function ($) {
  function fieldHasValue($field) {
    var type = String($field.attr('type') || '').toLowerCase();

    if (type === 'button' || type === 'submit' || type === 'reset') {
      return false;
    }

    return $.trim(String($field.val() || '')) !== '';
  }

  function blockHasValue($block) {
    var hasValue = false;

    $block.find('input, textarea, select').each(function () {
      if (fieldHasValue($(this))) {
        hasValue = true;
        return false;
      }
    });

    return hasValue;
  }

  function visibleColumnCount($section) {
    return $section.find('.mpbfix-compare-column:visible').length;
  }

  function refreshCompareCells($section) {
    var count = visibleColumnCount($section);

    $section.find('.mpbfix-compare-cell').each(function () {
      var $cell = $(this);
      var colIndex = parseInt($cell.attr('data-mpbfix-compare-cell-col') || '0', 10);

      if (colIndex < count) {
        $cell.show();
      } else {
        $cell.hide();
      }
    });
  }

  function setupCompareBuilder($section) {
    var $columns = $section.find('.mpbfix-compare-column');
    var $rows = $section.find('.mpbfix-compare-row');

    if (!$columns.length || !$rows.length) {
      return;
    }

    var lastFilledColumn = -1;
    var lastFilledRow = -1;

    $columns.each(function (index) {
      if (blockHasValue($(this))) {
        lastFilledColumn = index;
      }
    });

    $rows.each(function (index) {
      if (blockHasValue($(this))) {
        lastFilledRow = index;
      }
    });

    /*
     * 初期表示:
     * - 列は 入力済み最後 + 空欄1件。ただし最低3列。
     * - 行は 入力済み最後 + 空欄1件。ただし最低1行。
     *
     * 理由:
     * 比較表は2〜3列で使うケースが多いため、
     * 最初から3列見せた方が編集しやすい。
     */
    var showColumnsUntil = Math.max(lastFilledColumn + 1, 2);
    var showRowsUntil = Math.max(lastFilledRow + 1, 0);

    $columns.each(function (index) {
      $(this).toggle(index <= showColumnsUntil);
    });

    $rows.each(function (index) {
      $(this).toggle(index <= showRowsUntil);
    });

    refreshCompareCells($section);
    updateCompareButtons($section);
  }

  function updateCompareButtons($section) {
    var $nextCol = $section.find('.mpbfix-compare-column:hidden').first();
    var $nextRow = $section.find('.mpbfix-compare-row:hidden').first();

    $section.find('[data-mpbfix-compare-add="col"]').prop('disabled', !$nextCol.length);
    $section.find('[data-mpbfix-compare-add="row"]').prop('disabled', !$nextRow.length);
  }

  $(function () {
    $('.mpbfix-compare-builder-section').each(function () {
      setupCompareBuilder($(this));
    });
  });

  $(document).on('click', '.js-mpbfix-compare-add', function (event) {
    event.preventDefault();

    var $button = $(this);
    var type = $button.attr('data-mpbfix-compare-add');
    var $section = $button.closest('.mpbfix-compare-builder-section');

    if (type === 'col') {
      $section.find('.mpbfix-compare-column:hidden').first().slideDown(120);
      refreshCompareCells($section);
    }

    if (type === 'row') {
      $section.find('.mpbfix-compare-row:hidden').first().slideDown(120);
      refreshCompareCells($section);
    }

    updateCompareButtons($section);
  });
})(jQuery);
/* MPBFIX COMPARE BUILDER ADMIN END */




/* =========================================================
 * MPBFIX HIDE UNRELATED METABOXES START
 * ---------------------------------------------------------
 * B2C固定ページ編集画面で不要なメタボックスを隠す。
 *
 * 隠すもの:
 * - 平屋プラン一覧設定
 * - 注文住宅ページ追加設定
 * - Hub ページ設定
 * - 動画フォーマットID入力
 *
 * 注意:
 * - 表示だけ消す。保存データは消さない。
 * ========================================================= */
(function ($) {
  function isB2CEditor() {
    return $('.mpbfix-wrap').length > 0 || $('[name="naigai_mpbfix_meta_box_present"]').val() === '1';
  }

  function hideUnrelatedMetaboxes() {
    if (!isB2CEditor()) {
      return;
    }

    var pattern = /(平屋プラン一覧設定|注文住宅ページ追加設定|Hub\s*ページ設定|動画フォーマットID入力)/;

    $('.postbox').each(function () {
      var $box = $(this);
      var title = $.trim($box.find('.postbox-header h2, h2.hndle, .hndle').first().text());

      if (pattern.test(title)) {
        $box.attr('data-mpbfix-hidden-unrelated', '1').hide();
      }
    });
  }

  $(function () {
    hideUnrelatedMetaboxes();
    setTimeout(hideUnrelatedMetaboxes, 300);
    setTimeout(hideUnrelatedMetaboxes, 900);
  });
})(jQuery);
/* MPBFIX HIDE UNRELATED METABOXES END */
