(function () {
  'use strict';

  function getSlides(hero) {
    return Array.prototype.slice.call(hero.querySelectorAll('[data-iez-hero-slide]'));
  }

  function setText(el, text) {
    if (!el) {
      return;
    }

    text = text || '';
    el.textContent = text;
    el.hidden = text === '';
  }

  function runTextMotion(hero, slide) {
    var motion = (slide && slide.getAttribute('data-caption-motion')) ||
      hero.getAttribute('data-iez-caption-motion') ||
      'none';

    hero.setAttribute('data-iez-caption-motion', motion);
    hero.classList.remove('is-text-motion-run');

    if (motion === 'none') {
      return;
    }

    void hero.offsetWidth;
    hero.classList.add('is-text-motion-run');
  }

  function updateSlideText(hero, slide) {
    if (!slide) {
      return;
    }

    var caption = hero.querySelector('[data-iez-hero-caption]');
    var title = hero.querySelector('[data-iez-hero-title]');
    var lead = hero.querySelector('[data-iez-hero-lead]');

    setText(caption, slide.getAttribute('data-caption') || '');
    setText(title, slide.getAttribute('data-title') || (title ? title.textContent : ''));
    setText(lead, slide.getAttribute('data-lead') || '');

    runTextMotion(hero, slide);
  }

  function createBurnsNav(hero, onPrev, onNext) {
    hero.querySelectorAll('.iez-hero__nav').forEach(function (button) {
      button.remove();
    });

    var prev = document.createElement('button');
    prev.type = 'button';
    prev.className = 'iez-hero__nav iez-hero__nav--prev';
    prev.setAttribute('aria-label', '前の画像');

    var next = document.createElement('button');
    next.type = 'button';
    next.className = 'iez-hero__nav iez-hero__nav--next';
    next.setAttribute('aria-label', '次の画像');

    prev.addEventListener('click', onPrev);
    next.addEventListener('click', onNext);

    hero.appendChild(prev);
    hero.appendChild(next);
  }

  function initManualSlides(hero) {
    var slides = getSlides(hero);

    if (!slides.length) {
      return;
    }

    var interval = parseInt(hero.getAttribute('data-iez-hero-interval') || '9000', 10);
    interval = Number.isFinite(interval) && interval >= 4000 ? interval : 9000;

    var index = 0;
    var timer = null;

    function show(nextIndex) {
      slides[index].classList.remove('is-active');
      index = (nextIndex + slides.length) % slides.length;

      var next = slides[index];
      next.classList.remove('is-active');
      void next.offsetWidth;
      next.classList.add('is-active');

      updateSlideText(hero, next);
    }

    function restart() {
      if (timer) {
        window.clearInterval(timer);
      }

      if (slides.length > 1) {
        timer = window.setInterval(function () {
          show(index + 1);
        }, interval);
      }
    }

    slides.forEach(function (slide, i) {
      slide.classList.toggle('is-active', i === 0);
    });

    updateSlideText(hero, slides[0]);

    if (slides.length > 1) {
      createBurnsNav(
        hero,
        function () {
          show(index - 1);
          restart();
        },
        function () {
          show(index + 1);
          restart();
        }
      );
    }

    restart();
  }

  function initSwiper(hero) {
    var swiperEl = hero.querySelector('.iez-hero__swiper');

    hero.querySelectorAll('.iez-hero__nav').forEach(function (button) {
      button.remove();
    });

    if (swiperEl && window.Swiper) {
      var slides = getSlides(hero);

      new window.Swiper(swiperEl, {
        loop: true,
        speed: 900,
        slidesPerView: 1,
        autoplay: {
          delay: parseInt(hero.getAttribute('data-iez-hero-interval') || '9000', 10),
          disableOnInteraction: false
        },
        pagination: {
          el: hero.querySelector('.iez-hero__swiper-pagination'),
          clickable: true
        },
        navigation: {
          nextEl: hero.querySelector('.iez-hero__swiper-next'),
          prevEl: hero.querySelector('.iez-hero__swiper-prev')
        },
        on: {
          init: function () {
            updateSlideText(hero, slides[this.realIndex || 0] || slides[0]);
          },
          slideChangeTransitionStart: function () {
            updateSlideText(hero, slides[this.realIndex || 0] || slides[0]);
          }
        }
      });

      return;
    }

    initManualSlides(hero);
  }

  function initVideo(hero) {
    var video = hero.querySelector('video.iez-hero__video');
    var slide = hero.querySelector('[data-iez-hero-slide]');

    hero.querySelectorAll('.iez-hero__nav').forEach(function (button) {
      button.remove();
    });

    updateSlideText(hero, slide);

    if (!video) {
      return;
    }

    video.muted = true;
    video.playsInline = true;

    var promise = video.play();
    if (promise && typeof promise.catch === 'function') {
      promise.catch(function () {});
    }
  }

  function initHero(hero) {
    var engine = hero.getAttribute('data-iez-hero-engine') || 'burns';

    if (engine === 'fade') {
      engine = 'burns';
      hero.setAttribute('data-iez-hero-engine', 'burns');
    }

    if (engine === 'video') {
      initVideo(hero);
      return;
    }

    if (engine === 'swiper') {
      initSwiper(hero);
      return;
    }

    initManualSlides(hero);
  }

  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-iez-hero]').forEach(initHero);
  });
})();
