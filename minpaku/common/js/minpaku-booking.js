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
    /**
     * 今回は「人数」に一本化するため children は使わない
     * 既存コードとの互換のため 0 固定で保持する
     */
    children: 0,
    monthCursor: null,
  };

  const guestDraft = {
    adults: state.adults,
    /**
     * 今回は children を使わないので 0 固定
     */
    children: 0,
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

  /**
   * 合計人数
   * - 今回の仕様では「人数」に一本化するので adults だけを見る
   */
  function totalGuests() {
    return state.adults;
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

  function openModal(modal) {
    if (!modal) return;
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
        loop: false,
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

    if (!ymd || !isSelectableDate(ymd)) {
      setCalendarError('この日は選択できません。空き日を選択してください。');
      return;
    }

    /*
     * 日付選択ルール:
     * - 未選択: クリック日をチェックインにする
     * - 開始日のみ選択中:
     *   - 同じ日を押したら解除
     *   - 開始日より前を押したら開始日を差し替え
     *   - 開始日より後を押したらチェックアウトにする
     * - 期間確定後:
     *   - 選択中の日付を押したら解除
     *   - 別の日を押したら、その日を新しい開始日にして選び直し
     */
    const hasCheckIn = !!state.checkIn;
    const hasCheckOut = !!state.checkOut;

    // 期間確定後は、別日クリックで即リセットして新しい開始日にする
    if (hasCheckIn && hasCheckOut) {
      if (ymd === state.checkIn || ymd === state.checkOut || (ymd > state.checkIn && ymd < state.checkOut)) {
        state.checkIn = '';
        state.checkOut = '';
      } else {
        state.checkIn = ymd;
        state.checkOut = '';
      }

      refreshDateUi();
      return;
    }

    // 未選択なら開始日を入れる
    if (!hasCheckIn) {
      state.checkIn = ymd;
      state.checkOut = '';

      refreshDateUi();
      return;
    }

    // 開始日だけ選択中に同じ日を押したら解除
    if (ymd === state.checkIn) {
      state.checkIn = '';
      state.checkOut = '';

      refreshDateUi();
      return;
    }

    // 開始日より前なら開始日を差し替え
    if (ymd < state.checkIn) {
      state.checkIn = ymd;
      state.checkOut = '';

      refreshDateUi();
      return;
    }

    const nights = diffNights(state.checkIn, ymd);

    if (nights < cfg.minNights) {
      setCalendarError(`最低 ${cfg.minNights}泊以上で選択してください。`);
      renderCalendar();
      return;
    }

    if (!isRangeSelectable(state.checkIn, ymd)) {
      setCalendarError('選択した範囲に予約不可日があります。空き日だけを選択してください。');
      renderCalendar();
      return;
    }

    // ここで2回目クリックをチェックアウトに固定
    state.checkOut = ymd;

    renderCalendar();
    renderBookingCard();
    renderCheckoutSummary();
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
      if (isCheckIn || isCheckOut) classes += ' is-selected';
      if (inRange) classes += ' is-in-range';
      if (isTodayMark) classes += ' is-today';

      html += `
        <button
          type="button"
          class="${classes}"
          data-calendar-day="${ymd}"
          aria-pressed="${isCheckIn || isCheckOut ? 'true' : 'false'}"
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

    if (guestHelp) {
      guestHelp.textContent = `合計 ${guestDraft.adults}名（最大 ${cfg.capacity}名）`;
    }
  }

  function openGuestModal() {
    clearBookingError();
    guestDraft.adults = state.adults;
    guestDraft.children = 0;
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
      selectionGuests.textContent = `${state.adults}名`;
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

    /**
     * 今回はチェックイン / チェックアウト時刻を表示しない
     * - 時刻入力欄が無い
     * - 料金も時刻では変わらない
     * ため、確認画面では日付だけを表示する
     */
    const datesText = `${formatDateJa(state.checkIn)} 〜 ${formatDateJa(state.checkOut)}`;
    const guestsText = `合計 ${state.adults}名`;

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

  function resetPaymentElement() {
    if (paymentElementInstance && typeof paymentElementInstance.destroy === 'function') {
      paymentElementInstance.destroy();
    }
    if (paymentElementWrap) {
      paymentElementWrap.innerHTML = '';
    }
    paymentElementInstance = null;
    elementsInstance = null;
    currentClientSecret = '';
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
    /**
     * 今回は children を使わないので 0 固定で送る
     */
    formData.append('children', '0');
    formData.append('name', paymentNameInput ? paymentNameInput.value.trim() : '');
    formData.append('email', paymentEmailInput ? paymentEmailInput.value.trim() : '');

    const response = await fetch(window.mnpkBooking.ajaxUrl, {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
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
    const calc = calculateBooking();

    if (!calc.valid) {
      setBookingError(calc.message || '予約内容を確認してください。');
      return false;
    }

    if (!paymentForm || !paymentElementWrap) {
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

    setPaymentSkeletonState(false);
    resetPaymentElement();

    try {
      const { clientSecret } = await createPaymentIntent();
      currentClientSecret = clientSecret;

      stripeInstance = window.Stripe(window.mnpkBooking.publishableKey);
      elementsInstance = stripeInstance.elements({
        clientSecret: currentClientSecret,
        appearance: { theme: 'stripe' },
      });

      paymentElementInstance = elementsInstance.create('payment', {
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
      });

      paymentElementInstance.on('loaderstart', () => {
        setPaymentSkeletonState(false);
      });

      paymentElementInstance.on('ready', () => {
        setPaymentSkeletonState(true);
      });

      paymentElementInstance.mount('#mnpk-payment-element');
      bindPaymentElementReadyObserver();

      window.setTimeout(() => {
        if (paymentElementWrap && paymentElementWrap.children.length > 0) {
          setPaymentSkeletonState(true);
        }
      }, 1200);

      return true;
    } catch (error) {
      console.error(error);
      setPaymentSkeletonState(true);
      setPaymentError(error.message || '支払いフォームの準備に失敗しました。');
      return false;
    }
  }

  function buildCheckoutUrl() {
    const url = new URL(cfg.checkoutUrl || cfg.detailUrl, window.location.origin);
    url.searchParams.set('checkin', state.checkIn);
    url.searchParams.set('checkout', state.checkOut);
    url.searchParams.set('adults', String(state.adults));
    url.searchParams.set('children', '0');
    return url.toString();
  }

  async function openMobilePaymentModal() {
    const ok = renderCheckoutSummary();
    if (!ok) return;
    openModal(paymentModal);
    await mountPaymentElement();
  }

  async function initCheckoutPaymentOnLoad() {
    const checkoutPaymentSection = document.querySelector('.mnpk-checkout-payment');

    if (!checkoutPaymentSection) return;
    if (!paymentForm || !paymentElementWrap) return;

    const ok = renderCheckoutSummary();
    if (!ok) {
      console.warn('[mnpk] renderCheckoutSummary returned false on checkout init');
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
        const currentTotal = guestDraft.adults;

        if (action === 'minus') {
          if (type === 'adults' && guestDraft.adults > 1) guestDraft.adults -= 1;
        }

        if (action === 'plus') {
          if (currentTotal >= cfg.capacity) return;
          if (type === 'adults') guestDraft.adults += 1;
        }

        syncGuestRows();
      });
    });
  });

  document.querySelector('[data-apply-guests]')?.addEventListener('click', async () => {
    state.adults = guestDraft.adults;

    /**
     * 今回は children を使わないので常に 0
     */
    state.children = 0;
    guestDraft.children = 0;

    closeModal(guestModal);
    renderBookingCard();
    renderCheckoutSummary();
    setPaymentSkeletonState(false);
    resetPaymentElement();

    /**
     * checkout ページで人数変更したら、
     * その新しい金額で Payment Element を作り直す
     */
    if (isCheckoutPage && paymentForm) {
      await mountPaymentElement();
    }

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

      if (!elementsInstance || !paymentElementInstance) {
        const mounted = await mountPaymentElement();
        if (!mounted) return;
      }

      setPaymentError('');

      try {
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
          setPaymentError(result.error.message || '決済に失敗しました。');
          return;
        }

        if (
          result.paymentIntent &&
          ['succeeded', 'processing', 'requires_capture'].includes(result.paymentIntent.status)
        ) {
          alert('お支払い処理を受け付けました。');
          closeModal(paymentModal);
          return;
        }

        setPaymentError('決済ステータスを確認できませんでした。');
      } catch (error) {
        console.error(error);
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
