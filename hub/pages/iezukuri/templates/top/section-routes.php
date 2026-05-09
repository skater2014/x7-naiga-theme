<?php
if (!defined('ABSPATH')) { exit; }
?>

<section class="ch-section ch-section--white ch-top-routes" aria-labelledby="ch-top-routes-title">
    <div class="ch-shell">
        <div class="ch-head ch-head--with-link">
            <div>
                <p class="ch-eyebrow">Entrance</p>
                <h2 id="ch-top-routes-title" class="ch-section-title">家づくりの入口</h2>
                <p class="ch-top-routes__lead">
                    考え方、設計方針、中古住宅、二世帯住宅へ。知りたい内容から進めます。
                </p>
            </div>

            <a class="ch-inline-link" href="<?php echo esc_url(home_url('/iezukuri/design-policy/')); ?>">
                設計方針を見る
            </a>
        </div>

        <div class="ch-route-grid">
            <article class="ch-route-card">
                <a href="<?php echo esc_url(home_url('/iezukuri/concept/')); ?>">
                    <div class="ch-route-card__image">
                        <span>Concept</span>
                    </div>
                    <div class="ch-route-card__body">
                        <p class="ch-route-card__label">Concept</p>
                        <h3>家づくりの考え方</h3>
                        <p>土地、自然、家族の時間。まずは家づくりの思想を紹介します。</p>
                        <b>詳しく見る</b>
                    </div>
                </a>
            </article>

            <article class="ch-route-card">
                <a href="<?php echo esc_url(home_url('/iezukuri/design-policy/')); ?>">
                    <div class="ch-route-card__image">
                        <span>Design</span>
                    </div>
                    <div class="ch-route-card__body">
                        <p class="ch-route-card__label">Design Policy</p>
                        <h3>設計方針</h3>
                        <p>光、風、動線、性能、素材。暮らしやすさをつくる理由を整理します。</p>
                        <b>詳しく見る</b>
                    </div>
                </a>
            </article>

            <article class="ch-route-card">
                <a href="<?php echo esc_url(home_url('/iezukuri/chuko/')); ?>">
                    <div class="ch-route-card__image">
                        <span>Chuko</span>
                    </div>
                    <div class="ch-route-card__body">
                        <p class="ch-route-card__label">Used House</p>
                        <h3>中古住宅</h3>
                        <p>購入前の確認、改修範囲、費用配分まで。中古住宅の判断軸を整理します。</p>
                        <b>詳しく見る</b>
                    </div>
                </a>
            </article>

            <article class="ch-route-card">
                <a href="<?php echo esc_url(home_url('/iezukuri/nisetai/')); ?>">
                    <div class="ch-route-card__image">
                        <span>Nisetai</span>
                    </div>
                    <div class="ch-route-card__body">
                        <p class="ch-route-card__label">Two-family House</p>
                        <h3>二世帯住宅</h3>
                        <p>親世帯と子世帯の距離感、共有と分離、将来の使い方から考えます。</p>
                        <b>詳しく見る</b>
                    </div>
                </a>
            </article>
        </div>
    </div>
</section>
