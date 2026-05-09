<?php
/**
 * Template Name: 家づくり：新築住宅
 * Template Post Type: page
 *
 * 対象:
 * /iezukuri/shinchiku/
 *
 * 役割:
 * 那須の建設会社として、新築住宅の相談内容を整理して見せるページ。
 *
 * 方針:
 * - 土地探しの話は入れない
 * - 間取り計画、標準仕様、設備、断熱、気密、耐震を標準側に置く
 * - PC作業、ガレージ作業、造園、畑、プールはプレミアム側に分ける
 * - CSS / JS 読み込みは inc/enqueue.php 側で管理する
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header('customhome');
?>

<main id="primary" class="hub-customhome-page hub-customhome-subpage nh-page" data-iezukuri-page="new-house">
    <section class="ch-hero ch-fullbleed is-no-media" data-customhome-hero>
        <div class="ch-hero__overlay"></div>

        <div class="ch-shell ch-hero__inner">
            <div class="ch-hero__content">
                <p class="ch-hero__company">内外建設株式会社</p>

                <p class="ch-hero__eyebrow">新築住宅</p>

                <h1 class="ch-hero__title">
                    那須の暮らしに合わせて、<br>
                    長く使える住まいをつくる
                </h1>

                <p class="ch-hero__lead">
                    新築住宅は、外観や部屋数だけで決めるものではありません。
                    間取り、収納、家事動線、設備、断熱、気密、耐震、そして仕事や趣味に使える場所まで含めて、
                    暮らしに合う住まいとして計画します。
                </p>

                <div class="ch-hero__actions">
                    <a class="ch-btn ch-btn--primary" href="<?php echo esc_url(home_url('/contact/')); ?>">
                        新築住宅について相談する
                    </a>
                    <a class="ch-btn ch-btn--ghost" href="<?php echo esc_url(home_url('/sekou-jirei/')); ?>">
                        施工事例を見る
                    </a>
                </div>
            </div>
        </div>
    </section>

<section class="ch-subpage-section nh-section">
        <div class="ch-shell nh-section-head">
            <p class="nh-kicker">Planning</p>
            <h2>暮らし方から考える間取り計画</h2>
            <p>
                新築住宅では、部屋数だけでなく、毎日の動き方を整理することが大切です。
                料理、洗濯、片付け、仕事、来客、家族の帰宅時間などを確認しながら、
                無理のない間取りを考えます。
            </p>
            <p>
                那須で長く暮らす住まいとして、室内のあたたかさ、収納量、家事動線、
                将来の使いやすさまで含めて計画します。
            </p>
        </div>

        <div class="ch-shell nh-card-grid">
            <article class="nh-card">
                <span>01</span>
                <h3>LDKと家事動線</h3>
                <p>
                    キッチン、洗面、浴室、収納、玄関のつながりを整理し、
                    毎日の動きが重くならない間取りを考えます。
                </p>
            </article>

            <article class="nh-card">
                <span>02</span>
                <h3>収納計画</h3>
                <p>
                    玄関収納、食品収納、洗面まわり、季節用品、掃除道具など、
                    生活に必要な収納量と場所を整理します。
                </p>
            </article>

            <article class="nh-card">
                <span>03</span>
                <h3>将来の使いやすさ</h3>
                <p>
                    子どもの成長、親との同居、老後の暮らし方など、
                    家族の変化に対応しやすい住まいを考えます。
                </p>
            </article>

            <article class="nh-card">
                <span>04</span>
                <h3>小さな作業場所</h3>
                <p>
                    パソコン作業、書類整理、勉強、家計管理などに使える
                    カウンターや小さな作業場所も計画に入れます。
                </p>
            </article>
        </div>
    </section>

    <section class="ch-subpage-section nh-section is-standard">
        <div class="ch-shell nh-section-head is-center">
            <p class="nh-kicker">Standard</p>
            <h2>新築住宅の標準仕様</h2>
            <p>
                標準仕様は、家の基本性能と毎日の使いやすさを支える部分です。
                設備だけでなく、断熱、気密、耐震、換気、窓、屋根、外壁、電気・ネット環境まで含めて考えます。
            </p>
        </div>

        <div class="ch-shell nh-spec-grid">
            <article>
                <h3>断熱</h3>
                <p>那須の冬を考え、壁・床・天井・窓まわりの断熱を整えます。室内の温度差を抑え、暖房効率を高めます。</p>
            </article>

            <article>
                <h3>気密</h3>
                <p>すき間から熱が逃げにくいよう、建物全体の気密性に配慮します。断熱と合わせて室内環境を安定させます。</p>
            </article>

            <article>
                <h3>耐震</h3>
                <p>長く安心して暮らすために、構造や耐震性を大切にします。間取りの自由度だけでなく建物としての強さも考えます。</p>
            </article>

            <article>
                <h3>換気・湿気対策</h3>
                <p>空気環境や湿気対策も住み心地に関わります。換気計画を整え、結露や湿気による不快感を抑えます。</p>
            </article>

            <article>
                <h3>水回り設備</h3>
                <p>キッチン、浴室、洗面、トイレは毎日使う場所です。掃除のしやすさ、収納、交換のしやすさも含めて計画します。</p>
            </article>

            <article>
                <h3>電気・ネット環境</h3>
                <p>照明、コンセント、スイッチ、インターネット配線は使いやすさに直結します。在宅ワークや家電配置も考えます。</p>
            </article>

            <article>
                <h3>窓・採光</h3>
                <p>窓の位置や大きさは、明るさ、断熱性、視線、風通しに関わります。見た目だけでなく暮らしやすさを考えます。</p>
            </article>

            <article>
                <h3>屋根・外壁</h3>
                <p>屋根や外壁は住まいを守る大切な部分です。耐久性、印象、将来のメンテナンスまで含めて選びます。</p>
            </article>
        </div>
    </section>

    <section class="ch-subpage-section nh-section">
        <div class="ch-shell nh-work-layout">
            <div class="nh-section-head">
                <p class="nh-kicker">Work Space</p>
                <h2>仕事や趣味に使える作業スペース</h2>
                <p>
                    これからの住まいでは、くつろぐ場所だけでなく、仕事や趣味に使える場所も大切です。
                    パソコン作業、オンライン会議、資料整理、DIY、工具収納など、
                    暮らし方に合わせて作業スペースを考えます。
                </p>
            </div>

            <div class="nh-two-grid">
                <article class="nh-panel">
                    <h3>標準に近い作業スペース</h3>
                    <ul>
                        <li>LDK横のワークカウンター</li>
                        <li>寝室横の小さな作業スペース</li>
                        <li>家族共有のスタディスペース</li>
                        <li>コンセント・照明・ネット環境</li>
                    </ul>
                </article>

                <article class="nh-panel is-premium">
                    <h3>プレミアム側の作業スペース</h3>
                    <ul>
                        <li>個室書斎</li>
                        <li>防音性を考えたワークルーム</li>
                        <li>造作デスク・造作収納</li>
                        <li>ガレージ併設のDIY作業場</li>
                    </ul>
                </article>
            </div>
        </div>
    </section>

    <section class="ch-subpage-section nh-section is-premium">
        <div class="ch-shell nh-premium-head">
            <div>
                <p class="nh-kicker">Premium</p>
                <h2>暮らしを広げるプレミアム提案</h2>
            </div>
            <p>
                標準仕様で住まいの基本性能を整えたうえで、希望に合わせて暮らしを広げる提案もできます。
                造園、畑、プール、ガレージ作業場、本格ワークスペースなどは、
                全員に必要な標準仕様ではなく、目的に合わせて切り分ける提案です。
            </p>
        </div>

        <div class="ch-shell nh-premium-grid">
            <article>
                <h3>本格ワークスペース</h3>
                <p>オンライン会議、防音、背景、収納、照明、コンセント、ネット環境まで考えます。</p>
            </article>

            <article>
                <h3>ガレージ・DIY作業場</h3>
                <p>工具収納、作業台、木工作業、メンテナンス作業に使える場所を計画します。</p>
            </article>

            <article>
                <h3>プレミアム造園・畑</h3>
                <p>植栽、アプローチ、テラス、外部照明、家庭菜園、道具置き場も含めて計画できます。</p>
            </article>

            <article>
                <h3>プール・外遊びスペース</h3>
                <p>安全性、視線、排水、メンテナンス、テラスとのつながりを考えます。</p>
            </article>
        </div>
    </section>

    <section class="ch-subpage-section nh-section is-gallery">
        <div class="ch-shell nh-section-head is-center">
            <p class="nh-kicker">Gallery</p>
            <h2>写真で見せる内容</h2>
            <p>
                画像は後で差し替えられるように、まずは表示枠を用意します。
                完成写真、標準仕様、作業スペース、ガレージ、造園まわりを分けて見せます。
            </p>
        </div>

        <div class="ch-shell nh-visual-grid">
            <figure>
                <div>外観・LDK</div>
                <figcaption>暮らしの中心になる空間</figcaption>
            </figure>

            <figure>
                <div>標準仕様</div>
                <figcaption>断熱・気密・耐震・設備</figcaption>
            </figure>

            <figure>
                <div>PC作業</div>
                <figcaption>在宅ワークや資料整理の場所</figcaption>
            </figure>

            <figure>
                <div>ガレージ</div>
                <figcaption>DIYや工具収納に使う場所</figcaption>
            </figure>

            <figure>
                <div>造園・畑</div>
                <figcaption>庭を楽しむ外部空間</figcaption>
            </figure>

            <figure>
                <div>プール</div>
                <figcaption>外遊びや水遊びの計画</figcaption>
            </figure>
        </div>
    </section>

    <section class="ch-subpage-section nh-section is-flow">
        <div class="ch-shell nh-section-head">
            <p class="nh-kicker">Flow</p>
            <h2>相談から完成までの流れ</h2>
            <p>
                標準仕様とプレミアム提案を混ぜずに整理しながら、
                必要なものを順番に決めていきます。
            </p>
        </div>

        <ol class="ch-shell nh-flow-list">
            <li><span>01</span><strong>ご相談</strong><p>現在の暮らし、困っていること、希望する住まい方を伺います。</p></li>
            <li><span>02</span><strong>間取り計画</strong><p>LDK、家事動線、収納、作業スペース、将来の使い方を整理します。</p></li>
            <li><span>03</span><strong>標準仕様の整理</strong><p>断熱、気密、耐震、換気、水回り、電気、外装など基本性能を確認します。</p></li>
            <li><span>04</span><strong>プレミアム提案</strong><p>造園、畑、プール、ガレージ、ワークルームなどを必要に応じて切り分けます。</p></li>
            <li><span>05</span><strong>施工</strong><p>構造、下地、断熱、仕上げまで確認しながら工事を進めます。</p></li>
            <li><span>06</span><strong>完成・引き渡し</strong><p>完成後の確認を行い、暮らし始められる状態でお引き渡しします。</p></li>
        </ol>
    </section>

    <section class="ch-subpage-section nh-cta">
        <div class="ch-shell">
            <p class="nh-kicker">Contact</p>
            <h2>新築住宅のことを、まずは相談してみませんか</h2>
            <p>
                間取り、標準仕様、設備、作業スペース、プレミアム造園まで、
                必要なものを分けて整理します。
            </p>
            <a class="nh-btn is-primary" href="<?php echo esc_url(home_url('/contact/')); ?>">
                お問い合わせ
            </a>
        </div>
    </section>

</main>

<?php
get_footer();
