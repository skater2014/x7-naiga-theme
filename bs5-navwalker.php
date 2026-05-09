<?php
/**
 * CSDev - Bootstrap 5 wp_nav_menu walker
 * Supports WP MultiLevel menus
 * Based on https://github.com/AlexWebLab/bootstrap-5-wordpress-navbar-walker
 * Requires additional CSS fixes
 * CSS at https://gist.github.com/cdsaenz/d401330ba9705cfe7c18b19634c83004
 * CHANGE: removed custom display_element. Just call the menu with a $depth of 3 or more.
 */

// カスタムウォーカークラスの定義
class bs5_Walker extends Walker_Nav_menu
{
    // 追加のプロパティの定義
    private $current_item; // 現在のメニューアイテム
    private $has_mega_thumbs; // 'mega-thumbs' クラスの有無
    private $swiper_added; // Swiper 要素が追加されたかどうか

    // コンストラクタ
    function __construct() {
        $this->has_mega_thumbs = false; // 'mega-thumbs' プロパティの初期化
        $this->swiper_added = false; // Swiper 追加フラグの初期化
    }

    // メニューレベルの開始
    function start_lvl(&$output, $depth = 0, $args = null)
    {
        $indent = str_repeat("\t", $depth);
        $submenu_class = ($depth > 0) ? ' dropdown-submenu' : '';
        $ul_class = ' class="dropdown-menu' . $submenu_class . ' depth_' . $depth . '"';
        $output .= "\n$indent<ul$ul_class>\n";

        // 現在のメニューアイテムのクラスを配列として取得
        $classes = !empty($this->current_item->classes) ? (array) $this->current_item->classes : [];
        $this->has_mega_thumbs = in_array('mega-thumbs', $classes);

        if ($this->has_mega_thumbs && !$this->swiper_added) {
            $output .= "$indent<div class=\"swiper\">\n";
            $output .= "$indent\t<div class=\"swiper-wrapper\">\n";
            $this->swiper_added = true;
        }

        if ($this->has_mega_thumbs && $depth === 0) {
            $output .= "$indent\t<div class=\"swiper-button-prev\"></div>\n";
            $output .= "$indent\t<div class=\"swiper-button-next\"></div>\n";
        }
    }

    // メニューアイテムの開始
    function start_el(&$output, $item, $depth = 0, $args = null, $id = 0)
    {
        $this->current_item = $item;
        $indent = ($depth) ? str_repeat("\t", $depth) : '';
        $class_names = '';

        // 現在のメニューアイテムのクラスを配列として取得
        $classes = !empty($item->classes) ? (array) $item->classes : [];
        $classes[] = ($args->walker->has_children) ? 'dropdown' : '';
        $classes[] = 'nav-item';
        $classes[] = 'nav-item-' . esc_attr($item->ID); // Ensure ID is escaped

        // クラス名をフィルタリングして結合
        $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args));
        $class_names = ' class="' . esc_attr($class_names) . '"';

        $id = apply_filters('nav_menu_item_id', 'menu-item-' . esc_attr($item->ID), $item, $args);
        $id = $id ? ' id="' . esc_attr($id) . '"' : '';

        $output .= $indent . '<li' . $id . $class_names . '>';

        $attributes = !empty($item->attr_title) ? ' title="' . esc_attr($item->attr_title) . '"' : '';
        $attributes .= !empty($item->target) ? ' target="' . esc_attr($item->target) . '"' : '';
        $attributes .= !empty($item->xfn) ? ' rel="' . esc_attr($item->xfn) . '"' : '';
        $attributes .= !empty($item->url) ? ' href="' . esc_url($item->url) . '"' : '';

        // アクティブクラスの判定
        $active_class = ($item->current || $item->current_item_ancestor || in_array("current_page_parent", (array) $item->classes, true) || in_array("current-post-ancestor", (array) $item->classes, true)) ? 'active' : '';
        $nav_link_class = ($depth > 0) ? 'dropdown-item ' : 'nav-link ';

        // ドロップダウンのトリガークラスを追加
        if ($args->walker->has_children || in_array('mega-thumbs', (array) $item->classes)) {
            $attributes .= ' class="' . $nav_link_class . $active_class . ' dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"';
        } else {
            $attributes .= ' class="' . $nav_link_class . $active_class . '"';
        }

        $item_output = $args->before;
        $item_output .= '<a' . $attributes . '>';
        $item_output .= $args->link_before . apply_filters('the_title', $item->title, $item->ID) . $args->link_after;
        $item_output .= '</a>';
        $item_output .= $args->after;

        // サムネイル画像が利用可能な場合は追加
        if (has_post_thumbnail($item->object_id)) {
            $thumbnail = wp_get_attachment_image_src(get_post_thumbnail_id($item->object_id), 'thumbnail');
            if ($thumbnail) {
                // サムネイル画像をメニューアイテムに適用
                $item_output = '<a' . $attributes . '>';
                $item_output .= '<img src="' . esc_url($thumbnail[0]) . '" alt="' . esc_attr($item->title) . '" class="nav-item-thumbnail">'; // 画像の追加
                $item_output .= '<h7>' . esc_html($item->title) . '</h7>'; // タイトルを追加
                $item_output .= '</a>';
            }
        }

        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }

    // メニューレベルの終了
    function end_lvl(&$output, $depth = 0, $args = null) {
        $indent = str_repeat("\t", $depth);
        $output .= "$indent</ul>\n";

        if ($this->has_mega_thumbs && $depth === 0) {
            $output .= "$indent\t</div>\n"; // Swiper-wrapper を閉じる
            $output .= "$indent</div>\n"; // Swiper を閉じる
        }
    }
}

// 新しいメニューを登録
register_nav_menu('main-menu', 'Main menu');
?>
