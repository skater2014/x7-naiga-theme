<?php

/**
 * Template Name: 予約フォームページ
 * 
 * data-index="3" post code address
 */
get_header('77'); ?>




<?php
// functrions.php 側で　page-store-reservation フッターリンクからの遷移URLを生成している。　
// 戻るページの処理　現在のURLから post_id または new_post_id を取得 
$post_id = isset($_GET['post_id']) ? intval($_GET['post_id']) : null;
$new_post_id = isset($_GET['new_post_id']) ? intval($_GET['new_post_id']) : null;

// 戻るリンクの決定
$return_link = $new_post_id ? get_permalink($new_post_id) : ($post_id ? get_permalink($post_id) : home_url('/'));

// 戻るリンクを表示
if ($post_id || $new_post_id) :
?>
  <div class="back-link">
    <a href="<?php echo esc_url($return_link); ?>" class="form-back-button">← 物件ページに戻る</a>
  </div>
<?php endif; ?>




<div id="store-reservation-form-container" class="store-reservation-form">
  <div class="store-reservation-page-content">

    <!-- Step 1: Input Screen -->
    <div class="step active" id="step-input">
      <?php
      // URLからpost_idを取得
      $post_id = isset($_GET['post_id']) ? $_GET['post_id'] : $post->ID;

      // ここでカスタムフィールドから「売却済み」と「価格」を取得
      $sold_out = get_post_meta($post_id, 'sold-out', true); // 売却済みの状態を取得
      $price = get_post_meta($post_id, 'Price', true); // 価格を取得

      // 売却済みかつ価格が空または0の場合、または sold-out が true の場合
      $sold_out_condition = ($sold_out || empty($price) || $price == '0');

      // スタイルのクラスを設定
      $style_class = $sold_out_condition ? 'sold-out' : 'available';
      ?>

      <h3 class="store-title <?php echo $style_class; ?>">
        <?php
        // 売却済みの物件かどうかを確認し、タイトルを変更
        if ($sold_out_condition) {
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
          // URLからパラメータを取得
          //$post_title = isset($_GET['title']) ? urldecode($_GET['title']) : 'タイトルなし'; // "物件タイトル"
          $post_id = isset($_GET['post_id']) ? intval($_GET['post_id']) : ''; // 559
          //$new_post_id = isset($_GET['new_post_id']) ? intval($_GET['new_post_id']) : ''; // 123
          $price = isset($_GET['price']) ? urldecode($_GET['price']) : '価格情報なし'; // 1200

          // 初期化
          $post_title = 'タイトルなし';
          $new_mark = ''; // NEWマークの初期値

          // new_post_id が優先される
          if ($new_post_id) {
            $post_id = $new_post_id;
          }

          // 投稿IDが存在する場合
          if ($post_id) {
            $post_title = get_the_title($post_id); // 投稿タイトルを取得
            $video_id = get_post_meta($post_id, 'page_video_id', true); // 動画ID
            $type = get_post_meta($post_id, 'page_featured_type', true); // 動画タイプ

            // サムネイルURLを取得
            $thumbnail_url = get_the_post_thumbnail_url($post_id, 'full');
            $thumbnail_url = $thumbnail_url ?: get_template_directory_uri() . '/images/noimage.gif'; // サムネイルが無ければデフォルト画像

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


            // NEWマークの判定
            $days = 14; // NEWマークを表示する日数
            $now = time(); // 現在時間
            $entry = get_the_time('U', $post_id); // 投稿日時
            $term = ($now - $entry) / 86400; // 経過日数

            // 経過日数が指定日数以内ならNEWマークを表示
            if ($days > $term) {
              $new_mark = '<div class="box new-post"><div class="ribbon ribbon-top-left"><span>New</span></div></div>';
            }
          } else {
            // 投稿IDが無い場合のデフォルト設定
            $background_image = get_template_directory_uri() . '/images/noimage.gif';
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
                <h3>価格:
                  <span id="reservation-property-price">
                    <?php
                    $price = isset($_GET['price']) && is_numeric($_GET['price']) ? (int) $_GET['price'] : 0;
                    $sold_out = $sold_out ?? false; // 売却済みの変数が未定義なら初期化

                    if ($sold_out || $price == 0) {
                      echo '<span class="sold-out">売却済</span>';
                    } else {
                      echo esc_html(number_format($price)) . '万円';
                    }
                    ?>
                  </span>
                </h3>
              </div>
            </div>
          </div>
        </div>


        <!-- 入力フィールド -->
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

        <div class="form-row-group">
          <div class="form-row">
            <label for="email">メールアドレス: <span class="required">＊必須</span></label>
            <input type="email" id="email" name="email" class="input-field" placeholder="例: email@example.com" required>
          </div>
          <div class="form-row">
            <label for="postcode3">郵便番号: <span class="required">＊必須</span></label>
            <input type="text" id="postcode" name="postcode" class="input-field postcode" data-index="2" placeholder="例: 100-0001（自動でハイフンが入ります）" required>
          </div>
        </div>

        <div class="form-row-group">
          <div class="form-row">
            <label for="address">住所: <span class="required">＊必須</span></label>
            <input type="text" id="address" name="address" class="input-field" data-index="2" placeholder="例: 東京都千代田区1-1-1" required>
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






<?php get_footer(); ?>