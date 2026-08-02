<?php
if (!defined('ABSPATH')) {
  exit;
}

/**
 * 文字なし・ラベルなしの住宅断面図を使う。
 * ラベル・説明・クリック操作はHTML/CSS/JS側で管理する。
 */
$cutaway_src = get_template_directory_uri() . '/hub/pages/iezukuri/assets/images/concept/season-performance/house-cutaway-clean.png';
?>

<div class="ch-concept-core ch-season-performance" data-ch-season-demo data-season="spring">

  <header class="ch-season-performance__head">
    <p class="ch-kicker">PERFORMANCE CONCEPT</p>
    <h2 class="ch-section-title">快適な暮らしを支える、住まいの見えない工夫。</h2>
    <p>
      断熱・気密・換気・窓まわりの性能は、普段の暮らしでは見えにくい部分です。
      暑さや寒さ、湿気や結露を抑えながら、家の中の心地よさを保つための考え方を紹介します。
    </p>
  </header>

  <div class="ch-season-performance__layout">

    <aside class="ch-season-nav" aria-label="季節を選ぶ">
      <h3>季節を選ぶ</h3>

      <button type="button" class="ch-season-btn is-active" data-season-btn="spring" aria-pressed="true">
        <span>春</span>
        <small>寒暖差・風・花粉</small>
      </button>

      <button type="button" class="ch-season-btn" data-season-btn="summer" aria-pressed="false">
        <span>夏</span>
        <small>日射・湿気・高温多湿</small>
      </button>

      <button type="button" class="ch-season-btn" data-season-btn="autumn" aria-pressed="false">
        <span>秋</span>
        <small>朝晩の冷え込み</small>
      </button>

      <button type="button" class="ch-season-btn" data-season-btn="winter" aria-pressed="false">
        <span>冬</span>
        <small>寒さ・積雪・結露</small>
      </button>

      <div class="ch-season-summary">
        <article class="ch-season-summary__item is-active" data-season-card="spring">
          <p>春の那須</p>
          <h4>寒暖差・風・花粉に備える</h4>
          <ul>
            <li>すき間風を防ぎ、室温の変化を抑える</li>
            <li>計画換気で花粉や外気の影響を抑える</li>
            <li>樹脂サッシで窓まわりの快適性を守る</li>
          </ul>
        </article>

        <article class="ch-season-summary__item" data-season-card="summer">
          <p>夏の那須</p>
          <h4>日射・湿気・高温多湿に備える</h4>
          <ul>
            <li>高断熱で外の熱を室内へ伝えにくくする</li>
            <li>壁・屋根の通気で湿気と熱を逃がす</li>
            <li>計画換気で空気を入れ替える</li>
          </ul>
        </article>

        <article class="ch-season-summary__item" data-season-card="autumn">
          <p>秋の那須</p>
          <h4>朝晩の冷え込みに備える</h4>
          <ul>
            <li>断熱と気密で室温の急変を抑える</li>
            <li>樹脂サッシで冷気と熱損失を抑える</li>
            <li>換気しながら空気を安定させる</li>
          </ul>
        </article>

        <article class="ch-season-summary__item" data-season-card="winter">
          <p>冬の那須</p>
          <h4>寒さ・積雪・結露に備える</h4>
          <ul>
            <li>屋根・壁・床から熱を逃がしにくくする</li>
            <li>基礎・土台まわりからの冷気を抑える</li>
            <li>結露を防ぎ、家の耐久性を守る</li>
          </ul>
        </article>
      </div>
    </aside>

    <main class="ch-house-view" aria-label="高気密高断熱の住宅性能図">

<div class="ch-house-view__season-bg" aria-hidden="true"></div>

      <div class="ch-house-view__focus">
        <span>選択中の季節</span>
        <strong data-season-focus>春：寒暖差・風・花粉に、気密・換気・窓性能で備える</strong>
      </div>

      <figure class="ch-house-cutaway">
        <img
          src="<?php echo esc_url($cutaway_src); ?>"
          alt="住宅の断面イメージ"
          loading="eager"
          decoding="async">

        <button type="button" class="ch-map-label ch-map-label--insulation" data-label-id="insulation">
          <b>高断熱</b><span>屋根・天井・床</span>
        </button>

        <button type="button" class="ch-map-label ch-map-label--wall" data-label-id="wall-insulation">
          <b>外壁断熱</b><span>外気の影響を抑える</span>
        </button>

        <button type="button" class="ch-map-label ch-map-label--sash" data-label-id="sash">
          <b>樹脂サッシ・Low-E複層ガラス</b><span>冷気・結露・熱損失を抑える</span>
        </button>

        <button type="button" class="ch-map-label ch-map-label--airtight" data-label-id="airtight">
          <b>高気密</b><span>すき間風を防ぐ</span>
        </button>

        <button type="button" class="ch-map-label ch-map-label--type1" data-label-id="type1-vent">
          <b>熱交換型第一種換気</b><span>給気・排気を機械で制御</span>
        </button>

        <button type="button" class="ch-map-label ch-map-label--planned" data-label-id="planned-vent">
          <b>24時間計画換気</b><span>空気の入口と出口を設計</span>
        </button>

        <button type="button" class="ch-map-label ch-map-label--supply" data-label-id="supply-air">
          <b>給気</b><span>新鮮な空気を入れる</span>
        </button>

        <button type="button" class="ch-map-label ch-map-label--exhaust" data-label-id="exhaust-air">
          <b>排気</b><span>汚れた空気を出す</span>
        </button>

        <button type="button" class="ch-map-label ch-map-label--air-treatment" data-label-id="air-treatment">
          <b>気密処理</b><span>余計なすき間風を防ぐ</span>
        </button>

        <button type="button" class="ch-map-label ch-map-label--airtight-line" data-label-id="airtight-line">
          <b>気密ライン</b><span>家全体を連続して守る</span>
        </button>

        <button type="button" class="ch-map-label ch-map-label--vent" data-label-id="wall-roof-vent">
          <b>壁・屋根の通気</b><span>湿気と熱を逃がす</span>
        </button>

        <button type="button" class="ch-map-label ch-map-label--foundation" data-label-id="foundation">
          <b>基礎・土台まわりの気密</b><span>床下からの冷気を防ぐ</span>
        </button>

        <button type="button" class="ch-map-label ch-map-label--floor-insulation" data-label-id="floor-insulation">
          <b>床断熱・床下防湿</b><span>足元の冷えと湿気</span>
        </button>

        <button type="button" class="ch-map-label ch-map-label--condensation" data-label-id="condensation">
          <b>結露対策</b><span>窓・壁・湿気を抑える</span>
        </button>

        <button type="button" class="ch-map-label ch-map-label--energy" data-label-id="energy">
          <b>省エネ・快適性</b><span>冷暖房効率を高める</span>
        </button>

        <button type="button" class="ch-map-label ch-map-label--ng ch-map-label--ng-window" data-label-id="ng-window">
          <b>NG 窓まわり</b><span>すき間ができやすい</span>
        </button>

        <button type="button" class="ch-map-label ch-map-label--ng ch-map-label--ng-floor" data-label-id="ng-floor">
          <b>NG 点検口・床下</b><span>すき間ができやすい</span>
        </button>

        <button type="button" class="ch-map-label ch-map-label--ng ch-map-label--ng-pipe" data-label-id="ng-pipe">
          <b>NG 配管貫通部</b><span>すき間ができやすい</span>
        </button>

      </figure>

      <div class="ch-label-strip" aria-label="性能ラベル">
        <button type="button" data-label-id="insulation">高断熱<span>屋根・天井・床</span></button>
        <button type="button" data-label-id="wall-insulation">外壁断熱<span>外気の影響</span></button>
        <button type="button" data-label-id="sash">樹脂サッシ・Low-E複層ガラス<span>窓の性能</span></button>
        <button type="button" data-label-id="airtight">高気密<span>すき間風</span></button>
        <button type="button" data-label-id="type1-vent">熱交換型第一種換気<span>給気・排気を機械で制御</span></button>
        <button type="button" data-label-id="planned-vent">24時間計画換気<span>空気の入口と出口を設計</span></button>
        <button type="button" data-label-id="supply-air">給気<span>空気を入れる</span></button>
        <button type="button" data-label-id="exhaust-air">排気<span>空気を出す</span></button>
        <button type="button" data-label-id="air-treatment">気密処理<span>すき間対策</span></button>
        <button type="button" data-label-id="airtight-line">気密ライン<span>連続した気密</span></button>
        <button type="button" data-label-id="wall-roof-vent">壁・屋根の通気<span>湿気と熱</span></button>
        <button type="button" data-label-id="foundation">基礎・土台まわりの気密<span>床下冷気</span></button>
        <button type="button" data-label-id="floor-insulation">床断熱・床下防湿<span>冷えと湿気</span></button>
        <button type="button" data-label-id="condensation">結露対策<span>窓・壁・湿気</span></button>
        <button type="button" data-label-id="energy">省エネ・快適性<span>冷暖房効率</span></button>
        <button type="button" data-label-id="ng-window">NG 窓まわり<span>すき間注意</span></button>
        <button type="button" data-label-id="ng-floor">NG 点検口・床下<span>すき間注意</span></button>
        <button type="button" data-label-id="ng-pipe">NG 配管貫通部<span>すき間注意</span></button>

      </div>
    </main>

    <aside class="ch-label-detail" aria-live="polite">
      <p class="ch-label-detail__eyebrow">選択中のラベル</p>

      <div class="ch-detail-head">
        <h3 data-label-title>高気密</h3>
        <p data-label-subtitle>すき間風を防ぐ性能</p>
      </div>

      <?php
      /*
             * 詳細画像の表示枠。
             *
             * 役割:
             * - JSの labelData.xxx.image に画像名がある場合だけ、ここへ画像を表示する
             * - 例: labelData.sash.image = 'sash-low-e.png'
             *
             * 画像の保存場所:
             * - hub/pages/iezukuri/assets/images/concept/season-performance/
             *
             * 実際に読み込む画像例:
             * - hub/pages/iezukuri/assets/images/concept/season-performance/sash-low-e.png
             *
             * 注意:
             * - ここには画像ファイル名を書かない
             * - 画像ファイル名は JS 側の labelData で管理する
             * - このPHPは「画像のベースフォルダ」だけをJSへ渡す
             */
      ?>
      <div
        class="ch-detail-visual"
        data-label-visual
        data-image-base="<?php echo esc_url(get_template_directory_uri() . '/hub/pages/iezukuri/assets/images/concept/season-performance'); ?>"
        hidden></div>

      <div class="ch-label-detail__box">
        <strong>役割</strong>
        <p data-label-role>余計なすき間を減らし、計画した換気と断熱性能を発揮しやすくします。</p>
      </div>

      <div class="ch-label-detail__box">
        <strong>那須で効く理由</strong>
        <p data-label-reason>冬の冷気、春の風、花粉、夏の湿気の侵入を抑え、室温と空気環境を安定させます。</p>
      </div>

      <div class="ch-label-detail__tags" data-label-tags></div>
    </aside>

  </div>

  <section class="ch-proof-flow">
    <div><span>01</span><strong>設計で確認</strong>
      <p>断熱・換気・窓仕様を確認。</p>
    </div>
    <div><span>02</span><strong>現場でつくる</strong>
      <p>職人が共通ルールで施工。</p>
    </div>
    <div><span>03</span><strong>施工管理が見る</strong>
      <p>配管・窓・基礎まわりを確認。</p>
    </div>
    <div><span>04</span><strong>床まわりを整える</strong>
      <p>床断熱・床下防湿・点検性を確認。</p>
    </div>
    <div><span>05</span><strong>施工内容を確認</strong>
      <p>写真と仕様で確認。</p>
    </div>
  </section>

</div>

<script src="<?php echo esc_url(get_template_directory_uri() . '/hub/pages/iezukuri/js/concept-season.js'); ?>" defer></script>
