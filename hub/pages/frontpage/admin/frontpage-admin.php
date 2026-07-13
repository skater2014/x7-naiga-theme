<?php

/**
 * Frontpage settings admin
 *
 * 管理場所:
 * hub/pages/frontpage/admin/frontpage-admin.php
 *
 * 目的:
 * - 左メニュー「フロントページ設定」を表示
 * - 上部メディア（最大5枠）を、画像 / MP4 / YouTube / Vimeo のどれか1つだけ選ばせる
 * - 選んだ種類に関係ない入力欄は出さない
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('naigai_fp_get')) {
    function naigai_fp_get($post_id, $key, $default = '')
    {
        /*
         * 管理画面用の値取得
         *
         * 役割:
         * - まだ一度も保存されていない項目だけ、管理画面にデフォルト値を表示する
         * - 一度保存された項目は、空文字でもユーザーの入力として尊重する
         *
         * 重要:
         * - フロント側で勝手にデフォルト文言を出さないための前提
         * - 管理画面でデフォルト文言を消して保存した場合、空欄のまま維持される
         */
        if (metadata_exists('post', $post_id, $key)) {
            return get_post_meta($post_id, $key, true);
        }

        return $default;
    }
}

if (!function_exists('naigai_fp_media_field')) {
    function naigai_fp_media_field($post_id, $key, $label, $library_type = 'image', $allowed_mime = 'image')
    {
        $id  = (int) get_post_meta($post_id, $key, true);
        $src = '';

        if ($id) {
            if ($library_type === 'image') {
                $src = wp_get_attachment_image_url($id, 'thumbnail');
            } else {
                $src = wp_get_attachment_image_url($id, 'thumbnail');
                if (!$src) {
                    $src = wp_mime_type_icon($id);
                }
            }
        }
?>
        <div class="fp-admin-media">
            <div class="fp-admin-media__preview">
                <?php if ($src) : ?>
                    <img src="<?php echo esc_url($src); ?>" alt="">
                <?php else : ?>
                    <span>未選択</span>
                <?php endif; ?>
            </div>

            <input type="hidden" name="<?php echo esc_attr($key); ?>" value="<?php echo esc_attr($id); ?>">

            <button
                type="button"
                class="button fp-admin-select-media"
                data-target="<?php echo esc_attr($key); ?>"
                data-library="<?php echo esc_attr($library_type); ?>"
                data-mime="<?php echo esc_attr($allowed_mime); ?>">
                <?php echo esc_html($label); ?>
            </button>

            <button
                type="button"
                class="button fp-admin-clear-media"
                data-target="<?php echo esc_attr($key); ?>">
                クリア
            </button>
        </div>
    <?php
    }
}


if (!function_exists('naigai_fp_render_url_select')) {
    function naigai_fp_render_url_select($name, $current_url, $default_url = '')
    {
        /*
         * URL選択フィールド
         *
         * 役割:
         * - 管理画面でURLを手入力させず、選択一覧から選べるようにする
         * - valueには固定ページIDではなく、URL文字列を保存する
         * - 未保存の場合は $default_url を選択状態にする
         * - 固定ページのURLが変わる可能性がある場合でも、管理画面で再選択できる
         *
         * 保存される値:
         * - /contact/ などのURL文字列
         * - 「リンクなし」は __none__ として保存する
         */
        $current_url = trim((string) $current_url);
        $default_url = trim((string) $default_url);

        if ($current_url === '' && $default_url !== '') {
            $current_url = $default_url;
        }

        $url_options = array(
            home_url('/')                         => 'トップページ',
            home_url('/fudousan/')                => '不動産を見る',
            home_url('/iezukuri/')                => '家づくりを見る',
            home_url('/iezukuri/plans/')          => '間取りプラン一覧',
            home_url('/minpaku/')                 => '宿泊を探す',
            home_url('/contact/')                 => 'お問い合わせ',
            home_url('/reservation/')             => '来店予約',
            home_url('/nasu-ideal-home/')         => '那須の魅力',
            home_url('/room-gallary/')            => 'お部屋ギャラリー / 分譲地案内',
            home_url('/zairai-kouhou/')           => '自然素材・在来工法',
            home_url('/hokubei-jutaku/')          => '北米住宅',
            home_url('/nasu-used-renovation/')    => '中古住宅リノベーション',
        );

        $pages = get_posts(array(
            'post_type'      => 'page',
            'post_status'    => array('publish', 'private', 'draft'),
            'posts_per_page' => -1,
            'orderby'        => 'menu_order title',
            'order'          => 'ASC',
        ));

        foreach ($pages as $page) {
            $page_url = get_permalink($page->ID);
            if (!$page_url) {
                continue;
            }

            $url_options[$page_url] = get_the_title($page->ID) . ' / ' . wp_make_link_relative($page_url);
        }
    ?>
        <select name="<?php echo esc_attr($name); ?>">
            <option value="__none__" <?php selected($current_url, '__none__'); ?>>リンクなし</option>
            <?php foreach ($url_options as $url => $label) : ?>
                <option value="<?php echo esc_url($url); ?>" <?php selected(untrailingslashit($current_url), untrailingslashit($url)); ?>>
                    <?php echo esc_html($label); ?>
                </option>
            <?php endforeach; ?>
        </select>
    <?php
    }
}

if (!function_exists('naigai_fp_normalize_media_values')) {
    function naigai_fp_normalize_media_values($post_id)
    {
        for ($i = 1; $i <= 5; $i++) {
            $type = get_post_meta($post_id, "_fp_hero_media_{$i}_type", true);
            if (!in_array($type, array('image', 'mp4', 'youtube', 'vimeo'), true)) {
                $type = 'image';
                update_post_meta($post_id, "_fp_hero_media_{$i}_type", $type);
            }

            if ($type === 'image') {
                delete_post_meta($post_id, "_fp_hero_media_{$i}_mp4_id");
                delete_post_meta($post_id, "_fp_hero_media_{$i}_mp4_url");
                delete_post_meta($post_id, "_fp_hero_media_{$i}_youtube");
                delete_post_meta($post_id, "_fp_hero_media_{$i}_vimeo");
            }

            if ($type === 'mp4') {
                delete_post_meta($post_id, "_fp_hero_media_{$i}_youtube");
                delete_post_meta($post_id, "_fp_hero_media_{$i}_vimeo");
            }

            if ($type === 'youtube') {
                delete_post_meta($post_id, "_fp_hero_media_{$i}_image_id");
                delete_post_meta($post_id, "_fp_hero_media_{$i}_mp4_id");
                delete_post_meta($post_id, "_fp_hero_media_{$i}_mp4_url");
                delete_post_meta($post_id, "_fp_hero_media_{$i}_vimeo");
            }

            if ($type === 'vimeo') {
                delete_post_meta($post_id, "_fp_hero_media_{$i}_image_id");
                delete_post_meta($post_id, "_fp_hero_media_{$i}_mp4_id");
                delete_post_meta($post_id, "_fp_hero_media_{$i}_mp4_url");
                delete_post_meta($post_id, "_fp_hero_media_{$i}_youtube");
            }
        }
    }
}

if (!function_exists('naigai_fp_save_settings')) {
    function naigai_fp_save_settings($post_id)
    {
        $text_keys = array(
            '_fp_hero_kicker',
            '_fp_hero_title',
            '_fp_hero_lead',
            '_fp_business_title',
            '_fp_business_text',
            '_fp_life_image_mode',
        );

        for ($i = 1; $i <= 5; $i++) {
            $text_keys[] = "_fp_hero_media_{$i}_type";
            $text_keys[] = "_fp_hero_media_{$i}_label";
            $text_keys[] = "_fp_hero_media_{$i}_mp4_url";
            $text_keys[] = "_fp_hero_media_{$i}_youtube";
            $text_keys[] = "_fp_hero_media_{$i}_vimeo";

            $text_keys[] = "_fp_notice_{$i}_date";
            $text_keys[] = "_fp_notice_{$i}_label";
            $text_keys[] = "_fp_notice_{$i}_title";
            $text_keys[] = "_fp_notice_{$i}_text";
            $text_keys[] = "_fp_notice_{$i}_url";
        }

        $id_keys = array();

        for ($i = 1; $i <= 5; $i++) {
            $id_keys[] = "_fp_hero_media_{$i}_image_id";
            $id_keys[] = "_fp_hero_media_{$i}_mp4_id";
            $id_keys[] = "_fp_notice_{$i}_thumb_id";
        }

        /* Frontpage managed content fields */
        for ($i = 1; $i <= 4; $i++) {
            $text_keys[] = "_fp_hero_btn_{$i}_label";
            $text_keys[] = "_fp_hero_btn_{$i}_url";
            $text_keys[] = "_fp_hero_btn_{$i}_color";
        }

        foreach (array('realestate', 'home', 'stay', 'business') as $service_key) {
            $text_keys[] = "_fp_service_{$service_key}_title";
            $text_keys[] = "_fp_service_{$service_key}_text";
            $text_keys[] = "_fp_service_{$service_key}_url";
        }

        $text_keys[] = '_fp_business_button_label';
        $text_keys[] = '_fp_business_button_url';

        for ($i = 1; $i <= 4; $i++) {
            $text_keys[] = "_fp_business_check_{$i}";
        }

        $text_keys[] = '_fp_life_kicker';
        $text_keys[] = '_fp_life_title';
        $text_keys[] = '_fp_life_text';
        $text_keys[] = '_fp_life_button_label';
        $text_keys[] = '_fp_life_button_url';

        /*
         * 暮らしカード保存項目
         *
         * 役割:
         * - 「那須での暮らし」スライダーの各カードを保存する
         * - タイトル / 本文 / リンクURL / 画像ID を保存する
         * - リンク先は「固定ページID」ではなく、URL文字列として保存する
         *
         * 理由:
         * - 管理画面でURLを選択できるようにするため
         * - フロント側では保存されたURLをそのままカードリンクに反映するため
         *
         * メタキー:
         * - _fp_life_point_1_title
         * - _fp_life_point_1_text
         * - _fp_life_point_1_url
         * - _fp_life_point_1_image_id
         */
        for ($i = 1; $i <= 4; $i++) {
            $text_keys[] = "_fp_life_point_{$i}_title";
            $text_keys[] = "_fp_life_point_{$i}_text";
            $text_keys[] = "_fp_life_point_{$i}_url";
            $id_keys[]   = "_fp_life_point_{$i}_image_id";
        }

        $text_keys[] = '_fp_cta_title';
        $text_keys[] = '_fp_cta_btn1_label';
        $text_keys[] = '_fp_cta_btn1_url';
        $text_keys[] = '_fp_cta_btn2_label';
        $text_keys[] = '_fp_cta_btn2_url';

        $id_keys[] = '_fp_life_image_id';
        $id_keys[] = '_fp_cta_image_id';

        foreach ($text_keys as $key) {
            if (isset($_POST[$key])) {
                update_post_meta($post_id, $key, sanitize_textarea_field(wp_unslash($_POST[$key])));
            }
        }

        foreach ($id_keys as $key) {
            if (isset($_POST[$key])) {
                update_post_meta($post_id, $key, absint($_POST[$key]));
            }
        }

        naigai_fp_normalize_media_values($post_id);
    }
}

if (!function_exists('naigai_fp_render_media_slot')) {
    function naigai_fp_render_media_slot($post_id, $i)
    {
        $type = naigai_fp_get($post_id, "_fp_hero_media_{$i}_type", 'image');
        if (!in_array($type, array('image', 'mp4', 'youtube', 'vimeo'), true)) {
            $type = 'image';
        }

        $slot_label = $i === 1 ? 'メディア1（大）' : 'メディア' . $i . '（小）';
    ?>
        <section class="fp-admin-card fp-admin-media-slot" data-fp-media-slot>
            <h2><?php echo esc_html($slot_label); ?></h2>

            <div class="fp-admin-field">
                <label>表示タイプ</label>
                <select name="_fp_hero_media_<?php echo (int) $i; ?>_type" class="js-fp-media-type">
                    <option value="image" <?php selected($type, 'image'); ?>>画像だけ表示</option>
                    <option value="mp4" <?php selected($type, 'mp4'); ?>>MP4動画だけ表示</option>
                    <option value="youtube" <?php selected($type, 'youtube'); ?>>YouTubeを表示</option>
                    <option value="vimeo" <?php selected($type, 'vimeo'); ?>>Vimeoを表示</option>
                </select>
                <p class="description">ここで選んだ種類だけがフロントに表示されます。他の入力欄は使いません。</p>
            </div>

            <div class="fp-admin-media-row" data-fp-row="image">
                <h3>画像</h3>
                <p class="description">画像表示の時だけ使います。</p>
                <?php naigai_fp_media_field($post_id, "_fp_hero_media_{$i}_image_id", '画像を選択', 'image', 'image'); ?>
            </div>

            <div class="fp-admin-media-row" data-fp-row="mp4">
                <h3>MP4動画</h3>
                <p class="description">MP4動画表示の時だけ使います。MP4以外は選べません。</p>
                <?php naigai_fp_media_field($post_id, "_fp_hero_media_{$i}_mp4_id", 'MP4動画を選択', 'video', 'video/mp4'); ?>
            </div>

            <div class="fp-admin-media-row" data-fp-row="youtube">
                <h3>YouTube</h3>
                <p class="description">YouTube表示の時だけ使います。ID または URL を入力。</p>
                <input
                    type="text"
                    name="_fp_hero_media_<?php echo (int) $i; ?>_youtube"
                    value="<?php echo esc_attr(naigai_fp_get($post_id, "_fp_hero_media_{$i}_youtube", '')); ?>"
                    placeholder="例: dQw4w9WgXcQ または YouTube URL">
            </div>

            <div class="fp-admin-media-row" data-fp-row="vimeo">
                <h3>Vimeo</h3>
                <p class="description">Vimeo表示の時だけ使います。ID または URL を入力。</p>
                <input
                    type="text"
                    name="_fp_hero_media_<?php echo (int) $i; ?>_vimeo"
                    value="<?php echo esc_attr(naigai_fp_get($post_id, "_fp_hero_media_{$i}_vimeo", '')); ?>"
                    placeholder="例: 123456789 または Vimeo URL">
            </div>

            <div class="fp-admin-field">
                <label>表示ラベル</label>
                <input
                    type="text"
                    name="_fp_hero_media_<?php echo (int) $i; ?>_label"
                    value="<?php echo esc_attr(naigai_fp_get($post_id, "_fp_hero_media_{$i}_label", '')); ?>"
                    placeholder="未入力なら種類名を表示">
            </div>
        </section>
    <?php
    }
}

if (!function_exists('naigai_fp_render_settings_page')) {
    function naigai_fp_render_settings_page()
    {
        $post_id = (int) get_option('page_on_front');
        $post    = $post_id ? get_post($post_id) : null;

        echo '<div class="wrap fp-admin-wrap">';
        echo '<h1>フロントページ設定</h1>';

        if (isset($_GET['updated']) && $_GET['updated'] === '1') {
            echo '<div class="notice notice-success is-dismissible"><p>保存しました。</p></div>';
        }

        if (!$post) {
            echo '<div class="notice notice-error"><p>フロントページが設定されていません。</p></div>';
            echo '</div>';
            return;
        }

        echo '<p><strong>編集対象:</strong> ' . esc_html(get_the_title($post_id)) . ' / ID: ' . esc_html($post_id) . '</p>';

        echo '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '">';
        echo '<input type="hidden" name="action" value="naigai_save_frontpage_settings">';
        echo '<input type="hidden" name="frontpage_post_id" value="' . esc_attr($post_id) . '">';
        wp_nonce_field('naigai_save_frontpage_settings', 'naigai_frontpage_nonce');

    ?>
        <section class="fp-admin-card">
            <h2>ヒーロー文言</h2>

            <div class="fp-admin-field">
                <label>小見出し</label>
                <input type="text" name="_fp_hero_kicker" value="<?php echo esc_attr(naigai_fp_get($post_id, '_fp_hero_kicker', 'NASU GROUP')); ?>">
            </div>

            <div class="fp-admin-field">
                <label>大見出し</label>
                <textarea name="_fp_hero_title"><?php echo esc_textarea(naigai_fp_get($post_id, '_fp_hero_title', "那須の住まいと暮らしの相談窓口")); ?></textarea>
            </div>

            <div class="fp-admin-field">
                <label>リード文</label>
                <textarea name="_fp_hero_lead"><?php echo esc_textarea(naigai_fp_get($post_id, '_fp_hero_lead', "不動産探しから家づくり、宿泊・民泊、事業活用まで。那須での暮らしをまとめて相談できます。")); ?></textarea>
            </div>
        </section>

        <section class="fp-admin-card fp-admin-media-help">
            <h2>上部メディア（最大5枠）</h2>
            <p class="description">メディア1は大きく、メディア2〜5は右側に表示します。各枠で「画像 / MP4 / YouTube / Vimeo」のどれか1つを選びます。選んだタイプの入力欄だけ表示します。</p>
        </section>

        <div class="fp-admin-media-grid">
            <?php
            for ($i = 1; $i <= 5; $i++) {
                naigai_fp_render_media_slot($post_id, $i);
            }
            ?>
        </div>

        <section class="fp-admin-card">
            <h2>お知らせ・コラム 簡易入力</h2>

            <?php for ($i = 1; $i <= 3; $i++) : ?>
                <div class="fp-admin-notice-item">
                    <h3>お知らせ <?php echo (int) $i; ?></h3>

                    <div class="fp-admin-field">
                        <label>サムネイル</label>
                        <?php naigai_fp_media_field($post_id, "_fp_notice_{$i}_thumb_id", 'サムネイルを選択', 'image', 'image'); ?>
                    </div>

                    <div class="fp-admin-field fp-admin-half">
                        <label>日付</label>
                        <input type="text" name="_fp_notice_<?php echo (int) $i; ?>_date" value="<?php echo esc_attr(naigai_fp_get($post_id, "_fp_notice_{$i}_date", '')); ?>" placeholder="2024.05.10">
                    </div>

                    <div class="fp-admin-field fp-admin-half">
                        <label>ラベル</label>
                        <input type="text" name="_fp_notice_<?php echo (int) $i; ?>_label" value="<?php echo esc_attr(naigai_fp_get($post_id, "_fp_notice_{$i}_label", 'お知らせ')); ?>">
                    </div>

                    <div class="fp-admin-field">
                        <label>タイトル</label>
                        <input type="text" name="_fp_notice_<?php echo (int) $i; ?>_title" value="<?php echo esc_attr(naigai_fp_get($post_id, "_fp_notice_{$i}_title", '')); ?>">
                    </div>

                    <div class="fp-admin-field">
                        <label>本文</label>
                        <textarea name="_fp_notice_<?php echo (int) $i; ?>_text"><?php echo esc_textarea(naigai_fp_get($post_id, "_fp_notice_{$i}_text", '')); ?></textarea>
                    </div>

                    <div class="fp-admin-field">
                        <label>リンクURL</label>
                        <?php
                        /*
                         * お知らせ・コラムURL
                         *
                         * 役割:
                         * - カスタム投稿を作る前でも、PR用の固定ページや通常ページへ誘導できるようにする
                         * - URLは手入力ではなく選択一覧から選ぶ
                         */
                        naigai_fp_render_url_select(
                            "_fp_notice_{$i}_url",
                            get_post_meta($post_id, "_fp_notice_{$i}_url", true),
                            home_url('/')
                        );
                        ?>
                    </div>
                </div>
            <?php endfor; ?>
        </section>
        <!-- Frontpage Managed Content Fields -->

        <section class="fp-admin-card">
            <h2>Hero CTA</h2>
            <p>PCでは4個まで表示できます。タブレット・モバイルの表示数はCSS側で2個に制限します。</p>

            <div class="fp-admin-media-grid">
                <?php
                $fp_hero_btn_defaults = array(
                    1 => array('不動産を見る', home_url('/fudousan/'), 'blue'),
                    2 => array('家づくりを見る', home_url('/iezukuri/'), 'green'),
                    3 => array('宿泊を探す', home_url('/minpaku/'), 'gold'),
                    4 => array('お問い合わせ', home_url('/contact/'), 'outline'),
                );

                for ($i = 1; $i <= 4; $i++) :
                    $btn_label = naigai_fp_get($post_id, "_fp_hero_btn_{$i}_label", $fp_hero_btn_defaults[$i][0]);
                    $btn_url   = naigai_fp_get($post_id, "_fp_hero_btn_{$i}_url", $fp_hero_btn_defaults[$i][1]);
                    $btn_color = naigai_fp_get($post_id, "_fp_hero_btn_{$i}_color", $fp_hero_btn_defaults[$i][2]);
                ?>
                    <section class="fp-admin-card fp-admin-media-slot">
                        <h2>Hero CTA <?php echo (int) $i; ?></h2>

                        <div class="fp-admin-field">
                            <label>ボタン文言</label>
                            <input type="text" name="_fp_hero_btn_<?php echo (int) $i; ?>_label" value="<?php echo esc_attr($btn_label); ?>">
                        </div>

                        <div class="fp-admin-field">
                            <label>URL</label>
                            <?php
                            /*
                             * Hero CTA URL
                             * - URLを直接入力させず、管理画面の選択一覧から選ぶ
                             * - デフォルトは既存の $fp_hero_btn_defaults を使う
                             */
                            naigai_fp_render_url_select(
                                "_fp_hero_btn_{$i}_url",
                                get_post_meta($post_id, "_fp_hero_btn_{$i}_url", true),
                                $fp_hero_btn_defaults[$i][1]
                            );
                            ?>
                        </div>

                        <div class="fp-admin-field">
                            <label>色</label>
                            <select name="_fp_hero_btn_<?php echo (int) $i; ?>_color">
                                <option value="blue" <?php selected($btn_color, 'blue'); ?>>青</option>
                                <option value="green" <?php selected($btn_color, 'green'); ?>>緑</option>
                                <option value="gold" <?php selected($btn_color, 'gold'); ?>>金</option>
                                <option value="outline" <?php selected($btn_color, 'outline'); ?>>白枠</option>
                            </select>
                        </div>
                    </section>
                <?php endfor; ?>
            </div>
        </section>

        <section class="fp-admin-card">
            <h2>サービスカード</h2>
            <p>フロントページ上部の4つの入口カードを管理します。</p>

            <div class="fp-admin-media-grid">
                <?php
                $fp_service_defaults = array(
                    'realestate' => array('不動産の窓口', '土地や中古住宅の購入・売却、住み替えまで住まい探しをトータルサポート。', home_url('/fudousan/')),
                    'home'       => array('家づくりの窓口', '注文住宅・リノベーションなど理想の住まいをカタチにします。', home_url('/iezukuri/')),
                    'stay'       => array('民泊・住宅宿泊', '那須の魅力を体験できる宿泊施設の運営や、住宅宿泊・民泊運用相談を承ります。', home_url('/minpaku/')),
                    'business'   => array('法人向けのご相談', '土地活用・建物活用・事業用途のご提案やパートナー相談まで、事業の可能性を広げます。', home_url('/contact/')),
                );

                $fp_service_labels = array(
                    'realestate' => '不動産',
                    'home'       => '家づくり',
                    'stay'       => '民泊・宿泊',
                    'business'   => '法人相談',
                );

                foreach ($fp_service_defaults as $service_key => $defaults) :
                ?>
                    <section class="fp-admin-card fp-admin-media-slot">
                        <h2><?php echo esc_html($fp_service_labels[$service_key]); ?></h2>

                        <div class="fp-admin-field">
                            <label>タイトル</label>
                            <input type="text" name="_fp_service_<?php echo esc_attr($service_key); ?>_title" value="<?php echo esc_attr(naigai_fp_get($post_id, "_fp_service_{$service_key}_title", $defaults[0])); ?>">
                        </div>

                        <div class="fp-admin-field">
                            <label>本文</label>
                            <textarea name="_fp_service_<?php echo esc_attr($service_key); ?>_text"><?php echo esc_textarea(naigai_fp_get($post_id, "_fp_service_{$service_key}_text", $defaults[1])); ?></textarea>
                        </div>

                        <div class="fp-admin-field">
                            <label>URL</label>
                            <?php
                            /*
                             * サービスカードURL
                             * - 不動産 / 家づくり / 民泊 / 法人相談 のリンク先を選択式で管理する
                             */
                            naigai_fp_render_url_select(
                                "_fp_service_{$service_key}_url",
                                get_post_meta($post_id, "_fp_service_{$service_key}_url", true),
                                $defaults[2]
                            );
                            ?>
                        </div>
                    </section>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="fp-admin-card">
            <h2>法人向けブロック</h2>

            <div class="fp-admin-field">
                <label>タイトル</label>
                <input type="text" name="_fp_business_title" value="<?php echo esc_attr(naigai_fp_get($post_id, '_fp_business_title', '事業用途・パートナー開発もサポート')); ?>">
            </div>

            <div class="fp-admin-field">
                <label>本文</label>
                <textarea name="_fp_business_text"><?php echo esc_textarea(naigai_fp_get($post_id, '_fp_business_text', '土地の使い方や建物の活用方法に応じて、事業化の企画から設計・運営、パートナー・協業までワンストップで支援します。')); ?></textarea>
            </div>

            <div class="fp-admin-media-grid">
                <?php
                $fp_business_check_defaults = array(
                    1 => '土地活用',
                    2 => '建物活用',
                    3 => '事業用途',
                    4 => 'パートナー相談',
                );

                for ($i = 1; $i <= 4; $i++) :
                ?>
                    <div class="fp-admin-field">
                        <label>チェック項目<?php echo (int) $i; ?></label>
                        <input type="text" name="_fp_business_check_<?php echo (int) $i; ?>" value="<?php echo esc_attr(naigai_fp_get($post_id, "_fp_business_check_{$i}", $fp_business_check_defaults[$i])); ?>">
                    </div>
                <?php endfor; ?>
            </div>

            <div class="fp-admin-field">
                <label>ボタン文言</label>
                <input type="text" name="_fp_business_button_label" value="<?php echo esc_attr(naigai_fp_get($post_id, '_fp_business_button_label', '法人向けのご相談はこちら →')); ?>">
            </div>

            <div class="fp-admin-field">
                <label>ボタンURL</label>
                <?php
                /*
                 * 法人向けブロックのボタンURL
                 * - PR・問い合わせ・法人向け固定ページなどに差し替えられるようにする
                 */
                naigai_fp_render_url_select(
                    '_fp_business_button_url',
                    get_post_meta($post_id, '_fp_business_button_url', true),
                    home_url('/contact/')
                );
                ?>
            </div>
        </section>

        <section class="fp-admin-card">
            <h2>那須での暮らし</h2>

            <?php
            /*
             * URL選択肢
             *
             * 役割:
             * - 「那須での暮らし」内で使うURLを、管理画面のselectから選べるようにする
             * - selectのvalueには固定ページIDではなくURL文字列を入れる
             * - 既定の4ページに加えて、存在する固定ページも一覧に出す
             *
             * 重要:
             * - デフォルトURLは今までの指定をそのまま使う
             * - 保存される値は _fp_life_button_url / _fp_life_point_1_url など
             */
            $fp_life_default_urls = array(
                'life_button' => home_url('/nasu-ideal-home/'),
                1 => home_url('/room-gallary/'),
                2 => home_url('/zairai-kouhou/'),
                3 => home_url('/hokubei-jutaku/'),
                4 => home_url('/nasu-used-renovation/'),
            );

            $fp_life_url_options = array(
                home_url('/nasu-ideal-home/')        => '那須の魅力',
                home_url('/room-gallary/')           => 'お部屋ギャラリー / 分譲地案内',
                home_url('/zairai-kouhou/')          => '自然素材・在来工法',
                home_url('/hokubei-jutaku/')         => '北米住宅',
                home_url('/nasu-used-renovation/')   => '中古住宅リノベーション',
            );

            $fp_life_pages = get_posts(array(
                'post_type'      => 'page',
                'post_status'    => array('publish', 'private', 'draft'),
                'posts_per_page' => -1,
                'orderby'        => 'menu_order title',
                'order'          => 'ASC',
            ));

            foreach ($fp_life_pages as $link_page) {
                $page_url = get_permalink($link_page->ID);
                if (!$page_url) {
                    continue;
                }

                $fp_life_url_options[$page_url] = get_the_title($link_page->ID) . ' / ' . wp_make_link_relative($page_url);
            }

            $fp_life_render_url_select = function ($name, $current_url, $default_url) use ($fp_life_url_options) {
                /*
                 * URL選択select
                 *
                 * 役割:
                 * - 未保存なら default_url を選択状態にする
                 * - 「リンクなし」を選んだ場合は __none__ を保存する
                 * - URL文字列をそのまま保存するので、フロント側はそのURLをそのまま使える
                 */
                $current_url = trim((string) $current_url);

                if ($current_url === '') {
                    $current_url = $default_url;
                }
            ?>
                <select name="<?php echo esc_attr($name); ?>">
                    <option value="__none__" <?php selected($current_url, '__none__'); ?>>リンクなし</option>

                    <?php foreach ($fp_life_url_options as $url => $label) : ?>
                        <option value="<?php echo esc_url($url); ?>" <?php selected(untrailingslashit($current_url), untrailingslashit($url)); ?>>
                            <?php echo esc_html($label); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php
            };
            ?>

            <div class="fp-admin-field">
                <label>ラベル</label>
                <input type="text" name="_fp_life_kicker" value="<?php echo esc_attr(naigai_fp_get($post_id, '_fp_life_kicker', 'NASU LIFE')); ?>">
            </div>

            <div class="fp-admin-field">
                <label>タイトル</label>
                <input type="text" name="_fp_life_title" value="<?php echo esc_attr(naigai_fp_get($post_id, '_fp_life_title', '那須での暮らし')); ?>">
            </div>

            <div class="fp-admin-field">
                <label>本文</label>
                <textarea name="_fp_life_text"><?php echo esc_textarea(naigai_fp_get($post_id, '_fp_life_text', '豊かな自然に囲まれ、四季の移ろいを感じられる那須。子育て世代からセカンドライフまで、自分らしい暮らし方が見つかります。')); ?></textarea>
            </div>

            <div class="fp-admin-field">
                <label>CTAボタン文言</label>
                <input type="text" name="_fp_life_button_label" value="<?php echo esc_attr(naigai_fp_get($post_id, '_fp_life_button_label', '那須の魅力をもっと見る')); ?>">
                <p class="description">左側CTAボタンの文言です。空にするとフロントには表示しません。</p>
            </div>

            <div class="fp-admin-field">
                <label>CTAリンク先URL（那須の魅力をもっと見る）</label>
                <?php
                /*
                 * 左側CTAボタンURL
                 *
                 * 役割:
                 * - 「那須の魅力をもっと見る」のリンク先URLを管理画面で選択する
                 * - 未保存なら /nasu-ideal-home/ をデフォルトにする
                 */
                $fp_life_render_url_select(
                    '_fp_life_button_url',
                    get_post_meta($post_id, '_fp_life_button_url', true),
                    $fp_life_default_urls['life_button']
                );
                ?>
                <p class="description">左側CTA「那須の魅力をもっと見る」のリンク先です。未保存なら /nasu-ideal-home/、リンクなしならフロントではボタンを出しません。</p>
            </div>

            <div class="fp-admin-field">
                <label>セクション画像</label>
                <?php naigai_fp_media_field($post_id, '_fp_life_image_id', '那須での暮らし画像を選択', 'image', 'image'); ?>
            </div>

            <div class="fp-admin-field">
                <label>画像の使い方</label>
                <?php $fp_life_image_mode = naigai_fp_get($post_id, '_fp_life_image_mode', 'side'); ?>
                <select name="_fp_life_image_mode">
                    <option value="side" <?php selected($fp_life_image_mode, 'side'); ?>>本文とカードの間に画像を表示</option>
                    <option value="background" <?php selected($fp_life_image_mode, 'background'); ?>>背景画像として表示</option>
                </select>
            </div>

            <h3>暮らしカード</h3>
            <div class="fp-admin-media-grid">
                <?php
                /*
                 * 暮らしカード入力欄
                 *
                 * 役割:
                 * - タイトル / 本文 / リンク先URL / 画像をカードごとに管理する
                 * - リンク先URLはselectで選ぶ
                 * - フロント表示の空判定は「タイトル・本文・画像」だけで行う
                 * - リンク先URLだけ選んでいてもカードは表示しない
                 *
                 * 保存されるメタキー:
                 * - _fp_life_point_1_title
                 * - _fp_life_point_1_text
                 * - _fp_life_point_1_url
                 * - _fp_life_point_1_image_id
                 */
                for ($i = 1; $i <= 4; $i++) :
                    if ($i === 1) {
                        $default_title = '自然と共に暮らす';
                        $default_text  = '美しい自然を身近に感じられる環境。';
                        $default_url   = $fp_life_default_urls[1];
                    } elseif ($i === 2) {
                        $default_title = 'アクセスも快適';
                        $default_text  = '週末利用や拠点づくりにも対応。';
                        $default_url   = $fp_life_default_urls[2];
                    } elseif ($i === 3) {
                        $default_title = '家族にやさしい環境';
                        $default_text  = '安心して暮らせる地域環境。';
                        $default_url   = $fp_life_default_urls[3];
                    } else {
                        $default_title = '';
                        $default_text  = '';
                        $default_url   = $fp_life_default_urls[4];
                    }
                ?>
                    <section class="fp-admin-card fp-admin-media-slot">
                        <h2>カード<?php echo (int) $i; ?></h2>

                        <div class="fp-admin-field">
                            <label>タイトル</label>
                            <input
                                type="text"
                                name="_fp_life_point_<?php echo (int) $i; ?>_title"
                                value="<?php echo esc_attr(naigai_fp_get($post_id, "_fp_life_point_{$i}_title", $default_title)); ?>">
                        </div>

                        <div class="fp-admin-field">
                            <label>本文</label>
                            <textarea name="_fp_life_point_<?php echo (int) $i; ?>_text"><?php echo esc_textarea(naigai_fp_get($post_id, "_fp_life_point_{$i}_text", $default_text)); ?></textarea>
                        </div>

                        <div class="fp-admin-field">
                            <label>リンク先URL</label>
                            <?php
                            $fp_life_render_url_select(
                                "_fp_life_point_{$i}_url",
                                get_post_meta($post_id, "_fp_life_point_{$i}_url", true),
                                $default_url
                            );
                            ?>
                            <p class="description">このカードをクリックした時のURLです。URLだけではフロントに表示しません。タイトル・本文・カード画像が全部空なら、このカードは非表示です。</p>
                        </div>

                        <div class="fp-admin-field">
                            <label>カード画像</label>
                            <?php naigai_fp_media_field($post_id, "_fp_life_point_{$i}_image_id", 'カード画像を選択', 'image', 'image'); ?>
                        </div>
                    </section>
                <?php endfor; ?>
            </div>
        </section>

        <section class="fp-admin-card">
            <h2>最後のCTA</h2>

            <div class="fp-admin-field">
                <label>CTA見出し</label>
                <?php
                /*
                 * 最後のCTA見出し
                 *
                 * 役割:
                 * - 最後の横長CTAに表示する見出しを管理する
                 * - ここは段落を増やしたくないので textarea ではなく input にする
                 * - 既にDBに改行入りで保存されている場合も、管理画面表示時に1行へ戻す
                 * - フロント側でも wp_strip_all_tags + preg_replace で1行化して表示する
                 *
                 * 保存メタキー:
                 * - _fp_cta_title
                 */
                $fp_cta_title_admin = naigai_fp_get($post_id, '_fp_cta_title', '那須の住まい・不動産・宿泊のこと、お気軽にご相談ください');
                $fp_cta_title_admin = trim(preg_replace('/\s+/u', ' ', wp_strip_all_tags((string) $fp_cta_title_admin)));
                ?>
                <input
                    type="text"
                    name="_fp_cta_title"
                    value="<?php echo esc_attr($fp_cta_title_admin); ?>">
                <p class="description">改行なしの1行見出しです。不要な段落や改行を作りません。</p>
            </div>

            <div class="fp-admin-field">
                <label>ボタン1 文言</label>
                <input type="text" name="_fp_cta_btn1_label" value="<?php echo esc_attr(naigai_fp_get($post_id, '_fp_cta_btn1_label', 'お問い合わせ')); ?>">
            </div>

            <div class="fp-admin-field">
                <label>ボタン1 URL</label>
                <?php
                /*
                 * 最後のCTA ボタン1 URL
                 * - デフォルトはお問い合わせ
                 */
                naigai_fp_render_url_select(
                    '_fp_cta_btn1_url',
                    get_post_meta($post_id, '_fp_cta_btn1_url', true),
                    home_url('/contact/')
                );
                ?>
            </div>

            <div class="fp-admin-field">
                <label>ボタン2 文言</label>
                <input type="text" name="_fp_cta_btn2_label" value="<?php echo esc_attr(naigai_fp_get($post_id, '_fp_cta_btn2_label', '来店予約')); ?>">
            </div>

            <div class="fp-admin-field">
                <label>ボタン2 URL</label>
                <?php
                /*
                 * 最後のCTA ボタン2 URL
                 * - デフォルトは来店予約
                 */
                naigai_fp_render_url_select(
                    '_fp_cta_btn2_url',
                    get_post_meta($post_id, '_fp_cta_btn2_url', true),
                    home_url('/reservation/')
                );
                ?>
            </div>

            <div class="fp-admin-field">
                <label>CTA背景画像</label>
                <?php naigai_fp_media_field($post_id, '_fp_cta_image_id', 'CTA背景画像を選択', 'image', 'image'); ?>
            </div>
        </section>

    <?php
        submit_button('保存する');

        echo '</form>';
        echo '</div>';
    }
}

add_action('admin_menu', function () {
    add_menu_page(
        'フロントページ設定',
        'フロントページ設定',
        'edit_pages',
        'naigai-frontpage-settings',
        'naigai_fp_render_settings_page',
        'dashicons-admin-home',
        58
    );
});

add_action('admin_post_naigai_save_frontpage_settings', function () {
    $post_id = isset($_POST['frontpage_post_id']) ? absint($_POST['frontpage_post_id']) : 0;

    if (!$post_id || !current_user_can('edit_post', $post_id)) {
        wp_die('権限がありません。');
    }

    if (!isset($_POST['naigai_frontpage_nonce']) || !wp_verify_nonce($_POST['naigai_frontpage_nonce'], 'naigai_save_frontpage_settings')) {
        wp_die('nonce error');
    }

    naigai_fp_save_settings($post_id);

    wp_safe_redirect(admin_url('admin.php?page=naigai-frontpage-settings&updated=1'));
    exit;
});

add_action('admin_enqueue_scripts', function ($hook) {
    if ($hook !== 'toplevel_page_naigai-frontpage-settings') {
        return;
    }

    wp_enqueue_media();

    wp_add_inline_style('wp-admin', '
        .fp-admin-wrap { max-width: 1120px; }
        .fp-admin-card {
            background: #fff;
            border: 1px solid #dcdcde;
            border-radius: 10px;
            padding: 18px;
            margin: 18px 0;
        }
        .fp-admin-card h2 {
            margin-top: 0;
            font-size: 18px;
        }
        .fp-admin-section-title {
            margin-top: 28px;
        }
        .fp-admin-field {
            margin: 14px 0;
        }
        .fp-admin-field label {
            display: block;
            font-weight: 700;
            margin-bottom: 6px;
        }
        .fp-admin-field input[type="text"],
        .fp-admin-field input[type="url"],
        .fp-admin-field textarea,
        .fp-admin-field select {
            width: 100%;
            max-width: 760px;
        }
        .fp-admin-field textarea {
            min-height: 84px;
        }
        .fp-admin-media-row {
            display: none;
            padding: 14px;
            margin: 14px 0;
            border: 1px solid #dcdcde;
            border-radius: 8px;
            background: #f6f7f7;
        }
        .fp-admin-media-row.is-active {
            display: block;
        }
        .fp-admin-media-row h3 {
            margin: 0 0 8px;
        }
        .fp-admin-media {
            display: flex;
            gap: 12px;
            align-items: center;
            flex-wrap: wrap;
        }
        .fp-admin-media__preview {
            width: 96px;
            height: 68px;
            border: 1px solid #c3c4c7;
            background: #fff;
            display: grid;
            place-items: center;
            overflow: hidden;
        }
        .fp-admin-media__preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .fp-admin-media__preview span {
            color: #777;
            font-size: 12px;
        }
        .fp-admin-notice-item {
            border-top: 1px solid #dcdcde;
            padding-top: 16px;
            margin-top: 16px;
        }
    ');

    wp_add_inline_script('jquery-core', <<<JS
jQuery(function($){
  function updateMediaSlot(slot) {
    var type = slot.find('.js-fp-media-type').val() || 'image';
    slot.find('[data-fp-row]').removeClass('is-active');
    slot.find('[data-fp-row="' + type + '"]').addClass('is-active');
  }

  $('[data-fp-media-slot]').each(function(){
    updateMediaSlot($(this));
  });

  $(document).on('change', '.js-fp-media-type', function(){
    updateMediaSlot($(this).closest('[data-fp-media-slot]'));
  });

  $(document).on('click', '.fp-admin-select-media', function(e){
    e.preventDefault();

    var btn = $(this);
    var target = btn.data('target');
    var field = $('input[name="'+target+'"]');
    var preview = btn.closest('.fp-admin-media').find('.fp-admin-media__preview');
    var libraryType = btn.data('library') || 'image';
    var allowedMime = btn.data('mime') || 'image';

    var frame = wp.media({
      title: libraryType === 'video' ? 'MP4動画を選択' : '画像を選択',
      button: { text: '選択する' },
      multiple: false,
      library: { type: libraryType }
    });

    frame.on('select', function(){
      var attachment = frame.state().get('selection').first().toJSON();
      var mime = String(attachment.mime || '');

      if (allowedMime === 'video/mp4' && mime !== 'video/mp4') {
        alert('MP4動画欄では MP4（video/mp4）だけ選択できます。');
        return;
      }

      if (allowedMime === 'image' && !mime.match(/^image\\//)) {
        alert('画像欄では画像ファイルだけ選択できます。');
        return;
      }

      field.val(attachment.id);

      if (attachment.sizes && attachment.sizes.thumbnail) {
        preview.html('<img src="'+attachment.sizes.thumbnail.url+'" alt="">');
      } else if (attachment.icon) {
        preview.html('<img src="'+attachment.icon+'" alt="">');
      } else {
        preview.html('<span>選択済み</span>');
      }
    });

    frame.open();
  });

  $(document).on('click', '.fp-admin-clear-media', function(e){
    e.preventDefault();

    var btn = $(this);
    var target = btn.data('target');

    $('input[name="'+target+'"]').val('');
    btn.closest('.fp-admin-media').find('.fp-admin-media__preview').html('<span>未選択</span>');
  });
});
JS);
});


/* =========================================================
 * FP_FORCE_MEDIA_UI_V2
 * 管理画面のメディアUIを強制的に整理
 * - 2カラム
 * - 選択タイプの欄だけ表示
 * - MP4は video/mp4 のみ許可
 * ========================================================= */

add_action('admin_enqueue_scripts', function ($hook) {
    if ($hook !== 'toplevel_page_naigai-frontpage-settings') {
        return;
    }

    wp_enqueue_media();
    wp_enqueue_script('jquery');

    wp_add_inline_style('wp-admin', '
        .fp-admin-wrap {
            max-width: 1180px;
        }

        .fp-admin-media-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
            align-items: start;
            margin: 16px 0 28px;
        }

        .fp-admin-media-slot {
            margin: 0 !important;
        }

        .fp-admin-media-slot h2 {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding-bottom: 10px;
            border-bottom: 1px solid #dcdcde;
        }

        .fp-admin-media-slot .fp-admin-field:first-of-type {
            padding: 12px;
            background: #eef6fb;
            border: 1px solid #c7ddeb;
            border-radius: 8px;
        }

        .fp-admin-media-slot .js-fp-media-type {
            max-width: 100%;
            font-weight: 700;
        }

        .fp-admin-media-row {
            display: none !important;
            padding: 14px;
            margin: 14px 0;
            border: 1px solid #dcdcde;
            border-radius: 8px;
            background: #f6f7f7;
        }

        .fp-admin-media-row.is-active {
            display: block !important;
        }

        .fp-admin-media-row h3 {
            margin: 0 0 8px;
            font-size: 15px;
        }

        .fp-admin-media {
            display: flex;
            gap: 12px;
            align-items: center;
            flex-wrap: wrap;
        }

        .fp-admin-media__preview,
        .fp-admin-media-preview {
            width: 110px;
            height: 76px;
            border: 1px solid #c3c4c7;
            background: #fff;
            display: grid;
            place-items: center;
            overflow: hidden;
        }

        .fp-admin-media__preview img,
        .fp-admin-media-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .fp-admin-media__preview span,
        .fp-admin-media-preview span {
            color: #777;
            font-size: 12px;
        }

        .fp-admin-select-media[data-library="video"] {
            background: #135e96;
            border-color: #135e96;
            color: #fff;
        }

        .fp-admin-select-media[data-library="image"] {
            background: #f6f7f7;
        }

        .fp-admin-card.fp-admin-media-help {
            margin-bottom: 12px;
        }

        @media (max-width: 900px) {
            .fp-admin-media-grid {
                grid-template-columns: 1fr;
            }
        }
    ');
});

add_action('admin_footer', function () {
    $screen = function_exists('get_current_screen') ? get_current_screen() : null;
    if (!$screen || $screen->id !== 'toplevel_page_naigai-frontpage-settings') {
        return;
    }
    ?>
    <script>
        jQuery(function($) {
            function updateMediaSlot(slot) {
                var type = slot.find('.js-fp-media-type').val() || 'image';
                slot.find('[data-fp-row]').removeClass('is-active');
                slot.find('[data-fp-row="' + type + '"]').addClass('is-active');
            }

            $('[data-fp-media-slot]').each(function() {
                updateMediaSlot($(this));
            });

            $(document)
                .off('change.fpMediaType', '.js-fp-media-type')
                .on('change.fpMediaType', '.js-fp-media-type', function() {
                    updateMediaSlot($(this).closest('[data-fp-media-slot]'));
                });

            $(document).off('click', '.fp-admin-select-media');
            $(document).on('click', '.fp-admin-select-media', function(e) {
                e.preventDefault();

                var btn = $(this);
                var target = btn.data('target');
                var field = $('input[name="' + target + '"]');
                var preview = btn.closest('.fp-admin-media').find('.fp-admin-media__preview, .fp-admin-media-preview');

                var libraryType = btn.data('library') || 'image';
                var allowedMime = btn.data('mime') || 'image';

                var frame = wp.media({
                    title: libraryType === 'video' ? 'MP4動画を選択' : '画像を選択',
                    button: {
                        text: '選択する'
                    },
                    multiple: false,
                    library: {
                        type: libraryType
                    }
                });

                frame.on('select', function() {
                    var attachment = frame.state().get('selection').first().toJSON();
                    var mime = String(attachment.mime || '');

                    if (allowedMime === 'video/mp4' && mime !== 'video/mp4') {
                        alert('MP4動画欄では MP4（video/mp4）だけ選択できます。');
                        return;
                    }

                    if (allowedMime === 'image' && !mime.match(/^image\//)) {
                        alert('画像欄では画像ファイルだけ選択できます。');
                        return;
                    }

                    field.val(attachment.id);

                    if (libraryType === 'video') {
                        preview.html('<span>MP4選択済み<br>ID: ' + attachment.id + '</span>');
                    } else if (attachment.sizes && attachment.sizes.thumbnail) {
                        preview.html('<img src="' + attachment.sizes.thumbnail.url + '" alt="">');
                    } else if (attachment.icon) {
                        preview.html('<img src="' + attachment.icon + '" alt="">');
                    } else {
                        preview.html('<span>選択済み<br>ID: ' + attachment.id + '</span>');
                    }
                });

                frame.open();
            });

            $(document).off('click', '.fp-admin-clear-media');
            $(document).on('click', '.fp-admin-clear-media', function(e) {
                e.preventDefault();

                var btn = $(this);
                var target = btn.data('target');

                $('input[name="' + target + '"]').val('');
                btn.closest('.fp-admin-media').find('.fp-admin-media__preview, .fp-admin-media-preview').html('<span>未選択</span>');
            });
        });
    </script>
<?php
});


/* =========================================================
 * FP_MOBILE_CTA_RULE_NOTICE
 * 管理画面にスマホCTA表示ルールを明記
 * ========================================================= */
add_action('admin_footer', function () {
    $screen = function_exists('get_current_screen') ? get_current_screen() : null;

    if (!$screen || $screen->id !== 'toplevel_page_naigai-frontpage-settings') {
        return;
    }
?>
    <script>
        jQuery(function($) {
            if ($('.fp-mobile-cta-rule-notice').length) {
                return;
            }

            var notice = $(
                '<div class="fp-admin-card fp-mobile-cta-rule-notice">' +
                '<h2>スマホ表示のCTAルール</h2>' +
                '<p class="description">スマホのヒーロー上部では、ボタンが多すぎると見づらいため、表示は2つだけに制限しています。</p>' +
                '<ul style="margin-left:1.2em;list-style:disc;">' +
                '<li><strong>表示:</strong> 不動産を見る / お問い合わせ</li>' +
                '<li><strong>非表示:</strong> 家づくりを見る / 宿泊を探す</li>' +
                '<li>家づくり・宿泊は下のサービスカードから案内します。</li>' +
                '<li>スマホでは、CTAは画像・動画Swiperの下に表示します。</li>' +
                '</ul>' +
                '</div>'
            );

            $('.fp-admin-wrap h1').first().after(notice);
        });
    </script>
<?php
});
