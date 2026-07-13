(function () {
  const root = document.querySelector('[data-ch-season-demo]');
  if (!root) return;

  /*
   * mapLabels:
   * - PCの断面図上に出す少数ラベル
   *
   * detailLabels:
   * - 下部カードに出す選択ラベル
   * - モバイルではこのカードだけで詳細を切り替える
   */
  const seasonData = {
    spring: {
      focus: '春：寒暖差・風・花粉に、換気・窓性能・気密・床まわりで備える',
      mapLabels: ['type1-vent', 'planned-vent', 'sash', 'airtight', 'floor-insulation'],
      detailLabels: ['type1-vent', 'planned-vent', 'sash', 'airtight', 'floor-insulation']
    },
    summer: {
      focus: '夏：日射・湿気・高温多湿に、換気・窓性能・気密・床まわりで備える',
      mapLabels: ['type1-vent', 'planned-vent', 'sash', 'airtight', 'floor-insulation'],
      detailLabels: ['type1-vent', 'planned-vent', 'sash', 'airtight', 'floor-insulation']
    },
    autumn: {
      focus: '秋：朝晩の冷え込みに、換気・窓性能・気密・床まわりで備える',
      mapLabels: ['type1-vent', 'planned-vent', 'sash', 'airtight', 'floor-insulation'],
      detailLabels: ['type1-vent', 'planned-vent', 'sash', 'airtight', 'floor-insulation']
    },
    winter: {
      focus: '冬：寒さ・積雪・結露に、換気・窓性能・気密・床まわりで備える',
      mapLabels: ['type1-vent', 'planned-vent', 'sash', 'airtight', 'floor-insulation'],
      detailLabels: ['type1-vent', 'planned-vent', 'sash', 'airtight', 'floor-insulation']
    }
  };

  const labelData = {
    insulation: {
      title: '高断熱',
      subtitle: '屋根・天井・壁・床から熱を逃がしにくくする',
      role: '屋根・天井・壁・床からの熱の出入りを抑え、冷暖房の効率と室温の安定を支えます。',
      reason: '那須の冬の寒さ、夏の日射、秋の冷え込みに対して、家全体の温度変化を小さくします。',
      tags: ['屋根断熱', '天井断熱', '壁断熱', '床断熱', '室温安定']
    },
    'wall-insulation': {
      title: '外壁断熱',
      subtitle: '外気の影響を受けにくくする',
      role: '外壁からの熱の出入りを抑え、室内環境を安定させます。',
      reason: '夏の熱気、冬の冷気の影響を抑え、那須の寒暖差に強い住まいにします。',
      tags: ['外壁', '断熱材', '熱損失対策']
    },
    sash: {
      title: '樹脂サッシ・Low-E複層ガラス',
      subtitle: '窓の冷気・結露・熱損失を抑える',

      /*
       * 詳細画像。
       *
       * 実ファイル:
       * hub/pages/iezukuri/assets/images/concept/season-performance/sash-low-e.png
       *
       * PHP側の data-image-base と、このファイル名をJSで結合して表示する。
       */
      image: 'sash-low-e.png',
      imageAlt: '樹脂サッシとLow-E複層ガラスのイメージ',

      role: '窓からの熱の出入りを抑え、結露の発生を軽減します。',
      reason: '那須の朝晩の冷え込みや冬の寒さに対して、窓まわりの快適性を守ります。',
      tags: ['Low-Eガラス', '樹脂サッシ', '窓性能', '結露軽減']
    },
    airtight: {
      title: '高気密',
      subtitle: '余計なすき間風を防ぐ',

      /*
       * 詳細画像。
       *
       * 役割:
       * - 「高気密」を選択した時に右側詳細パネルへ表示する画像
       * - 画像内に説明文やタイトルは入れない
       * - 説明文はJS側の title / subtitle / role / reason で表示する
       *
       * 実ファイル:
       * hub/pages/iezukuri/assets/images/concept/season-performance/airtight.png
       *
       * 表示の仕組み:
       * - PHP側の data-image-base が画像フォルダを持つ
       * - JS側の image がファイル名を持つ
       * - setDetail() の中で base + '/' + image を結合して img.src に入れる
       */
      image: 'airtight.png',
      imageAlt: '高気密住宅の気密ラインと気密処理のイメージ',

      role: '窓まわり、配管貫通部、床と壁の取り合いなどのすき間を処理し、計画した性能を発揮しやすくします。',
      reason: '冷気、湿気、花粉、すき間風の侵入を抑え、換気を計画通りに動かしやすくします。',
      tags: ['気密ライン', '気密テープ', 'すき間風対策']
    },
    'planned-vent': {
      title: '24時間計画換気',
      subtitle: '家全体の空気の通り道をつくる',

      /*
       * 詳細画像。
       *
       * 役割:
       * - 「24時間計画換気」を選択した時に右側詳細パネルへ表示する画像
       * - 画像内にタイトルや説明文は入れない
       * - 説明文はHTML/JS側の title / subtitle / role / reason で表示する
       *
       * 実ファイル:
       * hub/pages/iezukuri/assets/images/concept/season-performance/planned-ventilation.png
       *
       * 表示の仕組み:
       * - PHP側の data-image-base が画像フォルダを持つ
       * - JS側の image がファイル名を持つ
       * - setDetail() の中で base + '/' + image を結合して img.src に入れる
       *
       * 注意:
       * - ここには絶対パスを書かない
       * - ファイル名だけを書く
       */
      image: 'planned-ventilation.png',
      imageAlt: '24時間計画換気で家全体の空気が流れるイメージ',

      role: 'どこから新しい空気を入れ、どこから湿気や汚れた空気を出すかを設計し、家全体の空気を計画的に入れ替えます。',
      reason: '高気密住宅では、すき間風に頼らず、換気設備で空気を動かすことが大切です。春の花粉、夏の湿気、冬の結露対策にもつながります。',
      tags: ['給気', '排気', '24時間換気', '湿気対策']
    },
    'type1-vent': {
      title: '熱交換型第一種換気',
      subtitle: '給気・排気を機械で制御し、熱を回収する換気方式',

      /*
       * 詳細画像。
       *
       * 実ファイル:
       * hub/pages/iezukuri/assets/images/concept/season-performance/type1-ventilation.png
       *
       * PHP側の data-image-base と、このファイル名をJSで結合して表示する。
       */
      image: 'type1-ventilation.png',
      imageAlt: '熱交換型第一種換気システムのイメージ',

      role: '給気と排気の両方を機械で行い、空気の流れを管理しやすくします。熱交換型の場合、排気する空気の熱を利用して、外から入る空気の温度変化を抑えやすくします。',
      reason: '高気密住宅と組み合わせることで、すき間風に頼らず計画した空気の流れをつくりやすくなります。那須の冬の冷気や夏の湿気の影響も抑えやすくします。',
      tags: ['熱交換型', '第一種換気', '空気管理']
    },
    'supply-air': {
      title: '給気',
      subtitle: '新鮮な空気を計画的に入れる',
      role: '給気口や換気設備から外気を取り入れ、室内の空気を計画的に入れ替えます。',
      reason: '高気密住宅では、すき間風ではなく設計した給気ルートで空気を入れることが重要です。',
      tags: ['給気', '空気の入口', '計画換気']
    },
    'exhaust-air': {
      title: '排気',
      subtitle: '汚れた空気と湿気を外へ出す',
      role: 'トイレ、洗面、キッチン、居室などから湿気やにおいを外へ排出し、空気環境を整えます。',
      reason: '湿気がこもると結露やカビの原因になります。排気の設計が快適性と耐久性に関わります。',
      tags: ['排気', '湿気対策', 'におい対策']
    },
    'wall-roof-vent': {
      title: '壁・屋根の通気',
      subtitle: '湿気と熱を外へ逃がす',
      role: '壁や屋根の通気層で、構造内の湿気や熱を外へ逃がします。',
      reason: '夏の湿気や熱、冬の結露リスクを抑え、家の耐久性を守ります。',
      tags: ['外壁通気', '屋根通気', '湿気対策']
    },
    foundation: {
      title: '基礎・土台まわりの気密',
      subtitle: '床下からの冷気と湿気を抑える',
      role: '基礎と土台、床と壁、点検口まわりの気密ラインを連続させます。',
      reason: '冬の床下冷気や湿気の影響を抑え、足元の快適性と断熱性能を支えます。',
      tags: ['基礎', '土台', '床下', '点検口']
    },
    'floor-insulation': {
      title: '床断熱・床下防湿',
      subtitle: '足元の冷えと床下の湿気を抑える',

      /*
       * 詳細画像。
       *
       * 役割:
       * - 「床断熱・床下防湿」を選択した時に右側詳細パネルへ表示する画像
       * - 画像内に説明文やタイトルは入れない
       * - 説明文はJS側の title / subtitle / role / reason で表示する
       *
       * 実ファイル:
       * hub/pages/iezukuri/assets/images/concept/season-performance/floor-insulation.png
       */
      image: 'floor-insulation.png',
      imageAlt: '床断熱と床下防湿のイメージ',

      role: '床から伝わる冷気を抑える床断熱と、床下に湿気をためにくくする防湿対策を組み合わせ、足元の冷えと床下環境を整えます。',
      reason: '那須は冬の冷え込みや湿気の影響を受けやすいため、床まわりを整えることで、足元の冷え、結露、カビ、木部の劣化リスクを抑えやすくします。',
      tags: ['床断熱', '床下防湿', '足元の冷え', '床下環境']
    },
    condensation: {
      title: '結露対策',
      subtitle: '窓・壁・湿気の納まりで防ぐ',
      role: '断熱、窓性能、換気、通気を組み合わせて、結露の発生を抑えます。',
      reason: '那須の冬の冷え込みや湿気に対して、カビや劣化を防ぎやすくします。',
      tags: ['窓結露', '壁内結露', '湿気管理']
    },
    energy: {
      title: '省エネ・快適性',
      subtitle: '少ないエネルギーで快適に暮らす',
      role: '断熱・気密・換気を整えることで、冷暖房効率を高めます。',
      reason: '夏も冬も冷暖房に頼りすぎず、家計にやさしい住まいを目指します。',
      tags: ['冷暖房効率', '省エネ', '快適性']
    },
    'air-treatment': {
      title: '気密処理',
      subtitle: 'すき間ができやすい部分を処理する',
      role: '窓まわり、配管貫通部、コンセント、点検口など、すき間ができやすい部分を施工段階で処理します。',
      reason: '細かなすき間を放置すると、冷気・湿気・花粉が入りやすくなります。',
      tags: ['気密テープ', '貫通部処理', 'すき間対策']
    },
    'airtight-line': {
      title: '気密ライン',
      subtitle: '家全体を連続した線で守る',
      role: '壁・床・天井・窓・基礎まわりの気密を途切れさせず、家全体で性能を発揮できるようにします。',
      reason: '一部だけ気密が切れると、そこから冷気や湿気が入り込みます。',
      tags: ['連続気密', '施工品質', '気密施工']
    },
    'ng-window': {
      title: 'NG 窓まわり',
      subtitle: '窓まわりはすき間ができやすい',
      role: 'サッシまわりの取り合いは、断熱欠損や気密切れが起きやすいため、丁寧な処理が必要です。',
      reason: '冬の冷気、結露、すき間風の原因になりやすい部分です。',
      tags: ['窓まわり', '断熱欠損', '結露注意']
    },
    'ng-floor': {
      title: 'NG 点検口・床下',
      subtitle: '床下からの冷気に注意',
      role: '床下点検口や床と壁の取り合いは、気密が切れやすい部分のため確認が必要です。',
      reason: '那須の冬は床下からの冷気が体感温度に影響します。',
      tags: ['床下', '点検口', '足元の冷え']
    },
    'ng-pipe': {
      title: 'NG 配管貫通部',
      subtitle: '配管まわりのすき間に注意',
      role: '給排水管、電気配線、換気ダクトなどが壁や床を貫通する部分は、気密処理が必要です。',
      reason: '小さなすき間でも、冷気・湿気・虫・花粉の侵入経路になります。',
      tags: ['配管貫通部', 'ダクト', '気密処理']
    }
  };

  const seasonButtons = root.querySelectorAll('[data-season-btn]');
  const seasonCards = root.querySelectorAll('[data-season-card]');
  const focus = root.querySelector('[data-season-focus]');
  const stripButtons = root.querySelectorAll('.ch-label-strip [data-label-id]');
  const mapLabels = root.querySelectorAll('.ch-map-label[data-label-id]');
  const allLabelButtons = root.querySelectorAll('[data-label-id]');

  const title = root.querySelector('[data-label-title]');
  const subtitle = root.querySelector('[data-label-subtitle]');
  const role = root.querySelector('[data-label-role]');
  const reason = root.querySelector('[data-label-reason]');
  const tags = root.querySelector('[data-label-tags]');
  const visual = root.querySelector('[data-label-visual]');

  let currentSeason = 'spring';

  function setDetail(labelId) {
    const data = labelData[labelId] || labelData.airtight;

    if (title) title.textContent = data.title;
    if (subtitle) subtitle.textContent = data.subtitle;
    if (role) role.textContent = data.role;
    if (reason) reason.textContent = data.reason;

    /*
     * 詳細画像の表示。
     *
     * data.image がある項目だけ画像を表示する。
     * data.image が無い項目では画像枠を隠す。
     */
    if (visual) {
      visual.innerHTML = '';

      if (data.image) {
        const base = visual.getAttribute('data-image-base') || '';
        const img = document.createElement('img');

        img.src = base.replace(/\/$/, '') + '/' + data.image;
        img.alt = data.imageAlt || data.title || '';
        img.loading = 'lazy';
        img.decoding = 'async';

        visual.appendChild(img);
        visual.hidden = false;
      } else {
        visual.hidden = true;
      }
    }

    if (tags) {
      tags.innerHTML = '';

      data.tags.forEach(function (tag) {
        const span = document.createElement('span');
        span.textContent = tag;
        tags.appendChild(span);
      });
    }

    allLabelButtons.forEach(function (btn) {
      btn.classList.toggle('is-active', btn.getAttribute('data-label-id') === labelId);
    });
  }

  function setSeason(season) {
    currentSeason = season;

    const data = seasonData[season] || seasonData.spring;
    const mapIds = data.mapLabels || [];
    const detailIds = data.detailLabels || mapIds;

    root.setAttribute('data-season', season);

    if (focus) focus.textContent = data.focus;

    seasonButtons.forEach(function (btn) {
      const active = btn.getAttribute('data-season-btn') === season;
      btn.classList.toggle('is-active', active);
      btn.setAttribute('aria-pressed', active ? 'true' : 'false');
    });

    seasonCards.forEach(function (card) {
      card.classList.toggle('is-active', card.getAttribute('data-season-card') === season);
    });

    mapLabels.forEach(function (label) {
      const id = label.getAttribute('data-label-id');
      const pinIndex = mapIds.indexOf(id);
      const active = pinIndex !== -1;

      label.classList.toggle('is-season-active', active);

      /*
       * 番号ピンの正本。
       *
       * CSSのcounterではなく、JSの mapLabels の順番を番号にする。
       * これで「1 = 熱交換型第一種換気」に固定する。
       */
      if (active) {
        label.setAttribute('data-pin-number', String(pinIndex + 1));
      } else {
        label.removeAttribute('data-pin-number');
      }
    });

    stripButtons.forEach(function (btn) {
      const id = btn.getAttribute('data-label-id');
      const cardIndex = detailIds.indexOf(id);
      const active = cardIndex !== -1;

      btn.classList.toggle('is-season-active', active);

      /*
       * 下部カードもJSの detailLabels の順番に並べる。
       */
      if (active) {
        btn.style.order = String(cardIndex + 1);
      } else {
        btn.style.order = '';
      }
    });

    setDetail(detailIds[0] || mapIds[0] || 'airtight');
  }

  seasonButtons.forEach(function (btn) {
    btn.addEventListener('click', function () {
      setSeason(btn.getAttribute('data-season-btn'));
    });
  });

  stripButtons.forEach(function (btn) {
    btn.addEventListener('click', function () {
      const id = btn.getAttribute('data-label-id');
      const data = seasonData[currentSeason] || seasonData.spring;

      if ((data.detailLabels || []).includes(id)) {
        setDetail(id);
      }
    });
  });

  mapLabels.forEach(function (label) {
    label.addEventListener('click', function () {
      const id = label.getAttribute('data-label-id');
      const data = seasonData[currentSeason] || seasonData.spring;

      if ((data.mapLabels || []).includes(id)) {
        setDetail(id);
      }
    });
  });

  setSeason('spring');
})();
