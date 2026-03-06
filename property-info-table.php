<?php
global $post;

$post_id = get_the_ID();

// ---------------------------------------------
// メタ取得
// ---------------------------------------------
$property_name    = get_post_meta($post_id, 'PropertyName', true);
$price            = get_post_meta($post_id, 'Price', true);
$units_sold       = get_post_meta($post_id, 'UnitsSold', true);
$total_units      = get_post_meta($post_id, 'TotalUnits', true);
$layout           = get_post_meta($post_id, 'Layout', true);
$building_area    = get_post_meta($post_id, 'BuildingArea', true);
$land_area        = get_post_meta($post_id, 'LandArea', true);
$location         = get_post_meta($post_id, 'Location', true);
$transport        = get_post_meta($post_id, 'Transport', true);
$ownership_type   = get_post_meta($post_id, 'OwnershipType', true);
$land_category    = get_post_meta($post_id, 'LandCategory', true);
$zoning           = get_post_meta($post_id, 'Zoning', true);
$urban_planning   = get_post_meta($post_id, 'Urban_Planning', true);
$remarks          = get_post_meta($post_id, 'Remarks', true);
$road_orientation = get_post_meta($post_id, 'RoadOrientation', true);
$sold_out         = get_post_meta($post_id, 'sold-out', true);

// ---------------------------------------------
// symbol-defs.svg のURL
// 既存の IcoMoon sprite を使う
// ---------------------------------------------

// ---------------------------------------------
// ラベルごとのアイコンマップ
// 既存 sprite の symbol id を使う
// ---------------------------------------------
$property_icon_map = array(
    '物件名'   => 'icon-home',
    '所在地'   => 'icon-location',
    '権利形態' => 'icon-key',
    '価格'     => 'icon-coin-yen',
    '販売戸数' => 'icon-stack',
    '総戸数'   => 'icon-users',
    '間取り'   => 'icon-table',
    '建物面積' => 'icon-office',
    '土地面積' => 'icon-map',
    '地目'     => 'icon-leaf',
    '用途地域' => 'icon-compass',
    '都市計画' => 'icon-map2',
    '交通'     => 'icon-road',
    '接道状況' => 'icon-road',
    '備考'     => 'icon-file-text2',
);

// ---------------------------------------------
// アイコン出力ヘルパー
// 外部サイト参照ではなく、テーマ内 images/symbol-defs.svg を使う
// ---------------------------------------------
$render_property_icon = function ($label) use ($property_icon_map) {
    $symbol_id = isset($property_icon_map[$label]) ? $property_icon_map[$label] : 'icon-file-text2';
    $ref = '#' . $symbol_id;

    return '<svg class="property-icon-svg" aria-hidden="true" focusable="false">'
        . '<use href="' . esc_attr($ref) . '" xlink:href="' . esc_attr($ref) . '"></use>'
        . '</svg>';
};

// ---------------------------------------------
// カテゴリ判定
// ---------------------------------------------
$categories        = get_the_category($post_id);
$is_house          = false;
$is_land           = false;
$is_house_archive  = is_post_type_archive('house');
$is_search         = is_search();
$is_house_type_tax = is_tax('house-type');
$is_region_tax     = is_tax('region');

if (!empty($categories) && !is_wp_error($categories)) {
    foreach ($categories as $category) {
        if ($category->slug === 'naigai-construction') {
            $is_house = true;
        } elseif ($category->slug === 'naigai-tochi') {
            $is_land = true;
        }
    }
}

// ---------------------------------------------
// 数値整形ヘルパー
// ---------------------------------------------
$normalize_number = function ($value) {
    return preg_replace('/[^0-9.]/', '', (string) $value);
};

$properties = array();

// ---------------------------------------------
// 土地系
// ---------------------------------------------
if ($is_land || $is_region_tax || ($is_search && !$is_house)) {
    $price_display = '';
    $price_number  = $normalize_number($price);

    if ($sold_out || $price_number === '' || (float) $price_number <= 0) {
        $price_display = '<span class="sold-out">売却済</span>';
    } else {
        $price_display = number_format((float) $price_number) . '万円';
    }

    $land_area_number = $normalize_number($land_area);

    $properties = array(
        '物件名'   => $property_name,
        '所在地'   => $location,
        '権利形態' => $ownership_type,
        '価格'     => $price_display,
        '土地面積' => $land_area_number !== '' ? number_format((float) $land_area_number) . '㎡' : '',
        '地目'     => $land_category,
        '用途地域' => $zoning,
        '都市計画' => $urban_planning,
        '交通'     => $transport,
        '備考'     => $remarks,
    );
}

// ---------------------------------------------
// 住宅系
// ---------------------------------------------
if ($is_house_archive || $is_house || $is_house_type_tax || is_singular('house') || ($is_search && $is_house)) {
    $price_number = $normalize_number($price);

    if ($sold_out || $price_number === '' || (float) $price_number <= 0) {
        $price_display = '<span class="sold-out">売却済</span>';
    } else {
        $price_display = number_format((float) $price_number) . '万円';
    }

    $building_area_number = $normalize_number($building_area);
    $land_area_number     = $normalize_number($land_area);
    $road_number          = $normalize_number($road_orientation);

    $properties = array(
        '物件名'   => $property_name,
        '所在地'   => $location,
        '権利形態' => $ownership_type,
        '価格'     => $price_display,
        '販売戸数' => $units_sold,
        '総戸数'   => $total_units,
        '間取り'   => $layout,
        '建物面積' => $building_area_number !== '' ? number_format((float) $building_area_number) . '㎡' : '',
        '土地面積' => $land_area_number !== '' ? number_format((float) $land_area_number) . '㎡' : '',
        '交通'     => $transport,
        '接道状況' => $road_number !== '' ? $road_number . 'm' : '',
        '備考'     => $remarks,
    );
}

// 空値除外
$properties = array_filter($properties, function ($value) {
    return $value !== '' && $value !== null;
});

if (!empty($properties)) :
?>
    <div class="property-info-table">
        <?php foreach ($properties as $label => $value) : ?>
            <div class="property-row">
                <div class="property-label">
                    <span class="property-label-inner">
                        <span class="property-label-icon" aria-hidden="true">
                            <?php echo $render_property_icon($label); ?>
                        </span>
                        <span class="property-label-text"><?php echo esc_html($label); ?></span>
                    </span>
                </div>

                <div class="property-value">
                    <?php
                    if ($label === '価格') {
                        echo wp_kses($value, array(
                            'span' => array(
                                'class' => array(),
                            ),
                        ));
                    } else {
                        echo esc_html($value);
                    }
                    ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>