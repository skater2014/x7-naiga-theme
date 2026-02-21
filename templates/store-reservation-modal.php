<!-- 予約リンクボタン モーダル -->
<div id="store-reservation-modal" class="store-reservation-modal">
  <div class="store-reservation-modal-content">
    <span class="close-btn">×</span>

    <!-- Step 1: Input Screen -->
    <div class="step active" id="step-input">
      <h3 id="reservation-header">来店予約</h3>


      <form id="store-reservation-form" action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" method="POST">
        <?php wp_nonce_field('store_reservation_action', 'store_reservation_nonce'); ?>

        <div class="property-info">
          <?php
          // サムネイルURLを取得
          $thumbnail_url = get_the_post_thumbnail_url(get_the_ID(), 'full');
          // サムネイルURLがない場合にデフォルト画像を使用
          $thumbnail_url = !empty($thumbnail_url) ? $thumbnail_url : 'path_to_default_image.jpg';
          ?>

          <!-- 画像・動的に設定した背景画像を適用 -->
          <div id="reservation-property-thumbnail" class="reservation-property-thumbnail" style="background-image: url('<?php echo esc_url($thumbnail_url); ?>'); background-size: cover; background-position: center center;">
            <!-- 「New」タグはJavaScriptで動的に追加されます -->
            <!-- 「New」タグは初期状態では非表示 -->
            <div id="reservation-property-new"></div>
          </div>

          <!-- 物件・採用情報 -->
          <div class="property-details-modal">
            <h3><span id="reservation-property-title"></span></h3>
            <!-- 採用タイトルの部分はデフォルトで非表示にし、リクルートページで表示 -->
            <!-- 物件IDと価格を横並びにするためのdiv 物件価格と給与を判別表示 -->
            <div class="property-info-row">
              <h3>
                <span id="reservation-property-label"></span>
                <span id="reservation-property-id"><?php echo esc_html(get_the_ID()); ?></span>
              </h3>
              <!-- 価格:の表示部分を固定 -->

              <h3>価格:
                <span id="reservation-property-price">
                  <?php
                  // リクルートページの場合、価格ではなく「給与：応相談」を表示
                  if (is_singular('recruitment')) {
                    echo '<span id="reservation-recruit-price">給与：応相談</span>';
                  }
                  // それ以外の場合は価格を表示
                  elseif (empty($price) || $price == '0') {
                    // 売却済みの場合、赤色かつ太字で表示
                    echo '<span style="color: red; font-weight: bold;">売却済</span>';
                  } else {
                    // 価格が設定されている場合
                    echo esc_html($price) . ' 万円';
                  }
                  ?>
                </span>
              </h3>

            </div>
          </div>
        </div>

        <!-- 入力フィールド -->
        <!-- お名前（姓・名 分割） -->
        <div class="form-row-group">
          <div class="form-row">
            <label for="last_name">姓: <span class="required">＊必須</span></label>
            <input type="text" id="last_name" name="last_name" class="input-field" placeholder="例: 山田" required>
          </div>
          <div class="form-row">
            <label for="first_name">名: <span class="required">＊必須</span></label>
            <input type="text" id="first_name" name="first_name" class="input-field" placeholder="例: 太郎" required>
          </div>
        </div>

        <!-- カタカナ（セイ・メイ 分割） -->
        <div class="form-row-group">
          <div class="form-row">
            <label for="last_name_kana">セイ: <span class="required">＊必須</span></label>
            <input type="text" id="last_name_kana" name="last_name_kana" class="input-field" placeholder="例: ヤマダ" required>
          </div>
          <div class="form-row">
            <label for="first_name_kana">メイ: <span class="required">＊必須</span></label>
            <input type="text" id="first_name_kana" name="first_name_kana" class="input-field" placeholder="例: タロウ" required>
          </div>
        </div>


        <div class="form-row-group">
          <div class="form-row">
            <label for="email">メールアドレス: <span class="required">＊必須</span></label>
            <input type="email" id="email" name="email" class="input-field" placeholder="例: email@example.com" required>
          </div>
          <div class="form-row">
            <label for="postcode">郵便番号: <span class="required">＊必須</span></label>
            <input type="text" id="postcode" name="postcode" class="input-field postcode" data-index="1" placeholder="例: 100-0001（自動でハイフンが入ります）" required>
          </div>
        </div>

        <div class="form-row-group">
          <div class="form-row">
            <label for="address">住所: <span class="required">＊必須</span></label>
            <input type="text" id="address" name="address" class="input-field" data-index="1" placeholder="例: 東京都千代田区1-1-1" required>
          </div>

          <div class="form-row">
            <label for="phone">電話番号: <span class="required">＊必須</span></label>
            <input type="text" id="phone" name="phone" class="input-field phone" placeholder="例: 090-1234-5678（自動でハイフンが入ります）" required>
          </div>
        </div>

        <div class="form-row-group">
          <div class="form-row">
            <label for="visit-date">来店日時: <span class="required">＊必須</span></label>
            <input type="date" id="visit-date" name="visit-date" class="input-field" min="<?php echo date('Y-m-d'); ?>" required>
          </div>
          <div class="form-row">
            <label for="time-slot">時間帯: <span class="required">＊必須</span></label>
            <select id="time-slot" name="time-slot" class="input-field" required>
              <option value="" disabled selected>選択してください</option>
              <option value="10:00-11:00">10:00-11:00</option>
              <option value="11:00-12:00">11:00-12:00</option>
              <option value="12:00-13:00">12:00-13:00</option>
              <option value="13:00-14:00">13:00-14:00</option>
              <option value="14:00-15:00">14:00-15:00</option>
              <option value="15:00-16:00">15:00-16:00</option>
            </select>
          </div>
        </div>

        <input type="hidden" name="action" value="store_reservation">
        <button type="button" id="to-confirm-modal" class="form-button">確認する</button>

        <!-- 利用規約とプライバシーポリシー -->
        <p class="attention">
          <span>※<a href="<?php echo esc_url(home_url('/rule/')); ?>" target="_blank">利用規約</a>及び<a href="<?php echo esc_url(home_url('/privacypolicy/')); ?>" target="_blank">プライバシーポリシー</a>を必ずお読みください。<br>
            上記内容に同意いただいた場合は、確認画面へお進みください。</span>
        </p>
      </form>
    </div>

    <!-- Step 2: Confirmation Screen モーダルページ -->
    <div class="step" id="step-confirm" style="display: none;">
      <h3 id="confirm-header">確認画面</h3>
      <div class="confirm-message">
        <p>以下の内容で予約してもよろしいですか？</p>

        <p>お名前: <span id="confirm-名前"></span></p>
        <p>カタカナ: <span id="confirm-カタカナ"></span></p>
        <p>メールアドレス: <span id="confirm-メールアドレス"></span></p>
        <p>郵便番号: <span id="confirm-郵便番号"></span></p>
        <p>住所: <span id="confirm-住所"></span></p>
        <p>電話番号: <span id="confirm-電話番号"></span></p>
        <p>来店日時: <span id="confirm-訪問日"></span></p>
        <p>時間帯: <span id="confirm-時間帯"></span></p>

        <!-- 記事のタイトルを動的に表示 -->
        <h3 id="confirm-info-title"></h3>

        <!-- 物件IDまたは採用IDを表示 -->
        <!-- ラベル（採用ID: or 物件ID:）と ID本体を分離表示 -->
        <p>
          <span id="confirm-property-label"></span>
          <span id="confirm-物件ID"></span>
        </p>

        <!-- 採用情報 -->
        <p id="confirm-採用タイトル" style="display: none;">採用タイトル: <span id="confirm-採用タイトル-text"></span></p>
        <p id="confirm-採用給与" style="display: none;">給与: <span id="confirm-採用給与-text"></span></p>

        <!-- 物件情報 -->
        <p id="confirm-物件タイトル" style="display: none;">物件タイトル: <span id="confirm-物件タイトル-text"></span></p>
        <p id="confirm-物件価格" style="display: none;">価格: <span id="confirm-物件価格-text"></span></p>

        <button type="button" id="submit-reservation">送信する</button>
        <button type="button" id="back-to-input">戻る</button>
      </div>
    </div>


    <!-- Step 3: Completion Screen -->
    <div class="step" id="step-complete" style="display: none;">
      <h3>送信完了</h3>
      <p class="complete-message">ありがとうございます。担当者から返事を致します。</p>
    </div>

  </div>
</div>