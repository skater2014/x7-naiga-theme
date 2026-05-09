document.addEventListener('DOMContentLoaded', function () {
  if (typeof Swiper === 'undefined') {
    return;
  }

  document.querySelectorAll('.mnpk-hero-swiper').forEach(function (el) {
    var slideCount = el.querySelectorAll('.swiper-slide').length;

    new Swiper(el, {
      loop: slideCount > 1,
      speed: 700,
      effect: 'fade',
      fadeEffect: {
        crossFade: true
      },
      autoplay: slideCount > 1 ? {
        delay: 5000,
        disableOnInteraction: false
      } : false,
      pagination: {
        el: el.querySelector('.mnpk-hero-swiper__pagination'),
        clickable: true
      }
    });
  });
});
