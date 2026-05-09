<?php
/**
 * =========================================================
 * iezukuri/inc/hero.php
 *
 * 役割:
 * - 家づくりページの Hero メタ取得を一元化
 * - DBを勝手に上書きしない
 * - 新メタ _iez_* を優先し、旧 _ch_* / _hub_* をfallbackで読む
 * =========================================================
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('naigai_iezukuri_raw_meta')) {
    function naigai_iezukuri_raw_meta($post_id, $key)
    {
        global $wpdb;

        $value = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT meta_value FROM {$wpdb->postmeta}
                 WHERE post_id = %d AND meta_key = %s
                 ORDER BY meta_id DESC
                 LIMIT 1",
                (int) $post_id,
                (string) $key
            )
        );

        return is_string($value) ? maybe_unserialize($value) : $value;
    }
}

if (!function_exists('naigai_iezukuri_is_page_id')) {
    function naigai_iezukuri_is_page_id($post_id)
    {
        $post = get_post($post_id);

        if (!$post || $post->post_type !== 'page') {
            return false;
        }

        if ($post->post_name === 'iezukuri') {
            return true;
        }

        foreach (get_post_ancestors($post) as $ancestor_id) {
            if (get_post_field('post_name', $ancestor_id) === 'iezukuri') {
                return true;
            }
        }

        return false;
    }
}

if (!function_exists('naigai_iezukuri_csv_ids')) {
    function naigai_iezukuri_csv_ids($value)
    {
        if (is_array($value)) {
            $value = implode(',', $value);
        }

        $ids = preg_split('/[,\s]+/', (string) $value);
        $ids = array_filter(array_map('absint', $ids));

        return array_values(array_unique($ids));
    }
}

if (!function_exists('naigai_iezukuri_first_image_id')) {
    function naigai_iezukuri_first_image_id($post_id)
    {
        $single_keys = array(
            '_iez_hero_image_id',
            '_ch_subpage_hero_image_id',
            '_ch_hero_image_id',
            '_hub_ch_hero_image_id',
            '_hub_hero_image_id',
        );

        foreach ($single_keys as $key) {
            $id = absint(naigai_iezukuri_raw_meta($post_id, $key));
            if ($id > 0) {
                return $id;
            }
        }

        $gallery_keys = array(
            '_iez_hero_gallery_ids',
            '_hub_ch_hero_gallery_ids',
            '_hub_hero_gallery_ids',
            '_hub_hero_image_ids',
        );

        foreach ($gallery_keys as $key) {
            $ids = naigai_iezukuri_csv_ids(naigai_iezukuri_raw_meta($post_id, $key));
            if (!empty($ids)) {
                return (int) $ids[0];
            }
        }

        $thumb_id = absint(naigai_iezukuri_raw_meta($post_id, '_thumbnail_id'));
        return $thumb_id > 0 ? $thumb_id : 0;
    }
}

if (!function_exists('naigai_iezukuri_gallery_ids')) {
    function naigai_iezukuri_gallery_ids($post_id)
    {
        $gallery_keys = array(
            '_iez_hero_gallery_ids',
            '_hub_ch_hero_gallery_ids',
            '_hub_hero_gallery_ids',
            '_hub_hero_image_ids',
        );

        foreach ($gallery_keys as $key) {
            $ids = naigai_iezukuri_csv_ids(naigai_iezukuri_raw_meta($post_id, $key));
            if (!empty($ids)) {
                return $ids;
            }
        }

        $image_id = naigai_iezukuri_first_image_id($post_id);
        return $image_id ? array($image_id) : array();
    }
}

if (!function_exists('naigai_iezukuri_text_meta')) {
    function naigai_iezukuri_text_meta($post_id, $keys, $default = '')
    {
        foreach ((array) $keys as $key) {
            $value = naigai_iezukuri_raw_meta($post_id, $key);
            if (is_string($value) && trim($value) !== '') {
                return $value;
            }
        }

        return $default;
    }
}

/**
 * 既存テンプレートが古いメタキーを見ていても、
 * DBを上書きせずに fallback 値を返す。
 */
add_filter('get_post_metadata', function ($value, $object_id, $meta_key, $single) {
    static $running = false;

    if ($running || !naigai_iezukuri_is_page_id($object_id)) {
        return $value;
    }

    $target_keys = array(
        '_ch_subpage_hero_image_id',
        '_ch_hero_image_id',
        '_hub_ch_hero_image_id',
        '_hub_hero_image_id',
        '_hub_ch_hero_gallery_ids',
        '_hub_hero_gallery_ids',
        '_hub_hero_image_ids',
    );

    if (!in_array($meta_key, $target_keys, true)) {
        return $value;
    }

    $running = true;
    $actual = naigai_iezukuri_raw_meta($object_id, $meta_key);
    $running = false;

    if ($actual !== null && $actual !== '' && $actual !== '0' && $actual !== 0) {
        return $value;
    }

    if (strpos($meta_key, 'gallery') !== false || $meta_key === '_hub_hero_image_ids') {
        $ids = naigai_iezukuri_gallery_ids($object_id);
        $fallback = implode(',', $ids);
    } else {
        $fallback = naigai_iezukuri_first_image_id($object_id);
    }

    if (!$fallback) {
        return $value;
    }

    return $single ? $fallback : array($fallback);
}, 10, 4);

/**
 * body class 補正。
 * 既存CSSが .hub-customhome-subpage--{slug} を前提にしているため。
 */
add_filter('body_class', function ($classes) {
    if (!is_page()) {
        return $classes;
    }

    $post = get_post();

    if (!$post) {
        return $classes;
    }

    if ($post->post_name === 'iezukuri') {
        $classes[] = 'hub-customhome-page';
    }

    foreach (get_post_ancestors($post) as $ancestor_id) {
        if (get_post_field('post_name', $ancestor_id) === 'iezukuri') {
            $classes[] = 'hub-customhome-subpage';
            $classes[] = 'hub-customhome-subpage--' . sanitize_html_class($post->post_name);
            break;
        }
    }

    return array_values(array_unique($classes));
});
