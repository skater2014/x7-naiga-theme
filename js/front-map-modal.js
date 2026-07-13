/**
 * =========================================================
 * Google Map 共通モーダル
 * =========================================================
 *
 * このJSの役割:
 * - Google Mapの「取得」やメタキーの保存はしない。
 * - PHP側で取得済みの地図HTMLを data-map-html から受け取る。
 * - data-map-html は base64 で渡されるため、JSで復号して iframe を表示する。
 * - モーダルHTMLはページごとに作らず、このJSが body 内へ1個だけ生成する。
 *
 * 地図データの取得方法:
 * - 通常の不動産詳細:
 *   functions.php 側で GoogleEmbedcode 等のメタキーから取得する。
 *
 * - /fudousan:
 *   投稿IDを入口に、既存の不動産用helperで地図HTMLを取得する。
 *
 * 重要:
 * - 取得方法はページごとに異なってよい。
 * - 共通化するのは「開く・地図を入れる・閉じる」という表示処理だけ。
 *
 * /fudousan 以外の不動産詳細ページで動く仕組み:
 *
 * 1. functions.php の詳細表示処理が GoogleEmbedcode 等を取得する。
 *
 * 2. 取得したURLまたは iframe HTMLから、表示用の iframe HTMLを作る。
 *
 * 3. iframe HTMLを base64_encode() して、
 *    クリック要素の data-map-html 属性へ入れる。
 *
 *    例:
 *
 *    <span
 *      class="google-location-trigger"
 *      data-map-open
 *      data-map-html="base64化されたiframe HTML"
 *    >
 *      Google位置
 *    </span>
 *
 * 4. このJSはページ名や投稿タイプを判定しない。
 *    document 全体のクリックを監視し、
 *    .google-location-trigger または [data-map-open] が
 *    クリックされた時だけ処理を開始する。
 *
 * 5. クリックされた要素自身の data-map-html を取得し、
 *    decodeBase64() で元の iframe HTMLへ戻す。
 *
 * 6. ensureModal() が共通モーダルを1個だけ用意し、
 *    .js-google-map-modal-body に iframe HTMLを挿入して表示する。
 *
 * そのため:
 * - /fudousan は投稿IDを入口に地図HTMLを取得してよい。
 * - 通常の詳細ページは GoogleEmbedcode 等から取得してよい。
 * - 地図の取得方法が違っても、最終的に data-map-html を渡せば
 *   同じ front-map-modal.js で表示できる。
 *
 * クリック対象:
 * - .google-location-trigger
 * - [data-map-open]
 *
 * 注意:
 * data-map-html は上記クリック対象の要素自身に付ける。
 * .icon-location など子要素を直接クリック対象にすると、
 * 子要素には data-map-html がないため空のモーダルになる。
 *
 * CSS:
 * - Google Mapモーダルの共通スタイルは style.css で管理する。
 * - fudousan.css にはGoogle Map専用スタイルを重複して書かない。
 *
 * scripts.js:
 * - Google Mapモーダルの開閉処理は置かない。
 * - Google Mapの表示処理はこのファイルへ集約する。
 * =========================================================
 */

document.addEventListener('DOMContentLoaded', function () {
  'use strict';

  function ensureModal() {
    var modal = document.getElementById('googleMapModal');

    if (modal) return modal;

    modal = document.createElement('div');
    modal.id = 'googleMapModal';
    modal.className = 'google-map-modal';
    modal.setAttribute('data-map-modal', '');
    modal.setAttribute('aria-hidden', 'true');
    modal.style.display = 'none';

    modal.innerHTML =
      '<div class="google-map-modal-content">' +
        '<button type="button" class="google-map-modal-close" id="closeModal" data-map-close aria-label="閉じる">&times;</button>' +
'<div class="google-map-modal-body js-google-map-modal-body"></div>' +
      '</div>';

    document.body.appendChild(modal);
    return modal;
  }

  function openModal(modal) {
    if (!modal) return;
    modal.style.display = 'block';
    modal.setAttribute('aria-hidden', 'false');
    document.body.classList.add('is-map-modal-open');
  }

  function closeModal(modal) {
    if (!modal) return;
    modal.style.display = 'none';
    modal.setAttribute('aria-hidden', 'true');
    document.body.classList.remove('is-map-modal-open');
  }

  function decodeBase64(value) {
    try {
      return atob(value);
    } catch (e) {
      console.error('data-map-html の decode に失敗:', e);
      return '';
    }
  }

  function resolveModalFromTrigger(trigger) {
    if (!trigger) return null;

    var target = trigger.getAttribute('data-map-target');
    if (target) {
      return document.querySelector(target);
    }

    return (
      document.getElementById('googleMapModal') ||
      document.querySelector('[data-map-modal]') ||
      document.querySelector('.google-map-modal')
    );
  }

  document.addEventListener('click', function (event) {
    var trigger = event.target.closest(
      '.google-location-trigger, [data-map-open]'
    );
    if (!trigger) return;

    event.preventDefault();

    var modal = resolveModalFromTrigger(trigger) || ensureModal();
    if (!modal) return;

    var encodedHtml = trigger.getAttribute('data-map-html');


    if (encodedHtml) {
      var body = modal.querySelector('.js-google-map-modal-body');


      var decoded = decodeBase64(encodedHtml);

      if (body) body.innerHTML = decoded;


    }

    openModal(modal);
  });

  document.addEventListener('click', function (event) {
    var closeBtn = event.target.closest(
      '#closeModal, .google-map-modal-close, [data-map-close]'
    );
    if (!closeBtn) return;

    event.preventDefault();

    var modal =
      closeBtn.closest('[data-map-modal]') ||
      closeBtn.closest('.google-map-modal') ||
      document.getElementById('googleMapModal');

    closeModal(modal);
  });

  document.addEventListener('click', function (event) {
    var modal =
      event.target.matches('[data-map-modal], .google-map-modal')
        ? event.target
        : null;

    if (modal && event.target === modal) {
      closeModal(modal);
    }
  });

  document.addEventListener('keydown', function (event) {
    if (event.key !== 'Escape') return;

    document
      .querySelectorAll('[data-map-modal], .google-map-modal')
      .forEach(function (modal) {
        closeModal(modal);
      });
  });
});
