<?php
// 最大10枚までのメイン画像とサムネイル画像を取得
$images = [];
$post_id = get_the_ID();

for ($i = 1; $i <= 10; $i++) {
    // まずは「生データ」を取る（ここでは esc しない）
    $raw_url   = get_post_meta($post_id, '_iframe_image_url_' . $i, true);
    $raw_title = (string) get_post_meta($post_id, '_iframe_image_title_' . $i, true);
    $raw_text  = (string) get_post_meta($post_id, '_iframe_image_text_' . $i, true);

    $url   = esc_url_raw(trim($raw_url));
    $title = sanitize_text_field(trim($raw_title));
    $text  = sanitize_text_field(trim($raw_text));

    // URLがある場合のみ追加
    if ($url !== '') {
        $images[] = [
            'url'   => $url,   // 出力時に esc_url
            'title' => $title, // 出力時に esc_html / esc_attr
            'text'  => $text,  // 出力時に esc_html / esc_attr
        ];
    }
}

// 画像がない場合は何も出さない（template-part 前提なら return でOK）
if (empty($images)) {
    return;
}

$main_image = $images[0];
$main_title = $main_image['title'];
$main_text  = $main_image['text'];

echo '<div id="image-tab" class="tab-pane image-tab active">';

// メイン画像表示
echo '<div class="main-image-container">';
echo '<div class="main-image-slider">';
echo '<button class="slider-btn prev-btn" type="button" onclick="changeMainImage(-1)">&#10094;</button>';

echo '<img id="main-image" class="main-image"'
    . ' src="' . esc_url($main_image['url']) . '"'
    . ' alt="' . esc_attr($main_title !== '' ? $main_title : 'image') . '"'
    . ' data-index="0"'
    . ' data-title="' . esc_attr($main_title) . '"'
    . ' data-text="' . esc_attr($main_text) . '"'
    . '>';

echo '<button class="slider-btn next-btn" type="button" onclick="changeMainImage(1)">&#10095;</button>';

// タイトル・テキスト（どちらかがある時だけ出す）
if ($main_title !== '' || $main_text !== '') {
    echo '<div class="area-detail" id="area-detail-0">';
    echo '<div class="text-container">';

    if ($main_title !== '') {
        echo '<p id="main-image-title" class="main-image-title">' . esc_html($main_title) . '</p>';
    }
    if ($main_text !== '') {
        echo '<p id="main-image-text" class="main-image-text">' . esc_html($main_text) . '</p>';
    }

    echo '</div>';
    echo '</div>';
}

echo '</div>'; // .main-image-slider
echo '<div id="image-index" class="image-index">1 / ' . (int) count($images) . '</div>';
echo '</div>'; // .main-image-container

// サムネイルの表示
echo '<div class="thumbnail-container" id="thumbnail-container">';
foreach ($images as $index => $image) {
    $t = $image['title'];
    $x = $image['text'];

    echo '<div class="thumbnail-item"'
        . ' data-index="' . (int) $index . '"'
        . ' data-title="' . esc_attr($t) . '"'
        . ' data-text="' . esc_attr($x) . '"'
        . ' onclick="changeMainImageWithThumbnail(this)">';

    echo '<img src="' . esc_url($image['url']) . '" alt="' . esc_attr($t !== '' ? $t : 'thumbnail') . '" class="thumbnail-image">';

    if ($t !== '') {
        echo '<div class="thumbnail-title">' . esc_html($t) . '</div>';
    }

    echo '</div>';
}
echo '</div>'; // .thumbnail-container

echo '</div>'; // .image-tab
