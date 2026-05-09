<?php
/**
 * 家づくりページ SEO
 *
 * 役割:
 * - /iezukuri/chuko/ の title / meta description / OGP / JSON-LD を出す
 * - functions.php に散らさない
 */

if (!defined('ABSPATH')) {
    exit;
}

function naigai_iezukuri_is_chuko_page() {
    if (!is_page()) {
        return false;
    }

    $post = get_post();
    if (!$post) {
        return false;
    }

    return trim((string) get_page_uri($post), '/') === 'iezukuri/chuko';
}

add_filter('pre_get_document_title', function ($title) {
    if (!naigai_iezukuri_is_chuko_page()) {
        return $title;
    }

    return '那須の中古住宅修理・補修・リフォーム相談｜雨漏り・外壁・水回り・床下確認｜内外建設';
}, 20);

add_action('wp_head', function () {
    if (!naigai_iezukuri_is_chuko_page()) {
        return;
    }

    $url = get_permalink();
    $title = '那須の中古住宅修理・補修・リフォーム相談｜雨漏り・外壁・水回り・床下確認｜内外建設';
    $description = '那須・那須塩原で中古住宅の修理、補修、リフォームをご検討の方へ。雨漏り、屋根、外壁、床下、水回り、断熱、外構・伐採まで、住まいの状態を確認し、優先順位を整理してご提案します。';

    $image = '';
    $post_id = get_queried_object_id();

    if ($post_id && has_post_thumbnail($post_id)) {
        $image = get_the_post_thumbnail_url($post_id, 'large');
    }

    if (!$image) {
        $image = home_url('/wp-content/uploads/iezukuri/chuko-ogp.jpg');
    }

    echo "\n" . '<meta name="description" content="' . esc_attr($description) . '">' . "\n";
    echo '<link rel="canonical" href="' . esc_url($url) . '">' . "\n";

    echo '<meta property="og:type" content="website">' . "\n";
    echo '<meta property="og:title" content="' . esc_attr($title) . '">' . "\n";
    echo '<meta property="og:description" content="' . esc_attr($description) . '">' . "\n";
    echo '<meta property="og:url" content="' . esc_url($url) . '">' . "\n";
    echo '<meta property="og:image" content="' . esc_url($image) . '">' . "\n";

    echo '<meta name="twitter:card" content="summary_large_image">' . "\n";

    $schema = array(
        '@context' => 'https://schema.org',
        '@graph' => array(
            array(
                '@type' => 'BreadcrumbList',
                '@id' => trailingslashit($url) . '#breadcrumb',
                'itemListElement' => array(
                    array(
                        '@type' => 'ListItem',
                        'position' => 1,
                        'name' => 'ホーム',
                        'item' => home_url('/'),
                    ),
                    array(
                        '@type' => 'ListItem',
                        'position' => 2,
                        'name' => '家づくり',
                        'item' => home_url('/iezukuri/'),
                    ),
                    array(
                        '@type' => 'ListItem',
                        'position' => 3,
                        'name' => '中古住宅の修理・補修',
                        'item' => $url,
                    ),
                ),
            ),
            array(
                '@type' => 'Service',
                '@id' => trailingslashit($url) . '#service',
                'name' => '中古住宅の修理・補修・リフォーム相談',
                'description' => $description,
                'areaServed' => array(
                    array('@type' => 'Place', 'name' => '那須町'),
                    array('@type' => 'Place', 'name' => '那須塩原市'),
                    array('@type' => 'Place', 'name' => '栃木県北エリア'),
                ),
                'provider' => array(
                    '@type' => 'LocalBusiness',
                    'name' => '内外建設株式会社',
                    'url' => home_url('/'),
                ),
                'serviceType' => array(
                    '中古住宅修理',
                    '住宅補修',
                    'リフォーム相談',
                    '雨漏り修理',
                    '外壁補修',
                    '水回りリフォーム',
                    '床下確認',
                    '外構・伐採相談',
                ),
            ),
            array(
                '@type' => 'FAQPage',
                '@id' => trailingslashit($url) . '#faq',
                'mainEntity' => array(
                    array(
                        '@type' => 'Question',
                        'name' => '小さな修理だけでも相談できますか？',
                        'acceptedAnswer' => array(
                            '@type' => 'Answer',
                            'text' => '雨樋、外壁、建具、水回りなど、部分的な補修から相談できます。気になる箇所の写真があると確認がスムーズです。',
                        ),
                    ),
                    array(
                        '@type' => 'Question',
                        'name' => '中古住宅を購入する前でも相談できますか？',
                        'acceptedAnswer' => array(
                            '@type' => 'Answer',
                            'text' => '購入前でも相談できます。雨漏り、床下、水回り、断熱、外壁など、購入後に費用がかかりやすい箇所を確認します。',
                        ),
                    ),
                    array(
                        '@type' => 'Question',
                        'name' => '補助金を使えるか分からなくても相談できますか？',
                        'acceptedAnswer' => array(
                            '@type' => 'Answer',
                            'text' => '窓、断熱、給湯器、水回り、バリアフリーなど、対象になる可能性がある工事かを確認します。補助金は年度、予算、対象製品、申請時期によって利用可否が変わります。',
                        ),
                    ),
                ),
            ),
        ),
    );

    echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
}, 20);
