<?php
/**
 * /minpaku 編集画面 メタボックス整理
 *
 * 対象:
 * - 固定ページ slug=minpaku
 *
 * 目的:
 * - /minpaku 管理画面に、注文住宅・Hub・平屋・施工事例系のメタボックスを出さない
 * - 民泊運営サポートLP設定は残す
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('add_meta_boxes_page', function ($post) {
    if (!($post instanceof WP_Post)) {
        return;
    }

    $slug = (string) get_post_field('post_name', $post->ID);

    if ($slug !== 'minpaku') {
        return;
    }

    global $wp_meta_boxes;

    if (empty($wp_meta_boxes['page']) || !is_array($wp_meta_boxes['page'])) {
        return;
    }

    $remove_title_keywords = array(
        '注文住宅',
        'Hub ページ設定',
        'Hubページ設定',
        '平屋',
        '施工事例',
        'CTAメディア',
        'Hero会社名',
        'design-office',
        '動画フォーマットID入力',
        '動画フォーマット',
    );

    $remove_id_keywords = array(
        'hub',
        'customhome',
        'construction',
        'ch_',
        'plan',
        'design-office',
        '動画フォーマットID入力',
        '動画フォーマット',
        'sekou',
        'cta',
        'video',
        'movie',
        'format',
    );

    foreach ($wp_meta_boxes['page'] as $context => $priorities) {
        if (!is_array($priorities)) {
            continue;
        }

        foreach ($priorities as $priority => $boxes) {
            if (!is_array($boxes)) {
                continue;
            }

            foreach ($boxes as $box_id => $box) {
                /*
                 * 民泊サポートLP本体は残す。
                 */
                if ($box_id === 'naigai-mps-support-settings') {
                    continue;
                }

                $title = isset($box['title']) ? wp_strip_all_tags((string) $box['title']) : '';
                $remove = false;

                foreach ($remove_title_keywords as $keyword) {
                    if ($keyword !== '' && strpos($title, $keyword) !== false) {
                        $remove = true;
                        break;
                    }
                }

                if (!$remove) {
                    foreach ($remove_id_keywords as $keyword) {
                        if ($keyword !== '' && strpos((string) $box_id, $keyword) !== false) {
                            $remove = true;
                            break;
                        }
                    }
                }

                if ($remove) {
                    unset($wp_meta_boxes['page'][$context][$priority][$box_id]);
                }
            }
        }
    }
}, 999999);
