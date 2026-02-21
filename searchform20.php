<form role="search" method="get" class="search-form-20" action="<?php echo esc_url(home_url('/')); ?>">
    <!-- house_type の選択 -->
    <select name="house_type">
        <option value="">間取り</option>
        <?php
        $house_type_terms = get_terms(array(
            'taxonomy' => 'house-type',
            'orderby' => 'name',
            'hide_empty' => false,
        ));

        if (!empty($house_type_terms) && !is_wp_error($house_type_terms)) {
            foreach ($house_type_terms as $term) {
                $selected = (isset($_GET['house_type']) && $_GET['house_type'] === $term->slug) ? 'selected' : '';
                echo '<option value="' . esc_attr($term->slug) . '" ' . $selected . '>' . esc_html($term->name) . '</option>';
            }
        }
        ?>
    </select>

    <!-- region の選択 -->
    <select name="region">
        <option value="">地域</option>
        <?php
        $region_terms = get_terms(array(
            'taxonomy' => 'region',
            'orderby' => 'name',
            'hide_empty' => false,
        ));

        if (!empty($region_terms) && !is_wp_error($region_terms)) {
            foreach ($region_terms as $term) {
                $selected = (isset($_GET['region']) && $_GET['region'] === $term->slug) ? 'selected' : '';
                echo '<option value="' . esc_attr($term->slug) . '" ' . $selected . '>' . esc_html($term->name) . '</option>';
            }
        }
        ?>
    </select>

    <!-- 検索フォーム -->
    <label>
        <input type="text" placeholder="検索" name="s" value="<?php echo get_search_query(); ?>">
    </label>

    <!-- 検索アイコン -->
    <button type="submit" aria-label="Search">
        <i class="fas fa-search"></i> <!-- アイコン追加 -->
    </button>
</form>





<style>
.search-form-20 {
    display: flex;
    justify-content: space-between;
    align-items: center;
    overflow: hidden;
    border: 2px solid #2589d0;
    border-radius: 5px;
    max-width: 480px; /* フォームの幅を広げてより余裕を持たせる */
    height: 45px; /* 高さを少し高めに調整 */
    /*padding: 5px; /* 内側の余白を少し調整 */
    background-color: #f5f5f5; /* 背景色を薄いグレーにして落ち着いた印象 */
}

.search-form-20 input[type="text"] {
    width: 140px; /* 入力欄の幅を広げて余裕を持たせる */
    padding: 8px 15px;
    font-size: 1em;
    border: 1px solid #ddd;
    background: #fff;
    color: #333;
    outline: none;
    box-sizing: border-box;
    border-radius: 3px; /* 入力欄の角を丸める */
    /*margin-right: 10px; /* 入力欄とセレクトボックスの間の隙間 */
    transition: border-color 0.3s ease; /* フォーカス時に枠線の色を変える */
}

.search-form-20 input[type="text"]:focus {
    border-color: #2589d0; /* フォーカス時に枠線が青く変わる */
}

.search-form-20 select {
    width: 100px; /* セレクトボックスの幅を広げる */
    padding: 8px;
    font-size: 1em;
    border: 1px solid #ddd;
    background: #fff;
    color: #333;
    outline: none;
    box-sizing: border-box;
    border-radius: 3px;
    /*margin-right: 10px; /* セレクトボックスとボタンの間に隙間 */
    transition: border-color 0.3s ease; /* フォーカス時に枠線の色を変える */
}

.search-form-20 select:focus {
    border-color: #2589d0; /* フォーカス時に枠線が青く変わる */
}

.search-form-20 button {
    width: 45px; /* ボタンの幅を少し広げて押しやすく */
    height: 45px; /* ボタンの高さを調整して中央に揃える */
    border: none;
    background-color: #2589d0; /* ボタンの背景色 */
    color: #fff; /* ボタンのテキスト色 */
    font-size: 1.2em; /* テキストのフォントサイズを調整 */
    cursor: pointer;
    display: flex;
    justify-content: center;
    align-items: center;
    border-radius: 5px; /* ボタンの角を丸める */
    transition: background-color 0.3s ease; /* ホバー時に色が変わる */
}

.search-form-20 button:hover {
    background-color: #1e74b8; /* ホバー時にボタンの色を変更 */
}

.search-form-20 button::after {
    width: 18px;
    height: 18px;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath d='M18.031 16.6168L22.3137 20.8995L20.8995 22.3137L16.6168 18.031C15.0769 19.263 13.124 20 11 20C6.032 20 2 15.968 2 11C2 6.032 6.032 2 11 2C15.968 2 20 6.032 20 11C20 13.124 19.263 15.0769 18.031 16.6168ZM16.0247 15.8748C17.2475 14.6146 18 12.8956 18 11C18 7.1325 14.8675 4 11 4C7.1325 4 4 7.1325 4 11C4 14.8675 7.1325 18 11 18C12.8956 18 14.6146 17.2475 15.8748 16.0247L16.0247 15.8748Z' fill='%23fff'%3E%3C/path%3E%3C/svg%3E");
    background-repeat: no-repeat;
    content: '';
}


</style>
