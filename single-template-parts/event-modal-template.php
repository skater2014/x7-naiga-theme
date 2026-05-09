<!-- Modal (Bootstrap 5) -->
<div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" id="eventModalLabel">イベントの詳細</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <!-- 画像 + 抜粋（横並び） -->
        <div class="event-media">
          <img id="eventEyecatch" src="" alt="アイキャッチ画像" loading="lazy">

          <div class="event-side">
            <!-- ✅ pやめる：WP excerpt が <p> を含むから崩れる -->
            <div id="eventExcerpt" class="event-excerpt"></div>
          </div>
        </div>

        <!-- イベント詳細（本文HTMLが入る想定なら div） -->
        <div id="eventDetails" class="event-details">
          イベントの詳細情報がここに表示されます。
        </div>

        <!-- Google認証ボタン -->
        <div id="authButtonContainer" class="mt-3"></div>

        <!-- 予約フォーム（認証後に表示） -->
        <form id="reservationForm" class="mt-3" style="display:none;">
          <div class="mb-3">
            <label for="nameField" class="form-label">お名前</label>
            <input type="text" class="form-control" id="nameField" placeholder="お名前を入力してください" required>
          </div>

          <div class="mb-3">
            <label for="emailField" class="form-label">メールアドレス</label>
            <input type="email" class="form-control" id="emailField" placeholder="メールアドレスを入力してください" required>
          </div>

          <div class="mb-3">
            <label for="reservationDate" class="form-label">訪問予約</label>
            <input type="date" class="form-control" id="reservationDate" name="reservationDate">
          </div>

          <div class="mb-3">
            <label for="timeField" class="form-label">時間帯</label>
            <select class="form-select" id="timeField" required>
              <option value="10:00">10:00</option>
              <option value="13:00">13:00</option>
              <option value="16:00">16:00</option>
            </select>
          </div>

          <button type="submit" class="btn btn-primary">予約を送信</button>
        </form>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">閉じる</button>
      </div>

    </div>
  </div>
</div>