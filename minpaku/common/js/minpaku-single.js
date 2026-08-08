/**
 * =========================================================
 * minpaku-single.js
 * 民泊「詳細ページ / checkoutページ」専用JS
 * =========================================================
 *
 * 読み込み場所:
 * - functions.php
 * - themebs_enqueue_scripts()
 * - is_singular('minpaku') または checkout endpoint の時に読み込む
 *
 * 対象ページ:
 * - single-minpaku.php
 * - single-minpaku-checkout.php
 *
 * 対象HTML:
 * - single-minpaku.php 787行目付近
 *   #mnpk-date-modal
 *
 * 主な役割:
 * 1. 詳細ページ右側の予約カードを動かす
 * 2. #mnpk-date-modal の日付モーダルを開閉する
 * 3. カレンダーを描画する
 * 4. チェックイン / チェックアウトを選択する
 * 5. 人数モーダルを動かす
 * 6. checkout画面の料金・日付・人数を同期する
 * 7. Stripe支払いモーダルを動かす
 * 8. 写真モーダル / Swiper を動かす
 *
 * 重要:
 * - アーカイブ一覧の予約モーダルはこのJSでは動かさない
 * - アーカイブ側は minpaku/common/js/minpaku-archive-booking.js が担当
 * - このJSは #mnpk-booking-card が無いページでは即returnする
 *
 * 関連CSS:
 * - minpaku/common/css/minpaku-booking.css
 *
 * 関連HTML:
 * - 詳細モーダル: #mnpk-date-modal
 * - 人数モーダル: #mnpk-guest-modal
 * - 支払いモーダル: #mnpk-payment-modal
 * - 写真モーダル: #mnpk-photo-modal
 * =========================================================
 */
document.addEventListener('DOMContentLoaded', function () {
  const page = document.querySelector('.mnpk-single-page, .mnpk-checkout-page');
  if (!page) return;

  const bookingCard = document.getElementById('mnpk-booking-card');
  if (!bookingCard) return;

  const legacyBookingCardData = document.getElementById('mnpk-booking-card-data');

  const readDataset = (name, fallback = '') => {
    const mainValue = bookingCard.dataset[name];
    if (mainValue !== undefined && mainValue !== '') {
      return mainValue;
    }

    if (legacyBookingCardData) {
      const legacyValue = legacyBookingCardData.dataset[name];
      if (legacyValue !== undefined && legacyValue !== '') {
        return legacyValue;
      }
    }

    return fallback;
  };

  const readInt = (name, fallback = 0) => {
    const value = parseInt(readDataset(name, String(fallback)), 10);
    return Number.isNaN(value) ? fallback : value;
  };

  const readFloat = (name, fallback = 0) => {
    const value = parseFloat(readDataset(name, String(fallback)));
    return Number.isNaN(value) ? fallback : value;
  };

  const readJsonArray = (name, fallback = []) => {
    const raw = readDataset(name, '');
    if (!raw) return fallback;

    try {
      const parsed = JSON.parse(raw);
      return Array.isArray(parsed) ? parsed : fallback;
    } catch (error) {
      console.error('JSON parse error:', error);
      return fallback;
    }
  };

  const cfg = {
    postId: readInt('postId', 0),
    nightlyPrice: readFloat('nightlyPrice', 0),
    weekendPrice: readFloat('weekendPrice', 0),
    cleaningFee: readFloat('cleaningFee', 0),
    capacity: readInt('capacity', 1),
    baseGuests: readInt('baseGuests', 1),
    extraGuestFee: readFloat('extraGuestFee', 0),
    minNights: readInt('minNights', 1),
    checkinTime: readDataset('checkinTime', '15:00'),
    checkoutTime: readDataset('checkoutTime', '10:00'),
    openStartDate: readDataset('openStartDate', ''),
    cleaningBufferDays: readInt('cleaningBufferDays', 0),
    cleaningNote: readDataset('cleaningNote', ''),
    calendarEvents: readJsonArray('calendarEvents', []),
    checkoutUrl: readDataset('checkoutUrl', ''),
    detailUrl: readDataset('detailUrl', window.location.href),
    leadImage: readDataset('leadImage', ''),
    stayTitle: readDataset(
      'stayTitle',
      document.querySelector('.mnpk-title, .mnpk-checkout-page__title')?.textContent?.trim() || '',
    ),
    stayMeta: readDataset('stayMeta', ''),
  };

  if (!cfg.weekendPrice || cfg.weekendPrice <= 0) {
    cfg.weekendPrice = cfg.nightlyPrice;
  }
  if (!cfg.baseGuests || cfg.baseGuests < 1) {
    cfg.baseGuests = 1;
  }
  if (cfg.baseGuests > cfg.capacity) {
    cfg.baseGuests = cfg.capacity;
  }

  const state = {
    checkIn: readDataset(
      'initialCheckin',
      document.getElementById('mnpk-checkin-input')?.value || '',
    ),
    checkOut: readDataset(
      'initialCheckout',
      document.getElementById('mnpk-checkout-input')?.value || '',
    ),
    adults: readInt('initialAdults', 1),
    children: readInt('initialChildren', 0),
    monthCursor: null,
  };

  const guestDraft = {
    adults: state.adults,
    children: state.children,
  };

  const isCheckoutPage = !!document.querySelector('.mnpk-checkout-page');
  const dateModal = document.getElementById('mnpk-date-modal');
  const guestModal = document.getElementById('mnpk-guest-modal');
  const paymentModal = document.getElementById('mnpk-payment-modal');
  const photoModal = document.getElementById('mnpk-photo-modal');

  const checkinInput = document.getElementById('mnpk-checkin-input');
  const checkoutInput = document.getElementById('mnpk-checkout-input');

  const selectionDates = bookingCard.querySelector('[data-selection-dates]');
  const selectionGuests = bookingCard.querySelector('[data-selection-guests]');
  const priceRoom = bookingCard.querySelector('[data-price-room]');
  const priceGuest = bookingCard.querySelector('[data-price-guest]');
  const priceCleaning = bookingCard.querySelector('[data-price-cleaning]');
  const priceTotal = bookingCard.querySelector('[data-price-total]');
  const bookingError = bookingCard.querySelector('[data-booking-error]');

  const allCheckoutDates = Array.from(document.querySelectorAll('[data-checkout-dates]'));
  const allCheckoutGuests = Array.from(document.querySelectorAll('[data-checkout-guests]'));
  const allCheckoutRoomFee = Array.from(document.querySelectorAll('[data-checkout-room-fee]'));
  const allCheckoutGuestFee = Array.from(document.querySelectorAll('[data-checkout-guest-fee]'));
  const allCheckoutCleaningFee = Array.from(
    document.querySelectorAll('[data-checkout-cleaning-fee]'),
  );
  const allCheckoutTotal = Array.from(document.querySelectorAll('[data-checkout-total]'));
  const allCheckoutStayTitle = Array.from(document.querySelectorAll('[data-checkout-stay-title]'));
  const allCheckoutStayMeta = Array.from(document.querySelectorAll('[data-checkout-stay-meta]'));
  const allCheckoutThumb = Array.from(document.querySelectorAll('[data-checkout-thumb]'));

  const paymentForm = document.getElementById('mnpk-payment-form');
  const paymentElementWrap = document.getElementById('mnpk-payment-element');
  const paymentElementFrameWrap = document.querySelector('[data-payment-element-wrap]');
  const paymentElementSkeleton = document.querySelector('[data-payment-skeleton]');
  const paymentErrorBox = document.querySelector('[data-payment-error]');
  const paymentStatusBox = document.querySelector('[data-payment-status]');
  const paymentNameInput = document.querySelector('[data-payment-name]');
  const paymentEmailInput = document.querySelector('[data-payment-email]');
  const paymentTotalLabel = document.querySelector('[data-payment-total]');
  const mobileOpenButtons = Array.from(
    document.querySelectorAll('[data-mnpk-mobile-booking-open]'),
  );

  const guestHelp = document.querySelector('[data-guest-help]');
  const calendarCheckinLabel = document.querySelector('[data-calendar-checkin-label]');
  const calendarCheckoutLabel = document.querySelector('[data-calendar-checkout-label]');
  const calendarHelp = document.querySelector('[data-calendar-help]');
  const calendarError = document.querySelector('[data-calendar-error]');
  const calendarMonthEls = document.querySelectorAll('[data-calendar-month]');

  let stripeInstance = null;
  let elementsInstance = null;
  let paymentElementInstance = null;

  // NAIGAI_STRIPE_READY_MOUNT_FIX_20260730
  let paymentElementReady = false;
  let paymentElementMountPromise = null;
  let currentClientSecret = '';
  let reopenPaymentAfterPicker = '';

  const today = startOfDay(new Date());

  function isMobile() {
    return window.matchMedia('(max-width: 767px)').matches;
  }

  function yen(value) {
    return new Intl.NumberFormat('ja-JP', {
      style: 'currency',
      currency: 'JPY',
      maximumFractionDigits: 0,
    }).format(Number(value || 0));
  }

  function startOfDay(date) {
    return new Date(date.getFullYear(), date.getMonth(), date.getDate());
  }

  function startOfMonth(date) {
    return new Date(date.getFullYear(), date.getMonth(), 1);
  }

  function parseYmd(ymd) {
    if (!ymd || !/^\d{4}-\d{2}-\d{2}$/.test(ymd)) return null;
    const [y, m, d] = ymd.split('-').map(Number);
    return new Date(y, m - 1, d);
  }

  function formatYmd(date) {
    const y = date.getFullYear();
    const m = String(date.getMonth() + 1).padStart(2, '0');
    const d = String(date.getDate()).padStart(2, '0');
    return `${y}-${m}-${d}`;
  }

  function addDays(date, days) {
    const next = new Date(date);
    next.setDate(next.getDate() + days);
    return next;
  }

  function isSameDay(a, b) {
    return formatYmd(a) === formatYmd(b);
  }

  function diffNights(startYmd, endYmd) {
    const start = parseYmd(startYmd);
    const end = parseYmd(endYmd);
    if (!start || !end) return 0;
    return Math.round((end - start) / (1000 * 60 * 60 * 24));
  }

  function formatDateJa(ymd) {
    const date = parseYmd(ymd);
    if (!date) return '未選択';
    return date.toLocaleDateString('ja-JP', {
      year: 'numeric',
      month: 'long',
      day: 'numeric',
      weekday: 'short',
    });
  }

  function totalGuests() {
    return state.adults + state.children;
  }

  function setBookingError(message = '') {
    if (!bookingError) return;

    if (!message) {
      bookingError.hidden = true;
      bookingError.textContent = '';
      return;
    }

    bookingError.hidden = false;
    bookingError.textContent = message;
  }

  function clearBookingError() {
    setBookingError('');
  }

  function setCalendarError(message = '') {
    if (!calendarError) return;

    if (!message) {
      calendarError.hidden = true;
      calendarError.textContent = '';
      return;
    }

    calendarError.hidden = false;
    calendarError.textContent = message;
  }


  function setPaymentDebugStatus(message = '', isError = false) {
    if (!paymentStatusBox) return;

    if (!message) {
      paymentStatusBox.hidden = true;
      paymentStatusBox.textContent = '';
      paymentStatusBox.classList.remove('is-error');
      return;
    }

    paymentStatusBox.hidden = false;
    paymentStatusBox.textContent = message;
    paymentStatusBox.classList.toggle('is-error', !!isError);
  }
  function setPaymentError(message = '') {
    if (!paymentErrorBox) return;

    if (!message) {
      paymentErrorBox.hidden = true;
      paymentErrorBox.textContent = '';
      return;
    }

    paymentErrorBox.hidden = false;
    paymentErrorBox.textContent = message;
  }

  function clearAllErrors() {
    clearBookingError();
    setCalendarError('');
    setPaymentError('');
  }


  // =========================================================
  // MINPAKU_CHECKOUT_SHARED_THANKS
  // =========================================================
  //
  // Stripe決済成功後、
  // checkoutの「決済前画面」から
  // 共通サンクス表示へ切り替える。
  //
  // URLは変更しない。
  //
  // 共通化するのは画面のHTML。
  // CVは minpaku_purchase という
  // 民泊専用イベントで分離する。
  // =========================================================

  function pushMinpakuPurchaseEvent(paymentIntent) {
    if (!paymentIntent || !paymentIntent.id) return;

    const transactionId = String(paymentIntent.id);

    /*
     * 同じPaymentIntentで、
     * 同一タブからイベントを複数回発火しないためのガード。
     */
    const storageKey = `mnpk_purchase_${transactionId}`;

    try {
      if (
        window.sessionStorage.getItem(storageKey) === '1'
      ) {
        return;
      }
    } catch (error) {
      console.warn(
        '[mnpk] sessionStorage read failed',
        error
      );
    }

    const calc = calculateBooking();

    /*
     * =====================================================
     * 民泊専用CV
     * =====================================================
     *
     * サンクスURLで判定するのではなく、
     *
     * event: minpaku_purchase
     *
     * というイベント名で判別する。
     *
     * そのため、
     * お問い合わせと同じサンクスUIを使っても
     * CVは別々に管理できる。
     *
     * GTM側では、
     * minpaku_purchase をトリガーとして
     * GA4のpurchase等へ接続できる。
     */
    window.dataLayer = window.dataLayer || [];

    /*
 * NAIGAI_MINPAKU_GA4_PURCHASE
 *
 * Stripeの決済が成功したときに、
 * サイト独自イベント「minpaku_purchase」をdataLayerへ送る。
 *
 * このJSからGA4へ直接送信しているわけではない。
 *
 * 流れ:
 *
 * Stripe 決済成功
 *   ↓
 * dataLayer.push()
 *   event = minpaku_purchase
 *   ↓
 * GTMのカスタムイベントトリガー
 *   minpaku_purchase
 *   ↓
 * GTMの「Google アナリティクス: GA4 イベント」
 *   event name = purchase
 *   ↓
 * GA4
 *
 * ecommerce の各値はGTM側の
 * 「データレイヤーの変数（DLV）」で読み取る。
 *
 * 対応:
 *
 * ecommerce.transaction_id
 *   → DLV - ecommerce.transaction_id
 *   → Stripe PaymentIntent IDを購入取引IDとして使用
 *
 * ecommerce.value
 *   → DLV - ecommerce.value
 *   → 今回の決済総額
 *
 * ecommerce.currency
 *   → DLV - ecommerce.currency
 *   → JPY
 *
 * ecommerce.items
 *   → DLV - ecommerce.items
 *   → GA4の商品 / 宿泊施設情報
 *
 * GTM側でこれらをGA4の purchase イベントパラメータへ渡す。
 *
 * 注意:
 * GTMのDLV変数そのものはWordPressコードでは定義しない。
 * GTM管理画面側で設定・管理する。
 */
window.dataLayer.push({
      event: 'minpaku_purchase',

      ecommerce: {
        transaction_id: transactionId,
        value:
          calc && calc.valid
            ? Number(calc.total)
            : 0,
        currency: 'JPY',

        items: [
          {
            item_id: String(cfg.postId || ''),
            item_name:
              document
                .querySelector('[data-checkout-stay-title]')
                ?.textContent
                ?.trim() || document.title,
            quantity: 1,
          },
        ],
      },
    });

    try {
      window.sessionStorage.setItem(
        storageKey,
        '1'
      );
    } catch (error) {
      console.warn(
        '[mnpk] sessionStorage write failed',
        error
      );
    }
  }


  function showMinpakuCheckoutThanks(paymentIntent) {
    if (!isCheckoutPage) return false;

    const checkoutHead =
      document.querySelector(
        '.mnpk-checkout-page-head'
      );

    const checkoutMain =
      document.querySelector(
        '.mnpk-checkout-page-main'
      );

    const checkoutThanks =
      document.getElementById(
        'mnpk-checkout-thanks'
      );

    if (!checkoutMain || !checkoutThanks) {
      console.error(
        '[mnpk] checkout thanks UI not found'
      );

      return false;
    }

    /*
     * =====================================================
     * 決済後は決済前UIを全部終了する
     * =====================================================
     *
     * 決済後に不要なもの:
     *
     * ・宿泊日の「変更」
     * ・人数の「変更」
     * ・料金再計算UI
     * ・氏名入力
     * ・メール入力
     * ・Stripe Payment Element
     * ・支払い確定ボタン
     *
     * 個別に隠すのではなく、
     * checkout本体をまとめて非表示にする。
     */
    if (checkoutHead) {
      checkoutHead.hidden = true;
    }

    checkoutMain.hidden = true;

    /*
     * PHP側であらかじめ出力している
     * 共通サンクスだけを表示する。
     */
    checkoutThanks.hidden = false;

    document.body.classList.add(
      'mnpk-checkout-is-complete'
    );

    /*
     * サンクス表示とCV計測は別処理。
     *
     * ここでは民泊専用CVだけ発火する。
     */
    pushMinpakuPurchaseEvent(
      paymentIntent
    );

    window.scrollTo({
      top: 0,
      behavior: 'smooth',
    });

    return true;
  }


  // =========================================================
  // MINPAKU_CHECKOUT_HISTORY_BACK
  // =========================================================
  //
  // checkoutの「前のページへ戻る」は、
  // 詳細ページURLを新しく開くリンクではない。
  //
  // ブラウザ履歴を本当に1段だけ戻す。
  //
  // 正常な流れ:
  //
  //   宿泊一覧
  //      ↓
  //   宿泊詳細
  //      ↓
  //   checkout
  //
  // checkoutでBack
  //      ↓
  //   宿泊詳細
  //
  // 詳細でもう一度Back
  //      ↓
  //   宿泊一覧
  //
  // これによって
  //
  // checkout → 詳細 → checkout
  //
  // という固定URLによる往復を防ぐ。
  // =========================================================

  const checkoutBackResetKey =
    'mnpk_checkout_reset_after_back';


  document
    .querySelectorAll('[data-mnpk-history-back]')
    .forEach((link) => {

      link.addEventListener('click', (event) => {

        let canUseInternalHistory = false;

        /*
         * 同じサイト内からcheckoutへ来た場合だけ
         * history.back() を使用する。
         *
         * URL直接入力や外部サイトから開いた場合は
         * HTMLのhrefをそのまま使用する。
         */
        try {

          if (document.referrer) {

            const referrer =
              new URL(document.referrer);

            canUseInternalHistory =
              referrer.origin ===
                window.location.origin
              && window.history.length > 1;

          }

        } catch (error) {

          console.warn(
            '[mnpk] Back Link referrer check failed',
            error
          );

        }


        if (!canUseInternalHistory) {
          return;
        }

        event.preventDefault();


        /*
         * ===================================================
         * checkout入力内容を次回へ持ち越さない
         * ===================================================
         *
         * Backした瞬間に履歴そのものを削除するわけではない。
         *
         * 「このcheckoutを次に表示した時は、
         *  フォームを新しい状態で作り直す」
         *
         * という目印だけsessionStorageへ保存する。
         *
         * 日付・人数はURLの
         *
         * ?checkin=
         * &checkout=
         * &adults=
         * &children=
         *
         * から再構築できる。
         *
         * 一方、
         *
         * ・名前
         * ・メール
         * ・Stripeカード入力
         *
         * は古い状態を復元しない。
         */
        if (isCheckoutPage) {

          try {

            window.sessionStorage.setItem(
              checkoutBackResetKey,
              '1'
            );

          } catch (error) {

            console.warn(
              '[mnpk] checkout reset flag save failed',
              error
            );

          }

        }


        /*
         * hrefで詳細ページを開き直すのではなく、
         * ブラウザ履歴を1段だけ戻す。
         */
        window.history.back();

      });

    });


  /*
   * =========================================================
   * checkoutが履歴から復元された場合
   * =========================================================
   *
   * Chrome等はBack/Forward時に
   * ページをBFCacheからそのまま復元することがある。
   *
   * その場合、
   *
   * ・名前
   * ・メール
   * ・Stripe Payment Element
   *
   * まで以前の画面状態が復元される可能性がある。
   *
   * Back Linkで一度離れたcheckoutを再表示した場合は
   * 1回だけページを読み直し、
   * Stripeを含めて新規状態から開始する。
   *
   * URLはそのままなので、
   * 日程・人数はquery stringから再表示される。
   */
  window.addEventListener(
    'pageshow',
    () => {

      if (!isCheckoutPage) {
        return;
      }

      let shouldReset = false;

      try {

        shouldReset =
          window.sessionStorage.getItem(
            checkoutBackResetKey
          ) === '1';

      } catch (error) {

        console.warn(
          '[mnpk] checkout reset flag read failed',
          error
        );

      }

      if (!shouldReset) {
        return;
      }


      /*
       * 先にフラグを削除する。
       *
       * 削除してからreloadすることで
       * reload → reload → reload
       * の無限ループを防ぐ。
       */
      try {

        window.sessionStorage.removeItem(
          checkoutBackResetKey
        );

      } catch (error) {

        console.warn(
          '[mnpk] checkout reset flag remove failed',
          error
        );

      }


      /*
       * checkoutページを新しく読み直す。
       *
       * 結果:
       *
       * 日付・人数
       *   → URLから復元
       *
       * 名前・メール
       *   → 空
       *
       * Stripeカード
       *   → Payment Elementを新しく生成
       */
      window.location.reload();

    }
  );

  function openModal(modal) {
    if (!modal) return;

    // =====================================================
    // MINPAKU_PHOTO_MODAL_BODY_LAYER
    // =====================================================
    //
    // 写真モーダルは既存 #mnpk-photo-modal を使う。
    // 新しいモーダルを作る処理ではない。
    //
    // 写真だけは body 直下へ移動し、
    // サイトヘッダー・パンくず・民泊ナビ等の
    // 重なり順の影響を受けないようにする。
    //
    // 日付・人数など他のモーダルは移動しない。
    if (
      modal.id === 'mnpk-photo-modal'
      && modal.parentElement !== document.body
    ) {
      document.body.appendChild(modal);
    }

    modal.classList.add('is-open');
    modal.setAttribute('aria-hidden', 'false');
    document.body.classList.add('mnpk-modal-open');
  }

  function closeModal(modal) {
    if (!modal) return;
    modal.classList.remove('is-open');
    modal.setAttribute('aria-hidden', 'true');

    if (!document.querySelector('.mnpk-modal.is-open')) {
      document.body.classList.remove('mnpk-modal-open');
    }
  }

  document.querySelectorAll('[data-close-modal]').forEach((button) => {
    button.addEventListener('click', () => {
      const modal = button.closest('.mnpk-modal');
      closeModal(modal);
    });
  });

  document.addEventListener('keydown', (event) => {
    if (event.key !== 'Escape') return;
    document.querySelectorAll('.mnpk-modal.is-open').forEach((modal) => closeModal(modal));
  });

  let mobileGallerySwiper = null;
  let photoModalSwiper = null;

  if (window.Swiper) {
    const mobileGalleryEl = document.querySelector('.mnpk-gallery-swiper');
    if (mobileGalleryEl) {
      mobileGallerySwiper = new Swiper(mobileGalleryEl, {
        slidesPerView: 1,
        spaceBetween: 0,
        loop: false,
        pagination: {
          el: mobileGalleryEl.querySelector('.mnpk-gallery-swiper__pagination'),
          clickable: true,
        },
      });
    }

    const photoModalEl = document.querySelector('.mnpk-photo-modal-swiper');
    if (photoModalEl && photoModal) {
      photoModalSwiper = new Swiper(photoModalEl, {
        slidesPerView: 1,
        spaceBetween: 16,
        loop: true,
        navigation: {
          prevEl: photoModal.querySelector('.mnpk-photo-prev'),
          nextEl: photoModal.querySelector('.mnpk-photo-next'),
        },
        pagination: {
          el: photoModal.querySelector('.mnpk-photo-pagination'),
          clickable: true,
        },
      });
    }
  }

  document.querySelectorAll('[data-open-gallery]').forEach((button) => {
    button.addEventListener('click', () => {
      const index = parseInt(button.dataset.galleryIndex || '0', 10);
      openModal(photoModal);
      if (photoModalSwiper) {
        /*
         * モーダルをbody直下へ移した後なので、
         * Swiperへ現在の幅・高さを再計算させる。
         */
        photoModalSwiper.update();
        photoModalSwiper.slideTo(index, 0);
      }
    });
  });

  const normalizedEvents = (cfg.calendarEvents || [])
    .map((event) => {
      const start = String(event.start || '').trim();
      const end = String(event.end || '').trim();
      const status = String(event.status || 'blocked')
        .trim()
        .toLowerCase();
      const note = String(event.note || '').trim();
      if (!start || !end) return null;

      return {
        start: start <= end ? start : end,
        end: end >= start ? end : start,
        status,
        note,
      };
    })
    .filter(Boolean)
    .sort((a, b) => a.start.localeCompare(b.start));

  function findExplicitEventForDate(ymd) {
    return normalizedEvents.find((event) => ymd >= event.start && ymd <= event.end) || null;
  }

  function isWithinCleaningBuffer(ymd) {
    if (!cfg.cleaningBufferDays || cfg.cleaningBufferDays <= 0) {
      return false;
    }

    if (findExplicitEventForDate(ymd)) {
      return false;
    }

    return normalizedEvents.some((event) => {
      const isReserved = ['reserved', 'booked'].includes(event.status);
      if (!isReserved) return false;

      const endDate = parseYmd(event.end);
      if (!endDate) return false;

      for (let i = 1; i <= cfg.cleaningBufferDays; i++) {
        const bufferDate = formatYmd(addDays(endDate, i));
        if (bufferDate === ymd) {
          return true;
        }
      }
      return false;
    });
  }

  function getDateStatus(ymd) {
    const date = parseYmd(ymd);
    if (!date) return { status: 'blocked', mark: '—', note: '日付不正' };
    if (date < today) return { status: 'blocked', mark: '—', note: '過去日は選択不可' };
    if (cfg.openStartDate && ymd < cfg.openStartDate) {
      return { status: 'blocked', mark: '—', note: '営業開始前' };
    }

    const explicitEvent = findExplicitEventForDate(ymd);
    if (explicitEvent) {
      if (['available', 'open', 'vacant', 'ok', '○'].includes(explicitEvent.status)) {
        return { status: 'available', mark: '○', note: explicitEvent.note || '予約可能' };
      }
      if (['reserved', 'booked'].includes(explicitEvent.status)) {
        return { status: 'reserved', mark: '×', note: explicitEvent.note || '予約済み' };
      }
      if (explicitEvent.status === 'cleaning') {
        return {
          status: 'cleaning',
          mark: '清',
          note: explicitEvent.note || cfg.cleaningNote || '清掃中',
        };
      }
      return { status: 'blocked', mark: '—', note: explicitEvent.note || '停止中' };
    }

    if (isWithinCleaningBuffer(ymd)) {
      return { status: 'cleaning', mark: '清', note: cfg.cleaningNote || '清掃バッファ日' };
    }

    return { status: 'available', mark: '○', note: '予約可能' };
  }

  function isSelectableDate(ymd) {
    return getDateStatus(ymd).status === 'available';
  }

  function isRangeSelectable(startYmd, endYmd) {
    const nights = diffNights(startYmd, endYmd);
    if (nights <= 0) return false;
    if (nights < cfg.minNights) return false;

    let cursor = parseYmd(startYmd);
    while (formatYmd(cursor) < endYmd) {
      const ymd = formatYmd(cursor);
      if (!isSelectableDate(ymd)) {
        return false;
      }
      cursor = addDays(cursor, 1);
    }
    return true;
  }

  function getCalendarBaseDate() {
    if (state.checkIn) {
      const selected = parseYmd(state.checkIn);
      if (selected) return selected;
    }

    if (cfg.openStartDate) {
      const openStart = parseYmd(cfg.openStartDate);
      if (openStart && openStart > today) return openStart;
    }

    return today;
  }

  function renderDateLabels() {
    if (calendarCheckinLabel) {
      calendarCheckinLabel.textContent = state.checkIn ? formatDateJa(state.checkIn) : '未選択';
    }

    if (calendarCheckoutLabel) {
      calendarCheckoutLabel.textContent = state.checkOut ? formatDateJa(state.checkOut) : '未選択';
    }

    if (calendarHelp) {
      calendarHelp.textContent = cfg.openStartDate
        ? `営業開始日は ${formatDateJa(cfg.openStartDate)} です。`
        : '営業日として開いている日だけ選択できます。';
    }
  }

  function handleDatePick(ymd) {
    clearBookingError();
    setCalendarError('');

    if (!state.checkIn || (state.checkIn && state.checkOut)) {
      state.checkIn = ymd;
      state.checkOut = '';
      renderCalendar();
      return;
    }

    if (ymd <= state.checkIn) {
      state.checkIn = ymd;
      state.checkOut = '';
      renderCalendar();
      return;
    }

    if (!isRangeSelectable(state.checkIn, ymd)) {
      setCalendarError(
        `選択した範囲に予約不可日があります。最低 ${cfg.minNights}泊以上で、空き日だけを選択してください。`,
      );
      return;
    }

    state.checkOut = ymd;
    renderCalendar();
  }

  function renderMonth(mountEl, baseDate) {
    const year = baseDate.getFullYear();
    const month = baseDate.getMonth();
    const firstDay = new Date(year, month, 1);
    const lastDay = new Date(year, month + 1, 0);
    const startWeekday = firstDay.getDay();
    const totalDays = lastDay.getDate();
    const weekdays = ['日', '月', '火', '水', '木', '金', '土'];

    let html = '';
    html += `<div class="mnpk-calendar-month__title">${year}年${month + 1}月</div>`;
    html += '<div class="mnpk-calendar-weekdays">';
    weekdays.forEach((wd) => (html += `<span>${wd}</span>`));
    html += '</div><div class="mnpk-calendar-days">';

    for (let i = 0; i < startWeekday; i++) {
      html += '<span class="mnpk-calendar-day mnpk-calendar-day--empty"></span>';
    }

    for (let day = 1; day <= totalDays; day++) {
      const date = new Date(year, month, day);
      const ymd = formatYmd(date);
      const status = getDateStatus(ymd);

      const isDisabled = status.status !== 'available';
      const isCheckIn = state.checkIn === ymd;
      const isCheckOut = state.checkOut === ymd;
      const inRange =
        state.checkIn && state.checkOut && ymd > state.checkIn && ymd < state.checkOut;
      const isTodayMark = isSameDay(date, today);

      let classes = 'mnpk-calendar-day';
      classes += ` is-${status.status}`;
      if (isDisabled) classes += ' is-disabled';
      if (isCheckIn) classes += ' is-checkin';
      if (isCheckOut) classes += ' is-checkout';
      if (inRange) classes += ' is-in-range';
      if (isTodayMark) classes += ' is-today';

      html += `
        <button
          type="button"
          class="${classes}"
          data-calendar-day="${ymd}"
          ${isDisabled ? 'data-calendar-disabled="1"' : ''}
          ${status.note ? `title="${status.note.replace(/"/g, '&quot;')}"` : ''}
        >
          <span class="mnpk-calendar-day__num">${day}</span>
          <span class="mnpk-calendar-day__mark">${status.mark}</span>
        </button>
      `;
    }

    html += '</div>';
    mountEl.innerHTML = html;
  }

  function renderCalendar() {
    if (!calendarMonthEls.length) return;

    if (!state.monthCursor) {
      state.monthCursor = startOfMonth(getCalendarBaseDate());
    }

    calendarMonthEls.forEach((el, index) => {
      const monthDate = new Date(
        state.monthCursor.getFullYear(),
        state.monthCursor.getMonth() + index,
        1,
      );
      renderMonth(el, monthDate);
    });

    renderDateLabels();

    document.querySelectorAll('[data-calendar-day]').forEach((button) => {
      if (button.dataset.calendarDisabled === '1') return;
      button.addEventListener('click', () => handleDatePick(button.dataset.calendarDay));
    });
  }

  function openDateModal() {
    clearBookingError();

    if (!state.monthCursor) {
      state.monthCursor = startOfMonth(getCalendarBaseDate());
    }

    renderCalendar();

    if (!normalizedEvents.length && !cfg.openStartDate) {
      setCalendarError(
        '営業開始日または空き状況イベントが未設定です。管理画面の「営業開始日・空き状況」を確認してください。',
      );
    } else {
      setCalendarError('');
    }

    openModal(dateModal);
  }

  function syncGuestRows() {
    document.querySelectorAll('.mnpk-guest-row').forEach((row) => {
      const type = row.dataset.guestType;
      const valueEl = row.querySelector('[data-guest-value]');
      if (valueEl) {
        valueEl.textContent = String(guestDraft[type] || 0);
      }
    });

    const total = guestDraft.adults + guestDraft.children;
    if (guestHelp) {
      guestHelp.textContent = `合計 ${total}名（最大 ${cfg.capacity}名）`;
    }
  }

  function openGuestModal() {
    clearBookingError();
    guestDraft.adults = state.adults;
    guestDraft.children = state.children;
    syncGuestRows();
    openModal(guestModal);
  }

  function calculateBooking() {
    const result = {
      valid: false,
      message: '',
      nights: 0,
      roomFee: 0,
      guestFee: 0,
      cleaningFee: 0,
      total: 0,
    };

    if (!state.checkIn || !state.checkOut) {
      result.message = 'チェックイン・チェックアウトを選択してください。';
      return result;
    }

    const nights = diffNights(state.checkIn, state.checkOut);
    result.nights = nights;

    if (nights <= 0) {
      result.message = 'チェックアウトはチェックインより後の日付を選択してください。';
      return result;
    }

    if (nights < cfg.minNights) {
      result.message = `この施設は最低 ${cfg.minNights}泊から予約できます。`;
      return result;
    }

    if (!isRangeSelectable(state.checkIn, state.checkOut)) {
      result.message = '選択した日程に予約不可日があります。';
      return result;
    }

    const guests = totalGuests();
    if (guests > cfg.capacity) {
      result.message = `人数は最大 ${cfg.capacity}名までです。`;
      return result;
    }

    let roomFee = 0;
    let cursor = parseYmd(state.checkIn);

    for (let i = 0; i < nights; i++) {
      const day = cursor.getDay();
      const isWeekend = day === 5 || day === 6;
      roomFee += isWeekend ? cfg.weekendPrice : cfg.nightlyPrice;
      cursor = addDays(cursor, 1);
    }

    const extraGuests = Math.max(0, guests - cfg.baseGuests);
    const guestFee = extraGuests * cfg.extraGuestFee * nights;
    const cleaningFee = cfg.cleaningFee;
    const total = roomFee + guestFee + cleaningFee;

    result.valid = true;
    result.roomFee = roomFee;
    result.guestFee = guestFee;
    result.cleaningFee = cleaningFee;
    result.total = total;

    return result;
  }

  function renderBookingCard() {
    if (selectionDates) {
      selectionDates.textContent =
        state.checkIn && state.checkOut
          ? `${formatDateJa(state.checkIn)} 〜 ${formatDateJa(state.checkOut)}`
          : '日付を選択';
    }

    if (selectionGuests) {
      const guestsText = [`${state.adults + state.children}名`];
      if (state.children > 0) {
        guestsText.push(`子ども ${state.children}名`);
      }
      selectionGuests.textContent = guestsText.join(' / ');
    }

    const calc = calculateBooking();

    if (!calc.valid) {
      if (priceRoom) priceRoom.textContent = '—';
      if (priceGuest) priceGuest.textContent = cfg.extraGuestFee > 0 ? '—' : '0円';
      if (priceCleaning)
        priceCleaning.textContent = cfg.cleaningFee > 0 ? yen(cfg.cleaningFee) : '0円';
      if (priceTotal) priceTotal.textContent = '—';
      clearBookingError();
      return;
    }

    if (priceRoom) priceRoom.textContent = `${yen(calc.roomFee)} / ${calc.nights}泊`;
    if (priceGuest) priceGuest.textContent = calc.guestFee > 0 ? yen(calc.guestFee) : '0円';
    if (priceCleaning)
      priceCleaning.textContent = calc.cleaningFee > 0 ? yen(calc.cleaningFee) : '0円';
    if (priceTotal) priceTotal.textContent = yen(calc.total);
    clearBookingError();
  }

  function renderCheckoutSummary() {
    const calc = calculateBooking();
    if (!calc.valid) {
      setBookingError(calc.message);
      return false;
    }

    const guests = totalGuests();
    const datesText = `${formatDateJa(state.checkIn)} ${cfg.checkinTime} チェックイン / ${formatDateJa(state.checkOut)} ${cfg.checkoutTime} チェックアウト`;
    const guestsText = `${guests}名`;

    allCheckoutDates.forEach((el) => (el.textContent = datesText));
    allCheckoutGuests.forEach((el) => (el.textContent = guestsText));
    allCheckoutRoomFee.forEach((el) => (el.textContent = yen(calc.roomFee)));
    allCheckoutGuestFee.forEach(
      (el) => (el.textContent = calc.guestFee > 0 ? yen(calc.guestFee) : '0円'),
    );
    allCheckoutCleaningFee.forEach(
      (el) => (el.textContent = calc.cleaningFee > 0 ? yen(calc.cleaningFee) : '0円'),
    );
    allCheckoutTotal.forEach((el) => (el.textContent = yen(calc.total)));
    allCheckoutStayTitle.forEach((el) => (el.textContent = cfg.stayTitle || '宿泊先'));
    allCheckoutStayMeta.forEach((el) => (el.textContent = cfg.stayMeta || ''));
    allCheckoutThumb.forEach((el) => {
      if (cfg.leadImage) {
        el.src = cfg.leadImage;
        el.alt = cfg.stayTitle || '宿泊先画像';
      }
    });

    if (paymentTotalLabel) {
      paymentTotalLabel.textContent = yen(calc.total);
    }

    clearBookingError();
    return true;
  }

  // NAIGAI_STRIPE_TARGET_PRESERVE_FIX_20260730
  function resetPaymentElement() {
    if (paymentElementInstance && typeof paymentElementInstance.destroy === 'function') {
      paymentElementInstance.destroy();
    }
    if (paymentElementWrap) {
      let paymentElementTarget =
        paymentElementWrap.querySelector('#mnpk-payment-element');

      if (!paymentElementTarget) {
        paymentElementTarget = document.createElement('div');
        paymentElementTarget.id = 'mnpk-payment-element';
        paymentElementTarget.className = 'mnpk-payment-element';
        paymentElementWrap.appendChild(paymentElementTarget);
      } else {
        paymentElementTarget.innerHTML = '';
      }
    }
    paymentElementInstance = null;
    elementsInstance = null;
    currentClientSecret = '';
    paymentElementReady = false;
    setPaymentError('');
  }

  async function createPaymentIntent() {
    const calc = calculateBooking();

    if (!calc.valid) {
      throw new Error(calc.message || '予約内容を確認してください。');
    }

    if (!window.mnpkBooking || !window.mnpkBooking.ajaxUrl) {
      throw new Error('JavaScript の設定値が不足しています。localize 設定を確認してください。');
    }

    const formData = new URLSearchParams();
    formData.append('action', 'mnpk_create_payment_intent');
    formData.append('nonce', window.mnpkBooking.nonce || '');
    formData.append('post_id', String(cfg.postId));
    formData.append('check_in', state.checkIn);
    formData.append('check_out', state.checkOut);
    formData.append('adults', String(state.adults));
    formData.append('children', String(state.children));
    formData.append('name', paymentNameInput ? paymentNameInput.value.trim() : '');
    formData.append('email', paymentEmailInput ? paymentEmailInput.value.trim() : '');

    /*
     * Stripe PaymentIntent 作成AJAX。
     *
     * credentials: 'same-origin'
     *   WordPressのログイン/セッションCookieを
     *   同一オリジンのadmin-ajax.phpへ送る。
     *
     * cache: 'no-store'
     *   決済用レスポンスをブラウザキャッシュへ残さない。
     *
     * ajaxUrl自体も /wp-admin/admin-ajax.php の
     * 相対URLにしているため、localhost / 127.0.0.1 が
     * 混在しても別オリジンへ送信しない。
     */
    const response = await fetch(window.mnpkBooking.ajaxUrl, {
      method: 'POST',
      credentials: 'same-origin',
      cache: 'no-store',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
      },
      body: formData.toString(),
    });

    const result = await response.json();

    if (!result || !result.success) {
      throw new Error(result?.data?.message || 'PaymentIntent の作成に失敗しました。');
    }

    if (!result.data || !result.data.client_secret) {
      throw new Error('client_secret が返ってきませんでした。');
    }

    return {
      clientSecret: result.data.client_secret,
      amount: result.data.amount || calc.total,
    };
  }

  function bindPaymentElementReadyObserver() {
    if (!paymentElementWrap) return;

    const markReadyIfMounted = () => {
      const mountedNode =
        paymentElementWrap.querySelector('iframe, .__PrivateStripeElement') ||
        paymentElementWrap.children.length > 0;

      if (mountedNode) {
        setPaymentSkeletonState(true);
        return true;
      }
      return false;
    };

    if (markReadyIfMounted()) return;

    const observer = new MutationObserver(() => {
      if (markReadyIfMounted()) {
        observer.disconnect();
      }
    });

    observer.observe(paymentElementWrap, { childList: true, subtree: true });

    window.setTimeout(() => {
      markReadyIfMounted();
      observer.disconnect();
    }, 5000);
  }

  function setPaymentSkeletonState(isReady) {
    if (!paymentElementFrameWrap) return;

    paymentElementFrameWrap.classList.toggle('is-loading', !isReady);
    paymentElementFrameWrap.classList.toggle('is-ready', !!isReady);

    if (paymentElementSkeleton) {
      paymentElementSkeleton.hidden = !!isReady;
    }

    if (paymentElementWrap) {
      paymentElementWrap.setAttribute('aria-busy', isReady ? 'false' : 'true');
    }
  }
  async function mountPaymentElement() {
    /**
     * MINPAKU_PAYMENT_FRONTEND_GUARD
     * =====================================================
     * 本番でオンライン決済を使用しない場合の表示制御。
     *
     * 料金計算・日程・人数などの予約UIはそのまま利用する。
     *
     * Stripe決済だけを停止し、
     * PaymentIntent作成やPayment Element生成へ進ませない。
     *
     * PHP側にも同じガードがあるため、
     * これはセキュリティ処理ではなく利用者向けUI制御。
     * =====================================================
     */
    /*
     * wp_localize_script()では値が文字列化されることがある。
     *
     * "1"だけを決済ONとして扱い、
     * "0" / 空文字 / false / 未定義は安全側のOFFにする。
     */
    const paymentFeatureEnabled =
      window.mnpkBooking &&
      String(window.mnpkBooking.paymentEnabled) === '1';

    if (!paymentFeatureEnabled) {

      if (typeof setPaymentSkeletonState === 'function') {
        setPaymentSkeletonState(true);
      }

      if (paymentElementWrap) {
        paymentElementWrap.innerHTML = `
          <div class="mnpk-payment-disabled-notice">
            <strong>オンライン決済は現在準備中です。</strong>
            <p>
              宿泊料金や予約内容は確認できます。
              オンラインでのお支払いは現在受け付けていません。
            </p>
          </div>
        `;
      }

      if (paymentSubmitButton) {
        paymentSubmitButton.disabled = true;
        paymentSubmitButton.textContent =
          'オンライン決済準備中';
      }

      setPaymentError('');

      return false;
    }
    /*
     * 同じ時間に複数のマウント処理を走らせない。
     * すでに処理中なら、その完了を待つ。
     */
    if (paymentElementMountPromise) {
      return paymentElementMountPromise;
    }

    paymentElementMountPromise = (async () => {
      const calc = calculateBooking();

      if (!calc.valid) {
        setBookingError(calc.message || '予約内容を確認してください。');
        return false;
      }

      if (!paymentForm || !paymentElementWrap) {
        setPaymentError('支払いフォームの表示場所が見つかりません。');
        return false;
      }

      if (!window.mnpkBooking || !window.mnpkBooking.publishableKey) {
        setPaymentError('Stripe の公開可能キーが未設定です。');
        return false;
      }

      if (typeof window.Stripe !== 'function') {
        setPaymentError('Stripe.js が読み込まれていません。');
        return false;
      }

      setPaymentDebugStatus('PaymentIntent 作成開始');
      setPaymentSkeletonState(false);
      resetPaymentElement();

      try {
        const { clientSecret } = await createPaymentIntent();

        setPaymentDebugStatus('client_secret 取得');
        currentClientSecret = clientSecret;

        stripeInstance = window.Stripe(
          window.mnpkBooking.publishableKey
        );

        elementsInstance = stripeInstance.elements({
          clientSecret: currentClientSecret,
          appearance: {
            theme: 'stripe',
          },
        });

        paymentElementInstance = elementsInstance.create(
          'payment',
          {
            layout: {
              type: 'accordion',
              defaultCollapsed: false,
              radios: true,
              spacedAccordionItems: false,
            },
            fields: {
              billingDetails: {
                name: 'never',
                email: 'never',
                phone: 'auto',
                address: 'if_required',
              },
            },
            wallets: {
              applePay: 'never',
              googlePay: 'never',
              link: 'never',
            },
          }
        );

        paymentElementReady = false;

        /*
         * Stripeのreadyイベントが発生するまで、
         * mountPaymentElementを完了扱いにしない。
         */
        const readyPromise = new Promise((resolve, reject) => {
          let settled = false;

          const timeout = window.setTimeout(() => {
            if (settled) return;

            settled = true;

            reject(
              new Error(
                'Stripeの支払いフォームの準備が完了しませんでした。'
              )
            );
          }, 15000);

          paymentElementInstance.on('loaderstart', () => {
            setPaymentSkeletonState(false);
            setPaymentDebugStatus('Stripe loading');
          });

          paymentElementInstance.on('ready', () => {
            if (settled) return;

            settled = true;
            paymentElementReady = true;

            window.clearTimeout(timeout);

            setPaymentSkeletonState(true);
            setPaymentDebugStatus('Stripe ready');

            resolve(true);
          });
        });

        setPaymentDebugStatus('Stripe mount 実行');

        /*
         * 文字列セレクターではなく、
         * 取得済みの実際のDOM要素へマウントする。
         */
        const paymentElementTarget =
          paymentElementWrap.querySelector('#mnpk-payment-element');

        if (!paymentElementTarget) {
          throw new Error('Stripeの設置先が見つかりません。');
        }

        paymentElementInstance.mount(paymentElementTarget);

        bindPaymentElementReadyObserver();

        await readyPromise;

        return true;
      } catch (error) {
        console.error('[mnpk mountPaymentElement]', error);

        paymentElementReady = false;

        setPaymentSkeletonState(true);
        setPaymentDebugStatus(
          '失敗: '
            + (
              error.message
              || '支払いフォームの準備に失敗しました。'
            ),
          true
        );

        setPaymentError(
          error.message
          || '支払いフォームの準備に失敗しました。'
        );

        return false;
      }
    })();

    try {
      return await paymentElementMountPromise;
    } finally {
      paymentElementMountPromise = null;
    }
  }

  function buildCheckoutUrl() {
    const url = new URL(cfg.checkoutUrl || cfg.detailUrl, window.location.origin);
    url.searchParams.set('checkin', state.checkIn);
    url.searchParams.set('checkout', state.checkOut);
    url.searchParams.set('adults', String(state.adults));
    url.searchParams.set('children', String(state.children));
    return url.toString();
  }

  async function openMobilePaymentModal() {
    setPaymentDebugStatus('checkout init 開始');
    const ok = renderCheckoutSummary();
    if (!ok) return;
    openModal(paymentModal);
    await mountPaymentElement();
  }

  async function initCheckoutPaymentOnLoad() {
    const checkoutPaymentSection = document.querySelector('.mnpk-checkout-payment');
    if (!checkoutPaymentSection) return;
    if (!paymentForm || !paymentElementWrap) return;

    setPaymentDebugStatus('checkout init 開始');
    const ok = renderCheckoutSummary();
    if (!ok) {
      console.warn('[mnpk] renderCheckoutSummary returned false on checkout init');
      setPaymentDebugStatus('summary NG', true);
      return;
    }

    await mountPaymentElement();
  }

  window.addEventListener('load', () => {
    initCheckoutPaymentOnLoad().catch((error) => {
      console.error(error);
      setPaymentSkeletonState(true);
      setPaymentError('支払いフォームの準備に失敗しました。');
    });
  });

  async function handleBookingSubmit(event) {
    if (event) event.preventDefault();

    const calc = calculateBooking();
    if (!calc.valid) {
      setBookingError(calc.message || '予約内容を確認してください。');
      if (!state.checkIn || !state.checkOut) {
        openDateModal();
      }
      return;
    }

    if (isMobile() && !isCheckoutPage) {
      await openMobilePaymentModal();
      return;
    }

    if (!cfg.checkoutUrl) {
      setBookingError('checkout URL が未設定です。');
      return;
    }

    window.location.href = buildCheckoutUrl();
  }

  bookingCard.querySelector('[data-open-dates]')?.addEventListener('click', openDateModal);
  bookingCard.querySelector('[data-open-guests]')?.addEventListener('click', openGuestModal);

  document.querySelector('[data-calendar-prev]')?.addEventListener('click', () => {
    state.monthCursor = new Date(
      state.monthCursor.getFullYear(),
      state.monthCursor.getMonth() - 1,
      1,
    );
    renderCalendar();
  });

  document.querySelector('[data-calendar-next]')?.addEventListener('click', () => {
    state.monthCursor = new Date(
      state.monthCursor.getFullYear(),
      state.monthCursor.getMonth() + 1,
      1,
    );
    renderCalendar();
  });

  document.querySelector('[data-calendar-clear]')?.addEventListener('click', () => {
    state.checkIn = '';
    state.checkOut = '';
    if (checkinInput) checkinInput.value = '';
    if (checkoutInput) checkoutInput.value = '';
    clearAllErrors();
    renderCalendar();
    renderBookingCard();
    renderCheckoutSummary();
  });

  document.querySelector('[data-apply-dates]')?.addEventListener('click', async () => {
    if (!state.checkIn || !state.checkOut) {
      setCalendarError('チェックイン・チェックアウトを選択してください。');
      return;
    }

    if (!isRangeSelectable(state.checkIn, state.checkOut)) {
      setCalendarError(
        `選択した範囲に予約不可日があります。最低 ${cfg.minNights}泊以上で、空き日だけを選択してください。`,
      );
      return;
    }

    if (checkinInput) checkinInput.value = state.checkIn;
    if (checkoutInput) checkoutInput.value = state.checkOut;

    closeModal(dateModal);
    renderBookingCard();
    renderCheckoutSummary();

    if (reopenPaymentAfterPicker === 'dates') {
      reopenPaymentAfterPicker = '';
      await openMobilePaymentModal();
    }
  });

  document.querySelectorAll('.mnpk-guest-row').forEach((row) => {
    const type = row.dataset.guestType;

    row.querySelectorAll('[data-guest-action]').forEach((button) => {
      button.addEventListener('click', () => {
        const action = button.dataset.guestAction;
        const currentTotal = guestDraft.adults + guestDraft.children;

        if (action === 'minus') {
          if (type === 'adults' && guestDraft.adults > 1) guestDraft.adults -= 1;
          if (type === 'children' && guestDraft.children > 0) guestDraft.children -= 1;
        }

        if (action === 'plus') {
          if (currentTotal >= cfg.capacity) return;
          if (type === 'adults') guestDraft.adults += 1;
          if (type === 'children') guestDraft.children += 1;
        }

        syncGuestRows();
      });
    });
  });

  document.querySelector('[data-apply-guests]')?.addEventListener('click', async () => {
    state.adults = guestDraft.adults;
    state.children = guestDraft.children;
    closeModal(guestModal);
    renderBookingCard();
    renderCheckoutSummary();

    if (reopenPaymentAfterPicker === 'guests') {
      reopenPaymentAfterPicker = '';
      await openMobilePaymentModal();
    }
  });

  bookingCard
    .querySelector('[data-booking-submit]')
    ?.addEventListener('click', handleBookingSubmit);
  mobileOpenButtons.forEach((button) => button.addEventListener('click', handleBookingSubmit));

  document.querySelectorAll('[data-checkout-edit]').forEach((button) => {
    button.addEventListener('click', () => {
      const type = button.dataset.checkoutEdit || '';

      if (isMobile() && paymentModal?.classList.contains('is-open')) {
        closeModal(paymentModal);
        reopenPaymentAfterPicker = type;
      }

      if (type === 'dates') {
        openDateModal();
      }
      if (type === 'guests') {
        openGuestModal();
      }
    });
  });

  if (paymentForm) {
    paymentForm.addEventListener('submit', async (event) => {
      event.preventDefault();

      if (!paymentNameInput?.value.trim()) {
        setPaymentError('お名前を入力してください。');
        paymentNameInput?.focus();
        return;
      }

      if (!paymentEmailInput?.value.trim()) {
        setPaymentError('メールアドレスを入力してください。');
        paymentEmailInput?.focus();
        return;
      }

      const summaryOk = renderCheckoutSummary();
      if (!summaryOk) return;

      /*
       * オブジェクトの存在だけでなく、
       * Stripeのready完了も確認する。
       */
      // NAIGAI_STRIPE_SUBMIT_READY_FIX_20260730
      // NAIGAI_STRIPE_MOUNTED_ELEMENT_FIX_20260730
      const hasMountedPaymentElement = () => {
        if (!paymentElementWrap) return false;

        return Boolean(
          paymentElementWrap.querySelector(
            'iframe, .__PrivateStripeElement, [data-testid="payment-element"]'
          )
        );
      };

      if (
        !stripeInstance
        || !elementsInstance
        || !paymentElementInstance
        || !hasMountedPaymentElement()
      ) {
        setPaymentDebugStatus('Payment Elementを再作成しています');

        const mounted = await mountPaymentElement();

        if (
          !mounted
          || !stripeInstance
          || !elementsInstance
          || !paymentElementInstance
        ) {
          setPaymentError(
            'Stripeの支払いフォームを再作成できませんでした。'
          );
          return;
        }

        /*
         * Stripeのreadyイベントまたはiframe生成を待つ。
         * 変数が存在するだけではconfirmPaymentを実行しない。
         */
        const mountedDeadline = Date.now() + 10000;

        while (!hasMountedPaymentElement() && Date.now() < mountedDeadline) {
          await new Promise((resolve) => window.setTimeout(resolve, 100));
        }

        if (!hasMountedPaymentElement()) {
          setPaymentError(
            'Stripeの支払いフォームを表示できませんでした。'
          );
          return;
        }
      }

      setPaymentError('');

      try {
        /*
         * Stripe公式の手順。
         * confirmPayment前に入力検証とウォレット情報収集を行う。
         */
        if (typeof elementsInstance.submit === 'function') {
          const submitResult = await elementsInstance.submit();

          if (submitResult?.error) {
            setPaymentDebugStatus(
              'elements.submit error: '
                + (submitResult.error.message || '入力内容を確認してください。'),
              true
            );
            setPaymentError(
              submitResult.error.message || 'カード情報を確認してください。'
            );
            return;
          }
        }

        if (!hasMountedPaymentElement()) {
          setPaymentError(
            '決済フォームがページから外れました。ページを再読み込みしてください。'
          );
          return;
        }

        const result = await stripeInstance.confirmPayment({
          elements: elementsInstance,
          confirmParams: {
            payment_method_data: {
              billing_details: {
                name: paymentNameInput.value.trim(),
                email: paymentEmailInput.value.trim(),
              },
            },
          },
          redirect: 'if_required',
        });

        if (result.error) {
          console.error('[mnpk confirmPayment error]', result.error);

          const debugMessage = [
            result.error.type || '',
            result.error.code || '',
            result.error.decline_code || '',
            result.error.message || '決済に失敗しました。'
          ].filter(Boolean).join(' | ');

          setPaymentDebugStatus('confirm error: ' + debugMessage, true);
          setPaymentError(result.error.message || '決済に失敗しました。');
          return;
        }

        if (
          result.paymentIntent &&
          result.paymentIntent.status === 'succeeded'
        ) {
          /*
           * checkout専用ページでは、
           * alertを出して終わるのではなく
           * ページ全体を共通サンクスへ切り替える。
           */
          if (isCheckoutPage) {
            showMinpakuCheckoutThanks(
              result.paymentIntent
            );

            return;
          }

          /*
           * 詳細ページ上の既存支払いモーダルは
           * 現時点では従来の完了動作を維持する。
           */
          alert('お支払いが完了しました。');
          closeModal(paymentModal);

          return;
        }

        setPaymentError('決済ステータスを確認できませんでした。');
      } catch (error) {
        console.error('[mnpk confirmPayment catch]', error);
        setPaymentDebugStatus('confirm catch: ' + (error.message || '決済処理に失敗しました。'), true);
        setPaymentError(error.message || '決済処理に失敗しました。');
      }
    });
  }

  if (window.location.hash === '#mnpk-booking-card') {
    bookingCard.scrollIntoView({ behavior: 'smooth', block: 'start' });
    if (isMobile()) {
      setTimeout(() => {
        if (!state.checkIn || !state.checkOut) {
          openDateModal();
        }
      }, 250);
    }
  }

  const panoramaButtons = Array.from(document.querySelectorAll('.mnpk-panorama-thumb'));
  const panoramaTitle = document.querySelector('[data-panorama-title]');
  const panoramaText = document.querySelector('[data-panorama-text]');
  const panoramaViewer = document.getElementById('mnpk-panorama-viewer');

  if (panoramaViewer && panoramaButtons.length > 0) {
    const panoItems = panoramaButtons.map((button, index) => ({
      index,
      url: button.dataset.panoramaUrl || '',
      title: button.dataset.panoramaTitle || `パノラマ ${index + 1}`,
      text: button.dataset.panoramaText || '',
      button,
    }));

    function setActivePanoramaButton(index) {
      panoItems.forEach((item) => item.button.classList.toggle('is-active', item.index === index));
    }

    function setPanoramaMeta(item) {
      if (panoramaTitle) panoramaTitle.textContent = item.title || '';
      if (panoramaText) panoramaText.textContent = item.text || '';
    }

    function showPanorama(item) {
      setActivePanoramaButton(item.index);
      setPanoramaMeta(item);

      if (!window.pannellum || !item.url) return;

      try {
        panoramaViewer.innerHTML = '';
        window.pannellum.viewer('mnpk-panorama-viewer', {
          type: 'equirectangular',
          panorama: item.url,
          autoLoad: true,
          showControls: false,
        });
      } catch (error) {
        console.error('pannellum error:', error);
      }
    }

    showPanorama(panoItems[0]);
    panoItems.forEach((item) => item.button.addEventListener('click', () => showPanorama(item)));
  }

  renderBookingCard();
  renderCheckoutSummary();
  renderDateLabels();
  syncGuestRows();

  if (isCheckoutPage && paymentForm) {
    mountPaymentElement();
  }
});
