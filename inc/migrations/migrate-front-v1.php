<?php
if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/migrate-runner.php';

function naigai_run_front_migration_v1($force = false)
{
    $group   = 'front';
    $version = 'v1';

    if (!$force && naigai_migrate_has_run($group, $version)) {
        naigai_migrate_log('front v1 already done');
        return;
    }

    $front_id = (int) get_option('page_on_front');
    if ($front_id <= 0) {
        naigai_migrate_log('front page not set');
        return;
    }

    $contact_id           = naigai_migrate_page_id('contact');
    $construction_hub_id  = naigai_migrate_page_id('construction-hub');
    $realestate_hub_id    = naigai_migrate_page_id('realestate-hub');

    $data = array(
        '_hub_kicker'            => '総合案内',
        '_hub_title'             => get_the_title($front_id),
        '_hub_lead'              => '那須エリアで住まい・土地・建設・不動産の相談先を探している方に向けた総合案内ページです。',
        '_hub_gateway_title'     => '目的から探す',
        '_hub_secondary_title'   => 'まずは相談からでも大丈夫です',
        '_hub_secondary_text'    => '不動産・建設・住まいに関する導線をまとめて確認できます。',
        '_hub_cta_title'         => '相談したい方はこちら',
        '_hub_cta_primary_label' => 'お問い合わせ',
        '_hub_cta_primary_url'   => home_url('/contact/'),
        '_hub_cta_primary_page_id' => $contact_id,
    );

    foreach ($data as $key => $value) {
        if ($value === 0 || $value === '') {
            continue;
        }
        naigai_migrate_set_if_empty($front_id, $key, $value);
    }

    $cards = array(
        1 => array(
            'title'   => '不動産業窓口',
            'text'    => '土地探し、売却、査定など不動産の相談はこちら。',
            'url'     => home_url('/realestate-hub/'),
            'page_id' => $realestate_hub_id,
        ),
        2 => array(
            'title'   => '建設業窓口',
            'text'    => '家づくり、施工実例、工法の案内はこちら。',
            'url'     => home_url('/construction-hub/'),
            'page_id' => $construction_hub_id,
        ),
        3 => array(
            'title'   => '民泊・宿泊案内',
            'text'    => '宿泊や民泊の利用案内はこちら。',
            'url'     => home_url('/minpaku-stay/'),
            'page_id' => 0,
        ),
        4 => array(
            'title'   => 'まずはお問い合わせ',
            'text'    => '迷ったら相談からでも大丈夫です。',
            'url'     => home_url('/contact/'),
            'page_id' => $contact_id,
        ),
    );

    foreach ($cards as $i => $card) {
        $base = "_hub_card_{$i}";
        naigai_migrate_set_if_empty($front_id, "{$base}_title", $card['title']);
        naigai_migrate_set_if_empty($front_id, "{$base}_text",  $card['text']);
        naigai_migrate_set_if_empty($front_id, "{$base}_url",   $card['url']);

        if (!empty($card['page_id'])) {
            naigai_migrate_set_if_empty($front_id, "{$base}_page_id", (int) $card['page_id']);
        }
    }

    naigai_migrate_mark_done($group, $version);
    naigai_migrate_log('front v1 done');
}
