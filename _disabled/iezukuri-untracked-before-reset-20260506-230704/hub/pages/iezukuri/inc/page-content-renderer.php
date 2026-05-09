<?php
/**
 * =========================================================
 * Iezukuri fixed page content renderer
 * ---------------------------------------------------------
 * 対象:
 * - 固定ページ /iezukuri/new-house/
 * - 固定ページ /iezukuri/chuko/
 * - 固定ページ /iezukuri/nisetai/
 *
 * 役割:
 * - 管理画面で保存した _ch_page_* JSON をフロントに表示する
 * - メタが空なら false を返し、既存 templates/content/*.php を fallback 表示する
 *
 * 注意:
 * - トップページ /iezukuri/ には使わない
 * - iez_plan の _ch_plan_* メタとは分ける
 * =========================================================
 */

if (!defined('ABSPATH')) {
    exit;
}

function naigai_iezukuri_page_json_meta($post_id, $key, $default = array()) {
    $raw = get_post_meta($post_id, $key, true);

    if ($raw === '') {
        return $default;
    }

    $decoded = json_decode((string) $raw, true);

    if (!is_array($decoded)) {
        return $default;
    }

    return $decoded;
}

function naigai_iezukuri_render_page_text($text) {
    $text = trim((string) $text);

    if ($text === '') {
        return;
    }

    $parts = preg_split("/\r\n|\n|\r/", $text);

    foreach ($parts as $part) {
        $part = trim($part);

        if ($part !== '') {
            echo '<p>' . esc_html($part) . '</p>';
        }
    }
}

function naigai_iezukuri_render_page_content_from_meta($post_id = 0) {
    $post_id = $post_id ? absint($post_id) : get_the_ID();

    if (!$post_id) {
        return false;
    }

    $hero     = naigai_iezukuri_page_json_meta($post_id, '_ch_page_hero_json');
    $sections = naigai_iezukuri_page_json_meta($post_id, '_ch_page_sections_json');
    $cta      = naigai_iezukuri_page_json_meta($post_id, '_ch_page_cta_json');

    $has_content = !empty($hero) || !empty($sections) || !empty($cta);

    if (!$has_content) {
        return false;
    }

    $slug = get_post_field('post_name', $post_id);

    echo '<div class="ch-page-content ch-page-meta-content ch-page-meta-content--' . esc_attr($slug) . '">';

    if (!empty($hero)) {
        $eyebrow = $hero['eyebrow'] ?? '';
        $title   = $hero['title'] ?? get_the_title($post_id);
        $lead    = $hero['lead'] ?? '';

        echo '<section class="ch-hero ch-fullbleed is-no-media" data-customhome-hero>';
        echo '<div class="ch-shell">';
        echo '<div class="ch-hero__content">';
        echo '<p class="ch-hero__company">内外建設株式会社</p>';

        if ($eyebrow !== '') {
            echo '<p class="ch-hero__eyebrow">' . esc_html($eyebrow) . '</p>';
        }

        echo '<h1 class="ch-hero__title">' . nl2br(esc_html($title)) . '</h1>';

        if ($lead !== '') {
            echo '<p class="ch-hero__lead">' . nl2br(esc_html($lead)) . '</p>';
        }

        echo '<div class="ch-hero__actions">';
        echo '<a class="ch-btn ch-btn--primary" href="' . esc_url(home_url('/contact/')) . '">相談する</a>';
        echo '<a class="ch-btn ch-btn--ghost" href="' . esc_url(home_url('/iezukuri/')) . '">家づくりトップへ</a>';
        echo '</div>';

        echo '</div>';
        echo '</div>';
        echo '</section>';
    }

    if (!empty($sections) && is_array($sections)) {
        foreach ($sections as $index => $section) {
            if (!is_array($section)) {
                continue;
            }

            $type   = $section['type'] ?? 'text';
            $kicker = $section['kicker'] ?? '';
            $title  = $section['title'] ?? '';
            $body   = $section['body'] ?? '';
            $items  = $section['items'] ?? array();

            echo '<section class="ch-subpage-section ch-meta-section ch-meta-section--' . esc_attr($type) . '">';
            echo '<div class="ch-shell">';

            if ($kicker !== '') {
                echo '<p class="ch-eyebrow">' . esc_html($kicker) . '</p>';
            }

            if ($title !== '') {
                echo '<h2 class="ch-section-title">' . esc_html($title) . '</h2>';
            }

            if ($body !== '') {
                echo '<div class="ch-meta-section__body">';
                naigai_iezukuri_render_page_text($body);
                echo '</div>';
            }

            if (!empty($items) && is_array($items)) {
                echo '<div class="ch-meta-card-grid">';

                foreach ($items as $item) {
                    if (!is_array($item)) {
                        continue;
                    }

                    $item_title = $item['title'] ?? '';
                    $item_body  = $item['body'] ?? '';

                    echo '<article class="ch-meta-card">';

                    if ($item_title !== '') {
                        echo '<h3>' . esc_html($item_title) . '</h3>';
                    }

                    if ($item_body !== '') {
                        echo '<p>' . esc_html($item_body) . '</p>';
                    }

                    echo '</article>';
                }

                echo '</div>';
            }

            echo '</div>';
            echo '</section>';
        }
    }

    if (!empty($cta)) {
        $cta_title = $cta['title'] ?? '';
        $cta_body  = $cta['body'] ?? '';
        $btn_text  = $cta['button_text'] ?? '無料相談・資料請求';
        $btn_url   = $cta['button_url'] ?? home_url('/contact/');

        echo '<section class="ch-section ch-cta ch-meta-cta">';
        echo '<div class="ch-shell">';

        if ($cta_title !== '') {
            echo '<h2 class="ch-cta__title">' . esc_html($cta_title) . '</h2>';
        }

        if ($cta_body !== '') {
            echo '<p class="ch-cta__text">' . esc_html($cta_body) . '</p>';
        }

        echo '<a class="ch-btn ch-btn--primary" href="' . esc_url($btn_url) . '">' . esc_html($btn_text) . '</a>';

        echo '</div>';
        echo '</section>';
    }

    echo '</div>';

    return true;
}
