/**
 * =========================================================
 * IEZUKURI TOP PLAN FRAGMENT AJAX
 * =========================================================
 *
 * 役割:
 * - /iezukuri/ トップページの「3つの住まい」カードをクリックした時に、
 *   ページ遷移せず、下の詳細表示エリアだけを差し替える。
 *
 * 何をしているか:
 * 1. [data-iez-plan-card] を持つカードを探す
 * 2. クリックされたカードの data-iez-plan-term を読む
 * 3. WordPress AJAX に term をPOST送信する
 * 4. PHP側で生成されたHTML断片を受け取る
 * 5. [data-iez-plan-fragment] の中身を差し替える
 *
 * 紐づくページ:
 * - 主に /iezukuri/ トップページ
 *
 * 紐づくHTML:
 * - [data-iez-plan-card]
 *   → クリック対象の3カード
 *
 * - data-iez-plan-term
 *   → どの住宅タイプを読み込むかを示す値
 *   例:
 *   data-iez-plan-term="one-family"
 *   data-iez-plan-term="two-family"
 *   data-iez-plan-term="dual-life"
 *
 * - [data-iez-plan-fragment]
 *   → AJAXで取得したHTMLを流し込む表示先
 *
 * - .ch-service-route-card
 *   → active表示を付け替えるカードの外枠
 *
 * 紐づくPHP:
 * - wp_ajax_naigai_iez_plan_fragment
 * - wp_ajax_nopriv_naigai_iez_plan_fragment
 *
 * PHP側では action=naigai_iez_plan_fragment を受けて、
 * term に応じたHTMLを返す必要がある。
 *
 * 紐づくJS設定:
 * - window.NaigaiIezukuriPlanAjax
 *
 * 必要な中身:
 * {
 *   ajaxUrl: "WordPressのadmin-ajax.php",
 *   nonce: "AJAX検証用nonce"
 * }
 *
 * 注意:
 * - これは間取り詳細ページのタブ切り替えではない
 * - これはLightbox用JSでもない
 * - HTML側の出力先が [data-iez-plan-detail-output] のままだと、このJSは動かない
 * - このJSを使うなら、出力先属性を [data-iez-plan-fragment] に合わせる必要がある
 */
(function () {
  /**
   * DOM読み込み後に処理を実行するための小さい関数。
   *
   * document.readyState が loading ではない場合:
   * - すでにDOMが読めるので即実行する
   *
   * loading の場合:
   * - DOMContentLoaded まで待つ
   */
  function ready(fn) {
    if (document.readyState !== 'loading') {
      fn();
      return;
    }

    document.addEventListener('DOMContentLoaded', fn);
  }

  ready(function () {
    /**
     * 3カードを取得する。
     *
     * 紐づくHTML:
     * <a data-iez-plan-card data-iez-plan-term="one-family">...</a>
     * <a data-iez-plan-card data-iez-plan-term="two-family">...</a>
     * <a data-iez-plan-card data-iez-plan-term="dual-life">...</a>
     *
     * この属性が無いカードは、このJSではクリック対象にならない。
     */
    const cards = Array.from(document.querySelectorAll('[data-iez-plan-card]'));

    /**
     * AJAXで取得したHTMLを差し込む場所。
     *
     * 紐づくHTML:
     * <div data-iez-plan-fragment></div>
     *
     * 注意:
     * もしHTML側が
     * <div data-iez-plan-detail-output></div>
     * になっている場合、このJSは target を取得できず return する。
     */
    const target = document.querySelector('[data-iez-plan-fragment]');

    /**
     * WordPress側から wp_localize_script などで渡されるAJAX設定。
     *
     * 必要:
     * - config.ajaxUrl
     *   → admin-ajax.php のURL
     *
     * - config.nonce
     *   → PHP側で check_ajax_referer するためのnonce
     *
     * 想定:
     * window.NaigaiIezukuriPlanAjax = {
     *   ajaxUrl: ".../wp-admin/admin-ajax.php",
     *   nonce: "..."
     * };
     */
    const config = window.NaigaiIezukuriPlanAjax || {};

    /**
     * 必要なものが揃っていなければ何もしない。
     *
     * ここで止まる条件:
     * - data-iez-plan-card が無い
     * - data-iez-plan-fragment が無い
     * - ajaxUrl が渡されていない
     * - nonce が渡されていない
     */
    if (!cards.length || !target || !config.ajaxUrl || !config.nonce) {
      return;
    }

    /**
     * 実行中のfetchを管理するためのAbortController。
     *
     * 役割:
     * - 連続クリックされた時に、前のAJAX通信を中断する
     * - 古いレスポンスが後から返ってきて表示を上書きする事故を防ぐ
     */
    let controller = null;

    /**
     * クリックされたカードを active 状態にする。
     *
     * 何をしているか:
     * - 全カードを確認する
     * - クリックされたカードだけ active にする
     * - aria-current も true / false に切り替える
     *
     * 紐づくHTML:
     * - [data-iez-plan-card]
     * - .ch-service-route-card
     *
     * 紐づくCSS:
     * - .ch-service-route-card.is-active
     * - [data-iez-plan-card][aria-current="true"]
     */
    function setActive(activeCard) {
      cards.forEach((card) => {
        /**
         * card の外側に .ch-service-route-card があれば取得する。
         *
         * card 自体が .ch-service-route-card の場合も closest で拾える。
         */
        const item = card.closest('.ch-service-route-card');

        /**
         * 今処理している card が、クリックされた card かどうか。
         */
        const active = card === activeCard;

        /**
         * 見た目用の active クラスを切り替える。
         */
        if (item) {
          item.classList.toggle('is-active', active);
        }

        /**
         * アクセシビリティ用。
         *
         * 現在選択中のカードだけ aria-current="true" にする。
         */
        card.setAttribute('aria-current', active ? 'true' : 'false');
      });
    }

    /**
     * クリックされたカードに対応するHTML断片をAJAXで読み込む。
     *
     * 流れ:
     * 1. card.dataset.iezPlanTerm から term を取得
     * 2. 既存通信があれば中断
     * 3. target を loading 状態にする
     * 4. admin-ajax.php にPOSTする
     * 5. JSONを受け取る
     * 6. json.data.html を target.innerHTML に入れる
     */
    async function loadPlanFragment(card) {
      /**
       * data-iez-plan-term の値を取得する。
       *
       * HTML:
       * data-iez-plan-term="one-family"
       *
       * JS:
       * card.dataset.iezPlanTerm
       */
      const term = card.dataset.iezPlanTerm;

      /**
       * term が無ければ、どの内容を読み込むか分からないので中止。
       */
      if (!term) {
        return;
      }

      /**
       * すでに通信中なら中断する。
       *
       * 例:
       * - 一世帯住宅をクリック
       * - すぐ二世帯住宅をクリック
       * → 一世帯住宅の通信を中断して、二世帯住宅だけ生かす
       */
      if (controller) {
        controller.abort();
      }

      controller = new AbortController();

      /**
       * 先にカードの active 表示を切り替える。
       */
      setActive(card);

      /**
       * 読み込み中の状態を表示先に付ける。
       *
       * 紐づくCSS:
       * - [data-iez-plan-fragment].is-loading
       */
      target.classList.add('is-loading');

      /**
       * aria-busy はアクセシビリティ用。
       *
       * true:
       * - この領域は今更新中
       *
       * false:
       * - 更新完了
       */
      target.setAttribute('aria-busy', 'true');

      /**
       * WordPress AJAX に送るPOSTデータを作る。
       *
       * action:
       * - PHP側のAJAXフック名に紐づく
       *
       * nonce:
       * - 不正リクエスト対策
       *
       * term:
       * - どの住宅タイプを表示するか
       */
      const body = new URLSearchParams();
      body.set('action', 'naigai_iez_plan_fragment');
      body.set('nonce', config.nonce);
      body.set('term', term);

      try {
        /**
         * WordPress admin-ajax.php にPOST送信する。
         *
         * credentials: same-origin
         * - WordPressログインCookieなど、同一オリジンの認証情報を送る
         *
         * signal: controller.signal
         * - controller.abort() で通信中断できるようにする
         */
        const response = await fetch(config.ajaxUrl, {
          method: 'POST',
          credentials: 'same-origin',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
          },
          body: body.toString(),
          signal: controller.signal,
        });

        /**
         * PHP側から返るJSONを読む。
         *
         * 想定レスポンス:
         * {
         *   success: true,
         *   data: {
         *     html: "<section>...</section>"
         *   }
         * }
         */
        const json = await response.json();

        /**
         * レスポンス形式が想定外ならエラーにする。
         *
         * ここで落ちる例:
         * - PHP側で Fatal error
         * - nonce 不一致
         * - json.success が false
         * - json.data.html が無い
         */
        if (!json || !json.success || !json.data || typeof json.data.html !== 'string') {
          throw new Error('AJAX response error');
        }

        /**
         * 取得したHTMLを表示先に流し込む。
         *
         * ここで /iezukuri/ トップの下部表示が差し替わる。
         */
        target.innerHTML = json.data.html;

        /**
         * 差し替えた場所までスクロールする。
         *
         * 注意:
         * - 固定ヘッダー分の余白調整はしていない
         * - ヘッダーに隠れる場合は scroll-margin-top をCSS側で指定するとよい
         */
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      } catch (error) {
        /**
         * AbortError は意図的な中断なのでエラー表示しない。
         *
         * それ以外は console.error に出す。
         */
        if (error.name !== 'AbortError') {
          console.error(error);
        }
      } finally {
        /**
         * 成功/失敗どちらでも loading 状態を解除する。
         */
        target.classList.remove('is-loading');
        target.setAttribute('aria-busy', 'false');

        /**
         * 現在の通信管理をリセットする。
         */
        controller = null;
      }
    }

    /**
     * 各カードにクリックイベントを付ける。
     *
     * 役割:
     * - 通常のリンク遷移を止める
     * - AJAXで詳細HTMLを読み込む
     *
     * 紐づくHTML:
     * <a href="..." data-iez-plan-card data-iez-plan-term="one-family">
     */
    cards.forEach((card) => {
      card.addEventListener('click', function (event) {
        /**
         * aタグの場合にページ遷移しないように止める。
         */
        event.preventDefault();

        /**
         * クリックされたカードの内容を読み込む。
         */
        loadPlanFragment(card);
      });
    });
  });
})();
