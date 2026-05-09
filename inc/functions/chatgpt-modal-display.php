<?php
if (!defined('ABSPATH')) exit;

if (!defined('NAIGAI_CHATGPT_DISPLAY_OPT')) {
    define('NAIGAI_CHATGPT_DISPLAY_OPT', 'naigai_chatgpt_display_opt');
}

function naigai_chatgpt_display_get() {
    $saved = get_option(NAIGAI_CHATGPT_DISPLAY_OPT, array());
    if (!is_array($saved)) $saved = array();

    $settings = array_merge(array(
        'routes' => array('front'),
    ), $saved);

    if (!isset($settings['routes']) || !is_array($settings['routes'])) {
        $settings['routes'] = array('front');
    }

    $settings['routes'] = array_values(array_unique(array_filter(array_map('sanitize_text_field', $settings['routes']))));
    return $settings;
}

function naigai_chatgpt_should_display() {
    if (is_admin()) return false;

    $routes = naigai_chatgpt_display_get()['routes'];

    if (in_array('off', $routes, true)) return false;
    if (in_array('all', $routes, true)) return true;

    if (in_array('front', $routes, true) && is_front_page()) return true;
    if (in_array('blog_index', $routes, true) && is_home()) return true;

    $post_id = get_queried_object_id();
    if ($post_id && in_array('page:' . $post_id, $routes, true)) return true;

    foreach ($routes as $route) {
        if (strpos($route, 'singular:') === 0) {
            $post_type = substr($route, 9);
            if ($post_type && is_singular($post_type)) return true;
        }

        if (strpos($route, 'archive:') === 0) {
            $post_type = substr($route, 8);
            if ($post_type === 'post' && is_home()) return true;
            if ($post_type && is_post_type_archive($post_type)) return true;
        }

        if (strpos($route, 'tax:') === 0) {
            $tax = substr($route, 4);
            if ($tax === 'category' && is_category()) return true;
            if ($tax === 'post_tag' && is_tag()) return true;
            if ($tax && is_tax($tax)) return true;
        }
    }

    return false;
}

if (!function_exists('naigai_should_show_chatgpt_modal')) {
    function naigai_should_show_chatgpt_modal() {
        return naigai_chatgpt_should_display();
    }
}

function naigai_chatgpt_first_post_link($post_type) {
    $q = get_posts(array(
        'post_type'      => $post_type,
        'post_status'    => 'publish',
        'posts_per_page' => 1,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'fields'         => 'ids',
    ));

    return !empty($q[0]) ? get_permalink($q[0]) : '';
}

function naigai_chatgpt_archive_link($post_type) {
    if ($post_type === 'post') {
        $posts_page = (int) get_option('page_for_posts');
        return $posts_page ? get_permalink($posts_page) : home_url('/');
    }

    $url = get_post_type_archive_link($post_type);
    return $url ? $url : '';
}

function naigai_chatgpt_first_term_link($tax) {
    $terms = get_terms(array(
        'taxonomy'   => $tax,
        'hide_empty' => false,
        'number'     => 1,
    ));

    if (is_wp_error($terms) || empty($terms)) return '';

    $url = get_term_link($terms[0]);
    return is_wp_error($url) ? '' : $url;
}

function naigai_chatgpt_page_depth($page) {
    $depth = 0;
    $parent = (int) $page->post_parent;

    while ($parent > 0) {
        $depth++;
        $parent = (int) get_post_field('post_parent', $parent);
    }

    return $depth;
}

function naigai_chatgpt_display_checkbox($route, $label, $routes, $note = '', $preview_url = '', $admin_url = '') {
    ?>
    <div style="margin:0 0 12px; padding:8px 10px; border-bottom:1px solid #f0f0f1;">
        <label style="display:block; margin:0 0 6px;">
            <input
                type="checkbox"
                name="routes[]"
                value="<?php echo esc_attr($route); ?>"
                <?php checked(in_array($route, $routes, true)); ?>
            >
            <strong><?php echo esc_html($label); ?></strong>

            <?php if ($note !== '') : ?>
                <span style="color:#666; margin-left:6px;"><?php echo esc_html($note); ?></span>
            <?php endif; ?>
        </label>

        <?php if ($preview_url) : ?>
            <div style="margin:4px 0 0 24px;">
                <a class="button button-small" href="<?php echo esc_url($preview_url); ?>" target="_blank" rel="noopener">表示確認</a>
                <span style="margin-left:8px;">URL:</span>
                <a href="<?php echo esc_url($preview_url); ?>" target="_blank" rel="noopener">
                    <code><?php echo esc_html($preview_url); ?></code>
                </a>
            </div>
        <?php endif; ?>

        <?php if ($admin_url) : ?>
            <div style="margin:4px 0 0 24px;">
                <a class="button button-small" href="<?php echo esc_url($admin_url); ?>" target="_blank" rel="noopener">管理</a>
                <span style="margin-left:8px;">管理URL:</span>
                <a href="<?php echo esc_url($admin_url); ?>" target="_blank" rel="noopener">
                    <code><?php echo esc_html($admin_url); ?></code>
                </a>
            </div>
        <?php endif; ?>
    </div>
    <?php
}

add_action('admin_menu', function () {
    add_menu_page(
        'ChatGPTモーダル表示設定',
        'ChatGPTモーダル',
        'manage_options',
        'naigai-chatgpt-modal',
        'naigai_chatgpt_display_page',
        'dashicons-format-chat',
        58
    );
});

add_action('admin_init', function () {
    if (!isset($_POST['naigai_chatgpt_save'])) return;
    if (!current_user_can('manage_options')) return;

    check_admin_referer('naigai_chatgpt_display');

    $routes = isset($_POST['routes']) ? (array) wp_unslash($_POST['routes']) : array();
    $routes = array_values(array_unique(array_filter(array_map('sanitize_text_field', $routes))));

    if (empty($routes)) $routes = array('off');
    if (in_array('off', $routes, true)) $routes = array('off');

    update_option(NAIGAI_CHATGPT_DISPLAY_OPT, array('routes' => $routes));

    wp_safe_redirect(admin_url('admin.php?page=naigai-chatgpt-modal&updated=1'));
    exit;
});

function naigai_chatgpt_display_page() {
    if (!current_user_can('manage_options')) return;

    $routes = naigai_chatgpt_display_get()['routes'];

    $pages = get_pages(array(
        'post_status' => 'publish',
        'sort_column' => 'menu_order,post_title',
        'sort_order'  => 'ASC',
    ));

    $post_types = get_post_types(array('public' => true), 'objects');
    $taxonomies = get_taxonomies(array('public' => true), 'objects');
    ?>
    <div class="wrap">
        <h1>ChatGPTモーダル表示設定</h1>

        <?php if (isset($_GET['updated'])) : ?>
            <div class="notice notice-success is-dismissible"><p>保存しました。</p></div>
        <?php endif; ?>

        <form method="post">
            <?php wp_nonce_field('naigai_chatgpt_display'); ?>

            <p><button class="button button-primary" name="naigai_chatgpt_save" value="1">保存</button></p>

            <h2>共通</h2>
            <div style="padding:12px; border:1px solid #ccd0d4; background:#fff; max-width:1100px;">
                <?php
                naigai_chatgpt_display_checkbox('front', 'フロントページ', $routes, '', home_url('/'), admin_url('options-reading.php'));
                naigai_chatgpt_display_checkbox('all', '全ページに表示', $routes, 'サイト全体', home_url('/'));
                naigai_chatgpt_display_checkbox('off', '非表示', $routes, '※これを選ぶと他の選択は無視します');
                ?>
            </div>

            <h2>固定ページ</h2>
            <div style="max-height:360px; overflow:auto; padding:12px; border:1px solid #ccd0d4; background:#fff; max-width:1100px;">
                <?php foreach ($pages as $page) : ?>
                    <?php
                    $depth = naigai_chatgpt_page_depth($page);
                    $indent = str_repeat('— ', $depth);
                    naigai_chatgpt_display_checkbox(
                        'page:' . $page->ID,
                        $indent . $page->post_title,
                        $routes,
                        'ID:' . $page->ID,
                        get_permalink($page->ID),
                        get_edit_post_link($page->ID, '')
                    );
                    ?>
                <?php endforeach; ?>
            </div>

            <h2>詳細ページ</h2>
            <div style="padding:12px; border:1px solid #ccd0d4; background:#fff; max-width:1100px;">
                <?php foreach ($post_types as $post_type => $obj) : ?>
                    <?php
                    if (in_array($post_type, array('attachment', 'page'), true)) continue;

                    $preview = naigai_chatgpt_first_post_link($post_type);
                    $admin = admin_url($post_type === 'post' ? 'edit.php' : 'edit.php?post_type=' . $post_type);

                    naigai_chatgpt_display_checkbox(
                        'singular:' . $post_type,
                        $obj->labels->name . ' の詳細ページ',
                        $routes,
                        'post_type: ' . $post_type,
                        $preview,
                        $admin
                    );
                    ?>
                <?php endforeach; ?>
            </div>

            <h2>アーカイブページ</h2>
            <div style="padding:12px; border:1px solid #ccd0d4; background:#fff; max-width:1100px;">
                <?php
                $posts_page = (int) get_option('page_for_posts');
                $blog_url = $posts_page ? get_permalink($posts_page) : home_url('/');
                naigai_chatgpt_display_checkbox('blog_index', '投稿一覧ページ', $routes, 'is_home()', $blog_url, admin_url('edit.php'));
                ?>

                <?php foreach ($post_types as $post_type => $obj) : ?>
                    <?php
                    if (in_array($post_type, array('attachment', 'page'), true)) continue;

                    $preview = naigai_chatgpt_archive_link($post_type);
                    $admin = admin_url($post_type === 'post' ? 'edit.php' : 'edit.php?post_type=' . $post_type);

                    naigai_chatgpt_display_checkbox(
                        'archive:' . $post_type,
                        $obj->labels->name . ' のアーカイブページ',
                        $routes,
                        'post_type: ' . $post_type,
                        $preview,
                        $admin
                    );
                    ?>
                <?php endforeach; ?>
            </div>

            <h2>カテゴリー・タグ・カスタム分類アーカイブ</h2>
            <div style="padding:12px; border:1px solid #ccd0d4; background:#fff; max-width:1100px;">
                <?php foreach ($taxonomies as $tax => $obj) : ?>
                    <?php
                    if (in_array($tax, array('nav_menu', 'link_category', 'post_format'), true)) continue;

                    $preview = naigai_chatgpt_first_term_link($tax);
                    $admin = admin_url('edit-tags.php?taxonomy=' . $tax);

                    naigai_chatgpt_display_checkbox(
                        'tax:' . $tax,
                        $obj->labels->name . ' アーカイブ',
                        $routes,
                        'taxonomy: ' . $tax,
                        $preview,
                        $admin
                    );
                    ?>
                <?php endforeach; ?>
            </div>

            <p><button class="button button-primary" name="naigai_chatgpt_save" value="1">保存</button></p>
        </form>
    </div>
    <?php
}

add_action('wp_enqueue_scripts', function () {
    if (!naigai_chatgpt_should_display()) return;
    if (wp_script_is('naigai-ai-chat', 'enqueued')) return;

    $path = get_template_directory() . '/js/openai-chat-modal.js';

    wp_enqueue_script(
        'naigai-ai-chat',
        get_template_directory_uri() . '/js/openai-chat-modal.js',
        array('jquery'),
        file_exists($path) ? filemtime($path) : null,
        true
    );

    wp_localize_script('naigai-ai-chat', 'naigaiAiChatAjax', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('naigai_ai_chat_nonce'),
    ));
}, 99);

add_action('wp_footer', function () {
    static $done = false;

    if ($done) return;
    if (!naigai_chatgpt_should_display()) return;

    $done = true;
    get_template_part('templates/chatgpt-modal');
}, 20);
