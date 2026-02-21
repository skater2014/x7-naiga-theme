<?php
/*
  slick-vertical-slider.php
  このファイルは、Breaking Newsと最近の投稿一覧を垂直スライダーで表示します。
*/

// 最近の投稿を取得するクエリ
$args = array(
  'posts_per_page' => 10, // 表示する記事の数
);

$customPosts = get_posts($args);

// 投稿があるかどうかを確認
if ($customPosts) : ?>
  <div>Breaking News<svg class="icon icon-newspaper"><use xlink:href="#icon-newspaper"></use></svg></div>
  <ul class="gallery-slider">
    <?php
    // 各投稿にループ
    foreach ($customPosts as $post) :
      setup_postdata($post);
    ?>
      <li>
        <a href="<?php the_permalink(); ?>">
          <?php the_title(); ?> <!-- 記事タイトルを表示 -->
        </a>
      </li>
    <?php endforeach; ?>
  </ul>
<?php else : // 投稿が見つからない場合 ?>
  <p>このカテゴリーにはまだ記事がありません</p>
<?php endif;

// 投稿データをリセット
wp_reset_postdata();
?>
