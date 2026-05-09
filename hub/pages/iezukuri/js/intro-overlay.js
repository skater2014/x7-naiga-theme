(function () {
  'use strict';

  var overlay = document.querySelector('.iez-intro-overlay');
  if (!overlay) return;

  var params = new URLSearchParams(window.location.search);
  var forceIntro = params.get('intro') === '1';
  var resetIntro = params.get('intro') === 'reset';
  var storageKey = 'naigai_iezukuri_intro_seen_v2';

  if (resetIntro) {
    try {
      localStorage.removeItem(storageKey);
      sessionStorage.removeItem(storageKey);
    } catch (e) {}
  }

  /*
   * 通常アクセスでは一度見たら再表示しない。
   * 確認したい時だけ /iezukuri/?intro=1
   */
  if (!forceIntro && !resetIntro) {
    try {
      if (localStorage.getItem(storageKey) === '1') {
        overlay.remove();
        return;
      }
    } catch (e) {}
  }

  var duration = parseInt(overlay.getAttribute('data-duration') || '15000', 10);
  var slideInterval = parseInt(overlay.getAttribute('data-slide-interval') || '3500', 10);
  var bgmDelay = parseInt(overlay.getAttribute('data-bgm-delay') || '0', 10);
  var bgmEnabled = overlay.getAttribute('data-bgm-enabled') === '1';

  duration = Math.max(5000, duration);
  slideInterval = Math.max(1800, slideInterval);
  bgmDelay = Math.max(0, bgmDelay);

  overlay.style.setProperty('--iez-intro-duration', duration + 'ms');

  var html = document.documentElement;
  var body = document.body;
  var previousScrollY = window.scrollY || window.pageYOffset || 0;

  /*
   * イントロ中はページ本体を動かさない。
   * 終了後は必ずページトップへ戻す。
   */
  html.classList.add('iez-intro-lock');
  body.classList.add('iez-intro-lock');

  overlay.classList.remove('is-ending', 'is-done');
  overlay.classList.add('is-started');

  var audio = overlay.querySelector('.iez-intro-overlay__audio');
  var soundButton = overlay.querySelector('.iez-intro-overlay__sound');
  var skipButton = overlay.querySelector('.iez-intro-overlay__skip');

  var nasuSlides = Array.prototype.slice.call(
    overlay.querySelectorAll('.iez-intro-overlay__side--nasu .iez-intro-overlay__slide')
  );

  var tokyoSlides = Array.prototype.slice.call(
    overlay.querySelectorAll('.iez-intro-overlay__side--tokyo .iez-intro-overlay__slide')
  );

  var index = 0;
  var slideTimer = null;
  var finished = false;

  function setActive(slides, activeIndex) {
    if (!slides.length) return;

    slides.forEach(function (slide, i) {
      slide.classList.toggle('is-active', i === activeIndex % slides.length);
    });
  }

  function nextSlide() {
    index += 1;
    setActive(nasuSlides, index);
    setActive(tokyoSlides, index);
  }

  function updateSoundLabel(isOn, blocked) {
    if (!soundButton) return;

    if (isOn) {
      soundButton.textContent = '♪ BGM ON';
      soundButton.setAttribute('aria-pressed', 'true');
      soundButton.classList.add('is-playing');
      soundButton.classList.remove('is-blocked');
      return;
    }

    soundButton.textContent = blocked ? '♪ 音をON' : '♪ BGM OFF';
    soundButton.setAttribute('aria-pressed', 'false');
    soundButton.classList.remove('is-playing');
    soundButton.classList.toggle('is-blocked', !!blocked);
  }

  function playAudio() {
    if (!audio) {
      updateSoundLabel(false, false);
      return Promise.resolve(false);
    }

    audio.muted = false;
    audio.volume = 0.72;

    var promise = audio.play();

    if (!promise || typeof promise.then !== 'function') {
      updateSoundLabel(!audio.paused, audio.paused);
      return Promise.resolve(!audio.paused);
    }

    return promise.then(function () {
      updateSoundLabel(true, false);
      return true;
    }).catch(function () {
      updateSoundLabel(false, true);
      return false;
    });
  }

  function pauseAudio() {
    if (!audio) return;
    audio.pause();
    updateSoundLabel(false, false);
  }

  function cleanupAfterIntro() {
    html.classList.remove('iez-intro-lock');
    body.classList.remove('iez-intro-lock');

    /*
     * /iezukuri 本体を必ず先頭から見せる。
     */
    window.scrollTo({ top: 0, left: 0, behavior: 'auto' });
  }

  function finishIntro() {
    if (finished) return;
    finished = true;

    try {
      localStorage.setItem(storageKey, '1');
    } catch (e) {}

    pauseAudio();

    if (slideTimer) {
      window.clearInterval(slideTimer);
    }

    overlay.classList.add('is-ending');

    window.setTimeout(function () {
      overlay.classList.add('is-done');
      overlay.remove();
      cleanupAfterIntro();
    }, 900);
  }

  function unlockAudioOnce() {
    if (!audio || finished || !audio.paused) return;
    playAudio();
  }

  if (soundButton && audio) {
    soundButton.addEventListener('click', function (event) {
      event.preventDefault();
      event.stopPropagation();

      if (audio.paused) {
        playAudio();
      } else {
        pauseAudio();
      }
    });
  }

  if (skipButton) {
    skipButton.addEventListener('click', function (event) {
      event.preventDefault();
      event.stopPropagation();
      finishIntro();
    });
  }

  setActive(nasuSlides, 0);
  setActive(tokyoSlides, 0);

  if (nasuSlides.length > 1 || tokyoSlides.length > 1) {
    slideTimer = window.setInterval(nextSlide, slideInterval);
  }

  if (audio) {
    updateSoundLabel(false, false);

    if (bgmEnabled) {
      window.setTimeout(function () {
        playAudio();
      }, bgmDelay);
    }

    document.addEventListener('pointerdown', unlockAudioOnce, { once: false });
    document.addEventListener('keydown', unlockAudioOnce, { once: false });
  } else if (soundButton) {
    soundButton.style.display = 'none';
  }

  window.setTimeout(finishIntro, duration);
})();
