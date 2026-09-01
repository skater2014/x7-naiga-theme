<?php
if (!defined('ABSPATH')) {
    exit;
}

$post_id = get_queried_object_id() ?: get_the_ID();

$get_meta = static function (string $key, $default = '') use ($post_id) {
    $value = get_post_meta($post_id, $key, true);
    return ($value === '' || $value === null) ? $default : $value;
};

$get_image_id = static function (string $key) use ($post_id): int {
    return absint(get_post_meta($post_id, $key, true));
};

$image_html = static function (
    int $id,
    string $alt = '',
    string $size = 'large'
): string {
    if ($id <= 0) {
        return '';
    }

    return (string) wp_get_attachment_image(
        $id,
        $size,
        false,
        array(
            'alt'     => $alt,
            'loading' => 'lazy',
        )
    );
};

$iezukuri_top    = get_page_by_path('iezukuri');
$iezukuri_top_id = $iezukuri_top ? (int) $iezukuri_top->ID : 0;

$top_service_image = static function (int $index) use ($iezukuri_top_id): int {
    if (!$iezukuri_top_id) {
        return 0;
    }

    return absint(
        get_post_meta(
            $iezukuri_top_id,
            '_ch_top_service_' . $index . '_image_id',
            true
        )
    );
};

$intro_image_id = $get_image_id('_ch_nasu_intro_image_id');

if (!$intro_image_id) {
    $intro_image_id = $top_service_image(1);
}

$intro_button_label = $get_meta(
    '_ch_nasu_intro_btn_label',
    '注文住宅の考え方を見る'
);

$intro_button_url = $get_meta(
    '_ch_nasu_intro_btn_url',
    home_url('/iezukuri/concept/')
);

$services = array(
    array(
        'num'      => '01',
        'eyebrow'  => 'NEW HOUSE',
        'title'    => '新築住宅',
        'text'     => '土地と暮らし方から、一から住まいを計画する。',
        'url'      => home_url('/iezukuri/new-house/'),
        'image_id' => $top_service_image(1),
    ),
    array(
        'num'      => '02',
        'eyebrow'  => 'TWO FAMILY',
        'title'    => '二世帯住宅',
        'text'     => '親世帯と子世帯の距離感から、共有と分離を考える。',
        'url'      => home_url('/iezukuri/nisetai/'),
        'image_id' => $top_service_image(2),
    ),
    array(
        'num'      => '03',
        'eyebrow'  => 'RENOVATION',
        'title'    => '住宅リフォーム',
        'text'     => '今ある住まいを活かして、暮らし方に合わせて整える。',
        'url'      => home_url('/iezukuri/chuko/'),
        'image_id' => $top_service_image(3),
    ),
);

$lifestyle_images = array(
    $get_image_id('_ch_nasu_card1_image_id'),
    $get_image_id('_ch_nasu_card2_image_id'),
    $get_image_id('_ch_nasu_card3_image_id'),
);

if (!$lifestyle_images[0]) {
    $lifestyle_images[0] = $top_service_image(1);
}
if (!$lifestyle_images[1]) {
    $lifestyle_images[1] = $top_service_image(2);
}
if (!$lifestyle_images[2]) {
    $lifestyle_images[2] = $top_service_image(3);
}

$lifestyles = array(
    array(
        'title'    => $get_meta('_ch_nasu_card1_title', '定住'),
        'text'     => $get_meta(
            '_ch_nasu_card1_text',
            '通勤・買い物・家事・収納まで、毎日の生活動線を軸に考えます。'
        ),
        'image_id' => $lifestyle_images[0],
    ),
    array(
        'title'    => $get_meta('_ch_nasu_card2_title', '二拠点生活'),
        'text'     => $get_meta(
            '_ch_nasu_card2_text',
            '滞在頻度、留守中の管理、仕事場所や趣味収納まで含めて考えます。'
        ),
        'image_id' => $lifestyle_images[1],
    ),
    array(
        'title'    => $get_meta('_ch_nasu_card3_title', '家族・親世帯と暮らす'),
        'text'     => $get_meta(
            '_ch_nasu_card3_text',
            '水回り、駐車、生活時間、将来の変化まで家族の距離感から整理します。'
        ),
        'image_id' => $lifestyle_images[2],
    ),
);

$land_image_id = $get_image_id('_ch_nasu_feature_image_id');

if (!$land_image_id) {
    $land_image_id = $top_service_image(3);
}

$land_button_label = $get_meta(
    '_ch_nasu_feature_btn_label',
    '間取り・プランを見る'
);

$land_button_url = $get_meta(
    '_ch_nasu_feature_btn_url',
    home_url('/iezukuri/plans/')
);
?>

<div class="nhg-page" data-nasu-house-page>

    <section id="iez-content" class="nhg-intro">
        <div class="nhg-shell nhg-intro__grid">

            <div class="nhg-intro__copy">
                <p class="ch-eyebrow">NASU HOUSE</p>

                <h2>
                    那須での暮らしを、<br>
                    家のかたちから考える。
                </h2>

                <p class="nhg-lead">
                    定住、二拠点、家族との暮らし。
                    まずは那須でどんな暮らしをしたいかを整理し、
                    そこから新築・二世帯住宅・リフォームという住まいの選択肢を考えます。
                </p>

                <div class="nhg-intro__actions">
                    <a
                        class="ch-btn ch-btn--primary"
                        href="<?php echo esc_url($intro_button_url); ?>"
                    >
                        <?php echo esc_html($intro_button_label); ?>
                    </a>
                </div>
            </div>

            <figure class="nhg-intro__media">
                <?php
                echo $image_html(
                    $intro_image_id,
                    '那須の自然と住まい',
                    'large'
                );
                ?>
            </figure>

        </div>
    </section>


    <section id="iez-nasu-build" class="nhg-routes">
        <div class="nhg-shell">

            <header class="nhg-section-head">
                <div>
                    <p class="ch-eyebrow">PLAN YOUR HOME</p>
                    <h2>住まいの計画を選ぶ</h2>
                </div>

                <p>
                    新築、二世帯住宅、リフォーム。
                    暮らし方に合う住まいのかたちから、それぞれのページをご覧いただけます。
                </p>
            </header>

            <div class="nhg-routes__grid">
                <?php foreach ($services as $service) : ?>
                    <a
                        class="nhg-route"
                        href="<?php echo esc_url($service['url']); ?>"
                    >
                        <figure class="nhg-route__media">
                            <?php
                            echo $image_html(
                                (int) $service['image_id'],
                                (string) $service['title'],
                                'medium_large'
                            );
                            ?>
                        </figure>

                        <div class="nhg-route__body">
                            <div class="nhg-route__meta">
                                <span><?php echo esc_html($service['num']); ?></span>
                                <span><?php echo esc_html($service['eyebrow']); ?></span>
                            </div>

                            <h3><?php echo esc_html($service['title']); ?></h3>
                            <p><?php echo esc_html($service['text']); ?></p>

                            <span class="nhg-route__link">
                                <?php echo esc_html($service['title']); ?>を見る
                                <span aria-hidden="true">→</span>
                            </span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>

        </div>
    </section>


    <section id="iez-nasu-climate" class="nhg-life">
        <div class="nhg-shell">

            <header class="nhg-section-head nhg-section-head--light">
                <div>
                    <p class="ch-eyebrow">LIFESTYLE</p>
                    <h2>那須での暮らし方から考える</h2>
                </div>

                <p>
                    定住、二拠点生活、家族・親世帯との暮らし。
                    それぞれの住み方に合わせて、必要な間取りや設備を考えます。
                </p>
            </header>

            <div
                class="swiper nhg-life__swiper"
                data-nasu-life-swiper
            >
                <div class="swiper-wrapper">

                    <?php foreach ($lifestyles as $item) : ?>
                        <article class="swiper-slide nhg-life-card">

                            <figure class="nhg-life-card__media">
                                <?php
                                echo $image_html(
                                    (int) $item['image_id'],
                                    (string) $item['title'],
                                    'large'
                                );
                                ?>
                            </figure>

                            <div class="nhg-life-card__body">
                                <h3><?php echo esc_html($item['title']); ?></h3>
                                <p><?php echo esc_html($item['text']); ?></p>
                            </div>

                        </article>
                    <?php endforeach; ?>

                </div>

                <div class="nhg-life__controls">
                    <div class="swiper-pagination"></div>

                    <button
                        class="swiper-button-prev"
                        type="button"
                        aria-label="前へ"
                    ></button>

                    <button
                        class="swiper-button-next"
                        type="button"
                        aria-label="次へ"
                    ></button>
                </div>
            </div>

        </div>
    </section>


    <section id="iez-nasu-land" class="nhg-land">
        <div class="nhg-shell nhg-land__grid">

            <figure class="nhg-land__media">
                <?php
                echo $image_html(
                    $land_image_id,
                    '那須の土地と暮らし',
                    'large'
                );
                ?>
            </figure>

            <div class="nhg-land__copy">
                <p class="ch-eyebrow">LAND & LIFE</p>

                <h2>
                    家だけではなく、<br>
                    土地と暮らしを一緒に考える。
                </h2>

                <p>
                    駐車スペースや庭、日当たり、道路との距離まで。
                    建物だけでなく、敷地全体と日々の暮らし方を一緒に考えます。
                    暮らしのイメージが固まったら、
                    具体的な間取り・プランをご覧いただけます。
                </p>

                <a
                    class="ch-btn ch-btn--primary"
                    href="<?php echo esc_url($land_button_url); ?>"
                >
                    <?php echo esc_html($land_button_label); ?>
                </a>
            </div>

        </div>
    </section>

</div>
