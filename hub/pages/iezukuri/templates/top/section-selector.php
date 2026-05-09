<?php
/**
 * /iezukuri 暮らし方選択カード
 *
 * 役割:
 * - ユーザーに目的を選ばせる
 * - クリックしても別ページへ遷移しない
 * - JSで下のLPパネルを切り替える
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<section class="ch-section ch-life-selector" aria-labelledby="ch-life-selector-title">
    <div class="ch-shell">
        <div class="ch-head">
            <div>
                <p class="ch-eyebrow">Choose Your Life</p>
                <h2 id="ch-life-selector-title" class="ch-section-title">暮らし方から選ぶ</h2>
                <p class="ch-section-lead">
                    住宅の種類ではなく、あなたの暮らし方に近いものを選んでください。
                    選ぶと、このページ内の内容が切り替わります。
                </p>
            </div>
        </div>

        <div class="ch-life-selector__grid" data-iez-life-selector>
            <button type="button" class="ch-life-card is-active" data-life-target="newlywed">
                <span>01</span>
                <strong>新婚・一世帯の新築</strong>
                <small>初めての家づくり</small>
            </button>

            <button type="button" class="ch-life-card" data-life-target="nisetai">
                <span>02</span>
                <strong>二世帯住宅</strong>
                <small>親世帯・子世帯で暮らす</small>
            </button>

            <button type="button" class="ch-life-card" data-life-target="dual-life">
                <span>03</span>
                <strong>二拠点生活</strong>
                <small>都市部と那須の暮らし</small>
            </button>

            <button type="button" class="ch-life-card" data-life-target="retirement">
                <span>04</span>
                <strong>定年後の暮らし</strong>
                <small>平屋・小さく暮らす</small>
            </button>

            <button type="button" class="ch-life-card" data-life-target="renovation">
                <span>05</span>
                <strong>中古住宅リフォーム</strong>
                <small>今ある家を整える</small>
            </button>
        </div>
    </div>
</section>
