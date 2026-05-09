<?php
if (!defined('ABSPATH')) {
    exit;
}

/* =========================================================
 * 対象テンプレート判定
 * ========================================================= */
if (!function_exists('ngu_is_target_template')) {
    function ngu_is_target_template($post_id = 0)
    {
        $post_id = $post_id ?: get_the_ID();

        if (!$post_id) {
            return false;
        }

        return get_post_type($post_id) === 'page'
            && get_page_template_slug($post_id) === 'page-nasu-guide.php';
    }
}

/* =========================================================
 * デフォルトURL解決
 * ========================================================= */
if (!function_exists('ngu_default_page_url')) {
    function ngu_default_page_url($slug, $fallback_path)
    {
        $page = get_page_by_path($slug, OBJECT, 'page');

        if ($page instanceof WP_Post) {
            $url = get_permalink($page->ID);
            if ($url) {
                return $url;
            }
        }

        return home_url($fallback_path);
    }
}

/* =========================================================
 * FAQデフォルト
 * ========================================================= */
if (!function_exists('ngu_page_default_faq_items')) {
    function ngu_page_default_faq_items()
    {
        return [];
    }
}

/* =========================================================
 * デフォルト値
 * ========================================================= */
if (!function_exists('ngu_page_defaults')) {
    function ngu_page_defaults()
    {
        return [
            '_ngu_hero_eyebrow'   => 'NASU GUIDE',
            '_ngu_hero_title'     => '那須で理想の住まいを考える',
            '_ngu_hero_lead'      => '分譲地から考える住まいづくり、北米住宅、自然素材の住まいまで、那須での暮らし方に合わせてご案内します。',
            '_ngu_hero_btn1_text' => '分譲地から考える',
            '_ngu_hero_btn1_url'  => '#land-guide',
            '_ngu_hero_btn2_text' => '住まいから考える',
            '_ngu_hero_btn2_url'  => '#style-guide',

            '_ngu_intro_eyebrow' => 'Concept',
            '_ngu_intro_title'   => '那須での住まいの考え方',
            '_ngu_intro_text'    => '土地から考えるか、住まいのスタイルから考えるか。那須での暮らし方に合わせて、わかりやすくご案内します。',

            '_ngu_branch_eyebrow' => 'Choose your way',
            '_ngu_branch_title'   => '土地から考えるか、住まいから考えるか',
            '_ngu_branch_text'    => '那須での住まいづくりを、わかりやすく二つの導線で整理しました。',

            '_ngu_land_branch_title'    => '分譲地から考える',
            '_ngu_land_branch_text'     => '黒田原分譲地など、区画・立地・広さから家づくりを考えたい方へ。',
            '_ngu_land_branch_btn_text' => '分譲地・土地情報を見る',
            '_ngu_land_branch_btn_url'  => home_url('/naigai-tochi/'),

            '_ngu_style_branch_title'    => '住まいから考える',
            '_ngu_style_branch_text'     => '北米住宅と自然素材の住まい、それぞれの特徴から理想の暮らし方を考えたい方へ。',
            '_ngu_style_branch_btn_text' => '住宅一覧を見る',
            '_ngu_style_branch_btn_url'  => home_url('/house/'),

            '_ngu_land_eyebrow'      => 'Land guide',
            '_ngu_land_head_title'   => '黒田原分譲地のご案内',
            '_ngu_land_head_text'    => '分譲地の特徴や土地選びの考え方を、住まいづくりの入口としてご案内します。',
            '_ngu_land_sec_title'    => '区画と周辺環境を確認する',
            '_ngu_land_sec_text'     => '区画の特徴、周辺環境、接道状況、建て方の考え方を整理しながら、定住・移住・別荘それぞれに合う土地選びをご紹介します。',
            '_ngu_land_sec_btn_text' => '黒田原分譲地を見る',
            '_ngu_land_sec_btn_url'  => home_url('/naigai-tochi/'),

            '_ngu_style_eyebrow'   => 'Home style',
            '_ngu_style_sec_title' => '住まいのスタイル',
            '_ngu_style_sec_text'  => '北米住宅と自然素材の住まい、それぞれの魅力を暮らしのイメージとともにご案内します。',

            '_ngu_north_title'    => '北米住宅',
            '_ngu_north_text'     => 'ガレージ付き住宅、輸入住宅の雰囲気、ゆとりある住まい方を提案します。',
            '_ngu_north_btn_text' => '北米住宅を見る',
            '_ngu_north_btn_url'  => ngu_default_page_url('hokubei-jutaku', '/hokubei-jutaku/'),

            '_ngu_postbeam_title'    => '自然素材の住まい',
            '_ngu_postbeam_text'     => '無垢材や自然素材を活かし、木のぬくもりを感じる住まい。梁や柱を活かした空間設計で、落ち着いた暮らしを提案します。',
            '_ngu_postbeam_btn_text' => '自然素材の住まいを見る',
            '_ngu_postbeam_btn_url'  => ngu_default_page_url('zairai-kouhou', '/zairai-kouhou/'),

            '_ngu_used_eyebrow'    => 'Used Renovation',
            '_ngu_used_title'      => '中古住宅リノベという選び方',
            '_ngu_used_text'       => '新築だけでなく、中古住宅を活かして暮らし方に合う住まいを整えたい方へ。購入前の検討から改修の考え方までご案内します。',
            '_ngu_used_body_title' => '購入前から改修イメージを整理したい方へ',
            '_ngu_used_body_text'  => '間取りの見直し、断熱・設備更新、内装や外装の整え方まで、那須での定住・二拠点生活・セカンドハウスにも合う中古住宅リノベの考え方をまとめています。',
            '_ngu_used_btn_text'   => '中古リノベのページを見る',
            '_ngu_used_btn_url'    => home_url('/nasu-used-renovation/'),

            '_ngu_support_eyebrow' => 'Support',
            '_ngu_biz_title'       => '個人のお客様・事業者様への対応',
            '_ngu_b2c_title'       => '個人のお客様へ',
            '_ngu_b2c_text'        => '定住・別荘・二拠点生活を検討される個人のお客様向けのご相談。',
            '_ngu_b2b_title'       => '法人・事業者様へ',
            '_ngu_b2b_text'        => '分譲地活用、区画利用、法人・事業者様向けのご相談にも対応。',

            '_ngu_company_title'    => '会社案内',
            '_ngu_company_text'     => '内外グループは、那須での土地探し・住まいづくり・分譲地のご案内まで対応しています。',
            '_ngu_company_btn_text' => '会社概要を見る',
            '_ngu_company_btn_url'  => home_url('/company/'),

            '_ngu_faq_eyebrow' => 'FAQ',
            '_ngu_faq_title'   => 'よくある質問',

            '_ngu_cta_title'     => '那須での住まい探し・土地探しをご相談ください',
            '_ngu_cta_text'      => '分譲地・住宅・会社案内・ご相談まで、お気軽にお問い合わせください。',
            '_ngu_cta_btn1_text' => '来店予約',
            '_ngu_cta_btn1_url'  => home_url('/reservation/'),
            '_ngu_cta_btn2_text' => 'お問い合わせ',
            '_ngu_cta_btn2_url'  => home_url('/contact/'),
        ];
    }
}

/* =========================================================
 * フィールド一覧
 * ========================================================= */
if (!function_exists('ngu_plain_text_fields')) {
    function ngu_plain_text_fields()
    {
        return [
            '_ngu_hero_eyebrow',
            '_ngu_hero_title',
            '_ngu_hero_btn1_text',
            '_ngu_hero_btn2_text',
            '_ngu_intro_eyebrow',
            '_ngu_intro_title',
            '_ngu_branch_eyebrow',
            '_ngu_branch_title',
            '_ngu_land_branch_title',
            '_ngu_land_branch_btn_text',
            '_ngu_style_branch_title',
            '_ngu_style_branch_btn_text',
            '_ngu_land_eyebrow',
            '_ngu_land_head_title',
            '_ngu_land_sec_title',
            '_ngu_land_sec_btn_text',
            '_ngu_style_eyebrow',
            '_ngu_style_sec_title',
            '_ngu_north_title',
            '_ngu_north_btn_text',
            '_ngu_postbeam_title',
            '_ngu_postbeam_btn_text',
            '_ngu_used_eyebrow',
            '_ngu_used_title',
            '_ngu_used_body_title',
            '_ngu_used_btn_text',
            '_ngu_support_eyebrow',
            '_ngu_biz_title',
            '_ngu_b2c_title',
            '_ngu_b2b_title',
            '_ngu_company_title',
            '_ngu_company_btn_text',
            '_ngu_faq_eyebrow',
            '_ngu_faq_title',
            '_ngu_cta_title',
            '_ngu_cta_btn1_text',
            '_ngu_cta_btn2_text',
        ];
    }
}

if (!function_exists('ngu_textarea_fields')) {
    function ngu_textarea_fields()
    {
        return [
            '_ngu_hero_lead',
            '_ngu_intro_text',
            '_ngu_branch_text',
            '_ngu_land_branch_text',
            '_ngu_style_branch_text',
            '_ngu_land_head_text',
            '_ngu_land_sec_text',
            '_ngu_style_sec_text',
            '_ngu_north_text',
            '_ngu_postbeam_text',
            '_ngu_used_text',
            '_ngu_used_body_text',
            '_ngu_b2c_text',
            '_ngu_b2b_text',
            '_ngu_company_text',
            '_ngu_cta_text',
        ];
    }
}

if (!function_exists('ngu_url_fields')) {
    function ngu_url_fields()
    {
        return [
            '_ngu_hero_btn1_url',
            '_ngu_hero_btn2_url',
            '_ngu_land_branch_btn_url',
            '_ngu_style_branch_btn_url',
            '_ngu_land_sec_btn_url',
            '_ngu_north_btn_url',
            '_ngu_postbeam_btn_url',
            '_ngu_used_btn_url',
            '_ngu_company_btn_url',
            '_ngu_cta_btn1_url',
            '_ngu_cta_btn2_url',
        ];
    }
}

if (!function_exists('ngu_image_fields')) {
    function ngu_image_fields()
    {
        return [
            '_ngu_hero_bg_id',
            '_ngu_land_branch_img_id',
            '_ngu_style_branch_img_id',
            '_ngu_land_sec_img_id',
            '_ngu_north_img_id',
            '_ngu_postbeam_img_id',
            '_ngu_used_img_id',
            '_ngu_company_img_id',
        ];
    }
}

/* =========================================================
 * 共通取得
 * ========================================================= */
if (!function_exists('ngu_meta_with_default')) {
    function ngu_meta_with_default($post_id, $meta_key, $fallback = '')
    {
        $defaults = ngu_page_defaults();
        $default  = array_key_exists($meta_key, $defaults) ? $defaults[$meta_key] : $fallback;
        $value    = get_post_meta($post_id, $meta_key, true);

        return ($value !== '' && $value !== null) ? $value : $default;
    }
}

if (!function_exists('ngu_image_url')) {
    function ngu_image_url($attachment_id, $size = 'full')
    {
        $attachment_id = absint($attachment_id);

        if (!$attachment_id) {
            return '';
        }

        $url = wp_get_attachment_image_url($attachment_id, $size);
        return $url ? $url : '';
    }
}

if (!function_exists('ngu_image_alt')) {
    function ngu_image_alt($attachment_id, $fallback = '')
    {
        $attachment_id = absint($attachment_id);

        if (!$attachment_id) {
            return $fallback;
        }

        $alt = trim((string) get_post_meta($attachment_id, '_wp_attachment_image_alt', true));
        if ($alt !== '') {
            return $alt;
        }

        $title = get_the_title($attachment_id);
        if (!empty($title)) {
            return $title;
        }

        return $fallback;
    }
}

if (!function_exists('ngu_safe_url')) {
    function ngu_safe_url($url, $default = '')
    {
        $url = trim((string) $url);
        return $url !== '' ? $url : $default;
    }
}

if (!function_exists('ngu_btn_html')) {
    function ngu_btn_html($text, $url, $class = 'ngu-btn--primary')
    {
        $text = trim((string) $text);
        $url  = trim((string) $url);

        if ($text === '' || $url === '') {
            return '';
        }

        return sprintf(
            '<a class="ngu-btn %1$s" href="%2$s">%3$s</a>',
            esc_attr($class),
            esc_url($url),
            esc_html($text)
        );
    }
}

if (!function_exists('ngu_normalize_faq_items')) {
    function ngu_normalize_faq_items($items)
    {
        $normalized = [];

        if (!is_array($items)) {
            return $normalized;
        }

        foreach ($items as $item) {
            $q = isset($item['q']) ? trim((string) $item['q']) : '';
            $a = isset($item['a']) ? trim((string) $item['a']) : '';

            if ($q === '' || $a === '') {
                continue;
            }

            $normalized[] = [
                'q' => $q,
                'a' => $a,
            ];
        }

        return $normalized;
    }
}

/* =========================================================
 * フロント用データ一括取得
 * ========================================================= */
if (!function_exists('ngu_get_page_data')) {
    function ngu_get_page_data($post_id)
    {
        $post_id = absint($post_id);

        $hero_bg_id          = absint(get_post_meta($post_id, '_ngu_hero_bg_id', true));
        $land_branch_img_id  = absint(get_post_meta($post_id, '_ngu_land_branch_img_id', true));
        $style_branch_img_id = absint(get_post_meta($post_id, '_ngu_style_branch_img_id', true));
        $land_sec_img_id     = absint(get_post_meta($post_id, '_ngu_land_sec_img_id', true));
        $north_img_id        = absint(get_post_meta($post_id, '_ngu_north_img_id', true));
        $postbeam_img_id     = absint(get_post_meta($post_id, '_ngu_postbeam_img_id', true));
        $used_img_id         = absint(get_post_meta($post_id, '_ngu_used_img_id', true));
        $company_img_id      = absint(get_post_meta($post_id, '_ngu_company_img_id', true));

        return [
            'hero_eyebrow'   => ngu_meta_with_default($post_id, '_ngu_hero_eyebrow'),
            'hero_title'     => ngu_meta_with_default($post_id, '_ngu_hero_title'),
            'hero_lead'      => ngu_meta_with_default($post_id, '_ngu_hero_lead'),
            'hero_btn1_text' => ngu_meta_with_default($post_id, '_ngu_hero_btn1_text'),
            'hero_btn1_url'  => ngu_safe_url(ngu_meta_with_default($post_id, '_ngu_hero_btn1_url')),
            'hero_btn2_text' => ngu_meta_with_default($post_id, '_ngu_hero_btn2_text'),
            'hero_btn2_url'  => ngu_safe_url(ngu_meta_with_default($post_id, '_ngu_hero_btn2_url')),
            'hero_bg_id'     => $hero_bg_id,
            'hero_bg_url'    => ngu_image_url($hero_bg_id, 'full'),

            'intro_eyebrow' => ngu_meta_with_default($post_id, '_ngu_intro_eyebrow'),
            'intro_title'   => ngu_meta_with_default($post_id, '_ngu_intro_title'),
            'intro_text'    => ngu_meta_with_default($post_id, '_ngu_intro_text'),

            'branch_eyebrow' => ngu_meta_with_default($post_id, '_ngu_branch_eyebrow'),
            'branch_title'   => ngu_meta_with_default($post_id, '_ngu_branch_title'),
            'branch_text'    => ngu_meta_with_default($post_id, '_ngu_branch_text'),

            'land_branch_img_id'   => $land_branch_img_id,
            'land_branch_img_url'  => ngu_image_url($land_branch_img_id, 'large'),
            'land_branch_title'    => ngu_meta_with_default($post_id, '_ngu_land_branch_title'),
            'land_branch_text'     => ngu_meta_with_default($post_id, '_ngu_land_branch_text'),
            'land_branch_btn_text' => ngu_meta_with_default($post_id, '_ngu_land_branch_btn_text'),
            'land_branch_btn_url'  => ngu_safe_url(ngu_meta_with_default($post_id, '_ngu_land_branch_btn_url')),

            'style_branch_img_id'   => $style_branch_img_id,
            'style_branch_img_url'  => ngu_image_url($style_branch_img_id, 'large'),
            'style_branch_title'    => ngu_meta_with_default($post_id, '_ngu_style_branch_title'),
            'style_branch_text'     => ngu_meta_with_default($post_id, '_ngu_style_branch_text'),
            'style_branch_btn_text' => ngu_meta_with_default($post_id, '_ngu_style_branch_btn_text'),
            'style_branch_btn_url'  => ngu_safe_url(ngu_meta_with_default($post_id, '_ngu_style_branch_btn_url')),

            'land_eyebrow'      => ngu_meta_with_default($post_id, '_ngu_land_eyebrow'),
            'land_head_title'   => ngu_meta_with_default($post_id, '_ngu_land_head_title'),
            'land_head_text'    => ngu_meta_with_default($post_id, '_ngu_land_head_text'),
            'land_sec_title'    => ngu_meta_with_default($post_id, '_ngu_land_sec_title'),
            'land_sec_text'     => ngu_meta_with_default($post_id, '_ngu_land_sec_text'),
            'land_sec_btn_text' => ngu_meta_with_default($post_id, '_ngu_land_sec_btn_text'),
            'land_sec_btn_url'  => ngu_safe_url(ngu_meta_with_default($post_id, '_ngu_land_sec_btn_url')),
            'land_sec_img_id'   => $land_sec_img_id,
            'land_sec_img_url'  => ngu_image_url($land_sec_img_id, 'large'),

            'style_eyebrow'   => ngu_meta_with_default($post_id, '_ngu_style_eyebrow'),
            'style_sec_title' => ngu_meta_with_default($post_id, '_ngu_style_sec_title'),
            'style_sec_text'  => ngu_meta_with_default($post_id, '_ngu_style_sec_text'),

            'north_img_id'   => $north_img_id,
            'north_img_url'  => ngu_image_url($north_img_id, 'large'),
            'north_title'    => ngu_meta_with_default($post_id, '_ngu_north_title'),
            'north_text'     => ngu_meta_with_default($post_id, '_ngu_north_text'),
            'north_btn_text' => ngu_meta_with_default($post_id, '_ngu_north_btn_text'),
            'north_btn_url'  => ngu_safe_url(ngu_meta_with_default($post_id, '_ngu_north_btn_url')),

            'postbeam_img_id'   => $postbeam_img_id,
            'postbeam_img_url'  => ngu_image_url($postbeam_img_id, 'large'),
            'postbeam_title'    => ngu_meta_with_default($post_id, '_ngu_postbeam_title'),
            'postbeam_text'     => ngu_meta_with_default($post_id, '_ngu_postbeam_text'),
            'postbeam_btn_text' => ngu_meta_with_default($post_id, '_ngu_postbeam_btn_text'),
            'postbeam_btn_url'  => ngu_safe_url(ngu_meta_with_default($post_id, '_ngu_postbeam_btn_url')),

            'used_eyebrow'    => ngu_meta_with_default($post_id, '_ngu_used_eyebrow'),
            'used_title'      => ngu_meta_with_default($post_id, '_ngu_used_title'),
            'used_text'       => ngu_meta_with_default($post_id, '_ngu_used_text'),
            'used_body_title' => ngu_meta_with_default($post_id, '_ngu_used_body_title'),
            'used_body_text'  => ngu_meta_with_default($post_id, '_ngu_used_body_text'),
            'used_btn_text'   => ngu_meta_with_default($post_id, '_ngu_used_btn_text'),
            'used_btn_url'    => ngu_safe_url(ngu_meta_with_default($post_id, '_ngu_used_btn_url')),
            'used_img_id'     => $used_img_id,
            'used_img_url'    => ngu_image_url($used_img_id, 'large'),

            'support_eyebrow' => ngu_meta_with_default($post_id, '_ngu_support_eyebrow'),
            'biz_title'       => ngu_meta_with_default($post_id, '_ngu_biz_title'),
            'b2c_title'       => ngu_meta_with_default($post_id, '_ngu_b2c_title'),
            'b2c_text'        => ngu_meta_with_default($post_id, '_ngu_b2c_text'),
            'b2b_title'       => ngu_meta_with_default($post_id, '_ngu_b2b_title'),
            'b2b_text'        => ngu_meta_with_default($post_id, '_ngu_b2b_text'),

            'company_img_id'   => $company_img_id,
            'company_img_url'  => ngu_image_url($company_img_id, 'large'),
            'company_title'    => ngu_meta_with_default($post_id, '_ngu_company_title'),
            'company_text'     => ngu_meta_with_default($post_id, '_ngu_company_text'),
            'company_btn_text' => ngu_meta_with_default($post_id, '_ngu_company_btn_text'),
            'company_btn_url'  => ngu_safe_url(ngu_meta_with_default($post_id, '_ngu_company_btn_url')),

            'faq_eyebrow' => ngu_meta_with_default($post_id, '_ngu_faq_eyebrow'),
            'faq_title'   => ngu_meta_with_default($post_id, '_ngu_faq_title'),
            'faq_items'   => ngu_normalize_faq_items(get_post_meta($post_id, '_ngu_faq_items', true)),

            'cta_title'     => ngu_meta_with_default($post_id, '_ngu_cta_title'),
            'cta_text'      => ngu_meta_with_default($post_id, '_ngu_cta_text'),
            'cta_btn1_text' => ngu_meta_with_default($post_id, '_ngu_cta_btn1_text'),
            'cta_btn1_url'  => ngu_safe_url(ngu_meta_with_default($post_id, '_ngu_cta_btn1_url')),
            'cta_btn2_text' => ngu_meta_with_default($post_id, '_ngu_cta_btn2_text'),
            'cta_btn2_url'  => ngu_safe_url(ngu_meta_with_default($post_id, '_ngu_cta_btn2_url')),
        ];
    }
}

/* =========================================================
 * 管理画面メディア
 * ========================================================= */
if (!function_exists('ngu_admin_enqueue_scripts')) {
    function ngu_admin_enqueue_scripts($hook)
    {
        if ($hook !== 'post.php' && $hook !== 'post-new.php') {
            return;
        }

        $screen = get_current_screen();
        if (!$screen || $screen->post_type !== 'page') {
            return;
        }

        $post_id = 0;

        if (!empty($_GET['post'])) {
            $post_id = absint($_GET['post']);
        } elseif (!empty($_POST['post_ID'])) {
            $post_id = absint($_POST['post_ID']);
        }

        if ($hook === 'post.php' && $post_id && !ngu_is_target_template($post_id)) {
            return;
        }

        wp_enqueue_media();
        wp_enqueue_script('jquery');

        $inline_js = <<<JS
jQuery(function($){
    function openNguMediaFrame(target, preview) {
        var frame = wp.media({
            title: '画像を選択',
            button: { text: 'この画像を使用' },
            multiple: false
        });

        frame.on('select', function(){
            var attachment = frame.state().get('selection').first().toJSON();

            $('#' + target).val(attachment.id);

            if (preview) {
                $('#' + preview).html('<img src="' + attachment.url + '" style="max-width:240px;height:auto;display:block;">');
            }
        });

        frame.open();
    }

    $(document).on('click', '.ngu-upload-image', function(e){
        e.preventDefault();
        openNguMediaFrame($(this).data('target'), $(this).data('preview'));
    });

    $(document).on('click', '.ngu-remove-image', function(e){
        e.preventDefault();

        var target  = $(this).data('target');
        var preview = $(this).data('preview');

        $('#' + target).val('');
        $('#' + preview).html('');
    });
});
JS;

        wp_add_inline_script('jquery', $inline_js);
    }
}
add_action('admin_enqueue_scripts', 'ngu_admin_enqueue_scripts');

/* =========================================================
 * メタボックス
 * ========================================================= */
if (!function_exists('ngu_add_meta_boxes')) {
    function ngu_add_meta_boxes()
    {
        global $post;

        if (!$post || $post->post_type !== 'page' || !ngu_is_target_template($post->ID)) {
            return;
        }

        add_meta_box(
            'ngu_page_settings',
            '那須の住まい案内 設定',
            'ngu_render_page_settings_metabox',
            'page',
            'normal',
            'high'
        );

        add_meta_box(
            'ngu_faq_settings',
            'FAQ 設定',
            'ngu_render_faq_metabox',
            'page',
            'normal',
            'default'
        );
    }
}
add_action('add_meta_boxes', 'ngu_add_meta_boxes');

/* =========================================================
 * 入力UI
 * ========================================================= */
if (!function_exists('ngu_render_image_field')) {
    function ngu_render_image_field($post_id, $meta_key, $label, $field_id)
    {
        $attachment_id = absint(get_post_meta($post_id, $meta_key, true));
        $url = $attachment_id ? wp_get_attachment_image_url($attachment_id, 'medium') : '';
?>
        <div style="margin-bottom:18px;">
            <p><strong><?php echo esc_html($label); ?></strong></p>

            <input type="hidden" id="<?php echo esc_attr($field_id); ?>" name="<?php echo esc_attr($meta_key); ?>" value="<?php echo esc_attr($attachment_id); ?>">

            <div id="<?php echo esc_attr($field_id); ?>_preview" style="margin:8px 0;">
                <?php if ($url) : ?>
                    <img src="<?php echo esc_url($url); ?>" style="max-width:240px;height:auto;display:block;">
                <?php endif; ?>
            </div>

            <button type="button" class="button ngu-upload-image" data-target="<?php echo esc_attr($field_id); ?>" data-preview="<?php echo esc_attr($field_id); ?>_preview">画像を選択</button>
            <button type="button" class="button ngu-remove-image" data-target="<?php echo esc_attr($field_id); ?>" data-preview="<?php echo esc_attr($field_id); ?>_preview">画像を削除</button>
        </div>
    <?php
    }
}

if (!function_exists('ngu_text_input')) {
    function ngu_text_input($post_id, $meta_key, $label, $type = 'text')
    {
        $value = ngu_meta_with_default($post_id, $meta_key);
    ?>
        <p style="margin-bottom:16px;">
            <label>
                <strong><?php echo esc_html($label); ?></strong><br>
                <input type="<?php echo esc_attr($type); ?>" name="<?php echo esc_attr($meta_key); ?>" value="<?php echo esc_attr($value); ?>" style="width:100%;max-width:100%;">
            </label>
        </p>
    <?php
    }
}

if (!function_exists('ngu_textarea_input')) {
    function ngu_textarea_input($post_id, $meta_key, $label, $rows = 4)
    {
        $value = ngu_meta_with_default($post_id, $meta_key);
    ?>
        <p style="margin-bottom:16px;">
            <label>
                <strong><?php echo esc_html($label); ?></strong><br>
                <textarea name="<?php echo esc_attr($meta_key); ?>" rows="<?php echo esc_attr($rows); ?>" style="width:100%;max-width:100%;"><?php echo esc_textarea($value); ?></textarea>
            </label>
        </p>
<?php
    }
}

/* =========================================================
 * メタボックス描画
 * ========================================================= */
if (!function_exists('ngu_render_page_settings_metabox')) {
    function ngu_render_page_settings_metabox($post)
    {
        wp_nonce_field('ngu_save_meta', 'ngu_meta_nonce');

        echo '<h3>HERO</h3>';
        ngu_text_input($post->ID, '_ngu_hero_eyebrow', 'HEROアイブロー');
        ngu_render_image_field($post->ID, '_ngu_hero_bg_id', 'HERO背景画像', 'ngu_hero_bg_id');
        ngu_text_input($post->ID, '_ngu_hero_title', 'HEROタイトル');
        ngu_textarea_input($post->ID, '_ngu_hero_lead', 'HERO説明');
        ngu_text_input($post->ID, '_ngu_hero_btn1_text', 'HEROボタン1 文言');
        ngu_text_input($post->ID, '_ngu_hero_btn1_url', 'HEROボタン1 URL');
        ngu_text_input($post->ID, '_ngu_hero_btn2_text', 'HEROボタン2 文言');
        ngu_text_input($post->ID, '_ngu_hero_btn2_url', 'HEROボタン2 URL');

        echo '<hr><h3>導入</h3>';
        ngu_text_input($post->ID, '_ngu_intro_eyebrow', '導入アイブロー');
        ngu_text_input($post->ID, '_ngu_intro_title', '導入タイトル');
        ngu_textarea_input($post->ID, '_ngu_intro_text', '導入説明');

        echo '<hr><h3>2導線セクション</h3>';
        ngu_text_input($post->ID, '_ngu_branch_eyebrow', '2導線アイブロー');
        ngu_text_input($post->ID, '_ngu_branch_title', '2導線見出し');
        ngu_textarea_input($post->ID, '_ngu_branch_text', '2導線説明', 3);

        echo '<hr><h3>分岐カード：分譲地から考える</h3>';
        ngu_render_image_field($post->ID, '_ngu_land_branch_img_id', '分譲地カード画像', 'ngu_land_branch_img_id');
        ngu_text_input($post->ID, '_ngu_land_branch_title', '分譲地カードタイトル');
        ngu_textarea_input($post->ID, '_ngu_land_branch_text', '分譲地カード説明');
        ngu_text_input($post->ID, '_ngu_land_branch_btn_text', '分譲地カードボタン文言');
        ngu_text_input($post->ID, '_ngu_land_branch_btn_url', '分譲地カードボタンURL');

        echo '<hr><h3>分岐カード：住まいから考える</h3>';
        ngu_render_image_field($post->ID, '_ngu_style_branch_img_id', '住まいカード画像', 'ngu_style_branch_img_id');
        ngu_text_input($post->ID, '_ngu_style_branch_title', '住まいカードタイトル');
        ngu_textarea_input($post->ID, '_ngu_style_branch_text', '住まいカード説明');
        ngu_text_input($post->ID, '_ngu_style_branch_btn_text', '住まいカードボタン文言');
        ngu_text_input($post->ID, '_ngu_style_branch_btn_url', '住まいカードボタンURL');

        echo '<hr><h3>分譲地セクション</h3>';
        ngu_text_input($post->ID, '_ngu_land_eyebrow', '分譲地アイブロー');

        /* 上部見出し側 */
        ngu_text_input($post->ID, '_ngu_land_head_title', '分譲地セクション上部タイトル');
        ngu_textarea_input($post->ID, '_ngu_land_head_text', '分譲地セクション上部説明');

        /* 下部カード側 */
        ngu_render_image_field($post->ID, '_ngu_land_sec_img_id', '分譲地セクション画像', 'ngu_land_sec_img_id');
        ngu_text_input($post->ID, '_ngu_land_sec_title', '分譲地カードタイトル');
        ngu_textarea_input($post->ID, '_ngu_land_sec_text', '分譲地カード説明');
        ngu_text_input($post->ID, '_ngu_land_sec_btn_text', '分譲地セクションボタン文言');
        ngu_text_input($post->ID, '_ngu_land_sec_btn_url', '分譲地セクションボタンURL');

        echo '<hr><h3>住まいスタイルセクション</h3>';
        ngu_text_input($post->ID, '_ngu_style_eyebrow', '住まいスタイルアイブロー');
        ngu_text_input($post->ID, '_ngu_style_sec_title', 'スタイルセクションタイトル');
        ngu_textarea_input($post->ID, '_ngu_style_sec_text', 'スタイルセクション説明');

        echo '<h4>北米住宅</h4>';
        ngu_render_image_field($post->ID, '_ngu_north_img_id', '北米住宅画像', 'ngu_north_img_id');
        ngu_text_input($post->ID, '_ngu_north_title', '北米住宅タイトル');
        ngu_textarea_input($post->ID, '_ngu_north_text', '北米住宅説明');
        ngu_text_input($post->ID, '_ngu_north_btn_text', '北米住宅ボタン文言');
        ngu_text_input($post->ID, '_ngu_north_btn_url', '北米住宅ボタンURL');

        echo '<h4>自然素材の住まい</h4>';
        ngu_render_image_field($post->ID, '_ngu_postbeam_img_id', '自然素材の住まい画像', 'ngu_postbeam_img_id');
        ngu_text_input($post->ID, '_ngu_postbeam_title', '自然素材の住まいタイトル');
        ngu_textarea_input($post->ID, '_ngu_postbeam_text', '自然素材の住まい説明');
        ngu_text_input($post->ID, '_ngu_postbeam_btn_text', '自然素材の住まいボタン文言');
        ngu_text_input($post->ID, '_ngu_postbeam_btn_url', '自然素材の住まいボタンURL');

        echo '<hr><h3>中古住宅リノベ導線</h3>';
        ngu_text_input($post->ID, '_ngu_used_eyebrow', '中古リノベ アイブロー');
        ngu_text_input($post->ID, '_ngu_used_title', '中古リノベ セクションタイトル');
        ngu_textarea_input($post->ID, '_ngu_used_text', '中古リノベ セクション説明', 3);
        ngu_render_image_field($post->ID, '_ngu_used_img_id', '中古リノベ画像', 'ngu_used_img_id');
        ngu_text_input($post->ID, '_ngu_used_body_title', '中古リノベ カードタイトル');
        ngu_textarea_input($post->ID, '_ngu_used_body_text', '中古リノベ カード説明', 4);
        ngu_text_input($post->ID, '_ngu_used_btn_text', '中古リノベ ボタン文言');
        ngu_text_input($post->ID, '_ngu_used_btn_url', '中古リノベ ボタンURL');

        echo '<hr><h3>B2C / B2B</h3>';
        ngu_text_input($post->ID, '_ngu_support_eyebrow', 'Supportアイブロー');
        ngu_text_input($post->ID, '_ngu_biz_title', 'B2C/B2Bセクションタイトル');
        ngu_text_input($post->ID, '_ngu_b2c_title', 'B2Cタイトル');
        ngu_textarea_input($post->ID, '_ngu_b2c_text', 'B2C説明', 3);
        ngu_text_input($post->ID, '_ngu_b2b_title', 'B2Bタイトル');
        ngu_textarea_input($post->ID, '_ngu_b2b_text', 'B2B説明', 3);

        echo '<hr><h3>会社情報</h3>';
        ngu_render_image_field($post->ID, '_ngu_company_img_id', '会社情報画像', 'ngu_company_img_id');
        ngu_text_input($post->ID, '_ngu_company_title', '会社情報タイトル');
        ngu_textarea_input($post->ID, '_ngu_company_text', '会社情報説明');
        ngu_text_input($post->ID, '_ngu_company_btn_text', '会社情報ボタン文言');
        ngu_text_input($post->ID, '_ngu_company_btn_url', '会社情報ボタンURL');

        echo '<hr><h3>CTA</h3>';
        ngu_text_input($post->ID, '_ngu_cta_title', 'CTAタイトル');
        ngu_textarea_input($post->ID, '_ngu_cta_text', 'CTA説明');
        ngu_text_input($post->ID, '_ngu_cta_btn1_text', 'CTAボタン1 文言');
        ngu_text_input($post->ID, '_ngu_cta_btn1_url', 'CTAボタン1 URL');
        ngu_text_input($post->ID, '_ngu_cta_btn2_text', 'CTAボタン2 文言');
        ngu_text_input($post->ID, '_ngu_cta_btn2_url', 'CTAボタン2 URL');
    }
}

if (!function_exists('ngu_render_faq_metabox')) {
    function ngu_render_faq_metabox($post)
    {
        $faq_title   = ngu_meta_with_default($post->ID, '_ngu_faq_title');
        $faq_eyebrow = ngu_meta_with_default($post->ID, '_ngu_faq_eyebrow');
        $faq_items   = get_post_meta($post->ID, '_ngu_faq_items', true);
        $faq_items   = is_array($faq_items) ? $faq_items : ngu_page_default_faq_items();

        echo '<p><strong>FAQアイブロー</strong><br><input type="text" name="_ngu_faq_eyebrow" value="' . esc_attr($faq_eyebrow) . '" style="width:100%;"></p>';
        echo '<p><strong>FAQタイトル</strong><br><input type="text" name="_ngu_faq_title" value="' . esc_attr($faq_title) . '" style="width:100%;"></p>';
        echo '<p><strong>FAQ項目</strong></p>';

        for ($i = 0; $i < 6; $i++) {
            $q = isset($faq_items[$i]['q']) ? $faq_items[$i]['q'] : '';
            $a = isset($faq_items[$i]['a']) ? $faq_items[$i]['a'] : '';

            echo '<div style="border:1px solid #ddd;padding:12px;margin-bottom:14px;background:#fafafa;">';
            echo '<p><strong>質問 ' . ($i + 1) . '</strong><br><input type="text" name="ngu_faq_items[' . $i . '][q]" value="' . esc_attr($q) . '" style="width:100%;"></p>';
            echo '<p><strong>回答 ' . ($i + 1) . '</strong><br><textarea name="ngu_faq_items[' . $i . '][a]" rows="4" style="width:100%;">' . esc_textarea($a) . '</textarea></p>';
            echo '</div>';
        }
    }
}

/* =========================================================
 * 保存
 * ========================================================= */
if (!function_exists('ngu_save_page_meta')) {
    function ngu_save_page_meta($post_id)
    {
        if (!isset($_POST['ngu_meta_nonce']) || !wp_verify_nonce($_POST['ngu_meta_nonce'], 'ngu_save_meta')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        if (!ngu_is_target_template($post_id)) {
            return;
        }

        foreach (ngu_plain_text_fields() as $field) {
            if (isset($_POST[$field])) {
                update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
            }
        }

        foreach (ngu_textarea_fields() as $field) {
            if (isset($_POST[$field])) {
                update_post_meta($post_id, $field, sanitize_textarea_field($_POST[$field]));
            }
        }

        foreach (ngu_url_fields() as $field) {
            if (isset($_POST[$field])) {
                update_post_meta($post_id, $field, esc_url_raw($_POST[$field]));
            }
        }

        foreach (ngu_image_fields() as $field) {
            $value = isset($_POST[$field]) ? absint($_POST[$field]) : 0;

            if ($value > 0) {
                update_post_meta($post_id, $field, $value);
            } else {
                delete_post_meta($post_id, $field);
            }
        }

        $faq_items = [];

        if (isset($_POST['ngu_faq_items']) && is_array($_POST['ngu_faq_items'])) {
            foreach ($_POST['ngu_faq_items'] as $item) {
                $q = isset($item['q']) ? sanitize_text_field($item['q']) : '';
                $a = isset($item['a']) ? sanitize_textarea_field($item['a']) : '';

                if ($q !== '' || $a !== '') {
                    $faq_items[] = [
                        'q' => $q,
                        'a' => $a,
                    ];
                }
            }
        }

        update_post_meta($post_id, '_ngu_faq_items', $faq_items);
    }
}
add_action('save_post_page', 'ngu_save_page_meta');
