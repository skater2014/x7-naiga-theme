document.addEventListener('DOMContentLoaded', function () {
  var body = document.body;
  var page = document.querySelector('.hub-customhome-page');
  var header = document.querySelector('.ch-site-header');
  var cta = document.querySelector('[data-customhome-cta]');
  var localNav = document.querySelector('[data-customhome-localnav]');
  var menuToggle = document.querySelector('[data-ch-menu-toggle]');
  var drawer = document.querySelector('[data-ch-menu-drawer]');
  var localNavMode = page ? page.getAttribute('data-localnav-mode') : 'hero_to_cta';

  function openDrawer() {
    if (!menuToggle || !drawer) return;
    menuToggle.setAttribute('aria-expanded', 'true');
    drawer.hidden = false;
    body.classList.add('is-customhome-drawer-open');
  }

  function closeDrawer() {
    if (!menuToggle || !drawer) return;
    menuToggle.setAttribute('aria-expanded', 'false');
    drawer.hidden = true;
    body.classList.remove('is-customhome-drawer-open');
  }

  if (menuToggle && drawer) {
    menuToggle.addEventListener('click', function () {
      if (menuToggle.getAttribute('aria-expanded') === 'true') {
        closeDrawer();
      } else {
        openDrawer();
      }
    });

    drawer.addEventListener('click', function (event) {
      if (event.target === drawer) closeDrawer();
    });

    drawer.querySelectorAll('a').forEach(function (link) {
      link.addEventListener('click', closeDrawer);
    });
  }

  function getHashFromLink(link) {
    try {
      var url = new URL(link.href, window.location.origin);
      if (url.pathname === window.location.pathname) {
        return url.hash || '';
      }
    } catch (e) {}
    return '';
  }

  function setActive(hash) {
    if (!localNav) return;
    localNav.querySelectorAll('a').forEach(function (link) {
      var isActive = getHashFromLink(link) === hash;
      link.classList.toggle('is-active', isActive);
      if (link.parentElement) {
        link.parentElement.classList.toggle('is-active', isActive);
      }
    });
  }

  if (localNav) {
    localNav.querySelectorAll('a').forEach(function (link) {
      link.addEventListener('click', function (event) {
        var hash = getHashFromLink(link);
        if (!hash) return;

        var target = document.querySelector(hash);
        if (!target) return;

        event.preventDefault();

        var headerHeight = header ? header.offsetHeight : 0;
        var navHeight = localNav ? localNav.offsetHeight : 0;
        var top = target.getBoundingClientRect().top + window.pageYOffset - headerHeight - navHeight - 18;

        window.scrollTo({ top: top, behavior: 'smooth' });
        setActive(hash);
      });
    });
  }

  var sections = [];
  ['#customhome-feature', '#customhome-works', '#customhome-flow', '#customhome-company', '#customhome-contact'].forEach(function (hash) {
    var el = document.querySelector(hash);
    if (el) sections.push(el);
  });

  if ('IntersectionObserver' in window && sections.length) {
    var observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          setActive('#' + entry.target.id);
        }
      });
    }, {
      root: null,
      rootMargin: '-20% 0px -60% 0px',
      threshold: 0.15
    });

    sections.forEach(function (section) {
      observer.observe(section);
    });
  }

  function syncLocalNavVisibility() {
    if (!localNav || !header) return;

    var headerHeight = header.offsetHeight;
    var startY = localNav.dataset.fixedStartY ? parseFloat(localNav.dataset.fixedStartY) : null;
    if (startY === null || Number.isNaN(startY)) {
      startY = localNav.getBoundingClientRect().top + window.pageYOffset - headerHeight;
      localNav.dataset.fixedStartY = String(startY);
    }

    var shouldFix = window.pageYOffset >= startY;
    var shouldStop = false;

    if (localNavMode === 'hero_to_cta' && cta) {
      var ctaY = cta.getBoundingClientRect().top + window.pageYOffset;
      var endY = startY + ((ctaY - startY) * 0.55);
      shouldStop = window.pageYOffset >= endY;
    }

    body.classList.toggle('has-past-hero', shouldFix && !shouldStop);
  }

  window.addEventListener('scroll', syncLocalNavVisibility, { passive: true });
  window.addEventListener('resize', function () {
    if (localNav) delete localNav.dataset.fixedStartY;
    syncLocalNavVisibility();
  });
  syncLocalNavVisibility();

  var moreButton = document.querySelector('[data-customhome-more-btn]');
  var morePanel = document.getElementById('customhome-works-more');

  if (moreButton && morePanel) {
    moreButton.addEventListener('click', function () {
      var label = moreButton.querySelector('.ch-more-btn__label');
      var hiddenItems = Array.prototype.slice.call(morePanel.querySelectorAll('.ch-work-card[hidden]'));
      var isExpanded = moreButton.getAttribute('aria-expanded') === 'true';

      if (hiddenItems.length === 0 && isExpanded) {
        morePanel.classList.remove('is-visible');
        setTimeout(function () {
          morePanel.hidden = true;
          morePanel.querySelectorAll('.ch-work-card').forEach(function (card) {
            card.hidden = true;
          });
          moreButton.setAttribute('aria-expanded', 'false');
          if (label) label.textContent = '施工事例をもっと見る';
        }, 180);
        return;
      }

      moreButton.classList.add('is-loading');
      if (label) label.textContent = '読み込み中...';

      setTimeout(function () {
        morePanel.hidden = false;
        morePanel.classList.add('is-visible');

        hiddenItems.slice(0, 4).forEach(function (card) {
          card.hidden = false;
        });

        moreButton.classList.remove('is-loading');
        moreButton.setAttribute('aria-expanded', 'true');

        var remain = morePanel.querySelectorAll('.ch-work-card[hidden]').length;
        if (label) {
          label.textContent = remain > 0 ? 'さらに施工事例を読む' : '施工事例を閉じる';
        }
      }, 260);
    });
  }

  document.querySelectorAll('[data-customhome-cta-swiper]').forEach(function (host) {
    var enabled = (host.getAttribute('data-swiper-enabled') || '1') === '1';
    var navOn = (host.getAttribute('data-swiper-navigation') || '1') === '1';
    var pagerOn = (host.getAttribute('data-swiper-pagination') || '1') === '1';
    var autoplayOn = (host.getAttribute('data-swiper-autoplay') || '0') === '1';
    var videoControls = (host.getAttribute('data-video-controls') || '0') === '1';

    host.querySelectorAll('video').forEach(function (video) {
      if (videoControls) {
        video.setAttribute('controls', 'controls');
      } else {
        video.removeAttribute('controls');
      }
    });

    if (typeof Swiper === 'undefined') return;

    var target = host.classList.contains('swiper') ? host : host.querySelector('.swiper');
    if (!target) target = host;

    var slides = target.querySelectorAll('.swiper-slide');
    var prevEl = target.querySelector('.swiper-button-prev');
    var nextEl = target.querySelector('.swiper-button-next');
    var paginationEl = target.querySelector('.swiper-pagination');

    if (prevEl) prevEl.style.display = enabled && navOn && slides.length > 1 ? '' : 'none';
    if (nextEl) nextEl.style.display = enabled && navOn && slides.length > 1 ? '' : 'none';
    if (paginationEl) paginationEl.style.display = enabled && pagerOn && slides.length > 1 ? '' : 'none';

    if (!enabled || slides.length <= 1) {
      if (target.swiper) {
        try { target.swiper.destroy(true, true); } catch (e) {}
      }
      return;
    }

    if (target.swiper) {
      try { target.swiper.destroy(true, true); } catch (e) {}
    }

    new Swiper(target, {
      loop: slides.length > 1,
      slidesPerView: 1,
      speed: 650,
      autoHeight: false,
      watchOverflow: true,
      autoplay: autoplayOn && slides.length > 1 ? {
        delay: 4200,
        disableOnInteraction: false,
        pauseOnMouseEnter: false
      } : false,
      navigation: navOn && prevEl && nextEl ? {
        nextEl: nextEl,
        prevEl: prevEl
      } : false,
      pagination: pagerOn && paginationEl ? {
        el: paginationEl,
        clickable: true
      } : false
    });
  });
});
