<?php
/**
 * /iezukuri/plans/
 *
 * 参考プラン一覧ページ
 */

if (!defined('ABSPATH')) {
    exit;
}

naigai_iezukuri_render_blocks(array(
    array(
        'name' => 'intro',
        'args' => array(
            'kicker' => 'PLAN',
            'title'  => '間取り・坪数から参考プランを探す。',
            'lead'   => '平屋、2階建て、二世帯など、暮らし方に合わせた住まいの参考プランを一覧で確認できます。',
        ),
    ),
    array(
        'name' => 'plan-list',
    ),
));
