<?php
/**
 * =========================================================
 * 共通サンクス表示
 * template-parts/common/thanks-state.php
 * =========================================================
 *
 * 【このファイルの役割】
 *
 * お問い合わせ・予約・民泊決済などで使う
 * 「処理完了後のサンクス表示」を1か所にまとめる。
 *
 * ---------------------------------------------------------
 * 共通化するもの
 * ---------------------------------------------------------
 *
 * ・チェックアイコン
 * ・完了タイトル
 * ・完了メッセージ
 * ・完了後のボタン
 *
 * ---------------------------------------------------------
 * 共通化しないもの
 * ---------------------------------------------------------
 *
 * CV計測は、このPHPでは行わない。
 *
 * 例:
 *
 * お問い合わせ成功
 *     → contact系CV
 *
 * 民泊Stripe決済成功
 *     → minpaku_purchase
 *
 * このように、
 * サンクス画面の見た目が同じでも
 * CVイベントは処理ごとに分離できる。
 * =========================================================
 */

if (!defined('ABSPATH')) {
    return;
}

$thanks = wp_parse_args(
    isset($args) && is_array($args) ? $args : array(),
    array(
        'variant'       => 'default',
        'heading_tag'   => 'h2',
        'title'         => 'ありがとうございます',
        'message'       => '',
        'button_label'  => '',
        'button_url'    => '',
        'button_type'   => 'link',
        'button_class'  => '',
    )
);

$allowed_heading_tags = array('h1', 'h2', 'h3');

$heading_tag = in_array(
    $thanks['heading_tag'],
    $allowed_heading_tags,
    true
)
    ? $thanks['heading_tag']
    : 'h2';

$variant = sanitize_html_class((string) $thanks['variant']);
?>

<div
    class="naigai-thanks-card customer-info-card customer-info-card--thanks"
    data-thanks-variant="<?php echo esc_attr($variant); ?>"
>
    <div
        class="naigai-thanks-card__icon customer-info-thanks__icon"
        aria-hidden="true"
    >
        ✓
    </div>

    <?php
    /**
     * heading_tag は上で h1 / h2 / h3 のみに制限している。
     *
     * お問い合わせでは h2、
     * checkout完了画面ではページ本体になるため h1
     * として利用できる。
     */
    echo '<' . $heading_tag . '>';
    echo esc_html((string) $thanks['title']);
    echo '</' . $heading_tag . '>';
    ?>

    <?php if ($thanks['message'] !== '') : ?>
        <p class="naigai-thanks-card__message">
            <?php echo wp_kses_post((string) $thanks['message']); ?>
        </p>
    <?php endif; ?>

    <?php if ($thanks['button_label'] !== '') : ?>

        <div
            class="naigai-thanks-card__actions customer-info-actions customer-info-actions--center"
        >

            <?php if ($thanks['button_type'] === 'button') : ?>

                <?php
                /**
                 * お問い合わせフォームは、
                 * JSで入力画面へ戻すため button を利用する。
                 */
                ?>
                <button
                    type="button"
                    class="<?php echo esc_attr((string) $thanks['button_class']); ?>"
                >
                    <?php echo esc_html((string) $thanks['button_label']); ?>
                </button>

            <?php elseif ($thanks['button_url'] !== '') : ?>

                <?php
                /**
                 * 民泊checkout完了後は、
                 * ブラウザ履歴ではなく宿泊詳細URLへ戻す。
                 */
                ?>
                <a
                    href="<?php echo esc_url((string) $thanks['button_url']); ?>"
                    class="<?php echo esc_attr((string) $thanks['button_class']); ?>"
                >
                    <?php echo esc_html((string) $thanks['button_label']); ?>
                </a>

            <?php endif; ?>

        </div>

    <?php endif; ?>
</div>
