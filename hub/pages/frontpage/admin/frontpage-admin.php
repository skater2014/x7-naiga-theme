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
    function naigai_fp_get($post_id, $key, $default = '') {
        $v = get_post_meta($post_id, $key, true);
        return ($v !== '' && $v !== null) ? $v : $default;
    }
}

if (!function_exists('naigai_fp_media_field')) {
    function naigai_fp_media_field($post_id, $key, $label, $library_type = 'image', $allowed_mime = 'image') {
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
                data-mime="<?php echo esc_attr($allowed_mime); ?>"
            >
                <?php echo esc_html($label); ?>
            </button>

            <button
                type="button"
                class="button fp-admin-clear-media"
                data-target="<?php echo esc_attr($key); ?>"
            >
                クリア
            </button>
        </div>
        <?php
    }
}

if (!function_exists('naigai_fp_normalize_media_values')) {
    function naigai_fp_normalize_media_values($post_id) {
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
    function naigai_fp_save_settings($post_id) {
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

        $text_keys[] = '_fp_cta_title';
        $text_keys[] = '_fp_cta_btn1_label';
        $text_keys[] = '_fp_cta_btn1_url';
        $text_keys[] = '_fp_cta_btn2_label';
        $text_keys[] = '_fp_cta_btn2_url';

        $id_keys[] = '_fp_life_image_id';
        $id_keys[] = '_fp_cta_image_id';

        for ($i = 1; $i <= 3; $i++) {
            $text_keys[] = "_fp_life_point_{$i}_title";
            $text_keys[] = "_fp_life_point_{$i}_text";
            $id_keys[]   = "_fp_life_point_{$i}_image_id";
        }

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
    function naigai_fp_render_media_slot($post_id, $i) {
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
                    placeholder="例: dQw4w9WgXcQ または YouTube URL"
                >
            </div>

            <div class="fp-admin-media-row" data-fp-row="vimeo">
                <h3>Vimeo</h3>
                <p class="description">Vimeo表示の時だけ使います。ID または URL を入力。</p>
                <input
                    type="text"
                    name="_fp_hero_media_<?php echo (int) $i; ?>_vimeo"
                    value="<?php echo esc_attr(naigai_fp_get($post_id, "_fp_hero_media_{$i}_vimeo", '')); ?>"
                    placeholder="例: 123456789 または Vimeo URL"
                >
            </div>

            <div class="fp-admin-field">
                <label>表示ラベル</label>
                <input
                    type="text"
                    name="_fp_hero_media_<?php echo (int) $i; ?>_label"
                    value="<?php echo esc_attr(naigai_fp_get($post_id, "_fp_hero_media_{$i}_label", '')); ?>"
                    placeholder="未入力なら種類名を表示"
                >
            </div>
        </section>
        <?php
    }
}

if (!function_exists('naigai_fp_render_settings_page')) {
    function naigai_fp_render_settings_page() {
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
                <textarea name="_fp_hero_title"><?php echo esc_textarea(naigai_fp_get($post_id, '_fp_hero_title', "那須で暮らす・建てる・泊まる。\n住まいも、事業も。\nまるごと相談できる総合窓口")); ?></textarea>
            </div>

            <div class="fp-admin-field">
                <label>リード文</label>
                <textarea name="_fp_hero_lead"><?php echo esc_textarea(naigai_fp_get($post_id, '_fp_hero_lead', "不動産探しから家づくり、民泊のご相談まで。\n那須の暮らしをもっと豊かに、もっと自由に。")); ?></textarea>
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
            <h2>下部CTA設定</h2>
            <p class="description">画像を入れた場合だけ、フロントページ下部CTAに画像を表示します。未設定なら今まで通りテキストCTAだけで表示します。</p>

            <div class="fp-admin-field">
                <label>CTA画像</label>
                <?php naigai_fp_media_field($post_id, "_fp_cta_image_id", 'CTA背景画像を選択', 'image', 'image'); ?>
            </div>
        </section>

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
                        <input type="url" name="_fp_notice_<?php echo (int) $i; ?>_url" value="<?php echo esc_attr(naigai_fp_get($post_id, "_fp_notice_{$i}_url", '')); ?>">
                    </div>
                </div>
            <?php endfor; ?>
        </section>
        <?php
        ?>
        ?>
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
                            <input type="url" name="_fp_hero_btn_<?php echo (int) $i; ?>_url" value="<?php echo esc_attr($btn_url); ?>">
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
                            <input type="url" name="_fp_service_<?php echo esc_attr($service_key); ?>_url" value="<?php echo esc_attr(naigai_fp_get($post_id, "_fp_service_{$service_key}_url", $defaults[2])); ?>">
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
                <input type="url" name="_fp_business_button_url" value="<?php echo esc_attr(naigai_fp_get($post_id, '_fp_business_button_url', home_url('/contact/'))); ?>">
            </div>
        </section>

        <section class="fp-admin-card">
            <h2>那須での暮らし</h2>

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
                <label>ボタン文言</label>
                <input type="text" name="_fp_life_button_label" value="<?php echo esc_attr(naigai_fp_get($post_id, '_fp_life_button_label', '那須の魅力をもっと見る')); ?>">
            </div>

            <div class="fp-admin-field">
                <label>ボタンURL</label>
                <input type="url" name="_fp_life_button_url" value="<?php echo esc_attr(naigai_fp_get($post_id, '_fp_life_button_url', home_url('/'))); ?>">
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
                $fp_life_point_defaults = array(
                    1 => array('自然と共に暮らす', '美しい自然を身近に感じられる環境。'),
                    2 => array('アクセスも快適', '週末利用や拠点づくりにも対応。'),
                    3 => array('家族にやさしい環境', '安心して暮らせる地域環境。'),
                );

                for ($i = 1; $i <= 3; $i++) :
                    ?>
                    <section class="fp-admin-card fp-admin-media-slot">
                        <h2>カード<?php echo (int) $i; ?></h2>

                        <div class="fp-admin-field">
                            <label>タイトル</label>
                            <input type="text" name="_fp_life_point_<?php echo (int) $i; ?>_title" value="<?php echo esc_attr(naigai_fp_get($post_id, "_fp_life_point_{$i}_title", $fp_life_point_defaults[$i][0])); ?>">
                        </div>

                        <div class="fp-admin-field">
                            <label>本文</label>
                            <textarea name="_fp_life_point_<?php echo (int) $i; ?>_text"><?php echo esc_textarea(naigai_fp_get($post_id, "_fp_life_point_{$i}_text", $fp_life_point_defaults[$i][1])); ?></textarea>
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
                <textarea name="_fp_cta_title"><?php echo esc_textarea(naigai_fp_get($post_id, '_fp_cta_title', "暮らし・不動産・家づくり・宿泊・事業まで\nお気軽にご相談ください")); ?></textarea>
            </div>

            <div class="fp-admin-field">
                <label>ボタン1 文言</label>
                <input type="text" name="_fp_cta_btn1_label" value="<?php echo esc_attr(naigai_fp_get($post_id, '_fp_cta_btn1_label', 'お問い合わせ')); ?>">
            </div>

            <div class="fp-admin-field">
                <label>ボタン1 URL</label>
                <input type="url" name="_fp_cta_btn1_url" value="<?php echo esc_attr(naigai_fp_get($post_id, '_fp_cta_btn1_url', home_url('/contact/'))); ?>">
            </div>

            <div class="fp-admin-field">
                <label>ボタン2 文言</label>
                <input type="text" name="_fp_cta_btn2_label" value="<?php echo esc_attr(naigai_fp_get($post_id, '_fp_cta_btn2_label', '来店予約')); ?>">
            </div>

            <div class="fp-admin-field">
                <label>ボタン2 URL</label>
                <input type="url" name="_fp_cta_btn2_url" value="<?php echo esc_attr(naigai_fp_get($post_id, '_fp_cta_btn2_url', home_url('/reservation/'))); ?>">
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
    jQuery(function($){
      function updateMediaSlot(slot) {
        var type = slot.find('.js-fp-media-type').val() || 'image';
        slot.find('[data-fp-row]').removeClass('is-active');
        slot.find('[data-fp-row="' + type + '"]').addClass('is-active');
      }

      $('[data-fp-media-slot]').each(function(){
        updateMediaSlot($(this));
      });

      $(document)
        .off('change.fpMediaType', '.js-fp-media-type')
        .on('change.fpMediaType', '.js-fp-media-type', function(){
          updateMediaSlot($(this).closest('[data-fp-media-slot]'));
        });

      $(document).off('click', '.fp-admin-select-media');
      $(document).on('click', '.fp-admin-select-media', function(e){
        e.preventDefault();

        var btn = $(this);
        var target = btn.data('target');
        var field = $('input[name="'+target+'"]');
        var preview = btn.closest('.fp-admin-media').find('.fp-admin-media__preview, .fp-admin-media-preview');

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

          if (allowedMime === 'image' && !mime.match(/^image\//)) {
            alert('画像欄では画像ファイルだけ選択できます。');
            return;
          }

          field.val(attachment.id);

          if (libraryType === 'video') {
            preview.html('<span>MP4選択済み<br>ID: '+ attachment.id +'</span>');
          } else if (attachment.sizes && attachment.sizes.thumbnail) {
            preview.html('<img src="'+ attachment.sizes.thumbnail.url +'" alt="">');
          } else if (attachment.icon) {
            preview.html('<img src="'+ attachment.icon +'" alt="">');
          } else {
            preview.html('<span>選択済み<br>ID: '+ attachment.id +'</span>');
          }
        });

        frame.open();
      });

      $(document).off('click', '.fp-admin-clear-media');
      $(document).on('click', '.fp-admin-clear-media', function(e){
        e.preventDefault();

        var btn = $(this);
        var target = btn.data('target');

        $('input[name="'+target+'"]').val('');
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
    jQuery(function($){
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

