<?php

/**
 * =========================================================
 * hub/pages/iezukuri/templates/top/section-plan-fragment.php
 * =========================================================
 *
 * /iezukuri/ トップ：3カード下の切替 fragment
 *
 * 対象ページ:
 * - /iezukuri/ トップページ
 *
 * このファイルの役割:
 * - /iezukuri/ トップの「3つの住まい」カードをクリックした後に表示する
 *   差し替え用HTMLを作る
 *
 * 何を表示するか:
 * - 施工事例
 * - 間取りとプラン
 * - 住宅の特徴
 * - CTA
 *
 * 重要:
 * - このファイル自体はAJAX通信をしない
 * - AJAXで呼び出すPHP側、または別テンプレートから include される想定
 * - JSが取得したHTMLを [data-iez-plan-fragment] の中へ innerHTML で差し込む
 *
 * 紐づくJS:
 * - /iezukuri/ トップの3カード切替JS
 * - action=naigai_iez_plan_fragment をPOSTするJS
 * - JS側では json.data.html としてこのHTMLを受け取る想定
 *
 * 紐づくHTML:
 * - クリック側:
 *   [data-iez-plan-card]
 *
 * - 差し込み先:
 *   [data-iez-plan-fragment]
 *
 * 紐づくCPT:
 * - iez_plan
 *
 * 紐づくタクソノミー:
 * - iez_plan_type
 *
 * 受け取る変数:
 * - $iez_plan_term_slug
 *
 * 例:
 * - one-family
 * - two-family
 * - dual-life
 *
 * この変数を使って、該当する iez_plan を1件取得する。
 *
 * 注意:
 * - ここでは posts_per_page => 1 なので、同じタームに複数の間取りがあっても
 *   最初の1件だけ表示する
 *
 * - Site Reading / Flow などの大きなセクションはここでは出さない
 *   ここは3カード下に出す「横長テーブル」だけを返す場所
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * =========================================================
 * 01. 取得対象タクソノミーとタームslugの準備
 * =========================================================
 *
 * 役割:
 * - どの住宅タイプの間取りを表示するか決める
 *
 * $taxonomy:
 * - iez_plan_type 固定
 *
 * $term_slug:
 * - 外側から渡された $iez_plan_term_slug を sanitize_title() して使う
 *
 * 紐づくJS:
 * - JSがカードの data-iez-plan-term または data-term を読み、
 *   AJAXで term としてPHP側へ送る
 *
 * 紐づくPHP:
 * - AJAXハンドラー側で $iez_plan_term_slug に値を入れて、
 *   このファイルを include する想定
 */
$taxonomy  = 'iez_plan_type';
$term_slug = isset($iez_plan_term_slug) ? sanitize_title($iez_plan_term_slug) : '';

/**
 * =========================================================
 * 02. ターム取得
 * =========================================================
 *
 * 役割:
 * - one-family / two-family / dual-life などのslugから
 *   iez_plan_type のターム情報を取得する
 */
$term = $term_slug ? get_term_by('slug', $term_slug, $taxonomy) : null;

/**
 * タームが存在しない場合の表示
 *
 * 例:
 * - JSから空のtermが来た
 * - 存在しないslugが来た
 * - iez_plan_type に該当タームが登録されていない
 */
if (!$term || is_wp_error($term)) {
    echo '<div class="ch-plan-fragment"><p class="ch-plan-fragment__empty">表示する種類が見つかりません。</p></div>';
    return;
}

/**
 * =========================================================
 * 03. 該当タームに紐づく iez_plan を1件取得
 * =========================================================
 *
 * 役割:
 * - クリックされた住宅タイプに対応する間取り詳細ページを探す
 *
 * post_type:
 * - iez_plan
 *
 * taxonomy:
 * - iez_plan_type
 *
 * posts_per_page:
 * - 1
 *
 * 注意:
 * - 複数件表示ではなく、代表プラン1件だけを表示する設計
 * - 複数プランを出したい場合は posts_per_page を増やし、
 *   下のHTML出力もループ対応に変える必要がある
 */
$q = new WP_Query(array(
    'post_type'      => 'iez_plan',
    'post_status'    => 'publish',
    'posts_per_page' => 1,
    'tax_query'      => array(
        array(
            'taxonomy' => $taxonomy,
            'field'    => 'term_id',
            'terms'    => array((int) $term->term_id),
        ),
    ),
));

/**
 * 該当する iez_plan が無い場合の表示
 */
if (!$q->have_posts()) {
    echo '<div class="ch-plan-fragment"><p class="ch-plan-fragment__empty">この種類に紐づく間取り詳細ページはまだありません。</p></div>';
    return;
}

/**
 * WP_Query の投稿をセットする
 *
 * ここから get_the_ID(), get_the_title(), get_permalink() が
 * 取得した iez_plan の情報になる
 */
$q->the_post();

$plan_id    = get_the_ID();
$plan_title = get_the_title();
$plan_url   = get_permalink();

/**
 * =========================================================
 * 04. メタ値から添付画像IDを拾うヘルパー
 * =========================================================
 *
 * 関数:
 * - naigai_iez_fragment_collect_ids()
 *
 * 役割:
 * - iez_plan のメタ値から画像IDらしき数値を集める
 *
 * 対応している形式:
 * - 配列
 * - serializeされた値
 * - JSON文字列
 * - 数値
 * - 数値を含む文字列
 *
 * なぜ必要か:
 * - 画像メタの保存形式が統一されていない場合でも、
 *   できるだけ添付画像IDを拾えるようにするため
 *
 * 注意:
 * - 文字列内の2桁以上の数字も拾うため、
 *   画像IDではない数字を拾う可能性もある
 * - 後続処理で get_post_type($id) === 'attachment' を確認して絞り込む
 */
function naigai_iez_fragment_collect_ids($value)
{
    $ids = array();

    /**
     * 配列なら中身を再帰的に確認する
     */
    if (is_array($value)) {
        foreach ($value as $v) {
            $ids = array_merge($ids, naigai_iez_fragment_collect_ids($v));
        }
        return $ids;
    }

    /**
     * serializeされている可能性があるので maybe_unserialize する
     */
    $value = maybe_unserialize($value);

    /**
     * unserialize後に配列になった場合は、再帰的に確認する
     */
    if (is_array($value)) {
        return naigai_iez_fragment_collect_ids($value);
    }

    /**
     * 数値なら添付画像ID候補として追加する
     */
    if (is_numeric($value)) {
        $ids[] = (int) $value;
        return $ids;
    }

    /**
     * 文字列の場合:
     * - JSONとして読めるならJSONを再帰処理
     * - JSONでなければ、文字列中の2桁以上の数字を拾う
     */
    if (is_string($value)) {
        $json = json_decode($value, true);
        if (is_array($json)) {
            return naigai_iez_fragment_collect_ids($json);
        }

        preg_match_all('/\b\d{2,}\b/', $value, $m);
        foreach ($m[0] as $num) {
            $ids[] = (int) $num;
        }
    }

    return $ids;
}

/**
 * =========================================================
 * 05. 施工事例画像 / 間取り画像を取得するヘルパー
 * =========================================================
 *
 * 関数:
 * - naigai_iez_fragment_media_items()
 *
 * 役割:
 * - iez_plan の全メタを見て、
 *   施工事例画像または間取り画像を集める
 *
 * 引数:
 * - $post_id
 *   対象の iez_plan ID
 *
 * - $mode
 *   'work' : 施工事例画像を取得
 *   'plan' : 平面図・配置図・間取り画像を取得
 *
 * 判断方法:
 * - メタキー名に含まれる文字で分類する
 *
 * plan と判断する文字:
 * - floor
 * - plan
 * - heimen
 * - madori
 * - site
 * - haichi
 * - 配置
 * - 平面
 * - 間取り
 *
 * work と判断する文字:
 * - interior
 * - room
 * - works
 * - gallery
 * - photo
 * - reference
 * - 内装
 * - 施工
 * - 写真
 *
 * 注意:
 * - work では外観/exteriorを除外している
 * - plan では画像タイトル・alt・URLにも
 *   floor / plan / site / 平面 / 配置 / 間取り などが含まれるか確認している
 */
function naigai_iez_fragment_media_items($post_id, $mode)
{
    /**
     * 対象プランの全メタを取得する
     */
    $all_meta = get_post_meta($post_id);
    $ids = array();

    /**
     * メタキーを見ながら、画像ID候補を集める
     */
    foreach ($all_meta as $key => $values) {
        $key_l = strtolower((string) $key);

        /**
         * メタキー名から「間取り系」か判断
         */
        $is_plan_key = preg_match('/floor|plan|heimen|madori|site|haichi|配置|平面|間取り/', $key_l);

        /**
         * メタキー名から「施工写真系」か判断
         */
        $is_work_key = preg_match('/interior|room|works|gallery|photo|reference|内装|施工|写真/', $key_l);

        /**
         * plan モード:
         * 間取り系のメタキーだけ見る
         */
        if ($mode === 'plan' && !$is_plan_key) {
            continue;
        }

        /**
         * work モード:
         * 施工写真系だけ見る
         * ただし、間取り系や外観は除外する
         */
        if ($mode === 'work' && (!$is_work_key || $is_plan_key || preg_match('/exterior|外観/', $key_l))) {
            continue;
        }

        /**
         * メタ値から添付画像IDを集める
         */
        foreach ($values as $v) {
            $ids = array_merge($ids, naigai_iez_fragment_collect_ids($v));
        }
    }

    /**
     * 重複・空値を整理する
     */
    $ids = array_values(array_unique(array_filter($ids)));

    $items = array();

    /**
     * 添付画像IDを画像表示用データに変換する
     */
    foreach ($ids as $id) {
        /**
         * attachment 以外は除外する
         */
        if (get_post_type($id) !== 'attachment') {
            continue;
        }

        /**
         * 表示用画像URLとリンク先URLを取得する
         */
        $src = wp_get_attachment_image_url($id, $mode === 'plan' ? 'large' : 'medium_large');
        $href = wp_get_attachment_url($id);

        if (!$src || !$href) {
            continue;
        }

        /**
         * alt / title を取得する
         */
        $alt = get_post_meta($id, '_wp_attachment_image_alt', true);
        $title = get_the_title($id);
        $haystack = strtolower($title . ' ' . $alt . ' ' . $href);

        /**
         * work モードでは外観画像を除外する
         *
         * 理由:
         * - ここでは「施工事例＝内装写真」中心で見せたい想定
         */
        if ($mode === 'work' && (strpos($haystack, '外観') !== false || strpos($haystack, 'exterior') !== false)) {
            continue;
        }

        /**
         * plan モードでは、間取り・配置図らしい画像だけに絞る
         */
        if ($mode === 'plan' && !preg_match('/floor|plan|site|平面|配置|間取り|madori|haichi/', $haystack)) {
            continue;
        }

        $items[] = array(
            'src'   => $src,
            'href'  => $href,
            'alt'   => $alt ? $alt : $title,
            'title' => $title,
        );
    }

    /**
     * 表示件数:
     * - plan : 最大2件
     * - work : 最大4件
     */
    return array_slice($items, 0, $mode === 'plan' ? 2 : 4);
}

/**
 * =========================================================
 * 06. 住宅の特徴ラベルを作るヘルパー
 * =========================================================
 *
 * 関数:
 * - naigai_iez_fragment_specs()
 *
 * 役割:
 * - iez_plan に付いているタクソノミーから特徴ラベルを作る
 * - 足りない分は住宅タイプごとの固定ラベルを足す
 *
 * 参照するタクソノミー:
 * - iez_plan_feature
 * - iez_plan_size
 * - iez_plan_layout
 *
 * ターム別の追加ラベル:
 * - two-family:
 *   生活音配慮 / 共有動線 / 収納計画
 *
 * - dual-life:
 *   管理しやすい / 水回り集中 / 滞在向け
 *
 * - その他:
 *   平屋 / 収納 / 回遊動線
 *
 * 表示件数:
 * - 最大6件
 */
function naigai_iez_fragment_specs($post_id, $term_slug)
{
    $labels = array();

    /**
     * 住宅特徴タームを追加する
     */
    $raw_terms = get_the_terms($post_id, 'iez_plan_feature');
    if ($raw_terms && !is_wp_error($raw_terms)) {
        foreach ($raw_terms as $t) {
            $labels[] = $t->name;
        }
    }

    /**
     * サイズ・間取りタームを追加する
     */
    foreach (array('iez_plan_size', 'iez_plan_layout') as $tax) {
        $terms = get_the_terms($post_id, $tax);
        if ($terms && !is_wp_error($terms)) {
            foreach ($terms as $t) {
                $labels[] = $t->name;
            }
        }
    }

    /**
     * 住宅タイプごとの固定ラベルを追加する
     */
    if ($term_slug === 'two-family') {
        $labels = array_merge($labels, array('生活音配慮', '共有動線', '収納計画'));
    } elseif ($term_slug === 'dual-life') {
        $labels = array_merge($labels, array('管理しやすい', '水回り集中', '滞在向け'));
    } else {
        $labels = array_merge($labels, array('平屋', '収納', '回遊動線'));
    }

    /**
     * 重複・空値を整理する
     */
    $labels = array_values(array_unique(array_filter($labels)));

    /**
     * 最大6件に絞る
     */
    return array_slice($labels, 0, 6);
}

/**
 * =========================================================
 * 07. 表示用データを作成
 * =========================================================
 *
 * $work_items:
 * - 施工事例画像。最大4件
 *
 * $plan_items:
 * - 平面図・配置図など。最大2件
 *
 * $features:
 * - 住宅特徴ラベル。最大6件
 */
$work_items = naigai_iez_fragment_media_items($plan_id, 'work');
$plan_items = naigai_iez_fragment_media_items($plan_id, 'plan');
$features   = naigai_iez_fragment_specs($plan_id, $term_slug);

/**
 * WP_Query のグローバル投稿状態を戻す
 *
 * 注意:
 * - ここから下は $plan_id / $plan_url / $term などの変数を使って出力する
 */
wp_reset_postdata();
?>

<?php
/**
 * =========================================================
 * 08. 差し替え用HTML出力
 * =========================================================
 *
 * このHTMLが JS によって /iezukuri/ トップの
 * [data-iez-plan-fragment] の中へ入る。
 *
 * 紐づくCSS:
 * - .ch-plan-fragment
 * - .ch-plan-fragment__head
 * - .ch-plan-fragment__table
 * - .ch-plan-fragment__row
 * - .ch-plan-fragment__label
 * - .ch-plan-fragment__body
 * - .ch-plan-fragment__media-grid
 * - .ch-plan-fragment__thumb
 * - .ch-plan-fragment__features
 * - .ch-plan-fragment__actions
 * - .ch-plan-fragment__btn
 *
 * 出力内容:
 * 1. 見出し
 * 2. 施工事例
 * 3. 間取りとプラン
 * 4. 住宅の特徴
 * 5. CTA
 */
?>
<div class="ch-plan-fragment" data-plan-id="<?php echo esc_attr($plan_id); ?>">
    <div class="ch-plan-fragment__head">
        <p class="ch-eyebrow">Plans</p>
        <h3><?php echo esc_html($term->name); ?>の内容</h3>
    </div>

    <div class="ch-plan-fragment__table">

        <?php
        /**
         * =====================================================
         * 08-01. 施工事例
         * =====================================================
         *
         * 役割:
         * - 対象 iez_plan に紐づく内装・施工写真を表示する
         *
         * 表示件数:
         * - 最大4件
         *
         * 画像が無い場合:
         * - 「内装写真を準備中です。」を表示
         */
        ?>
        <div class="ch-plan-fragment__row">
            <div class="ch-plan-fragment__label">
                <strong>施工事例</strong>
                <small>Works</small>
            </div>
            <div class="ch-plan-fragment__body">
                <?php if (!empty($work_items)) : ?>
                    <div class="ch-plan-fragment__media-grid is-works">
                        <?php foreach ($work_items as $item) : ?>
                            <a class="ch-plan-fragment__thumb is-work" href="<?php echo esc_url($item['href']); ?>">
                                <span><img src="<?php echo esc_url($item['src']); ?>" alt="<?php echo esc_attr($item['alt']); ?>"></span>
                                <b><?php echo esc_html($item['title']); ?></b>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php else : ?>
                    <p class="ch-plan-fragment__empty">内装写真を準備中です。</p>
                <?php endif; ?>
            </div>
        </div>

        <?php
        /**
         * =====================================================
         * 08-02. 間取りとプラン
         * =====================================================
         *
         * 役割:
         * - 対象 iez_plan に紐づく平面図・配置図などを表示する
         *
         * 表示件数:
         * - 最大2件
         *
         * 画像が無い場合:
         * - 「間取り画像を準備中です。」を表示
         */
        ?>
        <div class="ch-plan-fragment__row">
            <div class="ch-plan-fragment__label">
                <strong>間取りとプラン</strong>
                <small>Plans</small>
            </div>
            <div class="ch-plan-fragment__body">
                <?php if (!empty($plan_items)) : ?>
                    <div class="ch-plan-fragment__media-grid is-plans">
                        <?php foreach ($plan_items as $item) : ?>
                            <a class="ch-plan-fragment__thumb is-plan" href="<?php echo esc_url($item['href']); ?>">
                                <span><img src="<?php echo esc_url($item['src']); ?>" alt="<?php echo esc_attr($item['alt']); ?>"></span>
                                <b><?php echo esc_html($item['title']); ?></b>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php else : ?>
                    <p class="ch-plan-fragment__empty">間取り画像を準備中です。</p>
                <?php endif; ?>
            </div>
        </div>

        <?php
        /**
         * =====================================================
         * 08-03. 住宅の特徴
         * =====================================================
         *
         * 役割:
         * - 対象 iez_plan の特徴ラベルを表示する
         * - タクソノミー + 固定ラベルから作成
         *
         * 表示件数:
         * - 最大6件
         */
        ?>
        <div class="ch-plan-fragment__row">
            <div class="ch-plan-fragment__label">
                <strong>住宅の特徴</strong>
                <small>Features</small>
            </div>
            <div class="ch-plan-fragment__body">
                <ul class="ch-plan-fragment__features">
                    <?php foreach ($features as $feature) : ?>
                        <li><?php echo esc_html($feature); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <?php
        /**
         * =====================================================
         * 08-04. CTA
         * =====================================================
         *
         * 役割:
         * - 問い合わせページへの誘導
         * - 対象の間取り詳細ページへの誘導
         *
         * リンク:
         * - /iezukuri/contact/
         * - 対象 iez_plan の詳細ページ
         */
        ?>
        <div class="ch-plan-fragment__row">
            <div class="ch-plan-fragment__label">
                <strong>CTA</strong>
                <small>相談・資料請求</small>
            </div>
            <div class="ch-plan-fragment__body">
                <div class="ch-plan-fragment__actions">
                    <a class="ch-plan-fragment__btn is-primary" href="<?php echo esc_url(home_url('/iezukuri/contact/')); ?>">
                        無料相談・資料請求
                    </a>
                    <a class="ch-plan-fragment__btn is-secondary" href="<?php echo esc_url($plan_url); ?>">
                        詳細ページを見る
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>