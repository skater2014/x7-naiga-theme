/**
 * =========================================================
 * minpaku-archive-booking.js
 * /minpaku-stay アーカイブ一覧ページ専用JS
 * =========================================================
 *
 * 読み込み場所:
 * - minpaku/inc/enqueue.php
 * - naigai_minpaku_enqueue_assets()
 * - is_post_type_archive('minpaku') の時に読み込む
 *
 * 対象ページ:
 * - archive-minpaku.php
 * - URL例: /minpaku-stay/
 *
 * 対象HTML:
 * - archive-minpaku.php 285行目付近
 *   #mnpk-archive-booking-modal
 *
 * 主な役割:
 * 1. 一覧カードの「日付を選ぶ」ボタンを拾う
 *    - data-archive-booking-open
 * 2. クリックされたカードの data-* から料金・定員・空き状況を読む
 * 3. #mnpk-archive-booking-modal を開く
 * 4. アーカイブ用カレンダーを描画する
 * 5. チェックイン / チェックアウトを選択する
 * 6. 選択済みの日付をモーダル内に表示する
 * 7. 「この日程で確認とお支払いへ」クリックで checkout URL へ遷移する
 *
 * 重要:
 * - 詳細ページの #mnpk-date-modal はこのJSでは動かさない
 * - 詳細ページは minpaku/common/js/minpaku-single.js が担当
 * - アーカイブHTMLのIDは #mnpk-archive-booking-modal のままにする
 * - data-archive-* 属性はアーカイブJSが使うので消さない
 *
 * 関連CSS:
 * - minpaku/common/css/minpaku-booking.css
 *
 * 関連HTML:
 * - アーカイブモーダル: #mnpk-archive-booking-modal
 * - 開くボタン: [data-archive-booking-open]
 * - 前月ボタン: [data-archive-calendar-prev]
 * - 次月ボタン: [data-archive-calendar-next]
 * - 決定ボタン: [data-archive-booking-submit]
 * =========================================================
 */
document.addEventListener('DOMContentLoaded', function () {
  const modal = document.getElementById('mnpk-archive-booking-modal');
  if (!modal) return;

  const titleEl = modal.querySelector('[data-archive-booking-title]');
  const checkinLabel = modal.querySelector('[data-archive-checkin-label]');
  const checkoutLabel = modal.querySelector('[data-archive-checkout-label]');
  const helpEl = modal.querySelector('[data-archive-calendar-help]');
  const errorEl = modal.querySelector('[data-archive-booking-error]');
  const monthEls = modal.querySelectorAll('[data-archive-calendar-month]');
  const guestValueEl = modal.querySelector('[data-archive-guest-value]');
  const guestHelpEl = modal.querySelector('[data-archive-guest-help]');
  const priceRoomEl = modal.querySelector('[data-archive-price-room]');
  const priceGuestEl = modal.querySelector('[data-archive-price-guest]');
  const priceCleaningEl = modal.querySelector('[data-archive-price-cleaning]');
  const priceTotalEl = modal.querySelector('[data-archive-price-total]');
  const submitBtn = modal.querySelector('[data-archive-booking-submit]');

  let cfg = null;
  let state = {
    checkIn: '',
    checkOut: '',
    adults: 1,
    monthCursor: startOfMonth(new Date()),
  };

  function yen(value) {
    const num = Number(value || 0);
    return num.toLocaleString('ja-JP', {
      style: 'currency',
      currency: 'JPY',
      maximumFractionDigits: 0,
    });
  }

  function parseJson(raw, fallback) {
    if (!raw) return fallback;
    try {
      const parsed = JSON.parse(raw);
      return Array.isArray(parsed) ? parsed : fallback;
    } catch (error) {
      console.warn('[mnpk archive booking] calendar json parse failed', error);
      return fallback;
    }
  }

  function toInt(value, fallback) {
    const parsed = parseInt(value, 10);
    return Number.isNaN(parsed) ? fallback : parsed;
  }

  function toFloat(value, fallback) {
    const parsed = parseFloat(value);
    return Number.isNaN(parsed) ? fallback : parsed;
  }

  function pad(num) {
    return String(num).padStart(2, '0');
  }

  function toYmd(date) {
    return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}`;
  }

  function parseYmd(ymd) {
    if (!ymd) return null;
    const parts = String(ymd).split('-').map(Number);
    if (parts.length !== 3 || parts.some(Number.isNaN)) return null;
    return new Date(parts[0], parts[1] - 1, parts[2]);
  }

  function startOfMonth(date) {
    return new Date(date.getFullYear(), date.getMonth(), 1);
  }

  function addDays(date, days) {
    const next = new Date(date);
    next.setDate(next.getDate() + days);
    return next;
  }

  function daysBetween(startYmd, endYmd) {
    const start = parseYmd(startYmd);
    const end = parseYmd(endYmd);
    if (!start || !end) return 0;
    return Math.round((end - start) / 86400000);
  }

  function normalizeEventDate(value) {
    if (!value) return '';
    return String(value).slice(0, 10);
  }

  function findEvent(ymd) {
    if (!cfg || !Array.isArray(cfg.calendarEvents)) return null;

    return cfg.calendarEvents.find((event) => {
      const start = normalizeEventDate(event.start || event.date || event.from);
      const end = normalizeEventDate(event.end || event.to || event.start || event.date);

      if (!start) return false;

      if (!end || end === start) {
        return ymd === start;
      }

      return ymd >= start && ymd <= end;
    }) || null;
  }

  function getDateStatus(ymd) {
    const today = toYmd(new Date());
    if (ymd < today) {
      return { status: 'blocked', note: '過去日は選択できません。' };
    }

    if (cfg.openStartDate && ymd < cfg.openStartDate) {
      return { status: 'blocked', note: '営業開始前です。' };
    }

    const event = findEvent(ymd);
    if (!event) {
      return { status: 'available', note: '予約可能' };
    }

    const status = String(event.status || 'blocked').toLowerCase();
    if (['available', 'open', 'vacant', 'ok', '○', '〇'].includes(status)) {
      return { status: 'available', note: event.note || '予約可能' };
    }

    return { status: 'blocked', note: event.note || '停止中' };
  }

  function isAvailable(ymd) {
    return getDateStatus(ymd).status === 'available';
  }

  function setError(message) {
    if (!errorEl) return;
    if (!message) {
      errorEl.hidden = true;
      errorEl.textContent = '';
      return;
    }
    errorEl.hidden = false;
    errorEl.textContent = message;
  }

  function openModal() {
    modal.classList.add('is-open');
    modal.setAttribute('aria-hidden', 'false');
    document.body.classList.add('mnpk-modal-open');
  }

  function closeModal() {
    modal.classList.remove('is-open');
    modal.setAttribute('aria-hidden', 'true');
    document.body.classList.remove('mnpk-modal-open');
  }

  function selectDay(ymd) {
    setError('');

    if (!isAvailable(ymd)) {
      setError(getDateStatus(ymd).note || 'この日は選択できません。');
      return;
    }

    if (!state.checkIn || (state.checkIn && state.checkOut)) {
      state.checkIn = ymd;
      state.checkOut = '';
    } else {
      if (ymd <= state.checkIn) {
        state.checkIn = ymd;
        state.checkOut = '';
      } else {
        const nights = daysBetween(state.checkIn, ymd);
        if (nights < cfg.minNights) {
          setError(`最低 ${cfg.minNights}泊から選択できます。`);
          return;
        }

        let cursor = parseYmd(state.checkIn);
        while (cursor && toYmd(cursor) < ymd) {
          if (!isAvailable(toYmd(cursor))) {
            setError('選択範囲に予約できない日が含まれています。');
            return;
          }
          cursor = addDays(cursor, 1);
        }

        state.checkOut = ymd;
      }
    }

    renderAll();
  }

  function renderMonth(target, monthDate) {
    if (!target) return;

    const year = monthDate.getFullYear();
    const month = monthDate.getMonth();
    const first = new Date(year, month, 1);
    const last = new Date(year, month + 1, 0);
    const startDay = first.getDay();

    let html = `<div class="mnpk-calendar-month__title">${year}年 ${month + 1}月</div>`;
    html += '<div class="mnpk-calendar-grid">';
    ['日', '月', '火', '水', '木', '金', '土'].forEach((d) => {
      html += `<span class="mnpk-calendar-weekday">${d}</span>`;
    });

    for (let i = 0; i < startDay; i += 1) {
      html += '<span class="mnpk-calendar-empty"></span>';
    }

    for (let day = 1; day <= last.getDate(); day += 1) {
      const date = new Date(year, month, day);
      const ymd = toYmd(date);
      const status = getDateStatus(ymd);
      const disabled = status.status !== 'available';
      const isCheckIn = state.checkIn === ymd;
      const isCheckOut = state.checkOut === ymd;
      const inRange = state.checkIn && state.checkOut && ymd > state.checkIn && ymd < state.checkOut;

      let classes = 'mnpk-calendar-day';
      if (disabled) classes += ' is-disabled';
      if (isCheckIn) classes += ' is-checkin';
      if (isCheckOut) classes += ' is-checkout';
      if (inRange) classes += ' is-in-range';

      html += `
        <button
          type="button"
          class="${classes}"
          data-archive-calendar-day="${ymd}"
          ${disabled ? 'disabled' : ''}
          title="${status.note || ''}"
        >
          <span>${day}</span>
        </button>
      `;
    }

    html += '</div>';
    target.innerHTML = html;
  }

  function calculate() {
    if (!cfg || !state.checkIn || !state.checkOut) {
      return {
        valid: false,
        message: 'チェックイン日とチェックアウト日を選択してください。',
        nights: 0,
        roomFee: 0,
        guestFee: 0,
        cleaningFee: cfg ? cfg.cleaningFee : 0,
        total: 0,
      };
    }

    const nights = daysBetween(state.checkIn, state.checkOut);
    if (nights < cfg.minNights) {
      return {
        valid: false,
        message: `最低 ${cfg.minNights}泊から選択できます。`,
        nights,
        roomFee: 0,
        guestFee: 0,
        cleaningFee: cfg.cleaningFee,
        total: 0,
      };
    }

    let roomFee = 0;
    let cursor = parseYmd(state.checkIn);

    while (cursor && toYmd(cursor) < state.checkOut) {
      const day = cursor.getDay();
      const isWeekend = day === 5 || day === 6;
      roomFee += isWeekend ? cfg.weekendPrice : cfg.nightlyPrice;
      cursor = addDays(cursor, 1);
    }

    const extraGuests = Math.max(0, state.adults - cfg.baseGuests);
    const guestFee = extraGuests * cfg.extraGuestFee * nights;
    const cleaningFee = cfg.cleaningFee;
    const total = roomFee + guestFee + cleaningFee;

    return {
      valid: true,
      message: '',
      nights,
      roomFee,
      guestFee,
      cleaningFee,
      total,
    };
  }

  function updateSummary() {
    const calc = calculate();

    if (checkinLabel) checkinLabel.textContent = state.checkIn || '未選択';
    if (checkoutLabel) checkoutLabel.textContent = state.checkOut || '未選択';
    if (guestValueEl) guestValueEl.textContent = String(state.adults);
    if (guestHelpEl) guestHelpEl.textContent = `合計 ${state.adults}名（最大 ${cfg.capacity}名）`;

    if (priceRoomEl) priceRoomEl.textContent = calc.roomFee > 0 ? yen(calc.roomFee) : '—';
    if (priceGuestEl) priceGuestEl.textContent = calc.guestFee > 0 ? yen(calc.guestFee) : '0円';
    if (priceCleaningEl) priceCleaningEl.textContent = cfg.cleaningFee > 0 ? yen(cfg.cleaningFee) : '0円';
    if (priceTotalEl) priceTotalEl.textContent = calc.total > 0 ? yen(calc.total) : '—';

    if (submitBtn) {
      submitBtn.disabled = !calc.valid;
    }
  }

  function renderAll() {
    monthEls.forEach((el, index) => {
      const date = new Date(state.monthCursor.getFullYear(), state.monthCursor.getMonth() + index, 1);
      renderMonth(el, date);
    });
    updateSummary();
  }

  function setupFromButton(button) {
    const capacity = Math.max(1, toInt(button.dataset.capacity, 1));
    const baseGuests = Math.min(capacity, Math.max(1, toInt(button.dataset.baseGuests, 1)));
    const nightlyPrice = toFloat(button.dataset.nightlyPrice, 0);
    const weekendPrice = toFloat(button.dataset.weekendPrice, nightlyPrice);

    cfg = {
      postId: toInt(button.dataset.postId, 0),
      title: button.dataset.stayTitle || '',
      detailUrl: button.dataset.detailUrl || '',
      checkoutUrl: button.dataset.checkoutUrl || '',
      nightlyPrice,
      weekendPrice: weekendPrice > 0 ? weekendPrice : nightlyPrice,
      cleaningFee: toFloat(button.dataset.cleaningFee, 0),
      capacity,
      baseGuests,
      extraGuestFee: toFloat(button.dataset.extraGuestFee, 0),
      minNights: Math.max(1, toInt(button.dataset.minNights, 1)),
      openStartDate: button.dataset.openStartDate || '',
      cleaningBufferDays: toInt(button.dataset.cleaningBufferDays, 0),
      cleaningNote: button.dataset.cleaningNote || '',
      calendarEvents: parseJson(button.dataset.calendarEvents || '[]', []),
    };

    state = {
      checkIn: '',
      checkOut: '',
      adults: 1,
      monthCursor: startOfMonth(new Date()),
    };

    if (titleEl) titleEl.textContent = cfg.title || '日付を選択';
    if (helpEl) {
      helpEl.textContent = cfg.cleaningNote || '空きのある日をチェックイン日・チェックアウト日の順に選択してください。';
    }

    setError('');
    renderAll();
  }

  function submitBooking() {
    const calc = calculate();

    if (!calc.valid) {
      setError(calc.message || '予約内容を確認してください。');
      return;
    }

    if (!cfg.checkoutUrl) {
      setError('checkout URL が未設定です。');
      return;
    }

    const url = new URL(cfg.checkoutUrl, window.location.origin);
    url.searchParams.set('checkin', state.checkIn);
    url.searchParams.set('checkout', state.checkOut);
    url.searchParams.set('adults', String(state.adults));
    url.searchParams.set('children', '0');

    window.location.href = url.toString();
  }

  document.querySelectorAll('[data-archive-booking-open]').forEach((button) => {
    button.addEventListener('click', () => {
      setupFromButton(button);
      openModal();
    });
  });

  modal.addEventListener('click', (event) => {
    const close = event.target.closest('[data-archive-booking-close]');
    if (close) {
      event.preventDefault();
      closeModal();
      return;
    }

    const dayButton = event.target.closest('[data-archive-calendar-day]');
    if (dayButton) {
      event.preventDefault();
      selectDay(dayButton.dataset.archiveCalendarDay);
      return;
    }

    const guestAction = event.target.closest('[data-archive-guest-action]');
    if (guestAction) {
      event.preventDefault();
      const action = guestAction.dataset.archiveGuestAction;

      if (action === 'minus' && state.adults > 1) {
        state.adults -= 1;
      }

      if (action === 'plus' && state.adults < cfg.capacity) {
        state.adults += 1;
      }

      updateSummary();
      return;
    }

    if (event.target.closest('[data-archive-calendar-prev]')) {
      event.preventDefault();
      state.monthCursor = new Date(state.monthCursor.getFullYear(), state.monthCursor.getMonth() - 1, 1);
      renderAll();
      return;
    }

    if (event.target.closest('[data-archive-calendar-next]')) {
      event.preventDefault();
      state.monthCursor = new Date(state.monthCursor.getFullYear(), state.monthCursor.getMonth() + 1, 1);
      renderAll();
      return;
    }

    if (event.target.closest('[data-archive-calendar-clear]')) {
      event.preventDefault();
      state.checkIn = '';
      state.checkOut = '';
      setError('');
      renderAll();
    }
  });

  submitBtn?.addEventListener('click', (event) => {
    event.preventDefault();
    submitBooking();
  });

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && modal.classList.contains('is-open')) {
      closeModal();
    }
  });
});
