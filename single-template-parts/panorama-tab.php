<?php
$post_id = get_the_ID();

// パノラマ画像の取得（最大10枚）
$panorama_images = [];

for ($i = 1; $i <= 10; $i++) {
    $raw_url   = get_post_meta($post_id, '_iframe_panorama_image_url_' . $i, true);
    $raw_title = (string) get_post_meta($post_id, '_iframe_panorama_image_title_' . $i, true);
    $raw_text  = (string) get_post_meta($post_id, '_iframe_panorama_image_text_' . $i, true);

    $url   = esc_url_raw(trim($raw_url));
    $title = sanitize_text_field(trim($raw_title));
    $text  = sanitize_text_field(trim($raw_text));

    if ($url !== '') {
        $panorama_images[] = [
            'url'   => $url,   // 出力時に esc_url
            'title' => $title, // 出力時に esc_html / esc_attr
            'text'  => $text,  // 出力時に esc_html / esc_attr
        ];
    }
}

// 無ければ何も出さない（template-partなら return でOK）
if (empty($panorama_images)) {
    return;
}

// 初期パノラマ
$first = $panorama_images[0];
$first_panorama_url   = $first['url'];
$first_panorama_title = $first['title'];
$first_panorama_text  = $first['text'];

// パノラマタブの表示
echo '<div id="panorama-tab" class="tab-pane panorama-tab">';

// メインのパノラマビュー
echo '<div class="panorama-container" id="panorama-1" style="width:100%; height:400px;" data-panorama-url="'
    . esc_url($first_panorama_url)
    . '"></div>';

// 操作ボタン
echo '<div class="panorama-controls">';
echo '<button id="panorama-prev" class="panorama-btn" type="button">⬅️ 前へ</button>';
echo '<button id="panorama-play" class="panorama-btn" type="button">▶️ 再生</button>';
echo '<button id="panorama-stop" class="panorama-btn" type="button">⏸️ 停止</button>';
echo '<button id="panorama-next" class="panorama-btn" type="button">➡️ 次へ</button>';
echo '</div>';

// タイトル・テキスト（どちらかがある時だけ）
if ($first_panorama_title !== '' || $first_panorama_text !== '') {
    echo '<div id="panorama-title-text">';
    if ($first_panorama_title !== '') {
        echo '<div class="panorama-title">' . esc_html($first_panorama_title) . '</div>';
    }
    if ($first_panorama_text !== '') {
        echo '<div class="panorama-text">' . esc_html($first_panorama_text) . '</div>';
    }
    echo '</div>';
}

// インデックス表示
echo '<div id="panorama-index">1 / ' . (int) count($panorama_images) . '</div>';

// サブ画像
echo '<div class="sub-images-container">';
foreach ($panorama_images as $index => $panorama) {
    $t = $panorama['title'];
    $x = $panorama['text'];
    $u = $panorama['url'];

    echo '<div class="sub-image-item' . (($index === 0) ? ' active' : '') . '"'
        . ' data-index="' . (int) $index . '"'
        . ' data-url="' . esc_url($u) . '"'
        . ' data-title="' . esc_attr($t) . '"'
        . ' data-text="' . esc_attr($x) . '"'
        . '>';

    echo '<img src="' . esc_url($u) . '" alt="' . esc_attr($t !== '' ? $t : 'panorama') . '" style="max-width:100px;">';

    if ($t !== '') {
        echo '<div class="sub-image-title">' . esc_html($t) . '</div>';
    }

    echo '</div>';
}
echo '</div>'; // sub-images-container

echo '</div>'; // panorama-tab
