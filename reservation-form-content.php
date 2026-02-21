<!-- reservation-form-content.php コンテンツ表示のみ-->
<div id="store-reservation-form-container" class="store-reservation-form">
  <div class="store-reservation-page-content">

    <!-- Step 1: Input Screen -->
    <div class="step active" id="step-input">
      <?php
      // ここでカスタムフィールドから「売却済み」と「価格」を取得
      $sold_out = get_post_meta($post->ID, 'sold-out', true); // 売却済みの状態を取得
      $price = get_post_meta($post->ID, 'Price', true); // 価格を取得

      // 売却済みかつ価格が空または0の場合、または sold-out が true の場合
      $sold_out_condition = ($sold_out || empty($price) || $price == '0');

      // スタイルのクラスを設定
      $style_class = $sold_out_condition ? 'sold-out' : 'available';
      ?>

      <h3 class="store-title <?php echo $style_class; ?>">
        <?php
        // 投稿タイプが 'recruitment' の場合、特定のメッセージを表示
        if (is_singular('recruitment')) {
          echo '下記フォームよりお問い合わせよろしくお願いします。';
        } elseif ($sold_out_condition && get_post_type($post->ID) !== 'blog') {
          // 売却済みの場合、そして投稿タイプが 'blog' でない場合
          echo '現在売却済みですが、下記フォームよりお問い合わせよろしくお願いします。';
        } else {
          echo '来店予約';
        }
        ?>
      </h3>







      <form id="store-reservation-form" action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" method="POST">
        <?php wp_nonce_field('store_reservation_action', 'store_reservation_nonce'); ?>

        <div class="property-info">
          <?php
          // 親ファイルから渡された引数（post_id）を受け取る　スクリプトで制御していないPHP側で情報を採取している。
          $post_id = isset($args['post_id']) ? $args['post_id'] : null;
          // post_id が取得できた場合の処理
          if ($post_id) {
            // 投稿情報を取得
            $post_title = get_the_title($post_id); // 投稿タイトル
            $video_id = get_post_meta($post_id, 'page_video_id', true); // 動画ID
            $type = get_post_meta($post_id, 'page_featured_type', true); // 動画タイプ
            $thumbnail_url = get_the_post_thumbnail_url($post_id, 'full');
            $thumbnail_url = $thumbnail_url ?: get_template_directory_uri() . '/images/noimage.gif'; // サムネイル

            // display_custom_fields_table_1 が存在する場合に実行　このページでは、価格表示を取得するために行う。
            if (function_exists('display_custom_fields_table_1')) {
              // display_custom_fields_table_1 関数を実行して内容を取得
              ob_start(); // バッファリング開始
              display_custom_fields_table_1($post);
              $custom_fields_content = ob_get_clean(); // バッファ内容を取得

              // <div class="cta-container"> 内の価格情報を正規表現で抽出
              preg_match('/<div class="cta-container">([^<]+)<\/div>/', $custom_fields_content, $matches);
              $price = isset($matches[1]) ? trim($matches[1]) : '価格情報なし'; // 価格が見つからなければ '価格情報なし'
            }

            // 価格情報を取得
            $price = get_post_meta($post_id, 'Price', true); // 'Price' カスタムフィールドから価格を取得

            // 動画サムネイルの設定
            if ($type == 'youtube' && !empty($video_id)) {
              // maxresdefault.jpg が取得できない場合は hqdefault.jpg を使用
              $background_image = 'https://img.youtube.com/vi/' . esc_attr($video_id) . '/maxresdefault.jpg';

              // maxresdefault.jpg が存在しない場合に hqdefault.jpg を代替
              if (!@getimagesize($background_image)) {
                $background_image = 'https://i.ytimg.com/vi/' . esc_attr($video_id) . '/hqdefault.jpg';
              }
            } elseif ($type == 'vimeo' && !empty($video_id)) {
              // Vimeoの場合のサムネイル画像
              $background_image = 'https://vumbnail.com/' . esc_attr($video_id) . '.jpg';
            } else {
              // アイキャッチ画像などの他の画像を使用
              $background_image = $thumbnail_url;
            }


            // NEWマーク
            $days = 14; // NEWマークを表示する日数
            $now = time(); // 現在時間
            $entry = get_the_time('U', $post_id); // 投稿日時
            $term = ($now - $entry) / 86400; // 経過日数

            if ($days > $term) {
              $new_mark = '<div class="box new-post"><div class="ribbon ribbon-top-left"><span>New</span></div></div>';
            } else {
              $new_mark = '';
            }
          } else {
            // post_id が無い場合
            $background_image = get_template_directory_uri() . '/images/noimage.gif';
            $post_title = 'タイトルなし';
            $price = '価格情報なし'; // 価格がない場合のデフォルト値
            $new_mark = '';
          }
          ?>

          <div id="reservation-property-thumbnail" class="reservation-property-thumbnail" style="background-image: url('<?php echo esc_url($background_image); ?>');">
            <!-- NEWマークの表示 -->
            <?php echo $new_mark; ?>
          </div>

          <!-- 物件詳細 -->
          <div class="property-details">
            <div class="property-info-left">
              <h3>物件タイトル: <span id="reservation-property-title"><?php echo esc_html($post_title); ?></span></h3>
              <div class="property-info-row">
                <h3>物件ID: <span id="reservation-property-id"><?php echo esc_html($post_id); ?></span></h3>

                <?php if (get_post_type($post_id) !== 'blog') : ?>
                  <!-- blog投稿タイプ以外でのみ価格情報を表示 -->
                  <h3>
                    <?php
                    // リクルートページの場合、価格の代わりに「給与：応相談」を表示
                    if (is_singular('recruitment')) {
                      echo '給与：応相談';
                    }
                    // 価格が未設定または0なら売却済、それ以外なら「○○万円」と表示
                    elseif (empty($price) || $price == '0') {
                      echo '<span style="font-weight: bold; color: red;">売却済</span>';
                    } else {
                      echo '価格: ' . esc_html($price) . ' 万円';
                    }
                    ?>
                  </h3>



                <?php endif; ?>
              </div>
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
            <input type="text" id="postcode" name="postcode" class="input-field postcode" data-index="3" placeholder="例: 100-0001（自動でハイフンが入ります）" required>
          </div>
        </div>

        <div class="form-row-group">
          <div class="form-row">
            <label for="address">住所: <span class="required">＊必須</span></label>
            <input type="text" id="address" name="address" class="input-field" data-index="3" placeholder="例: 東京都千代田区1-1-1" required>
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

        <!--<input type="hidden" name="action" value="store_reservation">-->
        <input type="hidden" name="action" value="store_reservation_submit">

        <button type="button" id="to-confirm-page" class="form-button">確認する</button>

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