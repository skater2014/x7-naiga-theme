<?php
if (!defined('ABSPATH')) {
  exit;
}

$favicon_url = get_site_icon_url(64);
?>

<button type="button" id="chatgpt-launcher" class="chatgpt-launcher" aria-label="チャット相談を開く">
  <svg class="chatgpt-launcher-icon" viewBox="0 0 36 32" aria-hidden="true" focusable="false">
    <use href="#icon-bubbles3"></use>
  </svg>
  <span class="chatgpt-launcher-label">チャット相談</span>
</button>

<div id="chatgpt-modal" class="chatgpt-modal" aria-hidden="true">
  <button type="button" class="chatgpt-close-btn" aria-label="閉じる">×</button>

  <div class="chatgpt-modal-content" role="dialog" aria-modal="false" aria-labelledby="chatgpt-modal-title">
    <div class="chatgpt-modal-header">
      <h2 id="chatgpt-modal-title">ご案内</h2>
    </div>

    <div class="chatgpt-modal-body">
      <div class="chatgpt-company-box">
        <div class="chatgpt-company-grid">
          <?php if (!empty($favicon_url)) : ?>
            <div class="chatgpt-company-logo-col">
              <img
                src="<?php echo esc_url($favicon_url); ?>"
                alt="内外土地開発株式会社・内外建設株式会社"
                class="chatgpt-company-favicon"
                width="28"
                height="28">
            </div>
          <?php endif; ?>

          <div class="chatgpt-company-info-col">
            <dl class="chatgpt-company-meta">
              <div class="chatgpt-company-row">
                <dt>会社名</dt>
                <dd class="chatgpt-company-names">
                  <span>内外土地開発株式会社</span>
                  <span>内外建設株式会社</span>
                </dd>
              </div>

              <div class="chatgpt-company-row">
                <dt>電話</dt>
                <dd><a href="tel:0364298700">03-6429-8700</a></dd>
              </div>

              <div class="chatgpt-company-row">
                <dt>営業時間</dt>
                <dd>9:00〜18:00</dd>
              </div>
            </dl>
          </div>
        </div>
      </div>

      <div id="chatgpt-quick-actions" class="chatgpt-quick-actions">
        <fieldset class="chatgpt-intent-group">
          <legend class="chatgpt-intent-legend">物件を探す</legend>
          <div class="chatgpt-option-grid">
            <label class="chatgpt-radio-card">
              <input type="radio" name="chatgpt_intent_choice" value="search_land">
              <span class="chatgpt-radio-card-text">土地</span>
            </label>

            <label class="chatgpt-radio-card">
              <input type="radio" name="chatgpt_intent_choice" value="search_building">
              <span class="chatgpt-radio-card-text">建物</span>
            </label>
          </div>
        </fieldset>

        <fieldset class="chatgpt-intent-group">
          <legend class="chatgpt-intent-legend">売却・査定を相談する</legend>
          <div class="chatgpt-option-grid">
            <label class="chatgpt-radio-card">
              <input type="radio" name="chatgpt_intent_choice" value="assessment_land">
              <span class="chatgpt-radio-card-text">土地</span>
            </label>

            <label class="chatgpt-radio-card">
              <input type="radio" name="chatgpt_intent_choice" value="assessment_building">
              <span class="chatgpt-radio-card-text">建物</span>
            </label>
          </div>
        </fieldset>
      </div>

      <div id="chatgpt-messages" class="chatgpt-messages" aria-live="polite"></div>
      <input type="hidden" id="chatgpt-intent" value="">
    </div>

    <div id="chatgpt-error-box" class="chatgpt-error-box" hidden></div>

    <div class="chatgpt-form">
      <textarea
        id="chatgpt-message"
        class="chatgpt-message"
        rows="2"
        placeholder="ご質問ください。"></textarea>

      <button type="button" id="send_message" class="chatgpt-send-btn">送信</button>
    </div>
  </div>
</div>