<?php
/**
 * Room Gallery Meta Box の追加
 */

error_log('✅ function-page.php が読み込まれました！');


// ページテンプレートが "page-gallery-room.php" の場合にメタボックスを追加
function add_room_gallery_metabox() {
    global $post;
    // ページテンプレートが "page-gallery-room.php" の場合のみ表示
    if ( $post && 'page-gallery-room.php' === get_page_template_slug( $post->ID ) ) {
        add_meta_box(
            'room_gallery_metabox',             // メタボックスID
            'Room Gallery Images',              // タイトル
            'room_gallery_metabox_callback',    // コールバック関数
            'page',                             // 対象投稿タイプ
            'normal',                           // 表示位置
            'high'                              // 優先度
        );
    }
}
add_action( 'add_meta_boxes', 'add_room_gallery_metabox' );


// メタボックスの出力コールバック
function room_gallery_metabox_callback( $post ) {
    // セキュリティ用nonceフィールド
    wp_nonce_field( 'room_gallery_save', 'room_gallery_nonce' );
    
    // 既存の画像ID（カンマ区切りの文字列）を取得
    $gallery_images = get_post_meta( $post->ID, 'room_gallery_images', true );
    // 配列に変換（なければ空配列）
    $image_ids = ! empty( $gallery_images ) ? explode( ',', $gallery_images ) : array();
    ?>
    <div id="room-gallery-container">
        <p>※ 最大20枚の画像をアップロードできます。</p>
        <ul id="room-gallery-images">
            <?php foreach ( $image_ids as $image_id ) : 
                $image_src = wp_get_attachment_image_src( $image_id, 'thumbnail' );
                if ( $image_src ) : ?>
                    <li data-attachment_id="<?php echo esc_attr( $image_id ); ?>">
                        <img src="<?php echo esc_url( $image_src[0] ); ?>" style="max-width:100%;" />
                        <a href="#" class="room-gallery-remove">Remove</a>
                    </li>
            <?php endif; endforeach; ?>
        </ul>
        <input type="hidden" id="room_gallery_images" name="room_gallery_images" value="<?php echo esc_attr( $gallery_images ); ?>" />
        <p>
            <button type="button" id="room-gallery-add" class="button">Add Images</button>
        </p>
    </div>
    <script>
    jQuery(document).ready(function($){
        var frame;
        $('#room-gallery-add').on('click', function(e){
            e.preventDefault();
            if (frame) {
                frame.open();
                return;
            }
            frame = wp.media({
                title: 'Select Gallery Images',
                button: { text: 'Add to gallery' },
                multiple: true
            });
            frame.on('select', function(){
                var attachments = frame.state().get('selection').toArray();
                var ids = $('#room_gallery_images').val() ? $('#room_gallery_images').val().split(',') : [];
                attachments.forEach(function(attachment){
                    attachment = attachment.toJSON();
                    if(ids.indexOf(attachment.id.toString()) === -1){
                        ids.push(attachment.id);
                        $('#room-gallery-images').append('<li data-attachment_id="'+attachment.id+'"><img src="'+attachment.sizes.thumbnail.url+'" style="max-width:100%;" /><a href="#" class="room-gallery-remove">Remove</a></li>');
                    }
                });
                // 最大20枚まで制限
                if(ids.length > 20){
                    ids = ids.slice(0,20);
                }
                $('#room_gallery_images').val(ids.join(','));
            });
            frame.open();
        });
        
        $('#room-gallery-images').on('click', '.room-gallery-remove', function(e){
            e.preventDefault();
            var li = $(this).closest('li');
            var id = li.data('attachment_id').toString();
            li.remove();
            var ids = $('#room_gallery_images').val().split(',');
            ids = ids.filter(function(item){ return item !== id; });
            $('#room_gallery_images').val(ids.join(','));
        });
    });
    </script>
    <?php
}

// メタボックスのデータ保存
function room_gallery_save_meta( $post_id ) {
    // nonce チェック
    if ( ! isset( $_POST['room_gallery_nonce'] ) || ! wp_verify_nonce( $_POST['room_gallery_nonce'], 'room_gallery_save' ) ) {
        return;
    }
    // オートセーブの場合はスキップ
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    // 権限チェック
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }
    // 送信された画像IDをサニタイズして保存（カンマ区切りの文字列）
    if ( isset( $_POST['room_gallery_images'] ) ) {
        $ids = sanitize_text_field( $_POST['room_gallery_images'] );
        update_post_meta( $post_id, 'room_gallery_images', $ids );
    }
}
add_action( 'save_post', 'room_gallery_save_meta' );


// ====================================
// 🎥 動画設定 (MP4 & YouTube)
// ====================================

// 1. メタボックス追加
function add_video_metaboxes() {
    add_meta_box(
        'video_metabox',
        '動画設定 (MP4 & YouTube)',
        'render_video_metabox',
        ['post', 'page'],
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'add_video_metaboxes');

// 2. メタボックスの中身を表示
function render_video_metabox($post) {
    $media_url = get_post_meta($post->ID, '_media_url', true);
    $media_file = get_post_meta($post->ID, '_media_file', true);
    $youtube_ids = [];

    for ($i = 1; $i <= 5; $i++) {
        $youtube_ids[$i] = get_post_meta($post->ID, "_youtube_id_$i", true);
    }
    ?>
    <p>MP4動画の外部URLまたはアップロード:</p>
    <input type="text" name="media_url" value="<?php echo esc_url($media_url); ?>" placeholder="https://example.com/video.mp4"><br>
    <input type="text" name="media_file" id="media_file" value="<?php echo esc_url($media_file); ?>" readonly>
    <button class="button media-upload-button">動画をアップロード</button>

    <hr>
    <p>YouTube 動画ID（最大5件）:</p>
    <?php for ($i = 1; $i <= 5; $i++): ?>
        <input type="text" name="youtube_id_<?php echo $i; ?>" value="<?php echo esc_attr($youtube_ids[$i]); ?>" placeholder="YouTube ID"><br>
    <?php endfor; ?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelector(".media-upload-button").addEventListener("click", function(e) {
                e.preventDefault();
                var frame = wp.media({ title: "動画を選択", library: { type: "video" }, multiple: false });
                frame.on("select", function() {
                    var attachment = frame.state().get("selection").first().toJSON();
                    document.getElementById("media_file").value = attachment.url;
                });
                frame.open();
            });
        });
    </script>
    <?php
}

// 3. 保存処理
function save_video_metabox($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (isset($_POST['media_url'])) update_post_meta($post_id, '_media_url', esc_url_raw($_POST['media_url']));
    if (isset($_POST['media_file'])) update_post_meta($post_id, '_media_file', esc_url_raw($_POST['media_file']));
    for ($i = 1; $i <= 5; $i++) {
        if (isset($_POST["youtube_id_$i"])) {
            update_post_meta($post_id, "_youtube_id_$i", sanitize_text_field($_POST["youtube_id_$i"]));
        }
    }
}
add_action('save_post', 'save_video_metabox');


// MP4 表示関数
function display_media_mp4($post_id = null) {
    $post_id = $post_id ?: get_the_ID();
    $url = get_post_meta($post_id, '_media_file', true) ?: get_post_meta($post_id, '_media_url', true);
    if ($url) {
        echo '<div class="custom-video"><video muted autoplay loop playsinline><source src="' . esc_url($url) . '" type="video/mp4"></video></div>';
    } else {
        echo '<p>MP4動画はありません。</p>';
    }
}

// 📌 YouTube動画スライダーを表示
function display_media_youtube($post_id = null) {
    // 現在の投稿IDを取得（未指定なら）
    if ($post_id === null) {
        $post_id = get_the_ID();
    }

    $youtube_ids = [];

    // 🔁 最大5件のYouTube IDを配列に格納
    for ($i = 1; $i <= 5; $i++) {
        $id = get_post_meta($post_id, "_youtube_id_$i", true);
        if (!empty($id)) {
            $youtube_ids[] = $id;
        }
    }

    // 🎥 動画がない場合の処理
    if (empty($youtube_ids)) {
        echo '<p>YouTube動画はありません。</p>';
        return;
    }

    // ✅ SwiperのHTML構造（ラッパー）
    echo '<div class="swiper youtube-video-slider">';
    echo '<div class="swiper-wrapper">';

    // 🔁 各YouTube動画を .swiper-slide に出力
    foreach ($youtube_ids as $id) {
        echo '<div class="swiper-slide">';
        echo '<div class="youtube-video">';
        echo '<iframe width="100%" height="315" src="https://www.youtube.com/embed/' . esc_attr($id) . '" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
        echo '</div>';
        echo '</div>';
    }

    echo '</div>'; // .swiper-wrapper

    // ✅ ナビゲーションボタンとページネーション（このスライダー専用）
    echo '<div class="swiper-button-prev"></div>';
    echo '<div class="swiper-button-next"></div>';
    echo '<div class="swiper-pagination"></div>';
    echo '</div>';

    // ✅ Swiper初期化スクリプト（window.load後に発火）
    echo '<script>
    window.addEventListener("load", function () {
        new Swiper(".youtube-video-slider", {
            loop: true,                     // 🔁 ループ再生
            spaceBetween: 20,              // スライド間の余白
            slidesPerView: 1,              // デフォルト1枚

            // 📱 ブレークポイントによる表示枚数の切り替え
            breakpoints: {
                576: { slidesPerView: 2 },
                768: { slidesPerView: 3 },
                1024: { slidesPerView: 4 }
            },

            // 🎯 このスライダー専用のナビゲーション
            navigation: {
                nextEl: ".youtube-video-slider .swiper-button-next",
                prevEl: ".youtube-video-slider .swiper-button-prev"
            },

            // 🔘 ページネーションもこのスライダー専用に
            pagination: {
                el: ".youtube-video-slider .swiper-pagination",
                clickable: true
            }
        });
    });
    </script>';
}





// 📌 ギャラリー画像メタボックスを追加
function add_gallery_metabox() {
    add_meta_box(
        'room_gallery_metabox',
        'ギャラリー画像 (最大20枚)',
        'render_gallery_metabox',
        'page', // ✅ 投稿・固定ページで利用可能
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'add_gallery_metabox');

// 📌 ギャラリー画像メタボックスの表示
function render_gallery_metabox($post) {
    $gallery_images = get_post_meta($post->ID, 'room_gallery_images', true);
    if (!is_array($gallery_images)) {
        $gallery_images = !empty($gallery_images) ? explode(',', $gallery_images) : [];
    }
    ?>
    <p>最大20枚まで画像を追加できます。</p>
    <div id="room-gallery-container">
        <ul id="room-gallery-list">
            <?php foreach ($gallery_images as $image_id) : ?>
                <?php $img_url = wp_get_attachment_image_src($image_id, 'thumbnail'); ?>
                <li data-id="<?php echo esc_attr($image_id); ?>">
                    <img src="<?php echo esc_url($img_url[0]); ?>" width="100">
                    <button type="button" class="remove-gallery-image">削除</button>
                </li>
            <?php endforeach; ?>
        </ul>
        <input type="hidden" id="room_gallery_images" name="room_gallery_images" value="<?php echo esc_attr(implode(',', $gallery_images)); ?>">
        <button type="button" id="add-gallery-images" class="button">画像を追加</button>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const mediaButton = document.getElementById("add-gallery-images");
            const galleryList = document.getElementById("room-gallery-list");
            const hiddenInput = document.getElementById("room_gallery_images");

            mediaButton.addEventListener("click", function(event) {
                event.preventDefault();
                let frame = wp.media({
                    title: "ギャラリー画像を選択",
                    library: { type: "image" },
                    multiple: true
                });

                frame.on("select", function() {
                    let selection = frame.state().get("selection").toArray();
                    let imageIds = hiddenInput.value ? hiddenInput.value.split(",") : [];

                    selection.forEach(function(attachment) {
                        if (imageIds.length < 20) {
                            imageIds.push(attachment.id);
                            let listItem = document.createElement("li");
                            listItem.setAttribute("data-id", attachment.id);
                            listItem.innerHTML = `<img src="${attachment.sizes.thumbnail.url}" width="100">
                                <button type="button" class="remove-gallery-image">削除</button>`;
                            galleryList.appendChild(listItem);
                        }
                    });

                    hiddenInput.value = imageIds.join(",");
                });

                frame.open();
            });

            galleryList.addEventListener("click", function(event) {
                if (event.target.classList.contains("remove-gallery-image")) {
                    let listItem = event.target.closest("li");
                    listItem.remove();
                    let imageIds = Array.from(document.querySelectorAll("#room-gallery-list li"))
                        .map(li => li.getAttribute("data-id"));
                    hiddenInput.value = imageIds.join(",");
                }
            });
        });
    </script>
    <?php
}

// 📌 ギャラリー画像データの保存
function save_gallery_metabox($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

    if (isset($_POST['room_gallery_images'])) {
        update_post_meta($post_id, 'room_gallery_images', sanitize_text_field($_POST['room_gallery_images']));
    }
}
add_action('save_post', 'save_gallery_metabox');

// ✅ ページのトップのSwiperスライダー用メタボックスを追加（最大20枚対応）
add_action('add_meta_boxes', function () {
    add_meta_box(
        'custom_image_metabox',
        'スライダー画像（最大20枚）',
        'custom_image_metabox_callback',
        'page',
        'normal',
        'high'
    );
});

function custom_image_metabox_callback($post) {
    for ($i = 1; $i <= 20; $i++) {
        $image_id = get_post_meta($post->ID, "slider_image_$i", true);
        $image_url = $image_id ? wp_get_attachment_image_url($image_id, 'medium') : '';
        echo '<div class="slider-image-block" style="margin-bottom: 10px;">';
        echo '<label>画像'.$i.'：</label><br>';
        echo '<input type="hidden" name="slider_image_'.$i.'" id="slider_image_'.$i.'" value="'.esc_attr($image_id).'">';
        echo '<img id="slider_preview_'.$i.'" src="'.esc_url($image_url).'" style="max-width: 200px; display: '.($image_url ? 'block' : 'none').';"><br>';
        echo '<button type="button" class="select-image-button button" data-target="'.$i.'">画像を選択</button> ';
        echo '<button type="button" class="remove-image-button button" data-target="'.$i.'">削除</button>';
        echo '</div>';
    }
    wp_nonce_field('save_slider_images', 'slider_images_nonce');
    ?>
    <script>
    jQuery(document).ready(function($){
        // 画像選択
        $('.select-image-button').on('click', function(e){
            e.preventDefault();
            var target = $(this).data('target');
            var frame = wp.media({
                title: '画像を選択',
                button: { text: '選択' },
                multiple: false
            });
            frame.on('select', function(){
                var attachment = frame.state().get('selection').first().toJSON();
                $('#slider_image_' + target).val(attachment.id);
                $('#slider_preview_' + target).attr('src', attachment.url).show();
            });
            frame.open();
        });

        // 削除ボタン処理
        $('.remove-image-button').on('click', function(e){
            e.preventDefault();
            var target = $(this).data('target');
            $('#slider_image_' + target).val('');
            $('#slider_preview_' + target).attr('src', '').hide();
        });
    });
    </script>
    <?php
}


// ✅ メタボックスの保存処理（最大20枚）
add_action('save_post', function($post_id) {
    if (!isset($_POST['slider_images_nonce']) || !wp_verify_nonce($_POST['slider_images_nonce'], 'save_slider_images')) return;
    for ($i = 1; $i <= 20; $i++) {
        if (isset($_POST["slider_image_$i"])) {
            update_post_meta($post_id, "slider_image_$i", intval($_POST["slider_image_$i"]));
        }
    }
});
