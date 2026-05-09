document.addEventListener('DOMContentLoaded', function () {
  var sliders = document.querySelectorAll('.js-hub-swiper');

  if (!sliders.length || typeof window.Swiper !== 'function') {
    return;
  }

  sliders.forEach(function (slider) {
    if (slider.swiper) {
      return;
    }

    slider.classList.add('is-swiper-ready');

    var scope =
      slider.closest('.hub-hero-section, .hub-hero__media, .hub-card__media, .hub-hero__grid, .hub-section, .hub-page-realestate') ||
      slider.parentElement ||
      slider;

    var pagination =
      slider.querySelector('.swiper-pagination') ||
      scope.querySelector('.swiper-pagination');

    var nextEl =
      slider.querySelector('.swiper-button-next') ||
      scope.querySelector('.swiper-button-next');

    var prevEl =
      slider.querySelector('.swiper-button-prev') ||
      scope.querySelector('.swiper-button-prev');

    var slideCount = slider.querySelectorAll('.swiper-slide').length;

    var swiper = new window.Swiper(slider, {
      slidesPerView: 1,
      spaceBetween: 0,
      loop: slideCount > 1,
      watchOverflow: true,
      observer: true,
      observeParents: true,
      autoHeight: false,
      pagination: pagination ? {
        el: pagination,
        clickable: true
      } : false,
      navigation: nextEl && prevEl ? {
        nextEl: nextEl,
        prevEl: prevEl
      } : false
    });

    if (nextEl) {
      nextEl.addEventListener('click', function (event) {
        event.preventDefault();
        event.stopPropagation();
        swiper.slideNext();
      });
    }

    if (prevEl) {
      prevEl.addEventListener('click', function (event) {
        event.preventDefault();
        event.stopPropagation();
        swiper.slidePrev();
      });
    }
  });
});
