/**
 * 家づくりプラン詳細
 * LightGallery 共通モーダル
 *
 * ============================================================
 * 目的
 * ============================================================
 *
 * PC / Mobile 共通で、
 *
 * ・外観写真
 * ・平面図
 * ・配置図
 * ・内装写真
 * ・ページ上部の統合ギャラリーのメイン画像
 *
 * を同じ LightGallery で開く。
 *
 * モーダル内では、
 *
 * ・大きな画像
 * ・前へ / 次へ
 * ・画像枚数
 * ・下部サムネイル
 * ・サムネイルクリックによる画像切替
 *
 * を使用する。
 *
 * ============================================================
 * 重要
 * ============================================================
 *
 * 画像一覧を別に作らない。
 *
 * PHPですでに出力されている
 *
 * data-iez-plan-lightbox
 *
 * のJSONを正本として使用する。
 *
 * また、クリック対象は既存の
 *
 * data-iez-plan-open
 *
 * をそのまま使用する。
 *
 * これにより、iezukuri.js が後から生成する
 * 統合ギャラリーのメイン画像にも対応できる。
 */

(function () {
  'use strict';


  /**
   * ----------------------------------------------------------
   * LightGalleryプラグイン一覧
   * ----------------------------------------------------------
   */
  function collectPlugins() {
    var plugins = [];

    /*
     * モーダル下部のサムネイル。
     */
    if (window.lgThumbnail) {
      plugins.push(window.lgThumbnail);
    }

    /*
     * 拡大表示。
     */
    if (window.lgZoom) {
      plugins.push(window.lgZoom);
    }

    return plugins;
  }


  /**
   * ----------------------------------------------------------
   * data-iez-plan-lightbox を読む
   * ----------------------------------------------------------
   */
  function readLightboxItems(board) {
    var raw =
      board.getAttribute('data-iez-plan-lightbox') || '[]';

    var sourceItems = [];

    try {
      sourceItems = JSON.parse(raw);
    } catch (error) {
      sourceItems = [];
    }

    if (!Array.isArray(sourceItems)) {
      return [];
    }


    /**
     * 現在ページに表示されている小画像を調べる。
     *
     * data-plan-src
     *     = モーダル用の大画像
     *
     * img.src
     *     = ページ表示用の小画像
     *
     * LightGalleryのサムネイルには
     * できるだけ小画像を使用する。
     */
    var thumbByFullUrl = {};

    board.querySelectorAll('[data-plan-src]').forEach(
      function (button) {
        var fullUrl =
          button.getAttribute('data-plan-src') || '';

        var image =
          button.querySelector('img');

        if (!fullUrl || !image) {
          return;
        }

        thumbByFullUrl[fullUrl] =
          image.currentSrc ||
          image.getAttribute('src') ||
          fullUrl;
      }
    );


    /**
     * LightGallery用形式に変換。
     *
     * src
     *   大きく表示する画像
     *
     * thumb
     *   下部サムネイル
     *
     * alt
     *   画像説明
     */
    var seen = {};

    return sourceItems
      .filter(function (item) {
        if (
          !item ||
          !item.src ||
          seen[item.src]
        ) {
          return false;
        }

        seen[item.src] = true;

        return true;
      })
      .map(function (item) {
        return {
          src: item.src,

          thumb:
            thumbByFullUrl[item.src] ||
            item.src,

          alt:
            item.title ||
            '住宅画像',

          subHtml:
            item.title ||
            ''
        };
      });
  }


  /**
   * ----------------------------------------------------------
   * クリックされた画像が何番目か調べる
   * ----------------------------------------------------------
   */
  function findImageIndex(items, src) {
    if (!src) {
      return 0;
    }

    var index = items.findIndex(
      function (item) {
        return item.src === src;
      }
    );

    return index >= 0 ? index : 0;
  }


  /**
   * ----------------------------------------------------------
   * LightGallery初期化
   * ----------------------------------------------------------
   */
  function initPlanLightGallery() {

    if (
      typeof window.lightGallery !==
      'function'
    ) {
      return;
    }


    var boards =
      document.querySelectorAll(
        '.iez-plan-detail-board'
      );


    boards.forEach(function (board) {

      if (!board) {
        return;
      }

      if (
        board.dataset.iezLgReady ===
        '1'
      ) {
        return;
      }


      /*
       * PHPの画像一覧を読む。
       */
      var items =
        readLightboxItems(board);


      if (!items.length) {
        return;
      }


      /*
       * 二重初期化防止。
       */
      board.dataset.iezLgReady = '1';


      /*
       * ------------------------------------------------------
       * Dynamic mode
       * ------------------------------------------------------
       *
       * HTML側に
       *
       * .js-iez-lg-item
       * data-src
       *
       * を追加する必要はない。
       *
       * 既存の
       *
       * data-iez-plan-lightbox
       *
       * をそのままLightGalleryへ渡す。
       */
      var gallery =
        window.lightGallery(
          board,
          {
            dynamic: true,
            dynamicEl: items,

            plugins:
              collectPlugins(),

            controls: true,
            counter: true,

            download: false,

            /*
             * モーダル下部サムネイルを表示。
             */
            thumbnail: true,

            closable: true,
            escKey: true,

            keyPress: true,

            enableDrag: true,
            enableSwipe: true,

            hideBarsDelay: 3000,

            speed: 300,
            preload: 2,

            mobileSettings: {
              controls: true,
              showCloseIcon: true,
              download: false
            }
          }
        );


      /**
       * ------------------------------------------------------
       * 画像クリック
       * ------------------------------------------------------
       *
       * boardでイベントを受けるため、
       * 後からJSで追加された
       * 統合ギャラリーのメイン画像にも対応する。
       */
      board.addEventListener(
        'click',
        function (event) {

          var open =
            event.target.closest(
              '[data-iez-plan-open]'
            );


          if (
            !open ||
            !board.contains(open)
          ) {
            return;
          }


          /*
           * 旧独自モーダルへイベントを渡さない。
           *
           * これで
           *
           * LightGallery
           * ＋
           * 旧モーダル
           *
           * が二重に開くのを防ぐ。
           */
          event.preventDefault();
          event.stopPropagation();


          var src =
            open.getAttribute(
              'data-plan-src'
            ) || '';


          /*
           * 現在クリックした画像の位置から開く。
           */
          var index =
            findImageIndex(
              items,
              src
            );


          gallery.openGallery(index);
        }
      );
    });
  }


  /**
   * DOM完成後に実行。
   */
  if (
    document.readyState ===
    'loading'
  ) {
    document.addEventListener(
      'DOMContentLoaded',
      initPlanLightGallery
    );
  } else {
    initPlanLightGallery();
  }

})();
