<?php
if (!defined("ABSPATH")) {
    exit;
}

/**
 * =========================================================
 * Hub context を返す
 * =========================================================
 *
 * 戻り値:
 * - front
 * - construction
 * - realestate
 * - ""
 */
function naigai_get_hub_context($post_id = 0)
{
    if ($post_id <= 0) {
        $post_id = get_queried_object_id();
    }

    $post_id = (int) $post_id;
    if ($post_id <= 0) {
        return "";
    }

    $front_page_id = (int) get_option("page_on_front");
    if ($front_page_id > 0 && $front_page_id === $post_id) {
        return "front";
    }

    $template = get_page_template_slug($post_id);

    if ($template === "template-construction-hub.php") {
        return "construction";
    }

    if ($template === "template-realestate-hub.php") {
        return "realestate";
    }

    return "";
}

/**
 * =========================================================
 * Hub ページか
 * =========================================================
 */
function naigai_is_hub_page($post_id = 0)
{
    return naigai_get_hub_context($post_id) !== "";
}

/**
 * =========================================================
 * body_class に Hub 用クラス追加
 * =========================================================
 */
function naigai_add_hub_body_classes($classes)
{
    $context = naigai_get_hub_context();

    if ($context === "") {
        return $classes;
    }

    $classes[] = "hub-page";
    $classes[] = "hub-context-" . $context;

    return $classes;
}
add_filter("body_class", "naigai_add_hub_body_classes");
