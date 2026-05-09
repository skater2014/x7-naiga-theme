/**
 * =========================================================
 * iezukuri subpage / concept motion script
 * =========================================================
 *
 * このファイルの役割:
 * 1. 家づくりサブページのHero画像を簡易スライダー化する
 * 2. /iezukuri/concept/ の本文ブロックをスクロール表示アニメーションさせる
 *
 * 注意:
 * - これは /iezukuri/ トップページの3カード切り替えAJAXではない
 * - 「新築住宅 / 二世帯住宅 / 住宅リフォーム」をクリックして
 *   Site Reading / Works / Plans / Flow / CTA を差し替える処理は、この中には無い
 */

/* =========================================================
 * 01. サブページHero用の簡易スライダー
 * =========================================================
 *
 * 役割:
 * - data-iez-sub-hero を持つHeroブロックを探す
 * - その中のスライドを横にスライドさせる
 * - ページャーボタンをJS側で生成する
 * - 5.2秒ごとに自動で次のスライドへ切り替える
 *
 * 紐づくHTML:
 * - [data-iez-sub-hero]   : Hero全体の親要素
 * - [data-iez-sub-track]  : 横に動かすスライドの入れ物
 * - [data-iez-sub-slide]  : 1枚ごとのスライド
 * - [data-iez-sub-pager]  : JSがページャーボタンを追加する場所
 *
 * 紐づくCSS:
 * - [data-iez-sub-track] 側で display:flex などを持つ想定
 * - JSで track.style.transform = translateX(...) を直接指定する
 * - ページャーの現在位置は .is-active で制御する
 *
 * 想定されるPHPテンプレート:
 * - hub/pages/iezukuri/template-parts/subpage/section-sub-hero.php
 * - または data-iez-sub-hero を出力しているHero共通パーツ
 */

(function () {
  'use strict';

  /**
   * 1つのHeroブロックをスライダー化する
   *
   * @param {HTMLElement} root
   * data-iez-sub-hero が付いたHero全体の要素
   */
  function initHero(root) {
    /**
     * スライド全体を横に動かすトラック
     *
     * HTML側:
     * <div data-iez-sub-track>...</div>
     */
    var track = root.querySelector('[data-iez-sub-track]');

    /**
     * 1枚ごとのスライド
     *
     * HTML側:
     * <div data-iez-sub-slide>...</div>
     */
    var slides = Array.prototype.slice.call(root.querySelectorAll('[data-iez-sub-slide]'));

    /**
     * ページャーボタンを入れる場所
     *
     * HTML側:
     * <div data-iez-sub-pager></div>
     *
     * JS側で button を生成してここに追加する
     */
    var pager = root.querySelector('[data-iez-sub-pager]');

    /**
     * track が無い、またはスライドが1枚以下なら何もしない
     *
     * 理由:
     * - 1枚だけならスライダー化する必要がない
     * - track が無い状態で transform を当てるとエラーになる
     */
    if (!track || slides.length <= 1) {
      return;
    }

    /**
     * 現在表示しているスライド番号
     */
    var index = 0;

    /**
     * JSで生成したページャーボタン一覧
     *
     * go() の中で現在のボタンだけ .is-active を付ける
     */
    var buttons = [];

    /**
     * 指定した番号のスライドへ移動する
     *
     * @param {number} next
     * 次に表示したいスライド番号
     */
    function go(next) {
      /**
       * スライド番号をループさせる
       *
       * 例:
       * - 最後の次 → 先頭へ戻る
       * - 先頭の前 → 最後へ戻る
       */
      index = (next + slides.length) % slides.length;

      /**
       * track を横に移動する
       *
       * CSS側ではなくJSが直接 transform を指定している
       * 1枚目: translateX(0%)
       * 2枚目: translateX(-100%)
       * 3枚目: translateX(-200%)
       */
      track.style.transform = 'translateX(' + -100 * index + '%)';

      /**
       * 現在表示中のページャーだけ .is-active を付ける
       *
       * 紐づくCSS:
       * [data-iez-sub-pager] button.is-active
       * などで見た目を変える想定
       */
      buttons.forEach(function (button, i) {
        button.classList.toggle('is-active', i === index);
      });
    }

    /**
     * ページャーがHTMLに存在する場合だけ、
     * スライド枚数分のbuttonをJSで生成する
     */
    if (pager) {
      slides.forEach(function (_, i) {
        var button = document.createElement('button');

        button.type = 'button';

        /**
         * アクセシビリティ用ラベル
         *
         * 画面には出ないが、読み上げソフト向けに
         * 「Hero 1」「Hero 2」のような意味を持たせる
         */
        button.setAttribute('aria-label', 'Hero ' + (i + 1));

        /**
         * ページャーボタンを押したら、
         * 対応するスライド番号へ移動する
         */
        button.addEventListener('click', function () {
          go(i);
        });

        pager.appendChild(button);
        buttons.push(button);
      });
    }

    /**
     * 初期表示
     *
     * 最初は0番目、つまり1枚目を表示する
     */
    go(0);

    /**
     * 自動再生
     *
     * 5200ms = 5.2秒ごとに次のスライドへ移動する
     *
     * 注意:
     * - マウスホバーで停止する処理は無い
     * - 画面外で停止する処理も無い
     * - Swiperではなく自作の簡易処理
     */
    window.setInterval(function () {
      go(index + 1);
    }, 5200);
  }

  /**
   * DOM読み込み後に、
   * ページ内の全ての data-iez-sub-hero をスライダー化する
   */
  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-iez-sub-hero]').forEach(initHero);
  });
})();

/* =========================================================
 * 02. /iezukuri/concept/ 用のスクロール表示アニメーション
 * =========================================================
 *
 * 役割:
 * - .ch-concept-core の中にある data-iez-concept-reveal 要素を探す
 * - 画面内に入ったら .is-inview を付ける
 * - CSS側で opacity / transform などを切り替えて表示演出する
 *
 * 紐づくHTML:
 * - .ch-concept-core
 *   → conceptページ本文の親ブロック
 *
 * - [data-iez-concept-reveal]
 *   → スクロールで表示アニメーションさせたい各要素
 *
 * 紐づくCSS:
 * - body.is-iez-concept-motion-ready
 *   → JSが有効な時だけアニメーション待機状態にするためのbodyクラス
 *
 * - [data-iez-concept-reveal]
 *   → 初期状態。非表示・少し下げるなど
 *
 * - [data-iez-concept-reveal].is-inview
 *   → 表示状態。opacity:1 / transform:none など
 *
 * - --iez-concept-delay
 *   → JSが要素ごとに付ける表示遅延用CSS変数
 *
 * 想定されるPHPテンプレート:
 * - hub/pages/iezukuri/template-parts/page-content/concept.php
 * - または .ch-concept-core を出力しているconcept用テンプレート
 */
/* === IEZUKURI CONCEPT MOTION START === */
(function () {
  'use strict';

  document.addEventListener('DOMContentLoaded', function () {
    /**
     * conceptページ本文の親ブロック
     *
     * この要素が無ければ、conceptページではないと判断して何もしない
     */
    var root = document.querySelector('.ch-concept-core');

    if (!root) {
      return;
    }

    /**
     * スクロール表示アニメーション対象
     *
     * HTML側で data-iez-concept-reveal を付けた要素だけ対象にする
     */
    var targets = Array.prototype.slice.call(root.querySelectorAll('[data-iez-concept-reveal]'));

    /**
     * 対象が無いなら何もしない
     */
    if (!targets.length) {
      return;
    }

    /**
     * JSが動いていることをbodyに知らせる
     *
     * CSS側ではこのbodyクラスを使って、
     * JS有効時だけアニメーション用の初期状態を当てる想定
     */
    document.body.classList.add('is-iez-concept-motion-ready');

    /**
     * 各要素に表示遅延を付ける
     *
     * 1個目: 0ms
     * 2個目: 80ms
     * 3個目: 160ms
     * ...
     * 最大360msまでに制限
     *
     * CSS側では var(--iez-concept-delay) を transition-delay 等に使う
     */
    targets.forEach(function (target, index) {
      target.style.setProperty('--iez-concept-delay', Math.min(index * 80, 360) + 'ms');
    });

    /**
     * 古いブラウザで IntersectionObserver が使えない場合
     *
     * スクロール監視はせず、最初から全部表示する
     */
    if (!('IntersectionObserver' in window)) {
      targets.forEach(function (target) {
        target.classList.add('is-inview');
      });
      return;
    }

    /**
     * スクロール監視
     *
     * 対象要素が画面内に入ったら .is-inview を付ける
     */
    var observer = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (entry) {
          /**
           * まだ画面内に入っていなければ何もしない
           */
          if (!entry.isIntersecting) {
            return;
          }

          /**
           * 画面内に入った要素を表示状態にする
           *
           * 紐づくCSS:
           * [data-iez-concept-reveal].is-inview
           */
          entry.target.classList.add('is-inview');

          /**
           * 一度表示したら監視を解除する
           *
           * 何度も出たり消えたりさせないため
           */
          observer.unobserve(entry.target);
        });
      },
      {
        /**
         * 要素が14%見えたら発火
         */
        threshold: 0.14,

        /**
         * 画面下から少し早めに発火させる調整
         */
        rootMargin: '0px 0px -8% 0px',
      },
    );

    /**
     * 全対象要素を監視開始
     */
    targets.forEach(function (target) {
      observer.observe(target);
    });
  });
})();
/* === IEZUKURI CONCEPT MOTION END === */
