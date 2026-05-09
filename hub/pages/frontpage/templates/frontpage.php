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
    $v = get_post_meta($post_id, $key, true);
    return ($v !== '' && $v !== null) ? $v : $default;
};

$fp_url = function ($key, $default = '') use ($fp_get) {
    $v = $fp_get($key, $default);
    return $v ? esc_url($v) : '#';
};

$fp_icon_svg = function ($key) {
    $icons = array(
        // 不動産: 土地 + 住宅
        'realestate' => '<svg viewBox="0 0 64 64" aria-hidden="true"><path d="M10 47h44"/><path d="M14 47V34l11-9 11 9v13"/><path d="M23 47V38h5v9"/><path d="M18 31l7-6 7 6"/><path d="M40 47V28h12v19"/><path d="M43 34h6"/><path d="M43 40h6"/><path d="M8 54c6-4 11-4 17 0s11 4 17 0 10-4 14 0"/><path d="M11 31l10-6"/></svg>',

        // 家づくり: 家
        'home' => '<svg viewBox="0 0 64 64" aria-hidden="true"><path d="M10 31L32 13l22 18"/><path d="M18 29v24h28V29"/><path d="M28 53V40h8v13"/><path d="M42 18V10h7v15"/><path d="M24 35h5"/><path d="M35 35h5"/></svg>',

        // 民泊・住宅宿泊: ベッド
        'stay' => '<svg viewBox="0 0 64 64" aria-hidden="true"><path d="M10 37h44"/><path d="M12 25h17v12H12z"/><path d="M29 30h19a6 6 0 0 1 6 6v1H29z"/><path d="M10 21v28"/><path d="M54 37v12"/><path d="M15 49v-5"/><path d="M49 49v-5"/></svg>',

        // 法人: 握手
        'business' => '<svg viewBox="0 0 64 64" aria-hidden="true"><path d="M23 35l9-9 7 7a5 5 0 0 0 7 0l3-3"/><path d="M18 29l10-10 8 4 8-4 10 10"/><path d="M8 27l9-9 9 9-9 9z"/><path d="M56 27l-9-9-9 9 9 9z"/><path d="M24 40l6 6"/><path d="M31 40l6 6"/><path d="M38 39l5 5"/></svg>',

        // 事業用途・パートナー開発: ビル + 握手
        'partner' => '<svg viewBox="0 0 64 64" aria-hidden="true"><path d="M9 52h46"/><path d="M13 52V16h18v36"/><path d="M31 52V26h12v26"/><path d="M18 22h4"/><path d="M18 29h4"/><path d="M18 36h4"/><path d="M36 32h3"/><path d="M36 39h3"/><path d="M23 47l7-7 5 5a4 4 0 0 0 6 0l2-2"/><path d="M19 43l7-7 6 3 6-3 7 7"/><path d="M16 42l5-5 5 5-5 5z"/><path d="M48 42l-5-5-5 5 5 5z"/></svg>',
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

$render_media = function ($slot, $class = '') use ($post_id, $fp_get, $extract_youtube_id, $extract_vimeo_id) {
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
        echo '<p class="fp-hero-media__copy-kicker">' . esc_html($fp_get('_fp_hero_kicker', 'NASU GROUP')) . '</p>';
        echo '<h2>' . nl2br(esc_html($fp_get('_fp_hero_title', "那須で暮らす・建てる・泊まる。\n住まいも、事業も。\nまるごと相談できる総合窓口"))) . '</h2>';
        echo '<p class="fp-hero-media__copy-lead">' . nl2br(esc_html($fp_get('_fp_hero_lead', "不動産探しから家づくり、民泊のご相談まで。\n那須の暮らしをもっと豊かに、もっと自由に。"))) . '</p>';
        echo '<div class="fp-hero-media__copy-actions">';
        echo '<a class="fp-btn fp-btn--blue" href="' . esc_url(home_url('/fudousan/')) . '">不動産を見る</a>';
        echo '<a class="fp-btn fp-btn--green" href="' . esc_url(home_url('/iezukuri/')) . '">家づくりを見る</a>';
        echo '<a class="fp-btn fp-btn--gold" href="' . esc_url(home_url('/minpaku/')) . '">宿泊を探す</a>';
        echo '<a class="fp-btn fp-btn--outline" href="' . esc_url(home_url('/contact/')) . '">お問い合わせ</a>';
        echo '</div>';
        echo '</div>';
    }

    echo '</div>';
};

$service_cards = array(
    array(
        'key'   => 'realestate',
        'icon'  => '⌂',
        'title' => $fp_get('_fp_service_realestate_title', '不動産の窓口'),
        'text'  => $fp_get('_fp_service_realestate_text', '土地や中古住宅の購入・売却、住み替えまで住まい探しをトータルサポート。'),
        'url'   => $fp_url('_fp_service_realestate_url', home_url('/fudousan/')),
    ),
    array(
        'key'   => 'home',
        'icon'  => '⌂',
        'title' => $fp_get('_fp_service_home_title', '家づくりの窓口'),
        'text'  => $fp_get('_fp_service_home_text', '注文住宅・リノベーションなど理想の住まいをカタチにします。'),
        'url'   => $fp_url('_fp_service_home_url', home_url('/iezukuri/')),
    ),
    array(
        'key'   => 'stay',
        'icon'  => '▭',
        'title' => $fp_get('_fp_service_stay_title', '民泊・住宅宿泊'),
        'text'  => $fp_get('_fp_service_stay_text', '那須の魅力を体験できる宿泊施設の運営や、住宅宿泊・民泊運用相談を承ります。'),
        'url'   => $fp_url('_fp_service_stay_url', home_url('/minpaku/')),
    ),
    array(
        'key'   => 'business',
        'icon'  => '◎',
        'title' => $fp_get('_fp_service_business_title', '法人向けのご相談'),
        'text'  => $fp_get('_fp_service_business_text', '土地活用・建物活用・事業用途のご提案やパートナー相談まで、事業の可能性を広げます。'),
        'url'   => $fp_url('_fp_service_business_url', home_url('/contact/')),
    ),
);

$notice_items = array();
for ($i = 1; $i <= 3; $i++) {
    $notice_items[] = array(
        'thumb_id' => (int) $fp_get("_fp_notice_{$i}_thumb_id", 0),
        'date'     => $fp_get("_fp_notice_{$i}_date", $i === 1 ? '2024.05.10' : ($i === 2 ? '2024.04.20' : '2024.04.05')),
        'label'    => $fp_get("_fp_notice_{$i}_label", $i === 2 ? 'コラム' : 'お知らせ'),
        'title'    => $fp_get("_fp_notice_{$i}_title", $i === 1 ? 'ゴールデンウィーク休業のお知らせ' : ($i === 2 ? '那須での暮らしを楽しむ、春の過ごし方' : '民泊運営セミナーを開催しました')),
        'text'     => $fp_get("_fp_notice_{$i}_text", 'フロントページ用の簡易お知らせ本文を管理画面から入力できます。'),
        'url'      => $fp_get("_fp_notice_{$i}_url", '#'),
    );
}
?>

<main class="fp-portal">
    <section class="fp-hero">
        <div class="fp-shell fp-hero__grid">
            <div class="fp-hero__body">
                <p class="fp-eyebrow"><?php echo esc_html($fp_get('_fp_hero_kicker', 'NASU GROUP')); ?></p>
                <h1 class="fp-hero__title">
                    <?php echo nl2br(esc_html($fp_get('_fp_hero_title', "那須で暮らす・建てる・泊まる。\n住まいも、事業も。\nまるごと相談できる総合窓口"))); ?>
                </h1>
                <p class="fp-hero__lead">
                    <?php echo nl2br(esc_html($fp_get('_fp_hero_lead', "不動産探しから家づくり、民泊のご相談まで。\n那須の暮らしをもっと豊かに、もっと自由に。"))); ?>
                </p>

                <div class="fp-hero__actions">
                    <?php
                    $fp_hero_btn_defaults = array(
                        1 => array('不動産を見る', home_url('/fudousan/'), 'blue'),
                        2 => array('家づくりを見る', home_url('/iezukuri/'), 'green'),
                        3 => array('宿泊を探す', home_url('/minpaku/'), 'gold'),
                        4 => array('お問い合わせ', home_url('/contact/'), 'outline'),
                    );

                    for ($i = 1; $i <= 4; $i++) :
                        $btn_label = $fp_get("_fp_hero_btn_{$i}_label", $fp_hero_btn_defaults[$i][0]);
                        $btn_url   = $fp_get("_fp_hero_btn_{$i}_url", $fp_hero_btn_defaults[$i][1]);
                        $btn_color = $fp_get("_fp_hero_btn_{$i}_color", $fp_hero_btn_defaults[$i][2]);
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

            /*
             * 何も設定されていない場合だけ、メディア1をプレースホルダーとして出す。
             */
            if (empty($fp_media_slots)) {
                $fp_media_slots[] = 1;
            }

            $fp_media_count = min(count($fp_media_slots), 5);
            ?>

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
                    <h2><?php echo esc_html($fp_get('_fp_business_title', '事業用途・パートナー開発もサポート')); ?></h2>
                    <p><?php echo esc_html($fp_get('_fp_business_text', '土地の使い方や建物の活用方法に応じて、事業化の企画から設計・運営、パートナー・協業までワンストップで支援します。')); ?></p>
                </div>
            </div>
            <ul class="fp-business__checks">
                <li>土地活用のご提案</li>
                <li>建物活用のご提案</li>
                <li>事業用途の企画・設計</li>
                <li>パートナー・協業のご相談</li>
            </ul>
            <a class="fp-business__btn" href="<?php echo esc_url(home_url('/contact/')); ?>">法人向けのご相談はこちら →</a>
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
                        $property_q = new WP_Query(array(
                            'post_type'      => array('house', 'post'),
                            'posts_per_page' => 2,
                            'post_status'    => 'publish',
                            'ignore_sticky_posts' => true,
                        ));
                        if ($property_q->have_posts()) :
                            while ($property_q->have_posts()) :
                                $property_q->the_post();
                                ?>
                                <a class="fp-mini-card" href="<?php the_permalink(); ?>">
                                    <span class="fp-mini-card__thumb">
                                        <?php if (has_post_thumbnail()) the_post_thumbnail('medium'); ?>
                                    </span>
                                    <strong><?php the_title(); ?></strong>
                                    <small>詳しく見る</small>
                                </a>
                                <?php
                            endwhile;
                            wp_reset_postdata();
                        else :
                            for ($i = 1; $i <= 2; $i++) :
                                ?>
                                <a class="fp-mini-card" href="<?php echo esc_url(home_url('/fudousan/')); ?>">
                                    <span class="fp-mini-card__thumb fp-mini-card__thumb--empty"></span>
                                    <strong><?php echo $i === 1 ? '那須町 中古戸建' : '那須塩原市 土地'; ?></strong>
                                    <small><?php echo $i === 1 ? '2,980万円 / 3LDK' : '1,250万円 / 土地'; ?></small>
                                </a>
                                <?php
                            endfor;
                        endif;
                        ?>
                    </div>
                </div>

                <div class="fp-pickup-block">
                    <div class="fp-block-head">
                        <h3>施工事例</h3>
                        <a href="<?php echo esc_url(home_url('/iezukuri/')); ?>">すべて見る →</a>
                    </div>
                    <div class="fp-mini-cards">
                        <a class="fp-mini-card" href="<?php echo esc_url(home_url('/iezukuri/')); ?>">
                            <span class="fp-mini-card__thumb fp-mini-card__thumb--empty"></span>
                            <strong>大きな窓とつながる平屋</strong>
                            <small>那須町 / 注文住宅</small>
                        </a>
                        <a class="fp-mini-card" href="<?php echo esc_url(home_url('/iezukuri/')); ?>">
                            <span class="fp-mini-card__thumb fp-mini-card__thumb--empty"></span>
                            <strong>自然と調和する木の家</strong>
                            <small>那須町 / 注文住宅</small>
                        </a>
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
    $fp_life_kicker = $fp_get('_fp_life_kicker', 'NASU LIFE');
    $fp_life_title  = $fp_get('_fp_life_title', '那須での暮らし');
    $fp_life_text   = $fp_get('_fp_life_text', '豊かな自然に囲まれ、四季の移ろいを感じられる那須。子育て世代からセカンドライフまで、自分らしい暮らし方が見つかります。');
    $fp_life_button_label = $fp_get('_fp_life_button_label', '那須の魅力をもっと見る');
    $fp_life_button_url   = $fp_get('_fp_life_button_url', home_url('/'));

    /*
     * Lifeカード:
     * 現状の管理画面は _fp_life_point_1〜3 を使う。
     * 将来 _fp_life_cards_json を追加した場合はそちらを優先できるようにしておく。
     */
    $fp_life_points = array();

    $fp_life_cards_json = $fp_get('_fp_life_cards_json', '');
    $fp_life_cards_data = $fp_life_cards_json ? json_decode($fp_life_cards_json, true) : array();

    if (is_array($fp_life_cards_data) && !empty($fp_life_cards_data)) {
        foreach ($fp_life_cards_data as $card) {
            if (!is_array($card)) {
                continue;
            }

            $fp_life_points[] = array(
                'title'    => isset($card['title']) ? (string) $card['title'] : '',
                'text'     => isset($card['text']) ? (string) $card['text'] : '',
                'image_id' => isset($card['image_id']) ? (int) $card['image_id'] : 0,
            );
        }
    }

    if (empty($fp_life_points)) {
        $fp_life_point_defaults = array(
            1 => array('自然と共に暮らす', '美しい自然を身近に感じられる環境。'),
            2 => array('アクセスも快適', '週末利用や拠点づくりにも対応。'),
            3 => array('家族にやさしい環境', '安心して暮らせる地域環境。'),
        );

        for ($i = 1; $i <= 3; $i++) {
            $fp_life_points[] = array(
                'title'    => $fp_get("_fp_life_point_{$i}_title", $fp_life_point_defaults[$i][0]),
                'text'     => $fp_get("_fp_life_point_{$i}_text", $fp_life_point_defaults[$i][1]),
                'image_id' => (int) $fp_get("_fp_life_point_{$i}_image_id", 0),
            );
        }
    }
    ?>

    <section class="fp-life fp-life--cards-swiper">
        <div class="fp-shell fp-life__inner">
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

                <?php if ($fp_life_button_label !== '') : ?>
                    <a class="fp-btn fp-btn--outline" href="<?php echo esc_url($fp_life_button_url); ?>">
                        <?php echo esc_html($fp_life_button_label); ?>
                    </a>
                <?php endif; ?>
            </div>

            <div class="fp-life__points-shell">
                <div class="fp-life__points swiper js-fp-life-swiper">
                    <div class="swiper-wrapper">
                        <?php foreach ($fp_life_points as $point) : ?>
                            <div class="swiper-slide">
                                <article class="fp-life__point">
                                    <?php if (!empty($point['image_id'])) : ?>
                                        <span class="fp-life__point-image">
                                            <?php echo wp_get_attachment_image((int) $point['image_id'], 'large'); ?>
                                        </span>
                                    <?php endif; ?>

                                    <span class="fp-life__point-body">
                                        <?php if (!empty($point['title'])) : ?>
                                            <strong><?php echo esc_html($point['title']); ?></strong>
                                        <?php endif; ?>

                                        <?php if (!empty($point['text'])) : ?>
                                            <small><?php echo esc_html($point['text']); ?></small>
                                        <?php endif; ?>
                                    </span>
                                </article>
                            </div>
                        <?php endforeach; ?>
                    </div>
<button type="button" class="fp-life__nav fp-life__nav--prev" aria-label="前へ"></button>
                    <button type="button" class="fp-life__nav fp-life__nav--next" aria-label="次へ"></button>
                </div>
            </div>
        </div>
    </section>

    <?php
    $fp_cta_image_id  = (int) $fp_get('_fp_cta_image_id', 0);
    $fp_cta_image_url = $fp_cta_image_id ? wp_get_attachment_image_url($fp_cta_image_id, 'large') : '';
    $fp_cta_title     = $fp_get('_fp_cta_title', "暮らし・不動産・家づくり・宿泊・事業まで\nお気軽にご相談ください");

    $fp_cta_btn1_label = $fp_get('_fp_cta_btn1_label', 'お問い合わせ');
    $fp_cta_btn1_url   = $fp_get('_fp_cta_btn1_url', home_url('/contact/'));
    $fp_cta_btn2_label = $fp_get('_fp_cta_btn2_label', '来店予約');
    $fp_cta_btn2_url   = $fp_get('_fp_cta_btn2_url', home_url('/reservation/'));

    $fp_cta_classes = array('fp-cta');
    $fp_cta_classes[] = $fp_cta_image_url ? 'fp-cta--has-bg-image' : 'fp-cta--no-image';

    $fp_cta_style = '';
    if ($fp_cta_image_url) {
        $fp_cta_style = ' style="--fp-cta-bg-image: url(' . esc_url($fp_cta_image_url) . ');"';
    }
    ?>

    <section class="<?php echo esc_attr(implode(' ', $fp_cta_classes)); ?>"<?php echo $fp_cta_style; ?>>
        <div class="fp-shell fp-cta__inner">
            <div class="fp-cta__body">
                <?php if ($fp_cta_title !== '') : ?>
                    <h2><?php echo nl2br(esc_html($fp_cta_title)); ?></h2>
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
