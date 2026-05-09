<?php

/**
 * Template Name: 総合予約フォームページ
 * 
 * data-index="2" post code address
 */
get_header('77'); ?>

<style>
  /* ==========================
     共通レイアウト
  ========================== */
  .reservation-search-intro,
  .reservation-search-wrap,
  #store-reservation-form-container {
    max-width: 1200px;
    margin: 0 auto 24px;
    padding-left: 20px;
    padding-right: 20px;
    box-sizing: border-box;
  }

  /* ==========================
     STEP案内
  ========================== */
  .reservation-step-guide {
    max-width: 1200px;
    margin: 0 auto 16px;
    padding-left: 20px;
    padding-right: 20px;
    box-sizing: border-box;
  }

  .reservation-step-guide span {
    display: block;
    background: #eaf4fb;
    border-left: 4px solid #0073aa;
    color: #1f2937;
    padding: 12px 16px;
    border-radius: 6px;
    line-height: 1.7;
    font-size: 14px;
  }

  .reservation-step-guide strong {
    display: inline-block;
    margin-right: 8px;
    color: #0073aa;
    font-weight: 700;
  }

  .reservation-step-guide--next {
    margin-top: 8px;
    margin-bottom: 20px;
  }

  /* ダークモード */
  body.dark-theme .reservation-step-guide span,
  body.dark .reservation-step-guide span {
    background: rgba(0, 115, 170, 0.16);
    color: #f3f4f6;
    border-left-color: #39b7ff;
  }

  body.dark-theme .reservation-step-guide strong,
  body.dark .reservation-step-guide strong {
    color: #7fd3ff;
  }

  /* ==========================
     補助説明のpタグ
  ========================== */
  .reservation-search-note {
    max-width: 1200px;
    margin: 0 auto 14px;
    padding-left: 20px;
    padding-right: 20px;
    box-sizing: border-box;
    font-size: 14px;
    line-height: 1.8;
    color: #4b5563;
  }

  body.dark-theme .reservation-search-note,
  body.dark .reservation-search-note {
    color: #d1d5db;
  }

  /* ==========================
     検索フォームボタン
  ========================== */
  #reservation-search-form button {
    min-height: 46px;
  }

  #search-button {
    flex: 1 1 100%;
    background-color: #0073aa;
    color: #fff;
    border: none;
    cursor: pointer;
    transition: background-color 0.3s ease;
  }

  #search-button:disabled {
    background-color: #9ca3af;
    cursor: not-allowed;
  }

  #search-button:hover:not(:disabled) {
    background-color: #005f87;
  }

  /* リセットは副ボタン */
  #search-reset-button {
    flex: 0 0 auto;
    min-width: 180px;
    margin-left: auto;
    background: #f3f4f6;
    color: #374151;
    border: 1px solid #d1d5db;
    cursor: pointer;
    transition: background-color 0.3s ease, border-color 0.3s ease;
  }

  #search-reset-button:hover {
    background: #e5e7eb;
    border-color: #9ca3af;
  }

  /* ダークモード時のリセット */
  body.dark-theme #search-reset-button,
  body.dark #search-reset-button {
    background: #2b3138;
    color: #f3f4f6;
    border-color: #4b5563;
  }

  body.dark-theme #search-reset-button:hover,
  body.dark #search-reset-button:hover {
    background: #374151;
  }

  /* ==========================
     下フォーム初期表示の案内
  ========================== */


  /* 下フォームの見た目 */
  .store-reservation-page-content {
    border-radius: 8px;
    padding: 20px;
    box-sizing: border-box;
  }

  /* 念のためフォーム本体の余計な余白を消す */
  #store-reservation-form {
    margin: 0;
    padding: 0;
  }

  /* ==========================
     intro
  ========================== */
  .reservation-search-intro {
    padding-top: 20px;
  }

  .reservation-search-intro h1 {
    margin: 0 0 12px;
  }

  .reservation-search-intro__content p {
    margin: 0;
    line-height: 1.8;
  }

  /* ==========================
     検索フォーム
  ========================== */
  #reservation-search-form {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    align-items: center;
    justify-content: flex-start;
    background-color: #f8f8f8;
    border-radius: 8px;
    padding: 20px;
    box-sizing: border-box;
  }

  #reservation-search-form select {
    flex: 1 1 calc(25% - 10px);
    min-width: 140px;
    padding: 10px 12px;
    font-size: 14px;
    border-radius: 4px;
    border: 1px solid #ccc;
    background-color: #fff;
    box-sizing: border-box;
  }

  #reservation-search-form button {
    padding: 10px 12px;
    font-size: 14px;
    border-radius: 4px;
    box-sizing: border-box;
  }

  #reservation-search-form button {
    background-color: #0073aa;
    color: #fff;
    border: none;
    cursor: pointer;
    transition: background-color 0.3s ease;
  }

  #reservation-search-form button:disabled {
    background-color: #ccc;
    cursor: not-allowed;
  }

  #reservation-search-form button:hover:not(:disabled) {
    background-color: #005f87;
  }

  /* ==========================
     tablet
  ========================== */
  @media screen and (max-width: 768px) {

    .reservation-search-intro,
    .reservation-search-wrap,
    #store-reservation-form-container {
      padding-left: 16px;
      padding-right: 16px;
    }

    .store-reservation-page-content {
      padding: 16px;
    }

    #reservation-search-form select,
    #reservation-search-form button {
      flex: 1 1 calc(50% - 10px);
    }
  }

  /* ==========================
     small mobile
  ========================== */
  @media screen and (max-width: 480px) {

    .reservation-search-intro,
    .reservation-search-wrap,
    #store-reservation-form-container {
      padding-left: 12px;
      padding-right: 12px;
    }

    .store-reservation-page-content {
      padding: 12px;
    }

    #reservation-search-form select,
    #reservation-search-form button {
      flex: 1 1 100%;
    }
  }

  @media screen and (max-width: 768px) {

    .reservation-step-guide,
    .reservation-search-note {
      padding-left: 16px;
      padding-right: 16px;
    }

    #reservation-search-form select {
      flex: 1 1 calc(50% - 10px);
    }

    #search-reset-button {
      flex: 1 1 100%;
      margin-left: 0;
    }
  }

  @media screen and (max-width: 480px) {

    .reservation-step-guide,
    .reservation-search-note {
      padding-left: 12px;
      padding-right: 12px;
    }

    #reservation-search-form select {
      flex: 1 1 100%;
    }
  }

  @media screen and (max-width: 768px) {
    .reservation-step-guide {
      padding-left: 16px;
      padding-right: 16px;
    }
  }

  @media screen and (max-width: 480px) {
    .reservation-step-guide {
      padding-left: 12px;
      padding-right: 12px;
    }
  }
</style>

<section class="reservation-search-intro">
  <h1><?php the_title(); ?></h1>
  <div class="reservation-search-intro__content">
    <?php the_content(); ?>
  </div>
</section>

<div class="reservation-step-guide">
  <span><strong>STEP 1</strong> まずはこちらから、来店予約したい土地・住宅などの物件をお探しください。</span>
</div>

<p class="reservation-search-note">
  カテゴリを選ぶと、土地は地域、住宅は間取りなど、選択できる条件が切り替わります。ご希望の条件を選んで、物件を表示してください。
</p>

<div class="reservation-search-wrap">
  <form id="reservation-search-form">
    <select name="property_category" id="property_category">
      <option value="">カテゴリを選択</option>
      <option value="naigai-tochi">土地</option>
      <option value="house">住宅</option>
    </select>

    <select name="house-type" id="house-type" disabled>
      <option value="">間取り</option>
    </select>

    <select name="region" id="region">
      <option value="">地域</option>
    </select>

    <select name="selected_price" id="selected_price">
      <option value="">価格を選択</option>
    </select>

    <button type="button" id="search-button" disabled>この条件で物件を表示</button>
    <button type="button" id="search-reset-button" class="search-reset-button">条件をリセット</button>
  </form>
</div>

<div class="reservation-step-guide reservation-step-guide--next">
  <span><strong>STEP 2</strong> 表示された物件を確認して、下のフォームから来店予約情報をご入力ください。</span>
</div>

<div id="store-reservation-form-container" class="store-reservation-form">
  <div class="store-reservation-page-content">

    <!-- Step 1: Input Screen -->
    <div class="step active" id="step-input">

      <form id="store-reservation-form" action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" method="POST">
        <?php wp_nonce_field('store_reservation_action', 'store_reservation_nonce'); ?>

        <!--プレビューの表示させろ１件のみ-->
        <div class="property-info">
          <div id="reservation-property-thumbnail" class="reservation-property-thumbnail" style="background-image: url('<?php echo esc_url($background_image); ?>');">
            <!-- NEWマークの表示 -->
            <div id="reservation-new-mark"></div>
          </div>

          <!-- 物件詳細 -->
          <div class="property-details">
            <div class="property-info-left">
              <h3>物件タイトル: <span id="reservation-property-title">検索結果がここに表示されます</span></h3>
              <div class="property-info-row">
                <h3>物件ID: <span id="reservation-property-id">-</span></h3>
                <h3>価格: <span id="reservation-property-price">-</span></h3>
              </div>
            </div>
          </div>
        </div>

        <!-- 入力フィールド（姓・名の分割）-->
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
            <label for="postcode5">郵便番号: <span class="required">＊必須</span></label>
            <input type="text" id="postcode" name="postcode" class="input-field postcode" data-index="5" placeholder="例: 100-0001" required>
          </div>
        </div>

        <!--来店予約 住所-->
        <div class="form-row-group">
          <div class="form-row">
            <label for="address">住所: <span class="required">＊必須</span></label>
            <input type="text" id="address" name="address" class="input-field" data-index="5" placeholder="例: 東京都千代田区1-1-1" required>
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
      <!-- 確認画面の全体見出し -->
      <h3 id="confirm-header">確認画面</h3>

      <div class="confirm-message">
        <!-- 確認メッセージ -->
        <p>以下の内容で予約してもよろしいですか？</p>

        <!-- =====================================================
      お客様情報
    ===================================================== -->
        <h3>お客様情報</h3>
        <p>お名前: <span id="confirm-名前"></span></p>
        <p>カタカナ: <span id="confirm-カタカナ"></span></p>
        <p>メールアドレス: <span id="confirm-メールアドレス"></span></p>
        <p>郵便番号: <span id="confirm-郵便番号"></span></p>
        <p>住所: <span id="confirm-住所"></span></p>
        <p>電話番号: <span id="confirm-電話番号"></span></p>

        <!-- =====================================================
      来店予約情報
    ===================================================== -->
        <h3>来店予約情報</h3>
        <p>来店日時: <span id="confirm-訪問日"></span></p>
        <p>時間帯: <span id="confirm-時間帯"></span></p>

        <!-- =====================================================
      ご希望の物件情報 / 採用情報
      -------------------------------------------------------
      通常は物件情報を表示
      採用ページなら採用情報を表示
    ===================================================== -->
        <h3 id="confirm-info-title">ご希望の物件情報</h3>

        <!-- 物件ID / 採用ID -->
        <p>
          <span id="confirm-property-label">物件ID: </span>
          <span id="confirm-物件ID"></span>
        </p>

        <!-- 採用ページ用 -->
        <p id="confirm-採用タイトル" style="display: none;">
          採用タイトル: <span id="confirm-採用タイトル-text"></span>
        </p>
        <p id="confirm-採用給与" style="display: none;">
          給与: <span id="confirm-採用給与-text"></span>
        </p>

        <!-- 通常の物件ページ用 -->
        <p id="confirm-物件タイトル" style="display: none;">
          物件タイトル: <span id="confirm-物件タイトル-text"></span>
        </p>
        <p id="confirm-物件価格" style="display: none;">
          価格: <span id="confirm-物件価格-text"></span>
        </p>

        <!-- 操作ボタン -->
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

<script>
  jQuery(document).ready(function($) {
    // カテゴリー変更時にフィルターオプションを更新
    $('#property_category').on('change', function() {
      var cat = $(this).val();

      if (!cat) {
        resetSearchForm();
        return;
      }

      $.ajax({
        url: '/wp-admin/admin-ajax.php',
        type: 'POST',
        dataType: 'json',
        data: {
          action: 'get_filter_options',
          property_category: cat
        },
        success: function(response) {
          if (response.success) {
            var prices = response.data.prices || [];
            var $priceSelect = $('#selected_price');

            $priceSelect.html('<option value="">価格を選択</option>');

            $.each(prices, function(index, price) {
              $priceSelect.append('<option value="' + price + '">' + price + '</option>');
            });

            $('#search-button').prop('disabled', false);
          } else {
            console.error(response.data && response.data.error ? response.data.error : 'フィルター取得に失敗しました。');
          }
        },
        error: function(xhr, status, error) {
          console.error('Ajax Error:', error);
        }
      });
    });

    // 検索ボタン押下時
    function searchProperties() {
      $.ajax({
        url: '/wp-admin/admin-ajax.php',
        type: 'POST',
        dataType: 'json',
        data: {
          action: 'get_reservation_properties',
          property_category: $('#property_category').val(),
          house_type: $('#house-type').val(),
          region: $('#region').val()
        },
        success: function(response) {
          $('#reservation-property-thumbnail')
            .css('background-image', "url('" + (response.thumbnail || '') + "')");

          $('#reservation-property-title')
            .text(response.title || '検索結果がここに表示されます')
            .removeClass('is-placeholder');

          $('#reservation-property-id').text(response.id || '-');
          var priceHtml = response.price || '-';

          if (typeof priceHtml === 'string' && priceHtml.indexOf('売却済み') !== -1) {
            priceHtml = '<span style="color:#d60000;font-weight:700;">' + priceHtml + '</span>';
          }

          $('#reservation-property-price').html(priceHtml);
          $('#reservation-new-mark').html(response.new_mark || '');
        },
        error: function(xhr, status, error) {
          console.log('Ajax error:', error);
        }
      });
    }

    // リセット
    function resetSearchForm() {
      $('#property_category').val('');
      $('#house-type').val('').prop('disabled', true);
      $('#region').val('');
      $('#selected_price').html('<option value="">価格を選択</option>');
      $('#search-button').prop('disabled', true);

      $('#reservation-property-thumbnail').css('background-image', '');
      $('#reservation-property-title')
        .text('検索結果がここに表示されます')
        .addClass('is-placeholder');
      $('#reservation-property-id').text('-');
      $('#reservation-property-price').html('-');
      $('#reservation-new-mark').html('');
    }

    // イベント登録
    $('#search-button').on('click', searchProperties);
    $('#search-reset-button').on('click', resetSearchForm);
  });
</script>

<?php get_footer(); ?>