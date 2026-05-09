# /fudousan ページ

このフォルダーは固定ページ `/fudousan` の表示用。

## 入口
- 旧入口: `hub/templates/realestate.php`
- 実体: `hub/pages/fudousan/template.php`

`hub/templates/realestate.php` は互換用に残し、実体はこのフォルダーに集約する。

## 重要
来店予約モーダルは `js/scripts.js` の既存コードを使う。
そのためカードHTMLは以下の旧仕様に合わせる。

- `.main-custom-post`
- `.blog-post-image`
- `h2 a`
- `.property-info-table .property-row .property-label`
- `.property-info-table .property-row .property-value`

`scripts.js` は変更しない。
