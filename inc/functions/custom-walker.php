<?php

// 投稿専用フッターメニューから　フッターリンクからpost id と　post prise を　single.php と　カスタム投稿　house から　取得している。
// ニューリンクに動的な情報を追加 現在表示中の投稿ページに応じて、ナビゲーションメニューのリンクに動的なクエリパラメータ（例: post_idやprice）を追加します。
// 例:　元のリンク: https://naigaicorp.net/naigai-tochi/recommended-land/%e5%9c%9f%e5%9c%b0%ef%bc%98.html
// 変更後のリンク: https://naigaicorp.net/test?post_id=53&price=550

class Custom_Walker_Nav_Menu extends Walker_Nav_Menu
{
    // メニューアイテムの出力処理
    public function start_el(&$output, $item, $depth = 0, $args = null, $id = 0)
    {
        // メニューアイテムの元のURLを取得
        $original_url = $item->url;

        // メニューアイテムの名前が「booking」リンクか確認
        if (strpos($original_url, '/booking') !== false) {
            // 現在の投稿ページが存在しているかチェック
            if ($post_id = get_queried_object_id()) {
                $post = get_post($post_id);

                if ($post && in_array($post->post_type, ['post', 'house'])) {
                    $price = get_post_meta($post_id, 'Price', true);
                    $price = $price ? $price : '';

                    $encoded_title = $post->post_title;

                    $days = 14;
                    $now = time();
                    $entry = get_the_time('U', $post_id);
                    $term = ($now - $entry) / 86400;

                    $new_post_id = ($days > $term) ? $post_id : '';

                    $updated_url = home_url('/booking') . '?title=' . urlencode($encoded_title);

                    if ($new_post_id) {
                        $updated_url .= '&new_post_id=' . intval($new_post_id);
                    } else {
                        $updated_url .= '&post_id=' . intval($post_id);
                    }

                    $updated_url .= '&price=' . urlencode($price);

                    $item->url = $updated_url;
                    $item->title = 'この物件の来店予約';
                }
            }
        }

        parent::start_el($output, $item, $depth, $args, $id);
    }
}
