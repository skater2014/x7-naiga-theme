/**
 * Frontpage Hero Media Swiper
 * 目的:
 * - tablet / mobile では5枚メディアを縦積みにしない
 * - 横方向に自動で流す
 * - PCではCSSグリッドに戻す
 */
(function () {
  'use strict';

  var mq = window.matchMedia('(max-width: 1200px)');
  var swiperInstance = null;
  var originalParentMap = new WeakMap();

  function getHeroMedia() {
    return document.querySelector('.fp-hero-media--adaptive');
  }

  function enableSwiper() {
    var root = getHeroMedia();
    if (!root || root.classList.contains('fp-hero-media--tablet-swiper')) {
      return;
    }

    var items = Array.prototype.slice.call(root.children).filter(function (el) {
      return el.classList && el.classList.contains('fp-hero-media__item');
    });

    if (items.length <= 1) {
      return;
    }

    root.classList.add('swiper', 'fp-hero-media--tablet-swiper');

    var wrapper = document.createElement('div');
    wrapper.className = 'swiper-wrapper fp-hero-media__swiper-wrapper';

    items.forEach(function (item) {
      originalParentMap.set(item, root);
      item.classList.add('swiper-slide');
      wrapper.appendChild(item);
    });

    root.appendChild(wrapper);

    var pagination = document.createElement('div');
    pagination.className = 'swiper-pagination fp-hero-media__pagination';
    root.appendChild(pagination);

    var prev = document.createElement('button');
    prev.type = 'button';
    prev.className = 'fp-hero-media__nav fp-hero-media__nav--prev';
    prev.setAttribute('aria-label', '前の画像へ');
    prev.textContent = '‹';

    var next = document.createElement('button');
    next.type = 'button';
    next.className = 'fp-hero-media__nav fp-hero-media__nav--next';
    next.setAttribute('aria-label', '次の画像へ');
    next.textContent = '›';

    root.appendChild(prev);
    root.appendChild(next);

    if (window.Swiper) {
      swiperInstance = new Swiper(root, {
        loop: true,
        speed: 650,
        slidesPerView: 1.15,
        spaceBetween: 14,
        centeredSlides: true,
        watchOverflow: true,
        autoplay: {
          delay: 2600,
          disableOnInteraction: false,
          pauseOnMouseEnter: false
        },
        pagination: {
          el: pagination,
          clickable: true
        },
        navigation: {
          prevEl: prev,
          nextEl: next
        },
        breakpoints: {
          700: {
            slidesPerView: 1.65,
            centeredSlides: false,
            spaceBetween: 16
          },
          960: {
            slidesPerView: 2.25,
            centeredSlides: false,
            spaceBetween: 18
          }
        }
      });
    } else {
      root.classList.add('fp-hero-media--snap-fallback');
    }
  }

  function disableSwiper() {
    var root = getHeroMedia();
    if (!root || !root.classList.contains('fp-hero-media--tablet-swiper')) {
      return;
    }

    if (swiperInstance && swiperInstance.destroy) {
      swiperInstance.destroy(true, true);
      swiperInstance = null;
    }

    var wrapper = root.querySelector('.fp-hero-media__swiper-wrapper');
    var items = wrapper
      ? Array.prototype.slice.call(wrapper.querySelectorAll('.fp-hero-media__item'))
      : Array.prototype.slice.call(root.querySelectorAll('.fp-hero-media__item'));

    items.forEach(function (item) {
      item.classList.remove('swiper-slide');
      root.appendChild(item);
    });

    if (wrapper) wrapper.remove();

    root.querySelectorAll('.fp-hero-media__pagination, .fp-hero-media__nav').forEach(function (el) {
      el.remove();
    });

    root.classList.remove('swiper', 'fp-hero-media--tablet-swiper', 'fp-hero-media--snap-fallback');
  }

  function sync() {
    if (mq.matches) {
      enableSwiper();
    } else {
      disableSwiper();
    }
  }

  document.addEventListener('DOMContentLoaded', sync);
  window.addEventListener('resize', function () {
    window.clearTimeout(window.__fpHeroSwiperTimer);
    window.__fpHeroSwiperTimer = window.setTimeout(sync, 120);
  });

  if (mq.addEventListener) {
    mq.addEventListener('change', sync);
  } else if (mq.addListener) {
    mq.addListener(sync);
  }
})();
