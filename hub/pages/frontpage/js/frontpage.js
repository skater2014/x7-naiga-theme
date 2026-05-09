/**
 * Frontpage JS
 * PC: HeroメディアはCSSグリッド
 * 1200px以下: Heroメディアを横Swiper化
 */
(function () {
  'use strict';

  var mq = window.matchMedia('(max-width: 1200px)');
  var swiper = null;

  function autoplayMp4(scope) {
    (scope || document).querySelectorAll('.fp-hero-media__video').forEach(function (video) {
      video.muted = true;
      video.playsInline = true;
      video.loop = true;

      var play = video.play();
      if (play && typeof play.catch === 'function') {
        play.catch(function () {});
      }
    });
  }

  function getHeroMedia() {
    return document.querySelector('.fp-hero-media--adaptive');
  }

  function getHeroItems(root) {
    return Array.prototype.slice.call(root.children).filter(function (el) {
      return el.classList && el.classList.contains('fp-hero-media__item');
    });
  }

  function setupTabletSwiper() {
    var heroMedia = getHeroMedia();

    if (!heroMedia) {
      return;
    }

    if (heroMedia.dataset.mobileSwiperReady === '1') {
      autoplayMp4(heroMedia);
      return;
    }

    var items = getHeroItems(heroMedia);

    if (items.length <= 1) {
      autoplayMp4(heroMedia);
      return;
    }

    heroMedia.classList.add('swiper', 'fp-hero-media--mobile-swiper');

    var wrapper = document.createElement('div');
    wrapper.className = 'swiper-wrapper fp-hero-media__mobile-wrapper';

    items.forEach(function (item) {
      item.classList.add('swiper-slide');
      wrapper.appendChild(item);
    });

    heroMedia.appendChild(wrapper);

    var pagination = document.createElement('div');
    pagination.className = 'swiper-pagination fp-hero-media__mobile-pagination';
    heroMedia.appendChild(pagination);

    if (typeof window.Swiper === 'function') {
      swiper = new window.Swiper(heroMedia, {
        loop: true,
        speed: 700,
        slidesPerView: 1.12,
        spaceBetween: 14,
        centeredSlides: true,
        watchOverflow: true,
        autoplay: {
          delay: 2400,
          disableOnInteraction: false,
          pauseOnMouseEnter: false
        },
        pagination: {
          el: pagination,
          clickable: true
        },
        breakpoints: {
          700: {
            slidesPerView: 1.55,
            centeredSlides: false,
            spaceBetween: 16
          },
          960: {
            slidesPerView: 2.2,
            centeredSlides: false,
            spaceBetween: 18
          }
        },
        on: {
          init: function () {
            autoplayMp4(heroMedia);
          },
          slideChangeTransitionEnd: function () {
            autoplayMp4(heroMedia);
          }
        }
      });
    } else {
      heroMedia.classList.add('fp-hero-media--snap-fallback');
    }

    heroMedia.dataset.mobileSwiperReady = '1';
    autoplayMp4(heroMedia);
  }

  function destroyTabletSwiper() {
    var heroMedia = getHeroMedia();

    if (!heroMedia || heroMedia.dataset.mobileSwiperReady !== '1') {
      return;
    }

    if (swiper && swiper.destroy) {
      swiper.destroy(true, true);
      swiper = null;
    }

    var wrapper = heroMedia.querySelector('.fp-hero-media__mobile-wrapper');
    var items = wrapper
      ? Array.prototype.slice.call(wrapper.querySelectorAll('.fp-hero-media__item'))
      : Array.prototype.slice.call(heroMedia.querySelectorAll('.fp-hero-media__item'));

    items.forEach(function (item) {
      item.classList.remove('swiper-slide');
      heroMedia.appendChild(item);
    });

    if (wrapper) {
      wrapper.remove();
    }

    heroMedia.querySelectorAll('.fp-hero-media__mobile-pagination').forEach(function (el) {
      el.remove();
    });

    heroMedia.classList.remove('swiper', 'fp-hero-media--mobile-swiper', 'fp-hero-media--snap-fallback');
    delete heroMedia.dataset.mobileSwiperReady;

    autoplayMp4(heroMedia);
  }

  function syncHeroSwiper() {
    if (mq.matches) {
      setupTabletSwiper();
    } else {
      destroyTabletSwiper();
    }
  }

  document.addEventListener('DOMContentLoaded', syncHeroSwiper);

  window.addEventListener('resize', function () {
    clearTimeout(window.__frontpageHeroSwiperTimer);
    window.__frontpageHeroSwiperTimer = setTimeout(syncHeroSwiper, 120);
  });

  if (mq.addEventListener) {
    mq.addEventListener('change', syncHeroSwiper);
  } else if (mq.addListener) {
    mq.addListener(syncHeroSwiper);
  }
})();

/**
 * Frontpage Life Cards Swiper
 * - 矢印なし
 * - 丸ドットページャーあり
 * - autoplayなし
 * - loopなし
 * - カードの余りを少し見せる
 * - Hero Swiperには触らない
 */
(function () {
  'use strict';

  function setupLifeSwiper() {
    var el = document.querySelector('.js-fp-life-swiper');

    if (!el || !window.Swiper || el.dataset.lifeSwiperReady === '1') {
      return;
    }

    var slideCount = el.querySelectorAll('.swiper-slide').length;
    if (!slideCount) {
      return;
    }

    /* 矢印ボタンは不要なので、HTMLに残っていても消す */
    el.querySelectorAll('.fp-life__nav').forEach(function (button) {
      button.remove();
    });

    /* 丸ドット用ページャー。HTMLになければJSで作る */
    var paginationEl = el.querySelector('.fp-life__pagination');
    if (!paginationEl) {
      paginationEl = document.createElement('div');
      paginationEl.className = 'fp-life__pagination';
      el.appendChild(paginationEl);
    }

    new window.Swiper(el, {
      slidesPerView: 1.14,
      spaceBetween: 14,
      speed: 520,
      loop: false,
      rewind: true,
      watchOverflow: true,
      pagination: {
        el: paginationEl,
        clickable: true
      },
      breakpoints: {
        640: {
          slidesPerView: 1.25,
          spaceBetween: 16
        },
        900: {
          slidesPerView: 1.65,
          spaceBetween: 18
        },
        1200: {
          slidesPerView: 2.08,
          spaceBetween: 20
        }
      }
    });

    el.dataset.lifeSwiperReady = '1';
  }

  document.addEventListener('DOMContentLoaded', setupLifeSwiper);
})();

