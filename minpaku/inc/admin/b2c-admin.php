<?php

if (!function_exists('naigai_mpbfix_is_b2c_admin_page')) {
    function naigai_mpbfix_is_b2c_admin_page($post = null)
    {
        $post = get_post($post);

        if (!$post || $post->post_type !== 'page') {
            return false;
        }

        return (string) get_page_template_slug($post->ID) === 'page-minpaku-b2c.php';
    }
}


if (!defined('ABSPATH')) {
    exit;
}

/**
 * 民泊B2C 管理画面（復旧版・1カラム）
 * - タブは使わない
 * - 既存の _mpb_* メタキーへ保存する
 * - 画像はメディアライブラリから選択する
 * - 旧コード衝突回避のため、関数名は naigai_mpbfix_* を使う
 */

function naigai_mpbfix_is_target_template($post_id = 0)
{
    $post_id = $post_id ?: get_the_ID();

    if (!$post_id) {
        return false;
    }

    return get_post_type($post_id) === 'page'
        && get_page_template_slug($post_id) === 'page-minpaku-b2c.php';
}

function naigai_mpbfix_text($post_id, $key, $default = '')
{
    $value = get_post_meta($post_id, $key, true);

    if ($value === '' || $value === null) {
        return $default;
    }

    return is_scalar($value) ? (string) $value : $default;
}

function naigai_mpbfix_bool($post_id, $key, $default = false)
{
    $value = get_post_meta($post_id, $key, true);

    if ($value === '' || $value === null) {
        return $default;
    }

    return in_array((string) $value, array('1', 'true', 'on', 'yes'), true);
}

function naigai_mpbfix_json_array($post_id, $key)
{
    $raw = get_post_meta($post_id, $key, true);

    if (!is_string($raw) || trim($raw) === '') {
        return array();
    }

    $decoded = json_decode($raw, true);

    return is_array($decoded) ? $decoded : array();
}

function naigai_mpbfix_render_text($name, $label, $value = '', $placeholder = '')
{
    echo '<div class="mpbfix-field">';
    echo '<label for="' . esc_attr($name) . '">' . esc_html($label) . '</label>';
    echo '<input type="text" class="widefat" id="' . esc_attr($name) . '" name="' . esc_attr($name) . '" value="' . esc_attr($value) . '" placeholder="' . esc_attr($placeholder) . '">';
    echo '</div>';
}

function naigai_mpbfix_render_textarea($name, $label, $value = '', $rows = 4, $placeholder = '')
{
    echo '<div class="mpbfix-field">';
    echo '<label for="' . esc_attr($name) . '">' . esc_html($label) . '</label>';
    echo '<textarea class="widefat" rows="' . (int) $rows . '" id="' . esc_attr($name) . '" name="' . esc_attr($name) . '" placeholder="' . esc_attr($placeholder) . '">' . esc_textarea($value) . '</textarea>';
    echo '</div>';
}


function naigai_mpbfix_render_select($name, $label, $value, $options = array())
{
    echo '<div class="mpbfix-field">';
    echo '<label for="' . esc_attr($name) . '">' . esc_html($label) . '</label>';
    echo '<select class="widefat" id="' . esc_attr($name) . '" name="' . esc_attr($name) . '">';
    foreach ($options as $opt_value => $opt_label) {
        echo '<option value="' . esc_attr($opt_value) . '" ' . selected((string) $value, (string) $opt_value, false) . '>' . esc_html($opt_label) . '</option>';
    }
    echo '</select>';
    echo '</div>';
}

function naigai_mpbfix_render_checkbox($name, $label, $checked = false)
{
    echo '<div class="mpbfix-field">';
    echo '<label class="mpbfix-checkbox">';
    echo '<input type="checkbox" id="' . esc_attr($name) . '" name="' . esc_attr($name) . '" value="1" ' . checked($checked, true, false) . '>';
    echo ' ' . esc_html($label);
    echo '</label>';
    echo '</div>';
}

function naigai_mpbfix_attachment_preview_html($attachment_id)
{
    $attachment_id = absint($attachment_id);

    if ($attachment_id <= 0) {
        return '';
    }

    $thumb = wp_get_attachment_image_url($attachment_id, 'thumbnail');

    if (!$thumb) {
        return '';
    }

    $file = basename((string) get_attached_file($attachment_id));

    return '<div class="mpbfix-media-card">'
        . '<img src="' . esc_url($thumb) . '" alt="">'
        . '<div class="mpbfix-media-meta">ID: ' . (int) $attachment_id . '<br>' . esc_html($file) . '</div>'
        . '</div>';
}

function naigai_mpbfix_gallery_preview_html($ids_csv)
{
    $ids = array_filter(array_map('absint', preg_split('/[\s,]+/', (string) $ids_csv)));

    if (empty($ids)) {
        return '';
    }

    $html = '<div class="mpbfix-gallery-grid">';

    foreach ($ids as $attachment_id) {
        $thumb = wp_get_attachment_image_url($attachment_id, 'thumbnail');

        if (!$thumb) {
            continue;
        }

        $html .= '<div class="mpbfix-gallery-card">';
        $html .= '<img src="' . esc_url($thumb) . '" alt="">';
        $html .= '<div class="mpbfix-media-meta">ID: ' . (int) $attachment_id . '</div>';
        $html .= '</div>';
    }

    $html .= '</div>';

    return $html;
}

function naigai_mpbfix_render_media_picker($name, $label, $value = '')
{
    $attachment_id = absint($value);

    echo '<div class="mpbfix-field js-mpbfix-media-field">';
    echo '<label>' . esc_html($label) . '</label>';
    echo '<input type="hidden" class="js-mpbfix-media-id" name="' . esc_attr($name) . '" value="' . esc_attr($attachment_id) . '">';
    echo '<div class="mpbfix-actions">';
    echo '<button type="button" class="button button-secondary js-mpbfix-pick-image">画像を選ぶ</button>';
    echo '<button type="button" class="button js-mpbfix-clear-image">クリア</button>';
    echo '</div>';
    echo '<div class="mpbfix-media-preview">' . naigai_mpbfix_attachment_preview_html($attachment_id) . '</div>';
    echo '</div>';
}

function naigai_mpbfix_render_gallery_picker($name, $label, $value = '')
{
    echo '<div class="mpbfix-field js-mpbfix-gallery-field">';
    echo '<label>' . esc_html($label) . '</label>';
    echo '<input type="hidden" class="js-mpbfix-gallery-ids" name="' . esc_attr($name) . '" value="' . esc_attr((string) $value) . '">';
    echo '<div class="mpbfix-actions">';
    echo '<button type="button" class="button button-secondary js-mpbfix-pick-gallery">複数画像を選ぶ</button>';
    echo '<button type="button" class="button js-mpbfix-clear-gallery">クリア</button>';
    echo '</div>';
    echo '<div class="mpbfix-gallery-preview">' . naigai_mpbfix_gallery_preview_html($value) . '</div>';
    echo '<p class="description">2枚以上でフロントは swiper 表示になります。</p>';
    echo '</div>';
}


function naigai_mpbfix_video_preview_html($attachment_id)
{
    $attachment_id = absint($attachment_id);

    if ($attachment_id <= 0) {
        return '';
    }

    $file = basename((string) get_attached_file($attachment_id));
    $url  = wp_get_attachment_url($attachment_id);

    if (!$url) {
        return '';
    }

    return '<div class="mpbfix-video-card">'
        . '<div class="mpbfix-media-meta">ID: ' . (int) $attachment_id . '<br>' . esc_html($file) . '</div>'
        . '</div>';
}

function naigai_mpbfix_render_video_picker($name, $label, $value = '')
{
    $attachment_id = absint($value);

    echo '<div class="mpbfix-field js-mpbfix-video-field">';
    echo '<label>' . esc_html($label) . '</label>';
    echo '<input type="hidden" class="js-mpbfix-video-id" name="' . esc_attr($name) . '" value="' . esc_attr($attachment_id) . '">';
    echo '<div class="mpbfix-actions">';
    echo '<button type="button" class="button button-secondary js-mpbfix-pick-video">mp4を選ぶ</button>';
    echo '<button type="button" class="button js-mpbfix-clear-video">クリア</button>';
    echo '</div>';
    echo '<div class="mpbfix-video-preview">' . naigai_mpbfix_video_preview_html($attachment_id) . '</div>';
    echo '</div>';
}


function naigai_mpbfix_register_page_metabox($post = null)
{
    if ($post && get_post_type($post) !== 'page') {
        return;
    }

    remove_meta_box('mpb_b2c_settings', 'page', 'normal');
    remove_meta_box('mpb_b2c_settings', 'page', 'advanced');
    remove_meta_box('mpb_b2c_settings', 'page', 'side');

    remove_meta_box('naigai_mpb2_b2c_settings', 'page', 'normal');
    remove_meta_box('naigai_mpb2_b2c_settings', 'page', 'advanced');
    remove_meta_box('naigai_mpb2_b2c_settings', 'page', 'side');

    remove_meta_box('naigai_mpbfix_b2c_settings', 'page', 'normal');
    remove_meta_box('naigai_mpbfix_b2c_settings', 'page', 'advanced');
    remove_meta_box('naigai_mpbfix_b2c_settings', 'page', 'side');

    add_meta_box(
        'naigai_mpbfix_b2c_settings',
        '民泊B2C 設定',
        'naigai_mpbfix_render_meta_box',
        'page',
        'normal',
        'high'
    );
}

if (!function_exists('naigai_mpbfix_is_b2c_template_page')) {
    function naigai_mpbfix_is_b2c_template_page($post_id = 0)
    {
        $post_id = absint($post_id);
        if ($post_id <= 0) {
            return false;
        }

        return get_post_type($post_id) === 'page'
            && get_page_template_slug($post_id) === 'page-minpaku-b2c.php';
    }
}

function naigai_mpbfix_add_meta_boxes_page($post)
{
    
        if (!naigai_mpbfix_is_b2c_admin_page($post)) { return; }
$post_id = is_object($post) && isset($post->ID) ? absint($post->ID) : 0;
    if (!naigai_mpbfix_is_b2c_template_page($post_id)) {
        return;
    }

    naigai_mpbfix_register_page_metabox($post);
}
add_action('add_meta_boxes_page', 'naigai_mpbfix_add_meta_boxes_page', 100, 1);

function naigai_mpbfix_add_meta_boxes_fallback($post_type, $post)
{
    
        if ($post_type !== 'page' || !naigai_mpbfix_is_b2c_admin_page($post)) { return; }
$post_id = is_object($post) && isset($post->ID) ? absint($post->ID) : 0;
    if (!naigai_mpbfix_is_b2c_template_page($post_id)) {
        return;
    }

    if ($post_type !== 'page') {
        return;
    }

    naigai_mpbfix_register_page_metabox($post);
}
add_action('add_meta_boxes', 'naigai_mpbfix_add_meta_boxes_fallback', 100, 2);

function naigai_mpbfix_enqueue_admin_assets($hook)
{
    if (!in_array($hook, array('post.php', 'post-new.php'), true)) {
        return;
    }

    $screen = function_exists('get_current_screen') ? get_current_screen() : null;

    if (!$screen || $screen->post_type !== 'page') {
        return;
    }

    wp_enqueue_media();

    $css_abs = get_template_directory() . '/minpaku/admin/css/minpaku-b2c-admin.css';
    $js_abs  = get_template_directory() . '/minpaku/admin/js/minpaku-b2c-admin.js';

    if (file_exists($css_abs)) {
        wp_enqueue_style(
            'naigai-mpbfix-admin-css',
            get_template_directory_uri() . '/minpaku/admin/css/minpaku-b2c-admin.css',
            array(),
            (string) filemtime($css_abs)
        );
    }

    if (file_exists($js_abs)) {
        wp_enqueue_script(
            'naigai-mpbfix-admin-js',
            get_template_directory_uri() . '/minpaku/admin/js/minpaku-b2c-admin.js',
            array('jquery'),
            (string) filemtime($js_abs),
            true
        );
    }
}
add_action('admin_enqueue_scripts', 'naigai_mpbfix_enqueue_admin_assets');

/**
 * =========================================================
 * B2C ADMIN MINPAKU LINK SELECT HELPERS
 * ---------------------------------------------------------
 * 役割:
 * - B2C固定ページのボタンURLを自由入力にしない
 * - minpaku関連の公開済み固定ページだけを選択肢にする
 * - /minpaku-stay/ のような民泊アーカイブURLも固定候補として出す
 *
 * 対象:
 * - HERO ボタン URL
 * - Feature ボタン URL
 * - CTA ボタン URL
 *
 * 注意:
 * - フロント側のメタキー名は変えない
 * - 既存URLが選択肢外の場合は、管理画面では「現在保存中」として表示する
 * - 保存時に新規で選択肢外URLを入れることは防ぐ
 * =========================================================
 */

if (!function_exists('naigai_mpbfix_normalize_minpaku_url')) {
    function naigai_mpbfix_normalize_minpaku_url($url)
    {
        $url = trim((string) $url);

        if ($url === '') {
            return '';
        }

        return trailingslashit(esc_url_raw($url));
    }
}

if (!function_exists('naigai_mpbfix_get_minpaku_url_options')) {
    function naigai_mpbfix_get_minpaku_url_options()
    {
        static $options = null;

        if (is_array($options)) {
            return $options;
        }

        $options = array(
            '' => '選択してください',
        );

        /*
         * 宿泊施設一覧は固定ページではなく、minpaku投稿タイプのアーカイブ。
         * get_post_type_archive_link() が取れない場合は既存URLへfallbackする。
         */
        $archive_url = get_post_type_archive_link('minpaku');
        if (!$archive_url) {
            $archive_url = home_url('/minpaku-stay/');
        }

        $archive_url = naigai_mpbfix_normalize_minpaku_url($archive_url);
        if ($archive_url !== '') {
            $options[$archive_url] = '宿泊施設一覧（/minpaku-stay/）';
        }

        /*
         * CONTACT URL OPTION / B2C button select
         * -----------------------------------------------------
         * B2C固定ページのCTA/HEROボタンから、通常のお問い合わせへ誘導する。
         * contact は minpaku 固定ページではないが、B2C導線で使うため例外として許可する。
         */
        $contact_page = get_page_by_path('contact');
        $contact_url  = $contact_page instanceof WP_Post ? get_permalink($contact_page->ID) : home_url('/contact/');
        $contact_url  = naigai_mpbfix_normalize_minpaku_url($contact_url);

        if ($contact_url !== '') {
            $options[$contact_url] = 'お問い合わせ（/contact/）';
        }

        $pages = get_pages(array(
            'post_status' => 'publish',
            'sort_column' => 'post_title',
            'sort_order' => 'ASC',
        ));

        foreach ($pages as $page) {
            if (!$page instanceof WP_Post) {
                continue;
            }

            $slug     = (string) $page->post_name;
            $template = (string) get_page_template_slug($page->ID);

            $is_minpaku_page =
                $slug === 'minpaku'
                || strpos($slug, 'minpaku-') === 0
                || in_array($template, array(
                    'page-minpaku-b2c.php',
                    'page-minpaku-support.php',
                ), true);

            if (!$is_minpaku_page) {
                continue;
            }

            $url = naigai_mpbfix_normalize_minpaku_url(get_permalink($page->ID));

            if ($url === '') {
                continue;
            }

            $options[$url] = get_the_title($page->ID) . '（/' . $slug . '/）';
        }

        return $options;
    }
}

if (!function_exists('naigai_mpbfix_render_minpaku_url_select')) {
    function naigai_mpbfix_render_minpaku_url_select($name, $label, $value, $help = '')
    {
        $value   = naigai_mpbfix_normalize_minpaku_url($value);
        $options = naigai_mpbfix_get_minpaku_url_options();

        echo '<p class="mpbfix-field mpbfix-field--minpaku-url">';
        echo '<label><strong>' . esc_html($label) . '</strong><br>';
        echo '<select name="' . esc_attr($name) . '" style="width:100%;max-width:720px;">';

        if ($value !== '' && !isset($options[$value])) {
            echo '<option value="' . esc_attr($value) . '" selected>';
            echo esc_html('現在保存中（選択肢外）: ' . $value);
            echo '</option>';
        }

        foreach ($options as $url => $option_label) {
            echo '<option value="' . esc_attr($url) . '" ' . selected($value, $url, false) . '>';
            echo esc_html($option_label);
            echo '</option>';
        }

        echo '</select></label>';

        $description = $help !== ''
            ? $help
            : 'minpaku関連の公開済み固定ページ、または宿泊施設一覧だけ選択できます。';

        echo '<br><span class="description">' . esc_html($description) . '</span>';
        echo '</p>';
    }
}

if (!function_exists('naigai_mpbfix_sanitize_minpaku_url_select')) {
    function naigai_mpbfix_sanitize_minpaku_url_select($raw_url, $old_url = '')
    {
        $url = naigai_mpbfix_normalize_minpaku_url($raw_url);

        if ($url === '') {
            return '';
        }

        $options = naigai_mpbfix_get_minpaku_url_options();

        if (isset($options[$url])) {
            return $url;
        }

        /*
         * 既に保存されていた選択肢外URLは、保存ボタンで突然消さない。
         * ただし、新規で選択肢外URLを保存することは防ぐ。
         */
        $old_url = naigai_mpbfix_normalize_minpaku_url($old_url);

        if ($old_url !== '' && $url === $old_url) {
            return $old_url;
        }

        return '';
    }
}


function naigai_mpbfix_render_meta_box($post)
{
    
        if (!naigai_mpbfix_is_b2c_admin_page($post)) { return; }
wp_nonce_field('naigai_mpbfix_save_meta_box', 'naigai_mpbfix_meta_box_nonce');
    echo '<input type="hidden" name="naigai_mpbfix_meta_box_present" value="1">';

    $current_template = get_page_template_slug($post->ID);

    if (!naigai_mpbfix_is_target_template($post->ID)) {
        echo '<p><strong>この固定ページは民泊B2Cテンプレートではありません。</strong></p>';
        echo '<p>現在のテンプレート: <code>' . esc_html($current_template !== '' ? $current_template : '(default)') . '</code></p>';
        echo '<p>テンプレートを <code>page-minpaku-b2c.php</code> に設定すると、この下に民泊B2C用の入力欄が出ます。</p>';
        return;
    }

    $feature_items = naigai_mpbfix_json_array($post->ID, '_mpb_feature_items_json');
    $guide_items   = naigai_mpbfix_json_array($post->ID, '_mpb_guide_items_json');
    $flow_items    = naigai_mpbfix_json_array($post->ID, '_mpb_flow_items_json');
    $faq_items     = naigai_mpbfix_json_array($post->ID, '_mpb_faq_items_json');
    $compare       = naigai_mpbfix_json_array($post->ID, '_mpb_compare_table_json');

    $compare_columns = (!empty($compare['columns']) && is_array($compare['columns'])) ? $compare['columns'] : array();
    $compare_rows    = (!empty($compare['rows']) && is_array($compare['rows'])) ? $compare['rows'] : array();

    echo '<div class="mpbfix-wrap">';

    echo '<div class="mpbfix-section"><h2>基本設定</h2>';
    naigai_mpbfix_render_text('_mpb_layout_type', 'レイアウト種別', naigai_mpbfix_text($post->ID, '_mpb_layout_type', 'standard'), 'standard / guide / family / group / workation / compare');
    naigai_mpbfix_render_checkbox('_mpb_show_hero', 'Hero を表示', naigai_mpbfix_bool($post->ID, '_mpb_show_hero', true));
    naigai_mpbfix_render_checkbox('_mpb_show_intro', 'Intro を表示', naigai_mpbfix_bool($post->ID, '_mpb_show_intro', true));
    naigai_mpbfix_render_checkbox('_mpb_show_footer_nav', '共通 footer nav を表示', naigai_mpbfix_bool($post->ID, '_mpb_show_footer_nav', true));
    echo '</div>';

    // B2Cビルダー準備: セクションの表示/非表示と並び順を保存する。
    // 基本設定とは分けて、独立したセクションとして表示する。
    naigai_mpbfix_render_sections_builder($post->ID);

    echo '<div class="mpbfix-section"><h2>Hero</h2>';
echo '';
    naigai_mpbfix_render_text('_mpb_hero_eyebrow', 'HERO 上小見出し', naigai_mpbfix_text($post->ID, '_mpb_hero_eyebrow', ''));
    naigai_mpbfix_render_text('_mpb_hero_title', 'HERO タイトル', naigai_mpbfix_text($post->ID, '_mpb_hero_title', ''));
    naigai_mpbfix_render_textarea('_mpb_hero_lead', 'HERO 説明文', naigai_mpbfix_text($post->ID, '_mpb_hero_lead', ''), 4);
    naigai_mpbfix_render_media_picker('_mpb_hero_image_id', 'HERO 単体画像', naigai_mpbfix_text($post->ID, '_mpb_hero_image_id', ''));
    naigai_mpbfix_render_gallery_picker('_mpb_hero_gallery_ids', 'HERO ギャラリー画像', naigai_mpbfix_text($post->ID, '_mpb_hero_gallery_ids', ''));
    naigai_mpbfix_render_video_picker('_mpb_hero_video_mp4_id', 'HERO mp4 動画', naigai_mpbfix_text($post->ID, '_mpb_hero_video_mp4_id', ''));
    echo '<p class="description mpbfix-hero-media-note">ギャラリー画像を入れると mp4 は選択不可。mp4 を入れるとギャラリー画像は自動で解除されます。</p>';
    naigai_mpbfix_render_checkbox('_mpb_hero_swiper_autoplay', 'Hero を自動スライドする', naigai_mpbfix_bool($post->ID, '_mpb_hero_swiper_autoplay', true));
    naigai_mpbfix_render_checkbox('_mpb_hero_swiper_pagination', 'ページャを表示する', naigai_mpbfix_bool($post->ID, '_mpb_hero_swiper_pagination', true));
    naigai_mpbfix_render_checkbox('_mpb_hero_swiper_navigation', '矢印を表示する', naigai_mpbfix_bool($post->ID, '_mpb_hero_swiper_navigation', false));
    naigai_mpbfix_render_checkbox('_mpb_hero_swiper_loop', 'ループする', naigai_mpbfix_bool($post->ID, '_mpb_hero_swiper_loop', true));
    naigai_mpbfix_render_select('_mpb_hero_swiper_pagination_position', 'ページャ配置', naigai_mpbfix_text($post->ID, '_mpb_hero_swiper_pagination_position', 'center-bottom'), array(
        'center-bottom' => '中央下',
        'right-bottom' => '右下',
        'right-center-vertical' => '右中央縦並び',
    ));
    naigai_mpbfix_render_text('_mpb_hero_swiper_delay', '自動スライド間隔(ms)', naigai_mpbfix_text($post->ID, '_mpb_hero_swiper_delay', '5000'), '5000');
    naigai_mpbfix_render_text('_mpb_hero_btn1_text', 'HERO ボタン1 テキスト', naigai_mpbfix_text($post->ID, '_mpb_hero_btn1_text', ''));
    naigai_mpbfix_render_minpaku_url_select('_mpb_hero_btn1_url', 'HERO ボタン1 URL', naigai_mpbfix_text($post->ID, '_mpb_hero_btn1_url', ''));
    naigai_mpbfix_render_text('_mpb_hero_btn2_text', 'HERO ボタン2 テキスト', naigai_mpbfix_text($post->ID, '_mpb_hero_btn2_text', ''));
    naigai_mpbfix_render_minpaku_url_select('_mpb_hero_btn2_url', 'HERO ボタン2 URL', naigai_mpbfix_text($post->ID, '_mpb_hero_btn2_url', ''));
    echo '</div>';

    echo '<div class="mpbfix-section"><h2>Intro</h2>';
echo '';
    naigai_mpbfix_render_text('_mpb_intro_title', 'Intro タイトル', naigai_mpbfix_text($post->ID, '_mpb_intro_title', ''));
    naigai_mpbfix_render_textarea('_mpb_intro_text', 'Intro 説明文', naigai_mpbfix_text($post->ID, '_mpb_intro_text', ''), 5);
    naigai_mpbfix_render_media_picker('_mpb_intro_image_id', 'Intro 画像', naigai_mpbfix_text($post->ID, '_mpb_intro_image_id', ''));
    echo '</div>';

    echo '<div class="mpbfix-section mpbfix-repeat-section" data-mpbfix-repeat="feature"><h2>Feature</h2>';
echo '<p class="description">必要な件数まで追加できます。空の項目は保存しません。</p><p><button type="button" class="button button-secondary js-mpbfix-repeat-add">＋ Feature を追加</button></p>';
    for ($i = 0; $i < 50; $i++) {
        $item = isset($feature_items[$i]) && is_array($feature_items[$i]) ? $feature_items[$i] : array();
        $btn1 = !empty($item['btn1']) && is_array($item['btn1']) ? $item['btn1'] : array();
        $btn2 = !empty($item['btn2']) && is_array($item['btn2']) ? $item['btn2'] : array();

        echo '<div class="mpbfix-item mpbfix-repeat-item"><h3>Feature ' . ($i + 1) . '</h3>';
        naigai_mpbfix_render_text("mpb_feature_{$i}_label", 'ラベル', $item['label'] ?? '');
        naigai_mpbfix_render_text("mpb_feature_{$i}_title", 'タイトル', $item['title'] ?? '');
        naigai_mpbfix_render_textarea("mpb_feature_{$i}_text", '説明文', $item['text'] ?? '', 4);
        naigai_mpbfix_render_media_picker("mpb_feature_{$i}_image_id", '画像', $item['image_id'] ?? 0);
        naigai_mpbfix_render_text("mpb_feature_{$i}_btn1_text", 'ボタン1 テキスト', $btn1['text'] ?? '');
        naigai_mpbfix_render_minpaku_url_select("mpb_feature_{$i}_btn1_url", 'ボタン1 URL', $btn1['url'] ?? '');
        naigai_mpbfix_render_text("mpb_feature_{$i}_btn2_text", 'ボタン2 テキスト', $btn2['text'] ?? '');
        naigai_mpbfix_render_minpaku_url_select("mpb_feature_{$i}_btn2_url", 'ボタン2 URL', $btn2['url'] ?? '');
        echo '</div>';
    }
    echo '</div>';

    echo '<div class="mpbfix-section mpbfix-repeat-section" data-mpbfix-repeat="guide"><h2>Guide</h2>';
echo '<p class="description">必要な件数まで追加できます。空の項目は保存しません。</p><p><button type="button" class="button button-secondary js-mpbfix-repeat-add">＋ Guide を追加</button></p>';
    for ($i = 0; $i < 50; $i++) {
        $item = isset($guide_items[$i]) && is_array($guide_items[$i]) ? $guide_items[$i] : array();
        echo '<div class="mpbfix-item mpbfix-repeat-item"><h3>Guide ' . ($i + 1) . '</h3>';
        naigai_mpbfix_render_text("mpb_guide_{$i}_title", 'タイトル', $item['title'] ?? '');
        naigai_mpbfix_render_textarea("mpb_guide_{$i}_text", '説明文', $item['text'] ?? '', 4);
        naigai_mpbfix_render_media_picker("mpb_guide_{$i}_image_id", '画像', $item['image_id'] ?? 0);
        echo '</div>';
    }
    echo '</div>';

    echo '<div class="mpbfix-section mpbfix-compare-builder-section" data-mpbfix-compare-builder="1"><h2>比較表</h2>';
echo '';
    echo '<p class="description">比較表は、列と行を必要な分だけ追加できます。空の列・空の行は保存しません。将来のページビルダーでも使えるように、保存先は <code>_mpb_compare_table_json</code> にまとめます。</p>';

    echo '<div class="mpbfix-compare-actions">';
    echo '<button type="button" class="button button-secondary js-mpbfix-compare-add" data-mpbfix-compare-add="col">＋ 列を追加</button> ';
    echo '<button type="button" class="button button-secondary js-mpbfix-compare-add" data-mpbfix-compare-add="row">＋ 行を追加</button>';
    echo '</div>';

    echo '<div class="mpbfix-compare-note">';
    echo '<strong>使い方:</strong> 列は「民泊 / 一棟貸し / 貸別荘」などの比較対象、行は「向いている使い方 / 過ごし方 / 確認したいこと」などの比較項目です。';
    echo '</div>';

    echo '<h3 class="mpbfix-compare-subtitle">比較する列</h3>';
    echo '<div class="mpbfix-compare-column-list">';
    for ($i = 0; $i < 12; $i++) {
        $col = isset($compare_columns[$i]) && is_array($compare_columns[$i]) ? $compare_columns[$i] : array();

        echo '<div class="mpbfix-item mpbfix-compare-column" data-mpbfix-compare-col="' . esc_attr((string) $i) . '">';
        echo '<h3>列 ' . ($i + 1) . '</h3>';

        /*
         * 比較表の列データ。
         *
         * 保存先:
         * _mpb_compare_table_json["columns"][]
         *
         * 将来ビルダー対応:
         * type=compare のセクションがこの columns を読むだけで、
         * 任意の場所に比較表を配置できる。
         */
        naigai_mpbfix_render_text("mpb_compare_col_{$i}_title", '列タイトル', $col['title'] ?? '');
        // 比較表はテキスト専用。画像は Feature / Guide 側で管理する。
echo '</div>';
    }
    echo '</div>';

    echo '<h3 class="mpbfix-compare-subtitle">比較する行</h3>';
    echo '<div class="mpbfix-compare-row-list">';
    for ($i = 0; $i < 50; $i++) {
        $row = isset($compare_rows[$i]) && is_array($compare_rows[$i]) ? $compare_rows[$i] : array();
        $cells = isset($row['cells']) && is_array($row['cells']) ? $row['cells'] : array();

        echo '<div class="mpbfix-item mpbfix-compare-row" data-mpbfix-compare-row="' . esc_attr((string) $i) . '">';
        echo '<h3>行 ' . ($i + 1) . '</h3>';

        /*
         * 比較表の行ラベル。
         *
         * 例:
         * - 向いている使い方
         * - 過ごし方
         * - 確認したいこと
         */
        naigai_mpbfix_render_text("mpb_compare_row_{$i}_label", '行ラベル', $row['label'] ?? '');

        echo '<div class="mpbfix-compare-row-cells">';
        for ($j = 0; $j < 12; $j++) {
            echo '<div class="mpbfix-compare-cell" data-mpbfix-compare-cell-col="' . esc_attr((string) $j) . '">';

            /*
             * 比較表のセル本文。
             *
             * 保存先:
             * _mpb_compare_table_json["rows"][$i]["cells"][$j]
             *
             * 注意:
             * 列数に合わせてJSで表示/非表示を切り替える。
             * 空セルは保存時に末尾から整理される。
             */
            naigai_mpbfix_render_textarea("mpb_compare_row_{$i}_cell_{$j}", '列' . ($j + 1) . ' 本文', $cells[$j] ?? '', 3);

            echo '</div>';
        }
        echo '</div>';

        echo '</div>';
    }
    echo '</div>';

    echo '</div>';


    echo '<div class="mpbfix-section mpbfix-repeat-section" data-mpbfix-repeat="flow"><h2>Flow</h2>';
echo '<p class="description">必要な件数まで追加できます。空の項目は保存しません。</p><p><button type="button" class="button button-secondary js-mpbfix-repeat-add">＋ Flow を追加</button></p>';
    for ($i = 0; $i < 50; $i++) {
        $item = isset($flow_items[$i]) && is_array($flow_items[$i]) ? $flow_items[$i] : array();
        echo '<div class="mpbfix-item mpbfix-repeat-item"><h3>STEP ' . ($i + 1) . '</h3>';
        naigai_mpbfix_render_text("mpb_flow_{$i}_title", 'タイトル', $item['title'] ?? '');
        naigai_mpbfix_render_textarea("mpb_flow_{$i}_text", '説明文', $item['text'] ?? '', 4);
        echo '</div>';
    }
    echo '</div>';

    echo '<div class="mpbfix-section mpbfix-repeat-section" data-mpbfix-repeat="faq"><h2>FAQ</h2>';
echo '';
    echo '<p class="description">必要な件数まで追加できます。空の項目は保存しません。</p><p><button type="button" class="button button-secondary js-mpbfix-repeat-add">＋ FAQ を追加</button></p>';
    naigai_mpbfix_render_text('_mpb_faq_title', 'FAQ タイトル', naigai_mpbfix_text($post->ID, '_mpb_faq_title', ''));
    naigai_mpbfix_render_textarea('_mpb_faq_text', 'FAQ 導入文', naigai_mpbfix_text($post->ID, '_mpb_faq_text', ''), 4);
    for ($i = 0; $i < 50; $i++) {
        $item = isset($faq_items[$i]) && is_array($faq_items[$i]) ? $faq_items[$i] : array();
        echo '<div class="mpbfix-item mpbfix-repeat-item"><h3>FAQ ' . ($i + 1) . '</h3>';
        naigai_mpbfix_render_text("mpb_faq_{$i}_q", '質問', $item['q'] ?? '');
        naigai_mpbfix_render_textarea("mpb_faq_{$i}_a", '回答', $item['a'] ?? '', 4);
        echo '</div>';
    }
    echo '</div>';

    echo '<div class="mpbfix-section"><h2>CTA</h2>';
naigai_mpbfix_render_text('_mpb_cta_title', 'CTA タイトル', naigai_mpbfix_text($post->ID, '_mpb_cta_title', ''));
    naigai_mpbfix_render_textarea('_mpb_cta_text', 'CTA 説明文', naigai_mpbfix_text($post->ID, '_mpb_cta_text', ''), 4);
    naigai_mpbfix_render_media_picker('_mpb_cta_image_id', 'CTA 単体画像', naigai_mpbfix_text($post->ID, '_mpb_cta_image_id', ''));
    naigai_mpbfix_render_gallery_picker('_mpb_cta_gallery_ids', 'CTA ギャラリー画像', naigai_mpbfix_text($post->ID, '_mpb_cta_gallery_ids', ''));
    naigai_mpbfix_render_video_picker('_mpb_cta_video_mp4_id', 'CTA mp4 動画', naigai_mpbfix_text($post->ID, '_mpb_cta_video_mp4_id', ''));

    $cta_show_media = get_post_meta($post->ID, '_mpb_cta_show_media', true);
    if ($cta_show_media === '') {
        $cta_show_media = '1';
    }

    $cta_media_autoplay = get_post_meta($post->ID, '_mpb_cta_media_autoplay', true);
    if ($cta_media_autoplay === '') {
        $cta_media_autoplay = '0';
    }

    $cta_video_controls = get_post_meta($post->ID, '_mpb_cta_video_controls', true);
    if ($cta_video_controls === '') {
        $cta_video_controls = '1';
    }

    $cta_swiper_pagination = get_post_meta($post->ID, '_mpb_cta_swiper_pagination', true);
    if ($cta_swiper_pagination === '') {
        $cta_swiper_pagination = '1';
    }

    $cta_swiper_navigation = get_post_meta($post->ID, '_mpb_cta_swiper_navigation', true);
    if ($cta_swiper_navigation === '') {
        $cta_swiper_navigation = '1';
    }

    $cta_swiper_delay = (int) get_post_meta($post->ID, '_mpb_cta_swiper_delay', true);
    if ($cta_swiper_delay <= 0) {
        $cta_swiper_delay = 5000;
    }

    echo '<p class="description">通常CTAを選んだ時だけ使います。「CTAメディアを表示する」をONにすると、画像・swiper・mp4をCTAに表示します。mp4動画がある場合はmp4を優先します。ギャラリー画像を2枚以上選ぶとswiper表示になります。単体画像だけの場合は1枚画像を表示します。コンタクトフォームを選んだ時は、CTA画像・CTAボタンは使わず、フォームだけを表示します。</p>';

    echo '<p><label><input type="checkbox" name="_mpb_cta_show_media" value="1" ' . checked('1', $cta_show_media, false) . '> CTAに画像・動画を表示する</label></p>';
    echo '<p><label><input type="checkbox" name="_mpb_cta_media_autoplay" value="1" ' . checked('1', $cta_media_autoplay, false) . '> 自動再生を有効にする</label></p>';
    echo '<p><label><input type="checkbox" name="_mpb_cta_video_controls" value="1" ' . checked('1', $cta_video_controls, false) . '> mp4 コントローラーを表示する</label></p>';
    echo '<p><label><input type="checkbox" name="_mpb_cta_swiper_pagination" value="1" ' . checked('1', $cta_swiper_pagination, false) . '> swiper ページャーを表示する</label></p>';
    echo '<p><label><input type="checkbox" name="_mpb_cta_swiper_navigation" value="1" ' . checked('1', $cta_swiper_navigation, false) . '> swiper 矢印を表示する</label></p>';
    echo '<p><label>swiper 自動再生間隔(ms) <input type="number" min="1000" step="500" name="_mpb_cta_swiper_delay" value="' . esc_attr((string) $cta_swiper_delay) . '" style="width:120px;"></label></p>';
    naigai_mpbfix_render_text('_mpb_cta_btn1_text', 'CTA ボタン1 テキスト', naigai_mpbfix_text($post->ID, '_mpb_cta_btn1_text', ''));
    naigai_mpbfix_render_minpaku_url_select('_mpb_cta_btn1_url', 'CTA ボタン1 URL', naigai_mpbfix_text($post->ID, '_mpb_cta_btn1_url', ''));
    naigai_mpbfix_render_text('_mpb_cta_btn2_text', 'CTA ボタン2 テキスト', naigai_mpbfix_text($post->ID, '_mpb_cta_btn2_text', ''));
    naigai_mpbfix_render_minpaku_url_select('_mpb_cta_btn2_url', 'CTA ボタン2 URL', naigai_mpbfix_text($post->ID, '_mpb_cta_btn2_url', ''));
    echo '</div>';

    echo '</div>';
}

if (!function_exists('naigai_mpbfix_default_sections')) {
    function naigai_mpbfix_default_sections()
    {
        return array(
            array('type' => 'hero',    'label' => 'Hero',             'order' => 10,  'enabled' => 1),
            array('type' => 'intro',   'label' => 'Intro',            'order' => 20,  'enabled' => 1),
            array('type' => 'feature', 'label' => 'Feature',          'order' => 30,  'enabled' => 1),
            array('type' => 'guide',   'label' => 'Guide',            'order' => 40,  'enabled' => 1),
            array('type' => 'compare', 'label' => '比較表',           'order' => 50,  'enabled' => 0),
            array('type' => 'flow',    'label' => 'Flow',             'order' => 60,  'enabled' => 1),
            array('type' => 'faq',     'label' => 'FAQ',              'order' => 70,  'enabled' => 1),
            array('type' => 'content', 'label' => '本文',             'order' => 80,  'enabled' => 0),
            array('type' => 'cta',     'label' => 'CTA',              'order' => 90,  'enabled' => 1),
);
    }
}



if (!function_exists('naigai_mpbfix_get_sections_builder')) {
    /**
     * 保存済み _mpb_sections_json と標準セクションを結合する。
     *
     * 目的:
     * - 既に保存されている順番を優先
     * - 新しく追加したセクション type があれば自動で補完
     * - 壊れたJSONでも標準順に戻せる
     */
    function naigai_mpbfix_get_sections_builder($post_id)
    {
        $defaults = naigai_mpbfix_default_sections();
        $default_map = array();

        foreach ($defaults as $section) {
            $default_map[$section['type']] = $section;
        }

        $saved_raw = (string) get_post_meta($post_id, '_mpb_sections_json', true);
        $saved = json_decode($saved_raw, true);

        $merged = array();
        $used = array();

        if (is_array($saved)) {
            foreach ($saved as $row) {
                if (empty($row['type']) || !isset($default_map[$row['type']])) {
                    continue;
                }

                $type = (string) $row['type'];
                $base = $default_map[$type];

                $merged[] = array(
                    'type'    => $type,
                    'label'   => $base['label'],
                    'order'   => isset($row['order']) ? (int) $row['order'] : (int) ($base['order'] ?? 90),
                    'enabled' => isset($row['enabled']) ? (int) !!$row['enabled'] : 1,
                );

                $used[$type] = true;
            }
        }

        foreach ($defaults as $section) {
            if (isset($used[$section['type']])) {
                continue;
            }

            $merged[] = array(
                'type'    => $section['type'],
                'label'   => $section['label'],
                'order'   => (int) ($section['order'] ?? 90),
                'enabled' => 1,
            );
        }

        usort($merged, function ($a, $b) {
            return ((int) $a['order']) <=> ((int) $b['order']);
        });

        return $merged;
    }
}

if (!function_exists('naigai_mpbfix_builder_count_filled')) {
    function naigai_mpbfix_builder_count_filled($post_id, $keys)
    {
        $filled = 0;

        foreach ($keys as $key) {
            $value = get_post_meta($post_id, $key, true);

            if (is_array($value)) {
                if (!empty($value)) {
                    $filled++;
                }
                continue;
            }

            $value = trim((string) $value);

            if ($value !== '' && $value !== '0' && $value !== '[]') {
                $filled++;
            }
        }

        return $filled;
    }
}

if (!function_exists('naigai_mpbfix_builder_status')) {
    function naigai_mpbfix_builder_status($post_id, $type)
    {
        $keys_map = array(
            'hero' => array(
                '_mpb_hero_eyebrow',
                '_mpb_hero_title',
                '_mpb_hero_lead',
                '_mpb_hero_image_id',
                '_mpb_hero_gallery_ids',
                '_mpb_hero_video_mp4_id',
                '_mpb_hero_btn1_text',
                '_mpb_hero_btn2_text',
            ),
            'intro' => array(
                '_mpb_intro_title',
                '_mpb_intro_text',
                '_mpb_intro_image_id',
            ),
            'feature' => array(
                '_mpb_feature_items_json',
                '_mpb_feature1_title',
                '_mpb_feature1_text',
                '_mpb_feature1_image_id',
                '_mpb_feature2_title',
                '_mpb_feature2_text',
                '_mpb_feature2_image_id',
                '_mpb_feature3_title',
                '_mpb_feature3_text',
                '_mpb_feature3_image_id',
            ),
            'guide' => array(
                '_mpb_guide_items_json',
                '_mpb_guide_card1_title',
                '_mpb_guide_card1_text',
                '_mpb_guide_card1_image_id',
                '_mpb_guide_card2_title',
                '_mpb_guide_card2_text',
                '_mpb_guide_card2_image_id',
                '_mpb_guide_card3_title',
                '_mpb_guide_card3_text',
                '_mpb_guide_card3_image_id',
                '_mpb_guide_card4_title',
                '_mpb_guide_card4_text',
                '_mpb_guide_card4_image_id',
            ),
            'compare' => array(
                '_mpb_compare_table_json',
                '_mpb_compare_table_html',
            ),
            'flow' => array(
                '_mpb_flow_title',
                '_mpb_flow_text',
                '_mpb_flow_items_json',
                '_mpb_flow_1_title',
                '_mpb_flow_1_text',
                '_mpb_flow_2_title',
                '_mpb_flow_2_text',
                '_mpb_flow_3_title',
                '_mpb_flow_3_text',
                '_mpb_flow_4_title',
                '_mpb_flow_4_text',
            ),
            'faq' => array(
                '_mpb_faq_title',
                '_mpb_faq_text',
                '_mpb_faq_items_json',
                '_mpb_faq_1_q',
                '_mpb_faq_1_a',
                '_mpb_faq_2_q',
                '_mpb_faq_2_a',
                '_mpb_faq_3_q',
                '_mpb_faq_3_a',
                '_mpb_faq_4_q',
                '_mpb_faq_4_a',
                '_mpb_faq_5_q',
                '_mpb_faq_5_a',
                '_mpb_faq_6_q',
                '_mpb_faq_6_a',
                '_mpb_faq_7_q',
                '_mpb_faq_7_a',
                '_mpb_faq_8_q',
                '_mpb_faq_8_a',
                '_mpb_faq_9_q',
                '_mpb_faq_9_a',
                '_mpb_faq_10_q',
                '_mpb_faq_10_a',
            ),
            'cta' => array(
                '_mpb_cta_mode',
                '_mpb_cta_title',
                '_mpb_cta_text',
                '_mpb_cta_image_id',
                '_mpb_cta_gallery_ids',
                '_mpb_cta_video_mp4_id',
                '_mpb_cta_btn1_text',
                '_mpb_cta_btn2_text',
            ),
        );

        if ($type === 'content') {
            $content = trim(wp_strip_all_tags((string) get_post_field('post_content', $post_id)));
            return array(
                'filled' => $content !== '' ? 1 : 0,
                'total'  => 1,
            );
        }

        if (!isset($keys_map[$type])) {
            return array(
                'filled' => 0,
                'total'  => 0,
            );
        }

        return array(
            'filled' => naigai_mpbfix_builder_count_filled($post_id, $keys_map[$type]),
            'total'  => count($keys_map[$type]),
        );
    }
}

if (!function_exists('naigai_mpbfix_render_sections_builder')) {
    function naigai_mpbfix_render_sections_builder($post_id)
    {
        $sections = naigai_mpbfix_get_sections_builder($post_id);

        $cta_mode = get_post_meta($post_id, '_mpb_cta_mode', true);
        if ($cta_mode === '' || $cta_mode === 'both' || !in_array($cta_mode, array('cta', 'form'), true)) {
            $cta_mode = 'cta';
        }

        echo '<div class="mpbfix-section mpbfix-builder-section">';
        echo '<h2>レイアウト表示順</h2>';
        echo '<p class="description">左の ☰ をドラッグして、フロントに表示するHTMLの順番を決めます。使う要素だけ「使う」にチェックします。</p>';

        echo '<div id="mpbfix-sortable-sections" class="mpbfix-sortable-list">';

        foreach ($sections as $section) {
            $type = sanitize_key((string) ($section['type'] ?? ''));
            $label = (string) ($section['label'] ?? $type);
            $enabled = !empty($section['enabled']);

            if ($type === '' || $type === 'contact') {
                continue;
            }

            echo '<details class="mpbfix-layout-item" data-section-type="' . esc_attr($type) . '">';
            echo '<summary class="mpbfix-layout-summary">';
            echo '<span class="mpbfix-drag-handle" aria-hidden="true">☰</span>';
            echo '<strong class="mpbfix-layout-title">' . esc_html($label) . '</strong>';
            echo '<label class="mpbfix-layout-enabled" onclick="event.stopPropagation();">';
            echo '<input type="checkbox" name="mpb_sections_enabled[' . esc_attr($type) . ']" value="1" ' . checked($enabled, true, false) . '> 使う';
            echo '</label>';

            if ($type === 'cta') {
                echo '<div class="mpbfix-layout-cta-mode" onclick="event.stopPropagation();">';
                echo '<span class="mpbfix-layout-cta-mode__label">表示内容</span>';
                echo '<label><input type="radio" name="_mpb_cta_mode" value="cta" ' . checked($cta_mode, 'cta', false) . '> 通常CTA</label>';
                echo '<label><input type="radio" name="_mpb_cta_mode" value="form" ' . checked($cta_mode, 'form', false) . '> コンタクトフォーム</label>';
                echo '</div>';
            }

            echo '<input type="hidden" name="mpb_sections_ordered[]" value="' . esc_attr($type) . '">';
            echo '</summary>';

            echo '<div class="mpbfix-layout-body">';
            if ($type === 'cta') {
                echo '<p><strong>CTA</strong>をこの位置に表示します。CTAタイトル・説明文・画像・ボタンを表示します。</p>';
            } else {
                echo '<p>' . esc_html($label) . ' のHTMLブロックをこの位置に表示します。</p>';
            }
            echo '</div>';

            echo '</details>';
        }

        echo '</div>';
        echo '</div>';
    }
}



if (!function_exists('naigai_mpbfix_save_sections_builder')) {
    function naigai_mpbfix_save_sections_builder($post_id)
    {
        $defaults = naigai_mpbfix_default_sections();

        $default_map = array();
        foreach ($defaults as $section) {
            $type = sanitize_key((string) ($section['type'] ?? ''));
            if ($type !== '') {
                $default_map[$type] = $section;
            }
        }

        $enabled = array();
        if (isset($_POST['mpb_sections_enabled']) && is_array($_POST['mpb_sections_enabled'])) {
            $enabled = wp_unslash($_POST['mpb_sections_enabled']);
        }

        $ordered_types = array();

        if (isset($_POST['mpb_sections_ordered']) && is_array($_POST['mpb_sections_ordered'])) {
            foreach (wp_unslash($_POST['mpb_sections_ordered']) as $type) {
                $type = sanitize_key((string) $type);

                if ($type === 'contact') {
                    continue;
                }

                if ($type !== '' && isset($default_map[$type]) && !in_array($type, $ordered_types, true)) {
                    $ordered_types[] = $type;
                }
            }
        }

        foreach ($defaults as $section) {
            $type = sanitize_key((string) ($section['type'] ?? ''));
            if ($type !== '' && !in_array($type, $ordered_types, true)) {
                $ordered_types[] = $type;
            }
        }

        $sections = array();

        foreach ($ordered_types as $index => $type) {
            if (!isset($default_map[$type])) {
                continue;
            }

            $sections[] = array(
                'type'    => $type,
                'label'   => isset($default_map[$type]['label']) ? (string) $default_map[$type]['label'] : $type,
                'order'   => ($index + 1) * 10,
                'enabled' => isset($enabled[$type]) ? 1 : 0,
            );
        }

        update_post_meta($post_id, '_mpb_sections_json', naigai_mpbfix_json_encode($sections));
    }
}




function naigai_mpbfix_is_target_template_on_save($post_id)
{
    $saved_template = get_page_template_slug($post_id);
    if ($saved_template === 'page-minpaku-b2c.php') {
        return true;
    }

    $posted_template = '';
    if (isset($_POST['page_template'])) {
        $posted_template = sanitize_text_field(wp_unslash($_POST['page_template']));
    } elseif (isset($_POST['_wp_page_template'])) {
        $posted_template = sanitize_text_field(wp_unslash($_POST['_wp_page_template']));
    }

    if ($posted_template === 'page-minpaku-b2c.php') {
        return true;
    }

    return isset($_POST['naigai_mpbfix_meta_box_present'])
        && (string) $_POST['naigai_mpbfix_meta_box_present'] === '1';
}

function naigai_mpbfix_post_text($key, $default = '')
{
    if (!isset($_POST[$key])) {
        return $default;
    }

    return is_scalar($_POST[$key]) ? trim((string) wp_unslash($_POST[$key])) : $default;
}

function naigai_mpbfix_post_int($key, $default = 0)
{
    if (!isset($_POST[$key])) {
        return (int) $default;
    }

    return absint($_POST[$key]);
}

function naigai_mpbfix_post_bool($key, $default = false)
{
    if (!isset($_POST[$key])) {
        return $default ? '1' : '0';
    }

    return '1';
}

function naigai_mpbfix_json_encode($value)
{
    return wp_json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}

function naigai_mpbfix_save_meta_box($post_id)
{
    if (!isset($_POST['naigai_mpbfix_meta_box_nonce']) || !wp_verify_nonce($_POST['naigai_mpbfix_meta_box_nonce'], 'naigai_mpbfix_save_meta_box')) {
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

    if (!naigai_mpbfix_is_target_template_on_save($post_id)) {
        return;
    }

    // B2Cビルダー準備: セクションの表示/非表示と並び順を保存する。
    naigai_mpbfix_save_sections_builder($post_id);

    $text_fields = array(
        '_mpb_layout_type',
        '_mpb_hero_eyebrow',
        '_mpb_hero_title',
        '_mpb_hero_lead',
        '_mpb_hero_btn1_text',
        '_mpb_hero_btn2_text',
        '_mpb_hero_swiper_pagination_position',
        '_mpb_hero_swiper_delay',
        '_mpb_intro_title',
        '_mpb_intro_text',
        '_mpb_faq_title',
        '_mpb_faq_text',
        '_mpb_cta_title',
        '_mpb_cta_text',
        '_mpb_cta_btn1_text',
        '_mpb_cta_btn2_text',
        '_mpb_hero_gallery_ids',
    );

    $url_fields = array(
        '_mpb_hero_btn1_url',
        '_mpb_hero_btn2_url',
        '_mpb_cta_btn1_url',
        '_mpb_cta_btn2_url',
    );

    $int_fields = array(
        '_mpb_hero_image_id',
        '_mpb_intro_image_id',
        '_mpb_cta_image_id',
    );

    foreach ($text_fields as $key) {
        if (!array_key_exists($key, $_POST)) {
            continue;
        }

        update_post_meta($post_id, $key, sanitize_textarea_field(naigai_mpbfix_post_text($key, '')));
    }

    foreach ($url_fields as $key) {
        if (!array_key_exists($key, $_POST)) {
            continue;
        }

        $old_url = get_post_meta($post_id, $key, true);
        update_post_meta($post_id, $key, naigai_mpbfix_sanitize_minpaku_url_select(naigai_mpbfix_post_text($key, ''), $old_url));
    }

    foreach ($int_fields as $key) {
        if (!array_key_exists($key, $_POST)) {
            continue;
        }

        update_post_meta($post_id, $key, naigai_mpbfix_post_int($key, 0));
    }

    $posted_hero_gallery_ids = trim((string) naigai_mpbfix_post_text('_mpb_hero_gallery_ids', ''));
    $posted_hero_video_id    = naigai_mpbfix_post_int('_mpb_hero_video_mp4_id', 0);

    if ($posted_hero_video_id > 0 && $posted_hero_gallery_ids !== '') {
        update_post_meta($post_id, '_mpb_hero_gallery_ids', '');
    }

    update_post_meta($post_id, '_mpb_show_hero', naigai_mpbfix_post_bool('_mpb_show_hero', true));
    update_post_meta($post_id, '_mpb_show_intro', naigai_mpbfix_post_bool('_mpb_show_intro', true));
    update_post_meta($post_id, '_mpb_show_footer_nav', naigai_mpbfix_post_bool('_mpb_show_footer_nav', true));
    update_post_meta($post_id, '_mpb_hero_swiper_autoplay', naigai_mpbfix_post_bool('_mpb_hero_swiper_autoplay', true));
    update_post_meta($post_id, '_mpb_hero_swiper_pagination', naigai_mpbfix_post_bool('_mpb_hero_swiper_pagination', true));
    update_post_meta($post_id, '_mpb_hero_swiper_navigation', naigai_mpbfix_post_bool('_mpb_hero_swiper_navigation', false));
    update_post_meta($post_id, '_mpb_hero_swiper_loop', naigai_mpbfix_post_bool('_mpb_hero_swiper_loop', true));

    $feature_items = array();
    $old_feature_items = naigai_mpbfix_json_array($post_id, '_mpb_feature_items_json');

    for ($i = 0; $i < 50; $i++) {
        $item = array(
            'label'    => naigai_mpbfix_post_text("mpb_feature_{$i}_label", ''),
            'title'    => naigai_mpbfix_post_text("mpb_feature_{$i}_title", ''),
            'text'     => naigai_mpbfix_post_text("mpb_feature_{$i}_text", ''),
            'image_id' => naigai_mpbfix_post_int("mpb_feature_{$i}_image_id", 0),
        );

        $old_feature_item = isset($old_feature_items[$i]) && is_array($old_feature_items[$i]) ? $old_feature_items[$i] : array();
        $old_btn1 = !empty($old_feature_item['btn1']) && is_array($old_feature_item['btn1']) ? $old_feature_item['btn1'] : array();
        $old_btn2 = !empty($old_feature_item['btn2']) && is_array($old_feature_item['btn2']) ? $old_feature_item['btn2'] : array();

        $btn1_text = naigai_mpbfix_post_text("mpb_feature_{$i}_btn1_text", '');
        $btn1_url  = naigai_mpbfix_sanitize_minpaku_url_select(naigai_mpbfix_post_text("mpb_feature_{$i}_btn1_url", ''), $old_btn1['url'] ?? '');
        $btn2_text = naigai_mpbfix_post_text("mpb_feature_{$i}_btn2_text", '');
        $btn2_url  = naigai_mpbfix_sanitize_minpaku_url_select(naigai_mpbfix_post_text("mpb_feature_{$i}_btn2_url", ''), $old_btn2['url'] ?? '');

        if ($btn1_text !== '' || $btn1_url !== '') {
            $item['btn1'] = array('text' => $btn1_text, 'url' => $btn1_url);
        }

        if ($btn2_text !== '' || $btn2_url !== '') {
            $item['btn2'] = array('text' => $btn2_text, 'url' => $btn2_url);
        }

        if ($item['label'] !== '' || $item['title'] !== '' || $item['text'] !== '' || !empty($item['image_id']) || !empty($item['btn1']) || !empty($item['btn2'])) {
            $feature_items[] = $item;
        }
    }
    update_post_meta($post_id, '_mpb_feature_items_json', naigai_mpbfix_json_encode($feature_items));

    $guide_items = array();
    for ($i = 0; $i < 50; $i++) {
        $item = array(
            'title'    => naigai_mpbfix_post_text("mpb_guide_{$i}_title", ''),
            'text'     => naigai_mpbfix_post_text("mpb_guide_{$i}_text", ''),
            'image_id' => naigai_mpbfix_post_int("mpb_guide_{$i}_image_id", 0),
        );
        if ($item['title'] !== '' || $item['text'] !== '' || !empty($item['image_id'])) {
            $guide_items[] = $item;
        }
    }
    update_post_meta($post_id, '_mpb_guide_items_json', naigai_mpbfix_json_encode($guide_items));

    $flow_items = array();
    for ($i = 0; $i < 50; $i++) {
        $item = array(
            'title' => naigai_mpbfix_post_text("mpb_flow_{$i}_title", ''),
            'text'  => naigai_mpbfix_post_text("mpb_flow_{$i}_text", ''),
        );
        if ($item['title'] !== '' || $item['text'] !== '') {
            $flow_items[] = $item;
        }
    }
    update_post_meta($post_id, '_mpb_flow_items_json', naigai_mpbfix_json_encode($flow_items));

    $faq_items = array();
    for ($i = 0; $i < 50; $i++) {
        $item = array(
            'q' => naigai_mpbfix_post_text("mpb_faq_{$i}_q", ''),
            'a' => naigai_mpbfix_post_text("mpb_faq_{$i}_a", ''),
        );
        if ($item['q'] !== '' || $item['a'] !== '') {
            $faq_items[] = $item;
        }
    }
    update_post_meta($post_id, '_mpb_faq_items_json', naigai_mpbfix_json_encode($faq_items));

    /*
     * =========================================================
     * Compare table save
     * ---------------------------------------------------------
     * 保存先:
     * _mpb_compare_table_json
     *
     * 構造:
     * {
     *   "columns": [
     *     {"title": "民泊", "image_id": 0}
     *   ],
     *   "rows": [
     *     {
     *       "label": "向いている使い方",
     *       "cells": ["少人数向き", "グループ向き"]
     *     }
     *   ]
     * }
     *
     * 将来ビルダー対応:
     * - compare セクションはこのJSONだけを読めば表示できる
     * - セクションの並び順は将来 _mpb_sections_json 側で管理する
     * - ここでは比較表データの保存だけを担当する
     * =========================================================
     */
    $compare_columns = array();

    for ($i = 0; $i < 12; $i++) {
        $col = array(
            'title'    => naigai_mpbfix_post_text("mpb_compare_col_{$i}_title", ''),
            'image_id' => 0, // 比較表はテキスト専用。列画像は保存しない。
        );

        if ($col['title'] !== '' || $col['image_id'] > 0) {
            $compare_columns[] = $col;
        }
    }

    $compare_column_count = count($compare_columns);
    $compare_rows = array();

    for ($i = 0; $i < 50; $i++) {
        $label = naigai_mpbfix_post_text("mpb_compare_row_{$i}_label", '');
        $cells = array();
        $last_non_empty_cell_index = -1;

        for ($j = 0; $j < 12; $j++) {
            $cell = naigai_mpbfix_post_text("mpb_compare_row_{$i}_cell_{$j}", '');
            $cells[$j] = $cell;

            if ($cell !== '') {
                $last_non_empty_cell_index = $j;
            }
        }

        /*
         * 保存するセル数。
         *
         * 基本は保存済み列数に合わせる。
         * ただし、列タイトルを入れる前にセルだけ入力した場合も消さないように、
         * 入力済みセルの最後の位置までは残す。
         */
        $cell_keep_count = max($compare_column_count, $last_non_empty_cell_index + 1);
        $cells = array_slice($cells, 0, $cell_keep_count);

        $has_cell = false;
        foreach ($cells as $cell) {
            if ($cell !== '') {
                $has_cell = true;
                break;
            }
        }

        if ($label !== '' || $has_cell) {
            $compare_rows[] = array(
                'label' => $label,
                'cells' => $cells,
            );
        }
    }

    update_post_meta($post_id, '_mpb_compare_table_json', naigai_mpbfix_json_encode(array(
        'columns' => $compare_columns,
        'rows'    => $compare_rows,
    )));

}
add_action('save_post_page', 'naigai_mpbfix_save_meta_box', 20);
add_action('save_post', 'naigai_mpbfix_save_meta_box', 20);

/**
 * =========================================================
 * B2C admin save fix
 * - メディア選択JSを確実に読む
 * - Hero動画 / gallery などの保存を保険で固定
 * =========================================================
 */
if (!function_exists('naigai_mpb_is_b2c_template_target')) {
    function naigai_mpb_is_b2c_template_target($post_id = 0)
    {
        $post_id = absint($post_id);
        $saved_template = $post_id ? (string) get_page_template_slug($post_id) : '';
        $posted_template = isset($_POST['_wp_page_template'])
            ? sanitize_text_field(wp_unslash($_POST['_wp_page_template']))
            : '';

        return $saved_template === 'page-minpaku-b2c.php' || $posted_template === 'page-minpaku-b2c.php';
    }
}

if (!function_exists('naigai_mpb_force_admin_media_assets_fix')) {
    function naigai_mpb_force_admin_media_assets_fix($hook = '')
    {
        if (!is_admin()) {
            return;
        }

        $post_id = 0;
        if (isset($_GET['post'])) {
            $post_id = absint($_GET['post']);
        } elseif (isset($_POST['post_ID'])) {
            $post_id = absint($_POST['post_ID']);
        }

        if ($post_id <= 0 || !naigai_mpb_is_b2c_template_target($post_id)) {
            return;
        }

        wp_enqueue_media();

        $file = get_template_directory() . '/minpaku/admin/js/minpaku-b2c-admin-media.js';
        if (file_exists($file)) {
            wp_enqueue_script(
                'naigai-mpb-b2c-admin-media-fix',
                get_template_directory_uri() . '/minpaku/admin/js/minpaku-b2c-admin-media.js',
                array('jquery'),
                (string) filemtime($file),
                true
            );
        }
    }
    add_action('admin_enqueue_scripts', 'naigai_mpb_force_admin_media_assets_fix', 99);
}

if (!function_exists('naigai_mpb_force_save_media_meta_fix')) {
    function naigai_mpb_force_save_media_meta_fix($post_id, $post = null, $update = false)
    {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (wp_is_post_revision($post_id)) {
            return;
        }

        if (get_post_type($post_id) !== 'page') {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        if (!naigai_mpb_is_b2c_template_target($post_id)) {
            return;
        }

        $int_fields = array(
            '_mpb_hero_video_mp4_id',
            '_mpb_intro_image_id',
            '_mpb_cta_image_id',
        );

        foreach ($int_fields as $meta_key) {
            $posted_value = null;

            foreach (array($meta_key, ltrim($meta_key, '_')) as $field_name) {
                if (isset($_POST[$field_name])) {
                    $posted_value = absint(wp_unslash($_POST[$field_name]));
                    break;
                }
            }

            if ($posted_value === null) {
                continue;
            }

            if ($posted_value > 0) {
                update_post_meta($post_id, $meta_key, $posted_value);
            } else {
                delete_post_meta($post_id, $meta_key);
            }
        }

        $csv_fields = array(
            '_mpb_hero_gallery_ids',
        );

        foreach ($csv_fields as $meta_key) {
            $posted_value = null;

            foreach (array($meta_key, ltrim($meta_key, '_')) as $field_name) {
                if (isset($_POST[$field_name])) {
                    $posted_value = (string) wp_unslash($_POST[$field_name]);
                    break;
                }
            }

            if ($posted_value === null) {
                continue;
            }

            $ids = array_values(array_filter(array_map('absint', preg_split('/[\s,]+/', $posted_value))));
            if (!empty($ids)) {
                update_post_meta($post_id, $meta_key, implode(',', $ids));
            } else {
                delete_post_meta($post_id, $meta_key);
            }
        }

        $checkbox_fields = array(
            '_mpb_show_hero',
        );

        foreach ($checkbox_fields as $meta_key) {
            $seen = false;
            $value = 0;

            foreach (array($meta_key, ltrim($meta_key, '_')) as $field_name) {
                if (isset($_POST[$field_name])) {
                    $seen = true;
                    $raw = wp_unslash($_POST[$field_name]);
                    $value = ($raw === '1' || $raw === 'on' || $raw === 'true') ? 1 : 0;
                    break;
                }
            }

            if ($seen) {
                update_post_meta($post_id, $meta_key, $value);
            }
        }
    }
    add_action('save_post_page', 'naigai_mpb_force_save_media_meta_fix', 99, 3);
}


if (!function_exists('naigai_mpbfix_save_cta_media_controls')) {
    function naigai_mpbfix_save_cta_media_controls($post_id, $post = null, $update = false)
    {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (wp_is_post_revision($post_id)) {
            return;
        }

        if (get_post_type($post_id) !== 'page') {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        if (function_exists('naigai_mpb_is_b2c_template_target') && !naigai_mpb_is_b2c_template_target($post_id)) {
            return;
        }

        /*
         * CTA欄がPOSTされていない保存では、既存CTAメタを壊さない。
         */
        $cta_posted = false;

        foreach ($_POST as $key => $unused) {
            if (is_string($key) && strpos($key, '_mpb_cta_') === 0) {
                $cta_posted = true;
                break;
            }
        }

        if (!$cta_posted) {
            return;
        }

        /*
         * POSTに存在する時だけ更新。
         * 存在しない項目を delete しない。
         */
        if (array_key_exists('_mpb_cta_gallery_ids', $_POST)) {
            $raw = trim((string) wp_unslash($_POST['_mpb_cta_gallery_ids']));
            $ids = array();

            foreach (preg_split('/[\s,]+/', $raw) as $id) {
                $id = absint($id);
                if ($id > 0) {
                    $ids[] = $id;
                }
            }

            update_post_meta($post_id, '_mpb_cta_gallery_ids', implode(',', $ids));
        }

        foreach (array('_mpb_cta_video_mp4_id', '_mpb_cta_swiper_delay') as $key) {
            if (!array_key_exists($key, $_POST)) {
                continue;
            }

            update_post_meta($post_id, $key, (string) absint(wp_unslash($_POST[$key])));
        }

        /*
         * チェックボックスはCTA欄がPOSTされた時だけ 1/0 保存。
         */
        foreach (array(
            '_mpb_cta_show_media',
            '_mpb_cta_media_autoplay',
            '_mpb_cta_video_controls',
            '_mpb_cta_swiper_pagination',
            '_mpb_cta_swiper_navigation',
        ) as $key) {
            update_post_meta($post_id, $key, isset($_POST[$key]) ? '1' : '0');
        }
    }

    add_action('save_post_page', 'naigai_mpbfix_save_cta_media_controls', 120, 3);
}




/**
 * B2C以外の固定ページに民泊B2C設定が出た場合、最後に強制除去する。
 * add_meta_box登録漏れ対策の保険。
 */
if (!function_exists('naigai_mpbfix_remove_b2c_box_outside_target')) {
    function naigai_mpbfix_remove_b2c_box_outside_target($post_type, $post)
    {
        if ($post_type !== 'page' || naigai_mpbfix_is_b2c_admin_page($post)) {
            return;
        }

        global $wp_meta_boxes;

        if (empty($wp_meta_boxes['page']) || !is_array($wp_meta_boxes['page'])) {
            return;
        }

        foreach ($wp_meta_boxes['page'] as $context => $priorities) {
            if (!is_array($priorities)) {
                continue;
            }

            foreach ($priorities as $priority => $boxes) {
                if (!is_array($boxes)) {
                    continue;
                }

                foreach ($boxes as $id => $box) {
                    $title = isset($box['title']) ? wp_strip_all_tags((string) $box['title']) : '';
                    if (strpos($id, 'mpb') !== false || strpos($id, 'minpaku') !== false || strpos($title, '民泊B2C') !== false) {
                        unset($wp_meta_boxes['page'][$context][$priority][$id]);
                    }
                }
            }
        }
    }

    add_action('add_meta_boxes', 'naigai_mpbfix_remove_b2c_box_outside_target', PHP_INT_MAX, 2);
}


if (!function_exists('naigai_mpbfix_enqueue_layout_sortable_admin')) {
    function naigai_mpbfix_enqueue_layout_sortable_admin($hook)
    {
        if ($hook !== 'post.php' && $hook !== 'post-new.php') {
            return;
        }

        $post_id = 0;

        if (isset($_GET['post'])) {
            $post_id = (int) $_GET['post'];
        } elseif (isset($_POST['post_ID'])) {
            $post_id = (int) $_POST['post_ID'];
        }

        if ($post_id <= 0) {
            return;
        }

        if ((string) get_page_template_slug($post_id) !== 'page-minpaku-b2c.php') {
            return;
        }

        wp_enqueue_script('jquery-ui-sortable');
    }

    add_action('admin_enqueue_scripts', 'naigai_mpbfix_enqueue_layout_sortable_admin');
}

if (!function_exists('naigai_mpbfix_layout_sortable_admin_footer')) {
    function naigai_mpbfix_layout_sortable_admin_footer()
    {
        global $post;

        if (!($post instanceof WP_Post)) {
            return;
        }

        if ((string) get_page_template_slug($post->ID) !== 'page-minpaku-b2c.php') {
            return;
        }
        ?>
        <style>
          .mpbfix-sortable-list {
            display: grid;
            gap: 10px;
            margin-top: 14px;
          }

          .mpbfix-layout-item {
            border: 1px solid #dcdcde;
            border-radius: 10px;
            background: #fff;
          }

          .mpbfix-layout-item[open] {
            border-color: #2271b1;
            background: #f8fbff;
          }

          .mpbfix-layout-summary {
            display: grid;
            grid-template-columns: 42px minmax(160px, 1fr) auto;
            gap: 12px;
            align-items: center;
            padding: 14px 16px;
            cursor: pointer;
            list-style: none;
          }

          .mpbfix-layout-summary::-webkit-details-marker {
            display: none;
          }

          .mpbfix-drag-handle {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            border: 1px solid #c3c4c7;
            border-radius: 8px;
            background: #f6f7f7;
            cursor: grab;
            font-size: 18px;
            line-height: 1;
            user-select: none;
          }

          .mpbfix-drag-handle:active {
            cursor: grabbing;
          }

          .mpbfix-layout-title {
            font-size: 15px;
          }

          .mpbfix-layout-enabled,
          .mpbfix-layout-cta-mode {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
          }

          .mpbfix-layout-body {
            padding: 0 16px 16px 70px;
            border-top: 1px solid #dcdcde;
          }

          .mpbfix-sortable-placeholder {
            min-height: 58px;
            border: 2px dashed #72aee6;
            border-radius: 10px;
            background: #f0f6fc;
          }

          .mpbfix-layout-item.is-dragging {
            opacity: .72;
          }

          .mpbfix-builder-card,
          .mpbfix-builder-item,

          @media (max-width: 900px) {
            .mpbfix-layout-summary {
              grid-template-columns: 42px 1fr;
            }

            .mpbfix-layout-enabled {
              grid-column: 2 / -1;
            }

            .mpbfix-layout-body {
              padding-left: 16px;
            }
          }
        
          /* B2C SORTABLE ROW HANDLE FIX */
          .mpbfix-layout-summary {
            cursor: grab;
          }

          .mpbfix-layout-summary:active {
            cursor: grabbing;
          }

          .mpbfix-layout-summary input,
          .mpbfix-layout-summary select,
          .mpbfix-layout-summary label,
          .mpbfix-layout-summary button {
            cursor: auto;
          }

          .mpbfix-drag-handle {
            pointer-events: none;
          }
        
          /* B2C SUMMARY ROW DRAG FINAL */
          .mpbfix-layout-summary {
            cursor: grab !important;
            user-select: none;
          }

          .mpbfix-layout-summary:active {
            cursor: grabbing !important;
          }

          .mpbfix-layout-summary input,
          .mpbfix-layout-summary select,
          .mpbfix-layout-summary label,
          .mpbfix-layout-summary button {
            cursor: auto !important;
            user-select: auto;
          }

          .mpbfix-layout-item.is-dragging .mpbfix-layout-summary {
            cursor: grabbing !important;
          }
        
          /* B2C CTA MODE RADIO FINAL */
          .mpbfix-layout-cta-mode {
            display: inline-flex !important;
            align-items: center;
            gap: 10px;
            white-space: nowrap;
            padding: 4px 8px;
            border: 1px solid #c3c4c7;
            border-radius: 8px;
            background: #fff;
          }

          .mpbfix-layout-cta-mode__label {
            font-weight: 700;
          }

          .mpbfix-layout-cta-mode label {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            margin: 0;
          }

          .mpbfix-layout-summary {
            cursor: grab !important;
            user-select: none;
          }

          .mpbfix-layout-summary:active {
            cursor: grabbing !important;
          }

          .mpbfix-layout-summary input,
          .mpbfix-layout-summary select,
          .mpbfix-layout-summary label,
          .mpbfix-layout-cta-mode {
            cursor: auto !important;
            user-select: auto;
          }
        
          /* B2C CTA MODE RADIO ADMIN */
          .mpbfix-layout-cta-mode {
            display: inline-flex !important;
            align-items: center;
            gap: 10px;
            padding: 4px 8px;
            border: 1px solid #c3c4c7;
            border-radius: 8px;
            background: #fff;
            white-space: nowrap;
          }

          .mpbfix-layout-cta-mode__label {
            font-weight: 700;
          }

          .mpbfix-layout-cta-mode label {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            margin: 0;
          }
        </style>

        <script>
        jQuery(function ($) {
          var $list = $('#mpbfix-sortable-sections');

          if (!$list.length || !$.fn.sortable) {
            return;
          }

          $list.sortable({
            handle: '.mpbfix-layout-summary',
            cancel: 'input, select, textarea, button, label, .mpbfix-layout-body, .mpbfix-layout-cta-mode',
            items: '> .mpbfix-layout-item',
            placeholder: 'mpbfix-sortable-placeholder',
            forcePlaceholderSize: true,
            axis: 'y',
            distance: 5,
            tolerance: 'pointer',
            start: function (event, ui) {
              ui.item.addClass('is-dragging');
            },
            stop: function (event, ui) {
              ui.item.removeClass('is-dragging');
            }
          });
        });
        </script>
        <?php
    }

    add_action('admin_footer-post.php', 'naigai_mpbfix_layout_sortable_admin_footer', 99);
    add_action('admin_footer-post-new.php', 'naigai_mpbfix_layout_sortable_admin_footer', 99);
}


/**
 * B2C CTA表示内容を明示保存する。
 * [使う] チェックとは別に、CTAの中身を通常CTA / コンタクトフォームで切り替える。
 */
if (!function_exists('naigai_mpbfix_save_cta_mode_direct')) {
    function naigai_mpbfix_save_cta_mode_direct($post_id)
    {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (get_post_type($post_id) !== 'page') {
            return;
        }

        if ((string) get_page_template_slug($post_id) !== 'page-minpaku-b2c.php') {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        if (!isset($_POST['_mpb_cta_mode'])) {
            return;
        }

        $mode = sanitize_key((string) wp_unslash($_POST['_mpb_cta_mode']));

        if (!in_array($mode, array('cta', 'form'), true)) {
            $mode = 'cta';
        }

        update_post_meta($post_id, '_mpb_cta_mode', $mode);
    }

    add_action('save_post_page', 'naigai_mpbfix_save_cta_mode_direct', 99);
}


/**
 * B2C CTA fields final save guard.
 *
 * 目的:
 * - CTA入力欄がPOSTされている時だけ保存する
 * - POSTに無い時は既存値を空で上書きしない
 * - CTA画像/動画/ギャラリー/ボタン/表示内容をまとめて保存する
 */
if (!function_exists('naigai_mpbfix_save_cta_fields_no_clear_final')) {
    function naigai_mpbfix_save_cta_fields_no_clear_final($post_id)
    {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (get_post_type($post_id) !== 'page') {
            return;
        }

        if ((string) get_page_template_slug($post_id) !== 'page-minpaku-b2c.php') {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        $cta_posted = false;

        foreach ($_POST as $key => $unused) {
            if (is_string($key) && strpos($key, '_mpb_cta_') === 0) {
                $cta_posted = true;
                break;
            }
        }

        if (!$cta_posted) {
            return;
        }

        $text_keys = array(
            '_mpb_cta_title',
            '_mpb_cta_text',
            '_mpb_cta_btn1_text',
            '_mpb_cta_btn2_text',
        );

        foreach ($text_keys as $key) {
            if (!array_key_exists($key, $_POST)) {
                continue;
            }

            update_post_meta(
                $post_id,
                $key,
                sanitize_textarea_field((string) wp_unslash($_POST[$key]))
            );
        }

        $url_keys = array(
            '_mpb_cta_btn1_url',
            '_mpb_cta_btn2_url',
        );

        foreach ($url_keys as $key) {
            if (!array_key_exists($key, $_POST)) {
                continue;
            }

            $raw = sanitize_text_field((string) wp_unslash($_POST[$key]));
            $old = get_post_meta($post_id, $key, true);

            if (function_exists('naigai_mpbfix_sanitize_minpaku_url_select')) {
                $value = naigai_mpbfix_sanitize_minpaku_url_select($raw, $old);
            } else {
                $value = esc_url_raw($raw);
            }

            update_post_meta($post_id, $key, $value);
        }

        $int_keys = array(
            '_mpb_cta_image_id',
            '_mpb_cta_video_mp4_id',
        );

        foreach ($int_keys as $key) {
            if (!array_key_exists($key, $_POST)) {
                continue;
            }

            update_post_meta($post_id, $key, (string) absint(wp_unslash($_POST[$key])));
        }

        if (array_key_exists('_mpb_cta_gallery_ids', $_POST)) {
            $raw_gallery = sanitize_text_field((string) wp_unslash($_POST['_mpb_cta_gallery_ids']));
            $ids = array();

            foreach (explode(',', $raw_gallery) as $id) {
                $id = absint(trim($id));
                if ($id > 0) {
                    $ids[] = $id;
                }
            }

            update_post_meta($post_id, '_mpb_cta_gallery_ids', implode(',', $ids));
        }

        if (array_key_exists('_mpb_cta_mode', $_POST)) {
            $mode = sanitize_key((string) wp_unslash($_POST['_mpb_cta_mode']));

            if (!in_array($mode, array('cta', 'form'), true)) {
                $mode = 'cta';
            }

            update_post_meta($post_id, '_mpb_cta_mode', $mode);
        }

        /*
         * チェックボックスはPOSTされない時に0扱い。
         * ただし、CTA欄自体がPOSTされている時だけ処理する。
         */
        $checkbox_keys = array(
            '_mpb_cta_show_media',
            '_mpb_cta_media_autoplay',
            '_mpb_cta_video_controls',
            '_mpb_cta_swiper_pagination',
            '_mpb_cta_swiper_navigation',
        );

        foreach ($checkbox_keys as $key) {
            update_post_meta($post_id, $key, isset($_POST[$key]) ? '1' : '0');
        }

        if (array_key_exists('_mpb_cta_swiper_delay', $_POST)) {
            $delay = absint(wp_unslash($_POST['_mpb_cta_swiper_delay']));
            update_post_meta($post_id, '_mpb_cta_swiper_delay', (string) max(0, $delay));
        }
    }

    add_action('save_post_page', 'naigai_mpbfix_save_cta_fields_no_clear_final', 150);
}


/**
 * =========================================================
 * B2C CTA media save finalizer
 * =========================================================
 *
 * 役割:
 * - CTA画像 / CTAギャラリー / CTA mp4 の保存を最後に正規化する
 * - 管理画面で選んだ hidden input の値を優先して保存する
 * - ただし _mpb_show_cta はここでは触らない
 *
 * 理由:
 * - _mpb_show_cta はページ全体のCTA表示ON/OFF
 * - CTAメディア保存処理で勝手に0へ落とすと、CTA全体が消える
 */
if (!function_exists('naigai_mpbfix_save_cta_media_finalizer')) {
    function naigai_mpbfix_save_cta_media_finalizer($post_id)
    {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (defined('DOING_AJAX') && DOING_AJAX) {
            return;
        }

        if (wp_is_post_revision($post_id) || wp_is_post_autosave($post_id)) {
            return;
        }

        if (get_post_type($post_id) !== 'page') {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        $saved_template = (string) get_page_template_slug($post_id);
        $posted_template = isset($_POST['page_template'])
            ? sanitize_text_field((string) wp_unslash($_POST['page_template']))
            : '';

        if ($saved_template !== 'page-minpaku-b2c.php' && $posted_template !== 'page-minpaku-b2c.php') {
            return;
        }

        /*
         * CTA mode
         */
        if (array_key_exists('_mpb_cta_mode', $_POST)) {
            $cta_mode = sanitize_text_field((string) wp_unslash($_POST['_mpb_cta_mode']));
            if ($cta_mode === '' || !in_array($cta_mode, array('cta', 'form'), true)) {
                $cta_mode = 'cta';
            }
            update_post_meta($post_id, '_mpb_cta_mode', $cta_mode);
        }

        /*
         * CTA media IDs
         */
        if (array_key_exists('_mpb_cta_image_id', $_POST)) {
            update_post_meta($post_id, '_mpb_cta_image_id', absint($_POST['_mpb_cta_image_id']));
        }

        if (array_key_exists('_mpb_cta_video_mp4_id', $_POST)) {
            update_post_meta($post_id, '_mpb_cta_video_mp4_id', absint($_POST['_mpb_cta_video_mp4_id']));
        }

        if (array_key_exists('_mpb_cta_gallery_ids', $_POST)) {
            $raw = trim((string) wp_unslash($_POST['_mpb_cta_gallery_ids']));
            $ids = array_filter(array_map('absint', preg_split('/[,\s]+/', $raw)));
            $ids = array_values(array_unique($ids));
            update_post_meta($post_id, '_mpb_cta_gallery_ids', implode(',', $ids));
        }

        /*
         * CTAメディア設定
         *
         * ここでは CTAメディア欄の設定だけ扱う。
         * _mpb_show_cta は触らない。
         */
        $media_bool_keys = array(
            '_mpb_cta_show_media',
            '_mpb_cta_media_autoplay',
            '_mpb_cta_video_controls',
            '_mpb_cta_swiper_pagination',
            '_mpb_cta_swiper_navigation',
        );

        foreach ($media_bool_keys as $key) {
            update_post_meta($post_id, $key, isset($_POST[$key]) ? '1' : '0');
        }

        if (array_key_exists('_mpb_cta_swiper_delay', $_POST)) {
            $delay = absint($_POST['_mpb_cta_swiper_delay']);
            update_post_meta($post_id, '_mpb_cta_swiper_delay', $delay > 0 ? (string) $delay : '5000');
        }
    }

    add_action('save_post_page', 'naigai_mpbfix_save_cta_media_finalizer', 999999);
}


