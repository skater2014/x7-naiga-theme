<?php
/**
 * iez_plan 追加タクソノミー
 *
 * ============================================================
 * このファイルの役割
 * ============================================================
 *
 * 家づくりの「参考プラン」を、ユーザーが探しやすくするための
 * 分類を登録する。
 *
 * 既存:
 * - 工事区分       新築 / リフォーム
 * - 建物形状       平屋 / 2階建て
 *
 * 今回追加:
 * - 間取り         2LDK / 3LDK / 4LDK など
 * - 延床面積帯     50㎡未満 / 50〜70㎡ / 70〜100㎡ / 100㎡以上
 *
 * ------------------------------------------------------------
 * 重要
 * ------------------------------------------------------------
 *
 * 「68.5㎡」などの実際の延床面積を置き換えるものではない。
 *
 * 実際の数値:
 *     68.5㎡
 *
 * 検索用taxonomy:
 *     50〜70㎡
 *
 * の両方を持つ。
 *
 * これにより、
 *
 *     50〜70㎡
 *        ↓
 *     同じ面積帯の参考プラン一覧
 *
 * というリンクを作れる。
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action(
    'init',
    'naigai_iez_plan_register_extra_taxonomies',
    20
);


/**
 * 追加taxonomyをWordPressへ登録する。
 */
function naigai_iez_plan_register_extra_taxonomies() {

    /*
     * ========================================================
     * 工事区分
     * ========================================================
     *
     * 現時点ではカードの主要検索項目としては使用しない。
     * 既存データとの互換性のため登録は維持する。
     */
    register_taxonomy(
        'iez_plan_scope',
        array('iez_plan'),
        array(
            'labels' => array(
                'name'          => '工事区分',
                'singular_name' => '工事区分',
                'search_items'  => '工事区分を検索',
                'all_items'     => 'すべての工事区分',
                'edit_item'     => '工事区分を編集',
                'update_item'   => '工事区分を更新',
                'add_new_item'  => '工事区分を追加',
                'new_item_name' => '新しい工事区分',
                'menu_name'     => '工事区分',
            ),
            'public'            => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'hierarchical'      => false,
            'rewrite'           => array(
                'slug'       => 'iezukuri/plan-scope',
                'with_front' => false,
            ),
            'show_in_rest' => true,
        )
    );


    /*
     * ========================================================
     * 建物形状
     * ========================================================
     *
     * 平屋 / 2階建てを住宅タイプとは分けて管理する。
     */
    register_taxonomy(
        'iez_plan_building_form',
        array('iez_plan'),
        array(
            'labels' => array(
                'name'          => '建物形状',
                'singular_name' => '建物形状',
                'search_items'  => '建物形状を検索',
                'all_items'     => 'すべての建物形状',
                'edit_item'     => '建物形状を編集',
                'update_item'   => '建物形状を更新',
                'add_new_item'  => '建物形状を追加',
                'new_item_name' => '新しい建物形状',
                'menu_name'     => '建物形状',
            ),
            'public'            => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'hierarchical'      => false,
            'rewrite'           => array(
                'slug'       => 'iezukuri/plan-building-form',
                'with_front' => false,
            ),
            'show_in_rest' => true,
        )
    );


    /*
     * ========================================================
     * 間取り
     * ========================================================
     *
     * _ch_plan_layout に保存している
     *
     *     2LDK
     *     3LDK
     *     4LDK
     *
     * などを検索用taxonomyとして持たせる。
     *
     * カード上では、
     *
     *     間取り  2LDK
     *
     * の「2LDK」をリンクにする予定。
     */
    register_taxonomy(
        'iez_plan_layout',
        array('iez_plan'),
        array(
            'labels' => array(
                'name'          => '間取り',
                'singular_name' => '間取り',
                'search_items'  => '間取りを検索',
                'all_items'     => 'すべての間取り',
                'edit_item'     => '間取りを編集',
                'update_item'   => '間取りを更新',
                'add_new_item'  => '間取りを追加',
                'new_item_name' => '新しい間取り',
                'menu_name'     => '間取り',
            ),
            'public'            => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'hierarchical'      => false,
            'rewrite'           => array(
                'slug'       => 'iezukuri/plan-layout',
                'with_front' => false,
            ),
            'show_in_rest' => true,
        )
    );


    /*
     * ========================================================
     * 延床面積帯
     * ========================================================
     *
     * 68.5㎡ / 109.3㎡などの実数ではなく、
     * 住宅プランを比較しやすい範囲へ分類する。
     *
     *     50㎡未満
     *     50〜70㎡
     *     70〜100㎡
     *     100㎡以上
     *
     * 実際の延床面積表示は今まで通り残す。
     */
    register_taxonomy(
        'iez_plan_area',
        array('iez_plan'),
        array(
            'labels' => array(
                'name'          => '延床面積帯',
                'singular_name' => '延床面積帯',
                'search_items'  => '延床面積帯を検索',
                'all_items'     => 'すべての延床面積帯',
                'edit_item'     => '延床面積帯を編集',
                'update_item'   => '延床面積帯を更新',
                'add_new_item'  => '延床面積帯を追加',
                'new_item_name' => '新しい延床面積帯',
                'menu_name'     => '延床面積帯',
            ),
            'public'            => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'hierarchical'      => true,
            'rewrite'           => array(
                'slug'       => 'iezukuri/plan-area',
                'with_front' => false,
            ),
            'show_in_rest' => true,
        )
    );


    /*
     * taxonomy登録後に固定termを用意する。
     */
    naigai_iez_plan_ensure_extra_terms();
}


/**
 * 固定して使うtermを作成する。
 *
 * すでに存在するtermは作り直さないため、
 * WordPressを開くたびに重複することはない。
 */
function naigai_iez_plan_ensure_extra_terms() {

    $terms = array(

        'iez_plan_scope' => array(
            'new-build'  => '新築',
            'renovation' => 'リフォーム',
        ),

        'iez_plan_building_form' => array(
            'hiraya'    => '平屋',
            'two-story' => '2階建て',
        ),

        'iez_plan_area' => array(
            'under-50sqm' => '50㎡未満',
            '50-70sqm'    => '50〜70㎡',
            '70-100sqm'   => '70〜100㎡',
            '100sqm-plus' => '100㎡以上',
        ),
    );

    foreach ($terms as $taxonomy => $items) {

        foreach ($items as $slug => $name) {

            if (!term_exists($slug, $taxonomy)) {

                wp_insert_term(
                    $name,
                    $taxonomy,
                    array(
                        'slug' => $slug,
                    )
                );
            }
        }
    }
}


/**
 * ============================================================
 * 間取り・延床面積帯を自動同期
 * ============================================================
 *
 * 管理画面で同じ情報を二重入力させないための処理。
 *
 *
 * 【間取り】
 *
 * 管理画面:
 *
 *     間取り = 2LDK
 *
 *         ↓ 自動
 *
 * taxonomy:
 *
 *     2LDK
 *
 *
 * 【延床面積】
 *
 * 管理画面:
 *
 *     延床面積 = 68.5㎡
 *
 *         ↓ 自動
 *
 * taxonomy:
 *
 *     50〜70㎡
 *
 *
 * したがって管理者は、
 * 今まで通り「間取り」「延床面積」を入力するだけでよい。
 */
function naigai_iez_plan_sync_search_taxonomies($post_id) {

    if (get_post_type($post_id) !== 'iez_plan') {
        return;
    }

    if (
        defined('DOING_AUTOSAVE')
        && DOING_AUTOSAVE
    ) {
        return;
    }

    if (wp_is_post_revision($post_id)) {
        return;
    }


    /*
     * ========================================================
     * 間取り
     * ========================================================
     */
    $layout = trim(
        (string) get_post_meta(
            $post_id,
            '_ch_plan_layout',
            true
        )
    );

    if ($layout !== '') {

        wp_set_object_terms(
            $post_id,
            array($layout),
            'iez_plan_layout',
            false
        );

    } else {

        wp_set_object_terms(
            $post_id,
            array(),
            'iez_plan_layout',
            false
        );
    }


    /*
     * ========================================================
     * 延床面積帯
     * ========================================================
     *
     * 「約68.5㎡」のような文字列でも、
     * 最初の数値部分だけを取得する。
     */
    $area_text = (string) get_post_meta(
        $post_id,
        '_ch_plan_total_area',
        true
    );

    $area_text = str_replace(
        array(',', '，'),
        '',
        $area_text
    );

    if (
        preg_match(
            '/([0-9]+(?:\.[0-9]+)?)/',
            $area_text,
            $matches
        )
    ) {

        $sqm = (float) $matches[1];

        /*
         * 境界値は重複させない。
         *
         * 70㎡ちょうど
         * → 70〜100㎡
         *
         * 100㎡ちょうど
         * → 100㎡以上
         */
        if ($sqm < 50) {

            $area_slug = 'under-50sqm';

        } elseif ($sqm < 70) {

            $area_slug = '50-70sqm';

        } elseif ($sqm < 100) {

            $area_slug = '70-100sqm';

        } else {

            $area_slug = '100sqm-plus';
        }

        wp_set_object_terms(
            $post_id,
            array($area_slug),
            'iez_plan_area',
            false
        );

    } else {

        wp_set_object_terms(
            $post_id,
            array(),
            'iez_plan_area',
            false
        );
    }
}

add_action(
    'save_post_iez_plan',
    'naigai_iez_plan_sync_search_taxonomies',
    30
);