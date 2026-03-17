<?php

/**
 * Template Name: 売却査定フォームページ
 * 
 *  * data-index="2" post code address

属性  主な用途
id  JavaScriptやCSSで 対象を指定するため
name    HTMLフォームで サーバーに送るため（POSTのキー名）
 */
get_header('77'); ?>

<div class="satei-page-hero">
  <h1>お客様の不動産売却査定フォーム</h1>
  <div class="satei-page-intro">
    <?php the_content(); ?>
  </div>
</div>
<!-- 物件情報（売却査定用） -->
<div id="store-reservation-form-container" class="store-reservation-form">
  <div class="store-reservation-page-content">
    <h2>お客様情報</h2>
    <div class="step active" id="step-input">
      <form id="store-reservation-form" action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" method="POST" enctype="multipart/form-data">
        <?php wp_nonce_field('store_reservation_action', 'store_reservation_nonce'); ?>

        <!-- 入力フィールド -->
        <!-- 物件の現況 選択欄 -->
        <h3>お客様 売却査定の情報</h3>
        <div class="form-row-group">
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

          <!-- ✅ ここに動的フィールドを挿入（既存のデザインに影響を与えない位置） -->
          <div id="dynamic-fields-container" class="form-row"></div>

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
          <div class="form-row">
            <label for="sale_price">売却希望価格（任意）:</label>
            <input type="text" id="sale_price" name="sale_price" class="input-field" placeholder="例: 3000万円 数値のみ入力してください">
          </div>
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
            <input type="text" id="postcode_2" name="postcode" class="input-field postcode" data-index="2" placeholder="例: 1000001（自動でハイフンが入ります）" required>
          </div>

          <!-- 売却査定の住所 -->
          <div class="form-row">
            <label for="address_2">査定物件の住所: <span class="required">＊必須</span></label>
            <input type="text" id="address_2" name="property_address" class="input-field" data-index="2" placeholder="例: 東京都千代田区1-1-1" required>
          </div>

          <!-- 売却理由 -->
          <div class="form-row">
            <label for="sale_reason_text">査定物件の売却理由（任意）:</label>
            <textarea id="sale_reason_text" name="sale_reason_text" class="input-field" placeholder="新しい定住先をなど"></textarea>
          </div>
        </div>

        <!-- 画像アップロードの説明 -->
        <div class="image-upload-info">
          <h3>添付写真・画像</h3>
          <p>物件の間取り図や、外観・内装などがわかる写真を添付いただくと、より正確に査定をすることが可能になります。</p>
          <p>添付できる画像は、5MB以内のJPG、GIF、PNG、BMPとなります。6つまで添付可能です。</p>
        </div>

        <!-- 画像アップロードフォーム -->
        <input type="file" id="property_image" name="property_image[]" accept="image/*" multiple>

        <!-- 画像プレビューコンテナ（初期は非表示） -->
        <div class="image-preview-container" style="display: flex;"></div>

        <div class="property-info">
          <h3>お客様 お住まいの情報</h3>
        </div>
        <!-- 入力フィールド（姓・名の分割）-->
        <!-- 姓・名・セイ・メイ を横並び -->
        <div class="form-row-group name-kana-row-group">
          <div class="form-row">
            <label for="last_name">姓: <span class="required">＊必須</span></label>
            <input type="text" id="last_name" name="last_name" class="input-field" placeholder="例: 山田" required>
          </div>
          <div class="form-row">
            <label for="first_name">名: <span class="required">＊必須</span></label>
            <input type="text" id="first_name" name="first_name" class="input-field" placeholder="例: 太郎" required>
          </div>
          <div class="form-row">
            <label for="last_name_kana">セイ: <span class="required">＊必須</span></label>
            <input type="text" id="last_name_kana" name="last_name_kana" class="input-field" placeholder="例: ヤマダ" required>
          </div>
          <div class="form-row">
            <label for="first_name_kana">メイ: <span class="required">＊必須</span></label>
            <input type="text" id="first_name_kana" name="first_name_kana" class="input-field" placeholder="例: タロウ" required>
          </div>
        </div>


        <!-- 🔽 ローマ字に変換された名前（非表示） -->
        <input type="hidden" id="name_slug" name="name_slug" value="">

        <div class="form-row-group">
          <div class="form-row">
            <label for="email">メールアドレス: <span class="required">＊必須</span></label>
            <input type="email" id="email" name="email" class="input-field" placeholder="例: email@example.com" required>
          </div>
          <div class="form-row">
            <label for="postcode">郵便番号: <span class="required">＊必須</span></label>
            <input type="text" id="postcode" name="postcode" class="input-field postcode" data-index="4" placeholder="例: 100-0001（自動でハイフンが入ります）" required>
            　
          </div>
        </div>

        <div class="form-row-group">
          <!-- お客様の住所 -->
          <div class="form-row">
            <label for="address_2">住所: <span class="required">＊必須</span></label>
            <input type="text" id="address" name="address" class="input-field" data-index="4" placeholder="例: 東京都千代田区1-1-1" required>
          </div>
          <div class="form-row">
            <label for="phone">電話番号: <span class="required">＊必須</span></label>
            <input type="text" id="phone" name="phone" class="input-field phone" placeholder="例: 090-1234-5678（自動でハイフンが入ります）" required>
          </div>
        </div>

        <input type="hidden" name="action" value="store_reservation">
        <button type="button" id="to-confirm-page" class="form-button">入力内容を確認</button>

        <!-- 利用規約とプライバシーポリシー -->
        <p class="attention">
          <span>※<a href="<?php echo esc_url(home_url('/rule/')); ?>" target="_blank">利用規約</a>及び<a href="<?php echo esc_url(home_url('/privacypolicy/')); ?>" target="_blank">プライバシーポリシー</a>を必ずお読みください。<br>
            上記内容に同意いただいた場合は、確認画面へお進みください。</span>
        </p>
      </form>
    </div>

    <!-- Step 2: Confirmation Screen 売却査定用ページ-->
    <div class="step" id="step-confirm" style="display: none;">
      <h3>確認画面</h3>
      <div class="confirm-message">
        <p>以下の内容で送信してもよろしいですか？</p>
        <h3>お客様 お住まいの情報</h3>
        <p>お名前: <span id="confirm-名前"></span></p>
        <p>カタカナ: <span id="confirm-カタカナ"></span></p>
        <p>メールアドレス: <span id="confirm-メールアドレス"></span></p>
        <p>電話番号: <span id="confirm-電話番号"></span></p>
        <p>郵便番号: <span id="confirm-郵便番号"></span></p> <!-- 修正されたID -->
        <p>住所: <span id="confirm-住所"></span></p> <!-- 修正されたID -->

        <!-- 新しく追加された査定物件の情報 -->
        <h3>お客様 査定物件の情報</h3>
        <p>査定物件の郵便番号: <span id="confirm-査定物件郵便番号"></span></p>
        <p>査定物件の住所: <span id="confirm-査定物件住所"></span></p>
        <p>物件の現況: <span id="confirm-物件状況"></span></p>
        <p>査定を希望する理由: <span id="confirm-査定理由"></span></p>
        <p>売却希望価格: <span id="confirm-売却価格"></span></p>
        <p>売却希望時期: <span id="confirm-売却時期"></span></p>
        <p>査定方法: <span id="confirm-査定方法"></span></p>

        <!-- 動的に追加されたフィールドの確認表示 -->
        <div id="confirm-dynamic-fields-container" class="form-row"></div>


        <div class="property-info">
          <h3>アップロード画像</h3>
          <div id="confirm-image-container" style="display: none;"></div>
        </div>
      </div>
      <button type="button" id="submit-reservation">送信する</button>
      <button type="button" id="back-to-input">戻る</button>
    </div>


    <!-- Step 3: Completion Screen -->
    <div class="step" id="step-complete" style="display: none;">
      <h3>送信完了</h3>
      <p class="complete-message">ありがとうございます。担当者から返事を致します。</p>
    </div>
  </div>
</div>

<style>
  #store-reservation-form-container h3 {
    color: #555555;
    font-family: "Arial", sans-serif;
    /* お好みのフォントに変更可能 */
    font-size: 24px;
    /* 必要に応じてサイズを調整 */
    padding: 3px;
  }


  #confirm-image-container {
    display: flex;
  }

  .image-upload-info p {
    color: #555555;
  }


  /* 🔹 プレビューコンテナ（画像を横並びにする） */
  .image-preview-container {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    justify-content: flex-start;
    align-items: center;
    margin-top: 10px;
    max-width: 100%;
  }

  /* 🔹 画像と削除ボタンのラップ */
  .image-wrapper {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-right: 10px;
    /* 🔹 画像間の余白 */
  }

  /* 🔹 画像のスタイル（デフォルト: PC向け） */
  .image-preview {
    width: 100px;
    height: 100px;
    border-radius: 5px;
    object-fit: cover;
  }

  /* 🔹 削除ボタン */
  .image-wrapper .delete-btn {
    background-color: #888;
    color: white;
    padding: 5px 10px;
    border: none;
    border-radius: 3px;
    font-size: 12px;
    cursor: pointer;
    width: 80px;
    display: block;
    margin-top: 5px;
  }

  /* 🔹 タブレット（横幅 768px 以下） */
  @media (max-width: 768px) {
    .image-preview {
      width: 90px;
      height: 90px;
    }

    .image-preview-container {
      gap: 8px;
    }
  }

  /* 🔹 スマホ（横幅 480px 以下） */
  @media (max-width: 480px) {
    .image-preview {
      width: 80px;
      height: 80px;
    }

    .image-preview-container {
      gap: 5px;
      justify-content: center;
      /* 中央配置 */
    }

    .image-wrapper .delete-btn {
      font-size: 10px;
      width: 70px;
      padding: 4px 8px;
    }
  }
</style>


<?php get_footer(); ?>