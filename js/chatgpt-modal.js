jQuery(document).ready(function($) {
    var modalOpen = false; // モーダルが開いているかどうかのフラグ
    var modalClosed = false; // モーダルが閉じられたかどうかのフラグ

    // モーダルを開く関数
    function openModal() {
        if (modalOpen || modalClosed) return; // モーダルが開いているか閉じた後は何もしない
        $('#chatgpt-modal').fadeIn(1000);  // fadeInでモーダルを表示
        modalOpen = true; // モーダルが開いた状態にする

        // 事前メッセージを表示
        var welcomeMessage = `
        <div class="gpt-response">
            こんにちは。内外土地開発（株）です。那須の不動産の物件でしたらご案内ができます。以下のリンクをご参照ください。
            <br>土地の案内ページ: <a href="https://naigaicorp.net/naigai-tochi" target="_blank">リンク</a>
            <br>建物の案内ページ: <a href="https://naigaicorp.net/naigai-construction" target="_blank">リンク</a>
            <br>ほかに何かわからないことがございましたら何でも聞いてくださいね！よろしくお願い申し上げます。
            <br><br>
            
            <strong>カスタマセンター:</strong>
            <br>TEL: <a href="tel:03-3454-5080">03-3454-5080</a>
            <br>MAIL: <a href="mailto:contact@naigaicorp.net">contact@naigaicorp.net</a>
            <br><br>
            
            <strong>本社:</strong> 内外土地開発株式会社
            <br>〒143-0025 東京都大田区南馬込4-26-18
            <br>TEL: <a href="tel:03-6429-8700">03-6429-8700(代)</a>
            <br><br>
            
            <strong>那須事務所:</strong> 内外建設株式会社
            <br>〒325-0021 那須塩原市安藤町40-430 内外ビル
            <br>TEL: <a href="tel:0287-62-1011">0287-62-1011(代)</a>
            <br><br>
            
            <strong>プライバシーポリシー:</strong> <a href="https://naigaicorp.net/privacypolicy" target="_blank">リンク</a>
        </div>
        `;
        
        $('#chatgpt-messages').html(welcomeMessage); // メッセージを表示
    }

    // モーダルを閉じる関数
    function closeModal() {
        $('#chatgpt-modal').fadeOut(1000);  // fadeOutでモーダルを非表示
        modalOpen = false; // モーダルが閉じた状態にする
        modalClosed = true; // モーダルが閉じられたフラグを立てる
    }

    // 「閉じる」ボタンがクリックされたとき
    $('.close-btn').click(function() {
        closeModal();
    });

    // ユーザーのメッセージ送信ボタン
    $('#send_message').click(function() {
        console.log('送信ボタンがクリックされました');
        var userMessage = $('#user_message').val();
        if (userMessage) {
            $.ajax({
                url: customAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'chatgpt_request',
                    security: customAjax.nonce, // nonceを送信
                    user_message: userMessage
                },
                success: function(response) {
                    console.log('APIレスポンス:', response); // レスポンス内容を確認
                    if (response.success) {
                        $('#chatgpt-messages').append('<div class="user-message">' + userMessage + '</div>');
                        $('#chatgpt-messages').append('<div class="gpt-response">' + response.data.message + '</div>');
                    } else {
                        $('#chatgpt-messages').append('<div class="gpt-response">エラー: ' + response.data.message + '</div>');
                    }
                    $('#user_message').val('');  // 入力フィールドをクリア
                },
                error: function(xhr, status, error) {
                    console.error("Ajaxエラー: " + error);
                    console.log("XHR: ", xhr);
                    console.log("Status: ", status);
                    // エラーメッセージをユーザーに表示
                    $('#chatgpt-messages').append('<div class="gpt-response">エラーが発生しました。</div>');
                }
            });
        }
    });

    // エンターキーで送信する処理
    $('#user_message').on('keypress', function(event) {
        if (event.keyCode === 13) {  // Enterキー (keyCode 13) が押されたとき
            event.preventDefault(); // 改行されないように
            $('#send_message').click();  // 送信ボタンをクリックした時と同じ処理を実行
        }
    });

    // ページスクロール時にモーダルを表示
    $(window).scroll(function() {
        if ($(window).scrollTop() > 100 && !modalOpen && !modalClosed) {  // モーダルが開いていないか、閉じられた場合は表示しない
            openModal();
        }
    });

    // モーダルの外側をクリックしたときに閉じる
    $('#chatgpt-modal').click(function(event) {
        // モーダルの内容をクリックした場合は閉じない
        if ($(event.target).is('#chatgpt-modal')) {
            closeModal();
        }
    });
});
