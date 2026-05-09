<?php
/**
 * =========================================================
 * plan-archive.php
 *
 * 対象:
 * - カスタム投稿タイプ: iez_plan
 *
 * 役割:
 * - iez_plan の一覧URLを /iezukuri/plans/ にする
 * - iez_plan の詳細URLを /iezukuri/plan/{slug}/ にする
 *
 * URL設計:
 * - 一覧: /iezukuri/plans/
 * - 詳細: /iezukuri/plan/test-hiraya-b/
 *
 * 注意:
 * - このファイルは「投稿タイプ登録より前」に読み込む必要がある
 * - 固定ページ /iezukuri/plans/ が公開状態だとURL衝突する
 * =========================================================
 */

if (!defined('ABSPATH')) {
    exit;
}

add_filter('register_post_type_args', function ($args, $post_type) {
    if ($post_type !== 'iez_plan') {
        return $args;
    }

    $args['public']             = true;
    $args['publicly_queryable'] = true;
    $args['show_ui']            = true;
    $args['has_archive']        = 'iezukuri/plans';

    $args['rewrite'] = array(
        'slug'       => 'iezukuri/plan',
        'with_front' => false,
    );

    return $args;
}, 20, 2);
