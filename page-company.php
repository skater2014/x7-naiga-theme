<?php

/**
 * Template Name: 会社概要
 * Template Post Type: page
 */

if (!defined('ABSPATH')) {
    exit;
}

// /company 専用CSS。
// 会社ページのHTMLはこのテンプレート、見た目は assets/css/page-company.css に分離する。
wp_enqueue_style(
    'naigai-page-company',
    get_template_directory_uri() . '/assets/css/pages/company.css',
    array(),
    filemtime(get_template_directory() . '/assets/css/pages/company.css')
);

get_header('77');

$post_id           = get_the_ID();
$page_title        = get_the_title($post_id) ?: '会社概要';
$is_company_editor = current_user_can('edit_post', $post_id);

/* =========================================================
 * helpers
 * ========================================================= */
if (!function_exists('ngc_theme_img')) {
    function ngc_theme_img($path)
    {
        return trailingslashit(get_stylesheet_directory_uri()) . ltrim($path, '/');
    }
}

if (!function_exists('ngc_meta_text')) {
    function ngc_meta_text($post_id, $key, $default = '')
    {
        $value = get_post_meta($post_id, $key, true);
        return $value !== '' ? $value : $default;
    }
}

if (!function_exists('ngc_meta_attachment_id')) {
    function ngc_meta_attachment_id($post_id, $key)
    {
        return absint(get_post_meta($post_id, $key, true));
    }
}

if (!function_exists('ngc_meta_image_url')) {
    function ngc_meta_image_url($post_id, $key, $size = 'full')
    {
        $attachment_id = ngc_meta_attachment_id($post_id, $key);
        if (!$attachment_id) {
            return '';
        }

        return wp_get_attachment_image_url($attachment_id, $size);
    }
}

if (!function_exists('ngc_first_image_url')) {
    function ngc_first_image_url($post_id, $keys, $size = 'full', $fallback = '')
    {
        foreach ((array) $keys as $key) {
            $url = ngc_meta_image_url($post_id, $key, $size);
            if ($url) {
                return $url;
            }
        }

        return $fallback;
    }
}

if (!function_exists('ngc_meta_lines')) {
    function ngc_meta_lines($post_id, $key, $default = '')
    {
        $raw   = ngc_meta_text($post_id, $key, $default);
        $lines = preg_split('/\r\n|\r|\n/', (string) $raw);

        $items = array();
        foreach ((array) $lines as $line) {
            $line = trim(wp_strip_all_tags($line));
            if ($line !== '') {
                $items[] = $line;
            }
        }

        return $items;
    }
}

if (!function_exists('ngc_btn')) {
    function ngc_btn($url, $label, $class = 'ngc-btn--primary', $target = '')
    {
        if (!$url || !$label) {
            return '';
        }

        $target_attr = '';
        if ($target) {
            $target_attr = ' target="' . esc_attr($target) . '" rel="noopener noreferrer"';
        }

        return sprintf(
            '<a class="ngc-btn %1$s" href="%2$s"%4$s>%3$s</a>',
            esc_attr($class),
            esc_url($url),
            esc_html($label),
            $target_attr
        );
    }
}

if (!function_exists('ngc_google_map_embed_url')) {
    function ngc_google_map_embed_url($address)
    {
        return 'https://maps.google.com/maps?q=' . rawurlencode($address) . '&t=m&z=15&output=embed&iwloc=near';
    }
}

if (!function_exists('ngc_google_map_link')) {
    function ngc_google_map_link($address)
    {
        return 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($address);
    }
}

if (!function_exists('ngc_company_default_meta')) {
    function ngc_company_default_meta($post_id = 0)
    {
        $post_id = absint($post_id);

        $page_title       = $post_id ? (get_the_title($post_id) ?: '会社概要') : '会社概要';
        $land_url         = home_url('/naigai-tochi/');
        $building_url     = home_url('/room-gallary/');
        $assessment_url   = home_url('/satei/');
        $reservation_url  = home_url('/reservation/');
        $recruitment_url  = home_url('/recruitment/');
        $privacy_url      = function_exists('get_privacy_policy_url') ? get_privacy_policy_url() : '';
        $terms_url        = home_url('/terms/');
        $living_guide_page = get_page_by_path('nasu-ideal-home', OBJECT, 'page');

        $default_living_guide_url   = $living_guide_page ? get_permalink($living_guide_page->ID) : home_url('/nasu-ideal-home/');
        $default_living_guide_title = $living_guide_page ? get_the_title($living_guide_page->ID) : '那須で理想の住まいを考える';
        $default_living_guide_text  = '土地から考える、住まいのスタイルから考える、分譲地・北米住宅・在来工法など、那須での暮らし方に合わせた住まい選びをご案内します。';

        return array(
            'company_hero_catch'             => 'NAIGAI GROUP',
            'company_hero_title'             => $page_title,
            'company_hero_lead'              => '那須エリアでの土地探し、新築・中古住宅、別荘、二拠点生活、貸別荘・民泊、個人向け・事業用賃貸、建設・改修まで。住まいと地域活用を総合的にサポートします。',
            'company_hero_btn1_text'         => '土地案内を見る',
            'company_hero_btn1_url'          => $land_url,
            'company_hero_btn2_text'         => '採用情報を確認する',
            'company_hero_btn2_url'          => $recruitment_url,

            'company_summary_card_1_title'   => '地域密着のサービス',
            'company_summary_card_1_text'    => '那須エリアを中心に、土地・建物・賃貸・建設まで、暮らしと不動産を横断してご案内します。',
            'company_summary_card_2_title'   => 'グループ連携の強み',
            'company_summary_card_2_text'    => '不動産、建設、資産運営を連携し、相談から実行、維持管理まで一貫して対応します。',
            'company_summary_card_3_title'   => '信頼ある相談窓口',
            'company_summary_card_3_text'    => '所在地、許認可、営業時間、対応領域を明確にし、安心して相談しやすい体制を整えています。',

            'company_company_eyebrow'        => 'Company',
            'company_company_title'          => '会社情報',
            'company_info_row_1_label'       => '呼称',
            'company_info_row_1_value'       => '内外グループ',
            'company_info_row_2_label'       => '構成会社',
            'company_info_row_2_value'       => '内外土地開発株式会社 / 内外建設株式会社 / 有限会社内外エステート',
            'company_info_row_3_label'       => '営業時間',
            'company_info_row_3_value'       => '平日 9:00 AM ～ 6:00 PM / 土日祝 9:00 AM ～ 5:00 PM',
            'company_info_row_4_label'       => '定休日',
            'company_info_row_4_value'       => '年中無休（年末年始、夏期休暇を除く）',
            'company_info_row_5_label'       => '主な事業',
            'company_info_row_5_value'       => '不動産売買、分譲地、住宅、別荘、中古再生、賃貸、貸別荘・民泊、建設・土木、改修、土地活用',
            'company_business_title'         => '事業内容',
            'company_business_items'         => implode("\n", array(
                '建売住宅の販売・分譲地の販売',
                '中古物件の買取・仲介',
                '遊休地の査定・買取',
                '注文住宅・別荘・二拠点住宅のご相談',
                '空き家・中古住宅の再生、改修',
                '貸別荘・民泊事業の企画・運営サポート',
                '個人向け・事業用の賃貸事業および管理',
                '土地の有効活用・事業活用のご相談',
                '建設・土木工事の設計、施工および請負',
                '建物の改修工事、造園の設計・施工',
            )),
            'company_info_guide_title'       => 'ご案内',
            'company_info_guide_lead'        => '内外グループの事業内容、拠点情報、取り組みについてご案内しています。',
            'company_info_guide_links_text'  => '土地案内・建物案内・売却査定・採用情報に関する詳細は、各案内ページをご確認ください。',
            'company_privacy_label'          => 'プライバシーポリシー',
            'company_privacy_url'            => $privacy_url,
            'company_terms_label'            => '利用規約',
            'company_terms_url'              => $terms_url,

            'company_group_1_name'           => '内外建設株式会社',
            'company_group_1_items'          => implode("\n", array(
                '栃木県知事 許可（般-3）第25079号',
                '建築工事業',
                '土木工事業',
                'とび・土工工事業',
                '解体工事業',
            )),
            'company_group_2_name'           => '内外土地開発株式会社',
            'company_group_2_items'          => implode("\n", array(
                '社団法人 全日本不動産協会会員',
                '社団法人 全日本不動産保証協会会員',
                '東京都知事(12)第38375号',
            )),
            'company_group_3_name'           => '有限会社内外エステート',
            'company_group_3_items'          => implode("\n", array(
                '資産運営・管理',
                '賃貸関連事業',
            )),

            'company_access_eyebrow'         => 'Access',
            'company_access_title'           => '所在地・アクセス',
            'company_access_lead'            => '各事務所の所在地を、住所とGoogle Mapで確認できます。',
            'company_office_1_label'         => '本社',
            'company_office_1_name'          => '内外土地開発株式会社',
            'company_office_1_zip'           => '〒143-0025',
            'company_office_1_address'       => '東京都大田区南馬込4-26-18',
            'company_office_1_tel'           => '03-6429-8700',
            'company_office_2_label'         => '那須事務所',
            'company_office_2_name'          => '内外建設株式会社',
            'company_office_2_zip'           => '〒325-0021',
            'company_office_2_address'       => '那須塩原市安藤町40-430',
            'company_office_2_tel'           => '0287-62-1011',

            'company_sdgs_eyebrow'           => 'Sustainability',
            'company_sdgs_title'             => '持続可能な地域と住まいづくりを目指して',
            'company_sdgs_lead'              => '空き家・中古住宅の再生、土地の有効活用、景観への配慮、ライフラインの整備検討、貸別荘・民泊、賃貸、地域協力会社との連携など、事業を通じた社会的な価値づくりを意識しています。',
            'company_sdgs_note'              => '※ 上記は事業内容に基づく自社の取り組み表現です。',
            'company_sdgs_1_goal'            => 'SDG 6',
            'company_sdgs_1_title'           => '地域条件に応じた水インフラの検討',
            'company_sdgs_1_text'            => '上下水道が未整備の地域では、井戸・ボーリング、浄化槽、汚水処理、汲み取りなど、地域条件に応じた生活インフラの確保を検討します。',
            'company_sdgs_1_tone'            => 'is-sky',
            'company_sdgs_2_goal'            => 'SDG 7',
            'company_sdgs_2_title'           => '省エネ・再生可能エネルギーの提案',
            'company_sdgs_2_text'            => '断熱性や設備計画に配慮し、条件に応じて太陽光発電などの導入可能性も検討しながら、持続可能な住環境を提案します。',
            'company_sdgs_2_tone'            => 'is-amber',
            'company_sdgs_3_goal'            => 'SDG 8',
            'company_sdgs_3_title'           => '地域の仕事と連携を支える',
            'company_sdgs_3_text'            => '地域の協力会社や専門事業者と連携し、不動産・建設・設備・維持管理まで、地域で支える住まいづくりを進めます。',
            'company_sdgs_3_tone'            => 'is-gray',
            'company_sdgs_4_goal'            => 'SDG 9',
            'company_sdgs_4_title'           => '土地の有効活用と地域資産の活性化',
            'company_sdgs_4_text'            => '個人・法人を問わず、遊休地や未利用地の有効活用を提案し、地域資産の価値向上と継続的な活用を支援します。',
            'company_sdgs_4_tone'            => 'is-green',
            'company_sdgs_5_goal'            => 'SDG 11',
            'company_sdgs_5_title'           => '空き家・中古住宅の再生',
            'company_sdgs_5_text'            => '空き家や中古住宅の再生・改修を通じて、既存ストックを活かし、長く住み続けられる住まいづくりを目指します。',
            'company_sdgs_5_tone'            => 'is-gold',
            'company_sdgs_6_goal'            => 'SDG 11',
            'company_sdgs_6_title'           => '貸別荘・民泊事業による地域活用',
            'company_sdgs_6_text'            => '遊休建物や別荘の活用方法として、貸別荘・民泊などの運用も視野に入れ、地域資源の循環と滞在価値の向上につなげます。',
            'company_sdgs_6_tone'            => 'is-rose',
            'company_sdgs_7_goal'            => 'SDG 11',
            'company_sdgs_7_title'           => '個人・事業用の賃貸事業',
            'company_sdgs_7_text'            => '個人向け・事業用の賃貸事業を通じて、多様な住まい方・働き方に対応できる地域の受け皿づくりを目指します。',
            'company_sdgs_7_tone'            => 'is-sky',
            'company_sdgs_8_goal'            => 'SDG 15',
            'company_sdgs_8_title'           => '景観と調和する設計',
            'company_sdgs_8_text'            => '那須町の景観計画の考え方を踏まえ、周辺の自然や街並みと調和する外観・配置・デザインを意識します。',
            'company_sdgs_8_tone'            => 'is-green',
            'company_sdgs_9_goal'            => 'SDG 17',
            'company_sdgs_9_title'           => '地域協力会社との連携',
            'company_sdgs_9_text'            => '浄化槽、設備、造成、維持管理などの分野で地域事業者と連携し、継続的な地域協働を大切にします。',
            'company_sdgs_9_tone'            => 'is-gray',

            'company_guide_section_eyebrow'  => 'Guide',
            'company_guide_section_title'    => 'ご相談内容に合わせてご案内します',
            'company_living_guide_title'     => $default_living_guide_title,
            'company_living_guide_text'      => $default_living_guide_text,
            'company_living_guide_label'     => '住まいのご案内を見る',
            'company_living_guide_url'       => $default_living_guide_url,
            'company_guide_1_title'          => '土地を探したい',
            'company_guide_1_text'           => '那須エリアの分譲地や土地情報を確認したい方はこちら。',
            'company_guide_1_url'            => $land_url,
            'company_guide_1_label'          => '土地案内を見る',
            'company_guide_2_title'          => '建物・住まいを見たい',
            'company_guide_2_text'           => '建物の雰囲気や住まいのスタイルを確認したい方はこちら。',
            'company_guide_2_url'            => $building_url,
            'company_guide_2_label'          => '建物案内を見る',
            'company_guide_3_title'          => '売却・査定を相談したい',
            'company_guide_3_text'           => '土地・建物の売却や査定、遊休地のご相談はこちら。',
            'company_guide_3_url'            => $assessment_url,
            'company_guide_3_label'          => '査定ページへ',
            'company_guide_4_title'          => '採用情報を見る',
            'company_guide_4_text'           => '地域の住まいづくり・不動産・建設に関わる仕事に興味のある方はこちら。',
            'company_guide_4_url'            => $recruitment_url,
            'company_guide_4_label'          => '採用ページへ',

            'company_final_cta_title'        => 'お気軽にご相談ください',
            'company_final_cta_btn1_text'    => '来店予約',
            'company_final_cta_btn1_url'     => $reservation_url,
            'company_final_cta_btn2_text'    => '土地案内',
            'company_final_cta_btn2_url'     => $land_url,
            'company_final_cta_btn3_text'    => '売却査定',
            'company_final_cta_btn3_url'     => $assessment_url,
        );
    }
}

$defaults = ngc_company_default_meta($post_id);

/* =========================================================
 * hero
 * ========================================================= */
$hero_fallback = ngc_theme_img('assets/img/company/company-hero.jpg');

$hero_pc_url = ngc_first_image_url(
    $post_id,
    array('company_hero_image_pc', 'company_hero_image'),
    'full',
    ''
);

$hero_sp_url = ngc_first_image_url(
    $post_id,
    array('company_hero_image_sp', 'company_hero_image_pc', 'company_hero_image'),
    'full',
    ''
);

if (!$hero_pc_url && has_post_thumbnail($post_id)) {
    $hero_pc_url = get_the_post_thumbnail_url($post_id, 'full');
}
if (!$hero_pc_url) {
    $hero_pc_url = $hero_fallback;
}
if (!$hero_sp_url) {
    $hero_sp_url = $hero_pc_url;
}

$hero_catch     = ngc_meta_text($post_id, 'company_hero_catch', $defaults['company_hero_catch']);
$hero_title     = ngc_meta_text($post_id, 'company_hero_title', $defaults['company_hero_title']);
$hero_lead      = ngc_meta_text($post_id, 'company_hero_lead', $defaults['company_hero_lead']);
$hero_btn1_text = ngc_meta_text($post_id, 'company_hero_btn1_text', $defaults['company_hero_btn1_text']);
$hero_btn1_url  = ngc_meta_text($post_id, 'company_hero_btn1_url', $defaults['company_hero_btn1_url']);
$hero_btn2_text = ngc_meta_text($post_id, 'company_hero_btn2_text', $defaults['company_hero_btn2_text']);
$hero_btn2_url  = ngc_meta_text($post_id, 'company_hero_btn2_url', $defaults['company_hero_btn2_url']);

/* =========================================================
 * summary
 * ========================================================= */
$summary_cards = array();
for ($i = 1; $i <= 3; $i++) {
    $summary_cards[] = array(
        'title' => ngc_meta_text($post_id, "company_summary_card_{$i}_title", $defaults["company_summary_card_{$i}_title"]),
        'text'  => ngc_meta_text($post_id, "company_summary_card_{$i}_text", $defaults["company_summary_card_{$i}_text"]),
    );
}

/* =========================================================
 * company info
 * ========================================================= */
$company_eyebrow = ngc_meta_text($post_id, 'company_company_eyebrow', $defaults['company_company_eyebrow']);
$company_title   = ngc_meta_text($post_id, 'company_company_title', $defaults['company_company_title']);

$company_rows = array();
for ($i = 1; $i <= 5; $i++) {
    $label = ngc_meta_text($post_id, "company_info_row_{$i}_label", $defaults["company_info_row_{$i}_label"]);
    $value = ngc_meta_text($post_id, "company_info_row_{$i}_value", $defaults["company_info_row_{$i}_value"]);

    if ($label !== '' || $value !== '') {
        $company_rows[] = array(
            'label' => $label,
            'value' => $value,
        );
    }
}

$business_title      = ngc_meta_text($post_id, 'company_business_title', $defaults['company_business_title']);
$business_items      = ngc_meta_lines($post_id, 'company_business_items', $defaults['company_business_items']);
$info_guide_title    = ngc_meta_text($post_id, 'company_info_guide_title', $defaults['company_info_guide_title']);
$info_guide_lead     = ngc_meta_text($post_id, 'company_info_guide_lead', $defaults['company_info_guide_lead']);
$info_guide_links    = ngc_meta_text($post_id, 'company_info_guide_links_text', $defaults['company_info_guide_links_text']);
$privacy_label       = ngc_meta_text($post_id, 'company_privacy_label', $defaults['company_privacy_label']);
$privacy_url         = ngc_meta_text($post_id, 'company_privacy_url', $defaults['company_privacy_url']);
$terms_label         = ngc_meta_text($post_id, 'company_terms_label', $defaults['company_terms_label']);
$terms_url           = ngc_meta_text($post_id, 'company_terms_url', $defaults['company_terms_url']);

$group_companies = array();
for ($i = 1; $i <= 3; $i++) {
    $name  = ngc_meta_text($post_id, "company_group_{$i}_name", $defaults["company_group_{$i}_name"]);
    $items = ngc_meta_lines($post_id, "company_group_{$i}_items", $defaults["company_group_{$i}_items"]);

    if ($name !== '' || !empty($items)) {
        $group_companies[] = array(
            'name'  => $name,
            'items' => $items,
        );
    }
}

/* =========================================================
 * access
 * ========================================================= */
$access_eyebrow = ngc_meta_text($post_id, 'company_access_eyebrow', $defaults['company_access_eyebrow']);
$access_title   = ngc_meta_text($post_id, 'company_access_title', $defaults['company_access_title']);
$access_lead    = ngc_meta_text($post_id, 'company_access_lead', $defaults['company_access_lead']);

$offices = array();
for ($i = 1; $i <= 2; $i++) {
    $office = array(
        'label'        => ngc_meta_text($post_id, "company_office_{$i}_label", $defaults["company_office_{$i}_label"]),
        'name'         => ngc_meta_text($post_id, "company_office_{$i}_name", $defaults["company_office_{$i}_name"]),
        'zip'          => ngc_meta_text($post_id, "company_office_{$i}_zip", $defaults["company_office_{$i}_zip"]),
        'address'      => ngc_meta_text($post_id, "company_office_{$i}_address", $defaults["company_office_{$i}_address"]),
        'tel'          => ngc_meta_text($post_id, "company_office_{$i}_tel", $defaults["company_office_{$i}_tel"]),
        // 追加: メタボックスで入力したGoogleビジネスプロフィールURL。
        // 空なら下で従来の住所検索リンクへ自動でフォールバックする。
        'profile_url'  => ngc_meta_text($post_id, "company_office_{$i}_profile_url", ''),
    );

    if ($office['name'] !== '' || $office['address'] !== '') {
        $offices[] = $office;
    }
}

/* =========================================================
 * sdgs
 * ========================================================= */
$sdgs_eyebrow = ngc_meta_text($post_id, 'company_sdgs_eyebrow', $defaults['company_sdgs_eyebrow']);
$sdgs_title   = ngc_meta_text($post_id, 'company_sdgs_title', $defaults['company_sdgs_title']);
$sdgs_lead    = ngc_meta_text($post_id, 'company_sdgs_lead', $defaults['company_sdgs_lead']);
$sdgs_note    = ngc_meta_text($post_id, 'company_sdgs_note', $defaults['company_sdgs_note']);

$sdgs_cards = array();
for ($i = 1; $i <= 9; $i++) {
    $sdgs_cards[] = array(
        'goal'  => ngc_meta_text($post_id, "company_sdgs_{$i}_goal", $defaults["company_sdgs_{$i}_goal"]),
        'title' => ngc_meta_text($post_id, "company_sdgs_{$i}_title", $defaults["company_sdgs_{$i}_title"]),
        'text'  => ngc_meta_text($post_id, "company_sdgs_{$i}_text", $defaults["company_sdgs_{$i}_text"]),
        'tone'  => ngc_meta_text($post_id, "company_sdgs_{$i}_tone", $defaults["company_sdgs_{$i}_tone"]),
    );
}

/* =========================================================
 * guide
 * ========================================================= */
$guide_section_eyebrow = ngc_meta_text($post_id, 'company_guide_section_eyebrow', $defaults['company_guide_section_eyebrow']);
$guide_section_title   = ngc_meta_text($post_id, 'company_guide_section_title', $defaults['company_guide_section_title']);

$living_guide_url   = ngc_meta_text($post_id, 'company_living_guide_url', $defaults['company_living_guide_url']);
$living_guide_title = ngc_meta_text($post_id, 'company_living_guide_title', $defaults['company_living_guide_title']);
$living_guide_text  = ngc_meta_text($post_id, 'company_living_guide_text', $defaults['company_living_guide_text']);
$living_guide_label = ngc_meta_text($post_id, 'company_living_guide_label', $defaults['company_living_guide_label']);

$living_guide_image_pc = ngc_first_image_url(
    $post_id,
    array('company_living_guide_image_pc', 'company_living_guide_image'),
    'full',
    ''
);

$living_guide_image_sp = ngc_first_image_url(
    $post_id,
    array('company_living_guide_image_sp', 'company_living_guide_image_pc', 'company_living_guide_image'),
    'full',
    ''
);

if (!$living_guide_image_sp) {
    $living_guide_image_sp = $living_guide_image_pc;
}

$guide_cards = array();
for ($i = 1; $i <= 4; $i++) {
    $image_pc = ngc_first_image_url(
        $post_id,
        array("company_guide_{$i}_image_pc", "company_guide_{$i}_image"),
        'large',
        ''
    );

    $image_sp = ngc_first_image_url(
        $post_id,
        array("company_guide_{$i}_image_sp", "company_guide_{$i}_image_pc", "company_guide_{$i}_image"),
        'large',
        ''
    );

    if (!$image_sp) {
        $image_sp = $image_pc;
    }

    $guide_cards[] = array(
        'title'    => ngc_meta_text($post_id, "company_guide_{$i}_title", $defaults["company_guide_{$i}_title"]),
        'text'     => ngc_meta_text($post_id, "company_guide_{$i}_text", $defaults["company_guide_{$i}_text"]),
        'url'      => ngc_meta_text($post_id, "company_guide_{$i}_url", $defaults["company_guide_{$i}_url"]),
        'label'    => ngc_meta_text($post_id, "company_guide_{$i}_label", $defaults["company_guide_{$i}_label"]),
        'image_pc' => $image_pc,
        'image_sp' => $image_sp,
        'class'    => $i === 1 ? 'ngc-btn--primary' : 'ngc-btn--outline',
    );
}

/* =========================================================
 * final cta
 * ========================================================= */
$final_cta_title     = ngc_meta_text($post_id, 'company_final_cta_title', $defaults['company_final_cta_title']);
$final_cta_btn1_text = ngc_meta_text($post_id, 'company_final_cta_btn1_text', $defaults['company_final_cta_btn1_text']);
$final_cta_btn1_url  = ngc_meta_text($post_id, 'company_final_cta_btn1_url', $defaults['company_final_cta_btn1_url']);
$final_cta_btn2_text = ngc_meta_text($post_id, 'company_final_cta_btn2_text', $defaults['company_final_cta_btn2_text']);
$final_cta_btn2_url  = ngc_meta_text($post_id, 'company_final_cta_btn2_url', $defaults['company_final_cta_btn2_url']);
$final_cta_btn3_text = ngc_meta_text($post_id, 'company_final_cta_btn3_text', $defaults['company_final_cta_btn3_text']);
$final_cta_btn3_url  = ngc_meta_text($post_id, 'company_final_cta_btn3_url', $defaults['company_final_cta_btn3_url']);
?>

<style>
    .company-page .guide-feature {
        display: grid;
        grid-template-columns: minmax(0, 1.05fr) minmax(0, 0.95fr);
        gap: 24px;
        align-items: stretch;
    }

    .company-page .guide-feature__media,
    .company-page .guide-card__image {
        position: relative;
        overflow: hidden;
        border-radius: 24px;
        background: #e9eef3;
    }

    .company-page .guide-feature__media {
        min-height: 420px;
    }

    .company-page .guide-card__image {
        min-height: 220px;
    }

    .company-page .guide-feature__media-link,
    .company-page .guide-feature__picture,
    .company-page .guide-feature__picture img,
    .company-page .guide-card__image-link,
    .company-page .guide-card__picture,
    .company-page .guide-card__picture img {
        display: block;
        width: 100%;
        height: 100%;
    }

    .company-page .guide-feature__picture img,
    .company-page .guide-card__picture img {
        object-fit: cover;
        object-position: center center;
    }

    .company-page .guide-feature__media.is-empty,
    .company-page .guide-card__image.is-empty {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 24px;
        text-align: center;
        color: #5d6b77;
        background: #f4f7fa;
    }

    @media (max-width: 767px) {
        .company-page .guide-feature {
            grid-template-columns: 1fr;
        }

        .company-page .guide-feature__media {
            min-height: 260px;
        }

        .company-page .guide-card__image {
            min-height: 180px;
        }
    }
</style>

<main id="primary" class="site-main company-page">

    <section class="company-hero">
        <picture class="company-hero__media" aria-hidden="true">
            <?php if ($hero_sp_url) : ?>
                <source media="(max-width: 767px)" srcset="<?php echo esc_url($hero_sp_url); ?>">
            <?php endif; ?>
            <img src="<?php echo esc_url($hero_pc_url); ?>" alt="<?php echo esc_attr($hero_title); ?>" loading="eager" fetchpriority="high">
        </picture>

        <div class="company-band-inner">
            <div class="company-hero__content">
                <div class="company-hero__eyebrow"><?php echo esc_html($hero_catch); ?></div>
                <h1><?php echo esc_html($hero_title); ?></h1>
                <p><?php echo esc_html($hero_lead); ?></p>
                <div class="company-hero__actions">
                    <?php echo ngc_btn($hero_btn1_url, $hero_btn1_text, 'ngc-btn--primary'); ?>
                    <?php echo ngc_btn($hero_btn2_url, $hero_btn2_text, 'ngc-btn--outline'); ?>
                </div>
            </div>
        </div>
    </section>

    <section class="summary-section">
        <div class="company-section-body">
            <div class="company-content-inner">
                <div class="summary-grid">
                    <?php foreach ($summary_cards as $card) : ?>
                        <article class="summary-card">
                            <h3><?php echo esc_html($card['title']); ?></h3>
                            <p><?php echo esc_html($card['text']); ?></p>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <section class="company-info">
        <div class="company-band">
            <div class="company-band-inner">
                <div class="ngc-section-head">
                    <p class="ngc-section-head__eyebrow"><?php echo esc_html($company_eyebrow); ?></p>
                    <h2><?php echo esc_html($company_title); ?></h2>
                </div>
            </div>
        </div>

        <div class="company-section-body">
            <div class="company-content-inner">
                <div class="ngc-card company-info__box">
                    <table class="company-info__table">
                        <tbody>
                            <?php foreach ($company_rows as $row) : ?>
                                <tr>
                                    <th><?php echo esc_html($row['label']); ?></th>
                                    <td><?php echo esc_html($row['value']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="company-info__subgrid">
                    <article class="ngc-card company-info__panel">
                        <h3><?php echo esc_html($business_title); ?></h3>
                        <ul class="company-list">
                            <?php foreach ($business_items as $item) : ?>
                                <li><?php echo esc_html($item); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </article>

                    <article class="ngc-card company-info__panel">
                        <h3><?php echo esc_html($info_guide_title); ?></h3>
                        <ul class="company-list company-list--guide">
                            <?php if ($info_guide_lead) : ?>
                                <li><?php echo esc_html($info_guide_lead); ?></li>
                            <?php endif; ?>
                            <li>
                                <a class="ngc-inline-link" href="<?php echo esc_url(ngc_meta_text($post_id, 'company_guide_1_url', $defaults['company_guide_1_url'])); ?>"><?php echo esc_html(ngc_meta_text($post_id, 'company_guide_1_title', $defaults['company_guide_1_title'])); ?></a>・
                                <a class="ngc-inline-link" href="<?php echo esc_url(ngc_meta_text($post_id, 'company_guide_2_url', $defaults['company_guide_2_url'])); ?>"><?php echo esc_html(ngc_meta_text($post_id, 'company_guide_2_title', $defaults['company_guide_2_title'])); ?></a>・
                                <a class="ngc-inline-link" href="<?php echo esc_url(ngc_meta_text($post_id, 'company_guide_3_url', $defaults['company_guide_3_url'])); ?>"><?php echo esc_html(ngc_meta_text($post_id, 'company_guide_3_title', $defaults['company_guide_3_title'])); ?></a>・
                                <a class="ngc-inline-link" href="<?php echo esc_url(ngc_meta_text($post_id, 'company_guide_4_url', $defaults['company_guide_4_url'])); ?>"><?php echo esc_html(ngc_meta_text($post_id, 'company_guide_4_title', $defaults['company_guide_4_title'])); ?></a>
                                <?php if ($info_guide_links) : ?>
                                    <?php echo esc_html(' ' . $info_guide_links); ?>
                                <?php endif; ?>
                            </li>
                            <?php if ($privacy_url && $privacy_label) : ?>
                                <li><a class="ngc-inline-link" href="<?php echo esc_url($privacy_url); ?>"><?php echo esc_html($privacy_label); ?></a></li>
                            <?php endif; ?>
                            <?php if ($terms_url && $terms_label) : ?>
                                <li><a class="ngc-inline-link" href="<?php echo esc_url($terms_url); ?>"><?php echo esc_html($terms_label); ?></a></li>
                            <?php endif; ?>
                        </ul>
                    </article>
                </div>

                <div class="group-grid">
                    <?php foreach ($group_companies as $company) : ?>
                        <article class="ngc-card group-card">
                            <h3><?php echo esc_html($company['name']); ?></h3>
                            <ul>
                                <?php foreach ($company['items'] as $item) : ?>
                                    <li><?php echo esc_html($item); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <section class="access-section">
        <div class="company-band">
            <div class="company-band-inner">
                <div class="ngc-section-head">
                    <p class="ngc-section-head__eyebrow"><?php echo esc_html($access_eyebrow); ?></p>
                    <h2><?php echo esc_html($access_title); ?></h2>
                    <p><?php echo esc_html($access_lead); ?></p>
                </div>
            </div>
        </div>

        <div class="company-section-body">
            <div class="company-content-inner">
                <div class="office-grid">
                    <?php foreach ($offices as $office) :
                        // 埋め込み地図は従来どおり住所ベースで表示する。
                        $map_embed_url = ngc_google_map_embed_url($office['address']);

                        // ボタンは優先順位を分ける。
                        // 1) メタボックスにビジネスプロフィールURLが入っていればそれを使う。
                        // 2) 空なら従来どおり住所ベースのGoogle Map検索リンクを使う。
                        $map_link_url  = !empty($office['profile_url'])
                            ? $office['profile_url']
                            : ngc_google_map_link($office['address']);
                    ?>
                        <article class="ngc-card office-card">
                            <div class="office-card__label"><?php echo esc_html($office['label']); ?></div>
                            <h3><?php echo esc_html($office['name']); ?></h3>
                            <p><?php echo esc_html($office['zip']); ?><br><?php echo esc_html($office['address']); ?><br>TEL: <?php echo esc_html($office['tel']); ?></p>
                            <div class="office-card__map">
                                <iframe src="<?php echo esc_url($map_embed_url); ?>" loading="lazy" allowfullscreen title="<?php echo esc_attr($office['name'] . ' の地図'); ?>"></iframe>
                            </div>
                            <div class="office-card__actions">
                                <?php echo ngc_btn($map_link_url, 'Google Mapで開く', 'ngc-btn--outline', '_blank'); ?>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <section class="sdgs-section">
        <div class="company-band">
            <div class="company-band-inner">
                <div class="ngc-section-head">
                    <p class="ngc-section-head__eyebrow"><?php echo esc_html($sdgs_eyebrow); ?></p>
                    <h2><?php echo esc_html($sdgs_title); ?></h2>
                    <p><?php echo esc_html($sdgs_lead); ?></p>
                </div>
            </div>
        </div>

        <div class="company-section-body">
            <div class="company-content-inner">
                <div class="sdgs-grid">
                    <?php foreach ($sdgs_cards as $card) : ?>
                        <article class="sdgs-card <?php echo esc_attr($card['tone']); ?>">
                            <div class="sdgs-card__goal"><?php echo esc_html($card['goal']); ?></div>
                            <h3><?php echo esc_html($card['title']); ?></h3>
                            <p><?php echo esc_html($card['text']); ?></p>
                        </article>
                    <?php endforeach; ?>
                </div>
                <?php if ($sdgs_note) : ?>
                    <p class="sdgs-note"><?php echo esc_html($sdgs_note); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section class="guide-section">
        <div class="company-band">
            <div class="company-band-inner">
                <div class="ngc-section-head">
                    <p class="ngc-section-head__eyebrow"><?php echo esc_html($guide_section_eyebrow); ?></p>
                    <h2><?php echo esc_html($guide_section_title); ?></h2>
                </div>
            </div>
        </div>

        <div class="company-section-body">
            <div class="company-content-inner">
                <div class="guide-stack">

                    <article class="guide-feature">
                        <div class="guide-feature__body">
                            <p class="guide-feature__eyebrow">Living Guide</p>
                            <h3><?php echo esc_html($living_guide_title); ?></h3>
                            <p><?php echo esc_html($living_guide_text); ?></p>
                            <div class="guide-feature__actions">
                                <a class="ngc-btn ngc-btn--primary" href="<?php echo esc_url($living_guide_url); ?>">
                                    <?php echo esc_html($living_guide_label); ?>
                                </a>
                            </div>
                        </div>

                        <?php if (!empty($living_guide_image_pc)) : ?>
                            <div class="guide-feature__media">
                                <a class="guide-feature__media-link" href="<?php echo esc_url($living_guide_url); ?>" aria-label="<?php echo esc_attr($living_guide_title); ?>">
                                    <picture class="guide-feature__picture">
                                        <?php if (!empty($living_guide_image_sp)) : ?>
                                            <source media="(max-width: 767px)" srcset="<?php echo esc_url($living_guide_image_sp); ?>">
                                        <?php endif; ?>
                                        <img src="<?php echo esc_url($living_guide_image_pc); ?>" alt="<?php echo esc_attr($living_guide_title); ?>" loading="lazy">
                                    </picture>
                                </a>
                            </div>
                        <?php elseif ($is_company_editor) : ?>
                            <div class="guide-feature__media is-empty">
                                会社概要ページ設定 → 住まい案内（Living Guide）設定 → 住まい案内画像（PC / SP）で設定してください
                            </div>
                        <?php else : ?>
                            <div class="guide-feature__media is-empty" aria-hidden="true"></div>
                        <?php endif; ?>
                    </article>

                    <div class="guide-grid">
                        <?php foreach ($guide_cards as $card) : ?>
                            <article class="guide-card">
                                <?php if (!empty($card['image_pc'])) : ?>
                                    <div class="guide-card__image">
                                        <a class="guide-card__image-link" href="<?php echo esc_url($card['url']); ?>" aria-label="<?php echo esc_attr($card['title']); ?>">
                                            <picture class="guide-card__picture">
                                                <?php if (!empty($card['image_sp'])) : ?>
                                                    <source media="(max-width: 767px)" srcset="<?php echo esc_url($card['image_sp']); ?>">
                                                <?php endif; ?>
                                                <img src="<?php echo esc_url($card['image_pc']); ?>" alt="<?php echo esc_attr($card['title']); ?>" loading="lazy">
                                            </picture>
                                        </a>
                                    </div>
                                <?php elseif ($is_company_editor) : ?>
                                    <div class="guide-card__image is-empty">
                                        会社概要ページ設定 → Guide カード設定 → カード画像（PC / SP）で設定してください
                                    </div>
                                <?php else : ?>
                                    <div class="guide-card__image is-empty" aria-hidden="true"></div>
                                <?php endif; ?>

                                <div class="guide-card__body">
                                    <h3><?php echo esc_html($card['title']); ?></h3>
                                    <p><?php echo esc_html($card['text']); ?></p>
                                    <div class="guide-card__actions">
                                        <a class="ngc-btn <?php echo esc_attr($card['class']); ?>" href="<?php echo esc_url($card['url']); ?>">
                                            <?php echo esc_html($card['label']); ?>
                                        </a>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <section class="final-cta">
        <div class="company-band-inner">
            <div class="final-cta__box">
                <h2><?php echo esc_html($final_cta_title); ?></h2>
                <div class="final-cta__actions">
                    <?php echo ngc_btn($final_cta_btn1_url, $final_cta_btn1_text, 'ngc-btn--primary'); ?>
                    <?php echo ngc_btn($final_cta_btn2_url, $final_cta_btn2_text, 'ngc-btn--primary'); ?>
                    <?php echo ngc_btn($final_cta_btn3_url, $final_cta_btn3_text, 'ngc-btn--primary'); ?>
                </div>
            </div>
        </div>
    </section>

</main>

<?php get_footer(); ?>