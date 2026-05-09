<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="mnpk-modal" id="mnpk-payment-modal" aria-hidden="true">
    <div class="mnpk-modal__backdrop" data-close-modal></div>

    <div
        class="mnpk-modal__dialog"
        role="dialog"
        aria-modal="true"
        aria-labelledby="mnpk-payment-modal-title"
    >
        <button
            type="button"
            class="mnpk-modal__close"
            data-close-modal
            aria-label="閉じる"
        >×</button>

        <div class="mnpk-modal__header">
            <h2 id="mnpk-payment-modal-title">お支払い</h2>
            <span>Payment Element</span>
        </div>

        <form id="mnpk-payment-form" class="mnpk-payment-form" novalidate>
            <div class="mnpk-form-grid" style="margin-bottom:16px;">
                <label class="mnpk-form-field">
                    <span>お名前</span>
                    <input
                        type="text"
                        id="mnpk-payment-name"
                        data-payment-name
                        name="payment_name"
                        placeholder="例: 山田 太郎"
                        autocomplete="name"
                        required
                    >
                </label>

                <label class="mnpk-form-field">
                    <span>メールアドレス</span>
                    <input
                        type="email"
                        id="mnpk-payment-email"
                        data-payment-email
                        name="payment_email"
                        placeholder="例: user@example.com"
                        autocomplete="email"
                        required
                    >
                </label>
            </div>

            <div class="mnpk-summary-box" style="margin-bottom:16px;">
                <h3>今回のお支払い</h3>
                <p><strong data-payment-total>—</strong></p>
            </div>

            <div id="mnpk-payment-element" style="margin-bottom:16px;"></div>

            <div
                class="mnpk-booking-error"
                data-payment-error
                hidden
            ></div>

            <div class="mnpk-modal__actions">
                <button
                    type="button"
                    class="mnpk-button mnpk-button--ghost"
                    data-close-modal
                >閉じる</button>

                <button
                    type="submit"
                    class="mnpk-button"
                    data-confirm-payment
                >支払いを確定する</button>
            </div>
        </form>
    </div>
</div>
