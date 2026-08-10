<?php

/**
 * WordPress を経由せず、このPHPが直接呼ばれた場合は終了する。
 *
 * このファイルは単独実行するものではなく、
 *
 * functions.php
 *   → minpaku/init.php
 *   → minpaku/inc/context.php
 *
 * の順で読み込まれることを前提とする。
 */
if (!defined('ABSPATH')) {
    exit;
}

/* === MINPAKU CONTEXT SINGLE SOURCE START === */

/**
 * ============================================================
 * 民泊領域の共通コンテキスト判定
 * ============================================================
 *
 * この関数を「民泊ページかどうか」の判定の正本とする。
 *
 * 対象:
 *
 * - /minpaku/
 * - /minpaku-stay/
 * - /minpaku-campaign/
 * - /minpaku-guide/
 * - /minpaku-flow/
 * - その他 slug に "minpaku" を含む固定ページ
 * - minpaku カスタム投稿アーカイブ
 * - minpaku カスタム投稿詳細
 * - 民泊専用ページテンプレート
 *
 *
 * 【設計上のルール】
 *
 * functions.php や各ページテンプレートに
 * 民泊判定を重複して書かない。
 *
 * 民泊関連で
 *
 *   「このページは民泊領域か？」
 *
 * を判断するときは、この関数を利用する。
 *
 *
 * functions.php
 *      ↓
 * minpaku/init.php
 *      ↓
 * minpaku/inc/context.php  ← 判定の正本
 *
 * という読み込み構造にする。
 */
if (!function_exists('naigai_is_minpaku_context')) {

    function naigai_is_minpaku_context()
    {
        /*
         * 現在表示しているWordPressオブジェクトから
         * 固定ページ等のslugを取得する。
         */
        $queried_obj = get_queried_object();
        $current_page_slug = '';

        if ($queried_obj instanceof WP_Post) {

            $current_page_slug =
                (string) $queried_obj->post_name;

        } elseif ($queried_obj instanceof WP_Term) {

            $current_page_slug =
                (string) $queried_obj->slug;

        } else {

            /*
             * get_queried_object() から取れなかった場合の保険。
             */
            $post_obj = get_post();

            if ($post_obj instanceof WP_Post) {
                $current_page_slug =
                    (string) get_post_field(
                        'post_name',
                        $post_obj
                    );
            }
        }


        /*
         * ----------------------------------------------------
         * 民泊領域と判定する条件
         * ----------------------------------------------------
         *
         * 1. minpaku投稿一覧
         * 2. minpaku投稿詳細
         * 3. 民泊専用テンプレート
         * 4. slug に minpaku を含む固定ページ
         *
         * 4 を入れているため、新しく
         *
         *   /minpaku-xxxx/
         *
         * を作っても個別条件を追加する必要がない。
         */
        return (
            is_post_type_archive('minpaku')
            || is_singular('minpaku')
            || is_page_template('page-minpaku-b2c.php')
            || is_page_template('page-minpaku-support.php')
            || (
                $current_page_slug !== ''
                && strpos(
                    $current_page_slug,
                    'minpaku'
                ) !== false
            )
        );
    }
}


/**
 * ============================================================
 * 民泊領域 共通 body class
 * ============================================================
 *
 * 民泊領域では <body> に
 *
 *   naigai-minpaku-context
 *
 * を追加する。
 *
 * これによりCSS側では、
 *
 *   body.naigai-minpaku-context ...
 *
 * を入口として、
 *
 * - PC header
 * - mobile header
 * - drawer
 * - navigation
 * - 共通layer
 *
 * をページごとに条件を書くことなく共通管理できる。
 *
 *
 * 【重要】
 *
 * Hero・カード・campaign固有レイアウトなど
 * 「そのページだけの見た目」は、
 * 各ページ専用CSSに残す。
 *
 * header / nav / drawer のような
 * 民泊全体の共通処理だけを共通CSSへ集約する。
 */
if (!function_exists('naigai_minpaku_add_context_body_class')) {

    function naigai_minpaku_add_context_body_class($classes)
    {
        if (naigai_is_minpaku_context()) {
            $classes[] = 'naigai-minpaku-context';
        }

        /*
         * 同じclassが複数回追加された場合の保険。
         */
        return array_values(
            array_unique($classes)
        );
    }
}

add_filter(
    'body_class',
    'naigai_minpaku_add_context_body_class'
);

/* === MINPAKU CONTEXT SINGLE SOURCE END === */
