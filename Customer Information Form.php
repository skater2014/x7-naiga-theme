<?php
/*
Template Name: Customer Information Form
*/
?>

<?php get_header('77'); ?>

<div class="customer-info-form">
    <h2>お客様情報入力</h2>
    <form action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" method="post">
        <?php wp_nonce_field('customer_info_form', 'customer_info_nonce'); ?>
        
        <label for="name">名前:<span style="color: red;">*</span></label>
        <input type="text" id="name" name="name" required>

        <label for="email">メールアドレス:<span style="color: red;">*</span></label>
        <input type="email" id="email" name="email" required>

        <label for="phone">電話番号:</label>
        <input type="tel" id="phone" name="phone">

        <label for="prefecture">都道府県:<span style="color: red;">*</span></label>
        <select name="prefecture" id="prefecture" required>
            <option value="" label="都道府県を選択してください"></option>
            <option value="北海道">北海道</option>
            <option value="青森県">青森県</option>
            <option value="岩手県">岩手県</option>
            <option value="宮城県">宮城県</option>
            <option value="秋田県">秋田県</option>
            <option value="山形県">山形県</option>
            <option value="福島県">福島県</option>
            <option value="茨城県">茨城県</option>
            <option value="栃木県">栃木県</option>
            <option value="群馬県">群馬県</option>
            <option value="埼玉県">埼玉県</option>
            <option value="千葉県">千葉県</option>
            <option value="東京都">東京都</option>
            <option value="神奈川県">神奈川県</option>
            <option value="新潟県">新潟県</option>
            <option value="富山県">富山県</option>
            <option value="石川県">石川県</option>
            <option value="福井県">福井県</option>
            <option value="山梨県">山梨県</option>
            <option value="長野県">長野県</option>
            <option value="岐阜県">岐阜県</option>
            <option value="静岡県">静岡県</option>
            <option value="愛知県">愛知県</option>
            <option value="三重県">三重県</option>
            <option value="滋賀県">滋賀県</option>
            <option value="京都府">京都府</option>
            <option value="大阪府">大阪府</option>
            <option value="兵庫県">兵庫県</option>
            <option value="奈良県">奈良県</option>
            <option value="和歌山県">和歌山県</option>
            <option value="鳥取県">鳥取県</option>
            <option value="島根県">島根県</option>
            <option value="岡山県">岡山県</option>
            <option value="広島県">広島県</option>
            <option value="山口県">山口県</option>
            <option value="徳島県">徳島県</option>
            <option value="香川県">香川県</option>
            <option value="愛媛県">愛媛県</option>
            <option value="高知県">高知県</option>
            <option value="福岡県">福岡県</option>
            <option value="佐賀県">佐賀県</option>
            <option value="長崎県">長崎県</option>
            <option value="熊本県">熊本県</option>
            <option value="大分県">大分県</option>
            <option value="宮崎県">宮崎県</option>
            <option value="鹿児島県">鹿児島県</option>
            <option value="沖縄県">沖縄県</option>
        </select>

        <label for="city">市区町村:<span style="color: red;">*</span></label>
        <input type="text" id="city" name="city" required>

        <label for="address_number">番地:<span style="color: red;">*</span></label>
        <input type="text" id="address_number" name="address_number" required>

        <label for="residence_years">居住年数:<span style="color: red;">*</span></label>
        <input type="number" id="residence_years" name="residence_years" min="0" required>

        <label for="job">職業:</label>
        <select name="job" id="job">
            <option value="" label=""></option>
            <option value="1">社長 / 代表者</option>
            <option value="2">会社員</option>
            <!-- Other options... -->
            <option value="99">その他</option>
        </select>

        <label for="age">年齢:</label>
        <input type="number" id="age" name="age" min="0">

        <label>性別:<span style="color: red;">*</span></label>
        <div class="gender-selection">
            <label><input type="radio" name="gender" value="male" required> 男性</label>
            <label><input type="radio" name="gender" value="female" required> 女性</label>
        </div>

        <label for="other_address">その他の住所情報:</label>
        <textarea id="other_address" name="other_address"></textarea>

		<p class="attention">
		    <span>※<a href="<?php echo esc_url( home_url( '/rule/' ) ); ?>" target="_blank">利用規約</a>及び<a href="<?php echo esc_url( home_url( '/privacypolicy/' ) ); ?>" target="_blank">プライバシーポリシー</a>を必ずお読みください。<br>
		    上記内容に同意いただいた場合は、確認画面へお進みください。</span>
		</p>

        <div class="background">
            <button type="submit" class="btn submit button2">上記に同意して確認画面へ</button>
        </div>

        <p class="attention">
            ※メール連絡をご希望でも、メールアドレスに間違いがありますと、やむなくお電話にてご連絡差し上げる場合もございます。
        </p>  
    </form>
</div>

<?php get_footer(); ?>
