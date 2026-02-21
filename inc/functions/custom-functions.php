<?php
/****************************************
  functions.php に記述するコード
  主な目的：
  - 投稿のカテゴリー・タクソノミー（house-type、regionなど）のリンクを生成する
  - 採用情報（recruitment）というカスタム投稿タイプを作成して管理する
  - 採用情報投稿用のカスタムフィールドを管理画面に追加
****************************************/


/**
 * [category_taxonomy_links] ショートコードの中身
 * 投稿ページ内に「カテゴリーリンク」と「タクソノミーリンク」を表示する
 * 例：
 *   親カテゴリー, 子カテゴリー, 間取り(house-type), 地域(region)
 */
function custom_category_taxonomy_links() {
    // カテゴリーやタクソノミーを分類ごとに格納する配列
    $category_links = [
        'parent' => [],      // 親カテゴリー
        'child' => [],       // 子カテゴリー
        'house-type' => [],  // 間取り
        'region' => []       // 地域
    ];

    /* ====== 投稿のカテゴリーを取得 ====== */
    $categories = get_the_category();
    foreach ($categories as $category) {
        // カテゴリー色を取得（設定がなければ青色に）
        $color = get_term_meta($category->term_id, 'category_color', true) ?: '#007BFF';
        // カテゴリーリンクを作成
        $link = '<a href="' . esc_url(get_category_link($category->term_id)) . '" style="color:' . esc_attr($color) . '; text-decoration: none;" class="category-name">' . esc_html($category->name) . '</a>';
        
        // 親か子かで格納先を変える
        if ($category->parent == 0) {
            $category_links['parent'][$category->term_id] = $link;
        } else {
            $category_links['child'][$category->term_id] = $link;
        }
    }

    /* ====== house-type タクソノミー（例：3LDKなど）を取得 ====== */
    $terms_house_type = get_the_terms(get_the_ID(), 'house-type');
    if ($terms_house_type && !is_wp_error($terms_house_type)) {
        foreach ($terms_house_type as $term) {
            $color = get_term_meta($term->term_id, 'taxonomy_color', true) ?: '#007BFF';
            $category_links['house-type'][$term->term_id] = '<a href="' . esc_url(get_term_link($term)) . '" style="color:' . esc_attr($color) . '; text-decoration: none;" class="category-name">' . esc_html($term->name) . '</a>';
        }
    }

    /* ====== region タクソノミー（例：那須町など）を取得 ====== */
    $terms_region = get_the_terms(get_the_ID(), 'region');
    if ($terms_region && !is_wp_error($terms_region)) {
        foreach ($terms_region as $term) {
            $color = get_term_meta($term->term_id, 'taxonomy_color', true) ?: '#007BFF';
            $category_links['region'][$term->term_id] = '<a href="' . esc_url(get_term_link($term)) . '" style="color:' . esc_attr($color) . '; text-decoration: none;" class="category-name">' . esc_html($term->name) . '</a>';
        }
    }

    /* ====== 配列の順序を整えて出力 ====== */
    foreach ($category_links as &$links) {
        ksort($links); // キーの昇順に並べ替え
    }
    unset($links);

    // 順序：親 → 子 → house-type → region
    return implode(', ', array_merge(
        $category_links['parent'],
        $category_links['child'],
        $category_links['house-type'],
        $category_links['region']
    ));
}
add_shortcode('category_taxonomy_links', 'custom_category_taxonomy_links'); // ショートコード登録


// 必要な場合は他のカスタム関数もここに追加


function register_recruitment_custom_post_type_and_taxonomies()
{
    // Recruitment カスタム投稿タイプを登録
    register_post_type(
        'recruitment',
        array(
            'label' => '採用',
            'labels' => array(
                'add_new' => '新規採用情報追加',
                'edit_item' => '採用情報の編集',
                'view_item' => '採用情報を表示',
                'search_items' => '採用情報を検索',
                'not_found' => '採用情報は見つかりませんでした。',
                'not_found_in_trash' => 'ゴミ箱に採用情報はありませんでした。'
            ),
            'public' => true,
            'description' => '採用情報を管理するカスタム投稿タイプです。',
            'hierarchical' => false,
            'has_archive' => true,
            'show_in_rest' => true,
            'supports' => array(
                'title',
                'editor',
                'thumbnail',
                'excerpt',
                'custom-fields',
                'revisions',
                'comments',
                'trackbacks',
                'page-attributes',
            ),
            'menu_position' => 5,
            'rewrite' => array('slug' => 'recruitment'),
            'taxonomies' => array('recruitment_category')
        )
    );

    // Recruitment カテゴリータクソノミーの登録
    register_taxonomy(
        'recruitment_category',
        'recruitment',
        array(
            'label' => 'Job Categories',
            'labels' => array(
                'popular_items' => 'よく使う職種',
                'edit_item' => '職種を編集',
                'add_new_item' => '新規職種を追加',
                'search_items' => '職種を検索'
            ),
            'public' => true,
            'description' => '採用職種を管理するタクソノミーです。',
            'hierarchical' => true,
            'show_in_rest' => true,
            'rewrite' => array('slug' => 'job-category')
        )
    );

    // Recruitment タグタクソノミーの登録
    register_taxonomy(
        'recruitment_tag',
        'recruitment',
        array(
            'label' => 'Job Tags',
            'labels' => array(
                'popular_items' => 'よく使うタグ',
                'edit_item' => 'タグを編集',
                'add_new_item' => '新規タグを追加',
                'search_items' => 'タグを検索'
            ),
            'public' => true,
            'description' => '採用タグを管理するタクソノミーです。',
            'hierarchical' => false,
            'update_count_callback' => '_update_post_term_count',
            'show_in_rest' => true
        )
    );

    // 各職種タクソノミー（宅建士、Sales、経理、1級建築士など）の追加
    $job_categories = array(
        '宅建士', 'Sales', '経理', 'Accounting', '1級建築士', '2級建築士', 'Architecture Level1', 
        'Architecture Level2', '施工管理士1級', '施工管理士2級', 'その他', 'Open position'
    );

    foreach ($job_categories as $category) {
        wp_insert_term($category, 'recruitment_category');
    }
}

// init アクションフックで登録
add_action('init', 'register_recruitment_custom_post_type_and_taxonomies');

function add_recruitment_metabox() {
    add_meta_box(
        'recruitment_info',
        '募集情報',
        'recruitment_info_callback',
        'recruitment'
    );
}
add_action('add_meta_boxes', 'add_recruitment_metabox');

// メタボックスのコールバック関数
function recruitment_info_callback($post) {
    wp_nonce_field('save_recruitment_metabox_data', 'recruitment_nonce');
    wp_nonce_field('google_map_nonce_action', 'google_map_nonce'); // Googleマップ用のnonce追加

    // フィールドの定義（新しく trial_period と address を追加）
    $fields = [
        'job_title' => '募集職種',
        'age_requirement' => '年齢制限',
        'experience_required' => '経験',
        'education_requirement' => '学歴',
        'Salary' => '給与',
        'location' => '勤務地',
        'trial_period' => '使用期間', // 追加
        'address' => '所在地', // 追加
        'benefits' => '福利厚生',
        'working_hours' => '勤務時間',
        'employment_type' => '雇用形態',
        'remarks' => '備考',
        'holiday' => 'その他・休日',
        'job_description' => 'お仕事の内容',

    ];

    // 各フィールドの入力フォームを表示
    foreach ($fields as $key => $label) {
        $value = get_post_meta($post->ID, $key, true);
        echo '<div class="form-field">';
        echo '<label for="' . esc_attr($key) . '">' . esc_html($label) . ':</label>';

        // 特殊フィールドの処理
        if ($key === 'location') {
            $google_embed_code = get_post_meta($post->ID, 'GoogleEmbedcode', true); // Google 埋め込みコード
            echo '<textarea name="google_embed_code" id="google_embed_code" class="regular-text" placeholder="埋め込みiframeコードを入力" rows="4" cols="50">' . esc_textarea($google_embed_code) . '</textarea><br>';

            // Googleマップのプレビュー表示
            if (!empty($google_embed_code)) {
                echo '<div class="google-map-preview" style="border: 1px solid #ddd; padding: 10px; margin-top: 10px;">';
                echo wp_kses_post($google_embed_code); // iframeコードを表示
                echo '</div>';
            }
        } elseif ($key === 'remarks') {
            echo '<textarea id="' . esc_attr($key) . '" name="' . esc_attr($key) . '">' . esc_textarea($value) . '</textarea>';
        } else {
            echo '<input type="text" id="' . esc_attr($key) . '" name="' . esc_attr($key) . '" value="' . esc_attr($value) . '" />';
        }
        echo '</div>';
    }
}


// 投稿データの保存処理
function save_recruitment_metabox_data($post_id) {
    // オートセーブの確認
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // フィールド名を定義（新しく trial_period と address を追加）
    $fields = ['job_title', 'age_requirement', 'experience_required', 'education_requirement', 'Salary', 'location', 'trial_period', 'address', 'benefits', 'working_hours', 'employment_type', 'remarks','holiday','job_description'];

    // 各フィールドの値を保存
    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            if ($field === 'location') {
                // location フィールドの場合、共通処理で保存
                save_google_embed_code_common($post_id, 'google_map_nonce', 'google_map_nonce_action', 'google_embed_code', 'GoogleEmbedcode');
            } else {
                // その他のフィールド
                $value = ($field === 'remarks') ? sanitize_textarea_field($_POST[$field]) : sanitize_text_field($_POST[$field]);
                update_post_meta($post_id, $field, $value);
            }
        }
    }
}

add_action('save_post', 'save_recruitment_metabox_data', 20, 2); // 優先順位を20に変更






function add_custom_admin_styles() {
    echo '<style>
        .form-field {
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .form-field label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .form-field input[type="text"],
        .form-field textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .form-field textarea {
            height: 100px;
        }
        </style>';
}
add_action('admin_head', 'add_custom_admin_styles');


// 投稿データの取得
global $post;  // WordPressのグローバルポストを取得

// $post が null の場合は処理を終了し、エラーログを出力
if ( !$post ) {
//     error_log('Post is not defined or null.');
    return; // $post が null の場合は処理を終了
}

// フィールドの配列
$fields = [
    '募集職種' => 'job_title',
    '募集対象' => 'age_requirement',
    '実務経験' => 'experience_required',
    '学歴' => 'education_requirement',
    '給与' => 'Salary',
    '勤務地' => 'location',
    '福利厚生' => 'benefits',
    '勤務時間' => 'working_hours',
    '雇用形態' => 'employment_type',
    'GoogleEmbedcode' => 'GoogleMap', // Google 勤務地図
    '備考' => 'remark',
    '仕様期間' => 'trial_period', // 使用期間フィールド
    '所在地' => 'address', // 所在地フィールド
    '休日・その他' =>'holiday',
    'お仕事の内容' => 'job_description',
];

// メタ情報の取得
$recruitment_details = [];
foreach ($fields as $label => $key) {
    $value = get_post_meta(get_the_ID(), $key, true);
    
    // 値が空（null, false, 空文字）の場合は "-" を格納
    $recruitment_details[$label] = isset($value) && $value !== '' ? ($key === 'Price' ? esc_html($value) . ' 万円' : esc_html($value)) : '-';
}

// デバッグ用: $recruitment_details の内容を確認
error_log(print_r($recruitment_details, true));


function display_recruitment_fields_table_0() {
    global $post;

    // 投稿IDを取得
    $post_id = get_the_ID();
    
    // 投稿が存在しない場合は処理を終了
    if (!$post_id) {
        return;
    }

    // カスタムフィールド「job_description」の値を取得
    $job_description = get_post_meta($post_id, 'job_description', true);
    
    // 業務内容が設定されている場合のみ表示
    if (!empty($job_description)) {
        ?>
    <section class="property-details">
        <div class="property-details-info">
            <h2 class="property-title">採用情報</h2>
            <table class="property-table">
                <tbody>
                    <tr>
                        <th>お仕事の内容</th>
                        <td><?php echo esc_html(get_post_meta($post_id, 'job_description', true)); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
        <?php
    }
}



// 表示関数
function display_recruitment_fields_table_1() {
    global $post; // $post をグローバル変数として宣言

    // 投稿が存在しない場合、またはグローバル $post が null の場合
    if (!isset($post) || !$post) {
//         error_log('Post is not defined or null in display_recruitment_fields_table_1.');
        return; // $post が null の場合は処理を終了
    }

    // 投稿IDを取得
    $post_id = get_the_ID();
    
    // 投稿が存在しない場合
    if (!$post_id) {
        error_log('Post ID is not valid.');
        return;
    }

    // Googleマップの埋め込みコード取得
    $google_embed_code = get_post_meta($post_id, 'GoogleEmbedcode', true);
    $iframe_content = '';

    if ($google_embed_code) {
        // 埋め込みコードがiframeタグを含んでいる場合はそのまま使用
        $iframe_content = strpos($google_embed_code, '<iframe') !== false
            ? $google_embed_code
            : '<iframe id="googleMapIframe" src="' . esc_url($google_embed_code) . '" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>';
    } else {
        error_log('Google map embed code is not set or invalid.');
    }

    ?>
    <section class="property-details">
        <div class="property-details-info">
            <h2 class="property-title">採用情報</h2>
            <table class="property-table">
                <tbody>
                    <tr>
                        <th>募集職種</th>
                        <td><?php echo esc_html(get_post_meta($post_id, 'job_title', true)); ?></td>
                        <th>募集対象年齢</th>
                        <td><?php echo esc_html(get_post_meta($post_id, 'age_requirement', true)) ?: '-'; ?>歳以上～</td>
                    </tr>
                    <tr>
                        <th>経験</th>
                        <td><?php echo esc_html(get_post_meta($post_id, 'experience_required', true)) ?: '-'; ?>年以上の経験～</td>
                        <th>学歴</th>
                        <td><?php echo esc_html(get_post_meta($post_id, 'education_requirement', true)); ?></td>
                    </tr>
                    <tr>
                        <th>給与</th>
                        <td>
                            <?php
                            $salary = get_post_meta($post_id, 'Salary', true);
                            echo !empty($salary) ? number_format((int)$salary) . '万円' : '応相談';
                            ?>
                        </td>
                        <th>勤務地</th>
                        <td>
                            <div style="margin-top: 10px;">
                                <span class="google-location-link">
                                    <svg class="icon-location" style="cursor:pointer; width: 20px; height: 20px;" id="iconLocation">
                                        <use xlink:href="#icon-location"></use>
                                    </svg>
                                    <a href="javascript:void(0);" id="openMapLink">Google位置</a>
                                </span>
                            </div>
                            <?php
                            // Googleマップの埋め込みコードがある場合に地図を表示
                            if ($iframe_content) {
                                // モーダルのHTMLを修正
                                echo '<div id="googleMapModal" class="google-map-modal" style="display: none;">
                                        <div class="google-map-modal-content">
                                            <span class="google-map-modal-close" id="closeModal">&times;</span>
                                            ' . $iframe_content . '
                                        </div>
                                      </div>';
                            } else {
                                echo '<p class="no-margin">Googleマップが設定されていません。</p>';
                            }
                            ?>
                            <p><?php echo esc_html(get_post_meta($post_id, 'address', true)) ?: '所在地情報はありません'; ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th>福利厚生</th>
                        <td><?php echo esc_html(get_post_meta($post_id, 'benefits', true)) . ' ' . '完備'; ?></td>
                        <th>仕様期間</th>
                        <td><?php echo esc_html(get_post_meta($post_id, 'trial_period', true)) . 'ヶ月'; ?></td>
                    </tr>
                    <tr>
                        <th>雇用形態</th>
                        <td><?php echo esc_html(get_post_meta($post_id, 'employment_type', true)); ?></td>
                        <th>勤務時間</th>
                        <td><?php echo esc_html(get_post_meta($post_id, 'working_hours', true)); ?></td>
                     </tr>
                     <tr>
                        <th>その他・休日</th>
                        <td><?php echo esc_html(get_post_meta($post_id, 'holiday', true)) . '日間'; ?></td>
                        <th>備考</th>
                        <td><?php echo esc_html(get_post_meta($post_id, 'remarks', true)) ?: '-'; ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
    <?php
}





