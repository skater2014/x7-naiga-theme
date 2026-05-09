<?php
if (!defined('ABSPATH')) {
    exit;
}

function naigai_migrate_log($message)
{
    echo $message . PHP_EOL;
}

function naigai_migrate_option_key($group, $version)
{
    return 'naigai_migrate_' . sanitize_key($group) . '_' . sanitize_key($version) . '_done';
}

function naigai_migrate_has_run($group, $version)
{
    return (bool) get_option(naigai_migrate_option_key($group, $version), false);
}

function naigai_migrate_mark_done($group, $version)
{
    update_option(naigai_migrate_option_key($group, $version), 1, false);
}

function naigai_migrate_set_if_empty($post_id, $key, $value)
{
    $current = get_post_meta((int) $post_id, $key, true);

    if ($current === '' || $current === null) {
        update_post_meta((int) $post_id, $key, $value);
        naigai_migrate_log("set post={$post_id} {$key}");
        return true;
    }

    naigai_migrate_log("keep post={$post_id} {$key}");
    return false;
}

function naigai_migrate_set_option_if_empty($key, $value)
{
    $current = get_option($key, null);

    if ($current === '' || $current === null || $current === false) {
        update_option($key, $value, false);
        naigai_migrate_log("set option={$key}");
        return true;
    }

    naigai_migrate_log("keep option={$key}");
    return false;
}

function naigai_migrate_page_id($slug)
{
    if ($slug === '') {
        return 0;
    }

    $page = get_page_by_path($slug, OBJECT, 'page');
    return $page ? (int) $page->ID : 0;
}

function naigai_migrate_post_id_by_slug($slug, $post_type = 'page')
{
    $post = get_page_by_path($slug, OBJECT, $post_type);
    return $post ? (int) $post->ID : 0;
}
