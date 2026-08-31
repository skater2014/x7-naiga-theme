<?php
/**
 * hub/pages/iezukuri/admin/metaboxes/parts/top-metabox.php
 *
 * /iezukuri トップページ専用入力
 *
 * 役割:
 * - トップページ本文と入口カードだけを管理する。
 * - Heroは hero-metabox.php に分ける。
 * - フロント描画はしない。
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('naigai_iez_admin_top_field_map')) {
    function naigai_iez_admin_top_field_map() {
        $fields = array(
            /*
             * 共通ヘッダーナビデザイン。
             * /iezukuri/ 配下すべての header-customhome.php がこの値を読む。
             */
            '_hub_ch_header_menu_style' => 'key',

            '_ch_top_intro_kicker' => 'text',
            '_ch_top_intro_title' => 'text',
            '_ch_top_intro_text' => 'textarea',

            '_ch_top_site_reading_title' => 'text',
            '_ch_top_site_reading_text' => 'textarea',
            '_ch_top_site_reading_image_id' => 'number',


            '_ch_top_plans_title' => 'text',
            '_ch_top_plans_text' => 'textarea',


            '_hub_ch_cta_eyebrow' => 'text',
            '_hub_ch_cta_title' => 'text',
            '_hub_ch_cta_text' => 'textarea',
            '_hub_ch_cta_btn1_label' => 'text',
            '_hub_ch_cta_btn1_url' => 'url',
            '_hub_ch_cta_secondary_override_label' => 'text',
            '_hub_ch_cta_secondary_override_url' => 'url',
            '_hub_ch_cta_image_id' => 'number',
            '_hub_ch_cta_video_mp4_id' => 'number',
            '_hub_ch_cta_gallery_ids' => 'text',
            '_hub_ch_cta_swiper_enabled' => 'text',
            '_hub_ch_cta_swiper_nav' => 'text',
            '_hub_ch_cta_swiper_pagination' => 'text',
            '_hub_ch_cta_video_controls' => 'text',
        );

        for ($i = 1; $i <= 3; $i++) {
            $fields["_ch_top_service_{$i}_title"] = 'text';
            $fields["_ch_top_service_{$i}_text"] = 'textarea';
            $fields["_ch_top_service_{$i}_url"] = 'url';
            $fields["_ch_top_service_{$i}_image_id"] = 'number';
        }

        return $fields;
    }
}

add_filter('naigai_iez_admin_fixed_page_fields', function ($fields, $post) {
    if (!function_exists('naigai_iez_admin_is_top_page') || !naigai_iez_admin_is_top_page($post)) {
        return $fields;
    }

    return array_merge($fields, naigai_iez_admin_top_field_map());
}, 10, 2);

if (!function_exists('naigai_iez_admin_render_top_input')) {
    function naigai_iez_admin_render_top_input($post, $get) {
        ?>
        <div class="naigai-iez-admin-section">
            <h3>家づくり入口</h3>

            <?php /* IEZUKURI COMMON HEADER STYLE ADMIN START */ ?>
            <details class="naigai-iez-admin-subsection" open>
                <summary><strong>00 共通ヘッダーナビ設定</strong></summary>

                <p class="description">
                    ここで選んだヘッダーナビデザインは、/iezukuri/ トップだけでなく、
                    会社概要・相談・コンセプトなど家づくり配下の全ページに反映されます。
                    サブページ側に同じ設定欄を作るとページごとにズレるため、トップページを正本にします。
                </p>

                <table class="form-table naigai-iez-admin-table">
                    <tbody>
                        <tr>
                            <th scope="row">
                                <label for="hub-ch-header-menu-style">ヘッダーナビデザイン</label>
                            </th>
                            <td>
                                <?php
                                $header_style_value = $get('_hub_ch_header_menu_style', 'default');

                                if (!in_array($header_style_value, array('default', 'pipe', 'minimal'), true)) {
                                    $header_style_value = 'default';
                                }
                                ?>
                                <select id="hub-ch-header-menu-style" name="_hub_ch_header_menu_style">
                                    <option value="default" <?php selected($header_style_value, 'default'); ?>>標準 / サブページ共通</option>
                                    <option value="pipe" <?php selected($header_style_value, 'pipe'); ?>>パイプ区切り</option>
                                    <option value="minimal" <?php selected($header_style_value, 'minimal'); ?>>ミニマル</option>
                                </select>
                                <p class="description">
                                    初期値は「標準 / サブページ共通」です。トップとサブページの見た目を揃える場合はこれを使います。
                                </p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </details>
<?php /* IEZUKURI COMMON HEADER STYLE ADMIN END */ ?>

            
            <?php
            /**
             * ========================================================
             * 03 3つの住まい
             * ========================================================
             *
             * 本番 /iezukuri/ の Service セクション。
             *
             * 3カードそれぞれについて、
             *
             * ・タイトル
             * ・サブタイトル
             * ・本文
             * ・画像
             * ・リンク
             * ・Story
             * ・特徴
             * ・設備
             *
             * をここだけで編集する。
             *
             * 旧「入口カード」や「比較表」とは混ぜない。
             */

            $service_defaults = array(

                1 => array(
                    'num' => '01',

                    'title' =>
                        '自分たちだけの間取りで建てる',

                    'subtitle' =>
                        '新築プラン',

                    'text' =>
                        '土地、駐車スペース、庭、収納、ワークスペースまで含めて、暮らし方から一から組み立てるプランです。',

                    'story_title' =>
                        '土地と暮らし方から、間取りを組み立てる。',

                    'story_text' =>
                        '在宅ワーク、趣味収納、車の台数、庭とのつながり、休日の過ごし方まで含めて、家族に合う間取りを整理します。那須で広めの住空間を取りたい方、自然やゴルフ、二拠点生活、サテライトオフィスを考えたい方にも向いています。',

                    'url' =>
                        home_url('/iezukuri/new-house/'),

                    'image_id' => 0,

                    'features' => array(
                        '土地・庭・駐車場まで一体で計画',
                        '書斎や趣味収納を入れやすい',
                        '生活動線を一から整理できる',
                    ),

                    'equipment' => array(
                        '断熱・窓・換気を最初から計画',
                        'コンセントや照明位置を暮らしに合わせる',
                        '将来のメンテナンスを想定しやすい',
                    ),
                ),


                2 => array(
                    'num' => '02',

                    'title' =>
                        '家族構成に合わせて暮らす',

                    'subtitle' =>
                        '二世帯・将来対応プラン',

                    'text' =>
                        '収納量、トイレ台数、駐車スペース、水回り、建具、生活時間の違いを整理するプランです。',

                    'story_title' =>
                        '家族の距離感から、間取りを考える。',

                    'story_text' =>
                        '玄関、水回り、トイレの数、収納、駐車スペース、生活時間の違いまで整理します。子どもの勉強道具や書籍、アイロンや洗濯、送り迎え、将来の介護や独立まで見ながら、長く使える住まいを考えます。',

                    'url' =>
                        home_url('/iezukuri/two-family/'),

                    'image_id' => 0,

                    'features' => array(
                        '玄関・水回りの共有/分離を整理',
                        '収納とトイレ台数を家族構成に合わせる',
                        'バリアフリー建具や引き戸も検討',
                    ),

                    'equipment' => array(
                        '生活時間差に配慮した設備計画',
                        '家事効率を上げる水回り配置',
                        '将来の介護や独立にも対応',
                    ),
                ),


                3 => array(
                    'num' => '03',

                    'title' =>
                        '今ある住まいを整える',

                    'subtitle' =>
                        'リフォーム・リノベーションプラン',

                    'text' =>
                        '台所、収納、トイレ、水回り、湿気対策、壁塗り、外回りの修理など、今ある住まいを活かすプランです。',

                    'story_title' =>
                        '修理だけで終わらせず、使える場所を増やす。',

                    'story_text' =>
                        '古い部分をすべて壊すのではなく、必要な修理と暮らしやすくする改善を分けて考えます。使っていなかった部屋や収納を整えることで、趣味、作業、家族のためのスペースとして使える余白も生まれます。',

                    'url' =>
                        home_url('/iezukuri/renovation/'),

                    'image_id' => 0,

                    'features' => array(
                        '既存構造を見ながら動線を改善',
                        '台所・収納・トイレを使いやすくする',
                        '使っていない空間を趣味や作業に活用',
                    ),

                    'equipment' => array(
                        '水回り修理・補修を優先',
                        '湿気対策や壁塗りを検討',
                        'メンテナンスしやすい状態に整える',
                    ),
                ),
            );
            ?>


            <details
                class="naigai-iez-admin-subsection"
                open
            >

                <summary>
                    <strong>03 3つの住まい</strong>
                </summary>


                <table
                    class="form-table naigai-iez-admin-table"
                >
                    <tbody>

                    <?php

                    /**
                     * =================================================
                     * Service セクション全体
                     * =================================================
                     */

                    naigai_iez_admin_text_input(
                        '_ch_top_plans_title',
                        'セクション見出し',
                        $get(
                            '_ch_top_plans_title',
                            '3つの住まい'
                        )
                    );


                    naigai_iez_admin_textarea(
                        '_ch_top_plans_text',
                        'セクション説明',
                        $get(
                            '_ch_top_plans_text',
                            '新築住宅、二世帯住宅、住宅リフォーム。気になる住まい方を選ぶと、外観・間取り図・内装を種類別に確認できます。'
                        ),
                        4
                    );

                    ?>

                    </tbody>
                </table>


                <?php foreach ($service_defaults as $i => $service) : ?>

                    <?php

                    /**
                     * DBにタイトルがあればDB値。
                     * なければ本番タイトルを表示する。
                     */
                    $service_admin_title =
                        $get(
                            "_ch_top_service_{$i}_title",
                            $service['title']
                        );

                    ?>


                    <details
                        class="naigai-iez-admin-subsection"
                        <?php echo $i === 1 ? 'open' : ''; ?>
                    >

                        <summary>
                            <strong>
                                <?php
                                echo esc_html(
                                    sprintf(
                                        '%02d %s',
                                        $i,
                                        $service_admin_title
                                    )
                                );
                                ?>
                            </strong>
                        </summary>


                        <table
                            class="form-table naigai-iez-admin-table"
                        >
                            <tbody>

                            <?php

                            /**
                             * =========================================
                             * 番号
                             * =========================================
                             */
                            naigai_iez_admin_text_input(
                                "_ch_top_service_{$i}_num",
                                '番号',
                                $get(
                                    "_ch_top_service_{$i}_num",
                                    $service['num']
                                )
                            );


                            /**
                             * =========================================
                             * カードタイトル
                             * =========================================
                             */
                            naigai_iez_admin_text_input(
                                "_ch_top_service_{$i}_title",
                                'タイトル',
                                $get(
                                    "_ch_top_service_{$i}_title",
                                    $service['title']
                                )
                            );


                            /**
                             * =========================================
                             * サブタイトル
                             * =========================================
                             */
                            naigai_iez_admin_text_input(
                                "_ch_top_service_{$i}_subtitle",
                                'サブタイトル',
                                $get(
                                    "_ch_top_service_{$i}_subtitle",
                                    $service['subtitle']
                                )
                            );


                            /**
                             * =========================================
                             * カード本文
                             * =========================================
                             */
                            naigai_iez_admin_textarea(
                                "_ch_top_service_{$i}_text",
                                '本文',
                                $get(
                                    "_ch_top_service_{$i}_text",
                                    $service['text']
                                ),
                                5
                            );


                            /**
                             * =========================================
                             * 本番カード画像
                             * =========================================
                             */
                            naigai_iez_admin_media_input(
                                "_ch_top_service_{$i}_image_id",
                                '画像',
                                $get(
                                    "_ch_top_service_{$i}_image_id",
                                    $service['image_id']
                                ),
                                'image'
                            );


                            /**
                             * =========================================
                             * 「選択する」のリンク
                             * =========================================
                             */
                            naigai_iez_admin_url_input(
                                "_ch_top_service_{$i}_url",
                                'リンクURL',
                                $get(
                                    "_ch_top_service_{$i}_url",
                                    $service['url']
                                )
                            );


                            /**
                             * =========================================
                             * Story 見出し
                             * =========================================
                             */
                            naigai_iez_admin_text_input(
                                "_ch_top_service_{$i}_story_title",
                                '詳細見出し',
                                $get(
                                    "_ch_top_service_{$i}_story_title",
                                    $service['story_title']
                                )
                            );


                            /**
                             * =========================================
                             * Story 本文
                             * =========================================
                             */
                            naigai_iez_admin_textarea(
                                "_ch_top_service_{$i}_story_text",
                                '詳細本文',
                                $get(
                                    "_ch_top_service_{$i}_story_text",
                                    $service['story_text']
                                ),
                                6
                            );


                            /**
                             * =========================================
                             * 特徴 1〜3
                             * =========================================
                             */
                            for ($n = 1; $n <= 3; $n++) {

                                naigai_iez_admin_text_input(
                                    "_ch_top_service_{$i}_feature_{$n}",
                                    "特徴 {$n}",
                                    $get(
                                        "_ch_top_service_{$i}_feature_{$n}",
                                        $service['features'][$n - 1]
                                    )
                                );
                            }


                            /**
                             * =========================================
                             * 設備 1〜3
                             * =========================================
                             */
                            for ($n = 1; $n <= 3; $n++) {

                                naigai_iez_admin_text_input(
                                    "_ch_top_service_{$i}_equipment_{$n}",
                                    "設備 {$n}",
                                    $get(
                                        "_ch_top_service_{$i}_equipment_{$n}",
                                        $service['equipment'][$n - 1]
                                    )
                                );
                            }

                            ?>

                            </tbody>
                        </table>

                    </details>

                <?php endforeach; ?>

            </details>


<details class="naigai-iez-admin-subsection">

                <summary>
                    <strong>04 暮らしのポイント</strong>
                </summary>

                <table class="form-table naigai-iez-admin-table">
                    <tbody>

                    <?php

                    naigai_iez_admin_text_input(
                        '_hub_ch_works_eyebrow',
                        '英字キッカー',
                        $get(
                            '_hub_ch_works_eyebrow',
                            'Living Points'
                        )
                    );


                    naigai_iez_admin_text_input(
                        '_hub_ch_works_title',
                        '見出し',
                        $get(
                            '_hub_ch_works_title',
                            '暮らしのポイント'
                        )
                    );

                    ?>

                    </tbody>
                </table>


                <?php for ($i = 1; $i <= 4; $i++) : ?>

                    <details
                        class="naigai-iez-admin-subsection"
                        <?php echo $i === 1 ? 'open' : ''; ?>
                    >

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
                                $get(
                                    "_hub_ch_work_{$i}_title",
                                    ''
                                )
                            );


                            naigai_iez_admin_textarea(
                                "_hub_ch_work_{$i}_text",
                                '本文',
                                $get(
                                    "_hub_ch_work_{$i}_text",
                                    ''
                                ),
                                4
                            );


                            naigai_iez_admin_media_input(
                                "_hub_ch_work_{$i}_image_id",
                                '画像',
                                $get(
                                    "_hub_ch_work_{$i}_image_id",
                                    ''
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
            /**
             * ========================================================
             * 06 Flow
             * ========================================================
             *
             * ここもフロントが読む _hub_ch_flow_* に統一する。
             */

            $flow_defaults = array(

                1 => array(
                    'ご相談・ヒアリング',
                    '理想の暮らしやご要望を、まずはじっくり伺います。'
                ),

                2 => array(
                    'プラン・資金計画',
                    '敷地条件やご予算に合わせて、住まいの方向性を整理します。'
                ),

                3 => array(
                    'ご契約',
                    '設計内容や費用をご確認いただき、ご契約へ進みます。'
                ),

                4 => array(
                    '詳細設計・仕様決定',
                    '間取りや内装、設備など、細部まで一緒に整えていきます。'
                ),

                5 => array(
                    '着工・施工',
                    '確かな品質管理のもと、丁寧に住まいをかたちにします。'
                ),

                6 => array(
                    'お引渡し・アフターサポート',
                    '完成後も安心して暮らしていただけるよう継続して支えます。'
                ),
            );
            ?>


            <details class="naigai-iez-admin-subsection">

                <summary>
                    <strong>05 Flow</strong>
                </summary>

                <table class="form-table naigai-iez-admin-table">
                    <tbody>

                    <?php

                    naigai_iez_admin_text_input(
                        '_hub_ch_flow_title',
                        '見出し',
                        $get(
                            '_hub_ch_flow_title',
                            '家づくりの流れ'
                        )
                    );

                    ?>

                    </tbody>
                </table>


                <?php foreach ($flow_defaults as $i => $flow) : ?>

                    <details class="naigai-iez-admin-subsection">

                        <summary>
                            <strong>
                                <?php
                                echo esc_html(
                                    sprintf(
                                        '%02d %s',
                                        $i,
                                        $flow[0]
                                    )
                                );
                                ?>
                            </strong>
                        </summary>

                        <table class="form-table naigai-iez-admin-table">
                            <tbody>

                            <?php

                            naigai_iez_admin_text_input(
                                "_hub_ch_flow_{$i}_title",
                                'タイトル',
                                $get(
                                    "_hub_ch_flow_{$i}_title",
                                    $flow[0]
                                )
                            );


                            naigai_iez_admin_textarea(
                                "_hub_ch_flow_{$i}_text",
                                '本文',
                                $get(
                                    "_hub_ch_flow_{$i}_text",
                                    $flow[1]
                                ),
                                3
                            );

                            ?>

                            </tbody>
                        </table>

                    </details>

                <?php endforeach; ?>

            </details>



<details class="naigai-iez-admin-subsection" open>
                <summary><strong>06 CTA</strong></summary>
                <table class="form-table naigai-iez-admin-table">
                    <tbody>
                        <?php
                        naigai_iez_admin_text_input('_hub_ch_cta_eyebrow', 'CTAキッカー', $get('_hub_ch_cta_eyebrow', 'CONTACT'));
                        naigai_iez_admin_text_input('_hub_ch_cta_title', 'CTA見出し', $get('_hub_ch_cta_title', ''));
                        naigai_iez_admin_textarea('_hub_ch_cta_text', 'CTA本文', $get('_hub_ch_cta_text', ''), 5);
                        naigai_iez_admin_text_input('_hub_ch_cta_btn1_label', 'メインボタン文言', $get('_hub_ch_cta_btn1_label', ''));
                        naigai_iez_admin_url_input('_hub_ch_cta_btn1_url', 'メインボタンURL', $get('_hub_ch_cta_btn1_url', ''));
                        naigai_iez_admin_text_input('_hub_ch_cta_secondary_override_label', 'サブボタン文言', $get('_hub_ch_cta_secondary_override_label', ''));
                        naigai_iez_admin_url_input('_hub_ch_cta_secondary_override_url', 'サブボタンURL', $get('_hub_ch_cta_secondary_override_url', ''));

                        naigai_iez_admin_media_input('_hub_ch_cta_image_id', 'CTA単体画像', $get('_hub_ch_cta_image_id', ''), 'image');
                        naigai_iez_admin_media_input('_hub_ch_cta_video_mp4_id', 'CTA単体MP4動画', $get('_hub_ch_cta_video_mp4_id', ''), 'video');
                        naigai_iez_admin_media_input('_hub_ch_cta_gallery_ids', 'CTA Swiper画像', $get('_hub_ch_cta_gallery_ids', ''), 'image', true, '複数画像はメディアライブラリーから選択。');
                        ?>
                        <tr>
                            <th scope="row">Swiper / 動画設定</th>
                            <td>
                                <label><input type="checkbox" name="_hub_ch_cta_swiper_enabled" value="1" <?php checked($get('_hub_ch_cta_swiper_enabled', '1'), '1'); ?>> Swiper有効</label><br>
                                <label><input type="checkbox" name="_hub_ch_cta_swiper_nav" value="1" <?php checked($get('_hub_ch_cta_swiper_nav', '1'), '1'); ?>> 矢印表示</label><br>
                                <label><input type="checkbox" name="_hub_ch_cta_swiper_pagination" value="1" <?php checked($get('_hub_ch_cta_swiper_pagination', '1'), '1'); ?>> ドット表示</label><br>
                                <label><input type="checkbox" name="_hub_ch_cta_video_controls" value="1" <?php checked($get('_hub_ch_cta_video_controls', '0'), '1'); ?>> 動画コントロール表示</label>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </details>


        </div>
        <?php
    }
}


/* top CTA media normalize */
if (!function_exists('naigai_iez_admin_save_top_cta_media_items')) {
    function naigai_iez_admin_save_top_cta_media_items($post_id)
    {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        $post = get_post($post_id);
        if (!$post || !function_exists('naigai_iez_admin_is_top_page') || !naigai_iez_admin_is_top_page($post)) {
            return;
        }

        update_post_meta($post_id, '_hub_ch_cta_swiper_enabled', isset($_POST['_hub_ch_cta_swiper_enabled']) ? '1' : '0');
        update_post_meta($post_id, '_hub_ch_cta_swiper_nav', isset($_POST['_hub_ch_cta_swiper_nav']) ? '1' : '0');
        update_post_meta($post_id, '_hub_ch_cta_swiper_pagination', isset($_POST['_hub_ch_cta_swiper_pagination']) ? '1' : '0');
        update_post_meta($post_id, '_hub_ch_cta_video_controls', isset($_POST['_hub_ch_cta_video_controls']) ? '1' : '0');

        $items = array();

        $video_id = isset($_POST['_hub_ch_cta_video_mp4_id']) ? absint($_POST['_hub_ch_cta_video_mp4_id']) : 0;
        if ($video_id > 0) {
            $video_url = wp_get_attachment_url($video_id);
            if ($video_url) {
                $items[] = array(
                    'type'          => 'video',
                    'id'            => $video_id,
                    'attachment_id' => $video_id,
                    'url'           => esc_url_raw($video_url),
                    'mime'          => get_post_mime_type($video_id) ?: 'video/mp4',
                    'alt'           => '',
                );
            }
        }

        $gallery_raw = isset($_POST['_hub_ch_cta_gallery_ids']) ? sanitize_text_field(wp_unslash($_POST['_hub_ch_cta_gallery_ids'])) : '';
        $gallery_ids = array_filter(array_map('absint', explode(',', $gallery_raw)));

        foreach ($gallery_ids as $image_id) {
            $image_url = wp_get_attachment_url($image_id);
            if (!$image_url) {
                continue;
            }

            $items[] = array(
                'type'          => 'image',
                'id'            => $image_id,
                'attachment_id' => $image_id,
                'url'           => esc_url_raw($image_url),
                'mime'          => get_post_mime_type($image_id) ?: 'image/jpeg',
                'alt'           => get_post_meta($image_id, '_wp_attachment_image_alt', true),
            );
        }

        if (empty($items)) {
            $image_id = isset($_POST['_hub_ch_cta_image_id']) ? absint($_POST['_hub_ch_cta_image_id']) : 0;
            if ($image_id > 0) {
                $image_url = wp_get_attachment_url($image_id);
                if ($image_url) {
                    $items[] = array(
                        'type'          => 'image',
                        'id'            => $image_id,
                        'attachment_id' => $image_id,
                        'url'           => esc_url_raw($image_url),
                        'mime'          => get_post_mime_type($image_id) ?: 'image/jpeg',
                        'alt'           => get_post_meta($image_id, '_wp_attachment_image_alt', true),
                    );
                }
            }
        }

        update_post_meta($post_id, '_hub_ch_cta_media_items', $items);
    }
}
add_action('save_post_page', 'naigai_iez_admin_save_top_cta_media_items', 120);
