<?php
if (!defined('ABSPATH')) {
    exit;
}

$entrance_asset_base = trailingslashit(get_template_directory_uri())
    . 'hub/pages/iezukuri/assets/images/top/entrance/';

$entrance_asset = static function ($filename) use ($entrance_asset_base) {
    return $entrance_asset_base . rawurlencode($filename);
};

$works_page = get_page_by_path('iezukuri/works');
$living_url = $works_page instanceof WP_Post
    ? get_permalink($works_page)
    : home_url('/iezukuri/#customhome-works');

$entrance_items = array(
    array(
        'label' => 'Concept',
        'title' => '家づくりの考え方',
        'text'  => '土地、自然、家族の時間から、住まいづくりの考え方を見ます。',
        'url'   => home_url('/iezukuri/concept/'),
        'image' => $entrance_asset('家づくりの考え方.webp'),
    ),
    array(
        'label' => 'Design Policy',
        'title' => '設計方針',
        'text'  => '光、風、性能、素材。暮らしやすさをつくる設計の理由を見ます。',
        'url'   => home_url('/iezukuri/design-policy/'),
        'image' => $entrance_asset('設計方針.webp'),
    ),
    array(
        'label' => 'Plans',
        'title' => '間取りとプラン',
        'text'  => '平屋・二世帯・二拠点など、暮らし方から間取りを見ます。',
        'url'   => home_url('/iezukuri/plans/'),
        'image' => $entrance_asset('間取りとプラン.webp'),
    ),
    array(
        'label' => 'Living Points',
        'title' => '暮らしのポイント',
        'text'  => '実際の住まいから、素材・間取り・外とのつながりを見ます。',
        'url'   => $living_url,
        'image' => $entrance_asset('暮らしのポイント.webp'),
    ),
);
?>

<section class="ch-section ch-section--white ch-top-routes" aria-labelledby="ch-top-routes-title" data-iez-entrance="1">
    <div class="ch-shell">
        <div class="ch-head ch-head--with-link">
            <div>
                <p class="ch-eyebrow">Entrance</p>
                <h2 id="ch-top-routes-title" class="ch-section-title">家づくりの入口</h2>
                <p class="ch-top-routes__lead">
                    考え方、設計方針、間取り、暮らしのポイントへ。知りたい内容から進めます。
                </p>
            </div>
        </div>

        <div class="ch-route-grid">
            <?php foreach ($entrance_items as $item) : ?>
                <article class="ch-route-card">
                    <a href="<?php echo esc_url($item['url']); ?>">
                        <div class="ch-route-card__image">
                            <img
                                src="<?php echo esc_url($item['image']); ?>"
                                alt="<?php echo esc_attr($item['title']); ?>"
                                loading="lazy"
                                decoding="async"
                            >
                        </div>

                        <div class="ch-route-card__body">
                            <p class="ch-route-card__label"><?php echo esc_html($item['label']); ?></p>
                            <h3><?php echo esc_html($item['title']); ?></h3>
                            <p><?php echo esc_html($item['text']); ?></p>
                            <b>詳しく見る</b>
                        </div>
                    </a>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
