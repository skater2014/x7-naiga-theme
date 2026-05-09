<?php
if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/migrate-runner.php';

function naigai_run_hub_migration_v1($force = false)
{
    $group   = 'hub';
    $version = 'v1';

    if (!$force && naigai_migrate_has_run($group, $version)) {
        naigai_migrate_log('hub v1 already done');
        return;
    }

    $contact_id     = naigai_migrate_page_id('contact');
    $reservation_id = naigai_migrate_page_id('reservation');

    $config = array(
        'construction-hub' => array(
            '_hub_kicker' => '建設業のご相談',
            '_hub_title' => '建設業窓口',
            '_hub_lead' => '家を建てたい、施工実例を見たい、どんな家づくりができるか知りたい。そんな方に向けて、家づくりに関する情報をまとめています。',
            '_hub_gateway_title' => 'こちらからご覧いただけます',
            '_hub_secondary_title' => '家づくりの流れもご案内します',
            '_hub_secondary_text' => '相談から完成までの流れや、比較しながら見たい方向けの案内をここに追加していきます。',
            '_hub_cta_title' => '相談してみたい方はこちら',
            '_hub_cta_primary_label' => 'お問い合わせ',
            '_hub_cta_secondary_label' => '来店予約',
            '_hub_cta_primary_url' => home_url('/contact/'),
            '_hub_cta_secondary_url' => home_url('/reservation/'),
            '_hub_cta_primary_page_id' => $contact_id,
            '_hub_cta_secondary_page_id' => $reservation_id,
            'links' => array(
                1 => array('title' => '注文住宅一覧を見る', 'text' => '建築の考え方や住宅情報を見たい方へ。', 'url' => home_url('/naigai-construction/'), 'page_id' => 0),
                2 => array('title' => '施工実例・住宅一覧を見る', 'text' => '実例や住まいのイメージを見たい方へ。', 'url' => home_url('/nasu-jutaku/'), 'page_id' => 0),
                3 => array('title' => '在来工法について見る', 'text' => '在来工法の特徴を知りたい方へ。', 'url' => home_url('/zairai/'), 'page_id' => naigai_migrate_page_id('zairai')),
                4 => array('title' => '枠組壁工法について見る', 'text' => '枠組壁工法の特徴を知りたい方へ。', 'url' => home_url('/wakugumi/'), 'page_id' => naigai_migrate_page_id('wakugumi')),
                5 => array('title' => '施工実例ページへ進む', 'text' => '施工事例をまとめて見たい方へ。', 'url' => home_url('/sekoujirei/'), 'page_id' => naigai_migrate_page_id('sekoujirei')),
            ),
        ),
        'realestate-hub' => array(
            '_hub_kicker' => '不動産のご相談',
            '_hub_title' => '不動産業窓口',
            '_hub_lead' => '土地を探したい、売却を考えたい、査定をしてみたい。不動産に関する入口を、わかりやすくまとめたご案内ページです。',
            '_hub_gateway_title' => 'こちらから探せます',
            '_hub_secondary_title' => '相談内容からも選べます',
            '_hub_secondary_text' => '土地購入、売却、買取、住み替えなど、目的別の導線をここに追加していきます。',
            '_hub_cta_title' => 'まずは相談したい方はこちら',
            '_hub_cta_primary_label' => 'お問い合わせ',
            '_hub_cta_secondary_label' => '来店予約',
            '_hub_cta_primary_url' => home_url('/contact/'),
            '_hub_cta_secondary_url' => home_url('/reservation/'),
            '_hub_cta_primary_page_id' => $contact_id,
            '_hub_cta_secondary_page_id' => $reservation_id,
            'links' => array(
                1 => array('title' => '土地を探す', 'text' => '那須エリアの土地情報を見たい方へ。', 'url' => home_url('/naigai-tochi/'), 'page_id' => 0),
                2 => array('title' => '売却査定をしてみる', 'text' => 'まずは価格感を知りたい方へ。', 'url' => home_url('/satei/'), 'page_id' => naigai_migrate_page_id('satei')),
                3 => array('title' => '売却・買取を相談する', 'text' => '売却や買取の相談をしたい方へ。', 'url' => home_url('/contact/'), 'page_id' => $contact_id),
                4 => array('title' => '不動産コラムを読む', 'text' => '不動産の基礎知識を知りたい方へ。', 'url' => home_url('/fudosan-column/'), 'page_id' => 0),
                5 => array('title' => '来店予約をする', 'text' => '直接相談したい方はこちら。', 'url' => home_url('/reservation/'), 'page_id' => $reservation_id),
            ),
        ),
    );

    foreach ($config as $slug => $data) {
        $post_id = naigai_migrate_post_id_by_slug($slug, 'page');
        if ($post_id <= 0) {
            naigai_migrate_log("skip {$slug}");
            continue;
        }

        foreach ($data as $key => $value) {
            if ($key === 'links') {
                continue;
            }
            naigai_migrate_set_if_empty($post_id, $key, $value);
        }

        foreach ($data['links'] as $i => $link) {
            $base = "_hub_link_{$i}";
            naigai_migrate_set_if_empty($post_id, "{$base}_title", $link['title']);
            naigai_migrate_set_if_empty($post_id, "{$base}_text",  $link['text']);
            naigai_migrate_set_if_empty($post_id, "{$base}_url",   $link['url']);

            if (!empty($link['page_id'])) {
                naigai_migrate_set_if_empty($post_id, "{$base}_page_id", (int) $link['page_id']);
            }
        }
    }

    naigai_migrate_mark_done($group, $version);
    naigai_migrate_log('hub v1 done');
}
