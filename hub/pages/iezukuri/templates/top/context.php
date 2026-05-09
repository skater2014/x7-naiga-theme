<?php
/**
 * hub/pages/iezukuri/templates/top/context.php
 * 役割: /iezukuri トップのメタ取得・URL解決・配列生成
 * 元データ: 分割実行時点の template-iezukuri.php
 */
if (!defined('ABSPATH')) {
    exit;
}

$post_id = get_queried_object_id();

$defaults = function_exists('naigai_customhome_defaults') ? naigai_customhome_defaults() : array();

$meta = static function ($key, $fallback = '') use ($post_id) {
    $value = get_post_meta($post_id, $key, true);
    return ($value !== '' && $value !== null) ? $value : $fallback;
};

$attachment_url = static function ($attachment_id, $size = 'full') {
    $attachment_id = absint($attachment_id);
    if (!$attachment_id) {
        return '';
    }

    $mime = get_post_mime_type($attachment_id);
    if (strpos((string) $mime, 'image/') === 0) {
        $url = wp_get_attachment_image_url($attachment_id, $size);
        return $url ? $url : '';
    }

    $url = wp_get_attachment_url($attachment_id);
    return $url ? $url : '';
};

$page_url = static function ($slugs, $fallback = '/') {
    if (function_exists('naigai_customhome_find_page_url')) {
        return naigai_customhome_find_page_url($slugs, $fallback);
    }

    foreach ((array) $slugs as $slug) {
        $page = get_page_by_path($slug);
        if ($page) {
            return get_permalink($page->ID);
        }
    }

    return home_url($fallback);
};

$svg_icon = static function ($name) {
    $icons = array(
        'design' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 18.5V20h1.5l9.9-9.9-1.5-1.5L4 18.5Zm12-11.9 1.5-1.5a1.06 1.06 0 0 1 1.5 0l.9.9a1.06 1.06 0 0 1 0 1.5l-1.5 1.5-2.4-2.4Z" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        'performance' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3 4 7v5c0 5 3.4 8.7 8 9.9 4.6-1.2 8-4.9 8-9.9V7l-8-4Z" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/><path d="m9.5 12 1.7 1.7 3.8-4" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        'material' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 20c6-1 12-7 13-13-6 1-12 7-13 13Z" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/><path d="M8 16c2-2 4.5-4.5 8-8" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>',
        'lifestyle' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M16.5 20v-2a4.5 4.5 0 0 0-9 0v2" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/><circle cx="12" cy="8" r="3" fill="none" stroke="currentColor" stroke-width="1.7"/><path d="M21 20v-1.5a3.5 3.5 0 0 0-2.5-3.35M3 20v-1.5a3.5 3.5 0 0 1 2.5-3.35" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>',
        'hearing' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 6h16v9H7l-3 3V6Z" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg>',
        'plan' => '<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="6" fill="none" stroke="currentColor" stroke-width="1.7"/><path d="m20 20-4.2-4.2" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>',
        'contract' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M8 3h7l4 4v14H8z" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="M15 3v4h4M10 12h6M10 16h6" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>',
        'detail' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 10.5 12 3l9 7.5" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/><path d="M5.5 9.5V20h13V9.5M9 20v-6h6v6" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg>',
        'build' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="m14 4 6 6M4 20l7-2 9-9-5-5-9 9-2 7Z" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg>',
        'support' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 21s7-3.7 7-10V5l-7-2-7 2v6c0 6.3 7 10 7 10Z" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="m9.5 12 1.7 1.7 3.8-4" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>',
    );

    return isset($icons[$name]) ? $icons[$name] : '';
};

$room_url    = $page_url(array('room-gallery', 'room-gallary'), '/room-gallery/');
$hokubei_url = $page_url(array('hokubei-jutaku'), '/hokubei-jutaku/');
$natural_url = $page_url(array('zairai-kouhou'), '/zairai-kouhou/');
$ideal_url   = $page_url(array('nasu-ideal-home'), '/nasu-ideal-home/');
$contact_url = $page_url(array('contact', 'customer-info-form'), '/contact/');

$brand_logo_id  = absint(get_post_meta($post_id, '_hub_ch_brand_logo_id', true));
$brand_logo_url = $brand_logo_id ? wp_get_attachment_image_url($brand_logo_id, 'large') : '';
$brand_text     = get_post_meta($post_id, '_hub_ch_brand_text', true);
$brand_subtext  = get_post_meta($post_id, '_hub_ch_brand_subtext', true);
$hero_company   = get_post_meta($post_id, '_hub_ch_hero_company', true);

$header_style   = get_post_meta($post_id, '_hub_ch_header_menu_style', true);
$page_style     = get_post_meta($post_id, '_hub_ch_page_menu_style', true);
$localnav_mode  = get_post_meta($post_id, '_hub_ch_localnav_mode', true);

$cta2_override_label    = get_post_meta($post_id, '_hub_ch_cta_secondary_override_label', true);
$cta2_override_page_id  = absint(get_post_meta($post_id, '_hub_ch_cta_secondary_override_page_id', true));
$cta2_override_url      = get_post_meta($post_id, '_hub_ch_cta_secondary_override_url', true);
$cta2_override_page_url = $cta2_override_page_id ? get_permalink($cta2_override_page_id) : '';

if ($brand_text === '') {
    $brand_text = '内外グループ';
}
if ($brand_subtext === '') {
    $brand_subtext = '';
}
if ($hero_company === '') {
    $hero_company = $brand_text;
}
if ($header_style === '') {
    $header_style = 'default';
}
if ($page_style === '') {
    $page_style = 'default';
}
if ($localnav_mode === '') {
    $localnav_mode = 'hero_to_cta';
}

$hero_kicker     = $meta('_hub_ch_hero_kicker', '');
$hero_title      = $meta('_hub_ch_hero_title', "理想を、かたちに。\n世界にひとつの、注文住宅。");
$hero_lead       = $meta('_hub_ch_hero_lead', '家族の想いに寄り添い、暮らしをデザインする。性能・デザイン・素材、そのすべてにこだわった、あなただけの住まいをつくります。');
$hero_btn1_label = $meta('_hub_ch_hero_btn1_label', '無料相談・資料請求');
$hero_btn1_url   = $meta('_hub_ch_hero_btn1_url', $contact_url);
$hero_btn2_label = $meta('_hub_ch_hero_btn2_label', 'お問い合わせ');
$hero_btn2_url   = $meta('_hub_ch_hero_btn2_url', $contact_url);

$hero_video_mp4_id  = absint(get_post_meta($post_id, '_hub_ch_hero_video_mp4_id', true));
$hero_video_webm_id = absint(get_post_meta($post_id, '_hub_ch_hero_video_webm_id', true));
$hero_poster_id     = absint(get_post_meta($post_id, '_hub_ch_hero_video_poster_id', true));

$hero_video_mp4  = $attachment_url($hero_video_mp4_id);
$hero_video_webm = $attachment_url($hero_video_webm_id);
$hero_poster     = $attachment_url($hero_poster_id);
$hero_fallback   = $hero_poster ? $hero_poster : get_the_post_thumbnail_url($post_id, 'full');

$feature_eyebrow = $meta('_hub_ch_feature_eyebrow', 'FEATURE');
$feature_title   = $meta('_hub_ch_feature_title', '私たちの家づくりの特徴');

$feature_defaults = array(
    1 => array('title' => '自由設計のデザイン力', 'text' => '一般建築士とつくる自由設計。デザイン性と機能性を両立した唯一無二の住まいを形にします。', 'icon' => 'design'),
    2 => array('title' => '高い住宅性能', 'text' => '断熱・気密・耐震・耐久の視点から、長く快適に暮らせる性能バランスを丁寧に整えます。', 'icon' => 'performance'),
    3 => array('title' => '自然素材へのこだわり', 'text' => '無垢材や塗り壁など、素材が持つ風合いと心地よさを、暮らしの質に結びつけます。', 'icon' => 'material'),
    4 => array('title' => '暮らしに寄り添う提案', 'text' => '家族構成、生活動線、将来設計まで見据えて、住まい方そのものを一緒に考えます。', 'icon' => 'lifestyle'),
);

$features = array();
for ($i = 1; $i <= 4; $i++) {
    $features[] = array(
        'title' => $meta("_hub_ch_feature_{$i}_title", $feature_defaults[$i]['title']),
        'text'  => $meta("_hub_ch_feature_{$i}_text", $feature_defaults[$i]['text']),
        'icon'  => $feature_defaults[$i]['icon'],
    );
}

$works_eyebrow = $meta('_hub_ch_works_eyebrow', 'Living Points');
$works_title   = $meta('_hub_ch_works_title', '暮らしのポイント');

$works = array();
$repeater_works = get_post_meta($post_id, '_hub_ch_work_items', true);
if (is_array($repeater_works) && !empty($repeater_works)) {
    foreach ($repeater_works as $item) {
        $attachment_id = isset($item['attachment_id']) ? absint($item['attachment_id']) : 0;
        $title = isset($item['title']) ? $item['title'] : '';
        $text  = isset($item['text']) ? $item['text'] : '';
        $image = $attachment_url($attachment_id, 'large');

        if ($attachment_id || $title !== '' || $text !== '') {
            $works[] = array(
                'title' => $title,
                'text'  => $text,
                'image' => $image,
            );
        }
    }
} else {
    for ($i = 1; $i <= 8; $i++) {
        $title = $meta("_hub_ch_work_{$i}_title", $defaults["work_{$i}_title"] ?? '');
        $text  = $meta("_hub_ch_work_{$i}_text", $defaults["work_{$i}_text"] ?? '');
        $image_id = absint(get_post_meta($post_id, "_hub_ch_work_{$i}_image_id", true));
        $image = $attachment_url($image_id, 'large');

        if ($title === '' && $text === '' && $image === '') {
            continue;
        }

        $works[] = array(
            'title' => $title,
            'text'  => $text,
            'image' => $image,
        );
    }
}

$flow_eyebrow = $meta('_hub_ch_flow_eyebrow', 'FLOW');
$flow_title   = $meta('_hub_ch_flow_title', '家づくりの流れ');

$flow_defaults = array(
    1 => array('num' => '01', 'title' => 'ご相談・ヒアリング', 'text' => '理想の暮らしやご要望を、まずはじっくり伺います。', 'icon' => 'hearing'),
    2 => array('num' => '02', 'title' => 'プラン提案・お見積り', 'text' => '敷地やご予算に合わせて、最適な住まい方をご提案します。', 'icon' => 'plan'),
    3 => array('num' => '03', 'title' => 'ご契約', 'text' => '内容にご納得いただいたうえで、丁寧に契約を進めます。', 'icon' => 'contract'),
    4 => array('num' => '04', 'title' => '詳細設計・仕様決定', 'text' => '間取りや内装、設備など、細部まで一緒に整えていきます。', 'icon' => 'detail'),
    5 => array('num' => '05', 'title' => '着工・施工', 'text' => '確かな品質管理のもと、丁寧に住まいをかたちにします。', 'icon' => 'build'),
    6 => array('num' => '06', 'title' => 'お引渡し・アフターサポート', 'text' => '完成後も安心して暮らしていただけるよう継続して支えます。', 'icon' => 'support'),
);

$flows = array();
for ($i = 1; $i <= 6; $i++) {
    $flows[] = array(
        'num'   => $meta("_hub_ch_flow_{$i}_num", $flow_defaults[$i]['num']),
        'title' => $meta("_hub_ch_flow_{$i}_title", $flow_defaults[$i]['title']),
        'text'  => $meta("_hub_ch_flow_{$i}_text", $flow_defaults[$i]['text']),
        'icon'  => $flow_defaults[$i]['icon'],
    );
}

$cta_eyebrow = $meta('_hub_ch_cta_eyebrow', 'CONTACT');
$cta_title   = $meta('_hub_ch_cta_title', '理想の住まいづくりを、一緒に始めませんか？');
$cta_text    = $meta('_hub_ch_cta_text', '土地探しから資金計画まで、家づくりのことなら何でもご相談ください。構想段階でも大丈夫です。');
$cta_btn1_label = $meta('_hub_ch_cta_btn1_label', '無料相談・資料請求');
$cta_btn1_url   = $meta('_hub_ch_cta_btn1_url', $contact_url);
$cta_btn2_label = $cta2_override_label !== '' ? $cta2_override_label : '理想の住まいを考える';
$cta_btn2_url   = $cta2_override_page_url !== '' ? $cta2_override_page_url : ($cta2_override_url !== '' ? $cta2_override_url : $ideal_url);
$cta_image_id   = absint(get_post_meta($post_id, '_hub_ch_cta_image_id', true));
$cta_image      = $attachment_url($cta_image_id, 'large');

$cta_media_items = array();
$cta_media_meta = get_post_meta($post_id, '_hub_ch_cta_media_items', true);
$cta_swiper_enabled = get_post_meta($post_id, '_hub_ch_cta_swiper_enabled', true);
$cta_swiper_nav = get_post_meta($post_id, '_hub_ch_cta_swiper_nav', true);
$cta_swiper_pagination = get_post_meta($post_id, '_hub_ch_cta_swiper_pagination', true);
$cta_video_controls = get_post_meta($post_id, '_hub_ch_cta_video_controls', true);

if ($cta_swiper_enabled === '') $cta_swiper_enabled = '1';
if ($cta_swiper_nav === '') $cta_swiper_nav = '1';
if ($cta_swiper_pagination === '') $cta_swiper_pagination = '1';
if ($cta_video_controls === '') $cta_video_controls = '0';

if (is_array($cta_media_meta) && !empty($cta_media_meta)) {
    foreach ($cta_media_meta as $item) {
        $type = isset($item['type']) && $item['type'] === 'video' ? 'video' : 'image';
        $attachment_id = isset($item['attachment_id']) ? absint($item['attachment_id']) : 0;
        if (!$attachment_id) {
            continue;
        }

        $url = $attachment_url($attachment_id, 'large');
        $mime = get_post_mime_type($attachment_id);

        if ($url) {
            $cta_media_items[] = array(
                'type' => $type,
                'url'  => $url,
                'mime' => $mime ? $mime : '',
            );
        }
    }
}

get_header('customhome');
?>
<main class="hub-customhome-page" data-localnav-mode="<?php echo esc_attr($localnav_mode); ?>">
