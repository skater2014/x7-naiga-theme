<?php
// 最大10枚までのメイン画像とサムネイル画像を取得
$images = [];
$post_id = get_the_ID();

for ($i = 1; $i <= 10; $i++) {
    $raw_url   = get_post_meta($post_id, '_iframe_image_url_' . $i, true);
    $raw_title = (string) get_post_meta($post_id, '_iframe_image_title_' . $i, true);
    $raw_text  = (string) get_post_meta($post_id, '_iframe_image_text_' . $i, true);

    $url   = esc_url_raw(trim((string) $raw_url));
    $title = sanitize_text_field(trim($raw_title));
    $text  = sanitize_text_field(trim($raw_text));

    if ($url !== '') {
        $images[] = [
            'url'   => $url,
            'title' => $title,
            'text'  => $text,
        ];
    }
}

if (empty($images)) {
    return;
}

$main_image = $images[0];
$main_title = $main_image['title'];
$main_text  = $main_image['text'];

echo '<div id="image-tab" class="tab-pane image-tab active">';

// メイン画像
echo '<div class="main-image-container">';
echo   '<div class="main-image-slider">';

echo     '<button class="slider-btn prev-btn" type="button" aria-label="前へ">&#10094;</button>';

echo     '<img id="main-image" class="main-image"'
    . ' src="' . esc_url($main_image['url']) . '"'
    . ' alt="' . esc_attr($main_title !== '' ? $main_title : 'image') . '"'
    . ' data-index="0"'
    . ' data-title="' . esc_attr($main_title) . '"'
    . ' data-text="' . esc_attr($main_text) . '"'
    . '>';

echo     '<button class="slider-btn next-btn" type="button" aria-label="次へ">&#10095;</button>';

// タイトル・テキスト
if ($main_title !== '' || $main_text !== '') {
    echo '<div class="area-detail" id="area-detail-0"><div class="text-container">';
    if ($main_title !== '') {
        echo '<p id="main-image-title" class="main-image-title">' . esc_html($main_title) . '</p>';
    }
    if ($main_text !== '') {
        echo '<p id="main-image-text" class="main-image-text">' . esc_html($main_text) . '</p>';
    }
    echo '</div></div>';
}

echo   '</div>'; // .main-image-slider
echo   '<div id="image-index" class="image-index">1 / ' . (int) count($images) . '</div>';
echo '</div>';   // .main-image-container

// サムネイル
echo '<div class="thumbnail-container" id="thumbnail-container">';
foreach ($images as $index => $image) {
    $t = $image['title'];
    $x = $image['text'];

    echo '<div class="thumbnail-item"'
        . ' data-index="' . (int) $index . '"'
        . ' data-title="' . esc_attr($t) . '"'
        . ' data-text="' . esc_attr($x) . '"'
        . '>';

    echo '<img src="' . esc_url($image['url']) . '" alt="' . esc_attr($t !== '' ? $t : 'thumbnail') . '" class="thumbnail-image">';

    if ($t !== '') {
        echo '<div class="thumbnail-title">' . esc_html($t) . '</div>';
    }

    echo '</div>';
}
echo '</div>'; // .thumbnail-container

echo '</div>'; // #image-tab