<?php
/**
 * hub/pages/iezukuri/admin/metaboxes/subpage-metabox.php
 *
 * 家づくり 固定ページ入力
 *
 * 対象:
 * - post_type = page
 * - /iezukuri/ トップページ
 * - /iezukuri/ 配下の固定ページ
 *
 * 対象外:
 * - 間取り詳細 CPT: iez_plan
 *
 * 役割:
 * - 固定ページの Hero / トップ本文 / 詳細本文 / CTA を編集する。
 * - 管理画面CSS/JSは admin/enqueue.php に任せる。
 * - ここでは wp_enqueue_* を書かない。
 */

if (!defined('ABSPATH')) {
    exit;
}

$naigai_iez_metabox_part_files = array(
    __DIR__ . '/parts/hero-metabox.php',
    __DIR__ . '/parts/top-metabox.php',
    __DIR__ . '/chuko-metabox.php',
);

foreach ($naigai_iez_metabox_part_files as $naigai_iez_metabox_part_file) {
    if (file_exists($naigai_iez_metabox_part_file)) {
        require_once $naigai_iez_metabox_part_file;
    }
}


if (!function_exists('naigai_iez_admin_is_fixed_page_target')) {
    function naigai_iez_admin_is_fixed_page_target($post) {
        if (!$post || $post->post_type !== 'page') {
            return false;
        }

        $uri = get_page_uri($post);

        return $uri === 'iezukuri' || strpos($uri, 'iezukuri/') === 0;
    }
}

if (!function_exists('naigai_iez_admin_is_top_page')) {
    function naigai_iez_admin_is_top_page($post) {
        return $post && $post->post_type === 'page' && get_page_uri($post) === 'iezukuri';
    }
}

if (!function_exists('naigai_iez_admin_get_meta')) {
    function naigai_iez_admin_get_meta($post_id, $key, $default = '') {
        $value = get_post_meta($post_id, $key, true);

        if ($value !== '') {
            return $value;
        }

        $fallbacks = array(
            '_ch_hero_gallery_ids' => array('_ch_hero_gallery_ids', '_ch_hero_gallery_ids'),
            '_ch_hero_image_id' => array('_ch_hero_image_id', '_ch_hero_image_id'),
            '_ch_hero_video_mp4_id' => array('_ch_hero_video_mp4_id', '_mpb_hero_video_mp4_id'),
        );

        if (!empty($fallbacks[$key])) {
            foreach ($fallbacks[$key] as $old_key) {
                $old_value = get_post_meta($post_id, $old_key, true);
                if ($old_value !== '') {
                    return $old_value;
                }
            }
        }

        return $default;
    }
}

if (!function_exists('naigai_iez_admin_icon_choices')) {
    function naigai_iez_admin_icon_choices() {
        if (function_exists('naigai_iez_icon_choices')) {
            return naigai_iez_icon_choices();
        }

        return array(
            'new-house'       => '新築住宅',
            'hiraya'          => '平屋',
            'two-story'       => '二階建て住宅',
            'two-family'      => '二世帯住宅',
            'renovation'      => '住宅リフォーム',
            'used-renovation' => '中古住宅リフォーム',
            'dual-life'       => '二拠点生活',
            'consultation'    => '相談',
        );
    }
}

if (!function_exists('naigai_iez_admin_text_input')) {
    function naigai_iez_admin_text_input($id, $label, $value, $description = '') {
        ?>
        <tr>
            <th scope="row"><label for="<?php echo esc_attr($id); ?>"><?php echo esc_html($label); ?></label></th>
            <td>
                <input type="text" id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($id); ?>" value="<?php echo esc_attr($value); ?>" class="regular-text">
                <?php if ($description !== '') : ?>
                    <p class="description"><?php echo esc_html($description); ?></p>
                <?php endif; ?>
            </td>
        </tr>
        <?php
    }
}

if (!function_exists('naigai_iez_admin_url_input')) {
    function naigai_iez_admin_url_input($id, $label, $value, $description = '') {
        ?>
        <tr>
            <th scope="row"><label for="<?php echo esc_attr($id); ?>"><?php echo esc_html($label); ?></label></th>
            <td>
                <input type="url" id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($id); ?>" value="<?php echo esc_attr($value); ?>" class="regular-text">
                <?php if ($description !== '') : ?>
                    <p class="description"><?php echo esc_html($description); ?></p>
                <?php endif; ?>
            </td>
        </tr>
        <?php
    }
}

if (!function_exists('naigai_iez_admin_textarea')) {
    function naigai_iez_admin_textarea($id, $label, $value, $rows = 5, $description = '') {
        ?>
        <tr>
            <th scope="row"><label for="<?php echo esc_attr($id); ?>"><?php echo esc_html($label); ?></label></th>
            <td>
                <textarea id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($id); ?>" rows="<?php echo esc_attr($rows); ?>" class="large-text"><?php echo esc_textarea($value); ?></textarea>
                <?php if ($description !== '') : ?>
                    <p class="description"><?php echo esc_html($description); ?></p>
                <?php endif; ?>
            </td>
        </tr>
        <?php
    }
}

if (!function_exists('naigai_iez_admin_media_input')) {
    function naigai_iez_admin_media_input($id, $label, $value, $media_type = 'image', $multiple = false, $description = '') {
        $button_label = $media_type === 'video' ? 'MP4を選択' : ($multiple ? '画像を複数選択' : '画像を選択');
        ?>
        <tr>
            <th scope="row"><label for="<?php echo esc_attr($id); ?>"><?php echo esc_html($label); ?></label></th>
            <td>
                <input type="text" id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($id); ?>" value="<?php echo esc_attr($value); ?>" class="regular-text">
                <button
                    type="button"
                    class="button <?php echo $multiple ? 'button-primary' : ''; ?>"
                    data-iez-media-target="#<?php echo esc_attr($id); ?>"
                    data-iez-media-type="<?php echo esc_attr($media_type); ?>"
                    <?php echo $multiple ? 'data-iez-media-multiple="1"' : ''; ?>
                    data-iez-media-title="<?php echo esc_attr($label); ?>"
                    data-iez-media-button="このメディアを使う"
                ><?php echo esc_html($button_label); ?></button>
                <button
                    type="button"
                    class="button"
                    data-iez-media-clear="#<?php echo esc_attr($id); ?>"
                >クリア</button>

                <div class="naigai-iez-media-preview" data-iez-preview-for="<?php echo esc_attr($id); ?>"></div>

                <?php if ($description !== '') : ?>
                    <p class="description"><?php echo esc_html($description); ?></p>
                <?php endif; ?>
            </td>
        </tr>
        <?php
    }
}

if (!function_exists('naigai_iez_admin_icon_select')) {
    function naigai_iez_admin_icon_select($id, $label, $value) {
        $choices = naigai_iez_admin_icon_choices();
        ?>
        <tr>
            <th scope="row"><label for="<?php echo esc_attr($id); ?>"><?php echo esc_html($label); ?></label></th>
            <td>
                <select id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($id); ?>">
                    <?php foreach ($choices as $key => $name) : ?>
                        <option value="<?php echo esc_attr($key); ?>" <?php selected($value, $key); ?>>
                            <?php echo esc_html($name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <p class="description">SVGアイコンは inc/icon-catalog.php で管理します。</p>
            </td>
        </tr>
        <?php
    }
}

/*
 * ============================================================
 * IEZ_FIXED_PAGE_CLASSIC_EDITOR_20260806
 * 家づくり固定ページ 管理画面
 * ============================================================
 *
 * 家づくり固定ページは
 *
 *     家づくり 固定ページ入力
 *
 * メタボックスを中心に編集する。
 *
 * Gutenbergを使用するとClassic Meta Boxが
 * 下部の互換エリアへ表示され、
 * 画面が上下に分断されて編集しづらくなる。
 *
 * そのため家づくり固定ページだけ
 * ブロックエディタを使用しない。
 *
 * 通常の固定ページや投稿には影響させない。
 * ============================================================
 */
add_filter(
    'use_block_editor_for_post',
    function ($use_block_editor, $post) {

        if (
            !($post instanceof WP_Post)
            || $post->post_type !== 'page'
        ) {
            return $use_block_editor;
        }

        if (
            !function_exists(
                'naigai_iez_admin_is_fixed_page_target'
            )
            ||
            !naigai_iez_admin_is_fixed_page_target(
                $post
            )
        ) {
            return $use_block_editor;
        }

        /*
         * 家づくり固定ページだけClassic編集画面へ。
         */
        return false;
    },
    20,
    2
);

add_action('add_meta_boxes', function ($post_type, $post) {
    if ($post_type !== 'page') {
        return;
    }

    if (!naigai_iez_admin_is_fixed_page_target($post)) {
        return;
    }

    add_meta_box(
        'naigai_iez_fixed_page_input',
        '家づくり 固定ページ入力',
        'naigai_iez_render_fixed_page_input_metabox',
        'page',
        'normal',
        'high'
    );
}, 10, 2);

if (!function_exists('naigai_iez_render_fixed_page_input_metabox')) {
    function naigai_iez_render_fixed_page_input_metabox($post) {
        wp_nonce_field('naigai_iez_save_fixed_page_input', 'naigai_iez_fixed_page_nonce');

        $get = function ($key, $default = '') use ($post) {
            return naigai_iez_admin_get_meta($post->ID, $key, $default);
        };

        $is_top = naigai_iez_admin_is_top_page($post);
        $is_chuko = function_exists('naigai_iez_admin_is_chuko_page') && naigai_iez_admin_is_chuko_page($post);

        if (function_exists('naigai_iez_admin_render_hero_input')) {
            naigai_iez_admin_render_hero_input($post, $get);
        }

        if ($is_top && function_exists('naigai_iez_admin_render_top_input')) {
            naigai_iez_admin_render_top_input($post, $get);
        } elseif ($is_chuko && function_exists('naigai_iez_admin_render_chuko_input')) {
            naigai_iez_admin_render_chuko_input($post);
        } else {
            ?>
            <div class="naigai-iez-admin-section">
                <h3>サブコンテンツ（ページ本文）</h3>

<p class="description">
    Heroの下に表示するページ本文です。
    Hero・Footer直前CTAとは別の内容です。
</p>

                <table class="form-table naigai-iez-admin-table">
                    <tbody>
                        <?php
                                            /*
                     * ====================================================
                     * IEZ_SUB_CONTENT_ADMIN_NOTE_20260806
                     * サブコンテンツ（ページ本文）
                     * ====================================================
                     *
                     * _ch_intro_* = ページ本文の導入部分
                     * _ch_body_*  = ページ本文の本体部分
                     *
                     * Heroではない。
                     * Footerではない。
                     * Footer直前のサブCTAでもない。
                     * ====================================================
                     */
                    echo '<p class="description" style="margin:0 0 16px;">'
                        . 'この項目はページ本文です。Hero・Footer直前サブCTAとは別です。'
                        . '</p>';
naigai_iez_admin_text_input('_ch_intro_kicker', '導入キッカー', $get('_ch_intro_kicker', ''));
                        naigai_iez_admin_text_input('_ch_intro_title', '導入見出し', $get('_ch_intro_title', ''));
                        naigai_iez_admin_textarea('_ch_intro_text', '導入本文', $get('_ch_intro_text', ''), 5);

                        /*
                         * ====================================================
                         * IEZ_CONTENT_ITEMS_ADMIN_CALL_20260806
                         * サブコンテンツ「画像＋テキスト」
                         * ====================================================
                         *
                         * 旧:
                         * - 本文見出し
                         * - 本文
                         * - 本文画像ID
                         * - ギャラリー画像ID
                         *
                         * は管理画面から廃止。
                         *
                         * 画像とその画像に対応する文章を
                         * 1セットとして管理する。
                         *
                         * 1セットのみ登録
                         *     → 単体画像
                         *
                         * 複数セット登録
                         *     → 複数画像
                         *
                         * 表示パターンだけ変更することで
                         * カード / 左画像 / 右画像 / 左右交互
                         * を同じ保存データから表示できる。
                         * ====================================================
                         */
                        naigai_iez_admin_render_content_items($post);
                        ?>
                    </tbody>
                </table>
            </div>
            <?php
        }
        ?>

        <div class="naigai-iez-admin-section">
            <h3>Footer直前 サブCTA</h3>

<p class="description">
    ページ本文の後、共通Footerの直前に表示するCTAです。
    Hero CTAとは別の項目です。
</p>

            <table class="form-table naigai-iez-admin-table">
                <tbody>
                    <?php
                    naigai_iez_admin_text_input(
                        '_ch_sub_cta_kicker',
                        'CTAキッカー',
                        $get('_ch_sub_cta_kicker', '')
                    );

                    naigai_iez_admin_text_input(
                        '_ch_sub_cta_title',
                        'CTA見出し',
                        $get('_ch_sub_cta_title', '')
                    );

                    naigai_iez_admin_textarea(
                        '_ch_sub_cta_text',
                        'CTA本文',
                        $get('_ch_sub_cta_text', ''),
                        4
                    );

                    naigai_iez_admin_text_input(
                        '_ch_sub_cta_primary_text',
                        'メインボタン文言',
                        $get('_ch_sub_cta_primary_text', '')
                    );

                    naigai_iez_admin_url_input(
                        '_ch_sub_cta_primary_url',
                        'メインボタンURL',
                        $get('_ch_sub_cta_primary_url', '')
                    );

                    naigai_iez_admin_text_input(
                        '_ch_sub_cta_secondary_text',
                        'サブボタン文言',
                        $get('_ch_sub_cta_secondary_text', '')
                    );

                    naigai_iez_admin_url_input(
                        '_ch_sub_cta_secondary_url',
                        'サブボタンURL',
                        $get('_ch_sub_cta_secondary_url', '')
                    );
                    ?>
                </tbody>
            </table>
        </div>
        <?php
    }
}


add_action('save_post_page', function ($post_id) {
    if (!isset($_POST['naigai_iez_fixed_page_nonce'])) {
        return;
    }

    if (!wp_verify_nonce($_POST['naigai_iez_fixed_page_nonce'], 'naigai_iez_save_fixed_page_input')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_page', $post_id)) {
        return;
    }

    $post = get_post($post_id);

    if (!naigai_iez_admin_is_fixed_page_target($post)) {
        return;
    }

    $fields = array(
        '_ch_hero_kicker' => 'text',
        '_ch_hero_title' => 'text',
        '_ch_hero_lead' => 'textarea',
        '_ch_hero_image_id' => 'number',
        '_ch_hero_gallery_ids' => 'ids',
        '_ch_hero_engine' => 'key',
        '_ch_hero_video_mp4_id' => 'number',
        '_ch_hero_motion' => 'key',
        '_ch_hero_interval' => 'number',

        '_ch_top_intro_kicker' => 'text',
        '_ch_top_intro_title' => 'text',
        '_ch_top_intro_text' => 'textarea',
        '_ch_top_site_reading_title' => 'text',
        '_ch_top_site_reading_text' => 'textarea',
        '_ch_top_site_reading_image_id' => 'number',
        '_ch_top_works_title' => 'text',
        '_ch_top_works_text' => 'textarea',
        '_ch_top_works_image_id' => 'number',
        '_ch_top_plans_title' => 'text',
        '_ch_top_plans_text' => 'textarea',
        '_ch_top_plans_image_id' => 'number',
        '_ch_top_flow_title' => 'text',
        '_ch_top_flow_text' => 'textarea',

        '_ch_intro_kicker' => 'text',
        '_ch_intro_title' => 'text',
        '_ch_intro_text' => 'textarea',
        /*
         * ====================================================
         * IEZ_OLD_BODY_FIELDS_REMOVED_20260806
         * ====================================================
         *
         * 以下の旧フィールドは新規保存対象から外した。
         *
         * _ch_body_title
         * _ch_body_text
         * _ch_body_image_id
         * _ch_gallery_ids
         *
         * 新しい画像＋文章は
         * _ch_content_items に配列で保存する。
         * ====================================================
         */

        /*
         * ====================================================
         * Footer直前 サブCTA
         * ====================================================
         *
         * section.iez-sub-cta 専用。
         *
         * Hero用 _ch_hero_* とは完全に別。
         * ページ本文 _ch_intro_* / _ch_body_* とも別。
         */
        '_ch_sub_cta_kicker'         => 'text',
        '_ch_sub_cta_title'          => 'text',
        '_ch_sub_cta_text'           => 'textarea',

        '_ch_sub_cta_primary_text'   => 'text',
        '_ch_sub_cta_primary_url'    => 'url',

        '_ch_sub_cta_secondary_text' => 'text',
        '_ch_sub_cta_secondary_url'  => 'url',
    );

    $fields = apply_filters('naigai_iez_admin_fixed_page_fields', $fields, $post);

    for ($i = 1; $i <= 3; $i++) {
        $fields["_ch_top_route_{$i}_icon"] = 'key';
        $fields["_ch_top_route_{$i}_title"] = 'text';
        $fields["_ch_top_route_{$i}_text"] = 'textarea';
        $fields["_ch_top_route_{$i}_url"] = 'url';
        $fields["_ch_top_route_{$i}_image_id"] = 'number';
    }

    foreach ($fields as $field => $type) {
        if (!isset($_POST[$field])) {
            continue;
        }

        $value = wp_unslash($_POST[$field]);

        if ($type === 'number') {
            $value = preg_replace('/[^0-9]/', '', $value);
        } elseif ($type === 'ids') {
            $value = preg_replace('/[^0-9,]/', '', $value);
            $value = trim($value, ',');
        } elseif ($type === 'key') {
            $value = sanitize_key($value);
        } elseif ($type === 'url') {
            $value = esc_url_raw($value);
        } elseif ($type === 'textarea') {
            $value = sanitize_textarea_field($value);
        } else {
            $value = sanitize_text_field($value);
        }

        update_post_meta($post_id, $field, $value);
    }

    /*
     * 旧キーにもミラー保存。
     * 目的:
     * - 既存 renderer / template が旧キーを読んでいても反映させる。
     * - 移行中の「選んだのに表示されない」を防ぐ。
     */
    $gallery_ids = get_post_meta($post_id, '_ch_hero_gallery_ids', true);

if ($gallery_ids === '') {
} else {
}

$image_id = get_post_meta($post_id, '_ch_hero_image_id', true);

if ($image_id === '' || absint($image_id) === 0) {
} else {
}

$video_id = get_post_meta($post_id, '_ch_hero_video_mp4_id', true);

if ($video_id === '' || absint($video_id) === 0) {
} else {
}
});


/* IEZ_HERO_EXISTING_METABOX_UI_START */
/**
 * 既存メタボックス内 Hero UI 統合
 *
 * 役割:
 * - CTA用の別メタボックスは作らない。
 * - 既存の Hero CTA URL 入力欄の横に固定ページURL選択を出す。
 * - 編集画面をコンパクトにする。
 * - CTA保存メタキーは _ch_hero_* に統一する。
 */
if (!function_exists('naigai_iez_existing_metabox_hero_engine')) {
    function naigai_iez_existing_metabox_hero_engine($engine) {
        $engine = sanitize_key((string) $engine);

        if ($engine === 'fade') {
            return 'burns';
        }

        $allowed = array('image', 'swiper', 'video', 'burns');
        return in_array($engine, $allowed, true) ? $engine : 'burns';
    }
}

if (!function_exists('naigai_iez_existing_metabox_hero_save')) {
    function naigai_iez_existing_metabox_hero_save($post_id) {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        if (
            !isset($_POST['naigai_iez_fixed_page_nonce']) ||
            !wp_verify_nonce(
                sanitize_text_field(wp_unslash($_POST['naigai_iez_fixed_page_nonce'])),
                'naigai_iez_save_fixed_page_input'
            )
        ) {
            return;
        }

        $text_fields = array(
            '_ch_hero_cta_text',
            '_ch_hero_sub_cta_text',
        );

        foreach ($text_fields as $key) {
            $value = isset($_POST[$key]) ? sanitize_text_field(wp_unslash($_POST[$key])) : '';

            if ($value === '') {
                delete_post_meta($post_id, $key);
            } else {
                update_post_meta($post_id, $key, $value);
            }
        }

        $url_fields = array(
            '_ch_hero_cta_url',
            '_ch_hero_sub_cta_url',
        );

        foreach ($url_fields as $key) {
            $value = isset($_POST[$key]) ? esc_url_raw(wp_unslash($_POST[$key])) : '';

            if ($value === '') {
                delete_post_meta($post_id, $key);
            } else {
                update_post_meta($post_id, $key, $value);
            }
        }

        if (isset($_POST['_ch_hero_engine'])) {
            update_post_meta(
                $post_id,
                '_ch_hero_engine',
                naigai_iez_existing_metabox_hero_engine(wp_unslash($_POST['_ch_hero_engine']))
            );
        }
    }
}

/*
 * ============================================================
 * IEZ_HERO_LEGACY_SAVE_DISABLED_20260806
 * 旧Hero保存処理は実行しない
 * ============================================================
 *
 * naigai_iez_existing_metabox_hero_save()
 * は過去データとの互換用コードとして定義だけ残す。
 *
 * save_post_page へは登録しない。
 *
 * 現在の正式なHero保存処理は
 *
 *     naigai_iez_save_hero_new_keys_only()
 *
 * だけ。
 *
 * 保存先も
 *
 *     _ch_hero_*
 *
 * のみに統一する。
 * ============================================================
 */

if (!function_exists('naigai_iez_existing_metabox_hero_admin_ui')) {
    function naigai_iez_existing_metabox_hero_admin_ui() {
        global $post;

        if (!$post || $post->post_type !== 'page') {
            return;
        }

        $pages = get_pages(array(
            'sort_column' => 'menu_order,post_title',
            'sort_order'  => 'ASC',
            'post_status' => array('publish', 'draft', 'private'),
        ));

        $choices = array();

        foreach ($pages as $page) {
            $url = get_permalink($page->ID);
            $path = trim((string) parse_url($url, PHP_URL_PATH), '/');

            $choices[] = array(
                'url'   => $url,
                'label' => ($path !== '' ? '/' . $path . '/' : $page->post_title) . ' - ' . $page->post_title,
            );
        }
        ?>
        <script>
        document.addEventListener('DOMContentLoaded', function () {
            var choices = <?php echo wp_json_encode($choices); ?>;

            function addPicker(targetName) {
                var input =
                    document.getElementById(targetName) ||
                    document.querySelector('[name="' + targetName + '"]');

                if (!input) {
                    return;
                }

                if (
                    input.nextElementSibling &&
                    input.nextElementSibling.classList &&
                    input.nextElementSibling.classList.contains('naigai-iez-existing-url-picker')
                ) {
                    return;
                }

                var select = document.createElement('select');
                select.className = 'naigai-iez-existing-url-picker';
                select.setAttribute('data-target', targetName);

                var first = document.createElement('option');
                first.value = '';
                first.textContent = '固定ページからURLを選択';
                select.appendChild(first);

                choices.forEach(function (choice) {
                    var option = document.createElement('option');
                    option.value = choice.url;
                    option.textContent = choice.label;
                    select.appendChild(option);
                });

                select.addEventListener('change', function () {
                    if (!select.value) {
                        return;
                    }

                    input.value = select.value;
                    input.dispatchEvent(new Event('change', { bubbles: true }));
                });

                input.insertAdjacentElement('afterend', select);
            }

            addPicker('_ch_hero_cta_url');
            addPicker('_ch_hero_sub_cta_url');
        });
        </script>
        <?php
    }
}

add_action('admin_footer-post.php', 'naigai_iez_existing_metabox_hero_admin_ui');
add_action('admin_footer-post-new.php', 'naigai_iez_existing_metabox_hero_admin_ui');
/* IEZ_HERO_EXISTING_METABOX_UI_END */


/* IEZ_HERO_MOTION_ADMIN_FINAL_START */
/**
 * Hero motion UI
 *
 * 画像:
 * - kenburns-top / bottom / left / right / none
 *
 * 見出し:
 * - caption / h1 / lead だけ
 *
 * CTA:
 * - 動かさない
 */
if (!function_exists('naigai_iez_admin_sanitize_motion_value')) {
    function naigai_iez_admin_sanitize_motion_value($motion) {
        $motion = sanitize_key((string) $motion);

        $legacy = array(
            'zoom-in'   => 'kenburns-top',
            'zoom-out'  => 'kenburns-bottom',
            'pan-left'  => 'kenburns-left',
            'pan-right' => 'kenburns-right',
            'pan-up'    => 'kenburns-top',
            'pan-down'  => 'kenburns-bottom',
            'fade'      => 'kenburns-top',
        );

        if (isset($legacy[$motion])) {
            return $legacy[$motion];
        }

        $allowed = array('none', 'kenburns-top', 'kenburns-bottom', 'kenburns-left', 'kenburns-right');
        return in_array($motion, $allowed, true) ? $motion : 'kenburns-top';
    }
}

if (!function_exists('naigai_iez_admin_sanitize_caption_motion')) {
    function naigai_iez_admin_sanitize_caption_motion($motion) {
        $motion = sanitize_key((string) $motion);
        $allowed = array('none', 'focus', 'slide-up');

        return in_array($motion, $allowed, true) ? $motion : 'none';
    }
}

add_action('save_post_page', function ($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (
        !isset($_POST['naigai_iez_fixed_page_nonce']) ||
        !wp_verify_nonce(
            sanitize_text_field(wp_unslash($_POST['naigai_iez_fixed_page_nonce'])),
            'naigai_iez_save_fixed_page_input'
        )
    ) {
        return;
    }

    if (isset($_POST['_ch_hero_motion'])) {
        update_post_meta(
            $post_id,
            '_ch_hero_motion',
            naigai_iez_admin_sanitize_motion_value(wp_unslash($_POST['_ch_hero_motion']))
        );
    }

    if (isset($_POST['_ch_hero_caption_motion'])) {
        update_post_meta(
            $post_id,
            '_ch_hero_caption_motion',
            naigai_iez_admin_sanitize_caption_motion(wp_unslash($_POST['_ch_hero_caption_motion']))
        );
    }
}, 70);

if (!function_exists('naigai_iez_admin_motion_ui_final')) {
    function naigai_iez_admin_motion_ui_final() {
        global $post;

        if (!$post || $post->post_type !== 'page') {
            return;
        }

        $caption_motion = get_post_meta($post->ID, '_ch_hero_caption_motion', true);
        if ($caption_motion === '') {
            $caption_motion = 'none';
        }
        ?>
        <script>
        document.addEventListener('DOMContentLoaded', function () {
            var motion = document.querySelector('[name="_ch_hero_motion"]');

            if (motion) {
                var current = motion.value || 'kenburns-top';
                var map = {
                    'zoom-in': 'kenburns-top',
                    'zoom-out': 'kenburns-bottom',
                    'pan-left': 'kenburns-left',
                    'pan-right': 'kenburns-right',
                    'pan-up': 'kenburns-top',
                    'pan-down': 'kenburns-bottom',
                    'fade': 'kenburns-top',
                    'none': 'none',
                    'kenburns-top': 'kenburns-top',
                    'kenburns-bottom': 'kenburns-bottom',
                    'kenburns-left': 'kenburns-left',
                    'kenburns-right': 'kenburns-right'
                };

                current = map[current] || 'kenburns-top';

                motion.innerHTML = ''
                    + '<option value="kenburns-top">kenburns-top：上基準でゆっくり拡大</option>'
                    + '<option value="kenburns-bottom">kenburns-bottom：下基準でゆっくり拡大</option>'
                    + '<option value="kenburns-left">kenburns-left：左へ流しながら拡大</option>'
                    + '<option value="kenburns-right">kenburns-right：右へ流しながら拡大</option>'
                    + '<option value="none">none：動かさない</option>';

                motion.value = current;
            }

            if (!document.querySelector('[name="_ch_hero_caption_motion"]')) {
                var anchor =
                    document.querySelector('[name="_ch_hero_motion"]') ||
                    document.querySelector('[name="_ch_hero_interval"]');

                if (anchor) {
                    var tr = anchor.closest('tr');
                    var next = document.createElement('tr');

                    next.innerHTML = ''
                        + '<th scope="row"><label for="_ch_hero_caption_motion">Hero 見出しモーション</label></th>'
                        + '<td>'
                        + '<select id="_ch_hero_caption_motion" name="_ch_hero_caption_motion" class="regular-text">'
                        + '<option value="none">none：動かさない</option>'
                        + '<option value="focus">focus：文字をフォーカス表示</option>'
                        + '<option value="slide-up">slide-up：下から表示</option>'
                        + '</select>'
                        + '<p class="description">caption / H1 / lead だけに適用。CTAボタンは固定。</p>'
                        + '</td>';

                    tr.insertAdjacentElement('afterend', next);
                    next.querySelector('select').value = <?php echo wp_json_encode($caption_motion); ?>;
                }
            }
        });
        </script>
        <?php
    }
}

add_action('admin_footer-post.php', 'naigai_iez_admin_motion_ui_final');
add_action('admin_footer-post-new.php', 'naigai_iez_admin_motion_ui_final');
/* IEZ_HERO_MOTION_ADMIN_FINAL_END */


/* IEZ_HERO_SAVE_NEW_KEYS_ONLY_START */

/**
 * ============================================================
 * IEZ_HERO_SAVE_POLICY_20260806
 * 家づくり固定ページ Hero 正式保存処理
 * ============================================================
 *
 * 【正式保存先】
 *
 * Heroは _ch_hero_* だけを使用する。
 *
 *
 * ------------------------------------------------------------
 * Hero文字
 * ------------------------------------------------------------
 *
 * _ch_hero_kicker
 * _ch_hero_title
 * _ch_hero_lead
 *
 *
 * ------------------------------------------------------------
 * Heroメディア
 * ------------------------------------------------------------
 *
 * _ch_hero_image_id
 * _ch_hero_gallery_ids
 * _ch_hero_video_mp4_id
 *
 *
 * ------------------------------------------------------------
 * Hero CTA
 * ------------------------------------------------------------
 *
 * メイン:
 *
 * _ch_hero_cta_text
 * _ch_hero_cta_url
 *
 * サブ:
 *
 * _ch_hero_sub_cta_text
 * _ch_hero_sub_cta_url
 *
 *
 * ============================================================
 * CTAについて重要
 * ============================================================
 *
 * CTAは「文言だけ」入力してもフロントには表示しない。
 *
 * 必ず
 *
 *     CTA文言
 *          ＋
 *     CTA URL
 *
 * の両方を入力する。
 *
 * URLまで入力しないとCTAは表示しない。
 *
 *
 * 例:
 *
 * CTA文言:
 *     家づくり相談をする
 *
 * CTA URL:
 *     /iezukuri/contact/
 *
 *      ↓
 *
 * この2つが両方ある場合だけCTAを表示する。
 *
 *
 * 文言だけ:
 *     表示しない
 *
 * URLだけ:
 *     表示しない
 *
 *
 * ============================================================
 * 削除について
 * ============================================================
 *
 * 管理画面で値を削除した場合は
 * delete_post_meta() を実行する。
 *
 * 以前のように空値を保存したままにしたり、
 * 旧 _hub_* Heroメタを再利用したりしない。
 * ============================================================
 */

if (!function_exists('naigai_iez_save_hero_new_keys_only')) {

    function naigai_iez_save_hero_new_keys_only($post_id)
    {
        /*
         * 自動保存では変更しない。
         */
        if (
            defined('DOING_AUTOSAVE')
            && DOING_AUTOSAVE
        ) {
            return;
        }

        /*
         * リビジョン保存では変更しない。
         */
        if (
            wp_is_post_revision($post_id)
            || wp_is_post_autosave($post_id)
        ) {
            return;
        }

        /*
         * 固定ページ以外では動かさない。
         */
        if (
            get_post_type($post_id)
            !== 'page'
        ) {
            return;
        }

        /*
         * 編集権限が無いユーザーでは保存しない。
         */
        if (
            !current_user_can(
                'edit_post',
                $post_id
            )
        ) {
            return;
        }

        /*
         * ====================================================
         * nonce
         * ====================================================
         *
         * 実際の
         * 「家づくり 固定ページ入力」
         * メタボックスが出しているnonceと同じものを確認する。
         *
         * ここが以前一致していなかったため、
         * CTAなどが保存されていなかった。
         */
        if (
            !isset(
                $_POST[
                    'naigai_iez_fixed_page_nonce'
                ]
            )
            ||
            !wp_verify_nonce(
                sanitize_text_field(
                    wp_unslash(
                        $_POST[
                            'naigai_iez_fixed_page_nonce'
                        ]
                    )
                ),
                'naigai_iez_save_fixed_page_input'
            )
        ) {
            return;
        }


        /*
         * ====================================================
         * 1. 一行テキスト
         * ====================================================
         */

        $text_fields = array(
            '_ch_hero_engine',
            '_ch_hero_kicker',
            '_ch_hero_title',
            '_ch_hero_motion',
            '_ch_hero_caption_motion',

            '_ch_hero_cta_text',
            '_ch_hero_sub_cta_text',
        );

        foreach ($text_fields as $key) {

            /*
             * 入力が無い場合も空扱いにする。
             *
             * これによって以前の値を確実に削除できる。
             */
            $value = isset($_POST[$key])
                ? sanitize_text_field(
                    wp_unslash(
                        $_POST[$key]
                    )
                )
                : '';

            if ($value === '') {

                delete_post_meta(
                    $post_id,
                    $key
                );

            } else {

                update_post_meta(
                    $post_id,
                    $key,
                    $value
                );
            }
        }


        /*
         * ====================================================
         * 2. 複数行Hero本文
         * ====================================================
         */

        $lead = isset(
            $_POST['_ch_hero_lead']
        )
            ? sanitize_textarea_field(
                wp_unslash(
                    $_POST['_ch_hero_lead']
                )
            )
            : '';

        if ($lead === '') {

            delete_post_meta(
                $post_id,
                '_ch_hero_lead'
            );

        } else {

            update_post_meta(
                $post_id,
                '_ch_hero_lead',
                $lead
            );
        }


        /*
         * ====================================================
         * 3. CTA URL
         * ====================================================
         *
         * URLはesc_url_raw()して保存する。
         *
         * CTA文言とURLは別々に保存するが、
         * フロント表示は「両方ある時だけ」。
         *
         * URLまで入力しないとCTAは表示しない。
         * ====================================================
         */

        $url_fields = array(
            '_ch_hero_cta_url',
            '_ch_hero_sub_cta_url',
        );

        foreach ($url_fields as $key) {

            $value = isset($_POST[$key])
                ? esc_url_raw(
                    wp_unslash(
                        $_POST[$key]
                    )
                )
                : '';

            if ($value === '') {

                delete_post_meta(
                    $post_id,
                    $key
                );

            } else {

                update_post_meta(
                    $post_id,
                    $key,
                    $value
                );
            }
        }


        /*
         * ====================================================
         * 4. 単体画像 / MP4
         * ====================================================
         *
         * 管理画面で「削除」した場合、
         * hidden input が空または0になる。
         *
         * その場合はDBのメタそのものを削除する。
         */
        $id_fields = array(
            '_ch_hero_image_id',
            '_ch_hero_video_mp4_id',
        );

        foreach ($id_fields as $key) {

            $value = isset($_POST[$key])
                ? absint(
                    wp_unslash(
                        $_POST[$key]
                    )
                )
                : 0;

            if (!$value) {

                delete_post_meta(
                    $post_id,
                    $key
                );

            } else {

                update_post_meta(
                    $post_id,
                    $key,
                    $value
                );
            }
        }


        /*
         * ====================================================
         * 5. Heroギャラリー
         * ====================================================
         *
         * ギャラリーから全画像を削除した場合も
         * 必ずDBを空にする。
         *
         * POST自体が無い場合にも
         * 古いギャラリーを残さない。
         */
        $gallery_raw = isset(
            $_POST['_ch_hero_gallery_ids']
        )
            ? (string) wp_unslash(
                $_POST['_ch_hero_gallery_ids']
            )
            : '';

        $gallery_ids = array_values(
            array_unique(
                array_filter(
                    array_map(
                        'absint',
                        preg_split(
                            '/[\s,]+/',
                            trim($gallery_raw)
                        )
                    )
                )
            )
        );

        if (empty($gallery_ids)) {

            delete_post_meta(
                $post_id,
                '_ch_hero_gallery_ids'
            );

        } else {

            update_post_meta(
                $post_id,
                '_ch_hero_gallery_ids',
                implode(
                    ',',
                    $gallery_ids
                )
            );
        }


        /*
         * ====================================================
         * 6. スライド切替時間
         * ====================================================
         */

        $interval_raw = isset(
            $_POST['_ch_hero_interval']
        )
            ? absint(
                wp_unslash(
                    $_POST['_ch_hero_interval']
                )
            )
            : 0;

        if ($interval_raw > 0) {

            update_post_meta(
                $post_id,
                '_ch_hero_interval',
                max(
                    4000,
                    $interval_raw
                )
            );

        } else {

            delete_post_meta(
                $post_id,
                '_ch_hero_interval'
            );
        }
    }
}


/*
 * priority 90:
 *
 * 他の通常本文保存が終わったあと、
 * 最終的にHeroの正式キー _ch_hero_* を確定させる。
 */
add_action(
    'save_post_page',
    'naigai_iez_save_hero_new_keys_only',
    90
);

/* IEZ_HERO_SAVE_NEW_KEYS_ONLY_END */


/* IEZ_HERO_PER_SLIDE_TEXT_UI_START */
/**
 * Hero画像ごとの本文・CodePen風motion UI
 * 保存メタキー: _ch_hero_slides_json
 */
if (!function_exists('naigai_iez_save_per_slide_text_ui')) {
    function naigai_iez_save_per_slide_text_ui($post_id) {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        if (
            !isset($_POST['naigai_iez_fixed_page_nonce']) ||
            !wp_verify_nonce(
                sanitize_text_field(wp_unslash($_POST['naigai_iez_fixed_page_nonce'])),
                'naigai_iez_save_fixed_page_input'
            )
        ) {
            return;
        }

        if (!isset($_POST['_ch_hero_slides_json'])) {

            /*
             * IEZ_HERO_SLIDE_DELETE_POLICY_20260806
             *
             * ギャラリー画像をすべて削除すると、
             * スライド用hidden input自体が無くなる場合がある。
             *
             * その場合は以前のスライドJSONを残さない。
             */
            delete_post_meta(
                $post_id,
                '_ch_hero_slides_json'
            );

            return;
        }

        $rows = json_decode((string) wp_unslash($_POST['_ch_hero_slides_json']), true);

        if (!is_array($rows)) {
            delete_post_meta($post_id, '_ch_hero_slides_json');
            return;
        }

        $clean = array();

        foreach ($rows as $row) {
            if (!is_array($row)) {
                continue;
            }

            $image_id = absint($row['image_id'] ?? 0);

            if (!$image_id) {
                continue;
            }

            $motion = sanitize_key($row['motion'] ?? 'kenburns-top');
            if (!in_array($motion, array('none', 'kenburns-top', 'kenburns-bottom', 'kenburns-left', 'kenburns-right'), true)) {
                $motion = 'kenburns-top';
            }

            $caption_motion = sanitize_key($row['caption_motion'] ?? 'none');
            if (!in_array($caption_motion, array('none', 'focus', 'slide-up'), true)) {
                $caption_motion = 'none';
            }

            $clean[] = array(
                'image_id'       => $image_id,
                'caption'        => sanitize_text_field($row['caption'] ?? ''),
                'title'          => sanitize_text_field($row['title'] ?? ''),
                'lead'           => sanitize_textarea_field($row['lead'] ?? ''),
                'motion'         => $motion,
                'caption_motion' => $caption_motion,
            );
        }

        if (empty($clean)) {
            delete_post_meta($post_id, '_ch_hero_slides_json');
        } else {
            update_post_meta($post_id, '_ch_hero_slides_json', wp_json_encode($clean, JSON_UNESCAPED_UNICODE));
        }
    }
}

add_action('save_post_page', 'naigai_iez_save_per_slide_text_ui', 95);

if (!function_exists('naigai_iez_render_per_slide_text_ui')) {
    function naigai_iez_render_per_slide_text_ui() {
        global $post;

        if (!$post || $post->post_type !== 'page') {
            return;
        }

        $gallery = get_post_meta($post->ID, '_ch_hero_gallery_ids', true);
        $single = get_post_meta($post->ID, '_ch_hero_image_id', true);

        $ids = array_values(array_filter(array_map('absint', explode(',', (string) $gallery))));

        if (empty($ids) && $single) {
            $ids = array(absint($single));
        }

        $saved = json_decode((string) get_post_meta($post->ID, '_ch_hero_slides_json', true), true);
        $map = array();

        if (is_array($saved)) {
            foreach ($saved as $row) {
                $image_id = absint($row['image_id'] ?? 0);
                if ($image_id) {
                    $map[$image_id] = $row;
                }
            }
        }

        $rows = array();

        foreach ($ids as $index => $image_id) {
            $row = $map[$image_id] ?? array();

            $rows[] = array(
                'image_id'       => $image_id,
                'thumb'          => wp_get_attachment_image_url($image_id, 'thumbnail'),
                'caption'        => $row['caption'] ?? '',
                'title'          => $row['title'] ?? '',
                'lead'           => $row['lead'] ?? '',
                'motion'         => $row['motion'] ?? array('kenburns-top', 'kenburns-bottom', 'kenburns-left', 'kenburns-right')[$index % 4],
                'caption_motion' => $row['caption_motion'] ?? 'none',
            );
        }
        ?>
        <script>
        document.addEventListener('DOMContentLoaded', function () {
          var rows = <?php echo wp_json_encode($rows, JSON_UNESCAPED_UNICODE); ?>;

          if (!rows.length) {
            return;
          }

          var anchor =
            document.querySelector('[name="_ch_hero_gallery_ids"]') ||
            document.querySelector('[name="_ch_hero_image_id"]');

          if (!anchor || document.querySelector('.naigai-iez-slide-text-box')) {
            return;
          }

          function esc(value) {
            return String(value || '')
              .replace(/&/g, '&amp;')
              .replace(/</g, '&lt;')
              .replace(/>/g, '&gt;')
              .replace(/"/g, '&quot;')
              .replace(/'/g, '&#039;');
          }

          function opt(value, label, current) {
            return '<option value="' + value + '"' + (value === current ? ' selected' : '') + '>' + label + '</option>';
          }

          var box = document.createElement('div');
          box.className = 'naigai-iez-slide-text-box';

          var html = '';
          html += '<h4>Hero 画像ごとの本文・動き</h4>';
          html += '<p class="description">Hero複数画像に合わせて、画像ごとの小見出し・H1・本文・動きを設定します。H1 / p の動きは画像切替間隔を基準にします。</p>';
          html += '<input type="hidden" id="_ch_hero_slides_json" name="_ch_hero_slides_json">';

          rows.forEach(function (row, index) {
            html += '<div class="naigai-iez-slide-text-row" data-image-id="' + row.image_id + '">';
            html += '<div>';
            if (row.thumb) {
              html += '<img src="' + row.thumb + '" alt="">';
            }
            html += '<p style="margin:6px 0 0;font-size:11px;color:#646970;">ID: ' + row.image_id + '</p>';
            html += '</div>';

            html += '<div>';
            html += '<label>小見出し</label>';
            html += '<input type="text" data-field="caption" value="' + esc(row.caption) + '">';
            html += '<label style="margin-top:8px;">見出し（H1）</label>';
            html += '<input type="text" data-field="title" value="' + esc(row.title) + '">';
            html += '<label style="margin-top:8px;">本文（p）</label>';
            html += '<textarea data-field="lead">' + esc(row.lead) + '</textarea>';
            html += '</div>';

            html += '<div>';
            html += '<label>画像の動き</label>';
            html += '<select data-field="motion">';
            html += opt('kenburns-top', 'kenburns-top：上基準', row.motion);
            html += opt('kenburns-bottom', 'kenburns-bottom：下基準', row.motion);
            html += opt('kenburns-left', 'kenburns-left：左へ流す', row.motion);
            html += opt('kenburns-right', 'kenburns-right：右へ流す', row.motion);
            html += opt('none', 'none：動かさない', row.motion);
            html += '</select>';

            html += '<label style="margin-top:8px;">文字の動き（H1 / p）</label>';
            html += '<select data-field="caption_motion">';
            html += opt('none', 'none：動かさない', row.caption_motion);
            html += opt('focus', 'focus：ゆっくりフォーカス', row.caption_motion);
            html += opt('slide-up', 'slide-up：ゆっくり下から表示', row.caption_motion);
            html += '</select>';
            html += '</div>';

            html += '</div>';
          });

          box.innerHTML = html;

          var tr = anchor.closest('tr');

          if (tr && tr.parentNode && String(tr.tagName).toLowerCase() === 'tr') {
            var wrapRow = document.createElement('tr');
            wrapRow.className = 'naigai-iez-slide-text-table-row';

            var wrapCell = document.createElement('td');
            wrapCell.colSpan = 2;
            wrapCell.appendChild(box);

            wrapRow.appendChild(wrapCell);
            tr.insertAdjacentElement('afterend', wrapRow);
          } else {
            anchor.insertAdjacentElement('afterend', box);
          }

          function sync() {
            var output = [];

            box.querySelectorAll('.naigai-iez-slide-text-row').forEach(function (row) {
              var item = {
                image_id: parseInt(row.getAttribute('data-image-id'), 10),
                caption: '',
                title: '',
                lead: '',
                motion: 'kenburns-top',
                caption_motion: 'none'
              };

              row.querySelectorAll('[data-field]').forEach(function (field) {
                item[field.getAttribute('data-field')] = field.value || '';
              });

              output.push(item);
            });

            document.getElementById('_ch_hero_slides_json').value = JSON.stringify(output);
          }

          box.addEventListener('input', sync);
          box.addEventListener('change', sync);
          sync();
        });
        </script>
        <?php
    }
}

add_action('admin_footer-post.php', 'naigai_iez_render_per_slide_text_ui');
add_action('admin_footer-post-new.php', 'naigai_iez_render_per_slide_text_ui');
/* IEZ_HERO_PER_SLIDE_TEXT_UI_END */

/**
 * ============================================================
 * IEZ_CONTENT_ITEMS_SYSTEM_V2_20260806
 * サブコンテンツ「画像＋テキスト」
 * 各コンテンツ個別レイアウト版
 * ============================================================
 *
 * 【管理単位】
 *
 * 1つのコンテンツ =
 *
 *     画像
 *     見出し
 *     文章
 *     配置
 *
 *
 * 【各コンテンツで選択できる配置】
 *
 * card
 *
 *     画像
 *      ↓
 *     文章
 *
 *
 * image-left
 *
 *     画像 ｜ 文章
 *
 *
 * image-right
 *
 *     文章 ｜ 画像
 *
 *
 * ============================================================
 * 単体画像と複数画像
 * ============================================================
 *
 * 別々の機能にはしない。
 *
 * _ch_content_items が1件
 *
 *     → 単体
 *
 * _ch_content_items が複数件
 *
 *     → 複数
 *
 *
 * cardを複数続けて登録した場合は、
 * PCでは横並びカードとして表示する。
 *
 *
 * ============================================================
 * Mobile
 * ============================================================
 *
 * PCで image-right を選択していても、
 * モバイルでは必ず
 *
 *     画像
 *      ↓
 *     文章
 *
 * の順に戻す。
 *
 *
 * ============================================================
 * 保存先
 * ============================================================
 *
 * _ch_content_items
 *
 * の各項目へ、
 *
 *     image_id
 *     title
 *     text
 *     layout
 *
 * を保存する。
 *
 *
 * 旧 _ch_content_layout は新規保存には使用しない。
 * 既存データ互換の読み取りだけに使用する。
 * ============================================================
 */


/**
 * サブコンテンツ管理UIを表示する。
 *
 * @param WP_Post $post 現在編集中の固定ページ。
 * @return void
 */
function naigai_iez_admin_render_content_items($post) {

    if (!$post instanceof WP_Post) {
        return;
    }


    /*
     * WordPress標準の
     * 「メディアを追加 / 画像を選択」
     * を使用するために読み込む。
     */
    wp_enqueue_media();


    /*
     * ========================================================
     * 新しいコンテンツ一覧
     * ========================================================
     */
    $items = get_post_meta(
        $post->ID,
        '_ch_content_items',
        true
    );


    if (!is_array($items)) {
        $items = array();
    }


    /*
     * ========================================================
     * 旧全体レイアウト
     * ========================================================
     *
     * 旧版ですでに保存したデータとの互換だけに使う。
     *
     * 新しく保存するときは
     * 各コンテンツの layout を使う。
     */
    $legacy_layout = sanitize_key(
        (string) get_post_meta(
            $post->ID,
            '_ch_content_layout',
            true
        )
    );


    /*
     * まだコンテンツが1件もない場合でも、
     * 管理画面には最初の空行を1件表示する。
     */
    if (!$items) {

        $items = array(
            array(
                'image_id' => 0,
                'title'    => '',
                'text'     => '',
                'layout'   => 'image-left',
            ),
        );
    }

    ?>

    <tr>

        <th scope="row">
            画像＋テキスト
        </th>


        <td>

            <div
                id="iez-content-items-admin"
                class="iez-content-items-admin"
            >

                <?php
                foreach (
                    $items
                    as $index => $item
                ) :

                    if (!is_array($item)) {
                        continue;
                    }


                    $image_id = absint(
                        $item['image_id'] ?? 0
                    );


                    $title = sanitize_text_field(
                        (string) (
                            $item['title']
                            ?? ''
                        )
                    );


                    $item_text = sanitize_textarea_field(
                        (string) (
                            $item['text']
                            ?? ''
                        )
                    );


                    /*
                     * =========================================
                     * 各コンテンツの配置
                     * =========================================
                     */
                    $item_layout = sanitize_key(
                        (string) (
                            $item['layout']
                            ?? ''
                        )
                    );


                    $allowed_layouts = array(
                        'card',
                        'image-left',
                        'image-right',
                    );


                    /*
                     * 旧データにlayoutがない場合だけ、
                     * 以前のページ全体レイアウトを引き継ぐ。
                     */
                    if (
                        !in_array(
                            $item_layout,
                            $allowed_layouts,
                            true
                        )
                    ) {

                        if (
                            $legacy_layout === 'image-right'
                        ) {

                            $item_layout =
                                'image-right';

                        } elseif (
                            $legacy_layout === 'cards'
                        ) {

                            $item_layout =
                                'card';

                        } elseif (
                            $legacy_layout === 'alternate'
                        ) {

                            $item_layout =
                                ($index % 2 === 0)
                                    ? 'image-left'
                                    : 'image-right';

                        } else {

                            $item_layout =
                                'image-left';
                        }
                    }

                    ?>

                    <div
                        class="iez-content-item-admin"
                        data-index="<?php echo esc_attr($index); ?>"
                    >


                        <div
                            class="iez-content-item-admin__header"
                        >

                            <strong>

                                コンテンツ

                                <span
                                    class="iez-content-item-number"
                                >
                                    <?php echo esc_html($index + 1); ?>
                                </span>

                            </strong>


                            <div
                                class="iez-content-item-admin__buttons"
                            >

                                <button
                                    type="button"
                                    class="button iez-content-item-up"
                                >
                                    ↑
                                </button>


                                <button
                                    type="button"
                                    class="button iez-content-item-down"
                                >
                                    ↓
                                </button>


                                <button
                                    type="button"
                                    class="button iez-content-item-remove"
                                >
                                    削除
                                </button>

                            </div>

                        </div>


                        <!--
                        ======================================================
                        このコンテンツだけのレイアウト
                        ======================================================

                        ページ全体ではなく、
                        この1件だけに適用する。

                        そのため、

                        コンテンツ1 = 画像左
                        コンテンツ2 = 画像右
                        コンテンツ3 = カード

                        のような混在が可能。
                        ======================================================
                        -->

                        <p>

                            <label>
                                <strong>
                                    このコンテンツの配置
                                </strong>
                            </label>

                            <br>

                            <select
                                class="iez-content-item-layout"
                                name="naigai_iez_content_items[<?php echo esc_attr($index); ?>][layout]"
                                style="min-width:280px;"
                            >

                                <option
                                    value="image-left"
                                    <?php selected($item_layout, 'image-left'); ?>
                                >
                                    画像左 ＋ 文章右
                                </option>


                                <option
                                    value="image-right"
                                    <?php selected($item_layout, 'image-right'); ?>
                                >
                                    文章左 ＋ 画像右
                                </option>


                                <option
                                    value="card"
                                    <?php selected($item_layout, 'card'); ?>
                                >
                                    画像上 ＋ 文章下（カード）
                                </option>

                            </select>

                        </p>


                        <!-- 画像 -->

                        <div
                            class="iez-content-item-admin__media"
                        >

                            <div
                                class="iez-content-image-preview"
                            >

                                <?php
                                if ($image_id) {

                                    echo wp_get_attachment_image(
                                        $image_id,
                                        'thumbnail'
                                    );
                                }
                                ?>

                            </div>


                            <input
                                type="hidden"
                                class="iez-content-image-id"
                                name="naigai_iez_content_items[<?php echo esc_attr($index); ?>][image_id]"
                                value="<?php echo esc_attr($image_id); ?>"
                            >


                            <button
                                type="button"
                                class="button iez-content-image-select"
                            >
                                画像を選択
                            </button>


                            <button
                                type="button"
                                class="button iez-content-image-clear"
                            >
                                画像を外す
                            </button>

                        </div>


                        <!-- 見出し -->

                        <p>

                            <label>
                                <strong>
                                    見出し（任意）
                                </strong>
                            </label>

                            <input
                                type="text"
                                class="widefat iez-content-item-title"
                                name="naigai_iez_content_items[<?php echo esc_attr($index); ?>][title]"
                                value="<?php echo esc_attr($title); ?>"
                            >

                        </p>


                        <!-- 本文 -->

                        <p>

                            <label>
                                <strong>
                                    画像に対応する文章
                                </strong>
                            </label>

                            <textarea
                                class="widefat iez-content-item-text"
                                name="naigai_iez_content_items[<?php echo esc_attr($index); ?>][text]"
                                rows="4"
                            ><?php echo esc_textarea($item_text); ?></textarea>

                        </p>


                    </div>

                <?php endforeach; ?>

            </div>


            <p>

                <button
                    type="button"
                    class="button button-secondary"
                    id="iez-content-item-add"
                >
                    ＋ コンテンツを追加
                </button>

            </p>


            <p class="description">

                コンテンツごとに
                「画像左」「画像右」「カード」
                を選択できます。

                モバイルではすべて
                「画像 → 文章」の1列表示になります。

            </p>


            <style>

                /*
                 * =================================================
                 * WordPress管理画面専用
                 * =================================================
                 */

                .iez-content-items-admin {
                    display: grid;
                    gap: 16px;
                }


                .iez-content-item-admin {
                    padding: 16px;
                    border: 1px solid #dcdcde;
                    border-radius: 8px;
                    background: #fff;
                }


                .iez-content-item-admin__header {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    gap: 12px;
                    margin-bottom: 16px;
                }


                .iez-content-item-admin__buttons {
                    display: flex;
                    gap: 6px;
                }


                .iez-content-item-admin__media {
                    display: flex;
                    align-items: center;
                    flex-wrap: wrap;
                    gap: 10px;
                    margin: 14px 0 16px;
                }


                .iez-content-image-preview {
                    width: 120px;
                    min-height: 80px;
                }


                .iez-content-image-preview img {
                    display: block;
                    width: 120px;
                    height: 80px;
                    object-fit: cover;
                }

            </style>


            <script>
            jQuery(function ($) {

                var $wrap =
                    $('#iez-content-items-admin');


                /*
                 * =================================================
                 * 並び替え後のinput nameを再構築
                 * =================================================
                 *
                 * layoutも含めて
                 *
                 * [0]
                 * [1]
                 * [2]
                 *
                 * の連番に直す。
                 */
                function renumberItems() {

                    $wrap
                        .find('.iez-content-item-admin')
                        .each(function (index) {

                            var $row =
                                $(this);


                            $row.attr(
                                'data-index',
                                index
                            );


                            $row
                                .find(
                                    '.iez-content-item-number'
                                )
                                .text(
                                    index + 1
                                );


                            $row
                                .find(
                                    '.iez-content-item-layout'
                                )
                                .attr(
                                    'name',
                                    'naigai_iez_content_items['
                                    + index
                                    + '][layout]'
                                );


                            $row
                                .find(
                                    '.iez-content-image-id'
                                )
                                .attr(
                                    'name',
                                    'naigai_iez_content_items['
                                    + index
                                    + '][image_id]'
                                );


                            $row
                                .find(
                                    '.iez-content-item-title'
                                )
                                .attr(
                                    'name',
                                    'naigai_iez_content_items['
                                    + index
                                    + '][title]'
                                );


                            $row
                                .find(
                                    '.iez-content-item-text'
                                )
                                .attr(
                                    'name',
                                    'naigai_iez_content_items['
                                    + index
                                    + '][text]'
                                );
                        });
                }


                /*
                 * =================================================
                 * 新しいコンテンツ行
                 * =================================================
                 *
                 * 初期配置は
                 *
                 *     画像左＋文章右
                 *
                 * にしている。
                 */
                function newItemHtml() {

                    return `

                        <div class="iez-content-item-admin">

                            <div class="iez-content-item-admin__header">

                                <strong>
                                    コンテンツ
                                    <span class="iez-content-item-number"></span>
                                </strong>

                                <div class="iez-content-item-admin__buttons">

                                    <button
                                        type="button"
                                        class="button iez-content-item-up"
                                    >
                                        ↑
                                    </button>

                                    <button
                                        type="button"
                                        class="button iez-content-item-down"
                                    >
                                        ↓
                                    </button>

                                    <button
                                        type="button"
                                        class="button iez-content-item-remove"
                                    >
                                        削除
                                    </button>

                                </div>

                            </div>


                            <p>

                                <label>
                                    <strong>
                                        このコンテンツの配置
                                    </strong>
                                </label>

                                <br>

                                <select
                                    class="iez-content-item-layout"
                                    style="min-width:280px;"
                                >

                                    <option value="image-left">
                                        画像左 ＋ 文章右
                                    </option>

                                    <option value="image-right">
                                        文章左 ＋ 画像右
                                    </option>

                                    <option value="card">
                                        画像上 ＋ 文章下（カード）
                                    </option>

                                </select>

                            </p>


                            <div class="iez-content-item-admin__media">

                                <div class="iez-content-image-preview"></div>

                                <input
                                    type="hidden"
                                    class="iez-content-image-id"
                                    value=""
                                >

                                <button
                                    type="button"
                                    class="button iez-content-image-select"
                                >
                                    画像を選択
                                </button>

                                <button
                                    type="button"
                                    class="button iez-content-image-clear"
                                >
                                    画像を外す
                                </button>

                            </div>


                            <p>

                                <label>
                                    <strong>
                                        見出し（任意）
                                    </strong>
                                </label>

                                <input
                                    type="text"
                                    class="widefat iez-content-item-title"
                                    value=""
                                >

                            </p>


                            <p>

                                <label>
                                    <strong>
                                        画像に対応する文章
                                    </strong>
                                </label>

                                <textarea
                                    class="widefat iez-content-item-text"
                                    rows="4"
                                ></textarea>

                            </p>

                        </div>

                    `;
                }


                /*
                 * 追加
                 */
                $('#iez-content-item-add')
                    .on(
                        'click',
                        function () {

                            $wrap.append(
                                newItemHtml()
                            );

                            renumberItems();
                        }
                    );


                /*
                 * 削除
                 */
                $wrap.on(
                    'click',
                    '.iez-content-item-remove',
                    function () {

                        $(this)
                            .closest(
                                '.iez-content-item-admin'
                            )
                            .remove();

                        renumberItems();
                    }
                );


                /*
                 * 上へ
                 */
                $wrap.on(
                    'click',
                    '.iez-content-item-up',
                    function () {

                        var $row =
                            $(this)
                                .closest(
                                    '.iez-content-item-admin'
                                );

                        var $prev =
                            $row.prev(
                                '.iez-content-item-admin'
                            );


                        if ($prev.length) {

                            $row.insertBefore(
                                $prev
                            );

                            renumberItems();
                        }
                    }
                );


                /*
                 * 下へ
                 */
                $wrap.on(
                    'click',
                    '.iez-content-item-down',
                    function () {

                        var $row =
                            $(this)
                                .closest(
                                    '.iez-content-item-admin'
                                );

                        var $next =
                            $row.next(
                                '.iez-content-item-admin'
                            );


                        if ($next.length) {

                            $row.insertAfter(
                                $next
                            );

                            renumberItems();
                        }
                    }
                );


                /*
                 * =================================================
                 * WordPressメディアライブラリ
                 * =================================================
                 */
                $wrap.on(
                    'click',
                    '.iez-content-image-select',
                    function () {

                        var $row =
                            $(this)
                                .closest(
                                    '.iez-content-item-admin'
                                );


                        if (
                            typeof wp === 'undefined'
                            || !wp.media
                        ) {

                            alert(
                                'メディアライブラリを読み込めません。'
                            );

                            return;
                        }


                        var frame =
                            wp.media({

                                title:
                                    '画像を選択',

                                button: {
                                    text:
                                        'この画像を使用'
                                },

                                multiple:
                                    false
                            });


                        frame.on(
                            'select',
                            function () {

                                var attachment =
                                    frame
                                        .state()
                                        .get('selection')
                                        .first()
                                        .toJSON();


                                var imageUrl =
                                    attachment.url;


                                if (
                                    attachment.sizes
                                    && attachment.sizes.thumbnail
                                ) {

                                    imageUrl =
                                        attachment
                                            .sizes
                                            .thumbnail
                                            .url;
                                }


                                $row
                                    .find(
                                        '.iez-content-image-id'
                                    )
                                    .val(
                                        attachment.id
                                    );


                                $row
                                    .find(
                                        '.iez-content-image-preview'
                                    )
                                    .html(
                                        '<img src="'
                                        + imageUrl
                                        + '" alt="">'
                                    );
                            }
                        );


                        frame.open();
                    }
                );


                /*
                 * 画像解除。
                 *
                 * 文章や配置は残す。
                 */
                $wrap.on(
                    'click',
                    '.iez-content-image-clear',
                    function () {

                        var $row =
                            $(this)
                                .closest(
                                    '.iez-content-item-admin'
                                );


                        $row
                            .find(
                                '.iez-content-image-id'
                            )
                            .val('');


                        $row
                            .find(
                                '.iez-content-image-preview'
                            )
                            .empty();
                    }
                );


                /*
                 * 保存直前にも番号を確定。
                 */
                $('#post')
                    .on(
                        'submit',
                        function () {

                            renumberItems();
                        }
                    );


                renumberItems();

            });
            </script>

        </td>

    </tr>

    <?php
}


/**
 * ============================================================
 * 各コンテンツ保存処理
 * ============================================================
 *
 * 1件ごとに
 *
 *     layout
 *     image_id
 *     title
 *     text
 *
 * を保存する。
 *
 *
 * ページ全体の
 *
 *     _ch_content_layout
 *
 * はもう新規保存しない。
 *
 * ============================================================
 *
 * @param int $post_id 固定ページID。
 * @return void
 */
function naigai_iez_save_content_items($post_id) {

    /*
     * このUIがPOSTされていないページでは何もしない。
     */
    if (
        !isset(
            $_POST['naigai_iez_content_items']
        )
    ) {
        return;
    }


    /*
     * 家づくり固定ページのnonce確認。
     */
    if (
        !isset(
            $_POST[
                'naigai_iez_fixed_page_nonce'
            ]
        )
    ) {
        return;
    }


    if (
        !wp_verify_nonce(
            sanitize_text_field(
                wp_unslash(
                    $_POST[
                        'naigai_iez_fixed_page_nonce'
                    ]
                )
            ),
            'naigai_iez_save_fixed_page_input'
        )
    ) {
        return;
    }


    /*
     * 自動保存では保存しない。
     */
    if (
        defined('DOING_AUTOSAVE')
        && DOING_AUTOSAVE
    ) {
        return;
    }


    /*
     * 編集権限確認。
     */
    if (
        !current_user_can(
            'edit_page',
            $post_id
        )
    ) {
        return;
    }


    $raw_items =
        is_array(
            $_POST[
                'naigai_iez_content_items'
            ]
        )
            ? wp_unslash(
                $_POST[
                    'naigai_iez_content_items'
                ]
            )
            : array();


    $items =
        array();


    $allowed_layouts =
        array(
            'card',
            'image-left',
            'image-right',
        );


    foreach (
        $raw_items
        as $raw_item
    ) {

        if (
            !is_array(
                $raw_item
            )
        ) {
            continue;
        }


        /*
         * =============================================
         * 各コンテンツの配置
         * =============================================
         */
        $layout =
            sanitize_key(
                (string) (
                    $raw_item['layout']
                    ?? 'image-left'
                )
            );


        if (
            !in_array(
                $layout,
                $allowed_layouts,
                true
            )
        ) {

            $layout =
                'image-left';
        }


        /*
         * =============================================
         * 保存値
         * =============================================
         */
        $item =
            array(

                'layout' =>
                    $layout,

                'image_id' =>
                    absint(
                        $raw_item['image_id']
                        ?? 0
                    ),

                'title' =>
                    sanitize_text_field(
                        (string) (
                            $raw_item['title']
                            ?? ''
                        )
                    ),

                'text' =>
                    sanitize_textarea_field(
                        (string) (
                            $raw_item['text']
                            ?? ''
                        )
                    ),
            );


        /*
         * 画像・見出し・文章が全部空なら保存しない。
         *
         * layoutだけの空行はDBへ入れない。
         */
        if (
            !$item['image_id']
            && $item['title'] === ''
            && $item['text'] === ''
        ) {
            continue;
        }


        $items[] =
            $item;
    }


    if ($items) {

        update_post_meta(
            $post_id,
            '_ch_content_items',
            $items
        );

    } else {

        delete_post_meta(
            $post_id,
            '_ch_content_items'
        );
    }


    /*
     * ========================================================
     * 旧ページ全体レイアウトを削除
     * ========================================================
     *
     * 新方式では各コンテンツ自身がlayoutを持つため、
     * ページ全体レイアウトは不要。
     *
     * 新UIから保存したタイミングで整理する。
     */
    delete_post_meta(
        $post_id,
        '_ch_content_layout'
    );
}


add_action(
    'save_post_page',
    'naigai_iez_save_content_items',
    60
);

