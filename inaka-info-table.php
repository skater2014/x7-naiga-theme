<?php // ブログ記事一覧・アーカイブ内で「生活提案」情報を表示する専用テンプレート

// 投稿のIDを取得
$post_id = get_the_ID();

// ブログ用のライフスタイル情報をカスタムフィールドから取得
$garden = get_post_meta($post_id, 'blog_garden', true); // 家庭菜園可
$all_electric = get_post_meta($post_id, 'blog_all_electric', true); // オール電化
$water_supply = get_post_meta($post_id, 'blog_water_supply', true); // 上下水道
$rural_life = get_post_meta($post_id, 'blog_rural_life', true); // 田舎暮らし向き
$permanent_residence = get_post_meta($post_id, 'blog_permanent_residence', true); // 定住向き
$near_school = get_post_meta($post_id, 'blog_near_school', true); // 学校が近い
$near_hospital = get_post_meta($post_id, 'blog_near_hospital', true); // 病院が近い

// 情報を連想配列にまとめる
// 各カスタムフィールド取得
$features = array(
    // 暮らし・生活系
    '家庭菜園可' => get_post_meta($post_id, 'blog_garden', true),
    'オール電化' => get_post_meta($post_id, 'blog_all_electric', true),
    '水道完備' => get_post_meta($post_id, 'blog_water_supply', true),
    '田舎暮らし向け' => get_post_meta($post_id, 'blog_rural_life', true),
    '定住向け' => get_post_meta($post_id, 'blog_permanent_residence', true),
    '学校が近い' => get_post_meta($post_id, 'blog_near_school', true),
    '病院が近い' => get_post_meta($post_id, 'blog_near_hospital', true),
    '二拠点生活向け' => get_post_meta($post_id, 'blog_dual_life', true),
    'アクセス良好' => get_post_meta($post_id, 'blog_access_tokyo', true),
    '高速ネット対応' => get_post_meta($post_id, 'blog_wifi_ready', true),
    '短期滞在' => get_post_meta($post_id, 'blog_short_stay_ok', true),
    '分譲管理' => get_post_meta($post_id, 'blog_manage_support', true),
    '自然環境でリフレッシュ' => get_post_meta($post_id, 'blog_silent_env', true),
    '建物・敷地の柔軟性' => get_post_meta($post_id, 'blog_easy_maintenance', true),
    '駐車スペース有り' => get_post_meta($post_id, 'blog_parking_ready', true),
    '家族での敷地と間取り' => get_post_meta($post_id, 'blog_family_use', true),
    '初期費用・維持費が低減' => get_post_meta($post_id, 'blog_second_home_cost', true),

    // レジャー・観光系（新規追加）
    '人気レストランが近い' => get_post_meta($post_id, 'blog_near_restaurant', true),
    'リゾートホテルが多い' => get_post_meta($post_id, 'blog_near_resorts', true),
    'ゴルフ場が近い' => get_post_meta($post_id, 'blog_near_golf', true),
    'キャンプ場が近い' => get_post_meta($post_id, 'blog_near_camp', true),
    'アウトレットモールが近い' => get_post_meta($post_id, 'blog_near_outlet', true),
    '那須どうぶつ王国近く' => get_post_meta($post_id, 'blog_near_animal_kingdom', true),
    'スキー場・雪遊びも可能' => get_post_meta($post_id, 'blog_winter_sports', true),
    '日帰り温泉施設が充実' => get_post_meta($post_id, 'blog_near_onsen', true),
    'トレッキング・登山が楽しめる' => get_post_meta($post_id, 'blog_trekking_ok', true),
    '子育て環境が整っている' => get_post_meta($post_id, 'blog_child_friendly', true),
);

// ここで確認する！
//echo '<pre>';
//var_dump($features);
//echo '</pre>';

// 1つでも値がある場合のみ出力
$has_feature = false;
foreach ($features as $value) {
    if (!empty($value)) {
        $has_feature = true;
        break;
    }
}

if ($has_feature) {
    echo '<div class="property-info-table blog-lifestyle-table">';
    $heading = get_post_meta($post_id, 'blog_lifestyle_heading', true);
    if (empty($heading)) {
        $heading = '暮らしの特徴';
    }
    echo '<h3 class="table-title">' . esc_html($heading) . '</h3>';
    foreach ($features as $label => $value) {
        if (!empty($value)) {
            echo '<div class="property-row">';
            echo '<div class="property-label">' . esc_html($label) . '</div>';
            echo '<div class="property-value">' . esc_html($value) . '</div>';
            echo '</div>';
        }
    }
    echo '</div>';
}
