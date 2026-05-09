<?php
if (!defined('ABSPATH')) {
    exit;
}

/* =========================================================
 * 注文住宅ページの対象判定
 * 役割:
 * - 注文住宅ページだけにメタボックスを出す
 * - 民泊B2Cや他の固定ページには出さない
 * ========================================================= */
if (!function_exists('naigai_is_customhome_target_page')) {
    function naigai_is_customhome_target_page($post_id = 0)
    {
        $post_id = (int) $post_id;
        if ($post_id <= 0) {
            return false;
        }

        $post = get_post($post_id);
        if (!$post || $post->post_type !== 'page') {
            return false;
        }

        $template = (string) get_page_template_slug($post_id);
        $slug     = (string) $post->post_name;

        if ($template === 'template-construction-hub.php') {
            return true;
        }

        if (in_array($slug, array('construction-hub', 'iezukuri'), true)) {
            return true;
        }

        return false;
    }
}

/* =========================================================
 * 注文住宅ページ専用メニュー location
 * ========================================================= */
if (!function_exists('naigai_customhome_register_menu_location')) {
    function naigai_customhome_register_menu_location()
    {
        register_nav_menu('customhome-page-menu', 'Custom Home Page Menu');
    }
    add_action('after_setup_theme', 'naigai_customhome_register_menu_location');
}

/* =========================================================
 * 注文住宅ページ専用メニューをコードで自動作成
 * ========================================================= */
if (!function_exists('naigai_customhome_seed_menu')) {
    function naigai_customhome_seed_menu()
    {
        $location  = 'customhome-page-menu';
        $menu_name = '注文住宅ページメニュー';

        $menu = wp_get_nav_menu_object($menu_name);
        if (!$menu) {
            $menu_id = wp_create_nav_menu($menu_name);
            $menu    = wp_get_nav_menu_object($menu_id);
        }

        if (!$menu || empty($menu->term_id)) {
            return;
        }

        $menu_id = (int) $menu->term_id;
        $items   = wp_get_nav_menu_items($menu_id);

        if (empty($items)) {
            $defaults = array(
                array('title' => '家づくりの特徴', 'url' => '#customhome-feature'),
                array('title' => '施工事例',       'url' => '#customhome-works'),
                array('title' => '家づくりの流れ', 'url' => '#customhome-flow'),
                array('title' => '会社案内',       'url' => '#customhome-company'),
                array('title' => 'お問い合わせ',   'url' => '#customhome-contact'),
            );

            foreach ($defaults as $index => $item) {
                wp_update_nav_menu_item($menu_id, 0, array(
                    'menu-item-title'    => $item['title'],
                    'menu-item-url'      => $item['url'],
                    'menu-item-status'   => 'publish',
                    'menu-item-type'     => 'custom',
                    'menu-item-object'   => 'custom',
                    'menu-item-position' => $index + 1,
                ));
            }
        }

        $locations = get_theme_mod('nav_menu_locations', array());
        if (!is_array($locations)) {
            $locations = array();
        }

        if (empty($locations[$location]) || (int) $locations[$location] !== $menu_id) {
            $locations[$location] = $menu_id;
            set_theme_mod('nav_menu_locations', $locations);
        }
    }
    add_action('init', 'naigai_customhome_seed_menu', 30);
}

/* =========================================================
 * デフォルト文言
 * ========================================================= */
if (!function_exists('naigai_customhome_defaults')) {
    function naigai_customhome_defaults()
    {
        return array(
            'hero_kicker' => 'CUSTOM HOME',
            'hero_title'  => "理想を、かたちに。\n世界にひとつの、注文住宅。",
            'hero_lead'   => '家族の想いに寄り添い、暮らしをデザインする。性能・デザイン・素材、そのすべてにこだわった、あなただけの住まいをつくります。',
            'hero_btn1_label' => '無料相談・資料請求',
            'hero_btn1_url'   => home_url('/contact/'),
            'hero_btn2_label' => 'お問い合わせ',
            'hero_btn2_url'   => home_url('/contact/'),

            'feature_eyebrow' => 'FEATURE',
            'feature_title'   => '私たちの家づくりの特徴',

            'feature_1_title' => '自由設計のデザイン力',
            'feature_1_text'  => '一般建築士とつくる自由設計。デザイン性と機能性を両立した唯一無二の住まいを形にします。',
            'feature_2_title' => '高い住宅性能',
            'feature_2_text'  => '断熱・気密・耐震・耐久の視点から、長く快適に暮らせる性能バランスを丁寧に整えます。',
            'feature_3_title' => '自然素材へのこだわり',
            'feature_3_text'  => '無垢材や塗り壁など、素材が持つ風合いと心地よさを、暮らしの質に結びつけます。',
            'feature_4_title' => '暮らしに寄り添う提案',
            'feature_4_text'  => '家族構成、生活動線、将来設計まで見据えて、住まい方そのものを一緒に考えます。',

            'works_eyebrow' => 'WORKS',
            'works_title'   => '施工事例',
            'work_1_title'  => '自然と調和する平屋の住まい',
            'work_1_text'   => '那須の景観と馴染む、伸びやかな平屋プラン。',
            'work_2_title'  => '吹き抜けが心地よい家',
            'work_2_text'   => '光と風が巡る、開放感のあるLDK中心の住まい。',
            'work_3_title'  => '中庭のあるコの字型の家',
            'work_3_text'   => '外からの視線を抑えつつ、内側に豊かな余白をつくる設計。',
            'work_4_title'  => '木の温もりを感じるモダンな家',
            'work_4_text'   => '素材の表情を活かし、上質で落ち着いた日常をつくる住まい。',

            'flow_eyebrow' => 'FLOW',
            'flow_title'   => '家づくりの流れ',
            'flow_1_num'   => '01',
            'flow_1_title' => 'ご相談・ヒアリング',
            'flow_1_text'  => '理想の暮らしやご要望を、まずはじっくり伺います。',
            'flow_2_num'   => '02',
            'flow_2_title' => 'プラン提案・お見積り',
            'flow_2_text'  => '敷地やご予算に合わせて、最適な住まい方をご提案します。',
            'flow_3_num'   => '03',
            'flow_3_title' => 'ご契約',
            'flow_3_text'  => '内容にご納得いただいたうえで、丁寧に契約を進めます。',
            'flow_4_num'   => '04',
            'flow_4_title' => '詳細設計・仕様決定',
            'flow_4_text'  => '間取りや内装、設備など、細部まで一緒に整えていきます。',
            'flow_5_num'   => '05',
            'flow_5_title' => '着工・施工',
            'flow_5_text'  => '確かな品質管理のもと、丁寧に住まいをかたちにします。',
            'flow_6_num'   => '06',
            'flow_6_title' => 'お引渡し・アフターサポート',
            'flow_6_text'  => '完成後も安心して暮らしていただけるよう継続して支えます。',

            'cta_eyebrow'    => 'CONTACT',
            'cta_title'      => '理想の住まいづくりを、一緒に始めませんか？',
            'cta_text'       => '土地探しから資金計画まで、家づくりのことなら何でもご相談ください。構想段階でも大丈夫です。',
            'cta_btn1_label' => '無料相談・資料請求',
            'cta_btn1_url'   => home_url('/contact/'),
            'cta_btn2_label' => 'お問い合わせ',
            'cta_btn2_url'   => home_url('/contact/'),
        );
    }
}

/* =========================================================
 * テキスト行
 * ========================================================= */
if (!function_exists('naigai_customhome_render_text_row')) {
    function naigai_customhome_render_text_row($post, $meta_key, $label, $default = '', $type = 'text', $rows = 4)
    {
        $value = get_post_meta($post->ID, $meta_key, true);
        if ($value === '') {
            $value = $default;
        }
        ?>
        <tr>
            <th scope="row">
                <label for="<?php echo esc_attr($meta_key); ?>"><?php echo esc_html($label); ?></label>
            </th>
            <td>
                <?php if ($type === 'textarea') : ?>
                    <textarea
                        id="<?php echo esc_attr($meta_key); ?>"
                        name="<?php echo esc_attr($meta_key); ?>"
                        rows="<?php echo (int) $rows; ?>"
                        class="large-text"
                    ><?php echo esc_textarea($value); ?></textarea>
                <?php else : ?>
                    <input
                        type="text"
                        id="<?php echo esc_attr($meta_key); ?>"
                        name="<?php echo esc_attr($meta_key); ?>"
                        value="<?php echo esc_attr($value); ?>"
                        class="regular-text"
                    >
                <?php endif; ?>
            </td>
        </tr>
        <?php
    }
}

/* =========================================================
 * 画像 / 動画 プレビューHTML
 * ========================================================= */
if (!function_exists('naigai_customhome_get_media_preview_html')) {
    function naigai_customhome_get_media_preview_html($attachment_id)
    {
        $attachment_id = (int) $attachment_id;

        if ($attachment_id <= 0) {
            return '<div class="naigai-ch-media-empty">未選択</div>';
        }

        $mime = (string) get_post_mime_type($attachment_id);
        $url  = (string) wp_get_attachment_url($attachment_id);

        if ($url === '') {
            return '<div class="naigai-ch-media-empty">未選択</div>';
        }

        if (strpos($mime, 'image/') === 0) {
            return '<img src="' . esc_url($url) . '" alt="" class="naigai-ch-media-preview-image">';
        }

        if (strpos($mime, 'video/') === 0) {
            return '<video class="naigai-ch-media-preview-video" controls muted playsinline preload="metadata"><source src="' . esc_url($url) . '" type="' . esc_attr($mime) . '"></video>';
        }

        return '<div class="naigai-ch-media-empty">プレビュー非対応</div>';
    }
}

/* =========================================================
 * メディア行
 * 役割:
 * - URL文字列ではなく画像 / 動画のプレビューを出す
 * - ボタン位置ずれを止める
 * ========================================================= */
if (!function_exists('naigai_customhome_render_media_row')) {
    function naigai_customhome_render_media_row($post, $meta_key, $label, $type = 'image')
    {
        $attachment_id = absint(get_post_meta($post->ID, $meta_key, true));
        $url = $attachment_id ? wp_get_attachment_url($attachment_id) : '';
        $preview_html = naigai_customhome_get_media_preview_html($attachment_id);
        ?>
        <tr>
            <th scope="row">
                <label for="<?php echo esc_attr($meta_key); ?>"><?php echo esc_html($label); ?></label>
            </th>
            <td>
                <div class="naigai-ch-media-field">
                    <div class="naigai-ch-media-preview" id="<?php echo esc_attr($meta_key); ?>_preview">
                        <?php echo $preview_html; ?>
                    </div>

                    <div class="naigai-ch-media-controls">
                        <input
                            type="hidden"
                            name="<?php echo esc_attr($meta_key); ?>"
                            id="<?php echo esc_attr($meta_key); ?>"
                            value="<?php echo esc_attr($attachment_id); ?>"
                        >

                        <div class="naigai-ch-media-buttons">
                            <button
                                type="button"
                                class="button button-secondary naigai-ch-media-open"
                                data-target="<?php echo esc_attr($meta_key); ?>"
                                data-preview="<?php echo esc_attr($meta_key); ?>_preview"
                                data-url="<?php echo esc_attr($meta_key); ?>_url"
                                data-type="<?php echo esc_attr($type); ?>"
                            >
                                メディアから選択
                            </button>

                            <button
                                type="button"
                                class="button naigai-ch-media-clear"
                                data-target="<?php echo esc_attr($meta_key); ?>"
                                data-preview="<?php echo esc_attr($meta_key); ?>_preview"
                                data-url="<?php echo esc_attr($meta_key); ?>_url"
                            >
                                クリア
                            </button>
                        </div>

                        <p class="description">画像はサムネイル、動画は再生プレビューで確認できます。</p>

                        <div
                            class="naigai-ch-media-url"
                            id="<?php echo esc_attr($meta_key); ?>_url"
                        ><?php echo $url ? esc_html($url) : '未選択'; ?></div>
                    </div>
                </div>
            </td>
        </tr>
        <?php
    }
}

/* =========================================================
 * 管理画面UI
 * 役割:
 * - メタボックスを見やすいカード構成にする
 * - 位置ずれを止める
 * ========================================================= */
if (!function_exists('naigai_customhome_admin_ui_assets')) {
    function naigai_customhome_admin_ui_assets()
    {
        static $printed = false;
        if ($printed) {
            return;
        }
        $printed = true;
        ?>
        <style>
        .naigai-ch-admin {
            display: grid;
            gap: 20px;
            margin-top: 16px;
        }

        .naigai-ch-admin-topnav {
            position: sticky;
            top: 32px;
            z-index: 20;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            padding: 16px;
            border: 1px solid #dcdcde;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.96);
        }

        .naigai-ch-admin-topnav a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 42px;
            padding: 0 18px;
            border-radius: 999px;
            background: #f6f7f7;
            border: 1px solid #d0d4d9;
            color: #1d2327;
            text-decoration: none;
            font-size: 14px;
            font-weight: 700;
        }

        .naigai-ch-admin-topnav a:hover {
            background: #eef4ff;
            border-color: #b7c8ff;
        }

        .naigai-ch-admin-topnav a.is-active {
            background: #1d2327;
            border-color: #1d2327;
            color: #ffffff;
        }

        .naigai-ch-admin-section {
            padding: 20px;
            border: 1px solid #dcdcde;
            border-radius: 18px;
            background: #ffffff;
            scroll-margin-top: 96px;
        }

        .naigai-ch-admin-section__title {
            margin: 0 0 16px;
            font-size: 19px;
            line-height: 1.4;
        }

        .naigai-ch-admin-split {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
        }

        .naigai-ch-admin-cards {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
        }

        .naigai-ch-admin-card {
            padding: 16px;
            border: 1px solid #eceef0;
            border-radius: 14px;
            background: #fbfbfc;
            min-width: 0;
        }

        .naigai-ch-admin-card--full {
            margin-bottom: 16px;
        }

        .naigai-ch-admin-card__title {
            margin: 0 0 12px;
            font-size: 14px;
            line-height: 1.45;
        }

        .naigai-ch-admin .form-table {
            margin-top: 0;
        }

        .naigai-ch-admin .form-table th {
            width: 160px;
            padding: 10px 12px 10px 0;
            vertical-align: top;
        }

        .naigai-ch-admin .form-table td {
            padding: 8px 0 12px;
        }

        .naigai-ch-admin .form-table textarea,
        .naigai-ch-admin .form-table input[type="text"] {
            width: 100%;
            max-width: 100%;
        }

        .naigai-ch-admin h3 {
            margin: 0 0 12px;
            padding: 0;
            border: 0;
            font-size: 14px;
        }

        .naigai-ch-media-field {
            display: grid;
            grid-template-columns: 220px 1fr;
            gap: 16px;
            align-items: start;
        }

        .naigai-ch-media-preview {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 132px;
            border: 1px solid #dcdcde;
            border-radius: 12px;
            background: #f6f7f7;
            overflow: hidden;
        }

        .naigai-ch-media-preview-image,
        .naigai-ch-media-preview-video {
            display: block;
            width: 100%;
            height: 132px;
            object-fit: cover;
            background: #000;
        }

        .naigai-ch-media-empty {
            color: #646970;
            font-size: 13px;
        }

        .naigai-ch-media-controls {
            display: grid;
            gap: 10px;
            min-width: 0;
        }

        .naigai-ch-media-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .naigai-ch-media-url {
            padding: 10px 12px;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            background: #fafafa;
            color: #50575e;
            font-size: 12px;
            line-height: 1.6;
            word-break: break-all;
        }

        @media (max-width: 1200px) {
            .naigai-ch-admin-cards,
            .naigai-ch-admin-split {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 960px) {
            .naigai-ch-admin-topnav {
                top: 0;
            }

            .naigai-ch-admin .form-table th,
            .naigai-ch-admin .form-table td {
                display: block;
                width: auto;
                padding-right: 0;
            }

            .naigai-ch-media-field {
                grid-template-columns: 1fr;
            }

            .naigai-ch-media-preview-image,
            .naigai-ch-media-preview-video {
                height: 180px;
            }
        }
        </style>

        <script>
        (function () {
            function buildPreviewHtml(attachment) {
                if (!attachment || !attachment.url) {
                    return '<div class="naigai-ch-media-empty">未選択</div>';
                }

                if (attachment.type === 'image' || (attachment.mime && attachment.mime.indexOf('image/') === 0)) {
                    return '<img src="' + attachment.url + '" alt="" class="naigai-ch-media-preview-image">';
                }

                if (attachment.type === 'video' || (attachment.mime && attachment.mime.indexOf('video/') === 0)) {
                    return '<video class="naigai-ch-media-preview-video" controls muted playsinline preload="metadata"><source src="' + attachment.url + '" type="' + (attachment.mime || 'video/mp4') + '"></video>';
                }

                return '<div class="naigai-ch-media-empty">プレビュー非対応</div>';
            }

            function setActiveNav(target) {
                var links = document.querySelectorAll('.naigai-ch-admin-topnav a[data-nav-target]');
                links.forEach(function (link) {
                    link.classList.toggle('is-active', link.getAttribute('data-nav-target') === target);
                });
            }

            document.addEventListener('click', function (event) {
                if (event.target.classList.contains('naigai-ch-media-open')) {
                    event.preventDefault();

                    var targetId  = event.target.getAttribute('data-target');
                    var previewId = event.target.getAttribute('data-preview');
                    var urlId     = event.target.getAttribute('data-url');
                    var type      = event.target.getAttribute('data-type') || 'image';

                    var target  = document.getElementById(targetId);
                    var preview = document.getElementById(previewId);
                    var urlBox  = document.getElementById(urlId);

                    if (!target || !preview || !urlBox) {
                        return;
                    }

                    var frame = wp.media({
                        title: 'メディアを選択',
                        button: { text: 'このファイルを使う' },
                        library: { type: type },
                        multiple: false
                    });

                    frame.on('select', function () {
                        var attachment = frame.state().get('selection').first().toJSON();
                        target.value = attachment.id || '';
                        preview.innerHTML = buildPreviewHtml(attachment);
                        urlBox.textContent = attachment.url || '未選択';
                    });

                    frame.open();
                }

                if (event.target.classList.contains('naigai-ch-media-clear')) {
                    event.preventDefault();

                    var clearTargetId  = event.target.getAttribute('data-target');
                    var clearPreviewId = event.target.getAttribute('data-preview');
                    var clearUrlId     = event.target.getAttribute('data-url');

                    var clearTarget  = document.getElementById(clearTargetId);
                    var clearPreview = document.getElementById(clearPreviewId);
                    var clearUrlBox  = document.getElementById(clearUrlId);

                    if (clearTarget) {
                        clearTarget.value = '';
                    }

                    if (clearPreview) {
                        clearPreview.innerHTML = '<div class="naigai-ch-media-empty">未選択</div>';
                    }

                    if (clearUrlBox) {
                        clearUrlBox.textContent = '未選択';
                    }
                }

                var navLink = event.target.closest('.naigai-ch-admin-topnav a[data-nav-target]');
                if (navLink) {
                    setActiveNav(navLink.getAttribute('data-nav-target'));
                }
            });

            document.addEventListener('DOMContentLoaded', function () {
                var first = document.querySelector('.naigai-ch-admin-topnav a[data-nav-target]');
                if (first) {
                    setActiveNav(first.getAttribute('data-nav-target'));
                }

                if ('IntersectionObserver' in window) {
                    var sections = document.querySelectorAll('.naigai-ch-admin-section[data-nav-section]');
                    var observer = new IntersectionObserver(function (entries) {
                        entries.forEach(function (entry) {
                            if (entry.isIntersecting) {
                                setActiveNav(entry.target.getAttribute('data-nav-section'));
                            }
                        });
                    }, {
                        root: null,
                        rootMargin: '-20% 0px -65% 0px',
                        threshold: 0.1
                    });

                    sections.forEach(function (section) {
                        observer.observe(section);
                    });
                }
            });
        }());
        </script>
        <?php
    }
}

/* =========================================================
 * メタボックス描画
 * ========================================================= */
if (!function_exists('naigai_customhome_meta_box')) {
    function naigai_customhome_meta_box($post)
    {
        if (!naigai_is_customhome_target_page($post->ID)) {
            echo '<p>このメタボックスは注文住宅ページ専用です。</p>';
            return;
        }

        $d = naigai_customhome_defaults();

        wp_nonce_field('naigai_customhome_meta_box_action', 'naigai_customhome_meta_box_nonce');
        naigai_customhome_admin_ui_assets();
        ?>
        <div class="naigai-ch-admin">

            <nav class="naigai-ch-admin-topnav" aria-label="注文住宅ページ設定ナビ">
                <a href="#naigai-ch-admin-hero" data-nav-target="hero" class="is-active">Hero</a>
                <a href="#naigai-ch-admin-feature" data-nav-target="feature">Feature</a>
                <a href="#naigai-ch-admin-works" data-nav-target="works">Works</a>
                <a href="#naigai-ch-admin-flow" data-nav-target="flow">Flow</a>
                <a href="#naigai-ch-admin-cta" data-nav-target="cta">CTA</a>
            </nav>

            <section id="naigai-ch-admin-hero" data-nav-section="hero" class="naigai-ch-admin-section">
                <h2 class="naigai-ch-admin-section__title">Hero</h2>

                <div class="naigai-ch-admin-split">
                    <div class="naigai-ch-admin-card">
                        <h3 class="naigai-ch-admin-card__title">テキスト設定</h3>
                        <table class="form-table" role="presentation">
                            <tbody>
                                <?php naigai_customhome_render_text_row($post, '_hub_ch_hero_kicker', 'Hero ラベル', $d['hero_kicker']); ?>
                                <?php naigai_customhome_render_text_row($post, '_hub_ch_hero_title', 'Hero タイトル', $d['hero_title'], 'textarea', 4); ?>
                                <?php naigai_customhome_render_text_row($post, '_hub_ch_hero_lead', 'Hero 説明文', $d['hero_lead'], 'textarea', 5); ?>
                                <?php naigai_customhome_render_text_row($post, '_hub_ch_hero_btn1_label', 'Hero 主ボタン文言', $d['hero_btn1_label']); ?>
                                <?php naigai_customhome_render_text_row($post, '_hub_ch_hero_btn1_url', 'Hero 主ボタンURL', $d['hero_btn1_url']); ?>
                                <?php naigai_customhome_render_text_row($post, '_hub_ch_hero_btn2_label', 'Hero 副ボタン文言', $d['hero_btn2_label']); ?>
                                <?php naigai_customhome_render_text_row($post, '_hub_ch_hero_btn2_url', 'Hero 副ボタンURL', $d['hero_btn2_url']); ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="naigai-ch-admin-card">
                        <h3 class="naigai-ch-admin-card__title">メディア設定</h3>
                        <table class="form-table" role="presentation">
                            <tbody>
                                <?php naigai_customhome_render_media_row($post, '_hub_ch_hero_video_mp4_id', 'Hero 動画 mp4', 'video'); ?>
                                <?php naigai_customhome_render_media_row($post, '_hub_ch_hero_video_webm_id', 'Hero 動画 webm', 'video'); ?>
                                <?php naigai_customhome_render_media_row($post, '_hub_ch_hero_video_poster_id', 'Hero poster 画像', 'image'); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <section id="naigai-ch-admin-feature" data-nav-section="feature" class="naigai-ch-admin-section">
                <h2 class="naigai-ch-admin-section__title">Feature</h2>

                <div class="naigai-ch-admin-card naigai-ch-admin-card--full">
                    <h3 class="naigai-ch-admin-card__title">共通設定</h3>
                    <table class="form-table" role="presentation">
                        <tbody>
                            <?php naigai_customhome_render_text_row($post, '_hub_ch_feature_eyebrow', 'Feature ラベル', $d['feature_eyebrow']); ?>
                            <?php naigai_customhome_render_text_row($post, '_hub_ch_feature_title', 'Feature タイトル', $d['feature_title']); ?>
                        </tbody>
                    </table>
                </div>

                <div class="naigai-ch-admin-cards">
                    <?php for ($i = 1; $i <= 8; $i++) : ?>
                        <div class="naigai-ch-admin-card">
                            <h3 class="naigai-ch-admin-card__title">Feature Card <?php echo (int) $i; ?></h3>
                            <table class="form-table" role="presentation">
                                <tbody>
                                    <?php naigai_customhome_render_text_row($post, "_hub_ch_feature_{$i}_title", "カード{$i} タイトル", $d["feature_{$i}_title"]); ?>
                                    <?php naigai_customhome_render_text_row($post, "_hub_ch_feature_{$i}_text", "カード{$i} 説明文", $d["feature_{$i}_text"], 'textarea', 4); ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endfor; ?>
                </div>
            </section>

            <section id="naigai-ch-admin-works" data-nav-section="works" class="naigai-ch-admin-section">
                <h2 class="naigai-ch-admin-section__title">Works</h2>

                <div class="naigai-ch-admin-card naigai-ch-admin-card--full">
                    <h3 class="naigai-ch-admin-card__title">共通設定</h3>
                    <table class="form-table" role="presentation">
                        <tbody>
                            <?php naigai_customhome_render_text_row($post, '_hub_ch_works_eyebrow', 'Works ラベル', $d['works_eyebrow']); ?>
                            <?php naigai_customhome_render_text_row($post, '_hub_ch_works_title', 'Works タイトル', $d['works_title']); ?>
                        </tbody>
                    </table>
                </div>

                <div class="naigai-ch-admin-cards">
                    <?php for ($i = 1; $i <= 4; $i++) : ?>
                        <div class="naigai-ch-admin-card">
                            <h3 class="naigai-ch-admin-card__title">Works Card <?php echo (int) $i; ?></h3>
                            <table class="form-table" role="presentation">
                                <tbody>
                                    <?php naigai_customhome_render_text_row($post, "_hub_ch_work_{$i}_title", "施工事例{$i} タイトル", $d["work_{$i}_title"]); ?>
                                    <?php naigai_customhome_render_text_row($post, "_hub_ch_work_{$i}_text", "施工事例{$i} 説明文", $d["work_{$i}_text"], 'textarea', 4); ?>
                                    <?php naigai_customhome_render_media_row($post, "_hub_ch_work_{$i}_image_id", "施工事例{$i} 画像", 'image'); ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endfor; ?>
                </div>
            </section>

            <section id="naigai-ch-admin-flow" data-nav-section="flow" class="naigai-ch-admin-section">
                <h2 class="naigai-ch-admin-section__title">Flow</h2>

                <div class="naigai-ch-admin-card naigai-ch-admin-card--full">
                    <h3 class="naigai-ch-admin-card__title">共通設定</h3>
                    <table class="form-table" role="presentation">
                        <tbody>
                            <?php naigai_customhome_render_text_row($post, '_hub_ch_flow_eyebrow', 'Flow ラベル', $d['flow_eyebrow']); ?>
                            <?php naigai_customhome_render_text_row($post, '_hub_ch_flow_title', 'Flow タイトル', $d['flow_title']); ?>
                        </tbody>
                    </table>
                </div>

                <div class="naigai-ch-admin-cards">
                    <?php for ($i = 1; $i <= 6; $i++) : ?>
                        <div class="naigai-ch-admin-card">
                            <h3 class="naigai-ch-admin-card__title">Flow Item <?php echo (int) $i; ?></h3>
                            <table class="form-table" role="presentation">
                                <tbody>
                                    <?php naigai_customhome_render_text_row($post, "_hub_ch_flow_{$i}_num", "Flow {$i} 番号", $d["flow_{$i}_num"]); ?>
                                    <?php naigai_customhome_render_text_row($post, "_hub_ch_flow_{$i}_title", "Flow {$i} タイトル", $d["flow_{$i}_title"]); ?>
                                    <?php naigai_customhome_render_text_row($post, "_hub_ch_flow_{$i}_text", "Flow {$i} 説明文", $d["flow_{$i}_text"], 'textarea', 4); ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endfor; ?>
                </div>
            </section>

            <section id="naigai-ch-admin-cta" data-nav-section="cta" class="naigai-ch-admin-section">
    <h2 class="naigai-ch-admin-section__title">CTA</h2>

    <?php
    $cta_swiper_enabled = get_post_meta($post->ID, '_hub_ch_cta_swiper_enabled', true);
    $cta_swiper_nav = get_post_meta($post->ID, '_hub_ch_cta_swiper_nav', true);
    $cta_swiper_pagination = get_post_meta($post->ID, '_hub_ch_cta_swiper_pagination', true);
    $cta_video_controls = get_post_meta($post->ID, '_hub_ch_cta_video_controls', true);

    if ($cta_swiper_enabled === '') $cta_swiper_enabled = '1';
    if ($cta_swiper_nav === '') $cta_swiper_nav = '1';
    if ($cta_swiper_pagination === '') $cta_swiper_pagination = '1';
    if ($cta_swiper_autoplay === '') $cta_swiper_autoplay = '0';
    if ($cta_video_controls === '') $cta_video_controls = '0';

    $cta_image_items = get_post_meta($post->ID, '_hub_ch_cta_image_items', true);
    $cta_video_items = get_post_meta($post->ID, '_hub_ch_cta_video_items', true);
    $cta_media_items = get_post_meta($post->ID, '_hub_ch_cta_media_items', true);
    $cta_legacy_image_id = absint(get_post_meta($post->ID, '_hub_ch_cta_image_id', true));

    if (!is_array($cta_image_items)) $cta_image_items = array();
    if (!is_array($cta_video_items)) $cta_video_items = array();
    if (!is_array($cta_media_items)) $cta_media_items = array();

    if (empty($cta_image_items) && empty($cta_video_items) && !empty($cta_media_items)) {
        foreach ($cta_media_items as $item) {
            if (!is_array($item)) continue;
            $type = isset($item['type']) && $item['type'] === 'video' ? 'video' : 'image';
            $attachment_id = isset($item['attachment_id']) ? absint($item['attachment_id']) : 0;
            if (!$attachment_id) continue;

            if ($type === 'video') {
                $cta_video_items[] = array('attachment_id' => $attachment_id);
            } else {
                $cta_image_items[] = array('attachment_id' => $attachment_id);
            }
        }
    }

    if (empty($cta_image_items) && $cta_legacy_image_id > 0) {
        $cta_image_items[] = array('attachment_id' => $cta_legacy_image_id);
    }

    $cta_image_ids = array();
    foreach ($cta_image_items as $item) {
        $id = is_array($item) ? absint($item['attachment_id'] ?? 0) : absint($item);
        if ($id > 0) $cta_image_ids[] = $id;
    }

    $cta_video_ids = array();
    foreach ($cta_video_items as $item) {
        $id = is_array($item) ? absint($item['attachment_id'] ?? 0) : absint($item);
        if ($id > 0) $cta_video_ids[] = $id;
    }
    ?>

    <style>
    .naigai-ch-admin-cta-media-actions{
        display:flex;
        gap:8px;
        flex-wrap:wrap;
        margin-bottom:10px;
    }
    .naigai-ch-admin-cta-grid{
        display:grid;
        grid-template-columns:repeat(auto-fill,minmax(130px,1fr));
        gap:10px;
    }
    .naigai-ch-admin-cta-item{
        border:1px solid #d7dce2;
        border-radius:12px;
        background:#fff;
        padding:8px;
    }
    .naigai-ch-admin-cta-thumb{
        width:100%;
        height:140px;
        border:1px solid #e5e7eb;
        border-radius:8px;
        overflow:hidden;
        display:grid;
        place-items:center;
        background:#f8fafc;
    }
    .naigai-ch-admin-cta-thumb img{
        width:100%;
        height:100%;
        object-fit:contain;
        background:#fff;
        display:block;
    }
    .naigai-ch-admin-cta-thumb video{
        width:100%;
        height:100%;
        object-fit:cover;
        display:block;
        background:#111;
    }
    .naigai-ch-admin-cta-name{
        margin-top:6px;
        font-size:12px;
        line-height:1.4;
        word-break:break-all;
    }
    </style>

    <div class="naigai-ch-admin-split">
        <div class="naigai-ch-admin-card">
            <h3 class="naigai-ch-admin-card__title">テキスト設定</h3>
            <table class="form-table" role="presentation">
                <tbody>
                    <?php naigai_customhome_render_text_row($post, '_hub_ch_cta_eyebrow', 'CTA ラベル', $d['cta_eyebrow']); ?>
                    <?php naigai_customhome_render_text_row($post, '_hub_ch_cta_title', 'CTA タイトル', $d['cta_title'], 'textarea', 3); ?>
                    <?php naigai_customhome_render_text_row($post, '_hub_ch_cta_text', 'CTA 説明文', $d['cta_text'], 'textarea', 4); ?>
                    <?php naigai_customhome_render_text_row($post, '_hub_ch_cta_btn1_label', 'CTA 主ボタン文言', $d['cta_btn1_label']); ?>
                    <?php naigai_customhome_render_text_row($post, '_hub_ch_cta_btn1_url', 'CTA 主ボタンURL', $d['cta_btn1_url']); ?>
                    <?php naigai_customhome_render_text_row($post, '_hub_ch_cta_btn2_label', 'CTA 副ボタン文言', $d['cta_btn2_label']); ?>
                    <?php naigai_customhome_render_text_row($post, '_hub_ch_cta_btn2_url', 'CTA 副ボタンURL', $d['cta_btn2_url']); ?>
                </tbody>
            </table>
        </div>

        <div class="naigai-ch-admin-card">
            <h3 class="naigai-ch-admin-card__title">CTA背景画像 / 動画 / Swiper設定</h3>
            <table class="form-table" role="presentation">
                <tbody>
                    <tr>
                        <th scope="row"><label for="_hub_ch_cta_image_ids">CTA背景画像</label></th>
                        <td>
                            <input type="hidden" id="_hub_ch_cta_image_ids" name="_hub_ch_cta_image_ids" value="<?php echo esc_attr(implode(',', $cta_image_ids)); ?>">
                            <div class="naigai-ch-admin-cta-media-actions">
                                <button type="button" class="button button-secondary" data-cta-open="image">背景画像を複数選択</button>
                                <button type="button" class="button" data-cta-clear="image">背景画像をクリア</button>
                            </div>
                            <div class="naigai-ch-admin-cta-grid" data-cta-preview="image">
                                <?php foreach ($cta_image_ids as $id) : ?>
                                    <?php
                                    $url = wp_get_attachment_image_url($id, 'medium');
                                    $name = get_the_title($id);
                                    if (!$url) continue;
                                    ?>
                                    <div class="naigai-ch-admin-cta-item">
                                        <div class="naigai-ch-admin-cta-thumb"><img src="<?php echo esc_url($url); ?>" alt=""></div>
                                        <div class="naigai-ch-admin-cta-name"><?php echo esc_html($name ?: basename($url)); ?></div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><label for="_hub_ch_cta_video_ids">CTA背景動画 mp4</label></th>
                        <td>
                            <input type="hidden" id="_hub_ch_cta_video_ids" name="_hub_ch_cta_video_ids" value="<?php echo esc_attr(implode(',', $cta_video_ids)); ?>">
                            <div class="naigai-ch-admin-cta-media-actions">
                                <button type="button" class="button button-secondary" data-cta-open="video">背景動画を複数選択</button>
                                <button type="button" class="button" data-cta-clear="video">背景動画をクリア</button>
                            </div>
                            <div class="naigai-ch-admin-cta-grid" data-cta-preview="video">
                                <?php foreach ($cta_video_ids as $id) : ?>
                                    <?php
                                    $url = wp_get_attachment_url($id);
                                    $name = get_the_title($id);
                                    if (!$url) continue;
                                    ?>
                                    <div class="naigai-ch-admin-cta-item">
                                        <div class="naigai-ch-admin-cta-thumb"><video src="<?php echo esc_url($url); ?>" muted playsinline preload="metadata"></video></div>
                                        <div class="naigai-ch-admin-cta-name"><?php echo esc_html($name ?: basename($url)); ?></div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">Swiper / 動画設定</th>
                        <td>
                            <label><input type="checkbox" name="_hub_ch_cta_swiper_enabled" value="1" <?php checked($cta_swiper_enabled, '1'); ?>> Swiper を使う</label><br>
                            <label><input type="checkbox" name="_hub_ch_cta_swiper_nav" value="1" <?php checked($cta_swiper_nav, '1'); ?>> 矢印を表示</label><br>
                            <label><input type="checkbox" name="_hub_ch_cta_swiper_pagination" value="1" <?php checked($cta_swiper_pagination, '1'); ?>> ページャーを表示</label><br>
                            <label><input type="checkbox" name="_hub_ch_cta_swiper_autoplay" value="1" <?php checked($cta_swiper_autoplay, '1'); ?>> 自動再生する</label><br>
                            <label><input type="checkbox" name="_hub_ch_cta_video_controls" value="1" <?php checked($cta_video_controls, '1'); ?>> 動画コントローラーを表示</label>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof wp === 'undefined' || !wp.media) return;

        function renderPreview(kind, items) {
            var wrap = document.querySelector('[data-cta-preview="' + kind + '"]');
            if (!wrap) return;

            if (!items.length) {
                wrap.innerHTML = '';
                return;
            }

            wrap.innerHTML = items.map(function(item){
                var thumb = kind === 'video'
                    ? '<video src="' + item.url + '" muted playsinline preload="metadata"></video>'
                    : '<img src="' + item.url + '" alt="">';

                return '<div class="naigai-ch-admin-cta-item"><div class="naigai-ch-admin-cta-thumb">' + thumb + '</div><div class="naigai-ch-admin-cta-name">' + (item.title || item.filename || '') + '</div></div>';
            }).join('');
        }

        function bindMediaPicker(kind) {
            var input = document.getElementById(kind === 'video' ? '_hub_ch_cta_video_ids' : '_hub_ch_cta_image_ids');
            var openBtn = document.querySelector('[data-cta-open="' + kind + '"]');
            var clearBtn = document.querySelector('[data-cta-clear="' + kind + '"]');

            if (openBtn) {
                openBtn.addEventListener('click', function () {
                    var frame = wp.media({
                        title: kind === 'video' ? 'CTA動画を選択' : 'CTA画像を選択',
                        button: { text: '選択したメディアを使用' },
                        multiple: true,
                        library: { type: kind === 'video' ? 'video' : 'image' }
                    });

                    frame.on('select', function () {
                        var items = frame.state().get('selection').toJSON();
                        input.value = items.map(function(item){ return item.id; }).join(',');
                        renderPreview(kind, items);
                    });

                    frame.open();
                });
            }

            if (clearBtn) {
                clearBtn.addEventListener('click', function () {
                    input.value = '';
                    renderPreview(kind, []);
                });
            }
        }

        bindMediaPicker('image');
        bindMediaPicker('video');
    });
    </script>
</section>

        </div>
        <?php
    }
}

/* =========================================================
 * メタボックス登録
 * 注文住宅ページだけに追加する
 * ========================================================= */
if (!function_exists('naigai_customhome_add_meta_box')) {
    function naigai_customhome_add_meta_box()
    {
        global $post;

        if (!$post || !naigai_is_customhome_target_page($post->ID)) {
            return;
        }

        add_meta_box(
            'naigai_customhome_meta_box',
            '注文住宅ページ設定',
            'naigai_customhome_meta_box',
            'page',
            'normal',
            'high'
        );
    }
// DISABLED: /iezukuri admin metabox removed.
//     add_action('add_meta_boxes', 'naigai_customhome_add_meta_box');
}

/* =========================================================
 * メディアUI読込
 * 注文住宅ページ編集時だけ
 * ========================================================= */
if (!function_exists('naigai_customhome_admin_enqueue_media')) {
    function naigai_customhome_admin_enqueue_media($hook)
    {
        if (!in_array($hook, array('post.php', 'post-new.php'), true)) {
            return;
        }

        $post_id = isset($_GET['post']) ? (int) $_GET['post'] : 0;
        if ($post_id <= 0 && isset($_POST['post_ID'])) {
            $post_id = (int) $_POST['post_ID'];
        }

        $is_main_target = $post_id > 0 && function_exists('naigai_is_customhome_target_page') && naigai_is_customhome_target_page($post_id);
        $is_sub_target  = $post_id > 0 && function_exists('naigai_customhome_subpage_common_is_target') && naigai_customhome_subpage_common_is_target($post_id);

        if (!$is_main_target && !$is_sub_target) {
            return;
        }

        wp_enqueue_media();
    }
    add_action('admin_enqueue_scripts', 'naigai_customhome_admin_enqueue_media');
}

/* =========================================================
 * 保存処理
 * 注文住宅ページだけで保存する
 * ========================================================= */
if (!function_exists('naigai_customhome_save_meta_box')) {
    function naigai_customhome_save_meta_box($post_id)
    {
        if (!naigai_is_customhome_target_page($post_id)) {
            return;
        }

        if (
            !isset($_POST['naigai_customhome_meta_box_nonce']) ||
            !wp_verify_nonce($_POST['naigai_customhome_meta_box_nonce'], 'naigai_customhome_meta_box_action')
        ) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        $text_fields = array(
            '_hub_ch_hero_kicker',
            '_hub_ch_hero_title',
            '_hub_ch_hero_lead',
            '_hub_ch_hero_btn1_label',
            '_hub_ch_hero_btn1_url',
            '_hub_ch_hero_btn2_label',
            '_hub_ch_hero_btn2_url',

            '_hub_ch_feature_eyebrow',
            '_hub_ch_feature_title',
            '_hub_ch_works_eyebrow',
            '_hub_ch_works_title',
            '_hub_ch_flow_eyebrow',
            '_hub_ch_flow_title',
            '_hub_ch_cta_eyebrow',
            '_hub_ch_cta_title',
            '_hub_ch_cta_text',
            '_hub_ch_cta_btn1_label',
            '_hub_ch_cta_btn1_url',
            '_hub_ch_cta_btn2_label',
            '_hub_ch_cta_btn2_url',
        );

        for ($i = 1; $i <= 4; $i++) {
            $text_fields[] = "_hub_ch_feature_{$i}_title";
            $text_fields[] = "_hub_ch_feature_{$i}_text";
        }

        for ($i = 1; $i <= 8; $i++) {
            $text_fields[] = "_hub_ch_work_{$i}_title";
            $text_fields[] = "_hub_ch_work_{$i}_text";
        }

        for ($i = 1; $i <= 6; $i++) {
            $text_fields[] = "_hub_ch_flow_{$i}_num";
            $text_fields[] = "_hub_ch_flow_{$i}_title";
            $text_fields[] = "_hub_ch_flow_{$i}_text";
        }

        foreach ($text_fields as $field) {
            if (isset($_POST[$field])) {
                $value = wp_unslash($_POST[$field]);

                if ($value !== '') {
                    update_post_meta($post_id, $field, $value);
                } else {
                    delete_post_meta($post_id, $field);
                }
            }
        }

        $id_fields = array(
            '_hub_ch_hero_video_mp4_id',
            '_hub_ch_hero_video_webm_id',
            '_hub_ch_hero_video_poster_id',
            '_hub_ch_cta_image_id',
        );

        for ($i = 1; $i <= 8; $i++) {
            $id_fields[] = "_hub_ch_work_{$i}_image_id";
        }

        foreach ($id_fields as $field) {
            $value = isset($_POST[$field]) ? absint($_POST[$field]) : 0;

            if ($value) {
                update_post_meta($post_id, $field, $value);
            } else {
                delete_post_meta($post_id, $field);
            }
        }

        $cta_image_ids_raw = isset($_POST['_hub_ch_cta_image_ids']) ? sanitize_text_field($_POST['_hub_ch_cta_image_ids']) : '';
        $cta_video_ids_raw = isset($_POST['_hub_ch_cta_video_ids']) ? sanitize_text_field($_POST['_hub_ch_cta_video_ids']) : '';

        $cta_image_ids = array_values(array_filter(array_map('absint', preg_split('/\s*,\s*/', $cta_image_ids_raw)), function ($id) {
            return $id > 0;
        }));
        $cta_video_ids = array_values(array_filter(array_map('absint', preg_split('/\s*,\s*/', $cta_video_ids_raw)), function ($id) {
            return $id > 0;
        }));

        $cta_image_items = array();
        foreach ($cta_image_ids as $id) {
            $cta_image_items[] = array(
                'type' => 'image',
                'attachment_id' => $id,
            );
        }

        $cta_video_items = array();
        foreach ($cta_video_ids as $id) {
            $cta_video_items[] = array(
                'type' => 'video',
                'attachment_id' => $id,
            );
        }

        if (empty($cta_image_items)) {
            delete_post_meta($post_id, '_hub_ch_cta_image_items');
        } else {
            update_post_meta($post_id, '_hub_ch_cta_image_items', $cta_image_items);
        }

        if (empty($cta_video_items)) {
            delete_post_meta($post_id, '_hub_ch_cta_video_items');
        } else {
            update_post_meta($post_id, '_hub_ch_cta_video_items', $cta_video_items);
        }

        $cta_media_items = array_merge($cta_image_items, $cta_video_items);
        if (empty($cta_media_items)) {
            delete_post_meta($post_id, '_hub_ch_cta_media_items');
        } else {
            update_post_meta($post_id, '_hub_ch_cta_media_items', $cta_media_items);
        }

        if (!empty($cta_image_ids)) {
            update_post_meta($post_id, '_hub_ch_cta_image_id', $cta_image_ids[0]);
        } else {
            delete_post_meta($post_id, '_hub_ch_cta_image_id');
        }

        update_post_meta($post_id, '_hub_ch_cta_swiper_enabled', isset($_POST['_hub_ch_cta_swiper_enabled']) ? '1' : '0');
        update_post_meta($post_id, '_hub_ch_cta_swiper_nav', isset($_POST['_hub_ch_cta_swiper_nav']) ? '1' : '0');
        update_post_meta($post_id, '_hub_ch_cta_swiper_pagination', isset($_POST['_hub_ch_cta_swiper_pagination']) ? '1' : '0');
        update_post_meta($post_id, '_hub_ch_cta_swiper_autoplay', isset($_POST['_hub_ch_cta_swiper_autoplay']) ? '1' : '0');
        update_post_meta($post_id, '_hub_ch_cta_video_controls', isset($_POST['_hub_ch_cta_video_controls']) ? '1' : '0');
    }
    add_action('save_post_page', 'naigai_customhome_save_meta_box');
}



/* ===== Customhome Subpage Common Meta Box ===== */

if (!function_exists('naigai_customhome_subpage_common_is_target')) {
    function naigai_customhome_subpage_common_is_target($post_id = 0)
    {
        $post_id = (int) $post_id;
        if ($post_id <= 0) {
            return false;
        }

        $post = get_post($post_id);
        if (!$post || $post->post_type !== 'page') {
            return false;
        }

        $template = (string) get_page_template_slug($post_id);
        $slug = (string) $post->post_name;
        $parent_id = (int) $post->post_parent;
        $parent_slug = $parent_id > 0 ? (string) get_post_field('post_name', $parent_id) : '';
        $subpage_template = trim((string) get_post_meta($post_id, '_ch_subpage_template', true));

        if ($template === 'page-construction-hub-sub.php') {
            return true;
        }

        if (in_array($parent_slug, array('construction-hub', 'iezukuri'), true)) {
            return true;
        }

        if ($subpage_template !== '') {
            return true;
        }

        return in_array($slug, array(
            'concept',
            'design-policy',
            'nasu-house',
            'design-office',
            'company',
            'contact',
        ), true);
    }
}

if (!function_exists('naigai_customhome_concept_is_target')) {
    function naigai_customhome_concept_is_target($post_id = 0)
    {
        if (!naigai_customhome_subpage_common_is_target($post_id)) {
            return false;
        }

        $slug = (string) get_post_field('post_name', $post_id);
        $template_key = trim((string) get_post_meta($post_id, '_ch_subpage_template', true));

        return $slug === 'concept' || $template_key === 'concept';
    }
}

if (!function_exists('naigai_customhome_subpage_common_select_row')) {
    function naigai_customhome_subpage_common_select_row($post_id, $meta_key, $label, array $options)
    {
        $value = (string) get_post_meta($post_id, $meta_key, true);

        echo '<tr>';
        echo '<th scope="row"><label for="' . esc_attr($meta_key) . '">' . esc_html($label) . '</label></th>';
        echo '<td><select id="' . esc_attr($meta_key) . '" name="' . esc_attr($meta_key) . '">';

        foreach ($options as $option_value => $option_label) {
            echo '<option value="' . esc_attr($option_value) . '" ' . selected($value, (string) $option_value, false) . '>' . esc_html($option_label) . '</option>';
        }

        echo '</select></td>';
        echo '</tr>';
    }
}

if (!function_exists('naigai_customhome_subpage_common_checkbox_row')) {
    function naigai_customhome_subpage_common_checkbox_row($post_id, $meta_key, $label)
    {
        $checked = (string) get_post_meta($post_id, $meta_key, true) === '1';

        echo '<tr>';
        echo '<th scope="row">' . esc_html($label) . '</th>';
        echo '<td><input type="hidden" name="' . esc_attr($meta_key) . '" value="0">';
        echo '<label><input type="checkbox" name="' . esc_attr($meta_key) . '" value="1" ' . checked($checked, true, false) . '> 有効にする</label></td>';
        echo '</tr>';
    }
}

if (!function_exists('naigai_customhome_subpage_common_add_meta_boxes')) {
    function naigai_customhome_subpage_common_add_meta_boxes()
    {
        global $post;

        if (!$post) {
            return;
        }

        if (naigai_customhome_subpage_common_is_target($post->ID)) {
            add_meta_box(
                'naigai_customhome_subpage_common_meta_box',
                '注文住宅サブページ共通設定',
                'naigai_customhome_subpage_common_render_meta_box',
                'page',
                'normal',
                'high'
            );
        }

        if (naigai_customhome_concept_is_target($post->ID)) {
            add_meta_box(
                'naigai_customhome_concept_meta_box',
                'concept ページ設定',
                'naigai_customhome_concept_render_meta_box',
                'page',
                'normal',
                'high'
            );
        }
    }
// DISABLED: old unused subpage common settings box. Use admin/page-settings-tabs.php only.
//     add_action('add_meta_boxes', 'naigai_customhome_subpage_common_add_meta_boxes', 25);
}

if (!function_exists('naigai_customhome_subpage_cleanup_meta_boxes')) {
    function naigai_customhome_subpage_cleanup_meta_boxes()
    {
        global $post;

        if (!$post) {
            return;
        }

        if (naigai_customhome_subpage_common_is_target($post->ID)) {
            remove_meta_box('naigai_customhome_meta_box', 'page', 'normal');
            remove_meta_box('naigai-customhome-extra-settings', 'page', 'normal');
            remove_meta_box('naigai-customhome-phase2', 'page', 'normal');
        } elseif (function_exists('naigai_is_customhome_target_page') && naigai_is_customhome_target_page($post->ID)) {
            remove_meta_box('naigai_customhome_subpage_common_meta_box', 'page', 'normal');
            remove_meta_box('naigai_customhome_concept_meta_box', 'page', 'normal');
        }
    }
// DISABLED: old unused subpage common settings box. Use admin/page-settings-tabs.php only.
//     add_action('add_meta_boxes', 'naigai_customhome_subpage_cleanup_meta_boxes', 999);
}

if (!function_exists('naigai_customhome_subpage_common_render_meta_box')) {
    function naigai_customhome_subpage_common_render_meta_box($post)
    {
        if (!naigai_customhome_subpage_common_is_target($post->ID)) {
            return;
        }

        wp_nonce_field('naigai_customhome_subpage_common_action', 'naigai_customhome_subpage_common_nonce');
        naigai_customhome_admin_ui_assets();

        echo '<table class="form-table" role="presentation"><tbody>';

        naigai_customhome_subpage_common_select_row($post->ID, '_ch_subpage_template', '本文テンプレート', array(
            ''              => 'slug に従う',
            'concept'       => 'concept',
            'design-policy' => 'design-policy',
            'nasu-house'    => 'nasu-house',
            'design-office' => 'design-office',
            'company'       => 'company',
            'contact'       => 'contact',
        ));

        naigai_customhome_render_text_row($post, '_ch_hero_kicker', 'Hero ラベル', '');
        naigai_customhome_render_text_row($post, '_ch_hero_title', 'Hero タイトル', '', 'textarea', 3);
        naigai_customhome_render_text_row($post, '_ch_hero_lead', 'Hero 説明文', '', 'textarea', 5);
        naigai_customhome_render_media_row($post, '_ch_hero_image_id', 'Hero 画像', 'image');
        naigai_customhome_render_text_row($post, '_ch_back_url', '戻る先 URL', '', 'url');
        naigai_customhome_render_text_row($post, '_ch_contact_url', 'お問い合わせ URL', '', 'url');
        naigai_customhome_render_text_row($post, '_ch_hero_primary_label', 'Hero 主ボタン文言', '');
        naigai_customhome_render_text_row($post, '_ch_hero_secondary_label', 'Hero 副ボタン文言', '');

        naigai_customhome_subpage_common_select_row($post->ID, '_ch_layout_mode', 'レイアウトモード', array(
            'stack'        => 'stack',
            'two-column'   => 'two-column',
            'content-form' => 'content-form',
            'content-faq'  => 'content-faq',
        ));

        naigai_customhome_subpage_common_checkbox_row($post->ID, '_ch_use_parent_works', '親の施工事例を流す');
        naigai_customhome_subpage_common_checkbox_row($post->ID, '_ch_use_parent_flow', '親の家づくりの流れを流す');
        naigai_customhome_subpage_common_checkbox_row($post->ID, '_ch_show_common_form', '共通フォームを表示');
        naigai_customhome_subpage_common_checkbox_row($post->ID, '_ch_show_common_faq', '共通 FAQ を表示');

        naigai_customhome_render_text_row($post, '_ch_common_form_shortcode', 'フォーム shortcode', '', 'textarea', 3);

        for ($i = 1; $i <= 5; $i++) {
            naigai_customhome_render_text_row($post, '_ch_faq_' . $i . '_q', 'FAQ ' . $i . ' 質問', '', 'textarea', 2);
            naigai_customhome_render_text_row($post, '_ch_faq_' . $i . '_a', 'FAQ ' . $i . ' 回答', '', 'textarea', 5);
        }

        echo '</tbody></table>';
    }
}

if (!function_exists('naigai_customhome_concept_render_meta_box')) {
    function naigai_customhome_concept_render_meta_box($post)
    {
        if (!naigai_customhome_concept_is_target($post->ID)) {
            return;
        }

        wp_nonce_field('naigai_customhome_concept_action', 'naigai_customhome_concept_nonce');
        naigai_customhome_admin_ui_assets();

        echo '<table class="form-table" role="presentation"><tbody>';

        naigai_customhome_render_text_row($post, '_ch_concept_intro_title', '導入タイトル', '', 'textarea', 3);
        naigai_customhome_render_text_row($post, '_ch_concept_intro_text', '導入本文', '', 'textarea', 6);

        for ($i = 1; $i <= 3; $i++) {
            naigai_customhome_render_text_row($post, '_ch_concept_principle_' . $i . '_title', 'こだわり ' . $i . ' タイトル', '');
            naigai_customhome_render_text_row($post, '_ch_concept_principle_' . $i . '_text', 'こだわり ' . $i . ' 本文', '', 'textarea', 4);
        }

        naigai_customhome_render_text_row($post, '_ch_concept_philosophy_title', '哲学タイトル', '');
        naigai_customhome_render_text_row($post, '_ch_concept_philosophy_text_1', '哲学本文 1', '', 'textarea', 4);
        naigai_customhome_render_text_row($post, '_ch_concept_philosophy_text_2', '哲学本文 2', '', 'textarea', 4);
        naigai_customhome_render_text_row($post, '_ch_concept_philosophy_btn_label', '哲学ボタン文言', '');
        naigai_customhome_render_text_row($post, '_ch_concept_philosophy_btn_url', '哲学ボタンURL', '', 'url');

        for ($i = 1; $i <= 4; $i++) {
            naigai_customhome_render_text_row($post, '_ch_concept_metric_' . $i . '_label', '指標 ' . $i . ' ラベル', '');
            naigai_customhome_render_text_row($post, '_ch_concept_metric_' . $i . '_text', '指標 ' . $i . ' 本文', '');
        }

        naigai_customhome_render_text_row($post, '_ch_concept_cases_title', '施工事例セクション見出し', '');
        naigai_customhome_render_text_row($post, '_ch_concept_cases_link_label', '施工事例リンク文言', '');

        for ($i = 1; $i <= 4; $i++) {
            naigai_customhome_render_text_row($post, '_ch_concept_case_' . $i . '_title', '施工事例 ' . $i . ' タイトル', '');
        }

        naigai_customhome_render_text_row($post, '_ch_concept_cta_title', 'CTA タイトル', '', 'textarea', 3);
        naigai_customhome_render_text_row($post, '_ch_concept_cta_text', 'CTA 本文', '', 'textarea', 4);
        naigai_customhome_render_text_row($post, '_ch_concept_cta_primary_label', 'CTA 主ボタン文言', '');
        naigai_customhome_render_text_row($post, '_ch_concept_cta_secondary_label', 'CTA 副ボタン文言', '');

        echo '</tbody></table>';
    }
}

if (!function_exists('naigai_customhome_subpage_common_save_meta_box')) {
    function naigai_customhome_subpage_common_save_meta_box($post_id)
    {
        if (!isset($_POST['naigai_customhome_subpage_common_nonce'])) {
            return;
        }

        if (!wp_verify_nonce($_POST['naigai_customhome_subpage_common_nonce'], 'naigai_customhome_subpage_common_action')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        if (!naigai_customhome_subpage_common_is_target($post_id)) {
            return;
        }

        $single_line_keys = array(
            '_ch_subpage_template',
            '_ch_hero_kicker',
            '_ch_back_url',
            '_ch_contact_url',
            '_ch_hero_primary_label',
            '_ch_hero_secondary_label',
            '_ch_layout_mode',
        );

        foreach ($single_line_keys as $key) {
            $value = isset($_POST[$key]) ? wp_unslash($_POST[$key]) : '';
            if ($key === '_ch_back_url' || $key === '_ch_contact_url') {
                $value = esc_url_raw($value);
            } else {
                $value = sanitize_text_field($value);
            }

            if ($value === '') {
                delete_post_meta($post_id, $key);
            } else {
                update_post_meta($post_id, $key, $value);
            }
        }

        $textarea_keys = array(
            '_ch_hero_title',
            '_ch_hero_lead',
            '_ch_common_form_shortcode',
        );

        foreach ($textarea_keys as $key) {
            $value = isset($_POST[$key]) ? sanitize_textarea_field(wp_unslash($_POST[$key])) : '';

            if ($value === '') {
                delete_post_meta($post_id, $key);
            } else {
                update_post_meta($post_id, $key, $value);
            }
        }

        $hero_image_id = isset($_POST['_ch_hero_image_id']) ? (int) $_POST['_ch_hero_image_id'] : 0;
        if ($hero_image_id > 0) {
            update_post_meta($post_id, '_ch_hero_image_id', $hero_image_id);
        } else {
            delete_post_meta($post_id, '_ch_hero_image_id');
        }

        $check_keys = array(
            '_ch_use_parent_works',
            '_ch_use_parent_flow',
            '_ch_show_common_form',
            '_ch_show_common_faq',
        );

        foreach ($check_keys as $key) {
            $value = isset($_POST[$key]) && (string) $_POST[$key] === '1' ? '1' : '0';
            update_post_meta($post_id, $key, $value);
        }

        for ($i = 1; $i <= 5; $i++) {
            $q_key = '_ch_faq_' . $i . '_q';
            $a_key = '_ch_faq_' . $i . '_a';

            $q_val = isset($_POST[$q_key]) ? sanitize_textarea_field(wp_unslash($_POST[$q_key])) : '';
            $a_val = isset($_POST[$a_key]) ? sanitize_textarea_field(wp_unslash($_POST[$a_key])) : '';

            if ($q_val === '') {
                delete_post_meta($post_id, $q_key);
            } else {
                update_post_meta($post_id, $q_key, $q_val);
            }

            if ($a_val === '') {
                delete_post_meta($post_id, $a_key);
            } else {
                update_post_meta($post_id, $a_key, $a_val);
            }
        }
    }
    add_action('save_post_page', 'naigai_customhome_subpage_common_save_meta_box', 25);
}

if (!function_exists('naigai_customhome_concept_save_meta_box')) {
    function naigai_customhome_concept_save_meta_box($post_id)
    {
        if (!isset($_POST['naigai_customhome_concept_nonce'])) {
            return;
        }

        if (!wp_verify_nonce($_POST['naigai_customhome_concept_nonce'], 'naigai_customhome_concept_action')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        if (!naigai_customhome_concept_is_target($post_id)) {
            return;
        }

        $keys = array(
            '_ch_concept_intro_title',
            '_ch_concept_intro_text',
            '_ch_concept_philosophy_title',
            '_ch_concept_philosophy_text_1',
            '_ch_concept_philosophy_text_2',
            '_ch_concept_philosophy_btn_label',
            '_ch_concept_philosophy_btn_url',
            '_ch_concept_cases_title',
            '_ch_concept_cases_link_label',
            '_ch_concept_cta_title',
            '_ch_concept_cta_text',
            '_ch_concept_cta_primary_label',
            '_ch_concept_cta_secondary_label',
        );

        for ($i = 1; $i <= 3; $i++) {
            $keys[] = '_ch_concept_principle_' . $i . '_title';
            $keys[] = '_ch_concept_principle_' . $i . '_text';
        }

        for ($i = 1; $i <= 4; $i++) {
            $keys[] = '_ch_concept_metric_' . $i . '_label';
            $keys[] = '_ch_concept_metric_' . $i . '_text';
            $keys[] = '_ch_concept_case_' . $i . '_title';
        }

        foreach ($keys as $key) {
            $value = isset($_POST[$key]) ? wp_unslash($_POST[$key]) : '';
            if (str_ends_with($key, '_url')) {
                $value = esc_url_raw($value);
            } else {
                $value = sanitize_textarea_field($value);
            }

            if ($value === '') {
                delete_post_meta($post_id, $key);
            } else {
                update_post_meta($post_id, $key, $value);
            }
        }
    }
    add_action('save_post_page', 'naigai_customhome_concept_save_meta_box', 26);
}

