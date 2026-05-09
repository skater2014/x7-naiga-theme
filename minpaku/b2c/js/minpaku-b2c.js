/**
 * =========================================================
 * minpaku B2C Swiper / CTA video controller
 * =========================================================
 *
 * 役割:
 * - 民泊B2CページのHeroスライダーを動かす
 * - 民泊B2CページのCTAスライダーを動かす
 * - CTA内の動画を data-autoplay="1" のときだけ自動再生する
 *
 * 対象HTML:
 * - .mnpk-hero-swiper
 * - .mnpk-cta-swiper
 * - .mnpk-page-cta__video[data-autoplay="1"]
 *
 * このJSが担当しないもの:
 * - 予約カレンダーモーダル
 * - チェックイン / チェックアウト選択
 * - data-calendar-day
 * - data-apply-dates
 * - data-direct-checkin
 * - data-direct-checkout
 *
 * つまり、日付選択モーダルの不具合はこのJSでは直らない。
 * このJSは「Swiper」と「CTA動画」専用。
 * =========================================================
 */

(function () {
  'use strict';

  /**
   * 同じJSが複数回読み込まれた場合でも、
   * Swiper初期化処理が二重に走らないようにする保険。
   */
  if (window.__naigaiMinpakuSwiperInitDone) {
    return;
  }

  window.__naigaiMinpakuSwiperInitDone = true;

  /**
   * ---------------------------------------------------------
   * data-* 属性を boolean に変換する関数
   * ---------------------------------------------------------
   *
   * HTML側では data-autoplay="1" や data-loop="true" のように
   * 文字列で入ってくるため、JS側で true / false に変換する。
   *
   * true 扱い:
   * - 1
   * - true
   * - on
   * - yes
   *
   * 値が空の場合は fallback を使う。
   */
  function toBool(value, fallback) {
    if (value === undefined || value === null || value === '') {
      return fallback;
    }

    return ['1', 'true', 'on', 'yes'].includes(String(value).toLowerCase());
  }

  /**
   * ---------------------------------------------------------
   * Swiper初期化
   * ---------------------------------------------------------
   *
   * 対象:
   * - .mnpk-hero-swiper
   * - .mnpk-cta-swiper
   *
   * HTML側の data-* を読んで動作を変える。
   *
   * 使用する data-*:
   * - data-loop="1"
   * - data-autoplay="1"
   * - data-pagination="1"
   * - data-navigation="1"
   * - data-delay="5000"
   */
  function initMinpakuSwipers() {
    /**
     * Swiper本体が読み込まれていなければ何もしない。
     *
     * ここで止まる場合:
     * - Swiper JSが読み込まれていない
     * - enqueue順がおかしい
     * - SwiperのCDNまたはローカルファイルが死んでいる
     */
    if (typeof Swiper === 'undefined') {
      return;
    }

    document.querySelectorAll('.mnpk-hero-swiper').forEach(function (swiperEl) {
      /**
       * 要素がない、または初期化済みならスキップ。
       *
       * data-swiper-ready="1" を付けて、
       * new Swiper() が同じ要素に複数回走るのを防ぐ。
       */
      if (!swiperEl || swiperEl.dataset.swiperReady === '1') {
        return;
      }

      swiperEl.dataset.swiperReady = '1';

      /**
       * HTML側の data-* 属性からSwiper設定を取得。
       */
      var loopEnabled = toBool(swiperEl.dataset.loop, true);
      var autoplayEnabled = toBool(swiperEl.dataset.autoplay, false);
      var paginationEnabled = toBool(swiperEl.dataset.pagination, true);
      var navigationEnabled = toBool(swiperEl.dataset.navigation, false);
      var delay = parseInt(swiperEl.dataset.delay || '5000', 10);

      if (!delay || delay < 1) {
        delay = 5000;
      }

      /**
       * Swiper共通設定。
       *
       * allowTouchMove:
       * - スマホでスワイプできるようにする
       *
       * simulateTouch:
       * - PCでもドラッグ操作できるようにする
       *
       * watchOverflow:
       * - スライドが1枚しかない場合に無駄な動作を抑える
       *
       * preventClicks: false:
       * - スライド内のリンクやボタンがクリックできなくなる事故を防ぐ
       */
      var config = {
        loop: loopEnabled,
        slidesPerView: 1,
        speed: 700,
        allowTouchMove: true,
        simulateTouch: true,
        grabCursor: true,
        watchOverflow: true,
        touchStartPreventDefault: false,
        preventClicks: false,
        preventClicksPropagation: false,
      };

      /**
       * 自動再生。
       *
       * data-autoplay="1" のときだけ有効。
       */
      if (autoplayEnabled) {
        config.autoplay = {
          delay: delay,
          disableOnInteraction: false,
          pauseOnMouseEnter: true,
        };
      }

      /**
       * ページネーション。
       *
       * Hero / CTA どちらにも対応できるように、
       * 複数の候補クラスから探す。
       */
      if (paginationEnabled) {
        var paginationEl = swiperEl.querySelector(
          '.mnpk-hero-swiper__pagination, .mnpk-cta-swiper__pagination, .swiper-pagination',
        );

        if (paginationEl) {
          config.pagination = {
            el: paginationEl,
            clickable: true,
          };
        }
      }

      /**
       * 前後ナビゲーション。
       *
       * data-navigation="1" のときだけ有効。
       *
       * Hero:
       * - .mnpk-hero-swiper__nav--prev
       * - .mnpk-hero-swiper__nav--next
       *
       * CTA:
       * - .mnpk-cta-swiper__nav--prev
       * - .mnpk-cta-swiper__nav--next
       */
      if (navigationEnabled) {
        var prevEl = swiperEl.querySelector(
          '.mnpk-hero-swiper__nav--prev, .mnpk-cta-swiper__nav--prev, .swiper-button-prev',
        );

        var nextEl = swiperEl.querySelector(
          '.mnpk-hero-swiper__nav--next, .mnpk-cta-swiper__nav--next, .swiper-button-next',
        );

        if (prevEl && nextEl) {
          config.navigation = {
            prevEl: prevEl,
            nextEl: nextEl,
          };
        }
      }

      /**
       * Swiper起動。
       */
      new Swiper(swiperEl, config);
    });
  }

  /**
   * ---------------------------------------------------------
   * CTA動画 autoplay
   * ---------------------------------------------------------
   *
   * 対象:
   * - .mnpk-page-cta__video[data-autoplay="1"]
   *
   * muted = true にする理由:
   * - ブラウザは音声付き動画の自動再生をブロックすることが多い
   * - muted にすると autoplay が通りやすい
   */
  function initCtaVideoAutoplay() {
    document.querySelectorAll('.mnpk-page-cta__video[data-autoplay="1"]').forEach(function (video) {
      if (!video) {
        return;
      }

      video.muted = true;

      var promise = video.play();

      /**
       * Safari / Chrome などで autoplay が拒否された場合でも、
       * JSエラーとして画面を壊さない。
       */
      if (promise && typeof promise.catch === 'function') {
        promise.catch(function () {});
      }
    });
  }

  /**
   * ---------------------------------------------------------
   * DOM読み込み後に実行
   * ---------------------------------------------------------
   */
  document.addEventListener('DOMContentLoaded', function () {
    initMinpakuSwipers();
    initCtaVideoAutoplay();
  });
})();


/* =========================================================
 * B2C CTA Swiper stable init
 *
 * 対象:
 * - .mnpk-cta-swiper
 *
 * 方針:
 * - CTA Swiper はここだけで初期化する
 * - マウスドラッグ由来の不安定さを避ける
 * - autoplay / pagination / navigation はHTML出力に従う
 * ========================================================= */
(function () {
  'use strict';

  function getDelay(el) {
    var delay = parseInt(el.getAttribute('data-delay') || '5000', 10);
    return Number.isFinite(delay) && delay >= 1000 ? delay : 5000;
  }

  function initCtaSwiper(el) {
    if (!el || typeof window.Swiper === 'undefined') {
      return;
    }

    var slides = el.querySelectorAll('.swiper-slide');

    if (slides.length <= 1) {
      return;
    }

    if (el.swiper) {
      el.swiper.update();
      return;
    }

    var pagination = el.querySelector('.swiper-pagination');
    var prev = el.querySelector('.swiper-button-prev, .mnpk-cta-swiper__nav--prev');
    var next = el.querySelector('.swiper-button-next, .mnpk-cta-swiper__nav--next');
    var autoplayEnabled = el.getAttribute('data-autoplay') === '1';

    var options = {
      slidesPerView: 1,
      spaceBetween: 0,
      loop: el.getAttribute('data-loop') !== '0' && slides.length > 1,
      speed: 650,
      watchOverflow: true,
      observer: true,
      observeParents: true,
      resizeObserver: true,

      /*
       * PCのマウスドラッグをOFF。
       * 変なgrab/drag pointerとクリック暴発を避ける。
       */
      grabCursor: false,
      simulateTouch: false,
      touchStartPreventDefault: false,
      preventClicks: false,
      preventClicksPropagation: false
    };

    if (pagination) {
      options.pagination = {
        el: pagination,
        clickable: true
      };
    }

    if (prev && next) {
      options.navigation = {
        prevEl: prev,
        nextEl: next
      };
    }

    if (autoplayEnabled) {
      options.autoplay = {
        delay: getDelay(el),
        disableOnInteraction: false
      };
    }

    new window.Swiper(el, options);
  }

  function initAllCtaSwipers() {
    document.querySelectorAll('.mnpk-cta-swiper').forEach(initCtaSwiper);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAllCtaSwipers);
  } else {
    initAllCtaSwipers();
  }

  window.addEventListener('load', initAllCtaSwipers);
})();

