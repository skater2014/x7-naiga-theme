<?php
/**
 * /iezukuri page data schema
 * 全サブページの現在ページ入力を、セクション単位で管理する。
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('naigai_ch_page_data_overrides')) {
    function naigai_ch_page_data_overrides()
    {
        $cards = function ($prefix, $group, $count = 4) {
            $fields = array();

            for ($i = 1; $i <= $count; $i++) {
                $fields[] = array('key' => "{$prefix}_card{$i}_title", 'label' => "カード{$i} 見出し", 'type' => 'text', 'default' => '', 'group' => $group);
                $fields[] = array('key' => "{$prefix}_card{$i}_text", 'label' => "カード{$i} 本文", 'type' => 'textarea', 'default' => '', 'group' => $group);
                $fields[] = array('key' => "{$prefix}_card{$i}_image_id", 'label' => "カード{$i} サムネイル", 'type' => 'media_id', 'default' => '', 'group' => $group);
            }

            return $fields;
        };

        $faq = function ($prefix, $group, $count = 3) {
            $fields = array();

            for ($i = 1; $i <= $count; $i++) {
                $fields[] = array('key' => "{$prefix}_faq{$i}_q", 'label' => "Q{$i}", 'type' => 'text', 'default' => '', 'group' => $group);
                $fields[] = array('key' => "{$prefix}_faq{$i}_a", 'label' => "A{$i}", 'type' => 'textarea', 'default' => '', 'group' => $group);
            }

            return $fields;
        };

        $base = function ($prefix, $page_label, $defaults = array()) use ($cards, $faq) {
            $intro_title = $defaults['intro_title'] ?? $page_label;
            $intro_text  = $defaults['intro_text'] ?? '';
            $split_title = $defaults['split_title'] ?? $page_label;
            $split_text  = $defaults['split_text'] ?? '';
            $cta_title   = $defaults['cta_title'] ?? 'ご相談ください';
            $cta_text    = $defaults['cta_text'] ?? '';

            return array_merge(array(
                array('key' => "{$prefix}_intro_eyebrow", 'label' => '小見出し', 'type' => 'text', 'default' => $defaults['eyebrow'] ?? '', 'group' => "{$page_label} / 導入"),
                array('key' => "{$prefix}_intro_title", 'label' => '見出し', 'type' => 'text', 'default' => $intro_title, 'group' => "{$page_label} / 導入"),
                array('key' => "{$prefix}_intro_text", 'label' => '本文', 'type' => 'textarea', 'default' => $intro_text, 'group' => "{$page_label} / 導入"),
                array('key' => "{$prefix}_intro_image_id", 'label' => '導入画像', 'type' => 'media_id', 'default' => '', 'group' => "{$page_label} / 導入"),

                array('key' => "{$prefix}_split_eyebrow", 'label' => '小見出し', 'type' => 'text', 'default' => $defaults['split_eyebrow'] ?? '', 'group' => "{$page_label} / ストーリー"),
                array('key' => "{$prefix}_split_title", 'label' => '見出し', 'type' => 'text', 'default' => $split_title, 'group' => "{$page_label} / ストーリー"),
                array('key' => "{$prefix}_split_text", 'label' => '本文', 'type' => 'textarea', 'default' => $split_text, 'group' => "{$page_label} / ストーリー"),
                array('key' => "{$prefix}_split_image_id", 'label' => '横画像', 'type' => 'media_id', 'default' => '', 'group' => "{$page_label} / ストーリー"),

                array('key' => "{$prefix}_cta_title", 'label' => 'CTA見出し', 'type' => 'text', 'default' => $cta_title, 'group' => "{$page_label} / CTA"),
                array('key' => "{$prefix}_cta_text", 'label' => 'CTA本文', 'type' => 'textarea', 'default' => $cta_text, 'group' => "{$page_label} / CTA"),
                array('key' => "{$prefix}_cta_btn_label", 'label' => 'CTAボタン文言', 'type' => 'text', 'default' => '相談する', 'group' => "{$page_label} / CTA"),
                array('key' => "{$prefix}_cta_btn_page_id", 'label' => 'CTAリンク先ページ', 'type' => 'page_id', 'default' => '', 'group' => "{$page_label} / CTA"),
                array('key' => "{$prefix}_cta_btn_url", 'label' => 'CTAリンクURL', 'type' => 'url', 'default' => '', 'group' => "{$page_label} / CTA"),
                array('key' => "{$prefix}_cta_image_id", 'label' => 'CTA背景画像', 'type' => 'media_id', 'default' => '', 'group' => "{$page_label} / CTA"),
            ), $cards($prefix, "{$page_label} / カード・サムネイル"), $faq($prefix, "{$page_label} / Q&A"));
        };

        return array(
            'concept' => array(
                'page_label' => '注文住宅の考え方',
                'fields' => $base('_ch_concept', '注文住宅の考え方', array(
                    'eyebrow' => 'CONCEPT',
                    'intro_title' => '新規注文住宅',
                    'intro_text' => '那須での暮らしを前提に、土地、眺望、季節差、動線、庭とのつながりを整理しながら、無理のない住まいづくりを考えます。',
                    'split_eyebrow' => 'PLANNING',
                    'split_title' => '注文住宅の考え方',
                    'split_text' => '間取りを先に決めるのではなく、日々の過ごし方、収納、家事動線、来客や二拠点利用まで含めて整理します。',
                    'cta_title' => '家づくりを相談する',
                    'cta_text' => '土地探しから設計、資金計画まで、最初の段階から整理できます。',
                )),
            ),

            'design_policy' => array(
                'page_label' => '設計姿勢',
                'fields' => $base('_ch_design_policy', '設計姿勢', array(
                    'eyebrow' => 'DESIGN POLICY',
                    'intro_title' => '設計姿勢',
                    'intro_text' => '暮らし方、土地の条件、将来の使い方を整理しながら、無理のない家づくりを進めます。',
                    'split_title' => '設計の進め方',
                    'split_text' => '要望を形にする前に、生活動線、収納、光、風、外部空間との関係を確認します。',
                    'cta_title' => '設計について相談する',
                )),
            ),

            'nasu_shot' => array(
                'page_label' => '那須での家づくり',
                'fields' => $base('_ch_nasu', '那須での家づくり', array(
                    'eyebrow' => 'NASU HOUSE',
                    'intro_title' => '那須での家づくり',
                    'intro_text' => '寒暖差、敷地の広さ、眺望、庭とのつながりを踏まえ、那須で暮らしやすい住まいを考えます。',
                    'split_title' => '土地と季節を読む',
                    'split_text' => '道路、駐車、雪、日当たり、風の通り方まで、土地ごとの特徴を家づくりに反映します。',
                    'cta_title' => '那須の家づくりを相談する',
                )),
            ),

            'design_office' => array(
                'page_label' => 'デザインと設計',
                'fields' => $base('_ch_design_office', 'デザインと設計', array(
                    'eyebrow' => 'DESIGN & DETAIL',
                    'intro_title' => 'デザインと設計',
                    'intro_text' => '見た目だけでなく、使いやすさ、維持管理、素材感を含めて住まいの完成度を高めます。',
                    'split_title' => '細部まで整える',
                    'split_text' => '窓、照明、収納、外構まで一体で考え、暮らしの中で長く使いやすい設計を目指します。',
                    'cta_title' => 'デザインを相談する',
                )),
            ),

            'company' => array(
                'page_label' => '会社概要',
                'fields' => array_merge($base('_ch_company', '会社概要', array(
                    'eyebrow' => 'COMPANY',
                    'intro_title' => '会社概要',
                    'intro_text' => '那須エリアでの住まい、不動産、暮らしの相談窓口として、地域に合わせたご提案を行います。',
                    'split_title' => '地域に根ざした相談窓口',
                    'split_text' => '土地や建物だけでなく、移住、二拠点、民泊、売却など暮らしに関わる相談を整理します。',
                    'cta_title' => '会社へ相談する',
                )), array(
                    array('key' => '_ch_company_address', 'label' => '住所', 'type' => 'textarea', 'default' => '', 'group' => '会社概要 / 会社情報'),
                    array('key' => '_ch_company_tel', 'label' => '電話番号', 'type' => 'text', 'default' => '', 'group' => '会社概要 / 会社情報'),
                    array('key' => '_ch_company_hours', 'label' => '営業時間', 'type' => 'text', 'default' => '', 'group' => '会社概要 / 会社情報'),
                    array('key' => '_ch_company_map_iframe', 'label' => 'Google Map iframe', 'type' => 'textarea', 'default' => '', 'group' => '会社概要 / アクセス'),
                )),
            ),

            'contact' => array(
                'page_label' => 'ご相談・資料請求',
                'fields' => array_merge($base('_ch_contact', 'ご相談・資料請求', array(
                    'eyebrow' => 'CONTACT',
                    'intro_title' => 'ご相談・資料請求',
                    'intro_text' => '土地探し、資金計画、間取りの考え方、那須での暮らし方まで、お気軽にご相談ください。',
                    'split_title' => 'ご相談の流れ',
                    'split_text' => '内容を確認したうえで、必要な資料や次の進め方をご案内します。',
                    'cta_title' => 'お問い合わせフォーム',
                    'cta_text' => '下記フォームよりご相談内容をお送りください。',
                )), array(
                    array('key' => '_ch_contact_form_title', 'label' => 'フォーム見出し', 'type' => 'text', 'default' => 'お問い合わせフォーム', 'group' => 'ご相談・資料請求 / コンタクトフォーム'),
                    array('key' => '_ch_contact_form_lead', 'label' => 'フォーム説明', 'type' => 'textarea', 'default' => '内容を確認後、担当者よりご連絡いたします。', 'group' => 'ご相談・資料請求 / コンタクトフォーム'),
                    array('key' => '_ch_contact_form_shortcode', 'label' => 'フォーム shortcode', 'type' => 'text', 'default' => '', 'group' => 'ご相談・資料請求 / コンタクトフォーム'),
                )),
            ),
        );
    }
}
