<?php
/**
 * 家づくりプラン CPT 管理画面
 *
 * 対象:
 * - post_type: iez_plan
 *
 * 編集できるもの:
 * - プラン概要
 * - 外観写真
 * - 平面図 / 2F / 配置図
 * - 複数内装写真
 * - PDF
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('add_meta_boxes', function () {
    add_meta_box(
        'naigai_iez_plan_detail_metabox',
        '家づくりプラン詳細設定',
        'naigai_iez_plan_render_detail_metabox',
        'iez_plan',
        'normal',
        'high'
    );
});

function naigai_iez_plan_render_detail_metabox($post) {
    wp_nonce_field('naigai_iez_plan_save_detail_meta', 'naigai_iez_plan_detail_nonce');

    $text_fields = array(
        '_ch_plan_label' => array(
            'label' => 'プランラベル',
            'default' => '平屋 P-A',
            'type' => 'text',
        ),
        '_ch_plan_name' => array(
            'label' => 'プラン名',
            'default' => get_the_title($post),
            'type' => 'text',
        ),
        '_ch_plan_style' => array(
            'label' => 'テイスト',
            'default' => '洋風',
            'type' => 'text',
        ),
        '_ch_plan_catch' => array(
            'label' => '上部キャッチ',
            'type'  => 'text',
        ),
        '_ch_plan_layout' => array(
            'label' => '間取り',
            'default' => '2LDK',
            'type' => 'text',
        ),
        '_ch_plan_total_area' => array(
            'label' => '延床面積',
            'default' => '72㎡',
            'type' => 'text',
        ),
        '_ch_plan_tsubo' => array(
            'label' => '坪数',
            'default' => '約21.8坪',
            'type' => 'text',
        ),
        '_ch_plan_building_area' => array(
            'label' => '建築面積',
            'default' => '75㎡',
            'type' => 'text',
        ),
        '_ch_plan_family' => array(
            'label' => '想定家族',
            'default' => '夫婦・少人数世帯',
            'type' => 'text',
        ),
        '_ch_plan_description' => array(
            'label' => '説明文',
            'default' => '暮らしやすさをコンパクトにまとめた平屋プランです。家事動線、収納、採光、庭とのつながりを確認できます。',
            'type' => 'textarea',
        ),
    
        '_ch_plan_gallery_heading' => array(
            'label' => '複数内装写真：見出し',
            'type'  => 'text',
        ),
        '_ch_plan_gallery_text' => array(
            'label' => '複数内装写真：説明文',
            'type'  => 'textarea',
        ),
);

    $floor_type = get_post_meta($post->ID, '_ch_plan_floor_type', true);
    if ($floor_type === '') {
        $floor_type = 'one_story';
    }

    $image_fields = array(
        '_ch_plan_exterior_image_id' => array(
            'label' => '1段目：外観写真',
            'desc'  => '詳細ページの一番上に大きく表示します。',
        ),
        '_ch_plan_1f_image_id' => array(
            'label' => '2段目：平面図 / 1F',
            'desc'  => '平屋の場合は平面図として表示します。',
        ),
        '_ch_plan_2f_image_id' => array(
            'label' => '2F 平面図',
            'desc'  => '2階建ての場合だけ使います。空ならタブは出ません。',
        ),
        '_ch_plan_site_image_id' => array(
            'label' => '配置図',
            'desc'  => '配置図タブに表示します。',
        ),
    );

    $gallery_raw = get_post_meta($post->ID, '_ch_plan_gallery_image_ids', true);
    $gallery_ids = array_values(array_filter(array_map('absint', explode(',', (string) $gallery_raw))));

    $pdf_id = (int) get_post_meta($post->ID, '_ch_plan_pdf_id', true);
    $pdf_url = $pdf_id ? wp_get_attachment_url($pdf_id) : '';
    ?>
<?php
    $image_labels_raw = get_post_meta($post->ID, '_ch_plan_image_labels_json', true);
    $image_labels = json_decode((string) $image_labels_raw, true);
    if (!is_array($image_labels)) {
        $image_labels = array();
    }
    ?>
    <input
        type="hidden"
        name="_ch_plan_image_labels_json"
        value="<?php echo esc_attr(wp_json_encode($image_labels, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)); ?>"
        data-iez-plan-image-labels-json
    >

<div class="iez-plan-admin">
        <div class="iez-plan-admin__section">
            <h3>基本情報・概要</h3>

            <p class="iez-plan-admin__field">
                <label for="_ch_plan_floor_type">住宅タイプ</label>
                <select id="_ch_plan_floor_type" name="_ch_plan_floor_type">
                    <option value="one_story" <?php selected($floor_type, 'one_story'); ?>>平屋</option>
                    <option value="two_story" <?php selected($floor_type, 'two_story'); ?>>2階建て</option>
                </select>
            </p>

            <?php foreach ($text_fields as $key => $field) : ?>
                <?php
                $value = get_post_meta($post->ID, $key, true);
                if ($value === '') {
                    $value = $field['default'];
                }
                ?>

                <p class="iez-plan-admin__field">
                    <label for="<?php echo esc_attr($key); ?>">
                        <?php echo esc_html($field['label']); ?>
                    </label>

                    <?php if ($field['type'] === 'textarea') : ?>
                        <textarea
                            id="<?php echo esc_attr($key); ?>"
                            name="<?php echo esc_attr($key); ?>"
                            rows="4"
                        ><?php echo esc_textarea($value); ?></textarea>
                    <?php else : ?>
                        <input
                            id="<?php echo esc_attr($key); ?>"
                            type="text"
                            name="<?php echo esc_attr($key); ?>"
                            value="<?php echo esc_attr($value); ?>"
                        >
                    <?php endif; ?>
                </p>
            <?php endforeach; ?>
        </div>

        <div class="iez-plan-admin__section">
            <h3>画像設定</h3>

            <div class="iez-plan-admin__grid">
                <?php foreach ($image_fields as $key => $field) : ?>
                    <?php
                    $image_id = (int) get_post_meta($post->ID, $key, true);
                    $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'medium') : '';
                    ?>

                    <div class="iez-plan-media-box" data-iez-plan-image-box>
                        <p class="iez-plan-media-box__title">
                            <?php echo esc_html($field['label']); ?>
                        </p>

                        <span class="iez-plan-media-box__desc">
                            <?php echo esc_html($field['desc']); ?>
                        </span>

                        <div class="iez-plan-media-box__preview" data-iez-plan-preview>
                            <?php if ($image_url) : ?>
                                <img src="<?php echo esc_url($image_url); ?>" alt="">
                            <?php else : ?>
                                <span class="iez-plan-admin__empty">画像未設定</span>
                            <?php endif; ?>
                        </div>

                        <input
                            type="hidden"
                            name="<?php echo esc_attr($key); ?>"
                            value="<?php echo esc_attr($image_id); ?>"
                            data-iez-plan-image-id
                        >

                        <div class="iez-plan-admin__actions">
                            <button type="button" class="button button-primary" data-iez-plan-select>
                                画像を選択
                            </button>

                            <button type="button" class="button" data-iez-plan-remove>
                                削除
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="iez-plan-admin__section">
            <h3>4段目：複数内装写真</h3>
            <p class="description">LDK、キッチン、玄関、寝室、庭まわりなどを複数選択します。</p>

            <div data-iez-plan-gallery-box>
                <input
                    type="hidden"
                    name="_ch_plan_gallery_image_ids"
                    value="<?php echo esc_attr(implode(',', $gallery_ids)); ?>"
                    data-iez-plan-gallery-input
                >

                <div class="iez-plan-gallery-admin__preview" data-iez-plan-gallery-preview>
                    <?php if (!empty($gallery_ids)) : ?>
                        <?php foreach ($gallery_ids as $gallery_id) : ?>
                            <?php $url = wp_get_attachment_image_url($gallery_id, 'thumbnail'); ?>
                            <?php if ($url) : ?>
                                <span class="iez-plan-gallery-admin__item">
                                    <img src="<?php echo esc_url($url); ?>" alt="" data-id="<?php echo esc_attr($gallery_id); ?>">
                                    <label class="iez-plan-admin__image-label" data-iez-plan-image-label="<?php echo esc_attr($gallery_id); ?>">
                                        <span>画像名</span>
                                        <input
                                            type="text"
                                            value="<?php echo esc_attr($image_labels[(string) $gallery_id] ?? get_the_title($gallery_id)); ?>"
                                            data-iez-plan-image-label-input
                                            data-id="<?php echo esc_attr($gallery_id); ?>"
                                            placeholder="例：LDK 16.9帖 / 2LDK 平面図"
                                        >
                                    </label>
                                </span>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <span class="iez-plan-admin__empty">内装写真は未設定です</span>
                    <?php endif; ?>
                </div>

                <p>
                    <button type="button" class="button button-primary" data-iez-plan-gallery-select>
                        内装写真を選択
                    </button>

                    <button type="button" class="button" data-iez-plan-gallery-clear>
                        すべて削除
                    </button>
                </p>
            </div>
        </div>

        <div class="iez-plan-admin__section">
            <h3>5段目：住宅の特徴・マーケティング訴求</h3>
            <p class="description">
                耐震性、断熱、寒冷地対応、樹脂サッシ、バリアフリー、駐車スペースなど、住宅の見どころを編集できます。1行につき「見出し｜説明文｜アイコンキー」で入力してください。
            </p>

            <?php
            $feature_default_lines = array(
                '耐震性｜構造・基礎・柱の考え方を整理し、安心して暮らせる住まいを目指します。｜shield',
                '基礎｜建物を支える基礎部分を重視し、長く住むための土台を整えます。｜foundation',
                '屋根｜那須の気候や積雪・雨風を考え、住まいを守る屋根計画を検討します。｜roof',
                '壁・外装｜外観デザインだけでなく、耐久性やメンテナンス性にも配慮します。｜wall',
                '樹脂サッシ｜窓まわりの断熱性を高め、冬の冷え込みや結露対策にもつなげます。｜window',
                '寒冷地対応｜那須エリアの寒さを考え、断熱・窓・換気を含めた快適性を検討します。｜snow',
                'ユニットバス｜掃除しやすさ、断熱性、将来の使いやすさを考えた水まわりを選べます。｜bath',
                'トイレ｜日常の使いやすさと掃除のしやすさを考え、配置や設備を整理できます。｜toilet',
                'バリアフリー｜将来の暮らしやすさを考え、段差や動線を整理できます。｜accessibility',
                'ウッドデッキ｜庭とのつながりをつくり、外時間を楽しめる住まいにできます。｜deck',
                '駐車スペース｜敷地条件に合わせて、車の出入りや来客時の使いやすさを考えます。｜car',
                '収納計画｜各所に収納を設け、生活感を抑えたすっきりした暮らしを目指します。｜storage',
            );

            $feature_lines = get_post_meta($post->ID, '_ch_plan_marketing_features', true);

            if ($feature_lines === '') {
                $feature_lines = implode("\n", $feature_default_lines);
            }
            ?>

            <p class="iez-plan-admin__field">
                <label for="_ch_plan_marketing_features">住宅の特徴</label>
                <textarea
                    id="_ch_plan_marketing_features"
                    name="_ch_plan_marketing_features"
                    rows="14"
                    placeholder="耐震性｜構造・基礎・柱の考え方を整理し、安心して暮らせる住まいを目指します。｜shield｜shield"
                ><?php echo esc_textarea($feature_lines); ?></textarea>

                <p class="description">
                    アイコンキー:
                    shield / foundation / roof / wall / window / snow / bath / toilet / accessibility / deck / car / storage / home
                </p>
            </p>
        </div>

        <div class="iez-plan-admin__section">
            <h3>PDF確認・ダウンロード</h3>
            <?php
            $plan_slug = get_post_field('post_name', $post->ID);
            $upload_dir = wp_upload_dir();

            $generated_pdf_path = '';
            $generated_pdf_url  = '';

            if ($plan_slug !== '' && !empty($upload_dir['basedir'])) {
                $generated_pdf_path = trailingslashit($upload_dir['basedir']) . 'iezukuri-pdf/' . $plan_slug . '.pdf';
                $generated_pdf_url  = home_url('/wp-content/uploads/iezukuri-pdf/' . $plan_slug . '.pdf');
            }

            $has_generated_pdf = (
                $generated_pdf_path &&
                file_exists($generated_pdf_path) &&
                filesize($generated_pdf_path) > 0
            );

            $pdf_preview_url = add_query_arg('plan_pdf', '1', get_permalink($post->ID));
            ?>

            <p class="description">
                PDF専用表示は管理者確認用です。ユーザーには通常ページの「PDFダウンロード」だけを表示します。
            </p>

            <p>
                <a class="button" href="<?php echo esc_url($pdf_preview_url); ?>" target="_blank" rel="noopener">
                    PDF専用表示を確認
                </a>

                <?php if ($has_generated_pdf) : ?>
                    <a class="button button-primary" href="<?php echo esc_url($generated_pdf_url); ?>" target="_blank" rel="noopener">
                        生成済みPDFを開く
                    </a>
                <?php else : ?>
                    <span class="description">生成済みPDFはまだありません。</span>
                <?php endif; ?>
            </p>

            <p class="description">
                保存先: <code><?php echo esc_html($generated_pdf_path); ?></code>
            </p>

            <p class="description">
                PDFを再生成する場合は、ターミナルで <code>tools/generate_iez_plan_pdf.sh <?php echo esc_html($plan_slug); ?></code> を実行します。
            </p>
        </div>

        <div class="iez-plan-admin__section">
            <h3>PDF資料</h3>

            <div data-iez-plan-pdf-box>
                <input
                    type="hidden"
                    name="_ch_plan_pdf_id"
                    value="<?php echo esc_attr($pdf_id); ?>"
                    data-iez-plan-pdf-id
                >

                <p data-iez-plan-pdf-preview>
                    <?php if ($pdf_url) : ?>
                        <a href="<?php echo esc_url($pdf_url); ?>" target="_blank" rel="noopener">
                            現在のPDFを開く
                        </a>
                    <?php else : ?>
                        <span class="iez-plan-admin__empty">PDF未設定</span>
                    <?php endif; ?>
                </p>

                <p>
                    <button type="button" class="button button-primary" data-iez-plan-pdf-select>
                        PDFを選択
                    </button>

                    <button type="button" class="button" data-iez-plan-pdf-remove>
                        PDFを削除
                    </button>
                </p>
            </div>
        </div>
    </div>
    <?php
}

add_action('save_post_iez_plan', function ($post_id) {
    if (!isset($_POST['naigai_iez_plan_detail_nonce'])) {
        return;
    }

    if (!wp_verify_nonce($_POST['naigai_iez_plan_detail_nonce'], 'naigai_iez_plan_save_detail_meta')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    $text_keys = array(
        '_ch_plan_label',
        '_ch_plan_name',
        '_ch_plan_style',
        '_ch_plan_layout',
        '_ch_plan_total_area',
        '_ch_plan_tsubo',
        '_ch_plan_building_area',
        '_ch_plan_family',
        '_ch_plan_description',
    );

    foreach ($text_keys as $key) {
        if (isset($_POST[$key])) {
            update_post_meta($post_id, $key, sanitize_textarea_field(wp_unslash($_POST[$key])));
        }
    }

    $floor_type = isset($_POST['_ch_plan_floor_type'])
        ? sanitize_key(wp_unslash($_POST['_ch_plan_floor_type']))
        : 'one_story';

    if (!in_array($floor_type, array('one_story', 'two_story'), true)) {
        $floor_type = 'one_story';
    }

    update_post_meta($post_id, '_ch_plan_floor_type', $floor_type);

    $image_keys = array(
        '_ch_plan_exterior_image_id',
        '_ch_plan_1f_image_id',
        '_ch_plan_2f_image_id',
        '_ch_plan_site_image_id',
    );

    foreach ($image_keys as $key) {
        $value = isset($_POST[$key]) ? absint($_POST[$key]) : 0;

        if ($value > 0) {
            update_post_meta($post_id, $key, $value);
        } else {
            delete_post_meta($post_id, $key);
        }
    }

    $gallery_raw = isset($_POST['_ch_plan_gallery_image_ids'])
        ? sanitize_text_field(wp_unslash($_POST['_ch_plan_gallery_image_ids']))
        : '';

    $gallery_ids = array_values(array_filter(array_map('absint', explode(',', $gallery_raw))));

    if (!empty($gallery_ids)) {
        update_post_meta($post_id, '_ch_plan_gallery_image_ids', implode(',', $gallery_ids));
    } else {
        delete_post_meta($post_id, '_ch_plan_gallery_image_ids');
    }

    if (isset($_POST['_ch_plan_marketing_features'])) {
        update_post_meta(
            $post_id,
            '_ch_plan_marketing_features',
            sanitize_textarea_field(wp_unslash($_POST['_ch_plan_marketing_features']))
        );
    }

    $pdf_id = isset($_POST['_ch_plan_pdf_id']) ? absint($_POST['_ch_plan_pdf_id']) : 0;

    if ($pdf_id > 0) {
        update_post_meta($post_id, '_ch_plan_pdf_id', $pdf_id);
    } else {
        delete_post_meta($post_id, '_ch_plan_pdf_id');
    }
});

/**
 * =========================================================
 * naigai_iezukuri_enqueue_plan_metabox_admin_assets
 *
 * 対象:
 * - WordPress管理画面
 * - 投稿タイプ: iez_plan の新規作成 / 編集画面
 *
 * 役割:
 * - 間取りプラン管理画面用CSS/JSを読み込む
 *
 * 読み込むファイル:
 * - hub/pages/iezukuri/admin/assets/css/plans-metabox.css
 * - hub/pages/iezukuri/admin/assets/js/plans-metabox.js
 *
 * 注意:
 * - 公開ページには読み込まない
 * - plans-metabox.php 内に <style> / inline script を置かない
 * =========================================================
 */

/**
 * =========================================================
 * iez_plan 画像表示名 JSON 保存
 *
 * メタキー:
 * - _ch_plan_image_labels_json
 * =========================================================
 */
add_action('admin_footer-post.php', 'naigai_iez_plan_image_labels_admin_boot');
add_action('admin_footer-post-new.php', 'naigai_iez_plan_image_labels_admin_boot');

function naigai_iez_plan_image_labels_admin_boot() {
    global $post;

    if (!$post || $post->post_type !== 'iez_plan') {
        return;
    }

    $raw = get_post_meta($post->ID, '_ch_plan_image_labels_json', true);
    $labels = json_decode((string) $raw, true);

    if (!is_array($labels)) {
        $labels = array();
    }
    ?>
    <script>
      window.NaigaiIezPlanImageLabels = <?php echo wp_json_encode($labels, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
    </script>
    <?php
}

add_action('save_post_iez_plan', 'naigai_iez_plan_save_image_labels_json', 99);

function naigai_iez_plan_save_image_labels_json($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (!isset($_POST['_ch_plan_image_labels_json'])) {
        return;
    }

    $raw = wp_unslash($_POST['_ch_plan_image_labels_json']);
    $decoded = json_decode((string) $raw, true);

    if (!is_array($decoded)) {
        delete_post_meta($post_id, '_ch_plan_image_labels_json');
        return;
    }

    $clean = array();

    foreach ($decoded as $id => $label) {
        $id = absint($id);
        $label = sanitize_text_field($label);

        if ($id && $label !== '') {
            $clean[(string) $id] = $label;
        }
    }

    if (!empty($clean)) {
        update_post_meta(
            $post_id,
            '_ch_plan_image_labels_json',
            wp_json_encode($clean, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );
    } else {
        delete_post_meta($post_id, '_ch_plan_image_labels_json');
    }
}
