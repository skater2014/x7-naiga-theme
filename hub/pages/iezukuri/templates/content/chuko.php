<?php

/**
 * /iezukuri/chuko
 * 住まいの修理・補修 本文
 *
 * 母体:
 * hub/pages/iezukuri/templates/template-iezukuri-subpage.php
 *
 * ここに書くもの:
 * - 住まいの修理本文だけ
 *
 * ここに書かないもの:
 * - get_header()
 * - get_footer()
 * - main
 * - hero
 * - CTA
 */

if (!defined('ABSPATH')) {
    exit;
}

/*
 * chuko は専用本文パーツを使う。
 * page-content-renderer.php の仮メタ本文を先に出すと、
 * 「見出し / 本文を入力します。」で return してしまうため呼ばない。
 */
?>

<div class="ch-subpage-content ch-subpage-content--chuko ch-repair-content" data-iezukuri-content="chuko">

    <section class="ch-repair-section is-white" id="chuko-repair-check">
        <div class="ch-shell">
            <div class="ch-repair-lead">
                <p class="ch-repair-kicker">USED HOUSE REPAIR</p>
                <h2>住み慣れた家は、見た目より先に「傷みの原因」を確認する。</h2>
                <p>
                    築年数が経過したお住まいの修理は、壁紙や床をきれいにする前に、雨漏り・屋根・外壁・床下・水回りなど、
                    建物を傷める原因を確認することが大切です。原因を残したまま内装だけ直すと、
                    後から修理範囲が広がり、費用も増えやすくなります。
                </p>
            </div>

            <div class="ch-repair-grid">
                <article class="ch-repair-card">
                    <span class="ch-repair-card__num">01</span>
                    <h3>雨漏り・屋根修理</h3>
                    <p>
                        天井のシミ、屋根材の割れ、板金の浮き、雨樋の詰まりを確認します。
                        雨漏りは内装より先に直す必要があります。
                    </p>
                </article>

                <article class="ch-repair-card">
                    <span class="ch-repair-card__num">02</span>
                    <h3>外壁・ひび割れ補修</h3>
                    <p>
                        外壁の割れ、塗装の劣化、コーキング切れを確認します。
                        水が入る前に補修して、構造部分の傷みを防ぎます。
                    </p>
                </article>

                <article class="ch-repair-card">
                    <span class="ch-repair-card__num">03</span>
                    <h3>床下・基礎の確認</h3>
                    <p>
                        床の沈み、基礎のひび、湿気、白蟻、土台の腐食を確認します。
                        住みながら直す場合も、床下確認は重要です。
                    </p>
                </article>

                <article class="ch-repair-card">
                    <span class="ch-repair-card__num">04</span>
                    <h3>水回り修理</h3>
                    <p>
                        キッチン、浴室、洗面、トイレ、給排水管の劣化を確認します。
                        漏水や配管の古さは、内装より優先します。
                    </p>
                </article>

                <article class="ch-repair-card">
                    <span class="ch-repair-card__num">05</span>
                    <h3>断熱・窓まわり</h3>
                    <p>
                        寒さ、結露、すきま風、窓の劣化を確認します。
                        断熱や窓の改善は、暮らしやすさと光熱費に関わります。
                    </p>
                </article>

                <article class="ch-repair-card">
                    <span class="ch-repair-card__num">06</span>
                    <h3>内装・建具の補修</h3>
                    <p>
                        壁、床、天井、ドア、収納、階段の傷みを確認します。
                        表面の修理は、雨漏りや水回りの確認後に進めます。
                    </p>
                </article>
            </div>
        </div>
    </section>

    <section class="ch-repair-section">
        <div class="ch-shell ch-repair-split">
            <div class="ch-repair-head">
                <p class="ch-repair-kicker">FIRST CHECK</p>
                <h2>先に確認する場所</h2>
                <p>
                    住宅リフォームを考える前に、まず「水が入る場所」「建物を支える場所」「生活に必要な設備」を確認します。
                    この3つを見ずに内装工事を始めると、後からやり直しになることがあります。
                </p>
            </div>

            <div class="ch-repair-check-list">
                <article>
                    <b>水が入る場所</b>
                    <span>屋根 / 外壁 / 窓 / 雨樋 / ベランダ</span>
                </article>
                <article>
                    <b>建物を支える場所</b>
                    <span>基礎 / 土台 / 柱 / 床下 / 白蟻被害</span>
                </article>
                <article>
                    <b>生活に必要な設備</b>
                    <span>給排水管 / 浴室 / トイレ / キッチン / 電気</span>
                </article>
            </div>
        </div>
    </section>

    <section class="ch-repair-section is-white">
        <div class="ch-shell">
            <div class="ch-repair-head">
                <p class="ch-repair-kicker">PRIORITY</p>
                <h2>修理は、見た目ではなく優先順位で決める。</h2>
                <p>
                    全部を一度に直す必要はありません。ただし、放置すると建物に影響する部分は先に対応します。
                    「今すぐ直す場所」と「後で直せる場所」を分けることが大切です。
                </p>
            </div>

            <div class="ch-repair-priority__list">
                <article>
                    <b>最優先</b>
                    <h3>雨漏り・漏水・構造の傷み</h3>
                    <p>放置すると修理範囲が広がるため、最初に確認します。</p>
                </article>

                <article>
                    <b>次に確認</b>
                    <h3>屋根・外壁・床下・水回り</h3>
                    <p>生活への影響と建物への負担を見て、順番を決めます。</p>
                </article>

                <article>
                    <b>計画して進める</b>
                    <h3>断熱・内装・間取り変更</h3>
                    <p>暮らしやすさを上げる工事は、予算に合わせて段階的に進めます。</p>
                </article>
            </div>
        </div>
    </section>

    <section class="ch-repair-section">
        <div class="ch-shell ch-repair-flow">
            <div class="ch-repair-head">
                <p class="ch-repair-kicker">FLOW</p>
                <h2>住まいの修理の進め方</h2>
                <p>
                    現地確認から、修理箇所の整理、優先順位、見積もり、工事までを段階的に進めます。
                </p>
            </div>

            <ol class="ch-repair-flow__steps">
                <li>
                    <span>01</span>
                    <div>
                        <h3>困っている場所を確認</h3>
                        <p>雨漏り、床の沈み、水回り、寒さ、外壁の傷みなどを聞き取ります。</p>
                    </div>
                </li>

                <li>
                    <span>02</span>
                    <div>
                        <h3>現地で建物を確認</h3>
                        <p>屋根、外壁、室内、床下、水回り、建具などを確認します。</p>
                    </div>
                </li>

                <li>
                    <span>03</span>
                    <div>
                        <h3>修理の優先順位を整理</h3>
                        <p>今すぐ必要な修理と、後からでもよい修理を分けます。</p>
                    </div>
                </li>

                <li>
                    <span>04</span>
                    <div>
                        <h3>予算に合わせて工事内容を決める</h3>
                        <p>全部直すのか、段階的に直すのか、費用と内容を整理します。</p>
                    </div>
                </li>
            </ol>
        </div>
    </section>

    <section class="ch-repair-section is-white">
        <div class="ch-shell">
            <div class="ch-repair-head">
                <p class="ch-repair-kicker">COST PLAN</p>
                <h2>修理費用は、優先順位で整理する。</h2>
                <p>
                    経年劣化が気になる住まいは、ひとつの修理だけで終わらないことがあります。
                    だから、屋根・外壁・水回り・内装を別々に考えるのではなく、
                    先に全体を確認して「今すぐ直す工事」と「後でよい工事」に分けます。
                </p>
            </div>

            <div class="ch-repair-cost-cards">
                <article>
                    <h3>すぐ直す工事</h3>
                    <p>雨漏り、漏水、構造の傷みなど、放置すると被害が広がる工事。</p>
                </article>
                <article>
                    <h3>近いうちに必要な工事</h3>
                    <p>屋根、外壁、水回り、床下など、劣化の進み方を見て計画する工事。</p>
                </article>
                <article>
                    <h3>後からでもよい工事</h3>
                    <p>内装、収納、建具など、生活しながら段階的に進められる工事。</p>
                </article>
            </div>
        </div>
    </section>

    <section class="ch-repair-section">
        <div class="ch-shell">
            <div class="ch-repair-prep">
                <div class="ch-repair-head">
                    <p class="ch-repair-kicker">BEFORE CONSULTATION</p>
                    <h2>相談前に、修理したい場所を整理する。</h2>
                    <p>
                        住まいの修理は、現地確認の前に「どこが気になるか」「いつから症状があるか」を整理しておくと、
                        必要な修理と後回しにできる工事を判断しやすくなります。
                    </p>
                </div>

                <div class="ch-repair-prep__grid">
                    <article>
                        <h3>気になる症状</h3>
                        <p>雨漏り、床の沈み、外壁のひび、水回りの漏水、寒さ、結露など。</p>
                    </article>

                    <article>
                        <h3>症状が出た時期</h3>
                        <p>いつから気になっているか、雨の日だけか、冬だけかなどを整理します。</p>
                    </article>

                    <article>
                        <h3>過去の修理履歴</h3>
                        <p>屋根、外壁、水回り、給排水管、白蟻対策など、過去に直した場所を確認します。</p>
                    </article>

                    <article>
                        <h3>予算と優先順位</h3>
                        <p>全部直すのか、今必要な場所だけ直すのか、段階的に進めるのかを考えます。</p>
                    </article>
                </div>
            </div>
        </div>
    </section>

    <section class="ch-repair-section is-white ch-repair-compare" id="chuko-repair-renovation">
        <div class="ch-shell">
            <div class="ch-repair-head">
                <p class="ch-repair-kicker">REPAIR OR RENOVATION</p>
                <h2>修理とリノベーションは分けて考える。</h2>
                <p>
                    年数が経過したお住まいでは、まず建物を守るための「修理」を優先します。
                    その上で、暮らしやすさを変える「リノベーション」を計画します。
                    同時に考えることはできますが、目的と優先順位は分けて整理します。
                </p>
            </div>

            <div class="ch-repair-table-wrap" role="region" aria-label="修理とリノベーションの違い">
                <table class="ch-repair-table">
                    <thead>
                        <tr>
                            <th>項目</th>
                            <th>修理・補修</th>
                            <th>リノベーション</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th>目的</th>
                            <td>傷みや不具合を直して、家を安全に使える状態に戻す。</td>
                            <td>間取り・内装・設備を変えて、暮らしやすさを上げる。</td>
                        </tr>
                        <tr>
                            <th>優先度</th>
                            <td>雨漏り、漏水、床下、屋根、外壁などは先に確認する。</td>
                            <td>修理が必要な場所を確認した後、予算に合わせて計画する。</td>
                        </tr>
                        <tr>
                            <th>対象</th>
                            <td>屋根、外壁、基礎、床下、水回り、配管、断熱、建具。</td>
                            <td>間取り変更、内装、収納、キッチン交換、浴室交換、デザイン変更。</td>
                        </tr>
                        <tr>
                            <th>判断基準</th>
                            <td>放置すると被害が広がるか。生活に支障があるか。</td>
                            <td>暮らし方に合うか。使いやすくなるか。将来の使い方に合うか。</td>
                        </tr>
                        <tr>
                            <th>進め方</th>
                            <td>現地確認 → 劣化箇所の整理 → 優先順位 → 必要な修理。</td>
                            <td>希望整理 → 修理範囲確認 → 予算調整 → 改修内容を決定。</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <p class="ch-repair-table-note">※ 先に修理が必要な場所を確認すると、リノベーションのやり直しや追加費用を防ぎやすくなります。</p>
        </div>
    </section>

</div>
