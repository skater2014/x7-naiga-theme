<?php
/**
 * ============================================================
 * /iezukuri/ トップ本文 管理画面編集データ
 * ============================================================
 *
 * 【このファイルがやること】
 *
 * WordPress管理画面
 *      ↓
 * top-metabox.php
 *      ↓
 * post_meta に保存
 *      ↓
 * このファイルで読み出す
 *      ↓
 * section-feature.php
 * section-works.php
 *      ↓
 * /iezukuri/ に表示
 *
 *
 * 【重要】
 *
 * このファイルは以下には触らない。
 *
 * - Intro / Skip処理
 * - Hero
 * - CTA
 * - Intro用JavaScript
 * - Intro用CSS
 *
 * つまり、
 * 「イントロがスキップできなくなる」
 * という部分とは完全に切り離している。
 * ============================================================
 */

if (!defined('ABSPATH')) {
    exit;
}


/**
 * ============================================================
 * メタデータ取得
 * ============================================================
 *
 * 管理画面でまだ一度も保存されていない場合だけ
 * $default を返す。
 *
 * すでに空文字で保存されている場合は、
 * 「ユーザーが空にした」と判断して空文字を返す。
 */
function naigai_iez_top_editable_meta($post_id, $key, $default = '')
{
    if (metadata_exists('post', $post_id, $key)) {
        return get_post_meta($post_id, $key, true);
    }

    return $default;
}


/**
 * ============================================================
 * 画像ID → URL
 * ============================================================
 */
function naigai_iez_top_editable_image($image_id)
{
    $image_id = absint($image_id);

    if (!$image_id) {
        return '';
    }

    $url = wp_get_attachment_image_url($image_id, 'large');

    return $url ? $url : '';
}


/**
 * ============================================================
 * 管理画面データをフロント用にまとめる
 * ============================================================
 */
function naigai_iez_top_editable_data($post_id)
{
    $post_id = absint($post_id);


    /**
     * --------------------------------------------------------
     * 1. 「3つの住まい」
     * --------------------------------------------------------
     *
     * タイトル・本文・リンク・画像については、
     * 既存 top-metabox.php の
     *
     * _ch_top_service_1_*
     * _ch_top_service_2_*
     * _ch_top_service_3_*
     *
     * をそのまま使う。
     *
     * つまり既存管理画面を無駄にしない。
     */

    $service_defaults = array(

        1 => array(
            'term'        => 'one-family',
            'num'         => '01',
            'title'       => '自分たちだけの間取りで建てる',
            'subtitle'    => '新築プラン',
            'text'        => '土地、駐車スペース、庭、収納、ワークスペースまで含めて、暮らし方から一から組み立てるプランです。',
            'story_title' => '土地と暮らし方から、間取りを組み立てる。',
            'story_text'  => '在宅ワーク、趣味収納、車の台数、庭とのつながり、休日の過ごし方まで含めて、家族に合う間取りを整理します。',
            'path'        => 'iezukuri/new-house',
        ),

        2 => array(
            'term'        => 'two-family',
            'num'         => '02',
            'title'       => '家族構成に合わせて暮らす',
            'subtitle'    => '二世帯・将来対応プラン',
            'text'        => '収納量、トイレ台数、駐車スペース、水回り、建具、生活時間の違いを整理するプランです。',
            'story_title' => '家族の距離感から、間取りを考える。',
            'story_text'  => '玄関、水回り、トイレの数、収納、駐車スペース、生活時間の違いまで整理します。',
            'path'        => 'iezukuri/two-family',
        ),

        3 => array(
            'term'        => 'used-renovation',
            'num'         => '03',
            'title'       => '今ある住まいを整える',
            'subtitle'    => 'リフォーム・リノベーションプラン',
            'text'        => '台所、収納、トイレ、水回り、湿気対策、壁塗り、外回りの修理など、今ある住まいを活かすプランです。',
            'story_title' => '修理だけで終わらせず、使える場所を増やす。',
            'story_text'  => '古い部分をすべて壊すのではなく、必要な修理と暮らしやすくする改善を分けて考えます。',
            'path'        => 'iezukuri/renovation',
        ),
    );


    $service_items = array();


    foreach ($service_defaults as $i => $default) {

        /**
         * 既存管理画面で設定された画像ID。
         */
        $image_id = absint(
            naigai_iez_top_editable_meta(
                $post_id,
                "_ch_top_service_{$i}_image_id",
                0
            )
        );


        /**
         * 管理画面画像をURLへ変換。
         */
        $image = naigai_iez_top_editable_image($image_id);


        /**
         * 管理画面で画像未設定なら、
         * 今まで使っていたページ画像へ戻す。
         *
         * これで既存表示を壊さない。
         */
        if (
            $image === ''
            && function_exists('naigai_iez_top_page_image')
        ) {
            $image = naigai_iez_top_page_image($default['path']);
        }


        /**
         * 詳細ページURL。
         */
        $default_url = function_exists('naigai_iez_top_page_url')
            ? naigai_iez_top_page_url($default['path'])
            : home_url('/' . trim($default['path'], '/') . '/');


        /**
         * 間取りページURL。
         */
        $plan_url = function_exists('naigai_iez_top_page_url')
            ? naigai_iez_top_page_url('iezukuri/plans')
            : home_url('/iezukuri/plans/');


        /**
         * 特徴3項目。
         */
        $features = array();

        for ($n = 1; $n <= 3; $n++) {

            $value = naigai_iez_top_editable_meta(
                $post_id,
                "_ch_top_service_{$i}_feature_{$n}",
                ''
            );

            if ($value !== '') {
                $features[] = $value;
            }
        }


        /**
         * 設備3項目。
         */
        $equipment = array();

        for ($n = 1; $n <= 3; $n++) {

            $value = naigai_iez_top_editable_meta(
                $post_id,
                "_ch_top_service_{$i}_equipment_{$n}",
                ''
            );

            if ($value !== '') {
                $equipment[] = $value;
            }
        }


        /**
         * section-feature.php が今まで使っていた
         * $service_items と同じ配列形式で返す。
         *
         * これが重要。
         *
         * HTML構造は変えず、
         * 「中身だけ管理画面」に変える。
         */
        $service_items[] = array(

            'term' => $default['term'],

            'tax_terms' => array(
                $default['term']
            ),

            'num' => naigai_iez_top_editable_meta(
                $post_id,
                "_ch_top_service_{$i}_num",
                $default['num']
            ),

            /**
             * 既存「06 入口カード」のタイトルを使用。
             */
            'title' => naigai_iez_top_editable_meta(
                $post_id,
                "_ch_top_service_{$i}_title",
                $default['title']
            ),

            'subtitle' => naigai_iez_top_editable_meta(
                $post_id,
                "_ch_top_service_{$i}_subtitle",
                $default['subtitle']
            ),

            /**
             * 既存「06 入口カード」の説明文を使用。
             */
            'text' => naigai_iez_top_editable_meta(
                $post_id,
                "_ch_top_service_{$i}_text",
                $default['text']
            ),

            /**
             * 既存「06 入口カード」のURLを使用。
             */
            'url' => naigai_iez_top_editable_meta(
                $post_id,
                "_ch_top_service_{$i}_url",
                $default_url
            ),

            'plan_url' => $plan_url,

            'image' => $image,

            'story_title' => naigai_iez_top_editable_meta(
                $post_id,
                "_ch_top_service_{$i}_story_title",
                $default['story_title']
            ),

            'story_text' => naigai_iez_top_editable_meta(
                $post_id,
                "_ch_top_service_{$i}_story_text",
                $default['story_text']
            ),

            'features' => $features,

            'equipment' => $equipment,
        );
    }


    /**
     * --------------------------------------------------------
     * 3. 暮らしのポイント
     * --------------------------------------------------------
     *
     * カードは最大6件。
     *
     * 1件も入力されていない場合は、
     * section-works.php 側で既存の $works を使う。
     *
     * つまり管理画面未入力でも
     * 現在のページは消えない。
     */

    $works = array();


    for ($i = 1; $i <= 6; $i++) {

        $title = naigai_iez_top_editable_meta(
            $post_id,
            "_hub_ch_work_{$i}_title",
            ''
        );

        $text = naigai_iez_top_editable_meta(
            $post_id,
            "_hub_ch_work_{$i}_text",
            ''
        );

        $image_id = absint(
            naigai_iez_top_editable_meta(
                $post_id,
                "_hub_ch_work_{$i}_image_id",
                0
            )
        );


        /**
         * 完全に空ならカードを作らない。
         */
        if (
            $title === ''
            && $text === ''
            && !$image_id
        ) {
            continue;
        }


        $works[] = array(

            'title' => $title,

            'text' => $text,

            'image' => naigai_iez_top_editable_image(
                $image_id
            ),
        );
    }


    /**
     * --------------------------------------------------------
     * 最終返却
     * --------------------------------------------------------
     */

    return array(

        /**
         * 「04 Plans」で入力したタイトルを、
         * 「3つの住まい」の見出しとして使う。
         */
        'services_title' => naigai_iez_top_editable_meta(
            $post_id,
            '_ch_top_plans_title',
            '3つの住まい'
        ),

        /**
         * 「04 Plans」で入力した本文。
         */
        'services_text' => naigai_iez_top_editable_meta(
            $post_id,
            '_ch_top_plans_text',
            '暮らし方に合う住まいを選びます。'
        ),

        'service_items' => $service_items,


        /**
         * 「03 Works」で入力したタイトル・本文。
         */
        'works_title' => naigai_iez_top_editable_meta(
            $post_id,
            '_ch_top_works_title',
            '暮らしのポイント'
        ),

        'works_text' => naigai_iez_top_editable_meta(
            $post_id,
            '_ch_top_works_text',
            ''
        ),

        'works' => $works,
    );
}


/**
 * ============================================================
 * 管理画面に追加する「詳細本文」
 * ============================================================
 *
 * 既存 top-metabox.php の
 *
 * 01 導入
 * 02 Site Reading
 * 03 Works
 * 04 Plans
 * 05 Flow
 * 06 入口カード
 *
 * はそのまま残す。
 *
 * ここでは不足している項目だけ追加する。
 */
function naigai_iez_top_editable_render_extra_fields($post)
{
    if (!$post || $post->post_type !== 'page') {
        return;
    }


    /**
     * /iezukuri/ 以外には表示しない。
     */
    if (trim((string) get_page_uri($post), '/') !== 'iezukuri') {
        return;
    }


    /**
     * --------------------------------------------------------
     * 06A 3つの住まい 詳細
     * --------------------------------------------------------
     */
    ?>
    <details class="naigai-iez-admin-subsection">
        <summary>
            <strong>06A 3つの住まい 詳細</strong>
        </summary>

        <p class="description">
            タイトル・説明文・URL・画像は、
            上の「06 入口カード」を使用します。
            ここでは追加の詳細情報だけ編集します。
        </p>

        <?php for ($i = 1; $i <= 3; $i++) : ?>

            <details class="naigai-iez-admin-subsection">
                <summary>
                    <strong>
                        住まい <?php echo esc_html($i); ?> 詳細
                    </strong>
                </summary>

                <table class="form-table naigai-iez-admin-table">
                    <tbody>

                    <?php

                    /**
                     * 番号。
                     */
                    naigai_iez_admin_text_input(
                        "_ch_top_service_{$i}_num",
                        '番号',
                        get_post_meta(
                            $post->ID,
                            "_ch_top_service_{$i}_num",
                            true
                        )
                    );


                    /**
                     * サブタイトル。
                     */
                    naigai_iez_admin_text_input(
                        "_ch_top_service_{$i}_subtitle",
                        'サブタイトル',
                        get_post_meta(
                            $post->ID,
                            "_ch_top_service_{$i}_subtitle",
                            true
                        )
                    );


                    /**
                     * 詳細見出し。
                     */
                    naigai_iez_admin_text_input(
                        "_ch_top_service_{$i}_story_title",
                        '詳細見出し',
                        get_post_meta(
                            $post->ID,
                            "_ch_top_service_{$i}_story_title",
                            true
                        )
                    );


                    /**
                     * 詳細本文。
                     */
                    naigai_iez_admin_textarea(
                        "_ch_top_service_{$i}_story_text",
                        '詳細本文',
                        get_post_meta(
                            $post->ID,
                            "_ch_top_service_{$i}_story_text",
                            true
                        ),
                        5
                    );


                    /**
                     * 特徴3件。
                     */
                    for ($n = 1; $n <= 3; $n++) {

                        naigai_iez_admin_text_input(
                            "_ch_top_service_{$i}_feature_{$n}",
                            "特徴 {$n}",
                            get_post_meta(
                                $post->ID,
                                "_ch_top_service_{$i}_feature_{$n}",
                                true
                            )
                        );
                    }


                    /**
                     * 設備3件。
                     */
                    for ($n = 1; $n <= 3; $n++) {

                        naigai_iez_admin_text_input(
                            "_ch_top_service_{$i}_equipment_{$n}",
                            "設備 {$n}",
                            get_post_meta(
                                $post->ID,
                                "_ch_top_service_{$i}_equipment_{$n}",
                                true
                            )
                        );
                    }

                    ?>

                    </tbody>
                </table>
            </details>

        <?php endfor; ?>

    </details>


    <?php
    /**
     * --------------------------------------------------------
     * 06C 暮らしのポイント
     * --------------------------------------------------------
     */
    ?>

    <details class="naigai-iez-admin-subsection">

        <summary>
            <strong>06C 暮らしのポイント カード</strong>
        </summary>

        <p class="description">
            1件以上入力すると、
            フロントの「暮らしのポイント」は
            このカード内容に切り替わります。
        </p>


        <?php for ($i = 1; $i <= 6; $i++) : ?>

            <details class="naigai-iez-admin-subsection">

                <summary>
                    <strong>
                        カード <?php echo esc_html($i); ?>
                    </strong>
                </summary>

                <table class="form-table naigai-iez-admin-table">
                    <tbody>

                    <?php

                    naigai_iez_admin_text_input(
                        "_hub_ch_work_{$i}_title",
                        'タイトル',
                        get_post_meta(
                            $post->ID,
                            "_hub_ch_work_{$i}_title",
                            true
                        )
                    );

                    naigai_iez_admin_textarea(
                        "_hub_ch_work_{$i}_text",
                        '本文',
                        get_post_meta(
                            $post->ID,
                            "_hub_ch_work_{$i}_text",
                            true
                        ),
                        4
                    );

                    naigai_iez_admin_media_input(
                        "_hub_ch_work_{$i}_image_id",
                        '画像ID',
                        get_post_meta(
                            $post->ID,
                            "_hub_ch_work_{$i}_image_id",
                            true
                        ),
                        'image'
                    );

                    ?>

                    </tbody>
                </table>

            </details>

        <?php endfor; ?>

    </details>

    <?php
}


/**
 * ============================================================
 * 今回追加したフィールドだけ保存
 * ============================================================
 *
 * 既存
 *
 * _ch_top_service_*
 * _ch_top_plans_*
 * _ch_top_works_*
 *
 * は既存保存処理へ任せる。
 *
 * ここで二重保存しない。
 */
function naigai_iez_top_editable_save_extra_fields($post_id)
{
    /**
     * WordPress自動保存なら何もしない。
     */
    if (
        defined('DOING_AUTOSAVE')
        && DOING_AUTOSAVE
    ) {
        return;
    }


    /**
     * 編集権限が無ければ保存しない。
     */
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }


    /**
     * /iezukuri/ 固定ページ以外は対象外。
     */
    $post = get_post($post_id);

    if (
        !$post
        || trim((string) get_page_uri($post), '/') !== 'iezukuri'
    ) {
        return;
    }


    /**
     * 保存対象一覧。
     */
    $fields = array();


    /**
     * 3つの住まい詳細。
     */
    for ($i = 1; $i <= 3; $i++) {

        $fields["_ch_top_service_{$i}_num"] =
            'text';

        $fields["_ch_top_service_{$i}_subtitle"] =
            'text';

        $fields["_ch_top_service_{$i}_story_title"] =
            'text';

        $fields["_ch_top_service_{$i}_story_text"] =
            'textarea';


        for ($n = 1; $n <= 3; $n++) {

            $fields[
                "_ch_top_service_{$i}_feature_{$n}"
            ] = 'text';

            $fields[
                "_ch_top_service_{$i}_equipment_{$n}"
            ] = 'text';
        }
    }


    /**
     * 暮らしのポイント。
     */
    for ($i = 1; $i <= 6; $i++) {

        $fields[
            "_hub_ch_work_{$i}_title"
        ] = 'text';

        $fields[
            "_hub_ch_work_{$i}_text"
        ] = 'textarea';

        $fields[
            "_hub_ch_work_{$i}_image_id"
        ] = 'int';
    }


    /**
     * 実際に保存。
     */
    foreach ($fields as $key => $type) {

        if (!isset($_POST[$key])) {
            continue;
        }


        $raw = wp_unslash(
            $_POST[$key]
        );


        switch ($type) {

            case 'textarea':

                $value =
                    sanitize_textarea_field($raw);

                break;


            case 'int':

                $value =
                    absint($raw);

                break;


            default:

                $value =
                    sanitize_text_field($raw);

                break;
        }


        update_post_meta(
            $post_id,
            $key,
            $value
        );
    }
}


/**
 * 固定ページ保存時に実行。
 */
add_action(
    'save_post_page',
    'naigai_iez_top_editable_save_extra_fields',
    95
);


/**
 * ============================================================
 * CLEAN ADMIN SAVE
 * ============================================================
 *
 * 管理画面を整理した後の保存処理。
 *
 * フロントと管理画面で同じmetaキーを使う。
 */
if (
    !function_exists(
        'naigai_iez_top_clean_admin_save'
    )
) {

    function naigai_iez_top_clean_admin_save($post_id)
    {
        /**
         * 自動保存では何もしない。
         */
        if (
            defined('DOING_AUTOSAVE')
            && DOING_AUTOSAVE
        ) {
            return;
        }


        /**
         * 編集権限確認。
         */
        if (
            !current_user_can(
                'edit_post',
                $post_id
            )
        ) {
            return;
        }


        /**
         * /iezukuri/ だけ対象。
         */
        $post = get_post($post_id);

        if (
            !$post
            || trim(
                (string) get_page_uri($post),
                '/'
            ) !== 'iezukuri'
        ) {
            return;
        }


        $fields = array();


        /**
         * ----------------------------------------------------
         * 3つの住まい
         * ----------------------------------------------------
         */
        $fields['_ch_top_plans_title'] = 'text';
        $fields['_ch_top_plans_text']  = 'textarea';


        for ($i = 1; $i <= 3; $i++) {

            $fields[
                "_ch_top_service_{$i}_num"
            ] = 'text';

            $fields[
                "_ch_top_service_{$i}_title"
            ] = 'text';

            $fields[
                "_ch_top_service_{$i}_subtitle"
            ] = 'text';

            $fields[
                "_ch_top_service_{$i}_text"
            ] = 'textarea';

            $fields[
                "_ch_top_service_{$i}_story_title"
            ] = 'text';

            $fields[
                "_ch_top_service_{$i}_story_text"
            ] = 'textarea';

            $fields[
                "_ch_top_service_{$i}_url"
            ] = 'url';

            $fields[
                "_ch_top_service_{$i}_image_id"
            ] = 'int';


            for ($n = 1; $n <= 3; $n++) {

                $fields[
                    "_ch_top_service_{$i}_feature_{$n}"
                ] = 'text';

                $fields[
                    "_ch_top_service_{$i}_equipment_{$n}"
                ] = 'text';
            }
        }


        /**
         * ----------------------------------------------------
         * 暮らしのポイント
         * ----------------------------------------------------
         *
         * フロントが読むmetaを直接保存する。
         */
        $fields[
            '_hub_ch_works_eyebrow'
        ] = 'text';

        $fields[
            '_hub_ch_works_title'
        ] = 'text';


        for ($i = 1; $i <= 6; $i++) {

            $fields[
                "_hub_ch_work_{$i}_title"
            ] = 'text';

            $fields[
                "_hub_ch_work_{$i}_text"
            ] = 'textarea';

            $fields[
                "_hub_ch_work_{$i}_image_id"
            ] = 'int';
        }


        /**
         * ----------------------------------------------------
         * Flow
         * ----------------------------------------------------
         */
        $fields[
            '_hub_ch_flow_title'
        ] = 'text';


        for ($i = 1; $i <= 6; $i++) {

            $fields[
                "_hub_ch_flow_{$i}_title"
            ] = 'text';

            $fields[
                "_hub_ch_flow_{$i}_text"
            ] = 'textarea';
        }


        /**
         * ----------------------------------------------------
         * 保存
         * ----------------------------------------------------
         */
        foreach ($fields as $key => $type) {

            if (
                !array_key_exists(
                    $key,
                    $_POST
                )
            ) {
                continue;
            }


            $raw =
                wp_unslash(
                    $_POST[$key]
                );


            switch ($type) {

                case 'textarea':

                    $value =
                        sanitize_textarea_field(
                            $raw
                        );

                    break;


                case 'url':

                    $value =
                        esc_url_raw(
                            $raw
                        );

                    break;


                case 'int':

                    $value =
                        absint(
                            $raw
                        );

                    break;


                default:

                    $value =
                        sanitize_text_field(
                            $raw
                        );

                    break;
            }


            update_post_meta(
                $post_id,
                $key,
                $value
            );
        }
    }
}


add_action(
    'save_post_page',
    'naigai_iez_top_clean_admin_save',
    200
);
