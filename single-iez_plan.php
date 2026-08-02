<?php
/**
 * single-iez_plan.php
 *
 * 家づくりプラン詳細ページ
 */

if (!defined('ABSPATH')) {
    exit;
}

if (isset($_GET['plan_pdf']) && $_GET['plan_pdf'] === '1') {
    $pdf_template = get_template_directory() . '/hub/pages/iezukuri/templates/pdf-plan.php';

    if (file_exists($pdf_template)) {
        include $pdf_template;
        exit;
    }

    wp_die('PDF専用テンプレートが見つかりません。');
}

get_header('customhome');

$page_id = get_queried_object_id();
$slug    = $page_id ? get_post_field('post_name', $page_id) : '';

$upload_dir = wp_upload_dir();

$generated_pdf_path = '';
$generated_pdf_url  = '';

if ($slug) {
    $upload_dir = wp_upload_dir();

    if (empty($upload_dir['error'])) {
        $generated_pdf_path = trailingslashit($upload_dir['basedir']) . 'iezukuri-pdf/' . $slug . '.pdf';
    }

    $generated_pdf_url = home_url('/wp-content/uploads/iezukuri-pdf/' . rawurlencode($slug) . '.pdf');
}

$has_generated_pdf = (
    $generated_pdf_path &&
    file_exists($generated_pdf_path) &&
    filesize($generated_pdf_path) > 0
);

/*
 * PDFダウンロードURLの優先順位:
 * 1. 編集画面でアップロード/選択したPDF (_ch_plan_pdf_id)
 * 2. 自動生成PDF uploads/iezukuri-pdf/{slug}.pdf
 * 3. 保存済みPDF URL (_ch_plan_pdf_url)
 */
$selected_pdf_id  = (int) get_post_meta(get_the_ID(), '_ch_plan_pdf_id', true);
$selected_pdf_url = '';

if ($selected_pdf_id && get_post_mime_type($selected_pdf_id) === 'application/pdf') {
    $selected_pdf_url = wp_get_attachment_url($selected_pdf_id);
}

$saved_pdf_url = trim((string) get_post_meta(get_the_ID(), '_ch_plan_pdf_url', true));

$pdf_download_url = '';

if ($selected_pdf_url) {
    $pdf_download_url = $selected_pdf_url;
} elseif ($has_generated_pdf) {
    $pdf_download_url = $generated_pdf_url;
} elseif ($saved_pdf_url) {
    $pdf_download_url = $saved_pdf_url;
}

$has_pdf_download = ($pdf_download_url !== '');
?>

<main
    id="primary"
    class="hub-customhome-subpage iezukuri-subpage iezukuri-plan-single iezukuri-plan-single--<?php echo esc_attr($slug); ?>"
    data-iezukuri-page="plan-single"
    data-iezukuri-slug="<?php echo esc_attr($slug); ?>"
>
<?php
    /**
     * 間取り詳細の本体表示。
     *
     * 読み込み経路:
     * 1. hub/pages/iezukuri/inc/block-renderer.php
     *    - naigai_iezukuri_render_block('plan-viewer') を定義
     *
     * 2. hub/pages/iezukuri/templates/components/block-plan-viewer.php
     *    - 実際のHTML本体
     *    - 投稿タイトル
     *    - 編集画面の説明文 _ch_plan_description
     *    - 外観写真 / 平面図 / 配置図 / 仕様情報
     *    を表示する。
     *
     * 注意:
     * - 旧パス hub/pages/iezukuri/template-parts/subpage/blocks/ は現在使わない。
     * - single-iez_plan.php は読み込み係。
     * - タイトルや説明文のHTMLは block-plan-viewer.php 側で管理する。
     */
    if (function_exists('naigai_iezukuri_render_block')) {
        naigai_iezukuri_render_block('plan-viewer');
    } else {
        /**
         * block-renderer.php が読まれていない場合の保険。
         * 現在実在するコンポーネントを直接読む。
         */
        $block = get_template_directory() . '/hub/pages/iezukuri/templates/components/block-plan-viewer.php';

        if (file_exists($block)) {
            include $block;
        }
    }
    ?>
</main>

<?php get_footer('iezukuri'); ?>
