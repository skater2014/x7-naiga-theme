<?php

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('zairai_template_slug')) {
    function zairai_template_slug()
    {
        return 'page-zairai.php';
    }
}

if (!function_exists('zairai_normalize_post_id')) {
    function zairai_normalize_post_id($post = null)
    {
        if (is_numeric($post)) {
            return absint($post);
        }

        if (is_object($post) && isset($post->ID)) {
            return absint($post->ID);
        }

        $current = get_post();
        return $current ? absint($current->ID) : 0;
    }
}

if (!function_exists('zairai_is_target_page')) {
    function zairai_is_target_page($post = null)
    {
        $post_id = zairai_normalize_post_id($post);

        return $post_id
            && get_post_type($post_id) === 'page'
            && get_page_template_slug($post_id) === zairai_template_slug();
    }
}

if (!function_exists('zairai_page_defaults')) {
    function zairai_page_defaults()
    {
        return [
            '_zairai_hero_eyebrow'            => 'Natural Materials',
            '_zairai_hero_title'              => '自然素材の住まい',
            '_zairai_hero_text'               => '無垢材や自然素材のぬくもりを活かしながら、那須での暮らしに合う心地よい住まいをご提案します。',
            '_zairai_hero_primary_btn_text'   => 'ご相談はこちら',
            '_zairai_hero_primary_btn_url'    => home_url('/company'),
            '_zairai_hero_secondary_btn_text' => '詳しく見る',
            '_zairai_hero_secondary_btn_url'  => '#zairai-guide',

            '_zairai_intro_eyebrow' => 'Guide',
            '_zairai_intro_title'   => '自然素材の住まいとは',
            '_zairai_intro_text'    => '木や自然素材の質感を活かし、梁や柱の表情、明るさ、落ち着きのある空間づくりを大切にした住まいです。素材のぬくもりを感じながら、暮らし方や敷地条件に合わせて柔軟に考えやすい点も魅力です。',

            '_zairai_guide_plus_eyebrow'      => 'Support',
            '_zairai_guide_plus_title'        => '自然素材の住まいの安心ポイント',
            '_zairai_guide_plus_text'         => '素材の心地よさだけでなく、暮らしやすさやご相談のしやすさも大切にしながら、那須での住まいづくりをお手伝いします。',
            '_zairai_guide_plus_point1_title' => '暮らし方に合わせて相談しやすい',
            '_zairai_guide_plus_point1_text'  => 'ご家族構成やご予算、土地の条件に合わせて、住まいの方向性を整理しながらご相談いただけます。',
            '_zairai_guide_plus_point2_title' => '那須の気候に合わせた考え方',
            '_zairai_guide_plus_point2_text'  => '寒暖差や自然環境も踏まえながら、断熱や窓配置、日差しの取り込み方まで含めて計画しやすい住まいです。',
            '_zairai_guide_plus_point3_title' => '完成後の暮らしもイメージしやすい',
            '_zairai_guide_plus_point3_text'  => '動線や収納、居心地のよい空間づくりまで、住み始めてからの使いやすさも意識して検討できます。',
            '_zairai_guide_plus_btn_text'     => '住まいの相談はこちら',
            '_zairai_guide_plus_btn_url'      => home_url('/company'),

            '_zairai_feature1_eyebrow' => 'Design',
            '_zairai_feature1_title'   => '木の表情を活かした設計',
            '_zairai_feature1_text'    => '梁や柱の表情を活かした空間、吹き抜けのある明るい間取り、大きな窓を取り入れた開放感のある住まいなど、素材感を大切にしながら設計しやすい住まいです。',

            '_zairai_feature2_eyebrow' => 'Comfort',
            '_zairai_feature2_title'   => '心地よさは計画が大切',
            '_zairai_feature2_text'    => '断熱性・気密性・窓計画・素材の選び方は、住み心地に大きく関わります。自然素材の心地よさを活かしながら、地域性やご希望に合わせた計画が大切です。',

            '_zairai_feature3_eyebrow'      => 'Lifestyle',
            '_zairai_feature3_title'        => '那須での暮らしに合う自然素材の住まい',
            '_zairai_feature3_text'         => '四季の変化や自然環境を身近に感じられる那須では、木のぬくもりや落ち着きのある空間づくりが日々の暮らしにやさしく寄り添います。日差しの取り込み方や窓の配置、断熱計画も含めて、快適で心地よい住まいをご提案します。',
            '_zairai_feature3_btn_text'     => '那須での住まい相談はこちら',
            '_zairai_feature3_btn_url'      => home_url('/company'),
            '_zairai_feature3_sub_btn_text' => '自然素材の住まいの特長を見る',
            '_zairai_feature3_sub_btn_url'  => '#zairai-guide',

            '_zairai_flow_eyebrow' => 'Flow',
            '_zairai_flow_title'   => '家づくりの流れ',
            '_zairai_flow_text'    => "ご相談\nプランのご提案\n設計・仕様調整\n着工\n完成・お引き渡し",

            '_zairai_cta_eyebrow'  => 'Contact',
            '_zairai_cta_title'    => '自然素材の住まいづくりのご相談はこちら',
            '_zairai_cta_text'     => '自然素材の住まいや、那須での暮らしに合う住まいづくりについてお気軽にご相談ください。',
            '_zairai_cta_btn_text' => 'ご相談はこちら',
            '_zairai_cta_url'      => home_url('/company'),
        ];
    }
}

if (!function_exists('zairai_meta_sections')) {
    function zairai_meta_sections()
    {
        return [
            [
                'title'  => 'Hero',
                'fields' => [
                    ['type' => 'text',     'key' => '_zairai_hero_eyebrow',            'label' => 'Heroアイブロー'],
                    ['type' => 'image',    'key' => '_zairai_hero_image_id',           'label' => 'Hero画像'],
                    ['type' => 'text',     'key' => '_zairai_hero_title',              'label' => 'Heroタイトル'],
                    ['type' => 'textarea', 'key' => '_zairai_hero_text',               'label' => 'Hero説明文', 'rows' => 4],
                    ['type' => 'text',     'key' => '_zairai_hero_primary_btn_text',   'label' => 'Hero主ボタン文言'],
                    ['type' => 'text',     'key' => '_zairai_hero_primary_btn_url',    'label' => 'Hero主ボタンURL'],
                    ['type' => 'text',     'key' => '_zairai_hero_secondary_btn_text', 'label' => 'Hero副ボタン文言'],
                    ['type' => 'text',     'key' => '_zairai_hero_secondary_btn_url',  'label' => 'Hero副ボタンURL'],
                ],
            ],
            [
                'title'  => '導入',
                'fields' => [
                    ['type' => 'text',     'key' => '_zairai_intro_eyebrow',  'label' => '導入アイブロー'],
                    ['type' => 'image',    'key' => '_zairai_intro_image_id', 'label' => '導入画像'],
                    ['type' => 'text',     'key' => '_zairai_intro_title',    'label' => '導入見出し'],
                    ['type' => 'textarea', 'key' => '_zairai_intro_text',     'label' => '導入本文', 'rows' => 5],
                ],
            ],
            [
                'title'  => '右側サポートブロック',
                'fields' => [
                    ['type' => 'text',     'key' => '_zairai_guide_plus_eyebrow',      'label' => 'アイブロー'],
                    ['type' => 'image',    'key' => '_zairai_guide_plus_image_id',     'label' => '画像'],
                    ['type' => 'text',     'key' => '_zairai_guide_plus_title',        'label' => '見出し'],
                    ['type' => 'textarea', 'key' => '_zairai_guide_plus_text',         'label' => '本文', 'rows' => 4],
                    ['type' => 'text',     'key' => '_zairai_guide_plus_point1_title', 'label' => 'ポイント1見出し'],
                    ['type' => 'textarea', 'key' => '_zairai_guide_plus_point1_text',  'label' => 'ポイント1本文', 'rows' => 3],
                    ['type' => 'text',     'key' => '_zairai_guide_plus_point2_title', 'label' => 'ポイント2見出し'],
                    ['type' => 'textarea', 'key' => '_zairai_guide_plus_point2_text',  'label' => 'ポイント2本文', 'rows' => 3],
                    ['type' => 'text',     'key' => '_zairai_guide_plus_point3_title', 'label' => 'ポイント3見出し'],
                    ['type' => 'textarea', 'key' => '_zairai_guide_plus_point3_text',  'label' => 'ポイント3本文', 'rows' => 3],
                    ['type' => 'text',     'key' => '_zairai_guide_plus_btn_text',     'label' => 'ボタン文言'],
                    ['type' => 'text',     'key' => '_zairai_guide_plus_btn_url',      'label' => 'ボタンURL'],
                ],
            ],
            [
                'title'  => '特徴1',
                'fields' => [
                    ['type' => 'text',     'key' => '_zairai_feature1_eyebrow', 'label' => 'アイブロー'],
                    ['type' => 'image',    'key' => '_zairai_feature1_image_id', 'label' => '画像'],
                    ['type' => 'text',     'key' => '_zairai_feature1_title',   'label' => '見出し'],
                    ['type' => 'textarea', 'key' => '_zairai_feature1_text',    'label' => '本文', 'rows' => 4],
                ],
            ],
            [
                'title'  => '特徴2',
                'fields' => [
                    ['type' => 'text',     'key' => '_zairai_feature2_eyebrow', 'label' => 'アイブロー'],
                    ['type' => 'image',    'key' => '_zairai_feature2_image_id', 'label' => '画像'],
                    ['type' => 'text',     'key' => '_zairai_feature2_title',   'label' => '見出し'],
                    ['type' => 'textarea', 'key' => '_zairai_feature2_text',    'label' => '本文', 'rows' => 4],
                ],
            ],
            [
                'title'  => '特徴3',
                'fields' => [
                    ['type' => 'text',     'key' => '_zairai_feature3_eyebrow',      'label' => 'アイブロー'],
                    ['type' => 'image',    'key' => '_zairai_feature3_image_id',     'label' => '画像'],
                    ['type' => 'text',     'key' => '_zairai_feature3_title',        'label' => '見出し'],
                    ['type' => 'textarea', 'key' => '_zairai_feature3_text',         'label' => '本文', 'rows' => 4],
                    ['type' => 'text',     'key' => '_zairai_feature3_btn_text',     'label' => '主ボタン文言'],
                    ['type' => 'text',     'key' => '_zairai_feature3_btn_url',      'label' => '主ボタンURL'],
                    ['type' => 'text',     'key' => '_zairai_feature3_sub_btn_text', 'label' => '副ボタン文言'],
                    ['type' => 'text',     'key' => '_zairai_feature3_sub_btn_url',  'label' => '副ボタンURL'],
                ],
            ],
            [
                'title'  => 'Flow',
                'fields' => [
                    ['type' => 'text',     'key' => '_zairai_flow_eyebrow',  'label' => 'アイブロー'],
                    ['type' => 'image',    'key' => '_zairai_flow_image_id', 'label' => '予備画像'],
                    ['type' => 'text',     'key' => '_zairai_flow_title',    'label' => '見出し'],
                    ['type' => 'textarea', 'key' => '_zairai_flow_text',     'label' => '本文', 'rows' => 8],
                ],
            ],
            [
                'title'  => 'CTA',
                'fields' => [
                    ['type' => 'text',     'key' => '_zairai_cta_eyebrow',  'label' => 'アイブロー'],
                    ['type' => 'image',    'key' => '_zairai_cta_image_id', 'label' => '画像'],
                    ['type' => 'text',     'key' => '_zairai_cta_title',    'label' => '見出し'],
                    ['type' => 'textarea', 'key' => '_zairai_cta_text',     'label' => '本文', 'rows' => 4],
                    ['type' => 'text',     'key' => '_zairai_cta_btn_text', 'label' => 'ボタン文言'],
                    ['type' => 'text',     'key' => '_zairai_cta_url',      'label' => 'URL'],
                ],
            ],
        ];
    }
}

if (!function_exists('zairai_get_value')) {
    function zairai_get_value($post_id, $key, $fallback = '')
    {
        $defaults = zairai_page_defaults();
        $default  = array_key_exists($key, $defaults) ? $defaults[$key] : $fallback;
        $value    = get_post_meta($post_id, $key, true);

        return ($value !== '' && $value !== null) ? $value : $default;
    }
}

if (!function_exists('zairai_get_url')) {
    function zairai_get_url($post_id, $key, $fallback = '')
    {
        return trim((string) zairai_get_value($post_id, $key, $fallback));
    }
}

if (!function_exists('zairai_get_image_id')) {
    function zairai_get_image_id($post_id, $key)
    {
        return absint(get_post_meta($post_id, $key, true));
    }
}

if (!function_exists('zairai_get_image_url')) {
    function zairai_get_image_url($post_id, $key, $size = 'full')
    {
        $image_id = zairai_get_image_id($post_id, $key);
        return $image_id ? (string) wp_get_attachment_image_url($image_id, $size) : '';
    }
}

if (!function_exists('zairai_get_image_alt')) {
    function zairai_get_image_alt($post_id, $key, $fallback = '')
    {
        $image_id = zairai_get_image_id($post_id, $key);

        if (!$image_id) {
            return $fallback;
        }

        $alt = trim((string) get_post_meta($image_id, '_wp_attachment_image_alt', true));
        if ($alt !== '') {
            return $alt;
        }

        $title = get_the_title($image_id);
        return $title ? $title : $fallback;
    }
}

if (!function_exists('zairai_split_lines')) {
    function zairai_split_lines($text)
    {
        $rows = preg_split('/\r\n|\r|\n/', (string) $text);

        return array_values(array_filter(array_map(function ($row) {
            $row = trim((string) $row);
            $row = preg_replace('/^[↓→➝➜➡]+/u', '', $row);
            return trim($row);
        }, $rows)));
    }
}

if (!function_exists('zairai_text_html')) {
    function zairai_text_html($text)
    {
        return nl2br(esc_html((string) $text));
    }
}

if (!function_exists('zairai_btn')) {
    function zairai_btn($url, $text, $class = 'zairai-btn-primary')
    {
        if (!$url || !$text) {
            return '';
        }

        return sprintf(
            '<a class="zairai-btn %1$s" href="%2$s">%3$s</a>',
            esc_attr($class),
            esc_url($url),
            esc_html($text)
        );
    }
}

if (!function_exists('zairai_page_data')) {
    function zairai_page_data($post_id)
    {
        $hero = [
            'eyebrow'            => zairai_get_value($post_id, '_zairai_hero_eyebrow'),
            'title'              => zairai_get_value($post_id, '_zairai_hero_title'),
            'text'               => zairai_get_value($post_id, '_zairai_hero_text'),
            'image'              => zairai_get_image_url($post_id, '_zairai_hero_image_id', 'full'),
            'image_alt'          => zairai_get_image_alt($post_id, '_zairai_hero_image_id', zairai_get_value($post_id, '_zairai_hero_title')),
            'primary_btn_text'   => zairai_get_value($post_id, '_zairai_hero_primary_btn_text'),
            'primary_btn_url'    => zairai_get_url($post_id, '_zairai_hero_primary_btn_url'),
            'secondary_btn_text' => zairai_get_value($post_id, '_zairai_hero_secondary_btn_text'),
            'secondary_btn_url'  => zairai_get_url($post_id, '_zairai_hero_secondary_btn_url'),
        ];

        $intro = [
            'eyebrow'   => zairai_get_value($post_id, '_zairai_intro_eyebrow'),
            'title'     => zairai_get_value($post_id, '_zairai_intro_title'),
            'text'      => zairai_get_value($post_id, '_zairai_intro_text'),
            'image'     => zairai_get_image_url($post_id, '_zairai_intro_image_id', 'large'),
            'image_alt' => zairai_get_image_alt($post_id, '_zairai_intro_image_id', zairai_get_value($post_id, '_zairai_intro_title')),
        ];

        $guide_plus = [
            'eyebrow'      => zairai_get_value($post_id, '_zairai_guide_plus_eyebrow'),
            'title'        => zairai_get_value($post_id, '_zairai_guide_plus_title'),
            'text'         => zairai_get_value($post_id, '_zairai_guide_plus_text'),
            'image'        => zairai_get_image_url($post_id, '_zairai_guide_plus_image_id', 'large'),
            'image_alt'    => zairai_get_image_alt($post_id, '_zairai_guide_plus_image_id', zairai_get_value($post_id, '_zairai_guide_plus_title')),
            'point1_title' => zairai_get_value($post_id, '_zairai_guide_plus_point1_title'),
            'point1_text'  => zairai_get_value($post_id, '_zairai_guide_plus_point1_text'),
            'point2_title' => zairai_get_value($post_id, '_zairai_guide_plus_point2_title'),
            'point2_text'  => zairai_get_value($post_id, '_zairai_guide_plus_point2_text'),
            'point3_title' => zairai_get_value($post_id, '_zairai_guide_plus_point3_title'),
            'point3_text'  => zairai_get_value($post_id, '_zairai_guide_plus_point3_text'),
            'btn_text'     => zairai_get_value($post_id, '_zairai_guide_plus_btn_text'),
            'btn_url'      => zairai_get_url($post_id, '_zairai_guide_plus_btn_url'),
        ];

        $feature1 = [
            'eyebrow'   => zairai_get_value($post_id, '_zairai_feature1_eyebrow'),
            'title'     => zairai_get_value($post_id, '_zairai_feature1_title'),
            'text'      => zairai_get_value($post_id, '_zairai_feature1_text'),
            'image'     => zairai_get_image_url($post_id, '_zairai_feature1_image_id', 'large'),
            'image_alt' => zairai_get_image_alt($post_id, '_zairai_feature1_image_id', zairai_get_value($post_id, '_zairai_feature1_title')),
        ];

        $feature2 = [
            'eyebrow'   => zairai_get_value($post_id, '_zairai_feature2_eyebrow'),
            'title'     => zairai_get_value($post_id, '_zairai_feature2_title'),
            'text'      => zairai_get_value($post_id, '_zairai_feature2_text'),
            'image'     => zairai_get_image_url($post_id, '_zairai_feature2_image_id', 'large'),
            'image_alt' => zairai_get_image_alt($post_id, '_zairai_feature2_image_id', zairai_get_value($post_id, '_zairai_feature2_title')),
        ];

        $feature3 = [
            'eyebrow'      => zairai_get_value($post_id, '_zairai_feature3_eyebrow'),
            'title'        => zairai_get_value($post_id, '_zairai_feature3_title'),
            'text'         => zairai_get_value($post_id, '_zairai_feature3_text'),
            'image'        => zairai_get_image_url($post_id, '_zairai_feature3_image_id', 'large'),
            'image_alt'    => zairai_get_image_alt($post_id, '_zairai_feature3_image_id', zairai_get_value($post_id, '_zairai_feature3_title')),
            'btn_text'     => zairai_get_value($post_id, '_zairai_feature3_btn_text'),
            'btn_url'      => zairai_get_url($post_id, '_zairai_feature3_btn_url'),
            'sub_btn_text' => zairai_get_value($post_id, '_zairai_feature3_sub_btn_text'),
            'sub_btn_url'  => zairai_get_url($post_id, '_zairai_feature3_sub_btn_url'),
        ];

        $flow = [
            'eyebrow'   => zairai_get_value($post_id, '_zairai_flow_eyebrow'),
            'title'     => zairai_get_value($post_id, '_zairai_flow_title'),
            'text'      => zairai_get_value($post_id, '_zairai_flow_text'),
            'image'     => zairai_get_image_url($post_id, '_zairai_flow_image_id', 'large'),
            'image_alt' => zairai_get_image_alt($post_id, '_zairai_flow_image_id', zairai_get_value($post_id, '_zairai_flow_title')),
            'steps'     => zairai_split_lines(zairai_get_value($post_id, '_zairai_flow_text')),
        ];

        $flow['side_image'] = $guide_plus['image'] ?: $flow['image'];
        $flow['side_image_alt'] = $guide_plus['image'] ? $guide_plus['title'] : $flow['title'];

        $cta = [
            'eyebrow'   => zairai_get_value($post_id, '_zairai_cta_eyebrow'),
            'title'     => zairai_get_value($post_id, '_zairai_cta_title'),
            'text'      => zairai_get_value($post_id, '_zairai_cta_text'),
            'btn_text'  => zairai_get_value($post_id, '_zairai_cta_btn_text'),
            'url'       => zairai_get_url($post_id, '_zairai_cta_url'),
            'image'     => zairai_get_image_url($post_id, '_zairai_cta_image_id', 'large'),
            'image_alt' => zairai_get_image_alt($post_id, '_zairai_cta_image_id', zairai_get_value($post_id, '_zairai_cta_title')),
        ];

        return [
            'hero'       => $hero,
            'intro'      => $intro,
            'guide_plus' => $guide_plus,
            'feature1'   => $feature1,
            'feature2'   => $feature2,
            'feature3'   => $feature3,
            'flow'       => $flow,
            'cta'        => $cta,
        ];
    }
}

if (!function_exists('zairai_admin_enqueue_assets')) {
    function zairai_admin_enqueue_assets($hook)
    {
        if (!in_array($hook, ['post.php', 'post-new.php'], true)) {
            return;
        }

        $post_id = 0;

        if (isset($_GET['post'])) {
            $post_id = absint($_GET['post']);
        } elseif (isset($_POST['post_ID'])) {
            $post_id = absint($_POST['post_ID']);
        }

        if (!$post_id || !zairai_is_target_page($post_id)) {
            return;
        }

        wp_enqueue_media();
        wp_enqueue_script('jquery');

        wp_add_inline_style('common', '
            #zairai_page_metabox .zairai-meta-grid{display:grid;grid-template-columns:1fr 1fr;gap:24px}
            #zairai_page_metabox .zairai-meta-grid .full{grid-column:1/-1}
            #zairai_page_metabox .zairai-meta-box{padding:18px;border:1px solid #d0d7de;border-radius:10px;background:#fff}
            #zairai_page_metabox .zairai-field{margin:0 0 16px}
            #zairai_page_metabox .zairai-field label{display:block;margin:0 0 6px;font-weight:700}
            #zairai_page_metabox .zairai-field input[type="text"],
            #zairai_page_metabox .zairai-field textarea{width:100%}
            #zairai_page_metabox .zairai-image-preview{margin:0 0 8px}
            #zairai_page_metabox .zairai-image-preview img{display:block;max-width:240px;height:auto;border:1px solid #d0d7de;background:#fff;padding:4px}
            #zairai_page_metabox .zairai-actions{display:flex;gap:8px;flex-wrap:wrap}
            @media (max-width:960px){
                #zairai_page_metabox .zairai-meta-grid{grid-template-columns:1fr}
            }
        ');

        wp_add_inline_script('jquery', "
            jQuery(function($){
                $(document).on('click', '.zairai-select-image', function(e){
                    e.preventDefault();

                    const wrap = $(this).closest('.zairai-image-field');
                    const input = wrap.find('.zairai-image-id');
                    const preview = wrap.find('.zairai-image-preview');
                    const removeBtn = wrap.find('.zairai-remove-image');

                    const frame = wp.media({
                        title: '画像を選択',
                        button: { text: 'この画像を使う' },
                        multiple: false
                    });

                    frame.on('select', function(){
                        const attachment = frame.state().get('selection').first().toJSON();
                        input.val(attachment.id);
                        preview.html('<img src=\"' + attachment.url + '\" alt=\"\">');
                        removeBtn.show();
                    });

                    frame.open();
                });

                $(document).on('click', '.zairai-remove-image', function(e){
                    e.preventDefault();

                    const wrap = $(this).closest('.zairai-image-field');
                    wrap.find('.zairai-image-id').val('');
                    wrap.find('.zairai-image-preview').empty();
                    $(this).hide();
                });
            });
        ");
    }
}
add_action('admin_enqueue_scripts', 'zairai_admin_enqueue_assets');

if (!function_exists('zairai_add_metabox')) {
    function zairai_add_metabox()
    {
        global $post;

        if (!$post || !zairai_is_target_page($post->ID)) {
            return;
        }

        add_meta_box(
            'zairai_page_metabox',
            '自然素材の住まいページ設定',
            'zairai_render_metabox',
            'page',
            'normal',
            'high'
        );
    }
}
add_action('add_meta_boxes', 'zairai_add_metabox');

if (!function_exists('zairai_render_field')) {
    function zairai_render_field($post_id, $field)
    {
        $type  = $field['type'];
        $key   = $field['key'];
        $label = $field['label'];

        if ($type === 'image') {
            $image_id  = zairai_get_image_id($post_id, $key);
            $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'medium') : '';
?>
            <div class="zairai-field zairai-image-field">
                <label><?php echo esc_html($label); ?></label>
                <input type="hidden" name="<?php echo esc_attr($key); ?>" class="zairai-image-id" value="<?php echo esc_attr($image_id); ?>">
                <div class="zairai-image-preview">
                    <?php if ($image_url) : ?>
                        <img src="<?php echo esc_url($image_url); ?>" alt="">
                    <?php endif; ?>
                </div>
                <div class="zairai-actions">
                    <button type="button" class="button zairai-select-image">画像を選択</button>
                    <button type="button" class="button zairai-remove-image" <?php echo $image_id ? '' : 'style="display:none;"'; ?>>画像を削除</button>
                </div>
            </div>
        <?php
            return;
        }

        $value = zairai_get_value($post_id, $key);

        if ($type === 'textarea') {
            $rows = isset($field['rows']) ? (int) $field['rows'] : 4;
        ?>
            <div class="zairai-field">
                <label for="<?php echo esc_attr($key); ?>"><?php echo esc_html($label); ?></label>
                <textarea id="<?php echo esc_attr($key); ?>" name="<?php echo esc_attr($key); ?>" rows="<?php echo esc_attr($rows); ?>"><?php echo esc_textarea($value); ?></textarea>
            </div>
        <?php
            return;
        }
        ?>
        <div class="zairai-field">
            <label for="<?php echo esc_attr($key); ?>"><?php echo esc_html($label); ?></label>
            <input id="<?php echo esc_attr($key); ?>" type="text" name="<?php echo esc_attr($key); ?>" value="<?php echo esc_attr($value); ?>">
        </div>
    <?php
    }
}

if (!function_exists('zairai_render_metabox')) {
    function zairai_render_metabox($post)
    {
        wp_nonce_field('zairai_save_metabox', 'zairai_metabox_nonce');
        $sections = zairai_meta_sections();
    ?>
        <div class="zairai-meta-grid">
            <?php foreach ($sections as $section) : ?>
                <div class="full">
                    <div class="zairai-meta-box">
                        <h3><?php echo esc_html($section['title']); ?></h3>
                        <?php foreach ($section['fields'] as $field) : ?>
                            <?php zairai_render_field($post->ID, $field); ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
<?php
    }
}

if (!function_exists('zairai_save_metabox')) {
    function zairai_save_metabox($post_id)
    {
        if (!isset($_POST['zairai_metabox_nonce']) || !wp_verify_nonce($_POST['zairai_metabox_nonce'], 'zairai_save_metabox')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        if (get_post_type($post_id) !== 'page') {
            return;
        }

        $template = isset($_POST['_wp_page_template'])
            ? sanitize_text_field(wp_unslash($_POST['_wp_page_template']))
            : get_page_template_slug($post_id);

        if ($template !== zairai_template_slug()) {
            return;
        }

        foreach (zairai_meta_sections() as $section) {
            foreach ($section['fields'] as $field) {
                $key  = $field['key'];
                $type = $field['type'];
                $raw  = isset($_POST[$key]) ? wp_unslash($_POST[$key]) : '';

                switch ($type) {
                    case 'textarea':
                        $value = sanitize_textarea_field($raw);
                        break;

                    case 'image':
                        $value = absint($raw);
                        break;

                    case 'text':
                    default:
                        if (substr($key, -4) === '_url') {
                            $value = esc_url_raw($raw);
                        } else {
                            $value = sanitize_text_field($raw);
                        }
                        break;
                }

                update_post_meta($post_id, $key, $value);
            }
        }
    }
}
add_action('save_post_page', 'zairai_save_metabox');
