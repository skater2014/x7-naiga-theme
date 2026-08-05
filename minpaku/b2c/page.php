<?php

/**
 * Template Name: Minpaku B2C Unified
 *
 * 役割:
 * - 民泊 B2C 固定ページの共通テンプレート
 * - メタキーが空のブロックはフロントに出さない
 * - hero は画像1枚なら通常表示、2枚以上なら swiper 表示
 * - intro / feature / guide / compare / flow / faq / cta は独立セクション
 * - 各セクションは JSON メタを読んで無制限に出せる
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header('77');

$post_id = get_the_ID();

/**
 * =========================================================
 * 基本ヘルパー
 * =========================================================
 */
$meta_text = static function (int $post_id, string $key, string $default = ''): string {
    $value = get_post_meta($post_id, $key, true);

    if (is_string($value)) {
        $value = trim($value);
    }

    if ($value === '' || $value === null) {
        return $default;
    }

    return is_scalar($value) ? (string) $value : $default;
};

$meta_bool = static function (int $post_id, string $key, bool $default = true): bool {
    $value = get_post_meta($post_id, $key, true);

    if ($value === '' || $value === null) {
        return $default;
    }

    return in_array((string) $value, array('1', 'true', 'on', 'yes'), true);
};

$meta_image = static function (int $post_id, string $key, string $size = 'full', string $default_alt = ''): array {
    $image_id = absint(get_post_meta($post_id, $key, true));

    if (!$image_id) {
        return array(
            'id'  => 0,
            'url' => '',
            'alt' => $default_alt,
        );
    }

    $url = wp_get_attachment_image_url($image_id, $size);
    $alt = trim((string) get_post_meta($image_id, '_wp_attachment_image_alt', true));

    return array(
        'id'  => $image_id,
        'url' => $url ? (string) $url : '',
        'alt' => $alt !== '' ? $alt : $default_alt,
    );
};

$meta_gallery = static function (int $post_id, string $key, string $size = 'full', string $default_alt = ''): array {
    $raw = trim((string) get_post_meta($post_id, $key, true));

    if ($raw === '') {
        return array();
    }

    $ids = array_filter(array_map('absint', preg_split('/[\s,]+/', $raw)));
    $items = array();

    foreach ($ids as $image_id) {
        if (!$image_id) {
            continue;
        }

        $url = wp_get_attachment_image_url($image_id, $size);

        if (!$url) {
            continue;
        }

        $alt = trim((string) get_post_meta($image_id, '_wp_attachment_image_alt', true));

        $items[] = array(
            'id'  => $image_id,
            'url' => (string) $url,
            'alt' => $alt !== '' ? $alt : $default_alt,
        );
    }

    return $items;
};

$link_from_meta = static function (int $post_id, string $base_key): string {
    $page_id = absint(get_post_meta($post_id, "{$base_key}_page_id", true));

    if ($page_id > 0) {
        $permalink = get_permalink($page_id);

        if ($permalink) {
            return (string) $permalink;
        }
    }

    $raw_url = trim((string) get_post_meta($post_id, "{$base_key}_url", true));

    return $raw_url !== '' ? $raw_url : '';
};

$button_from_meta = static function (int $post_id, string $base_key, string $class) use ($meta_text, $link_from_meta): ?array {
    $text = $meta_text($post_id, "{$base_key}_text", '');
    $url  = $link_from_meta($post_id, $base_key);

    if ($text === '' || $url === '') {
        return null;
    }

    return array(
        'text'  => $text,
        'url'   => $url,
        'class' => $class,
    );
};

$text_to_html = static function (string $text): string {
    $text = trim($text);

    return $text === '' ? '' : nl2br(esc_html($text));
};

$render_text_block = static function (string $text) use ($text_to_html): string {
    $text = trim($text);

    if ($text === '') {
        return '';
    }

    return '<div class="mnpk-text-block"><p>' . $text_to_html($text) . '</p></div>';
};

$render_actions = static function (?array $btn1, ?array $btn2): string {
    if (!$btn1 && !$btn2) {
        return '';
    }

    ob_start();
    ?>
    <div class="mnpk-page-actions">
        <?php if ($btn1) : ?>
            <a class="mnpk-page-btn <?php echo esc_attr($btn1['class']); ?>" href="<?php echo esc_url($btn1['url']); ?>">
                <?php echo esc_html($btn1['text']); ?>
            </a>
        <?php endif; ?>

        <?php if ($btn2) : ?>
            <a class="mnpk-page-btn <?php echo esc_attr($btn2['class']); ?>" href="<?php echo esc_url($btn2['url']); ?>">
                <?php echo esc_html($btn2['text']); ?>
            </a>
        <?php endif; ?>
    </div>
    <?php
    return trim((string) ob_get_clean());
};

$meta_json = static function (int $post_id, string $key): array {
    $raw = get_post_meta($post_id, $key, true);

    if (!is_string($raw) || trim($raw) === '') {
        return array();
    }

    $decoded = json_decode($raw, true);

    return is_array($decoded) ? $decoded : array();
};

$normalize_repeater_items = static function (array $items): array {
    $normalized = array();

    foreach ($items as $item) {
        if (!is_array($item)) {
            continue;
        }

        $title = isset($item['title']) ? trim((string) $item['title']) : '';
        $text  = isset($item['text']) ? trim((string) $item['text']) : '';
        $label = isset($item['label']) ? trim((string) $item['label']) : '';
        $image_id = isset($item['image_id']) ? absint($item['image_id']) : 0;

        $image = array('id' => 0, 'url' => '', 'alt' => '');

        if ($image_id > 0) {
            $image = $GLOBALS['meta_image_from_id_helper']($image_id);
        }

        $btn1 = null;
        $btn2 = null;

        if (!empty($item['btn1']) && is_array($item['btn1'])) {
            $btn1_text = trim((string) ($item['btn1']['text'] ?? ''));
            $btn1_url  = trim((string) ($item['btn1']['url'] ?? ''));

            if ($btn1_text !== '' && $btn1_url !== '') {
                $btn1 = array(
                    'text'  => $btn1_text,
                    'url'   => $btn1_url,
                    'class' => 'is-primary',
                );
            }
        }

        if (!empty($item['btn2']) && is_array($item['btn2'])) {
            $btn2_text = trim((string) ($item['btn2']['text'] ?? ''));
            $btn2_url  = trim((string) ($item['btn2']['url'] ?? ''));

            if ($btn2_text !== '' && $btn2_url !== '') {
                $btn2 = array(
                    'text'  => $btn2_text,
                    'url'   => $btn2_url,
                    'class' => 'is-secondary',
                );
            }
        }

        if ($title === '' && $text === '' && $label === '' && empty($image['url']) && !$btn1 && !$btn2) {
            continue;
        }

        $normalized[] = array(
            'label' => $label,
            'title' => $title,
            'text'  => $text,
            'image' => $image,
            'btn1'  => $btn1,
            'btn2'  => $btn2,
        );
    }

    return array_values($normalized);
};

$GLOBALS['meta_image_from_id_helper'] = static function (int $image_id, string $size = 'large', string $default_alt = ''): array {
    if ($image_id <= 0) {
        return array(
            'id'  => 0,
            'url' => '',
            'alt' => $default_alt,
        );
    }

    $url = wp_get_attachment_image_url($image_id, $size);
    $alt = trim((string) get_post_meta($image_id, '_wp_attachment_image_alt', true));

    return array(
        'id'  => $image_id,
        'url' => $url ? (string) $url : '',
        'alt' => $alt !== '' ? $alt : $default_alt,
    );
};

/**
 * =========================================================
 * hero
 * =========================================================
 */
$hero_single_image = $meta_image($post_id, '_mpb_hero_image_id', 'full', get_the_title($post_id));
$hero_gallery      = $meta_gallery($post_id, '_mpb_hero_gallery_ids', 'full', get_the_title($post_id));
$hero_video_mp4_id   = absint(get_post_meta($post_id, '_mpb_hero_video_mp4_id', true));
$hero_video_mp4_url  = $hero_video_mp4_id > 0 ? (string) wp_get_attachment_url($hero_video_mp4_id) : '';
$hero_video_mime     = $hero_video_mp4_id > 0 ? (string) get_post_mime_type($hero_video_mp4_id) : '';

$hero_swiper_autoplay_meta = (string) get_post_meta($post_id, '_mpb_hero_swiper_autoplay', true);
$hero_swiper_pagination_meta = (string) get_post_meta($post_id, '_mpb_hero_swiper_pagination', true);
$hero_swiper_navigation_meta = (string) get_post_meta($post_id, '_mpb_hero_swiper_navigation', true);
$hero_swiper_loop_meta = (string) get_post_meta($post_id, '_mpb_hero_swiper_loop', true);

$hero_swiper_autoplay = ($hero_swiper_autoplay_meta === '')
    ? true
    : in_array($hero_swiper_autoplay_meta, array('1', 'true', 'on', 'yes'), true);

$hero_swiper_pagination = ($hero_swiper_pagination_meta === '')
    ? true
    : in_array($hero_swiper_pagination_meta, array('1', 'true', 'on', 'yes'), true);

$hero_swiper_navigation = ($hero_swiper_navigation_meta === '')
    ? false
    : in_array($hero_swiper_navigation_meta, array('1', 'true', 'on', 'yes'), true);

$hero_swiper_loop = ($hero_swiper_loop_meta === '')
    ? true
    : in_array($hero_swiper_loop_meta, array('1', 'true', 'on', 'yes'), true);

$hero_swiper_pagination_position = trim((string) get_post_meta($post_id, '_mpb_hero_swiper_pagination_position', true));
if ($hero_swiper_pagination_position === '') {
    $hero_swiper_pagination_position = 'center-bottom';
}

$hero_swiper_delay = (int) get_post_meta($post_id, '_mpb_hero_swiper_delay', true);
if ($hero_swiper_delay <= 0) {
    $hero_swiper_delay = 5000;
}


if (empty($hero_gallery) && !empty($hero_single_image['url'])) {
    $hero_gallery[] = $hero_single_image;
}

$hero = array(
    'eyebrow' => $meta_text($post_id, '_mpb_hero_eyebrow', ''),
    'title'   => $meta_text($post_id, '_mpb_hero_title', get_the_title($post_id)),
    'lead'    => $meta_text($post_id, '_mpb_hero_lead', ''),
    'images'  => $hero_gallery,
    'btn1'    => $button_from_meta($post_id, '_mpb_hero_btn1', 'is-primary'),
    'btn2'    => $button_from_meta($post_id, '_mpb_hero_btn2', 'is-secondary'),
);

$hero_has_video   = ($hero_video_mp4_id > 0 && $hero_video_mp4_url !== '');
$hero_video_poster = !empty($hero['images'][0]['url']) ? (string) $hero['images'][0]['url'] : (!empty($hero_single_image['url']) ? (string) $hero_single_image['url'] : '');
$hero_has_images  = !empty($hero['images']);
$hero_has_swiper  = !$hero_has_video && count($hero['images']) > 1;
$hero_first_image = $hero_has_images ? $hero['images'][0] : array('url' => '', 'alt' => '');
$hero_has_title   = trim((string) $hero['title']) !== '';
$hero_has_lead    = trim((string) $hero['lead']) !== '';
$hero_has_actions = !empty($hero['btn1']) || !empty($hero['btn2']);
$should_render_hero = $meta_bool($post_id, '_mpb_show_hero', true)
    && ($hero_has_video || $hero_has_images || $hero_has_title || $hero_has_lead || $hero_has_actions);

/**
 * =========================================================
 * 固定本文メタ
 * =========================================================
 */
$intro_image = $meta_image($post_id, '_mpb_intro_image_id', 'large', '');
$cta_single_image = $meta_image($post_id, '_mpb_cta_image_id', 'large', '');
$cta_gallery = $meta_gallery($post_id, '_mpb_cta_gallery_ids', 'large', '');
$cta_video_mp4_id = absint(get_post_meta($post_id, '_mpb_cta_video_mp4_id', true));
$cta_video_mp4_url = $cta_video_mp4_id ? (string) wp_get_attachment_url($cta_video_mp4_id) : '';
$cta_video_mime = $cta_video_mp4_id ? (string) get_post_mime_type($cta_video_mp4_id) : '';

$cta_show_media = $meta_bool($post_id, '_mpb_cta_show_media', true);
$cta_media_autoplay = $meta_bool($post_id, '_mpb_cta_media_autoplay', false);
$cta_video_controls = $meta_bool($post_id, '_mpb_cta_video_controls', true);
$cta_swiper_pagination = $meta_bool($post_id, '_mpb_cta_swiper_pagination', true);
$cta_swiper_navigation = $meta_bool($post_id, '_mpb_cta_swiper_navigation', true);
$cta_swiper_delay = (int) get_post_meta($post_id, '_mpb_cta_swiper_delay', true);
if ($cta_swiper_delay <= 0) {
    $cta_swiper_delay = 5000;
}

if (empty($cta_gallery) && !empty($cta_single_image['url'])) {
    $cta_gallery[] = $cta_single_image;
}

$intro = array(
    'title' => $meta_text($post_id, '_mpb_intro_title', ''),
    'text'  => $meta_text($post_id, '_mpb_intro_text', ''),
    'image' => $intro_image,
);

$cta = array(
    'title' => $meta_text($post_id, '_mpb_cta_title', ''),
    'text'  => $meta_text($post_id, '_mpb_cta_text', ''),
    'image' => $cta_single_image,
    'images' => $cta_gallery,
    'video' => array(
        'id' => $cta_video_mp4_id,
        'url' => $cta_video_mp4_url,
        'mime' => $cta_video_mime,
    ),
    'btn1'  => $button_from_meta($post_id, '_mpb_cta_btn1', 'is-primary'),
    'btn2'  => $button_from_meta($post_id, '_mpb_cta_btn2', 'is-secondary'),
);

$intro_has_image = !empty($intro['image']['url']);
$intro_has_title = trim((string) $intro['title']) !== '';
$intro_has_text  = trim((string) $intro['text']) !== '';
$intro_has_body  = $intro_has_title || $intro_has_text;
$should_render_intro = $meta_bool($post_id, '_mpb_show_intro', true) && ($intro_has_image || $intro_has_body);

$cta_has_title   = trim((string) $cta['title']) !== '';
$cta_has_text    = trim((string) $cta['text']) !== '';
$cta_has_actions = !empty($cta['btn1']) || !empty($cta['btn2']);
$cta_has_body    = $cta_has_title || $cta_has_text || $cta_has_actions;

$cta_has_video = $cta_show_media && !empty($cta['video']['url']);
$cta_has_swiper = $cta_show_media && !$cta_has_video && count($cta['images']) > 1;
$cta_first_image = !empty($cta['images']) ? $cta['images'][0] : array('url' => '', 'alt' => '');
$cta_has_image = $cta_show_media && !$cta_has_video && !$cta_has_swiper && !empty($cta_first_image['url']);
$cta_has_visual = $cta_has_video || $cta_has_swiper || $cta_has_image;
$cta_mode = $meta_text($post_id, '_mpb_cta_mode', 'cta');

/*
 * CTAセクションは1つだけ。
 * - cta  : 通常CTAだけ表示
 * - form : コンタクトフォームだけ表示
 *
 * both は廃止。保存済み both / 空 / 不正値 は cta として扱う。
 */
if ($cta_mode === '' || $cta_mode === 'both' || !in_array($cta_mode, array('cta', 'form'), true)) {
    $cta_mode = 'cta';
}

$contact_form_template = locate_template('template-parts/contact/customer-info-form-inner.php', false, false);
$should_render_contact_form = ($cta_mode === 'form') && $contact_form_template !== '';
$should_render_cta_content  = ($cta_mode === 'cta') && ($cta_has_visual || $cta_has_body);

/*
 * form選択だがフォームテンプレートが無い場合:
 * - CTA内容があればCTAへ戻す
 * - CTAも空ならセクションを出さない
 */
if ($cta_mode === 'form' && !$should_render_contact_form && ($cta_has_visual || $cta_has_body)) {
    $cta_mode = 'cta';
    $should_render_cta_content = true;
}

$should_render_cta = $should_render_contact_form || $should_render_cta_content;

$cta_media_class = '';
if ($cta_has_video) {
    $cta_media_class = 'has-cta-video';
} elseif ($cta_has_swiper) {
    $cta_media_class = 'has-cta-swiper';
} elseif ($cta_has_image) {
    $cta_media_class = 'has-cta-image';
}


$render_contact_form_template = static function () use ($contact_form_template): string {
    if ($contact_form_template === '') {
        return '';
    }

    ob_start();
    include $contact_form_template;
    return trim((string) ob_get_clean());
};

/**
 * =========================================================
 * 無制限リピーター JSON
 * =========================================================
 */
$feature_items = $normalize_repeater_items($meta_json($post_id, '_mpb_feature_items_json'));
$guide_items   = $normalize_repeater_items($meta_json($post_id, '_mpb_guide_items_json'));
$flow_items    = $normalize_repeater_items($meta_json($post_id, '_mpb_flow_items_json'));

$faq_items_raw = $meta_json($post_id, '_mpb_faq_items_json');
$faq_items = array();

foreach ($faq_items_raw as $item) {
    if (!is_array($item)) {
        continue;
    }

    $q = trim((string) ($item['q'] ?? ''));
    $a = trim((string) ($item['a'] ?? ''));

    if ($q === '' && $a === '') {
        continue;
    }

    $faq_items[] = array('q' => $q, 'a' => $a);
}

$faq_title = $meta_text($post_id, '_mpb_faq_title', '');
$faq_text  = $meta_text($post_id, '_mpb_faq_text', '');
$should_render_faq = ($faq_title !== '' || $faq_text !== '' || !empty($faq_items));

/**
 * =========================================================
 * 無制限比較表 JSON
 * 形式:
 * {
 *   "columns":[{"title":"賃貸","image_id":123},{"title":"一時賃貸","image_id":456}],
 *   "rows":[
 *     {"label":"契約期間","cells":["2年契約が多い","短期で使いやすい"]},
 *     {"label":"初期費用","cells":["敷金礼金あり","抑えやすい"]}
 *   ]
 * }
 * =========================================================
 */
$compare_json = $meta_json($post_id, '_mpb_compare_table_json');
$compare_columns = array();
$compare_rows    = array();

if (!empty($compare_json['columns']) && is_array($compare_json['columns'])) {
    foreach ($compare_json['columns'] as $col) {
        if (!is_array($col)) {
            continue;
        }

        $title    = trim((string) ($col['title'] ?? ''));
        $image_id = absint($col['image_id'] ?? 0);
        $image    = $GLOBALS['meta_image_from_id_helper']($image_id, 'large', $title);

        if ($title === '' && empty($image['url'])) {
            continue;
        }

        $compare_columns[] = array(
            'title' => $title,
            'image' => $image,
        );
    }
}

if (!empty($compare_json['rows']) && is_array($compare_json['rows'])) {
    foreach ($compare_json['rows'] as $row) {
        if (!is_array($row)) {
            continue;
        }

        $label = trim((string) ($row['label'] ?? ''));
        $cells = array();

        if (!empty($row['cells']) && is_array($row['cells'])) {
            foreach ($row['cells'] as $cell) {
                $cells[] = trim((string) $cell);
            }
        }

        $has_cell_text = false;
        foreach ($cells as $cell_text) {
            if ($cell_text !== '') {
                $has_cell_text = true;
                break;
            }
        }

        if ($label === '' && !$has_cell_text) {
            continue;
        }

        $compare_rows[] = array(
            'label' => $label,
            'cells' => $cells,
        );
    }
}

$should_render_compare = !empty($compare_columns) && !empty($compare_rows);

$content_html = apply_filters('the_content', get_post_field('post_content', $post_id));


/**
 * =========================================================
 * B2C SECTION BUILDER FRONT STAGE 2
 * ---------------------------------------------------------
 * 役割:
 * - 管理画面で保存した _mpb_sections_json を読む
 * - 各セクションに CSS order を付けて表示順を変える
 * - enabled=0 のセクションは非表示にする
 *
 * 現段階の方針:
 * - 既存HTML構造を大きく壊さない
 * - Hero / Intro / Feature / Guide / Compare / Flow / FAQ / Content / CTA
 *   の順番をまず見た目上で入れ替えられるようにする
 *
 * 次段階:
 * - 各セクションHTMLを render 関数化し、HTML出力順自体も
 *   _mpb_sections_json に合わせる
 * =========================================================
 */
$mpb_section_defaults = array(
    'hero'    => array('order' => 10, 'enabled' => 1),
    'intro'   => array('order' => 20, 'enabled' => 1),
    'feature' => array('order' => 30, 'enabled' => 1),
    'guide'   => array('order' => 40, 'enabled' => 1),
    'compare' => array('order' => 50, 'enabled' => 1),
    'flow'    => array('order' => 60, 'enabled' => 1),
    'faq'     => array('order' => 70, 'enabled' => 1),
    'content' => array('order' => 80, 'enabled' => 1),
    'cta'     => array('order' => 90, 'enabled' => 1),
);

$mpb_section_config = $mpb_section_defaults;
$mpb_sections_raw = (string) get_post_meta($post_id, '_mpb_sections_json', true);
$mpb_sections_saved = json_decode($mpb_sections_raw, true);

if (is_array($mpb_sections_saved)) {
    foreach ($mpb_sections_saved as $row) {
        if (empty($row['type'])) {
            continue;
        }

        $type = (string) $row['type'];

        if (!isset($mpb_section_config[$type])) {
            continue;
        }

        if (isset($row['order'])) {
            $mpb_section_config[$type]['order'] = (int) $row['order'];
        }

        if (isset($row['enabled'])) {
            $mpb_section_config[$type]['enabled'] = (int) !!$row['enabled'];
        }
    }
}

$mpb_section_attrs = static function (string $type) use ($mpb_section_config): string {
    $config = $mpb_section_config[$type] ?? array('order' => 999, 'enabled' => 1);

    $order = (int) ($config['order'] ?? 999);
    $enabled = !empty($config['enabled']);

    $style = '--mnpk-section-order:' . $order . '; order:' . $order . ';';

    if (!$enabled) {
        $style .= ' display:none !important;';
    }

    return 'data-mnpk-section="' . esc_attr($type) . '" style="' . esc_attr($style) . '"';
};

$page_classes = array('mnpk-page', 'mnpk-page--unified');
?>
<div class="<?php echo esc_attr(implode(' ', $page_classes)); ?>">
    <main class="mnpk-page-shell">

        <?php
        /*
         * ========================================================
         * NAIGAI_MINPAKU_B2C_BACKLINK_BEGINNER_GUIDE
         * 民泊B2C共通「前のページに戻る」
         * ========================================================
         *
         * 【対象】
         * minpaku-guide
         * minpaku-campaign
         * minpaku-faq
         * minpaku-rules
         * minpaku-flow
         * minpaku-difference
         * minpaku-family
         * minpaku-group
         * minpaku-workation
         * その他 page-minpaku-b2c.php を使用するB2Cページ
         *
         * 【表示】
         * サイト内の別ページから移動してきた場合だけ表示する。
         *
         * そのため、民泊上部ナビゲーションから
         * FAQ → ご利用案内
         * 民泊ガイド → FAQ
         * 宿泊一覧 → お得情報
         * などへ移動した場合も表示対象になる。
         *
         * 【非表示】
         * URL直接入力
         * ブックマーク
         * Google等の外部サイト
         * Refererが取得できない場合
         *
         * 【デザイン】
         * 新しいCSSは作らない。
         * 民泊で既存使用している
         * .mnpk-back-wrap / .mnpk-back-link
         * をそのまま使用する。
         * ========================================================
         */
        get_template_part(
            'template-parts/common/minpaku-internal-back-link'
        );
        ?>

        <?php
/* B2C_REAL_HTML_ORDER_START */
$mpb_layout_blocks = array();
?>
<?php ob_start(); ?>
<?php if ($should_render_hero) : ?>
            <section class="mnpk-page-section" <?php echo $mpb_section_attrs('hero'); ?>>
                <div
                    class="mnpk-page-hero mnpk-panel <?php echo $hero_has_video ? 'has-video' : ($hero_has_swiper ? 'has-swiper' : ($hero_has_images ? 'has-image' : 'is-no-image')); ?>"
                    <?php echo (!$hero_has_video && !$hero_has_swiper && !empty($hero_first_image['url'])) ? ' style="background-image:url(' . esc_url($hero_first_image['url']) . ');"' : ''; ?>
                >
                    <?php if ($hero_has_video) : ?>
                        <div class="mnpk-page-hero__media is-video" aria-hidden="true">
                            <video class="mnpk-page-hero__video" autoplay muted loop playsinline preload="metadata"<?php if ($hero_video_poster !== '') : ?> poster="<?php echo esc_url($hero_video_poster); ?>"<?php endif; ?>>
                                <source src="<?php echo esc_url($hero_video_mp4_url); ?>" type="<?php echo esc_attr($hero_video_mime !== '' ? $hero_video_mime : 'video/mp4'); ?>">
                            </video>
                        </div>
                    <?php elseif ($hero_has_swiper) : ?>
                        <div class="mnpk-page-hero__media <?php echo $hero_swiper_navigation ? 'has-navigation' : 'has-no-navigation'; ?> <?php echo $hero_swiper_pagination ? 'has-pagination' : 'has-no-pagination'; ?> pagination-<?php echo esc_attr($hero_swiper_pagination_position); ?>" aria-hidden="true">
                            <div
                                class="swiper mnpk-hero-swiper"
                                data-loop="<?php echo $hero_swiper_loop ? '1' : '0'; ?>"
                                data-autoplay="<?php echo $hero_swiper_autoplay ? '1' : '0'; ?>"
                                data-pagination="<?php echo $hero_swiper_pagination ? '1' : '0'; ?>"
                                data-navigation="<?php echo $hero_swiper_navigation ? '1' : '0'; ?>"
                                data-delay="<?php echo (int) $hero_swiper_delay; ?>"
                            >
                                <div class="swiper-wrapper">
                                    <?php foreach ($hero['images'] as $hero_slide) : ?>
                                        <div class="swiper-slide">
                                            <div class="mnpk-page-hero__slide" style="background-image:url('<?php echo esc_url($hero_slide['url']); ?>');"></div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <?php if ($hero_swiper_pagination) : ?>
                                    <div class="swiper-pagination mnpk-hero-swiper__pagination"></div>
                                <?php endif; ?>
                                <?php if ($hero_swiper_navigation) : ?>
                                    <button type="button" class="mnpk-hero-swiper__nav mnpk-hero-swiper__nav--prev" aria-label="前へ"></button>
                                    <button type="button" class="mnpk-hero-swiper__nav mnpk-hero-swiper__nav--next" aria-label="次へ"></button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="mnpk-page-hero__overlay" aria-hidden="true"></div>

                    <div class="mnpk-page-hero__inner">
                        <div class="mnpk-page-hero__content">
                            <?php if ($hero['eyebrow'] !== '') : ?>
                                <p class="mnpk-page-eyebrow"><?php echo esc_html($hero['eyebrow']); ?></p>
                            <?php endif; ?>

                            <?php if ($hero_has_title) : ?>
                                <h1 class="mnpk-page-hero__title"><?php echo esc_html($hero['title']); ?></h1>
                            <?php endif; ?>

                            <?php if ($hero_has_lead) : ?>
                                <div class="mnpk-page-hero__lead">
                                    <?php echo $text_to_html($hero['lead']); ?>
                                </div>
                            <?php endif; ?>

                            <?php echo $render_actions($hero['btn1'], $hero['btn2']); ?>
                        </div>
                    </div>
                </div>
            </section>
        <?php endif; ?>
<?php $mpb_layout_blocks['hero'] = ob_get_clean(); ?>

        <?php ob_start(); ?>
<?php if ($should_render_intro) : ?>
            <section class="mnpk-page-section" <?php echo $mpb_section_attrs('intro'); ?>>
                <div class="mnpk-panel">
                    <div class="mnpk-panel__inner">
                        <?php if ($intro_has_image && $intro_has_body) : ?>
                            <div class="mnpk-split">
                                <figure class="mnpk-split__media">
                                    <img src="<?php echo esc_url($intro['image']['url']); ?>" alt="<?php echo esc_attr($intro['image']['alt']); ?>">
                                </figure>
                                <div class="mnpk-split__body">
                                    <?php if ($intro_has_title) : ?>
                                        <h2><?php echo esc_html($intro['title']); ?></h2>
                                    <?php endif; ?>
                                    <?php if ($intro_has_text) : ?>
                                        <?php echo $render_text_block($intro['text']); ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php elseif ($intro_has_body) : ?>
                            <div class="mnpk-content-block">
                                <?php if ($intro_has_title) : ?>
                                    <h2><?php echo esc_html($intro['title']); ?></h2>
                                <?php endif; ?>
                                <?php if ($intro_has_text) : ?>
                                    <?php echo $render_text_block($intro['text']); ?>
                                <?php endif; ?>
                            </div>
                        <?php elseif ($intro_has_image) : ?>
                            <figure class="mnpk-solo-media">
                                <img src="<?php echo esc_url($intro['image']['url']); ?>" alt="<?php echo esc_attr($intro['image']['alt']); ?>">
                            </figure>
                        <?php endif; ?>
                    </div>
                </div>
            </section>
        <?php endif; ?>
<?php $mpb_layout_blocks['intro'] = ob_get_clean(); ?>

        <?php ob_start(); ?>
<?php if (!empty($feature_items)) : ?>
            <section class="mnpk-page-section" <?php echo $mpb_section_attrs('feature'); ?>>
                <div class="mnpk-card-grid">
                    <?php foreach ($feature_items as $item) : ?>
                        <div class="mnpk-panel">
                            <article class="mnpk-card mnpk-panel__inner <?php echo !empty($item['image']['url']) ? 'has-image' : 'is-text-only'; ?>">
                                <?php if (!empty($item['image']['url'])) : ?>
                                    <figure class="mnpk-card__media">
                                        <img src="<?php echo esc_url($item['image']['url']); ?>" alt="<?php echo esc_attr($item['image']['alt']); ?>">
                                    </figure>
                                <?php endif; ?>

                                <?php if ($item['label'] !== '' || $item['title'] !== '' || $item['text'] !== '' || $item['btn1'] || $item['btn2']) : ?>
                                    <div class="mnpk-card__content">
                                        <?php if ($item['label'] !== '') : ?>
                                            <p class="mnpk-section-eyebrow"><?php echo esc_html($item['label']); ?></p>
                                        <?php endif; ?>

                                        <?php if ($item['title'] !== '') : ?>
                                            <h2><?php echo esc_html($item['title']); ?></h2>
                                        <?php endif; ?>

                                        <?php if ($item['text'] !== '') : ?>
                                            <p><?php echo $text_to_html($item['text']); ?></p>
                                        <?php endif; ?>

                                        <?php if ($item['btn1'] || $item['btn2']) : ?>
                                            <div class="mnpk-card__actions">
                                                <?php if ($item['btn1']) : ?>
                                                    <a class="mnpk-page-btn <?php echo esc_attr($item['btn1']['class']); ?>" href="<?php echo esc_url($item['btn1']['url']); ?>">
                                                        <?php echo esc_html($item['btn1']['text']); ?>
                                                    </a>
                                                <?php endif; ?>

                                                <?php if ($item['btn2']) : ?>
                                                    <a class="mnpk-page-btn <?php echo esc_attr($item['btn2']['class']); ?>" href="<?php echo esc_url($item['btn2']['url']); ?>">
                                                        <?php echo esc_html($item['btn2']['text']); ?>
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </article>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
<?php $mpb_layout_blocks['feature'] = ob_get_clean(); ?>

        <?php ob_start(); ?>
<?php if (!empty($guide_items)) : ?>
            <section class="mnpk-page-section" <?php echo $mpb_section_attrs('guide'); ?>>
                <div class="mnpk-guide-grid">
                    <?php foreach ($guide_items as $item) : ?>
                        <div class="mnpk-panel">
                            <article class="mnpk-guide-card mnpk-panel__inner <?php echo !empty($item['image']['url']) ? 'has-image' : 'is-text-only'; ?>">
                                <?php if (!empty($item['image']['url'])) : ?>
                                    <figure class="mnpk-guide-card__media">
                                        <img src="<?php echo esc_url($item['image']['url']); ?>" alt="<?php echo esc_attr($item['image']['alt']); ?>">
                                    </figure>
                                <?php endif; ?>

                                <?php if ($item['title'] !== '' || $item['text'] !== '') : ?>
                                    <div class="mnpk-card__content">
                                        <?php if ($item['title'] !== '') : ?>
                                            <h2><?php echo esc_html($item['title']); ?></h2>
                                        <?php endif; ?>

                                        <?php if ($item['text'] !== '') : ?>
                                            <p><?php echo $text_to_html($item['text']); ?></p>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </article>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
<?php $mpb_layout_blocks['guide'] = ob_get_clean(); ?>

        <?php ob_start(); ?>
<?php if ($should_render_compare) : ?>
            <section class="mnpk-page-section" <?php echo $mpb_section_attrs('compare'); ?>>
                <div class="mnpk-panel">
                    <div class="mnpk-panel__inner">
                        <div class="mnpk-compare-table-wrap">
                            <table class="mnpk-compare-table">
                                <thead>
                                    <tr>
                                        <th scope="col" class="mnpk-compare-table__corner">比較項目</th>
                                        <?php foreach ($compare_columns as $col) : ?>
                                            <th scope="col" class="mnpk-compare-table__head">
<?php if ($col['title'] !== '') : ?>
                                                    <span class="mnpk-compare-table__title"><?php echo esc_html($col['title']); ?></span>
                                                <?php endif; ?>
                                            </th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($compare_rows as $row) : ?>
                                        <tr>
                                            <th scope="row" class="mnpk-compare-table__rowhead">
                                                <?php echo esc_html($row['label']); ?>
                                            </th>

                                            <?php foreach ($compare_columns as $index => $unused) : ?>
                                                <td class="mnpk-compare-table__cell" data-label="<?php echo esc_attr($compare_columns[$index]['title'] ?? ''); ?>">
                                                    <?php
                                                    $cell_text = isset($row['cells'][$index]) ? trim((string) $row['cells'][$index]) : '';
                                                    if ($cell_text !== '') {
                                                        echo $text_to_html($cell_text);
                                                    }
                                                    ?>
                                                </td>
                                            <?php endforeach; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
        <?php endif; ?>
<?php $mpb_layout_blocks['compare'] = ob_get_clean(); ?>

        <?php ob_start(); ?>
<?php if (!empty($flow_items)) : ?>
            <section class="mnpk-page-section" <?php echo $mpb_section_attrs('flow'); ?>>
                <div class="mnpk-panel mnpk-page-flow">
                    <div class="mnpk-panel__inner">
                        <div class="mnpk-flow-track">
                            <?php foreach ($flow_items as $index => $item) : ?>
                                <article class="mnpk-flow-step">
                                    <p class="mnpk-flow-step__label">STEP <?php echo esc_html((string) ($index + 1)); ?></p>

                                    <?php if ($item['title'] !== '') : ?>
                                        <h3><?php echo esc_html($item['title']); ?></h3>
                                    <?php endif; ?>

                                    <?php if ($item['text'] !== '') : ?>
                                        <p><?php echo $text_to_html($item['text']); ?></p>
                                    <?php endif; ?>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </section>
        <?php endif; ?>
<?php $mpb_layout_blocks['flow'] = ob_get_clean(); ?>

        <?php ob_start(); ?>
<?php if ($should_render_faq) : ?>
            <section class="mnpk-page-section" <?php echo $mpb_section_attrs('faq'); ?>>
                <div class="mnpk-panel mnpk-page-faq">
                    <div class="mnpk-panel__inner">
                        <?php if ($faq_title !== '') : ?>
                            <h2><?php echo esc_html($faq_title); ?></h2>
                        <?php endif; ?>

                        <?php if ($faq_text !== '') : ?>
                            <p><?php echo $text_to_html($faq_text); ?></p>
                        <?php endif; ?>

                        <?php if (!empty($faq_items)) : ?>
                            <div class="mnpk-faq-list">
                                <?php foreach ($faq_items as $faq_index => $item) : ?>
                                    <details class="mnpk-faq-item"<?php echo $faq_index === 0 ? " open" : ""; ?>>
                                        <summary class="mnpk-faq-item__q">
                                            <span class="mnpk-faq-item__label">Q</span>
                                            <span class="mnpk-faq-item__qtext"><?php echo esc_html($item['q']); ?></span>
                                        </summary>

                                        <div class="mnpk-faq-item__a">
                                            <span class="mnpk-faq-item__label is-a">A</span>
                                            <div class="mnpk-faq-item__atext">
                                                <p><?php echo $text_to_html($item['a']); ?></p>
                                            </div>
                                        </div>
                                    </details>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </section>
        <?php endif; ?>
<?php $mpb_layout_blocks['faq'] = ob_get_clean(); ?>

        <?php ob_start(); ?>
<?php if (trim(wp_strip_all_tags($content_html)) !== '') : ?>
            <section class="mnpk-page-section" <?php echo $mpb_section_attrs('content'); ?>>
                <div class="mnpk-page-entry mnpk-panel">
                    <div class="mnpk-panel__inner">
                        <?php echo $content_html; ?>
                    </div>
                </div>
            </section>
        <?php endif; ?>
<?php $mpb_layout_blocks['content'] = ob_get_clean(); ?>

        


        <?php ob_start(); ?>
<?php if ($should_render_cta) : ?>
            <section class="mnpk-page-section" <?php echo $mpb_section_attrs('cta'); ?>>
                <?php if ($should_render_contact_form) : ?>
                    <div class="mnpk-panel mnpk-page-cta is-form-only no-actions">
                        <div class="mnpk-panel__inner">
                            <?php echo $render_contact_form_template(); ?>
                        </div>
                    </div>

                <?php elseif ($should_render_cta_content) : ?>
                    <div class="mnpk-panel mnpk-page-cta <?php echo esc_attr($cta_media_class); ?> <?php echo ($cta_has_visual && $cta_has_body) ? 'is-image-and-body' : ($cta_has_body ? 'is-body-only' : 'is-image-only'); ?> <?php echo $cta_has_actions ? 'has-actions' : 'no-actions'; ?>">
                        <div class="mnpk-panel__inner">

                            <?php if ($cta_has_visual && $cta_has_body) : ?>
                                <div class="mnpk-split">
                                    <div class="mnpk-split__media mnpk-split__media--cta">
                                        <?php if ($cta_has_video) : ?>
                                            <div class="mnpk-page-cta__media is-video">
                                                <video
                                                    class="mnpk-page-cta__video"
                                                    playsinline
                                                    preload="metadata"
                                                    data-autoplay="<?php echo $cta_media_autoplay ? '1' : '0'; ?>"
                                                    <?php echo $cta_video_controls ? 'controls' : ''; ?>
                                                    <?php echo $cta_media_autoplay ? 'autoplay muted loop' : ''; ?>
                                                    <?php if (!empty($cta_first_image['url'])) : ?>
                                                        poster="<?php echo esc_url($cta_first_image['url']); ?>"
                                                    <?php endif; ?>
                                                >
                                                    <source src="<?php echo esc_url($cta['video']['url']); ?>" type="<?php echo esc_attr($cta['video']['mime'] !== '' ? $cta['video']['mime'] : 'video/mp4'); ?>">
                                                </video>
                                            </div>
                                        <?php elseif ($cta_has_swiper) : ?>
                                            <div class="mnpk-page-cta__media is-swiper">
                                                <div class="swiper mnpk-cta-swiper" data-loop="1" data-delay="<?php echo esc_attr((string) $cta_swiper_delay); ?>">
                                                    <div class="swiper-wrapper">
                                                        <?php foreach ($cta['images'] as $image) : ?>
                                                            <?php if (!empty($image['url'])) : ?>
                                                                <div class="swiper-slide">
                                                                    <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt'] ?? ''); ?>">
                                                                </div>
                                                            <?php endif; ?>
                                                        <?php endforeach; ?>
                                                    </div>
                                                    <?php if ($cta_swiper_pagination) : ?><div class="swiper-pagination"></div><?php endif; ?>
                                                    <?php if ($cta_swiper_navigation) : ?>
                                                        <div class="swiper-button-prev"></div>
                                                        <div class="swiper-button-next"></div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php elseif ($cta_has_image) : ?>
                                            <figure class="mnpk-page-cta__media is-image">
                                                <img src="<?php echo esc_url($cta_first_image['url']); ?>" alt="<?php echo esc_attr($cta_first_image['alt'] ?? ''); ?>">
                                            </figure>
                                        <?php endif; ?>
                                    </div>

                                    <div class="mnpk-split__body">
                                        <div class="mnpk-content-block">
                                            <?php if ($cta_has_title) : ?>
                                                <h2><?php echo esc_html($cta['title']); ?></h2>
                                            <?php endif; ?>

                                            <?php if ($cta_has_text) : ?>
                                                <p><?php echo $text_to_html($cta['text']); ?></p>
                                            <?php endif; ?>

                                            <?php if ($cta_has_actions) : ?>
                                                <div class="mnpk-page-actions">
                                                    <?php if (!empty($cta['btn1']['url']) && !empty($cta['btn1']['text'])) : ?>
                                                        <a class="mnpk-page-btn is-primary" href="<?php echo esc_url($cta['btn1']['url']); ?>"><?php echo esc_html($cta['btn1']['text']); ?></a>
                                                    <?php endif; ?>

                                                    <?php if (!empty($cta['btn2']['url']) && !empty($cta['btn2']['text'])) : ?>
                                                        <a class="mnpk-page-btn is-secondary" href="<?php echo esc_url($cta['btn2']['url']); ?>"><?php echo esc_html($cta['btn2']['text']); ?></a>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                            <?php else : ?>
                                <?php if ($cta_has_visual) : ?>
                                    <?php if ($cta_has_video) : ?>
                                        <div class="mnpk-page-cta__media is-video">
                                            <video
                                                class="mnpk-page-cta__video"
                                                playsinline
                                                preload="metadata"
                                                data-autoplay="<?php echo $cta_media_autoplay ? '1' : '0'; ?>"
                                                <?php echo $cta_video_controls ? 'controls' : ''; ?>
                                                <?php echo $cta_media_autoplay ? 'autoplay muted loop' : ''; ?>
                                            >
                                                <source src="<?php echo esc_url($cta['video']['url']); ?>" type="<?php echo esc_attr($cta['video']['mime'] !== '' ? $cta['video']['mime'] : 'video/mp4'); ?>">
                                            </video>
                                        </div>
                                    <?php elseif ($cta_has_swiper) : ?>
                                        <div class="mnpk-page-cta__media is-swiper">
                                            <div class="swiper mnpk-cta-swiper" data-loop="1" data-delay="<?php echo esc_attr((string) $cta_swiper_delay); ?>">
                                                <div class="swiper-wrapper">
                                                    <?php foreach ($cta['images'] as $image) : ?>
                                                        <?php if (!empty($image['url'])) : ?>
                                                            <div class="swiper-slide">
                                                                <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt'] ?? ''); ?>">
                                                            </div>
                                                        <?php endif; ?>
                                                    <?php endforeach; ?>
                                                </div>
                                                <?php if ($cta_swiper_pagination) : ?><div class="swiper-pagination"></div><?php endif; ?>
                                                <?php if ($cta_swiper_navigation) : ?>
                                                    <div class="swiper-button-prev"></div>
                                                    <div class="swiper-button-next"></div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php elseif ($cta_has_image) : ?>
                                        <figure class="mnpk-page-cta__media is-image">
                                            <img src="<?php echo esc_url($cta_first_image['url']); ?>" alt="<?php echo esc_attr($cta_first_image['alt'] ?? ''); ?>">
                                        </figure>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <?php if ($cta_has_body) : ?>
                                    <div class="mnpk-content-block">
                                        <?php if ($cta_has_title) : ?>
                                            <h2><?php echo esc_html($cta['title']); ?></h2>
                                        <?php endif; ?>

                                        <?php if ($cta_has_text) : ?>
                                            <p><?php echo $text_to_html($cta['text']); ?></p>
                                        <?php endif; ?>

                                        <?php if ($cta_has_actions) : ?>
                                            <div class="mnpk-page-actions">
                                                <?php if (!empty($cta['btn1']['url']) && !empty($cta['btn1']['text'])) : ?>
                                                    <a class="mnpk-page-btn is-primary" href="<?php echo esc_url($cta['btn1']['url']); ?>"><?php echo esc_html($cta['btn1']['text']); ?></a>
                                                <?php endif; ?>

                                                <?php if (!empty($cta['btn2']['url']) && !empty($cta['btn2']['text'])) : ?>
                                                    <a class="mnpk-page-btn is-secondary" href="<?php echo esc_url($cta['btn2']['url']); ?>"><?php echo esc_html($cta['btn2']['text']); ?></a>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>

                        </div>
                    </div>
                <?php endif; ?>
            </section>
        <?php endif; ?>
<?php $mpb_layout_blocks['cta'] = ob_get_clean(); ?>

<?php
/*
 * B2C_REAL_HTML_ORDER_RENDER
 * _mpb_sections_json の順番どおりにHTMLを出力する。
 * CSS orderではなく、PHPのecho順そのものを変える。
 */
$mpb_layout_default_sections = array(
    array('type' => 'hero',    'enabled' => 1),
    array('type' => 'intro',   'enabled' => 1),
    array('type' => 'feature', 'enabled' => 1),
    array('type' => 'guide',   'enabled' => 1),
    array('type' => 'compare', 'enabled' => 0),
    array('type' => 'flow',    'enabled' => 1),
    array('type' => 'faq',     'enabled' => 1),
    array('type' => 'content', 'enabled' => 0),
    array('type' => 'cta',     'enabled' => 1),
);

$mpb_layout_allowed = array();
foreach ($mpb_layout_default_sections as $mpb_layout_default_section) {
    $mpb_layout_allowed[$mpb_layout_default_section['type']] = $mpb_layout_default_section;
}

$mpb_layout_saved = $meta_json($post_id, '_mpb_sections_json');
$mpb_layout_sections = array();
$mpb_layout_used = array();

if (is_array($mpb_layout_saved)) {
    foreach ($mpb_layout_saved as $mpb_layout_section) {
        if (!is_array($mpb_layout_section)) {
            continue;
        }

        $mpb_layout_type = sanitize_key((string) ($mpb_layout_section['type'] ?? ''));

        if ($mpb_layout_type === '' || $mpb_layout_type === 'contact' || !isset($mpb_layout_allowed[$mpb_layout_type])) {
            continue;
        }

        $mpb_layout_sections[] = array(
            'type'    => $mpb_layout_type,
            'enabled' => isset($mpb_layout_section['enabled']) ? (int) !!$mpb_layout_section['enabled'] : 1,
        );

        $mpb_layout_used[$mpb_layout_type] = true;
    }
}

foreach ($mpb_layout_default_sections as $mpb_layout_default_section) {
    $mpb_layout_type = $mpb_layout_default_section['type'];

    if (isset($mpb_layout_used[$mpb_layout_type])) {
        continue;
    }

    $mpb_layout_sections[] = $mpb_layout_default_section;
}

$mpb_layout_rendered = array();

foreach ($mpb_layout_sections as $mpb_layout_section) {
    $mpb_layout_type = sanitize_key((string) ($mpb_layout_section['type'] ?? ''));

    if ($mpb_layout_type === '' || empty($mpb_layout_section['enabled'])) {
        continue;
    }

    if (!isset($mpb_layout_blocks[$mpb_layout_type])) {
        continue;
    }

    $mpb_layout_html = (string) $mpb_layout_blocks[$mpb_layout_type];

    if (trim($mpb_layout_html) === '') {
        continue;
    }

    echo $mpb_layout_html;
    $mpb_layout_rendered[$mpb_layout_type] = true;
}
?>

        <?php
        /*
         * =========================================================
         * B2C FOOTER NAV POSITION RULE
         * ---------------------------------------------------------
         * footer nav は必ず <main class="mnpk-page-shell"> の中、
         * </main> の直前に配置する。
         *
         * 理由:
         * - .mnpk-page-shell が B2C 固定ページ全体の幅・余白・gap を管理している
         * - main の外に出すと、FAQ / CTA / footer nav の幅がズレる
         * - footer nav 側CSSで width: min(...) や margin auto を追加して合わせない
         *
         * NG:
         * - .mnpk-page の外へ include する
         * - footer nav 専用の外側 shell を追加する
         * =========================================================
         */
        $mnpk_footer_links = locate_template('minpaku/b2c/templates/layout-footer-links.php');

        if ($mnpk_footer_links) {
            include $mnpk_footer_links;
        }
        ?>

    </main>
</div>

<?php
/**
 * B2C固定ページ共通フッター。
 *
 * 役割:
 * - footer.php を読み込む
 * - footer.php 内の wp_footer() を通す
 * - ログイン中の管理バー、WP標準JS、プラグインのfooter処理を正常に出す
 *
 * 注意:
 * - 民泊 footer nav は </main> の直前に置く
 * - get_footer() はページ全体を閉じた後に呼ぶ
 */
get_footer();
