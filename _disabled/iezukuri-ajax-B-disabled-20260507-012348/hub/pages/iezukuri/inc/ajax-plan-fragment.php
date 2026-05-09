<?php

/**
 * =========================================================
 * /iezukuri トップ：3カード切替 AJAX ハンドラー
 * =========================================================
 *
 * このファイルの役割:
 * - /iezukuri/ トップの「3つの住まい」カードをクリックした時に、
 *   JSから送られてきた term を受け取る
 * - term に対応する iez_plan_type の間取り情報を探す
 * - section-plan-fragment.php を読み込んでHTML断片を作る
 * - 作ったHTMLを JSON としてJSへ返す
 *
 * 流れ:
 * 1. JSがカードクリックを検知
 * 2. JSが data-iez-plan-term から term を取得
 * 3. JSが admin-ajax.php に POST する
 * 4. このPHPが action=naigai_iez_plan_fragment を受ける
 * 5. nonce を検証する
 * 6. $_POST['term'] を受け取る
 * 7. section-plan-fragment.php を include する
 * 8. 出力されたHTMLを json.data.html として返す
 * 9. JSが /iezukuri/ トップの [data-iez-plan-fragment] に差し込む
 *
 * 紐づくJS:
 * - action: naigai_iez_plan_fragment
 * - nonce: NaigaiIezukuriPlanAjax.nonce
 * - ajaxUrl: NaigaiIezukuriPlanAjax.ajaxUrl
 *
 * 紐づくHTML:
 * - クリック側:
 *   [data-iez-plan-card]
 *   [data-iez-plan-term]
 *
 * - 差し込み先:
 *   [data-iez-plan-fragment]
 *
 * 紐づくテンプレート:
 * - hub/pages/iezukuri/templates/top/section-plan-fragment.php
 *
 * 紐づくCPT / taxonomy:
 * - post_type: iez_plan
 * - taxonomy: iez_plan_type
 *
 * 注意:
 * - このファイルはHTMLを直接表示するページテンプレートではない
 * - JSから呼ばれた時だけ admin-ajax.php 経由で動く
 * - このファイルが loader.php などで require されていないと、AJAX action は登録されない
 * - JS側の nonce 名と check_ajax_referer() の action 名が一致していないと失敗する
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * =========================================================
 * 01. WordPress AJAX action 登録
 * =========================================================
 *
 * wp_ajax_...
 * - ログイン中ユーザー向けのAJAX
 *
 * wp_ajax_nopriv_...
 * - 未ログインユーザー向けのAJAX
 *
 * 今回は一般閲覧者も /iezukuri/ トップで3カードを押すため、
 * nopriv 側も登録している。
 *
 * JS側で送る必要がある値:
 * - action=naigai_iez_plan_fragment
 */
add_action('wp_ajax_naigai_iez_plan_fragment', 'naigai_iezukuri_ajax_plan_fragment');
add_action('wp_ajax_nopriv_naigai_iez_plan_fragment', 'naigai_iezukuri_ajax_plan_fragment');


/**
 * =========================================================
 * 02. AJAX本体
 * =========================================================
 *
 * 役割:
 * - nonce を確認する
 * - POSTされた term を取得する
 * - section-plan-fragment.php を読み込む
 * - そのHTMLをJSONで返す
 */
function naigai_iezukuri_ajax_plan_fragment()
{
    /**
     * -----------------------------------------------------
     * nonce 検証
     * -----------------------------------------------------
     *
     * 役割:
     * - 不正なAJAXリクエストを防ぐ
     *
     * 第1引数:
     * - nonce作成時の action 名
     *
     * 第2引数:
     * - POSTで送られてくる nonce のキー名
     *
     * JS側:
     * body.set('nonce', config.nonce);
     *
     * PHP側で nonce を作る時は:
     * wp_create_nonce('naigai_iez_plan_fragment')
     *
     * で作る必要がある。
     */
    check_ajax_referer('naigai_iez_plan_fragment', 'nonce');

    /**
     * -----------------------------------------------------
     * term slug 取得
     * -----------------------------------------------------
     *
     * JSから送られる値:
     * $_POST['term']
     *
     * 想定値:
     * - one-family
     * - two-family
     * - dual-life
     *
     * この値は section-plan-fragment.php 側で使う。
     */
    $iez_plan_term_slug = isset($_POST['term'])
        ? sanitize_title(wp_unslash($_POST['term']))
        : '';

    /**
     * -----------------------------------------------------
     * 差し替えHTMLテンプレートの場所
     * -----------------------------------------------------
     *
     * このテンプレート内で、
     * $iez_plan_term_slug を使って iez_plan_type を検索する。
     */
    $template = get_template_directory() . '/hub/pages/iezukuri/templates/top/section-plan-fragment.php';

    /**
     * テンプレートが存在しない場合はエラーJSONを返す。
     *
     * JS側では json.success が false になる。
     */
    if (!file_exists($template)) {
        wp_send_json_error(array(
            'message' => 'section-plan-fragment.php が見つかりません。',
        ), 404);
    }

    /**
     * -----------------------------------------------------
     * HTML断片を生成
     * -----------------------------------------------------
     *
     * ob_start():
     * - include したPHPが echo するHTMLを一旦バッファに貯める
     *
     * include $template:
     * - section-plan-fragment.php を実行する
     *
     * ob_get_clean():
     * - バッファに貯めたHTMLを文字列として取得する
     *
     * ここで作られるHTML:
     * - 施工事例
     * - 間取りとプラン
     * - 住宅の特徴
     * - CTA
     */
    ob_start();
    include $template;
    $html = ob_get_clean();

    /**
     * -----------------------------------------------------
     * JSへ成功レスポンスを返す
     * -----------------------------------------------------
     *
     * 返るJSON:
     * {
     *   "success": true,
     *   "data": {
     *     "html": "<div class=\"...\">...</div>"
     *   }
     * }
     *
     * JS側:
     * target.innerHTML = json.data.html;
     */
    wp_send_json_success(array(
        'html' => $html,
    ));
}
