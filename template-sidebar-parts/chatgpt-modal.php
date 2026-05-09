<!-- CHATGPTモーダルウィンドウ -->
<div id="chatgpt-modal" class="chatgpt-modal">
    <div class="chatgpt-modal-content">
        <span class="close-btn">&times;</span> <!-- 閉じるボタン -->
        <div id="chatgpt-container">
            <div id="chatgpt-messages"></div>
            <input type="text" id="user_message" placeholder="メッセージを入力してください">
            <button id="send_message">送信</button>
            <div id="response"></div>
        </div>
    </div>
</div>

<style>
    /* CHATGPTモーダルのスタイル */
    .chatgpt-modal {
        display: none; /* 最初は非表示 */
        position: fixed;
        z-index: 9999;  /* 他の要素よりも前面に表示 */
        right: 0;  /* 右端に配置 */
        bottom: 0;  /* 下端に配置 */
        width: 80%;
        height: auto;
        max-width: 500px; /* 最大幅を設定 */
        background-color: rgba(0, 0, 0, 0.5);
    }
    .chatgpt-modal-content {
        background-color: white;
        padding: 20px;
        border: 1px solid #888;
        position: relative; /* 閉じるボタンを配置するため */
        width: 100%;
    }
    .close-btn {
        color: #aaa;
        position: absolute;
        top: 10px;
        right: 10px;
        width: 40px; /* ボタンの直径 */
        height: 40px; /* ボタンの直径 */
        background-color: #ff4c4c; /* ボタンの色 */
        color: white;
        border-radius: 50%; /* 丸くする */
        font-size: 30px; /* 「×」のサイズ */
        display: flex;
        justify-content: center;
        align-items: center;
        cursor: pointer;
        border: none; /* ボーダーをなくす */
        box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.2); /* 影を追加 */
        transition: background-color 0.3s ease, transform 0.2s ease;
    }
    /* ホバー時とフォーカス時 */
    .close-btn:hover, .close-btn:focus {
        background-color: #ff1f1f; /* ホバー時の色 */
        transform: scale(1.1); /* ホバー時に少し拡大 */
    }

    #chatgpt-container {
        display: flex;
        flex-direction: column;
    }
    #chatgpt-messages {
        max-height: 300px;
        overflow-y: auto;
        margin-bottom: 10px;
    }
    #user_message {
        padding: 10px;
        margin-bottom: 10px;
        width: 100%;
    }
    #send_message {
        padding: 10px;
        cursor: pointer;
    }

    /* チャットのメッセージテキストの色を黒に設定 */
    #chatgpt-messages {
        max-height: 300px;
        overflow-y: auto;
        margin-bottom: 10px;
        color: black; /* メッセージの文字色を黒に変更 */
    }

    /* ユーザーのメッセージ */
    .user-message {
        padding: 8px;
        margin-bottom: 10px;
        border-radius: 5px;
        background-color: #d1e6ff; /* ユーザーのメッセージ背景色（青） */
        color: black; /* テキスト色（黒） */
        align-self: flex-start; /* 左寄せ */
    }

    /* GPTのメッセージ */
    .gpt-response {
        padding: 8px;
        margin-bottom: 10px;
        border-radius: 5px;
        background-color: #e1f7d5; /* GPTメッセージの背景色（緑） */
        color: black; /* テキスト色（黒） */
        align-self: flex-end; /* 右寄せ */
    }

    /* ユーザーとGPTのメッセージに共通のスタイル */
    .user-message, .gpt-response {
        max-width: 100%;  /* 各メッセージの最大幅 */
        word-wrap: break-word;  /* 長い単語を折り返す */
    }

</style>


