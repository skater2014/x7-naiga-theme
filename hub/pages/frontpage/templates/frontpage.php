<?php

/**
 * Frontpage Portal
 *
 * 役割:
 * - フロントページを総合窓口として表示する
 * - 不動産 / 家づくり / 民泊・宿泊 / 法人向け相談 の入口を作る
 * - 上部3分割メディアは 画像 / mp4 両対応
 * - お知らせ・コラムはカスタム投稿なしでもメタボックス入力で表示
 */

if (!defined('ABSPATH')) {
    exit;
}

$post_id = get_queried_object_id();

$fp_get = function ($key, $default = '') use ($post_id) {
    /*
     * フロント用の値取得
     *
     * 役割:
     * - 保存済みの管理画面メタ値だけを優先して読む
     * - 空文字で保存されている場合も、その空文字を尊重する
     * - デフォルト文言は原則として管理画面側に表示・保存させる
     *
     * 重要:
     * - 管理画面でデフォルト文言を消した場合、フロント側で勝手に戻さない
     */
    if (metadata_exists('post', $post_id, $key)) {
        return get_post_meta($post_id, $key, true);
    }

    return $default;
};

$fp_url = function ($key, $default = '') use ($fp_get) {
    $v = $fp_get($key, $default);
    return $v ? esc_url($v) : '#';
};

$fp_one_line = function ($value) {
    /*
     * 1行テキスト化
     *
     * 役割:
     * - 管理画面のtextareaに入った改行を、見出しでは強制改行にしない
     * - 余計な段落・改行で「見出しが分解される」問題を防ぐ
     * - 本文ではなく、見出し専用で使う
     */
    return trim(preg_replace('/\s+/u', ' ', wp_strip_all_tags((string) $value)));
};

$fp_clean_url = function ($url) {
    /*
     * URL正規化
     *
     * 役割:
     * - 管理画面のURL選択で「リンクなし」を選んだ場合、__none__ をフロントに出さない
     * - 空や __none__ は # に変換する
     * - 通常のURLはそのまま返す
     */
    $url = trim((string) $url);
    return ($url === '' || $url === '__none__') ? '#' : $url;
};

$fp_url_to_post_id = function ($url) {
    /*
     * URLから固定ページIDを取得
     *
     * 役割:
     * - 管理画面で選択したURLをもとに、対応する固定ページを探す
     * - オリジナル見出しが空の場合、そのページタイトルをカード見出しに使う
     * - オリジナル本文が空の場合、そのページの抜粋や本文冒頭を使う
     */
    $url = trim((string) $url);

    if ($url === '' || $url === '#' || $url === '__none__') {
        return 0;
    }

    $post_id_from_url = url_to_postid($url);
    if ($post_id_from_url) {
        return (int) $post_id_from_url;
    }

    $home_path = trim((string) wp_parse_url(home_url('/'), PHP_URL_PATH), '/');
    $url_path  = trim((string) wp_parse_url($url, PHP_URL_PATH), '/');

    if ($home_path !== '' && strpos($url_path, $home_path . '/') === 0) {
        $url_path = trim(substr($url_path, strlen($home_path)), '/');
    }

    if ($url_path === '') {
        return 0;
    }

    $page = get_page_by_path($url_path, OBJECT, 'page');
    return $page ? (int) $page->ID : 0;
};

$fp_page_excerpt = function ($page_id) {
    /*
     * 固定ページの説明文を取得
     *
     * 優先順位:
     * 1. 固定ページの抜粋
     * 2. 固定ページ本文の冒頭
     */
    $page_id = (int) $page_id;
    if (!$page_id) {
        return '';
    }

    if (has_excerpt($page_id)) {
        return trim((string) get_the_excerpt($page_id));
    }

    $page = get_post($page_id);
    if (!$page) {
        return '';
    }

    return wp_trim_words(wp_strip_all_tags($page->post_content), 34, '…');
};

$fp_icon_svg = function ($key) {
    /*
     * 役割:
     * - フロントページのサービスカード用SVGアイコンを返す
     * - 不動産 / 家づくり / 民泊・住宅宿泊 / 法人相談 / 事業用途ブロックを切り替える
     *
     * 方針:
     * - 24px系のシンプルな線画アイコンで統一
     * - CSS側の currentColor で色を管理する
     * - 民泊はホテル・宿泊として読みやすい「ベッド」アイコンにする
     */
    $svg_attr = 'viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round"';

    $icons = array(
        'realestate' => '<svg ' . $svg_attr . '>
            <path d="M3 21h18"/>
            <path d="M5 21V10l7-6 7 6v11"/>
            <path d="M9 21v-7h6v7"/>
            <path d="M9 10h.01"/>
            <path d="M15 10h.01"/>
        </svg>',

        'home' => '<svg ' . $svg_attr . '>
            <path d="M3 11l9-8 9 8"/>
            <path d="M5 10v11h14V10"/>
            <path d="M10 21v-6h4v6"/>
            <path d="M16 5v3"/>
        </svg>',

        'stay' => '<svg ' . $svg_attr . '>
            <path d="M3 21V9"/>
            <path d="M21 21v-7a4 4 0 0 0-4-4H8"/>
            <path d="M3 14h18"/>
            <path d="M7 10V7a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3"/>
            <path d="M3 18h18"/>
        </svg>',

        'business' => '<svg ' . $svg_attr . '>
            <path d="M4 21h16"/>
            <path d="M6 21V5a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v16"/>
            <path d="M9 7h1"/>
            <path d="M14 7h1"/>
            <path d="M9 11h1"/>
            <path d="M14 11h1"/>
            <path d="M9 15h1"/>
            <path d="M14 15h1"/>
        </svg>',

        'partner' => '<svg ' . $svg_attr . '>
            <path d="M3 21h18"/>
            <path d="M5 21V7a2 2 0 0 1 2-2h5v16"/>
            <path d="M12 9h5a2 2 0 0 1 2 2v10"/>
            <path d="M8 9h1"/>
            <path d="M8 13h1"/>
            <path d="M15 13h1"/>
            <path d="M15 17h1"/>
        </svg>',
    );

    return $icons[$key] ?? $icons['realestate'];
};

$extract_youtube_id = function ($value) {
    $value = trim((string) $value);
    if ($value === '') {
        return '';
    }

    if (preg_match('~(?:youtu\.be/|youtube\.com/(?:watch\?v=|embed/|shorts/))([A-Za-z0-9_-]{6,})~', $value, $m)) {
        return $m[1];
    }

    if (preg_match('~^[A-Za-z0-9_-]{6,}$~', $value)) {
        return $value;
    }

    return '';
};

$extract_vimeo_id = function ($value) {
    $value = trim((string) $value);
    if ($value === '') {
        return '';
    }

    if (preg_match('~vimeo\.com/(?:video/)?([0-9]+)~', $value, $m)) {
        return $m[1];
    }

    if (preg_match('~^[0-9]+$~', $value)) {
        return $value;
    }

    return '';
};

$render_media = function ($slot, $class = '') use ($post_id, $fp_get, $fp_one_line, $fp_clean_url, $extract_youtube_id, $extract_vimeo_id) {
    $type = $fp_get("_fp_hero_media_{$slot}_type", 'image');

    if (!in_array($type, array('image', 'mp4', 'youtube', 'vimeo'), true)) {
        $type = 'image';
    }

    $image_id = (int) $fp_get("_fp_hero_media_{$slot}_image_id", 0);
    $mp4_id   = (int) $fp_get("_fp_hero_media_{$slot}_mp4_id", 0);
    $youtube  = trim((string) $fp_get("_fp_hero_media_{$slot}_youtube", ''));
    $vimeo    = trim((string) $fp_get("_fp_hero_media_{$slot}_vimeo", ''));
    $label    = trim((string) $fp_get("_fp_hero_media_{$slot}_label", ''));

    $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'large') : '';
    $video_url = $mp4_id ? wp_get_attachment_url($mp4_id) : '';

    $youtube_id = $extract_youtube_id($youtube);
    $vimeo_id   = $extract_vimeo_id($vimeo);

    if ($label === '') {
        $label = array(
            'image'   => '画像',
            'mp4'     => 'MP4動画',
            'youtube' => 'YouTube',
            'vimeo'   => 'Vimeo',
        )[$type];
    }

    echo '<div class="fp-hero-media__item ' . esc_attr($class) . ' fp-hero-media__item--' . esc_attr($type) . '">';

    if ($type === 'image') {
        if ($image_url) {
            echo '<img class="fp-hero-media__img" src="' . esc_url($image_url) . '" alt="">';
        } else {
            echo '<div class="fp-hero-media__placeholder"></div>';
        }
    } elseif ($type === 'mp4') {
        if ($video_url) {
            $poster_attr = $image_url ? ' poster="' . esc_url($image_url) . '"' : '';
            echo '<video class="fp-hero-media__video" muted playsinline loop autoplay preload="metadata"' . $poster_attr . '>';
            echo '<source src="' . esc_url($video_url) . '" type="video/mp4">';
            echo '</video>';
            echo '<button class="fp-hero-media__play" type="button" aria-label="動画を再生・停止"></button>';
        } else {
            echo '<div class="fp-hero-media__placeholder"><span>MP4未設定</span></div>';
        }
    } elseif ($type === 'youtube') {
        if ($youtube_id) {
            echo '<lite-youtube class="fp-hero-media__lite-youtube" videoid="' . esc_attr($youtube_id) . '" params="rel=0&modestbranding=1"></lite-youtube>';
        } else {
            echo '<div class="fp-hero-media__placeholder"><span>YouTube未設定</span></div>';
        }
    } elseif ($type === 'vimeo') {
        if ($vimeo_id) {
            echo '<iframe class="fp-hero-media__iframe fp-hero-media__iframe--vimeo" src="https://player.vimeo.com/video/' . esc_attr($vimeo_id) . '" title="Vimeo video" loading="lazy" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>';
        } else {
            echo '<div class="fp-hero-media__placeholder"><span>Vimeo未設定</span></div>';
        }
    }

    echo '<span class="fp-hero-media__badge">' . esc_html($label) . '</span>';

    if ((int) $slot === 1) {
        echo '<div class="fp-hero-media__copy">';
        echo '<p class="fp-hero-media__copy-kicker">' . esc_html($fp_get('_fp_hero_kicker', '')) . '</p>';
        echo '<h2>' . esc_html($fp_one_line($fp_get('_fp_hero_title', ''))) . '</h2>';
        echo '<p class="fp-hero-media__copy-lead">' . esc_html($fp_one_line($fp_get('_fp_hero_lead', ''))) . '</p>';
        echo '<div class="fp-hero-media__copy-actions">';
        for ($i = 1; $i <= 4; $i++) {
            $btn_label = $fp_get("_fp_hero_btn_{$i}_label", '');
            $btn_url   = $fp_clean_url($fp_get("_fp_hero_btn_{$i}_url", ''));
            $btn_color = $fp_get("_fp_hero_btn_{$i}_color", 'blue');

            if ($btn_label === '') {
                continue;
            }

            echo '<a class="fp-btn fp-btn--' . esc_attr($btn_color) . '" href="' . esc_url($btn_url) . '">' . esc_html($btn_label) . '</a>';
        }
        echo '</div>';
        echo '</div>';
    }

    echo '</div>';
};

$service_cards = array(
    array(
        'key'   => 'realestate',
        'icon'  => '⌂',
        'title' => $fp_get('_fp_service_realestate_title', ''),
        'text'  => $fp_get('_fp_service_realestate_text', ''),
        'url'   => $fp_url('_fp_service_realestate_url', ''),
    ),
    array(
        'key'   => 'home',
        'icon'  => '⌂',
        'title' => $fp_get('_fp_service_home_title', ''),
        'text'  => $fp_get('_fp_service_home_text', ''),
        'url'   => $fp_url('_fp_service_home_url', ''),
    ),
    array(
        'key'   => 'stay',
        'icon'  => '▭',
        'title' => $fp_get('_fp_service_stay_title', ''),
        'text'  => $fp_get('_fp_service_stay_text', ''),
        'url'   => $fp_url('_fp_service_stay_url', ''),
    ),
    array(
        'key'   => 'business',
        'icon'  => '◎',
        'title' => $fp_get('_fp_service_business_title', ''),
        'text'  => $fp_get('_fp_service_business_text', ''),
        'url'   => $fp_url('_fp_service_business_url', ''),
    ),
);

$notice_items = array();
for ($i = 1; $i <= 3; $i++) {
    /*
     * お知らせ・PRカード
     *
     * 役割:
     * - フロント側に固定のデフォルト文言を書かない
     * - 管理画面で保存した値だけを読む
     * - タイトルや本文が空なら、選択URLの固定ページタイトル・抜粋を使う
     * - カスタム投稿をまだ作らない段階では、固定ページへのPR導線として使える
     */
    $notice_url = $fp_clean_url($fp_get("_fp_notice_{$i}_url", ''));
    $notice_page_id = $fp_url_to_post_id($notice_url);

    $custom_title = trim((string) $fp_get("_fp_notice_{$i}_title", ''));
    $custom_text  = trim((string) $fp_get("_fp_notice_{$i}_text", ''));

    $notice_title = $custom_title !== '' ? $custom_title : ($notice_page_id ? get_the_title($notice_page_id) : '');
    $notice_text  = $custom_text !== '' ? $custom_text : ($notice_page_id ? $fp_page_excerpt($notice_page_id) : '');

    $thumb_id = (int) $fp_get("_fp_notice_{$i}_thumb_id", 0);
    if (!$thumb_id && $notice_page_id) {
        $thumb_id = (int) get_post_thumbnail_id($notice_page_id);
    }

    $notice_items[] = array(
        'thumb_id' => $thumb_id,
        'date'     => $fp_get("_fp_notice_{$i}_date", ''),
        'label'    => $fp_get("_fp_notice_{$i}_label", ''),
        'title'    => $notice_title,
        'text'     => $notice_text,
        'url'      => $notice_url,
    );
}
?>

<main class="fp-portal">
    <section class="fp-hero">
        <div class="fp-shell fp-hero__grid">
            <div class="fp-hero__body">
                <p class="fp-eyebrow"><?php echo esc_html($fp_get('_fp_hero_kicker', '')); ?></p>
                <h1 class="fp-hero__title">
                    <?php echo esc_html($fp_one_line($fp_get('_fp_hero_title', ''))); ?>
                </h1>
                <p class="fp-hero__lead">
                    <?php echo esc_html($fp_one_line($fp_get('_fp_hero_lead', ''))); ?>
                </p>

                <div class="fp-hero__actions">
                    <?php
                    /* Hero CTAは管理画面で保存された値だけ表示する */
                    for ($i = 1; $i <= 4; $i++) :
                        $btn_label = $fp_get("_fp_hero_btn_{$i}_label", '');
                        $btn_url   = $fp_clean_url($fp_get("_fp_hero_btn_{$i}_url", ''));
                        $btn_color = $fp_get("_fp_hero_btn_{$i}_color", 'blue');
                        if ($btn_label === '') {
                            continue;
                        }
                    ?>
                        <a class="fp-btn fp-btn--<?php echo esc_attr($btn_color); ?>" href="<?php echo esc_url($btn_url); ?>">
                            <?php echo esc_html($btn_label); ?>
                        </a>
                    <?php endfor; ?>
                </div>
            </div>

            <?php
            /*
             * 上部メディア:
             * - 最大5枠
             * - 設定済みの枠だけ表示
             * - 表示枚数に応じて CSS クラスを付ける
             *
             * 例:
             * 2枚 → fp-hero-media--count-2
             * 3枚 → fp-hero-media--count-3
             * 4枚 → fp-hero-media--count-4
             * 5枚 → fp-hero-media--count-5
             */
            $fp_has_media_slot = function ($slot) use ($fp_get) {
                $type = $fp_get("_fp_hero_media_{$slot}_type", 'image');

                if ($type === 'image') {
                    return (int) $fp_get("_fp_hero_media_{$slot}_image_id", 0) > 0;
                }

                if ($type === 'mp4') {
                    return (int) $fp_get("_fp_hero_media_{$slot}_mp4_id", 0) > 0;
                }

                if ($type === 'youtube') {
                    return trim((string) $fp_get("_fp_hero_media_{$slot}_youtube", '')) !== '';
                }

                if ($type === 'vimeo') {
                    return trim((string) $fp_get("_fp_hero_media_{$slot}_vimeo", '')) !== '';
                }

                return false;
            };

            $fp_media_slots = array();

            for ($i = 1; $i <= 5; $i++) {
                if ($fp_has_media_slot($i)) {
                    $fp_media_slots[] = $i;
                }
            }

            $fp_media_count = min(count($fp_media_slots), 5);
            ?>

            <?php if (!empty($fp_media_slots)) : ?>
                <div class="fp-hero-media fp-hero-media--adaptive fp-hero-media--count-<?php echo esc_attr($fp_media_count); ?>" aria-label="フロントページメディア">
                    <?php
                    foreach ($fp_media_slots as $index => $slot) {
                        $render_media(
                            $slot,
                            $index === 0 ? 'fp-hero-media__item--main' : 'fp-hero-media__item--sub'
                        );
                    }
                    ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="fp-services">
        <div class="fp-shell fp-services__grid">
            <?php foreach ($service_cards as $card) : ?>
                <a class="fp-service-card fp-service-card--<?php echo esc_attr($card['key']); ?>" href="<?php echo esc_url($card['url']); ?>">
                    <span class="fp-service-card__icon" aria-hidden="true"><?php echo $fp_icon_svg($card['key']); ?></span>
                    <span class="fp-service-card__body">
                        <strong><?php echo esc_html($card['title']); ?></strong>
                        <small><?php echo esc_html($card['text']); ?></small>
                        <em>詳しく見る →</em>
                    </span>
                </a>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="fp-business">
        <div class="fp-shell fp-business__inner">
            <div class="fp-business__title">
                <span class="fp-business__icon" aria-hidden="true"><?php echo $fp_icon_svg('partner'); ?></span>
                <div>
                    <h2><?php echo esc_html($fp_get('_fp_business_title', '')); ?></h2>
                    <p><?php echo esc_html($fp_get('_fp_business_text', '')); ?></p>
                </div>
            </div>
            <ul class="fp-business__checks">
                <?php for ($i = 1; $i <= 4; $i++) : ?>
                    <?php $business_check = $fp_get("_fp_business_check_{$i}", ''); ?>
                    <?php if ($business_check !== '') : ?>
                        <li><?php echo esc_html($business_check); ?></li>
                    <?php endif; ?>
                <?php endfor; ?>
            </ul>
            <?php
            $fp_business_button_label = $fp_get('_fp_business_button_label', '');
            $fp_business_button_url   = $fp_clean_url($fp_get('_fp_business_button_url', ''));
            ?>
            <?php if ($fp_business_button_label !== '') : ?>
                <a class="fp-business__btn" href="<?php echo esc_url($fp_business_button_url); ?>"><?php echo esc_html($fp_business_button_label); ?></a>
            <?php endif; ?>
        </div>
    </section>

    <section class="fp-pickup">
        <div class="fp-shell">
            <h2 class="fp-section-title">ピックアップ</h2>

            <div class="fp-pickup__grid">
                <div class="fp-pickup-block">
                    <div class="fp-block-head">
                        <h3>おすすめ物件</h3>
                        <a href="<?php echo esc_url(home_url('/fudousan/')); ?>">すべて見る →</a>
                    </div>

                    <div class="fp-mini-cards">
                        <?php
                        /*
         * おすすめ物件
         * - 通常投稿 post のうち、naigai-tochi カテゴリーだけ取得
         * - 建物・中古戸建・LDK は出さない
         * - 表示枠は2つ
         */
                        $property_q = new WP_Query(array(
                            'post_type'           => 'post',
                            'category_name'       => 'naigai-tochi',
                            'posts_per_page'      => 2,
                            'post_status'         => 'publish',
                            'orderby'             => 'date',
                            'order'               => 'DESC',
                            'ignore_sticky_posts' => true,
                        ));

                        $property_count = 0;

                        if ($property_q->have_posts()) :
                            while ($property_q->have_posts()) :
                                $property_q->the_post();
                                $property_count++;
                        ?>
                                <a class="fp-mini-card" href="<?php the_permalink(); ?>">
                                    <span class="fp-mini-card__thumb">
                                        <?php if (has_post_thumbnail()) the_post_thumbnail('medium'); ?>
                                    </span>
                                    <strong><?php the_title(); ?></strong>
                                    <small>土地情報を見る</small>
                                </a>
                            <?php
                            endwhile;
                            wp_reset_postdata();
                        endif;

                        /*
         * 土地投稿が2件未満の場合だけ、空カードで2枠を維持する。
         * ここでも中古戸建・LDK・建物情報は出さない。
         */
                        for ($i = $property_count; $i < 2; $i++) :
                            ?>
                            <a class="fp-mini-card" href="<?php echo esc_url(home_url('/fudousan/')); ?>">
                                <span class="fp-mini-card__thumb fp-mini-card__thumb--empty"></span>
                                <strong>土地情報</strong>
                                <small>土地情報を見る</small>
                            </a>
                        <?php endfor; ?>
                    </div>
                </div>

                <div class="fp-pickup-block">
                    <div class="fp-block-head">
                        <h3>間取りプラン</h3>
                        <a href="<?php echo esc_url(home_url('/iezukuri/plans/')); ?>">すべて見る →</a>
                    </div>

                    <div class="fp-mini-cards">
                        <?php
                        /*
                     * 間取りプラン
                     * - /iezukuri/plans/ の一覧と同じ iez_plan を使う
                     * - フロントではランダムで2件だけ表示
                     * - レイアウトは既存の fp-mini-card のまま
                     */
                        $plan_q = new WP_Query(array(
                            'post_type'           => 'iez_plan',
                            'post_status'         => 'publish',
                            'posts_per_page'      => 2,
                            'orderby'             => 'rand',
                            'ignore_sticky_posts' => true,
                        ));

                        if ($plan_q->have_posts()) :
                            while ($plan_q->have_posts()) :
                                $plan_q->the_post();

                                $plan_id = get_the_ID();

                                /*
                             * 一覧・詳細側と同じ画像の拾い方
                             * 1. 外観写真
                             * 2. アイキャッチ
                             * 3. ギャラリー先頭
                             * 4. 1F平面図
                             */
                                $plan_thumb_id = (int) get_post_meta($plan_id, '_ch_plan_exterior_image_id', true);

                                if (!$plan_thumb_id && has_post_thumbnail($plan_id)) {
                                    $plan_thumb_id = (int) get_post_thumbnail_id($plan_id);
                                }

                                if (!$plan_thumb_id) {
                                    $gallery_raw = get_post_meta($plan_id, '_ch_plan_gallery_image_ids', true);
                                    $gallery_ids = array_values(array_filter(array_map('absint', explode(',', (string) $gallery_raw))));

                                    if (!empty($gallery_ids)) {
                                        $plan_thumb_id = (int) $gallery_ids[0];
                                    }
                                }

                                if (!$plan_thumb_id) {
                                    $plan_thumb_id = (int) get_post_meta($plan_id, '_ch_plan_1f_image_id', true);
                                }
                        ?>
                                <a class="fp-mini-card" href="<?php the_permalink(); ?>">
                                    <span class="fp-mini-card__thumb<?php echo !$plan_thumb_id ? ' fp-mini-card__thumb--empty' : ''; ?>">
                                        <?php
                                        if ($plan_thumb_id) {
                                            echo wp_get_attachment_image($plan_thumb_id, 'medium');
                                        }
                                        ?>
                                    </span>
                                    <strong><?php the_title(); ?></strong>
                                    <small>間取りを見る</small>
                                </a>
                            <?php
                            endwhile;
                            wp_reset_postdata();
                        else :
                            ?>
                            <a class="fp-mini-card" href="<?php echo esc_url(home_url('/iezukuri/plans/')); ?>">
                                <span class="fp-mini-card__thumb fp-mini-card__thumb--empty"></span>
                                <strong>間取りプラン一覧</strong>
                                <small>プランを見る</small>
                            </a>
                            <a class="fp-mini-card" href="<?php echo esc_url(home_url('/iezukuri/plans/')); ?>">
                                <span class="fp-mini-card__thumb fp-mini-card__thumb--empty"></span>
                                <strong>家づくりの参考プラン</strong>
                                <small>一覧を見る</small>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="fp-pickup-block fp-notices">
                    <div class="fp-block-head">
                        <h3>お知らせ・コラム</h3>
                        <a href="<?php echo esc_url(home_url('/')); ?>">すべて見る →</a>
                    </div>

                    <?php foreach ($notice_items as $item) : ?>
                        <a class="fp-notice" href="<?php echo esc_url($item['url'] ?: '#'); ?>">
                            <span class="fp-notice__thumb">
                                <?php
                                if ($item['thumb_id']) {
                                    echo wp_get_attachment_image($item['thumb_id'], 'thumbnail');
                                }
                                ?>
                            </span>
                            <span class="fp-notice__body">
                                <span class="fp-notice__meta">
                                    <?php echo esc_html($item['date']); ?>
                                    <em><?php echo esc_html($item['label']); ?></em>
                                </span>
                                <strong><?php echo esc_html($item['title']); ?></strong>
                                <small><?php echo esc_html($item['text']); ?></small>
                            </span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <?php
    $fp_life_kicker = trim((string) $fp_get('_fp_life_kicker', ''));
    $fp_life_title  = trim((string) $fp_get('_fp_life_title', ''));
    $fp_life_text   = trim((string) $fp_get('_fp_life_text', ''));
    $fp_life_button_label = trim((string) $fp_get('_fp_life_button_label', ''));

    /*
     * 那須での暮らし：左側CTAボタンURL
     *
     * 役割:
     * - 管理画面側の _fp_life_button_url をCTAリンクとして使う
     * - まだ保存されていない場合だけ /nasu-ideal-home/ をデフォルトにする
     * - 管理画面で「リンクなし」を選んだ場合はボタン自体を表示しない
     */
    $fp_life_button_url = $fp_clean_url($fp_get('_fp_life_button_url', home_url('/nasu-ideal-home/')));
    $fp_life_has_button = $fp_life_button_label !== '' && $fp_life_button_url !== '#';

    /*
     * Lifeカードを先に組み立てる
     *
     * 空判定の基準:
     * - タイトルが空
     * - 本文が空
     * - カード画像が未設定
     * この3つが全部空なら、そのカードは表示しない。
     *
     * 重要:
     * - リンク先URLは「コンテンツ」ではなく「クリック先」なので、空判定に入れない
     * - URLだけ選ばれていても、固定ページタイトル・抜粋・アイキャッチで勝手に補完しない
     * - 表示できるカードが0件なら、スライダー外枠・swiper-wrapper・矢印も出さない
     */
    $fp_life_points = array();

    for ($i = 1; $i <= 4; $i++) {
        $point_title    = trim((string) $fp_get("_fp_life_point_{$i}_title", ''));
        $point_text     = trim((string) $fp_get("_fp_life_point_{$i}_text", ''));
        $point_url      = $fp_clean_url($fp_get("_fp_life_point_{$i}_url", ''));
        $point_image_id = (int) $fp_get("_fp_life_point_{$i}_image_id", 0);

        if ($point_title === '' && $point_text === '' && !$point_image_id) {
            continue;
        }

        $fp_life_points[] = array(
            'title'    => $point_title,
            'text'     => $point_text,
            'url'      => $point_url,
            'image_id' => $point_image_id,
        );
    }

    $fp_life_has_text = $fp_life_kicker !== '' || $fp_life_title !== '' || $fp_life_text !== '' || $fp_life_has_button;
    $fp_life_has_points = !empty($fp_life_points);
    ?>

    <?php if ($fp_life_has_text || $fp_life_has_points) : ?>
        <section class="fp-life fp-life--cards-swiper">
            <div class="fp-shell fp-life__inner">
                <?php if ($fp_life_has_text) : ?>
                    <div class="fp-life__text">
                        <?php if ($fp_life_kicker !== '') : ?>
                            <p class="fp-eyebrow"><?php echo esc_html($fp_life_kicker); ?></p>
                        <?php endif; ?>

                        <?php if ($fp_life_title !== '') : ?>
                            <h2><?php echo esc_html($fp_life_title); ?></h2>
                        <?php endif; ?>

                        <?php if ($fp_life_text !== '') : ?>
                            <p><?php echo nl2br(esc_html($fp_life_text)); ?></p>
                        <?php endif; ?>

                        <?php if ($fp_life_has_button) : ?>
                            <a class="fp-btn fp-btn--outline" href="<?php echo esc_url($fp_life_button_url); ?>">
                                <?php echo esc_html($fp_life_button_label); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if ($fp_life_has_points) : ?>
                    <div class="fp-life__points-shell">
                        <div class="fp-life__points swiper js-fp-life-swiper">
                            <div class="swiper-wrapper">
                                <?php foreach ($fp_life_points as $point) : ?>
                                    <div class="swiper-slide">
                                        <?php if ($point['url'] !== '#') : ?>
                                            <a class="fp-life__point" href="<?php echo esc_url($point['url']); ?>">
                                            <?php else : ?>
                                                <article class="fp-life__point">
                                                <?php endif; ?>

                                                <?php if ($point['image_id']) : ?>
                                                    <span class="fp-life__point-image">
                                                        <?php echo wp_get_attachment_image($point['image_id'], 'large'); ?>
                                                    </span>
                                                <?php endif; ?>

                                                <span class="fp-life__point-body">
                                                    <?php if ($point['title'] !== '') : ?>
                                                        <strong><?php echo esc_html($point['title']); ?></strong>
                                                    <?php endif; ?>

                                                    <?php if ($point['text'] !== '') : ?>
                                                        <small><?php echo esc_html($point['text']); ?></small>
                                                    <?php endif; ?>
                                                </span>

                                                <?php if ($point['url'] !== '#') : ?>
                                            </a>
                                        <?php else : ?>
                                            </article>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <?php if (count($fp_life_points) > 1) : ?>
                                <button type="button" class="fp-life__nav fp-life__nav--prev" aria-label="前へ"></button>
                                <button type="button" class="fp-life__nav fp-life__nav--next" aria-label="次へ"></button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    <?php endif; ?>

    <?php
    $fp_cta_image_id  = (int) $fp_get('_fp_cta_image_id', 0);
    $fp_cta_image_url = $fp_cta_image_id ? wp_get_attachment_image_url($fp_cta_image_id, 'large') : '';
    /*
     * 最後のCTA見出し
     *
     * 役割:
     * - 管理画面で入力したCTA見出しを取得する
     * - 既にDBに改行入りで保存されていても、下の表示時に1行化する
     * - nl2br() は使わない。不要な <br> や段落感を出さない
     */
    $fp_cta_title     = $fp_get('_fp_cta_title', '');

    $fp_cta_btn1_label = $fp_get('_fp_cta_btn1_label', '');
    $fp_cta_btn1_url   = $fp_clean_url($fp_get('_fp_cta_btn1_url', ''));
    $fp_cta_btn2_label = $fp_get('_fp_cta_btn2_label', '');
    $fp_cta_btn2_url   = $fp_clean_url($fp_get('_fp_cta_btn2_url', ''));

    $fp_cta_classes = array('fp-cta');
    $fp_cta_classes[] = $fp_cta_image_url ? 'fp-cta--has-bg-image' : 'fp-cta--no-image';

    $fp_cta_style = '';
    if ($fp_cta_image_url) {
        $fp_cta_style = ' style="--fp-cta-bg-image: url(' . esc_url($fp_cta_image_url) . ');"';
    }
    ?>

    <section class="<?php echo esc_attr(implode(' ', $fp_cta_classes)); ?>" <?php echo $fp_cta_style; ?>>
        <div class="fp-shell fp-cta__inner">
            <div class="fp-cta__body">
                <?php if ($fp_cta_title !== '') : ?>
                    <?php
                    /*
                     * CTA見出し表示
                     * - $fp_one_line() で改行・連続スペースを1つの半角スペースにまとめる
                     * - これで「暮らし・不動産...」と「お気軽に...」が別段落のように割れない
                     */
                    ?>
                    <h2><?php echo esc_html($fp_one_line($fp_cta_title)); ?></h2>
                <?php endif; ?>

                <div class="fp-cta__actions">
                    <?php if ($fp_cta_btn1_label !== '') : ?>
                        <a class="fp-btn fp-btn--blue" href="<?php echo esc_url($fp_cta_btn1_url); ?>">
                            <?php echo esc_html($fp_cta_btn1_label); ?>
                        </a>
                    <?php endif; ?>

                    <?php if ($fp_cta_btn2_label !== '') : ?>
                        <a class="fp-btn fp-btn--outline" href="<?php echo esc_url($fp_cta_btn2_url); ?>">
                            <?php echo esc_html($fp_cta_btn2_label); ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</main>