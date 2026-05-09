/* =========================================================
 * /iezukuri/ 3カード → iez_plan 投稿を動的表示
 *
 * - PHPが window.NAIGAI_IEZ_PLAN_HOME にデータを出す
 * - show_in_home + show_in_cards の投稿だけ表示
 * - 1件なら通常表示
 * - 複数件なら Swiper があればSwiper、なければ横スクロール
 * ========================================================= */
(function () {
  'use strict';

  function esc(value) {
    return String(value || '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  function arr(value) {
    return Array.isArray(value) ? value : [];
  }

  function thumb(item) {
    var kind = item.kind === 'plan' ? 'plan' : 'work';

    return [
      '<a class="iez-switch-thumb is-' + kind + '" href="' + esc(item.href || item.src) + '" target="_blank" rel="noopener">',
        '<span class="iez-switch-thumb__media">',
          '<img src="' + esc(item.src) + '" alt="' + esc(item.alt || item.title) + '">',
        '</span>',
        '<span class="iez-switch-thumb__title">' + esc(item.title) + '</span>',
      '</a>'
    ].join('');
  }

  function flow() {
    return ['01', '02', '03', '04', '05', '06'].map(function (n) {
      return '<li><span>' + n + '</span></li>';
    }).join('');
  }

  function spec(plan) {
    var specs = arr(plan.specs);

    if (!specs.length) return '';

    return '<p class="iez-switch-plan-specs">' + specs.map(esc).join(' / ') + '</p>';
  }

  function table(plan) {
    var works = arr(plan.works);
    var plans = arr(plan.plans);

    return [
      '<div class="iez-switch-table" data-iez-switch-table data-plan-id="' + esc(plan.id) + '">',

        '<div class="iez-switch-row is-reading">',
          '<div class="iez-switch-row__label">',
            '<strong>家づくりの考え方</strong>',
            '<small>住まい別に整理</small>',
          '</div>',
          '<div class="iez-switch-row__body">',
            '<div class="iez-switch-reading">',
              '<div>',
                '<h3>' + esc(plan.reading_title || plan.title) + '</h3>',
                spec(plan),
                '<p>' + esc(plan.reading_text) + '</p>',
              '</div>',
            '</div>',
          '</div>',
        '</div>',

        '<div class="iez-switch-row">',
          '<div class="iez-switch-row__label">',
            '<strong>施工実例</strong>',
            '<small>Works</small>',
          '</div>',
          '<div class="iez-switch-row__body">',
            works.length
              ? '<div class="iez-switch-strip is-works">' + works.map(thumb).join('') + '</div>'
              : '<p class="iez-switch-empty">施工実例画像は未設定です。</p>',
          '</div>',
        '</div>',

        '<div class="iez-switch-row">',
          '<div class="iez-switch-row__label">',
            '<strong>間取りとプラン</strong>',
            '<small>Plans</small>',
          '</div>',
          '<div class="iez-switch-row__body">',
            plans.length
              ? '<div class="iez-switch-strip is-plans">' + plans.map(thumb).join('') + '</div>'
              : '<p class="iez-switch-empty">平面図・配置図は未設定です。</p>',
          '</div>',
        '</div>',

        '<div class="iez-switch-row is-flow">',
          '<div class="iez-switch-row__label">',
            '<strong>家づくりの流れ</strong>',
            '<small>Flow</small>',
          '</div>',
          '<div class="iez-switch-row__body">',
            '<ol class="iez-switch-flow">' + flow() + '</ol>',
          '</div>',
        '</div>',

        '<div class="iez-switch-row is-cta">',
          '<div class="iez-switch-row__label">',
            '<strong>CTA</strong>',
            '<small>相談・資料請求</small>',
          '</div>',
          '<div class="iez-switch-row__body">',
            '<div class="iez-switch-cta">',
              '<a class="iez-switch-btn is-primary" href="/iezukuri/contact/">無料相談・資料請求</a>',
              '<a class="iez-switch-btn is-secondary" href="' + esc(plan.url) + '">詳細ページを見る</a>',
            '</div>',
          '</div>',
        '</div>',

      '</div>'
    ].join('');
  }

  function render(panel, termData) {
    var plans = arr(termData && termData.plans);

    if (!plans.length) {
      panel.innerHTML = [
        '<div class="iez-switch-table is-empty">',
          '<div class="iez-switch-row is-reading">',
            '<div class="iez-switch-row__label"><strong>Plans</strong><small>未設定</small></div>',
            '<div class="iez-switch-row__body">',
              '<p class="iez-switch-empty">この分類に表示対象のプランがありません。詳細ページ編集で show in home / show in cards をONにしてください。</p>',
            '</div>',
          '</div>',
        '</div>'
      ].join('');
      return;
    }

    if (plans.length === 1) {
      panel.innerHTML = table(plans[0]);
      return;
    }

    panel.innerHTML = [
      '<div class="iez-switch-multi-head">',
        '<strong>' + esc(termData.label || 'Plans') + '</strong>',
        '<span>' + plans.length + '件のプラン</span>',
      '</div>',
      '<div class="iez-switch-swiper swiper js-iez-switch-swiper">',
        '<div class="swiper-wrapper">',
          plans.map(function (plan) {
            return '<div class="swiper-slide">' + table(plan) + '</div>';
          }).join(''),
        '</div>',
        '<div class="iez-switch-swiper__nav">',
          '<button class="iez-switch-swiper-prev" type="button">←</button>',
          '<div class="iez-switch-swiper-pagination"></div>',
          '<button class="iez-switch-swiper-next" type="button">→</button>',
        '</div>',
      '</div>'
    ].join('');

    var root = panel.querySelector('.js-iez-switch-swiper');

    if (root && window.Swiper) {
      new window.Swiper(root, {
        slidesPerView: 1,
        spaceBetween: 18,
        autoHeight: true,
        pagination: {
          el: root.querySelector('.iez-switch-swiper-pagination'),
          clickable: true
        },
        navigation: {
          prevEl: root.querySelector('.iez-switch-swiper-prev'),
          nextEl: root.querySelector('.iez-switch-swiper-next')
        }
      });
    }
  }

  function scrollToPanel(panel) {
    var header = document.querySelector('.ch-site-header');
    var headerH = header ? header.offsetHeight : 0;
    var top = panel.getBoundingClientRect().top + window.pageYOffset - headerH - 22;

    window.scrollTo({
      top: Math.max(0, top),
      behavior: 'smooth'
    });
  }

  document.addEventListener('DOMContentLoaded', function () {
    var data = window.NAIGAI_IEZ_PLAN_HOME || {};
    var grid = document.querySelector('[data-iez-plan-tabs]');
    if (!grid) return;

    var cards = Array.prototype.slice.call(grid.querySelectorAll('[data-iez-plan-card]'));
    if (!cards.length) return;

    var panel = document.querySelector('[data-iez-plan-detail-output]');
    if (!panel) {
      panel = document.createElement('div');
      panel.setAttribute('data-iez-plan-detail-output', '');
      grid.insertAdjacentElement('afterend', panel);
    }

    panel.className = 'iez-switch-panel';

    function activate(card, shouldScroll) {
      var term = card.getAttribute('data-iez-plan-term') || 'one-family';
      var termData = data.terms && data.terms[term];

      cards.forEach(function (item) {
        item.classList.toggle('is-active', item === card);
      });

      render(panel, termData);

      if (shouldScroll) {
        window.setTimeout(function () {
          scrollToPanel(panel);
        }, 60);
      }
    }

    cards.forEach(function (card) {
      card.addEventListener('click', function (event) {
        event.preventDefault();
        event.stopPropagation();
        activate(card, true);
      });
    });

    activate(cards.find(function (card) {
      return card.classList.contains('is-active');
    }) || cards[0], false);
  });
})();
