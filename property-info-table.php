<?php // カテゴリーページ・アーカイブページに物件情報を表示させている 
global $post;

// メタボックスの情報を取得
$property_name = get_post_meta(get_the_ID(), 'PropertyName', true);
$price = get_post_meta(get_the_ID(), 'Price', true);
$units_sold = get_post_meta(get_the_ID(), 'UnitsSold', true);
$total_units = get_post_meta(get_the_ID(), 'TotalUnits', true);
$layout = get_post_meta(get_the_ID(), 'Layout', true);
$building_area = get_post_meta(get_the_ID(), 'BuildingArea', true);
$land_area = get_post_meta(get_the_ID(), 'LandArea', true);
$location = get_post_meta(get_the_ID(), 'Location', true);
$transport = get_post_meta(get_the_ID(), 'Transport', true);
$ownership_type = get_post_meta(get_the_ID(), 'OwnershipType', true);
$land_category = get_post_meta(get_the_ID(), 'LandCategory', true);
$zoning = get_post_meta(get_the_ID(), 'Zoning', true);
$urban_planning = get_post_meta(get_the_ID(), 'Urban_Planning', true);
$remarks = get_post_meta(get_the_ID(), 'Remarks', true);
$road_orientation = get_post_meta($post->ID, 'RoadOrientation', true); // 接道状況（道路の向き）
$sold_out = get_post_meta($post->ID, 'sold-out', true);


// 現在の投稿のアーカイブとカテゴリーを取得
$categories = get_the_category();
$is_house = false;
$is_land = false;
$is_archive = is_archive(); // 利用していない
$is_house_archive = is_post_type_archive('house'); // `/house/` のアーカイブか？

$is_search = is_search();
$is_house_type_tax = is_tax('house-type');  // 'house-type' タクソノミーを判定
$is_region_tax = is_tax('region');  // 'region' タクソノミーを判定
$is_review = false;  // reviewカテゴリのフラグを追加


// 投稿が属しているカテゴリーを確認
foreach ($categories as $category) {
    if ($category->slug === 'naigai-construction') {
        $is_house = true;  // 住宅カテゴリ
    } elseif ($category->slug === 'naigai-tochi') {
        $is_land = true;  // 土地カテゴリ
    }
}

// 検索ページの場合、レビュー記事を除外する
if (is_search() && $is_review) {
    return; // この投稿は検索結果に含めない
}

// 物件情報の配列を定義
$properties = [];

// **土地カテゴリまたは `region` アーカイブ（建物面積を表示しない）**
if ($is_land || $is_region_tax || ($is_search && !$is_house)) {  
    $properties = array_merge($properties, [
        '物件名' => $property_name,
        '所在地' => $location,
        '権利形態' => $ownership_type,
        '価格' => number_format((int)$price) . '万円',
        '土地面積' => (!empty($land_area)) ? number_format((float)preg_replace('/[^0-9.]/', '', $land_area)) . '㎡' : '土地面積情報が設定されていません。',
        '地目' => $land_category,
        '用途地域' => $zoning,
        '都市計画' => $urban_planning,
        '交通' => $transport,
        '備考' => $remarks,
    ]);
}

// **住宅カテゴリ (建物面積を表示する)**
if ($is_house_archive || $is_house || $is_house_type_tax || is_singular('house') || ($is_search && $is_house)) {
    $properties = array_merge($properties, [
        '物件名' => $property_name,
        '所在地' => $location,
        '権利形態' => $ownership_type,
        // 価格が空白または0の場合、または 'sold-out' が設定されている場合は「売却済み」
        '価格' => (empty($price) || $price == 0 || $sold_out) ? '<span class="sold-out">売却済</span>' : number_format((int)$price) . '万円',
        '販売戸数' => $units_sold,
        '総戸数' => $total_units,
        '間取り' => $layout,
        // **建物面積を `is_land` でなく `is_house` のみに適用**
        '建物面積' => (!empty($building_area)) ? number_format((float)preg_replace('/[^0-9.]/', '', $building_area)) . '㎡' : '建物面積情報が設定されていません。',
        '土地面積' => (!empty($land_area)) ? number_format((float)preg_replace('/[^0-9.]/', '', $land_area)) . '㎡' : '土地面積情報が設定されていません。',
        '交通' => $transport,
        '接道状況' => (!empty($road_orientation)) ? preg_replace('/[^0-9.]/', '', $road_orientation) . 'm' : '接道状況情報が設定されていません。',
        '備考' => $remarks,
    ]);
}



// 物件情報がある場合に表示
if (!empty($properties)) {
    echo '<div class="property-info-table">';
    foreach ($properties as $label => $value) {
        if (!empty($value)) {
            echo '<div class="property-row">';
            echo '<div class="property-label">' . esc_html($label) . '</div>';

            // 「価格」の場合はそのまま表示（売却済みの場合は「売却済み」を表示）
            echo '<div class="property-value">' . $value . '</div>';

            echo '</div>';
        }
    }
    echo '</div>';
}
?>
