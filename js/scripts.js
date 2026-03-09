// ダークモードの状態をローカルストレージから取得して適用
if (!window.darkModeInitialized) {
  window.darkModeInitialized = true;
  const darkModeBtn = document.querySelector('#btn-mode');
  const darkModeState = localStorage.getItem('darkMode');

  if (darkModeBtn) {
    darkModeBtn.checked = darkModeState === 'true';
    toggleDarkMode(darkModeBtn.checked);

    darkModeBtn.addEventListener('change', () => {
      const isChecked = darkModeBtn.checked;
      toggleDarkMode(isChecked);
      localStorage.setItem('darkMode', isChecked ? 'true' : 'false');
    });

    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
      toggleDarkMode(e.matches);
    });
  }
}

// ダークモードの適用/解除
function toggleDarkMode(isDarkMode) {
  document.body.classList.toggle('dark-theme', isDarkMode);
  document.body.classList.toggle('light-theme', !isDarkMode);
  document.documentElement.style.setProperty('--link-color-white', isDarkMode ? '#fff' : '');
}
// トップページに戻るボタン
document.addEventListener('DOMContentLoaded', function () {
  console.log('✅ DOMContentLoaded: トップボタン処理開始');

  // `topBtn` がすでに存在するか確認
  let topBtn = document.getElementById('topBtn');
  if (!topBtn) {
    topBtn = document.createElement('button');
    topBtn.id = 'topBtn';
    topBtn.textContent = 'Top';
    topBtn.title = 'トップに戻る';
    topBtn.style.position = 'fixed';
    topBtn.style.bottom = '10px';
    topBtn.style.right = '20px';
    topBtn.style.opacity = '0';
    topBtn.style.transition = 'opacity 0.3s ease-in-out';
    topBtn.style.zIndex = '9999';
    topBtn.style.display = 'block'; // **✅ 確実に表示する**

    // **🔹 追加先の確認**
    let container = document.querySelector('.page-wrapper');
    if (!container) {
      console.warn('⚠️ `.page-wrapper` が見つかりません。`body` に追加します');
      container = document.body;
    }
    container.appendChild(topBtn);

    let isHovering = false;
    let timeoutId;

    // **✅ トグル関数の修正**
    function toggleButtonVisibility() {
      console.log('📌 スクロール位置:', window.scrollY);
      topBtn.style.opacity = window.scrollY > 600 && !isHovering ? '1' : '0';
    }

    topBtn.addEventListener('mouseover', function () {
      isHovering = true;
    });

    topBtn.addEventListener('mouseout', function () {
      isHovering = false;
      clearTimeout(timeoutId);
      timeoutId = setTimeout(function () {
        if (!isHovering) {
          topBtn.style.opacity = '0';
        }
      }, 2000);
    });

    topBtn.addEventListener('click', function () {
      window.scrollTo({ top: 0, behavior: 'smooth' });
      topBtn.style.opacity = '0';
    });

    window.addEventListener('mousemove', function () {
      clearTimeout(timeoutId);
      if (!isHovering && window.scrollY > 600) {
        topBtn.style.opacity = '1';
      }
      timeoutId = setTimeout(function () {
        if (!isHovering) {
          topBtn.style.opacity = '0';
        }
      }, 2000);
    });

    window.addEventListener('scroll', toggleButtonVisibility);

    // **✅ 初回チェック**
    toggleButtonVisibility();
  }
});

/*open rotated → ::after で矢印が動く ＆ .sub-menu が display: block; になる
✔ click で open クラスを追加・削除するだけで開閉が管理できる
✔ document の click イベントで、外側クリック時に open を削除すれば閉じる！*/

/** 📌 モバイルメニュー開閉 */
function setupMobileMenu() {
  jQuery('.header-menu-mobile .menu-item-has-children > a')
    .off('click')
    .on('click', function (e) {
      if (window.innerWidth > 1024) return;

      e.preventDefault();
      e.stopPropagation();

      var parentItem = jQuery(this).parent();
      var submenu = parentItem.find('.sub-menu').first();
      var isOpen = parentItem.hasClass('open');

      // 🔹 他の開いているメニューを即閉じる（アニメーション中断）
      jQuery('.header-menu-mobile .menu-item-has-children.open')
        .not(parentItem)
        .removeClass('open rotated')
        .find('.sub-menu')
        .stop(true, true) // アニメーション中断
        .slideUp(0); // 即時閉じる（0ms）

      if (isOpen) {
        // 🔹 自分を閉じる
        submenu.stop(true, true).slideUp(300);
        parentItem.removeClass('open rotated');
      } else {
        // 🔹 自分を開く
        submenu.stop(true, true).slideDown(300);
        parentItem.addClass('open rotated');
      }
    });

  // 🔹 外側クリックで全て閉じる
  jQuery(document)
    .off('click.closeMobileMenu')
    .on('click.closeMobileMenu', function (e) {
      if (window.innerWidth > 1024) return;
      if (!jQuery(e.target).closest('.header-menu-mobile').length) {
        jQuery('.header-menu-mobile .menu-item-has-children.open')
          .removeClass('open rotated')
          .find('.sub-menu')
          .stop(true, true)
          .slideUp(300);
      }
    });
}

/** 📌 PC（1025px〜）切替時にモバイル状態をリセット */
function resetMobileMenuOnResize() {
  if (window.innerWidth >= 1025) {
    jQuery('.header-menu-mobile .menu-item-has-children').removeClass('open rotated');
    jQuery('.header-menu-mobile .sub-menu').removeAttr('style');
  }
}

/**
 * モバイルメニューを固定ヘッダーの下にぴったり配置するための高さ計算スクリプト
 *
 * 目的：
 * - 固定ヘッダー（.header__top）の高さを取得し、CSS変数にセットする
 * - モバイルメニュー（.navg__list.header-menu-mobile）がヘッダーの下から表示されるようにする
 * - ページやデバイスによってヘッダー高さが変わっても対応できるようにする
 *
 * 補足：
 * - 固定処理はCSS側の `.header__top { position: fixed; }` が担当
 * - このJSは高さを測ってCSS変数に渡すだけ
 * - CSSでは `top: var(--header-top-height, 60px);` と書くことで、
 *   JS未実行時でもデフォルト値60pxを使って配置が崩れないようにしている
 */

/*function updateSliderOffset() {
    const headerTop = document.querySelector('.header__top');
    const navList = document.querySelector('.navg__list.header-menu-mobile');

    if (!headerTop) return;

    let totalHeight = headerTop.offsetHeight;

    if (window.innerWidth <= 1024 && navList) {
        const style = window.getComputedStyle(navList);
        if (style.display !== 'none' && style.visibility !== 'hidden') {
            totalHeight += navList.offsetHeight;
        }
    }

    // CSS変数にセット
    document.documentElement.style.setProperty('--total-header-height', totalHeight + 'px');
}

document.addEventListener('DOMContentLoaded', updateSliderOffset);
window.addEventListener('resize', updateSliderOffset);*/

/** 📌 サイドメニュー（.sidebar-dropdown-menu）の開閉 */
function setupSidebarMenu() {
  console.log('✅ setupSidebarMenu() 実行');

  jQuery('.sidebar-dropdown-menu .menu-item-has-children > a')
    .off('click')
    .on('click', function (e) {
      e.preventDefault(); // 🔹 デフォルトのリンク動作を防ぐ

      var parentItem = jQuery(this).parent(); // 📌 クリックされたアイテムの親要素
      var submenu = parentItem.find('.sub-menu').first(); // 📌 クリックされたアイテムのサブメニュー
      var isOpen = parentItem.hasClass('open'); // 📌 既に開いているかどうかをチェック

      // 🔹 他の開いているメニューを閉じる
      jQuery('.sidebar-dropdown-menu .menu-item-has-children.open')
        .not(parentItem)
        .removeClass('open rotated')
        .find('.sub-menu')
        .stop(true, true)
        .slideUp(300);

      // 🔹 クリックされたメニューの開閉処理
      submenu.stop(true, true).slideToggle(300);
      parentItem.toggleClass('open rotated');

      // 🔹 `aria-expanded` を切り替え
      jQuery(this).attr('aria-expanded', isOpen ? 'false' : 'true');

      console.log('📌 サイドメニュー開閉:', isOpen ? '閉じる' : '開く');
    });
}

/**
 * サイドバー（drawer）全体の開閉
 *
 * 対象要素
 * - 開くボタン   : #touch-menu
 * - 閉じるボタン : .drawer-close
 * - drawer本体   : .sidebar-navigation
 *
 * 役割
 * - 開くボタンで drawer を表示
 * - 閉じるボタンで drawer を非表示
 * - body.menu-open を付け外し
 * - aria-expanded / aria-label を更新
 * - 外側クリック / Escape でも閉じる
 * - スワイプで開閉
 */
function setupHamburgerMenu() {
  var $openBtn = jQuery('#touch-menu');
  var $closeBtn = jQuery('.drawer-close');
  var $drawer = jQuery('#sidebar-navigation');
  var $body = jQuery('body');

  if (!$openBtn.length || !$drawer.length) return;

  function setMenuState(open) {
    $body.toggleClass('menu-open', open);
    $drawer.toggleClass('visible', open);
    $openBtn.toggleClass('is-open', open);
    $openBtn.attr('aria-expanded', open ? 'true' : 'false');
    $openBtn.attr('aria-label', open ? 'メニューを閉じる' : 'メニューを開く');
  }

  function toggleFromEvent(e, open) {
    if (e.type === 'keydown') {
      if (e.key !== 'Enter' && e.key !== ' ') return;
      e.preventDefault();
    }
    setMenuState(open);
  }

  $openBtn.off('.drawerToggle').on('click.drawerToggle keydown.drawerToggle', function (e) {
    var isOpen = $body.hasClass('menu-open');
    toggleFromEvent(e, !isOpen);
  });

  $closeBtn.off('.drawerToggle').on('click.drawerToggle keydown.drawerToggle', function (e) {
    toggleFromEvent(e, false);
  });

  jQuery(document)
    .off('keydown.drawerToggleEsc')
    .on('keydown.drawerToggleEsc', function (e) {
      if (e.key === 'Escape') setMenuState(false);
    });

  jQuery(document)
    .off('click.drawerToggleOutside')
    .on('click.drawerToggleOutside', function (e) {
      var $target = jQuery(e.target);
      if (!$body.hasClass('menu-open')) return;
      if ($target.closest('#sidebar-navigation, #touch-menu').length) return;
      setMenuState(false);
    });

  setMenuState(false);
}

/* 🔰（任意）DOM準備後に初期状態を明示的に「閉じる」にそろえる */
jQuery(function ($) {
  function getLikedPosts() {
    try {
      return (JSON.parse(localStorage.getItem('likedPosts')) || []).map(String);
    } catch (e) {
      return [];
    }
  }

  function setLikedPosts(arr) {
    const uniq = Array.from(new Set(arr.map(String)));
    localStorage.setItem('likedPosts', JSON.stringify(uniq));
  }

  function setUI(postId, isActive) {
    const $wrap = $('.heart-icon[data-post-id="' + postId + '"]');
    $wrap.toggleClass('active', !!isActive);
    $wrap.find('svg').toggleClass('liked', !!isActive);
  }

  getLikedPosts().forEach(function (postId) {
    setUI(postId, true);
  });

  $(document).on('click', '.heart-icon', function (e) {
    e.preventDefault();
    e.stopPropagation();

    const $this = $(this);
    const postId = String($this.data('post-id'));
    const prevActive = $this.hasClass('active');
    const nextActive = !prevActive;

    setUI(postId, nextActive);

    let likedPosts = getLikedPosts();
    if (nextActive) likedPosts.push(postId);
    else likedPosts = likedPosts.filter((id) => id !== postId);
    setLikedPosts(likedPosts);

    $.ajax({
      url: customAjax.ajaxurl,
      type: 'POST',
      dataType: 'json',
      data: {
        action: 'toggle_like',
        post_id: postId,
        liked: nextActive ? 1 : 0,
        nonce: customAjax.nonce,
      },
    })
      .done(function (res) {
        if (!res || res.success !== true) {
          rollback();
        }
      })
      .fail(function () {
        rollback();
      });

    function rollback() {
      setUI(postId, prevActive);

      let back = getLikedPosts();
      if (prevActive) back.push(postId);
      else back = back.filter((id) => id !== postId);
      setLikedPosts(back);
    }
  });
});

/** 📌 ナビ系初期化（1回だけ実行） */
jQuery(document).ready(function ($) {
  console.log('🚀 ナビ系初期化開始');

  if (typeof setupMobileMenu === 'function') {
    setupMobileMenu();
  } else {
    console.error('❌ setupMobileMenu が定義されていません！');
  }

  if (typeof resetMobileMenuOnResize === 'function') {
    resetMobileMenuOnResize();
  } else {
    console.error('❌ resetMobileMenuOnResize が定義されていません！');
  }

  if (typeof setupSidebarMenu === 'function') {
    setupSidebarMenu();
  } else {
    console.error('❌ setupSidebarMenu が定義されていません！');
  }

  if (typeof setupHamburgerMenu === 'function') {
    setupHamburgerMenu();
  } else {
    console.error('❌ setupHamburgerMenu が定義されていません！');
  }

  $(window)
    .off('resize.resetMobileMenuOnResize')
    .on('resize.resetMobileMenuOnResize', function () {
      if (typeof resetMobileMenuOnResize === 'function') {
        resetMobileMenuOnResize();
      }
    });
});

/*
  ▼このスクリプトがやること（超ざっくり）header- wrapper に　postion fixedで表示の対応。
    1) WordPressの管理バー(#wpadminbar)の高さを測って、CSS変数 --admin-offset に入れる
       → CSS側で .header-wrapper { top: var(--admin-offset) } として、管理バーを避けて配置
    2) 固定ヘッダー全体(.header-wrapper)の「実際の高さ」を測って、CSS変数 --hdr-spacer に入れる
       → CSS側で 本文の padding-top: var(--hdr-spacer) として、ヘッダーと本文の重なりを防ぐ
    3) 管理画面(/wp-admin/)では動かさない（乱れ防止）
    4) モバイル幅(～767.98px)のときだけ有効
*/

/*
  このスクリプトの仕事：
  - 管理バーの高さ → --admin-offset（ヘッダーの top に使う）
  - 固定ヘッダーの高さ → --hdr-spacer（本文の padding-top に使う）
  - 管理画面では動かさない／モバイル幅のときだけ動かす
*/
(function () {
  const root = document.documentElement; // ← CSS変数を書き込む場所（<html>）
  /*const mq   = window.matchMedia('(max-width: 767.98px)'); // ← モバイルだけ*/
  const mq = window.matchMedia('(max-width: 1199.98px)'); // ← モバイル＋タブレット

  function setPx(name, v) {
    // ← CSS変数に「px付き」で値を入れる小関数
    root.style.setProperty(name, Math.round(v) + 'px');
  }

  function updateHeaderSpacer() {
    // 1) 管理画面(/wp-admin/)ではやらない（DOM構造が違って乱れるのを防ぐ）
    if (/\/wp-admin\//.test(location.pathname)) {
      root.style.removeProperty('--hdr-spacer'); // 念のため消す
      root.style.removeProperty('--admin-offset'); // 念のため消す
      return;
    }

    // 2) モバイル幅じゃないならリセット（PC/タブレットはこの仕組みを使わない）
    if (!mq.matches) {
      setPx('--hdr-spacer', 0); // 本文の押し下げをゼロに
      setPx('--admin-offset', 0); // ヘッダーの top ずらしもゼロに
      return;
    }

    // 3) 必要な要素を取得
    const wrapper = document.querySelector('.header-wrapper'); // 固定している“ヘッダーの親”
    const adminBar = document.getElementById('wpadminbar'); // WPの黒い管理バー（ログイン時だけ存在）

    // 4) 管理バーの高さを --admin-offset に入れる
    //    CSS側で .header-wrapper { top: var(--admin-offset) } として、バーを避ける
    const adminHeight = adminBar ? adminBar.offsetHeight : 0;
    setPx('--admin-offset', adminHeight);

    // 5) ヘッダーが無ければ押し下げ不要
    if (!wrapper) {
      setPx('--hdr-spacer', 0);
      return;
    }

    // 6) 本文を下げる量 = 「固定ヘッダーの実際の高さ」
    //    wrapper.offsetHeight は“その要素自身の高さ”だけを返す
    //    （管理バー分まで含めないので、ログイン時でもズレない）
    const headerHeight = wrapper.offsetHeight;

    // 7) CSS変数 --hdr-spacer に、その高さを入れる
    //    → CSSが本文の padding-top に使い、ヘッダーと重ならなくなる
    setPx('--hdr-spacer', headerHeight);
  }

  // 8) 画面ができたとき・読み込み完了時・リサイズ・向き変更で測り直し
  ['DOMContentLoaded', 'load', 'resize', 'orientationchange'].forEach((evt) => {
    window.addEventListener(evt, updateHeaderSpacer);
  });

  // 9) ヘッダーや管理バーの高さが途中で変わっても追従（例：パンくず改行）
  if ('ResizeObserver' in window) {
    const ro = new ResizeObserver(updateHeaderSpacer);
    const wrapper = document.querySelector('.header-wrapper');
    const adminBar = document.getElementById('wpadminbar');
    if (wrapper) ro.observe(wrapper);
    if (adminBar) ro.observe(adminBar);
  }

  // 10) モバイル幅 ⇄ PC幅 の切り替え時も測り直し
  if (mq.addEventListener) mq.addEventListener('change', updateHeaderSpacer);
  else mq.addListener(updateHeaderSpacer); // 古いブラウザ向け
})(); // 読み込まれたらすぐ実行

/* =========================================================
   HOME SWIPER - SIMPLE + OVERLAY
   動画:
   - 再生 or × で隠す
   - 外へ出たら再表示
   画像:
   - 基本表示のまま
   ========================================================= */

function showHomeSlideOverlay(feature) {
  if (!feature) return;
  feature.classList.remove('is-overlay-hidden');
}

function hideHomeSlideOverlay(feature) {
  if (!feature) return;
  feature.classList.add('is-overlay-hidden');
}

function resetHomeSlideOverlayState(root) {
  if (!root) return;

  root.querySelectorAll('.home-slide-feature').forEach(function (feature) {
    feature.classList.remove('is-overlay-hidden');
  });
}

function bindHomeSlideOverlay(root) {
  if (!root || root.dataset.homeOverlayBound === '1') return;
  root.dataset.homeOverlayBound = '1';

  root.querySelectorAll('.home-slide-feature.has-video').forEach(function (feature) {
    feature.addEventListener('mouseleave', function () {
      showHomeSlideOverlay(feature);
    });

    feature.addEventListener('focusout', function () {
      setTimeout(function () {
        if (!feature.contains(document.activeElement)) {
          showHomeSlideOverlay(feature);
        }
      }, 0);
    });
  });

  root.addEventListener('click', function (e) {
    var closeBtn = e.target.closest('.home-slide-overlay__close');
    if (closeBtn) {
      e.preventDefault();
      e.stopPropagation();

      var closeFeature = closeBtn.closest('.home-slide-feature.has-video');
      hideHomeSlideOverlay(closeFeature);
      return;
    }

    if (e.target.closest('.home-slide-overlay__title a')) {
      return;
    }

    var videoFeature = e.target.closest('.home-slide-feature.has-video');
    if (!videoFeature) return;

    hideHomeSlideOverlay(videoFeature);

    var liteYouTube = videoFeature.querySelector('lite-youtube');
    var liteVimeo = videoFeature.querySelector('lite-vimeo');

    if (liteYouTube) {
      requestAnimationFrame(function () {
        liteYouTube.click();
      });
      return;
    }

    if (liteVimeo) {
      requestAnimationFrame(function () {
        liteVimeo.click();
      });
    }
  });
}

function initHomeSlideOverlay(swiper) {
  if (!swiper || swiper.destroyed || !swiper.el) return;
  bindHomeSlideOverlay(swiper.el);
  resetHomeSlideOverlayState(swiper.el);
}

function initHomeSwiper() {
  var el = document.getElementById('home-swiper');
  if (!el || typeof Swiper === 'undefined') return;

  if (el.swiper && !el.swiper.destroyed) {
    el.swiper.destroy(true, true);
  }

  var slides = el.querySelectorAll('.swiper-wrapper > .swiper-slide');
  var slideCount = slides.length;
  if (!slideCount) return;

  new Swiper(el, {
    slidesPerView: 1,
    slidesPerGroup: 1,
    spaceBetween: 0,
    speed: 500,
    autoHeight: false,
    loop: slideCount > 1,
    rewind: slideCount <= 1,
    allowTouchMove: slideCount > 1,
    watchOverflow: true,
    preloadImages: false,
    updateOnImagesReady: true,
    observer: true,
    observeParents: true,
    observeSlideChildren: true,

    navigation: {
      prevEl: el.querySelector('.swiper-button-prev'),
      nextEl: el.querySelector('.swiper-button-next'),
    },

    pagination: {
      el: el.querySelector('.swiper-pagination'),
      clickable: true,
    },

    on: {
      init: function () {
        var root = document.querySelector('.swiper-home');
        if (root) root.classList.remove('invisibility');
        initHomeSlideOverlay(this);
      },

      imagesReady: function () {
        this.update();
      },

      resize: function () {
        this.update();
      },

      slideChange: function () {
        resetHomeSlideOverlayState(this.el);
      },

      loopFix: function () {
        resetHomeSlideOverlayState(this.el);
      },
    },
  });
}

function initHomeIsotope($) {
  var $grid = $('.home-posts');

  if (!$grid.length || typeof $.fn.isotope === 'undefined') return;

  $grid.imagesLoaded(function () {
    $grid.isotope({
      itemSelector: '.archive-post-box',
      layoutMode: 'fitRows',
      filter: '*',
    });
  });

  $('.home-cats-selection')
    .off('click.homeCats')
    .on('click.homeCats', 'ul li a', function (e) {
      e.preventDefault();
      $('.home-cats-selection ul li a').removeClass('active');
      $(this).addClass('active');
      $grid.isotope({ filter: $(this).attr('href') });
    });
}

/* =========================================================
   HOME SWIPER - FIXED FULL VERSION
   - 動画オーバーレイの安定化
   - ×で閉じても即再表示しない
   - メディア領域を一度出て戻った時だけ再表示
   - スライド移動時は非アクティブ動画を停止して初期化
   ========================================================= */
(function ($) {
  'use strict';

  /* -----------------------------------------
     共通
  ----------------------------------------- */
  function getHomeSwiperEl() {
    return document.getElementById('home-swiper');
  }

  function getVideoFeatureFromNode(node) {
    if (!node || !node.closest) return null;
    return node.closest('.home-slide-feature.has-video');
  }

  function getMediaColumnFromNode(node) {
    if (!node || !node.closest) return null;
    return node.closest('.home-slide-media-column');
  }

  function getVideoElement(feature) {
    if (!feature) return null;
    return feature.querySelector('lite-youtube, lite-vimeo, iframe');
  }

  function ensureOriginalVideoMarkup(feature) {
    if (!feature || feature.__homeOriginalVideoMarkupSaved) return;

    var media = feature.querySelector('lite-youtube, lite-vimeo');
    if (media) {
      feature.__homeOriginalVideoMarkup = media.outerHTML;
      feature.__homeOriginalVideoMarkupSaved = true;
    }
  }

  function showHomeSlideOverlay(feature) {
    if (!feature) return;
    feature.classList.remove('is-overlay-hidden');
    feature.dataset.overlayCanRestore = '0';
  }

  function hideHomeSlideOverlay(feature) {
    if (!feature) return;
    feature.classList.add('is-overlay-hidden');
    feature.dataset.overlayCanRestore = '0';
  }

  function armHomeSlideOverlayRestore(feature) {
    if (!feature) return;
    if (!feature.classList.contains('is-overlay-hidden')) return;
    feature.dataset.overlayCanRestore = '1';
  }

  function resetHomeSlideOverlayState(root) {
    if (!root) return;

    root.querySelectorAll('.home-slide-feature.has-video').forEach(function (feature) {
      showHomeSlideOverlay(feature);
      ensureOriginalVideoMarkup(feature);
    });
  }

  /* -----------------------------------------
     動画停止 / 初期化
  ----------------------------------------- */
  function restoreOriginalVideoElement(feature) {
    if (!feature) return;

    ensureOriginalVideoMarkup(feature);

    var originalMarkup = feature.__homeOriginalVideoMarkup;
    if (!originalMarkup) return;

    var currentMedia = getVideoElement(feature);
    var overlay = feature.querySelector('.home-slide-overlay');

    var wrap = document.createElement('div');
    wrap.innerHTML = originalMarkup.trim();
    var freshMedia = wrap.firstElementChild;

    if (!freshMedia) return;

    if (currentMedia) {
      currentMedia.replaceWith(freshMedia);
    } else if (overlay) {
      feature.insertBefore(freshMedia, overlay);
    } else {
      feature.appendChild(freshMedia);
    }
  }

  function stopAndResetVideoFeature(feature) {
    if (!feature) return;

    restoreOriginalVideoElement(feature);
    showHomeSlideOverlay(feature);
  }

  function stopInactiveSlideVideos(swiper) {
    if (!swiper || !swiper.slides || !swiper.slides.length) return;

    swiper.slides.forEach(function (slide, index) {
      if (index === swiper.activeIndex) return;

      slide.querySelectorAll('.home-slide-feature.has-video').forEach(function (feature) {
        stopAndResetVideoFeature(feature);
      });
    });
  }

  /* -----------------------------------------
     オーバーレイバインド
  ----------------------------------------- */
  function bindHomeSlideOverlay(root) {
    if (!root || root.dataset.homeOverlayBound === '1') return;
    root.dataset.homeOverlayBound = '1';

    root.querySelectorAll('.home-slide-feature.has-video').forEach(function (feature) {
      ensureOriginalVideoMarkup(feature);
    });

    root.querySelectorAll('.home-slide-media-column').forEach(function (mediaColumn) {
      if (mediaColumn.dataset.homeMediaColumnBound === '1') return;
      mediaColumn.dataset.homeMediaColumnBound = '1';

      mediaColumn.addEventListener('pointerleave', function () {
        var hiddenFeature = mediaColumn.querySelector(
          '.home-slide-feature.has-video.is-overlay-hidden',
        );
        if (hiddenFeature) {
          armHomeSlideOverlayRestore(hiddenFeature);
        }
      });

      mediaColumn.addEventListener('pointerenter', function () {
        var hiddenFeature = mediaColumn.querySelector(
          '.home-slide-feature.has-video.is-overlay-hidden',
        );
        if (!hiddenFeature) return;

        if (hiddenFeature.dataset.overlayCanRestore === '1') {
          showHomeSlideOverlay(hiddenFeature);
        }
      });
    });

    root.addEventListener('click', function (e) {
      var closeBtn = e.target.closest('.home-slide-overlay__close');
      if (closeBtn) {
        e.preventDefault();
        e.stopPropagation();

        var closeFeature = getVideoFeatureFromNode(closeBtn);
        hideHomeSlideOverlay(closeFeature);
        return;
      }

      if (e.target.closest('.home-slide-overlay__title a')) {
        return;
      }

      var feature = getVideoFeatureFromNode(e.target);
      if (!feature) return;

      if (feature.classList.contains('is-overlay-hidden')) {
        return;
      }

      hideHomeSlideOverlay(feature);

      var liteYouTube = feature.querySelector('lite-youtube');
      var liteVimeo = feature.querySelector('lite-vimeo');

      if (liteYouTube) {
        requestAnimationFrame(function () {
          liteYouTube.click();
        });
        return;
      }

      if (liteVimeo) {
        requestAnimationFrame(function () {
          liteVimeo.click();
        });
      }
    });

    root.addEventListener('focusout', function (e) {
      var mediaColumn = getMediaColumnFromNode(e.target);
      if (!mediaColumn) return;

      if (e.relatedTarget && mediaColumn.contains(e.relatedTarget)) {
        return;
      }

      var hiddenFeature = mediaColumn.querySelector(
        '.home-slide-feature.has-video.is-overlay-hidden',
      );
      if (hiddenFeature) {
        armHomeSlideOverlayRestore(hiddenFeature);
      }
    });

    root.addEventListener('focusin', function (e) {
      var mediaColumn = getMediaColumnFromNode(e.target);
      if (!mediaColumn) return;

      if (e.relatedTarget && mediaColumn.contains(e.relatedTarget)) {
        return;
      }

      var hiddenFeature = mediaColumn.querySelector(
        '.home-slide-feature.has-video.is-overlay-hidden',
      );
      if (!hiddenFeature) return;

      if (hiddenFeature.dataset.overlayCanRestore === '1') {
        showHomeSlideOverlay(hiddenFeature);
      }
    });
  }

  function initHomeSlideOverlay(swiper) {
    if (!swiper || swiper.destroyed || !swiper.el) return;
    bindHomeSlideOverlay(swiper.el);
    resetHomeSlideOverlayState(swiper.el);
  }

  /* -----------------------------------------
     Swiper
  ----------------------------------------- */
  function initHomeSwiper() {
    var el = getHomeSwiperEl();
    if (!el || typeof Swiper === 'undefined') return;

    if (el.__homeSwiperInitialized === '1' && el.swiper && !el.swiper.destroyed) {
      return el.swiper;
    }

    var slides = el.querySelectorAll('.swiper-wrapper > .swiper-slide');
    var slideCount = slides.length;
    if (!slideCount) return;

    var swiper = new Swiper(el, {
      slidesPerView: 1,
      slidesPerGroup: 1,
      spaceBetween: 0,
      speed: 500,
      autoHeight: false,
      loop: slideCount > 1,
      rewind: slideCount <= 1,
      allowTouchMove: slideCount > 1,
      watchOverflow: true,
      preloadImages: false,
      updateOnImagesReady: true,

      /* 再初期化暴発を防ぐため observer 系は外す */
      observer: false,
      observeParents: false,
      observeSlideChildren: false,

      navigation: {
        prevEl: el.querySelector('.swiper-button-prev'),
        nextEl: el.querySelector('.swiper-button-next'),
      },

      pagination: {
        el: el.querySelector('.swiper-pagination'),
        clickable: true,
      },

      on: {
        init: function () {
          el.__homeSwiperInitialized = '1';

          var root = document.querySelector('.swiper-home');
          if (root) {
            root.classList.remove('invisibility');
          }

          initHomeSlideOverlay(this);
          stopInactiveSlideVideos(this);
          this.update();
        },

        imagesReady: function () {
          this.update();
        },

        resize: function () {
          this.update();
        },

        slideChangeTransitionStart: function () {
          stopInactiveSlideVideos(this);
        },

        slideChangeTransitionEnd: function () {
          stopInactiveSlideVideos(this);
        },

        loopFix: function () {
          stopInactiveSlideVideos(this);
        },
      },
    });

    return swiper;
  }

  /* -----------------------------------------
     Isotope
  ----------------------------------------- */
  function initHomeIsotope() {
    var $grid = $('.home-posts');

    if (!$grid.length || typeof $.fn.isotope === 'undefined') return;

    $grid.imagesLoaded(function () {
      $grid.isotope({
        itemSelector: '.archive-post-box',
        layoutMode: 'fitRows',
        filter: '*',
      });
    });

    $('.home-cats-selection')
      .off('click.homeCats')
      .on('click.homeCats', 'ul li a', function (e) {
        e.preventDefault();
        $('.home-cats-selection ul li a').removeClass('active');
        $(this).addClass('active');
        $grid.isotope({
          filter: $(this).attr('href'),
        });
      });
  }

  /* -----------------------------------------
     起動
  ----------------------------------------- */
  $(function () {
    initHomeSwiper();
    initHomeIsotope();

    $(window)
      .off('resize.homeSwiperSimple orientationchange.homeSwiperSimple')
      .on('resize.homeSwiperSimple orientationchange.homeSwiperSimple', function () {
        var el = getHomeSwiperEl();
        if (el && el.swiper && !el.swiper.destroyed) {
          el.swiper.update();
        }
      });
  });

  window.addEventListener('load', function () {
    var el = getHomeSwiperEl();
    if (el && el.swiper && !el.swiper.destroyed) {
      el.swiper.update();
      stopInactiveSlideVideos(el.swiper);
    }
  });
})(jQuery);

// ニュース ティカー slick slider
jQuery(document).ready(function ($) {
  var $slider = $('.gallery-slider');
  var sliderInitialized = false;

  function initializeSlider() {
    $slider.slick({
      arrows: false,
      autoplay: true,
      autoplaySpeed: 2000,
      vertical: true, // 垂直方向にスライドするように設定
    });
    sliderInitialized = true;

    // スライダーの初期化後に表示する
    // JavaScriptで初期化後に透明度を戻す
    $slider.css('opacity', 1);
  }

  function toggleSlider() {
    if (window.innerWidth < 768) {
      if (sliderInitialized) {
        $slider.slick('slickPause'); // スライダーを停止
        $slider.hide();
      }
    } else {
      if (sliderInitialized) {
        $slider.slick('slickPlay'); // スライダーを再開
        $slider.show();
      } else {
        initializeSlider();
      }
    }
  }

  // ページ読み込み時にスライダーを初期化し、表示する
  initializeSlider();
  toggleSlider();

  // ウィンドウリサイズ時にスライダーの表示を切り替える
  $(window).on('resize', toggleSlider);
});

// single.php　slider
jQuery(document).ready(function (jQuery) {
  jQuery('.test-slider-for').slick({
    slidesToShow: 1,
    slidesToScroll: 1,
    arrows: false,
    fade: true,
    asNavFor: '.test-slider-nav',
  });

  jQuery('.test-slider-nav').slick({
    slidesToShow: 3,
    slidesToScroll: 1,
    asNavFor: '.test-slider-for',
    dots: true,
    focusOnSelect: true,
  });

  jQuery('a[data-slide]').on('click', function (e) {
    e.preventDefault();
    var slideno = jQuery(this).data('slide');
    jQuery('.test-slider-nav').slick('slickGoTo', slideno - 1);
  });
});

document.addEventListener('DOMContentLoaded', function () {
  var calendarEl = document.getElementById('calendar');
  var listEl = document.getElementById('naigai-property-list');
  if (!calendarEl) return;

  function getCurrentPostIdFromBody() {
    var m = document.body.className.match(/\bpostid-(\d+)\b/);
    return m ? String(m[1]) : '';
  }

  function isWeekView(viewType) {
    return String(viewType || '') === 'timeGridWeek';
  }

  function openReservationModal(data) {
    if (!data) return;

    var $modal = jQuery('#store-reservation-modal');
    if (!$modal.length) return;

    $modal.find('#reservation-property-title').text(data.title || '物件情報');
    $modal.find('#reservation-property-thumbnail').css({
      'background-image': 'url(' + (data.thumbnail || '') + ')',
      'background-size': 'cover',
      'background-position': 'center',
    });
    $modal.find('#reservation-header').text('来店予約');
    $modal.find('#reservation-property-id').text(data.post_id || '');
    $modal.find('#reservation-property-label').text('物件ID: ');
    $modal.find('#reservation-property-price').text(data.price || '売却済');

    $modal
      .attr('data-post-id', data.post_id || '')
      .attr('data-post-type', data.post_type || '')
      .attr('data-permalink', data.permalink || '')
      .attr('data-staff', data.staff || '')
      .attr('data-period-label', data.period_label || '')
      .attr('data-time-label', data.time_label || '')
      .attr('data-type', data.type || '')
      .attr('data-area', data.area || '');

    $modal.addClass('active').css({ display: 'flex', visibility: 'visible' }).fadeIn(300);
    $modal.find('#step-input').show();
    $modal.find('#step-confirm').hide();
    $modal.find('#step-complete').hide();
  }

  function openReserveByPostId(postId) {
    postId = String(postId || '').trim();
    if (!postId) return;

    var currentId = getCurrentPostIdFromBody();
    var isSingle = document.body.classList.contains('single');

    if (isSingle && currentId && currentId === postId) {
      var $target = jQuery('#store-reservation');
      if ($target.length) {
        jQuery('html, body').animate({ scrollTop: Math.max($target.offset().top - 80, 0) }, 300);
        return;
      }
    }

    jQuery.ajax({
      url: '/wp-admin/admin-ajax.php',
      type: 'POST',
      dataType: 'json',
      data: {
        action: 'naigai_get_reservation_modal_data',
        post_id: postId,
      },
      success: function (response) {
        if (!response || !response.success || !response.data) return;
        openReservationModal(response.data);
      },
    });
  }

  function setMode(mode, calendar) {
    var viewHarness = calendarEl.querySelector('.fc-view-harness');

    if (mode === 'list') {
      calendarEl.classList.add('naigai-mode-list');
      if (viewHarness) viewHarness.style.display = 'none';
      if (listEl) listEl.style.display = 'block';
      return;
    }

    calendarEl.classList.remove('naigai-mode-list');
    if (viewHarness) viewHarness.style.display = '';
    if (listEl) listEl.style.display = 'none';

    if (mode === 'week') {
      calendar.changeView('timeGridWeek');
    } else {
      calendar.changeView('dayGridMonth');
    }
  }

  function loadPropertyList(params) {
    params = params || {};
    params.action = 'naigai_get_property_list';

    jQuery.ajax({
      url: '/wp-admin/admin-ajax.php',
      type: 'POST',
      dataType: 'json',
      data: params,
      success: function (response) {
        if (!response || !response.success || !listEl) return;
        listEl.innerHTML = response.data.html;
        if (calendarEl.classList.contains('naigai-mode-list')) {
          listEl.style.display = 'block';
        }
      },
    });
  }

  function getFormParams(form) {
    var fd = new FormData(form);
    var params = {};
    fd.forEach(function (value, key) {
      params[key] = value;
    });
    return params;
  }

  function buildMonthEventContent(arg) {
    var p = arg.event.extendedProps || {};
    var typeLabel = p.property_type_label || '';
    var shortTitle = p.short_title || arg.event.title || '';
    var staff = p.staff || '';

    var wrap = document.createElement('div');
    wrap.className = 'naigai-fc-event';

    var line1 = document.createElement('div');
    line1.className = 'naigai-fc-event__title';
    line1.textContent = '● ' + (typeLabel ? typeLabel : '') + (shortTitle ? '｜' + shortTitle : '');
    wrap.appendChild(line1);

    if (staff) {
      var line2 = document.createElement('div');
      line2.className = 'naigai-fc-event__staff';
      line2.textContent = '担当者：' + staff;
      wrap.appendChild(line2);
    }

    return wrap;
  }

  function buildWeekEventContent(arg) {
    var p = arg.event.extendedProps || {};
    var typeLabel = p.property_type_label || '';
    var shortTitle = p.short_title || arg.event.title || '';
    var timeLabel = p.time_label || '';
    var staff = p.staff || '';

    var wrap = document.createElement('div');
    wrap.className = 'naigai-fc-event naigai-fc-event--week';

    var line1 = document.createElement('div');
    line1.className = 'naigai-fc-event__title';
    line1.textContent = '● ' + (typeLabel ? typeLabel : '') + (shortTitle ? '｜' + shortTitle : '');
    wrap.appendChild(line1);

    if (timeLabel) {
      var line2 = document.createElement('div');
      line2.className = 'naigai-fc-event__time';
      line2.textContent = '案内 ' + timeLabel;
      wrap.appendChild(line2);
    }

    if (staff) {
      var line3 = document.createElement('div');
      line3.className = 'naigai-fc-event__staff';
      line3.textContent = '担当：' + staff;
      wrap.appendChild(line3);
    }

    return wrap;
  }

  jQuery.ajax({
    url: '/wp-admin/admin-ajax.php',
    type: 'POST',
    dataType: 'json',
    data: { action: 'naigai_get_calendar_events' },
    success: function (events) {
      if (!Array.isArray(events)) return;

      var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'ja',
        events: events,
        buttonText: { today: '今日' },
        customButtons: {
          naigaiList: {
            text: '一覧',
            click: function () {
              setMode('list', calendar);
            },
          },
          naigaiWeek: {
            text: '週',
            click: function () {
              setMode('week', calendar);
            },
          },
          naigaiMonth: {
            text: '月',
            click: function () {
              setMode('month', calendar);
            },
          },
        },
        headerToolbar: {
          left: 'prev,next today',
          center: 'title',
          right: 'naigaiList,naigaiWeek,naigaiMonth',
        },
        allDaySlot: true,
        slotMinTime: '09:00:00',
        slotMaxTime: '19:00:00',
        slotLabelFormat: {
          hour: 'numeric',
          minute: '2-digit',
          hour12: false,
        },
        eventContent: function (arg) {
          var viewType = arg.view && arg.view.type ? arg.view.type : '';
          var node = isWeekView(viewType)
            ? buildWeekEventContent(arg)
            : buildMonthEventContent(arg);

          return { domNodes: [node] };
        },
        eventClick: function (info) {
          info.jsEvent.preventDefault();
          var p = info.event.extendedProps || {};
          openReserveByPostId(p.post_id || info.event.id || '');
        },
      });

      calendar.render();
      setMode('list', calendar);
    },
  });

  loadPropertyList({});

  document.addEventListener('submit', function (e) {
    var form = e.target.closest('#naigai-property-filter-form');
    if (!form) return;
    e.preventDefault();

    var params = getFormParams(form);
    params.paged = 1;
    loadPropertyList(params);
  });

  document.addEventListener('click', function (e) {
    var resetBtn = e.target.closest('.naigai-filter-reset');
    if (resetBtn) {
      e.preventDefault();
      loadPropertyList({});
      return;
    }

    var pageBtn = e.target.closest('.naigai-page-btn');
    if (pageBtn) {
      e.preventDefault();
      var wrap = document.getElementById('naigai-property-list');
      var form = wrap ? wrap.querySelector('#naigai-property-filter-form') : null;
      var params = form ? getFormParams(form) : {};
      params.paged = pageBtn.getAttribute('data-page') || 1;
      loadPropertyList(params);
      return;
    }

    var btn = e.target.closest('.naigai-property-row__cta');
    if (btn) {
      e.preventDefault();
      openReserveByPostId(btn.getAttribute('data-post-id'));
    }
  });
});

/*フッター帯 スクロールで表示される固定メニューを制御するJavaScript*/
window.onload = function () {
  const footerFixed = document.getElementById('js-footer-fixed'); // フッター要素

  // 初期状態ではフッターは非表示
  footerFixed.classList.remove('show');

  // スクロールイベントの制御
  let isScrolling;

  // モバイル・タブレット（最大1024px）でのみスクロールイベントを追加
  if (window.innerWidth <= 1024) {
    window.addEventListener('scroll', function () {
      // スクロール中にフッターメニューを非表示に
      footerFixed.classList.remove('show');

      // スクロール停止後にフッターメニューを表示
      window.clearTimeout(isScrolling);
      isScrolling = setTimeout(function () {
        footerFixed.classList.add('show');
      }, 500); // 500ms待機してから表示
    });
  }

  // デスクトップ（1025px以上）ではフッターは常に表示
  if (window.innerWidth > 1024) {
    footerFixed.classList.add('show');
  }
};

// テーブル１　スライド
// 📌 画像ビュー スライダー＆スワイプ
document.addEventListener('DOMContentLoaded', function () {
  let currentIndex = 0;
  const images = document.querySelectorAll('.thumbnail-item'); // サムネイルリスト
  const mainImage = document.getElementById('main-image'); // メイン画像
  const mainTitle = document.getElementById('main-image-title'); // 画像タイトル
  const mainText = document.getElementById('main-image-text'); // 画像テキスト
  const imageIndexDisplay = document.getElementById('image-index'); // 画像のインデックス表示
  const thumbnailContainer = document.getElementById('thumbnail-container'); // サムネイルコンテナ
  const prevButton = document.querySelector('.prev-btn'); // 前へボタン
  const nextButton = document.querySelector('.next-btn'); // 次へボタン

  // **🔹 初期値チェック**
  if (!mainImage || !imageIndexDisplay || !thumbnailContainer || !prevButton || !nextButton) {
    console.error('❌ 必要な要素が見つかりません');
    return;
  }

  if (images.length === 0) {
    console.error('❌ サムネイル画像が見つかりません');
    return;
  }

  /** 📌 メイン画像を変更（スライダーボタン用） */
  function changeMainImage(direction) {
    let newIndex = currentIndex + direction;

    // **🔹 インデックスの範囲チェック**
    if (newIndex >= images.length) {
      newIndex = 0;
    } else if (newIndex < 0) {
      newIndex = images.length - 1;
    }

    currentIndex = newIndex;
    updateMainImage();
  }

  /** 📌 サムネイルをクリックしてメイン画像を変更 */
  function changeMainImageWithThumbnail(element) {
    let newIndex = parseInt(element.dataset.index, 10); // **🔹 `-1` は不要**

    // **🔹 インデックスの範囲チェック**
    if (newIndex < 0 || newIndex >= images.length) {
      console.error('❌ 無効なサムネイルインデックス:', newIndex);
      return;
    }

    currentIndex = newIndex;
    updateMainImage();
  }

  /** 📌 メイン画像を更新 */
  function updateMainImage() {
    const selectedThumbnail = images[currentIndex];

    // **🔹 `selectedThumbnail` が undefined でないことを確認**
    if (!selectedThumbnail) {
      console.error('❌ `selectedThumbnail` が見つかりません（インデックス:', currentIndex, '）');
      return;
    }

    const thumbnailImage = selectedThumbnail.querySelector('img');

    if (thumbnailImage) {
      mainImage.src = thumbnailImage.src;
    } else {
      console.error('❌ `thumbnailImage` が見つかりません（インデックス:', currentIndex, '）');
    }

    imageIndexDisplay.textContent = `${currentIndex + 1} / ${images.length}`;

    // 🔹 タイトルとテキストの更新
    if (mainTitle) {
      const newTitle = selectedThumbnail.dataset.title || '';
      mainTitle.textContent = newTitle;
      mainTitle.style.display = newTitle ? 'block' : 'none';
    }

    if (mainText) {
      const newText = selectedThumbnail.dataset.text || '';
      mainText.textContent = newText;
      mainText.style.display = newText ? 'block' : 'none';
    }

    // 🔹 サムネイルのアクティブ状態を更新
    images.forEach((thumb, index) => {
      thumb.classList.toggle('active-thumbnail', index === currentIndex);
    });

    // 🔹 サムネイルスライダーのスクロール調整
    scrollToThumbnail(currentIndex);
  }

  /** 📌 サムネイルスライダーを中央に寄せる */
  function scrollToThumbnail(index) {
    if (!thumbnailContainer) return;

    const selectedThumbnail = images[index];
    if (selectedThumbnail) {
      const containerWidth = thumbnailContainer.offsetWidth;
      const thumbnailWidth = selectedThumbnail.offsetWidth;
      const selectedLeft = selectedThumbnail.offsetLeft;

      let moveX = -(selectedLeft - containerWidth / 2 + thumbnailWidth / 2);
      thumbnailContainer.style.transform = `translateX(${moveX}px)`;
      thumbnailContainer.style.transition = 'transform 0.3s ease-in-out';
    }
  }

  /** 📌 スワイプ（モバイル・PC対応） */
  function addSwipeEvents(element, callback) {
    let startX,
      isSwiping = false;

    element.addEventListener(
      'touchstart',
      (event) => {
        startX = event.touches[0].clientX;
        isSwiping = false;
      },
      { passive: false },
    );

    element.addEventListener(
      'touchmove',
      (event) => {
        let currentX = event.touches[0].clientX;
        let diffX = startX - currentX;
        if (Math.abs(diffX) > 30) {
          isSwiping = true;
          event.preventDefault();
        }
      },
      { passive: false },
    );

    element.addEventListener('touchend', (event) => {
      if (!isSwiping) return;
      let endX = event.changedTouches[0].clientX;
      if (startX > endX + 30) {
        callback(1);
      } else if (startX < endX - 30) {
        callback(-1);
      }
    });
  }

  // **🔹 ボタンイベント追加**
  prevButton.addEventListener('click', () => changeMainImage(-1));
  nextButton.addEventListener('click', () => changeMainImage(1));

  // **🔹 スワイプイベント適用**
  addSwipeEvents(document.querySelector('.main-image-slider'), changeMainImage);
  addSwipeEvents(thumbnailContainer, changeMainImage);

  // **🔹 サムネイルクリックイベント適用**
  images.forEach((thumb) => {
    thumb.addEventListener('click', function () {
      changeMainImageWithThumbnail(this);
    });
  });

  // **🔹 初期画像の表示**
  updateMainImage();
});

/*============================================================*/
// パノラマ　スワイプ　スライド
document.addEventListener('DOMContentLoaded', function () {
  console.log('✅ パノラマビュー: スクリプト開始');

  // **🔹 必要な要素を取得**
  const panoramaContainer = document.getElementById('panorama-1');
  const panoramaTitle = document.querySelector('.panorama-title');
  const panoramaText = document.querySelector('.panorama-text');
  const panoramaIndex = document.getElementById('panorama-index');
  const subImageContainer = document.querySelector('.sub-images-container');
  const subImages = document.querySelectorAll('.sub-image-item');
  const playButton = document.getElementById('panorama-play');
  const stopButton = document.getElementById('panorama-stop');
  const nextButton = document.getElementById('panorama-next');
  const prevButton = document.getElementById('panorama-prev');

  let currentIndex = 0;
  let autoRotateInterval = null;
  let autoRotateSpeed = 15;
  window.viewer = null;

  /** 📌 Pannellum の初期化関数 */
  function initPannellum(imageUrl) {
    if (!imageUrl) {
      console.error('❌ Pannellum の画像URLが無効:', imageUrl);
      return;
    }
    if (window.viewer) {
      window.viewer.destroy();
    }

    window.viewer = pannellum.viewer('panorama-1', {
      type: 'equirectangular',
      panorama: imageUrl,
      autoLoad: true,
      showControls: true,
      compass: true,
      autoRotate: 0,
    });
  }

  /** 📌 サムネイルをクリックしてパノラマ画像を変更 */
  function changePanoramaImageWithIndex(index) {
    if (index < 0 || index >= subImages.length) {
      console.warn('⚠️ 無効なインデックス: ', index);
      return;
    }

    const element = subImages[index];
    if (!element) {
      console.error('❌ サムネイルが見つかりません: ', index);
      return;
    }

    const url = element.getAttribute('data-url');
    const title = element.getAttribute('data-title') || 'タイトルなし';
    const text = element.getAttribute('data-text') || '説明なし';

    if (!url) {
      console.error('❌ パノラマ画像のURLが無効:', url);
      return;
    }

    initPannellum(url);
    panoramaTitle.textContent = title;
    panoramaText.textContent = text;
    panoramaIndex.textContent = `${index + 1} / ${subImages.length}`;

    subImages.forEach((item) => item.classList.remove('active'));
    element.classList.add('active');

    currentIndex = index;
    scrollToThumbnail(index);
  }

  /** 📌 サムネイルを中央にスクロール */
  function scrollToThumbnail(index) {
    if (!subImages[index] || !subImageContainer) return;
    const thumb = subImages[index];
    const containerWidth = subImageContainer.offsetWidth;
    const thumbOffset = thumb.offsetLeft - subImageContainer.offsetLeft;
    const thumbWidth = thumb.offsetWidth;
    const scrollX = thumbOffset - containerWidth / 2 + thumbWidth / 2;
    subImageContainer.scrollTo({ left: scrollX, behavior: 'smooth' });
  }

  /** 📌 自動回転の処理 */
  function startAutoRotate() {
    if (!window.viewer) {
      console.error('❌ viewer が未定義です');
      return;
    }
    stopAutoRotate();

    autoRotateInterval = setInterval(() => {
      if (!window.viewer) {
        console.error('❌ viewer が未定義のため回転を停止');
        stopAutoRotate();
        return;
      }
      const currentYaw = window.viewer.getYaw();
      window.viewer.setYaw(currentYaw + autoRotateSpeed);
    }, 30);
  }

  function stopAutoRotate() {
    if (autoRotateInterval) {
      clearInterval(autoRotateInterval);
      autoRotateInterval = null;
    }
  }

  /** 📌 再生・停止ボタンの動作 */
  if (playButton) playButton.addEventListener('click', startAutoRotate);
  if (stopButton) stopButton.addEventListener('click', stopAutoRotate);

  /** 📌 「次へ」「前へ」ボタンの動作 */
  if (nextButton) {
    nextButton.addEventListener('click', () => {
      if (currentIndex + 1 < subImages.length) {
        changePanoramaImageWithIndex(currentIndex + 1);
      }
    });
  }
  if (prevButton) {
    prevButton.addEventListener('click', () => {
      if (currentIndex - 1 >= 0) {
        changePanoramaImageWithIndex(currentIndex - 1);
      }
    });
  }

  /** 📌 サムネイルクリックイベントを設定 */
  subImages.forEach((thumb, index) => {
    thumb.addEventListener('click', function () {
      changePanoramaImageWithIndex(index);
    });
  });

  /** 📌 サムネイルのスワイプ機能 */
  let startX = 0;
  let scrollLeft = 0;
  let isDragging = false;

  // ✅ subImageContainer があるページだけ実行（無いページで scripts.js を落とさない）
  if (subImageContainer) {
    subImageContainer.addEventListener('mousedown', (e) => {
      isDragging = true;
      startX = e.pageX - subImageContainer.offsetLeft;
      scrollLeft = subImageContainer.scrollLeft;
      subImageContainer.style.cursor = 'grabbing';
    });

    subImageContainer.addEventListener('mouseleave', () => {
      isDragging = false;
      subImageContainer.style.cursor = 'grab';
    });

    subImageContainer.addEventListener('mouseup', () => {
      isDragging = false;
      subImageContainer.style.cursor = 'grab';
    });

    subImageContainer.addEventListener('mousemove', (e) => {
      if (!isDragging) return;
      e.preventDefault();
      const x = e.pageX - subImageContainer.offsetLeft;
      const walk = (x - startX) * 1.5;
      subImageContainer.scrollLeft = scrollLeft - walk;
    });

    subImageContainer.addEventListener(
      'touchstart',
      (e) => {
        startX = e.touches[0].clientX;
        scrollLeft = subImageContainer.scrollLeft;
        isDragging = true;
      },
      { passive: true },
    );

    subImageContainer.addEventListener(
      'touchmove',
      (e) => {
        if (!isDragging) return;
        const x = e.touches[0].clientX;
        const walk = (x - startX) * 1.5;
        subImageContainer.scrollLeft = scrollLeft - walk;
      },
      { passive: true },
    );

    subImageContainer.addEventListener('touchend', () => {
      isDragging = false;
    });
  }

  // **🔹 初期化**
  if (panoramaContainer && panoramaContainer.dataset.panoramaUrl) {
    initPannellum(panoramaContainer.dataset.panoramaUrl);
  } else {
    console.error('❌ 初期パノラマ画像が見つかりません');
    return;
  }
});

document.addEventListener('DOMContentLoaded', function () {
  console.log('✅ タブ切り替えスクリプト開始');
  setTimeout(initializeTabSwitching, 300);
});

function initializeTabSwitching() {
  const commentLink = document.querySelector('.sidebar-comment-num');
  setInitialActiveTabs();
  setupTabLinks('tab-link', 'tab-pane'); // 画像ビュー & パノラマビュー
  setupCustomTabLinks('custom-tab-link', 'custom-tab-pane'); // 予約 & 口コミ
  setupCommentTabSwitch(commentLink);
}

function setInitialActiveTabs() {
  // 画像ビューを初期表示
  switchTab('image-tab', 'tab-pane', 'tab-link');
  // 来店予約を初期表示（開いたままにする）
  switchCustomTab('reservation', 'custom-tab-pane', 'custom-tab-link');
}

// 画像ビュー & パノラマビューのタブ切り替えを設定
function setupTabLinks(linkClass, paneClass) {
  document.querySelectorAll('.' + linkClass).forEach((tabLink) => {
    tabLink.addEventListener('click', function () {
      switchTab(this.getAttribute('data-tab'), paneClass, linkClass);
    });
  });
}

// 予約 & 口コミのタブ切り替えを設定
function setupCustomTabLinks(linkClass, paneClass) {
  document.querySelectorAll('.' + linkClass).forEach((tabLink) => {
    tabLink.addEventListener('click', function () {
      switchCustomTab(this.getAttribute('data-tab'), paneClass, linkClass);
    });
  });
}

// 画像ビュー & パノラマビューのタブを切り替える関数
function switchTab(tabId, paneClass, linkClass) {
  // すべてのタブコンテンツを非表示にする
  document.querySelectorAll('.' + paneClass).forEach((tab) => {
    tab.classList.remove('active');
    tab.style.display = 'none';
  });
  // すべてのタブリンクのアクティブ状態を解除
  document.querySelectorAll('.' + linkClass).forEach((link) => {
    link.classList.remove('active');
  });
  // 選択されたタブを表示
  const targetTab = document.getElementById(tabId);
  if (targetTab) {
    targetTab.classList.add('active');
    targetTab.style.display = 'block';
  }
  // 選択されたタブのリンクをアクティブにする
  const targetLink = document.querySelector(`[data-tab='${tabId}']`);
  if (targetLink) {
    targetLink.classList.add('active');
  }
  // パノラマビューを開いた場合はビューをリサイズ
  if (tabId === 'panorama-tab' && window.viewer) {
    console.log('✅ Pannellum ビューのリサイズを実行');
    window.viewer.resize();
  }
}

// 予約 & 口コミのタブを切り替える関数
function switchCustomTab(tabId, paneClass, linkClass) {
  // すべてのカスタムタブコンテンツを非表示
  document.querySelectorAll('.' + paneClass).forEach((tab) => {
    tab.classList.remove('active');
    tab.style.display = 'none';
  });
  // すべてのカスタムタブリンクのアクティブ状態を解除
  document.querySelectorAll('.' + linkClass).forEach((link) => {
    link.classList.remove('active');
  });

  // 選択されたタブを取得
  const targetTab = document.getElementById('tab-' + tabId);
  const reservationTab = document.getElementById('tab-reservation');

  if (targetTab && reservationTab) {
    if (tabId === 'comments') {
      reservationTab.style.display = 'none'; // 口コミ表示時は来店予約を非表示
    } else {
      reservationTab.style.display = 'block'; // 来店予約タブ表示時は開いたままにする
    }
    targetTab.classList.add('active');
    targetTab.style.display = 'block';
  }

  // 選択されたカスタムタブのリンクをアクティブにする
  const targetLink = document.querySelector(`.custom-tab-link[data-tab='${tabId}']`);
  if (targetLink) {
    targetLink.classList.add('active');
  }
}

// コメント数リンクをクリックしたら口コミタブを開く
function setupCommentTabSwitch(commentLink) {
  if (commentLink) {
    commentLink.addEventListener('click', function () {
      const commentsTabLink = document.querySelector(".custom-tab-link[data-tab='comments']");
      if (commentsTabLink) {
        commentsTabLink.click();
      }
    });
  }
}

// テーブル２ 物件ギャラリー スワイプ & スライダー
document.addEventListener('DOMContentLoaded', function () {
  let slideIndex = 0; // 現在のスライドインデックス
  const slides = document.querySelectorAll('#slider_1 .slide'); // メインスライダーの画像リスト
  const slideCount = slides.length; // スライドの総数
  const thumbnails = document.querySelectorAll('#thumbnail-slider-1 .thumbnail'); // サムネイルリスト
  const slideCountElement = document.getElementById('slide-count-1'); // スライド番号表示
  const thumbnailSlider = document.getElementById('thumbnail-slider-1'); // サムネイルスライダー
  const thumbnailContainer = document.querySelector('.thumbnail-container'); // サムネイルのコンテナ

  // スライドがない場合はエラーメッセージを表示
  if (slideCount === 0) {
    console.error('❌ スライドが見つかりません (#slider_1 .slide)');
    return;
  }

  /**
   * 📌 指定したインデックスのスライドを表示する
   * @param {number} index - 表示したいスライドのインデックス
   */
  function showSlide(index) {
    if (index >= slideCount) {
      slideIndex = 0; // 最後のスライドの次は最初に戻る
    } else if (index < 0) {
      slideIndex = slideCount - 1; // 最初のスライドの前は最後に戻る
    } else {
      slideIndex = index;
    }

    // すべてのスライドを非表示にする
    slides.forEach((slide) => (slide.style.display = 'none'));
    slides[slideIndex].style.display = 'block'; // 現在のスライドを表示

    // スライド番号の更新
    if (slideCountElement) {
      slideCountElement.textContent = `${slideIndex + 1} / ${slideCount}`;
    }

    // サムネイルのアクティブ状態を変更
    thumbnails.forEach((thumb, idx) => {
      thumb.classList.toggle('active-thumbnail', idx === slideIndex);
    });

    // サムネイルスライダーの位置を調整
    moveThumbnailSlider(slideIndex);
  }

  /**
   * 📌 サムネイルスライダーを自動で中央寄せにする
   * @param {number} index - アクティブなスライドのインデックス
   */
  function moveThumbnailSlider(index) {
    if (!thumbnailSlider) return;

    const selectedThumbnail = thumbnails[index]; // 現在のスライドに対応するサムネイル
    if (selectedThumbnail) {
      const sliderWidth = thumbnailContainer.offsetWidth; // サムネイルスライダーの幅
      const thumbnailWidth = selectedThumbnail.offsetWidth; // 選択されたサムネイルの幅
      const selectedLeft = selectedThumbnail.offsetLeft; // 選択されたサムネイルの左位置

      // スライダーを中央寄せに移動
      let moveX = -(selectedLeft - sliderWidth / 2 + thumbnailWidth / 2);
      thumbnailSlider.style.transform = `translateX(${moveX}px)`;
      thumbnailSlider.style.transition = 'transform 0.3s ease-in-out'; // スムーズなアニメーション
    }
  }

  // 🔹 **ナビゲーションボタンのイベントリスナー**
  document.querySelector('.prev').addEventListener('click', () => showSlide(slideIndex - 1));
  document.querySelector('.next').addEventListener('click', () => showSlide(slideIndex + 1));

  // 🔹 **サムネイルクリック時にスライド切り替え**
  thumbnails.forEach((thumb, index) => {
    thumb.addEventListener('click', () => showSlide(index));
  });

  /**
   * 📌 スワイプ（タッチ & マウスドラッグ）イベントの追加
   * @param {HTMLElement} element - スワイプ操作を適用する要素
   * @param {Function} callback - スワイプ方向に応じた処理
   */
  function addSwipeEvents(element, callback) {
    let startX,
      isSwiping = false;

    // **タッチ操作 (モバイル)**
    element.addEventListener(
      'touchstart',
      (event) => {
        startX = event.touches[0].clientX;
        isSwiping = false;
      },
      { passive: false },
    );

    element.addEventListener(
      'touchmove',
      (event) => {
        let currentX = event.touches[0].clientX;
        let diffX = startX - currentX;
        if (Math.abs(diffX) > 30) {
          isSwiping = true;
          event.preventDefault(); // **🔥 iOS / Safari / Firefox のスクロール抑制**
        }
      },
      { passive: false },
    );

    element.addEventListener('touchend', (event) => {
      if (!isSwiping) return;
      let endX = event.changedTouches[0].clientX;
      if (startX > endX + 30) {
        callback(1);
      } else if (startX < endX - 30) {
        callback(-1);
      }
    });

    // **PC対応（マウスドラッグ）**
    let isMouseDown = false;
    let mouseStartX = 0;

    element.addEventListener('mousedown', (event) => {
      isMouseDown = true;
      mouseStartX = event.clientX;
    });

    element.addEventListener('mousemove', (event) => {
      if (!isMouseDown) return;
      let mouseDiffX = mouseStartX - event.clientX;
      if (Math.abs(mouseDiffX) > 30) {
        if (mouseDiffX > 0) {
          callback(1);
        } else {
          callback(-1);
        }
        isMouseDown = false;
      }
    });

    element.addEventListener('mouseup', () => {
      isMouseDown = false;
    });

    element.addEventListener('mouseleave', () => {
      isMouseDown = false;
    });
  }

  // 🔹 **スライド本体のスワイプイベント**
  addSwipeEvents(document.getElementById('slider_1'), (direction) =>
    showSlide(slideIndex + direction),
  );

  // 🔹 **サムネイルスライダーのスワイプイベント**
  addSwipeEvents(thumbnailSlider, (direction) => {
    const newIndex = slideIndex + direction;
    if (newIndex >= 0 && newIndex < slideCount) {
      showSlide(newIndex);
    }
    thumbnailSlider.style.transform += `translateX(${direction * -70}px)`;
  });

  // 🔹 **初期スライドを表示**
  showSlide(slideIndex);
});

/*テーブル3 物件詳細ページ*/
document.addEventListener('DOMContentLoaded', function () {
  // メインスライダー設定
  var swiper3 = new Swiper('#slider_3-container', {
    slidesPerView: 3, // 一度に表示するスライド数（デスクトップ表示では3枚）
    spaceBetween: 10, // スライド間の余白
    loop: true, // 無限ループ
    navigation: {
      nextEl: '.swiper-button-next',
      prevEl: '.swiper-button-prev',
    },
    breakpoints: {
      // レスポンシブ対応
      1200: {
        slidesPerView: 3,
      },
      768: {
        slidesPerView: 2,
      },
      320: {
        slidesPerView: 1,
      },
    },
  });

  // サムネイルスライドの設定
  var swiperThumbnail = new Swiper('.thumbnail-swiper-container', {
    slidesPerView: 5, // サムネイルは一度に5枚表示
    spaceBetween: 10, // サムネイル間の余白
    loop: true, // 無限ループ
    slideToClickedSlide: true, // サムネイルをクリックしたら、メインスライダーも移動
    centeredSlides: false, // アクティブなスライドを中央に配置しない
    breakpoints: {
      1200: {
        slidesPerView: 8, // デスクトップ表示
      },
      768: {
        slidesPerView: 6, // タブレット表示
      },
      320: {
        slidesPerView: 4, // モバイル表示
        spaceBetween: 10, // モバイル表示時のサムネイル間隔を10pxに設定
      },
    },
  });

  // 初期位置を0に設定
  swiperThumbnail.slideTo(0); // 初期位置を1番目に設定
  updateThumbnailBorder(0); // 最初のサムネイルにボーダーを追加

  // サムネイルクリック時に対応するスライドに移動
  document.querySelectorAll('.thumbnail-3').forEach(function (thumbnail, index) {
    thumbnail.addEventListener('click', function () {
      // メインスライダーをクリックされたサムネイルのスライドに移動
      // サムネイルクリックでメインスライダーの表示を1枚ずつずらす
      let newIndex = index; // クリックされたサムネイルのインデックスを取得
      swiper3.slideTo(newIndex); // メインスライダーを移動
      updateThumbnailBorder(newIndex); // サムネイルのボーダー更新
    });
  });

  // メインスライダーがスライドされた際にサムネイルのボーダーを更新
  swiper3.on('slideChange', function () {
    var activeIndex = swiper3.realIndex;
    swiperThumbnail.slideTo(activeIndex); // サムネイルスライダーも追随してスライド
    updateThumbnailBorder(activeIndex); // サムネイルのボーダー更新
    updateSlideNumber(activeIndex); // スライド番号を更新
  });

  // サムネイルのボーダーを更新する関数
  function updateThumbnailBorder(activeIndex) {
    document.querySelectorAll('.thumbnail-3').forEach(function (thumbnail, index) {
      if (index === activeIndex) {
        thumbnail.style.border = '2px solid #0073e6'; // アクティブなサムネイルにボーダーを追加
      } else {
        thumbnail.style.border = ''; // 他のサムネイルのボーダーを削除
      }
    });
  }

  // スライド番号を更新する関数
  function updateSlideNumber(activeIndex) {
    var totalSlides = swiper3.slides.length; // swiperのスライド数を取得
    var currentSlide = activeIndex + 1; // 1から始める番号表示
    document.getElementById('slide-number').textContent = `${currentSlide} / ${totalSlides}`;
  }

  // 初期のスライド番号を設定
  updateSlideNumber(swiper3.realIndex);
});

jQuery(function ($) {
  function getLikedPosts() {
    try {
      return (JSON.parse(localStorage.getItem('likedPosts')) || []).map(String);
    } catch (e) {
      return [];
    }
  }

  function setLikedPosts(arr) {
    const uniq = Array.from(new Set(arr.map(String)));
    localStorage.setItem('likedPosts', JSON.stringify(uniq));
  }

  function setUI(postId, isActive) {
    const $wrap = $('.heart-icon[data-post-id="' + postId + '"]');
    $wrap.toggleClass('active', !!isActive);
    $wrap.find('svg').toggleClass('liked', !!isActive);
    $wrap.attr('aria-pressed', isActive ? 'true' : 'false');
  }

  getLikedPosts().forEach(function (postId) {
    setUI(postId, true);
  });

  $(document).on('click', '.heart-icon', function (e) {
    e.preventDefault();
    e.stopPropagation();

    const $this = $(this);
    const postId = String($this.data('post-id'));
    const prevActive = $this.hasClass('active');
    const nextActive = !prevActive;

    setUI(postId, nextActive);

    let likedPosts = getLikedPosts();
    if (nextActive) {
      likedPosts.push(postId);
    } else {
      likedPosts = likedPosts.filter((id) => id !== postId);
    }
    setLikedPosts(likedPosts);

    $.ajax({
      url: customAjax.ajaxurl,
      type: 'POST',
      dataType: 'json',
      data: {
        action: 'toggle_like',
        post_id: postId,
        liked: nextActive ? 1 : 0,
        nonce: customAjax.nonce,
      },
    })
      .done(function (res) {
        if (!res || res.success !== true) {
          rollback();
          console.error('toggle_like returned error:', res);
          return;
        }

        const likedVal = Number(res.data?.liked ?? (nextActive ? 1 : 0));
        setUI(postId, likedVal === 1);

        let synced = getLikedPosts();
        if (likedVal === 1) {
          synced.push(postId);
        } else {
          synced = synced.filter((id) => id !== postId);
        }
        setLikedPosts(synced);
      })
      .fail(function (xhr) {
        rollback();
        console.error('AJAX failed:', xhr.responseText);
      });

    function rollback() {
      setUI(postId, prevActive);

      let back = getLikedPosts();
      if (prevActive) {
        back.push(postId);
      } else {
        back = back.filter((id) => id !== postId);
      }
      setLikedPosts(back);
    }
  });
});

// jQuery(document).ready(function ($) {
// ハートアイコンがクリックされた場合、ページ遷移を防ぐ
// $('.heart-icon').on('click', function (event) {
// event.preventDefault(); // リンク遷移を防ぐ
// var postId = $(this).data('post-id');

// ここで「いいね」状態を切り替える処理（例: Ajaxリクエストなど）を追加
// 例:
// $.ajax({
//     url: 'your-ajax-handler-url',
//     method: 'POST',
//     data: { post_id: postId, action: 'toggle_like' },
//     success: function(response) {
//         // 成功時の処理
//     }
// });

// アイコンの切り替え
// $(this).find('svg').toggleClass('liked');
// });
// });

// ビュー切り替えと価格フィルターの設定
// まず、ビュー切り替え（リストビュー／グリッドビュー）や価格フィルターの設定を行うコードです。ページの初期状態やURLパラメータに基づいてビューや価格を適用します。
jQuery(document).ready(function ($) {
  // 初期表示時にURLパラメータを取得して反映
  function applyFiltersFromURL() {
    const urlParams = new URLSearchParams(window.location.search);
    const viewMode = urlParams.get('view') || 'list';
    const minPrice = urlParams.get('min_price') || 1000;
    const maxPrice = urlParams.get('max_price') || 4000;

    // ビュー設定
    $('body')
      .toggleClass('gallery-view', viewMode === 'grid')
      .toggleClass('list-view', viewMode !== 'grid');
    $('#view-grid').prop('checked', viewMode === 'grid');
    $('#view-list').prop('checked', viewMode === 'list');

    // 価格設定
    $('#min-price').val(minPrice);
    $('#max-price').val(maxPrice);
  }

  // 初期表示時にフィルターとビューを適用
  applyFiltersFromURL();

  // ビュー切り替え（リスト／ギャラリー）
  $('input[name="view"]').on('change', function () {
    const viewMode = $(this).val();
    $('body')
      .toggleClass('gallery-view', viewMode === 'grid')
      .toggleClass('list-view', viewMode !== 'grid');
    updateURLParam('view', viewMode);
  });

  // 価格フィルター変更時にリロードせずにURLを更新
  $('#min-price, #max-price').on('change', function () {
    updateURLParam('min_price', $('#min-price').val());
    updateURLParam('max_price', $('#max-price').val());
    filterResultsByPrice();
  });

  // URLのパラメータを更新
  function updateURLParam(param, value) {
    const currentURL = new URL(window.location.href);
    currentURL.searchParams.set(param, value);
    window.history.pushState({}, '', currentURL.toString());
  }

  // 価格フィルター適用後の表示更新
  function filterResultsByPrice() {
    console.log('価格フィルター適用:', $('#min-price').val(), $('#max-price').val());
  }
});

jQuery(document).ready(function ($) {
  // ページネーションのリンクをクリックした時の処理
  $(document).on('click', '.blog-pagination a', function (e) {
    e.preventDefault(); // リンクのデフォルトの動作をキャンセル

    var page_url = $(this).attr('href');
    if (!page_url) {
      console.error('Error: ページURLが取得できません');
      return;
    }

    // 現在のURLから`view`パラメータを取得
    var currentUrl = new URL(window.location.href);
    var viewMode = currentUrl.searchParams.get('view') || 'list';

    // `//` の重複を防ぐ
    if (!page_url.endsWith('/')) {
      page_url += '/';
    }

    // page_urlを正しい形に変換
    var newUrl;
    try {
      newUrl = new URL(page_url, window.location.origin);
      newUrl.searchParams.set('view', viewMode); // 'view'パラメータを追加
    } catch (e) {
      console.error('newUrl作成エラー:', e);
      return;
    }

    console.log('AJAX Request URL:', newUrl.toString());

    // URLを更新
    window.history.pushState({}, '', newUrl.toString());

    // Ajaxリクエストを実行
    $.ajax({
      url: newUrl.toString(),
      type: 'GET',
      success: function (data) {
        // 取得したHTMLをページに反映
        $('#main').html($(data).find('#main').html());
        $('.blog-pagination').html($(data).find('.blog-pagination').html());
        applyViewMode(viewMode);
        $('html, body').animate({ scrollTop: 0 }, 'fast');
        bindButtons();
      },
      error: function (xhr, status, error) {
        console.error('AJAXエラー:', status, error);
      },
    });
  });

  // ビューのラジオボタン切り替え
  $(document).on('change', '.view-toggle-radio', function () {
    var viewMode = $(this).val();
    applyViewMode(viewMode);
    var currentUrl = new URL(window.location.href);
    currentUrl.searchParams.set('view', viewMode);
    window.history.pushState({}, '', currentUrl.toString());
  });

  // ビューの変更
  function applyViewMode(viewMode) {
    if (viewMode === 'grid') {
      $('#main').addClass('gallery-view').removeClass('list-view');
    } else {
      $('#main').addClass('list-view').removeClass('gallery-view');
    }
  }

  // 現在のURLからビューのモードを取得
  function getViewModeFromUrl() {
    var currentUrl = new URL(window.location.href);
    return currentUrl.searchParams.get('view') || 'list';
  }

  // ボタンのイベントを設定
  function bindButtons() {
    $('#store-reservation')
      .off('click')
      .on('click', function () {
        alert('予約ボタンがクリックされました');
      });
  }

  // 初期状態でビューを設定
  (function () {
    var viewMode = getViewModeFromUrl();
    applyViewMode(viewMode);
  })();
});

// 価格範囲を更新する関数
/*function updatePriceRange() {
    var priceRangeSlider = document.getElementById("price-range-slider");
    
    // スライダーの値を取得
    var sliderValue = parseInt(priceRangeSlider.value);

    // カテゴリごとの価格範囲を設定
    var category = "<?php echo get_queried_object()->slug; ?>"; // 現在のカテゴリースラッグを取得

    var minPrice = sliderValue;
    var maxPrice = 0;

    // カテゴリに応じた価格範囲の設定
    if (category === 'naigai-construction') {
        maxPrice = minPrice + 1000; // 最大価格は最小価格から1000万円高い
    } else if (category === 'naigai-tochi') {
        // `naigai-tochi`カテゴリーは、最小価格が100万から、最大価格が2000万円の範囲
        // 最小価格は100万円から始まり、最大価格は2000万円に制限
        maxPrice = Math.min(minPrice + 1900, 2000); // 最小価格に1900万円を加算しても最大2000万円
    } else {
        maxPrice = minPrice + 1000; // 他のカテゴリはデフォルトで1000万円
    }

    // 最大価格が最小価格以下にならないように修正
    if (maxPrice < minPrice) {
        maxPrice = minPrice + 1000; // 最小価格より低い場合は1000万円上限に設定
    }

    // ラベルを更新（万円単位で表示）
    document.getElementById("price_range_label").textContent = minPrice + '万円';
    document.getElementById("max_price_label").textContent = maxPrice + '万円';

    // URLパラメータを更新（万円単位）
    var url = new URL(window.location.href);
    url.searchParams.set('min_price', minPrice);  // 最小価格
    url.searchParams.set('max_price', maxPrice);  // 最大価格

    // ページ番号を1にリセット
    url.searchParams.set('paged', 1);

    // 新しいURLにリダイレクト
    window.location.href = url.toString();
}*/

// 物件詳細ページの最後のスライダー
jQuery(document).ready(function ($) {
  // Slickスライダーの初期化
  var $slider = $('.popular-post-slider .slick-slider'); // スライダーのインスタンスを取得

  $slider.slick({
    autoplay: false, // 自動スライドを無効化
    dots: false, // ドットナビゲーションを無効化
    arrows: false, // デフォルトの矢印ボタンを無効化
    speed: 300, // スライドのスピード
    infinite: true, // 無限ループを有効にする
  });

  // カスタム矢印ボタンの設定
  $('.popular-slider-prev').click(function () {
    $slider.slick('slickPrev'); // slickPrev()を呼び出す
  });
  $('.popular-slider-next').click(function () {
    $slider.slick('slickNext'); // slickNext()を呼び出す
  });
});

// p_is_mobile() が正常に動作していない場合、JavaScriptで画面幅を判定し、ページが表示された後にメニューの表示を制御
/*document.addEventListener('load', function() {
    // 初回読み込み時に画面幅を取得してメニューの表示を切り替え
    toggleMenuBasedOnWidth();

    // リサイズ時にもメニュー表示を切り替える
    window.addEventListener('resize', function() {
        toggleMenuBasedOnWidth();
    });

    function toggleMenuBasedOnWidth() {
        var isMobile = window.innerWidth <= 768; // モバイル判定
        var mobileMenu = document.querySelector('.header-menu-mobile');
        var pcMenu = document.querySelector('.header-menu-pc');

        if (isMobile) {
            if (mobileMenu) mobileMenu.style.display = 'block';
            if (pcMenu) pcMenu.style.display = 'none';
        } else {
            if (mobileMenu) mobileMenu.style.display = 'none';
            if (pcMenu) pcMenu.style.display = 'block';
        }
    }
});*/

// モーダル地図を開く関数　修正中
document.addEventListener('DOMContentLoaded', function () {
  // モーダル要素（Googleマップ表示用のポップアップ）をIDで取得
  var modal = document.getElementById('googleMapModal'); // モーダルのIDは 'googleMapModal' です

  // Google位置リンクをクリックしてモーダルを表示するためのリンク要素をIDで取得
  var openMapLink = document.getElementById('openMapLink'); // リンクのIDは 'openMapLink' です

  // モーダルを閉じるためのボタンをIDで取得
  var closeModal = document.getElementById('closeModal'); // 閉じるボタンのIDは 'closeModal' です

  // アイコンのSVG要素をクラス名で取得（クリックでモーダル表示）
  var iconLocation = document.querySelector('.icon-location'); // クラス名でアイコン（'icon-location'）を取得

  // 必要な要素がすべて存在する場合に処理を続ける
  if (modal && openMapLink && closeModal && iconLocation) {
    // Google位置リンク（ID: openMapLink）をクリックしたとき、モーダルを表示
    openMapLink.addEventListener('click', function () {
      modal.style.display = 'block'; // モーダルを表示するためにスタイルを 'block' に設定
    });

    // アイコン（クラス: icon-location）をクリックしたとき、モーダルを表示
    iconLocation.addEventListener('click', function () {
      modal.style.display = 'block'; // モーダルを表示するためにスタイルを 'block' に設定
    });

    // モーダル内の閉じるボタン（ID: closeModal）をクリックしたとき、モーダルを非表示
    closeModal.addEventListener('click', function () {
      modal.style.display = 'none'; // モーダルを非表示にするためにスタイルを 'none' に設定
    });

    // モーダル自体（背景部分）をクリックしたとき、モーダルを非表示にする処理
    modal.addEventListener('click', function (event) {
      if (event.target === modal) {
        // クリックされたターゲットがモーダル本体か確認
        modal.style.display = 'none'; // モーダルを非表示にする
      }
    });
  } else {
    console.error(
      '必要な要素が見つかりません: googleMapModal, openMapLink, icon-location, or closeModal',
    );
  }
});

// 予約モーダル　書き込み
document.addEventListener('DOMContentLoaded', () => {
  const $modal = jQuery('#store-reservation-modal');
  const $closeBtn = $modal.find('.close-btn');
  const $form = $modal.find('form'); // ✅ フォームを取得

  var isRecruitmentPage = window.location.href.includes('/recruitment');

  jQuery(document).on('click', '.store-reserve-link', function (event) {
    event.preventDefault();
    console.log('✅ リンクがクリックされました');

    var postElement = jQuery(this).closest('.main-custom-post, li');
    var post_id = jQuery(this).data('post-id');

    // **タイトル取得**
    var title =
      postElement.find('h1').text().trim() ||
      postElement.find('h2 a').text().trim() ||
      postElement.find('h2').text().trim();

    if (!title || title === '') {
      title = isRecruitmentPage ? '採用情報' : '物件情報';
    }

    $modal.find('#reservation-property-title').text(title);

    // **サムネイル画像の取得**
    var thumbnail = '';
    var video_id = postElement.find('lite-youtube').attr('videoid');

    if (postElement.find('.blog-post-image').hasClass('youtube') && video_id) {
      thumbnail = 'https://i.ytimg.com/vi/' + video_id + '/hqdefault.jpg';
    } else if (postElement.find('.blog-post-image').hasClass('vimeo')) {
      var vimeoThumbnail = postElement.find('lite-vimeo').css('background-image');
      if (vimeoThumbnail && vimeoThumbnail !== 'none') {
        thumbnail = vimeoThumbnail.replace(/url\(["']?(.*?)["']?\)/, '$1');
      }
    } else {
      var backgroundImage = postElement.find('.blog-post-image').css('background-image');
      if (backgroundImage && backgroundImage !== 'none') {
        thumbnail = backgroundImage.replace(/url\(["']?(.*?)["']?\)/, '$1');
      }
    }

    // **デフォルトサムネイル画像**
    if (!thumbnail || thumbnail === '') {
      thumbnail = 'https://naigaicorp.net/wp-content/themes/Xiaoyu%20Tekken7/images/noimage.gif';
    }

    // **モーダルにサムネイルを表示**
    $modal.find('#reservation-property-thumbnail').css({
      'background-image': 'url(' + thumbnail + ')',
      'background-size': 'cover',
      'background-position': 'center',
    });

    // **モーダルのヘッダーを設定**
    $modal.find('#reservation-header').text(isRecruitmentPage ? '面接予約' : '来店予約');

    // **給与 or 価格の取得**
    var price = '';
    if (isRecruitmentPage) {
      var salaryElement = postElement
        .find('.recruitment-info-table .recruitment-row .recruitment-label')
        .filter(function () {
          return jQuery(this).text().trim() === '給与';
        })
        .next('.recruitment-value');

      price = salaryElement.length ? salaryElement.text().trim() : '応相談';
    } else {
      var priceElement = postElement
        .find('.property-info-table .property-row .property-label')
        .filter(function () {
          return jQuery(this).text().trim() === '価格';
        })
        .next('.property-value');

      price = priceElement.length ? priceElement.text().trim() : '売却済';
    }

    // **モーダル内の情報を設定**
    // IDは数値だけセット（formDataで使う）
    $modal.find('#reservation-property-id').text(post_id);

    // ラベルは別の span にセット（表示専用）
    $modal.find('#reservation-property-label').text(isRecruitmentPage ? '採用ID: ' : '物件ID: ');

    $modal.find('#reservation-property-price').text(price === '売却済' ? '売却済' : price);

    console.log('✅ モーダルを開く処理が実行されました');

    // **モーダルを開く & 画面リセット**
    $modal.addClass('active').css({ display: 'flex', visibility: 'visible' }).fadeIn(300);
    $modal.find('#step-input').show(); // 入力画面を表示
    $modal.find('#step-confirm').hide(); // 確認画面を非表示
    $modal.find('#step-complete').hide(); // 完了画面を非表示
  });

  // **モーダルを閉じる処理**
  function closeModal() {
    console.log('✅ モーダルを閉じる処理が実行されました');

    // **フォームの内容をリセット**
    $form[0].reset();

    // **モーダルを閉じる際に画面をリセット**
    $modal.removeClass('active').fadeOut(300, function () {
      $modal.css({ visibility: 'hidden', display: 'none' });

      // **入力画面を表示**
      $modal.find('#step-input').show();
      $modal.find('#step-confirm').hide();
      $modal.find('#step-complete').hide();

      console.log('✅ モーダルを完全に閉じました（フォームリセット済み）');
    });
  }

  // **閉じるボタンの処理**
  $closeBtn.on('click', function () {
    closeModal();
  });

  // **モーダルの背景クリックで閉じる**
  jQuery(document).on('click', function (e) {
    if (jQuery(e.target).is('#store-reservation-modal.active')) {
      console.log('✅ モーダル背景がクリックされました');
      closeModal();
    }
  });
});

// **script 側でのフォームの送信・リセット　リックの動作**
/*jQuery(document).ready(function () {
    // **確認画面を表示**
    jQuery('#to-confirm-modal, #to-confirm-page').on('click', function () {
        const buttonId = jQuery(this).attr('id');
        const parent = jQuery(this).closest('.store-reservation-modal-content, .store-reservation-page-content');

        // **バリデーションチェック**
        if ((buttonId === 'to-confirm-modal' && !validateForm('.store-reservation-modal-content')) || 
            (buttonId === 'to-confirm-page' && !validateForm('.store-reservation-page-content'))) {
            return;  // バリデーションが失敗した場合、処理を中止
        }

        // **フォームデータ取得＆確認画面更新**
        const formData = getFormData(parent);
        updateConfirmationData(formData);

        // **モーダル or 固定ページの処理**
        if (buttonId === 'to-confirm-modal') {
            showConfirmationScreen(formData, '.store-reservation-modal-content');
        } else if (buttonId === 'to-confirm-page') {
            showConfirmationScreen(formData, '.store-reservation-page-content');
        }
    });

    // **戻るボタンの処理**
    jQuery('.store-reservation-modal-content, .store-reservation-page-content').on('click', '#back-to-input', function () {
        const parent = jQuery(this).closest('.store-reservation-modal-content, .store-reservation-page-content');
        parent.find('#step-confirm').hide();  // 確認画面を非表示
        parent.find('#step-input').show();  // 入力フォームを表示
    });

    // **モーダルと固定ページの両方に対応（完了フォームのリセット処理）**
    jQuery('.store-reservation-modal-content, .store-reservation-page-content').on('click', '#submit-reservation', function () {
        const parent = jQuery(this).closest('.store-reservation-modal-content, .store-reservation-page-content');

        if (parent.length === 0) {
            console.error("親要素が取得できませんでした");
            return;
        }

        parent.find('#step-input, #step-confirm').hide();
        parent.find('#step-complete').show();

        const formData = getFormData(parent);
        sendReservationData(formData, parent);

        setTimeout(function () {
            console.log("フォームリセット処理開始");

            // **フォームをリセット**
            resetForm(parent);

            // **固定ページの場合のみリセット後に再表示**
            if (parent.hasClass('store-reservation-page-content')) {
                console.log("固定ページのフォームをリセット後、再表示");
                parent.find('#step-complete').hide();
                parent.find('#step-input').css({ 'display': 'block', 'visibility': 'visible' }).show();
            } 
            
            // **モーダルの場合は閉じる**
            if (parent.hasClass('store-reservation-modal-content')) {
                console.log("モーダルを閉じる");
                parent.closest('.store-reservation-modal').fadeOut(300, function () {
                    jQuery(this).removeClass('active');
                });
            }

            console.log("フォームリセット処理完了");
        }, 5000);
    });

// **フォームをリセットする関数**
function resetForm(parent) {
    console.log("フォームをリセットします");

    // **すべての入力フィールドをリセット**
    parent.find('input[type="text"], input[type="email"], input[type="tel"], input[type="number"], textarea').val('');

    // **ファイル入力をリセット**
    parent.find('input[type="file"]').val('');

    // **チェックボックス・ラジオボタンのリセット**
    parent.find('input[type="checkbox"], input[type="radio"]').prop('checked', false);

    // **選択リストのデフォルト値にリセット**
    parent.find('select').each(function () {
        let defaultVal = jQuery(this).find('option[selected]').val() || jQuery(this).find('option:first').val();
        jQuery(this).val(defaultVal).trigger('change');
    });

    // 🔹 **アップロード画像をリセット**
    uploadedImages = []; // **画像リストをクリア**
    
    // 🔹 **画像プレビューをリセット**
    displayUploadedImages(uploadedImages, '.image-preview-container'); 
    displayUploadedImages(uploadedImages, '#confirm-image-container');

    console.log("フォームリセット完了");
}




    // **HTMLタグを含んでいるかチェックする関数**
    function containsHTML(str) {
        let pattern = /<\/?[^>]+(>|$)/g;  // HTMLタグを検出する正規表現
        return pattern.test(str);
    }


    // **画像アップロードを最初に処理**
function uploadImagesAndSubmitForm(formData, parent) {
    if (uploadedImages.length > 0) {
        uploadImages(uploadedImages, function (uploadedUrls) {
            formData.uploaded_images = uploadedUrls; // ✅ 画像URLを取得後に送信
            sendReservationData(formData, parent);
        });
    } else {
        formData.uploaded_images = []; // ✅ 画像なしで送信
        sendReservationData(formData, parent);
    }
}

    // **画像アップロード**
    function uploadImages(images, callback) {
        let formData = new FormData();
        formData.append('action', 'upload_images');
        
        images.forEach((image, index) => {
            formData.append('images[]', image);
        });

        jQuery.ajax({
            url: customAjax.ajaxurl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.success) {
                    callback(response.uploaded_image_urls);
                } else {
                    alert('画像アップロードに失敗しました');
                    callback([]);
                }
            },
            error: function () {
                alert('画像アップロード時に通信エラーが発生しました');
                callback([]);
            }
        });
    }


    function sendReservationData(formData, parent) {
        // **AJAX送信前に売却査定の選択リストの値を日本語に変換**
        formData.property_status = propertyStatusMap[formData.property_status] || "未選択";
        formData.sale_reason = saleReasonMap[formData.sale_reason] || "未選択";
        formData.sale_period = salePeriodMap[formData.sale_period] || "未選択";
        formData.valuation_method = valuationMethodMap[formData.valuation_method] || "未選択";

        // **売却理由のバリデーション（HTMLタグ禁止 & 最大文字数）**
        formData.sale_reason_text = formData.sale_reason_text || "";  // undefined を防ぐ

        if (formData.sale_reason_text.trim() !== "" && containsHTML(formData.sale_reason_text)) {
            alert("売却理由にHTMLタグは使用できません。");
            return;
        }

        if (formData.sale_reason_text.trim() !== "" && formData.sale_reason_text.length > 500) {
            alert("売却理由は500文字以内で入力してください。");
            return;
        }

        jQuery.ajax({
            url: customAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'store_reservation_submit',
                store_reservation_nonce: jQuery('#store_reservation_nonce').val(),
                data: JSON.stringify(formData)  // **マッピング後のデータを送信**
            },
            success: function (response) {
                if (response.success) {
                    parent.find('#step-complete').show();
                } else {
                    alert('送信に失敗しました：' + (response.data.message || '原因不明'));
                }
            },
            error: function (xhr, status, error) {
                alert('通信エラーが発生しました。再度お試しください。');
            }
        });
    }


});// 終了タグ*/

jQuery(document).ready(function ($) {
  // 郵便番号（既存）
  $('.postcode').on('input', function () {
    let val = $(this)
      .val()
      .replace(/[^0-9]/g, '');
    if (val.length > 3) {
      val = val.slice(0, 3) + '-' + val.slice(3, 7);
    }
    $(this).val(val);
  });

  // 電話番号（携帯・固定電話を網羅的に対応）
  $('.phone')
    .off('input.phoneFormat') // ✅ 二重バインド防止（自分のだけ外す）
    .on('input.phoneFormat', function () {
      let val = $(this).val().replace(/\D/g, ''); // 数字だけ

      // 携帯 / IP電話（070/080/090/050）: 3-4-4（最大11桁）
      if (/^(070|080|090|050)/.test(val)) {
        val = val.slice(0, 11);
        val = val.replace(/^(\d{3})(\d{0,4})(\d{0,4}).*$/, function (_, a, b, c) {
          return [a, b, c].filter(Boolean).join('-');
        });

        // フリーダイヤル（0120）: 4-3-3（最大10桁）
      } else if (/^0120/.test(val)) {
        val = val.slice(0, 10);
        val = val.replace(/^(\d{4})(\d{0,3})(\d{0,3}).*$/, function (_, a, b, c) {
          return [a, b, c].filter(Boolean).join('-');
        });

        // フリーダイヤル（0800）: 4-3-4（最大11桁）
      } else if (/^0800/.test(val)) {
        val = val.slice(0, 11);
        val = val.replace(/^(\d{4})(\d{0,3})(\d{0,4}).*$/, function (_, a, b, c) {
          return [a, b, c].filter(Boolean).join('-');
        });

        // 東京(03) / 大阪(06): 2-4-4（最大10桁）
      } else if (/^(03|06)/.test(val)) {
        val = val.slice(0, 10);
        val = val.replace(/^(\d{2})(\d{0,4})(\d{0,4}).*$/, function (_, a, b, c) {
          return [a, b, c].filter(Boolean).join('-');
        });

        // その他の固定：暫定 3-3-4（最大10桁）
      } else {
        val = val.slice(0, 10);
        val = val.replace(/^(\d{3})(\d{0,3})(\d{0,4}).*$/, function (_, a, b, c) {
          return [a, b, c].filter(Boolean).join('-');
        });
      }

      $(this).val(val);
    });
});

// 郵便番号から住所を自動入力する関数（無制限に対応）　page-satei-reservation html 属性 name="address" と name="property_address" 両方に対応：
// ✅ 正式対応版：複数の data-index で address / property_address 両方に対応
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.postcode').forEach((postcodeElement) => {
    postcodeElement.addEventListener('blur', function () {
      const form = postcodeElement.closest('form');
      if (!form) return;

      const postcodeIndex = postcodeElement.dataset.index;

      // 👇 form 内の address 全部から対象 index を持つものだけ取得
      const addressEl = Array.from(form.querySelectorAll('.input-field[name="address"]')).find(
        (el) => el.dataset.index === postcodeIndex,
      );

      const propertyAddressEl = Array.from(
        form.querySelectorAll('.input-field[name="property_address"]'),
      ).find((el) => el.dataset.index === postcodeIndex);

      if (addressEl) {
        autoFillAddress(postcodeElement, addressEl);
      }
      if (propertyAddressEl) {
        autoFillAddress(postcodeElement, propertyAddressEl);
      }
    });
  });
});

// 郵便番号  data-index="1" が郵便番号と住所で同じ値であることで郵便番号1 に対応する住所1 に自動補完

/**
 * 郵便番号から住所を取得し、該当する住所欄に自動入力する関数
 *
 * @param {HTMLElement} postcodeElement - 郵便番号の入力フィールド
 * @param {HTMLElement} addressElement - 対応する住所の入力フィールド
 */
function autoFillAddress(postcodeElement, addressElement) {
  // 郵便番号や住所の入力欄が存在しない場合は処理を中止（安全対策）
  if (!postcodeElement || !addressElement) {
    console.error('autoFillAddress: addressElement が未定義です。処理を中止します。');
    return;
  }

  // ユーザーが入力した郵便番号からハイフンを除去（例: "100-0001" → "1000001"）
  const postcode = postcodeElement.value.replace('-', '');

  console.log(`郵便番号検索開始: ${postcode}`);

  // 郵便番号が7桁の場合にAPIリクエストを実行
  if (postcode.length === 7) {
    fetch(`https://zipcloud.ibsnet.co.jp/api/search?zipcode=${postcode}`)
      .then((response) => response.json())
      .then((data) => {
        // APIのレスポンスが正常で、住所が取得できた場合
        if (data.status === 200 && data.results) {
          const addressData = data.results[0];

          // 住所を結合（例: 東京都千代田区霞が関）＋末尾に半角スペース
          const fullAddress = `${addressData.address1}${addressData.address2}${addressData.address3} `;

          // 住所フィールドに値をセット
          addressElement.value = fullAddress;

          // 入力しやすいように、カーソルを末尾に移動（地番などすぐ入力できる）
          addressElement.focus();
          addressElement.setSelectionRange(fullAddress.length, fullAddress.length);

          console.log(`住所入力完了: ${fullAddress}`);
        } else {
          // 郵便番号が無効な場合や一致しない場合
          console.warn('住所が見つかりませんでした');
          addressElement.value = '';
        }
      })
      .catch((error) => {
        // API通信エラー時の処理
        console.error('APIリクエストに失敗:', error);
        addressElement.value = '';
      });
  } else {
    // 郵便番号が7桁未満の場合
    console.warn('7桁の郵便番号を入力してください');
    addressElement.value = '';
  }
}

/**
 * 郵便番号フィールドに blur イベント（入力後フォーカスが外れたとき）を設定し、
 * モーダルフォームと通常ページの両方で住所自動補完を有効にする関数
 */
function setupAddressAutoFill() {
  // 🔹 モーダルの郵便番号・住所フィールドを取得（class名でスコープ限定）
  const modalPostcode = document.querySelector('.store-reservation-modal-content #postcode');
  const modalAddress = document.querySelector('.store-reservation-modal-content #address');

  // 🔹 通常ページの郵便番号・住所フィールドを取得
  const pagePostcode = document.querySelector('.store-reservation-page-content #postcode');
  const pageAddress = document.querySelector('.store-reservation-page-content #address');

  // ✅ モーダル側の郵便番号入力時に → 自動で住所入力
  if (modalPostcode && modalAddress) {
    modalPostcode.addEventListener('blur', function () {
      autoFillAddress(this, modalAddress);
    });
  }

  // ✅ ページ側の郵便番号入力時に → 自動で住所入力
  if (pagePostcode && pageAddress) {
    pagePostcode.addEventListener('blur', function () {
      autoFillAddress(this, pageAddress);
    });
  }
}

// 🔹 ページ読み込み時に setupAddressAutoFill を実行して初期化する
document.addEventListener('DOMContentLoaded', function () {
  if (typeof setupAddressAutoFill === 'function') setupAddressAutoFill();
});

/**
 * 指定フィールドにカスタムバリデーションを付与（存在する場合のみ）
 * @param {jQuery} parent  フォームの親コンテナ（.store-reservation-xxx-content）
 * @param {string} fieldId #id セレクタ
 * @param {Function} validationFn (value) => true/false
 * @param {string} errorMessage   失敗時メッセージ
 */
function validateField(parent, fieldId, validationFn, errorMessage) {
  const field = parent.find(fieldId)[0];
  if (!field) return; // 要素が無ければ何もしない（ページ/モーダル/査定で差がある前提）

  field.setCustomValidity(''); // まずリセット
  const value = (field.value ?? '').toString();

  if (!validationFn(value)) {
    field.setCustomValidity(errorMessage);
  }
}

/**
 * フォーム全体のバリデーション（重要：selector文字列ではなく parent(jQuery) を渡す）
 * @param {jQuery} parent  .store-reservation-modal-content or .store-reservation-page-content
 * @returns {boolean}
 */
function validateForm(parent) {
  // ✅ parent が壊れてたら止める
  if (!parent || parent.length === 0) {
    console.error('validateForm: parent が不正', parent);
    return false;
  }

  // ✅ フォーム要素を「その親の中」から拾う（ID重複でもブレない）
  let $form = parent.find('#store-reservation-form').first();
  if ($form.length === 0) $form = parent.find('form').first();

  const form = $form[0];
  if (!form) {
    console.error('validateForm: form が見つからない', parent[0]);
    return false;
  }

  // -----------------------------
  // ▼ 各フィールドのカスタムチェック
  // -----------------------------

  // 姓（漢字など）
  validateField(
    parent,
    '#last_name',
    (v) => /^[ぁ-んァ-ヶ一-龯々〆〤ー]+$/.test(v),
    '姓は漢字、ひらがな、カタカナで入力してください',
  );

  // 名（漢字など）
  validateField(
    parent,
    '#first_name',
    (v) => /^[ぁ-んァ-ヶ一-龯々〆〤ー]+$/.test(v),
    '名は漢字、ひらがな、カタカナで入力してください',
  );

  // セイ（カタカナ）
  validateField(
    parent,
    '#last_name_kana',
    (v) => /^[ァ-ヶー]+$/.test(v),
    'セイはカタカナで入力してください',
  );

  // メイ（カタカナ）
  validateField(
    parent,
    '#first_name_kana',
    (v) => /^[ァ-ヶー]+$/.test(v),
    'メイはカタカナで入力してください',
  );

  // メールアドレス
  validateField(
    parent,
    '#email',
    (v) => /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(v),
    '有効なメールアドレスを入力してください',
  );

  // 電話番号（ハイフン付き形式：03-1234-5678 / 090-1234-5678 / 0120-123-456 などを許容）
  validateField(
    parent,
    '#phone',
    (v) => /^0\d{1,4}-\d{1,4}-\d{3,4}$/.test(v),
    '電話番号は正しい形式で入力してください（ハイフンは自動で入ります）',
  );

  // 郵便番号
  validateField(
    parent,
    '#postcode',
    (v) => /^\d{3}-\d{4}$/.test(v),
    '郵便番号は123-4567の形式で入力してください',
  );

  // 住所（空はNG）
  validateField(parent, '#address', (v) => v.trim() !== '', '住所を入力してください');

  // 売却理由（任意）500文字以内、HTML禁止（存在する場合のみ）
  validateField(
    parent,
    '#sale_reason_text',
    (v) => v.trim() === '' || (!containsHTML(v) && v.length <= 500),
    '売却理由は500文字以内で入力してください（HTMLタグは禁止）',
  );

  // ▼ 売却査定系（存在する時だけチェック）
  validateField(parent, '#property_status', (v) => !!v, '物件の現況は必須です');
  validateField(parent, '#sale_reason', (v) => !!v, '査定を希望する理由は必須です');
  validateField(parent, '#sale_period', (v) => !!v, '売却希望時期は必須です');
  validateField(parent, '#valuation_method', (v) => !!v, '査定方法は必須です');

  // 数値（任意）
  validateField(
    parent,
    '#land_area',
    (v) => !v || /^[0-9]+(\.[0-9]+)?$/.test(v),
    '土地面積(㎡)は数値で入力してください',
  );
  validateField(
    parent,
    '#building_area',
    (v) => !v || /^[0-9]+(\.[0-9]+)?$/.test(v),
    '建物面積(㎡)は数値で入力してください',
  );
  validateField(
    parent,
    '#rental_income',
    (v) => !v || /^[0-9]+(\.[0-9]+)?$/.test(v),
    '賃料収入(万円/月)は数値で入力してください',
  );

  // -----------------------------
  // ▼ HTML5 バリデーション実行（not focusable対策）
  // -----------------------------

  // ✅ まず「visit-date / time-slot」を整える（ここが先）
  const hasMethod = $form.find('#valuation_method').length > 0;
  const method = hasMethod ? $form.find('#valuation_method').val() : null;

  // valuation_method があるフォーム：visit の時だけ必須
  // 無いフォーム：見えてるなら必須（必要なら false に変えてOK）
  const needVisit = hasMethod ? method === 'visit' : true;

  $form.find('#visit-date, #time-slot, [name="visit-date"], [name="time-slot"]').each(function () {
    const el = this;
    const $el = jQuery(el);

    const visible = $el.is(':visible') && el.getClientRects().length > 0;

    // ✅ disabled は「見えてない時だけ」
    el.disabled = !visible;

    // ✅ required は「見えてる + 必要な時だけ」
    el.required = visible && needVisit;

    // hidden/disabled 時に残ってるエラーを消す
    if (!visible && typeof el.setCustomValidity === 'function') el.setCustomValidity('');
  });

  // ✅ その後に checkValidity（順番これが正解）
  if (form.checkValidity()) return true;

  // ✅ 「見えてる invalid」だけを狙ってフォーカス（not focusable回避）
  const $invalid = parent.find(':input:invalid').filter(':visible').first();

  if ($invalid.length) {
    const el = $invalid[0];
    el.focus();
    if (typeof el.reportValidity === 'function') el.reportValidity();
  } else {
    console.error('invalidはあるが、見えてるinvalidが無い（hidden required / 重複の可能性）');
  }

  return false;
}

/**
 * 入力された文字列にHTMLタグが含まれているかを判定する関数　フォームにhtmlをブロックするため　売却理由欄（#sale_reason_text）
 *
 * 例えば以下のようなタグを検出する：
 *   - <b>太字</b>
 *   - <script>alert('xss')</script>
 *   - <div class="example">
 *
 * 対象文字列に "<タグ>" のようなパターンがあれば true を返す
 * なければ false（＝HTMLタグが含まれていない）
 *
 * @param {string} str - チェック対象の文字列
 * @returns {boolean} - HTMLタグが含まれている場合は true、含まれていなければ false
 */
function containsHTML(str) {
  // <や</ではじまるHTMLタグっぽい構文を検出する正規表現
  const pattern = /<\/?[a-z][\s\S]*?>/i;

  // 上記の正規表現にマッチしたら true を返す
  return pattern.test(str);
}

/**
 * 選択リストの value を日本語のラベルに変換する関数
 *
 * 大文字・小文字の違いを吸収して、常に正しくマッピングできるようにする。
 *
 * @param {Object} map - マッピングオブジェクト（例：floorPlanMap）
 * @param {string} value - フォームから取得した選択肢の値（例："1K", "1k", "1LDK"）
 * @returns {string} - マッピングされた日本語ラベル（例："1K", "1LDK"）または "未設定"
 */
function getMappedValue(map, value) {
  // `map` または `value` が未定義なら "未設定" を返す
  if (!map || !value) return '未設定';

  // 🔹 `value` を小文字に変換してから検索
  // 例: "1K" → "1k", "1LDK" → "1ldk"
  let lowerValue = value.toLowerCase();

  // 🔹 小文字に変換した値で `map` を検索し、マッチする値があれば返す
  // 例: map["1k"] が存在すれば "1K" を返す
  return map[lowerValue] ? map[lowerValue] : '未設定';
}

// 下記は、英語の用語を日本語に変化するために、propertyStatusMap　などに定義している。

// **物件の現況 マッピング**
if (!window.propertyStatusMap) {
  window.propertyStatusMap = {
    land_with_building: '土地付き建物',
    vacant_land: '更地',
    detached_house: '戸建',
    condominium: 'マンション区分',
    rented: '商業施設',
  };
}

// **査定を希望する理由 マッピング**
if (!window.saleReasonMap) {
  window.saleReasonMap = {
    inheritance: '相続',
    relocation: '住み替え',
    financial: '金銭的理由',
    investment: '投資物件の売却',
  };
}

// **売却希望時期 マッピング**
if (!window.salePeriodMap) {
  window.salePeriodMap = {
    '1_month': '1ヶ月以内',
    '3_months': '3ヶ月以内',
    '6_months': '6ヶ月以内',
    '1_year': '1年以内',
    undecided: '未定',
  };
}

// **査定方法 マッピング**
if (!window.valuationMethodMap) {
  window.valuationMethodMap = {
    desk: '机上査定（簡易）',
    visit: '訪問査定（詳細）',
  };
}

// **建物面積 マッピング**
if (!window.buildingAreaMap) {
  window.buildingAreaMap = {
    small: '50㎡未満',
    medium: '50㎡〜100㎡',
    large: '100㎡以上',
  };
}

// **築年数 マッピング**
if (!window.yearBuiltMap) {
  window.yearBuiltMap = {
    new: '新築',
    '5_years': '5年以内',
    '10_years': '10年以内',
    over_10_years: '10年以上',
  };
}

// **間取り マッピング**
if (!window.floorPlanMap) {
  window.floorPlanMap = {
    '1k': '1K',
    '1ldk': '1LDK',
    '2ldk': '2LDK',
    '3ldk': '3LDK',
    '4ldk': '4LDK以上',
  };
}

// **賃料収入 マッピング**
if (!window.rentalIncomeMap) {
  window.rentalIncomeMap = {
    none: '賃料なし',
    low: '10万円以下',
    medium: '10万円〜20万円',
    high: '20万円以上',
  };
}

// **売却希望価格 マッピング**
/*if (!window.salePriceMap) {
    window.salePriceMap = {
        "low": "1000万円以下",
        "medium": "1000万円〜3000万円",
        "high": "3000万円以上"
    };
}*/

// **査定物件の郵便番号 マッピング**
/*if (!window.propertyPostcodeMap) {
    window.propertyPostcodeMap = {
        "none": "郵便番号なし",
        "valid": "設定済み"
    };
}*/

// **査定物件の住所 マッピング**
/*if (!window.propertyAddressMap) {
    window.propertyAddressMap = {
        "none": "住所なし",
        "valid": "設定済み"
    };
}

// **アップロード画像 マッピング**
if (!window.uploadedImagesMap) {
    window.uploadedImagesMap = {
        "none": "画像なし",
        "valid": "画像あり"
    };
}*/

// ----------------------------------------------------------------------
// ▼ フォーム送信データをまとめる関数
// ----------------------------------------------------------------------
function getFormData(parent) {
  var isRecruitmentPage = window.location.href.includes('/recruitment');

  // **参照URLの追加：モーダルかどうか、現在のURL**
  var isModal = parent.closest('#store-reservation-modal').length > 0;
  var pageURL = window.location.href;

  // **物件IDまたは採用IDを取得**
  var propertyId = jQuery('#reservation-property-id').text().trim() || '未設定';

  // **記事タイトルを取得**
  var propertyTitle = jQuery('#reservation-property-title').text().trim() || '未設定';

  // **価格（給与）を取得**
  var price = jQuery('#reservation-property-price').text().trim();

  return {
    // ----------------------------
    // ▼ 基本入力フィールド
    // ----------------------------
    last_name: parent.find('#last_name').val(),
    first_name: parent.find('#first_name').val(),
    last_name_kana: parent.find('#last_name_kana').val(),
    first_name_kana: parent.find('#first_name_kana').val(),
    name_slug: parent.find('#name_slug').val(), // 🔽 ローマ字変換された名前（hidden）

    email: parent.find('#email').val(),
    phone: parent.find('#phone').val(),
    postcode: parent.find('#postcode').val(),
    address: parent.find('#address').val(), // 来店・面接予約の住所
    'visit-date': parent.find('#visit-date').val(),
    'time-slot': parent.find('#time-slot').val(),

    // ▼ 送信元の情報（ここが追加！）
    from_modal: isModal ? 'モーダル' : 'ページ',
    page_url: pageURL,
    // ----------------------------
    // ▼ 物件情報 / 採用情報
    // ----------------------------
    property_id: propertyId,
    property_title: propertyTitle,
    property_price: price,

    // ----------------------------
    // ▼ 売却査定フィールド
    // ----------------------------
    property_status: parent.find('#property_status').val() || '未選択',
    sale_price: parent.find('#sale_price').val(),
    sale_price: parent.find('#sale_price').val() || '未設定',
    sale_period: parent.find('#sale_period').val() || '未選択',
    valuation_method: parent.find('#valuation_method').val() || '未選択',
    property_postcode: parent.find('#postcode_2').val() || '',
    property_address: parent.find('#address_2').val() || '', // 売却査定の住所
    sale_reason_text: parent.find('#sale_reason_text').val() || '',

    // ----------------------------
    // ▼ 動的に生成される項目（売却査定フォームの追加項目）
    // ----------------------------
    land_area: parent.find('#land_area').length ? parent.find('#land_area').val() : '未設定',
    building_area: parent.find('#building_area').length
      ? parent.find('#building_area').val()
      : '未設定',
    rental_income: parent.find('#rental_income').length
      ? parent.find('#rental_income').val()
      : '未設定',
    year_built: parent.find('#year_built').length ? parent.find('#year_built').val() : '未設定',
    floor_plan: parent.find('#floor_plan').length ? parent.find('#floor_plan').val() : '未設定',

    // ----------------------------
    // ▼ アップロード画像
    // ----------------------------
    uploaded_images: [...uploadedImages],
  };
}

// ----------------------------------------------------------------------
// ▼ 確認画面にデータを反映する関数
//    （Step 2 などで呼び出される想定）html のIDを参照に取得
// ----------------------------------------------------------------------
function updateConfirmationData(formData) {
  var isRecruitmentPage = window.location.href.includes('/recruitment');

  // H3タイトル切り替え（採用 or 物件）
  jQuery('#confirm-info-title').text(isRecruitmentPage ? '採用情報' : '物件情報');

  // formData から結合して表示
  const fullName = (formData.last_name || '') + ' ' + (formData.first_name || '');
  const fullKana = (formData.last_name_kana || '') + ' ' + (formData.first_name_kana || '');

  // ----------------------------
  // ▼ 基本情報をセット
  // ----------------------------
  jQuery('#confirm-名前').text(fullName); // ← 修正済み
  jQuery('#confirm-カタカナ').text(fullKana); // ← 修正済み
  jQuery('#confirm-メールアドレス').text(formData.email);
  jQuery('#confirm-電話番号').text(formData.phone);
  jQuery('#confirm-郵便番号').text(formData.postcode);
  jQuery('#confirm-住所').text(formData.address);
  jQuery('#confirm-訪問日').text(formData['visit-date']);
  jQuery('#confirm-時間帯').text(formData['time-slot']);

  // 物件ID
  jQuery('#confirm-property-label').text(isRecruitmentPage ? '採用ID: ' : '物件ID: ');
  jQuery('#confirm-物件ID').text(formData.property_id);

  // ----------------------------
  // ▼ タイトルと価格（給与）
  // ----------------------------
  jQuery(
    '#confirm-採用タイトル, #confirm-採用給与, #confirm-物件タイトル, #confirm-物件価格',
  ).hide();

  if (isRecruitmentPage) {
    jQuery('#confirm-採用タイトル-text').text(formData.property_title);
    jQuery('#confirm-採用タイトル').show();
    jQuery('#confirm-採用給与-text').text(
      formData.property_price === '応相談' ? '応相談' : formData.property_price + '円',
    );
    jQuery('#confirm-採用給与').show();
  } else {
    jQuery('#confirm-物件タイトル-text').text(formData.property_title);
    jQuery('#confirm-物件タイトル').show();
    let priceText = formData.property_price;

    if (priceText === '売却済') {
      priceText = '売却済';
    } else if (!priceText.includes('万円')) {
      priceText += '万円';
    }

    jQuery('#confirm-物件価格-text').text(priceText);
    jQuery('#confirm-物件価格').show();
  }

  // ----------------------------
  // ▼ 売却査定情報をセット（🔹 `getMappedValue()` 適用）
  // ----------------------------
  jQuery('#confirm-物件状況').text(getMappedValue(propertyStatusMap, formData['property_status']));
  jQuery('#confirm-査定理由').text(formData['sale_reason_text'] || '');
  jQuery('#confirm-売却価格').text(
    formData['sale_price'] !== '未設定' ? formData['sale_price'] + '万円' : '未設定',
  );
  jQuery('#confirm-売却時期').text(getMappedValue(salePeriodMap, formData['sale_period']));
  jQuery('#confirm-査定方法').text(
    getMappedValue(valuationMethodMap, formData['valuation_method']),
  );
  jQuery('#confirm-査定物件郵便番号').text(formData['property_postcode']);
  jQuery('#confirm-査定物件住所').text(formData['property_address']);

  // ----------------------------
  // ▼ 売却査定情報（動的フィールド）
  // ----------------------------
  const confirmDynamicFieldsContainer = jQuery('#confirm-dynamic-fields-container');
  confirmDynamicFieldsContainer.empty(); // 既存のフィールドをクリア

  const fields = [];
  if (formData.land_area !== '未設定') {
    fields.push(`<p><strong>土地面積:</strong> ${formData.land_area}㎡</p>`);
  }
  if (formData.building_area !== '未設定') {
    fields.push(`<p><strong>建物面積:</strong> ${formData.building_area}㎡</p>`);
  }
  if (formData.year_built !== '未設定') {
    fields.push(`<p><strong>築年数:</strong> ${formData.year_built}年</p>`);
  }
  if (formData.floor_plan !== '未設定') {
    fields.push(
      `<p><strong>間取り:</strong> ${getMappedValue(floorPlanMap, formData.floor_plan)}</p>`,
    );
  }
  if (formData.rental_income !== '未設定') {
    fields.push(`<p><strong>賃料収入:</strong> ${formData.rental_income}万円/月</p>`);
  }

  // 確認画面に挿入
  if (fields.length > 0) {
    confirmDynamicFieldsContainer.append(fields.join('')).show();
  } else {
    confirmDynamicFieldsContainer.hide();
  }

  // ----------------------------
  // ▼ アップロード画像の表示
  // ----------------------------
  displayUploadedImages(formData['uploaded_images'], '#confirm-image-container');
}

// ----------------------------------------------------------------------
// ▼ 別の確認画面表示用（showConfirmationScreen）の例　html のIDを参照に取得
// ----------------------------------------------------------------------
function showConfirmationScreen(formData, selector) {
  const parent = jQuery(selector);
  const confirmSection = parent.find('#step-confirm');
  const inputSection = parent.find('#step-input');
  // formData から結合して表示
  const fullName = (formData.last_name || '') + ' ' + (formData.first_name || '');
  const fullKana = (formData.last_name_kana || '') + ' ' + (formData.first_name_kana || '');

  var isRecruitmentPage = window.location.href.includes('/recruitment');

  // 見出しタイトル
  confirmSection.find('#confirm-info-title').text(isRecruitmentPage ? '採用情報' : '物件情報');

  // ----------------------------
  // ▼ 基本情報をセット
  // ----------------------------
  confirmSection.find('#confirm-名前').text(fullName); // ← 修正
  confirmSection.find('#confirm-カタカナ').text(fullKana); // ← 修正
  confirmSection.find('#confirm-メールアドレス').text(formData.email);
  confirmSection.find('#confirm-電話番号').text(formData.phone);
  confirmSection.find('#confirm-郵便番号').text(formData.postcode);
  confirmSection.find('#confirm-住所').text(formData.address);
  confirmSection.find('#confirm-訪問日').text(formData['visit-date']);
  confirmSection.find('#confirm-時間帯').text(formData['time-slot']);

  // 物件ID
  confirmSection.find('#confirm-物件ID').text(formData.property_id);

  // ----------------------------
  // ▼ 動的項目の表示（例）確認画面に単位を表示
  // ----------------------------
  confirmSection
    .find('#confirm-土地面積')
    .text(formData.land_area ? formData.land_area + '㎡' : '');
  confirmSection
    .find('#confirm-建物面積')
    .text(formData.building_area ? formData.building_area + '㎡' : '');
  confirmSection
    .find('#confirm-賃料収入')
    .text(formData.rental_income ? formData.rental_income + '万円/月' : '');
  confirmSection
    .find('#confirm-築年数')
    .text(formData.year_built ? formData.year_built + '年' : '');
  confirmSection.find('#confirm-間取り').text(formData.floor_plan || '');

  // 画面切り替え
  inputSection.hide();
  confirmSection.show();
}

// 売却査定の選択項目 ページ読み込み後、フォームの項目を動的に切り替える処理
document.addEventListener('DOMContentLoaded', function () {
  const propertyStatus = document.getElementById('property_status'); // 現況の選択欄
  const dynamicFieldsContainer = document.getElementById('dynamic-fields-container'); // 追加フィールド表示エリア
  if (!propertyStatus || !dynamicFieldsContainer) return;

  updateFields(); // ページ読み込み時に一度実行
  if (propertyStatus) if (propertyStatus) propertyStatus.addEventListener('change', updateFields); // 現況が変更されたら実行

  // ▼ 現況に応じてフォーム項目を切り替える関数
  function updateFields() {
    if (!propertyStatus || !dynamicFieldsContainer) return;
    if (dynamicFieldsContainer) dynamicFieldsContainer.innerHTML = ''; // 既存のフィールドをリセット

    let selectedValue = propertyStatus.value;
    let fields = [];

    // ▼ 「更地」「土地付き建物」の場合は土地面積フィールドを表示
    if (['vacant_land', 'land_with_building'].includes(selectedValue)) {
      fields.push(createFormRow('land_area', '土地面積', 'number', '150'));
    }

    // ▼ 「戸建」「マンション」「商業施設」「土地付き建物」の場合は建物関係の入力欄を表示
    if (['detached_house', 'condominium', 'rented', 'land_with_building'].includes(selectedValue)) {
      fields.push(createFormRow('building_area', '建物面積', 'number', '120'));
      fields.push(createFormRow('year_built', '築年数', 'number', '30'));
      fields.push(
        createSelectRow('floor_plan', '間取り', {
          '1K': '1K',
          '1LDK': '1LDK',
          '2LDK': '2LDK',
          '3LDK': '3LDK',
          '4LDK以上': '4LDK以上',
        }),
      );
    }

    // ▼ 「商業施設」の場合は賃料収入欄を表示
    if (selectedValue === 'rented') {
      fields.push(createFormRow('rental_income', '賃料収入', 'number', '50'));
    }

    // 🔸 2カラム対応：2つごとに1行としてまとめて表示する
    let rowGroup = document.createElement('div');
    rowGroup.className = 'form-row-group';

    fields.forEach((field, index) => {
      rowGroup.appendChild(field);
      if ((index + 1) % 2 === 0 || index === fields.length - 1) {
        dynamicFieldsContainer.appendChild(rowGroup);
        rowGroup = document.createElement('div');
        rowGroup.className = 'form-row-group';
      }
    });
  }

  // ▼ 単一入力フィールドを生成する関数（例: 土地面積など）
  // 🔸 ラベルに単位（㎡など）は書かない。出力時に追加する。
  function createFormRow(id, label, type, placeholder) {
    let div = document.createElement('div');
    div.className = 'form-row';

    div.innerHTML = `
            <label for="${id}">${label}<span class="required">＊必須</span>:</label>
            <input type="${type}" id="${id}" name="${id}" class="input-field" placeholder="例: ${placeholder}" required>
        `;
    return div;
  }
  // ▼ セレクトボックス（例: 間取り）を生成する関数
  function createSelectRow(id, label, options) {
    let div = document.createElement('div');
    div.className = 'form-row';

    let selectHTML = `<label for="${id}">${label}<span class="required">＊必須</span>:</label>
                          <select id="${id}" name="${id}" class="input-field" required>
                              <option value="">選択してください</option>`;

    for (let key in options) {
      selectHTML += `<option value="${key}">${options[key]}</option>`;
    }

    selectHTML += `</select>`;
    div.innerHTML = selectHTML;
    return div;
  }
});

// 📌 DOMが読み込まれたらスクリプトを実行　パノラマビュー設定
/*document.addEventListener("DOMContentLoaded", function () {
    // **🔹 各要素を取得**
    const panoramaContainer = document.getElementById("panorama-1"); // パノラマ画像を表示するコンテナ
    const panoramaTitle = document.querySelector(".panorama-title"); // パノラマ画像のタイトル
    const panoramaText = document.querySelector(".panorama-text"); // パノラマ画像の説明テキスト
    const panoramaIndex = document.getElementById("panorama-index"); // 現在の画像インデックス
    const subImages = document.querySelectorAll(".sub-image-item"); // サムネイル画像のリスト

    let viewer; // Pannellum のビューアを格納する変数

    // **📌 Pannellum の初期化関数**
    function initPannellum(imageUrl) {
        if (!imageUrl) {
            console.error("❌ パノラマ画像のURLがありません");
            return;
        }
        if (viewer) {
            viewer.destroy(); // 既存のビューアがある場合は破棄
        }
        // Pannellum ビューアを作成
        viewer = pannellum.viewer("panorama-1", {
            type: "equirectangular", // 360°パノラマ画像を設定
            panorama: imageUrl, // 表示するパノラマ画像のURL
            autoLoad: true, // ページ読み込み時に自動ロード
            showControls: true, // コントロールボタンを表示
            compass: true, // 方位コンパスを表示
            autoRotate: 0 // 自動回転はオフ
        });
    }

    // **📌 初回ロード時に Pannellum を初期化**
    if (panoramaContainer) {
        const initialPanoramaUrl = panoramaContainer.dataset.panoramaUrl; // 初期パノラマ画像のURLを取得
        if (initialPanoramaUrl) {
            initPannellum(initialPanoramaUrl); // 初期パノラマ画像を表示
        } else {
            console.error("❌ 初期パノラマ画像が設定されていません");
        }
    }

    // **📌 サムネイルをクリックしたときにパノラマ画像を切り替える関数**
    function changePanoramaImageWithIndex(element) {
        const newUrl = element.dataset.url; // 選択されたサムネイルの画像URL
        const newTitle = element.dataset.title; // タイトル
        const newText = element.dataset.text; // 説明テキスト
        const newIndex = Array.from(subImages).indexOf(element) + 1; // 何番目の画像か取得（1から始まる）

        if (!newUrl) {
            console.error("❌ 変更するパノラマ画像のURLがありません");
            return;
        }

        initPannellum(newUrl); // Pannellum で新しい画像を読み込む
        panoramaTitle.textContent = newTitle || ""; // タイトルを更新
        panoramaText.textContent = newText || ""; // テキストを更新
        panoramaIndex.textContent = `${newIndex} / ${subImages.length}`; // 画像インデックスを更新

        // **🔹 すべてのサムネイルから "active" クラスを削除し、クリックしたサムネイルに追加**
        subImages.forEach(item => item.classList.remove("active"));
        element.classList.add("active");
    }

    // **📌 サブ画像（サムネイル）をクリックするとパノラマ画像を切り替え**
    subImages.forEach((thumb) => {
        thumb.addEventListener("click", function () {
            changePanoramaImageWithIndex(this);
        });
    });

    // **📌 タブの切り替え（画像ビュー ↔ パノラマビュー）**
    const tabLinks = document.querySelectorAll(".tab-link"); // タブのリンクを取得
    tabLinks.forEach(tabLink => {
        tabLink.addEventListener("click", function () {
            // **🔹 すべてのタブコンテンツを非表示**
            const allTabs = document.querySelectorAll(".tab-pane");
            allTabs.forEach(tab => tab.classList.remove("active"));

            // **🔹 クリックされたタブに対応するコンテンツを表示**
            const targetTabId = tabLink.getAttribute("data-tab"); // タブのIDを取得
            const targetTab = document.getElementById(targetTabId);
            if (targetTab) {
                targetTab.classList.add("active");
            }

            // **🔹 すべてのタブリンクの "active" を解除し、クリックしたタブを "active" に**
            tabLinks.forEach(link => link.classList.remove("active"));
            tabLink.classList.add("active");

            // **🔹 Pannellum ビューを更新（パノラマタブが開かれた場合のみ）**
            if (targetTabId === "panorama-tab" && viewer) {
                viewer.resize(); // Pannellum の表示を正しく更新
            }
        });
    });
});*/

/*document.addEventListener("DOMContentLoaded", function () {
    console.log("✅ タブ切り替えスクリプト開始");

    const tabLinks = document.querySelectorAll(".tab-link");

    tabLinks.forEach(tabLink => {
        tabLink.addEventListener("click", function () {
            const targetTabId = tabLink.getAttribute("data-tab");
            const targetTab = document.getElementById(targetTabId);

            if (targetTab) {
                document.querySelectorAll(".tab-pane").forEach(tab => tab.classList.remove("active"));
                targetTab.classList.add("active");

                document.querySelectorAll(".tab-link").forEach(link => link.classList.remove("active"));
                tabLink.classList.add("active");

                if (targetTabId === "panorama-tab" && window.viewer) {
                    window.viewer.resize();
                }
            }
        });
    });
});



// 予約フォームとレビューコメントの切り替え
document.addEventListener('DOMContentLoaded', function() {
    // タブをクリックしたときに切り替えを行う処理
    const tabLinks = document.querySelectorAll('.custom-tab-link');
    const tabPanes = document.querySelectorAll('.custom-tab-pane');
    const commentLink = document.querySelector('.sidebar-comment-num');  // コメント数リンクの取得

    tabLinks.forEach(tab => {
        tab.addEventListener('click', function() {
            // すべてのタブから "active" を削除
            tabLinks.forEach(link => link.classList.remove('active'));
            // すべてのタブの内容を非表示
            tabPanes.forEach(pane => pane.style.display = 'none');
            
            // クリックされたタブに "active" を追加
            this.classList.add('active');
            // 対応するタブコンテンツを表示
            const tabId = this.getAttribute('data-tab');
            const activeTabPane = document.getElementById('tab-' + tabId);
            activeTabPane.style.display = 'block';
        });
    });

    // 初期状態で "予約フォーム" タブが表示されるように設定
    document.querySelector('.custom-tab-link.active').click();

    // コメント数リンクをクリックしたときに "口コミレビュー" タブを表示
    if (commentLink) {
        commentLink.addEventListener('click', function() {
            // "口コミレビュー" タブをクリック
            const commentsTabLink = document.querySelector('.custom-tab-link[data-tab="comments"]');
            if (commentsTabLink) {
                commentsTabLink.click();  // クリックイベントを発生させる
            }
        });
    }
});*/

// 検索フィルター　来店予約
jQuery(document).ready(function ($) {
  console.log('✅ スクリプト開始');

  let categorySelect = $('#property_category');
  let houseTypeSelect = $('#house-type');
  let regionSelect = $('#region');
  let priceSelect = $('#selected_price');
  let searchButton = $('#search-button');

  searchButton.prop('disabled', false);

  categorySelect.change(function () {
    let category = categorySelect.val();
    if (!category) return;

    console.log('カテゴリ変更:', category);

    $.ajax({
      url: customAjax.ajaxurl,
      type: 'POST',
      dataType: 'json',
      data: { action: 'fetch_category_filters', category: category },
      success: function (response) {
        console.log('✅ フィルター取得成功:', response);

        houseTypeSelect
          .empty()
          .append('<option value="">間取りを選択</option>')
          .prop('disabled', true);
        if (response.success && response.data.house_types.length > 0) {
          $.each(response.data.house_types, function (index, term) {
            houseTypeSelect.append('<option value="' + term.slug + '">' + term.name + '</option>');
          });
          houseTypeSelect.prop('disabled', false);
        }

        regionSelect.empty().append('<option value="">地域を選択</option>').prop('disabled', false);
        if (response.success && response.data.regions.length > 0) {
          $.each(response.data.regions, function (index, term) {
            regionSelect.append('<option value="' + term.slug + '">' + term.name + '</option>');
          });
        }

        updatePriceFilters(response.data.prices);
      },
    });
  });

  houseTypeSelect.change(function () {
    const category = categorySelect.val();
    const houseType = houseTypeSelect.val();

    if (category !== 'house' && category !== 'naigai-construction') return;
    if (!houseType) return;

    console.log('🏠 間取り選択 → 地域を絞り込み:', houseType);

    $.ajax({
      url: customAjax.ajaxurl,
      type: 'POST',
      dataType: 'json',
      data: {
        action: 'fetch_regions_by_house_type',
        category: category,
        house_type: houseType,
      },
      success: function (response) {
        regionSelect.empty().append('<option value="">地域を選択</option>').prop('disabled', false);
        if (response.success && response.data.length > 0) {
          $.each(response.data, function (index, term) {
            regionSelect.append('<option value="' + term.slug + '">' + term.name + '</option>');
          });
        } else {
          regionSelect.prop('disabled', true);
        }
      },
      error: function (xhr) {
        console.error('❌ 地域絞り込み失敗:', xhr.responseText);
      },
    });
  });

  regionSelect.change(function () {
    const category = categorySelect.val();
    const region = regionSelect.val();

    if (!region) return;

    if (category === 'naigai-tochi') {
      console.log('🌲 地域選択 → 土地カテゴリの価格再取得:', region);
    } else {
      console.log('🏠 地域選択 → 建物カテゴリの価格再取得:', region);
    }

    const data = {
      action: 'fetch_price_by_filters',
      category: category,
      region: region,
    };

    if (category === 'house' || category === 'naigai-construction') {
      data.house_type = houseTypeSelect.val();
    }

    $.ajax({
      url: customAjax.ajaxurl,
      type: 'POST',
      dataType: 'json',
      data: data,
      success: function (response) {
        if (response.success) {
          updatePriceFilters(response.data.prices);
        } else {
          priceSelect
            .empty()
            .append('<option value="">価格がありません</option>')
            .prop('disabled', true);
        }
      },
      error: function (xhr) {
        console.error('❌ 価格取得エラー:', xhr.responseText);
      },
    });
  });

  function updatePriceFilters(prices) {
    console.log('✅ 価格フィルター更新:', prices);

    priceSelect.empty().append('<option value="">価格を選択</option>').prop('disabled', false);
    if (prices.length > 0) {
      $.each(prices, function (index, price) {
        let priceLabel = parseInt(price.slug.split('-')[0]) === 0 ? '売却済' : price.name;
        priceSelect.append('<option value="' + price.slug + '">' + priceLabel + '</option>');
      });
    } else {
      priceSelect
        .empty()
        .append('<option value="">価格がありません</option>')
        .prop('disabled', true);
    }
  }

  searchButton.click(function () {
    let selectedPrice = priceSelect.val();
    if (!selectedPrice) {
      alert('価格を選択してください');
      return;
    }

    let priceParts = selectedPrice.split('-');
    let postId = priceParts[1];

    console.log('🔎 検索実行: postId=', postId);

    $.ajax({
      url: customAjax.ajaxurl,
      type: 'POST',
      dataType: 'json',
      data: { action: 'fetch_search_results', post_id: postId },
      success: function (response) {
        console.log('✅ 検索結果取得成功:', response);

        if (response.success && response.data) {
          $('#reservation-property-title').text(response.data.title);
          $('#reservation-property-id').text(response.data.post_id);
          $('#reservation-property-price').text(response.data.price);
          $('#reservation-property-thumbnail').css(
            'background-image',
            'url(' + response.data.image + ')',
          );
        } else {
          alert('該当する物件が見つかりません');
          console.warn('⚠️ 検索結果なし:', response);
        }
      },
      error: function (xhr) {
        console.error('❌ 検索失敗:', xhr.responseText);
      },
    });
  });
});

// メガメニューのサイズ
document.addEventListener('DOMContentLoaded', function () {
  var megaMenus = document.querySelectorAll('.mega-sub-menu'); // すべての .mega-sub-menu を取得

  megaMenus.forEach(function (megaMenu, index) {
    // transform の調整（-10% ずつズラす）
    var offset = -40 + index * -10; // 初期値 -40% から、各メニューごとに -10% ずつズラす
    megaMenu.style.transform = `translateX(${offset}%)`; // CSS の transform プロパティを適用
  });
});

// 画像のurをユーザーの送信した名前を参照にカタカナ入力をローマ字（姓_名形式）に変換するスクリプト
jQuery(document).ready(function ($) {
  function capitalize(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
  }

  function kanaToRomajiFormatted(kana) {
    const map = {
      ア: 'a',
      イ: 'i',
      ウ: 'u',
      エ: 'e',
      オ: 'o',
      カ: 'ka',
      キ: 'ki',
      ク: 'ku',
      ケ: 'ke',
      コ: 'ko',
      サ: 'sa',
      シ: 'shi',
      ス: 'su',
      セ: 'se',
      ソ: 'so',
      タ: 'ta',
      チ: 'chi',
      ツ: 'tsu',
      テ: 'te',
      ト: 'to',
      ナ: 'na',
      ニ: 'ni',
      ヌ: 'nu',
      ネ: 'ne',
      ノ: 'no',
      ハ: 'ha',
      ヒ: 'hi',
      フ: 'fu',
      ヘ: 'he',
      ホ: 'ho',
      マ: 'ma',
      ミ: 'mi',
      ム: 'mu',
      メ: 'me',
      モ: 'mo',
      ヤ: 'ya',
      ユ: 'yu',
      ヨ: 'yo',
      ラ: 'ra',
      リ: 'ri',
      ル: 'ru',
      レ: 're',
      ロ: 'ro',
      ワ: 'wa',
      ヲ: 'wo',
      ン: 'n',
      ガ: 'ga',
      ギ: 'gi',
      グ: 'gu',
      ゲ: 'ge',
      ゴ: 'go',
      ザ: 'za',
      ジ: 'ji',
      ズ: 'zu',
      ゼ: 'ze',
      ゾ: 'zo',
      ダ: 'da',
      ヂ: 'ji',
      ヅ: 'zu',
      デ: 'de',
      ド: 'do',
      バ: 'ba',
      ビ: 'bi',
      ブ: 'bu',
      ベ: 'be',
      ボ: 'bo',
      パ: 'pa',
      ピ: 'pi',
      プ: 'pu',
      ペ: 'pe',
      ポ: 'po',
      キャ: 'kya',
      キュ: 'kyu',
      キョ: 'kyo',
      シャ: 'sha',
      シュ: 'shu',
      ショ: 'sho',
      チャ: 'cha',
      チュ: 'chu',
      チョ: 'cho',
      ニャ: 'nya',
      ニュ: 'nyu',
      ニョ: 'nyo',
      ヒャ: 'hya',
      ヒュ: 'hyu',
      ヒョ: 'hyo',
      ミャ: 'mya',
      ミュ: 'myu',
      ミョ: 'myo',
      リャ: 'rya',
      リュ: 'ryu',
      リョ: 'ryo',
      ギャ: 'gya',
      ギュ: 'gyu',
      ギョ: 'gyo',
      ジャ: 'ja',
      ジュ: 'ju',
      ジョ: 'jo',
      ビャ: 'bya',
      ビュ: 'byu',
      ビョ: 'byo',
      ピャ: 'pya',
      ピュ: 'pyu',
      ピョ: 'pyo',
      ー: '-',
      '　': ' ',
      ' ': ' ',
    };

    const combos = Object.keys(map).filter((k) => k.length === 2);
    for (let i = 0; i < combos.length; i++) {
      kana = kana.replace(new RegExp(combos[i], 'g'), map[combos[i]]);
    }

    kana = kana.replace(/./g, (char) => map[char] || '');
    const parts = kana
      .trim()
      .split(/\s+/)
      .map((part) => capitalize(part));
    return parts.join('_').replace(/[^a-zA-Z0-9_]/g, '');
  }

  // 🔹 カタカナ入力が変わったときにローマ字に変換して hidden にセット
  function updateNameSlug() {
    const kanaFull = ($('#last_name_kana').val() + ' ' + $('#first_name_kana').val()).trim();
    const romaji = kanaToRomajiFormatted(kanaFull);
    $('#name_slug').val(romaji);
    console.log('🎯 ローマ字変換:', romaji);
  }

  $('#last_name_kana, #first_name_kana').on('input', updateNameSlug);
});

jQuery(document).ready(function ($) {
  var modalOpen = false;
  var modalClosed = false;

  function openModal() {
    if (modalOpen || modalClosed) return;
    $('#chatgpt-modal').fadeIn(1000);
    modalOpen = true;

    var welcomeMessage = `
      <div class="gpt-response">
        こんにちは。内外土地開発（株）です。那須の不動産の物件でしたらご案内ができます。以下のリンクをご参照ください。
        <br>土地の案内ページ: <a href="https://naigaicorp.net/naigai-tochi" target="_blank">リンク</a>
        <br>建物の案内ページ: <a href="https://naigaicorp.net/naigai-construction" target="_blank">リンク</a>
        <br>ほかに何かわからないことがございましたら何でも聞いてくださいね！よろしくお願い申し上げます。
      </div>
    `;

    $('#chatgpt-messages').html(welcomeMessage);
  }

  function closeModal() {
    $('#chatgpt-modal').fadeOut(1000);
    modalOpen = false;
    modalClosed = true;
  }

  $('.close-btn').click(function () {
    closeModal();
  });

  $('#send_message').click(function () {
    var userMessage = $('#user_message').val();
    if (userMessage) {
      $.ajax({
        url: customAjax.ajaxurl,
        type: 'POST',
        data: {
          action: 'chatgpt_request',
          security: customAjax.nonce,
          user_message: userMessage,
        },
        success: function (response) {
          if (response.success) {
            $('#chatgpt-messages').append('<div class="user-message">' + userMessage + '</div>');
            $('#chatgpt-messages').append(
              '<div class="gpt-response">' + response.data.message + '</div>',
            );
          } else {
            $('#chatgpt-messages').append(
              '<div class="gpt-response">エラー: ' + response.data.message + '</div>',
            );
          }
          $('#user_message').val('');
        },
        error: function () {
          $('#chatgpt-messages').append('<div class="gpt-response">エラーが発生しました。</div>');
        },
      });
    }
  });

  $('#user_message').on('keypress', function (event) {
    if (event.keyCode === 13) {
      event.preventDefault();
      $('#send_message').click();
    }
  });

  $(window).scroll(function () {
    if ($(window).scrollTop() > 100 && !modalOpen && !modalClosed) {
      openModal();
    }
  });

  $('#chatgpt-modal').click(function (event) {
    if ($(event.target).is('#chatgpt-modal')) {
      closeModal();
    }
  });
});


/* =========================================================
   SWIPER RESERVATION CTA OVERRIDE
   - Swiper の「来店予約」は予約モーダルを直接開く
   - ChatGPT モーダルと干渉しない
   ========================================================= */
(function ($) {
  'use strict';

  function ensureReservationModalOpen(postId, permalink) {
    var $modal = $('#store-reservation-modal');
    if (!$modal.length) return;

    $modal
      .attr('data-post-id', postId || '')
      .attr('data-permalink', permalink || '');

    $modal.find('input[name="post_id"], input[name="property_id"], input[name="postId"]').first().val(postId || '');
    $modal.find('input[name="permalink"], input[name="property_permalink"]').first().val(permalink || '');

    $('body').addClass('store-reservation-modal-open');
    $modal.stop(true, true).fadeIn(200).addClass('active');

    $(document).trigger('storeReservationModal:open');
  }

  function openSwiperReservation(btn) {
    var postId = btn.getAttribute('data-post-id') || '';
    var permalink = btn.getAttribute('data-permalink') || '';

    $(document).trigger('storeReservationModal:open');

    if (typeof window.openReserveByPostId === 'function') {
      try {
        window.openReserveByPostId(postId, permalink);
      } catch (err) {
        console.error('openReserveByPostId failed:', err);
      }
    }

    setTimeout(function () {
      var $modal = $('#store-reservation-modal');
      if (!$modal.length) return;

      if (!$modal.is(':visible') && !$modal.hasClass('active')) {
        ensureReservationModalOpen(postId, permalink);
      }
    }, 30);
  }

  document.addEventListener('click', function (e) {
    var btn = e.target.closest('.home-slide-cta.naigai-property-row__cta');
    if (!btn) return;

    e.preventDefault();
    e.stopPropagation();
    if (typeof e.stopImmediatePropagation === 'function') {
      e.stopImmediatePropagation();
    }

    openSwiperReservation(btn);
  }, true);

  $(document).on('click', '#store-reservation-modal .close-btn, #store-reservation-modal [data-modal-close="1"]', function (e) {
    e.preventDefault();
    var $modal = $('#store-reservation-modal');
    $modal.stop(true, true).fadeOut(200, function () {
      $modal.removeClass('active');
      $('body').removeClass('store-reservation-modal-open');
      $(document).trigger('storeReservationModal:close');
    });
  });

  $(document).on('click', '#store-reservation-modal', function (e) {
    if ($(e.target).is('#store-reservation-modal')) {
      var $modal = $('#store-reservation-modal');
      $modal.stop(true, true).fadeOut(200, function () {
        $modal.removeClass('active');
        $('body').removeClass('store-reservation-modal-open');
        $(document).trigger('storeReservationModal:close');
      });
    }
  });

  document.addEventListener('keydown', function (e) {
    if (e.key !== 'Escape') return;

    var $modal = $('#store-reservation-modal');
    if (!$modal.length) return;
    if (!$modal.is(':visible') && !$modal.hasClass('active')) return;

    $modal.stop(true, true).fadeOut(200, function () {
      $modal.removeClass('active');
      $('body').removeClass('store-reservation-modal-open');
      $(document).trigger('storeReservationModal:close');
    });
  });
})(jQuery);

