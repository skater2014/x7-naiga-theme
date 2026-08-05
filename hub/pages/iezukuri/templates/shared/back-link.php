<?php
/**
 * ============================================================
 * 家づくり共通「前のページへ戻る」
 * ============================================================
 *
 * この部品は /iezukuri/ と、その配下ページで共通使用する。
 *
 * 【表示する場合】
 * このサイト内の別ページから、現在の家づくりページへ移動してきた場合。
 *
 * 例:
 *   フロントページ
 *      ↓
 *   /iezukuri/
 *
 * この場合は「前のページへ戻る」を表示し、
 * クリックするとブラウザ履歴の1つ前へ戻る。
 *
 * 【表示しない場合】
 * ・URLをアドレスバーへ直接入力した
 * ・ブックマークから直接開いた
 * ・外部サイトから来た
 * ・Referer が取得できない
 *
 * 直接アクセスなのに戻るボタンだけ表示される状態を防止する。
 *
 * CSSはここでは変更しない。
 * 見た目は既存の家づくり専用CSSをそのまま使用する。
 * ============================================================
 */

if (!defined('ABSPATH')) {
    exit;
}

/*
 * 同じリクエスト内でこの部品が複数回読み込まれても、
 * 戻るリンクを二重表示しない。
 */
if (!empty($GLOBALS['naigai_iezukuri_back_link_output_done'])) {
    return;
}

/*
 * HTTP_REFERER = ブラウザが送る「直前に見ていたページ」のURL。
 *
 * 直接アクセスでは通常この値が空になるため、
 * その場合は戻るリンクを表示しない。
 */
/*
 * ============================================================
 * IEZUKURI_PLAN_TAXONOMY_BACK_TO_ARCHIVE
 * taxonomyページ専用「参考プラン一覧へ戻る」
 * ============================================================
 *
 * taxonomyページは、
 * ブラウザ履歴ではなく参考プラン一覧を明確な戻り先にする。
 *
 * 直接URLからtaxonomyへ入った場合でも表示する。
 *
 * 見た目は既存の家づくりBack Linkをそのまま使用し、
 * 新しいCSSは追加しない。
 */
$naigai_plan_taxonomies = array(
    'iez_plan_type',
    'iez_plan_size',
    'iez_plan_feature',
    'iez_plan_structure',
    'iez_plan_scope',
    'iez_plan_building_form',
    'iez_plan_layout',
    'iez_plan_area',
);

if (is_tax($naigai_plan_taxonomies)) {

    $plan_archive_url =
        get_post_type_archive_link('iez_plan');

    /*
     * 万一投稿タイプ側のarchive URLが取得できない場合だけ、
     * 現在使用している参考プラン一覧URLへ戻す。
     */
    if (!$plan_archive_url) {
        $plan_archive_url =
            home_url('/iezukuri/plans/');
    }

    /*
     * このリクエストでは通常の履歴Back Linkを
     * 後から二重表示させない。
     */
    $GLOBALS[
        'naigai_iezukuri_back_link_output_done'
    ] = true;
    ?>

    <nav
        class="iez-plan-single-back-nav iezukuri-common-back-nav"
        data-iezukuri-common-back-link
        aria-label="参考プラン一覧へ戻る"
    >
        <a
            class="iez-plan-single-back-nav__link"
            href="<?php echo esc_url($plan_archive_url); ?>"
        >
            ← 参考プラン一覧へ戻る
        </a>
    </nav>

    <?php
    return;
}


/*
 * taxonomy以外では従来どおり、
 * Refererを使った「前のページへ戻る」を使用する。
 */
$referer = wp_get_referer();

if (!$referer) {
    return;
}

/*
 * 自サイト内からの遷移だけ許可する。
 * 外部サイトから来た場合には表示しない。
 */
$home_host    = wp_parse_url(home_url('/'), PHP_URL_HOST);
$referer_host = wp_parse_url($referer, PHP_URL_HOST);

if (
    !$home_host ||
    !$referer_host ||
    strtolower($home_host) !== strtolower($referer_host)
) {
    return;
}

/*
 * 現在のページ自身からのRefererだった場合も表示しない。
 * リロード等で「戻る」が自分自身を指すのを防ぐ。
 */
$current_url = home_url(
    isset($_SERVER['REQUEST_URI'])
        ? wp_unslash($_SERVER['REQUEST_URI'])
        : '/'
);

$current_url = untrailingslashit($current_url);
$referer     = untrailingslashit($referer);

if ($current_url === $referer) {
    return;
}

/*
 * ここまで条件を通過した時だけ表示する。
 */
$GLOBALS['naigai_iezukuri_back_link_output_done'] = true;
?>

<nav
    class="iez-plan-single-back-nav iezukuri-common-back-nav"
    data-iezukuri-common-back-link
    aria-label="前のページへ戻る"
>
    <a
        class="iez-plan-single-back-nav__link"
        href="<?php echo esc_url($referer); ?>"
        onclick="window.history.back(); return false;"
    >
        ← 前のページへ戻る
    </a>
</nav>