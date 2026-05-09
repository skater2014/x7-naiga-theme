<?php
/**
 * iez_plan 住宅特徴カタログ
 *
 * 重要:
 * - SVGを投稿メタへ保存しない。
 * - 特徴タイトル・説明文・SVGはPHP側で固定管理する。
 * - 投稿側には feature_keys / option_keys だけ保存する。
 *
 * 今回の追加:
 * - iez_plan_type: used-renovation
 *   または
 * - iez_plan_feature: existing-house
 *
 * を持つ投稿では、中古住宅リフォーム用の特徴セットを自動で表示する。
 *
 * 目的:
 * - 新築向けの「耐震性・基礎・屋根...」だけではなく、
 *   中古住宅リフォームのページ内容に合う
 *   「中古住宅診断」「Before / After」「間取り変更」「外観刷新」「水回り更新」
 *   を先に表示するため。
 */

if (!defined('ABSPATH')) {
    exit;
}

function naigai_iez_plan_feature_catalog() {
    return array(
        'new_build' => array(
            'title' => '新築計画',
            'text'  => '土地条件や家族構成に合わせて、はじめから暮らしやすい住まいを計画します。',
            'icon'  => 'new_build',
        ),
        'renovation' => array(
            'title' => 'リフォーム住宅',
            'text'  => '既存建物の状態を確認し、断熱・水回り・動線を見直して住みやすく整えます。',
            'icon'  => 'renovation',
        ),

        /**
         * ---------------------------------------------------------
         * 中古住宅リフォーム用の特徴
         * ---------------------------------------------------------
         *
         * 役割:
         * - used-renovation の詳細ページで自動表示する。
         * - Before / After 型の中古住宅ページに合わせて、
         *   既存建物確認、間取り変更、外観刷新、水回り更新を前面に出す。
         */
        'used_house_check' => array(
            'title' => '中古住宅診断',
            'text'  => '既存建物の状態を確認し、使える部分と直す部分を整理してリフォーム計画を立てます。',
            'icon'  => 'used_house',
        ),
        'before_after' => array(
            'title' => 'Before / After',
            'text'  => '外観・内装・水回りの変化を比較し、リフォーム後の暮らしを分かりやすく確認できます。',
            'icon'  => 'before_after',
        ),
        'layout_change' => array(
            'title' => '間取り変更',
            'text'  => '既存の部屋割りや動線を見直し、今の暮らしに合う使いやすい間取りへ整えます。',
            'icon'  => 'layout_change',
        ),
        'exterior_refresh' => array(
            'title' => '外観刷新',
            'text'  => '外壁・玄関まわり・外構を整え、古さを抑えて明るく清潔感のある印象に変えます。',
            'icon'  => 'exterior_refresh',
        ),
        'water_area_renovation' => array(
            'title' => '水回り更新',
            'text'  => 'キッチン、洗面、浴室、トイレを見直し、掃除のしやすさと日常の使いやすさを高めます。',
            'icon'  => 'water_area',
        ),
        'maintenance_easy' => array(
            'title' => '管理しやすい住まい',
            'text'  => '外装・設備・収納を整え、住み始めた後の手入れや維持管理もしやすい住まいにします。',
            'icon'  => 'maintenance',
        ),

        'one_floor' => array(
            'title' => 'ワンフロア動線',
            'text'  => '平屋ならではの移動しやすい動線で、日常の暮らしやすさを高めます。',
            'icon'  => 'one_floor',
        ),
        'stairs' => array(
            'title' => '階段計画',
            'text'  => '2階建てでは、階段位置や上下階の動線を整理し、使いやすさを考えます。',
            'icon'  => 'stairs',
        ),
        'second_floor' => array(
            'title' => '2階動線',
            'text'  => '寝室・子ども部屋・収納など、2階の使い方に合わせて動線を整えます。',
            'icon'  => 'second_floor',
        ),

        'earthquake' => array(
            'title' => '耐震性',
            'text'  => '構造・基礎・柱の考え方を整理し、安心して暮らせる住まいを目指します。',
            'icon'  => 'shield',
        ),
        'foundation' => array(
            'title' => '基礎',
            'text'  => '建物を支える基礎部分を重視し、長く住むための土台を整えます。',
            'icon'  => 'foundation',
        ),
        'roof' => array(
            'title' => '屋根',
            'text'  => '那須の気候や積雪・雨風を考え、住まいを守る屋根計画を検討します。',
            'icon'  => 'roof',
        ),
        'wall' => array(
            'title' => '壁・外装',
            'text'  => '外観デザインだけでなく、耐久性やメンテナンス性にも配慮します。',
            'icon'  => 'wall',
        ),
        'sash' => array(
            'title' => '樹脂サッシ',
            'text'  => '窓まわりの断熱性を高め、冬の冷え込みや結露対策にもつなげます。',
            'icon'  => 'window',
        ),
        'cold_area' => array(
            'title' => '寒冷地対応',
            'text'  => '寒さを考え、断熱・窓・換気を含めた快適性を検討します。',
            'icon'  => 'snow',
        ),
        'unit_bath' => array(
            'title' => 'ユニットバス',
            'text'  => '掃除しやすさ、断熱性、将来の使いやすさを考えた水まわりを選べます。',
            'icon'  => 'bath',
        ),
        'toilet' => array(
            'title' => 'トイレ',
            'text'  => '日常の使いやすさと掃除のしやすさを考え、配置や設備を整理できます。',
            'icon'  => 'toilet',
        ),
        'barrier_free' => array(
            'title' => 'バリアフリー',
            'text'  => '将来の暮らしやすさを考え、段差や動線を整理できます。',
            'icon'  => 'accessibility',
        ),
        'storage' => array(
            'title' => '収納計画',
            'text'  => '各所に収納を設け、生活感を抑えたすっきりした暮らしを目指します。',
            'icon'  => 'storage',
        ),

        'deck' => array(
            'title' => 'ウッドデッキ',
            'text'  => '庭とのつながりをつくり、外時間を楽しめる住まいにできます。',
            'icon'  => 'deck',
        ),
        'parking' => array(
            'title' => '駐車スペース',
            'text'  => '敷地条件に合わせて、車の出入りや来客時の使いやすさを考えます。',
            'icon'  => 'car',
        ),
        'work_space' => array(
            'title' => 'ワークスペース',
            'text'  => '在宅作業や趣味の時間に使える小さな作業場所を計画できます。',
            'icon'  => 'desk',
        ),
        'solar' => array(
            'title' => '太陽光対応',
            'text'  => '将来の太陽光や省エネ設備を見据えた計画も検討できます。',
            'icon'  => 'solar',
        ),
        'pet' => array(
            'title' => 'ペット対応',
            'text'  => '床材・動線・掃除のしやすさなど、ペットとの暮らしにも配慮できます。',
            'icon'  => 'pet',
        ),
    );
}

function naigai_iez_plan_base_feature_keys() {
    return array(
        'earthquake',
        'foundation',
        'roof',
        'wall',
        'sash',
        'cold_area',
        'unit_bath',
        'toilet',
        'barrier_free',
        'storage',
    );
}

/**
 * 中古住宅リフォーム用のデフォルト特徴キー。
 *
 * used-renovation の投稿では、汎用特徴の代わりにこれを先頭に使う。
 */
function naigai_iez_plan_used_renovation_feature_keys() {
    return array(
        'used_house_check',
        'before_after',
        'layout_change',
        'exterior_refresh',
        'water_area_renovation',
        'storage',
        'cold_area',
        'maintenance_easy',
    );
}

function naigai_iez_plan_parse_key_list($raw) {
    if (is_array($raw)) {
        return array_values(array_filter(array_map('sanitize_key', $raw)));
    }

    $raw = (string) $raw;

    if ($raw === '') {
        return array();
    }

    $decoded = json_decode($raw, true);
    if (is_array($decoded)) {
        return array_values(array_filter(array_map('sanitize_key', $decoded)));
    }

    return array_values(array_filter(array_map('sanitize_key', preg_split('/[\s,]+/', $raw))));
}

function naigai_iez_plan_has_term_slug($post_id, $taxonomy, $slug) {
    $terms = get_the_terms($post_id, $taxonomy);

    if (!$terms || is_wp_error($terms)) {
        return false;
    }

    foreach ($terms as $term) {
        if ($term->slug === $slug) {
            return true;
        }
    }

    return false;
}

/**
 * 中古住宅リフォーム投稿かどうかを判定する。
 *
 * 判定対象:
 * - iez_plan_type: used-renovation
 * - iez_plan_feature: existing-house
 */
function naigai_iez_plan_is_used_renovation($post_id) {
    return naigai_iez_plan_has_term_slug($post_id, 'iez_plan_type', 'used-renovation')
        || naigai_iez_plan_has_term_slug($post_id, 'iez_plan_feature', 'existing-house');
}

function naigai_iez_plan_resolve_feature_keys($post_id) {
    /**
     * 住宅特徴の決定順:
     *
     * 1. 中古住宅リフォームなら中古住宅用セットを使う。
     * 2. _ch_plan_feature_keys があれば追加で反映する。
     * 3. 2階建てなら stairs / second_floor を追加する。
     * 4. _ch_plan_option_keys があれば最後に足す。
     */
    $is_used_renovation = naigai_iez_plan_is_used_renovation($post_id);
    $manual = naigai_iez_plan_parse_key_list(get_post_meta($post_id, '_ch_plan_feature_keys', true));

    if ($is_used_renovation) {
        $keys = naigai_iez_plan_used_renovation_feature_keys();

        if (!empty($manual)) {
            $keys = array_merge($keys, $manual);
        }
    } elseif (!empty($manual)) {
        $keys = $manual;
    } else {
        $keys = naigai_iez_plan_base_feature_keys();
    }

    if (!$is_used_renovation && naigai_iez_plan_has_term_slug($post_id, 'iez_plan_scope', 'new-build')) {
        array_unshift($keys, 'new_build');
    }

    if (!$is_used_renovation && naigai_iez_plan_has_term_slug($post_id, 'iez_plan_scope', 'renovation')) {
        array_unshift($keys, 'renovation');
    }

    $floor_type = get_post_meta($post_id, '_ch_plan_floor_type', true);

    if ($floor_type === 'two_story' || naigai_iez_plan_has_term_slug($post_id, 'iez_plan_building_form', 'two-story')) {
        $keys[] = 'stairs';
        $keys[] = 'second_floor';
    } else {
        $keys[] = 'one_floor';
    }

    $option_keys = naigai_iez_plan_parse_key_list(get_post_meta($post_id, '_ch_plan_option_keys', true));
    $keys = array_merge($keys, $option_keys);

    return array_values(array_unique(array_filter($keys)));
}

function naigai_iez_plan_resolve_feature_items($post_id) {
    $catalog = naigai_iez_plan_feature_catalog();
    $items   = array();

    foreach (naigai_iez_plan_resolve_feature_keys($post_id) as $key) {
        if (isset($catalog[$key])) {
            $item        = $catalog[$key];
            $item['key'] = $key;
            $items[]     = $item;
        }
    }

    return $items;
}

if (!function_exists('naigai_iez_plan_detail_icon_svg')) {
    function naigai_iez_plan_detail_icon_svg($icon) {
        $icon = sanitize_key($icon);

        $paths = array(
            'shield' => '<path d="M24 6l12 4v10c0 8-5 15-12 18-7-3-12-10-12-18V10l12-4z"/><path d="M19 24l4 4 7-9"/>',
            'foundation' => '<path d="M13 30h22"/><path d="M16 26h16"/><path d="M19 22h10"/><path d="M24 12v10"/><path d="M18 16h12"/>',
            'roof' => '<path d="M10 24l14-12 14 12"/><path d="M15 23v13h18V23"/><path d="M21 36V26h6v10"/>',
            'wall' => '<path d="M10 14h28v22H10z"/><path d="M10 22h28"/><path d="M10 30h28"/><path d="M19 14v8"/><path d="M29 22v8"/><path d="M19 30v6"/>',
            'window' => '<path d="M14 10h20v28H14z"/><path d="M24 10v28"/><path d="M14 24h20"/>',
            'snow' => '<path d="M24 8v32"/><path d="M12 16l24 16"/><path d="M36 16L12 32"/><path d="M18 11l6 5 6-5"/><path d="M18 37l6-5 6 5"/>',
            'bath' => '<path d="M13 24h25v4c0 6-5 10-11 10h-3c-6 0-11-4-11-10v-4z"/><path d="M16 24V13c0-3 2-5 5-5 2 0 4 1 5 3"/><path d="M25 12h7"/>',
            'toilet' => '<path d="M18 8h12v15H18z"/><path d="M16 23h16v5c0 5-3 9-8 9s-8-4-8-9v-5z"/><path d="M20 37h8"/>',
            'accessibility' => '<circle cx="24" cy="10" r="3"/><path d="M24 14v13"/><path d="M15 19h18"/><path d="M24 27l-7 11"/><path d="M24 27l7 11"/>',
            'deck' => '<path d="M10 30h28"/><path d="M14 20h20v10H14z"/><path d="M18 30v8"/><path d="M30 30v8"/><path d="M14 24h20"/>',
            'car' => '<path d="M12 28h24l-3-8H15l-3 8z"/><path d="M14 28v7"/><path d="M34 28v7"/><circle cx="18" cy="34" r="2"/><circle cx="30" cy="34" r="2"/>',
            'storage' => '<path d="M15 12h18v26H15z"/><path d="M15 20h18"/><path d="M15 28h18"/><path d="M21 16h6"/><path d="M21 24h6"/><path d="M21 32h6"/>',
            'stairs' => '<path d="M10 36h8v-6h8v-6h8v-6h4"/><path d="M10 40h28"/>',
            'second_floor' => '<path d="M10 34h28"/><path d="M14 34V18l10-8 10 8v16"/><path d="M18 34v-8h6v8"/><path d="M28 20h5"/><path d="M28 25h5"/>',
            'new_build' => '<path d="M10 24l14-12 14 12"/><path d="M15 23v14h18V23"/><path d="M24 17v20"/><path d="M17 37h14"/>',
            'renovation' => '<path d="M11 34l10-10"/><path d="M18 21l5 5"/><path d="M26 12l10 10"/><path d="M31 17L18 30"/><path d="M28 28h9v9h-9z"/>',
            'one_floor' => '<path d="M9 30h30"/><path d="M13 30V20l11-8 11 8v10"/><path d="M19 30v-7h10v7"/>',

            /**
             * 中古住宅リフォーム用SVG。
             */
            'used_house' => '<path d="M9 24l15-12 15 12"/><path d="M14 22v17h20V22"/><path d="M19 39V28h10v11"/><path d="M31 12l6 6"/><path d="M35 8l6 6"/><path d="M33 30l6 6"/><path d="M39 30l-6 6"/>',
            'before_after' => '<path d="M9 12h12v24H9z"/><path d="M27 12h12v24H27z"/><path d="M23 24h4"/><path d="M25 21l3 3-3 3"/><path d="M13 18h4M13 24h4M31 18h4M31 24h4M31 30h4"/>',
            'layout_change' => '<path d="M10 12h28v24H10z"/><path d="M19 12v24"/><path d="M10 24h18"/><path d="M28 24h10"/><path d="M15 18h8"/><path d="M25 30l5-5 5 5"/><path d="M30 25v9"/>',
            'exterior_refresh' => '<path d="M8 25l16-13 16 13"/><path d="M13 23v16h22V23"/><path d="M18 39V29h8v10"/><path d="M31 28h5"/><path d="M36 10l2 4 4 2-4 2-2 4-2-4-4-2 4-2 2-4"/>',
            'water_area' => '<path d="M11 25h26v5a8 8 0 0 1-8 8H19a8 8 0 0 1-8-8v-5z"/><path d="M16 25V14a5 5 0 0 1 5-5h2"/><path d="M23 13h8"/><path d="M34 10c2 3 3 5 3 7"/><path d="M18 38l-2 4M32 38l2 4"/>',
            'maintenance' => '<path d="M13 34l11-11"/><path d="M19 20l5 5"/><path d="M30 11l7 7"/><path d="M33 14 20 27"/><path d="M10 39h28"/><path d="M29 29h8v8h-8z"/>',

            'desk' => '<path d="M10 24h28"/><path d="M14 24v12"/><path d="M34 24v12"/><path d="M17 18h14v6"/>',
            'solar' => '<circle cx="24" cy="20" r="6"/><path d="M24 7v5"/><path d="M24 28v5"/><path d="M11 20h5"/><path d="M32 20h5"/><path d="M15 11l3 3"/><path d="M33 11l-3 3"/><path d="M12 37h24"/>',
            'pet' => '<circle cx="18" cy="18" r="3"/><circle cx="30" cy="18" r="3"/><circle cx="16" cy="27" r="3"/><circle cx="32" cy="27" r="3"/><path d="M24 24c-5 0-8 4-8 8 0 3 2 5 5 4l3-1 3 1c3 1 5-1 5-4 0-4-3-8-8-8z"/>',
            'home' => '<path d="M10 24l14-12 14 12"/><path d="M15 23v14h18V23"/><path d="M21 37V27h6v10"/>',
        );

        $path = isset($paths[$icon]) ? $paths[$icon] : $paths['home'];

        return '<svg viewBox="0 0 48 48" aria-hidden="true" focusable="false">' . $path . '</svg>';
    }
}
