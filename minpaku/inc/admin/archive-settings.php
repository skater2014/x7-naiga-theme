<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * =========================================================
 * 民泊アーカイブ設定
 * =========================================================
 *
 * 役割:
 * - 管理画面に「民泊アーカイブ設定」メニューを追加
 * - アーカイブ上部コンテンツを option に保存
 * - archive-minpaku.php から呼べる形で値を返す
 * - minpaku を含む固定ページだけ選べる select を追加
 */

/**
 * minpaku を含む固定ページ一覧
 */
function naigai_minpaku_archive_page_options()
{
    $pages = get_pages(array(
        'sort_column' => 'menu_order,post_title',
        'sort_order'  => 'ASC',
        'post_status' => 'publish',
    ));

    $options = array();

    foreach ($pages as $page) {
        if (!$page instanceof WP_Post) {
            continue;
        }

        $uri = (string) get_page_uri($page->ID);
        if ($uri === '' || strpos($uri, 'minpaku') === false) {
            continue;
        }

        $options[$page->ID] = sprintf(
            '%s  (%s)',
            $page->post_title,
            '/' . ltrim($uri, '/')
        );
    }

    return $options;
}

/**
 * slug から固定ページIDを探す
 */
function naigai_minpaku_archive_find_page_id_by_path($path)
{
    $page = get_page_by_path($path, OBJECT, 'page');
    return ($page instanceof WP_Post) ? (int) $page->ID : 0;
}

/**
 * 設定項目一覧
 * archive-minpaku.php 側の $portal['...'] キーに合わせる
 */
function naigai_minpaku_archive_settings_fields()
{
    return array(
        'hero' => array(
            'title' => 'ヒーロー',
            'fields' => array(
                'hero_title'           => array('label' => 'ヒーロー見出し', 'type' => 'text'),
                'hero_lead'            => array('label' => 'ヒーロー説明文', 'type' => 'textarea'),
                'hero_note'            => array('label' => 'ヒーロー補足文', 'type' => 'textarea'),
                'hero_primary_text'    => array('label' => 'ヒーローボタン1 テキスト', 'type' => 'text'),
                'hero_primary_anchor'  => array('label' => 'ヒーローボタン1 アンカー（例: #stay-list）', 'type' => 'text'),
                'hero_secondary_text'  => array('label' => 'ヒーローボタン2 テキスト', 'type' => 'text'),
                'hero_secondary_url'   => array('label' => 'ヒーローボタン2 URL', 'type' => 'url'),
                'hero_image_id'        => array('label' => 'ヒーロー画像', 'type' => 'media'),
            ),
        ),

        'intro' => array(
            'title' => '導入',
            'fields' => array(
                'intro_title'          => array('label' => '導入セクション見出し', 'type' => 'text'),
                'intro_nav_label'      => array('label' => '導入ナビ表示名', 'type' => 'text'),
                'intro_note_text'      => array('label' => '導入 見出し下本文', 'type' => 'textarea'),
                'intro_text'           => array('label' => '導入セクション本文', 'type' => 'textarea'),
            ),
        ),

        'featured' => array(
            'title' => '特集カード 6件',
            'fields' => array(
                'featured_1_badge'   => array('label' => '特集1 バッジ', 'type' => 'text'),
                'featured_1_icon'    => array('label' => '特集1 アイコン', 'type' => 'text'),
                'featured_1_title'   => array('label' => '特集1 見出し', 'type' => 'text'),
                'featured_1_text'    => array('label' => '特集1 本文', 'type' => 'textarea'),
                'featured_1_page_id' => array('label' => '特集1 遷移先（minpaku固定ページ）', 'type' => 'page_select'),

                'featured_2_badge'   => array('label' => '特集2 バッジ', 'type' => 'text'),
                'featured_2_icon'    => array('label' => '特集2 アイコン', 'type' => 'text'),
                'featured_2_title'   => array('label' => '特集2 見出し', 'type' => 'text'),
                'featured_2_text'    => array('label' => '特集2 本文', 'type' => 'textarea'),
                'featured_2_page_id' => array('label' => '特集2 遷移先（minpaku固定ページ）', 'type' => 'page_select'),

                'featured_3_badge'   => array('label' => '特集3 バッジ', 'type' => 'text'),
                'featured_3_icon'    => array('label' => '特集3 アイコン', 'type' => 'text'),
                'featured_3_title'   => array('label' => '特集3 見出し', 'type' => 'text'),
                'featured_3_text'    => array('label' => '特集3 本文', 'type' => 'textarea'),
                'featured_3_page_id' => array('label' => '特集3 遷移先（minpaku固定ページ）', 'type' => 'page_select'),

                'featured_4_badge'   => array('label' => '特集4 バッジ', 'type' => 'text'),
                'featured_4_icon'    => array('label' => '特集4 アイコン', 'type' => 'text'),
                'featured_4_title'   => array('label' => '特集4 見出し', 'type' => 'text'),
                'featured_4_text'    => array('label' => '特集4 本文', 'type' => 'textarea'),
                'featured_4_page_id' => array('label' => '特集4 遷移先（minpaku固定ページ）', 'type' => 'page_select'),

                'featured_5_badge'   => array('label' => '特集5 バッジ', 'type' => 'text'),
                'featured_5_icon'    => array('label' => '特集5 アイコン', 'type' => 'text'),
                'featured_5_title'   => array('label' => '特集5 見出し', 'type' => 'text'),
                'featured_5_text'    => array('label' => '特集5 本文', 'type' => 'textarea'),
                'featured_5_page_id' => array('label' => '特集5 遷移先（minpaku固定ページ）', 'type' => 'page_select'),

                'featured_6_badge'   => array('label' => '特集6 バッジ', 'type' => 'text'),
                'featured_6_icon'    => array('label' => '特集6 アイコン', 'type' => 'text'),
                'featured_6_title'   => array('label' => '特集6 見出し', 'type' => 'text'),
                'featured_6_text'    => array('label' => '特集6 本文', 'type' => 'textarea'),
                'featured_6_page_id' => array('label' => '特集6 遷移先（minpaku固定ページ）', 'type' => 'page_select'),

                'featured_7_badge'   => array('label' => '特集7 ラベル', 'type' => 'text'),
                'featured_7_icon'    => array('label' => '特集7 アイコン', 'type' => 'text'),
                'featured_7_title'   => array('label' => '特集7 見出し', 'type' => 'text'),
                'featured_7_text'    => array('label' => '特集7 本文', 'type' => 'textarea'),
                'featured_7_page_id' => array('label' => '特集7 固定ページ', 'type' => 'page'),

                'featured_8_badge'   => array('label' => '特集8 ラベル', 'type' => 'text'),
                'featured_8_icon'    => array('label' => '特集8 アイコン', 'type' => 'text'),
                'featured_8_title'   => array('label' => '特集8 見出し', 'type' => 'text'),
                'featured_8_text'    => array('label' => '特集8 本文', 'type' => 'textarea'),
                'featured_8_page_id' => array('label' => '特集8 固定ページ', 'type' => 'page'),
            ),
        ),
        'style' => array(
            'title' => '過ごし方',
            'fields' => array(
                'style_1_title'        => array('label' => '過ごし方1 見出し', 'type' => 'text'),
                'style_1_text'         => array('label' => '過ごし方1 本文', 'type' => 'textarea'),
                'style_2_title'        => array('label' => '過ごし方2 見出し', 'type' => 'text'),
                'style_2_text'         => array('label' => '過ごし方2 本文', 'type' => 'textarea'),
                'style_3_title'        => array('label' => '過ごし方3 見出し', 'type' => 'text'),
                'style_3_text'         => array('label' => '過ごし方3 本文', 'type' => 'textarea'),
                'style_4_title'        => array('label' => '過ごし方4 見出し', 'type' => 'text'),
                'style_4_text'         => array('label' => '過ごし方4 本文', 'type' => 'textarea'),
            ),
        ),

        'difference' => array(
            'title' => '民泊・一棟貸し・貸別荘の違い',
            'fields' => array(
                'difference_title'       => array('label' => '違い 見出し', 'type' => 'text'),
                'difference_nav_label'   => array('label' => '違い ナビ表示名', 'type' => 'text'),
                'difference_note_text'   => array('label' => '違い 見出し下本文', 'type' => 'textarea'),
                'stay_list_nav_label'     => array('label' => '宿泊施設 ナビ表示名', 'type' => 'text'),
                'stay_list_note_text'     => array('label' => '宿泊施設 見出し下本文', 'type' => 'textarea'),
                'support_nav_label'       => array('label' => '運営サポート ナビ表示名', 'type' => 'text'),
                'support_note_text'       => array('label' => '運営サポート 見出し下本文', 'type' => 'textarea'),
                'difference_text'        => array('label' => '違い 本文', 'type' => 'textarea'),
                'difference_button_text' => array('label' => '違い ボタン文言', 'type' => 'text'),
                'difference_button_page_id' => array('label' => '違い 遷移先（minpaku固定ページ）', 'type' => 'page_select'),
            ),
        ),

        'campaign' => array(
            'title' => 'お得情報',
            'fields' => array(
                'campaign_title'       => array('label' => 'お得情報 見出し', 'type' => 'text'),
                'campaign_lead'        => array('label' => 'お得情報 説明文', 'type' => 'textarea'),

                'campaign_1_title'     => array('label' => 'お得情報1 見出し', 'type' => 'text'),
                'campaign_1_text'      => array('label' => 'お得情報1 本文', 'type' => 'textarea'),
                'campaign_1_link_text' => array('label' => 'お得情報1 リンク文言', 'type' => 'text'),
                'campaign_1_link_url'  => array('label' => 'お得情報1 URL', 'type' => 'url'),

                'campaign_2_title'     => array('label' => 'お得情報2 見出し', 'type' => 'text'),
                'campaign_2_text'      => array('label' => 'お得情報2 本文', 'type' => 'textarea'),
                'campaign_2_link_text' => array('label' => 'お得情報2 リンク文言', 'type' => 'text'),
                'campaign_2_link_url'  => array('label' => 'お得情報2 URL', 'type' => 'url'),

                'campaign_3_title'     => array('label' => 'お得情報3 見出し', 'type' => 'text'),
                'campaign_3_text'      => array('label' => 'お得情報3 本文', 'type' => 'textarea'),
                'campaign_3_link_text' => array('label' => 'お得情報3 リンク文言', 'type' => 'text'),
                'campaign_3_link_url'  => array('label' => 'お得情報3 URL', 'type' => 'url'),

                'campaign_button_text'    => array('label' => 'お得情報 下部ボタン文言', 'type' => 'text'),
                'campaign_button_page_id' => array('label' => 'お得情報 下部遷移先（minpaku固定ページ）', 'type' => 'page_select'),
            ),
        ),

        'flow' => array(
            'title' => '予約から決済までの流れ',
            'fields' => array(
                'flow_title'       => array('label' => '予約の流れ 見出し', 'type' => 'text'),
                'flow_text'        => array('label' => '予約の流れ 本文', 'type' => 'textarea'),
                'flow_button_text' => array('label' => '予約の流れ ボタン文言', 'type' => 'text'),
                'flow_button_page_id' => array('label' => '予約の流れ 遷移先（minpaku固定ページ）', 'type' => 'page_select'),
            ),
        ),

        'rules' => array(
            'title' => 'ご利用案内・利用規約',
            'fields' => array(
                'rules_title'       => array('label' => 'ご利用案内 見出し', 'type' => 'text'),
                'rules_text'        => array('label' => 'ご利用案内 本文', 'type' => 'textarea'),
                'rules_button_text' => array('label' => 'ご利用案内 ボタン文言', 'type' => 'text'),
                'rules_button_page_id' => array('label' => 'ご利用案内 遷移先（minpaku固定ページ）', 'type' => 'page_select'),
            ),
        ),

        'guide' => array(
            'title' => '那須ガイド',
            'fields' => array(
                'guide_title'          => array('label' => '那須ガイド 見出し', 'type' => 'text'),
                'guide_lead'           => array('label' => '那須ガイド 説明文', 'type' => 'textarea'),

                'guide_1_title'        => array('label' => 'ガイド1 見出し', 'type' => 'text'),
                'guide_1_text'         => array('label' => 'ガイド1 本文', 'type' => 'textarea'),
                'guide_2_title'        => array('label' => 'ガイド2 見出し', 'type' => 'text'),
                'guide_2_text'         => array('label' => 'ガイド2 本文', 'type' => 'textarea'),
                'guide_3_title'        => array('label' => 'ガイド3 見出し', 'type' => 'text'),
                'guide_3_text'         => array('label' => 'ガイド3 本文', 'type' => 'textarea'),
                'guide_4_title'        => array('label' => 'ガイド4 見出し', 'type' => 'text'),
                'guide_4_text'         => array('label' => 'ガイド4 本文', 'type' => 'textarea'),

                'guide_button_text'    => array('label' => '那須ガイド ボタン文言', 'type' => 'text'),
                'guide_button_page_id' => array('label' => '那須ガイド 遷移先（minpaku固定ページ）', 'type' => 'page_select'),
            ),
        ),

        'faq' => array(
            'title' => 'FAQ',
            'fields' => array(
                'faq_title'            => array('label' => 'FAQ 見出し', 'type' => 'text'),
                'faq_lead'             => array('label' => 'FAQ 補足文', 'type' => 'textarea'),
                'faq_1_q'              => array('label' => 'FAQ1 質問', 'type' => 'text'),
                'faq_1_a'              => array('label' => 'FAQ1 回答', 'type' => 'textarea'),
                'faq_2_q'              => array('label' => 'FAQ2 質問', 'type' => 'text'),
                'faq_2_a'              => array('label' => 'FAQ2 回答', 'type' => 'textarea'),
                'faq_3_q'              => array('label' => 'FAQ3 質問', 'type' => 'text'),
                'faq_3_a'              => array('label' => 'FAQ3 回答', 'type' => 'textarea'),
                'faq_4_q'              => array('label' => 'FAQ4 質問', 'type' => 'text'),
                'faq_4_a'              => array('label' => 'FAQ4 回答', 'type' => 'textarea'),
                'faq_5_q'              => array('label' => 'FAQ5 質問', 'type' => 'text'),
                'faq_5_a'              => array('label' => 'FAQ5 回答', 'type' => 'textarea'),
                'faq_6_q'              => array('label' => 'FAQ6 質問', 'type' => 'text'),
                'faq_6_a'              => array('label' => 'FAQ6 回答', 'type' => 'textarea'),
                'faq_button_text'      => array('label' => 'FAQ ボタン文言', 'type' => 'text'),
                'faq_button_page_id'   => array('label' => 'FAQ 遷移先（minpaku固定ページ）', 'type' => 'page_select'),
            ),
        ),

        'cta' => array(
            'title' => 'CTA',
            'fields' => array(
                'cta_land_title'          => array('label' => 'CTA左 見出し', 'type' => 'text'),
                'cta_land_text'           => array('label' => 'CTA左 本文', 'type' => 'textarea'),
                'cta_land_button_text'    => array('label' => 'CTA左 ボタン文言', 'type' => 'text'),
                'cta_land_button_url'     => array('label' => 'CTA左 ボタンURL', 'type' => 'url'),

                'cta_support_title'       => array('label' => 'CTA右 見出し', 'type' => 'text'),
                'cta_support_text'        => array('label' => 'CTA右 本文', 'type' => 'textarea'),
                'cta_support_button_text' => array('label' => 'CTA右 ボタン文言', 'type' => 'text'),
                'cta_support_button_url'  => array('label' => 'CTA右 ボタンURL', 'type' => 'url'),
            ),
        ),
    );
}

/**
 * デフォルト値
 * 保存が空でも archive-minpaku.php はここから表示できる
 */
function naigai_minpaku_archive_settings_defaults()
{
    return array(
        'hero_title'              => '那須の民泊・一棟貸し・貸別荘の宿泊先一覧',
        'hero_lead'               => '那須で民泊や一棟貸しの宿泊先を探している方へ。自然の中で過ごせる滞在先、家族やグループで使いやすい宿泊先を一覧でご案内します。',
        'hero_note'               => '設備や定員、雰囲気を見ながら比較し、そのまま各宿泊詳細ページへ進めます。',
        'hero_primary_text'       => '宿泊先を見る',
        'hero_primary_anchor'     => '#stay-list',
        'hero_secondary_text'     => '民泊運営サポート',
        'hero_secondary_url'      => home_url('/minpaku/'),
        'hero_image_id'           => 0,

        'intro_title'             => '那須での過ごし方から宿泊先を選ぶ',
        'intro_text'              => '少人数の滞在、家族旅行、グループ利用、ワーケーションなど、目的に合わせて宿泊先を選べるように構成します。',

        'featured_1_badge'              => 'Guide',
        'featured_1_icon'             => 'bbq',
        'featured_1_title'              => '那須の民泊ガイド',
        'featured_1_text'               => '宿泊先を探す前に、那須でどんな時間を過ごしたいかを整理したい方向けの入口ページです。',
        'featured_1_page_id'            => naigai_minpaku_archive_find_page_id_by_path('minpaku-guide'),

        'featured_2_badge'              => 'Family',
        'featured_2_icon'             => 'group',
        'featured_2_title'              => '家族で泊まる',
        'featured_2_text'               => '家族旅行向けに、人数、寝室数、設備、過ごしやすさの見方を整理したページです。',
        'featured_2_page_id'            => naigai_minpaku_archive_find_page_id_by_path('minpaku-family'),

        'featured_3_badge'              => 'Group',
        'featured_3_icon'             => 'work',
        'featured_3_title'              => 'グループで泊まる',
        'featured_3_text'               => '複数人での滞在に向いた宿泊先を、定員や共有スペースの観点から見やすくしたページです。',
        'featured_3_page_id'            => naigai_minpaku_archive_find_page_id_by_path('minpaku-group'),

        'featured_4_badge'              => 'Workation',
        'featured_4_icon'             => 'stay',
        'featured_4_title'              => 'ワーケーションで泊まる',
        'featured_4_text'               => '自然の中で仕事と滞在を両立したい方向けに、選び方を整理したページです。',
        'featured_4_page_id'            => naigai_minpaku_archive_find_page_id_by_path('minpaku-workation'),

        'featured_5_badge'              => 'Check',
        'featured_5_icon'             => 'nature',
        'featured_5_title'              => '予約前に確認したいこと',
        'featured_5_text'               => '料金、人数、設備、立地など、比較前に見ておきたいポイントをまとめています。',
        'featured_5_page_id'            => naigai_minpaku_archive_find_page_id_by_path('minpaku-campaign'),

        'featured_6_badge'              => 'FAQ',
        'featured_6_icon'             => 'activity',
        'featured_6_title'              => 'よくある質問',
        'featured_6_text'               => '民泊・一棟貸し・貸別荘の違いや予約前の疑問をまとめた質問ページです。',
        'featured_6_page_id'            => naigai_minpaku_archive_find_page_id_by_path('minpaku-faq'),

        'featured_7_badge'             => 'Flow',
        'featured_7_icon'             => 'food',
        'featured_7_title'             => '予約〜決済の流れ',
        'featured_7_text'              => '日程選択から予約内容の確認、オンライン決済までの流れを確認できます。',
        'featured_7_page_id'           => 0,

        'featured_8_badge'             => 'FAQ',
        'featured_8_icon'             => 'facility',
        'featured_8_title'             => 'よくある質問',
        'featured_8_text'              => '民泊・一棟貸し・貸別荘の違いや予約前の疑問をまとめた質問ページです。',
        'featured_8_page_id'           => 0,
        'style_1_title'           => '家族でゆっくり過ごす',
        'style_1_text'            => '一棟貸しなら、周囲を気にしすぎずに家族の時間を過ごしやすくなります。',
        'style_2_title'           => 'グループで泊まる',
        'style_2_text'            => '複数人で集まりたいときは、定員や寝室数、駐車スペースを比較しやすくします。',
        'style_3_title'           => '前泊・短期滞在',
        'style_3_text'            => '観光や移動前後の短い宿泊でも使いやすい宿泊先を見つけやすくします。',
        'style_4_title'           => 'ワーケーション',
        'style_4_text'            => '自然の中で仕事と滞在を両立したい方へ向けた使い方も案内できます。',

        'difference_title'        => '民泊・一棟貸し・貸別荘の違い',
        'difference_text'         => '民泊、一棟貸し、貸別荘の違いを整理して、宿泊スタイルに合う滞在先を選びやすくします。',
        'difference_button_text'  => '違いを見る',
        'difference_button_page_id' => naigai_minpaku_archive_find_page_id_by_path('minpaku-difference'),

        'campaign_title'          => '宿泊前に確認しておきたいこと',
        'campaign_lead'           => '料金だけでなく、人数・設備・立地・周辺環境など、宿泊先選びで見たいポイントを整理しておくと比較しやすくなります。',
        'campaign_1_title'        => '定員と寝室数',
        'campaign_1_text'         => '人数に対して余裕のある広さかどうかを、宿泊前に確認しやすくします。',
        'campaign_1_link_text'    => '',
        'campaign_1_link_url'     => '',
        'campaign_2_title'        => '設備と滞在スタイル',
        'campaign_2_text'         => 'キッチン、駐車場、滞在日数の条件などを見ながら選べるようにします。',
        'campaign_2_link_text'    => '',
        'campaign_2_link_url'     => '',
        'campaign_3_title'        => '那須での過ごし方',
        'campaign_3_text'         => '観光、自然、静かな滞在など、過ごし方に合わせて宿を選びやすくします。',
        'campaign_3_link_text'    => '',
        'campaign_3_link_url'     => '',
        'campaign_button_text'    => 'お得情報をもっと見る',
        'campaign_button_page_id' => naigai_minpaku_archive_find_page_id_by_path('minpaku-campaign'),

        'flow_title'              => '予約から決済までの流れ',
        'flow_text'               => '宿泊先の比較から日付選択、予約内容の確認、オンライン決済までの流れを、宿泊者向けにわかりやすく整理します。',
        'flow_button_text'        => '予約の流れを見る',
        'flow_button_page_id'     => naigai_minpaku_archive_find_page_id_by_path('minpaku-flow'),

        'rules_title'             => 'ご利用案内・利用規約',
        'rules_text'              => 'チェックイン、チェックアウト、キャンセルポリシー、設備利用、注意事項など、宿泊前に確認しておきたい内容をまとめます。',
        'rules_button_text'       => 'ご利用案内を見る',
        'rules_button_page_id'    => naigai_minpaku_archive_find_page_id_by_path('minpaku-rules'),

        'guide_title'             => '那須で宿泊先を探す方へ',
        'guide_lead'              => 'このページでは宿泊先の一覧だけでなく、民泊や一棟貸しを選ぶときに見ておきたいポイントも整理して掲載できます。',
        'guide_1_title'           => '民泊と一棟貸しの違い',
        'guide_1_text'            => '滞在スタイルに合わせて宿泊先を選びやすいように、使い方を整理します。',
        'guide_2_title'           => '人数で選ぶ',
        'guide_2_text'            => '少人数向けか、家族・グループ向けかを比較しやすくします。',
        'guide_3_title'           => '設備で選ぶ',
        'guide_3_text'            => 'キッチンや駐車場など、必要な条件を確認しやすくします。',
        'guide_4_title'           => '滞在目的で選ぶ',
        'guide_4_text'            => '観光、前泊、ワーケーションなど、目的に応じて探しやすくします。',
        'guide_button_text'       => '那須での過ごし方を見る',
        'guide_button_page_id'    => naigai_minpaku_archive_find_page_id_by_path('minpaku-guide'),

        'faq_title'               => 'よくある質問',
        'faq_lead'                => '宿泊前によくある質問をまとめています。詳しい案内は専用ページで確認できます。',
        'faq_1_q'                 => '民泊と一棟貸しは同じですか？',
        'faq_1_a'                 => '運用形態や見せ方は異なりますが、このページでは滞在先として比較しやすい形でご案内します。',
        'faq_2_q'                 => '設備や人数はどこで確認できますか？',
        'faq_2_a'                 => '各宿泊詳細ページで、設備や定員、条件を確認できるようにします。',
        'faq_3_q'                 => '宿泊先が少ない場合でもページを作れますか？',
        'faq_3_a'                 => '一覧だけでなく、説明コンテンツを上に入れることで入口ページとして使えます。',
        'faq_4_q'                 => '',
        'faq_4_a'                 => '',
        'faq_5_q'                 => '',
        'faq_5_a'                 => '',
        'faq_6_q'                 => '',
        'faq_6_a'                 => '',
        'faq_button_text'         => 'FAQをもっと見る',
        'faq_button_page_id'      => naigai_minpaku_archive_find_page_id_by_path('minpaku-faq'),

        'cta_land_title'          => '那須での過ごし方をもっと見る',
        'cta_land_text'           => '宿泊先選びだけでなく、那須でどう過ごしたいかを先に整理したい方向けの導線です。',
        'cta_land_button_text'    => '那須での過ごし方を見る',
        'cta_land_button_url'     => home_url('/minpaku-guide/'),

        'cta_support_title'       => '民泊運営の相談へ',
        'cta_support_text'        => '物件探し、改修、運営体制までまとめて相談したい方向けの導線として使えます。',
        'cta_support_button_text' => '民泊運営サポートへ',
        'cta_support_button_url'  => home_url('/minpaku/'),
    );
}

/**
 * sanitize
 */
function naigai_minpaku_archive_settings_sanitize($input)
{
    $defaults = naigai_minpaku_archive_settings_defaults();
    $sections = naigai_minpaku_archive_settings_fields();

    $output = $defaults;

    foreach ($sections as $section) {
        foreach ($section['fields'] as $key => $field) {
            $raw = isset($input[$key]) ? wp_unslash($input[$key]) : '';

            switch ($field['type']) {
                case 'textarea':
                    $output[$key] = sanitize_textarea_field($raw);
                    break;

                case 'url':
                    $output[$key] = esc_url_raw($raw);
                    break;

                case 'media':
                    $output[$key] = absint($raw);
                    break;

                case 'page_select':
                    $output[$key] = absint($raw);
                    break;

                default:
                    $output[$key] = sanitize_text_field($raw);
                    break;
            }
        }
    }

    return $output;
}

/**
 * option 登録
 */
function naigai_register_minpaku_archive_settings()
{
    register_setting(
        'naigai_minpaku_archive_settings_group',
        'naigai_minpaku_archive_content',
        array(
            'type'              => 'array',
            'sanitize_callback' => 'naigai_minpaku_archive_settings_sanitize',
            'default'           => naigai_minpaku_archive_settings_defaults(),
        )
    );
}
add_action('admin_init', 'naigai_register_minpaku_archive_settings');

/**
 * 管理画面メニュー
 */
function naigai_add_minpaku_archive_settings_menu()
{
    add_menu_page(
        '民泊アーカイブ設定',
        '民泊アーカイブ設定',
        'edit_theme_options',
        'naigai-minpaku-archive-settings',
        'naigai_render_minpaku_archive_settings_page',
        'dashicons-admin-home',
        58
    );
}
add_action('admin_menu', 'naigai_add_minpaku_archive_settings_menu');

/**
 * メディア読み込み
 */
function naigai_enqueue_minpaku_archive_settings_assets($hook_suffix)
{
    if ($hook_suffix !== 'toplevel_page_naigai-minpaku-archive-settings') {
        return;
    }

    wp_enqueue_media();

    $js = <<<'JS'
jQuery(function($){
    function openMediaFrame(button){
        var target = button.data('target');
        var preview = button.data('preview');

        var frame = wp.media({
            title: '画像を選択',
            button: { text: 'この画像を使う' },
            library: { type: 'image' },
            multiple: false
        });

        frame.on('select', function(){
            var attachment = frame.state().get('selection').first().toJSON();
            $('#' + target).val(attachment.id);
            if (attachment.sizes && attachment.sizes.medium) {
                $('#' + preview).html('<img src="' + attachment.sizes.medium.url + '" style="max-width:220px;height:auto;display:block;">');
            } else {
                $('#' + preview).html('<img src="' + attachment.url + '" style="max-width:220px;height:auto;display:block;">');
            }
        });

        frame.open();
    }

    $(document).on('click', '.naigai-media-select', function(e){
        e.preventDefault();
        openMediaFrame($(this));
    });

    $(document).on('click', '.naigai-media-remove', function(e){
        e.preventDefault();
        var target = $(this).data('target');
        var preview = $(this).data('preview');
        $('#' + target).val('');
        $('#' + preview).html('');
    });
});
JS;

    wp_add_inline_script('jquery-core', $js);

    $css = <<<'CSS'
.naigai-archive-settings-wrap .naigai-section{
    margin:24px 0 0;
    padding:24px;
    background:#fff;
    border:1px solid #dcdcde;
}
.naigai-archive-settings-wrap .naigai-section h2{
    margin:0 0 18px;
}
.naigai-archive-settings-wrap .naigai-field{
    margin:0 0 18px;
}
.naigai-archive-settings-wrap .naigai-field label{
    display:block;
    margin:0 0 8px;
    font-weight:700;
}
.naigai-archive-settings-wrap .naigai-field input[type="text"],
.naigai-archive-settings-wrap .naigai-field input[type="url"],
.naigai-archive-settings-wrap .naigai-field textarea,
.naigai-archive-settings-wrap .naigai-field select{
    width:100%;
    max-width:none;
}
.naigai-archive-settings-wrap .naigai-field textarea{
    min-height:90px;
}
.naigai-media-actions{
    display:flex;
    gap:8px;
    margin-top:10px;
}
.naigai-media-preview{
    margin-top:12px;
}
CSS;

    wp_register_style('naigai-minpaku-archive-settings-inline', false);
    wp_enqueue_style('naigai-minpaku-archive-settings-inline');
    wp_add_inline_style('naigai-minpaku-archive-settings-inline', $css);
}
add_action('admin_enqueue_scripts', 'naigai_enqueue_minpaku_archive_settings_assets');

/**
 * フィールド描画
 */
function naigai_render_minpaku_archive_settings_field($key, $field, $value)
{
    echo '<div class="naigai-field">';
    echo '<label for="' . esc_attr($key) . '">' . esc_html($field['label']) . '</label>';

    switch ($field['type']) {
        case 'textarea':
            echo '<textarea id="' . esc_attr($key) . '" name="naigai_minpaku_archive_content[' . esc_attr($key) . ']">' . esc_textarea($value) . '</textarea>';
            break;

        case 'url':
            echo '<input type="url" id="' . esc_attr($key) . '" name="naigai_minpaku_archive_content[' . esc_attr($key) . ']" value="' . esc_attr($value) . '">';
            break;

        case 'media':
            $preview_id = 'preview_' . $key;
            echo '<input type="hidden" id="' . esc_attr($key) . '" name="naigai_minpaku_archive_content[' . esc_attr($key) . ']" value="' . esc_attr($value) . '">';
            echo '<div class="naigai-media-actions">';
            echo '<button type="button" class="button naigai-media-select" data-target="' . esc_attr($key) . '" data-preview="' . esc_attr($preview_id) . '">画像を選択</button>';
            echo '<button type="button" class="button naigai-media-remove" data-target="' . esc_attr($key) . '" data-preview="' . esc_attr($preview_id) . '">画像を外す</button>';
            echo '</div>';
            echo '<div class="naigai-media-preview" id="' . esc_attr($preview_id) . '">';
            if (!empty($value)) {
                echo wp_get_attachment_image((int) $value, 'medium');
            }
            echo '</div>';
            break;

        case 'page_select':
            $options = naigai_minpaku_archive_page_options();
            echo '<select id="' . esc_attr($key) . '" name="naigai_minpaku_archive_content[' . esc_attr($key) . ']">';
            echo '<option value="0">選択してください</option>';
            foreach ($options as $page_id => $label) {
                echo '<option value="' . esc_attr($page_id) . '"' . selected((int) $value, (int) $page_id, false) . '>' . esc_html($label) . '</option>';
            }
            echo '</select>';
            break;

        default:
            echo '<input type="text" id="' . esc_attr($key) . '" name="naigai_minpaku_archive_content[' . esc_attr($key) . ']" value="' . esc_attr($value) . '">';
            break;
    }

    echo '</div>';
}

/**
 * 設定ページ描画
 */
function naigai_render_minpaku_archive_settings_page()
{
    if (!current_user_can('edit_theme_options')) {
        return;
    }

    $sections = naigai_minpaku_archive_settings_fields();
    $values   = naigai_get_minpaku_archive_content();

    echo '<div class="wrap naigai-archive-settings-wrap">';
    echo '<h1>民泊アーカイブ設定</h1>';
    echo '<p>この画面で入力した内容が archive-minpaku.php の上部コンテンツに反映されます。minpaku を含む固定ページはプルダウンから選択できます。</p>';

    echo '<form method="post" action="options.php">';
    settings_fields('naigai_minpaku_archive_settings_group');

    foreach ($sections as $section) {
        echo '<div class="naigai-section">';
        echo '<h2>' . esc_html($section['title']) . '</h2>';

        foreach ($section['fields'] as $key => $field) {
            $value = isset($values[$key]) ? $values[$key] : '';
            naigai_render_minpaku_archive_settings_field($key, $field, $value);
        }

        echo '</div>';
    }

    submit_button('保存する');
    echo '</form>';
    echo '</div>';
}

/**
 * archive-minpaku.php から使う取得関数
 */
function naigai_get_minpaku_archive_content()
{
    $defaults = naigai_minpaku_archive_settings_defaults();
    $saved    = get_option('naigai_minpaku_archive_content', array());

    if (!is_array($saved)) {
        $saved = array();
    }

    $content = wp_parse_args($saved, $defaults);

    $content['hero_image_url'] = '';
    if (!empty($content['hero_image_id'])) {
        $hero_url = wp_get_attachment_image_url((int) $content['hero_image_id'], 'full');
        if ($hero_url) {
            $content['hero_image_url'] = $hero_url;
        }
    }

    return $content;
}
