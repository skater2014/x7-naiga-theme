<?php
/**
 * =========================================================
 * block-plan-list.php
 *
 * 対象ページ:
 * - /iezukuri/plans/
 *
 * 役割:
 * - カスタム投稿 iez_plan の一覧カードを表示する
 * - 各カードから /iezukuri/plan/{slug}/ の詳細ページへ移動する
 *
 * 重要:
 * - /iezukuri/plans/ の固定ページ一覧専用
 * - /iezukuri/plan/{slug}/ の詳細ページでは使わない
 * - PDF専用テンプレート pdf-plan.php では使わない
 *
 * メタ取得方針:
 * - 現在の管理画面データは _ch_plan_* に入っている
 * - 古い _iez_plan_* も保険として見る
 *
 * 一覧カード画像:
 * - _ch_plan_exterior_image_id を最優先
 * - 平面図 / 配置図 / 間取り図は一覧カード画像に使わない
 *
 * CSS対象:
 * - .iez-plan-archive
 * - .iez-plan-archive__grid
 * - .iez-plan-archive-card
 * =========================================================
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * ---------------------------------------------------------
 * メタを候補順に取得する
 * ---------------------------------------------------------
 */
if (!function_exists('naigai_iez_plan_list_get_meta')) {
    function naigai_iez_plan_list_get_meta($post_id, $keys, $default = '') {
        foreach ((array) $keys as $key) {
            $value = get_post_meta($post_id, $key, true);

            if ($value !== '' && $value !== null) {
                return $value;
            }
        }

        return $default;
    }
}

/**
 * ---------------------------------------------------------
 * 値から画像IDを取り出す
 * ---------------------------------------------------------
 */
if (!function_exists('naigai_iez_plan_list_get_image_id_from_value')) {
    function naigai_iez_plan_list_get_image_id_from_value($value) {
        if (is_array($value)) {
            foreach ($value as $item) {
                $id = naigai_iez_plan_list_get_image_id_from_value($item);

                if ($id) {
                    return $id;
                }
            }

            return 0;
        }

        if (!is_scalar($value)) {
            return 0;
        }

        $raw = trim((string) $value);

        if ($raw === '') {
            return 0;
        }

        $maybe = maybe_unserialize($raw);
        if (is_array($maybe)) {
            return naigai_iez_plan_list_get_image_id_from_value($maybe);
        }

        $json = json_decode($raw, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($json)) {
            return naigai_iez_plan_list_get_image_id_from_value($json);
        }

        if (preg_match_all('/\d+/', $raw, $matches)) {
            foreach ($matches[0] as $num) {
                $id = (int) $num;

                if ($id > 0 && wp_attachment_is_image($id)) {
                    return $id;
                }
            }
        }

        return 0;
    }
}

/**
 * ---------------------------------------------------------
 * 一覧カード用の外観画像ID
 * ---------------------------------------------------------
 *
 * 対象:
 * - /iezukuri/plans/
 *
 * 優先:
 * - _ch_plan_exterior_image_id
 *
 * 禁止:
 * - _ch_plan_1f_image_id
 * - _ch_plan_site_image_id
 * - 平面図 / 配置図 / 間取り図
 */
if (!function_exists('naigai_iez_plan_list_get_card_image_id')) {
    function naigai_iez_plan_list_get_card_image_id($post_id) {
        $preferred_keys = array(
            '_ch_plan_exterior_image_id',
            '_ch_plan_hero_image_id',
            '_ch_plan_main_image_id',
            '_ch_plan_card_image_id',

            '_iez_plan_exterior_image_id',
            '_iez_plan_exterior_id',
            '_iez_plan_exterior_photo_id',
            '_iez_plan_hero_image_id',
            '_iez_plan_main_image_id',
            '_iez_plan_thumbnail_id',
            '_iez_plan_card_image_id',
        );

        foreach ($preferred_keys as $key) {
            $value = get_post_meta($post_id, $key, true);
            $id    = naigai_iez_plan_list_get_image_id_from_value($value);

            if ($id) {
                return $id;
            }
        }

        $thumb_id = get_post_thumbnail_id($post_id);

        if ($thumb_id && wp_attachment_is_image($thumb_id)) {
            return (int) $thumb_id;
        }

        return 0;
    }
}

/**
 * ---------------------------------------------------------
 * 延床面積の表示
 * ---------------------------------------------------------
 *
 * _ch_plan_total_area と _ch_plan_tsubo が両方ある場合:
 * - 72㎡（約21.8坪）
 */
if (!function_exists('naigai_iez_plan_list_get_floor_area_label')) {
    function naigai_iez_plan_list_get_floor_area_label($post_id) {
        $total_area = naigai_iez_plan_list_get_meta($post_id, array(
            '_ch_plan_total_area',
        ));

        $tsubo = naigai_iez_plan_list_get_meta($post_id, array(
            '_ch_plan_tsubo',
        ));

        if ($total_area !== '' && $tsubo !== '') {
            return $total_area . '（' . $tsubo . '）';
        }

        if ($total_area !== '') {
            return $total_area;
        }

        if ($tsubo !== '') {
            return $tsubo;
        }

        return naigai_iez_plan_list_get_meta($post_id, array(
            '_iez_plan_floor_area',
            '_iez_plan_total_floor_area',
            '_iez_plan_area',
            '_iez_plan_total_area',
            'floor_area',
            'total_floor_area',
            'plan_area',
            'area',
        ));
    }
}

/*
 * ============================================================
 * 参考プラン一覧 + taxonomy絞り込み
 * ============================================================
 *
 * 通常の参考プラン一覧では全件表示する。
 *
 * taxonomyページを開いた場合だけ、
 * 現在のtermに該当する iez_plan に絞り込む。
 *
 * 不動産用taxonomy.phpの検索処理は使用しない。
 */
/*
 * 3列 × 3段 = 9件を1ページとする。
 * 10件目から2ページ目を表示する。
 */
$plan_paged = max(
    1,
    (int) get_query_var('paged'),
    (int) get_query_var('page')
);

$plan_query_args = array(
    'post_type'      => 'iez_plan',
    'post_status'    => 'publish',
    'posts_per_page' => 9,
            'paged'          => $plan_paged,
    'orderby'        => array(
        'menu_order' => 'ASC',
        'date'       => 'DESC',
    ),
);

$plan_taxonomies = array(
    'iez_plan_type',
    'iez_plan_size',
    'iez_plan_feature',
    'iez_plan_structure',
    'iez_plan_scope',
    'iez_plan_building_form',
    'iez_plan_layout',
    'iez_plan_area',
);

if (is_tax($plan_taxonomies)) {

    $current_term = get_queried_object();

    if (
        $current_term
        && !empty($current_term->taxonomy)
        && !empty($current_term->term_id)
    ) {
        $plan_query_args['tax_query'] = array(
            array(
                'taxonomy' => $current_term->taxonomy,
                'field'    => 'term_id',
                'terms'    => (int) $current_term->term_id,
            ),
        );
    }
}

$q = new WP_Query($plan_query_args);
?>

<section class="iez-block iez-plan-archive" data-iezukuri-block="plan-list">
    <div class="iez-block__inner iez-plan-archive__inner">
        <p class="iez-block__kicker">FLOOR PLAN</p>
        <h1 class="iez-block__title">
    <?php
    echo esc_html(
        is_tax($plan_taxonomies)
            ? single_term_title('', false) . 'の参考プラン'
            : '参考プラン一覧'
    );
    ?>
</h1>
        <p class="iez-block__lead">
            平屋・コンパクト住宅・家族構成に合わせた参考プランを確認できます。
        </p>

        <?php if ($q->have_posts()) : ?>
            <div class="iez-plan-archive__grid">
                <?php
                while ($q->have_posts()) :
                    $q->the_post();

                    $post_id  = get_the_ID();
                    $image_id = naigai_iez_plan_list_get_card_image_id($post_id);

                    /*
                     * ---------------------------------------------------------
                     * 一覧カード用 PDF ダウンロード
                     *
                     * 対象:
                     * - /iezukuri/plans/
                     *
                     * 仕様:
                     * - 生成済みPDFがある場合だけ「PDFダウンロード」を表示
                     * - 保存先: /wp-content/uploads/iezukuri-pdf/{slug}.pdf
                     * - 詳細ページ側 single-iez_plan.php と同じ考え方
                     * ---------------------------------------------------------
                     */
                    $upload_dir = wp_upload_dir();
                    $plan_slug  = get_post_field('post_name', $post_id);

                    $generated_pdf_path = '';
                    $generated_pdf_url  = '';
                    $has_generated_pdf  = false;

                    if ($plan_slug !== '' && !empty($upload_dir['basedir'])) {
                        $generated_pdf_path = trailingslashit($upload_dir['basedir']) . 'iezukuri-pdf/' . $plan_slug . '.pdf';
                        $generated_pdf_url  = home_url('/wp-content/uploads/iezukuri-pdf/' . $plan_slug . '.pdf');

                        $has_generated_pdf = (
                            file_exists($generated_pdf_path) &&
                            filesize($generated_pdf_path) > 0
                        );
                    }

                    $label = naigai_iez_plan_list_get_meta($post_id, array(
                        '_ch_plan_label',
                        '_iez_plan_label',
                        '_iez_plan_plan_label',
                        '_iez_plan_code',
                    ));

                    $plan_name = get_the_title($post_id);

                    $layout = naigai_iez_plan_list_get_meta($post_id, array(
                        '_ch_plan_layout',
                        '_iez_plan_layout',
                        '_iez_plan_madori',
                        '_iez_plan_floor_plan',
                        'layout',
                        'madori',
                        'plan_layout',
                    ));

                    $floor_area = naigai_iez_plan_list_get_floor_area_label($post_id);

                    $building_area = naigai_iez_plan_list_get_meta($post_id, array(
                        '_ch_plan_building_area',
                        '_iez_plan_building_area',
                        '_iez_plan_build_area',
                        '_iez_plan_construction_area',
                        'building_area',
                        'build_area',
                    ));

                    $family = naigai_iez_plan_list_get_meta($post_id, array(
                        '_ch_plan_family',
                        '_iez_plan_family',
                        '_iez_plan_target_family',
                        '_iez_plan_family_type',
                        'family',
                        'target_family',
                    ));

/*
 * ============================================================
 * カード内の絞り込みリンク
 * ============================================================
 *
 * 新しい「分類」行は作らない。
 *
 * 今すでに表示している
 *
 *   ・上部ラベル
 *   ・間取り
 *   ・延床面積
 *
 * を、そのまま検索導線として利用する。
 */


/*
 * 間取り
 *
 * 例:
 * 2LDK → 2LDKの参考プラン一覧
 */
$layout_terms = get_the_terms(
    $post_id,
    'iez_plan_layout'
);

$layout_term = (
    !is_wp_error($layout_terms)
    && !empty($layout_terms)
)
    ? reset($layout_terms)
    : null;

$layout_url = '';

if ($layout_term) {

    $term_link = get_term_link($layout_term);

    if (!is_wp_error($term_link)) {
        $layout_url = $term_link;
    }
}


/*
 * 延床面積帯
 *
 * 実際の面積
 *   68.5㎡（約20.7坪）
 *
 * はそのまま残す。
 *
 * その横に検索用の
 *   50〜70㎡
 * を表示する。
 */
$area_terms = get_the_terms(
    $post_id,
    'iez_plan_area'
);

$area_term = (
    !is_wp_error($area_terms)
    && !empty($area_terms)
)
    ? reset($area_terms)
    : null;

$area_url = '';

if ($area_term) {

    $term_link = get_term_link($area_term);

    if (!is_wp_error($term_link)) {
        $area_url = $term_link;
    }
}


/*
 * カード上部ラベル
 *
 * 二世帯住宅、平屋、2階建て等が
 * 既存termと一致した場合だけリンクにする。
 *
 * 一致しない自由入力ラベルは、
 * 従来どおり普通の文字として表示する。
 */
$label_url = '';

if ($label !== '') {

    /*
     * 「2階建てプラン」
     * ↓
     * 「2階建て」
     *
     * としてtermを探す。
     */
    $label_lookup = preg_replace(
        '/プラン$/u',
        '',
        trim($label)
    );

    foreach (
        array(
            'iez_plan_type',
            'iez_plan_building_form',
        )
        as $label_taxonomy
    ) {

        $matched_term = get_term_by(
            'name',
            $label_lookup,
            $label_taxonomy
        );

        if (!$matched_term) {
            continue;
        }

        $term_link = get_term_link($matched_term);

        if (!is_wp_error($term_link)) {
            $label_url = $term_link;
            break;
        }
    }
}
                    ?>
                    <article class="iez-plan-archive-card">
                        <div class="iez-plan-archive-card__inner">
                            <a class="iez-plan-archive-card__image" href="<?php the_permalink(); ?>">
                                <?php if ($image_id) : ?>
                                    <?php
                                    echo wp_get_attachment_image(
                                        $image_id,
                                        'large',
                                        false,
                                        array(
                                            'loading' => 'lazy',
                                            'alt'     => esc_attr($plan_name),
                                        )
                                    );
                                    ?>
                                <?php else : ?>
                                    <span>NO IMAGE</span>
                                <?php endif; ?>
                            </a>

                            <div class="iez-plan-archive-card__body">
                                <?php if ($label !== '') : ?>
                                    <p class="iez-plan-archive-card__label">
    <?php if ($label_url !== '') : ?>
        <a href="<?php echo esc_url($label_url); ?>">
            <?php echo esc_html($label); ?>
        </a>
    <?php else : ?>
        <?php echo esc_html($label); ?>
    <?php endif; ?>
</p>
                                <?php endif; ?>

                                <h2 class="iez-plan-archive-card__title"><a href="<?php the_permalink(); ?>"><?php echo esc_html($plan_name); ?></a></h2>

                                <dl class="iez-plan-archive-card__meta">
                                    <?php if ($layout !== '') : ?>
                                        <div>
                                            <dt>間取り</dt>
                                            <dd>
    <?php if ($layout_url !== '') : ?>
        <a href="<?php echo esc_url($layout_url); ?>">
            <?php echo esc_html($layout); ?>
        </a>
    <?php else : ?>
        <?php echo esc_html($layout); ?>
    <?php endif; ?>
</dd>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($floor_area !== '') : ?>
                                        <div class="iez-plan-archive-card__meta-row is-floor-area">
                                            <dt>延床面積</dt>
                                            <dd>
    <span class="iez-plan-archive-card__area-value">
        <?php echo esc_html($floor_area); ?>
    </span>

    <?php if ($area_term && $area_url !== '') : ?>
        <span class="iez-plan-archive-card__area-filter">
            <span class="iez-plan-archive-card__area-filter-label">
                面積帯
            </span>

            <a href="<?php echo esc_url($area_url); ?>">
                <?php echo esc_html($area_term->name); ?>
            </a>
        </span>
    <?php endif; ?>
</dd>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($building_area !== '') : ?>
                                        <div>
                                            <dt>建築面積</dt>
                                            <dd><?php echo esc_html($building_area); ?></dd>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($family !== '') : ?>
                                        <div>
                                            <dt>想定家族</dt>
                                            <dd><?php echo esc_html($family); ?></dd>
                                        </div>
                                    <?php endif; ?>
                                </dl>

                                <div class="iez-plan-archive-card__actions">
                                    <a class="iez-plan-archive-card__button" href="<?php the_permalink(); ?>">
                                        詳細を見る
                                    </a>

                                    <?php if ($has_generated_pdf) : ?>
                                        <a class="iez-plan-archive-card__button is-download" href="<?php echo esc_url($generated_pdf_url); ?>" download>
                                            PDFダウンロード
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>

            <?php if ((int) $q->max_num_pages > 1) : ?>
                <?php
                /*
                 * 9件を超えた場合だけ表示するページャー。
                 *
                 * 通常アーカイブとtaxonomy一覧の両方で
                 * 同じ処理を使用する。
                 */
                $big = 999999999;

                $pagination_links = paginate_links(array(
                    'base'      => str_replace(
                        $big,
                        '%#%',
                        esc_url(get_pagenum_link($big))
                    ),
                    'current'   => $plan_paged,
                    'total'     => (int) $q->max_num_pages,
                    'mid_size'  => 2,
                    'end_size'  => 1,
                    'prev_text' => '← 前へ',
                    'next_text' => '次へ →',
                    'type'      => 'array',
                ));
                ?>

                <?php if (!empty($pagination_links)) : ?>
                    <nav
                        class="iez-plan-archive__pagination"
                        aria-label="参考プラン一覧のページ"
                    >
                        <?php foreach ($pagination_links as $pagination_link) : ?>
                            <?php echo wp_kses_post($pagination_link); ?>
                        <?php endforeach; ?>
                    </nav>
                <?php endif; ?>

            <?php endif; ?>
        <?php else : ?>
            <div class="iez-plan-archive__empty">
                <p>現在、表示できる参考プランがありません。</p>
            </div>
        <?php endif; ?>

        <?php wp_reset_postdata(); ?>
    </div>
</section>
