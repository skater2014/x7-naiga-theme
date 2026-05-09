(function () {
  function esc(s) {
    return String(s || '').replace(/[&<>"']/g, function (c) {
      return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[c];
    });
  }

  function itemHtml(item) {
    if (!item) return '';

    if (item.type === 'video') {
      return '<video class="ch-hero__video" autoplay muted loop playsinline preload="metadata">' +
        '<source src="' + esc(item.src) + '" type="video/mp4">' +
        '</video>';
    }

    return '<img class="ch-hero__image" src="' + esc(item.src) + '" alt="' + esc(item.alt || '') + '">';
  }

  function swiperHtml(items) {
    var html = '<div class="swiper js-ch-hero-swiper ch-hero-swiper"><div class="swiper-wrapper">';

    items.forEach(function (item) {
      html += '<div class="swiper-slide">' + itemHtml(item) + '</div>';
    });

    html += '</div></div>';
    return html;
  }

  function findScope() {
    return document.querySelector(
      '.ch-subpage-hero.ch-hero,.ch-hero.ch-fullbleed.ch-subpage-hero,.hub-customhome-subpage__hero,.ch-page-hero'
    );
  }

  function findHost(scope) {
    var host = scope.querySelector('.ch-hero__media');

    if (!host) {
      host = document.createElement('div');
      host.className = 'ch-hero__media';
      scope.insertBefore(host, scope.firstChild);
    }

    return host;
  }

  function clean(scope, host) {
    scope.querySelectorAll('.ch-hero-ui').forEach(function (el) {
      el.remove();
    });

    scope.querySelectorAll('.ch-hero__media').forEach(function (el) {
      if (el !== host) el.remove();
    });

    host.innerHTML = '';
  }

  function createUi(scope, items, config, swiper) {
    if (config.mode !== 'swiper' || items.length <= 1) return;

    var ui = document.createElement('div');
    ui.className = 'ch-hero-ui';

    if (config.navigation) {
      var prev = document.createElement('button');
      prev.type = 'button';
      prev.className = 'ch-hero-ui__arrow ch-hero-ui__arrow--prev';
      prev.setAttribute('aria-label', '前の画像へ');
      prev.innerHTML = '‹';

      var next = document.createElement('button');
      next.type = 'button';
      next.className = 'ch-hero-ui__arrow ch-hero-ui__arrow--next';
      next.setAttribute('aria-label', '次の画像へ');
      next.innerHTML = '›';

      prev.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        if (swiper && typeof swiper.slidePrev === 'function') swiper.slidePrev();
      });

      next.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        if (swiper && typeof swiper.slideNext === 'function') swiper.slideNext();
      });

      ui.appendChild(prev);
      ui.appendChild(next);
    }

    if (config.pagination) {
      var dots = document.createElement('div');
      dots.className = 'ch-hero-ui__dots';

      items.forEach(function (_, index) {
        var dot = document.createElement('button');
        dot.type = 'button';
        dot.className = 'ch-hero-ui__dot';
        dot.setAttribute('aria-label', index + 1 + '枚目へ');

        dot.addEventListener('click', function (e) {
          e.preventDefault();
          e.stopPropagation();

          if (!swiper) return;

          if (typeof swiper.slideToLoop === 'function') {
            swiper.slideToLoop(index);
          } else {
            swiper.slideTo(index);
          }
        });

        dots.appendChild(dot);
      });

      ui.appendChild(dots);
    }

    scope.appendChild(ui);

    function updateDots() {
      var real = swiper && typeof swiper.realIndex === 'number' ? swiper.realIndex : 0;

      ui.querySelectorAll('.ch-hero-ui__dot').forEach(function (dot, index) {
        dot.classList.toggle('is-active', index === real);
      });
    }

    if (swiper && typeof swiper.on === 'function') {
      swiper.on('slideChange', updateDots);
    }

    updateDots();
  }

  function initSwiper(swiperEl, config) {
    if (typeof window.Swiper !== 'function') return null;

    if (swiperEl.swiper) {
      try {
        swiperEl.swiper.destroy(true, true);
      } catch (e) {}
    }

    var options = {
      loop: !!config.loop,
      effect: config.effect || 'slide',
      speed: Number(config.speed || 500),
      allowTouchMove: true,
      simulateTouch: true,
      grabCursor: true,
      preventClicks: false,
      preventClicksPropagation: false
    };

    if (options.effect === 'fade') {
      options.fadeEffect = { crossFade: true };
    }

    if (config.autoplay) {
      options.autoplay = {
        delay: Number(config.delay || 2500),
        disableOnInteraction: false
      };
    }

    var sw = new window.Swiper(swiperEl, options);
    swiperEl.dataset.swiperReady = '1';
    window.NAIGAI_CH_SWIPER = sw;

    return sw;
  }

  function render(retry) {
    retry = retry || 0;

    var items = window.NAIGAI_CH_HERO_MEDIA || [];
    var config = window.NAIGAI_CH_HERO_CONFIG || {};
    var scope = findScope();

    if (!scope) return;

    var host = findHost(scope);
    clean(scope, host);

    host.setAttribute('data-ch-hero-bridge', '1');
    host.setAttribute('data-ch-hero-mode', config.mode || '');
    host.setAttribute('data-ch-hero-count', String(items.length || 0));
    host.setAttribute('data-ch-hero-effect', config.effect || '');
    host.setAttribute('data-ch-hero-pagination', config.pagination ? '1' : '0');
    host.setAttribute('data-ch-hero-navigation', config.navigation ? '1' : '0');
    host.setAttribute('data-ch-hero-autoplay', config.autoplay ? '1' : '0');

    if (!items.length) {
      host.classList.add('is-empty');
      return;
    }

    host.classList.remove('is-empty');

    if (config.mode !== 'swiper' || items.length <= 1) {
      host.innerHTML = itemHtml(items[0]);
      return;
    }

    if (typeof window.Swiper !== 'function') {
      if (retry < 30) {
        setTimeout(function () {
          render(retry + 1);
        }, 120);
      }
      return;
    }

    host.innerHTML = swiperHtml(items);

    var swiperEl = host.querySelector('.js-ch-hero-swiper');
    var sw = initSwiper(swiperEl, config);

    createUi(scope, items, config, sw);
  }

  document.addEventListener('DOMContentLoaded', function () {
    render(0);
  });

  window.addEventListener('load', function () {
    render(0);
  });
})();
