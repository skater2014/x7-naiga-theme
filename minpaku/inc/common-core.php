<?php
/**
 * =========================================================
 * MINPAKU_COMMON_CORE_ROLE_MAP
 * minpaku/inc/common-core.php
 * =========================================================
 *
 * 現在の役割:
 * - 民泊CPT minpaku の登録
 * - 民泊共通helper
 * - 料金・ギャラリー・Stripe key helper
 * - mnpkBooking の wp_localize_script
 * - 民泊投稿編集画面のメタボックス
 * - 空室・予約不可カレンダー管理
 * - 予約料金計算
 * - Stripe PaymentIntent AJAX
 *
 * 注意:
 * - このファイルは現状かなり大きい。
 * - いきなり全部を分割すると予約・決済・管理画面に影響が出やすい。
 * - まず functions.php からの移動を終え、その後に役割単位で小分けする。
 *
 * 今後の分割候補:
 * - minpaku/inc/post-type.php
 * - minpaku/inc/helpers.php
 * - minpaku/inc/frontend/localize.php
 * - minpaku/inc/admin/meta-boxes.php
 * - minpaku/inc/admin/availability.php
 * - minpaku/inc/booking-calculator.php
 * - minpaku/inc/ajax/payment-intent.php
 * =========================================================
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * =========================================================
 * 民泊カスタム投稿 + メタボックス + 予約 / Stripe 決済
 * =========================================================
 *
 * このファイルの目的:
 * 1. 民泊投稿タイプ「minpaku」を登録する
 * 2. 管理画面で民泊施設の基本情報を保存する
 * 3. 管理画面で営業開始日・空き状況を保存する
 * 4. フロント表示用の情報を取り出す
 * 5. Stripe Checkout Session を作る
 *
 * 読み込み場所:
 * functions.php で次のように読み込む
 *
 * require_once get_template_directory() . '/inc/functions/functions-minpaku.php';
 *
 * 重要:
 * - このファイルは「民泊専用のロジック」をまとめたもの
 * - Stripe の秘密鍵はここに直書きしない
 * - wp-config.php 側で
 *   define('NAIGAI_STRIPE_SECRET_KEY', '...');
 *   のように定義しておく
 * =========================================================
 */


/* =========================================================
 * 1. 民泊投稿タイプを登録
 * =========================================================
 *
 * URL方針:
 * - 一覧   : /minpaku-stay/
 * - 詳細   : /minpaku-stay/room/施設スラッグ/
 *
 * ポイント:
 * - has_archive は一覧URLを決める
 * - rewrite['slug'] は詳細URLの親ディレクトリを決める
 * - 施設ごとの最後の部分は、投稿ごとのスラッグが自動で入る
 */
if (!function_exists('mnpk_register_post_type')) {
    function mnpk_register_post_type()
    {
        $labels = array(
            'name'               => '那須の民泊・一棟貸し・貸別荘の宿泊施設一覧',
            'singular_name'      => '民泊施設',
            'add_new'            => '新規追加',
            'add_new_item'       => '民泊施設を追加',
            'edit_item'          => '民泊施設を編集',
            'new_item'           => '新しい民泊施設',
            'view_item'          => '民泊施設を表示',
            'search_items'       => '民泊施設を検索',
            'not_found'          => '民泊施設が見つかりませんでした。',
            'not_found_in_trash' => 'ゴミ箱に民泊施設はありませんでした。',
            'all_items'          => '民泊施設一覧',
            'archives'           => '民泊施設一覧',
            'menu_name'          => '民泊施設',
        );

        register_post_type('minpaku', array(
            'labels'              => $labels,
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'show_in_rest'        => true,

            // 一覧ページURL
            'has_archive'         => 'minpaku-stay',

            // 詳細ページURL
            // /minpaku-stay/room/施設スラッグ/
            'rewrite'             => array(
                'slug'       => 'minpaku-stay/room',
                'with_front' => false,
                'feeds'      => true,
                'pages'      => true,
            ),

            'query_var'           => 'minpaku',
            'menu_position'       => 6,
            'menu_icon'           => 'dashicons-admin-home',
            'hierarchical'        => false,
            'supports'            => array(
                'title',
                'editor',
                'excerpt',
                'thumbnail',
                'custom-fields',
                'revisions',
                'page-attributes',
            ),
            'taxonomies'          => array('category', 'post_tag'),
            'publicly_queryable'  => true,
            'exclude_from_search' => false,
            'map_meta_cap'        => true,
        ));
    }
}
add_action('init', 'mnpk_register_post_type', 20);


/* =========================================================
 * 2. rewrite を一度だけ更新
 * =========================================================
 *
 * 投稿タイプのURL設定を変えた時、
 * WordPress に新しいURLルールを覚えさせる必要がある。
 *
 * 毎回 flush すると重いので、一度だけ実行する。
 */
if (!function_exists('mnpk_maybe_flush_rewrite_rules')) {
    function mnpk_maybe_flush_rewrite_rules()
    {
        $version = '20260414_02';

        if (get_option('mnpk_rewrite_version') === $version) {
            return;
        }

        mnpk_register_post_type();
        flush_rewrite_rules(false);
        update_option('mnpk_rewrite_version', $version);
    }
}
add_action('init', 'mnpk_maybe_flush_rewrite_rules', 99);


/* =========================================================
 * 3. 予約ページURL
 * =========================================================
 *
 * 予約ページがあればそのURLを返す。
 * 無ければ /minpaku-booking/ を仮の戻り先として使う。
 */
if (!function_exists('mnpk_get_booking_page_url')) {
    function mnpk_get_booking_page_url($post_id = 0)
    {
        $page = get_page_by_path('minpaku-booking', OBJECT, 'page');
        $url  = $page ? get_permalink($page->ID) : home_url('/minpaku-booking/');

        if ($post_id) {
            $url = add_query_arg(array('post_id' => absint($post_id)), $url);
        }

        return $url;
    }
}


/* =========================================================
 * 4. よく使う小さな helper
 * ========================================================= */

/**
 * post meta を安全に読む helper
 */
if (!function_exists('mnpk_meta')) {
    function mnpk_meta($post_id, $key, $default = '')
    {
        $value = get_post_meta($post_id, $key, true);
        return ($value !== '' && $value !== null) ? $value : $default;
    }
}

/**
 * true / false を「あり / なし」に変える helper
 */
if (!function_exists('mnpk_bool_label')) {
    function mnpk_bool_label($value, $true_label = 'あり', $false_label = 'なし')
    {
        return !empty($value) ? $true_label : $false_label;
    }
}

/**
 * 金額表示 helper
 */
if (!function_exists('mnpk_money')) {
    function mnpk_money($value)
    {
        $value = (int) $value;

        if ($value <= 0) {
            return '要相談';
        }

        return number_format($value) . '円';
    }
}

/**
 * ギャラリー画像ID一覧を配列で返す
 */
if (!function_exists('mnpk_gallery_ids')) {
    function mnpk_gallery_ids($post_id)
    {
        $raw = (string) get_post_meta($post_id, '_mnpk_gallery_ids', true);

        if ($raw === '') {
            return array();
        }

        $ids = array_map('absint', array_filter(array_map('trim', explode(',', $raw))));
        return array_values(array_filter($ids));
    }
}

/**
 * 投稿サムネイル + ギャラリー画像の URL 一覧を返す
 */
if (!function_exists('mnpk_get_gallery_image_urls')) {
    function mnpk_get_gallery_image_urls($post_id)
    {
        $image_urls = array();

        if (has_post_thumbnail($post_id)) {
            $thumbnail_url = get_the_post_thumbnail_url($post_id, 'full');
            if ($thumbnail_url) {
                $image_urls[] = $thumbnail_url;
            }
        }

        foreach (mnpk_gallery_ids($post_id) as $attachment_id) {
            $url = wp_get_attachment_url($attachment_id);
            if ($url && !in_array($url, $image_urls, true)) {
                $image_urls[] = $url;
            }
        }

        return $image_urls;
    }
}

/**
 * Stripe の秘密鍵を読む helper
 *
 * 目的:
 * - 新しい定数名 NAIGAI_STRIPE_SECRET_KEY を優先
 * - もし昔の MINPAKU_STRIPE_SECRET_KEY が残っていても救済
 */
/**
 * =========================================================
 * 民泊オンライン決済：環境全体のON / OFF
 * =========================================================
 *
 * 【この関数の役割】
 *
 * 各施設には既に
 *
 * - _mnpk_booking_enabled
 * - _mnpk_online_payment_enabled
 *
 * という個別設定がある。
 *
 * この関数はそれより一段上の、
 *
 * 「このWordPress環境そのものでStripe決済を許可するか」
 *
 * を判定する。
 *
 * ---------------------------------------------------------
 * なぜ必要か
 * ---------------------------------------------------------
 *
 * ローカル:
 *   Stripe TESTキーで決済画面を開発・検証したい。
 *
 * 本番:
 *   Stripeを正式採用するか未確定の間は、
 *   誤って本番決済を開始させたくない。
 *
 * そのためテーマコードを削除せず、
 * 決済機能だけ安全に停止できるようにする。
 *
 * ---------------------------------------------------------
 * 優先順位
 * ---------------------------------------------------------
 *
 * 1. NAIGAI_MINPAKU_PAYMENT_ENABLED が定義されていれば
 *    その値を最優先する。
 *
 * 2. 定義されていない場合、
 *    localhost / 127.0.0.1 は開発環境としてON。
 *
 * 3. それ以外は安全側に倒してOFF。
 *
 * 本番でStripeを正式利用すると決めた場合だけ、
 * wp-config.php 等で以下を設定する。
 *
 * define('NAIGAI_MINPAKU_PAYMENT_ENABLED', true);
 *
 * =========================================================
 */
if (!function_exists('mnpk_is_payment_feature_enabled')) {
    function mnpk_is_payment_feature_enabled()
    {
        if (defined('NAIGAI_MINPAKU_PAYMENT_ENABLED')) {

            return filter_var(
                NAIGAI_MINPAKU_PAYMENT_ENABLED,
                FILTER_VALIDATE_BOOLEAN
            );
        }

        $host = isset($_SERVER['HTTP_HOST'])
            ? strtolower((string) $_SERVER['HTTP_HOST'])
            : '';

        $host = preg_replace('/:\d+$/', '', $host);

        if (
            $host === '127.0.0.1' ||
            $host === 'localhost'
        ) {
            return true;
        }

        /*
         * 本番では明示的にONにするまで決済させない。
         *
         * Stripeキーが存在していても、
         * このスイッチがfalseならPaymentIntentを作らない。
         */
        return false;
    }
}

if (!function_exists('mnpk_get_stripe_secret_key')) {
    function mnpk_get_stripe_secret_key()
    {
        if (defined('NAIGAI_STRIPE_SECRET_KEY') && NAIGAI_STRIPE_SECRET_KEY) {
            return NAIGAI_STRIPE_SECRET_KEY;
        }

        if (defined('MINPAKU_STRIPE_SECRET_KEY') && MINPAKU_STRIPE_SECRET_KEY) {
            return MINPAKU_STRIPE_SECRET_KEY;
        }

        return '';
    }
}


/* =========================================================
 * 5. フロント JS に ajaxUrl / nonce を渡す
 * =========================================================
 *
 * JavaScript から admin-ajax.php に送るために必要。
 */
/**
 * Moved:
 * フロントJSへ渡す mnpkBooking localize は
 * minpaku/inc/frontend/localize.php に分離済み。
 */



/* =========================================================
 * 6. 基本メタボックス
 * ========================================================= */

if (!function_exists('mnpk_add_meta_boxes')) {
    function mnpk_add_meta_boxes()
    {
        add_meta_box(
            'mnpk_meta_box',
            '民泊施設情報',
            'mnpk_render_meta_box',
            'minpaku',
            'normal',
            'high'
        );
    }
}
add_action('add_meta_boxes', 'mnpk_add_meta_boxes');

/**
 * 保存するメタキー一覧
 */
if (!function_exists('mnpk_meta_fields')) {
    function mnpk_meta_fields()
    {
        return array(
            '_mnpk_lead',
            '_mnpk_nightly_price',
            '_mnpk_weekend_price',
            '_mnpk_cleaning_fee',
            '_mnpk_capacity',
            '_mnpk_base_guests',
            '_mnpk_extra_guest_fee',
            '_mnpk_bedrooms',
            '_mnpk_beds',
            '_mnpk_checkin_time',
            '_mnpk_checkout_time',
            '_mnpk_min_nights',
            '_mnpk_wifi',
            '_mnpk_parking',
            '_mnpk_kitchen',
            '_mnpk_bath',
            '_mnpk_toilet',
            '_mnpk_aircon',
            '_mnpk_washer',
            '_mnpk_amenities',
            '_mnpk_smoking',
            '_mnpk_pet',
            '_mnpk_children',
            '_mnpk_cancel_policy',
            '_mnpk_booking_enabled',
            '_mnpk_online_payment_enabled',
            '_mnpk_google_pay',
            '_mnpk_visa',
            '_mnpk_mastercard',
            '_mnpk_payment_note',
            '_mnpk_booking_note',
            '_mnpk_gallery_ids',
        );
    }
}

/**
 * 管理画面の input 1個を出す helper
 */
if (!function_exists('mnpk_render_text_input')) {
    function mnpk_render_text_input($post_id, $key, $label, $type = 'text', $placeholder = '')
    {
        $value = mnpk_meta($post_id, $key, '');

        echo '<p style="margin-bottom:14px;">';
        echo '<label><strong>' . esc_html($label) . '</strong><br>';
        echo '<input type="' . esc_attr($type) . '" name="' . esc_attr($key) . '" value="' . esc_attr($value) . '" placeholder="' . esc_attr($placeholder) . '" style="width:100%;max-width:100%;"></label>';
        echo '</p>';
    }
}

/**
 * 管理画面の textarea 1個を出す helper
 */
if (!function_exists('mnpk_render_textarea')) {
    function mnpk_render_textarea($post_id, $key, $label, $rows = 4, $desc = '')
    {
        $value = mnpk_meta($post_id, $key, '');

        echo '<p style="margin-bottom:14px;">';
        echo '<label><strong>' . esc_html($label) . '</strong><br>';
        echo '<textarea name="' . esc_attr($key) . '" rows="' . absint($rows) . '" style="width:100%;max-width:100%;">' . esc_textarea($value) . '</textarea></label>';

        if ($desc) {
            echo '<br><span class="description">' . esc_html($desc) . '</span>';
        }

        echo '</p>';
    }
}

/**
 * 管理画面の checkbox 1個を出す helper
 */
if (!function_exists('mnpk_render_checkbox')) {
    function mnpk_render_checkbox($post_id, $key, $label)
    {
        $checked = !empty(get_post_meta($post_id, $key, true));

        echo '<p style="margin-bottom:10px;">';
        echo '<label><input type="checkbox" name="' . esc_attr($key) . '" value="1" ' . checked($checked, true, false) . '> ' . esc_html($label) . '</label>';
        echo '</p>';
    }
}

/**
 * 管理画面のギャラリー画像選択JS
 */
if (!function_exists('mnpk_admin_enqueue')) {
    function mnpk_admin_enqueue($hook)
    {
        global $post;

        if (($hook !== 'post.php' && $hook !== 'post-new.php') || !$post || $post->post_type !== 'minpaku') {
            return;
        }

        wp_enqueue_media();
        wp_enqueue_script('jquery');

        $js = <<<'JS'
jQuery(function($){
    let galleryFrame = null;

    $('#mnpk-pick-gallery').on('click', function(e){
        e.preventDefault();

        if (galleryFrame) {
            galleryFrame.open();
            return;
        }

        galleryFrame = wp.media({
            title: 'ギャラリー画像を選択',
            button: { text: 'この画像を使う' },
            multiple: true,
            library: { type: 'image' }
        });

        galleryFrame.on('select', function(){
            const selection = galleryFrame.state().get('selection').toJSON();
            const ids = selection.map(item => item.id);
            const html = selection.map(item => '<div style="width:120px;"><img src="' + item.url + '" style="width:100%;height:auto;display:block;"></div>').join('');

            $('#_mnpk_gallery_ids').val(ids.join(','));
            $('#mnpk-gallery-preview').html(html);
        });

        galleryFrame.open();
    });

    $('#mnpk-clear-gallery').on('click', function(e){
        e.preventDefault();
        $('#_mnpk_gallery_ids').val('');
        $('#mnpk-gallery-preview').html('');
    });
});
JS;

        wp_add_inline_script('jquery', $js);
    }
}
add_action('admin_enqueue_scripts', 'mnpk_admin_enqueue');

/**
 * 基本メタボックス本体
 */
if (!function_exists('mnpk_render_meta_box')) {
    function mnpk_render_meta_box($post)
    {
        wp_nonce_field('mnpk_save_meta', 'mnpk_meta_nonce');

        echo '<h3>基本情報</h3>';
        mnpk_render_textarea($post->ID, '_mnpk_lead', 'リード文', 3);
        mnpk_render_text_input($post->ID, '_mnpk_nightly_price', '平日料金（円）', 'number', '例: 28000');
        mnpk_render_text_input($post->ID, '_mnpk_weekend_price', '週末料金（円）', 'number', '例: 35000');
        mnpk_render_text_input($post->ID, '_mnpk_cleaning_fee', '清掃料金（円）', 'number', '例: 8000');
        mnpk_render_text_input($post->ID, '_mnpk_capacity', '定員', 'number', '例: 6');
        mnpk_render_text_input($post->ID, '_mnpk_base_guests', '基本料金に含む人数', 'number', '例: 2');
        mnpk_render_text_input($post->ID, '_mnpk_extra_guest_fee', '追加人数料金（1人1泊あたり円）', 'number', '例: 3000');
        mnpk_render_text_input($post->ID, '_mnpk_bedrooms', '部屋数', 'number', '例: 2');
        mnpk_render_text_input($post->ID, '_mnpk_beds', 'ベッド数', 'number', '例: 4');
        mnpk_render_text_input($post->ID, '_mnpk_checkin_time', 'チェックイン', 'text', '例: 15:00');
        mnpk_render_text_input($post->ID, '_mnpk_checkout_time', 'チェックアウト', 'text', '例: 10:00');
        mnpk_render_text_input($post->ID, '_mnpk_min_nights', '最低宿泊日数', 'number', '例: 2');

        echo '<hr><h3>設備・条件</h3>';
        mnpk_render_checkbox($post->ID, '_mnpk_wifi', 'Wi-Fi あり');
        mnpk_render_checkbox($post->ID, '_mnpk_parking', '駐車場あり');
        mnpk_render_checkbox($post->ID, '_mnpk_kitchen', 'キッチンあり');
        mnpk_render_checkbox($post->ID, '_mnpk_bath', 'バスあり');
        mnpk_render_checkbox($post->ID, '_mnpk_toilet', 'トイレあり');
        mnpk_render_checkbox($post->ID, '_mnpk_aircon', 'エアコンあり');
        mnpk_render_checkbox($post->ID, '_mnpk_washer', '洗濯機あり');
        mnpk_render_checkbox($post->ID, '_mnpk_smoking', '喫煙可');
        mnpk_render_checkbox($post->ID, '_mnpk_pet', 'ペット可');
        mnpk_render_checkbox($post->ID, '_mnpk_children', '子ども可');
        mnpk_render_textarea($post->ID, '_mnpk_amenities', 'アメニティ', 4, '例: タオル、シャンプー、ドライヤー');
        mnpk_render_textarea($post->ID, '_mnpk_cancel_policy', 'キャンセルポリシー', 3);

        echo '<hr><h3>予約・決済</h3>';
        mnpk_render_checkbox($post->ID, '_mnpk_booking_enabled', '予約受付を有効にする');
        mnpk_render_checkbox($post->ID, '_mnpk_online_payment_enabled', 'オンライン決済を有効にする');
        mnpk_render_checkbox($post->ID, '_mnpk_google_pay', 'Google Pay 対応');
        mnpk_render_checkbox($post->ID, '_mnpk_visa', 'Visa 対応');
        mnpk_render_checkbox($post->ID, '_mnpk_mastercard', 'Mastercard 対応');
        mnpk_render_textarea($post->ID, '_mnpk_payment_note', '決済メモ', 3, '例: Stripe Checkout を利用');
        mnpk_render_textarea($post->ID, '_mnpk_booking_note', '予約メモ', 3, '例: 宿泊前に本人確認が必要です');

        echo '<hr><h3>ギャラリー</h3>';
        $gallery_ids = mnpk_meta($post->ID, '_mnpk_gallery_ids', '');
        echo '<input type="hidden" id="_mnpk_gallery_ids" name="_mnpk_gallery_ids" value="' . esc_attr($gallery_ids) . '">';
        echo '<p><button type="button" class="button" id="mnpk-pick-gallery">ギャラリー画像を選択</button> ';
        echo '<button type="button" class="button" id="mnpk-clear-gallery">ギャラリー画像をクリア</button></p>';
        echo '<div id="mnpk-gallery-preview" style="display:flex;gap:10px;flex-wrap:wrap;">';

        foreach (mnpk_gallery_ids($post->ID) as $attachment_id) {
            $url = wp_get_attachment_image_url($attachment_id, 'medium');
            if ($url) {
                echo '<div style="width:120px;"><img src="' . esc_url($url) . '" style="width:100%;height:auto;display:block;"></div>';
            }
        }

        echo '</div>';
    }
}

/**
 * 基本メタ保存
 */
if (!function_exists('mnpk_save_meta')) {
    function mnpk_save_meta($post_id)
    {
        if (!isset($_POST['mnpk_meta_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['mnpk_meta_nonce'])), 'mnpk_save_meta')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (get_post_type($post_id) !== 'minpaku' || !current_user_can('edit_post', $post_id)) {
            return;
        }

        $checkbox_fields = array(
            '_mnpk_wifi',
            '_mnpk_parking',
            '_mnpk_kitchen',
            '_mnpk_bath',
            '_mnpk_toilet',
            '_mnpk_aircon',
            '_mnpk_washer',
            '_mnpk_smoking',
            '_mnpk_pet',
            '_mnpk_children',
            '_mnpk_booking_enabled',
            '_mnpk_online_payment_enabled',
            '_mnpk_google_pay',
            '_mnpk_visa',
            '_mnpk_mastercard',
        );

        $textarea_fields = array(
            '_mnpk_lead',
            '_mnpk_amenities',
            '_mnpk_cancel_policy',
            '_mnpk_payment_note',
            '_mnpk_booking_note',
        );

        $number_fields = array(
            '_mnpk_nightly_price',
            '_mnpk_weekend_price',
            '_mnpk_cleaning_fee',
            '_mnpk_capacity',
            '_mnpk_base_guests',
            '_mnpk_extra_guest_fee',
            '_mnpk_bedrooms',
            '_mnpk_beds',
            '_mnpk_min_nights',
        );

        foreach (mnpk_meta_fields() as $field) {
            if (in_array($field, $checkbox_fields, true)) {
                update_post_meta($post_id, $field, isset($_POST[$field]) ? '1' : '');
                continue;
            }

            if (in_array($field, $textarea_fields, true)) {
                $value = isset($_POST[$field]) ? sanitize_textarea_field(wp_unslash($_POST[$field])) : '';
                update_post_meta($post_id, $field, $value);
                continue;
            }

            if (in_array($field, $number_fields, true)) {
                $value = isset($_POST[$field]) ? absint(wp_unslash($_POST[$field])) : 0;
                update_post_meta($post_id, $field, $value);
                continue;
            }

            $value = isset($_POST[$field]) ? sanitize_text_field(wp_unslash($_POST[$field])) : '';
            update_post_meta($post_id, $field, $value);
        }
    }
}
add_action('save_post_minpaku', 'mnpk_save_meta');


/* =========================================================
 * 8. 空き状況 helper
 * ========================================================= */

if (!function_exists('mnpk_availability_status_options')) {
    function mnpk_availability_status_options()
    {
        return array(
            'available' => '空き',
            'reserved'  => '予約済み',
            'cleaning'  => '清掃',
            'blocked'   => '停止',
        );
    }
}

if (!function_exists('mnpk_get_calendar_events')) {
    function mnpk_get_calendar_events($post_id)
    {
        $raw = get_post_meta($post_id, '_mnpk_calendar_events', true);

        if (empty($raw)) {
            return array();
        }

        if (is_array($raw)) {
            return $raw;
        }

        $decoded = json_decode((string) $raw, true);
        if (!is_array($decoded)) {
            return array();
        }

        $events = array();
        $allowed_statuses = array_keys(mnpk_availability_status_options());

        foreach ($decoded as $row) {
            $start  = isset($row['start']) ? sanitize_text_field($row['start']) : '';
            $end    = isset($row['end']) ? sanitize_text_field($row['end']) : '';
            $status = isset($row['status']) ? sanitize_key($row['status']) : 'blocked';
            $note   = isset($row['note']) ? sanitize_text_field($row['note']) : '';

            if ($start === '' || $end === '') {
                continue;
            }

            if ($end < $start) {
                $tmp   = $start;
                $start = $end;
                $end   = $tmp;
            }

            if (!in_array($status, $allowed_statuses, true)) {
                $status = 'blocked';
            }

            $events[] = array(
                'start'  => $start,
                'end'    => $end,
                'status' => $status,
                'note'   => $note,
            );
        }

        return $events;
    }
}

if (!function_exists('mnpk_get_calendar_payload')) {
    function mnpk_get_calendar_payload($post_id)
    {
        return array(
            'open_start_date'      => (string) get_post_meta($post_id, '_mnpk_open_start_date', true),
            'cleaning_buffer_days' => max(0, (int) get_post_meta($post_id, '_mnpk_cleaning_buffer_days', true)),
            'cleaning_note'        => (string) get_post_meta($post_id, '_mnpk_cleaning_note', true),
            'events'               => mnpk_get_calendar_events($post_id),
        );
    }
}


/* =========================================================
 * 9. 空き状況メタボックス
 * ========================================================= */

if (!function_exists('mnpk_add_availability_meta_box')) {
    function mnpk_add_availability_meta_box()
    {
        add_meta_box(
            'mnpk_availability_meta_box',
            '営業開始日・空き状況',
            'mnpk_render_availability_meta_box',
            'minpaku',
            'normal',
            'default'
        );
    }
}
add_action('add_meta_boxes', 'mnpk_add_availability_meta_box');

if (!function_exists('mnpk_render_availability_meta_box')) {
    function mnpk_render_availability_meta_box($post)
    {
        wp_nonce_field('mnpk_save_availability_meta', 'mnpk_availability_meta_nonce');

        $open_start_date      = (string) get_post_meta($post->ID, '_mnpk_open_start_date', true);
        $cleaning_buffer_days = max(0, (int) get_post_meta($post->ID, '_mnpk_cleaning_buffer_days', true));
        $cleaning_note        = (string) get_post_meta($post->ID, '_mnpk_cleaning_note', true);
        $events               = mnpk_get_calendar_events($post->ID);
        $status_options       = mnpk_availability_status_options();

        if (empty($events)) {
            $events[] = array(
                'start'  => '',
                'end'    => '',
                'status' => 'available',
                'note'   => '',
            );
        }

        echo '<p><strong>営業開始日</strong><br>';
        echo '<input type="date" name="_mnpk_open_start_date" value="' . esc_attr($open_start_date) . '" style="width:220px;">';
        echo '<br><span class="description">この日より前は front カレンダーで選択不可にします。</span></p>';

        echo '<p><strong>清掃バッファ日数</strong><br>';
        echo '<input type="number" min="0" step="1" name="_mnpk_cleaning_buffer_days" value="' . esc_attr($cleaning_buffer_days) . '" style="width:120px;">';
        echo '<br><span class="description">予約済み期間の終了後に、自動で清掃扱いにする日数です。</span></p>';

        echo '<p><strong>清掃メモ</strong><br>';
        echo '<textarea name="_mnpk_cleaning_note" rows="3" style="width:100%;max-width:100%;">' . esc_textarea($cleaning_note) . '</textarea>';
        echo '<br><span class="description">front のカレンダーで「清掃中」に出す説明文です。</span></p>';

        echo '<hr>';
        echo '<h3 style="margin:0 0 12px;">空き状況イベント</h3>';
        echo '<p class="description" style="margin:0 0 12px;">例: 2026-04-20〜2026-04-22 を「予約済み」にすると、その期間は front で選択不可になります。</p>';

        echo '<table class="widefat striped" id="mnpk-availability-table" style="margin-top:8px;">';
        echo '<thead><tr>';
        echo '<th style="width:22%;">開始日</th>';
        echo '<th style="width:22%;">終了日</th>';
        echo '<th style="width:18%;">状態</th>';
        echo '<th>メモ</th>';
        echo '<th style="width:90px;">操作</th>';
        echo '</tr></thead>';
        echo '<tbody>';

        foreach ($events as $event) {
            $start  = isset($event['start']) ? (string) $event['start'] : '';
            $end    = isset($event['end']) ? (string) $event['end'] : '';
            $status = isset($event['status']) ? (string) $event['status'] : 'available';
            $note   = isset($event['note']) ? (string) $event['note'] : '';

            if (!array_key_exists($status, $status_options)) {
                $status = 'available';
            }

            echo '<tr class="mnpk-availability-row">';
            echo '<td><input type="date" name="mnpk_event_start[]" value="' . esc_attr($start) . '" style="width:100%;"></td>';
            echo '<td><input type="date" name="mnpk_event_end[]" value="' . esc_attr($end) . '" style="width:100%;"></td>';

            echo '<td><select name="mnpk_event_status[]" style="width:100%;">';
            foreach ($status_options as $status_key => $status_label) {
                echo '<option value="' . esc_attr($status_key) . '" ' . selected($status, $status_key, false) . '>' . esc_html($status_label) . '</option>';
            }
            echo '</select></td>';

            echo '<td><input type="text" name="mnpk_event_note[]" value="' . esc_attr($note) . '" style="width:100%;" placeholder="例: オーナー利用 / 清掃 / 予約済み"></td>';
            echo '<td><button type="button" class="button mnpk-remove-row">削除</button></td>';
            echo '</tr>';
        }

        echo '</tbody></table>';

        echo '<p style="margin-top:12px;">';
        echo '<button type="button" class="button button-secondary" id="mnpk-add-availability-row">行を追加</button>';
        echo '</p>';

        echo '<script type="text/template" id="mnpk-availability-row-template">';
        echo '<tr class="mnpk-availability-row">';
        echo '<td><input type="date" name="mnpk_event_start[]" value="" style="width:100%;"></td>';
        echo '<td><input type="date" name="mnpk_event_end[]" value="" style="width:100%;"></td>';
        echo '<td><select name="mnpk_event_status[]" style="width:100%;">';
        foreach ($status_options as $status_key => $status_label) {
            echo '<option value="' . esc_attr($status_key) . '">' . esc_html($status_label) . '</option>';
        }
        echo '</select></td>';
        echo '<td><input type="text" name="mnpk_event_note[]" value="" style="width:100%;" placeholder="例: オーナー利用 / 清掃 / 予約済み"></td>';
        echo '<td><button type="button" class="button mnpk-remove-row">削除</button></td>';
        echo '</tr>';
        echo '</script>';
    }
}

if (!function_exists('mnpk_admin_enqueue_availability')) {
    function mnpk_admin_enqueue_availability($hook)
    {
        global $post;

        if (($hook !== 'post.php' && $hook !== 'post-new.php') || !$post || $post->post_type !== 'minpaku') {
            return;
        }

        wp_enqueue_script('jquery');

        $js = <<<'JS'
jQuery(function($){
    const tableBody = $('#mnpk-availability-table tbody');
    const template  = $('#mnpk-availability-row-template').html();

    $('#mnpk-add-availability-row').on('click', function(e){
        e.preventDefault();
        tableBody.append(template);
    });

    $(document).on('click', '.mnpk-remove-row', function(e){
        e.preventDefault();

        const rows = tableBody.find('.mnpk-availability-row');

        if (rows.length <= 1) {
            const row = $(this).closest('tr');
            row.find('input[type="date"]').val('');
            row.find('input[type="text"]').val('');
            row.find('select').val('available');
            return;
        }

        $(this).closest('tr').remove();
    });
});
JS;

        wp_add_inline_script('jquery', $js);
    }
}
add_action('admin_enqueue_scripts', 'mnpk_admin_enqueue_availability');

if (!function_exists('mnpk_save_availability_meta')) {
    function mnpk_save_availability_meta($post_id)
    {
        if (
            !isset($_POST['mnpk_availability_meta_nonce']) ||
            !wp_verify_nonce(
                sanitize_text_field(wp_unslash($_POST['mnpk_availability_meta_nonce'])),
                'mnpk_save_availability_meta'
            )
        ) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (get_post_type($post_id) !== 'minpaku' || !current_user_can('edit_post', $post_id)) {
            return;
        }

        $open_start_date = isset($_POST['_mnpk_open_start_date'])
            ? sanitize_text_field(wp_unslash($_POST['_mnpk_open_start_date']))
            : '';

        $cleaning_buffer_days = isset($_POST['_mnpk_cleaning_buffer_days'])
            ? max(0, absint(wp_unslash($_POST['_mnpk_cleaning_buffer_days'])))
            : 0;

        $cleaning_note = isset($_POST['_mnpk_cleaning_note'])
            ? sanitize_textarea_field(wp_unslash($_POST['_mnpk_cleaning_note']))
            : '';

        update_post_meta($post_id, '_mnpk_open_start_date', $open_start_date);
        update_post_meta($post_id, '_mnpk_cleaning_buffer_days', $cleaning_buffer_days);
        update_post_meta($post_id, '_mnpk_cleaning_note', $cleaning_note);

        $starts   = isset($_POST['mnpk_event_start']) ? (array) wp_unslash($_POST['mnpk_event_start']) : array();
        $ends     = isset($_POST['mnpk_event_end']) ? (array) wp_unslash($_POST['mnpk_event_end']) : array();
        $statuses = isset($_POST['mnpk_event_status']) ? (array) wp_unslash($_POST['mnpk_event_status']) : array();
        $notes    = isset($_POST['mnpk_event_note']) ? (array) wp_unslash($_POST['mnpk_event_note']) : array();

        $allowed_statuses = array_keys(mnpk_availability_status_options());
        $events = array();
        $max = max(count($starts), count($ends), count($statuses), count($notes));

        for ($i = 0; $i < $max; $i++) {
            $start  = isset($starts[$i]) ? sanitize_text_field($starts[$i]) : '';
            $end    = isset($ends[$i]) ? sanitize_text_field($ends[$i]) : '';
            $status = isset($statuses[$i]) ? sanitize_key($statuses[$i]) : 'blocked';
            $note   = isset($notes[$i]) ? sanitize_text_field($notes[$i]) : '';

            if ($start === '' || $end === '') {
                continue;
            }

            if ($end < $start) {
                $tmp   = $start;
                $start = $end;
                $end   = $tmp;
            }

            if (!in_array($status, $allowed_statuses, true)) {
                $status = 'blocked';
            }

            $events[] = array(
                'start'  => $start,
                'end'    => $end,
                'status' => $status,
                'note'   => $note,
            );
        }

        update_post_meta($post_id, '_mnpk_calendar_events', wp_json_encode($events, JSON_UNESCAPED_UNICODE));
    }
}
add_action('save_post_minpaku', 'mnpk_save_availability_meta');


/* =========================================================
 * 10. 料金計算
 * =========================================================
 *
 * 重要:
 * - フロント側の合計金額をそのまま信用しない
 * - PHP 側で再計算する
 */
if (!function_exists('mnpk_calculate_booking_total')) {
    function mnpk_calculate_booking_total($post_id, $checkin, $checkout, $adults, $children)
    {
        $result = array(
            'valid'        => false,
            'message'      => '',
            'nights'       => 0,
            'room_fee'     => 0,
            'guest_fee'    => 0,
            'cleaning_fee' => 0,
            'total'        => 0,
        );

        $checkin_ts  = strtotime($checkin);
        $checkout_ts = strtotime($checkout);

        if (!$checkin_ts || !$checkout_ts) {
            $result['message'] = '日付が不正です。';
            return $result;
        }

        $nights = (int) floor(($checkout_ts - $checkin_ts) / DAY_IN_SECONDS);
        if ($nights <= 0) {
            $result['message'] = 'チェックアウト日はチェックイン日の翌日以降にしてください。';
            return $result;
        }

        $nightly_price   = (int) get_post_meta($post_id, '_mnpk_nightly_price', true);
        $weekend_price   = (int) get_post_meta($post_id, '_mnpk_weekend_price', true);
        $cleaning_fee    = (int) get_post_meta($post_id, '_mnpk_cleaning_fee', true);
        $capacity        = max(1, (int) get_post_meta($post_id, '_mnpk_capacity', true));
        $base_guests     = max(1, (int) get_post_meta($post_id, '_mnpk_base_guests', true));
        $extra_guest_fee = max(0, (int) get_post_meta($post_id, '_mnpk_extra_guest_fee', true));
        $min_nights      = max(1, (int) get_post_meta($post_id, '_mnpk_min_nights', true));

        if ($nightly_price <= 0) {
            $result['message'] = '宿泊料金が未設定です。';
            return $result;
        }

        if ($weekend_price <= 0) {
            $weekend_price = $nightly_price;
        }

        if ($nights < $min_nights) {
            $result['message'] = '最低宿泊日数を満たしていません。';
            return $result;
        }

        $guest_count = max(1, (int) $adults + (int) $children);

        if ($guest_count > $capacity) {
            $result['message'] = '定員を超えています。';
            return $result;
        }

        $room_fee = 0;
        $cursor   = $checkin_ts;

        /**
         * 金曜(5)・土曜(6)を週末料金とする
         */
        for ($i = 0; $i < $nights; $i++) {
            $weekday = (int) date('w', $cursor);
            $is_weekend = ($weekday === 5 || $weekday === 6);
            $room_fee += $is_weekend ? $weekend_price : $nightly_price;
            $cursor = strtotime('+1 day', $cursor);
        }

        $extra_guests = max(0, $guest_count - $base_guests);
        $guest_fee    = $extra_guests * $extra_guest_fee * $nights;
        $total        = $room_fee + $guest_fee + $cleaning_fee;

        $result['valid']        = true;
        $result['nights']       = $nights;
        $result['room_fee']     = $room_fee;
        $result['guest_fee']    = $guest_fee;
        $result['cleaning_fee'] = $cleaning_fee;
        $result['total']        = $total;

        return $result;
    }
}


/* =========================================================
 * 11. PaymentIntent 作成
 * =========================================================
 *
 * 役割:
 * - 詳細ページ内の Payment Element 用に PaymentIntent を作る
 * - 外部 checkout.stripe.com へ飛ばさず、同じページ内で決済する
 */
if (!function_exists('mnpk_create_payment_intent')) {
    function mnpk_create_payment_intent()
    {

        /**
         * MINPAKU_PAYMENT_ENVIRONMENT_GUARD
         * =====================================================
         * Stripe環境スイッチの最終防御。
         *
         * JavaScript側の表示状態に関係なく、
         * 決済OFF環境ではStripe PaymentIntentを作成しない。
         *
         * これにより本番でStripeを採用するか未確定でも、
         * テーマ内に決済コードを残したまま安全に公開できる。
         * =====================================================
         */
        if (
            function_exists('mnpk_is_payment_feature_enabled') &&
            !mnpk_is_payment_feature_enabled()
        ) {
            wp_send_json_error(
                array(
                    'message' =>
                        'オンライン決済は現在準備中です。'
                ),
                403
            );
        }
        $nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';

        if (!$nonce || !wp_verify_nonce($nonce, 'mnpk_booking_nonce')) {
            wp_send_json_error(array(
                'message' => '不正なリクエストです。ページを再読み込みしてもう一度お試しください。'
            ), 403);
        }

        $stripe_secret_key = mnpk_get_stripe_secret_key();

        if (!$stripe_secret_key) {
            wp_send_json_error(array(
                'message' => 'Stripe の秘密鍵が未設定です。wp-config.php と docker-compose.yml を確認してください。'
            ), 400);
        }

        $post_id   = isset($_POST['post_id']) ? absint($_POST['post_id']) : 0;
        $checkin   = isset($_POST['check_in']) ? sanitize_text_field(wp_unslash($_POST['check_in'])) : '';
        $checkout  = isset($_POST['check_out']) ? sanitize_text_field(wp_unslash($_POST['check_out'])) : '';
        $adults    = isset($_POST['adults']) ? absint($_POST['adults']) : 1;
        $children  = isset($_POST['children']) ? absint($_POST['children']) : 0;
        $name      = isset($_POST['name']) ? sanitize_text_field(wp_unslash($_POST['name'])) : '';
        $email     = isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '';

        if (!$post_id || get_post_type($post_id) !== 'minpaku') {
            wp_send_json_error(array('message' => '施設情報が見つかりません。'), 400);
        }

        if (!$checkin || !$checkout) {
            wp_send_json_error(array('message' => '宿泊日程を入力してください。'), 400);
        }

        $booking_ok = !empty(get_post_meta($post_id, '_mnpk_booking_enabled', true));
        $payment_ok = !empty(get_post_meta($post_id, '_mnpk_online_payment_enabled', true));

        if (!$booking_ok || !$payment_ok) {
            wp_send_json_error(array('message' => 'この施設は現在オンライン決済予約を受け付けていません。'), 400);
        }

        $calc = mnpk_calculate_booking_total($post_id, $checkin, $checkout, $adults, $children);

        if (!$calc['valid']) {
            wp_send_json_error(array('message' => $calc['message']), 400);
        }

        $guest_count = max(1, $adults + $children);
        $post_title  = get_the_title($post_id);

        $body = array(
            'amount'                             => (string) $calc['total'],
            'currency'                           => 'jpy',
            'automatic_payment_methods[enabled]' => 'true',
            'description'                        => $post_title . ' ご宿泊料金',
            'metadata[post_id]'                  => (string) $post_id,
            'metadata[stay_title]'               => $post_title,
            'metadata[detail_url]'               => get_permalink($post_id),
            'metadata[checkin]'                  => $checkin,
            'metadata[checkout]'                 => $checkout,
            'metadata[adults]'                   => (string) $adults,
            'metadata[children]'                 => (string) $children,
            'metadata[guest_count]'              => (string) $guest_count,
            'metadata[nights]'                   => (string) $calc['nights'],
            'metadata[name]'                     => $name,
            'metadata[email]'                    => $email,
        );

        if ($email !== '') {
            $body['receipt_email'] = $email;
        }

        $response = wp_remote_post('https://api.stripe.com/v1/payment_intents', array(
            'timeout' => 30,
            'headers' => array(
                'Authorization' => 'Bearer ' . $stripe_secret_key,
                'Content-Type'  => 'application/x-www-form-urlencoded',
            ),
            'body' => $body,
        ));

        if (is_wp_error($response)) {
            wp_send_json_error(array(
                'message' => $response->get_error_message()
            ), 500);
        }

        $code = wp_remote_retrieve_response_code($response);
        $json = json_decode(wp_remote_retrieve_body($response), true);

        if ($code >= 300 || empty($json['client_secret'])) {
            $message = !empty($json['error']['message'])
                ? $json['error']['message']
                : 'PaymentIntent の作成に失敗しました。';

            wp_send_json_error(array(
                'message' => $message
            ), 500);
        }

        wp_send_json_success(array(
            'client_secret' => sanitize_text_field($json['client_secret']),
            'amount'        => (int) $calc['total'],
        ));
    }
}
add_action('wp_ajax_mnpk_create_payment_intent', 'mnpk_create_payment_intent');
add_action('wp_ajax_nopriv_mnpk_create_payment_intent', 'mnpk_create_payment_intent');
