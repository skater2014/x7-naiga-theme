<form role="search" method="get" class="search-form-30" action="<?php echo esc_url( home_url( '/' ) ); ?>">
    <label>
        <input type="text" placeholder="Search" name="s">
    </label>
    <button type="submit" aria-label="Search"></button>
</form>

<style>
.search-form-30 {
    display: flex;
    justify-content: space-between;
    align-items: center;
    overflow: hidden;
    border: 2px solid #2589d0;
    border-radius: 3px;
    max-width:189px; /* 全体的な幅を制限 */
    height: 35px;
}

.search-form-30 input {
    width: 150px;
    height: 35px; /* 高さを調整してボタンと揃えます */
    padding: 5px 15px;
    border: none;
    box-sizing: border-box;
    font-size: 0.9em; /* フォントサイズを調整 */
    outline: none;
    background: #fff;
}

.search-form-30 button {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 35px; /* アイコンが隠れないようにボタンの幅を調整 */
    height: 35px; /* アイコンの高さに合わせてボタンを調整 */
    border: none;
    background-color: #2589d0;
    cursor: pointer;
    padding: 0; /* ボタン内の余白をリセット */
}

.search-form-30 button::after {
    content: '';
    width: 18px; /* アイコンのサイズを適切に設定 */
    height: 18px; /* アイコンのサイズを適切に設定 */
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath d='M18.031 16.6168L22.3137 20.8995L20.8995 22.3137L16.6168 18.031C15.0769 19.263 13.124 20 11 20C6.032 20 2 15.968 2 11C2 6.032 6.032 2 11 2C15.968 2 20 6.032 20 11C20 13.124 19.263 15.0769 18.031 16.6168ZM16.0247 15.8748C17.2475 14.6146 18 12.8956 18 11C18 7.1325 14.8675 4 11 4C7.1325 4 4 7.1325 4 11C4 14.8675 7.1325 18 11 18C12.8956 18 14.6146 17.2475 15.8748 16.0247L16.0247 15.8748Z' fill='%23fff'%3E%3C/path%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-size: contain;
}
</style>
