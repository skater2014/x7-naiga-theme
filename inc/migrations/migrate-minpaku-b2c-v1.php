<?php
if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/migrate-runner.php';

function naigai_run_minpaku_b2c_migration_v1($force = false)
{
    $group   = 'minpaku_b2c';
    $version = 'v1';

    if (!$force && naigai_migrate_has_run($group, $version)) {
        naigai_migrate_log('minpaku_b2c v1 already done');
        return;
    }

    $config = array(
        'minpaku-guide' => array(
            '_mpb_hero_eyebrow' => 'MINPAKU STAY',
            '_mpb_hero_title'   => '那須での過ごし方と宿泊の楽しみ方',
            '_mpb_hero_text'    => '那須での滞在をもっと楽しみたい方へ。民泊・一棟貸しの魅力や、過ごし方のヒントをまとめています。',
            '_mpb_intro_title'  => 'こんな方におすすめ',
            '_mpb_intro_text'   => '観光だけでなく、暮らすように滞在したい方、家族やグループでゆったり過ごしたい方に向いています。',
            '_mpb_cta_label'    => '宿泊先一覧を見る',
            '_mpb_cta_url'      => home_url('/minpaku-stay/'),
        ),
        'minpaku-family' => array(
            '_mpb_hero_eyebrow' => 'MINPAKU STAY',
            '_mpb_hero_title'   => '家族で那須の民泊・一棟貸しに泊まる',
            '_mpb_hero_text'    => '家族旅行や三世代の滞在にも使いやすい、那須エリアの民泊・一棟貸しの魅力をまとめています。',
            '_mpb_intro_title'  => '家族旅行で選ばれる理由',
            '_mpb_intro_text'   => '部屋を分けて使いやすいこと、周囲に気を使いすぎずに過ごしやすいことが魅力です。',
            '_mpb_cta_label'    => '家族向けの宿泊先を見る',
            '_mpb_cta_url'      => home_url('/minpaku-stay/'),
        ),
        'minpaku-group' => array(
            '_mpb_hero_eyebrow' => 'MINPAKU STAY',
            '_mpb_hero_title'   => 'グループで那須の民泊・一棟貸しに泊まる',
            '_mpb_hero_text'    => '友人同士や複数人の旅行で使いやすい、グループ向けの民泊・一棟貸しの使い方をご案内します。',
            '_mpb_intro_title'  => 'グループ滞在に向くポイント',
            '_mpb_intro_text'   => '人数に合わせて動きやすく、共用スペースで集まりやすいことがメリットです。',
            '_mpb_cta_label'    => 'グループ向けの宿泊先を見る',
            '_mpb_cta_url'      => home_url('/minpaku-stay/'),
        ),
        'minpaku-workation' => array(
            '_mpb_hero_eyebrow' => 'MINPAKU STAY',
            '_mpb_hero_title'   => 'ワーケーションで那須の民泊に泊まる',
            '_mpb_hero_text'    => '仕事と滞在を両立したい方へ。那須でのワーケーション利用に向けた宿泊の考え方をまとめています。',
            '_mpb_intro_title'  => 'ワーケーション向けの使い方',
            '_mpb_intro_text'   => '滞在のしやすさ、落ち着いた環境、長めの利用にも向く使い方を確認できます。',
            '_mpb_cta_label'    => 'ワーケーション向けの宿泊先を見る',
            '_mpb_cta_url'      => home_url('/minpaku-stay/'),
        ),
        'minpaku-campaign' => array(
            '_mpb_hero_eyebrow' => 'MINPAKU STAY',
            '_mpb_hero_title'   => '那須の民泊・一棟貸しのお得情報',
            '_mpb_hero_text'    => '季節や滞在スタイルに合わせた、お得な利用情報や導線をまとめています。',
            '_mpb_intro_title'  => 'お得情報の見方',
            '_mpb_intro_text'   => '時期や条件に合わせて、ぴったりの宿泊先を選びやすくするための案内です。',
            '_mpb_cta_label'    => '宿泊先一覧を見る',
            '_mpb_cta_url'      => home_url('/minpaku-stay/'),
        ),
        'minpaku-faq' => array(
            '_mpb_hero_eyebrow' => 'FAQ',
            '_mpb_hero_title'   => '那須の民泊・一棟貸し よくある質問',
            '_mpb_hero_text'    => '予約前によくある質問を、わかりやすくまとめています。',
            '_mpb_intro_title'  => '予約前に確認したいこと',
            '_mpb_intro_text'   => '宿泊前の不安や確認したい点を先に見られるようにしています。',
            '_mpb_cta_label'    => '宿泊先一覧を見る',
            '_mpb_cta_url'      => home_url('/minpaku-stay/'),
        ),
        'minpaku-rules' => array(
            '_mpb_hero_eyebrow' => 'GUIDE',
            '_mpb_hero_title'   => '那須の民泊・一棟貸し ご利用案内・利用規約',
            '_mpb_hero_text'    => '安心して利用いただくためのルールや、事前に確認したい内容をまとめています。',
            '_mpb_intro_title'  => '事前に確認したいこと',
            '_mpb_intro_text'   => 'チェックイン、チェックアウト、注意事項など、滞在前に見ておきたい内容です。',
            '_mpb_cta_label'    => '宿泊先一覧を見る',
            '_mpb_cta_url'      => home_url('/minpaku-stay/'),
        ),
        'minpaku-flow' => array(
            '_mpb_hero_eyebrow' => 'FLOW',
            '_mpb_hero_title'   => '宿泊予約からオンライン決済までの流れ',
            '_mpb_hero_text'    => '予約から確認、支払いまでの流れを順番に確認できます。',
            '_mpb_intro_title'  => '予約の流れ',
            '_mpb_intro_text'   => '初めての方でもわかりやすいように、手順を整理しています。',
            '_mpb_cta_label'    => '宿泊先一覧を見る',
            '_mpb_cta_url'      => home_url('/minpaku-stay/'),
        ),
        'minpaku-difference' => array(
            '_mpb_hero_eyebrow' => 'COMPARE',
            '_mpb_hero_title'   => '民泊・一棟貸し・貸別荘の違い',
            '_mpb_hero_text'    => '宿泊スタイルの違いを知りたい方に向けて、選び方のポイントをまとめています。',
            '_mpb_intro_title'  => '違いを知って選ぶ',
            '_mpb_intro_text'   => '人数や目的に合わせて、どの滞在スタイルが向いているか比較しやすくしています。',
            '_mpb_cta_label'    => '宿泊先一覧を見る',
            '_mpb_cta_url'      => home_url('/minpaku-stay/'),
        ),
    );

    foreach ($config as $slug => $meta) {
        $post_id = naigai_migrate_post_id_by_slug($slug, 'page');

        if ($post_id <= 0) {
            naigai_migrate_log("skip {$slug}");
            continue;
        }

        naigai_migrate_set_if_empty($post_id, '_wp_page_template', 'page-minpaku-b2c.php');

        foreach ($meta as $key => $value) {
            naigai_migrate_set_if_empty($post_id, $key, $value);
        }
    }

    naigai_migrate_mark_done($group, $version);
    naigai_migrate_log('minpaku_b2c v1 done');
}
