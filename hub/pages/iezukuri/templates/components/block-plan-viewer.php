<?php

/**
 * 家づくりプラン詳細
 *
 * 正しい表示順:
 * 1段目: 外観写真
 * 2段目: 左 平面図/配置図 + 右 プラン概要
 * 3段目: 操作ボタン
 * 4段目: 複数内装写真
 * 5段目: 住宅の特徴・見どころ
 */

if (!defined('ABSPATH')) {
    exit;
}

$page_id = get_queried_object_id();

if (!$page_id) {
    return;
}

$plan_label         = get_post_meta($page_id, '_ch_plan_label', true) ?: '平屋 P-A';
$plan_name          = get_post_meta($page_id, '_ch_plan_name', true) ?: get_the_title($page_id);
$plan_style         = get_post_meta($page_id, '_ch_plan_style', true) ?: '洋風';
$plan_layout        = get_post_meta($page_id, '_ch_plan_layout', true) ?: '2LDK';
$plan_total_area    = get_post_meta($page_id, '_ch_plan_total_area', true) ?: '72㎡';
$plan_tsubo         = get_post_meta($page_id, '_ch_plan_tsubo', true) ?: '約21.8坪';
$plan_building_area = get_post_meta($page_id, '_ch_plan_building_area', true) ?: '75㎡';
$plan_family        = get_post_meta($page_id, '_ch_plan_family', true) ?: '夫婦・少人数世帯';
$plan_description   = get_post_meta($page_id, '_ch_plan_description', true) ?: '暮らしやすさをコンパクトにまとめた平屋プランです。家事動線、収納、採光、庭とのつながりを確認できます。';

$floor_type = get_post_meta($page_id, '_ch_plan_floor_type', true) ?: 'one_story';

$exterior_id = (int) get_post_meta($page_id, '_ch_plan_exterior_image_id', true);
$plan_1f_id  = (int) get_post_meta($page_id, '_ch_plan_1f_image_id', true);
$plan_2f_id  = (int) get_post_meta($page_id, '_ch_plan_2f_image_id', true);
$site_id     = (int) get_post_meta($page_id, '_ch_plan_site_image_id', true);
$pdf_id      = (int) get_post_meta($page_id, '_ch_plan_pdf_id', true);
$pdf_url     = $pdf_id ? wp_get_attachment_url($pdf_id) : '';

/**
 * PDFダウンロードURL。
 *
 * 優先順位:
 * 1. 編集画面で選択したPDF
 * 2. 自動生成PDF /uploads/iezukuri-pdf/{slug}.pdf
 * 3. 保存済みURL _ch_plan_pdf_url
 */
$plan_slug          = get_post_field('post_name', $page_id);
$selected_pdf_url   = '';
$generated_pdf_url  = '';
$generated_pdf_path = '';
$saved_pdf_url      = trim((string) get_post_meta($page_id, '_ch_plan_pdf_url', true));

if ($pdf_id && get_post_mime_type($pdf_id) === 'application/pdf') {
    $selected_pdf_url = wp_get_attachment_url($pdf_id);
}

if ($plan_slug) {
    $upload_dir = wp_upload_dir();

    if (empty($upload_dir['error'])) {
        $generated_pdf_path = trailingslashit($upload_dir['basedir']) . 'iezukuri-pdf/' . $plan_slug . '.pdf';
    }

    $generated_pdf_url = home_url('/wp-content/uploads/iezukuri-pdf/' . rawurlencode($plan_slug) . '.pdf');
}

$has_generated_pdf = (
    $generated_pdf_path &&
    file_exists($generated_pdf_path) &&
    filesize($generated_pdf_path) > 0
);

$pdf_download_url = '';

if ($selected_pdf_url) {
    $pdf_download_url = $selected_pdf_url;
} elseif ($has_generated_pdf) {
    $pdf_download_url = $generated_pdf_url;
} elseif ($saved_pdf_url) {
    $pdf_download_url = $saved_pdf_url;
}

$gallery_raw = get_post_meta($page_id, '_ch_plan_gallery_image_ids', true);
$gallery_ids = array_values(array_filter(array_map('absint', explode(',', (string) $gallery_raw))));

if (!$exterior_id && has_post_thumbnail($page_id)) {
    $exterior_id = (int) get_post_thumbnail_id($page_id);
}

if (!$exterior_id && !empty($gallery_ids)) {
    $exterior_id = (int) $gallery_ids[0];
}

if (!$exterior_id && $plan_1f_id) {
    $exterior_id = $plan_1f_id;
}

$tabs = array();

if ($floor_type === 'two_story') {
    if ($plan_1f_id) {
        $tabs[] = array(
            'key'      => '1f',
            'label'    => '1F',
            'title'    => '1階 平面図',
            'image_id' => $plan_1f_id,
        );
    }

    if ($plan_2f_id) {
        $tabs[] = array(
            'key'      => '2f',
            'label'    => '2F',
            'title'    => '2階 平面図',
            'image_id' => $plan_2f_id,
        );
    }
} else {
    if ($plan_1f_id) {
        $tabs[] = array(
            'key'      => '1f',
            'label'    => '平面図',
            'title'    => '平面図',
            'image_id' => $plan_1f_id,
        );
    }
}

if ($site_id) {
    $tabs[] = array(
        'key'      => 'site',
        'label'    => '配置図',
        'title'    => '配置図',
        'image_id' => $site_id,
    );
}

if (!function_exists('naigai_iez_plan_detail_img_url')) {
    function naigai_iez_plan_detail_img_url($image_id, $size = 'full')
    {
        $image_id = absint($image_id);

        if (!$image_id) {
            return '';
        }

        $url = wp_get_attachment_image_url($image_id, $size);

        return $url ? $url : '';
    }
}

if (!function_exists('naigai_iez_plan_detail_icon_key')) {
    /**
     * 住宅の特徴タイトルからアイコン種類を自動判定する。
     *
     * 管理画面は今まで通り「見出し｜説明文」で入力。
     * アイコンはタイトル名から自動で変える。
     */
    function naigai_iez_plan_detail_icon_key($title)
    {
        $title = (string) $title;

        if (strpos($title, '耐震') !== false || strpos($title, '構造') !== false) {
            return 'shield';
        }

        if (strpos($title, '基礎') !== false) {
            return 'foundation';
        }

        if (strpos($title, '屋根') !== false) {
            return 'roof';
        }

        if (strpos($title, '壁') !== false || strpos($title, '外装') !== false || strpos($title, '外壁') !== false) {
            return 'wall';
        }

        if (strpos($title, 'サッシ') !== false || strpos($title, '窓') !== false) {
            return 'window';
        }

        if (strpos($title, '寒冷') !== false || strpos($title, '断熱') !== false || strpos($title, '雪') !== false) {
            return 'snow';
        }

        if (strpos($title, 'バス') !== false || strpos($title, '浴室') !== false || strpos($title, '風呂') !== false) {
            return 'bath';
        }

        if (strpos($title, 'トイレ') !== false) {
            return 'toilet';
        }

        if (strpos($title, 'バリアフリー') !== false || strpos($title, '段差') !== false) {
            return 'accessibility';
        }

        if (strpos($title, 'ウッドデッキ') !== false || strpos($title, 'デッキ') !== false || strpos($title, '庭') !== false) {
            return 'deck';
        }

        if (strpos($title, '駐車') !== false || strpos($title, '車') !== false) {
            return 'car';
        }

        if (strpos($title, '収納') !== false) {
            return 'storage';
        }

        return 'home';
    }
}

if (!function_exists('naigai_iez_plan_detail_icon_svg')) {
    /**
     * プラン詳細「住宅の特徴」用SVG。
     *
     * 外部サイト/CDNは参照しない。
     * PDF化・ローカル・本番で安定させるため、inline SVGで出力する。
     */
    function naigai_iez_plan_detail_icon_svg($icon)
    {
        $icon = sanitize_key($icon);

        $icons = array(
            'shield' => '<svg viewBox="0 0 48 48" aria-hidden="true"><path d="M24 6 12 11v10c0 9 5.8 15.7 12 18 6.2-2.3 12-9 12-18V11L24 6Z"></path><path d="m18.5 24 4 4 8-9"></path></svg>',

            'foundation' => '<svg viewBox="0 0 48 48" aria-hidden="true"><path d="M9 35h30"></path><path d="M12 29h24v6H12z"></path><path d="M16 23h16v6H16z"></path><path d="M20 17h8v6h-8z"></path><path d="M14 39h20"></path></svg>',

            'roof' => '<svg viewBox="0 0 48 48" aria-hidden="true"><path d="M6 24 24 9l18 15"></path><path d="M12 22v17h24V22"></path><path d="M19 39V27h10v12"></path></svg>',

            'wall' => '<svg viewBox="0 0 48 48" aria-hidden="true"><path d="M9 13h30v24H9z"></path><path d="M9 21h30M9 29h30"></path><path d="M18 13v8M30 13v8M15 21v8M27 21v8M21 29v8M33 29v8"></path></svg>',

            'window' => '<svg viewBox="0 0 48 48" aria-hidden="true"><path d="M12 9h24v30H12z"></path><path d="M24 9v30M12 24h24"></path><path d="M16 43h16"></path></svg>',

            'snow' => '<svg viewBox="0 0 48 48" aria-hidden="true"><path d="M24 6v36M10 14l28 20M38 14 10 34"></path><path d="m18 9 6 6 6-6M18 39l6-6 6 6M8 24h8M32 24h8"></path></svg>',

            'bath' => '<svg viewBox="0 0 48 48" aria-hidden="true"><path d="M10 25h30v5a9 9 0 0 1-9 9H19a9 9 0 0 1-9-9v-5Z"></path><path d="M15 25V13a5 5 0 0 1 5-5h2"></path><path d="M22 12h7M18 39l-2 4M32 39l2 4"></path></svg>',

            'toilet' => '<svg viewBox="0 0 48 48" aria-hidden="true"><path d="M16 8h16v14H16z"></path><path d="M14 22h20v4a9 9 0 0 1-9 9h-2a9 9 0 0 1-9-9v-4Z"></path><path d="M20 35h8l2 7H18l2-7Z"></path></svg>',

            'accessibility' => '<svg viewBox="0 0 48 48" aria-hidden="true"><circle cx="24" cy="9" r="4"></circle><path d="M14 19h20"></path><path d="M24 13v12"></path><path d="M18 42l4-17"></path><path d="M30 42l-4-17"></path></svg>',

            'deck' => '<svg viewBox="0 0 48 48" aria-hidden="true"><path d="M8 31h32"></path><path d="M11 24h26v7H11z"></path><path d="M14 31v8M24 31v8M34 31v8"></path><path d="M12 19c5-6 14-7 22-2"></path></svg>',

            'car' => '<svg viewBox="0 0 48 48" aria-hidden="true"><path d="M12 26l3-9h18l3 9"></path><path d="M10 26h28v10H10z"></path><circle cx="17" cy="36" r="3"></circle><circle cx="31" cy="36" r="3"></circle><path d="M14 26h20"></path></svg>',

            'storage' => '<svg viewBox="0 0 48 48" aria-hidden="true"><path d="M12 14h24v26H12z"></path><path d="M16 8h16v6H16z"></path><path d="M18 24h12M18 31h8"></path></svg>',

            'home' => '<svg viewBox="0 0 48 48" aria-hidden="true"><path d="M7 24 24 10l17 14"></path><path d="M13 22v18h22V22"></path><path d="M20 40V29h8v11"></path></svg>',
        );

        return isset($icons[$icon]) ? $icons[$icon] : $icons['home'];
    }
}

$feature_lines_raw = get_post_meta($page_id, '_ch_plan_marketing_features', true);

if ($feature_lines_raw === '') {
    $feature_lines_raw = implode("\n", array(
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
    ));
}

$features = array();

foreach (preg_split('/\r\n|\r|\n/', $feature_lines_raw) as $line) {
    $line = trim($line);

    if ($line === '') {
        continue;
    }

    $parts = array_map('trim', explode('｜', $line, 3));

    $features[] = array(
        'icon'  => naigai_iez_plan_detail_icon_key($parts[0]),
        'title' => $parts[0],
        'text'  => isset($parts[1]) ? $parts[1] : '',
    );
}

$lightbox = array();

if ($exterior_id && naigai_iez_plan_detail_img_url($exterior_id)) {
    $lightbox[] = array(
        'src'   => naigai_iez_plan_detail_img_url($exterior_id),
        'title' => '外観写真',
    );
}

foreach ($tabs as $tab) {
    $url = naigai_iez_plan_detail_img_url($tab['image_id']);

    if ($url) {
        $lightbox[] = array(
            'src'   => $url,
            'title' => $tab['title'],
        );
    }
}

foreach ($gallery_ids as $gallery_id) {
    $url = naigai_iez_plan_detail_img_url($gallery_id);
    $alt = get_post_meta($gallery_id, '_wp_attachment_image_alt', true);

    if ($url) {
        $lightbox[] = array(
            'src'   => $url,
            'title' => $alt !== '' ? $alt : '内装写真',
        );
    }
}
?>

<section class="iez-block iez-plan-detail" id="plan-viewer">
    <div class="iez-block__inner iez-plan-detail__inner">

        <article
            class="iez-plan-detail-board"
            data-iez-plan-card
            data-iez-plan-lightbox="<?php echo esc_attr(wp_json_encode($lightbox)); ?>">
            <header class="iez-plan-detail-board__header">
                <p class="iez-block__kicker">FLOOR PLAN</p>
                <h2 class="iez-block__title">間取りから、住まいの大きさを確認できます。</h2>
                <p class="iez-block__lead">
                    外観、平面図、配置図、延床面積、坪数、暮らし方の目安をまとめて確認できます。
                </p>
            </header>

            <!-- 1段目: 外観写真 -->
            <section class="iez-plan-detail-board__row iez-plan-detail-board__row--exterior" aria-label="外観写真">
                <?php if ($exterior_id && naigai_iez_plan_detail_img_url($exterior_id, 'large')) : ?>
                    <button
                        class="iez-plan-detail-board__exterior-button iez-plan-zoom-button"
                        type="button"
                        data-iez-plan-open
                        data-plan-src="<?php echo esc_url(naigai_iez_plan_detail_img_url($exterior_id)); ?>"
                        data-plan-title="外観写真">
                        <span class="iez-plan-detail-board__badge">外観写真</span>
                        <img
                            src="<?php echo esc_url(naigai_iez_plan_detail_img_url($exterior_id, 'large')); ?>"
                            alt="<?php echo esc_attr($plan_name); ?> 外観写真">
                    </button>
                <?php else : ?>
                    <div class="iez-plan-detail-board__empty">外観写真は未設定です</div>
                <?php endif; ?>
            </section>

            <!-- 2段目: 左 平面図 / 右 概要欄 -->
            <section class="iez-plan-detail-board__row iez-plan-detail-board__row--plan-summary" aria-label="平面図とプラン概要">
                <div class="iez-plan-detail-board__drawing" data-iez-plan-drawing-root>
                    <?php if (!empty($tabs)) : ?>
                        <div class="iez-plan-detail-board__tabs is-count-<?php echo esc_attr(max(1, min(4, count($tabs)))); ?>">
                            <?php foreach ($tabs as $index => $tab) : ?>
                                <button
                                    class="iez-plan-detail-board__tab<?php echo $index === 0 ? ' is-active' : ''; ?>"
                                    type="button"
                                    data-iez-plan-tab="<?php echo esc_attr($tab['key']); ?>">
                                    <?php echo esc_html($tab['label']); ?>
                                </button>
                            <?php endforeach; ?>
                        </div>

                        <div class="iez-plan-detail-board__panels">
                            <?php foreach ($tabs as $index => $tab) : ?>
                                <?php
                                $draw_url = naigai_iez_plan_detail_img_url($tab['image_id'], 'large');
                                $full_url = naigai_iez_plan_detail_img_url($tab['image_id']);
                                ?>

                                <div
                                    class="iez-plan-detail-board__panel<?php echo $index === 0 ? ' is-active' : ''; ?>"
                                    data-iez-plan-panel="<?php echo esc_attr($tab['key']); ?>">
                                    <?php if ($draw_url) : ?>
                                        <button
                                            class="iez-plan-detail-board__drawing-button iez-plan-zoom-button"
                                            type="button"
                                            data-iez-plan-open
                                            data-plan-src="<?php echo esc_url($full_url); ?>"
                                            data-plan-title="<?php echo esc_attr($tab['title']); ?>">
                                            <span class="iez-plan-detail-board__badge"><?php echo esc_html($tab['label']); ?></span>
                                            <img
                                                src="<?php echo esc_url($draw_url); ?>"
                                                alt="<?php echo esc_attr($tab['title']); ?>">
                                        </button>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else : ?>
                        <div class="iez-plan-detail-board__empty">平面図は未設定です</div>
                    <?php endif; ?>
                </div>

                <aside class="iez-plan-detail-board__summary" aria-label="プラン概要">
                    <p class="iez-plan-detail-board__label"><?php echo esc_html($plan_label); ?></p>

                    <h3 class="iez-plan-detail-board__title">
                        <?php echo esc_html($plan_name); ?>
                    </h3>

                    <p class="iez-plan-detail-board__style">
                        <?php echo esc_html($plan_style); ?>
                    </p>

                    <p class="iez-plan-detail-board__layout">
                        <?php echo esc_html($plan_layout); ?>
                    </p>

                    <p class="iez-plan-detail-board__text">
                        <?php echo esc_html($plan_description); ?>
                    </p>

                    <dl class="iez-plan-detail-board__summary-list">
                        <div>
                            <dt>間取り</dt>
                            <dd><?php echo esc_html($plan_layout); ?></dd>
                        </div>
                        <div>
                            <dt>延床面積</dt>
                            <dd><?php echo esc_html($plan_total_area); ?>（<?php echo esc_html($plan_tsubo); ?>）</dd>
                        </div>
                        <div>
                            <dt>建築面積</dt>
                            <dd><?php echo esc_html($plan_building_area); ?></dd>
                        </div>
                        <div>
                            <dt>想定家族</dt>
                            <dd><?php echo esc_html($plan_family); ?></dd>
                        </div>
                    </dl>
                </aside>
            </section>
            <!-- 3段目: 操作ボタン -->
            <section class="iez-plan-detail-board__row iez-plan-detail-board__row--actions" aria-label="操作ボタン">
                <a class="iez-plan-single-control__btn is-secondary" href="<?php echo esc_url(home_url('/iezukuri/plans/')); ?>">
                    参考プラン一覧へ戻る
                </a>

                <?php if ($pdf_download_url) : ?>
                    <a
                        class="iez-plan-single-control__btn is-primary"
                        href="<?php echo esc_url($pdf_download_url); ?>"
                        target="_blank"
                        rel="noopener">
                        PDFダウンロード
                    </a>
                <?php endif; ?>

                <a class="iez-plan-single-control__btn is-secondary" href="<?php echo esc_url(home_url('/iezukuri/contact/')); ?>">
                    このプランを相談する
                </a>
            </section>

            <!-- 4段目: 複数内装写真 -->
            <?php if (!empty($gallery_ids)) : ?>
                <section class="iez-plan-detail-board__row iez-plan-detail-board__row--gallery" aria-label="複数内装写真">
                    <header class="iez-plan-detail-board__section-header">
                        <h3>複数内装写真</h3>
                        <p>LDK、キッチン、玄関、寝室、庭まわりなどを確認できます。</p>
                    </header>

                    <div class="iez-plan-detail-board__gallery-grid">
                        <?php foreach ($gallery_ids as $gallery_id) : ?>
                            <?php
                            $thumb = naigai_iez_plan_detail_img_url($gallery_id, 'medium');
                            $full  = naigai_iez_plan_detail_img_url($gallery_id, 'full');
                            $alt   = get_post_meta($gallery_id, '_wp_attachment_image_alt', true);

                            if (!$thumb || !$full) {
                                continue;
                            }

                            if ($alt === '') {
                                $alt = '内装写真';
                            }
                            ?>

                            <button
                                class="iez-plan-detail-board__gallery-item iez-plan-zoom-button"
                                type="button"
                                data-iez-plan-open
                                data-plan-src="<?php echo esc_url($full); ?>"
                                data-plan-title="<?php echo esc_attr($alt); ?>">
                                <img src="<?php echo esc_url($thumb); ?>" alt="<?php echo esc_attr($alt); ?>">
                                <span><?php echo esc_html($alt); ?></span>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>

            <!-- 5段目: 住宅の特徴 -->
            <section class="iez-plan-detail-board__row iez-plan-detail-board__row--features" aria-label="住宅の特徴">
                <header class="iez-plan-detail-board__section-header">
                    <h3>住宅の特徴</h3>
                    <p>構造・断熱・窓・設備・バリアフリー・外構など、住まいの安心材料を確認できます。</p>
                </header>

                <div class="iez-plan-detail-board__feature-grid">
                    <?php foreach ($features as $feature) : ?>
                        <div class="iez-plan-detail-board__feature-card">
                            <span class="iez-plan-detail-board__feature-icon">
                                <?php echo naigai_iez_plan_detail_icon_svg($feature['icon']); ?>
                            </span>

                            <div class="iez-plan-detail-board__feature-body">
                                <h4><?php echo esc_html($feature['title']); ?></h4>
                                <p><?php echo esc_html($feature['text']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <!-- 6段目: 関連する参考プラン -->
            <?php
            $related_tax_query = array('relation' => 'OR');

            foreach (array('iez_plan_type', 'iez_plan_size', 'iez_plan_feature') as $tax) {
                $term_ids = wp_get_post_terms($page_id, $tax, array('fields' => 'ids'));

                if (!is_wp_error($term_ids) && !empty($term_ids)) {
                    $related_tax_query[] = array(
                        'taxonomy' => $tax,
                        'field'    => 'term_id',
                        'terms'    => $term_ids,
                    );
                }
            }

            $related_query_args = array(
                'post_type'      => 'iez_plan',
                'post_status'    => 'publish',
                'posts_per_page' => 3,
                'post__not_in'   => array($page_id),
                'orderby'        => 'date',
                'order'          => 'DESC',
            );

            if (count($related_tax_query) > 1) {
                $related_query_args['tax_query'] = $related_tax_query;
            }

            $related_query = new WP_Query($related_query_args);
            ?>

            <?php if ($related_query->have_posts()) : ?>
                <section class="iez-plan-detail-board__row iez-plan-detail-board__row--related" aria-label="関連する参考プラン">
                    <header class="iez-plan-detail-board__section-header">
                        <h3>関連する参考プラン</h3>
                        <p>住宅タイプ・坪数・特徴が近いプランを表示しています。</p>
                    </header>

                    <div class="iez-plan-detail-board__related-grid">
                        <?php while ($related_query->have_posts()) : ?>
                            <?php
                            $related_query->the_post();
                            $related_id = get_the_ID();

                            $related_thumb_id = (int) get_post_meta($related_id, '_ch_plan_exterior_image_id', true);

                            if (!$related_thumb_id && has_post_thumbnail($related_id)) {
                                $related_thumb_id = (int) get_post_thumbnail_id($related_id);
                            }

                            $related_thumb = $related_thumb_id ? wp_get_attachment_image_url($related_thumb_id, 'medium') : '';
                            $related_layout = get_post_meta($related_id, '_ch_plan_layout', true);
                            $related_area   = get_post_meta($related_id, '_ch_plan_total_area', true);
                            $related_tsubo  = get_post_meta($related_id, '_ch_plan_tsubo', true);
                            ?>

                            <article class="iez-plan-detail-board__related-card">
                                <a href="<?php the_permalink(); ?>">
                                    <?php if ($related_thumb) : ?>
                                        <img src="<?php echo esc_url($related_thumb); ?>" alt="<?php echo esc_attr(get_the_title()); ?>">
                                    <?php endif; ?>

                                    <div class="iez-plan-detail-board__related-body">
                                        <h4><?php the_title(); ?></h4>

                                        <?php if ($related_layout || $related_area) : ?>
                                            <p>
                                                <?php if ($related_layout) : ?>
                                                    <?php echo esc_html($related_layout); ?>
                                                <?php endif; ?>

                                                <?php if ($related_area) : ?>
                                                    / <?php echo esc_html($related_area); ?>
                                                    <?php if ($related_tsubo) : ?>
                                                        （<?php echo esc_html($related_tsubo); ?>）
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                </a>
                            </article>
                        <?php endwhile; ?>
                    </div>
                </section>
                <?php wp_reset_postdata(); ?>
            <?php endif; ?>

        </article>
    </div>
</section>

<div class="iez-plan-modal" data-iez-plan-modal-root aria-hidden="true">
    <div class="iez-plan-modal__backdrop" data-iez-plan-close></div>

    <div class="iez-plan-modal__dialog" role="dialog" aria-modal="true" aria-label="画像を拡大表示">
        <div class="iez-plan-modal__header">
            <p class="iez-plan-modal__title" data-iez-plan-title>画像</p>
            <button class="iez-plan-modal__close" type="button" data-iez-plan-close>閉じる</button>
        </div>

        <div class="iez-plan-modal__body">
            <button class="iez-plan-modal__nav is-prev" type="button" data-iez-plan-prev>前へ</button>
            <img class="iez-plan-modal__image" src="" alt="" data-iez-plan-image>
            <button class="iez-plan-modal__nav is-next" type="button" data-iez-plan-next>次へ</button>
        </div>
    </div>
</div>