<?php
/**
 * ============================================================
 * Footer直前 サブCTA
 * ============================================================
 *
 * このテンプレートは
 *
 *     <section class="iez-sub-cta">
 *
 * を表示するための専用テンプレート。
 *
 *
 * ============================================================
 * このセクションの位置
 * ============================================================
 *
 * Hero
 *     ↓
 * ページ本文 / フォーム等
 *     ↓
 * Footer直前 サブCTA ← このファイル
 *     ↓
 * 共通Footer
 *
 *
 * ============================================================
 * Heroとは別
 * ============================================================
 *
 * Heroは
 *
 *     _ch_hero_*
 *
 * を使用する。
 *
 * このサブCTAからHeroメタは読まない。
 *
 *
 * ============================================================
 * ページ本文とも別
 * ============================================================
 *
 * ページ本文は
 *
 *     _ch_intro_*
 *     _ch_body_*
 *
 * を使用する。
 *
 * このサブCTAから本文メタは読まない。
 *
 *
 * ============================================================
 * 正式な保存キー
 * ============================================================
 *
 * _ch_sub_cta_kicker
 * _ch_sub_cta_title
 * _ch_sub_cta_text
 *
 * _ch_sub_cta_primary_text
 * _ch_sub_cta_primary_url
 *
 * _ch_sub_cta_secondary_text
 * _ch_sub_cta_secondary_url
 *
 *
 * ============================================================
 * 重要
 * ============================================================
 *
 * 表示文言をPHPへ直書きしない。
 *
 * 管理画面
 *     ↓
 * _ch_sub_cta_*
 *     ↓
 * このテンプレート
 *     ↓
 * <section class="iez-sub-cta">
 *
 * の1本だけにする。
 *
 * 旧 _iezukuri_sub_cta_* は使用しない。
 * 旧 _ch_cta_* も使用しない。
 *
 *
 * ============================================================
 * ボタン表示ルール
 * ============================================================
 *
 * ボタンは
 *
 *     文言
 *       ＋
 *     URL
 *
 * の両方がある場合だけ表示する。
 *
 * 文言のみ → 表示しない
 * URLのみ  → 表示しない
 *
 * ============================================================
 */

if (!defined('ABSPATH')) {
    exit;
}

$page_id = get_queried_object_id();

if (!$page_id) {
    return;
}


/*
 * ============================================================
 * 管理画面で保存した値だけを読む
 * ============================================================
 */

$kicker = trim(
    (string) get_post_meta(
        $page_id,
        '_ch_sub_cta_kicker',
        true
    )
);

$title = trim(
    (string) get_post_meta(
        $page_id,
        '_ch_sub_cta_title',
        true
    )
);

$text = trim(
    (string) get_post_meta(
        $page_id,
        '_ch_sub_cta_text',
        true
    )
);

$primary_text = trim(
    (string) get_post_meta(
        $page_id,
        '_ch_sub_cta_primary_text',
        true
    )
);

$primary_url = trim(
    (string) get_post_meta(
        $page_id,
        '_ch_sub_cta_primary_url',
        true
    )
);

$secondary_text = trim(
    (string) get_post_meta(
        $page_id,
        '_ch_sub_cta_secondary_text',
        true
    )
);

$secondary_url = trim(
    (string) get_post_meta(
        $page_id,
        '_ch_sub_cta_secondary_url',
        true
    )
);


/*
 * ============================================================
 * ボタン有効判定
 * ============================================================
 */

$show_primary = (
    $primary_text !== ''
    && $primary_url !== ''
);

$show_secondary = (
    $secondary_text !== ''
    && $secondary_url !== ''
);


/*
 * ============================================================
 * サブCTA全体の表示判定
 * ============================================================
 *
 * 何も入力されていなければ
 * section.iez-sub-cta 自体を出力しない。
 */

$has_content = (
    $kicker !== ''
    || $title !== ''
    || $text !== ''
    || $show_primary
    || $show_secondary
);

if (!$has_content) {
    return;
}
?>

<section
    class="iez-sub-cta"
    id="iez-cta"
    data-iez-sub-cta
>
    <div class="iez-sub-cta__inner">

        <?php if ($kicker !== '') : ?>

            <p class="iez-sub-cta__kicker">
                <?php echo esc_html($kicker); ?>
            </p>

        <?php endif; ?>


        <?php if ($title !== '') : ?>

            <h2 class="iez-sub-cta__title">
                <?php echo esc_html($title); ?>
            </h2>

        <?php endif; ?>


        <?php if ($text !== '') : ?>

            <p class="iez-sub-cta__text">
                <?php echo nl2br(
                    esc_html($text)
                ); ?>
            </p>

        <?php endif; ?>


        <?php if (
            $show_primary
            || $show_secondary
        ) : ?>

            <div class="iez-sub-cta__actions">

                <?php if ($show_primary) : ?>

                    <a
                        class="iez-sub-btn iez-sub-btn--primary"
                        href="<?php echo esc_url($primary_url); ?>"
                    >
                        <?php echo esc_html($primary_text); ?>
                    </a>

                <?php endif; ?>


                <?php if ($show_secondary) : ?>

                    <a
                        class="iez-sub-btn iez-sub-btn--ghost"
                        href="<?php echo esc_url($secondary_url); ?>"
                    >
                        <?php echo esc_html($secondary_text); ?>
                    </a>

                <?php endif; ?>

            </div>

        <?php endif; ?>

    </div>
</section>