<?php
/**
 * 固定ページのメタボックス追加（Room Gallery用）
 * 
 * - 特定のテンプレート (`page-gallary-room.php`) でのみメタボックスを表示
 * - 最大20枚の画像をアップロード可能
 * - 画像データは `post_meta` に保存
 */

// メタボックス追加関数
if ( ! function_exists( 'add_room_gallery_metabox' ) ) {
    function add_room_gallery_metabox() {
        global $post;

        // 現在の投稿が `page-gallary-room.php` テンプレートを使用している場合のみメタボックスを追加
        if ( $post && 'page-gallary-room.php' === get_page_template_slug( $post->ID ) ) {
            add_meta_box(
                'room_gallery_metabox',
                'Room Gallery Images',  
                'room_gallery_metabox_callback',
                'page',  
                'normal', 
                'high'  
            );
        }
    }
    add_action( 'add_meta_boxes', 'add_room_gallery_metabox' );
}

// メタボックスのHTML構築
if ( ! function_exists( 'room_gallery_metabox_callback' ) ) {
    function room_gallery_metabox_callback( $post ) {
        // セキュリティ用nonceフィールド
        wp_nonce_field( 'room_gallery_save', 'room_gallery_nonce' );

        // 既存の画像IDを取得（カンマ区切りの文字列として保存されている）
        $gallery_images = get_post_meta( $post->ID, 'room_gallery_images', true );
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

    // 画像追加ボタン
    $('#room-gallery-add').on('click', function(e){
        e.preventDefault();
        if (frame) {
            frame.open();
            return;
        }
        frame = wp.media({
            title: 'Select Gallery Images',
            button: { text: 'Add to gallery' },
            multiple: true,
            library: { type: 'image' },
            query: { posts_per_page: -1 } // 🔥 上限解除（10件制限を回避）
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
            if (ids.length > 20) {
                alert('You can only upload up to 20 images.');
                ids = ids.slice(0, 20);
            }

            $('#room_gallery_images').val(ids.join(','));
        });

        frame.open();
    });

    // 画像削除ボタン
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
}

// メタボックスのデータを保存
if ( ! function_exists( 'room_gallery_save_meta' ) ) {
    function room_gallery_save_meta( $post_id ) {
        // nonce チェック
        if ( ! isset( $_POST['room_gallery_nonce'] ) || ! wp_verify_nonce( $_POST['room_gallery_nonce'], 'room_gallery_save' ) ) {
            return;
        }

        // オートセーブ時はスキップ
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        // 権限チェック
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        // 送信された画像IDをサニタイズして保存
        if ( isset( $_POST['room_gallery_images'] ) ) {
            $ids = sanitize_text_field( $_POST['room_gallery_images'] );
            update_post_meta( $post_id, 'room_gallery_images', $ids );
        }
    }
    add_action( 'save_post', 'room_gallery_save_meta' );
}
