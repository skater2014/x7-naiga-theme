<?php if ( is_active_sidebar( 'custom_sidebar' ) ) : ?>
    <div id="custom-sidebar">
        <?php dynamic_sidebar( 'custom_sidebar' ); ?>
    </div>
<?php else : ?>
    <p>サイドバーにはウィジェットが追加されていません。</p>
<?php endif; ?>


<aside class="sidebar">
    <!-- サイドバーの他の部分 -->

    <?php
    // カテゴリスラッグの設定（手動で設定するか、必要に応じて変更）
    $category_slug = 'naigai-construction'; // 例：'naigai-construction'

    // 初期設定（デフォルトの価格設定）
    $default_min_price = 1000;
    $default_max_price = 2000;

    // カテゴリごとの価格設定
    if ($category_slug === 'naigai-construction' || $category_slug === 'house') {
        $min_price = 1000;
        $max_price = 4000;  // 最大価格
    } elseif ($category_slug === 'naigai-tochi') {
        $min_price = 100;
        $max_price = 2000;  // 最大価格
    } else {
        $min_price = $default_min_price;
        $max_price = $default_max_price;
    }
    ?>

    <fieldset class="price-filter">
        <legend>価格範囲で絞り込む</legend>
        <div>
            <label for="price_range">価格範囲: ￥<span id="price_range_label_min"><?php echo number_format($min_price); ?>万円</span> 〜 ￥<span id="price_range_label_max"><?php echo number_format($max_price); ?>万円</span></label>

            <input type="range"
                   id="price-range-slider"
                   class="price-filter"
                   min="<?php echo $min_price; ?>"
                   max="<?php echo $max_price; ?>"
                   step="100"
                   value="<?php echo $min_price; ?>"
                   onchange="updatePriceRange();"
                   oninput="updatePriceRange();"
                   style="width: 100%">
        </div>
    </fieldset>

    <!-- サイドバーの他の部分 -->
</aside>
