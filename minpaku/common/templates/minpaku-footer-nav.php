<?php

/**
 * minpaku/common/templates/minpaku-footer-nav.php
 */

if (!defined('ABSPATH')) {
    exit;
}

$feature_links = array(
    array('label' => '民泊ガイド',      'url' => home_url('/minpaku-guide/')),
    array('label' => '違いを知る',      'url' => home_url('/minpaku-difference/')),
    array('label' => '家族で泊まる',    'url' => home_url('/minpaku-family/')),
    array('label' => 'グループ利用',    'url' => home_url('/minpaku-group/')),
    array('label' => 'ワーケーション',  'url' => home_url('/minpaku-workation/')),
);

$check_links = array(
    array('label' => '予約前の確認',      'url' => home_url('/minpaku-check/')),
    array('label' => '予約〜決済の流れ',  'url' => home_url('/minpaku-flow/')),
    array('label' => '利用案内・規約',    'url' => home_url('/minpaku-rules/')),
    array('label' => 'よくある質問',      'url' => home_url('/minpaku-faq/')),
    array('label' => '運営サポート',      'url' => home_url('/minpaku/')),
);
?>

<section class="mnpk-footer-nav minpaku-footer-nav" aria-label="民泊フッターナビ">
    <div class="mnpk-footer-nav__head">
        <p class="mnpk-footer-nav__eyebrow">MINPAKU FOOTER NAV</p>
        <h2 class="mnpk-footer-nav__title">那須の民泊を探す・知る・準備する</h2>
        <p class="mnpk-footer-nav__lead">
            宿泊先の比較だけでなく、違い、過ごし方、予約前の確認事項、ご利用案内までまとめてたどれるようにした導線です。
        </p>
    </div>

    <div class="mnpk-footer-nav__columns">
        <div class="mnpk-footer-nav__column">
            <h3>特集ページ</h3>
            <ul class="mnpk-footer-nav__links">
                <?php foreach ($feature_links as $link) : ?>
                    <li>
                        <a href="<?php echo esc_url($link['url']); ?>">
                            <span class="mnpk-footer-nav__label"><?php echo esc_html($link['label']); ?></span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="mnpk-footer-nav__column">
            <h3>宿泊前の確認</h3>
            <ul class="mnpk-footer-nav__links">
                <?php foreach ($check_links as $link) : ?>
                    <li>
                        <a href="<?php echo esc_url($link['url']); ?>">
                            <span class="mnpk-footer-nav__label"><?php echo esc_html($link['label']); ?></span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</section>