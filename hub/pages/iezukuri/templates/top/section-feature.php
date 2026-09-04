<?php
/**
 * hub/pages/iezukuri/templates/top/section-feature.php
 *
 * /iezukuri/ トップ導線。
 * - 暮らし方から選ぶ住まいの入口
 * - 3つの住まい
 * - 選択パネル：外観 / 間取り図 / 内装
 * - Site Reading
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('naigai_iez_top_page_url')) {
    function naigai_iez_top_page_url($path)
    {
        $path = trim((string) $path, '/');
        $page = get_page_by_path($path);

        if ($page instanceof WP_Post) {
            return get_permalink($page);
        }

        return home_url('/' . $path . '/');
    }
}

if (!function_exists('naigai_iez_top_page_image')) {
    function naigai_iez_top_page_image($path)
    {
        $page = get_page_by_path(trim((string) $path, '/'));

        if ($page instanceof WP_Post && has_post_thumbnail($page->ID)) {
            return get_the_post_thumbnail_url($page->ID, 'large');
        }

        return '';
    }
}

if (!function_exists('naigai_iez_top_image_url')) {
    function naigai_iez_top_image_url($value)
    {
        if (is_array($value)) {
            $value = reset($value);
        }

        if (is_string($value)) {
            $value = trim($value);

            if (strpos($value, ',') !== false) {
                $parts = array_filter(array_map('trim', explode(',', $value)));
                $value = reset($parts);
            }
        }

        if (!$value) {
            return '';
        }

        if (is_numeric($value)) {
            $url = wp_get_attachment_image_url((int) $value, 'large');
            return $url ? $url : '';
        }

        if (is_string($value) && preg_match('#^https?://#', $value)) {
            return esc_url_raw($value);
        }

        return '';
    }
}

if (!function_exists('naigai_iez_top_gallery_urls')) {
    function naigai_iez_top_gallery_urls($value)
    {
        $ids = is_array($value)
            ? $value
            : array_filter(array_map('trim', explode(',', (string) $value)));

        $urls = array();

        foreach ($ids as $id) {
            $url = naigai_iez_top_image_url($id);

            if ($url) {
                $urls[] = $url;
            }
        }

        return array_values(array_unique($urls));
    }
}

if (!function_exists('naigai_iez_top_plan_by_terms')) {
    function naigai_iez_top_plan_by_terms($terms)
    {
        $terms = array_values(array_filter((array) $terms));

        if (!$terms || !taxonomy_exists('iez_plan_type')) {
            return null;
        }

        $base = array(
            'post_type'      => 'iez_plan',
            'post_status'    => 'publish',
            'posts_per_page' => 1,
            'orderby'        => array(
                'menu_order' => 'ASC',
                'date'       => 'DESC',
            ),
            'tax_query'      => array(
                array(
                    'taxonomy' => 'iez_plan_type',
                    'field'    => 'slug',
                    'terms'    => $terms,
                ),
            ),
        );

        foreach (array('_ch_plan_show_in_home', '_ch_plan_is_card_primary') as $meta_key) {
            $q = new WP_Query(array_merge($base, array(
                'meta_query' => array(
                    array(
                        'key'   => $meta_key,
                        'value' => '1',
                    ),
                ),
            )));

            if (!empty($q->posts[0]) && $q->posts[0] instanceof WP_Post) {
                return $q->posts[0];
            }
        }

        $q = new WP_Query($base);

        return !empty($q->posts[0]) && $q->posts[0] instanceof WP_Post ? $q->posts[0] : null;
    }
}

if (!function_exists('naigai_iez_top_service_media')) {
    function naigai_iez_top_service_media($item)
    {
        $fallback = !empty($item['image']) ? $item['image'] : '';
        $plan = naigai_iez_top_plan_by_terms(!empty($item['tax_terms']) ? $item['tax_terms'] : array());

        $media = array(
            'exterior' => $fallback,
            'interior' => '',
            'floor1'   => '',
            'floor2'   => '',
            'site'     => '',
        );

        if (!$plan) {
            return $media;
        }

        $id = $plan->ID;

        $exterior = naigai_iez_top_image_url(get_post_meta($id, '_ch_plan_exterior_image_id', true));
        $thumb    = get_the_post_thumbnail_url($id, 'large');
        $floor1   = naigai_iez_top_image_url(get_post_meta($id, '_ch_plan_1f_image_id', true));
        $floor2   = naigai_iez_top_image_url(get_post_meta($id, '_ch_plan_2f_image_id', true));
        $site     = naigai_iez_top_image_url(get_post_meta($id, '_ch_plan_site_image_id', true));
        $gallery  = naigai_iez_top_gallery_urls(get_post_meta($id, '_ch_plan_gallery_image_ids', true));

        $used = array_filter(array($exterior, $thumb, $floor1, $floor2, $site));
        $interior = '';

        foreach ($gallery as $url) {
            if (!in_array($url, $used, true)) {
                $interior = $url;
                break;
            }
        }

        return array(
            'exterior' => $exterior ?: ($thumb ?: $fallback),
            'interior' => $interior,
            'floor1'   => $floor1,
            'floor2'   => $floor2,
            'site'     => $site,
        );
    }
}

if (!function_exists('naigai_iez_top_media_image')) {
    function naigai_iez_top_media_image($url, $label)
    {
        if ($url) {
            echo '<img src="' . esc_url($url) . '" alt="' . esc_attr($label) . '">';
            return;
        }

        echo '<span>' . esc_html($label) . '</span>';
    }
}

$gateway_items = array(
    array(
        'label' => 'Concept',
        'title' => '家づくりの考え方',
        'text'  => '土地、自然、家族の時間。まずは家づくりの思想を紹介します。',
        'url'   => naigai_iez_top_page_url('iezukuri/concept'),
        'image' => naigai_iez_top_page_image('iezukuri/concept'),
    ),
    array(
        'label' => 'Design Policy',
        'title' => '設計方針',
        'text'  => '光、風、動線、性能、素材。暮らしやすさをつくる理由を整理します。',
        'url'   => naigai_iez_top_page_url('iezukuri/design-policy'),
        'image' => naigai_iez_top_page_image('iezukuri/design-policy'),
    ),
    array(
        'label' => 'Plans',
        'title' => '間取りとプラン',
        'text'  => '平屋、2階建て、二世帯、リフォーム。暮らし方から間取りを考えます。',
        'url'   => naigai_iez_top_page_url('iezukuri/plans'),
        'image' => naigai_iez_top_page_image('iezukuri/plans'),
    ),
    array(
        'label' => 'Living Points',
        'title' => '暮らしのポイント',
        'text'  => '実際の住まいから、素材・間取り・外とのつながりを見ていきます。',
        'url'   => naigai_iez_top_page_url('iezukuri/works'),
        'image' => naigai_iez_top_page_image('iezukuri/works'),
    ),
);

include get_template_directory() . '/hub/pages/iezukuri/templates/top/feature/section-feature-data.php';

?>

<?php
/*
 * ============================================================
 * IEZUKURI TOP BODY DATA BRIDGE
 * ============================================================
 *
 * 既存のPHP直書きデータを、
 * 管理画面保存値へ差し替える。
 *
 * HTML/CSSは変更しない。
 * Intro / Hero / CTAにも触らない。
 */
if (
    function_exists(
        'naigai_iez_top_editable_data'
    )
) {

    /*
     * 今表示している /iezukuri/ のページID。
     */
    $naigai_top_page_id =
        get_queried_object_id();

    /*
     * 管理画面保存値を取得。
     */
    $naigai_top_body =
        naigai_iez_top_editable_data(
            $naigai_top_page_id
        );

    /*
     * 3つの住まい。
     *
     * 元々PHPに直書きされていた
     * $service_items を上書きする。
     */
    if (
        !empty(
            $naigai_top_body['service_items']
        )
    ) {
        $service_items =
            $naigai_top_body['service_items'];
    }

    /*
     * セクション見出し。
     */
    $naigai_services_title =
        $naigai_top_body['services_title'];

    $naigai_services_text =
        $naigai_top_body['services_text'];

}
?>

<section class="ch-section ch-top-services" aria-labelledby="ch-top-services-title">
    <div class="ch-shell">
        <div class="ch-head">
            <p class="ch-eyebrow">Service</p>
            <h2 id="ch-top-services-title" class="ch-section-title"><?php
echo esc_html(
    $naigai_services_title
    ?? '3つの住まい'
);
?></h2>
            <p class="ch-top-routes__lead">
                新築住宅、二世帯住宅、住宅リフォーム。気になる住まい方を選ぶと、外観・間取り図・内装を種類別に確認できます。
            </p>
        </div>

        <?php foreach ($service_items as $panel_index => $item) : ?>
            <input
                class="ch-service-panel-radio"
                type="radio"
                name="ch-service-panel"
                id="ch-service-panel-<?php echo esc_attr($panel_index); ?>"
                data-iez-service-radio>
        <?php endforeach; ?>

        <div class="ch-service-route-grid" data-iez-service-tabs>
            <?php foreach ($service_items as $panel_index => $item) : ?>
                <?php $media = naigai_iez_top_service_media($item); ?>

                <article class="ch-service-route-card">
                    <label
                        class="ch-service-route-card__select"
                        for="ch-service-panel-<?php echo esc_attr($panel_index); ?>"
                        data-iez-service-card>
                        <span class="ch-service-route-card__check" aria-hidden="true">✓</span>

                        <div class="ch-service-route-card__image">
                            <?php naigai_iez_top_media_image($media['exterior'], $item['title'] . ' 外観'); ?>
                        </div>

                        <div class="ch-service-route-card__body">
                            <span class="ch-service-route-card__num"><?php echo esc_html($item['num']); ?></span>
                            <h3><?php echo esc_html($item['title']); ?></h3>
                            <p><?php echo esc_html($item['text']); ?></p>
                            <b>選択する</b>
                        </div>
                    </label>
                </article>
            <?php endforeach; ?>
        </div>

        <div class="ch-service-panels" id="iezukuri-plan-fragment" data-iez-service-panel-output hidden aria-hidden="true">
            <?php foreach ($service_items as $panel_index => $item) : ?>
                <?php $media = naigai_iez_top_service_media($item); ?>

                <article class="ch-service-panel ch-service-panel--<?php echo esc_attr($panel_index); ?>">
                    <div class="ch-service-panel__head">
                        <strong class="ch-service-panel__num">01</strong>

                        <div class="ch-service-panel__copy">
                            <p class="ch-service-panel__label">Selected Plan</p>
                            <h3><?php echo esc_html($item['title']); ?></h3>
                            <p><?php echo esc_html($item['text']); ?></p>
                        </div>

                        <div class="ch-service-panel__actions">
                            <a class="ch-service-panel__link" href="<?php echo esc_url($item['plan_url']); ?>">
                                間取り詳細へ進む
                            </a>
                            <a class="ch-service-panel__link is-secondary" href="<?php echo esc_url($item['url']); ?>">
                                <?php echo esc_html($item['title']); ?>詳細へ進む
                            </a>
                        </div>
                    </div>

                    <div class="ch-service-panel__media" aria-label="<?php echo esc_attr($item['title'] . 'の写真と図面'); ?>">
                        <section class="ch-service-panel-media ch-service-panel-media--exterior">
                            <div class="ch-service-panel-media__title">外観</div>
                            <div class="ch-service-panel-media__image">
                                <?php naigai_iez_top_media_image($media['exterior'], $item['title'] . ' 外観'); ?>
                            </div>
                            <p>外から見た住まいの印象、屋根・窓・庭とのつながりを確認します。</p>
                        </section>

                        <section class="ch-service-panel-media ch-service-panel-media--floor">
                            <div class="ch-service-panel-media__title">間取り図</div>
                            <div class="ch-service-panel-media__floor">
                                <div class="ch-service-panel-media__image">
                                    <?php naigai_iez_top_media_image($media['floor1'], '1F間取り図'); ?>
                                </div>
                                <div class="ch-service-panel-media__image">
                                    <?php naigai_iez_top_media_image($media['floor2'] ?: $media['site'], $media['floor2'] ? '2F間取り図' : '配置図'); ?>
                                </div>
                            </div>
                            <p>生活動線、部屋のつながり、敷地との関係を確認します。</p>
                        </section>

                        <section class="ch-service-panel-media ch-service-panel-media--interior">
                            <div class="ch-service-panel-media__title">内装</div>
                            <div class="ch-service-panel-media__image">
                                <?php naigai_iez_top_media_image($media['interior'], $item['title'] . ' 内装'); ?>
                            </div>
                            <p>LDK、収納、水回り、素材感など暮らしの雰囲気を確認します。</p>
                        </section>
                    </div>

                    

                    <?php include get_template_directory() . '/hub/pages/iezukuri/templates/top/feature/section-feature-panel-after.php'; ?>
                </article>
            <?php endforeach; ?>
        </div>

        
    </div>
</section>

<section class="ch-section ch-site-reading" aria-labelledby="ch-site-reading-title" data-nasu-site-reading-static="1">
    <div class="ch-shell ch-site-reading__grid">
        <div class="ch-site-reading__head" style="min-width:0 !important; overflow:hidden !important;">
            <p class="ch-eyebrow">Site Reading</p>
            <h2 id="ch-site-reading-title" class="ch-section-title">
                那須の環境と暮らしから、<br>住まいを考える。
            </h2>
            <p class="ch-site-reading__summary">
                建物だけを先に決めるのではなく、敷地条件、那須の気候、家族の暮らし方、将来の変化まで整理しながら、住まいのかたちを考えます。
            </p>
            <figure
    class="ch-site-reading__media"
    style="
        display:block !important;
        position:relative !important;
        inset:auto !important;
        width:100% !important;
        max-width:560px !important;
        height:300px !important;
        margin:28px 0 0 !important;
        padding:0 !important;
        overflow:hidden !important;
        box-sizing:border-box !important;
        border-radius:18px !important;
        contain:layout paint !important;
        transform:none !important;
        z-index:0 !important;
    "
>
    <img
        src="<?php echo esc_url(get_template_directory_uri() . '/hub/pages/iezukuri/assets/images/nasu-house/nasu-house-forest-residence.png'); ?>"
        alt="那須の自然に囲まれた住まい"
        loading="lazy"
        decoding="async"
        width="560"
        height="300"
        style="
            display:block !important;
            position:static !important;
            inset:auto !important;
            width:100% !important;
            max-width:100% !important;
            height:100% !important;
            margin:0 !important;
            padding:0 !important;
            object-fit:cover !important;
            object-position:center !important;
            transform:none !important;
        "
    >
</figure>
        </div>
        <div class="ch-site-reading__body">
            <div class="ch-site-reading__points" aria-label="那須の家づくりで考える4つの視点">
                <article class="ch-site-reading__point"><span>01</span><h3>敷地条件を考える</h3><p>日当たり、道路との関係、駐車、庭、建物の配置など、敷地全体を見ながら住まいを考えます。</p></article>
                <article class="ch-site-reading__point"><span>02</span><h3>那須の気候を考える</h3><p>夏の日差し、冬の寒さ、雪、風、湿気など、地域の特徴に合わせて断熱・窓・配置を考えます。</p></article>
                <article class="ch-site-reading__point"><span>03</span><h3>暮らし方を考える</h3><p>定住、二拠点生活、家族構成、仕事、収納など、日々の過ごし方から間取りを考えます。</p></article>
                <article class="ch-site-reading__point"><span>04</span><h3>将来の変化を考える</h3><p>家族構成の変化やメンテナンスまで見据えて、長く使いやすい住まいを考えます。</p></article>
            </div>
            <div class="ch-site-reading__actions">
                <a class="ch-btn ch-btn--primary" href="<?php echo esc_url(home_url('/iezukuri/nasu-house/')); ?>">那須の家づくりを見る</a>
                <a class="ch-inline-link" href="<?php echo esc_url(naigai_iez_top_page_url('iezukuri/concept')); ?>">注文住宅の考え方を見る</a>
            </div>
        </div>
    </div>
</section>
