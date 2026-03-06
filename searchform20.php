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