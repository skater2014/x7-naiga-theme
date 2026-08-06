<?php

/**
 * ============================================================
 * IEZ_HERO_ADMIN_POLICY_20260806
 * 家づくり固定ページ Hero 管理画面仕様
 * ============================================================
 *
 * Heroの正式な保存キーは _ch_hero_* のみ。
 *
 *
 * 【Hero画像あり】
 *
 * _ch_hero_image_id
 * _ch_hero_gallery_ids
 * _ch_hero_video_mp4_id
 *
 * のいずれかが存在する
 *
 *      ↓
 *
 * 画像 / 動画Heroを表示する。
 *
 *
 * 【Hero画像なし + Hero文字等あり】
 *
 * _ch_hero_kicker
 * _ch_hero_title
 * _ch_hero_lead
 *
 * または有効なCTAが存在する
 *
 *      ↓
 *
 * 茶系背景Heroを表示する。
 *
 *
 * 【Hero完全空】
 *
 * 画像なし
 * 動画なし
 * キッカーなし
 * Heroタイトルなし
 * リードなし
 * 有効CTAなし
 *
 *      ↓
 *
 * Hero自体を表示しない。
 *
 * WordPress固定ページタイトル post_title を
 * 通常のH1として本文・フォーム上へ表示する。
 *
 *
 * ============================================================
 * CTA入力ルール
 * ============================================================
 *
 * CTAは
 *
 *     文言
 *       ＋
 *     URL
 *
 * の両方が必要。
 *
 * URLまで入力しないとCTAは表示しない。
 *
 * 文言だけ入力:
 *     表示しない
 *
 * URLだけ入力:
 *     表示しない
 *
 * 文言 + URL:
 *     表示する
 *
 *
 * ============================================================
 * 固定ページタイトルについて
 * ============================================================
 *
 * post_title と _ch_hero_title は別物。
 *
 * 固定ページタイトルを
 * _ch_hero_title へ自動コピーしない。
 * ============================================================
 */
/**
 * hub/pages/iezukuri/admin/metaboxes/parts/hero-metabox.php
 *
 * 家づくり固定ページ 共通Hero入力
 *
 * 対象:
 * - /iezukuri
 * - /iezukuri/ 配下の固定ページ
 *
 * 役割:
 * - Heroの文言 / CTA / 画像 / 動画 / motion を編集する。
 * - フロント描画はしない。
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('naigai_iez_admin_hero_field_map')) {
    function naigai_iez_admin_hero_field_map() {
        return array(
            '_ch_hero_kicker' => 'text',
            '_ch_hero_title' => 'text',
            '_ch_hero_lead' => 'textarea',
            '_ch_hero_cta_text' => 'text',
            '_ch_hero_cta_url' => 'url',
            '_ch_hero_sub_cta_text' => 'text',
            '_ch_hero_sub_cta_url' => 'url',
            '_ch_hero_image_id' => 'number',
            '_ch_hero_gallery_ids' => 'ids',
            '_ch_hero_engine' => 'key',
            '_ch_hero_video_mp4_id' => 'number',
            '_ch_hero_motion' => 'key',
            '_ch_hero_caption_motion' => 'key',
            '_ch_hero_interval' => 'number',
        );
    }
}

add_filter('naigai_iez_admin_fixed_page_fields', function ($fields, $post) {
    return array_merge($fields, naigai_iez_admin_hero_field_map());
}, 5, 2);

if (!function_exists('naigai_iez_admin_render_hero_input')) {
    function naigai_iez_admin_render_hero_input($post, $get) {
        $engine = $get('_ch_hero_engine', 'swiper');
        $motion = $get('_ch_hero_motion', 'zoom-in');
        $caption_motion = $get('_ch_hero_caption_motion', 'none');
        ?>
        <div class="naigai-iez-admin-section">
            <h3>Hero</h3>

            <details class="naigai-iez-admin-subsection" open>
                <summary><strong>01 文言</strong></summary>
                <table class="form-table naigai-iez-admin-table">
                    <tbody>
                        <?php
                        naigai_iez_admin_text_input('_ch_hero_kicker', 'キッカー', $get('_ch_hero_kicker', ''));
                        naigai_iez_admin_text_input('_ch_hero_title', 'タイトル', $get('_ch_hero_title', ''));
                        naigai_iez_admin_textarea('_ch_hero_lead', 'リード文', $get('_ch_hero_lead', ''), 4);
                        ?>
                    </tbody>
                </table>
            </details>

            <details class="naigai-iez-admin-subsection">
                <summary><strong>02 CTA</strong></summary>
                <table class="form-table naigai-iez-admin-table">
                    <tbody>
                        <?php
                        naigai_iez_admin_text_input('_ch_hero_cta_text', 'メインCTA文言', $get('_ch_hero_cta_text', ''));
                        naigai_iez_admin_url_input('_ch_hero_cta_url', 'メインCTA URL', $get('_ch_hero_cta_url', ''));
                        naigai_iez_admin_text_input('_ch_hero_sub_cta_text', 'サブCTA文言', $get('_ch_hero_sub_cta_text', ''));
                        naigai_iez_admin_url_input('_ch_hero_sub_cta_url', 'サブCTA URL', $get('_ch_hero_sub_cta_url', ''));
                        ?>
                    </tbody>
                </table>
            </details>

            <details class="naigai-iez-admin-subsection">
                <summary><strong>03 メディア</strong></summary>
                <table class="form-table naigai-iez-admin-table">
                    <tbody>
                        <?php
                        naigai_iez_admin_media_input('_ch_hero_image_id', 'Hero画像ID', $get('_ch_hero_image_id', ''), 'image');
                        naigai_iez_admin_media_input('_ch_hero_gallery_ids', 'Hero複数画像ID', $get('_ch_hero_gallery_ids', ''), 'image', true, '複数画像はカンマ区切りで保存されます。');
                        naigai_iez_admin_media_input('_ch_hero_video_mp4_id', 'Hero MP4動画ID', $get('_ch_hero_video_mp4_id', ''), 'video');
                        ?>
                    </tbody>
                </table>
            </details>

            <details class="naigai-iez-admin-subsection">
                <summary><strong>04 表示方式・動き</strong></summary>
                <table class="form-table naigai-iez-admin-table">
                    <tbody>
                        <tr>
                            <th scope="row"><label for="_ch_hero_engine">Heroエンジン</label></th>
                            <td>
                                <select id="_ch_hero_engine" name="_ch_hero_engine">
                                    <option value="image" <?php selected($engine, 'image'); ?>>image：単体画像</option>
                                    <option value="swiper" <?php selected($engine, 'swiper'); ?>>swiper：複数画像</option>
                                    <option value="burns" <?php selected($engine, 'burns'); ?>>burns：Ken Burns</option>
                                    <option value="video" <?php selected($engine, 'video'); ?>>video：MP4動画</option>
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row"><label for="_ch_hero_motion">画像モーション</label></th>
                            <td>
                                <select id="_ch_hero_motion" name="_ch_hero_motion">
                                    <option value="zoom-in" <?php selected($motion, 'zoom-in'); ?>>zoom-in</option>
                                    <option value="zoom-out" <?php selected($motion, 'zoom-out'); ?>>zoom-out</option>
                                    <option value="pan-left" <?php selected($motion, 'pan-left'); ?>>pan-left</option>
                                    <option value="pan-right" <?php selected($motion, 'pan-right'); ?>>pan-right</option>
                                    <option value="pan-up" <?php selected($motion, 'pan-up'); ?>>pan-up</option>
                                    <option value="pan-down" <?php selected($motion, 'pan-down'); ?>>pan-down</option>
                                    <option value="none" <?php selected($motion, 'none'); ?>>none</option>
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row"><label for="_ch_hero_caption_motion">文字モーション（H1 / p）</label></th>
                            <td>
                                <select id="_ch_hero_caption_motion" name="_ch_hero_caption_motion">
                                    <option value="none" <?php selected($caption_motion, 'none'); ?>>none</option>
                                    <option value="focus" <?php selected($caption_motion, 'focus'); ?>>focus：ゆっくりフォーカス</option>
                                    <option value="slide-up" <?php selected($caption_motion, 'slide-up'); ?>>slide-up：ゆっくり下から表示</option>
                                </select>
                            </td>
                        </tr>

                        <?php
                        naigai_iez_admin_text_input('_ch_hero_interval', '画像切替間隔', $get('_ch_hero_interval', '9000'), '単位: ms。9000 = 9秒。H1 / p の文字モーションもこの時間を基準にします。');
                        ?>
                    </tbody>
                </table>
            </details>
        </div>
        <?php
    }
}
