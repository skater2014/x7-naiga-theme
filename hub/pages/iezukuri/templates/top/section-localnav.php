<?php
/*
 * 中間ナビ表示方式 / トップページ用
 *
 * このファイルは WordPress の nav location ではない。
 * template-iezukuri.php から include される、トップページ用の中間ナビ。
 *
 * 役割:
 * - ページ途中の回遊導線として表示する
 * - footer メニューとは別役割
 *
 * 表示方式:
 * - このテンプレート内の HTML / PHP でリンクを出す
 * - WordPress管理画面 > 外観 > メニュー の location では管理していない
 *
 * CSS:
 * - hub/pages/iezukuri/css/common/nav.css
 * - 主な対象クラス: .ch-localnav
 */

/**
 * hub/pages/iezukuri/templates/top/section-localnav.php
 *
 * /iezukuri/ トップの中間ナビ。
 * 役割:
 * - トップから主要サブページへ送る
 * - 既存CSS: iezukuri-nav.css / iezukuri.css のクラスを使う
 */

if (!defined('ABSPATH')) {
    exit;
}

$links = array(
    array(
        'label' => '考え方',
        'url'   => home_url('/iezukuri/concept/'),
    ),
    array(
        'label' => '設計方針',
        'url'   => home_url('/iezukuri/design-policy/'),
    ),
    array(
        'label' => '間取り',
        'url'   => home_url('/iezukuri/plans/'),
    ),
    array(
        'label' => '新築住宅',
        'url'   => home_url('/iezukuri/new-house'),
    ),
    array(
        'label' => '二世帯住宅',
        'url'   => home_url('/iezukuri/nisetai'),
    ),
    array(
        'label' => '住宅リフォーム',
        'url'   => home_url('/iezukuri/chuko'),
    ),
    array(
        'label' => '暮らしのポイント',
        'url'   => home_url('/iezukuri/works/'),
    ),
    array(
        'label' => '相談',
        'url'   => home_url('/iezukuri/contact/'),
    ),
);
?>

<nav class="ch-localnav ch-localnav--underline" aria-label="家づくりページ内ナビゲーション">
    <div class="ch-shell">
        <ul class="ch-localnav__list">
            <?php foreach ($links as $link) : ?>
                <li>
                    <a href="<?php echo esc_url($link['url']); ?>">
                        <?php echo esc_html($link['label']); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</nav>
