<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * =========================================================
 * 会社概要ページ メタボックス
 * 対象テンプレート: page-company.php
 *
 * 追加内容
 * - 各拠点に「GoogleビジネスプロフィールURL」入力欄を追加
 * - 空の場合は従来どおり住所ベースの Google Map を使う
 * - 入力がある場合だけ、フロント側の「Google Mapで開く」ボタンを
 *   ビジネスプロフィールURLへ切り替えられるようにする
 *
 * 注意
 * - デザイン用CSSやフロント表示の見た目は変更しない
 * - このファイルはメタボックス/保存側のみ
 * =========================================================
 */

if (!function_exists('ngc_company_template_slug')) {
    function ngc_company_template_slug()
    {
        return 'page-company.php';
    }
}

if (!function_exists('ngc_company_get_current_post_id')) {
    function ngc_company_get_current_post_id()
    {
        if (isset($_GET['post'])) {
            return absint($_GET['post']);
        }

        if (isset($_POST['post_ID'])) {
            return absint(wp_unslash($_POST['post_ID']));
        }

        return 0;
    }
}

if (!function_exists('ngc_company_get_request_template_slug')) {
    function ngc_company_get_request_template_slug($post_id = 0)
    {
        if (isset($_POST['_wp_page_template'])) {
            return sanitize_text_field(wp_unslash($_POST['_wp_page_template']));
        }

        if (isset($_POST['page_template'])) {
            return sanitize_text_field(wp_unslash($_POST['page_template']));
        }

        $post_id = absint($post_id);
        if ($post_id > 0) {
            return (string) get_page_template_slug($post_id);
        }

        return '';
    }
}

if (!function_exists('ngc_is_company_template')) {
    function ngc_is_company_template($post_id = 0)
    {
        $post_id = absint($post_id);
        if (!$post_id) {
            return false;
        }

        return get_post_type($post_id) === 'page'
            && get_page_template_slug($post_id) === ngc_company_template_slug();
    }
}

if (!function_exists('ngc_company_is_page_editor_screen')) {
    function ngc_company_is_page_editor_screen($hook)
    {
        if (!in_array($hook, array('post.php', 'post-new.php'), true)) {
            return false;
        }

        $screen = get_current_screen();
        if (!$screen) {
            return false;
        }

        return $screen->post_type === 'page';
    }
}

if (!function_exists('ngc_company_should_load_admin_assets')) {
    function ngc_company_should_load_admin_assets($hook)
    {
        if (!ngc_company_is_page_editor_screen($hook)) {
            return false;
        }

        $post_id = ngc_company_get_current_post_id();
        if (!$post_id) {
            return false;
        }

        return ngc_is_company_template($post_id)
            || ngc_company_get_request_template_slug($post_id) === ngc_company_template_slug();
    }
}

if (!function_exists('ngc_company_default_meta')) {
    function ngc_company_default_meta($post_id = 0)
    {
        $post_id = absint($post_id);

        $page_title        = $post_id ? (get_the_title($post_id) ?: '会社概要') : '会社概要';
        $land_url          = home_url('/naigai-tochi/');
        $building_url      = home_url('/room-gallary/');
        $assessment_url    = home_url('/satei/');
        $reservation_url   = home_url('/reservation/');
        $recruitment_url   = home_url('/recruitment/');
        $privacy_url       = function_exists('get_privacy_policy_url') ? get_privacy_policy_url() : '';
        $terms_url         = home_url('/terms/');
        $living_guide_page = get_page_by_path('nasu-ideal-home', OBJECT, 'page');

        $default_living_guide_url   = $living_guide_page ? get_permalink($living_guide_page->ID) : home_url('/nasu-ideal-home/');
        $default_living_guide_title = $living_guide_page ? get_the_title($living_guide_page->ID) : '那須で理想の住まいを考える';
        $default_living_guide_text  = '土地から考える、住まいのスタイルから考える、分譲地・北米住宅・在来工法など、那須での暮らし方に合わせた住まい選びをご案内します。';

        $defaults = array(
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
            // 追加: 本社のGoogleビジネスプロフィールURL。空なら従来どおり住所検索リンクを使う。
            'company_office_1_profile_url'   => '',
            'company_office_2_label'         => '那須事務所',
            'company_office_2_name'          => '内外建設株式会社',
            'company_office_2_zip'           => '〒325-0021',
            'company_office_2_address'       => '那須塩原市安藤町40-430',
            'company_office_2_tel'           => '0287-62-1011',
            // 追加: 那須事務所のGoogleビジネスプロフィールURL。空なら従来どおり住所検索リンクを使う。
            'company_office_2_profile_url'   => '',

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

        $image_keys = array(
            'company_hero_image_pc',
            'company_hero_image_sp',
            'company_living_guide_image_pc',
            'company_living_guide_image_sp',
        );

        for ($i = 1; $i <= 4; $i++) {
            $image_keys[] = 'company_guide_' . $i . '_image_pc';
            $image_keys[] = 'company_guide_' . $i . '_image_sp';
        }

        foreach ($image_keys as $image_key) {
            $defaults[$image_key] = 0;
        }

        return $defaults;
    }
}

if (!function_exists('ngc_company_seed_default_meta')) {
    function ngc_company_seed_default_meta($post_id)
    {
        $post_id = absint($post_id);
        if (!$post_id || get_post_type($post_id) !== 'page') {
            return;
        }

        if (get_page_template_slug($post_id) !== ngc_company_template_slug()) {
            return;
        }

        $defaults = ngc_company_default_meta($post_id);
        foreach ($defaults as $key => $default_value) {
            $existing = get_post_meta($post_id, $key, true);
            if ($existing === '') {
                update_post_meta($post_id, $key, $default_value);
            }
        }
    }
}

if (!function_exists('ngc_company_text_value')) {
    function ngc_company_text_value($post_id, $key)
    {
        $defaults = ngc_company_default_meta($post_id);
        $value    = get_post_meta($post_id, $key, true);

        if ($value !== '') {
            return $value;
        }

        return isset($defaults[$key]) ? $defaults[$key] : '';
    }
}

if (!function_exists('ngc_render_media_field')) {
    function ngc_render_media_field($name, $value = 0, $label = '画像')
    {
        $image_id  = absint($value);
        $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'medium') : '';

        echo '<div class="ngc-media-field">';
        echo '<p><strong>' . esc_html($label) . '</strong></p>';
        echo '<input type="hidden" class="ngc-media-id" name="' . esc_attr($name) . '" value="' . esc_attr($image_id) . '">';

        if ($image_url) {
            echo '<div class="ngc-media-preview"><img src="' . esc_url($image_url) . '" alt=""></div>';
        } else {
            echo '<div class="ngc-media-preview is-empty">画像が未選択です</div>';
        }

        echo '<div class="ngc-media-actions">';
        echo '<button type="button" class="button button-secondary ngc-media-select">画像を選択</button>';
        echo '<button type="button" class="button ngc-media-remove"' . ($image_id ? '' : ' style="display:none;"') . '>画像を削除</button>';
        echo '</div>';
        echo '</div>';
    }
}

if (!function_exists('ngc_render_text_input')) {
    function ngc_render_text_input($post_id, $key, $label)
    {
        echo '<p>' . esc_html($label) . ': <input type="text" name="' . esc_attr($key) . '" value="' . esc_attr(ngc_company_text_value($post_id, $key)) . '" class="widefat"></p>';
    }
}

if (!function_exists('ngc_render_textarea_input')) {
    function ngc_render_textarea_input($post_id, $key, $label, $rows = 4, $description = '')
    {
        echo '<p>' . esc_html($label) . ': <textarea name="' . esc_attr($key) . '" class="widefat" rows="' . absint($rows) . '">' . esc_textarea(ngc_company_text_value($post_id, $key)) . '</textarea></p>';
        if ($description) {
            echo '<p class="description">' . esc_html($description) . '</p>';
        }
    }
}

if (!function_exists('ngc_render_select_input')) {
    function ngc_render_select_input($post_id, $key, $label, $options)
    {
        $current = ngc_company_text_value($post_id, $key);

        echo '<p>' . esc_html($label) . ': ';
        echo '<select name="' . esc_attr($key) . '" class="widefat">';
        foreach ((array) $options as $value => $text) {
            echo '<option value="' . esc_attr($value) . '" ' . selected($current, $value, false) . '>' . esc_html($text) . '</option>';
        }
        echo '</select></p>';
    }
}

if (!function_exists('ngc_company_admin_enqueue_media')) {
    function ngc_company_admin_enqueue_media($hook)
    {
        if (!ngc_company_should_load_admin_assets($hook)) {
            return;
        }

        wp_enqueue_media();

        wp_add_inline_style(
            'common',
            '
            .ngc-media-field{margin:12px 0 18px}
            .ngc-media-preview{margin:8px 0 10px;padding:12px;border:1px solid #dcdcde;background:#fff;min-height:90px;display:flex;align-items:center;justify-content:center}
            .ngc-media-preview.is-empty{color:#666;font-size:13px}
            .ngc-media-preview img{display:block;max-width:100%;height:auto}
            .ngc-media-actions{display:flex;gap:8px;flex-wrap:wrap}
            .ngc-box{padding:18px 0;border-top:1px solid #ddd}
            .ngc-box:first-child{padding-top:0;border-top:0}
            .ngc-grid-2{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px}
            .ngc-grid-3{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px}
            .ngc-card-block{padding:14px 0;border-top:1px solid #ddd}
            .ngc-card-block:first-of-type{border-top:0;padding-top:0}
            .ngc-notice{margin:0 0 12px;padding:10px 12px;background:#f6f7f7;border:1px solid #dcdcde}
            @media (max-width:782px){.ngc-grid-2,.ngc-grid-3{grid-template-columns:1fr;}}
            '
        );

        wp_add_inline_script(
            'jquery-core',
            <<<'JS'
jQuery(function ($) {
    $(document).on('click', '.ngc-media-select', function (e) {
        e.preventDefault();

        var $field = $(this).closest('.ngc-media-field');
        var $input = $field.find('.ngc-media-id');
        var $preview = $field.find('.ngc-media-preview');
        var $remove = $field.find('.ngc-media-remove');

        var frame = wp.media({
            title: '画像を選択',
            button: { text: 'この画像を使用' },
            multiple: false,
            library: { type: 'image' }
        });

        frame.on('select', function () {
            var attachment = frame.state().get('selection').first().toJSON();
            var imageUrl = attachment.url;

            if (attachment.sizes) {
                if (attachment.sizes.medium) {
                    imageUrl = attachment.sizes.medium.url;
                } else if (attachment.sizes.thumbnail) {
                    imageUrl = attachment.sizes.thumbnail.url;
                }
            }

            $input.val(attachment.id);
            $preview.removeClass('is-empty').html('<img src="' + imageUrl + '" alt="">');
            $remove.show();
        });

        frame.open();
    });

    $(document).on('click', '.ngc-media-remove', function (e) {
        e.preventDefault();

        var $field = $(this).closest('.ngc-media-field');
        $field.find('.ngc-media-id').val('');
        $field.find('.ngc-media-preview').addClass('is-empty').html('画像が未選択です');
        $(this).hide();
    });
});
JS
        );
    }
}
add_action('admin_enqueue_scripts', 'ngc_company_admin_enqueue_media');

if (!function_exists('ngc_add_company_metabox')) {
    function ngc_add_company_metabox($post)
    {
        if (!$post instanceof WP_Post || $post->post_type !== 'page') {
            return;
        }

        $template = ngc_company_get_request_template_slug($post->ID);
        if ($template !== ngc_company_template_slug()) {
            return;
        }

        add_meta_box(
            'ngc_company_page_metabox',
            '会社概要ページ設定',
            'ngc_company_metabox_callback',
            'page',
            'normal',
            'high'
        );
    }
}
add_action('add_meta_boxes_page', 'ngc_add_company_metabox');

if (!function_exists('ngc_company_metabox_callback')) {
    function ngc_company_metabox_callback($post)
    {
        ngc_company_seed_default_meta($post->ID);
        wp_nonce_field('ngc_company_save', 'ngc_company_nonce');

        $tone_options = array(
            'is-sky'   => 'is-sky',
            'is-amber' => 'is-amber',
            'is-gray'  => 'is-gray',
            'is-green' => 'is-green',
            'is-gold'  => 'is-gold',
            'is-rose'  => 'is-rose',
        );

        echo '<div class="ngc-notice">';
        echo '会社概要テンプレート（page-company.php）専用です。初期文言はメタキーへ投入済みです。';
        echo '</div>';

        echo '<div class="ngc-box">';
        echo '<p><strong>Hero 設定</strong></p>';
        echo '<div class="ngc-grid-2">';
        ngc_render_media_field('company_hero_image_pc', ngc_company_text_value($post->ID, 'company_hero_image_pc'), 'Hero画像（PC）');
        ngc_render_media_field('company_hero_image_sp', ngc_company_text_value($post->ID, 'company_hero_image_sp'), 'Hero画像（SP）');
        echo '</div>';
        ngc_render_text_input($post->ID, 'company_hero_catch', 'Catch');
        ngc_render_text_input($post->ID, 'company_hero_title', 'Title');
        ngc_render_textarea_input($post->ID, 'company_hero_lead', 'Lead', 4);
        ngc_render_text_input($post->ID, 'company_hero_btn1_text', 'ボタン1文言');
        ngc_render_text_input($post->ID, 'company_hero_btn1_url', 'ボタン1URL');
        ngc_render_text_input($post->ID, 'company_hero_btn2_text', 'ボタン2文言');
        ngc_render_text_input($post->ID, 'company_hero_btn2_url', 'ボタン2URL');
        echo '</div>';

        echo '<div class="ngc-box">';
        echo '<p><strong>Summary 設定</strong></p>';
        for ($i = 1; $i <= 3; $i++) {
            echo '<div class="ngc-card-block">';
            echo '<p><strong>カード' . (int) $i . '</strong></p>';
            ngc_render_text_input($post->ID, 'company_summary_card_' . $i . '_title', 'タイトル');
            ngc_render_textarea_input($post->ID, 'company_summary_card_' . $i . '_text', '本文', 3);
            echo '</div>';
        }
        echo '</div>';

        echo '<div class="ngc-box">';
        echo '<p><strong>会社情報セクション設定</strong></p>';
        ngc_render_text_input($post->ID, 'company_company_eyebrow', 'アイブロウ');
        ngc_render_text_input($post->ID, 'company_company_title', '見出し');
        for ($i = 1; $i <= 5; $i++) {
            echo '<div class="ngc-grid-2">';
            ngc_render_text_input($post->ID, 'company_info_row_' . $i . '_label', '表ラベル ' . $i);
            ngc_render_text_input($post->ID, 'company_info_row_' . $i . '_value', '表の値 ' . $i);
            echo '</div>';
        }
        ngc_render_text_input($post->ID, 'company_business_title', '事業内容 見出し');
        ngc_render_textarea_input($post->ID, 'company_business_items', '事業内容（1行1項目）', 10, '改行ごとに1項目として表示されます。');
        ngc_render_text_input($post->ID, 'company_info_guide_title', 'ご案内 見出し');
        ngc_render_textarea_input($post->ID, 'company_info_guide_lead', 'ご案内 リード', 3);
        ngc_render_textarea_input($post->ID, 'company_info_guide_links_text', 'ご案内 補足文', 2);
        ngc_render_text_input($post->ID, 'company_privacy_label', 'プライバシーポリシー文言');
        ngc_render_text_input($post->ID, 'company_privacy_url', 'プライバシーポリシーURL');
        ngc_render_text_input($post->ID, 'company_terms_label', '利用規約文言');
        ngc_render_text_input($post->ID, 'company_terms_url', '利用規約URL');
        echo '</div>';

        echo '<div class="ngc-box">';
        echo '<p><strong>グループ会社設定</strong></p>';
        for ($i = 1; $i <= 3; $i++) {
            echo '<div class="ngc-card-block">';
            echo '<p><strong>会社' . (int) $i . '</strong></p>';
            ngc_render_text_input($post->ID, 'company_group_' . $i . '_name', '会社名');
            ngc_render_textarea_input($post->ID, 'company_group_' . $i . '_items', '項目（1行1項目）', 6);
            echo '</div>';
        }
        echo '</div>';

        echo '<div class="ngc-box">';
        echo '<p><strong>所在地・アクセス設定</strong></p>';
        ngc_render_text_input($post->ID, 'company_access_eyebrow', 'アイブロウ');
        ngc_render_text_input($post->ID, 'company_access_title', '見出し');
        ngc_render_textarea_input($post->ID, 'company_access_lead', 'リード', 3);
        for ($i = 1; $i <= 2; $i++) {
            echo '<div class="ngc-card-block">';
            echo '<p><strong>拠点' . (int) $i . '</strong></p>';
            ngc_render_text_input($post->ID, 'company_office_' . $i . '_label', 'ラベル');
            ngc_render_text_input($post->ID, 'company_office_' . $i . '_name', '会社名');
            echo '<div class="ngc-grid-3">';
            ngc_render_text_input($post->ID, 'company_office_' . $i . '_zip', '郵便番号');
            ngc_render_text_input($post->ID, 'company_office_' . $i . '_tel', '電話番号');
            ngc_render_text_input($post->ID, 'company_office_' . $i . '_address', '住所');
            echo '</div>';
            // 追加: ここにGoogleビジネスプロフィールURLを入れる。
            // 空の場合はフロント側で従来どおり住所検索のGoogle Mapリンクへ自動で戻る。
            ngc_render_text_input($post->ID, 'company_office_' . $i . '_profile_url', 'GoogleビジネスプロフィールURL');
            echo '</div>';
        }
        echo '</div>';

        echo '<div class="ngc-box">';
        echo '<p><strong>SDGs 設定</strong></p>';
        ngc_render_text_input($post->ID, 'company_sdgs_eyebrow', 'アイブロウ');
        ngc_render_text_input($post->ID, 'company_sdgs_title', '見出し');
        ngc_render_textarea_input($post->ID, 'company_sdgs_lead', 'リード', 4);
        ngc_render_text_input($post->ID, 'company_sdgs_note', '注記');
        for ($i = 1; $i <= 9; $i++) {
            echo '<div class="ngc-card-block">';
            echo '<p><strong>SDGsカード' . (int) $i . '</strong></p>';
            echo '<div class="ngc-grid-3">';
            ngc_render_text_input($post->ID, 'company_sdgs_' . $i . '_goal', 'ゴール表示');
            ngc_render_text_input($post->ID, 'company_sdgs_' . $i . '_title', 'タイトル');
            ngc_render_select_input($post->ID, 'company_sdgs_' . $i . '_tone', 'トーン', $tone_options);
            echo '</div>';
            ngc_render_textarea_input($post->ID, 'company_sdgs_' . $i . '_text', '本文', 4);
            echo '</div>';
        }
        echo '</div>';

        echo '<div class="ngc-box">';
        echo '<p><strong>住まい案内（Living Guide）設定</strong></p>';
        ngc_render_text_input($post->ID, 'company_guide_section_eyebrow', 'Guide セクション アイブロウ');
        ngc_render_text_input($post->ID, 'company_guide_section_title', 'Guide セクション 見出し');
        ngc_render_text_input($post->ID, 'company_living_guide_title', 'タイトル');
        ngc_render_textarea_input($post->ID, 'company_living_guide_text', '本文', 4);
        ngc_render_text_input($post->ID, 'company_living_guide_label', 'ボタン文言');
        ngc_render_text_input($post->ID, 'company_living_guide_url', 'リンクURL');
        echo '<div class="ngc-grid-2">';
        ngc_render_media_field('company_living_guide_image_pc', ngc_company_text_value($post->ID, 'company_living_guide_image_pc'), '住まい案内画像（PC）');
        ngc_render_media_field('company_living_guide_image_sp', ngc_company_text_value($post->ID, 'company_living_guide_image_sp'), '住まい案内画像（SP）');
        echo '</div>';
        echo '</div>';

        echo '<div class="ngc-box">';
        echo '<p><strong>Guide カード設定</strong></p>';
        for ($i = 1; $i <= 4; $i++) {
            echo '<div class="ngc-card-block">';
            echo '<p><strong>カード' . (int) $i . '</strong></p>';
            ngc_render_text_input($post->ID, 'company_guide_' . $i . '_title', 'タイトル');
            ngc_render_textarea_input($post->ID, 'company_guide_' . $i . '_text', '本文', 3);
            ngc_render_text_input($post->ID, 'company_guide_' . $i . '_url', 'リンクURL');
            ngc_render_text_input($post->ID, 'company_guide_' . $i . '_label', 'ボタン文言');
            echo '<div class="ngc-grid-2">';
            ngc_render_media_field('company_guide_' . $i . '_image_pc', ngc_company_text_value($post->ID, 'company_guide_' . $i . '_image_pc'), 'カード画像（PC）');
            ngc_render_media_field('company_guide_' . $i . '_image_sp', ngc_company_text_value($post->ID, 'company_guide_' . $i . '_image_sp'), 'カード画像（SP）');
            echo '</div>';
            echo '</div>';
        }
        echo '</div>';

        echo '<div class="ngc-box">';
        echo '<p><strong>Final CTA 設定</strong></p>';
        ngc_render_text_input($post->ID, 'company_final_cta_title', '見出し');
        for ($i = 1; $i <= 3; $i++) {
            echo '<div class="ngc-grid-2">';
            ngc_render_text_input($post->ID, 'company_final_cta_btn' . $i . '_text', 'ボタン' . $i . ' 文言');
            ngc_render_text_input($post->ID, 'company_final_cta_btn' . $i . '_url', 'ボタン' . $i . ' URL');
            echo '</div>';
        }
        echo '</div>';
    }
}

if (!function_exists('ngc_save_company_metabox')) {
    function ngc_save_company_metabox($post_id)
    {
        if (!isset($_POST['ngc_company_nonce'])) {
            return;
        }

        $nonce = sanitize_text_field(wp_unslash($_POST['ngc_company_nonce']));
        if (!wp_verify_nonce($nonce, 'ngc_company_save')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (wp_is_post_revision($post_id)) {
            return;
        }

        if (get_post_type($post_id) !== 'page') {
            return;
        }

        if (!current_user_can('edit_page', $post_id)) {
            return;
        }

        $template = ngc_company_get_request_template_slug($post_id);
        if ($template !== ngc_company_template_slug()) {
            return;
        }

        $defaults = ngc_company_default_meta($post_id);

        $textarea_fields = array(
            'company_hero_lead',
            'company_business_items',
            'company_info_guide_lead',
            'company_info_guide_links_text',
            'company_access_lead',
            'company_sdgs_lead',
            'company_living_guide_text',
        );

        for ($i = 1; $i <= 3; $i++) {
            $textarea_fields[] = 'company_summary_card_' . $i . '_text';
            $textarea_fields[] = 'company_group_' . $i . '_items';
        }

        for ($i = 1; $i <= 9; $i++) {
            $textarea_fields[] = 'company_sdgs_' . $i . '_text';
        }

        for ($i = 1; $i <= 4; $i++) {
            $textarea_fields[] = 'company_guide_' . $i . '_text';
        }

        $image_fields = array(
            'company_hero_image_pc',
            'company_hero_image_sp',
            'company_living_guide_image_pc',
            'company_living_guide_image_sp',
        );

        for ($i = 1; $i <= 4; $i++) {
            $image_fields[] = 'company_guide_' . $i . '_image_pc';
            $image_fields[] = 'company_guide_' . $i . '_image_sp';
        }

        $url_fields = array(
            'company_hero_btn1_url',
            'company_hero_btn2_url',
            'company_privacy_url',
            'company_terms_url',
            'company_living_guide_url',
            'company_final_cta_btn1_url',
            'company_final_cta_btn2_url',
            'company_final_cta_btn3_url',
            // 追加: 拠点別のビジネスプロフィールURL。
            'company_office_1_profile_url',
            'company_office_2_profile_url',
        );

        for ($i = 1; $i <= 4; $i++) {
            $url_fields[] = 'company_guide_' . $i . '_url';
        }

        foreach ($defaults as $key => $default_value) {
            if (in_array($key, $image_fields, true)) {
                $value = isset($_POST[$key]) ? absint(wp_unslash($_POST[$key])) : 0;
                update_post_meta($post_id, $key, $value);
                continue;
            }

            if (in_array($key, $textarea_fields, true)) {
                $value = isset($_POST[$key]) ? sanitize_textarea_field(wp_unslash($_POST[$key])) : $default_value;
                update_post_meta($post_id, $key, $value);
                continue;
            }

            if (in_array($key, $url_fields, true)) {
                $value = isset($_POST[$key]) ? esc_url_raw(wp_unslash($_POST[$key])) : $default_value;
                update_post_meta($post_id, $key, $value);
                continue;
            }

            $value = isset($_POST[$key]) ? sanitize_text_field(wp_unslash($_POST[$key])) : $default_value;
            update_post_meta($post_id, $key, $value);
        }

        ngc_company_seed_default_meta($post_id);
    }
}
add_action('save_post_page', 'ngc_save_company_metabox');
