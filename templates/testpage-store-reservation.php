<?php
/*
Template Name: 店舗予約ページ
*/
get_header('77');  // ヘッダーを読み込む
?>

<!-- ページコンテンツ -->
<div class="page-content">
    <!-- 固定ページのコンテンツ -->
    <?php
    while ( have_posts() ) :
        the_post();
        the_content();  // 固定ページの内容を表示
    endwhile;
    ?>
</div>

<!-- 予約リンクボタン モーダル -->
<div id="store-reservation-modal" class="store-reservation-modal">
   <div class="store-reservation-modal-content">
       <span class="close-btn">×</span>

       <!-- モーダルのステップ内容（コードはそのままで良い） -->
       <div class="step active" id="step-input">
           <!-- フォーム内容をそのまま使用 -->
           <h3>来店予約</h3>
           <form id="store-reservation-form" action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" method="POST">
               <?php wp_nonce_field('store_reservation_action', 'store_reservation_nonce'); ?>

               <!-- 物件情報などの詳細をここに挿入 -->
               <div class="property-info">
                   <div id="reservation-property-thumbnail" class="reservation-property-thumbnail" style="background-image: url('<?php echo esc_url($thumbnail_url); ?>'); background-size: cover; background-position: center center;"></div>

                   <!-- 他の情報 -->
                   <div class="property-details-modal">
                       <h3>物件タイトル: <span id="reservation-property-title"><?php echo esc_html(get_the_title()); ?></span></h3>
                       <div class="property-info-row">
                           <h3>物件ID: <span id="reservation-property-id"><?php echo esc_html(get_the_ID()); ?></span></h3>
                           <h3>価格: <span id="reservation-property-price"></span></h3>
                       </div>
                   </div>
               </div>

               <!-- フォームの入力フィールド -->
               <div class="form-row-group">
                   <div class="form-row">
                       <label for="name">お名前: <span class="required">＊必須</span></label>
                       <input type="text" id="name" name="name" class="input-field" placeholder="例: 山田 太郎" required>
                   </div>
                   <!-- 他の入力フィールド -->
               </div>

               <input type="hidden" name="action" value="store_reservation">
               <button type="button" id="to-confirm" class="form-button">確認する</button>
           </form>
       </div>

       <!-- 他のステップ（確認画面、完了画面など） -->
   </div>
</div>

<?php
get_footer();  // フッターを読み込む
?>
