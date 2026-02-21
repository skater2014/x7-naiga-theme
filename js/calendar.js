const CLIENT_ID = '47097046654-fn613q7n02ceqpiomfuu8i2scu4gij6b.apps.googleusercontent.com';  // Google Cloud Consoleで取得したClient ID
const SCOPES = 'https://www.googleapis.com/auth/calendar.events';  // 必要なAPIスコープ
const REDIRECT_URI = 'https://naigaicorp.net';  // Google Cloud Consoleで設定したリダイレクトURI

let authToken = null;  // 認証トークンを格納する変数

// Google Identity Services (GSI) の初期化
function initializeGSI() {
    google.accounts.id.initialize({
        client_id: CLIENT_ID,
        callback: handleCredentialResponse,  // 認証成功後のコールバック関数
        scope: SCOPES  // 必要なスコープを指定
    });

    // 認証ボタンをレンダリング
    const authButtonContainer = document.getElementById('authButtonContainer');
    if (authButtonContainer) {
        google.accounts.id.renderButton(authButtonContainer, {
            theme: 'outline',
            size: 'large',
        });
    }
}

// 認証成功後の処理
function handleCredentialResponse(response) {
    console.log('Credential Response:', response);  // 取得した認証情報をログに出力

    if (response.credential) {
        authToken = response.credential;  // トークンをグローバル変数に保存

        // トークンが正しく取得された場合のみ、カレンダーAPIを呼び出す
        addEventToGoogleCalendar(authToken);
    } else {
        console.error('認証トークンの取得に失敗しました');
        alert('認証に失敗しました。再度お試しください。');
    }

    // 予約フォームを表示
    const reservationForm = document.getElementById('reservationForm');
    if (reservationForm) {
        reservationForm.style.display = 'block'; // フォームを表示
    }
}

// Googleカレンダーにイベントを追加
async function addEventToGoogleCalendar(token) {
    const event = {
        summary: 'Sample Event',  // イベントタイトル
        description: 'Sample Event Description',  // イベント詳細
        start: {
            dateTime: '2024-12-01T09:00:00',  // 開始日時（ISO形式）
            timeZone: 'Asia/Tokyo',  // タイムゾーンを指定
        },
        end: {
            dateTime: '2024-12-01T10:00:00',  // 終了日時（ISO形式）
            timeZone: 'Asia/Tokyo',  // タイムゾーンを指定
        },
        attendees: [{ email: 'sample@example.com' }],  // 参加者のメールアドレス
    };

    try {
        console.log("Sending event:", event);  // イベントデータを確認
        const response = await fetch('https://www.googleapis.com/calendar/v3/calendars/primary/events', {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`,  // トークンをヘッダーにセット
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(event),  // イベントデータをJSON形式で送信
        });

        const responseData = await response.json();
        if (response.ok) {
            alert('予約が完了しました！');
            console.log('イベントが追加されました:', responseData);
        } else {
            console.error('APIレスポンスエラー:', responseData.error);  // APIのエラー内容を出力
            alert(`エラーが発生しました: ${responseData.error.message}`);
        }
    } catch (error) {
        console.error('イベント追加エラー:', error);
        alert('予約に失敗しました。もう一度お試しください。');
    }
}

// ページロード時の処理
document.addEventListener('DOMContentLoaded', function() {
    initializeGSI();  // Google Identity Servicesの初期化処理実行
});
