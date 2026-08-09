<?php

/*
 * 固定ページ編集画面だけに共通管理CSSを読み込む。
 * 投稿・CPT・フロント画面には影響させない。
 */

if (!function_exists("naigai_enqueue_page_editor_admin_css")) {
    function naigai_enqueue_page_editor_admin_css($hook) {
        if ($hook !== "post.php" && $hook !== "post-new.php") {
            return;
        }

        $screen = get_current_screen();

        if (!$screen || $screen->post_type !== "page") {
            return;
        }

        $relative = "/css/admin-page-editor.css";
        $file = get_template_directory() . $relative;

        if (!file_exists($file)) {
            return;
        }

        wp_enqueue_style(
            "naigai-admin-page-editor",
            get_template_directory_uri() . $relative,
            array(),
            (string) filemtime($file)
        );
    }

    add_action(
        "admin_enqueue_scripts",
        "naigai_enqueue_page_editor_admin_css",
        100
    );
}
