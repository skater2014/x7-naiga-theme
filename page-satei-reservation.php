<?php

/**
 * Template Name: 売却査定フォームページ
 *
 * ------------------------------------------------------------
 * このテンプレートの役割
 * ------------------------------------------------------------
 * 1. 売却査定フォームを表示する
 * 2. 入力画面 → 確認画面 → 送信完了画面 の3段階を持つ
 * 3. 売却査定用の入力項目を表示する
 * 4. お客様情報と査定物件情報を分けて見せる
 *
 * 注意
 * ------------------------------------------------------------
 * このテンプレートは「画面表示用」です。
 * 実際の送信処理・メール処理・DB保存は functions.php 側の
 * AJAX ハンドラで行います。
 */
get_header('77');
?>

<div class="satei-page-hero">
  <!-- =========================================================
    ページ上部の見出し
    ========================================================= -->
  <h1>お客様の不動産売却査定フォーム</h1>

  <div class="satei-page-intro">
    <?php the_content(); ?>
  </div>
</div>

<!-- =========================================================
  売却査定フォーム全体
  ========================================================= -->
<div id="store-reservation-form-container" class="store-reservation-form">
  <div class="store-reservation-page-content">

    <!-- =======================================================
      Step 1: 入力画面
      -------------------------------------------------------
      お客様のご希望の売却査定の情報
      ＋ お客様情報
      をここで入力する
    ======================================================== -->
    <div class="step active" id="step-input">
      <form
        id="store-reservation-form"
        action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>"
        method="POST"
        enctype="multipart/form-data">
        <?php wp_nonce_field('store_reservation_action', 'store_reservation_nonce'); ?>

        <!-- ===================================================
          お客様のご希望の売却査定の情報
          ---------------------------------------------------
          売却査定に必要な情報を先に入力する
        ==================================================== -->
        <h2>お客様のご希望の売却査定の情報</h2>

        <div class="form-row-group">
          <!-- 物件の現況 -->
          <div class="form-row">
            <label for="property_status">物件の現況: <span class="required">＊必須</span></label>
            <select id="property_status" name="property_status" class="input-field" required>
              <option value="">選択してください</option>
              <option value="land_with_building">土地付き建物</option>
              <option value="vacant_land">更地</option>
              <option value="detached_house">戸建</option>
              <option value="condominium">マンション区分</option>
              <option value="rented">商業施設</option>
            </select>
          </div>

          <!-- 動的フィールドの挿入先
               例: 面積・築年数・間取りなどをJSで追加する場合に使用 -->
          <div id="dynamic-fields-container" class="form-row"></div>

          <!-- 査定を希望する理由 -->
          <div class="form-row">
            <label for="sale_reason">査定を希望する理由: <span class="required">＊必須</span></label>
            <select id="sale_reason" name="sale_reason" class="input-field" required>
              <option value="">選択してください</option>
              <option value="inheritance">相続</option>
              <option value="relocation">住み替え</option>
              <option value="financial">金銭的理由</option>
              <option value="investment">投資物件の売却</option>
            </select>
          </div>
        </div>

        <div class="form-row-group">
          <!-- 売却希望価格 -->
          <div class="form-row">
            <label for="sale_price">売却希望価格（任意）:</label>
            <input
              type="text"
              id="sale_price"
              name="sale_price"
              class="input-field"
              placeholder="例: 700（数値のみ入力してください）">
          </div>

          <!-- 売却希望時期 -->
          <div class="form-row">
            <label for="sale_period">売却希望時期: <span class="required">＊必須</span></label>
            <select id="sale_period" name="sale_period" class="input-field" required>
              <option value="">選択してください</option>
              <option value="1_month">1ヶ月以内</option>
              <option value="3_months">3ヶ月以内</option>
              <option value="6_months">6ヶ月以内</option>
              <option value="1_year">1年以内</option>
              <option value="undecided">未定</option>
            </select>
          </div>
        </div>

        <div class="form-row-group">
          <!-- 査定方法 -->
          <div class="form-row">
            <label for="valuation_method">査定方法: <span class="required">＊必須</span></label>
            <select id="valuation_method" name="valuation_method" class="input-field" required>
              <option value="">選択してください</option>
              <option value="desk">机上査定（簡易）</option>
              <option value="visit">訪問査定（詳細）</option>
            </select>
          </div>

          <!-- 査定物件の郵便番号 -->
          <div class="form-row">
            <label for="postcode_2">査定物件の郵便番号: <span class="required">＊必須</span></label>
            <input
              type="text"
              id="postcode_2"
              name="property_postcode"
              class="input-field postcode"
              data-index="2"
              placeholder="例: 325-0021（自動でハイフンが入ります）"
              required>
          </div>

          <!-- 査定物件の住所 -->
          <div class="form-row">
            <label for="address_2">査定物件の住所: <span class="required">＊必須</span></label>
            <input
              type="text"
              id="address_2"
              name="property_address"
              class="input-field"
              data-index="2"
              placeholder="例: 栃木県那須塩原市○○"
              required>
          </div>

          <!-- 売却理由の自由記述 -->
          <div class="form-row">
            <label for="sale_reason_text">査定物件の売却理由の詳細（任意）:</label>
            <textarea
              id="sale_reason_text"
              name="sale_reason_text"
              class="input-field"
              placeholder="例: 相続した不動産のため売却を検討しています"></textarea>
          </div>
        </div>

        <!-- ===================================================
          画像アップロード
          ---------------------------------------------------
          査定参考用の画像
        ==================================================== -->
        <div class="image-upload-info">
          <h3>添付写真・画像</h3>
          <p>物件の間取り図や、外観・内装などがわかる写真を添付いただくと、より正確に査定しやすくなります。</p>
          <p>添付できる画像は、5MB以内の JPG / GIF / PNG / BMP / WEBP を想定しています。</p>
        </div>

        <input type="file" id="property_image" name="property_image[]" accept="image/*" multiple>

        <!-- 画像プレビュー表示領域 -->
        <div class="image-preview-container" style="display: flex;"></div>

        <!-- ===================================================
          お客様情報
          ---------------------------------------------------
          実際にご連絡を差し上げるための情報
        ==================================================== -->
        <div class="property-info">
          <h2>お客様情報</h2>
        </div>

        <div class="form-row-group name-kana-row-group">
          <div class="form-row">
            <label for="last_name">姓: <span class="required">＊必須</span></label>
            <input type="text" id="last_name" name="last_name" class="input-field" placeholder="例: 佐野" required>
          </div>

          <div class="form-row">
            <label for="first_name">名: <span class="required">＊必須</span></label>
            <input type="text" id="first_name" name="first_name" class="input-field" placeholder="例: 隼一" required>
          </div>

          <div class="form-row">
            <label for="last_name_kana">セイ: <span class="required">＊必須</span></label>
            <input type="text" id="last_name_kana" name="last_name_kana" class="input-field" placeholder="例: サノ" required>
          </div>

          <div class="form-row">
            <label for="first_name_kana">メイ: <span class="required">＊必須</span></label>
            <input type="text" id="first_name_kana" name="first_name_kana" class="input-field" placeholder="例: ハヤカズ" required>
          </div>
        </div>

        <!-- ローマ字名などを別処理で使う場合の hidden -->
        <input type="hidden" id="name_slug" name="name_slug" value="">

        <div class="form-row-group">
          <div class="form-row">
            <label for="email">メールアドレス: <span class="required">＊必須</span></label>
            <input type="email" id="email" name="email" class="input-field" placeholder="例: email@example.com" required>
          </div>

          <div class="form-row">
            <label for="postcode">郵便番号: <span class="required">＊必須</span></label>
            <input
              type="text"
              id="postcode"
              name="postcode"
              class="input-field postcode"
              data-index="4"
              placeholder="例: 325-0021（自動でハイフンが入ります）"
              required>
          </div>
        </div>

        <div class="form-row-group">
          <div class="form-row">
            <label for="address">住所: <span class="required">＊必須</span></label>
            <input
              type="text"
              id="address"
              name="address"
              class="input-field"
              data-index="4"
              placeholder="例: 栃木県那須塩原市○○"
              required>
          </div>

          <div class="form-row">
            <label for="phone">電話番号: <span class="required">＊必須</span></label>
            <input
              type="text"
              id="phone"
              name="phone"
              class="input-field phone"
              placeholder="例: 080-1234-5678（自動でハイフンが入ります）"
              required>
          </div>
        </div>

        <!-- AJAX処理名 -->
        <input type="hidden" name="action" value="store_reservation">

        <!-- 入力内容確認へ進む -->
        <button type="button" id="to-confirm-page" class="form-button">入力内容を確認</button>

        <!-- 規約案内 -->
        <p class="attention">
          <span>
            ※<a href="<?php echo esc_url(home_url('/rule/')); ?>" target="_blank">利用規約</a>
            及び
            <a href="<?php echo esc_url(home_url('/privacypolicy/')); ?>" target="_blank">プライバシーポリシー</a>
            を必ずお読みください。<br>
            上記内容に同意いただいた場合は、確認画面へお進みください。
          </span>
        </p>
      </form>
    </div>

    <!-- =======================================================
      Step 2: 確認画面
      -------------------------------------------------------
      売却査定では ID / タイトル は表示しない
    ======================================================== -->
    <div class="step" id="step-confirm" style="display: none;">
      <h3>確認画面</h3>

      <div class="confirm-message">
        <p>以下の内容で送信してもよろしいですか？</p>

        <!-- お客様情報 -->
        <h3>お客様情報</h3>
        <p>お名前: <span id="confirm-名前"></span></p>
        <p>カタカナ: <span id="confirm-カタカナ"></span></p>
        <p>メールアドレス: <span id="confirm-メールアドレス"></span></p>
        <p>電話番号: <span id="confirm-電話番号"></span></p>
        <p>郵便番号: <span id="confirm-郵便番号"></span></p>
        <p>住所: <span id="confirm-住所"></span></p>

        <!-- 売却査定のご希望情報 -->
        <h3>お客様のご希望の売却査定の情報</h3>
        <p>査定物件の郵便番号: <span id="confirm-査定物件郵便番号"></span></p>
        <p>査定物件の住所: <span id="confirm-査定物件住所"></span></p>
        <p>物件の現況: <span id="confirm-物件状況"></span></p>
        <p>査定を希望する理由: <span id="confirm-査定理由"></span></p>
        <p>売却希望価格: <span id="confirm-売却価格"></span></p>
        <p>売却希望時期: <span id="confirm-売却時期"></span></p>
        <p>査定方法: <span id="confirm-査定方法"></span></p>

        <!-- 動的項目表示領域 -->
        <div id="confirm-dynamic-fields-container" class="form-row"></div>

        <!-- 画像 -->
        <h3>アップロード画像</h3>
        <div id="confirm-image-container" style="display: none;"></div>

        <!-- ボタン -->
        <button type="button" id="submit-reservation">送信する</button>
        <button type="button" id="back-to-input">戻る</button>
      </div>
    </div>

    <!-- =======================================================
      Step 3: 完了画面
    ======================================================== -->
    <div class="step" id="step-complete" style="display: none;">
      <h3>送信完了</h3>
      <p class="complete-message">ありがとうございます。担当者からご連絡いたします。</p>
    </div>
  </div>
</div>

<style>
  /* =========================================================
    見出しの見た目
  ========================================================= */
  #store-reservation-form-container h2,
  #store-reservation-form-container h3 {
    font-family: "Arial", sans-serif;
    font-size: 24px;
    padding: 3px;
  }

  /* =========================================================
    確認画面の画像領域
  ========================================================= */
  #confirm-image-container {
    display: flex;
  }

  /* =========================================================
    補助テキスト
  ========================================================= */
  .image-upload-info p {
    line-height: 1.8;
  }

  /* =========================================================
    プレビュー画像コンテナ
  ========================================================= */
  .image-preview-container {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    justify-content: flex-start;
    align-items: center;
    margin-top: 10px;
    max-width: 100%;
  }

  /* 画像ラッパー */
  .image-wrapper {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-right: 10px;
  }

  /* プレビュー画像 */
  .image-preview {
    width: 100px;
    height: 100px;
    border-radius: 5px;
    object-fit: cover;
  }

  /* 削除ボタン */
  .image-wrapper .delete-btn {
    background-color: #888;
    color: #fff;
    padding: 5px 10px;
    border: none;
    border-radius: 3px;
    font-size: 12px;
    cursor: pointer;
    width: 80px;
    display: block;
    margin-top: 5px;
  }

  /* タブレット */
  @media (max-width: 768px) {
    .image-preview {
      width: 90px;
      height: 90px;
    }

    .image-preview-container {
      gap: 8px;
    }
  }

  /* スマホ */
  @media (max-width: 480px) {
    .image-preview {
      width: 80px;
      height: 80px;
    }

    .image-preview-container {
      gap: 5px;
      justify-content: center;
    }

    .image-wrapper .delete-btn {
      font-size: 10px;
      width: 70px;
      padding: 4px 8px;
    }
  }
</style>

<?php get_footer(); ?>