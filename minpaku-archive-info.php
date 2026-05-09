<?php
if (!defined('ABSPATH')) {
    exit;
}

$post_id = get_the_ID();

$icon_map = array(
    '宿泊料金'       => 'icon-coin-yen',
    '週末料金'       => 'icon-coin-yen',
    '清掃料金'       => 'icon-coin-yen',
    '定員'           => 'icon-users',
    '部屋数'         => 'icon-office',
    'ベッド数'       => 'icon-home',
    'チェックイン'   => 'icon-file-text2',
    'チェックアウト' => 'icon-file-text2',
    'Wi-Fi'          => 'icon-file-text2',
    '駐車場'         => 'icon-road',
);

$render_label = function ($label) use ($icon_map) {
    $symbol_id = isset($icon_map[$label]) ? $icon_map[$label] : 'icon-file-text2';
    $ref = '#' . $symbol_id;

    return '<span class="property-label-inner">'
        . '<span class="property-label-icon property-table-label-icon" aria-hidden="true">'
        . '<svg class="property-icon-svg" aria-hidden="true" focusable="false">'
        . '<use href="' . esc_attr($ref) . '" xlink:href="' . esc_attr($ref) . '"></use>'
        . '</svg>'
        . '</span>'
        . '<span class="property-label-text">' . esc_html($label) . '</span>'
        . '</span>';
};

$rows = array(
    array(
        '宿泊料金',
        mnpk_money(mnpk_meta($post_id, '_mnpk_nightly_price')),
        '週末料金',
        mnpk_money(mnpk_meta($post_id, '_mnpk_weekend_price')),
    ),
    array(
        '清掃料金',
        mnpk_money(mnpk_meta($post_id, '_mnpk_cleaning_fee')),
        '定員',
        mnpk_meta($post_id, '_mnpk_capacity') ? mnpk_meta($post_id, '_mnpk_capacity') . '名' : '要確認',
    ),
    array(
        '部屋数',
        mnpk_meta($post_id, '_mnpk_bedrooms') ? mnpk_meta($post_id, '_mnpk_bedrooms') . '部屋' : '要確認',
        'ベッド数',
        mnpk_meta($post_id, '_mnpk_beds') ? mnpk_meta($post_id, '_mnpk_beds') . '台' : '要確認',
    ),
    array(
        'チェックイン',
        mnpk_meta($post_id, '_mnpk_checkin_time', '要確認'),
        'チェックアウト',
        mnpk_meta($post_id, '_mnpk_checkout_time', '要確認'),
    ),
    array(
        'Wi-Fi',
        mnpk_bool_label(mnpk_meta($post_id, '_mnpk_wifi')),
        '駐車場',
        mnpk_bool_label(mnpk_meta($post_id, '_mnpk_parking')),
    ),
);

echo '<div class="property-info-table minpaku-archive-info">';

foreach ($rows as $row) {
    echo '<div class="property-row">';

    echo '<div class="property-label">' . $render_label($row[0]) . '</div>';
    echo '<div class="property-value">' . esc_html($row[1]) . '</div>';

    echo '<div class="property-label">' . $render_label($row[2]) . '</div>';
    echo '<div class="property-value">' . esc_html($row[3]) . '</div>';

    echo '</div>';
}

echo '</div>';
