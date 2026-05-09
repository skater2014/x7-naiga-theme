<?php
/**
 * /iezukuri 選択型LPパネル
 *
 * 役割:
 * - 5種類のLPパネルをHTMLとして全部出す
 * - JSで選択されたものだけ表示する
 * - Ajaxは使わない
 */

if (!defined('ABSPATH')) {
    exit;
}

$life_panels = array(
    'newlywed' => array(
        'label' => 'New House',
        'title' => '新婚・一世帯の新築',
        'lead'  => '初めての家づくり。水回り、収納、将来の子ども部屋まで、暮らしの変化を見越して考えます。',
        'cta'   => '新築住宅を詳しく見る',
        'url'   => home_url('/iezukuri/new-home/'),
    ),
    'nisetai' => array(
        'label' => 'Two Family',
        'title' => '二世帯住宅',
        'lead'  => '親世帯と子世帯など、2つの世帯が同じ家や同じ敷地で暮らす住まいです。',
        'cta'   => '二世帯住宅を詳しく見る',
        'url'   => home_url('/iezukuri/nisetai'),
    ),
    'dual-life' => array(
        'label' => 'Dual Life',
        'title' => '二拠点生活',
        'lead'  => '都市部と那須など、暮らしの拠点を分ける住まい方。週末住宅やセカンドハウスもここに含みます。',
        'cta'   => '二拠点生活を相談する',
        'url'   => home_url('/contact/'),
    ),
    'retirement' => array(
        'label' => 'Retirement',
        'title' => '定年後の暮らし',
        'lead'  => '平屋、小さく暮らす、掃除しやすい、管理しやすい。将来を見越した住まい方です。',
        'cta'   => '定年後の暮らしを相談する',
        'url'   => home_url('/contact/'),
    ),
    'renovation' => array(
        'label' => 'Renovation',
        'title' => '中古住宅リフォーム',
        'lead'  => '今ある家や中古住宅を活かし、断熱、水回り、動線、収納を整える住まい方です。',
        'cta'   => '中古住宅リフォームを詳しく見る',
        'url'   => home_url('/iezukuri/chuko'),
    ),
);
?>

<section class="ch-section ch-life-panels" aria-label="選択された暮らし方の内容">
    <div class="ch-shell">
        <?php foreach ($life_panels as $key => $panel) : ?>
            <article class="ch-life-panel<?php echo $key === 'newlywed' ? ' is-active' : ''; ?>" data-life-panel="<?php echo esc_attr($key); ?>">
                <div class="ch-life-panel__head">
                    <p class="ch-eyebrow"><?php echo esc_html($panel['label']); ?></p>
                    <h2 class="ch-section-title"><?php echo esc_html($panel['title']); ?></h2>
                    <p><?php echo esc_html($panel['lead']); ?></p>
                </div>

                <div class="ch-life-panel__grid">
                    <section class="ch-life-panel__card">
                        <h3>向いている人</h3>
                        <p>ここに後で内容を入れます。</p>
                    </section>

                    <section class="ch-life-panel__card">
                        <h3>間取り</h3>
                        <p>将来的に家族構成に応じた間取りカードを埋め込みます。</p>
                    </section>

                    <section class="ch-life-panel__card">
                        <h3>Q&amp;A</h3>
                        <p>この暮らし方でよくある質問を表示します。</p>
                    </section>
                </div>

                <div class="ch-life-panel__cta">
                    <a class="ch-btn ch-btn--primary" href="<?php echo esc_url($panel['url']); ?>">
                        <?php echo esc_html($panel['cta']); ?>
                    </a>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>
