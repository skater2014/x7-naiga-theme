<?php
global $post;  // WordPressのグローバルポストを取得

// フィールドの配列
$fields = [
    '募集職種' => 'job_title',        // 職種
    '募集対象' => 'age_requirement',  // 年齢制限
    '実務経験' => 'experience_required',  // 経験
    '学歴'     => 'education_requirement',  // 学歴
    '給与'     => 'Salary',  // 給与
    '福利厚生' => 'benefits',  // 福利厚生
    '勤務時間' => 'working_hours',  // 勤務時間
    '雇用形態' => 'employment_type',  // 雇用形態
    '仕様期間' => 'trial_period',  // 仕様期間
];

// メタ情報の取得
$recruitment_details = [];
foreach ($fields as $label => $key) {
    $value = get_post_meta(get_the_ID(), $key, true);

    if ($key === 'Salary') {
        // 給与の処理：値が null または空文字なら '応相談'
        if ($value === null || $value === '') {
            $value = '応相談';
        } else {
            $value = esc_html($value) . ' 万円～';
        }
    } elseif ($key === 'trial_period') {
        // 仕様期間の特殊処理
        if ($value !== null && $value !== '') {
            $value = esc_html($value) . ' ヶ月間';
        } else {
            $value = $label . '情報が設定されていません。';
        }
    } elseif ($value !== null && $value !== '') {
        // 年齢・経験の特殊フォーマット処理
        if ($key === 'age_requirement') {
            $value = esc_html($value) . ' 歳～';
        } elseif ($key === 'experience_required') {
            $value = esc_html($value) . ' 年～';
        } else {
            $value = esc_html($value);
        }
    } else {
        // その他の項目が未設定の場合のデフォルトメッセージ
        $value = $label . '情報が設定されていません。';
    }

    $recruitment_details[$label] = $value;
}

// フロントエンドでの表示
if (is_singular('recruitment') || is_post_type_archive('recruitment')) {
    // スタイルを追加
    echo '<style>
.recruitment-info-table {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    padding: 5px;
}

.recruitment-row {
    display: flex;
    gap: 10px;
    flex: 1 0 23%;
}

.recruitment-label {
    font-weight: bold;
    flex: 0 0 40%;
}

.recruitment-value {
    flex: 1;
}

.google-location-container {
    display: flex;
    justify-content: space-between;
    margin-top: 20px;
    flex-wrap: nowrap; /* 横並びにする */
}

.google-location {
    flex: 1;
    display: flex;
    align-items: center;
    gap: 10px;
}

.remarks {
    flex: 1;
    display: flex;
    align-items: center;
}



/* タブレットのスタイル（最大1024px） */
@media (max-width: 1024px) {
    .recruitment-row {
        flex-direction: column;
        gap: 10px;
    }

    .recruitment-label {
        flex: 0 0 35%;
        margin-bottom: 5px;
    }

    .recruitment-value {
        flex: 0 0 100%;
    }

    .google-location-container {
        flex-direction: row; /* 変更: 横並びにする */
    }

}

/* モバイルのスタイル */
@media (max-width: 768px) {
    .recruitment-row {
        flex-direction: column;
        gap: 0px;
    }

    .recruitment-label {
        flex: 0 0 30%;
        margin-bottom: 5px;
    }

    .recruitment-value {
        flex: 0 0 100%;
    }

    .google-location-container {
        flex-direction: column; /* 変更: 縦並びにする */
    }

    .google-location {
        margin-bottom: 10px;
    }
}

/* ギャラリービューのモバイルスタイル */
@media (max-width: 768px) {
    .gallery-view .recruitment-row {
        flex-direction: column;
        gap: 0px;
    }

    .gallery-view .recruitment-label {
        flex: 0 0 30%;
        margin-bottom: 5px;
    }

    .gallery-view .recruitment-value {
        flex: 0 0 100%;
    }

    .gallery-view .google-location-container {
        flex-direction: column; /* 変更: 縦並びにする */
    }

    .gallery-view .google-location {
        margin-bottom: 10px;
    }
}

/* ギャラリービューのモバイルスタイル */
    .gallery-view .recruitment-row {
        flex-direction: column;
        gap: 0px;
    }

    .gallery-view .recruitment-label {
        flex: 0 0 30%;
        margin-bottom: 5px;
    }

    .gallery-view .recruitment-value {
        flex: 0 0 100%;
    }

    .gallery-view .google-location-container {
        flex-direction: column; /* 変更: 縦並びにする */
    }

    .gallery-view .google-location {
        margin-bottom: 10px;
    }



    </style>';

    // 情報の表示
    echo '<div class="recruitment-info-table">';
    foreach ($recruitment_details as $label => $value) {
        echo '<div class="recruitment-row">';
        echo '<div class="recruitment-label">' . esc_html($label) . '</div>';
        echo '<div class="recruitment-value">' . $value . '</div>';
        echo '</div>';
    }
    echo '</div>';

    // Googleマップの埋め込みコード取得
    $post_id = get_the_ID();
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

    // 地図と備考の表示
    echo '<div class="google-location-container">';

    // Googleマップの表示
    echo '<div class="google-location">';
    echo '<span class="google-location-link" id="openMapLink">';
    echo '<svg class="icon-location" style="cursor:pointer; width: 20px; height: 20px;" id="iconLocation">';
    echo '<use xlink:href="#icon-location"></use>';
    echo '</svg>';
    echo '<span>' . esc_html(get_post_meta($post_id, 'address', true)) ?: '所在地情報はありません' . '</span>';
    echo '</span>';
    echo '</div>';

    // モーダルのHTML
    if ($iframe_content) {
        echo '<div id="googleMapModal" class="google-map-modal" style="display: none;">
                <div class="google-map-modal-content">
                    <span class="google-map-modal-close" id="closeModal">&times;</span>
                    ' . $iframe_content . '
                </div>
              </div>';
    } else {
        echo '<p class="no-margin">Googleマップが設定されていません。</p>';
    }

    // 備考（remarks）の表示
    echo '<div class="remarks">';
    echo '<p>' . esc_html(get_post_meta($post_id, 'remarks', true)) ?: '備考情報はありません' . '</p>';
    echo '</div>'; // .remarks

    echo '</div>'; // .google-location-container
}
?>
