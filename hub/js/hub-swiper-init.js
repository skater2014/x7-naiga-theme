document.addEventListener('DOMContentLoaded', function () {
  var sliders = document.querySelectorAll('.js-hub-swiper');

  if (!sliders.length || typeof window.Swiper !== 'function') {
    return;
  }

  sliders.forEach(function (slider) {
    slider.classList.add('is-swiper-ready');

    var pagination = slider.querySelector('.swiper-pagination');
    var nextEl = slider.querySelector('.swiper-button-next');
    var prevEl = slider.querySelector('.swiper-button-prev');
    var slideCount = slider.querySelectorAll('.swiper-slide').length;

    var swiper = new window.Swiper(slider, {
      slidesPerView: 1,
      spaceBetween: 0,
      loop: slideCount > 1,
      watchOverflow: true,
      observer: true,
      observeParents: true,
      autoHeight: false,

      pagination: pagination
        ? {
            el: pagination,
            clickable: true,
          }
        : undefined,

      navigation:
        nextEl && prevEl
          ? {
              nextEl: nextEl,
              prevEl: prevEl,
            }
          : undefined,
    });

    if (slider.getAttribute('data-front-hero') !== '1') {
      return;
    }

    var heroSection = slider.closest('.hub-hero-section');
    if (!heroSection) {
      return;
    }

    var contentEl = heroSection.querySelector('.js-front-hero-content');
    var titleEl = heroSection.querySelector('.js-front-hero-title');
    var leadEl = heroSection.querySelector('.js-front-hero-lead');
    var leadCard = heroSection.querySelector('.hub-front-hero-card--desc');
    var extraEl = heroSection.querySelector('.js-front-hero-extra');

    if (!contentEl || !titleEl || !leadEl || !extraEl) {
      return;
    }

    var defaultTitle = contentEl.getAttribute('data-default-title') || '';
    var defaultLead = contentEl.getAttribute('data-default-lead') || '';
    var emptyExtraHtml =
      '<p class="hub-front-hero__empty">詳細情報は個別ページでご確認ください。</p>';

    function nl2brSafe(text) {
      return String(text || '').replace(/\n/g, '<br>');
    }

    function applyLinkedContent() {
      var activeIndex =
        typeof swiper.realIndex === 'number' ? swiper.realIndex : swiper.activeIndex;

      var slideEl = slider.querySelector(
        '.swiper-slide[data-slide-index="' + activeIndex + '"]'
      );

      if (!slideEl) {
        return;
      }

      var slideTitle = slideEl.getAttribute('data-slide-title') || '';
      var slideLead = slideEl.getAttribute('data-slide-lead') || '';

      titleEl.textContent = slideTitle !== '' ? slideTitle : defaultTitle;

      var nextLead = slideLead !== '' ? slideLead : defaultLead;

      if (nextLead !== '') {
        leadEl.hidden = false;
        leadEl.innerHTML = nl2brSafe(nextLead);

        if (leadCard) {
          leadCard.classList.remove('is-hidden');
        }
      } else {
        leadEl.hidden = true;
        leadEl.innerHTML = '';

        if (leadCard) {
          leadCard.classList.add('is-hidden');
        }
      }

      var tpl = document.getElementById('front-hero-extra-' + activeIndex);
      extraEl.innerHTML = tpl && tpl.innerHTML.trim() !== '' ? tpl.innerHTML : emptyExtraHtml;
    }

    applyLinkedContent();
    swiper.on('slideChange', applyLinkedContent);
    swiper.on('transitionEnd', applyLinkedContent);
  });
});
